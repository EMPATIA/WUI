<?php

namespace App\Http\Controllers;

use App\ComModules\Events;
use Illuminate\Http\Request;
use App\One\One;
use Datatables;
use Session;
use View;
use Breadcrumbs;
use Exception;
use Illuminate\Support\Collection;

class ConferenceEventSessionController extends Controller
{
    public function __construct()
    {



        View::share('title', trans('conferenceEvents.sessionsTitle'));


    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {

    }


    /**
     * Create a new resource.
     *
     * @return Response
     */
    public function create($eventKey)
    {
        try {
            $languages = Orchestrator::getLanguageList();
            $event = Events::getEvent($eventKey);
            $start = strtotime($event->start_date);
            $end = strtotime($event->end_date);

            $eventStartDate = date('Y-m-d',$start);
            $eventEndDate = date('Y-m-d',$end);

            return view('private.conferenceEvents.session', compact('languages', 'eventKey','eventStartDate','eventEndDate'));


        } catch (Exception $e) {
            return redirect()->back()->withErrors(["conferenceEvent.create" => $e->getMessage()]);
        }
    }


    /**
     * Show the form for editing the specified resource.
     * @return View
     */
    public function edit($eventKey,$sessionKey)
    {
        try{

            $languages = Orchestrator::getLanguageList();

            $session = Events::getSession($sessionKey);
            $translations = collect($session->session_translations)->keyBy('language_code')->all();
            $schedules = $session->schedules;
            $event = Events::getEvent($eventKey);

            $start = strtotime($event->start_date);
            $end = strtotime($event->end_date);

            $eventStartDate = date('Y-m-d', $start);
            $eventEndDate = date('Y-m-d', $end);

            return view('private.conferenceEvents.session', compact('session', 'translations', 'languages', 'eventKey', 'schedules', 'eventStartDate', 'eventEndDate'));

        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["session.show" => $e->getMessage()]);
        }
    }


    /**
     * Display the specified resource.
     */
    public function show($eventKey,$sessionKey)
    {
        try{
            Session::put('sessionKey', $sessionKey);
            $languages = Orchestrator::getLanguageList();
            $session = Events::getSession($sessionKey);

            $translations = collect($session->session_translations)->keyBy('language_code')->all();
            $schedules = $session->schedules;

            return view('private.conferenceEvents.session', compact('session', 'translations', 'languages', 'eventKey','schedules'));
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["session.show" => $e->getMessage()]);
        }
    }



    /**
     *Store a newly created resource in storage.
     */
    public function store(Request $request, $eventKey)
    {

        try {
            $schedules = [];
            $i=0;
            if($request->newStartDate != null) {
                foreach ($request->newStartDate as $startDate) {
                    $schedules [] = ['start_date' => $startDate, 'end_date' => $request->newEndDate[$i],
                        'start_time' => $request->newStartTime[$i],
                        'end_time' => $request->newEndTime[$i]];
                    $i++;
                }
            }

            if(count($schedules)> 0) {
                $languages = Orchestrator::getLanguageList();

                $contentTranslation = [];

                foreach ($languages as $language) {
                    $contentTranslation[] = [
                        'language_code' => $language->code,
                        'name' => $request->input("title_" . $language->code) != "" ? $request->input("title_" . $language->code) : "No title assigned",
                        'description' => $request->input("description_" . $language->code) != "" ? $request->input("title_" . $language->code) : "No description assigned"
                    ];
                }
                $session = Events::storeSession($eventKey, $contentTranslation, $schedules);

                Session::flash('message', trans('conferenceEvents.storeOk'));
                return redirect()->action('ConferenceEventSessionController@show', ['eventKey'=>$eventKey,'sessionKey'=>$session->session_key]);


            }
            return redirect()->back()->withErrors(["conferenceEventSession.update" => 'No Schedules added']);

        }
        catch(Exception $e) {
            //TODO: save inputs
            return redirect()->back()->withErrors(["conferenceEventSession.update" => $e->getMessage()]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $eventKey)
    {

        try {
            $sessionKey = $request->session_key;
            $schedulesKeys = $request->input('scheduleKey');


            $schedules = [];
            if($schedulesKeys != null) {
                foreach ($schedulesKeys as $scheduleKey) {
                    $schedules [] = ['schedule_key' => $scheduleKey,
                        'start_date' => $request->input("startDate_" . $scheduleKey),
                        'end_date' => $request->input("endDate_" . $scheduleKey),
                        'start_time' => $request->input("startTime_" . $scheduleKey),
                        'end_time' => $request->input("endTime_" . $scheduleKey)];
                }
            }
            $i=0;

            if($request->newStartDate != null) {
                foreach ($request->newStartDate as $startDate) {
                    $schedules [] = ['start_date' => $startDate, 'end_date' => $request->newEndDate[$i]];
                    $i++;
                }
            }

            if(count($schedules)> 0) {
                $languages = Orchestrator::getLanguageList();

                $contentTranslation = [];

                foreach ($languages as $language) {
                    $contentTranslation[] = [
                        'language_code' => $language->code,
                        'name' => $request->input("title_" . $language->code) != "" ? $request->input("title_" . $language->code) : "No title assigned",
                        'description' => $request->input("description_" . $language->code)
                    ];
                }
                Events::updateSession($sessionKey, $contentTranslation, $schedules);

                Session::flash('message', trans('conferenceEvents.update_ok'));
                return redirect()->action('ConferenceEventSessionController@show', ['eventKey'=>$eventKey,'sessionKey'=>$sessionKey]);
            }
            return redirect()->back()->withErrors(["conferenceEventSession.update" => 'No Schedules added']);

        }
        catch(Exception $e) {
            //TODO: save inputs
            return redirect()->back()->withErrors(["conferenceEventSession.update" => $e->getMessage()]);
        }
    }


    /**
     * Remove the specified resource from storage.
     * @return Response
     */
    public function destroy($eventKey,$sessionKey)
    {
        try {
            Events::deleteSession($sessionKey);
            Session::flash('message', trans('conferenceEvents.deleteOk'));
            return action('ConferenceEventsController@show',$eventKey);
        } catch (Exception $e) {
            //TODO: save inputs
            return action('ConferenceEventsController@show', ['eventKey' => $eventKey])->withErrors(["conferenceEventSession.destroy" => $e->getMessage()]);
        }
    }


    /**
     * Show confirm popup to remove the specified resource from storage.
     */
    public function delete($eventKey,$sessionKey){
        $data = array();
        $data['action'] = action("ConferenceEventSessionController@destroy", ['eventKey' => $eventKey,'sessionKey'=>$sessionKey]);
        $data['title'] = "DELETE";
        $data['msg'] = "Are you sure you want to delete this Session?";
        $data['btn_ok'] = "Delete";
        $data['btn_ko'] = "Cancel";

        return view("_layouts.deleteModal", $data);
    }


    /**
     * @return mixed
     */
    public function getIndexTable($eventKey){

        $languages = Orchestrator::getLanguageList();
        foreach ($languages as $lang) {
            if ($lang->default == 1)
                $language = $lang;
        }
        $langCode = $language->code;

        $sessions = Events::listSessions($langCode, $eventKey);

        $info = [];
        foreach ($sessions as $session){

            $info []= ['eventKey' =>$eventKey, 'sessionKey' =>$session->session_key, 'name'=>$session->session_translations[0]->name];

        }
        $temp = json_encode($info);
        $dataTable = json_decode($temp);

        $collection = Collection::make($dataTable);
        return Datatables::of($collection)
            ->editColumn('title', function ($collection) {
                return "<a href='" . action('ConferenceEventSessionController@show', ['sessionKey' => $collection->sessionKey, 'eventKey' => $collection->eventKey]) . "'>" . $collection->name . "</a>";
            })
            ->addColumn('action', function ($collection) {
                return ONE::actionButtons(['sessionKey' => $collection->sessionKey, 'eventKey' => $collection->eventKey], ['show' => 'ConferenceEventSessionController@show', 'delete' => 'ConferenceEventSessionController@delete']);
            })
            ->rawColumns(['title','action'])
            ->make(true);
    }
}

