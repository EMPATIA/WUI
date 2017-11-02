<?php

namespace App\Http\Controllers;

use App\ComModules\Analytics;
use App\ComModules\Auth;
use App\ComModules\CB;
use App\ComModules\Files;
use App\ComModules\Notify;
use App\ComModules\Orchestrator;
use App\Http\Requests\TopicRequest;
use App\Http\Requests\UserRequest;
use Carbon\Carbon;
use Chumper\Zipper\Zipper;
use Breadcrumbs;
use Datatables;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Facades\Excel;
use ONE;
use PDF;
use Session;
use URL;
use View;
use Illuminate\Support\Facades\Route;

class TopicController extends Controller
{

    public function __construct()
    {

    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {

    }

    /**
     * Create a new resource.
     *
     * @param $type
     * @param $cbKey
     * @return View
     */
    public function create($type, $cbKey)
    {
        try {
            // Get CB parameters
            $CbParameters = CB::getCbParametersOptions($cbKey)->parameters;

            if(!empty($relatedParameter = collect($CbParameters)->where('code','=','associated_topics'))){
                $data['relatedParameter'] = $relatedParameter;

            }
            $fileId = 0;
            $parameters = [];
            $fileCode = '';
            foreach ($CbParameters as $parameter) {
                $name = $parameter->parameter;
                $code = $parameter->type->code;

                $parameterOptions = [];
                $options = $parameter->options;
                foreach ($options as $option) {
                    $parameterOptions[$option->id] = $option->label;
                }
                $parameters[$name] = array('id' => $parameter->id, 'name' => $name, 'code' => $code, 'options' => $parameterOptions,'mandatory' => $parameter->mandatory,'description' => $parameter->description);

                /* check if is image */
                if ($parameter->code == 'image_map') {
                    $fileId = $parameter->value;
                }

                if ($fileId != 0) {
                    $file = Files::getFile($fileId);
                    $fileCode = $file->code;
                }
            }
            $status=CB::getStatusTypes();
            $cb = CB::getCb($cbKey);
            $configurations = collect($cb->configurations)->pluck('code')->toArray();

            $allowFiles = [];
            if( CB::checkCBsOption($configurations, 'ALLOW-FILES') ){
                $allowFiles[] = "docs";
            }

            if( CB::checkCBsOption($configurations, 'ALLOW-PICTURES')  ){
                $allowFiles[] = "images";
            }

            if(Session::has('filesToUpload')){
                Session::forget('filesToUpload');
            }


            $author = Auth::getUserByKey($cb->created_by);
            $cb_title = $cb->title;
            $cb_start_date = $cb->start_date;

            $data['title']          = trans('privateTopics.create_topic');
            $data['parameters']     = $parameters;
            $data['type']           = $type;
            $data['cbKey']          = $cbKey;
            $data['fileId']         = $fileId;
            $data['fileCode']       = $fileCode;
            $data['configurations'] = $configurations;
            $data['allowFiles']     = $allowFiles;
            $data['uploadKey']      = Files::getUploadKey();
            $data['sidebar']            = 'padsType';
            $data['active']             = 'topics';
            $data['cbAuthor']             = $author;
            $data['cb_title']           = $cb_title;
            $data['cb_start_date']      = $cb_start_date;
            $data['status']         =$status;

            return view('private.topics.topic', $data);
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["topic.create" => $e->getMessage()]);
        }
    }


    public function createWithUser($type,$cbKey) {
        try {
            $dataToView = array();

            /* Get User Creation Data */
            /* Get list of entities */
            $entities = [];
            $object = Orchestrator::getEntities();

            foreach($object as $entity){
                $entities[$entity->entity_key] = $entity->name;
            }

            // User Parameters
            $registerParametersResponse = Orchestrator::getEntityRegisterParameters();

            //verify user parameters with responses
            $registerParameters = [];
            foreach ($registerParametersResponse as $parameter){
                $parameterOptions = [];

                $file = null;
                if($parameter->parameter_type->code == 'radio_buttons' || $parameter->parameter_type->code == 'check_box' || $parameter->parameter_type->code == 'dropdown') {
                    foreach ($parameter->parameter_user_options as $option) {
                        $parameterOptions [] = [
                            'parameter_user_option_key' => $option->parameter_user_option_key,
                            'name' => $option->name
                        ];
                    }
                }
                $registerParameters []= [
                    'parameter_user_type_key'   => $parameter->parameter_user_type_key,
                    'parameter_type_code'       => $parameter->parameter_type->code,
                    'name'                      => $parameter->name,
                    'mandatory'                 => $parameter->mandatory,
                    'parameter_user_options'    => $parameterOptions
                ];
            }

            $dataToView["entities"] = $entities;
            $dataToView["registerParameters"] = $registerParameters;
            /* End of User Creation Data */
            /* ------------------------------------------------------------------------------------------------------ */
            /* Get Topic Creation data */
            // Get CB parameters
            $CbParameters = CB::getCbParametersOptions($cbKey)->parameters;

            $fileId = 0;
            $parameters = [];
            $fileCode = '';
            foreach ($CbParameters as $parameter) {
                $name = $parameter->parameter;
                $code = $parameter->type->code;

                $parameterOptions = [];
                $options = $parameter->options;
                foreach ($options as $option) {
                    $parameterOptions[$option->id] = $option->label;
                }
                $parameters[$name] = array('id' => $parameter->id, 'name' => $name, 'code' => $code, 'options' => $parameterOptions,'mandatory' => $parameter->mandatory,'description' => $parameter->description);

                /* check if is image */
                if ($parameter->code == 'image_map') {
                    $fileId = $parameter->value;
                }

                if ($fileId != 0) {
                    $file = Files::getFile($fileId);
                    $fileCode = $file->code;
                }
            }
            $status=CB::getStatusTypes();
            $cb = CB::getCb($cbKey);
            $configurations = collect($cb->configurations)->pluck('code')->toArray();

            $allowFiles = [];
            if( CB::checkCBsOption($configurations, 'ALLOW-FILES') ){
                $allowFiles[] = "docs";
            }

            if( CB::checkCBsOption($configurations, 'ALLOW-PICTURES')  ){
                $allowFiles[] = "images";
            }

            if(Session::has('filesToUpload')){
                Session::forget('filesToUpload');
            }

            $author = Auth::getUserByKey($cb->created_by);
            $cb_title = $cb->title;
            $cb_start_date = $cb->start_date;

            $dataToView['title']            = trans('privateTopics.create_topic');
            $dataToView['parameters']       = $parameters;
            $dataToView['type']             = $type;
            $dataToView['cbKey']            = $cbKey;
            $dataToView['fileId']           = $fileId;
            $dataToView['fileCode']         = $fileCode;
            $dataToView['configurations']   = $configurations;
            $dataToView['allowFiles']       = $allowFiles;
            $dataToView['uploadKey']        = Files::getUploadKey();
            $dataToView['sidebar']          = 'padsType';
            $dataToView['active']           = 'topics';
            $dataToView['cbAuthor']         = $author;
            $dataToView['cb_title']         = $cb_title;
            $dataToView['cb_start_date']    = $cb_start_date;
            $dataToView['status']           = $status;
            /* End of Get Topic Creation Data */

            return view('private.topics.createWithUser', $dataToView);
        } catch (Exception $e) {
            dd($e);
            return redirect()->back()->withErrors(["topic.createWithUser" => $e->getMessage()]);
        }
    }

    public function storeWithUser(Request $request, $type, $cbKey) {
        try {
            $userRequest = new UserRequest();
            $topicRequest = new TopicRequest();
            foreach ($request->all() as $inputKey=>$inputValue) {
                if (starts_with($inputKey, "userData_")) {
                    $inputKey = str_replace("userData_", "", $inputKey);
                    $userRequest[$inputKey] = $inputValue;
                } else if (starts_with($inputKey, "topicData_")) {
                    $inputKey = str_replace("topicData_", "", $inputKey);
                    $topicRequest[$inputKey] = $inputValue;
                }
            }

            /* User Creation */
            try {
                $userCreationResponse = (new UsersController())->store($userRequest,true);
                if (isset($userCreationResponse["success"]))
                    $userKey = $userCreationResponse["success"];
                else
                    throw new Exception($userCreationResponse["error"]??"unrecognized error while creating user");
            } catch (Exception $e) {
                return redirect()->back()->withErrors(["topic.withUser.store" => $e->getMessage()]);
            }

            /* Topic Creation */
            try {
                $topicRequest["topic_creator"] = $userKey;

                $topicCreationResponse = (new TopicController())->store($topicRequest,$type,$cbKey,true);
                if (isset($topicCreationResponse["success"]))
                    $topicKey = $topicCreationResponse["success"];
                else
                    throw new Exception($topicCreationResponse["error"]??"unrecognized error while creating topic");
            } catch (Exception $e) {
                dd(get_defined_vars());
                return redirect()->back()->withErrors(["topic.withUser.store" => $e->getMessage()]);
            }

            return redirect()->action('TopicController@show', ['cbKey' => $cbKey, 'topicKey' => $topicKey, 'type' => $type]);
        } catch (Exception $e) {
            dd(get_defined_vars());
            return redirect()->back()->withErrors(["topic.withUser.store" => $e->getMessage()]);
        }
    }

    /**
     * Edit a existent resource.
     *
     * @param $type
     * @param $cbKey
     * @param $topicKey
     * @return View
     */
    public function edit($type, $cbKey, $topicKey)
    {
        try {
            if(Session::get('user_role') != 'admin') {
                if (ONE::verifyUserPermissionsUpdate('cb', 'topics') == false) {
                    return redirect()->back()->withErrors(["cb.show" => trans('privateCbs.permission_message')]);
                }
            }

            $topic = CB::getTopicParameters($topicKey, null);

            $topic_author = (Auth::getUserByKey($topic->created_by))->name;

            $topicParameters = [];
            foreach ($topic->parameters as $param){
                $topicParameters[$param->id] = $param;
            }

            if(!($relatedParameter = collect($topic->parameters)->where('code','=','associated_topics'))->isEmpty()){

                $data['relatedParameter'] = json_decode($relatedParameter->first()->pivot->value);
                $data['relatedParameter']->id = $relatedParameter->first()->id;
                $topics = CB::getTopicsByParentKey($topicKey);
                $data['relatedParameter']->fetchedTopics = $topics;

            }
            $post = $topic->first_post;
            $CbParameters = CB::getCbParametersOptions($cbKey)->parameters;

            if(!isset($data['relatedParameter'])){
                if(!empty($relatedParameter = collect($CbParameters)->where('code','=','associated_topics'))){
                    $data['relatedParameter'] = $relatedParameter;

                }
            }
            // Request configurations
            $topicData = CB::getTopicDataWithChilds($topicKey);

            $configurations = $topicData->configurations;


            // Check Access
            if( !CB::checkCBsOption($configurations, 'PUBLIC-ACCESS') && !ONE::isAuth() ){
                return redirect()->action('AuthController@login');
            }

            $allowFiles = [];
            if( CB::checkCBsOption($configurations, 'ALLOW-FILES') ){
                $allowFiles[] = "docs";
            }

            if( CB::checkCBsOption($configurations, 'ALLOW-PICTURES')  ){
                $allowFiles[] = "images";
            }

            $fileId = 0;
            $posX = "";
            $posY = "";
            $parameters = [];
            foreach ($CbParameters as $parameter) {

                $name = $parameter->parameter;
                $code = $parameter->type->code;

                if( isset($topicParameters[$parameter->id]))
                    $value = $topicParameters[$parameter->id]->pivot->value;
                else
                    $value = "";

                $parameterOptions = [];
                $options = $parameter->options;
                foreach ($options as $option) {
                    $parameterOptions[$option->id] = $option->label;
                }

                $parameters[$name] = array('id' => $parameter->id, 'value' => $value, 'name' => $name, 'code' => $code, 'options' => $parameterOptions,'mandatory' => $parameter->mandatory,'description' => $parameter->description);

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
            $status=CB::getStatusTypes();
            $fileCode = '';
            if ($fileId != 0) {
                $file = Files::getFile($fileId);
                $fileCode = $file->code;
            }

            $title = trans('privateTopics.update_topic');

            $cb = CB::getCb($cbKey);
            $author = (Auth::getUserByKey($cb->created_by));
            $cb_title = $cb->title;
            $cb_start_date = $cb->start_date;

            Session::put('sidebarArguments', ['type' => $type, 'cbKey' => $cbKey, 'activeFirstMenu' => 'topics', 'topicKey' => $topicKey]);
            Session::put('sidebarArguments.activeSecondMenu', 'details');

            Session::put('sidebars', [0 => 'private', 1=> 'padsType', 2 => 'topics']);

            $data['title'] = $title;
            $data['topic'] = $topic;
            $data['cbKey'] = $cbKey;
            $data['topicKey'] = $topicKey;
            $data['type'] = $type;
            $data['post'] = $post;
            $data['parameters'] = $parameters;
            $data['topicParameters'] = $topicParameters;
            $data['fileCode'] = $fileCode;
            $data['allowFiles'] = $allowFiles;
            $data['configurations'] = $configurations;
            $data['uploadKey']      = Files::getUploadKey();
            $data['sidebar']            = 'topics';
            $data['active']             = 'details';
            $data['cbAuthor']             = $author;
            $data['cb_title']           = $cb_title;
            $data['cb_start_date']      = $cb_start_date;
            $data['topic_author']       = $topic_author;
            $data['status']             =$status;
            return view('private.topics.topic', $data);
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["topic.edit" => $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @param  string $type , string $cbKey, string $topicKey
     * @param $cbKey
     * @param $topicKey
     * @param null $version
     * @return View
     */
    public function show(Request $request, $type, $cbKey, $topicKey, $version = null)
    {
        $module = 'cb';
        $moduleType = 'topic';
        $data = [];
//        if(Session::get('user_role') != 'admin') {
//            if (ONE::verifyUserPermissionsShow('cb', 'topics') == false){
//                return redirect()->back()->withErrors(["cb.show" => trans('privateCbs.permission_message')]);
//            }
//        }

        try {
            $topic = CB::getTopicParameters($topicKey, $version);

            $topic_author = (Auth::getUserByKey($topic->created_by))->name;
            $topicParameters = [];

            foreach ($topic->parameters as $param) {
                $topicParameters[$param->id] = $param;
            }
            if(!($relatedParameter = collect($topic->parameters)->where('code','=','associated_topics'))->isEmpty()){

                $data['relatedParameter'] = json_decode($relatedParameter->first()->pivot->value);
                $data['relatedParameter']->id = $relatedParameter->first()->id;
                $topics = CB::getTopicsByParentKey($topicKey);
                $data['relatedParameter']->fetchedTopics = $topics;
            }

            $post = $topic->first_post;
            $CbParameters = CB::getCbParametersOptions($cbKey)->parameters;

            if(!isset($data['relatedParameter'])){
                if(!empty($relatedParameter = collect($CbParameters)->where('code','=','associated_topics'))){
                    $data['relatedParameter'] = $relatedParameter;
                }
            }

            // Request configurations
            $topicData = CB::getTopicDataWithChilds($topicKey);

            $configurations = $topicData->configurations;

            // Check Access
            if( !CB::checkCBsOption($configurations, 'PUBLIC-ACCESS') && !ONE::isAuth() ){
                return redirect()->action('AuthController@login');
            }

            $allowFiles = [];
            if( CB::checkCBsOption($configurations, 'ALLOW-FILES') ){
                $allowFiles[] = "docs";
            }

            if( CB::checkCBsOption($configurations, 'ALLOW-PICTURES')  ){
                $allowFiles[] = "images";
            }

            $fileId = 0;
            $posX = "";
            $posY = "";
            $parameters = [];
            foreach ($CbParameters as $parameter) {

                $name = $parameter->parameter;
                $code = $parameter->type->code;

                if( isset($topicParameters[$parameter->id]))
                    $value = $topicParameters[$parameter->id]->pivot->value;
                else
                    $value = "";

                $parameterOptions = [];
                $options = $parameter->options;
                foreach ($options as $option) {
                    $parameterOptions[$option->id] = $option->label;
                }

                $parameters[$name] = array('id' => $parameter->id, 'value' => $value, 'name' => $name, 'code' => $code, 'options' => $parameterOptions,'mandatory' => $parameter->mandatory);

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

            $fileCode = '';
            if ($fileId != 0) {
                $file = Files::getFile($fileId);
                $fileCode = $file->code;
            }

            $filesByType = [];
            $filesByType = CB::listFilesByType($topic->first_post->post_key);

            try{
                $user = Auth::getUserByKey($topic->created_by);
            } catch (Exception $e){
                $user = null;
            }

            $title = trans('privateTopics.show_topic');
            $cb = CB::getCb($cbKey);
            $cbAuthor = Auth::getUserByKey($cb->created_by);
            $cb_title = $cb->title;
            $cb_start_date = $cb->start_date;

            $cb = CB::getCbConfigurations($cbKey);
            $statusAvailable = CB::getStatusTypes();
            $statusTypes = [];
            foreach ($statusAvailable as $status){
                $statusTypes[$status->code] = $status->name;
            }

            $topicCooperators = CB::getCooperatorsList($request, $topicKey);

            Session::put('sidebarArguments', ['type' => $type, 'cbKey' => $cbKey, 'activeFirstMenu' => 'topics', 'topicKey' => $topicKey]);
            Session::put('sidebarArguments.activeSecondMenu', 'details');

            //Technical Analysis
            $hasAnalysis = !empty(CB::getCbQuestions($cbKey));

            $technicalAnalysis = null;
            if ($hasAnalysis){
                try{
                    $technicalAnalysis = CB::getTechnicalAnalysis($topicKey, $cbKey, $version);
                } catch (Exception $e){
                    // do nothing
                }
            }

            $data = [];
            $data['title']              = $title;
            $data['topic']              = $topic;
            $data['type']               = $type;
            $data['topicKey']           = $topicKey;
            $data['cbKey']              = $cbKey;
            $data['post']               = $post;
            $data['fileId']             = $fileId;
            $data['fileCode']           = $fileCode;
            $data['parameters']         = $parameters;
            $data['configurations']     = $configurations;
            $data['topicParameters']    = $topicParameters;
            $data['filesByType']        = $filesByType;
            $data['edit']               = $editTopic ?? 1;
            $data['user']               = $user ?? null;
            $data['delete']             = $deleteTopic ?? 1;
            $data['sidebar']            = 'topics';
            $data['active']             = 'details';
            $data['cbAuthor']           = $cbAuthor;
            $data['cb_title']           = $cb_title;
            $data['cb_start_date']      = $cb_start_date;
            $data['topic_author']       = $topic_author;
            $data['statusTypes']        = $statusTypes;

            $data['topicCooperators']   = $topicCooperators;
            $data['hasAnalysis']        = $hasAnalysis;
            $data['technicalAnalysis']  = $technicalAnalysis;

            return view('private.topics.topic', $data);
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["topic.show" => $e->getMessage()]);
        }
    }

    public function sendEmailNotificationGroups($cbKey, $code, $type, $topic, $status = null){
        //get cb template
        $cbTemplates = CB::getCbTemplates($cbKey);
        foreach ($cbTemplates as $cbTemplate){
            if($cbTemplate->configuration_code  == $code){
                $template = $cbTemplate;
            }
        }

        if($template!=''){
            $cbConfigurations = CB::getCbConfigurations($cbKey);
            foreach ($cbConfigurations->configurations as $cbConfiguration){
                if($template->configuration_code == $cbConfiguration->code){
                    $groups = json_decode($cbConfiguration->pivot->value);
                }
            }

            $usersEmail = [];
            foreach ($groups as $group){
                $users = Orchestrator::getUsersByEntityGroupKey($group);
                foreach ($users as $user){
                    $usersEmail[] = Orchestrator::getUserEmail($user->user_key);
                }
            }

            $userKey = (Session::get('user'))->user_key;

            $url = "<a href='".action('TopicController@show', [$type, $cbKey, $topic->topic_key])."'>".$topic->title."</a>";

            $tags = ["topic" => $url, "title_topic" => $topic->title, "status" => $status];

            $sendEmail = Notify::sendEmailByTemplateKey($template->template_key, $usersEmail, $userKey, $tags);
        }
        else{
            Session::flash('message', trans('topic.fail_send_email'));

        }
    }


    /**
     * @param $cbKey
     * @param $code
     * @param $type
     * @param $topic
     * @param $users
     * @param $owner
     * @param null $status
     */
    public function sendEmailNotification($cbKey, $code, $type, $topic, $users, $owner, $status = null){
        //get cb template
        $template = null;
        $cbTemplates = CB::getCbTemplates($cbKey);
        foreach ($cbTemplates as $cbTemplate){
            if($cbTemplate->configuration_code  == $code){
                $template = $cbTemplate;
            }
        }

        if(!is_null($template)){
            $usersEmail = [];
            foreach ($users as $user){
                $usersEmail[] = Orchestrator::getUserEmail($user->user_key);
            }

            if($owner!=null){
                $userEmail = Orchestrator::getUserEmail($owner);
                $usersEmail[] = $userEmail;
            }

            $userKey = (Session::get('user'))->user_key;

//            $url = "<a href='".action('TopicController@show', [$type, $cbKey, $topic->topic_key])."'>".$topic->title."</a>";  //private url
            $url = "<a href='".action('PublicTopicController@show', [$cbKey, $topic->topic_key, 'type' => $type])."'>".$topic->title."</a>";    //public url

            $tags = ["topic" => $url, "title_topic" => $topic->title, "status" => $status];

            $sendEmail = Notify::sendEmailByTemplateKey($template->template_key, $usersEmail, $userKey, $tags);
        }
        else{
            Session::flash('message', trans('topic.fail_send_email'));
        }
    }

    /**
     * Store the specified resource.
     * @param TopicRequest $requestTopic
     * @param $type
     * @param $cbKey
     * @param bool $internalCall
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function store(TopicRequest $requestTopic, $type, $cbKey, $internalCall = false)
    {
        try {
            //verify parameters of topic
            $parametersToSend = [];

            if(isset($requestTopic['myTopics']) && !empty($requestTopic['myTopics'])){
                $informationToStore = ['pad_type' => $requestTopic['pad_type'],'pad_key' => $requestTopic['pad_key'],'myTopics' => $requestTopic['myTopics']];
                $parametersToSend[] = array('parameter_id' => $requestTopic['associated_id'], 'value' => json_encode($informationToStore));
            }


            foreach ($requestTopic->all() as $key => $value) {

                if (strpos($key, 'parameter_maps_required_') !== false) {

                    $id = str_replace("parameter_maps_required_", "", $key);

                    if ($value != '')
                        $parametersToSend[] = array('parameter_id' => $id, 'value' => $value);

                } else if (strpos($key, 'parameter_required_') !== false) {

                    $id = str_replace("parameter_required_", "", $key);

                    if ($value != '')
                        $parametersToSend[] = array('parameter_id' => $id, 'value' => $value);

                } else if (strpos($key, 'parameter_') !== false) {
                    $id = str_replace("parameter_", "", $key);

                    if ($value != '')
                        $parametersToSend[] = array('parameter_id' => $id, 'value' => $value);

                } //Save position Image!
                else if (strpos($key, 'marker_pos_x_') !== false) {
                    $id = str_replace("marker_pos_x_", "", $key);


                    $posX = $requestTopic["marker_pos_x_" . $id];
                    $posY = $requestTopic["marker_pos_y_" . $id];
                    $parametersToSend[] = array('parameter_id' => $id, 'value' => $posX . "-" . $posY);
                }
            }
            //store new topic with parameters and !files
            $topic = CB::setTopicWithParameters($cbKey, $requestTopic, $parametersToSend, true);

            $post_key = $topic->first_post->post_key;
            if (Session::has('filesToUpload')) {
                $files = Session::get('filesToUpload');
                foreach ($files as $file) {
                    CB::setFilesForTopic($post_key, $file);
                }
            }

            $cbConfigs = CB::getCbConfigurations($cbKey);
            foreach ($cbConfigs->configurations as $cbConfig){
                if($cbConfig->code == 'notification_create_topic'){
                    $sendEmail = $this->sendEmailNotificationGroups($cbKey, 'notification_create_topic', $type, $topic);
                }
                if($cbConfig->code == 'notification_owner_create_topic'){
                    $owner = $topic->created_by;
                    $cooperators = CB::getCooperators($topic->topic_key);
                    if (is_array($cooperators))
                        $sendEmail = $this->sendEmailNotification($cbKey, 'notification_owner_create_topic', $type, $topic, $cooperators, $owner);
                }
            }

            Session::flash('message', trans('topic.store_ok'));

            if ($internalCall)
                return ["success" => $topic->topic_key];
            else
                return redirect()->action('TopicController@show', ['cbKey' => $cbKey, 'topicKey' => $topic->topic_key, 'type' => $type]);

        } catch (Exception $e) {
            if ($internalCall)
                return ["error" => $e->getMessage()];
            else
                return redirect()->back()->withErrors(["topic.store" => $e->getMessage()]);
        }
    }

    /**
     * Update the specified resource.
     *
     * @param TopicRequest $requestTopic
     * @param  int $topicKey
     * @return Response
     */
    public function update(TopicRequest $requestTopic, $type, $cbKey, $topicKey)
    {
        try {
            // $type = $this->cbType[$requestTopic->type] ;


            $parametersToSend = [];

            if(isset($requestTopic['myTopics']) && !empty($requestTopic['myTopics'])){
                $informationToStore = ['pad_type' => $requestTopic['pad_type'],'pad_key' => $requestTopic['pad_key'],'myTopics' => $requestTopic['myTopics']];
                $parametersToSend[] = array('parameter_id' => $requestTopic['associated_id'], 'value' => json_encode($informationToStore));
            }
            foreach ($requestTopic->all() as $key => $value) {


                if ( strpos($key, 'parameter_maps_required_') !== false ) {

                    $id = str_replace("parameter_maps_required_", "", $key);

                    if ($value != '')
                        $parametersToSend[] = array('parameter_id' => $id, 'value' => $value);

                } else if ( strpos($key, 'parameter_required_') !== false ) {

                    $id = str_replace("parameter_required_", "", $key);

                    if ($value != '')
                        $parametersToSend[] = array('parameter_id' => $id, 'value' => $value);

                }else if (strpos($key, 'parameter_') !== false) {
                    $id = str_replace("parameter_", "", $key);

                    if ($value != '')
                        $parametersToSend[] = array('parameter_id' => $id, 'value' => $value);

                } //Save position Image!
                else if(strpos($key, 'marker_pos_x_') !== false){
                    $id = str_replace("marker_pos_x_", "", $key);


                    $posX = $requestTopic["marker_pos_x_".$id];
                    $posY = $requestTopic["marker_pos_y_".$id];
                    $parametersToSend[] = array('parameter_id' => $id, 'value' => $posX . "-" . $posY);
                }
            }

            $requestTopic->request->add(['link' => action('PublicTopicController@show', [$cbKey, $topicKey, 'type' => $type])]);

            $topic = CB::updateTopicWithParameters($topicKey, $requestTopic, $parametersToSend, true);

            $cbConfigs = CB::getCbConfigurations($cbKey);
            foreach ($cbConfigs->configurations as $cbConfig){
                if($cbConfig->code == 'notification_edit_topic') {
                    $sendEmail = $this->sendEmailNotificationGroups($cbKey, 'notification_edit_topic', $type, $topic);
                }
                else if($cbConfig->code == 'notification_content_change'){
                    $followers = CB::getFollowersTopic($topic->topic_key);
                    $sendEmail = $this->sendEmailNotification($cbKey, 'notification_content_change', $type, $topic, $followers, null);
                }
                else if($cbConfig->code == 'notification_owner_edit_topic'){
                    $owner = $topic->created_by;
                    $cooperators = CB::getCooperators($topic->topic_key);
                    if (is_array($cooperators))
                        $sendEmail = $this->sendEmailNotification($cbKey, 'notification_owner_edit_topic', $type, $topic, $cooperators, $owner);
                }
            }

            Session::flash('message', trans('topic.update_ok'));
            return redirect()->action('TopicController@show', ['cbKey' => $cbKey, 'topicKey' => $topicKey, 'type' => $type]);

        } catch (Exception $e) {
            return redirect()->back()->withErrors(["topic.update" => $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $type
     * @param $cbKey
     * @param $topicKey
     * @return View
     */
    public function destroy($type, $cbKey, $topicKey)
    {
        try {

            $topic = CB::getTopic($topicKey);

            //Get values before deletion
            $followers = CB::getFollowersTopic($topicKey);
            $cooperators = CB::getCooperators($topicKey);

            CB::deleteTopic($topicKey);
            $cbConfigs = CB::getCbConfigurations($cbKey);
            foreach ($cbConfigs->configurations as $cbConfig){
                if($cbConfig->code == 'notification_delete_topic') {
                    $sendEmail = $this->sendEmailNotificationGroups($cbKey, 'notification_delete_topic', $type, $topic->topic);
                }
                else if($cbConfig->code == 'notification_delete'){
                    $sendEmail = $this->sendEmailNotification($cbKey, 'notification_delete', $type, $topic->topic, $followers, null);
                }
                else if($cbConfig->code == 'notification_owner_delete_topic'){
                    $owner = $topic->topic->created_by;
                    if ($owner != 'anonymous' && is_array($cooperators)) {
                        $sendEmail = $this->sendEmailNotification($cbKey, 'notification_owner_delete_topic', $type, $topic->topic, $cooperators, $owner);
                    }
                }

                return action('CbsController@showTopics', ['cbKey' => $cbKey, 'type' => $type]);
            }
            return action('CbsController@show', ['cbKey' => $cbKey, 'topicKey' => $topicKey, 'type' => $type]);

        } catch (Exception $e) {
            return redirect()->back()->withErrors(["topic.destroy" => $e->getMessage()]);
        }

    }

    /**
     * Remove the specified topic from storage.
     *
     * @param $type
     * @param $cbKey
     * @param $topicKey
     * @return View
     */
    public function delete($type, $cbKey, $topicKey)
    {
        $data = array();

        $data['action'] = action("TopicController@destroy", ['type' => $type, 'topicKey' => $topicKey, 'cbKey' => $cbKey]);
        $data['title'] = "DELETE";
        $data['msg'] = "Are you sure you want to delete this Topic?";
        $data['btn_ok'] = "Delete";
        $data['btn_ko'] = "Cancel";

        return view("_layouts.deleteModal", $data);
    }

    /**
     * @param Request $request
     * @param $type
     * @param $cbKey
     * @return string
     */
    public function updateStatus(Request $request, $type, $cbKey)
    {
        try {
            if(Session::get('user_role') != 'admin') {
                if (ONE::verifyUserPermissionsUpdate('cb', 'topics') == false) {
                    return redirect()->back()->withErrors(["cb.show" => trans('privateCbs.permission_message')]);
                }
            }
            $newStatus = CB::updateTopicStatus($request);
            if($request->status_type_code=='accepted'){
                $response = CB::getTopic($request->topicKey);
                if($response->topic){

                    if($response->topic->created_by=='anonymous'){

                        $user = array(
                            'email' => CB::getAnonymousEmail($response->topic->topic_key),
                            'name' => 'anonymous'
                        );
                    }else{
                        $user = (array) Auth::getUserByKey($response->topic->created_by);
                    }

                    if($user) {

                        if($response->cb) {
                            $emailType = 'topic_status_update';
                            $tags = [
                                "topic_title" => $response->topic->title,
                                "topic_link" => URL::action("PublicTopicController@show", ['cbKey' => $response->cb->cb_key, 'topicKey' => $response->topic->topic_key, 'type' => $type]),
                                "cb_link" => URL::action("PublicCbsController@show", ['cbKey' => $response->cb->cb_key]),
                            ];

                            $cbConfigs = CB::getCbConfigurations($cbKey);
                            foreach ($cbConfigs->configurations as $cbConfig){
                                if($cbConfig->code == 'notification_topic_status_change') {
                                    $sendEmail = $this->sendEmailNotificationGroups($cbKey, 'notification_topic_status_change', $type, $response->topic);
                                }
                                else if($cbConfig->code == 'notification_status_change'){
                                    $followers = CB::getFollowersTopic($response->topic->topic_key);
                                    $sendEmail = $this->sendEmailNotification($cbKey, 'notification_status_change', $type, $response->topic, $followers, null);
                                }
                                else if($cbConfig->code == 'notification_owner_status_change'){
                                    $owner = $response->topic->created_by;
                                    $cooperators = CB::getCooperators($response->topic->topic_key);
                                    if (is_array($cooperators))
                                        $sendEmail = $this->sendEmailNotification($cbKey, 'notification_owner_status_change', $type, $response->topic, $cooperators, $owner);
                                }
                            }

                        }
                    }
                }
            }

            //  ====================================    NOTIFICATIONS

            //Get Status Types List
            $statusTypes = CB::getStatusTypes();

            //Get Status Name for Notification
            $status = null;
            foreach ($statusTypes as $statusType){
                if($statusType->id == $newStatus->status_type_id){
                    $status = $statusType->name;
                }
            }

            $cbConfigs = CB::getCbConfigurations($cbKey);
            foreach ($cbConfigs->configurations as $cbConfig){
                if($cbConfig->code == 'notification_topic_status_change') {
                    $sendEmail = $this->sendEmailNotificationGroups($cbKey, 'notification_topic_status_change', $type, $response->topic, $status);
                }
                else if($cbConfig->code == 'notification_status_change'){
                    $followers = CB::getFollowersTopic($response->topic->topic_key);
                    $sendEmail = $this->sendEmailNotification($cbKey, 'notification_status_change', $type, $response->topic, $followers, null, $status);
                }
                else if($cbConfig->code == 'notification_owner_change_status'){
                    $owner = $response->topic->created_by;
                    $cooperators = CB::getCooperators($response->topic->topic_key);
                    if (is_array($cooperators))
                        $sendEmail = $this->sendEmailNotification($cbKey, 'notification_owner_change_status', $type, $response->topic, $cooperators, $owner, $status);
                }
            }


            return action('CbsController@showTopics', ['type'=>$type,'cbKey' =>$cbKey]);

        } catch (Exception $e) {
            return "false";
        }
    }

    /**
     * Updates topic's status Accepted/Not Accepted
     * @param Request $request
     * @return string
     */
    public function updateStatusTopic(Request $request)
    {
        try {
            $type = $request->type;
            $cbKey = $request->cbKey;
            $topicKey =  $request->topicKey;

            $newStatus = CB::updateTopicStatus($request);

            //Get Status Types List
            $statusTypes = CB::getStatusTypes();

            //Get Status Name for Notification
            $status = null;
            foreach ($statusTypes as $statusType){
                if($statusType->id == $newStatus->status_type_id){
                    $status = $statusType->name;
                }
            }

            $topic = CB::getTopic($topicKey);
            $cbConfigs = CB::getCbConfigurations($cbKey);
            foreach ($cbConfigs->configurations as $cbConfig){
                if($cbConfig->code == 'notification_topic_status_change') {
                    $sendEmail = $this->sendEmailNotificationGroups($cbKey, 'notification_topic_status_change', $type, $topic, $status);
                }
                else if($cbConfig->code == 'notification_status_change'){
                    $followers = CB::getFollowersTopic($topic->topic_key);
                    $sendEmail = $this->sendEmailNotification($cbKey, 'notification_status_change', $type, $topic, $followers, null, $status);
                }
                else if($cbConfig->code == 'notification_owner_change_status'){
                    $owner = $topic->created_by;
                    $cooperators = CB::getCooperators($topic->topic_key);
                    if (is_array($cooperators))
                        $sendEmail = $this->sendEmailNotification($cbKey, 'notification_owner_change_status', $type, $topic, $cooperators, $owner, $status);
                }
            }

            return action('CbsController@showTopics', ['type' => $type, 'cbKey' => $cbKey]);

        } catch (Exception $e) {
            return "false";
        }
    }


    /**
     * @param Request $request
     * @return string
     */
    public function statusHistory(Request $request)
    {
        try {
            $topicKey = $request->topicKey;
            $historyStatus = CB::getStatusHistory($topicKey);
            $html = '';
            if(count($historyStatus) > 0) {
                foreach ($historyStatus as $status) {
                    $html .= '<dl>';
                    // Parse with carbon
                    $date = Carbon::parse($status->created_at)->toDateString();
                    $html .= '<div class="panel panel-default flat">';
                    $html .= '<div class="panel-heading"><b>'.$status->name.'</b> '.$date.'</div>';
                    $html .= '<div class="panel-body">';
                    if(isset($status->comments) && count($status->comments) > 0){
                        $html .= '<h4>'.trans('topic.comments').'</h4>';
                        foreach ($status->comments as $comment) {
                            $html .= '<dd>';
                            $html .= empty($comment->public) ? trans('topic.privateComment') : trans('topic.publicComment');
                            $html .= '</dd>';
                            $html .= '<dd>'.$comment->content.'</dd>';

                        }
                    }else{
                        $html .= '<dd>' . trans('topic.noCommentsAvailable') . '</dd>';
                    }
                    $html .= '</div>';
                    $html.= '</div>';
                    $html .= '</dl>';
                    $html .= '<hr style="margin: 10px 0 10px 0">';
                }
            }else{
                $html .= '<dt>' . trans('topic.noStatusHistoryAvailable') . '</dt>';
            }
            return $html;
        } catch (Exception $e) {
            return "false";
        }
    }

    /**
     * @param $type
     * @param $cbKey
     * @param $topicKey
     * @return $this|View
     */
    public function showPosts($type, $cbKey, $topicKey){
        try {
            $topic = CB::getTopicParameters($topicKey, null);

            // Request configurations
            $topicData = CB::getTopicDataWithChilds($topicKey);

            $configurations = $topicData->configurations;

            $title = trans('privateTopics.show_posts');

            $cb = CB::getCb($cbKey);
            $author = (Auth::getUserByKey($cb->created_by))->name;
            $cb_title = $cb->title;
            $cb_start_date = $cb->start_date;

            $data = [];
            $data['title']              = $title;
            $data['topic']              = $topic;
            $data['type']               = $type;
            $data['topicKey']           = $topicKey;
            $data['type']               = $type;
            $data['configurations']     = $configurations;
            $data['cbKey']              = $cbKey;
            $data['sidebar']              = 'topics';
            $data['active']              = 'posts';
            $data['author']             = $author;
            $data['cb_title']           = $cb_title;
            $data['cb_start_date']      = $cb_start_date;


            return view('private.topics.topicModeration', $data);
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["topic.edit" => $e->getMessage()]);
        }

    }


    /**
     * @param Request $request
     * @param $type
     * @param $cbKey
     * @return mixed
     */
    public function getIndexTable(Request $request, $type, $cbKey)
    {

        $response = CB::topicsWithLastPost($request, $cbKey);
        $topics = $response->topics;
        $recordsTotal = $response->recordsTotal;

        $users = Collect($topics)->pluck('created_by');

        $usersKeysNames = Collect(Auth::getUserNames($users))->pluck('name', 'user_key');

        $collection = Collection::make($topics);

        $module = 'cb';
        $moduleType = 'topics';

        $edit = Session::get('user_role') == 'admin' || Session::get('user_permissions')->$module->$moduleType->permission_update;
        $delete = Session::get('user_role') == 'admin' || Session::get('user_permissions')->$module->$moduleType->permission_delete;

        $topicData = CB::getCBAndTopics($cbKey);
        $configurations = $topicData->configurations;
        $isQuestionnaire = false;

        if((ONE::checkCBsOption($configurations, 'TOPIC-AS-PRIV-QUESTIONNAIRE')) || (ONE::checkCBsOption($configurations, 'TOPIC-AS-PUBLIC-QUESTIONNAIRE'))){
            $isQuestionnaire = true;
        }

        return Datatables::of($collection)
            ->editColumn('title', function($collection) use($type, $cbKey, $isQuestionnaire){
                return "<a href='".action('TopicController@show', [$type, $cbKey, $collection->topic_key])."'>".($isQuestionnaire ? trans('privateTopics.show_questionnaire') : $collection->title)."</a>";
            })
            ->editColumn('created_by', function ($collection) use ($usersKeysNames) {
                if($collection->created_by == 'anonymous')
                    return ucfirst($collection->created_by);
                else
                    return "<a href='" . action('UsersController@show', [$collection->created_by]) . "'>" . $usersKeysNames[$collection->created_by] . "</a>";
            })
            ->addColumn('action', function ($collection) use ($type, $cbKey, $edit, $delete) {
                if($edit and $delete)
                    return ONE::actionButtons([$type, $cbKey, $collection->topic_key], ['form' => 'topic', 'edit' => 'TopicController@edit', 'delete' => 'TopicController@delete']);
                elseif($edit==false and $delete)
                    return ONE::actionButtons([$type, $cbKey, $collection->topic_key], ['form' => 'topic', 'delete' => 'TopicController@delete']);
                elseif($edit and $delete==false)
                    return ONE::actionButtons([$type, $cbKey, $collection->topic_key], ['form' => 'topic', 'edit' => 'TopicController@edit']);
                else
                    return null;
            })
            ->skipPaging()
            ->setTotalRecords($recordsTotal)
            ->make(true);
    }

    public function getFullTopicsTable($type)
    {
        $listCbs = Orchestrator::getAllCbs();
        $topics = CB::getAllTopics($listCbs);
        $collection = Collection::make($topics);
        return Datatables::of($collection)
            ->editColumn('title', function($collection) use ($listCbs) {
                return "<a href='" . action('TopicController@show', [$listCbs->{$collection->cb_key}->cb_type->code, $collection->cb_key, $collection->topic_key]) . "'>" . $collection->title;
            })
            ->addColumn('action', function ($collection) use ($listCbs) {
                // return '<a href="javascript:updateStatus(\''.$collection->topic_key.'\',\'accepted\')">' . '<span class="badge badge-success">'.trans('privatePropositionModeration.accept').'</span>' . '</a>
                // <a href="javascript:updateStatus(\''.$collection->topic_key.'\',\'not_accepted\')">' . '<span class="badge badge-danger">'.trans('privatePropositionModeration.reject').'</span>' . "</a>";
                return '<a href="javascript:updateStatus(\''.$collection->topic_key.'\',\'moderated\',\''.$collection->cb_key.'\',\''.$listCbs->{$collection->cb_key}->cb_type->code.'\')">' . '<span class="badge badge-success">'.trans('privatePropositionModeration.moderate').'</span>' . '</a>';
            })
            ->make(true);
    }

    /**
     * @param Request $request
     * @param $type
     * @param $cbKey
     * @return mixed
     */
    public function getIndexTableStatus(Request $request, $type, $cbKey)
    {
        $recordsTotal = 0;

        $response = CB::topicsWithLastPost($request, $cbKey);

        $topics = $response->topics;
        $recordsTotal = $response->recordsTotal;
        $recordsFiltered = $response->recordsFiltered;
        $cbKeys[] = $cbKey;

        $users = Collect($topics)->pluck('created_by');

        $usersKeysNames = Collect(Auth::getUserNames($users))->pluck('name', 'user_key');

        $collection = Collection::make($topics);

        $module = 'cb';
        $moduleType = 'topics';

        $status = (Session::get('user_role') == 'admin' || Session::get('user_permissions')->$module->$moduleType->permission_update);
        $history = (Session::get('user_role') == 'admin' || Session::get('user_permissions')->$module->$moduleType->permission_delete);
        $edit = Session::get('user_role') == 'admin' || Session::get('user_permissions')->$module->$moduleType->permission_update;
        $delete = Session::get('user_role') == 'admin' || Session::get('user_permissions')->$module->$moduleType->permission_delete;

        $dataTableData = Datatables::of($collection)
            ->editColumn('select_topics', function ($topic) {
                return '<input class="topic_id" type="checkbox" value="'.$topic->id.'" id="'.$topic->id.'"/>';
            })
            ->editColumn('title', function ($topic) use ($type, $cbKey) {
                return '<a href="'.action('TopicController@show', [$type, $cbKey, isset($topic->topic_key) ? $topic->topic_key : null]).'">'.($topic->title ?? null).'</a>';
//                return '<div style="display: none" class="topic_id">'.$topic->id.'</div><a href="'.action('TopicController@show', [$type, $cbKey, isset($topic->topic_key) ? $topic->topic_key : null]).'">'.($topic->title ?? null).'</a>';
            })
            ->editColumn('status', function ($topic) {
                return isset($topic->status->name) ? $topic->status->name : trans('topic.noStatusAvailable');
            })
            ->editColumn('technical_analysis', function ($topic) use($type,$cbKey) {
                if (collect($topic->technical_analysis??[])->where('active', 1)->count()){

                    $decision = collect($topic->technical_analysis)->where('active', 1)->first();
                    if (!empty($decision) && $decision->decision < 0){
                        return  '<a href="'.action("TechnicalAnalysisController@show", ['type' => $type, 'cbKey' => $cbKey, 'topicKey' => $topic->topic_key]).'">' .
                                    '<img src="'.asset("/images/techEvaluation-icon-red.svg") .'" alt="'.trans('privateTopics.decision_failed').'" height="32" width="32" data-toggle="tooltip" title="'.trans('privateTopics.decision_failed').'">' .
                                '</a>';
                    } elseif (!empty($decision) && $decision->decision > 0){
                        return  '<a href="'.action("TechnicalAnalysisController@show", ['type' => $type, 'cbKey' => $cbKey, 'topicKey' => $topic->topic_key]).'">' .
                                    '<img src="'.asset("/images/techEvaluation-icon-green.svg") .'" alt="'.trans('privateTopics.decision_passed').'" height="32" width="32" data-toggle="tooltip" title="'.trans('privateTopics.decision_passed').'">' .
                                '</a>';
                    } elseif (!empty($decision) && $decision->decision == 0){
                        return  '<a href="'.action("TechnicalAnalysisController@show", ['type' => $type, 'cbKey' => $cbKey, 'topicKey' => $topic->topic_key]).'">' .
                                    '<img src="'.asset("/images/techEvaluation-icon.svg") .'" alt="'.trans('privateTopics.decision_undetermined').'" height="32" width="32" data-toggle="tooltip" title="'.trans('privateTopics.decision_undetermined').'">' .
                                '</a>';

                    }
                }
                return  '<a href="'.action("TechnicalAnalysisController@create", ['type' => $type, 'cbKey' => $cbKey, 'topicKey' => $topic->topic_key]).'">' .
                            '<img src="'.asset("/images/techEvaluation-icon-grey.svg") .'" alt="'.trans('privateTopics.create_technical_analysis').'" height="32" width="32" data-toggle="tooltip" title="'.trans('privateTopics.create_technical_analysis').'">' .
                        '</a>';
            })
            ->editColumn('name', function ($collection) use ($usersKeysNames){
                if ($collection->created_by != 'anonymous') {
                    return $usersKeysNames[$collection->created_by];
                }
                return trans('privateUser.anonymous');
            })
            ->addColumn('update_status', function ($collection) use ($type, $cbKey, $status, $history) {
                if($status and $history)
                    return '<a href="javascript:updateStatus(\''.$collection->topic_key.'\')">' . '<span class="btn btn-flat btn-edit" data-toggle="tooltip" title="'.trans('privateCbs.topic_status').'"><i class="fa fa-repeat" aria-hidden="true"></i></span>' . '</a>
              <a href="javascript:showStatusHistory(\''.$collection->topic_key.'\')">' . '<span class="btn btn-flat btn-edit" data-toggle="tooltip" title="'.trans('topic.history').'"><i class="fa fa-history" aria-hidden="true"></i></span>' . "</a>";
                elseif($status==false and $history)
                    return '<a href="javascript:showStatusHistory(\''.$collection->topic_key.'\')">' . '<span class="btn btn-flat btn-info">'.trans(" ").'</span>' . "</a>";
                elseif($status and $history==false)
                    return '<a href="javascript:updateStatus(\''.$collection->topic_key.'\')">' . '<span class="btn btn-flat btn-warning">'.trans(" ").'</span>' . '</a>';
                else
                    return null;

            })->addColumn('action', function ($collection) use ($type, $cbKey, $edit, $delete) {
                if($edit and $delete)
                    return ONE::actionButtons([$type, $cbKey, $collection->topic_key], ['form' => 'topic', 'edit' => 'TopicController@edit', 'delete' => 'TopicController@delete']);
                elseif($edit==false and $delete)
                    return ONE::actionButtons([$type, $cbKey, $collection->topic_key], ['form' => 'topic', 'delete' => 'TopicController@delete']);
                elseif($edit and $delete==false)
                    return ONE::actionButtons([$type, $cbKey, $collection->topic_key], ['form' => 'topic', 'edit' => 'TopicController@edit']);
                else
                    return null;
            })
            ->with('filtered', $recordsFiltered)
            ->skipPaging()
            ->setTotalRecords($recordsTotal);


        if ($request->has("parameters.vote_event") && !empty($request->get("parameters")["vote_event"]??"")) {
            $dataTableData
                ->addColumn("votes",function($topic) {
                    return $topic->balance_votes ?? 0;
                })
                ->order(function(){
                    return true;
                });
        } else
            $dataTableData
                ->addColumn("votes",function($topic) {
                    return 0;
                });

        return $dataTableData->make(true);
    }

    /**
     * @param $type
     * @param $topicKey
     * @return mixed
     */
    public function getIndexTablePosts($type, $cbKey ,$topicKey)
    {
        // getting posts
        $topicData = CB::getTopicPrivateDataWithChilds($topicKey);

        $posts = $topicData->posts;

        foreach($posts as $post){
            if(!empty($post->replies)){
                foreach($post->replies as $reply){
                    $postsList[] = $reply;

                }
            }

            $postsList[] = $post;
        }
        // preparing userkeys array
        $usersKey = [];
        foreach ($posts as $post) {
            $usersKey[] = $post->created_by;
        }

        // getting usernames with userkeys array
        $responseAuth = Auth::listUser($usersKey);
        $userNames = [];
        foreach ($responseAuth as $item) {
            $userNames[$item->user_key] = $item->name;
        }

        // Topics Configurations
        $topicData = CB::getTopicDataWithChilds($topicKey);
        $commentsNeedsAuth = ONE::checkCBsOption($topicData->configurations, 'COMMENT-NEEDS-AUTHORIZATION');

        // Filtering posts: requiring authorization and q need to be aproved + Post with abuse report
        $filteredPosts = [];
        foreach($postsList as $post){

            $filteredPosts[] = $post;
            if(!isset($post->abuses))
                $post->abuses = 0;
        }

        $response = CB::getCbAbuses($cbKey);

        foreach($response as $posts){
            foreach($posts->posts as $post){
                if(!empty($post->abuses)){

                }
            }
        }

        $collection = Collection::make($filteredPosts);

        return Datatables::of($collection)
            ->addColumn('approve', function ($collection) use ($type, $cbKey, $topicKey, $commentsNeedsAuth)  {

                $html = "";
                if($commentsNeedsAuth && $collection->active == 0){
                    $html = '<a href="'. action('PostController@active', [$type, $cbKey, $topicKey,$collection->post_key, 1, 'posts']) .'" class="btn btn-flat btn-success btn-xs btn-thumbs-up-active" data-toggle="tooltip" data-original-title="approve"><i class="glyphicon glyphicon-thumbs-up"></i> </a> ';
                    $html .= '<a href="'. action('PostController@active', [$type, $cbKey, $topicKey,$collection->post_key, 0, 'posts']) .'" class="btn btn-flat btn-danger btn-xs" data-toggle="tooltip" data-original-title="disapprove"><i class="glyphicon glyphicon-thumbs-down"></i> </a> ';
                    //$html .= "<span class='badge badge-warning' data-toggle=\"tooltip\" data-original-title='".trans("privatePosts.disapproved")."'>&nbsp;</span>";
                } else if($commentsNeedsAuth && $collection->active == 1) {
                    $html = '<a href="'. action('PostController@active', [$type, $cbKey, $topicKey,$collection->post_key, 1, 'posts']) .'" class="btn btn-flat btn-success btn-xs" data-toggle="tooltip" data-original-title="approve"><i class="glyphicon glyphicon-thumbs-up"></i> </a> ';
                    $html .= '<a href="'. action('PostController@active', [$type, $cbKey, $topicKey,$collection->post_key, 0, 'posts']) .'" class="btn btn-flat btn-danger btn-xs btn-thumbs-down-active" data-toggle="tooltip" data-original-title="disapprove"><i class="glyphicon glyphicon-thumbs-down"></i> </a> ';
                    // $html .= "<span class='badge badge-success' data-toggle=\"tooltip\" data-original-title='".trans("privatePosts.approved")."'>&nbsp;</span>";
                }

                return $html;
            })
            ->editColumn('message', function ($collection) {
                return  $collection->contents;
            })->editColumn('parent_id', function ($collection) {
                if($collection->parent_id != 0)
                    return  $collection->parent_id;
                else
                    return '';
            })
            ->editColumn('created_by', function ($collection) use ($userNames) {
                return !empty($userNames[$collection->created_by]) ? $userNames[$collection->created_by] : "";
            })
            ->editColumn('abuses', function ($collection) use ($type, $cbKey, $topicKey) {

                if($collection->blocked == 0){
                    $buttons = "";
                    if( $collection->abuses > 0 ){
                        $buttons .= '<a href="'. action('PostController@blocked', [$type, $cbKey, $topicKey,$collection->post_key, 1, 'posts']) .'" class="btn btn-flat btn-danger btn-xs" data-toggle="tooltip" data-original-title="Block"><i class="glyphicon glyphicon-thumbs-up"></i> '.trans("privatePosts.block").'</a>';
                    }

                    if($collection->abuses == 0){
                        $labelType = "badge badge-secondary";
                    } else if($collection->abuses == 1){
                        $labelType = "badge badge-warning";
                    } else {
                        $labelType = "badge badge-danger";
                    }
                    if( $collection->abuses > 0 ) {
                        $content = "<a href='javascript:showAbuses(\"" . $collection->post_key . "\")'><span class='label " . $labelType . "'>" . $collection->abuses . "</a></span> " . $buttons;
                    }else{
                        $content = "<span class='label " . $labelType . "'>" . $collection->abuses . "</span> " . $buttons;
                    }
                } else {
                    $buttons = '<a href="'. action('PostController@blocked', [$type, $cbKey, $topicKey,$collection->post_key, 0, 'posts']) .'" class="btn btn-flat btn-success btn-xs" data-toggle="tooltip" data-original-title="unblock"><i class="glyphicon glyphicon-thumbs-up"></i> '.trans("privatePosts.unblock").'</a>';
                    $content = "<a href='javascript:showAbuses(\"".$collection->post_key."\")'><span class='badge badge-danger'>".$collection->abuses." / ".trans("privatePosts.blocked")."</a></span> ".$buttons;
                }

                return $content;
            })
            ->addColumn('action', function ($collection) use ($type, $cbKey, $topicKey) {
                return ONE::actionButtons([$type, $cbKey ,$topicKey, $collection->post_key], ['delete' => 'PostController@delete']);
            })
            ->make(true);
    }

    /**
     * @param Request $request
     * @param $type
     * @param $cbKey
     * @param $topicKey
     */
    public function getAbuses(Request $request, $type, $cbKey, $topicKey){


        $response = CB::getCbAbuses($cbKey);
        $html = "";
        foreach($response as $posts){
            foreach($posts->posts as $post){
                if(!empty($post->abuses) && $post->post_key == $request->postKey){
                    foreach($post->abuses as $abuse){
                        $html .= "<div class='panel panel-default flat'>";
                        switch($abuse->type_id){
                            case 1: $html .= "<div class='panel-heading'> " .trans('privatePropositionModeration.spam')." </div>";
                                break;
                            case 2: $html .= "<div class='panel-heading'> " . trans('privatePropositionModeration.contains_hate_speech_or_atacks')." </div>";
                                break;
                            case 3: $html .= "<div class='panel-heading'> " .trans('privatePropositionModeration.content_not_recommended')." </div>";
                                break;
                        }
                        $html .= "<div class='panel-body' style='overflow-y:auto'>";

                        $html .= "<p>". $abuse->comment ."</p>";

                        $html .= "</div></div>";
                    }


                }
            }
        }

        echo $html;
    }

    /**
     * @param Request $request
     */
    public function getAbusesPrivate(Request $request){

        $response = CB::getCbAbuses($request->cbKey);

        $html = "";
        foreach($response->json()->data as $posts){
            foreach($posts->posts as $post){
                if(!empty($post->abuses) && $post->post_key == $request->postKey){
                    foreach($post->abuses as $abuse){
                        $html .= "<div class='panel panel-default flat'>";
                        switch($abuse->type_id){
                            case 1: $html .= "<div class='panel-heading'> " .trans('privatePropositionModeration.spam')." </div>";
                                break;
                            case 2: $html .= "<div class='panel-heading'> " . trans('privatePropositionModeration.contains_hate_speech_or_atacks')." </div>";
                                break;
                            case 3: $html .= "<div class='panel-heading'> " .trans('privatePropositionModeration.content_not_recommended')." </div>";
                                break;
                        }
                        $html .= "<div class='panel-body' style='overflow-y:auto'>";

                        $html .= "<p>". $abuse->comment ."</p>";

                        $html .= "</div></div>";
                    }


                }
            }
        }

        echo $html;
    }

//    private function sendNotifyEmail($request)
//    {
//        $response = CB::getTopic($request->topicKey);
//        $allStatus = CB::getStatusTypes();
//        $status = '';
//
//        foreach ($allStatus as $item){
//            if ($item->code == $request->status_type_code){
//                $status = $item->name;
//            }
//        }
//
//        if($response->topic){
//            if($response->topic->created_by=='anonymous'){
//                $user = array(
//                    'email' => CB::getAnonymousEmail($response->topic->topic_key),
//                    'name' => 'anonymous'
//                );
//            }else{
//                $user = (array) Auth::getUserByKey($response->topic->created_by);
//            }
//
//            if($user) {
//                if($response->cb) {
//                    if($request->type != 'qa'){
//                        $emailType = 'topic_status_update';
//                        $tags = [
//                            "user_name"     => $user['name'],
//                            "status"        => $status,
//                            "topic_title"   => $response->topic->title,
//                            "topic_link"    => URL::action("PublicTopicController@show", ['cbKey' => $response->cb->cb_key, 'topicKey' => $response->topic->topic_key, 'type' => $request->type]),
//                            "cb_link"       => URL::action("PublicCbsController@show", ['cbKey' => $response->cb->cb_key, 'type' => $request->type]),
//                        ];
//                        $response = Notify::sendEmail($emailType, $tags, $user);
//                    }else{
//                        $emailType = 'qa_changed_status_notification';
//                        $tags = [
//                            "user_name"     => $user['name'],
//                            "status"        => $status,
//                            "topic"   => $response->topic->title,
//                            "link"    => URL::action("PublicTopicController@show", ['cbKey' => $response->cb->cb_key, 'topicKey' => $response->topic->topic_key, 'type' => $request->type]),
//                            "cb_link"       => URL::action("PublicCbsController@show", ['cbKey' => $response->cb->cb_key, 'type' => $request->type]),
//                        ];
//                        $response = Notify::sendEmail($emailType, $tags, $user);
//                    }
//                }
//            }
//        }
//    }



    public function excel(Request $request, $type, $cbKey){
        // Getting data to export
        $exportIds = $request->input('exportIds') ?? null;
        $data = CB::getDataToExport($cbKey, true, $exportIds);

        $topics = $data->topics;

        // preparing userkeys array
        $usersKey = [];
        foreach ($topics as $topic) {
            $usersKey[] = $topic->created_by;
        }

        // getting usernames with userkeys array
        $responseAuth = Auth::listUser($usersKey);
        $userNames = [];
        foreach ($responseAuth as $item) {
            $userNames[$item->user_key] = $item->name;
            $userEmails[$item->user_key] = $item->email;
        }

        // Convert each member of the returned collection into an array,
        // and append it to the topics array.
        $parametersData = [];
        $parametersTitle = [];
        $availableVoteEvents = [];

        foreach ($topics as $topic) {
            // Parameters by topic key
            foreach ($topic->parameters as $parameter) {

                $parameterPivotValues = [];
                if (count($parameter->options) > '1') {
                    $parameterPivotValues = explode(',', $parameter->pivot->value);
                }

                if($parameter->code == 'topic_checkpoint_phase' || (!empty($parameter->visible) && ($parameter->visible == 1) && (isset($topic->topicVersionId) ? (isset($parameter->pivot->topic_version_id) ? $parameter->pivot->topic_version_id == $topic->topicVersionId : true) : true))) {

                    $options = [];
                    foreach (!empty($parameter->options) ? $parameter->options : [] as $optionItem) {

                        if (empty($parameterPivotValues)) {
                            if ($optionItem->id == $parameter->pivot->value) {
                                $options[] = !empty($optionItem->label) ? $optionItem->label : "";
                            }
                        } else {
                            if (in_array($optionItem->id, $parameterPivotValues)) {
                                $options[] = !empty($optionItem->label) ? $optionItem->label : "";
                            }
                        }
                    }

                    if (!empty($parameterPivotValues)) {
                        $options = implode(', ', $options);
                    }

                    if(!empty($parameter->pivot->value)){
                        $checkForZeros = explode(',', $parameter->pivot->value);
                        if(in_array("0",$checkForZeros)){
                            $parameter->pivot->value = '';
                        }
                    }
                    if($parameter->code == 'topic_checkpoint_phase'){
                        $parametersData[$topic->topic_key]['phases']["name"] = $parameter->type->name;
                        if(empty($parametersData[$topic->topic_key]['phases']["value"])){
                            $parametersData[$topic->topic_key]['phases']["value"] = '';
                        }
                        if($parameter->pivot->value == 1 && !str_contains($parametersData[$topic->topic_key]['phases']["value"],$parameter->parameter))
                            $parametersData[$topic->topic_key]['phases']["value"] = $parametersData[$topic->topic_key]['phases']["value"].$parameter->parameter.', ';
                    }else{
                        $parametersData[$topic->topic_key][$parameter->id]["name"] = $parameter->type->name;
                        $parametersData[$topic->topic_key][$parameter->id]["value"] = empty($options) ? $parameter->pivot->value : $options;

                        // Headers
                        $parametersTitle[$parameter->id] = $parameter->parameter ?? $parameter->type->name;
                    }

                }
            }
            if (isset($topic->voteData)) {
                foreach ($topic->voteData as $key => $value) {
                    $availableVoteEvents[$key] = $value->name;
                }
            }
        }
        Excel::create('Topics', function($excel)  use ($topics, $userNames, $parametersTitle, $parametersData, $availableVoteEvents, $type, $cbKey) {
            $excel->sheet("Data", function ($sheet) use ($topics, $userNames, $parametersTitle, $parametersData, $availableVoteEvents, $type, $cbKey) {
                $sheet->loadView('private.cbs.excel.topics', compact('topics', 'userNames','parametersData','parametersTitle', 'availableVoteEvents', 'type', 'cbKey') );
            });
        })->download('xlsx');

    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function getTopicsTechnicalEvaluation(Request $request){
        $listCbs = Orchestrator::getAllCbs();
        $topics = CB::getAllTopicsWithTecnicalEvaluation($listCbs);

        $collection = Collection::make($topics);

        return Datatables::of($collection)
            ->editColumn('title', function ($collection) use($listCbs) {
                return "<a href='" . action('TopicController@show', [$collection->cb_type, $collection->cb_key, $collection->topic_key]) . "'>" . $collection->title;
            })
            ->make(true);
    }


    /**
     * @param Request $request
     * @param $type
     * @param $cbKey
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function pdfList(Request $request, $type, $cbKey)
    {
        try {
            $exportIds = $request->input('exportIds') ?? null;

            // Getting data to export
            $data = CB::getDataToExport($cbKey, true, $exportIds);

            $cb = $data->cb;
            $allTopics = $data->topics;

            $topicFiles = CB::getTopicsFilesByType($allTopics);
            if (!empty($topicFiles)) {
                foreach ($topicFiles as $key => $file) {
                    $filesByType[$key] = $file;
                }
            }
            $allTopicsArray = array_chunk($allTopics, 50);

            $pdf = array();
            foreach ($allTopicsArray as $allTopics) {
                $pdf[] = PDF::loadView('private.cbs.pdf.topics', compact('cb', 'allTopics', 'filesByType'))
                    ->setPaper('a4', 'portrait')->setWarnings(false);
            }


            if (count($pdf) > 1) {
                do {
                    $zipFileName = storage_path("app/topics-" . Carbon::now()->format("Y-m-d_his") . ".zip");
                    if (\File::exists($zipFileName))
                        $zipFileName = "";
                } while (empty($zipFileName));
                $zipFile = (new Zipper)->make($zipFileName);

                $fileNames = array();
                foreach ($pdf as $index => $pdfItem) {
                    do {
                        $fileName = storage_path("app/PDFTemp-" . str_random(32) . ".pdf");
                        if (\File::exists($fileName))
                            $fileName = "";
                    } while (empty($fileName));

                    $pdfItem->save($fileName);
                    $zipFile->add($fileName, "topics-" . $index . ".pdf");
                    $fileNames[] = $fileName;
                }

                $zipFile->make($zipFileName);
                \File::delete($fileNames);
                return response()->download($zipFileName)->deleteFileAfterSend(true);
            } else {
                return $pdf[0]->download('topics.pdf');
            }
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["cb.show" => $e->getMessage()]);
        }


    }

    /**
     * @param $type
     * @param $cbKey
     * @param $topicKey
     * @param $status
     * @param $version
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function changeActiveVersionStatus($type, $cbKey, $topicKey, $status, $version){
        try {
            if(Session::get('user_role') != 'admin') {
                if (ONE::verifyUserPermissionsUpdate('cb', 'topics') == false) {
                    return redirect()->back()->withErrors(["cb.show" => trans('privateCbs.permission_message')]);
                }
            }
            $mayChangeParentTopics = false;
            if($type == strtolower('EVENT')){
                $mayChangeParentTopics = true;
            }

            CB::changeActiveVersionStatus($topicKey, $version, $status, Session::get('user')->user_key,$mayChangeParentTopics);
            return redirect()->action("TopicController@show", ["type" => $type, "cbKey" => $cbKey, "topicKey" => $topicKey, "version" => $version]);
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["private.topics.changeActiveVersion" => $e->getMessage()]);
        }
    }


    /**
     * @param $type
     * @param $cbKey
     * @param $topicKey
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showCooperators($type, $cbKey, $topicKey)
    {
        $cb = CB::getCbConfigurations($cbKey);

        $title = trans('privateIdeas.show_cooperators');

        $cbAuthor = Auth::getUserByKey($cb->created_by);

        $sidebar = 'topics';
        $active = 'cooperators';

        Session::put('sidebarArguments', ['type' => $type, 'cbKey' => $cbKey, 'activeFirstMenu' => 'cooperators']);
        Session::put('sidebars', [0 => 'private', 1 => 'topics']);

        return view('private.cbs.cooperators', compact('title', 'cb', 'cbKey', 'type', 'cbAuthor', 'sidebar', 'active', 'topicKey'));
    }

    /**
     * @param Request $request
     * @param $type
     * @param $cbKey
     * @param $topicKey
     * @return mixed
     */
    public function showCooperatorsTable(Request $request, $type, $cbKey, $topicKey)
    {
        $topicCooperators = CB::getCooperatorsList($request, $topicKey);
        $collection = isset($topicCooperators->cooperators) ? Collection::make($topicCooperators->cooperators) : Collection::make([]);
        $recordsTotal = $topicCooperators->recordsTotal;
        $recordsFiltered = $topicCooperators->recordsFiltered;
        $permissions = $topicCooperators->permissions;

        return Datatables::of($collection)
            ->addColumn('name', function ($collection) {
                return $collection->name;
            })
            ->addColumn('permissions', function ($collection) use($permissions, $topicKey){
                $toReturn = "<div class='col-xs-3 col-md-6'>
                            <select id='permission' style='width:100%;' class='form-control permission_select' onchange=\"changePermissions(this,'".$collection->user_key."', '".$topicKey."')\" name=''>";

                foreach ($permissions as $permission) {
                    if($collection->type_id == $permission->id){
                        $toReturn .= "<option selected=\"selected\" value=\"$permission->id\">$permission->name</option>";
                    }
                    else{
                        $toReturn .= "<option value=\"$permission->id\">$permission->name</option>";
                    }
                }

                return $toReturn . "</select></div>";
            })
            ->addColumn('action', function ($collection) use($topicKey, $type, $cbKey){
                return ONE::actionButtons(['type' => $type, 'cbKey' => $cbKey, 'topicKey' => $topicKey, 'userKey' => $collection->user_key], ['delete' => 'TopicController@deleteCooperator']);
            })
            ->with('filtered', $recordsFiltered ?? 0)
            ->skipPaging()
            ->setTotalRecords($recordsTotal ?? 0)
            ->make(true);
    }

    /**
     * @param Request $request
     * @param $topicKey
     * @return mixed
     */
    public function addCooperator(Request $request, $topicKey){
        $response = Orchestrator::setCooperators($topicKey, $request->cooperatorsKey);
        Session::flash('message', trans('cooperators.store_ok'));
        return $response;
    }

    /**
     * @param $type
     * @param $cbKey
     * @param $topicKey
     * @param $userKey
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function deleteCooperator($type, $cbKey, $topicKey, $userKey){
        $data = array();

        $data['action'] = action("TopicController@destroyCooperator", ['type' => $type, 'cbKey' => $cbKey, 'topicKey' => $topicKey, 'userKey' => $userKey]);
        $data['title'] = "DELETE";
        $data['msg'] = "Are you sure you want to delete this Topic?";
        $data['btn_ok'] = "Delete";
        $data['btn_ko'] = "Cancel";

        return view("_layouts.deleteModal", $data);
    }

    /**
     * @param $type
     * @param $cbKey
     * @param $topicKey
     * @param $userKey
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function destroyCooperator($type, $cbKey, $topicKey, $userKey){
        try{
            Orchestrator::deleteCooperator($topicKey, $userKey);
            Session::flash('message', trans('cooperators.delete_ok'));
            return action('TopicController@showCooperators', ['type' => $type, 'cbKey' => $cbKey, 'topicKey' => $topicKey]);
        }catch(Exception $e) {
            Session::flash('error', trans('cooperators.delete_nok'));
            return redirect()->back()->withErrors(["cooperators.destroy" => $e->getMessage()]);
        }
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function entityUsers(Request $request){
        $response = Orchestrator::getEntityUsers($request);
        $collection = Collection::make($response->users);

        $recordsTotal = $response->recordsTotal;
        $recordsFiltered = $response->recordsFiltered;

        return Datatables::of($collection)
            ->addColumn('cooperatorCheckbox', function ($collection) {
                return "<div class='oneSwitch'><input onclick=\"toggleCooperatorItem(this,'".$collection->name."')\" type='checkbox' name='cooperators[]' value='".$collection->user_key."' class='oneSwitch-checkbox' id='cooperatorCheckbox_".$collection->user_key."'  ><label class='oneSwitch-label' for='cooperatorCheckbox_".$collection->user_key."'><span class='oneSwitch-inner'></span><span class='oneSwitch-switch'></span></label></div>";
            })
            ->addColumn('name', function ($collection) {
                return $collection->name;
            })
            ->with('filtered', $recordsFiltered ?? 0)
            ->skipPaging()
            ->setTotalRecords($recordsTotal ?? 0)
            ->make(true);
    }

    public function updateCooperatorPermission(Request $request, $topicKey){
        $response = Orchestrator::updateCooperatorPermission($topicKey, $request);
        return $response;
    }
}
