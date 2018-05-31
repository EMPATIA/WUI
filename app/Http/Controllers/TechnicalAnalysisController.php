<?php

namespace App\Http\Controllers;

use App\ComModules\Auth;
use App\ComModules\CB;
use App\ComModules\Files;
use App\ComModules\Orchestrator;
use App\One\One;
use Exception;
use Illuminate\Http\Request;
use Session;
use Datatables;

class TechnicalAnalysisController extends Controller
{

    /**
     * This method verifies if Topic has a Technical Analysis.If there isn't a TA
     * show an "empty" view. If TA exists then show the Technical Analysis with its data
     *
     * @param $type
     * @param $cbKey
     * @param $topicKey
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function verifyIfExistsTechnicalAnalysis($type, $cbKey, $topicKey)
    {
        try {
            $topic = CB::getVerificationIfTechnicalAnalysisExist($topicKey, true);
            $technicalAnalysis = empty($topic->technical_analysis) ? null : $topic->technical_analysis;

            if(!empty($technicalAnalysis)){
                return redirect()->action('TechnicalAnalysisController@show', ["type"=>$type,"cbKey"=>$cbKey,"topicKey"=>$topicKey]);
            } else {
                return redirect()->action('TechnicalAnalysisController@create', ["type"=>$type,"cbKey"=>$cbKey,"topicKey"=>$topicKey]);
            }
        } catch (Exception $e) {
            return redirect()->back()->withErrors([trans("privateTechnicalAnalysis.error_on_verify") => $e->getMessage()]);
        }
    }

    /**
     * The method create prepares to show the editing view, getting the existing Tech Analysis
     * Questions to present them on the view. It verifies if between creation steps
     * Technical Analysis was created. If so return to Technical Analysis show view.
     *
     * @param $type
     * @param $cbKey
     * @param $topicKey
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View|string
     */
    public function create($type, $cbKey, $topicKey){

        try {
            /* Response is false if there was created a Tech Analysis between stages and come with the questions */
            $technicalAnalysisQuestions = CB::getQuestionsAndExistenceOfTechnicalAnalysis($cbKey,$topicKey, true);

            //            TOPIC RELATED DATA - BEGIN
            $data['topic'] = CB::getTopicParameters($topicKey);

            $data['topic_author'] = (Auth::getUser($data['topic']->created_by))->name;
            $data['topicParameters'] = [];

            foreach ($data['topic']->parameters as $param) {
                $data['topicParameters'][$param->id] = $param;
            }
            if(!($relatedParameter = collect($data['topic']->parameters)->where('code','=','associated_topics'))->isEmpty()){

                $data['relatedParameter'] = json_decode($relatedParameter->first()->pivot->value);
                $data['relatedParameter']->id = $relatedParameter->first()->id;
                $topics = CB::getTopicsByParentKey($topicKey);
                $data['relatedParameter']->fetchedTopics = $topics;
            }

            $data['post'] = $data['topic']->first_post;
            $CbParameters = CB::getCbParametersOptions($cbKey)->parameters;

            if(!isset($data['relatedParameter'])){
                if(!empty($relatedParameter = collect($CbParameters)->where('code','=','associated_topics'))){
                    $data['relatedParameter'] = $relatedParameter;
                }
            }

            // Request configurations
            $topicData = CB::getTopicDataWithChilds($topicKey);

            $data['configurations'] = $topicData->configurations;

            // Check Access
            if( !CB::checkCBsOption($data['configurations'], 'PUBLIC-ACCESS') && !ONE::isAuth() ){
                return redirect()->action('AuthController@login');
            }

            $allowFiles = [];
            if( CB::checkCBsOption($data['configurations'], 'ALLOW-FILES') ){
                $allowFiles[] = "docs";
            }

            if( CB::checkCBsOption($data['configurations'], 'ALLOW-PICTURES')  ){
                $allowFiles[] = "images";
            }

            $fileId = 0;
            $posX = "";
            $posY = "";
            $data['parameters'] = [];
            foreach ($CbParameters as $parameter) {

                $name = $parameter->parameter;
                $code = $parameter->type->code;

                if( isset($data['topicParameters'][$parameter->id]))
                    $value = $data['topicParameters'][$parameter->id]->pivot->value;
                else
                    $value = "";

                $parameterOptions = [];
                $options = $parameter->options;
                foreach ($options as $option) {
                    $parameterOptions[$option->id] = $option->label;
                }

                $data['parameters'][$name] = array('id' => $parameter->id, 'value' => $value, 'name' => $name, 'code' => $code, 'options' => $parameterOptions,'mandatory' => $parameter->mandatory);

                /* check if is image */
                if ($parameter->code == 'image_map') {
                    $fileId = $parameter->value;

                    if (count($value) > 0) {
                        $coordinates = explode("-", $value);

                        if (count($coordinates) == 2) {
                            if(strlen($coordinates[0]) > 0 && strlen($coordinates[1])){
                                $posX = $coordinates[0];
                                $posY = $coordinates[1];
                            }
                        }
                    }
                }
            }

            $data['fileCode'] = '';
            if ($fileId != 0) {
                $file = Files::getFile($fileId);
                $data['fileCode'] = $file->code;
            }

            $data['filesByType'] = [];
            $data['filesByType'] = CB::listFilesByType($data['topic']->first_post->post_key);

            try{
                $data['user']  = Auth::getUserByKey($data['topic']->created_by);
            } catch (Exception $e){
                $data['user']  = null;
            }

            $cb = CB::getCb($cbKey);
            $data['cbAuthor'] = Auth::getUser($cb->created_by);
            $data['cb_title'] = $cb->title;
            $data['cb_start_date'] = $cb->start_date;

//            $cb = CB::getCbConfigurations($cbKey);
            $statusAvailable = CB::getStatusTypes();
            $data['statusTypes']  = [];
            foreach ($statusAvailable as $status){
                $data['statusTypes'] [$status->code] = $status->name;
            }
//            TOPIC RELATED DATA - END

            if(!$technicalAnalysisQuestions->technicalAnalysisExists){              /* If TA doesn't exist forward to store */
                $data['type'] = $type;
                $data['cbKey'] = $cbKey;
                $data['topicKey'] = $topicKey;
                $data['technicalAnalysisQuestions'] = $technicalAnalysisQuestions->technicalAnalysisQuestions;
                $data['sidebar']  = 'topics';
                $data['active']   = 'technicalAnalysis';

                return view('private.topics.technicalAnalysis.technicalAnalysis', $data);
            }

            /* Technical Analysis was created while stayed on this route, therefore go forward to show the Tech Analysis */
            return redirect()->action('TechnicalAnalysisController@show', ['type' => $type, 'cbKey' => $cbKey, 'topicKey' => $topicKey]);

        } catch(Exception $e) {
            return redirect()->back()->withErrors([trans("privateTechnicalAnalysis.error_on_create") => $e->getMessage()]);
        }

    }

    /**
     * This method send values to be store in CB module. This values are questions
     * respective answers (send with question key and with the answer value) and
     * the content of the 4 details of Technical Analysis.
     *
     * @param Request $request
     * @param $type
     * @param $cbKey
     * @param $topicKey
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function store(Request $request, $type, $cbKey, $topicKey){

        try {
            /* Build the Technical Analysis Questions with the Answers (get the questions by finding the request field with question_, removing  */
            /* it and get the question key, the answer to that question is attributed by =>) to properly send to ComModules for treat the data   */
            $technicalAnalysisQuestionsAndAnswers = null;
            foreach($request->all() as $key => $value){
                if(str_contains($key, 'question_'))
                    $technicalAnalysisQuestionsAndAnswers[str_replace('question_', '', $key)] = $value;
            }

            $response = CB::createTechnicalAnalysis($request,$topicKey,$technicalAnalysisQuestionsAndAnswers);

            Session::flash('message', trans('technicalAnalysis.created_ok'));
            if (One::getEntityKey()=="bFHWY3wSIlHEvkM9mC6loE8WWUgMqD4Z")
                return redirect()->action("TechnicalAnalysisController@show",["type"=>$type,"cbKey"=>$cbKey,"topicKey"=>$topicKey]);
            else
                return view('private.topics.technicalAnalysis.technicalAnalysisNotifications', ['type' => $type, 'cbKey' => $cbKey, 'topicKey' => $topicKey, 'technicalAnalysisKey' => $response->technical_analysis_key]);
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["privateTechnicalAnalysis.error_on_store" => $e->getMessage()])->withInput();
        }

    }

    /**
     * This method gets a Technical Analysis with this $topicKey and all the questions
     * of this CB (with $cbKey). The received object is a Technical Analysis that comes
     * with an array of Questions and each Question comes with the respective Answer
     * (from this Technical Analysis) and without an Answer. Also comes with topic title
     * and with an array with version and date of creation of this TA so it will show on
     * a versions dropdown
     *
     * @param $type
     * @param $cbKey
     * @param $topicKey
     * @param null $version
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show($type, $cbKey, $topicKey, $version = null) {
        try{
            $data = [];
            $technicalAnalysis = CB::getTechnicalAnalysis($topicKey,$cbKey,$version); /* get TA with all question and TA respective answers. Receive also topic title*/
            $technicalAnalysisActive = $technicalAnalysis->technicalAnalysisActive;

            foreach($technicalAnalysis->technicalAnalysisVersionData as $technicalAnalysisVersion){
                if(!$technicalAnalysisVersion->active){
                    $marker = '';
                }else{
                    $marker = '* ';
                }
                $technicalAnalysisVersions[$technicalAnalysisVersion->version] =  $marker.'v'.$technicalAnalysisVersion->version.' '.$technicalAnalysisVersion->created_at;
            }

//            TOPIC RELATED DATA - BEGIN
            $data['topic'] = CB::getTopicParameters($topicKey);

            $data['topic_author'] = (Auth::getUser($data['topic']->created_by))->name;
            $data['topicParameters'] = [];

            foreach ($data['topic']->parameters as $param) {
                $data['topicParameters'][$param->id] = $param;
            }
            if(!($relatedParameter = collect($data['topic']->parameters)->where('code','=','associated_topics'))->isEmpty()){

                $data['relatedParameter'] = json_decode($relatedParameter->first()->pivot->value);
                $data['relatedParameter']->id = $relatedParameter->first()->id;
                $topics = CB::getTopicsByParentKey($topicKey);
                $data['relatedParameter']->fetchedTopics = $topics;
            }

            $data['post'] = $data['topic']->first_post;
            $CbParameters = CB::getCbParametersOptions($cbKey)->parameters;

            if(!isset($data['relatedParameter'])){
                if(!empty($relatedParameter = collect($CbParameters)->where('code','=','associated_topics'))){
                    $data['relatedParameter'] = $relatedParameter;
                }
            }

            // Request configurations
            $topicData = CB::getTopicDataWithChilds($topicKey);

            $data['configurations'] = $topicData->configurations;

            // Check Access
            if( !CB::checkCBsOption($data['configurations'], 'PUBLIC-ACCESS') && !ONE::isAuth() ){
                return redirect()->action('AuthController@login');
            }

            $allowFiles = [];
            if( CB::checkCBsOption($data['configurations'], 'ALLOW-FILES') ){
                $allowFiles[] = "docs";
            }

            if( CB::checkCBsOption($data['configurations'], 'ALLOW-PICTURES')  ){
                $allowFiles[] = "images";
            }

            $fileId = 0;
            $posX = "";
            $posY = "";
            $data['parameters'] = [];
            foreach ($CbParameters as $parameter) {

                $name = $parameter->parameter;
                $code = $parameter->type->code;

                if( isset($data['topicParameters'][$parameter->id]))
                    $value = $data['topicParameters'][$parameter->id]->pivot->value;
                else
                    $value = "";

                $parameterOptions = [];
                $options = $parameter->options;
                foreach ($options as $option) {
                    $parameterOptions[$option->id] = $option->label;
                }

                $data['parameters'][$name] = array('id' => $parameter->id, 'value' => $value, 'name' => $name, 'code' => $code, 'options' => $parameterOptions,'mandatory' => $parameter->mandatory);

                /* check if is image */
                if ($parameter->code == 'image_map') {
                    $fileId = $parameter->value;

                    if (count($value) > 0) {
                        $coordinates = explode("-", $value);

                        if (count($coordinates) == 2) {
                            if(strlen($coordinates[0]) > 0 && strlen($coordinates[1])){
                                $posX = $coordinates[0];
                                $posY = $coordinates[1];
                            }
                        }
                    }
                }
            }

            $data['fileCode'] = '';
            if ($fileId != 0) {
                $file = Files::getFile($fileId);
                $data['fileCode'] = $file->code;
            }

            $data['filesByType'] = [];
            $data['filesByType'] = CB::listFilesByType($data['topic']->first_post->post_key);

            try{
                $data['user']  = Auth::getUserByKey($data['topic']->created_by);
            } catch (Exception $e){
                $data['user']  = null;
            }

            $cb = CB::getCb($cbKey);
            $data['cbAuthor'] = Auth::getUserByKey($cb->created_by);
            $data['cb_title'] = $cb->title;
            $data['cb_start_date'] = $cb->start_date;

//            $cb = CB::getCbConfigurations($cbKey);
            $statusAvailable = CB::getStatusTypes();
            $data['statusTypes']  = [];
            foreach ($statusAvailable as $status){
                $data['statusTypes'] [$status->code] = $status->name;
            }
//            TOPIC RELATED DATA - END

            $actionUrl = action('TechnicalAnalysisController@show', ['type' => $type, 'cbKey' => $cbKey, 'topicKey' => $topicKey]);

            $data['actionUrl']                     = $actionUrl;
            $data['type']                          = $type;
            $data['cbKey']                         = $cbKey;
            $data['topicKey']                      = $topicKey;
            $data['technicalAnalysis']             = $technicalAnalysisActive;
            $data['technicalAnalysisQuestions']    = $technicalAnalysisActive->technical_analysis_questions;
            $data['technicalAnalysisVersions']     = $technicalAnalysisVersions ?? [];
            $data['sidebar']                       = 'topics';
            $data['active']                        = 'technicalAnalysis';
            $data['updated_by']                    = Auth::getUserByKey($technicalAnalysis->technicalAnalysisActive->updated_by);

            return view('private.topics.technicalAnalysis.technicalAnalysis', $data);

        } catch (Exception $e){
            return redirect()->back()->withErrors(["privateTechnicalAnalysis.error_on_update" => $e->getMessage()]);
        }
    }

    /**
     * This method gets a Technical Analysis with this $topicKey and all the questions
     * of this CB (with $cbKey). The received object is a Technical Analysis that comes
     * with an array of Questions and each Question comes with the respective Answer
     * (from this Technical Analysis) and without an Answer. Also comes with topic title.
     *
     * @param $type
     * @param $cbKey
     * @param $topicKey
     * @param $version
     * @return string
     */
    public function edit($type, $cbKey, $topicKey,$version){

        try {
            $technicalAnalysis = CB::getTechnicalAnalysis($topicKey, $cbKey,$version); /* get TA with all question and TA respective answers. Receive also topic title*/

            $technicalAnalysisActive = $technicalAnalysis->technicalAnalysisActive;

            //            TOPIC RELATED DATA - BEGIN
            $data['topic'] = CB::getTopicParameters($topicKey);

            $data['topic_author'] = (Auth::getUser($data['topic']->created_by))->name;
            $data['topicParameters'] = [];

            foreach ($data['topic']->parameters as $param) {
                $data['topicParameters'][$param->id] = $param;
            }
            if(!($relatedParameter = collect($data['topic']->parameters)->where('code','=','associated_topics'))->isEmpty()){

                $data['relatedParameter'] = json_decode($relatedParameter->first()->pivot->value);
                $data['relatedParameter']->id = $relatedParameter->first()->id;
                $topics = CB::getTopicsByParentKey($topicKey);
                $data['relatedParameter']->fetchedTopics = $topics;
            }

            $data['post'] = $data['topic']->first_post;
            $CbParameters = CB::getCbParametersOptions($cbKey)->parameters;

            if(!isset($data['relatedParameter'])){
                if(!empty($relatedParameter = collect($CbParameters)->where('code','=','associated_topics'))){
                    $data['relatedParameter'] = $relatedParameter;
                }
            }

            // Request configurations
            $topicData = CB::getTopicDataWithChilds($topicKey);

            $data['configurations'] = $topicData->configurations;

            // Check Access
            if( !CB::checkCBsOption($data['configurations'], 'PUBLIC-ACCESS') && !ONE::isAuth() ){
                return redirect()->action('AuthController@login');
            }

            $allowFiles = [];
            if( CB::checkCBsOption($data['configurations'], 'ALLOW-FILES') ){
                $allowFiles[] = "docs";
            }

            if( CB::checkCBsOption($data['configurations'], 'ALLOW-PICTURES')  ){
                $allowFiles[] = "images";
            }

            $fileId = 0;
            $posX = "";
            $posY = "";
            $data['parameters'] = [];
            foreach ($CbParameters as $parameter) {

                $name = $parameter->parameter;
                $code = $parameter->type->code;

                if( isset($data['topicParameters'][$parameter->id]))
                    $value = $data['topicParameters'][$parameter->id]->pivot->value;
                else
                    $value = "";

                $parameterOptions = [];
                $options = $parameter->options;
                foreach ($options as $option) {
                    $parameterOptions[$option->id] = $option->label;
                }

                $data['parameters'][$name] = array('id' => $parameter->id, 'value' => $value, 'name' => $name, 'code' => $code, 'options' => $parameterOptions,'mandatory' => $parameter->mandatory);

                /* check if is image */
                if ($parameter->code == 'image_map') {
                    $fileId = $parameter->value;

                    if (count($value) > 0) {
                        $coordinates = explode("-", $value);

                        if (count($coordinates) == 2) {
                            if(strlen($coordinates[0]) > 0 && strlen($coordinates[1])){
                                $posX = $coordinates[0];
                                $posY = $coordinates[1];
                            }
                        }
                    }
                }
            }

            $data['fileCode'] = '';
            if ($fileId != 0) {
                $file = Files::getFile($fileId);
                $data['fileCode'] = $file->code;
            }

            $data['filesByType'] = [];
            $data['filesByType'] = CB::listFilesByType($data['topic']->first_post->post_key);

            try{
                $data['user']  = Auth::getUserByKey($data['topic']->created_by);
            } catch (Exception $e){
                $data['user']  = null;
            }

            $cb = CB::getCb($cbKey);
            $data['cbAuthor'] = Auth::getUser($cb->created_by);
            $data['cb_title'] = $cb->title;
            $data['cb_start_date'] = $cb->start_date;

//            $cb = CB::getCbConfigurations($cbKey);
            $statusAvailable = CB::getStatusTypes();
            $data['statusTypes']  = [];
            foreach ($statusAvailable as $status){
                $data['statusTypes'] [$status->code] = $status->name;
            }
//            TOPIC RELATED DATA - END

            $data ['type'] = $type;
            $data ['cbKey'] = $cbKey;
            $data ['topicKey'] = $topicKey;
            $data ['technicalAnalysis'] = $technicalAnalysisActive;
            $data ['technicalAnalysisQuestions'] = $technicalAnalysisActive->technical_analysis_questions;
            $data ['sidebar']  = 'topics';
            $data ['active']   = 'technicalAnalysis';

            return view('private.topics.technicalAnalysis.technicalAnalysis', $data);
        } catch (Exception $e){
            return redirect()->back()->withErrors(["privateTechnicalAnalysis.error_on_edit" => $e->getMessage()]);
        }
    }

    /**
     * This method send values to be updated in CB module. These values are questions
     * respective answers (send with question key and with the answer value) and
     * the content of the 4 details of Technical Analysis. This update will work
     * on CB almost equal to a create because each time this is updated a new
     * version will be created.
     *
     * @param Request $request
     * @param $type
     * @param $cbKey
     * @param $topicKey
     * @param $version
     * @return \Illuminate\Http\RedirectResponse
     * @internal param $technicalAnalysisKey
     */
    public function update(Request $request, $type, $cbKey, $topicKey, $version)
    {
        /* receive Technical Analysis attributes: impact,budget,execution,sustainability and questions and answers from $request*/
        try {
            /* Build the Technical Analysis Questions with the Answers (get the questions by finding the request field with question_, removing  */
            /* it and get the question key, the answer to that question is attributed by =>) to properly send to ComModules for treat the data   */
            $technicalAnalysisQuestionsAndAnswers = null;
            foreach($request->all() as $key => $value){
                if(str_contains($key, 'question_'))
                    $technicalAnalysisQuestionsAndAnswers[str_replace('question_', '', $key)] = $value;
            }

            $technicalAnalysisUpdated = CB::updateTechnicalAnalysis($request, $topicKey, $version, $technicalAnalysisQuestionsAndAnswers);   /* send new values to CB to update them there */

            Session::flash('message', trans('technicalAnalysis.update_ok'));

            if (One::getEntityKey()=="bFHWY3wSIlHEvkM9mC6loE8WWUgMqD4Z")
                return redirect()->action("TechnicalAnalysisController@show",["type"=>$type,"cbKey"=>$cbKey,"topicKey"=>$topicKey]);
            else
                return view('private.topics.technicalAnalysis.technicalAnalysisNotifications', ['type' => $type, 'cbKey' => $cbKey, 'topicKey' => $topicKey, 'technicalAnalysisKey' => $technicalAnalysisUpdated->technical_analysis_key, 'version' => $technicalAnalysisUpdated->version]);

        } catch (Exception $e) {
            return redirect()->back()->withErrors(["privateTechnicalAnalysis.error_on_update" => $e->getMessage()])->withInput();
        }
    }

    /**
     * This method prepare the data for window of delete confirmation on the screen.
     * If choose delete then go forward to destroy
     *
     * @param $type
     * @param $cbKey
     * @param $topicKey
     * @param $version
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function delete($type, $cbKey, $topicKey, $version){

        $data = array();

        $data['action'] = action("TechnicalAnalysisController@destroy", ['type' => $type, 'cbKey' => $cbKey, 'topicKey' => $topicKey, 'version' => $version]);
        $data['title'] = "DELETE";
        $data['msg'] = "Are you sure you want to delete this Technical Analysis?";
        $data['btn_ok'] = "Delete";
        $data['btn_ko'] = "Cancel";

        return view("_layouts.deleteModal", $data);
    }

    /**
     * Send the Topic key to safe delete the Technical Analysis on CB
     * and its correspondent technical analysis (with topic key)
     *
     * @param $type
     * @param $cbKey
     * @param $topicKey
     * @param $version
     * @return string
     */
    public function destroy($type, $cbKey, $topicKey,$version){
        try {
            CB::destroyTechnicalAnalysis($topicKey);
            return action('TopicController@show', ['type' => $type, 'cbKey' => $cbKey, 'topicKey' => $topicKey]);

        } catch (Exception $e){
            return redirect()->back()->withErrors(["privateTechnicalAnalysis.error_on_delete" => $e->getMessage()])->getTargetUrl();
        }

    }

    /**
     * This method come from a click button to activate the version
     * that is shown on the view. Here is send topic key and version
     * to CB so to know each TA are we dealing with and which version
     * needs to be activated. At this point it returns showing the
     * latest added version
     *
     * @param $type
     * @param $cbKey
     * @param $topicKey
     * @param $version
     * @return string
     */
    public function activateVersion($type, $cbKey, $topicKey, $version = null){

        try {
            CB::activateTechnicalAnalysis($topicKey, $version);

            Session::flash('message', trans('technicalAnalysis.update_ok'));

            return redirect()->action('TechnicalAnalysisController@show', ['type' => $type, 'cbKey' => $cbKey, 'topicKey' => $topicKey , 'version' => $version]);
        } catch (Exception $e){
            return redirect()->back()->withErrors(["privateTechnicalAnalysis.error_on_delete" => $e->getMessage()])->getTargetUrl();
        }

    }

    public function entityGroupsTable(){
        $entityGroups = collect(Orchestrator::getEntityGroups());

        return Datatables::of($entityGroups)
            ->editColumn('select_groups', function ($collection) {
                return '<input class="group_key" type="checkbox" value="'.$collection->entity_group_key.'" id="'.$collection->entity_group_key.'"/>';
            })
            ->addColumn('name', function ($collection) {
                return $collection->name;
            })
            ->rawColumns(['select_groups'])
            ->make(true);
    }

    public function entityManagersTable(){
        $managers = collect(Orchestrator::getEntityManagers());

        return Datatables::of($managers)
            ->editColumn('select_managers', function ($collection) {
                return '<input class="user_key" type="checkbox" value="'.$collection->user_key.'" id="'.$collection->user_key.'"/>';
            })
            ->addColumn('name', function ($collection) {
                return $collection->name;
            })
            ->rawColumns(['select_managers'])
            ->make(true);

    }

    public function sendNotification(Request $request, $type, $cbKey, $topicKey, $technicalAnalysisKey){
        $site = Orchestrator::getSite(Session::get('X-SITE-KEY'));

        $user = Session::get('user');

        $response = CB::sendTechnicalAnalysisNotification($request, $technicalAnalysisKey, $site, $user->user_key);

        if($response == 'Ok'){
            Session::flash('message', trans('technicalAnalysis.send_notification_ok'));
        }
        else{
            Session::flash('message', trans('technicalAnalysis.send_notification_not_ok'));
        }
        return redirect()->action('TechnicalAnalysisController@show', ['type' => $type, 'cbKey' => $cbKey, 'topicKey' => $topicKey]);
    }

}
