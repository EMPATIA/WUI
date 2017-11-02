<?php

namespace App\Http\Controllers;

use App\ComModules\Auth;
use App\ComModules\CB;
use App\ComModules\Orchestrator;
use Cache;
use Carbon\Carbon;
use Datatables;
use DaveJamesMiller\Breadcrumbs\Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use App\ComModules\LogsRequest;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use ONE;
use Session;
use Symfony\Component\VarDumper\Cloner\Data;

class QuickAccessController extends Controller
{
    //
    public function __construct()
    {

    }


    /**
     * @return \BladeView|bool|\Illuminate\Contracts\View\Factory|\Illuminate\View\View|void
     */
    public function index()
    {
        try{
            //verify is Entity is in Session
            $isEntityInSession = !Session::has('X-ENTITY-KEY');
            $serversData = LogsRequest::getAllServers();
            //$componentData = LogsRequest::getAllComponents();
            //$nComponents = count($componentData);
            //$dashBoardElementsInformation = CB::getAvailableDashBoardElementsWithConfigurations();

            if(Session::has('X-ENTITY-KEY') && (!Session::has('userDashboardElements') || !Cache::has('entityDashboardElements_'.ONE::getEntityKey()))) {
                $dashBoardElementsInformation = CB::getAvailableDashBoardElementsWithConfigurations();

                Cache::put('entityDashboardElements_'.ONE::getEntityKey(),$dashBoardElementsInformation->available_entity_elements,1440);
                Session::put('userDashboardElements', $dashBoardElementsInformation->current_user_elements);
            }
            $entityDashboardElements = Cache::get('entityDashboardElements_'.ONE::getEntityKey());

            Session::forget('sidebars');
            Session::forget('sidebarActive');
            Session::forget('sidebarArguments');
            Session::put('sidebars', [0 => 'private']);
            Session::put('sidebarActive', 'private');

            //return view
            return view('private', compact('isEntityInSession', 'serversData','entityDashboardElements'));
        }catch (Exception $e) {

            return  $e->getMessage();
        }
    }


    /**
     * @return Datatables|string
     */
    public function getActivePads(){

        try {
            //Get CBs list
            $cbs= Collect(Orchestrator::getAllCbs());
            $cbsDetails = CB::getListCBs($cbs);

            $cbsDetails = collect($cbsDetails)->filter(function ($cbDetail) use($cbs){

                if(($cbDetail->start_date <= Carbon::now() && $cbDetail->start_date != '0000-00-00')
                    && ($cbDetail->end_date >= Carbon::now() || $cbDetail->end_date == NULL)
                    && $cbs->has($cbDetail->cb_key)){

                    $cbDetail->type = $cbs->get($cbDetail->cb_key)->cb_type->code;
                    return true;
                }
                return false;
            })->take(5);

            $color = [];
            $color['idea'] = 'style="color:#2daf47"';
            $color['proposal'] = 'style="color:#afad2d"';
            $color['forum'] = 'style="color:#4286f4"';
            $color['discussion'] = 'style="color:#af2d2d"';
            $color['survey'] = 'style="color:#5d2daf"';
            $color['tematicConsultation'] = 'style="color:#a85e5f"';
            $color['publicConsultation'] = 'style="color:#cc7f28"';

            //  get latest 10 elements
            $newList = collect($cbsDetails);

            //Datatable
            return Datatables::of($newList)
                ->editColumn('title', function($newList) use($color) {
                    return '<i class="fa fa-circle '.$newList->type.'" aria-hidden="true" data-toggle="tooltip" data-placement="right" '.(isset($color[$newList->type]) ? $color[$newList->type] : null).' title="'.$newList->type.'"></i>&nbsp;&nbsp;<a href="'. action('CbsController@showTopics', [$newList->type,$newList->cb_key]) . '"\>' . $newList->title . '</a>';
                })
                ->make(true);


        } catch (Exception $e) {
            return  $e->getMessage();
        }



    }

    function getPostsToModerate()
    {
        $cbs= Collect(Orchestrator::getAllCbs());
        $cbsDetails = CB::getListCBs($cbs);
        $cbsDetails = collect($cbsDetails);

        $cbKeysTypes = $cbs->pluck('cb_type.code', 'cb_key');
        $cbKeys = $cbsDetails->pluck('cb_key');
        $cbKeys = $cbKeys->toArray();
        $postsToModerate = CB::getPostsThatNeedsApproval($cbKeys);

        $users = Collect($postsToModerate)->pluck('created_by');

        $usersKeysNames = Collect(Auth::getUserNames($users))->pluck('name', 'user_key');

        $collection = Collection::make($postsToModerate)->take(15);

        return Datatables::of($collection)
            ->editColumn('topic', function($collection) use($cbKeysTypes){
                return "<a href='".action('TopicController@show', [$cbKeysTypes[$collection->cb->cb_key], $collection->cb->cb_key, $collection->topic->topic_key])."'>".$collection->topic->title."</a>";
            })
            ->editColumn('created_by', function($collection) use($usersKeysNames){
                if ($collection->created_by != 'anonymous') {
                    return $usersKeysNames[$collection->created_by] ?? '';
                }
                return trans('privateUser.anonymous');
            })
            ->editColumn('content', function($collection) use($cbKeysTypes){
                return "<a href='".action('TopicController@show', [$cbKeysTypes[$collection->cb->cb_key], $collection->cb->cb_key, $collection->topic->topic_key])."'>".$collection->contents."</a>";
            })
            ->addColumn('action', function ($collection) use($cbKeysTypes){
                $html = '<a href="'. action('PostController@active', [$cbKeysTypes[$collection->cb->cb_key], $collection->cb->cb_key, $collection->topic->topic_key,$collection->post_key, 1, 'home']) .'" class="btn btn-flat btn-success btn-xs" data-toggle="tooltip" data-original-title="approve"><i class="glyphicon glyphicon-thumbs-up"></i> </a> 
                <a href="'. action('PostController@blocked', [$cbKeysTypes[$collection->cb->cb_key], $collection->cb->cb_key, $collection->topic->topic_key,$collection->post_key, 1, 'home']) .'" class="btn btn-flat btn-danger btn-xs" data-toggle="tooltip" data-original-title="disapprove"><i class="glyphicon glyphicon-thumbs-down"></i> </a>';
                return $html;
            })
            ->make(true);
    }

    public function getUsersWithUnreadMessages($configurations)
    {
        $arguments = QuickAccessController::prepareArgumentsToSend($configurations);

        $userKeys = Orchestrator::getUsersWithUnreadMessages2($arguments['sortOrder'] ?? false,$arguments['numberOfRecords'] ?? false);

        $users = Auth::listUser($userKeys);
        $collection = Collection::make($users);
        // in case of json
        return Datatables::of($collection)
            ->editColumn('name', function ($collection){
                return "<a href='".action('UsersController@showUserMessages', ['user_key' => $collection->user_key])."'>".$collection->name."</a>";
            })
            ->make(true);
    }

    public function firstInstallWizard(Request $request) {
        try{
            if (!Session::get("firstInstallWizardStarted", false))
                $entitiesCount = count(Orchestrator::getEntities());
            else
                $entitiesCount = 0;
        } catch (Exception $e){
            $entitiesCount = 1;
        }

        if ($entitiesCount != 0)
            return redirect()->back();

        Session::forget('url_previous');
        Session::put("firstInstallWizardStarted", true);
        return view("private.wizards.install");
    }
    public function firstInstallWizardFinish() {
        Session::forget("firstInstallWizardStarted");
        Session::forget("firstInstallWizardEntityName");
        Session::forget("firstInstallWizardCBName");
        return redirect()->action("QuickAccessController@index");
    }


    /**
     * @param $configurations
     * @return \Illuminate\Http\JsonResponse
     */
    public static function getCommentsToModerate($configurations){

        try {
            $arguments = QuickAccessController::prepareArgumentsToSend($configurations);

            $information = CB::getParticipationInformation($arguments['cbKey'],true,false,false,false,false,true,true,false,true,false,$arguments['sortOrder'] ?? false,$arguments['numberOfRecords'] ?? false);

            $users = Collect($information->query_result)->pluck('created_by');

            $usersKeysNames = Collect(Auth::getUserNames($users))->pluck('name', 'user_key');

            $collection = Collection::make($information->query_result);

            $commentsNeedAuthorization = false;
            if(!$collection->isEmpty()) {
                $commentsNeedAuthorization = ONE::checkCBsOption(collect($information->cb->configurations)->pluck('code')->toArray(), 'COMMENT-NEEDS-AUTHORIZATION');
            }
            $data['commentsNeedAuthorization'] = $commentsNeedAuthorization;
            $data['usersKeysNames'] = $usersKeysNames;
            $data['collection'] = $collection;
            $data['arguments'] = $arguments;

            return view('private.dashBoardElements.sections.posts', $data);

        } catch (Exception $e) {
            return response()->json(['error' => trans('quickAccessController.failedToRetrievePostsToModerate')]);
        }

    }


    public static function getTopicsToModerate($configurations){

        try {
            $arguments = QuickAccessController::prepareArgumentsToSend($configurations);

            $information = CB::getParticipationInformation($arguments['cbKey'],true,true,false,false,false,false,false,false,true,false,$arguments['sortOrder'] ?? false,$arguments['numberOfRecords'] ?? false);

            $users = Collect($information->query_result)->pluck('created_by');

            $usersKeysNames = Collect(Auth::getUserNames($users))->pluck('name', 'user_key');

            $collection = Collection::make($information->query_result);
            $data['usersKeysNames'] = $usersKeysNames;
            $data['collection'] = $collection;
            $data['updateToStatus'] = strtolower('MODERATED');
            $data['arguments'] = $arguments;

            return view('private.dashBoardElements.sections.topicsToModerate', $data);

        } catch (Exception $e) {
            return response()->json(['error' => trans('quickAccessController.failedToRetrievePostsToModerate')]);
        }

    }



    public static function getUsersRegistrationConfirmation($configurations){

        try {
            $arguments = QuickAccessController::prepareArgumentsToSend($configurations);

            $siteKey = Session::get("X-SITE-KEY");

            $users = Auth::usersToModerate2($arguments, $siteKey);

            // dd($users);
            // $collection = Collection::make($data);

            $data['arguments'] = $arguments;
            $data['users'] = $users;

            return view('private.dashBoardElements.sections.usersRegistrationConfirmation', $data);

        } catch (Exception $e) {
            return response()->json(['error' => trans('quickAccessController.failedToRetrieveUsersRegistrationConfirmation')]);
        }
    }

    public static function getUnreadMessages($configurations){

        try {
            $arguments = QuickAccessController::prepareArgumentsToSend($configurations);

            $userKeys = Orchestrator::getUsersWithUnreadMessages2($arguments);
            $users = Auth::listUser($userKeys);

            // $collection = Collection::make($information->query_result);
            $data['arguments'] = $arguments;
            $data['users'] = $users;

            return view('private.dashBoardElements.sections.unreadMessages', $data);

        } catch (Exception $e) {
            return response()->json(['error' => trans('quickAccessController.failedToRetrieveUsersRegistrationConfirmation')]);
        }
    }




    public static function prepareArgumentsToSend($configurations)
    {
        $arguments = [];

        if(!empty($padKey = collect($configurations)->where('code','=','pad_key')->first())){
            $arguments['cbKey'] = $padKey->pivot->value;
        }

        if(!empty($sortOrder = collect($configurations)->where('code','=','sort_order')->first())){
            $arguments['sortOrder'] = $sortOrder->pivot->value;
        }

        if(!empty($numberOfRecords = collect($configurations)->where('code','=','records_to_show')->first())){
            $arguments['numberOfRecords'] = $numberOfRecords->pivot->value;
        }

        if(!empty($numberOfRecords = collect($configurations)->where('code','=','pad_type')->first())){
            $arguments['padType'] = $numberOfRecords->pivot->value;
        }

        return $arguments;


    }


    /**IMPROVED DASHBOARD ELEMENTS LOAD END**/

}
