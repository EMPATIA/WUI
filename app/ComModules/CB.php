<?php

namespace App\ComModules;

use App\One\One;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;
use Datatables;
use Illuminate\Support\Facades\Redis;
use Session;
use View;
use Breadcrumbs;
use Exception;

class CB {
    public static function getAllCBsOptionTags(){
        return [
            'ALLOW-REPORT-ABUSE' => 'security_allow_report_abuses',
            'ALLOW-COMMENTS' => 'topic_comments_allow_comments',
            'CREATE-TOPIC' => 'security_create_topics',
            'CREATE-TOPICS-ANONYMOUS' => 'security_create_topics_anonymous',
            'ANONYMOUS-CREATE-TOPIC-ACCESS' => 'security_anonymous_create_topic_access',
            'PUBLIC-ACCESS' => 'security_public_access',
            'COMMENT-NEEDS-AUTHORIZATION' => 'security_comment_authorization',
            'COMMENTS-ANONYMOUS' => 'security_anonymous_comments',
            'HIDE-TOPIC-URLS' => 'hide_topic_url',
            'ALLOW-CO-OP' => 'topic_options_allow_co_op',
            'ALLOW-FILES' => 'topic_options_allow_files',
            'ALLOW-FOLLOW' => 'topic_options_allow_follow',
            'ALLOW-PICTURES' => 'topic_options_allow_pictures',
            'ALLOW-SHARE' => 'topic_options_allow_share',
            'ALLOW-USER-COUNT' => 'topic_options_allow_user_count',
            'ALLOW-VIDEO-LINK' => 'topic_options_allow_video_link',
            'ALLOW-NEWS' => 'topic_options_allow_news',
            'TOPIC-NEED-MODERATION' => 'topic_need_moderation',
            'TOPIC-COMMENTS-NORMAL' => 'topic_comments_normal',
            'DISABLE-COMMENTS-FUNCTIONALITY' => 'disable_comments_functionality',
            'TOPIC-COMMENTS-POSITIVE-NEGATIVE' => 'topic_comments_positive_negative',
            'TOPIC-ALLOW-EVENT-ASSOCIATION' => 'topic_options_allow_event_association',
            'TOPIC-COMMENTS-ALL' => 'topic_comments_positive_neutral_negative',
            'ONLY-ONE-TOPIC' => 'only_one_topic',
            'TOPIC-AS-PRIV-QUESTIONNAIRE' => 'topic_as_private_questionnaire',
            'TOPIC-AS-PUBLIC-QUESTIONNAIRE' => 'topic_as_public_questionnaire',
            'ALLOW-ALLIANCE' => 'allow_alliance',
            'SHOW-STATUS' => 'show_status',
            'ALLOW-FILTER-STATUS' => 'allow_filter_status',

            'TAB-ORDER-RANDOM'  => 'tab_random',
            'TAB-ORDER-RECENT'  => 'tab_recent',
            'TAB-ORDER-POPULAR'  => 'tab_popular',
            'TAB-ORDER-COMMENTS'  => 'tab_comments',
            'FOOTER-VOTE-STATISTICS' => 'footer_vote_statistics',

            'TOPIC-PUBLISH-NEEDED' => 'publish_needed',
            'TOPICS-CAN-BE-PUBLISHED' => 'topics_can_be_published',
            'ALLOW-MIGRATION-TO-PROPOSAL' => 'topic_allow_migration_to_proposal',

            'BASIC-REGISTRATION-CAN-CREATE-TOPICS' => 'basic_registration_can_create_topics'
        ];
    }

    public static function getAllSecurityTags(){
        return [
            'ALLOW-REPORT-ABUSE' => 'security_allow_report_abuses',
            'ALLOW-COMMENTS' => 'security_allow_comments',
            'CREATE-TOPIC' => 'security_create_topics',
            'CREATE-TOPICS-ANONYMOUS' => 'security_create_topics_anonymous',
            'ANONYMOUS-CREATE-TOPIC-ACCESS' => 'security_anonymous_create_topic_access',
            'PUBLIC-ACCESS' => 'security_public_access',
            'COMMENT-NEEDS-AUTHORIZATION' => 'security_comment_authorization',
            'COMMENTS-ANONYMOUS' => 'security_anonymous_comments',

        ];
    }

    public static function getAllTopicOptionsTags(){
        return [
            'ALLOW-CO-OP' => 'topic_options_allow_co_op',
            'ALLOW-FILES' => 'topic_options_allow_files',
            'ALLOW-FOLLOW' => 'topic_options_allow_follow',
            'ALLOW-PICTURES' => 'topic_options_allow_pictures',
            'ALLOW-SHARE' => 'topic_options_allow_share',
            'ALLOW-USER-COUNT' => 'topic_options_allow_user_count',
            'ALLOW-VIDEO-LINK' => 'topic_options_allow_video_link',
            'TOPIC-NEED-MODERATION' => 'topic_need_moderation',
            'ALLOW-ALLIANCE' => 'allow_alliance',

            'TAB-ORDER-RANDOM'  => 'tab_random',
            'TAB-ORDER-RECENT'  => 'tab_recent',
            'TAB-ORDER-POPULAR'  => 'tab_popular',
            'TAB-ORDER-COMMENTS'  => 'tab_comments',

            'TOPICS-CAN-BE-PUBLISHED' => 'topics_can_be_published'
        ];
    }

    public static function checkCBsOption($cbOptions, $optionTAG){
        $allOptions = CB::getAllCBsOptionTags();
        if(isset($allOptions[$optionTAG])){
            $tag = $allOptions[$optionTAG];
            if (in_array($tag, $cbOptions,true)){
                return true;
            }
        }

        return false;
    }

    /*
     * Verify if a specific cb template exists
     */
    public static function verifyTemplate($cbKey, $configCode){
        $response = ONE::get([
            'component' => 'empatia',
            'api' => 'cb',
            'method' => 'verifyTemplate',
            'params' => [
                'cbKey' => $cbKey,
                'configCode' => $configCode
            ]
        ]);

        if($response->statusCode()!= 200){
            throw new Exception(trans("comModulesCB.errorVerifyTemplate"));
        }
        return $response->json()->data;
    }

    /*
     * Save a new cb template
     */
    public static function setCbTemplate($configCode, $cbKey, $emailTemplateKey){
        $response = ONE::post([
            'component' => 'empatia',
            'api' => 'cb',
            'method' => 'cbTemplate',
            'params' => [
                'cbKey' => $cbKey,
                'configCode' => $configCode,
                'emailTemplateKey' => $emailTemplateKey
            ]
        ]);

        if($response->statusCode()!= 200){
            throw new Exception(trans("comModulesCB.errorRetrievingCbTemplate"));
        }
        return $response->json();
    }

    public static function getCbTemplates($cbKey){
        $response = ONE::get([
            'component' => 'empatia',
            'api' => 'cb',
            'method' => 'getCbTemplates',
            'params' => [
                'cbKey' => $cbKey
            ]
        ]);

        if($response->statusCode()!= 200){
            throw new Exception(trans("comModulesCB.errorRetrievingCbTemplate"));
        }
        return $response->json();
    }

    public static function getCbVotes($cbKey){
        $response = ONE::get([
            'component' => 'empatia',
            'api' => 'cb',
            'method' => 'votes',
            'api_attribute' => $cbKey
        ]);
        if($response->statusCode()!= 200){
            throw new Exception(trans("comModulesCB.errorRetrievingVotes"));
        }
        return $response->json()->data;
    }

    public static function getListCbVotes($cbKey) {

        $response = ONE::get([
            'component' => 'empatia',
            'api' => 'cb',
            'method' => 'votes',
            'api_attribute' => $cbKey
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failedToGetListOfCbVotes"));
        }
        return $response->json()->data;
    }

    public static function getCbVote($cbKey,$voteKey) {

        $response = ONE::get([
            'component' => 'empatia',
            'api' => 'cb',
            'method' => 'votes',
            'api_attribute' => $cbKey,
            'attribute' => $voteKey
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failedToGetCbVote"));
        }
        return $response->json();
    }

    public static function getCbById($cbId) {

        $response = ONE::get([
            'component' => 'empatia',
            'api' => 'cb',
            'method' => 'getCbById',
            'api_attribute' => $cbId
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failedToGetCb"));
        }
        return $response->json();
    }

    public static function getCbByKey($cbKey) {
        $response = ONE::get([
            'component' => 'empatia',
            'api' => 'cb',
            'api_attribute' => $cbKey,
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failedToGetCbByKey"));
        }

        return $response->json();
    }

    public static function getListCBs($list) {

        $listCb = [];
        foreach ($list as $item) {
            $listCb[] = $item->cb_key ?? $item;
        }

        $response = ONE::post([
            'component' => 'empatia',
            'api' => 'cb',
            'method' => 'listCBs',
            'params' => [
                'cbList' => $listCb
            ]
        ]);

// !is_null($response->json()) ? dd('remote/DD',$response->json()) : die('remote/ECHO' .$response->content());
        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failedToGetListOfCbs"));
        }
        return $response->json()->data;
    }

    public static function addTopicNews($topicKey,$news_key) {

        $response = ONE::post([
            'component' => 'empatia',
            'api' => 'topic',
            'api_attribute' => $topicKey,
            'method' => 'addTopicNews',
            'params' => [
                'news_key' => $news_key
            ]
        ]);

        if($response->statusCode() != 201){
            throw new Exception(trans("comModulesCB.failedToAddTopicNews"));
        }
        return $response->json()->data;
    }

    public static function getTopicNews($topicKey) {

        $response = ONE::get([
            'component' => 'empatia',
            'api' => 'topic',
            'api_attribute' => $topicKey,
            'method' => 'getTopicNews'
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failedToGetTopicNews"));
        }
        return $response->json();
    }


    /**
     * Retrieves a list of CBs withs Statistics for given Cbs Array of Keys
     * Possible to filter by open and closed CBs - just pass $cbsStatus
     *
     * Statistics: Total Cbs Topics comments;
     *              open/closed CBs
     *              and an array of vote events keys
     *              for likes count
     *
     * @param $list
     * @param null $cbsStatus
     * @return mixed
     * @throws Exception
     */
    public static function getListCbsWithStats($list, $cbsStatus = null) {

        $listCb = [];
        foreach ($list as $item) {
            $listCb[] = $item->cb_key;
        }

        $response = ONE::post([
            'component' => 'empatia',
            'api' => 'cb',
            'method' => 'listCbsWithStatistics',
            'params' => [
                'cbList' => $listCb,
                'cbsStatus' => $cbsStatus
            ]
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failedToGetListOfCbsWithStats"));
        }
        return $response->json()->data;
    }

    public static function getCBsByKeys($keys) {


        $response = ONE::post([
            'component' => 'empatia',
            'api' => 'cb',
            'method' => 'listCBs',
            'params' => [
                'cbList' => $keys
            ]
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failedToGetListOfCbs"));
        }
        return $response->json()->data;
    }

    public static function getCb($cbKey) {
        $response = ONE::get([
            'component' => 'empatia',
            'api' => 'cb',
            'attribute' => $cbKey
        ]);
        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failed_to_get_cb"));
        }
        return $response->json();
    }

    public static function topicsWithLastPost($request, $cbKey, $showWithFlags = false){
        $parameters = $request->parameters ?? null;
        $filters_static = $request->filters_static ?? null;

        $response = ONE::get([
            'component'     => 'empatia',
            'api'           => 'cb',
            'api_attribute' => $cbKey,
            'method'        => 'topicsWithLastPost',
            'params'        => [
                'parameters'        => $parameters,
                'withFlags'      => $showWithFlags,
                'filters_static'    => $filters_static,
                'tableData'         => One::tableData($request),
            ]
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failedToGetListOfTopic"));
        }
        return $response->json()->data;
    }

    public static function topicsWithLastPostTableData($cbKey,$tableData=[]){
        if (empty($tableData)){
            $tableData['order']['value'] = 'id';
            $tableData['order']['dir'] = 'asc';
            $tableData['start'] = 0;
            $tableData['length'] = 1000;
        }

        $response = ONE::get([
            'component' => 'empatia',
            'api' => 'cb',
            'api_attribute' => $cbKey,
            'method' => 'topicsWithLastPost',
            'params' => [
                'tableData' => $tableData
            ]
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failedToGetListOfTopic"));
        }

        return $response->json()->data;
    }

    public static function getAllTopics($listCbs){

        $response = ONE::post([
            'component' => 'empatia',
            'api' => 'topic',
            'method' => 'topicsWithModeration',
            'params' => [
                'data' => $listCbs
            ]
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failedToGetListOfTopic"));
        }
        return $response->json()->data;
    }
    public static function getAllTopicsWithTecnicalEvaluation($listCbs){

        $response = ONE::post([
            'component' => 'empatia',
            'api' => 'topic',
            'method' => 'topicsWithTechnicalEvaluation',
            'params' => [
                'data' => $listCbs
            ]
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failedToGetListOfTopic"));
        }
        return $response->json()->data;
    }

    /**
     * @return mixed
     * @throws Exception
     */
    public static function getConfigurations(){
        $response = ONE::get([
            'component' => 'empatia',
            'api' => 'configuration',
            'method' => 'getConfigurationOptions'
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failedToGetConfigurations"));
        }
        return $response->json()->data;
    }

    public static function getParametersTypes(){
        $response = ONE::get([
            'component' => 'empatia',
            'api' => 'parameterTypes',
            'method' => 'list',
        ]);
        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failed_to_get_parameters_types"));
        }
        return $response->json()->data;
    }

    public static function getParameterType($paramTypeId){
        $response = ONE::get([
            'component' => 'empatia',
            'api' => 'parameterTypes',
            'api_attribute' => $paramTypeId
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failedToGetParameterType"));
        }
        return $response->json();
    }


    public static function getParameters(){
        $response = ONE::get([
            'component' => 'empatia',
            'api' => 'parameters',
            'method' => 'list',
        ]);
        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failedToGetCbParameters"));
        }
        return $response->json()->data;
    }

    public static function getParameter($param){
        $response = ONE::get([
            'component' => 'empatia',
            'api' => 'parameters',
            'api_attribute' => $param
        ]);
        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failedToGetParameter"));
        }
        return $response->json();
    }

    public static function getCbParameters($cbKey){
        $response = ONE::get([
            'component' => 'empatia',
            'api' => 'cb',
            'method' => 'parameters',
            'api_attribute' => $cbKey
        ]);
        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failedToGetCbParameters"));
        }
        return $response->json()->parameters;
    }

    public static function getParameterOptions($param){
        $response = ONE::get([
            'component' => 'empatia',
            'api' => 'parameters',
            'api_attribute' => $param,
            'method' => 'options'
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failedToGetCbParametersWithOptions"));
        }
        return $response->json();
    }


    public static function getParameterOptionsEdit($param){
        $response = ONE::get([
            'component' => 'empatia',
            'api' => 'parameters',
            'api_attribute' => $param,
            'method' => 'edit'
        ]);
        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failedToGetCbParametersWithOptionsWithTranslations"));
        }

        return $response->json();
    }

    public static function getFieldTypes()
    {
        $response = ONE::get([
            'component' => 'empatia',
            'api' => 'fieldTypes',
            'method' => 'list'
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failedToGetCbParametersWithOptionsWithTranslations"));
        }

        return $response->json()->data;
    }


    public static function getTopicStatistics($topicKey){
        $response = ONE::get([
            'component' => 'empatia',
            'api' => 'topic',
            'method' => 'statistics',
            'api_attribute' => $topicKey
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failedTopicStatistics"));
        }
        return $response->json();
    }

    public static function getCBAndTopics($cbKey, $filterList = []){
        $response = ONE::get([
            'component' => 'empatia',
            'api' => 'cb',
            'api_attribute' => $cbKey,
            'method' => 'getAllInformation',
            'params' => [
                'filter_list' => $filterList
            ]
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failedToGetAllTopics"));
        }
        return $response->json();
    }

    public static function getTopics($cbKey){
        $response = ONE::get([
            'component' => 'empatia',
            'api' => 'cb',
            'api_attribute' => $cbKey,
            'method' => 'getAllTopics',
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failedToGetTopics"));
        }
        return $response->json();
    }

    public static function getTopicsList($cbKey){
        $response = ONE::get([
            'component' => 'empatia',
            'api' => 'cb',
            'api_attribute' => $cbKey,
            'method' => 'getTopicsList',
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failedToGetTopics"));
        }
        return $response->json();
    }

    public static function getCBAndTopicsWithPagination($cbKey, $pageToken = null, $numberOfTopicsToShow = 6, $filterList = []){
        $response = ONE::post([
            'component' => 'empatia',
            'api' => 'cb',
            'api_attribute' => $cbKey,
            'method' => 'getWithPagination',
            'params' => [
                'pageToken' => $pageToken,
                'filter_list' => $filterList,
                'numberOfTopicsToShow' => $numberOfTopicsToShow
            ]
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failedToGetAllTopicsWithPagination"));
        }
        return $response->json();
    }

    public static function getTopicParameters($topicKey, $topicVersion = "", $publicCall = false){

        if($topicVersion == ""){
            $params =  [
                'publicCall' => $publicCall,
            ];
        } else{
            $params =  [
                'topicVersion' => $topicVersion,
                'publicCall' => $publicCall,
            ];
        }

        $response = ONE::get([
            'component' => 'empatia',
            'api' => 'topic',
            'method' => 'parameters',
            'api_attribute' => $topicKey,
            'params' => $params
        ]);

        if($response->statusCode() != 200){
            if($response->statusCode() == 401){
                throw new Exception(trans('privateCbs.permission_message'));
            }
            throw new Exception(trans("comModulesCB.failedToGetTopicParameters"));
        }

        return $response->json();
    }
    public static function getTopicStatus($topicKey){
        $response = ONE::get([
            'component' => 'empatia',
            'api' => 'topic',
            'method' => 'getTopicStatus',
            'api_attribute' => $topicKey
        ]);
        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failedToGetTopicStatus"));
        }
        return $response->json();
    }

    public static function getTopicDataWithChilds($topicKey, $orderBy = 'ASC'){
        $response = ONE::get([
            'component'     => 'empatia',
            'api'           => 'topic',
            'api_attribute' => $topicKey,
            'method'        => 'dataWithChilds',
            'params'        => [
                'orderBy' => $orderBy
            ]
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failedToGetTopicDataWithChildParameters"));
        }
        return $response->json();
    }
    public static function getTopicDataWithChildsForModal($topicKey, $orderBy = 'ASC'){

        $response = ONE::get([
            'component'     => 'empatia',
            'api'           => 'topic',
            'api_attribute' => $topicKey,
            'method'        => 'privateDataWithChildsForModal',
            'params'        => [
                'orderBy' => $orderBy
            ]
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failedToGetTopicDataWithChildParametersForModal"));
        }
        return $response->json();
    }

    public static function getTopicPrivateDataWithChilds($topicKey, $orderBy = 'ASC'){
        $response = ONE::get([
            'component' => 'empatia',
            'api' => 'topic',
            'api_attribute' => $topicKey,
            'method' => 'privateDataWithChilds',
            'params' => [
                'orderBy' => $orderBy
            ]

        ]);
        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failedToGetTopicDataWithChildParameters"));
        }
        return $response->json();
    }

    public static function getPostManagerList($cbKeys,$showWithAbuses = 0, $showCommentsNeedsAuth = 0, $showWithFlags = false, $request = []){

        $response = ONE::post([
            'component' => 'empatia',
            'api' => 'post',
            'method' => 'postManagerList',
            'params' => [
                'cbKeys' => $cbKeys,
                'showWithAbuses' => $showWithAbuses,
                'showCommentsNeedsAuth' => $showCommentsNeedsAuth,
                'showWithFlags' => $showWithFlags,
                'tableData' => One::tableData($request),
            ]
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failedToGetAllPosts"));
        }
        return $response->json();
    }

    public static function getPostManagerListLastOnes($cbKeys,$showWithAbuses = 0, $showCommentsNeedsAuth = 0){
        $response = ONE::post([
            'component' => 'empatia',
            'api' => 'post',
            'method' => 'postManagerListLastly',
            'params' => [
                'cbKeys' => $cbKeys,
                'showWithAbuses' => $showWithAbuses,
                'showCommentsNeedsAuth' => $showCommentsNeedsAuth
            ]
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failedToGetAllPosts"));
        }
        return $response->json()->data;
    }

    public static function getPostsThatNeedsApproval($cbKeys){
        $response = ONE::post([
            'component' => 'empatia',
            'api' => 'post',
            'method' => 'listThatNeedsApproval',
            'params' => [
                'cb_keys' => $cbKeys
            ]
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failedToGetAllPosts"));
        }
        return $response->json()->data;
    }

    public static function getPosts($cbKeys){
        $response = ONE::post([
            'component' => 'empatia',
            'api' => 'post',
            'method' => 'listThatNeedsApproval',
            'params' => [
                'cb_keys' => $cbKeys
            ]
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failedToGetAllPosts"));
        }
        return $response->json()->data;
    }

    /**
     * @param $cbKey
     * @return mixed
     * @throws Exception
     */
    public static function getCbConfigurations($cbKey){
        $response = ONE::get([
            'component' => 'empatia',
            'api' => 'cb',
            'api_attribute' => $cbKey,
            'method'        => 'configurations'
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failedToGetCbConfigurations"));
        }
        return $response->json();
    }

    public static function getAnonymousEmail($topicKey){
        $response = ONE::get([
            'component' => 'empatia',
            'api' => 'topic',
            'api_attribute' => $topicKey,
            'method' => 'getTopicUserEmail'
        ]);
        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failedToGetAnonymousEmail"));
        }
        return $response->json();
    }

    public static function getCbModerators($cbKey){
        $response = ONE::get([
            'component' => 'empatia',
            'api' => 'cb',
            'api_attribute' => $cbKey,
            'method' => 'moderators'

        ]);
        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failedToGetCbModerators"));
        }
        return $response->json()->data;
    }

    public static function getCbChildren($cbKey){
        $response = ONE::get([
            'component' => 'empatia',
            'api' => 'cb',
            'api_attribute' => $cbKey,
            'method' => 'getCbChildren'

        ]);
        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failedToGetCbChildren"));
        }
        return $response->json();
    }

    public static function getCbParametersOptions($cbKey, $privateTopicsList = false){
        $response = ONE::get([
            'component' => 'empatia',
            'api' => 'cb',
            'method' => 'options',
            'api_attribute' => $cbKey,
            'params' => [
                'privateTopicsList' => $privateTopicsList
            ]
        ]);
        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failedToGet_parameter_with_options"));
        }
        return $response->json();
    }

    public static function getUserPosts()
    {
        $response = ONE::get([
            'component' => 'empatia',
            'api' => 'post',
            'method' => 'postTimeline'
        ]);
        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failedToGetUserPosts"));
        }
        return $response->json()->data;
    }


    /*  SET's  */

    public static function setCbVote($request, $newVoteEvent) {

        $cbKey = $request->cbKey;
        $methodSelect = $request->methodSelect;
        $name = $request->name;

        //verify generic configs
        $genericConfigs = [];

        foreach ($request->all() as $key => $value) {
            if (strpos($key, 'genericConfig_') !== false) {
                $key = str_replace("genericConfig_", "", $key);
                if ($value != '')
                    $genericConfigs[] = array('vote_configuration_key' => $key, 'value' => $value);
            }
        }

        $response = ONE::post([
            'component' => 'empatia',
            'api' => 'cb',
            'method' => 'votes',
            'api_attribute' => $cbKey,
            'params' => [
                'vote_key' => $newVoteEvent->key,
                'vote_method' => $newVoteEvent->method->code,
                'name' => $name,
                'configurations' => $genericConfigs
            ]
        ]);

        if($response->statusCode() != 201){
            throw new Exception(trans("comModulesCB.failedToSetNewVoteInstanceToCb"));
        }
        return $response;
    }

    public static function setCbVoteWithData($data, $newVoteEvent) {

        $cbKey = $data["cbKey"];
        $methodSelect = $data["methodSelect"];
        $name = $data["name"];
        $genericConfigs = $data["genericConfigs"];

        $response = ONE::post([
            'component' => 'empatia',
            'api' => 'cb',
            'method' => 'votes',
            'api_attribute' => $cbKey,
            'params' => [
                'vote_key' => $newVoteEvent->key,
                'vote_method' => $methodSelect,
                'name' => $name,
                'configurations' => $genericConfigs
            ]
        ]);

        if($response->statusCode() != 201){
            throw new Exception(trans("comModulesCB.failedToSetNewVoteInstanceToCb"));
        }
        return $response;
    }

    public static function setNewCb($requestCB){
        $response = ONE::post([
            'component' => 'empatia',
            'api' => 'cb',
            'params' => [
                'title' => $requestCB["title"],
                'contents' => $requestCB["description"],
                'start_date' => $requestCB["start_date"],
                'end_date' => $requestCB["end_date"],
                'start_topic' => $requestCB["start_topic"],
                'end_topic' => $requestCB["end_topic"],
                'start_topic_edit' => $requestCB["start_topic_edit"],
                'end_topic_edit' => $requestCB["end_topic_edit"],
                'start_submit_proposal' => $requestCB["start_submit_proposal"],
                'end_submit_proposal' => $requestCB["end_submit_proposal"],
                'start_technical_analysis' => $requestCB["start_technical_analysis"],
                'end_technical_analysis' => $requestCB["end_technical_analysis"],
                'start_complaint' => $requestCB["start_complaint"],
                'end_complaint' => $requestCB["end_complaint"],
                'start_show_results' => $requestCB["start_show_results"],
                'end_show_results' => $requestCB["end_show_results"],
                'start_vote' => $requestCB["start_vote"],
                'end_vote' => $requestCB["end_vote"],
                'template' => $requestCB["template"],
                'filters' => $requestCB["filters"],
                'page_key' => $requestCB["page_key"]
            ]
        ]);

        if($response->statusCode() != 201){
            throw new Exception(trans("comModulesCB.failedToSetNewCbInCbs"));
        }
        return $response->json();
    }

    public static function setStepperNewCb($data){
        $response = ONE::post([
            'component' => 'empatia',
            'api' => 'cb',
            'method' => 'create',
            'params' => $data
        ]);

        if($response->statusCode() != 201){
            throw new Exception(trans("comModulesCB.failedToSetNewCbInCbsStepper"));
        }
        return $response->json();
    }

    public static function createCbChild($data){
        $response = ONE::post([
            'component' => 'empatia',
            'api' => 'cb',
            //  'method' => 'create',
            'params' => $data
        ]);


        if($response->statusCode() != 201){
            throw new Exception(trans("comModulesCB.failedToCreateCbChild"));
        }
        return $response->json();
    }

    /**
     * @param $cbKey
     * @param $configurations
     * @param null $configGroups
     * @param null $deadline
     * @param int $flag
     * @throws Exception
     */
    public static function setCbConfigurations($cbKey, $configurations, $configGroups = null, $deadline = null, $flag = -1)
    {
        $response = ONE::post([
            'component'     => 'empatia',
            'api'           => 'cb',
            'api_attribute' => $cbKey,
            'method'        => 'configurations',
            'params'        => [
                'configurations'    => $configurations,
                'configGroups'      => $configGroups,
                'deadline'          => $deadline,
                'type'              => $flag
            ]
        ]);

        if($response->statusCode() != 201){
            throw new Exception(trans("comModulesCB.failedToSetNewCbConfigurationsInCbs"));
        }
    }

    /**
     * @param $cbKey
     * @param $params
     * @throws Exception
     */
    public static function setCbParameters($cbKey, $params){
        $response = ONE::post([
            'component' => 'empatia',
            'api' => 'cb',
            'method' => 'parameters',
            'api_attribute' => $cbKey,
            'params' => [
                'parameters' => $params]
        ]);
        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failedToSetCbParameter"));
        }
    }

    public static function setCbParamOptions($cbKey,$options){
        $response = ONE::post([
            'component' => 'empatia',
            'api' => 'cb',
            'method' => 'options',
            'api_attribute' => $cbKey,
            'params' => [
                'options' => $options
            ]
        ]);
        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failedToSetCbParameterOptions"));
        }
    }


    public static function setParametersWithNewOptions($param,$options){
        $response = ONE::post([
            'component' => 'empatia',
            'api' => 'parameters',
            'method' => 'optionsMulti',
            'api_attribute' => $param,
            'params' => [
                'options' => $options
            ]
        ]);
        if($response->statusCode() != 201){
            throw new Exception(trans("comModulesCB.failedToSetNewOptions"));
        }
        return $response->json()->data;
    }

    public static function setParameters($data){
        $response = ONE::post([
            'component' => 'empatia',
            'api'       => 'parameters',
            'params'    => $data
        ]);
        if($response->statusCode() != 201){
            throw new Exception(trans("comModulesCB.failedToSetNewParameter"));
        }
        return $response->json();
    }

    /**
     * @param request
     * @return mixed
     * @throws Exception
     */
    public static function setParameterTypeWithField($name, $code, $translations, $types)
    {
        $response = ONE::post([
            'component' => 'empatia',
            'api'       => 'parameterTypes',
            'params'    => [
                "name" => $name,
                "code" => $code,
                "translations" => $translations,
                "types" => $types
            ]
        ]);
        if($response->statusCode()!= 201) {
            throw new Exception(trans("comModulesOrchestrator.errorAddingParameterTypeWithFields"));
        }
        return $response->json();

    }

    public static function setParameterType($name, $code, $options, $translations, $request)
    {
        $response = ONE::post([
            'component' => 'empatia',
            'api'       => 'parameterTypes',
            'params'    => [
                "name" => $name,
                "code" => $code,
                "options" => $options,
                "translations" => $translations,
                "types" => $request
            ]
        ]);

        if($response->statusCode()!= 201) {
            throw new Exception(trans("comModulesOrchestrator.errorAddingParameterType"));
        }
        return $response->json();

    }

    public static function setCbModerators($cbKey,$moderators){
        $response = ONE::post([
            'component' => 'empatia',
            'api' => 'cb',
            'api_attribute' => $cbKey,
            'method' => 'moderators',
            'params' => [
                'moderators' => $moderators
            ]
        ]);
        if($response->statusCode() != 201){
            throw new Exception(trans("comModulesCB.failedToSetNewCbModerators"));
        }
        return $response->json();
    }

    public static function setTopicWithParameters($cbKey, $requestTopic, $parametersToSend, $isPrivate = false){
        $response = ONE::post([
            'component' => 'empatia',
            'api' => 'topic',
            'params' => [
                'private' => $isPrivate ?? false,
                'title' => $requestTopic["title"],
                'contents' => $requestTopic["contents"],
                'summary' => $requestTopic["summary"],
                'created_on_behalf' => $requestTopic["created_on_behalf"],
                'start_date' => array_key_exists("start_date",$requestTopic) ? $requestTopic["start_date"] : '',
                'end_date' =>  array_key_exists("end_date",$requestTopic) ? $requestTopic["end_date"] : '',
                'parent_topic_key' => $requestTopic["parent_topic_key"] ? $requestTopic["parent_topic_key"] : '',
                'topic_creator' => $requestTopic["topic_creator"] ? $requestTopic["topic_creator"] : '',
                'cb_key' => $cbKey,
                'parameters' => $parametersToSend
            ]
        ]);
        if ($response->statusCode() != 201) {
            throw new Exception(trans("comModulesCB.failedToSetNewCbTopic"));
        }
        return $response->json()->topic;
    }

    public static function setAnnotation($data){

        $response = ONE::post([
            'component' => 'empatia',
            'api' => 'annotation',
            'params' => $data

        ]);

        if ($response->statusCode() != 201) {
            throw new Exception(trans("comModulesCB.failedToSetNewAnnotation"));
        }
        return $response->json();
    }

    public static function setFilesForTopic($post_key, $file){
        $response = ONE::post([
            'component' => 'empatia',
            'api' => 'post',
            'api_attribute' => $post_key,
            'method' => 'files',
            'params' => [
                'file_id' => $file->file_id,
                'name' => $file->name,
                'type_id' => $file->type_id,
                'description' => !empty($file->description) ? $file->description : 'description',
                'file_code' => $file->file_code
            ]
        ]);

        if($response->statusCode()!= 201){
            throw new Exception(trans("comModulesCB.failedToSetFilesForTopic"));
        }

        return $response;
    }

    public static function setFilesArrayForTopic($post_key, $files){
        $response = ONE::post([
            'component' => 'empatia',
            'api' => 'post',
            'api_attribute' => $post_key,
            'method' => 'addFiles',
            'params' => [
                "files" => $files
            ]
        ]);

        if($response->statusCode()!= 201){
            throw new Exception(trans("comModulesCB.failedToSetFilesForTopic"));
        }

        return $response;
    }


    public static function updateFilesArrayForTopic($post_key, $files){
        $response = ONE::put([
            'component' => 'empatia',
            'api' => 'post',
            'api_attribute' => $post_key,
            'method' => 'updateFiles',
            'params' => [
                "files" => $files
            ]
        ]);

        if($response->statusCode()!= 201){
            throw new Exception(trans("comModulesCB.failedToSetFilesForTopic"));
        }

        return $response;
    }


    public static function getFilesOfTopics($topics){

        $response = ONE::post([
            'component' => 'empatia',
            'api' => 'post',
            'method' => 'getTopicsFiles',
            'params' => array(
                'topics' => $topics
            )
        ]);

        if($response->statusCode()!= 200){
            throw new Exception(trans("comModulesCB.failedToGetFilesOfTopics"));
        }

        return $response->json()->data;
    }
    /*  UPDATE's  */

    /**
     * @param $cbKey
     * @param $requestCB
     * @return mixed
     * @throws Exception
     */
    public static function updateCB($cbKey, $requestCB){
        $response = ONE::put([
            'component' => 'empatia',
            'api' => 'cb',
            'params' => [
                'title' => $requestCB["title"],
                'contents' => $requestCB["description"],
                'start_date' => $requestCB["start_date"],
                'end_date' => $requestCB["end_date"],
                'start_topic' => $requestCB["start_topic"],
                'end_topic' => $requestCB["end_topic"],
                'start_topic_edit' => $requestCB["start_topic_edit"],
                'end_topic_edit' => $requestCB["end_topic_edit"],
                'start_submit_proposal' => $requestCB["start_submit_proposal"],
                'end_submit_proposal' => $requestCB["end_submit_proposal"],
                'start_technical_analysis' => $requestCB["start_technical_analysis"],
                'end_technical_analysis' => $requestCB["end_technical_analysis"],
                'start_complaint' => $requestCB["start_complaint"],
                'end_complaint' => $requestCB["end_complaint"],
                'start_show_results' => $requestCB["start_show_results"],
                'end_show_results' => $requestCB["end_show_results"],
                'start_vote' => $requestCB["start_vote"],
                'end_vote' => $requestCB["end_vote"],
                'filters' => $requestCB["filters"],
                'tag' =>  (isset($requestCB["tag"]))?$requestCB["tag"]:'',
                'template' => $requestCB["template"],
                'page_key' => $requestCB["page_key"],
                'parent_cb_id' => $requestCB["parent_cb_id"]
            ],
            'attribute' => $cbKey
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failedToUpdateCbInCbs"));
        }
        return $response->json();
    }

    /**
     * @param $topicKey
     * @param $requestTopic
     * @param $parametersToSend
     * @param bool $isPrivate
     * @return mixed
     * @throws Exception
     */
    public static function updateTopicWithParameters($topicKey, $requestTopic, $parametersToSend, $isPrivate = false){

        $siteComplete = Orchestrator::getSite(Session::get('X-SITE-KEY'));

        $site = [];
        $site['name'] = $siteComplete->name;
        $site['no_reply_email'] = $siteComplete->no_reply_email;
        $response = ONE::put([
            'component' => 'empatia',
            'api'       => 'topic',
            'api_attribute' => $topicKey,
            'params'    => [
                'private'           => $isPrivate ?? false,
                'title'             => $requestTopic["title"],
                'contents'          => $requestTopic["contents"] ?? " ",
                'summary'           => $requestTopic["summary"],
                'status'            => $requestTopic["status"] ?? null,
                'created_on_behalf' => $requestTopic["created_on_behalf"] ?? null,
                'parameters'        => $parametersToSend,
                'start_date'        => !empty($requestTopic["start_date"]) ? $requestTopic["start_date"] : null,
                'end_date'          => !empty($requestTopic["end_date"]) ? $requestTopic["end_date"] : null,
                'site'              => $site,
                'topic_key'         => $topicKey,
                'link'              => $requestTopic['link'] ?? null,
                'cb_key'             => $requestTopic['cbKey'] ?? null
            ],

        ]);
        if ($response->statusCode() != 200) {
            throw new Exception(trans("comModulesCB.failedToUpdateTopic"));
        }
        return $response->json()->topic;
    }


    /**
     * @param $paramId
     * @param $data
     * @return mixed
     * @throws Exception
     */
    public static function updateParameter($paramId, $data)
    {
        $response = ONE::put([
            'component'     => 'empatia',
            'api'           => 'parameters',
            'api_attribute' => $paramId,
            'params' => $data
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failedToUpdateParameter"));
        }
        return $response->json();
    }


    public static function updateAnnotation($data, $annotationKey)
    {
        $response = ONE::put([
            'component' => 'empatia',
            'api' => 'annotation',
            'api_attribute' => $annotationKey,
            'params' => $data
        ]);
        return $response->json();

    }

    /*   DELETE's   */

    public static function deleteAnnotation($annotationKey)
    {
        $response = ONE::delete([
            'component' => 'empatia',
            'api' => 'annotation',
            'attribute' => $annotationKey

        ]);
        return $response->json();

    }

    public static function deleteCbVote($cbKey, $voteKey) {

        $response = ONE::delete([
            'component' => 'empatia',
            'api' => 'cb',
            'method' => 'votes',
            'api_attribute' => $cbKey,
            'attribute' => $voteKey
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failedToDeleteCbVote"));
        }
        return $response;
    }

    public static function deleteCb($cbKey){
        $response = ONE::delete([
            'component' => 'empatia',
            'api' => 'cb',
            'attribute' => $cbKey
        ]);
        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failedToDeleteCbInCbs"));
        }
    }

    public static function deleteTopicNews($topicKey,$news_key)
    {
        $response = ONE::delete([
            'component' => 'empatia',
            'api' => 'topic',
            'api_attribute' => $topicKey,
            'method' => 'deleteTopicNews',
            'attribute' => $news_key
        ]);

        return $response->json();

    }

    public static function deleteModerator($cbKey,$idModerator){
        $response = ONE::delete([
            'component' => 'empatia',
            'api' => 'cb',
            'api_attribute' => $cbKey,
            'method' => 'moderators',
            'attribute' => $idModerator,
        ]);
        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failedToDeleteModeratorInCb"));
        }
    }

    public static function deleteParameter($paramId){

        $response = ONE::delete([
            'component' => 'empatia',
            'api' => 'parameters',
            'api_attribute' => $paramId
        ]);
        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failedToDeleteParameterInCb"));
        }
    }

    public static function deleteParameterType($parameterTypeId){
        $response = ONE::delete([
            'component' => 'empatia',
            'api' => 'parameterTypes',
            'api_attribute' => $parameterTypeId
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failedToDeleteParameterInCb"));
        }
    }

    public static  function getCooperators($topicKey){
        $response = ONE::get([
            'component' => 'empatia',
            'api' => 'topic',
            'method' => 'cooperators',
            'params' => [
                'topic_key' => $topicKey
            ]
        ]);

        if ($response->statusCode() != 200) {
            throw new Exception(trans("comModulesCB.failedToGetCooperators"));
        }
        return $response->json();
    }

    /**
     * @param $request
     * @param $topicKey
     * @return mixed
     * @throws Exception
     */
    public static  function getCooperatorsList($request, $topicKey){
        $response = ONE::get([
            'component' => 'empatia',
            'api' => 'topic',
            'method' => 'cooperators',
            'attribute' => 'list',
            'params' => [
                'topic_key' => $topicKey,
                'tableData' => One::tableData($request ?? [])
            ]
        ]);

        if ($response->statusCode() != 200) {
            throw new Exception(trans("comModulesCB.failedToGetCooperators"));
        }
        return $response->json();
    }

    public static function getCooperatorPermissions($topicKey){

        $response = ONE::get([
            'component' => 'empatia',
            'api' => 'topic',
            'method' => 'permissions',
            'api_attribute' => $topicKey

        ]);

        if ($response->statusCode() != 200) {
            throw new Exception(trans("comModulesCB.failedToGetCooperatorPermissions"));
        }
        return $response->json()->data;
    }

    public static function getAnnotations($topicKey){

        $response = ONE::get([

            'component' => 'empatia',
            'api' => 'annotation',
            'api_attribute' => $topicKey

        ]);

        if ($response->statusCode() != 200) {
            throw new Exception(trans("comModulesCB.failedToGetAnnotations"));
        }
        return $response->json()->data;
    }

    public static function getAnnotationsTags($topicKey){
        $response = ONE::get([
            'component' => 'empatia',
            'api' => 'annotation',
            'method' => 'tags',
            'prams' => $topicKey

        ]);

        if ($response->statusCode() != 200) {
            throw new Exception(trans("comModulesCB.failedToGetAnnotations"));
        }
        return $response->json()->data;
    }

    public static function getCbConfigTypes()
    {
        $response = ONE::get([
            'component' => 'empatia',
            'api' => 'configurationType',
            'method' => 'list'
        ]);
        if($response->statusCode()!= 200){
            throw new Exception(trans("comModulesCB.errorRetrievingConfigTypes"));
        }
        return $response->json()->data;

    }

    public static function SetConfigType($code, $translation)
    {
        $response = ONE::post([
            'component' => 'empatia',
            'api' => 'configurationType',
            'params' => [
                'code' => $code,
                'translations' => $translation
            ]
        ]);
        if($response->statusCode() != 201){
            throw new Exception(trans("comModulesCB.failedToSetNewConfigType"));
        }
        return $response->json();

    }

    public static function getCbConfigType($id)
    {
        $response = ONE::get([
            'component' => 'empatia',
            'api' => 'configurationType',
            'api_attribute' => $id
        ]);
        if($response->statusCode()!= 200){
            throw new Exception(trans("comModulesCB.errorRetrievingConfigType"));
        }
        return $response->json();
    }

    public static function getCbConfigTypeEdit($id)
    {
        $response = ONE::get([
            'component' => 'empatia',
            'api' => 'configurationType',
            'method' => 'edit',
            'api_attribute' => $id
        ]);
        if($response->statusCode()!= 200){
            throw new Exception(trans("comModulesCB.errorRetrievingConfigTypeForEdit"));
        }
        return $response->json();

    }

    public static function UpdateConfigType($id,$code, $translation)
    {
        $response = ONE::put([
            'component' => 'empatia',
            'api' => 'configurationType',
            'api_attribute' => $id,
            'params' => [
                'code' => $code,
                'translations' => $translation
            ]
        ]);
        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failedToUpdateConfigType"));
        }
        return $response->json();

    }

    public static function deleteConfigType($id)
    {
        $response = ONE::delete([
            'component' => 'empatia',
            'api' => 'configurationType',
            'api_attribute' => $id
        ]);
        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failedToDeleteConfigType"));
        }
        return $response->json();
    }

    public static function getCbsConfigsByType($configTypeId)
    {
        $response = ONE::get([
            'component' => 'empatia',
            'api' => 'configurationType',
            'method' => 'showTypeConfigurations',
            'api_attribute' => $configTypeId
        ]);
        if($response->statusCode()!= 200){
            throw new Exception(trans("comModulesCB.errorRetrievingCbsConfigs"));
        }
        return $response->json()->configurations;
    }

    public static function SetCbsConfig($configTypeId, $code, $translation)
    {
        $response = ONE::post([
            'component' => 'empatia',
            'api' => 'configuration',
            'params' => [
                'configuration_type_id' => $configTypeId,
                'code' => $code,
                'translations' => $translation
            ]
        ]);
        if($response->statusCode() != 201){
            throw new Exception(trans("comModulesCB.failedToSetNewCbsConfig"));
        }
        return $response->json();
    }

    public static function getCbsConfig($id)
    {
        $response = ONE::get([
            'component' => 'empatia',
            'api' => 'configuration',
            'api_attribute' => $id
        ]);
        if($response->statusCode()!= 200){
            throw new Exception(trans("comModulesCB.errorRetrievingCbsConfig"));
        }
        return $response->json();

    }

    public static function getCbsConfigEdit($id)
    {
        $response = ONE::get([
            'component' => 'empatia',
            'api' => 'configuration',
            'method' => 'edit',
            'api_attribute' => $id
        ]);
        if($response->statusCode()!= 200){
            throw new Exception(trans("comModulesCB.errorRetrievingCbsConfigForEdit"));
        }
        return $response->json();
    }

    public static function UpdateCbsConfig($configTypeId, $id, $code, $translation)
    {
        $response = ONE::put([
            'component' => 'empatia',
            'api' => 'configuration',
            'api_attribute' => $id,
            'params' => [
                'configuration_type_id' => $configTypeId,
                'code' => $code,
                'translations' => $translation
            ]
        ]);
        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failedToUpdateCbsConfig"));
        }
        return $response->json();
    }

    public static function deleteCbsConfig($id)
    {
        $response = ONE::delete([
            'component' => 'empatia',
            'api' => 'configuration',
            'api_attribute' => $id
        ]);
        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failedToDeleteCbsConfig"));
        }
    }

    public static function getVotesConfigurations()
    {
        $response = ONE::get([
            'component'  => 'empatia',
            'api'        => 'voteConfigurations',
            'method'     => 'list'
        ]);
        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failedToGetVotesConfigurations"));
        }

        return $response->json()->data;
    }

    public static function setVoteConfiguration($code, $translation)
    {
        $response = ONE::post([
            'component' => 'empatia',
            'api'       => 'voteConfigurations',
            'params'    => [
                'code' => $code,
                'translations' => $translation
            ]
        ]);
        if($response->statusCode()!= 201)
        {
            throw new Exception(trans("comModulesCB.failedToSetNewVoteConfiguration"));
        }
        return $response->json();
    }

    public static function getVoteConfiguration($configKey)
    {
        $response = ONE::get([
            'component' => 'empatia',
            'api' => 'voteConfigurations',
            'api_attribute' => $configKey
        ]);
        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failedToGetVoteConfiguration"));
        }
        return $response->json();
    }

    public static function getVoteConfigurationEdit($configKey)
    {
        $response = ONE::get([
            'component' => 'empatia',
            'api' => 'voteConfigurations',
            'method' => 'edit',
            'api_attribute' => $configKey
        ]);
        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failedToGetVoteConfigurationWithTranslations"));
        }
        return $response->json();
    }

    public static function updateVoteConfiguration($configKey,$code, $translation)
    {
        $response = ONE::put([
            'component' => 'empatia',
            'api'       => 'voteConfigurations',
            'attribute' => $configKey,
            'params'    => [
                'code' => $code,
                'translations' => $translation
            ]
        ]);
        if($response->statusCode()!= 200)
        {
            throw new Exception(trans("comModulesCB.failedToUpdateVoteConfiguration"));
        }
        return $response->json();
    }

    public static function deleteVoteConfig($configKey)
    {
        $response = ONE::delete([
            'component' => 'empatia',
            'api'       => 'voteConfigurations',
            'attribute' => $configKey,
        ]);
        if($response->statusCode()!= 200)
        {
            throw new Exception(trans("comModulesCB.failedToDeleteVoteConfig"));
        }
    }

    public static function updateCbVote($request, $cbKey, $voteKey)
    {
        //verify generic configs
        $genericConfigs = [];

        foreach ($request->all() as $key => $value) {
            if (strpos($key, 'genericConfig_') !== false) {
                $key = str_replace("genericConfig_", "", $key);
                if ($value != '')
                    $genericConfigs[] = array('vote_configuration_key' => $key, 'value' => $value);
            }
        }

        $response = ONE::put([
            'component' => 'empatia',
            'api' => 'cb',
            'method' => 'votes',
            'api_attribute' => $cbKey,
            'attribute' => $voteKey,
            'params' => [
                'configurations' => $genericConfigs
            ]
        ]);
        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failedToUpdateCbVote"));
        }
        return $response->json();
    }

    public static function getStatusTypes()
    {
        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'statusTypes',
            'method'    => 'list'
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failedToGetStatusTypes"));
        }
        return $response->json()->data;
    }

    public static function updateTopicStatus($request)
    {
        $topicKey = $request->topicKey ?? $request['topicKey'];
        $status_type_code = $request->status_type_code ?? $request['status_type_code'];
        $siteComplete = Orchestrator::getSite(Session::get('X-SITE-KEY'));

        $site = [];
        $site['name'] = $siteComplete->name;
        $site['no_reply_email'] = $siteComplete->no_reply_email;

        $response = ONE::post([
            'component' => 'empatia',
            'api'       => 'status',
            'params'    => [
                'topic_key' => $topicKey,
                'code'      => $status_type_code,
                'site'      => $site,
                'comment'   => [
                    'content'   => isset($request->contentStatusComment) ? $request->contentStatusComment : '',
                    'public_content' =>  isset($request->contentStatusPublicComment) ? $request->contentStatusPublicComment :'',
                ]
            ]
        ]);

        if($response->statusCode() != 201){
            throw new Exception(trans("comModulesCB.failedToSetNewStatusInTopic"));
        }
        return $response->json();
    }

    public static function getStatusHistory($topicKey)
    {
        $response = ONE::get([
            'component' => 'empatia',
            'api' => 'status',
            'method' => 'history',
            'api_attribute' => $topicKey,
        ]);
        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failedToGetStatusHistory"));
        }
        return $response->json()->data;
    }

    public static function updateStepperCb($cbKey,$data)
    {
        $response = ONE::put([
            'component' => 'empatia',
            'api' => 'cb',
            'attribute' => $cbKey,
            'method' => 'update',
            'params' => $data
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failed_to_update_cb_in_cbs_stepper"));
        }
        return $response->json();
    }

    /**
     * Deletes a Topic
     *
     * @param $topicKey
     * @return mixed
     * @throws Exception
     */
    public static function deleteTopic($topicKey){

        $response = ONE::delete([
            'component' => 'empatia',
            'api' => 'topic',
            'attribute' => $topicKey
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failedToDeleteTopic"));
        }
        return $response;

    }


    /*------------------------------ Topic Reviews --------------------------------------*/

    /**
     *
     * Returns a list with Topic Reviews
     *
     * @param $topicKey
     * @param $isGroup
     * @return mixed
     * @throws Exception
     */
    public static function getTopicReviewsByType($topicKey, $isGroup){

        $response = ONE::get([
            'component' => 'empatia',
            'api' => 'topicReviews',
            'method' => 'listByType',
            'attribute' => $topicKey,
            'params' => [
                'is_group' => $isGroup
            ]

        ]);

        if ($response->statusCode() != 200) {
            throw new Exception(trans("comModulesCB.failedToGetTopicReviews"));
        }
        return $response->json()->data;
    }

    public static function getTopicReviews($topicKey){

        $response = ONE::get([
            'component' => 'empatia',
            'api' => 'topicReviews',
            'method' => 'list',
            'attribute' => $topicKey

        ]);

        if ($response->statusCode() != 200) {
            throw new Exception(trans("comModulesCB.failedToGetTopicReviews"));
        }
        return $response->json()->data;
    }

    /**
     *
     * gets a topic Review for given key
     *
     * @param $topicReviewKey
     * @return mixed
     * @throws Exception
     */
    public static function getTopicReview($topicReviewKey)
    {
        $response = ONE::get([
            'component' => 'empatia',
            'api' => 'topicReviews',
            'api_attribute' => $topicReviewKey
        ]);
        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failed_to_get_topic_review"));
        }
        return $response->json();
    }

    /**
     * sets a topic Review
     *
     * @param $request
     * @param $topicKey
     * @param $reviewers
     * @return mixed
     * @throws Exception
     */
    public static function setTopicReview($request, $topicKey, $reviewers) {

        $response = ONE::post([
            'component' => 'empatia',
            'api' => 'topicReviews',
            'params' => [
                'topic_key' => $topicKey,
                'code' => $request->code,
                'description' => $request->description,
                'subject' => $request->subject,
                'reviewers' => $reviewers
            ]

        ]);

        if ($response->statusCode() != 201) {
            throw new Exception(trans("comModulesCB.failedToSetTopicReview"));
        }
        return $response->json();

    }

    /**
     * Updates Topic Review
     *
     * @param $request
     * @param $topicKey
     * @param $topicReviewKey
     * @param $reviewers
     * @return mixed
     * @throws Exception
     */
    public static function updateTopicReview($request, $topicKey, $topicReviewKey, $reviewers){


        $response = ONE::put([
            'component' => 'empatia',
            'api' => 'topicReviews',
            'attribute' => $topicReviewKey,
            'params' => [
                'topic_key' => $topicKey,
                'code' => $request->code,
                'description' => $request->description,
                'subject' => $request->subject,
                'reviewers' => $reviewers
            ]
        ]);


        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failed_to_update_topic_review"));
        }
        return $response->json();

    }

    /**
     * Deletes a Topic Review
     *
     * @param $topicReviewKey
     * @return mixed
     * @throws Exception
     */
    public static function deleteTopicReview($topicReviewKey){

        $response = ONE::delete([
            'component' => 'empatia',
            'api' => 'topicReviews',
            'attribute' => $topicReviewKey
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failedToDeleteTopicReview"));
        }
        return $response;

    }

    /*------------------------------ //END Topic Reviews  --------------------------------------*/

    /*------------------------------ Topic Reviews Replies --------------------------------------*/

    /**
     *
     * Returns a list with Topic Review Replies
     *
     * @param $topicKey
     * @param $isGroup
     * @return mixed
     * @throws Exception
     */
    public static function getTopicReviewReplies($topicReviewKey){

        $response = ONE::get([
            'component' => 'empatia',
            'api' => 'topicReviewReplies',
            'method' => 'list',
            'attribute' => $topicReviewKey

        ]);

        if ($response->statusCode() != 200) {
            throw new Exception(trans("comModulesCB.failed_to_get_topic_review_replies"));
        }
        return $response->json();

    }

    /**
     * Gets a Topic Review Reply from given key
     *
     * @param $topicReviewReplyKey
     * @return mixed
     * @throws Exception
     */
    public static function getTopicReviewReply($topicReviewReplyKey){

        $response = ONE::get([
            'component' => 'empatia',
            'api' => 'topicReviewReplies',
            'api_attribute' => $topicReviewReplyKey
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failed_to_get_topic_review_reply"));
        }
        return $response->json();

    }

    /**
     * Sets a Topic Review Reply
     *
     * @param $request
     * @param $topicReviewKey
     * @return mixed
     * @throws Exception
     */
    public static function setTopicReviewReply($request, $topicReviewKey) {


        $response = ONE::post([
            'component' => 'empatia',
            'api' => 'topicReviewReplies',
            'params' => [
                'topic_review_key' => $topicReviewKey,
                'content' => $request->content,
                'code' => $request->status
            ]

        ]);

        if ($response->statusCode() != 201) {
            throw new Exception(trans("comModulesCB.failed_to_set_topic_review_reply"));
        }
        return $response->json();

    }

    /**
     * Updates a Topic Review Reply
     *
     * @param $request
     * @param $topicReviewReplyKey
     * @return mixed
     * @throws Exception
     */
    public static function updateTopicReviewReply($request, $topicReviewReplyKey){

        $response = ONE::put([
            'component' => 'empatia',
            'api' => 'topicReviewReplies',
            'attribute' => $topicReviewReplyKey,
            'params' => [
                'content' => $request->content,
                'code' => $request->status
            ]
        ]);
        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failed_to_update_topic_review_reply"));
        }
        return $response->json();

    }

    /**
     * Deletes a Topic Review Reply
     *
     * @param $topicReviewReplyKey
     * @return mixed
     * @throws Exception
     */
    public static function deleteTopicReviewReply($topicReviewReplyKey){

        $response = ONE::delete([
            'component' => 'empatia',
            'api' => 'topicReviewReplies',
            'attribute' => $topicReviewReplyKey
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failed_to_delete_topic_review_reply"));
        }
        return $response;

    }




    /*------------------------------ //END Topic Reviews Replies --------------------------------------*/

    /**
     * Gets list of Topic Review Status Types
     *
     * @param null $action
     * @return mixed
     * @throws Exception
     */
    public static function getTopicReviewStatusTypes($action = null){
        $response = ONE::get([
            'component' => 'empatia',
            'api' => 'topicReviewStatusTypes',
            'method' => 'list',
            'params' => [
                'action' => $action,
            ]
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failed_to_get_topic_review_status_type"));
        }
        return $response->json();

    }


    public static function followTopic($topicKey) {

        $response = ONE::post([
            'component' => 'empatia',
            'api' => 'topicFollowers',
            'params' => [
                'topic_key' => $topicKey,
            ]
        ]);
        if ($response->statusCode() != 201) {
            throw new Exception(trans("comModulesCB.failed_to_follow_topic"));
        }
        return $response->json();
    }


    public static function getFollowersTopic($topicKey) {

        $response = ONE::get([
            'component' => 'empatia',
            'api' => 'topic',
            'method' => 'topicFollowers',
            'api_attribute' => $topicKey,
        ]);

        if ($response->statusCode() != 200) {
            throw new Exception(trans("comModulesCB.failed_to_get_followers_topic"));
        }
        return $response->json()->data;
    }


    public static function unfollowTopic($topicKey){

        $response = ONE::delete([
            'component' => 'empatia',
            'api' => 'topicFollowers',
            'attribute' => $topicKey
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failed_to_unfollow_topic"));
        }
        return $response->json();

    }

    public static function getUserTopics($userKey) {
        $response = ONE::get([
            'component' => 'empatia',
            'api' => 'topic',
            'api_attribute' => 'user',
            'method' => $userKey,
        ]);
        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failed_to_get_topic_review_status_type"));
        }
        return $response->json();
    }

    public static function getUserTopicsPaginated($userKey,$topicsPerPage, $page, $cbKeys = []) {
        $response = ONE::post([
            'component' => 'empatia',
            'api' => 'topic',
            'api_attribute' => 'user',
            'method' => $userKey,
            'attribute' => 'paginated',
            'params' => [
                'topicsPerPage' => $topicsPerPage,
                'page'          => $page,
                'cbKeys'        => $cbKeys
            ]
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failed_to_get_user_topics_paginated"));
        }
        return $response->json();
    }

    public static function getUserTopicsTimeline($userKey, $noConsultations = false) {
        $response = ONE::get([
            'component'     => 'empatia',
            'api'           => 'topic',
            'api_attribute' => 'user',
            'method'        => $userKey,
            'attribute'     => 'timeline',
            'params' => [
                "noConsultations" => $noConsultations
            ]
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failed_to_get_topic_review_status_type"));
        }
        return $response->json();
    }

    public static function getTopicForTimeline($topicKey)
    {
        $response = ONE::get([
            'component' => 'empatia',
            'api' => 'topic',
            'api_attribute' => $topicKey,
            'method'    => 'forTimeline'
        ]);
        if($response->statusCode() != 200) {
            throw new Exception(trans("comModulesCB.failedToGetParameterWithOptions"));
        }
        return $response->json();
    }

    /*------------------------------  Alliances Reviews Replies -----------------------------------------*/
    public static function createAlliance($originTopicKey, $destinyTopicKey, $message = "") {
        $response = ONE::post([
            'component' => 'empatia',
            "key"   => "topic",
            'api' => 'ally',
            'api_attribute' => 'create',
            'method' => $originTopicKey,
            'attribute' => $destinyTopicKey,
            'params' => [
                "request_message" => $message,
            ]
        ]);
        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failed_to_create_alliance"));
        }
        return $response->json();
    }
    public static function responseToAlliance($ally_key, $response, $message = "") {
        $response = ONE::post([
            'component' => 'empatia',
            "key"   => "topic",
            'api' => 'ally',
            'api_attribute' => 'respond',
            'method' => $ally_key,
            'params' => [
                "response" => $response,
                "message" => $message
            ]
        ]);
        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failed_to_respond_to_alliance"));
        }
        return $response->json();
    }

    /*------------------------------ END Alliances Reviews Replies --------------------------------------*/

    public static function getCbVoteEvents($cbKey){

        $response = ONE::get([
            'component' => 'empatia',
            'api' => 'cb',
            'method' => 'eventsList',
            'api_attribute' => $cbKey
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failed_to_get_cb_vote_events"));
        }
        return $response->json()->data;

    }

    public static function checkIfUserHasTopics($cbKey,$userKey){
        $response = ONE::get([
            'component' => 'empatia',
            'api' => 'cb',
            'method' => 'hasTopics',
            'api_attribute' => $cbKey,
            'attribute' => $userKey,
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failedToVerifyUserTopisc"));
        }
        return $response->json();
    }

    public static function getAllUserTopics($cbKey){
        $response = ONE::get([
            'component' => 'empatia',
            'api' => 'cb',
            'method' => 'getAllUserTopics',
            'api_attribute' => $cbKey,
        ]);
        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failed_to_get_all_user_topics"));
        }
        return $response->json();
    }

    public static function getCbAnalytics($cbKey){
        $response = ONE::get([
            'component' => 'empatia',
            'api' => 'cb',
            'api_attribute' => $cbKey,
            'method' => 'analytics'
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failed_to_get_cb_analytics"));
        }
        return $response->json()->data;

    }

    public static function updateParameterTypes($parameterTypeID, $name, $code, $options, $translations, $request)
    {
        $response = ONE::put([
            'component' => 'empatia',
            'api'       => 'parameterTypes',
            'api_attribute' => $parameterTypeID,
            'params'    => [
                "name" => $name,
                "code" => $code,
                "options" => $options,
                "translations" => $translations,
                "types" => $request
            ]
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesOrchestrator.errorAddingParameterType"));
        }
        return $response->json();
    }

    public static function storeCbPermission($cbKey, $permissions, $groupKey, $userKey, $optionsId){
        $response = ONE::post([
            'component' => 'empatia',
            'api'       => 'padPermissions',
            'params' => [
                'cbKey' => $cbKey,
                'permissions' => $permissions,
                'groupKey' => $groupKey,
                'userKey' => $userKey,
                'optionsId' => $optionsId
            ]
        ]);

        if($response->statusCode()!= 201) {
            throw new Exception(trans("comModulesOrchestrator.error_storing_cb_permissions"));
        }

        return $response->json();
    }

    public static function getCbParametersOptionsPermissions($groupKey, $userKey, $cbKey, $parameterOptions)
    {
        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'padPermissions',
            'method' => 'list',
            'params' => [
                'cbKey' => $cbKey,
                'groupKey' => $groupKey,
                'userKey' => $userKey,
                'parameterOptions' => $parameterOptions
            ]
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesOrchestrator.error_getting_cb_permissions"));
        }
        return $response->json()->data;
    }

    public static function getPadsParametersOptionsPermissions($cbKey, $userKey)
    {
        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'padPermissions',
            'method'    => 'getOptionsPermissions',
            'params'    => ['cbKey' => $cbKey, 'userKey' => $userKey]
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesOrchestrator.error_getting_cb_parameter_options_permissions"));
        }

        return $response->json();
    }

    public static function getTopicsByKeys($topics)
    {
        $response = ONE::post([
            'component' => 'empatia',
            'api'       => 'topic',
            'method' => 'getTopics',
            'params' => [
                'topic_keys' => $topics,
            ]
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesOrchestrator.error_getting_topics"));
        }

        return $response->json()->data;
    }

    public static function getCBStatistics($cbKey) {
        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'cb',
            'api_attribute' => $cbKey,
            'method' => 'statistics'
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesCB.error_getting_cb_statistics"));
        }

        return $response->json()->data;
    }

    /**
     * @param $requestPost
     * @param $topicKey
     * @param $commentType
     * @return mixed
     * @throws Exception
     */
    public static function storePost($requestPost, $topicKey, $commentType)
    {
        $siteComplete = Orchestrator::getSite(Session::get('X-SITE-KEY'));

        $site = [];
        $site['name'] = $siteComplete->name;
        $site['no_reply_email'] = $siteComplete->no_reply_email;

        $response = ONE::post([
            'component' => 'empatia',
            'api'       => 'post',
            'params'    => [
                'contents'  => $requestPost["contents"],
                'parent_id' => $requestPost["parent_id"],
                'topic_key' => $topicKey,
                'type_code' => $commentType,
                'site'      => $site,
                'type'      => $requestPost['type'],
                'link'      => $requestPost['link']
            ]
        ]);

        if ($response->statusCode() != 201) {
            throw new Exception("comModulesCB.failed_to_post_comment.");
        }

        return $response->json();
    }

    public static function getTopicsFilesByType($topics)
    {
        $response = ONE::post([
            'component' => 'empatia',
            'api'       => 'post',
            'method' => 'getTopicsFiles',
            'params' => [
                'topics' => $topics,
            ]
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesCB.error_getting_topics_files"));
        }

        return $response->json()->data;

    }

    /** THIS FUNCTION WAS UPDATED TO RETURN ALL THE TOPIC INFORMATION *
     * @param $topicKey
     * @return
     * @throws Exception
     */
    public static function getTopic($topicKey, $publicCall = false)
    {

        $response = ONE::get([
            'component' => 'empatia',
            'api' => 'topic',
            'api_attribute' => $topicKey,
            'params' => [
                'publicCall' => $publicCall
            ]
        ]);

        if($response->statusCode() != 200) {
            throw new Exception(trans("comModulesCB.failedToGetAllTopicInformation"));
        }
        return $response->json();
    }


    /**
     * @param $cbKey
     * @param $topicKeys
     * @param int $numberOfTopicsToShow
     * @param null $pageToken
     * @param array $filterList
     * @return mixed
     * @throws Exception
     */
    public static function getCBAndTopicKeys($cbKey, $topicKeys, $numberOfTopicsToShow = 6, $pageToken = null, $filterList = []) {
        $response = ONE::post([
            'component' => 'empatia',
            'api' => 'cb',
            'api_attribute' => $cbKey,
            'method' => 'getWithTopicKeys',
            'params' => [
                'pageToken' => $pageToken,
                'filter_list' => $filterList,
                'numberOfTopicsToShow' => $numberOfTopicsToShow,
                'topicKeys' => $topicKeys
            ]
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failed_to_get_cb_and_topic_keys"));
        }
        return $response->json();
    }

    public static function getTopicsMostVoted($cbKey) {
        $response = ONE::post([
            'component' => 'empatia',
            'api' => 'cb',
            'api_attribute' => $cbKey,
            'method' => 'getTopicsbyVotes'
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failed_to_get_cb_and_topic_keys"));
        }
        return $response->json()->data;
    }

    /**
     * THIS FUNCTION RETURNS THE TOPIC POSTS WITH PAGINATION
     * @param $request
     * @param $topicKey
     * @param string $orderBy
     * @return mixed
     * @throws Exception
     */
    public static function getTopicPostsWithPagination($request, $topicKey, $orderBy = 'ASC'){

        $response = ONE::get([
            'component'     => 'empatia',
            'api'           => 'topic',
            'api_attribute' => $topicKey,
            'method'        => 'getTopicPostsWithPagination',
            'params'        => [
                'orderBy' => $orderBy,
                'pageToken' => $request['pageToken'] ?? null,
                'typeOfComment' => $request['typeOfComment'] ?? null,
                'numberOfPostsToShow' => $request['numberOfPostsToShow'] ?? 6,
                'numberOfRepliesToShow' => $request['numberOfRepliesToShow'] ?? 6,
                'postToLoadRepliesFrom' => $request['postToLoadRepliesFrom'] ?? null,
                'publicCall' => true
            ]
        ]);

        if($response->statusCode() != 200){

            throw new Exception(trans("comModulesCB.failedToGetTopicDataWithChildParameters"));
        }
        return $response->json();
    }


    /**
     * GET TOPICS FROM CB WITH THE SUPPLIED TOPICS NUMBERS
     * @param $cbKey
     * @param $topicsNumbers
     * @return mixed
     * @throws Exception
     */
    public static function getTopicsToByTopicNumber($cbKey,$topicsNumbers)
    {
        $response = ONE::post([
            'component'     => 'empatia',
            'api'           => 'cb',
            'method'        => 'getTopicsByNumber',
            'params'        => [
                'cbKey' => $cbKey,
                'topicsNumbers' => $topicsNumbers
            ]
        ]);

        if($response->statusCode() != 200){
            if($response->statusCode() == 408){
                return ['error' => 408, 'message' => $response->json()];
            }elseif($response->statusCode() == 409){
                return ['error' => 409, 'message' => $response->json()];
            }else{
                return ['error' => 500, 'message' => $response->json()];
            }
        }
        return $response->json()->data;
    }


    /**
     * @param $cbKey
     * @param bool $withVotes
     * @param bool $exportIds
     * @return mixed
     * @throws Exception
     */
    public static function getDataToExport($cbKey, $withVotes = false, $exportIds = false)
    {
        // Topics
        $response = ONE::get([
            'component'     => 'empatia',
            'api'           => 'cb',
            'api_attribute' => $cbKey,
            'method'        => 'getDataToExport',
            'params' => [
                'withVotes' => $withVotes,
                'exportIds' => $exportIds
            ]
        ]);

        if($response->statusCode() != 200) {
            throw new Exception(trans("comModulesCB.failedToGetDataToExport"));
        }
        return $response->json()->data;
    }

    public static function publishUserTopic($cbKey)
    {
        $response = ONE::get([
            'component' => 'empatia',
            'api' => 'topic',
            'api_attribute' => $cbKey,
            'method' => 'publishUserTopic'
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failedToPublishUserTopic"));
        }
        return $response->json();
    }


    /** Export topics to new cb
     * @param $cbKey
     * @param $cbKeyExport
     * @param $topics
     * @param $mappingParameters
     * @param $mappingOptions
     * @param $topTopics
     * @param $minVotes
     * @return
     * @throws Exception
     */
    public static function exportTopics($cbKey,$cbKeyExport,$topics,$mappingParameters,$mappingOptions,$topTopics,$minVotes)
    {
        $response = ONE::post([
            'component'     => 'empatia',
            'api'           => 'cb',
            'api_attribute' => $cbKey,
            'method'        => 'exportTopics',
            'params'        => [
                'cb_key_export'      => $cbKeyExport,
                'topic_keys'         => $topics,
                'mapping_parameters' => $mappingParameters,
                'mapping_options'    => $mappingOptions,
                'top_topics'         => $topTopics,
                'min_votes'          => $minVotes
            ]
        ]);
        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failed_to_export_topics"));
        }
        return $response->json();
    }

    /**
     * @param $request
     * @param $cbKey
     * @param $voteKey
     * @return mixed
     * @throws Exception
     */
    public static function updateVoteConfigurations($request, $cbKey, $voteKey)
    {

        $response = ONE::put([
            'component' => 'empatia',
            'api' => 'cb',
            'method' => 'votesConfigurations',
            'api_attribute' => $cbKey,
            'attribute' => $voteKey,
            'params'=> [
                'votesConfig' => $request->configs ?? null,

            ]
        ]);
        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failedToUpdateVoteConfigurations"));
        }
        return $response->json();
    }


    /**
     * @param $cbKey
     * @param $voteKey
     * @return mixed
     * @throws Exception
     */
    public static function getCbVoteConfig($cbKey, $voteKey) {

        $response = ONE::get([
            'component' => 'empatia',
            'api' => 'cb',
            'method' => 'votesConfig',
            'api_attribute' => $cbKey,
            'attribute' => $voteKey
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failedToCbVoteConfig"));
        }
        return $response->json();
    }


    /**
     * @param $cbKey
     * @return mixed
     * @throws Exception
     */
    public static function getCbConfigurationPermissions($cbKey){
        $response = ONE::get([
            'component' => 'empatia',
            'api' => 'cb',
            'api_attribute' => $cbKey,
            'method'        => 'configurationPermissions'
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failedToGetCbConfigurationPermissions"));
        }
        return $response->json();
    }


    /**
     * @return mixed
     * @throws Exception
     */
    public static function getConfigurationPermissions(){
        $response = ONE::get([
            'component' => 'empatia',
            'api' => 'configurationPermission',
            'method' => 'list'
        ]);
        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failedToGetConfigurations"));
        }
        return $response->json()->data;
    }

    /**
     * @param $levels
     * @param $cbKey
     * @return mixed
     * @throws Exception
     */
    public static function insertConfigurationPermission($levels, $cbKey){
        $response = ONE::get([
            'component' => 'empatia',
            'api' => 'configurationPermission',
            'method' => 'insertConfigurationPermission',
            'params'=>[
                'levels'=>$levels,
                'cbKey'=>$cbKey
            ]
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failedToInsertConfigurationPermission"));
        }
        return $response->json();
    }


    /**
     * @param $levels
     * @param $cbKey
     * @return mixed
     * @throws Exception
     */
    public static function updateConfigurationPermission($levels, $cbKey){
        $response = ONE::get([
            'component' => 'empatia',
            'api' => 'configurationPermission',
            'method' => 'updateConfigurationPermission',
            'params'=>[
                'levels'=>$levels,
                'cbKey'=>$cbKey
            ]
        ]);
        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failedToUpdateConfigurationPermission"));
        }
        return $response->json();
    }


    /**
     * @return mixed
     * @throws Exception
     */
    public static function getFlagTypesList()
    {
        $response = ONE::get([
            'component' => 'empatia',
            'api' => 'flagTypes',
            'method'     => 'list'
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failedToGetFlagTypes"));
        }
        return $response->json()->data;
    }

    /**
     * @param $request
     * @param $translations
     * @return mixed
     * @throws Exception
     */
    public static function setFlagType($request, $translations)
    {
        $response = ONE::post([
            'component' => 'empatia',
            'api' => 'flagTypes',
            'params' => [
                'code' => $request->code,
                'translations' => $translations
            ]
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failedToSetFlagType"));
        }
        return $response->json()->data;
    }

    /**
     * @param $id
     * @return mixed
     * @throws Exception
     */
    public static function getFlagType($id)
    {
        $response = ONE::get([
            'component' => 'empatia',
            'api' => 'flagTypes',
            'api_attribute' => $id

        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failedToGetFlagType"));
        }
        return $response->json()->data;
    }

    /**
     * @param $request
     * @param $translations
     * @param $id
     * @return mixed
     * @throws Exception
     */
    public static function updateFlagType($request, $translations, $id){
        $response = ONE::put([
            'component' => 'empatia',
            'api' => 'flagTypes',
            'params' => [
                'code' => $request->code,
                'translations' => $translations
            ],
            'api_attribute' => $id
        ]);
        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failedToUpdateFlagType"));
        }
        return $response->json();
    }

    /**
     * @param $id
     * @return mixed
     */
    public static function deleteFlagType($id)
    {
        $response = ONE::delete([
            'component' => 'empatia',
            'api' => 'flagTypes',
            'attribute' => $id

        ]);
        return $response->json();
    }

    /**
     * @param $cbKey
     * @return mixed
     * @throws Exception
     */
    public static function getFlagsFromCb($cbKey)
    {
        $response = ONE::get([
            'component' => 'empatia',
            'api' => 'flags',
            'api_attribute' => $cbKey,
            'method'     => 'getFlagsFromCb'

        ]);
        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failedToGetFlagsFromCb"));
        }
        return $response->json()->data;
    }


    /**
     * @param $id
     * @return mixed
     * @throws Exception
     */
    public static function getFlag($id)
    {
        $response = ONE::get([
            'component' => 'empatia',
            'api' => 'flags',
            'api_attribute' => $id
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failedToGetFlag"));
        }
        return $response->json()->data;
    }


    /**
     * @param $request
     * @param $translations
     * @param $attachmentCode
     * @return mixed
     * @throws Exception
     */
    public static function setFlag($request, $translations, $attachmentCode)
    {
        $response = ONE::post([
            'component' => 'empatia',
            'api' => 'flags',
            'params' => [
                'attributes' => $request->all(),
                'translations' => $translations,
                'attachmentCode' => $attachmentCode,
            ]
        ]);
        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failedToSetFlagType"));
        }
        return $response->json()->data;
    }


    /**
     * @param $request
     * @param $translations
     * @param $id
     * @return mixed
     * @throws Exception
     */
    public static function updateFlag($request, $translations, $id){
        $response = ONE::put([
            'component' => 'empatia',
            'api' => 'flags',
            'params' => [
                'attributes' => $request->all(),
                'translations' => $translations
            ],
            'api_attribute' => $id
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failedToUpdateFlag"));
        }
        return $response->json();
    }

    public static function deleteFlag($id){
        $response = One::delete([
            'component' => 'empatia',
            'api' => 'flags',
            'api_attribute' => $id
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failedToDeleteFlag"));
        }
        return $response->json();
    }

    /**
     * @param $cbKey
     * @param $flagType
     * @return mixed
     * @throws Exception
     */
    public static function getCbWithFlags($cbKey, $flagType) {

        $response = ONE::get([
            'component' => 'empatia',
            'api' => 'cb',
            'method'     => 'getCbWithFlags',
            'api_attribute' => $cbKey,
            'params' => [
                'flagType' => $flagType,
            ],
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failedToGetCbWithFlags"));
        }
        return $response->json();
    }

    /**
     * @param $request
     * @param $translations
     * @param $attachmentCode
     * @return mixed
     * @throws Exception
     */
    public static function attachFlag($request)
    {
        $response = ONE::post([
            'component' => 'empatia',
            'api'       => 'flags',
            'method'    => 'attachFlag',
            'params'    => $request->all()
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failedToSetFlagType"));
        }
        return $response->json()->data;
    }

    /**
     * @param $elementKey
     * @param $attachmentCode
     * @return mixed
     * @throws Exception
     */
    public static function getElementFlagHistory($elementKey, $attachmentCode)
    {
        $response = ONE::post([
            'component' => 'empatia',
            'api' => 'flags',
            'method'    => 'getElementFlagHistory',
            'params'    => [
                'elementKey'     => $elementKey,
                'attachmentCode' => $attachmentCode,
            ]
        ]);
        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failedToGetElementFlagHistory"));
        }
        return $response->json()->data;
    }

    /**
     * @return mixed
     * @throws Exception
     */
    public static function getActions()
    {
        $response = ONE::get([
            'component' => 'empatia',
            'api' => 'actions',
            'method' => 'list'

        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failedToGetActions"));
        }
        return $response->json()->data;
    }

    /**
     * @param $cbKey
     * @return mixed
     * @throws Exception
     */
    public static function getQuestionnaires($cbKey)
    {
        $response = ONE::get([
            'component' => 'empatia',
            'api' => 'cbQuestionnaire',
            'method' => 'getCbQuestionnaire',
            'params'=>[
                'cbKey'=>$cbKey,
            ]
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failedToGetQuestionnaires"));
        }
        return $response->json()->data;
    }

    /**
     * @param $cbKey
     * @param $actionCode
     * @param $voteKey
     * @return mixed
     * @throws Exception
     */
    public static function getCbQuestionnaireTemplate($cbKey, $actionCode, $voteKey)
    {
        $response = ONE::get([
            'component' => 'empatia',
            'api' => 'cbQuestionnaire',
            'method' => 'getCbQuestionnaireTemplate',
            'params'=>[
                'cbKey' => $cbKey,
                'actionCode' => $actionCode,
                'voteKey' => $voteKey
            ]
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failedToGetCbQuestionnaireTemplate"));
        }
        return $response->json()->data;
    }

    /**
     * @param $actionElements
     * @param $cbKey
     * @return mixed
     * @throws Exception
     */
    public static function setCbQuestionnaire($actionElements, $cbKey)
    {
        $response = ONE::put([
            'component' => 'empatia',
            'api'       => 'cbQuestionnaire',
            'method'    => 'setCbQuestionnaire',
            'params'    =>[
                'elements'  => $actionElements,
                'cb_key'    => $cbKey
            ]
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failedToSetCbQuestionnaires"));
        }
        return $response->json();
    }

    /**
     * @param $cbQuestionnaireKey
     * @param $userKey
     * @return mixed
     * @throws Exception
     */
    public static function getCbQuestionnaireUser($cbQuestionnaireKey, $userKey){
        $response = ONE::post([
            'component' => 'empatia',
            'api' => 'cbQuestionnaireUser',
            'method'     => 'getCbQuestionnaireUser',
            'params'=>[
                'user_key' => $userKey,
                'cb_questionnaire_key' => $cbQuestionnaireKey
            ]
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failedToGetCbTopicAuthors"));
        }
        return $response->json()->data;
    }

    /**
     * @param $user
     * @param $cbQuestionnaireKey
     * @return mixed
     * @throws Exception
     */
    public static function setCbQuestionnaireUser($user, $cbQuestionnaireKey){
        $response = ONE::get([
            'component' => 'empatia',
            'api' => 'cbQuestionnaireUser',
            'method' => 'setCbQuestionnaireUser',
            'params'=>[
                'userKey' => $user,
                'cbQuestionnaireKey' => $cbQuestionnaireKey
            ]
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failedToSetCbQuestionnaireUsers"));
        }
        return $response->json();
    }

    /**
     * @param $cbKey
     * @return mixed
     * @throws Exception
     */
    public static function getCbTopicAuthors($cbKey)
    {
        $response = ONE::get([
            'component' => 'empatia',
            'api' => 'cb',
            'method'     => 'getCbTopicAuthors',
            'api_attribute' => $cbKey
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failedToGetCbTopicAuthors"));
        }
        return $response->json();
    }


    /**
     * @param $request
     * @param $cbKeys
     * @param bool $withConfigurations
     * @param bool $withTopics
     * @param bool $withParameters
     * @param bool $withParameterOptions
     * @param bool $withPosts
     * @param bool $withFlags
     * @param bool $withTranslations
     * @param array $withFilters
     * @return
     * @throws Exception
     */
    public static function getParticipationInformationForDataTable($request, $cbKeys, $withConfigurations = false, $withTopics = false, $withParameters = false, $withParameterOptions = false, $withPosts = false, $withFlags = false, $withTranslations = false,$withFilters = [])
    {

        $response = ONE::post([
            'component' => 'empatia',
            'api'       => 'cb',
            'method'    => 'getParticipationInformationForDataTable',
            'params'    => [
                'cbKeys'               => $cbKeys,
                'withConfigurations'   => $withConfigurations,
                'withTopics'           => $withTopics,
                'withParameters'       => $withParameters,
                'withParameterOptions' => $withParameterOptions,
                'withPosts'            => $withPosts,
                'withFlags'            => $withFlags,
                'withTranslations'     => $withTranslations,
                'tableData'            => One::tableData($request),
                'withFilters'          => $withFilters,
            ]
        ]);
        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failedToGetParticipationInformationForDataTable"));
        }
        return $response->json()->data;
    }

    /**
     * @param $cbKey
     * @return mixed
     * @throws Exception
     */
    public static function getCbTranslations($cbKey)
    {
        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'cbTranslation',
            'method'    => 'getCbTranslations',
            'params'    => [
                'cb_key'=> $cbKey->cb_key
            ]
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failedToGetCbTranslations"));
        }
        return $response->json()->data;
    }

    /**
     * @param $cbKey
     * @param $request
     * @return mixed
     * @throws Exception
     */
    public static function storeOrUpdate($cbKey, $request)
    {
        $response = ONE::post([
            'component' => 'empatia',
            'api'       => 'cbTranslation',
            'method'    => 'storeOrUpdate',
            'params'    => [
                'cb_key'        => $cbKey,
                'translations'  => $request->translations,
                'code'          => $request->code
            ]
        ]);


        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failedToGetStoreOrUpdate"));
        }
        return $response->json();
    }

    /**
     * @param $cbKey
     * @param $request
     * @return mixed
     * @throws Exception
     */
    public static function delete($cbKey, $request)
    {
        $response = ONE::post([
            'component' => 'empatia',
            'api'       => 'cbTranslation',
            'method'    => 'delete',
            'params'   => [
                'cb_key'    => $cbKey,
                'code'      => $request->code
            ]
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failedToGetDelete"));
        }
        return $response->json();
    }

    /**
     *
     * @param $entity
     * @param $user
     * @return mixed
     * @throws Exception
     */
    public static function getCbEntity($entity, $user)
    {
        $response = ONE::post([
            'component' => 'empatia',
            'api'       => 'cbTranslation',
            'method'    => 'getCbEntity',
            'params'    => [
                'entity'    => $entity,
                'user'      => $user
            ]
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failedToGetCbEntity"));
        }
        return $response->json();
    }

    /**
     * @param $cbKey
     * @param $request
     * @return mixed
     * @throws Exception
     */
    public static function storeCodeAdminOrManager($cbKey, $request)
    {
        $response = ONE::post([
            'component' => 'empatia',
            'api'       => 'cbTranslation',
            'method'    => 'storeCodeAdminOrManager',
            'params'    => [
                'cb_key'    => $cbKey,
                'cb'        => $request->cb,
            ]
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failedToGetStoreCodeAdminOrManager"));
        }
        return $response->json();
    }

    /**
     * @param $cbKey
     * @param $code
     * @param $language
     * @param $status
     * @return mixed
     * @throws Exception
     */
    public static function translation($cbKey, $code, $language, $status)
    {
        $response = ONE::get([

            'component' => 'empatia',
            'api' => 'cbTranslation',
            'method'     => 'translation',
            'params'   => [
                'cbKey'=> $cbKey,
                'code' => $code,
                'language'=>$language,
                'status' => $status
            ]
        ]);
        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failedToGetTranslation"));
        }
        return $response->json();
    }

    /**
     * @param $cbKey
     * @param $code
     * @return mixed
     * @throws Exception
     */
    public static function getCode($cbKey, $code)
    {
        $response = ONE::get([
            'component' => 'empatia',
            'api' => 'cbTranslation',
            'method'     => 'getCode',
            'params'   => [
                'cb_key'=> $cbKey,
                'code'=> $code,
            ]
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failedToGetCode"));
        }
        return $response->json();
    }

    /**
     * @param $cbKey
     * @param null $pageToken
     * @param int $numberOfTopicsToShow
     * @param array $filterList
     * @return mixed
     * @throws Exception
     */
    public static function getPublicPadParticipation($cbKey, $pageToken = null, $numberOfTopicsToShow = 6, $filterList = []){

        $response = ONE::post([
            'component' => 'empatia',
            'api' => 'cb',
            'api_attribute' => $cbKey,
            'method' => 'getPublicPadInformation',
            'params' => [
                'pageToken' => $pageToken,
                'filter_list' => $filterList,
                'numberOfTopicsToShow' => $numberOfTopicsToShow
            ]
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failedToGetAllTopicsWithPagination"));
        }

        /*
        if(!empty($response->json())){
            return $response->json();
        } else {
            return json_decode($response->content());
        }
        */
        return $response->json();
    }

    public static function getMultiplePadsInformation($pageToken = null, $numberOfTopicsToShow = 6, $filterList = []){
        $response = ONE::post([
            'component' => 'empatia',
            'api' => 'cb',
            'method' => 'getMultiplePublicPadsInformation',
            'params' => [
                'pageToken' => $pageToken,
                'filter_list' => $filterList,
                'numberOfTopicsToShow' => $numberOfTopicsToShow
            ]
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failedToGetAllTopicsWithPagination"));
        }
        return $response->json();
    }

    /**
     * @param $cbKey
     * @param $topicCheckpointNewId
     * @return mixed
     * @throws Exception
     */
    public static function finishPhase($cbKey, $topicCheckpointNewId) {
        $response = ONE::get([
            'component' => 'empatia',
            'api' => 'cb',
            'api_attribute' => $cbKey,
            'method' => 'finishPhase',
            'attribute' => $topicCheckpointNewId
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failed_to_finish_phase"));
        }
        return $response->json();

    }

    /**
     * @param $cbKey
     * @param $topicCheckpointNewId
     * @return mixed
     * @throws Exception
     */
    public static function finishPhase2($cbKey, $topicCheckpointNewId) {
        $response = ONE::get([
            'component' => 'empatia',
            'api' => 'cb',
            'api_attribute' => $cbKey,
            'method' => 'finishPhase2',
            'attribute' => $topicCheckpointNewId
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failed_to_finish_phase2"));
        }
        return $response->json();
    }

    public static function switchToNewParameter($cbKey) {
        $response = ONE::get([
            'component' => 'empatia',
            'api' => 'cb',
            'api_attribute' => $cbKey,
            'method' => 'switchToNewParameter'
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failed_to_finish_phase"));
        }
        return $response->json();
    }

    /**
     * @param $cbKey
     * @return mixed
     * @throws Exception
     */
    public static function getStatusTranslations($cbKey)
    {
        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'cbTranslation',
            'method'    => 'getStatusTranslations',
            'params'    => [
                'cb_key'=> $cbKey,
            ]
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failedToGetStatusTranslations"));
        }

//        TODO: change to Cache
        Redis::set("cb-translations",json_encode($response->json()));

        return $response->json();
    }

    /**
     * @param $cbKey
     * @return mixed
     * @throws Exception
     */
    public static function getCbQuestions($cbKey){

        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'technicalAnalysisQuestions',
            'method'    => 'list',
            'attribute' => $cbKey
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failedToGetCbQuestions"));
        }
        return $response->json();
    }

    /**
     * @param Request $request
     * @param $contentTranslation
     * @param $cbKey
     * @return mixed
     * @throws Exception
     */
    public static function setCbQuestion(Request $request, $contentTranslation, $cbKey)
    {
        $response = ONE::post([
            'component' => 'empatia',
            'api'       => 'technicalAnalysisQuestions',
            'params'    => [
                'cb_key'        => $cbKey,
                'translations'  => $contentTranslation,
                'acceptable'    => $request->input('acceptable'),
                'code'          => $request->get("code")
            ]]);

        if ($response->statusCode() != 201) {
            throw new Exception(trans("comModulesCB.failedToSetCbQuestion"));
        }
        return $response->json();
    }

    /**
     * @param $techAnalysisQuestionKey
     * @return mixed
     * @throws Exception
     */
    public static function getCbQuestion($techAnalysisQuestionKey){
        $response = ONE::get([
            'component'     => 'empatia',
            'api'           => 'technicalAnalysisQuestions',
            'api_attribute' => $techAnalysisQuestionKey
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failedToGetCbQuestion"));
        }
        return $response->json();
    }

    /**
     * @param $techAnalysisQuestionKey
     * @return mixed
     * @throws Exception
     */
    public static function editCbQuestion($techAnalysisQuestionKey){
        $response = ONE::get([
            'component'     => 'empatia',
            'api'           => 'technicalAnalysisQuestions',
            'api_attribute' => $techAnalysisQuestionKey,
            'method'        => 'edit'
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failedToEditCbQuestion"));
        }
        return $response->json();
    }

    /**
     * @param $data
     * @param $techAnalysisQuestionKey
     * @return mixed
     * @throws Exception
     */
    public static function updateQuestion($data, $techAnalysisQuestionKey)
    {
        $response = ONE::put([
            'component'     => 'empatia',
            'api'           => 'technicalAnalysisQuestions',
            'api_attribute' => $techAnalysisQuestionKey,
            'params'        => $data
        ]);

        if ($response->statusCode() != 200) {
            throw new Exception(trans("comModulesCB.failedToUpdateQuestion"));
        }
        return $response->json();
    }

    /**
     * @param $techAnalysisQuestionKey
     * @throws Exception
     */
    public static function deleteQuestion($techAnalysisQuestionKey)
    {
        $response = ONE::delete([
            'component'     => 'empatia',
            'api'           => 'technicalAnalysisQuestions',
            'api_attribute' => $techAnalysisQuestionKey
        ]);

        if ($response->statusCode() != 200) {
            throw new Exception(trans("comModulesCB.failedToDeleteQuestion"));
        }
    }

    /**
     * @param $topicKey
     * @return mixed
     * @throws Exception
     */
    public static function getVerificationIfTechnicalAnalysisExist($topicKey, $noException = false){

        $response = ONE::get([
            'component'     => 'empatia',
            'api'           => 'technicalAnalysis',
            'method'        => 'TopicTechnicalAnalysis',
            'attribute'     => $topicKey
        ]);

        if($response->statusCode() != 200){
            if ($noException) 
                return [];
            else
                throw new Exception(trans("comModulesCB.failedToVerifyIfTechnicalAnalysisExists"));
        } 
        return $response->json();
    }

    /**
     * @param $cbKey
     * @param $topicKey
     * @return mixed
     * @throws Exception
     */
    public static function getQuestionsAndExistenceOfTechnicalAnalysis($cbKey, $topicKey, $noException = false)
    {
        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'technicalAnalysisQuestions',
            'method'    => 'questionsAndExistenceOfTechnicalAnalysis',
            'attribute' => $cbKey,
            'params'    => [
                "topicKey" => $topicKey
            ]
        ]);

        if ($response->statusCode() != 200) {
            if ($noException)
                return [];
            else
                throw new Exception(trans("comModulesCB.failedToGetTechnicalAnalysisQuestionsAndExistenceOfTechnicalAnalysis"));
        }
        return $response->json();
    }

    /**
     * @param $request
     * @param $topicKey
     * @param $technicalAnalysisQuestionsAndAnswers
     * @return mixed
     * @throws Exception
     */
    public static function createTechnicalAnalysis($request, $topicKey, $technicalAnalysisQuestionsAndAnswers)
    {
        $response = ONE::post([
            'component' => 'empatia',
            'api'       => 'technicalAnalysis',
            'params'    => [
                "impact"                                => $request->impact,
                "budget"                                => $request->budget,
                "accepted"                              => isset($request->accepted) ? array_keys($request->accepted) : null,
                "topicKey"                              => $topicKey,
                "execution"                             => $request->execution,
                "sustainability"                        => $request->sustainability,
                "decision"                              => $request->decision??0,
                "technicalAnalysisQuestionsAndAnswers"  => $technicalAnalysisQuestionsAndAnswers
            ]
        ]);

        if ($response->statusCode() != 201) {
            throw new Exception(trans("comModulesCB.failedToCreateTechnicalAnalysis"));
        }
        return $response->json();
    }

    /**
     * @param $topicKey
     * @param $cbKey
     * @param $version
     * @return mixed
     * @throws Exception
     */
    public static function getTechnicalAnalysis($topicKey, $cbKey, $version)
    {
        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'technicalAnalysis',
            'method'    => 'topic',
            'attribute' => $topicKey,
            'params'    => [
                "cbKey"     => $cbKey,
                "version"   => $version
            ]
        ]);

        if ($response->statusCode() != 200) {
            throw new Exception(trans("comModulesCB.failedToGetTechnicalAnalysis"));
        }
        return $response->json();
    }

    /**
     * @param $topicKey
     * @return mixed
     * @throws Exception
     */
    public static function destroyTechnicalAnalysis($topicKey)
    {
        $response = ONE::delete([
            'component'     => 'empatia',
            'api'           => 'technicalAnalysis',
            'api_attribute' => $topicKey
        ]);

        if ($response->statusCode() != 200) {
            throw new Exception(trans("comModulesCB.failedToDestroyTechnicalAnalysis"));
        }

        return $response->json();
    }

    /**
     * @param $request
     * @param $topicKey
     * @param $version
     * @param $technicalAnalysisQuestionsAndAnswers
     * @return mixed
     * @throws Exception
     */
    public static function updateTechnicalAnalysis($request, $topicKey, $version, $technicalAnalysisQuestionsAndAnswers)
    {
        $response = ONE::put([
            'component'     => 'empatia',
            'api'           => 'technicalAnalysis',
            'api_attribute' => $version,
            'params' => [
                "impact"                                => $request->impact,
                "budget"                                => $request->budget,
                "accepted"                              => isset($request->accepted) ? array_keys($request->accepted) : null,
                "topicKey"                              => $topicKey,
                "execution"                             => $request->execution,
                "sustainability"                        => $request->sustainability,
                "decision"                              => $request->decision??0,
                "technicalAnalysisQuestionsAndAnswers"  => $technicalAnalysisQuestionsAndAnswers
            ]
        ]);

        if ($response->statusCode() != 200) {
            throw new Exception(trans("comModulesCB.failedToUpdateTechnicalAnalysis"));
        }
        return $response->json();
    }

    /**
     * @param $topicKey
     * @param $version
     * @return mixed
     * @throws Exception
     */
    public static function activateTechnicalAnalysis($topicKey, $version)
    {
        $response = ONE::put([
            'component' => 'empatia',
            'api'       => 'technicalAnalysis',
            'method'    => 'activate',
            'attribute' => $topicKey,
            'params'    => ["version" => $version]
        ]);

        if ($response->statusCode() != 200) {
            throw new Exception(trans("comModulesCB.failedToActivateTechnicalAnalysis"));
        }
        return $response->json();
    }

    /* Dashboard related methods */

    public static function getDashBoardElementsList()
    {
        $response = ONE::get([
            'component' => 'empatia',
            'api' => 'dashBoardElements',
            'method'     => 'list'
        ]);


        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failedToGetDashBoardElementsList"));
        }
        return $response->json()->data;
    }

    public static function getEntityDashBoardElements()
    {
        $response = ONE::get([
            'component' => 'empatia',
            'api' => 'dashBoardElements',
            'method'     => 'getEntityDashBoardElements'
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failedToGetEntityDashBoardElements"));
        }
        return $response->json()->data;
    }

    public static function setDashBoardElement($request, $translations)
    {
        $response = ONE::post([
            'component' => 'empatia',
            'api' => 'dashBoardElements',
            'params' => [
                'code' => $request->code,
                'default_position' => $request->default_position ?? 0,
                'translations' => $translations
            ]
        ]);
        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failedToSetDashBoardElement"));
        }
        return $response->json()->data;
    }

    public static function getDashBoardElement($id)
    {
        $response = ONE::get([
            'component' => 'empatia',
            'api' => 'dashBoardElements',
            'api_attribute' => $id

        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failedToGetDashBoardElement"));
        }
        return $response->json()->data;
    }

    public static function updateDashBoardElement($request, $translations,$id){

        $response = ONE::put([
            'component' => 'empatia',
            'api' => 'dashBoardElements',
            'params' => [
                'code' => $request->code,
                'default_position' => $request->default_position ?? 0,
                'translations' => $translations,
                'configurations' => $request->dashBoardElementConfigurations ?? null
            ],
            'api_attribute' => $id
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failedToUpdateDashBoardElement"));
        }
        return $response->json();
    }

    public static function deleteDashBoardElement($id)
    {
        $response = ONE::delete([
            'component' => 'empatia',
            'api' => 'dashBoardElements',
            'attribute' => $id

        ]);
        return $response->json();
    }


    public static function getDashBoardElementConfigurationsList($request)
    {
        $response = ONE::get([
            'component' => 'empatia',
            'api' => 'dashBoardElementConfigurations',
            'method'     => 'list',
            'params' => [
                'tableData' => ONE::tableData($request)
            ]
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failedToGetDashBoardElementConfigurationsList"));
        }
        return $response->json()->data;
    }
    public static function deleteDashBoardElementConfiguration($id)
    {
        $response = ONE::delete([
            'component' => 'empatia',
            'api' => 'dashBoardElementConfigurations',
            'attribute' => $id

        ]);
        return $response->json();
    }

    public static function setDashBoardElementConfiguration($request, $translations)
    {
        $response = ONE::post([
            'component' => 'empatia',
            'api' => 'dashBoardElementConfigurations',
            'params' => [
                'code' => $request->code,
                'type' => $request->type,
                'translations' => $translations
            ]
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failedToSetDashBoardElementConfiguration"));
        }
        return $response->json()->data;
    }

    public static function getDashBoardElementConfiguration($id)
    {
        $response = ONE::get([
            'component' => 'empatia',
            'api' => 'dashBoardElementConfigurations',
            'api_attribute' => $id

        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failedToGetDashBoardElementConfiguration"));
        }
        return $response->json()->data;
    }

    public static function updateEntityDashBoardElements($id)
    {
        $response = ONE::get([
            'component' => 'empatia',
            'api' => 'dashBoardElements',
            'api_attribute' => $id,
            'method' => 'updateEntityDashBoardElements',


        ]);

        if($response->statusCode() != 200){

            throw new Exception(trans("comModulesCB.failedToGetDashBoardElement"));
        }
        return $response->json();
    }

    public static function setUserDashBoardElement($attributes,$userKey)
    {
        $response = ONE::get([
            'component' => 'empatia',
            'api' => 'dashBoardElements',
            'method' => 'setUserDashBoardElement',
            'params' => [
                'attributes' => $attributes,
                'userKey' => $userKey
            ]
        ]);

        if($response->statusCode() != 200){

            throw new Exception(trans("comModulesCB.failedToSetUserDashBoardElement"));
        }
        return $response->json();
    }
    public static function getAvailableDashBoardElementsWithConfigurations()
    {
        $response = ONE::get([
            'component' => 'empatia',
            'api' => 'dashBoardElements',
            'method'     => 'getAvailableDashBoardElementsWithConfigurations'
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failedToGetAvailableDashBoardElementsWithConfigurations"));
        }
        return $response->json()->data;
    }

    public static function updateDashBoardElementConfiguration($request, $translations,$id){

        $response = ONE::put([
            'component' => 'empatia',
            'api' => 'dashBoardElementConfigurations',
            'params' => [
                'code' => $request->code,
                'type' => $request->type,
                'default_value' =>  $request->default_value,
                'translations' => $translations,

            ],
            'api_attribute' => $id
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failedToUpdateDashBoardElement"));
        }
        return $response->json();
    }

    public static function unsetUserDashBoardElement($id,$userKey)
    {
        $response = ONE::delete([
            'component' => 'empatia',
            'api' => 'dashBoardElements',
            'method' => 'unsetUserDashBoardElement',
            'params' => [
                'id' => $id,
                'userKey' => $userKey
            ]
        ]);

        if($response->statusCode() != 200){

            throw new Exception(trans("comModulesCB.failedToSetUserDashBoardElement"));
        }
        return $response->json();
    }

    /**
     * @param $cbKeys
     * @param bool $withConfigurations
     * @param bool $withTopics
     * @param bool $withStatus
     * @param bool $withParameters
     * @param bool $withParameterOptions
     * @param bool $withPosts
     * @param bool $withFlags
     * @param bool $withTranslations
     * @param bool $withModeration
     * @param bool $withFilters
     * @param bool $withSortOrder
     * @param bool $withLimit
     * @return mixed
     * @throws Exception
     * @internal param $request
     */
    public static function getParticipationInformation($cbKeys, $withConfigurations = true, $withTopics = false, $withStatus = false, $withParameters = false, $withParameterOptions = false, $withPosts = false, $withFlags = false, $withTranslations = false, $withModeration = false, $withFilters = false, $withSortOrder = false, $withLimit = false)
    {

        $response = ONE::post([
            'component' => 'empatia',
            'api'       => 'cb',
            'method'    => 'getParticipationInformation',
            'params'    => [
                'cbKeys'               => $cbKeys,
                'withConfigurations'   => $withConfigurations,
                'withTopics'           => $withTopics,
                'withStatus'           => $withStatus,
                'withParameters'       => $withParameters,
                'withParameterOptions' => $withParameterOptions,
                'withPosts'            => $withPosts,
                'withFlags'            => $withFlags,
                'withTranslations'     => $withTranslations,
                'withModeration'       => $withModeration,
                'withFilters'          => $withFilters,
                'withSortOrder'        => $withSortOrder,
                'withLimit'            => $withLimit
            ]
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failedToGetParticipationInformatione"));
        }
        return $response->json()->data;

    }
    public static function updateUserDashBoardElement($elementId,$attributes,$userKey)
    {
        $response = ONE::put([
            'component' => 'empatia',
            'api' => 'dashBoardElements',
            'method' => 'updateUserDashBoardElement',
            'attribute' => $elementId,
            'params' => [
                'attributes' => $attributes,
                'userKey' => $userKey
            ]
        ]);

        if($response->statusCode() != 200){

            throw new Exception(trans("comModulesCB.failedToUpdateUserDashBoardElement"));
        }
        return $response->json();
    }
    public static function reorderUserDashBoardElements($positions)
    {
        $response = ONE::put([
            'component' => 'empatia',
            'api' => 'dashBoardElements',
            'method'     => 'reorderUserDashBoardElements',
            'params' => [
                'positions' => $positions,
            ]
        ]);
        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failedToGetAvailableDashBoardElementsWithConfigurations"));
        }
        return $response->json();
    }

    /**
     * @param $topicKey
     * @param $version
     * @param $status
     * @param $activeBy
     * @param bool $mayChangeParentTopics
     * @return mixed
     * @throws Exception
     */
    public static function changeActiveVersionStatus($topicKey, $version, $status, $activeBy,$mayChangeParentTopics = false){
        $response = ONE::post([
            'component' => 'empatia',
            'api' => 'topic',
            'method' => 'activeVersionStatus',
            'api_attribute' => $topicKey,
            'params' => [
                'version' => $version,
                'status' => $status,
                'activeBy' => $activeBy,
                'checkParentTopics' => $mayChangeParentTopics
            ]
        ]);

        if ($response->statusCode() != 200) {
            throw new Exception(trans("comModulesCB.failedToGetTopicVersions"));
        }
        return $response->json();
    }


    public static function sendTechnicalAnalysisNotification($request, $technicalAnalysisKey, $site, $userKey){
        $response = ONE::post([
            'component' => 'empatia',
            'api'       => 'technicalAnalysis',
            'method'    => 'sendNotification',
            'api_attribute' => $technicalAnalysisKey,
            'params'    => [
                "groups" => $request->input('groups'),
                "managers" => $request->input('managers'),
                "site" => $site,
                'userKey' => $userKey
            ]
        ]);


        if ($response->statusCode() != 200) {
            throw new Exception(trans("comModulesCB.failedToSendTechnicalAnalysisNotification"));
        }
        return $response->json();
    }


    public static function getCbMenuTranslations($cbKey) {
        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'cbMenuTranslation',
            'api_attribute' => $cbKey,
            'method'    => 'list'
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failedToGetCbMenuTranslations"));
        }
        return $response->json()->data;
    }

    /**
     * @param $cbKey
     * @param $request
     * @return mixed
     * @throws Exception
     */
    public static function storeOrUpdateCbMenuTranslation($cbKey, $request) {
        $response = ONE::post([
            'component' => 'empatia',
            'api'       => 'cbMenuTranslation',
            'method'    => 'storeOrUpdate',
            'params'    => [
                'cb_key'        => $cbKey,
                'translations'  => $request->translations,
                'code'          => $request->code
            ]
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failedToStoreOrUpdateCbMenuTranslation"));
        }
        return $response->json();
    }
    public static function deleteCbMenuTranslation($cbKey, $code) {
        $response = ONE::delete([
            'component' => 'empatia',
            'api'       => 'cbMenuTranslation',
            'api_attribute' => $cbKey,
            'method'    => $code
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failedToDeleteCbMenuTranslation"));
        }
        return $response->json();
    }
    public static function isCbMenuTranslationCodeUsed($cbKey, $code) {
        $response = ONE::get([
            'component'     => 'empatia',
            'api'           => 'cbMenuTranslation',
            'api_attribute' => $cbKey,
            'method'        => 'isCodeUsed',
            'attribute'     => $code
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failedToIsCbMenuTranslationCodeUsed"));
        }
        return $response->json();
    }
    public static function getEntityCbsWithMenuTranslations($user, $entity = null) {
        $response = ONE::post([
            'component' => 'empatia',
            'api'       => 'cbMenuTranslation',
            'method'    => 'getEntityCbsWithTranslations',
            'params'    => [
                'user'      => $user,
                'entity'    => $entity
            ]
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failedToGetEntityCbsWithMenuTranslations"));
        }
        return $response->json();
    }
    public static function copyCbMenuTranslations($origin, $destiny) {
        $response = ONE::post([
            'component' => 'empatia',
            'api' => 'cbMenuTranslation',
            'api_attribute' => $destiny,
            'method'     => 'copy',
            'params'   => [
                'origin'    => $origin
            ]
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failedToCopyCbMenuTranslations"));
        }
        return $response->json();
    }


    public static function getPostAbuse($id){
        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'postabuse',
            'attribute' => $id
        ]);

        if ($response->statusCode() != 200) {
            throw new Exception(trans("comModulesCB.failedToGetPostAbuse"));
        }
        return $response->json()->postabuse;
    }

    public static function getPost($postId){
        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'post',
            'attribute' => $postId
        ]);

        if ($response->statusCode() != 200) {
            throw new Exception(trans("comModulesCB.failedToGetPost"));
        }

        return $response->json()->post;
    }

    public static function storePostAbuse($requestTopic){
        $response = ONE::post([
            'component' => 'empatia',
            'api'       => 'postabuse',
            'params'    => [
                'comment' => $requestTopic["comment"]
            ]
        ]);

        if ($response->statusCode() != 200) {
            throw new Exception(trans("comModulesCB.failedToStorePostAbuse"));
        }

        return $response->json()->postabuse;
    }

    public static function updatePostAbuse($requestForum, $id){
        $response = ONE::put([
            'component' => 'empatia',
            'api'       => 'postabuse',
            'params'    => [
                'comment' => $requestForum["comment"]
            ],
            'attribute' => $id
        ]);

        if ($response->statusCode() != 200) {
            throw new Exception(trans("comModulesCB.failedToUpdatePostAbuse"));
        }

        return $response->json();
    }

    public static function destroyPostAbuse($id){
        $response = ONE::delete([
            'component' => 'empatia',
            'api'       => 'postabuse',
            'attribute' => $id,
        ]);

        if ($response->statusCode() != 200) {
            throw new Exception(trans("comModulesCB.failedToDeletePostAbuse"));
        }

        return $response->json();
    }

    public static function listPostAbuse($postId){
        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'postabuse',
            'method'    => 'list',
            'attribute' => $postId
        ]);

        if ($response->statusCode() != 200) {
            throw new Exception(trans("comModulesCB.failedToGetListPostAbuse"));
        }

        return $response->json()->postabuses;
    }

    public static function getListAbusesByCB($cb){
        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'postabuse',
            'method'    => 'listByCb',
            'attribute' => $cb

        ]);

        if ($response->statusCode() != 200) {
            throw new Exception(trans("comModulesCB.failedToGetListAbuseByCb"));
        }

        return $response->json()->postabuses;
    }

    public static function getListAbusesByTopic($topicId){
        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'postabuse',
            'method'    => 'listByTopic',
            'attribute' => $topicId
        ]);

        if ($response->statusCode() != 200) {
            throw new Exception(trans("comModulesCB.failedToGetListAbuseByTopic"));
        }

        return $response->json()->postabuses;
    }

    public static function acceptAllForumAbuses($cbId){
        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'postabuse',
            'method'    => 'acceptAllForumAbuses',
            'attribute' => $cbId
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failedToAcceptAllForumAbuses"));
        }
        return $response->json();
    }

    public static function declineAllForumAbuses($cbId){
        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'postabuse',
            'method'    => 'declineAllForumAbuses',
            'attribute' => $cbId
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failedToDeclineAllForumAbuses"));
        }
        return $response->json();
    }

    public static function acceptAllTopicAbuses($topicId){
        $response = ONE::get([
            'component' => 'cb',
            'api'       => 'postabuse',
            'method'    => 'acceptAllTopicAbuses',
            'attribute' => $topicId
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failedToAcceptAllTopicAbuses"));
        }
        return $response->json()->data;
    }

    public static function declineAllTopicAbuses($topicId){
        $response = ONE::get([
            'component' => 'cb',
            'api'       => 'postabuse',
            'method'    => 'declineAllTopicAbuses',
            'attribute' => $topicId
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failedToDeclineAllTopicAbuses"));
        }

        return $response->json();
    }

    public static  function acceptPostAbuses($postId){
        $response = ONE::get([
            'component' => 'cb',
            'api'       => 'postabuse',
            'method'    => 'acceptPostAbuses',
            'attribute' => $postId
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failedToAcceptPostAbuses"));
        }

        return $response->json();
    }

    public static  function declinePostAbuses($postId){
        $response = ONE::get([
            'component' => 'cb',
            'api'       => 'postabuse',
            'method'    => 'declinePostAbuses',
            'attribute' => $postId
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failedToDeclinePostAbuses"));
        }

        return $response->json();
    }

    public static function getVotes($cbKey){
        $response = ONE::get([
            'component' => 'empatia',
            'api' => 'cb',
            'api_attribute' => $cbKey,
            'method' => 'votes'
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failedToGetVotes"));
        }

        return $response->json();
    }

    public static function updateBlock($cbKey){
        $response = ONE::put([
            'component' => 'empatia',
            'api' => 'cb',
            'api_attribute' => $cbKey,
            'method' => 'block'
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failedToUpdateBlock"));
        }
        return $response->json();
    }

    public static function setTopicWithFirstPost($params){
        $response = ONE::post([
            'component' => 'empatia',
            'api'       => 'topic',
            'method'    => 'topicsWithFirstPost',
            'params' => [
                'topicList' => $params
            ]
        ]);


        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failedToSetTopicWithFirstPost"));
        }
        return $response->json()->data;
    }

    public static function getTopicWithFirstPost($cbKey){
        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'cb',
            'api_attribute' => $cbKey,
            'method'       => 'topicsWithFirstPost',
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failedToGetTopicWithFirstPost"));
        }
        return $response->json();
    }

    public static function getParam($id){
        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'parameters',
            'attribute' => $id
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failedToGetParameter"));
        }
        return $response->json();
    }

    public static function updateParam($id, $params){
        $response = ONE::put([
            'component' => 'empatia',
            'api'       => 'parameters',
            'params'    => $params,
            'attribute' => $id
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failedToUpdateParameter"));
        }
        return $response->json();
    }

    public static function deleteParam($id){
        $response = ONE::delete([
            'component' => 'empatia',
            'api'       => 'parameters',
            'attribute' => $id,
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failedToDeleteParameter"));
        }
        return $response->json();
    }

    public static function listParameters(){
        $response = ONE::get([
            'component'     => 'empatia',
            'api'           => 'parameters',
            'method'        => 'list',
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failedToDeleteParameter"));
        }
        return $response->json()->data;
    }

    public static function updatePostBlock($postKey, $value){
        $response = ONE::put([
            'component' => 'empatia',
            'api'       => 'post',
            'api_attribute' => $postKey,
            'method'    => 'blocked',
            'params' =>[
                'blocked' => $value
            ]
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failedToDeleteParameter"));
        }
        return $response->json();
    }

    public static function activePost($postKey, $value){
        $response = ONE::put([
            'component' => 'empatia',
            'api'       => 'post',
            'api_attribute' => $postKey,
            'method'    => 'active',
            'params' =>[
                'active' => $value
            ]
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failedToActivePost"));
        }
        return $response->json();
    }

    public static function deletePost($postKey){
        $response = ONE::delete([
            'component' => 'empatia',
            'api'       => 'post',
            'attribute' => $postKey,
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failedToDeletePost"));
        }
        return $response->json();
    }

    public static function listFilesByType($postKey){
        $response = ONE::get([
            'component'     => 'empatia',
            'api'           => 'post',
            'api_attribute' => $postKey,
            'method'        => 'filesByType',
            'attribute'     => 'list'
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failedToGetListFilesByType"));
        }
        return $response->json()->data;
    }

    public static function getExportProposalsToProjects(){
        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'cb',
            'api_attribute' => 'exportProposalsToProjects',
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failedToGetExportProposalsToProjects"));
        }
        return $response->json();
    }

    public static function getTopicData($topicId){
        $response = ONE::get([
            'component' => 'empatia',
            'api' => 'topic',
            'api_attribute' => $topicId,
            'method' => 'data'
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failedToGetTopicData"));
        }
        return $response->json();
    }

    public static function updatePost($contents, $topicKey, $postKey){
        $response = ONE::put([
            'component' => 'empatia',
            'api' => 'post',
            'params' => [
                'contents' => $contents,
                'topic_key' => $topicKey
            ],
            'api_attribute' => $postKey
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failedToGetTopicData"));
        }
        return $response->json();
    }

    public static function setPostLike($postId){
        $response = ONE::post([
            'component' => 'empatia',
            'api' => 'postlike',
            'method' => 'like',
            'params' => [
                'post_id' => $postId
            ]
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failedToSetPostLike"));
        }
        return $response->json();
    }

    public static function setPostDislike($postId){
        $response = ONE::post([
            'component' => 'empatia',
            'api' => 'postlike',
            'method' => 'dislike',
            'params' => [
                'post_id' => $postId
            ]
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failedToSetPostDislike"));
        }
        return $response->json();
    }

    public static function deletePostLike($postId){
        $response = ONE::delete([
            'component' => 'empatia',
            'api' => 'postlike',
            'attribute' => $postId
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failedToDeletePostLike"));
        }
        return $response->json();
    }

    public static function setReportAbuse($postKey, $typeId, $comment){
        $response = ONE::post([
            'component' => 'empatia',
            'api' => 'postabuse',
            'params' => [
                'post_key' => $postKey,
                'type_id' => !empty($typeId) ? $typeId : 1,
                'comment' => !empty($comment) ? $comment : ""
            ]
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failedToSetReportAbuse"));
        }
        return $response->json();
    }

    public static function getPostHistory($postKey){
        $response = ONE::get([
            'component' => 'empatia',
            'api' => 'post',
            'api_attribute' =>  $postKey,
            'method' => 'history'
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failedToGetPostHistory"));
        }
        return $response->json()->data;
    }

    public static function updateFilesForTopic($request){
        $response = ONE::put([
            'component' => 'empatia',
            'api' => 'post',
            'api_attribute' => $request->post_key,
            'method' => 'files',
            'attribute' => $request->file_id,
            'params' => [
                'name' => $request->name,
                'description' => $request->description,
            ]

        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failedToUpdateFilesForTopic"));
        }
        return $response->json()->data;
    }

    public static function getFilesForTopic($request){
        $response = ONE::get([
            'component' => 'empatia',
            'api' => 'post',
            'api_attribute' => $request->post_key,
            'method' => 'files',
            'attribute' => $request->file_id
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failedToGetFilesForTopic"));
        }
        return $response->json()->data;
    }

    public static function listFilesForTopic($postKey){
        $response = ONE::get([
            'component' => 'empatia',
            'api' => 'post',
            'api_attribute' => $postKey,
            'method' => 'files',
            'attribute' => 'list'
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failedToListFilesForTopic"));
        }
        return $response->json()->data;
    }

    public static function deleteFilesForTopic($request){
        $response = ONE::delete([
            'component'     => 'empatia',
            'api'           => 'post',
            'api_attribute' => $request->post_key,
            'method'        => 'files',
            'attribute'     => $request->file_id
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failedToDeleteFilesForTopic"));
        }
        return $response->json()->data;
    }

    public static function updateOrderFile($request){
        $response = ONE::put([
            'component'     => 'empatia',
            'api'           => 'post',
            'api_attribute' => $request->post_key,
            'method'        => 'orderFile',
            'params'        => [
                'type_id'   => $request->type_id,
                'movement'  => $request->movement
            ],
            'attribute'     => $request->file_id
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failedToUpdateOrderFile"));
        }
        return $response->json()->data;
    }

    public static function revertPost($request){
        $response = ONE::get([
            'component'=> 'empatia',
            'api' => 'post',
            'api_attribute' => $request->postKey,
            'method' => 'revertPost',
            'attribute' => $request->postVersion
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failedToGetRevertPost"));
        }
        return $response->json()->data;
    }

    public static function getCbAbuses($cbKey){
        $response = ONE::get([
            'component' => 'empatia',
            'api' => 'cb',
            'api_attribute' => $cbKey,
            'method' => 'abuses'
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failedToGetCbAbuses"));
        }
        return $response->json()->data;
    }


    public static function getTopicsByCbKey($cbKey){
        $response = ONE::get([
            'component' => 'empatia',
            'api' => 'cb',
            'api_attribute' => $cbKey,
            'method' => 'getTopicsByCbKey'
        ]);
        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failedToGetTopicsByCbKey"));
        }
        return $response->json()->data;
    }

    public static function publishTechnicalAnalysisResults($cbKey, $questionKeys, $parameterIds, $passedStatusKey, $failedStatusKey, $simulate = true) {
        $response = ONE::post([
            'component' => 'empatia',
            'api' => 'cb',
            'api_attribute' => $cbKey,
            'method' => 'publishTechnicalAnalysisResults',
            'params' => [
                "questions" => $questionKeys,
                "parameters" => $parameterIds,
                "passed" => $passedStatusKey,
                "failed" => $failedStatusKey,
                "simulate" => $simulate
            ]
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failedToPublishTechnicalAnalysisResults"));
        }
        return $response->json();
    }

    public static function getTopicsByParentKey($topicKey)
    {
        $response = ONE::post([
            'component' => 'empatia',
            'api'       => 'topic',
            'method' => 'getTopicsByParent',
            'params' => [
                'topicKey' => $topicKey,
            ]
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesOrchestrator.error_getting_topics"));
        }

        return $response->json()->data;
    }

    public static function toggleFlagActiveStatus($status, $elementKey, $relationId, $attachmentCode) {
        $response = ONE::post([
            'component' => 'empatia',
            'api' => 'flags',
            'method'    => 'toggleActiveStatus',
            'params'    => [
                'relationId'     => $relationId,
                'elementKey'     => $elementKey,
                'status'         => $status,
                'attachmentCode' => $attachmentCode,
            ]
        ]);
        
        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failedToToggleFlagActiveStatus"));
        }
        return $response->json();
    }
    public static function getUserLoginLevels($cbKey)
    {
        $response = ONE::get([
            'component' => 'empatia',
            'api' => 'cb',
            'api_attribute' => $cbKey,
            'method' => 'getPadActionsThatRequireLoginLevelsAccordingToUser',
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failed_to_get_user_login_levels"));
        }

        return $response->json()->data;
    }

    public static function exportVotesCountToParameter($cbKey, $parameterId, $eventKey)
    {
        $response = ONE::post([
            'component' => 'empatia',
            'api' => 'cb',
            'api_attribute' => $cbKey,
            'method' => 'exportVotesCountToParameter',
            'params' => [
                'parameter_id' => $parameterId,
                'event_key'    => $eventKey
            ]
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failed_to_export_votes_count_to_parameter"));
        }

        return $response->json();
    }

    public static function getActivePads()
    {
        $response = ONE::get([
            'component' => 'empatia',
            'api' => 'cb',
            'method' => 'getActivePads',
        ]);
        
        if($response->statusCode() != 200){
            return [];
        }
        
        return $response->json()->data;
    }

    public static function setTranslation($id = null, $code, $translation = null, $lang_code = "", $cbKey = "", $siteKey=""){

        $response = ONE::post([
            'component' => 'empatia',
            'api' => 'translation',
            'params' => [
                'id' => $id,
                'cb_key' => $cbKey,
                'site_key' => $siteKey,
                'code' => $code,
                'language_code' => $lang_code,
                'translation' => $translation
            ]
        ]);

        return $response->json();
    }

    public static function getCBSTranslations($siteKey = null, $cbKey = null){

        $response = ONE::get([
            'component' => 'empatia',
            'api' => 'translation',
            'method' => 'list',
            'params' => [
                'site_key' => $siteKey,
                'cb_key' => $cbKey
            ]
        ]);

        return $response->json();
    }

    
    public static function deleteTranslation($code, $id, $siteKey = null, $cbKey = null){

        $response = ONE::delete([
            'component' => 'empatia',
            'api' => 'translation',
            'method' => 'deleteLines',
            'params' => [
                'id' => $id,
                'code' => $code,
                'cb_key' => $cbKey,
                'site_key' => $siteKey
            ]
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failedToDeleteTranslation"));
        }
        return $response;
        
    }

    public static function getTranslation($code, $lang_code = "",  $siteKey="", $cbKey = ""){

        $response = ONE::get([
            'component' => 'empatia',
            'api' => 'translation',
            'method' => 'getTranslation',
            'params' => [
                'cb_key' => $cbKey,
                'site_key' => $siteKey,
                'code' => $code,
                'language_code' => $lang_code
            ]
        ]);


        return $response->json();
    }


    public static function getPad($cbKey)
    {
        $response = ONE::post([
            'component' => 'empatia',
            'api' => 'cb',
            'api_attribute' => $cbKey,
            'method' => 'getPad',
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failedToGetPad"));
        }

        return $response->json()->data;

    }

    public static function getPadTopics($cbKey)
    {
        $response = ONE::post([
            'url' => 'http://luismonteiro.empatia-dev.onesource.pt:5015',
            'component' => 'empatia',
            'api' => 'cb',
            'api_attribute' => $cbKey,
            'method' => 'getPadTopics',
        ]);
        
        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCB.failedToGetPadTopics"));
        }

        return $response->json()->data;

    }


    public static function displayTopic($topicKey)
    {
        $response = ONE::post([
            //'url' => 'http://luismonteiro.empatia-dev.onesource.pt:5015',
            'component' => 'empatia',
            'api' => 'topic',
            'api_attribute' => $topicKey,
            'method' => 'getTopic',
        ]);
//        !is_null($response->json()) ? dd("remote DD",$response->json()) : die("remote ECHO" .$response->content());

        if ($response->statusCode() != 200) {
            throw new Exception(trans("comModulesCB.failedToGetTopic"));
        }

        return $response->json()->data;
    }
    public static function getCbFilters($cbKey)
    {

        $response = ONE::get([
            'component' => 'empatia',
            'api' => 'cb',
            'method' => 'getCbFilters',
            'params' => [
                'cbKey' => $cbKey,
            ]
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesOrchestrator.error_getting_filters"));
        }

        return $response->json()->data;

    }

}
