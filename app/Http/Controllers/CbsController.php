<?php

namespace App\Http\Controllers;

use App\ComModules\Analytics;
use App\Http\Requests\CbsRequest;
use App\ComModules\Vote;
use App\ComModules\Files;
use App\ComModules\Notify;
use App\ComModules\Auth;
use App\ComModules\CB;
use App\ComModules\Questionnaire;
use App\ComModules\Orchestrator;
use App\ComModules\EMPATIA;
use App\ComModules\CM;
use App\Http\Requests\CbTopicsExportRequest;
use App\Http\Requests\PostRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Requests\UserRequest;
use App\One\One;
use Datatables;
use Illuminate\Support\Facades\URL;
use Session;
use View;
use Breadcrumbs;
use Exception;
use Illuminate\Support\Collection;

class CbsController extends Controller
{
    private $cbType;

    /**
     * CbsController constructor.
     */
    public function __construct()
    {
        $this->cbType = [
            'forum' => 'forum',
            'discussion' => 'discussion',
            'proposal' => 'proposal',
            'idea' => 'idea',
            'tematicConsultation' => 'tematicConsultation',
            'publicConsultation' => 'publicConsultation',
            'survey' => 'survey',
            'project' => 'project',
            'phase1' => 'phase1',
            'phase2' => 'phase2',
            'phase3' => 'phase3',
            'qa' => 'qa',
            'project_2c' => 'project_2c',
            'event' => 'event'
        ];
    }

    /**
     * Display a listing of the resource.
     *
     * @param $type
     * @return View
     */
    public function index($type)
    {
        try {
            switch ($type) {
                case $type == "idea":
                    $title = trans('privateIdeas.list_ideas');
                    return view('private.cbs.index', compact('type', 'title'));
                    break;
                case $type == "forum":
                    $title = trans('privateForums.list_forums');
                    return view('private.cbs.index', compact('type', 'title'));
                    break;
                case $type == "discussion":
                    $title = trans('privateDiscussions.list_discussions');
                    return view('private.cbs.index', compact('type', 'title'));
                    break;
                case $type == "proposal":
                    $title = trans('privateProposals.list_proposals');
                    return view('private.cbs.index', compact('type', 'title'));
                    break;
                case $type == "project_2c":
                    $title = trans('privateProject2Cs.list_project_2cs');
                    return view('private.cbs.index', compact('type', 'title'));
                    break;
                case $type == "topic":
                    $title = trans('privatePropositionModeration.list_propositions');
                    return view('private.cbs.index', compact('type', 'title'));
                    break;
                case $type == "publicConsultation":
                    $title = trans('privatePublicConsultations.list_public_consultations');
                    return view('private.cbs.index', compact('type', 'title'));
                    break;
                case $type == "tematicConsultation":
                    $title = trans('privateTematicConsultations.list_tematic_consultations');
                    return view('private.cbs.index', compact('type', 'title'));
                    break;
                case $type == "survey":
                    $title = trans('privateSurveys.list_surveys');
                    return view('private.cbs.index', compact('type', 'title'));
                    break;
                case $type == "phase1":
                    $title = trans('privatePhaseOne.list_phase1');
                    return view('private.cbs.index', compact('type', 'title'));
                    break;
                case $type == "phase2":
                    $title = trans('privatePhaseTwo.list_phase2');
                    return view('private.cbs.index', compact('type', 'title'));
                    break;
                case $type == "phase3":
                    $title = trans('privatePhaseThree.list_phase3');
                    return view('private.cbs.index', compact('type', 'title'));
                    break;
                case $type == "qa":
                    $title = trans('privateQA.list_qa');
                    return view('private.cbs.index', compact('type', 'title'));
                case $type == "event":
                    $title = trans('privateEvent.list_event');
                    return view('private.cbs.index', compact('type', 'title'));
                    break;
            }
        }catch (Exception $e){

        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function indexManager(Request $request)
    {
        try{
            $typeFilter = $request->typeFilter;
            $title = trans('privateIdeas.list_ideas');
            $allCbTypes = Orchestrator::getCbTypesList();

            if($typeFilter=='idea')
                return view('private.cbs.index_manager', compact('typeFilter', 'allCbTypes', 'title'));
            else {
                $title = trans('privateCbs.lastest_pads');
                return view('private.cbs.index_manager', compact('typeFilter', 'allCbTypes'));
            }
        }catch (Exception $e){

        }
    }



    /**
     * Create a new resource.
     *
     * @param $type
     * @return \Illuminate\Http\RedirectResponse|View
     */
    public function create(Request $request, $type)
    {
        try {
            //verify if cb type exists (defined in constructor)
            $api = $this->getApiByType($type);
            $configurations = CB::getConfigurations();
            $parameters = CB::getParameters();
            $parameterType = CB::getParametersTypes();
            $languages = Orchestrator::getLanguageList();
            $genericConfigs = CB::getVotesConfigurations();
            $parentCbKey = $request->input('parentCbKey',null);

            $data = [];
            if(isset($request->template_cb_key)){

                $cbTemplate = CB::getCb($request->template_cb_key);
                $cbTemplateConfigurations = collect($cbTemplate->configurations)->pluck('id')->toArray();
                $cbTemplateParameters =  $cbTemplate->parameters;
                $moderatorsKeys = collect($cbTemplate->moderators)->pluck('user_key');
                $moderators = Auth::getListNames($moderatorsKeys);
                $data["cb"] = $cbTemplate;
                $data["cbConfigurations"] = $cbTemplateConfigurations;
                $data["cbTemplateParameters"] = $cbTemplateParameters;
                $data["moderators"] = $moderators;
            }

            $data["type"] = $type;
            $data["configurations"] = $configurations;
            $data["parameters"] = $parameters;
            $data["parameterType"] = $parameterType;
            $data["genericConfigs"] = $genericConfigs;
            $data["languages"] = $languages;
            $data["parentCbKey"] = $parentCbKey;

            switch ($type) {
                case $type == "idea":
                    $data["title"] = trans('privateIdeas.create_ideas');
                    break;
                case $type == "forum":
                    $data["title"] = trans('privateForums.create_forums');
                    break;
                case $type == "discussion":
                    $data["title"] = trans('privateDiscussions.create_discussions');
                    break;
                case $type == "proposal":
                    $data["title"] = trans('privateProposals.create_proposals');
                    break;
                case $type == "project_2c":
                    $data["title"] = trans('privateProject2Cs.create_proposals');
                    break;
                case $type == "publicConsultation":
                    $data["title"] = trans('privatePublicConsultations.create_public_consultations');
                    break;
                case $type == "tematicConsultation":
                    $data["title"] = trans('privateTematicConsultations.create_tematic_consultations');
                    break;
                case $type == "survey":
                    $data["title"] = trans('privateSurveys.create_surveys');
                    break;
                case $type == "phase1":
                    $data["title"] = trans('privatePhaseOne.create_phase1');
                    break;
                case $type == "phase2":
                    $data["title"] = trans('privatePhaseTwo.create_phase2');
                    break;
                case $type == "phase3":
                    $data["title"] = trans('privatePhaseThree.create_phase3');
                    break;
                case $type == "qa":
                    $data["title"] = trans('privateQA.create_qa');
                    break;
                case $type == "event":
                    $data["title"] = trans('privateQA.create_event');
                    break;
            }

            $siteKey = Session::get('X-SITE-KEY');
            $contentList = CM::getNewContents('pages',$siteKey);

            $contentListType = [];

            foreach($contentList as $content){
                $contentListType[$content->content_key] = $content->name;
            }
            $data['contentListType'] = $contentListType;

            if($request->has('step')){
                $cb = CB::getCb($request->cbKey);
                $cbConfigurations = collect($cb->configurations)->pluck('id')->toArray();
                $cbParameters =  $cb->parameters;
                $cbVotes = CB::getListCbVotes($request->cbKey);
                $moderatorsKeys = collect($cb->moderators)->pluck('user_key');
                $moderators = Auth::getListNames($moderatorsKeys);
                $data['step'] = $request->step;
                $data['cbKey'] = $request->cbKey;
                $data['cb'] = $cb;
                $data["cbConfigurations"] = $cbConfigurations;
                $data["cbParameters"] = $cbParameters;
                $data['cbVotes'] = $cbVotes;
                $data["moderators"] = $moderators;
            }


            return view('private.cbs.create', $data);

        } catch (Exception $e) {
            return redirect()->back()->withErrors(["cbs.stepperCb" => $e->getMessage()]);
        }
    }

    public function categoryFilter(Request $request)
    {
        //Get Status Types List

        $statusTypes = CB::getStatusTypes();

        //Get Status Name for Notification
        foreach ($statusTypes as $statusType) {
            $status[] = $statusType;
        }

        return view('private.cbs.categoryFilter', compact('statusTypes') );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param $type
     * @param Request $requestCB
     * @return $this|array
     */
    public function store($type, Request $requestCB, $configurationsToSave = null)
    {
        try {
            $parentCbKey = $requestCB->input('parentCbKey',null);
            if (!is_null($parentCbKey)){
                $parent = CB::getCb($parentCbKey);
                $cb = CB::createCbChild(array(
                    'title' => $requestCB["title"] ." (".$requestCB["tag"].")",
                    'contents' => $requestCB["description"],
                    'start_date' => $requestCB["start_date"],
                    'end_date' => $requestCB["end_date"],
                    "parent_cb_id" => $parent->id,
                    "tag" => $requestCB["tag"],
                    "template" => $requestCB["template"],
                    "page_key" => $requestCB["page_key"]
                ));
            }else{
                $cb = CB::setNewCb($requestCB);
            }
            $configurations = CB::getConfigurations();
            $cbConfigurations = [];
            foreach ($configurations as $configuration) {
                foreach ($configuration->configurations as $options) {
                    $cbConfigurations[] = $options->id;

                }
            }
            if (empty($configurationsToSave)){
                $configurationsToSave = [];
                foreach ($requestCB->all() as $key => $value) {
                    if (strpos($key, 'configuration_') !== false) {
                        $id = str_replace("configuration_", "", $key);
                        $configurationsToSave[] = $id;
                        unset($cbConfigurations[array_search($id, $cbConfigurations)]);
                    }
                }
                CB::setCbConfigurations($cb->cb_key, $configurationsToSave, null, null, 0);
            }
            else{
                CB::setCbConfigurations($cb->cb_key, $configurationsToSave, null, null, 0);
            }


            if(is_null($parentCbKey)){
                $api = $this->getApiByType($type);
                Orchestrator::setNewCb($api,$cb->cb_key);
            }
            Session::flash('message', trans('Cbs.store_ok'));

            $cbKey = $cb->cb_key;
            if (Session::get("firstInstallWizardStarted",false)) {
                Session::put("firstInstallWizardCBName",$cb->title);
                if(Session::get('participatory', true)){
                    return redirect()->action("CbsController@createWizard");
                }
                return redirect()->action("QuickAccessController@firstInstallWizard");
            } else if($requestCB->input('flagNewCb')){
                return redirect()->action('CbsController@show', ['type' => $type,'cbKey' => $cbKey]);
            } else{
                return ['cbKey' => $cbKey,'parent_cb_id' => $cb->parent_cb_id];
            }
        }
        catch(Exception $e) {
            //TODO: save inputs
            return redirect()->back()->withErrors(["cb.store" => $e->getMessage()]);
        }
    }

    /**
     * @param Request $requestCB
     * @param $type
     * @param $cbKey
     */
    public function storeCbTemplate(Request $requestCB, $type, $cbKey)
    {
        try{
            $configurations = CB::getConfigurations();
            $moderators = CB::getCbModerators($cbKey);
            $api = $this->getApiByType($type);

            $votes = CB::getCbVotes($cbKey);
            $cb = CB::getCb($cbKey);
            $cbVoteEvents = CB::getCbVoteEvents($cbKey);
            $v = CB::getCbVoteEvents($cbKey);

            $genericConfigs = CB::getVotesConfigurations();
            $listCbVotes = CB::getListCbVotes($cbKey);

            $parameters = $cb->parameters;

            $cbConfigurations = [];
            foreach ($configurations as $config) {
                $cbConfigurations[] = (string) $config->id;
            }

            $data['title'] = $requestCB->templateName;
            $data['contents'] = $cb->contents;
            $data['start_date'] = $cb->start_date;
            $data['end_date'] = $cb->end_date;

            $cbTemplate = CB::setNewCb($data);

            CB::setCbParameters($cbTemplate->cb_key, $parameters);

            CB::setCbConfigurations($cbTemplate->cb_key, $cbConfigurations);

            Orchestrator::setNewCbTemplate($requestCB->templateName,$api,$cb->cb_key);
        }catch (Exception $e){

        }
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param $type
     * @param $cbKey
     * @return $this|View
     */
    public function advancedEdit($type,$cbKey)
    {
        try {
            $configurations = CB::getConfigurations();
            $cb = CB::getCbConfigurations($cbKey);
            $cbConfigurations = [];
            foreach ($cb->configurations as $config) {
                $cbConfigurations[] = $config->id;
            }

            switch ($type) {
                case $type == "idea":
                    $title = trans('privateIdeas.update_ideas');
                    return view('private.cbs.create', compact('cb', 'configurations', 'cbConfigurations', 'type', 'title'));
                    break;
                case $type == "forum":
                    $title = trans('privateForums.update_forums');
                    return view('private.cbs.create', compact('cb', 'configurations', 'cbConfigurations', 'type', 'title'));
                    break;
                case $type == "discussion":
                    $title = trans('privateDiscussions.update_discussions');
                    return view('private.cbs.create', compact('cb', 'configurations', 'cbConfigurations', 'type', 'title'));
                    break;
                case $type == "proposal":
                    $title = trans('privateProposals.update_proposals');
                    return view('private.cbs.create', compact('cb', 'configurations', 'cbConfigurations', 'type', 'title'));
                    break;
                case $type == "project_2c":
                    $title = trans('privateProject2Cs.update_project_2cs').' '.(isset($cb->title) ? $cb->title : null);
                    return view('private.cbs.create', compact('cb', 'configurations', 'cbConfigurations', 'type', 'title'));
                    break;
                case $type == "publicConsultation":
                    $title = trans('privatePublicConsultations.update_public_consultations');
                    return view('private.cbs.create', compact('cb', 'configurations', 'cbConfigurations', 'type', 'title'));
                    break;
                case $type == "tematicConsultation":
                    $title = trans('privateTematicConsultations.update_tematic_onsultations');
                    return view('private.cbs.create', compact('cb', 'configurations', 'cbConfigurations', 'type', 'title'));
                    break;
                case $type == "survey":
                    $title = trans('privateSurveys.update_surveys');
                    return view('private.cbs.create', compact('cb', 'configurations', 'cbConfigurations', 'type', 'title'));
                    break;
                case $type == "phase1":
                    $title = trans('privatePhaseOne.update_phase1');
                    return view('private.cbs.create', compact('cb', 'configurations', 'cbConfigurations', 'type', 'title'));
                    break;
                case $type == "phase2":
                    $title = trans('privatePhaseTwo.update_phase2');
                    return view('private.cbs.create', compact('cb', 'configurations', 'cbConfigurations', 'type', 'title'));
                    break;
                case $type == "phase3":
                    $title = trans('privatePhaseThree.update_phase3');
                    return view('private.cbs.create', compact('cb', 'configurations', 'cbConfigurations', 'type', 'title'));
                    break;
                case $type == "qa":
                    $title = trans('privateQA.update_qa');
                    return view('private.cbs.create', compact('cb', 'configurations', 'cbConfigurations', 'type', 'title'));
                    break;
            }
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["discussion.edit" => $e->getMessage()]);
        }
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param $type
     * @param $cbKey
     * @return $this|View
     */
    public function edit($type,$cbKey)
    {
        try {
            $configurations = CB::getConfigurations();
            $cb = CB::getCbConfigurations($cbKey);
            $cbConfigurations = [];
            foreach ($cb->configurations as $config) {
                $cbConfigurations[] = $config->id;
            }
            $statusAvailable = CB::getStatusTypes();
            $statusTypes = [];
            foreach ($statusAvailable as $status) {
                $statusTypes[$status->code] = $status->name;
            }

            $cbFilters = CB::getCbFilters($cbKey);

            if (!empty($cbFilters))
            {
                    $cbFilter = [];
                foreach ($cbFilters as $filter) {
                    foreach ($statusAvailable as $status) {
                        if ($filter == $status->code) {

                            $filterCode = $status->code;
                            $filter = $status->name;
                            $cbFilter[$filterCode] = $filter;
                        }
                    }
                }
            }
            $subpad = ($cb->parent_cb_id != 0);
            $rootCbKey = ($subpad)?\App\Unimi\NestedCbs::getRootCbKey($cb->id):$cb->cb_key;

            $siteKey = Session::get('X-SITE-KEY');
            $contentList = CM::getNewContents('pages',$siteKey);

            $contentListType = [];

            foreach($contentList as $content){
                $contentListType[$content->content_key] = $content->name;
            }

            switch ($type) {
                case $type == "idea":
                    $title = trans('privateIdeas.update_ideas');
                    return view('private.cbs.cb', compact('cb', 'configurations', 'cbConfigurations', 'type', 'title','subpad','rootCbKey', 'contentListType'));
                    break;
                case $type == "events":
                    $title = trans('privateEvents.update_events');
                    return view('private.cbs.cb', compact('cb', 'configurations', 'cbConfigurations', 'type', 'title','subpad','rootCbKey', 'contentListType'));
                    break;
                case $type == "forum":
                    $title = trans('privateForums.update_forums');
                    return view('private.cbs.cb', compact('cb', 'configurations', 'cbConfigurations', 'type', 'title','subpad','rootCbKey', 'contentListType'));
                    break;
                case $type == "discussion":
                    $title = trans('privateDiscussions.update_discussions');
                    return view('private.cbs.cb', compact('cb', 'configurations', 'cbConfigurations', 'type', 'title','subpad','rootCbKey', 'contentListType'));
                    break;
                case $type == "proposal":
                    $title = trans('privateProposals.update_proposals');
                    return view('private.cbs.cb', compact('cb', 'configurations', 'cbConfigurations', 'type','statusTypes','cbFilter', 'title','subpad','rootCbKey', 'contentListType'));
                    break;
                case $type == "project_2c":
                    $title = trans('privateProject2Cs.update_project_2cs').' '.(isset($cb->title) ? $cb->title : null);
                    return view('private.cbs.cb', compact('cb', 'configurations', 'cbConfigurations', 'type', 'title','subpad','rootCbKey', 'contentListType'));
                    break;
                case $type == "publicConsultation":
                    $title = trans('privatePublicConsultations.update_public_consultations');
                    return view('private.cbs.cb', compact('cb', 'configurations', 'cbConfigurations', 'type', 'title','subpad','rootCbKey', 'contentListType'));
                    break;
                case $type == "tematicConsultation":
                    $title = trans('privateTematicConsultations.update_tematic_consultations');
                    return view('private.cbs.cb', compact('cb', 'configurations', 'cbConfigurations', 'type', 'title','subpad','rootCbKey', 'contentListType'));
                    break;
                case $type == "survey":
                    $title = trans('privateSurveys.update_surveys');
                    return view('private.cbs.cb', compact('cb', 'configurations', 'cbConfigurations', 'type', 'title','subpad','rootCbKey', 'contentListType'));
                    break;
                case $type == "project":
                    $title = trans('privateProjects.update_project');
                    return view('private.cbs.cb', compact('cb', 'configurations', 'cbConfigurations', 'type', 'title','subpad','rootCbKey', 'contentListType'));
                    break;
                case $type == "phase1":
                    $title = trans('privatePhaseOne.update_phase1');
                    return view('private.cbs.cb', compact('cb', 'configurations', 'cbConfigurations', 'type', 'title','subpad','rootCbKey', 'contentListType'));
                    break;
                case $type == "phase2":
                    $title = trans('privatePhaseTwo.update_phase2');
                    return view('private.cbs.cb', compact('cb', 'configurations', 'cbConfigurations', 'type', 'title','subpad','rootCbKey', 'contentListType'));
                    break;
                case $type == "phase3":
                    $title = trans('privatePhaseThree.update_phase3');
                    return view('private.cbs.cb', compact('cb', 'configurations', 'cbConfigurations', 'type', 'title','subpad','rootCbKey', 'contentListType'));
                    break;
                case $type == "qa":
                    $title = trans('privatePhaseThree.update_qa');
                    return view('private.cbs.cb', compact('cb', 'configurations', 'cbConfigurations', 'type', 'title','subpad','rootCbKey', 'contentListType'));
                    break;
            }
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["discussion.edit" => $e->getMessage()]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param $type
     * @param $cbKey
     * @param CbsRequest $requestCB
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function update($type,$cbKey,CbsRequest $requestCB)
    {
        try {
            $title = $type;
            $configurations = CB::getConfigurations();

            $configGroups =[];
            if($requestCB->groups!=''){
                foreach ($requestCB->groups as $group){
                    $optionGroup = explode('_', $group);
                    $configGroups[$optionGroup[0]][] = $optionGroup[1];
                }
            }
            if(!empty($requestCB->start_topic)) {
                if (!empty($requestCB->end_topic)) {
                    if ($requestCB->start_topic > $requestCB->end_topic) {
                        return redirect()->back()->withErrors(["cb.update" => trans('privateCbs.start_topic_after_end_topic')]);
                    }
                    if (!empty($requestCB->end_date)) {
                        if ($requestCB->start_topic > $requestCB->end_date) {
                            return redirect()->back()->withErrors(["cb.update" => trans('privateCbs.start_topic_after_end_date')]);
                        }
                        if ($requestCB->end_topic > $requestCB->end_date) {
                            return redirect()->back()->withErrors(["cb.update" => trans('privateCbs.end_topic_after_end_date')]);
                        }

                    }
                }
                if (!empty($requestCB->start_topic_edit)) {
                    if ($requestCB->start_topic > $requestCB->start_topic_edit) {
                        return redirect()->back()->withErrors(["cb.update" => trans('privateCbs.start_topic_after_start_topic_edit')]);
                    }
                    if (!empty($requestCB->end_topic_edit)) {
                        if ($requestCB->start_topic_edit > $requestCB->end_topic_edit) {
                            return redirect()->back()->withErrors(["cb.update" => trans('privateCbs.start_topic_edit_after_end_topic_edit')]);
                        }
                    }
                }
                if (!empty($requestCB->start_vote)) {
                    if (!empty($requestCB->end_vote)) {
                        if ($requestCB->start_vote > $requestCB->end_vote) {
                            return redirect()->back()->withErrors(["cb.update" => trans('privateCbs.start_vote_after_end_vote')]);
                        }
                        if (!empty($requestCB->end_date)) {
                            if ($requestCB->start_vote > $requestCB->end_date) {
                                return redirect()->back()->withErrors(["cb.update" => trans('privateCbs.start_vote_after_end_date')]);
                            }
                            if ($requestCB->end_vote > $requestCB->end_date) {
                                return redirect()->back()->withErrors(["cb.update" => trans('privateCbs.end_vote_after_end_date')]);
                            }

                        }
                    }
                }
            }
            $cb = CB::updateCB($cbKey,$requestCB);
            if($type == "project_2c") \App\Unimi\NestedCbs::clearAllRedisCache($cbKey);

            if(isset($requestCB->configurations_flag) && $requestCB->configurations_flag == 1){
                CB::setCbConfigurations($cbKey,$requestCB->configs, null, null, 0);

                Session::flash('message', trans('cbs.updateOk'));
                return redirect()->action('CbsController@showConfigurations', ['type' => $type,'cbKey' => $cbKey]);
            }

            if(isset($requestCB->notifications_flag) && $requestCB->notifications_flag == 1){
                if($requestCB->deadline!=''){
                    CB::setCbConfigurations($cbKey,$requestCB->notif, $configGroups, $requestCB->deadline, 1);
                }
                else{
                    $notifications = [];
                    if($requestCB->notif) {
                        foreach ($requestCB->notif as $key => $value) {
                            if ($key != 'notification_deadline') {
                                $notifications [$key] = $value;
                            }
                        }
                    }
                    CB::setCbConfigurations($cbKey,$notifications, $configGroups, null, 1);
                }

                Session::flash('message', trans('cbs.updateOk'));
                return redirect()->action('CbsController@showNotifications', ['type' => $type,'cbKey' => $cbKey]);
            }
            if(isset($requestCB->security_flag) && $requestCB->security_flag == 1){
                return redirect()->action('CbsController@showSecurityConfigurations', ['type' => $type,'cbKey' => $cbKey]);
            }

            return redirect()->action('CbsController@show', ['type' => $type,'cbKey' => $cbKey]);

        }
        catch(Exception $e) {
            //TODO: save inputs
            return redirect()->back()->withErrors(["cb.update" => $e->getMessage()]);
        }
    }

    /**
     * @param $type
     * @param Request $requestCB
     * @return $this|int
     */
    public function updateCb($type, Request $requestCB)
    {
        try {
            $title = $type;
            $configurations = CB::getConfigurations();
            $arrayConfIDs = [];
            foreach ($configurations as $configuration) {
                foreach ($configuration->configurations as $options) {
                    $arrayConfIDs[] = $options->id;
                }
            }
            $cb = CB::updateCB($requestCB->cbKey,$requestCB);
            if($type == "project_2c") \App\Unimi\NestedCbs::clearAllRedisCache($requestCB->cbKey);

            $configurations = [];
            foreach ($requestCB->all() as $key => $value) {
                if (strpos($key, 'configuration_') !== false) {
                    $id = str_replace("configuration_", "", $key);
                    $configurations[] = $id;
                    unset($arrayConfIDs[array_search($id, $arrayConfIDs)]);
                }
            }
            CB::setCbConfigurations($cb->cb_key, $configurations);

            return 1;
        }
        catch(Exception $e) {
            //TODO: save inputs
            return redirect()->back()->withErrors(["cb.update" => $e->getMessage()]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param $type
     * @param $cbKey
     * @param CbsRequest $requestCB
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function advancedUpdate($type,$cbKey,CbsRequest $requestCB)
    {
        try {
            $title = $type;
            $configurations = CB::getConfigurations();
            $arrayConfIDs = [];
            foreach ($configurations as $configuration) {
                foreach ($configuration->configurations as $options) {
                    $arrayConfIDs[] = $options->id;
                }
            }
            $cb = CB::updateCB($cbKey,$requestCB);
            if($type == "project_2c") \App\Unimi\NestedCbs::clearAllRedisCache($cbKey);
            $arrayConfigurations = [];
            foreach ($requestCB->all() as $key => $value) {
                if (strpos($key, 'configuration_') !== false) {
                    $id = str_replace("configuration_", "", $key);
                    $arrayConfigurations[] = $id;
                    unset($arrayConfIDs[array_search($id, $arrayConfIDs)]);
                }
            }
            CB::setCbConfigurations($cbKey,$arrayConfigurations);

            if($type == "project_2c")
                \App\Unimi\NestedCbs::clearCb($cbKey);

            Session::flash('message', trans('cbs.updateOk'));
            return redirect()->action('CbsController@show', ['type' => $type,'cbKey' => $cbKey]);

        }
        catch(Exception $e) {
            //TODO: save inputs
            return redirect()->back()->withErrors(["cb.update" => $e->getMessage()]);
        }
    }

    /**
     * @param Request $request
     * @param $type
     * @param $cbKey
     * @param $configuration_code
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showNotificationEmailTemplate(Request $request, $type, $cbKey, $configuration_code)
    {
        try {
            $cbTemplates = CB::getCbTemplates($cbKey);
            $cb = CB::getCbConfigurations($cbKey);
            $languages = Orchestrator::getLanguageList();

            $translations = [];
            foreach ($cbTemplates as $cbTemplate) {
                if ($cbTemplate->configuration_code == $configuration_code) {
                    $emailTemplate = Notify::getEmailTemplateTranslations($cbTemplate->template_key);
                    if (isset($emailTemplate->translations)) {
                        $translations[$cbTemplate->configuration_code] = $emailTemplate->translations;
                    }
                }
            }

            $author = Auth::getUser($cb->created_by);

            Session::put('sidebarArguments', ['type' => $type, 'cbKey' => $cbKey, 'activeFirstMenu' => 'notifications']);
            Session::put('sidebars', [0 => 'private', 1 => 'padsType']);

            $data = [];
            $data['type'] = $type;
            $data['cbKey'] = $cbKey;
            $data['sidebar'] = 'padsType';
            $data['active'] = 'notifications';
            $data['title'] = $title ?? null;
            $data['cb'] = $cb;
            $data['author'] = $author->name;
            $data['config_code'] = $configuration_code;
            $data['languages'] = $languages;
            $data['translations'] = $translations;

            return view('private.cbs.cbEmailTemplate', $data);
        }catch (Exception $e){
            return redirect()->back()->withErrors($e->getMessage());
        }
    }

    /**
     * @param Request $request
     * @param $type
     * @param $cbKey
     * @param $configuration_code
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function editNotificationEmailTemplate(Request $request, $type, $cbKey, $configuration_code)
    {
        try{
            $cbTemplates = CB::getCbTemplates($cbKey);
            $cb = CB::getCbConfigurations($cbKey);
            $languages = Orchestrator::getLanguageList();

            $translations = [];
            foreach($cbTemplates as $cbTemplate){
                if($cbTemplate->configuration_code == $configuration_code){
                    $emailTemplate = Notify::getEmailTemplateTranslations($cbTemplate->template_key);
                    if(isset($emailTemplate->translations)){
                        $translations[$cbTemplate->configuration_code] = $emailTemplate->translations;
                    }
                }
            }

            $author = Auth::getUser($cb->created_by);

            Session::put('sidebarArguments', ['type' => $type, 'cbKey' => $cbKey, 'activeFirstMenu' => 'notifications']);
            Session::put('sidebars', [0 => 'private', 1=> 'padsType']);

            $data['type'] = $type;
            $data['cbKey'] = $cbKey;
            $data['sidebar'] = 'padsType';
            $data['active'] = 'notifications';
            $data['title'] = $title ?? null;
            $data['cb'] = $cb;
            $data['author'] = $author->name;
            $data['config_code'] = $configuration_code;
            $data['languages'] = $languages;
            $data['translations'] = $translations;

            return view('private.cbs.cbEmailTemplate', $data);
        }catch (Exception $e){

        }
    }

    /**
     * @param Request $request
     * @param $type
     * @param $cbKey
     * @param $configurationCode
     * @return string
     */
    public function createNotificationEmailTemplate(Request $request, $type, $cbKey, $configurationCode){
        try{
            $cb = CB::getCbConfigurations($cbKey);
            $languages = Orchestrator::getLanguageList();
            $author = Auth::getUser($cb->created_by);

            $data['type'] = $type;
            $data['cbKey'] = $cbKey;
            $data['sidebar'] = 'padsType';
            $data['active'] = 'notifications';
            $data['cb'] = $cb;
            $data['author'] = $author->name;
            $data['config_code'] = $configurationCode;
            $data['languages'] = $languages;
            $data['notificationTypeCode'] = $request->input('notification_type_code') ?? null;

            return view('private.cbs.cbEmailTemplate', $data);
        }catch (Exception $e){
            return "false";
        }
    }

    /**
     * @param Request $request
     * @param $type
     * @param $cbKey
     * @param $configurationCode
     * @return string
     */
    public function storeNotificationEmailTemplate (Request $request, $type, $cbKey, $configurationCode){
        try{
            $typeCode = $request->input('notification_type_code') ?? null;
            $typeKey = Notify::getTypeKey($typeCode);
            $params = $request->all();

            $siteKey = Session::get('X-SITE-KEY');
            $languages = Orchestrator::getLanguageList();

            $translations = [];
            foreach($languages as $language){
                if(!empty($params['content_'.$language->code])){
                    $translations[] = [
                        'language_code' => $language->code,
                        'subject' => $params['subject_'.$language->code],
                        'header' => "",
                        'content' => $params['content_'.$language->code],
                        'footer' => ""
                    ];
                }
            }

            $cbTemplate = CB::verifyTemplate($cbKey, $configurationCode);

            if($cbTemplate->exists == false){
                $template = Notify::postEmailTemplate($typeKey, $siteKey, $translations);
                $emailTemplateKey = $template->email_template_key;
                $cbTemplateNew = CB::setCbTemplate($configurationCode, $cbKey, $emailTemplateKey);

                Session::flash('message', trans('EmailTemplates.store_Ok'));
                return redirect()->action('CbsController@showNotificationEmailTemplate', [$type, $cbKey, $configurationCode]);
            }
            Session::flash('error', trans('EmailTemplates.already_exists'));
            return redirect()->action('CbsController@showNotificationEmailTemplate', [$type, $cbKey, $configurationCode]);
        }catch (Exception $e){
            Session::flash('error', trans('EmailTemplates.store_NOk'));
            return back()->withInput()->withErrors(["cb.show" => $e->getMessage()]);
        }
    }

    /**
     * @param Request $request
     * @param $type
     * @param $cbKey
     * @param $configuration_code
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function updateNotificationEmailTemplate(Request $request, $type, $cbKey, $configuration_code){
        try{
            $params = $request->all();
            $typeKey = Notify::getTypeKey('generic_cb_notifications');

            $languages = Orchestrator::getLanguageList();

            $translations = [];
            foreach($languages as $language){
                if(!empty($params['content_'.$language->code])){
                    $translations[] = [
                        'language_code' => $language->code,
                        'subject' => $params['subject_'.$language->code],
                        'header' => "",
                        'content' => $params['content_'.$language->code],
                        'footer' => ""
                    ];
                }
            }

            $cbTemplate = CB::verifyTemplate($cbKey, $configuration_code);

            if($cbTemplate->exists == true){
                $template = Notify::editEmailTemplate($typeKey, $cbTemplate->templateKey, $translations);
            }

            $cb = CB::getCbConfigurations($cbKey);
            $configurations = CB::getConfigurations();
            $cbTemplates = CB::getCbTemplates($cbKey);

            $cbConfigurations = [];

            foreach ($cb->configurations as $config) {
                if ($config->code == 'notification_deadline'){
                    $value = isset($config->pivot->value) ? json_decode($config->pivot->value) : null;
                    $cbConfigurations[$config->code][$config->id] = !empty($value->deadline) ? $value->deadline : null;
                }else{
                    $cbConfigurations[$config->code][$config->id] = isset($config->pivot->value) ? json_decode($config->pivot->value) : null;
                }
            }

            $userLevels = Orchestrator::getAllEntityLoginLevels(Session::get('X-ENTITY-KEY'));
            $author = Auth::getUser($cb->created_by);

            $groups = Orchestrator::getEntityGroups();
            $languages = Orchestrator::getLanguageList();

            Session::put('sidebarArguments', ['type' => $type, 'cbKey' => $cbKey, 'activeFirstMenu' => 'notifications']);
            Session::put('sidebars', [0 => 'private', 1=> 'padsType']);

            $data['author'] = $author->name;
            $data['sidebar'] = 'padsType';
            $data['active'] = 'notifications';
            $data['title'] = $title ?? null;
            $data['cb'] = $cb;
            $data['type'] = $type;
            $data['cbConfigurations'] = $cbConfigurations;
            $data['configurations'] = $configurations;
            $data['userLevels'] = $userLevels;
            $data['groups'] = $groups;
            $data['languages'] = $languages;
            $data['cbTemplates'] = collect($cbTemplates)->keyBy('configuration_code') ?? null;

            Session::flash('message', trans('EmailTemplates.updateOk'));
            return view('private.cbs.notifications', $data);
        } catch (Exception $e){

        }
    }

    /**
     * Display the specified resource.
     *
     * @param $type
     * @param $cbKey
     * @return View
     **/
    public function show(Request $request, $type,$cbKey)
    {
        try {
            $languages = Orchestrator::getLanguageList();
            $configurations = CB::getConfigurations();
            $cb = CB::getCbConfigurations($cbKey);
            $subpad = $cb->parent_cb_id != 0;
            $cbConfigurations = [];
            foreach ($cb->configurations as $config) {
                $cbConfigurations[] = $config->id;
            }
            $cbModerators = CB::getCbModerators($cbKey);

            $keys = [];
            foreach($cbModerators as $cbModerator){
                $keys[] = $cbModerator->user_key;
            }

            if (count($keys) > 0) {
                $moderators = json_decode(json_encode(Auth::getListNames($keys)), true);
            }

            foreach ($cbModerators as $cbModerator){
                $dateAdded = Carbon::createFromFormat('Y-m-d H:i:s' ,$cbModerator->created_at)->toDateString();
                $moderators[$cbModerator->user_key]['date_added'] = $dateAdded;
            }

            $statusAvailable = CB::getStatusTypes();
            $statusTypes = [];
            foreach ($statusAvailable as $status){
                $statusTypes[$status->code] = $status->name;
            }

            $cbFilters = CB::getCbFilters($cbKey) ?? [];


            $cbFilter = [];
            foreach ($cbFilters as $filter)
            {
                $cbFilter = [];
                foreach ($cbFilters as $filter)
                {
                    foreach ($statusAvailable as $status){
                        if ($filter == $status->code)
                        {

                            $filterCode = $status->code;
                            $filter = $status->name;
                            $cbFilter[$filterCode] = $filter;
                        }
                    }
                }
            }

            $hasTechnicalAnalysis = !empty(CB::getCbQuestions($cbKey));

            switch ($type) {
                case $type == "idea":
                    $title = trans('privateIdeas.show_ideas');
                    break;
                case $type == "forum":
                    $title = trans('privateForums.show_forums');
                    break;
                case $type == "discussion":
                    $title = trans('privateDiscussions.show_discussions');
                    break;
                case $type == "proposal":
                    $title = trans('privateProposals.show_proposals');
                    break;
                case $type == "project_2c":
                    $title = trans('privateProject2Cs.show_project_2cs').' '.(isset($cb->title) ? $cb->title : null);
                    break;
                case $type == "publicConsultation":
                    $title = trans('privatePublicConsultations.show_public_consultations');
                    break;
                case $type == "tematicConsultation":
                    $title = trans('privateTematicConsultations.show_tematic_consultations');
                    break;
                case $type == "survey":
                    $title = trans('privateSurveys.show_surveys');
                    break;
                case $type == "project":
                    $title = trans('privateProject.show_project').' '.(isset($cb->title) ? $cb->title : null);
                    break;
                case $type == "phase1":
                    $title = trans('privatePhaseOne.show_phase1').' '.(isset($cb->title) ? $cb->title : null);
                    break;
                case $type == "phase2":
                    $title = trans('privatePhaseTwo.show_phase2').' '.(isset($cb->title) ? $cb->title : null);
                    break;
                case $type == "phase3":
                    $title = trans('privatePhaseThree.show_phase3').' '.(isset($cb->title) ? $cb->title : null);
                    break;
                case $type == "qa":
                    $title = trans('privatePhaseThree.show_qa').' '.(isset($cb->title) ? $cb->title : null);
                    break;
            }

            //sidebar
            Session::put('sidebarArguments', ['type' => $type, 'cbKey' => $cbKey, 'activeFirstMenu' => 'details']);
            if($subpad){
                Session::put('sidebarActive', 'subpadsType');
            }else{
                Session::put('sidebarActive', 'padsType');
            }

            if($subpad){
                $sidebar = 'subpadsType';
                $rootCbKey = \App\Unimi\NestedCbs::getRootCbKey($cb->parent_cb_id);
            }else{
                $sidebar = 'padsType';
                $rootCbKey = $cbKey;
            }
            $active = 'details';
            $entityKey = Orchestrator::getSiteEntity($_SERVER["HTTP_HOST"])->entity_id;

            $siteKey = Session::get('X-SITE-KEY');
            $contentList = CM::getNewContents('pages',$siteKey);

            $contentListType = [];

            foreach($contentList as $content){
                $contentListType[$content->content_key] = $content->name;
            }

            $pageName = null;
            if(!empty($cb->page_key)){
                $response = CM::getNewContent($cb->page_key);
                $pageName = $response->name;
            }

            $checklists = EMPATIA::getCbChecklist($entityKey,$cbKey);

            return view('private.cbs.cb', compact('cb', 'configurations', 'cbConfigurations', 'moderators','title','cbFilters','cbFilter','statusTypes','type','languages', 'sidebar', 'active','subpad','rootCbKey','hasTechnicalAnalysis','checklists','entityKey','cbKey', 'contentListType', 'pageName'));
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["cb.show" => $e->getMessage()]);
        }
    }


    /**
     * @param $type
     * @param $cbKey
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showTopics($type, $cbKey)
    {
        try{
            $cb = CB::getCbConfigurations($cbKey);
            $cbVotes = CB::getCbVotes($cbKey);
            if (ONE::verifyModuleAccess('cb','flags')) {
                $cb->flags = CB::getCbWithFlags($cbKey,"TOPICS")->flags??[];
            }
            $languages = Orchestrator::getLanguageList();
            $statusAvailable = CB::getStatusTypes();
            $statusTypes = [];
            foreach ($statusAvailable as $status){
                $statusTypes[$status->code] = $status->name;
            }

            $cbAuthor = Auth::getUserByKey($cb->created_by);

            $filtered=[];
            $cbAuthors = CB::getCbTopicAuthors($cbKey);
            foreach ($cbAuthors as $author){
                $filtered[] = $author;
            }

            $authors = [];
            if (!is_null($filtered)) {
                $aux = $cbAuthors;
                $aux = Auth::listUser(collect($filtered)->unique(), $start_date = null, $end_date = null, $role = null, $info = null, $category = null, $min = null, $max = null, $birthday = null, $checkbox = null, $budget = null);
                foreach ($aux as $value) {
                    $authors[] = $value;
                }
            } else {
                $authors = null;
            }

            $CbParameters = CB::getCbParametersOptions($cbKey, true)->parameters;

            $fileId = 0;
            $parameters = [];
            $fileCode = '';

            $phases = collect($CbParameters)->where('code','=','topic_checkpoint_phase');
            if($phases->count()>0) {
                foreach ($phases as $phase) {
                    $name = $phase->parameter;
                    $parameterOptions[] = array('id' => $phase->id, 'name' => $name);
                }
                $parameters['phases'] = array('id' => 'phases', 'name' => 'status2', 'code' => 'topic_checkpoint_phase', 'options' => $parameterOptions);
            }

            foreach ($CbParameters as $parameter) {
                if($parameter->code != 'topic_checkpoint_phase') {
                    $name = $parameter->parameter;
                    $code = $parameter->type->code;

                    $parameterOptions = [];
                    $options = $parameter->options;
                    foreach ($options as $option) {
                        $parameterOptions[$option->id] = $option->label;
                    }
                    $parameters[$parameter->id] = array('id' => $parameter->id, 'name' => $name, 'code' => $code, 'options' => $parameterOptions, 'mandatory' => $parameter->mandatory);
                }
            }

            switch ($type) {
                case $type == "idea":
                    $title = trans('privateIdeas.show_topics');
                    break;
                case $type == "forum":
                    $title = trans('privateForums.show_topics');
                    break;
                case $type == "discussion":
                    $title = trans('privateDiscussions.show_topics');
                    break;
                case $type == "proposal":
                    $title = trans('privateProposals.show_topics');
                    break;
                case $type == "project_2c":
                    $title = trans('privateProject2Cs.show_topics').' '.(isset($cb->title) ? $cb->title : null);
                    break;
                case $type == "publicConsultation":
                    $title = trans('privatePublicConsultations.show_topics');
                    break;
                case $type == "tematicConsultation":
                    $title = trans('privateTematicConsultations.show_topics');
                    break;
                case $type == "survey":
                    $title = trans('privateSurveys.show_topics');
                    break;
                case $type == "phase1":
                    $title = trans('privatePhaseOne.show_topics');
                    break;
                case $type == "phase2":
                    $title = trans('privatePhaseTwo.show_topics');
                    break;
                case $type == "phase3":
                    $title = trans('privatePhaseThree.show_topics');
                    break;
                case $type == "qa":
                    $title = trans('privateQA.show_topics');
                    break;
            }

            Session::put('sidebarArguments', ['type' => $type, 'cbKey' => $cbKey, 'activeFirstMenu' => 'topics']);
            Session::put('sidebars', [0 => 'private', 1=> 'padsType']);

            $sidebar = 'padsType';
            $active = 'topics';

            $configurations = $cb->configurations;

            return view('private.cbs.topics', compact('title', 'cb', 'cbVotes','type', 'statusTypes', 'sidebar', 'cbAuthor', 'active','authors','parameters', 'configurations','languages'));
        }catch (Exception $e){
            return redirect()->back()->withErrors(["cb.showTopics" => $e->getMessage()]);
        }
    }

    /**
     * @param $type
     * @param $cbKey
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showParameters($type, $cbKey)
    {
        try{
            $cb = CB::getCbConfigurations($cbKey);

            switch ($type) {
                case $type == "idea":
                    $title = trans('privateIdeas.show_parameters');
                    break;
                case $type == "forum":
                    $title = trans('privateForums.show_parameters');
                    break;
                case $type == "discussion":
                    $title = trans('privateDiscussions.show_parameters');
                    break;
                case $type == "proposal":
                    $title = trans('privateProposals.show_parameters');
                    break;
                case $type == "project_2c":
                    $title = trans('privateProject2Cs.show_parameters').' '.(isset($cb->title) ? $cb->title : null);
                    break;
                case $type == "publicConsultation":
                    $title = trans('privatePublicConsultations.show_parameters');
                    break;
                case $type == "tematicConsultation":
                    $title = trans('privateTematicConsultations.show_parameters');
                    break;
                case $type == "survey":
                    $title = trans('privateSurveys.show_parameters');
                    break;
                case $type == "phase1":
                    $title = trans('privatePhaseOne.show_parameters');
                    break;
                case $type == "phase2":
                    $title = trans('privatePhaseTwo.show_parameters');
                    break;
                case $type == "phase3":
                    $title = trans('privatePhaseThree.show_parameters');
                    break;
                case $type == "qa":
                    $title = trans('privateQA.show_parameters');
                    break;
            }


            // Sidebar
            Session::put('sidebarArguments', ['type' => $type, 'cbKey' => $cbKey, 'activeFirstMenu' => 'parameters']);
            Session::put('sidebars', [0 => 'private', 1=> 'padsType']);

            $sidebar = 'padsType';
            $active = 'parameters';

            /*
            $subpad = $cb->parent_cb_id != 0;

            Session::put('sidebarArguments', ['type' => $type, 'cbKey' => $cbKey, 'activeFirstMenu' => 'parameters']);
            if ($subpad)
                Session::put('sidebars', [0 => 'private', 1=> 'subpadsType']);
            else
                Session::put('sidebars', [0 => 'private', 1=> 'padsType']);

            if ($subpad)
                $sidebar = 'subpadsType';
            else
                Session::put('sidebars', [0 => 'private', 1=> 'padsType']);
            */

            $cbAuthor = Auth::getUserByKey($cb->created_by);

            return view('private.cbs.parameters', compact('title', 'cb', 'type','cbAuthor','sidebar', 'active','subpad'));
        }catch (Exception $e){

        }
    }

    /**
     * @param $type
     * @param $cbKey
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showVotes($type, $cbKey)
    {
        try{
            $cb = CB::getCbConfigurations($cbKey);

            switch ($type) {
                case $type == "idea":
                    $title = trans('privateIdeas.show_votes');
                    break;
                case $type == "forum":
                    $title = trans('privateForums.show_votes');
                    break;
                case $type == "discussion":
                    $title = trans('privateDiscussions.show_votes');
                    break;
                case $type == "proposal":
                    $title = trans('privateProposals.show_votes');
                    break;
                case $type == "publicConsultation":
                    $title = trans('privatePublicConsultations.show_votes');
                    break;
                case $type == "tematicConsultation":
                    $title = trans('privateTematicConsultations.show_tematicvotes');
                    break;
                case $type == "survey":
                    $title = trans('privateSurveys.show_surveys');
                    break;
                case $type == "phase1":
                    $title = trans('privatePhaseOne.show_phase1');
                    break;
                case $type == "phase2":
                    $title = trans('privatePhaseTwo.show_phase2');
                    break;
                case $type == "phase3":
                    $title = trans('privatePhaseThree.show_phase3');
                    break;
                case $type == "qa":
                    $title = trans('privateQA.show_qa');
                    break;
            }

            // Sidebar
            Session::put('sidebarArguments', ['type' => $type, 'cbKey' => $cbKey, 'activeFirstMenu' => 'votes']);
            Session::put('sidebars', [0 => 'private', 1=> 'padsType']);

            $sidebar = 'padsType';
            $active = 'votes';

            $cbAuthor = Auth::getUserByKey($cb->created_by);

            return view('private.cbs.votes', compact('title', 'cb', 'type', 'cbAuthor', 'sidebar', 'active'));
        } catch (Exception $e){

        }
    }

    /**
     * @param Request $request
     * @param $type
     * @param $cbKey
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showModerators(Request $request, $type, $cbKey)
    {
        try{
            $cb = CB::getCbConfigurations($cbKey);
            $cbModerators = CB::getCbModerators($cbKey);

            $keys = [];
            foreach($cbModerators as $cbModerator){
                $keys[] = $cbModerator->user_key;
            }

            if (count($keys) > 0) {
                $moderators =json_decode(json_encode(Auth::getListNames($keys)), true);
            }

            foreach ($cbModerators as $cbModerator){
                $dateAdded = Carbon::createFromFormat('Y-m-d H:i:s' ,$cbModerator->created_at)->toDateString();
                $moderators[$cbModerator->user_key]['date_added'] = $dateAdded;
            }

            switch ($type) {
                case $type == "idea":
                    $title = trans('privateIdeas.show_moderators');
                    break;
                case $type == "forum":
                    $title = trans('privateForums.show_moderators');
                    break;
                case $type == "discussion":
                    $title = trans('privateDiscussions.show_moderators');
                    break;
                case $type == "proposal":
                    $title = trans('privateProposals.show_moderators');
                    break;
                case $type == "project_2c":
                    $title = trans('privateProject2Cs.show_moderators').' '.(isset($cb->title) ? $cb->title : null);
                    break;
                case $type == "publicConsultation":
                    $title = trans('privatePublicConsultations.show_moderators');
                    break;
                case $type == "tematicConsultation":
                    $title = trans('privateTematicConsultations.show_moderators');
                    break;
                case $type == "survey":
                    $title = trans('privateSurveys.show_surveys');
                    break;
                case $type == "phase1":
                    $title = trans('privatePhaseOne.show_phase1');
                    break;
                case $type == "phase2":
                    $title = trans('privatePhaseTwo.show_phase2');
                    break;
                case $type == "phase3":
                    $title = trans('privatePhaseThree.show_phase3');
                    break;
                case $type == "qa":
                    $title = trans('privateQA.show_qa');
                    break;
            }
            $step = $request->step ?? null;

            Session::put('sidebarArguments', ['type' => $type, 'cbKey' => $cbKey, 'activeFirstMenu' => 'moderators']);
            Session::put('sidebars', [0 => 'private', 1=> 'padsType']);

            $sidebar = 'padsType';
            $active = 'moderators';

            $cbAuthor = Auth::getUserByKey($cb->created_by);

            return view('private.cbs.moderators', compact('title', 'cb', 'cbKey', 'type', 'cbModerators', 'moderators', 'step', 'cbAuthor', 'sidebar', 'active'));
        }catch (Exception $e){

        }
    }

    /**
     * @param $type
     * @param $cbKey
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showConfigurations($type, $cbKey)
    {
        try{
            $cb = CB::getCbConfigurations($cbKey);
            $configurationsNew = CB::getConfigurations();

            $cbConfigurations = [];
            foreach ($cb->configurations as $config) {
                $cbConfigurations[$config->code][$config->id] = isset($config->pivot->value) ? json_decode($config->pivot->value) : null;
            }

            $configurations = [];
            foreach ($configurationsNew as $configuration){
                if($configuration->code != 'notifications' && $configuration->code != 'notifications_owners' && $configuration->code!= 'notifications_topic' && $configuration->code!='notification_deadline'){
                    $configurations[] = $configuration;
                }
            }


            switch ($type) {
                case $type == "idea":
                    $title = trans('privateIdeas.show_configurations');
                    break;
                case $type == "forum":
                    $title = trans('privateForums.show_configurations');
                    break;
                case $type == "discussion":
                    $title = trans('privateDiscussions.show_configurations');
                    break;
                case $type == "proposal":
                    $title = trans('privateProposals.show_configurations');
                    break;
                case $type == "project":
                    $title = trans('privateProject.show_topics').' '.(isset($cb->title) ? $cb->title : null);
                    break;
                case $type == "project_2c":
                    $title = trans('privateProject2Cs.show_configurations').' '.(isset($cb->title) ? $cb->title : null);
                    break;
                case $type == "publicConsultation":
                    $title = trans('privatePublicConsultations.show_configurations');
                    break;
                case $type == "tematicConsultation":
                    $title = trans('privateTematicConsultations.show_configurations');
                    break;
                case $type == "survey":
                    $title = trans('privateSurveys.show_configurations');
                    break;
                case $type == "phase1":
                    $title = trans('privatePhaseOne.show_configurations');
                    break;
                case $type == "phase2":
                    $title = trans('privatePhaseTwo.show_configurations');
                    break;
                case $type == "phase3":
                    $title = trans('privatePhaseThree.show_configurations');
                    break;
                case $type == "qa":
                    $title = trans('privateQA.show_configurations');
                    break;
            }

            $userLevels = Orchestrator::getAllEntityLoginLevels(Session::get('X-ENTITY-KEY'));

            if (!empty($userLevels)){
                $userLevels = collect($userLevels)->keyBy('login_level_key')->toArray();
            };

            $subpad = $cb->parent_cb_id != 0;

            Session::put('sidebarArguments', ['type' => $type, 'cbKey' => $cbKey, 'activeFirstMenu' => 'configurations']);
            Session::put('sidebars', [0 => 'private', 1=> ($subpad)?'subpadsType':'padsType']);

            $sidebar = ($subpad)?'subpadsType':'padsType';
            $active = 'configurations';

            $author = Auth::getUserByKey($cb->created_by);
            //$author = $author->name;

            $data['author']             = $author;
            $data['title']              = $title ?? "";
            $data['cb']                 = $cb;
            $data['type']               = $type;
            $data['cbConfigurations']   = $cbConfigurations;
            $data['configurations']     = $configurations;
            $data['sidebar']            = $sidebar;
            $data['active']             = 'configurations';
            $data['userLevels']         = $userLevels ?? null;

            return view('private.cbs.configurations', $data);
        }catch (Exception $e){

        }
    }

    /**
     * @param Request $request
     * @param $type
     * @param $cbKey
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showNotifications(Request $request, $type, $cbKey)
    {
        try{
            $cb = CB::getCbConfigurations($cbKey);
            $configurations = CB::getConfigurations();
            $cbTemplates = CB::getCbTemplates($cbKey);

            $cbConfigurations = [];
            foreach ($cb->configurations as $config) {

                if ($config->code == 'notification_deadline'){
                    $value = isset($config->pivot->value) ? json_decode($config->pivot->value) : null;
                    $cbConfigurations[$config->code][$config->id] = !empty($value->deadline) ?$value->deadline : null;
                }else
                    $cbConfigurations[$config->code][$config->id] = isset($config->pivot->value) ? json_decode($config->pivot->value) : null;
            }

            switch ($type) {
                case $type == "idea":
                    $title = trans('privateIdeas.show_configurations');
                    break;
                case $type == "forum":
                    $title = trans('privateForums.show_configurations');
                    break;
                case $type == "discussion":
                    $title = trans('privateDiscussions.show_configurations');
                    break;
                case $type == "proposal":
                    $title = trans('privateProposals.show_configurations');
                    break;
                case $type == "publicConsultation":
                    $title = trans('privatePublicConsultations.show_configurations');
                    break;
                case $type == "tematicConsultation":
                    $title = trans('privateTematicConsultations.show_configurations');
                    break;
                case $type == "survey":
                    $title = trans('privateSurveys.show_configurations');
                    break;
                case $type == "phase1":
                    $title = trans('privatePhaseOne.show_configurations');
                    break;
                case $type == "phase2":
                    $title = trans('privatePhaseTwo.show_configurations');
                    break;
                case $type == "phase3":
                    $title = trans('privatePhaseThree.show_configurations');
                    break;
                case $type == "qa":
                    $title = trans('privateQA.show_configurations');
                    break;
            }

            $userLevels = Orchestrator::getAllEntityLoginLevels(Session::get('X-ENTITY-KEY'));
            $cbAuthor = Auth::getUserByKey($cb->created_by);

            $groups = Orchestrator::getEntityGroups();
            $languages = Orchestrator::getLanguageList();

            Session::put('sidebarArguments', ['type' => $type, 'cbKey' => $cbKey, 'activeFirstMenu' => 'notifications']);
            Session::put('sidebars', [0 => 'private', 1=> 'padsType']);

            $sidebar = 'padsType';
            $active = 'notifications';

            $data['cbAuthor'] = $cbAuthor;
            $data['cb'] = $cb;
            $data['type'] = $type;
            $data['cbConfigurations'] = $cbConfigurations;
            $data['configurations'] = $configurations;
            $data['groups'] = $groups;
            $data['languages'] = $languages;
            $data['configurations'] = $configurations;
            $data['sidebar'] = $sidebar;
            $data['active'] = $active;
            $data['cbTemplates'] = collect($cbTemplates)->keyBy('configuration_code') ?? null;

            return view('private.cbs.notifications', $data);
        }catch (Exception $e){

        }
    }

    /**
     * @param Request $request
     * @param $type
     * @param $cbKey
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function editNotifications(Request $request, $type, $cbKey)
    {
        try {
            $typeCode = 'generic_cb_notifications';
            $cb = CB::getCbConfigurations($cbKey);
            $configurations = CB::getConfigurations();

            $cbTemplates = CB::getCbTemplates($cbKey);

            $translations = [];
            foreach ($cbTemplates as $cbTemplate) {
                $emailTemplate = Notify::getEmailTemplateTranslations($cbTemplate->template_key);
                if (isset($emailTemplate->translations)) {
                    $translations[$cbTemplate->configuration_code] = $emailTemplate->translations;
                }
            }

            $cbConfigurations = [];
            foreach ($cb->configurations as $config) {
                if ($config->code == 'notification_deadline') {
                    $value = isset($config->pivot->value) ? json_decode($config->pivot->value) : null;
                    $cbConfigurations[$config->code][$config->id] = !empty($value->deadline) ? $value->deadline : null;
                } else
                    $cbConfigurations[$config->code][$config->id] = isset($config->pivot->value) ? json_decode($config->pivot->value) : null;
            }

            $groups = Orchestrator::getEntityGroups();
            $languages = Orchestrator::getLanguageList();

            switch ($type) {
                case $type == "idea":
                    $title = trans('privateIdeas.show_configurations');
                    break;
                case $type == "forum":
                    $title = trans('privateForums.show_configurations');
                    break;
                case $type == "discussion":
                    $title = trans('privateDiscussions.show_configurations');
                    break;
                case $type == "proposal":
                    $title = trans('privateProposals.show_configurations');
                    break;
                case $type == "publicConsultation":
                    $title = trans('privatePublicConsultations.show_configurations');
                    break;
                case $type == "tematicConsultation":
                    $title = trans('privateTematicConsultations.show_configurations');
                    break;
                case $type == "survey":
                    $title = trans('privateSurveys.show_configurations');
                    break;
                case $type == "phase1":
                    $title = trans('privatePhaseOne.show_configurations');
                    break;
                case $type == "phase2":
                    $title = trans('privatePhaseTwo.show_configurations');
                    break;
                case $type == "phase3":
                    $title = trans('privatePhaseThree.show_configurations');
                    break;
                case $type == "qa":
                    $title = trans('privateQA.show_configurations');
                    break;
            }

            $userLevels = Orchestrator::getAllEntityLoginLevels(Session::get('X-ENTITY-KEY'));
            $author = Auth::getUser($cb->created_by);

            Session::put('sidebarArguments', ['type' => $type, 'cbKey' => $cbKey, 'activeFirstMenu' => 'notifications']);
            Session::put('sidebars', [0 => 'private', 1 => 'padsType']);

            $data['cbAuthor'] = $author;
            $data['sidebar'] = 'padsType';
            $data['active'] = 'notifications';
            $data['title'] = $title ?? null;
            $data['cb'] = $cb;
            $data['type'] = $type;
            $data['cbConfigurations'] = $cbConfigurations;
            $data['configurations'] = $configurations;
            $data['userLevels'] = $userLevels;
            $data['groups'] = $groups;
            $data['languages'] = $languages;
            $data['translations'] = $translations;
            $data['cbTemplates'] = collect($cbTemplates)->keyBy('configuration_code') ?? null;

            return view('private.cbs.notifications', $data);
        } catch (Exception $e) {

        }
    }

    /**
     * @param $type
     * @param $cbKey
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function editConfigurations($type, $cbKey)
    {
        try{
            $cb = CB::getCbConfigurations($cbKey);
            $configurationsNew = CB::getConfigurations();

            $cbConfigurations = [];
            foreach ($cb->configurations as $config) {
                $cbConfigurations[$config->code][$config->id] = isset($config->pivot->value) ? json_decode($config->pivot->value) : null;
            }

            $configurations = [];
            foreach ($configurationsNew as $configuration){
                if($configuration->code != 'notifications' && $configuration->code != 'notifications_owners' && $configuration->code!= 'notifications_topic' && $configuration->code!='notification_deadline'){
                    $configurations[] = $configuration;
                }
            }

            /*foreach ($cb->configurations as $config) {
                $cbConfigurations[] = $config->id;
            }*/

            switch ($type) {
                case $type == "idea":
                    $title = trans('privateIdeas.show_configurations');
                    break;
                case $type == "forum":
                    $title = trans('privateForums.show_configurations');
                    break;
                case $type == "discussion":
                    $title = trans('privateDiscussions.show_configurations');
                    break;
                case $type == "proposal":
                    $title = trans('privateProposals.show_configurations');
                    break;
                case $type == "project":
                    $title = trans('privateProject.show_topics').' '.(isset($cb->title) ? $cb->title : null);
                    break;
                case $type == "project_2c":
                    $title = trans('privateProject2Cs.show_configurations').' '.(isset($cb->title) ? $cb->title : null);
                    break;
                case $type == "publicConsultation":
                    $title = trans('privatePublicConsultations.show_configurations');
                    break;
                case $type == "tematicConsultation":
                    $title = trans('privateTematicConsultations.show_configurations');
                    break;
                case $type == "survey":
                    $title = trans('privateSurveys.show_configurations');
                    break;
                case $type == "phase1":
                    $title = trans('privatePhaseOne.show_configurations');
                    break;
                case $type == "phase2":
                    $title = trans('privatePhaseTwo.show_configurations');
                    break;
                case $type == "phase3":
                    $title = trans('privatePhaseThree.show_configurations');
                    break;
                case $type == "qa":
                    $title = trans('privateQA.show_configurations');
                    break;
            }

            $subpad = $cb->parent_cb_id != 0;
            $userLevels = Orchestrator::getAllEntityLoginLevels(Session::get('X-ENTITY-KEY'));
            $author = Auth::getUser($cb->created_by);

            Session::put('sidebarArguments', ['type' => $type, 'cbKey' => $cbKey, 'activeFirstMenu' => 'configurations']);
            Session::put('sidebars', [0 => 'private', 1=> ($subpad)?'subpadsType':'padsType']);

            $data['author'] =  $author;
            $data['sidebar'] = ($subpad)?'subpadsType':'padsType';
            $data['active'] = 'configurations';
            $data['title'] = $title ?? "";
            $data['cb'] = $cb;
            $data['type'] = $type;
            $data['cbConfigurations'] = $cbConfigurations;
            $data['configurations'] = $configurations;
            $data['userLevels'] = $userLevels;

            return view('private.cbs.configurations', $data);
        } catch (Exception $e) {

        }
    }


    /**
     * Remove the specified resource from storage.
     * @param $type
     * @param $cbKey
     * @return $this|string
     */
    public function destroy($type,$cbKey)
    {
        try {
            $api = $this->getApiByType($type);
            if($type == "project_2c")
                $subpad = \App\Unimi\NestedCbs::isSubpad($cbKey);
            else
                $subpad = false;

            if($type == "project_2c") \App\Unimi\NestedCbs::clearAllRedisCache($cbKey);
            CB::deleteCb($cbKey);


            if(!$subpad)
                Orchestrator::deleteCb($api,$cbKey);
            Session::flash('message', trans('cbs.deleteOk'));
            return action('CbsController@indexManager', ['typeFilter' => $type]);

        } catch (Exception $e) {
            //TODO: save inputs
            return redirect()->back()->withErrors(["cb.destroy" => $e->getMessage()]);
        }
    }

    /**
     * @param $type
     * @param $cbKey
     * @return View
     */
    public function delete($type, $cbKey){
        try{
            $data = array();
            $data['action'] = action("CbsController@destroy", ['type'=>$type,'cbKey'=>$cbKey]);
            $data['title'] = trans('privateCbs.delete');
            $data['msg'] = trans('privateCbs.are_you_sure_you_want_to_delete').' ?';
            $data['btn_ok'] = trans('privateCbs.delete');
            $data['btn_ko'] = trans('privateCbs.cancel');

            return view("_layouts.deleteModal", $data);
        } catch (Exception $e) {

        }
    }

    /**
     * @param $type
     * @return $this
     */
    public function getIndexTable($type){
        try {
            $api = $this->getApiByType($type);
            //TODO:catch error
            $cbList = Orchestrator::getCbTypes($api);

            if(count($cbList)>0)
                $cbs = CB::getListCBs($cbList);
            else{
                $cbs = [];
            }
            // in case of json
            $collection = Collection::make($cbs);
            return Datatables::of($collection)
                ->editColumn('title', function ($collection) use($type){
                    return "<a href='" . action('CbsController@show', ['type' => $type, 'cbKey'=>$collection->cb_key]) . "'>" . $collection->title . "</a>";
                })
                ->addColumn('action', function ($collection) use($type) {
                    return ONE::actionButtons(['type' => $type,'cbKey' => $collection->cb_key], ['form' => 'cbs','edit' => 'CbsController@edit', 'delete' => 'CbsController@delete']);
                })
                ->rawColumns(['title','action'])
                ->make(true);

        } catch (Exception $e) {
            //TODO: save inputs
            return redirect()->back()->withErrors(["cbs.getIndexTable" => $e->getMessage()]);
        }
    }

    /**
     * @param Request $request
     * @return $this|array
     */
    public function getListOfCbsByType(Request $request){
        try {
            $api = $this->getApiByType($request->type);
            //TODO:catch error
            $cbList = Orchestrator::getCbTypes($api);

            if(count($cbList)>0)
                $cbs = CB::getListCBs($cbList);
            else{
                $cbs = [];
            }
            // in case of json
            return $cbs;

        } catch (Exception $e) {
            //TODO: save inputs
            return redirect()->back()->withErrors(["cbs.getIndexTable" => $e->getMessage()]);
        }
    }

    /**
     * @param Request $request
     * @return $this|array
     */
    public function getListOfTopicsByCb(Request $request){

        $topics = CB::getTopicsByCbKey($request['cbKey']);

        // in case of json
        return $topics;

        /*} catch (Exception $e) {
            //TODO: save inputs

            return redirect()->back()->withErrors(["cbs.getIndexTable" => $e->getMessage()]);
        }*/
    }

    /**
     * @param Request $request
     * @return string
     */
    public function getActivePads(Request $request)
    {
        try {
            $start_date = $request->get("start_date");
            $end_date = $request->get("end_date");

            $cbs = [];

            $module = 'cb';
            $type = $request->filter_types ?? "";

            if(Session::get('user_role') == 'admin' || Session::get('user_role') == 'manager'){
                $filterTypes = !empty($request->filter_types) ? $request->filter_types :[];
                $cbs = collect(Orchestrator::getAllCbs())->toArray();
                if (!empty($filterTypes)) {
                    $cbs = collect($cbs)->filter(function($cb) use($filterTypes){
                        return $cb->cb_type->code == $filterTypes;
                    })->toArray();
                }

                $cbsDetails = CB::getListCBs($cbs);

                foreach ($cbsDetails as $cbsDetail) {
                    $cbsDetail->name = Auth::getUser($cbsDetail->created_by)->name ?? $cbsDetail->created_by;

                }
                if ($start_date!=null && $end_date!=null) {

                    $newList1=collect($cbsDetails)->where('start_date', '>=', $start_date);
                    $newList=$newList1->where('start_date', '<=', $end_date);

                }     else{
                    $newList=collect($cbsDetails)->sortByDesc('id');
                }
            }else
                $newList = collect([]);

            //Datatable
            return Datatables::of($newList)
                ->editColumn('title', function($newList) use($cbs) {
                    if(array_key_exists($newList->cb_key,$cbs)) {
                        return "<a href='" . action('CbsController@show', [$cbs[$newList->cb_key]->cb_type->code, $newList->cb_key]) . "'>" . $newList->title . "</a>";
                    }
                })
                ->addColumn('type', function($newList) use($cbs){
                    return trans('privateCbs.' . $cbs[$newList->cb_key]->cb_type->code);
                })
               ->rawColumns(['title', 'action'])
                ->make(true);
        } catch (Exception $e) {
            return  $e->getMessage();
        }
    }

    /**
     * Remove the specified Moderator from storage.
     *
     * @param $type
     * @param $cbKey
     * @param $idModerator
     * @return string
     */
    public function deleteModerator($type,$cbKey, $idModerator)
    {
        try {
            CB::deleteModerator($cbKey,$idModerator);

            return action('CbsController@showModerators', ["type" => $type, "cbKey" => $cbKey]);
        } catch (Exception $e) {
            //TODO: save inputs and show error-...
            return action('CbsController@show', ['type'=>$type,'cbKey'=>$cbKey]);
        }
    }

    /**
     * Show confirm popup to remove the specified resource from storage.
     *
     * @param $type
     * @param $cbKey
     * @param $idModerator
     * @return View
     */
    public function deleteModeratorConfirm($type,$cbKey, $idModerator){
        try{
            $data = array();
            $data['action'] = action("CbsController@deleteModerator", ['type'=>$type,'cbKey'=> $cbKey, 'id' => $idModerator]);
            $data['title'] = "DELETE";
            $data['msg'] = "Are you sure you want to delete this Moderator?";
            $data['btn_ok'] = "Delete";
            $data['btn_ko'] = "Cancel";

            return view("_layouts.deleteModal", $data);
        } catch (Exception $e) {

        }
    }

    /**
     * Add managers to Cb.
     *
     * @param $type
     * @param $cbKey
     * @param PostRequest $request
     * @return string
     */
    public function addModerator($type,$cbKey,PostRequest $request)
    {
        try {
            $moderators = [];
            $keys = json_decode($request->moderatorsKey);
            foreach($keys as $key){
                $moderators[] = array('cb_key' => $request->cbKey, 'user_key' => $key,'type_id' => 1, );
            }
            CB::setCbModerators($cbKey,$moderators);

            if(!empty($request->get("step")))
                return action('CbsController@showModerators', ['type'=>$type,'cbKey' =>$cbKey, 'step' => $request->get("step")]);
            else
                return action('CbsController@showModerators', ['type'=>$type,'cbKey' =>$cbKey]);

        } catch (Exception $e) {
            return "false";
        }
    }

    /**
     * Get all Users
     *
     * @param $type
     * @param $cbKey
     * @return string
     */
    public function allManagers($type,$cbKey)
    {
        try{
            $usersList = Orchestrator::getAllManagers();
            $cbModerators = CB::getCbModerators($cbKey);
            $keys = [];
            foreach($cbModerators as $cbModerator){
                $keys[] = $cbModerator->user_key;
            }
            $usersKeys = [];
            foreach ($usersList as $item) {
                if(!in_array($item->user_key, $keys))
                    $usersKeys[] = $item->user_key;
            }
            if (count($usersKeys) > 0) {
                $moderators = json_decode(json_encode(Auth::getListNames($keys)), true);
                if(count($moderators) > 0) {
                    $html = '<table  class="table table-hover table-striped dataTable no-footer table-responsive">';
                    $html .= '<tbody>';
                    $i = 0;
                    foreach ($moderators as $user) {
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
            return '<div style="text-align: center; min-height: 100px;padding-top: 40px; color:#3c8dbc; text-transform: uppercase"><b>Without Managers to show</b></div>';
        } catch (Exception $e) {

        }
    }

    /**
     * Get all Users
     * @param $type
     * @param null $cbKey
     * @return
     */
    public function allUsers($type,$cbKey = null)
    {
        try{
            $usersList = Orchestrator::getAllUsers();

            $data = [];
            $usersKeys = [];
            foreach ($usersList as $item) {
                // if(!in_array($item->user_key, $keys))
                $usersKeys[] = $item->user_key;
            }

            if (count($usersKeys) > 0) {
                $data = Auth::getUserNames($usersKeys);
            }
            $collection = Collection::make($data);

            // in case of json
            return Datatables::of($collection)
                ->addColumn('moderadorCheckbox', function ($collection) {
                    // return "<input name='moderators[]' value='".$collection->user_key."' type='checkbox'  />";
                    // $checked = "checked='true'";
                    if( !empty($collection->photo_id) ){
                        $userImage = URL::action('FilesController@download', ['id' => $collection->photo_id, 'code' => $collection->photo_code, 1] );
                    }else{
                        $userImage = asset('images/icon-user-default-160x160.png');
                    }
                    return "<div class='oneSwitch'><input onclick=\"toggleModeratorItem(this,'".$collection->name."','".$userImage."')\" type='checkbox' name='moderators[]' value='".$collection->user_key."' class='oneSwitch-checkbox' id='moderatorCheckbox_".$collection->user_key."'  ><label class='oneSwitch-label' for='moderatorCheckbox_".$collection->user_key."'><span class='oneSwitch-inner'></span><span class='oneSwitch-switch'></span></label></div>";
                })
                ->addColumn('name', function ($collection) {
                    return $collection->name;
                })
                ->addColumn('action', function () {
                    return "";
                })
                ->rawColumns(['moderadorCheckbox','action'])
                ->make(true);
        } catch (Exception $e) {

        }
    }

    /**
     * @param $type
     * @return bool|mixed
     * @throws Exception
     */
    private function getApiByType($type){
        try{
            $api = isset($this->cbType[$type]) ? $this->cbType[$type] : false;
            if ($api == false) {
                throw new Exception( "Error get cb type" );
            }
            return $api;
        } catch (Exception $e) {

        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|string
     */
    function getParameter(Request $request){
        try {
            if(!empty($request->cbKey)){
                $cbTemplate = CB::getCb($request->cbKey);
                $parametersCbTemplate =  $cbTemplate->parameters;

                foreach($parametersCbTemplate as $paramsChoosed){
                    if($paramsChoosed->id == $request->parameter_id){
                        $parameterTemplateChoosed = $paramsChoosed;
                    }
                }
                $languages = Orchestrator::getLanguageList();
                $parameterTypesList = CB::getParametersTypes();

                $parameterTypes = collect($parameterTypesList)->pluck('name','code')->toArray();
                $translations = collect($paramsChoosed->translations)->toArray();

                foreach($parameterTemplateChoosed->options as $options)
                    $translationsOptions[] = collect($options->translations)->toArray();

                $data = [];
                $data['parameterTemplateChoosed']= $parameterTemplateChoosed;
                $data['parameterTypes']= $parameterTypes;
                $data['parameterType']= $parameterTypesList;
                $data['parameterCounter']= $request->parameter_id;
                $data['languages']= $languages;
                $data['translations']= $translations;
                $data['translationsOptions']= $translationsOptions;

                return view('private.cbs.wizard.parameter', $data);
            }
            $actionType = $request->action_type;
            $show = ($actionType == 'show' ? true : false);
            $parameterId = $request->parameter_id;
            if(is_null($parameterId)){
                return 'false';
            }
            $languages = Orchestrator::getLanguageList();
            $parameterTypesList = CB::getParametersTypes();

            $parameterTypes = collect($parameterTypesList)->pluck('name','code')->toArray();

            /** Parameter to update need to be in Cache */
//            if(!Cache::has('mp_cb_parameter_'.$operatorKey)){
//                return 'false';
//            }
//            $parameter = Cache::get('mp_cb_parameter_'.$operatorKey)[$parameterId] ?? null;
//            if(empty($parameter)){
//                return 'false';
//            }
            $translations = $parameter['translations'];

            $data = [];
            $data['parameter']= $parameter;
            $data['parameterTypes']= $parameterTypes;
            $data['parameterType']= $parameterTypesList;
            $data['parameterCounter']= $parameterId;
            $data['languages']= $languages;
            $data['translations']= $translations;
            $data['show']= $show;

            return view('private.cbs.wizard.parameter', $data);
        } catch (Exception $e) {
            return 'false';
        }
    }

    /**
     * Add a new modal parameter.
     * @param Request $request
     * @return $this|View
     */
    public function addModalParameter(Request $request)
    {
        try {
            if(!empty( $request->get('parameterCounter'))){
                $parameterCounter = $request->get('parameterCounter');
            } else{
                $parameterCounter = 0;
            }

            $data = [];
            $data["parameterType"] = CB::getParametersTypes();
            $data["parameterCounter"] = $parameterCounter;

            return view('private.cbs.wizard.modalParameter', $data);
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["cbs.create" => $e->getMessage()]);
        }
    }

    /**
     * Add a new modal parameter.
     * @param Request $request
     * @return $this|View
     */
    public function addModalVote(Request $request)
    {
        try {
            if(!empty( $request->get('voteCounter'))){
                $voteCounter = $request->get('voteCounter');
            } else{
                $voteCounter = 0;
            }

            $methodGroup = Vote::getListMethodGroups();

            $data = [];
            $data["methodGroup"] = $methodGroup;
            $data["voteCounter"] = $voteCounter;

            return view('private.cbs.wizard.modalVote', $data);
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["cbs.create" => $e->getMessage()]);
        }
    }

    /**
     * Add a new parameter.
     * @param Request $request
     * @return $this|View
     */
    public function addParameter(Request $request)
    {
        try {
            $languages = Orchestrator::getLanguageList();
            $uploadKey = Files::getUploadKey();

            if(!empty( $request->get('parameterCounter'))){
                $parameterCounter = $request->get('parameterCounter');
            } else{
                $parameterCounter = 0;
            }

            $allowFiles = ['images'];

            $data = [];
            $data['allowFiles'] = $allowFiles;
            $data['uploadKey'] = $uploadKey;
            $data["parameterType"] = CB::getParametersTypes();
            $data["parameterCounter"] = $parameterCounter;
            $data["languages"] = $languages;

            return view('private.cbs.wizard.parameter', $data);
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["cbs.create" => $e->getMessage()]);
        }
    }

    /**
     * Add a new parameter template selection.
     * @param Request $request
     * @return $this|View
     */
    public function addParameterTemplateSelection(Request $request)
    {
        try {
            if(!empty( $request->get('parameterCounter'))){
                $parameterCounter = $request->get('parameterCounter');
            } else{
                $parameterCounter = 0;
            }

            // Parameter Templates
            $parameterTemplatesKeys = Orchestrator::getParametersTemplatesKeys();
            $array = [];
            foreach($parameterTemplatesKeys as $tmp){
                $array[] = $tmp->parameter_template_key;
            }
            $parameterTemplates  = CB::getParametersTemplates($array);

            $data = [];
            $data["parameterType"] = CB::getParametersTypes();
            $data["parameterCounter"] = $parameterCounter;
            $data["parameterTemplates"] = $parameterTemplates;

            return view('private.cbs.wizard.parameterTemplateSelection', $data);
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["cbs.create" => $e->getMessage()]);
        }
    }

    /**
     * Add a new parameter template.
     * @param Request $request
     * @return $this|View
     */
    public function addParameterTemplate(Request $request)
    {
        try {
            $parameterTemplatesKeys = Orchestrator::getParametersTemplatesKeys();
            $array = [];
            foreach($parameterTemplatesKeys as $tmp){
                $array[] = $tmp->parameter_template_key;
            }
            $parameterTemplates  = CB::getParametersTemplates($array);

            $parameterType = CB::getParametersTypes();
            $uploadKey = Files::getUploadKey();

            $templateId = "";

            if(isset($request->template)){
                $templateId = $request->template;
            }

            $imageMapFile = null;
            $parameterTemplateChoosed = null;
            if(isset($request->template)){
                foreach($parameterTemplates as $tmp){
                    if($request->template == $tmp->id)
                        $parameterTemplateChoosed = $tmp;
                }
                if(!empty($parameterTemplateChoosed->code) && $parameterTemplateChoosed->code == "image_map"){
                    $imageMapFile = Files::getFile($parameterTemplateChoosed->value);
                }
            }

            $data = [];
            $data["parameterCounter"] = !empty( $request->get('parameterCounter')) ? $request->get('parameterCounter') : 0 ;;
            $data["template"] = !empty($request->get('template'))? $request->get('template') : null;
            $data["parameterType"] = $parameterType;
            $data["parameterTemplateChoosed"] = $parameterTemplateChoosed;

            return view('private.cbs.wizard.parameter', $data);
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["cbs.create" => $e->getMessage()]);
        }
    }

    /**
     * Add a new vote.
     * @param Request $request
     * @return $this|View
     */
    public function addVote(Request $request)
    {
        try {
            if(!empty( $request->get('voteCounter'))){
                $voteCounter = $request->get('voteCounter');
            } else{
                $voteCounter = 0;
            }

            $methodGroup = Vote::getListMethodGroups();

            $data = [];
            $data["voteCounter"] = $voteCounter;
            $data["methodGroup"] = $methodGroup;

            return view('private.cbs.wizard.vote', $data);
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["cbs.create" => $e->getMessage()]);
        }
    }


    /** Show view with vote analysis by cb
     * @param $type
     * @param $cbKey
     * @return \Illuminate\Http\RedirectResponse|View
     */
    public function voteAnalysis(Request $request,$type, $cbKey)
    {
        try {
            $statisticsType = 'total_votes2';

            if(!empty($request->statistics_type)){
                $statisticsType = $request->statistics_type;
            }
            $title = trans('privateCbs.cb_vote_analysis');
            $data = [];
            $data['title'] = $title;
            $data['type'] = $type;
            $data['cbKey'] = $cbKey;
            $data['statisticsType'] = $statisticsType;
            $voteEventObj = null;

            $voteEventsList = CB::getCbVotes($cbKey);

            $voteEvents = collect($voteEventsList)->pluck('name','vote_key')->toArray();

            $voteEventKey = null;
            Session::put('sidebarArguments', ['type' => $type, 'cbKey' => $cbKey, 'activeFirstMenu' => 'voteAnalysis2']);
            Session::put('sidebars', [0 => 'private', 1=> 'padsType']);

            $data['sidebar'] = 'padsType';
            $data['active'] = 'voteAnalysis2';
            if(count($voteEvents) == 0){

                return view('private.cbs.cbVoteAnalysis.withoutVoteEvent', $data);
            }elseif(count($voteEvents) == 1){
                $voteEventKey = reset($voteEventsList)->vote_key;
                $data["voteEventKey"] = $voteEventKey;
            }else{
                $data["voteEvents"] = $voteEvents;
            }

            if($voteEventKey == null){
                $voteEventKey = Session::get("voteEventKey");
                $data['voteEventKey'] = $voteEventKey;
            }

            $result = Vote::getAllShowEvents($voteEventKey);
            if(!empty($result[0])){
                $voteEventObj = $result[0];
            }

            $view = '';
            switch ($statisticsType){
                case 'data_vote_analysis_by_channel':
                    $channels = ['kiosk','pc','mobile','tablet','other','in_person','sms'];
                    $data["channels"] = $channels;

                    $statisticsByChannel= Analytics::getVoteStatisticsByChannel($voteEventKey);
                    $data["votesByChannel"] = $statisticsByChannel->votes_by_channel ?? [];
                    $data["countByChannel"] = $statisticsByChannel->count_by_channel ?? [];
                    $data["firstByChannel"] = $statisticsByChannel->first_by_channel ?? [];
                    $data["secondByChannel"] = $statisticsByChannel->second_by_channel ?? [];

                    $statsOptions["view_submitted"] = 1;
                    $statisticsByChannel= Analytics::getVoteStatisticsByChannel($voteEventKey, $statsOptions);
                    $data["votesByChannelSubmitted"] = $statisticsByChannel->votes_by_channel ?? [];
                    $data["countByChannelSubmitted"] = $statisticsByChannel->count_by_channel ?? [];
                    $data["firstByChannelSubmitted"] = $statisticsByChannel->first_by_channel ?? [];
                    $data["secondByChannelSubmitted"] = $statisticsByChannel->second_by_channel ?? [];
                    $data["viewSubmitted"] = $statsOptions["view_submitted"];

                    $view = 'private.cbs.cbVoteAnalysis.dataVoteAnalysisByChannel';
                    break;
                case 'votes_by_date':
                    if(count($voteEvents) == 1) {
                        $voteAnalysisByDate = Analytics::getVoteStatisticsByDate($voteEventKey);
                        $data["votesByDate"] = $voteAnalysisByDate;
                    }
                    $view = 'private.cbs.cbVoteAnalysis.voteAnalysisDate';
                    break;
                case 'votes_by_parameter':
                    /** @collection $entityUserParameters - Get entity user parameters and filter by type */
                    $entityUserParameters = collect(Orchestrator::getEntityRegisterParameters())->filter(function($parameter){
                        return ($parameter->parameter_type->code == 'birthday' ||
                            $parameter->parameter_type->code == 'gender' ||
                            $parameter->parameter_type->code == 'check_box' ||
                            $parameter->parameter_type->code == 'radio_buttons' ||
                            $parameter->parameter_type->code == 'dropdown' ||
                            $parameter->parameter_type->code == 'neighborhood');
                    });
                    $ageValue = 90;
                    $data["ageInterval"] = '+'.$ageValue;
                    $neighborhoodKey = null;
                    if($entityUserParameters->keyBy('parameter_type.code')->has('neighborhood')){
                        $neighborhoodParam = $entityUserParameters->keyBy('parameter_type.code')->get('neighborhood');
                        $neighborhoodKey = $neighborhoodParam->parameter_user_type_key;
                        $neighborhoodName = $neighborhoodParam->name ?? '';
                        $data["secondParameterName"] = $neighborhoodName;
                    }
                    $genderKey = null;
                    if($entityUserParameters->keyBy('parameter_type.code')->has('gender')){
                        $genderParam = $entityUserParameters->keyBy('parameter_type.code')->get('gender');
                        $genderKey = $genderParam->parameter_user_type_key;
                        $genderName = $genderParam->name ?? '';
                        $data["thirdParameterName"] = $genderName;
                    }

                    $userParameters = $entityUserParameters->pluck('name','parameter_user_type_key');
                    $data["userParameters"] = $userParameters->toArray();

                    if(count($voteEvents) == 1) {
                        $firstParameterKey = collect($userParameters)->keys()->first();
                        $parameterName = $userParameters->get($firstParameterKey);
                        $parameterCode = $entityUserParameters->keyBy('parameter_user_type_key')->get($firstParameterKey)->parameter_type->code;

                        if($parameterCode == 'neighborhood'){
                            $neighborhoodKey = null;
                            $genderKey = null;
                        }

                        $statisticsByParameter = Analytics::getVoteStatisticsByParameter($voteEventKey,$firstParameterKey,$neighborhoodKey,$genderKey,$ageValue);
                        $data["votesByParameter"] = $statisticsByParameter->statistics_by_parameter;
                        $data["votesByTopicParameter"] = $statisticsByParameter->statistics_by_topic ?? [];
                        $data["countByParameter"] = $statisticsByParameter->count_by_parameter ?? [];
                        $data["firstByParameter"] = $statisticsByParameter->first_by_parameter ?? [];
                        $data["secondByParameter"] = $statisticsByParameter->second_by_parameter ?? [];
                        $data["parametersOptions"] = $statisticsByParameter->parameters_options ?? [];

                        // Statistics by age and two params
                        $data["statisticsByAgeTwoParams"] = $statisticsByParameter->statistics_by_age_two_params ?? [];
                        $data["secondParametersOptions"] = $statisticsByParameter->second_parameters_options ?? [];
                        $data["thirdParametersOptions"] = $statisticsByParameter->third_parameters_options ?? [];

                        $data["commutersStatistics"] = $statisticsByParameter->commuters_statistics ?? [];

                        $data["votePopulation"] = $statisticsByParameter->vote_population ?? [];
                        $data["votePopulationTwoParameters"] = $statisticsByParameter->vote_population_two_parameters ?? [];
                        $data["parameterKey"] = $firstParameterKey;
                        $data["parameterName"] = $parameterName;
                        $data["parameterCode"] = $parameterCode;
                    }
                    $view = 'private.cbs.cbVoteAnalysis.voteAnalysisParameter';
                    break;
                case 'top_votes':
                    if(count($voteEvents) == 1) {
                        $statisticsByTop = Analytics::getVoteStatisticsTop($voteEventKey,10);
                        $data["votesByTop"] = $statisticsByTop;
                    }
                    $view = 'private.cbs.cbVoteAnalysis.voteAnalysisTop';
                    break;
                case 'total_votes':
                    if(count($voteEvents) == 1) {
                        $top = 8;
                        $statsOptions["top"] = $top;
                        $statsOptions["view_submitted"] = !empty($request->view_submitted) ? $request->view_submitted: 0;
                        $statisticsTotal = Analytics::getVoteStatisticsTotal($voteEventKey,$statsOptions);
                        $statisticsTotalData = $statisticsTotal->data ?? [];
                        $statisticsTotalSummary = $statisticsTotal->summary ?? [];

                        $data["statisticsTotalData"] = $statisticsTotalData;
                        $data["statisticsTotalSummary"] = $statisticsTotalSummary;
                    }
                    $view = 'private.cbs.cbVoteAnalysis.voteAnalysisTotal';
                    break;
                case 'voters_per_day':
                    if(count($voteEvents) == 1) {
                        $votersPerDate = Analytics::getVoteStatisticsVotersPerDate($voteEventKey);
                        $data["votersPerDate"] = $votersPerDate;
                    }
                    $view = 'private.cbs.cbVoteAnalysis.voteAnalysisVotersPerDay';
                    break;
                case 'votes_summary':
                    if(count($voteEvents)==1){
                        $field = $request->field;
                        $statsOptions["view_submitted"] = !empty($request["view_submitted"]) ? $request["view_submitted"]: 0;
                        $statisticsTotal = Analytics::getVoteStatisticsVotesSummary($voteEventKey,$statsOptions);
                        $statisticsTotalData = $statisticsTotal->data ?? [];
                        $data["statisticsTotalData"] = $statisticsTotalData;
                        $data["viewSubmitted"] = $statsOptions["view_submitted"];
                    }
                    $view = 'private.cbs.cbVoteAnalysis.voteAnalysisVotesSummary';
                    break;
                case 'votes_topic_parameters':
                    $parameters = CB::getCbParametersOptions($cbKey); //get parameters
                    $test= CB::getParametersTypes();
                    $parametersWithOptions = [];
                    //parameters where option in parameter_type like 1
                    foreach (!empty($test) ? $test : [] as $item){
                        if($item->options == 1){
                            $parametersWithOptions[] = $item;
                        }
                    }
                    $parametersFiltered = [];
                    $aux = [];
                    // filter parameters
                    foreach (!empty($parameters->parameters) ? $parameters->parameters : []  as $parameter){
                        foreach (!empty($parametersWithOptions) ? $parametersWithOptions : [] as $parametersWithOption){
                            if($parameter->parameter_type_id == $parametersWithOption->id){
                                $parametersFiltered []  = $parameter->code;
                                $aux [] = $parameter;
                            }
                        }
                    }
                    if(!empty($voteEvents) && count($voteEvents) == 1){
                        $firstParameter = collect($aux)->first();
                        $firstParameterId = $firstParameter->id;
                        $topicParameters = Analytics::getVoteStatisticsTopicParameters($voteEventKey,$firstParameterId);
                        $data['topicParameters'] = $topicParameters;
                        $data["parameterKey"] = 0;
                    }
                    $data['parametersFiltered'] = $parametersFiltered;
                    $view = 'private.cbs.cbVoteAnalysis.voteAnalysisTopicParameters';
                    break;
                // Analytics v2.0
                case 'votes_by_date2':
                    $startDate  = $request->start_date;
                    $endDate    = $request->end_date;
                    $statsOptions["start_date"] = $startDate;
                    $statsOptions["end_date"] = $endDate;
                    $statsOptions["view_submitted"] = !empty($request->view_submitted) ? $request->view_submitted: 0;
                    if(count($voteEvents) == 1) {
                        $voteAnalysisByDate = Analytics::getVoteStatisticsByDateRange($voteEventKey, $statsOptions);
                        $data["votesByDate"] = $voteAnalysisByDate;
                    }
                    /*
                    $response = CB::getCBAndTopics($cbKey);
                    $topics = $response->topics;
                    $data["topics"] = $topics;
                    */
                    $response = CB::getTopicsList($cbKey);
                    $topics = $response->data;
                    $data["topics"] = $topics;
                    $view = 'private.cbs.cbVoteAnalysis2.voteAnalysisDate';
                    break;
                case 'total_votes2':
                    if(count($voteEvents) == 1) {
                        $top = 10;
                        $statsOptions["top"] = $top;
                        $statsOptions["view_submitted"] = !empty($request->view_submitted) ? $request->view_submitted: 0;
                        $statisticsTotal = Analytics::getVoteStatisticsTotal($voteEventKey,$statsOptions);
                        $statisticsTotalData = $statisticsTotal->data ?? [];
                        $statisticsTotalSummary = $statisticsTotal->summary ?? [];
                        $data["statisticsTotalData"] = $statisticsTotalData;
                        $data["statisticsTotalSummary"] = $statisticsTotalSummary;
                        $data["top"] = $top;
                        $data["viewSubmitted"] = $statsOptions["view_submitted"];
                    }
                    $view = 'private.cbs.cbVoteAnalysis2.voteAnalysisTotal';
                    break;
                case 'total_votes_detail2':
                    if(count($voteEvents) == 1) {
                        $top = null;
                        $statsOptions["top"] = $top;
                        $statsOptions["view_submitted"] = !empty($request->view_submitted) ? $request->view_submitted: 0;
                        $statisticsTotal = Analytics::getVoteStatisticsTotal($voteEventKey,$statsOptions);
                        $statisticsTotalData = $statisticsTotal->data ?? [];
                        $statisticsTotalSummary = $statisticsTotal->summary ?? [];
                        $data["statisticsTotalData"] = $statisticsTotalData;
                        $data["statisticsTotalSummary"] = $statisticsTotalSummary;
                        $data["top"] = $top;
                        $data["viewSubmitted"] = $statsOptions["view_submitted"];
                    }
                    $view = 'private.cbs.cbVoteAnalysis2.voteAnalysisTotalDetail';
                    break;
                case 'votes_by_user_parameters2':
                    /** @collection $entityUserParameters - Get entity user parameters and filter by type */
                    $entityUserParameters = collect(Orchestrator::getEntityRegisterParameters())->filter(function($parameter){
                        return ($parameter->parameter_type->code == 'birthday' ||
                            $parameter->parameter_type->code == 'gender' ||
                            $parameter->parameter_type->code == 'check_box' ||
                            $parameter->parameter_type->code == 'radio_buttons' ||
                            $parameter->parameter_type->code == 'dropdown' ||
                            $parameter->parameter_type->code == 'neighborhood' ||
                            $parameter->parameter_type->code == 'google_maps'
                        );
                    });


                    $ageValue = 90;
                    $data["ageInterval"] = '+'.$ageValue;

                    /*
                    $neighborhoodKey = null;
                    if($entityUserParameters->keyBy('parameter_type.code')->has('neighborhood')){
                        $neighborhoodParam = $entityUserParameters->keyBy('parameter_type.code')->get('neighborhood');
                        $neighborhoodKey = $neighborhoodParam->parameter_user_type_key;
                        $neighborhoodName = $neighborhoodParam->name ?? '';
                        $data["secondParameterName"] = $neighborhoodName;
                    }
                    $genderKey = null;
                    if($entityUserParameters->keyBy('parameter_type.code')->has('gender')){
                        $genderParam = $entityUserParameters->keyBy('parameter_type.code')->get('gender');
                        $genderKey = $genderParam->parameter_user_type_key;
                        $genderName = $genderParam->name ?? '';
                        $data["thirdParameterName"] = $genderName;
                    }
                    */

                    $userParameters = $entityUserParameters->pluck('name','parameter_user_type_key');
                    $data["userParameters"] = $userParameters->toArray();

                    /*
                    if(count($voteEvents) == 1) {
                        $firstParameterKey = collect($userParameters)->keys()->first();
                        $parameterName = $userParameters->get($firstParameterKey);
                        $parameterCode = $entityUserParameters->keyBy('parameter_user_type_key')->get($firstParameterKey)->parameter_type->code;

                        if($parameterCode == 'neighborhood'){
                            $neighborhoodKey = null;
                            $genderKey = null;
                        }

                        $statisticsByParameter = Analytics::getVoteStatisticsByParameter($voteEventKey,$firstParameterKey,$neighborhoodKey,$genderKey,$ageValue);
                        $data["votesByParameter"] = $statisticsByParameter->statistics_by_parameter;
                        $data["votesByTopicParameter"] = $statisticsByParameter->statistics_by_topic ?? [];
                        $data["countByParameter"] = $statisticsByParameter->count_by_parameter ?? [];
                        $data["firstByParameter"] = $statisticsByParameter->first_by_parameter ?? [];
                        $data["secondByParameter"] = $statisticsByParameter->second_by_parameter ?? [];
                        $data["parametersOptions"] = $statisticsByParameter->parameters_options ?? [];

                        // Statistics by age and two params
                        $data["statisticsByAgeTwoParams"] = $statisticsByParameter->statistics_by_age_two_params ?? [];
                        $data["secondParametersOptions"] = $statisticsByParameter->second_parameters_options ?? [];
                        $data["thirdParametersOptions"] = $statisticsByParameter->third_parameters_options ?? [];

                        $data["commutersStatistics"] = $statisticsByParameter->commuters_statistics ?? [];

                        $data["votePopulation"] = $statisticsByParameter->vote_population ?? [];
                        $data["votePopulationTwoParameters"] = $statisticsByParameter->vote_population_two_parameters ?? [];
                        $data["parameterKey"] = $firstParameterKey;
                        $data["parameterName"] = $parameterName;
                        $data["parameterCode"] = $parameterCode;
                    }
                    */
                    $view = 'private.cbs.cbVoteAnalysis2.voteAnalysisUserParameters';
                    break;
                case 'votes_by_topic_parameters2':
                    $parameters = CB::getCbParametersOptions($cbKey); //get parameters
                    $parametersTypes = CB::getParametersTypes();
                    $parametersWithOptions = [];
                    //parameters where option in parameter_type like 1
                    foreach (!empty($parametersTypes) ? $parametersTypes : [] as $parametersType){
                        if($parametersType->options == 1 || $parametersType->code == "google_maps"){
                            $parametersWithOptions[] = $parametersType;
                        }
                    }
                    $parametersFiltered = [];
                    $aux = [];
                    // filter parameters
                    foreach (!empty($parameters->parameters) ? $parameters->parameters : []  as $parameter){
                        foreach (!empty($parametersWithOptions) ? $parametersWithOptions : [] as $parametersWithOption){
                            if($parameter->parameter_type_id == $parametersWithOption->id){
                                $parametersFiltered[$parameter->id]  = $parameter->parameter;
                                $aux [] = $parameter;
                            }
                        }
                    }

                    /*
                    if(!empty($voteEvents) && count($voteEvents) == 1){
                        $firstParameter = collect($aux)->first();
                        $firstParameterId = $firstParameter->id;
                        $topicParameters = Analytics::getVoteStatisticsTopicParameters($voteEventKey,$firstParameterId);
                        $data['topicParameters'] = $topicParameters;
                    }
                    */

                    $data["voteEventKey"] = $voteEventKey;
                    $data['parametersFiltered'] = $parametersFiltered;

                    $view = 'private.cbs.cbVoteAnalysis2.voteAnalysisTopicParameters';
                    break;
            }

            Session::put('sidebarArguments', ['type' => $type, 'cbKey' => $cbKey, 'activeFirstMenu' => 'voteAnalysis'/*, 'topicKey' => $topicKey*/]);
            Session::put('sidebarArguments.activeSecondMenu', $statisticsType);

            $cb = CB::getCbConfigurations($cbKey);

            $data['voteEventObj'] = $voteEventObj;
            $data['cb'] = $cb;
            $data['sidebar'] = 'voteAnalysis';
            $data['active'] = $statisticsType;
            return view($view, $data);

        }catch(Exception $e){
            return redirect()->back()->withErrors([trans("privateCbsVote.vote_statistics_error") => $e->getMessage()]);
        }
    }


    /** Get vote Analysis
     * @param Request $request
     * @return string|View
     */
    public function getVoteAnalysis(Request $request)
    {
        try {
            $cbKey = $request->cb_key;
            $statisticsType = $request->statistics_type;
            $voteEventKey = $request->vote_event_key;
            $parameterKey = $request->parameter_key;
            $parameterId = $request->parameter_id;
            $options["start_date"]  = $request->start_date;
            $options["end_date"]  = $request->end_date;
            $options["total"]  = $request->total;
            $options["positive"]  = $request->positive;
            $options["negative"]  = $request->negative;
            $options["balance"]  = $request->balance;
            $options["topic_key"]  = $request->topic_key;
            $options["view_submitted"]  = $request->view_submitted;

            $data = [];
            $view = '';
            $voteEventObj = null;

            Session::put("voteEventKey", $voteEventKey);

            $result = Vote::getAllShowEvents($voteEventKey);
            if(!empty($result[0])){
                $voteEventObj = $result[0];
            }
            $voteEventStatus = Vote::getVoteStatus($voteEventKey);
            Session::put("voteEventStatus", $voteEventStatus);

            if(empty($statisticsType) || empty($voteEventKey)){
                return 'false';
            }

            switch ($statisticsType){
                case 'vote_analysis_date':
                    $votesByDate = Analytics::getVoteStatisticsByDate($voteEventKey);
                    $data["votesByDate"] = $votesByDate;
                    $view = 'private.cbs.cbVoteAnalysis.voteAnalysisByDate';
                    break;
                case 'vote_analysis_last_day':
                    $votesLastDay = Analytics::getVoteStatisticsLastDay($voteEventKey);
                    $data["votesLastDay"] = $votesLastDay;
                    $view = 'private.cbs.cbVoteAnalysis.voteAnalysisLastDay';
                    break;
                case 'vote_analysis_top_ten':
                    $statisticsByTop = Analytics::getVoteStatisticsTop($voteEventKey,10);
                    $data["votesByTop"] = $statisticsByTop;
                    $view = 'private.cbs.cbVoteAnalysis.voteAnalysisByTop';
                    break;
                case 'vote_analysis_top_tree_by_date':
                    $statisticsTopByDate = Analytics::getVoteStatisticsTopByDay($voteEventKey,3);
                    $data["votesTopByDate"] = $statisticsTopByDate;
                    $view = 'private.cbs.cbVoteAnalysis.voteAnalysisTopByDate';
                    break;
                case 'vote_analysis_by_parameter':
                    /** @collection $userParameters - Get entity user parameters */
                    $userParameters = collect(Orchestrator::getEntityRegisterParameters());
                    $userParametersByKey = $userParameters->keyBy('parameter_user_type_key');

                    if($userParametersByKey->count()!= 0) {
                        $parameterName = $userParametersByKey->get($parameterKey)->name;
                        $parameterCode = $userParametersByKey->get($parameterKey)->parameter_type->code;

                        $neighborhoodKey = null;
                        if ($parameterCode != 'neighborhood' && $userParameters->keyBy('parameter_type.code')->has('neighborhood')) {
                            $neighborhoodParam = $userParameters->keyBy('parameter_type.code')->get('neighborhood');
                            $neighborhoodKey = $neighborhoodParam->parameter_user_type_key;
                            $neighborhoodName = $neighborhoodParam->name ?? '';
                            $data["secondParameterName"] = $neighborhoodName;
                        }

                        $genderKey = null;
                        if ($parameterCode != 'gender' && $userParameters->keyBy('parameter_type.code')->has('gender')) {
                            $genderParam = $userParameters->keyBy('parameter_type.code')->get('gender');
                            $genderKey = $genderParam->parameter_user_type_key;
                            $genderName = $genderParam->name ?? '';
                            $data["thirdParameterName"] = $genderName;
                        }

                        $ageValue = 90;
                        $data["ageInterval"] = '+'.$ageValue;
                        $statisticsByParameter = Analytics::getVoteStatisticsByParameter($voteEventKey,$parameterKey,$neighborhoodKey,$genderKey,$ageValue);

                        $data["votesByParameter"] = $statisticsByParameter->statistics_by_parameter;
                        $data["votesByTopicParameter"] = $statisticsByParameter->statistics_by_topic ?? [];
                        $data["countByParameter"] = $statisticsByParameter->count_by_parameter ?? [];
                        $data["firstByParameter"] = $statisticsByParameter->first_by_parameter ?? [];
                        $data["secondByParameter"] = $statisticsByParameter->second_by_parameter ?? [];
                        $data["parametersOptions"] = $statisticsByParameter->parameters_options;

                        // Statistics by age and two params
                        $data["statisticsByAgeTwoParams"] = $statisticsByParameter->statistics_by_age_two_params ?? [];
                        $data["secondParametersOptions"] = $statisticsByParameter->second_parameters_options ?? [];
                        $data["thirdParametersOptions"] = $statisticsByParameter->third_parameters_options ?? [];

                        $data["commutersStatistics"] = $statisticsByParameter->commuters_statistics ?? [];

                        $data["votePopulation"] = $statisticsByParameter->vote_population ?? [];
                        $data["votePopulationTwoParameters"] = $statisticsByParameter->vote_population_two_parameters ?? [];
                        $data["parameterName"] = $parameterName;
                        $data["parameterCode"] = $parameterCode;
                        $data["parameterKey"] = $parameterKey;
                    }
                    $view = 'private.cbs.cbVoteAnalysis.voteAnalysisByParameter';
                    break;
                case 'vote_analysis_total':
                    $top = 8;
                    $statsOptions["top"] = $top;
                    $statsOptions["view_submitted"] = !empty($request->view_submitted) ? $request->view_submitted: 0;
                    $statisticsTotal = Analytics::getVoteStatisticsTotal($voteEventKey,$statsOptions);
                    $statisticsTotalData = $statisticsTotal->data ?? [];
                    $statisticsTotalSummary = $statisticsTotal->summary ?? [];

                    $data["statisticsTotalData"] = $statisticsTotalData;
                    $data["statisticsTotalSummary"] = $statisticsTotalSummary;
                    $view = 'private.cbs.cbVoteAnalysis.voteAnalysisByTotal';
                    break;
                case 'vote_analysis_by_channel':
                    $statisticsByChannel= Analytics::getVoteStatisticsByChannel($voteEventKey);
                    $channels = ['kiosk','pc','mobile','tablet','other','in_person','sms'];

                    $data["votesByChannel"] = $statisticsByChannel->votes_by_channel ?? [];
                    $data["countByChannel"] = $statisticsByChannel->count_by_channel ?? [];
                    $data["firstByChannel"] = $statisticsByChannel->first_by_channel ?? [];
                    $data["secondByChannel"] = $statisticsByChannel->second_by_channel ?? [];
                    $data["channels"] = $channels;
                    $view = 'private.cbs.cbVoteAnalysis.voteAnalysisByChannel';
                    break;
                case 'voter_analysis_by_channel':
                    $statisticsByChannel= Analytics::getVoterStatisticsByChannel($voteEventKey);
                    $channels = ['kiosk','pc','mobile','tablet','other','in_person'];

                    $data["votesByChannel"] = $statisticsByChannel->votes_by_channel ?? [];
                    $data["countByChannel"] = $statisticsByChannel->count_by_channel ?? [];
                    $data["channels"] = $channels;
                    $view = 'private.cbs.cbVoteAnalysis.voterAnalysisByChannel';
                    break;
                case 'voters_per_day':
                    $votersPerDate = Analytics::getVoteStatisticsVotersPerDate($voteEventKey);
                    $data["votersPerDate"] = $votersPerDate;
                    $data["type"] = $statisticsType;
                    $data["cbKey"] = $cbKey;
                    $view = 'private.cbs.cbVoteAnalysis.voteAnalysisVotersPerDayTab';
                    break;
                case 'vote_analysis_voters_per_day':
                    $votersPerDate = Analytics::getVoteStatisticsVotersPerDate($voteEventKey);
                    $data["votersPerDate"] = $votersPerDate;
                    $view = 'private.cbs.cbVoteAnalysis.voteAnalysisVotersPerDay';
                    break;
                case 'vote_analysis_topic_parameters':
                    $parameters = CB::getCbParametersOptions($cbKey); //get parameters
                    $test = CB::getParametersTypes();
                    $parametersWithOptions = [];
                    //parameters where option in parameter_type like 1
                    foreach (!empty($test) ? $test : [] as $item){
                        if($item->options == 1){
                            $parametersWithOptions[] = $item;
                        }
                    }
                    $parametersFiltered = [];
                    // filter parameters
                    foreach (!empty($parameters->parameters) ? $parameters->parameters : []  as $parameter){
                        foreach (!empty($parametersWithOptions) ? $parametersWithOptions : [] as $parametersWithOption){
                            if($parameter->parameter_type_id == $parametersWithOption->id){
                                $parametersFiltered []  = $parameter->code;
                                $aux [] = $parameter;
                            }
                        }
                    }

                    $topicParameters = Analytics::getVoteStatisticsTopicParameters($voteEventKey,$aux[$parameterKey]->id);

                    $data['parametersFiltered'] = $parametersFiltered;
                    $data['topicParameters'] = $topicParameters;
                    $data['parameterKey'] = $parameterKey;
                    $data['voteEventKey'] = $voteEventKey;
                    $view = 'private.cbs.cbVoteAnalysis.voteAnalysisByTopicParameters';
                    break;
                // Analytics v2.0
                    // votes_by_date2 - Tabs
                case 'vote_analysis_date2':
                    $statsOptions["view_submitted"] = !empty($request->view_submitted) ? $request->view_submitted: 0;
                    $statisticsTotal = Analytics::getVoteStatisticsTotal($voteEventKey, $statsOptions);
                    $statisticsTotalSummary = $statisticsTotal->summary ?? [];

                    $votesByDate = Analytics::getVoteStatisticsByDateRange($voteEventKey, $options);
                    $data["votesByDate"] = $votesByDate;
                    $data["statisticsTotalSummary"] = $statisticsTotalSummary;
                    $data["voteEventObj"] = $voteEventObj;
                    $data["voteEventKey"] = $voteEventKey;
                    $view = 'private.cbs.cbVoteAnalysis2.tabVoteAnalysisDate.voteAnalysisByDate';
                    break;
                case 'vote_analysis_hour2':
                    $votesByDate = Analytics::getVoteStatisticsByHour($voteEventKey, $options);
                    $data["votesByDate"] = $votesByDate;
                    $data["voteEventObj"] = $voteEventObj;
                    $data["voteEventKey"] = $voteEventKey;
                    $view = 'private.cbs.cbVoteAnalysis2.tabVoteAnalysisDate.voteAnalysisByHour';
                    break;
                case 'vote_analysis_total2':
                    $top = 10;
                    $statsOptions["top"] = $top;
                    $statsOptions["view_submitted"] = !empty($request->view_submitted) ? $request->view_submitted: 0;

                    $statisticsTotal = Analytics::getVoteStatisticsTotal($voteEventKey,$statsOptions);
                    $statisticsTotalData = $statisticsTotal->data ?? [];
                    $statisticsTotalSummary = $statisticsTotal->summary ?? [];
                    $data["statisticsTotalData"] = $statisticsTotalData;
                    $data["statisticsTotalSummary"] = $statisticsTotalSummary;
                    $data["top"] = $top;
                    $data["viewSubmitted"] = $statsOptions["view_submitted"];
                    $view = 'private.cbs.cbVoteAnalysis2.tabVoteAnalysisTotal.voteAnalysisByTotal';
                    break;
                case 'vote_analysis_by_channel2':
                    $top = 10;
                    $statsOptions["top"] = $top;
                    $statsOptions["view_submitted"] = !empty($request->view_submitted) ? $request->view_submitted: 0;
                    $statisticsTotal = Analytics::getVoteStatisticsTotal($voteEventKey,$statsOptions);
                    $statisticsTotalSummary = $statisticsTotal->summary ?? [];
                    $channels = ['kiosk','pc','mobile','tablet','other','in_person','sms'];
                    $statisticsTotal = Analytics::getVoteStatisticsTotalByChannel($voteEventKey,$statsOptions);
                    $data["statisticsTotalByChannel"] = $statisticsTotal;
                    //dd($statisticsTotal);
                    $data["statisticsTotalSummary"] = $statisticsTotalSummary;

                    $data["channels"] = $channels;
                    $data["top"] = $top;
                    $data["viewSubmitted"] = $statsOptions["view_submitted"];
                    $view = 'private.cbs.cbVoteAnalysis2.tabVoteAnalysisTotal.voteAnalysisByChannel';
                    break;
                case 'vote_analysis_total_detail2':
                    $statsOptions["view_submitted"] = !empty($request->view_submitted) ? $request->view_submitted: 0;
                    $statisticsTotal = Analytics::getVoteStatisticsTotal($voteEventKey, $statsOptions);
                    $statisticsTotalData = $statisticsTotal->data ?? [];
                    $statisticsTotalSummary = $statisticsTotal->summary ?? [];
                    $data["statisticsTotalData"] = $statisticsTotalData;
                    $data["statisticsTotalSummary"] = $statisticsTotalSummary;
                    $data["viewSubmitted"] = $statsOptions["view_submitted"];
                    $view = 'private.cbs.cbVoteAnalysis2.tabVoteAnalysisTotal.voteAnalysisByTotal';
                    break;
                case 'vote_analysis_by_channel_detail2':
                    $statsOptions["view_submitted"] = !empty($request->view_submitted) ? $request->view_submitted: 0;
                    $statisticsTotal = Analytics::getVoteStatisticsTotal($voteEventKey, $statsOptions);
                    $statisticsTotalSummary = $statisticsTotal->summary ?? [];
                    $channels = ['kiosk','pc','mobile','tablet','other','in_person','sms'];
                    $statsOptions["view_submitted"] = !empty($request->view_submitted) ? $request->view_submitted: 0;
                    $statisticsTotal = Analytics::getVoteStatisticsTotalByChannel($voteEventKey,$statsOptions);
                    $data["statisticsTotalByChannel"] = $statisticsTotal;
                    $data["statisticsTotalSummary"] = $statisticsTotalSummary;
                    $data["channels"] = $channels;
                    $data["viewSubmitted"] = $statsOptions["view_submitted"];
                    $view = 'private.cbs.cbVoteAnalysis2.tabVoteAnalysisTotal.voteAnalysisByChannel';
                    break;
                case 'vote_analysis_table_by_channel_detail2':
                    $statsOptions["view_submitted"] = !empty($request->view_submitted) ? $request->view_submitted: 0;
                    $statisticsByChannel= Analytics::getVoteStatisticsByChannel($voteEventKey,$statsOptions);
                    $channels = ['kiosk','pc','mobile','tablet','other','in_person','sms'];
                    $data["votesByChannel"] = $statisticsByChannel->votes_by_channel ?? [];
                    $data["countByChannel"] = $statisticsByChannel->count_by_channel ?? [];
                    $data["firstByChannel"] = $statisticsByChannel->first_by_channel ?? [];
                    $data["secondByChannel"] = $statisticsByChannel->second_by_channel ?? [];
                    $data["channels"] = $channels;
                    // dd($statisticsByChannel);
                    $data["viewSubmitted"] = $statsOptions["view_submitted"];

                    $view = 'private.cbs.cbVoteAnalysis2.tabVoteAnalysisTotal.voteAnalysisTableByChannel';
                    break;
                case 'votes_by_user_parameters2':
                    $viewSubmitted = !empty($request->view_submitted) ? $request->view_submitted: 0;
                    $ageValue = 90;
                    // Default view
                    $view = 'private.cbs.cbVoteAnalysis2.tabVoteAnalysisUserParameters.textfield';

                    /** @collection $userParameters - Get entity user parameters */
                    $userParameters = collect(Orchestrator::getEntityRegisterParameters());
                    $userParametersByKey = $userParameters->keyBy('parameter_user_type_key');

                    if($userParametersByKey->count()!= 0) {
                        $parameterName = $userParametersByKey->get($parameterKey)->name;
                        $parameterCode = $userParametersByKey->get($parameterKey)->parameter_type->code;

                        if( $parameterCode == "dropdown" || $parameterCode == "check_box" || $parameterCode == "radio_buttons"
                            || $parameterCode == "category" ||  $parameterCode == "budget"){
                            $neighborhoodKey = null;
                            if ($parameterCode != 'neighborhood' && $userParameters->keyBy('parameter_type.code')->has('neighborhood')) {
                                $neighborhoodParam = $userParameters->keyBy('parameter_type.code')->get('neighborhood');
                                $neighborhoodKey = $neighborhoodParam->parameter_user_type_key;
                                $neighborhoodName = $neighborhoodParam->name ?? '';
                                $data["secondParameterName"] = $neighborhoodName;
                            }

                            $genderKey = null;
                            if ($parameterCode != 'gender' && $userParameters->keyBy('parameter_type.code')->has('gender')) {
                                $genderParam = $userParameters->keyBy('parameter_type.code')->get('gender');
                                $genderKey = $genderParam->parameter_user_type_key;
                                $genderName = $genderParam->name ?? '';
                                $data["thirdParameterName"] = $genderName;
                            }


                            $options["start_date"]  = $request->start_date;
                            $options["end_date"]  = $request->end_date;
                            $votesByDate = Analytics::getVoteStatisticsByParameterChannelDateRange($voteEventKey, $parameterKey,
                                ["start_date" => !empty($request->start_date) ? $request->start_date : $voteEventObj->start_date,
                                    "end_date" => !empty($request->end_date) ? $request->end_date : $voteEventObj->end_date,
                                    'parameter_key' => $parameterKey,
                                    'view_submitted' => $viewSubmitted
                                ]);

                            $statisticsByParameterChannel= Analytics::getVoteStatisticsByParameterChannel($voteEventKey,$parameterKey,$neighborhoodKey,$genderKey,$ageValue,$viewSubmitted);
                            $data["statisticsByParameterChannel"] = $statisticsByParameterChannel;

                            $data["ageInterval"] = '+'.$ageValue;
                            $statisticsByParameter = Analytics::getVoteStatisticsByParameter($voteEventKey,$parameterKey,$neighborhoodKey,$genderKey,$ageValue,$viewSubmitted);

                            $data["votesByParameter"] = $statisticsByParameter->statistics_by_parameter;
                            $data["votesByTopicParameter"] = $statisticsByParameter->statistics_by_topic ?? [];
                            $data["countByParameter"] = $statisticsByParameter->count_by_parameter ?? [];
                            $data["firstByParameter"] = $statisticsByParameter->first_by_parameter ?? [];
                            $data["secondByParameter"] = $statisticsByParameter->second_by_parameter ?? [];
                            $data["parametersOptions"] = $statisticsByParameter->parameters_options;

                            // Statistics by age and two params
                            $data["statisticsByAgeTwoParams"] = $statisticsByParameter->statistics_by_age_two_params ?? [];
                            $data["secondParametersOptions"] = $statisticsByParameter->second_parameters_options ?? [];
                            $data["thirdParametersOptions"] = $statisticsByParameter->third_parameters_options ?? [];

                            $data["commutersStatistics"] = $statisticsByParameter->commuters_statistics ?? [];

                            $data["votePopulation"] = $statisticsByParameter->vote_population ?? [];
                            $data["votePopulationTwoParameters"] = $statisticsByParameter->vote_population_two_parameters ?? [];
                            $data["parameterName"] = $parameterName;
                            $data["parameterCode"] = $parameterCode;
                            $data["parameterKey"] = $parameterKey;

                            $data["votesByDate"] = $votesByDate;
                            $data["voteEventKey"] = $voteEventKey;
                            $view = 'private.cbs.cbVoteAnalysis2.tabVoteAnalysisUserParameters.itemList';
                        } else if( $parameterCode == "number" ){
                            $view = 'private.cbs.cbVoteAnalysis2.tabVoteAnalysisUserParameters.number';
                        } else if( $parameterCode == "google_maps" ){
                            $options = ["parameter_key" => $parameterKey, "view_submitted"=>  !empty($request->view_submitted) ? $request->view_submitted: 0];
                            $voteStatistics = Analytics::getVoteStatisticsByUser($voteEventKey, $options);
                            $geoLocations = $voteStatistics->data;
                            $data["parameterKey"] = $parameterKey;
                            $data["geoLocations"] = $geoLocations;
                            $view = 'private.cbs.cbVoteAnalysis2.tabVoteAnalysisUserParameters.geolocation';
                        }
                    }
                    break;
                case 'votes_by_topic_parameters2':
                    $viewSubmitted = !empty($request->view_submitted) ? $request->view_submitted: 0;
                    $data["viewSubmitted"] = $viewSubmitted;
                    $ageValue = 90;

                    // Default view
                    $view = 'private.cbs.cbVoteAnalysis2.tabVoteAnalysisUserParameters.itemList';

                    // Getting Parameter details
                    $param =  CB::getParameterOptions($parameterId);
                    $parameterCode = !empty($param->code) ? $param->code :"";

                    if( $parameterCode == "dropdown" || $parameterCode == "check_box" || $parameterCode == "radio_buttons"
                        || $parameterCode == "category" ||  $parameterCode == "budget" ){
                        $data["parameterName"] = !empty($param->parameter) ? $param->parameter : "";

                        $parameters = CB::getCbParametersOptions($cbKey); //get parameters
                        $parametersTypes = CB::getParametersTypes();
                        $parametersWithOptions = [];

                        //parameters where option in parameter_type like 1
                        foreach (!empty($parametersTypes) ? $parametersTypes : [] as $parametersType){
                            if($parametersType->options == 1){
                                $parametersWithOptions[] = $parametersType;
                            }
                        }
                        $parametersFiltered = [];
                        // filter parameters
                        foreach (!empty($parameters->parameters) ? $parameters->parameters : []  as $parameter){
                            foreach (!empty($parametersWithOptions) ? $parametersWithOptions : [] as $parametersWithOption){
                                if($parameter->parameter_type_id == $parametersWithOption->id){
                                    $parametersFiltered[$parameter->id] = $parameter->code;
                                    $aux [$parameter->id] = $parameter;
                                }
                            }
                        }

                        //  Vote Statistics By Topic Parameter
                        $neighborhoodKey  = null; // Please review this!?
                        $genderKey = null;        // Please review this!?
                        $statisticsByParameter = Analytics::getVoteStatisticsByTopicParameter($voteEventKey, $cbKey,$parameterId,$neighborhoodKey,$genderKey,$ageValue,$viewSubmitted);

                        // statistics By parameter channel  ------- -------
                        $statisticsByParameterChannel= Analytics::getVoteStatisticsByTopicParameterChannel($voteEventKey, $cbKey,$parameterId,$neighborhoodKey,$genderKey,$ageValue,$viewSubmitted);
                        $data["statisticsByParameterChannel"] = $statisticsByParameterChannel;
                        $votesByDate = Analytics::getVoteStatisticsByTopicParameterChannelDateRange($voteEventKey ,$parameterId,
                            ["start_date" => !empty($request->start_date) ? $request->start_date : $voteEventObj->start_date,
                                "end_date" => !empty($request->end_date) ? $request->end_date : $voteEventObj->end_date,
                                'parameter_id' => $parameterId,
                                'cb_key' =>$cbKey,
                                'view_submitted' => $viewSubmitted
                            ]);

                        // Data to send to view
                        $data["selectLineToView"] = $request->selectLineToView;
                        $data["votesByDate"] = $votesByDate;
                        $data["parameterId"] = $parameterId;
                        $data["voteEventKey"] = $voteEventKey;
                        $data["cbKey"] = $cbKey;
                        $data["votesByParameter"] = $statisticsByParameter->statistics_by_parameter;
                        $data["votesByTopicParameter"] = $statisticsByParameter->statistics_by_topic ?? [];
                        $data["parametersOptions"] = $statisticsByParameter->parameters_options;
                        $data["parameterCode"] = $parameterCode;
                        $data["ageInterval"] = '+'.$ageValue;
                        $view = 'private.cbs.cbVoteAnalysis2.tabVoteAnalysisTopicParameters.itemList';
                    } else if( $parameterCode == "number" ){
                        $view = 'private.cbs.cbVoteAnalysis2.tabVoteAnalysisTopicParameters.number';
                    } else if( $parameterCode == "google_maps" ){
                        //  Vote Statistics By Topic Parameter
                        // $statisticsByParameter = Analytics::getVoteGeoStats($voteEventKey, $cbKey, $parameterId);
                        $options = ["cb_key" => $cbKey , "parameter_id" => $parameterId, "view_submitted"=>  !empty($request->view_submitted) ? $request->view_submitted: 0];
                        $voteStatistics = Analytics::getVoteStatisticsByTopic($voteEventKey, $options);
                        $geoLocations = $voteStatistics->data;
                        $data["parameterId"] = $parameterId;
                        $data["geoLocations"] = $geoLocations;
                        $view = 'private.cbs.cbVoteAnalysis2.tabVoteAnalysisTopicParameters.geolocation';
                    }

                    break;
                case 'votes_by_user_parameters2_chartdates_only':
                    $ageValue = 90;
                    $options["start_date"]  = $request->start_date;
                    $options["end_date"]  = $request->end_date;
                    $votesByDate = Analytics::getVoteStatisticsByParameterChannelDateRange($voteEventKey, $parameterKey,
                        ["start_date" => !empty($request->start_date) ? $request->start_date : $voteEventObj->start_date,
                            "end_date" => !empty($request->end_date) ? $request->end_date : $voteEventObj->end_date,
                            'parameter_key' => $parameterKey,
                            "view_submitted"=>  !empty($request->view_submitted) ? $request->view_submitted: 0]);
                    $data["votesByDate"] = $votesByDate;
                    $data["parameterKey"] = $parameterKey;
                    $data["votesByDate"] = $votesByDate;
                    $data["voteEventKey"] = $voteEventKey;
                    $data["selectLineToView"] = $request->selectLineToView;


                    $view = 'private.cbs.cbVoteAnalysis2.tabVoteAnalysisUserParameters._requestDateChart';
                break;
                case 'votes_by_topic_parameters2_chartdates_only':
                    $ageValue = 90;
                    $options["start_date"]  = $request->start_date;
                    $options["end_date"]  = $request->end_date;
                    $votesByDate = Analytics::getVoteStatisticsByTopicParameterChannelDateRange($voteEventKey ,$parameterId,
                        ["start_date" => !empty($request->start_date) ? $request->start_date : $voteEventObj->start_date,
                            "end_date" => !empty($request->end_date) ? $request->end_date : $voteEventObj->end_date,
                            'parameter_id' => $parameterId,
                            'cb_key' =>$cbKey,
                            "view_submitted"=>  !empty($request->view_submitted) ? $request->view_submitted: 0
                        ]);

                    // dd($votesByDate);
                    $data["votesByDate"] = $votesByDate;
                    $data["parameterId"] = $parameterId;
                    $data["votesByDate"] = $votesByDate;
                    $data["voteEventKey"] = $voteEventKey;
                    $data["selectLineToView"] = $request->selectLineToView;


                    $view = 'private.cbs.cbVoteAnalysis2.tabVoteAnalysisTopicParameters._requestDateChart';
                    break;
            }

            if(empty($data)){
                return 'false';
            }

            $data['voteEventObj'] = $voteEventObj;
            return view($view, $data);

        }catch (Exception $e){
            return $e->getMessage();
        }
    }

    /**
     * @param Request $request
     * @param $type
     * @param $cbKey
     * @return string
     */
    public function getVotesSummaryTable(Request $request, $type, $cbKey){
        try{
            $voteEventKey = $request->vote_event_key;
            // $field = $request->field;
            // options: ["field" => $field]
            $statsOptions["view_submitted"] = !empty($request["view_submitted"]) ? $request["view_submitted"]: 0;
            $data["viewSubmitted"] = $statsOptions["view_submitted"];

            $statisticsTotal = Analytics::getVoteStatisticsVotesSummary($voteEventKey, $statsOptions);
            $statisticsTotalData = $statisticsTotal->data ?? [];
            $collection = Collection::make($statisticsTotalData);
            // dd($collection);
            return Datatables::of($collection)
                ->make(true);
        }catch(Exception $e){
            return $e->getMessage();
        }

    }

    /**
     * @param Request $request
     * @return array
     */
    public function getAllTemplates(Request $request){
        try{
            $listEntityCbTemplates = Orchestrator::getAllEntityCbTemplatesList();

            if(!empty($listEntityCbTemplates)){
                foreach($listEntityCbTemplates as $entityCbTemplate){
                    $names[] = [
                        'key' => $entityCbTemplate->cb_key,
                        'name' => $entityCbTemplate->name
                    ];

                }

                return ['data' => $names];
            }else{
                return ['data' => []];
            }
        } catch (Exception $e) {

        }
    }

    /**
     * @param Request $request
     * @param $type
     * @return string
     */
    public function getCbTemplate(Request $request, $type)
    {
        try{
            if($request->has('templateCbKey')){
                return action('CbsController@create', ['type' => $type, 'template_cb_key' => $request->templateCbKey]);
            }
        } catch (Exception $e) {

        }
    }

    /**
     * @param Request $request
     * @param $type
     * @param $cbKey
     * @return $this|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function voteAnalysisEmpaville(Request $request, $type, $cbKey)
    {
        try{
            $voteEvents = CB::getCbVotes($cbKey);

            if(empty($voteEvents[0])){
                //Page error no data
                throw new Exception("No Vote Events.");
            }
            $response = Analytics::getEmapvilleSchools($voteEvents, $cbKey);

            $voteSession = [];
            if((!empty($response->data) && (!empty($response->summary)) )){
                $voteSession["byProposal"] = $response->data;
                $voteSession["summary"] = $response->summary;

                // Prepare Data for Top Ten
                if(sizeof($voteSession["byProposal"]) > 8){
                    $arrayTop = array_slice($voteSession["byProposal"] , 0, 8);
                    $voteSession["top"] = $arrayTop;
                }else{
                    $voteSession["top"] = $voteSession["byProposal"];
                }

            }

            Session::put('sidebarArguments', ['type' => $type, 'cbKey' => $cbKey, 'activeFirstMenu' => 'empavilleAnalysis']);

            $sidebar = 'padsType';
            $active = 'empavilleAnalysis';
            $title = trans('privateCbs.empaville');
            return view('private.cbs.empavilleAnalysis.index', compact('cbKey','voteSession', 'type', 'sidebar', 'active','title'));
        }catch(Exception $e) {
            return redirect()->back()->withErrors(["empavilleDashboard.proposals.error" => $e->getMessage()]);
        }

    }

    /**
     * @param Request $request
     * @param $type
     * @param $action
     * @param $step
     * @return string
     */
    public function moderateRouting(Request $request, $type, $action, $step){
        try{
            $cbKey = $request->cbKey;
            if($action == "create"){
                if($step == "param"){
                    return action('CbsParametersController@create', ['type' => $type, 'cbKey' => $cbKey, 'step' => $step]);
                }

                if($step == "votes"){
                    return action('CbsVoteController@create', ['type' => $type, 'cbKey' => $cbKey, 'step' => $step]);
                }

                if($step == "moderators"){
                    return action('CbsController@showModerators', ['type' => $type, 'cbKey' => $cbKey, 'step' => $step]);
                }
            }

            if($action == "show"){
                if($step == "param"){
                    $parameterId = $request->parameterId;
                    return action('CbsParametersController@show', ['type' => $type, 'cbKey' => $cbKey, 'parameterId' => $parameterId, 'step' => $step]);
                }

                if($step == "votes"){

                    $voteKey = $request->voteKey;
                    return action('CbsVoteController@show', ['type' => $type, 'cbKey' => $cbKey, 'voteKey' => $voteKey, 'step' => $step]);
                }
            }
        } catch (Exception $e) {

        }
    }

    /**
     * @param $type
     * @param $cbKey
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showGroupPermissions($type, $cbKey){
        try{
            Session::put('sidebarArguments', ['type' => $type, 'cbKey' => $cbKey, 'activeFirstMenu' => 'permissions']);

            $sidebar = 'padsType';
            $active = 'permissions';
            $title = trans("privateCbsPermissions.permissions") ;
            return view('private.cbs.indexPermissions', compact('type', 'cbKey', 'active', 'sidebar','title'));
        } catch (Exception $e) {

        }
    }

    /**
     * @param Request $request
     * @param $type
     * @param $cbKey
     * @return mixed
     */
    public function getGroupsPads(Request $request, $type, $cbKey)
    {
        try{
            $filters = $request->get("filterType",[]);
            $data = [];

            if(empty($filters) || in_array("users",$filters)) {
                $managersKeys = collect(Orchestrator::getAllManagers())->pluck("user_key");
                $managers = collect(Auth::getUserNames($managersKeys))->toArray();

                foreach ($managers as $manager) {
                    $manager->type = trans("privateCbs.manager");
                }

                $data = array_merge($data,$managers);
            }

            if(empty($filters) || in_array("groups",$filters)) {
                $groups = collect(Orchestrator::getEntityGroups())->toArray();

                foreach ($groups as $group){
                    $group->type = trans("privateCbs.group");
                }
                $data = array_merge($data,$groups);
            }

            return Datatables::of(collect($data))
                ->editColumn('title', function ($collection) use($type, $cbKey){
                    return "<a href='" . action('CbsController@permissions', ['type' => $type, 'cbKey'=>$cbKey, 'groupKey' => $collection->entity_group_key ?? "", 'userKey' => $collection->user_key ?? ""]) . "'>" . $collection->name . "</a>";
                })
                ->rawColumns(['title'])
                ->make(true);
        } catch (Exception $e) {
            return [$e->getMessage(),$e->getLine()];
        }
    }

    /**
     * @param Request $request
     * @param $type
     * @param $cbKey
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showPermissions(Request $request, $type, $cbKey)
    {
        try{
            $cb = CB::getCb($cbKey);

            $parameters = $cb->parameters;

            $parameterOptions = [];

            foreach($parameters as $parameter){
                if(!empty($parameter->options)){
                    foreach($parameter->options as $options){
                        $parameterOptions[] = (integer) $options->id;
                    }
                }
            }
            $groupKey = $request->input('groupKey') ?? null;
            $userKey = $request->input('userKey') ?? null;

            $permissionsList = CB::getCbParametersOptionsPermissions($groupKey, $userKey, $cbKey, $parameterOptions);
            $permissions = [];
            $parameterPermissions = [];
            if(!empty($permissionsList)){
                foreach($permissionsList as $permission){
                    foreach($permission->parameter_options as $option){
                        $permissions[$option->id] = ['show' => $permission->permission_show, 'create' => $permission->permission_create, 'update' => $permission->permission_update, 'delete' => $permission->permission_delete];
                        if($permission->permission_show){
                            $parameterPermissions[] = $option->parameter_id.'_show';
                        }

                        if($permission->permission_create){
                            $parameterPermissions[] = $option->parameter_id.'_create';
                        }if($permission->permission_update){
                            $parameterPermissions[] = $option->parameter_id.'_update';
                        }if($permission->permission_delete){
                            $parameterPermissions[] = $option->parameter_id.'_delete';
                        }
                    }
                }
            }

            $parameterPermissions = array_unique($parameterPermissions);

            Session::put('sidebarArguments', ['type' => $type, 'cbKey' => $cbKey, 'activeFirstMenu' => 'permissions']);

            $sidebar = 'padsType';
            $active = 'permissions';

            return view('private.cbs.permissions', compact('type', 'cbKey', 'parameters', 'groupKey', 'userKey', 'permissions', 'parameterPermissions', 'active', 'sidebar'));
        } catch (Exception $e) {

        }
    }

    /**
     * @param Request $request
     * @param $type
     * @param $cbKey
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storePermissions(Request $request, $type, $cbKey)
    {
        try{
            $groupKey = $request->groupKey ?? null;
            $userKey = $request->userKey ?? null;
            $parameters = CB::getCbParametersOptions($cbKey);
            $optionsId = [];
            foreach ($parameters->parameters as $parameter) {
                if (!empty($parameter->options)) {
                    foreach ($parameter->options as $option) {
                        $optionsId[] = $option->id;
                    }
                }
            }

            $permissions = [];
            $optionsIds = [];
            if (isset($request->modules_types)) {
                foreach ($optionsId as $id) {
                    foreach ($request->modules_types as $module) {

                        if (isset($module[$id]))
                            $permissions[$id] = [
                                'optionId'          => $id,
                                'permission_show'   => isset($module[$id]['show']) ? (integer)$module[$id]['show'] : 0,
                                'permission_create' => isset($module[$id]['create']) ? (integer)$module[$id]['create'] : 0,
                                'permission_update' => isset($module[$id]['update']) ? (integer)$module[$id]['update'] : 0,
                                'permission_delete' => isset($module[$id]['delete']) ? (integer)$module[$id]['delete'] : 0,
                            ];
                        $optionsIds[] = $id;
                    }
                }
            }

            CB::storeCbPermission($cbKey, $permissions, $groupKey, $userKey, $optionsIds);

            Session::flash('message', trans('privateCbs.permissions_stored'));
            return redirect()->action('CbsController@showGroupPermissions', compact('type', 'cbKey'));
        } catch (Exception $e) {
            return redirect()->back()->withErrors([trans('privateCbs.failed_to_store_permissions') => $e->getMessage()]);
        }
    }

    /**
     * @param Request $request
     * @param $type
     * @return string
     */
    public function getDetailsView(Request $request, $type){
        try{
            $cbKey = $request->cbKey;
            return action('CbsController@show', ['type' => $type, 'cbKey' => $cbKey]);
        } catch (Exception $e) {

        }
    }

    /** Get Topics to Export
     * @param Request $request
     * @param $type
     * @param $padKey
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function topicsToExport(Request $request, $type, $padKey)
    {
        try{

            $voteEventKey = $request->vote_event_key ?? null;
            if (!empty($voteEventKey)) {
                $top = $request->top_topics ?? null;
                $minVotes = $request->min_votes ?? 0;
                try{
                    $topics = Analytics::getTopTopics($voteEventKey,$top,$minVotes,$padKey);
                }catch (Exception $e){
                    $topics = [];
                }
            } else {
                try {
                    $topics = CB::getTopicsByCbKey($padKey);
                    foreach ($topics as $topic) {
                        $topic->topic_name = $topic->title;
                        $topic->total_votes = "-";
                    }
                } catch(Exception $e) {
                    $topics = [];
                }
            }

            $users = Collect($topics)->pluck('created_by');
            $usersKeysNames = [];
            if(!$users->isEmpty()){
                $usersKeysNames = Collect(Auth::getUserNames($users))->pluck('name', 'user_key');
            }

            $collection = Collection::make($topics);

            return Datatables::of($collection)
                ->editColumn('title', function($collection) use($type, $padKey){
                    return "<a href='".action('TopicController@show', [$type, $padKey, $collection->topic_key])."'>".$collection->topic_name."</a>";
                })
                ->editColumn('created_by', function ($collection) use ($usersKeysNames) {
                    if($collection->created_by == 'anonymous')
                        return ucfirst($collection->created_by);
                    else
                        return "<a href='" . action('UsersController@show', [$collection->created_by]) . "'>" . $usersKeysNames[$collection->created_by] . "</a>";
                })
                ->addColumn("status",function($collection) {
                    return $collection->active_status->status_type->code??"No status";
                })
                ->addColumn('action', function ($collection) {

                    $button = "<input type='checkbox' name='topics[]' id='topic_".$collection->topic_key."' class='' value='".$collection->topic_key."' /><label for='topic_".$collection->topic_key."' class='css-label'></label>";
                    return $button;
                })
                ->rawColumns(['title','created_by','action'])
                ->make(true);
        } catch (Exception $e) {

        }
    }

    /** Show Export topics view
     * @param Request $request
     * @param $type
     * @param $cbKey
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showExportTopics(Request $request, $type, $cbKey)
    {
        try{
            $sidebar = 'padsType';
            $active = 'exportTopics';
            Session::put('sidebarArguments', ['type' => $type, 'cbKey' => $cbKey, 'activeFirstMenu' => $active]);
            Session::put('sidebarActive', 'padsType');

            $voteEventsList = CB::getCbVotes($cbKey);

            $voteEvents = collect($voteEventsList)->pluck('name','vote_key')->toArray();
            $data = [];
            $data['sidebar'] = $sidebar;
            $data['active'] = $active;
            $data['type'] = $type;
            $data['cbKey'] = $cbKey;

            if(count($voteEvents) == 0){
                $data["voteEvents"] = null;
            }elseif(count($voteEvents) == 1){
                $data["voteEventKey"] = reset($voteEventsList)->vote_key;
            }else{
                $data["voteEvents"] = $voteEvents;
            }

            return view('private.cbs.exportTopics.exportTopics', $data);
        } catch (Exception $e) {

        }
    }


    /** Export pad topics to new pad
     * @param CbTopicsExportRequest $request
     * @param $type
     * @param $cbKey
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function exportTopics(CbTopicsExportRequest $request, $type, $cbKey)
    {
        try{
            $cbKeyExport = $request->pad_selected;
            $topics = $request->topics ?? [];
            $topTopics = $request->top_topics ?? 10;
            $minVotes = $request->min_votes ?? 0;
            $mappingParameters = [];
            $mappingOptions = [];
            foreach ($request->all() as $key => $input){
                if (strpos($key, 'parameter_') === 0) {
                    $mappingParameters[str_replace('parameter_','',$key)] = $input;
                }
                elseif(strpos($key, 'option_') === 0){
                    $mappingOptions[str_replace('option_','',$key)] = $input;
                }
            }
            CB::exportTopics($cbKey,$cbKeyExport,$topics,$mappingParameters,$mappingOptions,$topTopics,$minVotes);
            Session::flash('message', trans('privateCbs.topics_exported'));

            return redirect()->action('CbsController@showTopics',['type' => $request->pad_type, 'cbKey' => $cbKeyExport]);
        }catch(Exception $e){
            return redirect()->back()->withErrors([trans('privateCbs.topics_exported_error') => $e->getMessage()]);
        }

    }

    /** Mapping parameters form cb to cb export
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|string
     */
    public function mappingParams(Request $request)
    {
        try{
            $cbKey = $request->cb_key;
            $parameters = collect(CB::getCbParametersOptions($cbKey)->parameters)->keyBy('id');
            $cbKeyExport = $request->cb_key_export;
            /** Group by parameters by type code of final cb*/
            $parametersExport = collect(CB::getCbParametersOptions($cbKeyExport)->parameters)->groupBy('code');
            Session::put('mapping_parameters', $parameters);
            Session::put('mapping_parametersExport', $parametersExport);
            $data = [];
            $data['cbKey'] = $cbKey;
            $data['parameters'] = $parameters;
            $data['parametersExport'] = $parametersExport;

            return view('private.cbs.exportTopics.mappingParams', $data);
        }catch (Exception $e){
            return '';
        }

    }


    /** Mapping parameter Options form cb to cb export
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|string
     */
    public function mappingParamOptions(Request $request)
    {
        try{
            $cbKey = $request->cb_key;
            $cbKeyExport = $request->cb_key_export;
            $paramId = $request->param_id;
            $paramExportId = str_replace("param_", "", $request->param_export_id);
            if(Session::has('mapping_parameters')){
                $parameters = Session::get('mapping_parameters');
            }else{
                $parameters = collect(CB::getCbParametersOptions($cbKey)->parameters)->keyBy('id');
            }
            $parameter = $parameters->has($paramId) ? $parameters->get($paramId) : null;
            if(empty($parameter)){
                return '';
            }

            if(Session::has('mapping_parametersExport')){
                /** Group by parameters by type code of final cb*/
                $parametersExport = Session::get('mapping_parametersExport');
            }else{
                /** Group by parameters by type code of final cb*/
                $parametersExport = collect(CB::getCbParametersOptions($cbKeyExport)->parameters)->keyBy('id')->groupBy('type.code');
            }

            if($parametersExport->has($parameter->type->code)){
                $parametersByCode = $parametersExport->get($parameter->type->code)->keyBy('id');
                if($parametersByCode->has($paramExportId)){
                    $parameterExport = $parametersByCode->get($paramExportId);
                }else{
                    return '';
                }
                if(count($parameterExport->options) == 0){
                    return '';
                }
            }else{
                return '';
            }

            $data = [];
            $data['parameter'] = $parameter;
            $data['parameterExport'] = $parameterExport;

            return view('private.cbs.exportTopics.mappingParamOptions', $data);
        }catch (Exception $e){
            return '';
        }

    }

    /**
     * @param $type
     * @param $cbKey
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showSecurityConfigurations($type, $cbKey)
    {
        try{
            $cb = CB::getCb($cbKey);

            $cbConfig = CB::getCbConfigurationPermissions($cbKey);
            $configurations = CB::getConfigurationPermissions();

            $cbConfigurations = [];
            foreach ($cbConfig->cbConfigPermission as $config) {
                $cbConfigurations[$config->code][$config->id] = isset($config->pivot->value) ? json_decode($config->pivot->value) : null;
            }

            switch ($type) {
                case $type == "idea":
                    $title = trans('privateIdeas.show_securityconfigurations');
                    break;
                case $type == "forum":
                    $title = trans('privateForums.show_securityconfigurations');
                    break;
                case $type == "discussion":
                    $title = trans('privateDiscussions.show_securityconfigurations');
                    break;
                case $type == "proposal":
                    $title = trans('privateProposals.show_securityconfigurations');
                    break;
                case $type == "publicConsultation":
                    $title = trans('privatePublicConsultations.show_securityconfigurations');
                    break;
                case $type == "tematicConsultation":
                    $title = trans('privateTematicConsultations.show_securityconfigurations');
                    break;
                case $type == "survey":
                    $title = trans('privateSurveys.show_securityconfigurations');
                    break;
                case $type == "phase1":
                    $title = trans('privatePhaseOne.show_securityconfigurations');
                    break;
                case $type == "phase2":
                    $title = trans('privatePhaseTwo.show_securityconfigurations');
                    break;
                case $type == "phase3":
                    $title = trans('privatePhaseThree.show_securityconfigurations');
                    break;
                case $type == "qa":
                    $title = trans('privateQA.show_securityconfigurations');
                    break;
                case $type == "project":
                    $title = trans('privateProject.show_securityconfigurations');
                    break;
            }

            $userLevels = Orchestrator::getAllEntityLoginLevels(Session::get('X-ENTITY-KEY'));

            if (!empty($userLevels)){
                $userLevels = collect($userLevels)->keyBy('login_level_key')->toArray();
            };

            Session::put('sidebarArguments', ['type' => $type, 'cbKey' => $cbKey, 'activeFirstMenu' => 'security_configurations']);
            Session::put('sidebars', [0 => 'private', 1=> 'padsType']);

            $sidebar = 'padsType';
            $active = 'security_configurations';

            $cbAuthor = Auth::getUserByKey($cb->created_by);
            $votes=CB::getCbVotes($cbKey);

            $data['cbAuthor']             = $cbAuthor;
            $data['title']              = $title;
            $data['cb']                 = $cb;
            $data['type']               = $type;
            $data['cbConfigurations']   = $cbConfigurations;
            $data['configurations']     = $configurations;
            $data['sidebar']            = 'padsType';
            $data['active']             = $active;
            $data['userLevels']         = $userLevels ?? null;
            $data['titleConfPermission'] = trans('privateConfigPermission.accessLevels');
            $data['votes'] =$votes;
            $data['titleVotes'] = trans('privateConfigPermission.votes');

            $eventLevel=[];
            foreach ($votes as $vote) {
                $value=Vote::getEventLevel($cbKey,$vote->vote_key);
                foreach ($value as $key => $value2) {
                    $eventLevel[$vote->vote_key][0]=json_decode($value2->value);
                }
            }
            $data['eventLevel']=$eventLevel;


            return view('private.cbs.securityConfigurations', $data);
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["cbs.stepperCb" => $e->getMessage()]);
        }
    }

    /**
     * @param $type
     * @param $cbKey
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function editSecurityConfigurations($type, $cbKey)
    {
        try{
            $cb = CB::getCb($cbKey);

            $cbConfig = CB::getCbConfigurationPermissions($cbKey);
            $configurations = CB::getConfigurationPermissions();

            $cbConfigurations = [];
            foreach ($cbConfig->cbConfigPermission as $config) {
                $cbConfigurations[$config->code][$config->id] = isset($config->pivot->value) ? json_decode($config->pivot->value) : null;
            }

            $subpad = ($cb->parent_cb_id != 0);

            switch ($type) {
                case $type == "idea":
                    $title = trans('privateIdeas.show_securityconfigurations');
                    break;
                case $type == "forum":
                    $title = trans('privateForums.show_securityconfigurations');
                    break;
                case $type == "discussion":
                    $title = trans('privateDiscussions.show_securityconfigurations');
                    break;
                case $type == "proposal":
                    $title = trans('privateProposals.show_securityconfigurations');
                    break;
                case $type == "publicConsultation":
                    $title = trans('privatePublicConsultations.show_securityconfigurations');
                    break;
                case $type == "tematicConsultation":
                    $title = trans('privateTematicConsultations.show_securityconfigurations');
                    break;
                case $type == "survey":
                    $title = trans('privateSurveys.show_securityconfigurations');
                    break;
                case $type == "phase1":
                    $title = trans('privatePhaseOne.show_securityconfigurations');
                    break;
                case $type == "phase2":
                    $title = trans('privatePhaseTwo.show_securityconfigurations');
                    break;
                case $type == "phase3":
                    $title = trans('privatePhaseThree.show_configurations');
                    break;
                case $type == "qa":
                    $title = trans('privateQA.show_configurations');
                    break;
                case $type == "project":
                    $title = trans('privateProject.show_securityconfigurations');
                    break;
            }

            $userLevels = Orchestrator::getAllEntityLoginLevels(Session::get('X-ENTITY-KEY'));
            $author = Auth::getUser($cb->created_by);
            $votes=CB::getCbVotes($cbKey);


            Session::put('sidebarArguments', ['type' => $type, 'cbKey' => $cbKey, 'activeFirstMenu' => 'configurations']);
            Session::put('sidebars', [0 => 'private', 1=> ($subpad)?'subpadsType':'padsType']);

            $data['author'] = $author = $author->name;
            $data['sidebar'] =  ($subpad)?'subpadsType':'padsType';
            $data['active'] = 'security_configurations';
            $data['title'] = $title;
            $data['cb'] = $cb;
            $data['type'] = $type;
            $data['subpad'] = $subpad;
            $data['cbConfigurations'] = $cbConfigurations;
            $data['configurations'] = $configurations;
            $data['userLevels'] = $userLevels;
            $data['titleConfPermission'] = trans('privateConfigPermission.accessLevels');
            $data['votes'] =$votes;
            $data['titleVotes'] = trans('privateConfigPermission.votes');


            $eventLevel=[];
            foreach ($votes as $vote) {
                $value=Vote::getEventLevel($cbKey,$vote->vote_key);

                foreach ($value as $key => $value2) {
                    $eventLevel[$vote->vote_key][0]=json_decode($value2->value);
                }
            }
            $data['eventLevel']=$eventLevel;


            return view('private.cbs.securityConfigurations', $data);
        } catch (Exception $e) {

        }
    }

    /**
     * @param $type
     * @param $cbKey
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateSecurityConfigurations($type, $cbKey, Request $request)
    {
        try{
            $c = CB::getCbConfigurationPermissions($cbKey);
            $levels=$request->configs['user_level_permissions'];

            foreach ($c as $key2 => $value2) {
                if($key2=='cbConfigPermission'){
                    if(empty($value2)){
                        CB::insertConfigurationPermission($levels,$cbKey);
                    }else{
                        CB::updateConfigurationPermission($levels,$cbKey);
                    }
                }
            }
            if(!(is_null($request->vote['vote']))){
                $values=$request->vote['vote'];
                $eventLevel=Vote::getEventLevels();
            }

            if(!(is_null($request->vote['vote']))) {
                $values = $request->vote['vote'];
                $eventLevel = Vote::getEventLevels();
                if ($eventLevel->data == []) {
                    Vote::storeEventLevel($cbKey, $values);

                } else {
                    Vote::updateEventLevel($cbKey, $values);
                }
            }

            if(is_null($request->vote['vote'])){
                $values = [];
                $eventLevel = Vote::getEventLevels();
                Vote::updateEventLevel($cbKey, $values);
            }

            Session::flash('message', trans('cbs.updateConfigurationPermissions'));
            return redirect()->action('CbsController@showSecurityConfigurations', compact('type', 'cbKey'));
        } catch (Exception $e) {

        }
    }


    /**
     * @param $type
     * @param $cbKey
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showCbComments($type, $cbKey){
        try{
            $cb = CB::getCbWithFlags($cbKey,'POSTS');
            $languages = Orchestrator::getLanguageList();

            Session::put('sidebarArguments', ['type' => $type, 'cbKey' => $cbKey, 'activeFirstMenu' => 'comments']);
            Session::put('sidebars', [0 => 'private', 1=> 'padsType']);

            $sidebar = 'padsType';
            $active = 'comments';
            $title = trans('privateCbs.comments');
            return view('private.cbs.comments', compact('type', 'cbKey','cb','languages','sidebar','active','title'));

        } catch (Exception $e) {

        }
    }

    /**
     * @param Request $request
     * @param $type
     * @param $cbKey
     * @return mixed
     */
    public function getAllComments(Request $request, $type, $cbKey)
    {
        try{
            $cbsKeys[] = $cbKey;
            $showWithFlags = false;
            if(isset($request['filters_static']['flags_filter'])){
                $showWithFlags = $request['filters_static']['flags_filter'];
            }

            $postsToModerate = CB::getPostManagerList($cbsKeys,false,false,$showWithFlags,$request);
            $totalRecords = $postsToModerate->recordsTotal;
            $postsToModerate = $postsToModerate->data;

            $users = collect($postsToModerate)->pluck('created_by');

            $usersKeysNames = collect(Auth::getUserNames($users))->pluck('name', 'user_key');

            $collection = collect($postsToModerate);


            $commentsNeedAuthorization = false;
            if(!$collection->isEmpty()) {
                $commentsNeedAuthorization = $collection->first()->commentNeedsAuth;
            }

            return Datatables::of($collection)
                ->editColumn('topic.title', function($collection) use($cbKey, $type){
                    return "<a href='".action('PublicTopicController@show', [$cbKey, $collection->topic->topic_key,'type' => $type])."'>".$collection->topic->title."</a>";
                })
                ->editColumn('created_by', function($collection) use($usersKeysNames){
                    if ($collection->created_by != 'anonymous') {
                        return $usersKeysNames[$collection->created_by] ?? '';
                    }
                    return trans('privateUser.anonymous');
                })
                ->editColumn('contents', function($collection) use($cbKey,$type){
                    return "<a href='".action('PublicTopicController@show', [$cbKey, $collection->topic->topic_key,'type' => $type])."'>".$collection->contents."</a>";
                })
                ->editColumn('flag', function($collection){
                    if(!empty($collection->flags)){
                        return collect($collection->flags)->sortByDesc('pivot.updated_at')->first()->title;
                    }
                })
                ->addColumn('action', function ($collection) use($cbKey,$commentsNeedAuthorization,$type){
                    $html = '';

                    if ($collection->active)
                        $html .= '<a href="'. action('PostController@active', [$type, $cbKey, $collection->topic->topic_key,$collection->post_key, 1, 'comments']) .'" class="btn btn-flat btn-success btn-xs disabled" data-toggle="tooltip" data-original-title="approve"><i class="glyphicon glyphicon-thumbs-up"></i></a>';
                    else
                        $html .= '<a href="'. action('PostController@active', [$type, $cbKey, $collection->topic->topic_key,$collection->post_key, 1, 'comments']) .'" class="btn btn-flat btn-success btn-xs" data-toggle="tooltip" data-original-title="approve"><i class="glyphicon glyphicon-thumbs-up"></i></a>';

                    if ($collection->blocked)
                        $html .= '<a href="'. action('PostController@blocked', [$type, $cbKey, $collection->topic->topic_key,$collection->post_key, 1, 'comments']) .'" class="btn btn-flat btn-danger btn-xs disabled" data-toggle="tooltip" data-original-title="disapprove"><i class="glyphicon glyphicon-thumbs-down"></i> </a>';
                    else
                        $html .= '<a href="'. action('PostController@blocked', [$type, $cbKey, $collection->topic->topic_key,$collection->post_key, 1, 'comments']) .'" class="btn btn-flat btn-danger btn-xs" data-toggle="tooltip" data-original-title="disapprove"><i class="glyphicon glyphicon-thumbs-down"></i> </a>';

                    if( ONE::verifyModuleAccess('cb','flags') && !empty($collection->flags)) {
                        $html .= '<a class="attachFlag" href="javascript:attachFlag(\'' . $collection->post_key . '\')">' . '<span class="btn btn-flat btn-info btn-xs" title="' . trans("privateCbs.attach_flag") . '"><i class="fa fa-flag" aria-hidden="true"></i></span>' . '</a>';
                        $html .= '<a href="javascript:seeFlagHistory(\'' . $collection->post_key . '\')">' . '<span class="btn btn-flat btn-xs bg-yellow" title="' . trans("privateCbs.show_flag_history") . '"><i class="fa fa-binoculars" aria-hidden="true"></i></i></span>' . '</a>';
                    }

                    return $html;
                })
                /* Makes DataTable not reordering the data again - was messing up with dates */
                ->order(function(){})
                ->skipPaging()
                ->setTotalRecords($totalRecords)
                ->rawColumns(['topic.title','contents','action'])
                ->make(true);
        } catch (Exception $e) {

        }
    }

    /**
     * @param $type
     * @param $cbKey
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showQuestionnaires($type, $cbKey){
        try{
            $cb = CB::getCbConfigurations($cbKey);
            $questionnairesCb = CB::getQuestionnaires($cbKey);

            $cbQuestionnaires = null;
            if (!empty($questionnairesCb)){
                $cbQuestionnaires = collect($questionnairesCb)->toArray();
            }

            $cbVoteEvents = CB::getListCbVotes($cbKey);

            switch ($type) {
                case $type == "idea":
                    $title = trans('privateIdeas.show_configurations');
                    break;
                case $type == "forum":
                    $title = trans('privateForums.show_configurations');
                    break;
                case $type == "discussion":
                    $title = trans('privateDiscussions.show_configurations');
                    break;
                case $type == "proposal":
                    $title = trans('privateProposals.show_configurations');
                    break;
                case $type == "project_2c":
                    $title = trans('privateProject2Cs.show_configurations').' '.(isset($cb->title) ? $cb->title : null);
                    break;
                case $type == "publicConsultation":
                    $title = trans('privatePublicConsultations.show_configurations');
                    break;
                case $type == "tematicConsultation":
                    $title = trans('privateTematicConsultations.show_configurations');
                    break;
                case $type == "survey":
                    $title = trans('privateSurveys.show_configurations');
                    break;
                case $type == "phase1":
                    $title = trans('privatePhaseOne.show_configurations');
                    break;
                case $type == "phase2":
                    $title = trans('privatePhaseTwo.show_configurations');
                    break;
                case $type == "phase3":
                    $title = trans('privatePhaseThree.show_configurations');
                    break;
                case $type == "qa":
                    $title = trans('privateQA.show_configurations');
                    break;
            }

            Session::put('sidebarArguments', ['type' => $type, 'cbKey' => $cbKey, 'activeFirstMenu' => 'questionnaires']);
            Session::put('sidebars', [0 => 'private', 1=> 'padsType']);


            $questionnaires=Questionnaire::getQuestionnaireList();

            $questionnaire=[];
            if($questionnaires=='[]'){
                $questionnaire[]=null;
            }else{
                foreach ($questionnaires as $key => $value) {
                    $questionnaire[$value->form_key]=$value->title;
                }
            }

            $sidebar = 'padsType';
            $active = 'questionnaires';

            $author = Auth::getUserByKey($cb->created_by);

            $actions=CB::getActions();

            if(Session::get('questionnaireTemplate')){
                Session::forget('questionnaireTemplate');
            }

            return view('private.cbs.questionnaires', compact('type', 'cbKey', 'active', 'sidebar','cb', 'author', 'questionnaire', 'actions', 'cbQuestionnaires', 'cbVoteEvents'));
        } catch (Exception $e) {

        }
    }


    /**
     * @param $type
     * @param $cbKey
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function editQuestionnaires($type, $cbKey)
    {
        try{
            $cb = CB::getCbConfigurations($cbKey);
            $cbQuestionnaires=collect(CB::getQuestionnaires($cbKey))->toArray();

            $cbVoteEvents = CB::getListCbVotes($cbKey);

            $questionnaires=Questionnaire::getQuestionnaireList();

            $questionnaire=[];
            if($questionnaires=='[]'){
                $questionnaire[]=null;
            }else{
                foreach ($questionnaires as $key => $value) {
                    $questionnaire[$value->form_key]=$value->title;
                }
            }

            switch ($type) {
                case $type == "idea":
                    $title = trans('privateIdeas.edit_configurations');
                    break;
                case $type == "forum":
                    $title = trans('privateForums.edit_configurations');
                    break;
                case $type == "discussion":
                    $title = trans('privateDiscussions.edit_configurations');
                    break;
                case $type == "proposal":
                    $title = trans('privateProposals.edit_configurations');
                    break;
                case $type == "project_2c":
                    $title = trans('privateProject2Cs.edit_configurations').' '.(isset($cb->title) ? $cb->title : null);
                    break;
                case $type == "publicConsultation":
                    $title = trans('privatePublicConsultations.edit_configurations');
                    break;
                case $type == "tematicConsultation":
                    $title = trans('privateTematicConsultations.edit_configurations');
                    break;
                case $type == "survey":
                    $title = trans('privateSurveys.edit_configurations');
                    break;
                case $type == "phase1":
                    $title = trans('privatePhaseOne.edit_configurations');
                    break;
                case $type == "phase2":
                    $title = trans('privatePhaseTwo.edit_configurations');
                    break;
                case $type == "phase3":
                    $title = trans('privatePhaseThree.edit_configurations');
                    break;
                case $type == "qa":
                    $title = trans('privateQA.edit_configurations');
                    break;
            }

            Session::put('sidebarArguments', ['type' => $type, 'cbKey' => $cbKey, 'activeFirstMenu' => 'questionnaires']);

            $sidebar = 'padsType';
            $active = 'questionnaires';

            $author = Auth::getUser($cb->created_by);

            $actions=CB::getActions();

            return view('private.cbs.questionnaires', compact('title', 'cb', 'type', 'author', 'sidebar', 'active', 'questionnaire', 'actions', 'cbQuestionnaires', 'cbVoteEvents'));
        } catch (Exception $e) {

        }
    }

    /**
     * @param $type
     * @param $cbKey
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateQuestionnaires($type, $cbKey, Request $request)
    {
        try{
            $days = [];
            $action = [];
            $notify = [];
            $ignore = [];
            $elements = [];
            $questionnaire = [];
            $actionElements = [];

            $cbVoteEvents = CB::getListCbVotes($cbKey);

            foreach ($request->all() as $key => $value) {
                switch ($key){
                    case 'action':
                        $action = array_keys($value);
                        break;
                    case 'questionnaire':
                        $questionnaire = ($value);
                        break;
                    case 'notify':
                        $notify = ($value);
                        break;
                    case 'ignore':
                        $ignore = ($value);
                        break;
                    case 'days_ignore':
                        $days = ($value);
                        break;
                    default:
                        break;
                }
            }

            foreach ($action as $value) {
                foreach ($questionnaire as $key2 => $value2) {
                    foreach ($days as $key5 => $value5) {
                        if($value == ($key2)){
                            $elements['questionnaire_key'] = $value2;
                        }
                        if($value == ($key5)){
                            $elements['days'] = $value5;
                        }
                    }
                }

                if($notify!=[]){
                    foreach ($notify as $key3 => $value3) {
                        if($value == ($key3)){
                            $elements['notify'] = $value3;
                        }
                    }
                } else{
                    $elements['notify'] = null;
                }

                if($ignore!=[]){
                    if (in_array($value, array_keys($ignore))) {
                        $elements['ignore'] = '1';
                    } else {
                        $elements['ignore'] = null;
                    }
                } else{
                    $elements['ignore'] = null;
                }

                $actionElements[$value] = $elements;
            }

            $voteEvents = [];
            foreach($cbVoteEvents as $voteEvent){
                if (isset($actionElements['vote_event_'.$voteEvent->vote_key])){
                    $voteEvents[$voteEvent->vote_key] = $actionElements['vote_event_'.$voteEvent->vote_key];
                    unset($actionElements['vote_event_'.$voteEvent->vote_key]);
                    if(!empty($voteEvents)){
                        $actionElements['vote_event'] = $voteEvents;
                    }
                }
            }

            if(Session::has('questionnaireTemplate')){
                $translations = Session::get('questionnaireTemplate');
                foreach ($translations as $key => $item){
                    if ($key == 'vote_event'){
                        foreach ($item as $container => $voteEvent){
                            foreach ($voteEvent as $voteEventKey => $translations){
                                if (isset($actionElements[$key][$voteEventKey])) {
                                    $actionElements[$key][$voteEventKey]['translations'] = (collect($translations)->keyBy('language_code'))->toArray();
                                }
                            }
                        }
                    } else {
                        if (isset($actionElements[$key])){
                            $actionElements[$key]['translations'] = (collect($item)->keyBy('language_code'))->toArray();
                        }
                    }
                }
            }

            $cbQuestionnaires = CB::setCbQuestionnaire($actionElements, $cbKey);

            Session::forget('questionnaireTemplate');
            Session::flash('message', trans('cbs.update_questionnaire_ok'));

            return redirect()->action('CbsController@showQuestionnaires', compact('type', 'cbKey', 'cbQuestionnaires'));
        } catch (Exception $e) {

        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function editQuestionnaireTemplate(Request $request)
    {
        try{
            $cbKey = $request->input('cb_key');
            $data['actionCode'] = $request->input('action_code');
            $data['voteKey'] = $request->input('vote_key');
            $data['type'] = $request->input('type');
            $data['form'] = json_decode($request->input('form_data'));
            $data['cb'] = CB::getCbConfigurations($cbKey);

            $authorData = Auth::getUser($data['cb']->created_by);

            if(!empty($authorData)){
                $data['author'] =  $authorData->name;
            }

            $data['languages'] = Orchestrator::getLanguageList();

            $questionnaireTemplate = Session::get('questionnaireTemplate.'.$data['actionCode']);

            if(!is_null($questionnaireTemplate)){
                $translations = [];
                if (is_null($data['voteKey'])){
                    foreach ($questionnaireTemplate as $item){
                        $translations[$item['language_code']] = $item;
                    }
                } else {
                    foreach ($questionnaireTemplate as $voteItem){
                        $vote = $voteItem[$data['voteKey']];
                        foreach ($vote as $item){
                            $translations[$item['language_code']] = $item;
                        }
                    }
                }

                $data['translations'] = $translations;
            } else {
                $questionnaireTemplate = CB::getCbQuestionnaireTemplate($cbKey, $data['actionCode'], $data['voteKey']);
                if(!is_null($questionnaireTemplate)){
                    $translations = [];
                    foreach ($questionnaireTemplate as $item){
                        $translations[$item->language_code] = (array) $item;
                    }
                    $data['translations'] = $translations;
                }
            }

            return view('private.cbs.questionnaireTemplate', $data);
        } catch (Exception $e) {

        }
    }

    /**
     * @param Request $request
     * @return string
     */
    public function updateQuestionnaireTemplate(Request $request)
    {
        try{
            $cbKey = $request->input('cb_key');
            $type = $request->input('type');
            $actionCode = $request->input('action_code');
            $voteKey = $request->input('vote_key');

            $languages = Orchestrator::getLanguageList();
            $parametersSerialized = $request->input('parameters');
            $parameters = [];

            foreach ($parametersSerialized as $key => $item){
                $parameters[$item['name']] = $item['value'];
            }

            if(!empty($voteKey)){
                $translations = [];
                foreach($languages as $language){
                    if(!empty($parameters['content_'.$language->code])){
                        $translations[$voteKey][$language->code] = [
                            'language_code' => $language->code,
                            'accept' => $parameters['accept_'.$language->code],
                            'ignore' => $parameters['ignore_'.$language->code],
                            'content' => $parameters['content_'.$language->code]
                        ];
                    }
                }
                Session::push('questionnaireTemplate.'.$actionCode,$translations);
            }
            else{
                $translations = [];
                foreach($languages as $language){
                    if(!empty($parameters['content_'.$language->code])){
                        $translations[$language->code] = [
                            'language_code' => $language->code,
                            'accept' => $parameters['accept_'.$language->code],
                            'ignore' => $parameters['ignore_'.$language->code],
                            'content' => $parameters['content_'.$language->code]
                        ];
                    }
                }
                Session::put('questionnaireTemplate.'.$actionCode, $translations);
            }
            return 'OK';
//            return redirect()->action('CbsController@editQuestionnaires', ['type'=> $type, 'cbKey'=>$cbKey, 'f'=>'cbsQuestionnaires']);
        } catch (Exception $e) {
            return 'ERROR';
        }

    }

    /**
     * @param $cbKey
     * @param $topicCheckpointNewId
     */
    public function finishPhase($cbKey, $topicCheckpointNewId) {
        try {
            dd(CB::finishPhase($cbKey, $topicCheckpointNewId));
        } catch (Exception $e) {
            dd("exception",$e);
        }
    }

    /**
     * @param $cbKey
     * @param $topicCheckpointNewId
     */
    public function finishPhase2($cbKey, $topicCheckpointNewId) {
        try {
            dd(CB::finishPhase2($cbKey, $topicCheckpointNewId));
        } catch (Exception $e) {
            dd("exception",$e);
        }
    }

    /**
     * @param Request $request
     * @param $type
     * @param $cbKey
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function duplicate(Request $request, $type, $cbKey)
    {
        $data = [];
        $data['type'] = $type;
        $data['cbKey'] = $cbKey;
        $data['configurations'] = CB::getConfigurations();
        $data['oldCbConfigurations'] = collect(CB::getCbConfigurations($cbKey)->configurations)->keyBy('code');

        return view('private.cbs.duplicateCb', $data);
    }

    public function createWizard(Request $request, $type = null){
        try {
            $title = trans('privateCbs.create_cb_wizard');

            if (empty($type)) {
                return view('private.wizards.cb-select-type', compact('title'));
            } else {
                $configurations = CB::getConfigurations();
                return view('private.wizards.cb', compact('type', 'title', 'configurations'));
            }
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["cbs.wizard" => $e->getMessage()]);
        }
    }
    public function storeWizard($type, Request $requestCB) {
        /* Try to add the Module Type Permission to the entity */
        try {
            if (Session::get("firstInstallWizardStarted",false)) {
                $modules = collect(Orchestrator::getModulesList()->data ?? []);
                $cbModule = $modules->where("code", "=", "cb")->first() ?? [];

                if (!empty($cbModule)) {
                    $cbModuleType = collect($cbModule->module_types)->where("code", "=", $type)->first() ?? [];

                    if (!empty($cbModuleType)) {
                        Orchestrator::setModuleTypeForEntity($cbModule->module_key, $cbModuleType->module_type_key);
                        \Cache::forget('entityModulesActive_' . ONE::getEntityKey());
                    }
                }
            }
        } catch (Exception $e) {}

        try {
            if ($type=="empaville") {
                try {
                    // CB
                    $requestCB["title"] = $requestCB->get("title");
                    $requestCB["description"] = '';
                    $requestCB["start_date"] = Carbon::now()->format("Y-m-d");

                    //Configurations
                    $requestCB["configuration_8"] = 1;
                    $requestCB["configuration_9"] = 1;
                    $requestCB["configuration_10"] = 1;
                    $requestCB["configuration_11"] = 1;
                    $requestCB["configuration_15"] = 1;
                    $requestCB["configuration_21"] = 1;
                    $requestCB["configuration_23"] = 1;

                    $cb = CB::setNewCb($requestCB);
                    $cbKey = $cb->cb_key;

                    $configurations = CB::getConfigurations();
                    $cbConfigurations = [];
                    foreach ($configurations as $configuration) {
                        foreach ($configuration->configurations as $options) {
                            $cbConfigurations[] = $options->id;
                        }
                    }
                    $configurationsToSave = [];
                    foreach ($requestCB->all() as $key => $value) {
                        if (strpos($key, 'configuration_') !== false) {
                            $id = str_replace("configuration_", "", $key);
                            $configurationsToSave[] = $id;
                            unset($cbConfigurations[array_search($id, $cbConfigurations)]);
                        }
                    }

                    CB::setCbConfigurations($cbKey, $configurationsToSave, null, null, 0);
                    Orchestrator::setNewCb("idea",$cbKey);

                    // CB Parameters
                    $parameterBudget = array(
                        "parameter_type_id" => 7,
                        "cb_key" => $cbKey,
                        "translations" => array(
                          array(
                            "language_code" => "pt",
                            "parameter" => "Budget",
                            "description" => "Indica il costo della tua proposta"
                          )
                        ),
                        "code" => "dropdown",
                        "parameter_code" => "",
                        "mandatory" => "1",
                        "visible" => "1",
                        "visible_in_list" => "1",
                        "private" => "0",
                        "use_filter" => "0",
                        "value" => null,
                        "options" => array(
                            array(
                                "translations" => array(
                                    0 => array(
                                        "language_code" => "pt",
                                        "label" => "100"
                                    )
                                ),
                                "optionFields" => [],
                                "code" => ""
                            ),
                            array(
                                "translations" => array(
                                    array(
                                        "language_code" => "pt",
                                        "label" => "50",
                                    )
                                ),
                                "optionFields" => [],
                                "code" => ""
                            ),
                            array(
                                "translations" => array(
                                    array(
                                        "language_code" => "pt",
                                        "label" => "25"
                                    )
                                ),
                                "optionFields" => [],
                                "code" => ""
                            )
                        ),
                        "fields" => []
                    );
                    CB::setParameters($parameterBudget);

                    $parameterImageMap = array(
                        "parameter_type_id" => 8,
                        "cb_key" => $cbKey,
                        "translations" => array(
                            array(
                                "language_code" => "pt",
                                "parameter" => "Empaville Map",
                                "description" => "Empaville Map",
                            )
                        ),
                        "code" => "image_map",
                        "parameter_code" => "",
                        "mandatory" => "1",
                        "visible" => "1",
                        "visible_in_list" => "1",
                        "private" => "0",
                        "use_filter" => "0",
                        "value" => "",
                        "options" => [],
                        "fields" => []
                    );
                    CB::setParameters($parameterImageMap);


                    // CB Event Vote
                    $voteEventRequest = new Request();
                    $voteEventRequest["cbKey"] = $cbKey;
                    $voteEventRequest["name"] = "Vote Event 2";
                    $voteEventRequest["code"] = "";
                    $voteEventRequest["endDate"] = Carbon::now()->addMonth(1)->subDay(1)->format("Y-m-d");
                    $voteEventRequest["endTime"] = "23:59";
                    $voteEventRequest["startDate"] = Carbon::now()->format("Y-m-d");
                    $voteEventRequest["startTime"] = "00:00";
                    $voteEventRequest["methodSelect"] = "3";
                    $voteEventRequest["genericConfig_mmUjgKoB7yarps9MSk86ELRBdtq8Ry6m"] = "1";

                    $configurations = array(
                        array(
                            "configuration_id" => "4",
                            "value"            => 0
                        ),
                        array(
                            "configuration_id" => "5",
                            "value"            => 3
                        ),
                        array(
                            "configuration_id" => "6",
                            "value"            => 2
                        ),
                        array(
                            "configuration_id" => "7",
                            "value"            => 1
                        )
                    );

                    $newVoteEvent = Vote::setVoteEvent($voteEventRequest, $configurations);
                    $voteNew = CB::setCbVote($voteEventRequest, $newVoteEvent);

                    
                        return redirect()->action("CbsController@show",["type" => "idea", "cbKey" => $cbKey]);
                    
                } catch(Exception $e) {
                    return redirect()->back()->withErrors(["cbs.store" => $e->getMessage()]);
                }
            } else{
                if ($type == 'proposal' || $type == 'project')
                {
                    if (empty(Session::get('participatory')))
                    {
                        Session::put('participatory', $type);
                    }
                    else{
                        Session::put('participatory_second', $type);
                    }
                }
                $configurations = CB::getConfigurations();
                foreach ($configurations as $configuration) {
                    foreach ($configuration->configurations as $options) {
                        if ($options->code == 'security_create_topics'){
                            if ($type == 'proposal' || $type == 'idea' || $type == 'fix_my_street'){
                                $configurationsToSave[] = $options->id ;
                            }
                        }
                        if ($options->code == 'security_comment_authorization'){
                            if ($type == 'proposal' || $type == 'project'){
                                $configurationsToSave[] = $options->id ;
                            }
                        }
                        if ($options->code == 'topic_need_moderation'){
                            if ($type == 'proposal' || $type == 'idea' || $type == 'fix_my_street'){
                                $configurationsToSave[] = $options->id ;
                            }
                        }
                        if ($options->code == 'security_allow_report_abuses'){
                            if ($type == 'idea' || $type == 'consultation' || $type == 'fix_my_street'){
                                $configurationsToSave[] = $options->id ;
                            }
                        }
                        if ($options->code == 'topic_comments_allow_comments' || $options->code == 'disable_comments_functionality'){
                            if ($type == 'proposal' || $type == 'idea' || $type == 'consultation' || $type == 'fix_my_street'){
                                $configurationsToSave[] = $options->id ;
                            }
                        }
                        if ($options->code == 'show_status' || $options->code == 'allow_filter_status'){
                            if ($type == 'proposal' || $type == 'project' || $type == 'idea' || $type == 'fix_my_street'){
                                $configurationsToSave[] = $options->id ;
                            }
                        }
                        if ($options->code == 'security_public_access' || $options-> code == 'topic_options_allow_share'){
                                $configurationsToSave[] = $options->id ;
                        }
                    }
                }
                return $this->store($type, $requestCB, $configurationsToSave);
            }
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["cbs.store" => $e->getMessage()]);
        }
    }


    public function publishTechnicalAnalysisForm(Request $request,$type,$cbKey) {
        try {
            $technicalAnalysisQuestions = CB::getCbQuestions($cbKey);
            $cb = CB::getCb($cbKey);
            $cbParameters = CB::getCbParameters($cbKey);
            $cbStatusTypes = CB::getStatusTypes();

            //sidebar
            Session::put('sidebarArguments', ['type' => $type, 'cbKey' => $cbKey, 'activeFirstMenu' => 'details']);
            Session::put('sidebars', [0 => 'private', 1=> 'padsType']);
            $sidebar = 'padsType';
            $active = 'details';

            return view('private.cbs.publishTechnicalAnalysis.index', compact('technicalAnalysisQuestions','cbParameters','cbStatusTypes','sidebar','type','cbKey','active','cb'));
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["private.cbs.publishTechnicalAnalysis.create" => $e->getMessage()]);
        }
    }
    public function publishTechnicalAnalysisConfirmation(Request $request, $type, $cbKey) {
        try {
            $questionKeys = $request->get("description-question");
            $parameterIds = $request->get("description-parameter");
            $passedStatusKey = $request->get("status-passed");
            $failedStatusKey = $request->get("status-failed");

            $result = CB::publishTechnicalAnalysisResults($cbKey,$questionKeys,$parameterIds, $passedStatusKey, $failedStatusKey, true);

            if (!empty($result->errors))
                return redirect()->back()->withInput()->with("exportTechnicalAnalysisErrors",$result->errors);

            $data = array(
                "type" => $type,
                "cbKey" => $cbKey,
                "questions" => $result->data->questions,
                "parameters" => $result->data->parameters,
                "passing" => $result->data->passing,
                "failing" => $result->data->failing,
                "noDecision" => $result->data->noDecision,
                "noAnalysis" => $result->data->noAnalysis,

                "passedStatusKey" => $passedStatusKey,
                "failedStatusKey" => $failedStatusKey,

                "cb" => CB::getCb($cbKey)
            );

            return view('private.cbs.publishTechnicalAnalysis.confirmation', $data);
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["private.cbs.publishTechnicalAnalysis.confirm" => $e->getMessage()]);
        }
    }
    public function publishTechnicalAnalysisSubmit(Request $request, $type, $cbKey) {
        try {
            $questionKeys = $request->get("questionKeys");
            $parameterIds = $request->get("parameterIds");
            $passedStatusKey = $request->get("passedStatusKey");
            $failedStatusKey = $request->get("failedStatusKey");

            $result = CB::publishTechnicalAnalysisResults($cbKey,$questionKeys,$parameterIds, $passedStatusKey, $failedStatusKey, false);

            if (!empty($result->errors))
                return redirect()->back()->withInput()->with("exportTechnicalAnalysisErrors",$result->errors);

            $data = array(
                "type" => $type,
                "cbKey" => $cbKey,
                "questions" => $result->data->questions,
                "parameters" => $result->data->parameters,
                "passing" => $result->data->passing,
                "failing" => $result->data->failing,
                "noAnalysis" => $result->data->noAnalysis,

                "passedStatusKey" => $passedStatusKey,
                "failedStatusKey" => $failedStatusKey,

                "publishResult" => $result->result,

                "cb" => CB::getCb($cbKey)
            );

            return view('private.cbs.publishTechnicalAnalysis.result', $data);
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["private.cbs.publishTechnicalAnalysis.publish" => $e->getMessage()]);
        }
    }

    public function getUsers(Request $request) {
        try {
            $usersData = Orchestrator::getEntityUsers($request)->users;
            $users = [];
            foreach ($usersData as $user) {
                if (!empty($user->name)) {
                    $users[$user->user_key] = array(
                        "name" => $user->name,
                        "user_key" => $user->user_key
                    );
                }
            }

            return $users;
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["cbs.getUsers" => $e->getMessage()]);
        }
    }

    public function stepType(Request $request){

        try{

            $typesList = Orchestrator::getCbTypesList();
            $configurations = CB::getConfigurations();

            $data = [];

            $data["typesList"] = $typesList;
            $data["title"] = trans('privateCbs.selectCbType');
            $data["configurations"] = $configurations;

            return view('private.cbs.wizard.step0', $data);

        }catch (Exception $e){
            return redirect()->back();
        }
    }

    /**
     * Show the form for creating a new cb check list.
     *
     * @return Response
     */
    public function addCheckList(Request $request)
    {
        try {

            return view('private.cbs.addCheckList', compact('name'));

        } catch (Exception $e) {

        }
        return redirect()->back()->withErrors(["privateCbs.addCheckList" => $e->getMessage()]);
    }
    /**
     * Update the specified resource in cb check list.
     *
     * @param Request $request
     * @return Response
     */
    public function updateChecklistItem(Request $request)
    {
        try {

            EMPATIA::updateChecklistItem($request);

        } catch (Exception $e) {

        }
        return redirect()->back()->withErrors(["privateCbs.updateCheckList" => $e->getMessage()]);
    }

    /**
     * Show the form for creating a new cb check list.
     *
     * @return Response
     */
    public function createChecklistItem(Request $request)
    {
        try {

            EMPATIA::createChecklistItem($request);

        } catch (Exception $e) {

        }
        return redirect()->back()->withErrors(["privateCbs.createCheckList" => $e->getMessage()]);
    }

    public function removeCheckListItem(Request $request)
    {
        try {

            EMPATIA::removeCheckListItem($request->checklist_key);

        } catch (Exception $e) {

        }
        return redirect()->back()->withErrors(["privateCbs.removeCheckList" => $e->getMessage()]);
    }

    /**
     * Display a listing of cbs permissions.
     *
     * @return \Illuminate\Http\Response
     */
    public function permissions(Request $request, $type, $cbKey)
    {
        $title =  trans('privateCbs.permissions');
        $groupKey = $request->input('groupKey') ?? null;
        $userKey = $request->input('userKey') ?? null;

        $entityKey = Orchestrator::getSiteEntity($_SERVER["HTTP_HOST"])->entity_id;
        $permissions = EMPATIA::getCBPermissions($cbKey,$groupKey, $userKey,$entityKey);

        // sidebar info
        Session::put('sidebarArguments', ['type' => $type, 'cbKey' => $cbKey, 'activeFirstMenu' => 'permissions']);
        Session::put('sidebarActive', 'padsType');
        $sidebar = 'padsType';
        $rootCbKey = $cbKey;
        $active = 'permissions';

        return view('private.cbs.cbsPermission', compact('permissions','type','cbKey','title','active','sidebar','rootCbKey'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $code, $userId, $groupId,
     * @return \Illuminate\Http\Response
     */
    public function updatePermission(Request $request, $type, $cbKey)
    {
        $entityKey = Orchestrator::getSiteEntity($_SERVER["HTTP_HOST"])->entity_id;

        if($request->permission){
            EMPATIA::updateCBPermissions($cbKey,$request->code,$request->userId,$request->groupId,0,$entityKey);
        }
        else{
            EMPATIA::updateCBPermissions($cbKey,$request->code,$request->userId,$request->groupId,1,$entityKey);
        }
    }

}
