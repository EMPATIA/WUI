<?php

namespace App\Http\Controllers;

use App\ComModules\CM;
use App\ComModules\Orchestrator;
use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Datatables;
use Session;
use View;
use Illuminate\Support\Collection;
use App\One\One;
use Cache;

class BEMenuController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(){
        try {
            $title = trans('privateBEMenu.list_title');
            $menuData = CM::getEntityBEMenu();

            return view('private.beMenu.index', compact('title','menuData'));
        } catch (Exception $e) {
            return redirect()->back()->withErrors([trans('beMenu.index') => $e->getMessage()]);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        try {
            $menuElements = CM::getBEMenuElements($request)->beMenuElements ?? [];
            $languages = Orchestrator::getLanguageList();

            return view('private.beMenu.beMenu', compact('menuElements','languages'));
        } catch (Exception $e) {
            return redirect()->back()->withErrors([ trans('beMenuElements.create') => $e->getMessage()]);
        }
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

            $storeData = [
                "element" => $request->get("menuElement"),
                "parameters" => $request->get("parameters",[])
            ];
            foreach($languages as $language){
                if($request->input("name_" . $language->code) && !empty($request->input("name_" . $language->code))) {
                    $storeData["translations"][] = [
                        'language_code' => $language->code,
                        'name' => $request->input("name_" . $language->code) ?? null
                    ];
                }
            }

            CM::createEntityBEMenuElement($storeData);
            $this->deleteMenuCache();
            Session::flash('message', trans('beMenuElements.store_ok'));

            return redirect()->action('BEMenuController@index');
        } catch (Exception $e){
            return redirect()->back()->withErrors([ trans('beMenuElements.store') => $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($key)
    {
        try {
            $element = CM::getEntityBEMenuElement($key);
            $languages = Orchestrator::getLanguageList();

            return view('private.beMenu.beMenu', compact('element','languages'));
        } catch(Exception $e) {
            return redirect()->back()->withErrors(["beMenuElements.show" => $e->getMessage()])->getTargetUrl();
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($key)
    {
        try {
            $element = CM::getEntityBEMenuElement($key);
            $languages = Orchestrator::getLanguageList();

            return view('private.beMenu.beMenu', compact('element','languages'));
        } catch(Exception $e) {
            return redirect()->back()->withErrors(["beMenuElements.edit" => $e->getMessage()])->getTargetUrl();
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $key) {
        try {
            $languages = Orchestrator::getLanguageList();

            $storeData = [
                "parameters" => $request->get("parameters",[])
            ];
            foreach($languages as $language){
                if($request->input("name_" . $language->code) && !empty($request->input("name_" . $language->code))) {
                    $storeData["translations"][] = [
                        'language_code' => $language->code,
                        'name' => $request->input("name_" . $language->code) ?? null
                    ];
                }
            }

            CM::updateEntityBEMenuElements($key,$storeData);
            Session::flash('message', trans('beMenuElements.update_ok'));
            $this->deleteMenuCache();

            return redirect()->action('BEMenuController@index');
        } catch (Exception $e){
            return redirect()->back()->withErrors([ trans('beMenuElements.update') => $e->getMessage()]);
        }
    }

    public function delete($key)
    {
        $data = array();

        $data['action'] = action("BEMenuController@destroy", $key);
        $data['title'] = trans('privateBEMenuElements.delete');
        $data['msg'] = trans('privateBEMenuElements.are_you_sure_you_want_to_delete');
        $data['btn_ok'] = trans('privateBEMenuElements.delete');
        $data['btn_ko'] = trans('privateBEMenuElements.cancel');

        return view("_layouts.deleteModal", $data);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $key
     * @return \Illuminate\Http\Response
     */
    public function destroy($key)
    {
        try {
            CM::deleteEntityBEMenuElements($key);
            $this->deleteMenuCache();
            Session::flash('message', trans('privateBEMenuElements.delete_ok'));

            return action('BEMenuController@index');
        } catch(Exception $e) {
            return back()->withErrors([ trans('privateBEMenuElements.delete_error') => $e->getMessage()])->getTargetUrl();
        }
    }

    public function getElementParameters(Request $request) {
        try {
            $menuElement = $request->get("menuElement","");
            if (!empty($menuElement)){
                $parameters = CM::getBEMenuElement($menuElement)->parameters;

                return view("private.beMenu.menuElementParameter",compact('parameters'));
            } else {
                return response()->json(["failed" => true],400);
            }
        } catch(Exception $e) {
            return response()->json(["failed" => true,'e'=>$e],500);
        }
    }

    public function updateOrder(Request $request) {
        try {
            $source = $request->source;  //id do menu que estamos a arrastar
            $destination = $request->destination;  //id do menu pai
            $ordering = json_decode($request->order);  //ordem nova dentro do submenu
            $rootOrdering = json_decode($request->rootOrder); //ordem nova caso tenha ido para o root (sem pai)

            CM::reorderEntityBEMenu($source, $destination, $rootOrdering, $ordering);
            $this->deleteMenuCache();

            return response()->json(["success"=>true],200);
        } catch (Exception $e) {
            return response()->json(["success"=>false],500);
        }
    }

    public function import() {
        try {
            CM::importDefaultMenu();
            $this->deleteMenuCache();
            return redirect()->back();
        } catch (Exception $e){
            return redirect()->back()->withErrors(["BEMenu.import" => $e->getMessage()]);
        }
    }

    private function deleteMenuCache($entityKey = false) {
        if (empty($entityKey))
            $entityKey = ONE::getEntityKey();

        Cache::forget("BEMENU-" . $entityKey);
    }
}