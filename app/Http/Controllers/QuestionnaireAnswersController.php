<?php

namespace App\Http\Controllers;

use App\ComModules\Analytics;
use App\ComModules\Auth;
use App\ComModules\Questionnaire;
use App\Http\Requests\QuestionnaireRequest;
use App\One\One;
use Datatables;
use Illuminate\Http\Request;
use PhpParser\Node\Expr\Array_;
use Session;
use View;
use Breadcrumbs;
use Exception;
use Illuminate\Support\Collection;
use PDF;
use Maatwebsite\Excel\Facades\Excel;


class QuestionnaireAnswersController extends Controller
{
    public function __construct()
    {
        View::share('title', trans('questionnaireAnswers.title'));

    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function show($questionnarieKey, $formReplyKey)
    {
        try {
            $questionnaire = Questionnaire::getStatisticsByFormReply($questionnarieKey, $formReplyKey);

            $formKey = $questionnaire->form_key;
            $formPublic = $questionnaire->public;
            $titleQuestionnaire = $questionnaire->title;
            $questionsAll = $questionnaire->question_groups;
            $formReply = $questionnaire->formReply;


            $questionsDependencies = [];
            foreach ($questionsAll as $questionGroup) {
                foreach ($questionGroup->questions as $question) {
                    if (count($question->question_options) > 0) {
                        foreach ($question->question_options as $opt) {
                            if (count($opt->dependencies) > 0) {
                                $dependencies = [];
                                foreach ($opt->dependencies as $depend) {
                                    $dependencies[] = $depend->question_key;
                                }
                                $questionsDependencies [] = [
                                    'question_id' => $question->id,
                                    'question_key' => $question->question_key,
                                    'option_key' => $opt->question_option_key,
                                    'option_id' => $opt->id,
                                    'type' => strtoupper(preg_replace('/\s+/', '', $question->question_type->name)),
                                    'dependencies' => $dependencies
                                ];
                            }
                        }
                    }
                }
            }
            $title = trans('privateQuestionnaireAnswers.show_questionnaireAnswer') . ' ' . (isset($questionnaire->title) ? $questionnaire->title : null);
            return view('private.questionnaire.answers', compact('title', 'titleQuestionnaire', 'questionsAll', 'formKey', 'questionsDependencies', 'formPublic', 'formReply'));

        } catch (Exception $e) {
            return redirect()->back()->withErrors(["questionnaire.answers" => $e->getMessage()]);
        }
    }

    /**
     * Get Question list of answers
     *
     * @param Request $request
     * @return list|\Illuminate\Http\RedirectResponse
     */
    public function getListOfAnswers(Request $request){

        try{
            $id = $request->id;
            $list = Questionnaire::getQuestionAnswers($id);
            return $list;
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["questionnaire.answers" => $e->getMessage()]);
        }



    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\RedirectResponse|View
     */
    public function statistics($formKey)
    {

        try{

            $getAnswers = Questionnaire::getFormRepliesList($formKey);
            $totalAnswers = count($getAnswers);

            $questionnaire = Analytics::getStatistics($formKey);
            $formKey = $questionnaire->form_key;
            $formPublic = $questionnaire->public;
            $titleQuestionnaire = $questionnaire->title;
            $questionsAll = $questionnaire->question_groups;

            $questionsDependencies = [];
            foreach ($questionsAll as $questionGroup) {
                foreach ($questionGroup->questions as $question){
                    if (count($question->question_options) > 0) {
                        foreach ($question->question_options as $opt) {
                            if (count($opt->dependencies) > 0) {
                                $dependencies = [];
                                foreach ($opt->dependencies as $depend){
                                    $dependencies[] = $depend->question_key;
                                }
                                $questionsDependencies [] = [
                                    'question_id'   => $question->id,
                                    'question_key'   => $question->question_key,
                                    'option_key'    => $opt->question_option_key,
                                    'option_id'     => $opt->id,
                                    'type'          => strtoupper(preg_replace('/\s+/', '', $question->question_type->name)),
                                    'dependencies'  => $dependencies
                                ];
                            }
                        }
                    }
                }
            }

            $title = trans('privateQuestionnaireAnswers.show_questionnaireAnswer').' '.(isset($questionnaire->title) ? $questionnaire->title: null);
            return view('private.questionnaire.statistics', compact('title', 'titleQuestionnaire', 'questionsAll', 'formKey', 'questionsDependencies', 'formPublic','totalAnswers'));
        } catch (Exception $e) {
            return redirect()->back()->withErrors([trans('privateQuestionnaireAnswers.statistics') => $e->getMessage()]);
        }
    }


    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function statisticsPdf($formKey)
    {
        set_time_limit(0);


        $questionnaire = Analytics::getStatistics($formKey);

        $formKey = $questionnaire->form_key;
        $formPublic = $questionnaire->public;
        $titleQuestionnaire = $questionnaire->title;
        $questionsAll = $questionnaire->question_groups;

        $questionsDependencies = [];
        foreach ($questionsAll as $questionGroup) {
            foreach ($questionGroup->questions as $question){
                if (count($question->question_options) > 0) {
                    foreach ($question->question_options as $opt) {
                        if (count($opt->dependencies) > 0) {
                            $dependencies = [];
                            foreach ($opt->dependencies as $depend){
                                $dependencies[] = $depend->question_key;
                            }
                            $questionsDependencies [] = [
                                'question_id'   => $question->id,
                                'question_key'   => $question->question_key,
                                'option_key'    => $opt->question_option_key,
                                'option_id'     => $opt->id,
                                'type'          => strtoupper(preg_replace('/\s+/', '', $question->question_type->name)),
                                'dependencies'  => $dependencies
                            ];
                        }
                    }
                }
            }
        }

        // return view('private.questionnaire.downloadPdf',compact('titleQuestionnaire','questionsAll','formKey','questionsDependencies','formPublic'));
        $pdf = PDF::loadView('private.questionnaire.statisticsPdf',compact( 'questionnaire','titleQuestionnaire','questionsAll','formKey','questionsDependencies','formPublic'))
            ->setPaper('a4')->setWarnings(false);

        return $pdf->download('statisticsQuestionnaire.pdf');

    }


    /**
     * Export data to Excel
     *
     * @return \Illuminate\Http\RedirectResponse|View
     */
    public function excel($formKey)
    {
        try{
            $questionnaire = Analytics::getStatistics($formKey);

            $formKey = $questionnaire->form_key;
            $formPublic = $questionnaire->public;
            $titleQuestionnaire = $questionnaire->title;
            $questionsAll = $questionnaire->question_groups;

            $questionsDependencies = [];
            foreach ($questionsAll as $questionGroup) {
                foreach ($questionGroup->questions as $question){
                    if (count($question->question_options) > 0) {
                        foreach ($question->question_options as $opt) {
                            if (count($opt->dependencies) > 0) {
                                $dependencies = [];
                                foreach ($opt->dependencies as $depend){
                                    $dependencies[] = $depend->question_key;
                                }
                                $questionsDependencies [] = [
                                    'question_id'   => $question->id,
                                    'question_key'   => $question->question_key,
                                    'option_key'    => $opt->question_option_key,
                                    'option_id'     => $opt->id,
                                    'type'          => strtoupper(preg_replace('/\s+/', '', $question->question_type->name)),
                                    'dependencies'  => $dependencies
                                ];
                            }
                        }
                    }
                }
            }

            $data = [];
            $questions = [];
            foreach ($questionsAll as $questionGroup) {
                foreach ($questionGroup->questions as $question) {

                    $options2 = [];
                    foreach (!empty( $question->question_options) ? $question->question_options : [] as $option) {
                        $options2[ $option->id ] = $option->label;
                    }

                    //Get User Names
                    $userKeys = collect($question->form_replies)->pluck('created_by');
                    $usersKeysNames = Collect(Auth::getUserNames($userKeys))->pluck('name', 'user_key');

                    // Form replies
                    foreach ($question->form_replies as $formReply) {

                        $data[$formReply->form_reply_id][$formReply->question_id]["question"] = $question->question;
                        $data[$formReply->form_reply_id][$formReply->question_id]["question_type"] =  !empty($question->question_type->name) ? $question->question_type->name : "";
                        if(!empty($formReply->question_option_id) &&  array_key_exists($formReply->question_option_id,$options2)){
                            $data[$formReply->form_reply_id][$formReply->question_id]["question_option"] = $options2[(integer) $formReply->question_option_id];
                        } else if(!empty($formReply->answer) &&  array_key_exists($formReply->answer,$options2) ) {
                            $data[$formReply->form_reply_id][$formReply->question_id]["question_option"] = $options2[(integer) $formReply->answer];
                        } else {
                            $data[$formReply->form_reply_id][$formReply->question_id]["question_option"] = $formReply->answer;
                        }
                        //Insert UserKeu and User Name - who answered the question
                        $data[$formReply->form_reply_id][$formReply->question_id]["created_by"] = $formReply->created_by ?? null;
                        $data[$formReply->form_reply_id][$formReply->question_id]["created_by_name"] =  $usersKeysNames[$formReply->created_by] ?? null;
                    }

                    // Questions
                    $questions[$formReply->question_id] = $question->question;
                }
            }

            $locations = [];
            $array = json_decode(json_encode($questionnaire->form_replies), true);
            foreach($array as $key => $form_reply){
                if(!empty($form_reply["location"])) {
                    $obj = json_decode($form_reply["location"]);
                    $locations[$key]["location"] = $obj->location;
                    $locations[$key]["long"] = $obj->long;
                    $locations[$key]["lat"] = $obj->lat;
                }
            }

            Excel::create('QuestionnaireAnswers', function($excel)  use ($questionnaire,$data,$questions,$locations) {

                $excel->sheet("Data", function ($sheet) use ($data,$questions,$locations) {
                    $sheet->loadView('private.questionnaire.excel.data', compact('data','questions','questionnaire','locations'));
                });

                $excel->sheet("Total", function ($sheet) use ($questionnaire) {
                    $sheet->loadView('private.questionnaire.excel.total', compact('questionnaire'));
                });

            })->download('xlsx');

        } catch (Exception $e) {
            return redirect()->back()->withErrors([trans('privateQuestionnaireAnswers.statistics') => $e->getMessage()]);
        }
    }


    /**
     * Export data to pdf
     *
     * @return \Illuminate\Http\RedirectResponse|View
     */
    public function exportToPdf()
    {


    }

}