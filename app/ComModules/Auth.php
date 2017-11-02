<?php

namespace App\ComModules;

use App\One\One;
use Exception;

class Auth {

    public static function listUser($usersKey) {
        $response = ONE::post([
            'component' => 'empatia',
            'api'       => 'auth',
            'method'    => 'listUser',
            'params'    => [
                'userList' => $usersKey
            ]
        ]);
        return $response->json();
    }

    public static function getListNames($keys){
        $response = ONE::post([
            'component' => 'empatia',
            'api' => 'auth',
            'method' => 'listNames',
            'params' => [
                'userList' => $keys
            ]
        ]);
        if($response->statusCode()!= 200){
            throw new Exception(trans("comModulesAuth.errorRetrievingListOfNamesOfUsers"));
        }
        return $response->json()->data;
    }

    public static function getUserNames($keys){
        $response = ONE::post([
            'component' => 'empatia',
            'api' => 'auth',
            'method' => 'listNames',
            'params' => [
                'userList' => $keys
            ]
        ]);
        if($response->statusCode()!= 200){
            throw new Exception(trans("comModulesAuth.errorRetrievingListOfNamesOfUsers"));
        }
        return $response->json()->data;
    }

    /**
     * @param $keys
     * @return mixed
     * @throws Exception
     */
    public static function getPublicListNames($keys){
        $response = ONE::post([
            'component' => 'empatia',
            'api' => 'auth',
            'method' => 'publicListNames',
            'params' => [
                'userList' => $keys
            ]
        ]);

        if($response->statusCode()!= 200){
            throw new Exception(trans("comModulesAuth.errorRetrievingPublicListOfNamesOfUsers"));
        }
        return $response->json()->data;
    }

    /**
     * @param bool $withSms
     * @return mixed
     * @throws Exception
     */
    public static function getUser($withSms = false) {
        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'auth',
            'method'       => 'getUser',
            'params'       => [
                'withSms' => $withSms
            ]
        ]);
        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesAuth.errorRetrievingUser"));
        }
        return $response->json()->user;
    }

    public static function getUserByKey($userKey, $withSms = false) {
        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'auth',
            'attribute' => $userKey,
            'params' => ['withSms' => $withSms]
        ]);
        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesAuth.errorRetrievingUser"));
        }
        return $response->json();
    }

    /**
     * @param $userKey
     * @return mixed
     * @throws Exception
     */
    public static function getUserParameters($userKey) {
        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'getUserParameters',
            'attribute' => $userKey
        ]);
        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesAuth.errorRetrievingUser"));
        }
        return $response->json()->user;
    }


    public static function login($email,$password){
        $response = ONE::post([
            'component' => 'empatia',
            'api' => 'auth',
            'method' => 'authenticate',
            'params' => [
                'email' => $email,
                'password' => $password
            ]
        ]);

        if($response->statusCode() != 200){
            if($response->statusCode() == 500 || $response->statusCode() == 401){
                throw new Exception(trans("comModulesAuth.invalid_credentials"));
            }

            throw new Exception(trans("comModulesAuth.errorInLogin"));
        }
        return $response->json();
    }

    public static function storeUser($name, $email, $password, $surname = null)
    {
        $response = ONE::post([
            'component' => 'empatia',
            'api'       => 'auth',
            'params'    => [
                'name'       => $name,
                'email'      => $email,
                'password'   => $password,
                'surname' => $surname,
            ]
        ]);
        if($response->statusCode() != 201){
            if($response->statusCode() == 499){
                throw new Exception(trans("comModulesAuth.errorEmailAlreadyRegisteredInLibertrium"));
            }
            if($response->statusCode() == 409){
                throw new Exception(trans("comModulesAuth.errorEmailAlreadyregister"));
            }
            throw new Exception(trans("comModulesAuth.errorInStoreUser"));
        }

        return $response->json();
    }

    public static function storeUserV2($params)
    {
        $response = ONE::post([
            'component' => 'empatia',
            'api'       => 'auth',
            'params'    => $params
        ]);
        if($response->statusCode() != 201){
            if($response->statusCode() == 499){
                throw new Exception(trans("comModulesAuth.errorEmailAlreadyRegisteredInLibertrium"));
            }
            if($response->statusCode() == 409){
                throw new Exception(trans("comModulesAuth.errorEmailAlreadyregister"));
            }
            throw new Exception(trans("comModulesAuth.errorInStoreUser"));
        }

        return $response->json();
    }

    public static function storeUserInPerson($data, $details)
    {
        $response = ONE::post([
            'component' => 'empatia',
            'api'       => 'auth',
            'method'    => 'storeId',
            'params'    => [
                'name'          => $data['name'],
                'identity_card' => $data['identity_card'],
                'parameters'   => $details
            ]
        ]);
        if($response->statusCode() != 201){
            throw new Exception(trans("comModulesAuth.errorInStoreUser"));
        }
        return $response->json();
    }

    public static function storeNewUser($data,$userDetails)
    {
        $response = ONE::post([
            'component' => 'empatia',
            'api'       => 'auth',
            'params'    => [
                'name'       => isset($data['name']) ? $data['name'] : null,
                'email'      => isset($data['email']) ? $data['email'] : null,
                'password'   => isset($data['password']) ? $data['password'] : null,
                'identity_card'   => isset($data['identity_card']) ? $data['identity_card'] : null,
                'vat_number'   => isset($data['vat_number']) ? $data['vat_number'] : null,
                'parameters' => isset($userDetails) ? $userDetails : null
            ]
        ]);
        if($response->statusCode() != 201){
            if($response->statusCode() == 409){
                throw new Exception(trans("comModulesAuth.errorIdNumberOrEmailAlreadyExists"));
            } else {
                throw new Exception(trans("comModulesAuth.errorInStoreUser"));
            }
        }
        return $response->json();
    }

    public static function updateUser($userKey,$data,$userDetails, $withSms = false)
    {
        $response = ONE::put([
            'component' => 'empatia',
            'api'       => 'auth',
            'attribute' => $userKey,
            'params'    => [
                'name'       => isset($data['name']) ? $data['name'] : null,
                'email'      => isset($data['email']) ? $data['email'] : null,
                'password'   => isset($data['password']) ? $data['password'] : null,
                'identity_card'   => isset($data['identity_card']) ? $data['identity_card'] : null,
                'vat_number'   => isset($data['vat_number']) ? $data['vat_number'] : null,
                'parameters' => isset($userDetails) ? $userDetails : null,
                'withSms' => $withSms
            ]
        ]);

        if($response->statusCode() != 200){
            if($response->statusCode() == 409){
                throw new Exception(trans("comModulesAuth.errorIdNumberOrEmailAlreadyExists"));
            } else {
                throw new Exception(trans("comModulesAuth.errorInUpdateUser"));
            }
        }
        return $response->json();
    }

    public static function deleteUser($userKey)
    {
        $response = ONE::delete([
            'component' => 'empatia',
            'api'       => 'auth',
            'attribute' => $userKey,
        ]);
        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesAuth.errorDeletingUser"));
        }

    }

    public static function passwordRecovery($email)
    {
        $response = ONE::post([
            'component' => 'empatia',
            'api'       => 'auth',
            'method'       => 'recover',
            'params'    => [
                'email' => $email
            ]
        ]);
        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesAuth.errorInPasswordRecovery"));
        }
        return $response->json();
    }

    public static function updatePasswordRecovery($userKey, $recoverToken, $password)
    {
        $response = ONE::post([
            'component' => 'empatia',
            'api'       => 'auth',
            'method'    => 'recover',
            'params'    => [
                'user_key'                  => $userKey,
                'recover_password_token'    => $recoverToken,
                'password'                  => $password
            ]
        ]);
        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesAuth.errorInPasswordUpdateRecovery"));
        }
        return $response->json();
    }

    public static function listUserConfirmed($usersKey) {
        $response = ONE::post([
            'component' => 'empatia',
            'api'       => 'auth',
            'method'    => 'listUserConfirmed',
            'params'    => [
                'userList' => $usersKey
            ]
        ]);
        return $response->json();
    }

    public static function updateUserPassword($oldPassword, $password, $userKey = null)
    {
        $response = ONE::post([
            'component' => 'empatia',
            'api'       => 'auth',
            'method'    => 'updatePassword',
            'params'    => [
                'old_password'  => $oldPassword,
                'password'      => $password,
                'user_key'      => $userKey
            ]
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesAuth.errorInPasswordUpdate"));
        }
        return $response->json();
    }

    public static function searchEmail($email) {
        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'auth',
            'method'    => 'searchEmail',
            'params'    => [
                'email' => $email,
            ]
        ]);
        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesAuth.failed_to_search_email"));
        }
        return $response->json();
    }

    public static function emailExists($email) {
        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'auth',
            'method'    => 'emailExists',
            'params'    => [
                'email' => $email,
            ]
        ]);
        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesAuth.failed_to_get_email_existence"));
        }
        return $response->json();
    }


    public static function generateSMSToken() {
        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'auth',
            'method'    => 'getSmsToken',

        ]);
        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesAuth.failed_to_generate_sms_token"));
        }
        return $response->json();
    }

    public static function validateSmsToken($smsToken) {

        $response = ONE::post([
            'component' => 'empatia',
            'api'       => 'auth',
            'method'    => 'validateSmsToken',
            'params'    => [
                'sms_token' => $smsToken,
            ]
        ]);
        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesAuth.failed_to_validate_sms_token"));
        }

        return $response->json();

    }

    public static function storeSmsAttempt() {

        $response = ONE::post([
            'component' => 'empatia',
            'api'       => 'auth',
            'method'    => 'setSmsAttempt',
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesAuth.failed_to_validate_sms_token"));
        }

        return $response->json();

    }

    public static function resetNumberOfSmsSent($userKey) {

        $response = ONE::post([
            'component' => 'empatia',
            'api'       => 'auth',
            'method'    => 'resetNumberSms',
            'params'    => ['user_key' => $userKey ]
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesAuth.failed_to_validate_sms_token"));
        }

        return $response->json();

    }

    public static function verifyVatNumber($parameter_user_key,$value) {

        $response = ONE::post([
            'component' => 'empatia',
            'api'       => 'auth',
            'method'    => 'verifyUniqueParameter',
            'params'    => [
                'parameter_user_key' => $parameter_user_key,
                'value' => $value,
            ]
        ]);
        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesAuth.failed_to_verify_vat_number"));
        }
        return $response->json();
    }

    public static function verifyMobileNumber($parameter_user_key,$value) {

        $response = ONE::post([
            'component' => 'empatia',
            'api'       => 'auth',
            'method'    => 'verifyUniqueParameter',
            'params'    => [
                'parameter_user_key' => $parameter_user_key,
                'value' => $value,
            ]
        ]);
        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesAuth.failed_to_verify_vat_number"));
        }
        return $response->json();
    }

    public static function setPublicParameter($parameter_key,$value) {
        $response = ONE::post([
            'component' => 'empatia',
            'api'       => 'auth',
            'method'    => 'setPublicParameter',
            'params'    => [
                'parameter_key' => $parameter_key,
                'value' => $value,
            ]
        ]);
        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesAuth.failed_to_change_parameter_permissions"));
        }
        return true;
    }

    /**
     * return the Entity User List to populate the Datatable
     *
     * @param $request
     * @return mixed
     * @throws Exception
     */
    public static function getUserList($request)
    {
        $response = ONE::post([
            'component' => 'empatia',
            'api'       => 'auth',
            'method'    => 'getUserList',
            'params'    => [
                'tableData' => One::tableData($request),
                'role' => empty($request->role) ? null : $request->role,
            ]
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesAuth.failed_to_get_user_list"));
        }
        return $response->json();
    }

    /**
     * @param $request
     * @param $siteKey
     * @return mixed
     * @throws Exception
     */
    public static function usersToModerate2($arguments, $siteKey)
    {
        $response = ONE::post([
            'component' => 'empatia',
            'api'       => 'auth',
            'method'    => 'usersToModerate2',
            'params' => [
                'arguments' => $arguments,
                'site_key' => $siteKey,
            ],
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesAuth.failed_to_get_users_to_moderate_list"));
        }
        return $response->json()->data;
    }

    /**
     * @param $request
     * @param $siteKey
     * @return mixed
     * @throws Exception
     */
    public static function usersToModerate($request, $siteKey)
    {
        $response = ONE::post([
            'component' => 'empatia',
            'api'       => 'auth',
            'method'    => 'usersToModerate',
            'params' => [
                'table_data' => ONE::tableData($request),
                'site_key' => $siteKey,
            ],
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesAuth.failed_to_get_users_to_moderate_list"));
        }
        return $response->json()->data;
    }

    /**
     * delete the user register parameters from the current entity
     * @param $parameters
     * @param $userKey
     * @return mixed
     * @throws Exception
     */
    public static function deleteUserParameters($parameters, $userKey)
    {
        $response = ONE::post([
            'component' => 'empatia',
            'api'       => 'auth',
            'method'    => 'deleteUserParameters',
            'params' => [
                'parameters' => $parameters,
                'user_key' => $userKey
            ]
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesAuth.failed_to_delete_user_parameters"));
        }
        return $response->json();
    }

    /**
     * GET A USER ACCORDING TO SOME PARAMETERS OR EMAIL
     * @param $search
     * @param $data
     * @return array
     */
    public static function getUserAccordingToSuppliedFields($search, $data)
    {
        $response = ONE::post([
            'component' => 'empatia',
            'api'       => 'auth',
            'method'    => 'getUserAccordingToFields',
            'params'    => [
                'search'     => $search,
                'email'      => isset($data['email']) ? $data['email'] : null,
                'parameters'   => isset($data['parameters']) ? $data['parameters'] : null,
            ]
        ]);
        if($response->statusCode() != 200){
            return ['error' => 500, 'message' => trans('comModulesAuth.failedToRetrieveUser')];
        }
        return $response->json();
    }


    /**
     * STORE A USER THREW IN PERSON REGISTRATION
     * @param $data
     * @param $userDetails
     * @return array
     */
    public static function storeInPersonRegistration($data, $userDetails)
    {
        $response = ONE::post([
            'component' => 'empatia',
            'api'       => 'auth',
            'params'    => [
                'name'       => isset($data['name']) ? $data['name'] : null,
                'email'      => isset($data['email']) ? $data['email'] : null,
                'password'   => isset($data['password']) ? $data['password'] : null,
                'identity_card'   => isset($data['identity_card']) ? $data['identity_card'] : null,
                'vat_number'   => isset($data['vat_number']) ? $data['vat_number'] : null,
                'parameters' => isset($userDetails) ? $userDetails : null
            ]
        ]);

        if($response->statusCode() != 201){
            if($response->statusCode() == 408){
                return ['error' => 408, 'message' => trans('comModulesAuth.parametersAlreadyUsed')];
            } elseif($response->statusCode() == 409) {
                return ['error' => 409, 'message' => trans('comModulesAuth.errorIdNumberOrEmailAlreadyExists')];
            }else{
                return ['error' => 500, 'message' => trans('comModulesAuth.errorInStoreUser')];
            }
        }
        return $response->json();
    }

    /**
     * @param $userKey
     * @return mixed
     * @throws Exception
     */
    public static function manuallyConfirmUserEmail($userKey) {
        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'auth',
            'method'    => 'manuallyConfirmUserEmail',
            'params'    => [
                'user_key'=> $userKey
            ]
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesAuth.errorManuallyConfirmingUserEmail"));
        }
        return $response->json();
    }
    /**
     * @param $userKey
     * @return mixed
     * @throws Exception
     */
    public static function manuallyConfirmUserSms($userKey) {
        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'auth',
            'method'    => 'manuallyConfirmUserSms',
            'params'    => [
                'user_key'=> $userKey
            ]
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesAuth.errorManuallyConfirmingUserSms"));
        }
        return $response->json();
    }

    public static function logout(){
        $response = ONE::get([
            'component' => 'empatia',
            'api' => 'auth',
            'method' => 'logout'
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesAuth.errorLogout"));
        }
        return $response->json();
    }

    public static function authenticateAlphanumeric($code){
        $response = ONE::post([
            'component' => 'empatia',
            'api' => 'auth',
            'method' => 'authenticateAlphanumeric',
            'params' => [
                'alphanumeric_code' => $code,
            ]
        ]);

        return $response->json();
    }

    public static function confirmEmail($confirmation_code){
        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'auth',
            'method'    => 'confirm',
            'attribute' => $confirmation_code
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesAuth.errorConfirmEmail"));
        }
        return $response->json();
    }

    public static function updateManager($requestUser, $userKey){
        $response = ONE::put([
            'component' => 'empatia',
            'api' => 'auth',
            'attribute' => $userKey,
            'params' => $requestUser->all()
        ]);
        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesAuth.errorUpdateManager"));
        }
        return $response->json();
    }

    public static function setAuthenticateRFID($rfid){
        $response = ONE::post([
            'component' => 'empatia',
            'api' => 'auth',
            'method' => 'authenticateRFID',
            'params' => [
                'rfid' => $rfid
            ]
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesAuth.errorSetAuthenticateRFID"));
        }
        return $response->json()->token;
    }

    public static function setUserPhoto($userKey, $fileId,$fileCode) {
        $response = ONE::put([
            'component' => 'empatia',
            'api'       => 'auth',
            'attribute' => $userKey,
            'params'    => [
                'photo_id'  => $fileId,
                'photo_code'  => $fileCode
            ],
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesAuth.failed_to_set_user_photo"));
        }
        return $response->json();
    }

    public static function validateToken() {
        $response = One::get([
            "component" => "empatia",
            "api"       => "auth",
            "method"    => "validate"
        ]);

        if($response->statusCode() != 200){
            throw new Exception("comModulesAuth.failed_to_validate_token");
        }
        return $response->json();
    }
}
