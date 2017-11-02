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

    public static function getVoteStatisticsByParameter($voteEventKey, $parameterKey,$secondParameterKey,$thirdParameterKey,$ageInterval = null)
    {

        $response = ONE::post([
            'component' => 'analytics',
            'api' => 'voteEvent',
            'api_attribute' => $voteEventKey,
            'method' => 'statisticsByParameter',
            'params' => [
                'parameter_key' => $parameterKey,
                'second_parameter_key' => $secondParameterKey,
                'third_parameter_key' => $thirdParameterKey,
                'age_value' => $ageInterval
            ]
        ]);

        if($response->statusCode()!= 200){
            throw new Exception(trans("comModulesAnalytics.error_retrieving_statistics_by_parameter"));
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

    public static function getVoteStatisticsTotal($voteEventKey,$top)
    {

        $response = ONE::post([
            'component' => 'analytics',
            'api'       => 'voteEvent',
            'api_attribute' => $voteEventKey,
            'method'    => 'votes',
            'params' => [
                'top' => $top
            ]
        ]);
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
    public static function getVoteStatisticsByChannel($voteEventKey)
    {
        $response = ONE::get([
            'component' => 'analytics',
            'api'       => 'voteEvent',
            'api_attribute' => $voteEventKey,
            'method'    => 'votesByChannel',
        ]);
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
    public static function getVoteStatisticsVotesSummary($voteEventKey)
    {
        $response = ONE::post([
            'component' => 'analytics',
            'api'       => 'voteEvent',
            'api_attribute' => $voteEventKey,
            'method'    => 'votesSummary',
        ]);
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