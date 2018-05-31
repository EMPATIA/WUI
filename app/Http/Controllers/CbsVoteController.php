<?php

namespace App\Http\Controllers;

use App\ComModules\Auth;
use App\ComModules\Analytics;
use App\ComModules\CB;
use App\ComModules\Orchestrator;
use App\ComModules\Vote;
use App\Http\Requests\PostRequest;
use Illuminate\Http\Request;
use App\Http\Requests\UserRequest;
use App\One\One;
use Datatables;
use Illuminate\Support\MessageBag;
use Session;
use View;
use Breadcrumbs;
use Exception;
use Illuminate\Support\Collection;

class CbsVoteController extends Controller
{
    public function __construct()
    {

    }

    public function index()
    {

    }

    /**
     * Create a new resource.
     * @param Request $request
     * @param $type
     * @param $cbKey
     * @return View
     */
    public function create(Request $request, $type, $cbKey)
    {
        try {
            $data = [];

            $data['configurations'] = CB::getConfigurations();
            $data['genericConfigs'] = CB::getVotesConfigurations();
            $data['methodGroup'] = Vote::getListMethodGroups();
            $data['advancedConfigs'] = Vote::getGeneralConfigurationTypes();
            $data['cb'] = CB::getCbConfigurations($cbKey);

            $data['sidebar'] = 'padsType';
            $data['active'] = 'votes';

            $data['cbDetails'] = CB::getCb($cbKey);
            $data['author'] = (Auth::getUser($data['cbDetails']->created_by))->name;
            $data['cb_title'] = $data['cbDetails']->title;
            $data['cb_start_date'] = $data['cbDetails']->start_date;
            $data['title'] = trans('privateVotes.create_vote');
            $data["step"] = $request->get("step");


            $data['userLevels'] = Orchestrator::getAllEntityLoginLevels(Session::get('X-ENTITY-KEY'));

            if (!empty($data['userLevels'])){
                $data['userLevels'] = collect($data['userLevels'])->keyBy('login_level_key')->toArray();
            };

            $data['titleConfigurations'] ='Create Vote Levels';
            $data['type'] = $type;
            $data['cbKey'] = $cbKey;

            // dd($data);

            return view('private.cbs.vote', $data);
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["vote.create" => $e->getMessage()]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     * @param $cbId
     * @param $voteKey
     * @return View
     */
    public function edit($type, $cbKey, $voteKey)
    {
        try {

            $cb = CB::getCbConfigurations($cbKey);
            $configurations = CB::getConfigurations();

            $cbConfigurations = [];
            foreach ($cb->configurations as $config) {
                $cbConfigurations[$config->code][$config->id] = isset($config->pivot->value) ? json_decode($config->pivot->value) : null;
            }

            $genericConfigs = CB::getVotesConfigurations();
            $vote = CB::getCbVote($cbKey,$voteKey);
            $voteConfigs = [];
            foreach ($vote->vote_configurations as $vote_configuration) {
                $voteConfigs[$vote_configuration->vote_configuration_key] = $vote_configuration->value;
            }

            $listCbVotes = CB::getListCbVotes($cbKey);
            $name = "";
            foreach ($listCbVotes as $cbVote) {
                if ($cbVote->vote_key === $voteKey) {
                    $name = $cbVote->name;
                    break;
                }
            }
            $result = Vote::getAllShowEvents($voteKey);
            $eventVote = $result[0];

            $noEdit = false;
            $currentDate = date('Y-m-d');
            if ($eventVote->start_date < $currentDate) {
                $noEdit = true;
            }
            $html = $this->configurationsHtml($eventVote, $noEdit);
            $voteEvent = $eventVote;

            $sidebar = 'padsType';
            $active = 'votes';

            $title = trans('privateVotes.edit_vote').' '.(isset($name) ? $name: null);

            $cb = CB::getCb($cbKey);
            $cbAuthor = Auth::getUser($cb->created_by);
            $cb_title = $cb->title;
            $cb_start_date = $cb->start_date;
            $userLevels = Orchestrator::getAllEntityLoginLevels(Session::get('X-ENTITY-KEY'));

            if (!empty($userLevels)){
                $userLevels = collect($userLevels)->keyBy('login_level_key')->toArray();
            };

            $titleConfigurations='Create Vote Levels';

            $vote_conf_Key='';

            foreach ($vote->vote_configurations as $vote_configuration) {
                if($vote_configuration->code=='user_level_permissions' && $vote_configuration->value!='0')
                    $vote_conf_Key[$vote_configuration->vote_configuration_key] = json_decode($vote_configuration->value);
            }


            return view('private.cbs.vote', compact('title', 'type', 'html', 'voteEvent', 'cbKey', 'voteKey', 'name','noEdit','genericConfigs','voteConfigs', 'cb', 'cbAuthor', 'cb_title', 'cb_start_date', 'sidebar', 'active', 'configurations', 'titleConfigurations', 'userLevels','vote_conf_Key' ));
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["vote.edit" => $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     * @param $cbId
     * @param $voteKey
     * @return View
     */
    public function show(Request $request, $type, $cbKey, $voteKey)
    {
        try {
            $cb = CB::getCb($cbKey);
            $configurations = CB::getConfigurations();

            if($request->has('step'))
                $step = $request->step;

            $genericConfigs = CB::getVotesConfigurations();

            $vote = CB::getCbVote($cbKey,$voteKey);

            $voteConfigs = [];
            foreach ($vote->vote_configurations as $vote_configuration) {
                $voteConfigs[$vote_configuration->vote_configuration_key] = $vote_configuration->value;
            }

            $titleConfigurations='Create Vote Levels';

            $vote_conf_Key='';

            foreach ($vote->vote_configurations as $vote_configuration) {
                if($vote_configuration->code=='user_level_permissions' && $vote_configuration->value!='0')
                    $vote_conf_Key[$vote_configuration->vote_configuration_key] = json_decode($vote_configuration->value);
            }


            $name = $vote->name;

            $result = Vote::getAllShowEvents($voteKey);
            $voteEvent = $result[0];

            $html = $this->configurationsHtml($voteEvent, true);

            $sidebar = 'vote';
            $active = 'votes';

            $title = trans('privateVotes.show_vote').' '.(isset($name) ? $name: null);


            $cbAuthor = (Auth::getUser($cb->created_by));
            $cb_title = $cb->title;
            $cb_start_date = $cb->start_date;
            $userLevels = Orchestrator::getAllEntityLoginLevels(Session::get('X-ENTITY-KEY'));

            if (!empty($userLevels)){
                $userLevels = collect($userLevels)->keyBy('login_level_key')->toArray();
            };

            return view('private.cbs.vote', compact('title', 'type', 'html', 'voteEvent', 'cbKey', 'voteKey', 'name','genericConfigs','voteConfigs', 'cb', 'step', 'cbAuthor', 'cb_title', 'cb_start_date', 'sidebar', 'active', 'configurations', 'userLevels', 'titleConfigurations', 'vote_conf_Key'));
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["vote.show" => $e->getMessage()]);
        }
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return View
     */
    public function store($type, Request $request)
    {
        try {
            $cbKey = $request->cbKey;

            if($request->has('step'))
                $step = $request->step;

            $configurations = [];
            foreach ($request->all() as $key => $value) {
                if (strpos($key, 'config_') !== false) {
                    $key = str_replace("config_", "", $key);
                    if ($value != '')
                        $configurations[] = ['configuration_id' => $key, 'value' => $value];
                }
            }

            $newVoteEvent = Vote::setVoteEvent($request, $configurations);

            $voteNew = CB::setCbVote($request, $newVoteEvent);
            $voteKey = $voteNew->json();

            // CB::updateVoteConfigurations($request,$cbKey,$voteKey->vote_key);
            Session::flash('message', trans('voteEvent.store_ok'));

            if(isset($request->votes))
                return redirect()->action('CbsController@create', ['type'=>$type,'cbKey'=>$cbKey, 'step' => 'votes']);
            else
                return redirect()->action('CbsController@showVotes', ['type' => $type, 'cbKey' => $cbKey]);

        } catch (Exception $e) {
            //TODO: save inputs
            return redirect()->action('CbsController@showVotes', ['type' => $type, 'cbKey' => $cbKey])->with('errors', new MessageBag(['storeNok' => $e->getMessage()]));
        }
    }

    /**
     * Update the specified resource in storage.
     * @param $type
     * @param Request $request
     * @return View
     */
    public function update(Request $request,$type,$cbKey,$voteKey)
    {
        try {
            $configurations = [];

            foreach ($request->all() as $key => $value) {
                if (strpos($key, 'config_') !== false) {
                    $key = str_replace("config_", "", $key);
                    if ($value != '')
                        $configurations[] = ['configuration_id' => $key, 'value' => $value];
                }
            }

            $voteEvent = Vote::updateVoteEvent($request, $configurations);
            $cbVote = CB::updateCbVote($request,$cbKey,$voteKey);
            // $VoteConfigurations=CB::updateVoteConfigurations($request,$cbKey,$voteKey);

            Session::flash('message', trans('voteEvent.update_ok'));
            return redirect()->action('CbsController@showVotes', ['type' => $type, 'cbKey' => $cbKey]);

        } catch (Exception $e) {
            //TODO: save inputs
            return redirect()->back()->withErrors(["vote.update" => $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     * @param $cbKey
     * @param $voteKey
     * @param $type
     * @return View
     */
    public function destroy($type, $cbKey, $voteKey)
    {
        try {

            $cbVote = CB::deleteCbVote($cbKey, $voteKey);

            Session::flash('message', trans('vote.delete_ok'));
            return action('CbsController@showVotes', ['type' => $type, 'cbKey' => $cbKey]);

        } catch (Exception $e) {
            //TODO: save inputs
            return action('CbsController@show', ['type' => $type, 'cbKey' => $cbKey])->with('errors', new MessageBag(['destroyNok' => $e->getMessage()]));
        }
    }

    /**
     * Remove the specified vote event from storage.
     * @param $cbId
     * @param $voteKey
     * @return View
     */
    public function delete($type, $cbKey, $voteKey)
    {

        $data = array();
        $data['action'] = action("CbsVoteController@destroy", ['type' => $type, 'idVoteEvent' => $voteKey, 'cbKey' => $cbKey]);
        $data['title'] = trans('privateCbsVote.delete');
        $data['msg'] = trans('privateCbsVote.are_you_sure_you_want_to_delete').' ?';
        $data['btn_ok'] = trans('privateCbsVote.delete');
        $data['btn_ko'] = trans('privateCbsVote.cancel');

        return view("_layouts.deleteModal", $data);
    }


    public function voteList($type, $cbKey, $voteKey)
    {
        try{
            $module = 'cb';
            $permissionType = 'vote_list';

            $sidebar = 'vote';
            $active = 'votes';
            
            return view('private.cbs.votesList',compact('type','cbKey','voteKey','sidebar','active','module','permissionType'));
        }catch (Exception $e){
            dd($e->getMessage());
        }
    }

    public function getIndexTableVoteList(Request $request, $type, $cbKey, $voteKey)
    {
        try {
            $module = 'cb';
            $permissionType = 'vote_list';

            $role = Session::get('user_role');

            $filters = array(
                "voteKey" => $request->eventKey ?? "",
                "deleted" => !empty($request->get("deleted")) ? 1 : 0,
                "submitted" => !empty($request->get("submitted")) ? 1 : 0,
                "source" => $request->sources
            );

            if(Session::get('user_role') == 'admin'){

                $cb = CB::getCbByKey($cbKey);
                $topics = collect(CB::getTopicsByCbKey($cbKey))->keyBy('topic_key')->toArray();
                $response = Vote::getVoteList($voteKey,$role,$filters,$request);
                
                $ids = [];
                
                $votesList = $response->votes;
                $recordsTotal = $response->recordsTotal;
                $recordsFiltered = $response->recordsFiltered;

                $votesList = collect($votesList)->keyBy('id');

                foreach($votesList as $vote){
                    if(!empty($vote->user_key)){
                        $user = Auth::getuserbykey($vote->user_key);
                        $userId = $user->user_parameters[0]->user_id;
                        $userName = $user->name;
                        
                        $vote->user_id = $userId;
                        $vote->user_name = $userName;
                        $vote->topic_name = $topics[$vote->vote_key]->title;
                        if($request->userId != "" && $userId != $request->userId){
                            // $ids[] = $vote->id;
                            unset($votesList[$vote->id]);
                        }
                    }
                }

                $collection = $votesList;
            }
            else
                return null;


            
            $edit = Session::get('user_role') == 'admin';
            $delete = Session::get('user_role') == 'admin';

            return Datatables::of($collection)
                ->editColumn('select_votes', function ($collection) use ($type, $cbKey, $voteKey,$edit) {
                    return '<input class="votes_id" type="checkbox" value="' . $collection->id . '" id="' . $collection->id . '" cbType="' . $type . '" cbKey="' . $cbKey . ' "voteKey="'.$voteKey.' " voteId="' . $collection->id . ' "/>';

                })
                ->editColumn('user_key', function ($collection) use ($type,$cbKey,$edit,$delete) {
                    if($edit || $delete)
                        return $collection->user_key;
                    else
                        return null;
                })
                ->editColumn('user_name', function ($collection) use ($type,$cbKey,$edit,$delete) {
                    if($edit || $delete)
                        return "<a href='" . action('UsersController@show', ['userKey' => $collection->user_key]) . "'>" . $collection->user_name . "</a>";
                    else
                        return null;
                })
                ->editColumn('vote_key', function ($collection) use ($type,$cbKey,$edit,$delete) {
                    if($edit || $delete)
                        return $collection->vote_key;
                    else
                        return null;
                })
                ->editColumn('topic_name', function ($collection) use ($type,$cbKey,$edit,$delete) {
                    if($edit || $delete)
                        return "<a href='" . action('TopicController@show', ['type' => $type, 'cbKey' => $cbKey, 'topicKey' => $collection->vote_key]) . "'>" . $collection->topic_name . "</a>";
                    else
                        return null;
                })
                ->editColumn('submitted', function ($collection) use ($type,$cbKey,$voteKey,$edit) {
                    if($collection->submitted == 1)
                        return trans('privateCbsVote.yes');
                    else
                        return trans('privateCbsVote.no');
                })
                ->addColumn('action', function ($collection) use ($type,$cbKey, $voteKey, $edit, $delete) {
                    if($edit == true and $delete == true && $collection->submitted == 0)
                        return ONE::actionButtons(['type' => $type, 'cbKey' => $cbKey, 'voteKey' => $voteKey, 'voteId' => $collection->id, 'submit' => 'submit'], ['form' => 'vote', 'activate' => 'CbsVoteController@submitVote', 'delete' => 'CbsVoteController@deleteVote']);
                    elseif($edit == true and $delete == true && $collection->submitted == 1)
                        return ONE::actionButtons(['type' => $type, 'cbKey' => $cbKey, 'voteKey' => $voteKey, 'voteId' => $collection->id, 'submit' => 'unsubmit'], ['form' => 'vote', 'activate' => 'CbsVoteController@submitVote', 'delete' => 'CbsVoteController@deleteVote']);
                    elseif($edit == false and $delete == true)
                        return ONE::actionButtons(['type' => $type, 'cbKey' => $cbKey, 'voteId' => $collection->id], ['form'=> 'vote', 'delete' => 'CbsVoteController@deleteVote']);
                    elseif($edit == true && $delete == false)
                        return ONE::actionButtons(['type' => $type, 'cbKey' => $cbKey, 'voteKey' => $voteKey, 'voteId' => $collection->id, 'submit' => 'unsubmit'], ['form' => 'vote', 'activate' => 'CbsVoteController@submitVote']);
                    else
                        return null;
                })
                ->with('filtered', $recordsFiltered ?? 0)
                ->skipPaging()
                ->setTotalRecords($recordsTotal ?? 0)
                ->rawColumns(['select_votes','user_key','user_name','vote_key','topic_name','submitted','action'])
                ->make(true);
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["groupTypes.tableGroupTypes" => $e->getMessage()]);
        }

    }

    /**
     * Remove the specified vote event from storage.
     * @param $cbId
     * @param $voteKey
     * @return View
     */
    public function deleteVote(Request $request, $type, $cbKey, $voteKey)
    {
        $voteId = $request->voteId;
        $data = array();
        $data['action'] = action("CbsVoteController@deleteUserVote", ['type' => $type, 'cbKey' => $cbKey, 'voteKey' => $voteKey, 'voteId' => $voteId]);
        $data['title'] = trans('privateCbsVote.delete');
        $data['msg'] = trans('privateCbsVote.are_you_sure_you_want_to_delete').' ?';
        $data['btn_ok'] = trans('privateCbsVote.delete');
        $data['btn_ko'] = trans('privateCbsVote.cancel');

        return view("_layouts.deleteModal", $data);
    }

    public function deleteUserVote(Request $request, $type, $cbKey, $voteKey, $voteId){
        try {

            if(!empty($request->multipleVotes) && $request->multipleVotes == 1){
                Vote::deleteVotes($request->voteId);

                Session::flash('message', trans('vote.delete_ok'));
                return action('CbsVoteController@voteList', ['type' => $type, 'cbKey' => $cbKey, 'voteKey' => $voteKey]);
            }
            $cbVote = Vote::deleteUserVote($voteId);

            Session::flash('message', trans('vote.delete_ok'));
            return action('CbsVoteController@voteList', ['type' => $type, 'cbKey' => $cbKey, 'voteKey' => $voteKey]);

        } catch (Exception $e) {
            dd($e->getMessage());
            return redirect()->back()->withErrors(["vote.destroyNok" => $e->getMessage()]);
        }
    }

    public function submitVote(Request $request, $type, $cbKey, $voteKey)
    {
        $voteId = $request->voteId;
        $submit = $request->submit;
        $data = array();
        $data['action'] = action("CbsVoteController@submitUserVote", ['type' => $type, 'cbKey' => $cbKey, 'voteKey' => $voteKey, 'voteId' => $voteId, 'submit' => $submit]);
        if($submit == "submit"){
            $data['title'] = trans('privateCbsVote.submit');
            $data['msg'] = trans('privateCbsVote.are_you_sure_you_want_to_submit').' ?';
            $data['btn_ok'] = trans('privateCbsVote.submit');
        }else{
            $data['title'] = trans('privateCbsVote.unsubmit');
            $data['msg'] = trans('privateCbsVote.are_you_sure_you_want_to_unsubmit').' ?';
            $data['btn_ok'] = trans('privateCbsVote.unsubmit');
        }
        $data['btn_ko'] = trans('privateCbsVote.cancel');
        
        return view("_layouts.submitVoteModal", $data);
    }

    public function submitUserVote(Request $request, $type, $cbKey, $voteKey, $voteId){
        try {

            if(empty($voteId)){
                $voteId = $request->voteId;
            }
            if(!empty($request->submit) && $request->submit == "submit"){
                try{
                    Vote::submitUserVote($voteId, $request->submit);

                    Session::flash('message', trans('vote.submitted_ok'));
                    return action('CbsVoteController@voteList', ['type' => $type, 'cbKey' => $cbKey, 'voteKey' => $voteKey]);
                }catch(Exception $e){                    
                    return redirect()->back()->withErrors(["vote.submitted_nok" => $e->getMessage()]);
                }
            }
            else{
                try{
                    Vote::submitUserVote($voteId, $request->submit);

                    Session::flash('message', trans('vote.unsubmitted_ok'));
                    return action('CbsVoteController@voteList', ['type' => $type, 'cbKey' => $cbKey, 'voteKey' => $voteKey]);
                }catch(Exception $e){
                    return redirect()->back()->withErrors(["vote.unsubmitted_nok" => $e->getMessage()]);
                }
            }
            // $cbVote = Vote::submitUserVote($voteId);

            // Session::flash('message', trans('vote.submitted_ok'));
            // return action('CbsVoteController@voteList', ['type' => $type, 'cbKey' => $cbKey, 'voteKey' => $voteKey]);

        } catch (Exception $e) {
            dd($e->getMessage());
            return redirect()->back()->withErrors(["vote.submitted_nok" => $e->getMessage()]);
        }
    }

    /** Get index's vote events
     * @param $type
     * @param $cbKey
     * @return null
     * @internal param $cbId
     */
    public function getIndexTableVote($type, $cbKey)
    {

        $module = 'cb';
        $permissionType = 'pad_votes';
        if(Session::get('user_role') == 'admin'){
            $parameters = [];
            $cbVotesList = CB::getListCbVotes($cbKey);

            $cbVotesList = collect($cbVotesList);

            $cbVoteEventsData = $cbVotesList->pluck("vote_key")->toArray();
            $cbVoteEventsNames = $cbVotesList->pluck("name","vote_key")->toArray();

            $collection = collect(Vote::getVoteEventMethods($cbVoteEventsData));
        }else
            return null;

        $edit = Session::get('user_role') == 'admin';
        $delete = Session::get('user_role') == 'admin';

        return Datatables::of($collection)
            ->editColumn('name', function ($collection) use ($type,$cbKey,$cbVoteEventsNames) {
                return "<a href='" . action('CbsVoteController@show', ['type' => $type, 'cbKey' => $cbKey, 'vote' => $collection->key]) . "'>" . $cbVoteEventsNames[$collection->key] . "</a>";
            })
            ->editColumn('title', function ($collection) use ($type,$cbKey) {
                return "<a href='" . action('CbsVoteController@show', ['type' => $type, 'cbKey' => $cbKey, 'vote' => $collection->key]) . "'>" . $collection->method->name . "</a>";
            })
            ->editColumn('statistics', function ($collection) use ($type,$cbKey) {
                return "<a class='btn-info-small' href='" . action('CbsVoteController@voteEventStatistics', ['type' => $type, 'cbKey' => $cbKey,'voteEventKey' => $collection->key]) . "'><span data-toggle=\"tooltip\" title=\"\" data-original-title=\"". trans('privateCbsVote.statistics')."\"><i class=\"fa fa-bar-chart\" aria-hidden=\"true\"></i></span></a>";
            })
            ->addColumn('action', function ($collection) use ($type,$cbKey, $delete, $edit) {
                if($edit == true and $delete == true)
                    return ONE::actionButtons(['type' => $type, 'cbKey' => $cbKey, 'id' => $collection->key], ['show' => 'CbsVoteController@show', 'delete' => 'CbsVoteController@delete']);
                elseif($edit == true and $delete == false)
                    return ONE::actionButtons(['type' => $type, 'cbKey' => $cbKey, 'id' => $collection->key], ['form'=> 'vote','edit' => 'CbsVoteController@edit']);
                elseif($edit == false and $delete == true)
                    return ONE::actionButtons(['type' => $type, 'cbKey' => $cbKey, 'id' => $collection->key], ['form'=> 'vote', 'delete' => 'CbsVoteController@delete']);
                else
                    return null;
            })
            ->rawColumns(['name','title','statistics','action'])
            ->make(true);

    }

    /** Pass configurations to html
     * @param $response
     * @param $show
     * @return string
     */

    private function configurationsHtml($response, $show, $advancedConfig = false, $voteId = null)
    {

        $disabled = '';
        $readonly = '';
        if ($show == true) {
            $disabled = 'pointer-events: none;';
            $readonly = 'readonly';
        }

        if( !empty($voteId) ){
            $suffixId = $voteId."_";
        } else {
            $suffixId = "";
        }


        $configurations = $response->configurations;
        $html = '';
        if (count($configurations) > 0) {

            $html .= '<div class="row">';
            $i = 0;
            // dd($response, $configurations);
            
            foreach ($configurations as $config) {

                $html .= '<div class="col-xs-12 col-md-6">';

                // Form group

                $html .= '<div class="form-group">';

                $html .= '<label class="input-group btn-group-vertical">' . ($config->name ?? $config->code ?? $config->configuration_code ?? "") . '</label>';
//                $html .= '<input type="number" name="config[]" value="' . $config->id . '" hidden >';

                switch (strtoupper($config->parameter_type)) {
                    case 'BOOLEAN':
                        $html .= '<div class="row">';
                        $html .= '<div class="col-md-6">';
                        $html .= '<input id="inputYes'.$suffixId.$i.'" type="radio" name="'.($advancedConfig ? "advancedConfig_" : "config_").$suffixId.$config->id.'" value="1" style="margin-right:4px;'.$disabled.'"';
                        $html .= isset($config->pivot->value) ? ($config->pivot->value == '1' ? 'checked' : '') : 'checked';
                        $html .= '><label for="inputYes'.$suffixId.$i.$disabled.'" style="margin-right:40px;font-weight: normal" > ' . trans('voteEvent.yes') . '</label>';
                        $html .= '</div>';
                        $html .= '<div class="col-md-6">';
                        $html .= '<input id="inputNo' . $i .$suffixId. '" type="radio" name="'.($advancedConfig ? "advancedConfig_" : "config_").$suffixId.$config->id.'" value="0" style="margin-right:4px;font-weight: normal;' . $disabled . '"';
                        $html .= isset($config->value) ? ($config->value == '0' ? 'checked' : '') : '';
                        $html .= '><label for="inputNo' . $i .$suffixId.$disabled. '" style="font-weight: normal" > ' . trans('voteEvent.no') . '</label>';
                        $html .= '</div>';
                        $html .= '</div>';
                        if($response->code == 'multi_voting'){
                            $html .= '<div class="row" style="margin-top:20px">';
                            $html .= '<div class="col-12">';
                            $html .= '<label class="input-group btn-group-vertical">'.trans('voteEvent.weightVote').'</label>';
                            $html .= '<div class="row">';
                            $html .= '<div class="col-6 h2">';
                            $html .= '<div class="onoffswitch">';
                            $html .= '<input id="weightTypeVote" name="weightTypeVote" type="checkbox" class="onoffswitch-checkbox" value="1" onclick="weightVote()">';
                            $html .= '<label for="weightTypeVote" class="onoffswitch-label">';
                            $html .= '<span class="onoffswitch-inner"></span>';
                            $html .= '<span class="onoffswitch-switch"></span>';
                            $html .= '</label>';
                            $html .= '</div>';
                            $html .= '</div>';
                            $html .= '</div>';
                            $html .= '</div>';
                            $html .= '</div>';
                        }
                        break;
                    case 'NUMERIC':
                        $html .= '<input class="form-control" type="number" name="'.($advancedConfig ? "advancedConfig_" : "config_").$suffixId.$config->id.'" min="0" placeholder="Number" value="' . (isset($config->value) ? $config->value : 0) . '" ' . $readonly . '>';
                        break;
                }
                $html .= '</div>';

                $html .= '</div>';
                $i++;
            }

            $html .= '</div>';

        }

        return $html;
    }

    public function weightVote(Request $request){
        $languages = [];
        $languages = Orchestrator::getLanguageList();

        $weightId = $request->vezes;
        $langs = [];
        foreach($languages as $language){
            $id = $weightId . '_' . $language->code;
            $langs[$id] = ['title' => $language->name, 'html' => '<label for="text">Text</label><input class="form-control" type="text" name="text-'. $weightId .'-'.$language->code.'">'];
        }

        return view('private.cbs.weightVote', compact('langs','weightId'));
    }

    /*
     * Get all methods
     * @param PostRequest $request
     * @return string
     */
    public function getMethods(PostRequest $request)
    {
        $groupId = $request->postId;

        $methodsList = Vote::getGroupMethod($groupId);

        $html = '';
        if (count($methodsList) > 0) {

            $html .= '<p></p><select class="form-control" id="methodSelect" name="methodSelect" required onchange="getMethodConfigurations()">';
            $html .= '<option value="">Select method types</option>';
            foreach ($methodsList as $option) {
                $html .= '<option  value="' . $option->id . '"> ' . $option->name  . '</option>';
            }
            $html .= '</select>';
        }
        return $html;
    }


    /**
     * Get all methods
     * @param PostRequest $request
     * @return string
     */

    public function getMethodsData(PostRequest $request)
    {
        $groupId = $request->postId;
        $methodsList = Vote::getGroupMethod($groupId);
        return $methodsList;
    }

    /**
     * Get methods configurations
     * @param PostRequest $request
     * @return mixed
     */
    public function getMethodConfigurations(PostRequest $request)
    {

        $methodId = $request->postId;
        $advancedConfig = $request->advancedConf?:false;
        $voteId = $request->voteId?:null;
        $voteMethodsConfiguration = Vote::getVoteMethodWithConfigurations($methodId);

        return $html = $this->configurationsHtml($voteMethodsConfiguration, false, $advancedConfig,$voteId);
    }

    public function getParameterTypes(Request $request)
    {
        $configCode = $request->configCode;

        $parameterTypes = Orchestrator::getVoteConfigParameterTypes($configCode);

        $parameterTypesArray = array();
        foreach ($parameterTypes as $type) {
            if (!in_array($type->parameter_type_code, $parameterTypesArray)) {
                $parameterTypesArray[] = $type->parameter_type_code;
            }
        }

        $parameterUserTypes = Orchestrator::getParameterUserTypesList($parameterTypesArray);

        $html = '';
        if (count($parameterUserTypes) > 0) {
            $html .= '<p></p><select class="form-control" id="parameterTypeSelect" name="parameterTypeSelect" required onchange="getAdvancedConfigurations()">';
            $html .= '<option value="">' . trans("privateCbs.selectParameterUserType") . '</option>';
            foreach ($parameterUserTypes as $option) {
                $html .= '<option  value="' . $option->parameter_user_type_key . '"> ' . $option->name . '</option>';
            }
            $html .= '</select>';
            $html .= '<div id="advancedConfigs" class="box-body">';
            if (in_array('minimum_age', $parameterTypesArray)) {
                $html .= 'minimum_age';
            } elseif (in_array('age', $parameterTypesArray)) {
                $html .= 'age';
            }
            $html .= '</div>';
        }
        return $html;
    }


    public function voteEventStatistics($type, $cbKey,$voteEventKey)
    {
        try {
            $cb = CB::getCb($cbKey);
            $statisticsByDate = Analytics::getVoteStatisticsByDate($voteEventKey);
//            $statisticsByTown = Analytics::getVoteStatisticsByTown($voteEventKey);
//            $statisticsByAge = Analytics::getVoteStatisticsByAge($voteEventKey);
//            $statisticsByGender = Analytics::getVoteStatisticsByGender($voteEventKey);
//            $statisticsByProfession = Analytics::getVoteStatisticsByProfession($voteEventKey);
//            $statisticsByEducation = Analytics::getVoteStatisticsByEducation($voteEventKey);
            $data = [];
            $data["type"] = $type;
            $data["cbKey"] = $cbKey;
            $data["voteEventKey"] = $voteEventKey;
            $data["votesByDate"] = $statisticsByDate;
            $data["cb"] = $cb;

//            $data["votesByTown"] = $statisticsByTown;
//            $data["votesByAge"] = $statisticsByAge;
//            $data["votesByGender"] = $statisticsByGender;
//            $data["votesByProfession"] = $statisticsByProfession;
//            $data["votesByEducation"] = $statisticsByEducation;


            return view('private.cbs.statistics.voteStatistics', $data);


        }catch(Exception $e){
            return redirect()->back()->withErrors([trans("privateCbsVote.vote_statistics_error") => $e->getMessage()]);
        }
    }

    public function getStatistics(Request $request,$voteEventKey){
        try {
            $statisticsType = $request->statistics_type;
            $data = [];
            $view = '';
            if(!$statisticsType){
                return 'false';
            }
            switch ($statisticsType){
                case 'town':
                    $statisticsByTown = Analytics::getVoteStatisticsByTown($voteEventKey);
                    $data["votesByTown"] = $statisticsByTown;
                    $view = 'private.cbs.statistics.statisticsByTown';
                    break;
                case 'age':
                    $statisticsByAge = Analytics::getVoteStatisticsByAge($voteEventKey);
                    $data["votesByAge"] = $statisticsByAge;
                    $view = 'private.cbs.statistics.statisticsByAge';
                    break;
                case 'gender':
                    $statisticsByGender = Analytics::getVoteStatisticsByGender($voteEventKey);
                    $data["votesByGender"] = $statisticsByGender;
                    $view = 'private.cbs.statistics.statisticsByGender';
                    break;
                case 'profession':
                    $statisticsByProfession = Analytics::getVoteStatisticsByProfession($voteEventKey);
                    $data["votesByProfession"] = $statisticsByProfession;
                    $view = 'private.cbs.statistics.statisticsByProfession';
                    break;
                case 'education':
                    $statisticsByEducation = Analytics::getVoteStatisticsByEducation($voteEventKey);
                    $data["votesByEducation"] = $statisticsByEducation;
                    $view = 'private.cbs.statistics.statisticsByEducation';
                    break;
            }
            if(empty($data)){
                return 'false';
            }
            return view($view, $data);

        }catch (Exception $e){
            return 'false';
        }

    }


    /**
     * RETURN THE VIEW TO REGISTER IN PERSON VOTING
     * @param $type
     * @param $cbKey
     * @param $voteEventKey
     * @return View
     */
    public function registerInPersonVoting($type, $cbKey, $voteKey){
        try {
            $cb = CB::getCb($cbKey);
            $title = trans("privateUsers.inPersonRegistrationTitleFor").' '.$type.' '.$cb->title;

            $sidebar = 'vote';
            $active = 'registerInPersonVoting';
            return view('private.cbs.registerInPersonVoting', compact('title','voteKey','cbKey','sidebar','active','type'));
        }catch(Exception $e) {
            return redirect()->back();
        }
    }


    /**
     * PRINT NEW LINE TO INFORM THE PO
     * @param $code
     * @param $topics
     * @param $votesReplaced
     * @return string
     */
    public function generateHtmlLineForUserVotes($code,$topics,$votesReplaced)
    {

        $topicsHtml = '';
        if(count($topics) > 0){
            foreach ($topics as $topic){
                $topicsHtml .= '<b data-toggle="tooltip" class="project-item" title="'.$topic->title.'">#'.$topic->topic_number.'</b> ';
            }
        }

        if(isset($votesReplaced) && $votesReplaced){
            return '<div class="new-user-line">
                    <div class="col-xs-2">'.$code.'</div>
                    <div class="col-xs-2"><span class="color-blue"><i>'.trans('privateUsers.votes_replaced').'</i></span></div>
                    <div class="col-xs-8">'.$topicsHtml.'</div>
                    </div>';

        }else{
            return '<div class="new-user-line">
                    <div class="col-xs-2">'.$code.'</div>
                    <div class="col-xs-2"><span class="color-green"><i>'.trans('privateUsers.votes_registered').'</i></span></div>
                    <div class="col-xs-8">'.$topicsHtml.'</div>
                    </div>';
        }
    }


    /**
     * BUILD THE OBJECT TO ATTACH A USER TO THE EVENT VOTE CODE
     * @param Request $request
     * @param bool $replaced
     * @return \Illuminate\Http\JsonResponse|string
     */
    public function saveInPersonVotes(Request $request, $replaced = false)
    {
        try{
            $voteEventKey = $request['vote_event_key'];
            $inputs = $request['inputs'];
            $cbKey = $request['cbKey'];
            $topicKeys = [];

            $fieldsToVote = [];

            foreach ($inputs as $input){
                $fieldsToVote[$input['name']] = $input['value'];
            }

            //PREVENT IF EMPTY
            if(!empty($fieldsToVote['code']) && !empty($fieldsToVote['votes'])){
                $userVotes = explode(",",$fieldsToVote['votes']);
                $getTopicsResponse = CB::getTopicsToByTopicNumber($cbKey,$userVotes);
                if(isset($getTopicsResponse['error'])){
                    return response()->json(["error" => trans('registerInPersonVoting.errorInRetrievingTopics')]);
                }else{
                    $topicKeys = collect($getTopicsResponse)->pluck(['topic_key']);
                }
                $voteResponse = Vote::registerUserInPersonVoting($fieldsToVote['code'], $voteEventKey, $topicKeys);
                if(isset($voteResponse['error'])){
                    if($voteResponse['error'] == 409){ //USER HAS ALREADY VOTED
                        return response()->json(["warning" => '<i class="fa fa-exclamation-triangle replace-user-votes cursor"
                                                                  id="'.uniqid().'" aria-hidden="true" style="color:orange;"
                                                                  title="'.$voteResponse['message'].'"></i>']);
                    }elseif($voteResponse['error'] == 408){ //NO CODE
                        return response()->json(["error" => $voteResponse['message']]);
                    }else{
                        return response()->json(["error" => $voteResponse['message']]);
                    }
                }

                return $this->generateHtmlLineForUserVotes($fieldsToVote['code'],$getTopicsResponse,$replaced);

            }else{
                return response()->json(["error" => trans('registerInPersonVoting.errorNeedToFillRequiredFields')]);
            }

        }catch(Exception $e) {
            return response()->json(["error" => trans('registerInPersonVoting.errorInSaveInPersonVotes')]);
        }
    }


    /**
     * THIS FUNCTION DELETES THE USER PREVIOUS VOTES
     * AND REPLACES WITH THE SUPPLIED ONES
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function replaceUserVotesWithInPersonVotes(Request $request)
    {
        try{
            $voteEventKey = $request['vote_event_key'];
            $inputs = $request['inputs'];

            $fieldsToVote = [];

            foreach ($inputs as $input){
                $fieldsToVote[$input['name']] = $input['value'];
            }
            if(!empty($fieldsToVote['code'])){
                $voteDeleteResponse = Vote::deleteUserVotesInVoteEvent($fieldsToVote['code'],$voteEventKey );
                if(isset($voteDeleteResponse['error'])){
                    return response()->json(["error" => $voteDeleteResponse['message']]);
                }else{
                    return $this->saveInPersonVotes($request,true);
                }
            }else{
                return response()->json(["error" => trans('registerInPersonVoting.errorNeedToFillRequiredFields')]);
            }

        }catch(Exception $e) {
            return response()->json(["error" => trans('registerInPersonVoting.errorInReplaceUserVotesWithInPersonVotes')]);
        }
    }

    public function mapVotesToParameter($type, $cbKey, $voteKey) {
        try {
            $cbParameters = CB::getCbParameters($cbKey);

            $sidebar = 'vote';
            $active = 'votes';
            
            return view('private.cbs.mapVotesToParameter', compact('cbParameters','type','cbKey','voteKey','sidebar','active'));
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["private.cbs.mapVotesToParameter.create" => $e->getMessage()]);
        }
    }

    public function mapVotesToParameterSubmit(Request $request, $type, $cbKey, $voteKey) {
        try {
            $parameterId = $request->get("parameter");
            CB::exportVotesCountToParameter($cbKey, $parameterId, $voteKey);
            
            Session::flash('message', trans('mapVotesToParameter.submit_ok'));
            return redirect()->action('CbsController@show', ['type' => $type, 'cbKey' => $cbKey]);
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["private.cbs.mapVotesToParameter.submit" => $e->getMessage()]);
        }
    }

}
