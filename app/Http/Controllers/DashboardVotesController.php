<?php

namespace App\Http\Controllers;

use App\ComModules\Analytics;
use App\ComModules\CB;
use App\ComModules\Orchestrator;
use DateInterval;
use DatePeriod;
use DateTime;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\One\One;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Collection;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\UserRequest;
use App\Http\Requests\CountryRequest;
use Datatables;
use Session;
use View;
use Breadcrumbs;
use Exception;

class DashboardVotesController extends Controller
{
    public function __construct()
    {
        View::share('title', trans('dashboardVotes.title'));

    }
    
    /**
     * Display charts for Proposals.
     *
     * @return Response
     */
    public function proposals($cbId = "")
    {
        try {

            // Request Orchestrator for proposals

            $response = Orchestrator::getIdeas();
            $ideaList  = $response->data;
                        
            // Prepare data to request CB details
            $cbKeys = [];
            foreach ($ideaList as $idea){
                $cbKeys[] = $idea->cb_key;
            }                 
            
            // Request CB for details
            $ideas = CB::getListCBs($cbKeys);
            
            // If CB is not defined return the view with no charts
            if($cbId == ""){
                return view('private.dashboardVotes.proposals',compact('ideas'));
            }

            // Request for vote sessions
            $voteSessionsList = CB::getCbVotes($cbId);
            // Request votes for all vote sessions
            $voteSessions = [];
            foreach($voteSessionsList as $voteSession){
                $response = Analytics::getVotes($voteSession->vote_id, $cbId);

                if((!empty($response->data) && (!empty($response->summary)) )){

                    $voteSessions[$voteSession->vote_id]["byProposal"] = $response->data;
                    $voteSessions[$voteSession->vote_id]["summary"] = $response->summary;
                }

                $response = Analytics::getVotesDaily($voteSession->vote_id, $cbId);

                $responseArray = json_decode($response->content() ,true);

                //Prepare information for daily voting
                if(!empty($responseArray['data'])){
                    $positives = [];
                    $negatives = [];
                    $labels = [];
                    $interval = new DateInterval('P1D');


                    if(!empty($voteSessions[$voteSession->vote_id]["summary"]->end_date) || $voteSessions[$voteSession->vote_id]["summary"]->end_date >  Carbon::today()){
                        $dateRange = new DatePeriod( new DateTime($voteSessions[$voteSession->vote_id]["summary"]->start_date) , $interval , Carbon::today());

                    }else{
                        $dateRange = new DatePeriod( new DateTime($voteSessions[$voteSession->vote_id]["summary"]->start_date) , $interval ,new DateTime($voteSessions[$voteSession->vote_id]["summary"]->end_date) );
                    }


                    foreach ($dateRange as $date){
                        $labels[] = $date->format("Y-m-d");
                    }
                    foreach ($labels as $dailyData ){
                        $positives[] = empty($responseArray['data'][$dailyData]['positives']) ? 0 : $responseArray['data'][$dailyData]['positives'] ;
                        $negatives[] = empty($responseArray['data'][$dailyData]['negatives']) ? 0 :$responseArray['data'][$dailyData]['negatives'] ;
                    }

                    $voteSessions[$voteSession->vote_id]["daily"] = ['labels'=>$labels , 'positives'=> $positives, 'negatives' => $negatives];
                }

                // Prepare Data for Top Ten
                $arrayTop = array_slice($voteSessions[$voteSession->vote_id]["byProposal"] , 0, 10);
                $voteSessions[$voteSession->vote_id]["top"] = $arrayTop;
            }

            // Return view with data
            return view('private.dashboardVotes.proposals', compact('ideas','voteSessions','cbId'));
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["dashboardVotes.proposals.error" => $e->getMessage()]);
        }        
        return view('private.dashboardVotes.proposals');
    }
    
    
}
