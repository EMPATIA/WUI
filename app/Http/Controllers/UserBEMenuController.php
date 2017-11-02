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

class UserBEMenuController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($userKey = null){
        try {
            $data = [];
            if (empty($userKey)) {
                $currentUser = true;
                $userKey = One::getUserKey();
            } else {
                $currentUser = false;

                $data['sidebar'] = 'manager';
                $data['active'] = 'personal_dynamic_be_menu';
                $data['role'] = 'manager';
            }

            $title = trans('privateBEMenu.list_title');
            $menuData = CM::getEntityBEMenu($userKey);

            $data["title"] = $title;
            $data["menuData"] = $menuData;
            $data["userKey"] = $userKey;
            $data["currentUser"] = $currentUser;
            
            return view('private.beMenu.index', $data);
        } catch (Exception $e) {
            return redirect()->back()->withErrors([trans('beMenu.index') => $e->getMessage()]);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request, $userKey = null) {
        try {
            $data = [];
            if (empty($userKey)) {
                $currentUser = true;
                $userKey = One::getUserKey();
            } else {
                $currentUser = false;

                $data['sidebar'] = 'manager';
                $data['active'] = 'personal_dynamic_be_menu';
                $data['role'] = 'manager';
            }

            $menuElements = CM::getBEMenuElements($request)->beMenuElements ?? [];
            $languages = Orchestrator::getLanguageList();

            $data["menuElements"] = $menuElements;
            $data["languages"] = $languages;
            $data["userKey"] = $userKey;
            $data["currentUser"] = $currentUser;
            
            return view('private.beMenu.beMenu', $data);
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
    public function store(Request $request, $userKey = null)
    {
        try {
            if (empty($userKey)) {
                $currentUser = true;
                $userKey = One::getUserKey();
            } else
                $currentUser = false;

            $languages = Orchestrator::getLanguageList();

            $storeData = [
                "element" => $request->get("menuElement"),
                "parameters" => $request->get("parameters",[]),
                "userKey" => $userKey
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
            $this->deleteMenuCache($userKey);
            Session::flash('message', trans('beMenuElements.store_ok'));

            if(!$currentUser)
                return redirect()->action('UserBEMenuController@userIndex',["userKey" => $userKey]);
            else
                return redirect()->action('UserBEMenuController@index');
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
    public function show($key, $userKey = null)
    {
        try {
            $data = [];
            if (empty($userKey)) {
                $currentUser = true;
                $userKey = One::getUserKey();
            } else {
                $currentUser = false;

                $data['sidebar'] = 'manager';
                $data['active'] = 'personal_dynamic_be_menu';
                $data['role'] = 'manager';
            }

            $element = CM::getEntityBEMenuElement($key, $userKey);
            $languages = Orchestrator::getLanguageList();

            $data["element"] = $element;
            $data["languages"] = $languages;
            $data["userKey"] = $userKey;        
            $data["currentUser"] = $currentUser;    

            return view('private.beMenu.beMenu', $data);
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
    public function edit($key, $userKey = null)
    {
        try {
            $data = [];
            if (empty($userKey)) {
                $currentUser = true;
                $userKey = One::getUserKey();
            } else {
                $currentUser = false;

                $data['sidebar'] = 'manager';
                $data['active'] = 'personal_dynamic_be_menu';
                $data['role'] = 'manager';
            }

            $element = CM::getEntityBEMenuElement($key, $userKey);
            $languages = Orchestrator::getLanguageList();

            $data["element"] = $element;
            $data["languages"] = $languages;
            $data["userKey"] = $userKey;
            $data["currentUser"] = $currentUser;

            return view('private.beMenu.beMenu', $data);
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
    public function update(Request $request, $key, $userKey = null) {
        try {
            if (empty($userKey)) {
                $currentUser = true;
                $userKey = One::getUserKey();
            } else
                $currentUser = false;

            $languages = Orchestrator::getLanguageList();

            $storeData = [
                "parameters" => $request->get("parameters",[]),
                "userKey" => $userKey
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
            $this->deleteMenuCache($userKey);

            if(!$currentUser)
                return redirect()->action('UserBEMenuController@userIndex',["userKey" => $userKey]);
            else
                return redirect()->action('UserBEMenuController@index');
        } catch (Exception $e){
            return redirect()->back()->withErrors([ trans('beMenuElements.update') => $e->getMessage()]);
        }
    }

    public function delete($key, $userKey = null)
    {
        $data = array();

        if(!empty($userKey))
            $data['action'] = action("UserBEMenuController@userDestroy", ["userKey" => $userKey, "menuKey" => $key]);
        else
            $data['action'] = action("UserBEMenuController@destroy", $key);

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
    public function destroy($key, $userKey = null)
    {
        try {
            if (empty($userKey)) {
                $currentUser = true;
                $userKey = One::getUserKey();
            } else
                $currentUser = false;

            CM::deleteEntityBEMenuElements($key,$userKey);
            $this->deleteMenuCache($userKey);
            Session::flash('message', trans('privateBEMenuElements.delete_ok'));

            if(!$currentUser)
                return action('UserBEMenuController@userIndex',["userKey" => $userKey]);
            else
                return action('UserBEMenuController@index');
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

    public function updateOrder(Request $request, $userKey = null) {
        try {
            if (empty($userKey))
                $userKey = One::getUserKey();

            $source = $request->source;  //id do menu que estamos a arrastar
            $destination = $request->destination;  //id do menu pai
            $ordering = json_decode($request->order);  //ordem nova dentro do submenu
            $rootOrdering = json_decode($request->rootOrder); //ordem nova caso tenha ido para o root (sem pai)

            CM::reorderEntityBEMenu($source, $destination, $rootOrdering, $ordering, $userKey);
            $this->deleteMenuCache($userKey);

            return response()->json(["success"=>true],200);
        } catch (Exception $e) {
            return response()->json(["success"=>false],500);
        }
    }

    public function import($userKey = null) {
        try {
            if (empty($userKey))
                $userKey = One::getUserKey();

            CM::importDefaultMenu($userKey);
            $this->deleteMenuCache($userKey);
            return redirect()->back();
        } catch (Exception $e){
            return redirect()->back()->withErrors(["BEMenu.import" => $e->getMessage()]);
        }
    }

    private function deleteMenuCache($userKey) {
        if ($userKey==One::getUserKey())
            Session::put("BEMENU","update");
    }


    /* User Menu Routes */
    public function userIndex($userKey) {
        return $this->index($userKey);
    }
    public function userCreate(Request $request, $userKey) {
        return $this->create($request, $userKey);
    }
    public function userStore(Request $request, $userKey) {
        return $this->store($request, $userKey);
    }
    public function userShow($userKey, $key) {
        return $this->show($key, $userKey);
    }
    public function userEdit($userKey, $key) {
        return $this->edit($key, $userKey);
    }
    public function userUpdate(Request $request, $userKey, $key) {
        return $this->update($request, $key, $userKey);
    }
    public function userDelete($userKey, $key) {
        return $this->delete($key,$userKey);
    }
    public function userDestroy($userKey, $key) {
        return $this->destroy($key,$userKey);
    }
    public function userUpdateOrder(Request $request, $userKey) {
        return $this->updateOrder($request, $userKey);
    }
    public function userImport($userKey) {
        return $this->import($userKey);
    }
}