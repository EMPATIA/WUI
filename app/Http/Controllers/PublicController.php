<?php

namespace App\Http\Controllers;

use App\ComModules\CM;
use App\ComModules\Files;
use App\ComModules\Orchestrator;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use App\One\One;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Collection;
use Session;

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
        try {
            $response = Orchestrator::getSiteUseTerm();

            if(isset($response)){
                $useTerms = html_entity_decode($response->content);
            }else{
                $useTerms = $response;
            }

            try {
                $homeContent = CM::getContentByKey($this->contentKey);
                // Banners
                $data['banners'] = $this->getFirstFiles($this->contentKey, 2);
            } catch (Exception $e) {
                $homeContent = "";
            }

            try {
                $siteContent = CM::getContentByKey($this->siteContentKey);
            } catch (Exception $e) {
                $siteContent = "";
            }


            // Get news list
            try {
                $dataNews = Orchestrator::getPageListByType("news", 5);
                $lastNews = [];
                if (!empty($dataNews)) {
                    $lastNews = CM::getVariousContents($dataNews);
                }
            } catch (Exception $e) {
                $lastNews = [];
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
            try {
                $dataEvents = Orchestrator::getPageListByType("events");
                $lastEvents = [];
                $lastEventsAll = [];
                if (!empty($dataEvents)) {
                    $lastEvents = CM::getVariousContents($dataEvents);
                    $lastEventsAll = $lastEvents; //TODO: Temporary solution - last events collect should be moved and handled in the views
                    $lastEvents = collect($lastEvents)->where("start_date", ">=", Carbon::today()->toDateString())->sortBy("start_date")->take(5)->toArray();
                }
            } catch (Exception $e) {
                $lastEvents = [];
            }

            //TODO:only for empatia
            $stream = Orchestrator::getEmpatiaStream();
            
            $homePageConfigurations =[];



            $goalIcon = ['inclusion', 'integration', 'deliberativeQuality', 'replicationAndAdaptation', 'efficiency', 'ehnancedEvaluation', 'accountability', 'marketability'];

            if($stream) {
                $data['stream'] = $stream;
            }
            $data['goalIcon'] = $goalIcon;
            $data['homePageConfigurations'] = $homePageConfigurations;
            $data['lastNews'] = $lastNews;
            $data['newsImage'] = $newsImage;
            $data['lastEvents'] = $lastEvents;
            $data['lastEventsAll'] = $lastEventsAll; // TODO: Temporary solution - to remove when lastEvents contains all events
            $data['homeContent'] = $homeContent;
            $data['siteContent'] = $siteContent;
            $data['conf_analytics'] = isset($siteConf) ? $siteConf : null;
            $data['useTerms'] = $useTerms;

            return view('public.'.ONE::getEntityLayout().'.home.index', $data);
        }
        catch(Exception $e) {
            return view('public.'.ONE::getEntityLayout().'.home.index');
        }
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

}
