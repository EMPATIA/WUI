<?php

namespace App\Http\Controllers;

use App\ComModules\CB;
use App\ComModules\Orchestrator;
use Illuminate\Http\Request;
use App\Http\Requests\KioskRequest;
use App\One\One;
use Datatables;
use Session;
use View;
use Breadcrumbs;
use Exception;
use Illuminate\Support\Collection;
use App\RequestKiosks;
use PDF;
use App\ComModules;
use App\ComModules\Vote;

class KiosksController extends Controller
{
    private $attributes = [];
    private $actions = [];
    private $position = [];
    private $tablePosition = [];


    public function __construct()
    {
        $this->requestKiosk = new RequestKiosks();
        $this->formSettings();

        // Table Position settings
        $this->position[0][1] = 1;
        $this->position[1][1] = 2;
        $this->position[2][1] = 3;
        $this->position[3][1] = 4;
        $this->position[3][0] = 5;
        $this->position[2][0] = 6;
        $this->position[1][0] = 7;
        $this->position[0][0] = 8;

        $this->tablePosition[1] = [0,1];
        $this->tablePosition[2] = [1,1];
        $this->tablePosition[3] = [2,1];
        $this->tablePosition[4] = [3,1];
        $this->tablePosition[5] = [3,0];
        $this->tablePosition[6] = [2,0];
        $this->tablePosition[7] = [1,0];
        $this->tablePosition[8] = [0,0];

        View::share('title', trans('kiosk.title'));

    }

    private function formSettings(){
        // cb_key
        $this->attributes["cb_key"] = ['class' => 'form-control', 'id' => 'cb_key','required' => 'required'];

        // Title
        $this->attributes["title"] = ['class' => 'form-control', 'id' => 'title','required' => 'required'];

        // kiosk_key
        $this->attributes["kiosk_key"] = ['class' => 'form-control', 'id' => 'kiosk_key'];

        // if(Session::get('user_role', 'manager')  == 'manager'){
        $this->attributes["kiosk_key"] = array_merge($this->attributes["kiosk_key"], array('readonly' => 'readonly'));
        // }

        // Form actions
        if(Session::get('user_role', 'manager')  == 'admin'){
            $this->actions["edit"] = 'KiosksController@edit';
            $this->actions["delete"] = 'KiosksController@delete';
            $this->actions["show"] = 'KiosksController@show';
            $this->actions["update"] = 'KiosksController@update';
            $this->actions["store"] = 'KiosksController@store';
            $this->actions["index"] = 'KiosksController@index';
        } else {
            $this->actions["edit"] = 'KiosksController@edit';
            $this->actions["delete"] = null;
            $this->actions["show"] = 'KiosksController@show';
            $this->actions["update"] = 'KiosksController@update';
            $this->actions["store"] = 'KiosksController@store';
            $this->actions["index"] = 'KiosksController@index';
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $title = trans('privateKiosks.kiosks');
        return view('private.kiosk.index', compact('title', 'permissions'));
    }


    /**
     * Create a new resource.
     *
     * @return Response
     */
    public function create()
    {
        try {
            //GET CBS INFORMATION
            $cbTypes = Orchestrator::getCbTypesList();
            $cbsData = [];

            $cbTypeCodes = [];
            foreach($cbTypes as $cbType){
                // Type
                $cbTypeCodes[$cbType->code] = $cbType->code;

                $cbsData[$cbType->code] = [];
                $list =  Orchestrator::getCbTypes($cbType->code);
                $listCb = CB::getListCBs($list);
                foreach ($listCb as $item) {
                    $cbsData[$cbType->code][$item->cb_key] = $item->title;
                }
            }
            // Get Kiosk Type List
            $kioskTypesList = Orchestrator::getKioskTypes();

            // Setting data for a Page Select
            $kioskTypes = [];
            foreach($kioskTypesList as $item){
                $kioskTypes[$item->id] = $item->title;
            }

            // View
            $title = trans('privateKiosks.createKiosk');
            return view('private.kiosk.kiosk', compact('cbTypeCodes','cbsData', 'title','entities','kioskTypes'));
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["kiosk.index" => $e->getMessage()]);
        }

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param $key
     * @return View
     */
    public function edit($key)
    {
        try {
            //GET CBS INFORMATION
            $cbTypes = Orchestrator::getCbTypesList();
            $cbsData = [];

            $cbTypeCodes = [];
            foreach($cbTypes as $cbType){
                // Type
                $cbTypeCodes[$cbType->code] = $cbType->code;

                $cbsData[$cbType->code] = [];
                $list =  Orchestrator::getCbTypes($cbType->code);
                $listCb = CB::getListCBs($list);
                foreach ($listCb as $item) {
                    $cbsData[$cbType->code][$item->cb_key] = $item->title;
                }
            }

            // Get Kiosk Type List
            $kioskTypesList = Orchestrator::getKioskTypes();

            // Setting data for a Page Select
            $kioskTypes = [];
            foreach($kioskTypesList as $item){
                $kioskTypes[$item->id] = $item->title;
            }

            // Kiosk
            $kiosk = Orchestrator::getKiosk($key);
            $cb = CB::getCbParametersOptions($kiosk->entity_cb->cb_key);


            // Get kiosk proposals
            $proposalsList = Orchestrator::getProposals($kiosk->kiosk_key);

            $proposalsIds = [];
            foreach($proposalsList as $proposal){
                $proposalsIds[]  = $proposal->proposal_key;
            }

            $proposals = CB::setTopicWithFirstPost($proposalsIds);

            // Prepare data to be used in views
            $proposalsData = [];
            foreach($proposals as $item){
                $proposalsData[$item->id] = $item->title;
            }

            // Votes
            $votes = [];
            if(!empty($kiosk->entity_cb->cb_key)) {
                $voteEventsList = CB::getListCbVotes($kiosk->entity_cb->cb_key);
                foreach($voteEventsList as $vote){
                    $votes[$vote->vote_key] = $vote->name;
                }
            }
            // Return view with data
            return view('private.kiosk.kiosk', compact('cbTypeCodes','cbsData','title', 'kiosk','cb','votes','attributes','kioskTypes','actions','proposalsList','proposalsData'));
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["kiosk.show" => $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $key
     * @return \Illuminate\Http\RedirectResponse|View
     */
    public function show($key)
    {
        try {
            //GET CBS INFORMATION
            $cbTypes = Orchestrator::getCbTypesList();
            $cbsData = [];

            $cbTypeCodes = [];
            foreach($cbTypes as $cbType){
                // Type
                $cbTypeCodes[$cbType->code] = $cbType->code;

                $cbsData[$cbType->code] = [];
                $list =  Orchestrator::getCbTypes($cbType->code);
                $listCb = [];

                foreach ($list as $item) {
                    $listCb[] = $item->cb_key;
                }
                if(!empty($listCb)){

                    $list = CB::getListCBs($listCb);
                    foreach ($list as $item) {
                        $cbsData[$cbType->code][$item->cb_key] = $item->title;
                    }
                }
            }

            // Kiosk
            $kiosk = Orchestrator::getKiosk($key);
            $cb = CB::getCbParametersOptions($kiosk->entity_cb->cb_key);

            // Votes
            $vote = "";
            if(!empty($kiosk->entity_cb->cb_key)){

                $voteEvents = Vote::getAllShowEvents($kiosk->event_key);
                $vote = '';
                if(count($voteEvents) > 0) {
                    $cbVote = CB::getCbVote($kiosk->entity_cb->cb_key, $kiosk->event_key);
                    foreach ($voteEvents as $vt) {
                        if ($kiosk->event_key == $vt->key) {
                            $vote = $cbVote->name . "&nbsp;&nbsp;[" . $vt->method->name . "]";
                        }
                    }
                }
            }
            // Get Menu Type
            $kioskTypeName = null;
            if($kiosk->kiosk_type_id!=0){
                $response = Orchestrator::getKioskType($kiosk->kiosk_type_id);
                $kioskTypeName = $response->title;
            }

            //TODO: this is used for no screen kiosk ?!
            // Get kiosk proposals

            $proposalsList = Orchestrator::getProposals($kiosk->kiosk_key);

            $proposalsTmp = [];
            $proposalsIds = [];
            foreach($proposalsList as $proposal){
                $proposalsIds[] = $proposal->proposal_key;

                $proposalsTmp[$proposal->position] = $proposal;
            }

            $proposals = CB::setTopicWithFirstPost($proposalsIds);

            // Prepare data to be used in views
            $proposalsData = [];
            foreach($proposals as $item){
                $proposalsData[$item->id] = $item->title;
            }

            // Rebuild an array with all proposals that aren't in entity
            $topicList = [];
            if(!empty($kiosk->entity_cb)){
                $topics = CB::getTopicWithFirstPost($kiosk->entity_cb->cb_key)->data;
                foreach($topics as $topic){
                    if(!in_array($topic->id, $proposalsIds)){
                        $topicList[] = $topic;
                    }
                }
            }
            //TODO: END

            // View
            return view('private.kiosk.kiosk', compact('cbTypeCodes','cbsData','kiosk','cb','topicList','vote','attributes','actions','kioskTypeName','proposalsList','proposalsData','proposalsTmp'));
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["kiosk.show" => $e->getMessage()]);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|View
     */
    public function store(KioskRequest $request)
    {
        try {
            // Prepare data to send
            $data = [
                "title" => $request["title"],
                "event_key" => $request["event_key"],
                "kiosk_key" => $request["kiosk_key"],
                "cb_key" => $request["cb_key"],
                "kiosk_type_id" => $request["kiosk_type_id"],
            ];
            $kiosk = Orchestrator::setKiosk($data);

            Session::flash('message', trans('kiosk.store_ok'));
            return redirect()->action('KiosksController@show', $kiosk->kiosk_key);
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["kiosk.store" => $e->getMessage()]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param KioskRequest $request
     * @param $key
     * @return KiosksController|\Illuminate\Http\RedirectResponse
     */
    public function update(KioskRequest $request, $key)
    {
        try {
            // Prepare data to send
            $data = [
                "title" => $request["title"],
                "event_key" => $request["event_key"],
                "kiosk_key" => $request["kiosk_key"],
                "cb_key" => $request["cb_key"],
                "kiosk_type_id" => $request["kiosk_type_id"]
            ];

            // Update Kiosk
            $kiosk = Orchestrator::updateKiosk($data,$key);
            Session::flash('message', trans('kiosk.updateOk'));
            return redirect()->action('KiosksController@show', $kiosk->kiosk_key);

        }catch(Exception $e) {
            return redirect()->back()->withErrors(["kiosk.update" => $e->getMessage()]);
        }
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  $key
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($key)
    {
        try {
            Orchestrator::deleteKiosk($key);
            Session::flash('message', trans('kiosk.delete_ok'));
            return action('KiosksController@index');
        }
        catch(Exception $e) {
            //TODO: save inputs
            return redirect()->back()->withErrors(["eventSchedule.destroy" => $e->getMessage()]);
        }
    }


    public function delete($key){
        $data = array();

        $data['action'] = action("KiosksController@destroy", $key);
        $data['title'] = "DELETE";
        $data['msg'] = "Are you sure you want to delete this Kiosk?";
        $data['btn_ok'] = "Delete";
        $data['btn_ko'] = "Cancel";

        return view("_layouts.deleteModal", $data);
    }

    public function getVoteOptions(Request $request){
        // Votes
        $votes = [];
        if(!empty($request->cb_key)){
            $voteList = Vote::getVoteMethods($request->cb_key);
            foreach($voteList as $vote){
                $votes[$vote["voteKey"]] = $vote["name"]."&nbsp;&nbsp;[".$vote["methodName"]."]";
            }
        }
        return $votes;
    }

    public function getIndexTable(){
        // Request for Data List
        $kioskList = Orchestrator::getKioskList();

        // JSON data collection
        $collection = Collection::make($kioskList);

        // Render Datatable
        return Datatables::of($collection)
            ->editColumn('title', function ($kiosk) {
                return "<a href='".action('KiosksController@show', $kiosk->kiosk_key)."'>".$kiosk->title."</a>";
            })
            ->addColumn('action', function ($eventSchedule) {
                return ONE::actionButtons($eventSchedule->kiosk_key, ['show' => 'KiosksController@show', 'delete' => 'KiosksController@delete'] );
            })
            ->rawColumns(['title','action'])
            ->make(true);
    }


    public function addProposal($kioskKey){
        return view('private.kiosk.addProposal',  compact("kioskKey"));
    }


    public function tableAddProposal($kioskKey){
        try {
            // Kiosk
            $response = $this->requestKiosk->getKiosk($kioskKey);
            if( $response->statusCode() == 200 ){
                $kiosk = $response->json();
            }else if($response->statusCode() == 404) {
                throw new Exception("Kiosk not found!");
            } else {
                throw new Exception("An error occurried while getting kiosk.");
            }

            // Get kiosk proposals
            $proposalsList = Orchestrator::getProposals($kioskKey);
            $proposalsKeys = [];
            foreach($proposalsList as $proposal){
                $proposalsKeys[]  = $proposal->proposal_key;
            }

            $response = Orchestrator::getIdea($kiosk->entity_cb_id);

            $cbKey = $response->cb_key;

            $topics = CB::getTopicWithFirstPost($cbKey);

            // Rebuild an array with all languages that aren't in entity
            $topicList = [];
            foreach($topics as $topic){
                if(!in_array($topic->id, $proposalsKeys)){
                    $topicList[] = $topic;
                }
            }

            // in case of json
            $proposal = Collection::make($topicList);

            return Datatables::of($proposal)
                ->editColumn('title', function ($proposal)  {
                    return $proposal->title;
                })
                ->addColumn('action', function ($proposal) use ($kioskKey) {
                    return ONE::actionButtons([$kioskKey,$proposal->id], ['add' => 'KiosksController@addProposalAction']);
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["kiosk.show" => $e->getMessage()]);
        }
    }

    public function addProposalAction($kioskKey,$proposalKey){

        try {
            $response = Orchestrator::setProposal($proposalKey, $kioskKey, 1);
            return redirect()->action('KiosksController@show', $kioskKey);
        }
        catch(Exception $e) {
            //TODO: save inputs
            return redirect()->back()->withErrors(["entity.store" => $e->getMessage()]);
        }
    }

    public function deleteProposal($kioskKey,$id){
        $data = array();

        $data['action'] = action("KiosksController@destroyProposal", [$kioskKey,$id]);
        $data['title'] = "DELETE";
        $data['msg'] = "Are you sure you want to delete this Proposal from this Kiosk?";
        $data['btn_ok'] = "Delete";
        $data['btn_ko'] = "Cancel";

        return view("_layouts.deleteModal", $data);
    }

    public function destroyProposal($kioskKey,$id){
        try {

            Orchestrator::deleteProposal($kioskKey, $id);
            Session::flash('message', trans('kiosk.deleteProposalOk'));

            return action('KiosksController@show', $kioskKey);

        }
        catch(Exception $e) {
            //TODO: save inputs
            return redirect()->back()->withErrors(["eventSchedule.destroy" => $e->getMessage()]);
        }

    }


    /**
     * Update the Proposals Order in storage.
     *
     * @param PostRequest $request
     * @return $this|View
     */
    public function updateProposalOrder(Request $request,$kioskKey)
    {

        $source = $request->source;  // id do menu que estamos a arrastar
        $ordering = json_decode($request->order);  //ordem nova caso tenha ido para o root (sem pai)

        Orchestrator::updateProposalOrder($kioskKey, $ordering);
        Session::flash('message', trans('menu.updateOrder_ok'));
        return redirect()->action('MenusController@index');
    }


    public function download($key)
    {
        try {
            // Input attributes
            $attributes = $this->attributes;
            $actions = $this->actions;

            // Kiosk
            $response = $this->requestKiosk->getKiosk($key);
            if( $response->statusCode() == 200 ){
                $kiosk = $response->json();
            }else if($response->statusCode() == 404) {
                throw new Exception("Kiosk not found!");
            } else {
                throw new Exception("An error occurried while getting kiosk.");
            }

            // Idea
            $response = $this->requestKiosk->getIdea($kiosk->entity_cb_id);
            if( $response->statusCode() == 200 ){
                $idea = (array) $response->json();
            }else if($response->statusCode() == 404) {
                $idea= null;
            } else {
                throw new Exception("An error occurried while getting idea.");
            }

            // Votes
            /*
            $vote = "";
            if(!empty($kiosk->entity_cb->cb_key)){
                $voteList = Vote::getVoteMethods($kiosk->entity_cb->cb_key);
                foreach($voteList as $vt){
                    if($kiosk->event_key == $vt["voteKey"]){
                        $vote = $vt["name"]."&nbsp;&nbsp;[".$vt["methodName"]."]";
                    }
                } 
            }            
            */
            // Get Menu Type 
            /*
            if($kiosk->kiosk_type_id!=0){
                $response = ONE::get([
                    'component' => 'orchestrator',
                    'api'       => 'kiosktype',
                    'attribute' => $kiosk->kiosk_type_id,
                ]);
                
                // if an error ocurried while getting data
                if( $response->statusCode() == 200 ){
                    $kioskTypeName = $response->json()->title; 
                }else if($response->statusCode() == 404 ){
                    $kioskTypeName = null;
                } else {
                    throw new Exception("An error occurried while getting Type Name.");
                }       
            }
            */

            // Get kiosk proposals
            $proposalsList = Orchestrator::getProposals($kiosk->kiosk_key);

            $proposalsTmp = [];
            $proposalsIds = [];
            foreach($proposalsList as $proposal){
                $proposalsIds[] = $proposal->proposal_key;
                $proposalsTmp[$proposal->position] = $proposal;
            }

            // Get data details
            $proposals = CB::setTopicWithFirstPost($proposalsIds);

            // Prepare data to be used in views
            $usersKey = [];
            $proposalsData = [];
            foreach($proposals as $item){
                $proposalsData[$item->id]["title"] = $item->title;
                $proposalsData[$item->id]["contents"] = $item->contents;
                $proposalsData[$item->id]["created_by"] = $item->created_by;

                $usersKey[] = $item->created_by;


                $parameters = [];
                if( !empty($item->parameters) ){
                    $j = 0;
                    for($k = 0; $k < count($item->parameters); $k++){
                        $label = "";
                        if($item->parameters[$k]->code == "image_map"){
                            $label = One::verifyEmpavilleGeoArea($item->parameters[$k]->pivot->value );
                        }
                        if( !empty($item->parameters[$k]->options ) ){
                            foreach($item->parameters[$k]->options as $p)  {
                                if($item->parameters[$k]->pivot->value == $p->id ){
                                    $label = $p->label;
                                }
                            }
                        }

                        if($item->parameters[$k]->code == "image_map"){
                            $proposalsData[$item->id]["location"] =  $label;
                        } else {
                            $parameters[$j]["code"] = $item->parameters[$k]->code;
                            $parameters[$j]["description"] = $item->parameters[$k]->parameter;
                            $parameters[$j]["label"] = $label;
                            $j++;
                        }
                    }
                }

                $proposalsData[$item->id]["parameters"] = $parameters;
            }


            $responseAuth = ComModules\Auth::listUser($usersKey);
            $userNames = [];
            foreach ($responseAuth as $item){
                $userNames[$item->user_key] =  $item->name;
            }


            // return view('private.kiosk.download',compact('proposalsData','proposalsTmp','userNames'));
            $pdf = PDF::loadView('private.kiosk.download',compact('proposalsData','proposalsTmp'))
                ->setPaper('a4')->setOrientation('landscape')->setWarnings(false);
            return $pdf->download('kiosk.pdf');

        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["kiosk.show" => $e->getMessage()]);
        }

    }


    public function storeProposals(Request $request, $kioskKey){

        $proposals =  json_decode($request->proposals);

        $data = [];
        foreach($proposals as $proposal){
            $data[] = ['proposal_key' => $proposal[0], 'kiosk_key' => $kioskKey, 'position' => $this->position[ $proposal[1] ][ $proposal[2] ] ];
        }

        try {
            Orchestrator::storeProposals($kioskKey, $data);
            return "1";
        }
        catch(Exception $e) {
            //TODO: save inputs
            Session::flash('error', "An error occurried");
            return "0";
        }

    }


    /**
     * Get vote events.
     *
     */

    public function getVoteEvents(Request $request){

        $voteEventsList = CB::getListCbVotes($request->cb_key);
        $htmlOptions ='<option value="" selected="selected">'.trans('form.select_value').'</option>';
        foreach ($voteEventsList as $vote){
            $htmlOptions .='<option value="'.$vote->vote_key.'">'.$vote->name.'</option>';
        }
        return $htmlOptions;
    }

}