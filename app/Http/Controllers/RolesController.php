<?php

namespace App\Http\Controllers;

use App\ComModules\Orchestrator;
use App\Http\Requests\RoleRequest;
use App\One\One;
use App\Http\Requests;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Datatables;
use Session;
use View;
use Breadcrumbs;

class RolesController extends Controller
{
    public function __construct()
    {

        View::share('title', trans('roles.title'));



    }


    public function index()
    {
        $title = trans('privateRoles.list_roles');
        return view('private.roles.index', compact('title'));
    }



    public function create()
    {
        $title = trans('privateRoles.create_role');
        return view('private.roles.role', compact('title'));
    }


    public function store(RoleRequest $request)
    {
        try {

            $role = Orchestrator::setNewRole($request);
            Session::flash('message', trans('roles.store_ok'));
            return redirect()->action('RolesController@show', $role->role_key);

        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["roles.store" => $e->getMessage()]);
        }
    }

    public function showPermissions($roleKey)
    {
        try {
            $role = Orchestrator::getRole($roleKey);

            $data = $role->permissions;

            $permissions = [];
            foreach($data as $permission){
                $permissions[$permission->code."-create"] = $permission->create;
                $permissions[$permission->code."-view"] = $permission->view;
                $permissions[$permission->code."-update"] = $permission->update;
                $permissions[$permission->code."-delete"] = $permission->delete;
            }

            $title = trans('privateRoles.permissions').' '.(isset($role->name) ? $role->name: null);

            $sidebar = 'functions';
            $active = 'permissions';

            Session::put('sidebarArguments', ['roleKey' => $roleKey, 'activeFirstMenu' => 'permissions']);

            return view('private.roles.permissions', compact('title', 'role', 'permissions', 'sidebar', 'active', 'roleKey'));
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["roles.show" => $e->getMessage()]);
        }
    }


    public function show($roleKey)
    {
        try {
            $role = Orchestrator::getRole($roleKey);

            $title = trans('privateRoles.show_role').' '.(isset($role->name) ? $role->name: null);

            $sidebar = 'functions';
            $active = 'role';

            Session::put('sidebarArguments', ['roleKey' => $roleKey, 'activeFirstMenu' => 'permissions']);

            return view('private.roles.role', compact('title', 'role', 'sidebar', 'active', 'roleKey'));
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["roles.show" => $e->getMessage()]);
        }
    }


    public function edit($roleKey)
    {
        try {

            $role = Orchestrator::getRole($roleKey);
            $title = trans('privateRoles.update_role').' '.(isset($role->name) ? $role->name: null);

            $sidebar = 'functions';
            $active = 'role';

            Session::put('sidebarArguments', ['roleKey' => $roleKey, 'activeFirstMenu' => 'permissions']);

            return view('private.roles.role', compact('title', 'role', 'sidebar', 'active', 'roleKey'));

        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["roles.edit" => $e->getMessage()]);
        }
    }



    public function update(RoleRequest $request, $roleKey)
    {

        try {

            $role = Orchestrator::updateRole($request,$roleKey);
            Session::flash('message', trans('roles.update_ok'));
            return redirect()->action('RolesController@show', $role->role_key);

        }
        catch(Exception $e) {
            //TODO: save inputs
            return redirect()->back()->withErrors(["roles.update" => $e->getMessage()]);
        }
    }


    public function destroy($roleKey){

        try {

            Orchestrator::deleteRole($roleKey);
            Session::flash('message', trans('roles.delete_ok'));
            return action('RolesController@index');


        }
        catch(Exception $e) {
            //TODO: save inputs
            return redirect()->back()->withErrors(["roles.destroy" => $e->getMessage()]);
        }
    }



    public function getIndexTable()
    {
        $roles = Orchestrator::getRolesList();
        // in case of json
        $collection = Collection::make($roles);

        $edit = true;
        $delete = true;

        return Datatables::of($collection)
            ->editColumn('name', function ($collection) {
                return "<a href='".action('RolesController@show', $collection->role_key)."'>".$collection->name."</a>";
            })
            ->addColumn('action', function ($collection) use($edit, $delete) {
                if($edit == true and $delete == true)
                    return ONE::actionButtons($collection->role_key, ['form' => 'roles', 'edit' => 'RolesController@edit', 'delete' => 'RolesController@delete']);
                elseif($edit == false and $delete == true)
                    return ONE::actionButtons($collection->role_key, ['form' => 'roles', 'delete' => 'RolesController@delete']);
                elseif($edit == true and $delete == false)
                    return ONE::actionButtons($collection->role_key, ['form' => 'roles', 'edit' => 'RolesController@edit']);
                else
                    return null;
            })
            ->rawColumns(['name','action'])
            ->make(true);
    }


    public function getPermissionsTable()
    {

        $roles = Orchestrator::getRolesList();
        // in case of json
        $collection = Collection::make($roles);

        return Datatables::of($collection)
            ->editColumn('name', function ($collection) {
                return "<a href='".action('RolesController@show', $collection->role_key)."'>".$collection->name."</a>";
            })
            ->addColumn('action', function ($collection) {
                return ONE::actionButtons($collection->role_key, ['edit' => 'RolesController@edit', 'delete' => 'RolesController@delete']);
            })
            ->rawColumns(['name','action'])
            ->make(true);
    }



    public function delete($roleKey){
        $data = array();

        $data['action'] = action("RolesController@destroy", $roleKey);
        $data['title'] = "DELETE";
        $data['msg'] = "Are you sure you want to delete this Role?";
        $data['btn_ok'] = "Delete";
        $data['btn_ko'] = "Cancel";

        return view("_layouts.deleteModal", $data);
    }


    /**
     * @param Request $request
     */
    public function setPermissionRole(Request $request)
    {
        $roleKey = $request->role_key;
        $code = $request->code;
        $module = $request->module;
        $api = $request->api;
        $option = $request->option;
        $value = $request->value;

        Orchestrator::storePermissions($roleKey, $code, $module, $api, $option, $value);

    }

    /*This method is called to change the sidebar in the page*/
    public function getSidebar1(Request $request, $roleKey)
    {
        $active = $request->url;
        return view('private.sidebar.functions', compact('roleKey', 'active'));
    }
}
