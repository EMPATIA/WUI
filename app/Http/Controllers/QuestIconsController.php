<?php

namespace App\Http\Controllers;

use App\ComModules\Files;
use App\ComModules\Questionnaire;
use App\Http\Requests\QuestIconRequest;
use App\One\One;
use App\Http\Requests;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Datatables;
use Session;
use View;
use Breadcrumbs;

class QuestIconsController extends Controller
{

    public function __construct()
    {
        View::share('title', trans('privateQuestIcons.title'));

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('private.questIcons.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('private.questIcons.icon');
    }

    /**
     * Store a newly created resource in storage.
     *
     *
     * @return \Illuminate\Http\Response
     */
    public function store(QuestIconRequest $request)
    {
        try {
            $icon = Questionnaire::setNewIcon($request);
            Session::flash('message', trans('privateQuestIcons.store_ok'));
            return redirect()->action('QuestIconsController@show', $icon->icon_key);

        }
        catch(Exception $e) {
            return redirect()->back()->withErrors([ trans('privateQuestIcons.store_nok') => $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show($iconKey)
    {
        try {

            $icon = Questionnaire::getIcon($iconKey);
            

            return view('private.questIcons.icon', compact('icon'));
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors([ trans('privateQuestIcons.show') => $e->getMessage()]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($iconKey)
    {
        try {
            $icon = Questionnaire::getIcon($iconKey);

            return view('private.questIcons.icon', compact('icon'));
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors([ trans('privateQuestIcons.edit') => $e->getMessage()]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $iconKey)
    {
        try {
            $icon = Questionnaire::updateIcon($request,$iconKey);
            Session::flash('message', trans('privateQuestIcons.update_ok'));
            return redirect()->action('QuestIconsController@show', $iconKey);

        }
        catch(Exception $e) {
            //TODO: save inputs
            return redirect()->back()->withErrors([ trans('privateQuestIcons.update_nok') => $e->getMessage()]);
        }
    }


    /**
     * Show delete resource confirmation
     * Remove the specified resource from storage.
     * @param $layoutKey
     * @return View
     */
    public function delete($iconKey)
    {
        $data = array();

        $data['action'] = action("QuestIconsController@destroy", $iconKey);
        $data['title'] = "DELETE";
        $data['msg'] = "Are you sure you want to delete this Icon?";
        $data['btn_ok'] = "Delete";
        $data['btn_ko'] = "Cancel";

        return view("_layouts.deleteModal", $data);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($iconKey)
    {
        try {
            Questionnaire::deleteIcon($iconKey);
            Session::flash('message', trans('privateQuestIcons.delete_ok'));
            return action('QuestIconsController@index');

        }
        catch(Exception $e) {
            return redirect()->back()->withErrors([ trans('privateQuestIcons.delete_nok') => $e->getMessage()]);
        }
    }

    /**
     * Display a listing of the resource.
     * @return mixed
     * @throws Exception
     */
    public function getIndexTable()
    {

        $icons = Questionnaire::getIcons();
        // in case of json
        $collection = Collection::make($icons);

        return Datatables::of($collection)
            ->editColumn('name', function ($collection) {
                return "<a href='".action('QuestIconsController@show', $collection->icon_key)."'>".$collection->name."</a>";
            })
            ->addColumn('action', function ($collection) {
                return ONE::actionButtons($collection->icon_key, ['form' => 'questIcon','edit' => 'QuestIconsController@edit', 'delete' => 'QuestIconsController@delete']);
            })
            ->make(true);
    }


    /**
     * Get file info
     * @param Request $request
     * @return string
     */
    public function addIconImage(Request $request){
        try{
            $fileId = $request->file_id;
            $file = Files::getFile($fileId);


            return json_encode($file);
        }
        catch(Exception $e) {
            return "false";
        }
    }
    
}
