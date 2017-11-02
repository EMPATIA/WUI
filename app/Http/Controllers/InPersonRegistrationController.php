<?php

namespace App\Http\Controllers;

use App\ComModules\Notify;
use App\ComModules\Auth;
use App\ComModules\CB;
use App\ComModules\Files;
use App\ComModules\Orchestrator;
use App\ComModules\Vote;
use App\Http\Requests\InPersonRegistrationRequest;
use App\One\One;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Datatables;
use Session;
use View;

class InPersonRegistrationController extends Controller
{
    private $cbType;

    public function __construct()
    {
        $this->cbType = [
            'forum' => 'forum',
            'discussion' => 'discussion',
            'proposal' => 'proposal',
            'idea' => 'idea',
            'tematicConsultation' => 'tematicConsultation',
            'publicConsultation' => 'publicConsultation',
            'survey' => 'survey'
        ];
    }


    /**
     * Display a listing of the resource.
     *
     * @return View
     */
    public function index()
    {
        $title = trans('privateUsers.inPersonRegistration');

        $sidebar = 'registration';
        $active = 'personRegistration';

        Session::put('sidebarArguments', ['activeFirstMenu' => 'personRegistration']);

        return view("private.inPersonRegistration.index", compact('title', 'sidebar', 'active'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return View
     */
    public function create()
    {
        if(Session::get('user_role') != 'admin'){
            if(!ONE::verifyUserPermissionsCreate('auth', 'in_person_registration')) {
                return redirect()->back()->withErrors(["inPersonResgistration.create" => trans('privateEntitiesDivided.permission_message')]);
            }
        }

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
                    $parameterOptions [] = [
                        'parameter_user_option_key' => $option->parameter_user_option_key,
                        'name' => $option->name,
                        'selected' => $selected
                    ];
                }
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

        $sidebar = 'registration';
        $active = 'personRegistration';

        Session::put('sidebarArguments', ['activeFirstMenu' => 'personRegistration']);

        return view('private.inPersonRegistration.user',compact('registerParameters', 'sidebar', 'active'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param InPersonRegistrationRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(InPersonRegistrationRequest $request)
    {
        if(Session::get('user_role') != 'admin'){
            if(!ONE::verifyUserPermissionsCreate('auth', 'in_person_registration')) {
                return redirect()->back()->withErrors(["inPersonResgistration.store" => trans('privateEntitiesDivided.permission_message')]);
            }
        }

        try {
            $userDetails = $request->all();
            $data['name'] = $request->name;
            $data['identity_card'] = isset($request->identity_card) ? $request->identity_card : null;
            unset($userDetails['_token']);
            unset($userDetails['form_name']);
            unset($userDetails['_method']);
            unset($userDetails['name']);
            unset($userDetails['identity_card']);

            $user = Auth::storeUserInPerson($data,$userDetails);
            $userEntity = Orchestrator::storeUser($user->user_key, ONE::getEntityKey());
            $userStatus = Orchestrator::updateUserStatus('authorized',$user->user_key);
            
            Session::flash('message', trans('privateInPersonRegistration.storeOk'));
            return redirect()->action('InPersonRegistrationController@show', $user->user_key);

        }
        catch(Exception $e) {
            return redirect()->back()->withErrors([ trans('privateInPersonRegistration.storeNok') => $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param $userKey
     * @return \Illuminate\Http\RedirectResponse|View
     */
    public function show($userKey)
    {
        if(Session::get('user_role') != 'admin'){
            if(!ONE::verifyUserPermissionsShow('auth', 'in_person_registration')) {
                return redirect()->back()->withErrors(["inPersonResgistration.show" => trans('privateEntitiesDivided.permission_message')]);
            }
        }

        try {

            $user = Auth::getUserByKey($userKey);
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
            $sidebar = 'registration';
            $active = 'personRegistration';

            Session::put('sidebarArguments', ['activeFirstMenu' => 'personRegistration']);
            return view('private.inPersonRegistration.user', compact('registerParameters','user', 'sidebar', 'active'));
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors([ trans('privateInPersonRegistration.show') => $e->getMessage()]);
        }

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param $userKey
     * @return \Illuminate\Http\RedirectResponse|View
     */
    public function edit($userKey)
    {
        if(Session::get('user_role') != 'admin'){
            if( !ONE::verifyUserPermissionsUpdate('auth', 'in_person_registration')) {
                return redirect()->back()->withErrors(["inPersonResgistration.edit" => trans('privateEntitiesDivided.permission_message')]);
            }
        }

        try {

            $user = Auth::getUserByKey($userKey);
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

            return view('private.inPersonRegistration.user', compact('registerParameters','user'));

        }
        catch(Exception $e) {
            return redirect()->back()->withErrors([ trans('privateInPersonRegistration.edit') => $e->getMessage()]);
        }
    }

    /**
     * Update the specified resource in storage.
     * @param InPersonRegistrationRequest $request
     * @param $userKey
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(InPersonRegistrationRequest $request, $userKey)
    {
        if(Session::get('user_role') != 'admin'){
            if(!ONE::verifyUserPermissionsUpdate('auth', 'in_person_registration')) {
                return redirect()->back()->withErrors(["inPersonResgistration.update" => trans('privateEntitiesDivided.permission_message')]);
            }
        }
        
        try {
            $userDetails = $request->all();
            $data['name'] = $request->name;
            $data['identity_card'] = isset($request->identity_card) ? $request->identity_card : null;
            unset($userDetails['_token']);
            unset($userDetails['form_name']);
            unset($userDetails['_method']);
            unset($userDetails['name']);
            unset($userDetails['identity_card']);

            $user = Auth::updateUser($userKey,$data,$userDetails);

            Session::flash('message', trans('privateInPersonRegistration.updateOk'));
            return redirect()->action('InPersonRegistrationController@show', $user->user_key);

        }
        catch(Exception $e) {
            //TODO: save inputs
            return redirect()->back()->withErrors([ trans('privateInPersonRegistration.updateNok') => $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $userKey
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($userKey)
    {
        if(Session::get('user_role') != 'admin'){
            if(!ONE::verifyUserPermissionsDelete('auth', 'in_person_registration')){
                return redirect()->back()->withErrors(["inPersonResgistration.destroy" => trans('privateEntitiesDivided.permission_message')]);
            }
        }
        
        try {

            Auth::deleteUser($userKey);
            Orchestrator::deleteUser($userKey);

            Session::flash('message', trans('privateInPersonRegistration.deleteOk'));
            return action('LayoutsController@index');

        }
        catch(Exception $e) {
            return redirect()->back()->withErrors([ trans('privateInPersonRegistration.deleteOk') => $e->getMessage()]);
        }
    }


    /**
     * Show delete resource confirmation
     * Remove the specified resource from storage.
     * @param $userKey
     * @return View
     */
    public function delete($userKey)
    {
        $data = array();

        $data['action'] = action("InPersonRegistrationController@destroy", $userKey);
        $data['title'] = "DELETE";
        $data['msg'] = "Are you sure you want to delete this User?";
        $data['btn_ok'] = "Delete";
        $data['btn_ko'] = "Cancel";

        return view("_layouts.deleteModal", $data);
    }


    /**
     * Display a listing of the resource.
     * @return mixed
     * @throws Exception
     */
    public function getIndexTable()
    {
        if (Session::get('user_role') == 'admin' || ONE::verifyUserPermissionsShow('auth', 'in_person_registration')){
            $usersResponse = Orchestrator::getAllUsers();

            $usersKey = [];
            foreach ($usersResponse as $user) {
                $usersKey[] = $user->user_key;
            }
            $usersDetails = Auth::listUser($usersKey);

            $collection = Collection::make($usersDetails);


        }else{
            $collection = Collection::make([]);
        }

        $edit = Session::get('user_role') == 'admin' || ONE::verifyUserPermissionsUpdate('auth', 'in_person_registration');
        $delete = ONE::verifyUserPermissions('auth', 'in_person_registration', 'delete');

        // in case of json
        return Datatables::of($collection)
            ->editColumn('name', function ($user) {
                return "<a href='".action('InPersonRegistrationController@show', $user->user_key)."'>".$user->name."</a>";
            })
            ->addColumn('action', function ($user) use($edit, $delete){
                if($edit == true and $delete == true)
                    return ONE::actionButtons( $user->user_key, ['edit' => 'InPersonRegistrationController@edit', 'delete' => 'InPersonRegistrationController@delete']);
                elseif($edit == false and $delete == true)
                    return ONE::actionButtons( $user->user_key, ['delete' => 'InPersonRegistrationController@delete']);
                elseif($edit == true and $delete == false)
                    return ONE::actionButtons( $user->user_key, ['edit' => 'InPersonRegistrationController@edit']);
                else
                    return null;
            })
            ->make(true);
    }


    /**
     * Display a resource.
     * @param $userKey
     * @return \Illuminate\Http\RedirectResponse|View
     */
    public function voteInPerson(Request $request,$userKey)
    {
        try {
            $votesKeys = "";
            $notSubmitted= true;
            // Check if type is set
            if (!isset($this->cbType[$request->type])) {
                throw new Exception(trans('privateInPersonRegistration.errorsNOcbType'));
            }

            // Variables Initialization
            $type = $this->cbType[$request->type];
            $cbsData = [];

            // Get CB Keys from Orchestrator
            $cbs = Orchestrator::getCbTypes($type);


            // Data not found / No keys
            if (count($cbs) == 0) {
                return view('private.inPersonRegistration.inPersonVote', compact('cbsData', 'usersNames', 'type'));
            }

            // Get data to list CBs
            $cbsData = CB::getListCBs($cbs);
            $cbAndTopics = [];
            $voteType = [];
            foreach ($cbsData as $cbTemp){
                if($cbTemp->start_date <= Carbon::now() && ($cbTemp->end_date >= Carbon::now() || !$cbTemp->end_date)){
                    $cbAndTopics []= CB::getCBAndTopics($cbTemp->cb_key);

                    // check if exist votes
                    $existVotesForSubmit = false;
                    $existVotes = 0;
                    $voteKey = '';
                    $allReadyVoted = [];
                    $remainingVotes = 0;
                    $cbVotes = CB::getCbVotes($cbTemp->cb_key);

                    $voteKeys = [];
                    foreach ($cbVotes as $vote) {
                        $voteKeys[] = $vote->vote_key;
                    }


                    $eventsResponse = Vote::getAllShowEventsNoTranslation($voteKeys);

                    //index of array = key of event
                    $eventVotes = [];
                    foreach ($eventsResponse as $ev) {
                        $eventVotes[$ev->key] = $ev;
                    }

                    $existVotes = 0;
                    foreach ($cbVotes as $vote) {
                        $vConfigurations = [];
                        $voteType = [];
                        $voteKey = $vote->vote_key;
                        $eventVote = $eventVotes[$voteKey];

                        //generic configurations of vote
                        $genericConfigurations = [];
                        foreach ($vote->vote_configurations as $vtConf) {
                            $genericConfigurations[$vtConf->code] = $vtConf->value;
                        }
                        foreach ($eventVote->configurations as $vtConfig) {
                            $vConfigurations[$vtConfig->code] = $vtConfig->value;
                        }

                        //vote status
                        $voteStatus = Vote::getVoteStatus($voteKey,$userKey);
                        if ($voteStatus->vote) {
                            $existVotes = 1;
                            if(count($voteStatus->votes) > 0 || (count($voteStatus->votes) == 0 && $voteStatus->remaining_votes->total > 0)) {
                                $existVotesForSubmit = true;
                            }
                        } else {
                            $existVotes = 0;
                        }

                        $generalSubmit= isset($voteStatus->can_vote) ? $voteStatus->can_vote : false;
                        if(!$generalSubmit){
                            $notSubmitted = $generalSubmit;
                        }
                        $remainingVotes = $voteStatus->remaining_votes;

                        $allReadyVoted = [];
                        foreach ($voteStatus->votes as $vtStatus) {
                            $allReadyVoted[$vtStatus->vote_key] = $vtStatus->value;
                        }

                        $methodName = '';
                        switch ($vote->vote_method) {
                            case 'likes':
                                $methodName = 'VOTE_METHOD_LIKE';
                                break;
                            case 'multi_voting':
                                $methodName = 'VOTE_METHOD_MULTI';
                                break;
                            case 'negative_voting':
                                $methodName = 'VOTE_METHOD_NEGATIVE';
                                break;
                            case 'rank':
                                $methodName = 'VOTE_METHOD_RANK';
                                break;
                        }
                        $voteType[]= [
                            "method" => $methodName,
                            "key" => $voteKey,
                            "remainingVotes" => $remainingVotes,
                            "existVotes" => $existVotes,
                            "allReadyVoted" => $allReadyVoted,
                            "eventVote" => $eventVote,
                            "totalVotes" => isset($voteStatus->total_votes) ? json_decode(json_encode($voteStatus->total_votes), true) : null,
                            "configurations" => $vConfigurations,
                            "genericConfigurations" => $genericConfigurations];
                    }
                    $votesByCb[$cbTemp->cb_key] = $voteType;
                }
            }
            return view("private.inPersonRegistration.inPersonVote", compact('cbAndTopics','votesByCb','userKey','topics', 'generalSubmit','notSubmitted','existVotesForSubmit'));

        }catch (Exception $e){
            return redirect()->back()->withErrors([ trans('privateInPersonRegistration.voteInPerson') => $e->getMessage()]);
        }
    }
    /**
     * Display a resource.
     * @param $userKey
     * @return string
     */
    public function voteSubmit(Request $request,$userKey)
    {
        $data = [];

        foreach ($request->voteCbs as $voteEvents){
            foreach ($voteEvents as $event){
                $data[] = $event['key'];
            }
        }
        $response = Vote::submitVoting($data,$userKey);
        if($response) {
            return 'submit_success';
        }
        return 'submit_error';
    }



}
