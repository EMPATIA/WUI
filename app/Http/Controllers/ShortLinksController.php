<?php

namespace App\Http\Controllers;

use Session;
use Exception;
use App\One\One;
use App\ComModules\EMPATIA;
use Illuminate\Http\Request;
use App\ComModules\Orchestrator;
use Datatables;

class ShortLinksController extends Controller {
    
    public function index() {
        $title = trans('privateShortLinks.short_links');

        return view('private.shortlinks.index', compact('title'));
    }

    public function create() {
        try{
            $title = trans('privateShortLinks.short_links');

            return view('private.shortlinks.shortlink', compact('title'));
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["shortlinks.create" => $e->getMessage()]);
        }

    }

    public function store(Request $request){
        try {
            $dataToSend = array(
                "name" => $request->get("name"),
                "code" => $request->get("code"),
                "url"  => $request->get("url")
            );
            
            $shortLink = EMPATIA::storeShortLink($dataToSend);
            
            return redirect()->action("ShortLinksController@show",$shortLink->short_link_key);
        } catch(Exception $e) {
            return redirect()->back()->withErrors(["shortlinks.store" => $e->getMessage()]);
        }
    }

    public function show($shortLinkKey) {
        try {
            $title = trans('privateShortLinks.short_links');

            $shortLink = EMPATIA::getShortLink($shortLinkKey);

            return view('private.shortlinks.shortlink', compact('shortLink','title'));            
        } catch(Exception $e) {
            return redirect()->back()->withErrors(["shortlinks.show" => $e->getMessage()]);
        }
    }

    public function edit($shortLinkKey) {
        try {
            $title = trans('privateShortLinks.short_links');

            $shortLink = EMPATIA::getShortLink($shortLinkKey);

            return view('private.shortlinks.shortlink', compact('shortLink','title'));            
        } catch(Exception $e) {
            return redirect()->back()->withErrors(["shortlinks.edit" => $e->getMessage()]);
        }
    }
    
    public function update(Request $request, $shortLinkKey){
        try {
            $dataToSend = array(
                "name" => $request->get("name"),
                "code" => $request->get("code"),
                "url"  => $request->get("url")
            );
            
            $shortLink = EMPATIA::updateShortLink($shortLinkKey, $dataToSend);
            
            return redirect()->action("ShortLinksController@show",$shortLink->short_link_key);
        } catch(Exception $e) {
            return redirect()->back()->withErrors(["shortlinks.update" => $e->getMessage()]);
        }
    }

    public function delete(Request $request,$shortLinkKey){
        $data = array();
        $data['action'] = action("ShortLinksController@destroy", $shortLinkKey);
        $data['title'] = trans('privateShortLinks.delete');
        $data['msg'] = trans('privateShortLinks.are_you_sure_you_want_to_delete').' ?';
        $data['btn_ok'] = trans('privateShortLinks.delete');
        $data['btn_ko'] = trans('privateShortLinks.cancel');

        return view("_layouts.deleteModal", $data);
    }
    
    public function destroy($shortLinkKey){
        try {
            EMPATIA::deleteShortLink($shortLinkKey);

            Session::flash('message', trans('privateShortLinks.delete_ok'));
            return action('ShortLinksController@index');
        } catch(Exception $e) {
            return redirect()->back()->withErrors(["privateShortLinks.destroy" => $e->getMessage()]);
        }
    }
    
    public function getIndexTable(Request $requet) {
        try {
            $shortLinksData = EMPATIA::getShortLinks($requet);

            // in case of json
            $shortLinks = collect($shortLinksData->shortLinks);

            //  Datatable with sent sms list
            return Datatables::of($shortLinks)
                ->editColumn('name', function ($shortLink) {
                    return "<a href='" . action('ShortLinksController@show', $shortLink->short_link_key) . "'>" . $shortLink->name . "</a>";
                })
                ->addColumn('action', function ($shortLink) {
                    return One::actionButtons($shortLink->short_link_key, ['show' => 'ShortLinksController@show','delete' => 'ShortLinksController@delete']);
                })
                ->rawColumns(['name','action'])
                ->with('total', $shortLinksData->recordsTotal)
                ->with('filtered', $shortLinksData->recordsFiltered)
                ->skipPaging()
                ->make(true);
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["groupTypes.tableGroupTypes" => $e->getMessage()]);
        }
    }


    public function resolveShortLink($shortCode) {
        try{
            $shortLinkUrl = EMPATIA::resolveShortLink($shortCode);

            return redirect($shortLinkUrl);
        } catch(Exception $e) {
            return redirect()->back();
        }
    }
}
