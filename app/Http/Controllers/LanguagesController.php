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
use App\Http\Requests\LanguageRequest;
use Datatables;
use Session;
use View;
use Breadcrumbs;

class LanguagesController extends Controller
{
    public function __construct()
    {
        View::share('private.languages', trans('language.language'));



    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $title = trans('language.languages');
        return view('private.languages.index', compact('title'));
    }

    /**
     * Create a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $title = trans('privateLanguages.create_language');
        return view('private.languages.language', compact('title'));
    }

    /**
     *Store a newly created resource in storage.
     *
     * @param LanguageRequest $request
     * @return $this|View
     */
    public function store(LanguageRequest $request)
    {
        try {
            $language = Orchestrator::storeLanguage($request->all());
            Session::flash('message', trans('privateLanguages.store_ok'));
            return redirect()->action('LanguagesController@show', $language->id);
        }
        catch(Exception $e) {
            //TODO: save inputs
            return redirect()->back()->withErrors(["language.store" => $e->getMessage()]);
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
            $language = Orchestrator::getLanguage($id);
            $title = trans('privateLanguages.show_language').' '.(isset($language->name) ? $language->name: null);

            $entityKey = One::getEntityKey();
            $sidebar = !empty($entityKey) ? 'entity' : null;
            $active = 'languages';

            Session::put('sidebarArguments', ['activeFirstMenu' => 'languages']);

            if(!empty($entityKey))
                return view('private.languages.language', compact('title','language', 'sidebar', 'active','entityKey'));

            else
                return view('private.languages.language', compact('title','language', 'sidebar', 'active'));
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["language.show" => $e->getMessage()]);
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
            $language = Orchestrator::getLanguage($id);

            $title = trans('privateLanguages.edit_language').' '.(isset($language->name) ? $language->name: null);
            
            $entityKey = One::getEntityKey();
            $sidebar = !empty($entityKey) ? 'entity' : null;
            $active = 'languages';

            Session::put('sidebarArguments', ['activeFirstMenu' => 'languages']);

            if(!empty($entityKey))
                return view('private.languages.language', compact('title','language', 'sidebar', 'active','entityKey'));

            else
                return view('private.languages.language', compact('title', 'language', 'sidebar', 'active'));
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["language.edit" => $e->getMessage()]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param LanguageRequest $request
     * @param $id
     * @return $this|View
     */
    public function update(LanguageRequest $request, $id)
    {
        try {
            $language = Orchestrator::updateLanguage($request->all(), $id);
            Session::flash('message', trans('privateLanguages.update_ok'));
            return redirect()->action('LanguagesController@show', $language->id);
        }
        catch(Exception $e) {
            //TODO: save inputs
            return redirect()->back()->withErrors(["language.update" => $e->getMessage()]);
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
            Orchestrator::deleteLang($id);
            Session::flash('message', trans('privateLanguages.delete_ok'));
        }
        catch(Exception $e) {
            //TODO: save inputs
            return redirect()->back()->withErrors(["language.destroy" => $e->getMessage()]);
        }
    }

    /**
     * Get the specified resource.
     *
     *
     */
    public function tableLanguages()
    {
        $manage = Orchestrator::getAllLanguages();

        // in case of json
        $language = Collection::make($manage);

        return Datatables::of($language)
            ->editColumn('name', function ($language) {
                return "<a href='".action('LanguagesController@show', $language->id)."'>".$language->name."</a>";
            })
            ->addColumn('action', function ($language) {
                return ONE::actionButtons($language->id, ['edit' => 'LanguagesController@edit', 'delete' => 'LanguagesController@delete', 'form' => 'languages']);
            })
            ->rawColumns(['name','action'])
            ->make(true);
    }

    public function delete($id){
        $data = array();

        $data['action'] = action("LanguagesController@destroy", $id);
        $data['title'] = "DELETE";
        $data['msg'] = "Are you sure you want to delete this Language?";
        $data['btn_ok'] = "Delete";
        $data['btn_ko'] = "Cancel";

        return view("_layouts.deleteModal", $data);
    }
}
