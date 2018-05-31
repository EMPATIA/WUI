<?php

namespace App\Http\Controllers;

use App\ComModules\Orchestrator;
use App\ComModules\Vote;
use App\Http\Requests\VoteMethodConfigRequest;
use App\One\One;
use App\Http\Requests;
use Exception;
use Illuminate\Support\Collection;
use App\Http\Requests\VoteMethodRequest;
use Datatables;
use Session;
use View;
use Breadcrumbs;

class VoteMethodConfigController extends Controller
{
    public function __construct()
    {
        View::share('title', trans('voteMethods.voteMethodConfig'));
        

    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        return view('private.voteMethods.index');
    }

    /**
     * Create a new resource.
     *
     * @return Response
     */
    public function create($methodId)
    {
        $languages = Orchestrator::getAllLanguages();
        $parameterType = ['boolean' => 'boolean', 'numeric' => 'numeric'];

        $voteMethodId = $methodId;

        $sidebar = 'votesMethods';
        $active = 'config';

        return view('private.voteMethods.methodConfiguration', compact('methodId','languages','parameterType', 'voteMethodId', 'sidebar', 'active'));
    }

    /**
     *Store a newly created resource in storage.
     *
     * @param VoteMethodRequest $request
     * @return $this|View
     */
    public function store(VoteMethodConfigRequest $request,$methodId)
    {
        try {
            $parameterType = $request->parameter_type;
            $code = $request->code;
            $languages = Orchestrator::getAllLanguages();
            $translations = [];
            foreach($languages as $language){
                if($request->input("name_".$language->code)) {
                    $translations[] = [
                        'language_code' => $language->code,
                        'name' => $request->input("name_" . $language->code),
                        'description' => $request->input("description_" . $language->code),
                    ];
                }
            }
            $config = Vote::SetVoteMethodConfig($methodId,$parameterType,$translations,$code);

            return redirect()->action('VoteMethodConfigController@show', ['methodId' =>  $methodId,'config' => $config->id]);

        }
        catch(Exception $e) {
            //TODO: save inputs
            return redirect()->back()->withErrors(["voteMethodConfig.store" => $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($methodId, $configId)
    {
        try {
            $config = Vote::getVoteMethodConfiguration($configId);
            $voteConfigId = $config->id;
            $voteMethodId = $methodId;

            $sidebar = 'votesMethods';
            $active = 'config';

            Session::put('sidebarArguments.voteMethodId', $voteMethodId);
            Session::put('sidebarArguments.activeSecondMenu', 'config');

            return view('private.voteMethods.methodConfiguration', compact('methodId','configId', 'config', 'voteConfigId', 'sidebar', 'active', 'voteMethodId'));
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["voteMethodConfig.show" => $e->getMessage()]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param $id
     * @return View
     */
    public function edit($methodId, $configId)
    {
        try {
            $config = Vote::getVoteMethodConfigurationEdit($configId);
            foreach ($config->translations as $translation){
                $configTranslation[$translation->language_code] = ['name' => $translation->name,'description' => $translation->description];
            }
            $parameterType = ['boolean' => 'boolean', 'numeric' => 'numeric'];
            $languages = Orchestrator::getAllLanguages();

            $voteConfigId = $config->id;
            $voteMethodId = $methodId;

            $sidebar = 'votesMethods';
            $active = 'config';

            Session::put('sidebarArguments.voteMethodId', $voteMethodId);
            Session::put('sidebarArguments.activeSecondMenu', 'config');

            return view('private.voteMethods.methodConfiguration', compact('methodId','configId', 'config', 'voteConfigId', 'sidebar', 'active', 'voteMethodId', 'parameterType', 'languages'));
            // return view('private.voteMethods.methodConfiguration', compact('methodId','config','parameterType','languages','configTranslation'));

        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["voteMethodConfig.edit" => $e->getMessage()]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param VoteMethodRequest $request
     * @param $id
     * @return $this|View
     */
    public function update(VoteMethodConfigRequest $request, $methodId, $configId)
    {
        try {

            $parameterType = $request->parameter_type;
            $code = $request->code;
            $languages = Orchestrator::getAllLanguages();
            $translations = [];
            foreach($languages as $language){
                if($request->input("name_".$language->code)) {
                    $translations[] = [
                        'language_code' => $language->code,
                        'name' => $request->input("name_" . $language->code),
                        'description' => $request->input("description_" . $language->code),
                    ];
                }
            }
            $config = Vote::updateVoteMethodConfig($methodId,$configId,$parameterType,$translations,$code);

            return redirect()->action('VoteMethodConfigController@show', ['methodId' =>  $methodId,'config' => $config->id]);


        }
        catch(Exception $e) {
            //TODO: save inputs
            return redirect()->back()->withErrors(["voteMethodConfig.update" => $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  $id
     * @return Response
     */
    public function destroy($methodId, $configId){

        try {
            Vote::deleteVoteMethodConfiguration($configId);

            Session::flash('message', trans('voteMethod.delete_ok'));
            return action('VoteMethodsController@show',$methodId);

        }
        catch(Exception $e) {
            //TODO: save inputs
            return redirect()->back()->withErrors(["voteMethodConfig.destroy" => $e->getMessage()]);
        }
    }

    public function delete($methodId, $configId){
        $data = array();

        $data['action'] = action("VoteMethodConfigController@destroy", ['methodId' => $methodId, 'configId' => $configId]);
        $data['title'] = "DELETE";
        $data['msg'] = "Are you sure you want to delete this Vote Method Configuration?";
        $data['btn_ok'] = "Delete";
        $data['btn_ko'] = "Cancel";

        return view("_layouts.deleteModal", $data);
    }

    /**
     * Get the specified resource.
     *
     *
     */
    public function tableConfigs($methodId)
    {
        $method = Vote::getVoteMethodWithConfigurations($methodId);
        $configs = $method->configurations;


        // in case of json
        $collection = Collection::make($configs);

        return Datatables::of($collection)
            ->editColumn('name', function ($collection) use($methodId) {
                return "<a href='".action('VoteMethodConfigController@show', ['methodId' => $methodId,'config' => $collection->id])."'>".$collection->name."</a>";
            })
            ->addColumn('action', function ($collection) use($methodId) {
                return ONE::actionButtons(['methodId' => $methodId,'config' => $collection->id], ['form'=> 'config','edit' => 'VoteMethodConfigController@edit', 'delete' => 'VoteMethodConfigController@delete']);
            })
            ->rawColumns(['name','action'])
            ->make(true);
    }
}
