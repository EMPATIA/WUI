<?php

namespace App\Http\Controllers;

use App\ComModules\Events;
use App\ComModules\Files;
use App\ComModules\Orchestrator;
use App\Http\Requests\ConferenceEventRequest;
use Illuminate\Http\Request;
use App\One\One;
use Datatables;
use Session;
use View;
use Breadcrumbs;
use Exception;
use Illuminate\Support\Collection;

class ConferenceEventsController extends Controller
{


    public function __construct()
    {



        View::share('title', trans('conferenceEvents.eventsTitle'));


    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        return view('private.conferenceEvents.index');
    }


    /**
     * Create a new resource.
     *
     * @return Response
     */
    public function create()
    {
        try {
            $languages = Orchestrator::getLanguageList();

            $uploadKey = Files::getUploadKey();
            return view('private.conferenceEvents.conferenceEvent', compact('languages','uploadKey'));

        } catch (Exception $e) {
            return redirect()->back()->withErrors(["conferenceEvent.create" => $e->getMessage()]);
        }
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param $id
     * @return View
     */
    public function edit($eventKey)
    {
        try{
            $languages = Orchestrator::getLanguageList();

            $event = Events::getEvent($eventKey);

            $uploadKey = Files::getUploadKey();
            $translations = collect($event->event_translations)->keyBy('language_code')->all();

            return view('private.conferenceEvents.conferenceEvent', compact('event', 'translations', 'languages', 'eventKey','uploadKey'));
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["idea.show" => $e->getMessage()]);
        }
    }


    /**
     * Display the specified resource.
     */
    public function show($eventKey)
    {
        try{
            Session::put('eventKey', $eventKey);
            $languages = Orchestrator::getLanguageList();
            $event = Events::getEvent($eventKey);
            $translations = collect($event->event_translations)->keyBy('language_code')->all();
            $response = Files::getFile($event->file_id);

            $fileCode = $response->code;
            $file = ['id' => $event->file_id, 'code' => $fileCode];
            return view('private.conferenceEvents.conferenceEvent', compact('event', 'translations', 'languages', 'eventKey','file'));

        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["idea.show" => $e->getMessage()]);
        }
    }



    /**
     *Store a newly created resource in storage.
     */
    public function store(ConferenceEventRequest $request)
    {
        try {

            $languages = Orchestrator::getLanguageList();

            $contentTranslation = [];

            foreach ($languages as $language) {
                $contentTranslation[] = [
                    'language_code' => $language->code,
                    'name' => $request->input("title_" . $language->code) != "" ? $request->input("title_" . $language->code) : "No title assigned",
                    'description' => $request->input("description_" . $language->code),
                    'footer' => $request->input("footer_" . $language->code) != "" ? $request->input("footer_" . $language->code) : "No additional info assigned"
                ];
            }
            $eventKey = Events::setNewEvent($request, $contentTranslation);

            Session::flash('message', trans('event.store_ok'));
            return redirect()->action('ConferenceEventsController@show',$eventKey);
        }
        catch(Exception $e) {
            //TODO: save inputs
            return redirect()->back()->withErrors(["conferenceEvent.store" => $e->getMessage()]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ConferenceEventRequest $request, $eventKey)
    {
        try {
            $languages = Orchestrator::getLanguageList();

            $contentTranslation = [];

            foreach ($languages as $language) {
                $contentTranslation[] = [
                    'language_code' => $language->code,
                    'name' => $request->input("title_" . $language->code) != "" ? $request->input("title_" . $language->code) : "No title assigned",
                    'description' => $request->input("description_" . $language->code) != "" ? $request->input("description_" . $language->code) : "No description assigned",
                    'footer' => $request->input("footer_" . $language->code) != "" ? $request->input("footer_" . $language->code) : "No additional info assigned"
                ];
            }

            Events::updateEvent($eventKey, $request, $contentTranslation);
            Session::flash('message', trans('conferenceEvent.update_ok'));
            return redirect()->action('ConferenceEventsController@show', $eventKey);


        }
        catch(Exception $e) {
            //TODO: save inputs
            return redirect()->back()->withErrors(["conferenceEvent.update" => $e->getMessage()]);
        }
    }


    /**
     * Remove the specified resource from storage.
     * @return Response
     */
    public function destroy($eventKey)
    {
        try {

            Events::deleteEvent($eventKey);
            Session::flash('message', trans('event.delete_ok'));
            return action('ConferenceEventsController@index');

        } catch (Exception $e) {
            //TODO: save inputs
            return redirect()->back()->withErrors(["conferenceEvents.destroy" => $e->getMessage()]);
        }
    }


    /**
     * Show confirm popup to remove the specified resource from storage.
     */
    public function delete($voteKey){
        $data = array();

        $data['action'] = action("ConferenceEventsController@destroy", $voteKey);
        $data['title'] = "DELETE";
        $data['msg'] = "Are you sure you want to delete this Event?";
        $data['btn_ok'] = "Delete";
        $data['btn_ko'] = "Cancel";

        return view("_layouts.deleteModal", $data);
    }


    /**
     * @return mixed
     */
    public function getIndexTable(){


        $languages = Orchestrator::getLanguageList();
        foreach ($languages as $lang){
            if($lang->default == 1)
                $language = $lang;
        }
        $langCode = $language->code;

        $events = Events::listEvents($langCode);

        $collection = Collection::make($events);
        return Datatables::of($collection)
            ->editColumn('title', function ($collection) {
                return "<a href='" . action('ConferenceEventsController@show', $collection->event_key) . "'>" . $collection->event_translations[0]->name . "</a>";
            })
            ->addColumn('action', function ($collection) {
                return ONE::actionButtons($collection->event_key, ['show' => 'ConferenceEventsController@show', 'delete' => 'ConferenceEventsController@delete']);
            })
            ->make(true);

    }


    public function addEventImage(Request $request){
        try{
            return $this->getEventFile($request->fileId);
        }
        catch(Exception $e) {
            return "false";
        }
    }

    public function getEventFile($fileId){
        try{
            $response = Files::getFile($fileId);
            $file = ['id' => $fileId,'name' => $response->name,'size' => $response->size, 'type' => $response->type];
            return $file;
        }
        catch(Exception $e) {
            return 'false';
        }
    }



}
