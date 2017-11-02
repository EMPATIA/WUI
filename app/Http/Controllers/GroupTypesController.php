<?php

namespace App\Http\Controllers;

use App\ComModules\Orchestrator;
use App\Http\Requests\GroupTypeRequest;
use Carbon\Carbon;
use Datatables;
use Exception;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Collection;
use ONE;
use Session;
use View;
use Breadcrumbs;

class GroupTypesController extends Controller
{

    public function __construct()
    {
        //TODO: view/edit breadcrumbs (below) for current content
       /* View::share('private.groupTypes', trans('groupTypes.group_type'));

        Breadcrumbs::register('private.groupTypes.index', function ($breadcrumbs) {
            $breadcrumbs->parent('private');
            $breadcrumbs->push(trans('privateGroupTypes.group_types.index'), action('EntitiesController@index'));
        });


        Breadcrumbs::register('private.groupTypes.edit', function ($breadcrumbs) {
            if (ONE::isAdmin()) {
                $breadcrumbs->parent('private');
            } else {
                $breadcrumbs->parent('private.groupTypes.index');
            }
            $breadcrumbs->push(trans('privateGroupTypes.group_types.edit'));
        });


        Breadcrumbs::register('private.groupTypes.create', function ($breadcrumbs) {
            if (ONE::isAdmin()) {
                $breadcrumbs->parent('private');
            } else {
                $breadcrumbs->parent('private.groupTypes.index');
            }
            $breadcrumbs->push(trans('privateGroupTypes.group_types.create'));
        });

        Breadcrumbs::register('private.groupTypes.store', function ($breadcrumbs) {
            if (ONE::isAdmin()) {
                $breadcrumbs->parent('private');
            } else {
                $breadcrumbs->parent('private.groupTypes.index');
            }
            $breadcrumbs->push(trans('privateGroupTypes.group_types.store'));
        });


        Breadcrumbs::register('private.groupTypes.show', function ($breadcrumbs) {
            if (ONE::isAdmin()) {
                $breadcrumbs->parent('private');
            } else {
                $breadcrumbs->parent('private.groupTypes.index');
            }
            $breadcrumbs->push(trans('privateGroupTypes.group_types.show'));
        });

        Breadcrumbs::register('private.groupTypes.update', function ($breadcrumbs) {
            if (ONE::isAdmin()) {
                $breadcrumbs->parent('private');
            } else {
                $breadcrumbs->parent('private.groupTypes.index');
            }
            $breadcrumbs->push(trans('privateGroupTypes.group_types.update'));
        });*/
    }

    /**
     * @return View
     */
    public function index()
    {
        //Page title
        $title = trans('privateGroupTypes.list_group_types');

        return view('private.groupTypes.index', compact('title'));
    }

    /**
     * @return $this
     */
    public function tableGroupTypes()
    {
        try {
            //Get all group types
            $groupTypes = Orchestrator::getGroupTypes();

            // in case of json
            $groupTypes = Collection::make($groupTypes);

            //  Datatable with Group Types list
            return Datatables::of($groupTypes)
                ->editColumn('code', function ($groupType) {
                    return "<a href='" . action('GroupTypesController@show', $groupType->group_type_key) . "'>" . $groupType->code . "</a>";
                })
                ->addColumn('action', function ($groupType) {
                    return ONE::actionButtons($groupType->group_type_key, ['edit' => 'GroupTypesController@edit', 'delete' => 'GroupTypesController@delete', 'form' => 'groupTypes']);
                })
                ->make(true);
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["groupTypes.tableGroupTypes" => $e->getMessage()]);
        }
    }

    /**
     * @param Request $request
     * @param $groupTypeKey
     * @return $this|View
     */
    public function show(Request $request, $groupTypeKey)
    {
        try {

            $groupType = Orchestrator::getGroupTypeByKey($groupTypeKey);

            // Form title (layout)
            $title = trans('privateGroupTypes.show_group_type');

            // Return the view with data
            $data = [];
            $data['title'] = $title;
            $data['id'] = $groupType->id;
            $data['groupType'] = $groupType;
            $data['groupTypeKey'] = $groupType->group_type_key;

            return view('private.groupTypes.groupType', $data);
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["groupType.show" => $e->getMessage()]);
        }
    }

    /**
     * @param Request $request
     * @return $this|View
     */
    public function create(Request $request)
    {
        try {
            // Form title (layout)
            $title = trans('privateGroupTypes.create_group_type');

            // Return the view with data
            $data = [];
            $data['title'] = $title;

            return view('private.groupTypes.groupType', $data);
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["groupType.create" => $e->getMessage()]);
        }
    }


    /**
     * @param GroupTypeRequest $request
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function store(GroupTypeRequest $request)
    {
        try {

            //Call to Com Module set method
            Orchestrator::setGroupType($request);

            // Message to show + redirect To
            Session::flash('message', trans('privateGroupTypes.store_ok'));
            return redirect()->action('GroupTypesController@index');



        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["groupType.store" => $e->getMessage()]);
        }
    }

    /**
     * @param Request $request
     * @param $groupTypeKey
     * @return $this|View
     */
    public function edit(Request $request, $groupTypeKey)
    {
        try {

            //get object
            $groupType = Orchestrator::getGroupTypeByKey($groupTypeKey);

           //set Form title (layout)
            $title = trans('privateGroupTypes.edit_group_type');

            // Return the view with data
            $data = [];
            $data['title'] = $title;
            $data['id'] = $groupType->id;
            $data['groupType'] = $groupType;
            $data['groupTypeKey'] = $groupType->group_type_key;

            return view('private.groupTypes.groupType', $data);

        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["groupType.edit" => $e->getMessage()]);
        }


    }

    /**
     * @param GroupTypeRequest $request
     * @param $groupTypeKey
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function update(GroupTypeRequest $request, $groupTypeKey)
    {
        try {
            //Call to Com Module update method
            Orchestrator::updateGroupType($request, $groupTypeKey);
            // Message to show + redirect To
            Session::flash('message', trans('privateGroupTypes.update_ok'));
            return redirect()->action('GroupTypesController@index');


        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["groupType.edit" => $e->getMessage()]);
        }


    }

    /**
     * @param $groupTypeKey
     * @return View
     */
    public function delete($groupTypeKey)
    {

        $data = array();

        $data['action'] = action("GroupTypesController@destroy", $groupTypeKey);
        $data['title'] =  trans('privateGroupTypes.delete');
        $data['msg'] = trans('privateGroupTypes.are_you_sure you_want_to_delete_this_group_type') . "?";
        $data['btn_ok'] = trans('privateGroupTypes.delete');
        $data['btn_ko'] = trans('privateGroupTypes.cancel');

        return view("_layouts.deleteModal", $data);
    }

    /**
     * @param $groupTypeKey
     * @return $this|string
     */
    public function destroy($groupTypeKey)
    {
       try {
            //Call to Com Module delete method
            Orchestrator::deleteGroupType($groupTypeKey);

            // Message to show + redirect To
            Session::flash('message', trans('privateGroupTypes.delete_ok'));
            return action('GroupTypesController@index');
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors([ trans('privateGroupTypes.delete_nok') => $e->getMessage()]);
        }
    }
}
