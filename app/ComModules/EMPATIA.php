<?php

namespace App\ComModules;

use App\One\One;
use Exception;

class EMPATIA {

    public static function getAccountRecoveryParametersForForm() {
        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'accountRecovery',
            'api_attribute'=> 'getParametersForForm'
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesEMPATIA.failed_to_get_account_recovery_parameters_for_form"));
        }
        return $response->json();
    }
    public static function validateAccountRecoveryRequest($formData) {
        $response = ONE::post([
            'component' => 'empatia',
            'api'       => 'accountRecovery',
            'api_attribute'    => 'validate',
            'params' => $formData
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesEMPATIA.failed_to_validate_account_recovery_request"));
        }
        return $response->json();
    }
    public static function recoverAccount($formData) {
        $response = ONE::post([
            'component' => 'empatia',
            'api'       => 'accountRecovery',
            'api_attribute'    => 'recover',
            'params' => $formData
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesEMPATIA.failed_to_recover_account"));
        }
        return $response->json();
    }
    public static function entityHasAccountRecovery() {
        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'accountRecovery',
            'api_attribute'    => 'isActive'
        ]);

        if($response->statusCode()!= 200) {
            return false;
        }
        return $response->json();
    }

    public static function getNewsletterSubscriptions($request) {
        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'newsletterSubscription',
            'api_attribute' => 'list',
            'params' => [
                'tableData' => One::tableData($request),
            ]
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesEMPATIA.failed_to_get_newsletter_subscriptions"));
        }
        return $response->json();
    }
    public static function getNewsletterSubscriptionsToExport() {
        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'newsletterSubscription',
            'api_attribute' => 'export'
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesEMPATIA.failed_to_get_newsletter_subscriptions_to_export"));
        }
        return $response->json();
    }
    public static function getNewsletterSubscription($newsletterSubscriptionKey) {
        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'newsletterSubscription',
            'api_attribute' => $newsletterSubscriptionKey
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesEMPATIA.failed_to_get_newsletter_subscription"));
        }
        return $response->json();
    }

    public static function getAllMessages($request, $userKey){
        $response = One::get([
            'component' => 'empatia',
            'api' => 'entityMessages',
            'params' => [
                'user_key' => $userKey,
                'tableData' => One::tableData($request),
            ]
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesEMPATIA.failed_to_get_all_messages"));
        }
        return $response->json();
    }

    public static function getEntityMessages($filters, $request = null){
        $response = One::get([
            'component' => 'empatia',
            'api' => 'entityMessages',
            'method' => 'getEntityMessages',
            'params' => [
                'filters' => $filters,
                'tableData' => One::tableData($request),
            ]
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesEMPATIA.failed_to_get_entity_messages"));
        }
        return $response->json();
    }

    /**
     * @return mixed
     * @throws Exception
     */
    public static function getNotificationTypes(){
        $response = One::get([
            'component' => 'empatia',
            'api'       => 'entity',
            'method'    => 'getNotificationTypes',
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesEMPATIA.failed_to_get_notification_types"));
        }
        return $response->json()->data;
    }

    /**
     * @return mixed
     * @throws Exception
     */
    public static function getEntityNotifications(){
        $response = One::get([
            'component' => 'empatia',
            'api'       => 'entity',
            'method'    => 'getEntityNotifications',
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesEMPATIA.failed_to_get_entity_notifications"));
        }
        return $response->json()->data;
    }

    /**
     * @param $entityNotificationTypeCodes
     * @param $groups
     * @return mixed
     * @throws Exception
     */
    public static function setEntityNotifications($entityNotificationTypeCodes, $groups){
        $response = One::post([
            'component' => 'empatia',
            'api'       => 'entity',
            'method'    => 'setEntityNotifications',
            'params' => [
                'entity_notification_type_codes' => $entityNotificationTypeCodes,
                'groups' => $groups
            ]
        ]);

        if($response->statusCode()!= 201) {
            throw new Exception(trans("comModulesEMPATIA.failed_to_update_entity_notifications"));
        }
        return $response->json();
    }

    /**
     * @param $notificationCode
     * @param $translations
     * @param $siteKey
     * @return mixed
     * @throws Exception
     */
    public static function setEntityNotificationTemplate($notificationCode, $translations, $siteKey){
        $response = One::post([
            'component' => 'empatia',
            'api'       => 'entity',
            'method'    => 'setEntityNotificationTemplate',
            'params'    => [
                'site_key'          => $siteKey,
                'translations'      => $translations,
                'notification_code' => $notificationCode,
            ]
        ]);

        if($response->statusCode()!= 201) {
            throw new Exception(trans("comModulesEMPATIA.failed_to_create_entity_notification_template"));
        }
        return $response->json();
    }

    /**
     * @param $templateKey
     * @return mixed
     * @throws Exception
     */
    public static function getEntityNotificationTemplate($templateKey)
    {
        $response = One::get([
            'component'     => 'empatia',
            'api'           => 'entity',
            'method'        => 'getEntityNotificationTemplate',
            'attribute' => $templateKey,
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesEMPATIA.failed_to_get_entity_notifications"));
        }
        return $response->json();
    }

    /**
     * @param $templateKey
     * @param $translations
     * @return mixed
     * @throws Exception
     */
    public static function updateEntityNotificationTemplate($templateKey, $translations)
    {
        $response = One::post([
            'component' => 'empatia',
            'api'       => 'entity',
            'method'    => 'updateEntityNotificationTemplate',
            'attribute' => $templateKey,
            'params'    => [
                'translations'  => $translations,
            ]
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesEMPATIA.failed_to_get_entity_notifications"));
        }
        return $response->json();
    }

    /**
     * @param $entityKey
     * @return mixed
     * @throws Exception
     */
    public static function manualUpdateTopicVotesInfo($entityKey) {
        $response = One::put([
            'component' => 'empatia',
            'api'       => 'entity',
            'method'    => 'manualUpdateTopicVotesInfo',
            'api_attribute' => $entityKey,
        ]);


        if ($response->statusCode() != 200){
            throw new Exception(trans("comModulesVote.errorManualUpdateTopicVotesInfo"));
        }
        return $response;
    }

    /**
     * @param $userAnalysisData
     * @return mixed
     * @throws Exception
     */
    public static function getUserAnalysis($userAnalysisData){
        $response = ONE::post([
            'component' => 'empatia',
            'api'       => 'userAnalysis',
            'method'    => 'getUserAnalysis',
            'params'    => [
                'logged_data' => $userAnalysisData
            ]
        ]);
        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesEMPATIA.failed_to_get_user_analysis"));
        }
        return $response->json();
    }


    /**
     * @param $data
     * @return mixed
     * @throws Exception
     */
    public static function storeUserAnalytics($data){
        $response = ONE::post([
            'component' => 'empatia',
            'api'       => 'userAnalysis',
            'params'    => [
                'data' => $data
            ]
        ]);

        if($response->statusCode()!= 201) {
            throw new Exception(trans("comModulesEMPATIA.failed_to_store_analysis_data"));
        }
        return $response->json();
    }


    /**
     * @param $params
     * @return mixed
     * @throws Exception
     */
    public static function getUserAnalysisStats($params = []){
        $response = ONE::post([
            'component' => 'empatia',
            'api'       => 'userAnalysis',
            'method'    => 'getUserAnalysisStats',
            'params'    => $params
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesEMPATIA.failed_to_get_user_analysis_stats"));
        }

        return $response->json();
    }

    /**
     * @param $userKeys
     * @param $questionnaireKey
     * @return mixed
     * @throws Exception
     */
    public static function generateUniqueKey($userKeys, $questionnaireKey){
        $response = ONE::post([
            'component' => 'empatia',
            'api'       => 'user',
            'method'    => 'generateUniqueKey',
            'params'    => [
                'user_keys' => $userKeys,
                'questionnaire_key' => $questionnaireKey
            ]
        ]);

        if($response->statusCode()!= 201) {
            throw new Exception(trans("comModulesEMPATIA.failed_to_generate_unique_key"));
        }
        return $response->json();
    }


    /**
     * @param $questionnaireKey
     * @param $userKey
     * @param $uniqueKey
     * @return mixed
     * @throws Exception
     */
    public static function verifyUniqueKey($questionnaireKey, $userKey, $uniqueKey){
        $response = One::post([
            'component' => 'empatia',
            'api'       => 'user',
            'method'    => 'verifyUniqueKey',
            'params'    => [
                'user_key' => $userKey,
                'questionnaire_key' => $questionnaireKey,
                'unique_key' => $uniqueKey,
            ]
        ]);

        if($response->statusCode()!= 200) {
            if ($response->statusCode() == 409){
                throw new Exception(trans("comModulesEMPATIA.url_already_used"));
            }
            throw new Exception(trans("comModulesEMPATIA.failed_to_verify_unique_key"));
        }
        return $response->json();
    }

    /**
     *
     * CB Operation Action Related
     *
     */

    public static function listOperationActions(){
        $response = One::get([
            'component' => 'empatia',
            'api'       => 'operationActions'
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesEMPATIA.failed_to_list_operation_actions"));
        }
        return $response->json();
    }

    /**
     * @param $data
     * @return
     * @throws Exception
     */

    public static function storeOperationAction($data){
        $response = One::post([
            'component' => 'empatia',
            'api'       => 'operationActions',
            'params'    => [
                'code' => $data->code,
                'name' => $data->name ?? null,
                'description' => $data->description ?? null,
            ]
        ]);

        if($response->statusCode()!= 201) {
            throw new Exception(trans("comModulesEMPATIA.failed_to_store_operation_action"));
        }
        return $response->json();
    }

    /**
     * @param $code
     * @return mixed
     * @throws Exception
     */
    public static function getOperationAction($code){
        $response = One::get([
            'component' => 'empatia',
            'api'       => 'operationActions',
            'api_attribute' => $code
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesEMPATIA.failed_to_get_operation_action"));
        }
        return $response->json();
    }

    /**
     * @param $data
     * @return mixed
     * @throws Exception
     */
    public static function updateOperationAction($data){
        $response = One::put([
            'component' => 'empatia',
            'api'       => 'operationActions',
            'api_attribute' => $data->code,
            'params'    => [
                'code' => $data->code,
                'name' => $data->name ?? null,
                'description' => $data->description ?? null,
            ]
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesEMPATIA.failed_to_update_operation_action"));
        }
        return $response->json();
    }

    /**
     * @param $code
     * @return mixed
     * @throws Exception
     */
    public static function deleteOperationAction($code){
        $response = ONE::delete([
            'component' => 'empatia',
            'api'       => 'operationActions',
            "api_attribute" => $code,
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesEMPATIA.failed_to_delete_operation_action"));
        }
        return $response->json();
    }

    /**
     *
     * CB Operation Type Related
     *
     */

    public static function listOperationTypes(){
        $response = One::get([
            'component' => 'empatia',
            'api'       => 'operationTypes'
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesEMPATIA.failed_to_list_operation_types"));
        }
        return $response->json();
    }

    /**
     * @param $data
     * @return
     * @throws Exception
     */

    public static function storeOperationType($data){
        $response = One::post([
            'component' => 'empatia',
            'api'       => 'operationTypes',
            'params'    => [
                'code' => $data->code,
                'name' => $data->name ?? null,
                'description' => $data->description ?? null,
            ]
        ]);

        if($response->statusCode()!= 201) {
            throw new Exception(trans("comModulesEMPATIA.failed_to_store_operation_type"));
        }
        return $response->json();
    }

    /**
     * @param $code
     * @return mixed
     * @throws Exception
     */
    public static function getOperationType($code){
        $response = One::get([
            'component' => 'empatia',
            'api'       => 'operationTypes',
            'api_attribute' => $code
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesEMPATIA.failed_to_get_operation_type"));
        }
        return $response->json();
    }

    /**
     * @param $data
     * @return mixed
     * @throws Exception
     */
    public static function updateOperationType($data){
        $response = One::put([
            'component' => 'empatia',
            'api'       => 'operationTypes',
            'api_attribute' => $data->code,
            'params'    => [
                'code' => $data->code,
                'name' => $data->name ?? null,
                'description' => $data->description ?? null,
            ]
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesEMPATIA.failed_to_update_operation_type"));
        }
        return $response->json();
    }

    /**
     * @param $code
     * @return mixed
     * @throws Exception
     */
    public static function deleteOperationType($code){
        $response = ONE::delete([
            'component' => 'empatia',
            'api'       => 'operationTypes',
            "api_attribute" => $code,
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesEMPATIA.failed_to_delete_operation_type"));
        }
        return $response->json();
    }

    /**
     *
     * CB Operation Schedule Related
     * @param $data
     * @return
     * @throws Exception
     */

    public static function storeCbOperationSchedule($data){
        $response = One::post([
            'component' => 'empatia',
            'api'       => 'cbOperationSchedules',
            'params'    => [
                'cb_key'                => $data['cb_key'],
                'active'                => $data['active'] ?? 0,
                'end_date'              => $data['end_date'] ?? null,
                'start_date'            => $data['start_date'] ?? null,
                'operation_type_code'   => $data['operation_type_code'],
                'operation_action_code' => $data['operation_action_code'],
            ]
        ]);

        if($response->statusCode()!= 201) {
            throw new Exception(trans("comModulesEMPATIA.failed_to_store_cb_operation_schedule"));
        }
        return $response->json();
    }

    /**
     * @param $cbOperationScheduleKey
     * @return mixed
     * @throws Exception
     */
    public static function getCbOperationSchedule($cbOperationScheduleKey){
        $response = One::get([
            'component' => 'empatia',
            'api'       => 'cbOperationSchedules',
            'api_attribute' => $cbOperationScheduleKey
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesEMPATIA.failed_to_get_cb_operation_schedule"));
        }
        return $response->json();
    }

    /**
     * @param $cbOperationScheduleKey
     * @param null $data
     * @return mixed
     * @throws Exception
     */
    public static function updateCbOperationSchedule($cbOperationScheduleKey, $data = null){
        $response = One::put([
            'component' => 'empatia',
            'api'       => 'cbOperationSchedules',
            'api_attribute' => $cbOperationScheduleKey,
            'params'    => [
                'active'        => $data['active'] ?? 0,
                'end_date'      => $data['end_date'] ?? null,
                'start_date'    => $data['start_date'] ?? null
            ]
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesEMPATIA.failed_to_update_cb_operation_schedule"));
        }
        return $response->json();
    }

    /**
     * @param $cbOperationScheduleKey
     * @return mixed
     * @throws Exception
     */
    public static function deleteCbOperationSchedule($cbOperationScheduleKey){
        $response = One::put([
            'component' => 'empatia',
            'api'       => 'cbOperationSchedules',
            'api_attribute' => $cbOperationScheduleKey
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesEMPATIA.failed_to_delete_cb_operation_schedule"));
        }
        return $response->json();
    }


    /**
     * @param $cbKey
     * @return mixed
     * @throws Exception
     */
    public static function getCbOperationScheduleGroup($cbKey){
        $response = One::get([
            'component' => 'empatia',
            'api'       => 'cbOperationSchedules',
            'method'    => 'getCbSchedules',
            'attribute' => $cbKey,
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesEMPATIA.failed_to_update_cb_operation_schedule_group"));
        }
        return $response->json();
    }

    /**
     * @param $cbKey
     * @param $operationTypeCode
     * @param $operationsActionCode
     * @return mixed
     * @throws Exception
     */
    public static function verifyCbOperationSchedule($cbKey, $operationTypeCode, $operationsActionCode){

        $response = One::post([
            'component' => 'empatia',
            'api'       => 'cbOperationSchedules',
            'method'    => 'verifyScheduleExternal',
            'params'    => [
                'cb_key'                => $cbKey,
                'operation_type_code'   => $operationTypeCode,
                'operation_action_code' => $operationsActionCode,
            ]
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesEMPATIA.failed_to_verify_cb_operation_schedule"));
        }
        return $response->json();
    }


    /* Short Links */
    public static function getShortLinks($request = null) {
        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'shortLinks',
            'method'    => 'list',
            'params' => [
                'tableData' => One::tableData($request),
            ]
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesEMPATIA.failed_to_retreive_short_links"));
        }
        return $response->json();
    }
    public static function getShortLink($shortLinkKey) {
        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'shortLinks',
            'method'    => $shortLinkKey
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesEMPATIA.failed_to_retreive_short_link"));
        }
        return $response->json();
    }
    public static function storeShortLink($data) {
        $response = ONE::post([
            'component' => 'empatia',
            'api'       => 'shortLinks',
            'params'    => $data
        ]);

        if($response->statusCode()!= 201) {
            if ($response->statusCode()==400)
                throw new Exception(trans("comModulesEMPATIA.existing_short_link_code"));

            throw new Exception(trans("comModulesEMPATIA.failed_to_store_short_link"));
        }
        return $response->json();
    }
    public static function updateShortLink($shortLinkKey, $data) {
        $response = ONE::put([
            'component' => 'empatia',
            'api'       => 'shortLinks',
            'method'    => $shortLinkKey,
            'params'    => $data
        ]);

        if($response->statusCode()!= 201) {
            throw new Exception(trans("comModulesEMPATIA.failed_to_update_short_link"));
        }
        return $response->json();
    }
    public static function deleteShortLink($shortLinkKey) {
        $response = ONE::delete([
            'component' => 'empatia',
            'api'       => 'shortLinks',
            'method'    => $shortLinkKey
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesEMPATIA.failed_to_delete_short_link"));
        }
        return $response->json();
    }
    public static function resolveShortLink($shortLinkCode) {
        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'shortLinks',
            'method'    => 'resolve',
            'attribute' => $shortLinkCode
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesEMPATIA.failed_to_resolve_short_link"));
        }
        return $response->json();
    }

    /**
     * @param $coopToken
     * @return mixed
     * @throws Exception
     */
    public static function verifyCoopToken($coopToken)
    {
        $response = One::get([
            'component'     => 'empatia',
            'api'           => 'topic',
            'method'        => 'cooperators/verifyCoopToken',
            'attribute'     => $coopToken,
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesEMPATIA.failed_to_verify_cooperation_token"));
        }
        return $response->json();
    }

    /**
     * @param $coopToken
     * @param $decision
     * @return mixed
     * @throws Exception
     */
    public static function updateCooperationStatus($coopToken, $decision)
    {
        $response = One::put([
            'component'     => 'empatia',
            'api'           => 'topic',
            'method'        => 'updateCoopStatus',
            'params'        => [
                'coopToken' => $coopToken,
                'decision'  => $decision
            ]
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesEMPATIA.failed_to_update_cooperation_status"));
        }
        return $response->json();
    }

    public static function operationSchedules($cbKey)
    {
        $response = One::get([
            'component'     => 'empatia',
            'api'           => 'cbOperationSchedules',
            'method'        => 'operationSchedules',
            'attribute'     => $cbKey,
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesEMPATIA.failed_to_get_operation_schedules"));
        }
        return $response->json();
    }

    public static function getUserTopicsCount($userKeys) {
        if (is_string($userKeys))
            $userKeys = array($userKeys);

        $response = One::post([
            'component'     => 'empatia',
            'api'           => 'user',
            'method'        => 'topicsCount',
            'params'        => [
                "userKeys" => $userKeys
            ]
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesEMPATIA.failed_to_get_user_topics_count"));
        }
        return $response->json();
    }

    public static function getEntityVoteEvents() {
        $response = One::get([
            'component'     => 'empatia',
            'api'           => 'entity',
            'method'        => 'entityVoteEvents'
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesEMPATIA.failed_to_Get_entity_vote_events"));
        }
        return $response->json();
    }

    public static function getTopicsKeysForVoteEvents($voteEventKeys) {
        if (is_string($voteEventKeys))
            $voteEventKeys = array($voteEventKeys);

        $response = One::post([
            'component'     => 'empatia',
            'api'           => 'vote',
            'method'        => 'getTopicsKeysForVoteEvents',
            'params'        => [
                'voteKeys' => $voteEventKeys
            ]
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesEMPATIA.failed_to_Get_entity_vote_events"));
        }
        return $response->json();
    }

    public static function anonymizeUsers($userKeys) {
        if (is_string($userKeys))
            $userKeys = array($userKeys);

        $response = One::delete([
            'component'     => 'empatia',
            'api'           => 'users',
            'method'        => 'anonymize',
            'params'        => [
                "userKeys" => $userKeys
            ]
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesEMPATIA.failed_to_anonymize_users"));
        }
        return $response->json();
    }
    public static function getTranslation($code, $language_code, $site_key=null, $cb_key=null){
        $response = One::get([
            'component'     => 'empatia',
            'api'           => 'translation',
            'method'        => 'getTranslation',
            'params'        => [
                "code" => $code,
                "lang_code" => $language_code,
                "cb_key"=> $cb_key,
                "site_key"=> $site_key,
            ]
        ]);

        if($response->statusCode()!= 200) {
            return null;
        }
        return $response->json();
    }
    /**
     * @return array with all sites per entity
     * @throws Exception
     */
    public static function getEntitiesSites()
    {
        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'site',
            'method'    => 'getEntitiesSites',
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesEMPATIA.errorGetEntitiesSites"));
        }
        return $response->json();
    }


    /**
     * Display a listing of the Cb check list.
     * @return \Illuminate\Http\Response
     * @throws Exception
     */
    public static function getCbChecklist($entity_key = null,$cb_Key = null)
    {
        $response = ONE::get([
            'component'   => 'empatia',
            'api'     => 'CbChecklists',
            'method'  => 'list',
            'params'  => [
                "entity_key" => $entity_key,
                "cb_key"     => $cb_Key,
            ]
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesEMPATIA.failedToGetCheckList"));
        }

        return $response->json()->data;
    }

    public static function updateChecklistItem($request)
    {
        $response = ONE::put([
            'component' => 'empatia',
            'api' => 'CbChecklists',
            'api_attribute' => $request->checklist_key,
            'params' => [
                $request->all()
            ]
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesEMPATIA.failedToUpdateChecklistItem"));
        }

        return $response->json()->data;
    }

    public static function createChecklistItem($request)
    {

        $response = ONE::post([
            'component' => 'empatia',
            'api' => 'CbChecklists',
            'params' => [
                "text"      => $request->text,
                "checked"   => $request->checked,
                "state"     => $request->state,
                "cbKey"     => $request->cbKey,
                "entityKey" => $request->entityKey,
            ]
        ]);
        if($response->statusCode() != 200){

            throw new Exception(trans("comModulesEMPATIA.failedCreateChecklistItem"));
        }

        return $response->json()->data;
    }

    public static function removeCheckListItem($checklist_Key)
    {
        $response = ONE::delete([
            'component' => 'empatia',
            'api'       => 'CbChecklists',
            'method'    => $checklist_Key,

        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesEMPATIA.failedToRemoveCheckListItem"));
        }

        return $response->json()->data;

    }
    public static function uploadFileTranslations($file,$cbKey,$siteKey)
    {
        $response = ONE::post([
            'component' => 'empatia',
            'api'       => 'translation',
            'method'    => 'uploadFileTranslation',
            'params'    => [
                'file'    => $file,
                'cbKey'  => $cbKey,
                'siteKey'=> $siteKey,
            ]
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesEMPATIA.errorUploadFileTranslations"));
        }
        return $response->json();
    }

/* OpenData ComModules */
    public static function getExistingOpenDatas() {
        $response = One::get([
            'component'     => 'empatia',
            'api'           => 'openData'
        ]);
    
        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesEMPATIA.failed_to_get_existing_open_data"));
        }
        return $response->json();
    }
    public static function getOpenDataConfigurations($entityKey) {
        $response = One::get([
            'component'     => 'empatia',
            'api'           => 'openData',
            'method'        => $entityKey
        ]);
    
        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesEMPATIA.failed_to_get_open_data_configurations"));
        }
        return $response->json();
    }
    
    public static function updateOpenDataConfigurations($entityKey, $userParameters, $cbsData) {
        $response = One::put([
            'component'     => 'empatia',
            'api'           => 'openData',
            'method'        => $entityKey,
            'params'        => [
                "user_parameters" => $userParameters,
                "cbs"             => $cbsData
            ]
        ]);
    
        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesEMPATIA.failed_to_update_open_data_configurations"));
        }
        return $response->json();
    }

    public static function exportOpenData($token, $type = null) {
        $response = One::post([
            'component'     => 'empatia',
            'api'           => 'openData',
            'method'        => 'export',
            'attribute'     => $token,
            'params'        => [
                "type"  => $type
            ]
        ]);
    
        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesEMPATIA.failed_to_export_open_data"));
        }
        return $response->json();
    }

    /* End of OpenData ComModules */



    /* Permissions - users/groups/cbs*/
    /**
     * @param $userKey
     * @return array with all menus and array with user permissions
     * @throws Exception
     */
    public static function getPermissions($userKey,$entityKey)
    {
        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'allPermissions',
            'method'    => 'permissions',
            'params'  => [
                "user_key" => $userKey,
                "entity_Key"=>$entityKey,
            ]
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesEMPATIA.errorGetMenusPermissions"));
        }
        return $response->json()->data;
    }


    /**
     * @param $code,$userId,$permission
     * @throws Exception
     */
    public static function updatePermissions($code,$userId,$permission,$entityKey)
    {
        $response = ONE::put([
            'component' => 'empatia',
            'api'       => 'allPermissions',
            'api_attribute' => $userId,
            'params'  => [
                "code" => $code,
                "permission" => $permission,
                "entity_Key" =>$entityKey,
            ]
        ]);

        if($response->statusCode() != 200){

            throw new Exception(trans("comModulesEMPATIA.errorUpdateMenusPermissions"));
        }
        return $response->json();
    }

    /**
     * @param $entityGroupKey
     * @return array with all groups permissions and array with user permissions
     * @throws Exception
     */
    public static function getGroupsPermissions($entityGroupKey,$entityKey)
    {
        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'allPermissions',
            'method'    => 'entityGroupsPermissions',
            'params'  => [
                "entityGroupKey" => $entityGroupKey,
                "entity_Key"      =>$entityKey,
            ]
        ]);
        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesEMPATIA.errorGetGroupsPermissions"));
        }
        return $response->json()->data;
    }


    /**
     * @param $code,$entityGroupId,$permission,$entityKey
     * @throws Exception
     */
    public static function updateGroupPermission($code,$entityGroupId,$permission,$entityKey)
    {
        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'allPermissions',
            'method'    => 'updateGroupPermission',
            'params'  => [
                "entityGroupId" =>$entityGroupId,
                "code" => $code,
                "permission" => $permission,
                "entity_Key" =>$entityKey,
            ]
        ]);
        if($response->statusCode() != 200){

            throw new Exception(trans("comModulesEMPATIA.errorUpdateGroupsPermissions"));
        }
        return $response->json();
    }

    /**
     * @param
     * @return array with all user permissions (menus, groups, cbs)
     * @throws Exception
     */
    public static function getUserPermissions($user,$entityKey)
    {
        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'allPermissions',
            'method'    => 'userPermission',
            'params'  => [
                "user" =>$user,
                "entity_Key" =>$entityKey,
            ]
        ]);
        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesEMPATIA.errorGetUserPermissions"));
        }
        return $response->json();
    }


    /**
     * @param $entityGroupKey
     * @return array with all groups permissions and array with user permissions
     * @throws Exception
     */
    public static function getCBPermissions($cbKey,$groupKey,$userKey,$entityKey)
    {
        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'allPermissions',
            'method'    => 'cbPermissions',
            'params'  => [
                'cbKey' => $cbKey,
                'groupKey' => $groupKey,
                'userKey' => $userKey,
                "entityKey" =>$entityKey,
            ]
        ]);
        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesEMPATIA.errorGetCbsPermissions"));
        }
        return $response->json()->data;
    }
    /**
     * @param $code,$userId,$groupId,$permission,$entityKey
     * @throws Exception
     */
    public static function updateCBPermissions($cbKey,$code,$userId,$groupId,$permission,$entityKey)
    {
        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'allPermissions',
            'method'    => 'updateCbPermissions',
            'params'  => [
                "cbKey" =>$cbKey,
                "code" => $code,
                "permission" => $permission,
                "entity_Key" => $entityKey,
                "userId" => $userId,
                "groupId" => $groupId,
            ]
        ]);
        if($response->statusCode() != 200){

            throw new Exception(trans("comModulesEMPATIA.errorUpdateCbsPermissions"));
        }
        return $response->json();
    }
    /**
     * @param $entityGroupKey
     * @return array with all groups permissions and array with user permissions
     * @throws Exception
     */
    public static function getUserCBPermissions($cbKey,$userKey,$entityKey)
    {
        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'allPermissions',
            'method'    => 'userCbPermissions',
            'params'  => [
                'cbKey' => $cbKey,
                'userKey' => $userKey,
                "entityKey" =>$entityKey,
            ]
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesEMPATIA.errorGetUserCbsPermissions"));
        }
        return $response->json();
    }

    /**
     * @param $entityGroupKey
     * @return array with user cbs
     * @throws Exception
     */
    public static function getUserCBs($user,$entityKey)
    {
        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'allPermissions',
            'method'    => 'cbs',
            'params'  => [
                'user' => $user,
                "entityKey" =>$entityKey,
            ]
        ]);
       
        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesEMPATIA.errorGetUserCbs"));
        }
        return $response->json();
    }
    /* End Permissions - users/groups/cbs*/
}


