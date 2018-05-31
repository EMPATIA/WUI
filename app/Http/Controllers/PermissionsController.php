<?php

namespace App\Http\Controllers;
use App\ComModules\LogsRequest;
use Illuminate\Http\Request;
use App\ComModules\EMPATIA;
use App\Http\Controllers\Controller;
use Illuminate\Support\Collection;
use Datatables;
use Session;
use App\ComModules\Orchestrator;

class PermissionsController extends Controller
{
    /**  MENUS*/
    /**
     * Display a listing of the menus and users permissions.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexUsers(Request $request)
    {
        $entityKey = Orchestrator::getSiteEntity($_SERVER["HTTP_HOST"])->entity_id;
        $permissions = EMPATIA::getPermissions($request->userKey,$entityKey);
        $title =  trans('privateUsers.permissions') ;
        $userName = $permissions->userName;

        $data = [];
        $data['title'] = $title;
        $data['userKey'] = $request->userKey;
        $data['sidebar'] = 'manager';
        $data['active'] = 'menuPermissions';
        $data['permissions'] = $permissions;
        $data['userName'] = $userName;

        Session::put('sidebarArguments', ['userKey' => $request->userKey, 'role' => 'manager', 'activeFirstMenu' => 'menuPermissions']);

        return view('private.user.menusPermission', $data);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $code, $userId
     * @return \Illuminate\Http\Response
     */
    public function updateUserPermission(Request $request)
    {
        $entityKey = Orchestrator::getSiteEntity($_SERVER["HTTP_HOST"])->entity_id;

        if($request->permission){
            EMPATIA::updatePermissions($request->code,$request->userId,0,$entityKey);
        }
        else{
            EMPATIA::updatePermissions($request->code,$request->userId,1,$entityKey);
        }
    }


    /**  ENTITY GROUPS */

    /**
     * Display a listing of entities groups permissions.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexGroups(Request $request)
    {
        $entityKey = Orchestrator::getSiteEntity($_SERVER["HTTP_HOST"])->entity_id;
        $permissions = EMPATIA::getGroupsPermissions($request->entityGroupKey,$entityKey);
        $title =  trans('privateEntityGroup.permissions');
        $groupName = $permissions->groupName;

        $data = [];
        $data['title'] = $title;
        $data['entityGroupKey'] = $request->entityGroupKey;
        $data['userKey'] = $request->userKey;
        $data['sidebar'] = 'entityGroupDetails';
        $data['active'] = 'groupPermissions';
        $data['permissions'] = $permissions;
        $data['groupName'] = $groupName;

        Session::put('sidebarArguments', ['entityGroupKey' => $request->entityGroupKey, 'groupTypeKey' => Session::get('sidebarArguments')['groupTypeKey'], 'activeFirstMenu' => 'groupPermissions']);

        return view('private.entityGroups.groupsPermission', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $code, $groupId
     * @return \Illuminate\Http\Response
     */
    public function updateGroupPermission(Request $request)
    {
        $entityKey = Orchestrator::getSiteEntity($_SERVER["HTTP_HOST"])->entity_id;

        if($request->permission){
            EMPATIA::updateGroupPermission($request->code,$request->groupId,0,$entityKey);
        }
        else{
            EMPATIA::updateGroupPermission($request->code,$request->groupId,1,$entityKey);
        }
    }


}
