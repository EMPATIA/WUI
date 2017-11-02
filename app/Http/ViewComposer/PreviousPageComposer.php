<?php

namespace App\Http\ViewComposer;

use App\One\One;
use Illuminate\Contracts\View\View;
use Request;
use Session;
use URL;

/**
 *
 * This Composer class is for save the last page visited by user
 *
 */
class PreviousPageComposer
{

    public function __construct()
    {

    }

    /**
     * Save URL of the views
     *
     * @return void
     */
    public function compose()
    {
        /** Check if current URL is not any of the actions*/
        $actionsBlackList = array(
            'App\Http\Controllers\AuthController@login',
            'App\Http\Controllers\AuthController@register',
            'App\Http\Controllers\AuthController@editPassword',
            'App\Http\Controllers\AuthController@updatePassword',
            'App\Http\Controllers\AuthController@migrateUserToEntityConfirmation',
            'App\Http\Controllers\AuthController@migrateUserToEntity',
        );

        if(!Request::ajax() && !empty(\Route::current()) && isset(\Route::current()->getAction()["controller"]) && !in_array(\Route::current()->getAction()["controller"],$actionsBlackList)) {            /** Save URL for redirect in case of login lost*/
            $previousUrl = URL::full();
            Session::put('url_previous', $previousUrl);
        }
    }
}