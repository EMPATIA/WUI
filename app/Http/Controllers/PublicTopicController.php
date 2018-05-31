<?php

namespace App\Http\Controllers;

use App\ComModules\Auth;
use App\ComModules\EMPATIA;
use App\ComModules\Notify;
use App\ComModules\Questionnaire;
use App\ComModules\LogsRequest;
use FontLib\TrueType\Collection;
use Illuminate\Pagination\Paginator;
use Illuminate\Validation\ValidationException;
use PDF;
use App\ComModules\Orchestrator;
use App\ComModules\Vote;
use App\ComModules\CB;
use App\ComModules\Files;
use App\Http\Requests\PostRequest;
use App\Http\Requests\TopicRequest;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Session;
use URL;
use View;
use Breadcrumbs;
use ONE;
use Agent;
use Chencha\Share\ShareFacade as Share;


class PublicTopicController extends Controller
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
            'survey' => 'survey',
            'project' => 'project',
            'phase1' => 'phase1',
            'phase2' => 'phase2',
            'phase3' => 'phase3',
            'qa' => 'qa',
            'project_2c' => 'project_2c',
            'event' => 'event'
        ];

        if (Route::current() == null) return;

        View::share('title', trans('topic.title'));
        $this->keyCb = Route::current()->parameter('cbKey');
        Session::put('cbId', $this->keyCb);
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request, $cbKey)
    {

        try {
            if (empty($request->type) || !isset($this->cbType[$request->type])) {
                throw new Exception(trans('error.noCBtype'));
            }
            $type = $this->cbType[$request->type];

            $topics = CB::topicsWithLastPost($request, $cbKey);

            // -----
            // array of users
            $usersKeys = [];
            $usersNames = [];

            foreach ($topics as $topic) {
                $usersKeys[] = $topic->created_by;

                if (isset($topic->last_post->created_by)) {
                    $usersKeys[] = $topic->last_post->created_by;
                }
            }

            if (count($usersKeys) > 0) {
                $usersNames = Auth::getListNames($usersKeys);
            }

            $isModerator = 0;
            if (Session::has('user')) {
                //Get Managers
                $moderators = CB::getCbModerators($cbKey);
                foreach ($moderators as $moderator) {
                    if ($moderator->user_key == Session::get('user')->user_key)
                        $isModerator = 1;
                }
            }

            return view('public.' . ONE::getEntityLayout() . '.cbs.' . $type . '.index', compact('topics', 'cbKey', 'usersNames', 'isModerator', 'type'));

        } catch (Exception $e) {
            return redirect()->back()->withErrors(["topic.edit" => $e->getMessage()]);
        }
    }

    /**
     * Create a new resource.
     * @param Request $request
     * @param $cbKey
     * @return $this|View
     */
    public function create(Request $request, $cbKey)
    {
        //
        try {
            if (!EMPATIA::verifyCbOperationSchedule($cbKey, 'topic', 'create')) {
                return redirect()->back()->withErrors(trans('cbs.OutsidePermittedCreationData'));
            }
        } catch (Exception $e) {
            // do nothing
        }

        try {
            if (Session::has('filesToUpload')) {
                Session::forget('filesToUpload');
            }
            if (Session::get('user') != null && ONE::userRole() == 'user') {

                $userKey = Session::get('user')->user_key;
                $code = 'create_topic';
                $diferenceUserLevelsLogin = Orchestrator::UserLoginLevels($userKey, $cbKey, $code);

                if ($diferenceUserLevelsLogin == true) {

                    // Request configurations (this can be improved. You can make only one request)
                    $topicData = CB::getCb($cbKey);

                    $type = !empty($topicData->template) ? $topicData->template : 'proposal';

                    $translations = CB::getCbTranslations($topicData ?? []);
                    $configurations = collect($topicData->configurations)->pluck('code')->toArray();
                    $userCanCreateTopic = $this->userCanCreateTopic($configurations, $topicData->moderators, $topicData, $type);

                    //IF THIS APPLIES THE userCanCreateTopic RETURNED A ACTION
                    if ($userCanCreateTopic != 'CAN-CREATE' && $userCanCreateTopic != 'CAN-VIEW-FORM') {
                         return redirect($userCanCreateTopic);
                    }

                    $allowFiles = [];
                    if (CB::checkCBsOption($configurations, 'ALLOW-FILES')) {
                        $allowFiles[] = "docs";
                    }

                    if (CB::checkCBsOption($configurations, 'ALLOW-PICTURES')) {
                        $allowFiles[] = "images";
                    }

                    // Get CB parameters
                    $CBparameters = CB::getCbParametersOptions($cbKey)->parameters;

                    $fileId = 0;
                    $parameters = [];
                    foreach ($CBparameters as $parameter) {

                        $name = $parameter->parameter;
                        $code = $parameter->type->code;

                        $parameterOptions = [];
                        $options = $parameter->options;
                        foreach ($options as $option) {
                            $parameterOptions[$option->id] = $option->label;
                        }
                        $parameters[$name] = array('id' => $parameter->id, 'parameter_code' => $parameter->parameter_code, 'name' => $name, 'code' => $code, 'options' => $parameterOptions, 'mandatory' => $parameter->mandatory, 'description' => $parameter->description, 'private' => $parameter->private, 'topicImage' => $parameter->topic_image, 'maxNumberOfFiles' => $parameter->max_number_files, 'max_number_files_flag' => $parameter->max_number_files_flag);

                        /* check if is image */
                        if ($parameter->code == 'image_map') {
                            $fileId = $parameter->value;
                        }
                    }

                    // Get questionnaire list and setting data for a questionnaire select
                    $questionnaires = [];

                    $qList = Questionnaire::getQuestionnaireList() ?? [];
                    foreach ($qList as $q) {
                        $questionnaires[$q->form_key] = $q->title;
                    }


                    $fileCode = '';
                    if ($fileId != 0) {
                        $file = Files::getFile($fileId);
                        $fileCode = $file->code;
                    }
                    $data['uploadKey'] = Files::getUploadKey();
                    $data['cbKey'] = $cbKey;
                    $data['parameters'] = $parameters;
                    $data['fileId'] = $fileId;
                    $data['fileCode'] = $fileCode;
                    $data['type'] = $type;
                    $data['questionnaires'] = $questionnaires;
                    $data['configurations'] = $configurations;
                    $data['allowFiles'] = $allowFiles;
                    $data['cb'] = $topicData;
                    $data['translations'] = $translations;
                    $data['userKey'] = (Session::has('user')) ? Session::get('user')->user_key : null;

                    return view('public.' . ONE::getEntityLayout() . '.cb.default.form', $data);
                } else {
                    return redirect()->back()->withErrors(trans('cbs.MissingConfigurationPermissions'));
                }
            } else {

//                if (empty($request->type) && !isset($this->cbType[$request->type])) {
//                    throw new Exception(trans('error.noCBtype'));
//                }
                // Request configurations (this can be improved. You can make only one request)
                $topicData = CB::getCb($cbKey);
                $type = !empty($topicData->template) ? $topicData->template : 'proposal';

                $translations = CB::getCbTranslations($topicData ?? []);

                $configurations = collect($topicData->configurations)->pluck('code')->toArray();

                $userCanCreateTopic = $this->userCanCreateTopic($configurations, $topicData->moderators, $topicData, $type);
                //IF THIS APPLIES THE userCanCreateTopic RETURNED A ACTION
                if ($userCanCreateTopic != 'CAN-CREATE' && $userCanCreateTopic != 'CAN-VIEW-FORM') {
                    return redirect($userCanCreateTopic);
                }

                $allowFiles = [];
                if (CB::checkCBsOption($configurations, 'ALLOW-FILES')) {
                    $allowFiles[] = "docs";
                }

                if (CB::checkCBsOption($configurations, 'ALLOW-PICTURES')) {
                    $allowFiles[] = "images";
                }

                // Get CB parameters
                $CBparameters = CB::getCbParametersOptions($cbKey)->parameters;
                $fileId = 0;
                $parameters = [];

                foreach ($CBparameters as $parameter) {

                    $name = $parameter->parameter;
                    $code = !empty($parameter->type) ? $parameter->type->code : "";

                    $parameterOptions = [];
                    $options = $parameter->options;
                    foreach ($options as $option) {
                        $parameterOptions[$option->id] = $option->label;
                    }
                    $parameters[$name] = array(
                        'id' => $parameter->id,
                        'name' => $name,
                        'code' => $code,
                        'options' => $parameterOptions,
                        'mandatory' => $parameter->mandatory,
                        'description' => $parameter->description,
                        'private' => $parameter->private,
                        'parameter_code' => $parameter->parameter_code,
                        'max_number_files_flag' => $parameter->max_number_files_flag,
                        'maxNumberOfFiles' => $parameter->max_number_files,
                        'topicImage' => $parameter->topic_image
                    );

                    /* check if is image */
                    if ($parameter->code == 'image_map') {
                        $fileId = $parameter->value;
                    }
                }

                // Get questionnaire list and setting data for a questionnaire select
                $questionnaires = [];

                $qList = Questionnaire::getQuestionnaireList() ?? [];
                foreach ($qList as $q) {
                    $questionnaires[$q->form_key] = $q->title;
                }

                $fileCode = '';
                if ($fileId != 0) {
                    $file = Files::getFile($fileId);
                    $fileCode = $file->code;
                }
                $data['uploadKey'] = Files::getUploadKey();
                $data['cbKey'] = $cbKey;
                $data['parameters'] = $parameters;
                $data['fileId'] = $fileId;
                $data['fileCode'] = $fileCode;
                $data['type'] = $type;
                $data['questionnaires'] = $questionnaires;
                $data['configurations'] = $configurations;
                $data['allowFiles'] = $allowFiles;
                $data['cb'] = $topicData;
                $data['translations'] = $translations;
                $data['userKey'] = (Session::has('user')) ? Session::get('user')->user_key : null;

                return view('public.' . ONE::getEntityLayout() . '.cb.default.form', $data);
            }

        } catch (Exception $e) {
            return redirect()->back()->withErrors(["topic.create" => $e->getMessage()]);
        }
    }

    /**
     * Edit a existent resource.
     *
     * @param Request $request
     * @param $cbKey
     * @param $topicKey
     * @return Response
     * @internal param $id
     */
    public function edit(Request $request, $cbKey, $topicKey)
    {
        try {
            if (!EMPATIA::verifyCbOperationSchedule($cbKey, 'topic', 'update')) {
                return redirect()->back()->withErrors(trans('cbs.OutsidePermittedUpdateData'));
            }
        } catch (Exception $e) {
        }
        try {
            /*
            if (empty($request->type) && !isset($this->cbType[$request->type])) {
                throw new Exception(trans('error.noCBtype'));
            }
            */
            $topicData = CB::getCb($cbKey);
            $type = !empty($topicData->template) ? $topicData->template : 'proposal';



            $topic = CB::getTopicParameters($topicKey, "", true);

            $post_key = $topic->first_post->post_key;

            $jsonFileList = [];
            if (!empty($post_key)) {
                $filesList = CB::listFilesForTopic($post_key);
                // Convert to json filelist
                foreach (!empty($filesList) ? $filesList : [] as $fileObj) {
                    $file = [
                        'id' => $fileObj->file_id,
                        'code' => $fileObj->file_code,
                        'name' => $fileObj->name,
                        'description' => $fileObj->description
                    ];
                    $file = (Object)$file;
                    $jsonFileList[$fileObj->type_id][] = $file;
                }
            }

            $topicParameters = [];
            foreach (!empty($topic->parameters) ? $topic->parameters : [] as $param) {
                $topicParameters[$param->id] = $param;
            }

            $post = $topic->first_post;
            $CBparameters = CB::getCbParametersOptions($cbKey)->parameters;

            // Request configurations
            $topicData = CB::getTopicDataWithChilds($topicKey);

            $configurations = $topicData->configurations;


            // Check Access
            if (!CB::checkCBsOption($configurations, 'PUBLIC-ACCESS') && !ONE::isAuth()) {
                return redirect()->action('AuthController@login');
            }

            $allowFiles = [];
            if (CB::checkCBsOption($configurations, 'ALLOW-FILES')) {
                $allowFiles[] = "docs";
            }

            if (CB::checkCBsOption($configurations, 'ALLOW-PICTURES')) {
                $allowFiles[] = "images";
            }

            $fileId = 0;
            $posX = "";
            $posY = "";
            $parameters = [];
            foreach ($CBparameters as $parameter) {

                $name = $parameter->parameter;
                $code = $parameter->type->code;

                if (isset($topicParameters[$parameter->id]))
                    $value = $topicParameters[$parameter->id]->pivot->value;
                else
                    $value = "";

                $parameterOptions = [];
                $options = $parameter->options;
                foreach ($options as $option) {
                    $parameterOptions[$option->id] = $option->label;
                }

                $parameters[$name] = array('id' => $parameter->id, 'value' => $value, 'name' => $name, 'code' => $code, 'options' => $parameterOptions, 'mandatory' => $parameter->mandatory, 'description' => $parameter->description, 'private' => $parameter->private);

                /* check if is image */
                if ($parameter->code == 'image_map') {
                    $fileId = $parameter->value;

                    if (count($value) > 0) {
                        $coordinates = explode("-", $value);

                        if (count($coordinates) == 2) {
                            if (strlen($coordinates[0]) > 0 && strlen($coordinates[1])) {
                                $posX = $coordinates[0];
                                $posY = $coordinates[1];
                            }
                        }
                    }
                }
            }

            $fileCode = '';
            if ($fileId != 0) {
                $file = Files::getFile($fileId);
                $fileCode = $file->code;
            }

            // Get questionnaire list and setting data for a questionnaire select
            $questionnaires = [];

            $qList = Questionnaire::getQuestionnaireList();
            foreach ($qList as $q) {
                $questionnaires[$q->form_key] = $q->title;
            }
            $uploadKey = Files::getUploadKey();
            $cb = CB::getCb($cbKey);
            $translations = CB::getCbTranslations($cb ?? []);
            //$cb = $topicData->cb;
            //
            return view('public.' . ONE::getEntityLayout() . '.cbs.' . $type . '.form', compact('uploadKey', 'topic', 'posX', 'posY', 'post', 'cbKey', 'parameters', 'fileId', 'fileCode', 'topicParameters', 'type', 'questionnaires', 'allowFiles', 'configurations', 'cb', 'jsonFileList', 'translations'));

        } catch (Exception $e) {
            return redirect()->back()->withErrors(["topic.edit" => $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     * @param Request $request
     * @param $cbKey
     * @param $topicKey
     * @internal param int $id
     * @return View
     */
    public function show(Request $request, $cbKey, $topicKey)
    {
        try {
            return $this->display($request, $cbKey, $topicKey);
            // CHECK THE CB TYPE
            $type = 'default';

            //GET ALL THE TOPIC RELEVANT INFORMATION

            $topicInformation = CB::getTopic($topicKey, true);

            //GET THE TOPIC CB
            //GET THE CB PARAMETERS
            $cb = $topicInformation->cb;
            $translations = CB::getCbTranslations($cb ?? []);
            $cbParameters = CB::getCbParametersOptions($cb->cb_key);
            //THIS WILL BE REPLACED WITH A AJAX CALL
            $topicData = CB::getTopicDataWithChilds($topicKey);

            $messages = $topicData->posts;
            $messagesNotModerated = $topicData->postsToModerate;
            $comments = (empty($topicData->positive_comments) && empty($topicData->neutral_comments) && empty($topicData->pnegative_comments)) ? false : true;
            //END OF TO BE REPLACED BY AJAX

            $comments = [];
            $id = 0;

            foreach ($messages as $key => $item) {
                if ($key > 0) {
                    $comments[$key] = ['flag' => 'moderated', 'details' => $item];
                    $id = $key;
                }
            }
            $id++;
            foreach ($messagesNotModerated as $item) {
                $comments[$id] = ['flag' => 'not_moderated', 'details' => $item];
                $id++;
            }

            $countComments = 0;
            $comments = collect($comments)->sortBy('details.created_at');
            $countComments = collect($comments)->count();

            //GET THE CB CONFIGURATIONS
            $configurations = $topicInformation->configurations;

            //GET THE TOPIC STATISTICS [NOTE -> NOW IT IS A ARRAY NOT A OBJECT]
            $statistics['posts_counter'] = $topicInformation->posts_counter;

            //CHECK THE ACCESS
            if (!CB::checkCBsOption($configurations, 'PUBLIC-ACCESS') && !ONE::isAuth()) {
                LogsRequest::setAccess('topic_show', false, $topicKey, null, $cbKey, null, null, null, null, 'type: ' . $type . '- Access denied because user wasn\'t logged in', Session::has('user') ? Session::get('user')->user_key : null);
                return redirect()->action('AuthController@login');
            }

            //DEFINE THE PERMISSIONS ARRAY
            $permissions = [];
            $permissions["ALLOW-VIDEO-LINK"] = CB::checkCBsOption($configurations, 'ALLOW-VIDEO-LINK');
            $permissions["ALLOW-SHARE"] = CB::checkCBsOption($configurations, 'ALLOW-SHARE');
            $permissions["ALLOW-FOLLOW"] = CB::checkCBsOption($configurations, 'ALLOW-FOLLOW');
            $permissions["ALLOW-ALLIANCE"] = CB::checkCBsOption($configurations, 'ALLOW-ALLIANCE');

            //INITIALIZED VARIABLES
            $posX = "";
            $posY = "";
            $fileId = 0;
            $dropDownOptions = [];

            //TOPIC FOLLOWERS
            $followers = isset($topicInformation->topic->followers) ? collect($topicInformation->topic->followers)->keyBy('user_key')->toArray() : [];
            $followersTooltip = array_slice($followers, 0, 10);

            //TOPIC STATE
            $closed = empty($topicInformation->topic->closed) ? false : true;

            //TOPIC PARAMETERS
            $parameters = $topicInformation->topic->parameters;
            $parameters = collect($parameters)->sortBy('position');
            foreach ($parameters as $parameter) {
                if ($parameter->type->code == 'image_map') {
                    $value = $parameter->pivot->value;
                    $fileId = $parameter->value;
                    if (count($value) > 0) {
                        $coordinates = explode("-", $value);
                        if (count($coordinates) == 2) {
                            if (strlen($coordinates[0]) > 0 && strlen($coordinates[1])) {
                                $posX = $coordinates[0];
                                $posY = $coordinates[1];
                            }
                        }
                    }
                } else if ($parameter->type->code == 'dropdown' || $parameter->type->code == 'category' || $parameter->type->code == 'budget' || $parameter->type->code == "radio_buttons") {
                    foreach ($parameter->options as $temp) {
                        $dropDownOptions[$temp->id] = $temp->label;
                        //GET GOOGLE MAP PIN FILE
                        if ($parameter->type->code == 'category') {
                            if ($parameter->pivot->value == $temp->id) {

                                //Get Topic Category
                                $topicInformation->topic->topic_category = $temp->label ?? null;

                                if (!empty($temp->pin)) {
                                    $pin = json_decode($temp->pin);
                                    if (!empty($pin[0])) {
                                        $categoryIcon = action('FilesController@download', ["id" => $pin[0]->id, "code" => $pin[0]->code, 1]);
                                    }
                                } else {
                                    $categoryIcon = '';
                                }

                                if (!empty($temp->icon)) {
                                    $icon = json_decode($temp->icon);
                                    if (!empty($icon[0])) {
                                        $categoryImage = action('FilesController@download', ["id" => $icon[0]->id, "code" => $icon[0]->code, 1]);
                                    }
                                } else {
                                    $categoryImage = '';
                                }

                            }
                        }
                    }
                }
            }

            //MESSAGES
            $topicMessage = null;
            if (count($messages) > 0) {
                $topicMessage = $messages[0];
            }
            if (count($messages) > 1) {
                $messages = array_slice($messages, 1, (count($messages) - 1));
            } else {
                $messages = [];
            }


            //BEGIN DEAL WITH THE USERS INFORMATION
            $followersKeys = collect($followers)->pluck('user_key')->toArray();
            $usersKeys = $followersKeys;
            $usersKeys[] = $topicInformation->topic->created_by;

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


            if (isset($topicData->positive_comments)) {
                foreach ($topicData->positive_comments as $positive_comment) {
                    $usersKeys[] = $positive_comment->created_by;
                }
            }
            if (isset($topicData->neutral_comments)) {
                foreach ($topicData->neutral_comments as $neutral_comments) {
                    $usersKeys[] = $neutral_comments->created_by;
                }
            }
            if (isset($topicData->negative_comments)) {
                foreach ($topicData->negative_comments as $negative_comments) {
                    $usersKeys[] = $negative_comments->created_by;
                }
            }

            $usersNames = [];
            if (count($usersKeys) > 0)
                $usersNames = json_decode(json_encode(Auth::getPublicListNames($usersKeys)), true);
            //END DEAL WITH THE USERS INFORMATION


            //CHECK IF CURRENT USER IS A MODERATOR FOR THIS TOPIC
            $isModerator = 0;
            if (Session::has('user')) {
                $currentUser = Session::get('user');
                if (One::isAdmin() || $topicInformation->topic->created_by == $currentUser->user_key) {
                    $isModerator = 1;
                } else {
                    if (!empty(collect($cb->moderators)->where('user_key', '=', $currentUser->user_key)->first())) {
                        $isModerator = 1;
                    }
                }
            }

            //DEAL WITH THE VOTES
            $voteKey = '';
            $notSubmitted = true;
            $voteType = [];
            $voteResults = [];

            if (!empty($topicInformation->cb_votes)) {

                $votes = $topicInformation->cb_votes;

                $voteKeys = [];
                foreach ($votes as $vote) {
                    $voteKeys[] = $vote->vote_key;
                }

                $eventsResponse = Vote::getAllShowEventsNoTranslation($voteKeys);

                //index of array = key of event
                $eventVotes = [];
                foreach ($eventsResponse as $ev) {
                    $eventVotes[$ev->key] = $ev;
                }
                $existVotesForSubmit = false;

                foreach ($votes as $vote) {
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
                            if (count($voteStatus->votes) > 0 || (count($voteStatus->votes) == 0 && isset($voteStatus->remaining_votes) && $voteStatus->remaining_votes->total > 0)) {
                                $existVotesForSubmit = true;
                            }
                        } else {
                            $existVotes = 0;
                        }

                        $generalSubmit = isset($voteStatus->can_vote) ? $voteStatus->can_vote : false;
                        if (!$generalSubmit) {
                            $notSubmitted = $generalSubmit;
                        }

                        $remainingVotes = $voteStatus->remaining_votes;

                        $allReadyVoted = [];
                        $allReadyVotedTypes = [];
                        foreach ($voteStatus->votes as $vtStatus) {
                            $allReadyVoted[$vtStatus->vote_key] = $vtStatus->value;

                        }
                        if (!empty($voteStatus->votes_types)) {
                            foreach ($voteStatus->votes_types as $types) {
                                $allReadyVotedTypes[$types->vote_key][$types->vote_type->id] = $types->value;
                            }
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
                            "name" => isset($vote->vote_key) ? $vote->name : null,
                            "method" => $methodName,
                            "key" => $voteKey,
                            "remainingVotes" => $remainingVotes,
                            "existVotes" => $existVotes,
                            "allReadyVoted" => $allReadyVoted,
                            "allReadyVotedTypes" => $allReadyVotedTypes,
                            "totalSummary" => $totalSummary,
                            "eventVote" => $eventVote,
                            "totalVotes" => isset($voteStatus->total_votes) ? json_decode(json_encode($voteStatus->total_votes), true) : null,
                            "configurations" => $vConfigurations,
                            "genericConfigurations" => $genericConfigurations,
                            "canVote" => $voteStatus->can_vote ?? true,
                            "canUnSubmit" => $canUnSubmit ?? false,
                            "submitedDate" => isset($voteStatus->submited_date) ? $voteStatus->submited_date->date : null,
                            "weight" => !empty($voteStatus->weightType) ? $voteStatus->weightType : null
                        ];

                    } else {
                        $remainingVotes = 0;
                        $voteKey = $vote->vote_key;
                        $eventVote = $eventVotes[$voteKey];
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
                        $currentVoteResults = Vote::getVoteResults($voteKey);
                        $voteResults[] = $currentVoteResults;
                        if (!empty($eventVote->end_date) && (Carbon::now() <= Carbon::parse($eventVote->end_date))) {
                            $existVotes = 1;
                        } else {
                            $existVotes = 0;
                        }

                        $genericConfigurations = [];
                        foreach ($vote->vote_configurations as $vtConf) {
                            $genericConfigurations[$vtConf->code] = $vtConf->value;
                        }
                        foreach ($eventVote->configurations as $vtConfig) {
                            $vConfigurations[$vtConfig->code] = $vtConfig->value;
                        }

                        $voteType[] = [
                            "name" => isset($vote->vote_key) ? $vote->name : null,
                            "method" => $methodName,
                            "key" => $voteKey,
                            "remainingVotes" => $remainingVotes,
                            "existVotes" => $existVotes,
                            "eventVote" => $eventVote,
                            "totalVotes" => isset($currentVoteResults->total_votes) ? json_decode(json_encode($currentVoteResults->total_votes), true) : null,
                            "configurations" => isset($vConfigurations) ? $vConfigurations : [],
                            "genericConfigurations" => isset($genericConfigurations) ? $genericConfigurations : [],
                            "disabled" => true,
                            "canVote" => false
                        ];
                    }
                }

                $canvote = [];
                foreach ($voteType as $vote) {
                    $canvote[$vote['method']] = $vote['canVote'];
                }

            }

            //THIS IS NOT CALLED ?
            if (ONE::isAuth() && ONE::checkAuthorization()) {
                $cbsData = $this->getCbsData($type);
                $cbAndTopics = $cbsData['cbAndTopics'];
                $votesByCb = $cbsData['votesByCb'];
            }

            //GET ALL MESSAGE FILES
            $fileCode = '';
            if ($fileId != 0) {
                $file = Files::getFile($fileId);
                $fileCode = $file->code;
            }
            $files = [];
            if ($topicMessage != null) {
                $files = CB::listFilesForTopic($topicMessage->post_key);
            }

            $prevIdea = null;
            $nextIdea = null;
            if (!empty($topicInformation->topicsKeys)) {
                $ideasKeys = $topicInformation->topicsKeys;
                $index = array_search($topicKey, $ideasKeys);

                if ($index == 0) {
                    if (count($ideasKeys) > 2) {
                        $prevIdea = $ideasKeys[count($ideasKeys) - 1];
                        $nextIdea = $ideasKeys[$index + 1];
                    } else if (count($ideasKeys) == 2) {
                        $nextIdea = $ideasKeys[count($ideasKeys) - 1];
                    }
                } else {
                    if (count($ideasKeys) > 2) {
                        if ($index == count($ideasKeys) - 1) {
                            $prevIdea = $ideasKeys[$index - 1];
                            $nextIdea = $ideasKeys[0];
                        } else {
                            $prevIdea = $ideasKeys[$index - 1];
                            $nextIdea = $ideasKeys[$index + 1];
                        }

                    } else if (count($ideasKeys) == 2) {
                        if ($index == count($ideasKeys) - 1) {
                            $prevIdea = $ideasKeys[0];
                        } else {
                            $nextIdea = $ideasKeys[count($ideasKeys) - 1];
                        }
                    }
                }

                $allowedToVote = false;
                if (empty($topicInformation->topic->start_date) && empty($topicInformation->topic->end_date)) {
                    $allowedToVote = true;
                } else {
                    $firstDate = Carbon::createFromFormat('Y-m-d H:i:s', $topicInformation->topic->start_date . ' 00:00:00');
                    $secondDate = Carbon::createFromFormat('Y-m-d H:i:s', $topicInformation->topic->end_date . ' 23:59:59');
                    if (Carbon::now()->between($firstDate, $secondDate)) {
                        $allowedToVote = true;
                    }
                }
            }

            $shareLinks = [];
            if (!empty($topicInformation->topic))
                $shareLinks["facebook"]["link"] = Share::load(action('PublicTopicController@show', [$cbKey, $topicInformation->topic->topic_key, 'type' => $type]), $topicInformation->topic->title)->facebook();

            $filesByType = [];
            if (isset($topicInformation->firstPostFiles))
                $filesByType = $topicInformation->firstPostFiles;

            $totalComments = isset($topicData->positive_comments) ? count($topicData->positive_comments) : 0;
            $totalComments += isset($topicData->neutral_comments) ? count($topicData->neutral_comments) : 0;
            $totalComments += isset($topicData->negative_comments) ? count($topicData->negative_comments) : 0;

            $topicInformation->topic->created_at = Carbon::parse($topicInformation->topic->created_at)->toDateString();

            if (!empty($voteType) && isset($voteType[1])) {
                $endVotingPhase = \Carbon\Carbon::parse($voteType[1]['eventVote']->end_date)->isPast();
            }

            $cooperators = CB::getCooperatorsList($request, $topicKey);
            if (!empty($cooperators)) {
                foreach ($cooperators->cooperators as $cooperator) {
                    foreach ($cooperators->permissions as $permission) {
                        if ($cooperator->type_id == $permission->id) {
                            $cooperatorsUsersKeysAndPermissions[$cooperator->user_key] = $permission->code;
                        }
                    }
                }
            }

            // Open Graph Tags - facebook
            $openGraphTags["title"] = !empty($topicData->topic->title) ? $topicData->topic->title : "";
            $openGraphTags["description"] = !empty($topicData->topic->contents) ? $topicData->topic->contents : "";
            $openGraphTags["image"] = (!empty($filesByType->images[0]->file_id) && $filesByType->images[0]->file_code) ? ["file_id" => $filesByType->images[0]->file_id, "file_code" => $filesByType->images[0]->file_code] : [];

            $data = [];
            $data['openGraphTags'] = $openGraphTags;
            $data['existVotesForSubmit'] = empty($existVotesForSubmit) ? null : $existVotesForSubmit;
            $data['moderators'] = $cb->moderators;
            $data['voteResults'] = empty($voteResults) ? null : $voteResults;
            $data['cbAndTopics'] = empty($cbAndTopics) ? null : $cbAndTopics;
            $data['votesByCb'] = empty($votesByCb) ? null : $votesByCb;
            $data['notSubmitted'] = $notSubmitted;
            $data['cb'] = $cb;
            $data['posX'] = $posX;
            $data['posY'] = $posY;
            $data['statistics'] = $statistics;
            $data['topicData'] = $topicData;
            $data['permissions'] = $permissions;
            $data['shareLinks'] = empty($shareLinks) ? null : $shareLinks;
            $data['allowedToVote'] = empty($allowedToVote) ? null : $allowedToVote;
            $data['filesByType'] = empty($filesByType) ? null : $filesByType;
            $data['type'] = $type;
            $data['prevIdea'] = $prevIdea;
            $data['nextIdea'] = $nextIdea;
            $data['messages'] = $messages;
            $data['messagesNotModerated'] = $messagesNotModerated;
            $data['comments'] = $comments;
            $data['countComments'] = $countComments;
            $data['topic'] = $topicInformation->topic;
            $data['topicMessage'] = $topicMessage;
            $data['cbKey'] = $cbKey;
            $data['topicKey'] = $topicKey;
            $data['usersNames'] = $usersNames;
            $data['configurations'] = $configurations;
            $data['cbParameters'] = $cbParameters;
            $data['parameters'] = $parameters;
            $data['files'] = $files;
            $data['posX'] = $posX;
            $data['posY'] = $posY;
            $data['dropDownOptions'] = $dropDownOptions;
            $data['isModerator'] = $isModerator;
            $data['voteKey'] = $voteKey;
            $data['fileId'] = $fileId;
            $data['fileCode'] = $fileCode;
            $data['voteType'] = $voteType;
            $data['closed'] = $closed;
            $data['comments'] = $comments;
            $data['parentTopic'] = $topicData->parentTopic;
            $data['childTopics'] = $topicData->childTopics ?? [];
            $data['positiveComments'] = $topicData->positive_comments ?? null;
            $data['neutralComments'] = $topicData->neutral_comments ?? null;
            $data['negativeComments'] = $topicData->negative_comments ?? null;
            $data['totalComments'] = $totalComments;
            $data['followers'] = $followers;
            $data['followersTooltip'] = $followersTooltip;
            $data['currentPhase'] = Session::get("SITE-CONFIGURATION.current_phase") ?? '';
            $data['categoryIcon'] = $categoryIcon ?? null;
            $data['securityConfigurationsVotes'] = [];
            $data['endVotingPhase'] = $endVotingPhase ?? null;
            $data['cooperators'] = $cooperators ?? null;
            $data['cooperatorsUsersKeysAndPermissions'] = $cooperatorsUsersKeysAndPermissions ?? null;
            $data['operationSchedules'] = $topicInformation->operationSchedules ?? null;
            $data['translations'] = $translations;

            $loginLevels = [];
            if (Session::has('user')) {
                $userKey = Session::get('user')->user_key;
                if(Session::get('SITE-CONFIGURATION.boolean_register_only_nif')==false){
                    $arrayVote = Vote::getEventLevelCbKey($cbKey);

                    $differenceUserLevelsLoginVotes = Orchestrator::UserLoginLevelsVotes($userKey, $cbKey, $arrayVote);

                    $data['securityConfigurationsVotes'] = $differenceUserLevelsLoginVotes;

                    $i = 0;
                    foreach ($data['securityConfigurationsVotes'] as $security) {
                        if (!empty($security->parameterUserTypes))
                            $i++;
                    }

                    $data['missing_login_levels'] = ($i > 0) ? true : false;

                    $loginLevels = CB::getUserLoginLevels($cbKey);
                }

            }

            $data['loginLevels'] = $loginLevels;
            //check if the user is accessing the topic trough a cooperation invite
            $coopToken = $request->input('coopToken');
            if ($coopToken) {
                $cooperation = EMPATIA::verifyCoopToken($coopToken);
                if (!empty(get_object_vars($cooperation))) {
                    $data['cooperationRequest'] = $cooperation;
                };
            }

            //check if the user is logged and if he's seeing a topic for which he was invited to be a cooperator
            if (isset($currentUser->user_key) && isset($cooperators) && !$coopToken) {
                $coopRequest = collect($cooperators->cooperators)->where('user_key', $currentUser->user_key)->first();
                if ($coopRequest && isset($coopRequest->cooperation->code)) {
                    if ($coopRequest->cooperation->code == 'requested') {
                        $data['cooperationRequest'] = $coopRequest;
                    }
                }
            }

            //  RETURNS THIS VIEW FOR THEMATIC CONSULTATIONS TO BE USED AS QUESTIONNAIRE
            if (((CB::checkCBsOption($data['configurations'], 'TOPIC-AS-PRIV-QUESTIONNAIRE')) || (CB::checkCBsOption($data['configurations'], 'TOPIC-AS-PUBLIC-QUESTIONNAIRE'))) && $data['type'] == 'tematicConsultation') {
                $jsonObj = json_encode(array('info' => 'Type: ' . $type, 'EntityLayout: ' . ONE::getEntityLayout()));
                LogsRequest::setAccess('topic_show', true, $topicKey, null, $cbKey, null, null, null, null, $jsonObj, Session::has('user') ? Session::get('user')->user_key : null);

                return view('public.' . ONE::getEntityLayout() . '.cbs.' . $type . '.topicQuestionnaire', $data);
            }
            if (isset($request->ajax_call)) {
                $jsonObj = json_encode(array('info' => 'Type: ' . $type, 'EntityLayout: ' . ONE::getEntityLayout()));
                LogsRequest::setAccess('topic_show', true, $topicKey, null, $cbKey, null, null, null, null, $jsonObj, Session::has('user') ? Session::get('user')->user_key : null);
                $sections = view('public.' . ONE::getEntityLayout() . '.cbs.' . $type . '.topic', $data)->renderSections();

                return $sections['content'];
            } else {

                $jsonObj = json_encode(array('info' =>'Type: '.$type, 'EntityLayout: '.ONE::getEntityLayout() ));

                // Topic show
                LogsRequest::setAccess('topic_show',true, $topicKey,null,$cbKey,null,null,null, null, $jsonObj,Session::has('user') ? Session::get('user')->user_key : null );
                $cbObj = CB::getCbByKey($cbKey);
                if( !empty($cbObj->template) ){
                    return view('public.' . ONE::getEntityLayout() . '.cb.' . $cbObj->template . '.topic', $data);
                }else{
                    return view('public.' . ONE::getEntityLayout() . '.cb.default.topic', $data);
                }
            }


        } catch (Exception $e) {
            $jsonObj = json_encode(array('error' => "Failure: " . $e->getMessage(), 'Type: ' . $request->type));
            LogsRequest::setAccess('topic_show', false, $topicKey, null, $cbKey, null, null, null, $jsonObj, null, Session::has('user') ? Session::get('user')->user_key : null);
            return redirect()->back()->withErrors(["topic.show" => $e->getMessage()]);
        }
    }


    public function getTopicDetailAjax(Request $request)
    {
        return $this->show($request, $request['cb_key'], $request['topic_key']);

    }

    /**
     * @param $cbParameter
     * @return string
     */
    public function getParameterValidationRule($cbParameter)
    {

        $validationString = '';
        $validationStringSeparator = '|';
        $addToken = false;
        if ($cbParameter->mandatory) {
            $validationString = 'required';
            $addToken = true;
        }
        switch ($cbParameter->code) {
            case "numeric":
                $validationString .= ($addToken ? $validationStringSeparator : '') . 'integer';
                break;
        }
        return $validationString;
    }


    /**
     * validate received cb parameters values
     * @param $cbKey
     * @param $requestTopic
     * @return bool
     */
    public function validateParameters($cbKey, $requestTopic)
    {
        $cbParameters = CB::getCbParameters($cbKey);
        $validationArray = [];
        if ($cbParameters) {
            foreach ($cbParameters as $parameter) {
                if (!empty($rule = $this->getParameterValidationRule($parameter)) && $parameter->code != 'image_map' && $parameter->code != 'google_maps') {
                    $validationArray['parameter_' . $parameter->id] = $rule;
                    $validationNames['parameter_' . $parameter->id] = 'field';
                }
            }
        }
        if (count($validationArray) > 0) {
            $this->validate($requestTopic, $validationArray, [], $validationNames ?? []);
        }
    }


    /**
     * Store the specified resource.
     * @param $cbKey
     * @param TopicRequest $requestTopic
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function store($cbKey, TopicRequest $requestTopic)
    {
        try {
            if (!EMPATIA::verifyCbOperationSchedule($cbKey, 'topic', 'create')) {
                return redirect()->back()->withErrors(trans('cbs.OutsidePermittedCreationData'));
            }
        } catch (Exception $e) {
            //
        }

        try {
            $topicData = CB::getCb($cbKey);
            $configurations = collect($topicData->configurations)->pluck('code')->toArray();
            $type = !empty($topicData->template) ? $topicData->template : 'default';

            $userCanCreateTopic = $this->userCanCreateTopic($configurations, $topicData->moderators, $topicData, $type);

            //IF THIS APPLIES THE userCanCreateTopic RETURNED A ACTION
            if ($userCanCreateTopic != 'CAN-CREATE') {
                if ($userCanCreateTopic == 'CAN-VIEW-FORM') {
                    Session::put('anonymous_topic_submit_inputs', $requestTopic->all());
                    return redirect()->action('PublicTopicController@registerMessage');
                }
                return redirect($userCanCreateTopic);
            }
            //verify parameters of topic
            $parametersToSend = [];
            foreach ($requestTopic->all() as $key => $value) {
                if (strpos($key, 'parameter_maps_required_') !== false) {

                    $id = str_replace("parameter_maps_required_", "", $key);

                    if ($value != '')
                        $parametersToSend[] = array('parameter_id' => $id, 'value' => $value);

                } else if (strpos($key, 'parameter_required_') !== false) {

                    $id = str_replace("parameter_required_", "", $key);

                    if ($value != '')
                        $parametersToSend[] = array('parameter_id' => $id, 'value' => $value);

                } else if (strpos($key, 'parameter_') !== false) {
                    $id = str_replace("parameter_", "", $key);

                    if ($value != '')
                        $parametersToSend[] = array('parameter_id' => $id, 'value' => $value);

                } //Save position Image!
                else if (strpos($key, 'xcoord_') !== false) {

                    $id = str_replace("xcoord_", "", $key);
                    $posX = $requestTopic["xcoord_" . $id];
                    $posY = $requestTopic["ycoord_" . $id];
                    $parametersToSend[] = array('parameter_id' => $id, 'value' => $posX . "," . $posY);
                    if ($requestTopic["coord_required_" . $id] === 'true' && empty($posX) && empty($posY)) {
                        throw new Exception(trans("publicTopic.map_empaville"));
                    }
                }
            }
            if ($type != 'qa') {
                try {
                    $this->validateParameters($cbKey, $requestTopic);
                } catch (ValidationException $e) {
                    $jsonObj = json_encode(array('error' => "Failure: " . $e->getMessage(), 'Type: ' . $requestTopic->type));
                    LogsRequest::setAccess('create_topic', false, null, null, $cbKey, null, null, null, $jsonObj, null, Session::has('user') ? Session::get('user')->user_key : null);

                    return redirect()->back()->withInput()->withErrors(["error" => ONE::transCb('topic_store_error_field_not_filled', $cbKey)]);
                }
            }

            //store new topic with parameters
            $topic = CB::setTopicWithParameters($cbKey, $requestTopic, $parametersToSend);
            $post_key = $topic->first_post->post_key;

            LogsRequest::setAccess('create_topic', true, $topic->topic_key, null, $cbKey, $post_key, null, null, null, 'Type: ' . $requestTopic->type, $topic->created_by);

            // FILES
            /*      if (!empty($requestTopic["files"])) {
                      // New files - based in oneFileUpload macro and in JSON format
                      $fileTypeIndex = 0;
                      $arrayFiles = [];
                      foreach (!empty($requestTopic["files"]) ? $requestTopic["files"] : [] as $fileTypeId => $fileStrArray) {
                          $fileObjArray = json_decode($fileStrArray);
                          foreach (!empty($fileObjArray) ? $fileObjArray : [] as $fileObj) {
                              $file = [
                                  'file_id' => $fileObj->id,
                                  'file_code' => $fileObj->code,
                                  'name' => $fileObj->name,
                                  'type_id' => $fileTypeId,
                                  'description' => $fileObj->description
                              ];
                              $file = (Object)$file;
                              $arrayFiles[] = $file;
                              $fileTypeIndex++;
                          }
                      }
                      // Store files for topic
                      if (!empty($arrayFiles) && !empty($topic->first_post->post_key)) {
                          CB::setFilesArrayForTopic($post_key, $arrayFiles);
                      }
                  } else if (Session::has('filesToUpload')) {
                      // Old files
                      $files = Session::get('filesToUpload');
                      foreach ($files as $file) {
                          CB::setFilesForTopic($post_key, $file);
                      }
                      Session::forget('filesToUpload');
                  } */

            //NOTIFICATIONS
            $cbConfigs = CB::getCbConfigurations($cbKey);
            foreach ($cbConfigs->configurations as $cbConfig) {
                if ($cbConfig->code == 'notification_create_topic') {
                    $sendEmail = $this->sendEmailNotificationGroups($cbKey, 'notification_create_topic', $type, $topic);
                }
                if ($cbConfig->code == 'notification_owner_create_topic') {
                    $owner = $topic->created_by;
                    $cooperators = CB::getCooperators($topic->topic_key);
                    if (is_array($cooperators))
                        $sendEmail = $this->sendEmailNotification($cbKey, 'notification_owner_create_topic', $type, $topic, $cooperators, $owner);
                }
            }


            if ($requestTopic->has("saveAndPublish") && ONE::checkCBsOption($configurations, 'TOPIC-PUBLISH-NEEDED') && ONE::checkCBsOption($configurations, 'TOPICS-CAN-BE-PUBLISHED')) {
                if ($this->publish($requestTopic, $cbKey, $topic->topic_key, true)) {
                    LogsRequest::setAccess('create_topic_stored_and_published', true, $topic->topic_key, null, $cbKey, $post_key, null, null, null, 'Type: ' . $requestTopic->type . ' topic.stored_and_published', $topic->created_by);
                    Session::flash('message', trans('topic.stored_and_published'));
                } else {
                    LogsRequest::setAccess('create_stored_but_failed_to_publish', true, $topic->topic_key, null, $cbKey, $post_key, null, null, null, 'Type: ' . $requestTopic->type . ' topic.stored_but_failed_to_publish', $topic->created_by);
                    Session::flash('message', trans('topic.stored_but_failed_to_publish'));
                }
            } else {
                LogsRequest::setAccess('create_topic_store_ok', true, $topic->topic_key, null, $cbKey, $post_key, null, null, null, 'Type: ' . $requestTopic->type . ' topic.store_ok', $topic->created_by);
                Session::flash('message', trans('topic.store_ok'));
            }

            /*if(isset($configurations) && !ONE::isAuth()){
                return redirect()->action('AuthController@login');
            }*/

            if (View::exists('public.' . ONE::getEntityLayout() . '.cb.default.formSuccess')) {
                return redirect()->action('PublicTopicController@formSuccess', ['cbKey' => $cbKey, 'topicKey' => $topic->topic_key, 'type' => $type]);
            }

            return redirect()->action('PublicCbsController@show', ['cbKey' => $cbKey, 'type' => $type]);

        } catch (Exception $e) {
            $jsonObjCreateTopic = json_encode(array('params' =>  'title: ' .$requestTopic["title"],
                'contents: ' .$requestTopic["contents"],
                'summary: ' .$requestTopic["summary"],
                'created_on_behalf: ' .$requestTopic["created_on_behalf"],
                'start_date: ' .array_key_exists("start_date",$requestTopic) ? $requestTopic["start_date"] : '',
                'end_date: ' . array_key_exists("end_date",$requestTopic) ? $requestTopic["end_date"] : '',
                'parent_topic_key: ' .$requestTopic["parent_topic_key"] ? $requestTopic["parent_topic_key"] : '',
                'topic_creator: ' .$requestTopic["topic_creator"] ? $requestTopic["topic_creator"] : ''));
            $jsonObj = json_encode(array('error' => "Failure: ".$e->getMessage(), 'Type: '.$requestTopic->type ));
            LogsRequest::setAccess('create_topic',false, null,null, $cbKey,  null,  null,null,  $jsonObj, $jsonObjCreateTopic, Session::has('user') ? Session::get('user')->user_key : null );
            return redirect()->back()->withErrors(["topic.store" => $e->getMessage()])->withInput();
        }
    }

    /**
     * Update the specified resource.
     *
     * @param TopicRequest $requestTopic
     * @param $cbKey
     * @param $topicKey
     * @return Response
     * @internal param TopicRequest $requestForum
     * @internal param int $id
     */
    public function update(TopicRequest $requestTopic, $cbKey, $topicKey)
    {
        try {
            if (!EMPATIA::verifyCbOperationSchedule($cbKey, 'topic', 'update')) {
                return redirect()->back()->withErrors(trans('cbs.OutsidePermittedUpdateData'));
            }
        } catch (Exception $e) {
        }

        try {
            $topicData = CB::getCb($cbKey);
            $type = !empty($topicData->template) ? $topicData->template : 'default';

            $parametersToSend = [];
            foreach ($requestTopic->all() as $key => $value) {

                if (strpos($key, 'parameter_maps_required_') !== false) {

                    $id = str_replace("parameter_maps_required_", "", $key);

                    if ($value != '')
                        $parametersToSend[] = array('parameter_id' => $id, 'value' => $value);

                } else if (strpos($key, 'parameter_required_') !== false) {

                    $id = str_replace("parameter_required_", "", $key);

                    if ($value != '')
                        $parametersToSend[] = array('parameter_id' => $id, 'value' => $value);

                } else if (strpos($key, 'parameter_') !== false) {
                    $id = str_replace("parameter_", "", $key);

                    if ($value != '')
                        $parametersToSend[] = array('parameter_id' => $id, 'value' => $value);

                } //Save position Image!
                else if (strpos($key, 'xcoord_') !== false) {
                    $id = str_replace("xcoord_", "", $key);

                    $posX = $requestTopic["xcoord_" . $id];
                    $posY = $requestTopic["ycoord_" . $id];
                    $parametersToSend[] = array('parameter_id' => $id, 'value' => $posX . "," . $posY);
                    if ($requestTopic["coord_required_" . $id] === 'true' && empty($posX) && empty($posY)) {
                        throw new Exception(trans("publicTopic.map_empaville"));
                    }
                }
            }
            try {

                $this->validateParameters($cbKey, $requestTopic);

            } catch (ValidationException $e) {

                return redirect()->back()->withInput();
            }
            $topic = CB::updateTopicWithParameters($topicKey, $requestTopic, $parametersToSend);

            $topicTmp = CB::getTopicParameters($topicKey, "", true); // this can be removed in future
            $post_key = $topicTmp->first_post->post_key;

            // FILES
            if (!empty($requestTopic["files"])) {
                // New files - based in oneFileUpload macro and in JSON format
                $fileTypeIndex = 0;
                $arrayFiles = [];
                foreach (!empty($requestTopic["files"]) ? $requestTopic["files"] : [] as $fileType => $fileStrArray) {

                    if (!empty($fileStrArray) && !empty(json_decode($fileStrArray))) {
//                        $fileTypeId = (isset($requestTopic["file_type_id"]) && !empty($requestTopic["file_type_id"][$fileTypeIndex])) ? $requestTopic["file_type_id"][$fileTypeIndex] : 1;
                        $fileObjArray = json_decode($fileStrArray);

                        foreach (!empty($fileObjArray) ? $fileObjArray : [] as $fileObj) {
                            if (is_array($fileObj)) {
                                foreach ($fileObj as $file) {
                                    $file = [
                                        'file_id' => $file->id,
                                        'file_code' => $file->code,
                                        'name' => $file->name,
                                        'type_id' => $file,
                                        'description' => $file->description
                                    ];

                                    $file = (Object)$file;
                                    $arrayFiles[] = $file;
                                    $fileTypeIndex++;
                                }
                            } else {
                                $file = [
                                    'file_id' => $fileObj->id,
                                    'file_code' => $fileObj->code,
                                    'name' => $fileObj->name,
                                    'type_id' => $fileType,
                                    'description' => $fileObj->description
                                ];
                                $file = (Object)$file;
                                $arrayFiles[] = $file;
                                $fileTypeIndex++;
                            }
                        }
                    }
                }
                // Update files for topic
                if (!empty($arrayFiles) && !empty($post_key)) {
                    CB::updateFilesArrayForTopic($post_key, $arrayFiles);
                }
            }

            // Update files list
            $jsonFileList = [];
            if (!empty($post_key)) {
                $filesList = CB::listFilesForTopic($post_key);
                // Convert to json filelist
                foreach (!empty($filesList) ? $filesList : [] as $fileObj) {
                    $file = [
                        'id' => $fileObj->file_id,
                        'code' => $fileObj->file_code,
                        'name' => $fileObj->name,
                        'description' => $fileObj->description
                    ];
                    $file = (Object)$file;
                    $jsonFileList[] = $file;
                }
            }
            //Notifications
            $cbConfigs = CB::getCbConfigurations($cbKey);
            foreach ($cbConfigs->configurations as $cbConfig) {
                if ($cbConfig->code == 'notification_edit_topic') {
                    $sendEmail = $this->sendEmailNotificationGroups($cbKey, 'notification_edit_topic', $type, $topic);
                } else if ($cbConfig->code == 'notification_content_change') {
                    $followers = CB::getFollowersTopic($topic->topic_key);
                    $sendEmail = $this->sendEmailNotification($cbKey, 'notification_content_change', $type, $topic, $followers, null);
                } else if ($cbConfig->code == 'notification_owner_edit_topic') {
                    $owner = $topic->created_by;
                    $cooperators = CB::getCooperators($topic->topic_key);
                    if (is_array($cooperators))
                        $sendEmail = $this->sendEmailNotification($cbKey, 'notification_owner_edit_topic', $type, $topic, $cooperators, $owner);
                }
            }

            if ($requestTopic->has("saveAndPublish")) {
                if ($this->publish($requestTopic, $cbKey, $topic->topic_key, true))
                    Session::flash('message', trans('topic.updated_and_published'));
                else
                    Session::flash('message', trans('topic.updated_but_failed_to_publish'));
            } else
                Session::flash('message', trans('topic.update_ok'));

            return redirect()->action('PublicTopicController@show', ['cbKey' => $cbKey, 'topicKey' => $topicKey, 'type' => $type, 'jsonFileList' => $jsonFileList]);

        } catch (Exception $e) {
            return redirect()->back()->withErrors(["topic.update" => $e->getMessage()])->withInput();
        }
    }

    /**
     * Destroy the specified resource.
     * @param Request $request
     * @param $cbKey
     * @param $topicKey
     * @return $this|string
     */
    public function destroy(Request $request, $cbKey, $topicKey)
    {
        try {
            CB::deleteTopic($topicKey);

            Session::flash('message', trans('PublicCbs.topicDestroyOk'));
            return action('PublicCbsController@show', ['cbKey' => $cbKey, 'type' => $request->type]);

        } catch (Exception $e) {
            return redirect()->back()->withErrors(["topic.destroy" => $e->getMessage()]);
        }
    }

    /**
     * Show confirm popup to remove the specified resource from storage.
     * @param Request $request
     * @param $cbKey
     * @param $topicKey
     * @return View
     */
    public function delete(Request $request, $cbKey, $topicKey)
    {
        $data = array();

        $data['action'] = action("PublicTopicController@destroy", ['cbKey' => $cbKey, 'topicKey' => $topicKey, 'type' => $request->type]);
        $data['title'] = trans('privatePublicTopic.delete');
        $data['msg'] = trans('privatePublicTopic.are_you_sure_you_want_to_delete_this_content');
        $data['btn_ok'] = trans('privatePublicTopic.delete');
        $data['btn_ko'] = trans('privatePublicTopic.cancel');


        return view("_layouts.deleteModal", $data);
    }

    /**
     * @param PostRequest $request
     * @param $cbKey
     * @param $topicKey
     * @return string
     */
    public function revertVersionTopic(PostRequest $request, $cbKey, $topicKey)
    {
        try {
            CB::revertPost($request);
            return 'true';
        } catch (Exception $e) {
            return 'false';
        }
    }

    /**
     * @param PostRequest $request
     * @param $cbKey
     * @param $topicKey
     * @return string
     */
    public function activeVersionTopic(PostRequest $request, $cbKey, $topicKey)
    {
        try {
            CB::revertPost($request);
            return 'true';
        } catch (Exception $e) {
            return 'false';
        }
    }

    /**
     * ------------------------------------------------------
     * //CHANGES MADE BY JORGE VALE:
     * ------------------------------------------------------
     * - removed the PostRequest -> doesn't validate anything
     * - made the method static.
     * ------------------------------------------------------
     * Vote the specified resource.
     * @param PostRequest $request
     * @return string
     */
    public static function vote(Request $request)
    {
        try {

            if (Agent::isPhone()) {
                $source = "mobile";
            } elseif (Agent::isTablet()) {
                $source = "tablet";
            } elseif (Agent::isDesktop()) {
                $source = "pc";
            } else {
                $source = "other";
            }
            $userKey = isset($request->userKey) ? $request->userKey : null;
            if ($userKey) {
                $response = Vote::setVote($request->voteKey, $request->topicKey, $request->value, $source, $userKey);

                LogsRequest::setAccess('create_vote', true, $request->topicKey, null, null, null, null, $request->voteKey, null, 'source:' . $source, $userKey);

            } else {
                $response = Vote::setVote($request->voteKey, $request->topicKey, $request->value, $source, null, $request->weightTypeId);

                LogsRequest::setAccess('create_vote', true, $request->topicKey, null, null, null, null, $request->voteKey, null, 'source:' . $source, $userKey);

            }
            if (!empty($response)) {
                $data = [];

                $data["vote"] = $response->value;

                if (isset($response->summary->total))
                    $data["total"] = $response->summary->total;

                if (isset($response->summary->user_votes))
                    $data["userVotes"] = $response->summary->user_votes;

                if (isset($response->summary->negative))
                    $data["negative"] = $response->summary->negative;

                if (isset($response->total_votes)) {
                    $totals = json_decode(json_encode($response->total_votes), true);
                    $data['totalPositive'] = isset($totals[$request->topicKey]) ? $totals[$request->topicKey]['positive'] : '0';
                    $data['totalNegative'] = isset($totals[$request->topicKey]) ? $totals[$request->topicKey]['negative'] : '0';
                }
                return json_encode($data);
            } else {
                $msg = '';
                switch ($response->error) {
                    case 'no_vote_available':
                        $msg = trans('errorVote.noVoteAvailable');

                        LogsRequest::setAccess('create_vote', false, $request->topicKey, null, null, null, null, null, null, 'no_vote_available, source:' . $source, $userKey);
                        break;
                    case 'can_not_vote':
                        $msg = trans('errorVote.canNotVote');
                        LogsRequest::setAccess('create_vote', false, $request->topicKey, null, null, null, null, null, null, 'can_not_vote, source:' . $source, $userKey);
                        break;
                }
                return json_encode(['errorMsg' => $msg]);
            }


        } catch (Exception $e) {
            $jsonObj = json_encode(array('error' => "Failure: " . $e->getMessage(), 'voteKey: ' . $request->voteKey, 'topicKey: ' . $request->topicKey, 'value: ' . $request->value, 'source: ' . Agent::isPhone() ? "mobile" : (Agent::isTablet() ? "tablet" : (Agent::isDesktop() ? "pc" : "other"))));
            LogsRequest::setAccess('create_vote', false, $request->topicKey, null, null, null, null, null, $jsonObj, null, Session::has('user') ? Session::get('user')->user_key : ($request->userKey ? $request->userKey : null));
            return json_encode(['errorMsg' => $e->getMessage()]);
            //return json_encode($e->getMessage());
        }
    }

    /**
     * @param $type
     * @return array|View
     */
    private function getCbsData($type)
    {
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
        foreach ($cbsData as $cbTemp) {
            if ($cbTemp->start_date <= Carbon::now() && ($cbTemp->end_date >= Carbon::now() || !$cbTemp->end_date)) {
                // check if exist votes
                $existVotes = 0;
                $existVotesForSubmit = false;
                $voteKey = '';
                $allReadyVoted = [];
                $remainingVotes = 0;
                $cbVotes = CB::getCbVotes($cbTemp->cb_key);

                $existVotes = 0;
                foreach ($cbVotes as $vote) {
                    $vConfigurations = [];
                    $voteType = [];
                    $voteKey = $vote->vote_key;

                    //vote status
                    $voteStatus = Vote::getVoteStatus($voteKey);
                    if ($voteStatus->vote) {
                        $existVotes = 1;
                        $existVotesForSubmit = true;
                    } else {
                        $existVotes = 0;
                    }

                    $remainingVotes = $voteStatus->remaining_votes;

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
                        "method" => $methodName,
                        "key" => $voteKey,
                        "remainingVotes" => $remainingVotes,
                        "existVotes" => $existVotes,
                        "totalVotes" => isset($voteStatus->total_votes) ? json_decode(json_encode($voteStatus->total_votes), true) : null,
                        "configurations" => $vConfigurations,
                    ];
                }
                $votesByCb[$cbTemp->cb_key] = $voteType;
            }
        }
        $data = ['cbAndTopics' => $cbsData, 'votesByCb' => $votesByCb, 'existVotesForSubmit' => $existVotesForSubmit];
        return $data;
    }

    /**
     * Download the specified resource.
     * @param Request $request
     * @param $cbKey
     * @param $topicKey
     * @return $this
     */
    public function download(Request $request, $cbKey, $topicKey)
    {
        try {
            // Type Check
            if (empty($request->type) || !isset($this->cbType[$request->type])) {
                throw new Exception(trans('error.noCBtype'));
            }

            $type = $this->cbType[$request->type];
            $cb = CB::getCBAndTopics($cbKey)->cb;
            $topicData = CB::getTopicDataWithChilds($topicKey);
            $topic = CB::getTopicParameters($topicKey, "", true);
            $topicMessage = null;
            $messages = $topicData->posts;
            if (count($messages) > 0) {
                $topicMessage = $messages[0];
            }

            /* --------------------- */
            /* Initialized variables */
            $posX = "";
            $posY = "";
            $fileId = 0;
            $dropDownOptions = [];

            $topic = CB::getTopicParameters($topicKey, "", true);
            $parameters = $topic->parameters;

            foreach ($parameters as $parameter) {
                if ($parameter->type->code == 'image_map') {
                    $value = $parameter->pivot->value;
                    $fileId = $parameter->value;

                    if (count($value) > 0) {
                        $coordinates = explode("-", $value);

                        if (count($coordinates) == 2) {
                            if (strlen($coordinates[0]) > 0 && strlen($coordinates[1])) {
                                $posX = $coordinates[0];
                                $posY = $coordinates[1];
                            }
                        }
                    }

                } else if ($parameter->type->code == 'dropdown' || $parameter->type->code == 'category' || $parameter->type->code == 'budget') {
                    foreach ($parameter->options as $temp) {
                        $dropDownOptions[$temp->id] = $temp->label;
                    }
                }
            }

            $filesByType = CB::listFilesByType($topic->first_post->post_key);

            // return view('public.'.ONE::getEntityLayout().'.cbs.'.$type.'.pdf.topic', compact('topicData','cb','topic','topicMessage','parameters','filesByType','dropDownOptions'));
            $pdf = PDF::loadView('public.' . ONE::getEntityLayout() . '.cbs.' . $type . '.pdf.topic', compact('topicData', 'cb', 'topic', 'topicMessage', 'parameters', 'filesByType', 'dropDownOptions'))
                ->setPaper('a4', 'portrait')->setWarnings(false);
            return $pdf->download('topic.pdf');


        } catch (Exception $e) {
            return redirect()->back()->withErrors(["topic.show" => $e->getMessage()]);
        }
    }

    public function followTopic(Request $request)
    {
        try {

            $topicKey = $request->topic_key;
            $actionType = $request->action_type;
            if (empty($topicKey) || empty($actionType)) {
                return 'false';
            }
            switch ($actionType) {
                case 'follow_topic':
                    CB::followTopic($topicKey);
                    return 'true';
                    break;
                case 'unfollow_topic':
                    CB::unfollowTopic($topicKey);
                    return 'true';
                    break;
            }

            return 'false';

        } catch (Exception $e) {
            return 'false';

        }

    }


    public function createAlly(Request $request, $cbKey, $topicKey)
    {
        $data = [
            "cbKey" => $cbKey,
            "topicKey" => $topicKey,
            "topics" => []
        ];

        $topicsDB = CB::getUserTopics(One::getUserKey())->topics;
        foreach ($topicsDB as $topic) {
            $data["topics"][$topic->topic_key] = $topic->title;
        }

        $html = view('public.' . ONE::getEntityLayout() . '.cbs.createAlliance', $data)->render();

        return response()->json(["content" => $html], 200);
    }

    public function storeAlly(Request $request, $cbKey, $topicKey)
    {
        if ($request->has("destiny_topic")) {
            CB::createAlliance($topicKey, $request->input("destiny_topic"), $request->input("original_request"));

            Session::flash("message", trans("publicCbs.ally_request_sent"));
        } else
            Session::flash("error", trans("publicCbs.ally_request_failed"));

        return redirect()->back();
    }

    public function updateAlly(Request $request, $cbKey, $topicKey, $allyKey)
    {
        if ($request->has("response_explanation")) {
            CB::responseToAlliance($allyKey, ($request->input("response") ? 1 : 0), $request->input("response_explanation"));

            Session::flash("message", trans("publicCbs.ally_responded"));
        } else
            Session::flash("error", trans("publicCbs.ally_failed_to_respond"));

        return redirect()->back();
    }

    /**
     * Retrieve data for que Questionnaires Modal
     * @param $cbKey
     * @param $topicKey
     * @param $code
     * @param null $voteKey
     * @return string
     */
    public function getQuestionnaireModalData($cbKey, $topicKey, $code, $voteKey = null)
    {
        try {
            $cbQuestionnaires = CB::getQuestionnaires($cbKey);
            $questionnaire = null;
            $questionnaireTemplate = null;
            $showQuestionnaire = false;
            $questionnaireModal = [];

            if (!empty($cbQuestionnaires)) {
                foreach ($cbQuestionnaires as $key => $cbQuestionnaire) {
                    if ($key == $code) {
                        $questionnaire = $cbQuestionnaire;
                    }
                }

                if (!is_null($voteKey)) {
                    foreach ($questionnaire as $key => $value) {
                        if ($key == $voteKey) {
                            $questionnaire = $value;
                        }
                    }
                }

//                $topic = CB::getTopic($topicKey); //TODO check if is really necessary

                if (!is_null($questionnaire)) {
                    if (!empty($questionnaire->cb_questionnaire_translation)) {
                        $questionnaireTemplate = collect($questionnaire->cb_questionnaire_translation)
                            ->where('language_code', '=', Session::get('LANG_CODE'))
                            ->first();
                        if (is_null($questionnaireTemplate)) {
                            $questionnaireTemplate = collect($questionnaire->cb_questionnaire_translation)
                                ->where('language_code', '=', Session::get('LANG_CODE_DEFAULT'))
                                ->first();
                        }
                    }


                    //CHECK IF QUESTIONNAIRE HAS BEEN ANSWERED ???
                    $formResponse = Questionnaire::verifyReply($questionnaire->questionnarie_key);

                    // GET USER IN SESSION
                    $user = Session::has('user') ? Session::get('user') : null;

                    if (!is_null($user)) {
                        if ($formResponse == false) {

                            //CHECK IF USER ALREADY IGNORED QUESTIONNAIRE
                            $ignoreQuestionnaire = CB::getCbQuestionnaireUser($questionnaire->cb_questionnarie_key, $user->user_key);


                            if (empty($ignoreQuestionnaire)) {
                                $showQuestionnaire = true;
                            } else {
                                $currentDate = Carbon::now();
                                $daysIgnoreUser = Carbon::parse($ignoreQuestionnaire->pivot->date_ignore);

                                $differenceInDays = $currentDate->diffInDays($daysIgnoreUser);

                                if ($differenceInDays > $questionnaire->days_to_ignore) {
                                    $showQuestionnaire = true;
                                }
                            }

                            if ($showQuestionnaire == true) {
                                $questionnaireModal['cbQuestionnaireKey'] = $questionnaire->cb_questionnarie_key;
                                $questionnaireModal['questionnaireKey'] = $questionnaire->questionnarie_key;
                                $questionnaireModal['content'] = $questionnaireTemplate->content ?? null;
                                $questionnaireModal['accept'] = $questionnaireTemplate->accept ?? null;
                                $questionnaireModal['ignore'] = $questionnaireTemplate->ignore ?? null;
                                $questionnaireModal['questionnaireIgnore'] = $questionnaire->ignore;

                                if (is_null($voteKey)) {
                                    Session::put('questionnaireModal', $questionnaireModal);
                                    return "true";
                                } else {
                                    return view('public.' . ONE::getEntityLayout() . '.cbs.questionnaireModal', compact('questionnaireModal'));
                                }
                            }
                        }
                    }
                }
            }

            return "false";
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * Auxiliary Method - to return a View with message to user after Topic Creation
     *
     * @return View
     */
    public function formSuccess(Request $request)
    {
        $cbKey = !empty($request->get('cbKey')) ? $request->get('cbKey') : null;
        $topicKey = !empty($request->get('topicKey')) ? $request->get('topicKey') : null;
        $type = !empty($request->get('type')) ? $request->get('type') : null;

        $message = 0;

        /*Retrieve data for que Questionnaires Modal
         */
        $this->getQuestionnaireModalData($cbKey, $topicKey, 'create_topic', null);

        $data = [];
        $data['message'] = $message;
        $data['cbKey'] = $cbKey;
        $data['topicKey'] = $topicKey;
        $data['type'] = $type;

        return view('public.' . ONE::getEntityLayout() . '.cb.' . 'default'  . '.formSuccess', $data);
    }


    /**
     * GET THE TOPIC COMMENTS THREW AJAX
     * WITH PAGINATION
     * @param Request $request
     * @return View
     */
    public function getTopicComments(Request $request)
    {
        try {
            $type = $request['type'];
            $cbKey = $request['cbKey'];
            $topicKey = $request['topicKey'];

            $topicData = CB::getTopicPostsWithPagination($request, $topicKey);

            $messages = $topicData->posts ?? [];
            $messagesNotModerated = $topicData->postsToModerate ?? [];
            $totalComments = $topicData->totalComments;
            if (!empty($request['pageToken'])) {
                $comments = $topicData->posts ?? [];
                $data['styles'] = [];
                $data['commentType'] = $request['typeOfComment'];
            } else {
                $comments = (empty($topicData->positive_comments) && empty($topicData->neutral_comments) && empty($topicData->pnegative_comments)) ? false : true;
            }

            //BEGIN TO DEAL WITH THE USERS INFORMATION
            $usersKeys[] = $topicData->topic->created_by;
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
            if (count($usersKeys) > 0) {
                $usersNames = json_decode(json_encode(Auth::getPublicListNames($usersKeys)), true);
            }
            //END DEAL WITH THE USERS INFORMATION

            $data['isModerator'] = false; // VERIFY THIS
            $data['pageToken'] = $topicData->pageToken;
            $data['type'] = $type;
            $data['cbKey'] = $cbKey;
            $data['usersNames'] = $usersNames;
            $data['topic'] = $topicData->topic;
            $data['configurations'] = $topicData->configurations;
            $data['messages'] = $messages;
            $data['messagesNotModerated'] = $messagesNotModerated;
            $data['topicKey'] = $topicKey;
            $data['comments'] = $comments;
            $data['parentTopic'] = $topicData->parentTopic;
            $data['positiveComments'] = $topicData->positive_comments ?? null;
            $data['neutralComments'] = $topicData->neutral_comments ?? null;
            $data['negativeComments'] = $topicData->negative_comments ?? null;
            $data['totalComments'] = $totalComments;
            $data['securityConfigurations'] = [];


            if (Session::get('user') != null && ONE::userRole() == 'user') {

                $userKey = Session::get('user')->user_key;
                $code = 'comment';
                $diferenceUserLevelsLogin = Orchestrator::UserLoginLevels($userKey, $cbKey, $code);

                if ($diferenceUserLevelsLogin != []) {
                    $data['securityConfigurations'] = $diferenceUserLevelsLogin;
                }
            }
            if (isset($request['pageToken'])) {
                if (isset($request['postToLoadRepliesFrom'])) {
                    $post['id'] = $request['postToLoadRepliesFrom'];
                    $post['replies'] = json_decode(json_encode($comments), true);
                    $data['message'] = $message;
                    return view('public.' . ONE::getEntityLayout() . '.cb.commentItemsNormalReplies', $data);
                }
                if (!is_null($request['typeOfComment'])) {
                    return view('public.' . ONE::getEntityLayout() . '.cb.commentItems', $data);
                } else {
                    return view('public.' . ONE::getEntityLayout() . '.cb.commentItemsNormal', $data);
                }
            }

            return view('public.' . ONE::getEntityLayout() . '.cb.commentsSection', $data);
        } catch (Exception $e) {
            return json_encode(['error' => "Can't load comments"]);
        }
    }


    /**
     * CHECK IF A USER CAN CREATE A TOPIC
     * @param $configurations
     * @param $moderators
     * @param $cb
     * @param $type
     * @return string
     */
    public static function userCanCreateTopic($configurations, $moderators, $cb, $type)
    {

        $today = Carbon::today()->format('Y-m-d');


        $isModerator = collect($moderators)->where('user_key', '=', ONE::getUserKey())->first();
        //CHECK IF CURRENT USER IS A MODERATOR OR A ADMIN
        if (!is_null($isModerator)) {
            return 'CAN-CREATE';
        }


        //CHECK IF CB ALLOWS CREATION OF TOPICS
        if (!CB::checkCBsOption($configurations, 'CREATE-TOPIC')) {
            return action('PublicCbsController@show', ['cbKey' => $cb->cb_key, 'type' => $type]);
        }

        //CHECK IF CB IS CLOSED
        if (!empty($cb->end_date) && ($today > $cb->end_date)) {
            return action('PublicCbsController@show', ['cbKey' => $cb->cb_key, 'type' => $type]);
        }

        //CHECK USER ACCESS
        if (ONE::isAuth()) {
            //CHECK IF USER ALREADY CREATED TOPICS
            if (CB::checkCBsOption($configurations, 'ONLY-ONE-TOPIC')) {
                $topics = CB::getAllUserTopics($cb->cb_key)->topics;
                if (count($topics) > 0) {
                    return action('SubPagesController@show', ["cbs", "notifyLimit"]);
                }
            }
        } else {

            //CHECK CB ACCESS
            if (!CB::checkCBsOption($configurations, 'CREATE-TOPICS-ANONYMOUS')) {

                //CHECK CB ACCESS - TOPIC FORM
                if (CB::checkCBsOption($configurations, 'ANONYMOUS-CREATE-TOPIC-ACCESS')) {
                    return 'CAN-VIEW-FORM';
                }

                return action('AuthController@login');
            }

        }

        return 'CAN-CREATE';

    }


    public function publish(Request $request, $cbKey, $topicKey, $internal = false)
    {
        try {
            CB::publishUserTopic($topicKey);
            if ($internal)
                return true;
            else
                return redirect()->back();
        } catch (Exception $e) {
            if ($internal)
                return false;
            else
                return redirect()->back()->withErrors(["topic.publish" => $e->getMessage()]);
        }
    }


    /** Show register message for cb submit
     * @return PublicTopicController|\Illuminate\Http\RedirectResponse
     */
    public function registerMessage()
    {
        try {
            return view('public.' . ONE::getEntityLayout() . '.cbs.registerMessage');
        } catch (Exception $e) {
            return redirect()->back()->withErrors([trans("publicTopic.register_message") => $e->getMessage()]);
        }
    }


    /** Store topic with inputs save in session
     * @return PublicTopicController|\Illuminate\Http\RedirectResponse
     */
    public function sessionStoreTopic()
    {
        try {
            if (Session::has('anonymous_topic_submit_inputs')) {
                $request = Session::get('anonymous_topic_submit_inputs');
                $cbKey = $request['cb_key'];
                $type = $request['type'];
            } else {
                return redirect()->back();
            }
            $topicRequest = new TopicRequest($request);
            Session::forget('anonymous_topic_submit_inputs');
            return $this->store($cbKey, $topicRequest);
        } catch (Exception $e) {
            return redirect()->back()->withErrors([trans("publicTopic.register_message") => $e->getMessage()]);
        }
    }


    public function sendEmailNotificationGroups($cbKey, $code, $type, $topic)
    {

        $template = '';
        $groups = [];

        //get cb template
        $cbTemplates = CB::getCbTemplates($cbKey);
        foreach ($cbTemplates as $cbTemplate) {
            if ($cbTemplate->configuration_code == $code) {
                $template = $cbTemplate;
            }
        }

        if ($template != '') {
            $cbConfigurations = CB::getCbConfigurations($cbKey);
            foreach ($cbConfigurations->configurations as $cbConfiguration) {
                if ($template->configuration_code == $cbConfiguration->code) {
                    $groups = json_decode($cbConfiguration->pivot->value);
                }
            }

            $usersEmail = [];
            foreach ($groups as $group) {
                $users = Orchestrator::getUsersByEntityGroupKey($group);
                foreach ($users as $user) {
                    $usersEmail[] = Orchestrator::getUserEmail($user->user_key);
                }
            }

            $userKey = (Session::get('user'))->user_key;

            $url = "<a href='" . action('TopicController@show', [$type, $cbKey, $topic->topic_key]) . "'>" . $topic->title . "</a>";

            $tags = ["topic" => $url, "title_topic" => $topic->title];

            $sendEmail = Notify::sendEmailByTemplateKey($template->template_key, $usersEmail, $userKey, $tags);
        } else {
            Session::flash('message', trans('topic.fail_send_email'));

        }
    }

    public function sendEmailNotification($cbKey, $code, $type, $topic, $users, $owner)
    {

        $template = '';

        //get cb template
        $cbTemplates = CB::getCbTemplates($cbKey);
        foreach ($cbTemplates as $cbTemplate) {
            if ($cbTemplate->configuration_code == $code) {
                $template = $cbTemplate;
            }
        }

        if ($template != '') {
            $usersEmail = [];
            foreach ($users as $user) {
                $usersEmail[] = Orchestrator::getUserEmail($user->user_key);
            }

            if ($owner != null) {
                $userEmail = Orchestrator::getUserEmail($owner);
                $usersEmail[] = $userEmail;
            }

            $userKey = (Session::get('user'))->user_key;

//            $url = "<a href='".action('TopicController@show', [$type, $cbKey, $topic->topic_key])."'>".$topic->title."</a>";
            $url = "<a href='" . action('PublicTopicController@show', [$cbKey, $topic->topic_key, 'type' => $type]) . "'>" . $topic->title . "</a>";


            $tags = ["topic" => $url, "title_topic" => $topic->title];

            $sendEmail = Notify::sendEmailByTemplateKey($template->template_key, $usersEmail, $userKey, $tags);
        } else {
            Session::flash('message', trans('topic.fail_send_email'));
        }
    }


    /**
     * @param Request $request
     * @param $cbKey
     * @param $topicKey
     * @return $this|string
     */
    public function updateCooperationStatus(Request $request, $cbKey, $topicKey)
    {

        try {
            $coopToken = $request->input('coop_token');
            $decision = $request->input('decision');
            $type = $request->input('type');

            EMPATIA::updateCooperationStatus($coopToken, $decision);

            return redirect()->action('PublicTopicController@show', ['cbKey' => $cbKey, 'topicKey' => $topicKey, 'type' => $type]);
        } catch (Exception $e) {
            return redirect()->back()->withErrors([trans("publicTopic.cooperation_request_message") => $e->getMessage()]);
        }
    }


    public function makeReclamation(Request $request, $type, $cbKey, $topicKey)
    {
        $type = $this->cbType[$request->type];
        return view('public.' . ONE::getEntityLayout() . '.cbs.' . $type . '.cbs.registerMessage');
    }



    /**
     * RETURN VIEW THAT LOADS EVERY VUE COMPONENT FOR TOPIC VIEW
     * @param Request $request
     * @param $cbKey
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function display(Request $request, $cbKey, $topicKey)
    {   
        $type = isset($request['type']) ? $request['type'] == 'event' ? 'event' : 'default' : 'default';
        $currentLanguage = 'pt';
        $currentUser = Session::get('user');
        $defaultImage =  ONE::getSiteConfiguration("file_logo_first","/images/demo/LogoEmpatia-l-02.png");
        $location = "38.730863375629575, -9.131621718188399";

        LogsRequest::setAccess('topic_show', true, $topicKey, null, $cbKey, null, null, null, null, 'type: '.$type, Session::has('user') ? Session::get('user')->user_key : null);

        return view('public.' . ONE::getEntityLayout() . '.cb.'.$type.'.test_topic', compact('cbKey','currentLanguage','type','topicKey', 'currentUser', 'location', 'defaultImage'));
        // return view('public.test_topic',compact('cbKey','currentLanguage','type','topicKey'));
    }


    /**
     * FETCH THE TOPIC INFORMATION
     * @param $topicKey
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function basicInformation($topicKey)
    {
        try{
            $data = CB::displayTopic($topicKey);
            $data->facebook = Share::load(action('PublicTopicController@show', [$data->cb->cb_key, $topicKey, 'type' => $data->cb->template ?? 'default']), $data->title)->facebook();
            LogsRequest::setAccess('topic_show', true, $topicKey, null, $data->cb->cb_key, null, null, null, null, 'type: '.$data->cb->template ?? 'default', Session::has('user') ? Session::get('user')->user_key : null);

            return response()->json($data, 200);
        } catch (Exception $e) {
            $jsonObj = json_encode(array('error' => "Failure: ".$e->getMessage() ));
            LogsRequest::setAccess('topic_show', false, $topicKey, null, null, null, null, null, $jsonObj, null, Session::has('user') ? Session::get('user')->user_key : null);

            return response()->json(['errors' => $e->getMessage()], 500);
        } catch (\Throwable $t) {
            $jsonObj = json_encode(array('error' => "Failure: ".$t->getMessage() ));
            LogsRequest::setAccess('topic_show', false, $topicKey, null, null, null, null, null, $jsonObj, null, Session::has('user') ? Session::get('user')->user_key : null);

            return response()->json(['errors' => $t->getMessage()], 500);
        }
    }

    /**
     * FETCH CHILD TOPICS
     * @param $topicKey
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getChildTopics($topicKey)
    {
        try{
            $data = CB::getChildTopics($topicKey);
            return response()->json($data, 200);
        } catch (Exception $e) {
            return response()->json(['errors' => $e->getMessage()], 500);
        } catch (\Throwable $t) {
            return response()->json(['errors' => $t->getMessage()], 500);
        }
    }

}
