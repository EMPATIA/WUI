<?php

namespace App\Http\Controllers;

use App\ComModules\Files;
use Carbon\Carbon;
use DOMDocument;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Collection;
use App\One\One;
use App\Http\Requests;
use App\Http\Requests\ContentRequest;
use App\ComModules\Orchestrator;
use App\ComModules\CM;
use Datatables;
use Session;
use View;
use Breadcrumbs;
use URL;

class ContentsController extends Controller
{

    public function __construct()
    {
        if(Route::current() == null) return;


        View::share('private.contents', trans('form.content'));

        $this->typeId = Route::current()->getParameter('typeId');
        if($this->typeId != null){
            Session::set('typeId', $this->typeId);
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @param $typeId
     * @return view
     */
    public function index($type)
    {
        switch($type) {
            case $type == "pages":
                $title = trans('privatePages.list_pages');
                return view('private.pages.index', compact('title', 'type'));
                break;
            case $type == "news":
                $title = trans('privateNews.list_news');
                return view('private.news.index', compact('title', 'type'));
                break;
            case $type == "events":
                $title = trans('privateEvents.list_event');
                return view('private.events.index', compact('title', 'type'));
                break;
        }
    }

    /**
     * Get a datatable of Contents of the specified type
     *
     * @param  int $type
     * @return Datatable of Collection made
     */
    public function contentsDataTable($type, Request $request)
    {
        try {
            if ($request->start_date!=null){
                $dates=explode("?end_date=",$request->start_date);
                $start_date=$dates[0];
                $end_date=$dates[1];

            }else{
                $start_date=null;
                $end_date=null;

            }
            if(Session::get('user_role') == 'admin' || ONE::verifyUserPermissionsShow('cm', $type)){
                // Get orchestractor page list
                $data = Orchestrator::getPageListByType($type, null, 1);

                // Get CM page contents filtered with page keys
                $list = CM::listContent($data);

                // in case of json
                if ($start_date!=null && $end_date!=null) {

                    $newList=Collection::make($list)->where('start_date', '>=', $start_date);
                    $collection=$newList->where('start_date', '<=', $end_date);

                }     else{
                    $collection = Collection::make($list);
                }
            }else
                $collection = Collection::make([]);

            $show = Session::get('user_role') == 'admin' || ONE::verifyUserPermissionsShow('cm', $type);
            $delete = Session::get('user_role') == 'admin' || ONE::verifyUserPermissionsDelete('cm', $type);

            return Datatables::of($collection)
                ->editColumn('title', function ($collection) {
                    $title = !empty( $collection->title ) ? $collection->title : trans('form.no_content_available');
                    return "<a href='" . action('ContentsController@show', $collection->content_key) . "'>" . $title . "</a>";
                })
                ->editColumn('start_date', function ($collection) {
                    return  $collection->start_date;
                })
                ->editColumn('publish_date', function ($collection) {
                    return  $collection->publish_date;
                })
                ->addColumn('action', function ($collection) use($show, $delete) {
                    if($show == true and $delete == true)
                        return ONE::actionButtons($collection->content_key, ['show' => 'ContentsController@show', 'delete' => 'ContentsController@delete']);
                    elseif($show == false and $delete == true)
                        return ONE::actionButtons($collection->content_key, ['delete' => 'ContentsController@delete']);
                    elseif($show == true and $delete == false)
                        return ONE::actionButtons($collection->content_key, ['show' => 'ContentsController@show']);
                    else
                        return null;
                })
                ->make(true);


            return redirect()->back()->withErrors(["content.indexTable" => $response->json()->error]);
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["content.indexTable" => $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param $contentKey
     * @param null $showVersion
     * @return View
     * @internal param int $id
     */
    public function show($contentKey, $showVersion = null)
    {
        $presentLanguage = Session::get('TEMP-LANGUAGE');
        $languages = Orchestrator::getLanguageList();


        try {

            //  GET PAGE TYPE
            $response = Orchestrator::getPage($contentKey);
            $type = $response->type;

            if(Session::get('user_role') != 'admin'){
                if(!ONE::verifyUserPermissionsShow('cm', $type)) {
                    return redirect()->back()->withErrors(["private" => trans('privateEntitiesDivided.permission_message')]);
                }
            }
            //  GET CONTENT
            $content = CM::getContent($contentKey, $showVersion);

            $typeId                 = $content->type_id;
            $translations           = collect($content->translations)->keyBy('language_code')->toArray();
            $versions               = [];
            $version                = 1;
            $activeVersion          = 1;
            $displayLinkableTypes   = false;
            $linkableTypes          = [];
            $uploadKey              = '';
            $content_type_type_id   = $content->content_type_type_id;


            /**
             * Get content versions
             */
            $data = CM::getContentVersions($contentKey);

            foreach ($data as $d) {
                if ($d->enabled) {
                    $activeVersion = $d->version;
                    $marker = '* ';
                } else {
                    $marker = '';
                }

                $versions[$d->version] = $marker . 'v' . $d->version . ' ' . $d->created_at;
            }

            $version = isset($showVersion) && $showVersion > 0 ? $showVersion : $activeVersion;

            /**
             * Check if present content type should display linkable content types
             */
            $data = CM::getAllContentTypes();

            foreach ($data as $d) {
                if ($d->id == $typeId) {
                    $displayLinkableTypes = $d->display_linkable;
                }
            }


            /**
             * Get linkable content types
             */
            if($displayLinkableTypes) {

                $data = CM::getLinkableContentType();

                /*
                foreach ($data as $d) {
                    $d->translations = collect($d->translations)->keyBy('language_code');
                    $linkableTypes[] = $d;
                }
                */
            }

            /**
             * Get upload key
             */
            $uploadKey = Files::getUploadKey();

            //  CONTENT TYPE TYPES
            $contentTypes = CM::listContentTypeTypes($type);

            $contentTypesSelect = [];
            foreach($contentTypes as $types){
                $contentTypesSelect[$types->id] = $types->name;
            }

            //  DATA TO SEND TO THE VIEW
            $data = [];
            $data['type'] = $type;
            $data['content'] = $content;
            $data['translations'] = $translations;
            $data['versions'] = $versions;
            $data['activeVersion'] = $activeVersion;
            $data['version'] = $version;
            $data['typeId'] = $typeId;
            $data['uploadKey'] = $uploadKey;
            $data['displayLinkableTypes'] = $displayLinkableTypes;
            $data['linkableTypes'] = $linkableTypes;
            $data['languages'] = $languages;
            $data['presentLanguage'] = $presentLanguage;
            $data['contentTypes'] = $displayLinkableTypes;
            $data['contentTypesSelect'] = $contentTypesSelect;
            $data['content_type_type_id'] = $content_type_type_id;

            /**
             * Return
             */
            switch ($type) {
                case "pages":
                    $data['title'] = trans('privatePages.show_page');
                    return view('private.pages.page', $data);
                    break;
                case "news":
                    $data['title'] = trans('privateNews.show_news').' '.(isset(reset($translations)->title) ? reset($translations)->title : null);
                    return view('private.news.news', $data);
                    break;
                case "events":
                    $data['title'] = trans('privateEvents.show_event').' '.(isset(reset($translations)->title) ? reset($translations)->title : null);
                    return view('private.events.event', $data);
                    break;
            }


            return redirect()->back()->withErrors(["content.show" => $response->json()->error]);
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["content.show" => $e->getMessage()]);
        }
    }

    /**
     * Create a new resource.
     *
     * @return Response
     */
    public function create($type)
    {

        if(Session::get('user_role') != 'admin'){
            if(!ONE::verifyUserPermissionsCreate('cm', $type)) {
                return redirect()->back()->withErrors(["private" => trans('privateEntitiesDivided.permission_message')]);
            }
        }

        $languages = Orchestrator::getLanguageList();

        $contentTypes = CM::listContentTypeTypes($type);
        $contentTypesSelect=[];
        //$newsTypesSelect = collect($newsTypes)->pluck('name', 'id')->toArray();
        foreach($contentTypes as $types){
            if(isset($types->name))
                $contentTypesSelect[$types->id] = $types->name;
            else
                $contentTypesSelect[$types->id] = "";
        }

        $data = [];
        $data['contentTypesSelect'] = $contentTypesSelect;
        $data['contentTypes'] = $contentTypes;
        $data['type'] = $type;
        $data['page'] = 'page';
        $data['languages'] = $languages;

        switch($type){
            case "pages":
                $data['title'] = trans('privatePages.create_page');
                return view('private.pages.page', $data);
                break;
            case "news":
                $data['title']  = trans('privateNews.create_news');
                return view('private.news.news', $data);
                break;
            case "events":
                $data['title'] = trans('privateEvents.create_event');
                return view('private.events.event', $data);
                break;
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param ContentRequest $request
     * @return $this|View
     */
    public function store(ContentRequest $request){
        $languages = Orchestrator::getLanguageList();

        $contentTranslation = [];
        foreach($languages as $language){
            $contentTranslation[] = [
                'language_id' =>    $language->id,
                'language_code' =>  $language->code,
                'title'       =>    $language->default == true ? $request->input("required_title_".$language->code) : $request->input("title_".$language->code),
                'summary'     =>    $language->default == true ? $request->input("required_summary_".$language->code) : $request->input("summary_".$language->code),
                'content'     =>    $language->default == true ? $request->input("required_content_".$language->code) : $request->input("content_".$language->code),
                'link'        =>    $request->input("link_".$language->code),
                'docs_main'   =>    $request->input('docs_main_'.$language->code) != "" ? $request->input('docs_main_'.$language->code) : 0,
                'docs_side'   =>    $request->input('docs_side_'.$language->code) != "" ? $request->input('docs_side_'.$language->code) : 0,
                'highlight'   =>    $request->input('highlight_'.$language->code) != "" ? $request->input('highlight_'.$language->code) : 0,
                'slideshow'   =>    $request->input('slideshow_'.$language->code) != "" ? $request->input('slideshow_'.$language->code) : 0,
            ];
        }

        try {
            $content = CM::setContent($request, $contentTranslation);

            Orchestrator::setPage($content, $request);

            Session::flash('message', trans('content.store_ok'));
            return redirect()->action('ContentsController@show', $content->content_key);
        }
        catch(Exception $e) {
            //TODO: save inputs
            return redirect()->back()->withErrors(["content.store" => $e->getMessage()]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param ContentRequest $request
     * @param $contentKey
     * @return $this|View
     */
    public function update(ContentRequest $request, $contentKey)
    {
        if(isset($request->type)){
            if(Session::get('user_role') != 'admin'){
                if(!ONE::verifyUserPermissionsUpdate('cm', $request->type)) {
                    return redirect()->back()->withErrors(["private" => trans('privateEntitiesDivided.permission_message')]);
                }
            }
        }
        $languages = Orchestrator::getLanguageList();

        $contentTranslation = [];
        foreach($languages as $language){
            $contentTranslation[] = [
                'language_id'   =>  $language->id,
                'language_code' =>  $language->code,
                'title'       =>    $language->default == true ? $request->input("required_title_".$language->code) :$request->input("title_".$language->code),
                'summary'     =>    $language->default == true ? $request->input("required_summary_".$language->code) :$request->input("summary_".$language->code),
                'content'     =>    $language->default == true ? $request->input("required_content_".$language->code) :$request->input("content_".$language->code),
                'link'        =>    $request->input("link_".$language->code),
                'docs_main'   =>    $request->input('docs_main_'.$language->code) != "" ? $request->input('docs_main_'.$language->code) : 0,
                'docs_side'   =>    $request->input('docs_side_'.$language->code) != "" ? $request->input('docs_side_'.$language->code) : 0,
                'highlight'   =>    $request->input('highlight_'.$language->code) != "" ? $request->input('highlight_'.$language->code) : 0,
                'slideshow'   =>    $request->input('slideshow_'.$language->code) != "" ? $request->input('slideshow_'.$language->code) : 0,
            ];
        }


        try {
            $content = CM::updateContent($contentKey, $request, $contentTranslation);
            Session::flash('message', trans('content.update_ok'));
            return redirect()->action('ContentsController@show', [$contentKey, $content->version]);

        }
        catch(Exception $e) {
            //TODO: save inputs
            return redirect()->back()->withErrors(["content.update" => $e->getMessage()]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param $contentKey
     * @return View
     */
    public function edit($contentKey, $showVersion = null)
    {
        try {

            //  GET PAGE TYPE
            $response = Orchestrator::getPage($contentKey);
            $type = $response->type;

            //$type = Orchestrator::getPage()->type ?? "";
            if(Session::get('user_role') != 'admin'){
                if(!ONE::verifyUserPermissionsUpdate('cm', $type)) {
                    return redirect()->back()->withErrors(["private" => trans('privateEntitiesDivided.permission_message')]);
                }
            }

            $languages = Orchestrator::getLanguageList();
            $presentLanguage = Session::get('TEMP-LANGUAGE');

            //  GET CONTENT
            $content = CM::getContent($contentKey, $showVersion);

            $typeId                 = $content->type_id;
            $content_type_type_id   = $content->content_type_type_id;
            $translations           = collect($content->translations)->keyBy('language_code')->toArray();
            $displayLinkableTypes   = false;
            $linkableTypes          = [];

            //  CONTENT TYPE TYPES
            $contentTypes = CM::listContentTypeTypes($type);
            $contentTypesSelect = [];
            foreach($contentTypes as $types){
                $contentTypesSelect[$types->id] = $types->name;
            }

            //  DATA TO SEND TO THE VIEW
            $data = [];
            $data['type'] = $type;
            $data['content'] = $content;
            $data['translations'] = $translations;
            $data['typeId'] = $typeId;
            $data['displayLinkableTypes'] = $displayLinkableTypes;
            $data['linkableTypes'] = $linkableTypes;
            $data['languages'] = $languages;
            $data['presentLanguage'] = $presentLanguage;
            $data['contentTypes'] = $displayLinkableTypes;
            $data['contentTypesSelect'] = $contentTypesSelect;
            $data['content_type_type_id'] = $content_type_type_id;

            switch($type){
                case "pages":
                    $data['title'] = trans('privatePages.update_page');
                    return view('private.pages.page', $data);
                    break;
                case "news":
                    $data['title'] = trans('privateNews.update_news').' '.(isset(reset($translations)->title) ? reset($translations)->title : null);
                    return view('private.news.news', $data);
                    break;
                case "events":
                    $data['title'] = trans('privateEvents.update_event').' '.(isset(reset($translations)->title) ? reset($translations)->title : null);
                    return view('private.events.event', $data);
                    break;
            }

            return redirect()->back()->withErrors(["content.edit" => $response->json()->error]);
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["content.edit" => $e->getMessage()]);
        }
    }

    /**
     * Button function to Activate New Version for the specified resource.
     *
     * @param $id, int $newVersion
     * @return View
     */
    public function activateVersion($contentKey, $newVersion)
    {
        try{
            CM::activateVersion($contentKey, $newVersion);
            Session::flash('message', trans('content.activateVersion_ok'));
            return redirect()->action('ContentsController@show', $contentKey);
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["content.activateVersion" => $e->getMessage()]);
        }
    }

    /**
     * Button function to Publish Content for the specified resource.
     *
     * @param $id
     * @return View
     */
    public function publish($contentKey)
    {
        try{
            CM::publishContent($contentKey);
            Session::flash('message', trans('content.publish_ok'));
            return redirect()->action('ContentsController@show', $contentKey);
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["content.publish" => $e->getMessage()]);
        }
    }

    /**
     * Button function to Unpublish Content for the specified resource.
     *
     * @param $contentKey
     * @return View
     */
    public function unpublish($contentKey)
    {
        try{
            CM::unpublishContent($contentKey);
            Session::flash('message', trans('content.unpublish_ok'));
            return redirect()->action('ContentsController@show', $contentKey);
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["content.publish" => $e->getMessage()]);
        }
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  $contentKey
     * @return Response
     */
    public function destroy($contentKey)
    {
        try {
            $content = CM::getContentByKey($contentKey);

            if( $content->type_id == 1){
                if(Session::get('user_role') != 'admin'){
                    if(!ONE::verifyUserPermissionsDelete('cm', 'pages')) {
                        return redirect()->back()->withErrors(["private" => trans('privateEntitiesDivided.permission_message')]);
                    }
                }
            } else if( $content->type_id == 2){
                if(Session::get('user_role') != 'admin'){
                    if(!ONE::verifyUserPermissionsDelete('cm', 'news')) {
                        return redirect()->back()->withErrors(["private" => trans('privateEntitiesDivided.permission_message')]);
                    }
                }
            } else if( $content->type_id == 3){
                if(Session::get('user_role') != 'admin'){
                    if(!ONE::verifyUserPermissionsDelete('cm', 'events')) {
                        return redirect()->back()->withErrors(["private" => trans('privateEntitiesDivided.permission_message')]);
                    }
                }
            }

            CM::deleteContent($contentKey);
            if( $content->type_id == 1){
                $type = "pages";
            } else if( $content->type_id == 2){
                $type = "news";
            } else if( $content->type_id == 3){
                $type = "events";
            }

            return action('ContentsController@index', $type);
        }
        catch(Exception $e) {
            //TODO: save inputs
            //return action('ContentsController@index', $content->type_id);
        }
    }

    /**
     * Opens a modal to enable the Delete Confirmation Dialog.
     *
     * @param  string $contentKey
     * @return view
     */
    public function delete($contentKey)
    {
        $data = array();

        $data['action'] = action("ContentsController@destroy", $contentKey);
        $data['title'] = trans('privateContents.delete');
        $data['msg'] = trans('privateContents.are_you_sure_you_want_to_delete') . '?';
        $data['btn_ok'] = trans('privateContents.delete');
        $data['btn_ko'] = trans('privateContents.cancel');

        return view("_layouts.deleteModal", $data);
    }

    /**
     * Add Files to specific content.
     *
     * @param Request $request
     * @return Response
     */
    public function addFile(Request $request)
    {
        try {
            if ($request->type_id == 4) {
                $temp_files = CM::getContentFiles($request->content_key, $request->type_id);
                if (count($temp_files) == 1) {
                    CM::deleteContentFiles($request, $temp_files);

                }
            }
            CM::updateContentFiles($request);

            return "true";
        }
        catch(Exception $e) {
            return "false";
        }
    }

    /**
     * Get Files of specific content.
     *
     * @param  string $contentKey, int $typeId
     * @return Response
     */
    public function getFiles($contentKey, $typeId = null)
    {
        try{
            $temp_files = CM::getContentFiles($contentKey, $typeId);

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
            return "false";
        }
    }

    /**
     * Get First 5 Files of specific type content.
     *
     * @param  string $contentKey, int $typeId
     * @return Response
     */
    public function getFirstFiles($contentKey, $typeId = null)
    {

        try{
            $temp_files = CM::getContentFiles($contentKey, $typeId);

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
            return "false";
        }
    }


    /**
     * Get File Detais of specific content.
     *
     * @param  string $contentKey, int $file_id
     * @return Response
     */
    public function getFileDetails($contentKey, $id)
    {
        try{
            $file = CM::getContentFiles($contentKey, $id);
            $title = trans('files.show_file').' '.(isset($file->name) ? $file->name : null);
            return view('private.files.fileDetails', compact('title','file','contentKey'));
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["content.getFileDetails" => $e->getMessage()]);
        }
    }

    /**
     * Update File Detais of specific content.
     *
     * @param  string $contentKey, int $file_id
     * @return Response
     */
    public function editFileDetails($contentKey, $id)
    {
        try{
            $file = CM::getContentFiles($contentKey, $id);
            $title = trans('files.show_file').' '.(isset($file->name) ? $file->name : null);
            return view('private.files.fileDetails', compact('title', 'file','contentKey'));
        }
        catch(Exception $e) {
            //TODO: save inputs
            return redirect()->back()->withErrors(["content.updateFileDetails" => $e->getMessage()]);
        }
    }

    /**
     * Update File Detais of specific content.
     *
     * @param Request $request
     * @param  string $contentKey, int $file_id
     * @return Response
     */
    public function updateFileDetails(Request $request, $contentKey, $id)
    {
        try{
            CM::updateContentFile($contentKey, $request->all(), $id);
            Session::flash('message', trans('content.updateFileDetails_ok'));
            return redirect()->action('ContentsController@show', $contentKey);
        }
        catch(Exception $e) {
            //TODO: save inputs
            return redirect()->back()->withErrors(["content.updateFileDetails" => $e->getMessage()]);
        }
    }

    /**
     * Order Files of specific content.
     *
     * @param Request $request
     * @return Response
     */
    public function orderFile(Request $request)
    {
        try{
            CM::orderFile($request->content_key, $request->type_id, $request->movement, $request->file_id);
            Session::flash('message', trans('content.updateFileDetails_ok'));
            return redirect()->action('ContentsController@show', $request->content_key);
        }
        catch(Exception $e) {
            //TODO: save inputs
            return redirect()->back()->withErrors(["content.updateFileDetails" => $e->getMessage()]);
        }
    }

    /**
     * Delete File of specific content from storage.
     *
     * @param  Request $request
     * @param  string $contentKey, int $file_id
     * @return Response
     */
    public function deleteFile(Request $request)
    {
        try{
            CM::deleteContentFile($request->content_key, $request->file_id);
            Session::flash('message', trans('content.delete_ok'));
            return action('ContentsController@show', $request->content_key);
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["content.deleteFile" => $e->getMessage()]);
        }
    }

    private function getMainMenu()
    {
        try{
            $menus = CM::listMenus(8);

            $finalMenus = $this->buildMainMenu($menus, []);

            return $finalMenus;
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["content.getMainMenu" => $e->getMessage()]);
        }
    }

    private function buildMainMenu($menus, $menusArray, $level = 0, $idParent = 0)
    {
        if ($level >= 3) {
            return $menusArray;
        }

        foreach ($menus as $menu) {
            $subMenu = [];
            if ($menu->parent_id == $idParent && $menu->translations[0]->title != "") {
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

    private function getSideMenu($id)
    {
        try{
            $menu = CM::getMenuContent($id);
            if(empty($menu->id)){
                return ['menu' => null];
            }

            $sideMenuRoot = $this->getSideMenuRoot($menu->id);

            $menuArray = [];

            if ($sideMenuRoot['level'] == 3) {
                $menuArray = $this->buildSideMenu($sideMenuRoot['menu'], []);
            }

            if (!empty($menuArray)) {
                $parent = CM::getMenu($sideMenuRoot["menu"]);
                $menuArray = [-1 => $parent] + $menuArray;
            }
            return ['menu' => $menuArray, 'active' => $sideMenuRoot['active']];
        } catch (Exception $e) {
            return "";
        }
    }

    private function getSideMenuRoot($id)
    {
        $menu = CM::getMenu($id);

        if ($menu->parent_id == 0)
            return ['level' => 1, "menu" => $menu->id, "active" => []];
        else {
            $res = $this->getSideMenuRoot($menu->parent_id);
            if ($res['level'] == 3) {
                $res['active'][] = $menu->id;
                return $res;
            } else
                return ['level' => $res['level'] + 1, 'menu' => $menu->id, "active" => []];
        }
    }

    private function buildSideMenu($id, $menusArray)
    {
        try{
            $menusList = CM::getSonsList($id);

            foreach ($menusList as $menu) {
                $subMenu = [];

                if ($menu->translations[0]->title != "") {
                    $subMenu[0] = $menu;

                    $subMenu = $this->buildSideMenu($menu->id, $subMenu);

                    if (count($subMenu) == 1) {
                        $menusArray[$menu->id] = $menu;
                    } else {
                        $menusArray[$menu->id] = $subMenu;
                    }
                }
            }

        } catch (Exception $e) {
            return redirect()->back()->withErrors(["content.buildSideMenu" => $e->getMessage()]);
        }
    }

    public function previewPage($contentKey, $version)
    {
        $menus= null;
        $sideMenu = null;

        $content = CM::getContentByKey($contentKey);

        $contentTranslation = CM::getContentVersion($contentKey, $version);

        if ($contentTranslation[0]->docs_main || $contentTranslation[0]->docs_side) {
            $files = $this->getFirstFiles($contentKey, 1);
        }

        $banners = $this->getFirstFiles($contentKey, 2);

        if ($contentTranslation[0]->slideshow) {
            $slideshow = $this->getFirstFiles($contentKey, 3);
        }

        if($content->type_id == 2){
            $news = $this->getLastNews();
        }elseif($content->type_id == 3) {
            $events = $this->getLastEvents();
        }

        // --> TODO : check TEST <--
        $pageContent = $contentTranslation[0];

        if (!empty($contentTranslation[0]->link || $contentTranslation[1]->link)) {
            if(!empty($contentTranslation[0]->link)){
                $iframe = $contentTranslation[0]->link;
            }elseif(!empty($contentTranslation[1]->link)){
                $iframe = $contentTranslation[1]->link;
            }
            return view('public.'.ONE::getEntityLayout().'.pages.content', compact('menus', 'pageContent', 'sideMenu', 'files', 'banners', 'slideshow', 'news', 'events', 'iframe', 'content', 'contentType'));
        }

        // TEMP FIX FOR PAGE IMG HEADERS
        $dom = new DOMDocument;
        try {
            $dom->loadHTML(html_entity_decode($contentTranslation[0]->content));
        } catch (\ErrorException $ex) {
            @$dom->loadHTML(html_entity_decode(htmlspecialchars($contentTranslation[0]->content)));
        }
        $img = $dom->getElementsByTagName('img');
        foreach ($img as $tag) {
            $src = $tag->getAttribute('src');
            $style = $tag->getAttribute('style');
            if (strpos($src, "https://empatia-dev.onesource.pt:5005/file/download/") == false && strpos($src, "process=download") !== false) {
                $src = "https://empatia-dev.onesource.pt:5005/file/download/" . $src;

                $tag->setAttribute('src', $src);
                if ($tag->getAttribute('width') == null && strpos($style, 'width') === false) {
                    $style = $style . "width:100%";
                    $tag->setAttribute('style', $style);
                }
            }
        }

        $aTags = $dom->getElementsByTagName('a');
        foreach ($aTags as $tag) {
            $src = $tag->getAttribute('href');
            if (strpos($src, "https://empatia-dev.onesource.pt:5005/file/download/") == false && strpos($src, "process=download") !== false) {
                $src = "https://empatia-dev.onesource.pt:5005/file/download/" . $src;

                $tag->setAttribute('href', $src);
            }
        }

        $html = $dom->saveHTML();

        return view('public.'.ONE::getEntityLayout().'.pages.content', compact('menus', 'pageContent', 'html', 'sideMenu', 'files', 'banners', 'slideshow', 'news', 'events', 'content'));


    }

    /**
     * Get a list of News.
     *
     * @return list of News.
     */
    public function getNewsList()
    {
        $news = CM::getContentNewsList();
        return $news;
    }

    /**
     * Get an array of News Ids.
     *
     * @return array of News Ids
     */
    public function getNewsIds()
    {
        return CM::getContentNewsId();
    }

    /**
     * Get a list of Present News.
     *
     * @return list of Present News.
     */
    public function getPresentNews()
    {
        return CM::getContentPresentNews();
    }

    /**
     * Get a list of Last 5 News.
     *
     * @return list of Last 5 News.
     */
    public function getLastNews()
    {
        return CM::getContentLastNews();
    }

    /**
     * Get an array of Events.
     *
     * @return array of Events.
     */
    public function getEventsIds()
    {
        return CM::getContentEventIds();
    }

    /**
     * Get a list of Events.
     *
     * @return list of Events.
     */
    public function getEventsList()
    {
        return CM::getEventsList();
    }

    /**
     * Get a list of Last 5 Events.
     *
     * @return list of Last 5 Events.
     */
    public function getLastEvents()
    {
        return CM::getContentLastEvents();
    }

    public function showNews($contentKey)
    {
        $menus = $this->getMainMenu();

        $informations = CM::getContent($contentKey);

        $informationsIds = $this->getNewsIds($informations->content_key);

        $previous = null;
        $next = null;

        if (array_search($contentKey, $informationsIds) > 0)
            $next = $informationsIds[array_search($contentKey, $informationsIds) - 1];

        if (array_search($contentKey, $informationsIds) < sizeof($informationsIds) - 1)
            $previous = $informationsIds[array_search($contentKey, $informationsIds) + 1];

        $title = trans('public.news');

        $link_name = trans('public.read_all');
        $link_page = URL::action('ContentsController@showNewsList', $informations->content_key);

        $sideType1 = 1;

        $sideTitle1 = "";
        $sideLink1 = "";
        $sideAction1 = "";
        $sideInf1 = "";

        $sideType2 = 1;

        $sideTitle2 = "<i class=\"fa fa-calendar\"></i> " . trans('public.events');
        $sideLink2 = trans('public.see_all');
        $sideAction2 = URL::action('ContentsController@showEventsList', $informations->content_key);
        $sideInf2 = $this->getLastEvents($informations->content_key);

        return view('public.'.ONE::getEntityLayout().'.pages.content', compact('menus', 'informations', 'title', 'previous', 'next', 'link_name', 'link_page', 'sideTitle1', 'sideInf1', 'sideLink1', 'sideAction1', 'sideType1', 'sideTitle2', 'sideInf2', 'sideLink2', 'sideAction2', 'sideType2'));
    }

    public function showEvent($contentKey)
    {
        $menus = $this->getMainMenu();

        $informations = CM::getContentByKey($contentKey);

        $informationsIds = $this->getEventsIds($informations->key);

        $previous = null;
        $next = null;

        if (array_search($id, $informationsIds) > 0)
            $next = $informationsIds[array_search($id, $informationsIds) - 1];

        if (array_search($id, $informationsIds) < sizeof($informationsIds) - 1)
            $previous = $informationsIds[array_search($id, $informationsIds) + 1];

        $title = trans('public.events');

        $link_name = trans('public.see_all');
        $link_page = URL::action('ContentsController@showEventsList', $informations->key);

        $sideType1 = 1;

        $sideTitle1 = "<i class=\"fa fa-newspaper-o\"></i> " . trans('public.news');
        $sideLink1 = trans('public.read_all');
        $sideAction1 = URL::action('ContentsController@showNewsList', $informations->key);
        $sideInf1 = $this->getLastNews($informations->key);

        $sideType2 = 2;

        $sideTitle2 = "";
        $sideLink2 = "";
        $sideAction2 = "";
        $sideInf2 = "";

        return view('public.pages.informations', compact('menus', 'informations', 'title', 'previous', 'next', 'link_name', 'link_page', 'sideTitle1', 'sideInf1', 'sideLink1', 'sideAction1', 'sideType1', 'sideTitle2', 'sideInf2', 'sideLink2', 'sideAction2', 'sideType2'));
    }

    public function showNewsList()
    {
        // Get pages list from Orchestrator
        $dataList = Orchestrator::getPageListByType("news");

        // Get CM page contents filtered with page keys
        $informations = CM::listContent($dataList);

        // $informations = $this->getNewsList();
        return view('public.'.ONE::getEntityLayout().'.pages.newsList', compact('informations'));
    }

    public function showEventsList()
    {
        // Get pages list from Orchestrator
        $dataList = Orchestrator::getPageListByType("events");

        // Get CM page contents filtered with page keys
        $informations = CM::listContent($dataList);

        return view('public.'.ONE::getEntityLayout().'.pages.eventsList', compact('informations'));
    }

    public function getTinyMCE()
    {
        return view('private._private.tinymce')->with('action', action('ContentsController@getTinyMCEView'));
    }

    public function getTinyMCEView($type = null)
    {
        $types[] = trans('contents.files');

        $uploadToken = Files::getUploadKey();
        $contentTypes = CM::getAllContentTypes();

        foreach($contentTypes as $contentType) {

            if( !empty($contentType->translations) ) {
                $types[$contentType->id] = $contentType->translations[0]->title ;
            } else if( !empty($contentType->title) ){
                $types[$contentType->id] = $contentType->title;

            } else {
                $types[$contentType->id] = $contentType->id;
            }
        }

        return view('private._private.tinymce-content', compact('uploadToken', 'types'));
    }

    public function tinyMCETable(Request $request)
    {
        if ($request['type'] == 0) {
            if ($request['tinymce_type'] === 'file') {

                $response = Files::getListFiles()->files;
                $files = Collection::make($response);

                return Datatables::of($files)
                    ->addColumn('title', function ($files) {
                        return $files->name;
                    })
                    ->addColumn('action', function ($files) {
                        return '<a class="btn btn-flat btn-info btn-xs" href="javascript:addFileBrowserLink(\'' . 'files/' . $files->id .'/'. $files->code . '\')"><i class="fa fa-external-link"></i></a>';
                    })
                    ->make(true);


            } elseif ($request['tinymce_type'] === 'image') {
                $response = Files::getListImagesFiles();
                $files = Collection::make(json_decode($response->content())->files);

                return Datatables::of($files)
                    ->addColumn('title', function ($files) {
                        return $files->name;
                    })
                    ->addColumn('action', function ($files) {
                        return '<a class="btn btn-flat btn-info btn-xs" href="javascript:addFileBrowserLink(\'' . 'files/' . $files->id .'/'. $files->code .'/'. 1 . '\')"><i class="fa fa-external-link"></i></a>';
                    })
                    ->make(true);
            }
        } elseif ($request['type'] > 0) {

            $response = CM::listContents($request['type']);
            $contents = Collection::make(json_decode($response->content())->data);

            return Datatables::of($contents)
                ->addColumn('title', function ($contents) {
                    return $contents->translations[0]->title;
                })
                ->addColumn('action', function ($contents) {
                    return '<a class="btn btn-flat btn-info btn-xs" href="javascript:addFileBrowserLink(\'' . URL::action('ContentsController@previewPage', $contents->content_key, false) . '\')"><i class="fa fa-external-link"></i></a>';
                })
                ->make(true);

        }
    }
}
