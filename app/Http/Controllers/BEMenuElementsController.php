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

class BEMenuElementsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $title = trans('private.beMenuElements.list_title');
            return view('private.beMenuElements.index', compact('title'));
        } catch (Exception $e) {
            return redirect()->back()->withErrors([ trans('beMenuElements.index') => $e->getMessage()]);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        try {
            $languages = Orchestrator::getAllLanguages();
            $parameters = CM::getBEMenuElementParameters($request)->beMenuElementParameters ?? [];

            return view('private.beMenuElements.beMenuElement', compact('languages', 'parameters'));
        } catch (Exception $e) {
            return redirect()->back()->withErrors([ trans('beMenuElements.create') => $e->getMessage()]);
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
                "code" => $request->get("code",""),
                "module_code" => $request->get("module_code",""),
                "module_type_code" => $request->get("module_type_code",""),
                "permission" => $request->get("permission",""),
                "controller" => $request->get("controller",""),
                "method" => $request->get("method",""),
                "translations" => [],
            ];

            $parametersData = json_decode($request->get("parameters-order",[]));
            foreach ($parametersData as $parameter) {
                $storeData["parameters"][] = $parameter->key;
            }

            foreach($languages as $language){
                if($request->input("name_" . $language->code) && !empty($request->input("name_" . $language->code))) {
                    $storeData["translations"][] = [
                        'language_code' => $language->code,
                        'name' => $request->input("name_" . $language->code) ?? null,
                        'description' => $request->input("description_" . $language->code) ?? null
                    ];
                }
            }

            $element = CM::createBEMenuElements($storeData);
            Session::flash('message', trans('beMenuElements.store_ok'));

            return redirect()->action('BEMenuElementsController@show', $element->key);
        } catch (Exception $e){
            return redirect()->back()->withErrors([ trans('beMenuElements.store') => $e->getMessage()]);
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
            $element = CM::getBEMenuElement($key);
            $languages = Orchestrator::getAllLanguages();

            return view('private.beMenuElements.beMenuElement', compact('element','languages'));
        } catch(Exception $e) {
            return redirect()->back()->withErrors(["beMenuElements.show" => $e->getMessage()])->getTargetUrl();
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $key)
    {
        try {
            $element = CM::getBEMenuElement($key);
            $parameters = CM::getBEMenuElementParameters($request);
            $languages = Orchestrator::getAllLanguages();

            $selectedParameters = collect($element->parameters)->pluck("code","key")->toArray();

            return view('private.beMenuElements.beMenuElement', compact('element','languages','parameters','selectedParameters'));
        } catch(Exception $e) {
            return redirect()->back()->withErrors(["beMenuElements.edit" => $e->getMessage()])->getTargetUrl();
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
                "code" => $request->get("code",""),
                "module_code" => $request->get("module_code",""),
                "module_type_code" => $request->get("module_type_code",""),
                "permission" => $request->get("permission",""),
                "controller" => $request->get("controller"),
                "method" => $request->get("method"),
                "translations" => []
            ];

            $parametersData = json_decode($request->get("parameters-order",[]));
            foreach ($parametersData as $parameter) {
                $storeData["parameters"][] = array(
                    "key" => $parameter->key,
                    "code" => $request->get($parameter->key . "-code","")
                );
            }

            foreach($languages as $language){
                if($request->input("name_" . $language->code) && !empty($request->input("name_" . $language->code))) {
                    $storeData["translations"][] = [
                        'language_code' => $language->code,
                        'name' => $request->input("name_" . $language->code) ?? null,
                        'description' => $request->input("description_" . $language->code) ?? null
                    ];
                }
            }

            $element = CM::updateBEMenuElements($key,$storeData);
            Session::flash('message', trans('beMenuElements.update_ok'));
            return redirect()->action('BEMenuElementsController@show', $element->key);
        } catch (Exception $e){
            return redirect()->back()->withErrors([ trans('beMenuElements.update') => $e->getMessage()]);
        }
    }

    public function delete($key)
    {
        $data = array();

        $data['action'] = action("BEMenuElementsController@destroy", $key);
        $data['title'] = trans('privateBEMenuElements.delete');
        $data['msg'] = trans('privateBEMenuElements.are_you_sure_you_want_to_delete');
        $data['btn_ok'] = trans('privateBEMenuElements.delete');
        $data['btn_ko'] = trans('privateBEMenuElements.cancel');

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
            CM::deleteBEMenuElements($key);
            Session::flash('message', trans('privateBEMenuElements.delete_ok'));
            return action('BEMenuElementsController@index');
        } catch(Exception $e) {
            return redirect()->back()->withErrors([ trans('privateBEMenuElements.delete_error') => $e->getMessage()])->getTargetUrl();
        }
    }

    /** Display a listing of the resource.
     *
     * @return mixed
     */
    public function getIndexTable(Request $request){
        try {
            $response = CM::getBEMenuElements($request);

            $actions = collect($response->beMenuElements);
            $recordsTotal = $response->recordsTotal;
            $recordsFiltered = $response->recordsFiltered;

            return Datatables::of($actions)
                ->editColumn('id',function ($element) {
                    return "<a href='".action('BEMenuElementsController@show', $element->key)."'>" . $element->id . "</a>";
                })
                ->editColumn('key',function ($element) {
                    return "<a href='".action('BEMenuElementsController@show', $element->key)."'>" . $element->key . "</a>";
                })
                ->editColumn('controller', function($element) {
                    if (!empty($element->controller))
                        return $element->controller;
                    else
                        return "<b>" . trans("beMenuElements.no_controller") . "</b>";
                })
                ->editColumn('method', function($element) {
                    if (!empty($element->method))
                        return $element->method;
                    else
                        return "<b>" . trans("beMenuElements.no_method") . "</b>";
                })
                ->editColumn('code', function($element) {
                    if (!empty($element->code))
                        return $element->code;
                    else
                        return "<b>" . trans("beMenuElements.no_code") . "</b>";
                })
                ->addColumn('action', function ($element) {
                    return ONE::actionButtons($element->key, ['form' => 'beMenuElements','edit' => 'BEMenuElementsController@edit', 'delete' => 'BEMenuElementsController@delete'] );
                })
                ->with('filtered', $recordsFiltered ?? 0)
                ->skipPaging()
                ->setTotalRecords($recordsTotal ?? 0)
                ->rawColumns(['id','key','controller','method','code','action'])
                ->make(true);
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["beMenuElements.getIndexTable" => $e->getMessage()]);
        }
    }
}