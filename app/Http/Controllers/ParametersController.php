<?php

namespace App\Http\Controllers;


use App\ComModules\CB;
use App\Http\Requests\ParameterRequest;
use App\One\One;
use Illuminate\Support\Collection;
use Datatables;
use Session;
use View;
use Breadcrumbs;


class ParametersController extends Controller
{

    public function __construct()
    {
        View::share('private.parameters', trans('parameters.parameters'));



    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('private.parameters.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        try {
            $data = CB::getParametersTypes();
            $types = [];
            foreach($data as $type){
                $types[$type->id] = $type->name;
            }

            return view('private.parameters.parameter', compact('types'));
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["parameters.create" => $e->getMessage()]);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ParameterRequest $request)
    {

        try {
            $parameter = CB::setParameters($request->all());

            Session::flash('message', trans('parameter.store_ok'));
            return redirect()->action('ParametersController@show', $parameter->id);
        }
        catch(Exception $e) {
            //TODO: save inputs
            return redirect()->back()->withErrors(["parameter.store" => $e->getMessage()]);
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {

            $parameter = CB::getParam($id);

            $data = CB::getParametersTypes();
            $types = [];
            foreach ($data as $type) {
                $types[$type->id] = $type->name;
            }

            return view('private.parameters.parameter', compact('types', 'parameter'));

        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["parameter.show" => $e->getMessage()]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        try {

            $parameter = CB::getParam($id);

            $data = CB::getParametersTypes();
            $types = [];
            foreach($data as $type){
                $types[$type->id] = $type->name;
            }

            return view('private.parameters.parameter', compact('types', 'parameter'));
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["parameter.edit" => $e->getMessage()]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ParameterRequest $request, $id)
    {
        try {
            $parameter = CB::updateParam($id, $request->all());
            Session::flash('message', trans('country.update_ok'));
            return redirect()->action('ParametersController@show', $parameter->id);
        }
        catch(Exception $e) {
            //TODO: save inputs
            return redirect()->back()->withErrors(["parameter.update" => $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            CB::deleteParam($id);
            Session::flash('message', trans('parameter.delete_ok'));
            return action('ParametersController@index');
        }
        catch(Exception $e) {
            return action('ParametersController@index');
        }
    }


    public function delete($id){
        $data = array();

        $data['action'] = action("ParametersController@destroy", $id);
        $data['title'] = "DELETE";
        $data['msg'] = "Are you sure you want to delete this parameter?";
        $data['btn_ok'] = "Delete";
        $data['btn_ko'] = "Cancel";

        return view("_layouts.deleteModal", $data);
    }




    public function parametersDataTable()
    {
        $manage = CB::listParameters();
        // in case of json
        $parameters = Collection::make($manage);

        return Datatables::of($parameters)
            ->editColumn('parameter', function ($parameter) {
                return "<a href='".action('ParametersController@show', $parameter->id)."'>".$parameter->parameter."</a>";
            })
            ->addColumn('action', function ($parameter) {
                return ONE::actionButtons($parameter->id, ['edit' => 'ParametersController@edit', 'delete' => 'ParametersController@delete']);
            })
            ->make(true);
    }
}
