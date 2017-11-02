<?php

namespace App\Http\Controllers;

use App\ComModules\Orchestrator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Session;

class UserTermsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        dd("index");
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        dd("create");

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param $siteKey
     * @return \Illuminate\Http\Response
     * @internal param int $id
     */
    public function show($siteKey)
    {
        try {
            $languages = Orchestrator::getLanguageList();
            $site = Orchestrator::getSite($siteKey);

            return view('private.entities.sites.useTerms', compact('site', 'languages'));
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
    public function edit($siteKey)
    {
        try {
            $languages = Orchestrator::getLanguageList();
            $site = Orchestrator::getSite($siteKey);

            return view('private.entities.sites.useTerms', compact('site', 'languages'));
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
    public function update(Request $request, $siteKey)
    {
        try {
            $languages = Orchestrator::getLanguageList();
            //$entityKey = ONE::getEntityKey();

            $contentTranslation = [];
            foreach($languages as $language){
                $contentTranslation[] = [
                    'language_id'   =>  $language->id,
                    'language_code' =>  $language->code,
                    'content'       =>  $language->default == true ? $request->input("required_content_".$language->code) :$request->input("content_".$language->code)
                ];
            }

            $request['use_terms'] = $contentTranslation;
            $site = Orchestrator::updateEntitySite($request, $siteKey);
            Session::flash('message', trans('entity.updateOk'));
            return redirect()->action('EntitiesSitesController@showUseTerms', ['siteKey' => $site->key]);
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["entities.updateSite" => $e->getMessage()]);
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
        dd(2);
    }
}
