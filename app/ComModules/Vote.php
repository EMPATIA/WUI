<?php

namespace App\ComModules;

use App\Http\Requests\Request;
use App\One\One;
use Exception;

class Vote {

    public static function getVoteMethodWithConfigurations($methodId){

        $response = ONE::get([
            'component' => 'vote',
            'api' => 'method',
            'api_attribute' => $methodId
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesVote.errorRetrievingVoteMethodsConfiguration"));
        }
        return $response->json();
    }

    public static function getListMethods(){

        $response = ONE::get([
            'component' => 'vote',
            'api' => 'method',
            'method' => 'list'
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesVote.errorRetrievingVoteMethodsList"));
        }
        return $response->json()->data;
    }

    public static function updateVoteEvent($request, $configurations = null) {

        $voteKey = $request->voteKey;
        $startDate = $request->startDate;
        $endDate = $request->endDate;
        $startTime = $request->startTime;
        $endTime = $request->endTime;

        $response = ONE::put([
            'component' => 'vote',
            'api' => 'event',
            'attribute' => $voteKey,
            'params' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'start_time' => $startTime,
                'end_time' => $endTime,
                'configurations' => $configurations,
            ]
        ]);

        if($response->statusCode() != 201){
            throw new Exception(trans("comModulesVote.errorUpdatingVoteEvent"));
        }
        return $response;
    }

    /**
     * @param $request
     * @param $configurations
     * @return mixed
     * @throws Exception
     */
    public static function setVoteEvent($request, $configurations) {

        // dd($request->all());

        $name           = $request->name;
        $code           = $request->code;
        $endDate        = $request->endDate;
        $endTime        = $request->endTime;
        $startDate      = $request->startDate;
        $startTime      = $request->startTime;
        $methodSelect   = $request->methodSelect;

        $array = [];
        if(!empty($request->weightTypeVote) && $request->weightTypeVote == 1){

            foreach ($request->all() as $key => $value) {
                $b = explode('_', $key);
                $c = explode('-',$key);
                if (strpos($key, '_pos') !== false) {
                    $array[$b[0]][$b[1]] = $value;
                }
                if (strpos($key, '_weight') !== false) {
                    $array[$b[0]][$b[1]] = $value;
                }
                if (strpos($key, 'text') !== false) {
                    $array[$c[1]]['translations'][$c[2]] = ['lang_code' => $c[2], 'translation' => $value];
                }
            }
        }

        $response = ONE::post([
            // 'url' => 'http://pedro.pinto.empatia-dev.onesource.pt:5011',
            'component' => 'vote',
            'api'       => 'event',
            'params'    => [
                'name'              => $name,
                'code'              => $code,
                'end_date'          => $endDate,
                'end_time'          => $endTime,
                'method_id'         => $methodSelect,
                'start_date'        => $startDate,
                'start_time'        => $startTime,
                'configurations'    => $configurations,
                'weightType'        => $array,
            ]
        ]);

        if($response->statusCode() != 201){
            throw new Exception(trans("comModulesVote.errorStoreVoteEvent"));
        }
        return $response->json();
    }

    public static function setVoteEventWithData($data) {

        $methodSelect = $data["methodSelect"];
        $startDate = $data["startDate"];
        $endDate = $data["endDate"];
        $startTime = $data["startTime"];
        $endTime = $data["endTime"];
        $configurations = $data["configurations"];

        $response = ONE::post([
            'component' => 'vote',
            'api' => 'event',
            'params' => [
                'method_id' => $methodSelect,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'start_time' => $startTime,
                'end_time' => $endTime,
                'configurations' => $configurations,
            ]
        ]);

        if($response->statusCode() != 201){
            throw new Exception(trans("comModulesVote.errorStoreVoteEvent"));
        }
        return $response->json();
    }

    public static function getAllShowEvents($eventKey) {
        $events = [];

        if(is_array($eventKey)){
            foreach($eventKey as $key){
                $events[] = $key;
            }
        } else {
            $events[] = $eventKey;
        }

        $response = ONE::post([
            'component' => 'vote',
            'api' => 'event',
            'method' => 'showEvents',
            'params' => [
                'events' => $events
            ]
        ]);
        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesVote.errorRetrievingShowEvents"));
        }

        return $response->json()->data;
    }

    public static function getListMethodGroups() {

        $response = ONE::get([
            'component' => 'vote',
            'api' => 'methodGroup',
            'method' => 'list'
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesVote.errorRetrievingListMethodGroups"));
        }
        return $response->json()->data;
    }

    public static function getVoteEventMethods($cbVoteEventsData){
        $response = ONE::post([
            'component' => 'vote',
            'api' => 'event',
            'method' => 'showEvents',
            'params' => [
                'events' => $cbVoteEventsData]
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesVote.errorRetrievingVoteMethods"));
        }
        return $response->json()->data;
    }

    public static function getVoteMethods($cbId){

        //TODO: delete method and use 'CB::getListCbVotes($cbId)' and 'Vote::getVoteEventMethods($cbVoteEventsData)' instead

        $response = ONE::get([
            'component' => 'cb',
            'api' => 'cb',
            'method' => 'votes',
            'api_attribute' => $cbId
        ]);

        if ($response->statusCode() == 200) {
            $cbVoteEvents = $response->json()->data;

            $cbVoteEventsData = [];
            $cbVoteEventsNames = [];
            foreach ($cbVoteEvents as $key => $cbVoteEvent){
                $cbVoteEventsData[] =  $cbVoteEvent->vote_key;
                $cbVoteEventsNames[$cbVoteEvent->vote_key] = $cbVoteEvent->name;
            }

            $response = ONE::post([
                'component' => 'vote',
                'api' => 'event',
                'method' => 'showEvents',
                'params' => [
                    'events'=>$cbVoteEventsData]
            ]);
            $voteEvent = $response->json()->data;
            $info = [];
            //TODO:send language and receive only one translation
            foreach ($voteEvent as $vt){
                $info[]= ['voteKey' => $vt->key, 'cbId' =>$cbId, 'methodName'=>$vt->method->name, 'name'=>$cbVoteEventsNames[ $vt->key]];
            }
            return $info;
        }
    }

    public static function getVotesTimeLine()
    {
        $response = ONE::get([
            'component' => 'vote',
            'api' => 'vote',
            'method' => 'voteTimeline'
        ]);
        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesVote.errorRetrievingVoteTimeLine"));
        }
        return $response->json()->data;
    }



    public static function SetVoteMethod($methodGroupId, $translation,$code)
    {
        $response = ONE::post([
            'component' => 'vote',
            'api'       => 'method',
            'params'    => [
                'method_group_id' => $methodGroupId,
                'translations' => $translation,
                'code' => $code
            ]
        ]);
        if($response->statusCode()!= 201) {
            throw new Exception(trans("comModulesVote.errorSettingNewVoteMethod"));

        }
        return $response->json();
    }

    public static function UpdateVoteMethod($methodId,$methodGroupId, $translation,$code)
    {
        $response = ONE::put([
            'component' => 'vote',
            'api'       => 'method',
            'attribute' => $methodId,
            'params'    => [
                'method_group_id'   => $methodGroupId,
                'translations'      => $translation,
                'code'              => $code
            ]
        ]);
        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesVote.errorUpdatingVoteMethod"));
        }
        return $response->json();

    }

    public static function deleteVoteMethod($id)
    {
        $response = ONE::delete([
            'component' => 'vote',
            'api'       => 'method',
            'attribute' => $id,
        ]);
        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesVote.errorDeletingVoteMethod"));
        }

    }

    public static function SetVoteMethodConfig($methodId,$parameterType,$translations,$code)
    {
        $response = ONE::post([
            'component' => 'vote',
            'api'       => 'configuration',
            'params'    => [
                'method_id' => $methodId,
                'parameter_type'    => $parameterType,
                'translations'      => $translations,
                'code'              => $code
            ]
        ]);
        if($response->statusCode()!= 201) {
            throw new Exception(trans("comModulesVote.errorSettingNewVoteMethod"));
        }
        return $response->json();


    }

    public static function getVoteMethodConfiguration($configId)
    {
        $response = ONE::get([
            'component' => 'vote',
            'api' => 'configuration',
            'attribute' => $configId
        ]);
        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesVote.errorRetrievingVoteMethodConfiguration"));
        }
        return $response->json();

    }

    public static function getVoteMethodEdit($methodId)
    {
        $response = ONE::get([
            'component' => 'vote',
            'api' => 'method',
            'method' => 'edit',
            'api_attribute' => $methodId
        ]);
        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesVote.errorRetrievingVoteMethodTranslations"));
        }
        return $response->json();

    }

    public static function getVoteMethodConfigurationEdit($configId)
    {
        $response = ONE::get([
            'component' => 'vote',
            'api' => 'configuration',
            'method' => 'edit',
            'api_attribute' => $configId
        ]);
        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesVote.errorRetrievingVoteMethodConfigurationTranslations"));
        }
        return $response->json();


    }

    public static function updateVoteMethodConfig($methodId, $configId, $parameterType, $translations,$code)
    {
        $response = ONE::put([
            'component' => 'vote',
            'api'       => 'configuration',
            'attribute' => $configId,
            'params'    => [
                'method_id' => $methodId,
                'parameter_type'    => $parameterType,
                'translations'      => $translations,
                'code'              => $code
            ]
        ]);
        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesVote.errorSettingMethodConfiguration"));
        }
        return $response->json();
    }

    public static function deleteVoteMethodConfiguration($configId)
    {
        $response = ONE::delete([
            'component' => 'vote',
            'api'       => 'configuration',
            'attribute' => $configId,
        ]);
        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesVote.errorDeletingMethod"));
        }
    }

    public static function getGroupMethod($groupId)
    {
        $response = ONE::get([
            'component' => 'vote',
            'api' => 'methodGroup',
            'method' => 'listMethods',
            'api_attribute' => $groupId
        ]);
        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesVote.errorRetrievingGroupMethods"));
        }
        return $response->json()->data;


    }

    public static function getVoteStatus($voteKey,$userKey = null)
    {


        if(!empty($userKey)){

            $response = One::get([
                'component' => 'vote',
                'api' => 'event',
                'api_attribute' => $voteKey,
                'method' => 'voteStatus',
                'params' => [
                    'user_key' => $userKey
                ]
            ]);

        }else{

            $response = One::get([
                // 'url' => 'http://pedro.pinto.empatia-dev.onesource.pt:5011',
                'component' => 'vote',
                'api' => 'event',
                'api_attribute' => $voteKey,
                'method' => 'voteStatus'
            ]);

        }

        if($response->statusCode() != 200){

            throw new Exception(trans("comModulesVote.errorRetrievingVoteStatus"));
        }
        return $response->json();

    }

    public static function getAllShowEventsNoTranslation($voteKeys)
    {
        $response = ONE::post([
            'component' => 'vote',
            'api' => 'event',
            'method' => 'showEventsNoTranslation',
            'params' => [
                'events' => $voteKeys
            ]
        ]);
        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesVote.errorRetrievingEventsNoTranslation"));
        }

        return $response->json()->data;
    }

    public static function getGeneralConfigurationTypes()
    {
        $response = ONE::get([
            'component' => 'vote',
            'api' => 'generalConfigType',
            'method' => 'list'
        ]);

        if ($response->statusCode() != 200){
            throw new Exception(trans("comModulesVote.errorRetrievingGeneralCongifurationTypes"));
        }
        return $response->json()->data;
    }

    public static function  submitVoting( $eventKeys, $userKey = null, $returnVotes = false){
        if (!is_array($eventKeys))
            $eventKeys = array($eventKeys);

        if ($returnVotes)
            $votes = [];

        foreach($eventKeys as $eventKey){
            if(isset($userKey)){
                $response = ONE::post([
                    'component' => 'vote',
                    'api'       => 'event',
                    'api_attribute' => $eventKey,
                    'method' => 'submitVotes',
                    'params' => [
                        'user_key' => $userKey,
                        'returnVotes' => $returnVotes
                    ]
                ]);
            }else{
                $response = ONE::post([
                    'component' => 'vote',
                    'api'       => 'event',
                    'api_attribute' => $eventKey,
                    'method' => 'submitVotes',
                    'params' => [
                        'returnVotes' => $returnVotes
                    ]
                ]);
            }

            if ($response->statusCode() != 200){
                throw new Exception(trans("comModulesVote.errorSubmitingVotesTypes"));
            } else if ($returnVotes && isset($response->json()->votes))
                $votes = array_merge($votes,$response->json()->votes);
        }

        return ($returnVotes) ? $votes : true;
    }


    public static function getVoteResults($voteKey)
    {
        $response = One::get([
            'component' => 'vote',
            'api' => 'event',
            'api_attribute' => $voteKey,
            'method' => 'voteResults'
        ]);
        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesVote.errorRetrievingVoteResult"));
        }
        return $response->json();

    }

    /**
     * @param $voteEventsList
     * @return mixed
     * @throws Exception
     */
    public static function getCbTotalVotes($voteEventsList)
    {
        $response = ONE::post([
            'component' => 'vote',
            'api' => 'event',
            'method' => 'getCbTotalVotes',
            'params' => [
                'vote_event_keys' => $voteEventsList
            ]
        ]);
        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesVote.errorRetrievingCbTotalVotes"));
        }

        return $response->json();
    }

    public static function getUserVotesForEvents($eventKeys)
    {
        $response = One::post([
            'component' => 'vote',
            'api' => 'event',
            'method' => 'userVotes',
            'params' => [
                'eventKeys' => $eventKeys,
            ]
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesVote.error_retrieving_user_votes_for_event"));
        }
        return $response->json();

    }

    public static function getUserVotesForEvent($eventKey, $userKey)
    {
        $response = One::post([
            // 'url' => 'http://pedro.pinto.empatia-dev.onesource.pt:5011',
            'component' => 'vote',
            'api' => 'event',
            'method' => 'userVotesForEvent',
            'params' => [
                'eventKey' => $eventKey,
                'userKey' => $userKey
            ]
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesVote.error_retrieving_user_votes_for_event"));
        }
        return $response->json();

    }


    /**
     * ATTACH USER KEY TO VOTE EVENT WITH A CERTAIN CODE
     * @param $userKey
     * @param $voteEventKey
     * @param $code
     * @return array
     */
    public static function attachUserToVoteEventWithCode($userKey, $voteEventKey, $code)
    {
        $response = One::post([
            'component' => 'vote',
            'api' => 'event',
            'method' => 'attachUserToVoteEventWithCode',
            'params' => [
                'userKey'  => $userKey,
                'eventKey' => $voteEventKey,
                'code'     => $code
            ]
        ]);

        if($response->statusCode() != 200){
            if($response->statusCode() == 408){
                return ['error' => 408, 'message' => trans('comModulesVote.theCodeIsAlreadyRegisteredInThisVoteEvent')];
            }elseif($response->statusCode() == 409){
                return ['error' => 409, 'message' => trans('comModulesVote.theUserIsAlreadyRegisteredInThisVoteEvent')];
            }else {
                return ['error' => 500, 'message' => trans('comModulesVote.failedToAttachUserToVoteEventWithCode')];
            }
        }
        return $response->json();

    }

    /**
     * REGISTER IN PERSON VOTES
     * @param $code
     * @param $voteEventKey
     * @param $userVotes
     * @return array
     */
    public static function registerUserInPersonVoting($code, $voteEventKey, $userVotes)
    {
        $response = One::post([
            'component' => 'vote',
            'api' => 'event',
            'method' => 'registerUserInPersonVoting',
            'params' => [
                'code'      => $code,
                'eventKey'  => $voteEventKey,
                'userVotes' => $userVotes
            ]
        ]);

        if($response->statusCode() != 200){
            if($response->statusCode() == 408){
                return ['error' => 408, 'message' => trans('comModulesVote.theCodeIsNotRegisteredForThisVoteEvent')];
            }elseif($response->statusCode() == 409){
                return ['error' => 409, 'message' => trans('comModulesVote.theUserHasAlreadyVoted')];
            }else{
                return ['error' => 500, 'message' => trans('comModulesVote.failedToRegisterUserInPersonVoting')];
            }
        }
        return $response->json();
    }


    /**
     * DELETE ALL PREVIOUS USER VOTES
     * @param $code
     * @param $voteEventKey
     * @return array
     */
    public static function deleteUserVotesInVoteEvent($code, $voteEventKey){

        $response = One::post([
            'component' => 'vote',
            'api' => 'event',
            'method' => 'deleteUserVotesInVoteEvent',
            'params' => [
                'code'      => $code,
                'eventKey'  => $voteEventKey
            ]
        ]);

        if($response->statusCode() != 200){
            if($response->statusCode() == 408){
                return ['error' => 408, 'message' => trans('comModulesVote.theCodeIsNotRegisteredForThisVoteEvent')];
            }else{
                return ['error' => 500, 'message' => trans('comModulesVote.failedToDeleteUserVotesInVoteEvent')];
            }
        }
        return $response->json();

    }


    /**
     * DELETE ALL PREVIOUS USER VOTES
     * @param $voteEventKey
     * @return array
     * @throws Exception
     */
    public static function unSubmitUserVotesInEvent($voteEventKey){

        $response = One::post([
            'component' => 'vote',
            'api' => 'event',
            'method' => 'unSubmitUserVotesInEvent',
            'params' => [
                'eventKey'  => $voteEventKey
            ]
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesVote.errorUnSubmittingUserVotes"));
        }
        return $response->json();

    }

    public static function getEventAndVotes($eventKey) {
        $response = One::get([
            'component' => 'vote',
            'api' => 'event',
            'api_attribute' => $eventKey,
            'method' => 'getEventAndVotes'
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesVote.errorRetreivingEventVotes"));
        }
        return $response->json();
    }

    public static function getDataForVoteCode($voteCodeData) {
        $response = One::post([
            'component' => 'vote',
            'api' => 'vote',
            'api_attribute' => 'voteCode',
            'params' => [
                'voteCodeData' => $voteCodeData
            ]
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesVote.errorRetreivingVotesByCode"));
        }
        return $response->json()->votes;
    }

    public static function storePublicUserVoting($request){

        $response = One::post([
            'component' => 'vote',
            'api' => 'event',
            'method' => 'storePublicUserVoting',
            'params' => [
                'eventKey'  => $request['vote_event_key'],
                'userKey' => $request['user_key'],
                'votes' => $request['votes'],

            ]
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesVote.errorSubmittingPublicUserVotes"));
        }
        return $response->json();

    }

    public static function getEventLevels() {
        $response = One::get([
            'component' => 'vote',
            'api' => 'eventlevels',
            'method' => 'list'
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesVote.errorRetrievingEventLevelsList"));
        }
        return $response->json();
    }


    public static function getEventLevel($cbKey, $eventKey) {
        $response = One::get([
            'component' => 'vote',
            'api' => 'eventlevels',
            'method' => 'eventlevel',
            'params'=>[
                'cb_key'=> $cbKey,
                'event_key'=>$eventKey
            ]
        ]);
        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesVote.errorRetrievingEventLevel"));
        }
        return $response->json();
    }



    public static function storeEventLevel($cbKey, $values) {
        $response = One::get([
            'component' => 'vote',
            'api' => 'eventlevels',
            'method' => 'storeEventLevel',
            'params'=>[
                'cb_key'=> $cbKey,
                'values'=>$values
            ]
        ]);
        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesVote.errorRetrievingStoreEventLevel"));
        }
        return $response->json();
    }



    public static function updateEventLevel($cbKey, $values) {
        $response = One::get([
            'component' => 'vote',
            'api' => 'eventlevels',
            'method' => 'updateEventLevel',
            'params'=>[
                'cb_key'=> $cbKey,
                'values'=>isset($values) ? $values:null
            ]
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesVote.errorRetrievingUpdateEventLevel"));
        }
        return $response->json();
    }


    public static function getEventLevelCbKey($cbKey) {
        $response = One::get([
            'component' => 'vote',
            'api' => 'eventlevels',
            'method' => 'eventlevelCbKey',
            'params'=>[
                'cb_key'=> $cbKey
            ]
        ]);
        // dd($response);
        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesVote.errorRetrievingEventLevel"));
        }
        return $response->json();
    }

    public static function getVote($voteKey){
        $response = ONE::get([
            'component' => 'vote',
            'api' => 'event',
            'attribute' => $voteKey
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesVote.errorRetrievingVote"));
        }
        return $response->json();
    }

    public static function setVote($event_key, $vote_key, $value,$source, $user_key, $type_id = null){
        if(!is_null($user_key)){
            $response = ONE::post([
                'component' => 'vote',
                'api' => 'vote',
                'params' => [
                    'event_key' =>  $event_key,
                    'vote_key' => $vote_key,
                    'value' => $value,
                    'source' => $source,
                    'user_key' => $user_key,

                ],
            ]);
        }
        else{
            $response = ONE::post([
                // 'url' => 'http://pedro.pinto.empatia-dev.onesource.pt:5011',
                'component' => 'vote',
                'api' => 'vote',
                'params' => [
                    'event_key' => $event_key,
                    'vote_key' => $vote_key,
                    'value' => $value,
                    'source' => $source,
                    'type_id' => $type_id

                ],
            ]);
        }

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesVote.errorRetrievingVote"));
        }
        return $response->json();
    }

    public static function getUserVotesCount($voteEvents, $userKeys, $voteKeys = null) {
        if (is_string($voteEvents))
            $voteEvents = array($voteEvents);

        if (is_string($userKeys))
            $userKeys = array($userKeys);

        if (is_string($voteKeys))
            $voteKeys = array($voteKeys);

        $response = One::post([
            'component'     => 'vote',
            'api'           => 'vote',
            'method'        => 'userVotesCount',
            'params'        => [
                "voteEvents" => $voteEvents,
                "userKeys" => $userKeys,
                "voteKeys" => $voteKeys
            ]
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesVote.failed_to_get_user_votes_count"));
        }
        return $response->json();
    }

    public static function deleteUserVotes() {
        $response = One::delete([
            'component'     => 'vote',
            'api'           => 'vote',
            'method'        => 'deleteUserVotes'
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesVote.failed_to_delete_user_votes"));
        }
        return $response->json();
    }

    public static function getEventsVoteCount($voteEventsKeys){
        if (is_string($voteEventsKeys))
            $voteEventsKeys = array($voteEventsKeys);

        $response = ONE::post([
            'component' => 'vote',
            'api' => 'event',
            'method' => 'voteCounts',
            'params' => [
                'events' => $voteEventsKeys
            ]
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesVote.failed_to_retrieve_vote_event_counts"));
        }
        return $response->json();
    }


    public static function getPadVotes($voteKeys, $userKey)
    {
        $response = ONE::post([
            'component' => 'vote',
            'api' => 'event',
            'method' => 'getPadVotes',
            'params' => [
                'events' => $voteKeys,
                'user_key' => $userKey
            ]
        ]);
        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesVote.failed_to_retrieve_vote_event_counts"));
        }
        return $response->json()->data;
    }

    public static function getVoteList($voteKey,$role,$filters,$request){
        $response = ONE::get([
            'component' => 'vote',
            'api' => 'vote',
            'method' => 'getVoteList',
            'params' => [
                'voteKey' => $voteKey,
                'role'    => $role,
                'filters' => $filters,
                'tableData' => One::tableData($request)
            ],
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesVote.failed_to_retrieve_votes_list"));
        }
        return $response->json();

    }

    public static function submitUserVote($votesId, $submit){

        $response = One::post([
            'component'     => 'vote',
            'api'           => 'vote',
            'method' => 'submitUserVote',
            'params' => [
                'votes_id' => $votesId,
                'submit'   => $submit
            ],
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesVote.failed_to_submit_user_votes"));
        }
        return $response->json();
    }

    public static function deleteUserVote($voteId){

        $response = One::delete([
            'component'     => 'vote',
            'api'           => 'vote',
            'attribute' => $voteId,
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesVote.failed_to_delete_user_vote"));
        }
        return $response->json();
    }

    public static function deleteVotes($votesId){

        $response = One::post([
            'component'     => 'vote',
            'api'           => 'vote',
            'method' => 'deleteVotes',
            'params' => ['votes_id' => $votesId],
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesVote.failed_to_delete_user_votes"));
        }
        return $response->json();
    }
}
