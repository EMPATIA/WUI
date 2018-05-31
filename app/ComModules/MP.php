<?php

namespace App\ComModules;

use App\One\One;
use Exception;

class MP {


    public static function getMp($mpKey)
    {
        $response = ONE::get([
            'component' => 'mp',
            'api' => 'mp',
            'attribute'    => $mpKey
        ]);

        if($response->statusCode()!= 200){
            throw new Exception(trans("comModulesMP.error_getting_mp"));
        }
        return $response->json();
    }


    public static function deleteMP($mpKey)
    {
        $response = ONE::delete([
            'component' => 'mp',
            'api' => 'mp',
            'attribute' => $mpKey,
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesMP.error_delete_mp"));
        }
        return $response;
    }


    public static function setNewMP($diagramCode,$translations,$parentsArray)
    {
        $response = ONE::post([
            'component' => 'mp',
            'api' => 'mp',
            'params' => [
                'diagram_code' => $diagramCode,
                'translations' => $translations,
                'operators'     => $parentsArray
            ]
        ]);
        if($response->statusCode()!= 201){
            throw new Exception(trans("comModulesMP.error_store_mp"));
        }
        return $response->json();
    }

    public static function updateMP($mpKey,$diagramCode,$translations,$parentsArray)
    {
        $response = ONE::put([
            'component' => 'mp',
            'api' => 'mp',
            'attribute' => $mpKey,
            'params' => [
                'diagram_code' => $diagramCode,
                'translations' => $translations,
                'operators'     => $parentsArray
            ]
        ]);
        if($response->statusCode()!= 200){
            throw new Exception(trans("comModulesMP.error_update_mp"));
        }
        return $response->json();
    }

    public static function getOperator($operatorKey)
    {
        $response = ONE::get([
            'component' => 'mp',
            'api' => 'operator',
            'attribute'  => $operatorKey
        ]);
        if($response->statusCode()!= 200){
            throw new Exception(trans("comModulesMP.error_getting_operator"));
        }
        return $response->json();
    }

    public static function updateOperator($operatorKey, $cbKey)
    {

        $response = ONE::put([
            'component' => 'mp',
            'api'       => 'operator',
            'attribute' => $operatorKey,
            'params'    => [
                'component_key'  => $cbKey,
            ]
        ]);
        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesMP.error_in_update_operator"));
        }
        return $response->json();

    }

    public static function getMps()
    {
        $response = ONE::get([
            'component' => 'mp',
            'api' => 'mp',
            'method'    => 'list'
        ]);

        if($response->statusCode()!= 200){
            throw new Exception(trans("comModulesMP.error_getting_mps_list"));
        }
        return $response->json()->data;

    }

    public static function getOperatorTypes()
    {
        $response = ONE::get([
            'component' => 'mp',
            'api' => 'operatorType',
            'method'    => 'list'
        ]);

        if($response->statusCode()!= 200){
            throw new Exception(trans("comModulesMP.error_getting_mp_types_list"));
        }
        return $response->json()->data;
    }


    public static function updateMpState($mpKey)
    {
        $response = ONE::post([
            'component' => 'mp',
            'api' => 'mp',
            'method' => 'updateState',
            'api_attribute' => $mpKey,
            'params' => [
                'mp_finished' => true
            ]
        ]);
        if($response->statusCode()!= 200){
            throw new Exception(trans("comModulesMP.error_update_mp_state"));
        }
        return $response->json();
    }

    public static function updateMpDates($mpKey, $startDate, $endDate)
    {
        $response = ONE::post([
            'component' => 'mp',
            'api' => 'mp',
            'method' => 'updateDates',
            'api_attribute' => $mpKey,
            'params' => [
                'start_date' => $startDate,
                'end_date' => $endDate
            ]
        ]);
        if($response->statusCode()!= 200){
            throw new Exception(trans("comModulesMP.error_update_mp_dates"));
        }
        return $response->json();
    }


}
