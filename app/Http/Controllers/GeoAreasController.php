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
use App\Http\Requests\GeoAreaRequest;
use Datatables;
use Session;
use View;
use Breadcrumbs;

class GeoAreasController extends Controller
{
    public function __construct()
    {
        View::share('private.geoareas', trans('geoarea.geoarea'));


    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $title = trans('privateGeoareas.list_geoareas');
        return view('private.geoareas.index', compact('title'));
    }

    /**
     * Create a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $title = trans('privateGeoareas.create_geoarea');
        return view('private.geoareas.geoarea', compact('title'));
    }

    /**
     *Store a newly created resource in storage.
     *
     * @param GeoAreaRequest $request
     * @return $this|View
     */
    public function store(GeoAreaRequest $request)
    {
        try {

            $geoArea = Orchestrator::setGeoArea($request->all());
            Session::flash('message', trans('geoaAea.store_ok'));
            return redirect()->action('GeoAreasController@show', $geoArea->id);
        }
        catch(Exception $e) {
            //TODO: save inputs
            return redirect()->back()->withErrors(["geoArea.store" => $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param $key
     * @return Response
     * @internal param int $id
     */
    public function show($key)
    {
        try {
            $geoArea = Orchestrator::getGeoArea($key);

            $title = trans('privateGeoareas.show_geoarea').' '.(isset($geoArea->name) ? $geoArea->name: null);
            return view('private.geoareas.geoarea', compact('title', 'geoArea'));
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["geoArea.show" => $e->getMessage()]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param $key
     * @return View
     * @internal param $id
     */
    public function edit($key)
    {
        try {
            $geoarea = Orchestrator::getGeoArea($key);

            $title = trans('privateGeoareas.show_geoarea').' '.(isset($geoarea->name) ? $geoarea->name: null);
            return view('private.geoareas.geoarea', compact('title','geoarea'));
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["geoarea.edit" => $e->getMessage()]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param GeoAreaRequest $request
     * @param $id
     * @return $this|View
     */
    public function update(GeoAreaRequest $request, $key)
    {
        try {

            $geoarea = Orchestrator::updateGeoArea($key, $request->name);
            Session::flash('message', trans('geoarea.update_ok'));
            return redirect()->action('GeoAreasController@show', $geoarea->geo_key);

        }
        catch(Exception $e) {
            //TODO: save inputs
            return redirect()->back()->withErrors(["geoarea.update" => $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  $id
     * @return Response
     */
    public function destroy($key){

        try {

            Orchestrator::deleteGeoArea($key);
            Session::flash('message', trans('geoarea.delete_ok'));
            return action('GeoAreasController@index');

        }
        catch(Exception $e) {
            //TODO: save inputs
            return redirect()->back()->withErrors(["geoarea.destroy" => $e->getMessage()]);
        }
    }

    public function delete($key){
        $data = array();

        $data['action'] = action("GeoAreasController@destroy", $key);
        $data['title'] = "DELETE";
        $data['msg'] = "Are you sure you want to delete this Geographic Area?";
        $data['btn_ok'] = "Delete";
        $data['btn_ko'] = "Cancel";

        return view("_layouts.deleteModal", $data);
    }

    /**
     * Get the specified resource.
     *
     *
     */
    public function tableGeoAreas()
    {
        $manage = Orchestrator::listGeoArea();

        // in case of json
        $geoarea = Collection::make($manage->data);

        return Datatables::of($geoarea)
            ->editColumn('name', function ($geoarea) {
                return "<a href='".action('GeoAreasController@show', $geoarea->geo_key)."'>".$geoarea->name."</a>";
            })
            ->addColumn('action', function ($geoarea) {
                return ONE::actionButtons($geoarea->geo_key, ['edit' => 'GeoAreasController@edit', 'delete' => 'GeoAreasController@delete']);
            })
            ->rawColumns(['name','action'])
            ->make(true);
    }
}
