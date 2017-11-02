<?php

namespace App\Http\Controllers;

use App\ComModules\EMPATIA;
use Carbon\Carbon;
use Datatables;
use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Collection;
use ONE;
use Session;

class OperationSchedulesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param $type
     * @param $cbKey
     * @return \Illuminate\Http\Response
     */
    public function index($type,$cbKey)
    {
        {
            //Page title
            $data['title'] = trans('privateOperationSchedules.list_operation_schedules');
            Session::put('sidebarArguments', ['type' => $type, 'cbKey' => $cbKey, 'activeFirstMenu' => 'flags']);
            Session::put('sidebars', [0 => 'private', 1=> 'padsType']);

            $data['sidebar'] = 'padsType';
            $data['active'] = 'operation_schedules';
            $data['type'] = $type;
            $data['cbKey'] = $cbKey;

            return view('private.cbs.operationSchedules.index', $data);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param Request $request
     * @param $type
     * @param $cbKey
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request, $type, $cbKey)
    {
        try{
            Session::put('sidebarArguments', ['type' => $type, 'cbKey' => $cbKey, 'activeFirstMenu' => 'flags']);
            Session::put('sidebars', [0 => 'private', 1=> 'padsType']);

            $data['title'] = trans('privateOperationSchedules.create_operation_schedules');
            $data['operationActions'] = EMPATIA::listOperationActions();
            $data['operationTypes'] = EMPATIA::listOperationTypes();
            $data['type'] = $type;
            $data['cbKey'] = $cbKey;
            $data['sidebar'] = 'padsType';
            $data['active'] = 'operation_schedules';

            return view('private.cbs.operationSchedules.operationSchedule', $data);

        } catch (Exception $e){
            return redirect()->back()->withErrors(["operationSchedules.create" => $e->getMessage()]);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $type, $cbKey)
    {
        try{
            $data['cb_key'] = $request->input('cbKey');
            $data['active'] = $request->input('active',0);
            $data['end_date'] = $request->input('endDate');
            $data['start_date'] = $request->input('startDate');
            $data['operation_type_code'] = $request->input('operationTypeSelect');
            $data['operation_action_code'] = $request->input('operationActionSelect');

            $operationSchedule = EMPATIA::storeCbOperationSchedule($data);

            return redirect()->action('OperationSchedulesController@show',[$type, $cbKey, $operationSchedule->cb_operation_schedule_key]);

        } catch (Exception $e){
            return redirect()->back()->withErrors(["operationSchedules.create" => $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @param $type
     * @param $cbKey
     * @param $operationScheduleKey
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $type, $cbKey, $operationScheduleKey)
    {
        try{
            Session::put('sidebarArguments', ['type' => $type, 'cbKey' => $cbKey, 'activeFirstMenu' => 'flags']);
            Session::put('sidebars', [0 => 'private', 1=> 'padsType']);

            $data['title'] = trans('privateOperationSchedules.show_operation_schedules');
            $data['type'] = $type;
            $data['cbKey'] = $cbKey;
            $data['sidebar'] = 'padsType';
            $data['active'] = 'operation_schedules';

            $data['operationActions'] = EMPATIA::listOperationActions();
            $data['operationTypes'] = EMPATIA::listOperationTypes();
            $data['operationSchedule'] = EMPATIA::getCbOperationSchedule($operationScheduleKey);

            return view('private.cbs.operationSchedules.operationSchedule', $data);

        } catch (Exception $e){
            return redirect()->back()->withErrors(["operationSchedules.index" => $e->getMessage()]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Request $request
     * @param $type
     * @param $cbKey
     * @param $operationScheduleKey
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $type, $cbKey, $operationScheduleKey)
    {
        try{
            Session::put('sidebarArguments', ['type' => $type, 'cbKey' => $cbKey, 'activeFirstMenu' => 'flags']);
            Session::put('sidebars', [0 => 'private', 1=> 'padsType']);

            $data['title'] = trans('privateOperationSchedules.edit_operation_schedules');
            $data['type'] = $type;
            $data['cbKey'] = $cbKey;
            $data['sidebar'] = 'padsType';
            $data['active'] = 'operation_schedules';

            $data['operationActions'] = EMPATIA::listOperationActions();
            $data['operationTypes'] = EMPATIA::listOperationTypes();
            $data['operationSchedule'] = EMPATIA::getCbOperationSchedule($operationScheduleKey);

            return view('private.cbs.operationSchedules.operationSchedule', $data);

        } catch (Exception $e){
            return redirect()->back()->withErrors(["operationSchedules.index" => $e->getMessage()]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param $type
     * @param $cbKey
     * @param $operationScheduleKey
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $type, $cbKey, $operationScheduleKey)
    {
        try{
            $data['active'] = $request->input('active',0);
            $data['end_date'] = $request->input('endDate');
            $data['start_date'] = $request->input('startDate');

            $operationSchedule = EMPATIA::updateCbOperationSchedule($operationScheduleKey, $data);

            return redirect()->action('OperationSchedulesController@show',[$type, $cbKey, $operationSchedule->cb_operation_schedule_key]);

        } catch (Exception $e){
            return redirect()->back()->withErrors(["operationSchedules.create" => $e->getMessage()]);
        }
    }

    /**
     * Deactivate the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return string
     */
    public function changeStatus(Request $request)
    {
        try{
            $cbOperationScheduleKey = $request->input('cbOperationScheduleKey');
            $data['active'] = $request->input('status');
            $requestOperationSchedules = EMPATIA::updateCbOperationSchedule($cbOperationScheduleKey, $data);

            return 'OK';
        } catch (Exception $e){
            return 'KO';
        }
    }

    public function getIndexTable($type, $cbKey)
    {
        try {
            $requestOperationSchedules = EMPATIA::getCbOperationScheduleGroup($cbKey);

            //in case of json
            $operationSchedules = Collection::make($requestOperationSchedules);

            //  Datatable with sent emails list
            return Datatables::of($operationSchedules)
                ->editColumn('action_name', function ($operationSchedules) use ($type, $cbKey) {
                    return "<a href='".action('OperationSchedulesController@show', ['type' => $type, 'cbKey' => $cbKey, 'key' => $operationSchedules->cb_operation_schedule_key])."'>".
                        $operationSchedules->operation_action->name ?? null . "</a>";
                })
                ->editColumn('type_name', function ($operationSchedules) {
                    return $operationSchedules->operation_type->name ?? null;
                })
                ->editColumn('start_date', function ($operationSchedules){
                    return $operationSchedules->start_date ?? null;
                })
                ->editColumn('end_date', function ($operationSchedules){
                    return $operationSchedules->end_date ?? null;
                })
                ->editColumn('active', function ($operationSchedules){
                    return $operationSchedules->active == 1 ? trans("operationSchedules.yes") : trans("operationSchedules.no");
                })
                ->addColumn('update_status', function ($operationSchedules){
                    return $operationSchedules->active == 1 ? "<button onclick='deactivate(\"$operationSchedules->cb_operation_schedule_key\")' type='button' class='btn btn-danger btn-xs'>".trans("operationSchedules.deactivate")."</button>" : "<button onclick='activate(\"$operationSchedules->cb_operation_schedule_key\")' type='button' class='btn btn-success btn-xs'>".trans("operationSchedules.activate")."</button>";
                })
                ->addColumn('action', function ($operationSchedules) use($type, $cbKey){
                    return ONE::actionButtons(['type' => $type, 'cbKey' => $cbKey, 'key' => $operationSchedules->cb_operation_schedule_key], ['form'=> 'operationSchedules','edit' => 'OperationSchedulesController@edit']);
                })
                ->make(true);
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["operationSchedules.getIndexTable" => $e->getMessage()]);
        }
    }
}
