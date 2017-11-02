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
        $currentLang = Session::get('LANG_CODE');
        $defaultLang = Session::get('LANG_CODE_DEFAULT');
        $middleware = Session::get('X-ACCESS-AREA');
        $layout = Session::get('X-LAYOUT-KEY');


        if((empty($layout) || empty($entityKey) || empty($siteKey)) || $middleware != 'public') {

            if (isset($_SERVER["HTTP_HOST"])){
//                $parameters = [
//                    "url" => $_SERVER["HTTP_HOST"] ,
//                ];
                $url = $_SERVER["HTTP_HOST"];
            }else{
//                $parameters = [
//                    "url" => "" ,
//                ];
                $url = '';
            }
            $response = Orchestrator::getSiteEntity($url);

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

        }elseif($siteKey == 'false'){
            return response()->json(['error' => 'Unauthorized'], 401)->send();
        }

        if((empty($currentLang) || empty($defaultLang)) ) {

            if(isset ($_SERVER['HTTP_ACCEPT_LANGUAGE']) ){
                $userLang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
            }
            else
                $userLang = 'en';


            ONE::clearLangSession();

            $allLang = ONE::getAllLanguages();
            foreach($allLang as $lang){
                if (!empty($request->cookie('EMPATIA-LANG'))){
                    if($request->cookie('EMPATIA-LANG') === $lang->code){
                        $userDefaultLang = $lang->code;
                    }
                }else{
                    if($userLang === $lang->code){
                        $userDefaultLang = $lang->code;
                    }
                }
                if($lang->default){
                    $defaultLang = $lang->code;
                }
            }

            if(isset($userDefaultLang)){
                Session::put('LANG_CODE' , $userDefaultLang);
            }
            else{
                Session::put('LANG_CODE' , $defaultLang);
            }
            Session::put('LANG_CODE_DEFAULT' , $defaultLang);
        }

        app()->setLocale(Session::get('LANG_CODE'));

        if (!empty($request->cookie('EMPATIA-LANG'))){
            Cookie::q('EMPATIA-LANG', $currentLang, 4500);
        }

        Session::put('X-ACCESS-AREA', 'public');

        /* Validate TOKEN */
        if (Session::has('X-AUTH-TOKEN')) {
            if(!ONE::checkIfValidToken()){
                ONE::clearSession();
            }else{
                $userInformation = Auth::getUser();
                $userLevel = Orchestrator::getUserLevel($userInformation->user_key);
                $userInformation->user_level = $userLevel->position ?? 0;
                Session::put('user', $userInformation);
                Session::put('user_level', $userLevel);
                /*$userKey = isset(Session::get('user')->user_key) ?? null;
                if (!empty($userKey)){
                   Session::put('user_level', Orchestrator::getUserLevel($userKey));
                }else{
                    Session::put('user_level',0);
                }*/

            }
        }
        $siteConfGroups = Orchestrator::getSiteConfGroups(Session::get('X-SITE-KEY'),true);


        $sessionSiteConfigurations = [];
        foreach($siteConfGroups as $key => $siteConfGroup){
            if (str_contains($key, 'file_')){

                if(isset($siteConfGroup)) {

                    if (!empty($element = json_decode($siteConfGroup))) {

                        $sessionSiteConfigurations[$key] = action('FilesController@download', [$element[0]->id, $element[0]->code, 1]);
                    }
                }
            }else{
                $sessionSiteConfigurations[$key] = $siteConfGroup ?? null;
            }

        }

        Session::set('SITE-CONFIGURATION',$sessionSiteConfigurations);

        return $next($request);

    }
}
