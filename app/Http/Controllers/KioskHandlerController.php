<?php

namespace App\Http\Controllers;

use App\ComModules\Auth;
use App\ComModules\CB;
use App\ComModules\Orchestrator;
use App\ComModules\Vote;
use Illuminate\Http\Request;
use App\One\One;
use Session;
use View;
use Exception;
use Illuminate\Support\Collection;


class KioskHandlerController extends Controller
{

    //**************************************KIOSK HARDWARE****************************************************************************************************************************************************


    //AUTHENTICATION
    public function authenticateRFID(Request $request)
    {
        try {

            // Authenticate with RFID

            $authToken = Auth::setAuthenticateRFID($request->json("rfid"));

            //get logged user information

            $response = Auth::getUser();
            $userInformation = $response->user;

            //get kiosk information
            $kiosk = Orchestrator::getKiosk($request->header('X-KIOSK-KEY'));

            //get vote status
            if (!empty($userKey)) {
                $voteData = Vote::getVoteStatus($kiosk->event_key, $userKey);

            } else {
                $voteData = Vote::getVoteStatus($kiosk->event_key);
            }

            return response()->json(['token' => $authToken, 'user' => $userInformation,'voteStatus' =>$voteData], 200);

        } catch (Exception $e) {
            return response()->json(['error' => $e], 500);
        }

    }

    public function logout(Request $request)
    {
        try {

            Auth::logout();

            return response()->json('OK', 200);

        } catch (Exception $e) {
            return response()->json(['error' => $e], 500);
        }

        return response()->json(['error' => 'Invalid Credentials'], 401);
    }


    public function getUserParameters(Request $request, $userKey)
    {
        try {
            $response = Auth::getUserParameters($userKey);

            return response()->json($response, 200);
        } catch (Exception $e) {
            return response()->json(['error' => $e], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }


    //GET KIOSK INFORMATION WITH CBS TOPICS AND TOPICS PARAMETERS WITH TRANSLATIONS

    public function getOrchestratorKiosk(Request $request, $kioskKey)
    {
        try {

            //get kiosk information
            $kiosk = Orchestrator::getKiosk($kioskKey);

            //get cb all information with topics and topic parameters

            $cb = CB::getCBAndTopics($kiosk->entity_cb->cb_key, []);

            //get user keys of user who created the topic
            $topics=$cb->topics;
            $userKeys = collect($topics)->pluck('created_by')->unique()->toArray();


            //get user information
            if (count($userKeys) > 0) {
                $response = Auth::listUser($userKeys);
                $usersNames=collect($response)->keyBy('user_key');
                //replace topics created by userkey with created by username
                foreach ($topics as $topic){
                    if($usersNames->has($topic->created_by)){
                        $topic->created_by = $usersNames->get($topic->created_by)->name;
                    }
                }
                $cb->topics=$topics;
            }
            //get cb parameters

            $cbWithParameters = CB::getCb($kiosk->entity_cb->cb_key);


            //get vote configuration
            $voteConfiguration=Vote::getAllShowEvents($kiosk->event_key);

            return response()->json(['kiosk' => $kiosk, 'cbwithTopics' => $cb,'cbWithParameters' => $cbWithParameters,'voteConfiguration' => $voteConfiguration],200);

        } catch (Exception $e) {
            return response()->json(['error' => $e], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }


    //VOTE

    public static function vote(Request $request)
    {

        try {

            $userKey = isset($request->userKey) ? $request->userKey : null;


//            return response()->json([$userKey,$eventKey,$voteKey,$value,$source,$request->header()]);

            $response = Vote::setVote($request->voteKey, $request->topicKey, $request->value, $request->source, $userKey);
            $data = [];
            $data["vote"] = $response->value;

            if(isset($response->summary->total))
                $data["total"] = $response->summary->total;

            if(isset($response->summary->positive))
                $data["positive"] = $response->summary->positive;

            if(isset($response->summary->negative))
                $data["negative"] = $response->summary->negative;

            if(isset($response->total_votes)) {
                $totals = json_decode(json_encode($response->total_votes),true);
                $data['totalPositive'] = isset($totals[$request->topicKey]) ? $totals[$request->topicKey]['positive'] : '0';
                $data['totalNegative'] = isset($totals[$request->topicKey]) ? $totals[$request->topicKey]['negative'] : '0';
            }
            return json_encode($data);

        } catch (Exception $e) {
            return json_encode($e->getMessage());
        }

    }


    //**************************************KIOSK MOBILE****************************************************************************************************************************************************

    //GET KIOSK INFORMATION TO MOBILE APP

    public function loginWithCredentials(Request $request){

        try{

            $response = Auth::login($request->email, $request->password);
            $authToken = $response->token;
            return response()->json(['token' => $authToken,], 200);

        } catch (Exception $e) {
            return response()->json(['error' => $e], 500);
        }
    }


    public function loginWithQRCode(Request $request){

        try {
            $response = Auth::authenticateAlphanumeric($request->code);
            $authToken = $response->token;
            return response()->json(['token' => $authToken,], 200);
        } catch (Exception $e) {
            return response()->json(['error' => $e], 500);
        }

    }


    public function afterLoginGetKioskInformationToMobile(Request $request)
    {
        try {

            //get logged user information
            $response = Auth::getUser();
            $userInformation = $response->user;

            //get kiosk information
            $kiosk = Orchestrator::getKiosk($request->header('X-KIOSK-KEY'));

            //get cb all information with topics and topic parameters

            $cb = CB::getCBAndTopics($kiosk->entity_cb->cb_key, []);

            //get user keys of user who created the topic
            $topics = $response->json()->topics;
            $userKeys = collect($topics)->pluck('created_by')->unique()->toArray();


            //get user information
            if (count($userKeys) > 0) {
                $response = Auth::listUser($userKeys);
                $usersNames = collect($response)->keyBy('user_key');
                //replace topics created by userkey with created by username
                foreach ($topics as $topic) {
                    if ($usersNames->has($topic->created_by)) {
                        $topic->created_by = $usersNames->get($topic->created_by)->name;
                    }
                }
            }

            //get vote status
            if (!empty($userKey)) {
                $voteData = Vote::getVoteStatus($kiosk->event_key, $userKeys);

            } else {
                $voteData = Vote::getVoteStatus($kiosk->event_key, null);
            }

            $voteStatus = collect($voteData)->toArray();

            //filter necessary topic information
            $topicWithFilteredInformation=[];
            $i=0;
            foreach ($topics as $topic){
                $topic = collect($topic)->only(['topic_key','created_by','created_at','contents','title','first_post','statistics','parameters'])->toArray();
                //topic key,title,summary,contents,
                $topicKey=$topic["topic_key"];
                $topicWithFilteredInformation[$i]["topic_key"] = isset($topicKey) ?  $topicKey :"";
                $topicWithFilteredInformation[$i]["title"] = isset($topic["title"]) ? $topic["title"] :"";
                $topicWithFilteredInformation[$i]["summary"] = isset($topic["contents"]) ? $topic["contents"]:"";
                $topicWithFilteredInformation[$i]["created_by"] = isset($topic["created_by"]) ? $topic["created_by"] :"";
                $topicWithFilteredInformation[$i]["created_at"] = isset($topic["created_at"]) ? $topic["created_at"] :"";
                $topicWithFilteredInformation[$i]["contents"]= isset($topic["first_post"]->contents) ? $topic["first_post"]->contents:"";


                //topic votes
                $votes=collect($voteStatus["votes"])->toArray();
                $votesPerIdeaPerUser=isset($votes[$topicKey]->value) ?  $votes[$topicKey]->value : 0;
                $topicWithFilteredInformation[$i]["vote"] = $votesPerIdeaPerUser;

                //topic parameters
                $topicParameters=[];
                $j=0;
                foreach ($topic["parameters"] as $topicParameter) {
                    $topicParameters[$j]["name"] = $topicParameter->parameter;
                    $topicParameters[$j]["description"] = $topicParameter->description;
                    $topicParameters[$j]["created_at"] = $topicParameter->created_at;
                    $topicParameters[$j]["visible_in_list"] = $topicParameter->visible_in_list;
                    $topicParameters[$j]["visible"] = $topicParameter->visible;
                    $topicParameters[$j]["code"] = $topicParameter->code;
                    $topicParameters[$j]["parameter_type_id"] = $topicParameter->parameter_type_id;
                    if($topicParameter->code=="google_maps" || $topicParameter->code=="image_map"){
                        $pivotValue=$topicParameter->pivot->value;
                    }
                    else if ($topicParameter->type->code == 'dropdown' || $topicParameter->type->code == 'category' || $topicParameter->type->code == 'budget'  || $topicParameter->type->code == "radio_buttons") {
                        foreach ($topicParameter->options as $temp) {
                            if($temp->id==$topicParameter->pivot->value) {
                                $pivotValue = $temp->label;
                            }
                        }
                    }
                    else{
                        $pivotValue=$topicParameter->pivot->value;
                    }
                    $topicParameters[$j]["value"] = $pivotValue;

                    $j++;
                }
                $topicWithFilteredInformation[$i]["parameters"] = $topicParameters;

                $i++;
            }
            $cb->topics=$topicWithFilteredInformation;

            //get vote configuration
            $voteConfiguration = Vote::getAllShowEvents($kiosk->event_key);


            return response()->json(['userInformation' => $userInformation, 'kiosk' => $kiosk, 'cbwithTopics' => $cb, 'voteConfiguration' => $voteConfiguration , 'voteStatus' =>$voteStatus], 200);

        }
        catch (Exception $e) {
            return response()->json(['error' => $e], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }



    public function getKioskInformationToMobile(Request $request)
    {
        try {

            //get kiosk information
            $kiosk = Orchestrator::getKiosk($request->header('X-KIOSK-KEY'));

            //get cb all information with topics and topic parameters
            $cb = CB::getCBAndTopics($kiosk->entity_cb->cb_key, "");

            //get user keys of user who created the topic
            $topics = $cb->topics;
            $userKeys = collect($topics)->pluck('created_by')->unique()->toArray();


            //get user information
            if (count($userKeys) > 0) {
                $response = Auth::listUser($userKeys);
                $usersNames = collect($response)->keyBy('user_key');
                //replace topics created by userkey with created by username
                foreach ($topics as $topic) {
                    if ($usersNames->has($topic->created_by)) {
                        $topic->created_by = $usersNames->get($topic->created_by)->name;
                    }
                }
            }

            //filter necessary topic information
            $topicWithFilteredInformation=[];
            $i=0;
            foreach ($topics as $topic){
                $topic = collect($topic)->only(['topic_key','created_by','created_at','contents','title','first_post','statistics','parameters'])->toArray();
                //topic key,title,summary,contents,
                $topicKey=$topic["topic_key"];
                $topicWithFilteredInformation[$i]["topic_key"] = isset($topicKey) ?  $topicKey :"";
                $topicWithFilteredInformation[$i]["title"] = isset($topic["title"]) ? $topic["title"] :"";
                $topicWithFilteredInformation[$i]["summary"] = isset($topic["contents"]) ? $topic["contents"]:"";
                $topicWithFilteredInformation[$i]["created_by"] = isset($topic["created_by"]) ? $topic["created_by"] :"";
                $topicWithFilteredInformation[$i]["created_at"] = isset($topic["created_at"]) ? $topic["created_at"] :"";
                $topicWithFilteredInformation[$i]["contents"]= isset($topic["first_post"]->contents) ? $topic["first_post"]->contents:"";

                //topic parameters
                $topicParameters=[];
                $j=0;
                foreach ($topic["parameters"] as $topicParameter) {
                    $topicParameters[$j]["name"] = $topicParameter->parameter;
                    $topicParameters[$j]["description"] = $topicParameter->description;
                    $topicParameters[$j]["created_at"] = $topicParameter->created_at;
                    $topicParameters[$j]["visible_in_list"] = $topicParameter->visible_in_list;
                    $topicParameters[$j]["visible"] = $topicParameter->visible;
                    $topicParameters[$j]["code"] = $topicParameter->code;
                    $topicParameters[$j]["parameter_type_id"] = $topicParameter->parameter_type_id;
                    if($topicParameter->code=="google_maps" || $topicParameter->code=="image_map"){
                        $pivotValue=$topicParameter->pivot->value;
                    }
                    else if ($topicParameter->type->code == 'dropdown' || $topicParameter->type->code == 'category' || $topicParameter->type->code == 'budget'  || $topicParameter->type->code == "radio_buttons") {
                        foreach ($topicParameter->options as $temp) {
                            if($temp->id==$topicParameter->pivot->value) {
                                $pivotValue = $temp->label;
                            }
                        }
                    }
                    else{
                        $pivotValue=$topicParameter->pivot->value;
                    }
                    $topicParameters[$j]["value"] = $pivotValue;

                    $j++;
                }
                $topicWithFilteredInformation[$i]["parameters"] = $topicParameters;

                $i++;
            }
            $cb->topics=$topicWithFilteredInformation;


            //get vote configuration
            $voteConfiguration = Vote::getAllShowEvents($kiosk->event_key);

            return response()->json([ 'kiosk' => $kiosk, 'cbwithTopics' => $cb, 'voteConfiguration' => $voteConfiguration], 200);
        }
        catch (Exception $e) {
            return response()->json(['error' => $e], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }






}