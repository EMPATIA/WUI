<?php

namespace App\Http\Controllers;

use App\ComModules\CM;
use App\ComModules\Files;
use App\ComModules\Orchestrator;
use App\One\One;
use Cache;
use Carbon\Carbon;
use DOMDocument;
use Exception;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Pagination\Paginator;
use Session;

class PublicContentsController extends Controller
{

    private $typeViews = ['pilots'];

    public function show(Request $request, $id)
    {

        $content = CM::getContentByKey($id);
        if ($content->published) {

            // Files //if ($content->docs_main || $content->docs_side)
            $files = $this->getFirstFiles($id, 1);


            // Banners
            $banners = $this->getFirstFiles($id, 2);


            // Slideshow //if ($content->slideshow)
            $slideshow = $this->getFirstFiles($id, 3);

            // images
            $images = $this->getFirstFiles($id, 4);

            if ($content->type_id == 2) {
                $news = $this->getLastNews();
            } elseif ($content->type_id == 3) {
                $events = $this->getLastEvents();
            }

            // --> TODO : check TEST <--
            $pageContent = $content;

            if (!empty($content->link || $content->link)) {
                if (!empty($content->link)) {
                    $iframe = $content->link;
                }
            }

            $html = html_entity_decode($content->content);

            if (isset($request->type)) {
                if (in_array($request->type, $this->typeViews) && $request->key) {
                    /*$homePageConfigData = Orchestrator::getHomePageConfigurationGroup($request->key);
                    $homePageConfig = [];
                    foreach ($homePageConfigData->home_page_configurations as $configValues) {
                        $homePageConfig[$configValues->home_page_type->code] = $configValues->value;
                    }*/
                    return view('public.' . ONE::getEntityLayout() . '.pages.' . $request->type . '.content', compact('menus', 'contentsImage', 'pageContent', 'html', 'sideMenu', 'images', 'files', 'banners', 'slideshow', 'news', 'events', 'content', 'iframe', 'homePageConfig'));
                }
            }

            // Open Graph Tags - facebook
            $openGraphTags["title"] = !empty( $content->title) ? $content->title : "";
            $openGraphTags["description"] = !empty($content->content) ? $content->content : "";
            $openGraphTags["image"] = (!empty($images[0]->id) && $images[0]->code ) ? ["file_id" => $images[0]->id, "file_code" => $images[0]->code] : [];

            return view('public.' . ONE::getEntityLayout() . '.pages.pageContent', compact('menus','contentsImage', 'pageContent', 'html', 'sideMenu', 'images', 'files', 'banners', 'slideshow', 'news', 'events', 'content', 'iframe' , 'openGraphTags'));
        }
        return view('public.' . ONE::getEntityLayout() . '.pages.noPublishContent');

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
        $news = CM::getContentNewsIds();

        return $news;
    }

    /**
     * Get a list of Present News.
     *
     * @return list of Present News.
     */
    public function getPresentNews()
    {
        $news = CM::getContentPresentNews();

        return $news;
    }

    /**
     * Get a list of Last 5 News.
     *
     * @return list of Last 5 News.
     */
    public static function getLastNews()
    {
        // Get news list
        $dataNews = Orchestrator::getPageListByType("news",3);
        $lastNews = [];
        if(!empty($dataNews)) {
            $lastNews = CM::getVariousContents($dataNews);
        }

        return $lastNews;
    }

    /**
     * Get an array of Events.
     *
     * @return array of Events.
     */
    public function getEventsIds()
    {
        $events = CM::getContentEventIds();

        return $events;
    }

    /**
     * Get a list of Events.
     *
     * @return list of Events.
     */
    public function getEventsList()
    {
        $events = CM::getEventsList();

        return $events;
    }

    /**
     * Get a list of Last 5 Events.
     *
     * @return list of Last 5 Events.
     */
    public function getLastEvents()
    {
        $response = CM::getContentLastEvents();

        $events = !empty($response) ? $response : [];
        return $events;
    }


    public function showContentsList(Request $request)
    {
        $contentType = $request->type;

        if(empty($contentType)){
            $contentType = 'news';
        }

        $contentsToShow = 6;

        if(!empty($request->page)){
            if (Cache::has(session()->getId().'_contents_list')){
                $allContents = Cache::get(session()->getId().'_contents_list')['contents'];
                $contentsToDisplay = array_slice ($allContents, ($contentsToShow * ($request->page-1) )) ;
                $contentsPagination = new Paginator($contentsToDisplay, $contentsToShow, $request->page);
                // Prepare data to send to the view

                $data=[];
                $data['contents'] = $allContents;

                $data['contentsImage'] = Cache::get(session()->getId().'_contents_list')['contentsImage'];
                $data['contentsPagination'] = $contentsPagination;

                Cache::put(session()->getId().'_news_list', $data, 30);
                return view('public.'.ONE::getEntityLayout().'.pages.'.$contentType.'ListContent', $data);
            }
        }

        // Number of news to list for each request
        $n = 5;

        // Get pages list from Orchestrator
        $dataList = Orchestrator::getPageListByType($contentType);

        // Get CM page contents filtered with page keys
        $contentsList = collect(CM::listContent($dataList));

        //order by publish_date (for news) or start_date (for events)
        if ($contentType == "news") {
            $contentsList = $contentsList->sortByDesc('publish_date');
        } else if ($contentType == "events"){
            $contentsList = $contentsList->sortByDesc('start_date');
        }

        // Filter data infinite pagination
        $contentsImage = [];
        $contents =[];

        foreach ($contentsList as $content){
            $contents[] = $content;

            foreach ($content->content_files as $file){
                if($file->type_id == 4){
                    $file = Files::getFile($file->file_id);
                    $contentsImage[$content->content_key] = ['id' => $file->id,'code' => $file->code];
                    break;
                }
            }
        }

        $contentsPagination = new Paginator($contents, $contentsToShow, $request->page);

        $data=[];
        $data['contents'] = $contents;
        $data['contentsImage'] = $contentsImage;
        $data['contentsPagination'] = $contentsPagination;

        Cache::put(session()->getId().'_contents_list', $data, 30);
        //return view('public.'.ONE::getEntityLayout().'.pages.newsList', $data);
        return view('public.'.ONE::getEntityLayout().'.pages.'.$contentType.'List', $data);
    }


    public function showNewsList(Request $request,$contentKey = "")
    {
        // Number of news to list for each request
        $n = 5;

        // Get pages list from Orchestrator
        $dataList = Orchestrator::getPageListByType("news");

        // Get CM page contents filtered with page keys
        $contents = CM::listContent($dataList);

        // Filter data infinite pagination
        $i = 0;
        $started = false;
        $newsImage = [];
        $informations =[];

        foreach ($contents as $content){
            $informations[] = $content;

            foreach ($content->content_files as $file){
                if($file->type_id == 4){
                    $file = Files::getFile($file->file_id);
                    $newsImage[$content->content_key] = ['id' => $file->id,'code' => $file->code];
                    break;
                }
            }
        }

        // Load view or content
        if(!empty($contentKey)){
            return view('public.'.ONE::getEntityLayout().'.pages.newsList', compact('informations','next','contentKey','newsImage'))->renderSections()['content'];
        } else {
            return view('public.'.ONE::getEntityLayout().'.pages.newsList', compact('informations','next','newsImage'));
        }

    }

    public function showEventsList()
    {
        // Get pages list from Orchestrator
        $dataList = Orchestrator::getPageListByType("events");

        // Get CM page contents filtered with page keys
        $informations = CM::listContent($dataList);

        return view('public.'.ONE::getEntityLayout().'.pages.eventsList', compact('informations'));
    }


    public function previewPage(Request $request, $contentKey, $version)
    {
        $sideMenu = null;
        $content = CM::getContentByKey($contentKey);

        $contentVersion  = CM::getContentVersion($contentKey, $version);
        if (isset($request->langCode)) {
            foreach ($contentVersion as $contentTranslation) {
                if ($contentTranslation->language_code == $request->langCode) {
                    $pageContent = $contentTranslation;
                    $pageContent->start_date = $content->start_date;
                    break;
                }
            }
        }else {
            foreach ($contentVersion as $contentTranslation) {
                if ($contentTranslation->language_code == Session::get('LANG_CODE')) {
                    $pageContent = $contentTranslation;
                    $pageContent->start_date = $content->start_date;
                    break;
                }
            }
        }
        if (isset($pageContent)){
            $files = $this->getFirstFiles($contentKey, 1);
            $banners = $this->getFirstFiles($contentKey, 2);
            $slideshow = $this->getFirstFiles($contentKey, 3);
            $images = $this->getFirstFiles($contentKey, 4);

            if ($content->type_id == 2) {
                $news = $this->getLastNews();
            } elseif ($content->type_id == 3) {
                $events = $this->getLastEvents();
            }

            $html = html_entity_decode($pageContent->content);
            return view('public.' . ONE::getEntityLayout() . '.pages.pageContent', compact('menus', 'pageContent', 'html', 'sideMenu', 'images', 'files', 'banners', 'slideshow', 'news', 'events', 'content'));

        }

    }


    public function showNewsListByType(Request $request)
    {

        $nNews = $request->get('objects_number');
        $contentTypeType = $request->get('content_type_type');

        // Get news list
        $dataNews = Orchestrator::getPageListByType("news",5);
        $lastNews = [];
        if(!empty($dataNews)) {
            $lastNews = CM::getContentsByKeyWithType($dataNews, $contentTypeType);
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
        $html = '';

        if (empty($lastNews) || count($lastNews)== 0){

            $html .= '<div class="row">';
            $html .= '<div class="col-sm-12 text-center">';
            $html .= '<div class="otherNE-button">';
            $html .= trans("cbs.there_are_no_news");
            $html .= '</div>';
            $html .= '</div>';
            $html .= '</div>';

        }else{

            foreach ($lastNews as $i => $news) {
                if ($i <= $nNews - 1){

                    $html .= '<div class="col-md-6 col-xs-12">';
                    $html .= '<a href="'. action('PublicContentsController@show', $news->content_key) .'">';
                    $html .= '<div class="row news-box-div">';
                    if (isset($news->content_type_type)) {

                        $html .= '<div class="col-xs-12 news-category news-regular-type" style="background:';
                        $html .= $news->content_type_type->color ?? '';
                        $html .= ';">';
                        $html .= $news->content_type_type->name ?? '';
                        $html .= '</div>';
                    }
                    $html .= '<div class="col-xs-12 news-inner-img-div" style="background-image:url(\' ';
                    $html .= isset($newsImage[$news->content_key]) ? action('FilesController@download', ['id'=>$newsImage[$news->content_key]['id'],'code'=>$newsImage[$news->content_key]['code'],1] ) : url('/images/empatia/default_img_contents.jpg');
                    $html .= ' \')">';
                    $html .= '</div>';
                    $html .= '<div class="col-md-12 col-xs-12">';
                    $html .= '<div class="row">';
                    if (isset($news->publish_date)) {
                        $html .= '<div class="col-xs-12 new-date-box">';
                        $html .= $news->publish_date;
                        $html .= '</div>';
                    }
                    $html .= '<div class="col-xs-12 news-title-box">';
                    $html .= $news->title;
                    $html .= '</div>';
                    $html .= '</div>';
                    $html .= '</div>';
                    $html .= '</div>';
                    $html .= '</a>';
                    $html .= '</div>';
                }
            }
        }

        $data = [];
        $data['success'] = true;
        $data['html'] = $html;

        return $data;
    }


    public function showEventsListByType(Request $request)
    {

        $nEvents = $request->get('objects_number');

        // Get events list
        $dataEvents = Orchestrator::getPageListByType("events",5);
        $lastEvents =[];
        if(!empty($dataEvents)) {
                $lastEvents = CM::getVariousContents($dataEvents);

        }
        $html = '';

        if (empty($lastEvents) || count($lastEvents)== 0){

            $html .= '<div class="row">';
            $html .= '<div class="col-sm-12 text-center">';
            $html .= '<div class="otherNE-button">';
            $html .= trans("cbs.there_are_no_events");
            $html .= '</div>';
            $html .= '</div>';
            $html .= '</div>';

        }else{
            foreach ($lastEvents as $i => $event) {
                if ($i <= $nEvents - 1){
                    $html .= '<div class="row">';
                    if (isset($event->start_date)) {
                        $html .= '<div class="col-xs-2 text-center events-clock">';
                        $html .= '<div class="events-clock-day">';
                        $html .= Carbon::parse($event->start_date)->formatLocalized("%e");
                        $html .= '</div>';
                        $html .= '<div class="events-clock-month">';
                        $html .= Carbon::parse($event->start_date)->formatLocalized("%b");
                        $html .= '</div>';
                        $html .= '</div>';
                    }
                    $html .= '<div class="col-xs-10">';
                    $html .= '<div class="row">';
                    $html .= '<div class="col-xs-12 new-title-box">';
                    $html .= '<a href="';
                    $html .= action('PublicContentsController@show', $event->content_key);
                    $html .= '">';
                    $html .= $event->title;
                    $html .= '</a>';
                    $html .= '</div>';
                    $html .= '<div class="col-xs-12 new-content-box">';
                    $html .= $event->summary;
                    $html .= '</div>';
                    $html .= '</div>';
                    $html .= '</div>';
                    $html .= '</div>';
                    if ($i <= $nEvents - 2) {
                        $html .= '<div class="box-separator-dark">&nbsp;</div>';
                    }
                }
            }
        }

        $data = [];
        $data['success'] = true;
        $data['html'] = $html;

        return $data;
    }


}
