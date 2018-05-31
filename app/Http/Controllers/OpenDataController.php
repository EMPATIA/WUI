<?php

namespace App\Http\Controllers;

use ONE;
use View;
use App\ComModules\EMPATIA;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use App\Http\Controllers\Controller;

class OpenDataController extends Controller
{
    public function index() {
        try {
            $title = trans('privateOpenData.openData');
            return view("private.openData.index", compact('title'));
        } catch(Exception $e) {
            return redirect()->back()->withErrors(["openData.index" => $e->getMessage()]);
        }
    }
    
    public function getIndexTable() {
        try {
            $openDatas = EMPATIA::getExistingOpenDatas();
            
            $openDatasDataTable = [];
            foreach ($openDatas as $openData) {
                $openDatasDataTable[] = array(
                    'entity_name' => $openData->entity->name,
                    'entity_key'  => $openData->entity_key,
                    'created_by'  => $openData->creator->name ?? $openData->created_by,
                    'created_at'  => $openData->created_at
                );
            }

            return Datatables::of(collect($openDatasDataTable))
                ->addColumn('action', function ($openData) {
                    return ONE::actionButtons($openData["entity_key"], ['show' => 'OpenDataController@show']);
                })
                ->make(true);
        } catch(Exception $e) {
            return redirect()->back()->withErrors(["openData.getIndexTable" => $e->getMessage()]);
        }
    }

    public function show($entityKey = null) {
        try {
            $isEditable = false;
            if (empty($entityKey) || !One::isAdmin()) {
                $entityKey = ONE::getEntityKey();
                $isEditable = true;
            }
            
            $openDataConfigurations = EMPATIA::getOpenDataConfigurations($entityKey);

            $entity = $openDataConfigurations->entity;
            $parameterUserTypes = $openDataConfigurations->parameterUserTypes;
            $cbs = $openDataConfigurations->cbs;
            $openData = $openDataConfigurations->openData;

            $entityOpenDataConfigurations = array(
                "user_parameters" => collect($openDataConfigurations->openData->user_parameters??[])->pluck("parameter_user_type_key","parameter_user_type_key")->toArray(),
                "cbs" => array()
            );

            foreach ($openDataConfigurations->openData->cb_parameters??[] as $cbParameter) {
                $entityOpenDataConfigurations["cbs"][$cbParameter->parameter->cb_id]["parameters"][$cbParameter->parameter_id] = $cbParameter->parameter_id;
            }
            foreach ($openDataConfigurations->openData->vote_events??[] as $voteEvent) {
                if(!empty($voteEvent->vote)){
                    $entityOpenDataConfigurations["cbs"][$voteEvent->vote->cb_id]["votes"][$voteEvent->vote_event_key] = $voteEvent->vote_event_key;
                }
            }
            
            return view("private.openData.openData",compact("isEditable","entity","parameterUserTypes","cbs","entityOpenDataConfigurations","openData"));
        } catch(Exception $e) {
            return redirect()->back()->withErrors(["openData.show" => $e->getMessage()]);
        }
    }

    public function edit() {
        return $this->show();
    }

    public function update(Request $request, $entityKey = null) {
        try {
            if (empty($entityKey) || !One::isAdmin())
                $entityKey = ONE::getEntityKey();
            
            $userParameterConfigurations = array();
            if(!empty($request->get("user_parameters_switch")))
                $userParameterConfigurations = $request->get("user_parameters");
            
            $cbsConfigurations = array();
            if(!empty($request->get("cbs_switch"))) {
                foreach ($request->get("cbs") as $cbKey=>$cbData) {
                    if (!empty($cbData["switch"])) {
                        if (!empty($cbData["parameters"]))
                            $cbsConfigurations[$cbKey]["parameters"] = $cbData["parameters"];

                        if (!empty($cbData["votes"]))
                            $cbsConfigurations[$cbKey]["votes"] = $cbData["votes"];
                    }
                }
            }

            EMPATIA::updateOpenDataConfigurations($entityKey, $userParameterConfigurations,$cbsConfigurations);

            \Session::flash('message', trans('privateOpenData.update_ok'));
            return redirect()->action('OpenDataController@show');
        } catch(Exception $e) {
            return redirect()->back()->withErrors(["openData.update" => $e->getMessage()]);
        }
    }

    public function export(Request $request, $token) {
        try {
            $response = EMPATIA::exportOpenData($token, $request->get("type"));
            
            return response()->json($response);
        } catch(Exception $e) {
            return response()->json(["error" => $e->getMessage()],500);
        }
    }
}
