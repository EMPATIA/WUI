<?php

namespace App\Http\Controllers;

use App\ComModules\CM;
use App\ComModules\Orchestrator;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\One\One;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Collection;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\UserRequest;
use App\Http\Requests\PagesRequest;
use Datatables;
use Session;
use View;
use Breadcrumbs;

class PagesController extends Controller
{
    public function __construct()
    {
        View::share('private.pages', trans('page.page'));


    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        return view('private.pages.index');
    }

    /**
     * Create a new resource.
     *
     * @return Response
     */
    public function create()
    {
//        $entity_id = rand(0,1);
//        if($entity_id == 0){
//            $entity_id = 4;
//        }
//        $response = ONE::get([
//            'component' => 'orchestrator',
//            'api'       => 'entity',
//            'attribute' => $entity_id
//        ]);
//        if($response->statusCode() == 200){
//            $entity_id = $response->json();
//        }
        $entity_id = 1;
        return view('private.pages.page', compact('entity_id'));
    }

    /**
     *Store a newly created resource in storage.
     *
     * @param Request $request
     * @return $this|View
     */
    public function store(PagesRequest $request)
    {
        $languages = [
            [
                "id"    => "5",
                "name"  => "English",
                "code"  => "en"
            ],
            [
                "id"    => "1",
                "name"  => "Portuguese",
                "code"  => "pt"
            ]
        ];
        $pageContent = [];
        try {
            $page = CM::setPage($request->entity_id);

            $i = 0;
            foreach($languages as $language){
                $title = $_REQUEST["title_".$language['code']];
                $summary = $_REQUEST["summary_".$language['code']];
                $content = $_REQUEST["content_".$language['code']];
                $pageContent[$i] = CM::setPageContent($page->id, $language['id'], 1, 1, $title, $summary, $content);
                $i++;
            }

            Session::flash('message', trans('page.store_ok'));
            return redirect()->action('PagesController@show', $page->id);
        }
        catch(Exception $e) {
            //TODO: save inputs
            return redirect()->back()->withErrors(["page.store" => $e->getMessage()]);
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
            $page = CM::getPage($id);

            $versions = CM::listPageContent($id);

            $pageContent = CM::getPageContent($id);

            $activeVersion = $pageContent[0]->version;

            //dd(compact('page', 'pageContent', 'entity_id', 'versions', 'activeVersion'));
            return view('private.pages.page', compact('page', 'pageContent', 'entity_id', 'versions', 'activeVersion'));

        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["page.show" => $e->getMessage()]);
        }
    }

    public function activateVersion($id, $oldVersion, $newVersion){
        try{

            $oldContents = CM::getPageContentWithVersion($id, $oldVersion);

            $newContents = CM::getPageContentWithVersion($id, $newVersion);

            foreach ($oldContents as $oldContent) {
                CM::enablePageContent(0);
            }

            foreach ($newContents as $newContent) {
                CM::enablePageContent(1);
            }
            Session::flash('message', trans('page.activateVersion_ok'));
            return redirect()->action('PagesController@show', $id);

        } catch (Exception $ex) {
            return redirect()->back()->withErrors(["page.activateVersion" => $e->getMessage()]);
        }

    }


//    public function showVersion(Request $request, $id, $version)
//    {
//
//
//            $page = Page::with(['contents' => function ($q) use ($version) {
//                $q->where('version', '=', $version)
//                    ->orderBy('id_language');
//            }, 'contents.user'])->where('active', '=', 1)
//                ->where('id_page', '=', $id)
//                ->firstOrFail();
//
//            $versions = PageContent::where('id_page', '=', $page->id)
//                ->where('id_language', '=', 1)
//                ->orderBy('version', 'desc')
//                ->lists('created', 'version');
//
//            $activeVersion = PageContent::where('id_page', '=', $page->id)
//                ->where('id_language', '=', 1)
//                ->where('enabled', '=', 1)
//                ->value('version');
//
//            foreach ($versions as $ver => $date) {
//                $date = 'v' . $ver . ' ' . $date;
//
//                if ($ver == $activeVersion) {
//                    $date = "* " . $date;
//                }
//
//                $versions[$ver] = $date;
//            }
//
//
//            return view('page.page_show', compact('page', 'versions', 'activeVersion', 'menuType'));
//         else {
//            return redirect()->action('PagesController@index');
//        }
//    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param $id
     * @return View
     */
    public function edit($id)
    {
        try {
            $page = CM::getPage($id);

            $entity_id = Orchestrator::getEntity($page->entity_id);

            $pageContent = CM::getPageContent($id);

            return view('private.pages.page', compact('page', 'entity_id', 'pageContent'));
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["page.edit" => $e->getMessage()]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param $id
     * @return $this|View
     */
    public function update(PagesRequest $request, $id)
    {
        $languages = [
            [
                "id"    => "5",
                "name"  => "English",
                "code"  => "en"
            ],
            [
                "id"    => "1",
                "name"  => "Portuguese",
                "code"  => "pt"
            ]
        ];
        $pageContent = [];
        try {
            $entities = Orchestrator::getEntities();
            foreach($entities as $entity){
                if($entity->name == $request->entity_id){
                    $entity_id = $entity->id;
                }
            }
            $page = CM::updatePage($entity_id, $id);

            $i = 0;
            foreach($languages as $language){
                $title = $_REQUEST["title_".$language['code']];
                $summary = $_REQUEST["summary_".$language['code']];
                $content = $_REQUEST["content_".$language['code']];
                $pageContent[$i] = CM::updatePageContent($page->id, $language['id'], $request->version, $request->enabled, $title, $summary, $content);
                $i++;
            }

            Session::flash('message', trans('page.update_ok'));
            return redirect()->action('PagesController@show', $page->id);

        }
        catch(Exception $e) {
            //TODO: save inputs
            return redirect()->back()->withErrors(["page.update" => $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  $id
     * @return Response
     */
    public function destroy($id)
    {
        try {
            CM::deletePage($id);
            Session::flash('message', trans('page.delete_ok'));
            return redirect()->action('PagesController@index');
        }
        catch(Exception $e) {
            //TODO: save inputs
            return redirect()->back()->withErrors(["page.destroy" => $e->getMessage()]);
        }
    }

    /**
     * Get the specified resource.
     *
     *
     */
    public function tablePages()
    {

        $manage = CM::listPages();

        // in case of json
        $page = Collection::make($manage);

        return Datatables::of($page)
            ->editColumn('id', function ($page) {
                return "<a href='".action('PagesController@show', $page->id)."'>".$page->id."</a>";
            })
            ->addColumn('action', function ($page) {
                return ONE::actionButtons($page->id, ['edit' => 'PagesController@edit', 'delete' => 'PagesController@destroy']);
            })
            ->rawColumns(['id','action'])
            ->make(true);
    }
}
