<?php

namespace App\Http\Controllers;

use App\ComModules\CM;
use App\ComModules\Files;
use App\ComModules\Orchestrator;
use Carbon\Carbon;
use App\ComModules\CB;
use App\ComModules\Auth;
use Exception;
use Illuminate\Http\Request;
use App\One\One;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Collection;
use Session;
use Redirect;

class PublicController extends Controller
{
    private $contentKey = 'ShAVLaa8tOrePz5osNtpjuLpjMhDHiHP';
    private $siteContentKey;
    public function __construct()
    {

    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $languages = [];
        $countries = [];
        $timezones = [];
        $currencies = [];

        if(!Session::has('all_languages')){
            $languages = Orchestrator::getAllLanguages();
            Session::put('all_languages',json_encode($languages));
        }

        if(!Session::has('countries')){
            $countries = Orchestrator::getCountryList();
            Session::put('countries',json_encode($countries));
        }
        
        if(!Session::has('timezones')){
            $timezones = Orchestrator::getTimeZoneList();
            Session::put('timezones',json_encode($timezones));
        }

        if(!Session::has('currencies')){
            $currencies = Orchestrator::getCurrencyList();
            Session::put('currencies',json_encode($currencies));
        }

        $role = 'manager';
        $url = "https://".$_SERVER["HTTP_HOST"]."/";

        $data['languages'] = json_decode(Session::get('all_languages'));
        $data['countries'] = json_decode(Session::get('countries'));
        $data['timezones'] = json_decode(Session::get('timezones'));
        $data['currencies'] = json_decode(Session::get('currencies'));
        $data['role'] = $role;
        $data['url'] = $url;

        return view('public.'.ONE::getEntityLayout().'.home.index', $data);
    }

    /**
     * Display a listing of the resource.
     *
     * @param $name , name of the view to load
     * @return Response
     */
    public function showCustomView($name)
    {

        try {
            $response = Orchestrator::getSiteUseTerm();

            if(isset($response)){
                $useTerms = html_entity_decode($response->content);
            }else{
                $useTerms = $response;
            }

            $homeContent =  CM::getContentByKey($this->contentKey);
            // Banners
            $data['banners'] = $this->getFirstFiles($this->contentKey, 2);

            $siteContent = CM::getContentByKey($this->siteContentKey);

            // Get news list
            $dataNews = Orchestrator::getPageListByType("news",5);
            $lastNews = [];
            if(!empty($dataNews)) {
                $lastNews = CM::getVariousContents($dataNews);
            }

            $newsImage = [];
            foreach ($lastNews as $new){
                foreach ($new->content_files as $file){
                    if($file->type_id == 4){
                        $file = Files::getFile($file->file_id);
                        $newsImage[$new->content_key] = ['id' => $file->id,'code' => $file->code];
                        break;
                    }
                }
            }

            // Get events list
            $dataEvents = Orchestrator::getPageListByType("events",5);
            $lastEvents =[];
            if(!empty($dataEvents)) {
                $lastEvents = CM::getVariousContents($dataEvents);

            }

            //TODO:only for empatia
            $stream = Orchestrator::getEmpatiaStream();
            $homePageConfigurations = ONE::getHomePageConfigurations();

            $goalIcon = ['inclusion', 'integration', 'deliberativeQuality', 'replicationAndAdaptation', 'efficiency', 'ehnancedEvaluation', 'accountability', 'marketability'];

            if($stream) {
                $data['stream'] = $stream;
            }
            $data['goalIcon'] = $goalIcon;
            $data['homePageConfigurations'] = $homePageConfigurations;
            $data['lastNews'] = $lastNews;
            $data['newsImage'] = $newsImage;
            $data['lastEvents'] = $lastEvents;
            $data['homeContent'] = $homeContent;
            $data['siteContent'] = $siteContent;
            $data['useTerms'] = $useTerms;
            $data['bannerName'] = $name.'Banner';
            return view('public.'.ONE::getEntityLayout().'.home.'.$name, $data);
        }
        catch(Exception $e) {
            return view('public.'.ONE::getEntityLayout().'.home.index');
        }
    }

    private function buildMainMenu($menus, $menusArray, $level = 0, $idParent = 0)
    {

//        return $menusArray;
        if ($level >= 3) {
            return $menusArray;
        }

        foreach ($menus as $menu) {

//            $menu = (array)$menu;
//            dd($menu['title']);
            $subMenu = [];
            if ($menu->parent_id == $idParent && $menu->title != "") {
                $subMenu[0] = $menu;

                $subMenu = $this->buildMainMenu($menus, $subMenu, $level + 1, $menu->id);

                if (count($subMenu) == 1) {
                    $menusArray[$menu->id] = $menu;
                } else {
                    $menusArray[$menu->id] = $subMenu;
                }
            }
        }

        return $menusArray;
    }

    /**
     * Get First 5 Files of specific type content.
     *
     * @param  int $content_id, int $typeId
     * @return Response
     */
    public function getFirstFiles($content_id, $typeId = null)
    {

        try{

            $temp_files = CM::getContentFiles($content_id, $typeId);

            $files_order = array();
            foreach($temp_files as $file){
                $files_order[] = $file->file_id;
            }
            $files = Files::listFiles($files_order);

            $files_key = array();
            $i = 0;
            foreach($files as $file){
                $files_key[$file->id] = $file;
                $files_key[$file->id]->key = $file->id;
                $i++;
            }

            $result = array();
            $i = 0;
            foreach($files_order as $key) {
                if(array_key_exists($key, $files_key)) {
                    $result[$i] = $files_key[$key];
                    $i++;
                }
            }
            return $result;

        }
        catch(Exception $e) {
            return null;
        }
    }



    public function getSubPage(Request $request){
        try{
            $subPage = $request->sub_page;
            if(empty($subPage)){
                throw new Exception('sub_page_not_found');
            }
            return view('public.'.ONE::getEntityLayout().'.cbs.'.$subPage);

        }catch(Exception $e){
            return redirect()->back()->withErrors([trans('publicHome.sub_page_error') => $e->getMessage()]);
        }
    }

    public function wizard(Request $request){

        if(env("DEMO_MODE",false)==true) {
            $languages = Orchestrator::getAllLanguages();
            $countries = Orchestrator::getCountryList();
            $timezones = Orchestrator::getTimeZoneList();
            $currencies = Orchestrator::getCurrencyList();
    
            $data['languages'] = $languages;
            $data['countries'] = $countries;
            $data['timezones'] = $timezones;
            $data['currencies'] = $currencies;
    
            return view('public.'.ONE::getEntityLayout().'.wizard.createEntity', $data);
        } else
        return redirect()->back();
    }

    public function storeWizard(Request $request){
        try {
            if(env("DEMO_MODE",false)==true) {

                $url = "https://".$_SERVER["HTTP_HOST"]."/".$request->input('designation');
                $noreply = $request->input('noreplyemail');
                $data['language_id'] = $request->input('languages');
                $data['country_id'] = $request->input('country');
                $data['currency_id'] = $request->input('currency');
                $data['timezone_id'] = $request->input('timezone');
                $data['name'] = $request->input('nameEntity');
                $data['url'] = $url ?? '';
                $data['no_reply_email'] = $noreply;
                $data['link'] = $url;
                $data['role'] = 'manager';
                
                $manager['name'] = $request->input('name');
                $manager['surname'] = $request->input('surname');
                $manager['email'] = $request->input('email');
                $manager['password'] = $request->input('password');

                $entity = Orchestrator::setEntity($data);               

                $entityController = new EntitiesController();
                $entityController->setEntityKey($request);
                $entityKey = $entity->entity_key;
                Session::put('X-ENTITY-KEY', $entityKey);

                Orchestrator::setEntityLanguage($request->get("languages"), $entity->entity_key);

                Orchestrator::setLayoutEntity($entity->entity_key, null, $request->input('layout'));


                // Store a new Manager

                $role = 'manager';

                $userDetails = $request->all();
                $email = $manager['email'];
                $password = $manager['password'];

                $userData = Auth::storeUserV2($manager);
                $user = $userData->user;


                //* Send to Orchestrator the user type and key*/
                if(isset($entityKey)) {
                    $response = Orchestrator::setEntityUser( $user->user_key, $entityKey, $role);
                } else {
                    $response = Orchestrator::setUser($user->user_key, $role);
                }
                
                $login = Auth::login($email,$password);
                // Change status to authorized
                if($role != 'admin')
                    Orchestrator::updateUserStatus('authorized' ,$user->user_key);

                
                $userKey = $user->user_key;

                $data['layout_reference'] = $request->input('layout');
                $data['entity_key'] = $entity->entity_key;
                $data['active'] = 1;
                Orchestrator::setNewEntitySite($entity->entity_key, $data);

                if(!empty($site)){

                    $sectionTypes = collect(CM::getSectionTypes());
                    $contentSection = $sectionTypes->where('code','contentSection')->first();
                    $buttonSection = $sectionTypes->where('code','buttonSection')->first();
                    $slideShowSection = $sectionTypes->where('code','slideShowSection')->first();
                    $multipleImagesSection = $sectionTypes->where('code','multipleImagesSection')->first();
                    $homepageItemSection = $sectionTypes->where('code','homepageItemSection')->first();
                    $singleImageSection = $sectionTypes->where('code','singleImageSection')->first();
    
                    //CREATE HOMEPAGE   
                    $dataToSend = array(
                        "content_type_code"     => "pages",
                        "name"                  => "Homepage",
                        "code"                  => "homepage",
                        "active"                => 0,
                        "start_date"            => null,
                        "publish_date"          => null,
                        "end_date"              => null,
                        "highlight"             => 0,
                        "sections"              => array(),
                        "site_keys"             => $site->key,
                    );
    
                    $topicKey = $request->input('topicKey',null); 
    
                    $dataToSend["sections"][0] = array(
                        "section_type_key" => $contentSection->section_type_key,
                        "code" => "",
                        "section_parameters" => array()
                    );
                    $sectionType = CM::getSectionType($dataToSend["sections"][0]["section_type_key"]);
                    foreach ($sectionType->section_type_parameters as $parameter) {
                        $newParameter = array(
                            "section_type_parameter_key"    => $parameter->section_type_parameter_key,
                            "code"                          => "",
                        );
                        foreach ($languages as $language) {
                            $newParameter["translations"][] = array(
                                "language_code" => $language->code,
                                "value"         => null
                            );
                        }
    
                        $dataToSend["sections"][0]["section_parameters"][] = $newParameter;
                    }
    
                    $dataToSend["sections"][1] = array(
                        "section_type_key" => $buttonSection->section_type_key,
                        "code" => "",
                        "section_parameters" => array()
                    );
                    $sectionType = CM::getSectionType($dataToSend["sections"][1]["section_type_key"]);
                    foreach ($sectionType->section_type_parameters as $parameter) {
                        $newParameter = array(
                            "section_type_parameter_key"    => $parameter->section_type_parameter_key,
                            "code"                          => "",
                        );
                        foreach ($languages as $language) {
                            $newParameter["translations"][] = array(
                                "language_code" => $language->code,
                                "value"         => null
                            );
                        }
    
                        $dataToSend["sections"][1]["section_parameters"][] = $newParameter;
                    }
                    
                    $dataToSend["sections"][2] = array(
                        "section_type_key" => $slideShowSection->section_type_key,
                        "code" => "",
                        "section_parameters" => array()
                    );
                    $sectionType = CM::getSectionType($dataToSend["sections"][2]["section_type_key"]);
                    foreach ($sectionType->section_type_parameters as $parameter) {
                        $newParameter = array(
                            "section_type_parameter_key"    => $parameter->section_type_parameter_key,
                            "code"                          => "",
                        );
                        foreach ($languages as $language) {
                            $newParameter["translations"][] = array(
                                "language_code" => $language->code,
                                "value"         => null
                            );
                        }
    
                        $dataToSend["sections"][2]["section_parameters"][] = $newParameter;
                    }
    
                    $dataToSend["sections"][3] = $dataToSend["sections"][0];
    
                    $dataToSend["sections"][4] = $dataToSend["sections"][0];
    
                    $dataToSend["sections"][5] = $dataToSend["sections"][0];
    
                    $dataToSend["sections"][6] = array(
                        "section_type_key" => $multipleImagesSection->section_type_key,
                        "code" => "",
                        "section_parameters" => array()
                    );
                    $sectionType = CM::getSectionType($dataToSend["sections"][6]["section_type_key"]);
                    foreach ($sectionType->section_type_parameters as $parameter) {
                        $newParameter = array(
                            "section_type_parameter_key"    => $parameter->section_type_parameter_key,
                            "code"                          => "",
                        );
                        foreach ($languages as $language) {
                            $newParameter["translations"][] = array(
                                "language_code" => $language->code,
                                "value"         => null
                            );
                        }
    
                        $dataToSend["sections"][6]["section_parameters"][] = $newParameter;
                    }
    
                    $dataToSend["sections"][7] = array(
                        "section_type_key" => $homepageItemSection->section_type_key,
                        "code" => "",
                        "section_parameters" => array()
                    );
                    $sectionType = CM::getSectionType($dataToSend["sections"][7]["section_type_key"]);
                    foreach ($sectionType->section_type_parameters as $parameter) {
                        $newParameter = array(
                            "section_type_parameter_key"    => $parameter->section_type_parameter_key,
                            "code"                          => "",
                        );
                        foreach ($languages as $language) {
                            $newParameter["translations"][] = array(
                                "language_code" => $language->code,
                                "value"         => null
                            );
                        }
    
                        $dataToSend["sections"][7]["section_parameters"][] = $newParameter;
                    }
    
                    $dataToSend["sections"][8] = $dataToSend["sections"][7];
    
                    $dataToSend["sections"][9] = $dataToSend["sections"][7];
    
                    $response = CM::createNewContent($dataToSend);
    
                    // CREATE MODAL
                    $dataToSend1 = array(
                        "content_type_code"     => "pages",
                        "name"                  => "Homepage Modal",
                        "code"                  => "home_modal",
                        "active"                => 0,
                        "start_date"            => null,
                        "publish_date"          => null,
                        "end_date"              => null,
                        "highlight"             => 0,
                        "sections"              => array(),
                        "site_keys"             => $site->key,
                    );
    
                    $dataToSend1["sections"][0] = array(
                        "section_type_key" => $singleImageSection->section_type_key,
                        "code" => "",
                        "section_parameters" => array()
                    );
                    $sectionType = CM::getSectionType($dataToSend1["sections"][0]["section_type_key"]);
                    foreach ($sectionType->section_type_parameters as $parameter) {
                        $newParameter = array(
                            "section_type_parameter_key"    => $parameter->section_type_parameter_key,
                            "code"                          => "",
                        );
                        foreach ($languages as $language) {
                            $newParameter["translations"][] = array(
                                "language_code" => $language->code,
                                "value"         => null
                            );
                        }
    
                        $dataToSend1["sections"][0]["section_parameters"][] = $newParameter;
                    }
    
                    $dataToSend1["sections"][1] = array(
                        "section_type_key" => $contentSection->section_type_key,
                        "code" => "",
                        "section_parameters" => array()
                    );
                    $sectionType = CM::getSectionType($dataToSend1["sections"][1]["section_type_key"]);
                    foreach ($sectionType->section_type_parameters as $parameter) {
                        $newParameter = array(
                            "section_type_parameter_key"    => $parameter->section_type_parameter_key,
                            "code"                          => "",
                        );
                        foreach ($languages as $language) {
                            $newParameter["translations"][] = array(
                                "language_code" => $language->code,
                                "value"         => null
                            );
                        }
    
                        $dataToSend1["sections"][1]["section_parameters"][] = $newParameter;
                    }
    
                    $dataToSend1["sections"][2] = array(
                        "section_type_key" => $buttonSection->section_type_key,
                        "code" => "",
                        "section_parameters" => array()
                    );
                    $sectionType = CM::getSectionType($dataToSend1["sections"][2]["section_type_key"]);
                    foreach ($sectionType->section_type_parameters as $parameter) {
                        $newParameter = array(
                            "section_type_parameter_key"    => $parameter->section_type_parameter_key,
                            "code"                          => "",
                        );
                        foreach ($languages as $language) {
                            $newParameter["translations"][] = array(
                                "language_code" => $language->code,
                                "value"         => null
                            );
                        }
    
                        $dataToSend1["sections"][2]["section_parameters"][] = $newParameter;
                    }
    
    
                    $response1 = CM::createNewContent($dataToSend1);
                    
                    //CREATE MODAL
                    $dataToSend2 = array(
                        "content_type_code"     => "pages",
                        "name"                  => "Splashed",
                        "code"                  => "splashed",
                        "active"                => 0,
                        "start_date"            => null,
                        "publish_date"          => null,
                        "end_date"              => null,
                        "highlight"             => 0,
                        "sections"              => array(),
                        "site_keys"             => array($site->key),
                    );
    
                    $dataToSend2["sections"][0] = array(
                        "section_type_key" => $contentSection->section_type_key,
                        "code" => "",
                        "section_parameters" => array()
                    );
                    $sectionType = CM::getSectionType($dataToSend2["sections"][0]["section_type_key"]);
                    foreach ($sectionType->section_type_parameters as $parameter) {
                        $newParameter = array(
                            "section_type_parameter_key"    => $parameter->section_type_parameter_key,
                            "code"                          => "",
                        );
                        foreach ($languages as $language) {
                            $newParameter["translations"][] = array(
                                "language_code" => $language->code,
                                "value"         => null
                            );
                        }
    
                        $dataToSend2["sections"][0]["section_parameters"][] = $newParameter;
                    }
    
                    $response2 = CM::createNewContent($dataToSend2);
    
                    if(!is_null($topicKey) && !empty($topicKey))
                        CB::addTopicNews($topicKey,$response->content_key);
                }

                //

            $permissionsToGive = array(
                "auth" => array(
                    "user_parameters",
                    "user",
                    "manager"
                ),
                "cb" => array(
                    "idea",
                    "project",
                    "proposal",
                    "topics",
                    "pad_parameters",
                    "pad_votes",
                    "configurations",
                    "topic_status",
                    "comments"
                ),
                "cm" => array(
                    "pages",
                    "news",
                    "menu"
                ),
                "orchestrator" => array(
                    "site_privacy_policy",
                    "site_use_terms",
                    "entity_language",
                    "entity_layout",
                    "entity_site",
                    "entity",
                    "role_permissions",
                    "site_configurations"
                ),
                "wui" => array(
                    "email",
                    "sites"
                )
            );
            $modules = collect(Orchestrator::getModulesList()->data ?? []);

            foreach ($permissionsToGive as $module=>$permissions) {
                $cbModule = $modules->where("code", "=", $module)->first() ?? [];

                if (!empty($cbModule)) {
                    foreach ($permissions as $permission) {
                        // dd(collect($cbModule->module_types)->where("code", "=", $permission)->first(), $permission);
                        $cbModuleType = collect($cbModule->module_types)->where("code", "=", $permission)->first() ?? [];

                        if (!empty($cbModuleType))
                            Orchestrator::setModuleTypeForEntity($cbModule->module_key, $cbModuleType->module_type_key,$entityKey);
                    }
                }
            }
            
            \Cache::forget('entityModulesActive_' . ONE::getEntityKey());

            $entityModulesList = Orchestrator::getActiveEntityModules($entityKey);
            $data = [];
            
            foreach ($entityModulesList as $moduleKey => $entityModules){
                foreach ($entityModules->types as $moduleTypeKey => $moduleType){
                    $temp['module_key'] = $moduleKey;
                    $temp['module_type_key'] = $moduleTypeKey;
                    $temp['permission_show'] = isset($request->modules_types[$moduleKey][$moduleTypeKey]['show']) ? true : true;
                    $temp['permission_create'] = isset($request->modules_types[$moduleKey][$moduleTypeKey]['create']) ? true : true;
                    $temp['permission_update'] = isset($request->modules_types[$moduleKey][$moduleTypeKey]['update']) ? true : true;
                    $temp['permission_delete'] = isset($request->modules_types[$moduleKey][$moduleTypeKey]['delete']) ? true : true;
                    $data[]= $temp;
                }

            }

            $dataSend['user_key'] = $userKey;
            $dataSend['entity_key'] = $entityKey;
            $dataSend['entity_permissions'] = $data;

            Orchestrator::setPermissions($dataSend);

                Session::flash('message', trans('entity.store_ok'));

                if(!empty($login->token)){

                    $authToken = $login->token;
                    /* TODO: Check User Role */
    
                    Session::put('X-AUTH-TOKEN', $authToken);
                    $userRequest = Auth::getUser();
    
                    /** Get User Login Level*/
    
                    $userLoginLevels = Orchestrator::getUserLoginLevels($userRequest->user_key);
                    $userRequest->user_login_levels = $userLoginLevels;
    
                    Session::put('user', $userRequest);
    
                    if ((isset($login->user_key)) && (isset($login->libertrium))){
                        $login = Orchestrator::storeUser($login->user_key, ONE::getEntityKey());
                    }
                    elseif(isset($login->user_key)){
                        /*Create User in orchestrator*/
                        Orchestrator::createUser($login);
                    }
    
    
                    return $this->checkRoleUser($userRequest,false,true);
                }

            
            }else{
                return redirect()->back();
            }


        } catch (Exception $e) {
            dd($e->getMessage(),$e->getLine());
            return redirect()->back()->withErrors(["entity.store" => $e->getMessage()]);
        }
    }

    private function checkRoleUser($user , $loginQRCode = false,$login = false)
    {
        try {

            $private = 0;

            $userValidate = Orchestrator::getUserAuthValidate();

            $role = $userValidate->role;

            if (($role == 'admin' || $role == 'manager') ){
                if($role != 'admin'){
                    $userPermissionsList = $userValidate->permissions;;

                    if(isset($userPermissionsList)){
                        Session::put('user_permissions', $userPermissionsList);
                        $this->userPermissionsForSidebar($userPermissionsList);
                        $this->userPermissionsGroupForSidebar($userPermissionsList);
                        Session::put('user_role', 'manager');
                    }
                }else{
                    Session::put('user_permissions_sidebar', ['all']);
                    Session::put('user_permissions_sidebar_groups', ['all']);
                    Session::put('user_role', 'admin');
                }
                if(Session::get('X-ENTITY-KEY', 'undefined') !== 'undefined'){
                    ONE::verifyModulesActive();
                }
                $private = 1;
            }
            
            //OneLog::info("Login done USER[".$role."]: ".$user->email);
            if($private == 1){
                if(\Request::get("adminLogin",false)) {
                    try{
                        $entitiesCount = count(Orchestrator::getEntities());
                    } catch (Exception $e){
                        $entitiesCount = 0;
                    }

                    Session::forget('url_previous');
                    if ($entitiesCount>0)
                        return redirect()->route("private");
                }

                if (ONE::asPermission('admin') && env("EMPAVILLE_MODE",false))
                    return redirect()->action('CbsController@createWizard',['type' => 'empaville']);

                /** Verify previous URL and redirect user*/
                if(Session::has('url_previous')){
                    return redirect()->action('CbsController@createWizard');
                }

                if(ONE::verifyModuleAccess('wui','wizard')){
                    return redirect()->action('PresentationController@show');
                }

                if(Session::has('url_previous')){
                    $url = Session::get('url_previous');
                    Session::forget('url_previous');

                    return  Redirect::to($url);
                }
                return redirect('private');
            }

        } catch (Exception $e) {
            ONE::clearSession();

            //OneLog::error("Login [checkRoleUser]".$e->getMessage());
            return redirect()->back()->withErrors(["auth.role" => $e->getMessage()]);
        }
    }

    private function userPermissionsForSidebar($userPermissions){

        if(empty($userPermissions)){
            return [];
        }

        $show = [];

        foreach($userPermissions as $moduleKey => $module){
            foreach($module as $key => $permissions){
                if($permissions->permission_show == 1)
                    $show[] = $key;
            }
        }
        Session::put('user_permissions_sidebar', $show);
    }

    private function userPermissionsGroupForSidebar($userPermissions){

        if(empty($userPermissions)){
            return [];
        }

        $groups = [
            'participation' => [
                'cb' => [ 'idea', 'forum', 'discussion', 'proposal', 'publicConsultation', 'tematicConsultation', 'survey', 'project'],
                'mp' => ['mp'],
                'q' => ['poll']
            ],
            'contents' => [
                'orchestrator' =>  ['entity_site'],
                'cm' => ['menu', 'pages', 'news', 'events', 'pages']
            ],
            'users' => [
                'auth' => ['manager', 'user', 'in_person_registration', 'confirm_user', 'user_parameters']
            ],
            'research' => [
                'q' => ['q'],
                'analytics' => ['test_code'],
                'wui' => ['open_data']
            ],
            'communication' => [
                'wui' => ['email', 'sms', 'history']
            ],
            'configurations' => [
                'wui' => ['entity_groups'],
                'orchestrator' => ['role'],
                'cm' => ['home_page_type'],
                'cb' => ['parameter_template']
            ]
        ];

        $showGroup = [];

        foreach($groups as $groupName => $group) {
            if(isset($group) and !empty($group)){
                foreach ($group as $key => $modules) {
                    if(isset($modules) and !empty($modules)){
                        foreach ($modules as $permissions) {
                            if(isset($userPermissions->$key->$permissions) && !empty($userPermissions->$key->$permissions) && ($userPermissions->$key->$permissions->permission_show == 1 || $userPermissions->$key->$permissions->permission_show == true)){
                                $showGroup[] = $groupName;
                                break;

                            }
                        }
                    }
                }
            }
        }

        Session::put('user_permissions_sidebar_groups', $showGroup);
    }

}
