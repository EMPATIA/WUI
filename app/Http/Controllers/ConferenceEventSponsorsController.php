<?php

namespace App\Http\Controllers;

use App\ComModules\Events;
use App\ComModules\Files;
use App\Http\Requests\EventSponsorRequest;
use Illuminate\Http\Request;
use App\One\One;
use Datatables;
use Session;
use View;
use Breadcrumbs;
use Exception;
use Illuminate\Support\Collection;

class ConferenceEventSponsorsController extends Controller
{
    public function __construct()
    {


        View::share('title', trans('conferenceEvents.sponsorsTitle'));



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
            $uploadKey = Files::getUploadKey();

            return view('private.conferenceEvents.sponsor', compact('eventKey', 'uploadKey'));

        } catch (Exception $e) {
            return redirect()->back()->withErrors(["conferenceEvent.create" => $e->getMessage()]);
        }
    }


    /**
     * Show the form for editing the specified resource.
     * @return View
     */
    public function edit($eventKey,$sponsorKey)
    {
        try{
            $sponsor = Events::getSponsor($sponsorKey);
            $uploadKey = Files::getUploadKey();

            return view('private.conferenceEvents.sponsor', compact('eventKey', 'sponsorKey', 'sponsor','uploadKey'));
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["speaker.edit" => $e->getMessage()]);
        }
    }


    /**
     * Display the specified resource.
     */
    public function show($eventKey,$sponsorKey)
    {
        try{
            Session::put('sponsorKey', $sponsorKey);
            $sponsor = Events::getSponsor($sponsorKey);
            $response = Files::getFile($sponsor->file_id);
            $fileCode = $response->code;
            $file = ['id' => $sponsor->file_id, 'code' => $fileCode];
            return view('private.conferenceEvents.sponsor', compact('eventKey', 'sponsorKey', 'sponsor','file'));
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["sponsor.show" => $e->getMessage()]);
        }
    }



    /**
     *Store a newly created resource in storage.
     */
    public function store(EventSponsorRequest $request)
    {
        try {
            $eventKey = $request->event_key;
            $sponsor = Events::setSponsor($request);

            Session::flash('message', trans('conferenceEvents.storeOk'));
            return redirect()->action('ConferenceEventSponsorsController@show', ['eventKey'=>$eventKey,'sponsorKey'=>$sponsor->sponsor_key]);
        }
        catch(Exception $e) {
            //TODO: save inputs
            return redirect()->back()->withErrors(["conferenceEventSpeaker.store" => $e->getMessage()]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(EventSponsorRequest $request)
    {

        try {
            $eventKey = $request->event_key;
            $sponsorKey = $request->sponsor_key;

            Events::updateSponsor($request, $sponsorKey);

            Session::flash('message', trans('conferenceEvents.updateOk'));
            return redirect()->action('ConferenceEventSponsorsController@show', ['eventKey'=>$eventKey,'sponsorKey'=>$sponsorKey]);
        }
        catch(Exception $e) {
            //TODO: save inputs
            return redirect()->back()->withErrors(["conferenceEventSponsor.update" => $e->getMessage()]);
        }
    }


    /**
     * Remove the specified resource from storage.
     * @return Response
     */
    public function destroy($eventKey,$sponsorKey)
    {
        try {
            Events::deleteSponsor($sponsorKey);
            Session::flash('message', trans('conferenceEvents.deleteOk'));
            return action('ConferenceEventsController@show',['eventKey' => $eventKey]);
        } catch (Exception $e) {
            //TODO: save inputs
            return action('ConferenceEventsController@show', ['eventKey' => $eventKey])->withErrors(["conferenceEventSponsor.destroy" => $e->getMessage()]);
        }
    }


    /**
     * Show confirm popup to remove the specified resource from storage.
     */
    public function delete($eventKey,$sponsorKey){
        $data = array();
        $data['action'] = action("ConferenceEventSponsorsController@destroy", ['eventKey' => $eventKey, 'sponsorKey' => $sponsorKey]);
        $data['title'] = "DELETE";
        $data['msg'] = "Are you sure you want to delete this Sponsor?";
        $data['btn_ok'] = "Delete";
        $data['btn_ko'] = "Cancel";

        return view("_layouts.deleteModal", $data);
    }


    /**
     * @return mixed
     */
    public function getIndexTable($eventKey){

        $sponsors = Events::listSponsors($eventKey);

        $collection = Collection::make($sponsors);
        return Datatables::of($collection)
            ->editColumn('title', function ($collection) use ($eventKey) {
                return "<a href='" . action('ConferenceEventSponsorsController@show', ['eventKey' => $eventKey, 'sponsorKey' => $collection->sponsor_key]) . "'>" . $collection->name . "</a>";
            })
            ->addColumn('action', function ($collection) use ($eventKey) {
                return ONE::actionButtons(['eventKey' => $eventKey, 'sponsorKey' => $collection->sponsor_key], ['show' => 'ConferenceEventSponsorsController@show', 'delete' => 'ConferenceEventSponsorsController@delete']);
            })
            ->rawColumns(['title','action'])
            ->make(true);



    }


    public function addImageSponsor(Request $request){
        try{
            $fileId = $request->file_id;
            return $this->getFile($fileId);
        }
        catch(Exception $e) {
            return "false";
        }
    }

    public function getFile($file_id){
        try{
            $response = Files::getFile($file_id);

            $file = ['id' => $file_id,'name' => $response->name,'size' => $response->size, 'type' => $response->type];
            return $file;
        }
        catch(Exception $e) {
            return 'false';
        }
    }
}
