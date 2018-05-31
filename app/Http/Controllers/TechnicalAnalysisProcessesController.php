<?php

namespace App\Http\Controllers;

use App\ComModules\Auth;
use App\ComModules\CB;
use App\ComModules\Orchestrator;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Datatables;
use App\One\One;
use Session;

class TechnicalAnalysisProcessesController extends Controller
{
    /**
     * @param $type
     * @param $cbKey
     * @return TechnicalAnalysisProcessesController|\Illuminate\Http\RedirectResponse
     */
    public function showQuestions($type, $cbKey)
    {
        try {
            $data['type'] = $type;
            $data['cb'] = CB::getCbConfigurations($cbKey);
            $data['sidebar'] = 'padsType';
            $data['active'] = 'technicalAnalysisProcess';

            Session::put('sidebarArguments', ['type' => $type, 'cbKey' => $cbKey, 'activeFirstMenu' => 'technicalAnalysisProcess']);
            Session::put('sidebars', [0 => 'private', 1=> 'padsType']);

            $data['title'] = trans('privateSidebar.technical_analysis_process') . ' ' . (isset($data['cb']->title) ? $data['cb']->title : null);

            return view('private.cbs.questions', $data);

        } catch (Exception $e) {
            return redirect()->back()->withErrors(["cbs.getIndexTable" => $e->getMessage()]);
        }
    }

    /**
     * @param $type
     * @param $cbKey
     * @return TechnicalAnalysisProcessesController|\Illuminate\Http\RedirectResponse
     */
    public function create($type, $cbKey)
    {
        try {
            //Getting languages for translations
            $languages = Orchestrator::getLanguageList();
            $cb = CB::getCb($cbKey);
            $author = (Auth::getUser($cb->created_by))->name;
            $cb_title = $cb->title;
            $cb_start_date = $cb->start_date;

            $data = [];
            $data['title'] = trans('privateCbs.create_question');
            $data['type'] = $type;
            $data['cbKey'] = $cbKey;
            $data['author'] = $author;
            $data['cb_title'] = $cb_title;
            $data['cb_start_date'] = $cb_start_date;
            $data['languages'] = $languages;
            $data['sidebar'] = 'padsType';
            $data['active'] = 'technicalAnalysisProcess';

            return view('private.cbs.technicalAnalysisProcess.question', $data);
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["question.create" => $e->getMessage()]);
        }
    }

    /**
     * @param $type
     * @param $cbKey
     * @return $this
     */
    public function getIndexTable($type, $cbKey)
    {
        try {
            $questions = CB::getCbQuestions($cbKey);

            // in case of json
            $collection = Collection::make($questions);

            return Datatables::of($collection)
                ->editColumn('question', function ($question) use ($type, $cbKey) {
                    return "<a href='" . action('TechnicalAnalysisProcessesController@show', ['type' => $type, 'cbKey' => $cbKey, $question->tech_analysis_question_key]) . "'>" . $question->question . "</a>";
                })
                ->addColumn('action', function ($question) use ($type, $cbKey) {
                    return ONE::actionButtons(['type' => $type, 'cbKey' => $cbKey, $question->tech_analysis_question_key], ['form' => 'question', 'edit' => 'TechnicalAnalysisProcessesController@edit', 'delete' => 'TechnicalAnalysisProcessesController@delete']);
                })
                ->rawColumns(['question','action'])
                ->make(true);
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["cbs.getIndexTable" => $e->getMessage()]);
        }
    }

    /**
     * @param $type
     * @param $cbKey
     * @param $techAnalysisQuestionKey
     * @return $this|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show($type, $cbKey, $techAnalysisQuestionKey)
    {
        try {
            $cb = CB::getCb($cbKey);
            $author = (Auth::getUser($cb->created_by))->name;
            $cb_title = $cb->title;
            $cb_start_date = $cb->start_date;
            $question = CB::getCbQuestion($techAnalysisQuestionKey);

            $data = [];
            $data['author'] = $author;
            $data['cb_title'] = $cb_title;
            $data['cb_start_date'] = $cb_start_date;
            $data['cbKey'] = $cbKey;
            $data['type'] = $type;
            $data['title'] = trans('privateCbs.show_question');
            $data['sidebar'] = 'padsType';
            $data['active'] = 'technicalAnalysisProcess';
            $data['techAnalysisQuestionKey'] = $techAnalysisQuestionKey;
            $data['question'] = $question;

            return view('private.cbs.technicalAnalysisProcess.question', $data);
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["question.show" => $e->getMessage()]);
        }
    }

    /**
     * @param Request $request
     * @param $type
     * @param $cbKey
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function store(Request $request, $type, $cbKey)
    {
        try {
            $languages = Orchestrator::getLanguageList();
            //Translations
            $contentTranslation = [];
            foreach($languages as $language){
                if($request->input("question_" . $language->code) && ($request->input("question_" . $language->code) != '') || $request->input("required_question_" . $language->code) && ($request->input("required_question_" . $language->code) != '')) {
                    $contentTranslation[] = [
                        'language_code' => $language->code,
                        'question' => $language->default == true ? $request->input("required_question_" . $language->code) : $request->input("question_" . $language->code),
                    ];
                }
            }
            $question = CB::setCbQuestion($request, $contentTranslation,$cbKey);

            Session::flash('message', trans('question.created_ok'));
            return redirect()->action('TechnicalAnalysisProcessesController@show', ['type' => $type, 'cbKey' => $cbKey, 'techAnalysisQuestionKey' => $question->tech_analysis_question_key]);
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["question.error_on_store" => $e->getMessage()]);
        }
    }

    /**
     * @param $type
     * @param $cbKey
     * @param $techAnalysisQuestionKey
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function delete($type, $cbKey, $techAnalysisQuestionKey)
    {
        $data = array();

        $data['action'] = action("TechnicalAnalysisProcessesController@destroy", ['type' => $type, 'cbKey' => $cbKey, 'techAnalysisQuestionKey' => $techAnalysisQuestionKey]);
        $data['title'] = "DELETE";
        $data['msg'] = "Are you sure you want to delete this question?";
        $data['btn_ok'] = "Delete";
        $data['btn_ko'] = "Cancel";

        return view("_layouts.deleteModal", $data);
    }

    public function edit($type, $cbKey, $techAnalysisQuestionKey)
    {
        try {
            $cb = CB::getCb($cbKey);
            $author = (Auth::getUser($cb->created_by))->name;
            $cb_title = $cb->title;
            $cb_start_date = $cb->start_date;
            // Getting languages for translations
            $languages = Orchestrator::getLanguageList();
            $question = CB::editCbQuestion($techAnalysisQuestionKey);

            $data = [];
            $data['title'] = trans('privateCbs.update_question');
            $data['type'] = $type;
            $data['cbKey'] = $cbKey;
            $data['author'] = $author;
            $data['cb_title'] = $cb_title;
            $data['cb_start_date'] = $cb_start_date;
            $data['sidebar'] = 'padsType';
            $data['active'] = 'technicalAnalysisProcess';
            $data['languages'] = $languages;
            $data['question'] = $question;
            $data['techAnalysisQuestionKey'] = $techAnalysisQuestionKey;

            return view('private.cbs.technicalAnalysisProcess.question', $data);
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["question.edit" => $e->getMessage()]);
        }
    }

    /**
     * @param Request $request
     * @param $type
     * @param $cbKey
     * @param $techAnalysisQuestionKey
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $type, $cbKey, $techAnalysisQuestionKey)
    {
        try {
            $languages = Orchestrator::getLanguageList();
            //Translations
            $translations = [];
            foreach($languages as $language){
                if($request->input("question_" . $language->code) && ($request->input("question_" . $language->code) != '') || $request->input("required_question_" . $language->code) && ($request->input("required_question_" . $language->code) != '')) {
                    $translations[] = [
                        'language_code' => $language->code,
                        'question' => $language->default == true ? $request->input("required_question_" . $language->code) : $request->input("question_" . $language->code),
                    ];
                }
            }
            $data = [];
            $data['translations'] = $translations;
            $data['acceptable'] = $request->input('acceptable');

            $question = CB::updateQuestion($data, $techAnalysisQuestionKey);
            Session::flash('message', trans('question.updated_ok'));
            return redirect()->action('TechnicalAnalysisProcessesController@showQuestions', ['type' => $type, 'cbKey' => $cbKey, 'techAnalysisQuestionKey' => $question->tech_analysis_question_key]);
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["question.error_on_update" => $e->getMessage()]);
        }
    }

    /**
     * @param $type
     * @param $cbKey
     * @param $techAnalysisQuestionKey
     * @return $this|string
     */
    public function destroy($type, $cbKey, $techAnalysisQuestionKey)
    {
        try{
            CB::deleteQuestion($techAnalysisQuestionKey);
            Session::flash('message', trans('question.delete_ok'));
            return action('TechnicalAnalysisProcessesController@showQuestions', ['type' => $type,'cbKey' => $cbKey]);
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["question.destroy" => $e->getMessage()]);
        }
    }
}
