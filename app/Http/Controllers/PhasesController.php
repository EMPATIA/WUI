<?php

namespace App\Http\Controllers;

use App\ComModules\CB;
use App\ComModules\Orchestrator;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\One\One;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Collection;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\UserRequest;
use App\Http\Requests\PhaseRequest;
use Datatables;
use Session;
use View;
use Breadcrumbs;

class PhasesController extends Controller
{
    public function __construct()
    {
        View::share('private.phases', trans('phase.phase'));


    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        return view('private.phases.index');
    }

    /**
     * Create a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $mp_id = 1;
        return view('private.phases.phase', compact('mp_id'));
    }

    /**
     *Store a newly created resource in storage.
     *
     * @param PhaseRequest $request
     * @return $this|View
     */
    public function store(PhaseRequest $request)
    {
        try {
            $phase = Orchestrator::setPhase($request->all());
            Session::flash('message', trans('phase.store_ok'));
            return redirect()->action('PhasesController@show', $phase->id);
        }
        catch(Exception $e) {
            //TODO: save inputs
            return redirect()->back()->withErrors(["phase.store" => $e->getMessage()]);
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
            $phase = Orchestrator::getPhase($id);

            return view('private.phases.phase', compact('phase'));
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["phase.show" => $e->getMessage()]);
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
            $phase = Orchestrator::getPhase($id);

            return view('private.phases.phase', compact('phase'));
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["phase.edit" => $e->getMessage()]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param PhaseRequest $request
     * @param $id
     * @return $this|View
     */
    public function update(PhaseRequest $request, $id)
    {
        try {
            $phase = Orchestrator::updatePhase($request->name, 1, $id);
            Session::flash('message', trans('phase.update_ok'));
            return redirect()->action('phasesController@show', $phase->id);
        }
        catch(Exception $e) {
            //TODO: save inputs
            return redirect()->back()->withErrors(["phase.update" => $e->getMessage()]);
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
            Orchestrator::deletePhase($id);
            Session::flash('message', trans('phase.delete_ok'));
            return action('PhasesController@index');
        }
        catch(Exception $e) {
            //TODO: save inputs
            return redirect()->back()->withErrors(["phase.destroy" => $e->getMessage()]);
        }
    }

    public function delete($id){
        $data = array();

        $data['action'] = action("PhasesController@destroy", $id);
        $data['title'] = "DELETE";
        $data['msg'] = "Are you sure you want to delete this Phase?";
        $data['btn_ok'] = "Delete";
        $data['btn_ko'] = "Cancel";

        return view("_layouts.deleteModal", $data);
    }

    /**
     * Get the specified resource.
     *
     *
     */
    public function tablePhases()
    {

        $manage = CB::listParameters();

        // in case of json
        $phase = Collection::make($manage);

        return Datatables::of($phase)
            ->editColumn('name', function ($phase) {
                return "<a href='".action('PhasesController@show', $phase->id)."'>".$phase->name."</a>";
            })
            ->addColumn('action', function ($phase) {
                return ONE::actionButtons($phase->id, ['edit' => 'PhasesController@edit', 'delete' => 'PhasesController@delete']);
            })
            ->make(true);
    }
}
