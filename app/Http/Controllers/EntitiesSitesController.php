<?php

namespace App\Http\Controllers;

use App\ComModules\Auth;
use App\ComModules\Notify;
use App\ComModules\Orchestrator;
use App\ComModules\CB;
use App\Http\Requests\EntitySiteRequest;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use App\One\One;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Collection;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\UserRequest;
use App\Http\Requests\EntityRequest;
use Datatables;
use Mockery\Matcher\Not;
use Session;
use View;
use Breadcrumbs;
use App\ComModules;
use Yajra\Datatables\Engines\CollectionEngine;

class EntitiesSitesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(Session::get('user_role') != 'admin'){
            return redirect()->back()->withErrors(["private" => trans('privateEntitiesDivided.permission_message')]);
        }

        if (ONE::isEntity()) {
            $entityKey = ONE::getEntityKey();
            $entity = Orchestrator::getEntity($entityKey);
            $title = trans('privateEntitiesSites.show_sites');

            return view('private.entities.sites.index', compact('title', 'entity'));
        } else {

            return redirect()->back()->withErrors(["entity.show" => trans('privateEntity.invalidEntity')]);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(Session::get('user_role') != 'admin'){
            return redirect()->back()->withErrors(["private" => trans('privateEntitiesDivided.permission_message')]);
        }

        $entityKey = ONE::getEntityKey();

        $entity = Orchestrator::getEntity($entityKey);
        $languages = Orchestrator::getLanguageList();

        $layouts = [];
        foreach ($entity->layouts as $layout) {
            $layouts[$layout->layout_key] = $layout->name;
        }

        $title = trans('privateEntitiesSites.create_site_managers');
        return view('private.entities.sites.site', compact('title', 'entityKey', 'layouts', 'languages'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(Session::get('user_role') != 'admin'){
            return redirect()->back()->withErrors(["private" => trans('privateEntitiesDivided.permission_message')]);
        }

        try {
            $data['end_date'] = $request->input('end_date');
            $data['start_date'] = $request->input('start_date');
            
            if (!empty($data["end_date"]) && !Carbon::parse($data["end_date"])->gt(Carbon::parse($data["start_date"])))
                return redirect()->back()->withErrors([trans("privateEntitiesDivided.end_date_must_be_greater_then_start_date")]);
                
            $entityKey = ONE::getEntityKey();
            $languages = Orchestrator::getLanguages($entityKey);
            $langsTemp = [];
            $contentTranslation = [];
            foreach($languages as $language){
                $contentTranslation[] = [
                    'language_id'   =>  $language->id,
                    'language_code' =>  $language->code,
                    'content'       =>  $language->default == true ? $request->input("required_content_".$language->code) :$request->input("content_".$language->code)
                ];
                $langsTemp[] = $language->code;
            }

            $request['use_terms'] = $contentTranslation;

            $site = Orchestrator::setNewEntitySite($entityKey, $request->all());
            $notify = Notify::newSiteEmailsTemplates([$site->key], $langsTemp);

            Session::flash('message', trans('entity.storeOk'));
            return redirect()->action('EntitiesSitesController@show', ['siteKey' => $site->key]);
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["entities.storeSite" => $e->getMessage()])->withInput();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param $siteKey
     * @return \Illuminate\Http\Response
     * @internal param int $id
     */
    public function show($siteKey)
    {
        Session::put('SITE_KEY', $siteKey ?? null);

        if(Session::get('user_role') != 'admin'){
            return redirect()->back()->withErrors(["private" => trans('privateEntitiesDivided.permission_message')]);
        }

        try {

            $entityKey = ONE::getEntityKey();

            $languages = Orchestrator::getLanguageList();

            $site = Orchestrator::getSite($siteKey);

            $homePageTypesList = Orchestrator::getHomePageTypeParents();

            $homePageTypes = [];

            foreach ($homePageTypesList as $homePageType) {
                $homePageTypes[$homePageType->home_page_type_key] = $homePageType->name;
            }

            $sidebar = 'site';
            $active = 'details';

            Session::put('sidebarArguments', ['siteKey' => $siteKey, 'activeFirstMenu' => 'details']);


            $title = trans('privateEntitiesSites.show_site') . ' ' . (isset($site->name) ? $site->name : null);
            return view('private.entities.sites.site', compact('title', 'entityKey', 'site', 'homePageTypes', 'languages', 'sidebar', 'active'));
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["entities.updateSite" => $e->getMessage()]);
        }
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param $siteKey
     * @return \Illuminate\Http\Response
     * @internal param int $id
     */
    public function edit($siteKey)
    {
        if(Session::get('user_role') != 'admin'){
            return redirect()->back()->withErrors(["private" => trans('privateEntitiesDivided.permission_message')]);
        }

        try {
            $entityKey = ONE::getEntityKey();
            $entity = Orchestrator::getEntity($entityKey);
            $layouts = [];
            foreach ($entity->layouts as $layout) {
                $layouts[$layout->layout_key] = $layout->name;
            }
            $site = Orchestrator::getSite($siteKey);
            $languages = Orchestrator::getLanguageList();

            $homePageTypesList = Orchestrator::getHomePageTypeParents();
            $homePageTypes = [];
            foreach ($homePageTypesList as $homePageType) {
                $homePageTypes[$homePageType->home_page_type_key] = $homePageType->name;
            }

            $sidebar = 'site';
            $active = 'details';

            Session::put('sidebarArguments', ['siteKey' => $siteKey, 'activeFirstMenu' => 'details']);

            $title = trans('privateEntitiesSites.update_site') . ' ' . (isset($site->name) ? $site->name : null);
            return view('private.entities.sites.site', compact('title', 'entityKey', 'site', 'layouts', 'homePageTypes', 'languages', 'sidebar', 'active'));
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["entities.updateSite" => $e->getMessage()]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param EntitySiteRequest|Request $request
     * @param $siteKey
     * @return \Illuminate\Http\Response
     * @internal param int $id
     */
    public function update(EntitySiteRequest $request, $siteKey)
    {
        if(Session::get('user_role') != 'admin'){
            return redirect()->back()->withErrors(["private" => trans('privateEntitiesDivided.permission_message')]);
        }


        try {
            $languages = Orchestrator::getLanguageList();
            //$entityKey = ONE::getEntityKey();

            $contentTranslation = [];
            foreach($languages as $language){
                $contentTranslation[] = [
                    'language_id'   =>  $language->id,
                    'language_code' =>  $language->code,
                    'content'       =>  $language->default == true ? $request->input("required_content_".$language->code) :$request->input("content_".$language->code)
                ];
            }

            $request['use_terms'] = $contentTranslation;

            $site = Orchestrator::updateEntitySite($request, $siteKey);
            Session::flash('message', trans('entity.updateOk'));
            return redirect()->action('EntitiesSitesController@show', ['siteKey' => $site->key]);
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["entities.updateSite" => $e->getMessage()]);
        }
    }

    public function deleteConfirm($siteKey){
        $data = array();
        $data['action'] = action("EntitiesSitesController@destroy", ['siteKey' => $siteKey]);
        $data['title'] = "DELETE";
        $data['msg'] = "Are you sure you want to delete this Site for this Entity?";
        $data['btn_ok'] = "Delete";
        $data['btn_ko'] = "Cancel";
        return view("_layouts.deleteModal", $data);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $siteKey
     * @return \Illuminate\Http\Response
     * @internal param int $id
     */
    public function destroy($siteKey)
    {
        if(Session::get('user_role') != 'admin'){
            return redirect()->back()->withErrors(["private" => trans('privateEntitiesDivided.permission_message')]);
        }

        try {
            Orchestrator::deleteEntitySite($siteKey);
            Session::flash('message', trans('entity.delete_ok'));
            return action('EntitiesSitesController@index');
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["entities.deleteSite" => $e->getMessage()]);
        }
    }

    public function tableSitesEntity()
    {
        try {
            if(Session::get('user_role') == 'admin'){
                $entityKey = ONE::getEntityKey();
                $entity = Orchestrator::getEntity($entityKey);
                $sites = $entity->sites;

                // in case of json
                $collection = Collection::make($sites);
            }else
                $collection = Collection::make([]);

            $edit = Session::get('user_role') == 'admin';
            $delete = Session::get('user_role') == 'admin';

            return Datatables::of($collection)
                ->editColumn('name', function ($collection) {
                    return "<a href='" . action('EntitiesSitesController@show', ['siteKey' => $collection->key]) . "'>" . $collection->name . "</a>";
                })
                ->addColumn('action', function ($collection) use($edit, $delete) {
                    if($edit == true and $delete == true)
                        return ONE::actionButtons(['siteKey' => $collection->key], ['form' => 'entitySites', 'edit' => 'EntitiesSitesController@edit', 'delete' => 'EntitiesSitesController@deleteConfirm']);
                    elseif($edit == true and $delete == false)
                        return ONE::actionButtons(['siteKey' => $collection->key], ['form' => 'entitySites', 'edit' => 'EntitiesSitesController@edit']);
                    elseif($edit == false and $delete == true)
                        return ONE::actionButtons(['siteKey' => $collection->key], ['form' => 'entitySites', 'delete' => 'EntitiesSitesController@deleteConfirm']);
                    else
                        return false;
                })
                ->rawColumns(['name','action'])
                ->make(true);
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["entitiesDivided.showSites" => $e->getMessage()]);
        }
    }

    /**
     * Get the site additional Url's
     * @param $siteKey
     * @return $this
     */
    public function getSiteAdditionalUrlsTable($siteKey)
    {
        try {
            $additionalLinks = Orchestrator::getSiteAdditionalLinks($siteKey);
            $collection = Collection::make($additionalLinks);

            return Datatables::of($collection)
                ->editColumn('link', function($collection){

                    return "<a href='" . action('SiteAdditionalUrlsController@show', ['id' => $collection->id]) . "'>" . $collection->link . "</a>";

                })
                ->addColumn('action', function ($collection){
                    return ONE::actionButtons($collection->id, ['form' => 'SiteAdditionalUrlsController', 'edit' => 'SiteAdditionalUrlsController@edit', 'delete' => 'SiteAdditionalUrlsController@deleteConfirm']);
                })
                ->rawColumns(['link','action'])
                ->make(true);
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["entitiesDivided.showSites" => $e->getMessage()]);
        }
    }


    public function tableSiteEmailsManagers($siteKey)
    {
        try{
            if(Session::get('user_role') == 'admin'){
                $templates = Notify::getEmailTemplatesSite($siteKey);

                $collection = Collection::make($templates);
            }else
                $collection = Collection::make([]);

            $edit = Session::get('user_role') == 'admin';
            $delete = Session::get('user_role') == 'admin';

            return Datatables::of($collection)
                ->editColumn('templateSubject', function($collection) use ($siteKey){
                    return '<a href="' . action('EmailTemplatesController@show', ['templateKey' => $collection->email_template_key]) . '">'. $collection->type->name . '</a>';
                })->addColumn('action',function($collection) use ($siteKey, $edit, $delete){
                    if($edit == true and $delete == true)
                        return ONE::actionButtons(['templateKey' => $collection->email_template_key], ['form' => 'emailTemplate', 'edit' => 'EmailTemplatesController@edit','delete' => 'EmailTemplatesController@delete']);
                    elseif($edit == false and $delete == true)
                        return ONE::actionButtons(['templateKey' => $collection->email_template_key], ['form' => 'emailTemplate','delete' => 'EmailTemplatesController@delete']);
                    if($edit == true and $delete == false)
                        return ONE::actionButtons(['templateKey' => $collection->email_template_key], ['form' => 'emailTemplate', 'edit' => 'EmailTemplatesController@edit']);
                    else
                        return null;
                })
                ->rawColumns(['templateSubject','action'])
                ->make(true);
        }catch (Exception $e){
            return redirect()->back()->withErrors(["entities.tableSiteEmailsManagers" => $e->getMessage()]);
        }
    }

    public function showHomePageConfigurations($siteKey){

        $homePageTypesList = Orchestrator::getHomePageTypeParents();

        $homePageTypes = [];

        foreach ($homePageTypesList as $homePageType) {
            $homePageTypes[$homePageType->home_page_type_key] = $homePageType->name;
        }

        $sidebar = 'site';
        $active = 'configurations';

        Session::put('sidebarArguments', ['siteKey' => $siteKey, 'activeFirstMenu' => 'configurations']);

        return view('private.entities.sites.homePageConfigurations', compact('siteKey', 'homePageTypes', 'sidebar', 'active'));
    }

    public function showEmailTemplates($siteKey){
        $sidebar = 'site';
        $active = 'emailTemplates';

        Session::put('sidebarArguments', ['siteKey' => $siteKey, 'activeFirstMenu' => 'emailTemplates']);

        return view('private.entities.sites.emailTemplates', compact('siteKey', 'sidebar', 'active'));
    }

    public function showSiteLevels($siteKey){
        $sidebar = 'site';
        $active = 'siteLevels';

        Session::put('sidebarArguments', ['siteKey' => $siteKey, 'activeFirstMenu' => 'siteLevels']);
        return view('private.entities.sites.loginLevels.index', compact('siteKey', 'sidebar', 'active'));
    }
    public function showStepperLoginList($siteKey){
        $sidebar = 'site';
        $active = 'stepperLogin';

        Session::put('sidebarArguments', ['siteKey' => $siteKey, 'activeFirstMenu' => 'stepperLogin']);

        return view('private.entities.sites.stepperLogin.index', compact('siteKey','sidebar','active'));
    }


    /** Get Site Use Terms
     * @param $siteKey
     * @param null $version
     * @return EntitiesSitesController|\Illuminate\Http\RedirectResponse|View
     */
    public function showUseTerms($siteKey,$version = null){
        try {
            $title = trans('privateSiteEthics.use_terms');
            $type = 'use_terms';
            $languages = Orchestrator::getLanguageList();
            $siteEthicsData = Orchestrator::getSiteEthics($siteKey,'use_terms',$version);
            $siteEthic = $siteEthicsData->site_ethic;
            $siteEthicVersionsData = $siteEthicsData->site_ethic_versions;
            /** If use terms not defined show message*/
            if(empty($siteEthic)){
                $message = trans('privateSiteEthics.no_use_terms_defined_please_add');
                $data = [];
                $data['type'] = $type;
                $data['title'] = $title;
                $data['message'] = $message;
                $data['siteKey'] = $siteKey;
                $data['sidebar'] = 'site';
                    $data['active'] = 'use_terms';
                return view('private.entities.sites.siteEthicEmpty', $data);
            }

            foreach ($siteEthicVersionsData as $siteEthicVersion) {
                if ($siteEthicVersion->active) {
                    $marker = '* ';
                } else {
                    $marker = '';
                }
                $siteEthicVersions[$siteEthicVersion->version] = $marker . 'v' . $siteEthicVersion->version . ' ' . $siteEthicVersion->created_at;
            }
            $actionUrl = action('EntitiesSitesController@showUseTerms',['site_key' => $siteKey]);
            $data = [];
            $data['title'] = $title;
            $data['type'] = $type;
            $data['siteEthic'] = $siteEthic;
            $data['languages'] = $languages;
            $data['siteKey'] = $siteKey;
            $data['actionUrl'] = $actionUrl;
            $data['siteEthicVersions'] = $siteEthicVersions ?? [];
            $data['sidebar'] = 'site';
            $data['active'] = 'use_terms';

            Session::put('sidebarArguments', ['siteKey' => $siteKey, 'activeFirstMenu' => 'use_terms']);

            return view('private.entities.sites.siteEthic', $data);
        } catch (Exception $e) {
            return redirect()->back()->withErrors([trans('privateSiteEthics.show_use_terms_error') => $e->getMessage()]);
        }

    }


    /** Get site Privacy Policy
     * @param $siteKey
     * @param null $version
     * @return EntitiesSitesController|\Illuminate\Http\RedirectResponse|View
     */
    public function showPrivacyPolicy($siteKey,$version = null){
        try {
            $title = trans('privateSiteEthics.privacy_policy');
            $type = 'privacy_policy';
            $languages = Orchestrator::getLanguageList();
            $siteEthicsData = Orchestrator::getSiteEthics($siteKey,'privacy_policy',$version);
            $siteEthic = $siteEthicsData->site_ethic;
            $siteEthicVersionsData = $siteEthicsData->site_ethic_versions;
            /** If privacy policy not defined show message*/
            if(empty($siteEthic)){
                $message = trans('privateSiteEthics.no_privacy_policy_defined_please_add');
                $data = [];
                $data['type'] = $type;
                $data['title'] = $title;
                $data['message'] = $message;
                $data['siteKey'] = $siteKey;
                $data['sidebar'] = 'site';
                $data['active'] = 'privacy_policy';
                return view('private.entities.sites.siteEthicEmpty', $data);
            }

            foreach ($siteEthicVersionsData as $siteEthicVersion) {
                if ($siteEthicVersion->active) {
                    $marker = '* ';
                } else {
                    $marker = '';
                }
                $siteEthicVersions[$siteEthicVersion->version] = $marker . 'v' . $siteEthicVersion->version . ' ' . $siteEthicVersion->created_at;
            }
            $actionUrl = action('EntitiesSitesController@showPrivacyPolicy',['site_key' => $siteKey]);
            $data = [];
            $data['title'] = $title;
            $data['type'] = $type;
            $data['siteEthic'] = $siteEthic;
            $data['languages'] = $languages;
            $data['siteKey'] = $siteKey;
            $data['actionUrl'] = $actionUrl;
            $data['siteEthicVersions'] = $siteEthicVersions ?? [];
            $data['sidebar'] = 'site';
            $data['active'] = 'privacy_policy';

            Session::put('sidebarArguments', ['siteKey' => $siteKey, 'activeFirstMenu' => 'privacy_policy']);
            return view('private.entities.sites.siteEthic', $data);
        } catch (Exception $e) {
            return redirect()->back()->withErrors([trans('privateSiteEthics.show_privacy_policy_error') => $e->getMessage()]);
        }

    }
}
