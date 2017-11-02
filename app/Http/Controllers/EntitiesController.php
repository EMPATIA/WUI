<?php

namespace App\Http\Controllers;

use App\ComModules\Auth;
use App\ComModules\EMPATIA;
use App\ComModules\Notify;
use App\ComModules\Orchestrator;
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
use Session;
use View;
use Breadcrumbs;
use App\ComModules;

class EntitiesController extends Controller
{
    public function __construct()
    {
        View::share('private.entities', trans('entity.entity'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $title = trans('privateEntities.list_entities');
        return view('private.entities.index', compact('title'));
    }

    /**
     * @param $entityKey
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function showEntity($entityKey)
    {
        if (ONE::isAdmin()) {
            $entity = Orchestrator::getEntity($entityKey);
            return redirect()->action('EntitiesController@show', $entity);
        } else {
            return redirect()->back()->withErrors(["entity.show" => trans('privateEntities.invalid_entity')]);
        }
    }

    /**
     * @param $entityKey
     * @return $this|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showLayouts($entityKey)
    {
        if (ONE::isAdmin()) {

            $entity = Orchestrator::getEntity($entityKey);

            $sidebar = 'entities';
            $active = 'layouts';

            Session::put('sidebarArguments', ['entityKey' => $entityKey, 'activeFirstMenu' => 'details']);
            return view('private.entities.layouts', compact('entity', 'entityKey', 'sidebar', 'active'));
        } else {

            return redirect()->back()->withErrors(["entity.show" => trans('privateEntities.invalid_entity')]);
        }
    }


    /**
     * @param $entityKey
     * @return $this|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showSites($entityKey)
    {
        if (ONE::isAdmin()) {
            $entity = Orchestrator::getEntity($entityKey);

            $sidebar = 'entities';
            $active = 'sites';

            Session::put('sidebarArguments', ['entityKey' => $entityKey, 'activeFirstMenu' => 'sites']);

            return view('private.entities.sites', compact('entity', 'entityKey', 'sidebar', 'active'));
        } else {
            return redirect()->back()->withErrors(["entity.show" => trans('privateEntities.invalid_entity')]);
        }
    }

    /**
     * @param $entityKey
     * @param $siteKey
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showUseTerms($entityKey, $siteKey)
    {
        $languages = Orchestrator::getLanguageList();
        $site = Orchestrator::getSite($siteKey);
        $title = trans('privateEntities.show_use_terms') . ' ' . (isset($site) ? $site->name : null);

        $sidebar = 'sites';
        $active = 'useTerms';

        Session::put('sidebarArguments.siteKey', $siteKey);
        Session::put('sidebarArguments.activeSecondMenu', 'useTerms');

        return view('private.entities.useTerms', compact('entityKey', 'site', 'siteKey', 'languages', 'title', 'sidebar', 'active'));
    }

    /**
     * @param $entityKey
     * @param $siteKey
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showHomePageConfigurations($entityKey, $siteKey)
    {
        $site = Orchestrator::getSite($siteKey);
        $homePageTypesList = Orchestrator::getHomePageTypeParents();
        $homePageTypes = [];
        foreach ($homePageTypesList as $homePageType) {
            $homePageTypes[$homePageType->home_page_type_key] = $homePageType->name;
        }

        $sidebar = 'sites';
        $active = 'homePageConfigurations';

        Session::put('sidebarArguments.siteKey', $siteKey);
        Session::put('sidebarArguments.activeSecondMenu', 'homePageConfigurations');

        return view('private.entities.homePageConfigurations', compact('homePageTypesList', 'homePageTypes', 'site', 'siteKey', 'entityKey', 'sidebar', 'active'));
    }


    /**
     * @param $entityKey
     * @param $siteKey
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showSiteLevels($entityKey, $siteKey)
    {
        $site = Orchestrator::getSite($siteKey);
        $homePageTypesList = Orchestrator::getHomePageTypeParents();
        $homePageTypes = [];
        foreach ($homePageTypesList as $homePageType) {
            $homePageTypes[$homePageType->home_page_type_key] = $homePageType->name;
        }
        return view('private.entities.homePageConfigurations', compact('homePageTypesList', 'homePageTypes', 'site', 'entityKey'));
    }

    /**
     * @param $entityKey
     * @param $siteKey
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showConfigurations($entityKey, $siteKey)
    {
        $site = Orchestrator::getSite($siteKey);
        $confs = Orchestrator::getSiteSiteConfigs();

        $title = trans('privateEntities.show_site_configurations') . ' ' . (isset($site) ? $site->name : null);

        $sidebar = 'sites';
        $active = 'configurations';

        Session::put('sidebarArguments.siteKey', $siteKey);
        Session::put('sidebarArguments.activeSecondMenu', 'configurations');

        return view('private.entities.configurations', compact('confs', 'entityKey', 'site', 'siteKey', 'title', 'sidebar', 'active'));
    }

    /**
     * @param $entityKey
     * @return $this|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showManagers($entityKey)
    {
        if (ONE::isAdmin()) {
            $entity = Orchestrator::getEntity($entityKey);

            $sidebar = 'entities';
            $active = 'managers';

            Session::put('sidebarArguments', ['entityKey' => $entityKey, 'activeFirstMenu' => 'managers']);

            return view('private.entities.managers', compact('entity', 'entityKey', 'sidebar', 'active'));
        } else {
            return redirect()->back()->withErrors(["entity.show" => trans('privateEntities.invalid_entity')]);
        }
    }

    /**
     * @param $entityKey
     * @return $this|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showModules($entityKey) {
        if (ONE::isAdmin()) {
            $entity = Orchestrator::getEntity($entityKey);

            $sidebar = 'entities';
            $active = 'modules';

            Session::put('sidebarArguments', ['activeFirstMenu' => 'modules']);

            return view('private.entities.modules', compact(  'entity', 'entityKey','sidebar', 'active'));
        } else {
            return redirect()->back()->withErrors(["entity.show" => trans('privateEntities.invalid_entity')]);
        }
    }

    /**
     * @param $entityKey
     * @return $this|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showAuthMethods($entityKey)
    {
        if (ONE::isAdmin()) {
            $entity = Orchestrator::getEntity($entityKey);

            $sidebar = 'entities';
            $active = 'auth';

            Session::put('sidebarArguments', ['entityKey' => $entityKey, 'activeFirstMenu' => 'auth']);

            return view('private.entities.authMethods', compact('entity', 'entityKey', 'sidebar', 'active'));
        } else {
            return redirect()->back()->withErrors(["entity.show" => trans('privateEntities.invalid_entity')]);
        }
    }

    /**
     * @param $entityKey
     * @return $this|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showLanguages($entityKey)
    {
        if (ONE::isAdmin()) {
            $entity = Orchestrator::getEntity($entityKey);

            $sidebar = 'entities';
            $active = 'languages';

            Session::put('sidebarArguments', ['entityKey' => $entityKey, 'activeFirstMenu' => 'languages']);

            return view('private.entities.languages', compact('entity', 'entityKey', 'sidebar', 'active'));
        } else {
            return redirect()->back()->withErrors(["entity.show" => trans('privateEntities.invalid_entity')]);
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @param $entityKey
     * @return Response
     * @internal param $entityId
     */
    public function addLanguage($entityKey)
    {
        $title = trans('privateLanguages.add_language');

        $sidebar = 'entities';
        $active = 'languages';

        Session::put('sidebarArguments', ['entityKey' => $entityKey, 'activeFirstMenu' => 'languages']);

        return view('private.entities.addLang', compact('title', 'entityKey', 'sidebar', 'active'));
    }


    /**
     * @param Request $request
     */
    public function setEntityKey(Request $request)
    {
        $entityKey = $request->entityKey;

        // Set X-ENTITY-KEY for header
        if (strlen($entityKey) == 0) {
            Session::pull('X-ENTITY-KEY');
        } else {
            Session::put('X-ENTITY-KEY', $entityKey);
            $entity = Orchestrator::getEntity($entityKey);

            if(!is_null($entity->timezone)){
                Session::put('TIMEZONE', $entity->timezone->name);
            }
        }

        // Language selection
        $languages = ONE::getAllLanguages();
        $languageSelected = false;
        foreach($languages as $language){
            // If language store in Session, exists in collection of Entity Languages use Language
            if( !empty(Session::get('LANG_CODE')) && !empty($language->code) && $language->code == Session::get('LANG_CODE') ){
                Session::put('LANG_CODE', $language->code);
                $languageSelected = true;
            }

            // Default language
            if(!empty($language->default) && $language->default == true){
                Session::put('LANG_CODE_DEFAULT', $language->code);
            }
        }

        // Language selected wasn't found Entity Languages list
        if(!$languageSelected) {
            Session::put('LANG_CODE', Session::get('LANG_CODE_DEFAULT') );
        }

        //Clear dashboard session data
        Session::remove("userDashboardElements");
    }

    /**
     * Display a listing of the resource.
     *
     * @param $entityKey
     * @param $languageId
     * @return Response
     */
    public function addLanguageAction($entityKey, $languageId)
    {
        try {

            Orchestrator::setLanguage($languageId, $entityKey, 0);

            return redirect()->action('EntitiesController@showLanguages', $entityKey);

        } catch (Exception $e) {
            return redirect()->back()->withErrors(["entity.store" => $e->getMessage()]);
        }
    }

    /**
     * Create a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $carbon = Carbon::now();
        $data = [];
        try {

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

            $data['language'] = $lang_name;
            $data['country'] = $country_name;
            $data['timezone'] = $timezone_name;
            $data['currency'] = $currency_name;
            $data['carbon'] = $carbon;
            $data['title'] = trans('privateEntities.create_entity');

            return view('private.entities.entity', $data);
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["private.entities.entity" => $e->getMessage()]);
        }

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param EntityRequest $request
     * @return Response
     */
    public function store(EntityRequest $request)
    {
        try {
            $entity = Orchestrator::setEntity($request);

            if($request->language_id != null) {
                Orchestrator::setLanguage($request->language_id, $entity->entity_key, 1);
            }

            Session::flash('message', trans('entity.store_ok'));
            return redirect()->action('EntitiesController@show', $entity->entity_key);

        } catch (Exception $e) {
            //TODO: save inputs
            return redirect()->back()->withErrors(["entity.store" => $e->getMessage()]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param $entityKey
     * @return Response
     * @internal param int $id
     */
    public function edit($entityKey)
    {
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
            $data['entityKey'] = $entityKey;
            $data['sidebar'] = 'entities';
            $data['active'] = 'details';

            Session::put('sidebarArguments', ['entityKey' => $entityKey, 'activeFirstMenu' => 'details']);

            return view('private.entities.entity', $data);
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["entities.edit" => $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param $entityKey
     * @return Response
     * @internal param int $id
     */
    public function show($entityKey)
    {
        try {
            $entity = Orchestrator::getEntity($entityKey);
            if(ONE::isAdmin()){
                $title = trans('privateEntities.show_entity') . ' ' . (isset($entity->name) ? $entity->name : null);

                $sidebar = 'entities';
                $active = 'details';

                Session::put('sidebarArguments', ['entityKey' => $entityKey, 'activeFirstMenu' => 'details']);

                return view('private.entities.entity', compact('title', 'entity', 'entityKey', 'sidebar', 'active'));
            }else{
                $title = trans('privateEntities.show_entity_manager') . ' ' . (isset($entity->name) ? $entity->name : null);
                return view('private.entities.entityManager', compact('title', 'entity'));
            }

        } catch (Exception $e) {
            return redirect()->back()->withErrors(["entities.show" => $e->getMessage()]);
        }
    }

    /**
     * Update the specified resource.
     *
     * @param EntityRequest $request
     * @param $entityKey
     * @return Response
     * @internal param int $id
     */
    public function update(EntityRequest $request, $entityKey)
    {
        try {
            Orchestrator::updateEntity($request, $entityKey);
            Session::flash('message', trans('privateEntities.update_ok'));
            return redirect()->action('EntitiesController@show', $entityKey);
        } catch (Exception $e) {
            //TODO: save inputs
            return redirect()->back()->withErrors(["entity.update" => $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $entityKey
     * @return Response
     * @internal param $id
     */
    public function destroy($entityKey)
    {
        try {
            Orchestrator::deleteEntity($entityKey);

            Session::flash('message', trans('privateEntities.delete_ok'));
            return action('EntitiesController@index');
        } catch (Exception $e) {
            //TODO: save inputs
            return redirect()->back()->withErrors(["entity.destroy" => $e->getMessage()]);
        }
    }

    /**
     * @return $this
     */
    public function tableEntities()
    {
        try {
            $entities = Orchestrator::getEntities();

            $usersKey = [];
            foreach ($entities as $item) {
                $usersKey[] = $item->created_by;
            }

            $responseAuth = Auth::listUser($usersKey);
            $userNames = [];

            foreach ($responseAuth as $item) {
                $userNames[$item->user_key] = $item->name;
            }

            // in case of json
            $entity = Collection::make($entities);

            return Datatables::of($entity)
                ->editColumn('name', function ($entity) {
                    return "<a href='" . action('EntitiesController@show', $entity->entity_key) . "'>" . $entity->name . "</a>";
                })
                ->editColumn('created_by', function ($entity) use ($userNames) {
                    return !(empty($userNames[$entity->created_by])) ? $userNames[$entity->created_by] : "";
                })
                ->addColumn('action', function ($entity) {
                    return ONE::actionButtons($entity->entity_key, ['edit' => 'EntitiesController@edit', 'delete' => 'EntitiesController@delete', 'form' => 'entities']);
                })
                ->make(true);
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["entity.tableEntities" => $e->getMessage()]);
        }
    }

    /**
     * List all resources from storage.
     *
     * @param $entityKey
     * @return mixed
     */
    public function tableUsersEntity($entityKey)
    {
        try {
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
                    return ONE::actionButtons([$entityKey, $user->user_key], ['form' => 'entities', 'edit' => 'EntitiesController@editManager', 'delete' => 'EntitiesController@deleteUserConfirm']);
                })
                ->make(true);
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["entity.tableUsersEntity" => $e->getMessage()]);
        }
    }

    /**
     * @param $entityKey
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function delete($entityKey)
    {
        $data = array();

        $data['action'] = action("EntitiesController@destroy", $entityKey);
        $data['title'] = "DELETE";
        $data['msg'] = "Are you sure you want to delete this Entity?";
        $data['btn_ok'] = "DELETE";
        $data['btn_ko'] = "CANCEL";

        return view("_layouts.deleteModal", $data);
    }

    /**
     * @param $entityKey
     * @return $this
     */
    public function tableLanguagesEntity($entityKey)
    {
        try {
            $entity = Orchestrator:: getEntity($entityKey);

            // in case of json
            $language = Collection::make($entity->languages);

            return Datatables::of($language)
                ->editColumn('name', function ($language) {
                    return "<a href='" . action('LanguagesController@show', $language->id) . "'>" . $language->name . "</a>";
                })
                ->addColumn('activateAction', function ($language) use ($entityKey) {
                    return ($language->pivot->default == 0) ? ONE::actionButtons([$entityKey, $language->id], ['activate' => 'EntitiesController@makeLangDefaultConfirm']) : '<span class="badge badge-success">' . trans("entities.default") . '</span>';
                })
                ->addColumn('action', function ($language) use ($entityKey) {
                    return ONE::actionButtons([$entityKey, $language->id], ['delete' => 'EntitiesController@deleteLangConfirm']);
                })
                ->make(true);
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["entities.tableLanguagesEntity" => $e->getMessage()]);
        }
    }

    /**
     * Opens modal to confirm will to activate the specified resource.
     *
     * @param  int $id
     * @param $languageId
     * @return View
     */
    public function makeLangDefaultConfirm($id, $languageId)
    {
        $data = array();

        $data['action'] = action("EntitiesController@makeLangDefault", [$id, $languageId]);
        $data['title'] = "MAKE DEFAULT";
        $data['msg'] = "Are you sure you want to make this Language Default for this entity?";
        $data['btn_ok'] = "Make default";
        $data['btn_ko'] = "Cancel";

        return view("_layouts.activateModal", $data);
    }

    /**
     * @param $entityKey
     * @param $languageId
     * @return string
     * @throws Exception
     */
    public function makeLangDefault($entityKey, $languageId)
    {

        Orchestrator::updateDefaultLanguage($entityKey, $languageId, 1);

        return action('EntitiesController@show', $entityKey);
    }

    /**
     * Opens modal to confirm will to activate the specified resource.
     *
     * @param $entityKey
     * @param $languageId
     * @return View
     */
    public function deleteLangConfirm($entityKey, $languageId)
    {
        $data = array();

        $data['action'] = action("EntitiesController@deleteLang", [$entityKey, $languageId]);
        $data['title'] = "DELETE";
        $data['msg'] = "Are you sure you want to delete this Language for this Entity?";
        $data['btn_ok'] = "Delete";
        $data['btn_ko'] = "Cancel";

        return view("_layouts.deleteModal", $data);
    }

    /**
     * Opens modal to confirm will to activate the specified resource.
     *
     * @param $entityKey
     * @param $userId
     * @return View
     * @internal param int $id
     */
    public function deleteUserConfirm($entityKey, $userId)
    {
        $data = array();

        $data['action'] = action("EntitiesController@deleteUser", [$entityKey, $userId]);
        $data['title'] = "DELETE";
        $data['msg'] = "Are you sure you want to delete this Manager for this Entity?";
        $data['btn_ok'] = "Delete";
        $data['btn_ko'] = "Cancel";

        return view("_layouts.deleteModal", $data);
    }

    /**
     * @param $entityKey
     * @param $languageId
     * @return EntitiesController|\Illuminate\Http\RedirectResponse
     */
    public function deleteLang($entityKey, $languageId)
    {
        try {
            Orchestrator::deleteLanguage($entityKey, $languageId);

            Session::flash('message', trans('privateEntities.delete_language_ok'));
            return action('EntitiesController@showLanguages', $entityKey);
        }
        catch(Exception $e) {
            return redirect()->action('EntitiesController@show', $entityKey)->withErrors([ trans('privateEntities.delete_ko') => $e->getMessage()]);
        }
    }

    /**
     * @param $entityKey
     * @param $userKey
     * @return $this|string
     */
    public function deleteUser($entityKey, $userKey)
    {
        try {

            Orchestrator::updateUser($entityKey, $userKey, 'user');

            Session::flash('message', trans('privateEntities.update_role_ok'));
            return action('EntitiesController@showManagers', $entityKey);

        } catch (Exception $e) {
            return redirect()->back()->withErrors(["entities.show" => $e->getMessage()]);
        }
    }

    /**
     * @param $entityKey
     * @return $this
     */
    public function tableAddLanguageEntity($entityKey)
    {
        try {
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
                    return ONE::actionButtons([$entityKey, $language->id], ['add' => 'EntitiesController@addLanguageAction']);
                })
                ->make(true);
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["entities.tableAddLanguageEntity" => $e->getMessage()]);
        }
    }

    /**
     * Create a new resource.
     *
     * @param $entityKey
     * @return View
     */
    public function createManager($entityKey)
    {
        try {
            $length = 10;
            $password = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
            $rolesData = Orchestrator::getRolesList($entityKey);
            $roles = [];
            foreach ($rolesData as $rd) {
                $roles[$rd->role_key] = $rd->name;
            }

            $title = trans('privateEntities.create_manager');

            $sidebar = 'entities';
            $active = 'managers';

            Session::put('sidebarArguments', ['entityKey' => $entityKey, 'activeFirstMenu' => 'managers']);

            return view('private.entities.createManager', compact('title', 'entityKey', 'password', 'roles', 'sidebar', 'active'));
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["entities.createManager" => $e->getMessage()]);
        }
    }

    /**
     * Create a new resource.
     *
     * @param UserRequest $requestUser
     * @param $entityKey
     * @return View
     */
    public function storeManager(UserRequest $requestUser, $entityKey)
    {
        try {
            $response = Auth::storeUserV2($requestUser->all());

            $user = $response->user;

            //* Send to Orchestrator the user type and key*/
            Orchestrator::setEntityUser($user->user_key, $entityKey, 'manager');
            Session::flash('message', trans('privateEntities.store_manager_ok'));
            return redirect()->action('EntitiesController@show', $entityKey);
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["entities.storeManager" => $e->getMessage()]);
        }
    }

    /**
     * Add a User as Entity Manager.
     *
     * @param $entityKey
     * @return View
     * @internal param $entityId
     */
    public function addManager($entityKey)
    {
        $title = trans('privateEntities.add_manager');

        $sidebar = 'entities';
        $active = 'managers';

        Session::put('sidebarArguments', ['entityKey' => $entityKey, 'activeFirstMenu' => 'managers']);

        return view('private.entities.entityUsers', compact('title', 'entityKey', 'sidebar', 'active'));
    }

    /**
     * Add a User as Entity Manager.
     *
     * @param $entityKey
     * @param $userKey
     * @return View
     * @internal param $entityKey
     */
    public function showManager($entityKey, $userKey)
    {
        try {
            // User
            $user = Auth::getUserByKey($userKey);
            $rolesUser = Orchestrator::getUserRoles($user->user_key);

            $userRoleName = '';
            if (count($rolesUser) == 1) {
                $userRoleName = $rolesUser[0]->name;
            }

            $sidebar = 'entities';
            $active = 'managers';

            Session::put('sidebarArguments', ['entityKey' => $entityKey, 'activeFirstMenu' => 'managers']);

            return view('private.entities.createManager', compact('entityKey', 'user', 'userRoleName', 'active', 'sidebar'));
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["private.entities.entityUsers" => $e->getMessage()]);
        }
    }

    /**
     * Add a User as Entity Manager.
     *
     * @param $entityKey
     * @param $userKey
     * @return View
     * @internal param $entityId
     */
    public function editManager($entityKey, $userKey)
    {
        try {

            // Use
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

            $sidebar = 'entities';
            $active = 'managers';

            Session::put('sidebarArguments', ['entityKey' => $entityKey, 'activeFirstMenu' => 'managers']);

            return view('private.entities.createManager', compact('entityKey', 'user', 'roles', 'userRoleKey', 'sidebar', 'active'));
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["private.entities.entityUsers" => $e->getMessage()]);
        }
    }


    /**
     * @param UserRequest $requestUser
     * @param $entityKey
     * @param $userKey
     * @return $this|\Illuminate\Http\RedirectResponse
     * @internal param $entityKey
     */
    public function updateManager(UserRequest $requestUser, $entityKey, $userKey)
    {
        try {
            Auth::updateManager($requestUser, $userKey);
            Orchestrator::setUserRoles($userKey, $requestUser->roles);

            return redirect()->action('EntitiesController@showManager', [$entityKey, $userKey]);
        } catch (Exception $e) {
            //TODO: save inputs
            return redirect()->back()->withErrors(["user.update" => $e->getMessage()]);
        }
    }

    /**
     * @param $entityKey
     * @return $this
     * @internal param $entityKey
     */
    public function tableSitesEntity($entityKey)
    {
        try {
            $entity = Orchestrator::getEntity($entityKey);
            $sites = $entity->sites;
            // in case of json
            $collection = Collection::make($sites);
            return Datatables::of($collection)
                ->editColumn('name', function ($collection) use ($entityKey) {
                    return "<a href='" . action('EntitiesController@showEntitySite', ['entityKey' => $entityKey, 'siteKey' => $collection->key]) . "'>" . $collection->name . "</a>";
                })
                ->addColumn('action', function ($collection) use ($entityKey) {
                    return ONE::actionButtons(['entityKey' => $entityKey, 'siteKey' => $collection->key], ['edit' => 'EntitiesController@editEntitySite', 'delete' => 'EntitiesController@deleteSiteConfirm']);
                })
                ->make(true);
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["entities.show" => $e->getMessage()]);
        }
    }

    /**
     * @param $entityKey
     * @return View
     * @internal param $entityKey
     */
    public function createEntitySite($entityKey)
    {
        $entity = Orchestrator::getEntity($entityKey);
        $languages = Orchestrator::getLanguages($entityKey);

        $layouts = [];
        foreach ($entity->layouts as $layout) {
            $layouts[$layout->layout_key] = $layout->name;
        }

        $confs = Orchestrator::getSiteSiteConfigs(null);

        $title = trans('privateSites.create_site');
        return view('private.entities.site', compact('title', 'entityKey', 'layouts', 'languages','confs'));
    }

    /**
     * @param $entityKey
     * @param EntitySiteRequest $request
     * @return $this|\Illuminate\Http\RedirectResponse
     * @internal param $entityId
     */
    public function storeEntitySite($entityKey, EntitySiteRequest $request)
    {
        try {
            $languages = Orchestrator::getLanguages($entityKey);

            $contentTranslation = [];
            foreach($languages as $language){
                $contentTranslation[] = [
                    'language_id'   =>  $language->id,
                    'language_code' =>  $language->code,
                    'content'       =>  $language->default == true ? $request->input("required_content_".$language->code) :$request->input("content_".$language->code)
                ];
            }
            $request['use_terms'] = $contentTranslation;

            $site = Orchestrator::setNewEntitySite($entityKey, $request->all());
            Session::flash('message', trans('privateSites.store_ok'));
            return redirect()->action('EntitiesController@showEntitySite', ['entityKey' => $entityKey, 'siteKey' => $site->key]);
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["entities.storeSite" => $e->getMessage()]);
        }
    }

    /**
     * @param EntitySiteRequest $request
     * @param $entityKey
     * @param $siteKey
     * @return $this|\Illuminate\Http\RedirectResponse
     * @internal param $entityId
     */
    public function updateEntitySite(EntitySiteRequest $request, $entityKey, $siteKey)
    {
        try {
            $languages = Orchestrator::getLanguages($entityKey);

            $contentTranslation = [];
            foreach($languages as $language){
                $contentTranslation[] = [
                    'language_id'   =>  $language->id,
                    'language_code' =>  $language->code,
                    'content'       =>  $language->default == true ? $request->input("required_content_".$language->code) :$request->input("content_".$language->code)
                ];
            }

            $configs = Orchestrator::getSiteSiteConfigs();
            $siteConfs = [];
            foreach($configs as $confGroup){
                foreach ($confGroup->confs as $conf) {
                    $siteConfs[] = [
                        'config_id' => $conf->id,
                        'config_value' => (isset($request->toArray()["configuration_" . $conf->id]) ? 1 : 0),
                    ];
                }
            }

            $request['use_terms'] = $contentTranslation;
            $request['siteConfs'] = $siteConfs;

            $site = Orchestrator::updateEntitySite($request, $siteKey);
            Session::flash('message', trans('privateSites.update_ok'));
            return redirect()->action('EntitiesController@showEntitySite', ['entityKey' => $entityKey, 'siteKey' => $site->key]);
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["entities.updateSite" => $e->getMessage()]);
        }
    }

    /**
     * @param $entityKey
     * @param $siteKey
     * @return $this|View
     * @internal param $entity_id
     */
    public function editEntitySite($entityKey, $siteKey)
    {
        try {
            $entity = Orchestrator::getEntity($entityKey);
            $layouts = [];
            foreach ($entity->layouts as $layout) {
                $layouts[$layout->layout_key] = $layout->name;
            }
            $site = Orchestrator::getSite($siteKey);
            $languages = Orchestrator::getLanguages($entityKey);

            $homePageTypesList = Orchestrator::getHomePageTypeParents();
            $homePageTypes = [];
            foreach ($homePageTypesList as $homePageType) {
                $homePageTypes[$homePageType->home_page_type_key] = $homePageType->name;
            }

            $confs = Orchestrator::getSiteSiteConfigs();

            $title = trans('privateSites.update_site') . ' ' . (isset($site->name) ? $site->name : null);

            $sidebar = 'sites';
            $active = 'details';

            Session::put('sidebarArguments', ['entityKey' => $entityKey, 'siteKey' => $siteKey, 'activeSecondMenu' => 'details']);

            return view('private.entities.site', compact('title', 'entityKey', 'site', 'siteKey', 'layouts', 'homePageTypes', 'languages','confs', 'sidebar', 'active'));
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["entities.updateSite" => $e->getMessage()]);
        }
    }

    /**
     * @param $entityKey
     * @param $siteKey
     * @return \Illuminate\Http\RedirectResponse|View
     * @internal param $entityKey
     */
    public function showEntitySite($entityKey, $siteKey)
    {
        try {
            $languages = Orchestrator::getLanguageList();
            $site = Orchestrator::getSite($siteKey);
            $homePageTypesList = Orchestrator::getHomePageTypeParents();
            $homePageTypes = [];
            foreach ($homePageTypesList as $homePageType) {
                $homePageTypes[$homePageType->home_page_type_key] = $homePageType->name;
            }
            $confs = Orchestrator::getSiteSiteConfigs();

            $title = trans('privateSites.show_site') . ' ' . (isset($site->name) ? $site->name : null);

            $sidebar = 'sites';
            $active = 'details';

            Session::put('sidebarArguments.siteKey', $siteKey);
            Session::put('sidebarArguments.activeSecondMenu', 'details');

            return view('private.entities.site', compact('title', 'entityKey', 'site', 'siteKey', 'homePageTypes', 'languages','confs', 'sidebar', 'active'));
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["entities.updateSite" => $e->getMessage()]);
        }
    }

    /**
     * @param $entityKey
     * @param $siteKey
     * @return View
     */
    public function deleteSiteConfirm($entityKey, $siteKey)
    {
        $data = array();
        $data['action'] = action("EntitiesController@destroyEntitySite", ['entityKey' => $entityKey, 'siteKey' => $siteKey]);
        $data['title'] = "DELETE";
        $data['msg'] = "Are you sure you want to delete this Site for this Entity?";
        $data['btn_ok'] = "Delete";
        $data['btn_ko'] = "Cancel";
        return view("_layouts.deleteModal", $data);
    }

    /**
     * @param $entityKey
     * @param $siteKey
     * @return $this|string
     */
    public function destroyEntitySite($entityKey, $siteKey)
    {
        try {
            Orchestrator::deleteEntitySite($siteKey);
            Session::flash('message', trans('privateSites.delete_ok'));
            return action('EntitiesController@show', $entityKey);
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["entities.deleteSite" => $e->getMessage()]);
        }
    }

    /**
     * Display a listing of the resource.
     * @param $entityKey
     * @return $this
     */
    public function tableLayoutsEntity($entityKey)
    {
        try {
            $entity = Orchestrator:: getEntity($entityKey);

            // in case of json
            $layouts = Collection::make($entity->layouts);

            return Datatables::of($layouts)
                ->editColumn('name', function ($layouts) {
                    return "<a href='" . action('LayoutsController@show', $layouts->layout_key) . "'>" . $layouts->name . "</a>";
                })
                ->addColumn('action', function ($layouts) use ($entityKey) {
                    return ONE::actionButtons(['entityKey' => $entityKey, 'layoutKey' => $layouts->layout_key], ['delete' => 'EntitiesController@deleteLayoutConfirm']);
                })
                ->make(true);
        } catch (Exception $e) {
            return redirect()->back()->withErrors([trans('privateEntities.table_layouts_entity') => $e->getMessage()]);
        }
    }

    /**
     * Show delete resource confirmation
     *
     * @param $entityKey
     * @param $layoutKey
     * @return View
     */
    public function deleteLayoutConfirm($entityKey, $layoutKey)
    {
        $data = array();

        $data['action'] = action("EntitiesController@deleteLayout", ['entityKey' => $entityKey, 'layoutKey' => $layoutKey]);
        $data['title'] = "DELETE";
        $data['msg'] = "Are you sure you want to delete this Layout for this Entity?";
        $data['btn_ok'] = "Delete";
        $data['btn_ko'] = "Cancel";

        return view("_layouts.deleteModal", $data);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $entityKey
     * @param $layoutKey
     * @return $this|string
     */
    public function deleteLayout($entityKey, $layoutKey)
    {
        try {
            Orchestrator::deleteEntityLayout($entityKey, $layoutKey);
            return action('EntitiesController@showLayouts', $entityKey);
        } catch (Exception $e) {
            return redirect()->back()->withErrors([trans('privateSites.show') => $e->getMessage()]);
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @param $entityKey
     * @return View
     */
    public function addLayout($entityKey)
    {
        $sidebar = 'entities';
        $active = 'layouts';

        Session::put('sidebarArguments', ['entityKey' => $entityKey, 'activeFirstMenu' => 'layouts']);

        return view('private.entities.addLayout', compact('entityKey', 'sidebar', 'active'));
    }

    /**
     * @param $entityKey
     * @return $this
     */
    public function tableAddLayout($entityKey)
    {
        try {
            $layouts = Orchestrator::getLayouts();
            $entity = Orchestrator::getEntity($entityKey);

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

            return Datatables::of($collection)
                ->editColumn('name', function ($collection) {
                    return $collection->name;
                })
                ->addColumn('action', function ($collection) use ($entityKey) {
                    return ONE::actionButtons(['entityKey' => $entityKey, 'layoutKey' => $collection->layout_key], ['add' => 'EntitiesController@addLayoutAction']);
                })
                ->make(true);
        } catch (Exception $e) {
            return redirect()->back()->withErrors([trans('privateSites.table_add_layout') => $e->getMessage()]);
        }
    }

    /**
     * Add a new resource in storage.
     *
     * @param $entityKey
     * @param $layoutKey
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function addLayoutAction($entityKey, $layoutKey)
    {
        try {
            $entity = Orchestrator::setLayoutEntity($entityKey, $layoutKey);
            $entity = Orchestrator::getEntity($entityKey);
            return view('private.entities.layouts', compact('entity'));
        } catch (Exception $e) {
            return redirect()->back()->withErrors([trans('privateSites.add_layout_action') => $e->getMessage()]);
        }
    }

    /**
     * @param $entityKey
     * @return View
     */
    public function addAuthMethod($entityKey)
    {
        $sidebar = 'entities';
        $active = 'auth';

        Session::put('sidebarArguments', ['entityKey' => $entityKey, 'activeFirstMenu' => 'auth']);

        return view('private.entities.addAuthMethod', compact('entityKey', 'sidebar', 'active'));
    }

    /**
     * @param $entityKey
     * @return $this
     */
    public function tableAuthMethod($entityKey)
    {
        try {
            $authMethodsList = Orchestrator::getEntityAuthMethods($entityKey);

            // in case of json
            $authMethods = Collection::make($authMethodsList);

            return Datatables::of($authMethods)
                ->addColumn('action', function ($authMethods) use ($entityKey) {
                    return ONE::actionButtons([$entityKey, $authMethods->auth_method_key], ['delete' => 'EntitiesController@deleteAuthMethodConfirm']);
                })
                ->make(true);
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["entities.tableAuthMethodEntity" => $e->getMessage()]);
        }
    }

    /**
     * @param $entityKey
     * @return $this
     */
    public function tableAddAuthMethod($entityKey)
    {
        try {
            $authMethodsList = Orchestrator::getAvailableEntityAuthMethods($entityKey);

            // in case of json
            $authMethods = Collection::make($authMethodsList);

            return Datatables::of($authMethods)
                ->editColumn('name', function ($authMethods) {
                    return $authMethods->name;
                })
                ->addColumn('action', function ($authMethods) use ($entityKey) {
                    return ONE::actionButtons([$entityKey, $authMethods->auth_method_key], ['add' => 'EntitiesController@addAuthMethodAction']);
                })
                ->make(true);
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["entities.tableAuthMethodEntity" => $e->getMessage()]);
        }
    }

    /**
     * @param $entityKey
     * @param $authMethodKey
     * @return View
     */
    public function deleteAuthMethodConfirm($entityKey, $authMethodKey)
    {
        $data = array();

        $data['action'] = action("EntitiesController@deleteAuthMethod", ['entityKey' => $entityKey, 'authMethodKey' => $authMethodKey]);
        $data['title'] = "DELETE";
        $data['msg'] = "Are you sure you want to delete this Authentication Method for this Entity?";
        $data['btn_ok'] = "Delete";
        $data['btn_ko'] = "Cancel";

        return view("_layouts.deleteModal", $data);
    }

    /**
     * @param $entityKey
     * @param $authMethodKey
     * @return $this|string
     */
    public function deleteAuthMethod($entityKey, $authMethodKey)
    {
        try {
            Orchestrator::deleteEntityAuthMethod($entityKey, $authMethodKey);
            Session::flash('message', trans('privateSites.delete_ok'));
            return action('EntitiesController@showAuthMethods', $entityKey);
        } catch (Exception $e) {
            return redirect()->back()->withErrors([trans('privateEntities.show') => $e->getMessage()]);
        }
    }

    /**
     * @param $entityKey
     * @param $authMethodKey
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function addAuthMethodAction($entityKey, $authMethodKey)
    {
        try {
            Orchestrator::setEntityAuthMethod($entityKey, $authMethodKey);
            Session::flash('message', trans('privateSites.auth_method_add_ok'));
            return redirect()->action('EntitiesController@showAuthMethods', $entityKey);
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["privateSites.auth_method_add" => $e->getMessage()]);
        }
    }

    /**
     * @param $entityKey
     * @return $this
     */
    public function tableEntityModule($entityKey)
    {
        try {
            $entityModulesList = Orchestrator::getActiveEntityModules($entityKey);

            // in case of json
            $modules = Collection::make($entityModulesList);

            return Datatables::of($modules)
                ->editColumn('name', function ($modules) {
                    return $modules->name;
                })
                ->editColumn('_blank', function () {
                    return "";
                })
                ->make(true);

        } catch (Exception $e) {
            return redirect()->back()->withErrors(["entities.tableEntityModule" => $e->getMessage()]);
        }
    }

    /**
     * @param $entityKey
     * @return $this|View
     */
    public function addEntityModule($entityKey)
    {
        try {
            $modules = Orchestrator::getModulesList();
            $entityModulesList = json_decode(json_encode(Orchestrator::getActiveEntityModules($entityKey)),true);

            $data = [];

            $data['entityKey'] = $entityKey;
            $data['modules'] = $modules->data;
            $data['entityModules'] = $entityModulesList;
            $data['activeModules'] = $entityModulesList;
            $data['sidebar'] = 'entities';
            $data['active'] = 'modules';

            Session::put('sidebarArguments', ['entityKey' => $entityKey, 'activeFirstMenu' => 'modules']);

            return view('private.entities.addEntityModule', $data);

        } catch (Exception $e) {
            return redirect()->back()->withErrors(["entities.addEntityModule" => $e->getMessage()]);
        }
    }

    /**
     * @param Request $request
     * @param $entityKey
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateEntityModules(Request $request, $entityKey)
    {
        try {
            $modules = isset($request->modules) ? $request->modules : [];
            $modulesTypes = isset($request->modules_types) ? $request->modules_types : [];
            $data = [];
            foreach ($modules as $module){
                $data [] = ['module_key' => $module, 'module_type_keys' => (array_key_exists ($module, $modulesTypes) ? $modulesTypes[$module] : [])];
            }
            Orchestrator::updateEntityModules($entityKey, $data);
            Session::flash('message', trans('privateEntities.module_update_ok'));

            \Cache::forget('entityModulesActive_'.$entityKey);
            return redirect()->action('EntitiesController@showModules', $entityKey);

        } catch (Exception $e) {
            Session::flash('message', trans('privateEntities.module_update_nok'));
            return redirect()->back()->withErrors(["privateEntities.add_entity_module" => $e->getMessage()]);
        }
    }

    /**
     * @param Request $request
     * @param $entityKey
     * @return $this
     */
    public function showNotifications(Request $request, $entityKey){
        try{
            if (Session::get('user_role') == 'admin' || ONE::verifyUserPermissionsShow('orchestrator', 'entity_notification')) {

                $data['notificationTypes'] = EMPATIA::getNotificationTypes();
                $data['entityNotifications'] = EMPATIA::getEntityNotifications();
                $data['entityKey'] = $entityKey;
                $data['groups'] = Orchestrator::getEntityGroups();

                $data['type'] = 'teste'; //Used Only For Debug - DELETE AFTER!!

                return view('private.entities.notifications.notifications', $data);
            }
            return redirect()->back()->withErrors(["Error" => "Unauthorized"]);
        } catch (Exception $e){
            Session::flash('error', trans('privateEntities.show_notifications_nok'));
            return redirect()->back()->withErrors($e->getMessage());
        }
    }

    /**
     * @param Request $request
     * @param $entityKey
     * @return $this|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function editNotifications(Request $request, $entityKey)
    {
        try {
            if (Session::get('user_role') == 'admin' || ONE::verifyUserPermissionsShow('orchestrator', 'entity_notification')) {

                $data['notificationTypes'] = EMPATIA::getNotificationTypes();
                $data['entityNotifications'] = EMPATIA::getEntityNotifications();
                $data['entityKey'] = $entityKey;
                $data['groups'] = Orchestrator::getEntityGroups();

                $data['type'] = 'teste'; //Used Only For Debug - DELETE AFTER!!

                return view('private.entities.notifications.notifications', $data);
            }
            return redirect()->back()->withErrors(["Error" => "Unauthorized"]);
        } catch (Exception $e) {
            Session::flash('error', trans('privateEntities.edit_notifications_nok'));
            return redirect()->back()->withErrors($e->getMessage());
        }
    }

    /**
     * @param Request $request
     * @param $entityKey
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function updateNotifications(Request $request, $entityKey){
        try{
            if (Session::get('user_role') == 'admin' || ONE::verifyUserPermissionsUpdate('orchestrator', 'entity_notification')) {

                $entityNotificationTypeCodes = $request->input('notifications');
                $groups = $request->input('groups');

                EMPATIA::setEntityNotifications($entityNotificationTypeCodes, $groups);

                Session::flash('message', trans('privateEntities.update_notifications_ok'));
                return redirect()->action('EntitiesController@showNotifications', $entityKey);
            }
            return redirect()->back()->withErrors(["Error" => "Unauthorized"]);
        } catch (Exception $e){
            Session::flash('error', trans('privateEntities.update_notifications_nok'));
            return redirect()->back()->withErrors($e->getMessage());
        }
    }

    /**
     * @param Request $request
     * @param $entityKey
     * @param $notificationCode
     * @return $this|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function createEntityNotificationTemplate(Request $request, $entityKey, $notificationCode){
        try{
            if (Session::get('user_role') == 'admin' || ONE::verifyUserPermissionsShow('orchestrator', 'entity_notification')) {

                $data['entityKey'] = $entityKey;
                $data['notificationCode'] = $notificationCode;
                $data['languages'] = Orchestrator::getLanguageList();

                return view('private.entities.notifications.entityEmailTemplate', $data);
            }
            return redirect()->back()->withErrors(["Error" => "Unauthorized"]);
        } catch (Exception $e){
            Session::flash('error', trans('privateEntities.create_notification_template_nok'));
            return redirect()->back()->withErrors($e->getMessage());
        }
    }

    /**
     * @param Request $request
     * @param $entityKey
     * @param $notificationCode
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showEntityNotificationTemplate(Request $request, $entityKey, $notificationCode){
        try{
            if (Session::get('user_role') == 'admin' || ONE::verifyUserPermissionsShow('orchestrator', 'entity_notification')) {

                $data['entityKey'] = $entityKey;
                $data['notificationCode'] = $notificationCode;
                $data['languages'] = Orchestrator::getLanguageList();
                $data['templateKey'] = $request->input('template_key');

                $translations = [];
                $emailTemplate = EMPATIA::getEntityNotificationTemplate($data['templateKey']);

                if (isset($emailTemplate->translations)) {
                    $translations[$notificationCode] = $emailTemplate->translations;
                }

                $data['translations'] = $translations;

                return view('private.entities.notifications.entityEmailTemplate', $data);
            }
            return redirect()->back()->withErrors(["Error" => "Unauthorized"]);
        } catch (Exception $e){
            Session::flash('error', trans('privateEntities.show_notification_template_nok'));
            return redirect()->back()->withErrors($e->getMessage());
        }
    }


    /**
     * @param Request $request
     * @param $entityKey
     * @param $notificationCode
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function editEntityNotificationTemplate(Request $request, $entityKey, $notificationCode){
        try{
            if (Session::get('user_role') == 'admin' || ONE::verifyUserPermissionsShow('orchestrator', 'entity_notification')) {

                $data['entityKey'] = $entityKey;
                $data['notificationCode'] = $notificationCode;
                $data['languages'] = Orchestrator::getLanguageList();
                $data['templateKey'] = $request->input('template_key');


                $translations = [];
                $emailTemplate = EMPATIA::getEntityNotificationTemplate($data['templateKey']);

                if (isset($emailTemplate->translations)) {
                    $translations[$notificationCode] = $emailTemplate->translations;
                }

                $data['translations'] = $translations;

                return view('private.entities.notifications.entityEmailTemplate', $data);
            }
            return redirect()->back()->withErrors(["Error" => "Unauthorized"]);
        } catch (Exception $e){
            Session::flash('error', trans('privateEntities.edit_notification_template_nok'));
            return redirect()->back()->withErrors($e->getMessage());
        }
    }

    /**
     * @param Request $request
     * @param $entityKey
     * @param $notificationCode
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function updateEntityNotificationTemplate(Request $request, $entityKey, $notificationCode){
        try {
            if (Session::get('user_role') == 'admin' || ONE::verifyUserPermissionsUpdate('orchestrator', 'entity_notification')) {

                $params = $request->all();
                $templateKey = $request->input('template_key');
                $languages = Orchestrator::getLanguageList();

                $translations = [];
                foreach ($languages as $language) {
                    if (!empty($params['content_' . $language->code])) {
                        $translations[] = [
                            'header' => "",
                            'footer' => "",
                            'content' => $params['content_' . $language->code],
                            'subject' => $params['subject_' . $language->code],
                            'language_code' => $language->code,
                        ];
                    }
                }

                EMPATIA::updateEntityNotificationTemplate($templateKey, $translations);

                Session::flash('message', trans('privateEntities.update_entity_notification_template_ok'));
                return redirect()->action('EntitiesController@showNotifications', $entityKey);
            }
            return redirect()->back()->withErrors(["Error" => "Unauthorized"]);
        }catch (Exception $e){}
        Session::flash('error', trans('privateEntities.update_notification_template_nok'));
        return back()->withInput()->withErrors($e->getMessage());
    }

    /**
     * @param Request $request
     * @param $entityKey
     * @param $notificationCode
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeEntityNotificationTemplate(Request $request, $entityKey, $notificationCode){
        try {
            if (Session::get('user_role') == 'admin' || ONE::verifyUserPermissionsCreate('orchestrator', 'entity_notification')) {
                $params = $request->all();
                $siteKey = Session::get('X-SITE-KEY');
                $languages = Orchestrator::getLanguageList();

                $translations = [];
                foreach ($languages as $language) {
                    if (!empty($params['content_' . $language->code])) {
                        $translations[] = [
                            'header' => "",
                            'footer' => "",
                            'content' => $params['content_' . $language->code],
                            'subject' => $params['subject_' . $language->code],
                            'language_code' => $language->code,
                        ];
                    }
                }
                EMPATIA::setEntityNotificationTemplate($notificationCode, $translations, $siteKey);

                Session::flash('message', trans('privateEntities.store_entity_notification_template_ok'));
                return redirect()->action('EntitiesController@showNotifications', $entityKey);
            }
            return redirect()->back()->withErrors(["Error" => "Unauthorized"]);
        }catch (Exception $e) {
            Session::flash('error', trans('privateEntities.create_notification_template_nok'));
            return back()->withInput()->withErrors($e->getMessage());
        }
    }

    public function createWizard(){
        try {
            $languages = Orchestrator::getAllLanguages();
            $countries = Orchestrator::getCountryList();
            $timezones = Orchestrator::getTimeZoneList();
            $currencies = Orchestrator::getCurrencyList();

            $title = trans('privateEntities.create_entity');

            return view('private.wizards.entity', compact("languages","countries","timezones","currencies","title"));
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["private.entities.entity" => $e->getMessage()]);
        }
    }

    public function storeWizard(Request $request){
        try {
            $data['country_id'] = $request->get("country");
            $data['country_id'] = $request->get("country");
            $data['currency_id'] = $request->get("currency");
            $data['timezone_id'] = $request->get("timezone");
            $data['name'] = $request->input('name');
            $data['url'] = $request->input('url');
            $data['description'] = $request->input('description');
            $data['designation'] = $request->input('designation');
            $data['no_reply_email'] = $request->input('no_reply_email');
            $data['link'] = $request->input('link');

            $entity = Orchestrator::setEntity($data);

            Orchestrator::setEntityLanguage($request->get("language"), $entity->entity_key);

            Orchestrator::setLayoutEntity($entity->entity_key, null, $request->input('layout'));

            $data['layout_reference'] = $request->input('layout');
            $data['entity_key'] = $entity->entity_key;
            $data['active'] = 1;
            Orchestrator::setNewEntitySite($entity->entity_key, $data);


            Session::flash('message', trans('entity.store_ok'));


            if (Session::get("firstInstallWizardStarted",false)) {
                $request->entityKey = $entity->entity_key;
                self::setEntityKey($request);
                Session::put("firstInstallWizardEntityName",$entity->name);
                return redirect()->action("QuickAccessController@firstInstallWizard");
            } else
                return redirect()->action('EntitiesController@show', $entity->entity_key);

        } catch (Exception $e) {
            return redirect()->back()->withErrors(["entity.store" => $e->getMessage()]);
        }
    }

    /**
     * @param Request $request
     * @return $this|string
     */
    public function manualUpdateTopicVotesInfo(Request $request){
        try {
            $entityKey = $request->input('entity_key');
            EMPATIA::manualUpdateTopicVotesInfo($entityKey);

            return 'OK';
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }
}
