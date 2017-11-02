<?php

namespace App\Http\Controllers;

use App\ComModules\Auth;
use App\ComModules\EMPATIA;
use App\ComModules\Orchestrator;
use App\ComModules\Notify;
use App\ComModules\Questionnaire;
use App\Http\Requests\PostRequest;
use Carbon\Carbon;
//use App\Http\Requests\Request;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use PhpParser\Node\Expr\Cast\Object_;
use View;
use Breadcrumbs;
use Session;
use App\One\One;
use App\ComModules\CB;



class PublicPostController extends Controller
{

    private $commentType;

    public function __construct()
    {
        $this->commentType = [
            'positive' => 'positive',
            'neutral' => 'neutral',
            'negative' => 'negative'
        ];

        if(Route::current() == null) return;

        View::share('title', trans('post.title'));

        $this->topicId = Route::current()->getParameter('topicId');
        if($this->topicId != null){
            Session::set('topicId', $this->topicId);
        }


    }

    /**
     * Verify if comment type exists
     *
     * @param $type
     * @return bool|mixed
     * @throws Exception
     */
    private function getCommentType($type){
        $commentType = $this->commentType[$type] ??  false;
        if (!$commentType) {
            throw new Exception( "Error get comment type" );
        }
        return $commentType;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index($id)
    {

        try {
            $response = CB::getTopic($id);
            $topic = $response->topic;

            $response = CB::getTopicData($topic->id);

            $messages = $response->posts;
            $configurations = $response->configurations;

            $topicMessage = $messages[0];

            if (count($messages) > 1) {
                $messages = array_slice($messages, 1, (count($messages) - 1));
            } else {
                $messages = [];
            }

            // array of users
            $usersKeys = [];

            $usersKeys[] = $topic->created_by;
            foreach ($messages as $message) {
                $usersKeys[] = $message->created_by;
            }

            $usersNames = [];
            if (count($usersKeys) > 0) {
                $usersNames = Auth::getListNames($usersKeys);
            }
            //--------
            $userKey = Session::get('user')->user_key;
            $cbId = Session::get('cbId', 0);
            $topicId = $id;


            //Check if is a moderator
            $isModerator = 0;
            if(Session::has('user')) {
                //Get Managers

                $moderators = CB::getCbModerators($cbId);
                foreach($moderators as $moderator){

                    if($moderator->user_key == Session::get('user')->user_key)
                        $isModerator = 1;
                }
            }

            return view('public.'.ONE::getEntityLayout().'.forum.post.post', compact('messages', 'topic', 'topicMessage', 'cbId', 'topicId', 'usersNames', 'userKey', 'configurations', 'isModerator'));

        } catch (Exception $e) {
            return redirect()->back()->withErrors(["post.post" => $e->getMessage()]);
        }
    }

    public function sendEmailNotification($cbKey, $code, $type, $topic, $users, $owner){
        //get cb template
        $cbTemplates = CB::getCbTemplates($cbKey);
        foreach ($cbTemplates as $cbTemplate){
            if($cbTemplate->configuration_code  == $code){
                $template = $cbTemplate;
            }
        }

        if($template!=''){
            $usersEmail = [];
            foreach ($users as $user){
                $usersEmail[] = Orchestrator::getUserEmail($user->user_key);
            }

            if($owner!=null){
                $userEmail = Orchestrator::getUserEmail($owner);
                $usersEmail[] = $userEmail;
            }


            $userKey = (Session::get('user'))->user_key ?? "anonymous";

            $url = "<a href='".action('TopicController@show', [$type, $cbKey, $topic->topic_key])."'>".$topic->title."</a>";

            $tags = ["topic" => $url, "topic_name" => $topic->title];

            $sendEmail = Notify::sendEmailByTemplateKey($template->template_key, $usersEmail, $userKey, $tags);
        }
        else{
            Session::flash('message', trans('topic.fail_send_email'));
        }
    }

    /**
     * Retrieve data for que Questionnaires Modal
     * @param $cbKey
     * @param $topicKey
     * @param $code
     * @param null $voteKey
     * @return string
     */
    public function getQuestionnaireModalData($cbKey, $topicKey, $code, $voteKey = null){

        try{
            $cbQuestionnaires = CB::getQuestionnaires($cbKey);
            $questionnaire = null;
            $questionnaireTemplate=null;
            $showQuestionnaire=false;
            $questionnaireModal = [];

            if(!empty($cbQuestionnaires)){
                foreach ($cbQuestionnaires as $key => $cbQuestionnaire){
                    if($key == $code){
                        $questionnaire = $cbQuestionnaire;
                    }
                }

                if(!is_null($voteKey)){
                    foreach ($questionnaire as $key => $value){
                        if($key == $voteKey){
                            $questionnaire = $value;
                        }
                    }
                }

                $topic = CB::getTopic($topicKey);

                if(!is_null($questionnaire)){
                    if(!empty($questionnaire->cb_questionnaire_translation)){
                        $questionnaireTemplate = collect($questionnaire->cb_questionnaire_translation)
                            ->where('language_code','=',Session::get('LANG_CODE'))
                            ->first();
                        if(is_null($questionnaireTemplate)){
                            $questionnaireTemplate = collect($questionnaire->cb_questionnaire_translation)
                                ->where('language_code','=',Session::get('LANG_CODE_DEFAULT'))
                                ->first();
                        }
                    }

                    $formResponse = Questionnaire::verifyReply($questionnaire->questionnarie_key);

                    $user = Session::has('user') ? Session::get('user') : null;

                    if(!is_null($user)){
                        if($formResponse==false){

                            $ignoreQuestionnaire = CB::getCbQuestionnaireUser($questionnaire->cb_questionnarie_key, $user->user_key);

                            if(empty($ignoreQuestionnaire)){
                                $showQuestionnaire = true;
                            }
                            else{
                                $currentDate = Carbon::now();
                                $daysIgnoreUser = Carbon::parse($ignoreQuestionnaire->pivot->date_ignore);

                                $differenceInDays = $currentDate->diffInDays($daysIgnoreUser);

                                if($differenceInDays > $questionnaire->days_to_ignore){
                                    //TODO - detach previous table entry
                                    $showQuestionnaire = true;
                                }
                            }

                            if($showQuestionnaire == true) {
                                $questionnaireModal['cbQuestionnaireKey'] = $questionnaire->cb_questionnarie_key;
                                $questionnaireModal['questionnaireKey'] = $questionnaire->questionnarie_key;
                                $questionnaireModal['content'] = $questionnaireTemplate->content ?? null;
                                $questionnaireModal['accept'] = $questionnaireTemplate->accept ?? null;
                                $questionnaireModal['ignore'] = $questionnaireTemplate->ignore ?? null;
                                $questionnaireModal['questionnaireIgnore'] = $questionnaire->ignore;
                                if(is_null($voteKey)){
                                    Session::put('questionnaireModal', $questionnaireModal);
                                    return "true";
                                }
                                else{
                                    return view('public.' . ONE::getEntityLayout() . '.cbs.questionnaireModal', compact('questionnaireModal'));
                                }
                            }
                        }
                    }
                }
            }

            return "false";
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * Store the specified resource.
     *
     * @param $topicKey
     * @param PostRequest $requestPost
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function store($topicKey, PostRequest $requestPost)
    {
        $cbKey = Session::get('cbId');

        try{
            if (!EMPATIA::verifyCbOperationSchedule($cbKey,'comment', 'create')){
                return redirect()->back()->withErrors(trans('cbs.OutsidePermittedCreationData'));
            }
        } catch (Exception $e){}

        try {
            if (empty($cbKey))
                $cbKey = $requestPost->get('cbKey') ?? null;

            $commentType = null;
            if ($requestPost->commentType){
                $commentType = $this->getCommentType($requestPost->commentType);
            }
            $requestPost->request->add(['link' => action('PublicTopicController@show', [$requestPost->cbKey , $requestPost->topicKey, 'type' => $requestPost->type])]);

            CB::storePost($requestPost, $topicKey, $commentType);

            $topic = CB::getTopic($topicKey);
            $cbConfigs = CB::getCbConfigurations($cbKey);
            foreach ($cbConfigs->configurations as $cbConfig){
                if($cbConfig->code == 'notification_new_comments'){
                    $followers = CB::getFollowersTopic($topic->topic->topic_key);
                    $sendEmail = $this->sendEmailNotification($cbKey, 'notification_new_comments', $requestPost->type, $topic->topic, $followers, null);
                }
                else if($cbConfig->code == 'notification_owner_new_comments'){
                    $owner = $topic->topic->created_by;
                    $cooperators = CB::getCooperators($topic->topic->topic_key);
                    $sendEmail = $this->sendEmailNotification($cbKey, 'notification_owner_new_comments', $requestPost->type, $topic->topic, $cooperators, $owner);
                }
            }

            $topicData = CB::getTopicDataWithChilds($topicKey);
            $configurations = $topicData->configurations;

            $this->getQuestionnaireModalData($requestPost->cbKey, $topicKey, "comment",null);

            if(ONE::checkCBsOption($configurations, 'COMMENT-NEEDS-AUTHORIZATION')) {
                Session::flash('message', trans('topic.commentWaitingForModeration'));
            }

            return redirect()->back();


        } catch (Exception $e) {
            return redirect()->back()->withErrors(["post.store" => $e->getMessage()]);
        }
    }


    /**
     * Update the specified resource.
     *
     * @param PostRequest $requestPost
     * @param $topicKey
     * @return string
     * @internal param TopicRequest $requestForum
     * @internal param int $id
     */
    public function update(PostRequest $requestPost,$topicKey)
    {
        $cbKey = Session::get('cbId');

        try{
            if (!EMPATIA::verifyCbOperationSchedule($cbKey,'comment', 'update')){
                return redirect()->back()->withErrors(trans('cbs.OutsidePermittedUpdateData'));
            }
        } catch (Exception $e){}
        try {
            CB::updatePost($requestPost['contents'], $topicKey, $requestPost['postKey']);

            $topicData = CB::getTopicDataWithChilds($topicKey);
            $configurations = $topicData->configurations;

            if(ONE::checkCBsOption($configurations, 'COMMENT-NEEDS-AUTHORIZATION')) {
                Session::flash('message', trans('topic.commentWaitingForModeration'));
            }

            return action("PublicTopicController@show", ['cbKey' => $requestPost['cbKey'],'topicKey' => $topicKey, 'type' => $requestPost['type']]);

        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * Remove the specified resource.
     *
     * @param $cbKey
     * @param $topicKey
     * @param $postKey
     * @param PostRequest $request
     * @return $this|\Illuminate\Http\RedirectResponse
     * @internal param $id
     */
    public function destroy($cbKey, $topicKey, $postKey, PostRequest $request)
    {
        try {
            CB::deletePost($postKey);

            return action('PublicTopicController@show', ['cbKey' => $cbKey,'topicKey' => $topicKey,'type'=>$request->type]);
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["post.destroy" => $e->getMessage()]);
        }
    }


    /**
     * Show confirm popup to remove the specified resource from storage.
     *
     * @param $cbKey
     * @param $topicKey
     * @param $postKey
     * @param PostRequest|Request $request
     * @return View
     * @internal param $id
     */
    public function delete($cbKey,$topicKey,$postKey, PostRequest $request){

        $data = array();

        $data['action'] = action("PublicPostController@destroy", ['cbKey' => $cbKey,'topicKey' => $topicKey,'postKey' => $postKey, 'type' => $request->type]);
        $data['title'] = "DELETE";
        $data['msg'] = "Are you sure you want to delete this Content?";
        $data['btn_ok'] = "Delete";
        $data['btn_ko'] = "Cancel";


        return view("_layouts.deleteModal", $data);
    }

    /**
     * Like the specified resource.
     *
     * @param PostRequest $request
     * @return Response
     * @internal param $idPost
     * @internal param PostRequest $requestPost
     */
    public function likePost(PostRequest $request)
    {
        try {
            $response = CB::setPostLike($request->idPost);
            return $response->id;

        } catch (Exception $e) {
            return redirect()->back()->withErrors(["post.store" => $e->getMessage()]);
        }
    }


    /**
     * Dislike the specified resource.
     *
     * @param PostRequest $request
     * @return Response
     * @internal param $idPost
     * @internal param PostRequest $requestPost
     */
    public function dislikePost(PostRequest $request)
    {
        try {
            $response = CB::setPostDislike($request->idPost);
            return $response->id;
        } catch (Exception $e) {
            Session::flash('error', $e->getMessage());
        }
    }


    /**
     * Remove the like resource from storage.
     *
     * @param PostRequest $request
     * @return Response
     * @internal param $id
     */
    public function deleteLike(PostRequest $request)
    {

        try {
            $response = CB::deletePostLike($request->idPost);
            return 1;

        } catch (Exception $e) {
            //TODO: save inputs
            Session::flash('error', $e->getMessage());
        }
    }

    /**
     * Report abuse of the specified resource.
     *
     * @param PostRequest|Request $request
     * @return Response
     * @internal param $idPost
     * @internal param PostRequest $requestPost
     */
    public function reportAbuse(PostRequest $request)
    {
        try {
            CB::setReportAbuse($request->post_key, $request->type_id, $request->comment);
        } catch (Exception $e) {
            Session::flash('error', $e->getMessage());
        }
    }


    public function showHistory(PostRequest $request){


        try {
            $posts = CB::getPostHistory($request->postKey);

            return json_encode($posts);

            /*
            $html = '<div class="row" style="max-height: 300px;overflow-y: auto;">';
            $html .= '<div class="col-md-1"></div><div class="col-md-10">';

            foreach($posts as $post){
                $html.= ' <small><b><i class="fa fa-commenting" title="Reply"></i> Created</b> in '.$post->created_at.'</small>';

                $html.= '<div style="border: 1px solid #dedede;margin-bottom: 10px; padding: 10px;min-height: 40px;">';

                $html.= $post->contents;

                $html.= '</div>';
            }

            $html.= '</div>';
            $html.= '</div>';


            return $html;
        */


        } catch (Exception $e) {
            return $e->getMessage();
        }

    }

    /**
     * Add Files to specific content.
     *
     * @param PostRequest $request
     * @return Response
     */
    public function addFile(PostRequest $request)
    {
        try {

            if($request->post_key === "0"){
                $file = ['file_id' => $request->file_id, 'file_code' => $request->file_code, 'name' => $request->name, 'type_id' => $request->type_id, 'description' => 'description'];
                $file = (Object) $file;

                //Session::push('filesToUpload.file', $request->file_id);
                Session::push('filesToUpload', $file);

                return "true";
            }

            CB::setFilesForTopic($request->post_key, $request);

            return "true";
        } catch (Exception $e) {

            return $e->getMessage();
        }
    }

    /**
     * Add Files to specific content.
     *
     * @param PostRequest $request
     * @return Response
     */
    public function editFile(PostRequest $request)
    {
        try {
            if($request->post_key == 0){
                $filesToUpload = [];
                if(Session::has('filesToUpload')) {
                    for($i=0; $i<count(Session::get('filesToUpload')); $i++){
                        if ($request->file_id === Session::get('filesToUpload')[$i]->file_id) {
                            Session::get('filesToUpload')[$i]->name = $request->name;
                            Session::get('filesToUpload')[$i]->description = $request->description;
                            return "true";
                        }
                    }
                }
            }

            CB::updateFilesForTopic($request);
            return "true";

        } catch (Exception $e) {
            return $e->getMessage();
        }
    }


    public function getFileDetails(PostRequest $request)
    {
        try {
            if($request->post_key == 0){
                for($i=0; $i<count(Session::get('filesToUpload')); $i++){
                    if($request->file_id === Session::get('filesToUpload')[$i]->file_id){
                        $file = Session::get('filesToUpload')[$i];

                        return view("_layouts.editFileModal", compact('file'));
                    }



                }

            }
            $file = CB::getFilesForTopic($request);
            $post_key = $request->post_key;
            return view("_layouts.editFileModal", compact('file', 'post_key'));
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * Get Files of specific content.
     *
     * @param $postKey
     * @return Response
     * @internal param int $content_id , int $type_id
     */
    public function getFiles(Request $request,$postKey)
    {
        try {

            if($postKey === "0" and Session::has('filesToUpload')){
                return Session::get('filesToUpload');

            }
            $files = CB::listFilesForTopic($postKey);

            if ($request->has("type") && $request->get("type")!=0) {
                foreach ($files as $key=>$file) {
                    if ($file->type_id!=$request->get("type"))
                        unset($files[$key]);
                }
            }
            return array_values($files);

        } catch (Exception $e) {
            return "exception";
        }
    }

    /**
     * Order Files of specific content.
     *
     * @param Request $request
     * @return Response
     */
    public function orderFile(PostRequest $request)
    {

        try{

            CB::updateOrderFile($request);
            Session::flash('message', trans('cb.updateFileDetails_ok'));
            return redirect()->action('PublicPostController@show', $request->post_key);
        }
        catch(Exception $e) {
            //TODO: save inputs
            return redirect()->back()->withErrors(["content.updateFileDetails" => $e->getMessage()]);
        }
    }


    /**
     * Delete File of specific post from storage.
     *
     * @param  Request $request
     * @param  string $postKey, int $file_id
     * @return Response
     */
    public function deleteFile(PostRequest $request)
    {
        try{

            if($request->post_key == 0 && Session::has('filesToUpload')){


                for($i=0; $i<count(Session::get('filesToUpload')); $i++){

                    if ($request->file_id === Session::get('filesToUpload')[$i]->file_id) {
                        $filesToUpload = Session::get('filesToUpload');
                        unset($filesToUpload[$i]);
                        $filesToUpload = array_values($filesToUpload);
                        Session::set('filesToUpload', $filesToUpload);
                        return "true";
                    }
                }
            }

            CB::deleteFilesForTopic($request);

            Session::flash('message', trans('content.delete_ok'));
            return response()->json(["success"=>"true"],200);
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["cb.deleteFile" => $e->getMessage()]);
        }
    }
}
