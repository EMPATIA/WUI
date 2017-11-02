<?php

namespace App\Http\Controllers;

use App\ComModules\Orchestrator;
use App\ComModules\Questionnaire;
use App\Http\Requests\PostRequest;
use App\Http\Requests\QuestionGroupRequest;
use App\Http\Requests\QuestionnaireRequest;
use App\One\One;
use Datatables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Session;
use View;
use Breadcrumbs;
use Exception;
use Illuminate\Support\Collection;


class QuestionGroupsController extends Controller
{


    public function __construct()
    {
        View::share('title', trans('question.groups.title'));




    }


    /**
     * Create a new resource.
     *
     * @param $formKey
     * @return Response
     */
    public function create($formKey)
    {
        //Getting languages for translations
        $languages = Orchestrator::getLanguageList();

        $title = trans('privateQuestionGroups.create_questionGroup');
        $questionnaireKey = $formKey;
        $sidebar = 'q';
        $active = 'details';

        Session::put('sidebarArguments', ['questionnaireKey' => $formKey, 'activeFirstMenu' => 'details']);

        return view('private.questionnaire.questiongroup', compact('title', 'questionnaireKey', 'formKey','languages', 'sidebar', 'active'));
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
            $response = Questionnaire::getQuestionGroup($key);
            $questiongroup = $response;

            $languages = Orchestrator::getLanguageList();
            $translations = collect($questiongroup->translations)->keyBy('language_code')->toArray();

            $title = trans('privateQuestionGroups.update_questionGroup').' '.(isset($questiongroup->title) ? $questiongroup->title: null);

            $questiongroupKey = $questiongroup->question_group_key;
            $sidebar = 'questionGroup';
            $active = 'details';

            Session::put('sidebarArguments', ['questionnaireKey' => $questiongroup->form->form_key, 'activeFirstMenu' => 'details', 'questiongroupKey' => $questiongroupKey]);
            Session::put('sidebarArguments.activeSecondMenu', 'details');

            return view('private.questionnaire.questiongroup', compact('title', 'questiongroup', 'questiongroupKey', 'languages', 'translations', 'sidebar', 'active'));

        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["questiongroup.show" => $e->getMessage()]);
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
            $response = Questionnaire::getQuestionGroup($key);
            $questiongroup = $response;

            $title = trans('privateQuestionGroups.show_questionGroup').' '.(isset($questiongroup->title) ? $questiongroup->title: null);

            $questiongroupKey = $questiongroup->question_group_key;

            $sidebar = 'questionGroup';
            $active = 'details';

            Session::put('sidebarArguments', ['questionnaireKey' => $questiongroup->form->form_key, 'activeFirstMenu' => 'details', 'questiongroupKey' => $questiongroupKey]);
            Session::put('sidebarArguments.activeSecondMenu', 'details');

            return view('private.questionnaire.questiongroup', compact('title', 'questiongroup', 'sidebar', 'active', 'questiongroupKey'));

        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["questiongroup.show" => $e->getMessage()]);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param QuestionGroupRequest $request
     * @return $this|View
     * @internal param QuestionnaireRequest
     */
    public function store(Request $request)
    {
        try {

            //Getting languages for translations
            $languages = Orchestrator::getLanguageList();
            $contentTranslation = [];

            foreach ($languages as $language){
                if($request->input('title_'.$language->code) && ($request->input("title_".$language->code)!='') || $request->input("title_".$language->code) && ($request->input("title_".$language->code)!='')){
                    $contentTranslation[] = [
                        'title' => $language->default == true ? $request->input("title_".$language->code) : $request->input("title_".$language->code),
                        'description' => $language->default == true ? $request->input("description_".$language->code) : $request->input("description_".$language->code),
                        'language_code' => $language->code
                    ];
                }
            }

            $response = Questionnaire::setNewQuestionGroup($request, $contentTranslation);
            Session::flash('message', trans('questionnaire.store_ok'));
            $questiongroup = $response;

            return redirect()->action('QuestionGroupsController@show',  $questiongroup->question_group_key);
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["questiongroup.store" => $e->getMessage()]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param QuestionnaireRequest $request
     * @param $id
     * @return $this|View
     */
    public function update(Request $request, $key)
    {
        try {
            $languages = Orchestrator::getLanguageList();

            $contentTranslation = [];
            foreach ($languages as $language){
                if($request->input('title_'.$language->code) && ($request->input("title_".$language->code)!='') || $request->input("title_".$language->code) && ($request->input("title_".$language->code)!='')){
                    $contentTranslation[] = [
                        'title' => $language->default == true ? $request->input("title_".$language->code) : $request->input("title_".$language->code),
                        'description' => $language->default == true ? $request->input("description_".$language->code) : $request->input("description_".$language->code),
                        'language_code' => $language->code
                    ];
                }
            }

            $response = Questionnaire::updateQuestionGroup($request, $key, $contentTranslation);
            $questiongroup = $response;
            Session::flash('message', trans('questionnaire.update_ok'));

            return redirect()->action('QuestionGroupsController@show', $questiongroup->question_group_key);
        }
        catch(Exception $e) {
            //TODO: save inputs
            return redirect()->back()->withErrors(["questiongroup.update" => $e->getMessage()]);
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
            $response = Questionnaire::getQuestionGroup($key);
            $formKey = $response->form->form_key;

            Questionnaire::deleteQuestionGroup($key);
            Session::flash('message', trans('questionnaire.delete_ok'));

            // $idQuestionnaire = Session::get('id_questionnaire', 0);
            return action('QuestionnairesController@show', $formKey);

        }
        catch(Exception $e) {
            //TODO: save inputs
            return redirect()->back()->withErrors(["questionnaire.destroy" => $e->getMessage()]);
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

        $data['action'] = action("QuestionGroupsController@destroy", $key);
        $data['title'] = "DELETE";
        $data['msg'] = "Are you sure you want to delete this Group?";
        $data['btn_ok'] = "Delete";
        $data['btn_ko'] = "Cancel";

        return view("_layouts.deleteModal", $data);
    }



    /**
     * Update the Questions Group Order in storage.
     *
     * @param PostRequest $request
     * @return string
     */
    public function updateOrder(PostRequest $request)
    {
        $source = $request->source;
        $destination = $request->destination;
        $ordering = json_decode($request->order);
        $rootOrdering = json_decode($request->rootOrder);
        $groupType = $request->groupType; //type of object moved
        $destinationGroupType = $request->destinationGroupType; //type of object moved
        $arrayQuestionsRequest = json_decode($request->order);
        if($groupType == 'question-group' && !isset($rootOrdering) || $groupType == 'question' && !isset($destinationGroupType) || $groupType == 'question' && isset($destinationGroupType) && $destinationGroupType != 'question-group'){
            return 'false';
        }
        if(isset($rootOrdering) && count($rootOrdering) > 0){
            $i = 1;
            $updatedPositions = [];
            foreach ($rootOrdering as $itemKey){
                $updatedPositions[]= array('question_group_key' => $itemKey, 'position' => $i);
                $i++;
            }

            Questionnaire::updateQuestionGroupPositions($updatedPositions);
            return 'true';
        }
        if(isset($ordering) && count($ordering) > 0 && isset($destination) && isset($source)){
            $i = 1;
            $updatedPositions = [];
            foreach ($ordering as $itemKey){
                $updatedPositions[]= array('key' => $itemKey, 'position' => $i);
                $i++;
            }

            Questionnaire::updateChangeQuestion($updatedPositions, $destination, $source);

            return 'true';
        }

        return 'false';

    }


    /**
     *
     *
     * @param $id
     * @return mixed
     */
    public function getQuestionGroups($key){

        Session::put('id_questiongroup', $key);

        $list = Questionnaire::getQuestionGroups($key);


        $result = '';
        if(count($list) > 0) {
            $result .= "<ol class='dd-list'>";
            foreach ($list as $item) {
                $result .= "<li class='dd-item nested-list-item' data-order='{$item->position}' data-id='{$item->question_group_key}' data-type='question-group' data-group='none'>";
                $result .= "<div class='dd-handle nested-list-handle'>";
                $result .= "<span class='glyphicon glyphicon-move'></span>";
                $result .= "</div>";
                $result .= "<div class='nested-list-content'>";
                $result .= "<a href='" . action("QuestionGroupsController@show", $item->question_group_key) . "'>$item->title</a>";
//                $result .= "<div class='nested-list-content' style='height: 35px; cursor: pointer' onclick=javascript:location.href='" . action('QuestionGroupsController@show', $item->question_group_key) . "' >";
//                $result .= $item->title;
                $result .= "<div class='pull-right'>";
                $result .= ONE::actionButtons($item->question_group_key, ['create' => 'QuestionsController@create']);
                $result .= "</div>";
                $result .= " </div>";
                if (count($item->questions) > 0) {
                    $result .= "<ol class='dd-list'>";
                    foreach ($item->questions as $question) {
                        $result .= "<li class='dd-item nested-list-item' data-order='{$question->position}' data-id='{$question->question_key}' data-type='question' data-group='{$item->question_group_key}'>";
                        $result .= "<div class='dd-handle nested-list-handle'>";
                        $result .= "<span class='glyphicon glyphicon-move'></span>";
                        $result .= "</div>";
                        $result .= "<div class='nested-list-content'>";
                        $result .= "<a href='" . action('QuestionsController@show', $question->question_key) . "'>$question->question</a>";
                        $result .= "<div class='pull-right'>";
                        $result .= "</div>";
                        $result .= " </div>";
                        $result .= " </li>";
                    }
                    $result .= "</ol>";
                }
                $result .= " </li>";
            }
            $result .= "</ol>";
        }
        return $result;

    }

}
