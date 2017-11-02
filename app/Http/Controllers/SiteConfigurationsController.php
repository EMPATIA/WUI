<?php

namespace App\Http\Controllers;

use App\ComModules\Orchestrator;
use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\SiteConfRequest;
use Session;

class SiteConfigurationsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($siteConfGroupKey)
    {
        $siteConfGroups = Orchestrator::getSiteConfGroups(Session::get('X-SITE-KEY'));
        $siteConfGroupsToSelect = [];
        foreach($siteConfGroups as $site){
            $siteConfGroupsToSelect[$site->id] = $site->name;
            if ($site->site_conf_group_key==$siteConfGroupKey)
                $siteConfGroupSelected = $site->id;
        }
        $languages = Orchestrator::getAllLanguages();

        if (count($siteConfGroupsToSelect)<1)
            return redirect()->back()->withErrors([trans('privateSiteConfig.noConfsAvailable')]);
        else{
            $sidebar = 'siteConfs';
            $active = 'confs';

            Session::put('sidebarArguments', ['siteConfGroupKey' => $siteConfGroupKey, 'activeFirstMenu' => 'confs']);

            return view('private.siteConf.siteConf',
                compact('siteConfGroupsToSelect','languages','siteConfGroupSelected','siteConfGroupKey','siteConfGroupDisabled', 'sidebar', 'active'));
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(SiteConfRequest $request, $siteConfGroupKey)
    {
        try {
            $languages = Orchestrator::getAllLanguages();
            $translation = [];
            foreach($languages as $language) {
                $newTranslation = array(
                    'language_code' => $language->code,
                );
                if($request->input("name_".$language->code))
                    $newTranslation["name"] = $request->input("name_" . $language->code);
                if($request->input("description_".$language->code))
                    $newTranslation["description"] = $request->input("description_" . $language->code);
                $translation[] = $newTranslation;
            }
            $siteConf = Orchestrator::setSiteConf($request,$translation);
            Session::flash('message', trans('privateSiteConf.storeOk'));
            return redirect()->action('SiteConfigurationsController@show', ['siteConfGroup'=>$siteConfGroupKey, 'siteConf'=>$siteConf->site_conf_key]);
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors([ trans('privateSiteConf.storeNok') => $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($siteConfGroupKey,$siteConfID)
    {
        try {
            $siteConf = Orchestrator::getSiteConf($siteConfID);

            $siteConfGroups = Orchestrator::getSiteConfGroups(Session::get('X-SITE-KEY'));
            $siteConfGroupsToSelect = [];
            foreach($siteConfGroups as $site){
                $siteConfGroupsToSelect[$site->id] = $site->name;
            }

            $configTranslation = array();
            foreach ($siteConf->translations as $translation) {
                $configTranslation[$translation->lang_code] = ['name' => $translation->name,'description' => $translation->description];
            }
            $languages = Orchestrator::getAllLanguages();
            foreach ($languages as $language) {
                if (!isset($configTranslation[$language->code]))
                    $configTranslation[$language->code] = [
                        'name' => trans('privateSiteConf.undefinedTranslation'),
                        'description' => trans('privateSiteConf.undefinedTranslation'),
                    ];
            }

            $sidebar = 'siteConfs';
            $active = 'confs';

            Session::put('sidebarArguments', ['siteConfGroupKey' => $siteConfGroupKey, 'activeFirstMenu' => 'confs']);

            return view('private.siteConf.siteConf',
                compact('siteConf','siteConfGroupsToSelect','configTranslation','languages','siteConfGroupKey', 'sidebar', 'active'));
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors([ trans('privateSiteConf.show') => $e->getMessage()]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($siteConfGroupKey, $siteConfID)
    {
        try {
            $siteConf = Orchestrator::getSiteConfEdit($siteConfID);
            $siteConfGroups = Orchestrator::getSiteConfGroups(Session::get('X-SITE-KEY'));
            $siteConfGroupsToSelect = [];
            foreach($siteConfGroups as $site){
                $siteConfGroupsToSelect[$site->id] = $site->name;
            }

            $configTranslation = array();
            foreach ($siteConf->translations as $translation){
                $configTranslation[$translation->lang_code] = ['name' => $translation->name,'description' => $translation->description];
            }
            $languages = Orchestrator::getAllLanguages();

            $sidebar = 'siteConfs';
            $active = 'confs';

            Session::put('sidebarArguments', ['siteConfGroupKey' => $siteConfGroupKey, 'activeFirstMenu' => 'confs']);

            return view('private.siteConf.siteConf',
                compact('siteConf','siteConfGroupsToSelect','configTranslation','languages','siteConfGroupKey', 'sidebar', 'active'));
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors([ trans('privateSiteConf.edit') => $e->getMessage()]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(SiteConfRequest $request, $siteConfGroupKey, $siteConfID)
    {
        try {
            $languages = Orchestrator::getAllLanguages();
            $translation = [];
            foreach($languages as $language) {
                $newTranslation = array(
                    'language_code' => $language->code,
                );
                if($request->input("name_".$language->code))
                    $newTranslation["name"] = $request->input("name_" . $language->code);
                if($request->input("description_".$language->code))
                    $newTranslation["description"] = $request->input("description_" . $language->code);
                $translation[] = $newTranslation;
            }
            $siteConf = Orchestrator::updateSiteConf($request, $siteConfID, $translation);
            Session::flash('message', trans('privateSiteConf.storeOk'));
            return redirect()->action('SiteConfGroupController@showConf', ['siteConfGroup'=>$siteConfGroupKey, 'siteConf'=>$siteConf->site_conf_key]);
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors([ trans('privateSiteConf.storeNok') => $e->getMessage()]);
        }
    }

    public function delete($siteConfGroupKey,$siteConfId){
        $data = array();

        $data['action'] = action("SiteConfigurationsController@destroy", ['siteConfGroupKey'=>$siteConfGroupKey,'siteConfId'=>$siteConfId]);
        $data['title'] = "DELETE";
        $data['msg'] = "Are you sure you want to delete this SiteConf?";
        $data['btn_ok'] = "Delete";
        $data['btn_ko'] = "Cancel";

        return view("_layouts.deleteModal", $data);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($siteConfGroupKey,$siteConfId)
    {
        try {
            Orchestrator::deleteSiteConf($siteConfId);
            Session::flash('message', trans('privateSiteConf.deleteOk'));

            return action('SiteConfGroupController@showSiteConfGroupConfigurations',$siteConfGroupKey);

        }
        catch(Exception $e) {
            return redirect()->back()->withErrors([ trans('privateSiteConf.deleteOk') => $e->getMessage()]);
        }
    }
}
