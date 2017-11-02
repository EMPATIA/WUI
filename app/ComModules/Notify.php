<?php

namespace App\ComModules;

use App\One\One;
use Exception;
use Session;

class Notify {
    /**
     * @param $emailType
     * @param $tags
     * @param $user
     * @return mixed
     * @throws Exception
     */
    public static function sendEmail($emailType, $tags, $user){

        $site = Orchestrator::getSite(Session::get('X-SITE-KEY'));
        if(isset($site->no_reply_email)) {
            $response = ONE::post([
                'component' => 'notify',
                'api' => 'email',
                'method' => 'emailSend',
                'attribute' => $emailType,
                'params' => [
                    'no_reply' => $site->no_reply_email,
                    'sender_name' => $site->name,
                    'recipient' => $user['email'],
                    'content' => $tags,
                    'user_key' => isset($user["user_key"]) ? $user["user_key"] : null
                ]
            ]);

            return $response->json();
        }
        return false;

    }
    public static function sendEmailUsingDirectData($emailType, $tags, $destiny){

        $site = Orchestrator::getSite(Session::get('X-SITE-KEY'));
        if(isset($site->no_reply_email)) {
            $response = ONE::post([
                'component' => 'notify',
                'api' => 'email',
                'method' => 'emailSend',
                'attribute' => $emailType,
                'params' => [
                    'no_reply' => $site->no_reply_email,
                    'sender_name' => $site->name,
                    'recipient' => $destiny,
                    'content' => $tags,
                    'user_key' => null
                ]
            ]);

            return $response->json();
        }
        return false;

    }

    public static function sendEmailByTemplateKey($templateKey, $usersEmail, $user_key, $tags){
        $site = Orchestrator::getSite(Session::get('X-SITE-KEY'));
        if(isset($site->no_reply_email)) {
            $response = ONE::post([
                'component' => 'notify',
                'api' => 'email',
                'method' => 'send',
                'attribute' => $templateKey,
                'params' => [
                    'no_reply' => $site->no_reply_email,
                    'sender_name' => $site->name,
                    'recipient' => $usersEmail,
                    'content' => $tags,
                    'user_key' => $user_key
                ]
            ]);

            return $response->json();
        }
        return false;
    }

    /**
     *
     * Receives an array of users
     *
     * @param $emailType
     * @param $tags
     * @param $users
     * @return bool
     */
    public static function sendEmailForMultipleUsers($emailType, $tags, $users){

        $site = Orchestrator::getSite(Session::get('X-SITE-KEY'));
        if(isset($site->no_reply_email)) {
            $response = ONE::post([
                'component' => 'notify',
                'api'       => 'email',
                'method'    => 'emailSend',
                'attribute' => $emailType,
                'params'    => [
                    'no_reply'      => $site->no_reply_email,
                    'sender_name'   => $site->name,
                    'recipient'     => $users,
                    'content'       => $tags,
                    'user_key'      => isset($user["user_key"]) ? $user["user_key"] : null
                ],
            ]);

            return $response->json();
        }
        return false;

    }

    public static function sendManyEmails($emailType, $emailData){
        $site = Orchestrator::getSite(Session::get('X-SITE-KEY'));
        if(isset($site->no_reply_email)) {
            $response = ONE::post([
                'component' => 'notify',
                'api' => 'email',
                'method' => 'sendMany',
                'attribute' => $emailType,
                'params' => [
                    'no_reply' => $site->no_reply_email,
                    'sender_name' => $site->name,
                    'emailData' => $emailData
                ]
            ]);

            return $response->json();
        }
        return false;

    }

    public static function sendSkebbySMS($recipient, $text){

        $response = ONE::post([
            'component' => 'notify',
            'api' => 'skebby',
            'method' => 'sendSMS',
            'params' => [
                'username' => Session::get("SITE-CONFIGURATION.sms_service_username"),
                'password' =>  Session::get("SITE-CONFIGURATION.sms_service_password"),
                'recipients' => $recipient,
                'text' => $text,
                'sms_type' => 'classic',
                'sender_string' => Session::get("SITE-CONFIGURATION.sms_service_sender_name")
            ]
        ]);

        return $response->json();
    }

    public static function getEmailTemplates()
    {
        $response = One::get([
            'component' => 'notify',
            'api' => 'emailTemplate',
            'method' => 'list'


        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesNotify.errorUpdatingTemplate"));
        }

        return $response->json();
    }

    public static function editEmailTemplate($typeKey, $templateKey, $translations){
        $response = ONE::put([
            'component' => 'notify',
            'api' => 'emailTemplate',
            'attribute' => $templateKey,
            'params' => [
                'type_key' => $typeKey,
                'translations' => $translations
            ]
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesNotify.errorUpdatingTemplate"));
        }
        return $response->json();
    }

    /**
     * @param $typeKey
     * @param $siteKey
     * @param $translations
     * @return mixed
     * @throws Exception
     */
    public static function postEmailTemplate($typeKey, $siteKey, $translations)
    {
        $response = One::post([
            'component' => 'notify',
            'api' => 'emailTemplate',
            'params' => [
                'type_key' => $typeKey,
                'site_key' => $siteKey,
                'translations' => $translations
            ],
        ]);

        if($response->statusCode()!= 201){
            throw new Exception(trans("comModulesNotify.failedSaveEmailTemplate"));
        }
        return $response->json();
    }

    /**
     * @param $typeCode
     * @return mixed
     * @throws Exception
     */
    public static  function getTypeKey($typeCode){
        $response = ONE::get([
            'component' => 'notify',
            'api' => 'type',
            'method' => 'getTypeKey',
            'params' => [
                'typeCode' => $typeCode
            ]
        ]);
        if($response->statusCode() != 200) {
            throw new Exception(trans("comModulesNotify.errorGettingTypeKey"));
        }
        return $response->json();
    }

    /**
     * @return mixed
     * @throws Exception
     */
    public static function getTypesAvailable(){
        $response = ONE::get([
            'component' => 'notify',
            'api' => 'type',
            'method' => 'list'
        ]);
        if($response->statusCode() != 200) {
            throw new Exception(trans("comModulesNotify.errorGettingTypesAvailable"));
        }
        return $response->json()->data;
    }

    /**
     * @param $templateKey
     * @throws Exception
     */
    public static function deleteEmailTemplate($templateKey)
    {
        $response = ONE::delete([
            'component' => 'notify',
            'api' => 'emailTemplate',
            'api_attribute' => $templateKey
        ]);

        if($response->statusCode() != 200) {
            throw new Exception(trans("comModulesNotify.errorDeletingTemplateEmail"));
        }

    }

    /**
     * @param $templateKey
     * @return mixed
     * @throws Exception
     */
    public static function getEmailTemplateTranslations($templateKey)
    {
        $response = One::get([
            'component' => 'notify',
            'api' => 'emailTemplate',
            'method' => 'edit',
            'api_attribute' => $templateKey
        ]);

        if($response->statusCode() != 200) {
            throw new Exception(trans("comModulesNotify.errorGettingTemplateEmailTranslations"));
        }
        return $response->json();
    }

    /**
     * @param $templateKey
     * @return mixed
     * @throws Exception
     */
    public static function getEmailTemplate($templateKey)
    {
        $response = One::get([
            'component' => 'notify',
            'api' => 'emailTemplate',
            'attribute' => $templateKey
        ]);

        if($response->statusCode() != 200) {
            throw new Exception(trans("comModulesNotify.errorGettingTemplateEmail"));
        }

        return $response->json();
    }

    /**
     * @return mixed
     * @throws Exception
     */
    public static function getEmailTemplateTypes()
    {
        $response = One::get([
            'component' => 'notify',
            'api' => 'type',
            'method' => 'list'
        ]);

        if($response->statusCode() != 200) {
            throw new Exception(trans("comModulesNotify.errorGettingTemplateEmailTypes"));
        }
        return $response->json()->data;
    }

    /**
     * @param $siteKey
     * @return mixed
     * @throws Exception
     */
    public static function getEmailTemplatesSite($siteKey)
    {
        $response = One::get([
            'component' => 'notify',
            'api' => 'emailTemplate',
            'method' => 'list',
            'params' => ['site_key' => $siteKey]
        ]);
        if($response->statusCode() != 200 ){
            throw new Exception(trans("comModulesNotify.errorGettingSiteEmailTemplates"));
        }
        return $response->json()->data;
    }

    /**
     * Gets list of emails by entity
     *
     * @param $request
     * @return mixed
     * @throws Exception
     */
    public static function getEmails($request)
    {
        $response = One::get([
            'component' => 'notify',
            'api'       => 'email',
            'method'    => 'entityEmail',
            'params'    => [
                'tableData' => One::tableData($request),
            ]
        ]);

        if($response->statusCode() != 200 ){
            throw new Exception(trans("comModulesNotify.error_getting_sent_emails"));
        }

        return $response->json();
    }

    /**
     *
     * Returns email from given emailKey
     *
     * @param $emailKey
     * @return mixed
     * @throws Exception
     */
    public static function getEmail($emailKey)
    {
        $response = One::get([
            'component' => 'notify',
            'api'       => 'email',
            'attribute' => $emailKey
        ]);
        if($response->statusCode() != 200) {
            throw new Exception(trans("comModulesNotify.error_getting_email"));
        }

        return $response->json();
    }

    /**
     * @param $siteKeys
     * @param $languages
     * @param $defaultLanguage
     * @return mixed
     * @throws Exception
     */
    public static function newSiteEmailsTemplates($siteKeys, $languages, $defaultLanguage = null  )
    {
        if(!isset($defaultLanguage))
            $defaultLanguage = Session::get('LANG_CODE_DEFAULT','INVALID');
        $response = One::post([
            'component'     => 'notify',
            'api'           => 'genericEmailTemplate',
            'method'        => 'newSiteTemplates',
            'params' => [
                'site_keys' => $siteKeys,
                'languages' => $languages,
                'default_language' => $defaultLanguage
            ],
        ]);

        if($response->statusCode()!= 201)
        {
            throw new Exception(trans("comModulesNotify.failedCopyNewSIteTemplates"));
        }
        return $response->json();
    }

    /**
     * @return mixed
     * @throws Exception
     */
    public static function getSmss()
    {

        $response = One::get([

            'component' => 'notify',
            'api' => 'sms',
            'method' => 'entitySms'
        ]);


        if($response->statusCode() != 200 ){
            throw new Exception(trans("comModulesNotify.error_getting_sent_sms"));
        }

        return $response->json();
    }

    /**
     * @param $smsKey
     * @return mixed
     * @throws Exception
     */
    public static function getSms($smsKey)
    {
        $response = One::get([

            'component' => 'notify',
            'api' => 'sms',
            'attribute' => $smsKey
        ]);
        if($response->statusCode() != 200) {
            throw new Exception(trans("comModulesNotify.error_getting_sms"));
        }

        return $response->json();
    }

    /* Newsletters */
    /**
     * @param $request
     * @return mixed
     * @throws Exception
     */
    public static function getNewsletters($request)
    {
        $response = ONE::get([
            'component' => 'notify',
            'api'       => 'newsletters',
            'api_attribute' => 'list',
            'params'    => [
                'tableData' => One::tableData($request),
            ]
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesNotify.failed_to_get_newsletters"));
        }
        return $response->json();
    }

    /**
     * @param $newsletterKey
     * @return mixed
     * @throws Exception
     */
    public static function getNewsletter($newsletterKey)
    {
        $response = ONE::get([
            'component' => 'notify',
            'api'       => 'newsletters',
            'api_attribute' => $newsletterKey
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesNotify.failed_to_get_newsletter"));
        }
        return $response->json();
    }

    /**
     * @param $data
     * @return mixed
     * @throws Exception
     */
    public static function createNewsletter($data){
        $response = ONE::post([
            'component' => 'notify',
            'api'       => 'newsletters',
            'params'    =>  $data
        ]);


        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesNotify.failedToCreateNewsletter"));
        }
        return $response->json();
    }

    /**
     * @param $newsletterKey
     * @param $data
     * @return mixed
     * @throws Exception
     */
    public static function updateNewsletter($newsletterKey, $data){
        $response = ONE::put([
            'component' => 'notify',
            'api'       => 'newsletters',
            'api_attribute' => $newsletterKey,
            'params'    => $data
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesNotify.error_updating_newsletter"));
        }
        return $response->json();
    }

    /**
     * @param $newsletterKey
     * @return mixed
     * @throws Exception
     */
    public static function deleteNewsletter($newsletterKey){
        $response = ONE::delete([
            'component' => 'notify',
            'api'       => 'newsletters',
            'api_attribute' => $newsletterKey,
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesNotify.failedToDeleteNewsletter"));
        }
        return $response->json();
    }

    public static function testNewsletter($newsletterKey, $userKey){
        $response = ONE::post([
            'component' => 'notify',
            'api'       => 'newsletters',
            'method' => 'testNewsletter',
            'attribute' => $newsletterKey,
            'params' =>[
                'user_key' => $userKey
            ]
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesNotify.failedToDeleteNewsletter"));
        }
        return $response->json();
    }

    /**
     * @param $request
     * @param $site
     * @return mixed
     * @throws Exception
     */
    public static function createEmails($request, $site)
    {
        $response = ONE::post([
            'component' => 'notify',
            'api'       => 'email',
            'method'    => 'createEmails',
            'params'    => [
                'to'            => (isset($request->users) ? $request->users : null),
                'subject'       => (isset($request->subject) ? $request->subject : null),
                'message'       => (isset($request->message) ? $request->message : null),
                'newsletter_id' => (isset($request->newsletter_id) ? $request->newsletter_id : null),
                'no_reply'      => $site->no_reply_email,
                'sender_name'   => $site->name,
                'action_url'    => (isset($request->action_url) ? $request->action_url : null)
            ]
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesNotify.failed_to_create_emails"));
        }
        return $response->json();
    }

    public static function setSms($request)
    {
        $response = ONE::post([
            'component' => 'notify',
            'api'       => 'sms',
            'method'   => 'createSms',
            'params'    => [
                'username' => Session::get("SITE-CONFIGURATION.sms_service_username"),
                'password' => Session::get("SITE-CONFIGURATION.sms_service_password"),
                'to'       => (isset($request->to) ? $request->to : null),
                'message'      => (isset($request->message) ? $request->message : null),
                'sms_type' => 'classic',

            ]
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesNotify.failed_to_set_sms"));
        }
        return $response->json();
    }



    public static function sendBulkSMS($recipient, $text, $indicative){
//        $recipient = '49' . $recipient;
        $response = ONE::post([
            'component' => 'notify',
            'api' => 'bulkSMS',
            'method' => 'sendSMS',
            'params' => [
                'username' => Session::get("SITE-CONFIGURATION.sms_service_username"),
                'password' =>  Session::get("SITE-CONFIGURATION.sms_service_password"),
                'recipient' => $recipient,
                'text' => $text,
                'sms_type' => 'classic',
                'sender_string' => Session::get("SITE-CONFIGURATION.sms_service_sender_name"),
                'indicative_number' => $indicative
            ]
        ]);

        return $response->json();
    }


    public static function sendSMS($recipient, $text) {
        $response = ONE::post([
            'component' => 'notify',
            'api' => 'sms',
            'method' => 'sendSMS',
            'params' => [
                'configurations' => array(
                    'username'      => Session::get("SITE-CONFIGURATION.sms_service_username",""),
                    'password'      => Session::get("SITE-CONFIGURATION.sms_service_password",""),
                    'sender_name'   => Session::get("SITE-CONFIGURATION.sms_service_sender_name",""),
                    'service'       => Session::get("SITE-CONFIGURATION.sms_service_code",""),
                ),
                'recipient' => $recipient,
                'content' => $text
            ]
        ]);
        return $response->json();
    }
}
