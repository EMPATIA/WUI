<?php

namespace App\Http\Controllers;

use App\ComModules\Orchestrator;
use App\Http\Requests\SiteConfGroupRequest;
use App\Http\Requests\SiteConfRequest;
use App\One\One;
use Exception;
use Illuminate\Support\Collection;
use Datatables;
use Session;
use View;
use Breadcrumbs;

class SiteConfGroupController extends Controller
{

    public function __construct()
    {
        View::share('title', trans('privateSiteConfGroup.title'));


    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('private.siteConfGroup.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $languages = Orchestrator::getAllLanguages();
        return view('private.siteConfGroup.siteConfGroup',compact('languages'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param SiteConfGroupRequest|\Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function store(SiteConfGroupRequest $request)
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
            $siteConfGroup = Orchestrator::setSiteConfGroup($request,$translation);
            Session::flash('message', trans('privateSiteConfGroups.storeOk'));
            return redirect()->action('SiteConfGroupController@show', $siteConfGroup->site_conf_group_key);
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors([ trans('privateSiteConfGroup.storeNok') => $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param $siteConfGroupID
     * @return \Illuminate\Http\Response
     * @internal param int $id
     */
    public function show($siteConfGroupID)
    {
        try {
            $siteConfGroup = Orchestrator::getSiteConfGroup($siteConfGroupID);
            $siteConfGroupKey = $siteConfGroup->site_conf_group_key;

            $configTranslation = array();
            foreach ($siteConfGroup->translations as $translation) {
                $configTranslation[$translation->lang_code] = ['name' => $translation->name,'description' => $translation->description];
            }
            $languages = Orchestrator::getAllLanguages();
            foreach ($languages as $language) {
                if (!isset($configTranslation[$language->code]))
                    $configTranslation[$language->code] = [
                        'name' => trans('privateSiteConfGroup.undefinedTranslation'),
                        'description' => trans('privateSiteConfGroup.undefinedTranslation'),
                    ];
            }

            $sidebar = 'siteConfs';
            $active = 'details';

            Session::put('sidebarArguments', ['siteConfGroupKey' => $siteConfGroupKey, 'activeFirstMenu' => 'details']);

            return view('private.siteConfGroup.siteConfGroup',
                compact('siteConfGroup', 'siteConfGroupKey', 'configTranslation','languages', 'sidebar', 'active'));
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors([ trans('privateSiteConfGroup.show') => $e->getMessage()]);
        }

    }

    /**
     * Update the specified resource in storage.
     *
     * @param SiteConfGroupRequest|\Illuminate\Http\Request $request
     * @param $siteConfGroupId
     * @return \Illuminate\Http\Response
     * @internal param int $id
     */
    public function update(SiteConfGroupRequest $request, $siteConfGroupId)
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
            $siteConf = Orchestrator::updateSiteConfGroup($request, $siteConfGroupId,$translation);
            Session::flash('message', trans('privateSiteConfGroup.updateOk'));
            return redirect()->action('SiteConfGroupController@edit', $siteConfGroupId);
        }
        catch(Exception $e) {
            //TODO: save inputs
            return redirect()->back()->withErrors([ trans('privateSiteConfGroup.updateNok') => $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $siteConfGroupId
     * @return \Illuminate\Http\Response
     * @internal param int $id
     */
    public function destroy($siteConfGroupId)
    {
        try {
            Orchestrator::deleteSiteConfGroup($siteConfGroupId);
            Session::flash('message', trans('privateSiteConfGroup.deleteOk'));

            return action('SiteConfGroupController@index');

        }
        catch(Exception $e) {
            return redirect()->back()->withErrors([ trans('privateSiteConfGroup.deleteOk') => $e->getMessage()]);
        }
    }

    public function delete($siteConfGroupId)
    {
        $data = array();

        $data['action'] = action("SiteConfGroupController@destroy", $siteConfGroupId);
        $data['title'] = "DELETE";
        $data['msg'] = "Are you sure you want to delete this SiteConfGroup?";
        $data['btn_ok'] = "Delete";
        $data['btn_ko'] = "Cancel";

        return view("_layouts.deleteModal", $data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param $siteConfGroupID
     * @return \Illuminate\Http\Response
     * @internal param int $id
     */
    public function edit($siteConfGroupID)
    {
        try {
            $siteConfGroup = Orchestrator::getSiteConfGroup($siteConfGroupID);

            $configTranslation = array();
            foreach ($siteConfGroup->translations as $translation) {
                $configTranslation[$translation->lang_code] = ['name' => $translation->name,'description' => $translation->description];
            }
            $languages = Orchestrator::getAllLanguages();

            return view('private.siteConfGroup.siteConfGroup',
                compact('siteConfGroup','configTranslation','languages'));
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors([ trans('privateSiteConfGroup.edit') => $e->getMessage()]);
        }
    }

    /**
     * Display a listing of the resource.
     * @return mixed
     * @throws Exception
     */
    public function getIndexTable()
    {

        $SiteConfGroup = Orchestrator::getSiteConfGroups(Session::get('X-SITE-KEY'));
        // in case of json
        $collection = Collection::make($SiteConfGroup);
        return Datatables::of($collection)
            ->editColumn('code', function ($collection) {
                return "<a href='".action('SiteConfGroupController@show', $collection->site_conf_group_key)."'>".$collection->code."</a>";
            })
            ->addColumn('action', function ($collection) {
                return ONE::actionButtons($collection->site_conf_group_key, ['edit' => 'SiteConfGroupController@edit', 'delete' => 'SiteConfGroupController@delete']);
            })
            ->make(true);
    }

    public function showSiteConfGroupConfigurations($siteConfGroupID)
    {
        $siteConfGroup = Orchestrator::getSiteConfGroup($siteConfGroupID);
        $siteConfGroupKey = $siteConfGroup->site_conf_group_key;

        $sidebar = 'siteConfs';
        $active = 'confs';

        Session::put('sidebarArguments', ['siteConfGroupKey' => $siteConfGroupKey, 'activeFirstMenu' => 'confs']);
        return view('private.siteConfGroup.siteConfGroupConfigurations', compact('siteConfGroup', 'siteConfGroupKey', 'active', 'sidebar'));
    }

    public function getConfsOfGroup($groupKey) {
        try {
            $response = Orchestrator::listSiteConf($groupKey);


            $collection = Collection::make($response);

            // in case of json
            return Datatables::of($collection)
                ->editColumn('code', function ($collection) use ($groupKey) {
                    return "<a href='".action('SiteConfigurationsController@show', ['siteConfGroup'=>$groupKey,'siteConf'=> $collection->site_conf_key])."'>".$collection->code."</a>";
                })
                ->addColumn('action', function ($collection) use ($groupKey) {
                    return ONE::actionButtons(['siteConfGroup'=>$groupKey,'siteConf'=> $collection->site_conf_key], ['form' => 'siteConfigurations', 'edit' => 'SiteConfigurationsController@edit', 'delete' => 'SiteConfigurationsController@delete']);
                })
                ->make(true);
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["privateSiteConf.getGroupConfTable" => $e->getMessage()]);
        }
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function createConf($siteConfGroupKey)
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
        else
            return view('private.siteConf.siteConf',
                compact('siteConfGroupsToSelect','languages','siteConfGroupSelected','siteConfGroupKey','siteConfGroupDisabled'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param SiteConfRequest|\Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function storeConf(SiteConfRequest $request,$siteConfGroupKey)
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
            return redirect()->action('SiteConfGroupController@showConf', ['siteConfGroup'=>$siteConfGroupKey, 'siteConf'=>$siteConf->site_conf_key]);
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors([ trans('privateSiteConf.storeNok') => $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param $siteConfGroupKey
     * @param $siteConfID
     * @return \Illuminate\Http\Response
     * @internal param int $id
     */
    public function showConf($siteConfGroupKey,$siteConfID)
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



            return view('private.siteConf.siteConf', compact('siteConf','siteConfGroupsToSelect','configTranslation','languages','siteConfGroupKey', 'sidebar', 'active'));
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors([ trans('privateSiteConf.show') => $e->getMessage()]);
        }

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param $siteConfID
     * @return \Illuminate\Http\Response
     * @internal param int $id
     */
    public function editConf($siteConfGroupKey,$siteConfID)
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

    public function deleteConf($siteConfGroupKey,$siteConfId)
    {
        $data = array();

        $data['action'] = action("SiteConfGroupController@destroyConf", ['siteConfGroupKey'=>$siteConfGroupKey,'siteConfId'=>$siteConfId]);
        $data['title'] = "DELETE";
        $data['msg'] = "Are you sure you want to delete this SiteConf?";
        $data['btn_ok'] = "Delete";
        $data['btn_ko'] = "Cancel";

        return view("_layouts.deleteModal", $data);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $siteConfId
     * @return \Illuminate\Http\Response
     * @internal param int $id
     */
    public function destroyConf($siteConfGroupKey,$siteConfId)
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
