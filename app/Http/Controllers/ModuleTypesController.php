<?php

namespace App\Http\Controllers;

use App\ComModules\Orchestrator;
use App\One\One;
use Datatables;
use Exception;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Collection;
use Session;

class ModuleTypesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $moduleKey = $request->moduleKey;

        $sidebar = 'modules';
        $active = 'types';

        Session::put('sidebarArguments', ['moduleKey' => $moduleKey, 'activeFirstMenu' => 'types']);

        return view('private.modules.moduleTypesIndex', compact('moduleKey', 'sidebar', 'active'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($moduleKey)
    {
        $sidebar = 'modules';
        $active = 'types';

        Session::put('sidebarArguments', ['moduleKey' => $moduleKey, 'activeFirstMenu' => 'types']);

        return view('private.modules.moduleType',compact('moduleKey', 'sidebar', 'active'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        try {
            $moduleKey = $request->moduleKey;
            $moduleType = Orchestrator::setNewModuleType($request,$moduleKey);
            Session::flash('message', trans('privateModuleTypes.storeOk'));
            return redirect()->action('ModulesController@show', $moduleType->module_key);

        }
        catch(Exception $e) {
            return redirect()->back()->withErrors([ trans('privateModuleTypes.storeNok') => $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param $key
     * @return \Illuminate\Http\RedirectResponse
     */
    public function show($key)
    {
        try {
            $moduleType = Orchestrator::getModuleType($key);
            $moduleKey = $moduleType->module->module_key;

            $sidebar = 'modules';
            $active = 'types';

            Session::put('sidebarArguments', ['moduleKey' => $moduleKey, 'activeFirstMenu' => 'types']);

            return view('private.modules.moduleType', compact('moduleType', 'moduleKey', 'sidebar', 'active'));
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors([ trans('privateModuleTypes.show') => $e->getMessage()]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function edit($key)
    {
        try {
            $moduleType = Orchestrator::getModuleType($key);
            $moduleKey = $moduleType->module->module_key;

            $sidebar = 'modules';
            $active = 'types';

            Session::put('sidebarArguments', ['moduleKey' => $moduleKey, 'activeFirstMenu' => 'types']);

            return view('private.modules.moduleType', compact('moduleType', 'moduleKey', 'sidebar', 'active'));
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors([ trans('privateModuleTypes.show') => $e->getMessage()]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $key)
    {
        try {
            $moduleType = Orchestrator::updateModuleType($request, $key);
            Session::flash('message', trans('privateModuleTypes.updateOk'));
            return redirect()->action('ModuleTypesController@show', $key);

        }
        catch(Exception $e) {
            //TODO: save inputs
            return redirect()->back()->withErrors([ trans('privateModuleTypes.updateNok') => $e->getMessage()]);
        }
    }

    /**
     * Show delete resource confirmation
     * Remove the specified resource from storage.
     * @param $key
     * @return View
     */
    public function delete($key)
    {
        $data = array();

        $data['action'] = action("ModuleTypesController@destroy", $key);
        $data['title'] = "DELETE";
        $data['msg'] = "Are you sure you want to delete this Module Type?";
        $data['btn_ok'] = "Delete";
        $data['btn_ko'] = "Cancel";
        return view("_layouts.deleteModal", $data);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $key
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($key)
    {
        try {
            $moduleType = Orchestrator::getModuleType($key);
            Orchestrator::deleteModuleType($key);
            Session::flash('message', trans('privateModuleTypes.deleteOk'));
            return action('ModulesController@show',$moduleType->module->module_key);

        }
        catch(Exception $e) {
            return redirect()->back()->withErrors([ trans('privateModuleTypes.deleteNOk') => $e->getMessage()]);
        }
    }

    public function showModuleType($key)
    {
        try {
            $moduleType = Orchestrator::getModuleType($key);
            $title = 'reervre';
            return view('private.modules.moduleType', compact('moduleType', 'title'));
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors([ trans('privateModules.show') => $e->getMessage()]);
        }
    }


    /**
     * Display a listing of the resource.
     * @param $moduleKey
     * @return mixed
     */
    public function getIndexTable($moduleKey)
    {
        $moduleTypes = Orchestrator::getModuleTypesList($moduleKey);
        // in case of json
        $collection = Collection::make($moduleTypes);

        return Datatables::of($collection)
            ->editColumn('name', function ($collection) {
                return "<a href='".action('ModuleTypesController@show', $collection->module_type_key)."'>".$collection->name."</a>";
            })
            ->addColumn('action', function ($collection) {
                return ONE::actionButtons($collection->module_type_key, ['form' => 'moduleType' ,'edit' => 'ModuleTypesController@edit', 'delete' => 'ModuleTypesController@delete']);
            })
            ->rawColumns(['name','action'])
            ->make(true);
    }


}
