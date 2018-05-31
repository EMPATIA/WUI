<?php

namespace App\Http\Controllers;

use App\ComModules\Orchestrator;
use App\One\One;
use Exception;
use Illuminate\Support\Collection;
use App\Http\Requests\AuthMethodRequest;
use Datatables;
use Session;
use View;

class AuthMethodsController extends Controller
{
    public function __construct()
    {
        View::share('private.authMethods', trans('authMethods.authMethods'));

    }

    /**
     * Display a listing of the resource.
     *
     * @return View
     */
    public function index()
    {
        $title = trans('privateAuthMethods.auth_methods');
        return view('private.authMethods.index', compact('title'));
    }

    /**
     * Create a new resource.
     *
     * @return View
     */
    public function create()
    {
        return view('private.authMethods.authMethod');
    }

    /**
     *Store a newly created resource in storage.
     *
     * @param AuthMethodRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(AuthMethodRequest $request)
    {
        try {

            $authMethod = Orchestrator::setAuthMethod($request);
            Session::flash('message', trans('privateAuthMethod.store_ok'));
            return redirect()->action('AuthMethodsController@show', $authMethod->auth_method_key);

        }
        catch(Exception $e) {
            return redirect()->back()->withErrors([trans('privateAuthMethod.store_error') => $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     * @param $auth_method_key
     * @return \Illuminate\Http\RedirectResponse|View
     */
    public function show($auth_method_key)
    {
        try {
            $authMethod = Orchestrator::getAuthMethod($auth_method_key);
            return view('private.authMethods.authMethod', compact('authMethod'));
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors([trans('privateAuthMethod.show_error') => $e->getMessage()]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     * @param $auth_method_key
     * @return \Illuminate\Http\RedirectResponse|View
     */
    public function edit($auth_method_key)
    {
        try {
            $authMethod = Orchestrator::getAuthMethod($auth_method_key);
            return view('private.authMethods.authMethod', compact('authMethod'));
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors([trans('privateAuthMethod.edit_error') => $e->getMessage()]);
        }
    }

    /**
     * Update the specified resource in storage.
     * @param AuthMethodRequest $request
     * @param $auth_method_key
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(AuthMethodRequest $request, $auth_method_key)
    {
        try {

            $authMethod = Orchestrator::updateAuthMethod($request,$auth_method_key);
            Session::flash('message', trans('privateAuthMethod.update_ok'));
            return redirect()->action('AuthMethodsController@show', $authMethod->auth_method_key);
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors([trans('privateAuthMethod.update_error') => $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     * @param $auth_method_key
     * @return string
     */
    public function destroy($auth_method_key){

        try {

            Orchestrator::deleteAuthMethod($auth_method_key);
            Session::flash('message', trans('privateAuthMethod.delete_ok'));
            return action('AuthMethodsController@index');

        }
        catch(Exception $e) {
            return redirect()->back()->withErrors([trans('privateAuthMethod.delete_error') => $e->getMessage()])->getTargetUrl();
        }
    }

    /** Show modal delete confirmation
     * @param $auth_method_key
     * @return View
     */
    public function delete($auth_method_key){
        $data = array();

        $data['action'] = action("AuthMethodsController@destroy", $auth_method_key);
        $data['title'] = trans('privateAuthMethod.delete');
        $data['msg'] = trans('privateAuthMethod.are_you_sure_you_want_to_delete');
        $data['btn_ok'] = trans('privateAuthMethod.delete');
        $data['btn_ko'] = trans('privateAuthMethod.cancel');

        return view("_layouts.deleteModal", $data);
    }


    /** Display a listing of the resource.
     * @return mixed
     */
    public function tableAuthMethods()
    {

        $authMethodsList = Orchestrator::getAuthMethodsList();
        $authMethod = Collection::make($authMethodsList);

        return Datatables::of($authMethod)
            ->editColumn('name', function ($authMethod) {
                return "<a href='".action('AuthMethodsController@show', $authMethod->auth_method_key)."'>".$authMethod->name."</a>";
            })
            ->addColumn('action', function ($authMethod) {
                return ONE::actionButtons($authMethod->auth_method_key, ['form' => 'authMethods' ,'edit' => 'AuthMethodsController@edit', 'delete' => 'AuthMethodsController@delete']);
            })
            ->rawColumns(['name','action'])
            ->make(true);
    }
}
