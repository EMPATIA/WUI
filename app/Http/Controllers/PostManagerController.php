<?php

namespace App\Http\Controllers;

use App\ComModules\Orchestrator;
use App\ComModules\Auth;
use App\ComModules\CB;
use Datatables;
use Session;
use View;
use Breadcrumbs;
use HttpClient;
use App\One\One;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;



class PostManagerController extends Controller
{
    public function __construct()
    {
        View::share('private.posts', trans('posts.posts'));


    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Advanced Search var's
        $showWithAbuses = !empty($request->input("showWithAbuses"))?:0;
        $showCommentsNeedsAuth = !empty($request->input("showCommentsNeedsAuth"))?:0;

        return view('private.postManager.index',compact("showWithAbuses","showCommentsNeedsAuth"));
    }

    /**
     * @return mixed
     */
    public function getIndexTable(Request $request)
    {
        // Getting CBs from an Entity
        $cbs = Orchestrator::getAllCbs();

        // CB Keys
        $cbKeys = [];
        foreach($cbs as $cb){
            $cbKeys[] = $cb->cb_key;
        }

        // Advanced Search var's
        $showWithAbuses = $request->input("showWithAbuses");
        $showCommentsNeedsAuth = $request->input("showCommentsNeedsAuth");

        // Getting posts
        $postsData = CB::getPostManagerList($cbKeys,$showWithAbuses,$showCommentsNeedsAuth,false, $request);
        $posts = $postsData->data;

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

        $collection = Collection::make($posts);
        return Datatables::of($collection)
            ->editColumn('topic', function ($collection) {
                return  !empty($collection->topic->title) ? $collection->topic->title : "";
            })
            ->addColumn('approve', function ($collection)  use ($showWithAbuses, $showCommentsNeedsAuth) {
                $html = "";
                if($collection->commentNeedsAuth && $collection->active == 0){
                    $html = '<a href="'. action('PostManagerController@active', [$collection->post_key, 1, "showWithAbuses" => $showWithAbuses, "showCommentsNeedsAuth" => $showCommentsNeedsAuth]) .'" class="btn btn-flat btn-success btn-xs" data-toggle="tooltip" data-original-title="approve"><i class="glyphicon glyphicon-thumbs-up"></i> </a> ';
                    $html .= "<span class='badge badge-warning'>".trans("privateTopics.needsApproval")."</span>";
                } else if($collection->commentNeedsAuth && $collection->active == 1) {
                    $html = '<a href="'. action('PostManagerController@active', [$collection->post_key, 0, "showWithAbuses" => $showWithAbuses, "showCommentsNeedsAuth" => $showCommentsNeedsAuth]) .'" class="btn btn-flat btn-danger btn-xs" data-toggle="tooltip" data-original-title="disapprove"><i class="glyphicon glyphicon-thumbs-down"></i> </a> ';
                    $html .= "<span class='badge badge-success'>".trans("privateTopics.approved")."</span>";
                }

                return $html;
            })
            ->editColumn('message', function ($collection) {
                return  $collection->contents;
            })
            ->editColumn('created_by', function ($collection) use ($userNames) {
                return (!empty($userNames[$collection->created_by])) ? $userNames[$collection->created_by] : "";
            })
            ->editColumn('abuses', function ($collection) use ($showWithAbuses, $showCommentsNeedsAuth) {
                if($collection->blocked == 0){
                    $buttons = "";
                    if( count($collection->abuses) > 0 ){
                        $buttons .= '<a href="'. action('PostManagerController@blocked', [$collection->post_key, 1, "showWithAbuses" => $showWithAbuses, "showCommentsNeedsAuth" => $showCommentsNeedsAuth]) .'" class="btn btn-flat btn-danger btn-xs" data-toggle="tooltip" data-original-title="Block"><i class="glyphicon glyphicon-thumbs-up"></i> '.trans("privatePosts.block").'</a>';
                    }

                    if(count($collection->abuses) == 0){
                        $labelType = "label-default";
                    } else if(count($collection->abuses) == 1){
                        $labelType = "label-warning";
                    } else {
                        $labelType = "label-danger";
                    }

                    $content = "<span class='label ".$labelType."'>".count($collection->abuses)."</span> ".$buttons;
                } else {
                    $buttons = '<a href="'. action('PostManagerController@blocked', [$collection->post_key, 0, "showWithAbuses" => $showWithAbuses, "showCommentsNeedsAuth" => $showCommentsNeedsAuth]) .'" class="btn btn-flat btn-success btn-xs" data-toggle="tooltip" data-original-title="unblock"><i class="glyphicon glyphicon-thumbs-up"></i> '.trans("privatePosts.unblock").'</a>';                    
                    $content = "<span class='badge badge-danger'>".count($collection->abuses)." / ".trans("privatePosts.blocked")."</span> ".$buttons;
                }

                return $content;
            })
            ->addColumn('action', function ($collection) use ($showWithAbuses,$showCommentsNeedsAuth)  {
                $html = "";
                if ($collection->active)
                    $html .= '<a href="'. action('PostManagerController@active', [$collection->post_key, 1, "showWithAbuses" => $showWithAbuses, "showCommentsNeedsAuth" => $showCommentsNeedsAuth]) .'" class="btn btn-flat btn-success btn-xs disabled" data-toggle="tooltip" data-original-title="approve"><i class="glyphicon glyphicon-thumbs-up"></i></a>';
                else
                    $html .= '<a href="'. action('PostManagerController@active', [$collection->post_key, 1, "showWithAbuses" => $showWithAbuses, "showCommentsNeedsAuth" => $showCommentsNeedsAuth]) .'" class="btn btn-flat btn-success btn-xs" data-toggle="tooltip" data-original-title="approve"><i class="glyphicon glyphicon-thumbs-up"></i></a>';

                if ($collection->blocked)
                    $html .= '<a href="'. action('PostManagerController@active', [$collection->post_key, 0, "showWithAbuses" => $showWithAbuses, "showCommentsNeedsAuth" => $showCommentsNeedsAuth]) .'" class="btn btn-flat btn-danger btn-xs disabled" data-toggle="tooltip" data-original-title="disapprove"><i class="glyphicon glyphicon-thumbs-down"></i> </a>';
                else
                    $html .= '<a href="'. action('PostManagerController@active', [$collection->post_key, 0, "showWithAbuses" => $showWithAbuses, "showCommentsNeedsAuth" => $showCommentsNeedsAuth]) .'" class="btn btn-flat btn-danger btn-xs" data-toggle="tooltip" data-original-title="disapprove"><i class="glyphicon glyphicon-thumbs-down"></i> </a>';

                return $html . ONE::actionButtons([$collection->post_key, "showWithAbuses" => $showWithAbuses, "showCommentsNeedsAuth" => $showCommentsNeedsAuth], ['delete' => 'PostManagerController@delete']);
            })
            /* Makes DataTable not reordering the data again - was messing up with dates */
            ->order(function(){})
            ->skipPaging()
            ->setTotalRecords($postsData->recordsTotal)
            ->make(true);
    }

    /**
     *
     *
     * @param $request
     * @param $postKey
     * @param $value
     * @return View
     */
    public function blocked(Request $request,$postKey, $value)
    {
        try {
            // Advanced Search var's
            $showWithAbuses = $request->input("showWithAbuses");
            $showCommentsNeedsAuth = $request->input("showCommentsNeedsAuth");

            CB::updatePostBlock($postKey, $value);

            return  redirect()->action('PostManagerController@index',["showWithAbuses" => $showWithAbuses, "showCommentsNeedsAuth" => $showCommentsNeedsAuth]);

        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["postManager.index" => trans("PrivatePostManager.errorWhileRemovingPost") ]);
        }
    }


    /**
     *
     *
     * @param $request
     * @param $postKey
     * @param $value
     * @return View
     */
    public function active(Request $request, $postKey, $value)
    {
        try {

            // Advanced Search var's
            $showWithAbuses = $request->input("showWithAbuses");
            $showCommentsNeedsAuth = $request->input("showCommentsNeedsAuth");

            CB::activePost($postKey, $value);

            return redirect()->action('PostManagerController@index', ["showWithAbuses" => $showWithAbuses, "showCommentsNeedsAuth" => $showCommentsNeedsAuth]);
        } catch(\Exception $e) {
            return redirect()->back()->withErrors(["postManager.active" => $e->getMessage()]);
        }
    }


    /**
     * Returns remove confirm view for modal display.
     *
     * @param $postKey
     * @return View
     */
    public function delete(Request $request,$postKey)
    {
        $data = array();

        // Advanced Search var's
        $showWithAbuses = $request->input("showWithAbuses");
        $showCommentsNeedsAuth = $request->input("showCommentsNeedsAuth");

        $data['action'] = action("PostManagerController@destroy", ['postKey' => $postKey,"showWithAbuses" => $showWithAbuses, "showCommentsNeedsAuth" => $showCommentsNeedsAuth]);
        $data['title'] = "DELETE";
        $data['msg'] = "Are you sure you want to delete this Post?";
        $data['btn_ok'] = "Delete";
        $data['btn_ko'] = "Cancel";

        return view("_layouts.deleteModal", $data);
    }

    /**
     * Remove the specified post from storage.
     *
     * @param $postKey
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request, $postKey){

        try {

            // Advanced Search var's
            $showWithAbuses = $request->input("showWithAbuses");
            $showCommentsNeedsAuth = $request->input("showCommentsNeedsAuth");

            $response = CB::deletePost($postKey);

            return action('PostManagerController@index',["showWithAbuses" => $showWithAbuses, "showCommentsNeedsAuth" => $showCommentsNeedsAuth]);

        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["postManager.index" => trans("PrivatePost.errorWhileRemovingPost") ]);
        }
    }

}
