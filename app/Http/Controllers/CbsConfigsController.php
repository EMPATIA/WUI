<?php

namespace App\Http\Controllers;

use App\ComModules\CB;
use App\ComModules\Orchestrator;
use App\Http\Requests\CbsConfigRequest;
use Illuminate\Http\Request;
use App\One\One;
use Datatables;
use Session;
use View;
use Exception;
use Illuminate\Support\Collection;

class CbsConfigsController extends Controller
{
    public function __construct()
    {
        View::share('title', trans('privateCbsConfigs.title'));

    }


    /**
     *
     */
    public function index()
    {
        
    }


    /**
     * @param $configTypeId
     * @return $this|View
     */
    public function create($configTypeId)
    {
        try {
            $languages = Orchestrator::getAllLanguages();

            $sidebar = 'cbs_configs';
            $active = 'config';

            Session::put('sidebarArguments', ['configTypeId' => $configTypeId, 'activeFirstMenu' => 'config']);
            
            return view('private.cbsConfigs.config', compact('languages','configTypeId', 'sidebar', 'active'));

        } catch (Exception $e) {
            return redirect()->back()->withErrors(["cbsConfig.create" => $e->getMessage()]);
        }
    }


    /**
     * @param CbsConfigRequest $request
     * @param $configTypeId
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function store(CbsConfigRequest $request, $configTypeId)
    {
        try {
            $code = $request->code;
            $languages = Orchestrator::getAllLanguages();
            $translation = [];
            foreach($languages as $language){

                if($request->input("title_".$language->code)) {
                    $translation[] = [
                        'language_code' => $language->code,
                        'title' => $request->input("title_" . $language->code),
                        'description' => $request->input("description_" . $language->code),
                    ];
                }
            }
            $config = CB::SetCbsConfig($configTypeId,$code,$translation);
            Session::flash('message', trans('privateCbsConfigs.store_ok'));
            return redirect()->action('CbsConfigsController@show', ['configTypeId' => $configTypeId,'id' => $config->id]);

        }
        catch(Exception $e) {
            //TODO: save inputs
            return redirect()->back()->withErrors(["cbsConfig.store" => $e->getMessage()]);
        }
    }


    /**
     * Show the form for editing the specified resource.
     * @param $configTypeId
     * @param $id
     * @return $this|View
     */
    public function edit($configTypeId,$id)
    {
        try {
            $config = CB::getCbsConfigEdit($id);
            $configId = $config->id;

            foreach ($config->translations as $translation){
                $configTranslation[$translation->language_code] = ['title' => $translation->title,'description' => $translation->description];
            }

            $languages = Orchestrator::getAllLanguages();

            $configId = $config->id;

            $sidebar = 'cbs_configs';
            $active = 'config';

            Session::put('sidebarArguments', ['configTypeId' => $configTypeId, 'activeFirstMenu' => 'config']);

            return view('private.cbsConfigs.config', compact('config', 'configId', 'languages','configTranslation','configTypeId', 'sidebar', 'active'));

        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["cbsConfig.edit" => $e->getMessage()]);
        }
    }

    /**
     * Update the specified resource in storage.
     * @param CbsConfigRequest $request
     * @param $configTypeId
     * @param $id
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function update(CbsConfigRequest $request,$configTypeId, $id)
    {
        try {
            $code = $request->code;
            $languages = Orchestrator::getAllLanguages();
            $translation = [];
            foreach($languages as $language){

                if($request->input("title_".$language->code)) {
                    $translation[] = [
                        'language_code' => $language->code,
                        'title' => $request->input("title_" . $language->code),
                        'description' => $request->input("description_" . $language->code),
                    ];
                }
            }
            $config = CB::UpdateCbsConfig($configTypeId,$id,$code,$translation);
            Session::flash('message', trans('privateCbsConfigs.update_ok'));
            return redirect()->action('CbsConfigsController@show', ['configTypeId' => $configTypeId,'id' => $config->id]);

        }
        catch(Exception $e) {
            //TODO: save inputs
            return redirect()->back()->withErrors(["cbsConfig.update" => $e->getMessage()]);
        }
    }


    /**
     * Display the specified resource.
     * @param $configTypeId
     * @param $id
     * @return $this|View
     */
    public function show($configTypeId,$id)
    {
        try {
            $config = CB::getCbsConfig($id);
            $configId = $config->id;

            $sidebar = 'cbs_configs';
            $active = 'config';

            Session::put('sidebarArguments', ['configTypeId' => $configTypeId, 'activeFirstMenu' => 'config']);
            return view('private.cbsConfigs.config', compact('config', 'configId', 'configTypeId', 'conf', 'sidebar', 'active'));
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["cbsConfig.show" => $e->getMessage()]);
        }
    }


    /**
     * Remove the specified resource from storage.
     * @param $configTypeId
     * @param $id
     * @return $this|string
     */
    public function destroy($configTypeId,$id)
    {

        try {
            CB::deleteCbsConfig($id);

            Session::flash('message', trans('privateCbsConfigs.delete_ok'));
            return action('CbsConfigTypesController@show',$configTypeId);

        } catch (Exception $e) {
            //TODO: save inputs
            return redirect()->back()->withErrors(["cbsConfigs.destroy" => $e->getMessage()]);
        }
    }


    /**
     * @param $configTypeId
     * @param $id
     * @return View
     */
    public function delete($configTypeId, $id){
        $data = array();
        $data['action'] = action("CbsConfigsController@destroy", ['configTypeId' => $configTypeId, 'id' => $id]);
        $data['title'] = "DELETE";
        $data['msg'] = "Are you sure you want to delete this Configuration?";
        $data['btn_ok'] = "Delete";
        $data['btn_ko'] = "Cancel";

        return view("_layouts.deleteModal", $data);
    }


    /**
     * @param $configTypeId
     * @return $this
     */
    public function getIndexTable($configTypeId){
        try {
            $configs = CB::getCbsConfigsByType($configTypeId);
            // in case of json
            $collection = Collection::make($configs);
            return Datatables::of($collection)
                ->editColumn('title', function ($collection) use ($configTypeId){
                    return "<a href='" . action('CbsConfigsController@show', ['configTypeId' => $configTypeId, 'id' => $collection->id]) . "'>" . $collection->title . "</a>";
                })
                ->addColumn('action', function ($collection) use ($configTypeId){
                    return ONE::actionButtons(['configTypeId' => $configTypeId,'id' => $collection->id], ['form' => 'config','edit' => 'CbsConfigsController@edit', 'delete' => 'CbsConfigsController@delete']);
                })
                ->rawColumns(['title','action'])
                ->make(true);

        } catch (Exception $e) {
            //TODO: save inputs
            return redirect()->back()->withErrors(["CbsConfigs.getIndexTable" => $e->getMessage()]);
        }
    }
}
