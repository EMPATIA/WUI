<?php

namespace App\Http\Controllers;

use App\ComModules\CB;
use App\ComModules\Orchestrator;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use One;
use Session;
use Datatables;

class DashBoardElementConfigurationsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //Page title
        $title = trans('privateDashBoardElementConfigurations.list_dashboard_element_configurations');

        return view('private.dashBoardElementConfigurations.index', compact('title'));
    }


    public function getIndexTable(Request $request)
    {
        try {

            $requestFlags = CB::getDashBoardElementConfigurationsList();
            // in case of json
            $dashBoardElementConfigurations = Collection::make($requestFlags);

            //  Datatable with sent emails list
            return Datatables::of($dashBoardElementConfigurations)
                ->editColumn('title', function ($dashBoardElementConfigurations) {
                    return "<a href='" . action('DashBoardElementConfigurationsController@show', $dashBoardElementConfigurations->id) . "'>" . $dashBoardElementConfigurations->title . "</a>";
                })
                ->addColumn('action', function ($dashBoardElementConfigurations) {
                    return ONE::actionButtons($dashBoardElementConfigurations->id, ['delete' => 'DashBoardElementConfigurationsController@delete']);
                })
                ->make(true);
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["dashBoardElementConfigurations.getIndexTable" => $e->getMessage()]);
        }
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $languages = Orchestrator::getLanguageList();

        try {
            // Form title (layout)
            $title = trans('privateDashBoardElementConfigurations.create_dashboard_element_configuration');

            $data = [];
            $data['title'] = $title;
            $data['languages'] = $languages;

            return view('private.dashBoardElementConfigurations.dashBoardElementConfiguration', $data);
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["dashBoardElementConfigurations.create" => $e->getMessage()]);
        }
    }


    /**
     * @param $request
     * @param $languages
     * @return array
     */
    public function prepareTranslationsToSend($request, $languages)
    {
        $translations = [];
        foreach($languages as $language){
            if(!empty($request->input("title_".$language->code))){
                $translations[] = [
                    'language_code' => $language->code,
                    'title'         => $request->input("title_" . $language->code),
                    'description'   => $request->input("description_" . $language->code) ?? null
                ];
            }
        }
        return $translations;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $languages = Orchestrator::getLanguageList();

            $translations = $this->prepareTranslationsToSend($request,$languages);

            //Call to Com Module set method
            CB::setDashBoardElementConfiguration($request, $translations);

            // Message to show + redirect To
            Session::flash('message', trans('privateDashBoardElementConfigurations.store_ok'));
            return redirect()->action('DashBoardElementConfigurationsController@index');

        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["dashBoardElementConfigurations.store" => $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $languages = Orchestrator::getLanguageList();
            $title = trans('privateDashBoardElementConfigurations.show_dashboard_element');
            //Call to Com Module set method
            $dashBoardElementConfiguration = CB::getDashBoardElementConfiguration($id);
            // Message to show + redirect To

            $data = [];
            $data['title'] = $title;
            $data['languages'] = $languages;
            $data['dashBoardElementConfiguration'] = $dashBoardElementConfiguration;
            $data['translations'] = $dashBoardElementConfiguration->translations;
            $data['id'] = $id;

            Session::put('sidebarArguments', ['id' => $id, 'activeFirstMenu' => 'details']);
            return view('private.dashBoardElementConfigurations.dashBoardElementConfiguration', $data);

        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["dashBoardElementConfigurations.store" => $e->getMessage()]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        try {
            $languages = Orchestrator::getLanguageList();
            $title = trans('privateDashBoardElementConfigurations.edit_dashboard_element');
            //Call to Com Module set method
            $dashBoardElementConfiguration = CB::getDashBoardElementConfiguration($id);
            // Message to show + redirect To

            $data = [];
            $data['title'] = $title;
            $data['languages'] = $languages;
            $data['dashBoardElementConfiguration'] = $dashBoardElementConfiguration;
            $data['translations'] = $dashBoardElementConfiguration->translations;

            Session::put('sidebarArguments', ['id' => $id, 'activeFirstMenu' => 'details']);

            return view('private.dashBoardElementConfigurations.dashBoardElementConfiguration', $data);

        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["dashBoardElementConfigurations.edit" => $e->getMessage()]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $languages = Orchestrator::getLanguageList();

            $translations = $this->prepareTranslationsToSend($request,$languages);

            //Call to Com Module set method
            CB::updateDashBoardElementConfiguration($request, $translations,$id);

            // Message to show + redirect To
            Session::flash('message', trans('privateDashBoardElementConfigurations.update_ok'));
            return redirect()->action('DashBoardElementConfigurationsController@show',$id);

        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["dashBoardElementConfigurations.store" => $e->getMessage()]);
        }
    }

    /**
     * @param $id
     * @return View
     */
    public function delete($id)
    {

        $data = array();

        $data['action'] = action("DashBoardElementConfigurationsController@destroy", $id);
        $data['title'] =  trans('privateDashBoardElementConfigurations.delete');
        $data['msg'] = trans('privateDashBoardElementConfigurations.are_you_sure you_want_to_delete_this_dashboard_element') . "?";
        $data['btn_ok'] = trans('privateDashBoardElementConfigurations.delete');
        $data['btn_ko'] = trans('privateDashBoardElementConfigurations.cancel');

        return view("_layouts.deleteModal", $data);
    }

    /**
     * @param $id
     * @return $this|string
     */
    public function destroy($id)
    {
        try {

            CB::deleteDashBoardElement($id);
            Session::flash('message', trans('privateDashBoardElementConfigurations.deleteOk'));
            return action('DashBoardElementConfigurationsController@index');

        } catch (Exception $e) {
            return redirect()->back()->withErrors(["dashBoardElementConfigurations.destroy" => $e->getMessage()]);
        }
    }
}
