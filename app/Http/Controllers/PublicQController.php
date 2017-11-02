<?php

namespace App\Http\Controllers;

use App\ComModules\Auth;
use App\ComModules\CB;
use App\ComModules\EMPATIA;
use App\ComModules\Files;
use App\ComModules\Questionnaire;
use App\Http\Requests\CBRequest;
use App\Http\Requests\QRequest;
use App\Http\Requests\UserRequest;
use App\One\One;
use App\User;
use Datatables;
use Illuminate\Support\Facades\URL;
use Redirect;
use Illuminate\Http\Request;
use Session;
use View;
use Breadcrumbs;
use Exception;
use Illuminate\Support\Collection;
use PDF;


class PublicQController extends Controller
{

    public function __construct()
    {

        View::share('title', 'Questionnaire');

    }


    public function success(){
        $message = 0;
        return view('public.'.ONE::getEntityLayout().'.questionnaire.index', compact('message'));
    }

    public function intro($questionnaireId)
    {
        try {
            $completed = Questionnaire::verifyReply($questionnaireId);
            if($completed){
                $message = 1;
                return view('public.'.ONE::getEntityLayout().'.questionnaire.index', compact('message'));
            }
            return redirect()->action('PublicQController@showQ',$questionnaireId);

        } catch (Exception $e) {
            return redirect()->action('PublicQController@showQ',$questionnaireId);
        }
    }

    /**
     * Display a question of the resource.
     * @param $questionnaireKey
     * @return $this|\Illuminate\Http\RedirectResponse|View
     */
    public function showQ($questionnaireKey)
    {
        try {
            /**
             * Get upload key
             */
            $uploadKey = Files::getUploadKey();

            $form = Files::getFormConstruction($questionnaireKey);

            $formKey = $form->form_key;
            $formPublic = $form->public;
            $titleQuestionnaire = $form->title;
            $descriptionQuestionnaire = $form->description;
            $questionsAll = $form->question_groups;
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
            return view('public.' . ONE::getEntityLayout() . '.questionnaire.form', compact('uploadKey','titleQuestionnaire', 'questionsAll', 'formKey','questionsDependencies','formPublic','descriptionQuestionnaire'));

        } catch (Exception $e) {
            return redirect()->back()->withErrors(["form.list" => $e->getMessage()]);
        }

    }



    /**
     * Store a newly created resource in storage.
     *
     * @param QRequest $request
     * @return $this|View
     * @internal param CBRequest $requestCB
     */
    public function store(QRequest $request)
    {

        $requestForm = $request->all();
        $questions = [];
        foreach($requestForm as $question => $answer){
            if(strpos($question, "text_") !== false){
                $questions[] = [
                    'question_id' => str_replace('text_', '', $question),
                    'answer' => $answer
                ];
            }
            if(strpos($question, "file_") !== false){
                $questions[] = [
                    'question_id' => str_replace('file_', '', $question),
                    'answer' => $answer
                ];
            }
            else if(strpos($question, "optionsRadios_") !== false){
                $questions[] = [
                    'question_id' => str_replace('optionsRadios_', '', $question),
                    'question_option_id' => str_replace('radio_', '', $answer),
                ];
            }
            else if(strpos($question, "textarea_") !== false){
                $questions[] = [
                    'question_id' => str_replace('textarea_', '', $question),
                    'answer' => $answer
                ];
            }
            else if(strpos($question, "optionsCheck_") !== false){

                if(count($answer)> 1) {
                    //unset($answer[0]);
                    foreach ($answer as $checkOption) {
                        $newAnswer [] = str_replace('check_', '', $checkOption);
                    }
                    $questions[] = [
                        'question_id' => str_replace('optionsCheck_', '', $question),
                        'question_option_id' => $newAnswer
                    ];
                }else{
                    $questions[] = [
                        'question_id' => str_replace('optionsCheck_', '', $question),
                        'question_option_id' => str_replace('check_', '', $answer[0])
                    ];
                }
            }
            else if(strpos($question, "optionsDropdown_") !== false){
                $questions[] = [
                    'question_id' => str_replace('optionsDropdown_', '', $question),
                    'question_option_id' => $answer
                ];
            }
        }
        try {
            $location = !empty($request->location) ? $request->location : "";
            $obj = Questionnaire::setFormReply($request, true, $location, $questions);
            $formKey = $request->questionnaire_key;
            $formReplyKey = $obj->form_reply_key;
            $message = 0;
            $locationJson = !empty($location) ? json_decode($location) : "";
            return view('public.'.ONE::getEntityLayout().'.questionnaire.index', compact('formKey','formReplyKey','message','locationJson'));
        } catch (Exception $e) {
            //TODO: save inputs
            return redirect()->back()->withErrors(["form.store" => $e->getMessage()]);
        }
    }



    /**
     * Store a step newly created resource.
     *
     * @param QRequest $request
     * @return $this|View
     * @internal param CBRequest $requestCB
     */
    public function storeStep(QRequest $request)
    {
        $requestForm = $request->all();
        $questions = [];
        foreach($requestForm as $question => $answer){
            if(strpos($question, "text_") !== false){
                $questions[] = [
                    'question_id' => str_replace('text_', '', $question),
                    'answer' => $answer
                ];
            }
            if(strpos($question, "file_") !== false){
                $questions[] = [
                    'question_id' => str_replace('file_', '', $question),
                    'answer' => $answer
                ];
            }
            else if(strpos($question, "optionsRadios_") !== false){
                $questions[] = [
                    'question_id' => str_replace('optionsRadios_', '', $question),
                    'question_option_id' => str_replace('radio_', '', $answer),
                ];
            }
            else if(strpos($question, "textarea_") !== false){
                $questions[] = [
                    'question_id' => str_replace('textarea_', '', $question),
                    'answer' => $answer
                ];
            }
            else if(strpos($question, "optionsCheck_") !== false){

                if(count($answer)> 1) {
                    //unset($answer[0]);
                    foreach ($answer as $checkOption) {
                        $newAnswer [] = str_replace('check_', '', $checkOption);
                    }
                    $questions[] = [
                        'question_id' => str_replace('optionsCheck_', '', $question),
                        'question_option_id' => $newAnswer
                    ];
                }else{
                    $questions[] = [
                        'question_id' => str_replace('optionsCheck_', '', $question),
                        'question_option_id' => str_replace('check_', '', $answer[0])
                    ];
                }

            }
            else if(strpos($question, "optionsDropdown_") !== false){
                $questions[] = [
                    'question_id' => str_replace('optionsDropdown_', '', $question),
                    'question_option_id' => $answer
                ];
            }
        }
        try {
            if(isset($request->formComplete) && $request->formComplete == 'true'){
                $complete = 1;
            }else{
                $complete = 0;
            }
            Questionnaire::setFormReply($request, $complete, $request->location, $questions);

            if($request->formComplete == 'true'){
                return 'success';
            }
            return 'true';
        } catch (Exception $e) {
            return 'false';
        }
    }


    public function showAnswers($questionnarieKey,$formReplyKey)
    {
        set_time_limit(0);

        try {
            $form = Questionnaire::getStatisticsByFormReply($questionnarieKey, $formReplyKey);


            //  $userKey = $form->formReply->created_by;
            $user = json_encode(['name' => trans('privateQuestionnairePDF.anonymous')]);


            $formKey = $form->form_key;
            $formPublic = $form->public;
            $location = !empty($form->formReply->location) ? json_decode($form->formReply->location)->location : "";
            $lat = !empty($form->formReply->location) ? json_decode($form->formReply->location)->lat : "";
            $long = !empty($form->formReply->location) ? json_decode($form->formReply->location)->long : "";

            //
            $formReplies = Questionnaire::getFormRepliesList($questionnarieKey);
            $counter = 0;
            foreach($formReplies as $formReply){
                $replayLocation = json_decode($formReply->location);
                if(!empty($replayLocation) && (string)$replayLocation->lat == (string)$lat  && (string)$replayLocation->long == (string)$long ) {
                    $counter++;
                }
            }


            $titleQuestionnaire = $form->title;
            $questionsAll = $form->question_groups;
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

            return view('public.'.ONE::getEntityLayout().'.questionnaire.showAnswers',compact('form', 'questionnarieKey', 'counter', 'location','lat' , 'long','user','titleQuestionnaire','questionsAll','formKey','questionsDependencies','formPublic'));
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["questionaires.show" => $e->getMessage()]);
        }
    }


    public function downloadPdfAnswer($questionnarieKey,$formReplyKey){
        set_time_limit(0);

        try {
            $form = Questionnaire::getStatisticsByFormReply($questionnarieKey, $formReplyKey);

            $userKey = $form->formReply->created_by;

            if( $userKey != 'anonymous'){
                $user = Auth::getUserByKey($userKey);
            }else{
                $user = json_encode(['name' => trans('privateQuestionnairePDF.anonymous')]);
            }

            $formKey = $form->form_key;
            $formPublic = $form->public;
            $titleQuestionnaire = $form->title;
            $questionsAll = $form->question_groups;
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

            // return view('public.'.ONE::getEntityLayout().'.questionnaire.downloadPdf',compact('user','titleQuestionnaire','questionsAll','formKey','questionsDependencies','formPublic'));
            $pdf = PDF::loadView('public.'.ONE::getEntityLayout().'.questionnaire.downloadPdf',compact('user','titleQuestionnaire','questionsAll','formKey','questionsDependencies','formPublic'))->setPaper('a4')->setWarnings(false);
            return $pdf->download('questionnaire.pdf');
        }
        catch(Exception $e) {
            return $e->getMessage();
        }
    }

    public function getFormRepliesLocations($questionnarieKey){
        $formReplies = Questionnaire::getFormRepliesList($questionnarieKey);
        $data = [];
        foreach($formReplies as $formReply){
            $location = json_decode($formReply->location);
            if(!empty($location)) {
                $data[] = [ 'link' => action("PublicQController@showAnswers",[$questionnarieKey, $formReply->form_reply_key]),
                    'lat' => $location->lat,
                    'long' => $location->long,
                    'title' => $location->location,
                    'details_link' => action("PublicQController@showDetailsAnswers",[$questionnarieKey, $formReply->form_reply_key]),
                    'form_reply_key' => $formReply->form_reply_key];
            }
        }

        return response()->json($data);
    }


    public function showRepliesByGeoCode(Request $request, $questionnarieKey){
        $formReplies = Questionnaire::getFormRepliesList($questionnarieKey);

        $long = !empty($request->long) ? $request->long : "";
        $lat = !empty($request->lat) ? $request->lat : "";
        $location = !empty($request->location) ? $request->location : "";

        $data = [];
        foreach($formReplies as $formReply){
            $replayLocation = json_decode($formReply->location);
            if(!empty($replayLocation) && (string)$replayLocation->lat == (string)$lat  && (string)$replayLocation->long == (string)$long ) {
                $createdAt = explode(" ",$formReply->created_at);
                $createdBy = !empty($formReply->created_by) ? $formReply->created_by : "";
                /*
                $response = ONE::get([
                    'component' => 'q',
                    'api' => 'form',
                    'api_attribute' => $questionnarieKey,
                    'method' => 'statisticsByFormReply',
                    'attribute' => $formReply->form_reply_key
                ]);
                $userName = "";
                if ($response->statusCode() == 200) {
                    $form = $response->json();
                    $questionsAll = $form->question_groups;
                    foreach($questionsAll as $questionGroup){
                        if( last($questionsAll) == $questionGroup){
                            $userName = $questionGroup->questions[1]->reply;
                        }
                    }
                }
                */
                $data[] = [
                    'link' => action("PublicQController@showAnswers",[$questionnarieKey, $formReply->form_reply_key]),
                    'lat' => $lat,
                    'long' => $long,
                    'created_at' => $createdAt[0],
                    'title' => $location,
                    'created_by'=> $createdBy
                ];
            }
        }

        return view('public.'.ONE::getEntityLayout().'.questionnaire.showRepliesByGeoCode',compact('data', 'location','lat' , 'long'));
    }


    public function showDetailsAnswers($questionnarieKey,$formReplyKey)
    {
        set_time_limit(0);

        try {
                $form = Questionnaire::getStatisticsByFormReply($questionnarieKey, $formReplyKey);


                //  $userKey = $form->formReply->created_by;
                $user = json_encode(['name' => trans('privateQuestionnairePDF.anonymous')]);


                $formKey = $form->form_key;
                $formPublic = $form->public;
                $location = !empty($form->formReply->location) ? json_decode($form->formReply->location)->location : "";
                $lat = !empty($form->formReply->location) ? json_decode($form->formReply->location)->lat : "";
                $long = !empty($form->formReply->location) ? json_decode($form->formReply->location)->long : "";


                $titleQuestionnaire = $form->title;
                $questionsAll = $form->question_groups;
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

                // Count how many answers for a location
                $formReplies = Questionnaire::getFormRepliesList($questionnarieKey);
                $counter = 0;
                foreach($formReplies as $formReply){
                    $replayLocation = json_decode($formReply->location);
                    if(!empty($replayLocation) && (string)$replayLocation->lat == (string)$lat  && (string)$replayLocation->long == (string)$long ) {
                        $counter++;
                    }

                    if($formReply->form_reply_key == $formReplyKey){
                        $listId = $counter;
                    }

                }

                $link = action("PublicQController@showRepliesByGeoCode",[$questionnarieKey, "location" =>$location, "lat" => $lat, "long" => $long  ]);

                return view('public.'.ONE::getEntityLayout().'.questionnaire.showDetailsAnswers', compact('questionnarieKey', 'link','counter', 'listId' , 'location', 'lat' , 'long','user','titleQuestionnaire','questionsAll','formKey','questionsDependencies','formPublic'));
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["questionaires.show" => $e->getMessage()]);
        }
    }

    public function getQuestion(){


        $questionnaire = Questionnaire::getFormConstruction('Yk550n1ZtZ8GTDCwx8UI84xjufZYaTb7');
        $randomQuestionGroup = rand(0, count($questionnaire->question_groups) - 1);
        $randomQuestion = rand(0, count($questionnaire->question_groups[$randomQuestionGroup]->questions) - 1);


        $qkey = collect($questionnaire->question_groups[$randomQuestionGroup]->questions[$randomQuestion])['question_key'];

        $questionnaireTitle = $questionnaire->question_groups[$randomQuestionGroup]->title;
        $question = $questionnaire->question_groups[$randomQuestionGroup]->questions[$randomQuestion]->question;
        $questionKey = $qkey;
        $options = $questionnaire->question_groups[$randomQuestionGroup]->questions[$randomQuestion]->question_options;

        return view('public.'.ONE::getEntityLayout().'.home.quiz', compact('questionnaireTitle', 'question', 'questionKey', 'options'));
    }

    /**
     * @param Request $request
     * @return array
     */
    public function submitAnswer(Request $request){
        $question = Questionnaire::getQuestion($request->key);
        $correctOptions = explode(',', $question->correctOption);
        $userAnswers = explode(',', $request->selected);

        if(count($correctOptions) == count($userAnswers)){
            foreach($correctOptions as $option){
                if(!in_array($option, $userAnswers))
                    return ['correct' => false, 'description' => $question->description];
            }
            return ['correct' => true, 'description' => $question->description];
        }else{
            return ['correct' => false, 'description' => $question->description];
        }
    }


    /**
     * @param $questionnaireKey
     * @param $userKey
     * @param $uniqueKey
     * @return string
     */
    public function autoLoginQ($questionnaireKey, $userKey, $uniqueKey){
        try{
            $verification = EMPATIA::verifyUniqueKey($questionnaireKey, $userKey, $uniqueKey);

            $authToken = $verification->token;
            Session::put('X-AUTH-TOKEN', $authToken);

            $userInformation = $verification->user;
            Session::put('user', $userInformation);

            return redirect()->action('PublicQController@showQ', $questionnaireKey);

        } catch (Exception $e){
            return response()->redirectTo("/")->withErrors($e->getMessage());

        }
    }
}
