<?php

namespace App\Http\Controllers;

use App\ComModules\Auth;
use App\ComModules\CB;
use App\ComModules\Orchestrator;
use App\One\One;
use Carbon\Carbon;
use Exception;
use Html;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Collection;
use Session;
use Yajra\Datatables\Facades\Datatables;

class ModerationController extends Controller
{
    /**
     * ModerationController constructor.
     */
    public function __construct()
    {
        //
    }


    /**
     * @return $this|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function topicsToModerate()
    {
        try{

            //  SIDEBAR HANDLE
            $sidebar = 'moderation';
            $active = 'topics_to_moderate';
            Session::put('sidebarArguments', ['activeFirstMenu' => $active]);


            $title = trans('privateModeration.list_with_topics_to_moderate');

            return view('private.moderation.topicsToModerate', compact('title', 'sidebar', 'active'));
            } catch (Exception $e) {
        return redirect()->back()->withErrors(["topic.create" => $e->getMessage()]);
        }
    }

    /**
     * @return $this|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function postsToModerate()
    {
        try{

            //  SIDEBAR HANDLE
            $sidebar = 'moderation';
            $active = 'posts_to_moderate';
            Session::put('sidebarArguments', ['activeFirstMenu' => $active]);

            $title = trans('privateModeration.list_with_topics_to_moderate');

            return view('private.moderation.postsToModerate', compact('title', 'sidebar', 'active'));
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["topic.create" => $e->getMessage()]);
        }
    }


    /**
     * @param $type
     * @return mixed
     */
    public function getAllTopicsTable($type = null)
    {
        $listCbs = Orchestrator::getAllCbs();

        $topics = CB::getAllTopics($listCbs);

        $collection = Collection::make($topics);

        return Datatables::of($collection)
            ->editColumn('title', function($collection) use ($listCbs) {
                return "<a href='" . action('TopicController@show', [$listCbs->{$collection->cb_key}->cb_type->code, $collection->cb_key, $collection->topic_key]) . "'>" . $collection->title;
            })
            ->editColumn('cbTitle', function($collection) use ($listCbs) {
                return "<a href='" . action('CbsController@show', [$listCbs->{$collection->cb_key}->cb_type->code, $collection->cb_key]) . "'>" . $collection->cb_title . "</a>";
            })
            ->addColumn('cbType', function($collection) use ($listCbs) {
                $cbType = $listCbs->{$collection->cb_key}->cb_type->code;
                return $cbType;
            })
            ->editColumn('userName', function($collection) {
                if( $collection->created_by != 'anonymous'){
                    $user = Auth::getUserByKey($collection->created_by); //TODO check if this call should be done here

                    return "<a href='".action('UsersController@show', ['userKey' => $user->user_key, 'role' => ''])."'>".$user->name."</a>";
                }else{

                    return trans('privateModeration.anonymous');
                }
            })
            ->addColumn('action', function ($collection) use ($listCbs) {
                return '<a href="javascript:updateStatus(\''.$collection->topic_key.'\',\'moderated\',\''.$collection->cb_key.'\',\''.$listCbs->{$collection->cb_key}->cb_type->code.'\')">' . '<span class="badge badge-success">'.trans('privateModeration.moderate').'</span>' . '</a><a href="javascript:updateStatus(\''.$collection->topic_key.'\',\'not_accepted\',\''.$collection->cb_key.'\',\''.$listCbs->{$collection->cb_key}->cb_type->code.'\')">' . '<span class="badge badge-danger">'.trans('privateModeration.reject').'</span>' . '</a>';
            })
            ->make(true);
    }


    /**
     * @return mixed
     */
    public function getPostsToModerate(Request $request)
    {
        $cbs = collect(Orchestrator::getAllCbs());
        $cbsDetails = collect(CB::getListCBs($cbs));

        $cbKeysTypes = $cbs->pluck('cb_type.code', 'cb_key');
        $cbKeys = $cbsDetails->pluck('cb_key')->toArray();
        $postsToModerate = CB::getPostManagerList($cbKeys,0,1, false, $request);
        $totalRecords = $postsToModerate->recordsTotal;
        $postsToModerate = $postsToModerate->data;

        $users = Collect($postsToModerate)->pluck('created_by');
        $usersKeysNames = collect(Auth::getUserNames($users))->pluck('name', 'user_key');

        $collection = Collection::make($postsToModerate);

        return Datatables::of($collection)
            ->editColumn('topic', function($collection) use($cbKeysTypes){
                return "<a href='".action('TopicController@show', [$cbKeysTypes[$collection->cb->cb_key], $collection->cb->cb_key, $collection->topic->topic_key])."'>".$collection->topic->title."</a>";
            })
            ->editColumn('created_by', function($collection) use($usersKeysNames){
                if ($collection->created_by != 'anonymous') {
//                    return $usersKeysNames[$collection->created_by] ?? '';  //TODO: user role
//                    $role = Orchestrator::getUserRoles($collection->created_by);

                    return "<a href='".action('UsersController@show', ['userKey' => $collection->created_by])."'>".$usersKeysNames[$collection->created_by] ?? ''."</a>";

                }
                return trans('privateModeration.anonymous');
            })
            ->editColumn('content', function($collection) use($cbKeysTypes){
                return "<a href='".action('TopicController@show', [$cbKeysTypes[$collection->cb->cb_key], $collection->cb->cb_key, $collection->topic->topic_key])."'>".$collection->contents."</a>";
            })
            ->editColumn('abuses', function($collection)  use($cbKeysTypes){
                if($collection->blocked == 0){
                    $buttons = "";
                    if( count($collection->abuses) > 0 ){
                            $buttons .= '<a href="'. action('PostController@blocked', [$cbKeysTypes[$collection->cb->cb_key], $collection->cb->cb_key, $collection->topic->topic_key,$collection->post_key, 1, 'home']) .'" class="btn btn-flat btn-danger btn-xs" data-toggle="tooltip" data-original-title="Block"><i class="glyphicon glyphicon-thumbs-up"></i> '.trans("privatePosts.block").'</a>';
                    }

                    if(count($collection->abuses) == 0){
                        $labelType = "label-default";
                    } else if(count($collection->abuses) == 1){
                        $labelType = "label-warning";
                    } else {
                        $labelType = "label-danger";
                    }
                    if( count($collection->abuses) > 0 ) {
                        $content = "<a href='javascript:showAbuses(\"" . $cbKeysTypes[$collection->cb->cb_key] . "\", \"" . $collection->cb->cb_key . "\", \"" . $collection->topic->topic_key . "\", \"" . $collection->post_key . "\")'><span class='label " . $labelType . "'>" . count($collection->abuses) . "</a></span> " . $buttons;
                    }else{
                        $content = "<span class='label " . $labelType . "'>" . count($collection->abuses) . "</span> " . $buttons;
                    }
                } else {
                    $buttons = '<a href="'. action('PostController@blocked', [$cbKeysTypes[$collection->cb->cb_key], $collection->cb->cb_key, $collection->topic->topic_key,$collection->post_key, 0, 'home']) .'" class="btn btn-flat btn-success btn-xs" data-toggle="tooltip" data-original-title="unblock"><i class="glyphicon glyphicon-thumbs-up"></i> '.trans("privatePosts.unblock").'</a>';
                    $content = "<a href='javascript:showAbuses(\"".$cbKeysTypes[$collection->cb->cb_key]."\", \"".$collection->cb->cb_key."\", \"" .$collection->topic->topic_key. "\", \"".$collection->post_key."\")'><span class='badge badge-danger'>".count($collection->abuses)." / ".trans("privatePosts.blocked")."</a></span> ".$buttons;
                }
                return $content;
            })
            ->addColumn('action', function ($collection) use($cbKeysTypes){
                $html = "<a href='javascript:getComments(\"".$cbKeysTypes[$collection->cb->cb_key]."\", \"".$collection->cb->cb_key."\" , \"".$collection->topic->topic_key."\")' class=\"btn btn-flat btn-info-small btn-xs\" title=\"\" data-original-title=\"".trans("privatePosts.show")."\" data-toggle=\"tooltip\"><i class=\"fa fa-eye\"></i></a> ";

                if ($collection->active)
                    $html .= '<a href="'. action('PostController@active', [$cbKeysTypes[$collection->cb->cb_key], $collection->cb->cb_key, $collection->topic->topic_key,$collection->post_key, 1, 'comments']) .'" class="btn btn-flat btn-success btn-xs disabled" data-toggle="tooltip" data-original-title="approve"><i class="glyphicon glyphicon-thumbs-up"></i></a>';
                else
                    $html .= '<a href="'. action('PostController@active', [$cbKeysTypes[$collection->cb->cb_key], $collection->cb->cb_key, $collection->topic->topic_key,$collection->post_key, 1, 'comments']) .'" class="btn btn-flat btn-success btn-xs" data-toggle="tooltip" data-original-title="approve"><i class="glyphicon glyphicon-thumbs-up"></i></a>';

                if ($collection->blocked)
                    $html .= '<a href="'. action('PostController@blocked', [$cbKeysTypes[$collection->cb->cb_key], $collection->cb->cb_key, $collection->topic->topic_key,$collection->post_key, 1, 'comments']) .'" class="btn btn-flat btn-danger btn-xs disabled" data-toggle="tooltip" data-original-title="disapprove"><i class="glyphicon glyphicon-thumbs-down"></i> </a>';
                else
                    $html .= '<a href="'. action('PostController@blocked', [$cbKeysTypes[$collection->cb->cb_key], $collection->cb->cb_key, $collection->topic->topic_key,$collection->post_key, 1, 'comments']) .'" class="btn btn-flat btn-danger btn-xs" data-toggle="tooltip" data-original-title="disapprove"><i class="glyphicon glyphicon-thumbs-down"></i> </a>';

                return $html;
            })
            /* Makes DataTable not reordering the data again - was messing up with dates */
            ->order(function(){})
            ->skipPaging()
            ->setTotalRecords($totalRecords)
            ->make(true);
    }


    /**
     * @param Request $request
     * @return array
     */
    public function ajaxShowComments(Request $request)
    {
        $data = [];
        $data['success'] = false;

        if(empty($request->cbKey) && empty($request->topicKey) && empty($request->type) ){
            return $data;
        }

        $type = $request->type;
        $cbKey = $request->cbKey;
        $topicKey = $request->topicKey;


        $topicData = CB::getTopicDataWithChildsForModal($topicKey);
        $messages = $topicData->posts;
        $messagesNotModerated = $topicData->postsToModerate;

        $usersKeys = [];
        foreach ($messages as $message) {
            $message->created_at = Carbon::parse($message->created_at)->toDateString();
            $usersKeys[] = $message->created_by;
            foreach ($message->replies as $reply) {
                $reply->created_at = Carbon::parse($reply->created_at)->toDateString();
                $usersKeys[] = $reply->created_by;
            }
        }

        foreach ($messagesNotModerated as $item) {
            $item->created_at = Carbon::parse($item->created_at)->toDateString();
            $usersKeys[] = $item->created_by;
        }


        $usersNames = [];
        if (count($usersKeys) > 0)
            $usersNames = Auth::getListNames($usersKeys);


        $data['html'] = '';
        foreach ($messages as $i=>$comment) {
            if ($i!=0)  //TODO: Change CB to return only comments and not topic content post
                $data['html'] .=  Html::oneCommentsItemNormalModal($comment, $usersNames, $cbKey, $type, $topicKey);
        }

        $data['success'] = true;
        return $data;
    }


}
