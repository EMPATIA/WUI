<?php

namespace App\One;

use App\ComModules\Auth;
use App\ComModules\CB;
use App\ComModules\EMPATIA;
use App\ComModules\LogsRequest;
use App\ComModules\Orchestrator;
use App\EntityNotificationType;
use Carbon\Carbon;
use Cache;
use Closure;
use Exception;
use Form;
use HttpClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Cookie;
use ReCaptcha\ReCaptcha;
use Session;
use Route;
use URL;
use Request as RequestClass;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Toastr;
use Log;
use Redis;
require(base_path().'/vendor/google/recaptcha/src/autoload.php');

class One
{
    use DispatchesJobs;

    public function __construct()
    {
    }


    public static function getEntityLayout(){
        return env('LAYOUT', !empty(Session::get('X-LAYOUT-KEY'))? Session::get('X-LAYOUT-KEY') : 'default');
    }

    public static function clearSession(){
        // IMPROVE IF POSSIBLE. CANNOT FLUSH BECAUSE OF LARAVEL FORM VALIDATIONS.
        
        Session::forget('user');
        Session::forget('sms_send');
        Session::forget('AUTH');
        Session::forget('USER-STATUS');
        Session::forget('user_level');
        Session::forget('X-AUTH-TOKEN');
        Session::forget('user_permissions');
        Session::forget('user_permissions_sidebar');
        Session::forget('user_permissions_sidebar_groups');
        Session::forget('user_role');
        Session::forget('USER-ROLE-CODE');
        Session::forget('languages');
        Session::forget('LANG_CODE');
        Session::forget('manager_user_permission');
        Session::forget('manager_user_permission_cbs');
        Session::forget('userDashboardElements');
        Session::forget('entityDashboardElements_'.ONE::getEntityKey());
    }

    public static function clearLangSession(){
        // VERY IMPORTANT: don't clear Langs
        /*
        Session::forget('LANG_CODE');
        Session::forget('LANG_CODE_DEFAULT');
         */
    }

    public static function clearEntitySession(){
        Session::forget('X-SITE-KEY');
        Session::forget('X-ENTITY-KEY');
    }


    /**
     * Check if entity exist on Session
     *
     * @return bool
     */
    public static  function isEntity(){
        if (Session::has('X-ENTITY-KEY')) {
            return true;
        }
        return false;
    }



    public static function forceEntityKeyFromURL(){
        try {
            $site = Orchestrator::getSiteEntity($_SERVER["HTTP_HOST"]);
            Session::put('X-SITE-KEY', $site->site_key);
            Session::put('X-ENTITY-KEY', $site->entity_id);
        } catch (Exception $e) {

        }
    }

    /**
     * Check if user exist on Session
     *
     * @return bool
     */
    public static function isAuth() {
        try {
            if (Session::has('X-AUTH-TOKEN')) {
                Orchestrator::getUserAuthValidate();
                return true;
            }
        } catch (Exception $e) {}
        return false;
    }


    /**
     * Check if user exist has Authorization
     *
     * @return bool
     */
    public static function checkAuthorization() {

        if (Session::has('AUTH') && Session::get('AUTH')) {
            return true;
        }
        return false;
    }

    /**
     * Check if user is type Admin
     *
     * @return bool
     */
    public static function isAdmin()
    {
        try {
            if (Session::has('X-AUTH-TOKEN')) {
                $user = Orchestrator::getUserAuthValidate();
                if ($user->admin)
                    return true;
            }
        } catch (Exception $e) {}
        return false;
    }

    /**
     * Get the user key
     *
     * @return bool
     */
    public static function getUserKey()
    {
        if (Session::has('X-AUTH-TOKEN')) {
            return Session::get('user')->user_key;
        }
        return '';
    }

    public static function isOwner($userKey){
        if(ONE::isAuth() && Session::has('user') && Session::get('user')->user_key == $userKey){
            return true;
        }
        return false;
    }


    public static function checkCBsOption($cbOptions, $optionTAG){
        return CB::checkCBsOption($cbOptions, $optionTAG);
    }

    /**
     * Get user permissions and save on Session
     *
     * @param $roles
     */
    public static function getUserPermissions($roles){

        Session::forget('USER-PERMISSION');

        $userPermissions = [];
        foreach($roles as $role){

            $permissions = $role->permissions;
            foreach($permissions as $permission){

                if(strlen($permission->code) > 0){
                    $code = $permission->code;

                    $api = $permission->api;
                    $module = $permission->module;

                    if($permission->create == 1){
                        $userPermissions[] =  $api.'_'.$module.'-create';
                    }
                    if($permission->view == 1){
                        $userPermissions[] =  $api.'_'.$module.'-view';
                    }
                    if($permission->update == 1){
                        $userPermissions[] =  $api.'_'.$module.'-update';
                    }
                    if($permission->delete == 1){
                        $userPermissions[] =  $api.'_'.$module.'-delete';
                    }
                }
            }
        }

        Session::put('USER-PERMISSION', $userPermissions);
    }




    /**
     * Check if permission exist
     *
     * @param $permissionToCheck
     * @return bool
     */
    public static function checkUserPermissions($permissionToCheck)
    {
        if(ONE::isAdmin())
            return true;

        if(Session::has('USER-PERMISSION')){
            $permissions = Session::get('USER-PERMISSION', []);

            if (in_array($permissionToCheck, $permissions)){
                return true;
            }
        }

        return false;
    }


    public static function getUrlFile($type){

        $siteKey = Session::get('X-SITE-KEY','INVALID');
        $entityKey = Session::get('X-ENTITY-KEY','INVALID');
        $components = Cache::get('COMPONENTS'.env('MODULE_TOKEN'));

        if(empty($components)) {

            $request = [
                'url' => env('COMPONENT_MODULE_AUTH') . '/components',
                'headers' => [
                    'X-MODULE-TOKEN: ' . env('MODULE_TOKEN', 'INVALID'),
                    'X-SITE-KEY: ' . $siteKey,
                    'X-ENTITY-KEY: ' . $entityKey]
            ];
            $response = HttpClient::GET($request);
            if ($response->statusCode() == 200) {
                $componentData = json_decode($response->content(), true);
                $components = $componentData['data'];
                Cache::put('COMPONENTS' . env('MODULE_TOKEN'), $components, 10);
            }
        }
        $url = $components['FILES'];
        if($type === 'upload'){
            $url .= '/file/upload/';
        }elseif($type === 'download'){
            $url .= '/file/download/';
        }
        return $url;

    }


    /**
     * Get user role
     *
     * @return string
     */
    public static function userRole()
    {
        if (empty(Session::get("USER-ROLE-CODE",""))) {
            $userRole = '';

            try {
                if (Session::has('X-AUTH-TOKEN')) {
                    $user = Orchestrator::getUserAuthValidate();
                    if($user->admin == 1)
                        $userRole = 'admin';
                    else
                        $userRole = $user->role ?? "";
                }
            } catch (Exception $e) {}

            Session::put('USER-ROLE-CODE', $userRole);
        }

        return Session::get('USER-ROLE-CODE', "");
    }

    /**
     * Get user role
     *
     * @param $permissionType
     * @return string
     */
    public static function asPermission($permissionType)
    {
        if (Session::has('X-AUTH-TOKEN')) {
            $role = ONE::userRole();

            if($role == 'admin'){
                return true;
            }else if($permissionType == 'manager'){
                if($role == 'admin' || $role == 'manager'){
                    return true;
                }
            }else if($permissionType == 'user'){
                if($role == 'admin' || $role == 'manager' || $role == 'user'){
                    return true;
                }
            }
        }

        return false;
    }

    public static function getEntities()
    {
        try {
            return Orchestrator::getEntities();
        } catch (Exception $e) {
            return [];
        }
    }


    /**
     * Get the specified resource.
     *
     *
     */
    public static function getAllLanguages()
    {
        try {
            if(!empty(Session::get("languages",""))) {
                return Session::get("languages");
            } else {
                $languages = Orchestrator::getLanguages(Session::get('X-ENTITY-KEY'));
                Session::put("languages",$languages);
                return $languages;
            }

        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * @param $keys
     * @param $request
     */
    public static function verifyKeysRequest($keys, Request $request)
    {
        foreach ($keys as $key) {
            $result = $request->json($key);
            if (!isset($result)) {
                abort(400);
            }
        }

    }

    public static function verifyToken(Request $request, $modid = null)
    {
        if (empty($request->header('X-AUTH-TOKEN')))
            abort(400);

        //TODO: Communicate with AuthModule

        return 'userKey-asdf';
    }

    /**
     * @param Request $request
     * @param null $modid
     * @return string
     */
    public static function getEntityKey()
    {
        return Session::get('X-ENTITY-KEY', null);
    }

    /**
     * @param Request $request
     * @internal param $entityKey
     * @internal param Request $request
     */
    public static function setEntityHeader(Request $request)
    {
        Session::put('X-ENTITY-KEY',$request->entityKey);
    }



    public static function actionType($name = 'form')
    {
        if (strpos(array_get(Route::getCurrentRoute()->getAction(), 'as', ''), $name . '.create') !== false)
            $type = 'create';
        else if (strpos(array_get(Route::getCurrentRoute()->getAction(), 'as', ''), $name . '.edit') !== false)
            $type = 'edit';
        else
            $type = 'show';

        if (RequestClass::input("f",null) != $name && $type != 'create') {
            $type = 'show';
        }

        return $type;
    }

    public static function isEdit() {

        if (strpos(array_get(Route::getCurrentRoute()->getAction(), 'as', ''), '.create') !== false) {
            return true;
        }

        return  RequestClass::input("f",null) == Session::get('oneForm');
    }



    /**
     * Init oneForm Options
     *
     * @param string $name
     * @param array $options
     * @return array $option
     */
    public static function initOptions($name, $options) {
        $options['class'] = !isset($options['class']) ? 'form-control' : $options["class"];
        $options['id'] = !isset($options['id']) ? $name : $options['id'];

        return $options;
    }

    public static function form($name = 'form', $title = null, $module = null, $moduleType = null, $type = null, $layout = '_layouts.form-empatia')
    {
        $name= str_replace(".","_",$name);

        if ($type == null) {
            $type = One::actionType($name);
        }


        $url = URL::current();
        if (strpos($url, '/private/') !== false){
            $layout = '_layouts.form-private';
        }
        return new OneForm($name, $module, $moduleType, $type, $layout, $title);
    }

    public static function actionButtons($id, $params)
    {
        $formId = "";


        $url = URL::current();
        $private = 0;
        $popup = 0;

        $conf = [
            'edit' => ['color' => 'btn-edit', 'icon' => 'pencil'],
            'create' => ['color' => 'btn-success  btn-xs', 'icon' => 'plus'],
            'show' => ['color' => 'btn-info  btn-xs', 'icon' => 'eye'],
            'delete' => ['color' => 'btn-delete btn-xs', 'icon' => 'remove'],
            'add' => ['color' => 'btn-warning  btn-xs', 'icon' => 'plus'],
            'accept' => ['color' => 'btn-success  btn-xs', 'icon' => 'check'],
            'decline' => ['color' => 'btn-danger  btn-xs', 'icon' => 'remove'],
            'activate' => ['color' => 'btn-success  btn-xs', 'icon' => 'check'],
            'send' => ['color' => 'btn-success  btn-xs', 'icon' => 'send'],
            'import' => ['color' => 'btn-success btn-xs', 'icon' => 'download']
        ];

        if (strpos($url, '/private/') !== false){
            $private = 1;
            $conf = [
                'edit' => ['color' => 'edit', 'icon' => 'pencil'],
                'create' => ['color' => 'create', 'icon' => 'plus'],
                'show' => ['color' => 'info-small', 'icon' => 'eye'],
                'delete' => ['color' => 'danger', 'icon' => 'remove'],
                'add' => ['color' => 'add-small', 'icon' => 'plus'],
                'accept' => ['color' => 'success', 'icon' => 'check'],
                'decline' => ['color' => 'danger', 'icon' => 'remove'],
                'activate' => ['color' => 'success  btn-xs', 'icon' => 'check'],
                'send' => ['color' => 'success', 'icon' => 'send'],
                'import' => ['color' => 'warning', 'icon' => 'download']
            ];
        }


        if (isset($params["form"])) {
            $formId = $params["form"];
            unset($params["form"]);
        }

        if (isset($params["popup"])) {
            if($params["popup"] == 'true'){
                $popup = 1;
            }
            unset($params["popup"]);
        }


        $html = '';
        foreach ($params as $type => $action) {

            if($type == 'activate' || $popup == 1) {
                $action = "javascript:oneActivate('" . action($action, $id) . "')";
            } else if($type == 'delete') {
                $action = "javascript:oneDelete('" . action($action, $id) . "')";
            } else {
                $action = action($action, $id);


                if(strpos($action, '?') !== false){
                    $action .= ( strlen($formId) > 0 && $type == "edit") ? "&f=".$formId : "";
                }else{
                    $action .= ( strlen($formId) > 0 && $type == "edit") ? "?f=".$formId : "";
                }

            }

            if($private == 1){
                $html .= '<a href="' . $action . '" class="btn btn-flat btn-' . $conf[$type]['color'] . ' btn-xs" data-toggle="tooltip" data-delay=\'{"show":"50"}\' title="' . trans('form.' . $type) . '"><i class="fa fa-' . $conf[$type]['icon'] . '"></i></a> ';
            }else{
                $html .= '<a href="' . $action . '" class="btn btn-flat ' . $conf[$type]['color'] . '" data-toggle="tooltip" data-delay=\'{"show":"1000"}\' title="' . trans('form.' . $type) . '"><i class="fa fa-' . $conf[$type]['icon'] . '"></i></a> ';
            }
        }
        return $html;
    }

    public static function getFromId(){
        return array_get(Route::getCurrentRoute()->getAction(), 'as', '');
    }

    public static function messages()
    {
        Session::forget('toastr::notifications');
        if (Session::has('message')) {
            Toastr::success(Session::get('message'));
        }

        if (Session::has('errors')) {
            $errors = Session::get('errors');
            foreach ($errors->all() as $message) {
                Toastr::error($message);
            }
        }

        return Toastr::render();
    }
    /* ================================== */

    /* Send Request */
    public static function get($requestData)
    {
        return One::send('GET', $requestData);
    }

    public static function put($requestData)
    {
        return One::send('PUT', $requestData);
    }

    public static function post($requestData)
    {
        return One::send('POST', $requestData);
    }

    public static function delete($requestData)
    {
        return One::send('DELETE', $requestData);
    }

    public static function send($action, $requestData)
    {
        try {
            $authToken = Session::get('X-AUTH-TOKEN', 'INVALID');
            $siteKey = Session::get('X-SITE-KEY', 'INVALID');
            $entityKey = Session::get('X-ENTITY-KEY', 'INVALID');
            $timezone = Session::get('TIMEZONE', 'INVALID');

            $currentLang = Session::get('LANG_CODE', 'INVALID');
            $defaultLang = Session::get('LANG_CODE_DEFAULT', 'INVALID');
            if (env('PERFORMANCE_FLAG', 'false') == 'true' && env('LOGS_FLAG', 'false') == 'true') {
                $performanceFlag = true;
            } else $performanceFlag = false;


            if (empty($currentLang)) {
                if (!empty($defaultLang)) {
                    $currentLang = $defaultLang;
                } else {
                    $defaultLang = 'en';
                    $currentLang = $defaultLang;
                }
            }


            $url = null;
            if (array_key_exists('url', $requestData)) {
                $url = $requestData['url'];
            } else {
                if (array_key_exists('component', $requestData)) {

                    $components = Cache::get('COMPONENTS' . env('MODULE_TOKEN'));

                    if (empty($components)) {

                        $request = [
                            'url' => env('COMPONENT_MODULE_AUTH') . '/components',
                            'headers' => [
                                'X-MODULE-TOKEN: ' . env('MODULE_TOKEN', 'INVALID'),
                                'X-SITE-KEY: ' . $siteKey,
                                'LANG-CODE: ' . $currentLang,
                                'LANG-CODE-DEFAULT: ' . $defaultLang,
                                'X-ENTITY-KEY: ' . $entityKey,
                                'TIMEZONE: ' . $timezone
                            ]
                        ];
                        $response = HttpClient::GET($request);
                        if ($response->statusCode() == 200) {
                            $componentData = json_decode($response->content(), true);
                            $components = $componentData['data'];
                            Cache::put('COMPONENTS' . env('MODULE_TOKEN'), $components, 10);
                        }

                    }

                    $array = array(
                        'analytics' => $components['ANALYTICS'],
                        'auth' => $components['AUTH'],
                        'cb' => $components['CB'],
                        'cm' => $components['CM'],
                        'files' => $components['FILES'],
                        'logs' => $components['LOGS'],
                        'mp' => $components['MP'],
                        'notify' => $components['NOTIFY'],
                        'orchestrator' => $components['ORCHESTRATOR'],
                        'q' => $components['Q'],
                        'vote' => $components['VOTE'],
                        'wui' => $components['WUI'],
                        'kiosk' => $components['KIOSK'],
                        'events' => $components['EVENTS'],
                        'empatia' => $components['EMPATIA']

                    );

                    $url = $array[$requestData['component']];

                }

            }
            if (!empty($url)) {
                if (!empty($requestData["api"]))
                    $requestData["api"] = trim($requestData["api"], " /");

                if (!empty($requestData["api_attribute"]))
                    $requestData["api_attribute"] = trim($requestData["api_attribute"], " /");

                if (!empty($requestData["method"]))
                    $requestData["method"] = trim($requestData["method"], " /");

                if (!empty($requestData["attribute"]))
                    $requestData["attribute"] = trim($requestData["attribute"], " /");

                if (!array_key_exists("params", $requestData))
                    $requestData["params"] = [];


                if (!empty($requestData['key'])) {

                    $url .= "/" . $requestData["key"];
                }
                if (!empty($requestData["api"])) {
                    $url .= "/" . $requestData["api"];
                }

                if (!empty($requestData["api_attribute"])) {
                    $url .= "/" . $requestData["api_attribute"];
                }

                if (!empty($requestData["method"]))
                    $url .= "/" . $requestData["method"];

                if (!empty($requestData["attribute"]))
                    $url .= "/" . $requestData["attribute"];


                /* TODO: Remove string by default */


                if (!empty($requestData["method"])) {
                    if ($requestData["method"] == 'moderators') {

                        // if($action == 'DELETE')
                        //   dd($url);
                    }
                }
                if (env('TIMELINE_DEBUG', false)) {
                    $performance = session()->getId() . "_" . time();
                } else {
                    $performance = 'INVALID';
                }
                if (!empty($requestData["headers"])) {
                    $request = [
                        'url' => $url,
                        'headers' => $requestData["headers"],
                        'params' => $requestData['params'],
                        'json' => true
                    ];
                } else {


                    $request = [
                        'url' => $url,
                        'headers' => [
                            'X-AUTH-TOKEN: ' . $authToken,
                            'X-MODULE-TOKEN: ' . env('MODULE_TOKEN', 'INVALID'),
                            'X-SITE-KEY: ' . $siteKey,
                            'LANG-CODE: ' . $currentLang,
                            'LANG-CODE-DEFAULT: ' . $defaultLang,
                            'X-ENTITY-KEY: ' . $entityKey,
                            'TIMEZONE: ' . $timezone,
                            'PERFORMANCE: ' . $performanceFlag

                        ],
                        'params' => $requestData['params'],
                        'params' => $requestData['params'],
                        'json' => true
                    ];


                }


                Log::debug("SEND: " . $action . " " . json_encode($request));

                $start_t = microtime(true);

                $request = ONE::removeInvalidHeaders($request);

                if (env('TIMELINE_DEBUG', false)) {
                    ONE::performanceEvaluation($performance, 'REQUEST_' . $action, $requestData['component'], null, $url);
                }


                if ($action === 'GET')
                    $response = HttpClient::GET($request);
                else if ($action === 'POST')
                    $response = HttpClient::POST($request);
                else if ($action === 'PUT')
                    $response = HttpClient::PUT($request);
                else if ($action === 'DELETE')
                    $response = HttpClient::DELETE($request);
                Log::debug("RCV: " . $action . " " . json_encode($response));

                if (env('TIMELINE_DEBUG', false)) {
                    ONE::performanceEvaluation($performance, 'REPLY_' . $action, $requestData['component'], null, $url);
                }

                $end_t = microtime(true);

                if (env('TIME_CHECK', false)) {
                    Log::debug("URL-REQUEST: " . $request['url'] . " | Pre-REQUEST: " . $start_t . " | Post-REQUEST: " . $end_t . " | Duration-REQUEST: " . ($end_t - $start_t));
                }

                return $response;
            }
        }catch (Exception $exception){
            echo "<script>location.reload();</script>";
            dd();
        }
    }



    public static function removeInvalidHeaders($request){

        $headers = $request['headers'];

        foreach($headers as $header){
            if (strpos($header, 'INVALID') !== false) {
                $key = array_search($header, $headers);
                unset($headers[$key]);
            }
        }

        $request['headers'] = $headers;
        return $request;
    }

    /**
     * @param $request
     * @param Closure $next
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public static function publicMiddleware($request, Closure $next)
    {
        // NOT BEING USED!
        
        //return $next($request);
    }


    public static function checkIfValidToken(){
        try {
            Auth::validateToken();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }



    /**
     * @param $request
     * @param Closure $next
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public static function privateMiddleware($request, Closure $next)
    {
        // Session Languages
        $currentLang = Session::get('LANG_CODE');
        $defaultLang = Session::get('LANG_CODE_DEFAULT');
        $entityKey = Session::get('X-ENTITY-KEY');
        $middleware = Session::get('X-ACCESS-AREA');
        $timezone = Session::get('TIMEZONE');

        //  Set Timezone
        if($entityKey != 'INVALID' && !is_null($entityKey) && empty($timezone)){
            try {
                $entity = Orchestrator::getEntity($entityKey);

                if(!is_null($entity->timezone)){
                    Session::put('TIMEZONE', $entity->timezone->name);
                }
            }
            catch(Exception $e) {
                return redirect()->action('AuthController@login');
            }
        }

        if((empty($currentLang) || empty($defaultLang))) {
            if($entityKey){
                $allLang = ONE::getAllLanguages();
                $default = reset($allLang);

                $langDefault  = "en";
                foreach($allLang as $language){
                    // Default language
                    if(!empty($language->default) && $language->default == true){
                        Session::put('LANG_CODE_DEFAULT', $language->code);
                        $langDefault = $language->code;
                    }
                }

                Session::put('LANG_CODE_DEFAULT', $langDefault);
            } else {
                Session::put('LANG_CODE_DEFAULT', 'en');
            }
        }

        // Language code for private
        if($middleware == 'private'){
            // Session::put('LANG_CODE_DEFAULT', 'en');
        }

        // Set Language APP
        if(!empty($currentLang)){
            App::setLocale($currentLang);
        } else{
            $lang = isset($langDefault) ? : "en";
            App::setLocale($lang);
        }

        // Validate Token (if not valid reedirect for login)
        if (Session::has('X-AUTH-TOKEN')) {
            $role = ONE::userRole();

            /* Melhorar este método */

            if(!ONE::checkIfValidToken())
                return redirect()->action('AuthController@login');

            if($role == 'admin' || $role == 'manager'){
                Session::put('X-ACCESS-AREA', 'private');
                return $next($request);
            }else if($role == 'user' ){
                return response()->json(['error' => 'Unauthorized'], 401)->send();
            }
        }

        return redirect()->action('AuthController@login');
    }


    /**
     * Implementation for the Authentication Middleware for loggin with an admin.
     *
     * @param $request
     * @param Closure $next
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public static function privateAuthAdminMiddleware($request, Closure $next)
    {
        App::setLocale('en');
        Session::put('LANG_CODE_DEFAULT', 'en');
        return redirect()->action('AuthController@adminLogin');
    }


    /**
     * Set app language code
     *
     * @param $languageCode
     */
    public static function setAppLanguage($languageCode)
    {
        Session::put('LANG_CODE' , $languageCode);
    }

    /**
     * Get app language code
     *
     * @return mixed
     */
    public static function getAppLanguageCode()
    {
        $currentLang = Session::get('LANG_CODE');

        if(empty($currentLang)){
            $allLang = ONE::getAllLanguages();

            foreach($allLang as $lang){
                if(isset($lang->default) && $lang->default){
                    return $lang->code;
                }
            }
        }

        return !empty($currentLang) ? $currentLang : "en";
    }

    /**
     * Get app language name
     *
     * @return mixed
     */
    public static function getAppLanguageName()
    {
        $currentLang = Session::get('LANG_CODE');
        $allLang = ONE::getAllLanguages();

        foreach($allLang as $lang){
            if(!empty($lang->default) && $lang->default && empty($currentLang)){
                return $lang->name;
            }

            if(!empty($currentLang)){
                if($currentLang == $lang->code)
                    return $lang->name;

            }
        }

        return 'English';
    }


    /* LOGS */
    public static function sendLog($type, $message)
    {
        return LogsRequest::sendLog($type,$message);
    }

    public static function getHomePageConfigurations()
    {
        if(!Session::has('homePageConfigurations')){
            $homePageConfigs = Orchestrator::getSiteHomePageConfigurations();
            Session::put('homePageConfigurations',$homePageConfigs);
        }
        return Session::get('homePageConfigurations');
    }

    /* ================= */

    public function getAccessMenu($code = null){
        $menus = '';
        $response = $this->get([
            'component' => 'empatia',
            'api'       => 'accessmenu',
            'method'    => 'info',
            'params'    => ['code' => $code]
        ]);
        
        if(($response->statusCode() == 200)&& !empty($response->json()->id)){
            $response = $this->get([
                'component' => 'empatia',
                'api'       => 'menu',
                'method'    => 'listByAccessId',
                'attribute' => $response->json()->id
            ]);

            if($response->statusCode() == 200){

                $accessMenu = json_decode($response->content(),true);

                $authToken = Session::get('X-AUTH-TOKEN', 'INVALID');
                $menusArray = $accessMenu['data'];
                if ($authToken == 'INVALID'){
                    $menusArray = ONE::verifyArray($accessMenu['data']);

                }
                $menus = $this->buildPublicMenu($menusArray);
            }
        }
        return $menus;
    }

    public function getAccessMenuDemo(){
        if(!empty(Session::get("menu",""))) {

            $menusArray = json_decode(Session::get("menu"), true);
            $menus = $this->buildPublicMenuDemo($menusArray);
            return $menus;
        } else {
            $menus = '';
            $response = $this->get([
                'component' => 'empatia',
                'api' => 'accessmenu',
                'method' => 'info'
            ]);

            if (($response->statusCode() == 200) && !empty($response->json()->id)) {
                $response = $this->get([
                    'component' => 'empatia',
                    'api' => 'menu',
                    'method' => 'listByAccessId',
                    'attribute' => $response->json()->id
                ]);

                if ($response->statusCode() == 200) {

                    $accessMenu = json_decode($response->content(), true);

                    $authToken = Session::get('X-AUTH-TOKEN', 'INVALID');
                    $menusArray = $accessMenu['data'];
                    if ($authToken == 'INVALID') {

                        $menusArray = ONE::verifyArray($accessMenu['data']);
                    }

                    Session::put("menu", json_encode($menusArray));
                    $menus = $this->buildPublicMenuDemo($menusArray);
                }
            }
            return $menus;
        }
    }

    public static function verifyArray($array, $parentPrivate =false){

        foreach ($array as  $key => $menu){

            if(isset($menu[0])  && is_array($menu[0])){
                $array[$key] = ONE::verifyArray($menu);
                if(empty($array[$key])){
                    unset($array[$key]);
                }
            }elseif ($menu['type'] == 'private'){
                if($key == 0){
                    return null;
                }
                unset($array[$key]);
            }
        }

        return $array;
    }


    public function getActionMenu($menu, $absolute = false){
        $action = "";
        if(!array_key_exists('type_id',$menu)){
            return $action;
        }
        switch ($menu['type_id']){
            case 1:
                $action = $absolute ? url($menu['link']) : $menu['link'];
                break;
            case 2:
                $action = URL::action("PublicContentsController@show", $menu['value'], $absolute);
                break;
            case 3:
                $action = URL::action("PublicContentsController@show", ['key' =>$menu['value'],'type' => "news"], $absolute);
                break;
            case 4:
                $action = URL::action("PublicContentsController@show", ['key' =>$menu['value'],'type' => "events"], $absolute);
                break;
            case 5:
                $action = URL::action("PublicCbsController@show", ["id" => $menu['value']], $absolute);
                break;
            case 6:
                $action = URL::action("PublicCbsController@show", ["id" => $menu['value']], $absolute);
                break;
            case 7:
                $action = URL::action("PublicCbsController@show", ["id" => $menu['value'], "type" => "proposal"], $absolute);
                break;
            case 8:
                $action = URL::action("PublicQController@showQ", $menu['value'], $absolute);
                break;
            case 9:
                $action = URL::action("EventSchedulesController@publicAttendance", $menu['value'], $absolute);
                break;
            case 10:
                $action = URL::action("PublicConfEventsController@index", $menu['value'], $absolute);
                break;
            case 11:
                $action = URL::action("PublicCbsController@show", ["id" => $menu['value']], $absolute);
                break;
            case 12:
                $action = URL::action("PublicCbsController@show", ["id" => $menu['value']], $absolute);
                break;
            case 13:
                $action = URL::action("PublicCbsController@show", ["id" => $menu['value']], $absolute);
                break;
            case 14:
                $action = URL::action("PublicCbsController@show", ["id" => $menu['value']], $absolute);
                break;
            case 15:
                $action = URL::action("PublicCbsController@show", ["id" => $menu['value']], $absolute);
                break;
            case 16:
                $action = URL::action("PublicCbsController@show", ["id" => $menu['value']], $absolute);
                break;
            case 17:
                $action = URL::action("PublicCbsController@show", ["id" => $menu['value']], $absolute);
                break;
            case 18:
                $action = URL::action("PublicContentManagerController@showC", ["contentKey" => $menu['value']], $absolute);
                break;
        }
        return $action;
    }


    public static function verifyEmpavilleGeoArea($coord){
        $coords = explode(',', $coord);
        $geoArea = "";

        if(!empty($coords[1])){

            if( ((int)$coords[1]) > 200 ) {
                $geoArea = trans('empaville.Downtown');
            }elseif ( ((int)$coords[1]) < 100 ){
                $geoArea = trans('empaville.Uptown');
            }else{
                $geoArea = trans('empaville.Middletown');
            }
        }
        return $geoArea;
    }

    public static function getEmpavilleImageMap(){
        //return asset("/images/empavilleSchools/parkEmpavilleMapClick.jpg");
        return asset("/images/empaville_map.jpg");
    }

    public static function getEmpavilleParkImageMap(){
        return asset("/images/empavilleSchools/parkEmpavilleMapClick.jpg");
        //return asset("/images/empaville_map.jpg");
    }


    /* ================= */


    //TODO: Remote HTML from this file!


    /* ================= */


    public function buildNestedMenu($menu, $accessMenu, $parent = 0)
    {
        $html = '';
        foreach ($menu as $menuEntry){
            if ($menuEntry->parent_id == $parent) {
                $html .= "<li class='dd-item nested-list-item' data-order='{$menuEntry->position}' data-id='{$menuEntry->menu_key}'>";

                $html .= "<div class='dd-handle nested-list-handle'>";
                $html .= "<span class='glyphicon glyphicon-move'></span>";
                $html .= "</div>";

                $html .= "<div class='nested-list-content'>";

                $html .= "<a href='".URL::action("MenusController@show", $menuEntry->menu_key) . "'>{$menuEntry->title}</a>";
                $html .= "<div class='pull-right' style='margin-top:-3px; color: #fffbfe;'>";
                $html .= ONE::actionButtons($menuEntry->menu_key, ['delete' => 'MenusController@delete']);
                $html .= "</div>";

                $html .= "<div class='pull-right'>";
                $html .= "<a style=\"margin-top:-6px; margin-right: 6px;color: #fffbfe;\" class=\"btn btn-flat btn-info btn-xs\" href='" . action("MenusController@show", $menuEntry->menu_key) . "'><i class=\"fa fa-eye\"></i></a>";
                $html .= "</div>";

                $html .= "</div>";
                $html .= $this->buildNestedMenu($menu, $accessMenu, $menuEntry->id);
                $html .= "</li>";


            }
        }
        return $html ? "\n<ol class=\"dd-list\">\n$html</ol>\n" : null;
    }

    /**
     *
     * Returns Entity Groups Tree View Page Html
     *
     * @param $groups
     * @param null $parent
     * @return null|string
     */
    public function buildNestedEntityGroups($groups, $parent = null)
    {

        $html = '';
        foreach ($groups as $item){
            if ($item->parent_group_id == $parent) {
                $html .= "<li class='dd-item nested-list-item' data-order='{$item->position}' data-id='{$item->entity_group_key}'>";

                $html .= "<div class='dd-handle nested-list-handle'>";
                $html .= "<span class='glyphicon glyphicon-move'></span>";
                $html .= "</div>";

                $html .= "<div class='nested-list-content'>";

                $html .= "<a href='".URL::action("EntityGroupsController@show", $item->entity_group_key) . "'>{$item->name}</a>";
                $html .= "<div class='pull-right' style='margin-top:-3px; color: #fffbfe;'>";
                $html .= ONE::actionButtons($item->entity_group_key, ['delete' => 'EntityGroupsController@delete']);
                $html .= "</div>";

                $html .= "<div class='pull-right'>";
                $html .= "<a style='margin-top:-6px; margin-right: 6px;color: #fffbfe;' class='btn btn-flat btn-success btn-xs' href='" . action("EntityGroupsController@edit", ['group_key' => $item->entity_group_key, 'f' => 'entityGroups']) . "'><i class='fa fa-pencil'></i></a>";
                $html .= "</div>";

                $html .= "</div>";
                $html .= $this->buildNestedEntityGroups($groups, $item->id);
                $html .= "</li>";


            }
        }
        return $html ? "\n<ol class=\"dd-list\">\n$html</ol>\n" : null;
    }

    public function buildNestedBEMenuManagement($menu, $accessMenu, $parent = 0, $currentUser = false, $specificUser = false)
    {
        $html = '';
        foreach ($menu as $menuEntry){
            if ($menuEntry->parent_id == $parent) {
                $canSeeLink = true;
                if (!empty($menuEntry->menu_element->controller) && !empty($menuEntry->menu_element->method)) {
                    if (!empty($menuEntry->menu_element->module_code)) {
                        $moduleTypeCodeToTest = $menuEntry->menu_element->module_type_code;

                        if ($moduleTypeCodeToTest == "-1") {
                            $temp = collect($menuEntry->parameters)->whereIn("element_code", ["access-control", "typeFilter"])->first();
                            if (!empty($temp))
                                $moduleTypeCodeToTest = $temp->value;
                        }
                        if (!ONE::verifyModuleAccess($menuEntry->menu_element->module_code, $moduleTypeCodeToTest))
                            $canSeeLink = false;
                    }

                    if (!empty($menuEntry->menu_element->module_code) && in_array($menuEntry->menu_element->permission, Session::get('user_permissions_sidebar')) && sizeOf(Session::get('user_permissions_sidebar')) > 1)
                        $canSeeLink = false;
                }

                if ($canSeeLink) {
                    $html .= "<li class='dd-item nested-list-item' data-order='{$menuEntry->position}' data-id='{$menuEntry->menu_key}'>";

                    $html .= "<div class='dd-handle nested-list-handle'>";
                    $html .= "<span class='glyphicon glyphicon-move'></span>";
                    $html .= "</div>";

                    $html .= "<div class='nested-list-content'>";

                    $url = "";
                    if ($specificUser)
                        $url = action("UserBEMenuController@userShow", ["userKey" => $specificUser, "menuKey" => $menuEntry->menu_key]);
                    elseif($currentUser)
                        $url = action("UserBEMenuController@show", $menuEntry->menu_key);
                    else
                        $url = action("BEMenuController@show", $menuEntry->menu_key);

                    $html .= "<a href='" . $url . "'>{$menuEntry->name}</a>";
                    $html .= "<div class='pull-right' style='margin-top:-3px; color: #fffbfe;'>";
                    
                    if($specificUser)
                        $html .= ONE::actionButtons(["userKey" => $specificUser, "menuKey" => $menuEntry->menu_key], ['delete' => 'UserBEMenuController@userDelete']);
                    elseif($currentUser)
                        $html .= ONE::actionButtons($menuEntry->menu_key, ['delete' => 'UserBEMenuController@delete']);
                    else
                        $html .= ONE::actionButtons($menuEntry->menu_key, ['delete' => 'BEMenuController@delete']);
                    
                    $html .= "</div>";

                    $html .= "<div class='pull-right'>";
                    

                    $url = "";
                    if ($specificUser)
                        $url = action("UserBEMenuController@userEdit", ["userKey" => $specificUser, "menuKey" => $menuEntry->menu_key, "f" => "BEMenu"]);
                    elseif($currentUser)
                        $url = action("UserBEMenuController@userEdit", ["menuKey" => $menuEntry->menu_key, "f" => "BEMenu"]);
                    else
                        $url = action("BEMenuController@edit", ["menuKey" => $menuEntry->menu_key, "f" => "BEMenu"]);
                    
                    $html .= "<a style=\"margin-top:-6px; margin-right: 6px;color: #fffbfe;\" class=\"btn btn-flat btn-success btn-xs\" href='" . $url . "'><i class=\"fa fa-pencil\"></i></a>";

                    $html .= "</div>";

                    $html .= "</div>";
                    $html .= $this->buildNestedBEMenuManagement($menu, $accessMenu, $menuEntry->id, $currentUser, $specificUser);
                    $html .= "</li>";
                }
            }
        }
        return $html ? "\n<ol class=\"dd-list\">\n$html</ol>\n" : null;
    }

    public function buildNestedBEMenu($menu, $parent = 0)
    {
        $html = '';
        foreach ($menu as $menuEntry) {
            if ($menuEntry->parent_id == $parent) {
                if ($menuEntry->menu_element->code === "section_header") {
                    $html .= "<li class=\"main-menu-title\">";
                    $html .= "<div data-toggle=\"collapse\" href=\"#collapse-".$menuEntry->id."\" class=\"title-menu\" toggle=\"false\"><div class=\"row\"><div class=\"col-9\"><div class=\"menu-border-bottom\">";
                    $html .= $menuEntry->name;
                    $html .= "</div></div><div class=\"col-3\"><i class=\"fa fa-chevron-down pull-right\"></i></div></div></div>";
                        $html .= "<ul id=\"collapse-".$menuEntry->id."\" class=\"collapse sub-menu-wrapper show\">";
                        $html .= $this->buildNestedBEMenu($menu, $menuEntry->id);
                        $html .= "</ul>";
                    $html .= "</li>";
                } else if ($menuEntry->menu_element->code === "external_url") {
                    $link = collect($menuEntry->parameters)->where("parameter.code", "url")->first();
                    if (!empty($link) && !empty($link->value)) {
                        $html .=
                            "<li class='treeview'>" .
                                "<div style='padding-top: 3px'>" .
                                    "<a href='" . $link->value . "'>" .
                                        $menuEntry->name .
                                    "</a>" .
                                "</div>" .
                                "<ul>" .
                                    $this->buildNestedBEMenu($menu, $menuEntry->id) .
                                "</ul>" .
                            "</li>";
                    }

                } else if (!empty($menuEntry->menu_element->controller) && !empty($menuEntry->menu_element->method)) {
                    $canSeeLink = true;
                    if (!empty($menuEntry->menu_element->module_code)) {
                        $moduleTypeCodeToTest = $menuEntry->menu_element->module_type_code;

                        if ($moduleTypeCodeToTest=="-1") {
                            $temp = collect($menuEntry->parameters)->whereIn("element_code",["access-control","typeFilter"])->first();
                            if (!empty($temp))
                                $moduleTypeCodeToTest = $temp->value;
                        }

                        if (!ONE::verifyModuleAccess($menuEntry->menu_element->module_code, $moduleTypeCodeToTest))
                            $canSeeLink = false;
                    }

                    if (!empty($menuEntry->menu_element->module_code) && in_array($menuEntry->menu_element->permission, Session::get('user_permissions_sidebar')) && sizeOf(Session::get('user_permissions_sidebar'))>1)
                        $canSeeLink = false;

                    if ($canSeeLink) {
                        $actionName = $menuEntry->menu_element->controller . "@" . $menuEntry->menu_element->method;

                        $actionParameters = array();
                        foreach ($menuEntry->parameters as $parameter) {
                            $actionParameters[$parameter->parameter->code] = $parameter->value;
                        }

                        $actionURL = "";
                        try {
                            if ($menuEntry->menu_element->code=="entity_groups") {
                                if($groupTypes = ONE::getGroupTypes()) {
                                    foreach($groupTypes as $item) {
                                        $actionURL = action("EntityGroupsController@showGroups", ["groupTypeKey" => $item->group_type_key]);
                                        break;
                                    }
                                }
                            } else
                                $actionURL = action($actionName, $actionParameters);
                        } catch (Exception $e) {

                        }

                        if (!empty($actionURL)) {
                            $url = "http" . (($_SERVER['SERVER_PORT'] == 443) ? "s://" : "://") . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
                            $menuActive = ($url ==$actionURL)  ? "menu-active" : "";
                            if($parent == 0) {
                                $html .=
                                    "<li class='main-menu-title'>" .
                                    "<div class='title-menu menu-border-bottom'>" .
                                    "<a href='" . $actionURL . "' class=\"".$menuActive." d-block\" >" .
                                    $menuEntry->name .
                                    "</a>" .
                                    "</div>" .
                                    "<ul class='margin-bottom-20'>" .
                                    $this->buildNestedBEMenu($menu, $menuEntry->id) .
                                    "</ul>" .
                                    "</li>";

                            } else {
                                $html .=
                                    "<li class='treeview'>" .
                                    "<div class='menu-wrapper'>" .
                                    "<a href='" . $actionURL . "' class=\"".$menuActive."\" >" .
                                    $menuEntry->name .
                                    "</a>" .
                                    "</div>" .
                                    "<ul>" .
                                    $this->buildNestedBEMenu($menu, $menuEntry->id) .
                                    "</ul>" .
                                    "</li>";
                            }
                        }
                    }
                }
            }
        }
        return $html;
    }


    /**
     * Allows reordering of Login Levels
     *
     * @param $groups
     * @return null|string
     */
    public function buildStackedLoginLevels($groups)
    {
        $html = '';
        foreach ($groups as $item){
            $html .= "<li class='dd-item nested-list-item' data-order='{$item->position}' data-id='{$item->level_parameter_key}'>";

            $html .= "<div class='dd-handle nested-list-handle'>";
            $html .= "<span class='glyphicon glyphicon-move'></span>";
            $html .= "</div>";

            $html .= "<div class='nested-list-content'>";

            $html .= "<a href='".URL::action("LoginLevelsController@show", $item->level_parameter_key) . "'>{$item->name}</a>";
            $html .= "<div class='pull-right' style='margin-top:-3px; color: #fffbfe;'>";
            $html .= ONE::actionButtons($item->level_parameter_key, ['delete' => 'LoginLevelsController@delete']);
            $html .= "</div>";

            $html .= "<div class='pull-right'>";
            $html .= "<a style=\"margin-top:-6px; margin-right: 6px;color: #fffbfe;\" class=\"btn btn-flat btn-info btn-xs\" href='" . action("LoginLevelsController@show", $item->level_parameter_key) . "'><i class=\"fa fa-eye\"></i></a>";
            $html .= "</div>";
        }
        return $html ? "\n<ol class=\"dd-list\">\n$html</ol>\n" : null;
    }

    public function buildPublicMenu($menuArray)
    {
        $html = '';
        foreach ($menuArray as $menu) {
            if (empty($menu['id'])) {
                //sub menu 1
                $html .= '<li class="dropdown">';

                $html .= '<a href="#" class="dropdown-toggle" data-toggle="dropdown">' . $menu[0]['title'] . '<span class="caret"></span></a>';
                $html .= '<ul class="dropdown-menu publicMenu">';
                //$html .= '<ul class="dropdown-menu">';
                foreach ($menu as $subMenu) {
                    if (empty($subMenu['id'])) {
                        //subSub menu 2
                        $html .= '<li class="dropdown-menu"><a href="#" class="dropdown-toggle">' . $subMenu[0]['title'] . '</a>';
                        $html .= '<ul class="dropdown-menu publicSubMenu">';

                        foreach ($subMenu as $subSubMenu) {
                            if ($subSubMenu['id'] != $subMenu[0]['id']) {
                                $current = $this->getActionMenu($subSubMenu,true) == URL::current() ? 'current' : '';
                                $html .= '<li><a href="' . $this->getActionMenu($subSubMenu) . '" class="' . $current . '">' . $subSubMenu['title'] . '</a></li>';
                            }
                        }

                        $html .= '</ul></li>';
                    } else {
                        if ($subMenu['id'] != $menu[0]['id']) {
                            $current = $this->getActionMenu($subMenu,true) == URL::current() ? 'current' : '';
                            $html .= '<li><a href="' . $this->getActionMenu($subMenu) . '" class="' . $current . '">' . $subMenu['title'] . '</a></li>';
                        }
                    }
                }
                $html .= '</ul></li>';
            } else {

                $current = $this->getActionMenu($menu,true) == URL::current() ? 'current' : '';
                $html .= '<li><a href="' . $this->getActionMenu($menu) . '" class="' . $current . '">' . $menu['title'] . '</a></li>';
            }
        }
        return $html;
    }

    


    public function buildSideMenu($menuArray, $level = 0)
    {
        $html = '';

        foreach ($menuArray['menu'] as $id => $menu) {
            if (is_array($menu)) {
                $open = false;
                if (count($menuArray['active']) > 1 && in_array($menu[0]->id_menu, $menuArray['active']))
                    $open = true;

                $title = $menu[0]->contents[0]->title;
                if ($level == 1)
                    $title = ' > ' . $title;
                elseif ($level == 2)
                    $title = ' - ' . $title;


                $html .= '<li>';
                $html .= '<a data-toggle="collapse" data-target="#submenu-' . $menu[0]->id_menu . '" style="cursor: pointer">' . $title . '<span class="caret pull-right" style="margin-top: 8px"></span></a>';

                $html .= '<ul id="submenu-' . $menu[0]->id_menu . '" class="nav nav-pills nav-stacked collapse' . ($open ? " in" : "") . '">';

                //BUILD CHILDREN
                $tmpMenu['menu'] = $menu;
                $tmpMenu['active'] = $menuArray['active'];
                $html .= $this->buildSideMenu($tmpMenu, $level + 1);

                $html .= '</ul>';
                $html .= '</li>';

            } else {

                if ($id == 0) {
                    continue;
                }

                $active = false;
                if (in_array($menu->id_menu, $menuArray['active']))
                    $active = true;

                $action = "";
                if ($menu->id_page != 0)
                    $action = URL::action("PagesController@showPublicSubPage", $menu->id_page);
                elseif ($menu->script != "")
                    $action = $menu->script;
                elseif ($menu->link != "") {
                    if (starts_with($menu->link, 'http://')) {
                        $action = $menu->link;
                    } else {
                        $action = '' . $menu->link;
                    }
                }

                $title = $menu->contents[0]->title;
                if ($level == 1)
                    $title = ' > ' . $title;
                elseif ($level == 2)
                    $title = ' - ' . $title;

                if ($id == -1)
                    $html .= '<li id="submenu-parent-item" ' . ($active ? "class=\"active\"" : "") . '><a id="submenu-parent-link" href="' . $action . '">' . $title . '</a></li>';
                else
                    $html .= '<li id="submenu-item" ' . ($active ? "class=\"active\"" : "") . '><a id="submenu-link" href="' . $action . '">' . $title . '</a></li>';

            }
        }

        return $html;
    }

    public function buildPublicMenuDemo($menuArray)
    {
        $html = '';
        foreach($menuArray as $menu){
            if(empty($menu['id']) ){
                $html .=
                    '<li class="nav-item dropdown nav-link">' .
                        '<a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">' . 
                            $menu[0]['title'] . 
                            '<span class="caret"></span>' .
                        '</a>' .
                        '<div class="dropdown-menu">';
                
                foreach ($menu as $subMenu) {
                    if ($subMenu['id'] != $menu[0]['id']) {
                        $html .= 
                            '<a class="nav-link" href="' . $this->getActionMenu($subMenu) . '">' .
                                $subMenu['title'] .
                            '</a>';
                    }
                }
                
                $html .= 
                        '</div>' .
                    '</li>';
            }
            else{
                $current = $this->getActionMenu($menu,true) == URL::current() ? 'active' : '';
                $html .= 
                    '<li class="nav-item nav-link' . $current . '">' .
                        '<a href="' . $this->getActionMenu($menu) . '" class="nav-link">' . 
                            $menu['title'] . 
                        '</a>' .
                    '</li>';


            }
        }
        return $html;
    }

    public function getFileIcon($file)
    {

        if (strpos($file->type, 'pdf') !== false) {
            return "<span class=\"fa fa-file-pdf-o\"></span>";
        } elseif (strpos($file->type, 'image') !== false) {
            return "<span class=\"fa fa-file-image-o\"></span>";
        } elseif (strpos($file->type, 'zip') !== false || strpos($file->type, 'rar') !== false) {
            return "<span class=\"fa fa-file-archive-o\"></span>";
        } else {
            return "<span class=\"fa fa-file-o\"></span>";
        }
    }

    public function fileIconByFilename($filename = ""){

        $ext = pathinfo($filename, PATHINFO_EXTENSION);

        $mTypes = [
            "images" => "jpg,gif,png,tif",
            "docs" => "doc,docx,rtf,zip,pdf,pptx,ppt,xls,xlsx",
            "videos" => "mpg,avi,asf,mov,qt,flv,swf,mp4,wmv,webm,vob,ogv,ogg,mpeg,3gp",
        ];

        if ( $ext == 'pdf' ) {
            return "<i class=\"fa fa-file-pdf-o\"></i>";
        } elseif ($ext == 'jpg' || $ext == 'gif' ||  $ext == 'png' ||  $ext == 'tif' ) {
            return "<i class=\"fa fa-file-image-o\"></i>";
        }  elseif ($ext == 'zip' || $ext == 'rar') {
            return "<i class=\"fa fa-file-archive-o\"></i>";
        }  elseif ($ext == 'doc' ||  $ext == 'docx' ||  $ext == 'rtf' ||  $ext == 'pptx' |  $ext == 'ppt' ||  $ext == 'xls' ||  $ext == 'xlsx') {
            return "<i class=\"fa fa-file\"></i>";
        }  elseif ($ext == 'mpg' || $ext == 'avi' ||  $ext == 'asf' ||  $ext == 'mov' ||  $ext == 'qt'
            || $ext == 'flv' || $ext == 'swf' ||  $ext == 'wmv' ||  $ext == 'webm' ||  $ext == 'vob'
            || $ext == 'ogv' || $ext == 'mpeg' ||  $ext == '3gp'
        ) {
            return "<i class=\"fa fa-video-camera\"></i>";
        } else {
            return "<i class=\"fa fa-file-o\"></i>";
        }
    }

    public function fileIconByFilename2($filename = ""){

        $ext = pathinfo($filename, PATHINFO_EXTENSION);

        $mTypes = [
            "images" => "jpg,gif,png,tif",
            "docs" => "doc,docx,rtf,zip,pdf,pptx,ppt,xls,xlsx",
            "videos" => "mpg,avi,asf,mov,qt,flv,swf,mp4,wmv,webm,vob,ogv,ogg,mpeg,3gp",
        ];

        if ( $ext == 'pdf' ) {
            return "<i class=\"far fa-file-pdf\"></i>";
        } elseif ($ext == 'jpg' || $ext == 'gif' ||  $ext == 'png' ||  $ext == 'tif' ) {
            return "<i class=\"far fa-file-image\"></i>";
        }  elseif ($ext == 'zip' || $ext == 'rar') {
            return "<i class=\"far fa-file-archive\"></i>";
        }  elseif ($ext == 'doc' ||  $ext == 'docx' ||  $ext == 'rtf' ||  $ext == 'pptx' |  $ext == 'ppt' ||  $ext == 'xls' ||  $ext == 'xlsx') {
            return "<i class=\"far fa-file\"></i>";
        }  elseif ($ext == 'mpg' || $ext == 'avi' ||  $ext == 'asf' ||  $ext == 'mov' ||  $ext == 'qt'
            || $ext == 'flv' || $ext == 'swf' ||  $ext == 'wmv' ||  $ext == 'webm' ||  $ext == 'vob'
            || $ext == 'ogv' || $ext == 'mpeg' ||  $ext == '3gp'
        ) {
            return "<i class=\"far fa-video-camera\"></i>";
        } else {
            return "<i class=\"far fa-file\"></i>";
        }
    }

    public function imageButtonUpload($idUploading,$classes = "", $iconClass = "fa fa-upload")
    {
        $html = '<div> ';
        $html .= '<a id="' . $idUploading . '" class="btn btn-flat btn-default btn-xs ' . $classes . '"><i class="'.$iconClass.'"></i> ' . $this->transSite('image.upload') . '</a> ';
        $html .= '</div> ';

        return $html;
    }



    public function fileUploadBox($id, $dropzoneMsg, $title, $idSelect, $idUploading, $idFiles)
    {
        $html = '<div id="' . $id . '" class="box box-primary"> ';
        $html .= '<div style="position: absolute; width: 100%;  text-align: center; opacity: 0.1 ; bottom: 0; font-size: 25px;"> ';
        $html .= '<i class="fa fa-cloud-download"></i> ' . $dropzoneMsg . '</div> ';
        $html .= '<div class="box-header with-border"> ';
        $html .= '<h3 class="box-title"><i class="fa fa-file-o"></i> ' . $title . '</h3> ';
        $html .= '<div class="box-tools pull-right"> ';
        // $html .= '<a class="btn btn-flat btn-info btn-xs"><i class="fa fa-gear"></i> ' . trans('files.type') . '</a> ';
        //$html .= '<a class="btn btn-flat btn-success btn-xs"><i class="fa fa-hand-pointer-o"></i> ' . trans('files.select') . '</a> ';
        $html .= '<a id="' . $idSelect . '" class="btn btn-flat btn-primary btn-xs"><i class="fa fa-upload"></i> ' . trans('files.upload') . '</a> ';
        $html .= '</div> ';
        $html .= '</div> ';
        $html .= '<div id="' . $idFiles . '" class="box-body" style="height: 150px; overflow-y: auto; overflow-x: hidden"> ';
        $html .= '</div> ';
        $html .= '<div id="' . $idUploading . '" class="box-footer" style="display: none"></div> ';
        $html .= '</div> ';

        return $html;
    }

    public function fileSimpleUploadBox($id, $dropzoneMsg, $title, $idSelect, $idUploading, $idFiles)
    {
        $html = '<div id="' . $id . '" class="fileupload-box"> ';
        $html .= '<div class="fu-dropzone-msg" style="position: absolute; width: 100%;  text-align: center; opacity: 0.4 ; bottom: 60px; font-size: 30px;"> ';
        $html .= '<i class="fa fa-cloud-download"></i> ' . $dropzoneMsg . '</div> ';
        $html .= '<div class="box-header with-border box-header-padding"> ';
        $html .= '<div class="row">';
        $html .= '<h3 class=" col-sm-10 box-title"><i class="fa fa-file-o"></i> ' . $title . '</h3> ';
        $html .= '<div class="col-sm-2 box-tools"> ';
        $html .= '<a id="' . $idSelect . '" class="btn btn-flat empatia btn-xs pull-right"><i class="fa fa-upload"></i> ' . trans('files.upload') . '</a> ';
        $html .= '</div>';
        $html .= '</div> ';
        $html .= '</div>';
        $html .= '<div id="' . $idFiles . '" class="box-body" style="min-height: 150px; max-height: 200px; overflow-y: auto; overflow-x: hidden;"> ';
        $html .= '</div> ';
        $html .= '<div id="' . $idUploading . '" class="box-footer" style="display: none"></div> ';
        $html .= '</div> ';

        return $html;
    }

    public function fileSingleUploadBox($id, $dropzoneMsg, $idSelect, $idUploading, $fileName)
    {
        $html = '<div id="' . $id . '" title="' . $dropzoneMsg . '" class="box single-upload-box"> ';
        $html .= '<div>';
        $html .= '<span id="file_name_uploaded">';
        $html .= $fileName;
        $html .= '</span>';
        $html .= '<a id="' . $idSelect . '" class="btn btn-flat single-upload-button btn-xs" style="float: right;"><i class="fa fa-upload"></i> ' . trans('files.upload') . '</a> ';
        $html .= '</div> ';
        $html .= '<div id="' . $idUploading . '" class="box-footer" style="display: none"></div> ';
        $html .= '</div> ';

        return $html;
    }

    public function fileDetailsModal($file, $action, $id, $title)
    {
        $html = '<div class="modal fade in" id="' . $id . '" role="dialog"> ';
        $html .= '<div class="modal-dialog"> ';
        $html .= '<div class="modal-content"> ';
        $html .= '<div class="modal-header"> ';
        $html .= '<button type="button" class="close" data-dismiss="modal">&times;</button> ';
        $html .= '<h4 class="modal-title">' . $title . '</h4> ';
        $html .= '</div> ';
        $html .= '<div class="modal-body"> ';
        $html .= '<div class="row"> ';
        $html .= '<div class="col-md-12"> ';
        $html .= Form::open()->id('fileDetails')->action($action)->post();
        $html .= Form::text(trans("pages.file_name"), "file_name", isset($file) ? $file->pivot->name : "");
        $html .= Form::text(trans("pages.file_description"), "file_description", isset($file) ? $file->pivot->description : "");
        $html .= Form::submit(trans("pages.submitFileDetails"));
        $html .= Form::close();
        $html .= '</div> ';
        $html .= '</div> ';
        $html .= '</div> ';
        $html .= '</div> ';
        $html .= '</div> ';
        $html .= '</div> ';

        return $html;
    }

    public function imageCropModal($id, $idTitle, $title)
    {
        $html = '<div class="modal fade docs-cropped" id="' . $id . '" aria-hidden="true" aria-labelledby="' . $idTitle . '" role="dialog" tabindex="-1"> ';
        $html .= '<div class="modal-dialog"> ';
        $html .= '<div class="modal-content"> ';
        $html .= '<div class="card">';
        $html .= '<div class="card-header"> ';
        $html .= '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button> ';
        $html .= '<h4 class="modal-title" id="' . $idTitle . '">' . $title . '</h4> ';
        $html .= '</div> ';
        $html .= '<div class="card-body"> ';
        $html .= '<div class="docs-preview clearfix"> ';
        $html .= '<div class="img-preview preview-lg"></div> ';
        $html .= '</div> ';
        $html .= '<div class="row"> ';
        $html .= '<div class="col-md-12"> ';
        $html .= '<div class="img-container"> ';
        $html .= '<img id="banner_img" src="" alt=""> ';
        $html .= '</div> ';
        $html .= '</div> ';
        $html .= '</div> ';
        $html .= '</div> ';
        $html .= '<div class="modal-footer"> ';
        $html .= '<button type="button" class="btn btn-default" data-dismiss="modal">' . trans('files.close') . '</button> ';
        $html .= '<a class="btn btn-primary" id="save_banner" data-method="getCroppedCanvas">' . trans('files.save') . '</a> ';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div> ';
        $html .= '</div> ';
        $html .= '</div> ';

        return $html;
    }

    public function fileUploader($variable, $route, $uploadedFunction, $idBrowseButton, $idDropZone, $idUploading, $idFiles, $type, $uploadToken,$acceptedTypes = null, $singleFile = false)
    {
        $maxFileSize = 25;

        $mTypes = [
            "images" => "jpg,gif,png,tif",
            "docs" => "doc,docx,rtf,zip,rar,pdf,xls,ppt,xppt",
            "videos" => "mpg,avi,asf,mov,qt,flv,swf,mp4,wmv,webm,vob,ogv,ogg,mpeg,3gp",
        ];

        $mTypesTitles = [
            "images" =>  "Images",
            "docs"   => "Docs",
            "excel"  => "Excel",
            "zip" => "Zip",
            "videos" => "Videos"
        ];

        $html = "var " . $variable . " = new plupload.Uploader({ ";
        $html .= "headers: { ";
        $html .= "'X-CSRF-TOKEN': \"" . csrf_token() . "\", ";
        $html .= "'X-UPLOAD-TOKEN': \"" . $uploadToken . "\", ";
        $html .= "'X-AUTH-TOKEN': \"" . Session::get('X-AUTH-TOKEN', 'INVALID') . "\" ";
        $html .= "}, ";
        $html .= "browse_button: '" . $idBrowseButton . "', ";
        $html .= "drop_element: document.getElementById('" . $idDropZone . "'), ";
        $html .= "runtimes: 'html5,flash,silverlight,html4', ";
        $html .= "url: \"" . $route . "\", ";
        $html .= "chunk_size: '1mb', ";

        if ($singleFile) {
            $html .= "multi_selection: false, ";
        }

        $html .= "filters: { ";

        // Maximum file size
        if(!empty($maxFileSize) && $maxFileSize!=0){
            $html .=  " max_file_size: '".$maxFileSize."mb',";
        }
        // Specify what files to browse for
        if(!empty($acceptedTypes)){
            $html .=  "mime_types: [";
            foreach($acceptedTypes as $tmp){
                $html .=  "{title: \"".$mTypesTitles[$tmp]."\", extensions: \"$mTypes[$tmp]\"},";
            }
            $html .=  "]";
        }

        $html .= "}, ";

        $html .= "flash_swf_url: \"" . asset('vendor/jildertmiedema/laravel-plupload/js/Moxie.swf') . "\", ";
        $html .= "silverlight_xap_url: \"" . asset('vendor/jildertmiedema/laravel-plupload/js/Moxie.xap') . "\", ";
        $html .= "init: { ";
        $html .= "PostInit: function () { ";
        $html .= "}, ";
        $html .= "FilesAdded: function (up, files) { ";
        $html .= "plupload.each(files, function (file) { ";
        $html .= "$(\"#" . $idUploading . "\").append(\"<div id='\" + file.id + \"' class='row'><div class='col-xs-6 col-6'>\" + file.name + \" (\" + plupload.formatSize(file.size) + \")</div><div class='col-xs-6 col-6'><div class='progress' style='margin-bottom: 0px'><div class='progress-bar progress-bar-success' role='progressbar' aria-valuenow='0' aria-valuemin='0' aria-valuemax='100' style='width:0%'><span>0%</span></div></div></div></div>\"); ";
        $html .= "}); ";
        $html .= $variable . ".start(); ";
        $html .= "$(\"#" . $idUploading . "\").slideDown(); ";
        $html .= "}, ";
        $html .= "UploadProgress: function (up, file) { ";
        $html .= "var div = $(\"#" . $idUploading . " #\" + file.id); ";
        $html .= "div.find(\"span\").html(file.percent + \"%\"); ";
        $html .= "div.find(\".progress-bar\").width(file.percent + \"%\"); ";
        $html .= "div.find(\".progress-bar\").attr(\"aria-valuenow\", file.percent); ";
        $html .= "}, ";
        $html .= "FileUploaded: function (up, file, obj) { ";
        $html .= "$(\"#" . $idUploading . " #\" + file.id).remove(); ";
        $html .= "try { ";
        $html .= $uploadedFunction . "(file.id, JSON.parse(obj.response), '#" . $idFiles . "', '" . $idUploading . "', " . $type . "); ";
        $html .= "} catch (e) { ";
        $html .= "console.log(e);";
        $html .= "} ";
        $html .= "}, ";
        $html .= "Error: function (up, err) { ";
        $html .= "toastr[\"error\"](err.message);";
        $html .= "var div = $(\"#" . $idUploading . " #\" + err.file.id); ";
        $html .= "div.find(\"span\").html(\"" . trans('files.error') . ": \" + err.message); ";
        $html .= "div.find(\".progress-bar\").width(\"100%\"); ";
        $html .= "div.find(\".progress-bar\").attr(\"aria-valuenow\", 100); ";
        $html .= "div.find(\".progress-bar\").removeClass(\"progress-bar-success\").addClass(\"progress-bar-danger\"); ";
        $html .= "} ";
        $html .= "} ";
        $html .= "}); ";

        return $html;
    }


    public function imageUploader($variable, $route, $uploadedFunction, $idBrowseButton, $idDropZone, $idUploading, $idFiles, $idModal, $aspectRatio, $type, $uploadToken)
    {
        $maxFileSize = 20;

        $html = "var " . $variable . " = new plupload.Uploader({ ";
        $html .= "headers: { ";
        $html .= "'X-CSRF-TOKEN': \"" . csrf_token() . "\", ";
        $html .= "'X-UPLOAD-TOKEN': \"" . $uploadToken . "\", ";
        $html .= "'X-AUTH-TOKEN': \"" . Session::get('X-AUTH-TOKEN', 'INVALID') . "\" ";
        $html .= "}, ";
        $html .= "browse_button: '" . $idBrowseButton . "', ";
        $html .= "drop_element: document.getElementById('" . $idDropZone . "'), ";
        $html .= "runtimes: 'html5,flash,silverlight,html4', ";
        $html .= "url: \"" . $route . "\", ";
        $html .= "chunk_size: '1mb', ";
        $html .= "multi_selection: false, ";
        $html .= "filters: { ";
        // Maximum file size
        if(!empty($maxFileSize) && $maxFileSize!=0){
            $html .=  " max_file_size: '".$maxFileSize."mb',";
        }
        // Specify what files to browse for
        $html .=  "mime_types: [";
        $html .=  "{title: \"images\", extensions: \"jpg,gif,png\"},";
        $html .=  "]";

        $html .= "}, ";
        $html .= "flash_swf_url: \"" . asset('vendor/jildertmiedema/laravel-plupload/js/Moxie.swf') . "\", ";
        $html .= "silverlight_xap_url: \"" . asset('vendor/jildertmiedema/laravel-plupload/js/Moxie.xap') . "\", ";
        $html .= "init: { ";
        $html .= "PostInit: function () { ";
        $html .= "}, ";
        $html .= "FilesAdded: function (up, files) { ";

        $html .= "originalData = {}; ";
        $html .= "if ($('#" . $idModal . "').hasClass('in')) { ";
        $html .= "$('#" . $idModal . "').modal('hide'); ";
        $html .= $variable . ".start(); ";
        $html .= "return; ";
        $html .= "} ";
        $html .= "$('#" . $idModal . "').modal(); ";
        $html .= "if (files && files[0]) {";
        $html .= "var reader = new FileReader(); ";
        $html .= "reader.onload = function (e) { ";
        $html .= "$('.img-container > img').attr('src', e.target.result); ";
        $html .= "}; ";
        $html .= "reader.readAsDataURL(files[0].getNative()); ";
        $html .= "var \$image = $(\".img-container > img\"); ";
        $html .= "up.splice(); ";
        $html .= "reader.onload = function (oFREvent) { ";
        $html .= "\$image.cropper('destroy'); ";
        $html .= "\$image.attr('src', this.result); ";
        $html .= "$(\".img-container > img\").attr(\"title\", files[0].name); ";
        $html .= "$(\".img-container > img\").attr(\"uploader\", \"" . $variable . "\"); ";
        $html .= "var options = { ";
        $html .= "aspectRatio: " . $aspectRatio . ", ";
        $html .= "dragMode: 'move', ";
        $html .= "crop: function (e) { ";
        $html .= "} ";
        $html .= "}; ";
        $html .= "\$image.on({}).cropper(options); ";
        $html .= "}; ";
        $html .= "} ";
        $html .= "}, ";
        $html .= "UploadProgress: function (up, file) { ";
        $html .= "var div = $(\"#" . $idUploading . " #\" + file.id); ";
        $html .= "div.find(\"span\").html(file.percent + \"%\"); ";
        $html .= "div.find(\".progress-bar\").width(file.percent + \"%\"); ";
        $html .= "div.find(\".progress-bar\").attr(\"aria-valuenow\", file.percent); ";
        $html .= "}, ";
        $html .= "FileUploaded: function (up, file, obj) { ";
        $html .= "try { ";
        $html .= $uploadedFunction . "(file.id, JSON.parse(obj.response), '#" . $idFiles . "', '" . $idUploading . "', " . $type . "); ";
        $html .= "} catch (e) { ";
        $html .= "console.log(e);";
        $html .= "} ";
        $html .= "}, ";
        $html .= "Error: function (up, err) { ";
        // Error on upload
        $html .= "toastr[\"error\"](err.message);";
        $html .= "var div = $(\"#" . $idUploading . " #\" + err.file.id); ";
        $html .= "div.find(\"span\").html(\"" . trans('files.error') . ": \" + err.message); ";
        $html .= "div.find(\".progress-bar\").width(\"100%\"); ";
        $html .= "div.find(\".progress-bar\").attr(\"aria-valuenow\", 100); ";
        $html .= "div.find(\".progress-bar\").removeClass(\"progress-bar-success\").addClass(\"progress-bar-danger\"); ";
        $html .= "} ";
        $html .= "} ";
        $html .= "}); ";

        return $html;
    }

    public function addTinyMCE($idField,$configurations = [])
    {
        // TinyMCE init
        $html = "tinyMCE.init({ ";
        $html .= "selector: \"".$idField."\", ";
        $html .= "setup: function (editor) {editor.on('change', function (e) {editor.save();});},";
        $html .= "language: \"".(array_key_exists('language',$configurations)? $configurations['language']: 'en_GB'). "\", ";
        $html .= "theme: \"".(array_key_exists('theme',$configurations)? $configurations['theme']: 'modern'). "\", ";
        $html .= "skin: \"".(array_key_exists('skin',$configurations)? $configurations['skin']: 'lightgray'). "\", ";
        // Option TinyMCE
        $html .= "convert_urls : 0,";
        $html .= "remove_script_host : 0,";
        $html .= "browser_spellcheck: \"".(array_key_exists('browser_spellcheck',$configurations)? $configurations['browser_spellcheck']: 'en_GB'). "\", ";

        $html .= "convert_urls : 0,";

        $html .= "extended_valid_elements: '+div[*],+a[*],+span[*]',";
        $html .= "valid_children: '+a[h1|h2|h3|h4|h5|span|div|img]',";

        // Default plugins
        if(!array_key_exists('plugins',$configurations) ){
            $configurations["plugins"] = ["advlist autolink link image lists charmap print preview hr anchor pagebreak",
                "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
                "save table contextmenu directionality emoticons template paste textcolor"];
        }

        // Plugins
        if(!empty($configurations['plugins'])){
            $html .= "plugins: [ ";
            foreach($configurations["plugins"] as $pluggin){
                $html .= "\"".$pluggin."\",";
            }
            $html .= " ], ";
        }

        $html .= "toolbar: \"".(array_key_exists('toolbar',$configurations)? $configurations['toolbar']: 'insertfile undo redo | styleselect | bold italic fontsizeselect | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | print preview media fullpage | forecolor backcolor emoticons'). "\", ";

        // Action/URL for images and files
        if(array_key_exists('action',$configurations)) {
            $html .= "file_browser_callback: function(field_name, url, type, win) { ";
            $html .= "tinymce.activeEditor.windowManager.open({ ";
            $html .= "title: \"Link Browser\", ";
            $html .= "file: \"" . $configurations['action'] . "\", ";
            $html .= "width: 800, ";
            $html .= "height: 300, ";
            $html .= "}, { ";
            $html .= "oninsert: function(url) { ";
            $html .= "win.document.getElementById(field_name).value = url; ";
            $html .= "}, ";
            $html .= "type: type ";
            $html .= "}); ";
            $html .= "} ";
        }

        $html .= "}) ";

        return $html;
    }



    public static function verifyModulesActive(){
        if(!Cache::has('entityModulesActive_'.ONE::getEntityKey())){
            $modules = json_decode(json_encode(Orchestrator::getEntityModules()),true);
            Cache::put('entityModulesActive_'.ONE::getEntityKey(), $modules, 60);
        }
        return Cache::get('entityModulesActive_'.ONE::getEntityKey());
    }


    public static function verifyModuleAccess($moduleCode, $moduleTypesCode = null){
        if(!ONE::getEntityKey()){
            return false;
        }
        $modules = ONE::verifyModulesActive();

        if($moduleTypesCode && array_key_exists($moduleCode,$modules)){
            return array_key_exists($moduleTypesCode,$modules[$moduleCode]['types']);
        }else{
            return array_key_exists($moduleCode,$modules);
        }
    }

    public static function checkPermissions($permRequired) {
        $user_role = Session::get("user_role");
        $user_perm = Session::get("manager_user_permission");

        // If admin always return true
        if($user_role == "admin") return true;

        if(is_null($user_perm)) return false;

        // If not an array (only one permission to check) convert to array
        if(!is_array($permRequired))
            $permRequired = [$permRequired];

        if(is_object($user_perm))
            $user_perm = get_object_vars($user_perm);

        // For each permission required check if user has permission
        foreach($permRequired as $perm) {
            // If user has required permission return true
            if(in_array($perm, $user_perm)) return true;
        }

        // User has no permission => return false
        return false;
    }

    public static function checkRoutePermissions($perms, $cbPerms, $controller) {
        $route = \Route::getCurrentRoute();
        $method = $route->getActionMethod();

        $cbKey = null;
        if(isset($route->parameters["cbId"])) {
            $cbKey = $route->parameters["cbId"];
        } else if(isset($route->parameters["cb_key"])) {
            $cbKey = $route->parameters["cb_key"];
        } else if(isset($route->parameters["cbKey"])) {
            $cbKey = $route->parameters["cbKey"];
        } else if(isset($route->parameters["cb"])) {
            $cbKey = $route->parameters["cb"];
        } else if(isset($route->parameters["pad_key"])) {
            $cbKey = $route->parameters["pad_key"];
        }

        // If admin always return true
        $user_role = Session::get("user_role");
        if($user_role == "admin") {
            if($cbKey == null && !isset($perms[$controller])) {

             // Notify admin there is no permission for this controller

                Session::flash('message', 'perm_error_controller: ' . $controller);
            } else if($cbKey != null && !isset($cbPerms[$controller])) {
                // Notify admin there is no permission for this controller & method
                Session::flash('message', 'perm_error_controller_cb: '.$cbKey." - ".$controller);
            } else if($cbKey != null && !isset($cbPerms[$controller][$method]) && !isset($cbPerms[$controller]["all"])) {
                // Notify admin there is no permission for this controller & method
                Session::flash('message', 'perm_error_controller_method_cb: '.$cbKey." - ".$controller." - ".$method);
            }
            return true;
        }

        // If controller permission not configured => ACCEPT PERMISSION!
        if(!isset($cbPerms[$controller])) {
            return true;
        }

        // Make sure it is an array
        if(!is_array($cbPerms[$controller]))
            $cbPerms[$controller] = [$cbPerms[$controller]];

        if($cbKey != null) {
            // User has "participation_admin"
            if(ONE::checkPermissions("participation_admin")) return true;

            // If controller METHOD permission not configured => ACCEPT PERMISSION!
            if(!isset($cbPerms[$controller][$method])) {
                // Check all permissions if no specific method is found
                if(isset($cbPerms[$controller]["all"])) {
                    foreach($cbPerms[$controller]["all"] as $perm) {
                        if ($perm == "all") return true;

                        // Check if user has permission for controller & method
                        if (ONE::checkCBPermissions($cbKey, $perm)) {
                            return true;
                        }
                    }
                    return false;
                }
                // Only true if all is not set => ACCEPT PERMISSION!
                return true;
            }

            // Check controller & method permissions for CB
            foreach($cbPerms[$controller][$method] as $perm) {
                if ($perm == "all") return true;

                // Check if user has permission for controller & method
                if (ONE::checkCBPermissions($cbKey, $perm)) {
                    return true;
                }
                // Check if user has permission for cb
                if (ONE::checkCBPermissions($cbKey, null)) {
                    return true;
                }
            }

            // No permission
            return false;
        } else {
            // Check controller permissions
            foreach ($perms[$controller] as $perm) {
                if ($perm == "all") return true;
                if (ONE::checkPermissions($perm)) return true;
                return false;
            }

            // No permission
            return false;
        }
    }

    public static function checkCBPermissions($cb_key, $permRequired) {
        $user_role = Session::get("user_role");

        // If admin always return true
        if($user_role == "admin") return true;

        // User has "participation_admin"
        if(ONE::checkPermissions("participation_admin")) return true;

        if($permRequired == null || $permRequired == 'show' ){
            if(Session::has("manager_user_permission_cbs")) {
                $user_perm_cb = Session::get("manager_user_permission_cbs");

                // If not an array (only one permission to check) convert to array
                if(!is_array($user_perm_cb)) {
                    //$user_perm_cb = [$user_perm_cb];
                }

                if(is_object($user_perm_cb))
                    $user_perm_cb = get_object_vars($user_perm_cb);

                // If user has required permission return true
                if(in_array($cb_key,$user_perm_cb)) {
                    return true;
                }
            }
        }

        // Permissions not in session => get permissions
        $entityKey = Session::get("X-ENTITY-KEY");
        $user = Session::get("user");
        $user_key = $user->user_key;

        if(!Session::has("manager_user_permission_cbs_".$cb_key)) {
            $user_perm = EMPATIA::getUserCBPermissions($cb_key, $user_key, $entityKey);
            Session::put("manager_user_permission_cbs_".$cb_key, $user_perm);
        } else {
            $user_perm = Session::get("manager_user_permission_cbs_".$cb_key);
        }

        // If CB admin permission return true
        if(!is_array($user_perm))
            $user_perm = get_object_vars($user_perm);

        if(in_array("participation_admin", $user_perm)) return true;

        // If not an array (only one permission to check) convert to array
        if(!is_array($permRequired))
            $permRequired = [$permRequired];

        // For each permission required check if user has permission in the CB
        foreach($permRequired as $perm) {
            // If user has required permission return true
            if(in_array($perm, $user_perm)) return true;
        }

        // User has no permission => return false
        return false;
    }


    /**
     * Returns a list of all Entity Group Types to be listed on sidebar Menu
     *
     * @return bool|mixed
     */
    public static function getGroupTypes(){

        try {
            $groupTypes = Orchestrator::getGroupTypes();

            foreach ($groupTypes as $item) {
                unset($item->id);
                unset($item->created_at);
                unset($item->updated_at);
            }

            return $groupTypes;


        }catch(Exception $e) {
            return false;
        }


    }


    public static function performanceEvaluation($id, $action, $comunicationComp, $jsonInformation = null, $url = null){
        $redis = Redis::connection();
        $redis->rpush('performance', json_encode(
            array(
                'request_key'           => $id,
                'component'             => 'WUI',
                'session_id'            => session()->getId(),
                'action'                => $action,
                'url'                   => $url,
                'comunication_component'=> $comunicationComp,
                'json_msg'              => $jsonInformation,
                'timestamp'             => microtime(true)
            )
        ));
    }


    public static function verifyUserMenuPermissions(){
        // DISABLE OLD PERMISSIONS
        return true;

        if(ONE::isAdmin()){
            return ['all'];
        }

        $userPermissions = Session::get('user_permissions');
        if(empty($userPermissions)){
            return [];
        }

        $show = [];

        foreach($userPermissions as $moduleKey => $module){
            foreach($module as $key => $permissions){
                foreach($permissions as $permission){
                    if($permission == 1)
                        $show[] = $key;
                }
            }
        }


        return $show;
    }

    public static function verifyUserMenuGroupPermissions(){
        // DISABLE OLD PERMISSIONS
        return true;

        if(ONE::isAdmin()){
            return ['all'];
        }

        $userPermissions = Session::get('user_permissions');

        $participation = ['cb' => ['idea', 'forum', 'discussion', 'proposal', 'publicConsultation', 'tematicConsultation', 'survey', 'project'], 'mp' => ['mp'], 'q' => ['poll']];
        $contents = ['orchestrator' =>  ['entity_site'], 'cm' => ['menu', 'pages', 'news', 'events', 'pages']];
        $users = ['auth' => ['manager', 'user', 'in_person_registration', 'confirm_user', 'user_parameters']];
        $research = ['q' => ['q'], 'analytics' => ['test_code'], 'wui' => ['open_data']];
        $communication = ['wui' => ['email', 'sms', 'history']];
        $configurations = ['wui' => ['entity_groups'], 'orchestrator' => ['role'], 'cm' => ['home_page_type'], 'cb' => ['parameter_template']];

        $showGroup = [];
        foreach($participation as $key => $modules){
            foreach($modules as $permissions){
                if (!empty($userPermissions->$key->$permissions)){
                    foreach($userPermissions->$key->$permissions as $permission){
                        if($permission == 1){
                            $showGroup[] = 'participation';
                            break;
                        }
                    }
                }

            }
        }

        foreach($contents as $key => $modules){
            foreach($modules as $permissions){
                if (!empty($userPermissions->$key->$permissions)) {
                    foreach($userPermissions->$key->$permissions as $permission){
                        if($permission == 1){
                            $showGroup[] = 'contents';
                            break;
                        }
                    }
                }
            }
        }

        foreach($users as $key => $modules){
            foreach($modules as $permissions){
                if (!empty($userPermissions->$key->$permissions)) {
                    foreach($userPermissions->$key->$permissions as $permission){
                        if($permission == 1){
                            $showGroup[] = 'users';
                            break;
                        }
                    }
                }
            }
        }

        foreach($research as $key => $modules){
            foreach($modules as $permissions){
                if (!empty($userPermissions->$key->$permissions)) {
                    foreach($userPermissions->$key->$permissions as $permission){
                        if($permission == 1){
                            $showGroup[] = 'research';
                            break;
                        }
                    }
                }
            }
        }

        foreach($communication as $key => $modules){
            foreach($modules as $permissions){
                if (!empty($userPermissions->$key->$permissions)) {
                    foreach($userPermissions->$key->$permissions as $permission){
                        if($permission == 1){
                            $showGroup[] = 'communication';
                            break;
                        }
                    }
                }
            }

        }

        foreach($configurations as $key => $modules){
            foreach($modules as $permissions){
                if (!empty($userPermissions->$key->$permissions)){
                    foreach($userPermissions->$key->$permissions as $permission){
                        if($permission == 1){
                            $showGroup[] = 'configurations';
                            break;
                        }
                    }
                }
            }
        }

        return $showGroup;
    }

    public static function verifyUserPermissions($module, $moduleType, $action){
        // DISABLE OLD PERMISSIONS
        return true;

        if(ONE::isAdmin()){
            return true;
        }

        $userPermissions = Session::get('user_permissions');
        if(empty($userPermissions)){
            return false;
        }

        if(isset($userPermissions->$module->$moduleType)){
            $permission = $userPermissions->$module->$moduleType;
            switch($action){
                case 'show': if($permission->permission_show == 1)
                    return true;
                    break;
                case 'create': if($permission->permission_create == 1)
                    return true;
                    break;
                case 'update': if($permission->permission_update == 1)
                    return true;
                    break;
                case 'delete': if($permission->permission_delete == 1)
                    return true;
                    break;
            }
        }

        return false;
    }

    public static function verifyUserPermissionsCrud($module, $moduleType){
        // DISABLE OLD PERMISSIONS
        return true;


        if(ONE::isAdmin()){
            return true;
        }

        $userPermissions = Session::get('user_permissions');
        if(empty($userPermissions)){
            return false;
        }

        if(isset($userPermissions->$module->$moduleType)){
            $permission = $userPermissions->$module->$moduleType;

            if($permission->permission_show == 1 or $permission->permission_create == 1 or $permission->permission_update == 1 or $permission->permission_delete == 1)
                return true;
        }

        return false;
    }

    public static function verifyUserPermissionsCreate($module, $moduleType){
        // DISABLE OLD PERMISSIONS
        return true;


        if(isset(Session::get('user_permissions')->$module->$moduleType)){
            if(Session::get('user_permissions')->$module->$moduleType->permission_create == 1)
                return true;
        }

        return false;
    }

    public static function verifyUserPermissionsUpdate($module, $moduleType){
        // DISABLE OLD PERMISSIONS
        return true;


        if(isset(Session::get('user_permissions')->$module->$moduleType)){
            if(Session::get('user_permissions')->$module->$moduleType->permission_update == 1)
                return true;
        }

        return false;
    }

    public static function verifyUserPermissionsDelete($module, $moduleType){
        // DISABLE OLD PERMISSIONS
        return true;


        if(isset(Session::get('user_permissions')->$module->$moduleType)) {
            if (Session::get('user_permissions')->$module->$moduleType->permission_delete == 1)
                return true;
        }

        return false;
    }

    public static function verifyUserPermissionsShow($module, $moduleType){
        // DISABLE OLD PERMISSIONS
        return true;

        if(isset(Session::get('user_permissions')->$module->$moduleType)) {
            if (Session::get('user_permissions')->$module->$moduleType->permission_show == 1)
                return true;
        }

        return false;
    }


    /** Get site Ethic by type
     * @param $type
     * @return string
     */
    public static function getSiteEthic($type){
        $siteEthics = Session::get('site_ethics');
        $langCode = Session::get('LANG_CODE');
        $langCodeDefault = Session::get('LANG_CODE_DEFAULT');
        $siteEthic = '';

        if (empty($siteEthics)) {
            try {
                $site = Orchestrator::getSiteEntity($_SERVER["HTTP_HOST"]);
                $siteEthics = $site->site_ethics;
                Session::put('site_ethics' , $siteEthics);
            } catch (Exception $e) {

            }
        }

        if(isset($siteEthics)&& isset($siteEthics->{$type}) && (isset($langCode) || isset($langCodeDefault))){
            $contentTranslation = $siteEthics->{$type}->{$langCode} ?? null;
            $contentTranslationDefault = $siteEthics->{$type}->{$langCodeDefault} ?? null;
            if(isset($contentTranslation) || isset($contentTranslationDefault)){
                $siteEthic = $contentTranslation->content ?? $contentTranslationDefault->content;
            }
        }
        if (isset($siteEthics) && empty($siteEthic))
            $siteEthic = collect($siteEthics->{ $type } ?? [])->first()->content ?? null;

        return $siteEthic;
    }




    /** Request for reCaptcha Google */

    public static function verifyRecaptcha($secretKey, $recaptchaResponse){

        $recaptcha = new \ReCaptcha\ReCaptcha($secretKey);
        $resp = $recaptcha->verify($recaptchaResponse);

        if ($resp->isSuccess()) {
            return true;
        } else {
            return false;
        }

    }



    public static function getGoogleAnalytics(){

        $value = Session::get("SITE-CONFIGURATION.google_analytics");
        if(!empty($value)){
            return $value;
        }
        return false;

    }

    public static function getPiwikAnalytics(){

        $value = Session::get("SITE-CONFIGURATION.piwik_analytics");
        if(!empty($value)){
            return $value;
        }
        return false;

    }

    /**
     * Verify if next level is SMS verification
     * @return bool
     */

    public static function getNextLevelSMSVerification(){
        $user = Session::get('user');
        if (!empty( $user->user_level)){
            $siteLevels = Orchestrator::getSiteLoginLevels();
            foreach ($siteLevels as $siteLevel){
                if ( $siteLevel->position == ($user->user_level+1) && $siteLevel->sms_verification == 1){
                    return true;
                }
            }

        }
        return false;
    }

    /**
     * Verify if next level is SMS verification
     * @return bool
     */

    public static function getNextLevelManualModeration(){
        $user = Session::get('user');
        if (!empty( $user->user_level)){
            $siteLevels = Orchestrator::getSiteLoginLevels();
            foreach ($siteLevels as $siteLevel){
                if ( $siteLevel->position == ($user->user_level+1) && $siteLevel->manual_verification == 1){
                    return true;
                }
            }

        }
        return false;
    }


    public static function getRedirectionAccordingToStep()
    {

        $user = Session::get('user');

        if (isset($user->user_level)){
            if($user->user_level == 0){ // HARDCODED LEVEL 0 [STEPPER PARAMETERS]
                return action('AuthController@stepperManager',['step' => 'parameters' ]);
            }
        }
        if(ONE::getNextLevelSMSVerification()){
            return action('AuthController@stepperManager',['step' => 'sms_validation' ]);
        }

        if(ONE::getNextLevelManualModeration()){
            return action("SubPagesController@show",["auth","waitingModeration"]);
        }

    }


    public static function getNewMessages()
    {
        if(Session::has('user')){
            $messages = collect(Orchestrator::getMessages())->where('viewed',false)->where('to',Session::get('user')->user_key);
            if($messages){
                return count($messages);
            }else{
                return 0;
            }
        }else{
            return 0;
        }
    }

    /**
     * @param $request
     * @return mixed
     */
    public static function tableData($request)
    {
        $order['column'] = $request->order[0]['column'] ?? null;

        $dataTable['start'] = $request->start ?? null;
        $dataTable['length'] = $request->length ?? null;
        $dataTable['order']['value'] = $request->columns[$order['column']]['data'] ?? null;
        $dataTable['order']['dir'] = $request->order[0]['dir'] ?? null;
        $dataTable['search'] = $request->search ?? null;

        return $dataTable;
    }


    /**
     * CHECK IF A USER CAN CREATE A TOPIC
     * @param $configurations
     * @param $isModerator
     * @param $cb
     * @param $type
     * @return string
     */
    public static function verifyUserCanCreateTopic($configurations,$isModerator, $cb)
    {
        $today = Carbon::today()->format('Y-m-d');

        //CHECK IF CURRENT USER IS A MODERATOR OR A ADMIN
        if(ONE::isAdmin() || (!empty($isModerator) && $isModerator == 1)){
            return true;
        }


        //CHECK IF CB ALLOWS CREATION OF TOPICS
        if (!CB::checkCBsOption($configurations, 'CREATE-TOPIC')) {
            return false;
        }

        //CHECK IF CB IS CLOSED
        if(!empty($cb->end_date) && ($today > $cb->end_date)){
            return false;
        }

        //CHECK USER ACCESS
        if(ONE::isAuth()) {
            //CHECK IF USER ALREADY CREATED TOPICS
            if (CB::checkCBsOption($configurations, 'ONLY-ONE-TOPIC')) {
                $topics = CB::getAllUserTopics($cb->cb_key)->topics;
                if(count($topics)>0){
                    return false;
                }
            }
        }else{

            //CHECK CB ACCESS
            if(!CB::checkCBsOption($configurations, 'CREATE-TOPICS-ANONYMOUS')){

                //CHECK CB ACCESS - TOPIC FORM
                if(CB::checkCBsOption($configurations, 'ANONYMOUS-CREATE-TOPIC-ACCESS')){
                    return true;
                }
                return false;
            }

        }
        return true;

    }

    public static function checkActiveMenu($menu){
        if(isset(Route::getCurrentRoute()->getAction()['name'])){
            if(Route::getCurrentRoute()->getAction()['name'] == $menu)
                return true;
        }
        return false;
    }


    /**
     * @param $dateTime
     * @param null $format
     * @param null $tag
     * @return null|string
     */
    public function convertTimezone($dateTime, $format = null, $tag = null)
    {
        $date = null;
        $html = null;

        if(!isset($tag)){
            $tag = 'span';
        }
        if  (!is_null($dateTime)){
            try{
                $date = Carbon::parse($dateTime);
            } catch (Exception $e) {

                $html = '<'. $tag . ' class = "convertTimezone" >';
                $html .= $dateTime;
                $html .= '</'.$tag.'>';

                return $html;
            }
            $html = '<'. $tag . ' class="convertTimezone" data-timestamp='. Carbon::parse($dateTime)->timestamp . ' data-format='. $format.'>';
            $html .= $format ? $date->format($format) : $date;
            $html .= ' UTC';
            $html .= '</'.$tag.'>';
        }
        return $html;
    }

    public function getCbMenuTranslation($code, $cbKey, $defaultTranslation){
        $primaryLanguage = Session::get('LANG_CODE',"");
        $defaultLanguage = Session::get('LANG_CODE_DEFAULT',"");

        if(empty($primaryLanguage)){
            if(!empty($defaultLang)){
                $primaryLanguage = $defaultLang;
            }else{
                $defaultLang = ONE::getAppLanguageCode();
                $primaryLanguage = $defaultLang;
            }
        }

        if (!empty($primaryLanguage) || !empty($defaultTranslation)) {
            if(!Cache::has('menuCbTranslations_' . $cbKey)){
                $translationsFromComponent = CB::getCbMenuTranslations($cbKey);

                $translationsStructure = array();
                foreach ($translationsFromComponent as $translationCode => $translations) {
                    foreach ($translations as $languageCode => $translation) {
                        $translationsStructure[$translationCode][$languageCode] = $translation;
                    }
                }

                Cache::put('menuCbTranslations_' . $cbKey, $translationsStructure, 1440);
            }

            $translations = Cache::get('menuCbTranslations_' . $cbKey);
            if (!empty($translations[$code] ?? [])) {
                if (!empty($translations[$code][$primaryLanguage] ?? ""))
                    return $translations[$code][$primaryLanguage];
                else if (!empty($translations[$code][$defaultLanguage] ?? ""))
                    return $translations[$code][$defaultLanguage];
                elseif(!empty(array_values($translations[$code])[0]))
                    return array_values($translations[$code])[0];
            }
        }
        return $defaultTranslation;
    }

    public static function getEntityBEMenu() {
        $dynamicMenuRenderData = false;
        
        /* Get the User Personal Menu */
        if(ONE::verifyModuleAccess('cm','personal_dynamic_be_menu') && Session::has("BEMENU") && !empty(Session::get("BEMENU")))
            $dynamicMenuRenderData = Session::get("BEMENU");

        /* If the user doesn't have a personal menu, try to get the Entity Menu */
        if (is_string($dynamicMenuRenderData) && strcasecmp($dynamicMenuRenderData,"update")!=0 && strcasecmp($dynamicMenuRenderData,"none")==0) {
            $dynamicMenuRenderData = false;
            if (!$dynamicMenuRenderData && ONE::verifyModuleAccess('cm','dynamic_be_menu') && Cache::has("BEMENU-" . ONE::getEntityKey()) && Cache::get("BEMENU-" . ONE::getEntityKey())) {
                $dynamicMenuRenderData = Cache::get("BEMENU-" . ONE::getEntityKey());
                Session::put("BEMENU","none");
            }            
        }

        /* In case there isn't any menu yet, fetch it */
        if (!$dynamicMenuRenderData || (is_string($dynamicMenuRenderData) && strcasecmp($dynamicMenuRenderData,"update")==0))
            $dynamicMenuRenderData = \App\ComModules\CM::getEntityBEMenuRenderData();

        /* Save the menu in session (personal menu) or cache (entity menu) */
        if(is_object($dynamicMenuRenderData) && $dynamicMenuRenderData->user_key==One::getUserKey())
            Session::put("BEMENU",$dynamicMenuRenderData);
        elseif(is_object($dynamicMenuRenderData) && empty($dynamicMenuRenderData->user_key))
            Cache::put("BEMENU-" . ONE::getEntityKey(), $dynamicMenuRenderData,1440);

        return $dynamicMenuRenderData;
    }

    public static function entityHasBEMenu(){
        return !empty(One::getEntityBEMenu());
    }

    /**
     * @param $operationSchedules
     * @param $operationType
     * @param $operationAction
     * @return bool|null
     */
    public static function checkOperationSchedulePermission($operationSchedules, $operationType, $operationAction) {
        if (is_array($operationSchedules))
            $operationSchedules = json_decode(json_encode($operationSchedules));
            
        if (is_object($operationSchedules)) {
            if (isset($operationSchedules->{ $operationType }->{ $operationAction }))
                return $operationSchedules->{ $operationType }->{ $operationAction };
            else
                return false;
        } else
            return null;
    }

    public static function siteConfigurationExists($code) {
        if (strpos($code,"SITE-CONFIGURATION.")===false)
            $code = "SITE-CONFIGURATION." . $code;
        
        return !empty(Session::get($code));
    }
    public static function getSiteConfiguration($code, $default = null) {
        if (strpos($code,"SITE-CONFIGURATION.")===false)
            $code = "SITE-CONFIGURATION." . $code;
        
        $value = Session::get($code);

        return empty($value) ? $default : $value;
    }

    public static function getStatusTranslation($translations = [], $code  = ""){
        if(isset($translations->{$code}) && isset($translations->{$code}->during->{ONE::getAppLanguageCode()})){
            return $translations->{$code}->during->{ONE::getAppLanguageCode()};
        }

        return trans("demo.".$code);
    }


    public static function topicHasParameterWithCode($parameters, $code) {
        return !is_null(self::getTopicParameterByCode($parameters, $code, null));
    }

    public static function getTopicParameterValueByCode($parameters, $code, $default = null) {
        $returned = self::getTopicParameterByCode($parameters, $code, null);

        return !empty($returned->pivot->value??"") ? $returned->pivot->value : $default;
    }
    public static function getTopicParameterByCode($parameters, $code, $default = null) {
        $parameter = collect($parameters)->where("parameter_code","=",$code)->first();

        return !empty($parameter) ? $parameter : $default;
    }

    public static function transSite($code, $site_key = null, $language_code = null) {

        $site_key = !empty($site_key) ? $site_key :  Session::get('X-SITE-KEY');
        $language_code = !empty($language_code) ? $language_code : (Session::has('LANG_CODE') ? Session::get('LANG_CODE') :  Session::get('LANG_CODE_DEFAULT'));

        if(empty($site_key) || empty($language_code)) {
            return "ERROR transSite.".$language_code.".".$code;
        }

        $trans = ONE::transGetArray($code, $site_key, null, $language_code);

        \Log::debug("SITE TRANS: received -> ".json_encode($trans));

        if(empty($trans->$code))
            return "transSite.".$language_code.".".$code;

        // Get translation from code
        $value = $trans->$code;

        // Check translation: null => no translation in DB ; not null => translation
        if(empty($value)) {
            return "transSite.".$language_code.".".$code;
        } else {
            return $value->translation;
        }
    }

    public static function transCb($code,  $cb_key, $language_code = null) {

        $language_code = !empty($language_code) ? $language_code : (Session::has('LANG_CODE') ? Session::get('LANG_CODE') :  Session::get('LANG_CODE_DEFAULT'));
        if(empty($cb_key) || empty($language_code)) {
            return "ERROR transCb.".$language_code.".".$code;
        }

        $trans = ONE::transGetArray($code, null, $cb_key, $language_code);
        if(empty($trans->$code))
            return "transCb.".$language_code.".".$code;

        // Get translation from code
        $value = $trans->$code;

        // Check translation: null => no translation in DB ; not null => translation
        if(empty($value)) {
            return "transCb.".$language_code.".".$code;
        } else {
            return $value->translation;
        }
    }

    private static function transGetArray($code, $site_key, $cb_key, $language_code) {
        if(!empty($site_key)) {
            $redis_key = $site_key . "_" . $language_code;
            $cb_key = null;
        } else {
            $redis_key = $cb_key . "_" . $language_code;
            $site_key = null;
        }

        $trans = json_decode(Redis::get($redis_key));
        \Log::debug("SITE TRANS: get from REDIS -> ".json_encode($trans));

        if($trans == null) {
            \Log::debug("SITE TRANS: no translations in REDIS");

            // Get JSON translations (site/cb & language)
            $trans = CB::getTranslation($code,$language_code, $site_key, $cb_key);
 //           $trans = ["new_teste" => "New Teste"];

            \Log::debug("SITE TRANS: translations received from EMPATIA -> ".json_encode($trans));

            // Store translations (site/cb & language) in cache
            Redis::set($redis_key, json_encode($trans));
        }

        // If code does not exist in EMPATIA component
//        if(empty($trans) || !array_key_exists($code, $trans)) {
//            \Log::debug("SITE TRANS: no code '".$code."' in translations array. Creating translations in EMPATIA.");
////            EMPATIA::createTranslation($code, $language_code, $site_key, $cb_key);
//            CB::setTranslation(null, $code ,null, null, $cb_key, $site_key);
//            // Get JSON translations (site/cb & language)
//            $trans = CB::getTranslation($code,$language_code, $site_key, $cb_key);
//            // $trans = ["teste" => null,"new_teste" => "New Teste"];
//            \Log::debug("SITE TRANS: translations received from EMPATIA -> ".json_encode($trans));
//
//            // Store translations (site/cb & language) in cache
//            Redis::set($redis_key, json_encode($trans));
//        }

        return $trans;
    }

    public static function validateNifFormat($nif, $ignoreFirst=false) {
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


    public static function getCurrentTimeZone(){
        $timezone = Session::get('TIMEZONE', 'INVALID');
        return $timezone;
    }

}
