<?php

namespace App\Http\Controllers;

use App\ComModules\CB;
use App\ComModules\CM;
use App\ComModules\Files;
use App\ComModules\Orchestrator;
use App\ComModules\Questionnaire;
use App\One\One;
use Datatables;
use Exception;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Collection;
use Session;
use View;

class HomePageConfigurationsController extends Controller
{
    public function __construct()
    {
        View::share('title', trans('privateHomePageConfigurations.title'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return View
     */
    public function index()
    {
        return view('private.homePageConfigurations.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param $siteKey
     * @param $homePageTypeKey
     * @return View
     */
    public function create($siteKey,$homePageTypeKey)
    {
        $languages = Orchestrator::getLanguageList();
        $homePageType = Orchestrator::getHomePageType($homePageTypeKey);

        $data = $this->getMenusTypesAndOptions();
        $data['homePageType']           = $homePageType;
        $data['languages']              = $languages;
        $data['siteKey']                = $siteKey;
        $data['title']                      = $homePageType->name;
        return view('private.homePageConfigurations.homePageConfiguration', $data);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        try {
            $languages = Orchestrator::getLanguageList();
            $groupName = $request->groupName;
            $homePageTypeKeys = $request->homePageTypeKeys;
            $siteKey = $request->siteKey;
            $homePageConfigurations = [];
            foreach ($homePageTypeKeys as $key){
                $typeCode = $request->input("typeCode_".$key);
                $translations = [];
                $value = null;
                switch($typeCode){
                    case 'text':
                    case 'text_area':
                    foreach($languages as $language){
                        if($request->input($language->code."_".$key)) {
                            $translations[] = [
                                'language_code' => $language->code,
                                'value' => $request->input($language->code."_".$key)
                            ];
                        }
                    }
                    break;
                    case 'internal_link':
                        $menuTypeId = $request->input("menuTypeId_".$key);
                        $valueInput = $request->input("value_".$key);
                        $value = $this->getPartialLink($menuTypeId,$valueInput);
                        break;
                    case 'link':
                        $value = $request->input("link_".$key);
                        break;
                    case 'image':
                        $value = $request->input("imageLink");
                        break;
                }
                $homePageConfigurations [] = [
                    'site_key'              => $siteKey,
                    'home_page_type_key'    => $key,
                    'translations'          => $translations,
                    'value'                 => $value
                ];
            }

            $homePageConfigurationGroup = Orchestrator::setHomePageConfigurationGroup($groupName,$homePageConfigurations);
            Session::flash('message', trans('homePageConfiguration.storeOk'));

            return redirect()->action('HomePageConfigurationsController@show', $homePageConfigurationGroup->group_key);
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors([trans("privateHomePageConfigurations.store") => $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param $groupConfigurationKey
     * @return \Illuminate\Http\RedirectResponse|View
     * @internal param $homePageConfigurationKey
     */
    public function show(Request $request, $groupConfigurationKey)
    {
        try {
            $homePageConfigurationGroup = Orchestrator::getHomePageConfigurationGroup($groupConfigurationKey);
            if($homePageConfigurationGroup->home_page_type_parent){
                $title = $homePageConfigurationGroup->home_page_type_parent->name;
            }
            else{
                $title = $homePageConfigurationGroup->home_page_configurations[0]->home_page_type->name;
            }

            $entityKey = ONE::getEntityKey();

            if($entityKey == null)
                $entityKey = $request->entityKey;

            $siteKey = $request->siteKey ?? null;

            $sidebar = 'sites';
            $active = 'homePageConfigurations';

            Session::put('sidebarArguments.siteKey', $siteKey);
            Session::put('sidebarArguments.activeSecondMenu', 'homePageConfigurations');

            return view('private.homePageConfigurations.homePageConfiguration', compact('homePageConfigurationGroup', 'entityKey', 'siteKey', 'title', 'sidebar', 'active'));
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors([ trans('privateHomePageConfigurations.show') => $e->getMessage()]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param $groupConfigurationKey
     * @return \Illuminate\Http\RedirectResponse|View
     */
    public function edit($groupConfigurationKey)
    {
        try {
            $languages = Orchestrator::getLanguageList();
            $homePageConfigurationGroup = Orchestrator::getHomePageConfigurationGroupEdit($groupConfigurationKey);
            $homePageConfiguration = json_decode(json_encode($homePageConfigurationGroup->data),true);
            if($homePageConfigurationGroup->home_page_type_parent_key){
                $homePageTypeKey = $homePageConfigurationGroup->home_page_type_parent_key;
            }
            else{
                $homePageTypeKey = reset($homePageConfiguration)['home_page_type']['home_page_type_key'];
            }
            $homePageType = Orchestrator::getHomePageType($homePageTypeKey);
            $data = $this->getMenusTypesAndOptions();
            $data['homePageType']               = $homePageType;
            $data['languages']                  = $languages;
            $data['homePageConfiguration']      = $homePageConfiguration;
            $data['homePageConfigurationGroup'] = $homePageConfigurationGroup;
            $data['title']                      = $homePageType->name;
            return view('private.homePageConfigurations.homePageConfiguration', $data);
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors([trans("privateHomePageConfigurations.edit") => $e->getMessage()]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param $groupKey
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $groupKey)
    {
        try {
            $languages = Orchestrator::getLanguageList();
            $homePageTypeKeys = $request->homePageTypeKeys;
            $homePageConfigurations = [];
            foreach ($homePageTypeKeys as $key){
                $typeCode = $request->input("typeCode_".$key);
                $translations = [];
                $value = null;
                switch($typeCode){
                    case 'text':
                    case 'text_area':
                        foreach($languages as $language){
                            if($request->input($language->code."_".$key)) {
                                $translations[] = [
                                    'language_code' => $language->code,
                                    'value' => $request->input($language->code."_".$key)
                                ];
                            }
                        }
                        break;
                    case 'internal_link':
                        $menuTypeId = $request->input("menuTypeId_".$key);
                        $valueInput = $request->input("value_".$key);
                        if($menuTypeId != '' && $valueInput != ''){
                            $value = $this->getPartialLink($menuTypeId,$valueInput);
                        }else{
                            $value = $request->input("valueUpdate_".$key);
                        }
                        break;
                    case 'link':
                        $value = $request->input("link_".$key);
                        break;
                    case 'image':
                        $value = $request->input("imageLink");
                        break;
                }
                $homePageConfigurations [] = [
                    'home_page_type_key'    => $key,
                    'translations'          => $translations,
                    'value'                 => $value
                ];
            }
            $homePageConfigurationGroup = Orchestrator::updateHomePageConfigurationGroup($groupKey,$homePageConfigurations);
            Session::flash('message', trans('privateHomePageConfigurations.updateOk'));
            return redirect()->action('HomePageConfigurationsController@show', $homePageConfigurationGroup->group_key);
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors([trans("privateHomePageConfigurations.updateNok") => $e->getMessage()]);
        }
    }


    /**
     * @param $groupKey
     * @return View
     * @internal param $homePageConfigurationKey
     */
    public function delete($groupKey)
    {
        $data = array();

        $data['action'] = action("HomePageConfigurationsController@destroy", $groupKey);
        $data['title'] = "DELETE";
        $data['msg'] = "Are you sure you want to delete this Home Page Configuration Group?".$groupKey;
        $data['btn_ok'] = "Delete";
        $data['btn_ko'] = "Cancel";

        return view("_layouts.deleteModal", $data);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $groupKey
     * @return \Illuminate\Http\RedirectResponse
     * @internal param $homePageConfigurationKey
     */
    public function destroy($groupKey)
    {
        try {
            $homePageConfiguration = Orchestrator::getHomePageConfigurationGroup($groupKey);

            Orchestrator::deleteHomePageConfigurationGroup($groupKey);
            Session::flash('message', trans('privateHomePageConfigurations.deleteOk'));

            return action('EntitiesController@showEntitySite', ['entityKey' => ONE::getEntityKey(), 'siteKey' => $homePageConfiguration->site->key]);
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors([ trans('privateHomePageConfigurations.deleteNok') => $e->getMessage()]);
        }
    }

    /**
     * @param $siteKey
     * @return mixed
     */
    public function getIndexTable(Request $request, $siteKey)
    {
        $homePageConfigurationsGroups = Orchestrator::getSiteHomePageConfigurationGroups($siteKey);
        // in case of json
        $collection = Collection::make($homePageConfigurationsGroups);

        return Datatables::of($collection)
            ->editColumn('group_name', function ($collection) use($request, $siteKey) {
                return "<a href='".action('HomePageConfigurationsController@show', ["groupKey" => $collection->group_key, "entityKey" => $request->entityKey, "siteKey" => $siteKey])."'>".$collection->group_name."</a>";
            })
            ->addColumn('action', function ($collection){
                return ONE::actionButtons($collection->group_key, ['form' => 'homePageConfigurations','edit' => 'HomePageConfigurationsController@edit', 'delete' => 'HomePageConfigurationsController@delete']);
            })
            ->make(true);
    }


    /**
     * Generate Url to create the specific resource
     * @param Request $request
     * @return string
     */
    public function getUrlWithHomePageTypeKey(Request $request){
        try {
            $siteKey = $request->siteKey;
            $homePageTypeKey = $request->homePageTypeKey;
            return action('HomePageConfigurationsController@create', ['siteKey' => $siteKey, 'homePageTypeKey' => $homePageTypeKey]);
        }
        catch(Exception $e) {
            return 'false';
        }

    }

    private function getPartialLink($type,$value){
        $action = "";
        switch ($type){
            case 1:
                break;
            case 2:
                $action = action("PublicContentsController@show", $value,false);
                break;
            case 3:
                $action = action("PublicContentsController@show", $value,false);
                break;
            case 4:
                $action = action("PublicContentsController@show", $value,false);
                break;
            case 5:
                $action = action("PublicCbsController@show", ["id" => $value, "type" => "forum"],false);
                break;
            case 6:
                $action = action("PublicCbsController@show", ["id" => $value, "type" => "discussion"],false);
                break;
            case 7:
                $action = action("PublicCbsController@show", ["id" => $value, "type" => "proposal"],false);
                break;
            case 8:
                $action = action("PublicQController@showQ", $value,false);
                break;
            case 9:
                $action = action("EventSchedulesController@publicAttendance", $value,false);
                break;
            case 10:
                $action = action("PublicConfEventsController@index", $value,false);
                break;
            case 11:
                $action = action("PublicCbsController@show", ["id" => $value, "type" => "idea"],false);
                break;
            case 12:
                $action = action("PublicCbsController@show", ["id" => $value, "type" => "publicConsultation"],false);
                break;
            case 13:
                $action = action("PublicCbsController@show", ["id" => $value, "type" => "tematicConsultation"],false);
                break;
        }
        return $action;
    }

    /**
     * Get file info
     * @param Request $request
     * @return string
     */
    public function addImage(Request $request){
        try{
            $fileId = $request->file_id;
            $file = Files::getFile($fileId);
            $link = '/files/'.$fileId.'/'.$file->code.'/1';
            $response = json_decode(json_encode($file),true);
            $response ['link'] = $link;
            return $response;
        }
        catch(Exception $e) {
            return "false";
        }
    }

    private function getMenusTypesAndOptions()
    {
        // Get Menu Type List and Setting data for the type Select
        $menuTypesList = CM::getMenuTypeList();
        $menuTypes = [];
        foreach($menuTypesList as $item){
            if($item->id != 1) {
                $menuTypes[$item->id] = $item->title;
            }
        }

        // Get pages list and setting data for a page select
        $dataPageList = Orchestrator::getPageListByType("pages");

        // Get CM page contents filtered with page keys
        $pageList = CM::listContent($dataPageList);
        $pages = [];
        foreach($pageList as $item){
            $pages[$item->content_key] = (isset($item->title)) ? $item->title : "";
        }

        // Get news list and setting data for a news select
        $dataNewsList = Orchestrator::getPageListByType("news");

        // Get CM page contents filtered with page keys
        $newsList = CM::listContent($dataNewsList);
        $news = [];
        foreach($newsList as $item){
            $news[$item->content_key] = (isset($item->title)) ? $item->title : "";
        }

        // Get pages list and setting data for a page select
        $dataEventList = Orchestrator::getPageListByType("events");

        // Get CM page contents filtered with page keys
        $eventList = CM::listContent($dataEventList);
        $events = [];
        foreach($eventList as $item){
            $events[$item->content_key] = (isset($item->title)) ? $item->title : "";
        }


        // Get questionnaire list and setting data for a questionnaire select
        $qList = Questionnaire::getQuestionnaireList();
        $questionnaires = [];
        foreach( $qList as $q){
            $questionnaires[$q->form_key] = $q->title;
        }

        // Get forum list and setting data for a forum select
        $list = Orchestrator::getCbTypes('forum');
        $forumslist = CB::getListCBs($list);
        $forums = [];
        foreach ($forumslist as $item) {
            $forums[$item->cb_key] = $item->title;
        }

        // Get Discussion list and setting data for a discussion select
        $list = Orchestrator::getCbTypes('discussion');
        $discussionsList = CB::getListCBs($list);
        $discussions = [];
        foreach ($discussionsList as $item) {
            $discussions[$item->cb_key] = $item->title;
        }

        // Get proposals list and setting data for a discussion select
        $list = Orchestrator::getCbTypes('proposal');
        $proposalsList = CB::getListCBs($list);
        $proposals = [];
        foreach ($proposalsList as $item) {
            $proposals[$item->cb_key] = $item->title;
        }

        // Get Polls / Event Schedule
        $pollsList = Questionnaire::getEventSchedulesList();
        $polls = [];
        foreach ($pollsList as $item) {
            $polls[$item->key] = $item->title;
        }

        // Get Ideas list and setting data for a discussion select
        $list = Orchestrator::getCbTypes('idea');
        $ideasList = CB::getListCBs($list);
        $ideas = [];
        foreach ($ideasList as $item) {
            $ideas[$item->cb_key] = $item->title;
        }

        // Get public consultations list and setting data for a discussion select
        $list = Orchestrator::getCbTypes('publicConsultation');
        $publicConsultationList = CB::getListCBs($list);
        $publicConsultations = [];
        foreach ($publicConsultationList as $item) {
            $publicConsultations[$item->cb_key] = $item->title;
        }

        // Get tematic consultations list and setting data for a discussion select
        $list = Orchestrator::getCbTypes('tematicConsultation');
        $tematicConsultationList = CB::getListCBs($list);
        $tematicConsultations = [];
        foreach ($tematicConsultationList as $item) {
            $tematicConsultations[$item->cb_key] = $item->title;
        }

        $data['pages']                  = $pages;
        $data['news']                   = $news;
        $data['events']                 = $events;
        $data['forums']                 = $forums;
        $data['discussions']            = $discussions;
        $data['ideas']                  = $ideas;
        $data['proposals']              = $proposals;
        $data['questionnaires']         = $questionnaires;
        $data['polls']                  = $polls;
        $data['menuTypes']              = $menuTypes;
        $data['conferenceEvents']       = [];
        $data['tematicConsultations']   = $tematicConsultations;
        $data['publicConsultations']    = $publicConsultations;

        return $data;
    }
}
