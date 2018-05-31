<?php

namespace App\ComModules;

use App\One\One;
use Illuminate\Support\Collection;


use Illuminate\Http\Request;

use Datatables;
use Session;
use View;
use Breadcrumbs;
use Exception;



class Files {


    public static function getUploadKey(){
        $response = ONE::get([
            'component' => 'files',
            'api' => 'file',
            'method' => 'genUploadKey',
        ]);
        if($response->statusCode()!= 200){
            throw new Exception(trans("comModulesFiles.errorRetrievingUploadKey"));

        }
        return $response->json()->upload_key;
    }

    public static function getFile($fileId){
        $response = ONE::get([
            'component' => 'files',
            'api' => 'file',
            'attribute' => $fileId
        ]);
        if($response->statusCode()!= 200){
            throw new Exception(trans("comModulesFiles.errorRetrievingFile"));
        }
        return $response->json()->file;
    }

    public static function listFiles($files_order){
        $response = ONE::post([
            'component' => 'files',
            'api'       => 'file/listFiles',
            'params'    => ['fileList' => $files_order]
        ]);

        if($response->statusCode()!= 200){
            throw new Exception(trans("comModulesFiles.errorRetrievingFiles"));
        }
        return $response->json()->data;
    }

    public static function getListFiles(){
        $response = ONE::get([
            'component' => 'files',
            'api'       => 'file',
            'method'    => 'list'
        ]);

        if($response->statusCode()!= 200){
            throw new Exception(trans("comModulesFiles.errorRetrievingListFiles"));
        }
        return $response->json();
    }

    public static function getListImagesFiles(){
        $response = ONE::get([
            'component' => 'files',
            'api'       => 'file',
            'method'    => 'listImages'
        ]);

        if($response->statusCode()!= 200){
            throw new Exception(trans("comModulesFiles.errorRetrievingListImagesFiles"));
        }
        return $response->json();
    }

    public static function getFormConstruction($questionnaireKey){
        $response = ONE::get([
            'component' => 'q',
            'api' => 'form',
            'api_attribute' => $questionnaireKey,
            'method' => 'construction'
        ]);

        if($response->statusCode()!= 200){
            throw new Exception(trans("comModulesFiles.errorRetrievingFormConstruction"));
        }
        return $response->json();
    }
}
