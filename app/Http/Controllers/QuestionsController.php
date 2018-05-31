<?php

namespace App\Http\Controllers;

use App\ComModules\Orchestrator;
use App\ComModules\Questionnaire;
use App\Http\Requests\PostRequest;
use App\Http\Requests\QuestionRequest;
use App\One\One;
use Datatables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Session;
use View;
use Breadcrumbs;
use Exception;
use Illuminate\Support\Collection;


class QuestionsController extends Controller
{

    public function __construct()
    {
        View::share('title', trans('question.title'));


    }


    /**
     * Create a new resource.
     *
     * @param $questionGroupKey
     * @return Response
     */
    public function create($questionGroupKey)
    {
        $languages = Orchestrator::getLanguageList();

        $icons = Questionnaire::getIcons();

        $questionType = Questionnaire::getQuestionList();
        $questiongroupKey = $questionGroupKey;

        $listType = [];
        foreach($questionType as $list){
            $listType[$list->question_type_key] = $list->name;
        }

        // $questionOptions = Questionnaire::getQuestionOptions($questionGroupKey);
        // $dependencies = Questionnaire::getQuestionGroupDependencies($questionGroupKey);

        /*
        $questionOptionDependencies = [];
        foreach($questionOptions as $questionOption){
            $questionoption = Questionnaire::getQuestionOption($questionOption->question_option_key);
            foreach ($questionoption->dependencies as $depend){
                $questionOptionDependencies[$questionOption->question_option_key][$depend->question_key] = 'true';
            }
        }
         */

        $title = trans('privateQuestions.create_question');

        $sidebar = 'questionGroup';
        $active = 'details';

        return view('private.questionnaire.question', compact('title', 'questionGroupKey' , 'questiongroupKey', 'listType', 'languages', 'icons', 'sidebar', 'active'  /* 'dependencies','questionOptions' */));

    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param $key
     * @return View
     * @internal param $id
     */
    public function edit($key)
    {
        try {
            $icons = Questionnaire::getIcons();
            $question = Questionnaire::getQuestion($key);
            $questionType = Questionnaire::getQuestionList();

            $listType = [];
            foreach($questionType as $list){
                $listType[$list->question_type_key] = $list->name;
            }
            $reuseQuestionsOptions = Questionnaire::getReuseQuestionOptions($question->question_group->form->form_key);
            $reuseOptions = [];
            $options = '';
            foreach ($reuseQuestionsOptions as $keyTmp => $questOption){
                foreach ($questOption as $opt){
                    $options .= $opt->label.($opt == end($questOption) ? '' :' / ');
                }
                $reuseOptions[$keyTmp] = $options;
                $options = '';
            }

            $correctOptions = collect($question->correctOptions)->pluck('question_option_key')->toArray();

            $questionOptions = Questionnaire::getQuestionOptions($key);
            $dependencies = Questionnaire::getQuestionDependencies($key);



            $questionOptionDependencies = [];
            foreach($questionOptions as $questionOption){
                $questionoption = Questionnaire::getQuestionOption($questionOption->question_option_key);
                foreach ($questionoption->dependencies as $depend){
                    $questionOptionDependencies[$questionOption->question_option_key][$depend->question_key] = 'true';
                }
            }


            $languages = Orchestrator::getLanguageList();
            $translations = collect($question->translations)->keyBy('language_code')->toArray();

            $title = trans('privateQuestions.update_question').' '.(isset($question->question) ? $question->question: null);

            $questionKey = $question->question_key;
            $sidebar = 'question';
            $active = 'details';

            Session::put('sidebarArguments', ['activeSecondMenu' => 'details', 'questionnaireKey' => $question->question_group->form->form_key, 'activeFirstMenu' => 'details', 'questiongroupKey' => $question->question_group->question_group_key, 'questionKey' => $question->question_key]);
            Session::put('sidebarArguments.activeThirdMenu', 'details');

            return view('private.questionnaire.question', compact('title', 'question', 'questionKey', 'sidebar', 'active', 'listType','reuseOptions','questionOptions','dependencies','questionOptionDependencies', 'languages', 'icons', 'translations', 'correctOptions'));


        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["question.edit" => $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param $key
     * @return Response
     * @internal param int $id
     */
    public function show($key)
    {
        try {
            $question = Questionnaire::getQuestion($key);

            $questionType = Questionnaire::getQuestionList();

            $listType = [];
            foreach($questionType as $list){
                $listType[$list->question_type_key] = $list->name;
            }

            $reuseQuestionsOptions = Questionnaire::getReuseQuestionOptions($question->question_group->form->form_key);

            $reuseOptions = [];
            $options = '';
            foreach ($reuseQuestionsOptions as $keyTmp => $questOption){
                foreach ($questOption as $opt){
                    $options .= $opt->label.($opt == end($questOption) ? '' :' / ');
                }
                $reuseOptions[$keyTmp] = $options;
                $options = '';
            }
            $title = trans('privateQuestions.show_question').' '.(isset($question->question) ? $question->question: null);

            $questionKey = $question->question_key;
            $sidebar = 'question';
            $active = 'details';
            Session::put('sidebarArguments', ['activeSecondMenu' => 'details',
                'questionnaireKey' => $question->question_group->form->form_key,
                'activeFirstMenu' => 'details',
                'questiongroupKey' => $question->question_group->question_group_key,
                'questionKey' => $question->question_key
            ]);
            Session::put('sidebarArguments.activeThirdMenu', 'details');
            return view('private.questionnaire.question', compact('title', 'question', 'questionKey', 'listType','reuseOptions', 'sidebar', 'active'));

        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["question.show" => $e->getMessage()]);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param QuestionRequest $request
     * @return $this|View
     * @internal param QuestionnaireRequest
     */
    public function store(Request $request)
    {
        $correctOptions = !empty($request->correctOption) ? explode(",",$request->correctOption) : [];

        try {
            $languages = Orchestrator::getLanguageList();
            $contentTranslation = [];

            foreach ($languages as $language){
                if($request->input('question_'.$language->code) && ($request->input("question_".$language->code)!='') || $request->input("question_".$language->code) && ($request->input("question_".$language->code)!='')){
                    $contentTranslation[] = [
                        'question' => $language->default == true ? $request->input("question_".$language->code) : $request->input("question_".$language->code),
                        'description' => $language->default == true ? $request->input("description_".$language->code) : $request->input("description_".$language->code),
                        'language_code' => $language->code
                    ];
                }
            }

            Session::flash('message', trans('question.store_ok'));
            $response = Questionnaire::setNewQuestion($request, $contentTranslation);
            $question = $response;

            $parameterItensIds = !empty($request->questionOptionsIds) ? explode(",",$request->questionOptionsIds) : [];

            foreach($parameterItensIds as $parameterItensId){

                $translations = [];
                foreach ($languages as $language){
                    $translationItem = [];
                    $translationItem["label"] = !empty($request->input('label_'.$parameterItensId."_".$language->code)) ? $request->input('label_'.$parameterItensId."_".$language->code) : null;
                    $translationItem["language_code"] = $language->code;
                    // One::verifyKeysRequest(array('question_key', 'label')
                    $translations[] = $translationItem;
                }

                $question_option_key = !empty($request->input('question_option_key_'.$parameterItensId)) ? $request->input('question_option_key_'.$parameterItensId) : null;
                $dependencies = !empty($request->input('dependencies_'.$parameterItensId)) ? $request->input('dependencies_'.$parameterItensId) : null;
                $iconId = !empty($request->input('icon_id_'.$parameterItensId)) ? $request->input('icon_id_'.$parameterItensId) : null;

                $data = [
                    'question_key' => $question->question_key,
                    'translations' => $translations,
                    'icon_id' => $iconId,
                    'dependencies' => $dependencies
                ];

                if(in_array($parameterItensId, $correctOptions))
                    $data['correctOption'] = $parameterItensId;

                $questionoption = Questionnaire::setNewQuestionOptionWithParams($data);
            }
            return redirect()->action('QuestionsController@show', $question->question_key);
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["question.store" => $e->getMessage()]);
        }
    }

    /**
     * Update the specified resource in storage.
     * @param QuestionRequest $request
     * @param $key
     * @return $this
     */
    public function update(Request $request, $key)
    {
        if(strlen($request->correctOption)>1)
            $correctOptions = !empty($request->correctOption) ? explode(",", $request->correctOption) : [];
        else
            $correctOptions = $request->correctOption;

        try {
            $languages = Orchestrator::getLanguageList();

            $questionOptionsRemove = !empty($request->questionOptionsRemove) ? explode(",",$request->questionOptionsRemove) : [];
            foreach($questionOptionsRemove as $questionOptionRemove){

                Questionnaire::deleteQuestionOption($questionOptionRemove);
            }

            $contentTranslation = [];
            foreach ($languages as $language){
                if($request->input('question_'.$language->code) && ($request->input("question_".$language->code)!='') || $request->input("question_".$language->code) && ($request->input("question_".$language->code)!='')){
                    $contentTranslation[] = [
                        'question' => $language->default == true ? $request->input("question_".$language->code) : $request->input("question_".$language->code),
                        'description' => $language->default == true ? $request->input("description_".$language->code) : $request->input("description_".$language->code),
                        'language_code' => $language->code
                    ];
                }
            }
            $responseQuestion = Questionnaire::updateQuestion($request, $key, $contentTranslation);

            Session::flash('message', trans('question.update_ok'));

            $parameterItensIds = !empty($request->questionOptionsIds) ? explode(",",$request->questionOptionsIds) : [];

            foreach($parameterItensIds as $parameterItensId){
                $question_option_key = !empty($request->input('question_option_key_'.$parameterItensId)) ? $request->input('question_option_key_'.$parameterItensId) : null;

                $dependencies = !empty($request->input('dependencies_'.$parameterItensId)) ? $request->input('dependencies_'.$parameterItensId) : null;
                $iconId = !empty($request->input('icon_id_'.$parameterItensId)) ? $request->input('icon_id_'.$parameterItensId) : null;
                // One::verifyKeysRequest(array('question_key', 'label')

                $translations = [];
                foreach ($languages as $language){
                    $translationItem = [];
                    $translationItem["label"] = !empty($request->input('label_'.$parameterItensId."_".$language->code)) ? $request->input('label_'.$parameterItensId."_".$language->code) : null;
                    $translationItem["language_code"] = $language->code;
                    // One::verifyKeysRequest(array('question_key', 'label')
                    $translations[] = $translationItem;
                }

                $data = [
                    'question_key' => $key,
                    'translations' => $translations,
                    'icon_id' => $iconId,
                    'dependencies' => $dependencies
                ];

                if((is_array($correctOptions) && in_array($parameterItensId, $correctOptions)) || ($correctOptions == $parameterItensId))
                    $data['correctOption'] = $parameterItensId;

                if( empty($question_option_key) ){
                    $questionoption = Questionnaire::setNewQuestionOptionWithParams($data);
                } else {
                    $questionoption = Questionnaire::updateQuestionOptionWithParams($question_option_key,$data);
                }
            }

            $questionOptionsRemove = !empty($request->questionOptionsRemove) ? explode(",",$request->questionOptionsRemove) : [];
            foreach($questionOptionsRemove as $questionOptionRemove){
                Questionnaire::deleteQuestionOption($questionOptionRemove);
            }

            $languages = Orchestrator::getLanguageList();


            return redirect()->action('QuestionsController@show', $responseQuestion->question_key);
        }
        catch(Exception $e) {
            //TODO: save inputs
            return redirect()->back()->withErrors(["questionnaire.update" => $e->getMessage()]);
        }
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param $key
     * @internal param $id
     * @return $this
     */
    public function destroy($key){

        try {
            $response = Questionnaire::getQuestion($key);

            $questionGroupKey = $response->question_group->question_group_key;

            Questionnaire::deleteQuestion($key);

            Session::flash('message', trans('question.delete_ok'));

            $id = Session::get('id_questiongroup', 0);

            return action('QuestionGroupsController@show', $questionGroupKey);

        }
        catch(Exception $e) {
            //TODO: save inputs
            return redirect()->back()->withErrors(["question.destroy" => $e->getMessage()]);
        }
    }


    /**
     * Show confirm popup to remove the specified resource from storage.
     *
     * @param $id
     * @return View
     */
    public function delete($key){
        $data = array();

        $data['action'] = action("QuestionsController@destroy", $key);
        $data['title'] = "DELETE";
        $data['msg'] = "Are you sure you want to delete this Content?";
        $data['btn_ok'] = "Delete";
        $data['btn_ko'] = "Cancel";

        return view("_layouts.deleteModal", $data);
    }


    /**
     * Update the Questions Order in storage.
     *
     * @param PostRequest $request
     * @return $this|View
     */
    public function updateOrder(PostRequest $request)
    {

        $arrayQuestionsRequest = json_decode($request->order);
        if(count($arrayQuestionsRequest) > 0){
            $i = 1;
            $updatedPositions = [];
            foreach ($arrayQuestionsRequest as $itemId){
                $updatedPositions[]= array('key' => $itemId, 'position' => $i);
                $i++;
            }
            try{
                Questionnaire::updateQuestionGroupPositions($updatedPositions);
                return '1';
            }catch (Exception $e){
                return '0';
            }

        }
    }


    public function getQuestions($key)
    {

        Session::put('id_questiongroup', $key);

            $list = Questionnaire::getListQuestionGroup($key);

            $result = '';
            foreach ($list as $item) {

                $result .= "<li class='dd-item nested-list-item' data-order='{$item->position}' data-id='{$item->question_key}'>";
                $result .= "<div class='dd-handle nested-list-handle' style='height: 35px;text-align: center;padding-left:0px; padding-right: 0px; width: 35px'>";
                $result .= "<span class='glyphicon glyphicon-move'></span>";
                $result .= "</div>";
                $result .= "<div class='nested-list-content' style='height: 35px; cursor: pointer' onclick=javascript:location.href='" . action('QuestionsController@show', $item->question_key) . "' >";
                $result .= $item->question;
                $result .= "<div class='pull-right'>";
                // $result .=  ONE::actionButtons($item->id, ['edit' => 'QuestionsController@edit', 'delete' => 'QuestionsController@delete']);
                $result .= "</div>";
                $result .= " </div></li>";
            }
            return $result ? "\n<ol class=\"dd-list\">\n$result</ol>\n" : null;

    }


    /**
     * Set the specified resource in storage.
     * @param $key
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function reuseOptions($key)
    {
        try {
            Questionnaire::reuseQuestionOptions($key);
            Session::flash('message', trans('question.reuseOk'));

            return redirect()->action('QuestionsController@show', $key);

        }
        catch(Exception $e) {
            //TODO: save inputs
            return redirect()->back()->withErrors([trans('question.reuseNok') => $e->getMessage()]);
        }
    }



}
