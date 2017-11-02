<?php

namespace App\ComModules;

use App\One\One;
use Exception;



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

    public static function getTrackingDataByTimeFilter($timeFilter) {
        $response = ONE::get(
            [
                "component" => "logs",
                "api" => "TrackingController",
                "method" => "getTrackingDataByTimeFilter",
                "params" => ['timeFilter'=> $timeFilter]

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
                "url" => "https://".$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"]
            ]
        ]);

        return $response;
    }
}