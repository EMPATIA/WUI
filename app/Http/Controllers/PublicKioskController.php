<?php

namespace App\Http\Controllers;

use App\ComModules\Analytics;
use App\ComModules\Auth;
use App\ComModules\CB;
use App\ComModules\Orchestrator;
use App\ComModules\Vote;
use App\One\One;
use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Redirect;
use Session;

class PublicKioskController extends Controller
{
    private $kioskKey = '';

    public function __construct()
    {
        $this->kioskKey = env('KIOSK', 'Z8Ml89vSpRS9Lhg4zwaJ8VkYYk0VTDOn');
    }

    public function login(Request $request){
        $kioskKey = $this->kioskKey;
        try {

            if (empty($request->code)) {
                return redirect()->back()->withErrors(["kiosk.login" => trans('publicKiosk.noData')]);
            } else {
                $response = Auth::authenticateAlphanumeric($request->code);
                $authToken = $response->token;
                Session::put('X-AUTH-TOKEN', $authToken);


                //get logged user information


                $userInformation = Auth::getUser();
                Session::put('user', $userInformation);
                $userKey = $userInformation->user_key;

                //get kiosk information
                $kiosk = Orchestrator::getKiosk($kioskKey);
                Session::put('kiosk', $kiosk);
                //get vote status
                if (!empty($userKey)) {
                    $voteData = Vote::getVoteStatus($kiosk->event_key, $userKey);

                } else {
                    $voteData = Vote::getVoteStatus($kiosk->event_key, null);
                }
                Session::put('Vote_Status', $voteData);
                return view('public.'.ONE::getEntityLayout().'.home.welcomeLogin', compact('voteData'));



            }
        }catch (Exception $e) {
            return redirect()->back()->withErrors(["login.error" => $e->getMessage()]);
        }
    }


    public function participate(Request $request){
        $kioskKey = $this->kioskKey;
        try {

                $authToken = Session::get('X-AUTH-TOKEN');
                Session::put('X-AUTH-TOKEN', $authToken);
                //get logged user information

                $userInformation = Auth::getUser();
                Session::put('user', $userInformation);
                $userKey = $userInformation->user_key;

                //get kiosk information
                $kiosk = Orchestrator::getKiosk($kioskKey);
                Session::put('kiosk', $kiosk);
                //get vote status
                if (!empty($userKey)) {
                    $voteData = Vote::getVoteStatus($kiosk->event_key, $userKey);

                } else {
                    $voteData = Vote::getVoteStatus($kiosk->event_key, null);
                }
                Session::put('Vote_Status', $voteData);
                return view('public.'.ONE::getEntityLayout().'.home.welcomeLogin', compact('voteData'));

        }catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }



    public function listVote(Request $request)
    {
        $kioskKey =$this->kioskKey;

        try{
            //get kiosk information

            $kiosk = Orchestrator::getKiosk($kioskKey);

            //get cb all information with topics and topic parameters

            $cb = CB::getCBAndTopics($kiosk->entity_cb->cb_key, []);


            //get vote configuration

            $events[] = $kiosk->event_key;

            //get vote status
            if (!empty($userKey)) {
                $voteData = Vote::getVoteStatus($kiosk->event_key, $userKey);

            } else {
                $voteData = Vote::getVoteStatus($kiosk->event_key, null);
            }
            Session::put('Vote_Status', $voteData);
            $voteToUse = $voteData->remaining_votes;
            $votesUsed = json_decode(json_encode($voteData->votes),true);
            foreach($cb->topics as &$topic){
                $topic->voted = 0;
            }
            return view('public.'.ONE::getEntityLayout().'.home.listIdeas', ['kiosk' => $kiosk, 'cbWithTopics' => $cb,'votesUsed'=> $votesUsed, 'voteToUse' => $voteToUse, 'user'=> Session::get('user')]);

        }catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function verifyVote(Request $request)
    {
        $voteStatus = Session::get('Vote_Status');
        $total = $voteStatus->remaining_votes->total ?? 0;
        $positive =  $voteStatus->remaining_votes->positive ?? 0;
        $negative =  $voteStatus->remaining_votes->negative ?? 0;
        $response = false;

        if(isset($request->vote) && $request->vote == 1){
            if(isset($request->remove) && $request->remove == 'true'){
                $positive = $positive + 1;
                $total = $total + 1;
                $response = 1;
            }elseif(isset($request->voteChange) && $request->voteChange == 'true'){
                if($total > 0 && $positive > 0){
                    $negative = $negative + 1;
                    $positive = $positive - 1;
                    $response = 2;
                }
            }
            else{
                if($total > 0 && $positive > 0){
                    $total = $total - 1;
                    $positive = $positive - 1;
                    $response = 3;
                }
            }
        }elseif(isset($request->vote) && $request->vote == -1){
            if(isset($request->remove) && $request->remove == 'true'){
                $negative = $negative + 1;
                $total = $total + 1;
                $response = 4;
            }elseif(isset($request->voteChange) && $request->voteChange == 'true'){
                if($total > 0 && $positive > 0){

                    $negative = $negative - 1;
                    $positive = $positive + 1;
                    $response = 5;
                }
            }
            else{
                if($total > 0 && $negative > 0){
                    $total = $total - 1;
                    $negative = $negative - 1;
                    $response = 6;
                }
            }
        }
        $voteStatus->remaining_votes->total=$total;
        $voteStatus->remaining_votes->positive = $positive;
        $voteStatus->remaining_votes->negative = $negative;
        Session::put('Vote_Status', $voteStatus);

        return response()->json(["data"=>$response, "total"=> $total, "positive"=> $positive, "negative" => $negative],200);
    }


    public function submitVotes(Request $request)
    {
        try {
            $kiosk = Session::get('kiosk');
            $eventKey = $kiosk->event_key;
            $userKey = Session::get('user')->user_key;
            $voteStatus = Session::get('Vote_Status');

            //clean votes
            if(isset($request->votes)) {
                foreach ($voteStatus->votes as $vote){
                    $topicKey=$vote->vote_key;
                    $value=$vote->value;
                    $response = ONE::post([
                        'component' => 'wui',
                        'api' => 'kioskHandler',
                        'method' => 'vote',
                        'params' => [
                            'event_key' => $eventKey,
                            'vote_key' => $topicKey,
                            'value' => $value,
                            'source' => 'kiosk',
                            'user_key' => $userKey
                        ]]);
                }

                //update new user votes
                foreach ($request->votes as $vote) {

                    $vote = explode('_', $vote);
                    $topicKey = $vote[0];
                    $value = $vote[1];
                    if($value  != 0){
                        $response = ONE::post([
                            'component' => 'wui',
                            'api' => 'kioskHandler',
                            'method' => 'vote',
                            'params' => [
                                'event_key' => $eventKey,
                                'vote_key' => $topicKey,
                                'value' => $value,
                                'source' => 'kiosk',
                                'user_key' => $userKey
                            ]]);

                    }
                }

            }

            Session::forget('userInformation');
            Session::forget('Vote_Status');
            Session::forget('X-AUTH-TOKEN');

            $data = ["reply"=>true];
            return response()->json($data, 200);

        }
        catch(Exception $e) {

            $msg = 'Failed to logout';;
            return response()->json(['error' => $msg], 500);
        }
    }

    public function verifyVoteMade(Request $request){
        $kiosk = Session::get('kiosk');

        $response = CB::getCBAndTopics($kiosk->entity_cb->cb_key, []);

        $topics = $response->topics;

        $i=0;
        $topicsWithVotation = [];
        foreach ($topics as $topic) {

            foreach ($request->votes as $vote) {
                $vote = explode('_',$vote);
                $topicKey=$vote[0];
                $value=(int)$vote[1];
                if ($topic->topic_key == $topicKey && $value!=0) {
                    $topicsWithVotation[$i] = $topic;
                    $topicsWithVotation[$i]->voted = $value;
                    $i++;
                }
            }
        }
        $topicsWithVotation = collect($topicsWithVotation)->toArray();
        return view('public.'.ONE::getEntityLayout().'.home.votesMade', ['topics'=> $topicsWithVotation]);
    }


    public function showResults(Request $request){
        $kioskKey =$this->kioskKey;

        $kiosk = Orchestrator::getKiosk($kioskKey);

        $top = 30;
        $statisticsTotal = Analytics::getVoteStatisticsTotal($kiosk->event_key,$top);

        $statisticsTotalData = $statisticsTotal->data ?? [];
        $statisticsTotalSummary = $statisticsTotal->summary ?? [];
        $data['voteEventKey'] = $kiosk->event_key;
        $data["statisticsTotalData"] = $statisticsTotalData;
        $data["statisticsTotalSummary"] = $statisticsTotalSummary;
        return view('public.'.ONE::getEntityLayout().'.home.voteStatistics', $data);
    }



}
