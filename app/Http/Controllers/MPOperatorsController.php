<?php

namespace App\Http\Controllers;

use App\ComModules\MP;
use App\Http\Requests\MPOperatorRequest;
use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Session;
use Validator;

class MPOperatorsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request,$operatorKey)
    {
        $mpKey = $request->mp_key;
        return redirect()->action('MPsController@showConfigurations', $mpKey);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function create($operatorKey)
    {
        try {
            $operator = MP::getOperator($operatorKey);
            $mp = $operator->mp;
            $action = '';
            switch($operator->operator_type->code){
                case 'start':
                    return view('private.mps.dates',compact('mp','operator'));
                    break;
                case 'idea':
                case 'proposal':
                    $action = action('MPCbsController@create', ['operator_key' => $operator->operator_key]);
                    break;
                case 'vote':
                    $action = action('MPVotesController@create', ['operator_key' => $operator->operator_key]);
                    break;
                case 'questionnaire':
                    $action = action('MPQuestionnairesController@create', ['operator_key' => $operator->operator_key]);
                    break;
            }
            if(empty($action)){
                throw new Exception('no_action_defined');
            }
            return redirect($action);
        }catch(Exception $e){
            return redirect()->back()->withErrors([ trans('privateMPOperators.create_error') => $e->getMessage()]);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param MPOperatorRequest $request
     * @param $operatorKey
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function store(MPOperatorRequest $request,$operatorKey)
    {
        try{
            $operator = MP::getOperator($operatorKey);
            if($operator->operator_type->code == 'start'){
                $startDate = $request->start_date;
                $endDate = $request->end_date;
                $mp = MP::updateMpDates($operator->mp->mp_key,$startDate,$endDate);
            }
            Session::flash('message', trans('privateMPOperators.store_ok'));
            return redirect()->action('MPsController@showConfigurations', $operator->mp->mp_key);
        }catch(Exception $e){
            return redirect()->back()->withErrors([ trans('privateMPOperators.store_error') => $e->getMessage()]);
        }

    }

    /**
     * Display the specified resource.
     * @param $operatorKey
     * @return MPOperatorsController|\Illuminate\Http\RedirectResponse
     */
    public function show($operatorKey)
    {
        try {
            $operator = MP::getOperator($operatorKey);
            $mp = $operator->mp;
            $action = '';
            switch($operator->operator_type->code){
                case 'start':
                    return view('private.mps.dates',compact('mp','operator'));
                    break;
                case 'idea':
                case 'proposal':
                    $action = action('MPCbsController@show', ['operator_key' => $operator->operator_key]);
                    break;
                case 'vote':
                    $action = action('MPVotesController@show', ['operator_key' => $operator->operator_key]);
                    break;
                case 'questionnaire':
                    $action = action('MPQuestionnairesController@show', ['operator_key' => $operator->operator_key]);
                    break;
            }
            if(empty($action)){
                throw new Exception('no_action_defined');
            }
            return redirect($action);
        }catch(Exception $e){
            return redirect()->back()->withErrors([ trans('privateMPOperators.show_error') => $e->getMessage()]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param $operatorKey
     * @return \Illuminate\Http\RedirectResponse
     */
    public function edit($operatorKey)
    {
        try {
            $operator = MP::getOperator($operatorKey);
            $mp = $operator->mp;
            $action = '';
            switch($operator->operator_type->code){
                case 'start':
                    return view('private.mps.dates',compact('mp','operator'));
                    break;
                case 'idea':
                case 'proposal':
                    $action = action('MPCbsController@edit', ['operator_key' => $operator->operator_key]);
                    break;
                case 'vote':
                    $action = action('MPVotesController@edit', ['operator_key' => $operator->operator_key]);
                    break;
                case 'questionnaire':
                    $action = action('MPQuestionnairesController@edit', ['operator_key' => $operator->operator_key]);
                    break;
            }
            if(empty($action)){
                throw new Exception('no_action_defined');
            }
            return redirect($action);
        }catch(Exception $e){
            return redirect()->back()->withErrors([ trans('privateMPOperators.edit_error') => $e->getMessage()]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param $operatorKey
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $operatorKey)
    {
        try{
            $operator = MP::getOperator($operatorKey);
            if($operator->operator_type->code == 'start'){
                $startDate = $request->start_date;
                $endDate = $request->end_date;
                $mp = MP::updateMpDates($operator->mp->mp_key,$startDate,$endDate);
            }
            Session::flash('message', trans('privateMPOperators.update_ok'));
            return redirect()->action('MPsController@showConfigurations', $operator->mp->mp_key);
        }catch(Exception $e){
            return redirect()->back()->withErrors([ trans('privateMPOperators.update_error') => $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
