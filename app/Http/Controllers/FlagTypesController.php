<?php

namespace App\Http\Controllers;

use App\ComModules\CB;
use App\ComModules\Orchestrator;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use One;
use Session;
use Datatables;

class FlagTypesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //Page title
        $title = trans('privateFlagTypes.list_flag_types');

        return view('private.flagTypes.index', compact('title'));
    }


    public function getIndexTable(Request $request)
    {
        try {

            $requestFlags = CB::getFlagTypesList();
            // in case of json
            $flagTypes = Collection::make($requestFlags);

            //  Datatable with sent emails list
            return Datatables::of($flagTypes)
                ->editColumn('title', function ($flagTypes) {
                    return "<a href='" . action('FlagTypesController@show', $flagTypes->id) . "'>" . collect($flagTypes->current_language_translation)->first()->title . "</a>";
                })
                ->addColumn('action', function ($flagTypes) {
                    return ONE::actionButtons($flagTypes->id, ['delete' => 'FlagTypesController@delete']);
                })
                ->rawColumns(['title','action'])
                ->make(true);
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["flagTypes.getIndexTable" => $e->getMessage()]);
        }
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $languages = Orchestrator::getLanguageList();

        try {
            // Form title (layout)
            $title = trans('privateFlagTypes.create_flag_type');

            $data = [];
            $data['title'] = $title;
            $data['languages'] = $languages;

            return view('private.flagTypes.flagType', $data);
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["flagTypes.create" => $e->getMessage()]);
        }
    }


    /**
     * @param $request
     * @param $languages
     * @return array
     */
    public function prepareTranslationsToSend($request, $languages)
    {
        $translations = [];
        foreach($languages as $language){
            if(!empty($request->input("title_".$language->code))){
                $translations[] = [
                    'language_code' => $language->code,
                    'title'         => $request->input("title_" . $language->code),
                    'description'   => $request->input("description_" . $language->code) ?? null
                ];
            }
        }
        return $translations;
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
            $languages = Orchestrator::getLanguageList();

            $translations = $this->prepareTranslationsToSend($request,$languages);

            //Call to Com Module set method
            CB::setFlagType($request, $translations);

            // Message to show + redirect To
            Session::flash('message', trans('privateFlagTypes.store_ok'));
            return redirect()->action('FlagTypesController@index');

        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["flagTypes.store" => $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $languages = Orchestrator::getLanguageList();
            $title = trans('privateFlagTypes.show_flag_type');
            //Call to Com Module set method
            $flagType = CB::getFlagType($id);
            // Message to show + redirect To

            $data = [];
            $data['title'] = $title;
            $data['languages'] = $languages;
            $data['flagType'] = $flagType;
            $data['translations'] = $flagType->translations;

            return view('private.flagTypes.flagType', $data);

        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["flagTypes.store" => $e->getMessage()]);
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
            $languages = Orchestrator::getLanguageList();
            $title = trans('privateFlagTypes.edit_flag_type');
            //Call to Com Module set method
            $flagType = CB::getFlagType($id);
            // Message to show + redirect To

            $data = [];
            $data['title'] = $title;
            $data['languages'] = $languages;
            $data['flagType'] = $flagType;
            $data['translations'] = $flagType->translations;

            return view('private.flagTypes.flagType', $data);

        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["flagTypes.edit" => $e->getMessage()]);
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
            $languages = Orchestrator::getLanguageList();

            $translations = $this->prepareTranslationsToSend($request,$languages);

            //Call to Com Module set method
            CB::updateFlagType($request, $translations,$id);

            // Message to show + redirect To
            Session::flash('message', trans('privateFlagTypes.update_ok'));
            return redirect()->action('FlagTypesController@show',$id);

        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["flagTypes.store" => $e->getMessage()]);
        }
    }

    /**
     * @param $id
     * @return View
     */
    public function delete($id)
    {

        $data = array();

        $data['action'] = action("FlagTypesController@destroy", $id);
        $data['title'] =  trans('privateFlagTypes.delete');
        $data['msg'] = trans('privateFlagTypes.are_you_sure you_want_to_delete_this_flag_type') . "?";
        $data['btn_ok'] = trans('privateFlagTypes.delete');
        $data['btn_ko'] = trans('privateFlagTypes.cancel');

        return view("_layouts.deleteModal", $data);
    }

    /**
     * @param $id
     * @return $this|string
     */
    public function destroy($id)
    {
        try {

            CB::deleteFlagType($id);
            Session::flash('message', trans('privateFlagTypes.deleteOk'));
            return action('FlagTypesController@index');

        } catch (Exception $e) {
            return redirect()->back()->withErrors(["flagTypes.destroy" => $e->getMessage()]);
        }
    }
}
