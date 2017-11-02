<?php

namespace App\Http\Controllers;

use App\ComModules\CB;
use App\ComModules\Orchestrator;
use App\Http\Requests\LayoutRequest;
use App\Http\Requests\VotesConfigRequest;
use App\One\One;
use App\Http\Requests;
use Exception;
use Illuminate\Support\Collection;
use Datatables;
use Session;
use View;
use Breadcrumbs;

class VotesConfigsController extends Controller
{

    public function __construct()
    {
        View::share('title', trans('privateVoteConfigs.title'));


    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $sidebar = 'votes';
        $active = 'config';

        Session::put('sidebarArguments', ['activeFirstMenu' => 'config']);
        return view('private.voteConfigs.index', compact('sidebar', 'active'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $languages = Orchestrator::getAllLanguages();

        $sidebar = 'votes';
        $active = 'config';

        Session::put('sidebarArguments', ['activeFirstMenu' => 'config']);

        return view('private.voteConfigs.voteConfig', compact('languages', 'sidebar', 'active'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param VotesConfigRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(VotesConfigRequest $request)
    {

        try {
            $code = $request->code;
            $languages = Orchestrator::getAllLanguages();
            $translation = [];
            foreach($languages as $language){

                if($request->input("name_".$language->code)) {
                    $translation[] = [
                        'language_code' => $language->code,
                        'name' => $request->input("name_" . $language->code),
                        'description' => $request->input("description_" . $language->code),
                    ];
                }
            }
            $configuration = CB::setVoteConfiguration($code,$translation);
            Session::flash('message', trans('privateVotesConfigs.store_ok'));
            return redirect()->action('VotesConfigsController@show', $configuration->vote_configuration_key);

        }
        catch(Exception $e) {
            //TODO: save inputs
            return redirect()->back()->withErrors(["privateVotesConfigs.store_nok" => $e->getMessage()]);
        }

    }

    /**
     * Display the specified resource.
     *
     * @param $configKey
     * @return \Illuminate\Http\Response
     */
    public function show($configKey)
    {
        try {

            $voteConfig = CB::getVoteConfiguration($configKey);
            $title = trans('privateVoteConfigs.show_vote_configuration');

            $sidebar = 'votes';
            $active = 'config';

            Session::put('sidebarArguments', ['activeFirstMenu' => 'config']);

            return view('private.voteConfigs.voteConfig', compact('voteConfig', 'title', 'sidebar', 'active'));
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["privateVotesConfigs.show" => $e->getMessage()]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param $configKey
     * @return \Illuminate\Http\Response
     */
    public function edit($configKey)
    {
        try {
            $voteConfig = CB::getVoteConfigurationEdit($configKey);
            foreach ($voteConfig->translations as $voteTrans){
                $translation[$voteTrans->language_code] = ['name' => $voteTrans->name,'description' => $voteTrans->description];
            }
            $languages = Orchestrator::getAllLanguages();
            $title = trans('privateVoteConfigs.edit_vote_configuration');

            $sidebar = 'votes';
            $active = 'config';

            Session::put('sidebarArguments', ['activeFirstMenu' => 'config']);

            return view('private.voteConfigs.voteConfig', compact('voteConfig','translation', 'languages', 'title', 'sidebar', 'active'));
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["privateVotesConfigs.edit" => $e->getMessage()]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param VotesConfigRequest $request
     * @param $configKey
     * @return \Illuminate\Http\Response
     */
    public function update(VotesConfigRequest $request, $configKey)
    {
        try {
            $code = $request->code;
            $languages = Orchestrator::getAllLanguages();
            $translation = [];
            foreach($languages as $language){

                if($request->input("name_".$language->code)) {
                    $translation[] = [
                        'language_code' => $language->code,
                        'name' => $request->input("name_" . $language->code),
                        'description' => $request->input("description_" . $language->code),
                    ];
                }
            }
            $configuration = CB::updateVoteConfiguration($configKey,$code,$translation);
            Session::flash('message', trans('privateVotesConfigs.update_ok'));
            return redirect()->action('VotesConfigsController@show', $configKey);

        }
        catch(Exception $e) {
            //TODO: save inputs
            return redirect()->back()->withErrors(["privateVotesConfigs.update_nok" => $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $configKey
     * @return \Illuminate\Http\Response
     */
    public function destroy($configKey)
    {
        try {
            CB::deleteVoteConfig($configKey);

            Session::flash('message', trans('privateVotesConfigs.destroy_ok'));
            return action('VotesConfigsController@index');

        }
        catch(Exception $e) {
            //TODO: save inputs
            return redirect()->back()->withErrors(["privateVotesConfigs.destroy_nok" => $e->getMessage()]);
        }
    }

    /**
     * Show delete resource confirmation
     * @param $configKey
     * @return View
     */
    public function delete($configKey)
    {
        $data = array();

        $data['action'] = action("VotesConfigsController@destroy", $configKey);
        $data['title'] = "DELETE";
        $data['msg'] = "Are you sure you want to delete this Config?";
        $data['btn_ok'] = "Delete";
        $data['btn_ko'] = "Cancel";

        return view("_layouts.deleteModal", $data);
    }


    /**
     * Display a listing of the resource.
     * @return mixed
     * @throws Exception
     */
    public function getIndexTable()
    {

        $configs = CB::getVotesConfigurations();
        // in case of json
        $collection = Collection::make($configs);

        return Datatables::of($collection)
            ->editColumn('name', function ($collection) {
                return "<a href='".action('VotesConfigsController@show', $collection->vote_configuration_key)."'>".$collection->name."</a>";
            })
            ->addColumn('action', function ($collection) {
                return ONE::actionButtons($collection->vote_configuration_key, ['form'=> 'voteConfig','edit' => 'VotesConfigsController@edit', 'delete' => 'VotesConfigsController@delete']);
            })
            ->make(true);
    }
    
}
