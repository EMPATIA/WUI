<?php

namespace App;

use App\One\One;
use Illuminate\Support\Collection;


use Illuminate\Http\Request;
use App\Http\Requests\KioskRequest;
use Datatables;
use Session;
use View;
use Breadcrumbs;
use Exception;



trait Kiosk {
    
    public function getIdeas() {
        $response = ONE::get([
            'component' => 'orchestrator',
            'api'       => 'idea',
            'method'    => 'list'
        ]);        
                
        if($response->statusCode() != 200){
            throw new Exception("Failed to get list of ideas.");
        }        
        
        $list = $response->json()->data;

        $listCb = [];
        foreach ($list as $item) {
            $listCb[] = $item->cb_key;
        }
        

        $response = ONE::post([
            'component' => 'cb',
            'api' => 'cb',
            'method' => 'listCBs',
            'params' => [
                'cbList' => $listCb
            ]
        ]);  
                
        if($response->statusCode() != 200){
            throw new Exception("Failed to get list of ideas.");
        }                
        
        return $response;
    }    
    
    public function getIdea($id) {

        $response = ONE::get([
            'component' => 'cb',
            'api' => 'cb',
            'attribute' => $id
        ]);             
        
        return $response;
    }     
    
    public function getEntities() {
        $response = ONE::get([
            'component' => 'orchestrator',
            'api'       => 'entity',
            'method'    => 'list'
        ]);                 
        if($response->statusCode() != 200){
            throw new Exception("Failed to get list of entities.");
        }           
        return $response;
    }
    
    public function getEntity($id) {
        $response = ONE::get([
            'component' => 'orchestrator',
            'api'       => 'entity',
            'attribute'    => $id
        ]);                 
        if($response->statusCode() != 200){
            throw new Exception("Failed to get entity.");
        }           
        return $response;
    }
        
    
    
    public function getKiosk($key) {       
        $response = ONE::get([
            'component' => 'orchestrator',
            'api'       => 'kiosk',
            'attribute' => $key
        ]);
        return $response;
    }
    
}




class RequestKiosks {
    //put your code here
    use Kiosk;
    
    
    
}
