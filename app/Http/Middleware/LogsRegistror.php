<?php

namespace App\Http\Middleware;

use App\Jobs\Logger;
use Closure;
use Illuminate\Support\Facades\Redis;
use Session;

class LogsRegistror
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {

        $proxy_ips = explode(',', env('PROXY_IPS'));
        $proxy_ips = !empty($proxy_ips[0]) ? $proxy_ips : null;

        $ip = $request->getClientIp();

        if (!is_null($proxy_ips) && in_array($ip, $proxy_ips) && filter_var($_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP)){
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }

        $isLogged = $request->session()->has('user');
        $userKey = null;
        if ($isLogged) {
            $userKey = $request->session()->get('user')->user_key;
        }

        $siteKey = $request->session()->get('X-SITE-KEY');
        $entityKey = $request->session()->get('X-ENTITY-KEY');

        Redis::setEx("logged:" . $request->session()->getId(), 300, json_encode(['phpsessionid' => $request->session()->getId(), 'timestamp' => microtime(true), 'ip' => $ip, 'user_key' => $userKey, 'entity_key' => $entityKey, 'site_key' => $siteKey]));

        Redis::setEx("pageAccess:" . $request->session()->getId() . microtime(true), 300, json_encode(['phpsessionid' => $request->session()->getId(), 'timestamp' => microtime(true), 'ip' => $ip, 'user_key' => $userKey, 'entity_key' => $entityKey, 'site_key' => $siteKey]));

        return $next($request);
    }

    /**
     * @param $request
     * @param $response
     */
    public function terminate($request, $response){
        if(env('LOGS_FLAG', 'false')=='true') {

            $proxy_ips = explode(',', env('PROXY_IPS'));
            $proxy_ips = !empty($proxy_ips[0]) ? $proxy_ips : null ;

            $isLogged = $request->session()->has('user');
            $authToken = null;
            $userKey = null;
            if ($isLogged) {
                $authToken = $request->session()->get('X-AUTH-TOKEN');
                $userKey = $request->session()->get('user')->user_key;
            }
            $ip = $request->getClientIp();

            if (!is_null($proxy_ips) && in_array($ip, $proxy_ips) && filter_var($_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP)){
                $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            }

            $url = $request->fullUrl();
            $siteKey = $request->session()->get('X-SITE-KEY');
            $method = $request->method(); //type of request (get, post....)
            $sessionId = $request->session()->getId();
            $tableKey = $sessionId . "-" . $ip . "-" . $url;
            $time_end = microtime(true);

            dispatch(new Logger("saveTrackingDataToDB",[
                "is_logged" => $isLogged,
                "auth_token" => $authToken,
                "user_key" => $userKey,
                "ip" => $ip,
                "url" => $url,
                "site_key" => $siteKey,
                "method" => $method,
                "session_id" => $sessionId,
                "table_key" => $tableKey,
                "time_start" => LARAVEL_START,
                "time_end" => $time_end
            ]));
        }
    }

}
