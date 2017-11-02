<?php

namespace App\Http\Controllers;

use App\ComModules\Orchestrator;
use App\Http\Requests\HomePageTypeRequest;
use App\One\One;
use Datatables;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Session;
use View;

class HomePageTypesController extends Controller
{
    public function __construct()
    {
        View::share('title', trans('privateHomePageTypes.title'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return View
     */
    public function index()
    {
        $title = trans('privateHomePageTypes.list_homePageTypes');
        return view('private.homePageTypes.index', compact('title'));
    }

    /**
     * Show the form for creating a new resource.
     * Only creates type Group
     * @return View
     */
    public function create()
    {
        if(Session::get('user_role') != 'admin'){
            if(!ONE::verifyUserPermissionsCreate('cm', 'home_page_type')){
                return redirect()->back()->withErrors(["homePageType.create" => trans('privateHomePagetype.permission_message')]);
            }
        }

        $types = [
            'group'         => trans('privateHomePageTypes.group'),
            'text'          => trans('privateHomePageTypes.text'),
            'text_area'     => trans('privateHomePageTypes.textArea'),
            'link'          => trans('privateHomePageTypes.link'),
            'internal_link' => trans('privateHomePageTypes.internalLink'),
            'image'         => trans('privateHomePageTypes.image')
        ];
        $parentsResponse = Orchestrator::getHomePageTypeParents();
        $parents = [];
        foreach ($parentsResponse as $parent){
            $parents[$parent->home_page_type_key] = $parent->name;
        }
        $title = trans('privateHomePageTypes.create_homePageType');

        return view('private.homePageTypes.homePageType',compact('title', 'types','parents'));
    }

    /**
     * Show the form for creating a new Group Type.
     *
     * @return View
     */
    public function createGroupType($homePageTypeKey)
    {

        $types = [
            //'group'         => trans('privateHomePageTypes.group'),
            'text'          => trans('privateHomePageTypes.text'),
            'text_area'     => trans('privateHomePageTypes.textArea'),
            'link'          => trans('privateHomePageTypes.link'),
            'internal_link' => trans('privateHomePageTypes.internalLink'),
            'image'         => trans('privateHomePageTypes.image')
        ];
        $parentsResponse = Orchestrator::getHomePageTypeParents();
        $parents = [];
        foreach ($parentsResponse as $parent){

            $parents[$parent->home_page_type_key] = $parent->name;

        }
        $title = trans('privateHomePageTypes.create_homePageType');
        return view('private.homePageTypes.homePageGroupType',compact('title', 'types','parents','homePageTypeKey'));
    }
    /**
     * Store a newly created resource in storage.
     * @param HomePageTypeRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(HomePageTypeRequest $request)
    {
        if(Session::get('user_role') != 'admin'){
            if(!ONE::verifyUserPermissionsCreate('cm', 'home_page_type')) {
                return redirect()->back()->withErrors(["homePageType.store" => trans('privateHomePagetype.permission_message')]);
            }
        }

        try {
            $homePageType = Orchestrator::setNewHomePageType($request);
            Session::flash('message', trans('privateHomePageTypes.storeOk'));

            return redirect()->action('HomePageTypesController@show', $homePageType->home_page_type_key);
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors([ trans('privateHomePageTypes.storeNok') => $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $homePageTypeKey
     * @return \Illuminate\Http\RedirectResponse|View
     */
    public function show($homePageTypeKey)
    {
        if(Session::get('user_role') != 'admin'){
            if(!ONE::verifyUserPermissionsShow('cm', 'home_page_type')) {
                return redirect()->back()->withErrors(["homePageType.show" => trans('privateHomePagetype.permission_message')]);
            }
        }
        
        try {
            $homePageType = Orchestrator::getHomePageType($homePageTypeKey);

            $title = trans('privateHomePageTypes.show_homePageType').' '.(isset($homePageType->name) ? $homePageType->name: null);

            $sidebar = 'cmHomePagesType';
            $active = 'details';

            Session::put('sidebarArguments', ['homePageTypeKey' => $homePageTypeKey, 'activeFirstMenu' => 'details']);

            return view('private.homePageTypes.homePageType', compact('title', 'homePageType', 'homePageTypeKey', 'sidebar', 'active'));
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors([ trans('privateHomePageTypes.show') => $e->getMessage()]);
        }
    }

    public function showHomePageTypesChildren($homePageTypeKey)
    {
        try {
            $homePageType = Orchestrator::getHomePageType($homePageTypeKey);

            $title = trans('privateHomePageTypes.list_home_page_type_children').' '.(isset($homePageType->name) ? $homePageType->name: null);

            $sidebar = 'cmHomePagesType';
            $active = 'children';

            Session::put('sidebarArguments', ['$homePageTypeKey' => $homePageTypeKey, 'activeFirstMenu' => 'children']);
            return view('private.homePageTypes.homePageTypeChildren', compact('title', 'homePageType', 'homePageTypeKey', 'sidebar', 'active'));
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors([ trans('privateHomePageTypes.show') => $e->getMessage()]);
        }
    }

    public function showHomePageGroupType($homePageTypeKey, $homePageGroupKey)
    {
        try {
            $homePageType = Orchestrator::getHomePageType($homePageGroupKey);

            $title = trans('privateHomePageTypes.list_home_page_type_children').' '.(isset($homePageType->name) ? $homePageType->name: null);

            $sidebar = 'cmHomePagesType';
            $active = 'children';

            Session::put('sidebarArguments', ['homePageTypeKey' => $homePageTypeKey, 'activeFirstMenu' => 'children']);

            return view('private.homePageTypes.homePageGroupType', compact('title', 'homePageType', 'homePageTypeKey', 'sidebar', 'active'));
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors([ trans('privateHomePageTypes.show') => $e->getMessage()]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param $homePageTypeKey
     * @return \Illuminate\Http\RedirectResponse|View
     */
    public function edit($homePageTypeKey)
    {
        if(Session::get('user_role') != 'admin'){
            if(!ONE::verifyUserPermissionsUpdate('cm', 'home_page_type')) {
                return redirect()->back()->withErrors(["homePageType.update" => trans('privateHomePagetype.permission_message')]);
            }
        }

        try {
            $types = [
                'text'      => trans('privateHomePageTypes.text'),
                'text_area' => trans('privateHomePageTypes.textArea'),
                'link'      => trans('privateHomePageTypes.link'),
                'image'     => trans('privateHomePageTypes.image')
            ];
            $parentsResponse = Orchestrator::getHomePageTypeParents($homePageTypeKey);
            $parents = [];
            foreach ($parentsResponse as $parent){
                $parents[$parent->home_page_type_key] = $parent->name;
            }
            $homePageType = Orchestrator::getHomePageType($homePageTypeKey);
            $title = trans('privateHomePageTypes.update_homePageType').' '.(isset($homePageType->name) ? $homePageType->name: null);

            $sidebar = 'cmHomePagesType';
            $active = 'details';

            Session::put('sidebarArguments', ['homePageTypeKey' => $homePageTypeKey, 'activeFirstMenu' => 'details']);

            return view('private.homePageTypes.homePageType', compact('title', 'homePageType', 'homePageTypeKey', 'types','parents', 'sidebar', 'active'));
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors([ trans('privateHomePageTypes.edit') => $e->getMessage()]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param HomePageTypeRequest $request
     * @param $homePageTypeKey
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(HomePageTypeRequest $request, $homePageTypeKey)
    {
        if(Session::get('user_role') != 'admin'){
            if(!ONE::verifyUserPermissionsUpdate('cm', 'home_page_type')) {
                return redirect()->back()->withErrors(["homePageType.update" => trans('privateHomePagetype.permission_message')]);
            }
        }
        
        try {
            $homePageType = Orchestrator::updateHomePageType($request,$homePageTypeKey);
            Session::flash('message', trans('privateHomePageTypes.updateOk'));
            return redirect()->action('HomePageTypesController@show', $homePageTypeKey);

        }
        catch(Exception $e) {
            //TODO: save inputs
            return redirect()->back()->withErrors([ trans('HomePageTypesController.updateNok') => $e->getMessage()]);
        }
    }

    /**
     * @param $homePageTypeKey
     * @return View
     */
    public function delete($homePageTypeKey)
    {
        $data = array();

        $data['action'] = action("HomePageTypesController@destroy", $homePageTypeKey);
        $data['title'] = "DELETE";
        $data['msg'] = "Are you sure you want to delete this Home Page Type?";
        $data['btn_ok'] = "Delete";
        $data['btn_ko'] = "Cancel";

        return view("_layouts.deleteModal", $data);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $homePageTypeKey
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($homePageTypeKey)
    {
        if(Session::get('user_role') != 'admin'){
            if(!ONE::verifyUserPermissionsDelete('cm', 'home_page_type')) {
                return redirect()->back()->withErrors(["homePageType.destroy" => trans('privateHomePagetype.permission_message')]);
            }
        }
        
        try {
            Orchestrator::deleteHomePageType($homePageTypeKey);
            Session::flash('message', trans('privateHomePageTypes.deleteOk'));
            return action('HomePageTypesController@index');

        }
        catch(Exception $e) {
            return redirect()->back()->withErrors([ trans('privateHomePageTypes.deleteNok') => $e->getMessage()]);
        }
    }

    /**
 * @return mixed
 */
    public function getIndexTable()
    {
        if(Session::get('user_role') == 'admin' || ONE::verifyUserPermissionsShow('cm', 'home_page_type')){
        $homePageTypes = Orchestrator::getGroupHomePageTypes();
        // in case of json
        $collection = Collection::make($homePageTypes);
        }else
            $collection = Collection::make([]);

        $edit = Session::get('user_role') == 'admin' || ONE::verifyUserPermissionsUpdate('cm', 'home_page_type');
        $delete = Session::get('user_role') == 'admin' || ONE::verifyUserPermissionsDelete('cm', 'home_page_type');

        return Datatables::of($collection)
            ->editColumn('name', function ($collection) {
                return "<a href='".action('HomePageTypesController@show', $collection->home_page_type_key)."'>".$collection->name."</a>";
            })
            ->addColumn('action', function ($collection) use($edit, $delete){
                if($delete == true and $edit == true)
                    return ONE::actionButtons($collection->home_page_type_key, ['form' => 'homePageTypes','edit' => 'HomePageTypesController@edit', 'delete' => 'HomePageTypesController@delete']);
                elseif($delete == false and $edit == true)
                    return ONE::actionButtons($collection->home_page_type_key, ['form' => 'homePageTypes','edit' => 'HomePageTypesController@edit']);
                elseif($delete == true and $edit == false)
                    return ONE::actionButtons($collection->home_page_type_key, ['form' => 'homePageTypes', 'delete' => 'HomePageTypesController@delete']);
                else
                    return null;
            })
            ->make(true);
    }

    /**
     * @return mixed
     */
    public function getGroupTypesTable(Request $request)
    {

        if(Session::get('user_role') == 'admin' || ONE::verifyUserPermissionsShow('cm', 'home_page_types_children')) {
            // Advanced Search var's
            $home_page_type_key = $request->input("home_page_type_key");

            $homePageTypes = Orchestrator::getHomePageGroupTypes($home_page_type_key);

            // in case of json
            $collection = Collection::make($homePageTypes);
        }else
            $collection = Collection::make([]);

        $edit = Session::get('user_role') == 'admin' ||  ONE::verifyUserPermissionsUpdate('cm', 'home_page_types_children');
        $delete = Session::get('user_role') == 'admin' || ONE::verifyUserPermissionsDelete('cm', 'home_page_types_children');

        return Datatables::of($collection)
            ->editColumn('name', function ($collection) use ($home_page_type_key) {
                return "<a href='".action('HomePageTypesController@showHomePageGroupType', ['homePageTypeKey' => $home_page_type_key, 'homePageGroupKey' => $collection->home_page_type_key])."'>".$collection->name."</a>";
            })
            ->addColumn('action', function ($collection) use($edit, $delete){
                if($edit == true and $delete == true)
                    return ONE::actionButtons($collection->home_page_type_key, ['form' => 'homePageTypes','edit' => 'HomePageTypesController@edit', 'delete' => 'HomePageTypesController@delete']);
                elseif($edit == false and $delete == true)
                    return ONE::actionButtons($collection->home_page_type_key, ['form' => 'homePageTypes', 'delete' => 'HomePageTypesController@delete']);
                elseif($edit == true and $delete == false)
                    return ONE::actionButtons($collection->home_page_type_key, ['form' => 'homePageTypes','edit' => 'HomePageTypesController@edit']);
                else
                    return null;
            })
            ->make(true);
    }
}
