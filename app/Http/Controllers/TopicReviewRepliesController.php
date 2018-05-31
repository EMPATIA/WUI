<?php

namespace App\Http\Controllers;

use App\ComModules\Auth;
use App\ComModules\CB;
use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Session;

class TopicReviewRepliesController extends Controller
{
    /**
     * TopicReviewRepliesController constructor.
     */
    public function __construct()
    {

    }

    /**
     * Lists Replies for a given Topic Review
     *
     * @param $type
     * @param $cbKey
     * @param $topicKey
     * @param $topicReviewKey
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|string
     */
    public function index($type, $cbKey, $topicKey, $topicReviewKey){

        try {

            //Permissions verification to show/hide edit elements on View
            $topicReview = CB::getTopicReview($topicReviewKey);
            $collection = collect($topicReview->topic_review_reviewers);
            $reviewers = $collection->pluck('reviewer_key');

            //Get Reviewers details
            $reviewers = collect(Auth::listUser($reviewers))->keyBy('user_key');

            //Verify if logged user is the Topic Review Author or a Reviewer
            $loggedUser = Session::get('user');

            //flag indicating if logged user has permission to access edition fields
            if(($topicReview->created_by == $loggedUser->user_key) || ($reviewers->has($loggedUser->user_key)))
                $hasPermission = true;

            //topicReviewKey
            $topicReviewReplies = CB::getTopicReviewReplies($topicReviewKey);
            $repliersKeys = collect($topicReviewReplies)->pluck('created_by');
            $repliers = collect(Auth::listUser($repliersKeys))->keyBy('user_key');

            //Insert reviewers name in each topic Review Reply
            foreach ($topicReviewReplies as $item){

               //$this = $repliers->get($item->created_by);
               $item->creator_details = $repliers->get($item->created_by);

            }
            $title = trans('privateTopicReviewReplies.topic_review_replies_list');
            $data = [];
            $data['title']                              = $title;
            $data['topicReviewReplies']                 = $topicReviewReplies;
            $data['type']                               = $type;
            $data['topicKey']                           = $topicKey;
            $data['cbKey']                              = $cbKey;
            $data['topicReviewKey']                     = $topicReviewKey;
            $data['hasPermission']                      = isset($hasPermission) ?? $hasPermission;


            return view('private.topics.topicReviewReplies.index', $data);
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors([trans("privateTopicReviewReplies.error_on_index") => $e->getMessage()])->getTargetUrl();
        }

    }

    /**
     * Shows a reply
     *
     * @param $type
     * @param $cbKey
     * @param $topicKey
     * @param $topicReviewKey
     * @param $topicReviewReplyKey
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|string
     */
    public function show($type, $cbKey, $topicKey, $topicReviewKey, $topicReviewReplyKey){
        try {


            //topicReviewKey
            $topicReviewReply = CB::getTopicReviewReply($topicReviewReplyKey);

            //get user details
            $user = Auth::getUserByKey($topicReviewReply->created_by);
            //add new field creator_name
            $topicReviewReply->creator_name = $user->name;

            // Request configurations
            $title = trans('privateTopicReviewReplies.show_topic_review_reply');

            $data = [];
            $data['title']                              = $title;
            $data['topicReviewReply']                   = $topicReviewReply;
            $data['type']                               = $type;
            $data['topicKey']                           = $topicKey;
            $data['cbKey']                              = $cbKey;
            $data['topicReviewKey']                     = $topicReviewKey;


            return view('private.topics.topicReviewReplies.topicReviewReply', $data);
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors([trans("privateTopicReviewReplies.error_on_show") => $e->getMessage()])->getTargetUrl();
        }
    }

    /**
     *
     * Returns a view for Topic Review Reply Creation
     * @param $type
     * @param $cbKey
     * @param $topicKey
     * @param $topicReviewKey
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|string
     */
    public function create($type, $cbKey, $topicKey, $topicReviewKey){
        try {
            // Form title (layout)
            $title = trans('privateTopicReview.create_topic_review_reply');

            $status = CB::getTopicReviewStatusTypes('create');

            // Return the view with data
            $data = [];
            $data['title'] = $title;
            $data['type']               = $type;
            $data['topicKey']           = $topicKey;
            $data['cbKey']              = $cbKey;
            $data['topicReviewKey']     = $topicReviewKey;
            $data['status']             = $status;

            return view('private.topics.topicReviewReplies.topicReviewReply', $data);
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors([trans("privateTopicReviewReplies.error_on_create") => $e->getMessage()])->getTargetUrl();
        }
    }

    /**
     *
     * Stores a new reply
     *
     * @param Request $request
     * @param $type
     * @param $cbKey
     * @param $topicKey
     * @param $topicReviewKey
     * @return \Illuminate\Http\RedirectResponse|string
     */
    public function store(Request $request, $type, $cbKey, $topicKey, $topicReviewKey){

        try {
            //Call to Com Module set method - Store Topic Review
            CB::setTopicReviewReply ($request, $topicReviewKey);

            // Message to show + redirect To
            Session::flash('message', trans('privateTopicReviewReplies.store_ok'));
            return redirect()->action('TopicReviewRepliesController@index', ['type'=>$type,'cbKey'=>$cbKey, 'topicKey' => $topicKey, 'topicReviewKey' => $topicReviewKey]);
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["privateTopicReviewReplies.error_on_store" => $e->getMessage()])->getTargetUrl();
        }
    }

    /**
     * Returns a view for Topic Review Reply Edition
     *
     * @param $type
     * @param $cbKey
     * @param $topicKey
     * @param $topicReviewKey
     * @param $topicReviewReplyKey
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|string
     */
    public function edit($type, $cbKey, $topicKey, $topicReviewKey, $topicReviewReplyKey){
        try {
            // Form title (layout)
            $title = trans('privateTopicReview.create_topic_review_reply');

            $topicReviewReply = CB::getTopicReviewReply($topicReviewReplyKey);

            $status = CB::getTopicReviewStatusTypes('edit');

            // Return the view with data
            $data = [];
            $data['title'] = $title;
            $data['type']               = $type;
            $data['topicKey']           = $topicKey;
            $data['cbKey']              = $cbKey;
            $data['topicReviewKey']     = $topicReviewKey;
            $data['topicReviewReply']   = $topicReviewReply;
            $data['status']             = $status;

            return view('private.topics.topicReviewReplies.topicReviewReply', $data);
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors([trans("privateTopicReviewReplies.error_on_edit") => $e->getMessage()])->getTargetUrl();
        }
    }

    /**
     *
     * Updates reply from given Key
     *
     * @param Request $request
     * @param $type
     * @param $cbKey
     * @param $topicKey
     * @param $topicReviewKey
     * @param $topicReviewReplyKey
     * @return \Illuminate\Http\RedirectResponse|string
     */
    public function update(Request $request, $type, $cbKey, $topicKey, $topicReviewKey, $topicReviewReplyKey){
        try {

            //Call to Com Module update method
            CB::updateTopicReviewReply($request, $topicReviewReplyKey);

            // Message to show + redirect To
            return redirect()->action('TopicReviewsController@index', ['type'=>$type,'cbKey'=>$cbKey, 'topicKey' => $topicKey, 'topicReviewKey' => $topicReviewKey])->with('message', trans('privateTopicReviewReplies.update_ok'));
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors([trans("privateTopicReviewReplies.error_on_update") => $e->getMessage()])->getTargetUrl();
        }
    }

    /**
     * Returns delete modal/dialog
     *
     * @param $type
     * @param $cbKey
     * @param $topicKey
     * @param $topicReviewKey
     * @param $topicReviewReplyKey
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function delete($type, $cbKey, $topicKey, $topicReviewKey, $topicReviewReplyKey){

        $data = array();

        $data['action'] = action("TopicReviewRepliesController@destroy", ['type'=>$type,'cbKey'=>$cbKey, 'topicKey' => $topicKey, 'topicReviewKey' => $topicReviewKey, 'topicReviewReplyKey' => $topicReviewReplyKey]);
        $data['title'] =  trans('privateTopicReviewReplies.delete');
        $data['msg'] = trans('privateTopicReviewReplies.are_you_sure you_want_to_delete_this_topic_review_reply') . "?";
        $data['btn_ok'] = trans('privateTopicReviewReplies.delete');
        $data['btn_ko'] = trans('privateTopicReviewReplies.cancel');

        return view("_layouts.deleteModal", $data);

    }

    /**
     * Deletes a Topic Review Reply
     *
     * @param $type
     * @param $cbKey
     * @param $topicKey
     * @param $topicReviewKey
     * @param $topicReviewReplyKey
     * @return string
     */
    public function destroy($type, $cbKey, $topicKey, $topicReviewKey, $topicReviewReplyKey){
        try {
            //delete
            CB::deleteTopicReviewReply($topicReviewReplyKey);

            // Message to show + redirect To
            Session::flash('message', trans('privateTopicReviewReplies.delete_ok'));
            return action('TopicReviewRepliesController@index', ['type'=>$type,'cbKey'=>$cbKey, 'topicKey' => $topicKey, 'topicReviewKey' => $topicReviewKey]);
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors([ trans('privateTopicReviewReplies.error_on_delete') => $e->getMessage()])->getTargetUrl();
        }
    }
}
