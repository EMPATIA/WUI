<?php

namespace App\ComModules;

use App\One\One;
use Exception;

class Social {

    public static function authenticateSocial($user, $facebook_secret, $facebook_id){
        $response = ONE::post([
            'component' => 'empatia',
            'api'       => 'auth',
            'method'    => 'authenticateSocial',
            'params'    => [
                'code' => "facebook",
                'email' => $user->getEmail(),
                'social_id' => $user->getId(),
                'name' => $user->getName(),
                'input_token' => $user->accessToken,
                'app_secret' => $facebook_secret,
                'app_id' => $facebook_id
            ]
        ]);

        if($response->statusCode() != 200 && $response->statusCode() != 401){
            throw new Exception(trans("comModulesSocial.errorFacebookAuthentication"));
        }


        return $response;
    }


    public static function storeSocialUser($userFacebook, $userEmpatia, $facebook_secret, $facebook_id){

        $response = ONE::post([
            'component' => 'empatia',
            'api'       => 'auth',
            'method'    => 'registerFacebookAccount',
            'params'    => [
                'code' => "facebook",
                'user_key' => $userEmpatia->user_key,
                'user_email' => $userEmpatia->email,
                'email' => $userFacebook->getEmail(),
                'social_id' => $userFacebook->getId(),
                'name' => $userFacebook->getName(),
                'input_token' => $userFacebook->accessToken,
                'app_secret' => $facebook_secret,
                'app_id' => $facebook_id
            ]
        ]);

        if($response->statusCode() != 201){
            throw new Exception(trans("comModulesSocial.error_storing_facebook_account"));
        }

        return $response->json();
    }

    public static function deleteSocialConnection($userKey)
    {
        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'auth',
            'method'    => 'removeFacebookAccount',
            'params'    => [
                'user_key' => $userKey,
            ]
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesSocial.error_removing_social_network"));
        }

        return $response->json();
    }

}
