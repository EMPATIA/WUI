<?php

namespace App\Http\Controllers;

use App\ComModules\Auth;
use App\ComModules\Notify;
use Datatables;
use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Collection;
use App\Http\Requests\ContentRequest;
use App\ComModules\Orchestrator;
use One;
use Session;
use Carbon\Carbon;

class emailsController extends Controller
{

    /**
     * emailsController constructor.
     */
    public function __construct()
    {

    }


    /**
     * Returns Emails List View
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        //Page title
        $title = trans('privateEmails.list_emails');
        $sidebar = 'email';
        $active = 'sent';

        Session::put('sidebarArguments', ['activeFirstMenu' => 'sent']);

        return view('private.emails.index', compact('title', 'sidebar', 'active'));
    }


    /**
     *
     * Returns data to datatable with emails list
     *
     * @param Request $request
     * @return $this
     */
    public function tableEmails(Request $request)
    {
        try {
            //Get all sent emails
            if (Session::get('user_role') == 'admin'){
                $response = Notify::getEmails($request);

                $sentEmails = collect($response->emails);
                $recordsTotal = $response->recordsTotal;
                $recordsFiltered = $response->recordsFiltered;
            } else
                $sentEmails = Collection::make([]);

            return Datatables::of($sentEmails)
                ->editColumn('recipient', function ($sentEmails) {
                    if(count(json_decode($sentEmails->recipient)) >1 )
                        return "<a href='" . action('EmailsController@show', $sentEmails->email_key) . "'>" . 'Multiple Recipients' . "</a>";
                    else
                        return "<a href='" . action('EmailsController@show', $sentEmails->email_key) . "'>" . $sentEmails->recipient . "</a>";
                })
                ->editColumn('subject', function ($sentEmails) {
                    return $sentEmails->subject ?? null;
                })
                ->editColumn('sent', function ($sentEmails) {
                    return $sentEmails->sent == '1' ? $sentEmails->updated_at : trans("privateEmails.not_sent");
                })
                ->addColumn('action', function ($sentEmails) {
                    return ONE::actionButtons($sentEmails->email_key, ['show' => 'EmailsController@show']);
                })
                ->with('filtered', $recordsFiltered ?? 0)
                ->skipPaging()
                ->setTotalRecords($recordsTotal ?? 0)
                ->rawColumns(['recipient','action'])
                ->make(true);
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["groupTypes.tableGroupTypes" => $e->getMessage()]);
        }
    }

    /**
     * Shows Email details from a given email Key
     *
     * @param Request $request
     * @param $emailKey
     * @return $this
     */
    public function show(Request $request, $emailKey)
    {
        try {
            $email = Notify::getEmail($emailKey);

            // Form title (layout)
            $title = trans('privateEmail.show_email');

            try {
                $user = Auth::getUserByKey($email->created_by);
                $userData = $user->name.'   ('.$user->email.') ';
            } catch (Exception $e){
                $userData = $email->created_by;
            }

            // Return the view with data
            $data = [];
            $data['title'] = $title;
            $data['email'] = $email;
            $data['userData'] = $userData;

            return view('private.emails.email', $data);
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["email.show" => $e->getMessage()]);
        }
    }

    /**
     * Shows Email details from a given email Key
     *
     * @param Request $request
     * @param $emailKey
     * @return $this
     */
    public function showSummary(Request $request)
    {
        try {

//            HEADER-------------------------------------------------------------------------------------
            $totalSentEmails = Notify::countTotalSentEmails();
            $totalNotSentEmails = Notify::countTotalNotSentEmails();
            $totalEmailsErrors = Notify::countTotalMailsErrors();

            $data["totalSentEmails"] = $totalSentEmails;
            $data["totalNotSentEmails"] = $totalNotSentEmails;
            $data["totalEmailsErrors"] = $totalEmailsErrors;

            $sidebar = 'email';
            $active = 'summary';
            $view = 'private.emails.summary';

            Session::put('sidebarArguments', [ 'activeFirstMenu' => 'summary']);

            return view($view, $data, compact('sidebar','active'));

        }catch(Exception $e){
            return redirect()->back()->withErrors(["sms.show" => $e->getMessage()]);
        }
    }

    public function showStats(Request $request)
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
            $totalSentEmails30D = Notify::countTotalSentEmails30DPersonalized($start_date, $end_date);
            $totalNotSentEmails30D = Notify::countTotalNotSentEmails30DPersonalized($start_date, $end_date);
            $totalEmailsErrors30D = Notify::countTotalEmailsErrors30DPersonalized($start_date, $end_date);
        }else{
            $totalSentEmails30D = null;
            $totalNotSentEmails30D = null;
            $totalEmailsErrors30D = null;
        }

        $dataTotalSentEmails30D = [];
        $dataTotalNotSentEmails30D = [];
        $dataTotalEmailsErrors30D = [];

        $sd = Carbon::parse($start_date);
        $ed = Carbon::parse($end_date);

        $days = $sd->diff($ed)->days;
        $sd->subDay(1);

        for($i = 0; $i <= $days; $i++)
        {
            $date = '';

            $date = $sd->addDays(1);

            $dataTotalSentEmails30D[$date->format('Y-m-d')] = 0;
            $dataTotalNotSentEmails30D[$date->format('Y-m-d')] = 0;
            $dataTotalEmailsErrors30D[$date->format('Y-m-d')] = 0;
        }

        foreach(!empty($totalSentEmails30D) ? $totalSentEmails30D : []  as $key => $item){
            $dataTotalSentEmails30D[$item-> year."-".(($item->month<10) ? "0".$item->month : $item->month)."-". (($item->day<10)? "0".$item->day : $item->day)] = !empty($item->total_sent_emails) ? $item->total_sent_emails : 0 ;
        }

        foreach(!empty($totalNotSentEmails30D) ? $totalNotSentEmails30D : []  as $key => $item){
            $dataTotalNotSentEmails30D[$item-> year."-".(($item->month<10) ? "0".$item->month : $item->month)."-". (($item->day<10)? "0".$item->day : $item->day)] = !empty($item->total_not_sent_emails) ? $item->total_not_sent_emails : 0 ;
        }

        foreach(!empty($totalEmailsErrors30D) ? $totalEmailsErrors30D : []  as $key => $item){
            $dataTotalEmailsErrors30D[$item-> year."-".(($item->month<10) ? "0".$item->month : $item->month)."-". (($item->day<10)? "0".$item->day : $item->day)] = !empty($item->total_emails_errors) ? $item->total_emails_errors : 0 ;
        }

        $dataForTotalSentEmails30D = [];
        $dataForTotalNotSentEmails30D = [];
        $dataForTotalEmailsErrors30D = [];

        foreach(!empty($dataTotalSentEmails30D) ? $dataTotalSentEmails30D : []  as $key => $item){
            $dataForTotalSentEmails30D[] = collect(["Data" => $key, 'name' => 'SentEmails', 'Emails' => $item])->toJson();
        }

        foreach(!empty($dataTotalNotSentEmails30D) ? $dataTotalNotSentEmails30D : []  as $key => $item){
            $dataForTotalNotSentEmails30D[] = collect(["Data" => $key, 'name' => 'NotSentEmails', 'Emails' => $item])->toJson();
        }

        foreach(!empty($dataTotalEmailsErrors30D) ? $dataTotalEmailsErrors30D : []  as $key => $item){
            $dataForTotalEmailsErrors30D[] = collect(["Data" => $key, 'name' => 'ErrorEmails', 'Emails' => $item])->toJson();
        }

        // Return data to show in chart
        return ["TotalSentEmails30D" => $dataForTotalSentEmails30D,
            "TotalNotSentEmails30D" => $dataForTotalNotSentEmails30D,
            "TotalEmailsErrors30D" => $dataForTotalEmailsErrors30D,
        ];

    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $languages = Orchestrator::getLanguageList();
        $users = Orchestrator::getListOfAvailableUsersToSendEmails();
        $title = trans('privateEmail.create_email');
        $sidebar = 'email';
        $active = 'send';

        Session::put('sidebarArguments', ['activeFirstMenu' => 'send']);

        return view('private.emails.email', compact('title', 'languages','users', 'sidebar', 'active'));
    }

    /**
     * @param Request $request
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function store(Request $request){
        try {
            $site = Orchestrator::getSite(Session::get('X-SITE-KEY'));
            if(isset($request->send_to_all)){
                dd('PLEASE WAIT FOR SUPPORT');
                $request->users = collect(Orchestrator::getListOfAvailableUsersToSendEmails())->pluck('email');
            }
            Notify::createEmails($request,$site);

            Session::flash('message', trans('email.store_ok'));
            return redirect()->action('EmailsController@index');
        }
        catch(Exception $e) {
            //   //TODO: save inputs
            return redirect()->back()->withErrors(["email.store" => $e->getMessage()]);
        }
    }
}
