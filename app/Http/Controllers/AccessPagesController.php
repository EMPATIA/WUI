<?php

namespace App\Http\Controllers;

use App\ComModules\Orchestrator;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\One\One;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Collection;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\UserRequest;
use App\Http\Requests\AccessPageRequest;
use Datatables;
use Session;
use View;
use Breadcrumbs;

class AccessPagesController extends Controller
{
    public function __construct()
    {
        View::share('private.accessPages', trans('accessPage.accessPage'));


    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        if(Session::get('user_role') != 'admin'){
            return redirect()->back()->withErrors(["private" => trans('privateEntitiesDivided.permission_message')]);
        }

        return view('private.accessPages.index');
    }

    /**
     * Create a new resource.
     *
     * @return Response
     */
    public function create()
    {
        if(Session::get('user_role') != 'admin'){
            return redirect()->back()->withErrors(["private" => trans('privateEntitiesDivided.permission_message')]);
        }

        $carbon = Carbon::now();
        $data = [];
        $entity_id = 1;
        try {

            $accessType = Orchestrator::listAccessType();

            $accessType_name = array();
            foreach($accessType as $at){
                $accessType_name[$at->id] = $at->name;
            }

            $data['accessType'] = $accessType_name;
            $data['entity_id'] = $entity_id;
            $data['carbon'] = $carbon;

            return view('private.accessPages.accessPage', $data);
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["private.accessPages.create" => $e->getMessage()]);
        }
    }

    /**
     *Store a newly created resource in storage.
     *
     * @param Request $request
     * @return $this|View
     */
    public function store(AccessPageRequest $request)
    {
        if(Session::get('user_role') != 'admin'){
            return redirect()->back()->withErrors(["private" => trans('privateEntitiesDivided.permission_message')]);
        }

        try {
            $accessPage = Orchestrator::storeAccessPage($request);
            Session::flash('message', trans('accessPage.store_ok'));
            return redirect()->action('AccessPagesController@show', $accessPage->id);
        }
        catch(Exception $e) {
            //TODO: save inputs
            return redirect()->back()->withErrors(["accessPage.store" => $e->getMessage()]);
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
            return redirect()->back()->withErrors(["private" => trans('privateEntitiesDivided.permission_message')]);
        }

        $data = [];
        try {
            $accessPage = Orchestrator::getAccessPage($id);

            $accessType = Orchestrator::listAccessType();

            /**
             * Since accessType only has 'list' method
             * this was the way to get the accesstype name
             * in the show function
             */
            $accessType_name = array();
            foreach($accessType as $at){
                if($at->id == $accessPage->access_type_id){
                    $accessType_name['name'] = $at->name;
                }
            }

            $data['accessPage'] = $accessPage;
            $data['accessType'] = $accessType_name;

            return view('private.accessPages.accessPage', $data);

        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["accessPages.show" => $e->getMessage()]);
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
            return redirect()->back()->withErrors(["private" => trans('privateEntitiesDivided.permission_message')]);
        }

        $carbon = Carbon::now();
        $data = [];
        try {

            $accessPage = Orchestrator::getAccessPage($id);

            $accessType = Orchestrator::listAccessType();

            $accessType_name = array();
            foreach($accessType as $at){
                $accessType_name[$at->id] = $at->name;
            }

            $data['accessPage'] = $accessPage;
            $data['accessType'] = $accessType_name;
            $data['carbon'] = $carbon;

            return view('private.accessPages.accessPage', $data);
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["accessPages.edit" => $e->getMessage()]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param $id
     * @return $this|View
     */
    public function update(AccessPageRequest $request, $id)
    {
        if(Session::get('user_role') != 'admin'){
            return redirect()->back()->withErrors(["private" => trans('privateEntitiesDivided.permission_message')]);
        }

        try {
            $accessPage = Orchestrator::updateAccessPage($request, $id);
            Session::flash('message', trans('accessPage.update_ok'));
            return redirect()->action('AccessPagesController@show', $accessPage->id);
        }
        catch(Exception $e) {
            //TODO: save inputs
            return redirect()->back()->withErrors(["accessPage.update" => $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  $id
     * @return Response
     */
    public function destroy($id)
    {
        if(Session::get('user_role') != 'admin'){
            return redirect()->back()->withErrors(["private" => trans('privateEntitiesDivided.permission_message')]);
        }

        try {
            Orchestrator::deleteAccessPage($id);
                Session::flash('message', trans('accessPage.delete_ok'));
                return action('AccessPagesController@index');
        }
        catch(Exception $e) {
            //TODO: save inputs
            return redirect()->back()->withErrors(["accessPage.destroy" => $e->getMessage()]);
        }
    }

    public function delete($id){
        $data = array();

        $data['action'] = action("AccessPagesController@destroy", $id);
        $data['title'] = "DELETE";
        $data['msg'] = "Are you sure you want to delete this Access Page?";
        $data['btn_ok'] = "Delete";
        $data['btn_ko'] = "Cancel";

        return view("_layouts.deleteModal", $data);
    }

    /**
     * Get the specified resource.
     *
     *
     */
    public function tableAccessPages()
    {
        $manage = Orchestrator::listAccessPage();

        // in case of json
        $accessPage = Collection::make($manage);

        return Datatables::of($accessPage)
            ->editColumn('name', function ($accessPage) {
                return "<a href='".action('AccessPagesController@show', $accessPage->id)."'>".$accessPage->name."</a>";
            })
            ->addColumn('action', function ($accessPage) {
                return ONE::actionButtons($accessPage->id, ['edit' => 'AccessPagesController@edit', 'delete' => 'AccessPagesController@delete']);
            })
            ->rawColumns(['name','action'])
            ->make(true);
    }
}