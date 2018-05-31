<?php

namespace App\Http\Controllers;

use App\ComModules\CB;
use App\ComModules\Orchestrator;
use Cache;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use One;
use Session;
use Datatables;

class DashBoardElementsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //Page title
        $title = trans('privateDashBoardElements.list_dashboard_elements');

        return view('private.dashBoardElements.index', compact('title'));
    }


    public function getIndexTable(Request $request)
    {
        try {

            $requestFlags = CB::getDashBoardElementsList();
            // in case of json
            $dashBoardElements = Collection::make($requestFlags);

            //  Datatable with sent emails list
            return Datatables::of($dashBoardElements)
                ->editColumn('title', function ($dashBoardElements) {
                    return "<a href='" . action('DashBoardElementsController@show', $dashBoardElements->id) . "'>" . $dashBoardElements->title . "</a>";
                })
                ->addColumn('action', function ($dashBoardElements) {
                    return ONE::actionButtons($dashBoardElements->id, ['delete' => 'DashBoardElementsController@delete']);
                })
                ->rawColumns(['title','action'])
                ->make(true);
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["dashBoardElements.getIndexTable" => $e->getMessage()]);
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
            $title = trans('privateDashBoardElements.create_dashboard_element');

            $data = [];
            $data['title'] = $title;
            $data['languages'] = $languages;

            return view('private.dashBoardElements.dashBoardElement', $data);
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["dashBoardElements.create" => $e->getMessage()]);
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
            CB::setDashBoardElement($request, $translations);

            // Message to show + redirect To
            Session::flash('message', trans('privateDashBoardElements.store_ok'));
            return redirect()->action('DashBoardElementsController@index');

        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["dashBoardElements.store" => $e->getMessage()]);
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
            $title = trans('privateDashBoardElements.show_dashboard_element');
            //Call to Com Module set method
            $dashBoardElement = CB::getDashBoardElement($id);
            // Message to show + redirect To

            $data = [];
            $data['title'] = $title;
            $data['languages'] = $languages;
            $data['dashBoardElement'] = $dashBoardElement;
            $data['translations'] = collect($dashBoardElement->translations)->keyBy('language_code')->toArray();
            $data['id'] = $id;

            return view('private.dashBoardElements.dashBoardElement', $data);

        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["dashBoardElements.store" => $e->getMessage()]);
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
            $title = trans('privateDashBoardElements.edit_dashboard_element');
            //Call to Com Module set method
            $dashBoardElement = CB::getDashBoardElement($id);
            // Message to show + redirect To

            $data = [];
            $data['title'] = $title;
            $data['languages'] = $languages;
            $data['dashBoardElement'] = $dashBoardElement;
            $data['translations'] = collect($dashBoardElement->translations)->keyBy('language_code')->toArray();

            return view('private.dashBoardElements.dashBoardElement', $data);

        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["dashBoardElements.edit" => $e->getMessage()]);
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
            CB::updatedashBoardElement($request, $translations,$id);

            // Message to show + redirect To
            Session::flash('message', trans('privateDashBoardElements.update_ok'));
            return redirect()->action('DashBoardElementsController@show',$id);

        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["dashBoardElements.store" => $e->getMessage()]);
        }
    }

    /**
     * @param $id
     * @return View
     */
    public function delete($id)
    {

        $data = array();

        $data['action'] = action("DashBoardElementsController@destroy", $id);
        $data['title'] =  trans('privateDashBoardElements.delete');
        $data['msg'] = trans('privateDashBoardElements.are_you_sure you_want_to_delete_this_dashboard_element') . "?";
        $data['btn_ok'] = trans('privateDashBoardElements.delete');
        $data['btn_ko'] = trans('privateDashBoardElements.cancel');

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
            Session::flash('message', trans('privateDashBoardElements.deleteOk'));
            return action('DashBoardElementsController@index');

        } catch (Exception $e) {
            return redirect()->back()->withErrors(["dashBoardElements.destroy" => $e->getMessage()]);
        }
    }


    public function loadConfigurationsView(Request $request)
    {
        try {
            $entityDashboardElements = Cache::get('entityDashboardElements_'.ONE::getEntityKey());
            $userDashboardElements = Session::get('userDashboardElements');
            $dataToView = array();

            if(!empty($request->get("id"))){
                $dashboardElementId = null;

                foreach ($userDashboardElements as $userDashboardElement) {
                    if ($userDashboardElement->id ==  $request->get("id")) {
                        $dataToView['id'] = $request->get("id");
                        $dataToView['userConfigurations'] =  $userDashboardElement->configurations;
                        $dashboardElementId = $userDashboardElement->dashboard_element_id;
                    }
                }

                foreach ($entityDashboardElements as $entityDashboardElement) {
                    if($entityDashboardElement->id == $dashboardElementId){
                        $dataToView['configurations'] = $entityDashboardElement->configurations;
                    }
                }
            } else if(!empty($entityDashboardElements)) {
                foreach ($entityDashboardElements as $entityDashboardElement) {
                    if($entityDashboardElement->id == $request->get("dashboard_element_id")){
                        $dataToView['configurations'] = $entityDashboardElement->configurations;
                    }
                }

                if(Session::has('sessionDashBoardElements')){
                    $elements = Session::get('sessionDashBoardElements');
                    foreach($elements["available_entity_elements"] as $element){
                        if($element->id == $request->get("dashboard_element_id")){
                            $data['configurations'] =  $element->configurations;
                            return view('private.dashBoardElements.configureDashBoardElement', $data);
                        }
                    }
                }
            }

            if (!empty($dataToView))
                return view('private.dashBoardElements.configureDashBoardElement', $dataToView);
        } catch (Exception $e) {
            return response()->json(['error' => trans('privateDashBoardElements.failedToLoadDashBoardConfigurationsView')]);
        }
    }

    public function setUserDashBoardElement(Request $request)
    {
        try {
            if (ONE::isEntity()) {

                // Get Element ID
                foreach ($request['inputs'] as $input) {
                    if($input['name']=="id") {
                        $elementId = $input['value'];
                    }
                }

                if (!empty($elementId)){

                    $attributes = [];
                    foreach ($request['inputs'] as $input) {
                        if($input['name']!="id") {
                            $attributes[$input['name']] = $input['value'];
                        }
                    }

                    $userKey = ONE::getUserKey();

                    CB::updateUserDashBoardElement($elementId, $attributes, $userKey);

                } else {
                    $attributes = [];
                    foreach ($request['inputs'] as $input) {
                        $attributes[$input['name']] = $input['value'];
                    }

                    $userKey = ONE::getUserKey();
                    CB::setUserDashBoardElement($attributes, $userKey);
                }

                Session::forget('userDashboardElements');
                return response()->json(['success' => trans('privateDashBoardElements.successOnUpdateUserDashBoarElements')]);
            }
        } catch (Exception $e) {
            return response()->json(['error' => trans('privateDashBoardElements.failedToUpdateUserDashBoarElements')]);
        }
    }

    public function unsetUserDashBoardElement(Request $request)
    {
        try {
            if (ONE::isEntity()) {
                $userKey = ONE::getUserKey();
                CB::unsetUserDashBoardElement($request["id"], $userKey);
                Session::forget('userDashboardElements');
                Session::forget('entityDashboardElements_'.ONE::getEntityKey());
                return response()->json(['success' => trans('privateDashBoardElements.successOnUpdateUserDashBoarElements')]);
            }
        } catch (Exception $e) {
            return response()->json(['error' => trans('privateDashBoardElements.failedToUpdateUserDashBoarElements')]);
        }
    }

    public function makeRequestAccordingToDashBoardElement(Request $request)
    {
        try {
            if(isset($request['dashBoardElement'])){
                $dashBoardElement = json_decode($request['dashBoardElement']);
                $code = $request['code'];

                switch($code){
                    case "comments_moderation":
                        return QuickAccessController::getCommentsToModerate($dashBoardElement->configurations);
                        break;
                    case "last_topics":
                        return QuickAccessController::getTopicsToModerate($dashBoardElement->configurations);
                        break;
                    case "pads_moderation":
                        return QuickAccessController::getTopicsToModerate($dashBoardElement->configurations);
                        break;
                    case "user_registration_confirmation":
                        return QuickAccessController::getUsersRegistrationConfirmation($dashBoardElement->configurations);
                        break;
                    case "unread_messages":
                        return QuickAccessController::getUnreadMessages($dashBoardElement->configurations);
                        break;
                    default:

                        break;
                }
            }
        } catch (\Exception $e) {
            return response()->json(['error' => trans('privateDashBoardElements.failedToMakeRequestAccordingToDashBoardElement')],500);
        }
    }


    public function reorderUserDashBoardElements(Request $request)
    {
        try {
            if(isset($request['positions'])){
                CB::reorderUserDashBoardElements($request["positions"]);
            }
        } catch (Exception $e) {
            return response()->json(['error' => trans('privateDashBoardElements.failedToReorderUserDashBoardElements')]);
        }
    }
}
