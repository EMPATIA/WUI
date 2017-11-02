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
use One;
use Session;

class smsController extends Controller
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
        $title = trans('privateSms.list_sms');

        return view('private.sms.index', compact('title'));
    }


    /**
     *
     * Returns data to datatable with sms list
     *
     * @return $this
     */
    public function tableSms()
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
                ->addColumn('action', function ($sentSms) {
                    return ONE::actionButtons($sentSms->sms_key, ['show' => 'SmsController@show']);
                })
                ->make(true);
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["groupTypes.tableGroupTypes" => $e->getMessage()]);
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

            return view('private.sms.sms', $data);
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["sms.show" => $e->getMessage()]);
        }
    }

    public function create()
    {

        $languages = Orchestrator::getLanguageList();

        $title = trans('privateSms.create_sms');

        return view('private.sms.sms', compact('title', 'languages'));


    }

    public function store(ContentRequest $request){


        try {
            $content = Notify::setSms($request);
            if($content=="true"){

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
