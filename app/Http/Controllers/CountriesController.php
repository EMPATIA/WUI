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
use App\Http\Requests\CountryRequest;
use Datatables;
use Session;
use View;
use Breadcrumbs;

class CountriesController extends Controller
{
    public function __construct()
    {
        View::share('private.countries', trans('privateCountries.country'));


    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $title = trans('privateCountries.countries');
        return view('private.countries.index', compact('title'));
    }

    /**
     * Create a new resource.
     *
     * @return Response
     */
    public function create()
    {
        return view('private.countries.country');
    }

    /**
     *Store a newly created resource in storage.
     *
     * @param CountryRequest $request
     * @return $this|View
     */
    public function store(CountryRequest $request)
    {
        try {
            $country = Orchestrator::setCountry($request->all());
            Session::flash('message', trans('privateCountries.store_ok'));
            return redirect()->action('CountriesController@show', $country->id);
        }
        catch(Exception $e) {
            //TODO: save inputs
            return redirect()->back()->withErrors(["country.store" => $e->getMessage()]);
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
            $country = Orchestrator::getCountry($id);

            return view('private.countries.country', compact('country'));
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["country.show" => $e->getMessage()]);
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
            $country = Orchestrator::getCountry($id);

            return view('private.countries.country', compact('country'));
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["country.edit" => $e->getMessage()]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param CountryRequest $request
     * @param $id
     * @return $this|View
     */
    public function update(CountryRequest $request, $id)
    {
        try {

            $country = Orchestrator::updateCountry($request->all(), $id);
            Session::flash('message', trans('privateCountries.update_ok'));
            return redirect()->action('CountriesController@show', $country->id);
        }
        catch(Exception $e) {
            //TODO: save inputs
            return redirect()->back()->withErrors(["country.update" => $e->getMessage()]);
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
            Orchestrator::deleteCountry($id);
            Session::flash('message', trans('privateCountries.delete_ok'));
            return action('CountriesController@index');
        }
        catch(Exception $e) {
            //TODO: save inputs
            return redirect()->back()->withErrors(["country.destroy" => $e->getMessage()]);
        }
    }

    public function delete($id){
        $data = array();

        $data['action'] = action("CountriesController@destroy", $id);
        $data['title'] = "DELETE";
        $data['msg'] = "Are you sure you want to delete this Country?";
        $data['btn_ok'] = "Delete";
        $data['btn_ko'] = "Cancel";

        return view("_layouts.deleteModal", $data);
    }

    /**
     * Get the specified resource.
     *
     *
     */
    public function tableCountries(Request $request)
    {
        $manage = Orchestrator::getCountryList($request);
        // in case of json
        $countries = collect($manage->countries);
        $recordsTotal = $manage->recordsTotal;
        $recordsFiltered = $manage->recordsFiltered;

        return Datatables::of($countries)
            ->editColumn('name', function ($country) {
                return "<a href='".action('CountriesController@show', $country->id)."'>".$country->name."</a>";
            })
            ->addColumn('action', function ($country) {
                return ONE::actionButtons($country->id, ['edit' => 'CountriesController@edit', 'delete' => 'CountriesController@delete', 'form' => 'countries']);
            })
            ->rawColumns(['name','action'])
            ->with('filtered', $recordsFiltered ?? 0)
            ->skipPaging()
            ->setTotalRecords($recordsTotal ?? 0)
            ->make(true);
    }
}
