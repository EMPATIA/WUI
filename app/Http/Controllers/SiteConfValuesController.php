<?php

namespace App\Http\Controllers;

use App\ComModules\Files;
use App\ComModules\Orchestrator;
use App\One\One;
use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Input;
use Session;

class SiteConfValuesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $siteKey = $request['siteKey'];
        $siteConfGroups = Orchestrator::getSiteConfGroups($siteKey);
        $uploadKey = Files::getUploadKey();
        $siteConfs = Orchestrator::getSiteConfs();

        $sidebar = 'site';
        $active = 'configurations';

        Session::put('sidebarArguments', ['siteKey' => $siteKey, 'activeFirstMenu' => 'configurations']);

        return view('private.entities.sites.configurations', compact('siteConfs', 'siteKey', 'siteConfGroups','uploadKey', 'sidebar', 'active'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
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
     * @param Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $siteKey = $request->site_key;
        $siteConfs = Orchestrator::getSiteConfs();

        return view('private.entities.sites.configurations', compact('siteConfs', 'siteKey'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $siteKey
     * @return \Illuminate\Http\Response
     */
    public function edit($siteKey)
    {
        $siteConfGroups = Orchestrator::getSiteConfGroups($siteKey);
        $uploadKey = Files::getUploadKey();
        $siteConfs = Orchestrator::getSiteConfs();

        $sidebar = 'site';
        $active = 'configurations';

        Session::put('sidebarArguments', ['siteKey' => $siteKey, 'activeFirstMenu' => 'configurations']);

        return view('private.entities.sites.configurations', compact('siteConfs', 'siteKey', 'siteConfGroups','uploadKey', 'sidebar', 'active'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $siteConfs = Orchestrator::getSiteConfs();

        $array = [];
        foreach ($siteConfs as $siteConf){

            if(isset($request[$siteConf->code])){
                $array[] = ['site_conf_id' => $siteConf->id, 'value' => $request[$siteConf->code]];
            }
            if (isset($request[$siteConf->code]) and $siteConf->code == 'boolean_test'){
                $array[] = ['site_conf_id' => $siteConf->id, 'value' => $request[$siteConf->code]];
            }
        }

        if(!empty($array)) {

            Orchestrator::updateSiteConfValues($array, $request['siteKey']);
        }
        return redirect()->action('SiteConfValuesController@index',['siteKey' => $request['siteKey']]);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function getSiteConfsFromGroup(Request $request){


        $explode = explode('_',$request->key);

        if(isset($explode[1])){

            $response = Orchestrator::listSiteConf($explode[1]);
            foreach($response as $resp){
                $confs[] = ["id" => $resp->id, "name" => $resp->name, "value" => !empty($resp->siteConfValues) ? $resp->siteConfValues[0]->value : ''];
            }

            return $confs;


        }

    }
}
