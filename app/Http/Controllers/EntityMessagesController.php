<?php

namespace App\Http\Controllers;

use App\ComModules\EMPATIA;
use App\Http\Controllers\Controller;
use Datatables;
use Illuminate\Http\Request;
use Session;
use One;
use Exception;

class EntityMessagesController extends Controller
{

    public function __construct(){

    }

    public function index(){
        $title = trans('privateEntities.list_messages');
        $sidebar = 'entityMessages';
        $active = 'messages';

        Session::put('sidebarArguments', ['activeFirstMenu' => 'messages']);

        return view('private.entityMessages.index', compact('title', 'sidebar', 'active'));
    }

    public function getIndexTable(Request $request){
        try {
            $userKey = Session::get('user')->user_key;
            $response = EMPATIA::getAllMessages($request, $userKey);

            $messages = isset($response->messages) ? collect($response->messages): Collection::make([]);
            $recordsTotal = $response->recordsTotal;
            $recordsFiltered = $response->recordsFiltered;

            return Datatables::of($messages)
                ->editColumn('to', function ($messages) {
                    if(!empty($messages->user_name)){
                        return "<a href='".action('UsersController@show', $messages->to)."'>" . $messages->user_name . "</a>";
                    }
                    else{
                        return null;
                    }

                })
                ->editColumn('value', function ($messages) {
                    return $messages->value ?? null;
                })
                ->editColumn('created_at', function ($messages) {
                    return $messages->created_at;
                })
                ->addColumn('action', function ($messages) {
                    return ONE::actionButtons($messages->from, ['show' => 'UsersController@showUserMessages']);
                })
                ->with('filtered', $recordsFiltered ?? 0)
                ->skipPaging()
                ->setTotalRecords($recordsTotal ?? 0)
                ->make(true);
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["groupTypes.tableGroupTypes" => $e->getMessage()]);
        }
    }

    public function showMessagesTable(Request $request, $flag){
        $title = trans('privateEntities.list_messages');
        $sidebar = 'entityMessages';

        if($flag == 'sentMessages')
            $active = 'sent_messages';
        else
            $active = 'received_messages';

        return view('private.entityMessages.messages', compact('title', 'flag', 'sidebar', 'active'));
    }

    public function getMessagesTable(Request $request, $flag){
        try {
            $response = EMPATIA::getEntityMessages($request, $flag);
            $messages = collect(isset($response->messages) ? $response->messages : []);
            
            $recordsTotal = $response->recordsTotal;
            $recordsFiltered = $response->recordsFiltered;

            return Datatables::of($messages)
                ->editColumn('to', function ($messages)  use($flag){
                    if(!empty($messages->user_name)) {
                        if($flag == 'sentMessages')
                            return "<a href='".action('UsersController@showUserMessages', $messages->to)."'>" . $messages->user_name . "</a>";
                        else
                            return "<a href='".action('UsersController@showUserMessages', $messages->from)."'>" . $messages->user_name . "</a>";
                    } else
                        return null;
                })
                ->editColumn('value', function ($messages) {
                    return $messages->value ?? null;
                })
                ->editColumn('created_at', function ($messages) {
                    return $messages->created_at;
                })
                ->addColumn('action', function ($messages) use($flag){
                    return ONE::actionButtons($messages->to, ['show' => 'UsersController@showUserMessages']);
                })
                ->with('filtered', $recordsFiltered ?? 0)
                ->skipPaging()
                ->setTotalRecords($recordsTotal ?? 0)
                ->make(true);
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["groupTypes.tableGroupTypes" => $e->getMessage()]);
        }
}


}
