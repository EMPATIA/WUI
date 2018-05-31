<?php

namespace App\Http\Controllers;

use App\ComModules\EMPATIA;
use App\ComModules\Vote;
use Carbon\Carbon;
use Exception;
use function GuzzleHttp\Promise\all;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Redis;
use App\One\ONE;

class UserAnalysisController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(){
        try {
            $data['title'] = trans('private.user_analysis.list_title');

            // Get data
            $dataAnalyses = UserAnalysisController::getAnalysisData();

            // Store data
            // UserAnalysisController::storeAccessData( $dataAnalyses );

            // Prepare data for the view
            $allData = $dataAnalyses["allData"];
            $accessData = $dataAnalyses["accessData"];

            $data['analysisData'] = $allData->analysis_data;
            $data['totalUsers'] = $allData->total_users;
            $data['totalUsersByEntity'] = $allData->total_users_by_entity;
            $data['totalUsersBySite'] = $allData->total_users_by_site;
            $data['total_page_access'] = count($accessData);

            return view('private.userAnalysis.index', $data);
        } catch (Exception $e) {
            return redirect()->back()->withErrors([ trans('UserAnalysis.index') => $e->getMessage()]);
        }
    }


    public static function getAnalysisData(){
        // Get data
        $redis = Redis::connection();
        $redisData = $redis->keys('logged*');

        $accessData = [];
        $allData = [];

        if (!empty($redisData)) {
            $loggedData = $redis->mget($redisData);
            $redisAccessData = $redis->keys('pageAccess*');
            $accessData = $redis->mget($redisAccessData);

            // Get user analysis from EMPATIA
            $allData = EMPATIA::getUserAnalysis($loggedData);
        }

        return ["accessData" => $accessData, "allData" => $allData];
    }

    public static function storeAccessData($dataAnalyses)
    {
        // Prepare data
        $allData = $dataAnalyses["allData"];
        $accessData = $dataAnalyses["accessData"];

        // Total users by Entity [counting]
        $dataToStore['total_users'] = $allData->total_users;
        $dataToStore['total_page_access'] = count($accessData);
        foreach(collect($allData->total_users_by_entity)->toArray() as $key => $users){
            $dataToStore['entities'][$key]['total_users'] = isset($users) ? count($users) : 0;
        }

        // Total page access by Entity [counting]
        foreach($accessData as $accessDataItem){
            $accessDataJson = json_decode($accessDataItem);
            $entityKey = $accessDataJson->entity_key;
            if( !empty($entityKey) )
                $dataToStore['entities'][$entityKey]['total_page_access'] = (isset($dataToStore['entities'][$entityKey]['total_page_access'])) ? $dataToStore['entities'][$entityKey]['total_page_access']+1 : 1;
        }

        // Store data
        EMPATIA::storeUserAnalytics($dataToStore);
    }

    public static function getAndStoreAccessData() {
        $dataAnalyses = self::getAnalysisData();
        self::storeAccessData( $dataAnalyses );
    }


    public static function getAnalysisStats(Request $request){
        try {
            $params = [];
            $params['start_date'] = $request->get("start_date");
            $params['end_date'] =  $request->get("end_date");
            $analysisStats = EMPATIA::getUserAnalysisStats($params);

            // Get all entity_keys
            $entitiesKeysArray = [];
            foreach(!empty($analysisStats->data) ? $analysisStats->data : []  as $key => $item){
                foreach(isset($item->entities) ? $item->entities :[] as $entityKey => $entityItem){
                    if(!in_array($entityKey, $entitiesKeysArray))
                        $entitiesKeysArray[] = $entityKey;
                }
            }

            $entities = [];
            $dataTotalUsers = [];
            $dataTotalPageAccess = [];
            $dataEntitiesTotalUsers = [];
            $dataEntitiesTotalPageAccess = [];
            foreach(!empty($analysisStats->data) ? $analysisStats->data : []  as $key => $item){
                $dataTotalUsers[] = collect(["timelapse" => $key, 'name' => 'Total', 'value' => !empty($item->total_users) ? $item->total_users : 0 ])->toJson();
                $dataTotalPageAccess[] = collect(["timelapse" => $key, 'name' => 'Total', 'value' => !empty($item->total_page_access) ? $item->total_page_access : 0])->toJson();
                // Entities
                $tmpEntitiesTotalUsers = [];
                $tmpEntitiesTotalPageAccess = [];
                for($i = 0; $i < count($entitiesKeysArray); $i++){
                    // initial values = 0
                    $tmpEntitiesTotalUsers[$entitiesKeysArray[$i]][$key]= collect(["timelapse" => $key, 'name' => 'Total', 'value' => 0])->toJson();
                    $tmpEntitiesTotalPageAccess[$entitiesKeysArray[$i]][$key] = collect(["timelapse" => $key, 'name' => 'Total', 'value' => 0])->toJson();
                }
                foreach(isset($item->entities) ? $item->entities :[] as $entityKey => $entityItem){
                    $entities[$entityKey] = !empty($entityItem->name) ? $entityItem->name : "";
                    $tmpEntitiesTotalUsers[$entityKey][$key] = collect(["timelapse" => $key, 'name' => 'Total', 'value' => !empty($entityItem->total_users) ? $entityItem->total_users  : 0])->toJson();
                    $tmpEntitiesTotalPageAccess[$entityKey][$key] =  collect(["timelapse" => $key, 'name' => 'Total', 'value' =>!empty($entityItem->total_page_access) ? $entityItem->total_page_access : 0 ])->toJson();
                }

                for($i = 0; $i < count($entitiesKeysArray); $i++) {
                    $dataEntitiesTotalUsers[$entityKey][] = $tmpEntitiesTotalUsers[$entitiesKeysArray[$i]][$key];
                    $dataEntitiesTotalPageAccess[$entityKey][] = $tmpEntitiesTotalPageAccess[$entitiesKeysArray[$i]][$key];
                }
            }

            // Return data to show in chart
            return ["TotalUsers" => $dataTotalUsers,
                    "TotalPageAccess" => $dataTotalPageAccess,
                    "dataEntitiesTotalUsers" => $dataEntitiesTotalUsers,
                    "dataEntitiesTotalPageAccess" => $dataEntitiesTotalPageAccess,
                    "entities" => $entities];

        } catch (Exception $e) {
            echo $e->getMessage();
            // return redirect()->back()->withErrors([ trans('UserAnalysis.index') => $e->getMessage()]);
        }
    }
}