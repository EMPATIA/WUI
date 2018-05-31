<?php

namespace App\Http\Controllers;

use App\ComModules\EMPATIA;
use App\ComModules\Orchestrator;
use App\Http\Controllers\Controller;
use Datatables;
use Illuminate\Http\Request;
use Session;
use One;
use Exception;

class EntityMessagesController extends Controller
{

    public function __construct()
    {

    }

    public function index()
    {
        try {
            $title = trans('privateEntities.list_messages');
            $active = "all_messages";

            return view('private.entityMessages.index', compact('title', 'active'));
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["private.entityMessages" => $e->getMessage()]);
        }
    }

    public function show(Request $request, $messageKey){
        $message = Orchestrator::getMessage($messageKey);

        $message = $message->value;

        return view('private.entityMessages.message',compact('message'));
    }

    public function getIndexTable(Request $request)
    {
        try {
            $filters = array(
                "sent" => !empty($request->get("sent_messages")) ? 1 : 0,
                "received" => !empty($request->get("received_messages")) ? 1 : 0
            );

            $response = EMPATIA::getEntityMessages($filters, $request);
            $messages = collect(isset($response->messages) ? $response->messages : []);
            $entityName = $response->entityName;

            $recordsTotal = $response->recordsTotal;
            $recordsFiltered = $response->recordsFiltered;

            return Datatables::of($messages)
                ->editColumn('to', function ($message) use ($entityName){
                    if ($message->type=="sent") {
                        if(!empty($message->receiver->name)){
                            return "<a href='" . action('UsersController@showUserMessages', $message->to) . "'>" . $message->receiver->name . "</a>";
                        }else{
                            return "";
                        }
                    } else {
                        return $entityName;
                    }
                })
                ->editColumn('from', function ($message) {
                    if($message->user_id == 0){
                        if(!empty($message->from)){
                            return $message->from;
                        }else{
                            return "";
                        }
                    }else{
                        if ($message->type=="received") {
                            if(!empty($message->sender->name)){
                                return "<a href='" . action('UsersController@showUserMessages', $message->to) . "'>" . $message->sender->name . "</a>";
                            }else{
                                return "";
                            }
                        } else {
                            return "<a href='" . action('UsersController@show', ["userKey" => $message->from, "role" => ($message->sender->orchUser->entities[0]->role??"manager")])  . "'>" . $message->sender->name . "</a>";
                        }
                    }
                })
                ->editColumn('value', function ($message) {
                    if (!empty($message->value)) {
                        if($message->user_id == 0){
                            $messageText = $message->value;
                            if (strlen($messageText)>103)
                                return substr($messageText,0, 100) . "...";
                            else
                                return $messageText; 
                        }else{
                            if (strlen($message->value)>103)
                                return substr($message->value,0, 100) . "...";
                            else
                                return $message->value;
                        }
                    }
                    return "<i>" . trans("privateEntities.empty_message") . "</i>";
                })
                ->addColumn('action', function ($message) {
                    if($message->user_id == 0){
                        return ONE::actionButtons($message->message_key, ['show' => 'EntityMessagesController@show']);
                    }else{
                        if ($message->type=="received") {
                            return ONE::actionButtons($message->from, ['show' => 'UsersController@showUserMessages']);
                        }else 
                            return ONE::actionButtons($message->to, ['show' => 'UsersController@showUserMessages']);
                    }
                })
                ->rawColumns(['to','from','received','value','action'])
                ->with('filtered', $recordsFiltered ?? 0)
                ->skipPaging()
                ->setTotalRecords($recordsTotal ?? 0)
                ->make(true);
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["groupTypes.tableGroupTypes" => $e->getMessage()]);
        }
    }
}
