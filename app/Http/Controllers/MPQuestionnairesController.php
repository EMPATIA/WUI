<?php

namespace App\Http\Controllers;

use App\ComModules\MP;
use App\ComModules\Questionnaire;
use App\Http\Requests\MPQuestionnaireRequest;
use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Session;

class MPQuestionnairesController extends Controller
{
    /**
     * Display a listing of the resource.
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
                throw new Exception(trans('privateMPQuestionnaires.error_in_operator'));
            }
            $operator = MP::getOperator($operatorKey);
            $questionnairesList = Questionnaire::getQuestionnaireList();
            $questionnaires = collect($questionnairesList)->pluck('title','form_key')->toArray();
            $data = [];
            $data['questionnaires'] = $questionnaires;
            $data['operator'] = $operator;

            return view('private.mps.questionnaire.questionnaire', $data);
        } catch (Exception $e) {
            return redirect()->back()->withErrors([trans('privateMPQuestionnaires.create_questionnaire_error') => $e->getMessage()]);
        }
    }

    /**
     * Store a newly created resource in storage.
     * @param MPQuestionnaireRequest $request
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function store(MPQuestionnaireRequest $request)
    {
        try {
            $operatorKey = $request->operator_key;
            $mpKey = $request->mp_key;
            $questionnaireKey = $request->questionnaire_key;

            if(empty($operatorKey) || empty($mpKey) || empty($questionnaireKey)){
                throw new Exception('operator_error');
            }
            MP::updateOperator($operatorKey,$questionnaireKey);

            Session::flash('message', trans('privateMPQuestionnaires.store_ok'));
            return redirect()->action('MPsController@showConfigurations', ['mp_key' => $mpKey]);

        } catch (Exception $e) {
            return redirect()->back()->withErrors([trans('privateMPQuestionnaires.store_error') => $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param $operatorKey
     * @return \Illuminate\Http\RedirectResponse
     */
    public function show($operatorKey)
    {
        try {
            $operator = MP::getOperator($operatorKey);
            $questionnairesList = Questionnaire::getQuestionnaireList();
            $questionnaires = collect($questionnairesList)->pluck('title','form_key')->toArray();

            $data = [];
            $data['questionnaires'] = $questionnaires;
            $data['operator'] = $operator;

            return view('private.mps.questionnaire.questionnaire', $data);

        }catch (Exception $e){
            return redirect()->back()->withErrors([trans('privateMPQuestionnaires.create_error') => $e->getMessage()]);
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
            $questionnairesList = Questionnaire::getQuestionnaireList();
            $questionnaires = collect($questionnairesList)->pluck('title','form_key')->toArray();

            $data = [];
            $data['questionnaires'] = $questionnaires;
            $data['operator'] = $operator;

            return view('private.mps.questionnaire.questionnaire', $data);

        }catch (Exception $e){
            return redirect()->back()->withErrors([trans('privateMPQuestionnaires.edit_error') => $e->getMessage()]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param MPQuestionnaireRequest $request
     * @param $operatorKey
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function update(MPQuestionnaireRequest $request, $operatorKey)
    {
        try {
            $mpKey = $request->mp_key;
            $questionnaireKey = $request->questionnaire_key;
            if(empty($mpKey) || empty($questionnaireKey)){
                throw new Exception('operator_error');
            }
            MP::updateOperator($operatorKey,$questionnaireKey);

            Session::flash('message', trans('privateMPQuestionnaires.update_ok'));
            return redirect()->action('MPsController@showConfigurations', ['mp_key' => $mpKey]);


        } catch (Exception $e) {
            return redirect()->back()->withErrors([trans('privateMPQuestionnaires.update_error') => $e->getMessage()]);
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
            MP::updateOperator($operatorKey,0);

            Session::flash('message', trans('privateMPQuestionnaires.delete_ok'));
            return action('MPsController@showConfigurations',$operator->mp->mp_key);

        } catch (Exception $e) {
            return redirect()->back()->withErrors([trans('privateMPQuestionnaires.delete_error') => $e->getMessage()])->getTargetUrl();
        }
    }


    /**
     * @param $operatorKey
     * @internal param $type
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function delete($operatorKey){
        $data = array();
        $data['action'] = action("MPQuestionnairesController@destroy", ['operatorKey'=>$operatorKey]);
        $data['title'] = trans('privateMPQuestionnaires.delete');
        $data['msg'] = trans('privateMPQuestionnaires.are_you_sure_you_want_to_delete').' ?';
        $data['btn_ok'] = trans('privateMPQuestionnaires.delete');
        $data['btn_ko'] = trans('privateMPQuestionnaires.cancel');

        return view("_layouts.deleteModal", $data);
    }
}
