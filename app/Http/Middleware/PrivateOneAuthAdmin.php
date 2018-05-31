<?php
namespace App\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Auth\Guard;
use Session;
use ONE;
use Illuminate\Support\Facades\URL;


class PrivateOneAuthAdmin
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
        return $next($request);
    }
}