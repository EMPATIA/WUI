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

//        $trackingData = $this->getTrackingTable('1');
//        $data['trackingDatas']=$trackingData;
        $title = trans('auditing.auditing');
            return view('private.tracking.tracking', compact('title'));

    }


    public function getTrackingTable(Request $request)
    {
        try {
            $timeFilter = '1';

            $response = LogsRequest::getTrackingDataByTimeFilter($request, $timeFilter);

            $collections = Collection::make($response->logs);

            $recordsTotal = $response->recordsTotal;
            $recordsFiltered = $collections->count();

            return Datatables::of($collections)
                ->with('filtered', $recordsFiltered ?? 0)
                ->skipPaging()
                ->setTotalRecords($recordsTotal ?? 0)
                ->make(true);
        } catch (Exception $e) {
        }

    }

}
