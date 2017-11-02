<?php

namespace App\Http\Controllers;

use App\ComModules\Orchestrator;
use App\Http\Requests\EntityLoginLevelRequest;
use App\One\One;
use Exception;
use Form;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Collection;
use Session;
use Yajra\Datatables\Facades\Datatables;

class EntityLoginLevelsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param $entityKey
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */

    public function index($entityKey)
    {
        try {
            $sidebar = 'entity';
            $active = 'entityLevels';

            Session::put('sidebarArguments', ['entityKey' => $entityKey, 'activeFirstMenu' => $active]);

            $title = trans('privateEntityLoginLevels.list_login_levels');
            return view('private.entities.loginLevels.index', compact('title','entityKey', 'sidebar', 'active'));

        } catch (Exception $e) {
            return redirect()->back()->withErrors([trans("privateEntityLoginLevels.index_error") => $e->getMessage()]);
        }
    }

    /**
     * @param Request $request
     * @param $loginLevelKey
     * @return EntityLoginLevelsController|\Illuminate\Http\RedirectResponse
     */
    public function show(Request $request, $loginLevelKey)
    {
        try {
            $entityKey = $request->entityKey ?? Session::get('X-ENTITY-KEY');
            $loginLevel = Orchestrator::getEntityLoginLevel($loginLevelKey);
            $loginLevelDependencies = Orchestrator::getAllEntityLoginLevels($entityKey);

            $sidebar = 'entityLoginLevels';
            $active = 'details';

            Session::put('sidebarArguments', ['activeFirstMenu' => $active]);

            return view('private.entities.loginLevels.loginLevel', compact('loginLevel', 'entityKey','loginLevelDependencies','sidebar','active'));
        } catch (Exception $e) {
            return redirect()->back()->withErrors([trans("privateEntityLoginLevels.show_error") => $e->getMessage()]);
        }
    }

    /**
     * @param Request $request
     * @return EntityLoginLevelsController|\Illuminate\Http\RedirectResponse
     */
    public function create(Request $request)
    {
        try {
            $entityKey = $request->entityKey ?? Session::get('X-ENTITY-KEY');
            $loginLevelDependencies = Orchestrator::getAllEntityLoginLevels($entityKey);

            return view('private.entities.loginLevels.loginLevel', compact('loginLevelDependencies','entityKey'));
        } catch (Exception $e) {
            return redirect()->back()->withErrors([trans("privateEntityLoginLevels.create_error") => $e->getMessage()]);
        }
    }


    /**
     * @param EntityLoginLevelRequest $request
     * @return EntityLoginLevelsController|\Illuminate\Http\RedirectResponse
     */
    public function store(EntityLoginLevelRequest $request)
    {
        try {
            $entityKey = $request->get('entity_key') ?? Session::get('X-ENTITY-KEY');
            Orchestrator::setEntityLoginLevel($request);
            Session::flash('message', trans('privateEntityLoginLevels.store_ok'));
            return redirect()->action('EntityLoginLevelsController@index', ['entityKey' => $entityKey]);
        } catch (Exception $e) {
            return redirect()->back()->withErrors([trans("privateEntityLoginLevels.store_error") => $e->getMessage()]);
        }
    }


    /**
     * @param Request $request
     * @param $loginLevelKey
     * @return EntityLoginLevelsController|\Illuminate\Http\RedirectResponse
     */
    public function edit(Request $request, $loginLevelKey)
    {
        try {
            $entityKey = $request->entityKey ?? Session::get('X-ENTITY-KEY');
            $loginLevel = Orchestrator::getEntityLoginLevel($loginLevelKey);
            $loginLevelDependencies = Orchestrator::getAllEntityLoginLevels($entityKey);

            return view('private.entities.loginLevels.loginLevel', compact('loginLevel', 'entityKey','loginLevelDependencies'));
        } catch (Exception $e) {
            return redirect()->back()->withErrors([trans("privateEntityLoginLevels.edit_error") => $e->getMessage()]);
        }
    }

    /**
     * @param EntityLoginLevelRequest $request
     * @param $loginLevelKey
     * @return EntityLoginLevelsController|\Illuminate\Http\RedirectResponse
     */
    public function update(EntityLoginLevelRequest $request, $loginLevelKey)
    {
        try {
            $entityKey = $request->entityKey ?? Session::get('X-ENTITY-KEY');

            Orchestrator::updateEntityLoginLevel($request, $loginLevelKey);
            Session::flash('message', trans('privateEntityLoginLevels.update_ok'));
            return redirect()->action('EntityLoginLevelsController@show', ['level_parameter_key' => $loginLevelKey, 'entity_key' => $entityKey]);
        } catch (Exception $e) {
            return redirect()->back()->withErrors([trans("privateEntityLoginLevels.update_error") => $e->getMessage()]);
        }
    }

    /**
     * @param Request $request
     * @param $loginLevelKey
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function delete(Request $request, $loginLevelKey)
    {
        $data = array();
        $entityKey = $request->entityKey ?? Session::get('X-ENTITY-KEY');
        $data['action'] = action("EntityLoginLevelsController@destroy", ['loginLevelKey' => $loginLevelKey, 'entityKey' => $entityKey]);

        $data['title'] = trans('privateEntityLoginLevels.delete');
        $data['msg'] = trans('privateEntityLoginLevels.are_you_sure_you_want_to_delete');
        $data['btn_ok'] = trans('privateEntityLoginLevels.delete');
        $data['btn_ko'] = trans('privateEntityLoginLevels.cancel');

        return view("_layouts.deleteModal", $data);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param $loginLevelKey
     * @return \Illuminate\Http\Response
     * @internal param int $id
     */
    public function destroy(Request $request, $loginLevelKey)
    {
        try {
            $entityKey = $request->entityKey ?? Session::get('X-ENTITY-KEY');

            Orchestrator::deleteEntityLoginLevel($loginLevelKey);
            Session::flash('message', trans('privateEntityLoginLevels.delete_ok'));
            return action('EntityLoginLevelsController@index', ['entityKey' => $entityKey]);
        } catch (Exception $e) {
            return redirect()->back()->withErrors([trans("privateEntityLoginLevels.delete_error") => $e->getMessage()])->getTargetUrl();
        }
    }


    /**
     * @param Request $request
     * @return EntityLoginLevelsController|\Illuminate\Http\RedirectResponse
     */

    public function getIndexTable(Request $request)
    {
        try {
            $entityKey = $request->entityKey ?? Session::get('X-ENTITY-KEY');

            $loginLevels = Orchestrator::getAllEntityLoginLevels($entityKey);

            // in case of json
            $collection = Collection::make($loginLevels);

            return Datatables::of($collection)
                ->editColumn('name', function ($collection)use($entityKey){
                    return "<a href='" . action('EntityLoginLevelsController@show', ['login_level_key' => $collection->login_level_key,'entity_key' => $entityKey]) . "'>" . $collection->name . "</a>";
                })
                ->addColumn('action', function ($collection)use($entityKey){
                    return ONE::actionButtons(['login_level_key' => $collection->login_level_key,'entity_key' => $entityKey], ['form' => 'entityLoginLevels', 'edit' => 'EntityLoginLevelsController@edit', 'delete' => 'EntityLoginLevelsController@delete']);
                })
                ->make(true);
        } catch (Exception $e) {
            return redirect()->back()->withErrors([trans("privateEntityLoginLevels.get_index_table_error") => $e->getMessage()]);
        }
    }

    /**
     * @param Request $request
     * @param $loginLevelKey
     * @return EntityLoginLevelsController|\Illuminate\Http\RedirectResponse
     */
    public function showParameters(Request $request,$loginLevelKey)
    {
        try {
            $entityKey = $request->entityKey ?? Session::get('X-ENTITY-KEY');
            $sidebar = 'entityLoginLevels';
            $active = 'parameters';

            return view('private.entities.loginLevels.parameters', compact('loginLevelKey', 'entityKey', 'active', 'sidebar'));
        } catch (Exception $e) {
            return redirect()->back()->withErrors([trans("privateEntityLoginLevels.show_parameters_error") => $e->getMessage()]);
        }
    }


    /**
     * @param Request $request
     * @return EntityLoginLevelsController|\Illuminate\Http\RedirectResponse
     */
    public function updateParameter(Request $request)
    {
        try {
            $parameterUserTypeKey = $request->get('parameterUserTypeKey');
            $loginLevelKey = $request->get('loginLevelKey');
            Orchestrator::updateEntityLoginLevelParameters($parameterUserTypeKey, $loginLevelKey);
        } catch (Exception $e) {
            return redirect()->back()->withErrors([trans("privateLoginLevels.update_parameter_error") => $e->getMessage()]);
        }
    }


    /**
     * @param Request $request
     * @param $loginLevelKey
     * @return EntityLoginLevelsController|\Illuminate\Http\RedirectResponse
     */
    public function getIndexParametersTable(Request $request,$loginLevelKey)
    {
        try {
            $loginLevelParameters = Orchestrator::getEntityLoginLevelParameters($loginLevelKey);

            // in case of json
            $collection = Collection::make($loginLevelParameters);
            return Datatables::of($collection)
                ->editColumn('selected', function ($collection) use ($loginLevelKey) {
                    $button = Form::oneSwitch("parameter_".$collection->parameter_user_type_key,null, $collection->selected ,["readonly"=>false,"onchange"=>"updateLoginLevelParameter('".action('EntityLoginLevelsController@updateParameter',['parameterUserTypeKey' => $collection->parameter_user_type_key, 'loginLevelKey' => $loginLevelKey])."')"]);
                    return $button;
                })
                ->editColumn('selected', function ($collection) use ($loginLevelKey) {
                    $button = Form::oneSwitch("parameter_".$collection->parameter_user_type_key,null, $collection->selected ,["readonly"=>false,"onchange"=>"updateLoginLevelParameter('".action('EntityLoginLevelsController@updateParameter',['parameterUserTypeKey' => $collection->parameter_user_type_key, 'loginLevelKey' => $loginLevelKey])."')"]);
                    return $button;
                })
                ->make(true);
        } catch (Exception $e) {
            return redirect()->back()->withErrors([trans("privateLoginLevels.get_index_parameters_table_error") => $e->getMessage()]);
        }
    }


    /**
     * Call to update all entity users for entity defined login levels
     *
     * @return $this
     */
    public function updateAllUserLevels()
    {
        try {

            //Call to CB ComModule Method
            $response = Orchestrator::autoUpdateEntityUsersLoginLevels();

            return redirect()->back();
        } catch (Exception $e) {
            return redirect()->back()->withErrors([trans("privateLoginLevels.update_all_user_levels_error") => $e->getMessage()]);
        }
    }


}
