<?php

namespace App\Http\Controllers;


use App\ComModules\CB;
use App\ComModules\MP;
use App\Http\Requests\MPRequest;
use App\ComModules\Orchestrator;
use App\One\One;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Datatables;
use Session;
use View;


class MPsController extends Controller
{
    public function __construct()
    {
    }

    /**
     * Display a listing of the resource.
     *
     * @return View
     */
    public function index()
    {
        //for bold in sidebar
        $name_view = "mp";

        return view('private.mps.index', compact('name_view'));

    }


    /** Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\RedirectResponse|View
     */
    public function create()
    {
        try {
            //for bold in sidebar
            $name_view = "mp";
            $languages = Orchestrator::getLanguageList();
            $operatorTypes = MP::getOperatorTypes();
            return view('private.mps.mp',compact('languages','operatorTypes', 'name_view'));
        }
        catch (Exception $e){
            return redirect()->back()->withErrors([ trans('privateMPs.create_error') => $e->getMessage()]);
        }

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param MPRequest $request
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function store(MPRequest $request)
    {
        try {
            $languages = Orchestrator::getLanguageList();
            // Translations
            $translations = [];
            foreach($languages as $language){
                if($request->input("name_" . $language->code) && ($request->input("name_" . $language->code) != '') || $request->input("required_name_" . $language->code) && ($request->input("required_name_" . $language->code) != '')) {
                    $translations[] = [
                        'language_code' => $language->code,
                        'name' => $language->default == true ? $request->input("required_name_" . $language->code) : $request->input("name_" . $language->code),
                        'description' => $request->input("description_" . $language->code)
                    ];
                }
            }
            $flowchart = json_decode($request->flowchart_data);
            $startOperatorKey = $this->verifyStartEndFlowChart($flowchart->operators);
            $parentOperatorKeys [] = $startOperatorKey;
            $parentsArray = $this->verifyFlowChart($flowchart->operators,$flowchart->links,$parentOperatorKeys);
            $mp = MP::setNewMP($request->flowchart_data,$translations,$parentsArray);
            Session::flash('message', trans('privateMPs.store_ok'));
            return redirect()->action('MPsController@show', $mp->mp_key);
        }
        catch (Exception $e){
            return redirect()->back()->withErrors([ trans('privateMPs.store_error') => $e->getMessage()]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param $mpKey
     * @return \Illuminate\Http\RedirectResponse|View
     */
    public function edit($mpKey)
    {
        try {
            //for bold in sidebar
            $name_view = "mp";
            $languages = Orchestrator::getLanguageList();
            $mp = MP::getMp($mpKey);
            $translations = collect($mp->translations)->keyBy('language_code')->toArray();
            $operatorTypes = MP::getOperatorTypes();

            $sidebar = 'mp_configurations';
            $active = 'details';

            Session::put('sidebarArguments', ['mpKey' => $mpKey, 'activeFirstMenu' => 'details']);

            return view('private.mps.mp', compact('mp','languages','translations','operatorTypes', 'name_view', 'sidebar', 'active'));
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors([ trans('privateMPs.edit_error') => $e->getMessage()]);
        }

    }

    /**
     * Display the specified resource.
     *
     * @param $mpKey
     * @return \Illuminate\Http\RedirectResponse|View
     */
    public function show($mpKey)
    {
        try {
            //for bold in sidebar
            $name_view = "mp";
            $languages = Orchestrator::getLanguageList();
            $mp = MP::getMp($mpKey);
            $translations = collect($mp->translations)->keyBy('language_code')->toArray();

            $sidebar = 'mp_configurations';
            $active = 'details';

            Session::put('sidebarArguments', ['mpKey' => $mpKey, 'activeFirstMenu' => 'details']);

            return view('private.mps.mp', compact('mp','languages','translations', 'name_view', 'sidebar', 'active'));
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors([ trans('privateMPs.show_mp_error') => $e->getMessage()]);
        }
    }


    /** Show mp configurations
     *
     * @param $mpKey
     * @return \Illuminate\Http\RedirectResponse|View
     */
    public function showConfigurations($mpKey){
        try {
            $mp = MP::getMp($mpKey);
            $mpOperators = $mp->operators ?? [];

            $sidebar = 'mp_configurations';
            $active = 'configurations';

            Session::put('sidebarArguments', ['mpKey' => $mpKey, 'activeFirstMenu' => 'configurations']);

            return view('private.mps.configurations', compact('mp','mpOperators', 'sidebar', 'active'));
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors([ trans('privateMPs.show_configurations_error') => $e->getMessage()]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param MPRequest $request
     * @param $mpKey
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(MPRequest $request, $mpKey)
    {
        try {
            $languages = Orchestrator::getLanguageList();
            $translations = [];
            foreach($languages as $language){
                if($request->input("name_" . $language->code) && ($request->input("name_" . $language->code) != '') || $request->input("required_name_" . $language->code) && ($request->input("required_name_" . $language->code) != '')) {
                    $translations[] = [
                        'language_code' => $language->code,
                        'name' => $language->default == true ? $request->input("required_name_" . $language->code) : $request->input("name_" . $language->code),
                        'description' => $request->input("description_" . $language->code)
                    ];
                }
            }
            $flowchart = json_decode($request->flowchart_data);
            $startOperatorKey = $this->verifyStartEndFlowChart($flowchart->operators);
            $parentOperatorKeys [] = $startOperatorKey;
            $parentsArray = $this->verifyFlowChart($flowchart->operators,$flowchart->links,$parentOperatorKeys);
            $mp = MP::updateMP($mpKey,$request->flowchart_data,$translations,$parentsArray);
            Session::flash('message', trans('privateMPs.update_ok'));
            return redirect()->action('MPsController@show', $mp->mp_key);
        }
        catch (Exception $e){
            return redirect()->back()->withErrors([ trans('privateMPs.update_error') => $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $mpKey
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($mpKey)
    {
        try {
            MP::deleteMP($mpKey);
            Session::flash('message', trans('privateMPs.delete_ok'));
            return action('MPsController@index');
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors([ trans('privateMPs.delete_error') => $e->getMessage()])->getTargetUrl();
        }
    }


    /** Show modal delete confirmation
     *
     * @param $mpKey
     * @return View
     */
    public function delete($mpKey)
    {
        $data = array();
        $data['action'] = action("MPsController@destroy", $mpKey);
        $data['title'] = trans('privateMPs.delete');
        $data['msg'] = trans('privateMPs.are_you_sure_you_want_to_delete');
        $data['btn_ok'] = trans('privateMPs.delete');
        $data['btn_ko'] = trans('privateMPs.cancel');

        return view("_layouts.deleteModal", $data);
    }


    /** Display a listing of the resource.
     *
     * @return mixed
     */
    public function getIndexTable()
    {
        $mps = MP::getMps();
        // in case of json
        $collection = Collection::make($mps);

        $edit = true;
        $delete = true;

        return Datatables::of($collection)
            ->editColumn('name', function ($collection) {
                return "<a href='".action('MPsController@show', $collection->mp_key)."'>".$collection->name."</a>";
            })
            ->addColumn('action', function ($collection) use($edit, $delete) {
                if($edit == true and $delete == true)
                    return ONE::actionButtons($collection->mp_key, ['form' => 'mp','edit' => 'MPsController@edit', 'delete' => 'MPsController@delete']);
                if($edit == true and $delete == false)
                    return ONE::actionButtons($collection->mp_key, ['form' => 'mp','edit' => 'MPsController@edit']);
                if($edit == false and $delete == true)
                    return ONE::actionButtons($collection->mp_key, ['form' => 'mp','delete' => 'MPsController@delete']);
                else
                    return null;
            })
            ->rawColumns(['name','action'])
            ->make(true);
    }


    /** Verify flow chart by rules
     * @param $flowchartOperators
     * @param $flowchartLinks
     * @param $parentOperatorKeys
     * @param array $arrayKeysVisited
     * @param array $parentsArray
     * @return array
     * @throws Exception
     */
    private function verifyFlowChart($flowchartOperators, $flowchartLinks, $parentOperatorKeys, $arrayKeysVisited = [], $parentsArray = [])
    {
        $parentsToSearch = [];
        foreach ($parentOperatorKeys as $parentKey)
        {
            if( !property_exists($flowchartOperators,$parentKey)){
                throw new Exception(trans("privateMPs.error_operator_not_exists"));
            }

            if(!array_key_exists($parentKey,$parentsArray)) {
                $parentsArray[$parentKey]['type'] = $flowchartOperators->{$parentKey}->type;
                $parentsArray[$parentKey]['operator_key'] = $flowchartOperators->{$parentKey}->operator_key;
                $parentsArray[$parentKey]['children'] = [];
                $parentsArray[$parentKey]['parents'] = [];
            }
            foreach ($flowchartLinks as $key => $link)
            {
                if(!in_array($key,$arrayKeysVisited) && ($parentKey == $link->fromOperator)){

                    if(array_key_exists($link->toOperator,$parentsArray) && in_array($parentKey,$parentsArray[$link->toOperator])){
                        throw new Exception(trans("privateMPs.error_operator_cannot_be_child_of_his_child"));
                    }
                    $parentsArray[$parentKey]['type'] = $flowchartOperators->{$parentKey}->type;
                    $parentsArray[$parentKey]['operator_key'] = $flowchartOperators->{$parentKey}->operator_key;
                    $parentsArray[$parentKey]['children'][] = $link->toOperator;
                    if(array_key_exists($link->toOperator,$parentsArray) && array_key_exists('parents',$parentsArray[$link->toOperator])){
                        $parentsArray[$link->toOperator]['parents'][] = $parentKey;
                    }
                    if(!in_array($link->toOperator,$parentsToSearch) && empty($parentsArray[$link->toOperator])) {
                        $parentsToSearch [] = $link->toOperator;
                        $parentsArray[$link->toOperator]['type'] = $flowchartOperators->{$link->toOperator}->type;
                        $parentsArray[$link->toOperator]['operator_key'] = $flowchartOperators->{$link->toOperator}->operator_key;
                        $parentsArray[$link->toOperator]['parents'] = [];
                        $parentsArray[$link->toOperator]['parents'][] = $parentKey;
                        $parentsArray[$link->toOperator]['children'] = [];
                    }
                    $arrayKeysVisited [] = $key;
                }
            }
            //verify if a vote have more than two
            if(empty($parentsArray[$parentKey]['type']) || ($parentsArray[$parentKey]['type'] == 'vote' && count($parentsArray[$parentKey]['parents']) > 1)){
                throw new Exception(trans("privateMPs.error_vote_operator_can_not_have_two_parents"));
            }
            if($parentsArray[$parentKey]['type'] == 'vote'){
                $parent = $this->verifyVoteParent($parentsArray,$parentsArray[$parentKey]['parents']);
                $parentsArray[$parentKey]['pad_parent'] = $parent;
            }
            if(empty($parentsArray[$parentKey]['children']) && ($flowchartOperators->{$parentKey}->type != 'end'))
            {
                throw new Exception(trans("privateMPs.error_operator_need_child"));
            }
            if((count((array)$flowchartLinks) == count($arrayKeysVisited)) && ((count((array)$flowchartOperators)) == count($parentsArray))){
                return $parentsArray;
            }

        }
        if(empty($parentsToSearch) && ((count((array)$flowchartOperators)) != count($parentsArray))){
            throw new Exception(trans("privateMPs.error_operator_without_parent_or_child"));
        }
        $parent = $this->verifyFlowChart($flowchartOperators,$flowchartLinks,$parentsToSearch,$arrayKeysVisited,$parentsArray);
        return $parent;
    }


    /** Verify flow chart start and end added, returning start operator
     *
     * @param $flowchartOperators
     * @return int|null|string
     * @throws Exception
     */
    private function verifyStartEndFlowChart($flowchartOperators){
        $startOperatorKey = null;
        $start = false;
        $end = false;
        foreach ($flowchartOperators as $key => $operator)
        {
            switch ($operator->type){
                case 'start':
                    $startOperatorKey = $key;
                    $start = true;
                    break;
                case 'end':
                    $end = true;
                    break;
            }
            if($start && $end){
                if(count((array)$flowchartOperators) > 2) {
                    return $startOperatorKey;
                }else{
                    throw new Exception(trans("privateMPs.flowchart_need_more_than_start_and_end"));
                }
            }
        }
        throw new Exception(trans("privateMPs.flowchart_start_and_end_required"));
    }

    /** Update Mp state - finished
     *
     * @param $mpKey
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateState($mpKey)
    {
        try {
            MP::updateMpState($mpKey);
            Session::flash('message', trans('privateMPOperators.finish_ok'));
            return redirect()->action('MPsController@showConfigurations', $mpKey);
        }
        catch (Exception $e){
            return redirect()->back()->withErrors([ trans('privateMPs.finish_error') => $e->getMessage()]);
        }
    }

    /** Verify if vote has Pad parent
     *
     * @param $parentsArray
     * @param $parentsKeys
     * @return mixed
     * @throws Exception
     */
    private function verifyVoteParent($parentsArray, $parentsKeys)
    {
        $parentsSearch = [];
        $parentPad = [];
        foreach ($parentsKeys as $parent){
            switch ($parentsArray[$parent]['type']){
                case 'idea':
                case 'proposal':
                    $parentPad [] = $parent;
                    break;
                default:
                    /** Merge two array of parents, without duplicates */
                    $parentsSearch = array_unique(array_merge($parentsSearch,$parentsArray[$parent]['parents']), SORT_STRING);
                    break;
            }
        }

        /** If only one Pad is found, the parent is ok*/
        if(count($parentPad) == 1){
            return $parentPad[0];
        }
        elseif(count($parentPad) > 1){
            throw new Exception(trans("privateMPs.error_vote_operator_has_more_than_one_pad_parent"));
        }
        /** No More Parents to search, and parent Pad not found*/
        if(empty($parentsSearch)){
            throw new Exception(trans("privateMPs.error_vote_operator_has_not_pad_parent"));
        }
        $parent = $this->verifyVoteParent($parentsArray,$parentsSearch);
        return $parent;

    }

}
