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

class CMSectionTypesController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title = trans('privateCMSectionTypes.sectionTypes');
        return view('private.CMSectionTypes.index', compact('title'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $languages = Orchestrator::getAllLanguages();
        $sectionTypeParameters = CM::getSectionTypeParameters();

        return view('private.CMSectionTypes.sectionType', compact('languages','sectionTypeParameters'));
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
            $createData = [
                "code" => $request->get("code"),
                "translatable" => $request->get("translatable",false),
                "translations" => [],
                "section_type_parameters" => $request->input("sectionTypeParameters")
            ];
            foreach($languages as $language){
                if($request->input("name_" . $language->code) && !empty($request->input("name_" . $language->code))) {
                    $createData["translations"][] = [
                        'language_code' => $language->code,
                        'value' => $request->input("name_" . $language->code) ?? null
                    ];
                }
            }

            $sectionType = CM::createSectionType($createData);
            Session::flash('message', trans('CMSectionTypes.update_ok'));
            return redirect()->action('CMSectionTypesController@show', $sectionType->section_type_key);
        } catch (Exception $e){
            return redirect()->back()->withErrors([ trans('CMSectionTypes.update_error') => $e->getMessage()]);
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
            $sectionType = CM::getSectionType($key);
            $languages = Orchestrator::getAllLanguages();
            return view('private.CMSectionTypes.sectionType', compact('sectionType','languages'));
        } catch(Exception $e) {
            return redirect()->back()->withErrors(["CMSectionTypes.show" => $e->getMessage()])->getTargetUrl();
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param $key
     * @return \Illuminate\Http\Response
     */
    public function edit($key)
    {
        try {
            $sectionType = CM::getSectionType($key);
            $languages = Orchestrator::getAllLanguages();
            $sectionTypeParametersSelected = collect($sectionType->section_type_parameters)->keyBy('section_type_parameter_key')->toArray();
            $sectionTypeParameters = collect(CM::getSectionTypeParameters())->keyBy("section_type_parameter_key")->toArray();

            $data = [];
            $data['sectionType'] = $sectionType;
            $data['languages'] = $languages;
            $data['sectionTypeParametersSelected'] = $sectionTypeParametersSelected;
            $data['sectionTypeParameters'] = $sectionTypeParameters;

            return view('private.CMSectionTypes.sectionType', $data);
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["CMSectionTypes.edit" => $e->getMessage()])->getTargetUrl();
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
                "translations" => [],
                "translatable" => $request->get("translatable",false),
                "section_type_parameters" => $request->input("sectionTypeParameters")
            ];
            foreach($languages as $language){
                if($request->input("name_" . $language->code)) {
                    $updateData["translations"][] = [
                        'language_code' => $language->code,
                        'value' => $request->input("name_" . $language->code) ?? null
                    ];
                }
            }
            $sectionType = CM::updateSectionType($key,$updateData);
            Session::flash('message', trans('CMSectionTypes.update_ok'));
            return redirect()->action('CMSectionTypesController@show', $sectionType->section_type_key);
        } catch (Exception $e){
            return redirect()->back()->withErrors([ trans('CMSectionTypes.update_error') => $e->getMessage()]);
        }
    }

    public function delete($key)
    {
        $data = array();

        $data['action'] = action("CMSectionTypesController@destroy", $key);
        $data['title'] = trans('CMSectionTypes.delete');
        $data['msg'] = trans('CMSectionTypes.are_you_sure_you_want_to_delete');
        $data['btn_ok'] = trans('CMSectionTypes.delete');
        $data['btn_ko'] = trans('CMSectionTypes.cancel');

        return view("_layouts.deleteModal", $data);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($key)
    {
        try {
            CM::deleteSectionType($key);
            Session::flash('message', trans('CMSectionTypes.delete_ok'));
            return action('CMSectionTypesController@index');
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors([ trans('CMSectionTypes.delete_error') => $e->getMessage()])->getTargetUrl();
        }
    }

    /** Display a listing of the resource.
     *
     * @return mixed
     */
    public function getIndexTable(Request $request){
        // Request for Data List
        $sectionTypesList = CM::getSectionTypes($request);
        // JSON data collection
        $collection = collect($sectionTypesList->sectionTypes);
        $recordsTotal = $sectionTypesList->recordsTotal;
        $recordsFiltered = $sectionTypesList->recordsFiltered;
        // Render Datatable
        return Datatables::of($collection)
            ->addColumn('key', function ($collection) {
                return "<a href='".action('CMSectionTypesController@show', $collection->section_type_key)."'>".$collection->section_type_key."</a>";
            })
            ->addColumn('action', function ($collection) {
                return ONE::actionButtons($collection->section_type_key, ['form' => 'CMSectionTypes','edit' => 'CMSectionTypesController@edit', 'delete' => 'CMSectionTypesController@delete'] );
            })
            ->rawColumns(['key','action'])
            ->with('filtered', $recordsFiltered ?? 0)
            ->skipPaging()
            ->setTotalRecords($recordsTotal ?? 0)
            ->make(true);
    }
}
