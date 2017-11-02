<?php

namespace App\Http\Controllers;

use App\ComModules\Auth;
use App\ComModules\CB;
use App\ComModules\Files;
use App\ComModules\Notify;
use App\ComModules\Orchestrator;
use App\ComModules\Vote;
use App\Http\Requests\ForumRequest;
use App\Http\Requests\PasswordUpdateRequest;
use App\Http\Requests\PostRequest;
use App\Http\Requests\UserRequest;
use App\One\One;
use Cache;
use Chencha\Share\Share;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Exception;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;
use App\User;
use Datatables;
use Mockery\Matcher\Not;
use Session;
use URL;
use View;
use Breadcrumbs;
use Faker\Factory as Faker;
use Illuminate\Pagination\LengthAwarePaginator;


class PublicUsersController extends Controller
{
    public function __construct()
    {

    }
    //Login, name, email, password

    /**
     * Display a listing of the resource.
     *
     * @return View
     */
    public function index()
    {
        try {

            $user = Auth::getUser();

            // ADDED TO VERIFY IF THE EMAIL IS FAKE
            if(!empty($user->email)) {
                if (!empty(Session::get("SITE-CONFIGURATION.boolean_no_email_needed"))) {
                    if (!empty(Session::get("SITE-CONFIGURATION.user_email_domain"))) {
                        $userEmailPieces = explode('@', $user->email);
                        $userEmailDomain = $userEmailPieces[count($userEmailPieces) - 1];
                        if ($userEmailDomain == Session::get("SITE-CONFIGURATION.user_email_domain")) {
                            $user->hide_fake_email = true;
                        }
                    }
                }
            }
            $votesTimeLine = [];
            $sort = [];
            $timeline = [];
//            $userVotes = Vote::getVotesTimeLine();
//            foreach ($userVotes as $key => $vote)
//            {
//                $topic = CB::getTopic($vote->vote_key);
//                if($vote->value == 1){
//                    $icon = 'fa fa-thumbs-o-up bg-green';
//                    $type = 'positive';
//                }
//                else{
//                    $icon = 'fa fa-thumbs-o-down bg-red';
//                    $type = 'negative';
//                }
//                $date = Carbon::parse($vote->created_at)->toDateString();
//                $time = Carbon::parse($vote->created_at)->toTimeString();
//                $votesTimeLine [] = ['icon'=> $icon,'message' => 'Added '.$type.' vote to "'.$topic->title.'"','date' => $date,'time' => $time];
//                $sort[] = strtotime($vote->created_at);
//
//                if($vote->deleted_at != null){
//                    $time = Carbon::parse($vote->deleted_at)->toTimeString();
//                    $date = Carbon::parse($vote->deleted_at)->toDateString();
//                    $votesTimeLine [] = ['icon' => "fa fa-times bg-red", 'message' => 'Removed '.$type.' vote from "'.$topic->title.'"','date' => $date,'time' => $time];
//                    $sort[] = strtotime($vote->deleted_at);
//                }
//            }
//            $userPosts = CB::getUserPosts();
//
//            $postsTimeline = [];
//            foreach ($userPosts as $post){
//                $topic = CB::getTopic($post->topic_id);
//                $icon = 'fa fa-comments bg-yellow';
//                $date = Carbon::parse($post->created_at)->toDateString();
//                $time = Carbon::parse($post->created_at)->toTimeString();
//                $postsTimeline [] = ['icon' => $icon, 'message' => 'Added post to "'.$topic->title, 'date' => $date, 'time' => $time,'content' => $post->contents];
//                $sort[] = strtotime($post->created_at);
//
//                if($post->deleted_at != null){
//                    $date = Carbon::parse($post->created_at)->toDateString();
//                    $time = Carbon::parse($post->created_at)->toTimeString();
//                    $postsTimeline [] = ['icon'=> "fa fa-times bg-red",'message' => 'Removed post from "'.$topic->title.'"','date' => $date,'time' => $time,'content' => $post->contents];
//                    $sort[] = strtotime($post->deleted_at);
//                }
//            }
//            $timeline =  array_merge($votesTimeLine,$postsTimeline);
//            array_multisort($sort, SORT_DESC, $timeline);


            return view('public.'.ONE::getEntityLayout().'.user.index', compact('user','timeline'));

        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["public.user.show" => $e->getMessage()]);
        }
    }


    /**
     * Create a new resource.
     *
     * @return View
     */
    public function create()
    {
        return view('public.'.ONE::getEntityLayout().'.user.user');
    }


    /**
     * Display the specified resource.
     *
     * @param  $userKey
     * @return View
     */
    public function show(Request $request, $userKey)
    {
        try {
            $this->clearCache();

            $registerCompleted = true;
            $user = Auth::getUser();

            // ADDED TO VERIFY IF THE EMAIL IS FAKE
            if(!empty($user->email)) {
                if (!empty(Session::get("SITE-CONFIGURATION.boolean_no_email_needed"))) {
                    if (!empty(Session::get("SITE-CONFIGURATION.user_email_domain"))) {
                        $userEmailPieces = explode('@', $user->email);
                        $userEmailDomain = $userEmailPieces[count($userEmailPieces) - 1];
                        if ($userEmailDomain == Session::get("SITE-CONFIGURATION.user_email_domain")) {
                            $user->hide_fake_email = true;
                        }
                    }
                }
            }
            $userParametersResponse = json_decode(json_encode($user->user_parameters),true);
            $registerParametersResponse = Orchestrator::getEntityRegisterParameters();

            //verify user parameters with responses
            $registerParameters = [];
            foreach ($registerParametersResponse as $parameter){
                $parameterOptions = [];
                $value = '';
                $file = null;
                if($parameter->parameter_type->code == 'radio_buttons' || $parameter->parameter_type->code == 'check_box' || $parameter->parameter_type->code == 'dropdown' || $parameter->parameter_type->code == 'gender') {
                    foreach ($parameter->parameter_user_options as $option) {
                        $selected = false;
                        if (isset($userParametersResponse[$parameter->parameter_user_type_key])) {
                            foreach ($userParametersResponse[$parameter->parameter_user_type_key] as $userOption) {
                                if($userOption['value'] == $option->parameter_user_option_key){
                                    $selected = true;
                                    break;
                                }
                            }
                        }
                        $parameterOptions [] = [
                            'parameter_user_option_key' => $option->parameter_user_option_key,
                            'name' => $option->name,
                            'selected' => $selected
                        ];
                    }
                }elseif($parameter->parameter_type->code == 'file'){
                    $id = isset($userParametersResponse[$parameter->parameter_user_type_key][0]) ? $userParametersResponse[$parameter->parameter_user_type_key][0]['value'] : '';
                    if($id != ''){
                        $file = json_decode(json_encode(Files::getFile($id)),true);
                    }

                }else{
                    $value = isset($userParametersResponse[$parameter->parameter_user_type_key][0]) ? $userParametersResponse[$parameter->parameter_user_type_key][0]['value'] : '';
                }
                $registerParameters []= [
                    'parameter_user_type_key'   => $parameter->parameter_user_type_key,
                    'parameter_type_code'       => $parameter->parameter_type->code,
                    'name'                      => $parameter->name,
                    'value'                     => isset($file) ? $file : $value,
                    'mandatory'                 => $parameter->mandatory,
                    'parameter_user_options'    => $parameterOptions
                ];
            }
            $userValidate = Orchestrator::getUserAuthValidate();
            if(!empty($userValidate->status) && $userValidate->status == 'registered'){
                $registerCompleted = false;
            }
            $uploadKey = Files::getUploadKey();
            $arrayVote=Vote::getEventLevelCbKey('t711bwqTXj3GSGiEVwa3li3YZDqvq4pL');

            $differenceUserLevelsLoginVotes= Orchestrator::UserLoginLevelsVotes($user->user_key, 't711bwqTXj3GSGiEVwa3li3YZDqvq4pL', $arrayVote);
            $i = 0;
            foreach($differenceUserLevelsLoginVotes as $security){
                if(!empty($security->parameterUserTypes)){

                    /*if(!in_array('email_verification', $security->parameterUserTypes))
                        $i++;
                    elseif(in_array('email_verification', $security->parameterUserTypes) && $user->confirmed == 0)
                        $i++;
                    elseif(in_array('email_verification', $security->parameterUserTypes) && $user->confirmed == 1 && count($security->parameterUserTypes) > 1)*/
                    $i++;
                }

            }

            $missing_fields = $i>0 ? true : false;

            return view('public.'.ONE::getEntityLayout().'.user.index', compact('registerParameters','user', 'registerCompleted','userParameters', 'uploadKey', 'missing_fields'));

        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["public.user.show" => $e->getMessage()]);
        }
    }

    /**
     * Create a new resource.
     *
     * @return \Illuminate\Http\RedirectResponse|View
     */
    public function edit(Request $request, $userKey)
    {
        try{
            $this->clearCache();
            $registerCompleted = true;
            $user = Auth::getUser();
            // ADDED TO VERIFY IF THE EMAIL IS FAKE
            if(!empty($user->email)) {
                if (!empty(Session::get("SITE-CONFIGURATION.boolean_no_email_needed"))) {
                    if (!empty(Session::get("SITE-CONFIGURATION.user_email_domain"))) {
                        $userEmailPieces = explode('@', $user->email);
                        $userEmailDomain = $userEmailPieces[count($userEmailPieces) - 1];
                        if ($userEmailDomain == Session::get("SITE-CONFIGURATION.user_email_domain")) {
                            $user->hide_fake_email = true;
                        }
                    }
                }
            }
            $userParametersResponse = json_decode(json_encode($user->user_parameters),true);
            $registerParametersResponse = Orchestrator::getEntityRegisterParameters();
            //verify user parameters with responses
            $registerParameters = [];
            foreach ($registerParametersResponse as $parameter){
                $parameterOptions = [];
                $value = '';
                $file = null;
                if($parameter->parameter_type->code == 'radio_buttons' || $parameter->parameter_type->code == 'check_box' || $parameter->parameter_type->code == 'dropdown' || $parameter->parameter_type->code == 'gender') {
                    foreach ($parameter->parameter_user_options as $option) {
                        $selected = false;
                        if (isset($userParametersResponse[$parameter->parameter_user_type_key])) {
                            foreach ($userParametersResponse[$parameter->parameter_user_type_key] as $userOption) {
                                if($userOption['value'] == $option->parameter_user_option_key){
                                    $selected = true;
                                    break;
                                }
                            }
                        }
                        $parameterOptions [] = [
                            'parameter_user_option_key' => $option->parameter_user_option_key,
                            'name' => $option->name,
                            'selected' => $selected
                        ];
                    }
                }elseif($parameter->parameter_type->code == 'file'){
                    $id = isset($userParametersResponse[$parameter->parameter_user_type_key][0]) ? $userParametersResponse[$parameter->parameter_user_type_key][0]['value'] : '';
                    if($id != ''){
                        $file = json_decode(json_encode(Files::getFile($id)),true);
                    }

                }else{
                    $value = isset($userParametersResponse[$parameter->parameter_user_type_key][0]) ? $userParametersResponse[$parameter->parameter_user_type_key][0]['value'] : '';
                }
                $registerParameters []= [
                    'parameter_user_type_key'   => $parameter->parameter_user_type_key,
                    'parameter_type_code'       => $parameter->parameter_type->code,
                    'name'                      => $parameter->name,
                    'value'                     => isset($file) ? $file : $value,
                    'mandatory'                 => $parameter->mandatory,
                    'parameter_user_options'    => $parameterOptions
                ];
            }
            $userValidate = Orchestrator::getUserAuthValidate();
            if(!empty($userValidate->status) && $userValidate->status == 'registered'){
                $registerCompleted = false;
            }
            $uploadKey = Files::getUploadKey();

            if(isset($request->view)){
                return view('public.'.ONE::getEntityLayout().'.user.completeFields', compact('registerParameters','user', 'registerCompleted','userParameters', 'uploadKey'));
            }

            return view('public.'.ONE::getEntityLayout().'.user.user', compact('registerParameters','user', 'registerCompleted','userParameters', 'uploadKey'));

        }catch (Exception $e){
            return redirect()->back()->withErrors([trans('publicUsers.editUserProfile') => $e->getMessage()]);
        }
    }


    public function clearCache()
    {
        if(!empty(preg_grep('/^topics_list_/', array_keys(Session::all())))){
            Session::forget(current(preg_grep('/^topics_list_/', array_keys(Session::all()))));
        }
    }
    /**
     * Update the specified resource in storage.
     *
     * @param UserRequest $requestUser
     * @param $user_key
     * @return PublicUsersController|\Illuminate\Http\RedirectResponse
     */
    public function update(UserRequest $requestUser, $user_key)
    {
        try{
            $this->clearCache();
            $userDetails = $requestUser->all();

            if(!empty($requestUser->password) && strlen($requestUser->password ) <= 2 )
                return redirect()->back()->withErrors([trans('publicUsers.profileUpdatedNOK') => trans('publicUsers.passwordMin3Chars')]);

            $data['name'] = $requestUser->name;
            $data['email'] = $requestUser->email;
            $data['password'] = isset($requestUser->password) ? $requestUser->password : null;
            $data['identity_card'] = isset($requestUser->identity_card) ? $requestUser->identity_card : null;
            $data['vat_number'] = isset($requestUser->vat_number) ? $requestUser->vat_number : null;
            unset($userDetails['_token']);
            unset($userDetails['form_name']);
            unset($userDetails['_method']);
            unset($userDetails['name']);
            unset($userDetails['email']);
            unset($userDetails['identity_card']);
            unset($userDetails['emailInputs']);
            unset($userDetails['stepper']);
            unset($userDetails['codeVerification']);
            if(isset($userDetails['password_confirmation'])){
                unset($userDetails['password_confirmation']);
            }
            if(isset($userDetails['password'])){
                unset($userDetails['password']);
            }
            if(isset($userDetails['vat_number'])){
                unset($userDetails['vat_number']);
            }
            foreach ($userDetails as $key => $userDetail){
                if (!isset($userDetail)){
                    unset($userDetails[$key]);
                }
            }

            //          Update User Level
            $user = Auth::updateUser($user_key,$data,$userDetails);

            $user_parameter = [];
            if ( isset($user->user_parameters) ){
                $user_parameter =  (array_keys(json_decode(json_encode($user->user_parameters), true)));
            }

            /** @deprecated - user level - to remove */
            //$response = Orchestrator::updateUserLevel($user_key,$user_parameter);

//            if (!empty($response->position)){
//                $user->user_level =  $response->position;
//            }else {
//                $user->user_level = 0;
//            }


            $userValidate = Orchestrator::getUserAuthValidate();

            if(!empty($userValidate->status) && $userValidate->status == 'registered'){
                $userStatus = Orchestrator::updateUserStatus('complete');
                Session::put('USER-STATUS', $userStatus->status);
            }

            Session::put('user', $user);

            if (isset($user->new_email) && ($user->new_email == 1)){
                $emailType = 'registry_confirmation';
                $tags = [
                    "name" => $user->name,
                    "link" => URL::action('AuthController@confirmEmail', $user->confirmation_code)
                ];
                Notify::sendEmail($emailType, $tags, (array) $user);
            }

            /** Check and update user Login Level*/
            $userLoginLevels = Orchestrator::checkAndUpdateUserLoginLevel($user_key);
            $user->user_login_levels = $userLoginLevels;

            /** ADDED BECAUSE OF THE JUSTIFICATION MESSAGE */
            if(!empty($requestUser->message)){
                Orchestrator::sendMessage($requestUser);
            }

            Session::flash('message', trans('publicUsers.profileUpdatedOK'));

            if(isset($requestUser->stepper)) {
                if (empty($data["phone_number"])) {
                    $parameters = Orchestrator::getParameterUserTypes();
                    foreach ($parameters as $parameter) {
                        if (!empty($parameter->parameter_type->code) && $parameter->parameter_type->code == 'mobile') {
                            $parameterKey = $parameter->parameter_user_type_key;
                            if (!empty($user->user_parameters->{$parameterKey}[0])) {
                                $data['phone_number'] = $user->user_parameters->{$parameterKey}[0]->value;
                                break;
                            }
                        }
                    }
                }
                Session::put('phone_number', $data['phone_number']);
//                if($requestUser->showCode){
//                    $data['thirdStepActive'] = false;
//                    $data['secondStepActive'] = true;
//                    $data['firstStepActive'] = true;
//                    $data['next'] = true;
//
//                    return view('public.'.ONE::getEntityLayout().'.auth.newStepper.smsValidation', $data);
//                }

                if($requestUser->codeVerification){
                    $data['thirdStepActive'] = false;
                    $data['secondStepActive'] = true;
                    $data['firstStepActive'] = true;
                    $data['phone_number'] = Session::get('phone_number');

                    $cantSendSmsMessage = false;
                    if(isset($user->sms_sent) && Session::has('SITE-CONFIGURATION.sms_max_send') && $user->sms_sent >= Session::get('SITE-CONFIGURATION.sms_max_send'))
                        $cantSendSmsMessage = true;

                    $data['cantSendSmsMessage'] = $cantSendSmsMessage;


                    return view('public.'.ONE::getEntityLayout().'.auth.newStepper.verificationCode', $data);
                }

                if (!empty($requestUser->get("emailInputs"))) {
                    foreach ($requestUser->get("emailInputs", []) as $emailInput) {

                        if (!empty($requestUser->get($emailInput, ""))) {
                            $confirmationCode = $user->user_parameters->{$emailInput}[0]->confirmation_code ?? "";

                            if (!empty($confirmationCode)) {
                                $emailAddress = $requestUser->get($emailInput, "");
                                AuthController::sendConfirmEmailForEmailParameter($user->name, $emailAddress, $confirmationCode);
                            }
                        }
                    }

                }

                return redirect()->action('AuthController@stepperManager', ['step' => $requestUser->stepper, 'sms_confirmation_skip' => $requestUser->sms_verification_skip]);

            }
//            dd($requestUser->stepper);
            return redirect()->back();
        }catch (Exception $e){
            return redirect()->back()->withErrors([trans('publicUsers.profileUpdatedNOK') => $e->getMessage()]);
        }
    }


    /**
     * Add Files to specific content.
     *
     * @param Request $request
     * @return Response
     */
    public function addPhoto(PostRequest $request)
    {
        try{
            Auth::setUserPhoto(Session::get('user')->user_key,$request->file_id,$request->file_code);

            //after photo added saves it in user session
            $userInformation = Auth::getUser();
            Session::put('user', $userInformation);


            return action('FilesController@download', ['id' => $request->file_id, 'code' => $request->file_code, 1] ) ;
        } catch(Exception $e) {
            return "false";
            return $e->getMessage();
        }
    }


    /**
     * Add Files to specific content.
     *
     * @param Request $request
     * @return string
     */
    public function fileUpload(Request $request)
    {
        try{
            $file = json_decode(json_encode(Files::getFile($request->file_id)),true);
            return $file;
        }
        catch(Exception $e) {
            return "false";
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param PasswordUpdateRequest $request
     * @return string
     */
    public function updatePassword(PasswordUpdateRequest $request)
    {
        try{
            $userKey = Session::has('user')? Session::get('user')->user_key : null;
            $oldPassword = $request->old_password;
            $password = $request->password;
            $user = Auth::updateUserPassword($oldPassword,$password);
            Session::flash('message', trans('publicUsers.updatePasswordOk'));

            return redirect()->action('PublicUsersController@edit',['userKey' => $userKey,'f' => 'user']);
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors([trans('publicUsers.updatePasswordNOk') => $e->getMessage()]);
        }
    }

    public function showTimeline(Request $request, $type) {
        try {
            $user = Auth::getUser();
            $timeline = $this->getTimeline($request,$type,$request->input("page") ?? 1);
            $title = trans("publicUserTimeline." . $type . "Title");

            return view('public.'.ONE::getEntityLayout().'.user.timeline', compact('title','user','timeline'))->with(["lastDate"=>null,"profileSection"=>"timeline","timelineSection"=>$type]);
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["public.user.show" => $e->getMessage()]);
        }
    }
    private function getTimeline(Request $request, $type, $page = 1) {
        $itemsPerPage = env("USER_TIMELINE_ITEMS_PER_PAGE",10);
        try {
            if ($type=="votes") {
                $timeline = (array)Vote::getVotesTimeLine();
                $timeline = new LengthAwarePaginator(
                    array_slice($timeline, ($page * $itemsPerPage) - $itemsPerPage, $itemsPerPage, true),
                    count($timeline),
                    $itemsPerPage,
                    $page,
                    [
                        'path' => $request->url(),
                        'query' => $request->query()
                    ]
                );

                foreach($timeline as $key=>&$vote) {
                    try {
                        $topic = CB::getTopicForTimeline($vote->vote_key);

                        $cbType = Orchestrator::getCbTypesByCbKey($topic->cb->cb_key)->code ?? null;
                        if (!is_null($topic->topic->deleted_at)) {
                            $link = "<span class='bottom-dashed' title='" . trans("publicUserTimeline.topic_deleted") . "'>" .
                                ($topic->topic->title ?? trans("publicUserTimeline.untitledTopic")) . "</span>";
                        } else if (!is_null($cbType))
                            $link = "<a href='" . action("PublicTopicController@show", ["cb" => $topic->cb->cb_key, "topic" => $topic->topic->topic_key, "type" => $cbType]) . "'>" .
                                ($topic->topic->title ?? trans("publicUserTimeline.untitledTopic")) . "</a>";
                        else
                            $link = ($topic->topic->title ?? trans("publicUserTimeline.untitledTopic"));

                        $vote->order_date = Carbon::parse($vote->order_date->date);
                        $vote->message = array(
                            'date' => $vote->order_date->toDateString(),
                            'time' => $vote->order_date->format("H:i")
                        );

                        if ($vote->value == 1) {
                            $vote->message["icon"] = 'fa fa-thumbs-o-up';
                            $vote->message["icon-bg"] = "color-green";
                            $voteType = "positive";
                        } else {
                            $vote->message["icon"] = 'fa fa-thumbs-o-down';
                            $vote->message["icon-bg"] = "color-red";
                            $voteType = "negative";
                        }

                        if ($vote->deleted_at==$vote->order_date) {
                            $vote->message["message"] = trans("publicUserTimeline.removedVoteFrom", ["type" => trans("publicUserTimeline." . $voteType . "Vote"), "link" => $link]);
                            $vote->message["crossed"] = true;
                        } else
                            $vote->message["message"] = trans("publicUserTimeline.addedVoteTo", ["type" => trans("publicUserTimeline." . $voteType . "lyVote"), "link" => $link]);
                    } catch (Exception $e) {
                        continue;
                    }
                }
            } else if ($type=="posts") {
                $timeline = collect(CB::getUserPosts())->toArray();
                $timeline = new LengthAwarePaginator(
                    array_slice($timeline, ($page * $itemsPerPage) - $itemsPerPage, $itemsPerPage, true),
                    count($timeline),
                    $itemsPerPage,
                    $page,
                    [
                        'path' => $request->url(),
                        'query' => $request->query()
                    ]
                );

                foreach ($timeline as $key=>$post) {
                    try {
                        $cbType = Orchestrator::getCbTypesByCbKey($post->cb_key)->code ?? null;
                        if (!is_null($cbType))
                            $link = "<a href='" . action("PublicTopicController@show", ["cb" => $post->cb_key, "topic" => $post->topic_key, "type" => $cbType]) . "'>" .
                                ($post->topic_title ?? trans("publicUserTimeline.untitledTopic")) . "</a>";
                        else
                            $link = ($post->topic_title ?? trans("publicUserTimeline.untitledTopic"));

                        $post->message = array(
                            'date' => Carbon::parse($post->created_at)->toDateString(),
                            'time' => Carbon::parse($post->created_at)->format("H:i"),
                            'content' => $post->contents
                        );

                        if ($post->deleted_at != null) {
                            $post->message["icon"] = "fa fa-times";
                            $post->message["icon-bg"] = "color-red";
                            $post->message["message"] = trans("publicUserTimeline.removedPost",["post"=>($post->topic_title ?? trans("publicUserTimeline.untitledTopic"))]);
                        } else if ($post->edition) {
                            $post->message["icon"] = "fa fa-pencil";
                            $post->message["icon-bg"] = "color-yellow";
                            $post->message["message"] = trans("publicUserTimeline.editedPost", ["link" => $link]);
                        } else {
                            $post->message["icon"] = "fa fa-plus";
                            $post->message["icon-bg"] = "color-blue";
                            $post->message["message"] = trans("publicUserTimeline.createdPost",["link"=>$link]);
                        }
                    } catch (Exception $e) {
                        continue;
                    }
                }
            } else { //Caso contrário, só podem ser os topicos dada a regra na rota
                $timeline = (array)CB::getUserTopicsTimeline(Session::get('user')->user_key)->topics;
                $timeline = new LengthAwarePaginator(
                    array_slice($timeline, ($page * $itemsPerPage) - $itemsPerPage, $itemsPerPage, true),
                    count($timeline),
                    $itemsPerPage,
                    $page,
                    [
                        'path' => $request->url(),
                        'query' => $request->query()
                    ]
                );

                foreach ($timeline as $key=>$topic) {
                    try {
                        $cbType = Orchestrator::getCbTypesByCbKey($topic->cb_key)->code ?? null;
                        if (!is_null($cbType))
                            $link = "<a href='" . action("PublicTopicController@show", ["cb" => $topic->cb_key, "topic" => $topic->topic_key, "type" => $cbType]) . "'>" .
                                ($topic->title ?? trans("publicUserTimeline.untitledTopic")) . "</a>";
                        else
                            $link = ($topic->title ?? trans("publicUserTimeline.untitledTopic"));

                        $topic->message = array(
                            'icon' => 'fa fa-comments',
                            'icon-bg' => "color-blue",
                            'message' => trans("publicUserTimeline.createdTopic",["link"=>$link]),
                            'date' => Carbon::parse($topic->created_at)->toDateString(),
                            'time' => Carbon::parse($topic->created_at)->format("H:i"),
                            'content' => $topic->contents
                        );

                    } catch (Exception $e) {
                        continue;
                    }
                }
            }

            return $timeline;
        } catch (Exception $e) {
            return [];
        }
    }


    /**
     * @param Request $request
     * @return View
     */
    public function fillLevelInfo(Request $request)
    {
        try{
            $user = Session::get('user');
            $siteLoginLevels = Orchestrator::getSiteLoginLevels();

            $userParametersResponse = json_decode(json_encode($user->user_parameters),true);
            $registerParametersResponse = Orchestrator::getEntityRegisterParameters();

            $parametersTotal = 0;
            $parametersFilled = count($userParametersResponse);

            $parameterUserTypes = [];
            foreach ($siteLoginLevels as $loginLevel){
                $parametersTotal += count($loginLevel->parameter_user_types);
                if ($loginLevel->position == $user->user_level + 1){
                    if(!empty($loginLevel->parameter_user_types)){
                        foreach ($loginLevel->parameter_user_types as $parameter_user_type){
                            $parameterUserTypes[] = $parameter_user_type->parameter_user_type_key;
                        }
                    }
                }
            }

            $parametersPercentageFilled = ($parametersFilled*100)/$parametersTotal;

            //verify user parameters with responses
            $registerParameters = [];
            foreach ($registerParametersResponse as $parameter) {
                if (in_array($parameter->parameter_user_type_key, $parameterUserTypes)) {
                    $parameterOptions = [];
                    $value = '';
                    $file = null;
                    if ($parameter->parameter_type->code == 'radio_buttons' || $parameter->parameter_type->code == 'check_box' || $parameter->parameter_type->code == 'dropdown') {
                        foreach ($parameter->parameter_user_options as $option) {
                            $selected = false;
                            if (isset($userParametersResponse[$parameter->parameter_user_type_key])) {
                                foreach ($userParametersResponse[$parameter->parameter_user_type_key] as $userOption) {
                                    if ($userOption['value'] == $option->parameter_user_option_key) {
                                        $selected = true;
                                        break;
                                    }
                                }
                            }
                            $parameterOptions [] = [
                                'parameter_user_option_key' => $option->parameter_user_option_key,
                                'name' => $option->name,
                                'selected' => $selected
                            ];
                        }
                    } elseif ($parameter->parameter_type->code == 'file') {
                        $id = isset($userParametersResponse[$parameter->parameter_user_type_key][0]) ? $userParametersResponse[$parameter->parameter_user_type_key][0]['value'] : '';
                        if ($id != '') {
                            $file = json_decode(json_encode(Files::getFile($id)), true);
                        }

                    } else {
                        $value = isset($userParametersResponse[$parameter->parameter_user_type_key][0]) ? $userParametersResponse[$parameter->parameter_user_type_key][0]['value'] : '';
                    }
                    $registerParameters [] = [
                        'parameter_user_type_key' => $parameter->parameter_user_type_key,
                        'parameter_type_code' => $parameter->parameter_type->code,
                        'name' => $parameter->name,
                        'value' => isset($file) ? $file : $value,
                        'mandatory' => $parameter->mandatory,
                        'parameter_user_options' => $parameterOptions
                    ];
                }
            }

            $data['parametersPercentageFilled'] = $parametersPercentageFilled;
            $data['registerParameters'] = $registerParameters;
            $data['user'] = $user;

            if(View::exists('public.'.ONE::getEntityLayout().'.auth.stepper')){
                return view('public.'.ONE::getEntityLayout().'.auth.stepper', $data);
            }

            return redirect()->action('PublicUsersController@edit',['userKey' => Session::get('user')->user_key,'f' => 'user']);

        }
        catch (Exception $e) {
            return redirect()->back()->withErrors(["user.updateProfile_fail" => $e->getMessage()]);
        }
    }

    /**
     * @param UserRequest $requestUser
     * @param $user_key
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function updateLevelInfo(UserRequest $requestUser, $user_key)
    {
        try{
            $userDetails = $requestUser->all();

            if(!empty($requestUser->password) && strlen($requestUser->password ) <= 2 )
                return redirect()->back()->withErrors([trans('publicUsers.profileUpdatedNOK') => trans('publicUsers.passwordMin3Chars')]);

            $data['name'] = $requestUser->name;
            $data['email'] = $requestUser->email;
            $data['password'] = isset($requestUser->password) ? $requestUser->password : null;
            $data['identity_card'] = isset($requestUser->identity_card) ? $requestUser->identity_card : null;
            $data['vat_number'] = isset($requestUser->vat_number) ? $requestUser->vat_number : null;
            unset($userDetails['_token']);
            unset($userDetails['form_name']);
            unset($userDetails['_method']);
            unset($userDetails['name']);
            unset($userDetails['email']);
            unset($userDetails['identity_card']);
            if(isset($userDetails['password_confirmation'])){
                unset($userDetails['password_confirmation']);
            }
            if(isset($userDetails['password'])){
                unset($userDetails['password']);
            }
            if(isset($userDetails['vat_number'])){
                unset($userDetails['vat_number']);
            }

            foreach ($userDetails as $key => $userDetail){
                if (empty($userDetail)){
                    unset($userDetails[$key]);
                }
            }

            $user = Auth::updateUser($user_key,$data,$userDetails);

            //Update User Level
            $userLevel = Orchestrator::updateUserLevel($user_key, arrary_keys(json_decode(json_encode($user->user_parameters),true)));

            if (!empty($userLevel->position)){
                $user->user_level = $userLevel->position;
            } else {
                $user->user_level = 0;
            }

            $userValidate = Orchestrator::getUserAuthValidate();
            if(!empty($userValidate->status) && $userValidate->status == 'registered'){
                $userStatus = Orchestrator::updateUserStatus('complete');
                Session::put('USER-STATUS', $userStatus->status);
            }


            $userLevel = Orchestrator::getUserLevel($user->user_key, $user->confirmed);
            $user->user_level = $userLevel->position ?? null;
            Session::put('user', $user);
            $siteLoginLevels = Orchestrator::getSiteLoginLevels();

            foreach ($siteLoginLevels as $loginLevel){
                if ($loginLevel->position == $user->user_level + 1){
                    if(empty($loginLevel->parameter_user_types)){
                        break;
                    } else {
                        return redirect()->action('PublicUsersController@fillLevelInfo', ['f' => 'levelForm']);
                    }
                }
            }

            return redirect()->action('PublicController@index');

        }catch (Exception $e){
            return redirect()->back()->withErrors([trans('publicUsers.profileUpdatedNOK') => $e->getMessage()]);
        }
    }

    /**
     * ------------------------------------------------------------
     * MESSAGES MODULE
     * ------------------------------------------------------------
     */

    /**
     * returns the view to display the messages in the profile
     * @return $this
     */
    public function showMessages() {
        try {
            $user = Auth::getUser();
            $title = trans("publicUserMessagesTitle");
            $messages = Orchestrator::getMessages();
            $userKeys = array_keys(collect($messages)->groupBy('from')->toArray());
            $messageUsers = Auth::listUser($userKeys);

            if($messageUsers) {
                foreach ($messageUsers as $messageUser) {
                    if($messages) {
                        foreach ($messages as $message) {
                            if ($message->from == $messageUser->user_key) {
                                $message->from_username = $messageUser->name . ' ' . ($messageUser->surname ?? '');
                            }
                        }
                    }
                }
            }

            $response = CB::getUserTopicsTimeline($user->user_key);
            $topics = $response->topics;

            return view('public.'.ONE::getEntityLayout().'.user.messages', compact('title','user','messages', 'topics'))->with(["profileSection"=>"messages"]);
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["public.user.show" => $e->getMessage()]);
        }
    }

    public function updatePrivacy(Request $request,$userKey)
    {
        try {
            $result = Auth::setPublicParameter($request->get("param"), ($request->get("value") == "false" ? 0 : 1));
            return response()->json(["response" => $result ? 1 : 0], ($result ? 200 : 500));
        } catch (Exception $e) {
            return response()->json(["error"], 500);
        }
    }


    /**
     * sends the message from the user to the entity
     * @param Request $request
     * @return $this
     */
    public function sendMessage(Request $request)
    {
        $url = "http" . (($_SERVER['SERVER_PORT'] == 443) ? "s://" : "://") . $_SERVER['HTTP_HOST'];
        $user = Auth::getUser();
        try {
            $tags = [
                "name"      => $user->name,
                "message"   => $request->message
            ];
            $response = Orchestrator::sendMessage($request,$tags);

            if ($response && $request->send_email == 'true'){

                $entityKey = Session::get('X-ENTITY-KEY');

                if($request->send_to_group){

                    //get entity name
                    $entity = Orchestrator::getPublicEntity($entityKey);

                    //Get Entity Managers to send message
                    $managers = Orchestrator::getAllManagersNoAuthNeeded();
                    $collectionManagers = Collection::make($managers);
                    $managersKeys = $collectionManagers->pluck('user_key');
                    $group = Auth::listUser($managersKeys);

                    //logged userKey
                    $user = Session::get('user');

                    //Notification for all Entity Managers
                    if(!empty($group)){
                        $emailType = 'group_message_sent_notification';
                        $tags = [
                            "message"   => $request->message,
                            "sender"    => $entity->name ?? null,
                            "user_who_sent"    => $user->name ?? null,
                            "link" => $url,
                        ];
                        $response = Notify::sendEmailForMultipleUsers($emailType, $tags, $group);
                    }

                }else{
                    $entity = is_null($entityKey) ? null : Orchestrator::getEntity($entityKey);

                    $user = Auth::getUserByKey($request->to);

                    $emailType = 'message_notification';
                    $tags = [
                        "name"      => $user->name,
                        "message"   => $request->message,
                        "sender"    => $entity->name ?? null,
                        "link"      => $url
                    ];
                    Notify::sendEmail($emailType, $tags, (array) $user);
                }
            }

            return 'success';
        }
        catch(Exception $e) {
            return 'error';
        }
    }


    public function deleteMessage(Request $request)
    {
        try {
            Orchestrator::deleteMessage($request);
            return 'success';
        }
        catch(Exception $e) {
            return 'error';
        }
    }

    /**
     * sends the message from the user to the entity
     * @param Request $request
     * @return $this
     */
    public function markMessagesAsSeen(Request $request)
    {
        try {
            Orchestrator::markMessagesAsSeen($request);
            return 'success';
        }
        catch(Exception $e) {
            return 'error';
        }
    }

    public function publicProfile(Request $request, $userKey) {
        try {
            $user = Auth::getUserParameters($userKey);
            $userParametersResponse = json_decode(json_encode($user->user_parameters),true);
            $registerParametersResponse = Orchestrator::getEntityRegisterParameters();
            //verify user parameters with responses
            $registerParameters = [];
            foreach ($registerParametersResponse as $parameter){
                $parameterOptions = [];
                $value = '';
                $file = null;
                if($parameter->parameter_type->code == 'radio_buttons' || $parameter->parameter_type->code == 'check_box' || $parameter->parameter_type->code == 'dropdown') {
                    foreach ($parameter->parameter_user_options as $option) {
                        $selected = false;
                        if (isset($userParametersResponse[$parameter->parameter_user_type_key])) {
                            foreach ($userParametersResponse[$parameter->parameter_user_type_key] as $userOption) {
                                if($userOption['value'] == $option->parameter_user_option_key){
                                    $selected = true;
                                    break;
                                }
                            }
                        }
                        $parameterOptions [] = [
                            'parameter_user_option_key' => $option->parameter_user_option_key,
                            'name' => $option->name,
                            'selected' => $selected,
                        ];
                    }
                }elseif($parameter->parameter_type->code == 'file'){
                    $id = isset($userParametersResponse[$parameter->parameter_user_type_key][0]) ? $userParametersResponse[$parameter->parameter_user_type_key][0]['value'] : '';
                    if($id != ''){
                        $file = json_decode(json_encode(Files::getFile($id)),true);
                    }

                }else{
                    $value = isset($userParametersResponse[$parameter->parameter_user_type_key][0]) ? $userParametersResponse[$parameter->parameter_user_type_key][0]['value'] : '';
                }
                $registerParameters []= [
                    'parameter_user_type_key'   => $parameter->parameter_user_type_key,
                    'parameter_type_code'       => $parameter->parameter_type->code,
                    'name'                      => $parameter->name,
                    'value'                     => isset($file) ? $file : $value,
                    'mandatory'                 => $parameter->mandatory,
                    'parameter_user_options'    => $parameterOptions,
                    'public_parameter'          => isset($userParametersResponse[$parameter->parameter_user_type_key][0]) ? $userParametersResponse[$parameter->parameter_user_type_key][0]['public_parameter'] : 0
                ];
            }
            return view('public.'.ONE::getEntityLayout().'.user.publicProfile', compact('user','registerParameters'));
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["public.user.profile" => $e->getMessage()]);
        }
    }




    public function userTopicsNewMethod(Request $request) {
        try {
            $user = Auth::getUser();

            if (!isset($request->ajax_call)) {
                return view('public.' . ONE::getEntityLayout() . '.user.topics', compact('user'));
            }

            $numberTopicToShow = $request->get("topics_to_show",$request->topics_to_show);

            //PREPARE ARGUMENTS TO SEND
            $filterList = collect(($request->all() ?? []))->toArray();

            $publicMultiplePadsInformation = CB::getMultiplePadsInformation($request->get("page",null), $numberTopicToShow, $request->all());

            $topicsPerPage = 10;
            $topicsPagination = $publicMultiplePadsInformation->topics;
            $usersNames = $publicMultiplePadsInformation->users;

//            //DEAL WITH FILES
            $fileTypes = [];
            $fileTypes["images"] = array("gif","jpg","png","bmp");
            $filesByType = [];

            foreach ($topicsPagination as $topic){
                if(!empty($topic->posts)){
                    foreach ($topic->posts as $post){
                        if(!empty($post->files)){
                            foreach ($post->files as $file) {
                                $array = explode('.', $file->name);
                                $extension = strtolower(end($array));
                                foreach ($fileTypes as $value) {
                                    if (in_array($extension, $value)) {
                                        $filesByType[$topic->topic_key] = json_decode(json_encode($file,JSON_FORCE_OBJECT));
                                        break;
                                    }
                                }
                            }
                        }
                    }
                }
            }

            $topicsPagination = new Paginator($topicsPagination, $numberTopicToShow, $request->page);

            // Prepare data to send to the view
            $data = [];
            if(isset($request->no_loop))
                $data['noLoop'] = true;

            $data['showHeader'] = true;
            $data['showStatistics'] = true;
            $data['cbParameters'] = isset($cbParameters) ? $cbParameters->parameters : null;
            $data['filterOptionSelected'] = isset($filterOptionSelected) ? $filterOptionSelected : null;
            $data['voteResults'] = isset($voteResults) ? $voteResults : null;
            $data['categoryColors'] = isset($categoryColors) ? $categoryColors : null;

//                $data['usersVotedInformation'] = $usersVotedInTopics;
            $data['filesByType'] = $filesByType;
            $data['usersNames'] = $usersNames;
            $data['allReadyVoted'] = $allReadyVoted ?? null;
            $data['remainingVotes'] = $remainingVotes ?? null;
            $data['cbsMenu'] = [];
            $data['voteKey'] = $voteKey ?? null;
            $data['categoriesNameById'] = [];
            $data['existVotes'] = $existVotes ?? null;
            $data['topicsLocation'] = [];
            $data['listType'] = [];
            $data['submittedProposal'] = [];
            $data['statusTypes'] = [];
//                    $data['parameters'] = $parameters;

//            }

            if(isset($request->no_loop))
                $data['noLoop'] = true;


            // This data is not to be cached
            $data['topicsTotals'] = $topicsPagination;
            $data['topicsPagination'] = $topicsPagination;
            $data['topics'] = $topicsPagination;
            $data['countTopics'] = sizeof($topicsPagination);
            $data['usersNames'] = $usersNames;
            $data['filesByType'] = $filesByType;
            $data['searchTerm'] = $request['search'];
            $data['securityConfigurations'] = [];
            $data['securityConfigurationsVotes']= [];
            $data["originalPageToken"] = $request->page ?? null;
            $data['pageToken'] = $publicMultiplePadsInformation->pageToken;
            $data['filterList'] = $filterList;
            $data['numberTopicToShow'] = $numberTopicToShow;

//            dd($filterList);



            return view('public.' . ONE::getEntityLayout() . '.user.topicsPads', $data)->with(['isAuth' => One::isAuth()]);



        } catch (Exception $exception) {
            dd($exception->getMessage());
            return redirect()->back()->withErrors(["public.user.topics" => $exception->getMessage()]);
        }
    }

    public function userTopics(Request $request) {
        try {

            $currentPageItems = LengthAwarePaginator::resolveCurrentPage();

            $topicsPerPage = 10;

            $user = Auth::getUser();
            $entityCbs = collect(Orchestrator::getAllCbs())->pluck("cb_key");
            $response = CB::getUserTopicsPaginated(Auth::getUser()->user_key,$topicsPerPage,$currentPageItems, $entityCbs);
            $topics = collect($response->topics);
            $totalTopics = $response->total ?? $topics->count();

            $topics = new LengthAwarePaginator($topics, $totalTopics, $topicsPerPage);
            $totalTopics = $response->total ?? $topics->count();

            $topics = new LengthAwarePaginator($topics, $totalTopics, $topicsPerPage);
            $topics->setPath($request->url());

            $cbTypes = collect(Orchestrator::getTypesByCbKeys($topics->getCollection()->unique("cb.cb_key")->pluck("cb.cb_key")->filter()));

            return view('public.'.ONE::getEntityLayout().'.user.topics',compact("user","topics","cbTypes","totalTopics"))->with(["profileSection"=>"topics"]);
        } catch (Exception $exception) {
            return redirect()->back()->withErrors(["public.user.topics" => $exception->getMessage()]);
        }
    }




    /**
     * BUILD THE OBJECT TO ATTACH A USER TO THE EVENT VOTE CODE
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|string
     */
    public function saveInPersonRegistration(Request $request)
    {
        try{
            $attachToCode = true;
            if(isset($request['doNotAttachToCode'])){
                $attachToCode = false;
            }
            $voteEventKey = $request['vote_event_key'];
            $faker = Faker::create();
            $inputs = $request['inputs'];
            $userBasicRegisterFields = [];
            $userParameterFields = collect($inputs)->filter(function($item){
                if(str_contains($item['name'],'parameter_')) {
                    return true;
                }
            });



            foreach ($inputs as $input){
                $userBasicRegisterFields[$input['name']] = $input['value'];
            }

            if(empty($userBasicRegisterFields['email'])){
                if(!isset($userBasicRegisterFields['surname'])){
                    $userBasicRegisterFields['surname'] = '';
                }
                $userBasicRegisterFields['email'] = AuthController::generateFakeEmail($userBasicRegisterFields['name'],$userBasicRegisterFields['surname']);
            }

            //WE NEED TO GENERATE A PASSWORD
            $userBasicRegisterFields['password'] = $faker->password;

            foreach ($userParameterFields as $parameterField){
                $userParameters[str_replace('parameter_','',$parameterField['name'])] = $parameterField['value'];
            }

            $storeUserResponse = $this->storeInPersonRegistration($userBasicRegisterFields, $userParameters);

            if(!$attachToCode){
                if(!isset($storeUserResponse['error'])){
                    if(isset($storeUserResponse['existed'])){ //USER ALREADY REGISTERED, DENY PERMISSION TO VOTE
                        return response()->json(["warning" => trans('comModulesAuth.userIsAlreadyRegistered')]);
                    }else{
                        return response()->json(["success" => $storeUserResponse['user_key']]);
                    }
                }else{
                    return response()->json(["error" => trans('comModulesAuth.errorInStoreUser')]);
                }
            }

            if(!isset($storeUserResponse['error'])){
                //ATTACH THIS USER TO THE VOTE EVENT WITH THE CODE ASSOCIATED
                $attachResponse = Vote::attachUserToVoteEventWithCode($storeUserResponse['user_key'],$voteEventKey,$userBasicRegisterFields['code']);
                if(is_array($attachResponse)){

                    return response()->json(["error" => $attachResponse['message']]);
                }else{
                    return $this->generateHtmlLineForUser($storeUserResponse);
                }
            }else{
                return response()->json(["error" => trans('comModulesAuth.errorInStoreUser')]);
            }


        }catch(Exception $e) {
            return response()->json(["error" => trans('comModulesAuth.errorInStoreUser')]);
        }
    }


    /**
     * STORES THE USER FOR IN PERSON REGISTRATION
     * @param $userBasicRegisterFields
     * @param $userParameters
     * @return array
     */
    public function storeInPersonRegistration($userBasicRegisterFields, $userParameters)
    {

        try {
            //STORE THE USER
            $storeNewUserResponse = Auth::storeInPersonRegistration($userBasicRegisterFields, $userParameters);
            //STORE FAILED
            if(!isset($storeNewUserResponse->user)){
                if($storeNewUserResponse['error'] == 408){ //comModulesAuth.parametersAlreadyUsed
                    $data['parameters'] = $userParameters;
                    $getUserResponse = Auth::getUserAccordingToSuppliedFields($search = 'PARAMETERS', $data);
                    $userInfo['user_key'] = $getUserResponse[0]->user_key;
                    $userInfo['name'] = $getUserResponse[0]->name;
                    $userInfo['surname'] = $getUserResponse[0]->surname ?? null;
                    $userInfo['existed'] = true;
                    return $userInfo;
                }elseif($storeNewUserResponse['error'] == 409){ //comModulesAuth.errorIdNumberOrEmailAlreadyExists
                    $getUserResponse = Auth::getUserAccordingToSuppliedFields($search = 'EMAIL', $userBasicRegisterFields);
                    $userInfo['user_key'] = $getUserResponse->user_key;
                    $userInfo['name'] = $getUserResponse->name;
                    $userInfo['surname'] = $getUserResponse->surname ?? null;
                    $userInfo['existed'] = true;
                    return $userInfo;
                }else{ //comModulesAuth.errorInStoreUser
                    return ['error' => trans('comModulesAuth.errorInStoreUser')];
                }

                //STORE OK
            }else{
                $user_key = $storeNewUserResponse->user->user_key;
                //ATTACH TO CURRENT ENTITY
                Orchestrator::storeUser($user_key,ONE::getEntityKey());

                $userInfo['user_key'] = $storeNewUserResponse->user->user_key;
                $userInfo['name'] = $storeNewUserResponse->user->name;
                $userInfo['surname'] = isset($storeNewUserResponse->user->surname) ?? '';
                return $userInfo;

            }

        }catch(Exception $e){
            return ['error' => trans('comModulesAuth.errorInStoreUser')];
        }
    }

    /**
     * @return $this|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showQuestionnaires() {
        try {
            $data['user']= Auth::getUser();
            $data['title'] = trans("publicUserQuestionnairesTitle");
            $data['questionnaires'] = Orchestrator::UserIgnoredQuestionnaires();

            return view('public.'.ONE::getEntityLayout().'.user.questionnaires',$data);
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["public.user.show" => $e->getMessage()]);
        }
    }

    public function verificationLandLine(){
        return view('public.'.ONE::getEntityLayout().'.auth.newStepper.verificationLandLine');
    }

    public function verificationCode(Request $request){
        $phoneNumber = $request->phone_number;

        return view('public.'.ONE::getEntityLayout().'.auth.newStepper.verificationCode', compact('phoneNumber'));
    }
}
