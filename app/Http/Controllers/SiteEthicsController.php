<?php

namespace App\Http\Controllers;

use App\ComModules\Orchestrator;
use App\Http\Requests\SiteEthicRequest;
use App\One\One;
use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Session;

class SiteEthicsController extends Controller
{

    /**
     * Show the form for creating a new resource.
     *
     * @param Request $request
     * @param $siteKey
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function create(Request $request,$siteKey)
    {
        try {
            $type = $request->type;
            $languages = Orchestrator::getLanguageList();
            $title = trans('privateSiteEthics.'.$type);
            return view('private.entities.sites.siteEthic', compact('siteKey', 'languages','type','title'));
        } catch (Exception $e) {
            return redirect()->back()->withErrors([trans('privateSiteEthics.create_error') => $e->getMessage()]);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param SiteEthicRequest|Request $request
     * @param $siteKey
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function store(SiteEthicRequest $request,$siteKey)
    {
        try{
            $typeCode = $request->type;
            $languages = Orchestrator::getLanguageList();

            $contentTranslation = [];
            foreach($languages as $language){
                $content = $language->default == true ? $request->input("required_content_".$language->code) :$request->input("content_".$language->code);
                if(!empty($content)){
                    $contentTranslation[] = [
                        'language_code' =>  $language->code,
                        'content'       =>  $content
                    ];
                }
            }
            $siteEthic = Orchestrator::setSiteEthic($siteKey,$typeCode,$contentTranslation);

            Session::flash('message', trans('privateSiteEthics.store_ok'));
            switch ($typeCode){
                case 'use_terms':
                    return redirect()->action('EntitiesSitesController@showUseTerms', ['siteKey' => $siteKey]);
                    break;
                case 'privacy_policy':
                    return redirect()->action('EntitiesSitesController@showPrivacyPolicy', ['siteKey' => $siteKey]);
                    break;
            }
        } catch (Exception $e) {
            return redirect()->back()->withErrors([trans('privateSiteEthics.store_error') => $e->getMessage()]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Request $request
     * @param $siteKey
     * @param $siteEthicKey
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function edit(Request $request, $siteKey,$siteEthicKey)
    {
        try {
            $version = $request->version ?? 1;
            $languages = Orchestrator::getLanguageList();
            $siteEthic = Orchestrator::getSiteEthicByKey($siteEthicKey,$version);

            if(empty($siteEthic->site_ethic_type->code)){
                return redirect()->back()->withErrors([trans('privateSiteEthics.type_error')]);
            }
            $type = $siteEthic->site_ethic_type->code;
            $title = trans('privateSiteEthics.'.$type);

            $data = [];
            $data['title'] = $title;
            $data['siteEthic'] = $siteEthic;
            $data['languages'] = $languages;
            $data['siteKey'] = $siteKey;
            $data['type'] = $type;

            if (!empty(One::getEntityKey())) {
                $data['sidebar'] = 'site';
                $data['active'] = $type;
            }

            return view('private.entities.sites.siteEthic', $data);
        } catch (Exception $e) {
            return redirect()->back()->withErrors([trans('privateSiteEthics.edit_error') => $e->getMessage()]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param SiteEthicRequest|Request $request
     * @param $siteKey
     * @param $ethicKey
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function update(SiteEthicRequest $request, $siteKey,$ethicKey)
    {
        try{
            $typeCode = $request->type;
            $languages = Orchestrator::getLanguageList();

            $contentTranslation = [];
            foreach($languages as $language){
                $content = $language->default == true ? $request->input("required_content_".$language->code) :$request->input("content_".$language->code);
                if(!empty($content)){
                    $contentTranslation[] = [
                        'language_code' =>  $language->code,
                        'content'       =>  $content
                    ];
                }
            }
            $siteEthic = Orchestrator::updateSiteEthic($siteKey,$ethicKey,$contentTranslation);

            Session::flash('message', trans('privateSiteEthics.update_ok'));
            switch ($typeCode){
                case 'use_terms':
                    return redirect()->action('EntitiesSitesController@showUseTerms', ['siteKey' => $siteKey]);
                    break;
                case 'privacy_policy':
                    return redirect()->action('EntitiesSitesController@showPrivacyPolicy', ['siteKey' => $siteKey]);
                    break;
            }
        } catch (Exception $e) {
            return redirect()->back()->withErrors([trans('privateSiteEthics.update_error') => $e->getMessage()]);
        }
    }


    /** Show modal delete confirmation
     *
     * @param $siteKey
     * @param $siteEthicKey
     * @return View
     */
    public function delete($siteKey,$siteEthicKey)
    {
        $data = array();
        $data['action'] = action("SiteEthicsController@destroy", ['site_key' => $siteKey,'site_ethic_key' => $siteEthicKey]);
        $data['title'] = trans('privateSiteEthics.delete');
        $data['msg'] = trans('privateSiteEthics.are_you_sure_you_want_to_delete');
        $data['btn_ok'] = trans('privateSiteEthics.delete');
        $data['btn_ko'] = trans('privateSiteEthics.cancel');

        return view("_layouts.deleteModal", $data);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $siteKey
     * @param $siteEthicKey
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($siteKey,$siteEthicKey)
    {
        try {
            Orchestrator::deleteSiteEthic($siteEthicKey);
            Session::flash('message', trans('privateSiteEthics.delete_ok'));
            return redirect()->back()->getTargetUrl();
        } catch (Exception $e) {
            return redirect()->back()->withErrors([trans('privateSiteEthics.delete_ok') => $e->getMessage()])->getTargetUrl();
        }
    }


    /** Activate to the specific version
     * @param $siteEthicKey
     * @param $version
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function activateVersion($siteEthicKey, $version)
    {
        try {
            $siteEthic = Orchestrator::activateSiteEthicVersion($siteEthicKey, $version);
            Session::flash('message', trans('privateSiteEthics.site_ethic_activated'));
            return redirect()->back();
        } catch (Exception $e) {
            return redirect()->back()->withErrors([trans('privateSiteEthics.site_ethic_activate_error') => $e->getMessage()]);
        }
    }



    public function showPublicSiteEthic($type)
    {
        try {
            $siteEthics = Session::get('site_ethics');
            $langCode = Session::get('LANG_CODE');
            $langCodeDefault = Session::get('LANG_CODE_DEFAULT');
            $data = [];
            $data["type"] = $type;
            if(isset($siteEthics)&& isset($siteEthics->{$type}) && (isset($langCode) || isset($langCodeDefault))){
                $contentTranslation = $siteEthics->{$type}->{$langCode} ?? null;
                $contentTranslationDefault = $siteEthics->{$type}->{$langCodeDefault} ?? null;
                if(isset($contentTranslation) || isset($contentTranslationDefault)){
                    $title = trans('empatiaSiteEthics.'.$type);
                    $siteEthic = $contentTranslation->content ?? $contentTranslationDefault->content;
                }
                $data['title'] = $title ?? null;
                $data['siteEthic'] = $siteEthic ?? null;
            }
            return view('public.'.ONE::getEntityLayout().'.siteEthics.default',$data);

        } catch (Exception $e) {
            return redirect()->back()->withErrors([trans('privateSiteEthics.show_public_site_ethic_error') => $e->getMessage()]);
        }

    }
}
