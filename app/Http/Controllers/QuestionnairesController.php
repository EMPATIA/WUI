<?php

namespace App\Http\Controllers;

use App\ComModules\Auth;
use App\ComModules\Orchestrator;
use App\ComModules\Questionnaire;
use App\Http\Requests\QuestionnaireRequest;
use Illuminate\Http\Request;
use App\One\One;
use Datatables;
use Session;
use URL;
use View;
use Breadcrumbs;
use Exception;
use Illuminate\Support\Collection;
use PDF;
use Chumper\Zipper\Facades\Zipper;
use Carbon\Carbon;
use File;

class QuestionnairesController extends Controller
{
    public function __construct()
    {

        View::share('title', trans('questionnaire.questionnaires'));


    }


    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $title = trans('privateQuestionnaires.questionnaires');
        return view('private.questionnaire.index', compact('title'));
    }


    /**
     * Create a new resource.
     *
     * @return Response
     */
    public function create()
    {
        if(Session::get('user_role') != 'admin'){
            return redirect()->back()->withErrors(["questionnaires.create" => trans('privateQuestionnaires.permission_message')]);
        }

        //Getting languages for translations
        $languages = Orchestrator::getLanguageList();

        $title = trans('privateQuestionnaires.create_questionnaire');
        return view('private.questionnaire.questionnaire', compact('title', 'languages'));
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
        if(Session::get('user_role') != 'admin'){
            return redirect()->back()->withErrors(["questionnaires.edit" => trans('privateQuestionnaires.permission_message')]);
        }
        try {
            // Getting languages for translations
            $languages = Orchestrator::getLanguageList();

            $response = Questionnaire::getForm($key);
            $questionnaire = $response;
            $questionnaireKey = $questionnaire->form_key;
            $translations = collect($questionnaire->translations)->keyBy('language_code')->toArray();

            Session::put('sidebarArguments', ['questionnaireKey' => $questionnaireKey, 'activeFirstMenu' => 'details']);

            $sidebar = 'q';
            $active = 'details';

            $title = trans('privateQuestionnaires.update_questionnaire').' '.(isset($questionnaire->title) ? $questionnaire->title: null);
            return view('private.questionnaire.questionnaire', compact('title', 'questionnaire','languages', 'translations', 'sidebar', 'active', 'questionnaireKey'));
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["questionnaire.show" => $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param $key
     * @internal param int $id
     * @return $this|View
     */
    public function show($key)
    {
        if(Session::get('user_role') != 'admin'){
            return redirect()->back()->withErrors(["questionnaires.show" => trans('privateQuestionnaires.permission_message')]);
        }

        try {
            $response = Questionnaire::getForm($key);
            $questionnaire = $response;

            $title = trans('privateQuestionnaires.show_questionnaire').' '.(isset($questionnaire->title) ? $questionnaire->title: null);

            $questionnaireKey = $questionnaire->form_key;

            Session::put('sidebarArguments', ['questionnaireKey' => $questionnaireKey, 'activeFirstMenu' => 'details']);

            $sidebar = 'q';
            $active = 'details';

            return view('private.questionnaire.questionnaire', compact('title', 'questionnaire', 'questionnaireKey', 'active', 'sidebar'));
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["questionnaire.show" => $e->getMessage()]);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param QuestionnaireRequest $request
     * @return $this|View
     * @internal param QuestionnaireRequest
     */
    public function store(Request $request)
    {
        if(Session::get('user_role') != 'admin'){
            return redirect()->back()->withErrors(["questionnaires.store" => trans('privateQuestionnaires.permission_message')]);
        }

        try {
            if (!empty($request->get("end_date")) && !Carbon::parse($request->get("end_date"))->gt(Carbon::parse($request->get("start_date"))))
                return redirect()->back()->withErrors([trans("privateQuestionnaires.end_date_must_be_greater_then_start_date")]);

            //Getting languages for translations
            $languages = Orchestrator::getLanguageList();
            $contentTranslation = [];

            foreach ($languages as $language){
                if($request->input("title_".$language->code) && ($request->input("title_".$language->code)!='') || $request->input("title_".$language->code) && ($request->input("title_".$language->code)!='')){
                    $contentTranslation[] = [
                        'title' => $language->default == true ? $request->input("title_".$language->code) : $request->input("title_".$language->code),
                        'description' =>$language->default == true ? $request->input("description_".$language->code) : $request->input("description_".$language->code),
                        'language_code' => $language->code
                    ];
                }
            }
            $response = Questionnaire::setNewForm($request,$contentTranslation);
            $questionnaire = $response;
            Session::flash('message', trans('questionnaire.store_ok'));
            return redirect()->action('QuestionnairesController@show', $questionnaire->form_key);
        }
        catch(Exception $e) {

            return redirect()->back()->withErrors(["questionnaire.store" => $e->getMessage()])->withInput();
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

        if(Session::get('user_role') != 'admin'){
            return redirect()->back()->withErrors(["questionnaires.update" => trans('privateQuestionnaires.permission_message')]);
        }

        try {
            if (!empty($request->get("end_date")) && !Carbon::parse($request->get("end_date"))->gt(Carbon::parse($request->get("start_date"))))
                return redirect()->back()->withErrors([trans("privateQuestionnaires.end_date_must_be_greater_then_start_date")]);
                
            $languages = Orchestrator::getLanguageList();

            $contentTranslation = [];
            foreach ($languages as $language){
                if($request->input("title_".$language->code) && ($request->input("title_".$language->code)!='') || $request->input("title_".$language->code) && ($request->input("title_".$language->code)!='')){
                    $contentTranslation[] = [
                        'title' => $language->default == true ? $request->input("title_".$language->code) : $request->input("title_".$language->code),
                        'description' =>$language->default == true ? $request->input("description_".$language->code) : $request->input("description_".$language->code),
                        'language_code' => $language->code
                    ];
                }
            }

            $response = Questionnaire::updateForm($key, $request, $contentTranslation);
            $questionnaire = $response;
            Session::flash('message', trans('questionnaire.update_ok'));

            return redirect()->action('QuestionnairesController@show', $questionnaire->form_key);
        }
        catch(Exception $e) {
            //TODO: save inputs
            return redirect()->back()->withErrors(["questionnaire.update" => $e->getMessage()]);
        }
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  $id
     * @return Response
     */
    public function destroy($key){
        if(Session::get('user_role') != 'admin'){
            return redirect()->back()->withErrors(["questionnaires.delete" => trans('privateQuestionnaires.permission_message')]);
        }

        try {
            Questionnaire::deleteForm($key);
            Session::flash('message', trans('questionnaire.delete_ok'));

            return action('QuestionnairesController@index');

        }
        catch(Exception $e) {
            //TODO: save inputs
            return redirect()->back()->withErrors(["questionnaire.destroy" => $e->getMessage()]);
        }
    }


    /**
     * Show confirm popup to remove the specified resource from storage.
     *
     * @param $key
     * @return View
     * @internal param $id
     */
    public function delete($key){
        $data = array();

        $data['action'] = action("QuestionnairesController@destroy", $key);
        $data['title'] = "DELETE";
        $data['msg'] = "Are you sure you want to delete this Content?";
        $data['btn_ok'] = "Delete";
        $data['btn_ko'] = "Cancel";

        return view("_layouts.deleteModal", $data);
    }


    /**
     *
     *
     * @return mixed
     */
    public function getIndexTable(){

        if(Session::get('user_role') == 'admin'){

            $list = Questionnaire::listForm();

            // in case of json
            $collection = Collection::make($list);

        }else
            $collection = Collection::make([]);

        $edit = Session::get('user_role') == 'admin';
        $delete = Session::get('user_role') == 'admin';

        return Datatables::of($collection)
            ->editColumn('key', function ($collection) {
                return "<a href='" . action('QuestionnairesController@show', $collection->form_key) . "'>" . $collection->form_key . "</a>";
            })
            ->editColumn('title', function ($collection) {
                return "<a href='" . action('QuestionnairesController@show', $collection->form_key) . "'>" . $collection->title . "</a>";
            })
            ->addColumn('action', function ($collection) use($edit, $delete){
                if($edit == true and $delete == true)
                    return ONE::actionButtons($collection->form_key, ['form' => 'questionnaire', 'edit' => 'QuestionnairesController@edit', 'delete' => 'QuestionnairesController@delete']);
                elseif($edit == false and $delete == true)
                    return ONE::actionButtons($collection->form_key, ['form' => 'questionnaire', 'delete' => 'QuestionnairesController@delete']);
                elseif($edit == true and $delete == false)
                    return ONE::actionButtons($collection->form_key, ['form' => 'questionnaire', 'edit' => 'QuestionnairesController@edit']);
                else
                    return null;
            })
            ->rawColumns(['key','title','action'])
            ->make(true);

    }


    /**
     *
     *
     * @return mixed
     */
    public function getTableUserAnswers($keyQuestionnaire){

        $list = Questionnaire::getFormRepliesList($keyQuestionnaire);

        $usersKeys = [];
        foreach ($list as $user) {
            if(!array_key_exists($user->created_by, $usersKeys) && $user->created_by != 'anonymous'){
                $usersKeys[] = $user->created_by;
            }
        }
        $usersNames = [];
        if (count($usersKeys) > 0)
            $usersNames = json_decode(json_encode(Auth::getUserNames($usersKeys)),true);

        // in case of json
        $collection = Collection::make($list);

        return Datatables::of($collection)
            ->editColumn('form_reply_key', function ($collection) use ($usersNames, $keyQuestionnaire) {
                return "<a class='btn btn-xs btn-flat btn-primary' href='".action('QuestionnairesController@downloadPdfAnswer', ['key' => $keyQuestionnaire , 'formReplyKey' => $collection->form_reply_key])."'><i class=\"fa fa-file-pdf-o\" aria-hidden=\"true\"></i></a>";
                // return "<input name='form_reply_key[]' value='".$collection->form_reply_key."' type='checkbox' class='input-checkbox-key text-center' onclick='checkIfExportIsAvailable();' >";
            })
            ->editColumn('name', function ($collection) use ($usersNames, $keyQuestionnaire) {
                return "<a href='". action('QuestionnaireAnswersController@show', ['key' => $keyQuestionnaire, 'formReplyKey' => $collection->form_reply_key]) ."'>" . (isset($usersNames[$collection->created_by]['name'])? $usersNames[$collection->created_by]['name'] : trans('questionaire.anounymous')) . "</a>";
            })
            ->editColumn('completed', function ($collection) use ($usersNames, $keyQuestionnaire) {
                return "<a href='". action('QuestionnaireAnswersController@show', ['key' => $keyQuestionnaire, 'formReplyKey' => $collection->form_reply_key]) ."'>" . (isset($collection->completed) ? $collection->completed : 'false') . "</a>";
            })
            ->addColumn('action', function ($collection) {
                return '';
            })
            ->rawColumns(['form_reply_key','name','completed','action'])
            ->make(true);

    }


    public function downloadPdf($key)
    {
        set_time_limit(0);

        try {

            $form = Questionnaire::getFormConstruction($key);

            if(!empty($form)){
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

                // return view('private.questionnaire.downloadPdf',compact('titleQuestionnaire','questionsAll','formKey','questionsDependencies','formPublic'));
                $pdf = PDF::loadView('private.questionnaire.downloadPdf',compact('titleQuestionnaire','questionsAll','formKey','questionsDependencies','formPublic'))
                    ->setPaper('a4')->setWarnings(false);

                return $pdf->download('questionnaire.pdf');

            }else{
                Session::put('redirect', 'public');

                Session::put('url_previous', URL::action('PublicQController@showQ',$key));

                return redirect()->action('AuthController@login');
            }
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["questionaires.show" => $e->getMessage()]);
        }
    }


    public function downloadPdfAnswer($questionnarieKey,$formReplyKey)
    {
        set_time_limit(0);

        try {
            $form = Questionnaire::getStatisticsByFormReply($questionnarieKey, $formReplyKey);

            if(!empty($form)){
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

                //return view('private.questionnaire.downloadPdf',compact('user','titleQuestionnaire','questionsAll','formKey','questionsDependencies','formPublic'));
                $pdf = PDF::loadView('private.questionnaire.downloadPdf',compact('user','titleQuestionnaire','questionsAll','formKey','questionsDependencies','formPublic'))->setPaper('a4')->setWarnings(false);

                return $pdf->download('questionnaire.pdf');

            }else{
                Session::put('redirect', 'public');

                Session::put('url_previous', URL::action('PublicQController@showQ',$questionnarieKey));

                return redirect()->action('AuthController@login');
            }
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["questionaires.show" => $e->getMessage()]);
        }
    }



    public function downloadPdfAnswerByForm($questionnaireKey)
    {
        set_time_limit(0);
        try {
            $form = Questionnaire::setStatisticsByForm($questionnaireKey);
            if(!empty($form)){
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

                $usersKeys = [];
                foreach ($form->formReplies as $key => $item) {
                    $answers = json_decode(json_encode($item),true);
                    $userKey = $answers["created_by"];

                    if(!array_key_exists($userKey, $usersKeys) && $userKey != 'anonymous'){
                        $usersKeys[] = $userKey;
                    }
                }

                $usersNames = [];
                if (count($usersKeys) > 0)
                    $usersNames = Auth::getPublicListNames($usersKeys);

                $path = storage_path()."/tmp";
                File::deleteDirectory($path);

                if(!File::exists($path)) {
                    // path does not exist
                    File::makeDirectory(storage_path()."/tmp/", 0777, true);
                }


                $folderPdf = storage_path().'/tmp/'."folder".Carbon::now()->toAtomString();
                File::makeDirectory($folderPdf, 0777, true);

                foreach($form->formReplies as $key => $item) {
                    // User
                    $answers = json_decode(json_encode($item),true);
                    $userKey = $answers["created_by"];
                    $user = !empty($usersNames[$userKey]["name"]) ? $usersNames[$userKey]["name"] : $userKey;

                    $pdf = PDF::loadView('private.questionnaire.downloadPdfByForm', compact('answers','user', 'titleQuestionnaire', 'questionsAll', 'formKey', 'questionsDependencies', 'formPublic'))->setPaper('a4')->setWarnings(false);
                    $pdf->save($folderPdf . '/questionnaireAnswers'.$key.'.pdf');
                }
                $yourfile = $folderPdf."/questionnaireAnswers".Carbon::now()->toAtomString().".zip";

                $files = glob($folderPdf.'*');
                Zipper::make($yourfile)->add($files)->close();

                // or however you get the path
                $file_name = basename($yourfile);
                header("Content-Type: application/zip");
                header("Content-Disposition: attachment; filename=$file_name");
                header("Content-Length: " . filesize($yourfile));
                readfile($yourfile);

                File::deleteDirectory($folderPdf);
                exit;

            }else{
                Session::put('redirect', 'public');

                Session::put('url_previous', URL::action('PublicQController@showQ', $questionnaireKey));

                return redirect()->action('AuthController@login');
            }

        } catch(Exception $e) {
        return redirect()->back()->withErrors(["questionaires.show" => $e->getMessage()]);
    }

}

public function showStatistics($key)
{
    try {

            $questionnaire = Questionnaire::getForm($key);
            $title = trans('privateQuestionnaires.show_statistics');

            $questionnaireKey = $questionnaire->form_key;

            Session::put('sidebarArguments', ['questionnaireKey' => $questionnaireKey, 'activeFirstMenu' => 'statistics']);

            $sidebar = 'q';
            $active = 'statistics';

            return view('private.questionnaire.statistic', compact('title', 'questionnaire', 'questionnaireKey', 'sidebar', 'active'));

    }
    catch(Exception $e) {
        return redirect()->back()->withErrors(["statistic.show" => $e->getMessage()]);
    }

}

}
