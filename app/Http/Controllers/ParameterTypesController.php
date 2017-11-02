<?php

namespace App\Http\Controllers;

use App\ComModules\CB;
use App\ComModules\Orchestrator;
use App\Http\Requests\ParameterTypesRequest;
use App\One\One;
use Datatables;
use Exception;
use Illuminate\Support\Collection;
use Session;
use View;

class parameterTypesController extends Controller
{

    public function __construct()
    {
        View::share('title', trans('privateParameterTypes.title'));


    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title = trans('privateParameterTypes.list_parameter_types');
        return view('private.parameterTypes.index', compact('title'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $fieldTypes = CB::getFieldTypes();
        $languages = Orchestrator::getLanguageList();

        foreach($fieldTypes as $type){
            $fieldType[$type->id] = $type->name;
        }

        $data = array();
        $data['fieldTypeSelect'] = $fieldType;
        $data['fieldTypes'] = $fieldTypes;
        $data['languages'] = $languages;

        return view('private.parameterTypes.parameterType', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param LayoutRequest|\Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function store(ParameterTypesRequest $request)
    {
        try {
            $fieldTypes = CB::getFieldTypes();

            if (!isset($request["user_parameter"]))
                $request["user_parameter"] = "0";

            $languages = Orchestrator::getLanguageList();
            $array_types = [];

            $array_of_selected_types = explode(',', $request['typesSelected']);

            foreach($array_of_selected_types as $value){
                if($value != "")
                    $array_types[] = (integer) $value;
            }
            $translations = [];
            $types = [];
            if(!empty($array_types) and !empty($fieldTypes)){
                foreach($fieldTypes as $type){
                    if(in_array($type->id, $array_types)) {
                        if ($type->code == 'icon' || $type->code == 'pin') {
                            $value = $request['width' . $type->code] . ',' . $request['height' . $type->code];
                            $types[] = ['id' => $type->id, 'code' => $type->code, 'value' => $value];
                        } elseif ($type->code == 'max_value' || $type->code == 'min_value') {
                            $types[] = ['id' => $type->id, 'code' => $type->code, 'value' => $request['max_value']];
                        } elseif ($type->code == 'min_value') {
                            $types[] = ['id' => $type->id, 'code' => $type->code, 'value' => $request['min_value']];
                        } else {
                            $types[] = ['id' => $type->id, 'code' => $type->code, 'value' => null];
                        }
                    }
                    foreach($languages as $language){
                        if(!empty($request[$type->code.'_content_name_'.$language->code])) {

                            $translations[$type->code][] = [
                                'language_code' => $language->code,
                                'name'          => $request[$type->code . '_content_name_' . $language->code],
                                'description'   => $request[$type->code . '_content_description_' . $language->code],
                            ];
                        }
                    }
                }
            }

            $options = $request->options ?? 0;

            $parameterType = CB::setParameterType($request->name, $request->code, $options, $translations, $types);
            Session::flash('message', trans('privateParameterTypes.store_ok'));
            return redirect()->action('ParameterTypesController@show', $parameterType->id);
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors([ trans('privateParameterTypes.store_nok') => $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param $parameterTypeID
     * @return \Illuminate\Http\Response
     * @internal param int $id
     */
    public function show($parameterTypeID)
    {
        try {
            $parameterType = CB::getParameterType($parameterTypeID);
            return view('private.parameterTypes.parameterType', compact('parameterType'));
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors([trans('privateParameterTypes.show') => $e->getMessage()]);
        }
    }

    public function edit($parameterTypeID){
        try{
            $parameterType = CB::getParameterType($parameterTypeID);
            $selectedTypes = "";
            foreach($parameterType->param_add_fields as $type){
                $selectedTypes .= $type->field_type_id . ",";
            }

            $selectedTypesArray = [];
            foreach($parameterType->param_add_fields as $type){
                $selectedTypesArray[] = $type->field_type_id;
            }

            $fieldTypes = CB::getFieldTypes();
            $languages = Orchestrator::getLanguageList();

            foreach($fieldTypes as $type){
                $fieldType[$type->id] = $type->name;
            }

            $title = trans('privateParameterTypes.edit_parameter_types') . " " . $parameterType->name;

            $data = array();
            $data['selectedTypes'] = $selectedTypes ?? null;
            $data['selectedTypesArray'] = $selectedTypesArray ?? null;
            $data['fieldTypeSelect'] = $fieldType;
            $data['fieldTypes'] = $fieldTypes;
            $data['parameterType'] = $parameterType;
            $data['languages'] = $languages;
            $data['title'] = $title;

            return view('private.parameterTypes.parameterType', $data);

            return view('private.parameterTypes.parameterType', compact('parameterType'));
        }catch(Exception $e) {
            return redirect()->back()->withErrors([trans('privateParameterTypes.edit') => $e->getMessage()]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param ParameterTypesRequest|\Illuminate\Http\Request $request
     * @param $parameterTypeID
     * @return \Illuminate\Http\Response
     * @internal param int $id
     */
    public function update(ParameterTypesRequest $request, $parameterTypeID)
    {
        try {
            $fieldTypes = CB::getFieldTypes();
            if (!isset($request["user_parameter"]))
                $request["user_parameter"] = "0";

            if(isset($request->options))
                $options = $request->options;
            else
                $options = 0;

            $languages = Orchestrator::getLanguageList();

            $array_types = [];
            $array_of_selected_types = explode(',', $request['typesSelected']);
            foreach($array_of_selected_types as $value){
                if($value != "" and $value !=0)
                    $array_types[] = (integer) $value;
            }

            $types = [];
            $translations = [];
            foreach($fieldTypes as $type){
                if(in_array($type->id, $array_types)) {
                    if ($type->code == 'icon' || $type->code == 'pin') {
                        $value = $request['width' . $type->code] . ',' . $request['height' . $type->code];
                        $types[] = ['id' => $type->id, 'code' => $type->code, 'value' => $value];
                    } elseif ($type->code == 'max_value' || $type->code == 'min_value') {
                        $types[] = ['id' => $type->id, 'code' => $type->code, 'value' => $request['max_value']];
                    } elseif ($type->code == 'min_value') {
                        $types[] = ['id' => $type->id, 'code' => $type->code, 'value' => $request['min_value']];
                    } else {
                        $types[] = ['id' => $type->id, 'code' => $type->code, 'value' => " "];
                    }
                }
                foreach($languages as $language){
                    if(!empty($request[$type->code.'_content_name_'.$language->code])) {

                        $translations[$type->code][] = [
                            'language_code' => $language->code,
                            'name'          => $request[$type->code . '_content_name_' . $language->code],
                            'description'   => $request[$type->code . '_content_description_' . $language->code],
                        ];
                    }
                }
            }
            $parameter = CB::updateParameterTypes($parameterTypeID, $request->name, $request->code, $options, $translations, $types);
            Session::flash('message', trans('privateParameterTypes.update_ok'));
            return redirect()->action('ParameterTypesController@show', $parameterTypeID);

        }
        catch(Exception $e) {
            //TODO: save inputs
            return redirect()->back()->withErrors([ trans('privateParameterTypes.update_nok') => $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $parameterTypeID
     * @return \Illuminate\Http\Response
     * @internal param int $id
     */
    public function destroy($parameterTypeID)
    {
        try {
            CB::deleteParameterType($parameterTypeID);
            Session::flash('message', trans('privateParameterTypes.delete_ok'));
            return action('ParameterTypesController@index');

        }
        catch(Exception $e) {
            return redirect()->back()->withErrors([ trans('privateParameterTypes.delete_nok') => $e->getMessage()]);
        }
    }


    /**
     * Show delete resource confirmation
     * Remove the specified resource from storage.
     * @param $layoutKey
     * @return View
     */
    public function delete($parameterTypeID)
    {
        $data = array();

        $data['action'] = action("ParameterTypesController@destroy", $parameterTypeID);
        $data['title'] = "DELETE";
        $data['msg'] = "Are you sure you want to delete this parameter type?";
        $data['btn_ok'] = "Delete";
        $data['btn_ko'] = "Cancel";

        return view("_layouts.deleteModal", $data);
    }


    /**
     * Display a listing of the resource.
     * @return mixed
     * @throws Exception
     */
    public function getIndexTable()
    {

        $parameterTypes = CB::getParametersTypes();
        // in case of json
        $collection = Collection::make($parameterTypes);

        return Datatables::of($collection)
            ->editColumn('name', function ($collection) {
                return "<a href='".action('ParameterTypesController@show', $collection->id)."'>".$collection->name."</a>";
            })
            ->addColumn('action', function ($collection) {
                return ONE::actionButtons($collection->id, ['form' => 'parameterType',/*'edit' => 'ParameterTypesController@edit', */'delete' => 'ParameterTypesController@delete']);
            })
            ->make(true);
    }

    public function getFieldTypes(){
        $fieldTypes = CB::getFieldTypes();


        $data = array();

        $data['fieldType'] = $fieldTypes;

        return view("_layouts.parametersTypeFieldTypes", $data);
    }

}