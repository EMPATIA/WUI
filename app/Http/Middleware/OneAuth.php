<?php
/**
 * Created by PhpStorm.
 * User: Vitor Fonseca
 * Date: 08/10/2015
 * Time: 15:34
 */

namespace App\Http\Middleware;

use App;
use App\ComModules\Auth;
use App\ComModules\Orchestrator;
use Closure;
use Cookie;
use Exception;
use Illuminate\Auth\Guard;
use Illuminate\Support\Facades\URL;
use Request;
use Session;
use ONE;

class OneAuth
{
    /**
     * Create a new filter instance.
     *
     * @internal param Guard $auth
     */
    public function __construct()
    {
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $siteKey = Session::get('X-SITE-KEY');
        $entityKey = Session::get('X-ENTITY-KEY');
        //$currentLang = Session::get('LANG_CODE');
        $defaultLang = Session::get('LANG_CODE_DEFAULT');
        $middleware = Session::get('X-ACCESS-AREA');
        $layout = Session::get('X-LAYOUT-KEY');
        $siteConfiguration = Session::get('SITE-CONFIGURATION');
        //$languages = Session::get('languages');
        
        if($request->is("auth/login")){
            ONE::clearSession();
            
            Session::put('X-SITE-KEY', $siteKey);
            Session::put('X-ENTITY-KEY', $entityKey);
            //Session::put('LANG_CODE', $currentLang);
            Session::put('LANG_CODE_DEFAULT',$defaultLang);
            Session::put('X-ACCESS-AREA', $middleware);
            Session::put('X-LAYOUT-KEY', $layout);
            Session::put('SITE-CONFIGURATION', $siteConfiguration);
            //Session::put('languages', $languages);
        }
        
        if((empty($layout) || empty($entityKey) || empty($siteKey)) || $middleware != 'public') {

            if (isset($_SERVER["HTTP_HOST"])){
                $url = $_SERVER["HTTP_HOST"];
            }else{
                $url = '';
            }
            
            try {
                $response = Orchestrator::getSiteEntity($url);
            } catch(Exception $e) {
                return response()->view("errors.invalidSite");
            }
            
            $layout = !empty($response->layout)? $response->layout : null;

            Session::put('X-LAYOUT-KEY' , $layout);
            Session::put('X-SITE-KEY' , $response->site_key);
            Session::put('X-ENTITY-KEY' , $response->entity_id);

            if(!is_null($response->site_ethics)){
                Session::put('site_ethics' , $response->site_ethics);
            }

            if(!is_null($response->timezone)){
                Session::put('TIMEZONE' , $response->timezone->name);
            }

        } elseif($siteKey == 'false') {
            return response()->json(['error' => 'Unauthorized'], 401)->send();
        }

        if((empty($currentLang) || empty($defaultLang)) ) {
            // Get browser language
            if(isset ($_SERVER['HTTP_ACCEPT_LANGUAGE']) ){
                $browserLang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
            }

            ONE::clearLangSession();

            $allLang = ONE::getAllLanguages();
            $userDefaultLang = null;
            $browserDefaultLang = null;
            $defaultLang = null;
            
            foreach ($allLang as $lang) {
                // Set COOKIE language as default
                if (!empty($request->cookie('EMPATIA-LANG')) && $request->cookie('EMPATIA-LANG') === $lang->code) {
                    $userDefaultLang = $lang->code;
                }
                
                // Validate browser language
                if (isset($browserLang) && $browserLang === $lang->code) {
                    $browserDefaultLang = $lang->code;
                }
                
                // Get default entity language
                if (isset($lang->default) && $lang->default) {
                    $defaultLang = $lang->code;
                }
            }
            
            if (isset($userDefaultLang)) {
                Session::put('LANG_CODE' , $userDefaultLang);
            } else if (isset($browserDefaultLang)) {
                Session::put('LANG_CODE' , $browserDefaultLang);
            } else {
                Session::put('LANG_CODE' , $defaultLang);
            }
            Session::put('LANG_CODE_DEFAULT' , $defaultLang);
        }

        app()->setLocale(Session::get('LANG_CODE'));

        Cookie::queue('EMPATIA-LANG', Session::get('LANG_CODE'), 21600);

        Session::put('X-ACCESS-AREA', 'public');

        /* Validate TOKEN */
        if (Session::has('X-AUTH-TOKEN')) {
            if (!ONE::checkIfValidToken()) {
                ONE::clearSession();
            } else if (!Session::has('user') || !Session::has('user_level')) {
                // Get user details
                $userInformation = Auth::getUser();
                Session::put('user', $userInformation);
                
                // DEPRECATED USER LEVEL
//                $userLevel = Orchestrator::getUserLevel($userInformation->user_key);
//                $userInformation->user_level = $userLevel->position ?? 0;
//                Session::put('user_level', $userLevel);
            }
        }

        if(!Session::has('SITE-CONFIGURATION')) {
            $siteConfGroups = Orchestrator::getSiteConfGroups(Session::get('X-SITE-KEY'), true);

            $sessionSiteConfigurations = [];
            foreach ($siteConfGroups as $key => $siteConfGroup) {
                if (str_contains($key, 'file_')) {

                    if (isset($siteConfGroup)) {

                        if (!empty($element = json_decode($siteConfGroup))) {
                            if (count($element) > 1) {
                                foreach ($element as $subElement) {
                                    $sessionSiteConfigurations[$key][] = action('FilesController@download', [$subElement->id, $subElement->code, 1]);
                                }
                            } else
                                $sessionSiteConfigurations[$key] = action('FilesController@download', [$element[0]->id, $element[0]->code, 1]);
                        }
                    }
                } else {
                    $sessionSiteConfigurations[$key] = $siteConfGroup ?? null;
                }

            }

            Session::put('SITE-CONFIGURATION',$sessionSiteConfigurations);
        }
        
        return $next($request);
    }
}
