<?php

namespace App\Http\Controllers;

use App\ComModules\Auth;
use App\ComModules\CB;
use App\ComModules\Orchestrator;
use App\Http\Requests\CBRequest;
use App\Http\Requests\PostRequest;
use App\One\One;
use Carbon\Carbon;
use Datatables;
use Illuminate\Support\Facades\Route;
use Session;
use URL;
use View;
use Alert;
use Breadcrumbs;
use Exception;
use Illuminate\Support\Collection;


class PublicForumController extends Controller
{
    private $entity_id;

    public function __construct()
    {
        if(Route::current() == null) return;

        View::share('title', trans('forum.title'));


        Breadcrumbs::register('public.forum.index', function ($breadcrumbs) {
            $breadcrumbs->parent('public');
            $breadcrumbs->push(trans('forum.index'), action('PublicForumController@index'));
        });

        Breadcrumbs::register('public.forum.show', function ($breadcrumbs) {
            $breadcrumbs->parent('public.forum.index');
            $breadcrumbs->push(trans('forum.show'));
        });

        Breadcrumbs::register('public.forum.create', function ($breadcrumbs) {
            $breadcrumbs->parent('public.forum.index');
            $breadcrumbs->push(trans('forum.create'));
        });

        Breadcrumbs::register('public.forum.store', function ($breadcrumbs) {
            $breadcrumbs->parent('public.forum.index');
            $breadcrumbs->push(trans('forum.show'));
        });

        Breadcrumbs::register('public.forum.update', function ($breadcrumbs) {
            $breadcrumbs->parent('public.forum.index');
            $breadcrumbs->push(trans('forum.show'));
        });
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        try {
            $list = Orchestrator::getCbTypes('forum');
            $forum = [];
            $usersNames = [];
            if (count($list) == 0) {
                return view('public.'.ONE::getEntityLayout().'.forum.index', compact('forum', 'usersNames'));
            }
            $forum = CB::getListCBs($list);
            $usersKeys = [];
            foreach ($forum as $topic) {
                if(isset($topic->lastpost->updated_at)){
                    $user = $topic->lastpost->created_by;

                    if(!array_key_exists($user , $usersKeys))
                        $usersKeys[] = $user;

                    $user = $topic->lasttopic->created_by;

                    if(!array_key_exists($user , $usersKeys))
                        $usersKeys[] = $user;
                }
                $usersKeys[] = $topic->created_by;
            }


            if(count($usersKeys) > 0){
                $usersNames = json_decode(Auth::getListNames($usersKeys), true)['data'];
            }

            return view('public.'.ONE::getEntityLayout().'.forum.index', compact('forum', 'usersNames'));

        } catch (Exception $e) {
            return redirect()->back()->withErrors(["forum.list" => $e->getMessage()]);
        }
    }

    /**
     * Create a new resource.
     *
     * @return Response
     */
    public function create()
    {

        try {
            $configurations = CB::getConfigurations();

            return view('public.'.ONE::getEntityLayout().'.forum.forum', compact('configurations'));

        } catch (Exception $e) {
            return redirect()->back()->withErrors(["forum.create" => $e->getMessage()]);
        }

    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param $id
     * @return View
     */
    public function edit($cbId)
    {
        try {
            $configurations = CB::getConfigurations();
            $forum = CB::getCbConfigurations($cbId);
            $forumConfigurations = [];
            foreach ($forum->configurations as $config) {
                $forumConfigurations[] = $config->id;
            }
            return view('public.'.ONE::getEntityLayout().'.forum.forum', compact('forum', 'configurations', 'forumConfigurations'));

        } catch (Exception $e) {
            return redirect()->back()->withErrors(["forum.edit" => $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return Response
     */
    public function show($cbId)
    {
        try {
            $configurations = CB::getConfigurations();
            $forum = CB::getCbConfigurations($cbId);
            $forumConfigurations = [];
            foreach ($forum->configurations as $config) {
                $forumConfigurations[] = $config->id;
            }
            $cbModerators = CB::getCbModerators($cbId);
            $keys = [];
            foreach($cbModerators as $cbModerator){
                $keys[] = $cbModerator->user_key;
                $dateAdded = Carbon::createFromFormat('Y-m-d H:i:s',$cbModerator->created_at)->toDateString();
                $moderators[$cbModerator->user_key]['date_added'] = $dateAdded;
            }
            if (count($keys) > 0) {
                $names = Auth::getListNames($keys);
                $moderators = array_merge_recursive(json_decode($names, true)['data'],$moderators);
            }
            return view('public.'.ONE::getEntityLayout().'.forum.forum', compact('forum', 'configurations', 'forumConfigurations', 'moderators'));

        } catch (Exception $e) {
            return redirect()->back()->withErrors(["forum.show" => $e->getMessage()]);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param CBRequest $requestCB
     * @return $this|View
     */
    public function store(CBRequest $requestCB)
    {
        try {
            $configurations = CB::getConfigurations();
            $arrayConfIDs = [];
            foreach ($configurations as $configuration) {
                foreach ($configuration->configurations as $options) {
                    $arrayConfIDs[] = $options->id;
                }
            }
            $arrayConfigurations = [];
            foreach ($requestCB->all() as $key => $value) {
                if (strpos($key, 'configuration_') !== false) {
                    $id = str_replace("configuration_", "", $key);
                    $arrayConfigurations[] = $id;
                    unset($arrayConfIDs[array_search($id, $arrayConfIDs)]);
                }
            }
            $cb = CB::setNewCb($requestCB);
            CB::setCbConfigurations($cb->id,$arrayConfigurations);
            Orchestrator::setNewCb('forum',$cb->id);
            Session::flash('message', trans('forum.store_ok'));
            return redirect()->action('PublicForumController@show', ['cbId' => $cb->id]);

        } catch (Exception $e) {
            //TODO: save inputs
            return redirect()->back()->withErrors(["forum.store" => $e->getMessage()]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param CBRequest $requestCB
     * @param $id
     * @return $this|View
     */
    public function update(CBRequest $requestCB, $cbId)
    {
        try {

            $configurations = CB::getConfigurations();
            $arrayConfIDs = [];
            foreach ($configurations as $configuration) {
                foreach ($configuration->configurations as $options) {
                    $arrayConfIDs[] = $options->id;
                }
            }
            $cb = CB::updateCB($cbId,$requestCB);
            $arrayConfigurations = [];
            foreach ($requestCB->all() as $key => $value) {
                if (strpos($key, 'configuration_') !== false) {
                    $id = str_replace("configuration_", "", $key);
                    $arrayConfigurations[] = $id;
                    unset($arrayConfIDs[array_search($id, $arrayConfIDs)]);
                }
            }
            CB::setCbConfigurations($cbId,$arrayConfigurations);
            Session::flash('message', trans('forum.updateOk'));
            return redirect()->action('PublicForumController@show', ['cbId' => $cbId]);
        } catch (Exception $e) {
            //TODO: save inputs
            return redirect()->back()->withErrors(["forum.update" => $e->getMessage()]);
        }
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  $id
     * @return Response
     */
    public function destroy($cbId)
    {

        try {
            CB::deleteCb($cbId);
            Orchestrator::deleteCb('forum',$cbId);
            Session::flash('message', trans('forum.delete_ok'));

            return action('PublicForumController@index');

        } catch (Exception $e) {
            //TODO: save inputs
            return redirect()->back()->withErrors(["forum.destroy" => $e->getMessage()]);
        }
    }


    /**
     * Show confirm popup to remove the specified resource from storage.
     *
     * @param $id
     * @return View
     */
    public function delete($id){
        $data = array();

        $data['action'] = action("PublicForumController@destroy", $id);
        $data['title'] = "DELETE";
        $data['msg'] = "Are you sure you want to delete this Content?";
        $data['btn_ok'] = "Delete";
        $data['btn_ko'] = "Cancel";

        return view("_layouts.deleteModal", $data);
    }

    /**
     * Remove the specified Moderator from storage.
     *
     * @param  $id
     * @return Response
     */
    public function deleteModerator($cbId, $idModerator)
    {


        try {

            CB::deleteModerator($cbId,$idModerator);
            return action('PublicForumController@show', $cbId);

        } catch (Exception $e) {
            //TODO: save inputs
            return action('PublicForumController@show', $cbId);
        }
    }



    /**
     * Show confirm popup to remove the specified resource from storage.
     *
     * @param $id
     * @return View
     */
    public function deleteModeratorConfirm($cbID, $idModerator){
        $data = array();

        $data['action'] = action("PublicForumController@deleteModerator", ['cbId'=> $cbID, 'id' => $idModerator]);
        $data['title'] = "DELETE";
        $data['msg'] = "Are you sure you want to delete this Moderator?";
        $data['btn_ok'] = "Delete";
        $data['btn_ko'] = "Cancel";

        return view("_layouts.modal", $data);
    }



    /**
     * Add managers to Cb.
     *
     * @param PostRequest $request
     * @return Response
     * @internal param $idPost
     * @internal param PostRequest $requestPost
     */
    public function addModerator(PostRequest $request)
    {
        try{
            $moderators = [];
            $keys = json_decode($request->moderatorsKey);
            foreach($keys as $key){
                $moderators[] = array('cb_id' => $request->idCb, 'user_key' => $key,'type_id' => 1);
            }
            CB::setCbModerators($request->idCb,$moderators);

            return action('PublicForumController@show', $request->idCb);

        } catch (Exception $e) {
            return 'false';
        }
    }





    /**
     * Get all Users
     *
     * @param $id
     * @return View
     */
    public function allUsers($cbId)
    {
        $usersList = Orchestrator::getAllUsers();
        $cbModerators = CB::getCbModerators($cbId);
        $keys = [];
        foreach($cbModerators as $cbModerator){
            $keys[] = $cbModerator->user_key;
        }
        $usersKeys = [];
        foreach ($usersList as $item) {
            if(!in_array($item->user_key, $keys))
                $usersKeys[] = $item->user_key;
        }
        if(count($usersKeys) > 0) {

            $names = Auth::getListNames($usersKeys);
            $users = json_decode($names, true)['data'];

            if(count($users) > 0) {
                $html = '<table  class="table table-hover table-striped dataTable no-footer table-responsive">';
                $html .= '<tbody>';
                $i = 0;
                foreach ($users as $user) {
                    $html .= '<tr class="col-md-12" style="height: 60px; border-bottom: 1px solid #999;">';

                    $html .= '<td class="col-md-1" class="bs-checkbox" style="padding:10px; vertical-align: middle;">';
                    $html .= '<input name="selectManager[]" type="checkbox" value="' . $user['user_key'] . '" >';
                    $html .= '</td>';
                    $html .= '<td class="col-md-4" style="text-align: center ">';
                    if ($user['photo_id'] > 0) {
                        $html .= '<img class="img-circle" src="'.URL::action('FilesController@download', ['id' => $user['photo_id'], 'code' => $user['photo_code'], 1] ).'" alt="User Image" style="height: 40px;">';
                    } else {
                        $html .= '<img class="img-circle" src="/images/icon-user-default-160x160.png" alt="User Image" style="height: 40px;">';
                    }
                    $html .= '</td>';
                    $html .= '<td class="col-md-7" style="padding: 10px;vertical-align: middle; ">';
                    $html .= '<p>' . $user['name'] . '</p>';
                    $html .= '</td>';

                    $html .= '</tr>';
                    $i++;

                }
                $html .= '</tbody>';
                $html .= '</table>';

                return $html;
            }

        }
        return '<div style="text-align: center; min-height: 100px;padding-top: 40px; color:#3c8dbc; text-transform: uppercase"><b>Without Users to show</b></div>';
    }




    /**
     * Get all Users
     *
     * @param $id
     * @return View
     */
    public function allManagers($cbId)
    {

        $usersList = Orchestrator::getAllManagers();
        $cbModerators = CB::getCbModerators($cbId);
        $keys = [];
        foreach($cbModerators as $cbModerator){
            $keys[] = $cbModerator->user_key;
        }
        $usersKeys = [];
        foreach ($usersList as $item) {
            if(!in_array($item->user_key, $keys))
                $usersKeys[] = $item->user_key;
        }

        if(count($usersKeys) > 0) {

            $names = Auth::getListNames($usersKeys);
            $users = json_decode($names, true)['data'];

            if(count($users) > 0) {

                $html = '<table  class="table table-hover table-striped dataTable no-footer table-responsive">';
                $html .= '<tbody>';

                $i = 0;
                foreach ($users as $user) {
                    $html .= '<tr class="col-md-12" style="height: 60px; border-bottom: 1px solid #999;">';

                    $html .= '<td class="col-md-1" class="bs-checkbox" style="padding:10px; vertical-align: middle;">';
                    $html .= '<input name="selectManager[]" type="checkbox" value="' . $user['user_key'] . '" >';
                    $html .= '</td>';
                    $html .= '<td class="col-md-4" style="text-align: center ">';

                    if ($user['photo_id'] > 0) {
                        $html .= '<img class="img-circle" src="'.URL::action('FilesController@download', ['id' => $user['photo_id'], 'code' => $user['photo_code'], 1] ).'" alt="User Image" style="height: 40px;">';
                        dd($html);
                    } else {
                        $html .= '<img class="img-circle" src="/images/icon-user-default-160x160.png" alt="User Image" style="height: 40px;">';
                    }
                    $html .= '</td>';
                    $html .= '<td class="col-md-7" style="padding: 10px;vertical-align: middle; ">';
                    $html .= '<p>' . $user['name'] . '</p>';
                    $html .= '</td>';

                    $html .= '</tr>';
                    $i++;

                }
                $html .= '</tbody>';
                $html .= '</table>';

                return $html;
            }

        }

        return '<div style="text-align: center; min-height: 100px;padding-top: 40px; color:#3c8dbc; text-transform: uppercase"><b>Without Managers to show</b></div>';
    }


}
