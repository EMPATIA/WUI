<?php

namespace App\Http\Controllers;

use App\ComModules\Auth;
use App\ComModules\Files;
use App\ComModules\Notify;
use App\ComModules\Orchestrator;
use App\ComModules\CB;
use App\Http\Requests\EntitySiteRequest;
use Carbon\Carbon;
use Exception;
use File;
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
use JildertMiedema\LaravelPlupload\Facades\Plupload;
use Mockery\Matcher\Not;
use Route;
use Session;
use View;
use Breadcrumbs;
use App\ComModules;
use Yajra\Datatables\Engines\CollectionEngine;

class EntitiesDividedController extends Controller
{

    public function __construct()
    {
        View::share('private.entitiesDivided', trans('entity.entity'));

    }


    public function edit($entityKey)
    {
        if(!ONE::verifyUserPermissions('orchestrator', 'entity', 'update')){
            return redirect()->back()->withErrors(["private" => trans('privateEntitiesDivided.permission_message')]);
        }

        $carbon = Carbon::now();
        $data = [];
        try {
            $entity = Orchestrator::getEntity($entityKey);

            $language = Orchestrator::getLanguageList();

            $country = Orchestrator::getCountryList();

            $timezone = Orchestrator::getTimeZoneList();

            $currency = Orchestrator::getCurrencyList();


            $lang_name = array();
            foreach ($language as $lang) {
                $lang_name[$lang->id] = $lang->name;
            }


            $country_name = array();
            foreach ($country as $cnt) {
                $country_name[$cnt->id] = $cnt->name;
            }

            $timezone_name = array();
            foreach ($timezone as $tz) {
                $timezone_name[$tz->id] = $tz->name;
            }

            $currency_name = array();
            foreach ($currency as $curr) {
                $currency_name[$curr->id] = $curr->currency;
            }
            $data['entity'] = $entity;
            $data['language'] = $lang_name;
            $data['country'] = $country_name;
            $data['timezone'] = $timezone_name;
            $data['currency'] = $currency_name;
            $data['carbon'] = $carbon;
            $data['title'] = $title = trans('privateEntities.edit_entity') . ' ' . (isset($entity->name) ? $entity->name : null);

            $data['sidebar'] = 'entity';
            $data['active'] = 'details';

            Session::put('sidebarArguments', ['activeFirstMenu' => 'details']);

            return view('private.entities.entity.index', $data);
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["entities.edit" => $e->getMessage()]);
        }
    }


    /**
     * Update the specified resource in storage.
     *
     * @param EntityRequest|Request $request
     * @return \Illuminate\Http\Response
     * @internal param int $id
     */
    public function update(EntityRequest $request)
    {
        if(!ONE::verifyUserPermissions('orchestrator', 'entity', 'update')){
            return redirect()->back()->withErrors(["private" => trans('privateEntitiesDivided.permission_message')]);
        }

        try {

            $entityKey = ONE::getEntityKey();

            Orchestrator::updateEntity($request, $entityKey);
            Session::flash('message', trans('entity.update_ok'));
            return redirect()->action('EntitiesDividedController@showEntity');

        } catch (Exception $e) {
            //TODO: save inputs
            return redirect()->back()->withErrors(["entity.update" => $e->getMessage()]);
        }
    }



    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function showEntity()
    {
        if(!ONE::verifyUserPermissions('orchestrator', 'entity', 'show')){
            return redirect()->back()->withErrors(["private" => trans('privateEntitiesDivided.permission_message')]);
        }

        if(One::isEntity()){
            try {
                $entityKey = ONE::getEntityKey();
                $entity = Orchestrator::getEntity($entityKey);
                $title = trans('privateEntitiesDivided.show_entity') . ' ' . (isset($entity->name) ? $entity->name : null);

                $sidebar = 'entity';
                $active = 'details';

                Session::put('sidebarArguments', ['activeFirstMenu' => 'details']);

                return view('private.entities.entity.index', compact('title', 'entity','entityKey', 'sidebar', 'active'));

            } catch (Exception $e) {
                return redirect()->back()->withErrors(["entities.show" => $e->getMessage()]);
            }
        }
    }





    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function showEntityDomainsList()
    {
        if(!ONE::verifyUserPermissions('orchestrator', 'entity', 'show')){
            return redirect()->back()->withErrors(["private" => trans('privateEntitiesDivided.permission_message')]);
        }

        if(One::isEntity()){
            try {
                $entityKey = ONE::getEntityKey();
                $entity = Orchestrator::getEntity($entityKey);
                $title = trans('privateEntitiesDivided.manage_domains_list') . ' ' . (isset($entity->name) ? $entity->name : null);

                return view('private.entities.authMethods.domainNamesList', compact('title', 'entity'));

            } catch (Exception $e) {
                return redirect()->back()->withErrors(["entities.show" => $e->getMessage()]);
            }
        }
    }

    /**
     * @return $this|View
     */
    public function showSites()
    {
        if (ONE::isEntity()) {
            $entity = Orchestrator::getEntity(ONE::getEntityKey());

            $title = trans('privateEntitiesDivided.show_sites_index');
            return view('private.entities.sites.index', compact('entity'));
        } else {

            return redirect()->back()->withErrors(["entity.show" => trans('privateEntity.invalidEntity')]);
        }
    }

    /**
     * @param $siteKey
     * @return $this|View
     */
    public function showEntitySite($siteKey)
    {

        try {

            $entityKey = ONE::getEntityKey();

            $languages = Orchestrator::getLanguageList();
            $site = Orchestrator::getSite($siteKey);
            $homePageTypesList = Orchestrator::getHomePageTypeParents();

            $homePageTypes = [];

            foreach ($homePageTypesList as $homePageType) {
                $homePageTypes[$homePageType->home_page_type_key] = $homePageType->name;
            }


            $title = trans('privateSites.show_site') . ' ' . (isset($site->name) ? $site->name : null);
            return view('private.entities.sites.site', compact('title', 'entityKey', 'site', 'homePageTypes', 'languages'));
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["entities.updateSite" => $e->getMessage()]);
        }
    }

    /**
     * @return View
     */
    public function createEntitySite()
    {
        $entityKey = ONE::getEntityKey();
        $entity = Orchestrator::getEntity($entityKey);
        $languages = Orchestrator::getLanguageList();

        $layouts = [];
        foreach ($entity->layouts as $layout) {
            $layouts[$layout->layout_key] = $layout->name;
        }

        $title = trans('privateSites.create_site_managers');
        return view('private.entities.sites.site', compact('title', 'entityKey', 'layouts', 'languages'));
    }

    /**
     * @param EntitySiteRequest $request
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function storeEntitySite(EntitySiteRequest $request)
    {
        if(!ONE::verifyUserPermissions('orchestrator', 'entity', 'create')){
            return redirect()->back()->withErrors(["private" => trans('privateEntitiesDivided.permission_message')]);
        }

        try {
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
            return redirect()->action('EntitiesController@showEntitySite', ['siteKey' => $site->key]);
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["entities.storeSite" => $e->getMessage()]);
        }
    }


    /**
     * @param $siteKey
     * @return $this|View
     */
    public function editEntitySite($siteKey)
    {
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

            $title = trans('privateSites.update_site') . ' ' . (isset($site->name) ? $site->name : null);
            return view('private.entities.sites.site', compact('title', 'entityKey', 'site', 'layouts', 'homePageTypes', 'languages'));
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["entities.updateSite" => $e->getMessage()]);
        }
    }

    /**
     * @param EntitySiteRequest $request
     * @param $siteKey
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function updateEntitySite(EntitySiteRequest $request, $siteKey)
    {
        try {
            $languages = Orchestrator::getLanguageList();
            $entityKey = ONE::getEntityKey();

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
            return redirect()->action('EntitiesDividedController@showEntitySite', ['siteKey' => $site->key]);
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["entities.updateSite" => $e->getMessage()]);
        }
    }

    /**
     * @param $siteKey
     * @return View
     */
    public function deleteSiteConfirm($siteKey)
    {
        $data = array();
        $data['action'] = action("EntitiesDividedController@destroyEntitySite", ['siteKey' => $siteKey]);
        $data['title'] = "DELETE";
        $data['msg'] = "Are you sure you want to delete this Site for this Entity?";
        $data['btn_ok'] = "Delete";
        $data['btn_ko'] = "Cancel";
        return view("_layouts.deleteModal", $data);
    }

    /**
     * @param $siteKey
     * @return $this|string
     */
    public function destroyEntitySite($siteKey)
    {
        try {


            Orchestrator::deleteEntitySite($siteKey);
            Session::flash('message', trans('entity.delete_ok'));
            return action('EntitiesDividedController@showSites');
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["entities.deleteSite" => $e->getMessage()]);
        }

    }

    /**
     * @param $siteKey
     * @return $this
     */
    public function tableSiteEmailsManagers($siteKey)
    {
        try{

            $templates = Notify::getEmailTemplatesSite($siteKey);

            $collection = Collection::make($templates);

            return Datatables::of($collection)
                ->editColumn('templateSubject', function($collection) use ($siteKey){
                    return '<a href="' . action('EmailTemplatesController@show', ['templateKey' => $collection->email_template_key]) . '">'. $collection->type->name . '</a>';
                })->addColumn('action',function($collection) use ($siteKey){
                    return ONE::actionButtons(['templateKey' => $collection->email_template_key], ['form' => 'emailTemplate', 'edit' => 'EmailTemplatesController@edit','delete' => 'EmailTemplatesController@delete']);
                })
                ->make(true);
        }catch (Exception $e){
            return redirect()->back()->withErrors(["entities.tableSiteEmailsManagers" => $e->getMessage()]);
        }
    }

    /**
     * @return $this|View
     */
    public function showLayouts()
    {
        if(!ONE::verifyUserPermissions('orchestrator', 'entity_layout', 'show')){
            return redirect()->back()->withErrors(["private" => trans('privateEntitiesDivided.permission_message')]);
        }

        if (ONE::isEntity()) {
            $entity = Orchestrator::getEntity(ONE::getEntityKey());
            $entityKey = $entity->entity_key;
            $title = trans('privateEntitiesDivided.show_layouts_index');

            $sidebar = 'entity';
            $active = 'layouts';

            Session::put('sidebarArguments', ['activeFirstMenu' => 'layouts']);

            return view('private.entities.layouts.index', compact('title', 'entity', 'entityKey','sidebar', 'active'));
        } else {
            return redirect()->back()->withErrors(["entity.show" => trans('privateEntity.invalidEntity')]);
        }
    }

    /**
     * @return $this
     */
    public function tableLayoutsEntity()
    {

        try {
            if(ONE::verifyUserPermissions('orchestrator', 'entity_layout', 'show')){
                $entityKey = One::getEntityKey();
                $entity = Orchestrator:: getEntity($entityKey);

                // in case of json
                $layouts = Collection::make($entity->layouts);
            }else
                $layouts = Collection::make([]);

            $delete = ONE::verifyUserPermissions('orchestrator', 'entity_layout', 'delete');

            return Datatables::of($layouts)
                ->editColumn('name', function ($layouts) {
                    return "<a href='" . action('LayoutsController@show', $layouts->layout_key) . "'>" . $layouts->name . "</a>";
                })
                ->addColumn('action', function ($layouts) use($delete){
                    if($delete)
                        return ONE::actionButtons(['layoutKey' => $layouts->layout_key], ['delete' => 'EntitiesDividedController@deleteLayoutConfirm']);
                    else
                        return null;
                })
                ->make(true);
        } catch (Exception $e) {
            return redirect()->back()->withErrors([trans('privateEntities.tableLayoutsEntity') => $e->getMessage()]);
        }
    }

    /**
     * @return View
     */
    public function addLayout()
    {
        $title = trans('privateEntitiesDivided.add_layout');


        $entityKey = ONE::getEntityKey();

        $sidebar = 'entity';
        $active = 'layouts';

        Session::put('sidebarArguments', ['activeFirstMenu' => 'layouts']);

        return view('private.entities.layouts.addLayout', compact('title', 'entityKey', 'sidebar', 'active'));


    }

    /**
     * @return $this
     */
    public function tableAddLayout()
    {
        try {

            if(ONE::verifyUserPermissions('orchestrator', 'entity_layout', 'show')){
                $layouts = Orchestrator::getLayouts();
                $entity = Orchestrator::getEntity(ONE::getEntityKey());

                // Getting layouts reference to an array
                $layoutsReferences = [];
                foreach ($entity->layouts as $item) {
                    $layoutsReferences[] = $item->reference;
                }

                // Rebuild an array with all languages that aren't in entity
                $layoutsList = [];
                foreach ($layouts as $layout) {
                    if (!in_array($layout->reference, $layoutsReferences)) {
                        $layoutsList[] = $layout;
                    }
                }

                // in case of json
                $collection = Collection::make($layoutsList);

            }else
                $collection = Collection::make([]);

            $create = ONE::verifyUserPermissions('orchestrator', 'entity_layout', 'create');
            return Datatables::of($collection)
                ->editColumn('name', function ($collection) {
                    return $collection->name;
                })
                ->addColumn('action', function ($collection) use($create){
                    if($create)
                        return ONE::actionButtons(['layoutKey' => $collection->layout_key], ['add' => 'EntitiesDividedController@addLayoutAction']);
                    else
                        return null;
                })
                ->make(true);
        } catch (Exception $e) {
            return redirect()->back()->withErrors([trans('privateEntities.tableAddLayout') => $e->getMessage()]);
        }
    }

    /**
     * @param $layoutKey
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function addLayoutAction($layoutKey)
    {
        try {
            $entityKey = ONE::getEntityKey();
            $entity = Orchestrator::setLayoutEntity($entityKey, $layoutKey);
            return redirect()->action('EntitiesDividedController@showLayouts');
        } catch (Exception $e) {
            return redirect()->back()->withErrors([trans('privateEntities.addLayoutAction') => $e->getMessage()]);
        }
    }

    /**
     * @param $layoutKey
     * @return View
     */
    public function deleteLayoutConfirm($layoutKey)
    {
        $data = array();

        $data['action'] = action("EntitiesDividedController@deleteLayout", ['layoutKey' => $layoutKey]);
        $data['title'] = "DELETE";
        $data['msg'] = "Are you sure you want to delete this Layout for this Entity?";
        $data['btn_ok'] = "Delete";
        $data['btn_ko'] = "Cancel";

        return view("_layouts.deleteModal", $data);
    }

    /**
     * @param $layoutKey
     * @return $this|string
     */
    public function deleteLayout($layoutKey)
    {

        try {
            $entityKey = ONE::getEntityKey();
            Orchestrator::deleteEntityLayout($entityKey, $layoutKey);
            return action('EntitiesDividedController@showLayouts');
        } catch (Exception $e) {
            return redirect()->back()->withErrors([trans('privateEntities.show') => $e->getMessage()]);
        }
    }

    /**
     * @return $this|View
     */
    public function showLanguages()
    {
        if(!ONE::verifyUserPermissions('orchestrator', 'entity_language', 'show')){
            return redirect()->back()->withErrors(["private" => trans('privateEntitiesDivided.permission_message')]);
        }

        if (ONE::isEntity()) {
            $entity = Orchestrator::getEntity(ONE::getEntityKey());
            $entityKey = $entity->entity_key;
            $title = trans('privateEntitiesDivided.show_languages');

            $sidebar = 'entity';
            $active = 'languages';

            Session::put('sidebarArguments', ['activeFirstMenu' => 'languages']);

            return view('private.entities.languages.index', compact('title', 'entity', 'entityKey', 'sidebar', 'active'));
        } else {
            return redirect()->back()->withErrors(["entity.show" => trans('privateEntity.invalidEntity')]);
        }
    }

    /**
     * @return $this|View
     */
    public function addLanguage()
    {
        $title = trans('privateEntitiesDivided.add_language');

        if (ONE::isEntity()) {
            $entityKey = ONE::getEntityKey();

            $sidebar = 'entity';
            $active = 'languages';

            Session::put('sidebarArguments', ['activeFirstMenu' => 'languages']);

            return view('private.entities.languages.addLang', compact('title', 'entityKey', 'active', 'sidebar'));
        } else {
            return redirect()->back()->withErrors(["entity.show" => trans('privateEntity.invalidEntity')]);
        }
    }

    /**
     * @return $this
     */
    public function tableAddLanguageEntity()
    {
        try {

            $entityKey = ONE::getEntityKey();

            // Get all languages
            $langs = Orchestrator::getAllLanguages();

            // Get Entity languages
            $entity = Orchestrator::getEntity($entityKey);

            // Getting languages codes to an array
            $langCodes = [];
            foreach ($entity->languages as $item) {
                $langCodes[] = $item->code;
            }

            // Rebuild an array with all languages that aren't in entity
            $languageList = [];
            foreach ($langs as $lang) {
                if (!in_array($lang->code, $langCodes)) {
                    $languageList[] = $lang;
                }
            }

            // in case of json
            $language = Collection::make($languageList);

            return Datatables::of($language)
                ->editColumn('name', function ($language) {
                    return $language->name;
                })
                ->addColumn('action', function ($language) use ($entityKey) {
                    return ONE::actionButtons([$language->id], ['add' => 'EntitiesDividedController@addLanguageAction']);
                })
                ->make(true);
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["entities.tableAddLanguageEntity" => $e->getMessage()]);
        }
    }

    /**
     * @param $languageId
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function addLanguageAction($languageId)
    {
        $entityKey = ONE::getEntityKey();


        try {

            Orchestrator::setLanguage($languageId, $entityKey, 0);
            return redirect()->action('EntitiesDividedController@showLanguages');

        } catch (Exception $e) {
            return redirect()->back()->withErrors(["entity.store" => $e->getMessage()]);
        }
    }

    /**
     * @return $this
     */
    public function tableLanguagesEntity()
    {

        try {
            if(ONE::verifyUserPermissions('orchestrator', 'entity_language', 'show')){

                $entityKey = One::getEntityKey();

                $entity = Orchestrator:: getEntity($entityKey);

                // in case of json
                $language = Collection::make($entity->languages);

            }else
                $language = Collection::make([]);

            $delete = ONE::verifyUserPermissions('orchestrator', 'entity_language', 'delete');

            return Datatables::of($language)
                ->editColumn('name', function ($language) {
                    return "<a href='" . action('LanguagesController@show', $language->id) . "'>" . $language->name . "</a>";
                })
                ->addColumn('activateAction', function ($language) {
                    return ($language->pivot->default == 0) ? ONE::actionButtons([$language->id], ['activate' => 'EntitiesDividedController@makeLangDefaultConfirm']) : '<span class="badge badge-success">' . trans("entities.default") . '</span>';
                })
                ->addColumn('action', function ($language) use($delete){
                    if($delete)
                        return ONE::actionButtons([$language->id], ['delete' => 'EntitiesDividedController@deleteLangConfirm']);
                    else
                        return null;
                })
                ->make(true);
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["entities.tableLanguagesEntity" => $e->getMessage()]);
        }
    }

    /**
     * @param $languageId
     * @return View
     */
    public function makeLangDefaultConfirm($languageId)
    {
        $data = array();

        $data['action'] = action("EntitiesDividedController@makeLangDefault", [$languageId]);
        $data['title'] = "MAKE DEFAULT";
        $data['msg'] = "Are you sure you want to make this Language Default for this entity?";
        $data['btn_ok'] = "Make default";
        $data['btn_ko'] = "Cancel";

        return view("_layouts.activateModal", $data);
    }

    /**
     * @param $languageId
     * @return string
     * @throws Exception
     */
    public function makeLangDefault($languageId)
    {
        $entityKey = ONE::getEntityKey();

        Orchestrator::updateDefaultLanguage($entityKey, $languageId, 1);

        return action('EntitiesDividedController@showLanguages');
    }

    /**
     * @param $languageId
     * @return View
     */
    public function deleteLangConfirm($languageId)
    {
        $data = array();

        $data['action'] = action("EntitiesDividedController@deleteLang", [$languageId]);
        $data['title'] = "DELETE";
        $data['msg'] = "Are you sure you want to delete this Language for this Entity?";
        $data['btn_ok'] = "Delete";
        $data['btn_ko'] = "Cancel";

        return view("_layouts.deleteModal", $data);
    }

    /**
     * @param $languageId
     * @return $this|string
     */
    public function deleteLang($languageId)
    {

        try {

            $entityKey = One::getEntityKey();

            Orchestrator::deleteLanguage($entityKey, $languageId);

            return action('EntitiesDividedController@showLanguages');
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["entities.show" => $e->getMessage()]);
        }
    }


    /**
     * @return $this|View
     */
    public function showManagers()
    {
        if (ONE::isEntity()) {
            $entity = Orchestrator::getEntity(ONE::getEntityKey());
            $title = trans('privateEntitiesDivided.show_managers');
            return view('private.entities.managers.index', compact('title', 'entity'));
        } else {
            return redirect()->back()->withErrors(["entity.show" => trans('privateEntity.invalidEntity')]);
        }
    }

    /**
     * @return $this|View
     */
    public function createManager()
    {
        try {

            $entityKey = ONE::getEntityKey();
            $length = 10;
            $password = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
            $rolesData = Orchestrator::getRolesList($entityKey);

            $roles = [];
            foreach ($rolesData as $rd) {
                $roles[$rd->role_key] = $rd->name;
            }


            $title = trans('privateEntitiesDivided.create_manager');
            return view('private.entities.managers.createManager', compact('title', 'entityKey', 'password', 'roles'));
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["entities.createManager" => $e->getMessage()]);
        }
    }

    /**
     * @return View
     */
    public function addManager()
    {
        $title = trans('privateEntitiesDivided.add_manager');
        $entityKey = ONE::getEntityKey();
        return view('private.entities.managers.entityUsers', compact('title', 'entityKey'));
    }

    /**
     * @return $this
     */
    public function tableUsersEntity()
    {
        try {

            $entityKey = ONE::getEntityKey();

            $response = Orchestrator::getAllManagers();

            $usersKey = [];

            foreach ($response as $item) {
                $usersKey[] = $item->user_key;
            }

            $manage = Auth::listUser($usersKey);

            $collection = Collection::make($manage);


            // in case of json
            return Datatables::of($collection)
                ->addColumn('action', function ($user) use ($entityKey) {

                    return ONE::actionButtons([$user->user_key], ['form' => 'entitiesDivided', 'edit' => 'EntitiesDividedController@editManager', 'delete' => 'EntitiesDividedController@deleteUserConfirm']);
                })
                ->make(true);
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["entity.tableUsersEntity" => $e->getMessage()]);
        }
    }

    /**
     * @param $userKey
     * @return $this|View
     */
    public function editManager($userKey)
    {
        try {

            $title = trans('privateEntitiesDivided.update_manager');

            $entityKey = ONE::getEntityKey();

            // User
            $user = Auth::getUserByKey($userKey);

            $rolesData = Orchestrator::getRolesList($entityKey);

            $roles = [];
            foreach ($rolesData as $rd) {
                $roles[$rd->role_key] = $rd->name;
            }

            $rolesUser = Orchestrator::getUserRoles($user->user_key);

            $userRoleKey = '';
            if (count($rolesUser) == 1) {
                $userRoleKey = $rolesUser[0]->role_key;
            }


            return view('private.entities.managers.createManager', compact('title', 'entityKey', 'user', 'roles', 'userRoleKey'));
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["private.entities.entityUsers" => $e->getMessage()]);
        }
    }

    /**
     * @param UserRequest $requestUser
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function storeManager(UserRequest $requestUser)
    {
        try {

            $response = Auth::storeUserV2($requestUser->all());

            $user = $response->json()->user;

            //* Send to Orchestrator the user type and key*/
            Orchestrator::setUser($user->user_key, 'manager');

            Session::flash('message', trans('entities.storeManagerOk'));
            return redirect()->action('EntitiesDividedController@showManagers');
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["entities.storeManager" => $e->getMessage()]);
        }
    }

    /**
     * @param UserRequest $requestUser
     * @param $userKey
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function updateManager(UserRequest $requestUser, $userKey)
    {
        try {


            Auth::updateManager($requestUser, $userKey);

            //Orchestrator::setUserRoles($userKey, $requestUser->roles);

            return redirect()->action('EntitiesDividedController@showManagers');
        } catch (Exception $e) {
            //TODO: save inputs
            return redirect()->back()->withErrors(["user.update" => $e->getMessage()]);
        }
    }

    /**
     * @param $userId
     * @return View
     */
    public function deleteUserConfirm($userId)
    {

        $data = array();

        $data['action'] = action("EntitiesDividedController@deleteUser", [$userId]);
        $data['title'] = "DELETE";
        $data['msg'] = "Are you sure you want to delete this Manager for this Entity?";
        $data['btn_ok'] = "Delete";
        $data['btn_ko'] = "Cancel";

        return view("_layouts.deleteModal", $data);
    }

    /**
     * @param $userKey
     * @return $this|string
     */
    public function deleteUser($userKey)
    {


        try {
            $entityKey = ONE::getEntityKey();
            Orchestrator::updateUserRole($userKey, $entityKey, 'user');
            Session::flash('message', trans('entities.updateRole_ok'));
            return action('EntitiesDividedController@showManagers');
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["entities.show" => $e->getMessage()]);
        }
    }

    /**
     * @return $this|View
     */
    public function showAuthMethods()
    {
        if(!ONE::verifyUserPermissions('orchestrator', 'entity_auth_method', 'show')){
            return redirect()->back()->withErrors(["private" => trans('privateEntitiesDivided.permission_message')]);
        }

        if (ONE::isEntity()) {
            $title = trans('privateEntitiesDivided.show_authMethods');
            $entity = Orchestrator::getEntity(ONE::getEntityKey());

            $sidebar = 'manageEntityRegistrationValues';
            $active = 'auth_methods';

            Session::put('sidebarArguments', ['activeFirstMenu' => 'authMethods', 'activeSecondMenu' => 'auth_methods']);

            return view('private.entities.authMethods.index', compact('title', 'entity', 'sidebar', 'active'));
        } else {
            return redirect()->back()->withErrors(["entity.show" => trans('privateEntity.invalidEntity')]);
        }
    }

    /**
     * @return $this
     */
    public function tableAuthMethod()
    {
        try {

            if (ONE::verifyUserPermissions('orchestrator', 'entity_auth_method', 'show')){
                $entityKey = ONE::getEntityKey();
                $authMethodsList = Orchestrator::getEntityAuthMethods($entityKey);

                // in case of json
                $authMethods = Collection::make($authMethodsList);
            }else
                $authMethods = Collection::make([]);

            $delete = ONE::verifyUserPermissions('orchestrator', 'entity_auth_method', 'delete');


            return Datatables::of($authMethods)
                ->addColumn('action', function ($authMethods) use($delete){
                    if($delete)
                        return ONE::actionButtons([$authMethods->auth_method_key], ['delete' => 'EntitiesDividedController@deleteAuthMethodConfirm']);
                    else
                        return null;
                })
                ->make(true);
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["entities.tableAuthMethodEntity" => $e->getMessage()]);
        }
    }

    /**
     * @return View
     */
    public function addAuthMethod()
    {
        $entityKey = ONE::getEntityKey();
        $title = trans('privateEntitiesDivided.add_authMethods');

        $sidebar = 'manageEntityRegistrationValues';
        $active = 'auth_methods';

        Session::put('sidebarArguments', ['activeFirstMenu' => 'authMethods', 'activeSecondMenu' => 'auth_methods']);

        return view('private.entities.authMethods.addAuthMethod', compact('entityKey', 'sidebar', 'active'));
    }

    /**
     * @return $this
     */
    public function tableAddAuthMethod()
    {
        try {
            $entityKey = ONE::getEntityKey();
            $authMethodsList = Orchestrator::getAvailableEntityAuthMethods($entityKey);

            // in case of json
            $authMethods = Collection::make($authMethodsList);

            return Datatables::of($authMethods)
                ->editColumn('name', function ($authMethods) {
                    return $authMethods->name;
                })
                ->addColumn('action', function ($authMethods) {
                    return ONE::actionButtons([$authMethods->auth_method_key], ['add' => 'EntitiesDividedController@addAuthMethodAction']);
                })
                ->make(true);
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["entities.tableAuthMethodEntity" => $e->getMessage()]);
        }
    }

    /**
     * @param $authMethodKey
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function addAuthMethodAction($authMethodKey)
    {
        try {
            $entityKey = ONE::getEntityKey();
            Orchestrator::setEntityAuthMethod($entityKey, $authMethodKey);
            Session::flash('message', trans('authMethod.addOk'));
            return redirect()->action('EntitiesDividedController@showAuthMethods');
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["authMethod.add" => $e->getMessage()]);
        }
    }

    /**
     * @param $authMethodKey
     * @return View
     */
    public function deleteAuthMethodConfirm($authMethodKey)
    {
        $data = array();

        $data['action'] = action("EntitiesDividedController@deleteAuthMethod", ['authMethodKey' => $authMethodKey]);
        $data['title'] = "DELETE";
        $data['msg'] = "Are you sure you want to delete this Authentication Method for this Entity?";
        $data['btn_ok'] = "Delete";
        $data['btn_ko'] = "Cancel";

        return view("_layouts.deleteModal", $data);
    }

    /**
     * @param $authMethodKey
     * @return $this|string
     */
    public function deleteAuthMethod($authMethodKey)
    {
        try {

            $entityKey = ONE::getEntityKey();
            Orchestrator::deleteEntityAuthMethod($entityKey, $authMethodKey);
            Session::flash('message', trans('authMethod.delete_ok'));
            return action('EntitiesDividedController@showAuthMethods', $entityKey);
        } catch (Exception $e) {
            return redirect()->back()->withErrors([trans('privateEntities.show') => $e->getMessage()]);
        }
    }



    /*public function showModules(){
        try{
            $entityKey = ONE::getEntityKey();
            $entity = Orchestrator::getEntity($entityKey);

            return view('private.entities.modules.index', compact('entity'));
        }catch(Exception $e) {
            return redirect()->back()->withErrors(["entities.showModules" => $e->getMessage()]);
        }
    }

    public function tableEntityModule()
    {
        try {
            $entityKey = ONE::getEntityKey();
            $entityModulesList = Orchestrator::getActiveEntityModules($entityKey);

            // in case of json
            $modules = Collection::make($entityModulesList);
            return Datatables::of($modules)
                ->editColumn('name', function ($modules) {
                    return $modules->name;
                })
                ->make(true);

        } catch (Exception $e) {
            return redirect()->back()->withErrors(["entities.tableEntityModule" => $e->getMessage()]);
        }
    }

    public function addEntityModule()
    {

        try {
            $entityKey = ONE::getEntityKey();
            $modules = Orchestrator::getModulesList();
            $entityModulesList = Orchestrator::getActiveEntityModules($entityKey);
            $modulesId = [];

            foreach ($entityModulesList as $entityModule){
                $modulesId[] = $entityModule->module->id;
            }

            $data = [];

            $data['entityKey'] = $entityKey;
            $data['modules'] = $modules;
            $data['modulesId'] = $modulesId;
            $data['activeModules'] = $entityModulesList;
            return view('private.entities.modules.module', $data);

        } catch (Exception $e) {
            return redirect()->back()->withErrors(["entities.addEntityModule" => $e->getMessage()]);
        }
    }


    public function updateEntityModules(Request $request)
    {
        $modules = array_keys($request->all());
        unset($modules[0]);

        try {
            $entityKey = ONE::getEntityKey();
            Orchestrator::updateEntityModules($entityKey, $modules);

            return redirect()->action('EntitiesDividedController@show', $entityKey);

        } catch (Exception $e) {
            return redirect()->back()->withErrors(["entities.addEntityModule" => $e->getMessage()]);
        }
    }*/





    /** -----------------------------------------------------------
     *  {BEGIN} Methods to deal with the entity registration values
     * ------------------------------------------------------------
     */

    /**
     * returns the view to show the list
     * of registered values for the entity
     * @param $type
     * @return \Illuminate\Http\RedirectResponse
     */
    public function showEntityRegistrationValues($type)
    {
        if(!ONE::verifyUserPermissions('orchestrator', 'entity', 'show')){
            return redirect()->back()->withErrors(["private" => trans('privateEntitiesDivided.permission_message')]);
        }
        if(One::isEntity()){
            try {
                $entityKey = ONE::getEntityKey();
                $entity = Orchestrator::getEntity($entityKey);
                $title = trans("privateEntitiesDivided.manage_$type") . ' ' . (isset($entity->name) ? $entity->name : null);
                $uploadKey = Files::getUploadKey();

                $sidebar = 'manageEntityRegistrationValues';
                $active = $type;

                Session::put('sidebarArguments', ['activeFirstMenu' => 'authMethods', 'activeSecondMenu' => $type]);

                return view('private.entities.authMethods.manageRegistrationValues', compact('title', 'entity','uploadKey','type', 'sidebar', 'active'));

            } catch (Exception $e) {
                return redirect()->back()->withErrors(["entities.show" => $e->getMessage()]);
            }
        }
    }

    /**
     * returns the collection of the entity
     * registration values according to type
     * @param $entityKey
     * @param $type
     * @return \Illuminate\Http\RedirectResponse
     * @internal param Request $request
     */
    public function getEntityRegistrationValues(Request $request, $entityKey,$type)
    {

        if(!ONE::verifyUserPermissions('orchestrator', 'entity', 'show')){
            return redirect()->back()->withErrors(["private" => trans('privateEntitiesDivided.permission_message')]);
        }

        if(One::isEntity()){
            try {
                $registrationValues = Orchestrator::getEntityRegistrationValues($request, $entityKey,$type);
                $collection = Collection::make($registrationValues->registrationValues);
                $recordsTotal = $registrationValues->recordsTotal;

                return Datatables::of($collection)
                    ->addColumn('action', function ($collection) use ($type,$entityKey){
                        return ONE::actionButtons(['id' => $collection->id, 'type' => $type,'entityKey' => $entityKey], ['delete' => 'EntitiesDividedController@deleteRegistrationValueConfirm']); /* deleteVatNumberConfirm*/
                    })
                    ->skipPaging()
                    ->setTotalRecords($recordsTotal)
                    ->make(true);


                return view('private.entities.authMethods.vatNumberList', compact('title', 'entity'));

            } catch (Exception $e) {
                return redirect()->back()->withErrors(["entities.show" => $e->getMessage()]);
            }
        }
    }


    /**
     * show modal to confirm the
     * delete of a entity registration value
     * @param $valueId
     * @param $type
     * @param $entityKey
     * @return View
     */
    public function deleteRegistrationValueConfirm($valueId,$type,$entityKey)
    {
        $data = array();
        $data['action'] = action("EntitiesDividedController@destroyRegistrationValue", ['id' => $valueId, 'type' => $type, 'entityKey' => $entityKey]);
        $data['title'] = "DELETE";
        $data['msg'] = "Are you sure you want to delete this entity $type?";
        $data['btn_ok'] = "Delete";
        $data['btn_ko'] = "Cancel";
        return view("_layouts.deleteModal", $data);
    }


    /**
     * destroy a entity registration
     * value according to given type
     * @param $valueId
     * @param $type
     * @param $entityKey
     * @return $this|string
     */
    public function destroyRegistrationValue($valueId,$type,$entityKey)
    {
        try {
            Orchestrator::deleteEntityRegistrationValue($valueId,$type,$entityKey); /** deleteVatNumber */
            Session::flash('message', trans('entity.registration_value_delete_ok'));
            return action('EntitiesDividedController@showEntityRegistrationValues',$type);
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["entities.deleteEntityRegistrationValue" => $e->getMessage()])->getTargetUrl();
        }

    }


    /**
     * uploads a file that contains a list of
     * registration values according to type
     * @param Request $request
     * @param $entityKey
     * @return string
     */
    public function uploadEntityRegistrationValues(Request $request, $entityKey) {
        return Plupload::receive('file', function ($file) use ($request,$entityKey){
            $importedData = [];
            $keys = [];
            $keysOrig = [];
            if (($handle = fopen($file->getPathName(), "r")) !== false) {

                /* Get Header of CSV File */
                $data = fgetcsv($handle, 1000, ";");
                if ($data != false) {
                    $num = count($data);
                    for ($c = 0; $c < $num; $c++) {
                        $keys[$c] = mb_strtolower($data[$c]);
                        $keysOrig[$c] = $data[$c];
                    }
                }

                /* Get CSV content */
                while (($data = fgetcsv($handle, 1000, ";")) !== false) {
                    $newEntry = array();
                    $num = count($data);
                    for ($c = 0; $c < $num; $c++) {
                        switch ($keys[$c]) {
                            case "vat_number":
                                $newEntry["vat_number"] = $data[$c];
                                break;

                            case "gender":
                                $newEntry["gender"] = $data[$c];
                                break;

                            case "surname":
                                $newEntry["surname"] = $data[$c];
                                break;

                            case "name":
                                $newEntry["name"] = $data[$c];

                                break;

                            case "birthdate":
                                $newEntry["birthdate"] = $data[$c];

                                break;

                            case "birthplace":
                                $newEntry["birthplace"] = $data[$c];

                                break;

                            case "residential_address":
                                $newEntry["residential_address"] = $data[$c];

                                break;

                            default:
                                /*$newEntry["parameters"][$keysOrig[$c]] = $data[$c];*/
                                break;
                        }

                    }

                    $importedData[] = $newEntry;

                }
            }
            File::delete($file->getPathName());

            try{
                $response = Orchestrator::importRegistrationFields($importedData,$request['type'],$entityKey);
                return $response;
            } catch (Exception $e) {
                return "KO";
            }
        });
        return "KO";
    }


    /**
     * add a single registration values according to type
     * @param Request $request
     * @param $entityKey
     * @return string
     */
    public function addSingleRegistrationValue(Request $request, $entityKey) {
        try {
            if($request['type'] == 'vat_numbers') {
                $importedData[]['vat_number'] = $request['vat_number'];
            }else{
                $importedData[] = array(
                    'name' => $request['domain_name'],
                    'title' => $request['domain_title']
                );
            }
            Orchestrator::importRegistrationFields($importedData, $request['type'], $entityKey);
            return 'OK';
        } catch (Exception $e) {
            return "KO";
        }
        return "KO";
    }



    /** -----------------------------------------------------------
     *  {END} Methods to deal with the entity registration values
     * ------------------------------------------------------------
     */

    public function manageDashBoardElements()
    {

        try {
            if (ONE::isEntity()) {
                $entity = CB::getEntityDashBoardElements();
                $availableDashBoardElements = $entity->availableDashBoardElements;

                return view('private.entities.manageDashboardElements', compact('entity', 'availableDashBoardElements'));
            }
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["entities.manageDashBoardElements" => $e->getMessage()]);
        }



    }

    public function updateEntityDashBoardElements($dashBoardElementId)
    {

        try {
            if (ONE::isEntity()) {
                CB::updateEntityDashBoardElements($dashBoardElementId);
                \Cache::forget('entityDashboardElements_'.ONE::getEntityKey());
                return response()->json(['success' => trans('privateEntitiesDivided.successOnupdateEntityDashBoardElements')]);
            }
        } catch (Exception $e) {
            return response()->json(['error' => trans('privateEntitiesDivided.failedToupdateEntityDashBoardElements')]);
        }
    }



















}
