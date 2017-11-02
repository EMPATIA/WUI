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
use App\Http\Requests\CurrencyRequest;
use Datatables;
use Session;
use View;
use Breadcrumbs;

class CurrenciesController extends Controller
{
    public function __construct()
    {
        View::share('private.currencies', trans('privateCurrencies.currency'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        return view('private.currencies.index');
    }

    /**
     * Create a new resource.
     *
     * @return Response
     */
    public function create()
    {
        return view('private.currencies.currency');
    }

    /**
     *Store a newly created resource in storage.
     *
     * @param CurrencyRequest $request
     * @return $this|View
     */
    public function store(CurrencyRequest $request)
    {
        try {
            $currency = Orchestrator::setCurrency($request->all());
            Session::flash('message', trans('privateCurrencies.store_ok'));
            return redirect()->action('CurrenciesController@show', $currency->id);
        }
        catch(Exception $e) {
            //TODO: save inputs
            return redirect()->back()->withErrors(["currency.store" => $e->getMessage()]);
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
            $currency = Orchestrator::getCurrency($id);

            return view('private.currencies.currency', compact('currency'));
        }
        catch(\Exception $e) {
            return redirect()->back()->withErrors(["currency.show" => $e->getMessage()]);
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
            $currency = Orchestrator::getCurrency($id);

            return view('private.currencies.currency', compact('currency'));
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["currency.edit" => $e->getMessage()]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param CurrencyRequest $request
     * @param $id
     * @return $this|View
     */
    public function update(CurrencyRequest $request, $id)
    {
        try {

            $currency = Orchestrator::updateCurrency($request->all(), $id);
            Session::flash('message', trans('privateCurrencies.update_ok'));
            return redirect()->action('CurrenciesController@show', $currency->id);

        }
        catch(Exception $e) {
            //TODO: save inputs
            return redirect()->back()->withErrors(["currency.update" => $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  $id
     * @return Response
     */
    public function destroy($id)
    {
        try {
            Orchestrator::deleteCurrency($id);
            Session::flash('message', trans('privateCurrencies.delete_ok'));
            return action('CurrenciesController@index');
        }
        catch(Exception $e) {
            //TODO: save inputs
            return redirect()->back()->withErrors(["currency.destroy" => $e->getMessage()]);
        }
    }

    public function delete($id){
        $data = array();

        $data['action'] = action("CurrenciesController@destroy", $id);
        $data['title'] = "DELETE";
        $data['msg'] = "Are you sure you want to delete this Currency?";
        $data['btn_ok'] = "Delete";
        $data['btn_ko'] = "Cancel";

        return view("_layouts.deleteModal", $data);
    }

    /**
     * Get the specified resource.
     *
     *
     */
    public function tableCurrencies()
    {
        $manage = Orchestrator::getCurrencyList();

        // in case of json
        $currency = Collection::make($manage->data);

        return Datatables::of($currency)
            ->editColumn('currency', function ($currency) {
                return "<a href='".action('CurrenciesController@show', $currency->id)."'>".$currency->currency."</a>";
            })
            ->addColumn('action', function ($currency) {
                return ONE::actionButtons($currency->id, ['edit' => 'CurrenciesController@edit', 'delete' => 'CurrenciesController@delete']);
            })
            ->make(true);
    }
}
