<?php

namespace App\Http\Controllers;

use App\ComModules\Files;
use App\ComModules\Notify;
use App\ComModules\Vote;
use App\ComModules\CB;
use App\ComModules\Orchestrator;
use App\ComModules\Auth;
use App\ComModules\LogsRequest;
use App\One\One;
use App\One\OneCbs;
use Cache;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Session;
use Cookie;
use Chencha\Share\ShareFacade as Share;
use URL;
use Illuminate\Pagination\Paginator;
use View;

class PublicCbsController extends Controller
{

    private $cbType;

    public function __construct()
    {
        $this->cbType = [
            'forum'               => 'forum',
            'discussion'          => 'discussion',
            'proposal'            => 'proposal',
            'idea'                => 'idea',
            'tematicConsultation' => 'tematicConsultation',
            'publicConsultation'  => 'publicConsultation',
            'survey'              => 'survey',
            'project'             => 'project',
            'phase1'              => 'phase1',
            'phase2'              => 'phase2',
            'phase3'              => 'phase3',
            'qa'                  => 'qa',
            'project_2c'          => 'project_2c',
            'event'          => 'event'
        ];
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return View
     */
    public function index(Request $request)
    {
        try {
            $padsToShow = 6;
            // Check if type is set
            if (!isset($this->cbType[$request->type])) {
                throw new Exception(trans('cbs.errorsNOcbType'));
            }
            $type = $this->cbType[$request->type];


            if (!empty($request->page)) {

                if (Cache::has(session()->getId() . '_list_' . $type)) {

                    $cbsData = Cache::get(session()->getId() . '_list_' . $type)['cbsData'];
                    $cbsToDisplay = array_slice($cbsData, ($padsToShow * ($request->page - 1)));
                    $cbsDataPagination = new Paginator($cbsToDisplay, $padsToShow, $request->page);
                    // Prepare data to send to the view
                    $data = [];
                    $data['cbsData'] = $cbsData;
                    $data['cbsDataPagination'] = $cbsDataPagination;
                    $data['usersNames'] = Cache::get(session()->getId() . '_list_' . $type)['usersNames'];
                    $data['type'] = $type;
                    $data['homePageConfigurations'] = Cache::get(session()->getId() . '_list_' . $type)['homePageConfigurations'];


                    Cache::put(session()->getId() . '_list_' . $type, $data, 30);
                    return view('public.' . ONE::getEntityLayout() . '.cbs.' . $type . '.padsList', $data);
                }
            }


            //get home page configurations
            $homePageConfigurations = Orchestrator::getSiteHomePageConfigurations();

            // Variables Initialization
            $usersNames = [];
            $cbsData = [];

            // Get CB Keys from Orchestrator
            $cbs = Orchestrator::getCbTypes($type);

            // Data not found / No keys
            if (count($cbs) == 0) {
                return view('public.' . ONE::getEntityLayout() . '.cbs.' . $type . '.index', compact('cbsData', 'usersNames', 'type', 'homePageConfigurations'));
            }

            // Get data to list CBs
            $cbsStatistics = [];
            $cbsTotalLikes = 0;
            $cbsData = CB::getListCbsWithStats($cbs, $request->cbsStatus);
            $cbsStatistics = $cbsData->cbsStatistics;
            $cbsData = $cbsData->cbs;
            //$cbsData = CB::getListCBs($cbs);

            // Gets user keys
            $usersKeys = [];
            foreach ($cbsData as $topic) {
                if (isset($topic->lastpost->updated_at)) {
                    $user = $topic->lastpost->created_by;

                    if (!array_key_exists($user, $usersKeys))
                        $usersKeys[] = $user;

                    $user = $topic->lasttopic->created_by;

                    if (!array_key_exists($user, $usersKeys))
                        $usersKeys[] = $user;
                }
                $usersKeys[] = $topic->created_by;
            }

            // Get user information to use in CB list
            if (count($usersKeys) > 0) {
                $usersNames = Auth::getPublicListNames($usersKeys);
            }

            // Sets the finish time in days for each CB

            /*
            $cbTime = [];
            foreach ($cbs as $cb){
                $time = Carbon::now();
                $cbTime[$cb->cb_key] = $time->diffInDays(new Carbon($cb->end_date));
            }
            */
            $cbsDataPagination = new Paginator($cbsData, $padsToShow, $request->page);
            $data = [];
            $data['cbsData'] = $cbsData;
            $data['cbsDataPagination'] = $cbsDataPagination;
            $data['usersNames'] = $usersNames;
            $data['type'] = $type;
            $data['homePageConfigurations'] = $homePageConfigurations;
            $data['cbsStatistics'] = $cbsStatistics;
            $data['cbsTotalLikes'] = $cbsTotalLikes;

            Cache::put(session()->getId() . '_list_' . $type, $data, 30);

            // Return view associated to type
            return view('public.' . ONE::getEntityLayout() . '.cbs.' . $type . '.index', $data);

        } catch (Exception $e) {
            return redirect()->back()->withErrors(["cbs.index" => trans('error.cbIndex')]);
        }

    }


    public function topicAsParameter($topicParameters, $parametersToFilter)
    {

        foreach($parametersToFilter as $key => $parameter){
            if($elementExists = collect($topicParameters)->where('id',$key)->first()){
                if($elementExists->pivot->value != $parameter)
                    return false;
            }else{
                return false;
            }

        }
        return true;
    }

    public function reorderTopics($topics,$sortOrder) {
        switch ($sortOrder) {
            case "order_by_recent":
                $topics = collect($topics)->sortByDesc('created_at')->toArray();
                break;
            case "order_by_popular":
                $topics = collect($topics)->sortByDesc(function ($topic, $key) {
                    return count($topic->followers);
                })->toArray();
                break;
            case "order_by_popular_as_parameter":
                $topics = collect($topics)->sortByDesc(function ($topic, $key) {
                    $followersTypes = collect($topic->parameters)->where('code','=','numeric');
                    $followers = 0;
                    if(!empty($followersTypes)){
                        foreach($followersTypes as $followersType){
                            $followers += $followersType->pivot->value;
                        }
                    }
                    return $followers;
                })->toArray();
                break;
            case "order_by_post_count":
                $topics = collect($topics)->sortByDesc(function ($topic, $key) {
                    return $topic->statistics->posts_counter;
                })->toArray();
                break;
            case "order_by_comments":
                $topics = collect($topics)->sortByDesc(function ($topic, $key) {
                    return isset($topic->posts_count) ? $topic->posts_count-1 : 0;
                })->toArray();
                break;
            default:
                shuffle($topics);
        }

        return $topics;
    }

    public function searchTermInTopics($topics, $term)
    {
        $term = strtoupper($term);
        $topics = collect($topics)->filter(function($topic) use ($term)
        {
            if ((strpos(strtoupper($topic->title), $term) !== false) || (strpos(strtoupper($topic->contents), $term) !== false) ) {
                return true;
            }
        })->toArray();
        return $topics;
    }

    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @param  string $cbKey
     * @return View
     */
    public function show(Request $request, $cbKey)
    {
        if($request->in_person_vote){
            return $this->showCbVoteInPerson($request, $cbKey);
        }

        return $this->display($request,$cbKey);

    }

    public function getPilotsForHomePage(Request $request)
    {

        try {

            $cbKey = $request->cb_key;
            $numberTopicToShow = 6;

            //Delete array of Files from Session if exists
            if (Session::has('filesToUpload')) {
                try {
                    Session::forget('filesToUpload');
                } catch (Exception $e) {
                    throw new Exception($e->getMessage());
                }
            }
            /**
             * Infinite Scroolling
             */

            if (!empty($request->page)) {

                if (Cache::has(session()->getId() . $cbKey)) {
                   
                    $topicArray = Cache::get(session()->getId() . $cbKey)['topicsTotals'];
                    $topicsToDisplay = array_slice($topicArray, ($numberTopicToShow * ($request->page - 1)));
                    $topicsToDisplay = new Paginator($topicsToDisplay, $numberTopicToShow, $request->page);
                    


                    // Prepare data to send to the view
                    $data = [];
                    $data['voteResults'] = Cache::get(session()->getId() . $cbKey)['voteResults'];
                    $data['categoryColors'] = Cache::get(session()->getId() . $cbKey)['categoryColors'];
                    $data['topicsTotals'] = Cache::get(session()->getId() . $cbKey)['topicsTotals'];
                    $data['topicsPagination'] = $topicsToDisplay ?? Cache::get(session()->getId() . $cbKey)['topicsPagination'];
                    $data['topics'] = Cache::get(session()->getId() . $cbKey)['topics'];
                    $data['countTopics'] = Cache::get(session()->getId() . $cbKey)['countTopics'];
                    $data['createTopics'] = Cache::get(session()->getId() . $cbKey)['createTopics'];
                    $data['inExecutionTopics'] = Cache::get(session()->getId() . $cbKey)['inExecutionTopics'];
                    $data['executedTopics'] = Cache::get(session()->getId() . $cbKey)['executedTopics'];
                    $data['openedTopics'] = Cache::get(session()->getId() . $cbKey)['openedTopics'];
                    $data['topicsOpenedPagination'] = $topicsOpenedToDisplay ?? Cache::get(session()->getId() . $cbKey)['topicsOpenedPagination'];
                    $data['closedTopics'] = Cache::get(session()->getId() . $cbKey)['closedTopics'];
                    $data['topicsClosedPagination'] = $topicsClosedToDisplay ?? Cache::get(session()->getId() . $cbKey)['topicsClosedPagination'];
                    $data['presentTopics'] = Cache::get(session()->getId() . $cbKey)['presentTopics'];
                    $data['topicsElapsed'] = Cache::get(session()->getId() . $cbKey)['topicsElapsed'];
                    $data['futureTopics'] = Cache::get(session()->getId() . $cbKey)['futureTopics'];
                    $data['configurations'] = Cache::get(session()->getId() . $cbKey)['configurations'];
                    $data['voteType'] = Cache::get(session()->getId() . $cbKey)['voteType'];
                    $data['parametersMaxCount'] = Cache::get(session()->getId() . $cbKey)['parametersMaxCount'];
                    $data['voteKey'] = Cache::get(session()->getId() . $cbKey)['voteKey'];
                    $data['cb'] = Cache::get(session()->getId() . $cbKey)['cb'];
                    $data['usersNames'] = Cache::get(session()->getId() . $cbKey)['usersNames'];
                    $data['cbKey'] = Cache::get(session()->getId() . $cbKey)['cbKey'];
                    $data['allReadyVoted'] = Cache::get(session()->getId() . $cbKey)['allReadyVoted'];
                    $data['remainingVotes'] = Cache::get(session()->getId() . $cbKey)['remainingVotes'];
                    $data['isModerator'] = Cache::get(session()->getId() . $cbKey)['isModerator'];
                    $data['parameters'] = Cache::get(session()->getId() . $cbKey)['parameters'];
                    $data['cbsMenu'] = Cache::get(session()->getId() . $cbKey)['cbsMenu'];
                    $data['categoriesNameById'] = Cache::get(session()->getId() . $cbKey)['categoriesNameById'];
                    $data['existVotes'] = Cache::get(session()->getId() . $cbKey)['existVotes'];
                    $data['topicsLocation'] = Cache::get(session()->getId() . $cbKey)['topicsLocation'];
                    $data['type'] = Cache::get(session()->getId() . $cbKey)['type'];
                    $data['cbExpiredDate'] = Cache::get(session()->getId() . $cbKey)['cbExpiredDate'];
                    $data['listType'] = Cache::get(session()->getId() . $cbKey)['listType'];
                    $data['submittedProposal'] = Cache::get(session()->getId() . $cbKey)['submittedProposal'];
                    $data['filesByType'] = Cache::get(session()->getId() . $cbKey)['filesByType'];

                    Cache::put(session()->getId() . $cbKey, $data, 30);

                    $viewType = $request->get('listType');

                    if (!is_null($viewType) && $viewType == 'listProposals') {
                        $sections = view('public.' . ONE::getEntityLayout() . '.cbs.' . Cache::get(session()->getId() . $cbKey)['type'] . '.topicsPadsInList', $data)->renderSections();
                        return $sections['topics'] . $sections['scripts'];
                    }
                    $sections = view('public.' . ONE::getEntityLayout() . '.cb.' . Cache::get(session()->getId() . $cbKey)['type'] . '.listTopics', $data)->renderSections();
                    return $sections['topics'] . $sections['scripts'];

                }
            }
            $listType = isset($request->listType) ? $request->listType : 'grid';
            $submittedProposal = isset($request->submit) ? $request->submit : null;

            if (!isset($this->cbType[$request->type])) {
                throw new Exception(trans('error.noCBtype'));
            }
            $type = $this->cbType[$request->type];

            $filesByType = [];
            $cbsMenu = [];
            $topicsLocation = [];
            $parameters = [];

            // array of users
            $usersKeys = [];
            $usersNames = [];
            $categoriesNameById = [];

            $parametersMaxCount = 0;

            $topicsPagination = [];

            $cbAndTopics = CB::getCBAndTopics($cbKey, $request->all());

            //--------------------------------------------------------------------------------------------------

            $cb = $cbAndTopics->cb;
            $topicsData = $cbAndTopics->topics;
            $topics = [];
            $moderators = $cbAndTopics->moderators;
            $configurations = $cbAndTopics->configurations;

            // Check Access
            if (!CB::checkCBsOption($configurations, 'PUBLIC-ACCESS') && !ONE::isAuth()) {
                return redirect()->action('AuthController@login');
            }
            // Check Access
            if (CB::checkCBsOption($configurations, 'TOPIC-NEED-MODERATION')) {
                foreach ($topicsData as $topicData) {
                    if (count($topicData->status) > 0 && isset($topicData->status[0]) && $topicData->status[0]->status_type->code != 'not_accepted') {
                        $topics [] = $topicData;
                    }
                }
            } else {
                $topics = $topicsData;
            }


            foreach ($topics as $topic) {
                /**
                 *  START
                 *  Make pagination for cbs infinite scroll
                 */
                $topicsPagination[] = $topic;

                /**
                 * END
                 */

                $usersKeys[] = $topic->created_by;

                // Add share links to topics (twitter,linkedin,facebook)
                $shareLinks = [];
                $shareLinks["twitter"] = Share::load(action('PublicTopicController@show', [$cbKey, $topic->topic_key, 'type' => $type]), $topic->title)->twitter();
                $shareLinks["linkedin"] = Share::load(action('PublicTopicController@show', [$cbKey, $topic->topic_key, 'type' => $type]), $topic->title)->linkedin();
                $shareLinks["facebook"] = Share::load(action('PublicTopicController@show', [$cbKey, $topic->topic_key, 'type' => $type]), $topic->title)->facebook();
                $topic->shareLinks = $shareLinks;

                $tempParametersCount = 0;
                foreach ($topic->parameters as $parameter) {
                    $name = $parameter->parameter;
                    $code = $parameter->code;
                    $value = $parameter->pivot->value;


                    if ($value != null) {
                        $tempParametersCount++;
                    }


                    $parameterOptions = [];
                    $options = $parameter->options;


                    foreach ($options as $option) {
                        $parameterOptions[] = array('id' => $option->id, 'name' => $option->label);
                        $categoriesNameById[$option->id] = $option->label;
                        if ($code == 'category') {
                            if ($parameter->pivot->value == $option->id) {
                                $categories[] = $option->label;
                                $topic->topic_category = $option->label;
                            }

                        }
                    }
                    $parameters[$code] = array('id' => $parameter->id, 'name' => $name, 'filter' => $parameter->use_filter, 'code' => $parameter->code, 'options' => $parameterOptions);

                    if ($parameter->type->code != 'image_map') {
                        $temp = '';
                        if (array_key_exists($value, $cbsMenu)) {
                            $temp = $cbsMenu[$value];
                            $temp .= ',' . $topic->id;
                        } else {
                            $temp .= $topic->id;
                        }

                        $cbsMenu[$value] = $temp;
                    } else {

                        $location = ONE::verifyEmpavilleGeoArea($value);

                        if ($location != "") {
                            $temp = '';
                            if (array_key_exists($location, $topicsLocation)) {
                                $temp = $topicsLocation[$location];
                                $temp .= ',' . $topic->id;
                            } else {
                                $temp .= $topic->id;
                            }

                            $topicsLocation[$location] = $temp;
                        }
                    }
                }

                if ($parametersMaxCount < $tempParametersCount) {
                    $parametersMaxCount = $tempParametersCount;
                }

                if (isset($topic->last_post->created_by)) {
                    $usersKeys[] = $topic->last_post->created_by;
                }

                $filesByType[$topic->topic_key] = CB::listFilesByType($topic->last_post->post_key);

            }

            $topicsPagination = new Paginator($topicsPagination, $numberTopicToShow, $request->page);

            if (count($usersKeys) > 0) {
                $usersNames = Auth::getPublicListNames($usersKeys);
            }
            $isModerator = 0;
            if (Session::has('user')) {

                if (One::isAdmin()) {
                    $isModerator = 1;
                } else {
                    //Get Managers
                    foreach ($moderators as $moderator) {

                        if ($moderator->user_key == Session::get('user')->user_key) {
                            $isModerator = 1;
                            break;
                        }
                    }
                }
            }

            $cbVotes = CB::getCbVotes($cbKey);
            foreach ($cbVotes as $cbVote) {
                $voteResults[] = Vote::getVoteResults($cbVote->vote_key);
            }

            $topicsPagination = new Paginator($topics, $numberTopicToShow, $request->page);

            // Check for create topics permission
            $createTopics = ($isModerator == 1
                || (ONE::checkCBsOption($configurations, 'CREATE-TOPIC') && !empty(Session::get('X-AUTH-TOKEN', null)))) ?: false;

            // Prepare data to send to the view
            $data = [];

            $data['voteResults'] = isset($voteResults) ? $voteResults : null;
            $data['categoryColors'] = isset($categoryColors) ? $categoryColors : null;
            $data['topicsTotals'] = $topics;
            $data['topicsPagination'] = $topicsPagination;
            $data['topics'] = $topics;
            $data['countTopics'] = sizeof($topics);
            $data['createTopics'] = $createTopics;
            $data['configurations'] = $configurations;

            $data['parametersMaxCount'] = $parametersMaxCount;
            $data['cb'] = $cb;
            $data['usersNames'] = $usersNames;
            $data['cbKey'] = $cbKey;

            $data['isModerator'] = $isModerator;
            $data['parameters'] = $parameters;
            $data['cbsMenu'] = $cbsMenu;
            $data['categoriesNameById'] = $categoriesNameById;

            $data['topicsLocation'] = $topicsLocation;
            $data['type'] = $type;
            $data['listType'] = $listType;
            $data['submittedProposal'] = $submittedProposal;
            $data['filesByType'] = $filesByType;

            Cache::put(session()->getId() . $cbKey, $data, 30);
            /*if($listType == 'list'){
                return view('public.'.ONE::getEntityLayout().'.cbs.'.$type.'.topicsInList', $data)->renderSections()['topics|scripts'];
            }
            if(!empty($request->status_view) && $request->status_view == 'closed'){
                return view('public.'.ONE::getEntityLayout().'.cbs.'.$type.'.closedTopics.list', $data)->renderSections()['topics|scripts'];

            }*/
            return view('public.' . ONE::getEntityLayout() . '.cbs.pilot.topicsHomepage', $data);
            //return $sections['topics'].$sections['scripts'];

        } catch (Exception $e) {
            return response()->json(['error' => 'Erro'], 500);
        }
    }

    /**
     * @param Request $request
     * Get CB topics list with a ajax request
     * @return $this|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getCbTopicsList(Request $request)
    {
        try{
            $view = $this->show($request, $request['cb_key']);
            $sections = $view->with(["isCMSSection"=> true])->renderSections();
            if (!empty($sections) && array_key_exists("topics", $sections))
                return $sections['topics'] . ($sections['scripts'] ?? "");
            else
                return $view->render();
        } catch (Exception $e) {
            return response()->json(['error' => 'Erro'], 500);
        }
    }

    /**
     * Display a resource.
     * @param Request $request
     * @return $this|\BladeView|bool|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showCbsWithTopics(Request $request)
    {
        try {
            $type = 'proposal';
            $cbsData = [];
            // Get CB Keys from Orchestrator
            $cbs = Orchestrator::getCbTypes($type);

            $cbsData = CB::getListCBs($cbs);
            $newCbs = [];
            foreach ($cbsData as $cbTemp) {
                if ($cbTemp->start_date <= Carbon::now() && ($cbTemp->end_date >= Carbon::now() || !$cbTemp->end_date)) {
                    $newCbs[] = $cbTemp;
                }
            }

            $cbAndTopics = [];

            // Data not found / No keys
            if (count($cbs) == 0) {
                return view('public.' . ONE::getEntityLayout() . '.cbs.' . $type . '.cbsWithTopics', compact('cbsData', 'usersNames', 'type', 'cbAndTopics'));
            }

            // Get data to list CBs
            $cbsData = CB::getListCBs($cbs);

            $totalUsersRegistered = Orchestrator::getTotalUsersRegistered();
            $totalCbsVotes = $this->getCbsTotalVotes($cbsData);
            $voteType = [];
            $votesByCb = [];
            $voteResultsByCB = [];

            $cbsMenu = [];
            $topicsLocation = [];
            $parameters = [];

            // array of users
            $usersKeys = [];
            $usersNames = [];
            $categoriesNameById = [];

            $parametersMaxCount = 0;

            $notSubmitted = true;

            foreach ($cbsData as $cbTemp) {
                if ($cbTemp->start_date <= Carbon::now() && ($cbTemp->end_date >= Carbon::now() || !$cbTemp->end_date)) {
                    $cbWithTopics = CB::getCBAndTopics($cbTemp->cb_key);
                    $cbAndTopics [] = $cbWithTopics;
                    $cb = $cbWithTopics->cb;
                    $topics = $cbWithTopics->topics;
                    $moderators = $cbWithTopics->moderators;
                    $configurations = $cbWithTopics->configurations;
                    // Check Access
                    if (!CB::checkCBsOption($configurations, 'PUBLIC-ACCESS') && !ONE::isAuth()) {
                        return redirect()->action('AuthController@login');
                    }
                    foreach ($cbWithTopics->topics as $topic) {
                        $usersKeys[] = $topic->created_by;

//                        // Add share links to topics (twitter,linkedin,facebook)
//                        $shareLinks = [];
//                        $shareLinks["twitter"] = Share::load(action('PublicTopicController@show',  [$cbTemp->cb->cb_key , $topic->topic_key, 'type' => $type] ), $topic->title )->twitter();
//                        $shareLinks["linkedin"] = Share::load(action('PublicTopicController@show', [$cbTemp->cb->cb_key , $topic->topic_key, 'type' => $type] ), $topic->title )->linkedin();
//                        $shareLinks["facebook"] = Share::load(action('PublicTopicController@show', [$cbTemp->cb->cb_key , $topic->topic_key, 'type' => $type] ), $topic->title )->facebook();
//
//                        $topic->shareLinks = $shareLinks;

                        $tempParametersCount = 0;
                        foreach ($topic->parameters as $parameter) {
                            $name = $parameter->parameter;
                            $code = $parameter->code;
                            $value = $parameter->pivot->value;

                            if ($value != null) {
                                $tempParametersCount++;
                            }


                            $parameterOptions = [];
                            $options = $parameter->options;

                            foreach ($options as $option) {
                                $parameterOptions[] = array('id' => $option->id, 'name' => $option->label);
                                $categoriesNameById[$option->id] = $option->label;
                            }
                            $parameters[$code] = array('id' => $parameter->id, 'name' => $name, 'options' => $parameterOptions);

                            if ($parameter->type->code != 'image_map') {
                                $temp = '';
                                if (array_key_exists($value, $cbsMenu)) {
                                    $temp = $cbsMenu[$value];
                                    $temp .= ',' . $topic->id;
                                } else {
                                    $temp .= $topic->id;
                                }

                                $cbsMenu[$value] = $temp;
                            } else {

                                $location = ONE::verifyEmpavilleGeoArea($value);

                                if ($location != "") {
                                    $temp = '';
                                    if (array_key_exists($location, $topicsLocation)) {
                                        $temp = $topicsLocation[$location];
                                        $temp .= ',' . $topic->id;
                                    } else {
                                        $temp .= $topic->id;
                                    }

                                    $topicsLocation[$location] = $temp;
                                }
                            }
                        }

                        if ($parametersMaxCount < $tempParametersCount) {
                            $parametersMaxCount = $tempParametersCount;
                        }

                        if (isset($topic->last_post->created_by)) {
                            $usersKeys[] = $topic->last_post->created_by;
                        }
                    }
                    // check if exist votes
                    $existVotes = 0;
                    $existVotesForSubmit = false;
                    $voteKey = '';
                    $allReadyVoted = [];
                    $remainingVotes = 0;
                    $voteType = [];
                    $voteResult = [];
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
                        if (ONE::isAuth()) {

                            $vConfigurations = [];

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
                            $voteStatus = Vote::getVoteStatus($voteKey);
                            $totalSummary = [];
                            if ($voteStatus->total_summary) {
                                $totalSummary = collect($voteStatus->total_summary)->toArray();
                            }
                            if ($voteStatus->vote) {
                                $existVotes = 1;
                                if (count($voteStatus->votes) > 0 || (count($voteStatus->votes) == 0 && $voteStatus->remaining_votes->total > 0)) {
                                    $existVotesForSubmit = true;
                                }
                            } else {
                                $existVotes = 0;
                            }
                            $remainingVotes = $voteStatus->remaining_votes;

                            $generalSubmit = isset($voteStatus->can_vote) ? $voteStatus->can_vote : false;
                            if (!$generalSubmit) {
                                $notSubmitted = $generalSubmit;
                            }

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
                            $voteType[] = [
                                "method"                => $methodName,
                                "key"                   => $voteKey,
                                "remainingVotes"        => $remainingVotes,
                                "existVotes"            => $existVotes,
                                "allReadyVoted"         => $allReadyVoted,
                                "totalSummary"          => $totalSummary,
                                "eventVote"             => $eventVote,
                                "totalVotes"            => isset($voteStatus->total_votes) ? json_decode(json_encode($voteStatus->total_votes), true) : null,
                                "configurations"        => $vConfigurations,
                                "genericConfigurations" => $genericConfigurations];
                        } else {
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
                            $voteResult[] = ['results' => collect(Vote::getVoteResults($vote->vote_key)->total_summary)->toArray(), 'method' => $methodName];
                            $voteResultsByCB [$cbTemp->cb_key] = $voteResult;
                        }
                    }
                    $votesByCb[$cbTemp->cb_key] = $voteType;
                }
            }
            if (count($usersKeys) > 0) {
                $usersNames = json_decode(json_encode(Auth::getPublicListNames($usersKeys)), true);
            }
            $isModerator = 0;
            if (Session::has('user')) {

                if (One::isAdmin()) {
                    $isModerator = 1;
                } else {
                    //Get Managers
                    foreach ($moderators as $moderator) {

                        if ($moderator->user_key == Session::get('user')->user_key) {
                            $isModerator = 1;
                            break;
                        }
                    }
                }
            }
            // Check for create topics permission
            $createTopics = ($isModerator == 1
                || (ONE::checkCBsOption($configurations, 'CREATE-TOPIC') && !empty(Session::get('X-AUTH-TOKEN', null)))
                /*|| ONE::checkCBsOption($configurations, 'CREATE-TOPICS-ANONYMOUS') */) ?: false;

            // Prepare data to send to the view
            $data = [];
            $data['createTopics'] = $createTopics;
            $data['voteType'] = $voteType;
            $data['parametersMaxCount'] = $parametersMaxCount;
            $data['usersNames'] = $usersNames;
            $data['isModerator'] = $isModerator;
            $data['parameters'] = $parameters;
            $data['cbsMenu'] = $cbsMenu;
            $data['categoriesNameById'] = $categoriesNameById;
            $data['topicsLocation'] = $topicsLocation;
            $data['type'] = $type;
            $data['cbAndTopics'] = $cbAndTopics;
            $data['votesByCb'] = $votesByCb;
            $data['notSubmitted'] = $notSubmitted;
            $data['voteResultsByCB'] = $voteResultsByCB;
            $data['existVotesForSubmit'] = $existVotesForSubmit;
            $data['totalUsersRegistered'] = $totalUsersRegistered;
            $data['totalCbsVotes'] = $totalCbsVotes;
            $cbs = $newCbs;
            $data['cbs'] = $cbs;
            $data['cbsData'] = $cbsData;
            $data['usersNames'] = $usersNames;
            $data['type'] = $type;
            $data['cbAndTopics'] = $cbAndTopics;

            return view('public.' . ONE::getEntityLayout() . '.cbs.' . $type . '.cbsWithTopics', $data);

        } catch (Exception $e) {
            return redirect()->back()->withErrors(["topic.edit" => $e->getMessage()]);
        }
    }

    /**
     * Display a resource.
     * @param Request $request
     * @return string
     */
    public function submitVotes(Request $request)
    {
        $data = [];

        foreach ($request->voteCbs as $voteEvents) {
            foreach ($voteEvents as $event) {
                $data[] = $event['key'];
            }
        }

        $user = Auth::getUser();

//        $emailType = 'vote_submitted';
//        $tags = [
//            "name" => $user->name,
//            "link" => URL::action("PublicQController@intro", 'rd60c5k5xchm0qNFg81zH7ZYc0TziWtT')
//        ];  // Email / Notify

//        $response = Notify::sendEmail($emailType, $tags, (array)$user);

        $response = Vote::submitVoting($data);
        if ($response) {
            return "true";
            // $questionnaire = URL::action("PublicQController@intro", 'rd60c5k5xchm0qNFg81zH7ZYc0TziWtT');
            // return $questionnaire;
        }
        return 'submit_error';
    }

    public function simpleSubmitVotes(Request $request)
    {
        $data = $request->eventKeys;
        $response = Vote::submitVoting($data);
        if ($response) {
            return "true";
        }
        return 'submit_error';
    }

    public function genericSubmitVotes(Request $request)
    {
        try {

            if (!empty($request->get("eventKey",""))) {
                if (!empty(Session::get("SITE-CONFIGURATION.user_email_domain","")) || !ends_with(Auth::getUser()->email,Session::get("SITE-CONFIGURATION.user_email_domain",""))) {

                    $response = Vote::submitVoting($request->get("eventKey"),null, true);

                    if (count($response)>0) {
                        $firstVote = collect($response)->first();
                        /* <Submission Timestamp>.<User key>.<Event ID>/<Number of Votes Submitted> */
                        $uniqueID = Carbon::parse($firstVote->updated_at)->timestamp . "." . Auth::getUser()->user_key . "." . $firstVote->event_id . "/" . count($response);

                        $topicKeys = [];
                        foreach ($response as $vote) {
                            $topicKeys[] = $vote->vote_key;
                        }
                        //$topics = CB::getTopicsByKeys($topicKeys);

                        //if (count($topics) > 0) {
//                            $tags = [
//                                "name" => Auth::getUser()->name,
//                                "votesCount" => count($response),
//                                "voteList" => view('public.' . ONE::getEntityLayout() . '.cbs.submittedVotesReceiptList', ["topics" => $topics])->render(),
//                                "uniqueID" => $uniqueID
//                            ];
//                            try {
//                                Notify::sendEmail('vote_submitted', $tags, (array)Auth::getUser());
//                            } catch (Exception $e) {
//
//                            }
                        // }
                    }
                } else{
                    $response = Vote::submitVoting($request->get("eventKey"));
                }

                if ($response) {
                    if (!empty($request->get("redirectAction","")))
                        return response()->json(["success"=>true, "redirect"=> action($request->get("redirectAction"))]);
                    else
                        return response()->json(["success"=>true]);
                }
            }
        } catch (Exception $e) {
            return response()->json(["error"=>true],500);
        }

        return response()->json(["error" => true],500);
    }

    public function votesSubmittedSuccessfuly(Request $request, $cbKey) {
        try {
            // $data["cb"] = CB::getCBAndTopics($cbKey);
            $data["type"] = $request->type;
            $data['cbKey'] = $cbKey;

            $cb = CB::getCbByKey($cbKey);
            if( !empty($cb->template) ) {
                return view('public.' . ONE::getEntityLayout() . '.cb.' . $cb->template . '.votesSubmitted', $data);
            }else {
                return view('public.' . ONE::getEntityLayout() . '.cb.default.votesSubmitted', $data);
            }
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["cb.votesSubmittedSuccessfuly" => $e->getMessage()]);
        }
    }

    /**
     * Show all location in Maps for Topics.
     * @param Request $request
     * @param $cbKey
     * @return $this|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function generalMap(Request $request, $cbKey)
    {
        try {
            //$topicsStatus = $request->topicsStatus;
            $type = $request->type;
            $listType = 'map';
            $cb = CB::getCBAndTopics($cbKey);
            $configurations = $cb->configurations;
            $i = 0;
            $locations = [];
            $topics = [];
            $topicsData = $cb->topics;
            //total topics to be sent to summary boxes
            $countTopics = count($cb->topics);

            // Check Access
            if (CB::checkCBsOption($configurations, 'TOPIC-NEED-MODERATION')) {
                foreach ($topicsData as $topicData) {
                    if (count($topicData->status) > 0 && isset($topicData->status[0]) && $topicData->status[0]->status_type->code != 'not_accepted') {
                        $topics [] = $topicData;
                    }
                }
            } else {
                $topics = $topicsData;
            }

            //counters for totals in summary boxes
            $totalCbComments = 0;
            $totalCbLikes = 0;
            $inExecutionTopics = [];


            foreach ($topics as $topic) {


                //Cb total comments counter
                $totalCbComments += isset($topic->statistics->posts_counter) ? $topic->statistics->posts_counter : 0;
                //Cb total likes counter
                $totalCbLikes += isset($topic->statistics->like_counter) ? $topic->statistics->like_counter : 0;

                //Cb count total topics in execution
                if (!empty($topic->status[0]->status_type) && ($topic->status[0]->status_type->code == "accepted" ||
                        $topic->status[0]->status_type->code == "in_execution")
                ) {
                    $inExecutionTopics[] = $topic;
                }

                $obj = collect($topic->parameters)->keyBy('code')->all();

                if (!empty($obj["google_maps"])) {


                    $link = action('PublicTopicController@show', [$cbKey, $topic->topic_key, 'type' => $this->cbType[$request->type]]);

                    $paramsList = '';
                    if (!empty($topic->parameters)) {

                        // Prepares a LIST with topic parameters to be shown in map dialog
                        foreach ($topic->parameters as $key => $param) {

                            if ($param->code != 'google_maps') {

                                if ($param->type->code == "dropdown" || $param->type->code == "category" || $param->type->code == "budget") {
                                    foreach ($param->options as $option) {
                                        if ($param->pivot->value == $option->id) {
                                            $paramsList .= '<li> <i class="fa fa-folder-open" aria-hidden="true"></i>&nbsp;<span>' . $option->label . '</span></li>';
                                        }
                                    }
                                } else {
                                    $paramsList .= '<li><i class="fa fa-folder-open" aria-hidden="true"></i>&nbsp;<span>' . $param->pivot->value . '</span></li>';
                                }
                            }
                        }
                    }

                    $googleMapStatusLabel = (!empty($topic->status)) ? $topic->status[0]->status_type->description : '' ;

                    //shows topic number (if != 0) along with the title in google maps popup
                    if (!empty($topic->topic_number)) {

                        $locations[$i][0] = '<div class=\"map-dialog-container\"><div class=\"map-dialog-title-container\"><div class=\"map-dialog-title\">#' . $topic->topic_number . '. ' . $googleMapStatusLabel . ' - ' . str_replace("'", "&apos;" ,$topic->title) . '</div><ul class=\"map-dialog-ul\">' . $paramsList . '</ul></div><a class=\"map-dialog-a btn btn-outlined-default-layout btn-success \" href=\"' . $link . '\" target=\"_blank\">' . trans('PublicCbs.viewMore') . '</a></div>';
                    } else {
                        $locations[$i][0] = '<div class=\"map-dialog-container\"><div class=\"map-dialog-title-container\"><div class=\"map-dialog-title\">' . $googleMapStatusLabel . ' - ' . str_replace("'", "&apos;" ,$topic->title) . '</div><ul class=\"map-dialog-ul\">' . $paramsList . '</ul></div><a class=\"map-dialog-a btn btn-outlined-default-layout btn-success\" href=\"' . $link . '\" target=\"_blank\">' . trans('PublicCbs.viewMore') . '</a></div>';
                    }

                    //$link = action('PublicTopicController@show', [$cbKey, $topic->topic_key, 'type' => $this->cbType[$request->type]]);
                    $coords = explode(",", $obj["google_maps"]->pivot->value);
                    if (count($coords) > 0) {
                        $locations[$i][1] = $coords[0];
                        $locations[$i][2] = $coords[1];
                        if (!empty($obj["category"])) {
                            $category = "";
                            foreach ($obj["category"]->options as $option) {
                                if ($obj["category"]->pivot->value == $option->id) {
                                    $category = $option->label;
                                }
                            }
                            $locations[$i][3] = strtolower(str_replace(" ", "_", $category));
                        }
                        $i++;
                    }
                }

            }

            // Get Total votes on CB
            $eventsKeyList = Cb::getCbVoteEvents($cbKey);
            if(!empty($eventsKeyList)){
                $absoluteTotalVotes = Vote::getCbTotalVotes($eventsKeyList);
            }else{
                $absoluteTotalVotes = [];
            }

            //send number of topics without geo-mapping
            $totalMapTopics = count($locations);
            $totalTopics = count($cb->topics);
            $totalNoMapTopics = $totalTopics - $totalMapTopics;

            $data = [];

            $data['locations'] = $locations;
            $data['cbKey'] = $cbKey;
            $data['type'] = $type;
            $data['listType'] = $listType;
            $data['configurations'] = $configurations;
            $data['cb'] = $cb;
            $data['totalNoMapTopics'] = $totalNoMapTopics;
            /* Info to be used in summary boxes*/
            $data['totalCbComments'] = $totalCbComments;
            $data['absoluteTotalVotes'] = $absoluteTotalVotes;
            $data['inExecutionTopics'] = $inExecutionTopics;
            $data['countTopics'] = $countTopics;


            return view('public.' . ONE::getEntityLayout() . '.cbs.generalMap', $data);

        } catch (Exception $e) {
            return redirect()->back()->withErrors(["cbs.generalMap" => $e->getMessage()]);
        }
    }


    /**
     * Show all location in Maps for Topics. AJAX
     * @param Request $request
     * @return $this|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getCbTopicsListMap(Request $request)
    {


        try {

            $cbKey = $request['cb_key'];
            $type = $request['type'];
            $listType = 'map';

            /** Get filter list from request
             *  filter_list request its a json string and needs to be decoded
             *  Collect json object and convert to php array with laravel collection
             *  Send $filterList in cb method
             */
            $filterList = [];
            if(!empty($request['filter_list'])){
                $filterList = json_decode($request['filter_list']);
                $filterList = collect($filterList)->toArray();
            }

            // $cb = CB::getCBAndTopics($cbKey,$filterList);
            // $topics = $cb->topics;

            //$request['only_maps_information'] = true;
            // $request['only_basic_information'] = true;

            $cb = CB::getPublicPadParticipation($cbKey, null, null, $request->all());

            // dd($cb[0]->topics);

            $topics = $cb[0]->topics;


            /*
            $topicsData = $cb->topics;
            $countTopics = count($cb->topics);
            $totalCbLikes = 0;
            $configurations = $cb->configurations;
            $totalCbComments = 0;
            */
            $i = 0;

            // $topics = [];
            $locations = [];
            $inExecutionTopics = [];

            /** THE USER CAN SEE THE TOPIC IF IT IS MODERATED */
            /*if (CB::checkCBsOption($configurations, 'TOPIC-NEED-MODERATION')) {
                foreach ($topicsData as $topicData) {
                    if (count($topicData->status) > 0 && isset($topicData->status[0]) && $topicData->status[0]->status_type->code == 'moderated') {
                        $topics [] = $topicData;
                    }
                }
            } else {
                $topics = $topicsData;
            }

            if(isset($request->filters)){
                $data['filters'] = $request->filters;
                $filters = array_filter(json_decode($request->filters));
                $filteredTopics = [];
                foreach ($topics as $topic){
                    if($this->topicAsParameter($topic->parameters,$filters)){
                        $filteredTopics[] = $topic;
                    }
                }
                $topics = $filteredTopics;
            }*/

            foreach ($topics as  $topic) {
                //GET TOPIC PARAMETERS
                if(!empty($topic->parameters)) {
                    $obj = collect($topic->parameters)->keyBy('code')->all();
                    //CHECK IF IT HAS LOCATION
                    if (!empty($obj["google_maps"])) {
                        $link = action('PublicTopicController@show', [$cbKey, $topic->topic_key, 'type' => $this->cbType[$request['type']]]);
                        $paramsList = '';
                        if (!empty($topic->parameters)) {
                            // Prepares a LIST with topic parameters to be shown in map dialog
                            foreach ($topic->parameters as $key => $param) {
                                if (!empty($param->code) && $param->code != 'google_maps') {
                                    if ($param->visible_in_list) {
                                        if ($param->type->code == "dropdown" || $param->type->code == "category") {
                                            foreach ($param->options as $option) {
                                                if ($param->pivot->value == $option->id) {
                                                    $paramsList .= '<li class="black bold" style="display: block; font-size:13px;"> <i class="fa fa-tag" aria-hidden="true"></i>&nbsp;<span>' . addslashes($option->label) . '111</span></li>';
                                                }
                                            }
                                        } else {
                                            $paramsList .= '<li class="black bold" style="display: block; font-size:13px;"><i class="fa fa-tag" aria-hidden="true"></i>&nbsp;<span>' . addslashes($param->pivot->value) . '</span></li>';
                                        }
                                    }
                                }
                            }
                        }


                        // $googleMapStatusLabel = (!empty($topic->status)) ? $topic->status[0]->status_type->description : '';

                        $locations[$i][0] = '<a href="' . $link . '" class="black"><div id="iw-container">' .
                            '<div class="iw-title">'.trim(preg_replace('/\s+/', ' ',str_replace("'", " ", $topic->title))).'</div>' .
                            '<div class="iw-content">';
                        $locations[$i][0] .= '<p style="width:265px;">';
                        if ($topic->contents == '') {
                            $topic->first_post->contents = trim(preg_replace('/\s+/', ' ',str_replace("'", " ", $topic->first_post->contents)));
                            // $locations[$i][0] .=  $topic->first_post->contents;
                        } else {
                            $topic->contents = trim(preg_replace('/\s+/', ' ',str_replace("'", " ", $topic->contents)));
                            $locations[$i][0] .=  $topic->contents;
                        }

                        $locations[$i][0] .= '</p>';
                        $locations[$i][0] .= $paramsList;

                        $locations[$i][0] .='</div>' .
                            '<div class="iw-bottom-gradient"></div>' .
                            '</div></a>';

                        //$link = action('PublicTopicController@show', [$cbKey, $topic->topic_key, 'type' => $this->cbType[$request->type]]);
                        $coords = explode(",", $obj["google_maps"]->pivot->value);


                        if (count($coords) > 0) {
                            $locations[$i][1] = $coords[0];
                            $locations[$i][2] = $coords[1];


                            if (!empty($obj["category"])) {
                                $category = "";
                                foreach ($obj["category"]->options as $option) {
                                    if ($obj["category"]->pivot->value == $option->id) {
                                        $category = $option->label;
                                    }
                                }
                                $locations[$i][3] = strtolower(str_replace(" ", "_", $category));
                            }


                            $locations[$i][4] = json_decode(OneCbs::getParameterOption($topic->parameters, "category", "pin"));
                            $locations[$i][5] = $category ?? '';


                            $i++;
                        }
                    }
                }
            }


            // Get Total votes on CB
            /*
            $eventsKeyList = Cb::getCbVoteEvents($cbKey);
            if(!empty($eventsKeyList)){
                $absoluteTotalVotes = Vote::getCbTotalVotes($eventsKeyList);
            }else{
                $absoluteTotalVotes = [];
            }
            */

            //send number of topics without geo-mapping
            $totalMapTopics = count($locations);
            $totalTopics = count($topics);
            $totalNoMapTopics = $totalTopics - $totalMapTopics;

            /** Categories for help - show in map view*/
            $categoriesHelp = [];
            if (!empty($cb->cb->parameters)) {
                $params = collect($cb->cb->parameters)->keyBy('code')->all();
                foreach(!empty($params["category"]->options) ? $params["category"]->options : [] as $option) {
                    $pin = isset($option->pin) ? json_decode($option->pin) : null;
                    $categoriesHelp [] = ['category' => $option->label, 'pin' => $pin];
                }
            }

            $data = [];
            $data['categoriesHelp'] = $categoriesHelp;
            $data['locations'] = $locations;
            $data['cbKey'] = $cbKey;
            $data['type'] = $type;
            $data['listType'] = $listType;
            // $data['configurations'] = $configurations;
            $data['cb'] = $cb;
            $data['totalNoMapTopics'] = $totalNoMapTopics;
            //$data['topics'] = $topicsData;
            /* Info to be used in summary boxes*/
            // $data['totalCbComments'] = $totalCbComments;
            // $data['absoluteTotalVotes'] = $absoluteTotalVotes;
            $data['inExecutionTopics'] = $inExecutionTopics;
            //$data['countTopics'] = $countTopics;

            return view('public.' . ONE::getEntityLayout() . '.cb.generalMap', $data);

        } catch (Exception $e) {
            return redirect()->back()->withErrors(["cb.generalMap" => $e->getMessage()]);
        }
    }


    private function getCbsTotalVotes($cbsData)
    {
        $totalVotes = 0;
        foreach ($cbsData as $cbTemp) {

            if ($cbTemp->start_date <= Carbon::now() && ($cbTemp->end_date >= Carbon::now() || !$cbTemp->end_date)) {

                $cbVotes = CB::getCbVotes($cbTemp->cb_key);

                foreach ($cbVotes as $vote) {
                    $voteResult = Vote::getVoteResults($vote->vote_key);
                    if (isset($voteResult->total_summary)) {
                        foreach ($voteResult->total_summary as $result) {
                            $totalVotes += $result->positive;
                        }
                    }
                }
            }
        }

        return $totalVotes;
    }

    public function showTopicsVoted(Request $request, $cbKey) {
        try {
            $numberTopicToShow = $request->get("topics_to_show",6);

            $cbVotes = CB::getCbVotes($cbKey);
            $type = $request->type;
            $eventKeys = [];
            foreach ($cbVotes as $cbVote) {
                $eventKeys[$cbVote->vote_key] = $cbVote->vote_key;
            }

            $userEventVotes = Vote::getUserVotesForEvents($eventKeys);
            $userTopicsVoted = [];
            foreach ($userEventVotes as $userEventVote) {
                $userTopicsVoted[$userEventVote->vote_key] = $userEventVote->vote_key;
            }

            $cbAndTopics = CB::getCBAndTopicKeys($cbKey, $userTopicsVoted, $numberTopicToShow, $request->get("page",null), $request->all());

            $cbParameters = CB::getCbParametersOptions($cbKey);
            foreach ($cbParameters->parameters as $parameter) {
                $name = $parameter->parameter;
                $code = $parameter->code;

                $parameterOptions = [];
                $options = $parameter->options;
                foreach ($options as $option) {

                    if ($parameter->id == $request->parameter_id and $option->id == $request->option_id) {
                        $filterOptionSelected = array('parameter_id' => $parameter->id, 'parameter_name' => $parameter->parameter, 'option_id' => $option->id, 'label' => $option->label);
                    }

                    $parameterOptions[] = array('id' => $option->id, 'name' => $option->label);
                    $categoriesNameById[$option->id] = $option->label;

                }
                $parameters[$code] = array('id' => $parameter->id, 'name' => $name, 'filter' => $parameter->use_filter, 'code' => $parameter->code, 'options' => $parameterOptions);
            }

            $topics = [];
            $cb = $cbAndTopics->cb;
            $topicsData = $cbAndTopics->topics;


            $moderators = $cbAndTopics->moderators;
            $configurations = $cbAndTopics->configurations;
            $statusTypes = $cbAndTopics->statusTypes;
            $voteKeys = $cbAndTopics->voteKeys;

            if (!CB::checkCBsOption($configurations, 'PUBLIC-ACCESS') && !ONE::isAuth())
                return redirect()->action('AuthController@login');

            // Check Access

//            topic_as_private_questionnaire
            if (CB::checkCBsOption($configurations, 'TOPIC-NEED-MODERATION')) {
                foreach ($topicsData as $topicData) {
                    if (count($topicData->status) > 0 && isset($topicData->status[0]) && $topicData->status[0]->status_type->code != 'not_accepted')
                        $topics [] = $topicData;
                }
            } else
                $topics = $topicsData;

            foreach ($topics as $topic) {
                $topic->closed = (!empty($topic->active_status) && $topic->active_status->status_type->code != 'moderated');
                foreach ($topic->parameters as $parameter) {
                    $code = $parameter->code;
                    $parameterOptions = [];
                    $options = $parameter->options;

                    foreach ($options as $option) {
                        $parameterOptions[] = array('id' => $option->id, 'name' => $option->label);
                        $categoriesNameById[$option->id] = $option->label;
                        if ($code == 'category') {
                            if ($parameter->pivot->value == $option->id) {
                                $categories[] = $option->label;
                                $topic->topic_category = $option->label;
                            }
                        }
                    }
                }
                /**
                 *  START
                 *  Make pagination for cbs infinite scroll
                 */
                $topicsPagination[] = $topic;

                /**
                 * END
                 */

                $usersKeys[] = $topic->created_by;

                // Add share links to topics (twitter,linkedin,facebook)
                $shareLinks = [];
                $shareLinks["twitter"] = Share::load(action('PublicTopicController@show', [$cbKey, $topic->topic_key, 'type' => $type]), $topic->title)->twitter();
                $shareLinks["linkedin"] = Share::load(action('PublicTopicController@show', [$cbKey, $topic->topic_key, 'type' => $type]), $topic->title)->linkedin();
                $shareLinks["facebook"] = Share::load(action('PublicTopicController@show', [$cbKey, $topic->topic_key, 'type' => $type]), $topic->title)->facebook();
                $topic->shareLinks = $shareLinks;

                if (isset($topic->last_post->created_by)) {
                    $usersKeys[] = $topic->last_post->created_by;
                }


            }

            $topicFiles = CB::getTopicsFilesByType($topics);
            if(!empty($topicFiles)) {
                foreach ($topicFiles as $key => $file) {
                    $filesByType[$key] = $file;
                }
            }
            if (isset($usersKeys) && count($usersKeys) > 0)
                $usersNames = Auth::getPublicListNames($usersKeys);

            $isModerator = 0;
            if (Session::has('user')) {
                if (One::isAdmin()) {
                    $isModerator = 1;
                } else {
                    //Get Managers
                    foreach ($moderators as $moderator) {

                        if ($moderator->user_key == Session::get('user')->user_key) {
                            $isModerator = 1;
                            break;
                        }
                    }
                }
            }

            // check if exist votes
            $existVotes = 0;
            $voteKey = '';
            $allReadyVoted = [];
            $remainingVotes = 0;
            $voteType = [];

            $cbVotes = CB::getCbVotes($cbKey);
            if (ONE::isAuth()) {
                $eventsResponse = Vote::getAllShowEventsNoTranslation($voteKeys);

                //index of array = key of event
                $eventVotes = [];
                foreach ($eventsResponse as $ev) {
                    $eventVotes[$ev->key] = $ev;
                }

                $existVotes = 0;

                foreach ($cbVotes as $vote) {
                    $vConfigurations = [];

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
                    $voteStatus = Vote::getVoteStatus($voteKey);
                    if ($voteStatus->vote) {
                        $existVotes = 1;
                    } else {
                        $existVotes = 0;
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

                    $voteType[] = [
                        "name" => isset($cbAndTopics->votes->$voteKey) ? $cbAndTopics->votes->$voteKey->name : null,
                        "method" => $methodName,
                        "key" => $voteKey,
                        "remainingVotes" => $remainingVotes,
                        "existVotes" => $existVotes,
                        "allReadyVoted" => $allReadyVoted,
                        "eventVote" => $eventVote,
                        "totalVotes" => isset($voteStatus->total_votes) ? json_decode(json_encode($voteStatus->total_votes), true) : null,
                        "alreadySubmitted" => $voteStatus->alreadySubmitted ?? false,
                        "configurations" => $vConfigurations,
                        "genericConfigurations" => $genericConfigurations,
                        "canVote" => $voteStatus->can_vote ?? true,
                        "canUnSubmit"           => $canUnSubmit ?? false,
                        "submitedDate"          => isset($voteStatus->submited_date) ? $voteStatus->submited_date->date : null
                    ];
                }
            }

            $presentTopics = [];
            $topicsElapsed = [];
            $futureTopics = [];
            if ($type == "publicConsultation" || $type == "tematicConsultation" || $type == "survey") {

                foreach ($topics as $topic) {

                    if (empty($topic->start_date) && empty($topic->end_date)) {
                        $presentTopics[] = $topic;
                    } else {

                        $firstDate = Carbon::createFromFormat('Y-m-d H:i:s', $topic->start_date . ' 00:00:00');
                        $secondDate = Carbon::createFromFormat('Y-m-d H:i:s', $topic->end_date . ' 23:59:59');

                        if (Carbon::now()->between($firstDate, $secondDate)) {
                            $presentTopics[] = $topic;
                        } else if ($firstDate->lt(Carbon::now())) {
                            $topicsElapsed[] = $topic;
                        } else if ($secondDate->gt(Carbon::now())) {
                            $futureTopics[] = $topic;
                        }
                    }

                }
            }

            // Opened Topic and Closed topics

            //$cbVotes = CB::getCbVotes($cbKey);
            if(empty($request->get("page"))) {
                foreach ($cbVotes as $cbVote) {
                    $voteResults[] = Vote::getVoteResults($cbVote->vote_key);
                }
            }
            $openedTopics = [];
            $closedTopics = [];
            $inExecutionTopics = [];
            $executedTopics = [];
            $totalCbComments = 0;
            $totalCbLikes = 0;
            $totalCbAccesses = 0;
            foreach ($topics as $topic) {
                //Cb total comments counter
                $totalCbComments += isset($topic->statistics->posts_counter) ? $topic->statistics->posts_counter : 0;
                //Cb total likes counter
                $totalCbLikes += isset($topic->statistics->like_counter) ? $topic->statistics->like_counter : 0;
                //Cb total accesses counter
                $totalCbAccesses += isset($topic->accesses) ? $topic->accesses : 0;

                if (empty($topic->status)) {
                    $openedTopics[] = $topic;
                } else {
                    $closedTopics[] = $topic;
                }

                // used in topics closed view
                if (!empty($topic->status[0]->status_type) && ($topic->status[0]->status_type->code == "accepted" ||
                        $topic->status[0]->status_type->code == "in_execution")
                ) {
                    $inExecutionTopics[] = $topic;
                } else if (!empty($topic->status[0]->status_type) && $topic->status[0]->status_type->code == "concluded") {
                    $executedTopics[] = $topic;
                }

            }

            // Check if CB expired date
            $cbExpiredDate = false;

            if (!empty($cb->end_date) && Carbon::createFromFormat('Y-m-d H:i:s', $cb->end_date . ' 23:59:59')->lt(Carbon::now()))
                $cbExpiredDate = true;

            // Check for create topics permission
            $createTopics = ($isModerator == 1
                || (ONE::checkCBsOption($configurations, 'CREATE-TOPIC') && !empty(Session::get('X-AUTH-TOKEN', null)))
                /*|| ONE::checkCBsOption($configurations, 'CREATE-TOPICS-ANONYMOUS') */) ?: false;

            // Get Total votes on CB
            $absoluteTotalVotes = [];
            if (isset($voteType) && !empty($voteType)) {
                $eventsKeyList = collect($voteType)->pluck('key')->toArray();
                $absoluteTotalVotes = Vote::getCbTotalVotes($eventsKeyList);
            }
            // Prepare data to send to the view
            $data = [];

            $data['filterOptionSelected'] = isset($filterOptionSelected) ? $filterOptionSelected : null;
            $data['voteResults'] = isset($voteResults) ? $voteResults : null;
            $data['categoryColors'] = isset($categoryColors) ? $categoryColors : null;
            $data['configurations'] = $configurations;
            $data['voteType'] = $voteType;
            $data['usersNames'] = $usersNames ?? [];
            $data['allReadyVoted'] = $allReadyVoted;
            $data['remainingVotes'] = $remainingVotes;
            $data['parameters'] = $parameters ?? [];
            $data['categoriesNameById'] = $categoriesNameById ?? [];
            $data['existVotes'] = $existVotes;
            $data['type'] = $type;
            $data['cbExpiredDate'] = $cbExpiredDate;
            $data['statusTypes'] = $statusTypes;
            $data['absoluteTotalVotes'] = $absoluteTotalVotes;
            $data['totalCbComments'] = $totalCbComments;
            $data['totalCbLikes'] = $totalCbLikes;
            $data['totalCbAccesses'] = $totalCbAccesses;
            $data['arrayIcons'] = ['accepted' => 'search', 'in_execution' => 'trophy', 'closed' => 'exclamation', 'not_accepted' => 'times'];
            $data['topicsTotals'] = $topics;
            $data['topicsPagination'] = $topics;
            $data['topics'] = $topics;
            $data['cb'] = $cb;
            $data['cbKey'] = $cbKey;
            $data['isModerator'] = $isModerator;
            $data['voteKey'] = $voteKey;
            $data['countTopics'] = sizeof($topics);
            $data['createTopics'] = $createTopics;
            $data['openedTopics'] = $openedTopics;
            $data['closedTopics'] = $closedTopics;
            $data['inExecutionTopics'] = $inExecutionTopics;
            $data['executedTopics'] = $executedTopics;
            $data['presentTopics'] = $presentTopics;
            $data['topicsElapsed'] = $topicsElapsed;
            $data['futureTopics'] = $futureTopics;
            $data['filesByType'] = $filesByType ?? [];
            $data['pageToken'] = $cbAndTopics->pageToken;
            $data["originalPageToken"] = $request->page ?? null;
            $data['searchTerm'] = $request->search;
            $data['statistics'] = $cbAndTopics->statistics;
            $data['voteType'] = $voteType;

            if (!empty($request->page) || $request->ajax()){
                return view('public.' . ONE::getEntityLayout() . '.cbs.' . $type . '.topicsVotedPads', $data);
            }else{
                if(!empty($cb->template))
                    return view('public.' . ONE::getEntityLayout() . '.cb.' . $cb->template . '.votesConfirm', $data);
                else
                    return view('public.' . ONE::getEntityLayout() . '.cb.default.votesConfirm', $data);
            }
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["topics.voted" => $e->getMessage()]);
        }
    }


    public function showTopicsCbsVoted(Request $request, $cbKey)
    {
        try {
            $cbKeys = $request->cb_keys;
            $type = $request->type;
            $userEventVotes = [];
            $data = [];
            $cb = [];
            $topics = [];
            $filesByType = [];
            $eventKeysByCb = [];
            $numberTopicToShow = $request->get("topics_to_show",100);
            foreach ($cbKeys as $key) {
                $cbVotes = CB::getCbVotes($key);
                $eventKeys = [];
                foreach ($cbVotes as $cbVote) {
                    $eventKeys[$cbVote->vote_key] = $cbVote->vote_key;
                }
                $userEventVotes = Vote::getUserVotesForEvents($eventKeys);

                $userTopicsVoted = [];
                foreach ($userEventVotes as $userEventVote) {
                    $userTopicsVoted[$userEventVote->vote_key] = $userEventVote->vote_key;
                }

                $cbAndTopics = CB::getCBAndTopicKeys($key, $userTopicsVoted, $numberTopicToShow, $request->get("page",null), $request->all());
                $cb[$key] = $cbAndTopics->cb;
                $topics[$key] = $cbAndTopics->topics;

                $topicFiles = CB::getTopicsFilesByType($cbAndTopics->topics);
                if(!empty($topicFiles)) {
                    foreach ($topicFiles as $itemKey => $file) {
                        $filesByType[$key][$itemKey] = $file;
                    }
                }

                foreach ($cbVotes as $cbVote) {
                    $eventKeysByCb[$key][$cbVote->vote_key] = $cbVote->vote_key;
                }
            }
            $data["cbKey"] = $cbKey;
            $data["cbs"] = $cb;
            $data["topics"] = $topics;
            $data['filesByType'] = $filesByType ?? [];
            $data["userEventVotes"] = $userEventVotes;
            $data["eventKeysByCb"] = $eventKeysByCb;
            $data["type"] = $type;
            if(!empty(collect($cb)->first()->template))
                return view('public.' . ONE::getEntityLayout() . '.cb.' . collect($cb)->first()->template . '.cbTopicsVotedWithChilds', $data);
            else
                return view('public.' . ONE::getEntityLayout() . '.cb.default.cbTopicsVotedWithChilds', $data);
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["topics.voted" => $e->getMessage()]);
        }
    }


    public function showCbDetail(Request $request, $cbKey)
    {
        try {


            if (!isset($this->cbType[$request->type])) {
                throw new Exception(trans('error.noCBtype'));
            }

            //  GET CB AND DETAILS
            $cbAndTopics = CB::getCBAndTopics($cbKey, $request->all());
            $cb = $cbAndTopics->cb;


            //  TYPE
            $type = $this->cbType[$request->type];


            //  CHECK IF MODERATOR

            $moderators = $cbAndTopics->moderators;

            $isModerator = 0;
            if (Session::has('user')) {

                if (One::isAdmin()) {
                    $isModerator = 1;
                } else {
                    //Get Managers
                    foreach ($moderators as $moderator) {

                        if ($moderator->user_key == Session::get('user')->user_key) {
                            $isModerator = 1;
                            break;
                        }
                    }
                }
            }

            // Prepare data to send to the view
            $data = [];
            $data['cbAndTopics'] = $cbAndTopics;
            $data['cb'] = $cb;
            $data['cbKey'] = $cb->cb_key;
            $data['isModerator'] = $isModerator;
            $data['type'] = $type;


            return view('public.' . ONE::getEntityLayout() . '.cbs.' . $type . '.cbDetail', $data);


        } catch (Exception $e) {
            return redirect()->back()->withErrors(["cb.show" => $e->getMessage()]);
        }


    }


    private function generalMapFunction($cbKey, $cbAndTopics, $type)
    {
        try {
            //$topicsStatus = $request->topicsStatus;
//            $type = $request->type;
            $type = $type;
//            $listType = 'map';
            $cb = $cbAndTopics;
            $configurations = $cb->configurations;
            $i = 0;
            $locations = [];
            $topics = [];
            $topicsData = $cb->topics;
            //total topics to be sent to summary boxes
            $countTopics = count($cb->topics);

            // Check Access
            if (CB::checkCBsOption($configurations, 'TOPIC-NEED-MODERATION')) {
                foreach ($topicsData as $topicData) {
                    if (count($topicData->status) > 0 && isset($topicData->status[0]) && $topicData->status[0]->status_type->code != 'not_accepted') {
                        $topics [] = $topicData;
                    }
                }
            } else {
                $topics = $topicsData;
            }

            //counters for totals in summary boxes
            $totalCbComments = 0;
            $totalCbLikes = 0;
            $inExecutionTopics = [];
            foreach ($topics as $topic) {

                //Cb total comments counter
                $totalCbComments += isset($topic->statistics->posts_counter) ? $topic->statistics->posts_counter : 0;
                //Cb total likes counter
                $totalCbLikes += isset($topic->statistics->like_counter) ? $topic->statistics->like_counter : 0;

                //Cb count total topics in execution
                if (!empty($topic->status[0]->status_type) && ($topic->status[0]->status_type->code == "accepted" ||
                        $topic->status[0]->status_type->code == "in_execution")
                ) {
                    $inExecutionTopics[] = $topic;
                }

                $obj = collect($topic->parameters)->keyBy('code')->all();

                if (!empty($obj["google_maps"])) {


                    $link = action('PublicTopicController@show', [$cbKey, $topic->topic_key, 'type' => $this->cbType[$type]]);

                    $paramsList = '';
                    if (!empty($topic->parameters)) {

                        // Prepares a LIST with topic parameters to be shown in map dialog
                        foreach ($topic->parameters as $key => $param) {

                            if ($param->code != 'google_maps') {

                                if ($param->type->code == "dropdown" || $param->type->code == "category" || $param->type->code == "budget") {
                                    foreach ($param->options as $option) {
                                        if ($param->pivot->value == $option->id) {
                                            $paramsList .= '<li> <i class="fa fa-folder-open" aria-hidden="true"></i>&nbsp;<span>' . $option->label . '</span></li>';
                                        }
                                    }
                                } else {
                                    $paramsList .= '<li><i class="fa fa-folder-open" aria-hidden="true"></i>&nbsp;<span>' . $param->pivot->value . '</span></li>';
                                }
                            }
                        }
                    }

                    $googleMapStatusLabel = (!empty($topic->status)) ? $topic->status[0]->status_type->description : '' ;

                    //shows topic number (if != 0) along with the title in google maps popup
                    if (!empty($topic->topic_number)) {

                        $locations[$i][0] = '<div class=\"map-dialog-container\"><div class=\"map-dialog-title-container\"><div class=\"map-dialog-title\">#' . $topic->topic_number . '. ' . $googleMapStatusLabel . ' - ' . str_replace("'", "&apos;" ,$topic->title) . '</div><ul class=\"map-dialog-ul\">' . $paramsList . '</ul></div><a class=\"map-dialog-a btn btn-outlined-default-layout btn-success \" href=\"' . $link . '\" target=\"_blank\">' . trans('PublicCbs.viewMore') . '</a></div>';
                    } else {
                        $locations[$i][0] = '<div class=\"map-dialog-container\"><div class=\"map-dialog-title-container\"><div class=\"map-dialog-title\">' . $googleMapStatusLabel . ' - ' . str_replace("'", "&apos;" ,$topic->title) . '</div><ul class=\"map-dialog-ul\">' . $paramsList . '</ul></div><a class=\"map-dialog-a btn btn-outlined-default-layout btn-success\" href=\"' . $link . '\" target=\"_blank\">' . trans('PublicCbs.viewMore') . '</a></div>';
                    }

                    //$link = action('PublicTopicController@show', [$cbKey, $topic->topic_key, 'type' => $this->cbType[$request->type]]);
                    $coords = explode(",", $obj["google_maps"]->pivot->value);
                    if (count($coords) > 0) {
                        $locations[$i][1] = $coords[0];
                        $locations[$i][2] = $coords[1];
                        if (!empty($obj["category"])) {
                            $category = "";
                            $pin = "";
                            foreach ($obj["category"]->options as $option) {
                                if ($obj["category"]->pivot->value == $option->id) {
                                    $category = $option->label;
                                    $pin = $option->pin ?? '';
                                }

                            }
                            $locations[$i][3] = strtolower(str_replace(" ", "_", $category));
                            if ($pin != ''){
                                $pin = json_decode($pin);
                                $locations[$i][4] = action('FilesController@download',["id"=>$pin[0]->id, "code" => $pin[0]->code, 1]);
                            }
                            else
                                $locations[$i][4] = '';
                        }
                        $i++;
                    }
                }

            }

            //send number of topics without geo-mapping
            $totalMapTopics = count($locations);
            $totalTopics = count($cb->topics);
            $totalNoMapTopics = $totalTopics - $totalMapTopics;


            $data = [];
            $data['locations'] = $locations;
            $data['totalNoMapTopics'] = $totalNoMapTopics;

            return $data;

        } catch (Exception $e)
        {
            return redirect()->back()->withErrors(["cbs.generalMap" => $e->getMessage()]);
        }
    }

    //THIS SHOULD BE RELOCATED TO THE COM MODEL
    public function exportProposalsToProjectsHARDCODED() {
        $response = CB::getExportProposalsToProjects();

        return $response;
    }

    public function unSubmitUserVotes(Request $request, $cbKey)
    {
        try{
            $type = $request['type'];
            $voteEventKey = $request['voteKey'];

            if(!empty($type) && !empty($voteEventKey)){
                Vote::unSubmitUserVotesInEvent($voteEventKey);
                return redirect()->action('PublicCbsController@show',['cbKey' => $cbKey, 'type' => $type ]);
            }else{
                throw new Exception(trans("cbs.no_cb_type_or_vote_key"));
            }

        } catch (Exception $e){
            dd($e);
        }
    }

    public function checkVoteCodeForm(Request $request, $cbKey) {
        try {
            $type = $request->type;

            $data = array(
                "cb"    => CB::getCb($cbKey),
                "cbKey" => $cbKey,
                "type"  => $type
            );

            return view('public.' . ONE::getEntityLayout() . '.cbs.' . $type . '.checkVoteCodeForm',$data);
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["cbs.voteCode" => $e->getMessage()]);
        }
    }

    public function checkVoteCode(Request $request, $cbKey) {
        try {
            /* The format of the code is Defined at PublicCbsController@genericSubmitVotes */
            $voteCodeData = preg_split('/[.\/]+/', str_replace(" ", "", $request->voteCode));
            if (count($voteCodeData)==4) {
                $userEventVotes = Vote::getDataForVoteCode($voteCodeData);
                $cbAndTopics = CB::getCBAndTopicKeys($cbKey, $userEventVotes, 1000);

                $type = $request->type;

                $cbParameters = CB::getCbParametersOptions($cbKey);
                foreach ($cbParameters->parameters as $parameter) {
                    $name = $parameter->parameter;
                    $code = $parameter->code;

                    $parameterOptions = [];
                    $options = $parameter->options;
                    foreach ($options as $option) {

                        if ($parameter->id == $request->parameter_id and $option->id == $request->option_id) {
                            $filterOptionSelected = array('parameter_id' => $parameter->id, 'parameter_name' => $parameter->parameter, 'option_id' => $option->id, 'label' => $option->label);
                        }

                        $parameterOptions[] = array('id' => $option->id, 'name' => $option->label);
                        $categoriesNameById[$option->id] = $option->label;

                    }
                    $parameters[$code] = array('id' => $parameter->id, 'name' => $name, 'filter' => $parameter->use_filter, 'code' => $parameter->code, 'options' => $parameterOptions);


                }

                $topics = [];
                $cb = $cbAndTopics->cb;
                $topicsData = $cbAndTopics->topics;


                $moderators = $cbAndTopics->moderators;
                $configurations = $cbAndTopics->configurations;
                $statusTypes = $cbAndTopics->statusTypes;
                $voteKeys = $cbAndTopics->voteKeys;

                if (!CB::checkCBsOption($configurations, 'PUBLIC-ACCESS') && !ONE::isAuth())
                    return redirect()->action('AuthController@login');

                // Check Access
                if (CB::checkCBsOption($configurations, 'TOPIC-NEED-MODERATION')) {
                    foreach ($topicsData as $topicData) {
                        if (count($topicData->status) > 0 && isset($topicData->status[0]) && $topicData->status[0]->status_type->code != 'not_accepted')
                            $topics [] = $topicData;
                    }
                } else
                    $topics = $topicsData;

                foreach ($topics as $topic) {
                    $topic->closed = (!empty($topic->active_status) && $topic->active_status->status_type->code != 'moderated');
                    foreach ($topic->parameters as $parameter) {
                        $code = $parameter->code;
                        $parameterOptions = [];
                        $options = $parameter->options;

                        foreach ($options as $option) {
                            $parameterOptions[] = array('id' => $option->id, 'name' => $option->label);
                            $categoriesNameById[$option->id] = $option->label;
                            if ($code == 'category') {
                                if ($parameter->pivot->value == $option->id) {
                                    $categories[] = $option->label;
                                    $topic->topic_category = $option->label;
                                }
                            }
                        }
                    }
                    /**
                     *  START
                     *  Make pagination for cbs infinite scroll
                     */
                    $topicsPagination[] = $topic;

                    /**
                     * END
                     */

                    $usersKeys[] = $topic->created_by;

                    // Add share links to topics (twitter,linkedin,facebook)
                    $shareLinks = [];
                    $shareLinks["twitter"] = Share::load(action('PublicTopicController@show', [$cbKey, $topic->topic_key, 'type' => $type]), $topic->title)->twitter();
                    $shareLinks["linkedin"] = Share::load(action('PublicTopicController@show', [$cbKey, $topic->topic_key, 'type' => $type]), $topic->title)->linkedin();
                    $shareLinks["facebook"] = Share::load(action('PublicTopicController@show', [$cbKey, $topic->topic_key, 'type' => $type]), $topic->title)->facebook();
                    $topic->shareLinks = $shareLinks;

                    if (isset($topic->last_post->created_by)) {
                        $usersKeys[] = $topic->last_post->created_by;
                    }


                }

                $topicFiles = CB::getTopicsFilesByType($topics);
                if(!empty($topicFiles)) {
                    foreach ($topicFiles as $key => $file) {
                        $filesByType[$key] = $file;
                    }
                }
                if (isset($usersKeys) && count($usersKeys) > 0)
                    $usersNames = Auth::getPublicListNames($usersKeys);

                $isModerator = 0;
                if (Session::has('user')) {
                    if (One::isAdmin()) {
                        $isModerator = 1;
                    } else {
                        //Get Managers
                        foreach ($moderators as $moderator) {

                            if ($moderator->user_key == Session::get('user')->user_key) {
                                $isModerator = 1;
                                break;
                            }
                        }
                    }
                }

                // check if exist votes
                $existVotes = 0;
                $voteKey = '';
                $allReadyVoted = [];
                $remainingVotes = 0;
                $voteType = [];

                $cbVotes = CB::getCbVotes($cbKey);
                if (ONE::isAuth()) {
                    $eventsResponse = Vote::getAllShowEventsNoTranslation($voteKeys);

                    //index of array = key of event
                    $eventVotes = [];
                    foreach ($eventsResponse as $ev) {
                        $eventVotes[$ev->key] = $ev;
                    }

                    $existVotes = 0;

                    foreach ($cbVotes as $vote) {
                        $vConfigurations = [];

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
                        $voteStatus = Vote::getVoteStatus($voteKey);
                        if ($voteStatus->vote) {
                            $existVotes = 1;
                        } else {
                            $existVotes = 0;
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

                        $voteType[] = [
                            "name" => isset($cbAndTopics->votes->$voteKey) ? $cbAndTopics->votes->$voteKey->name : null,
                            "method" => $methodName,
                            "key" => $voteKey,
                            "remainingVotes" => $remainingVotes,
                            "existVotes" => $existVotes,
                            "allReadyVoted" => $allReadyVoted,
                            "eventVote" => $eventVote,
                            "totalVotes" => isset($voteStatus->total_votes) ? json_decode(json_encode($voteStatus->total_votes), true) : null,
                            "alreadySubmitted" => $voteStatus->alreadySubmitted ?? false,
                            "configurations" => $vConfigurations,
                            "genericConfigurations" => $genericConfigurations,
                            "canVote" => $voteStatus->can_vote ?? true
                        ];
                    }
                }

                $presentTopics = [];
                $topicsElapsed = [];
                $futureTopics = [];
                if ($type == "publicConsultation" || $type == "tematicConsultation" || $type == "survey") {

                    foreach ($topics as $topic) {

                        if (empty($topic->start_date) && empty($topic->end_date)) {
                            $presentTopics[] = $topic;
                        } else {

                            $firstDate = Carbon::createFromFormat('Y-m-d H:i:s', $topic->start_date . ' 00:00:00');
                            $secondDate = Carbon::createFromFormat('Y-m-d H:i:s', $topic->end_date . ' 23:59:59');

                            if (Carbon::now()->between($firstDate, $secondDate)) {
                                $presentTopics[] = $topic;
                            } else if ($firstDate->lt(Carbon::now())) {
                                $topicsElapsed[] = $topic;
                            } else if ($secondDate->gt(Carbon::now())) {
                                $futureTopics[] = $topic;
                            }
                        }

                    }
                }

                // Opened Topic and Closed topics

                //$cbVotes = CB::getCbVotes($cbKey);
                if(empty($request->get("page"))) {
                    foreach ($cbVotes as $cbVote) {
                        $voteResults[] = Vote::getVoteResults($cbVote->vote_key);
                    }
                }
                $openedTopics = [];
                $closedTopics = [];
                $inExecutionTopics = [];
                $executedTopics = [];
                $totalCbComments = 0;
                $totalCbLikes = 0;
                $totalCbAccesses = 0;
                foreach ($topics as $topic) {
                    //Cb total comments counter
                    $totalCbComments += isset($topic->statistics->posts_counter) ? $topic->statistics->posts_counter : 0;
                    //Cb total likes counter
                    $totalCbLikes += isset($topic->statistics->like_counter) ? $topic->statistics->like_counter : 0;
                    //Cb total accesses counter
                    $totalCbAccesses += isset($topic->accesses) ? $topic->accesses : 0;

                    if (empty($topic->status)) {
                        $openedTopics[] = $topic;
                    } else {
                        $closedTopics[] = $topic;
                    }

                    // used in topics closed view
                    if (!empty($topic->status[0]->status_type) && ($topic->status[0]->status_type->code == "accepted" ||
                            $topic->status[0]->status_type->code == "in_execution")
                    ) {
                        $inExecutionTopics[] = $topic;
                    } else if (!empty($topic->status[0]->status_type) && $topic->status[0]->status_type->code == "concluded") {
                        $executedTopics[] = $topic;
                    }

                }

                // Check if CB expired date
                $cbExpiredDate = false;

                if (!empty($cb->end_date) && Carbon::createFromFormat('Y-m-d H:i:s', $cb->end_date . ' 23:59:59')->lt(Carbon::now()))
                    $cbExpiredDate = true;

                // Check for create topics permission
                $createTopics = ($isModerator == 1
                    || (ONE::checkCBsOption($configurations, 'CREATE-TOPIC') && !empty(Session::get('X-AUTH-TOKEN', null)))
                    /*|| ONE::checkCBsOption($configurations, 'CREATE-TOPICS-ANONYMOUS') */) ?: false;

                // Get Total votes on CB
                $absoluteTotalVotes = [];
                if (isset($voteType) && !empty($voteType)) {
                    $eventsKeyList = collect($voteType)->pluck('key')->toArray();
                    $absoluteTotalVotes = Vote::getCbTotalVotes($eventsKeyList);
                }

                // Prepare data to send to the view
                $data = [];

                $data['filterOptionSelected'] = isset($filterOptionSelected) ? $filterOptionSelected : null;
                $data['voteResults'] = isset($voteResults) ? $voteResults : null;
                $data['categoryColors'] = isset($categoryColors) ? $categoryColors : null;
                $data['configurations'] = $configurations;
                $data['voteType'] = $voteType;
                $data['usersNames'] = $usersNames ?? [];
                $data['allReadyVoted'] = $allReadyVoted;
                $data['remainingVotes'] = $remainingVotes;
                $data['parameters'] = $parameters;
                $data['categoriesNameById'] = $categoriesNameById;
                $data['existVotes'] = $existVotes;
                $data['type'] = $type;
                $data['cbExpiredDate'] = $cbExpiredDate;
                $data['statusTypes'] = $statusTypes;
                $data['absoluteTotalVotes'] = $absoluteTotalVotes;
                $data['totalCbComments'] = $totalCbComments;
                $data['totalCbLikes'] = $totalCbLikes;
                $data['totalCbAccesses'] = $totalCbAccesses;
                $data['arrayIcons'] = ['accepted' => 'search', 'in_execution' => 'trophy', 'closed' => 'exclamation', 'not_accepted' => 'times'];
                $data['topicsTotals'] = $topics;
                $data['topicsPagination'] = $topics;
                $data['topics'] = $topics;
                $data['cb'] = $cb;
                $data['cbKey'] = $cbKey;
                $data['isModerator'] = $isModerator;
                $data['voteKey'] = $voteKey;
                $data['countTopics'] = sizeof($topics);
                $data['createTopics'] = $createTopics;
                $data['openedTopics'] = $openedTopics;
                $data['closedTopics'] = $closedTopics;
                $data['inExecutionTopics'] = $inExecutionTopics;
                $data['executedTopics'] = $executedTopics;
                $data['presentTopics'] = $presentTopics;
                $data['topicsElapsed'] = $topicsElapsed;
                $data['futureTopics'] = $futureTopics;
                $data['filesByType'] = $filesByType ?? [];
                $data['pageToken'] = $cbAndTopics->pageToken;
                $data["originalPageToken"] = $request->page ?? null;
                $data['searchTerm'] = $request->search;
                $data['statistics'] = $cbAndTopics->statistics;
                $data['noLoop'] = true;
                $data['voteCode'] = $request->voteCode;

                return view('public.' . ONE::getEntityLayout() . '.cbs.' . $type . '.checkVoteCode', $data);
            } else
                return response()->json(["error"=>"failed","e"=>"non valid"],400);
        } catch (Exception $e) {
            return response()->json(["error"=>"failed","e"=>$e->getMessage()],500);
        }
    }

    public function publicUserVotingRegistration(Request $request, $type, $cbKey, $voteKey)
    {
        try {
            if(isset($request['municipality'])){
                Cookie::queue('choosed_municipality', $request['municipality'], (30*60*24));
            }

            if(isset($request['setForVoting'])){
                $voteEvent = Vote::getVoteStatus($voteKey);
                $totalVotesAllowed = 0;
                if($voteEvent){
                    $totalVotesAllowed = $voteEvent->remaining_votes->total + $voteEvent->remaining_votes->user_votes;
                }
                Cookie::queue('user_offline_voting', $totalVotesAllowed, (30*60*24));
                if (empty(Cookie::get("choosed_municipality")))
                    Cookie::queue('choosed_municipality', "nomunicipality", (30*60*24));
            }
            if (Cookie::get('choosed_municipality') == null){
                $sidebar = 'vote';
                $active = 'votes';

                $title = trans("private.registerMunicipalityForOfflineVoting");
                $parameters = Orchestrator::getEntityRegisterParameters();
                $parameter = collect($parameters)->where('code','=','municipality')->first();
                return view('private.cbs.offlineVoting', compact('type', 'title','parameter','cbKey','voteKey','sidebar','active'));
            }elseif(Cookie::get('user_offline_voting') == null){
                $sidebar = 'vote';
                $active = 'votes';
                $title = trans("private.registerMunicipalityForOfflineVoting");
                return view('private.cbs.offlineVoting',compact('type', 'title','parameter','cbKey','voteKey','sidebar','active'));
            }else{
                
                $cbAndTopics = CB::getCBAndTopics($cbKey);

                $topics = $cbAndTopics->topics;
                $cb = $cbAndTopics->cb;
                $topicFiles = CB::getTopicsFilesByType($topics);
                if(!empty($topicFiles)) {
                    foreach ($topicFiles as $key => $file) {
                        $filesByType[$key] = $file;
                    }
                }

                $totalVotesAllowed = Cookie::get('user_offline_voting');

                $parameters = Orchestrator::getEntityRegisterParameters();
                AuthController::logoutForVoteRegistration();
                $parameterValue = Cookie::get('choosed_municipality');

                if(Cookie::get('user_offline_voting') !== null) {

                    // $title = trans("privateUsers.publicUserVotingRegistrationTitleFor") . ' ' . $type . ' ' . $cb->title;

                    return view('public.' . ONE::getEntityLayout() . '.cb.publicUserVotingRegistration', compact('cbKey', 'voteKey', 'parameters','parameterValue','totalVotesAllowed','topics'));
                }else{
                    return redirect()->action('PublicController@index');
                }
            }


        }catch(Exception $e) {
            dd($e);
            return redirect()->back();
        }

    }


    public function publicUserVotingRegistrationStoreVotes(Request $request)
    {
        try{
            $vote = '';
            $userKey = '';
            if(isset($request['user_key'])){
                $userKey = $request['user_key'];
            }
            if(isset($request['votes'])){
                foreach ($request['votes'] as $vote){
                    $vote .= $vote.' ';
                }
            }

            $string = 'USER: '.$userKey.' VOTED FOR:'.$vote;
            Vote::storePublicUserVoting($request);
            return response()->json(["OK" => trans('registerInPersonVoting.votesStored')]);
        }catch(Exception $e) {
            return response()->json(["error" => trans('registerInPersonVoting.errorInReplaceUserVotesWithInPersonVotes')]);
        }
    }


    public function ignoreQuestionnaire(Request $request){

        $ignore = CB::setCbQuestionnaireUser($request->topicAuthor, $request->cbQuestionnaireKey);

        if($ignore){
            return "true";
        }

        return "false";
    }

    public function deleteFilesInSession()
    {
        if (Session::has('filesToUpload')) {
            try {
                Session::forget('filesToUpload');
            } catch (Exception $e) {
                throw new Exception($e->getMessage());
            }
        }
    }

    public function isParticipationModerator($moderators)
    {
        $isModerator = false;
        if (Session::has('user')) {
            if (One::isAdmin()) {
                $isModerator = true;
            } else {
                //GET MANAGERS
                foreach ($moderators as $moderator) {
                    if ($moderator->user_key == Session::get('user')->user_key) {
                        $isModerator = true;
                        break;
                    }
                }
            }
        }
        return $isModerator;
    }

    public function prepareCbParameters($cbParameters)
    {
        $phases = collect($cbParameters)->where('code','=','topic_checkpoint_phase');
        $parameters = [];

        if(!$phases->isEmpty()) {
            foreach ($phases as $phase) {
                $color = '#cccccc';
                if(isset($phase->parameter_fields)){
                    if(isset($phase->parameter_fields[0])){
                        $color = $phase->parameter_fields['0']->value;
                    }
                }

                $label = collect($phase->parameter_translations)->first()->parameter ?? [];
                $parameterOptions[] = array('id' => $phase->id, 'name' => !empty($label) ? $label : 'no translation', 'color' => $color );

            }
            $parameters['phases'] = array('id' => 'phases', 'name' => 'status', 'description' => 'status_description', 'filter' => 1, 'code' => 'topic_checkpoint_phase', 'options' => $parameterOptions);
        }

        foreach ($cbParameters as $parameter) {
            if($parameter->code != 'topic_checkpoint_phase') {

                $name = collect($parameter->parameter_translations)->first()->parameter ?? 'no name';
                $description = collect($parameter->parameter_translations)->first()->description ?? 'no description';

                $parameterOptions = [];
                $options = $parameter->options;
                if (!empty($parameter->options)) {
                    foreach ($options as $option) {
                        $parameterOptions[] = array('id' => $option->id, 'name' => !empty($label = collect($option->parameter_option_translations)->first()) ? $label->label : 'no translation');
                    }
                }
                $parameters[$parameter->id] = array('id' => $parameter->id, 'name' => $name, 'description' => $description, 'filter' => $parameter->use_filter,'parameter_code' => $parameter->parameter_code, 'code' => $parameter->code, 'options' => $parameterOptions);
            }
        }

        return $parameters;
    }

    public function prepareTopicParameters($topics,$cbParameters)
    {
        foreach ($topics as $topic) {
//            if(!isset($topic->parameters))
            foreach ($topic->_cached_data->parameters as $parameter) {

                if (!empty($cbParameters->{$parameter->id})) {
                    $parameter->parameter = $cbParameters->{$parameter->id}->name;
                    $parameter->description = $cbParameters->{$parameter->id}->description;

                    if(!empty($parameter->options)){
                        foreach ($parameter->options as $option){
                            $options = $cbParameters->{$parameter->id}->options;
                            $option->label = collect($options)->where('id',$option->id)->first()->name;
                        }
                    }
                }

            }
        }
    }

    public function prepareTopicsFiles($topicsPagination)
    {
        $fileTypes = [];
        $fileTypes["images"] = array("gif","jpg","png","bmp");
        $filesByType = [];
        foreach ($topicsPagination as $topic){
            if(!empty($topic->posts)){
                foreach ($topic->posts as $post){
                    if(!empty($post->files)){
                        foreach ($post->files as $file) {
                            $array = explode('.', $file->name);
                            $extension = strtolower(end($array));
                            foreach ($fileTypes as $key => $value) {
                                if (in_array($extension, $value)) {
                                    if(empty($filesByType[$topic->topic_key])) {
                                        $filesByType[$topic->topic_key] = json_decode(json_encode($file, JSON_FORCE_OBJECT));
                                    }
                                    break;
                                }
                            }
                        }
                    }
                }
            }
        }
        return $filesByType;
    }

    public function prepareCbVotes($voteKeys,$cbKey)
    {
        $cbVotes = CB::getCbVotes($cbKey);
        $existVotes = 0;
        $voteKey = '';
        $allReadyVoted = [];
        $remainingVotes = 0;
        $voteResults = [];
        if (ONE::isAuth()) {

            $eventsResponse = Vote::getAllShowEventsNoTranslation($voteKeys);

            //index of array = key of event
            $eventVotes = [];
            foreach ($eventsResponse as $ev) {
                $eventVotes[$ev->key] = $ev;
            }

            foreach ($cbVotes as $vote) {

                $vConfigurations = [];

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

                $voteStatus = Vote::getVoteStatus($voteKey);

                if ($voteStatus->vote) {
                    $existVotes = 1;
                } else {
                    $existVotes = 0;
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

                if(count($genericConfigurations) > 0) {
                    if (isset($genericConfigurations['allow_unsubmit_votes']) && $genericConfigurations['allow_unsubmit_votes'] == true) {
                        $canUnSubmit = true;
                    } else {
                        $canUnSubmit = false;
                    }
                }

                $voteType[] = [
                    "name"                  => isset($cbVotes->$voteKey) ? $cbVotes->$voteKey->name : null,
                    "method"                => $methodName,
                    "key"                   => $voteKey,
                    "remainingVotes"        => $remainingVotes,
                    "existVotes"            => $existVotes,
                    "allReadyVoted"         => $allReadyVoted,
                    "eventVote"             => $eventVote,
                    "totalVotes"            => isset($voteStatus->total_votes) ? json_decode(json_encode($voteStatus->total_votes), true) : null,
                    "alreadySubmitted"      => $voteStatus->alreadySubmitted ?? false,
                    "configurations"        => $vConfigurations,
                    "genericConfigurations" => $genericConfigurations,
                    "canVote"               => $voteStatus->can_vote ?? true,
                    "canUnSubmit"           => $canUnSubmit ?? false,
                    "submitedDate"          => isset($voteStatus->submited_date) ? $voteStatus->submited_date->date : null
                ];
            }

        } else {

            $eventsResponse = Vote::getAllShowEventsNoTranslation($voteKeys);

            foreach ($eventsResponse as $event) {
                $methodName = '';
                switch ($event->method->code) {
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
                $currentVoteEventResults = Vote::getVoteResults($event->key);
                $voteResults[] = $currentVoteEventResults;

                foreach ($cbVotes as $cbVote) {
                    if ($cbVote->vote_key == $event->key) {
                        $genericConfigurations = $cbVote->vote_configurations;
                    }
                }
                foreach ($event->configurations as $configuration) {
                    $eventConfigurations[$configuration->code] = $configuration->value;
                }
                foreach ($genericConfigurations as $configuration) {
                    $newGenericConfigurations[$configuration->code] = $configuration->value;
                }
                $voteType[] = [
                    "method"                => $methodName,
                    "key"                   => $event->key,
                    "remainingVotes"        => $remainingVotes,
                    "existVotes"            => 1,
                    "eventVote"             => $event,
                    "totalVotes"            => isset($currentVoteEventResults->total_votes) ? json_decode(json_encode($currentVoteEventResults->total_votes), true) : null,
                    "configurations"        => isset($eventConfigurations) ? $eventConfigurations : [],
                    "genericConfigurations" => isset($newGenericConfigurations) ? $newGenericConfigurations : [],
                    "disabled"              => true
                ];

            }

        }

        return $data = array([
            'voteType' => $voteType ?? [],
            'allReadyVoted' => $allReadyVoted,
            'remainingVotes' => $remainingVotes,
            'existVotes' => $existVotes,
            'voteKey' => $voteKey,
            'voteResults' => $voteResults
        ]);
    }

    public function dealWithSecurityPermissions($cbKey)
    {
        try {
            $userKey = Session::get('user')->user_key;
            $code = 'create_topic';
            $differenceUserLevelsLogin = Orchestrator::UserLoginLevels($userKey, $cbKey, $code);

            if ($differenceUserLevelsLogin != []) {
                $data['securityConfigurations'] = $differenceUserLevelsLogin;

            }

            $arrayVote = Vote::getEventLevelCbKey($cbKey);

            $differenceUserLevelsLoginVotes = Orchestrator::UserLoginLevelsVotes($userKey, $cbKey, $arrayVote);

            $data['securityConfigurationsVotes'] = $differenceUserLevelsLoginVotes;

            $i = 0;
            foreach ($data['securityConfigurationsVotes'] as $security) {
                if (isset($security->parameterUserTypes) and !empty($security->parameterUserTypes))
                    $i++;
            }
            $data['missing_login_levels'] = ($i > 0) ? true : false;

            return $data;
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["cb.show" => $e->getMessage()]);
        }
    }

    public function readFromCache($cbKey, $type)
    {
        try {
            $data = collect(json_decode(Session::get("topics_list_". $cbKey)))->toArray();
            $data["parameters"] = json_decode(json_encode($data["parameters"]), true);
            $data["cbKey"] = json_decode(json_encode($data["cbKey"]), true);

            if(isset($data["currentPhase"]))
                $data["currentPhase"] = json_decode(json_encode($data["currentPhase"]), true);
            if(isset($data["currentPhase"]))
                $data["moderators"] = json_decode(json_encode($data["moderators"]), true);
            if(isset($data["type"]))
                $data["type"] = json_decode(json_encode($data["type"]), true);
            if(isset($data["pageToken"]))
                $data["pageToken"] = json_decode(json_encode($data["pageToken"]), true);
            if(isset($data["currentPhaseColor"]))
                $data["currentPhaseColor"] = json_decode(json_encode($data["currentPhaseColor"]), true);
            if(isset($data["currentPhaseName"]))
                $data["currentPhaseName"] = json_decode(json_encode($data["currentPhaseName"]), true);
            if(isset($data["currentPhaseId"]))
                $data["currentPhaseId"] = json_decode(json_encode($data["currentPhaseId"]), true);
            if(isset($data["filterList"]))
                $data['filterList'] = json_decode(json_encode($data["filterList"]), true);

            if (Session::has("SITE-CONFIGURATION.current_phase") && $type != strtolower('QA')) {
                if (Session::get('user') != null) {
                    if(!isset($data["securityConfigurations"]) || !isset($data["missing_login_levels"])) {
                        $securityInformation = $this->dealWithSecurityPermissions($cbKey);
                        $data["securityConfigurations"] = $securityInformation["securityConfigurations"];
                        $data["missing_login_levels"] = $securityInformation["missing_login_levels"];
                        Session::put("topics_list_". $cbKey, json_encode($data), 30);
                    }else{
                        $data["securityConfigurations"] = json_decode(json_encode($data["securityConfigurations"]), true);
                        $data["missing_login_levels"] = json_decode(json_encode($data["missing_login_levels"]), true);
                    }
                }
            }

            if(isset($data["voteType"])){
                foreach ($data['voteType'] as &$voteType) {
                    $voteType = json_decode(json_encode($voteType), true);
                    $voteType['eventVote'] = json_decode(json_encode($voteType['eventVote'], true));

                }
            }


            if (isset($data['currentPhase'])) {
                if ($data['currentPhase'] == 201) {
                    return view('public.' . ONE::getEntityLayout() . '.cbs.phase2.list', $data);
                } elseif ($data['currentPhase'] >= 203) {
                    return view('public.' . ONE::getEntityLayout() . '.cbs.phase3.list', $data);
                } else {
                    return view('public.' . ONE::getEntityLayout() . '.cbs.phase1.listTopics', $data);
                }
            }
            return view('public.' . ONE::getEntityLayout() . '.cbs.' . $type . '.list', $data);
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["cb.show" => $e->getMessage()]);
        }
    }


    public function firstCall(Request $request, $cbKey)
    {
        try{
            Session::remove("cb-" . $cbKey . "-topicsOrder");
            $request['only_basic_information'] = true;

//            if(Session::has("topics_list_". $cbKey)){
//                return $this->readFromCache($cbKey, $type);
//            }
            $type='proposal';
            $basicInformation = CB::getPublicPadParticipation($cbKey, $request->get("page",null), null, $request->all());

            $translations = CB::getCbTranslations($basicInformation->cb)->data ?? null;
            $statusAvailable = CB::getStatusTypes();
            $statusTypes = [];
            foreach ($statusAvailable as $status){
                $statusTypes[$status->code] = $status->name;
            }

            $cbFilters = CB::getCbFilters($cbKey);
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
            $data['cbFilter'] = $cbFilter;
//            end status filer
            
            $data['configurations'] = $basicInformation->cb->configurations;
            $data['moderators'] = $basicInformation->cb->moderators;
            $data['cb'] = $basicInformation->cb;
            $data['type'] = $type;
            $data['cbKey'] = $cbKey;
            $data['parameters'] = $this->prepareCbParameters($basicInformation->cb->parameters);
            $data['pageToken'] = null;
            $data['operationSchedules'] = $basicInformation->operationSchedules;
            $data['statusTypes'] = $statusTypes;

            $data['createTopic'] = false;
            $data['needsRegisterFields'] = false;
            $data['needsEmailConfirmation'] = false;
            $data['needsEmailConfirmationAndRegisterFields'] = false;
            $data['translations'] = $translations;

//            if(Session::has('user') && collect(Session::get('user')->user_parameters)->count() == 5 && Session::get('user')->confirmed == 1){
//                $data['createTopic'] = true;
//            }else{
//                if(!Session::has('user'))
//                    $data['needsLogin'] = true;
//                elseif(collect(Session::get('user')->user_parameters)->count() < 5 && Session::get('user')->confirmed == 1)
//                    $data['needsRegisterFields'] = true;
//                elseif(collect(Session::get('user')->user_parameters)->count() == 5 && Session::get('user')->confirmed == 0)
//                    $data['needsEmailConfirmation'] = true;
//                else
//                    $data['needsEmailConfirmationAndRegisterFields'] = true;
//            }

            $loginLevels = [];

            if(Session::has("SITE-CONFIGURATION.current_phase") && $type != 'qa') {
                if(Session::get('user')!=null/* && ONE::userRole() == 'user'*/){
                    $userKey=Session::get('user')->user_key;
                    $code='create_topic';
                    $differenceUserLevelsLogin= Orchestrator::UserLoginLevels($userKey, $cbKey, $code);

                    if($differenceUserLevelsLogin!=[]){
                        $data['securityConfigurations'] = $differenceUserLevelsLogin;

                    }

                    $arrayVote=Vote::getEventLevelCbKey($cbKey);

                    $differenceUserLevelsLoginVotes= Orchestrator::UserLoginLevelsVotes($userKey, $cbKey, $arrayVote);

                    $data['securityConfigurationsVotes']=$differenceUserLevelsLoginVotes;

                    $i=0;
                    foreach($data['securityConfigurationsVotes'] as $security){
                        if(isset($security->parameterUserTypes) and !empty($security->parameterUserTypes))
                            $i++;
                    }
                    $data['missing_login_levels'] = ($i>0) ? true : false;
                }

                $voteKeys = [];
                foreach($basicInformation->cb->votes as $votes){
                    $voteKeys[] = $votes->vote_key;
                }

                $votePrepared = $this->prepareCbVotes($voteKeys, $basicInformation->cb->cb_key);
                $voteType = $votePrepared[0]['voteType'];
                $data['voteType'] = $voteType;
                foreach($data['voteType'] as $voteType){
                    $voteType= (json_decode(json_encode($voteType),true));
                }


                $data['currentPhase'] = Session::get("SITE-CONFIGURATION.current_phase");
                $currentPhase = $data['parameters']['phases'];

                if(Session::has("SITE-CONFIGURATION.current_phase")) {
                    $currentPhase = $data['parameters']['phases'];
                    if($data['currentPhase'] == 205)
                        $currentPhaseOption = collect($currentPhase['options'])->where('id','=',$data['currentPhase']-2);
                    else
                        $currentPhaseOption = collect($currentPhase['options'])->where('id','=',$data['currentPhase']);
                    if(isset($currentPhaseOption)){
                        $currentPhaseColor = collect($currentPhaseOption)->first()['color'];
                        $currentPhaseName= collect($currentPhaseOption)->first()['name'];
                        $currentPhaseId= collect($currentPhaseOption)->first()['id'];

                        $data['currentPhaseColor'] = $currentPhaseColor;
                        $data['currentPhaseName'] = $currentPhaseName;
                        $data['currentPhaseId'] = $currentPhaseId;

                    }
                }
            } else{
                if(Session::has('user')/* && ONE::userRole() == 'user'*/) {
                    $userKey = Session::get('user')->user_key;
                    $code = 'create_topic';

                    if(Session::get('SITE-CONFIGURATION.boolean_register_only_nif')==false){
                        $differenceUserLevelsLogin = Orchestrator::UserLoginLevels($userKey, $cbKey, $code);

                        if ($differenceUserLevelsLogin != []) {
                            $data['securityConfigurations'] = $differenceUserLevelsLogin;
                        }

                        if(Session::get('user')->confirmed != 1) {
                            if(!is_array($data['securityConfigurations']))
                                $data['securityConfigurations'] = [];
                            $data['securityConfigurations']["email"] = 'notConfirmed';
                            $data['needsEmailConfirmation'] = true;
                        } else {
                            if(!is_array($data['securityConfigurations']))
                                $data['securityConfigurations'] = [];
                            $data['securityConfigurations']["email"] = null;
                        }



                        $loginLevels = CB::getUserLoginLevels($cbKey);
                    }
                }
            }

            $voteKeys = [];
            foreach($basicInformation->cb->votes as $votes){
                $voteKeys[] = $votes->vote_key;
            }

            $votePrepared = $this->prepareCbVotes($voteKeys, $basicInformation->cb->cb_key);
            $voteType = $votePrepared[0]['voteType'];
            $data['voteType'] = $voteType;
            foreach($data['voteType'] as $voteType){
                $voteType= (json_decode(json_encode($voteType),true));
            }

            $data['loginLevels'] = $loginLevels;

            if($data['currentPhase']??null == 205)
                $filterList['filter_phases'] = Session::get("SITE-CONFIGURATION.current_phase")-2;
            else
                $filterList['filter_phases'] = Session::get("SITE-CONFIGURATION.current_phase");

            $data['filterList'] = $filterList;

            Session::put("topics_list_". $cbKey, json_encode($data), 30);

            if(!empty($basicInformation->cb->childs)){

                if(!empty($basicInformation->cb->template)){
                    return view('public.' . ONE::getEntityLayout() . '.cbs.' . $basicInformation->cb->template . '.cbDataWithChilds', $data);
                }else{
                    return view('public.' . ONE::getEntityLayout() . '.cbs.' . $type . '.cbDataWithChilds', $data);
                }

            } else {
                if(!empty($basicInformation->cb->template)){
                    return view('public.' . ONE::getEntityLayout() . '.cb.' . $basicInformation->cb->template . '.list', $data);
                }else{
                    return view('public.' . ONE::getEntityLayout() . '.cb.default.list', $data);
                }
            }


            return view('public.' . ONE::getEntityLayout() . '.cb.' . $type . '.topics', $data);
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["cb.show" => $e->getMessage()]);
        }
    }

    /**
     * @param Request $request
     * @param $cbKey
     * @return $this
     */
    public function showNew(Request $request, $cbKey)
    {
        //CB::switchToNewParameter($cbKey);
        try {
            //SECURITY FOR CB TYPE

            // dd($request->all());
//            $type = $this->cbType[$request['type']];
            $type='proposal';
            if (!isset($request->ajax_call) && $type != 'qa') {
                return $this->firstCall($request, $cbKey, $type);
            }
            // dd($request->all());

            $numberTopicToShow = $request->get("topics_to_show",50);

            //DELETE ARRAY OF FILES FROM SESSION IF EXISTS
            $this->deleteFilesInSession();

            //PREPARE ARGUMENTS TO SEND
            $filterList = collect(($request->all() ?? []))->toArray();



            if(Cache::has(session()->getId() . $cbKey . ($request['sort_order'] ?? ""))){

                $cacheData = collect(json_decode(Cache::get(session()->getId() . $cbKey . ($request['sort_order'] ?? ""))))->toArray();
                if(isset($cacheData['votesPerTopic'])){
                    $request['votesPerTopic'] = $cacheData['votesPerTopic'];
                }
            }

            $publicPadInformation = CB::getPublicPadParticipation($cbKey, $request->get("page",null), $numberTopicToShow, $request->all());
            if($publicPadInformation){
                $publicPadInformation = collect($publicPadInformation)->first();
            }

            $cb = $publicPadInformation->cb;

            $topicsPagination = $publicPadInformation->topics;
            $moderators = $publicPadInformation->moderators;
            $configurations = $publicPadInformation->configurations;
            $usersNames = $publicPadInformation->users;
            $voteKeys = $publicPadInformation->votes;
            $statistics = $publicPadInformation->statistics;
            $operationSchedules = $publicPadInformation->operationSchedules;

            if(empty($request->get("page"))) {
                $parameters = $this->prepareCbParameters($cb->parameters);
            }

            $translations = CB::getCbTranslations($publicPadInformation->cb) ?? [];

            if (!CB::checkCBsOption($configurations, 'PUBLIC-ACCESS') && !ONE::isAuth()){
                LogsRequest::setAccess('cbs_showNew',false, null,null,$cbKey,null,null,null,null, 'auth is not logged in', Session::has('user') ? Session::get('user')->user_key : null);
                return redirect()->action('AuthController@login');
            }

            if(!empty($request->get("page")) && !empty(Session::get("cb-" . $cbKey . "-topicsOrder")))
                $pageTopicKeys = Session::get("cb-" . $cbKey . "-topicsOrder");
            else
                $pageTopicKeys = [];

            foreach ($topicsPagination as $topic) {
                $shareLinks = [];
                $shareLinks["twitter"] = Share::load(action('PublicTopicController@show', [$cbKey, $topic->topic_key, 'type' => $type]), $topic->title)->twitter();
                $shareLinks["linkedin"] = Share::load(action('PublicTopicController@show', [$cbKey, $topic->topic_key, 'type' => $type]), $topic->title)->linkedin();
                $shareLinks["facebook"] = Share::load(action('PublicTopicController@show', [$cbKey, $topic->topic_key, 'type' => $type]), $topic->title)->facebook();
                $topic->shareLinks = $shareLinks;
                $topic->_cached_data = json_decode($topic->_cached_data);

                $pageTopicKeys[] = array(
                    "topic_key" => $topic->topic_key,
                    "title"      => $topic->title
                );
            }

            Session::put("cb-" . $cbKey . "-topicsOrder",$pageTopicKeys);
            unset($pageTopicKeys);

            //DEAL WITH FILES
            $filesByType = $this->prepareTopicsFiles($topicsPagination);

            //DEAL WITH USER CURRENT ROLE
            $isModerator = $this->isParticipationModerator($moderators);

            // if(empty($request->get("page"))) {
            //DEAL WITH VOTES
            $votePrepared = $this->prepareCbVotes($voteKeys, $cb->cb_key);
            $voteType = $votePrepared[0]['voteType'];
            $allReadyVoted = $votePrepared[0]['allReadyVoted'];
            $remainingVotes = $votePrepared[0]['remainingVotes'];
            $existVotes = $votePrepared[0]['existVotes'];
            $voteKey = $votePrepared[0]['voteKey'];
            $voteResults = $votePrepared[0]['voteResults'];


            /*
             * To get a list of the followers Names
             */
//                $users = [];
//                foreach($voteType as $typeVote){
//                    foreach($typeVote['totalVotes'] as $topic){
//                        if(isset($topic['users_votes'])){
//                            foreach($topic['users_votes'] as $user)
//                                $users[] = $user;
//                        }
//                    }
//                }
//
//                $usersVotedInTopics = Auth::getPublicListNames($users);
            // }
// dd($voteType);
            $presentTopics = [];
            $topicsElapsed = [];
            $futureTopics = [];
            if ($type == "publicConsultation" || $type == "tematicConsultation" || $type == "survey") {

                foreach ($topicsPagination as $topic) {

                    if (empty($topic->start_date) && empty($topic->end_date)) {
                        $presentTopics[] = $topic;
                    } else {

                        $firstDate = Carbon::createFromFormat('Y-m-d H:i:s', $topic->start_date . ' 00:00:00');
                        $secondDate = Carbon::createFromFormat('Y-m-d H:i:s', $topic->end_date . ' 23:59:59');

                        if (Carbon::now()->between($firstDate, $secondDate)) {
                            $presentTopics[] = $topic;
                        } else if ($firstDate->lt(Carbon::now())) {
                            $topicsElapsed[] = $topic;
                        } else if ($secondDate->gt(Carbon::now())) {
                            $futureTopics[] = $topic;
                        }
                    }

                }
            }

            $openedTopics = [];
            $closedTopics = [];
            $inExecutionTopics = [];
            $executedTopics = [];
            $totalCbComments = 0;
            $totalCbLikes = 0;
            $totalCbAccesses = 0;

            $absoluteTotalVotes = 0;
            $cb->_vote_statistics = json_decode(!empty($cb->_vote_statistics) ? $cb->_vote_statistics : "{}");

            if (!empty($cb->_vote_statistics) && !empty($cb->_vote_statistics->votes_by_event)) {
                foreach ($cb->_vote_statistics->votes_by_event as $eventKey => $eventVotes) {
                    $absoluteTotalVotes += $eventVotes;
                }
            }

            foreach ($topicsPagination as $topic) {
                //Cb total comments counter
                $totalCbComments += isset($topic->statistics->posts_counter) ? $topic->statistics->posts_counter : 0;
                //Cb total likes counter
                $totalCbLikes += isset($topic->statistics->like_counter) ? $topic->statistics->like_counter : 0;
                //Cb total accesses counter
                $totalCbAccesses += isset($topic->accesses) ? $topic->accesses : 0;

                if (empty($topic->status)) {
                    $openedTopics[] = $topic;
                } else {
                    $closedTopics[] = $topic;
                }

                // used in topics closed view
                if (!empty($topic->status[0]->status_type) && ($topic->status[0]->status_type->code == "accepted" ||
                        $topic->status[0]->status_type->code == "in_execution")
                ) {
                    $inExecutionTopics[] = $topic;
                } else if (!empty($topic->status[0]->status_type) && $topic->status[0]->status_type->code == "concluded") {
                    $executedTopics[] = $topic;
                }
            }

            if (((CB::checkCBsOption($configurations, 'TOPIC-AS-PRIV-QUESTIONNAIRE')) || (CB::checkCBsOption($configurations, 'TOPIC-AS-PUBLIC-QUESTIONNAIRE'))) && $type == 'tematicConsultation') {
                $numberTopicToShow = 8;
            }

            $topicsPagination = new Paginator($topicsPagination, $numberTopicToShow, $request->page);
            $topicsOpenedPagination = new Paginator($openedTopics, $numberTopicToShow, $request->page);
            $topicsClosedPagination = new Paginator($closedTopics, $numberTopicToShow, $request->page);

            // Check if CB expired date
            $cbExpiredDate = false;

            if (!empty($cb->end_date) && Carbon::createFromFormat('Y-m-d H:i:s', $cb->end_date . ' 23:59:59')->lt(Carbon::now()))
                $cbExpiredDate = true;

            // Check for create topics permission
            $createTopics = ($isModerator == 1
                || PublicTopicController::userCanCreateTopic($configurations,$moderators,$cb,$type) == 'CAN-CREATE'
                /*|| ONE::checkCBsOption($configurations, 'CREATE-TOPICS-ANONYMOUS') */) ?: false;

            // Prepare data to send to the view
            $data = [];

            if($type != 'qa'){

                // Get Total votes on CB
//                $absoluteTotalVotes = [];
                if (isset($voteType) && !empty($voteType)) {
                    $eventsKeyList = collect($voteType)->pluck('key')->toArray();
//                    $absoluteTotalVotes = Vote::getCbTotalVotes($eventsKeyList);
                }

                if(Session::has("SITE-CONFIGURATION.current_phase")) {
                    $data['currentPhase'] = Session::get("SITE-CONFIGURATION.current_phase");
                }

                if(isset($filterList['filter_phases']) && empty($request->get("page")) && $type != 'qa'){
                    $currentPhase = $parameters['phases'];

                    $currentPhaseOption = collect($currentPhase['options'])->where('id','=',$filterList['filter_phases']);

                    if(isset($currentPhaseOption)){
                        $currentPhaseColor = collect($currentPhaseOption)->first()['color'];
                        $currentPhaseName= collect($currentPhaseOption)->first()['name'];
                        $currentPhaseId= collect($currentPhaseOption)->first()['id'];
                        $data['currentPhaseColor'] = $currentPhaseColor;
                        $data['currentPhaseName'] = $currentPhaseName;
                        $data['currentPhaseId'] = $currentPhaseId;

                    }
                }
            }

//            if(Session::has("SITE-CONFIGURATION.current_phase")) {
//                $data['currentPhase'] = Session::get("SITE-CONFIGURATION.current_phase");
//            }
            /*Array with icons fa class correspondence*/

            if (!empty($request->page) && Cache::has(session()->getId() . $cbKey . ($request['sort_order'] ?? "")) && $type != 'qa' ) {
                $data = collect(json_decode(Cache::get(session()->getId() . $cbKey . ($request['sort_order'] ?? ""))))->toArray();
//                $this->prepareTopicParameters($topicsPagination,$data['parameters']);

                if(isset($voteType) && !empty($voteType)){
                    foreach($data['voteType'] as $voteType){
                        $voteType= (json_decode(json_encode($voteType),true));
                    }
                }

                $data["parameters"] = json_decode(json_encode($data["parameters"]),true);
                $data['usersNames'] = [];

                if(isset($voteType) && !empty($voteType)) {
                    $data['printVotes'] = false;
                    $data["voteType"] = json_decode(json_encode($data["voteType"]),true);
                }

                Cache::put(session()->getId() . $cbKey . ($request['sort_order'] ?? ""), json_encode($data), 30);
            } elseif($type != 'qa') {
                $data['showHeader'] = true;
                $data['showStatistics'] = true;
                $data['votesPerTopic'] = isset($votesPerTopic) ? $votesPerTopic : null;
                $data['cbParameters'] = isset($cbParameters) ? $cbParameters->parameters : null;
                $data['filterOptionSelected'] = isset($filterOptionSelected) ? $filterOptionSelected : null;
                $data['voteResults'] = isset($voteResults) ? $voteResults : null;
                $data['categoryColors'] = isset($categoryColors) ? $categoryColors : null;

                $data['voteType'] = $voteType ?? null;
//                $data['usersVotedInformation'] = $usersVotedInTopics;
                $data['filesByType'] = $filesByType;
                $data['usersNames'] = $usersNames;
                $data['allReadyVoted'] = $allReadyVoted ?? null;
                $data['remainingVotes'] = $remainingVotes ?? null;
                $data['cbsMenu'] = [];
                $data['voteKey'] = $voteKey ?? null;
                $data['categoriesNameById'] = [];
                $data['existVotes'] = $existVotes ?? null;
                $data['topicsLocation'] = [];
                $data['type'] = $type;
                $data['cbExpiredDate'] = $cbExpiredDate;
                $data['listType'] = [];
                $data['submittedProposal'] = [];
                $data['statusTypes'] = [];
                $data['absoluteTotalVotes'] = $absoluteTotalVotes;
                $data['totalCbComments'] = $totalCbComments;
                $data['totalCbLikes'] = $totalCbLikes;
                $data['totalCbAccesses'] = $totalCbAccesses;
                $data['filterList'] = $filterList;
                $data['parameters'] = $parameters;
                $data['cbParameters'] = json_decode(json_encode($parameters,JSON_FORCE_OBJECT));
                $data['statistics'] = $statistics;
                $data['printVotes'] = true;

            }

            if(isset($request->no_loop))
                $data['noLoop'] = true;

            Cache::put(session()->getId() . $cbKey . ($request['sort_order'] ?? ""), json_encode($data), 30);

            // This data is not to be cached
            $data['moderators'] = $moderators;
            $data['arrayIcons'] = ['accepted' => 'search', 'in_execution' => 'trophy', 'closed' => 'exclamation', 'not_accepted' => 'times'];
            $data['topicsTotals'] = $topicsPagination;
            $data['topicsPagination'] = $topicsPagination;
            $data['topics'] = $topicsPagination;
            $data['cb'] = $cb;
            $data['cbKey'] = $cbKey;
            $data['type'] = $type;
            $data['isModerator'] = $isModerator;
            $data['countTopics'] = sizeof($topicsPagination);
            $data['createTopics'] = $createTopics;
            $data['openedTopics'] = $openedTopics;
            $data['topicsOpenedPagination'] = $topicsOpenedPagination;
            $data['closedTopics'] = $closedTopics;
            $data['topicsClosedPagination'] = $topicsClosedPagination;
            $data['inExecutionTopics'] = $inExecutionTopics;
            $data['executedTopics'] = $executedTopics;
            $data['presentTopics'] = $presentTopics;
            $data['topicsElapsed'] = $topicsElapsed;
            $data['futureTopics'] = $futureTopics;
            $data['usersNames'] = $usersNames;
            $data['filesByType'] = $filesByType;
            $data['pageToken'] = $publicPadInformation->pageToken;
            $data['searchTerm'] = $request['search'];
            $data['statistics'] = $publicPadInformation->statistics;
            $data['filterList'] = $filterList;
            $data['securityConfigurations'] = [];
            $data['securityConfigurationsVotes']= [];
            $data['statistics'] = $statistics;
            $data['configurations'] = $configurations;
            $data["originalPageToken"] = $request->page ?? null;
            $data['filteredTopicsCount'] = $publicPadInformation->filteredTopicsCount??0;;
            $data['operationSchedules'] = $operationSchedules;
            $data['translations'] = $translations;

            $loginLevels = [];
            if(Session::has('user') && collect(Session::get('user')->user_parameters)->count() == 5 && Session::get('user')->confirmed == 1){
                $data['createTopic'] = true;
            }else{
                $data['createTopic'] = false;
            }
            if((ONE::getEntityLayout() == strtolower('default')) && Session::get('user')!=null){
                if(Session::get('SITE-CONFIGURATION.boolean_register_only_nif')==false){
                    $loginLevels = CB::getUserLoginLevels($cbKey);
                }
            }

            $data['loginLevels'] = $loginLevels;
            //LogsRequest::setAccess('cbs_showNew',true, null,null,$cbKey,null,null,null, null, null, Session::has('user') ? Session::get('user')->user_key : null);

            if (!empty($request->page) && Cache::has(session()->getId() . $cbKey . ($request['sort_order'] ?? ""))) {
                $data['showStatistics'] = false;
                $data['showHeader'] = false;
                $viewType = $request->get('listType');

                //  RETURNS THIS VIEW FOR THEMATIC CONSULTATIONS TO BE USED AS QUESTIONNAIRE
                if (((CB::checkCBsOption($data['configurations'], 'TOPIC-AS-PRIV-QUESTIONNAIRE')) || (CB::checkCBsOption($data['configurations'], 'TOPIC-AS-PUBLIC-QUESTIONNAIRE'))) && $data['type'] == 'tematicConsultation') {

                    return view('public.' . ONE::getEntityLayout() . '.cbs.' . $data['type'] . '.topicsPadsQuestionnaire', $data);
                }


                if (!is_null($viewType) && $viewType == 'listProposals') {
                    return view('public.' . ONE::getEntityLayout() . '.cbs.' . $data['type'] . '.topicsPadsInList', $data);
                }

                $cb = CB::getCbByKey($cbKey);
                if( !empty($cb->template) ) {
                    return view('public.' . ONE::getEntityLayout() . '.cb.' . $cb->template . '.listTopics', $data)->with(['isAuth' => One::isAuth()]);
                }else {
                    return view('public.' . ONE::getEntityLayout() . '.cb.default.listTopics', $data);
                }

            } else {
                $data['showHeader'] = true;
                //  RETURNS THIS VIEW FOR THEMATIC CONSULTATIONS TO BE USED AS QUESTIONNAIRE
                if (((CB::checkCBsOption($configurations, 'TOPIC-AS-PRIV-QUESTIONNAIRE')) || (CB::checkCBsOption($configurations, 'TOPIC-AS-PUBLIC-QUESTIONNAIRE'))) && $type == 'tematicConsultation') {
                    return view('public.' . ONE::getEntityLayout() . '.cbs.' . $type . '.cbDetail', $data);
                }

                if (!empty($request->status_view) && $request->status_view == 'closed') {
                    return view('public.' . ONE::getEntityLayout() . '.cbs.' . $type . '.closedTopics.list', $data);
                }

                if (isset($request->ajax_call)) {
                    if(!empty($cb->template)){
                        return view('public.' . ONE::getEntityLayout() . '.cb.' . $cb->template . '.listTopics', $data)->with(['isAuth' => One::isAuth()]);
                    }else{
                        return view('public.' . ONE::getEntityLayout() . '.cb.default.listTopics', $data)->with(['isAuth' => One::isAuth()]);
                    }

                } else {
                    if(!empty($cb->template)){
                        return view('public.' . ONE::getEntityLayout() . '.cb.' . $cb->template . '.list', $data)->with(['isAuth' => One::isAuth()]);
                    }else{
                        return view('public.' . ONE::getEntityLayout() . '.cb.default.list', $data)->with(['isAuth' => One::isAuth()]);
                    }
                }
            }
        } catch (Exception $e) {
            $jsonObj = json_encode(array('error' => "Failure: ".$e->getMessage() ));
            //LogsRequest::setAccess('cbs_showNew',false, null,null,$cbKey,null,null,null, $jsonObj, null, Session::has('user') ? Session::get('user')->user_key : null);
            return redirect()->back()->withErrors(["cbs.showNew" => $e->getMessage()]);
        }
    }

    public static function getTopicKeyByIndex($cbKey,$topicKey,$index) {
        try {
            $topicsOrder = Session::get("cb-" . $cbKey . "-topicsOrder");

            if (!empty($topicsOrder)) {
                if (!is_int($index)) {
                    if (strcasecmp($index, "next") == 0)
                        $index = 1;
                    else if (strcasecmp($index, "previous") == 0 || strcasecmp($index, "before") == 0)
                        $index = -1;
                }

                if (is_int($index)) {
                    $currentTopicIndex = collect($topicsOrder)->where("topic_key", $topicKey)->keys()->first() ?? -1;

                    if ($currentTopicIndex >= 0) {
                        $requestedTopicIndex = $currentTopicIndex + $index;

                        if (array_key_exists($requestedTopicIndex, $topicsOrder))
                            return $topicsOrder[$requestedTopicIndex];
                    }
                }
            }
        } catch(Exception $e) {}

        return [];
    }

    public function showCbVoteInPerson(Request $request, $cbKey){
        try{
            $numberTopicToShow = $request->get("topics_to_show",50);

            //PREPARE ARGUMENTS TO SEND
            $filterList = collect(($request->all() ?? []))->toArray();

            $publicPadInformation = CB::getPublicPadParticipation($cbKey, $request->get("page",null), $numberTopicToShow, $request->all());

            if($publicPadInformation){
                $publicPadInformation = collect($publicPadInformation)->first();
            }

            $cb = $publicPadInformation->cb;

            $topicsPagination = $publicPadInformation->topics;
            // $configurations = $publicPadInformation->configurations;
            // $usersNames = $publicPadInformation->users;
            $voteKeys = $publicPadInformation->votes;
            // $statistics = $publicPadInformation->statistics;
            // $operationSchedules = $publicPadInformation->operationSchedules;

            if(empty($request->get("page"))) {
                $parameters = $this->prepareCbParameters($cb->parameters);
            }

            if(!empty($request->get("page")) && !empty(Session::get("cb-" . $cbKey . "-topicsOrder")))
                $pageTopicKeys = Session::get("cb-" . $cbKey . "-topicsOrder");
            else
                $pageTopicKeys = [];

            foreach ($topicsPagination as $topic) {
                $topic->_cached_data = json_decode($topic->_cached_data);

                $pageTopicKeys[] = array(
                    "topic_key" => $topic->topic_key,
                    "title"      => $topic->title
                );
            }

            Session::put("cb-" . $cbKey . "-topicsOrder",$pageTopicKeys);
            unset($pageTopicKeys);

            //DEAL WITH FILES
            $filesByType = $this->prepareTopicsFiles($topicsPagination);

            //DEAL WITH VOTES
            $votePrepared = $this->prepareCbVotes($voteKeys, $cb->cb_key);
            $voteType = $votePrepared[0]['voteType'];
            $allReadyVoted = $votePrepared[0]['allReadyVoted'];
            $remainingVotes = $votePrepared[0]['remainingVotes'];
            $existVotes = $votePrepared[0]['existVotes'];
            $voteKey = $votePrepared[0]['voteKey'];
            $voteResults = $votePrepared[0]['voteResults'];

            $topicsPagination = new Paginator($topicsPagination, $numberTopicToShow, $request->page);
            // Check if CB expired date
            $cbExpiredDate = false;


            if (!empty($cb->end_date) && Carbon::createFromFormat('Y-m-d H:i:s', $cb->end_date . ' 23:59:59')->lt(Carbon::now()))
                $cbExpiredDate = true;

            // Prepare data to send to the view
            $data = [];
            if (isset($voteType) && !empty($voteType)) {
                $eventsKeyList = collect($voteType)->pluck('key')->toArray();
                //                    $absoluteTotalVotes = Vote::getCbTotalVotes($eventsKeyList);
            }

            if (!empty($request->page) && Cache::has(session()->getId() . $cbKey . ($request['sort_order'] ?? ""))) {
                $data = collect(json_decode(Cache::get(session()->getId() . $cbKey . ($request['sort_order'] ?? ""))))->toArray();
                //                $this->prepareTopicParameters($topicsPagination,$data['parameters']);

                if(isset($voteType) && !empty($voteType)){
                    foreach($data['voteType'] as $voteType){
                        $voteType= (json_decode(json_encode($voteType),true));
                    }
                }

                $data["parameters"] = json_decode(json_encode($data["parameters"]),true);

                if(isset($voteType) && !empty($voteType)) {
                    $data["voteType"] = json_decode(json_encode($data["voteType"]),true);
                }

                Cache::put(session()->getId() . $cbKey . ($request['sort_order'] ?? ""), json_encode($data), 30);
            }else {

                $data['votesPerTopic'] = isset($votesPerTopic) ? $votesPerTopic : null;
                $data['cbParameters'] = isset($cbParameters) ? $cbParameters->parameters : null;
                $data['voteResults'] = isset($voteResults) ? $voteResults : null;

                $data['voteType'] = $voteType ?? null;
                //                $data['usersVotedInformation'] = $usersVotedInTopics;
                $data['filesByType'] = $filesByType;
                $data['allReadyVoted'] = $allReadyVoted ?? null;
                $data['remainingVotes'] = $remainingVotes ?? null;
                $data['voteKey'] = $voteKey ?? null;
                $data['existVotes'] = $existVotes ?? null;
                $data['cbExpiredDate'] = $cbExpiredDate;
                // $data['submittedProposal'] = [];
                // $data['statusTypes'] = [];
                $data['filterList'] = $filterList;
                $data['parameters'] = $parameters;
                $data['cbParameters'] = json_decode(json_encode($parameters,JSON_FORCE_OBJECT));

            }

            if(isset($request->no_loop))
                $data['noLoop'] = true;

            Cache::put(session()->getId() . $cbKey . ($request['sort_order'] ?? ""), json_encode($data), 30);

            // This data is not to be cached
            $data['topics'] = $topicsPagination;
            $data['arrayIcons'] = ['accepted' => 'search', 'in_execution' => 'trophy', 'closed' => 'exclamation', 'not_accepted' => 'times'];
            $data['cb'] = $cb;
            $data['cbKey'] = $cbKey;
            $data['countTopics'] = sizeof($topicsPagination);
            $data['filesByType'] = $filesByType;
            $data['pageToken'] = $publicPadInformation->pageToken;
            $data['filterList'] = $filterList;
            $data['securityConfigurations'] = [];
            $data['securityConfigurationsVotes']= [];
            // $data['configurations'] = $configurations;  ----> talvez
            $data["originalPageToken"] = $request->page ?? null;
            $data['filteredTopicsCount'] = $publicPadInformation->filteredTopicsCount ?? 0;
            // $data['operationSchedules'] = $operationSchedules;  ----> talvez

            $loginLevels = [];
            $loginLevels = CB::getUserLoginLevels($cbKey);

            $data['loginLevels'] = $loginLevels;
//LogsRequest::setAccess('cbs_showNew',true, null,null,$cbKey,null,null,null, null, null, Session::has('user') ? Session::get('user')->user_key : null);

            if (!empty($request->page) && Cache::has(session()->getId() . $cbKey . ($request['sort_order'] ?? ""))) {
                // $viewType = $request->get('listType');
                return view('public.' . ONE::getEntityLayout() . '.cb.publicUserVotingTopics', $data);

            } else {
                if (isset($request->ajax_call)) {
                    return view('public.' . ONE::getEntityLayout() . '.cb.publicUserVotingTopics', $data)->with(['isAuth' => One::isAuth()]);

                } else {
                    return view('public.' . ONE::getEntityLayout() . '.cb.publicUserVotingTopics', $data)->with(['isAuth' => One::isAuth()]);
                }
            }

        }catch(Exception $e){
            dd($e->getMessage());
        }
    }

    //----------------------------------IMPROVEMENT-----------------------------//

    /**
     * RETURN VIEW THAT LOADS EVERY VUE COMPONENT
     * @param Request $request
     * @param $cbKey
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function display(Request $request, $cbKey)
    {

        $type = isset($request['type']) ? $request['type'] == 'event' ? 'event' : 'default' : 'default';
        $currentUser = Session::get('user');
        $currentLanguage = Session::get('LANG_CODE', 'pt');
        $defaultImage =  ONE::getSiteConfiguration("file_logo_first","/images/demo/LogoEmpatia-l-02.png");
        $cb = CB::getPad($cbKey);
        $data['cb'] =  $cb;
        $data['currentUser'] =  $currentUser;
        $data['currentLanguage'] =  $currentLanguage;
        $data['defaultImage'] =  $defaultImage;
        $data['type'] =  $type;
        $data['cbKey'] =  $cbKey;

        LogsRequest::setAccess('cb_show', true, null, null, $cbKey, null, null, null, null, 'type: '.$type, Session::has('user') ? Session::get('user')->user_key : null);

        return view('public.' . ONE::getEntityLayout() . '.cb.'.$type.'.topics',$data);
    }


    /**
     * FIRST REQUEST, DEALS ONLY WITH THE PAD
     * BASIC INFORMATION AND CACHED DATA
     * @param $cbKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function basicInformation($cbKey)
    {
        try{
            Session::remove("cb-" . $cbKey . "-topicsOrder");

            $data = CB::getPad($cbKey);
            LogsRequest::setAccess('cb_show', true, null, null, $cbKey, null, null, null, null, 'type: '.$data->template ?? 'default', Session::has('user') ? Session::get('user')->user_key : null);

            return response()->json($data, 200);
        } catch (Exception $e) {
            $jsonObj = json_encode(array('error' => "Failure: ".$e->getMessage() ));
            LogsRequest::setAccess('cb_show', false, null, null, $cbKey, null, null, null, $jsonObj, null, Session::has('user') ? Session::get('user')->user_key : null);

            return response()->json(['errors' => $e->getMessage()], 500);
        } catch (\Throwable $t) {
            $jsonObj = json_encode(array('error' => "Failure: ".$t->getMessage() ));
            LogsRequest::setAccess('cb_show', false, null, null, $cbKey, null, null, null, $jsonObj, null, Session::has('user') ? Session::get('user')->user_key : null);

            return response()->json(['errors' => $t->getMessage()], 500);
        }
    }



    /**
     * FIRST REQUEST, DEALS ONLY WITH THE PAD
     * BASIC INFORMATION AND CACHED DATA
     * @param $cbKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUserAvailableActions($cbKey)
    {

        try{
            $data = [];
            if(Session::has('user')){
                $data = CB::getUserLoginLevels($cbKey);
            }
            LogsRequest::setAccess('cb_login_levels', true, null, null, $cbKey, null, null, null, null, 'type: '.json_encode($data), Session::has('user') ? Session::get('user')->user_key : null);

            return response()->json($data, 200);
        } catch (Exception $e) {
            $jsonObj = json_encode(array('error' => "Failure: ".$e->getMessage() ));
            LogsRequest::setAccess('cb_login_levels', false, null, null, $cbKey, null, null, null, $jsonObj, null, Session::has('user') ? Session::get('user')->user_key : null);

            return response()->json(['errors' => $e->getMessage()], 500);
        } catch (\Throwable $t) {
            $jsonObj = json_encode(array('error' => "Failure: ".$t->getMessage() ));
            LogsRequest::setAccess('cb_login_levels', false, null, null, $cbKey, null, null, null, $jsonObj, null, Session::has('user') ? Session::get('user')->user_key : null);

            return response()->json(['errors' => $t->getMessage()], 500);
        }
    }



    /**
     * FETCH THE PAD TOPICS, ONLY DEALS WITH
     * THE CACHED DATA
     * @param $cbKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPadTopics($cbKey)
    {
        try{
            $data = CB::getPadTopics($cbKey);

            if(!empty(Session::get("cb-" . $cbKey . "-topicsOrder")))
                $pageTopicKeys = Session::get("cb-" . $cbKey . "-topicsOrder");
            else
                $pageTopicKeys = [];

            foreach ($data as $topic) {
                // Convert html entities and stip html tags for JS
                $topic->contents = strip_tags(html_entity_decode($topic->contents));
                
                $pageTopicKeys[] = array(
                    "topic_key" => $topic->topic_key,
                    "title"      => $topic->title
                );
            }

            Session::put("cb-" . $cbKey . "-topicsOrder",$pageTopicKeys);
            unset($pageTopicKeys);
          
            LogsRequest::setAccess('cb_get_topics', true, null, null, $cbKey, null, null, null, null, null, Session::has('user') ? Session::get('user')->user_key : null);

            return response()->json($data, 200);
        } catch (Exception $e) {
            $jsonObj = json_encode(array('error' => "Failure: ".$e->getMessage() ));
            LogsRequest::setAccess('cb_get_topics', false, null, null, $cbKey, null, null, null, $jsonObj, null, Session::has('user') ? Session::get('user')->user_key : null);

            return response()->json(['errors' => $e->getMessage()], 500);
        } catch (\Throwable $t) {
            $jsonObj = json_encode(array('error' => "Failure: ".$t->getMessage() ));
            LogsRequest::setAccess('cb_get_topics', false, null, null, $cbKey, null, null, null, $jsonObj, null, Session::has('user') ? Session::get('user')->user_key : null);

            return response()->json(['errors' => $t->getMessage()], 500);
        }
    }


    /**
     * FETCH THE PAD VOTES WITH CONFIGURATIONS IF A
     * USER IS LOGGED IN DEALS WITH THAT ALSO
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPadVotes(Request $request)
    {
        try{
            $voteKeys = collect($request->input('voteKeys'))->pluck('vote_key')->toArray();
            $userKey = Session::has('user') ? Session::get('user')->user_key : null;
            $data = Vote::getPadVotes($voteKeys,$userKey);

            LogsRequest::setAccess('cb_pad_votes', true, null, null, null, null, null, null, null, null, $userKey);

            return response()->json($data, 200);
        } catch (Exception $e) {
            $jsonObj = json_encode(array('error' => "Failure: ".$e->getMessage() ));
            LogsRequest::setAccess('cb_pad_votes', false, null, null, null, null, null, null, $jsonObj, null, Session::has('user') ? Session::get('user')->user_key : null);

            return response()->json(['errors' => $e->getMessage()], 500);
        } catch (\Throwable $t) {
            $jsonObj = json_encode(array('error' => "Failure: ".$t->getMessage() ));
            LogsRequest::setAccess('cb_pad_votes', false, null, null, null, null, null, null, $jsonObj, null, Session::has('user') ? Session::get('user')->user_key : null);

            return response()->json(['errors' => $t->getMessage()], 500);
        }
    }


    /**
     * METHOD FOR SETTING A TOPIC VOTE
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function voteInTopic(Request $request)
    {
        try{
            $data = PublicTopicController::vote($request);
            return response()->json($data, 200);
        } catch (Exception $e) {
            return response()->json(['errors' => $e->getMessage()], 500);
        } catch (\Throwable $t) {
            return response()->json(['errors' => $t->getMessage()], 500);
        }
    }
}
