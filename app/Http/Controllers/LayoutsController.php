<?php

namespace App\Http\Controllers;

use App\ComModules\Orchestrator;
use App\Http\Requests\LayoutRequest;
use App\One\One;
use App\Http\Requests;
use Exception;
use Illuminate\Support\Collection;
use Datatables;
use Session;
use View;
use Breadcrumbs;

class LayoutsController extends Controller
{

    public function __construct()
    {
        View::share('title', trans('privateLayouts.title'));


    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('private.layouts.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('private.layouts.layout');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param LayoutRequest|\Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function store(LayoutRequest $request)
    {
        try {
            $layout = Orchestrator::setNewLayout($request);
            Session::flash('message', trans('privateLayouts.store_ok'));
            return redirect()->action('LayoutsController@show', $layout->layout_key);

        }
        catch(Exception $e) {
            return redirect()->back()->withErrors([ trans('privateLayouts.store_nok') => $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param $layoutKey
     * @return \Illuminate\Http\Response
     * @internal param int $id
     */
    public function show($layoutKey)
    {
        try {
            $layout = Orchestrator::getLayout($layoutKey);

            return view('private.layouts.layout', compact('layout'));
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors([ trans('privateLayouts.show') => $e->getMessage()]);
        }

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param $layoutKey
     * @return \Illuminate\Http\Response
     * @internal param int $id
     */
    public function edit($layoutKey)
    {
        try {

            $layout = Orchestrator::getLayout($layoutKey);

            $sidebar = 'entity';
            $active = 'layouts';

            Session::put('sidebarArguments', ['activeFirstMenu' => 'layouts']);
            $entityKey = One::getEntityKey();
            if($entityKey == null)
                return view('private.layouts.layout', compact('layout'));

            return view('private.layouts.layout', compact('layout','entityKey', 'sidebar', 'active'));

        }
        catch(Exception $e) {
            return redirect()->back()->withErrors([ trans('privateLayouts.edit') => $e->getMessage()]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param LayoutRequest|\Illuminate\Http\Request $request
     * @param $layoutKey
     * @return \Illuminate\Http\Response
     * @internal param int $id
     */
    public function update(LayoutRequest $request, $layoutKey)
    {
        try {

            $layout = Orchestrator::updateLayout($request, $layoutKey);
            Session::flash('message', trans('privateLayouts.update_ok'));
            return redirect()->action('LayoutsController@show', $layoutKey);

        }
        catch(Exception $e) {
            //TODO: save inputs
            return redirect()->back()->withErrors([ trans('privateLayouts.update_nok') => $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $layoutKey
     * @return \Illuminate\Http\Response
     * @internal param int $id
     */
    public function destroy($layoutKey)
    {
        try {
            Orchestrator::deleteLayout($layoutKey);
            Session::flash('message', trans('privateLayouts.delete_ok'));
            return action('LayoutsController@index');

        }
        catch(Exception $e) {
            return redirect()->back()->withErrors([ trans('privateLayouts.delete_ok') => $e->getMessage()]);
        }
    }


    /**
     * Show delete resource confirmation
     * Remove the specified resource from storage.
     * @param $layoutKey
     * @return View
     */
    public function delete($layoutKey)
    {
        $data = array();

        $data['action'] = action("LayoutsController@destroy", $layoutKey);
        $data['title'] = "DELETE";
        $data['msg'] = "Are you sure you want to delete this Layout?";
        $data['btn_ok'] = "Delete";
        $data['btn_ko'] = "Cancel";

        return view("_layouts.deleteModal", $data);
    }


    /**
     * Display a listing of the resource.
     * @return mixed
     * @throws Exception
     */
    public function getIndexTable()
    {

        $layouts = Orchestrator::getLayouts();
        // in case of json
        $collection = Collection::make($layouts);

        return Datatables::of($collection)
            ->editColumn('name', function ($collection) {
                return "<a href='".action('LayoutsController@show', $collection->layout_key)."'>".$collection->name."</a>";
            })
            ->addColumn('action', function ($collection) {
                return ONE::actionButtons($collection->layout_key, ['edit' => 'LayoutsController@edit', 'delete' => 'LayoutsController@delete']);
            })
            ->make(true);
    }

}
