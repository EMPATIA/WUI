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

class StepperLoginController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        try {
            $siteKey = isset($request->siteKey) ? $request->siteKey : null;
            if (is_null($siteKey))
                $siteKey =  Session::get('SITE_KEY');

            $title = trans('privateEntitySitesLoginLevels.list_login_levels');

            return view('private.entities.sites.loginLevels.index', compact('title','siteKey'));
        } catch (Exception $e) {
            return redirect()->back()->withErrors([trans("privateLoginLevels.index_error") => $e->getMessage()]);
        }
    }

    /**
     * @param Request $request
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
            return view('private.entities.sites.loginLevels.loginLevel', compact('loginLevel', 'siteKey'));
        } catch (Exception $e) {
            return redirect()->back()->withErrors([trans("privateLoginLevels.show_error") => $e->getMessage()]);
        }
    }

    /**
     * @param Request $request
     * @param $levelParameterKey
     * @return LoginLevelsController|\Illuminate\Http\RedirectResponse
     */
    public function edit(Request $request, $levelParameterKey)
    {
        try {
            $siteKey = isset($request->siteKey) ? $request->siteKey : null;
            if (is_null($siteKey))
                $siteKey =  Session::get('SITE_KEY');

            $loginLevel = Orchestrator::getLoginLevel($levelParameterKey);
            return view('private.entities.sites.loginLevels.loginLevel', compact('loginLevel', 'siteKey'));
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

            return view('private.entities.sites.loginLevels.loginLevel', compact('siteKey'));
        } catch (Exception $e) {
            return redirect()->back()->withErrors([trans("privateLoginLevels.create_error") => $e->getMessage()]);
        }
    }


    /**
     * @param LoginLevelRequest $request
     * @return $this|\Illuminate\Http\RedirectResponse
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
     * @param Request $request
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
     * @param Request $request
     * @param $levelParameterKey
     * @return string
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
     * @param Request $request
     * @return $this
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

            return Datatables::of($collection)
                ->editColumn('name', function ($collection) use ($siteKey) {
                    return "<a href='" . action('LoginLevelsController@show', ['parameter_key' => $collection->level_parameter_key, 'siteKey' => $siteKey]) . "'>" . $collection->name . "</a>";
                })
                ->editColumn('mandatory', function ($collection) {
                    return ($collection->mandatory == 0) ? null : '<span style=\'margin-top:-6px; cursor:default;\'  class=\'btn btn-flat btn-success btn-xs\' data-toggle=\'tooltip\' data-delay=\'{&quot;show&quot;:&quot;1000&quot;}\'><i class=\'fa fa-check\'></i></span>';
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
                ->addColumn('action', function ($collection) use ($siteKey) {
                    return ONE::actionButtons(['levelParameterKey' => $collection->level_parameter_key, 'siteKey' => $siteKey], ['form' => 'siteLoginLevels', 'edit' => 'LoginLevelsController@edit', 'delete' => 'LoginLevelsController@delete']);
                })
                ->rawColumns(['name','mandatory','manual_verification','sms_verification','show_in_registration','action'])
                ->make(true);
        } catch (Exception $e) {
            return redirect()->back()->withErrors([trans("privateLoginLevels.get_index_table_error") => $e->getMessage()]);
        }
    }


    /**
     * @param Request $request
     * @return $this|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showConfigurations(Request $request)
    {
        try {

            $levelParameterKey = $request->get('levelParameterKey');
            $siteKey = $request->get('siteKey');
            if (is_null($siteKey))
                $siteKey =  Session::get('SITE_KEY');

            return view('private.entities.sites.loginLevels.configurations', compact('levelParameterKey', 'siteKey'));
        } catch (Exception $e) {
            return redirect()->back()->withErrors([trans("privateLoginLevels.show_configurations_error") => $e->getMessage()]);
        }
    }


    /**
     * @param Request $request
     * @return $this
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
     * @return $this
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
                ->rawColumns(['select'])
                ->make(true);
        } catch (Exception $e) {
            return redirect()->back()->withErrors([trans("privateLoginLevels.get_index_configuration_table_error") => $e->getMessage()]);
        }
    }


    /**
     * @param Request $request
     * @return $this|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showLevelReorder(Request $request)
    {
        try {
            $siteKey = isset($request->siteKey) ? $request->siteKey : null;
            if (is_null($siteKey))
                $siteKey =  Session::get('SITE_KEY');

            $loginLevels = Orchestrator::getLoginLevels($siteKey);

            $sidebar = 'site';
            $active = 'stepperLoginReorder';

            Session::put('sidebarArguments', ['siteKey' => $siteKey, 'activeFirstMenu' => 'stepperLoginReorder']);

            return view('private.entities.sites.loginLevels.loginLevelReorder', compact('loginLevels', 'siteKey', 'sidebar', 'active'));
        } catch (Exception $e) {
            return redirect()->back()->withErrors([trans("privateLoginLevels.show_level_reorder_error") => $e->getMessage()]);
        }
    }


    /**
     * @param Request $request
     * @return bool|string
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
