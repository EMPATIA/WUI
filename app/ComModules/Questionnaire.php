<?php

namespace App\ComModules;

use App\One\One;
use Exception;



class Questionnaire {

    /*    FORM     */
    public static function setNewForm($request, $contentTranslation){
        $response = ONE::post([
            'component' => 'q',
            'api'       => 'form',
            'params'    => [
                '_token' => $request->get("_token"),
                'form_name' => $request->get("form_name"),
                'start_date' => $request->get("start_date"),
                'end_date' => $request->get("end_date"),
                'public' => $request->get("public"),
                'translations' => $contentTranslation,
            ]
        ]);
        if($response->statusCode()!= 201)
        {
            throw new Exception(trans("comModulesSocial.errorSettingNewForm"));
        }
        return $response->json();
    }

    public static function getForm($key){
        $response = ONE::get([
            'component' => 'q',
            'api'       => 'form',
            'attribute' => $key
        ]);
        if($response->statusCode()!= 200){
            throw new Exception(trans("comModulesSocial.errorRetrievingForm"));
        }
        return $response->json();
    }

    public static function updateForm($key,$request, $contentTranslation){
        $response = ONE::put([
            'component' => 'q',
            'api'       => 'form',
            'params'    => [
                'start_date' => $request->get("start_date"),
                'end_date' => $request->get("end_date"),
                'public' => $request->get("public"),
                'translations' => $contentTranslation,
            ],
            'attribute'     => $key
        ]);
        if($response->statusCode()!= 200)
        {
            throw new Exception(trans("comModulesSocial.errorSettingForm"));
        }
        return $response->json();
    }

    /*    QUESTION GROUP   */
    public static function getQuestionGroup($key){
        $response = ONE::get([
            'component' => 'q',
            'api'       => 'questionGroup',
            'attribute' => $key
        ]);
        if($response->statusCode()!= 200){
            throw new Exception(trans("comModulesSocial.errorRetrievingQuestionGroup"));
        }
        return $response->json();
    }

    public static function setNewQuestionGroup($request, $contentTranslation){
        $response = ONE::post([
            'component' => 'q',
            'api'       => 'questionGroup',
            'params'    =>  [
                '_token' => $request->get('_token'),
                'form_key' => $request->get('form_key'),
                'position' => $request->get('position'),
                'translations' =>$contentTranslation
            ]
        ]);
        if($response->statusCode()!= 201)
        {
            throw new Exception(trans("comModulesSocial.errorSettingNewQuestionGroup"));
        }
        return $response->json();
    }

    public static function updateQuestionGroup($request, $key, $contentTranslation){
        $response = ONE::put([
            'component' => 'q',
            'api'       => 'questionGroup',
            'params'    => [
                'position' => $request->get('position'),
                'translations' =>$contentTranslation,
            ],
            'attribute'     => $key
        ]);
        if($response->statusCode()!= 200)
        {
            throw new Exception(trans("comModulesSocial.errorSettingQuestionGroup"));
        }
        return $response->json();
    }

    /*     QUESTION     */
    public static function getQuestion($questionKey){
        $response = ONE::get([
            'component' => 'q',
            'api'       => 'question',
            'attribute' => $questionKey
        ]);
        if($response->statusCode()!= 200){
            throw new Exception(trans("comModulesSocial.errorRetrievingQuestion"));
        }
        return $response->json();
    }

    public static function getQuestionList(){
        $response = ONE::get([
            'component' => 'q',
            'api'       => 'questionType',
            'method'    => 'list'
        ]);
        if($response->statusCode()!= 200){
            throw new Exception(trans("comModulesSocial.errorRetrievingQuestionList"));
        }
        return $response->json();
    }

    public static function setNewQuestion($request, $contentTranslation){
        $response = ONE::post([
            'component' => 'q',
            'api'       => 'question',
            'params'    => [
                '_token' => $request->get("_token"),
                'translations' => $contentTranslation,
                'question_type_key' => $request->get('question_type_key'),
                'mandatory' => $request->get('mandatory'),
                'question_group_key' => $request->get('question_group_key'),
                'position' => $request->get('position'),
                'questionOptionsIds' => $request->get('questionOptionsIds'),
                'questionOptionsRemove' => $request->get('questionOptionsRemove')
            ]
        ]);
        if($response->statusCode()!= 201)
        {
            throw new Exception(trans("comModulesSocial.errorSettingNewQuestion"));
        }
        return $response->json();
    }

    public static function updateQuestion($request, $key, $contentTranslation){
        $response = ONE::put([
            'component' => 'q',
            'api'       => 'question',
            'params'    => [
                'translations' => $contentTranslation,
                'question_type_key' => $request->get('question_type_key'),
                'mandatory' => $request->get('mandatory'),
                'question_group_key' => $request->get('question_group_key'),
                'position' => $request->get('position'),
                'questionOptionsIds' => $request->get('questionOptionsIds'),
                'questionOptionsRemove' => $request->get('questionOptionsRemove')

            ],
            'attribute'     => $key
        ]);
        if($response->statusCode()!= 200)
        {
            throw new Exception(trans("comModulesSocial.errorSettingQuestion"));
        }
        return $response->json();
    }

    /*   ICONS   */
    public static function getIcons()
    {
        $response = ONE::get([
            'component'  => 'q',
            'api'        => 'icon',
            'method'     => 'list'
        ]);


        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesSocial.errorRetrievingIcons"));
        }
        return $response->json()->data;

    }


    public static function setNewIcon($request)
    {
        $response = ONE::post([
            'component' => 'q',
            'api'       => 'icon',
            'params'    => $request->all()
        ]);
        if($response->statusCode()!= 201)
        {
            throw new Exception(trans("comModulesSocial.errorSettingNewIcons"));
        }
        return $response->json();

    }

    public static function getIcon($iconKey)
    {

        $response = ONE::get([
            'component' => 'q',
            'api'       => 'icon',
            'attribute' => $iconKey
        ]);
        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesSocial.errorRetrievingIcon"));
        }
        return $response->json();
    }

    public static function updateIcon($request, $iconKey)
    {
        $response = ONE::put([
            'component' => 'q',
            'api'       => 'icon',
            'params'    => $request->all(),
            'attribute' => $iconKey
        ]);
        if($response->statusCode()!= 200)
        {
            throw new Exception(trans("comModulesSocial.errorSettingIcon"));
        }
        return $response->json();
    }

    public static function deleteIcon($iconKey)
    {
        $response = ONE::delete([
            'component' => 'q',
            'api'       => 'icon',
            'attribute' => $iconKey
        ]);
        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesSocial.errorDeletingIcon"));
        }

    }

    public static function setNewQuestionOption($request)
    {

        $response = ONE::post([
            'component' => 'q',
            'api'       => 'questionOption',
            'params'    => $request->all()
        ]);
        if($response->statusCode() != 201){
            throw new Exception(trans("comModulesSocial.errorSettingNewQuestionOption"));
        }
        return $response->json();
    }

    public static function setNewQuestionOptionWithParams($params)
    {
        $response = ONE::post([
            'component' => 'q',
            'api'       => 'questionOption',
            'params'    => $params
        ]);
        if($response->statusCode() != 201){
            throw new Exception(trans("comModulesSocial.errorSettingNewQuestionOption"));
        }
        return $response->json();
    }

    public static function getQuestionOption($key)
    {
        $response = ONE::get([
            'component' => 'q',
            'api'       => 'questionOption',
            'attribute' => $key
        ]);
        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesSocial.errorRetrievingQuestionOption"));
        }
        return $response->json();

    }

    public static function updateQuestionOption($key, $request)
    {
        $response = ONE::put([
            'component' => 'q',
            'api'       => 'questionOption',
            'params'    => $request->all(),
            'attribute'     => $key
        ]);
        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesSocial.errorSettingQuestionOption"));
        }
        return $response->json();

    }


    public static function updateQuestionOptionWithParams($key, $params)
    {
        $response = ONE::put([
            'component' => 'q',
            'api'       => 'questionOption',
            'params'    => $params,
            'attribute'  => $key
        ]);
        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesSocial.errorSettingQuestionOption"));
        }
        return $response->json();
    }

    public static function getQuestionDependencies($questionKey)
    {
        $response = ONE::get([
            'component' => 'q',
            'api'       => 'question',
            'method'    =>  'dependencies',
            'attribute' => $questionKey
        ]);
        if($response->statusCode()!= 200){
            throw new Exception(trans("comModulesSocial.errorRetrievingQuestionDependencies"));
        }
        return $response->json();
    }

    public static function reuseQuestionOptions($key)
    {
        $response = ONE::get([
            'component' => 'q',
            'api'       => 'question',
            'method'    =>  'reuse',
            'api_attribute' => $key
        ]);
        if($response->statusCode()!= 200){
            throw new Exception(trans("comModulesSocial.errorRetrievingQuestions"));
        }

    }

    public static function getReuseQuestionOptions($formKey)
    {
        $response = ONE::get([
            'component'  => 'q',
            'api'        => 'question',
            'attribute' => $formKey,
            'method'     => 'getReuseOptions'
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesSocial.errorRetrievingReuseQuestionOptions"));
        }
        return $response->json()->data;

    }

    public static function setReuseOptions($request)
    {
        $response = ONE::post([
            'component' => 'q',
            'api'       => 'questionOption',
            'method'    => 'duplicateReuseOptions',
            'params'    => $request->all()
        ]);
        if($response->statusCode() != 201){
            throw new Exception(trans("comModulesSocial.errorSettingReuseQuestionOptions"));
        }
        return $response->json();

    }

    public static function getQuestionnaireList()
    {
        $response = ONE::get([
            'component' => 'q',
            'api' => 'form',
            'method' => 'list'
        ]);
    
        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesQuestionnaire.failedToGetQuestionnaireList"));
        }
        return $response->json()->data;

    }

    public static function getEventSchedulesList()
    {
        $response = ONE::get([
            'component' => 'q',
            'api'       => 'eventSchedule',
            'method'    => 'list'
        ]);

        if($response->statusCode() != 200) {
            throw new Exception(trans("comModulesQuestionnaire.failedToGetPoolsList"));
        }
        return $response->json()->data;
    }

    public static function getQuestionAnswers($id)
    {
        $response = ONE::get([
            'component' => 'q',
            'api' => 'formReplyAnswer',
            'api_attribute' => $id,
            'method' => 'listAnswers'

        ]);

        if ($response->statusCode() != 200) {
            throw new Exception(trans("comModulesQuestionnaire.failedToGetAnswers"));


        }

        return $response->json();

    }

    public static function getFormRepliesList($formKey)
    {
        $response = ONE::get([
            'component'     => 'q',
            'api'           => 'formReply',
            'attribute' => $formKey,
            'method'        => 'list',
        ]);

        if($response->statusCode() != 200) {
            throw new Exception(trans("comModulesQuestionnaire.failedToGetListOfReplies"));
        }

        return $response->json()->data;
    }

    public static function getQuestionnaire($questionnaireKey)
    {
        $response = ONE::get([
            'component' => 'q',
            'api'       => 'form',
            'attribute' => $questionnaireKey
        ]);

        if($response->statusCode() != 200) {
            throw new Exception(trans("comModulesQuestionnaire.failed_to_get_questionnaire"));

        }
        return $response->json();

    }


    public static function getQuestionOptions($key)
    {

        $response = ONE::get([
            'component'     => 'q',
            'api'           => 'question',
            'api_attribute' => $key,
            'method'        => 'questionOption',
            'attribute'     => 'list'
        ]);

        if($response->statusCode() != 200) {
            throw new Exception(trans("comModulesQuestionnaire.failedToGetQuestionOptions"));
        }

        return  $response->json();
    }

    public static function verifyReply($formKey){
        $response = ONE::get([
            'component'     => 'q',
            'api'           => 'formReply',
            'method' => 'verifyReply',
            'attribute'     => $formKey
        ]);

        if($response->statusCode() != 200) {
            throw new Exception(trans("comModulesQuestionnaire.failedToVerifyReply"));
        }

        return  $response->json()->response;
    }

    public static function getEventSchedule($key){
        $response = ONE::get([
            'component' => 'q',
            'api'       => 'eventSchedule',
            'attribute' => $key
        ]);

        if($response->statusCode() != 200) {
            throw new Exception(trans("comModulesQuestionnaire.failedToGetEventSchedule"));
        }

        return  $response->json();
    }

    public static function setEventSchedule($data){
        $response = ONE::post([
            'component' => 'q',
            'api'       => 'eventSchedule',
            'params'    => $data
        ]);

        if($response->statusCode() != 201) {
            throw new Exception(trans("comModulesQuestionnaire.failedToSetEventSchedule"));
        }

        return  $response->json();
    }

    public static function updateEventSchedule($data, $key){
        $response = ONE::put([
            'component' => 'q',
            'api'       => 'eventSchedule',
            'params'    => $data,
            'attribute' => $key
        ]);

        if($response->statusCode() != 200) {
            throw new Exception(trans("comModulesQuestionnaire.failedToUpdateEventSchedule"));
        }

        return  $response->json();
    }

    public static function updateEventSchedulePeriods($data, $key){
        $response = ONE::put([
            'component' => 'q',
            'api'       => 'eventSchedule',
            'params'    => $data,
            'attribute' => $key."/updatePeriods"
        ]);

        if($response->statusCode() != 200) {
            throw new Exception(trans("comModulesQuestionnaire.failedToUpdateEventSchedulePeriods"));
        }

        return  $response->json();
    }

    public static function deleteEventSchedule($id){
        $response = ONE::delete([
            'component' => 'q',
            'api'       => 'eventSchedule',
            'attribute' => $id,
        ]);

        if($response->statusCode() != 200) {
            throw new Exception(trans("comModulesQuestionnaire.failedToDeleteEventSchedule"));
        }

        return  $response->json();
    }

    public static function setParticipation($key, $name, $periods, $questions){
        $response = ONE::post([
            'component' => 'q',
            'api' => 'esParticipant',
            'attribute' => $key,
            'params' => [
                'name' => $name,
                'periods' => $periods,
                'questions' => $questions
            ]
        ]);

        if($response->statusCode() != 201) {
            throw new Exception(trans("comModulesQuestionnaire.failedToSetParticipation"));
        }

        return  $response->json();
    }

    public static function updateParticipation($key, $participationId, $name, $periods, $questions){
        $response = ONE::put([
            'component' => 'q',
            'api' => 'esParticipant',
            'attribute' => $key,
            'params' => [
                'participant_id' => $participationId,
                'name' => $name,
                'periods' => $periods,
                'questions' => $questions
            ]
        ]);

        if($response->statusCode() != 200) {
            throw new Exception(trans("comModulesQuestionnaire.failedToUpdateParticipation"));
        }

        return  $response->json();
    }

    public static function deleteParticipation($key){
        $response = ONE::delete([
            'component' => 'q',
            'api' => 'esParticipant',
            'attribute' => $key
        ]);

        if($response->statusCode() != 200) {
            throw new Exception(trans("comModulesQuestionnaire.failedToDeleteParticipation"));
        }

        return  $response->json();
    }

    public static function setAnonymousParticipation($key, $name, $periods, $questions){
        $response = ONE::post([
            'component' => 'q',
            'api' => 'esParticipant',
            'method' => 'anonymousStore',
            'attribute' => $key,
            'params' => [
                'name' => $name,
                'periods' => $periods,
                'questions' => $questions
            ]
        ]);

        if($response->statusCode() != 200) {
            throw new Exception(trans("comModulesQuestionnaire.failedToSetAnonymousParticipation"));
        }

        return  $response->json();
    }

    public static function updateAnonymousParticipation($key, $participationId, $name, $periods, $questions){
        $response = ONE::put([
            'component' => 'q',
            'api' => 'esParticipant',
            'method' => 'anonymousUpdate',
            'attribute' => $key,
            'params' => [
                'participant_id' => $participationId,
                'name' => $name,
                'periods' => $periods,
                'questions' => $questions
            ]
        ]);

        if($response->statusCode() != 201) {
            throw new Exception(trans("comModulesQuestionnaire.failedToUpdateAnonymousParticipation"));
        }

        return  $response->json();
    }

    public static function deleteAnonymousParticipation($key){
        $response = ONE::delete([
            'component' => 'q',
            'api' => 'esParticipant',
            'method' => 'anonymousDelete',
            'attribute' => $key
        ]);

        if($response->statusCode() != 200) {
            throw new Exception(trans("comModulesQuestionnaire.failedToDeleteAnonymousParticipation"));
        }

        return  $response->json();
    }

    public static function setFormReply($request, $completed, $location = null, $questions){
        $response = ONE::post([
            'component'     => 'q',
            'api'           => 'formReply',
            'params' => [
                'form_key' => $request->questionnaire_key,
                'location' => $location,
                'question_replies' => $questions,
                'completed' => $completed,
                'username' => !empty($request->username) ? $request->username : ""
            ]
        ]);

        if($response->statusCode() != 201) {
            throw new Exception(trans("comModulesQuestionnaire.failedToSetFormReply"));
        }

        return  $response->json();
    }

    public static function getStatisticsByFormReply($questionnarieKey, $formReplyKey){
        $response = ONE::get([
            'component' => 'q',
            'api' => 'form',
            'api_attribute' => $questionnarieKey,
            'method' => 'statisticsByFormReply',
            'attribute' => $formReplyKey
        ]);

        if($response->statusCode() != 200) {
            throw new Exception(trans("comModulesQuestionnaire.failedToGetStatisticsByFormReply"));
        }

        return  $response->json();
    }

    public static function getFormConstruction($key){
        $response = ONE::get([
            'component' => 'q',
            'api' => 'form',
            'api_attribute' => $key,
            'method' => 'construction'
        ]);

        if($response->statusCode() != 200) {
            throw new Exception(trans("comModulesQuestionnaire.failedToGetFormConstruction"));
        }

        return  $response->json();
    }

    public static function storeFormReply($questionnaireId, $questions){
        $response = ONE::post([
            'component'     => 'q',
            'api'           => 'formReply',
            'params' => [
                'form_id' => $questionnaireId,
                'question_replies' => $questions,
            ]
        ]);

        if($response->statusCode() != 201) {
            throw new Exception(trans("comModulesQuestionnaire.failedToStoreFormReply"));
        }

        return  $response->json();
    }

    public static function deleteQuestionGroup($key){
        $response = ONE::delete([
            'component' => 'q',
            'api' => 'questionGroup',
            'attribute' => $key,
        ]);

        if($response->statusCode() != 200) {
            throw new Exception(trans("comModulesQuestionnaire.failedToDeleteQuestionGroup"));
        }

        return  $response->json();
    }

    public static function updateQuestionGroupPositions($updatedPositions){
        $response = ONE::put([
            'component' => 'q',
            'api' => 'questionGroup',
            'method' => 'updatePositions',
            'params'    => [
                'data' => $updatedPositions
            ]
        ]);

        if($response->statusCode() != 200) {
            throw new Exception(trans("comModulesQuestionnaire.failedToUpdateQuestionGroupPositions"));
        }

        return  $response->json();
    }

    public static function updateChangeQuestion($updatedPositions, $destination, $source){
        $response = ONE::put([
            'component' => 'q',
            'api' => 'question',
            'method' => 'changeGroup',
            'params'    => [
                'question_group_key'    => $destination,
                'question_key'          => $source,
                'data' => $updatedPositions
            ]
        ]);

        if($response->statusCode() != 200) {
            throw new Exception(trans("comModulesQuestionnaire.failedToUpdateChangeQuestion"));
        }

        return  $response->json();
    }

    public static function getQuestionGroups($key){
        $response = ONE::get([
            'component'     => 'q',
            'api'           => 'form',
            'api_attribute' => $key,
            'method'        => 'questionGroups'
        ]);

        if($response->statusCode() != 200) {
            throw new Exception(trans("comModulesQuestionnaire.failedToGetQuestionGroups"));
        }

        return  $response->json()->data;
    }

    public static function deleteForm($key){
        $response = ONE::delete([
            'component' => 'q',
            'api'       => 'form',
            'attribute' => $key,
        ]);

        if($response->statusCode() != 200) {
            throw new Exception(trans("comModulesQuestionnaire.failedToDeleteForm"));
        }

        return  $response->json();
    }

    public static function listForm(){
        $response = ONE::get([
            'component' => 'q',
            'api'       => 'form',
            'method'    => 'list',
        ]);

        if($response->statusCode() != 200) {
            throw new Exception(trans("comModulesQuestionnaire.failedToListForm"));
        }

        return  $response->json()->data;
    }

    public static function setStatisticsByForm($questionnaireKey){
        $response = ONE::post([
            'component' => 'q',
            'api' => 'form',
            'api_attribute' => $questionnaireKey,
            'method' => 'statisticsByForm',
        ]);

        if($response->statusCode() != 200) {
            throw new Exception(trans("comModulesQuestionnaire.failedToSetStatisticsByForm"));
        }

        return  $response->json();
    }

    public static function deleteQuestionOption($key){
        $response = ONE::delete([
            'component' => 'q',
            'api'       => 'questionOption',
            'attribute' => $key,
        ]);

        if($response->statusCode() != 200) {
            throw new Exception(trans("comModulesQuestionnaire.failedToDeleteQuestionOption"));
        }

        return  $response->json();
    }

    public static function deleteQuestion($key){
        $response = ONE::delete([
            'component' => 'q',
            'api' => 'question',
            'attribute' => $key,
        ]);

        if($response->statusCode() != 200) {
            throw new Exception(trans("comModulesQuestionnaire.failedToDeleteQuestion"));
        }

        return  $response->json();
    }

    public static function getListQuestionGroup($key){
        $response = ONE::get([
            'component' => 'q',
            'api' => 'questionGroup',
            'api_attribute' => $key,
            'method' => 'question',
            'attribute' => 'list'
        ]);

        if($response->statusCode() != 200) {
            throw new Exception(trans("comModulesQuestionnaire.failedToDeleteQuestion"));
        }

        return  $response->json();
    }
}
