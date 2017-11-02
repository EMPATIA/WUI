<?php

namespace App\Http\Controllers;

use App\ComModules\CM;
use App\ComModules\Orchestrator;
use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Datatables;
use Session;
use View;
use Illuminate\Support\Collection;
use App\One\One;

class CMSectionTypeParametersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title = trans('privateCMSectionTypeParameters.list_title');
        return view('private.CMSectionTypeParameters.index', compact('title'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $languages = Orchestrator::getAllLanguages();

        return view('private.CMSectionTypeParameters.sectionTypeParameter', compact('languages'));
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
            $languages = Orchestrator::getAllLanguages();
            $storeData = [
                "code" => $request->get("code"),
                "type_code" => $request->get("type_code"),
                "translations" => []
            ];
            foreach($languages as $language){
                if($request->input("name_" . $language->code) && !empty($request->input("name_" . $language->code))) {
                    $storeData["translations"][] = [
                        'language_code' => $language->code,
                        'name' => $request->input("name_" . $language->code) ?? null,
                        'description' => $request->input("description_" . $language->code) ?? null
                    ];
                }
            }

            $sectionTypeParameter = CM::createSectionTypeParameter($storeData);
            Session::flash('message', trans('CMSectionTypeParameters.update_ok'));
            return redirect()->action('CMSectionTypeParametersController@show', $sectionTypeParameter->section_type_parameter_key);
        } catch (Exception $e){
            return redirect()->back()->withErrors([ trans('CMSectionTypeParameters.update_error') => $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($key)
    {
        try {
            $sectionTypeParameter = CM::getSectionTypeParameter($key);
            $languages = Orchestrator::getAllLanguages();

            return view('private.CMSectionTypeParameters.sectionTypeParameter', compact('sectionTypeParameter','languages'));
        } catch(Exception $e) {
            return redirect()->back()->withErrors(["CMSectionTypeParameters.show" => $e->getMessage()])->getTargetUrl();
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($key)
    {
        try {
            $sectionTypeParameter = CM::getSectionTypeParameter($key);
            $languages = Orchestrator::getAllLanguages();

            return view('private.CMSectionTypeParameters.sectionTypeParameter', compact('sectionTypeParameter', 'languages'));
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["CMSectionTypeParameters.edit" => $e->getMessage()])->getTargetUrl();
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $key) {
        try {
            $languages = Orchestrator::getAllLanguages();
            $updateData = [
                "code" => $request->get("code"),
                "type_code" => $request->get("type_code"),
                "translations" => []
            ];
            foreach($languages as $language){
                if($request->input("name_" . $language->code) && !empty($request->input("name_" . $language->code))) {
                    $updateData["translations"][] = [
                        'language_code' => $language->code,
                        'name' => $request->input("name_" . $language->code) ?? null,
                        'description' => $request->input("description_" . $language->code) ?? null
                    ];
                }
            }
            $sectionType = CM::updateSectionTypeParameter($key,$updateData);
            Session::flash('message', trans('CMSectionTypeParameters.update_ok'));
            return redirect()->action('CMSectionTypeParametersController@show', $sectionType->section_type_parameter_key);
        } catch (Exception $e){
            return redirect()->back()->withErrors([ trans('CMSectionTypeParameters.update_error') => $e->getMessage()]);
        }
    }

    public function delete($key)
    {
        $data = array();

        $data['action'] = action("CMSectionTypeParametersController@destroy", $key);
        $data['title'] = trans('CMSectionTypeParameters.delete');
        $data['msg'] = trans('CMSectionTypeParameters.are_you_sure_you_want_to_delete');
        $data['btn_ok'] = trans('CMSectionTypeParameters.delete');
        $data['btn_ko'] = trans('CMSectionTypeParameters.cancel');

        return view("_layouts.deleteModal", $data);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $key
     * @return \Illuminate\Http\Response
     */
    public function destroy($key)
    {
        try {
            CM::deleteSectionTypeParameter($key);
            Session::flash('message', trans('CMSectionTypeParameters.delete_ok'));
            return action('CMSectionTypeParametersController@index');
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors([ trans('CMSectionTypeParameters.delete_error') => $e->getMessage()])->getTargetUrl();
        }
    }

    /** Display a listing of the resource.
     *
     * @return mixed
     */

    public function getIndexTable(){
        // Request for Data List
        $sectionTypesList = CM::getSectionTypeParameters();

        // JSON data collection
        $collection = Collection::make($sectionTypesList);

        // Render Datatable
        return Datatables::of($collection)
            ->addColumn('key', function ($sectionType) {
                return "<a href='".action('CMSectionTypeParametersController@show', $sectionType->section_type_parameter_key)."'>".$sectionType->section_type_parameter_key."</a>";
            })
            ->addColumn('action', function ($sectionType) {
                return ONE::actionButtons($sectionType->section_type_parameter_key, ['form' => 'CMSectionTypeParameters','edit' => 'CMSectionTypeParametersController@edit', 'delete' => 'CMSectionTypeParametersController@delete'] );
            })
            ->make(true);
    }
}