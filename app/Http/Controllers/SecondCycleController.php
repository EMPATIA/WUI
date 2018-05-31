<?php

namespace App\Http\Controllers;

use App\ComModules\Auth;
use App\ComModules\CB;
use App\ComModules\CM;
use App\ComModules\Orchestrator;
use App\ComModules\Files;
use App\Unimi\NestedCbs;
use App\Unimi\SecondCycleParameters;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\One\One;
use Carbon\Carbon;
use Datatables;
use Illuminate\Support\Facades\Route;
use Session;
use URL;
use View;
use Alert;
use Breadcrumbs;
use Exception;
use Validator;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;

class SecondCycleController extends Controller{
    private $nestedCbs;

    private function check_permission($action){
        return  true;
    }

    private function populateCbs($cbKey,$override = array()){
        $defaults = array("root_level" => SecondCycleParameters::getRootLevel(), "parameters" => SecondCycleParameters::getParameters(),"configurations" => SecondCycleParameters::getConfigurations());
        $parameters = Session::get('SITE-CONFIGURATION.second_cycle_parameters',false);
        if(is_string($parameters)){
            $tmp = json_decode($parameters,true);
            if(!$tmp){
                switch(strtolower($parameters)){
                    default:
                        $parameters = array();
                }
            }else{
                $parameters = $tmp;
            }
        }

        if(!empty($parameters)){
            foreach(array_keys($defaults) as $k){
                if(isset($parameters[$k]))
                    $defaults[$k] = $parameters[$k];
            }
        }

        foreach($override as $k => $v){
            $defaults[$k] = $v;
        }

        $this->nestedCbs = new NestedCbs($cbKey, isset($parameters['graph'])?$parameters['graph']:SecondCycleParameters::getGraph(), $defaults);
    }

    public function initialize($cbKey){
        try {
            if (!$this->check_permission('initialize')) {
                return redirect()->back()->withErrors(["cb.show" => trans('secondCycle.permission_message')]);
            }

            $this->populateCbs($cbKey, array("autopopulate" => true));

            Session::flash('message', trans('secondCycle.initialize_success'));
            return redirect()->back();
        } catch (Exception $exception) {
            return redirect()->back()->withErrors(["secondCycle.init" => $exception->getMessage()]);
        }
    }
    
    public function manageCb($type, $cbKey){
        try {
            if ($type != "project_2c") {
                return redirect()->back()->withErrors(["second_cycle.show" => trans('secondCycle.invalid_type')]);
            }
            
	    if (!$this->check_permission('initialize')) {
                return redirect()->back()->withErrors(["cb.show" => trans('secondCycle.permission_message')]);
            }

            $this->populateCbs($cbKey);

            $level = $this->nestedCbs->getLevelByCbKey($cbKey);
            if (is_null($level)) {
                return redirect()->back()->withErrors(["cb.show" => trans('secondCycle.not_init_secondcycle')]);
            }

            $data['cbKey'] = $cbKey;
            $data['type'] = $type;

            $data['title'] = trans('secondCycle.manageCbSecondCycle');
            $data['data'] = $this->getLevels();
            $data['sidebar'] = 'padsType';
            $data['active'] = 'project_2c_manageCb';
            $data['type'] = 'project_2c';

            return view('private.second_cycle.manageCb', $data);
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["secondCycle.manageCb" => $e->getMessage()]);
        }
    }

    private function getLevelName($level){
        if (trans("secondCycle.".$level) != "secondCycle.".$level)
            return trans("secondCycle.".$level);

        return $level;
    }

    public function manage($type, $cbKey){
        try {
            if ($type != "project_2c") {
                return redirect()->back()->withErrors(["second_cycle.show" => trans('secondCycle.invalid_type')]);
            }

            $this->populateCbs($cbKey);

            $graph = $this->nestedCbs->getGraph();

            $level = $this->nestedCbs->getLevelByCbKey($cbKey);
            if (is_null($level)) {
                return redirect()->back()->withErrors(["cb.show" => trans('secondCycle.not_init_secondcycle')]);
            }

            try {
                $this->nestedCbs->importTopics(null,"category");
            } catch (Exception $e) {
                return redirect()->back()->withErrors(["cb.show" => trans('secondCycle.not_init_secondcycle')]);
            }

            $data['cbKey'] = $cbKey;
            $data['type'] = $type;
            $data['root_level'] = $level;
            $data['root_level_name'] = $this->getLevelName($level);
            $data['data'] = $this->getNodes($level);
            $data['empty2C'] = empty($data['data']);
            $data['title'] = trans('secondCycle.manageSecondCycle');
            $data['sidebar'] = 'padsType';
            $data['active'] = 'project_2c_manage';
            $data['type'] = 'project_2c';

            return view('private.second_cycle.manage', $data);
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["secondCycle.manage" => $e->getMessage()]);
        }
    }

    public function update_files($cbKey,$cbKeyChild){

        $this->populateCbs($cbKey);

        $level = $this->nestedCbs->getLevelByCbKey($cbKey);
        if(is_null($level)){
            return response()->toJson(['message' => trans('secondCycle.not_init_secondcycle')],404);
        }

        $level = $this->nestedCbs->getLevelByCbKey($cbKeyChild);
        if(is_null($level)){
            return response()->toJson(['message' => trans('secondCycle.not_init_secondcycle')],404);
        }

        NestedCbs::clearGetFiles($cbKeyChild);
        return response()->json(["message" => "OK"]);
    }

    private function getNodes($root_level){
        $graph = $this->nestedCbs->getGraph();

        $space = $this->nestedCbs->getSpace();
        $data = array();
        $levels = $space->getLevels();
        $level_name = array();
	$has_news = array();
        foreach($levels as $l){
            if (trans("secondCycle.".$l) != "secondCycle.".$l){
                $level_name[$l] = trans("secondCycle.".$l);
            }else{
                $level_name[$l] = $l;
            }
	    $has_news[$l] = $this->nestedCbs->hasNews($l);
        }
        $topics = $space->getNodes();

        while(!empty($topics)){
            $topic = array_pop($topics);
            $parents = $this->nestedCbs->getParentsOfTopic($topic,1);
            $level = $space->getLevel($topic);
            $parent = ((count($parents)  == 0)?$root_level.'-root':$level."-".$parents[0]);
            $data[$parent][] = array(
                "id" => $topic,
                "title" => $space->getAttribute($topic,'title'),
                "type" => "node",
                "cb_key" => $this->nestedCbs->getCbKeyByLevel($level),
		"has_news" => $has_news[$level]
            );
            if(isset($graph[$level])){
                foreach($graph[$level] as $l){
                    $data[$topic][] = array(
                        "id" => $l."-".$topic,
                        "title" => $level_name[$l],
                        "type" => $l,
                        "cb_key" => $this->nestedCbs->getCbKeyByLevel($l),
			"has_news" => false
                    );
                }
            }
        }
        
	return $data;
    }
   
    private function getLevels(){

        $graph = $this->nestedCbs->getGraph();

	if (empty($graph)){
		$levels = array($this->nestedCbs->getRootLevel());
	}else{
		$levels = array();
		foreach($graph as $k => $v){
			$levels[] = $k;
			$levels = array_merge($levels, $v);
		}
	}
        $levels = array_unique($levels);
	$data = array();
        $level_name = array();
        foreach($levels as $l){
            if (trans("secondCycle.".$l) != "secondCycle.".$l){
                $level_name[$l] = trans("secondCycle.".$l);
            }else{
                $level_name[$l] = $l;
            }
        }

        while(!empty($levels)){
            $level = array_pop($levels);
	    $parents = $this->nestedCbs->getParents($level);
            $parent = ((count($parents)  == 0)?"root-radix":$parents[0]);
            $data[$parent][] = array(
                "id" => $level,
                "title" => $level_name[$level],
                "type" => "node",
                "cb_key" => $this->nestedCbs->getCbKeyByLevel($level)
            );
        }

        return $data;
    }
    
    public function create($cbKey, $level, $parentTopicKey = null){
        if(!$this->check_permission('create')){
            return redirect()->back()->withErrors(["second_cycle.show" => trans('secondCycle.permission_message')]);
        }

        if(Session::has('filesToUpload')){
            Session::forget('filesToUpload');
        }

        $this->populateCbs($cbKey);

        $parents = $this->nestedCbs->getParents($level,1);
        $graph = $this->nestedCbs->getGraph();
        $levels = array();
        foreach($parents as $p){
            $tmp = (isset($graph[$p]))?$graph[$p]:array();
            $levels = array_merge($tmp,$levels);
        }

        if(!empty($levels) && is_null($parentTopicKey) || !empty($levels) && !in_array($level,$levels)){
            return redirect()->back()->withErrors(["second_cycle.show" => trans('secondCycle.invalid_level')]);
        }

        $this->nestedCbs->importTopics($parents);

        if(!is_null($parentTopicKey)){
            $parent_level = $this->nestedCbs->getSpace()->getLevel($parentTopicKey);
            if(!in_array($parent_level,$parents))
                return redirect()->back()->withErrors(["second_cycle.show" => trans('secondCycle.invalid_parent_topic')]);
        }

        $configurations = $this->nestedCbs->getConfigurationsByLevel($level);
        $allowFiles = [];
        if( CB::checkCBsOption($configurations, 'ALLOW-FILES') ){
            $allowFiles[] = "docs";
        }

        if( CB::checkCBsOption($configurations, 'ALLOW-PICTURES')  ){
            $allowFiles[] = "images";
        }

        $data['cbKey'] = $cbKey;
        $data['level'] = $level;
        $data['cbKeyChild'] = $this->nestedCbs->getCbKeyByLevel($level);
        $data['parentTopicKey'] = $parentTopicKey;
        $data['parents'] = array_merge(array($parentTopicKey),$this->nestedCbs->getParentsOfTopic($parentTopicKey));
        $data['topicKey'] = null;
        $data['space'] = $this->nestedCbs->getSpace();
        $data['parameters'] = $this->nestedCbs->getParameters($level);
        $data['configurations'] = $configurations;
        $data['allowFiles'] = $allowFiles;
        $data['title'] = trans('secondCycle.create')." (".$this->getLevelName($level).")";
        $data['uploadKey'] = Files::getUploadKey();
        $data['sidebar'] = 'padsType';
        $data['active'] = 'project_2c_manage';
        $data['type'] = 'project_2c';

        return view('private.second_cycle.create', $data);
    }

    public function edit($cbKey, $topicKey){
        if(!$this->check_permission('update')){
            return redirect()->back()->withErrors(["second_cycle.show" => trans('secondCycle.permission_message')]);
        }

        if(Session::has('filesToUpload')){
            Session::forget('filesToUpload');
        }

        $this->populateCbs($cbKey);

        try{
            $topic = CB::getTopicDataWithChilds($topicKey);
        }catch(Exception $e){
            return redirect()->back()->withErrors(["cb.show" => trans('secondCycle.topic_invalid')]);
        }

        $level = $this->nestedCbs->getLevelByCbKey($this->nestedCbs->getCbKeyByCbId($topic->topic->cb_id));

        if(!$level){
            return redirect()->back()->withErrors(["second_cycle.show" => trans('secondCycle.invalid_level')]);
        }

        $levels = $this->nestedCbs->getParents($level);
        $levels[] = $level;

        $this->nestedCbs->importTopics($levels);

        $configurations = $this->nestedCbs->getConfigurationsByLevel($level);
        $allowFiles = [];
        if( CB::checkCBsOption($configurations, 'ALLOW-FILES') ){
            $allowFiles[] = "docs";
        }

        if( CB::checkCBsOption($configurations, 'ALLOW-PICTURES')  ){
            $allowFiles[] = "images";
        }

        $data['cbKey'] = $cbKey;
        $data['level'] = $level;
        $data['parents'] = $this->nestedCbs->getParentsOfTopic($topicKey);
        $data['cbKeyChild'] = $this->nestedCbs->getCbKeyByLevel($level);
        $data['topicKey'] = $topicKey;
        $data['space'] = $this->nestedCbs->getSpace();
        $data['parameters'] = $this->nestedCbs->getParameters($level);
        $data['configurations'] = $configurations;
        $data['allowFiles'] = $allowFiles;
        $data['uploadKey'] = Files::getUploadKey();
        $data['title'] = trans('secondCycle.edit')." ".$data['space']->getAttribute($topicKey,'title');
        $data['post'] = $data['space']->getAttribute($topicKey,'post');

        return view('private.second_cycle.edit', $data);
    }

    public function internalShow($cbKey, $topicKey){
        if(!$this->check_permission('update')){
            return redirect()->back()->withErrors(["second_cycle.show" => trans('secondCycle.permission_message')]);
        }

        if(Session::has('filesToUpload')){
            Session::forget('filesToUpload');
        }

        $this->populateCbs($cbKey);

        try{
            $topic = CB::getTopicDataWithChilds($topicKey);
        }catch(Exception $e){
            return redirect()->back()->withErrors(["cb.show" => trans('secondCycle.topic_invalid')]);
        }

        $level = $this->nestedCbs->getLevelByCbKey($this->nestedCbs->getCbKeyByCbId($topic->topic->cb_id));

        if(!$level){
            return redirect()->back()->withErrors(["second_cycle.show" => trans('secondCycle.invalid_level')]);
        }

        $levels = $this->nestedCbs->getParents($level);
        $levels[] = $level;

        $this->nestedCbs->importTopics($levels);

        $configurations = $this->nestedCbs->getConfigurationsByLevel($level);
        $allowFiles = [];
        if( CB::checkCBsOption($configurations, 'ALLOW-FILES') ){
            $allowFiles[] = "docs";
        }

        if( CB::checkCBsOption($configurations, 'ALLOW-PICTURES')  ){
            $allowFiles[] = "images";
        }

        $data['cbKey'] = $cbKey;
        $data['parents'] = $this->nestedCbs->getParentsOfTopic($topicKey);
        $data['cbKeyChild'] = $this->nestedCbs->getCbKeyByLevel($level);
        $data['topicKey'] = $topicKey;
        $data['space'] = $this->nestedCbs->getSpace();
        $data['parameters'] = $this->nestedCbs->getParameters($level);
        $data['configurations'] = $configurations;
        $data['allowFiles'] = $allowFiles;
        $data['uploadKey'] = Files::getUploadKey();
        $data['title'] = trans('secondCycle.edit')." ".$data['space']->getAttribute($topicKey,'title');
        $data['post'] = $data['space']->getAttribute($topicKey,'post');

        return view('private.second_cycle.show', $data);
    }

    public function store(Request $request, $cbKey, $level, $parentTopicKey = null){
        try{

            if(!$this->check_permission('create')){
                return redirect()->back()->withErrors(["second_cycle.show" => trans('secondCycle.permission_message')]);
            }

            $this->populateCbs($cbKey);

            $parents = $this->nestedCbs->getParents($level,1);
            $graph = $this->nestedCbs->getGraph();
            $levels = array();
            foreach($parents as $p){
                $tmp = (isset($graph[$p]))?$graph[$p]:array();
                $levels = array_merge($tmp,$levels);
            }

            if(!empty($levels) && is_null($parentTopicKey) || !empty($levels) && !in_array($level,$levels)){
                return redirect()->back()->withErrors(["second_cycle.show" => trans('secondCycle.invalid_level')]);
            }

            $this->nestedCbs->importTopics($parents);

            if(!is_null($parentTopicKey)){
                $parent_level = $this->nestedCbs->getSpace()->getLevel($parentTopicKey);
                if(!in_array($parent_level,$parents))
                    return redirect()->back()->withErrors(["second_cycle.show" => trans('secondCycle.invalid_parent_topic')]);
            }

            $data = $this->validation($request,$this->nestedCbs->getParameters($level),array("controller" => 'SecondCycleController@create', "arguments" => ["cbKey" => $cbKey,'level' => $level,'parentTopicKey' => $parentTopicKey]));

            if (!is_array($data))
                return $data;

            if (Session::has('filesToUpload')) {
                $files = Session::get('filesToUpload');
                Session::forget('filesToUpload');
            }else{
                $files = array();
            }

            $topic_key = $this->nestedCbs->addNode($level,$data,$parentTopicKey,$files);
            $this->nestedCbs->clearRedisCache();

            Session::flash('message', trans('topic.store_ok'));

            return redirect()->action('SecondCycleController@manage', ['cbKey' => $cbKey, 'type' => 'project_2c']);

        } catch (Exception $e) {
            return redirect()->back()->withErrors(["topic.store" => $e->getMessage()]);
        }

    }

    public function update(Request $request, $cbKey, $topicKey){
        try{

            if(!$this->check_permission('update')){
                return redirect()->back()->withErrors(["second_cycle.show" => trans('secondCycle.permission_message')]);
            }

            $this->populateCbs($cbKey);

            try{
                $topic = CB::getTopicDataWithChilds($topicKey);
            }catch(Exception $e){
                return redirect()->back()->withErrors(["cb.show" => trans('secondCycle.topic_invalid')]);
            }

            $level = $this->nestedCbs->getLevelByCbKey($this->nestedCbs->getCbKeyByCbId($topic->topic->cb_id));

            if(!$level){
                return redirect()->back()->withErrors(["second_cycle.show" => trans('secondCycle.invalid_level')]);
            }

            $this->nestedCbs->importTopics(array($level));

            $data = $this->validation($request,$this->nestedCbs->getParameters($level),array("controller" => 'SecondCycleController@edit', "arguments" => ["cbKey" => $cbKey,"topicKey" => $topicKey ]));
            if (!is_array($data))
                return $data;

            if (Session::has('filesToUpload')) {
                $files = Session::get('filesToUpload');
                Session::forget('filesToUpload');
            }else{
                $files = array();
            }

            $topic_key = $this->nestedCbs->updateNode($topicKey,$data,$files);
            $this->nestedCbs->clearRedisCache();

            Session::flash('message', trans('topic.store_ok'));

            return redirect()->action('SecondCycleController@manage', ['cbKey' => $cbKey, 'type' => 'project_2c']);

        } catch (Exception $e) {
            return redirect()->back()->withErrors(["topic.store" => $e->getMessage()]);
        }

    }
    private function validation($request, $parameters, $action){
        $rules = array("title" => "required", "start_date" => "date_format:Y-m-d","end_date" => "date_format:Y-m-d|after:start_date");


        foreach($parameters as $p){

            switch($p['type']){
                case("numeric"):
                case("coin"):
                    $rules[$p['code']] = "numeric";
                    break;
                case("date"):
                    $rules[$p['code']] = "date_format:Y-m-d";
                    break;
                case("dropdown"):
                    $rules[$p['code']] = Rule::in(array_keys($p['options']));
                    break;
            }
            if($p['mandatory'] == 1){
                if(isset($rules[$p['code']])){
                    $rules[$p['code']] .= "|required";
                }else{
                    $rules[$p['code']] = "required";
                }
            }
        }
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->action($action['controller'],$action['arguments'])
                ->withErrors($validator)
                ->withInput();
        }

        $data = array();
        foreach(array("title","description","start_date","end_date","created_on_behalf") as $t){
            $tmp = trim($request->input($t));
            if(isset($tmp) && $tmp != "")
                $data[$t] = $tmp;
        }

        foreach($parameters as $p){
            $tmp = trim($request->input($p['code']));
            if(isset($tmp))
                $data[$p['code']] = $tmp;
        }

        return $data;
    }

    private function checkPublicAccess($level){
        $configurations = $this->nestedCbs->getConfigurationsByLevel($level);
        if(CB::checkCBsOption($configurations, 'PUBLIC-ACCESS') || ONE::isAuth())
            return true;
        return false;
    }

    public function destroy($cbKey, $topicKey){
        if(!$this->check_permission('delete')){
            return redirect()->back()->withErrors(["second_cycle.show" => trans('secondCycle.permission_message')]);
        }

        $this->populateCbs($cbKey);

        try{
            $topic = CB::getTopicDataWithChilds($topicKey);
        }catch(Exception $e){
            return redirect()->back()->withErrors(["cb.show" => trans('secondCycle.topic_invalid')]);
        }

        $cbkey_topic = $this->nestedCbs->getCbKeyByCbId($topic->topic->cb_id);
        $level = $this->nestedCbs->getLevelByCbKey($cbkey_topic);

        if(!$level){
            return redirect()->back()->withErrors(["second_cycle.show" => trans('secondCycle.invalid_level')]);
        }

        $parents = $this->nestedCbs->getParents($level,1);

        $graph = $this->nestedCbs->getGraph();

        $children = (isset($graph[$level]))?$graph[$level]:array();

        $this->nestedCbs->importTopics(array_merge($parents,$children,array($level)));

        foreach($children as $c){
            $tmp = $this->nestedCbs->getSpace()->getLinksByLevel($topicKey,$c);
            if (!empty($tmp))
                return redirect()->back()->withErrors(["second_cycle.show" => trans('secondCycle.topic_is_linked')]);
        }

        $parent_topic_key = $this->nestedCbs->getTopicKeyByTopicId($topic->topic->parent_topic_id);

        if($parent_topic_key != null)
            $level = $this->nestedCbs->getSpace()->getLevel($parent_topic_key);
        else
            $level = null;

        Session::flash('message', trans('cbs.deleteOk'));

        $this->nestedCbs->clearRedisCache();
        if($this->nestedCbs->deleteNode($cbkey_topic,$topicKey)){
            return action('SecondCycleController@manage', ['cbKey' => $cbKey, 'type' => 'project_2c']);
        }

        return redirect()->back()->withErrors(["topic.update" => "Error delete"]);
    }

    public function delete($cbKey, $topicKey){
        $data = array();

        $data['action'] = action("SecondCycleController@destroy", ['topicKey' => $topicKey, 'cbKey' => $cbKey]);
        $data['title'] = "DELETE";
        $data['msg'] = "Are you sure you want to delete?";
        $data['btn_ok'] = "Delete";
        $data['btn_ko'] = "Cancel";

        return view("_layouts.deleteModal", $data);
    }


    /* Public Part */
    public function index(Request $request,$cbKey){
        try{
            $this->populateCbs($cbKey, array("max_depth" => 2));
            $level = $this->nestedCbs->getLevelByCbKey($cbKey);
            if(!$level)
                return redirect()->back()->withErrors(["second_cycle.index" => trans('secondCycle.invalid_cbKey')]);

            if(!$this->checkPublicAccess($level)){
                return redirect()->back()->withErrors(["second_cycle.show" => trans('secondCycle.permission_message')]);
            }

            $parents = $this->nestedCbs->getParents($level);
            if(!empty($parents))
                return redirect()->back()->withErrors(["second_cycle.index" => trans('secondCycle.invalid_parents')]);

            $graph = $this->nestedCbs->getGraph();
            $levels = array_merge($graph[$level],array($level));

            $this->nestedCbs->importTopics($levels,"category");
            $this->nestedCbs->importFilesOfTopics(array($level));

            $data['cbKey'] = $cbKey;
            $data['level'] = $level;
            $data['space'] = $this->nestedCbs->getSpace();
            $data['cancreate'] = true;
            $data['title'] = $this->nestedCbs->getCbTitle($cbKey);
            $data['description'] = $this->nestedCbs->getCbDescription($cbKey);
            $data['parameters'] = collect($this->nestedCbs->getParameters($level))->keyBy("code")->toArray();
            $data['nestedCbs'] = $this->nestedCbs;

            return view('public.' . ONE::getEntityLayout() . '.second_cycle.'. $level .'.index', $data);
        }catch(Exception $e){
            return redirect()->back()->withErrors(["second_cycle.index" => $e->getMessage()]);
        }
    }

    public function list_ajax(Request $request, $cbKey, $level){
        $this->populateCbs($cbKey);

        $root_level = $this->nestedCbs->getLevelByCbKey($cbKey);
        if(!$root_level)
            throw new Exception();

        $graph = $this->nestedCbs->getGraph();
        if(isset($graph[$level]))
            $levels = array_merge($graph[$level],array($level),$this->nestedCbs->getParents($level));
        else
            $levels = array_merge($this->nestedCbs->getParents($level),array($level));

        if(!$this->checkPublicAccess($level)){
            throw new Exception();
        }

        $this->nestedCbs->importTopics($levels,"category");

        $data['space'] = $this->nestedCbs->getSpace();
        $data['cbKey'] = $cbKey;
        $data['filter'] = $request->input('filter');
        $data['parameters'] = collect($this->nestedCbs->getParameters($level))->keyBy("code")->toArray();
        $data['nestedCbs'] = $this->nestedCbs;

        return view('public.' . ONE::getEntityLayout() . '.second_cycle.'. $level .'.list_ajax', $data);
    }

    public function show($cbKey,$level,$topicKey) {
        try {
            $this->populateCbs($cbKey);

            $level_root = $this->nestedCbs->getLevelByCbKey($cbKey);
            if (!$level_root)
                return redirect()->back()->withErrors(["cb.show" => trans('secondCycle.topic_invalid')]);

            $levels = $this->nestedCbs->getParents($level);
            if (!empty($levels) && !in_array($level_root, $levels))
                return redirect()->back()->withErrors(["cb.show" => trans('secondCycle.topic_invalid')]);
            $levels[] = $level;
            $graph = $this->nestedCbs->getGraph();
            if (isset($graph[$level]))
                $levels = array_merge($levels, $graph[$level]);

            $this->nestedCbs->importTopics(null,"category");
            $this->nestedCbs->importFilesOfTopics($levels);

            $l = $this->nestedCbs->getSpace()->getLevel($topicKey);
            if ($l != $level)
                return redirect()->back()->withErrors(["cb.show" => trans('secondCycle.topic_invalid')]);

            if (!$this->checkPublicAccess($level)) {
                return redirect()->back()->withErrors(["second_cycle.show" => trans('secondCycle.permission_message')]);
            }

            $configurations = $this->nestedCbs->getConfigurationsByLevel($level);

            /* Handle Followers */
            $tmp_followers = CB::getFollowersTopic($topicKey);
            $followers = !empty($tmp_followers) ? collect($tmp_followers)->keyBy('user_key')->toArray() : [];
            $usersKeys = collect($followers)->pluck('user_key')->toArray();
            $usersNames = [];
            if (count($usersKeys) > 0)
                $usersNames = Auth::getPublicListNames($usersKeys);
            /*******************/

            $data['space'] = $this->nestedCbs->getSpace();
            $data['level'] = $level;
            $data['cbKey'] = $cbKey;
            $data['nestedCbs'] = $this->nestedCbs;
            $data['topicKey'] = $topicKey;
            $data['followers'] = $followers;
            $data['usersNames'] = $usersNames;
            $data['cbTitle'] = $this->nestedCbs->getCbTitle($cbKey);
            $data['parents'] = $this->nestedCbs->getParentsOfTopic($topicKey);
            $data['cancreate'] = true;
            $data['canupdate'] = true;
            $data['candelete'] = true;
            $data['canfollow'] = true;

            return view('public.' . ONE::getEntityLayout() . '.second_cycle.' . $level . '.show', $data);
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["second_cycle.show" => $e->getMessage()]);
        }
    }

    public function news($cbKey){

        try{
            $this->populateCbs($cbKey);
            $level = $this->nestedCbs->getLevelByCbKey($cbKey);
            if(!$level)
                return redirect()->back()->withErrors(["second_cycle.index" => trans('secondCycle.invalid_cbKey')]);

            if(!$this->checkPublicAccess($level)){
                return redirect()->back()->withErrors(["second_cycle.show" => trans('secondCycle.permission_message')]);
            }

            $parents = $this->nestedCbs->getParents($level);
            if(!empty($parents))
                return redirect()->back()->withErrors(["second_cycle.index" => trans('secondCycle.invalid_parents')]);

            $graph = $this->nestedCbs->getGraph();
            $levels = array_merge($graph[$level],array($level));

            $this->nestedCbs->importTopics($levels,"category");
            $this->nestedCbs->importFilesOfTopics(array($level));

            $data['cbKey'] = $cbKey;
            $data['level'] = $level;
            $data['space'] = $this->nestedCbs->getSpace();
            $data['cancreate'] = true;
            $data['title'] = $this->nestedCbs->getCbTitle($cbKey);
            $data['description'] = $this->nestedCbs->getCbDescription($cbKey);
            $data['parameters'] = collect($this->nestedCbs->getParameters($level))->keyBy("code")->toArray();
            $data['nestedCbs'] = $this->nestedCbs;

            $dataNews = Orchestrator::getPageListByType("news",5);
            $lastNews = [];
            if(!empty($dataNews)) {
                $lastNews = CM::getVariousContents($dataNews);
            }

            $lastNews = collect($lastNews)->sortByDesc('start_date')->take(5);

            $data['lastNews'] = $lastNews;

            foreach($this->getNodes($level) as $key => $topic){
                foreach($topic as $t){

                    if($t['type'] == "news"){
                        $newsCbKey = $t['cb_key'];
                    }
                }

            }

            $news = collect(CB::getCBAndTopics($newsCbKey)->topics)->sortByDesc('start_date')->take(5);

            foreach($news as $new){
                $new->topicSubProjectKey = $this->nestedCbs->getTopicKeyByTopicId($new->parent_topic_id);
                $new->topicProjectKey = collect($this->nestedCbs->getParentsOfTopic($new->topicSubProjectKey))->first();

            }

            $data['news'] = $news;

            return view('public.' . ONE::getEntityLayout() . '.pages.news', $data);
        }catch(Exception $e){
            return redirect()->back()->withErrors(["second_cycle.index" => $e->getMessage()]);
        }

    }

    public function showAll($cbKey, $type){
        if($type == 'global'){
            $dataNews = Orchestrator::getPageListByType("news");
            $lastNews = [];
            if(!empty($dataNews)) {
                $lastNews = CM::getVariousContents($dataNews);
            }

            $lastNews = collect($lastNews)->sortByDesc('start_date');

            $data['lastNews'] = $lastNews;
            $data['type'] = $type;
            $data['cbKey'] = $cbKey;
        }else{
            $this->populateCbs($cbKey);
            $level = $this->nestedCbs->getLevelByCbKey($cbKey);
            if(!$level)
                return redirect()->back()->withErrors(["second_cycle.index" => trans('secondCycle.invalid_cbKey')]);

            if(!$this->checkPublicAccess($level)){
                return redirect()->back()->withErrors(["second_cycle.show" => trans('secondCycle.permission_message')]);
            }

            $parents = $this->nestedCbs->getParents($level);
            if(!empty($parents))
                return redirect()->back()->withErrors(["second_cycle.index" => trans('secondCycle.invalid_parents')]);

            $graph = $this->nestedCbs->getGraph();
            $levels = array_merge($graph[$level],array($level));

            $this->nestedCbs->importTopics($levels,"category");
            $this->nestedCbs->importFilesOfTopics(array($level));

            $data['cbKey'] = $cbKey;
            $data['level'] = $level;
            $data['space'] = $this->nestedCbs->getSpace();
            $data['cancreate'] =true;
            $data['title'] = $this->nestedCbs->getCbTitle($cbKey);
            $data['description'] = $this->nestedCbs->getCbDescription($cbKey);
            $data['parameters'] = collect($this->nestedCbs->getParameters($level))->keyBy("code")->toArray();
            $data['nestedCbs'] = $this->nestedCbs;
            foreach($this->getNodes($level) as $key => $topic){
                foreach($topic as $t){

                    if($t['type'] == "news"){
                        $newsCbKey = $t['cb_key'];
                        break 2;
                    }
                }

            }

            $news = collect(CB::getCBAndTopics($newsCbKey)->topics)->sortByDesc('start_date');

            foreach($news as $new){
                $new->topicSubProjectKey = $this->nestedCbs->getTopicKeyByTopicId($new->parent_topic_id);
                $new->topicProjectKey = collect($this->nestedCbs->getParentsOfTopic($new->topicSubProjectKey))->first();
            }

            $data['news'] = $news;
            $data['type'] = $type;

        }

        return view('public.' . ONE::getEntityLayout() . '.pages.allNews', $data);
    }

    public function showFaqs(){
        return view('public.' . ONE::getEntityLayout() . '.pages.FAQs');
    }
}
