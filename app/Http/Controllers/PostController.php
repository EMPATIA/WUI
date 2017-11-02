<?php

namespace App\Http\Controllers;

use App\ComModules\CB;
use Datatables;
use Session;
use View;
use Breadcrumbs;
use HttpClient;
use App\One\One;
use Illuminate\Support\Collection;


class PostController extends Controller
{
    public function __construct()
    {

    }

    /**
     *
     *
     * @param $cbKey
     * @param $topicKey
     * @param $postKey
     * @return View
     */
    public function blocked($type, $cbKey, $topicKey, $postKey, $value, $redirect)
    {
        try {

            CB::updatePostBlock($postKey, $value);

            if ($redirect == 'posts') {
                return  redirect()->action('TopicController@showPosts', ['type' => $type, 'cbKey' => $cbKey, 'topicKey' => $topicKey]);
            }

            if ($redirect == 'home') {
                return redirect('/private');
            }

            if ($redirect == 'comments') {
                return  redirect()->action('CbsController@showCbComments', ['type' => $type, 'cbKey' => $cbKey]);
            }

        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["topic.show" => trans("PrivatePost.errorWhileRemovingPost") ]);
        }
    }




    /**
     *
     *
     * @param $cbKey
     * @param $topicKey
     * @param $postKey
     * @return View
     */
    public function active($type, $cbKey, $topicKey, $postKey, $value, $redirect)
    {
        try {
            $response = CB::activePost($postKey, $value);

            if ($redirect == 'posts') {
                return  redirect()->action('TopicController@showPosts', ['type' => $type, 'cbKey' => $cbKey, 'topicKey' => $topicKey]);
            }

            if ($redirect == 'home') {
                return redirect('/private');
            }
            if ($redirect == 'moderation') {
                return redirect('/private/moderation/posts');
            }

            if ($redirect == 'comments') {
                return  redirect()->action('CbsController@showCbComments', ['type' => $type, 'cbKey' => $cbKey]);
            }

        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["topic.show" => trans("PrivatePost.errorWhileRemovingPost") ]);
        }
    }


    /**
     * Returns remove confirm view for modal display.
     *
     * @param $cbKey
     * @param $topicKey
     * @param $postKey
     * @return View
     */
    public function delete($type, $cbKey, $topicKey, $postKey)
    {
        $data = array();

        $data['action'] = action("PostController@destroy", ['type' => $type, 'cbKey' => $cbKey, 'topicKey' => $topicKey, 'postKey' => $postKey]);
        $data['title'] = "DELETE";
        $data['msg'] = "Are you sure you want to delete this Post?";
        $data['btn_ok'] = "Delete";
        $data['btn_ko'] = "Cancel";

        return view("_layouts.deleteModal", $data);
    }

    /**
     * Remove the specified post from storage.
     *
     * @param $cbKey
     * @param $topicKey
     * @param $postKey
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function destroy($type,$cbKey,$topicKey,$postKey, $redirect){

        try {
            $response = CB::deletePost($postKey);

            if ($redirect == 'posts') {
                return  redirect()->action('TopicController@showPosts', ['type' => $type, 'cbKey' => $cbKey, 'topicKey' => $topicKey]);
            }

            if ( $redirect == 'home') {
                return redirect('/private');
            }

        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["topic.show" => trans("PrivatePost.errorWhileRemovingPost") ]);
        }
    }

}
