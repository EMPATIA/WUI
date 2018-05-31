<?php

namespace App\Http\Controllers;

use App\ComModules\Analytics;
use App\ComModules\CB;
use App\ComModules\Vote;
use App\Http\Requests\PostRequest;
use App\One\One;
use Carbon\Carbon;
use Exception;
use FontLib\TrueType\Collection;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\App;

class EmpavillePresentationController extends Controller
{
    public function __construct()
    {
    }


    public function index($cbKey,Request $request)
    {
        $lang = $request->presentationLang;
        App::setLocale($lang);
        $id=1;
        return redirect()->action('EmpavillePresentationController@next', ['cbKey' => $cbKey, 'id'=> $id,'lang' => $lang]);
    }



    public function next($cbKey, $id,Request $request)
    {
        $lang = $request->lang;
        App::setLocale($lang);
        if($id == '13'){
            return $this->totals($cbKey,$id);
        }
        return view('empaville.presentation.'.$lang.'.page'.$id, compact('cbKey', 'id','lang'));
    }


    public function showProposal($cbKey,$id,$count,Request $request)
    {
        $lang = $request->lang;
        App::setLocale($lang);
        if($count == 0){
            $id--;
            return redirect()->action('EmpavillePresentationController@next',['cbKey' => $cbKey, 'id'=> $id,'lang' => $lang]);
        }
        if($count == 5) {
            $id++;
            return redirect()->action('EmpavillePresentationController@next',['cbKey' => $cbKey, 'id'=> $id,'lang' => $lang]);
        }

        try {
            $usersNames = '';
            $usersKeys = '';
            Switch($count){
                case 1:
                    $usersKeys = 'zyZjJezUysOZKcBLuBVsfIKw6YNckVyr';
                    $usersNames = ['user_key' => 'defaultEMPATIAmoderator1','name'=> 'Moderator 1'];
                    break;
                case 2:
                    $usersKeys = '5gB2Uu5P2CkiiBowY1x6wuDJJ33v0FsQ';
                    $usersNames = ['user_key' => 'defaultEMPATIAmoderator2','name'=> 'Moderator 2'];
                    break;
                case 3:
                    $usersKeys = 'EKwNT7i5kW143QT4FXTCrISKcoqwaDMm';
                    $usersNames = ['user_key' => 'defaultEMPATIAmoderator3','name'=> 'Moderator 3'];
                    break;
                case 4:
                    $usersKeys = 'Jt17hlQ4ufj4iivMZ2JTODUleXR3IGks';
                    $usersNames = ['user_key' => 'defaultEMPATIAmoderator4','name'=> 'Moderator 4'];
                    break;
            }

            $ideasMenu = [];

            $ideasResponse = CB::topicsWithLastPost($request, $cbKey);
            $ideas = [];
            foreach ($ideasResponse as $ideaResp){
                if($ideaResp->created_by == $usersKeys){
                    $ideas []= $ideaResp;
                }
            }
            $categoriesNameById = [];

            foreach ($ideas as $idea) {

                foreach($idea->parameters as $parameters){

                    if($parameters->type->code != 'image_map') {
                        $value = $parameters->pivot->value;


                        $temp = '';
                        if (array_key_exists($value, $ideasMenu)) {
                            $temp = $ideasMenu[$value];
                            $temp .= ','.$idea->id;
                        }else{
                            $temp .= $idea->id;
                        }

                        $ideasMenu[$value] = $temp;
                    }
                }
            }



            $parameters = [];
            $response = CB::getCbParametersOptions($cbKey);
            $CBparameters = $response->parameters;

            foreach ($CBparameters as $parameter) {
                $name = $parameter->parameter;
                $parameterOptions = [];
                $options = $parameter->options;
                foreach ($options as $option) {
                    $parameterOptions[] = array('id' => $option->id, 'name' => $option->label);
                    $categoriesNameById[$option->id] = $option->label;
                }


                $parameters[$name] = array('id' => $parameter->id, 'name' => $name, 'options' => $parameterOptions);
            }

            $location =[];
            foreach ($ideas as $idea){
                foreach ($idea->parameters as $parameter){
                    if($parameter->code == 'image_map'){
                        $location [$idea->id] = One::verifyEmpavilleGeoArea($parameter->pivot->value);
                    }
                }
            }
            return view('empaville.presentation.'.$lang.'.page' . $id,compact('count','ideas','usersNames','cbKey','parameters','ideasMenu','categoriesNameById','location','id','lang'));
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["idea.index" => $e->getMessage()]);
        }
    }



    public function totals($cbKey,$id,$lang){
        try{
            //Request the first voteEvent key

            $voteEvents = CB::getCbVotes($cbKey);

            if(empty($voteEvents[0])){
                //Page error no data
                dd('No data found');
            }

            $response = Analytics::getVotes($voteEvents[0]->vote_id, $cbKey);

            if((!empty($response->data) && (!empty($response->summary)) )){
                $voteSession["byProposal"] = $response->data;
                $voteSession["summary"] = $response->summary;
            }
            // Prepare Data for Top Ten
            if(sizeof($voteSession["byProposal"]) > 8){
                $arrayTop = array_slice($voteSession["byProposal"] , 0, 8);
                $voteSession["top"] = $arrayTop;
            }else{
                $voteSession["top"] = $voteSession["byProposal"];
            }
            return view('empaville.presentation.'.$lang.'.page'.$id,compact('voteSession', 'cbKey', 'id','lang'));
        }catch(Exception $e) {
            return redirect()->back()->withErrors(["empavillePresentation.proposals.error" => $e->getMessage()]);
        }
    }


    public function closeVotes(Request $request){
        try {
            $response = CB::getVotes($request->cbKey);
            $voteKey = $response->data[0]->vote_key;

            $response = Vote::getVote($voteKey);
            $startDate = $response->start_date;
            $startTime = $response->start_time;
            $now = Carbon::now();
            $endTime = $startTime =  $now->hour.':00' ;
            $endDate = $now->toDateString();

            $data = Collection::make(['voteKey' => $voteKey, 'startDate' => $startDate, 'endDate' => $endDate, 'startTime' => $startTime, 'endTime' => $endTime]);

            $response = Vote::updateVoteEvent($data, null);
        }
        catch(Exception $e) {
            return "false";
        }
    }



    public function openVotes(Request $request){
        try {
            $response = CB::getVotes($request->cbKey);
            $voteKey = $response->data[0]->vote_key;


            $response = Vote::getVote($voteKey);

            $now = Carbon::now();

            $startTime =  $now->hour.':00';
            $startDate = $now->toDateString();
            $endDate = $now->addDay(1)->toDateString();

            $data = Collection::make(['voteKey' => $voteKey, 'startDate' => $startDate, 'endDate' => $endDate, 'startTime' => $startTime, 'endTime' => null]);

            $response = Vote::updateVoteEvent($data, null);
        }
        catch(Exception $e) {
            return "false";
        }
    }



    public function closeProposals(Request $request){
        try {
            CB::updateBlock($request->cbKey);
        }
        catch(Exception $e) {
            return "false";
        }
    }


}
