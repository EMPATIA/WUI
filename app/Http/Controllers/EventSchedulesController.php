<?php

namespace App\Http\Controllers;

use App\ComModules\Auth;
use App\ComModules\Notify;
use App\ComModules\Orchestrator;
use App\ComModules\Questionnaire;
use Mail;
use Carbon\Carbon;
use DOMDocument;
use Exception;
use Illuminate\Http\Request;
use App\One\One;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Collection;
use App\Http\Requests\EventScheduleRequest;
use App\Http\Requests\AttendanceRequest;
use Datatables;
use Session;
use Tolerance\Bridge\Symfony\Bundle\ToleranceBundle\DependencyInjection\CompilerPass\CollectOperationRunnersCompilerPass;
use View;
use Breadcrumbs;
use URL;
use App\RequestMenu;


class EventSchedulesController extends Controller
{
    public function __construct()
    {
        View::share('title', trans('eventSchedules.schedules'));

    }

    /**
     * Display a listing of the resource.
     *
     * @return View
     */
    public function index(){
        $title = trans('privateEventSchedules.polls');
        return view("private.eventSchedule.index", compact('title'));
    }

    /**
     * Create a new resource.
     *
     * @return Response
     */
    public function create()
    {
        if(Session::get('user_role') != 'admin'){
            return redirect()->back()->withErrors(["eventSchedules.create" => trans('privateEventSchedules.permission_message')]);
        }

        $title = trans('privateEventSchedules.create_eventSchedule');
        return view('private.eventSchedule.eventSchedule', compact('title'));
    }


    /**
     * Display the specified resource.
     *
     * @param  string  $key
     * @return Response
     */
    public function show($key)
    {
        if(Session::get('user_role') != 'admin'){
            return redirect()->back()->withErrors(["eventSchedules.show" => trans('privateEventSchedules.permission_message')]);
        }

        try {
            $eventSchedule = Questionnaire::getEventSchedule($key);

            $title = trans('privateEventSchedules.show_eventSchedule').' '.(isset($eventSchedule->title) ? $eventSchedule->title: null);
            return view('private.eventSchedule.eventSchedule', compact('title', 'eventSchedule'));
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["eventSchedule.show" => $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $key
     * @return Response
     */
    public function showPeriods($key)
    {
        if(Session::get('user_role') != 'admin'){
            return redirect()->back()->withErrors(["eventSchedules.create" => trans('privateEventSchedules.permission_message')]);
        }

        try {
            $eventSchedule = Questionnaire::getEventSchedule($key);
            return view('private.eventSchedulePeriods.eventSchedule', compact('eventSchedule'));
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["eventSchedulePeriods.show" => $e->getMessage()]);
        }
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return $this|View
     * @internal param EventScheduleRequest
     */
    public function store(Request $request)
    {
        if(Session::get('user_role') != 'admin'){
            return redirect()->back()->withErrors(["eventSchedules.store" => trans('privateEventSchedules.permission_message')]);
        }

        try {
            // Prepare periods
            $periods = [];
            for ($i = 0; $i < count($request["start_date"]); $i++){
                if( $request["start_date"][$i]!=""){
                    $periods[] = [ "start_date" => $request["start_date"][$i],
                        "end_date" => "",
                        "start_time" =>  $request["start_time"][$i],
                        "end_time" => "" ];
                }
            }

            // Prepare questions
            $questions = [];
            for ($i = 0; $i < count($request["question"]); $i++){
                if( $request["question"][$i]!=""){
                    $questions[] = [ "question" => $request["question"][$i]];
                }
            }

            // Prepare data to send
            $data = [
                "title" => $request["title"],
                "entity_id" => $request["entity_id"],
                "type_id" => $request["type_id"],
                "description" => $request["description"],
                "local" => $request["local"],
                "closed" => $request["closed"],
                "public" => $request["public"],
                "periods" => $periods,
                "questions" => $questions,
            ];

            $eventSchedule = Questionnaire::setEventSchedule($data);

            Session::flash('message', trans('eventSchedule.store_ok'));

            return view('private.eventSchedule.eventSchedule', compact('eventSchedule'));
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["eventSchedule.store" => $e->getMessage()]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * EventScheduleRequest $request
     * @param EventScheduleRequest $request
     * @param $key
     * @return $this|View
     */
    public function update(EventScheduleRequest $request, $key)
    {
        if(Session::get('user_role') != 'admin'){
            return redirect()->back()->withErrors(["eventSchedules.update" => trans('privateEventSchedules.permission_message')]);
        }

        try {
            // Prepare periods
            $periods = [];
            for ($i = 0; $i < count($request["period_id"]); $i++){
                if( $request["start_date"][$i]!="" ){
                    $periods[] = [ "period_id" => $request["period_id"][$i],
                        "start_date" => $request["start_date"][$i],
                        "end_date" => $request["start_date"][$i],
                        "start_time" => $request["start_time"][$i],
                        "end_time" => "",
                        "remove" => $request["remove"][$i]];
                }
            }

            // Prepare questions
            $questions = [];
            for ($i = 0; $i < count($request["question"]); $i++){
                if( $request["question"][$i]!=""){
                    $questions[] = ["question_id" => $request["question_id"][$i],
                        "question" => $request["question"][$i],
                        "remove" => $request["remove"][$i]];
                }
            }

            // Prepare Data to send
            $data = [
                "title" => $request["title"],
                "entity_id" => $request["entity_id"],
                "description" =>  $request["description"],
                "local" => $request["local"],
                "closed" => $request["closed"],
                "public" => $request["public"],
                "es_period_id" => $request["es_period_id"],
                "es_question_id" => $request["es_question_id"],
                "periods" => $periods,
                "questions" => $questions
            ];

            Questionnaire::updateEventSchedule($data, $key);

            $eventSchedule = Questionnaire::getEventSchedule($key);
            Session::flash('message', trans('eventSchedule.update_ok'));

            return view('private.eventSchedule.eventSchedule', compact('eventSchedule'));

        }
        catch(Exception $e) {
            //TODO: save inputs
            return redirect()->back()->withErrors(["eventSchedule.update" => $e->getMessage()]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * EventScheduleRequest $request
     * @param EventScheduleRequest $request
     * @param $key
     * @return $this|View
     */
    public function updateDetails(EventScheduleRequest $request, $key)
    {
        if(Session::get('user_role') != 'admin'){
            return redirect()->back()->withErrors(["eventSchedules.updateDetails" => trans('privateEventSchedules.permission_message')]);
        }

        try {
            // Prepare Data to send
            $data = [
                "title" => $request["title"],
                "entity_id" => $request["entity_id"],
                "description" =>  $request["description"],
                "local" => $request["local"]
            ];

            Questionnaire::updateEventScheduleDetails($data, $key);
            $eventSchedule = Questionnaire::getEventSchedule($key);
            Session::flash('message', trans('eventSchedule.update_ok'));

            return view('private.eventSchedule.eventSchedule', compact('title', 'eventSchedule'));
        }
        catch(Exception $e) {
            //TODO: save inputs
            return redirect()->back()->withErrors(["eventSchedule.update" => $e->getMessage()]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * EventScheduleRequest $request
     * @param EventScheduleRequest $request
     * @param $key
     * @return $this|View
     */
    public function updatePeriods(EventScheduleRequest $request, $key)
    {
        if(Session::get('user_role') != 'admin'){
            return redirect()->back()->withErrors(["eventSchedules.updatePediods" => trans('privateEventSchedules.permission_message')]);
        }

        try {
            // Prepare periods
            $periods = [];
            for ($i = 0; $i < count($request["period_id"]); $i++){
                if( $request["start_date"][$i]!="" ){
                    $periods[] = [ "period_id" => $request["period_id"][$i],
                        "start_date" => $request["start_date"][$i],
                        "end_date" => $request["start_date"][$i],
                        "start_time" => $request["start_time"][$i],
                        "end_time" => "",
                        "remove" => $request["remove"][$i]];
                }
            }

            // Prepare Data to send
            $data = [
                "periods" => $periods
            ];

            Questionnaire::updateEventSchedulePeriods($data, $key);
            $eventSchedule = Questionnaire::getEventSchedule($key);

            Session::flash('message', trans('editPeriods.update_ok'));

            return view('private.eventSchedule.editPeriods', compact('eventSchedule'));
        }
        catch(Exception $e) {
            //TODO: save inputs
            return redirect()->back()->withErrors(["editPeriods.update" => $e->getMessage()]);
        }
    }

    /**
     * Show the Event Schedule for editing the specified resource.
     *
     * @param $key
     * @return View
     */
    public function edit($key)
    {
        if(Session::get('user_role') != 'admin'){
            return redirect()->back()->withErrors(["eventSchedules.edit" => trans('privateEventSchedules.permission_message')]);
        }

        try {

            $eventSchedule = Questionnaire::getEventSchedule($key);

            $title = trans('privateEventSchedules.update_eventSchedule').' '.(isset($eventSchedule->title) ? $eventSchedule->title: null);
            return view('private.eventSchedule.eventSchedule', compact('title', 'eventSchedule'));

        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["eventSchedule.show" => $e->getMessage()]);
        }
    }

    /**
     * Show the Event Schedule for editing the specified resource.
     *
     * @param $key
     * @return View
     */
    public function editPeriods($key)
    {
        if(Session::get('user_role') != 'admin'){
            return redirect()->back()->withErrors(["eventSchedules.updatePediods" => trans('privateEventSchedules.permission_message')]);
        }

        try {
            $eventSchedule = Questionnaire::getEventSchedule($key);
            $title = trans('privateEventSchedules.update_periods');
            return view('private.eventSchedule.editPeriods', compact('title', 'eventSchedule'));
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["editPeriods.show" => $e->getMessage()]);
        }
    }



    /**
     * Show confirm popup to remove the specified resource from storage.
     *
     * @param $id
     * @return View
     */
    public function delete($id){
        $data = array();

        $data['action'] = action("EventSchedulesController@destroy", $id);
        $data['title'] = "DELETE";
        $data['msg'] = "Are you sure you want to delete this attendance?";
        $data['btn_ok'] = "Delete";
        $data['btn_ko'] = "Cancel";

        return view("_layouts.deleteModal", $data);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  $id
     * @return Response
     */
    public function destroy($id){

        if(Session::get('user_role') != 'admin'){
            return redirect()->back()->withErrors(["eventSchedules.destroy" => trans('privateEventSchedules.permission_message')]);
        }

        try {
            Questionnaire::deleteEventSchedule($id);
            Session::flash('message', trans('eventSchedule.delete_ok'));
            return action('EventSchedulesController@index');

        }
        catch(Exception $e) {
            //TODO: save inputs
            return redirect()->back()->withErrors(["eventSchedule.destroy" => $e->getMessage()]);
        }
    }


    /**
     * Get a datatable of Event Schedules.
     *
     * @return Datatable of Collection made
     */
    public function getIndexTable()
    {
        if(Session::get('user_role') == 'admin') {
            // Request for Data List

            $response =Questionnaire::getEventSchedulesList();

            if ($response) {
                // JSON data collection
                $collection = Collection::make($response);
            } else
                $collection = Collection::make([]);
        } else
            $collection = Collection::make([]);

        $delete = Session::get('user_role') == 'admin';


        // Render Datatable
        $datatable = Datatables::of($collection)
            ->editColumn('title', function ($eventSchedule) {
                return "<a href='".action('EventSchedulesController@show', $eventSchedule->key)."'>".$eventSchedule->title."</a>";
            })

            ->addColumn('action', function ($eventSchedule) use($delete){
                if($delete)
                    return ONE::actionButtons($eventSchedule->key, ['show' => 'EventSchedulesController@show', 'delete' => 'EventSchedulesController@delete']);
                else
                    return ONE::actionButtons($eventSchedule->key, ['show' => 'EventSchedulesController@show']);
            })
            ->rawColumns(['title','action'])
            ->make(true);

        return $datatable;

    }



    public function attendance(Request $request,$key){

        try {
            // Check if private or public
            $eventSchedule = Questionnaire::getEventSchedule($key);

            if($eventSchedule->public == 1){
                return redirect()->back()->withErrors(["public.".ONE::getEntityLayout().".eventSchedule.attendance" => "This is public"]);
            }

            // get user details
            $response = Auth::getUser();

            $eventSchedule = Questionnaire::getEventSchedule($key);

            if($eventSchedule->public == 1){
                return redirect()->back()->withErrors(["public.".ONE::getEntityLayout().".eventSchedule.attendance" => "This is public"]);
            }

            if ($response->statusCode() == 200) {
                return view('private.eventSchedule.attendance', compact('eventSchedule','key','user'));
            }

        } catch (Exception $e) {
            return redirect()->back()->withErrors(["private.eventSchedule.attendance" => $e->getMessage()]);
        }
    }


    public function storeAttendance(Request $request,$key){
        try {
            // Check if private or public
            $eventSchedule = Questionnaire::getEventSchedule($key);

//            $owner = Auth::getUserByKey($eventSchedule->created_by);

            if($eventSchedule->public == 1){
                return redirect()->back()->withErrors(["public.".ONE::getEntityLayout().".eventSchedule.attendance" => "This is public"]);
            }

            // Get user details
            $response =  Auth::getUser();

            $user = $response->user;

            // Defaults values for periods
            if( is_numeric($request->periods)){
                $periods = array($request->periods);
            }else if(empty($request->periods)){
                $periods = [];
            } else {
                $periods = $request->periods;
            }

            // Defaults values for questions
            if( is_numeric($request->questions)){
                $questions = array($request->questions);
            }else if(empty($request->questions)){
                $questions = [];
            } else {
                $questions = $request->questions;
            }

            // Store attendance / participation
            $response = Questionnaire::setParticipation($key, $request->name, $periods, $questions);

            // Get Event Schedule with participations


            // Get Event Schedule details
            $eventSchedule = Questionnaire::getEventSchedule($key);

            // Message / notify
            $title = "Event: ".$request->eventName." - New attendance";
            $msg = "The user with the name ".$request->name.", requested an attendance for this event.";
//            EventSchedulesController::notify($owner->email,$owner->name,$title,$msg);

            Session::flash('message', trans('attendance.create_ok'));

            return view('private.eventSchedule.attendance', compact('eventSchedule','key','user'));


        } catch (Exception $e) {
            return redirect()->back()->withErrors(["private.eventSchedule.attendance" => $e->getMessage()]);
        }
    }


    public function updateAttendance(Request $request,$key){

        try {
            // Check if private or public
            $eventSchedule = Questionnaire::getEventSchedule($key);

            if($eventSchedule->public == 1){
                return redirect()->back()->withErrors(["public.".ONE::getEntityLayout().".eventSchedule.attendance" => "This is public"]);
            }

            // Get user details
            $response = Auth::getUser();

            $user = $response->user;

            // Defaults values for periods
            if( is_numeric($request->periods) ){
                $periods = array($request->periods);
            }else if(empty($request->periods)){
                $periods = [];
            } else {
                $periods = $request->periods;
            }

            // Defaults values for questions
            if( is_numeric($request->questions)){
                $questions = array($request->questions);
            }else if(empty($request->questions)){
                $questions = [];
            } else {
                $questions = $request->questions;
            }

            // Update attendance / participation

            Questionnaire::updateParticipation($key, $request->participant_id, $request->name, $periods,$questions);

            $eventSchedule = Questionnaire::getEventSchedule($key);

            // Message / notify
//                $title = "Event: ".$request->eventName." - Update attendance";
//                $msg = "The user with the name ".$request->name.", updated the attendance for this event.";
//                EventSchedulesController::notify("ilidio@onesource.pt","Ilidio",$title,$msg);

            Session::flash('message', trans('attendance.update_ok'));

            return view('private.eventSchedule.attendance', compact('eventSchedule','key','user'));


        } catch (Exception $e) {
            return redirect()->back()->withErrors(["private.eventSchedule.attendance" => $e->getMessage()]);
        }
    }


    public function deleteAttendance(Request $request,$eventKey,$key){
        $data = array();

        $data['action'] = action("EventSchedulesController@destroyAttendance",[$eventKey,$key]);
        $data['title'] = "DELETE";
        $data['msg'] = "Are you sure you want to delete this attendance?";
        $data['btn_ok'] = "Delete";
        $data['btn_ko'] = "Cancel";

        return view("_layouts.deleteModal", $data);
    }

    public function destroyAttendance(Request $request,$eventKey,$key){
        try {
            // Check if private or public
            $eventSchedule = Questionnaire::getEventSchedule($key);

            if($eventSchedule->public == 1){
                return redirect()->back()->withErrors(["public.".ONE::getEntityLayout().".eventSchedule.attendance" => "This is public"]);
            }

            Questionnaire::deleteParticipation($key);
            return action('EventSchedulesController@attendance',$eventKey);

        }
        catch(Exception $e) {
            //TODO: save inputs
            return redirect()->back()->withErrors(["private.eventSchedule.attendance" => $e->getMessage()]);
        }
    }

    public function publicAttendance(Request $request,$key){
        try {


            // Check if private or public

            if (One::isAuth()) {
                // Get user details
                $user = Auth::getUser();
            }

            $eventSchedule = Questionnaire::getEventSchedule($key);

            if($eventSchedule->public == 0 && !One::isAuth()){
                Session::put('redirect', 'public');

                Session::put('url_previous', URL::action('EventSchedulesController@publicAttendance',$key));

                return redirect()->action('AuthController@login');
            }

			$alreadyVoted = false;
			if(!empty($user)){
				foreach($eventSchedule->participants as $participant) {
					if($user->user_key == $participant->user_key ) {
						$alreadyVoted = true;
					}
				}
			}
			if($eventSchedule->type_id == 1){/* Verify if Poll is type Date*/
				$periods = [];

				if(!empty($eventSchedule->periods)){
					foreach($eventSchedule->periods as $period){
						$periods[$period->start_date]["month"] = date("F", strtotime($period->start_date));
						$periods[$period->start_date]["year"] = date("Y", strtotime($period->start_date));
						$periods[$period->start_date]["day"] = date("j", strtotime($period->start_date));
						$periods[$period->start_date]["dayweek"] = $days[] = strftime('%A', strtotime($period->start_date));
						// Periods
						$periods[$period->start_date]["periods"][] = array("id" => $period->id ,"period" => $period->start_time);
					}
				}

				return view('public.'.ONE::getEntityLayout().'.poll.attendance_dates', compact('eventSchedule','key','user', 'periods', 'alreadyVoted'));
			}elseif ($eventSchedule->type_id == 2){
				return view('public.'.ONE::getEntityLayout().'.poll.attendance_questions', compact('eventSchedule','key','user', 'alreadyVoted'));
			}

        } catch (Exception $e) {
            return redirect()->back()->withErrors(["public.".ONE::getEntityLayout().".eventSchedule.attendance" => $e->getMessage()]);
        }
    }


    public function publicStoreAttendance(Request $request,$key){

        try {
            // Check if private or public
            $eventSchedule = Questionnaire::getEventSchedule($key);

            $owner = Auth::getUserByKey($eventSchedule->created_by);


            if($eventSchedule->public == 0 && !One::isAuth()){
                return redirect()->back()->withErrors(["private.eventSchedule.attendance" => "This is private"]);
            }

            // Request field Name cannot be empty
            if(empty($request->name)){
                return redirect()->back()->withErrors(["public.".ONE::getEntityLayout().".eventSchedule.attendance" =>trans('attendance.nameRequired')]);
            }

            // Defaults values for periods
            if( is_numeric($request->periods)){
                $periods = array($request->periods);
            }else if(empty($request->periods)){
                $periods = [];
            } else {
                $periods = $request->periods;
            }
            // Defaults values for questions
            if( is_numeric($request->questions)){
                $questions = array($request->questions);
            }else if(empty($request->questions)){
                $questions = [];
            } else {
                $questions = $request->questions;
            }

            // Resquest for user details
            if (One::isAuth()) {
                $response = Auth::getUser();

                $user = $response->user;
            }

            // Update attendance
            if (One::isAuth()) {
                $response = Questionnaire::setParticipation($key, $request->name, $periods, $questions);
            } else {
                $response = Questionnaire::setAnonymousParticipation($key, $request->name, $periods, $questions);
            }

            // Message / notify
//                $title = "Event: ".$request->eventName." - New attendance";
//                $msg = "The user with the name ".$request->name.", requested an attendance for this event.";
//                $site = Orchestrator::getSite(Session::get('X-SITE-KEY'));
//                $data ['users'] = [$owner->email];
//                $data ['subject'] = $title;
//                $data ['message'] = $msg;
//                Notify::createEmails((object) $data, $site);

            Session::flash('message', trans('attendance.create_ok'));
            return redirect()->action('EventSchedulesController@publicAttendance', $key);
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["public.".ONE::getEntityLayout().".eventSchedule.attendance" => $e->getMessage()]);
        }
    }


    public function publicUpdateAttendance(Request $request,$key){
        try{

            // Check if private or public
            $eventSchedule = Questionnaire::getEventSchedule($key);

            if($eventSchedule->public == 0 && !One::isAuth()){
                return redirect()->back()->withErrors(["private.eventSchedule.attendance" => "This is private"]);
            }

            // The name cannot be null
            if( empty($request->name)){
                return redirect()->back()->withErrors(["public.".ONE::getEntityLayout().".eventSchedule.attendance" =>trans('attendance.nameRequired')]);
            }

            // Get user details if auth
            if (One::isAuth()) {
                $response = Auth::getUser();

                $user = $response->user;
            }

            // Defaults values for periods
            if( is_numeric($request->periods) ){
                $periods = array($request->periods);
            }else if(empty($request->periods)){
                $periods = [];
            } else {
                $periods = $request->periods;
            }

            // Defaults values for questions
            if( is_numeric($request->questions)){
                $questions = array($request->questions);
            }else if(empty($request->questions)){
                $questions = [];
            } else {
                $questions = $request->questions;
            }

            // Update attendance
            if (One::isAuth()) {
                $response = Questionnaire::updateParticipation($key, $request->participant_id, $request->name, $periods, $questions);
            } else {
                $response = Questionnaire::updateAnonymousParticipation($key, $request->participant_id, $request->name, $periods, $questions);
            }

            Session::flash('message', trans('attendance.update_ok'));

            // Message / notify
//            $title = "Event: ".$request->eventName." - Updated attendance";
//            $msg = "The user with the name ".$request->name.", update the attendance for this event.";
//            $site = Orchestrator::getSite(Session::get('X-SITE-KEY'));
//            $data ['users'] = [$owner->email];
//            $data ['subject'] = $title;
//            $data ['message'] = $msg;
//            Notify::createEmails((object) $data, $site);

            return redirect()->action('EventSchedulesController@publicAttendance', $key);
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["public.".ONE::getEntityLayout().".eventSchedule.attendance" => $e->getMessage()]);
        }

    }


    public function publicDeleteAttendance(Request $request,$eventKey,$key){

        $data = array();
        $data['action'] = action("EventSchedulesController@publicDestroyAttendance",[$eventKey,$key]);
        $data['title'] = trans('PrivateEventSchedule.delete');
        $data['msg'] = trans('PrivateEventSchedule.are_you_sure_you_want_to_delete_this_attendance').' ?';
        $data['btn_ok'] = trans('PrivateEventSchedule.delete');
        $data['btn_ko'] = trans('PrivateEventSchedule.cancel');

        return view("_layouts.deleteModal", $data);
    }

    public function publicDestroyAttendance(Request $request,$eventKey,$key){
        try {
            // Check if private or public
            $eventSchedule = Questionnaire::getEventSchedule($eventKey);

            if ($eventSchedule->public == 0 && !One::isAuth()) {
                return redirect()->back()->withErrors(["private.eventSchedule.attendance" => "This is private"]);
            }

            // Delete attendance
            if (One::isAuth()) {
                Questionnaire::deleteParticipation($key);
            } else {
                Questionnaire::deleteAnonymousParticipation($key);
            }

            // Message / notify
            $title = "Event: " . $request->eventName . " - removed attendance";
            $msg = "The user with the name " . $request->name . ", removed the attendance for this event.";
            $site = Orchestrator::getSite(Session::get('X-SITE-KEY'));
            $data ['users'] = [$owner->email ?? ""];
            $data ['subject'] = $title;
            $data ['message'] = $msg;
            Notify::createEmails((object)$data, $site);

            return action('EventSchedulesController@publicAttendance', $eventKey);
        }
        catch(Exception $e) {
            //TODO: save inputs
            return redirect()->back()->withErrors(["public.publicAttendance.destroyAttendance" => $e->getMessage()]);
        }
    }
}
