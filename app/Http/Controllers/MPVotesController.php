<?php

namespace App\Http\Controllers;

use App\ComModules\CB;
use App\ComModules\MP;
use App\ComModules\Vote;
use Exception;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Session;

class MPVotesController extends Controller
{
    /**
     * MPVoteController constructor.
     */
    public function __construct()
    {
    }


    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function index(Request $request)
    {
        $mpKey = $request->mp_key;
        return redirect()->action('MPsController@showConfigurations', $mpKey);
    }


    /** Show the form for creating a new resource.
     * @param Request $request
     * @return $this|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create(Request $request)
    {

        try {
            $operatorKey = $request->operator_key;
            if(empty($operatorKey)){
                throw new Exception(trans('privateMPVotes.error_in_operator'));
            }

            $operator = MP::getOperator($operatorKey);
            if(empty($operator->pad_key)){
                throw new Exception(trans('privateMPVotes.no_pad_associated_to_vote'));
            }
            $genericConfigs = CB::getVotesConfigurations();
            $methodGroup = Vote::getListMethodGroups();
//            $advancedConfigs = Vote::getGeneralConfigurationTypes();
//            $cb = CB::getCbConfigurations($operator->parent_key);

            $title = trans('privateMPVotes.create_vote');

            $data = [];
            $data['genericConfigs'] = $genericConfigs;
            $data["operator"] = $operator;
            $data["methodGroup"] = $methodGroup;

            return view('private.mps.vote.vote', $data);
        } catch (Exception $e) {
            return redirect()->back()->withErrors([trans('privateMPVotes.create_vote_error') => $e->getMessage()]);
        }


    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        try {
            $operatorKey = $request->operator_key ?? null;
            $cbKey = $request->cb_key ?? null;
            $mpKey = $request->mp_key ?? null;
            if(empty($operatorKey) || empty($cbKey) || empty($mpKey)){
                throw new Exception('operator_error');
            }

            $configurations = [];
            $genericConfigs = [];

            foreach ($request->all() as $key => $value) {
                if (strpos($key, 'config_') !== false) {
                    $key = str_replace("config_", "", $key);
                    if ($value != '')
                        $configurations[] = ['configuration_id' => $key, 'value' => $value];
                }elseif (strpos($key, 'genericConfig_') !== false) {
                    $key = str_replace("genericConfig_", "", $key);
                    if ($value != '')
                        $genericConfigs[] = array('vote_configuration_key' => $key, 'value' => $value);
                }
            }

            $data = [
                'methodSelect' => !empty($request->input('methodSelect')) ? $request->input('methodSelect') : null ,
                'startDate' => !empty($request->input('startDate')) ? $request->input('startDate') : null ,
                'endDate' => !empty($request->input('endDate')) ? $request->input('endDate') : null ,
                'startTime' => !empty($request->input('startTime')) ? $request->input('startTime') : null ,
                'endTime' => !empty($request->input('endTime')) ? $request->input('endTime') : null ,
                'configurations' => $configurations
            ];

            // Vote setVoteEventWithData
            $newVoteEvent = Vote::setVoteEventWithData($data);

//            //verify generic configs
//            $genericConfigs = [];
//            foreach ($request->all() as $key => $value) {
//                if (strpos($key, 'genericConfig_') !== false) {
//                    $key = str_replace("genericConfig_", "", $key);
//                    if ($value != '')
//                        $genericConfigs[] = array('vote_configuration_key' => $key, 'value' => $value);
//                }
//            }

            $data = [
                'cbKey' => $cbKey,
                'methodSelect' => !empty($request->input('methodSelect')) ? $request->input('methodSelect') : null ,
                'name' => !empty($request->input('voteName')) ? $request->input('voteName') : null ,
                'genericConfigs' => $genericConfigs
            ];

            // Generic configurations
            CB::setCbVoteWithData($data, $newVoteEvent);
            MP::updateOperator($operatorKey,$newVoteEvent->key);



            Session::flash('message', trans('privateMPVotes.store_ok'));
            return redirect()->action('MPsController@showConfigurations', ['mp_key' => $mpKey]);


        } catch (Exception $e) {
            return redirect()->back()->withErrors([trans('privateMPVote.store_error') => $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     * @param $operatorKey
     * @return MPVotesController|\Illuminate\Http\RedirectResponse
     */
    public function show($operatorKey)
    {
        try {
            $operator = MP::getOperator($operatorKey);
            $genericConfigs = CB::getVotesConfigurations();
            $vote = CB::getCbVote($operator->pad_key,$operator->component_key);
            $voteConfigs = [];
            foreach ($vote->vote_configurations as $vote_configuration) {
                $voteConfigs[$vote_configuration->vote_configuration_key] = $vote_configuration->value;
            }
            $name = $vote->name;

            $result = Vote::getAllShowEvents($operator->component_key);
            $voteEvent = $result[0];
            $html = $this->configurationsHtml($voteEvent, true);

            $data = [];
            $data["operator"] = $operator;
            $data["voteName"] = $name;
            $data["voteEvent"] = $voteEvent;
            $data["genericConfigs"] = $genericConfigs;
            $data["voteConfigs"] = $voteConfigs;
            $data["html"] = $html;

            return view('private.mps.vote.vote', $data);

        }catch (Exception $e){
            return redirect()->back()->withErrors([trans('privateMPVotes.create_error') => $e->getMessage()]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     * @param $operatorKey
     * @return MPVotesController|\Illuminate\Http\RedirectResponse
     */
    public function edit($operatorKey)
    {
        try {
            $operator = MP::getOperator($operatorKey);
            $genericConfigs = CB::getVotesConfigurations();
            $vote = CB::getCbVote($operator->pad_key,$operator->component_key);
            $voteConfigs = [];
            foreach ($vote->vote_configurations as $vote_configuration) {
                $voteConfigs[$vote_configuration->vote_configuration_key] = $vote_configuration->value;
            }
            $name = $vote->name;

            $result = Vote::getAllShowEvents($operator->component_key);
            $voteEvent = $result[0];
            $noEdit = false;
            $currentDate = date('Y-m-d');
            if ($voteEvent->start_date < $currentDate) {
                $noEdit = true;
            }
            $html = $this->configurationsHtml($voteEvent, $noEdit);

            $data = [];
            $data["operator"] = $operator;
            $data["voteName"] = $name;
            $data["voteEvent"] = $voteEvent;
            $data["genericConfigs"] = $genericConfigs;
            $data["voteConfigs"] = $voteConfigs;
            $data["html"] = $html;

            return view('private.mps.vote.vote', $data);

        }catch (Exception $e){
            return redirect()->back()->withErrors([trans('privateMPVotes.edit_error') => $e->getMessage()]);
        }
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param $operatorKey
     * @return MPVotesController|\Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $operatorKey)
    {
        try {
            $cbKey = $request->cb_key ?? null;
            $mpKey = $request->mp_key ?? null;
            $voteKey = $request->voteKey ?? null;
            if(empty($mpKey) || empty($cbKey)|| empty($voteKey)){
                throw new Exception('operator_error');
            }
            $configurations = [];
            foreach ($request->all() as $key => $value) {
                if (strpos($key, 'config_') !== false) {
                    $key = str_replace("config_", "", $key);
                    if ($value != '')
                        $configurations[] = ['configuration_id' => $key, 'value' => $value];
                }elseif (strpos($key, 'genericConfig_') !== false) {
                    $key = str_replace("genericConfig_", "", $key);
                    if ($value != '')
                        $genericConfigs[] = array('vote_configuration_key' => $key, 'value' => $value);
                }
            }

//            //verify generic configs
//            $genericConfigs = [];
//            foreach ($request->all() as $key => $value) {
//                if (strpos($key, 'genericConfig_') !== false) {
//                    $key = str_replace("genericConfig_", "", $key);
//                    if ($value != '')
//                        $genericConfigs[] = array('vote_configuration_key' => $key, 'value' => $value);
//                }
//            }

            // Generic configurations

            $voteEvent = Vote::updateVoteEvent($request, $configurations);
            $cbVote = CB::updateCbVote($request,$cbKey,$voteKey);

            Session::flash('message', trans('privateMPVotes.store_ok'));
            return redirect()->action('MPsController@showConfigurations', ['mp_key' => $mpKey]);


        } catch (Exception $e) {
            return redirect()->back()->withErrors([trans('privateMPVote.store_error') => $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     * @param $operatorKey
     * @return $this|string
     */
    public function destroy($operatorKey)
    {
        try {
            $operator = MP::getOperator($operatorKey);
            $cbVote = CB::deleteCbVote($operator->pad_key, $operator->component_key);
            MP::updateOperator($operatorKey,0);

            Session::flash('message', trans('privateMPVotes.delete_ok'));
            return action('MPsController@showConfigurations',$operator->mp->mp_key);

        } catch (Exception $e) {
            return redirect()->back()->withErrors([trans('privateMPVotes.delete_error') => $e->getMessage()])->getTargetUrl();
        }
    }


    /** Show modal delete confirmation
     * @param $operatorKey
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function delete($operatorKey){
        $data = array();
        $data['action'] = action("MPVotesController@destroy", ['operatorKey'=>$operatorKey]);
        $data['title'] = trans('privateMPVotes.delete');
        $data['msg'] = trans('privateMPVotes.are_you_sure_you_want_to_delete').' ?';
        $data['btn_ok'] = trans('privateMPVotes.delete');
        $data['btn_ko'] = trans('privateMPVotes.cancel');

        return view("_layouts.deleteModal", $data);
    }


    /** Pass configurations to html
     * @param $response
     * @param $show
     * @param bool $advancedConfig
     * @param null $voteId
     * @return string
     */

    private function configurationsHtml($response, $show, $advancedConfig = false, $voteId = null)
    {

        $disabled = '';
        $readonly = '';
        if ($show == true) {
            $disabled = 'pointer-events: none;';
            $readonly = 'readonly';
        }

        if( !empty($voteId) ){
            $suffixId = $voteId."_";
        } else {
            $suffixId = "";
        }


        $configurations = $response->configurations;
        $html = '';
        if (count($configurations) > 0) {

            $html .= '<div class="row">';
            $i = 0;
            foreach ($configurations as $config) {
                $html .= '<div class="col-xs-12 col-md-6">';
                // Form group

                $html .= '<div class="form-group">';
                $html .= '<label class="input-group btn-group-vertical">' . $config->name . '</label>';
//                $html .= '<input type="number" name="config[]" value="' . $config->id . '" hidden >';

                switch (strtoupper($config->parameter_type)) {
                    case 'BOOLEAN':
                        $html .= '<div class="row">';
                        $html .= '<div class="col-md-6">';
                        $html .= '<input id="inputYes'.$suffixId.$i.'" type="radio" name="'.($advancedConfig ? "advancedConfig_" : "config_").$suffixId.$config->id.'" value="1" style="margin-right:4px;'.$disabled.'"';
                        $html .= isset($config->pivot->value) ? ($config->pivot->value == '1' ? 'checked' : '') : 'checked';
                        $html .= '><label for="inputYes'.$suffixId.$i.'" style="margin-right:40px;font-weight: normal" > ' . trans('privateMPVotes.yes') . '</label>';
                        $html .= '</div>';
                        $html .= '<div class="col-md-6">';
                        $html .= '<input id="inputNo' . $i .$suffixId. '" type="radio" name="'.($advancedConfig ? "advancedConfig_" : "config_").$suffixId.$config->id.'" value="0" style="margin-right:4px;font-weight: normal;' . $disabled . '"';
                        $html .= isset($config->value) ? ($config->value == '0' ? 'checked' : '') : '';
                        $html .= '><label for="inputNo' . $i .$suffixId. '" style="font-weight: normal" > ' . trans('privateMPVotes.no') . '</label>';
                        $html .= '</div>';
                        $html .= '</div>';
                        break;
                    case 'NUMERIC':
                        $html .= '<input class="form-control" type="number" name="'.($advancedConfig ? "advancedConfig_" : "config_").$suffixId.$config->id.'" min="0" placeholder="Number" value="' . (isset($config->value) ? $config->value : 0) . '" ' . $readonly . '>';
                        break;
                }
                $html .= '</div>';

                $html .= '</div>';
                $i++;
            }

            $html .= '</div>';
        }


        return $html;
    }
}
