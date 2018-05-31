<?php

namespace App\ComModules;

use App\Jobs\Access;
use App\One\One;
use Exception;
use Session;


class LogsRequest {

    public static function getAllServers(){

        $response = one::get(
            [

                "component" => "logs",
                "api" => "PerformanceController",
                "method" => "getAllServers"

            ]
        );

        if ($response->statusCode() != 200) {
            throw new Exception("Failed to get all servers in the DB.");
        }
        return json_decode($response->content(), true);
    }

    public static function getAllComponents(){

        $response = one::get(
            [

                "component" => "logs",
                "api" => "PerformanceController",
                "method" => "getAllComponents"
            ]
        );
        //dd($response);

        if ($response->statusCode() != 200) {
            throw new Exception("Failed to get all components in the DB.");
        }

        return json_decode($response->content(), true);
    }


    public static function getLastTrakingKey(){

        $response = one::get(
            [
                "component" => "logs",
                "api" => "TrackingController",
                "method" => "getLastTrackingKey"
            ]
        );

    }

    public static function setDatatoDB($cpu, $memoryUsed, $ioData) {
        $response = One::Post([
            'component' => 'logs',
            'api' => 'PerformanceController',
            'method' => 'saveDataToDB',
            'params' => ["cpu" => $cpu,
                "memory" => $memoryUsed,
                "io"=> $ioData]

        ]);
    }

    public static function setPerformanceFromDBForBarsGraph($serverIp, $component,$startRange, $endRange) {
        $response = One::Post([
            "component" => "logs",
            "api" => "PerformanceController",
            "method" => "sendPerformanceFromDBForBarsGraph",
            "params" => [
                'startRange' => $startRange,
                'endRange' => $endRange,
                'serverIp'=>$serverIp,
                'component'=>$component
            ]
        ]);

        return $response;
    }

    public static function setPerformanceFromDBByComponentServer($name,$timeFilter, $serverIp, $component,$startRange, $endRange) {
        $response = One::Post(
            [

                "component" => "logs",
                "api" => "PerformanceController",
                "method" => "sendPerformanceFromDBByComponentServer",
                "params" => ['name'=>$name,
                    'timeFilter'=> $timeFilter,
                    'startRange' => $startRange,
                    'endRange' => $endRange,
                    'serverIp'=>$serverIp,
                    'component'=>$component]
            ]
        );

        return $response;
    }

    public static function getTrackingData($id) {
        $response = ONE::get([

            'component' => 'logs',
            'api'       => 'TrackingController',
            'method'    => 'getTrackingData',
            'params' => ['id' => $id]
        ]);

        return $response->json();
    }

    public static function getTrackingRequestsData($id) {
        $response = ONE::get([

            'component' => 'logs',
            'api'       => 'TrackingController',
            'method'    => 'getTrackingRequestsData',
            'params' => ['tracking_id' => $id]
        ]);

        return $response->json();
    }

    public static function getTrackingDataByTimeFilter($request, $timeFilter) {

        $response = ONE::get(
            [
                "component" => "logs",
                "api" => "TrackingController",
                "method" => "getTrackingDataByTimeFilter",
                "params" => ['timeFilter'=> $timeFilter, 'tableData' => One::tableData($request)],

            ]
        );

        return $response->json();
    }

    public static function updateMessageException($e) {
        $response = One::Post([
            'component' => 'logs',
            'api' => 'TrackingController',
            'method' => 'updateMessageException',
            'params' => ["message" => $e->getMessage()]
        ]);

        return $response->json();
    }

    public static function getLastLog() {
        $response = One::get([

            'component' => 'logs',
            'api' => 'TrackingController',
            'method' => 'getLastLog',
        ]);

        return $response->json();
    }

    public static function saveTrackingDataToDB($lastLog, $method, $e) {
        $response = One::Post([

            'component' => 'logs',
            'api' => 'TrackingController',
            'method' => 'saveTrackingDataToDB',
            'params' => ["is_logged" => $lastLog->json()->is_logged,
                "auth_token" => $lastLog->json()->auth_token,
                "user_key" => $lastLog->json()->user_key,
                "ip" => $lastLog->json()->ip,
                "url" => $lastLog->json()->url,
                "site_key" => $lastLog->json()->site_key,
                "method" => $method,
                "session_id" => $lastLog->json()->session_id,
                "table_key" => $lastLog->json()->table_key,
                "time_start" => $lastLog->json()->time_start,
                "time_end" => $lastLog->json()->time_end,
                "message" => $e->getMessage()]
        ]);

        return $response->json();
    }

    public static function setTracking($method, $parameters) {
        $response = ONE::post([
            'component' => 'logs',
            'api' => 'TrackingController',
            'method' => $method,
            'params' => $parameters
        ]);

        return $response->json();
    }

    public static function sendLog($type,$message) {
        $response = ONE::post([
            'component' => 'logs',
            'method' => 'log',
            'params' => [
                "type" => $type,
                "component" => "1",
                "ip" => $_SERVER["REMOTE_ADDR"] ?? "-1",
                "message" => $message,
                "url" => "https://".(!empty($_SERVER["HTTP_HOST"]) ? $_SERVER["HTTP_HOST"] : "unknown/").(!empty($_SERVER["REQUEST_URI"]) ? $_SERVER["REQUEST_URI"] : "unknown")
            ]
        ]);

        return $response;
    }

    /**
     * Display a listing of the accesses - logs.
     * @return \Illuminate\Http\Response
     * @throws Exception
     */
    public static function getAccesses($request)
    {
        $response = ONE::get([
            'component' => 'logs',
            'api' => 'access',
            'method' => 'list',
            'params' => [
                'tableData' => One::tableData($request),
                'start_date' => $request->start_date ?? null,
                'end_date' => $request->end_date ?? null,
                'ip' => $request->filters_static['ip'] ?? null,
                'email' => $request->filters_static['email'] ?? null,
                'sites' => $request->filters_static['sites'] ?? null,
                'cbs' => $request->filters_static['cbs'] ?? null,
                'actions' => $request->select_actions ?? null,
                'result' => $request->filters_static['result'] ?? null,
            ]
        ]);
        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesLogs.failedToGetAccesses"));
        }

        return $response->json()->data;
    }

    public static function setAccess($currentAction, $currentResult, $topicKey = null, $contentKey = null, $cbKey = null, $postKey = null, $qKey = null, $vote_key = null, $error = null, $details = null, $userKey=null)
    {
        $proxy_ips = explode(',', env('PROXY_IPS'));
        $proxy_ips = !empty($proxy_ips[0]) ? $proxy_ips : null ;

        $ip = $_SERVER["REMOTE_ADDR"] ?? "-1";

        if (!is_null($proxy_ips) && in_array($ip, $proxy_ips) && filter_var($_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP)){
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }

        $url =  "https://".(!empty($_SERVER["HTTP_HOST"]) ? $_SERVER["HTTP_HOST"] : "unknown/").(!empty($_SERVER["REQUEST_URI"]) ? $_SERVER["REQUEST_URI"] : "unknown");
        $sessionId = Session::getId();
        $action = $currentAction;
        $result = $currentResult;
        $user_Key = Session::has('user') ? Session::get('user')->user_key : $userKey;
        $topic_key = $topicKey;

        $content_key = $contentKey;
        $cb_key = $cbKey;
        $post_key = $postKey;
        $q_key = $qKey;

        $entity_key = Session::get('X-ENTITY-KEY');
        $site_key = Session::get('X-SITE-KEY');

        dispatch(new Access( $url, $ip, $sessionId,$action, $result,$user_Key,$topic_key, $details, $error, $content_key, $cb_key, $post_key, $q_key, $vote_key, $entity_key, $site_key ));

    }

    /**
     * Display a actions array.
     * @return \Illuminate\Http\Response
     * @throws Exception
     */
    public static function getActions()
    {
        $response = ONE::get([
            'component' => 'logs',
            'api' => 'access',
            'method' => 'action',
        ]);
        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesLogs.failedToGetAllActions"));
        }
        return $response->json()->actions;
    }

    /**
     * @return array with all cbs
     * @return \Illuminate\Http\Response
     * @throws Exception
     */
    public static function getCbs()
    {
        $response = ONE::get([
            'component' => 'logs',
            'api' => 'access',
            'method' => 'cb',
        ]);
        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesLogs.failedToGetAllCbs"));
        }
        return $response->json()->cbs;
    }

    /**
     * @return array with all accesses
     * @return \Illuminate\Http\Response
     * @throws Exception
     */
    public static function getAnalytics($entity_key = null,$dayStart = null, $dayEnd = null){

        $response = ONE::get([
            'component' => 'logs',
            'api'       => 'analytics',
            'method'    => 'list',
            'params'  => [
                "entity_key" => $entity_key,
                "dayStart"   => $dayStart,
                "dayEnd"     => $dayEnd,
            ]
        ]);
        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesLogs.failedToGetAnalytics"));
        }
        return $response->json();
    }
}