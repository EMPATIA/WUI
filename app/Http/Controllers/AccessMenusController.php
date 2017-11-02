<?php

namespace App\Http\Controllers;

use App\ComModules\CM;
use App\ComModules\Orchestrator;
use App\Http\Requests\AccessMenuRequest;
use App\One\One;
use Datatables;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Session;
use View;

class AccessMenusController extends Controller
{
    public function __construct()
    {
        View::share('private.accessMenus', trans('accessMenu.accessMenu'));

    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {

        $title = trans('privateAccessMenus.list_accessMenus');
        return view('private.accessMenus.index', compact('title'));
    }

    /**
     * Create a new resource.
     *
     * @return Response
     */
    public function create()
    {
        if(Session::get('user_role') != 'admin'){
            if(!ONE::verifyUserPermissionsCreate('cm', 'menu')) {
                return redirect()->back()->withErrors(["private" => trans('privateEntitiesDivided.permission_message')]);
            }
        }

        $data = [];
        $entity_id = 1;
        try {
            $sitesList = Orchestrator::getSiteList();
            $sites = [];
            foreach($sitesList as $site){
                $sites[$site->key] = $site->link;
            }

            $accessType = Orchestrator::listAccessType();
            $accessTypeName = array();
            foreach($accessType as $at){
                $accessTypes[$at->id] = $at->name;
            }

            $data['accessTypes'] = $accessTypes;
            $data['entity_id'] = $entity_id;
            $data['sites'] = $sites;
            $data['title'] = trans('privateAccessMenus.create_accessMenus');
            return view('private.accessMenus.accessMenu', $data);

            return redirect()->back()->withErrors(["private.accessMenus.create" => $response->json()->error]);
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["private.accessMenus.create" => $e->getMessage()]);
        }

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param AccessMenuRequest $request
     * @return $this|View
     */
    public function store(AccessMenuRequest $request)
    {
        if(Session::get('user_role') != 'admin'){
            if(!ONE::verifyUserPermissionsCreate('cm', 'menu')) {
                return redirect()->back()->withErrors(["private" => trans('privateEntitiesDivided.permission_message')]);
            }
        }
        try {
            $accessMenu = Orchestrator::storeAccessMenu($request);
            Session::flash('message', trans('accessMenu.store_ok'));
            return redirect()->action('AccessMenusController@show', $accessMenu->id);
        }
        catch(Exception $e) {
            //TODO: save inputs
            return redirect()->back()->withErrors(["accessMenu.store" => $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        if(Session::get('user_role') != 'admin'){
            if(!ONE::verifyUserPermissionsShow('cm', 'menu')) {
                return redirect()->back()->withErrors(["private" => trans('privateEntitiesDivided.permission_message')]);
            }
        }

        $data = [];
        try {

            $accessMenu = Orchestrator::getAccessMenu($id);

            $sitesList = Orchestrator::getSiteList();
            $sites = [];
            foreach($sitesList as $site){
                $sites[$site->key] = $site->link;
            }

            $data['accessM'] = $id;
            $data['menu'] = CM::listMenus($id);

            $data['accessMenu'] = $accessMenu;
            $data['sites'] = $sites;
            $data['siteKey'] = $site->key;
            $data['title'] = trans('privateAccessMenus.show_group').' '.(isset($accessMenu->name) ? $accessMenu->name : null);
            $data['sidebar'] = 'menu';
            $data['active'] = 'accessMenu';

            Session::put('sidebarArguments', ['accessM' => $id, 'activeFirstMenu' => 'accessMenu']);

            return view('private.accessMenus.accessMenu', $data);
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["accessMenu.show" => $e->getMessage()]);
        }
    }

    public function showMenus($id){
        try{

            $accessMenu = Orchestrator::getAccessMenu($id);

            $data['accessM'] = $id;
            $data['menu'] = CM::listMenus($id);
            $data['accessMenu'] = $accessMenu;
            $data['sidebar'] = 'menu';
            $data['active'] = 'indexTree';

            Session::put('sidebarArguments', ['accessM' => $id, 'activeFirstMenu' => 'indexTree']);
            return view('private.accessMenus.indexTree', $data);
        }catch(Exception $e) {
            return redirect()->back()->withErrors(["accessMenu.show" => $e->getMessage()]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param $id
     * @return View
     */
    public function edit($id)
    {
        if(Session::get('user_role') != 'admin'){
            if(!ONE::verifyUserPermissionsUpdate('cm', 'menu')) {
                return redirect()->back()->withErrors(["private" => trans('privateEntitiesDivided.permission_message')]);
            }
        }

        $data = [];
        try {
            $accessMenu = Orchestrator::getAccessMenu($id);

            $sitesList = Orchestrator::getSiteList();

            $sites = [];
            foreach($sitesList as $site){
                $sites[$site->key] = $site->link;
            }


            $data['accessM'] = $id;
            $data['menu'] = CM::listMenus($id);
            $data['accessMenu'] = $accessMenu;
            $data['sites'] = $sites;
            $data['title'] = trans('privateAccessMenus.update_group');

            return view('private.accessMenus.accessMenu', $data);
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["accessMenu.edit" => $e->getMessage()]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param AccessMenuRequest $request
     * @param $id
     * @return $this|View
     */
    public function update(AccessMenuRequest $request, $id)
    {
        if(Session::get('user_role') != 'admin'){
            if(!ONE::verifyUserPermissionsUpdate('cm', 'menu')) {
                return redirect()->back()->withErrors(["private" => trans('privateEntitiesDivided.permission_message')]);
            }
        }

        try {
            $accessMenu = Orchestrator::updateAccessMenu($request, $id);
            Session::flash('message', trans('accessMenu.update_ok'));
            return redirect()->action('AccessMenusController@show', $accessMenu->id);
        }
        catch(Exception $e) {
            //TODO: save inputs
            return redirect()->back()->withErrors(["accessMenu.update" => $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return Response
     */
    public function destroy($id)
    {
        if(Session::get('user_role') != 'admin'){
            if(!ONE::verifyUserPermissionsDelete('cm', 'menu')) {
                return redirect()->back()->withErrors(["private" => trans('privateEntitiesDivided.permission_message')]);
            }
        }

        try {
            Orchestrator::deleteAccessMenu($id);
            Session::flash('message', trans('accessMenu.delete_ok'));
            return action('AccessMenusController@index');
        }
        catch(Exception $e) {
            //TODO: save inputs
            return redirect()->back()->withErrors(["accessMenu.destroy" => $e->getMessage()]);
        }
    }

    /**
     * Opens a modal to enable the Delete Confirmation Dialog.
     *
     * @param  int $id
     * @return view
     */
    public function delete($id){
        if(Session::get('user_role') != 'admin'){
            if(!ONE::verifyUserPermissionsDelete('cm', 'menu')) {
                return redirect()->back()->withErrors(["private" => trans('privateEntitiesDivided.permission_message')]);
            }
        }

        $data = array();
        $data['action'] = action("AccessMenusController@destroy", $id);
        $data['title'] = trans('privateAccessMenus.delete');
        $data['msg'] = trans('privateAccessMenus.are_you_sure_you_want_to_delete').' ?';
        $data['btn_ok'] = trans('privateAccessMenus.delete');
        $data['btn_ko'] = trans('privateAccessMenus.cancel');

        return view("_layouts.deleteModal", $data);
    }

    /**
     * Opens modal to confirm will to activate the specified resource.
     *
     * @param  int $id
     * @return view
     */
    public function activateConfirm($id){
        $data = array();

        $data['action'] = action("AccessMenusController@activate", $id);
        $data['title'] = "ACTIVATE";
        $data['msg'] = "Are you sure you want to activate this Access Menu for this site?";
        $data['btn_ok'] = "Activate";
        $data['btn_ko'] = "Cancel";

        return view("_layouts.activateModal", $data);
    }

    /**
     * Activate the specified resource from storage.
     *
     * @param  int $id
     * @return Response
     */
    public function activate($id)
    {
        try {
            Orchestrator::activateAccessMenu($id);
            Session::flash('message', trans('accessMenu.activate_ok'));
            return action('AccessMenusController@index');
        }
        catch(Exception $e) {
            //TODO: save inputs
            return redirect()->back()->withErrors(["accessMenu.activate_failed" => $e->getMessage()]);
        }
    }


    /**
     * Get the specified resource.
     *
     * @return Datatable of Collection made
     */
    public function tableAccessMenus(Request $request)
    {
        if(Session::get('user_role') == 'admin' || ONE::verifyUserPermissionsShow('cm', 'menu')){

            $manage = Orchestrator::listAccessMenu();

            // in case of json
            if($request->active=="1"){
                //  dd($manage->data);
                $accessMenu = Collection::make($manage)->where('active', 1);

            }else if($request->active=="2"){
                $accessMenu = Collection::make($manage)->where('active', 0);

            }else
                $accessMenu = Collection::make($manage);
        }else
            $accessMenu = Collection::make([]);

        $edit = Session::get('user_role') == 'admin' || ONE::verifyUserPermissionsUpdate('cm', 'menu');
        $delete = Session::get('user_role') == 'admin' || ONE::verifyUserPermissionsDelete('cm', 'menu');

        return Datatables::of($accessMenu)
            ->editColumn('name', function ($accessMenu) {
                return "<a href='".action('AccessMenusController@show', $accessMenu->id)."'>".$accessMenu->name."</a>";
            })
            ->addColumn('activeAction', function ($accessMenu) {
                return ($accessMenu->active == 0) ? ONE::actionButtons($accessMenu->id,['activate' => 'AccessMenusController@activateConfirm']) : "";
            })
            ->editColumn('siteLink', function ($accessMenu) {
                return !empty($accessMenu->site->link) ? $accessMenu->site->link : "";
            })
            ->editColumn('active', function ($accessMenu) {
                return ($accessMenu->active == 1) ? trans("Yes") : trans("No");
            })
            ->addColumn('action', function ($accessMenu) use($edit, $delete) {
                if($edit == true and $delete == true)
                    return ONE::actionButtons($accessMenu->id, ['form' => 'accessMenus', 'edit' => 'AccessMenusController@edit', 'delete' => 'AccessMenusController@delete']);
                elseif($edit == false and $delete == true)
                    return ONE::actionButtons($accessMenu->id, ['form' => 'accessMenus', 'delete' => 'AccessMenusController@delete']);
                elseif($edit == true and $delete == false)
                    return ONE::actionButtons($accessMenu->id, ['form' => 'accessMenus', 'edit' => 'AccessMenusController@edit']);
                else
                    return null;
            })
            ->make(true);
    }
}
