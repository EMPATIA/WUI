<?php

namespace App\Http\Controllers;

use App\ComModules\Files;
use App\ComModules\Questionnaire;
use App\ComModules\Orchestrator;
use App\Http\Requests\QuestionnaireRequest;
use App\Http\Requests\QuestionOptionRequest;
use App\One\One;
use Datatables;
use Illuminate\Http\Request;
use Session;
use View;
use Breadcrumbs;
use Exception;
use Illuminate\Support\Collection;


class QuestionOptionsController extends Controller
{


    public function __construct()
    {
        View::share('title', trans('questionoption.title'));



    }

    /**
     * Create a new resource.
     *
     * @return Response
     */
    public function create($questionKey)
    {
        try {
            $icons = Questionnaire::getIcons();
            $uploadKey = Files::getUploadKey();
            $question = Questionnaire::getQuestion($questionKey);
            $dependencies = Questionnaire::getQuestionDependencies($questionKey);

            $sidebar = 'question';
            $active = 'details';

            return view('private.questionnaire.questionoption', compact('questionKey','uploadKey','question','icons', 'dependencies', 'sidebar', 'active'));
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["questionoption.create" => $e->getMessage()]);
        }
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param $id
     * @return View
     */
    public function edit($key)
    {
        try {
            $icons = Questionnaire::getIcons();
            $questionoption = Questionnaire::getQuestionOption($key);
            $question = $questionoption->question;
            $questionKey = $question->question_key;
            $dependencies = Questionnaire::getQuestionDependencies($question->question_key);
            $uploadKey = Files::getUploadKey();

            $questionOptionDependencies = [];
            foreach ($questionoption->dependencies as $depend){
                $questionOptionDependencies[$depend->question_key] = 'true';
            }

            $sidebar = 'question';
            $active = 'details';

            return view('private.questionnaire.questionoption', compact('questionoption', 'questionKey', 'uploadKey','question','icons','dependencies','questionOptionDependencies', 'sidebar', 'active'));
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["questionoption.show" => $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($key)
    {
        try {
            $questionoption = Questionnaire::getQuestionOption($key);
            $question = $questionoption->question;
            $questionKey = $question->question_key;
            $dependencies = Questionnaire::getQuestionDependencies($question->question_key);
            $uploadKey = Files::getUploadKey();

            $questionOptionDependencies = [];
            foreach ($questionoption->dependencies as $depend){
                $questionOptionDependencies[$depend->question_key] = 'true';
            }

            $sidebar = 'question';
            $active = 'details';

            return view('private.questionnaire.questionoption', compact('questionoption', 'questionKey', 'uploadKey','question','dependencies','questionOptionDependencies', 'sidebar', 'active'));

        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["questionoption.show" => $e->getMessage()]);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param QuestionOptionRequest $request
     * @return $this|View
     * @internal param QuestionOptionRequest
     */
    public function store(QuestionOptionRequest $request)
    {
        try {
            $questionoption = Questionnaire::setNewQuestionOption($request);
            Session::flash('message', trans('questionoption.store_ok'));
            return redirect()->action('QuestionOptionsController@show', $questionoption->question_option_key);
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["questionoption.store" => $e->getMessage()]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param QuestionnaireRequest $request
     * @param $id
     * @return $this|View
     */
    public function update(QuestionOptionRequest $request, $key)
    {
        try {
            $questionoption = Questionnaire::updateQuestionOption($key,$request);
            Session::flash('message', trans('questionoption.update_ok'));
            return redirect()->action('QuestionOptionsController@show', $key);
        }
        catch(Exception $e) {
            //TODO: save inputs
            return redirect()->back()->withErrors(["questionoption.update" => $e->getMessage()]);
        }
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  $id
     * @return Response
     */
    public function destroy($key){

        try {
            $questionOption = Questionnaire::getQuestionOption($key);
            $questionKey = $questionOption->question->question_key;

            Questionnaire::deleteQuestionOption($key);
            Session::flash('message', trans('questionoption.delete_ok'));

            $id = Session::get('id_question', 0);

            return action('QuestionsController@show', $questionKey);

        }
        catch(Exception $e) {
            //TODO: save inputs
            return redirect()->back()->withErrors(["questionoption.destroy" => $e->getMessage()]);
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

        $data['action'] = action("QuestionOptionsController@destroy", $key);
        $data['title'] = "DELETE";
        $data['msg'] = "Are you sure you want to delete this Content?";
        $data['btn_ok'] = "Delete";
        $data['btn_ko'] = "Cancel";

        return view("_layouts.deleteModal", $data);
    }


//    public function getOptions(){
//
//        $response = ONE::get([
//            'component'     => 'q',
//            'api'           => 'questionOption',
//            'attribute'        => 'list'
//        ]);
//
//
//        if($response->statusCode() == 200) {
//            $list = $response->json();
//
//            // in case of json
//            $collection = Collection::make($list);
//
//            return Datatables::of($collection)
//                ->editColumn('id', function ($collection) {
//                    return "<a href='" . action('QuestionGroupsController@show', $collection->id) . "'>" . $collection->id . "</a>";
//                })
//                ->editColumn('label', function ($collection) {
//                    return "<a href='" . action('QuestionGroupsController@show', $collection->id) . "'>" . $collection->label . "</a>";
//                })
//
//                ->addColumn('action', function ($collection) {
//                    return ONE::actionButtons($collection->id, ['edit' => 'QuestionGroupsController@edit', 'delete' => 'QuestionGroupsController@delete']);
//                })
//                ->make(true);
//        }
//    }

    public function getQuestionOptions($key){

        Session::put('id_question', $key);
        $list = Questionnaire::getQuestionOptions($key);

        $result = '';
        if(count($list) > 0) {
            $result .= "<ol class='dd-list'>";
            foreach ($list as $item) {
                $result .= "<li class='dd-item nested-list-item' data-order='{$item->position}' data-id='{$item->question_option_key}' data-type='question-option'>";
                $result .= "<div class='dd-handle nested-list-handle'>";
                $result .= "<span class='glyphicon glyphicon-move'></span>";
                $result .= "</div>";
                $result .= "<div class='nested-list-content'>";
                $result .= "<a href='" . action("QuestionOptionsController@show", $item->question_option_key) . "'>$item->label</a>";
//                $result .= "<div class='nested-list-content' style='height: 35px; cursor: pointer' onclick=javascript:location.href='" . action('QuestionGroupsController@show', $item->question_group_key) . "' >";
//                $result .= $item->title;
                $result .= " </div>";
                $result .= " </li>";
            }
            $result .= "</ol>";
        }
        return $result;
    }


    /**
     * Update the Questions options Order in storage.
     *
     * @return string
     */
    public function updateOrder(Request $request)
    {

        $arrayQuestionsRequest = json_decode($request->order);
        if(count($arrayQuestionsRequest) > 0){
            $i = 1;
            $updatedPositions = [];
            foreach ($arrayQuestionsRequest as $itemId){
                $updatedPositions[]= array('question_option_key' => $itemId, 'position' => $i);
                $i++;
            }
            Questionnaire::updateQuestionGroupPositions($updatedPositions);
            return 'true';
        }
    }



    public function addOptionImage(Request $request){
        try{
            $fileId = $request->file_id;
            $file = Files::getFile($fileId);


            return json_encode($file);
        }
        catch(Exception $e) {
            return "false";
        }
    }


    public function useOptions(Request $request)
    {
        try {
            $question = Questionnaire::setReuseOptions($request);
            Session::flash('message', trans('questionoption.store_ok'));
            return redirect()->action('QuestionsController@show', $question->question_key);
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors([ trans('questionoption.storeNok') => $e->getMessage()]);
        }
    }

    public function addQuestionOption(Request $request, $questionKey = ""){
        try{
            if($questionKey != "")
                $dependencies = Questionnaire::getQuestionDependencies($questionKey);
            else
                $dependencies = [];

            $inputId =  $request->inputId;
            $languages = Orchestrator::getLanguageList();
            $icons = Questionnaire::getIcons();

            $i = 0;
            $views = [];
            foreach($languages as $language) {
                $languageCode = $language->code;
                if ($i == 0) {
                    $views[$language->code] = view('private.questionnaire.addQuestionOption', compact('inputId', 'languageCode', 'dependencies', 'icons'))->render();
                } else {
                    $views[$language->code] = view('private.questionnaire.addQuestionOptionTranslation', compact('inputId', 'languageCode'))->render();
                }
                $i++;
            }

            return $views;
        }
        catch(Exception $e) {
            return $e->getMessage();
        }
    }


}
