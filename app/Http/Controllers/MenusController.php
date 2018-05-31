<?php

namespace App\Http\Controllers;

use App\ComModules\CB;
use App\ComModules\Questionnaire;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\One\One;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Collection;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\UserRequest;
use App\Http\Requests\MenuRequest;
use App\ComModules\Orchestrator;
use App\ComModules\CM;
use Datatables;
use Session;
use View;
use Breadcrumbs;
use Exception;

class MenusController extends Controller
{
    private $configs = [];

    public function __construct()
    {
        View::share('private.menus', trans('menu.menu'));



    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index($accessM = null)
    {
        if(Session::get('user_role') != 'admin'){
            return redirect()->back()->withErrors(["private" => trans('privateEntitiesDivided.permission_message')]);
        }

        try {
            // Request for access menu list
            $accessMenu = Orchestrator::listAccessMenu();

            // Request menu list
            if(!empty($accessM)){

                $menu = CM::listMenus($accessM);
            }

            $title = trans('privateMenus.list_menus');
            // Return Menu Tree View
            return view('private.menus.indexTree', compact('title', 'menu', 'accessMenu', 'accessM'));

        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["menus.index" => $e->getMessage()]);
        }
    }

    /**
     * Create a new resource.
     *
     * @param Access Menu Type
     * @return Response
     */
    public function create($accessM = null)
    {
        try {

            $languages = Orchestrator::getLanguageList();

            // Get Menu Type List
            $menuTypesList = CM::getMenuTypeList();

            // Setting data for a Page Select
            $menuTypes = [];
            foreach($menuTypesList as $item){
                $menuTypes[$item->id] = $item->title;
            }

            // Get Access Menu List
            $access = Orchestrator::listAccessMenu();
            // Get the access menu
            foreach($access as $aM){
                if($aM->id == $accessM){
                    $accessMenu = $aM;
                }
            }

            // Get menu tree for an access menu
            $parent = CM::listMenus($accessMenu->id);

            // Get parent menu
            $parent_name = [];
            foreach($parent as $p){
                $parent_name[$p->id] = $p->title;
            }

            // Get pages list and setting data for a page select
            $dataPageList = Orchestrator::getPageListByType("pages");

            // Get CM page contents filtered with page keys
            $pageList = CM::listContent($dataPageList);

            $pages = [];
            foreach($pageList as $item){
                $pages[$item->content_key] = (isset($item->title)) ? $item->title : "";
            }

            /** Get pages by new cms*/
            $pagesNew = collect(CM::getNewContents("pages"))->pluck('name','content_key')->toArray();


            // Get news list and setting data for a news select
            $dataNewsList = Orchestrator::getPageListByType("news");

            // Get CM page contents filtered with page keys
            $newsList = CM::listContent($dataNewsList);

            $news = [];
            $news ['showContentsList'] = trans('privateMenus.news_list');
            foreach($newsList as $item){
                $news[$item->content_key] = (isset($item->title)) ? $item->title : "";
            }

            // Get pages list and setting data for a page select
            $dataEventList = Orchestrator::getPageListByType("events");

            // Get CM page contents filtered with page keys
            $eventList = CM::listContent($dataEventList);

            $events = [];
            $events ['showContentsList'] = trans('privateMenus.events_list');

            foreach($eventList as $item){
                $events[$item->content_key] = (isset($item->title)) ? $item->title : "";
            }


            // Get questionnaire list and setting data for a questionnaire select
            $questionnaires = [];

            $qList = Questionnaire::getQuestionnaireList();
            foreach( $qList as $q){
                $questionnaires[$q->form_key] = $q->title;
            }

            // Polls / Event Schedule
            $pollsList = Questionnaire::getEventSchedulesList();

            $polls = [];
            foreach ($pollsList as $item) {
                $polls[$item->key] = $item->title;
            }


            //GET CBS INFORMATION
            $cbTypes = Orchestrator::getCbTypesList();
            $cbsData = [];

            foreach($cbTypes as $cbType){

                $cbsData[$cbType->code] = ['list' => trans('privateMenus.pad_list')];
                $list =  Orchestrator::getCbTypes($cbType->code);
                $listCb = [];

                foreach ($list as $item) {
                    $listCb[] = $item;
                }
                if(!empty($listCb)){
                    $list = CB::getListCBs($listCb);
                    foreach ($list as $item) {
                        $cbsData[$cbType->code][$item->cb_key] = $item->title;
                    }
                }
            }


            // Prepare data to send to the view
            $data = [];
            $data['languages']  = $languages;
            $data['accessMenu'] = $accessMenu;
            $data['parents']    = $parent_name;
            $data['pages']      = $pages;
            $data['pagesNew']      = $pagesNew;

            $data['news']       = $news;
            $data['events']     = $events;
            $data['forums']     = $cbsData['forum'];

            $data['surveys']  = $cbsData['survey'];
            $data['discussions']  = $cbsData['discussion'];
            $data['ideas']  = $cbsData['idea'];
            $data['proposals']  = $cbsData['proposal'];
            $data['tematicConsultations'] = $cbsData['tematicConsultation'];
            $data['publicConsultations'] = $cbsData['publicConsultation'];
            $data['phase1'] = $cbsData['phase1'] ?? [];
            $data['phase2'] = $cbsData['phase2'] ?? [];
            $data['phase3'] = $cbsData['phase3'] ?? [];


            $data['questionnaires'] = $questionnaires;
            $data['polls'] = $polls;
            $data['menuTypes']  = $menuTypes;
            //$data['conferenceEvents'] = $conferenceEvents;
            $data['conferenceEvents'] = [];
            $data['title'] = trans('privateMenus.create_menus');


            // Return view
            return view('private.menus.menu', $data);
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["menu.index_failed" => $e->getMessage()]);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param MenuRequest $request
     * @return $this|View
     */
    public function store(MenuRequest $request)
    {
        $translations=[];

        $languages = Orchestrator::getLanguageList();
        $accM = $request->access_id;


        foreach($languages as $language) {
            $title = ($language->default == true ? $request->input("required_title_".$language->code) : $request->input("title_".$language->code));
            $link = ($_REQUEST["link_" . $language->code] == "") ? "" : $_REQUEST["link_" . $language->code];
            $translations[] = [
                'language_code' => $language->code,
                'title' => $title,
                'link' => $link
            ];
        }

        try {
            $menu = CM::setMenu($accM, $request->parent_id, $request->type_id, $request->value, $request->private, $translations);
            Session::flash('message', trans('menu.store_ok'));
            return redirect()->action('MenusController@show', $menu->menu_key);
        }
        catch(Exception $e) {
            //TODO: save inputs
            return redirect()->back()->withErrors(["menu.store" => $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        if(Session::get('user_role') != 'admin'){
            return redirect()->back()->withErrors(["private" => trans('privateEntitiesDivided.permission_message')]);
        }

        try {
            $data = [];

            $languages = Orchestrator::getLanguageList();


            // Get menu
            $menu = CM::editMenu($id);

            // Get access menu
            $accessMenu = Orchestrator::getAccessMenu($menu->access_id);

            // Get parent menu if exist's
            if ($menu->parent_key != "") {
                $response = CM::getMenuParent($menu->parent_key);

                $parent = (array)$response;
                $data['parent'] = $parent;
            }


            // Get Menu Type List
            $menuType = "";
            if ($menu->type_id != 0) {
                $response = CM::getMenuType($menu->type_id);
                // Extracting JSON from response
                $menuType = $response->title;
            }

            // Page
            $data['page'] = null;
            if (!empty($menu->value) && ($menu->type_id == 2 || $menu->type_id == 3 || $menu->type_id == 4)) {
                $response = CM::getContentByKey($menu->value);

                $page = (array)$response;
                $data['page'] = $page;

            }

            /** New CMS - Get Pages*/
            if (!empty($menu->value) && ($menu->type_id == 18)) {
                try {
                    $pageNew = CM::getNewContent($menu->value);
                } catch (Exception $e) {
                    $pageNew = null;
                }
                $data['pageNew'] = $pageNew;
            }

            // CB
            $data['cb'] = null;
            if (!empty($menu->value) && ($menu->type_id == 5 || $menu->type_id == 6 || $menu->type_id == 7 || $menu->type_id == 11
                    || $menu->type_id == 12 || $menu->type_id == 13 || $menu->type_id == 14 || $menu->type_id == 15
                    || $menu->type_id == 16 || $menu->type_id == 17)) {

                if ($menu->value == 'list') {
                    $data['cb'] = ['title' => trans('privateMenus.pad_list')];
                } else {
                    $response = CB::getCb($menu->value);
                    $cb = (array)$response;
                    $data['cb'] = $cb;
                }

                // questionnaires
                $data['questionnaire'] = null;
                if (!empty($menu->value) && $menu->type_id == 8) {
                    $response = Questionnaire::getQuestionnaire($menu->value);
                    $questionnaire = (array)$response;
                    $data['questionnaire'] = $questionnaire;
                }

                // eventSchedule
                $data['eventSchedule'] = null;
                if (!empty($menu->value) && $menu->type_id == 9) {
                    $response = Questionnaire::getEventSchedule($menu->value);
                    $eventSchedule = (array)$response;
                    $data['eventSchedule'] = $eventSchedule;
                }
            }

            // Menu translation
            $menuTranslation = collect($menu->translations)->keyBy('language_code')->toArray();

            // Prepare data to send to the view
            $data['menu'] = $menu;
            $data['languages'] = $languages;
            $data['accessMenu'] = $accessMenu;
            $data['accessM'] = $accessMenu->id;
            $data['menuTranslation'] = $menuTranslation;
            $data['menuType'] = $menuType;
            $data['title'] = trans('privateMenus.show_menu') . ' ' . (isset(reset($menuTranslation)->title) ? reset($menuTranslation)->title : null);
            $data['sidebar'] = 'menu';
            $data['active'] = 'indexTree';

            Session::put('sidebarArguments', ['accessM' => $accessMenu->id, 'activeFirstMenu' => 'indexTree']);

            return view('private.menus.menu', $data);
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["menu.show" => $e->getMessage()]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param $id
     * @return View
     */
    public function edit($id)
    {
        try {
            $languages = Orchestrator::getLanguageList();

            // Get menu details
            $menu = CM::editMenu($id);

            $menuTranslation = collect($menu->translations)->keyBy('language_code')->toArray();

            // Get Menu Type List
            $menuTypesList = CM::getMenuTypeList();

            // Setting data for a Page Select
            $menuTypes = [];
            foreach($menuTypesList as $item){
                $menuTypes[$item->id] = $item->title;
            }

            // Get access menu list
            $accessMenu = Orchestrator::getAccessMenu($menu->access_id);

            // Get menu list for a specific access menu
            $parent = CM::listMenus($accessMenu->id);

            $parentName = [];
            foreach($parent as $p){
                $parentName[$p->id] = $p->title;
            }
            $parentName[$p->id+1] = "NONE";

            // Get pages list and setting data for a page select
            $dataPageList = Orchestrator::getPageListByType("pages");

            // Get CM page contents filtered with page keys
            $pageList = CM::listContent($dataPageList);

            $pages = [];
            foreach($pageList as $item){
                $pages[$item->content_key] = (isset($item->title)) ? $item->title : "";
            }


            /** Get pages by new cms*/
            $pagesNew = collect(CM::getNewContents("pages"))->pluck('name','content_key')->toArray();

            // Get news list and setting data for a news select
            $dataNewsList = Orchestrator::getPageListByType("news");

            // Get CM page contents filtered with page keys
            $newsList = CM::listContent($dataNewsList);

            $news = [];
            $news ['showContentsList'] = trans('privateMenus.news_list');

            foreach($newsList as $item){
                $news[$item->content_key] = (isset($item->title)) ? $item->title : "";
            }

            // Get pages list and setting data for a page select
            $dataEventList = Orchestrator::getPageListByType("events");

            // Get CM page contents filtered with page keys
            $eventList = CM::listContent($dataEventList);

            $events = [];
            $events ['showContentsList'] = trans('privateMenus.events_list');

            foreach($eventList as $item){
                $events[$item->content_key] = (isset($item->title)) ? $item->title : "";
            }


            // Get questionnaire list and setting data for a questionnaire select
            $questionnaires = [];
            $qList = Questionnaire::getQuestionnaireList();
            foreach( $qList as $q){
                $questionnaires[$q->form_key] = $q->title;
            }

            // Polls / Event Schedule
            $pollsList = Questionnaire::getEventSchedulesList();

            $polls = [];
            foreach ($pollsList as $item) {
                $polls[$item->key] = $item->title;
            }


            //GET CBS INFORMATION
            $cbTypes = Orchestrator::getCbTypesList();
            $cbsData = [];

            foreach($cbTypes as $cbType){

                $cbsData[$cbType->code] = ['list' => trans('privateMenus.pad_list')];
                $list =  Orchestrator::getCbTypes($cbType->code);
                $listCb = [];

                foreach ($list as $item) {
                    $listCb[] = $item->cb_key;
                }
                if(!empty($listCb)){
                    $list = CB::getListCBs($listCb);
                    foreach ($list as $item) {
                        $cbsData[$cbType->code][$item->cb_key] = $item->title;
                    }
                }
            }


            //REVIEW CODE
            // Conference  Events
//            $response = ONE::get([
//                'component' => 'events',
//                'api'       => 'event',
//                'method'    => 'list'
//            ]);

//            if($response->statusCode() != 200) {
//                throw new Exception("Failed to get list of polls.");
//            }
            //$conferenceEventsList = $response->json()->data;
            $conferenceEventsList = [];

            $conferenceEvents = [];
            foreach ($conferenceEventsList as $item) {
                $conferenceEvents[$item->event_key] = $item->event_translations[0]->name;
            }


            // Prepare data to send to the view
            $data = [];
            $data['languages']  = $languages;
            $data['menu'] = $menu;
            $data['accessMenu'] = $accessMenu;
            $data['accessM'] = $accessMenu->id;
            $data['parents'] = $parentName;
            $data['pages'] = $pages;
            $data['pagesNew'] = $pagesNew;

            $data['news'] = $news;
            $data['events'] = $events;
            $data['questionnaires'] = $questionnaires;
            $data['polls'] = $polls;
            $data['menuTranslation'] = $menuTranslation;
            $data['menuTypes']  = $menuTypes;
            $data['conferenceEvents'] = $conferenceEvents;
            $data['conferenceEventTranslation']= $conferenceEventsList;
            $data['forums']     = $cbsData['forum'];
            $data['discussions']  = $cbsData['discussion'];
            $data['ideas']  = $cbsData['idea'];
            $data['proposals']  = $cbsData['proposal'];
            $data['tematicConsultations'] = $cbsData['tematicConsultation'];
            $data['publicConsultations'] = $cbsData['publicConsultation'];
            $data['phase1'] = $cbsData['phase1'] ?? [];
            $data['phase2'] = $cbsData['phase2'] ?? [];
            $data['phase3'] = $cbsData['phase3'] ?? [];


            $data['title'] = trans('privateMenus.update_menu').' '.(isset(reset($menuTranslation)->title) ? reset($menuTranslation)->title : null);
            $data['sidebar'] = 'menu';
            $data['active'] = 'indexTree';

            Session::put('sidebarArguments', ['accessM' => $accessMenu->id, 'activeFirstMenu' => 'indexTree']);

            // Return view
            return view('private.menus.menu', $data);
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["menu.index_failed" => $e->getMessage()]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param MenuRequest $request
     * @param $id
     * @return $this|View
     */
    public function update(MenuRequest $request, $menuKey)
    {
        $menuTranslation = [];
        $languages = Orchestrator::getLanguageList();

        foreach($languages as $language) {
            $title = ($language->default == true ? $request->input("required_title_".$language->code) : $request->input("title_".$language->code));
            $link = ($_REQUEST["link_" . $language->code] == "") ? "" : $_REQUEST["link_" . $language->code];
            $menuTranslation[] = [
                'language_code' => $language->code,
                'title' => $title,
                'link' => $link
            ];
        }

        try {
            $menu = CM::update($request->access_id, $request->parent_id, $request->page_id, $request->type_id, $request->value, $request->private, $menuTranslation, 0, $menuKey);
            Session::flash('message', trans('menu.store_ok'));
            return redirect()->action('MenusController@show', $menu->menu_key);
        }
        catch(Exception $e) {
            //TODO: save inputs
            return redirect()->back()->withErrors(["menu.update" => $e->getMessage()]);
        }
    }

    /**
     * Update the Menus Order in storage.
     *
     * @param Request $request
     * @return $this|View
     */
    public function updateOrder(Request $request)
    {
        $source = $request->source;  // id do menu que estamos a arrastar
        $destination = $request->destination;  // id do menu pai
        $ordering = json_decode($request->order);  // ordem nova dentro do submenu
        $rootOrdering = json_decode($request->rootOrder);  //ordem nova caso tenha ido para o root (sem pai)

        CM::updateMenuReorder($destination, $rootOrdering, $ordering, $source);
        Session::flash('message', trans('menu.updateOrder_ok'));
        return redirect()->action('MenusController@index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  $id
     * @return Response
     */
    public function destroy($id)
    {
        try {
            $menu = CM::getMenu($id);

            CM::deleteMenu($id);
            Session::flash('message', trans('menu.delete_ok'));
            return action('AccessMenusController@show', $menu->access_id);

        }
        catch(Exception $e) {
            //TODO: save inputs
            return redirect()->back()->withErrors(["menu.destroy" => $e->getMessage()]);
        }
    }

    /**
     * Opens a modal to enable the Delete Confirmation Dialog.
     *
     * @param  int $id
     * @return view
     */
    public function delete($id){

        $data = array();
        $data['action'] = action("MenusController@destroy", $id);
        $data['title'] = trans('privateMenus.delete');
        $data['msg'] = trans('privateMenus.are_you_sure_you_want_to_delete').' ?';
        $data['btn_ok'] = trans('privateMenus.delete');
        $data['btn_ko'] = trans('privateMenus.cancel');

        return view("_layouts.deleteModal", $data);
    }

}