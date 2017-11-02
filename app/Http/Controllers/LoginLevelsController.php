<?php

namespace App\Http\Controllers;

use App\ComModules\Orchestrator;
use App\Http\Requests\LoginLevelRequest;
use ONE;
use Illuminate\Http\Request;
use Datatables;
use Exception;
use Illuminate\Support\Collection;
use Session;

class LoginLevelsController extends Controller
{
    /**
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        try {
            $siteKey = isset($request->siteKey) ? $request->siteKey : null;
            if (is_null($siteKey))
                $siteKey =  Session::get('SITE_KEY');

            $title = trans('privateEntitySitesLoginLevels.list_login_levels');

            $sidebar = 'site';
            $active = 'siteLevels';

            Session::put('sidebarArguments', ['siteKey' => $siteKey, 'activeFirstMenu' => 'siteLevels']);

            return view('private.entities.sites.loginLevels.index', compact('title','siteKey', 'sidebar', 'active'));
        } catch (Exception $e) {
            return redirect()->back()->withErrors([trans("privateLoginLevels.index_error") => $e->getMessage()]);
        }
    }

    /**
     * @param $levelParameterKey
     * @return LoginLevelsController|\Illuminate\Http\RedirectResponse
     */
    public function show(Request $request, $levelParameterKey)
    {
        try {
            $loginLevel = Orchestrator::getLoginLevel($levelParameterKey);
            $siteKey = isset($request->siteKey) ? $request->siteKey : null;
            if (is_null($siteKey))
                $siteKey =  Session::get('SITE_KEY');

            $sidebar = 'loginLevelsParameters';
            $active = 'details';

            Session::put('sidebarArguments.levelParameterKey', $levelParameterKey);
            Session::put('sidebarArguments.activeSecondMenu', 'details');

            return view('private.entities.sites.loginLevels.loginLevel', compact('loginLevel', 'levelParameterKey', 'siteKey', 'sidebar', 'active'));
        } catch (Exception $e) {
            return redirect()->back()->withErrors([trans("privateLoginLevels.show_error") => $e->getMessage()]);
        }
    }

    /**
     * @param $levelParameterKey
     * @return LoginLevelsController|\Illuminate\Http\RedirectResponse
     */
    public function edit(Request $request, $levelParameterKey)
    {
        try {
            $siteKey = isset($request->siteKey) ? $request->siteKey : null;
            if (is_null($siteKey))
                $siteKey =  Session::get('SITE_KEY');

            $sidebar = 'loginLevelsParameters';
            $active = 'details';

            Session::put('sidebarArguments.levelParameterKey', $levelParameterKey);
            Session::put('sidebarArguments.activeSecondMenu', 'details');

            $loginLevel = Orchestrator::getLoginLevel($levelParameterKey);
            return view('private.entities.sites.loginLevels.loginLevel', compact('loginLevel', 'levelParameterKey', 'siteKey', 'sidebar', 'active'));
        } catch (Exception $e) {
            return redirect()->back()->withErrors([trans("privateLoginLevels.edit_error") => $e->getMessage()]);
        }
    }

    /**
     * @param LoginLevelRequest $request
     * @param $levelParameterKey
     * @return LoginLevelsController|\Illuminate\Http\RedirectResponse
     */
    public function update(LoginLevelRequest $request, $levelParameterKey)
    {
        try {
            $siteKey = isset($request->siteKey) ? $request->siteKey : null;
            if (is_null($siteKey))
                $siteKey =  Session::get('SITE_KEY');

            Orchestrator::updateLoginLevel($request, $levelParameterKey);
            Session::flash('message', trans('siteLoginLevels.update_ok'));
            return redirect()->action('LoginLevelsController@show', compact('levelParameterKey', 'siteKey'));
        } catch (Exception $e) {
            return redirect()->back()->withErrors([trans("privateLoginLevels.update_error") => $e->getMessage()]);
        }
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function create(Request $request)
    {
        try {
            $siteKey = isset($request->siteKey) ? $request->siteKey : null;
            if (is_null($siteKey))
                $siteKey =  Session::get('SITE_KEY');

            $sidebar = 'site';
            $active = 'siteLevels';

            Session::put('sidebarArguments', ['siteKey' => $siteKey, 'activeFirstMenu' => 'siteLevels']);

            return view('private.entities.sites.loginLevels.loginLevel', compact('siteKey', 'sidebar', 'active'));
        } catch (Exception $e) {
            return redirect()->back()->withErrors([trans("privateLoginLevels.create_error") => $e->getMessage()]);
        }
    }

    /**
     * @param LoginLevelRequest $request
     * @return LoginLevelsController|\Illuminate\Http\RedirectResponse
     */
    public function store(LoginLevelRequest $request)
    {
        try {
            $siteKey = isset($request->siteKey) ? $request->siteKey : null;
            if (is_null($siteKey))
                $siteKey =  Session::get('SITE_KEY');

            Orchestrator::setLoginLevel($request);
            Session::flash('message', trans('privateLoginLevels.store_ok'));
            return redirect()->action('LoginLevelsController@index', ['siteKey' => $siteKey]);
        } catch (Exception $e) {
            return redirect()->back()->withErrors([trans("privateLoginLevels.store_error") => $e->getMessage()]);
        }
    }

    /**
     * @param $levelParameterKey
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function delete(Request $request, $levelParameterKey)
    {
        $data = array();
        $siteKey = isset($request->siteKey) ? $request->siteKey : null;
        $data['action'] = action("LoginLevelsController@destroy", ['levelParameterKey' => $levelParameterKey, 'siteKey' => $siteKey]);
        $data['title'] = "DELETE";
        $data['msg'] = "Are you sure you want to delete this Level?";
        $data['btn_ok'] = "Delete";
        $data['btn_ko'] = "Cancel";

        return view("_layouts.deleteModal", $data);
    }

    /**
     * @param $levelParameterKey
     * @return LoginLevelsController|\Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request, $levelParameterKey)
    {
        try {
            $siteKey = isset($request->siteKey) ? $request->siteKey : null;
            if (is_null($siteKey))
                $siteKey =  Session::get('SITE_KEY');

            Orchestrator::deleteLoginLevel($levelParameterKey);
            Session::flash('message', trans('privateLoginLevels.delete_ok'));
            return action('LoginLevelsController@index', compact('siteKey'));
        } catch (Exception $e) {
            return redirect()->back()->withErrors([trans("privateLoginLevels.delete_error") => $e->getMessage()])->getTargetUrl();
        }
    }

    /**
     * @return LoginLevelsController|\Illuminate\Http\RedirectResponse
     */
    public function getIndexTable(Request $request)
    {
        try {

            $siteKey = isset($request->siteKey) ? $request->siteKey : null;
            if (is_null($siteKey))
                $siteKey =  Session::get('SITE_KEY');

            $loginLevels = Orchestrator::getLoginLevels($siteKey);
            // in case of json
            $collection = Collection::make($loginLevels);

            $edit = ONE::verifyUserPermissions('orchestrator', 'site_login_levels', 'update');
            $delete = ONE::verifyUserPermissions('orchestrator', 'site_login_levels', 'delete');

            return Datatables::of($collection)
                ->editColumn('name', function ($collection) use ($siteKey) {
                    return "<a href='" . action('LoginLevelsController@show', ['parameter_key' => $collection->level_parameter_key, 'siteKey' => $siteKey]) . "'>" . $collection->name . "</a>";
                })
                ->editColumn('mandatory', function ($collection) {
                    return ($collection->mandatory == 0) ? null : '<span style=\'margin-top:-6px; cursor:default;\'  class=\'badge badge-success\' data-toggle=\'tooltip\' data-delay=\'{&quot;show&quot;:&quot;1000&quot;}\'><i class=\'fa fa-check\'></i></span>';
                })
                ->editColumn('manual_verification', function ($collection) {
                    return ($collection->manual_verification == 0) ? null : '<span style=\'margin-top:-6px; cursor:default;\'  class=\'btn btn-flat btn-success btn-xs\' data-toggle=\'tooltip\' data-delay=\'{&quot;show&quot;:&quot;1000&quot;}\'><i class=\'fa fa-check\'></i></span>';
                })
                ->editColumn('sms_verification', function ($collection) {
                    return ($collection->sms_verification == 0) ? null : '<span style=\'margin-top:-6px; cursor:default;\'  class=\'btn btn-flat btn-success btn-xs\' data-toggle=\'tooltip\' data-delay=\'{&quot;show&quot;:&quot;1000&quot;}\'><i class=\'fa fa-check\'></i></span>';
                })
                ->editColumn('show_in_registration', function ($collection) {
                    return ($collection->show_in_registration == 0) ? null : '<span style=\'margin-top:-6px; cursor:default;\'  class=\'btn btn-flat btn-success btn-xs\' data-toggle=\'tooltip\' data-delay=\'{&quot;show&quot;:&quot;1000&quot;}\'><i class=\'fa fa-check\'></i></span>';
                })
                ->addColumn('action', function ($collection) use ($siteKey, $edit, $delete) {
                    if($edit == true and $delete == true)
                        return ONE::actionButtons(['levelParameterKey' => $collection->level_parameter_key, 'siteKey' => $siteKey], ['form' => 'siteLoginLevels', 'edit' => 'LoginLevelsController@edit', 'delete' => 'LoginLevelsController@delete']);
                    elseif($edit == false and $delete == true)
                        return ONE::actionButtons(['levelParameterKey' => $collection->level_parameter_key, 'siteKey' => $siteKey], ['form' => 'siteLoginLevels', 'delete' => 'LoginLevelsController@delete']);
                    elseif($edit == true and $delete == false)
                        return ONE::actionButtons(['levelParameterKey' => $collection->level_parameter_key, 'siteKey' => $siteKey], ['form' => 'siteLoginLevels', 'edit' => 'LoginLevelsController@edit']);
                    else
                        return null;
                })
                ->make(true);
        } catch (Exception $e) {
            return redirect()->back()->withErrors([trans("privateLoginLevels.get_index_table_error") => $e->getMessage()]);
        }
    }

    /**
     * @param Request $request
     * @return LoginLevelsController|\Illuminate\Http\RedirectResponse
     */
    public function showConfigurations(Request $request)
    {
        try {

            $levelParameterKey = $request->get('levelParameterKey');
            $siteKey = $request->get('siteKey');
            if (is_null($siteKey))
                $siteKey =  Session::get('SITE_KEY');

            $sidebar = 'loginLevelsParameters';
            $active = 'parameters';

            Session::put('sidebarArguments.levelParameterKey', $levelParameterKey);
            Session::put('sidebarArguments.activeSecondMenu', 'parameters');

            return view('private.entities.sites.loginLevels.configurations', compact('levelParameterKey', 'siteKey', 'active', 'sidebar'));
        } catch (Exception $e) {
            return redirect()->back()->withErrors([trans("privateLoginLevels.show_configurations_error") => $e->getMessage()]);
        }
    }

    /**
     * @param Request $request
     * @return LoginLevelsController|\Illuminate\Http\RedirectResponse
     */
    public function updateParameter(Request $request)
    {
        try {
            $parameterUserTypeKey = $request->get('parameterUserTypeKey');
            $levelParameterKey = $request->get('levelParameterKey');

            Orchestrator::updateLoginLevelParameters($parameterUserTypeKey, $levelParameterKey);
        } catch (Exception $e) {
            return redirect()->back()->withErrors([trans("privateLoginLevels.update_parameter_error") => $e->getMessage()]);
        }
    }

    /**
     * @param Request $request
     * @return LoginLevelsController|\Illuminate\Http\RedirectResponse
     */
    public function getIndexConfigurationsTable(Request $request)
    {
        try {
            $levelParameterKey = $request->get('levelParameterKey');
            $loginLevelParameters = Orchestrator::getLoginLevelParameters($levelParameterKey);

            // in case of json
            $collection = Collection::make($loginLevelParameters);
            return Datatables::of($collection)
                ->editColumn('selected', function ($collection) use ($levelParameterKey) {
                    if ($collection->selected == false){
                        $button = '<a href="'.action('LoginLevelsController@updateParameter',['parameterUserTypeKey' => $collection->parameter_user_type_key, 'levelParameterKey' => $levelParameterKey]).'" class="update-btn btn btn-flat btn-warning btn-xs" data-original-title="'.trans("privateLoginLevels.add").'"><i class="fa fa-plus"></i></a>';
                    } else {
                        $button = '<a href="'.action('LoginLevelsController@updateParameter',['parameterUserTypeKey' => $collection->parameter_user_type_key, 'levelParameterKey' => $levelParameterKey]).'" class="update-btn btn btn-flat btn-danger btn-xs" data-original-title="'.trans("privateLoginLevels.remove").'"><i class="fa fa-remove"></i></a>';
                    }
                    return $button;
                })
                ->make(true);
        } catch (Exception $e) {
            return redirect()->back()->withErrors([trans("privateLoginLevels.get_index_configuration_table_error") => $e->getMessage()]);
        }
    }

    /**
     * @return LoginLevelsController|\Illuminate\Http\RedirectResponse
     */
    public function showLevelReorder(Request $request)
    {
        try {
            $siteKey = isset($request->siteKey) ? $request->siteKey : null;
            if (is_null($siteKey))
                $siteKey =  Session::get('SITE_KEY');

            $sidebar = 'site';
            $active = 'reorder';

            Session::put('sidebarArguments.activeFirstMenu', 'reorder');

            $loginLevels = Orchestrator::getLoginLevels($siteKey);
            return view('private.entities.sites.loginLevels.loginLevelReorder', compact('loginLevels', 'siteKey', 'sidebar', 'active'));
        } catch (Exception $e) {
            return redirect()->back()->withErrors([trans("privateLoginLevels.show_level_reorder_error") => $e->getMessage()]);
        }
    }

    /**
     * @param Request $request
     * @return LoginLevelsController|bool|\Illuminate\Http\RedirectResponse
     */
    public function updateOrder(Request $request)
    {
        try {
            $ordering = json_decode($request->order);  // new order
            Orchestrator::updateLoginLevelPositions($ordering);
            return 'true';
        } catch (Exception $e) {
            return false;
        }
    }
}
