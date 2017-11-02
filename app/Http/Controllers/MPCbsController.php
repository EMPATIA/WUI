<?php

namespace App\Http\Controllers;

use App\ComModules\Auth;
use App\ComModules\CB;
use App\ComModules\Files;
use App\ComModules\MP;
use App\ComModules\Orchestrator;
use App\ComModules\Vote;
use App\Http\Requests\CbsRequest;
use Cache;
use Datatables;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Session;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\URL;

class MPCbsController extends Controller
{
    private $cbType;
    /**
     * MPCbsController constructor.
     */
    public function __construct()
    {
        $this->cbType = [
            'forum' => 'forum',
            'discussion' => 'discussion',
            'proposal' => 'proposal',
            'idea' => 'idea',
            'tematicConsultation' => 'tematicConsultation',
            'publicConsultation' => 'publicConsultation',
            'survey' => 'survey'
        ];
    }


    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function index(Request $request)
    {
        $mpKey = $request->mp_key;
        return redirect()->action('MPsController@showConfigurations', $mpKey);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function create(Request $request)
    {
        try {
            $operatorKey = $request->operator_key;
            if(empty($operatorKey)){
                throw new Exception(trans('privateMPCbs.error_in_operator'));
            }
            $operator = MP::getOperator($operatorKey);
            $configurations = CB::getConfigurations();
            $parameterType = CB::getParametersTypes();
            $languages = Orchestrator::getLanguageList();
            $data = [];
            $data["operator"] = $operator;
            $data["type"] = $operator->operator_type->code;
            $data["configurations"] = $configurations;
            $data["parameterType"] = $parameterType;
            $data["languages"] = $languages;
            return view('private.mps.cb.create', $data);

        }catch (Exception $e){
            return redirect()->back()->withErrors([trans('privateMPCbs.create_error') => $e->getMessage()]);
        }
    }

    /**
     * Store a newly created resource in storage.
     * @param CbsRequest $requestCB
     * @return MPCbsController|\Illuminate\Http\RedirectResponse
     */
    public function store(CbsRequest $requestCB)
    {
        try {

            $mpKey = $requestCB['mp_key'];
            $operatorKey = $requestCB['operator_key'];
            $api = $this->getApiByType($requestCB['operator_type']);
            $parameters = Orchestrator::getParametersTypes();

            /** CB details */
            $data = ['title' => $requestCB["title"],
                'contents' => $requestCB["description"],
                'start_date' => $requestCB["start_date"],
                'end_date' => $requestCB["end_date"]];

            /** Configurations */
            $configurations = CB::getConfigurations();
            $arrayConfIDs = [];
            foreach ($configurations as $configuration) {
                foreach ($configuration->configurations as $options) {
                    $arrayConfIDs[] = $options->id;

                }
            }
            $arrayConfigurations = [];
            foreach ($requestCB->all() as $key => $value) {
                if (strpos($key, 'configuration_') !== false) {
                    $id = str_replace("configuration_", "", $key);
                    $arrayConfigurations[] = $id;
                    unset($arrayConfIDs[array_search($id, $arrayConfIDs)]);
                }
            }
            $data["configurations"] = $arrayConfigurations;

            // Store parameters
            $parameterTypeId = 0;
            $parametersData = [];
            $parameterItensIds = !empty($requestCB->parameterItensIds) ? explode(",",$requestCB->parameterItensIds) : [];
            foreach($parameterItensIds as $parameterItensId){
                $fileId = null;
                $optionsSelect = !empty($requestCB->input('optionsNew_'.$parameterItensId)) ? $requestCB->input('optionsNew_'.$parameterItensId) : null;
                $parameterTypeSelect = !empty($requestCB->input('paramTypeSelect_'.$parameterItensId)) ? $requestCB->input('paramTypeSelect_'.$parameterItensId) :null;

                if($parameterTypeSelect == 'image_map')
                    $fileId = !empty($requestCB->input('file_id_'.$parameterItensId)) ? $requestCB->input('file_id_'.$parameterItensId) :null;

                $mandatory = !empty($requestCB->input('mandatory_'.$parameterItensId)) ? $requestCB->input('mandatory_'.$parameterItensId) : 0;
                $useFilter = !empty($requestCB->input('use_filter_'.$parameterItensId)) ? $requestCB->input('use_filter_'.$parameterItensId) : 0;
                $visible = !empty($requestCB->input('visible_'.$parameterItensId)) ? $requestCB->input('visible_'.$parameterItensId) : 0;
                $visibleInList = !empty($requestCB->input('visibleInList_'.$parameterItensId)) ? $requestCB->input('visibleInList_'.$parameterItensId) : 0;

                //parameter translations
                $parameterTranslations = [];
                $parameterTranslationsInput = !empty($requestCB->input('parameterName_'.$parameterItensId)) ? $requestCB->input('parameterName_'.$parameterItensId) : [];
                if(count($parameterTranslationsInput) > 0){
                    foreach ($parameterTranslationsInput as $key => $paramName) {
                        if (!empty($paramName)) {
                            $parameterDescriptionTranslations = !empty($requestCB->input('parameterDescription_'.$parameterItensId)) ? $requestCB->input('parameterDescription_'.$parameterItensId) : [];
                            $parameterDescription = !empty($parameterDescriptionTranslations[$key]) ? $parameterDescriptionTranslations[$key] : '';
                            $parameterTranslations[] = [
                                'language_code' => $key,
                                'parameter' => $paramName,
                                'description' => $parameterDescription
                            ];
                        }
                    }
                }
                // Options translations
                $options = [];
                $optionTranslations = [];
                if($optionsSelect != null){
                    foreach ($optionsSelect as $opt){
                        foreach ($opt as $key => $optTrans){
                            if(!empty($optTrans)) {
                                $optionTranslations [] = ['language_code' => $key,'label' => $optTrans];
                            }
                        }
                        $options [] = ['translations' => $optionTranslations];
                        $optionTranslations = [];
                    }
                }

                foreach ($parameters as $parameter){
                    if($parameter->code == $parameterTypeSelect){
                        $parameterTypeId = $parameter->id;
                        break;
                    }
                }

                if(!empty($parameterTranslations)) {
                    $parametersData[] = [
                        'parameter_type_id' => $parameterTypeId,
                        'translations' => $parameterTranslations,
                        'code' => $parameterTypeSelect,
                        'mandatory' => $mandatory,
                        'use_filter' => $useFilter,
                        'visible' => $visible,
                        'visible_in_list' => $visibleInList,
                        'value' => $fileId,
                        'options' => $options
                    ];
                }
            }

            $data["parameters"] = $parametersData;


            // Moderators
            $moderators = [];
            $keys = !empty($requestCB->moderators) ? $requestCB->moderators : [];
            foreach($keys as $key){
                $moderators[] = array('user_key' => $key,'type_id' => 1);
            }
            // CB::setCbModerators( $cb->cb_key,$moderators);
            $data["moderators"] = $moderators;

            // ...
            $cb = CB::setStepperNewCb($data);

            MP::updateOperator($operatorKey,$cb->cb_key);

            Session::flash('message', trans('privateMPCbs.store_ok'));
            return redirect()->action('MPsController@showConfigurations', ['mp_key' => $mpKey]);
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors([trans('privateMPCbs.store_error') => $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param $operatorKey
     * @return MPCbsController|\Illuminate\Http\RedirectResponse
     */
    public function show($operatorKey)
    {
        try {
            $operator = MP::getOperator($operatorKey);
            $cb = CB::getCb($operator->component_key);
            $moderatorsKeys = collect($cb->moderators)->pluck('user_key');
            $moderators = Auth::getListNames($moderatorsKeys);
            $cbConfigurations = collect($cb->configurations)->pluck('id')->toArray();
            $configurations = CB::getConfigurations();
            $parameterType = CB::getParametersTypes();
            $languages = Orchestrator::getLanguageList();
            $data = [];
            $data["operator"] = $operator;
            $data["cb"] = $cb;
            $data["type"] = $operator->operator_type->code;
            $data["configurations"] = $configurations;
            $data["cbConfigurations"] = $cbConfigurations;
            $data["parameters"] = $cb->parameters;
            $data["parameterType"] = $parameterType;
            $data["languages"] = $languages;
            $data["moderators"] = $moderators;



            return view('private.mps.cb.create', $data);

        }catch (Exception $e){
            return redirect()->back()->withErrors([trans('privateMPCbs.create_error') => $e->getMessage()]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param $operatorKey
     * @return MPCbsController|\Illuminate\Http\RedirectResponse
     */
    public function edit($operatorKey)
    {
        try {
            $operator = MP::getOperator($operatorKey);
            $cb = CB::getCb($operator->component_key);
            $moderatorsKeys = collect($cb->moderators)->pluck('user_key');
            $moderators = Auth::getListNames($moderatorsKeys);
            $cbConfigurations = collect($cb->configurations)->pluck('id')->toArray();
            $configurations = CB::getConfigurations();
            $parameters =  $cb->parameters;
            /** Put the parameters previously added to cb in Cache*/
            $parameterToCache = json_decode(collect($parameters)->keyBy('id')->toJson(),true);
            if(Cache::has('mp_cb_parameter_'.$operatorKey)){
                Cache::pull('mp_cb_parameter_'.$operatorKey);
            }
            Cache::put('mp_cb_parameter_'.$operatorKey, $parameterToCache, 30);

            $parameterType = CB::getParametersTypes();
            $languages = Orchestrator::getLanguageList();
            $data = [];
            $data["operator"] = $operator;
            $data["cb"] = $cb;
            $data["type"] = $operator->operator_type->code;
            $data["configurations"] = $configurations;
            $data["cbConfigurations"] = $cbConfigurations;
            $data["parameters"] = $parameters;
            $data["parameterType"] = $parameterType;
            $data["languages"] = $languages;
            $data["moderators"] = $moderators;
            return view('private.mps.cb.create', $data);

        }catch (Exception $e){
            return redirect()->back()->withErrors([trans('privateMPCbs.create_error') => $e->getMessage()]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param CbsRequest $requestCB
     * @param $operatorKey
     * @return MPCbsController|\Illuminate\Http\RedirectResponse
     */
    public function update(CbsRequest $requestCB, $operatorKey)
    {
        try {
            $mpKey = $requestCB->mp_key;
            $cbKey = $requestCB->cb_key;
            $api = $this->getApiByType($requestCB['operator_type']);
            $parameters = Orchestrator::getParametersTypes();

            // CB details
            $data = [
                'title' => $requestCB["title"],
                'contents' => $requestCB["description"],
                'start_date' => $requestCB["start_date"],
                'end_date' => $requestCB["end_date"]];

            // Configurations
            $configurations = CB::getConfigurations();
            $arrayConfIDs = [];
            foreach ($configurations as $configuration) {
                foreach ($configuration->configurations as $options) {
                    $arrayConfIDs[] = $options->id;

                }
            }
            $arrayConfigurations = [];
            foreach ($requestCB->all() as $key => $value) {
                if (strpos($key, 'configuration_') !== false) {
                    $id = str_replace("configuration_", "", $key);
                    $arrayConfigurations[] = $id;
                    unset($arrayConfIDs[array_search($id, $arrayConfIDs)]);
                }
            }
            $data["configurations"] = $arrayConfigurations;

            // Store parameters
            $parameterTypeId = 0;
            $parametersData = [];
            $parameterItensIds = !empty($requestCB->parameterItensIds) ? explode(",",$requestCB->parameterItensIds) : [];
            foreach($parameterItensIds as $parameterItensId){
                $fileId = null;
                $optionsSelect = !empty($requestCB->input('optionsNew_'.$parameterItensId)) ? $requestCB->input('optionsNew_'.$parameterItensId) : null;
                $parameterTypeSelect = !empty($requestCB->input('paramTypeSelect_'.$parameterItensId)) ? $requestCB->input('paramTypeSelect_'.$parameterItensId) :null;

                if($parameterTypeSelect == 'image_map')
                    $fileId = !empty($requestCB->input('file_id_'.$parameterItensId)) ? $requestCB->input('file_id_'.$parameterItensId) :null;

                $mandatory = !empty($requestCB->input('mandatory_'.$parameterItensId)) ? $requestCB->input('mandatory_'.$parameterItensId) : 0;
                $useFilter = !empty($requestCB->input('use_filter_'.$parameterItensId)) ? $requestCB->input('use_filter_'.$parameterItensId) : 0;
                $visible = !empty($requestCB->input('visible_'.$parameterItensId)) ? $requestCB->input('visible_'.$parameterItensId) : 0;
                $visibleInList = !empty($requestCB->input('visibleInList_'.$parameterItensId)) ? $requestCB->input('visibleInList_'.$parameterItensId) : 0;

                //parameter translations
                $parameterTranslations = [];
                $parameterTranslationsInput = !empty($requestCB->input('parameterName_'.$parameterItensId)) ? $requestCB->input('parameterName_'.$parameterItensId) : [];
                if(count($parameterTranslationsInput) > 0){
                    foreach ($parameterTranslationsInput as $key => $paramName) {
                        if (!empty($paramName)) {
                            $parameterDescriptionTranslations = !empty($requestCB->input('parameterDescription_'.$parameterItensId)) ? $requestCB->input('parameterDescription_'.$parameterItensId) : [];
                            $parameterDescription = !empty($parameterDescriptionTranslations[$key]) ? $parameterDescriptionTranslations[$key] : '';
                            $parameterTranslations[] = [
                                'language_code' => $key,
                                'parameter' => $paramName,
                                'description' => $parameterDescription
                            ];
                        }
                    }
                }
                // Options translations
                $options = [];
                $optionTranslations = [];
                if($optionsSelect != null){
                    foreach ($optionsSelect as $opt){
                        foreach ($opt as $key => $optTrans){
                            if(!empty($optTrans)) {
                                $optionTranslations [] = ['language_code' => $key,'label' => $optTrans];
                            }
                        }
                        $options [] = ['translations' => $optionTranslations];
                        $optionTranslations = [];
                    }
                }

                foreach ($parameters as $parameter){
                    if($parameter->code == $parameterTypeSelect){
                        $parameterTypeId = $parameter->id;
                        break;
                    }
                }

                if(!empty($parameterTranslations)) {
                    $parametersData[] = [
                        'parameter_type_id' => $parameterTypeId,
                        'translations' => $parameterTranslations,
                        'code' => $parameterTypeSelect,
                        'mandatory' => $mandatory,
                        'use_filter' => $useFilter,
                        'visible' => $visible,
                        'visible_in_list' => $visibleInList,
                        'value' => $fileId,
                        'options' => $options
                    ];
                }
            }
            //get old parameters
            $parametersOld = Cache::get('mp_cb_parameter_'.$operatorKey);
            foreach ($parametersOld as $paramOld)
            {
                $options = [];
                foreach ($paramOld['options'] as $opt)
                {
                    if(!empty($opt['new_option'])){
                        $options[] = [
                            'translations' => $opt['translations']
                        ];
                    }else{
                        $options[] = [
                            'id' => $opt['id'],
                            'translations' => $opt['translations']
                        ];
                    }

                }
                $parametersData[] = [
                    'id' => $paramOld['id'],
                    'translations' => $paramOld['translations'],
                    'mandatory' => $paramOld['mandatory'] ?? 0,
                    'use_filter' => $paramOld['use_filter'] ?? 0,
                    'visible' => $paramOld['visible'] ?? 0,
                    'visible_in_list' => $paramOld['visible_in_list'] ?? 0,
                    'value' => null,
                    'options' => $options
                ];
            }
//            Cache::pull('mp_cb_parameter_'.$operatorKey);


            $data["parameters"] = $parametersData;

            // Moderators
            $moderators = [];
            $keys = !empty($requestCB->moderators) ? $requestCB->moderators : [];
            foreach($keys as $key){
                $moderators[] = ['user_key' => $key,'type_id' => 1];
            }
            // CB::setCbModerators( $cb->cb_key,$moderators);
            $data["moderators"] = $moderators;
            $cb = CB::updateStepperCb($cbKey,$data);

            Session::flash('message', trans('privateMPCbs.update_ok'));
            return redirect()->action('MPsController@showConfigurations', ['mp_key' => $mpKey]);
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors([trans('privateMPCbs.update_error') => $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $operatorKey
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($operatorKey)
    {
        try {
            $operator = MP::getOperator($operatorKey);
            CB::deleteCb($operator->component_key);
            MP::updateOperator($operatorKey,0);

            Session::flash('message', trans('privateMPCbs.delete_ok'));
            return action('MPsController@showConfigurations',$operator->mp->mp_key);

        } catch (Exception $e) {
            return redirect()->back()->withErrors([trans('privateMPCbs.delete_error') => $e->getMessage()])->getTargetUrl();
        }
    }


    /** Show modal delete confirmation
     *
     * @param $operatorKey
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function delete($operatorKey){
        $data = array();
        $data['action'] = action("MPCbsController@destroy", ['operatorKey'=>$operatorKey]);
        $data['title'] = trans('privateMPCbs.delete');
        $data['msg'] = trans('privateMPCbs.are_you_sure_you_want_to_delete').' ?';
        $data['btn_ok'] = trans('privateMPCbs.delete');
        $data['btn_ko'] = trans('privateMPCbs.cancel');

        return view("_layouts.deleteModal", $data);
    }


    /**
     * Add a new modal parameter.
     *
     * @param Request $request
     * @return MPCbsController|\Illuminate\Http\RedirectResponse
     */
    public function addModalParameter(Request $request)
    {
        try {
            if(!empty( $request->get('parameterCounter'))){
                $parameterCounter = $request->get('parameterCounter');
            } else{
                $parameterCounter = 0;
            }

            $data = [];
            $data["parameterType"] = CB::getParametersTypes();
            $data["parameterCounter"] = $parameterCounter;

            return view('private.mps.cb.wizard.modalParameter', $data);
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["cbs.create" => $e->getMessage()]);
        }
    }


    /**
     * Add a new parameter.
     *
     * @param Request $request
     * @return MPCbsController|\Illuminate\Http\RedirectResponse
     */
    public function addParameter(Request $request)
    {
        try {
            $languages = Orchestrator::getLanguageList();

            if(!empty( $request->get('parameterCounter'))){
                $parameterCounter = $request->get('parameterCounter');
            } else{
                $parameterCounter = 0;
            }

            $data = [];
            $data["parameterType"] = CB::getParametersTypes();
            $data["parameterCounter"] = $parameterCounter;
            $data["languages"] = $languages;

            return view('private.mps.cb.wizard.parameter', $data);
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["cbs.create" => $e->getMessage()]);
        }
    }


    /**
     * Add a new parameter template selection.
     *
     * @param Request $request
     * @return MPCbsController|\Illuminate\Http\RedirectResponse
     */
    public function addParameterTemplateSelection(Request $request)
    {
        try {
            if(!empty( $request->get('parameterCounter'))){
                $parameterCounter = $request->get('parameterCounter');
            } else{
                $parameterCounter = 0;
            }

            // Parameter Templates
            $parameterTemplatesKeys = Orchestrator::getParametersTemplatesKeys();
            $array = [];
            foreach($parameterTemplatesKeys as $tmp){
                $array[] = $tmp->parameter_template_key;
            }
            $parameterTemplates  = CB::getParametersTemplates($array);

            $data = [];
            $data["parameterType"] = CB::getParametersTypes();
            $data["parameterCounter"] = $parameterCounter;
            $data["parameterTemplates"] = $parameterTemplates;

            return view('private.mps.cb.wizard.parameterTemplateSelection', $data);
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["cbs.create" => $e->getMessage()]);
        }
    }



    /**
     * Add a new parameter template.
     * @param Request $request
     * @return MPCbsController|\Illuminate\Http\RedirectResponse
     */
    public function addParameterTemplate(Request $request)
    {
        try {
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
            $data["parameterCounter"] = !empty( $request->get('parameterCounter')) ? $request->get('parameterCounter') : 0 ;;
            $data["template"] = !empty($request->get('template'))? $request->get('template') : null;
            $data["parameterType"] = $parameterType;
            $data["parameterTemplateChoosed"] = $parameterTemplateChoosed;

            return view('private.mps.cb.wizard.parameter', $data);
        } catch (Exception $e) {
            return redirect()->back()->withErrors([trans("privateMPCbs.all_add_parameter_template") => $e->getMessage()]);
        }
    }


    /**
     * Get all Users
     * @param $componentKey
     * @return MPCbsController|\Illuminate\Http\RedirectResponse
     */
    public function allUsers(Request  $request)
    {

        try {
            $componentKey = $request->component_key;
            $moderatorsKeys = array();
            if (!empty($componentKey)){
                $cb = CB::getCb($componentKey);
                $moderatorsKeys = collect($cb->moderators)->pluck('user_key')->toArray();
            }

            $usersList = Orchestrator::getAllUsers();

            $data = [];

            $usersKeys = collect($usersList)->pluck('user_key')->toArray();

            if (count($usersKeys) > 0) {
                $data = Auth::getUserNames($usersKeys);
            }
            $collection = Collection::make($data);

            // in case of json
            return Datatables::of($collection)
                ->addColumn('moderadorCheckbox', function ($collection) use($moderatorsKeys) {
                    // return "<input name='moderators[]' value='".$collection->user_key."' type='checkbox'  />";
                    // $checked = "checked='true'";
                    if (!empty($collection->photo_id)) {
                        $userImage = URL::action('FilesController@download', ['id' => $collection->photo_id, 'code' => $collection->photo_code, 1]);
                    } else {
                        $userImage = asset('images/icon-user-default-160x160.png');
                    }
                    return "<div class='oneSwitch'><input  onclick=\"toggleModeratorItem(this,'" . $collection->name . "','" . $userImage . "')\" ".(in_array($collection->user_key,$moderatorsKeys) ? 'checked' : null)." type='checkbox' name='moderators[]' value='" . $collection->user_key . "' class='oneSwitch-checkbox' id='moderatorCheckbox_" . $collection->user_key . "'><label class='oneSwitch-label' for='moderatorCheckbox_" . $collection->user_key . "'><span class='oneSwitch-inner'></span><span class='oneSwitch-switch'></span></label></div>";
                })
                ->addColumn('name', function ($collection) {
                    return $collection->name;
                })
                ->addColumn('action', function () {
                    return "";
                })
                ->make(true);
        }catch (Exception $e) {
            return redirect()->back()->withErrors([trans("privateMPCbs.all_users_error") => $e->getMessage()]);
        }
    }


    /** get cb type api
     * @param $type
     * @return bool|mixed
     * @throws Exception
     */
    private function getApiByType($type){
        $api = isset($this->cbType[$type]) ? $this->cbType[$type] : false;
        if ($api == false) {
            throw new Exception( "Error get cb type" );
        }
        return $api;
    }


    /** Get and show parameter details
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|string
     */
    public function getParameter(Request $request)
    {
        try {
            $operatorKey = $request->operator_key;
            $actionType = $request->action_type;
            $show = ($actionType == 'show' ? true : false);
            $parameterId = $request->parameter_id;
            if(is_null($parameterId)){
                return 'false';
            }
            $languages = Orchestrator::getLanguageList();
            $parameterTypesList = CB::getParametersTypes();
            $parameterTypes = collect($parameterTypesList)->pluck('name','code')->toArray();
            /** Parameter to update need to be in Cache */
            if(!Cache::has('mp_cb_parameter_'.$operatorKey)){
                return 'false';
            }
            $parameter = Cache::get('mp_cb_parameter_'.$operatorKey)[$parameterId] ?? null;
            if(empty($parameter)){
                return 'false';
            }
            $translations = $parameter['translations'];

            $data = [];
            $data['parameter']= $parameter;
            $data['parameterTypes']= $parameterTypes;
            $data['parameterType']= $parameterTypesList;
            $data['parameterCounter']= $parameterId;
            $data['languages']= $languages;
            $data['translations']= $translations;
            $data['show']= $show;
            $data['operatorKey']= $operatorKey;

            return view('private.mps.cb.wizard.parameter', $data);
        } catch (Exception $e) {
            return 'false';
        }

    }

    /** Remove Parameter from cache
     * @param Request $request
     * @return string
     */
    public function removeParameterCache(Request $request)
    {
        try {
            $operatorKey = $request->operator_key;
            $parameterId = $request->parameter_id;
            if(empty($operatorKey) || empty($parameterId))
            {
                return 'false';
            }
            if(Cache::has('mp_cb_parameter_'.$operatorKey)){
                $parameters = Cache::get('mp_cb_parameter_'.$operatorKey);
                unset($parameters[$parameterId]);
                Cache::pull('mp_cb_parameter_'.$operatorKey);
                Cache::put('mp_cb_parameter_'.$operatorKey,$parameters, 30);
            }
            return 'true';
        } catch (Exception $e) {
            return 'false';
        }
    }


    /** Update Parameter in Cache
     * @param Request $request
     * @return string
     */
    public function updateParameter(Request $request)
    {
        try {
            $operatorKey = $request->input('operator_key');
            $parameterId = $request->input('parameter_id');
            if(!Cache::has('mp_cb_parameter_'.$operatorKey)){
                return 'false';
            }
            $parameters = Cache::get('mp_cb_parameter_'.$operatorKey) ?? null;
            $parameter = $parameters[$parameterId];
            if(empty($parameter)){
                return 'false';
            }

            $parameter['mandatory'] =  $request->input('mandatory_'.$parameterId) ?? 0;
            $parameter['use_filter'] = $request->input('use_filter_'.$parameterId) ?? 0;
            $parameter['visible'] = $request->input('visible_'.$parameterId) ?? 0;
            $parameter['visible_in_list'] = $request->input('visibleInList_'.$parameterId) ?? 0;

            $parameterTranslationsInput = $request->input('parameterName_'.$parameterId) ?? [];
            foreach ($parameterTranslationsInput as $key => $paramName) {
                if (!empty($paramName)) {
                    $parameterDescriptionTranslations = $request->input('parameterDescription_'.$parameterId) ?? [];
                    $parameterDescription = $parameterDescriptionTranslations[$key] ?? '';
                    if(empty($parameter['translations'][$key])){
                        $parameter['translations'][$key] = [];
                        $parameter['translations'][$key]['language_code'] = $key;
                    }
                    $parameter['translations'][$key]['parameter'] = $paramName ?? '';
                    $parameter['translations'][$key]['description'] = $parameterDescription ?? '';
                }elseif (!empty($parameter['translations'][$key])){
                    $parameterDescriptionTranslations = $request->input('parameterDescription_'.$parameterId) ?? [];
                    $parameterDescription = $parameterDescriptionTranslations[$key] ?? '';
                    $parameter['translations'][$key]['parameter'] = $paramName ?? '';
                    $parameter['translations'][$key]['description'] = $parameterDescription ?? '';

                }
            }
            // Options translations
            $options = [];
            $optionsAdded = $request->input('optionsNew_'.$parameterId) ?? [];
            $optionsOld = $request->input('optionsOld_'.$parameterId) ?? [];
            foreach ($optionsOld as $key => $opt){
                $optionTranslations = [];
                foreach ($opt as $langKey => $optTrans){
                    if(!empty($optTrans)) {
                        $optionTranslations [$langKey] = [];
                        $optionTranslations [$langKey] = ['language_code' => $langKey,'label' => $optTrans];
                    }
                }
                $options[$key] = ['id' => $key, 'translations' => $optionTranslations];
            }
            foreach ($optionsAdded as  $opt){
                $optionTranslations = [];
                foreach ($opt as $langKey => $optTrans){
                    if(!empty($optTrans)) {
                        $optionTranslations [$langKey] = ['language_code' => $langKey,'label' => $optTrans];
                    }
                }
                $options[]['translations'] = $optionTranslations;
                $arrayKeys = array_keys($options);
                $lastKey = end($arrayKeys);
                $options[$lastKey]['id'] = $lastKey;
                $options[$lastKey]['new_option'] = true;
            }

            $parameter['options'] = $options;
            $parameters[$parameterId] = $parameter;

            Cache::pull('mp_cb_parameter_'.$operatorKey);
            Cache::put('mp_cb_parameter_'.$operatorKey,$parameters, 30);

            return 'true';
        } catch (Exception $e) {
            return 'false';
        }
    }

}
