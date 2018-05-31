<?php

namespace App\Http\Controllers;

use App\ComModules\EMPATIA;
use App\ComModules\Files;
use App\ComModules\LogsRequest;
use App\ComModules\Vote;
use Carbon\Carbon;
use ClassPreloader\Factory;
use Illuminate\Support\Facades\Hash;
use App\ComModules\Notify;
use App\ComModules\Auth;
use App\ComModules\Social;
use App\ComModules\Orchestrator;
use App\Http\Requests\LoginCodeRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\PasswordRecoveryRequest;
use App\Http\Requests\PasswordUpdateRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\QuestionnaireRegisterRequest;
use App\One\One;
use App\One\OneForm;
use App\One\OneLog;
use Cache;
use Input;
use Mail;
use Redirect;
use Illuminate\Http\Request;
use Session;
use View;
use Alert;
use Exception;
use Laravel\Socialite\Facades\Socialite;
use URL;
use Faker\Factory as Faker;



class AuthController extends Controller
{
    public function __construct()
    {

    }


    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        return view('auth.login');
    }


    /**
     * Displays login for admins.
     *
     * @return View Login
     */
    public function adminLogin()
    {
        ONE::clearSession();
        ONE::clearEntitySession();
        return view('auth.login',["adminLogin"=>true]);
    }


    /**
     * Displays login.
     *
     * @return View Login
     */
    public function login(Request $request)
    {
        //ONE::clearSession();
        $facebook_login=false;
        $google_login=false;
        //facebook/google login link appear or not

        $authMethods = Orchestrator::getAuthMethodsList();
        $facebookAuthMethod = false;
        $googleAuthMethod = false;
        foreach($authMethods as $methods){
            if($methods->code == 'facebook')
                $facebookAuthMethod = true;
            if($methods->code == 'google')
                $googleAuthMethod = true;
        }

        if(!empty(Session::all()['SITE-CONFIGURATION']['facebook_secret']) and !empty(Session::all()['SITE-CONFIGURATION']['facebook_id']) and $facebookAuthMethod)
            $facebook_login = true;

        if (View::exists('public.'.ONE::getEntityLayout().'.auth.login')) {
            return view('public.'.ONE::getEntityLayout().'.auth.login', compact('facebook_login', 'googleAuthMethod'));
        }
        return view('auth.login', compact('facebook_login', 'googleAuthMethod'));
    }


    /**
     * Logout.
     */
    public function logout() {
        //OneLog::info("LogOut");

        Auth::logout();

        ONE::clearSession();
        return redirect()->action('PublicController@index');
    }

    public static function logoutForVoteRegistration() {
        //OneLog::info("LogOut");

        Auth::logout();

        ONE::clearSession();
    }

    /**
     * @return View Register
     */
    public function register()
    {

        $response = Orchestrator::getSiteUseTerm();

        if(isset($response)){
            $email = isset($response->no_reply_email) ? $response->no_reply_email : null;
            $useTerms = isset($response->use_terms) ? html_entity_decode($response->use_terms->content) : null;
        }else{
            $email = $response;
            $useTerms = $response;
        }

        $registerParametersResponse = Orchestrator::getEntityRegisterParameters();

        //verify user parameters with responses
        $registerParameters = [];
        foreach ($registerParametersResponse as $parameter){
            $parameterOptions = [];
            $value = '';
            $file = null;
            if($parameter->parameter_type->code == 'radio_buttons' || $parameter->parameter_type->code == 'check_box' || $parameter->parameter_type->code == 'dropdown') {
                foreach ($parameter->parameter_user_options as $option) {
                    $selected = false;
                    if (isset($userParametersResponse[$parameter->parameter_user_type_key])) {
                        foreach ($userParametersResponse[$parameter->parameter_user_type_key] as $userOption) {
                            if($userOption['value'] == $option->parameter_user_option_key){
                                $selected = true;
                                break;
                            }
                        }
                    }
                    $parameterOptions [] = [
                        'parameter_user_option_key' => $option->parameter_user_option_key,
                        'name' => $option->name,
                        'selected' => $selected
                    ];
                }
            }elseif($parameter->parameter_type->code == 'file'){
                $id = isset($userParametersResponse[$parameter->parameter_user_type_key][0]) ? $userParametersResponse[$parameter->parameter_user_type_key][0]['value'] : '';
                if($id != ''){
                    $file = json_decode(json_encode(Files::getFile($id)),true);
                }

            }else{
                $value = isset($userParametersResponse[$parameter->parameter_user_type_key][0]) ? $userParametersResponse[$parameter->parameter_user_type_key][0]['value'] : '';
            }
            $registerParameters []= [
                'parameter_user_type_key'   => $parameter->parameter_user_type_key,
                'parameter_type_code'       => $parameter->parameter_type->code,
                'name'                      => $parameter->name,
                'code'                      => $parameter->code,
                'value'                     => isset($file) ? $file : $value,
                'mandatory'                 => $parameter->mandatory,
                'parameter_user_options'    => $parameterOptions
            ];
        }
        
        if(Session::get('SITE-CONFIGURATION.boolean_register_only_nif')==true){
            return view('public.'.ONE::getEntityLayout().'.auth.registerPenha', compact('useTerms', 'email', 'registerParameters'));
        }

        return view('public.'.ONE::getEntityLayout().'.auth.register', compact('useTerms', 'email', 'registerParameters'));
    }


    /**
     * @return View recovery
     */
    public function recovery()
    {
        return view('public.'.ONE::getEntityLayout().'.auth.recovery');
    }

    /**
     * @param PasswordRecoveryRequest $request
     * @return \Illuminate\Http\RedirectResponse|View
     */
    public function passwordRecovery(PasswordRecoveryRequest $request)
    {
        try{
            $email = $request->email;
            $user = Auth::passwordRecovery($email);
            // Email / Notify
            $emailType = 'password_recovery';
            $tags = [
                "name" => $user->name,
                "link" => URL::action("AuthController@editPassword",['userKey' => $user->user_key, 'recoverToken' => $user->recover_password_token])
            ];

            LogsRequest::setAccess('password_recovery',true, null,null,null,null,null,null,null, 'Email: '.$request->email, $user->user_key);

            $response = Notify::sendEmail($emailType, $tags, (array) $user);

            Session::flash('passwordRecovery', true);
            Session::flash('message', trans('auth.passwordRecoveryEmailSentOk'));
            if(View::exists('public.'.ONE::getEntityLayout().'.auth.recoverySuccess')){
                return redirect()->action('SubPagesController@show', ["auth","recoverySuccess"]);
            }else{
                return redirect()->action('PublicController@index');
            }

        }catch (Exception $e) {
            Session::flash('passwordRecovery', false);
            $jsonObj = json_encode(array('error' => "Failure: ".$e->getMessage(), 'Email' => $request->email ));
            LogsRequest::setAccess('password_recovery',false, null,null,null,null,null,null, $jsonObj, null, Session::has('user') ? Session::get('user')->user_key : null);

            return redirect()->back()->withErrors([trans('auth.passwordRecoveryEmailSentNOk') => $e->getMessage()]);
        }
    }


    /**
     * @param $userKey
     * @param $recoverToken
     * @return View recovery
     */
    public function editPassword($userKey,$recoverToken)
    {
        return view('public.'.ONE::getEntityLayout().'.auth.updatePassword',compact('userKey','recoverToken'));
    }


    /**
     * @param PasswordUpdateRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updatePassword(PasswordUpdateRequest $request)
    {
        try{
            $userKey = $request->userKey;
            $recoveryToken = $request->recoverToken;
            $password = $request->password;

            $user = Auth::updatePasswordRecovery($userKey,$recoveryToken,$password);

            // Email / Notify
            $emailType = 'reset_password_confirmation';
            $tags = [
                "name" => $user->name,
                "link" => URL::action("AuthController@login")
            ];
            $response = Notify::sendEmail($emailType, $tags, (array) $user);

            Session::flash('message', trans('auth.passwordUpdateEmailSentOk'));
            Session::flash('message', trans('auth.updatePasswordOk'));
            Session::remove("url_previous");
//            return redirect()->action('AuthController@login');
            if (View::exists('public.'.ONE::getEntityLayout().'.auth.recoveryMessageSuccess')) {
                return view('public.'.ONE::getEntityLayout().'.auth.recoveryMessageSuccess');
            }
        }catch (Exception $e) {
            Session::flash('passwordRecovery', false);

            return redirect()->back()->withErrors([trans('auth.updatePasswordError') => $e->getMessage()]);
        }
    }


    /**
     * User login.
     *
     * @param LoginRequest $request
     * @internal param TopicRequest $requestTopic
     * @return $this|\Illuminate\Http\RedirectResponse|Redirect
     */
    public function verifyLogin(LoginRequest $request)
    {

        try {
            if (!empty($request["email"] && !empty($request["password"]))){
                $response = Auth::login($request["email"], $request["password"]);
            }

            if(!empty($response->token)){

                $authToken = $response->token;
                /* TODO: Check User Role */

                Session::put('X-AUTH-TOKEN', $authToken);
                $userRequest = Auth::getUser();

                LogsRequest::setAccess('login',true, null,null,null,null,null,null,null, null, $userRequest->user_key);

                /** Get User Login Level*/

                $userLoginLevels = Orchestrator::getUserLoginLevels($userRequest->user_key);
                $userRequest->user_login_levels = $userLoginLevels;
                Session::put('user', $userRequest);

                if ((isset($response->user_key)) && (isset($response->libertrium))){
                    $response = Orchestrator::storeUser($response->user_key, ONE::getEntityKey());
                }
                elseif(isset($response->user_key)){
                    /*Create User in orchestrator*/
                    Orchestrator::createUser($response);
                }

                return $this->checkRoleUser($userRequest,false,true);
            }else{
                ONE::clearSession();
                //OneLog::error("Verify Login: ".$response->json()->error);
                $jsonObj = json_encode(array('error' => "Failure: Login Failed ", 'Request' => $request->email ));
                LogsRequest::setAccess('login',false, null,null,null,null,null,null,'errorLogin',  $jsonObj, Session::has('user') ? Session::get('user')->user_key : null);
                Session::flash('errorLogin', true);

                return redirect()->back()->withInput($request->except('password'));
            }
        } catch (Exception $e) {
            //OneLog::error("Verify Login: ".$e->getMessage());
            $jsonObj = json_encode(array('error' => "Failure: Login Failed ".$e->getMessage(), 'Request' => $request->email ));

            LogsRequest::setAccess('login',false, null,null,null,null,null,null,null, $jsonObj, Session::has('user') ? Session::get('user')->user_key : 'anonymous');
            return redirect()->back()->withErrors(["errorAuth" => $e->getMessage()])->withInput($request->except('password'));
        }
    }

    /**
     * User login By code.
     *
     * @param LoginCodeRequest $request
     * @return Response
     * @internal param LoginRequest $requestLoginCode
     * @internal param TopicRequest $requestTopic
     */
    public function verifyLoginCode(LoginCodeRequest $request)
    {
        try {
            Session::put('redirect','public');

            $code = $request["code"];
            $response = Auth::authenticateAlphanumeric($code);
            if($response->statusCode() == 200){


                $authToken = $response->json()->token;

                /* TODO: Check User Role */
                Session::put('X-AUTH-TOKEN', $authToken);

                $userInformation = Auth::getUser();;
                Session::put('user', $userInformation);

                return $this->checkRoleUser($userInformation);
            }else{
                ONE::clearSession();
                //OneLog::error("Verify Login: ".$response->json()->error);

                Alert::error($response->json()->error, 'Oops!');

                return redirect()->back()->withInput($request->except('password'));
            }

        } catch (Exception $e) {
            //OneLog::error("Verify Login: ".$e->getMessage());
            return redirect()->back()->withErrors(["topic.store" => $e->getMessage()])->withInput($request->except('password'));;
        }
    }


    /**
     * User login By code.
     *
     * @param LoginCodeRequest|Request $request
     * @param $code
     * @return Response
     * @internal param LoginRequest $requestLoginCode
     * @internal param TopicRequest $requestTopic
     */
    public function verifyLoginCodeLink(Request $request, $code)
    {
        try {
            Session::put('redirect','public');
            $response = Auth::authenticateAlphanumeric($code);
            if($response->statusCode() == 200){


                $authToken = $response->json()->token;

                /* TODO: Check User Role */
                Session::put('X-AUTH-TOKEN', $authToken);

                $userInformation = Auth::getUser();
                Session::put('user', $userInformation);

                return $this->checkRoleUser($userInformation, true);
            }else{
                Session::forget('X-AUTH-TOKEN');
                //OneLog::error("Verify Login: ".$response->json()->error);

                Alert::error($response->json()->error, 'Oops!');

                return redirect('public');
            }

        } catch (Exception $e) {
            ONE::clearSession();
            //OneLog::error("Verify Login: ".$e->getMessage());
            return redirect()->back()->withErrors(["topic.store" => $e->getMessage()])->withInput($request->except('password'));;
        }
    }
    /**
     * Verify and Create a new User
     *
     * @param RegisterRequest $request
     * @return Response
     * @internal param RegisterRequest $requestRegister
     * @internal param LoginRequest $requestLogin
     */
    public function verifyRegister(RegisterRequest $request)
    {
        try {
            if (!empty(Session::get('SITE-CONFIGURATION')['recaptcha_site_key']) &&  !empty(Session::get('SITE-CONFIGURATION')['recaptcha_secret_key'])){
                if(!empty(Input::get('g-recaptcha-response'))){
                    if(!ONE::verifyRecaptcha(Session::get('SITE-CONFIGURATION')['recaptcha_secret_key'], Input::get('g-recaptcha-response'))){
                        return redirect()->back()->withErrors(['auth.verifyRegister' => ONE::transSite('defaultRegister.captcha_login_error')])->withInput($request->except('password'));
                    }
                }else{
                    return redirect()->back()->withErrors(['auth.verifyRegister' => ONE::transSite('defaultRegister.captcha_login_error')])->withInput($request->except('password'));
                }
            }

            $response = Auth::storeUser($request["name"], $request["email"], $request["password"], $request["surname"]);

            $user = $response->user;
            $confirmation_code = $response->confirmation_code;

            $entity = ONE::getEntityKey();
            Orchestrator::createEntityUser($user, $entity);

            //TODO: verify response from orchestrator
            if (Session::get('SITE-CONFIGURATION.boolean_no_email_confirmation',false)) {
                $this->confirmEmail($user->confirmation_code);
            } else {
                // Email / Notify
                $emailType = 'registry_confirmation';
                $tags = [
                    "name" => $user->name,
                    "link" => URL::action('AuthController@confirmEmail', $user->confirmation_code)
                ];
                Notify::sendEmail($emailType, $tags, (array)$user);

                $this->updateUser($request, $user->user_key, false);

                Alert::info(trans('auth.confirmationEmailSend'));
                Session::flash('message', trans('register.confirmation.email.sent'));
            }

            Session::flash('message', trans('register.success'));
            //OneLog::info("Registration Success.");


            $request['auth.wait.registration'] = 1;

            /*if (View::exists('public.' . ONE::getEntityLayout() . '.auth.successMessage')) {
                return view('public.' . ONE::getEntityLayout() . '.auth.successMessage');
            }*/
            return redirect()->action('AuthController@login')->withInput($request->except('password'));

        } catch (Exception $e) {
            ONE::clearSession();
            return redirect()->back()->withErrors(["register.error" => $e->getMessage()])->withInput($request->except('password'));
        }
    }

    /**
     * Verify, Create a new User and login
     *
     * @param RegisterRequest $request
     * @return AuthController|Controller|\Illuminate\Http\RedirectResponse|Redirect
     * @internal param RegisterRequest $requestRegister
     * @internal param LoginRequest $requestLogin
     */
    public function verifyRegisterAndLogin(RegisterRequest $request)
    {
        \Log::info("AUTH_REGISTER New user [E: ".$request["email"]."][N: ".$request["name"]."] New");

        \Log::info(">>>> ERROR: 1");
        try {
            if (!empty(Session::get('SITE-CONFIGURATION')['recaptcha_site_key']) &&  !empty(Session::get('SITE-CONFIGURATION')['recaptcha_secret_key'])){

                if(!empty(Input::get('g-recaptcha-response'))){
                    if(!ONE::verifyRecaptcha(Session::get('SITE-CONFIGURATION')['recaptcha_secret_key'], Input::get('g-recaptcha-response'))){
                        $jsonObj = json_encode(array('error' => "Failure: Recaptcha", 'Email' => $request->email,'Name'=>$request->name ));
                        LogsRequest::setAccess('new_registration',false, null,null,null,null,null,null,$jsonObj, null, Session::has('user') ? Session::get('user')->user_key : null);
                        \Log::info("AUTH_REGISTER New user [E: ".$request["email"]."][N: ".$request["name"]."] Failure - Recaptcha");
                        return redirect()->back()->withErrors(['auth.verifyRegisterAndLogin' => ONE::transSite('defaultRegister.captcha_login_error')])->withInput($request->except('password'));
                    }
                }else{
                    $jsonObj = json_encode(array('error' => "Failure: Recaptcha", 'Email' => $request->email,'Name'=>$request->name ));
                    LogsRequest::setAccess('new_registration',false, null,null,null,null,null,null, $jsonObj, null, Session::has('user') ? Session::get('user')->user_key : null);
                    \Log::info("AUTH_REGISTER New user [E: ".$request["email"]."][N: ".$request["name"]."] Failure - Recaptcha");
                    return redirect()->back()->withErrors(['auth.verifyRegisterAndLogin' => ONE::transSite('defaultRegister.captcha_login_error')])->withInput($request->except('password'));
                }
            }

            ONE::clearSession();

            $parameters = Orchestrator::getEntityRegisterParameters();

            foreach($parameters as $parameter) {
                if(!empty($request[$parameter->parameter_user_type_key])){
                    if($parameter->code == 'cc'){
                        \Log::info(">>>> ERROR: CC");

                        $cc = $parameter->parameter_user_type_key;

                        if(isset($request->$cc)){
                            if(strlen(trim(str_replace( ' ', '', $request->$cc))) == 6) {
                                \Log::info("AUTH_REGISTER New user [E: ".$request["email"]."][N: ".$request["name"]."] CC_RESIDENCIA_6 - ".$request->$cc);
                                break;
                            }
                            if(strlen(trim(str_replace( ' ', '', $request->$cc))) == 9) {
                                \Log::info("AUTH_REGISTER New user [E: ".$request["email"]."][N: ".$request["name"]."] CC_RESIDENCIA_9 - ".$request->$cc);
                                break;
                            }


                            \Log::info("AUTH_REGISTER New user [E: ".$request["email"]."][N: ".$request["name"]."] CC - ".$request->$cc);
                            if(!$this->validateNumberCC($request->$cc)) {
                                $jsonObj = json_encode(array('error' => "Failure: ".$request->$cc, 'Email' => $request->email,'Name'=>$request->name ));
                                LogsRequest::setAccess('new_registration',false, null,null,null,null,null,null,$jsonObj, null, Session::has('user') ? Session::get('user')->user_key : null);
                                \Log::info("AUTH_REGISTER New user [E: ".$request["email"]."][N: ".$request["name"]."] CC_Failure - ".$request->$cc);
                                Throw new Exception(trans('user.cc_not_validated'));
                            }
                            \Log::info("AUTH_REGISTER New user [E: ".$request["email"]."][N: ".$request["name"]."] CC_OK - ".$request->$cc);
                        }
                    }

                    if($parameter->external_validation == 1){
                        $parameter_user_key = $parameter->parameter_user_type_key;
                        $value = $request[$parameter_user_key];
                        $vatNumberExist = Orchestrator::validateVatNumber($value);
                        if($vatNumberExist->vat_number != 1){
                            return redirect()->back()->withErrors(["registerError" => ONE::transSite('register.register_'.$parameter->name.'_number_not_valid'),"parameterUserKey" => $parameter_user_key,"parameterValue" => $value])->withInput($request->except('password'));
                        }else{
                            $vatNumber = $vatNumberExist->vat_number;
                            $isUnique = Auth::verifyVatNumber($parameter_user_key,$value,$vatNumber);
                            if($isUnique != 1){
                                return redirect()->back()->withErrors(["registerError" => ONE::transSite('register.register_'.$parameter->name.'_already_exist'),"parameterUserKey" => $parameter_user_key,"parameterValue" => $value])->withInput($request->except('password'));
                            }
                        }
                    }else{

                        if($parameter->mandatory == 1 && $parameter->parameter_unique == 1){
                            $parameter_user_key = $parameter->parameter_user_type_key;
                            $value = $request[$parameter_user_key];
                            $isUnique = Auth::verifyVatNumber($parameter_user_key,$value);
                            if($isUnique != 1){
                                return redirect()->back()->withErrors(["registerError" => ONE::transSite('register.register_parameter_unique_already_exist'),"parameterUserKey" => $parameter_user_key,"parameterValue" => $value])->withInput($request->except('password'));
                            }
                            if($parameter->code == 'nif') {
                                if (!$this->validateNifFormat($value)){
                                    return redirect()->back()->withErrors(["registerError" => ONE::transSite('register.register_parameter_unique_mal_formed'),"parameterUserKey" => $parameter_user_key,"parameterValue" => $value])->withInput($request->except('password'));
                                }
                            }
                        }
                    }
                }
            }

            \Log::info(">>>> ERROR: Done CC");

            if(!empty(Session::get("SITE-CONFIGURATION.boolean_no_email_needed")) && $request->get("withoutemail",0)==1) {
                if($parameter->mandatory == 1 && $parameter->parameter_unique == 1){
                    $parameter_user_key = $parameter->parameter_user_type_key;
                    $value = $request[$parameter_user_key];
                }
                $noEmail = 1;
                $request['name'] = 'registration_'.$value;
                $request["email"] = $this->generateFakeEmail($request['name'],$request['surname'],$value,$_SERVER['HTTP_HOST'],$noEmail);
                $request["password"] = $request["email"];
            }

            $name = $request["name"];
            $surname = $request["surname"];
            $email = $request["email"];
            $password = $request["password"];
            // $user = Auth::storeUser($name,$email,$password,$surname);

            $params = [];
            $params["name"] = $name;
            $params["surname"] = $surname;
            $params["email"] = $email;
            $params["password"] = $password;
            if(!empty($request["identity_card"])) {
                $params["identity_card"] = $request["identity_card"];
            }
            
            \Log::info(">>>> ERROR: 5");
            $user = Auth::storeUserV2($params);

            $user = $user->user;
            LogsRequest::setAccess('new_registration',true, null,null,null,null,null,null,null, null, $user->user_key);

            \Log::info(">>>> ERROR: 6: ".$user->user_key." ".ONE::getEntityKey());
            if(isset($user->user_key)) {
                $response = Orchestrator::storeUser($user->user_key, ONE::getEntityKey());
            }

            \Log::info(">>>> ERROR: 7");
            Session::put('new_registration',true);

            Session::flash('message', trans('register.success'));
            //OneLog::info("Registration Success.");
            $login = Auth::login($email,$password);

            $authToken = $login->token;

            /* TODO: Check User Role */
            Session::put('X-AUTH-TOKEN', $authToken);

            $userInformation = Auth::getUser();
            Session::put('user', $userInformation);
            $this->updateUser($request, $user->user_key);


            \Log::info("AUTH_REGISTER New user [E: ".$email."][N: ".$name."] Success");

            if (Session::get('SITE-CONFIGURATION.boolean_no_email_confirmation',false)) {
                $this->confirmEmail($user->confirmation_code);
            } elseif(env("DEMO_MODE",false)==true){
                return $authToken;
            }
            else {
                $this->sendConfirmEmail($request);
                Session::flash('message', trans('register.confirmationEmailSent'));

                if(Session::get('SITE-CONFIGURATION.boolean_register_only_nif')==true){
                    return view('public.'.ONE::getEntityLayout().'._layouts.index');
                }

                if (View::exists('public.' . ONE::getEntityLayout() . '.auth.successMessage')) {
                    return view('public.' . ONE::getEntityLayout() . '.auth.successMessage');
                }

            }

#	    \Log::info("AUTH_REGISTER New user [E: ".$email."][N: ".$name."] Failure - unknown status");

            //if not activated return view to complete registration
            return $this->checkRoleUser($userInformation);

        } catch (Exception $e) {
            $name = $request["name"];
            $email = $request["email"];
            $password = $request["password"];

            $params = [];
            $params["name"] = $name;
            $params["email"] = $email;
            $params["password"] = $password;
            if(!empty($request["identity_card"])) {
                $params["identity_card"] = $request["identity_card"];
            }

            ONE::clearSession();
            //OneLog::error("Verify Register".$e->getMessage());

            $jsonObjCreateRegister = json_encode(array($params));

            $jsonObj = json_encode(array('error' => "Failure: ".$e->getMessage(), 'Email' => $request->email,'Name'=>$request->name ));
            LogsRequest::setAccess('new_registration',false, null,null,null,null,null,null, $jsonObj, $jsonObjCreateRegister,Session::has('user') ? Session::get('user')->user_key : null);

            \Log::info("AUTH_REGISTER New user [E: ".$request["email"]."][N: ".$request["name"]."] Failure - ".$e->getMessage());
            \Log::info(">>>> ERROR: >> ".$e->getMessage());
            return redirect()->back()->withErrors([trans('auth.verifyRegisterAndLogin') => $e->getMessage()])->withInput($request->except('password'));
        }

    }

    public function questionnaireRegisterAndLogin(QuestionnaireRegisterRequest $request)
    {
        try {
            ONE::clearSession();

            /* Email and password */
            $email = $request->email;
            $password = $request->password;

            if(empty($password)) {
                $password = Hash::make(str_random(8));
            }

            /* Auth - store user - [Auth::storeNewUser] */
            $userDetails = $request->all();
            $data['name'] = $request->name;
            $data['email'] = $request->email;
            $data['password'] = $password;
            $data['identity_card'] = isset($request->identity_card) ? $request->identity_card : null;
            $data['vat_number'] = isset($request->vat_number) ? $request->vat_number : null;
            unset($userDetails['_token']);
            unset($userDetails['name']);
            unset($userDetails['email']);
            unset($userDetails['questionnaire_key']);

            /* Questionnaire key */
            $questionnaireKey = $request->questionnaire_key;

            if(empty($questionnaireKey)){
                throw new Exception(trans('register.no_questionnaire_key'));
            }

            $user = Auth::storeNewUser($data,$userDetails);

            /* Orchestrator - store user */
            $user = $user->user;
            if(isset($user->user_key)) {
                $response = Orchestrator::storeUser($user->user_key, ONE::getEntityKey());
            }

            /* Messages and Log */
            Session::flash('message', trans('register.success'));
            // Session::flash('message', trans('register.confirmationEmailSent'));
            //::info("Registration Success.");

            /* Force Login */
            $login = Auth::login($email,$password);
            $authToken = $login->token;

            /* TODO: Check User Role */
            Session::put('X-AUTH-TOKEN', $authToken);

            /* Store user information in session */
            $userInformation = Auth::getUser();
            Session::put('user', $userInformation);

            /* Redirect to Questionnaire */
            return redirect()->action('PublicQController@showQ', $questionnaireKey);

        } catch (Exception $e) {
            ONE::clearSession();
            //OneLog::error("Verify Register".$e->getMessage());
            return redirect()->back()->withErrors([trans('auth.verifyRegisterAndLogin') => $e->getMessage()])->withInput($request->except('password'));
        }
    }


    public function questionnaireVerifyAndLogin(Request $request)
    {
        try {
            ONE::clearSession();

            /* Email and password */
            $email = $request->login_email;
            $password = $request->login_password;

            /* Questionnaire key */
            $questionnaireKey = $request->questionnaire_key;

            if(empty($questionnaireKey)){
                throw new Exception(trans('register.no_questionnaire_key'));
            }

            /* Force Login */
            $login = Auth::login($email,$password);
            $authToken = $login->token;

            /* TODO: Check User Role */
            Session::put('X-AUTH-TOKEN', $authToken);

            /* Store user information in session */
            $userInformation = Auth::getUser();
            Session::put('user', $userInformation);

            /* Redirect to Questionnaire */
            return redirect()->action('PublicQController@showQ', $questionnaireKey);

        } catch (Exception $e) {
            ONE::clearSession();
            //OneLog::error("Verify Register".$e->getMessage());
            return redirect()->back()->withErrors([trans('auth.verifyRegisterAndLogin') => $e->getMessage()])->withInput($request->except('password'));
        }
    }



    public function registerAndReedirect(Request $request)
    {
        try {
            ONE::clearSession();

            /* Email and password */
            $email = $request->email;
            $password = $request->password;
            $url = $request->url;

            if(empty($password)) {
                $password = Hash::make(str_random(8));
            }

            /* Auth - store user - [Auth::storeNewUser] */
            $userDetails = $request->all();
            $data['name'] = $request->name;
            $data['email'] = $request->email;
            $data['password'] = $password;
            $data['identity_card'] = isset($request->identity_card) ? $request->identity_card : null;
            $data['vat_number'] = isset($request->vat_number) ? $request->vat_number : null;
            unset($userDetails['_token']);
            unset($userDetails['name']);
            unset($userDetails['email']);
            unset($userDetails['url']);

            if(empty($url)){
                throw new Exception(trans('register.url_is_needed'));
            }

            $user = Auth::storeNewUser($data,$userDetails);

            /* Orchestrator - store user */
            $user = $user->user;
            if(isset($user->user_key)) {
                $response = Orchestrator::storeUser($user->user_key, ONE::getEntityKey());
            }

            /* Messages and Log */
            Session::flash('message', trans('register.success'));
            // Session::flash('message', trans('register.confirmationEmailSent'));
            //OneLog::info("Registration Success.");

            /* Force Login */
            $login = Auth::login($email,$password);
            $authToken = $login->token;

            /* TODO: Check User Role */
            Session::put('X-AUTH-TOKEN', $authToken);

            /* Store user information in session */
            $userInformation = Auth::getUser();
            Session::put('user', $userInformation);

            /* Redirect to url */
            return Redirect::to($url);

        } catch (Exception $e) {
            ONE::clearSession();
            //OneLog::error("Verify Register".$e->getMessage());
            return redirect()->back()->withErrors([trans('auth.verifyRegisterAndLogin') => $e->getMessage()])->withInput($request->except('password'));
        }
    }


    public function confirmEmail($confirmation_code)
    {
        try{
            $response = Auth::confirmEmail($confirmation_code);

            if(isset($response->user)){
                $userKey = $response->user->user_key ?? null;

                if (!is_null($userKey)){
                    $user = Auth::getUserParameters($userKey);

                    $level = Orchestrator::getUserLevel($userKey);

                    if (isset($level->position)){
                        $user->user_level = $level->position;
                    } else {
                        $user->user_level = 0;
                    }

                    Orchestrator::checkAndUpdateUserLevel($user);

                    $level = Orchestrator::getUserLevel($userKey);

                    if (isset($level->position)){
                        $user->user_level = $level->position;
                    } else {
                        $user->user_level = 0;
                    }
                }

                Orchestrator::checkAndUpdateUserLoginLevel($userKey);
            }

            //OneLog::info("Confirm Email [".$confirmation_code."]");
            Session::flash('message', trans('register.confirmationEmailOK'));
            Session::flash('confirmedMail', true);
            $success = true;
            $message = trans('register.confirmationEmailOK');
            $title = trans('register.confirmationEmailTitleOK');

            if(Session::has(current(preg_grep('/^topics_list_/', array_keys(Session::all()))))){
                Session::forget(current(preg_grep('/^topics_list_/', array_keys(Session::all()))));
            }

            if (isset($response->token)){
                /* Login User */
                $authToken = $response->token;
                Session::put('X-AUTH-TOKEN', $authToken);

                /* Store user information in session */
                $userInformation = Auth::getUser();
                $userLevel = Orchestrator::getUserLevel($userInformation->user_key);
                $userInformation->user_level = $userLevel->position ?? 0;
                Session::put('user', $userInformation);
                Session::put('user_level', $userLevel);
            }

            return view('public.'.ONE::getEntityLayout().'.auth.emailConfirmed', compact('message','title','success'));
        } catch (Exception $e) {
            Session::flash('error', trans('register.confirmationEmailKO'));

            $lastUrl = !empty(Session::get('url_previous',"")) ? Session::get('url_previous',action("PublicController@index")) : action("PublicController@index");

            return view('public.'.ONE::getEntityLayout().'.auth.emailConfirmed', compact('message','title','success','lastUrl'));
        }
    }

    public function confirmEmailUserList($userKey){

        try{

            Orchestrator::updateUserStatus("authorized",$userKey);
            $user = Auth::getUserByKey($userKey);

            // Email / Notify
            $emailType = 'account_authorized';
            $tags = [
                "name" => $user->name,
                "link" => URL::action("PublicCbsController@showCbsWithTopics")
            ];
            $response = Notify::sendEmail($emailType, $tags, (array) $user);


            //OneLog::info("Authorized");
            Session::flash('message', trans('register.confirmationEmailOK'));

            return redirect()->back();
        }catch (Exception $e) {
            Session::flash('error', trans('register.confirmationEmailKO'));
            //OneLog::error("Confirm email: ".$e->getMessage());
            return redirect()->action('AuthController@login');
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
//                        Session::put('user_permissions', $userPermissionsList);
//                        $this->userPermissionsForSidebar($userPermissionsList);
//                        $this->userPermissionsGroupForSidebar($userPermissionsList);
                        Session::put('user_role', 'manager');
                    }
                    $entityKey = Orchestrator::getSiteEntity($_SERVER["HTTP_HOST"])->entity_id;
                    //get user manager menus and groups permissions
                    $userPermissions = EMPATIA::getUserPermissions($user,$entityKey);
                    Session::put('manager_user_permission',$userPermissions);

                    $user_cbs = EMPATIA::getUserCBs($user, $entityKey);
                    Session::put("manager_user_permission_cbs",$user_cbs);

                }else{
                    Session::put('user_permissions_sidebar', ['all']);
                    Session::put('user_permissions_sidebar_groups', ['all']);
                    Session::put('user_role', 'admin');
                    Session::put('manager_user_permission',null);
                }
                if(Session::get('X-ENTITY-KEY', 'undefined') !== 'undefined'){
                    ONE::verifyModulesActive();
                }
                $private = 1;
            }
            if($role != 'admin' && $role != 'manager') {


                switch ($userValidate->status) {
                    case 'needsEntityMigration':
                        Session::flash("X-AUTH-TOKEN-MIGRATE", Session::get("X-AUTH-TOKEN"));
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
//                        if (!empty(Session::get("SITE-CONFIGURATION.boolean_basic_registration_only"))){
//                            return redirect()->action('AuthController@showSuccess');
//                        }
                        

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
            //OneLog::info("Login done USER[".$role."]: ".$user->email);
            if($private == 1){
                if(\Request::get("adminLogin",false)) {
                    try{
                        $entitiesCount = count(Orchestrator::getEntities());
                    } catch (Exception $e){
                        $entitiesCount = 0;
                    }

                    Session::forget('url_previous');
                    if ($entitiesCount>0)
                        return redirect()->route("private");
                    else
                        return redirect()->action("QuickAccessController@firstInstallWizard");
                }

                if (ONE::asPermission('admin') && env("EMPAVILLE_MODE",false))
                    return redirect()->action('CbsController@createWizard',['type' => 'empaville']);

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

            //OneLog::error("Login [checkRoleUser]".$e->getMessage());
            return redirect()->back()->withErrors(["auth.role" => $e->getMessage()]);
        }
    }

    public function sendConfirmEmail(Request $request)
    {
        if(!empty(Session::get('user', null))){
            $user = Session::get('user');

            // Email / Notify
            $emailType = 'registry_confirmation';
            $tags = [
                "name" => $user->name,
                "link" => URL::action('AuthController@confirmEmail', $user->confirmation_code)
            ];
            $response = Notify::sendEmail($emailType, $tags, (array) $user);

            Session::flash('confirmMail', true);
            if(View::exists('public.'.ONE::getEntityLayout().'.auth.emailResend')){
                return view('public.'.ONE::getEntityLayout().'.auth.emailResend', compact('user'));
//                return redirect()->action('SubPagesController@show', ["auth","authNotify"]);
            }else{
                return redirect()->action('PublicController@index');
            }

        }else{
            $emailType = 'registry_confirmation';
            $tags = [
                "name" => Session::get('user_name'),
                "link" => URL::action('AuthController@confirmEmail', Session::get('confirmation_code')),
            ];
            $user['email'] = Session::get('user_email');
            $response = Notify::sendEmail($emailType, $tags, $user);

            Session::flash('confirmMail', true);
            if(View::exists('public.'.ONE::getEntityLayout().'.auth.authNotify')){
                return redirect()->action('SubPagesController@show', ["auth","authNotify"]);
            }else{
                return redirect()->action('PublicController@index');
            }

        }
        return redirect()->back()->withErrors(["auth.role" => trans('public.notLogin')]);
    }

    /**
     * @return View UserTerms
     */
    public function useTerms()
    {
        $response = Orchestrator::getSiteUseTerm();

        if(isset($response)){
            $useTerms = html_entity_decode($response->content);
        }else{
            $useTerms = $response;
        }
        return view('public.'.ONE::getEntityLayout().'.auth.useTerms', compact('useTerms'));
    }


    /**
     * @return View PrivacyPolicy
     */
    public function privacyPolicy(Request $request)
    {
        $response = Orchestrator::getSitePrivacyPolicy();
        if(isset($response)){
            $privacyPolicy = html_entity_decode($response->content);
        }else{
            $privacyPolicy = $response;
        }
        return view('public.'.ONE::getEntityLayout().'.auth.privacyPolicy', compact('privacyPolicy'));
    }


    /* Verify if user email exists */
    public function verifyEmailExists(Request $request) {
        return response()->json(["exists"=>Auth::emailExists($request->input("email"))->exists],200);
    }


    public function migrateUserToEntityConfirmation(Request $request) {
        Session::reflash();
        $facebook_login = false;
        if (View::exists('public.'.ONE::getEntityLayout().'.auth.migration'))
            return view('public.'.ONE::getEntityLayout().'.auth.migration', compact('facebook_login'));
        else
            return redirect("/");
    }
    public function migrateUserToEntity(Request $request) {
        Session::reflash();
        if ($request->input("response")==1) {
            Session::put("X-AUTH-TOKEN", Session::get("X-AUTH-TOKEN-MIGRATE"));
            Orchestrator::migrateUserToEntity(One::getUserKey());
            Session::flash("message", trans("auth.account_migrated_successfully"));
        }

        Session::remove("X-AUTH-TOKEN-MIGRATE");
        return redirect("/");
    }

    public function validateSmsToken(Request $request)
    {
        try {
            Auth::validateSmsToken($request['sms_token']);

            Orchestrator::autoUpdateUserLoginLevels();

            return 'OK';
        }catch (Exception $e) {
            return 'error';
        }

    }


    /** --------------------------
     * STEPPER REGISTER [BEGIN]
     * -----------------------------
     * @param Request $request
     * @param $step
     * @return \Illuminate\Http\RedirectResponse|View
     */
    /**/
    public function stepperManager(Request $request, $step/*, $sms_confirmation_skip = false*/)
    {

        /** DEFAULT VALUES [BEGIN] */
        /*$response = Orchestrator::getSiteUseTerm();
        if(isset($response)){
            $email = isset($response->no_reply_email) ? $response->no_reply_email : null;
            $useTerms = isset($response->use_terms) ? html_entity_decode($response->use_terms->content) : null;
        }else{
            $email = $response;
            $useTerms = $response;
        }*/
        $user = null;
        /** DEFAULT VALUES [END] */

        /* Resumes the Register from where it was left on */
        if ($step == "resume") {
            if (Session::has("user") && isset(Session::get("user")->user_level) && !is_null(Session::get("user")->user_level) && Session::get("user")->user_level==3)
                return redirect()->back();
            else if (Session::has("user"))
                return redirect()->to(ONE::getRedirectionAccordingToStep());
            else
                return redirect()->action('AuthController@stepperManager',['step' => 'register']);
        }

        /** FIRST STEP - BASIC REGISTRATION */
        if ($step == 'register' || $step == 'update') {
            /** VIEWS CONTROL */
            if (View::exists('public.' . ONE::getEntityLayout() . '.auth.newStepper.basicRegistration') && !One::isAuth()) {
                $firstStepActive = true;
                $registerParameters = $this->getStepperAdditionalFields();

                if ($step == 'update') {
                    $user = Session::get('user');
                }

                return view('public.' . ONE::getEntityLayout() . '.auth.newStepper.basicRegistration', compact('firstStepActive', 'user', 'registerParameters'));
            } elseif(View::exists('public.' . ONE::getEntityLayout() . '.auth.newStepper.basicRegistration') && One::isAuth()) {
                $firstStepActive = true;
                $user = Session::get('user');
                $userParametersResponse = json_decode(json_encode($user->user_parameters),true);
                $registerParametersResponse = $this->getStepperAdditionalFields();
                //verify user parameters with responses
                $registerParameters = [];
                foreach ($registerParametersResponse as $parameter){
                    $parameterOptions = [];
                    $value = '';
                    $file = null;

                    if($parameter['parameter_type_code'] == 'radio_buttons' || $parameter['parameter_type_code'] == 'check_box' || $parameter['parameter_type_code'] == 'dropdown' || $parameter['parameter_type_code'] == 'gender') {
                        foreach ($parameter['parameter_user_options'] as $option) {
                            $selected = false;
                            if (isset($userParametersResponse[$parameter['parameter_user_type_key']])) {
                                foreach ($userParametersResponse[$parameter['parameter_user_type_key']] as $userOption) {
                                    if($userOption['value'] == $option['parameter_user_option_key']){
                                        $selected = true;
                                        break;
                                    }
                                }
                            }
                            $parameterOptions [] = [
                                'parameter_user_option_key' => $option['parameter_user_option_key'],
                                'name' => $option['name'],
                                'selected' => $selected
                            ];
                        }
                    }elseif($parameter['parameter_type_code'] == 'file'){

                        $id = isset($userParametersResponse[$parameter['parameter_user_type_key']][0]) ? $userParametersResponse[$parameter['parameter_user_type_key']][0]['value'] : '';
                        if($id != ''){
                            $file = json_decode(json_encode(Files::getFile($id)),true);
                        }

                    }else{
                        $value = isset($userParametersResponse[$parameter['parameter_user_type_key']][0]) ? $userParametersResponse[$parameter['parameter_user_type_key']][0]['value'] : '';
                    }
                    $registerParameters []= [
                        'parameter_user_type_key'   => $parameter['parameter_user_type_key'],
                        'parameter_type_code'       => $parameter['parameter_type_code'],
                        'name'                      => $parameter['name'],
                        'value'                     => isset($file) ? $file : $value,
                        'mandatory'                 => $parameter['mandatory'],
                        'parameter_user_options'    => $parameterOptions
                    ];
                }
                if ($step == 'update') {
                    $user = Session::get('user');
                }

                return view('public.' . ONE::getEntityLayout() . '.auth.newStepper.basicRegistration', compact('firstStepActive', 'user', 'registerParameters'));
            }else{
                return redirect()->action('PublicController@index');
            }
        }


        if (ONE::isAuth() && $step == 'parameters') {

            /** THIRD STEP - SMS VALIDATION */

            $user = Session::get('user');

            /** VIEWS CONTROL */
            if (View::exists('public.' . ONE::getEntityLayout() . '.auth.newStepper.advancedRegistration')) {
                $secondStepActive = true;
                $firstStepActive = true;
                $registerParameters = $this->getStepperAdditionalFields();
                return view('public.' . ONE::getEntityLayout() . '.auth.newStepper.advancedRegistration', compact('secondStepActive', 'registerParameters', 'firstStepActive', 'user'));
            } else {
                return redirect()->action('PublicController@index');
            }

        }

        if (ONE::isAuth() && $step == 'sms_validation') {
            /** THIRD STEP - SMS VALIDATION */
            
            $user = Auth::getUser();

            $registerParameters = [];
            $cantSendSmsMessage = false;
            if (isset($user->sms_token) && !empty($user->sms_token)) {
                /** VIEWS CONTROL */
                if (View::exists('public.' . ONE::getEntityLayout() . '.auth.newStepper.smsValidation')) {
                    
                    $thirdStepActive = true;
                    $secondStepActive = true;
                    $firstStepActive = true;
                    

                    if(isset($user->sms_sent) && Session::has('SITE-CONFIGURATION.sms_max_send') && $user->sms_sent >= Session::get('SITE-CONFIGURATION.sms_max_send'))
                        $cantSendSmsMessage = true;

                    $indicative_values = explode(',', Session::get('SITE-CONFIGURATION.sms_indicative_number'));

                    return view('public.' . ONE::getEntityLayout() . '.auth.newStepper.smsValidation', compact('thirdStepActive', 'secondStepActive', 'firstStepActive', 'registerParameters', 'cantSendSmsMessage', 'indicative_values'));
                } else {
                    return redirect()->action('PublicController@index');
                }
            } else {

                if (View::exists('public.' . ONE::getEntityLayout() . '.auth.newStepper.smsValidation')) {

                
                    $thirdStepActive = true;
                    $secondStepActive = true;
                    $firstStepActive = true;
                    

                    if(isset($user->sms_sent) && Session::has('SITE-CONFIGURATION.sms_max_send') && $user->sms_sent > Session::get('SITE-CONFIGURATION.sms_max_send'))
                        $cantSendSmsMessage = true;

                    $indicative_values = explode(',', Session::get('SITE-CONFIGURATION.sms_indicative_number'));

                    return view('public.' . ONE::getEntityLayout() . '.auth.newStepper.smsValidation', compact('thirdStepActive', 'secondStepActive', 'firstStepActive', 'registerParameters', 'cantSendSmsMessage', 'indicative_values'));
                } else {
                    return redirect()->action('PublicController@index');
                }
            }
        }

        if (ONE::isAuth() && $step == 'confirmation') {

            /** THIRD STEP - SMS VALIDATION */
            $user = Session::get('user');
            Session::forget('phone_number');


            if (isset($user->sms_token) && !empty($user->sms_token)) {
                /** VIEWS CONTROL */
                if (View::exists('public.' . ONE::getEntityLayout() . '.auth.newStepper.confirmation')) {
                    $thirdStepActive = true;
                    $secondStepActive = true;
                    $firstStepActive = true;
                    return view('public.' . ONE::getEntityLayout() . '.auth.newStepper.confirmation', compact('thirdStepActive', 'secondStepActive', 'firstStepActive'));
                    
                } else {
                    return redirect()->action('PublicController@index');
                }
            } else {
                if (View::exists('public.' . ONE::getEntityLayout() . '.auth.newStepper.confirmation')) {
                    $thirdStepActive = true;
                    $secondStepActive = true;
                    $firstStepActive = true;
                    return view('public.' . ONE::getEntityLayout() . '.auth.newStepper.confirmation', compact('thirdStepActive', 'secondStepActive', 'firstStepActive'));
                    
                } else {
                    return redirect()->action('PublicController@index');
                }
            }
        }
    }



    /** --------------------------
     * STEPPER REGISTER [END]
    -----------------------------*/
    /**/

    /** --------------------------
     * STEPPER REGISTER [BEGIN]
    -----------------------------*/
    /**

     * @return View Register
     */
    public function stepperRegister()
    {

        $response = Orchestrator::getSiteUseTerm();
        if(isset($response)) {
            $email = isset($response->no_reply_email) ? $response->no_reply_email : null;
            $useTerms = html_entity_decode($response->use_terms->content);
        }else{
            $email = $response;
            $useTerms = $response;
        }

        if(ONE::isAuth()){
            $user = Session::get('user');
            if(!empty($user->sms_token)){
                if(View::exists('public.'.ONE::getEntityLayout().'.auth.stepper.stepFinalStep')) {
                    return view('public.' . ONE::getEntityLayout() . '.auth.stepper.stepFinalStep');
                }else {
                    return redirect()->action('PublicController@index');
                }
            }else{
                if(View::exists('public.'.ONE::getEntityLayout().'.auth.stepper.stepRegisterSuccess')) {
                    return view('public.' . ONE::getEntityLayout() . '.auth.stepper.stepRegisterSuccess');
                }else {
                    return redirect()->action('PublicController@index');
                }
            }
        }
        if(View::exists('public.'.ONE::getEntityLayout().'.auth.stepper.stepRegister')) {
            return view('public.' . ONE::getEntityLayout() . '.auth.stepper.stepRegister', compact('useTerms', 'email'));
        }else{
            return redirect()->action('PublicController@index');
        }
    }

    public function showSuccess()
    {
        return view('public.' . ONE::getEntityLayout() . '.auth.stepper.stepRegisterSuccess');
    }
    public function validateVatNumber(Request $request)
    {
        try{
            $vatNumberToValidate = $request['vat_number'];
            $parameterUserKey = $request['parameter_user_key'];
            $name = $request['name'];
            $surname = $request['surname'];
            if(!Auth::verifyVatNumber($parameterUserKey,$vatNumberToValidate)){
                return response()->json(["taken" => true],200); /** Already used */
            }
            $response = Orchestrator::validateVatNumber($vatNumberToValidate,$name,$surname);
            if(!$response->vat_number){
                return response()->json(["invalid" => true],200); /** Something went wrong */
            }else{
                return response()->json(["valid" => true],200); /** Something went wrong */
            }
        }catch (Exception $e) {
            return response()->json(["error" => true],200); /** Something went wrong */
        }
        return response()->json(["invalid" => true],200); /** Non existing in the db */

    }

    public function validateMobileNumber(Request $request)
    {
        try{
            $mobileNumberToValidate = $request['mobile'];
            $parameterUserKey = $request['parameter_user_key'];
            if(!Auth::verifyMobileNumber($parameterUserKey,$mobileNumberToValidate)){
                return response()->json(["taken" => true],200); /** Already used */
            }else{
                return response()->json(["valid" => true],200); /** Something went wrong */
            }
        }catch (Exception $e) {
            return response()->json(["error" => true],200); /** Something went wrong */
        }
        return response()->json(["invalid" => true],200); /** Non existing in the db */

    }


    public function validateDomainName(Request $request)
    {
        try{
            $domainName = $request['email'];
            $pieces = explode('@',$domainName);
            $domainNameToValidate = $pieces[count($pieces)-1];
            $result = Orchestrator::validateDomainName($domainNameToValidate);
            if($result==1)
                return response()->json(["valid" => true],200);
            else if ($result==-1)
                return response()->json(["nodomains" => true],200);

        }catch (Exception $e) {
            return response()->json(["error" => true],200); /** Something went wrong */
        }
        return response()->json(["invalid" => true],200); /** Non existing in the db */
    }

    /**
     * return the entity register parameters
     * @return View
     */
    public function getStepperAdditionalFields()
    {
        $registerParametersResponse = Orchestrator::getEntityRegisterParameters();
        //verify user parameters with responses
        $registerParameters = [];
        foreach ($registerParametersResponse as $parameter){
            $parameterOptions = [];
            $value = '';
            $file = null;
            if($parameter->parameter_type->code == 'radio_buttons' || $parameter->parameter_type->code == 'check_box' || $parameter->parameter_type->code == 'dropdown') {
                foreach ($parameter->parameter_user_options as $option) {
                    $selected = false;
                    if (isset($userParametersResponse[$parameter->parameter_user_type_key])) {
                        foreach ($userParametersResponse[$parameter->parameter_user_type_key] as $userOption) {
                            if($userOption['value'] == $option->parameter_user_option_key){
                                $selected = true;
                                break;
                            }
                        }
                    }
                    $parameterOptions [] = [
                        'parameter_user_option_key' => $option->parameter_user_option_key,
                        'name' => $option->name,
                        'selected' => $selected
                    ];
                }
            }elseif($parameter->parameter_type->code == 'file'){
                $id = isset($userParametersResponse[$parameter->parameter_user_type_key][0]) ? $userParametersResponse[$parameter->parameter_user_type_key][0]['value'] : '';
                if($id != ''){
                    $file = json_decode(json_encode(Files::getFile($id)),true);
                }

            }else{
                $value = isset($userParametersResponse[$parameter->parameter_user_type_key][0]) ? $userParametersResponse[$parameter->parameter_user_type_key][0]['value'] : '';
            }
            $registerParameters []= [
                'parameter_user_type_key'   => $parameter->parameter_user_type_key,
                'parameter_type_code'       => $parameter->parameter_type->code,
                'name'                      => $parameter->name,
                'value'                     => isset($file) ? $file : $value,
                'mandatory'                 => $parameter->mandatory,
                'parameter_user_options'    => $parameterOptions
            ];
        }
        return $registerParameters;
    }




    /**
     * Update the specified resource in storage.
     *
     * @param UserRequest $requestUser
     * @param $user_key
     * @return PublicUsersController|\Illuminate\Http\RedirectResponse
     */
    private function updateUser(Request $requestUser, $user_key, $login = true)
    {
        try{

            \Log::info(">>>> **** ERROR: 0");

            $email = $requestUser["email"];
            $password = $requestUser["password"];

            if(!$login){
                $login = Auth::login($email,$password);
                $authToken = $login->token;

                /* TODO: Check User Role */
                Session::put('X-AUTH-TOKEN', $authToken);

                $userInformation = Auth::getUser();
                Session::put('user', $userInformation);
            }

            $parameters = Orchestrator::getEntityRegisterParameters();

            foreach($parameters as $parameter) {
                if ($parameter->code == 'mobile' || $parameter->parameter_type->code == 'mobile') {
                    $number = $parameter->parameter_user_type_key;
                    if (isset($requestUser->$number) && strlen($requestUser->$number) > 0) {
                        $mobileNumber = trim($requestUser->$number, ' ');
                        if (!strpos($mobileNumber, '+351')) {
                            $mobileNumber = '+351' . $mobileNumber;
                        } elseif (strpos($mobileNumber, '00351')) {
                            $mobileNumber = str_replace('00', '+', $mobileNumber);
                        }
                        $requestUser->merge([$parameter->parameter_user_type_key => $mobileNumber]);
                    }
                }

                if($parameter->code == 'cc') {
                    $cc = $parameter->parameter_user_type_key;
                    \Log::info(">>>> **** ERROR: 111 CC->".json_encode($cc));

                    if (isset($requestUser->$cc)) {
                        $cc = trim(str_replace( ' ', '', strtoupper($requestUser->$cc)));
                        $requestUser->merge([$parameter->parameter_user_type_key => $cc]);
                        \Log::info(">>>> **** ERROR: 112 CC->".json_encode($requestUser->all()));


                    }
                }
            }

            $userDetails = $requestUser->all();

            $data['name'] = $requestUser->name;
            $data['surname'] = $requestUser->surname;
            $data['email'] = $requestUser->email;


            foreach ($userDetails as $key => $userDetail){
                $flag =false;
                if (empty($userDetail)){
                    unset($userDetails[$key]);
                    continue;
                }
                foreach ($parameters as $parameter){
                    if($parameter->parameter_user_type_key == $key){
                        $flag=true;
                    }
                }
                if(!$flag){
                    unset($userDetails[$key]);
                }
            }

#\Log::info("AUTH_REGISTER New user [E: ".$email."][N: ".$name."] PARAM: ".json_encode($userDetails));
            \Log::info(">>>> **** ERROR: 10");
            //          Update User Level
            $user = Auth::updateUser($user_key,$data,$userDetails);

            \Log::info(">>>> **** ERROR: 11");

            /** Check and update user Login Level*/
            $userLoginLevels = Orchestrator::checkAndUpdateUserLoginLevel($user_key);
            $user->user_login_levels = $userLoginLevels;


            $user_parameter = [];
            if ( isset($user->user_parameters) ){
                $user_parameter =  (array_keys(json_decode(json_encode($user->user_parameters), true)));
            }

            \Log::info(">>>> **** ERROR: 20");
            /** @deprecated - user level - to remove */
            $response = Orchestrator::updateUserLevel($user_key,$user_parameter);

            \Log::info(">>>> **** ERROR: 21");
            if (!empty($response->position)){
                $user->user_level =  $response->position;
                $user->user_level = $response->position;
            } else {
                $user->user_level = 0;
            }


            $userValidate = Orchestrator::getUserAuthValidate();

            if(!empty($userValidate->status) && $userValidate->status == 'registered'){
                $userStatus = Orchestrator::updateUserStatus('complete');
                Session::put('USER-STATUS', $userStatus->status);
            }
            Session::put('user', $user);

            /*Verification to send email*/
            if(ONE::getNextLevelSMSVerification()){
                foreach ($parameters as $parameter){
                    if(!empty($parameter->parameter_type->code) && $parameter->parameter_type->code == 'mobile'){
                        $parameterKey = $parameter->parameter_user_type_key;
                        if(!empty($user->user_parameters->{$parameterKey}[0])){
                            $smsToken = Auth::generateSMSToken();
                            $mobileNumber = $user->user_parameters->{$parameterKey}[0]->value;

                            if (!empty(Session::get("SITE-CONFIGURATION.sms_token_text",""))) {
                                $smsText = Session::get("SITE-CONFIGURATION.sms_token_text","");
                                if (!str_contains($smsText,"#code#"))
                                    $smsText .= " " . "#code#";
                            } else
                                $smsText = trans('auth.sms_message') . " #code#";

                            $smsText = str_replace("#code#", $smsToken, $smsText);

                            Notify::sendSMS($mobileNumber, $smsText);
                            $userInformation = Auth::getUser();
                            Session::put('user', $userInformation);
                            Session::put("last_sms_token_request", Carbon::now()->addMinutes(5));
                        }else{
                            Orchestrator::SmsUpdateLevel();
                        }
                    }
                }
            }

            if(!$login){
                /* TODO: Check User Role */
                Session::forget('X-AUTH-TOKEN');

                Session::forget('user');
            }

            return true;
        }catch (Exception $e){
            \Log::info(">>>> **** ERROR: >> ".$e->getMessage());
            Throw new Exception(trans('user.error_updating_user'));
            return false;
        }
    }

    public function sendNewSMSCode() {
        try {
            if (!Session::has("last_sms_token_request") || (Session::has("last_sms_token_request") && \Carbon\Carbon::now()->diffInSeconds(Session::get("last_sms_token_request"))<0)) {

               $user = Auth::getUser();

                if(Session::has("SITE-CONFIGURATION.sms_max_send") && $user->sms_sent < Session::get("SITE-CONFIGURATION.sms_max_send","")){
                    $parameters = Orchestrator::getParameterUserTypes();
                    foreach ($parameters as $parameter) {
                        if (!empty($parameter->parameter_type->code) && $parameter->parameter_type->code == 'mobile') {
                            $parameterKey = $parameter->parameter_user_type_key;
                            if (!empty($user->user_parameters->{$parameterKey}[0])) {
                                $smsToken = Auth::generateSMSToken();
                                $indicative = Session::get("SITE-CONFIGURATION.sms_indicative_number","");
                                $mobileNumber = $user->user_parameters->{$parameterKey}[0]->value;
                                if (!empty(Session::get("SITE-CONFIGURATION.sms_token_text",""))) {
                                    $smsText = Session::get("SITE-CONFIGURATION.sms_token_text","");
                                    if (!str_contains($smsText,"#code#"))
                                        $smsText .= " " . "#code#";
                                } else
                                    $smsText = trans('auth.sms_message') . " #code#";

                                $smsText = str_replace("#code#", $smsToken, $smsText);

                                Notify::sendSMS($mobileNumber, $smsText);
                                Auth::storeSmsAttempt();

                                $userInformation = Auth::getUser();

                                $userInformation->sms_token = $smsToken;
                                Session::put('user', $userInformation);
                                Session::put("last_sms_token_request", Carbon::now()->addMinutes(5));
                                return response()->json(["success"=>true],200);
                            }
                        }
                    }
                }
                return response()->json(["number_max_of_sms"=>true],200);
            }
            return response()->json(["fail"=>true],200);
        } catch (Exception $e) {
            return response()->json(["fail"=>true],200);
        }
    }

    /**
     * generate a fake email if the configuration [boolean_no_email_needed] is active
     * @param $name
     * @param $surname
     * @return string
     */
    public static function generateFakeEmail($name, $surname, $nif = null, $domain = null, $noEmail = null)
    {

        if(!empty($nif) && !empty($domain) && $noEmail){
            $fakeEmail = 'registration_'.$nif.'@'.$domain;
            return $fakeEmail;
        }
        elseif(!empty($nif) && !empty($domain)){
            $fakeEmail = 'inPerson_'.$nif.'@'.$domain;
            return $fakeEmail;
        }
        else{
            $faker = Faker::create();
            $fakeEmail = $faker->email; //FALLBACK VALUE

            //ONLY GENERATE FAKE EMAIL IF ALL THIS FIELDS ARE ASSESSABLE
            if(!empty($name) && !empty($surname) && !empty(Session::get("SITE-CONFIGURATION.user_email_domain"))){
                $name =  str_replace(' ', '',$name);
                $surname =  str_replace(' ', '',$surname);
                $fakeEmail = strtolower($name.$surname).Carbon::now()->format('_Ymd_his').'@'.Session::get("SITE-CONFIGURATION.user_email_domain");

            }
            return $fakeEmail;
        }
    }


    /**
     * delete the current user parameters in the current entity
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteUserParameters()
    {
        try {
            $userParameters = Orchestrator::getEntityRegisterParameters();

            $parameters = collect($userParameters)->pluck('id');

            $userKey = ONE::getUserKey();
            Auth::deleteUserParameters($parameters,$userKey);
            Orchestrator::deleteUserLevel($userKey);

            return response()->json(["success"=>true],200);
        } catch (Exception $e) {
            return response()->json(["fail"=>true],200);
        }
    }


    private function userPermissionsForSidebar($userPermissions){

        if(empty($userPermissions)){
            return [];
        }

        $show = [];

        foreach($userPermissions as $moduleKey => $module){
            foreach($module as $key => $permissions){
                if($permissions->permission_show == 1)
                    $show[] = $key;
            }
        }
        Session::put('user_permissions_sidebar', $show);
    }

    private function userPermissionsGroupForSidebar($userPermissions){

        if(empty($userPermissions)){
            return [];
        }

        $groups = [
            'participation' => [
                'cb' => [ 'idea', 'forum', 'discussion', 'proposal', 'publicConsultation', 'tematicConsultation', 'survey', 'project'],
                'mp' => ['mp'],
                'q' => ['poll']
            ],
            'contents' => [
                'orchestrator' =>  ['entity_site'],
                'cm' => ['menu', 'pages', 'news', 'events', 'pages']
            ],
            'users' => [
                'auth' => ['manager', 'user', 'in_person_registration', 'confirm_user', 'user_parameters']
            ],
            'research' => [
                'q' => ['q'],
                'analytics' => ['test_code'],
                'wui' => ['open_data']
            ],
            'communication' => [
                'wui' => ['email', 'sms', 'history']
            ],
            'configurations' => [
                'wui' => ['entity_groups'],
                'orchestrator' => ['role'],
                'cm' => ['home_page_type'],
                'cb' => ['parameter_template']
            ]
        ];

        $showGroup = [];

        foreach($groups as $groupName => $group) {
            if(isset($group) and !empty($group)){
                foreach ($group as $key => $modules) {
                    if(isset($modules) and !empty($modules)){
                        foreach ($modules as $permissions) {
                            if(isset($userPermissions->$key->$permissions) && !empty($userPermissions->$key->$permissions) && ($userPermissions->$key->$permissions->permission_show == 1 || $userPermissions->$key->$permissions->permission_show == true)){
                                $showGroup[] = $groupName;
                                break;

                            }
                        }
                    }
                }
            }
        }

        Session::put('user_permissions_sidebar_groups', $showGroup);
    }

    /**
     * @param $userKey
     * @return bool|string
     */
    public function manuallyConfirmUserEmail($userKey)
    {
        try {
            Auth::manuallyConfirmUserEmail($userKey);
            return 'OK';
        } catch (Exception $e) {
            return 'ERROR';
        }
    }

    /**
     * @param $userKey
     * @return string
     */
    public function manuallyConfirmUserSms($userKey)
    {
        try {
            /** Confirm User Email*/
            Auth::manuallyConfirmUserSms($userKey);
            return 'OK';
        } catch (Exception $e) {
            return 'ERROR';
        }
    }

    /**
     * @param Request $request
     * @return string
     */
    public function resendConfirmEmail(Request $request)
    {
        try {
            $userKey = $request->input('user_key');

            if (!is_null($userKey)){

                $user = Auth::getUserByKey($userKey);
                $role = ONE::userRole();

                if ($role == 'admin' || $role == 'manager'){

                    // Email / Notify
                    $emailType = 'registry_confirmation';
                    $tags = [
                        "name" => $user->name,
                        "link" => URL::action('AuthController@confirmEmail', $user->confirmation_code)
                    ];
                    $response = Notify::sendEmail($emailType, $tags, (array)$user);

                    Session::flash('confirmMail', true);

                    return 'OK';
                }
                return 'Authorization Error';
            }
        } catch (Exception $e) {
            return 'ERROR';
        }
        return 'ERROR';
    }



    public function accountRecovery() {
        try {
            $parameters = EMPATIA::getAccountRecoveryParametersForForm();
            return view('public.'.ONE::getEntityLayout().'.auth.accountRecovery.index',compact('parameters'));
        } catch (Exception $e) {
            return redirect()->back()->withErrors([trans('accountRecovery.accountRecoveryFailed') => $e->getMessage()]);
        }
    }
    public function accountRecoverySteps(Request $request, $step) {
        try {
            if ($step=="validate") {
                $hasTokenValidation = false;

                $parameters = EMPATIA::getAccountRecoveryParametersForForm();

                $userParameters = array();
                foreach ($parameters as $parameter) {
                    if ($parameter->parameter_user_type_key == "email")
                        $parameterKey = "email";
                    else
                        $parameterKey = $parameter->parameter_user_type->parameter_user_type_key;

                    $userParameters[$parameterKey] = $request->get($parameterKey, null);
                }

                $responseData = EMPATIA::validateAccountRecoveryRequest($userParameters);
                if ($responseData!=false) {
                    $userKey = $responseData->user->user_key;
                    if (!empty($responseData->user) && !empty($responseData->validation) &&
                        !empty($responseData->validation->parameter) && !empty($responseData->validation->value) && !empty($responseData->validation->token)
                    ) {

                        if (strtolower($responseData->validation->parameter) === "mobile") {
                            if (!empty(Session::get("SITE-CONFIGURATION.account_validation_sms", ""))) {
                                $smsText = Session::get("SITE-CONFIGURATION.account_validation_sms", "");
                                if (!str_contains($smsText, "#code#"))
                                    $smsText .= " " . "#code#";
                            } else
                                $smsText = trans('authAccountRecovery.sms_message') . " #code#";

                            $smsText = str_replace("#code#", $responseData->validation->token, $smsText);
                            Notify::sendSMS($responseData->validation->value, $smsText);
                        } else if (strtolower($responseData->validation->parameter) === "email") {
                            $emailType = 'account_recovery';
                            $tags = [
                                "name" => $responseData->user->name,
                                "code" => $responseData->validation->token
                            ];
                            Notify::sendEmail($emailType, $tags, (array)$responseData->user);
                        }
                        $hasTokenValidation = true;
                    }
                    Session::put("hasTokenValidation", $hasTokenValidation);

                    return view('public.' . ONE::getEntityLayout() . '.auth.accountRecovery.recover', compact('hasTokenValidation', 'userKey'));
                } else
                    return redirect()->back()->withErrors([trans("accountRecovery.no_account_match")]);
            } elseif ($step=="recover") {
                if ($request->get("password","")==$request->get("password_confirmation","") && !empty($request->get("email",""))) {
                    $dataToSend = array(
                        "userKey" => $request->get("userKey",""),
                        "email" => $request->get("email",""),
                        "password" => $request->get("password",""),
                        "hasCode" => Session::get("hasTokenValidation",false),
                        "code" => $request->get("confirmation","")
                    );
                    $user = EMPATIA::recoverAccount($dataToSend);
                    if (Session::get('SITE-CONFIGURATION.boolean_no_email_confirmation',false)) {
                        $this->confirmEmail($user->confirmation_code);
                    } else {
                        $this->sendConfirmEmail($request);
                        Session::flash('message', trans('authAccountRecovery.confirmationEmailSent'));
                    }

                    if (View::exists('public.' . ONE::getEntityLayout() . '.auth.accountRecovery.success'))
                        return view('public.' . ONE::getEntityLayout() . '.auth.accountRecovery.success');

                    return redirect('/');
                } else
                    return redirect()->back()->withErrors([trans('accountRecovery.missingDataForRecover')]);
            }
            return redirect()->back();
        } catch (Exception $e) {
            return redirect()->back()->withErrors([trans('accountRecovery.accountRecoveryFailed') => $e->getMessage()]);
        }
    }


    static function sendConfirmEmailForEmailParameter($userName, $destinyEmail, $confirmationCode) {
        $emailType = 'registry_confirmation';
        $tags = [
            "name" => $userName,
            "link" => URL::action('AuthController@confirmEmail', $confirmationCode)
        ];
        Notify::sendEmailUsingDirectData($emailType, $tags, $destinyEmail);
    }

    public function resetSentSms(Request $request)
    {
        Auth::resetNumberOfSmsSent($request->user_key);
    }

    public function getNumberFromChar($letter) {
        switch($letter)  {
            case '0' : return 0;
            case '1' : return 1;
            case '2' : return 2;
            case '3' : return 3;
            case '4' : return 4;
            case '5' : return 5;
            case '6' : return 6;
            case '7' : return 7;
            case '8' : return 8;
            case '9' : return 9;
            case 'A' : return 10;
            case 'B' : return 11;
            case 'C' : return 12;
            case 'D' : return 13;
            case 'E' : return 14;
            case 'F' : return 15;
            case 'G' : return 16;
            case 'H' : return 17;
            case 'I' : return 18;
            case 'J' : return 19;
            case 'K' : return 20;
            case 'L' : return 21;
            case 'M' : return 22;
            case 'N' : return 23;
            case 'O' : return 24;
            case 'P' : return 25;
            case 'Q' : return 26;
            case 'R' : return 27;
            case 'S' : return 28;
            case 'T' : return 29;
            case 'U' : return 30;
            case 'V' : return 31;
            case 'W' : return 32;
            case 'X' : return 33;
            case 'Y' : return 34;
            case 'Z' : return 35;
        }

        throw new Exception("Valor inválido no número de documento.");
    }

    public function validateNumberCC($ccNumber) {
        $sum = 0;
        $secondDigit = false;

        $ccNumber = trim(str_replace( ' ', '', strtoupper($ccNumber)));

        if(strlen($ccNumber) != 12){
            throw new Exception("Tamanho inválido para número de documento.");
        }

        for ($i = strlen($ccNumber)-1; $i >= 0; --$i)  {


            $valor = $this->getNumberFromChar($ccNumber[$i]);

            if ($secondDigit)  {
                $valor *= 2;

                if ($valor > 9)
                    $valor -= 9;
            }

            $sum += $valor;
            $secondDigit = !$secondDigit;
        }

        return ($sum % 10) == 0;
    }

    function validateNifFormat($nif, $ignoreFirst=false) {
        //Limpamos eventuais espaços a mais
        $nif=trim($nif);
        //Verificamos se é numérico e tem comprimento 9
        if (!is_numeric($nif) || strlen($nif)!=9) {
            return false;
        } else {
            $nifSplit=str_split($nif);
            //O primeiro digíto tem de ser 1, 2, 5, 6, 8 ou 9
            //Ou não, se optarmos por ignorar esta "regra"
            if (
                in_array($nifSplit[0], array(1, 2, 5, 6, 8, 9))
                ||
                $ignoreFirst
            ) {
                //Calculamos o dígito de controlo
                $checkDigit=0;
                for($i=0; $i<8; $i++) {
                    $checkDigit+=$nifSplit[$i]*(10-$i-1);
                }
                $checkDigit=11-($checkDigit % 11);
                //Se der 10 então o dígito de controlo tem de ser 0
                if($checkDigit>=10) $checkDigit=0;
                //Comparamos com o último dígito
                if ($checkDigit==$nifSplit[8]) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }
    }
}
