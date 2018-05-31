<?php

namespace App\Http\Controllers;

use App\ComModules\Orchestrator;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use App\One\One;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Collection;
use App\Http\Requests\ParameterUserTypeRequest;
use Datatables;
use Session;
use View;
use Breadcrumbs;

class ParameterUserTypesController extends Controller
{
    public function __construct()
    {
        View::share('private.parameterUserTypes', trans('parameterUserType.parameterUserType'));


    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $entityKey = One::getEntityKey();

        if(!empty($entityKey)){
            $title = trans('parameterUserType.parameterUserType');
            $sidebar = 'entity';
            $active = 'parameterUserTypes';

            return view('private.parameterUserTypes.index',compact('entityKey', 'sidebar', 'active','title'));
        }else{
            $title = trans('parameterUserType.parameterUserType');

            return view('private.parameterUserTypes.index',compact('title'));
        }
    }

    /**
     * Create a new resource.
     *
     * @return Response
     */
    public function create()
    {
        try {
            // Getting languages for translations
            $languages = Orchestrator::getLanguageList();

            // Gettting Parameters Types
            $parameterTypesData = Orchestrator::getParametersTypes();

            $types = [];
            foreach($parameterTypesData as $type){
                $types[$type->code] = $type->name;
            }

            return view('private.parameterUserTypes.parameterUserType', compact('types','languages'));
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["parameterUserTypes.edit" => $e->getMessage()]);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return $this|View
     */
    public function store(ParameterUserTypeRequest $request)
    {
        try {
            $parameterTypeCode = (isset($request->parameter_type_code) ? $request->parameter_type_code : null);
            $parameterCode = (isset($request->code) ? $request->code : null);
            $mandatory = (isset($request->parameterMandatory) ? $request->parameterMandatory : false);
            $unique = (isset($request->parameterUnique) ? $request->parameterUnique : false);
            $anonymizable = (isset($request->anonymizable) ? $request->anonymizable : false);
            $minimumUsers = (isset($request->minimum_users) ? $request->minimum_users : 0);
            $voteInPerson = (isset($request->vote_in_person) ? $request->voteInPerson : 0);
            $externalValidation = (isset($request->externalValidation) ? $request->externalValidation : 0);
            $languages = Orchestrator::getLanguageList();
            // Translations
            $contentTranslation = [];
            foreach($languages as $language){
                if($request->input("required_name_" . $language->code) && ($request->input("required_name_" . $language->code) != '') || $request->input("required_name_" . $language->code) && ($request->input("required_name_" . $language->code) != '')) {
                    $contentTranslation[] = [
                        'language_id' => $language->id,
                        'language_code' => $language->code,
                        'name' => ($language->default??true) == true ? $request->input("required_name_" . $language->code) : $request->input("required_name_" . $language->code),
                        'description' => $request->input("description_" . $language->code)
                    ];
                }
            }

            $optionsWithTranslation = null;
            switch ($parameterTypeCode) {
                case 'category':
                case 'budget':
                case 'radio_buttons':
                case 'check_box':
                case 'dropdown':
                case 'gender':
                    $optionsWithTranslation = [];
                    if(isset($request->optionsNew)) {
                        foreach ($request->optionsNew as $option){
                            $optionTranslation = [];
                            foreach ($languages as $language) {
                                if(isset($option[$language->code]) && $option[$language->code] != '') {
                                    $optionTranslation[] = [
                                        'language_code' => $language->code,
                                        'name' => isset($option[$language->code]) ? $option[$language->code] : null
                                    ];
                                }
                            }
                            $optionsWithTranslation[]['translations'] = $optionTranslation;
                        }
                    }
                    break;
            }

            $parameterUserType = Orchestrator::createParameterUserType($parameterTypeCode,$parameterCode,$mandatory,$unique,$anonymizable,$minimumUsers,$voteInPerson,$externalValidation,$contentTranslation,$optionsWithTranslation);
            
            if(!empty($request->external_validation_text) && !empty(One::getEntityKey())){

                $entityKey = One::getEntityKey();
                $parameterUserTypeId = $parameterUserType->id;
                $type = "vat_numbers";
                $externalValidationNumbers = $request->external_validation_text;
                $externalValidationNumbers = preg_split("/[\s,;]/", $externalValidationNumbers, -1, PREG_SPLIT_NO_EMPTY);
                $importedData = [];

                foreach($externalValidationNumbers as $number){
                    $number = trim($number);
                    $importedData[]['vat_number'] = $number;
                }
                
                Orchestrator::importRegistrationFields($importedData,$type,$entityKey,$parameterUserTypeId);
            }

            Session::flash('message', trans('parameterUserType.store_ok'));
            return redirect()->action('ParameterUserTypesController@show', $parameterUserType->parameter_user_type_key);

        } catch (Exception $e) {
            return redirect()->back()->withErrors(["parameterUserType.store" => $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $parameterUserTypeKey
     * @return \Illuminate\Http\RedirectResponse|View
     */
    public function show($parameterUserTypeKey)
    {
        try {
            // Getting languages for translations
            $languages = Orchestrator::getLanguageList();
            $parameterUserType = Orchestrator::getEditParameterUserType($parameterUserTypeKey);

            // Translations
            $translations = collect($parameterUserType->translations)->keyBy('language_code')->toArray();

            //Options translations
            $optionsTranslations = [];
            foreach ($parameterUserType->parameter_user_options as $option){
                $transOpt = collect($option->translations)->keyBy('language_code')->toArray();
                $optionsTranslations [$option->parameter_user_option_key] = $transOpt;
            }

            $parameterType = Orchestrator::getParametersType($parameterUserType->parameter_type_id);
            $typeName = $parameterType->name;
            
            $numbersVal= "";

            if($parameterUserType->external_validation == 1 && !empty(One::getEntityKey())){
                $entityKey = One::getEntityKey();
                $numbers = Orchestrator::getVatNumbers($entityKey,$parameterUserType->id);
                $numberVal = [];
                foreach($numbers as $number){
                    $numberVal[] = $number->vat_number;
                }

                $numbersVal = implode(';',$numberVal);
            }

            return view('private.parameterUserTypes.parameterUserType',
                compact('parameterUserType','typeName','languages','translations','optionsTranslations','numbersVal'));
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["parameterUserTypes.show" => $e->getMessage()]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param $parameterUserTypeKey
     * @return View
     */
    public function edit($parameterUserTypeKey)
    {
        try {
            // Getting languages for translations
            $languages = Orchestrator::getLanguageList();

            // Get Parameter User Type
            $parameterUserType = Orchestrator::getEditParameterUserType($parameterUserTypeKey);

            // Translations
            $translations = collect($parameterUserType->translations)->keyBy('language_code')->toArray();

            //Options translations
            $optionsTranslations = [];
            foreach ($parameterUserType->parameter_user_options as $option){
                $transOpt = collect($option->translations)->keyBy('language_code')->toArray();
                $optionsTranslations [$option->parameter_user_option_key] = $transOpt;
            }

            // Get Parameters Types
            $parameterTypesData = Orchestrator::getParametersTypes();

            $selectedType = null;
            $types = [];
            foreach($parameterTypesData as $type){
                $types[$type->code] = $type->name;
                if($parameterUserType->parameter_type_id == $type->id){
                    $selectedType = $type->code;
                }
            }

            $numbersVal= "";

            if($parameterUserType->external_validation == 1 && !empty(One::getEntityKey())){
                $entityKey = One::getEntityKey();
                $numbers = Orchestrator::getVatNumbers($entityKey,$parameterUserType->id);
                $numberVal = [];
                foreach($numbers as $number){
                    $numberVal[] = $number->vat_number;
                }

                $numbersVal = implode(';',$numberVal);
            }
			

            return view('private.parameterUserTypes.parameterUserType',
                compact('parameterUserType', 'types', 'languages','translations','selectedType','optionsTranslations','numbersVal'));
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["parameterUserTypes.edit" => $e->getMessage()]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param $parameterUserTypeKey
     * @return $this|View
     */
    public function update(ParameterUserTypeRequest $request, $parameterUserTypeKey)
    {
        try {
            $parameterTypeCode = (isset($request->parameter_type_code) ? $request->parameter_type_code : null);
            $parameterCode = (isset($request->code) ? $request->code : null);
            $mandatory = (isset($request->parameterMandatory) ? $request->parameterMandatory : false);
            $unique = (isset($request->parameterUnique) ? $request->parameterUnique : false);
            $anonymizable = (isset($request->anonymizable) ? $request->anonymizable : false);
            $minimumUsers = (isset($request->minimum_users) ? $request->minimum_users : 0);
            $voteInPerson = (isset($request->voteInPerson) ? $request->voteInPerson : 0);
            $externalValidation = (isset($request->externalValidation) ? $request->externalValidation : 0);
            $languages = Orchestrator::getLanguageList();
            // Translations
            $contentTranslation = [];
            foreach($languages as $language){
                if($request->input("required_name_" . $language->code) && ($request->input("required_name_" . $language->code) != '') || $request->input("required_name_" . $language->code) && ($request->input("required_name_" . $language->code) != '')) {
                    $contentTranslation[] = [
                        'language_id' => $language->id,
                        'language_code' => $language->code,
                        'name' => ($language->default??true) == true ? $request->input("required_name_" . $language->code) : $request->input("required_name_" . $language->code),
                        'description' => $request->input("description_" . $language->code)
                    ];
                }
            }

            $optionsWithTranslation = null;
            switch ($parameterTypeCode) {
                case 'category':
                case 'budget':
                case 'radio_buttons':
                case 'check_box':
                case 'dropdown':
                    $optionsWithTranslation = [];
                    if(isset($request->optionsNew)) {
                        foreach ($request->optionsNew as $option){
                            $optionTranslation = [];
                            foreach ($languages as $language) {
                                if(isset($option[$language->code]) && $option[$language->code] != '') {
                                    $optionTranslation[] = [
                                        'language_code' => $language->code,
                                        'name' => isset($option[$language->code]) ? $option[$language->code] : null
                                    ];
                                }
                            }
                            $optionsWithTranslation []['translations'] = $optionTranslation;
                        }
                    }
                    if(isset($request->optionsSelect)){
                        foreach ($request->optionsSelect as $key => $option){
                            $optionTranslation = [];
                            foreach ($languages as $language) {
                                if(isset($option[$language->code]) && $option[$language->code] != '') {
                                    $optionTranslation[] = [
                                        'language_code' => $language->code,
                                        'name' => isset($option[$language->code]) ? $option[$language->code] : null
                                    ];
                                }
                            }
                            $optionsWithTranslation [] = ['parameter_user_option_key' => $key,'translations' => $optionTranslation];
                        }
                    }
                    break;
            }

            $parameterUserType = Orchestrator::updateParameterUserType($parameterUserTypeKey,$parameterCode,$parameterTypeCode,$mandatory,$unique,$anonymizable,$minimumUsers,$voteInPerson,$externalValidation,$contentTranslation,$optionsWithTranslation);
            
            if(!empty($request->external_validation_text) && !empty(One::getEntityKey())){

                $entityKey = One::getEntityKey();
                $parameterUserTypeId = $parameterUserType->id;
                $type = "vat_numbers";
                $externalValidationNumbers = $request->external_validation_text;
                $externalValidationNumbers = preg_split("/[\s,;]/", $externalValidationNumbers, -1, PREG_SPLIT_NO_EMPTY);
                $importedData = [];

                foreach($externalValidationNumbers as $number){
                    $number = trim($number);
                    $importedData[]['vat_number'] = $number;
                }
                
                Orchestrator::deleteAllRegistrationValues($entityKey,$parameterUserTypeId);
                Orchestrator::importRegistrationFields($importedData,$type,$entityKey,$parameterUserTypeId);
            }
            
            Session::flash('message', trans('privateParameterUserTypes.update_ok'));
            return redirect()->action('ParameterUserTypesController@show', $parameterUserType->parameter_user_type_key);
        }
        catch(Exception $e) {
            //TODO: save inputs
            return redirect()->back()->withErrors(["roles.update" => $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  $parameterUserTypeKey
     * @return Response
     */
    public function destroy($parameterUserTypeKey)
    {
        try {
            $entityKey = One::getEntityKey();
            $parameterUserTypeId = Orchestrator::getParameterUserType($parameterUserTypeKey);
            $parameterUserTypeId = $parameterUserTypeId->id;
            Orchestrator::deleteAllRegistrationValues($entityKey,$parameterUserTypeId);
            Orchestrator::deleteParameterUserType($parameterUserTypeKey);
            Session::flash('message', trans('privateParameterUserTypes.delete_ok'));
            return action('ParameterUserTypesController@index');
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["parameterUserTypes.destroy" => $e->getMessage()]);
        }
    }

    /**
     * Opens a modal to enable the Delete Confirmation Dialog.
     *
     * @param string $parameterUserTypeKey
     * @return view
     */
    public function delete($parameterUserTypeKey){
        $data = array();
        $data['action'] = action("ParameterUserTypesController@destroy", ['parameterUserTypeKey' => $parameterUserTypeKey]);
        $data['title'] = "DELETE";
        $data['msg'] = "Are you sure you want to delete this user parameter?";
        $data['btn_ok'] = "Delete";
        $data['btn_ko'] = "Cancel";

        return view("_layouts.deleteModal", $data);
    }

    /**
     * Get a datatable of ParameterUserTypes List.
     *
     * @return Datatable of Collection made
     */
    public function getIndexTable(Request $request)
    {
        // Getting list
        $data = Orchestrator::getParameterUserTypes($request);

        // in case of json
        if(Session::get('user_role') == 'admin'){

            $collection = collect($data->parametersUserType);
            
            $recordsTotal = $data->recordsTotal;
            $recordsFiltered = $data->recordsFiltered;

        }
        else
            $collection = Collection::make([]);

        $delete = Session::get('user_role') == 'admin';

        return Datatables::of($collection)
            ->editColumn('id', function ($collection) {
                return $collection->id;
            })
            ->editColumn('code', function ($collection) {
                if(empty($collection->code))
                    return trans("parameterUserType.no_code_defined");
                else
                    return $collection->code;
            })
            ->editColumn('name', function ($collection) {
                return "<a href='".action('ParameterUserTypesController@show', $collection->parameter_user_type_key)."'>".$collection->name."</a>";
            })
            ->addColumn('action', function ($collection) use($delete){
                if (!empty(One::getEntityKey())) {
                    if($delete == true)
                        return ONE::actionButtons($collection->parameter_user_type_key, ['form' => 'parameterUserTypes', 'show' => 'ParameterUserTypesController@show', 'delete' => 'ParameterUserTypesController@delete']);
                    else
                        return ONE::actionButtons($collection->parameter_user_type_key, ['form' => 'parameterUserTypes', 'delete' => 'ParameterUserTypesController@delete']);
                }
            })
            ->rawColumns(['name','action'])
            ->with('filtered', $recordsFiltered ?? 0)
            ->skipPaging()
            ->setTotalRecords($recordsTotal ?? 0)
            ->make(true);
    }
}
