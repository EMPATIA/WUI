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
use App\Http\Requests\CategoryRequest;
use Datatables;
use Session;
use View;
use Breadcrumbs;

class CategoriesController extends Controller
{
    public function __construct()
    {
        View::share('private.categories', trans('category.category'));


    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $title = trans('privateCategories.list_categories');
        return view('private.categories.index', compact('title'));
    }

    /**
     * Create a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $title = trans('privateCategories.create_category');
        return view('private.categories.category', compact('title'));
    }

    /**
     *Store a newly created resource in storage.
     *
     * @param CategoryRequest|Request $request
     * @return $this|View
     */
    public function store(CategoryRequest $request)
    {
        try {

            $category = Orchestrator::storeCategory($request);
            Session::flash('message', trans('category.store_ok'));
            return redirect()->action('CategoriesController@show', $category->category_key);

        }
        catch(Exception $e) {
            //TODO: save inputs
            return redirect()->back()->withErrors(["category.store" => $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param $key
     * @return Response
     * @internal param int $id
     */
    public function show($key)
    {
        try {
            $category = Orchestrator::getCategory($key);

            $title = trans('privateCategories.show_category').' '.(isset($category->name) ? $category->name : null);
            return view('private.categories.category', compact('title', 'category'));
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["category.show" => $e->getMessage()]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param $id
     * @return View
     */
    public function edit($key)
    {
        try {
            $category = Orchestrator::getCategory($key);

            $title = trans('privateCategories.update_category').' '.(isset($category->name) ? $category->name : null);
            return view('private.categories.category', compact('title', 'category'));

        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["category.edit" => $e->getMessage()]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param $id
     * @return $this|View
     */
    public function update(CategoryRequest $request, $key)
    {
        try {

            $category = Orchestrator::updateCategory($request, $key);
            Session::flash('message', trans('category.update_ok'));
            return redirect()->action('CategoriesController@show', $category->category_key);

        }
        catch(Exception $e) {
            //TODO: save inputs
            return redirect()->back()->withErrors(["category.update" => $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $key
     * @return Response
     * @internal param $id
     */
    public function destroy($key)
    {
        try {
            Orchestrator::deleteCategory($key);

            Session::flash('message', trans('category.delete_ok'));
            return action('CategoriesController@index');
        }
        catch(Exception $e) {
            //TODO: save inputs
            return redirect()->back()->withErrors(["category.destroy" => $e->getMessage()]);
        }
    }

    public function delete($key){
        $data = array();

        $data['action'] = action("CategoriesController@destroy", $key);
        $data['title'] = "DELETE";
        $data['msg'] = "Are you sure you want to delete this Category?";
        $data['btn_ok'] = "Delete";
        $data['btn_ko'] = "Cancel";

        return view("_layouts.deleteModal", $data);
    }

    /**
     * Get the specified resource.
     *
     *
     */
    public function tableCategories()
    {
        $manage = Orchestrator::listCategories();

        // in case of json
        $category = Collection::make($manage->data);

        return Datatables::of($category)
            ->editColumn('name', function ($category) {
                return "<a href='".action('CategoriesController@show', $category->category_key)."'>".$category->name."</a>";
            })
            ->addColumn('action', function ($category) {
                return ONE::actionButtons($category->category_key, ['edit' => 'CategoriesController@edit', 'delete' => 'CategoriesController@delete']);
            })
            ->make(true);
    }
}