<?php

namespace App\Http\Controllers;

use App\ComModules\Orchestrator;
use App\One\One;
use App\Http\Requests;
use App\Http\Requests\SiteSiteConfigRequest;
use Exception;
use Illuminate\Support\Collection;
use Form;
use Datatables;
use Session;
use View;
use Breadcrumbs;

class SiteSiteConfigController extends Controller
{

    public function __construct()
    {
        View::share('title', trans('privateSiteSiteConfig.title'));


    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('private.SiteSiteConfig.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $siteConfs = Orchestrator::getSiteConfs();
        $siteConfsArray = array();
        foreach ($siteConfs as $siteConf) {
            $siteConfsArray[$siteConf->id] = $siteConf->name;
        }
        if (count($siteConfsArray)<1)
            return redirect()->back()->withErrors([trans('privateSiteSiteConfig.noConfsAvailable')]);
        else
            return view('private.SiteSiteConfig.SiteSiteConfig',compact("siteConfsArray"));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param SiteSiteConfigRequest|\Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function store(SiteSiteConfigRequest $request)
    {
        try {
            $request["parameter_value"] = (isset($request->parameter_value) ? $request->parameter_value : 0);
            $SiteSiteConfig = Orchestrator::setSiteSiteConfig($request);
            Session::flash('message', trans('privateSiteSiteConfig.storeOk'));
            return redirect()->action('SiteSiteConfigController@show', $SiteSiteConfig->id);
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors([ trans('privateSiteSiteConfig.storeNok') => $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param $SiteSiteConfigID
     * @return \Illuminate\Http\Response
     * @internal param int $id
     */
    public function show($SiteSiteConfigID)
    {
        try {
            $SiteSiteConfig = Orchestrator::getSiteSiteConfig($SiteSiteConfigID);
            $siteConfs = Orchestrator::getSiteConfs();
            $siteConfsArray = array();
            foreach ($siteConfs as $siteConf) {
                $siteConfsArray[$siteConf->id] = $siteConf->code;
            }

            return view('private.SiteSiteConfig.SiteSiteConfig',
                compact('SiteSiteConfig','siteConfsArray'));
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors([ trans('privateSiteSiteConfig.show') => $e->getMessage()]);
        }

    }

    /**
     * Update the specified resource in storage.
     *
     * @param SiteSiteConfigRequest|\Illuminate\Http\Request $request
     * @param $SiteSiteConfigId
     * @return \Illuminate\Http\Response
     * @internal param int $id
     */
    public function update(SiteSiteConfigRequest $request, $SiteSiteConfigId)
    {
        try {
            $request["parameter_value"] = (isset($request->parameter_value) ? $request->parameter_value : 0);

            $SiteSiteConfig = Orchestrator::updateSiteSiteConfig($request, $SiteSiteConfigId);
            Session::flash('message', trans('privateSiteSiteConfig.updateOk'));
            return redirect()->action('SiteSiteConfigController@edit', $SiteSiteConfigId);
        }
        catch(Exception $e) {
            //TODO: save inputs
            return redirect()->back()->withErrors([ trans('privateSiteSiteConfig.updateNok') => $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $SiteSiteConfigId
     * @return \Illuminate\Http\Response
     * @internal param int $id
     */
    public function destroy($SiteSiteConfigId)
    {
        try {
            Orchestrator::deleteSiteSiteConfig($SiteSiteConfigId);
            Session::flash('message', trans('privateSiteSiteConfig.deleteOk'));

            return action('SiteSiteConfigController@index');

        }
        catch(Exception $e) {
            return redirect()->back()->withErrors([ trans('privateSiteSiteConfig.deleteOk') => $e->getMessage()]);
        }
    }

    public function delete($SiteSiteConfigId)
    {
        $data = array();

        $data['action'] = action("SiteSiteConfigController@destroy", $SiteSiteConfigId);
        $data['title'] = "DELETE";
        $data['msg'] = "Are you sure you want to delete this SiteSiteConfig?";
        $data['btn_ok'] = "Delete";
        $data['btn_ko'] = "Cancel";

        return view("_layouts.deleteModal", $data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param $SiteSiteConfigID
     * @return \Illuminate\Http\Response
     * @internal param int $id
     */
    public function edit($SiteSiteConfigID)
    {
        try {
            $SiteSiteConfig = Orchestrator::getSiteSiteConfig($SiteSiteConfigID);
            $siteConfs = Orchestrator::getSiteConfs();
            $siteConfsArray = array();
            foreach ($siteConfs as $siteConf) {
                $siteConfsArray[$siteConf->id] = $siteConf->name;
            }

            $SiteSiteConfig->disabled = "disabled";

            return view('private.SiteSiteConfig.SiteSiteConfig',
                compact('SiteSiteConfig','siteConfsArray'));
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors([ trans('privateSiteSiteConfig.edit') => $e->getMessage()]);
        }
    }



    /**
     * Display a listing of the resource.
     * @return mixed
     * @throws Exception
     */
    public function getIndexTable()
    {
        $SiteSiteConfig = Orchestrator::getSiteSiteConfigs();
        $siteConfs = [];
        foreach ($SiteSiteConfig as $configGroup) {
            foreach ($configGroup->confs as $conf) {
                $siteConfs[] = [
                    "code"          => $conf->id,
                    "name"          => $conf->name,
                    "value"         => $conf->value,
                    "group"         => $configGroup->name,
                ];
            }
        }

        $collection = Collection::make($siteConfs);

        return Datatables::of($collection)
            ->editColumn('value',function($collection) {
                return Form::oneSwitch('param', null, isset($collection["value"]) ? $collection["value"] : null, ['class' => 'form-control']);
                //return $collection->value ? trans("privateSiteSiteConfig.ON") : trans("privateSiteSiteConfig.OFF");
            })
            ->editColumn('name', function ($collection) {
                return "<a href='".action('SiteSiteConfigController@show', $collection["code"])."'>".$collection["name"]."</a>";
            })
            ->addColumn('action', function ($collection) {
                return ONE::actionButtons($collection["code"], ['edit' => 'SiteSiteConfigController@edit', 'delete' => 'SiteSiteConfigController@delete']);
            })
            ->make(true);
    }

}