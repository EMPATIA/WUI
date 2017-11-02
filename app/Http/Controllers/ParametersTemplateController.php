<?php

namespace App\Http\Controllers;

use App\ComModules\CB;
use App\ComModules\Files;
use App\ComModules\Orchestrator;
use App\Http\Requests\ParameterTemplateRequest;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Session;
use View;
use Breadcrumbs;

use App\One\One;
use Exception;
use Illuminate\Support\Collection;
use Datatables;

class ParametersTemplateController extends Controller
{
    public function __construct()
    {

    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $name_view = "parameters_template";
        $title = trans('privateParameterTemplates.list_ParameterTemplates');
        return view('private.parametersTemplate.index', compact('title', 'name_view'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(Session::get('user_role') != 'admin'){
            if(!ONE::verifyUserPermissionsCreate('cb', 'parameter_template')) {
                return redirect()->back()->withErrors(["parametersTemplate.create" => trans('privateParametersTemplate.permission_message')]);
            }
        }

        try {
            $name_view = "parameters_template";

            $parameterType = CB::getParametersTypes();
            $uploadKey = Files::getUploadKey();

            $title = trans('privateParameterTemplates.create_parameterTemplate');
            return view('private.parametersTemplate.parameter', compact('title', 'uploadKey', 'parameterType', 'name_view'));

        } catch (Exception $e) {
            return redirect()->back()->withErrors([trans('parametersTemplate.create') => $e->getMessage()]);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param ParameterTemplateRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(ParameterTemplateRequest $request)
    {
        if(Session::get('user_role') != 'admin'){
            if(!ONE::verifyUserPermissionsCreate('cb', 'parameter_template')) {
                return redirect()->back()->withErrors(["parametersTemplate.store" => trans('privateParametersTemplate.permission_message')]);
            }
        }

        try {
            $fileId = null;
            $optionsSelect = isset($request->optionsNew)?$request->optionsNew:null;
            $parameterTypeSelect = $request->paramTypeSelect;
            if($parameterTypeSelect == 'image_map')
                $fileId = isset($request->file_id)?$request->file_id:null;
            $parameterDescription = $request->parameterDescription;
            $mandatory = isset($request->mandatory)?$request->mandatory : 0;
            $visible = isset($request->visible)?$request->visible : 0;
            $visibleInList = isset($request->visibleInList)?$request->visibleInList : 0;
            $useFilter = isset($request->use_filter)?$request->use_filter : 0;
            $parameterName = $request->parameterName;


            $parameters = CB::getParametersTypes();
            foreach ($parameters as $parameter){
                if($parameter->code == $parameterTypeSelect){
                    $parameterTypeId = $parameter->id;
                    break;
                }
            }
            $options = [];
            if($optionsSelect != null){
                foreach ($optionsSelect as $opt){
                    if(!empty($opt))
                        $options [] = ['label' => $opt];
                }
            }
            $data = [
                'parameter_type_id' => $parameterTypeId,
                'parameter'         => $parameterName,
                'description'       => $parameterDescription,
                'code'              => $parameterTypeSelect,
                'mandatory'         => $mandatory,
                'visible'           => $visible,
                'visible_in_list'   => $visibleInList,
                'use_filter'        => $useFilter,
                'value'             => $fileId,
                'options'           => $options
            ];

            $parameterTemplate = CB::setParametersTemplate($data);
            Orchestrator::setParameterTemplate($parameterTemplate->parameter_template_key);
            Session::flash('message', trans('parameterTemplate.store_ok'));
            return redirect()->action('ParametersTemplateController@show', ['parameterTemplateKey' => $parameterTemplate->parameter_template_key]);
        } catch (Exception $e) {
            //TODO: save inputs
            return redirect()->back()->withErrors([trans('parameterTemplate.storeNok') => $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param $key
     * @return \Illuminate\Http\Response
     */
    public function show($key)
    {
        if(Session::get('user_role') != 'admin'){
            if(!ONE::verifyUserPermissionsShow('cb', 'parameter_template')) {
                return redirect()->back()->withErrors(["parametersTemplate.show" => trans('privateParametersTemplate.permission_message')]);
            }
        }

        try {
            $name_view = "parameters_template";
            $parameterTemplate = CB::getParameterTemplateOptions($key);
            $parameterType = CB::getParameterType($parameterTemplate->parameter_type_id);
            $file = null;
            if($parameterType->code == 'image_map'){
                if(!empty($parameterTemplate->value) and is_numeric($parameterTemplate->value)) {
                    $file = Files::getFile($parameterTemplate->value);
                }
            }

            $title = trans('privateParameterTemplates.show_parameterTemplate').' '.(isset($parameterTemplate->parameter) ? $parameterTemplate->parameter: null);
            return view('private.parametersTemplate.parameter', compact('title', 'parameterTemplate','parameterType','file', 'name_view'));


        } catch (Exception $e) {
            return redirect()->back()->withErrors(["cbsParameters.show" => $e->getMessage()]);
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
        if(Session::get('user_role') != 'admin'){
            if(!ONE::verifyUserPermissionsUpdate('cb', 'parameter_template')) {
                return redirect()->back()->withErrors(["parametersTemplate.edit" => trans('privateParametersTemplate.permission_message')]);
            }
        }
        
        try {
            $name_view = "parameters_template";
            $parameterTemplate = CB::getParameterTemplateOptions($key);
            $parameterType = CB::getParameterType($parameterTemplate->parameter_type_id);
            $file = null;
            if($parameterType->code == 'image_map'){
                if(!empty($parameterTemplate->value) and is_numeric($parameterTemplate->value)) {
                    $file = Files::getFile($parameterTemplate->value);
                }
            }
            $title = trans('privateParameterTemplates.show_parameterTemplate').' '.(isset($parameterTemplate->parameter) ? $parameterTemplate->parameter: null);
            return view('private.parametersTemplate.parameter', compact('title', 'parameterTemplate','parameterType','file', 'name_view'));


        } catch (Exception $e) {
            return redirect()->back()->withErrors(["cbsParameters.show" => $e->getMessage()]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param ParameterTemplateRequest $request
     * @param $templateKey
     * @return \Illuminate\Http\Response
     */
    public function update(ParameterTemplateRequest $request, $templateKey)
    {
        if(Session::get('user_role') != 'admin'){
            if(!ONE::verifyUserPermissionsUpdate('cb', 'parameter_template')) {
                return redirect()->back()->withErrors(["parametersTemplate.update" => trans('privateParametersTemplate.permission_message')]);
            }
        }

        try {
            $fileId = null;
            $parameterTypeId = $request->paramTypeSelect;
            $parameterTypeCode = $request->parameterCode;
            if($parameterTypeCode == 'image_map')
                $fileId = isset($request->file_id)?$request->file_id:null;
            $parameterDescription = $request->parameterDescription;
            $mandatory = isset($request->parameterMandatory)?$request->parameterMandatory : 0;
            $visible = isset($request->visible)?$request->visible : 0;
            $visibleInList = isset($request->visibleInList)?$request->visibleInList : 0;
            $useFilter = isset($request->use_filter)?$request->use_filter : 0;
            $parameterName = $request->parameterName;
            $optionsSelect = isset($request->optionsSelect)?$request->optionsSelect:null;
            $optionsSelectIds = isset($request->optionsSelectIds)?$request->optionsSelectIds:null;
            $optionsNew = isset($request->optionsNew)?$request->optionsNew:null;

            $options = [];
            if($optionsSelect != null and $optionsSelectIds != null){
                foreach ($optionsSelect as $key => $opt){
                    if(!empty($opt)) {
                        $options [] = ['label' => $opt,'id' =>$optionsSelectIds[$key]];
                    }
                }
            }
            if($optionsNew != null){
                foreach ($optionsNew as $opt){
                    if(!empty($opt)) {
                        $options [] = ['label' => $opt];
                    }
                }
            }
            $data = [
                'parameter_type_id' => $parameterTypeId,
                'parameter' => $parameterName,
                'description' => $parameterDescription,
                'code' => $parameterTypeCode,
                'mandatory' => $mandatory,
                'visible'           => $visible,
                'visible_in_list'   => $visibleInList,
                'use_filter' => $useFilter,
                'value' => $fileId,
                'options' =>$options
            ];
            CB::updateParameterTemplate($templateKey,$data);
            Session::flash('message', trans('parameterTemplate.updateOk'));
            return redirect()->action('ParametersTemplateController@show', ['key' => $templateKey]);
        } catch (Exception $e) {
            //TODO: save inputs
            return redirect()->back()->withErrors([trans('parameterTemplate.updateNok') => $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $key
     * @return \Illuminate\Http\Response
     */
    public function destroy($key)
    {
        if(Session::get('user_role') != 'admin'){
            if(!ONE::verifyUserPermissionsDelete('cb', 'parameter_template')) {
                return redirect()->back()->withErrors(["parametersTemplate.destroy" => trans('privateParametersTemplate.permission_message')]);
            }
        }
        
        try {
            CB::deleteParameterTemplate($key);
            Orchestrator::deleteParameterTemplate($key);
            Session::flash('message', trans('parameter.deleteOk'));
            return action('ParametersTemplateController@index');
        } catch (Exception $e) {
            //TODO: save inputs
            Session::flash('errors', $e);
            return action('ParametersTemplateController@index');
        }
    }

    /**
     * Show confirm popup to remove the specified resource from storage.
     *
     * @param $key
     * @return View
     */
    public function delete($key){
        $data = array();

        $data['action'] = action("ParametersTemplateController@destroy", ['key' => $key]);
        $data['title'] = "DELETE";
        $data['msg'] = "Are you sure you want to delete this Parameter template?";
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
        if(Session::get('user_role') == 'admin' || ONE::verifyUserPermissions('cb', 'parameter_template', 'show')){
            $paramTemplateKeys = Orchestrator::getParametersTemplatesKeys();
            $array = [];
            foreach($paramTemplateKeys as $tmp){
                $array[] = $tmp->parameter_template_key;
            }
            $parameterTemplates  = CB::getParametersTemplates($array);

            // in case of json
            $parameters = Collection::make($parameterTemplates);
        }else
            $parameters = Collection::make([]);

        $edit = ONE::verifyUserPermissions('cb', 'parameter_template', 'update');
        $delete = ONE::verifyUserPermissions('cb', 'parameter_template', 'delete');

        return Datatables::of($parameters)
            ->editColumn('parameter', function ($parameter) {
                return "<a href='".action('ParametersTemplateController@show', $parameter->parameter_template_key)."'>".$parameter->parameter."</a>";
            })
            ->addColumn('action', function ($parameter) use($edit, $delete){
                if($edit and $delete)
                    return ONE::actionButtons($parameter->parameter_template_key, ['form' => 'parametersTemplate','edit' => 'ParametersTemplateController@edit', 'delete' => 'ParametersTemplateController@delete']);
                elseif($edit==false and $delete)
                    return ONE::actionButtons($parameter->parameter_template_key, ['form' => 'parametersTemplate', 'delete' => 'ParametersTemplateController@delete']);
                elseif($edit and $delete==false)
                    return ONE::actionButtons($parameter->parameter_template_key, ['form' => 'parametersTemplate','edit' => 'ParametersTemplateController@edit']);
                else
                    return null;
            })
            ->make(true);
    }
}
