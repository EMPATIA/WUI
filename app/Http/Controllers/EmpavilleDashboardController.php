<?php

namespace App\Http\Controllers;

use App\ComModules\Analytics;
use App\ComModules\CB;
use App\One\One;
use Datatables;
use Exception;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Collection;
use Agent;
use Illuminate\Support\Facades\App;
use View;

class EmpavilleDashboardController extends Controller
{
    public function __construct()
    {
        View::share('title', trans('empavilleDashboard.title'));

    }

    public function totals($cbKey, Request $request){
        try{
            $lang = $request->lang;
            App::setLocale($lang);
            //Request the first voteEvent key
            $voteEvents = CB::getCbVotes($cbKey);

            if(empty($voteEvents[0])){
                //Page error no data
                dd('No data found');
            }

            $response = Analytics::getVotes($voteEvents[0]->vote_key, $cbKey);

            $voteSession = [];
            if((!empty($response->json()->data) && (!empty($response->json()->summary)) )){
                $voteSession["byProposal"] = $response->json()->data;
                $voteSession["summary"] = $response->json()->summary;

                // Prepare Data for Top Ten
                if(sizeof($voteSession["byProposal"]) > 8){
                    $arrayTop = array_slice($voteSession["byProposal"] , 0, 8);
                    $voteSession["top"] = $arrayTop;
                }else{
                    $voteSession["top"] = $voteSession["byProposal"];
                }

            }

            return view('empaville.dashboards.totals', compact('lang', 'cbKey','voteSession'));
        }catch(Exception $e) {
            return redirect()->back()->withErrors(["empavilleDashboard.proposals.error" => $e->getMessage()]);
        }
    }


    public function byGender($cbKey, Request $request){
        try{
            $lang = $request->lang;
            App::setLocale($lang);
            //Request the first voteEvent key

            $voteEvents = CB::getCbVotes($cbKey);

            if(empty($voteEvents[0])){
                //Page error no data
                dd('No data found');
            }

            $votesByGender = Analytics::getVoteStatisticsByGender($voteEvents[0]->vote_key, $cbKey);

            $firstByGender = Analytics::getVotesFirstByGender($voteEvents[0]->vote_key, $cbKey);

            $secondByGender = Analytics::getVotesSecondByGender($voteEvents[0]->vote_key, $cbKey);
            return view('empaville.dashboards.gender', compact('lang', 'cbKey','votesByGender', 'firstByGender', 'secondByGender'));

        }catch(Exception $e) {
            return redirect()->back()->withErrors(["empavilleDashboard.proposals.error" => $e->getMessage()]);
        }
    }

    public function byProfession($cbKey, Request $request){
        try{
            $lang = $request->lang;
            App::setLocale($lang);
            //Request the first voteEvent key

            $voteEvents = CB::getCbVotes($cbKey);

            if(empty($voteEvents[0])){
                //Page error no data
                dd('No data found');
            }

            //Professions
            $votesByProfession = Analytics::getCountVotesByProfession($voteEvents[0]->vote_key, $cbKey);

            $firstByProfession = Analytics::getVotesFirstByProfession($voteEvents[0]->vote_key, $cbKey);

            $secondByProfession = Analytics::getVotesSecondByProfession($voteEvents[0]->vote_key, $cbKey);

            return view('empaville.dashboards.byProfession', compact('lang', 'cbKey','votesByProfession', 'firstByProfession', 'secondByProfession'));

        }catch(Exception $e) {
            return redirect()->back()->withErrors(["empavilleDashboard.proposals.error" => $e->getMessage()]);
        }
    }

    public function byNb($cbKey, Request $request){
        try{
            $lang = $request->lang;
            App::setLocale($lang);
            //Request the first voteEvent key
            $voteEvents = CB::getCbVotes($cbKey);

            if(empty($voteEvents[0])){
                //Page error no data
                dd('No data found');
            }

            //Get Votes by Neighbourhood
            $votesDataNb = Analytics::getVotesByNeighbourhood($voteEvents[0]->vote_key, $cbKey);

            //Neighbourhood
            $votesByNb = Analytics::getCountVotesByNeighbourhood($voteEvents[0]->vote_key, $cbKey);

            $firstByNb = Analytics::getVotesFirstByNeighbourhood($voteEvents[0]->vote_key, $cbKey);

            $secondByNb = Analytics::getVotesSecondByNeighbourhood($voteEvents[0]->vote_key, $cbKey);

            return view('empaville.dashboards.byNb', compact('lang', 'cbKey','votesByNb', 'firstByNb', 'secondByNb'));

        }catch(Exception $e) {
            return redirect()->back()->withErrors(["empavilleDashboard.proposals.error" => $e->getMessage()]);
        }
    }
    public function byGeoArea($cbKey, Request $request){
        try{
            $lang = $request->lang;
            App::setLocale($lang);
            //Request the first voteEvent key
            $voteEvents = CB::getCbVotes($cbKey);

            if(empty($voteEvents[0])){
                //Page error no data
                dd('No data found');
            }

            //Get Votes by Neighbourhood
            $votesByNb = Analytics::getVotesByNeighbourhood($voteEvents[0]->vote_key, $cbKey);
            foreach ($votesByNb as $key => $vote){
                $votesByNb[$key]->total = $vote->Uptown->balance + $vote->Middletown->balance + $vote->Downtown->balance;
            }

            // Prepare data for chart Middletown
            $data["Uptown"]["Uptown"]["positives"] = 0;
            $data["Uptown"]["Uptown"]["negatives"] = 0;
            $data["Uptown"]["Middletown"]["positives"] = 0;
            $data["Uptown"]["Middletown"]["negatives"] = 0;
            $data["Uptown"]["Downtown"]["positives"] = 0;
            $data["Uptown"]["Downtown"]["negatives"] = 0;
            $data["Middletown"]["Uptown"]["positives"] = 0;
            $data["Middletown"]["Uptown"]["negatives"] = 0;
            $data["Middletown"]["Middletown"]["positives"] = 0;
            $data["Middletown"]["Middletown"]["negatives"] = 0;
            $data["Middletown"]["Downtown"]["positives"] = 0;
            $data["Middletown"]["Downtown"]["negatives"] = 0;
            $data["Downtown"]["Uptown"]["positives"] = 0;
            $data["Downtown"]["Uptown"]["negatives"] = 0;
            $data["Downtown"]["Middletown"]["positives"] = 0;
            $data["Downtown"]["Middletown"]["negatives"] = 0;
            $data["Downtown"]["Downtown"]["positives"] = 0;
            $data["Downtown"]["Downtown"]["negatives"] = 0;

            foreach ($votesByNb as $vote){
                if(isset($data[$vote->geo_area])) {
                    $data[$vote->geo_area]["Downtown"]["positives"] += $vote->Downtown->positives;
                    $data[$vote->geo_area]["Downtown"]["negatives"] += $vote->Downtown->negatives;
                    $data[$vote->geo_area]["Middletown"]["positives"] += $vote->Middletown->positives;
                    $data[$vote->geo_area]["Middletown"]["negatives"] += $vote->Middletown->negatives;
                    $data[$vote->geo_area]["Uptown"]["positives"] += $vote->Uptown->positives;
                    $data[$vote->geo_area]["Uptown"]["negatives"] += $vote->Uptown->negatives;
                }
            }

            //Neighbourhood
            $countByNb = Analytics::getCountVotesByNeighbourhood($voteEvents[0]->vote_key, $cbKey);

            $firstByNb = Analytics::getVotesFirstByNeighbourhood($voteEvents[0]->vote_key, $cbKey);

            $secondByNb = Analytics::getVotesSecondByNeighbourhood($voteEvents[0]->vote_key, $cbKey);

            return view('empaville.dashboards.geoArea', compact('lang', 'cbKey', 'votesByNb', 'data','countByNb', 'firstByNb', 'secondByNb'));
        }catch(Exception $e) {
            return redirect()->back()->withErrors(["empavilleDashboard.proposals.error" => $e->getMessage()]);
        }

    }


    public function byAge($cbKey,Request $request){
        try{
            $lang = $request->lang;
            App::setLocale($lang);
            //Request the first voteEvent key
            $voteEvents = CB::getCbVotes($cbKey);

            if(empty($voteEvents[0])){
                //Page error no data
                dd('No data found');
            }


            //Age

            $votesByAge = Analytics::getCountVotesByAge($voteEvents[0]->vote_key, $cbKey);

            $firstByAge = Analytics::getVotesFirstByAge($voteEvents[0]->vote_key, $cbKey);

            $secondByAge = Analytics::getVotesSecondByAge($voteEvents[0]->vote_key, $cbKey);

            return view('empaville.dashboards.byAge', compact('lang', 'cbKey','votesByAge', 'firstByAge', 'secondByAge'));

        }catch(Exception $e) {
            return redirect()->back()->withErrors(["empavilleDashboard.proposals.error" => $e->getMessage()]);
        }
    }



    public function proposalsByProfession($cbKey,Request $request){
        try {
            $lang = $request->lang;
            App::setLocale($lang);
            //Request the first voteEvent key
            $voteEvents = CB::getCbVotes($cbKey);

            if (empty($voteEvents[0])) {
                //Page error no data
                dd('No data found');
            }

            $votesByProfession = Analytics::getVotesByProfession($voteEvents[0]->vote_key, $cbKey);
            $professions = $votesByProfession->professions;
            $votesByProfession = $votesByProfession->data;
            foreach ($votesByProfession as $key => $vote){

                $votesByProfession[$key]['total'] = 0;

                foreach ($vote['professions'] as $profession){
                    $votesByProfession[$key]['total'] += $profession['balance'];
                }
            }

            $byProfession = Analytics::getCountVotesByProfession($voteEvents[0]->vote_key, $cbKey);

            $firstByProfession = Analytics::getVotesFirstByProfession($voteEvents[0]->vote_key, $cbKey);

            $secondByProfession = Analytics::getVotesSecondByProfession($voteEvents[0]->vote_key, $cbKey);
            return view('empaville.dashboards.professions', compact('lang', 'cbKey', 'votesByProfession', 'professions', 'byProfession', 'firstByProfession', 'secondByProfession'));

        }catch(Exception $e) {
            return redirect()->back()->withErrors(["empavilleDashboard.proposals.error" => $e->getMessage()]);
        }
    }


    public function byChannel($cbKey,Request $request){
        try {
            $lang = $request->lang;
            App::setLocale($lang);

            //Request the first voteEvent key
            $voteEvents = CB::getCbVotes($cbKey);

            if (empty($voteEvents[0])) {
                //Page error no data
                dd('No data found');
            }
            //Get Votes by Channel

            $channels = ['kiosk','pc','mobile','tablet','other'];
            $votesByChannel = Analytics::getVotesByChannel($voteEvents[0]->vote_key, $cbKey);
            foreach ($votesByChannel as $key => $vote){

                $votesByChannel[$key]['total'] = 0;

                foreach ($vote['channels'] as $channel){
                    $votesByChannel[$key]['total'] += $channel['balance'];
                }
            }

            $byChannel = Analytics::getCountVotesByChannel($voteEvents[0]->vote_key, $cbKey);

            $firstByChannel = Analytics::getVotesFirstByChannel($voteEvents[0]->vote_key, $cbKey);

            $secondByChannel = Analytics::getVotesSecondByChannel($voteEvents[0]->vote_key, $cbKey);

            return view('empaville.dashboards.byChannel', compact('lang', 'cbKey', 'votesByChannel', 'channels', 'byChannel', 'firstByChannel', 'secondByChannel'));

        }catch(Exception $e) {
            return redirect()->back()->withErrors(["empavilleDashboard.proposals.error" => $e->getMessage()]);
        }

    }







    public function proposals($cbKey,Request $request){
        try{
            $lang = $request->lang;
            App::setLocale($lang);
            //Request the first voteEvent key
            $voteEvents = CB::getCbVotes($cbKey);

            if(empty($voteEvents[0])){
                //Page error no data
                dd('No data found');
            }

            $response = Analytics::getVotes($voteEvents[0]->vote_key, $cbKey);

            if((!empty($response->json()->data) && (!empty($response->json()->summary)) )){
                $voteSession["byProposal"] = $response->json()->data;
                $voteSession["summary"] = $response->json()->summary;
            }
            // Prepare Data for Top Ten
            if(sizeof($voteSession["byProposal"]) > 8){
                $arrayTop = array_slice($voteSession["byProposal"] , 0, 8);
                $voteSession["top"] = $arrayTop;
            }else{
                $voteSession["top"] = $voteSession["byProposal"];
            }

            return view('empaville.dashboards.index', compact('lang', 'cbKey','voteSession', 'votesByGender','firstByGender', 'secondByGender', 'countByNb','firstByNb', 'secondByNb', 'countByAge', 'firstByAge', 'secondByAge'));

        }catch(Exception $e) {

            return redirect()->back()->withErrors(["empavilleDashboard.proposals.error" => $e->getMessage()]);
        }

    }
}
