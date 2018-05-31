<?php

namespace App\Http\Controllers;

use App\ComModules\Events;
use App\Http\Requests\EventSpeakerRequest;
use App\One\One;
use Datatables;
use Exception;
use Illuminate\Support\Collection;
use Session;
use View;

class ConferenceEventSpeakerController extends Controller
{
    public function __construct()
    {


        View::share('title', trans('conferenceEvents.speakerTitle'));

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
    public function create($eventKey,$sessionKey)
    {

        try {

            return view('private.conferenceEvents.speaker', compact('eventKey', 'sessionKey'));

        } catch (Exception $e) {
            return redirect()->back()->withErrors(["conferenceEvent.create" => $e->getMessage()]);
        }
    }


    /**
     * Show the form for editing the specified resource.
     * @return View
     */
    public function edit($eventKey,$sessionKey,$speakerKey)
    {
        try{
            $speaker = Events::getSpeaker($speakerKey);
            return view('private.conferenceEvents.speaker', compact('eventKey', 'sessionKey', 'speaker'));
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["speaker.edit" => $e->getMessage()]);
        }
    }


    /**
     * Display the specified resource.
     */
    public function show($eventKey,$sessionKey,$speakerKey)
    {

        try{
            $speaker = Events::getSpeaker($speakerKey);
            return view('private.conferenceEvents.speaker', compact('eventKey', 'sessionKey', 'speaker'));

        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["session.show" => $e->getMessage()]);
        }
    }



    /**
     *Store a newly created resource in storage.
     */
    public function store(EventSpeakerRequest $request)
    {
        try {

            $sessionKey = $request->session_key;
            $eventKey = $request->event_key;

            $speaker = Events::setSpeaker($request);

            Session::flash('message', trans('conferenceEventSpeaker.storeOk'));
            return redirect()->action('ConferenceEventSpeakerController@show', ['eventKey'=>$eventKey,'sessionKey'=>$sessionKey,'speakerKey' =>$speaker->speaker_key]);
        }
        catch(Exception $e) {
            //TODO: save inputs
            return redirect()->back()->withErrors(["conferenceEventSpeaker.store" => $e->getMessage()]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(EventSpeakerRequest $request)
    {
        try {
            $sessionKey = $request->session_key;
            $eventKey = $request->event_key;
            $speakerKey = $request->speaker_key;

            Events::updateSpeaker($request, $speakerKey);

            Session::flash('message', trans('conferenceEvents.updateOk'));
            return redirect()->action('ConferenceEventSpeakerController@show', ['eventKey'=>$eventKey,'sessionKey'=>$sessionKey,'speakerKey' =>$speakerKey]);
        }
        catch(Exception $e) {
            //TODO: save inputs
            return redirect()->back()->withErrors(["conferenceEventSpeaker.update" => $e->getMessage()]);
        }
    }


    /**
     * Remove the specified resource from storage.
     * @return Response
     */
    public function destroy($eventKey,$sessionKey,$speakerKey)
    {
        try {
            Events::deleteSpeaker($speakerKey);
            Session::flash('message', trans('conferenceEvents.deleteOk'));
            return action('ConferenceEventSessionController@show',['eventKey' => $eventKey,'sessionKey' => $sessionKey]);
        } catch (Exception $e) {
            //TODO: save inputs
            return action('ConferenceEventSessionController@show', ['eventKey' => $eventKey,'sessionKey' => $sessionKey])->withErrors(["conferenceEventSpeaker.destroy" => $e->getMessage()]);
        }
    }


    /**
     * Show confirm popup to remove the specified resource from storage.
     */
    public function delete($eventKey,$sessionKey,$speakerKey){
        $data = array();
        $data['action'] = action("ConferenceEventSpeakerController@destroy", ['eventKey' => $eventKey,'sessionKey'=>$sessionKey, 'speakerKey' => $speakerKey]);
        $data['title'] = "DELETE";
        $data['msg'] = "Are you sure you want to delete this Speaker?";
        $data['btn_ok'] = "Delete";
        $data['btn_ko'] = "Cancel";

        return view("_layouts.deleteModal", $data);
    }


    /**
     * @return mixed
     */
    public function getIndexTable($eventKey,$sessionKey){

            $speakers = Events::listSpeakers($sessionKey);

            $collection = Collection::make($speakers);
            return Datatables::of($collection)
                ->editColumn('title', function ($collection) use ($eventKey,$sessionKey) {
                    return "<a href='" . action('ConferenceEventSpeakerController@show', ['eventKey' => $eventKey, 'sessionKey' => $sessionKey, 'speakerKey' => $collection->speaker_key]) . "'>" . $collection->name . "</a>";
                })
                ->addColumn('action', function ($collection) use ($eventKey,$sessionKey) {
                    return ONE::actionButtons(['eventKey' => $eventKey,'sessionKey' => $sessionKey, 'speakerKey' => $collection->speaker_key], ['show' => 'ConferenceEventSpeakerController@show', 'delete' => 'ConferenceEventSpeakerController@delete']);
                })
                ->rawColumns(['title','action'])
                ->make(true);

    }
}
