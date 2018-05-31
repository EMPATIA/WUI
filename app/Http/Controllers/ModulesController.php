<?php

namespace App\Http\Controllers;

use App\ComModules\Orchestrator;
use App\Http\Requests\ModuleRequest;
use App\One\One;
use Datatables;
use Exception;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Collection;
use Session;

class ModulesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title = trans('privateModules.modules');
        return view('private.modules.index', compact('title'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('private.modules.module');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param ModuleRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(ModuleRequest $request)
    {
        try {
            $module = Orchestrator::setNewModule($request);
            Session::flash('message', trans('privateModules.store_ok'));
            return redirect()->action('ModulesController@show', $module->module_key);

        }
        catch(Exception $e) {
            return redirect()->back()->withErrors([ trans('privateModules.store_nok') => $e->getMessage()]);
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
            $module = Orchestrator::getModule($key);
            $moduleKey = $module->module_key;

            $sidebar = 'modules';
            $active = 'details';

            Session::put('sidebarArguments', ['moduleKey' => $moduleKey, 'activeFirstMenu' => 'details']);

            return view('private.modules.module', compact('module', 'moduleKey', 'sidebar', 'active'));
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors([ trans('privateModules.show') => $e->getMessage()]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param $key
     * @return \Illuminate\Http\RedirectResponse
     */
    public function edit($key)
    {
        try {
            $module = Orchestrator::getModule($key);

            return view('private.modules.module', compact('module'));
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors([ trans('privateModules.show') => $e->getMessage()]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param ModuleRequest|Request $request
     * @param $key
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(ModuleRequest $request, $key)
    {
        try {

            $module = Orchestrator::updateModule($request, $key);
            Session::flash('message', trans('privateModules.update_ok'));
            return redirect()->action('ModulesController@show', $key);

        }
        catch(Exception $e) {
            //TODO: save inputs
            return redirect()->back()->withErrors([ trans('privateModules.update_nok') => $e->getMessage()]);
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

        $data['action'] = action("ModulesController@destroy", $key);
        $data['title'] = "DELETE";
        $data['msg'] = "Are you sure you want to delete this Module?";
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
            Orchestrator::deleteModule($key);
            Session::flash('message', trans('privateModules.delete_ok'));
            return action('ModulesController@index');

        }
        catch(Exception $e) {
            return redirect()->back()->withErrors([ trans('privateModules.delete_nok') => $e->getMessage()]);
        }
    }

    /**
     * Display a listing of the resource.
     * @return mixed
     * @throws Exception
     */
    public function getIndexTable()
    {
        $modules = Orchestrator::getModulesList();
        // in case of json
        $collection = Collection::make($modules->data);

        return Datatables::of($collection)
            ->editColumn('name', function ($collection) {
                return "<a href='".action('ModulesController@show', $collection->module_key)."'>".$collection->name."</a>";
            })
            ->addColumn('action', function ($collection) {
                return ONE::actionButtons($collection->module_key, ['form' => 'moduleType' ,'edit' => 'ModulesController@edit', 'delete' => 'ModulesController@delete']);
            })
            ->rawColumns(['name','action'])
            ->make(true);
    }
}
