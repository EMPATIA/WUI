<?php
namespace App\Http\Controllers;

session_start();

use App;
use App\ComModules\Files;
use App\ComModules\Social;
use App\ComModules\Orchestrator;
use App\Http\Requests\LoginCodeRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\One\One;
use App\One\OneLog;
use App\ComModules\LogsRequest;
//use Facebook\Exceptions\FacebookSDKException;
use Cache;
use Facebook\Facebook\Exceptions\FacebookSDKException;
use Faker\Provider\Image;
use HttpClient;
use Illuminate\Http\File;
use App\ComModules\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Mail;
use Redirect;
use Request;
use Session;
use View;
use Alert;
use Exception;
use Laravel\Socialite\Facades\Socialite;
use SammyK\LaravelFacebookSdk\LaravelFacebookSdk;
use Facebook\Facebook;
use App\ComModules\Notify;



class AuthSocialNetworkController extends Controller
{
    public function __construct()
    {

    }

    /**
     * Redirect the user to the Facebook authentication page.
     *
     * @param LaravelFacebookSdk $fb
     * @return Redirect
     */
    public function redirectToFacebook()
    {
        $facebook_secret = Session::all()['SITE-CONFIGURATION']['facebook_secret'];
        $facebook_id = Session::all()['SITE-CONFIGURATION']['facebook_id'];

        $fb = new Facebook([
            'app_id' => $facebook_id,
            'app_secret' => $facebook_secret,
            'default_graph_version' => 'v2.12',
        ]);

        $helper = $fb->getRedirectLoginHelper();

        $loginUrl = $helper->getReRequestUrl(action('AuthSocialNetworkController@handleFacebookCallback'), ['email', 'user_events']);

        return redirect($loginUrl);

    }


    /**
     * Obtain the user information from Facebook.
     *
     * @return Response
     */
    public function handleFacebookCallback()
    {
        $facebook_secret = Session::all()['SITE-CONFIGURATION']['facebook_secret'];
        $facebook_id = Session::all()['SITE-CONFIGURATION']['facebook_id'];

        $fb = new Facebook([
            'app_id' => $facebook_id,
            'app_secret' => $facebook_secret,
            'default_graph_version' => 'v2.12',
        ]);

        try{
            $helper = $fb->getRedirectLoginHelper();

        }catch(Exception $e){
            $jsonObj = json_encode(array('error' => "Failure: ".$e->getMessage() ));
            LogsRequest::setAccess('login_facebook',false, null,null,null,null,null,null, $jsonObj, 'authSocialNetwork.facebook_nok_connect',Session::has('user') ? Session::get('user')->user_key : 'anonymous');

            if (View::exists('public.'.ONE::getEntityLayout().'.auth.login')) {
                return redirect()->action("AuthController@login")->withErrors(["login.facebook" => "facebook_nok_connect"]);
            } else {
                return redirect()->action('PublicController@index')->withErrors(["errorAuth" => "facebook_nok_connect"]);
            }
        }

        try {
            $accessToken = $helper->getAccessToken(action("AuthSocialNetworkController@handleFacebookCallback") );
        } catch(FacebookResponseException $e) {
            // When Graph returns an error
            $jsonObj = json_encode(array('error' => "Failure: ".$e->getMessage() ));
            LogsRequest::setAccess('login_facebook',false, null,null,null,null,null,null, $jsonObj, 'authSocialNetwork.facebook_nok_connect',Session::has('user') ? Session::get('user')->user_key : 'anonymous');

            return redirect()->action("AuthController@login")->withErrors(["login.facebook" => "facebook_nok_connect"]);
        } catch(FacebookSDKException $e) {
            // When validation fails or other local issues
            $jsonObj = json_encode(array('error' => "Failure: ".$e->getMessage() ));
            LogsRequest::setAccess('login_facebook',false, null,null,null,null,null,null, $jsonObj, 'authSocialNetwork.facebook_nok_connect',Session::has('user') ? Session::get('user')->user_key : 'anonymous');

            if (View::exists('public.'.ONE::getEntityLayout().'.auth.login')) {
                return redirect()->action("AuthController@login")->withErrors(["login.facebook" => "facebook_nok_connect"]);
            } else {
                return redirect()->action('PublicController@index')->withErrors(["errorAuth" => "facebook_nok_connect"]);
            }
        }

        if (isset($accessToken)) {
            // Logged in!
            $_SESSION['facebook_access_token'] = (string) $accessToken;
            // Now you can redirect to another page and use the
            // access token from $_SESSION['facebook_access_token']
        } else {
            if (View::exists('public.'.ONE::getEntityLayout().'.auth.login')) {
                return redirect()->action("AuthController@login")->withErrors(["facebook" => "facebook_no_access_token"]);
            } else {
                return redirect()->action('PublicController@index')->withErrors(["errorAuth" => "facebook_no_access_token"]);
            }
        }

        // Sets the default fallback access token so we don't have to pass it to each request
        try {
            $fb->setDefaultAccessToken($accessToken);
        } catch(FacebookResponseException $e) {
            // When Graph returns an error
            $jsonObj = json_encode(array('error' => "Failure: ".$e->getMessage() ));
            LogsRequest::setAccess('login_facebook',false, null,null,null,null,null,null, $jsonObj, 'authSocialNetwork.facebook_nok_connect',Session::has('user') ? Session::get('user')->user_key : 'anonymous');

            if (View::exists('public.'.ONE::getEntityLayout().'.auth.login')) {
                return redirect()->action("AuthController@login")->withErrors(["facebook" => "facebook_nok_connect"]);
            } else {
                return redirect()->action('PublicController@index')->withErrors(["errorAuth" => "facebook_nok_connect"]);
            }
        }

        try {
            $response = $fb->get('/me?fields=id,name,email');
            $userNode = $response->getGraphUser();


        } catch(FacebookResponseException $e) {

            // When Graph returns an error
            $jsonObj = json_encode(array('error' => "Failure: ".$e->getMessage() ));
            LogsRequest::setAccess('login_facebook',false, null,null,null,null,null,null, $jsonObj, 'authSocialNetwork.facebook_nok_connect',Session::has('user') ? Session::get('user')->user_key : 'anonymous');

            if (View::exists('public.'.ONE::getEntityLayout().'.auth.login')) {
                return redirect()->action("AuthController@login")->withErrors(["login" => "facebook_nok_connect"]);
            } else {
                return redirect()->action('PublicController@index')->withErrors(["errorAuth" => "facebook_nok_connect"]);
            }

        } catch(FacebookSDKException $e) {

            // When validation fails or other local issues
            $jsonObj = json_encode(array('error' => "Failure: ".$e->getMessage() ));
            LogsRequest::setAccess('login_facebook',false, null,null,null,null,null,null, $jsonObj, 'authSocialNetwork.facebook_nok_connect',Session::has('user') ? Session::get('user')->user_key : 'anonymous');

            if (View::exists('public.'.ONE::getEntityLayout().'.auth.login')) {
                return redirect()->action("AuthController@login")->withErrors(["login" => "facebook_nok_connect"]);
            } else {
                return redirect()->action('PublicController@index')->withErrors(["errorAuth" => "facebook_nok_connect"]);
            }
        }
        $userNode->accessToken = $accessToken->getValue();
        if(empty($userNode->getEmail())){
            ONE::clearSession();
            OneLog::error("Could not Verify Email");
            $jsonObj = json_encode(array('error' => "Failure: Could not Verify Email" ));
            LogsRequest::setAccess('login_facebook',false, null,null,null,null,null,null, $jsonObj, "authSocialNetwork.email_permission",Session::has('user') ? Session::get('user')->user_key : 'anonymous');

            if (View::exists('public.'.ONE::getEntityLayout().'.auth.login')) {
                return redirect()->action("AuthController@login")->withErrors(["login" => "email_permission"]);
            } else {
                return redirect()->action('PublicController@index')->withErrors(["errorAuth" => "email_permission"]);
            }
        }

        ONE::forceEntityKeyFromURL();
        $entity = Orchestrator::getSiteEntity( $_SERVER["HTTP_HOST"] );

        if(Session::has('user')){
            try {
                $user = Session::get('user');
                $login = Social::storeSocialUser($userNode, $user, $facebook_secret, $facebook_id);
                /*
                $email = $userNode->getEmail();
                $emailType = 'registry_facebook';
                $tags = [
                    "#name" => $user->name
                ];
                $response = Notify::sendEmail($emailType, $tags, (array)$user);
                */
                LogsRequest::setAccess('store_social_user',true, null,null,null,null,null,null, null, 'authSocialNetwork.store_ok',Session::has('user') ? Session::get('user')->user_key : 'anonymous');

                Session::flash('message', trans('authSocialNetwork.store_ok'));
                // return redirect()->action('PublicUsersController@edit',['userKey' => Session::get('user')->user_key,'f' => 'user']);
                return redirect()->action('PublicUsersController@show',['userKey' => Session::get('user')->user_key]);
            }catch(Exception $e){
                $jsonObj = json_encode(array('error' => "Failure: ".$e->getMessage() ));
                LogsRequest::setAccess('login_facebook',false, null,null,null,null,null,null, $jsonObj, null,Session::has('user') ? Session::get('user')->user_key : 'anonymous');

                return redirect()->back()->withErrors(['authSocialNetwork' => $e->getMessage()]);
            }


        }else{
            $login = Social::authenticateSocial($userNode, $facebook_secret, $facebook_id);
            if( $login->statusCode() == 200 && isset($login->json()->token) ){

                if(isset($login->json()->user_key) && $login->json()->login == 0){
                    $response = Orchestrator::storeUser($login->json()->user_key, $entity->entity_id, 'facebook', $userNode->getId());
                }

                $authToken = $login->json()->token;
                /* TODO: Check User Role */
                Session::put('X-AUTH-TOKEN', $authToken);

                $userInformation = Auth::getUser();
                Session::put('user', $userInformation);

                return $this->checkRoleUser($userInformation);

            }else{
                OneLog::error("Verify Login: ".$login->json()->error);
                $jsonObj = json_encode(array('error' => "Failure: ".$login->json()->error ));
                LogsRequest::setAccess('login_facebook',false, null,null,null,null,null,null,$jsonObj, 'authSocialNetwork.message_email_already_exists', Session::has('user') ? Session::get('user')->user_key : 'anonymous');

                if (View::exists('public.'.ONE::getEntityLayout().'.auth.login')) {
                    return redirect()->action("AuthController@login")->withErrors(["login" => "message_email_already_exists"]);
                } else {
                    return redirect()->action('PublicController@index')->withErrors(["errorAuth" => "message_email_already_exists"]);
                }
            }
        }
    }

    private function checkRoleUser($user , $loginQRCode = false,$login = false)
    {
        try {

            $private = 0;

            $userValidate = Orchestrator::getUserAuthValidate();

            if(isset($userValidate->admin) && $userValidate->admin == 1){
                $role = 'admin';
                Session::put('AUTH', false);
            } else if ($userValidate->status=="needsEntityMigration") {
                Session::put("userNeedsEntityMigration",true);
                $role = "";
            } else
                $role = $userValidate->role;

            if (($role == 'admin' || $role == 'manager') ){
                if($role != 'admin'){
                    $userPermissionsList = $userValidate->permissions;;

                    if(isset($userPermissionsList)){
                        Session::put('user_permissions', $userPermissionsList);
                        $this->userPermissionsForSidebar($userPermissionsList);
                        $this->userPermissionsGroupForSidebar($userPermissionsList);
                        Session::put('user_role', 'manager');
                    }
                }else{
                    Session::put('user_permissions_sidebar', ['all']);
                    Session::put('user_permissions_sidebar_groups', ['all']);
                    Session::put('user_role', 'admin');
                }
                if(Session::get('X-ENTITY-KEY', 'undefined') !== 'undefined'){
                    ONE::verifyModulesActive();
                }
                $private = 1;
            }
            if($role != 'admin' && $role != 'manager') {


                switch ($userValidate->status) {
                    case 'needsEntityMigration':
                        // Session::flash("X-AUTH-TOKEN-MIGRATE", Session::get("X-AUTH-TOKEN"));
                        Session::put("X-AUTH-TOKEN-MIGRATE", Session::get("X-AUTH-TOKEN"));
                        Session::remove("X-AUTH-TOKEN");

                        Session::put('AUTH', true);
                        Session::put('USER-STATUS', $userValidate->status);

                        return redirect()->action('AuthController@migrateUserToEntityConfirmation');
                        break;
                    default:
                        $userLevel = Orchestrator::getUserLevel($user->user_key);
                        $user->user_level = $userLevel->position ?? 0;
                        $siteLoginLevels = Orchestrator::getSiteLoginLevels();

                        if($login){
                            if(Session::has('url_previous')){
                                $url = Session::get('url_previous');
                                Session::forget('url_previous');
                                return  Redirect::to($url);
                            }
                            return redirect()->action('PublicController@index');
                        }
                        if (!empty(Session::get("SITE-CONFIGURATION.boolean_basic_registration_only"))){
                            return redirect()->action('AuthController@showSuccess');
                        }
                        /** Verify previous URL and redirect user*/
                        if(Session::has('url_previous')){
                            $url = Session::get('url_previous');
                            Session::forget('url_previous');
                            return  Redirect::to($url);
                        }else{
                            return redirect('public');
                        }
                }
            }
            OneLog::info("Login done USER[".$role."]: ".$user->email);

            LogsRequest::setAccess('login_facebook',true, null,null,null,null,null,null,null, null, $user->user_key);


            if($private == 1){

                /** Verify previous URL and redirect user*/
                if(Session::has('url_previous')){
                    $url = Session::get('url_previous');
                    Session::forget('url_previous');
                    return  Redirect::to($url);
                }

                if(ONE::verifyModuleAccess('wui','wizard')){
                    return redirect()->action('PresentationController@show');
                }

                if(Session::has('url_previous')){
                    $url = Session::get('url_previous');
                    Session::forget('url_previous');

                    return  Redirect::to($url);
                }
                return redirect('private');
            }
            elseif($private == 0 || $loginQRCode){
                return redirect('public');
            }

        } catch (Exception $e) {
            ONE::clearSession();

            OneLog::error("Login [checkRoleUser]".$e->getMessage());
            $jsonObj = json_encode(array('error' => "Failure: ".$e->getMessage() ));
            LogsRequest::setAccess('login_facebook',false, null,null,null,null,null,null,$jsonObj, null, $user->user_key);
            return redirect()->back()->withErrors(["auth.role" => $e->getMessage()]);
        }
    }

    public function removeFacebook(){
        $user = Session::get('user');
        Social::deleteSocialConnection($user->user_key);
        Session::flash('message', trans('authSocialNetwork.remove_ok'));

        return redirect()->back();
    }

}
