<?php

namespace App\Http\Controllers;

use App\ComModules\Orchestrator;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\One\One;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Collection;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\UserRequest;
use App\Http\Requests\TagRequest;
use Datatables;
use Session;
use View;
use Breadcrumbs;

class TagsController extends Controller
{
    public function __construct()
    {
        View::share('private.tags', trans('tag.tag'));



    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        return view('private.tags.index');
    }

    /**
     * Create a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $entity_id = 1;
        return view('private.tags.tag', compact('entity_id'));
    }

    /**
     *Store a newly created resource in storage.
     *
     * @param TagRequest $request
     * @return $this|View
     */
    public function store(TagRequest $request)
    {
        try {
            $tag = Orchestrator::setTag($request->all());
            Session::flash('message', trans('tag.store_ok'));
            return redirect()->action('TagsController@show', $tag->id);

        }
        catch(Exception $e) {
            //TODO: save inputs
            return redirect()->back()->withErrors(["tag.store" => $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        try {
            $tag = Orchestrator::getTag($id);

            return view('private.tags.tag', compact('tag'));
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["tag.show" => $e->getMessage()]);
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
            $tag = Orchestrator::getTag($id);

            return view('private.tags.tag', compact('tag'));
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["tag.edit" => $e->getMessage()]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param TagRequest $request
     * @param $id
     * @return $this|View
     */
    public function update(TagRequest $request, $id)
    {
        try {
            $tag = Orchestrator::updateTag($id, $request->name, 1);
            Session::flash('message', trans('tag.update_ok'));
        }
        catch(Exception $e) {
            //TODO: save inputs
            return redirect()->back()->withErrors(["tag.update" => $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  $id
     * @return Response
     */
    public function destroy($id){

        try {
            Orchestrator::deleteTag($id);
            Session::flash('message', trans('tag.delete_ok'));
            return action('TagsController@index');
        }
        catch(Exception $e) {
            //TODO: save inputs
            return redirect()->back()->withErrors(["tag.destroy" => $e->getMessage()]);
        }
    }

    public function delete($id){
        $data = array();

        $data['action'] = action("TagsController@destroy", $id);
        $data['title'] = "DELETE";
        $data['msg'] = "Are you sure you want to delete this Tag?";
        $data['btn_ok'] = "Delete";
        $data['btn_ko'] = "Cancel";

        return view("_layouts.deleteModal", $data);
    }

    /**
     * Get the specified resource.
     *
     *
     */
    public function tableTags()
    {

        $manage = Orchestrator::listTag();

        // in case of json
        $tag = Collection::make($manage);

        return Datatables::of($tag)
            ->editColumn('name', function ($tag) {
                return "<a href='".action('TagsController@show', $tag->id)."'>".$tag->name."</a>";
            })
            ->addColumn('action', function ($tag) {
                return ONE::actionButtons($tag->id, ['show' => 'TagsController@show', 'delete' => 'TagsController@delete']);
            })
            ->make(true);
    }
}
