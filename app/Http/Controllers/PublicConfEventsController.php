<?php

namespace App\Http\Controllers;

use App\ComModules\Auth;
use App\ComModules\Events;
use App\ComModules\Orchestrator;
use Datatables;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\One\One;
use Illuminate\Support\Collection;
use Session;

class PublicConfEventsController extends Controller
{
    public function __construct()
    {

    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $eventKey = 'K853mbCVXqQsatajvD0yfzfTm9cMNYHR';
        $languages = Orchestrator::getLanguageList();
        foreach ($languages as $lang) {
            if ($lang->pivot->default == 1) {
                $language = $lang;
                break;
            }
        }
        $langCode = $language->code;
        $event = Events::getEvent($eventKey);
        $eventTranslation = [];
        foreach ($event->event_translations as $translation){
            if($translation->language_code == $langCode){
                $eventTranslation = $translation;
                break;
            }

        }
        $sessions = Events::listSessions($langCode, $eventKey);
        $speakers = [];
        foreach ($sessions as $session) {
            $speakers [$session->session_key] = Events::listSpeakers($session->session_key);

        }
        $sponsors = Events::listSponsors($eventKey);
        return view('public.'.ONE::getEntityLayout().'.conferenceEvents.index', compact('sessions', 'speakers', 'sponsors', 'event','eventTranslation'));
    }


    /**
     * Create a new resource.
     *
     * @return Response
     */
    public function create()
    {

    }


    /**
     * Show the form for editing the specified resource.
     * @return View
     */
    public function edit()
    {

    }


    /**
     * Display the specified resource.
     */
    public function show($eventKey)
    {
        $authToken = Session::get('X-AUTH-TOKEN', null);
        $event = Events::getConstructor($eventKey);
        if(!empty($authToken)){
            $response = Events::verifyRegistration($eventKey);
            $registered = $response->registered;
        }
        else{
            $registered = false;
        }


        return view('public.'.ONE::getEntityLayout().'.conferenceEvents.confEvent', compact('event','registered'));
    }



    /**
     *Store a newly created resource in storage.
     */
    public function store()
    {

    }

    /**
     * Update the specified resource in storage.
     */
    public function update()
    {

    }


    /**
     * Remove the specified resource from storage.
     * @return Response
     */
    public function destroy()
    {

    }


    /**
     * Show confirm popup to remove the specified resource from storage.
     */
    public function delete(){

    }


    public function setRegistration($eventKey){
        $response = Events::setRegistration($eventKey);
        return redirect()->action('PublicConfEventsController@show', $eventKey);
    }


    public function getRegistrations($eventKey){
        $usersAllowed =[
            'isabelferreira@ces.uc.pt',
            'michelangelosecchi@ces.uc.pt',
            'sofiaantunes@ces.uc.pt',
            'giovanni.allegretti@ces.uc.pt',
            'lparede@onesource.pt',
            'pvalente@onesource.pt',
            'cordeiro@onesource.pt',
            'jez.hall@pbpartners.org.uk',
            'sankar.sivarajah@brunel.ac.uk',
            'vishanth.weerakkody@brunel.ac.uk',
            'admin@empatia.pt'
        ];

        $response = Auth::getUser();
        $email = $response->email;

        if(in_array($email, $usersAllowed)){
            $response = Events::getEvent($eventKey);

            $title = $response->event_translations[0]->name;
            return view('public.'.ONE::getEntityLayout().'.conferenceEvents.registrationData', compact('eventKey','title'));
        }
        return redirect()->back()->withErrors(["Error" => "Unauthorized"]);

    }

    public function getRegistrationTable($eventKey){
        $response = Events::getRegistrations($eventKey);
        $data=[];
        foreach ($response->registrations as $register){
            $data[] = $register->user_key;
        }

        $resultUsers = array_unique($data);

        $users = Auth::listUser($resultUsers);
        $collection = Collection::make($users);

        // in case of json
        return Datatables::of($collection)
            ->make(true);

    }




}
