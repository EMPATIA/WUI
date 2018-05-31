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
use App\Http\Requests\BudgetRequest;
use Datatables;
use Session;
use View;
use Breadcrumbs;

class BudgetsController extends Controller
{
    public function __construct()
    {
        View::share('private.budgets', trans('budget.budget'));


    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        return view('private.budgets.index');
    }

    /**
     * Create a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $mp_id = 1;
        return view('private.budgets.budget', compact('mp_id'));
    }

    /**
     *Store a newly created resource in storage.
     *
     * @param BudgetRequest $request
     * @return $this|View
     */
    public function store(BudgetRequest $request)
    {
        try {
            $budget = Orchestrator::storeBudget($request);
            Session::flash('message', trans('budget.store_ok'));
            return redirect()->action('BudgetsController@show', $budget->id);
        }
        catch(Exception $e) {
            //TODO: save inputs
            return redirect()->back()->withErrors(["budget.store" => $e->getMessage()]);
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
            $budget = Orchestrator::getBudget($id);
            return view('private.budgets.budget', compact('budget'));
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["budget.show" => $e->getMessage()]);
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

            $budget = Orchestrator::getBudget($id);

            return view('private.budgets.budget', compact('budget'));
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["budget.edit" => $e->getMessage()]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param BudgetRequest $request
     * @param $id
     * @return $this|View
     */
    public function update(BudgetRequest $request, $id)
    {
        try {
            $budget = Orchestrator::updateBudget($request->name, $id);
            Session::flash('message', trans('budget.update_ok'));
            return redirect()->action('BudgetsController@show', $budget->id);
        }
        catch(Exception $e) {
            //TODO: save inputs
            return redirect()->back()->withErrors(["budget.update" => $e->getMessage()]);
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
            Orchestrator::deleteBudget($id);
            Session::flash('message', trans('budget.delete_ok'));
            return action('BudgetsController@index');
        }
        catch(Exception $e) {
            //TODO: save inputs
            return redirect()->back()->withErrors(["budget.destroy" => $e->getMessage()]);
        }
    }

    public function delete($id){
        $data = array();

        $data['action'] = action("BudgetsController@destroy", $id);
        $data['title'] = "DELETE";
        $data['msg'] = "Are you sure you want to delete this Budget?";
        $data['btn_ok'] = "Delete";
        $data['btn_ko'] = "Cancel";

        return view("_layouts.deleteModal", $data);
    }

    /**
     * Get the specified resource.
     *
     *
     */
    public function tableBudgets()
    {
        $manage = Orchestrator::listBudgets();

        // in case of json
        $budget = Collection::make($manage);

        return Datatables::of($budget)
            ->editColumn('name', function ($budget) {
                return "<a href='".action('BudgetsController@show', $budget->id)."'>".$budget->name."</a>";
            })
            ->addColumn('action', function ($budget) {
                return ONE::actionButtons($budget->id, ['edit' => 'BudgetsController@edit', 'delete' => 'BudgetsController@delete']);
            })
            ->rawColumns(['name','action'])
            ->make(true);
    }
}
