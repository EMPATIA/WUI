<?php

namespace App\Http\Controllers;

use App\ComModules\CB;
use App\ComModules\Orchestrator;
use App\Http\Requests\CbsConfigTypeRequest;
use Illuminate\Http\Request;
use App\One\One;
use Datatables;
use Session;
use View;
use Exception;
use Illuminate\Support\Collection;

class CbsConfigTypesController extends Controller
{
    public function __construct()
    {
        View::share('title', trans('cbConfigs.title'));

    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        return view('private.cbsConfigs.index');
    }


    /**
     * Create a new resource.
     *
     * @return Response
     */
    public function create()
    {
        try {
            $languages = Orchestrator::getAllLanguages();
            return view('private.cbsConfigs.configType', compact('languages'));

        } catch (Exception $e) {
            return redirect()->back()->withErrors(["CbConfigType.create" => $e->getMessage()]);
        }
    }


    /**
     *Store a newly created resource in storage.
     * @param CbsConfigTypeRequest $request
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function store(CbsConfigTypeRequest $request)
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
            $configType = CB::SetConfigType($code,$translation);
            Session::flash('message', trans('cbsConfigs.store_ok'));
            return redirect()->action('CbsConfigTypesController@show', ['id' => $configType->id]);

        }
        catch(Exception $e) {
            //TODO: save inputs
            return redirect()->back()->withErrors(["cbConfigType.store" => $e->getMessage()]);
        }
    }



    /**
     * Show the form for editing the specified resource.
     *
     * @param $id
     * @return View
     */
    public function edit($id)
    {
        try {
            $configType = CB::getCbConfigTypeEdit($id);
            foreach ($configType->translations as $translation){
                $configTypeTranslation[$translation->language_code] = ['title' => $translation->title,'description' => $translation->description];
            }
            
            $languages = Orchestrator::getAllLanguages();

            $configTypeId = $configType->id;

            $sidebar = 'cbs_configs';
            $active = 'details';

            Session::put('sidebarArguments', ['configTypeId' => $configTypeId, 'activeFirstMenu' => 'details']);

            return view('private.cbsConfigs.configType', compact('configType','languages','configTypeTranslation', 'configTypeId', 'sidebar', 'active'));

        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["cbConfigType.edit" => $e->getMessage()]);
        }
    }

    /**
     * Update the specified resource in storage.
     * @param CbsConfigTypeRequest $request
     * @param $id
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function update(CbsConfigTypeRequest $request, $id)
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
            $configType = CB::UpdateConfigType($id,$code,$translation);
            Session::flash('message', trans('cbsConfigs.update_ok'));
            return redirect()->action('CbsConfigTypesController@show', ['id' => $configType->id]);

        }
        catch(Exception $e) {
            //TODO: save inputs
            return redirect()->back()->withErrors(["cbConfigType.update" => $e->getMessage()]);
        }
    }


    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return $this|View
     */
    public function show($id)
    {
        try {
            $configType = CB::getCbConfigType($id);

            $configTypeId = $configType->id;

            $sidebar = 'cbs_configs';
            $active = 'details';

            Session::put('sidebarArguments', ['configTypeId' => $configTypeId, 'activeFirstMenu' => 'details']);

            return view('private.cbsConfigs.configType', compact('configType', 'configTypeId', 'sidebar', 'active'));
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["cbConfigType.show" => $e->getMessage()]);
        }
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  $id
     * @return $this|string
     */
    public function destroy($id)
    {

        try {
            CB::deleteConfigType($id);

            Session::flash('message', trans('cbsConfigs.delete_ok'));
            return action('CbsConfigTypesController@index');

        } catch (Exception $e) {
            //TODO: save inputs
            return redirect()->back()->withErrors(["cbConfigType.destroy" => $e->getMessage()]);
        }
    }


    /**
     * @param $id
     * @return View
     */
    public function delete($id){
        $data = array();
        $data['action'] = action("CbsConfigTypesController@destroy", $id);
        $data['title'] = "DELETE";
        $data['msg'] = "Are you sure you want to delete this Config type?";
        $data['btn_ok'] = "Delete";
        $data['btn_ko'] = "Cancel";

        return view("_layouts.deleteModal", $data);
    }


    /**
     * @return $this
     */
    public function getIndexTable(){
        try {
            $configTypes = CB::getCbConfigTypes();
            // in case of json
            $collection = Collection::make($configTypes);
            return Datatables::of($collection)
                ->editColumn('title', function ($collection){
                    return "<a href='" . action('CbsConfigTypesController@show', ['id'=>$collection->id]) . "'>" . $collection->title . "</a>";
                })
                ->addColumn('action', function ($collection){
                    return ONE::actionButtons(['id' => $collection->id], ['form' => 'cbConfigType','edit' => 'CbsConfigTypesController@edit', 'delete' => 'CbsConfigTypesController@delete']);
                })
                ->make(true);

        } catch (Exception $e) {
            //TODO: save inputs
            return redirect()->back()->withErrors(["CbConfigTypes.getIndexTable" => $e->getMessage()]);
        }
    }

    public function showConfigurations($id)
    {
        $configType = CB::getCbConfigType($id);

        $configTypeId = $configType->id;

        $sidebar = 'cbs_configs';
        $active = 'config';

        Session::put('sidebarArguments', ['configTypeId' => $configTypeId, 'activeFirstMenu' => 'config']);

        return view('private.cbsConfigs.configurations', compact('configType', 'configTypeId', 'sidebar', 'active'));
    }

}
