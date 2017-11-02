<?php

namespace App\Http\Controllers;

use App\ComModules\CB;
use App\ComModules\CM;
use App\ComModules\Orchestrator;
use App\ComModules\Auth;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\One\One;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Collection;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\UserRequest;
use App\Http\Requests\CountryRequest;
use Datatables;
use Session;
use View;
use Breadcrumbs;

class DashboardController extends Controller
{
    public function __construct()
    {
        View::share('title', trans('dashboard.title'));

    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function ideas()
    {
        $cbs = collect(Orchestrator::getAllCbs())->toArray();

        $cbs = collect($cbs)->filter(function($cb){
            return in_array($cb->cb_type->code,['idea']);
        })->toArray();

        $cbs_topics = CB::getCBsByKeys($cbs);

        $counter_topics = 0;
        foreach($cbs_topics as $topics){
            $counter_topics += $topics->statistics->topics;
        }

        return $counter_topics;
    }

    public function comments()
    {
        $cbs = collect(Orchestrator::getAllCbs())->toArray();

        $cbs = collect($cbs)->filter(function ($cb) {
            return in_array($cb->cb_type->code, ['idea']);
        })->toArray();

        $cbs_topics = CB::getCBsByKeys($cbs);

        $counter_posts = 0;
        foreach ($cbs_topics as $topics) {
            $counter_posts += $topics->statistics->posts;
        }

        return $counter_posts;
    }

    /**
     * Di
     *
     * @return Response
     */
    public function ideasCSV()
    {
        return view('private.dashboard.ideasCSV');
    }

    public function getRegisteredUsers(Request $request){
        $users = count(Orchestrator::getAllUsers());

        return $users;
    }

    public function getLoggedInUsers(Request $request){
        $users = Orchestrator::getAllUsers();

        $users = collect($users)->pluck('user_key');

        $users_timeout = Auth::listUser($users);
        $logged_in = [];
        foreach($users_timeout as $timeout){
            if(isset($timeout->timeout) and $timeout->timeout >= time()){
                $logged_in[] = $timeout->timeout;
            }
        }

        return count($logged_in);
    }

}
