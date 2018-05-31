<?php

namespace App\ComModules;
use App\One\One;
use Exception;

class Analytics
{

    /** get statistics
     * @param $formKey
     * @return mixed
     * @throws Exception
     */
    public static function getStatistics($formKey)
    {

        $response = ONE::get([
            'component' => 'analytics',
            'api' => 'q',
            'api_attribute' => $formKey,
            'method' => 'statistics'
        ]);

        if($response->statusCode()!= 200){
            throw new Exception(trans("comModulesAnalytics.error_retrieving_statistics"));
        }
        return $response->json();
    }



    /** Get vote Statistics by date and channel
     * @param $voteEventKey
     * @return mixed
     * @throws Exception
     */
    public static function getVoteStatisticsByDate($voteEventKey)
    {
        $response = ONE::get([
            'component' => 'analytics',
            'api' => 'voteEvent',
            'api_attribute' => $voteEventKey,
            'method' => 'statisticsByDate'
        ]);
        if($response->statusCode()!= 200){
            throw new Exception(trans("comModulesAnalytics.error_retrieving_statistics_by_date"));
        }
        return $response->json()->data;
    }

/*
    public static function getVoteStatisticsByChannelAndDate($voteEventKey, $options = [])
    {
        $response = ONE::get([
            'component' => 'analytics',
            'api' => 'voteEvent',
            'api_attribute' => $voteEventKey,
            'method' => 'statisticsByChannelAndDate',
            'params' => $options
        ]);

        if($response->statusCode()!= 200){
            throw new Exception(trans("comModulesAnalytics.error_retrieving_statistics_by_date_range"));
        }
        return $response->json()->data;
    }
*/


    /** Get vote Statistics by date and channel
     * @param $voteEventKey
     * @return mixed
     * @throws Exception
     */
    public static function getVoteStatisticsByDateRange($voteEventKey, $options = [])
    {
        $response = ONE::get([
            // 'url' => 'http://ilidio.empatia-dev.onesource.pt:5001',
            'component' => 'analytics',
            'api' => 'voteEvent',
            'api_attribute' => $voteEventKey,
            'method' => 'statisticsByDateRange',
            'params' => $options
        ]);


        if($response->statusCode()!= 200){
            throw new Exception(trans("comModulesAnalytics.error_retrieving_statistics_by_date_range"));
        }
        return $response->json()->data;
    }

    /** Get vote Statistics by date and channel
     * @param $voteEventKey
     * @return mixed
     * @throws Exception
     */
    public static function getVoteStatisticsByHour($voteEventKey, $options)
    {
        $response = ONE::get([
            // 'url' => 'http://ilidio.empatia-dev.onesource.pt:5001',
            'component' => 'analytics',
            'api' => 'voteEvent',
            'api_attribute' => $voteEventKey,
            'method' => 'statisticsByHour',
            'params' => $options
        ]);

        if($response->statusCode()!= 200){
            throw new Exception(trans("comModulesAnalytics.error_retrieving_statistics_by_date"));
        }
        return $response->json()->data;
    }

    /** Get vote Statistics by town and channel
     * @param $voteEventKey
     * @return mixed
     * @throws Exception
     */
    public static function getVoteStatisticsByTown($voteEventKey)
    {
        $response = ONE::get([
            'component' => 'analytics',
            'api' => 'voteEvent',
            'api_attribute' => $voteEventKey,
            'method' => 'statisticsByTown'
        ]);

        if($response->statusCode()!= 200){
            throw new Exception(trans("comModulesAnalytics.error_retrieving_statistics_by_town"));
        }
        return $response->json()->data;

    }

    public static function getVoteStatisticsByAge($voteEventKey)
    {

        $response = ONE::get([
            'component' => 'analytics',
            'api' => 'voteEvent',
            'api_attribute' => $voteEventKey,
            'method' => 'statisticsByAge'
        ]);

        if($response->statusCode()!= 200){
            throw new Exception(trans("comModulesAnalytics.error_retrieving_statistics_by_age"));
        }
        return $response->json()->data;
    }

    public static function getVoteStatisticsByGender($voteEventKey)
    {
        $response = ONE::get([
            'component' => 'analytics',
            'api' => 'voteEvent',
            'api_attribute' => $voteEventKey,
            'method' => 'statisticsByGender'
        ]);

        if($response->statusCode()!= 200){
            throw new Exception(trans("comModulesAnalytics.error_retrieving_statistics_by_gender"));
        }
        return $response->json()->data;
    }

    public static function getVoteStatisticsByProfession($voteEventKey)
    {
        $response = ONE::get([
            'component' => 'analytics',
            'api' => 'voteEvent',
            'api_attribute' => $voteEventKey,
            'method' => 'statisticsByProfession'
        ]);

        if($response->statusCode()!= 200){
            throw new Exception(trans("comModulesAnalytics.error_retrieving_statistics_by_profession"));
        }
        return $response->json()->data;

    }

    public static function getVoteStatisticsByEducation($voteEventKey)
    {
        $response = ONE::get([
            'component' => 'analytics',
            'api' => 'voteEvent',
            'api_attribute' => $voteEventKey,
            'method' => 'statisticsByEducation'
        ]);

        if($response->statusCode()!= 200){
            throw new Exception(trans("comModulesAnalytics.error_retrieving_statistics_by_education"));
        }
        return $response->json()->data;

    }

    public static function getVoteStatisticsTop($voteEventKey, $top)
    {
        $response = ONE::post([
            'component' => 'analytics',
            'api' => 'voteEvent',
            'api_attribute' => $voteEventKey,
            'method' => 'statisticsByTop',
            'params' => [
                'top' => $top
            ]
        ]);
        if($response->statusCode()!= 200){
            throw new Exception(trans("comModulesAnalytics.error_retrieving_statistics_by_top"));
        }
        return $response->json()->data;


    }

    public static function getVoteStatisticsTopByDay($voteEventKey, $top)
    {
        $response = ONE::post([
            'component' => 'analytics',
            'api' => 'voteEvent',
            'api_attribute' => $voteEventKey,
            'method' => 'statisticsTopByDate',
            'params' => [
                'top' => $top
            ]
        ]);
        if($response->statusCode()!= 200){
            throw new Exception(trans("comModulesAnalytics.error_retrieving_statistics_top_by_date"));
        }
        return $response->json()->data;

    }

    public static function getVoteStatisticsByParameter($voteEventKey, $parameterKey,$secondParameterKey,$thirdParameterKey,$ageInterval = null,$viewSubmitted = null)
    {
        $response = ONE::post([
          //  // 'url' => 'http://ilidio.empatia-dev.onesource.pt:5001',
            'component' => 'analytics',
            'api' => 'voteEvent',
            'api_attribute' => $voteEventKey,
            'method' => 'statisticsByParameter',
            'params' => [
                'parameter_key' => $parameterKey,
                'second_parameter_key' => $secondParameterKey,
                'third_parameter_key' => $thirdParameterKey,
                'age_value' => $ageInterval,
                'view_submitted' => $viewSubmitted
            ]
        ]);
        if($response->statusCode()!= 200){
            throw new Exception(trans("comModulesAnalytics.error_retrieving_vote_statistics_by_parameter"));
        }
        return $response->json()->data;
    }

    public static function getVoteStatisticsByTopicParameter($voteEventKey, $cbKey,$parameterId,$secondParameterKey,$thirdParameterKey,$ageInterval = null, $viewSubmitted = null)
    {
        $response = ONE::post([
            // 'url' => 'http://ilidio.empatia-dev.onesource.pt:5001',
            'component' => 'analytics',
            'api' => 'voteEvent',
            'api_attribute' => $voteEventKey,
            'method' => 'statisticsByTopicParameter',
            'params' => [
                'cb_key' => $cbKey,
                'parameter_id' => $parameterId,
                'second_parameter_key' => $secondParameterKey,
                'third_parameter_key' => $thirdParameterKey,
                'age_value' => $ageInterval,
                'view_submitted' => $viewSubmitted
            ]
        ]);

        if($response->statusCode()!= 200){
            throw new Exception(trans("comModulesAnalytics.error_retrieving_vote_statistics_by_topicParameter"));
        }
        return $response->json()->data;
    }

    public static function getVoteStatisticsByParameterChannel($voteEventKey, $parameterKey,$secondParameterKey,$thirdParameterKey,$ageInterval = null, $viewSubmitted = null)
    {
        $response = ONE::post([
            // 'url' => 'http://ilidio.empatia-dev.onesource.pt:5001',
            'component' => 'analytics',
            'api' => 'voteEvent',
            'api_attribute' => $voteEventKey,
            'method' => 'statisticsByParameterChannel',
            'params' => [
                'parameter_key' => $parameterKey,
                'second_parameter_key' => $secondParameterKey,
                'third_parameter_key' => $thirdParameterKey,
                'age_value' => $ageInterval,
                'view_submitted' => $viewSubmitted
            ]
        ]);
        if($response->statusCode()!= 200){
            throw new Exception(trans("comModulesAnalytics.error_retrieving_vote_statistics_by_parameter_channel"));
        }
        return $response->json()->data;
    }


    public static function getVoteStatisticsByTopicParameterChannel($voteEventKey,$cbKey ,$parameterId,$secondParameterKey,$thirdParameterKey,$ageInterval = null, $viewSubmitted = null)
    {
        $response = ONE::post([
            // 'url' => 'http://ilidio.empatia-dev.onesource.pt:5001',
            'component' => 'analytics',
            'api' => 'voteEvent',
            'api_attribute' => $voteEventKey,
            'method' => 'statisticsByTopicParameterChannel',
            'params' => [
                'cb_key' => $cbKey,
                'parameter_id' => $parameterId,
                'second_parameter_key' => $secondParameterKey,
                'third_parameter_key' => $thirdParameterKey,
                'age_value' => $ageInterval,
                'view_submitted' => $viewSubmitted
            ]
        ]);

        if($response->statusCode()!= 200){
            throw new Exception(trans("comModulesAnalytics.error_retrieving_vote_statistics_by_topic_parameter_channel"));
        }
        return $response->json()->data;
    }


    public static function getVoteStatisticsByParameterChannelDateRange($voteEventKey, $parameterKey,$options = null)
    {
        $response = ONE::post([
            // 'url' => 'http://ilidio.empatia-dev.onesource.pt:5001',
            'component' => 'analytics',
            'api' => 'voteEvent',
            'api_attribute' => $voteEventKey,
            'method' => 'voteStatisticsByParameterChannelDateRange',
            'params' => $options
        ]);

        if($response->statusCode()!= 200){
            throw new Exception(trans("comModulesAnalytics.error_retrieving_vote_statistics_by_parameter_channel_date_range"));
        }
        return $response->json()->data;
    }



    public static function getVoteStatisticsByTopicParameterChannelDateRange($voteEventKey ,$parameterId, $options = null)
    {
        $response = ONE::post([
            // 'url' => 'http://ilidio.empatia-dev.onesource.pt:5001',
            'component' => 'analytics',
            'api' => 'voteEvent',
            'api_attribute' => $voteEventKey,
            'method' => 'voteStatisticsByTopicParameterChannelDateRange',
            'params' => $options
        ]);

        if($response->statusCode()!= 200){
            throw new Exception(trans("comModulesAnalytics.error_retrieving_vote_statistics_by_topic_parameter_channel_date_range"));
        }
        return $response->json()->data;
    }



    public static function getVoteStatisticsByUser($voteEventKey, $options = null)
    {
        $response = ONE::post([
             // 'url' => 'http://ilidio.empatia-dev.onesource.pt:5001',
            'component' => 'analytics',
            'api' => 'voteEvent',
            'api_attribute' => $voteEventKey,
            'method' => 'getVoteStatisticsByUser',
            'params' => $options
        ]);

        // !is_null($response->json()) ? dd("remote/DD",$response->json()) : die("remote/ECHO" .$response->content());
        if($response->statusCode()!= 200){
            throw new Exception(trans("comModulesAnalytics.error_retrieving_vote_statistics_by_user"));
        }
        return $response->json()->data;
    }

    public static function getVoteStatisticsByTopic($voteEventKey, $options = null)
    {
        $response = ONE::post([
            // 'url' => 'http://ilidio.empatia-dev.onesource.pt:5001',
            'component' => 'analytics',
            'api' => 'voteEvent',
            'api_attribute' => $voteEventKey,
            'method' => 'getVoteStatisticsByTopic',
            'params' => $options
        ]);

        if($response->statusCode()!= 200){
            throw new Exception(trans("comModulesAnalytics.error_retrieving_vote_statistics_by_topic"));
        }
        return $response->json()->data;
    }


    public static function getVoteStatisticsLastDay($voteEventKey)
    {
        $response = ONE::post([
            'component' => 'analytics',
            'api' => 'voteEvent',
            'api_attribute' => $voteEventKey,
            'method' => 'statisticsLastDay'
        ]);

        if($response->statusCode()!= 200){
            throw new Exception(trans("comModulesAnalytics.error_retrieving_statistics_last_day"));
        }
        return $response->json()->data;
    }

    public static function getVoteStatisticsTotal($voteEventKey, $options = null)
    {
        $response = ONE::post([
//            'url' => 'http://luismonteiro.empatia-dev.onesource.pt:5001',
            'component' => 'analytics',
            'api'       => 'voteEvent',
            'api_attribute' => $voteEventKey,
            'method'    => 'votes',
            'params' => $options
        ]);

//       !is_null($response->json()) ? dd("remote/DD",$response->json()) : die("remote/ECHO" .$response->content());

        if($response->statusCode() != 200){
            throw new Exception("comModulesAnalytics.error_retrieving_total_statistics");
        }
        return $response->json();
    }

    public static function getVoteStatisticsTotalByChannel($voteEventKey, $options = null)
    {
        $response = ONE::post([
//             'url' => 'http://luismonteiro.empatia-dev.onesource.pt:5001',
            'component' => 'analytics',
            'api'       => 'voteEvent',
            'api_attribute' => $voteEventKey,
            'method'    => 'votesTotalByChannel',
            'params' => $options
        ]);
//        !is_null($response->json()) ? dd("remote/DD",$response->json()) : die("remote/ECHO" .$response->content());
        if($response->statusCode() != 200){
            throw new Exception("comModulesAnalytics.error_retrieving_total_statistics");
        }
        return $response->json();
    }

    /**
     * @param $voteEventKey
     * @return mixed
     * @throws Exception
     */
    public static function getVoteStatisticsByChannel($voteEventKey, $options = null)
    {
        $response = ONE::get([
            // 'url' => 'http://ilidio.empatia-dev.onesource.pt:5001',
            'component' => 'analytics',
            'api'       => 'voteEvent',
            'api_attribute' => $voteEventKey,
            'method'    => 'votesByChannel',
            'params' => $options
        ]);

        // !is_null($response->json()) ? dd("remote/DD",$response->json()) : die("remote/ECHO" .$response->content());

        if($response->statusCode() != 200){
            throw new Exception("comModulesAnalytics.error_retrieving_votes_by_channel");
        }
        return $response->json()->data;
    }

    /**
     * @param $voteEventKey
     * @return mixed
     * @throws Exception
     */
    public static function getVoterStatisticsByChannel($voteEventKey)
    {
        $response = ONE::get([
            'component' => 'analytics',
            'api'       => 'voteEvent',
            'api_attribute' => $voteEventKey,
            'method'    => 'votersByChannel',
        ]);

        if($response->statusCode() != 200){
            throw new Exception("comModulesAnalytics.error_retrieving_voters_by_channel");
        }
        return $response->json()->data;
    }

    /** Get votes summary
     * @param $voteEventKey
     * @return mixed
     * @throws Exception
     */
    public static function getVoteStatisticsVotesSummary($voteEventKey, $options = [])
    {
        $response = ONE::post([
            // 'url' => 'http://ilidio.empatia-dev.onesource.pt:5001',
            'component' => 'analytics',
            'api'       => 'voteEvent',
            'api_attribute' => $voteEventKey,
            'method'    => 'votesSummary',
            'params'=> $options
        ]);



        //!is_null($response->json()) ? dd("remote/DD",$response->json()) : die("remote/ECHO" .$response->content());

        if($response->statusCode() != 200){
            throw new Exception("comModulesAnalytics.error_retrieving_total_statistics");
        }
        return $response->json();


    }

    /** Get vote Statistics by different voters per day
     * @param $voteEventKey
     * @return mixed
     * @throws Exception
     */
    public static function getVoteStatisticsVotersPerDate($voteEventKey){

        $response = ONE::get([
            'component' => 'analytics',
            'api' => 'voteEvent',
            'api_attribute' => $voteEventKey,
            'method' => 'statisticsVotersPerDate'
        ]);
        if($response->statusCode()!= 200){
            throw new Exception(trans("comModulesAnalytics.error_retrieving_statistics_voters_per_date"));
        }
        return $response->json()->data;
    }
    

    /** Get top Topics
     * @param $voteEventKey
     * @param $top
     * @param $minVotes
     * @return mixed
     * @throws Exception
     */
    public static function getTopTopics($voteEventKey,$top,$minVotes,$cbKey = "")
    {

        $response = ONE::post([
            'component' => 'analytics',
            'api' => 'voteStatistics',
            'api_attribute' => $voteEventKey,
            'method' => 'topTopics',
            'params' => [
                'top_topics' => $top,
                'min_votes' => $minVotes,
                'cbKey' => $cbKey
            ]
        ]);
        if ($response->statusCode() != 200) {
            throw new Exception("comModulesAnalytics.error_retrieving_top_topics");
        }

        return $response->json()->data;
    }

    public static function getVoteStatisticsTopicParameters($voteEventKey, $paramId){
        $response = ONE::get([
            'component' => 'analytics',
            'api' => 'voteEvent',
            'api_attribute' => $voteEventKey,
            'method' => 'statisticsTopicParameters',
            'params' => [
                'paramId' => $paramId
            ]
        ]);
        if($response->statusCode()!= 200){
            throw new Exception(trans("comModulesAnalytics.error_retrieving_statistics_topic_parameters"));
        }
        return $response->json()->data;
    }

    public static function getEmapvilleSchools($voteEvents, $cbKey){
        $response = ONE::get([
            'component' => 'analytics',
            'api'       => 'voteEvent',
            'api_attribute' => $voteEvents[0]->vote_key,
            'method'    => 'empavilleSchools',
            'attribute' => $cbKey,
        ]);
        if($response->statusCode() != 200){
            throw new Exception("comModulesAnalytics.FailedToGetAnalytics.");
        }

        return $response->json();
    }

    public static function getVotes($voteSessionId, $cbId){
        $response = ONE::get([
            'component' => 'analytics',
            'api'       => 'voteEvent',
            'api_attribute' => $voteSessionId,
            'method'    => 'votes',
            'attribute' => $cbId,
        ]);

        if($response->statusCode() != 200){
            throw new Exception("comModulesAnalytics.FailedToGetVotes.");
        }
        return $response->json();
    }

    public static function getVotesDaily($voteSessionId, $cbId){
        $response = ONE::get([
            'component' => 'analytics',
            'api'       => 'voteEventDaily',
            'api_attribute' => $voteSessionId,
            'method'    => 'votes',
            'attribute' => $cbId,
        ]);

        if($response->statusCode() != 200){
            throw new Exception("comModulesAnalytics.FailedToGetVotesDaily.");
        }
        return $response;
    }

    public static function getVotesByGender($voteKey, $cbKey){
        $response = ONE::get([
            'component' => 'analytics',
            'api'       => 'voteEvent',
            'api_attribute' => $voteKey,
            'method'    => 'countByGender',
            'attribute' => $cbKey,
        ]);

        if($response->statusCode() != 200){
            throw new Exception("comModulesAnalytics.FailedToGetVotesByGender.");
        }
        return $response->json()->data;
    }

    public static function getVotesFirstByGender($voteKey, $cbKey){
        $response = ONE::get([
            'component' => 'analytics',
            'api'       => 'voteEvent',
            'api_attribute' => $voteKey,
            'method'    => 'firstByGender',
            'attribute' => $cbKey,
        ]);

        if($response->statusCode() != 200){
            throw new Exception("comModulesAnalytics.FailedToGetVotesFirstByGender.");
        }
        return $response->json()->data;
    }

    public static function getVotesSecondByGender($voteKey, $cbKey){
        $response = ONE::get([
            'component' => 'analytics',
            'api'       => 'voteEvent',
            'api_attribute' => $voteKey,
            'method'    => 'secondByGender',
            'attribute' => $cbKey,
        ]);

        if($response->statusCode() != 200){
            throw new Exception("comModulesAnalytics.FailedToGetVotesSecondByGender.");
        }
        return $response->json()->data;
    }

    public static function getVotesByProfession($voteKey, $cbKey){
        $response = ONE::get([
            'component' => 'analytics',
            'api'       => 'voteEvent',
            'api_attribute' => $voteKey,
            'method'    => 'votesByProfession',
            'attribute' => $cbKey,
        ]);

        if($response->statusCode() != 200){
            throw new Exception("comModulesAnalytics.FailedToGetCountVotesByProfession.");
        }
        return $response->json();
    }

    public static function getCountVotesByProfession($voteKey, $cbKey){
        $response = ONE::get([
            'component' => 'analytics',
            'api'       => 'voteEvent',
            'api_attribute' => $voteKey,
            'method'    => 'countByProfession',
            'attribute' => $cbKey,
        ]);

        if($response->statusCode() != 200){
            throw new Exception("comModulesAnalytics.FailedToGetCountVotesByProfession.");
        }
        return $response->json()->data;
    }

    public static function getVotesFirstByProfession($voteKey, $cbKey){
        $response = ONE::get([
            'component' => 'analytics',
            'api'       => 'voteEvent',
            'api_attribute' => $voteKey,
            'method'    => 'firstByProfession',
            'attribute' => $cbKey,
        ]);

        if($response->statusCode() != 200){
            throw new Exception("comModulesAnalytics.FailedToGetVotesFirstByProfession.");
        }
        return $response->json()->data;
    }

    public static function getVotesSecondByProfession($voteKey, $cbKey){
        $response = ONE::get([
            'component' => 'analytics',
            'api'       => 'voteEvent',
            'api_attribute' => $voteKey,
            'method'    => 'secondByProfession',
            'attribute' => $cbKey,
        ]);

        if($response->statusCode() != 200){
            throw new Exception("comModulesAnalytics.FailedToGetVotesSecondByProfession.");
        }
        return $response->json()->data;
    }

    public static function getVotesByNeighbourhood($voteKey, $cbKey){
        $response = ONE::get([
            'component' => 'analytics',
            'api'       => 'voteEvent',
            'api_attribute' => $voteKey,
            'method'    => 'votesByNb',
            'attribute' => $cbKey,
        ]);

        if($response->statusCode() != 200){
            throw new Exception("comModulesAnalytics.FailedToGetVotesByNeighbourhood.");
        }
        return $response->json()->data;
    }

    public static function getCountVotesByNeighbourhood($voteKey, $cbKey){
        $response = ONE::get([
            'component' => 'analytics',
            'api'       => 'voteEvent',
            'api_attribute' => $voteKey,
            'method'    => 'countByNb',
            'attribute' => $cbKey,
        ]);

        if($response->statusCode() != 200){
            throw new Exception("comModulesAnalytics.FailedToGetVotesByNeighbourhood.");
        }
        return $response->json()->data;
    }

    public static function getVotesFirstByNeighbourhood($voteKey, $cbKey){
        $response = ONE::get([
            'component' => 'analytics',
            'api'       => 'voteEvent',
            'api_attribute' => $voteKey,
            'method'    => 'firstByNb',
            'attribute' => $cbKey,
        ]);

        if($response->statusCode() != 200){
            throw new Exception("comModulesAnalytics.FailedToGetVotesFirstByNeighbourhood.");
        }
        return $response->json()->data;
    }

    public static function getVotesSecondByNeighbourhood($voteKey, $cbKey){
        $response = ONE::get([
            'component' => 'analytics',
            'api'       => 'voteEvent',
            'api_attribute' => $voteKey,
            'method'    => 'secondByNb',
            'attribute' => $cbKey,
        ]);

        if($response->statusCode() != 200){
            throw new Exception("comModulesAnalytics.FailedToGetVotesSecondByNeighbourhood.");
        }
        return $response->json()->data;
    }

    public static function getCountVotesByAge($voteKey, $cbKey){
        $response = ONE::get([
            'component' => 'analytics',
            'api'       => 'voteEvent',
            'api_attribute' => $voteKey,
            'method'    => 'countByAge',
            'attribute' => $cbKey,
        ]);

        if($response->statusCode() != 200){
            throw new Exception("comModulesAnalytics.FailedToGetCountVotesByAge.");
        }
        return $response->json()->data;
    }

    public static function getVotesFirstByAge($voteKey, $cbKey){
        $response = ONE::get([
            'component' => 'analytics',
            'api'       => 'voteEvent',
            'api_attribute' => $voteKey,
            'method'    => 'firstByAge',
            'attribute' => $cbKey,
        ]);

        if($response->statusCode() != 200){
            throw new Exception("comModulesAnalytics.FailedToGetVotesFirstByAge.");
        }
        return $response->json()->data;
    }

    public static function getVotesSecondByAge($voteKey, $cbKey){
        $response = ONE::get([
            'component' => 'analytics',
            'api'       => 'voteEvent',
            'api_attribute' => $voteKey,
            'method'    => 'secondByAge',
            'attribute' => $cbKey,
        ]);

        if($response->statusCode() != 200){
            throw new Exception("comModulesAnalytics.FailedToGetVotesSecondByAge.");
        }
        return $response->json()->data;
    }

    public static function getVotesByChannel($voteKey, $cbKey){
        $response = ONE::get([
            'component' => 'analytics',
            'api'       => 'voteEvent',
            'api_attribute' => $voteKey,
            'method'    => 'votesByChannel',
            'attribute' => $cbKey,
        ]);

        if($response->statusCode() != 200){
            throw new Exception("comModulesAnalytics.FailedToGetVotesByChannel.");
        }
        return $response->json()->data;
    }

    public static function getCountVotesByChannel($voteKey, $cbKey){
        $response = ONE::get([
            'component' => 'analytics',
            'api'       => 'voteEvent',
            'api_attribute' => $voteKey,
            'method'    => 'countByChannel',
            'attribute' => $cbKey,
        ]);

        if($response->statusCode() != 200){
            throw new Exception("comModulesAnalytics.FailedToGetCountVotesByChannel.");
        }
        return $response->json()->data;
    }

    public static function getVotesFirstByChannel($voteKey, $cbKey){
        $response = ONE::get([
            'component' => 'analytics',
            'api'       => 'voteEvent',
            'api_attribute' => $voteKey,
            'method'    => 'firstByChannel',
            'attribute' => $cbKey,
        ]);

        if($response->statusCode() != 200){
            throw new Exception("comModulesAnalytics.FailedToGetVotesFirstByChannel.");
        }
        return $response->json()->data;
    }

    public static function getVotesSecondByChannel($voteKey, $cbKey){
        $response = ONE::get([
            'component' => 'analytics',
            'api'       => 'voteEvent',
            'api_attribute' => $voteKey,
            'method'    => 'secondByChannel',
            'attribute' => $cbKey,
        ]);

        if($response->statusCode() != 200){
            throw new Exception("comModulesAnalytics.FailedToGetVotesSecondByChannel.");
        }
        return $response->json()->data;
    }
}