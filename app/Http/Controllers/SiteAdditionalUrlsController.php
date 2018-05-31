<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Datatables;
use App\ComModules\Orchestrator;
use App\One\One;
use Session;

class SiteAdditionalUrlsController extends Controller
{

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($siteKey)
    {
        try {
            $entityKey = ONE::getEntityKey();
            $languages = Orchestrator::getLanguageList();
            $site = Orchestrator::getSite($siteKey);

            $title = trans('privateEntitiesSites.site_add_additional_link') . ' ' . (isset($site->name) ? $site->name : null);
            return view('private.entities.sites.additionalLink', compact('title', 'entityKey', 'site' ,'languages'));
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["entities.createAdditionalLink" => $e->getMessage()]);
        }
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
            $request['link'] = str_replace("https://", '', $request['link']);
            $request['link'] = str_replace("http://", '', $request['link']);
            Orchestrator::storeSiteAdditionalLink($request['site_key'],$request['link']);
            Session::flash('message', trans('entity.storeAdditionalLinkOk'));
            return redirect()->action('EntitiesSitesController@show', ['siteKey' => $request['site_key']]);
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["entities.storeAdditionalLink" => $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $entityKey = ONE::getEntityKey();
            $languages = Orchestrator::getLanguageList();
            $additionalUrl = Orchestrator::getSiteAdditionalLinkById($id);
            $site = Orchestrator::getSiteById($additionalUrl->site_id);
            $title = trans('privateEntitiesSites.show_site_additional_link') . ' ' . (isset($site->name) ? $site->name : null);
            return view('private.entities.sites.additionalLink', compact('title', 'entityKey', 'site', 'additionalUrl', 'languages'));
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["entities.updateSite" => $e->getMessage()]);
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
            $entityKey = ONE::getEntityKey();
            $languages = Orchestrator::getLanguageList();
            $additionalUrl = Orchestrator::getSiteAdditionalLinkById($id);
            $site = Orchestrator::getSiteById($additionalUrl->site_id);
            $title = trans('privateEntitiesSites.show_site_additional_link');
            return view('private.entities.sites.additionalLink', compact('title', 'site','entityKey', 'additionalUrl', 'languages'));
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["entities.updateSite" => $e->getMessage()]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $request['link'] = str_replace("https://", '', $request['link']);
            $request['link'] = str_replace("http://", '', $request['link']);

            Orchestrator::updateSiteAdditionalLink($id,$request['link']);

            Session::flash('message', trans('entity.updateAdditionalLinkOk'));
            return redirect()->action('EntitiesSitesController@show', ['siteKey' => $request['site_key']]);
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["entities.updateAdditionalLink" => $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $urlId
     * @return \Illuminate\Http\Response
     * @internal param int $id
     */
    public function destroy($urlId)
    {
        try {
            $additionalUrl = Orchestrator::getSiteAdditionalLinkById($urlId);
            $site = Orchestrator::getSiteById($additionalUrl->site_id);
            Orchestrator::deleteAdditionalUrl($urlId);
            Session::flash('message', trans('entity.additional_link_delete_ok'));
            return action('EntitiesSitesController@show',$site->key);
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["entities.additional_link_delete_Nok" => $e->getMessage()]);
        }
    }

    public function deleteConfirm($id){
        $data = array();
        $data['action'] = action("SiteAdditionalUrlsController@destroy", ['id' => $id]);
        $data['title'] = "DELETE";
        $data['msg'] = "Are you sure you want to delete this Additional link";
        $data['btn_ok'] = "Delete";
        $data['btn_ko'] = "Cancel";
        return view("_layouts.deleteModal", $data);
    }
}
