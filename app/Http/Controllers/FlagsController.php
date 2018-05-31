<?php

namespace App\Http\Controllers;

use App\ComModules\CB;
use App\ComModules\Orchestrator;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use One;
use Session;
use Datatables;
use App\ComModules\Auth;

class FlagsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param $type
     * @param $cbKey
     * @return \Illuminate\Http\Response
     */
    public function index($type,$cbKey)
    {
        //Page title
        $title = trans('privateFlags.flags');
        Session::put('sidebarArguments', ['type' => $type, 'cbKey' => $cbKey, 'activeFirstMenu' => 'flags']);
        Session::put('sidebars', [0 => 'private', 1=> 'padsType']);

        $sidebar = 'padsType';
        $active = 'flags';

        return view('private.cbs.flags', compact('title','type','cbKey','sidebar','active'));
    }


    public function getIndexTable($type,$cbKey)
    {
        try {
            $requestFlags = CB::getFlagsFromCb($cbKey);
            // in case of json
            $flags = Collection::make($requestFlags);

            //  Datatable with sent emails list
            return Datatables::of($flags)
                ->editColumn('title', function ($flags) use($type,$cbKey){
                    return "<a href='" . action('FlagsController@show', [$type,$cbKey,$flags->id]) . "'>" . collect($flags->translations)->first()->title . "</a>";
                })
                ->editColumn('description', function ($flags) use($type,$cbKey){
                    return collect($flags->translations)->first()->description;
                })
                ->editColumn('private_flag', function ($flags){
                    return isset($flags->private_flag) ?  '<i class="fa fa-check-square" aria-hidden="true" style="color:green!important;"></i>' : '<i class="fa fa-ban" aria-hidden="true" style="color:red;"></i>';
                })
                ->editColumn('flag_visible', function ($flags){
                    return isset($flags->flag_visible) ?  '<i class="fa fa-check-square" aria-hidden="true" style="color:green!important;"></i>' : '<i class="fa fa-ban" aria-hidden="true" style="color:red;"></i>';
                })
                ->editColumn('public_visible', function ($flags){
                    return isset($flags->public_visible) ?  '<i class="fa fa-check-square" aria-hidden="true" style="color:green!important;"></i>' : '<i class="fa fa-ban" aria-hidden="true" style="color:red;"></i>';
                })
                ->addColumn('action', function ($flags) use($type,$cbKey){
                    return ONE::actionButtons(['id' => isset($flags->id) ? $flags->id : null,'type' => $type, 'cbKey' => $cbKey,'f' => 'flags' ], ['edit' => 'FlagsController@edit','delete' =>'FlagsController@delete']);
                })
                ->rawColumns(['title','private_flag','flag_visible','public_visible','action'])
                ->make(true);
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["flags.getIndexTable" => $e->getMessage()]);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param $type
     * @param $cbKey
     * @return \Illuminate\Http\Response
     */
    public function create($type,$cbKey)
    {
        $languages = Orchestrator::getLanguageList();
        $flagTypes = CB::getFlagTypesList();

        try {
            // Form title (layout)
            $title = trans('privateFlags.create_flag');

            $data = [];
            $data['type'] = $type;
            $data['cbKey'] = $cbKey;
            $data['title'] = $title;
            $data['languages'] = $languages;
            $data['flagTypes'] = $flagTypes;
            $data['sidebar'] = 'padsType';
            $data['active'] = 'flags';

            return view('private.cbs.flag', $data);
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["flags.create" => $e->getMessage()]);
        }
    }

    /**
     * @param $request
     * @param $languages
     * @return array
     */
    public function prepareTranslationsToSend($request, $languages)
    {
        $translations = [];
        foreach($languages as $language){
            if(!empty($request->input("title_".$language->code))){
                $translations[] = [
                    'language_code' => $language->code,
                    'title'         => $request->input("title_" . $language->code),
                    'description'   => $request->input("description_" . $language->code) ?? null
                ];
            }
        }
        return $translations;
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $languages = Orchestrator::getLanguageList();

            $translations = $this->prepareTranslationsToSend($request,$languages);

            //Call to Com Module set method
            CB::setFlag($request, $translations,'CB');

            // Message to show + redirect To
            Session::flash('message', trans('privateFlags.store_ok'));
            return redirect()->action('FlagsController@index',['type' => $request['type'], 'cbKey' => $request['cbKey']]);

        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["flagTypes.store" => $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param $type
     * @param $cbKey
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($type,$cbKey,$id)
    {
        try {
            $languages = Orchestrator::getLanguageList();
            $title = trans('privateFlags.show_flag');
            //Call to Com Module set method
            $flag = CB::getFlag($id);

            // Message to show + redirect To
            $data = [];
            $data['type'] = $type;
            $data['cbKey'] = $cbKey;
            $data['title'] = $title;
            $data['languages'] = $languages;
            $data['flag'] = $flag;
            $data['flagType'] = $flag->flag_type;
            $data['translations'] = $this->prepareTranslationsToShow($flag->translations);
            $data['sidebar'] = 'padsType';
            $data['active'] = 'flags';


            return view('private.cbs.flag', $data);

        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["privateFlags.show" => $e->getMessage()]);
        }
    }

    public function prepareTranslationsToShow($receivedTranslations)
    {
        $translations = [];
        if(!empty($receivedTranslations)){
            foreach ($receivedTranslations as $translation){
                $translations[$translation->language_code] = $translation;
            }
        }

        return $translations;

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        try {
            $languages = Orchestrator::getLanguageList();
            $title = trans('privateFlags.show_flag');
            //Call to Com Module set method
            $flag = CB::getFlag($id);
            $flagTypes = $flag->available_flag_types;

            // Message to show + redirect To

            $data = [];
            $data['type'] = $request['type'];
            $data['cbKey'] = $request['cbKey'];
            $data['title'] = $title;
            $data['languages'] = $languages;
            $data['flag'] = $flag;
            $data['flagTypes'] = $flagTypes;
            $data['flagType'] = $flag->flag_type;
            $data['translations'] = $this->prepareTranslationsToShow($flag->translations);
            $data['sidebar'] = 'padsType';
            $data['active'] = 'flags';

            return view('private.cbs.flag', $data);

        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["privateFlags.show" => $e->getMessage()]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $languages = Orchestrator::getLanguageList();

            $translations = $this->prepareTranslationsToSend($request,$languages);

            //Call to Com Module set method
            CB::updateFlag($request, $translations,$id);

            // Message to show + redirect To
            Session::flash('message', trans('privateFlags.update_ok'));
            return redirect()->action('FlagsController@index',['type' => $request['type'], 'cbKey' => $request['cbKey']]);

        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["flagTypes.store" => $e->getMessage()]);
        }
    }

    /**
     * @param $id
     * @return View
     */
    public function delete( Request $request,$id)
    {
        $data = array();

        $data['action'] = action("FlagsController@destroy", ['id' => $id, 'request' => $request->all()]);
        $data['title'] =  trans('FlagTypesController.delete');
        $data['msg'] = trans('FlagTypesController.are_you_sure you_want_to_delete_this_flag_type') . "?";
        $data['btn_ok'] = trans('FlagTypesController.delete');
        $data['btn_ko'] = trans('FlagTypesController.cancel');

        return view("_layouts.deleteModal", $data);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        try {
            $requestData = $request->input('request');

            $flag = CB::deleteFlag($id);

            Session::flash('message', trans('flags.delete_ok'));
            return action('FlagsController@index', ['type' => $requestData['type'], 'cbKey' => $requestData['cbKey']]);

        } catch (Exception $e) {
            return action('FlagsController@index', ['type' => $requestData['type'], 'cbKey' => $requestData['cbKey']])->with('errors', new MessageBag(['destroyNok' => $e->getMessage()]));
        }
    }


    public function attachFlag(Request $request)
    {
        try {
            CB::attachFlag($request);

            return response()->json(["success" => trans('privateCbs.flagAttachmentSuccess')]);
        }
        catch(Exception $e) {
            return response()->json(["error" => trans('privateCbs.flagAttachmentError')]);
        }
    }

    public function getElementFlagHistory(Request $request)
    {
        try {
            $elementKey = $request->input("elementKey");
            $attachmentCode = $request->input("attachmentCode");
            //Call to Com Module set method
            $flagHistory = CB::getElementFlagHistory($elementKey,$attachmentCode);

            $usersKeys = [];
            foreach ($flagHistory as $flag) {
                $usersKeys[$flag->pivot->created_by] = $flag->pivot->created_by;
            }
            $usersNames = Auth::getUserNames($usersKeys);
            foreach ($flagHistory as $flag) {
                if(!empty($usersNames->{ $flag->pivot->created_by }))
                    $flag->pivot->created_by = $usersNames->{ $flag->pivot->created_by }->name;
            }
            
            return view('private.cbs.flagHistory', compact('flagHistory','elementKey','attachmentCode'));
        }
        catch(Exception $e) {
            return response()->json(["error" => trans('privateCbs.cantFetchFlagHistory')]);
        }
    }

    public function toggleActiveStatus(Request $request) {
        try{
            CB::toggleFlagActiveStatus($request->input("status"),$request->input("elementKey"),$request->input("relationId"),$request->input("attachmentCode"));
            return response()->json(["success" => true]);
        } catch (Exception $e) {
            return response()->json(["error" => trans('privateCbs.failed_to_toggle_active_status')]);
        }
    }
}
