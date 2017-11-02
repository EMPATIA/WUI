<?php

namespace App\Http\Controllers;

use App\ComModules\Events;
use Exception;
use Illuminate\Http\Request;
use App\One\One;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use Datatables;
use Session;
use View;
use Breadcrumbs;
class RegistrationsController extends Controller
{
    public function __construct()
    {

    }

    /**
     * Create a new resource.
     *
     * @return Response
     */
    public function create($eventKey)
    {
        return view('public.registrations.registration',compact('eventKey'));
    }

    /**
     *Store a newly created registration in storage.
     *
     * @param Request $request
     * @return $this|View
     */
    public function store(Request $request)
    {

        try {
            $registration = Events::storeRegistration($request->all());
            Session::flash('message', trans('registration.store_ok'));
            return redirect()->action('PublicConfEventsController@show', $registration->registration_key);
        }
        catch(Exception $e) {
            dd($e);
            return redirect()->back()->withErrors(["registration.create" => $e->getMessage()]);
        }
    }
}
