<?php

namespace App\Http\Controllers;

use App\ComModules\LogsRequest;
use App\ComModules\Orchestrator;
use Illuminate\Http\Request;
use App\One\One;
use Exception;
use Datatables;
use Illuminate\Support\Collection;


class TrackingController extends Controller
{

    public function show($id){

        try {
            $modules = Orchestrator::getModulesList();
            $modulesArray = [];
            foreach($modules->data as $module){
                $modulesArray[$module->token]= $module->name;
            }

            $trackingData = LogsRequest::getTrackingData($id);
            $time = $trackingData[0]->time_end-$trackingData[0]->time_start;
            $trackingData[0]->time=$time;

            $data["trackingData"]= $trackingData[0];

            $requests = LogsRequest::getTrackingRequestsData($trackingData[0]->id);

            foreach ($requests as $key=>$request){
                $requests[$key]->name=$modulesArray[$request->module_token];
            }

            $data['requests']=$requests;


            return view('private.tracking.trackingDetails', $data);
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["tracking.show" => $e->getMessage()]);
        }
    }


    public function showTracking(){

        $trackingData = $this->getTrackingTable('1');
        $data['trackingDatas']=$trackingData;
            return view('private.tracking.tracking', $data);

    }


    public function getTrackingTable()
    {
        $timeFilter='1';
        $response = LogsRequest::getTrackingDataByTimeFilter($timeFilter);

        // in case of json

        $collections = Collection::make($response);
        foreach ($collections as $key=>$collection){
            if($collection->time_end==null || $collection->time_start==null) $time=0;
            else $time=$collection->time_end-$collection->time_start;
        $collections[$key]->time=$time;
        }

        return Datatables::of($collections)->filterColumn('user_key', 'ip', 'url', 'id')
            ->editColumn('id', function ($collections) {
                return "<a href='".action('TrackingController@show', $collections->id)."'>".$collections->id."</a>";
            })
            ->editColumn('action', function ($collections) {
                return ONE::actionButtons($collections->id, ['show' => 'TrackingController@show']);
            })
            ->make(true);


    }

}
