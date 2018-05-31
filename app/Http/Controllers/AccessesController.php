<?php

namespace App\Http\Controllers;

use App\ComModules\LogsRequest;
use Illuminate\Http\Request;
use App\ComModules\EMPATIA;
use App\ComModules\Orchestrator;
use App\Charts\Register;
use App\Http\Controllers\Controller;
use Illuminate\Support\Collection;
use Datatables;
use Session;
use Charts;

class AccessesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title = trans('privateLogs.accesses');

        //sites
        $sites = EMPATIA::getEntitiesSites();

        //cbs
        $cbs = LogsRequest::getCbs();

        //action
        $actions = LogsRequest::getActions();

        //results
        $results = [
            'all'  => trans('privateLogs.all'),
            'Ok'   => trans('privateLogs.Ok'),
            'Ko'   => trans('privateLogs.Ko'),
        ];


        return view('private.accesses.index', compact('title','actions','sites','cbs','results'));
    }

    /**
     * Show a logs/register chart.
     *
     * @return \Illuminate\Http\Response
     */
    public function analytic(Request $request, $entityKey = null )
    {
        $entityKey = $entityKey == '0' ? $entityKey : Orchestrator::getSiteEntity($_SERVER["HTTP_HOST"])->entity_id;

        $dayStart = !empty($request->dayStart) ? $request->dayStart : null;
        $dayEnd = !empty($request->dayEnd) ? $request->dayEnd : null;

        $title = trans('privateLogs.analytics');
        $chart = new Register;

        $chart->dataset(trans('privateLogs.LoginOk'), 'line', LogsRequest::getAnalytics($entityKey,$dayStart,$dayEnd)->loginOk)->color('#00ff00');
        $chart->dataset(trans('privateLogs.LoginKo'), 'line', LogsRequest::getAnalytics($entityKey,$request->dayStart,$request->dayEnd)->loginKo)->color('#008000');
        $chart->dataset(trans('privateLogs.RegisterOk'), 'line', LogsRequest::getAnalytics($entityKey,$request->dayStart,$request->dayEnd)->registerOk)->color('#00BFFF');
        $chart->dataset(trans('privateLogs.RegisterKo'), 'line', LogsRequest::getAnalytics($entityKey,$request->dayStart,$request->dayEnd)->registerKo)->color('#000080');
        $chart->dataset(trans('privateLogs.PasswordRecoveryOk'), 'line', LogsRequest::getAnalytics($entityKey,$request->dayStart,$request->dayEnd)->passwordRecoveryOk)->color('#FFA500');
        $chart->dataset(trans('privateLogs.PasswordRecoveryKo'), 'line', LogsRequest::getAnalytics($entityKey,$request->dayStart,$request->dayEnd)->passwordRecoveryKo)->color('#FF0000');

        $chart->labels(LogsRequest::getAnalytics($entityKey,$request->dayStart,$request->dayEnd)->days);
        $chart->title(trans('privateLogs.registerAndLoginAnalytics'));

        $topicChart = new Register;
        $topicChart->dataset(trans('privateLogs.CreateTopicOk'), 'line', LogsRequest::getAnalytics($entityKey,$request->dayStart,$request->dayEnd)->createTopicOk)->color('#00BFFF');
        $topicChart->dataset(trans('privateLogs.CreateTopicKo'), 'line', LogsRequest::getAnalytics($entityKey,$request->dayStart,$request->dayEnd)->createTopicKo)->color('#000080');
        $topicChart->dataset(trans('privateLogs.ShowTopicOk'), 'line', LogsRequest::getAnalytics($entityKey,$request->dayStart,$request->dayEnd)->showTopicOk)->color('#FFA500');
        $topicChart->dataset(trans('privateLogs.ShowTopicKo'), 'line', LogsRequest::getAnalytics($entityKey,$request->dayStart,$request->dayEnd)->showTopicKo)->color('#FF0000');

        $topicChart->labels(LogsRequest::getAnalytics($entityKey,$request->dayStart,$request->dayEnd)->days);
        $topicChart->title(trans('privateLogs.topicAnalytics'));

        return view('private.accesses.analytic', compact('title', 'chart','topicChart','dayStart', 'dayEnd','entityKey' ));
    }

    public function analyticEntityKey()
    {
        $entityKey = '0';
        return redirect()->action('\App\Http\Controllers\AccessesController@analytic', [$entityKey]);
    }



    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function tableAccesses(Request $request)
    {
        $accesses = LogsRequest::getAccesses($request);

        $collection = Collection::make($accesses);

        return Datatables::of($collection)
        
        ->editColumn('email', function ($collection) {
            if (!empty($collection->email))
                return '<span data-toggle="tooltip" title="'.trans('privateLogs.user_key').': '.$collection->user_key.' 
                '.trans('privateLogs.name').': '.$collection->name.'">'.$collection->email.'</span>';
            else
                return "";
        })

        ->rawColumns(['email'])
        ->make(true);
    }
}
