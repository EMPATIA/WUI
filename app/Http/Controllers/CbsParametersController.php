<?php

namespace App\Http\Controllers;

use App\ComModules\Auth;
use App\Http\Requests\CbParameterRequest;
use App\ComModules\CbParameterTemplate;
use App\ComModules\CB;
use App\ComModules\Files;
use App\ComModules\Orchestrator;
use Illuminate\Http\Request;
use App\One\One;
use Datatables;
use Session;
use View;
use Exception;
use Illuminate\Support\Collection;


class CbsParametersController extends Controller
{

    public function __construct()
    {


    }


    public function index()
    {

    }


    /** Create a new resource
     * @param $type
     * @param $cbKey
     * @param Request $request
     * @return View
     */
    public function create($type, $cbKey, Request $request)
    {
        try {
            if($request->has('step')){
                $step = $request->step;
            }

            if(Session::has('parameterFields'))
                Session::forget('parameterFields');

            if(Session::has('parameterTypeOptions'))
                Session::forget('parameterTypeOptions');

            $cb = CB::getCbConfigurations($cbKey);
            $languages = Orchestrator::getLanguageList();
            $parameterTemplatesKeys = Orchestrator::getParametersTemplatesKeys();
            $array = [];
            foreach($parameterTemplatesKeys as $tmp){
                $array[] = $tmp->parameter_template_key;
            }
            $parameterTemplates  = CB::getParametersTemplates($array);

            $parameterType = CB::getParametersTypes();
            $uploadKey = Files::getUploadKey();

            $templateId = "";

            if(isset($request->template)){
                $templateId = $request->template;
            }

            $imageMapFile = null;
            $parameterTemplateChoosed = null;
            if(isset($request->template)){
                foreach($parameterTemplates as $tmp){
                    if($request->template == $tmp->id)
                        $parameterTemplateChoosed = $tmp;
                }
                if(!empty($parameterTemplateChoosed->code) && $parameterTemplateChoosed->code == "image_map"){
                    $imageMapFile = Files::getFile($parameterTemplateChoosed->value);
                }
            }


            $data = [];
            $data['parameterTemplateChoosed'] = $parameterTemplateChoosed;
            $data['templateId'] = $templateId;
            $data['parameterTemplates'] = $parameterTemplates;
            $data['cbKey'] = $cbKey;
            $data['uploadKey'] = $uploadKey;
            $data['type'] = $type;
            $data['parameterType'] = $parameterType;
            $data['imageMapFile'] = $imageMapFile;
            $data['languages'] = $languages;
            $data['cb'] = $cb;
            $data['active'] = 'parameters';
            if(isset($step)){
                $data['step'] = $step;
            }

            $cb = CB::getCb($cbKey);
	    $subpad = $cb->parent_cb_id != 0;

            $author = (Auth::getUser($cb->created_by))->name;
            $cb_title = $cb->title;
            $cb_start_date = $cb->start_date;
            $data['author'] = $author;
            $data['cb_title'] = $cb_title;
            $data['cb_start_date'] = $cb_start_date;
            $data['sidebar'] = ($subpad)?'subpadsType':'padsType';

            return view('private.cbs.parameter', $data);

        } catch (Exception $e) {
            return redirect()->back()->withErrors(["Cbs.parameterCreate" => $e->getMessage()]);
        }
    }


    /**
     *Store a newly created resource in storage.
     *
     * @param Request $request
     * @param $type
     * @param $cbKey
     * @return $this|View
     */
    public function store(Request $request, $type, $cbKey)
    {
        try {
            $languages = Orchestrator::getLanguageList();
            $fileId = null;
            $optionsSelect = isset($request->optionsNew)?$request->optionsNew:null;
            $parameterTypeSelect = $request->paramTypeSelect;

            if($parameterTypeSelect == 'image_map'){
                $fileId = isset($request->file_id)?$request->file_id:null;

            }
            $mandatory = isset($request->mandatory)?$request->mandatory : 0;
            $visible = isset($request->visible)?$request->visible : 0;
            $visibleInList = isset($request->visibleInList)?$request->visibleInList : 0;
            $useFilter = isset($request->use_filter)?$request->use_filter : 0;
            $private = isset($request->private)?$request->private : 0;

            $parameterTranslations = [];
            foreach ($languages as $language) {
                $parameterName = $request->input("parameterName_" . $language->code);
                $parameterDescription = $request->input("parameterDescription_" . $language->code);
                if (!empty($parameterName)) {
                    $parameterTranslations[] = [
                        'language_code' => $language->code,
                        'parameter' => $parameterName,
                        'description' => $parameterDescription
                    ];
                }
            }
            $options = [];
            $optionTranslations = [];
            $optionField = [];
            $i = 1;
            if($optionsSelect != null){

                foreach ($optionsSelect as $opt){
                    foreach ($opt as $key => $optTrans){
                        if(!empty($optTrans)) {
                            $optionTranslations [] = ['language_code' => $key,'label' => $optTrans];
                        }

                    }
                    if($request->input("option_color_".$i) and !empty($request->input("option_color_".$i))){
                        $optionField[] = ['value' => $request->input("option_color_".$i), 'code' => 'color'];
                    }

                    if($request->input("option_max_value_".$i) and !empty($request->input("option_max_value_".$i))){
                        $optionField[] = ['value' => $request->input("option_max_value_".$i), 'code' => 'max_value'];
                    }

                    if($request->input("option_min_value_".$i) and !empty($request->input("option_min_value_".$i))){
                        $optionField[] = ['value' => $request->input("option_min_value_".$i), 'code' => 'min_value'];
                    }

                    if($request->input("option_pin_".$i) and $request->input("option_pin_".$i) != "[]"){
                        $optionField[] = ['value' => $request->input("option_pin_".$i), 'code' => 'pin'];
                    }

                    if($request->input("option_icon_".$i) and $request->input("option_icon_".$i) != "[]"){
                        $optionField[] = ['value' => $request->input("option_icon_".$i), 'code' => 'icon'];
                    }

                    $options [] = [
                        'translations' => $optionTranslations,
                        'optionFields' => $optionField,
                        'code'         => $request->input("option_code_".$i,"")
                    ];
                    $i++;
                    $optionTranslations = [];
                    $optionField = [];
                }
            }

            $fields = [];
            if($request->input("color")){
                $fields[] = ['value' => $request->input("color"), 'code' => 'color'];
            }

            if($request->input("min_value")){
                $fields[] = ['value' => $request->input("color"), 'code' => 'min_value'];
            }

            if($request->input("max_value")){
                $fields[] = ['value' => $request->input("max_value"), 'code' => 'max_value'];
            }

            if($request->input("pin")){
                $fields[] = ['value' => $request->input("pin"), 'code' => 'pin'];
            }

            if($request->input("icon")){
                $fields[] = ['value' => $request->input("icon"), 'code' => 'icon'];
            }

            $parameters = CB::getParametersTypes();
            $parameterType = collect($parameters)->where('code', $parameterTypeSelect)->first();

            $data = [
                'parameter_type_id' => $parameterType->id ?? null,
                'cb_key'            => $cbKey,
                'translations'      => $parameterTranslations,
                'code'              => $parameterTypeSelect,
                'parameter_code'    => $request->input('parameter_code') ?? null ,
                'mandatory'         => $mandatory,
                'visible'           => $visible,
                'visible_in_list'   => $visibleInList,
                'private'           => $private,
                'use_filter'        => $useFilter,
                'value'             => $fileId,
                'options'           => $options,
                'fields'            => $fields
            ];

            CB::setParameters($data);

            if($type == "project_2c")
                \App\Unimi\NestedCbs::clearCb($cbKey);

            Session::flash('message', trans('parameter.store_ok'));
            if(isset($request->param)){
                return redirect()->action('CbsController@create', ['type'=>$type,'cbKey'=>$cbKey, 'step' => 'param']);
            }else{
                return redirect()->action('CbsController@showParameters', ['type'=>$type,'cbKey'=>$cbKey]);
            }
        } catch (Exception $e) {
            //TODO: save inputs
            return redirect()->back()->withErrors(["parameter.store" => $e->getMessage()]);
        }


    }


    /** Edit resource
     * @param $type
     * @param $cbKey
     * @param $paramId
     * @return View
     */
    public function edit($type, $cbKey, $paramId)
    {
        try {
            $languages = Orchestrator::getLanguageList();
            $parameter = CB::getParameterOptionsEdit($paramId);
            $language = ONE::getAppLanguageCode();
            $optionField = count($parameter->options);
            $parameterType = CB::getParameterType($parameter->parameter_type_id);
            $uploadKey = Files::getUploadKey();
            $file = null;
            if($parameterType->code == 'image_map'){
                if(!empty($parameter->value) and is_numeric($parameter->value)) {
                    $file = Files::getFile($parameter->value);
                }
            }

            $existingCodes = [];
            foreach($parameter->options as $param){
                foreach($param->fields as $fields){
                    $existingCodes[$param->id][] = $fields->code;
                }
            }
            $title = trans('privateParameter.update_parameter');

            $active = 'parameters';
            $cb = CB::getCb($cbKey);
	        $subpad = $cb->parent_cb_id != 0;
            $sidebar = ($subpad)?'subpadsType':'padsType';
            $author = (Auth::getUser($cb->created_by))->name;
            $cb_title = $cb->title;
            $cb_start_date = $cb->start_date;

            return view('private.cbs.parameter', compact('type', 'parameter','parameterType','cbKey','paramId','file','languages','language', 'uploadKey', 'optionField', 'title', 'author', 'cb_title', 'cb_start_date', 'existingCodes', 'sidebar', 'active'));

        } catch (Exception $e) {

            return redirect()->back()->withErrors(["parameter.edit" => $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     * @param Request $request
     * @param $type
     * @param $cbKey
     * @param $paramId
     * @return View
     */
    public function show(Request $request, $type,$cbKey, $paramId)
    {
        try {
            if($request->has('step'))
                $step = $request->step;

            $languages = Orchestrator::getLanguageList();
            $parameter = CB::getParameterOptionsEdit($paramId);
            $parameterType = CB::getParameterType($parameter->parameter_type_id);
            $language = ONE::getAppLanguageCode();
            $uploadKey = Files::getUploadKey();
            $optionFieldsIndex = count($parameter->options);

            $file = null;
            if($parameterType->code == 'image_map'){
                if(!empty($parameter->value) and is_numeric($parameter->value)) {
                    $file = Files::getFile($parameter->value);
                }
            }
            $active = 'parameters';

            $title = trans('privateIdeas.show_parameters');

            $cb = CB::getCb($cbKey);
	        $subpad = $cb->parent_cb_id != 0;
            $sidebar = ($subpad)?'subpadsType':'padsType';
            $cbAuthor = (Auth::getUser($cb->created_by));
            $cb_title = $cb->title;
            $cb_start_date = $cb->start_date;
            return view('private.cbs.parameter', compact('type', 'parameter','parameterType','cbKey','paramId','file','languages', 'uploadKey', 'optionFieldsIndex', 'step','title', 'cbAuthor', 'cb_title', 'cb_start_date',  'language', 'sidebar', 'active'));


        } catch (Exception $e) {
            return redirect()->back()->withErrors(["cbsParameters.show" => $e->getMessage()]);
        }
    }


    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param $type
     * @param $cbKey
     * @param $paramId
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $type, $cbKey, $paramId)
    {
        try {
            $fileId = null;
            $parameterTypeId = $request->parameterTypeId;
            $parameterTypeCode = $request->parameterCode;
            $mandatory = isset($request->parameterMandatory)?$request->parameterMandatory : 0;
            $visible = isset($request->visible)?$request->visible : 0;
            $visibleInList = isset($request->visibleInList)?$request->visibleInList : 0;
            $useFilter = isset($request->use_filter)?$request->use_filter : 0;
            $private = isset($request->private)?$request->private : 0;
            if($parameterTypeCode == 'image_map')
                $fileId = isset($request->file_id)?$request->file_id:null;

            $optionsSelect = isset($request->optionsSelect)?$request->optionsSelect:null;
            $optionsNew = isset($request->optionsNew)?$request->optionsNew:null;

            $languages = Orchestrator::getLanguageList();

            $parameterTranslations = [];
            foreach ($languages as $language) {
                $parameterName = $request->input("parameterName_" . $language->code);
                $parameterDescription = $request->input("parameterDescription_" . $language->code);
                if (!empty($parameterName)) {
                    $parameterTranslations[] = [
                        'language_code' => $language->code,
                        'parameter' => $parameterName,
                        'description' => $parameterDescription
                    ];
                }
            }
            $options = [];
            $optionTranslations = [];
            $optionField = [];
            $i = 1;
            if($optionsSelect != null){

                foreach ($optionsSelect as $optId => $opt){
                    foreach ($opt as $key => $optTrans){
                        if(!empty($optTrans)) {
                            $optionTranslations [] = ['language_code' => $key,'label' => $optTrans];
                        }
                    }

                    if($request->input("option_color_".$i)){
                        $optionField[] = ['value' => $request->input("option_color_".$i), 'code' => 'color'];
                    }

                    if($request->input("option_max_value_".$i)){
                        $optionField[] = ['value' => $request->input("option_max_value_".$i), 'code' => 'max_value'];
                    }

                    if($request->input("option_min_value_".$i)){
                        $optionField[] = ['value' => $request->input("option_min_value_".$i), 'code' => 'min_value'];
                    }

                    if($request->input("option_pin_".$i)){
                        $optionField[] = ['value' => $request->input("option_pin_".$i), 'code' => 'pin'];
                    }else{
                        $optionField[] = ['value' => null, 'code' => 'pin'];
                    }

                    if($request->input("option_icon_".$i)){

                        $optionField[] = ['value' => $request->input("option_icon_".$i), 'code' => 'icon'];
                    }else{
                        $optionField[] = ['value' => null, 'code' => 'icon'];
                    }
                    if($request->input("not_passed_translation".$i))
                        $optionField[] = ['value' => $request->input("not_passed_translation".$i), 'code' => 'not_passed_translation'];
                    else
                        $optionField[] = ['value' => null, 'code' => 'not_passed_translation'];

                    $options [] = [
                        'option_id' => $optId,
                        'translations' => $optionTranslations,
                        'optionFields' => $optionField,
                        'code' => $request->get("option_code_" . $i,"")
                    ];
                    $i++;
                }
            }

            $fields['color'] =  $request->color;
            $fields['min_value'] = $request->min_value;
            $fields['max_value'] = $request->max_value;
            $fields['icon'] = $request->icon;
            $fields['pin'] = $request->pin;

            if($optionsNew != null){
                foreach ($optionsNew as $opt){

                    foreach ($opt as $key => $optTrans){
                        if(!empty($optTrans)) {
                            $optionTranslations [] = ['language_code' => $key,'label' => $optTrans];
                        }
                    }

                    if($request->input("option_color_".$i) and !empty($request->input("option_color_".$i))){
                        $optionField[] = ['value' => $request->input("option_color_".$i), 'code' => 'color'];
                    }

                    if($request->input("option_max_value_".$i) and !empty($request->input("option_max_value_".$i))){
                        $optionField[] = ['value' => $request->input("option_max_value_".$i), 'code' => 'max_value'];
                    }

                    if($request->input("option_min_value_".$i) and !empty($request->input("option_min_value_".$i))){
                        $optionField[] = ['value' => $request->input("option_min_value_".$i), 'code' => 'min_value'];
                    }

                    if($request->input("option_pin_".$i)){
                        $optionField[] = ['value' => $request->input("option_pin_".$i), 'code' => 'pin'];
                    }

                    if($request->input("option_icon_".$i)){
                        $optionField[] = ['value' => $request->input("option_icon_".$i), 'code' => 'icon'];
                    }

                    $i++;
                    $options [] = ['translations' => $optionTranslations, 'optionFields' => $optionField];

                }
            }

            $data = [
                'parameter_type_id' => $parameterTypeId,
                'cb_key' => $cbKey,
                'translations' => $parameterTranslations,
                'code' => $parameterTypeCode,
                'parameter_code' => $request->input('parameter_code') ?? null,
                'mandatory' => $mandatory,
                'visible' => $visible,
                'visible_in_list' => $visibleInList,
                'private' => $private,
                'use_filter' => $useFilter,
                'value' => $fileId,
                'options' =>$options,
                'fields' => $fields
            ];

            CB::updateParameter($paramId,$data);

            if($type == "project_2c")
                \App\Unimi\NestedCbs::clearCb($cbKey);

            Session::flash('message', trans('parameter.update_ok'));
            return redirect()->action('CbsParametersController@show', ['type'=>$type,'cbKey'=>$cbKey,'paramId' => $paramId]);
        } catch (Exception $e) {
            //TODO: save inputs
            return redirect()->back()->withErrors(["parameter.update" => $e->getMessage()]);
        }

    }


    /**
     * Remove the specified resource from storage.
     * @param $cbKey
     * @param $paramId
     * @return
     */
    public function destroy($type,$cbKey,$paramId)
    {
        try {
            CB::deleteParameter($paramId);

            if($type == "project_2c")
                \App\Unimi\NestedCbs::clearCb($cbKey);

            Session::flash('message', trans('parameter.delete_ok'));
            return action('CbsController@showParameters', ['type' => $type,'cbKey'=>$cbKey]);
        } catch (Exception $e) {
            //TODO: save inputs
            Session::flash('errors', $e);
            return action('CbsController@show', ['type' => $type,'cbKey'=>$cbKey]);
        }
    }
    /**
     * Show confirm popup to remove the specified resource from storage.
     * @param $cbID
     * @param $idParameter
     * @return View
     */
    public function delete($type,$cbID, $idParameter){

        $data = array();
        $data['action'] = action("CbsParametersController@destroy", ['type'=>$type,'cbKey' => $cbID,'paramId'=>$idParameter]);
        $data['title'] = trans('privateCbsParameters.delete');
        $data['msg'] = trans('privateCbsParameters.are_you_sure_you_want_to_delete').' ?';
        $data['btn_ok'] = trans('privateCbsParameters.delete');
        $data['btn_ko'] = trans('privateCbsParameters.cancel');

        return view("_layouts.deleteModal", $data);


    }


    /** Get index's parameters
     * @param $type
     * @param $cbKey
     * @return $this
     */
    public function getIndexTableParameters($type, $cbKey){

        try {
            $module = 'cb';
            $type = 'pad_parameters';
            if(Session::get('user_role') == 'admin' || Session::get('user_permissions')->$module->$type->permission_show){
                $parameters = CB::getCbParameters($cbKey);
                // in case of json
                foreach ($parameters as $parameter) {

                    $auth=Auth::getUser($cbKey);


                    $parameter->name=$auth->name;
                }

                $collection = Collection::make($parameters);
            }else
                $collection = Collection::make([]);

            $edit = Session::get('user_role') == 'admin' || Session::get('user_permissions')->$module->$type->permission_update;
            $delete = Session::get('user_role') == 'admin' || Session::get('user_permissions')->$module->$type->permission_delete;

            return Datatables::of($collection)
                ->editColumn('title', function ($collection) use ($type,$cbKey) {
                    return "<a href='" . action('CbsParametersController@show', ['type' => $type, 'cbKey' => $cbKey, 'paramId' => $collection->id]) . "'>" . $collection->parameter . "</a>";
                })
                ->addColumn('action', function ($collection) use ($type,$cbKey, $edit, $delete) {
                    if($edit == true and $delete == true)
                        return ONE::actionButtons(['type' => $type, 'cbKey' => $cbKey, 'id' => $collection->id], ['form'=> 'parameters','edit' => 'CbsParametersController@edit', 'delete' => 'CbsParametersController@delete']);
                    elseif($edit == true and $delete == false)
                        return ONE::actionButtons(['type' => $type, 'cbKey' => $cbKey, 'id' => $collection->id], ['form'=> 'parameters','edit' => 'CbsParametersController@edit']);
                    elseif($edit == false and $delete == true)
                        return ONE::actionButtons(['type' => $type, 'cbKey' => $cbKey, 'id' => $collection->id], ['form'=> 'parameters', 'delete' => 'CbsParametersController@delete']);
                    else
                        return null;

                })
                ->make(true);
        }catch (Exception $e) {
            //TODO: save inputs
            return redirect()->back()->withErrors(["cbs.getIndexTableParameters" => $e->getMessage()]);
        }

    }


    /** Pass options to html
     * @param $param
     * @param $paramOptions
     * @param $type
     * @return string
     */
    public function optionsHtml($param, $paramOptions, $type){

        $disabled = '';
        $selected = '';
        if($type == 'show'){
            $disabled = 'disabled';
            $selected = 'selected';
        }
        elseif($type == 'edit'){
            $selected = '';
        }

        $html = '';

        switch (strtoupper($param->parameter)) {
            case 'IMAGE MAP':
                if($type != 'show') {
                    $html .= '<div id="imageMap"></div>';
                }
                elseif($type == 'show') {
                    //TODO: getImage and show
                    $response = Files::getFile($param->pivot->value);

                    $fileCode = $response->code;
                    $html .= '<div class="box-body"> <div class="col-lg-3">';
                    $html .= '<img class="img" src="https://empatia-test.onesource.pt:5005/file/download/' . $param->pivot->value . '/' . $fileCode . '"  id="image_map">';
                    $html .= '</div></div>';

                }
                break;
            case 'CATEGORY':
                $html .= '<p></p><select class="form-control" id="optionSelect" name="optionSelect[]" multiple '.$disabled.'>';
                foreach ($paramOptions as $option) {

                    $html .= '<option  value="' . $option->id . '" '.$selected.'> ' . $option->name . '</option>';

                }
                $html .= '</select>';

                if($type == 'edit') {
                    $html .= '<div id="newOption"><p></label> </p></div>';
                    $html .= '<button class="btn btn-default" id="btnOption" onclick="addOption()">Add new option</button>';
                }
                break;
            default:
                $html .= '<div class="btn-group">';
                $html .= '<label class="input-group btn-group-vertical">Options</label>';
                $html .= '<p></p><select class="form-control " id="optionSelect" name="optionSelect[]" multiple required '.$disabled.'>';
                foreach ($paramOptions as $option) {
                    $html .= '<option  value="'.$option->id.'" '.$selected.'> '.$option->label.'</option>';
                }
                $html .= '</select>';
                $html .= '</div>';
                if($type == 'edit') {
                    $html .= '<div id="newOption"><p></label> </p></div>';
                    $html .= '<button class="btn btn-default" id="btnOption" onclick="addOption()">Add new option</button>';
                }
        }

        return $html;

    }


    /**Get parameter options
     * @param Request $request
     * @return string
     */
    public function getParameterOptions(Request $request)
    {
        $paramId = json_decode($request->postId);

        $param =  CB::getParameterOptions($paramId);
        $paramType = $param->parameter_type_id;
        $paramOptions = $param->options;

        $html = '';
        //Get param type name
        $response = CB::getParameterType($paramType);
        $name = $response->name;

        if($name == 'Text' || $name == 'Numeric' || $name == 'Text Area' || $name == 'Google Maps'){
            $html .= '<div id="textInput"></div>';
        } else if($name == 'Radio Buttons' || $name == 'Check Box'){

            switch (strtoupper($param->parameter)) {

                case 'CATEGORY':
                    $categories = Orchestrator::listCategories();
                    $flag = 0;

                    $html .= '<p></p><select class="form-control" id="optionSelect" name="optionSelect[]" multiple>';
                    foreach ($categories as $cat) {

                        $html .= '<option  value="' . $cat->id . '"> ' . $cat->name . '</option>';

                    }
                    $html .= '</select>';

                    $html .= '<div id="newOption"><p></label> </p></div>';
                    $html .= '<p></p><button class="btn btn-default" id="btnOption" onclick="addOption()">Add new option</button>';
                    break;
                default:
                    $html .= '<p></p><select class="form-control" id="optionSelect" name="optionSelect[]" multiple required>';
                    foreach ($paramOptions as $option) {
                        $html .= '<option  value="'.$option->id.'"> '.$option->label.'</option>';
                    }
                    $html .= '</select>';

                    $html .= '<div id="newOption"><p></label> </p></div>';

                    $html .= '<p></p><button class="btn btn-default" id="btnOption" onclick="addOption()">Add new option</button>';
            }

        }  else if($name == 'Image'){

            $html .= '<div id="imageMap"></div>';
        }

        return $html;
    }



    /**Get parameter options
     * @param Request $request
     * @return string
     */
    public function addParameterOptions(Request $request)
    {

        $idParam = $request->idParam;
        $label = $request->label;

        $newOptions [] = ['label' => $label];
        return CB::setParametersWithNewOptions($idParam, $newOptions);
    }

    /**
     * Add Files to specific content.
     *
     * @param Request $request
     * @return Response
     */
    public function addImageMap(Request $request){
        try{
            $fileId = $request->file_id;
            return $this->getFile($fileId);
        }
        catch(Exception $e) {
            return "false";
        }
    }

    public function getFile($file_id){
        try{
            $response = Files::getFile($file_id);
            $file = ['id' => $file_id,'name' => $response->name,'size' => $response->size, 'type' => $response->type];
            return $file;
        }
        catch(Exception $e) {
            return 'false';
        }
    }

    public function getNewOptionFields(Request $request, $type, $cbKey){
        $uploadKey = $request->uploadKey;
        $optionField = $request->optionField;

        if(Session::has('parameterFields')){
            $parameterTypeFields = Session::get('parameterFields');

            $fieldTypes = [];
            foreach($parameterTypeFields as $fields){
                $fieldTypes[] = $fields->code;
            }

            return view('private.cbs.parameterOptions', compact('uploadKey', 'optionField', 'fieldTypes'));
        }else{
            return '';
        }



    }

    public function getNewFields(Request $request, $type, $cbKey){

        $uploadKey = $request->uploadKey;

        $parameterTypeFields = Session::get('parameterFields');

        $fieldTypes = [];
        foreach($parameterTypeFields as $fields){
            $fieldTypes[] = $fields->code;
        }

        return view('private.cbs.parameterField', compact('uploadKey', 'fieldTypes'));
    }

    public function getParameterType(Request $request){
        if(Session::has('parameterFields'))
            Session::forget('parameterFields');

        if(Session::has('parameterFields'))
            Session::forget('parameterTypeOptions');

        $parameterTypes = CB::getParametersTypes();

        foreach($parameterTypes as $type){
            if($type->code == $request->code) {
                $parameterType = $type;
                break;
            }
        }

        if(isset($parameterType) and !empty($parameterType->param_add_fields)){
            Session::set('parameterFields', $parameterType->param_add_fields);
        }

        if(isset($parameterType) and $parameterType->options == 1){
            Session::set('parameterTypeOptions', $parameterType->options);
        }

        return ['parameterType' => $parameterType];
    }

}
