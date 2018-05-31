<?php

namespace App\Http\Controllers;

use App\ComModules\Notify;
use Datatables;
use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Collection;
use App\Http\Requests\ContentRequest;
use App\ComModules\Orchestrator;
use App\ComModules\Auth;
use One;
use Session;
use Carbon\Carbon;

class smsController extends Controller
{

    /**
     * smsController constructor.
     */
    public function __construct()
    {

    }


    /**
     * Returns Sms List View
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */

    public function index()
    {
        try {
            $statisticsType = 'resumeSms';
            $view = '';

//            HEADER-------------------------------------------------------------------------------------
            $totalSendedSms = Notify::countTotalSendedSms();
            $totalReceivedSms = Notify::countTotalReceivedSms();
            $totalSmsVotes = Notify::countTotalSmsVotes();
            $totalReceivedSmsErrors = Notify::countTotalReceivedSmsErrors();

            $data["totalSendedSms"] = $totalSendedSms;
            $data["totalReceivedSms"] = $totalReceivedSms;
            $data["totalSmsVotes"] = $totalSmsVotes;
            $data["totalReceivedSmsErrors"] = $totalReceivedSmsErrors;

//            GRAFICO 48H--------------------------------------------------------------------------------

            $totalSendedSmsLast48H = Notify::countTotalSendedSmsLast48hPerHour();
            $totalReceivedSmsLast48H = Notify::countTotalReceivedSmsLast48hPerHour();
            $totalSmsVotesLast48H = Notify::countTotalSmsVotesLast48hPerHour();
            $totalSmsVotesErrorsLast48H = Notify::countTotalSmsVotesErrorsLast48hPerHour();

            $data["totalSendedSmsLast48H"] = $totalSendedSmsLast48H;
            $data["totalReceivedSmsLast48H"] = $totalReceivedSmsLast48H;
            $data["totalSmsVotesLast48H"] = $totalSmsVotesLast48H;
            $data["totalSmsVotesErrorsLast48H"] = $totalSmsVotesErrorsLast48H;

            $sidebar = 'sms';
            $active = 'resume';

            $view = 'private.sms.resumeSms';

            Session::put('sidebarArguments', [ 'activeFirstMenu' => 'resume']);

            return view($view, $data, compact('sidebar','active'));

        }catch(Exception $e){
            return redirect()->back()->withErrors(["sms.show" => $e->getMessage()]);
        }
    }


    /**
     *
     * Returns data to datatable with sms list
     *
     * @return $this
     */
    public function tableSendedSms()
    {
        //
        try {
            //Get all sent sms

            $sentSms = Notify::getSmss();

            // in case of json
            $sentSms = Collection::make($sentSms);
            $sentSms = $sentSms->sortByDesc('created_at');

            //  Datatable with sent sms list
            return Datatables::of($sentSms)
                ->editColumn('recipient', function ($sentSms) {
                    return "<a href='" . action('SmsController@show', $sentSms->sms_key) . "'>" . $sentSms->recipient . "</a>";
                })
                ->editColumn('sent', function ($sentSms) {
                    if ($sentSms->sent == '1'){
                        return trans('privateSms.sended');
                    }else{
                        return trans('privateSms.not_sended');
                    }
                })
                ->addColumn('action', function ($sentSms) {
                    return ONE::actionButtons($sentSms->sms_key, ['show' => 'SmsController@show']);
                })
                ->rawColumns(['recipient','action'])
                ->make(true);
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["groupTypes.tableGroupTypes" => $e->getMessage()]);
        }
    }

    /**
     *
     * Returns data to datatable with sms list
     *
     * @return $this
     */
    public function tableReceivedSms()
    {
        //
        try {
            //Get all received sms

            $receivedSms = Notify::getReceivedSms();

            // in case of json
            $receivedSms = Collection::make($receivedSms);
            $receivedSms = $receivedSms->sortByDesc('created_at');

            //  Datatable with received sms list
            return Datatables::of($receivedSms)
                ->addColumn('action', function ($receivedSms) {
//                    return ONE::actionButtons($receivedSms->received_sms_key, ['show' => 'SmsController@showReceivedDetails']);
                })
                ->editColumn('processed', function ($receivedSms) {
                    if (strpos($receivedSms->answer, 'recebido') !== false) {
                        return trans('privateSms.received_vote');
                    }else{
                        return trans('privateSms.error_vote');
                    }
                })
                ->rawColumns(['processed'])
                ->make(true);
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["groupTypes.tableGroupTypes" => $e->getMessage()]);
        }
    }

    /**
     * @param Request $request
     * @return string
     */
    public function getSendedDatatableFilter(Request $request)
    {
        try {
            if ($request->start_date!=null){
                $dates=explode("?end_date=",$request->start_date);
                $start_date=$dates[0];
                $end_date=$dates[1];
                $values=explode("?value=",$request->start_date);
                $value= $values[1];
            }else{
                $start_date=null;
                $end_date=null;
            }

            if(Session::get('user_role') == 'admin'){
                $sentSms = Notify::getSmss();

                $sentSms = Collection::make($sentSms);

                $sentSms1 = Collection::make($sentSms)->pluck('created_by');
                $usersKey = $sentSms1->unique()->toArray();
                
                $responseAuth = Auth::listUser($usersKey);
                $userNames = [];
                foreach ($responseAuth as $item) {
                    $userNames[$item->user_key] = $item->name;
                }
                
                if ($start_date!=null && $end_date!=null && $value!=2) {

                    $newList=collect($sentSms)->where('created_at', '>=', $start_date)
                                              ->where('created_at', '<=', $end_date)
                                              ->where('sent','==',$value);


                }else{
                    $newList1=collect($sentSms)->where('created_at', '>=', $start_date);
                    $newList=$newList1->where('created_at', '<=', $end_date);
                }
            }else
                $newList = collect([]);

            //  Datatable with sended sms list
            return Datatables::of($newList)
                ->editColumn('recipient', function ($sentSms) {
                    return "<a href='" . action('SmsController@show', $sentSms->sms_key) . "'>" . $sentSms->recipient . "</a>";
                })
                ->editColumn('created_by', function ($collection) use ($userNames) {
                    return !empty($userNames[$collection->created_by]) ? $userNames[$collection->created_by] : "";
                })
                ->editColumn('sent', function ($sentSms) {
                    if ($sentSms->sent == '1'){
                        return trans('privateSms.sended');
                    }else{
                        return trans('privateSms.not_sended');
                    }
                })
                ->addColumn('action', function ($sentSms) {
                    return ONE::actionButtons($sentSms->sms_key, ['show' => 'SmsController@show']);
                })
                ->rawColumns(['recipient','action'])
                ->make(true);
        } catch (Exception $e) {
            return  $e->getMessage();
        }
    }

    /**
     * @param Request $request
     * @return string
     */
    public function getReceivedDatatableFilter(Request $request)
    {
        try {
            if ($request->start_date!=null){
                $dates=explode("?end_date=",$request->start_date);
                $start_date=$dates[0];
                $end_date=$dates[1];
                $statuss=explode("?status=",$request->start_date);
                $status= $statuss[1];
            }else{
                $start_date=null;
                $end_date=null;
            }

            if($status == 1){
                $status = 'O teu voto foi recebido e sera validado';
            }elseif($status == 0){
                $status = 'Houve um erro com o teu voto';
            }

            if(Session::get('user_role') == 'admin'){
                $receivedSms = Notify::getReceivedSms();

                $receivedSms = Collection::make($receivedSms);

                if ($start_date!=null && $end_date!=null && $status !=2) {

                    $newList1=collect($receivedSms)->where('created_at', '>=', $start_date)
                                                   ->where('created_at', '<=', $end_date);

                    $newList=$newList1->filter(function($sms) use($status){

                        return str_contains($sms->answer, $status);
                    });

                }     else{
                    $newList1=collect($receivedSms)->where('created_at', '>=', $start_date);
                    $newList=$newList1->where('created_at', '<=', $end_date);
                }
            }else
                $newList = collect([]);

            //  Datatable with received sms list
            return Datatables::of($newList)
                ->editColumn('processed', function ($receivedSms) {
                    if (strpos($receivedSms->answer, 'recebido') !== false) {
                        return trans('privateSms.received_vote');
                    }else{
                        return trans('privateSms.error_vote');
                    }
                })
                ->addColumn('action', function ($receivedSms) {
                    return ONE::actionButtons($receivedSms->received_sms_key, ['show' =>'SmsController@showReceivedDetails']);
                })
                ->rawColumns(['action'])
                ->make(true);
        } catch (Exception $e) {
            return  $e->getMessage();
        }
    }

    /**
     * Shows sms details from a given sms Key
     *
     * @param Request $request
     * @param $smsKey
     * @return $this
     */
    public function show(Request $request, $smsKey)
    {
        try {
            //TODO implement

            $sms = Notify::getSms($smsKey);

            // Form title (layout)
            $title = trans('privateSms.show_sms');

            // Return the view with data
            $data = [];
            $data['title'] = $title;
            $data['sms'] = $sms;

            $sidebar = 'sms';
            $active = 'sended';

            Session::put('sidebarArguments', [ 'activeFirstMenu' => 'sended']);

            return view('private.sms.sendedSmsDetails', $data, compact('sidebar','active'));
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["sms.show" => $e->getMessage()]);
        }
    }

    /**
     * Shows sms details from a given sms Key
     *
     * @param Request $request
     * @param $receivedSmsKey
     * @return $this
     */
    public function showReceivedDetails(Request $request, $receivedSmsKey)
    {

        try {
            //TODO implement

            $sms = Notify::getReceivedSmsDetails($receivedSmsKey);

            // Form title (layout)
            $title = trans('privateSms.show_sms');

            // Return the view with data
            $data = [];
            $data['title'] = $title;
            $data['sms'] = $sms;

            $sidebar = 'sms';
            $active = 'received';

            Session::put('sidebarArguments', ['activeFirstMenu' => 'received']);

            return view('private.sms.receivedSmsDetails', $data, compact('sidebar','active'));
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["sms.show" => $e->getMessage()]);
        }
    }

    public function showResume48H(Request $request)
    {
//            GRAFICO 48h--------------------------------------------------------------------------------

        $totalSendedSmsLast48H = Notify::countTotalSendedSmsLast48hPerHour();
        $totalReceivedSmsLast48H = Notify::countTotalReceivedSmsLast48hPerHour();
        $totalSmsVotesLast48H = Notify::countTotalSmsVotesLast48hPerHour();
        $totalSmsVotesErrorsLast48H = Notify::countTotalSmsVotesErrorsLast48hPerHour();

        $dataTotalSendedSms48H = [];
        $dataTotalReceivedSms48H = [];
        $dataTotalSmsVotes48H = [];
        $dataTotalSmsVotesErrors48H = [];

        $sd = Carbon::now()->subDays(2);
        $ed = Carbon::now();

        $hours = $sd->diffInHours($ed);
        $sd->subHour(1);

        //Create Arrays with all days between two dates
        for ($i = 0; $i <= $hours; $i++) {
            $date = '';

            $date = $sd->addHour(1);

            $dataTotalSendedSms48H[$date->format('Y-m-d H:00')] = 0;
            $dataTotalReceivedSms48H[$date->format('Y-m-d H:00')] = 0;
            $dataTotalSmsVotes48H[$date->format('Y-m-d H:00')] = 0;
            $dataTotalSmsVotesErrors48H[$date->format('Y-m-d H:00')] = 0;
        }

        //Change the values for the specific hour
        foreach (!empty($totalSendedSmsLast48H) ? $totalSendedSmsLast48H : [] as $key => $item) {
            $dataTotalSendedSms48H[$item->year . "-" . (($item->month < 10) ? "0" . $item->month : $item->month) . "-" . (($item->day < 10) ? "0" . $item->day : $item->day) ." ".(($item->hour < 10) ? "0" .$item->hour.":00":$item->hour.":00")] = !empty($item->total_sended_sms) ? $item->total_sended_sms : 0;
        }

        foreach (!empty($totalReceivedSmsLast48H) ? $totalReceivedSmsLast48H : [] as $key => $item) {
            $dataTotalReceivedSms48H[$item->year . "-" . (($item->month < 10) ? "0" . $item->month : $item->month) . "-" . (($item->day < 10) ? "0" . $item->day : $item->day)." " .(($item->hour < 10) ? "0" .$item->hour.":00":$item->hour.":00")] = !empty($item->total_received_sms) ? $item->total_received_sms : 0;
        }

        foreach (!empty($totalSmsVotesLast48H) ? $totalSmsVotesLast48H : [] as $key => $item) {
            $dataTotalSmsVotes48H[$item->year . "-" . (($item->month < 10) ? "0" . $item->month : $item->month) . "-" . (($item->day < 10) ? "0" . $item->day : $item->day)." ".(($item->hour < 10) ? "0" .$item->hour.":00":$item->hour.":00")] = !empty($item->total_sms_votes) ? $item->total_sms_votes : 0;
        }

        foreach (!empty($totalSmsVotesErrorsLast48H) ? $totalSmsVotesErrorsLast48H : [] as $key => $item) {
            $dataTotalSmsVotesErrors48H[$item->year . "-" . (($item->month < 10) ? "0" . $item->month : $item->month) . "-" . (($item->day < 10) ? "0" . $item->day : $item->day)." ".(($item->hour < 10) ? "0" .$item->hour.":00":$item->hour.":00")] = !empty($item->total_sms_votes_errors) ? $item->total_sms_votes_errors : 0;
        }

        //Create Arrays for return all data in Json
        $dataForTotalSendedSms48H = [];
        $dataForTotalReceivedSms48H = [];
        $dataForTotalSmsVotes48H = [];
        $dataForTotalSmsVotesErrors48H = [];

        foreach (!empty($dataTotalSendedSms48H) ? $dataTotalSendedSms48H : [] as $key => $item) {
            $dataForTotalSendedSms48H[] = collect(["Data" => $key, 'name' => 'sendedSms', 'Votos' => $item])->toJson();
        }

        foreach (!empty($dataTotalReceivedSms48H) ? $dataTotalReceivedSms48H : [] as $key => $item) {
            $dataForTotalReceivedSms48H[] = collect(["Data" => $key, 'name' => 'receivedSms', 'Votos' => $item])->toJson();
        }

        foreach (!empty($dataTotalSmsVotes48H) ? $dataTotalSmsVotes48H : [] as $key => $item) {
            $dataForTotalSmsVotes48H[] = collect(["Data" => $key, 'name' => 'smsVotes', 'Votos' => $item])->toJson();
        }

        foreach (!empty($dataTotalSmsVotesErrors48H) ? $dataTotalSmsVotesErrors48H : [] as $key => $item) {
            $dataForTotalSmsVotesErrors48H[] = collect(["Data" => $key, 'name' => 'smsVotesErrors', 'Votos' => $item])->toJson();
        }

        // Return data to show in chart
        return ["TotalSendedSms48H" => $dataForTotalSendedSms48H,
            "TotalReceivedSms48H" => $dataForTotalReceivedSms48H,
            "TotalSmsVotes48H" => $dataForTotalSmsVotes48H,
            "TotalSmsVotesErrors48H" => $dataForTotalSmsVotesErrors48H
        ];
    }

    public function showResume30D(Request $request)
    {
//            GRAFICO 30D--------------------------------------------------------------------------------

        $totalSendedSmsLast30D = Notify::countTotalSendedSmsLast30DPerDay();
        $totalReceivedSmsLast30D = Notify::countTotalReceivedSmsLast30DPerDay();
        $totalSmsVotesLast30D = Notify::countTotalSmsVotesLast30DPerDay();
        $totalSmsVotesErrorsLast30D = Notify::countTotalSmsVotesErrorsLast30DPerDay();

        $dataTotalSendedSms30D = [];
        $dataTotalReceivedSms30D = [];
        $dataTotalSmsVotes30D = [];
        $dataTotalSmsVotesErrors30D = [];

        $sd = Carbon::now()->subDays(30);
        $ed = Carbon::now();

        $days = $sd->diff($ed)->days;
        $sd->subDay(1);

        //Create Arrays with all days between two dates
        for ($i = 0; $i <= $days; $i++) {
            $date = '';

            $date = $sd->addDays(1);

            $dataTotalSendedSms30D[$date->format('Y-m-d')] = 0;
            $dataTotalReceivedSms30D[$date->format('Y-m-d')] = 0;
            $dataTotalSmsVotes30D[$date->format('Y-m-d')] = 0;
            $dataTotalSmsVotesErrors30D[$date->format('Y-m-d')] = 0;
        }

        //Change the values for the specific day
        foreach (!empty($totalSendedSmsLast30D) ? $totalSendedSmsLast30D : [] as $key => $item) {
            $dataTotalSendedSms30D[$item->year . "-" . (($item->month < 10) ? "0" . $item->month : $item->month) . "-" . (($item->day < 10) ? "0" . $item->day : $item->day)] = !empty($item->total_sended_sms) ? $item->total_sended_sms : 0;
        }

        foreach (!empty($totalReceivedSmsLast30D) ? $totalReceivedSmsLast30D : [] as $key => $item) {
            $dataTotalReceivedSms30D[$item->year . "-" . (($item->month < 10) ? "0" . $item->month : $item->month) . "-" . (($item->day < 10) ? "0" . $item->day : $item->day)] = !empty($item->total_received_sms) ? $item->total_received_sms : 0;
        }

        foreach (!empty($totalSmsVotesLast30D) ? $totalSmsVotesLast30D : [] as $key => $item) {
            $dataTotalSmsVotes30D[$item->year . "-" . (($item->month < 10) ? "0" . $item->month : $item->month) . "-" . (($item->day < 10) ? "0" . $item->day : $item->day)] = !empty($item->total_sms_votes) ? $item->total_sms_votes : 0;
        }

        foreach (!empty($totalSmsVotesErrorsLast30D) ? $totalSmsVotesErrorsLast30D : [] as $key => $item) {
            $dataTotalSmsVotesErrors30D[$item->year . "-" . (($item->month < 10) ? "0" . $item->month : $item->month) . "-" . (($item->day < 10) ? "0" . $item->day : $item->day)] = !empty($item->total_sms_votes_errors) ? $item->total_sms_votes_errors : 0;
        }

        //Create Arrays for return all data in Json
        $dataForTotalSendedSms30D = [];
        $dataForTotalReceivedSms30D = [];
        $dataForTotalSmsVotes30D = [];
        $dataForTotalSmsVotesErrors30D = [];

        foreach (!empty($dataTotalSendedSms30D) ? $dataTotalSendedSms30D : [] as $key => $item) {
            $dataForTotalSendedSms30D[] = collect(["Data" => $key, 'name' => 'sendedSms', 'Votos' => $item])->toJson();
        }

        foreach (!empty($dataTotalReceivedSms30D) ? $dataTotalReceivedSms30D : [] as $key => $item) {
            $dataForTotalReceivedSms30D[] = collect(["Data" => $key, 'name' => 'receivedSms', 'Votos' => $item])->toJson();
        }

        foreach (!empty($dataTotalSmsVotes30D) ? $dataTotalSmsVotes30D : [] as $key => $item) {
            $dataForTotalSmsVotes30D[] = collect(["Data" => $key, 'name' => 'smsVotes', 'Votos' => $item])->toJson();
        }

        foreach (!empty($dataTotalSmsVotesErrors30D) ? $dataTotalSmsVotesErrors30D : [] as $key => $item) {
            $dataForTotalSmsVotesErrors30D[] = collect(["Data" => $key, 'name' => 'smsVotesErrors', 'Votos' => $item])->toJson();
        }

        // Return data to show in chart
        return ["TotalSendedSms30D" => $dataForTotalSendedSms30D,
            "TotalReceivedSms30D" => $dataForTotalReceivedSms30D,
            "TotalSmsVotes30D" => $dataForTotalSmsVotes30D,
            "TotalSmsVotesErrors30D" => $dataForTotalSmsVotesErrors30D
        ];
    }


    public function showSendedSms()
    {
        //Page title
        $title = trans('privateSms.sent');

        $totalSendedSms = Notify::countTotalSendedSms();
        $totalSendedSmsLast30D = Notify::countTotalSendedSmsLast30D();
        $totalSendedSmsLast24H = Notify::countTotalSendedSmsLast24H();
        $totalSendedSmsLastHour = Notify::countTotalSendedSmsLastHour();

        $data["totalSendedSms"] = $totalSendedSms;
        $data["totalSendedSmsLast30D"] = $totalSendedSmsLast30D;
        $data["totalSendedSmsLast24H"] = $totalSendedSmsLast24H;
        $data["totalSendedSmsLastHour"] = $totalSendedSmsLastHour;

        $sidebar = 'sms';
        $active = 'sended';

        Session::put('sidebarArguments', [ 'activeFirstMenu' => 'sended', 'active' => 'sended']);

        return view('private.sms.sendedSms', $data, compact('title', 'sidebar', 'active'));
    }

    public function showReceivedSms()
    {
        //Page title
        $title = trans('privateSms.received_sms');

        $totalReceivedSms = Notify::countTotalReceivedSms();
        $totalReceivedSmsErrors = Notify::countTotalReceivedSmsErrors();
        $totalReceivedSmsLast24H = Notify::countTotalReceivedSmsLast24H();
        $totalReceivedSmsLast24hErrors = Notify::countTotalReceivedSmsLast24hErrors();

        $data["totalReceivedSms"] = $totalReceivedSms;
        $data["totalReceivedSmsErrors"] = $totalReceivedSmsErrors;
        $data["totalReceivedSmsLast24H"] = $totalReceivedSmsLast24H;
        $data["totalReceivedSmsLast24hErrors"] = $totalReceivedSmsLast24hErrors;

        $sidebar = 'sms';
        $active = 'received';

        Session::put('sidebarArguments', [ 'activeFirstMenu' => 'received', 'active' => 'received']);

        return view('private.sms.receivedSms', $data, compact('title', 'sidebar', 'active'));
    }

    public function showAnalyticsSms(Request $request)
    {


        //Page title
        $title = trans('privateSms.analytics_sms');

        $totalSendedSms = Notify::countTotalSendedSms();
        $totalReceivedSms = Notify::countTotalReceivedSms();
        $totalSmsVotes = Notify::countTotalSmsVotes();
        $totalReceivedSmsErrors = Notify::countTotalReceivedSmsErrors();

        $data["totalSendedSms"] = $totalSendedSms;
        $data["totalReceivedSms"] = $totalReceivedSms;
        $data["totalSmsVotes"] = $totalSmsVotes;
        $data["totalReceivedSmsErrors"] = $totalReceivedSmsErrors;

        $sidebar = 'sms';
        $active = 'analytics';

        Session::put('sidebarArguments', [ 'activeFirstMenu' => 'analytics', 'active' => 'analytics']);

        return view('private.sms.analyticsSms', $data, compact('title', 'sidebar','active'));
    }

    public function showAnalyticsSmsFiltered24H(Request $request)
    {

        if ($request->start_date!=null){
            $dates=explode("?end_date=",$request->start_date);
            $start_date=$dates[0];
            $end_date=$dates[1];
        }else{
            $start_date=null;
            $end_date=null;
        }

        if ($start_date !== null && $end_date !== null){
            $totalSendedSms24H = Notify::countTotalSendedSms24hPersonalized($start_date, $end_date);
            $totalReceivedSms24H = Notify::countTotalReceivedSms24hPersonalized($start_date, $end_date);
            $totalSmsVotes24H = Notify::countTotalSmsVotes24hPersonalized($start_date, $end_date);
            $totalSmsVotesErrors24H = Notify::countTotalSmsVotesErrors24hPersonalized($start_date, $end_date);
        }
        else {
            $dataTotalSendedSms24H = null;
            $dataTotalReceivedSms24H = null;
            $dataTotalSmsVotes24H = null;
            $dataTotalSmsVotesErrors24H = null;
        }

        $sd = new Carbon($start_date);
        $ed = new Carbon($end_date);

        $hours = $sd->diffInHours($ed);
        $sd->subHour(1);

        //Create Arrays with all days between two dates
        for ($i = 0; $i <= $hours; $i++) {
            $date = '';

            $date = $sd->addHour(1);

            $dataTotalSendedSms24H[$date->format('Y-m-d H:00')] = 0;
            $dataTotalReceivedSms24H[$date->format('Y-m-d H:00')] = 0;
            $dataTotalSmsVotes24H[$date->format('Y-m-d H:00')] = 0;
            $dataTotalSmsVotesErrors24H[$date->format('Y-m-d H:00')] = 0;
        }

        foreach(!empty($totalSendedSms24H) ? $totalSendedSms24H : []  as $key => $item){
            $dataTotalSendedSms24H[$item->year . "-" . (($item->month < 10) ? "0" . $item->month : $item->month) . "-" . (($item->day < 10) ? "0" . $item->day : $item->day) ." ".(($item->hour < 10) ? "0" .$item->hour.":00":$item->hour.":00")] = !empty($item->total_sended_sms) ? $item->total_sended_sms : 0;
        }

        foreach(!empty($totalReceivedSms24H) ? $totalReceivedSms24H : []  as $key => $item){
            $dataTotalReceivedSms24H[$item->year . "-" . (($item->month < 10) ? "0" . $item->month : $item->month) . "-" . (($item->day < 10) ? "0" . $item->day : $item->day) ." ".(($item->hour < 10) ? "0" .$item->hour.":00":$item->hour.":00")] = !empty($item->total_received_sms) ? $item->total_received_sms : 0;
        }

        foreach(!empty($totalSmsVotes24H) ? $totalSmsVotes24H : []  as $key => $item){
            $dataTotalSmsVotes24H[$item->year . "-" . (($item->month < 10) ? "0" . $item->month : $item->month) . "-" . (($item->day < 10) ? "0" . $item->day : $item->day)." ".(($item->hour < 10) ? "0" .$item->hour.":00":$item->hour.":00")] = !empty($item->total_sms_votes) ? $item->total_sms_votes : 0;
        }

        foreach(!empty($totalSmsVotesErrors24H) ? $totalSmsVotesErrors24H : []  as $key => $item){
            $dataTotalSmsVotesErrors24H[$item->year . "-" . (($item->month < 10) ? "0" . $item->month : $item->month) . "-" . (($item->day < 10) ? "0" . $item->day : $item->day)." ".(($item->hour < 10) ? "0" .$item->hour.":00":$item->hour.":00")] = !empty($item->total_sms_votes_errors) ? $item->total_sms_votes_errors : 0;
        }

        //Create Arrays for return all data in Json
        $dataForTotalSendedSms24H = [];
        $dataForTotalReceivedSms24H = [];
        $dataForTotalSmsVotes24H = [];
        $dataForTotalSmsVotesErrors24H = [];

        foreach (!empty($dataTotalSendedSms24H) ? $dataTotalSendedSms24H : [] as $key => $item) {
            $dataForTotalSendedSms24H[] = collect(["Data" => $key, 'name' => 'sendedSms', 'Votos' => $item])->toJson();
        }

        foreach (!empty($dataTotalReceivedSms24H) ? $dataTotalReceivedSms24H : [] as $key => $item) {
            $dataForTotalReceivedSms24H[] = collect(["Data" => $key, 'name' => 'receivedSms', 'Votos' => $item])->toJson();
        }

        foreach (!empty($dataTotalSmsVotes24H) ? $dataTotalSmsVotes24H : [] as $key => $item) {
            $dataForTotalSmsVotes24H[] = collect(["Data" => $key, 'name' => 'smsVotes', 'Votos' => $item])->toJson();
        }

        foreach (!empty($dataTotalSmsVotesErrors24H) ? $dataTotalSmsVotesErrors24H : [] as $key => $item) {
            $dataForTotalSmsVotesErrors24H[] = collect(["Data" => $key, 'name' => 'smsVotesErrors', 'Votos' => $item])->toJson();
        }

        // Return data to show in chart
        return ["TotalSendedSms24H" => $dataForTotalSendedSms24H,
            "TotalReceivedSms24H" => $dataForTotalReceivedSms24H,
            "TotalSmsVotes24H" => $dataForTotalSmsVotes24H,
            "TotalSmsVotesErrors24H" => $dataForTotalSmsVotesErrors24H
            ];

    }

    public function showAnalyticsSmsFiltered30D(Request $request)
    {

        if ($request->start_date!=null){
            $dates=explode("?end_date=",$request->start_date);
            $start_date=$dates[0];
            $end_date=$dates[1];
        }else{
            $start_date=null;
            $end_date=null;
        }

        if ($start_date !== null && $end_date !== null){
            $totalSendedSms30D = Notify::countTotalSendedSms30DPersonalized($start_date, $end_date);
            $totalReceivedSms30D = Notify::countTotalReceivedSms30DPersonalized($start_date, $end_date);
            $totalSmsVotes30D = Notify::countTotalSmsVotes30DPersonalized($start_date, $end_date);
            $totalSmsVotesErrors30D = Notify::countTotalSmsVotesErrors30DPersonalized($start_date, $end_date);

        }else{
            $dataTotalSendedSms30D = null;
            $dataTotalReceivedSms30D = null;
            $dataTotalSmsVotes30D = null;
            $dataTotalSmsVotesErrors30D = null;
        }

        $dataTotalSendedSms30D = [];
        $dataTotalReceivedSms30D = [];
        $dataTotalSmsVotes30D = [];
        $dataTotalSmsVotesErrors30D = [];

        $sd = Carbon::parse($start_date);
        $ed = Carbon::parse($end_date);

        $days = $sd->diff($ed)->days;
        $sd->subDay(1);

        for($i = 0; $i <= $days; $i++)
        {
            $date = '';

            $date = $sd->addDays(1);

            $dataTotalSendedSms30D[$date->format('Y-m-d')] = 0;
            $dataTotalReceivedSms30D[$date->format('Y-m-d')] = 0;
            $dataTotalSmsVotes30D[$date->format('Y-m-d')] = 0;
            $dataTotalSmsVotesErrors30D[$date->format('Y-m-d')] = 0;
        }

        foreach(!empty($totalSendedSms30D) ? $totalSendedSms30D : []  as $key => $item){
            $dataTotalSendedSms30D[$item-> year."-".(($item->month<10) ? "0".$item->month : $item->month)."-". (($item->day<10)? "0".$item->day : $item->day)] = !empty($item->total_sended_sms) ? $item->total_sended_sms : 0 ;
        }

        foreach(!empty($totalReceivedSms30D) ? $totalReceivedSms30D : []  as $key => $item){
            $dataTotalReceivedSms30D[$item-> year."-".(($item->month<10) ? "0".$item->month : $item->month)."-". (($item->day<10)? "0".$item->day : $item->day)] = !empty($item->total_received_sms) ? $item->total_received_sms : 0 ;
        }

        foreach(!empty($totalSmsVotes30D) ? $totalSmsVotes30D : []  as $key => $item){
            $dataTotalSmsVotes30D[$item-> year."-".(($item->month<10) ? "0".$item->month : $item->month)."-". (($item->day<10)? "0".$item->day : $item->day)] = !empty($item->total_sms_votes) ? $item->total_sms_votes : 0 ;
        }

        foreach(!empty($totalSmsVotesErrors30D) ? $totalSmsVotesErrors30D : []  as $key => $item){
            $dataTotalSmsVotesErrors30D[$item-> year."-".(($item->month<10) ? "0".$item->month : $item->month)."-". (($item->day<10)? "0".$item->day : $item->day)] = !empty($item->total_sms_votes_errors) ? $item->total_sms_votes_errors : 0 ;
        }

        $dataForTotalSendedSms30D = [];
        $dataForTotalReceivedSms30D = [];
        $dataForTotalSmsVotes30D = [];
        $dataForTotalSmsVotesErrors30D = [];

        foreach(!empty($dataTotalSendedSms30D) ? $dataTotalSendedSms30D : []  as $key => $item){
            $dataForTotalSendedSms30D[] = collect(["Data" => $key, 'name' => 'sendedSms', 'Votos' => $item])->toJson();
        }

        foreach(!empty($dataTotalReceivedSms30D) ? $dataTotalReceivedSms30D : []  as $key => $item){
            $dataForTotalReceivedSms30D[] = collect(["Data" => $key, 'name' => 'receivedSms', 'Votos' => $item])->toJson();
        }

        foreach(!empty($dataTotalSmsVotes30D) ? $dataTotalSmsVotes30D : []  as $key => $item){
            $dataForTotalSmsVotes30D[] = collect(["Data" => $key, 'name' => 'smsVotes', 'Votos' => $item])->toJson();
        }

        foreach(!empty($dataTotalSmsVotesErrors30D) ? $dataTotalSmsVotesErrors30D : []  as $key => $item){
            $dataForTotalSmsVotesErrors30D[] = collect(["Data" => $key, 'name' => 'smsVotesErrors', 'Votos' => $item])->toJson();
        }

        // Return data to show in chart
        return ["TotalSendedSms30D" => $dataForTotalSendedSms30D,
            "TotalReceivedSms30D" => $dataForTotalReceivedSms30D,
            "TotalSmsVotes30D" => $dataForTotalSmsVotes30D,
            "TotalSmsVotesErrors30D" => $dataForTotalSmsVotesErrors30D
        ];

    }

    public function create()
    {
        $languages = Orchestrator::getLanguageList();

        $title = trans('privateSms.create_sms');

        $sidebar = 'sms';
        $active = 'sms';

        Session::put('sidebarArguments', [ 'activeFirstMenu' => 'sms', 'active' => 'sms']);

        return view('private.sms.sendSms', compact('title', 'languages', 'sidebar', 'active'));

    }

    public function store(ContentRequest $request){

        $numbers=explode(",",$request['to']);

        try {
            $content = Notify::sendSMS($numbers, $request['message']);
            if($content->success){

                Session::flash('message', trans('sms.store_ok'));
                return redirect()->action('SmsController@show', "");

            }

            return redirect()->back()->withErrors(["sms.store" => $content->message]);

        } catch(Exception $e) {
            //     //TODO: save inputs
            return redirect()->back()->withErrors(["sms.store" => $e->getMessage()]);
        }
    }
}
