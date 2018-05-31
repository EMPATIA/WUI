<?php

namespace App\Http\Controllers;

use App\ComModules\Auth;
use App\ComModules\CB;
use App\ComModules\Notify;
use App\ComModules\Orchestrator;
use App\One\One;
use Datatables;
use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Collection;
use Session;
use URL;

class TopicReviewsController extends Controller
{
    /**
     * TopicReviewsController constructor.
     */
    public function __construct()
    {

    }

    /**
     * @param $type
     * @param $cbKey
     * @param $topicKey
     * @return $this|View
     */
    public function index(Request $request, $type, $cbKey, $topicKey){

        try {
            $topic = CB::getTopicParameters($topicKey);

            $topicReview = CB::getTopicReviews($topicKey);

            $entityGroups = Orchestrator::getEntityGroups();

            $entityGroups = collect($entityGroups)->pluck('entity_group_key');

            foreach($topicReview as $review){

                $collectionReviewers = collect($review->topic_review_reviewers);
                $reviewers = $collectionReviewers->pluck('reviewer_key');

                if(isset($review->topic_review_replies) && count($review->topic_review_replies) > 0){

                    $collection = collect($review->topic_review_replies);
                    $repliers = $collection->pluck('created_by');

                }
            }

            if(isset($repliers))
                $repliers = collect(Auth::listUser($repliers))->keyBy('user_key');

            foreach($topicReview as $review) {
                $user = Auth::getUserByKey($review->created_by);
                $review->creator_name = $user->name;

                $collection = collect($review->topic_review_reviewers);
                $reviewers = $collection->pluck('reviewer_key');

                //Get Reviewers details
                $reviewers = collect(Auth::listUser($reviewers))->keyBy('user_key');
                //add new field - reviewer_name - to each reviewer
                $collection->map(function ($collection) use($reviewers){
                    if ($reviewers->has($collection->reviewer_key)) {
                        $collection->reviewer_name = $reviewers->get($collection->reviewer_key)->name;
                    }
                    return $reviewers;
                });

                $review->topic_review_reviewers = $collection;
            }

            $title = trans('privateTopicReviews.topic_reviews_list');

            Session::put('sidebarArguments.activeSecondMenu', 'topicReviews');

            $data = [];
            $data['title']              = $title;
            $data['topic']              = $topic;
            $data['type']               = $type;
            $data['topicKey']           = $topicKey;
            $data['cbKey']              = $cbKey;
            $data['topicReviews']       = $topicReview;
            $data['sidebar']            = 'topics';
            $data['active']             = 'topicReviews';
            if(isset($repliers))
                $data['repliers'] = $repliers;

            return view('private.topics.topicReviews.index', $data);

        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["topic.edit" => $e->getMessage()]);
        }

    }


    /**
     * Returns the view with details for given Topic Review Key
     *
     * @param $type
     * @param $cbKey
     * @param $topicKey
     * @param $topicReviewKey
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|string
     */
    public function show($type, $cbKey, $topicKey, $topicReviewKey){
        try{
            $topicReview = CB::getTopicReview($topicReviewKey);

            //get user details
            $user = Auth::getUserByKey($topicReview->created_by);
            //add new field creator_name
            $topicReview->creator_name = $user->name;

            //Reviewers
            $collection = collect($topicReview->topic_review_reviewers);
            $reviewers = $collection->pluck('reviewer_key');

            //Get Reviewers details
            $reviewers = collect(Auth::listUser($reviewers))->keyBy('user_key');
            //add new field - reviewer_name - to each reviewer
            $collection->map(function ($collection) use($reviewers){
                if ($reviewers->has($collection->reviewer_key)) {
                    $collection->reviewer_name = $reviewers->get($collection->reviewer_key)->name;
                }
                return $reviewers;
            });

            //Verify if logged user is the Topic Review Author or a Reviewer
            $loggedUser = Session::get('user');
            if(($user->user_key == $loggedUser->user_key) || ($reviewers->has($loggedUser->user_key)))
                $hasPermission = true;

            //$statusCollection = collect($topicReview->topic_review_status);
            //include edited reviewers in topic review
            $topicReview->topic_review_reviewers = $collection;

            //set page title
            $title = trans('privateTopicReviews.topic_review_details').' '.(isset($topicReview->subject) ? $topicReview->subject : null);

            $data = [];
            $data['title']              = $title;
            $data['type']               = $type;
            $data['topicKey']           = $topicKey;
            $data['cbKey']              = $cbKey;
            $data['topicReview']        = $topicReview;
            $data['hasPermission']      = isset($hasPermission) ?? $hasPermission;
            $data['sidebar']            = 'topicReviews';
            $data['active']             = 'details';

            Session::put('sidebarArguments.topicReviewKey', $topicReviewKey);
            Session::put('sidebarArguments.activeThirdMenu', 'details');

            Session::put('sidebars', [0 => 'private', 1=> 'padsType', 2 => 'topics', 3 => 'topicReviews']);

            return view('private.topics.topicReviews.topicReview', $data);


        }catch(Exception $e) {
            return redirect()->back()->withErrors([trans("privateTopicReview.error_on_show") => $e->getMessage()])->getTargetUrl();
        }

    }

    /**
     *
     * Returns the view for Topic Review Create
     *
     * @param Request $request
     * @param $type
     * @param $cbKey
     * @param $topicKey
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|string
     */
    public function create(Request $request, $type, $cbKey, $topicKey)
    {
        try {
            // Form title (layout)
            $title = trans('privateTopicReview.create_topic_review');

            //Reviewers

            //Get Entity Users

            $usersKeys = collect(Orchestrator::getAllManagers())->pluck('user_key');
            $entityGroups = collect(Orchestrator::getEntityGroups());
            
            $users = Auth::listUser($usersKeys);

            $users = collect($users)->pluck('name','user_key');
            $entityGroups = collect($entityGroups)->pluck('name', 'entity_group_key');

            $select2data = array();
            foreach ($users as $i => $user){
                $select2data[] = array('id'=> 'user_'.$i,'text'=>$user);
            }
            foreach($entityGroups as $i => $groups){
                $select2data[] = array('id'=> 'group_'.$i,'text'=>$groups);
            }

            sort($select2data);


            // Return the view with data
            $data = [];
            $data['title'] = $title;
            $data['type']               = $type;
            $data['topicKey']           = $topicKey;
            $data['cbKey']              = $cbKey;
            $data['reviewers']          = $select2data;
            $data['sidebar']       = 'topics';
            $data['active']       = 'topicReviews';


            //return view('private.topics.topicReviews.topicReviewEmail', $data);
            return view('private.topics.topicReviews.topicReview', $data);
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors([trans("privateTopicReview.error_on_create") => $e->getMessage()])->getTargetUrl();
        }
    }

    /**
     *
     * Stores a new Topic Review
     *
     * @param Request $request
     * @param $type
     * @param $cbKey
     * @param $topicKey
     * @return \Illuminate\Http\RedirectResponse|string
     */
    public function store(Request $request, $type, $cbKey, $topicKey)
    {
        try {
            //prepare reviewers array for save
            $users = $request->get('users');

            if(!empty($users)){

                $reviewers = [];
                foreach ($users as $i=>$user){
                    $user = explode('_', $user);

                    $reviewers[$i]['key'] = $user[1];
                    if($user[0] == "user"){
                        $reviewers[$i]['key'] = $user[1];
                        $reviewers[$i]['is_group'] = 0;
                    }else{
                        $entityGroupUsers = Orchestrator::getUsersByEntityGroupKey($user[1]);
                        foreach($entityGroupUsers as $j => $entityGroup){
                            $group[$j]['key'] = $entityGroup->user_key;
                        }

                        $reviewers[$i]['is_group'] = 1;
                    }
                }
                
                $users = collect($reviewers)->pluck('key');
                $users = Auth::listUser($users);

                if(!empty($group)) {
                    $group = collect($group)->pluck('key');
                    $group = Auth::listUser($group);
                }

                if(!empty($users)) {
                    foreach ($users as $user) {
                        $emailType = 'topic_review';
                        $tags = [
                            "name"        => $user->name,
                            "link"        => URL::action("TopicController@show", ["type" => $type, "cbKey" => $cbKey, "topicKey" => $topicKey]),
                            "button_text" => trans("privateTopicReviews.click_button")
                        ];
                        $response = Notify::sendEmail($emailType, $tags, (array)$user);
                    }
                }

                if(!empty($group)){
                    foreach($group as $userGroup){
                        $emailType = 'topic_review';
                        $tags = [
                            "name" => $userGroup->name,
                            "link" => URL::action("TopicController@show",["type" => $type, "cbKey" => $cbKey, "topicKey" => $topicKey]),
                            "button_text" => trans("privateTopicReviews.click_button")
                        ];
                        $response = Notify::sendEmail($emailType, $tags, (array) $userGroup);
                    }
                }

                //Call to Com Module set method - Store Topic Review
                CB::setTopicReview($request, $topicKey, $reviewers);

                // Message to show + redirect To
                Session::flash('message', trans('privateTopicReview.store_ok'));
                return redirect()->action('TopicReviewsController@index', ['type'=>$type,'cbKey'=>$cbKey, 'topicKey' => $topicKey]);
            }else{
                return redirect()->back()->withErrors(["privateTopicReview.error_on_store"]);
            }

        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["privateTopicReview.error_on_store" => $e->getMessage()]);
        }
    }

    /**
     * Returns the view for Topic Review Edit
     *
     * @param $type
     * @param $cbKey
     * @param $topicKey
     * @param $topicReviewKey
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|string
     */
    public function edit($type, $cbKey, $topicKey, $topicReviewKey){
        try {
            // Form title (layout)
            $title = trans('privateTopicReview.edit_topic_review');

            //Load Topic Review
            $topicReview = CB::getTopicReview($topicReviewKey);

            /*----------- Author and Reviewers Details ----------- */
            //get user details
            $user = Auth::getUserByKey($topicReview->created_by);

            //add new field creator_name
            $topicReview->creator_name = $user->name;

            //Reviewers
            $collection = collect($topicReview->topic_review_reviewers);
            $reviewers = $collection->pluck('reviewer_key');
            //Get Reviewers details
            $reviewers = collect(Auth::listUser($reviewers))->keyBy('user_key');

            //add new field - reviewer_name - to each reviewer
            $collection->map(function ($collection) use($reviewers){
                if ($reviewers->has($collection->reviewer_key)) {
                    $collection->reviewer_name = $reviewers->get($collection->reviewer_key)->name;
                }
                return $reviewers;
            });

            //include edited reviewers in topic review
            $topicReview->topic_review_reviewers = $collection;

            /*----------- Reviewers ----------- */

            //Topic Reviewers
            $topicReviewers = collect($topicReview->topic_review_reviewers)->pluck('reviewer_key');
            //Entity Users
            $usersKeys = collect(Orchestrator::getAllUsers())->pluck('user_key');

            //entity users that are not reviewers
            $result = $usersKeys->diff($topicReviewers);

            //TODO: include groups

            // Email / Notify //TODO Send email to selected reviewers (if new added)

            //prepare values for select2
            $users = Auth::listUser($result);

            $users = collect($users)->pluck('email', 'user_key');
            //construct array with select2 structure
            $select2data = array();
            foreach ($users as $i =>$user){
                $select2data[] = array('id'=> $i,'text'=>$user);
            }

            //Status
            //TODO get available status for selection

            //Get Entity Groups


            // Return the view with data
            $data = [];
            $data['title'] = $title;
            $data['type']               = $type;
            $data['topicKey']           = $topicKey;
            $data['cbKey']              = $cbKey;
            $data['topicReview']        = $topicReview;
            $data['status']             = isset($status) ? $status: 'open'; //TODO Correct after get status complete
            $data['reviewers']          = $select2data;
            $data['sidebar']       = 'topics';
            $data['active']       = 'topicReviews';


            return view('private.topics.topicReviews.topicReview', $data);
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors([trans("privateTopicReview.error_on_create") => $e->getMessage()])->getTargetUrl();
        }
    }

    /**
     * Updates a given (key) Topic Review
     *
     * @param Request $request
     * @param $type
     * @param $cbKey
     * @param $topicKey
     * @param $topicReviewKey
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $type, $cbKey, $topicKey, $topicReviewKey){

        try {

            $users = $request->get('users');
            //TODO Add groups to reviewers
            $reviewers = [];
            if (!is_null($users)) {

                foreach ($users as $i=>$user){
                    $reviewers[$i]['key'] = $user;
                    $reviewers[$i]['is_group'] = 0;
                }
            }

            //Call to Com Module update method
            CB::updateTopicReview($request, $topicKey, $topicReviewKey, $reviewers);

            // Message to show + redirect To
            return redirect()->action('TopicReviewsController@index', ['type'=>$type,'cbKey'=>$cbKey, 'topicKey' => $topicKey])->with('message', trans('privateTopicReview.update_ok'));
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["entityGroup.edit" => $e->getMessage()]);
        }

    }

    /**
     * Returns delete modal/dialog
     *
     * @param $type
     * @param $cbKey
     * @param $topicKey
     * @param $topicReviewKey
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function delete($type, $cbKey, $topicKey, $topicReviewKey){

        $data = array();

        $data['action'] = action("TopicReviewsController@destroy", ['type'=>$type,'cbKey'=>$cbKey, 'topicKey' => $topicKey, 'topicReviewKey' => $topicReviewKey]);
        $data['title'] =  trans('privateTopicReview.delete');
        $data['msg'] = trans('privateTopicReview.are_you_sure you_want_to_delete_this_topic_review') . "?";
        $data['btn_ok'] = trans('privateTopicReview.delete');
        $data['btn_ko'] = trans('privateTopicReview.cancel');

        return view("_layouts.deleteModal", $data);
    }

    /**
     * Deletes a Topic Review
     *
     * @param $type
     * @param $cbKey
     * @param $topicKey
     * @param $topicReviewKey
     * @return string
     */
    public function destroy($type, $cbKey, $topicKey, $topicReviewKey)
    {
        try {
            //delete
            CB::deleteTopicReview($topicReviewKey);

            // Message to show + redirect To
            Session::flash('message', trans('privateTopicReview.delete_ok'));
            return action('TopicReviewsController@index', ['type'=>$type,'cbKey'=>$cbKey, 'topicKey' => $topicKey]);
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors([ trans('privateTopicReview.error_on_delete') => $e->getMessage()])->getTargetUrl();
        }
    }

    public function getReviewsFromUser(Request $request, $type, $cbKey, $topicKey){
        $userKeys = $request->keys;

        return action('TopicReviewsController@index', ['type' => $type, 'cbKey' => $cbKey, 'topicKey' => $topicKey, 'keys' => $userKeys]);
    }
}
