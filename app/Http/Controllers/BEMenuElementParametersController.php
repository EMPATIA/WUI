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

class BEMenuElementParametersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $title = trans('private.beMenuElementParameters.list_title');
            return view('private.beMenuElementParameters.index', compact('title'));
        } catch (Exception $e) {
            return redirect()->back()->withErrors([ trans('beMenuElementParameters.index') => $e->getMessage()]);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        try {
            $languages = Orchestrator::getAllLanguages();

            return view('private.beMenuElementParameters.beMenuElementParameter',compact('languages'));
        } catch (Exception $e) {
            return redirect()->back()->withErrors([ trans('beMenuElementParameters.create') => $e->getMessage()]);
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
            $languages = Orchestrator::getAllLanguages();

            $storeData = [
                "code" => $request->get("code"),
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

            $parameter = CM::createBEMenuElementParameters($storeData);
            Session::flash('message', trans('beMenuElementParameters.store_ok'));
            return redirect()->action('BEMenuElementParametersController@show', $parameter->key);
        } catch (Exception $e){
            return redirect()->back()->withErrors([ trans('beMenuElementParameters.store') => $e->getMessage()]);
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
            $parameter = CM::getBEMenuElementParameter($key);
            $languages = Orchestrator::getAllLanguages();

            return view('private.beMenuElementParameters.beMenuElementParameter', compact('parameter','languages'));
        } catch(Exception $e) {
            return redirect()->back()->withErrors(["beMenuElementParameters.show" => $e->getMessage()])->getTargetUrl();
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
            $parameter = CM::getBEMenuElementParameter($key);
            $languages = Orchestrator::getAllLanguages();

            return view('private.beMenuElementParameters.beMenuElementParameter', compact('parameter','languages'));
        } catch(Exception $e) {
            return redirect()->back()->withErrors(["beMenuElementParameters.edit" => $e->getMessage()])->getTargetUrl();
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

            $storeData = [
                "code" => $request->get("code"),
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

            $parameter = CM::updateBEMenuElementParameters($key,$storeData);
            Session::flash('message', trans('beMenuElementParameters.update_ok'));
            return redirect()->action('BEMenuElementParametersController@show', $parameter->key);
        } catch (Exception $e){
            return redirect()->back()->withErrors([ trans('beMenuElementParameters.update') => $e->getMessage()]);
        }
    }

    public function delete($key)
    {
        $data = array();

        $data['action'] = action("BEMenuElementParametersController@destroy", $key);
        $data['title'] = trans('privateBEMenuElementParameters.delete');
        $data['msg'] = trans('privateBEMenuElementParameters.are_you_sure_you_want_to_delete');
        $data['btn_ok'] = trans('privateBEMenuElementParameters.delete');
        $data['btn_ko'] = trans('privateBEMenuElementParameters.cancel');

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
            CM::deleteBEMenuElementParameters($key);
            Session::flash('message', trans('privateBEMenuElementParameters.delete_ok'));
            return action('BEMenuElementParametersController@index');
        }  catch(Exception $e) {
            return redirect()->back()->withErrors([ trans('privateBEMenuElementParameters.delete_error') => $e->getMessage()])->getTargetUrl();
        }
    }

    /** Display a listing of the resource.
     *
     * @return mixed
     */

    public function getIndexTable(Request $request){
        try {
            $response = CM::getBEMenuElementParameters($request);

            $actions = collect($response->beMenuElementParameters);
            $recordsTotal = $response->recordsTotal;
            $recordsFiltered = $response->recordsFiltered;

            return Datatables::of($actions)
                ->editColumn('id',function ($element) {
                    return "<a href='".action('BEMenuElementParametersController@show', $element->key)."'>" . $element->id . "</a>";
                })
                ->editColumn('key',function ($element) {
                    return "<a href='".action('BEMenuElementParametersController@show', $element->key)."'>" . $element->key . "</a>";
                })
                ->editColumn('code', function($element) {
                    if (!empty($element->code))
                        return $element->code;
                    else
                        return "<b>" . trans("beMenuElementParameters.no_code") . "</b>";
                })
                ->addColumn('action', function ($element) {
                    return ONE::actionButtons($element->key, ['form' => 'beMenuElements','edit' => 'BEMenuElementParametersController@edit', 'delete' => 'BEMenuElementParametersController@delete'] );
                })
                ->with('filtered', $recordsFiltered ?? 0)
                ->skipPaging()
                ->setTotalRecords($recordsTotal ?? 0)
                ->make(true);
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["beMenuElementParameters.getIndexTable" => $e->getMessage()]);
        }
    }
}