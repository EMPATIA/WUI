<?php

namespace App\Http\Controllers;

use App\ComModules\Orchestrator;
use App\ComModules\Vote;
use Exception;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\One\One;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Collection;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\UserRequest;
use App\Http\Requests\VoteMethodRequest;
use Datatables;
use Session;
use View;
use Breadcrumbs;

class VoteMethodsController extends Controller
{
    public function __construct()
    {
        View::share('title', trans('voteMethod.voteMethod'));


    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $sidebar = 'votes';
        $active = 'methods';

        Session::put('sidebarArguments', ['activeFirstMenu' => 'methods']);
        return view('private.voteMethods.index', compact('sidebar', 'active'));
    }

    /**
     * Create a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $languages = Orchestrator::getAllLanguages();
        $voteMethodsGroupList = Vote::getListMethodGroups();
        foreach($voteMethodsGroupList as $methodGroup){
            $voteMethodsGroup[$methodGroup->id] = $methodGroup->name;
        }

        $sidebar = 'votes';
        $active = 'methods';

        Session::put('sidebarArguments', ['activeFirstMenu' => 'methods']);

        return view('private.voteMethods.voteMethod', compact('voteMethodsGroup','languages', 'sidebar', 'active'));
    }

    /**
     *Store a newly created resource in storage.
     *
     * @param VoteMethodRequest $request
     * @return $this|View
     */
    public function store(VoteMethodRequest $request)
    {
        try {
            $methodGroupId = $request->method_group_id;
            $code = $request->code;
            $languages = Orchestrator::getAllLanguages();
            $voteMethodTranslation = [];
            foreach($languages as $language){

                if($request->input("name_".$language->code)) {
                    $voteMethodTranslation[] = [
                        'language_code' => $language->code,
                        'name' => $request->input("name_" . $language->code),
                        'description' => $request->input("description_" . $language->code),
                    ];
                }
            }

            $voteMethod = Vote::SetVoteMethod($methodGroupId,$voteMethodTranslation,$code);
            Session::flash('message', trans('voteMethod.store_ok'));
            return redirect()->action('VoteMethodsController@show', $voteMethod->id);

        }
        catch(Exception $e) {
            //TODO: save inputs
            return redirect()->back()->withErrors(["voteMethod.store" => $e->getMessage()]);
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
        try {

            $voteMethod = Vote::getVoteMethodWithConfigurations($id);
            $voteMethodId = $voteMethod->id;

            $sidebar = 'votesMethods';
            $active = 'details';

            Session::put('sidebarArguments.voteMethodId', $voteMethodId);
            Session::put('sidebarArguments.activeSecondMenu', 'details');

            return view('private.voteMethods.voteMethod', compact('voteMethod', 'voteMethodId', 'sidebar', 'active'));
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["voteMethod.show" => $e->getMessage()]);
        }
    }

    public function showConfigurations($id){
        try {

            $voteMethod = Vote::getVoteMethodWithConfigurations($id);
            $voteMethodId = $voteMethod->id;

            $sidebar = 'votesMethods';
            $active = 'config';

            Session::put('sidebarArguments.voteMethodId', $voteMethodId);
            Session::put('sidebarArguments.activeSecondMenu', 'config');

            return view('private.voteMethods.configurations', compact('voteMethod', 'voteMethodId', 'sidebar', 'active'));
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["voteMethod.show" => $e->getMessage()]);
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
        try {
            $voteMethod = Vote::getVoteMethodEdit($id);
            $voteMethodId = $voteMethod->id;
            foreach ($voteMethod->translations as $translation){
                $methodTranslation[$translation->language_code] = ['name' => $translation->name,'description' => $translation->description];
            }
            $languages = Orchestrator::getAllLanguages();
            $methodGroupList = Vote::getListMethodGroups();
            foreach($methodGroupList as $methodGroup){
                $voteMethodsGroup[$methodGroup->id] = $methodGroup->name;
            }

            $sidebar = 'votesMethods';
            $active = 'details';

            Session::put('sidebarArguments.voteMethodId', $voteMethodId);
            Session::put('sidebarArguments.activeSecondMenu', 'details');

            return view('private.voteMethods.voteMethod', compact('voteMethod', 'voteMethodId', 'methodTranslation','voteMethodsGroup', 'languages', 'sidebar', 'active'));
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["voteMethod.edit" => $e->getMessage()]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param VoteMethodRequest $request
     * @param $id
     * @return $this|View
     */
    public function update(VoteMethodRequest $request, $id)
    {
        try {

            $methodGroupId = $request->method_group_id;
            $code = $request->code;
            $languages = Orchestrator::getAllLanguages();
            $voteMethodTranslation = [];
            foreach($languages as $language){
                if($request->input("name_".$language->code)) {
                    $voteMethodTranslation[] = [
                        'language_code' => $language->code,
                        'name' => $request->input("name_" . $language->code),
                        'description' => $request->input("description_" . $language->code),
                    ];
                }
            }

            $voteMethod = Vote::UpdateVoteMethod($id,$methodGroupId,$voteMethodTranslation,$code);


            Session::flash('message', trans('voteMethod.update_ok'));
            return redirect()->action('VoteMethodsController@show', $voteMethod->id);


        }
        catch(Exception $e) {
            //TODO: save inputs
            return redirect()->back()->withErrors(["voteMethod.update" => $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  $id
     * @return Response
     */
    public function destroy($id){

        try {
            Vote::deleteVoteMethod($id);

                Session::flash('message', trans('voteMethod.delete_ok'));
                return action('VoteMethodsController@index');

        }
        catch(Exception $e) {
            //TODO: save inputs
            return redirect()->back()->withErrors(["voteMethod.destroy" => $e->getMessage()]);
        }
    }

    public function delete($id){
        $data = array();

        $data['action'] = action("VoteMethodsController@destroy", $id);
        $data['title'] = "DELETE";
        $data['msg'] = "Are you sure you want to delete this Vote Method?";
        $data['btn_ok'] = "Delete";
        $data['btn_ko'] = "Cancel";

        return view("_layouts.deleteModal", $data);
    }

    /**
     * Get the specified resource.
     *
     *
     */
    public function tableVoteMethods()
    {
        // Votemethod list
        

        // Entities
//        $votemethods = [];
        $voteMethodList = Vote::getListMethods();

        $voteMethod = Collection::make($voteMethodList);

        return Datatables::of($voteMethod)
            ->editColumn('title', function ($voteMethod) {
                return "<a href='".action('VoteMethodsController@show', $voteMethod->id)."'>".$voteMethod->name."</a>";
            })
            ->addColumn('action', function ($voteMethod) {
                return ONE::actionButtons($voteMethod->id, ['form'=> 'methods','edit' => 'VoteMethodsController@edit', 'delete' => 'VoteMethodsController@delete']);
            })
            ->rawColumns(['title','action'])
            ->make(true);

    }
}
