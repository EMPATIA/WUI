<?php

namespace App\Http\Controllers;

use App\ComModules\Orchestrator;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\One\One;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Collection;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\UserRequest;
use App\Http\Requests\TimezoneRequest;
use Datatables;
use Session;
use Symfony\Component\Routing\Loader\ObjectRouteLoader;
use View;
use Breadcrumbs;

class TimezonesController extends Controller
{
    public function __construct()
    {
        View::share('private.timezones', trans('privateTimezones.timezone'));


    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        return view('private.timezones.index');
    }

    /**
     * Create a new resource.
     *
     * @return Response
     */
    public function create()
    {
        return view('private.timezones.timezone');
    }

    /**
     *Store a newly created resource in storage.
     *
     * @param TimezoneRequest $request
     * @return $this|View
     */
    public function store(TimezoneRequest $request)
    {
        try {
            $timezone = Orchestrator::setTimezone($request->all());
            Session::flash('message', trans('privateTimezones.store_ok'));
            return redirect()->action('TimezonesController@show', $timezone->id);
        }
        catch(Exception $e) {
            //TODO: save inputs
            return redirect()->back()->withErrors(["timezone.store" => $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        try {
            $timezone = Orchestrator::getTimezone($id);

            return view('private.timezones.timezone', compact('timezone'));
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["timezone.show" => $e->getMessage()]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param $id
     * @return View
     */
    public function edit($id)
    {
        try {

            $timezone = Orchestrator::getTimezone($id);

            return view('private.timezones.timezone', compact('timezone'));
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["timezone.edit" => $e->getMessage()]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param TimezoneRequest $request
     * @param $id
     * @return $this|View
     */
    public function update(TimezoneRequest $request, $id)
    {

        try {
            $timezone = Orchestrator::updateTimezone($id, $request->all()) ;
            Session::flash('message', trans('privateTimezones.update_ok'));
            return redirect()->action('TimezonesController@show', $timezone->id);
        }
        catch(Exception $e) {
            //TODO: save inputs
            return redirect()->back()->withErrors(["timezone.update" => $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  $id
     * @return Response
     */
    public function destroy($id){

        try {
            Orchestrator::deleteTimezone($id);
            Session::flash('message', trans('privateTimezones.delete_ok'));
            return action('TimezonesController@index');
        }
        catch(Exception $e) {
            //TODO: save inputs
            return redirect()->back()->withErrors(["timezone.destroy" => $e->getMessage()]);
        }
    }

    public function delete($id){
        $data = array();

        $data['action'] = action("TimezonesController@destroy", $id);
        $data['title'] = "DELETE";
        $data['msg'] = "Are you sure you want to delete this Timezone?";
        $data['btn_ok'] = "Delete";
        $data['btn_ko'] = "Cancel";

        return view("_layouts.deleteModal", $data);
    }

    /**
     * Get the specified resource.
     *
     *
     */
    public function tableTimezones()
    {

        $manage = Orchestrator::getTimeZoneList();
        // in case of json
        $timezone = Collection::make($manage);

        foreach ($timezone as $item){
            $aux = explode("/", $item->name);
            $item->continent=$aux[0];
            $item->name= $aux[1];
        }

        return Datatables::of($timezone)
            ->editColumn('country_code', function ($timezone) {
                return "<a href='".action('TimezonesController@show', $timezone->id)."'>".$timezone->country_code."</a>";
            })
            ->addColumn('action', function ($timezone) {
                return ONE::actionButtons($timezone->id, ['edit' => 'TimezonesController@edit', 'delete' => 'TimezonesController@delete']);
            })
            ->make(true);
    }
}
