<?php

namespace App\Http\Controllers;

use App\ComModules\Auth;
use App\ComModules\Orchestrator;
use Datatables;
use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Collection;
use ONE;
use Session;

class EntityGroupsController extends Controller
{
    /**
     * EntityGroupsController constructor.
     */
    public function __construct()
    {

    }

    /**
     * Returns view with entity groups list
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        try {

            //Page title
            $groupTypeKey = $request->get('groupTypeKey');
            $title = trans('privateEntityGroups.list_entity_groups');

            return view('private.entityGroups.index', compact('title', 'groupTypeKey'));
        }catch(Exception $e){
            return redirect()->back();
        }
    }

    /**
     *
     * Returns view with entity group users
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showUsers(Request $request, $entityGroupKey){

        //entity group key to set and send on sidebar view
        $title = trans('privateEntityGroups.list_all_users');
        $sidebar = 'entityGroupDetails';
        $active = 'entity_group_users';
        $groupTypeKey = Session::get('sidebarArguments')['groupTypeKey'];

        Session::put('sidebarArguments', ['entityGroupKey' => $entityGroupKey, 'groupTypeKey' => $groupTypeKey, 'activeFirstMenu' => 'entity_group_users']);
        return view("private.entityGroups.users", compact('title','entityGroupKey', 'sidebar', 'active', 'groupTypeKey'));
    }


    /**
     * Returns data to datatable with entity group users list
     *
     * @param Request $request
     * @return mixed
     */
    public function tableGroupUsers(Request $request)
    {

        if (Session::get('user_role') == 'admin' || ONE::verifyUserPermissionsShow('wui', 'entity_groups_users')) {

            $data = Orchestrator::getUsersByEntityGroupKey($request->entityGroupKey);

            $userStatus = [];
            $usersKey = [];
            foreach ($data as $item) {
                $userStatus[$item->user_key] = !empty($item->pivot->status) ? $item->pivot->status : null;
                $usersKey[] = $item->user_key;
            }

            $manage = Auth::listUser($usersKey);
            $data = collect($data);

            foreach ($manage as $item) {

                $user = $data->whereIn('user_key', [$item->user_key])->first();
                $item->entityGroup = !empty($user->entityGroup) ? $user->entityGroup : false;
            }

            $collection = Collection::make($manage);
            $entityGroupKey = $request->entityGroupKey;
        }
        else{
            $collection = Collection::make([]);
            $entityGroupKey = $request->entityGroupKey;
        }

        $delete = Session::get('user_role') == 'admin' || ONE::verifyUserPermissionsDelete('wui', 'entity_groups_users');

        // in case of json
        return Datatables::of($collection)
            ->editColumn('name', function ($user){
                return "<a href='".action('UsersController@show', ['userKey' => $user->user_key])."'>".$user->name."</a>";
            })
            ->addColumn('action', function ($user) use($entityGroupKey, $delete){
                if($delete)
                    return "<a href='".action('EntityGroupsController@removeUser', ['entityGroupKey' => $entityGroupKey, 'userKey' => $user->user_key])."' class=\"btn btn-flat btn-danger btn-xs\" ><i class=\"fa fa-minus\"></i></a>" ;
                else
                    return null;
            })
            ->make(true);
    }


    /**
     *
     * Returns data to datatable in view modal with entity users list
     *
     * @param Request $request
     * @return mixed
     */
    public function tableEntityUsers(Request $request)
    {
        //users keys array - entity users not in given group
//        $usersKeys = collect(Orchestrator::getAllManagers())->pluck('user_key');
        $usersKeys = collect(Orchestrator::getUsers(null))->pluck('user_key');

        //users keys array - users already in given group
        $groupUsersKeys = collect(Orchestrator::getUsersByEntityGroupKey($request->entityGroupKey))->pluck('user_key');

        //results an array with the list of users not yet added to given group
        $usersKeys = $usersKeys->diff($groupUsersKeys);

        $manage = Auth::listUser($usersKeys);
        $collection = Collection::make($manage);
        $entityGroupKey = $request->entityGroupKey;

        // in case of json
        return Datatables::of($collection)
            ->editColumn('name', function ($user){
                return "<a href='".action('UsersController@show', ['userKey' => $user->user_key])."'>".$user->name."</a>";
            })
            ->addColumn('action', function ($user) use($entityGroupKey){
                return "<a href='".action('EntityGroupsController@addUser', ['entityGroupKey' => $entityGroupKey, 'userKey' => $user->user_key])."' class=\"btn btn-flat btn-warning btn-xs user\" ><i class=\"fa fa-plus\"></i></a>" ;

            })
            ->make(true);
    }

    /**
     *
     * Adds user to pivot table entity_group_user
     *
     * @param $entityGroupKey
     * @param $userKey
     * @return $this|string
     */
    public function addUser ($entityGroupKey, $userKey){

        try {
            Orchestrator::addEntityGroupUser($entityGroupKey, $userKey);

            Session::flash('message', trans('privateEntityGroups.user_add_ok'));
            return action('EntityGroupsController@tableEntityUsers');
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors([ trans('privateEntityGroups.user_add_nok') => $e->getMessage()]);
        }
    }

    /**
     * Removes user from pivot table entity_group_user
     *
     * @param $entityGroupKey
     * @param $userKey
     * @return $this
     */
    public function removeUser ($entityGroupKey, $userKey){

        try {
            Orchestrator::removeEntityGroupUser($entityGroupKey, $userKey);

            Session::flash('message', trans('privateEntityGroups.user_remove_ok'));
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors([ trans('privateEntityGroups.user_add_nok') => $e->getMessage()]);
        }
    }

    /**
     * Returns a Datatable collection with all Entity Groups
     *
     * @return $this
     */
    public function tableEntityGroups(Request $request)
    {

        try {
            //Get all entity groups
            $entityGroups = Orchestrator::getEntityGroupsByGroupTypeKey($request->get('groupTypeKey'));
            $groupTypeKey = $request->get('groupTypeKey');

            // in case of json
            $entityGroups = Collection::make($entityGroups);

            //  Datatable with Group Types list
            return Datatables::of($entityGroups)
                ->editColumn('name', function ($entityGroup){
                    return "<a href='" . action('EntityGroupsController@show', $entityGroup->entity_group_key) . "'>" . $entityGroup->name . "</a>";
                })
                ->addColumn('action', function ($entityGroup) use ($groupTypeKey) {
                    return ONE::actionButtons(['entityGroupKey' => $entityGroup->entity_group_key, 'groupTypeKey' => $groupTypeKey], ['edit' => 'EntityGroupsController@edit', 'delete' => 'EntityGroupsController@delete', 'form' => 'entityGroups']);
                })
                ->make(true);
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["entityGroups.tableEntityGroups" => $e->getMessage()]);
        }
    }

    /**
     * Shows Group details from a given group Key
     *
     * @param Request $request
     * @param $entityGroupKey
     * @return $this|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(Request $request, $entityGroupKey)
    {
        try {

            $entityGroup = Orchestrator::getEntityGroupByKey($entityGroupKey);

            // Form title (layout)
            $title = trans('privateEntityGroups.show_entity_group').' '.(isset($entityGroup->name) ? $entityGroup->name: null);




            // Return the view with data
            $data = [];
            $data['title'] = $title;
            // Advanced Search var's
            $data['showManagers'] = 0;
            $data['showUsers'] = 0;

            $data['groupTypeKey'] = $entityGroup->group_type->group_type_key;
            $data['entityGroup'] = $entityGroup;
            $data['entityGroupKey'] = $entityGroup->entity_group_key;
            $data['sidebar'] = 'entityGroupDetails';
            $data['active'] = 'entity_group_details';

            Session::put('sidebarArguments', ['entityGroupKey' => $data['entityGroupKey'], 'groupTypeKey' => $data['groupTypeKey'], 'activeFirstMenu' => 'entity_group_details']);

            return view('private.entityGroups.entityGroup', $data);
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["entityGroup.show" => $e->getMessage()]);
        }
    }

    /**
     * Returns view/form for new group creation
     * Sends to view a possible group parents list
     *
     * @param Request $request
     * @return $this|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create(Request $request)
    {
        try {
            // Form title (layout)
            $title = trans('privateEntityGroups.create_entity_group');

            //Get all entities
            $entityGroupsTree = Orchestrator::getEntityGroupsByGroupTypeKey($request->get('groupTypeKey'));

            //Construct/order array to populate Entities Group Parent Select (html element)
            $tree = $this->buildTree($entityGroupsTree);

            // Return the view with data
            $data = [];
            $data['title'] = $title;
            $data['tree'] = $tree;
            $data['groupTypeKey'] = $request->get('groupTypeKey');

            return view('private.entityGroups.entityGroup', $data);
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["entityGroup.create" => $e->getMessage()]);
        }
    }

    /**
     * Stores a new Entity Group
     *
     * @param Request $request
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        try {

            $newEntityGroup = $request->all();

            //Call to Com Module set method
            Orchestrator::setEntityGroup($newEntityGroup['name'],$newEntityGroup['designation'],$newEntityGroup['groupTypeKey'],$newEntityGroup['parentEntityGroupKey'] );

            // Message to show + redirect To
            Session::flash('message', trans('privateEntityGroups.store_ok'));
            return redirect()->action('EntityGroupsController@showGroups', ["groupTypeKey" => $newEntityGroup['groupTypeKey']]);

        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["entityGroup.store" => $e->getMessage()]);
        }
    }

    /**
     *
     * Called on Group Update - loads view with current Group Info
     *
     * @param $entityGroupKey
     * @return $this|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($entityGroupKey){
        try {

            //get object
            $entityGroup = Orchestrator::getEntityGroupByKey($entityGroupKey);

            //set Form title (layout)
            $title = trans('privateEntityGroups.edit_entity_group').' '.(isset($entityGroup->name) ? $entityGroup->name: null);

            /* --- --- --- */
            //set values to populate Entities Group Parent Select

            //Get all entities
            $entityGroupsTree = Orchestrator::getEntityGroupsByGroupTypeKey($entityGroup->group_type->group_type_key);


            //Construct/order array
            $tree = $this->buildTreeEdit($entityGroup->id, $entityGroupsTree);

            /* --- --- --- */


            $data = [];
            $data['title'] = $title;
            $data['tree'] = $tree;
            $data['groupTypeKey'] = $entityGroup->group_type->group_type_key;
            $data['entityGroup'] = $entityGroup;
            $data['entityGroupKey'] = $entityGroupKey;

            $data['sidebar'] = 'entityGroupDetails';
            $data['active'] = 'entity_group_details';

            Session::put('sidebarArguments', ['entityGroupKey' => $entityGroupKey, 'groupTypeKey' => $data['groupTypeKey'], 'activeFirstMenu' => 'entity_group_details']);


            //set parent value if exists
            if(! is_null($entityGroup->entity_group)){

                $data['parentEntityGroupKey'] = $entityGroup->entity_group->entity_group_key;
            }

            // Return the view with data
            return view('private.entityGroups.entityGroup', $data);

        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["entityGroup.edit" => $e->getMessage()]);
        }
    }

    /**
     *
     * Group Update
     *
     * @param Request $request
     * @param $groupTypeKey
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $groupTypeKey){

        try {

            $newEntityGroup = $request->all();
            //Call to Com Module update method
            Orchestrator::updateEntityGroup($newEntityGroup['name'],$newEntityGroup['designation'],$newEntityGroup['groupTypeKey'],$newEntityGroup['parentEntityGroupKey'], $groupTypeKey);

            // Message to show + redirect To
            Session::flash('message', trans('privateEntityGroups.update_ok'));
            //return redirect()->action('EntityGroupsController@index');
            return redirect()->action('EntityGroupsController@showGroups', ["groupTypeKey" => $newEntityGroup['groupTypeKey']]);

        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["entityGroup.edit" => $e->getMessage()]);
        }

    }

    /**
     * Called on delete action, shows delete modal info
     *
     * @param $entityGroupKey
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function delete($entityGroupKey)
    {

        $data = array();

        $data['action'] = action("EntityGroupsController@destroy", $entityGroupKey);
        $data['title'] =  trans('privateEntityGroups.delete');
        $data['msg'] = trans('privateEntityGroups.are_you_sure_you_want_to_delete_this_entity_group') . "?" . trans('privateEntityGroups.this_action_will_remove_any_dependent_groups');
        $data['btn_ok'] = trans('privateEntityGroups.delete');
        $data['btn_ko'] = trans('privateEntityGroups.cancel');

        return view("_layouts.deleteModal", $data);
    }

    /**
     *
     * Deletes Group by given Key
     *
     * @param $entityGroupKey
     * @return $this|string
     */
    public function destroy($entityGroupKey)
    {
        //get entity group before destruction, for redirection
        $entityGroup = Orchestrator::getEntityGroupByKey($entityGroupKey);

        try {
            //Call to Com Module delete method
            Orchestrator::deleteEntityGroup($entityGroupKey);

            // Message to show + redirect To
            Session::flash('message', trans('privateEntityGroups.delete_ok'));
            return action('EntityGroupsController@showGroups', ["groupTypeKey" => $entityGroup->group_type->group_type_key]);
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors([ trans('privateEntityGroups.delete_nok') => $e->getMessage()]);
        }
    }



    /**
     * Recursive function that order an array (plain) according to parent/children hierarchy
     *
     * @param $ar
     * @param null $pid
     * @param array $op
     * @param int $position
     * @return array
     */
    private function buildTree($ar, $pid = null, &$op = [], $position = 0) {

        foreach( $ar as $item ) {
            if( $item->parent_group_id == $pid ) {

                $op [] = ['position' => $position ,'item' => $item];
                //up a level
                $position +=1;
                $this->buildTree( $ar, $item->id, $op,$position);
                //down a level
                $position -=1;
            }
        }
        return $op;
    }

    /**
     * Recursive function that order an array (plain) according to parent/children hierarchy - Excludes current editing Group and its children
     * (A group can't be its own father, or has a children as its father)
     *
     * @param $id
     * @param $ar
     * @param null $pid
     * @param array $op
     * @param int $position
     * @param bool $check
     * @return array
     */
    private function buildTreeEdit($id, $ar, $pid = null, &$op = [], $position = 0, $check = false) {


        foreach( $ar as $item ) {
            if( $item->parent_group_id == $pid ) {

                //prevents inclusion of self Group and its children
                $item->id == $id ? $check = true : false;

                if(!$check)
                    $op [] = ['position' => $position ,'item' => $item];    //saves current item and its position (identation level to be sent to the view and used on select options)

                //up a level
                $position +=1;
                $this->buildTreeEdit($id, $ar, $item->id, $op,$position, $check);

                //down a level
                $position -=1;

                //resets "check" flag previously activated
                $position == 0 ? $check = false : true;
            }
        }

       return $op;
    }

    /**
     * Returns the view for draggable groups organization - tree view
     *
     * @return $this|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showGroups(Request $request){
        try{


            //Get all entity groups
            $entityGroups = Orchestrator::getEntityGroupsByGroupTypeKey($request->get('groupTypeKey'));


            // in case of json
            $entityGroups = Collection::make($entityGroups);


            // Form title (layout)
            $title = trans('privateEntityGroups.show_entity_group_tree');

            // Return the view with data
            $data = [];
            $data['title'] = $title;
            $data['entityGroups'] = $entityGroups;
            $data['groupTypeKey'] = $request->get('groupTypeKey');


            return view('private.entityGroups.indexTree', $data);
        }catch(Exception $e) {
            return redirect()->back()->withErrors(["accessMenu.show" => $e->getMessage()]);
        }

    }


    /**
     * Receives current (moved) group, it's parent and new order from tree
     * Updates Order ant Hierarchy
     *
     * @param Request $request
     */
    public function updateOrder(Request $request)
    {
        $source = $request->source;  // dragged group id
        $destination = $request->destination;  // parent group id
        $ordering = json_decode($request->order);  // new order
        $rootOrdering = json_decode($request->rootOrder);  //new order - if dragged to root/no parent


        Orchestrator::updateEntityGroupsOrder($source, $destination, $ordering, $rootOrdering);


    }


    /**
     * @param Request $request
     * @param $entityGroupKey
     * @return $this|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showPermissions(Request $request, $entityGroupKey)
    {
        try{
            $entityModulesList = Orchestrator::getActiveEntityModules(Session::get('X-ENTITY-KEY'));

            $entityGroupPermissionsList = Orchestrator::getPermissionsList(['entityGroupKey' => $entityGroupKey]);

            $permissions=[];
            foreach ($entityGroupPermissionsList as $entityGroupPermissions){

                if (isset($entityGroupPermissions->module->module_key,$entityGroupPermissions->module_type->module_type_key))
                    $permissions[$entityGroupPermissions->module->module_key][$entityGroupPermissions->module_type->module_type_key] = $entityGroupPermissions;
            }

            $data = [];

            $data['groupTypeKey'] = $request->groupTypeKey;
            $data['entityGroupKey'] = $entityGroupKey;
            $data['modules'] = $entityModulesList;
            $data['permissions'] = $permissions;

            $data['sidebar'] = 'entityGroupDetails';
            $data['active'] = 'entity_group_permissions';

            Session::put('sidebarArguments', ['entityGroupKey' => $data['entityGroupKey'], 'groupTypeKey' => $data['groupTypeKey'], 'activeFirstMenu' => 'entity_group_permissions']);

            return view('private.entityGroups.permissions', $data);
        }catch(Exception $e) {
            return redirect()->back()->withErrors(["privateEntityPermission.show" => $e->getMessage()]);
        }

    }

    /**
     *
     * @param Request $request
     * @param $entityGroupKey
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function storePermissions(Request $request, $entityGroupKey)
    {
        try{
            $entityModulesList = Orchestrator::getActiveEntityModules(Session::get('X-ENTITY-KEY'));
            $data = [];

            foreach ($entityModulesList as $moduleKey => $entityModules){
                foreach ($entityModules->types as $moduleTypeKey => $moduleType){
                    $temp['module_key'] = $moduleKey;
                    $temp['module_type_key'] = $moduleTypeKey;
                    $temp['permission_show'] = isset($request->modules_types[$moduleKey][$moduleTypeKey]['show']) ? true : false;
                    $temp['permission_create'] = isset($request->modules_types[$moduleKey][$moduleTypeKey]['create']) ? true : false;
                    $temp['permission_update'] = isset($request->modules_types[$moduleKey][$moduleTypeKey]['update']) ? true : false;
                    $temp['permission_delete'] = isset($request->modules_types[$moduleKey][$moduleTypeKey]['delete']) ? true : false;
                    $data[]= $temp;
                }

            }

            $dataSend['entity_group_key'] = $entityGroupKey;
            $dataSend['entity_permissions'] = $data;

            Orchestrator::setPermissions($dataSend);
            return redirect()->action('EntityGroupsController@showPermissions', ["groupTypeKey" => $entityGroupKey]);

        } catch(Exception $e) {
            return redirect()->back()->withErrors(["privateEntityPermission.add" => $e->getMessage()]);
        }



    }

}
