<?php

namespace App\Http\Controllers;

use App\ComModules\CM;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\One\One;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Collection;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\UserRequest;
use App\Http\Requests\TextRequest;
use Datatables;
use Session;
use View;
use Breadcrumbs;

class TextsController extends Controller
{
    public function __construct()
    {
        View::share('private.texts', trans('text.text'));


    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        return view('private.texts.index');
    }

    /**
     * Create a new resource.
     *
     * @return Response
     */
    public function create()
    {
        return view('private.texts.text');
    }

    /**
     *Store a newly created resource in storage.
     *
     * @param TextRequest $request
     * @return $this|View
     */
    public function store(TextRequest $request)
    {
        try {
            $text = CM::setText($request->all());
            Session::flash('message', trans('text.store_ok'));
            return redirect()->action('TextsController@show', $text->text_key);
        }
        catch(Exception $e) {
            //TODO: save inputs
            return redirect()->back()->withErrors(["text.store" => $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $text_key
     * @return Response
     */
    public function show($text_key)
    {
        try {
            $text = CM::getText($text_key);

            return view('private.texts.text', compact('text'));
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["text.show" => $e->getMessage()]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param $text_key
     * @return View
     */
    public function edit($text_key)
    {
        try {
            $text = CM::getText($text_key);

            return view('private.texts.text', compact('text'));
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["text.edit" => $e->getMessage()]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param TextRequest $request
     * @param $text_key
     * @return $this|View
     */
    public function update(TextRequest $request, $text_key)
    {

        try {
            $text = CM::updateText($text_key, $request->all());
            Session::flash('message', trans('text.update_ok'));
            return redirect()->action('TextsController@show', $text->text_key);
        }
        catch(Exception $e) {
            //TODO: save inputs
            return redirect()->back()->withErrors(["text.update" => $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  $text_key
     * @return Response
     */
    public function destroy($text_key){

        try {
            CM::deleteText($text_key);
            Session::flash('message', trans('text.delete_ok'));
            return action('TextsController@index');
        }
        catch(Exception $e) {
            //TODO: save inputs
            return redirect()->back()->withErrors(["text.destroy" => $e->getMessage()]);
        }
    }

    public function delete($id){
        $data = array();

        $data['action'] = action("TextsController@destroy", $id);
        $data['title'] = "DELETE";
        $data['msg'] = "Are you sure you want to delete this Text?";
        $data['btn_ok'] = "Delete";
        $data['btn_ko'] = "Cancel";

        return view("_layouts.deleteModal", $data);
    }

    /**
     * Get the specified resource.
     *
     *
     */
    public function tableTexts()
    {
        $manage = CM::listText();

        // in case of json
        $text = Collection::make($manage);

        return Datatables::of($text)
            ->editColumn('title', function ($text) {
                return "<a href='".action('TextsController@show', $text->text_key)."'>".$text->title."</a>";
            })
            ->addColumn('action', function ($text) {
                return ONE::actionButtons($text->text_key, ['edit' => 'TextsController@edit', 'delete' => 'TextsController@delete']);
            })
            ->make(true);
    }
}
