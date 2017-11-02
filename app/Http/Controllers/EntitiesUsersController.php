<?php

namespace App\Http\Controllers;

use App\ComModules\Auth;
use App\ComModules\Orchestrator;
use App\Http\Requests\UserRequest;
use App\One\One;
use Illuminate\Support\Collection;
use App\Http\Requests\EntityRequest;
use Request;
use App\Http\Controllers\UsersController;
use App\User;
use Datatables;
use Session;
use View;
use Breadcrumbs;

class EntitiesUsersController extends Controller
{
    public function __construct()
    {
        //
    }

    /**
     * Display a listing of the resource.
     *
     * @return View
     */
    public function index($role = "user")
    {
        return view("private.entitiesUsers.index", compact($role));
    }

    /**
     * Create a new resource.
     *
     * @return View
     */
    public function create()
    {
        return view('private.entitiesUsers.user');
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param $userKey
     * @return View
     */
    public function edit($userKey)
    {
        $user = Auth::getUserByKey($userKey);
        return view('private.user.user', compact('user'));
    }



    /**
     * Display the specified resource.
     *
     * @param  $userKey
     * @return View
     */
    public function show($userKey)
    {
        try {
            $user = Auth::getUserByKey($userKey);
            return view('private.user.user')->with('user', $user);
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["private.user.show" => $e->getMessage()]);
        }
    }


    /**
     * Display the specified resource.
     *
     * @return View
     */
    public function showProfile()
    {
        try {
            $user = Auth::getUser();

            return view('private.user.user')->with('user', $user);
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["private.user.show" => $e->getMessage()]);
        }
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param UserRequest $requestUser
     * @return $this|View
     */
    public function store(UserRequest $requestUser)
    {

        try {
            $user = Auth::storeUserV2($requestUser->all());

            //* Send to Orchestrator the user type and key*/
            Orchestrator::storeUser($user->user_key, 1);

            Session::flash('message', trans('user.private.store_ok'));

            return view('private.user.user', compact('user'));
        }
        catch(Exception $e) {
            //TODO: save inputs
            return redirect()->back()->withErrors(["user.private.store" => $e->getMessage()]);
        }
    }



    /**
     * Update the specified resource in storage.
     *
     * @param UserRequest $requestUser
     * @param $userKey
     * @return $this|View
     */
    public function update(UserRequest $requestUser, $userKey)
    {

        try {
            $user = Auth::updateManager($requestUser, $userKey);

            Session::flash('message', trans('private.user.update_ok'));
            return view('private.user.user', compact('user'));
        }
        catch(Exception $e) {
            //TODO: save inputs
            return redirect()->back()->withErrors(["user.private.update" => $e->getMessage()]);
        }
    }



    /**
     * Remove the specified resource from storage.
     *
     * @param  $userKey
     * @return Response
     */
    public function destroy($userKey){

        try {

            Auth::deleteUser($userKey);

            return redirect()->action('UsersController@index');
        }
        catch(Exception $e) {
            //TODO: save inputs
            return redirect()->back()->withErrors(["user.private.destroy" => $e->getMessage()]);
        }
    }


    /**
     * List all resources from storage.
     *
     * @return mixed
     */
    public function tableUsers()
    {
        $response = Orchestrator::getAllUsers();

        $usersKey = [];
        foreach ($response as $item){
            $usersKey[] = $item->user_key;
        }

        $manage = Auth::listUser($usersKey);
        $collection = Collection::make($manage);

        // in case of json
        return Datatables::of($collection)
            ->addColumn('action', function ($user) {
                return ONE::actionButtons($user->user_key, ['show' => 'EntitiesUsersController@show', 'add' => 'EntitiesUsersController@edit']);
            })
            ->make(true);
    }
}
