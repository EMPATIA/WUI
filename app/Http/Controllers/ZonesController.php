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
use App\Http\Requests\ZonesRequest;
use Datatables;
use Session;
use View;
use Breadcrumbs;

class ZonesController extends Controller
{
    public function __construct()
    {
        View::share('private.zones', trans('zone.zone'));



    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        return view('private.zones.index');
    }

    /**
     * Create a new resource.
     *
     * @return Response
     */
    public function create()
    {
        return view('private.zones.zone');
    }

    /**
     *Store a newly created resource in storage.
     *
     * @param ZonesRequest $request
     * @return $this|View
     */
    public function store(ZonesRequest $request)
    {
        try {
            $zone = Orchestrator::setZone($request->all());
            Session::flash('message', trans('zone.store_ok'));
            return redirect()->action('ZonesController@show', $zone->id);

        }
        catch(Exception $e) {
            //TODO: save inputs
            return redirect()->back()->withErrors(["zone.store" => $e->getMessage()]);
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
            $zone = Orchestrator::getZone($id);

            return view('private.zones.zone', compact('zone'));
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["zone.show" => $e->getMessage()]);
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
            $zone = Orchestrator::getZone($id);

            return view('private.zones.zone', compact('zone'));
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["zone.show" => $e->getMessage()]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param ZonesRequest $request
     * @param $id
     * @return $this|View
     */
    public function update(ZonesRequest $request, $id)
    {
        try {
            $zone = Orchestrator::updateZone($id, $request->all());
            Session::flash('message', trans('zone.update_ok'));
            return redirect()->action('ZonesController@show', $zone->id);
        }
        catch(Exception $e) {
            //TODO: save inputs
            return redirect()->back()->withErrors(["zone.update" => $e->getMessage()]);
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
            Orchestrator::deleteZone($id);
            Session::flash('message', trans('zone.delete_ok'));
            return redirect()->action('ZonesController@index');
        }
        catch(Exception $e) {
            //TODO: save inputs
            return redirect()->back()->withErrors(["zone.destroy" => $e->getMessage()]);
        }
    }

    /**
     * Get the specified resource.
     *
     *
     */
    public function tableZones()
    {
        $manage = Orchestrator::listZones();

        // in case of json
        $zone = Collection::make($manage);

        return Datatables::of($zone)
            ->editColumn('name', function ($zone) {
                return "<a href='".action('ZonesController@edit', $zone->id)."'>".$zone->name."</a>";
            })
            ->addColumn('action', function ($zone) {
                return ONE::actionButtons($zone->id, ['show' => 'ZonesController@show', 'delete' => 'ZonesController@destroy']);
            })
            ->make(true);
    }
}
