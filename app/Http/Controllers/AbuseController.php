<?php

namespace App\Http\Controllers;

use App\ComModules\CB;
use App\Http\Requests\TopicRequest;
use App\Http\Requests\UserRequest;
use Datatables;
use Session;
use View;
use Breadcrumbs;
use ONE;
use HttpClient;
use Illuminate\Support\Collection;


class AbuseController extends Controller
{
    public function __construct()
    {
        View::share('title', trans('abuse.title'));

    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {

        return view('private.abuse.index');
    }

    /**
     * Edit a existent resource.
     *
     * @param $id
     * @return Response
     */
    public function edit($id)
    {
        try {
            $abuse = CB::getPostAbuse($id);

            $post = CB::getPost($abuse->post_id);

            $users = Auth::listUser([$post->created_by, $abuse->created_by]);
            $usersNames = [];

            foreach($users as $user){
                $usersNames[$user->user_key] = $user->name;
            }

            return view('private.abuse.abuse', compact('abuse','post','usersNames'));
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["private.abuse.edit" => $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return Response
     */
    public function show($id)
    {
        try {
            $abuse = CB::getPostAbuse($id);

            $post = CB::getPost($abuse->post_id);

            $topic = CB::getTopic($post->topic_id);

            $users = Auth::listUser([$post->created_by, $abuse->created_by]);
            $usersNames = [];

            foreach($users as $user){
                $usersNames[$user->user_key] = $user->name;
            }

            return view('private.abuse.abuse', compact('abuse','post','usersNames', 'topic'));
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["private.abuse.show" => $e->getMessage()]);
        }
    }

    /**
     * Store the specified resource.
     *
     * @param TopicRequest $requestTopic
     * @return Response
     */
    public function store(TopicRequest $requestTopic)
    {

        try {

            $abuse = CB::storePostAbuse($requestTopic);

            $post = getPost($abuse->post_id);

            $users = Auth::listUser([$post->created_by, $abuse->created_by]);

            $usersNames = [];

            foreach($users as $user){
                $usersNames[$user->user_key] = $user->name;
            }

            Session::flash('message', trans('abuse.store_ok'));

            return view('private.abuse.abuse', compact('abuse','post','usersNames'));
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["private.abuse.store" => $e->getMessage()]);
        }
    }

    /**
     * Update the specified resource.
     *
     * @param TopicRequest $requestForum
     * @param  int $id
     * @return Response
     */
    public function update(TopicRequest $requestForum, $id)
    {
        try {
            $postAbuse = CB::updatePostAbuse($requestForum, $id);

            $abuse = CB::getPostAbuse($id);

            $post = CB::getPost($abuse->post_id);

            $users = Auth::listUser([$post->created_by, $abuse->created_by]);
            $usersNames = [];

            foreach($users as $user){
                $usersNames[$user->user_key] = $user->name;
            }

            Session::flash('message', trans('abuse.update_ok'));

            return view('private.abuse.abuse', compact('abuse','post','usersNames'));
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["private.abuse.update" => $e->getMessage()]);
        }
    }

    /**
     * Destroy the specified resource.
     *
     * @param  int $id
     * @return Response
     */
    public function destroy($id)
    {
        try {
            CB::destroyPostAbuse($id);
            return redirect()->action('AbuseController@index');
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["private.abuse.destroy" => $e->getMessage()]);
        }
    }


    public function getIndexTable($post_id)
    {
        $manage = CB::listPostAbuse($post_id);
        $collection = Collection::make($manage);

        return Datatables::of($collection)
            ->editColumn('id', function ($collection) {
                return "<a href='" . action('AbuseController@show', $collection->id) . "'>" . $collection->id . "</a>";
            })
            ->editColumn('type', function ($collection) {
                return "<a href='" . action('AbuseController@show', $collection->id) . "'>Post Abuse</a>";
            })
            ->editColumn('processed', function ($collection) {
                $processed = 'Yes';
                if($collection->processed == 0){
                    $processed = 'No';
                }
                return "<a href='" . action('AbuseController@show', $collection->id) . "'>" . $processed . "</a>";
            })
            ->addColumn('action', function ($collection) {
                return ONE::actionButtons($collection->id, ['show' => 'AbuseController@edit', 'delete' => 'AbuseController@destroy']);
            })
            ->make(true);
    }



    /**
     * Get the specified resource.
     *
     *
     */
    public function getAbusesByCBTable($cb)
    {
        $manage = CB::getListAbusesByCB($cb);
        $collection = Collection::make($manage);

        return Datatables::of($collection)
            ->editColumn('processed', function ($collection) {

                $processed = '<span class="badge badge-success">Yes</span>';

                if($collection->processed == 0){
                    $processed = '<span class="badge badge-danger">No</span>';
                }

                return "<a href='" . action('AbuseController@show', $collection->id) . "'>" . $processed . "</a>";
            })
            ->make(true);
    }

    /**
     * Get the specified resource.
     *
     *
     */
    public function getAbusesByTopicTable($topicId)
    {

        $manage = CB::getListAbusesByTopic($topicId);
        $collection = Collection::make($manage);

        return Datatables::of($collection)
            ->editColumn('postId', function ($collection) {
                return "<a href='" . action('AbuseController@show', $collection->id) . "'>" . $collection->post_id . "</a>";
            })
            ->editColumn('comment', function ($collection) {
                return "<a href='" . action('AbuseController@show', $collection->id) . "'>" . $collection->comment . "</a>";
            })
            ->addColumn('abuses', function ($collection) {
                return ONE::actionButtons($collection->post_id, ['accept' => 'AbuseController@acceptPostAbuses', 'decline' => 'AbuseController@declinePostAbuses']);
            })
            ->editColumn('processed', function ($collection) {

                $processed = '<span class="badge badge-success">Yes</span>';

                if($collection->processed == 0){
                    $processed = '<span class="badge badge-danger">No</span>';
                }

                return "<a href='" . action('AbuseController@show', $collection->id) . "'>" . $processed . "</a>";
            })
            ->make(true);
    }

    public function acceptAllForumAbuses($cbId)
    {
        try{
            CB::acceptAllForumAbuses($cbId);
            Session::flash('message', trans('abuse.allaccepted_ok'));
            return redirect()->action('ForumController@index');
        }catch (Exception $e) {
            return redirect()->back()->withErrors(["private.abuse.acceptAllForum" => $e->getMessage()]);
        }
    }

    public function declineAllForumAbuses($cbId)
    {
        try{

            CB::declineAllForumAbuses($cbId);
            Session::flash('message', trans('abuse.alldeclined_ok'));
            return redirect()->action('ForumController@index');

        }catch (Exception $e) {
            return redirect()->back()->withErrors(["private.abuse.declineAllForum" => $e->getMessage()]);
        }
    }

    public function acceptAllTopicAbuses($topicId)
    {
        try{

            CB::acceptAllTopicAbuses($topicId);
            Session::flash('message', trans('abuse.allaccepted_ok'));
            return redirect()->action('ForumController@index');

        }catch (Exception $e) {
            return redirect()->back()->withErrors(["private.abuse.acceptAllTopic" => $e->getMessage()]);
        }
    }

    public function declineAllTopicAbuses($topicId)
    {
        try{
            CB::declineAllTopicAbuses($topicId);

            Session::flash('message', trans('abuse.alldeclined_ok'));
            return redirect()->action('ForumController@index');

        }catch (Exception $e) {
            return redirect()->back()->withErrors(["private.abuse.declineAllTopic" => $e->getMessage()]);
        }
    }

    public function acceptPostAbuses($postId)
    {
        try{
            CB::acceptPostAbuses($postId);
            Session::flash('message', trans('abuse.accepted_ok'));
            return redirect()->action('ForumController@index');

        }catch (Exception $e) {
            return redirect()->back()->withErrors(["private.abuse.acceptAllTopic" => $e->getMessage()]);
        }
    }

    public function declinePostAbuses($postId)
    {
        try{

            CB::declinePostAbuses($postId);
            Session::flash('message', trans('abuse.declined_ok'));
            return redirect()->action('ForumController@index');

        }catch (Exception $e) {
            return redirect()->back()->withErrors(["private.abuse.acceptAllTopic" => $e->getMessage()]);
        }
    }

}
