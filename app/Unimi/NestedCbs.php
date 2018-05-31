<?php

namespace App\Unimi;

use Exception;
use Illuminate\Support\Facades\Redis;
use App\ComModules\CB;
use App\ComModules\Orchestrator;
use App\Unimi\Space;
use Session;

class NestedCbs{

	private $space;

	private $root_cbkey;

	private $root_level;

	private $cbs;

	private $sublevels;

	private $options;

	private $cbs_levels;

	private $topics;

	private $parent_topics;

	private $parameters;
	
	private $parents = array();

	private $max_depth = 20;

	private $euristic_exclude_levels = array();

	private $euristic_only_levels = array();

	private $populate_cb_module = false;

	private $cb_setting_configurations = false;

	private $configurations = array();
	
	private $cb_configurations = array();

	private $cb_id_to_cb_key = array();
	
	private $cache = true;

	private $auto_discover = true;

	public function __construct($root_cbkey, $sublevels = array(), $options = array()){
		$this->space = new Space();
		$this->root_cbkey = $root_cbkey;

		$this->options = $options;
		$this->parameters = array();

		if (!$this->auto_discover)
			$this->sublevels = $sublevels;
		else
			$this->sublevels = array();

		if(isset($options['autopopulate']) && $options['autopopulate']){
			$this->populate_cb_module = true;
			$this->cache = false;
			$this->auto_discover = false;
		}
	
		if(isset($options['root_level'])){
			$this->root_level = $options['root_level'];
		}else{
			$this->root_level = 'root';
		}

		if(isset($options['max_depth']) && intval($options['max_depth']) > 0){
			$this->max_depth = intval($options['max_depth']);
		}

		if(isset($options['euristic']['exclude'])){
			$this->euristic_exclude_levels = $options['euristic']['exclude'];
		}

		if(isset($options['euristic']['only'])){
			$this->euristic_only_levels = $options['euristic']['only'];
		}
		
		$this->cbs = $this->fetchCbs($this->root_cbkey,$this->root_level,null, $this->max_depth);
		$this->cbs[$this->root_cbkey] = $this->root_level;
		foreach($this->cbs as $k => $v){
			if(!isset($this->cbs_levels[$k])){
				$this->cbs_levels[$k] = array($v);
			}else{
				$this->cbs_levels[$k][] = $v;
			}
		}

		foreach($this->sublevels as $parent => $children){
			if (empty($children))
				continue;
			foreach($children as $child){
				if (isset($this->parents[$child])){
					$this->parents[$child][] = $parent;
				}else{
					$this->parents[$child] = array($parent);
				}
			}
		}
	}

	private function getCodeByParameterName($level,$name){
		if(!isset($this->options['parameters'][$level]))
			return null;
		foreach($this->options['parameters'][$level] as $param){
			foreach($param['name']['language'] as $n){
				if($name == $n){
					return $param['code'];
				}
			}
		}

		return null;
	}

	private function getOptionCodeByOptionLabel($level,$name,$label_opt){
		if(!isset($this->options['parameters'][$level]))
			return null;
		foreach($this->options['parameters'][$level] as $param){
			foreach($param['name']['language'] as $n){
				if($name == $n){
					if(isset($param['options_select'])){
						foreach($param['options_select'] as $p){
							foreach($p['name']['language'] as $n2){
								if($n2 == $label_opt){
									if(isset($p['code']))
										return $p['code'];
									else
										return null;
								}
							}
						}
					}
					return null;
				}
			}
		}

		return null;
	}

	private function getPositionByCode($level,$code){
		$pos = 1000;
		if(!isset($this->options['parameters'][$level]))
			return $pos;
		foreach($this->options['parameters'][$level] as $param){
			if($code == $param['code']){
				if (isset($param['position']))
					return $param['position'];
				else
					return $pos;
			}
		}

		return $pos;
	}
	private function saveCbParameter($cbkey,$sublevel,$already_imported=array()){
		if (!isset($this->options['parameters'][$sublevel]))
			return false;

		$languages = Orchestrator::getLanguageList();
		$valid_fields = array("color","max_value","min_value","pin","icon");
		$parameters = CB::getParametersTypes();

		foreach($this->options['parameters'][$sublevel] as $param){
			if(!empty($already_imported) && in_array($param['code'],$already_imported))
				continue;
			
			$optionsSelect = isset($param['options_select'])?$param['options_select']:null;
			$parameterTypeSelect = $param['type'];
			$mandatory = isset($param['mandatory'])?$param['mandatory'] : 0;
			$visible = isset($param['visible'])?$param['visible'] : 0;
			$visibleInList = isset($param['visibleInList'])?$param['visibleInList'] : 0;
			$useFilter = isset($param['use_filter'])?$param['use_filter'] : 0;

			$parameterTranslations = array();
			foreach ($languages as $language) {
			    try {
                    $parameterName = $param['name']['language'][$language->code];
                    $parameterDescription = $param['description']['language'][$language->code];
                } catch (Exception $e) {
			        //No Translation -> NEEDS TO BE FIXED
                    $lang = "en";
                    if (!is_null($lang)) {
                        $parameterName = $param['name']['language'][$lang];
                        $parameterDescription = $param['description']['language'][$lang];
                    }
                }
				if (!empty($parameterName)) {
					$parameterTranslations[] = [
						'language_code' => $language->code,
						'parameter' => $parameterName,
						'description' => $parameterDescription
					];
				}
			}
			$options = array();
			if($optionsSelect != null){

				foreach($optionsSelect as $opt){

					$optionTranslations = array();
					$optionField = array();
					foreach ($opt['name']['language'] as $key => $optTrans){
						if(!empty($optTrans)) {
							$optionTranslations[] = ['language_code' => $key,'label' => $optTrans];
						}

					}
					foreach($valid_fields as $f){
						if(isset($opt[$f])){
							$optionField[] = array('code' => $f,'value' => $opt[$f]);
						}
					}

					$options[] = ['translations' => $optionTranslations, 'optionFields' => $optionField];

				}
			}
			$fields = array();
			foreach($valid_fields as $f){
				if(isset($param[$f])){
					$fields[] = array('code' => $f,'value' => $param[$f]);
				}
			}

			foreach ($parameters as $parameter){
				if($parameter->code == $parameterTypeSelect){
					$parameterTypeId = $parameter->id;
					break;
				}
			}
			
			$data = [
				'parameter_type_id' => $parameterTypeId,
				'cb_key'            => $cbkey,
				'translations'      => $parameterTranslations,
				'code'              => $parameterTypeSelect,
				'mandatory'         => $mandatory,
				'visible'           => $visible,
				'visible_in_list'   => $visibleInList,
				'use_filter'        => $useFilter,
				'value'             => null,
				'options'           => $options,
				'fields'            => $fields
			];

			CB::setParameters($data);
		}
	
		if(isset($this->options['configurations'][$sublevel])){
			if(!$this->cb_setting_configurations)
				$this->importCbConfigurations();

			$confs = array();
			foreach($this->options['configurations'][$sublevel] as $conf){
				$confs[] = $this->cb_setting_configurations[$conf];
			}
	
			CB::setCbConfigurations($cbkey, $confs);
		}


		return true;
	}

	private function importCbConfigurations(){
		$types = CB::getConfigurations();
		foreach($types as $type){
			foreach($type->configurations as $configuration){
				$this->cb_setting_configurations[$configuration->code] = $configuration->id;
			}
		}
	}

	private function getCbParameters($cbkey){
		$cb = $this->getCb($cbkey);
		if(!isset($cb->parameters))
			return array();

		return $cb->parameters;
	}

	private function getCbConfigurations($cbkey){
		$cb = $this->getCb($cbkey);
		if(!isset($cb->configurations))
			return array();

		$configurations = array();
		foreach($cb->configurations as $c){
			$configurations[] = $c->code;
		}

		return $configurations;
	}

	function hasNews($level){
		$cbKey = $this->getCbKeyByLevel($level);
		if(!$cbKey)
			return false;

		$configurations = $this->getCbConfigurations($cbKey);
		return (in_array("allow_news",$configurations));
	}

	private function importParameters($cbkey,$level){
		$CbParameters = $this->getCbParameters($cbkey);
		$codes = array();

		foreach ($CbParameters as $parameter) {
			$name = $parameter->parameter;
			$type_code = $parameter->type->code;

			$parameterOptions = array();
			$parameterCodeOptions = array();
			$options = $parameter->options;
			foreach ($options as $option) {
			    $parameterOptions[$option->id] = $option->label;
			    $parameterCodeOptions[$option->id] = $this->getOptionCodeByOptionLabel($level,$name,$option->label);
			}

			$code = $this->getCodeByParameterName($level,$name);

			if(is_null($code))
				$code = "param_".$parameter->id;

			$this->parameters[$cbkey][$parameter->id] = array('code' => $code, 'id' => $parameter->id, 'name' => $name, 'type' => $type_code, 'options' => $parameterOptions,'optionsCode' => $parameterCodeOptions, 'mandatory' => $parameter->mandatory);

			if(isset($code))
				$codes[] = $code;
		}
		
		$this->cb_configurations[$cbkey] = $this->getCbConfigurations($cbkey);

		if($this->populate_cb_module && isset($this->options['parameters'][$level]) && count($codes) < count($this->options['parameters'][$level])){
			$this->saveCbParameter($cbkey,$level,$codes);
			$this->importParameters($cbkey,$level);
		}
	}

	private function getCbChildren($cbkey){
		$result = ($this->cache)?json_decode(Redis::get("nestedcbs-children-$cbkey")):null;
		if(!$result){
			$result = CB::getCbChildren($cbkey);
			if(!$this->populate_cb_module)
				Redis::set("nestedcbs-children-$cbkey",json_encode($result));
			else
				Redis::set("nestedcbs-children-$cbkey",null);
		}

		return $result;
	}
	
	private function getCb($cbkey){
		$result = ($this->cache)?json_decode(Redis::get("nestedcbs-cb-$cbkey")):null;
		if(!$result){
			$result = CB::getCb($cbkey);
			if(!$this->populate_cb_module)
				Redis::set("nestedcbs-cb-$cbkey",json_encode($result));
			else
				Redis::set("nestedcbs-cb-$cbkey",null);
		}

		return $result;
	}

	public function getCbTitle($cbkey){
		$result = $this->getCb($cbkey);
		if(!$result)
			return null;

		return $result->title;
	}
	
	public function getCbDescription($cbkey){
		$result = $this->getCb($cbkey);
		if(!$result)
			return null;

		return $result->contents;
	}

	private function fetchCbs($cbkey,$level,$title=null,$depth=20){
		$children = array();
		$sublevels = isset($this->sublevels[$level])? $this->sublevels[$level]:array();

		if($depth < 1){
			return array();
		}

		if(!empty($this->euristic_only_levels) && !in_array($level,$this->euristic_only_levels)){
			return array();
		}

		$depth = $depth - 1;

		$sublevels = array_diff($sublevels, $this->euristic_exclude_levels);

		$result = $this->getCbChildren($cbkey);

		if($this->auto_discover && $level == "root" && !is_null($result->tag) && $result->tag != ""){ // Fix root name in autodiscover
			$level = $result->tag;
			$this->root_level = $level;
		}

		$this->cb_id_to_cb_key[$result->id] = $cbkey;

		if(is_null($title))
			$title = $result->title;

		if(!($result->tag == $level || $result->parent_cb_id == 0))
			throw new Exception("Error in graph");

		$cb_id = $result->id;

		$this->importParameters($cbkey,$level);

		if(isset($result->children)){
			foreach($result->children as $cb){
				$children[$cb->cb_key] = $cb->tag;
				$this->cb_id_to_cb_key[$cb->id] = $cb->cb_key;
				$this->sublevels[$level][] = $cb->tag;
			}
		}

		if($this->populate_cb_module){ //Autodiscover should be disabled
			$cb_to_create = array_diff($sublevels,array_values($children));

			foreach($cb_to_create as $tag){
				$cb = CB::createCbChild(array(
					"title" => $title." (".$tag.")",
					"parent_cb_id" => $cb_id,
					"contents" => "",
					"tag" => $tag,
					"start_date" => $result->start_date,
					"end_date" => $result->end_date,
				));

				$this->saveCbParameter($cb->cb_key,$tag);

				$children[$cb->cb_key] = $tag;
				$this->cb_id_to_cb_key[$cb->id] = $cb->cb_key;
			}
		
		}

		$cbs = $children;
		foreach($children as $cb => $tag){
			if(!in_array($cb,$cbs))
				$cbs = array_merge($cbs,$this->fetchCbs($cb,$tag,$title,$depth));
		}


		return $cbs;
	}

	function importTopics($levels = array(),$order = null){
		if (is_string($levels))
			$levels = array($levels);
		$nodes = array();
		$nodes[] = $this->root_level;
		$visited = array();
		$links = array();

		while(!empty($nodes)){
			$level = array_pop($nodes);
			$visited[] = $level;
			$this->space->addLevel($level);

			if (isset($this->sublevels[$level])){
				$tmp = array_diff($this->sublevels[$level],$visited);
				$nodes = array_merge($tmp, $nodes);
			}

			if (empty($levels) || in_array($level,$levels)){
				$topics = $this->getTopics($this->getCbKeyByLevel($level));

				if(!empty($order)) {
                    if ($order==="category") {
                        $topics = collect($topics)->sortBy(function($topic,$key) {
                            if (!empty($topic->parameters)) {
                                $parameters = collect($topic->parameters);

                                $category = $parameters->where("code","category")->first();
                                if (!empty($category)) {
                                    return $category->pivot->value;
                                }
                            }

                            return 0;
                        });
                    } else
                        $topics = collect($topics)->sortBy("title");
                } else
                    $topics = collect($topics)->sortBy("title");
			}else{
				$topics = array();
			}
			foreach($topics as $t){
				if (empty($t))
					continue;
				$this->topics[$t->id] =  $t->topic_key;
				$this->space->addNode($level, $t->topic_key, $this->getParametersFromTopic($t));
				if ($t->parent_topic_id != 0)
					$links[] = array($t->parent_topic_id,$t->id);
			}
		}
		
		while(!empty($links)){
			$link = array_pop($links);
			if (isset($this->topics[$link[0]]) && isset($this->topics[$link[1]])){
				if (!isset($this->parent_topics[$link[1]]))
					$this->parent_topics[$this->topics[$link[1]]] = array($this->topics[$link[0]]);
				else
					$this->parent_topics[$this->topics[$link[1]]][] = $this->topics[$link[0]];

				$this->space->addLink($this->topics[$link[1]],$this->topics[$link[0]]);
			}
		}
	}

	function importFilesOfTopics($levels = array()){
		if (is_string($levels))
			$levels = array($levels);
		if (empty($levels))
			$levels = $this->space->getLevels();
		foreach($levels as $l){
			$files = $this->getFiles($this->getCbKeyByLevel($l));
			foreach($files as $topic_key => $v){
				$this->space->setAttribute($topic_key,"files",$v);
			}
		}
	}

	private function getTopics($cbkey){
		$result = ($this->cache)?json_decode(Redis::get("nestedcbs-getTopics-$cbkey")):null;
		if(!$result){
			$result = CB::topicsWithLastPostTableData($cbkey);
			if(!$this->populate_cb_module)
				Redis::set("nestedcbs-getTopics-$cbkey",json_encode($result));
			else
				Redis::set("nestedcbs-getTopics-$cbkey",null);
		}
		if(isset($result->topics))
			return $result->topics;
		return $result;
	}

	private function getFiles($cbkey){
		$result = ($this->cache)?json_decode(Redis::get("nestedcbs-getFiles-$cbkey")):null;
		if(!$result){
			$topics = $this->getTopics($cbkey);
			$data = array();
			foreach ($topics as $t){
                if(isset($t->last_post->post_key) && isset($t->topic_key)){
					$data[] = array("topic_key" => $t->topic_key,"last_post" => array("post_key" => $t->last_post->post_key));
				}
			}
			$result = CB::getFilesOfTopics($data);
			if(!$this->populate_cb_module)
				Redis::set("nestedcbs-getFiles-$cbkey",json_encode($result));
			else
				Redis::set("nestedcbs-getFiles-$cbkey",null);
		}
		return $result;
	}

	function getConfigurationsByLevel($level){
		$cbkey = $this->getCbKeyByLevel($level);
		
		if (!$cbkey || !isset($this->cb_configurations[$cbkey]))
			return array();

		return $this->cb_configurations[$cbkey];
	}

	private function getParametersFromTopic($t){
		$params = array();
		if (!isset($t))
			return array();
		$defaults = array('title' => 'title','description' => 'contents','start_date' => 'start_date','end_date' => 'end_date','created' =>'created_at' ,'modified' => 'updated_at','created_on_behalf' => 'created_on_behalf');

		/* WORKAROUND: getTopicsWithLastPost doesn't return the most recent post */
		if(isset($t->last_post->contents) && $t->last_post->updated_at >= $t->updated_at){
			$params['description'] = $t->last_post->contents;
		}

		foreach($defaults as $k => $d){
			if (isset($params[$k])){
				continue;
			}elseif(isset($t->{$d})){
				$params[$k] = $t->{$d};
			}else{
				$params[$k] = null;
			}
		}
		if(isset($t->parameters)){
			foreach($t->parameters as $p){
				if ($this->hasOptions($this->cb_id_to_cb_key[$t->cb_id],$p->id)){
					$params[$this->getKeyForParameter($this->cb_id_to_cb_key[$t->cb_id],$p->id)] = $this->getOptions($this->cb_id_to_cb_key[$t->cb_id], $p->id, $p->pivot->value);
					$params["code_".$this->getKeyForParameter($this->cb_id_to_cb_key[$t->cb_id],$p->id)] = $this->getOptionsCode($this->cb_id_to_cb_key[$t->cb_id], $p->id, $p->pivot->value);

                    if ($this->getKeyForParameter($this->cb_id_to_cb_key[$t->cb_id],$p->id)=="category" && isset($p->pivot->value)) {
                        $option = collect($p->options)->where("id",$p->pivot->value);
                        if ($option->count()>0 && isset($option->first()->parameter_option_fields)) {
                            $fields = $option->first()->parameter_option_fields;
                            foreach ($fields as $field) {
                                $params["category_".$field->code] = $field->value;
                            }
                        }
                    }
				} else{
					$params[$this->getKeyForParameter($this->cb_id_to_cb_key[$t->cb_id],$p->id)] = $p->pivot->value;
				}
			}
		}
	
		if(isset($t->last_post))
			$params['post'] = $t->last_post;

		return $params;
	}

	function hasOptions($cbkey,$param_id){
		if (!empty($this->parameters[$cbkey][$param_id]['options']))
			return true;

		return false;		
	}

	function getOptions($cbkey,$param_id,$option_id){
		if (isset($this->parameters[$cbkey][$param_id]['options'][$option_id]))
			return $this->parameters[$cbkey][$param_id]['options'][$option_id];

		return null;		
	}
	
	function getOptionsCode($cbkey,$param_id,$option_id){
		if (isset($this->parameters[$cbkey][$param_id]['options'][$option_id]))
			return $this->parameters[$cbkey][$param_id]['optionsCode'][$option_id];

		return null;		
	}

	function getGraph($level = null){
		if(is_null($level))
			return $this->sublevels;

		if(isset($this->sublevels[$level]))
			return $this->sublevels[$level];

		return array();
	}

	function getLevelByCbKey($cbKey){
		if(isset($this->cbs[$cbKey]))
			return $this->cbs[$cbKey];

		return null;
	}

	function getParents($level,$max_level = 99){
		$visited = array();
		$levels = array();

		if (isset($this->parents[$level]))
			$queue = $this->parents[$level];
		else
			$queue = array();

		foreach($queue as $q){
			$levels[$q] = 1;
		}

		while(!empty($queue)){
			$node = array_pop($queue);

			if($levels[$node] > $max_level)
				continue;

			$visited[] = $node;


			if (isset($this->parents[$node]))
				$parents = $this->parents[$node];
			else
				$parents = array();

			foreach($parents as $p){
				$levels[$p] = $levels[$node] + 1;
			}

			$parents = array_diff($parents,$visited);
			$queue = array_merge($queue,$parents);
		}

		return $visited;
	}

	private function getKeyForParameter($cb_key,$parameter_id){
		if(isset($this->parameters[$cb_key][$parameter_id]['code']))
			return $this->parameters[$cb_key][$parameter_id]['code'];

		if(isset($this->parameters[$cb_key][$parameter_id]['id']))
			return "param_".$this->parameters[$cb_key][$parameter_id]['id'];		

		return rand(1,10000);
	}

	private function getParameterIdByKey($cb_key,$key){

		if(!isset($this->parameters[$cb_key]))
			return null;

		foreach($this->parameters[$cb_key] as $parameter_id => $v){
			if($v['code'] == $key)
				return $parameter_id;
		}

		return null;
	}

	function deleteNode($cbkey,$topic){
		CB::deleteTopic($topic);

		$this->space->removeNode($topic);
		self::clearGetTopics($cbkey);
		self::clearGetFiles($cbkey);

		return true;
	}

	function addNode($sublevel, $options, $parent_topic=null, $files = array()){
		$default = array("title","description","start_date","end_date","created_on_behalf");

		$data = array();

		$cbkey = $this->getCbKeyByLevel($sublevel);

		if(is_null($cbkey))
			return null;

		foreach($default as $d){
			if(isset($options[$d])){
				if ($d == "description")
					$data['contents'] = $options[$d];
				else
					$data[$d] = $options[$d];
			}
		}

		if (!is_null($parent_topic) && $this->getTopicIdByTopicKey($parent_topic)){
			$data['parent_topic_id'] = $this->getTopicIdByTopicKey($parent_topic);
			$data['parent_topic_key'] = $parent_topic;
		}else{
			$data['parent_topic_id'] = 0;
		}

		foreach($default as $d){
			unset($options[$d]);
		}

		$parameters = array();
		if(!empty($options)){
			foreach($options as $k => $v){
				$parameter_id = $this->getParameterIdByKey($cbkey,$k);
				if(!is_null($parameter_id)){
					$parameters[] = array("parameter_id" => $parameter_id, "value" => $v);
				}
			}
		}
		
		$topic = CB::setTopicWithParameters($cbkey, $data, $parameters, true);
		self::clearGetTopics($cbkey);

		$this->space->addNode($this->cbs[$cbkey], $topic->topic_key, $this->getParametersFromTopic(CB::getTopicDataWithChilds($topic->topic_key)));
		if (isset($this->topics[$data['parent_topic_id']]) && $data['parent_topic_id'] != 0)
			$this->space->addLink($topic->topic_key,$this->topics[$data['parent_topic_id']]);

		$post_key = $topic->first_post->post_key;

		if (!empty($files)) {
			self::clearGetFiles($cbkey);
			foreach ($files as $file) {
				CB::setFilesForTopic($post_key, $file);
			}

		}

		return $topic->topic_key;
	}

	function getCbKeyByLevel($level){
		foreach($this->cbs as $cb_key => $l){
			if($l == $level)
				return $cb_key;
		}
		
		return null;
	}

	function getCbKeyByCbId($cb_id){
		if(isset($this->cb_id_to_cb_key[$cb_id]))
			return $this->cb_id_to_cb_key[$cb_id];
		return null;
	}
	
	function getTopicKeyByTopicId($topic_id){
		if(isset($this->topics[$topic_id]))
			return $this->topics[$topic_id];
		return null;
	}

	function getTopicIdByTopicKey($topicKey){
		foreach($this->topics as $id => $key){
			if($key == $topicKey)
				return $id;			
		}

		return null;
	}

	function updateNode($topic_key, $options, $files = array()){
		$default = array("title","description","start_date","end_date","created_on_behalf");

		if(!in_array($topic_key,$this->topics))
			return false;

		$topic = CB::getTopicDataWithChilds($topic_key);

		$data = array();

		foreach($default as $d){
			if(isset($options[$d])){
				if ($d == "description")
					$data['contents'] = $options[$d];
				else
					$data[$d] = $options[$d];
			}
		}

		foreach($default as $d){
			unset($options[$d]);
		}
		
		$parameters = array();
		if(!empty($options)){
			foreach($options as $k => $v){
				$parameter_id = $this->getParameterIdByKey($this->cb_id_to_cb_key[$topic->topic->cb_id],$k);
				if(!is_null($parameter_id)){
					$parameters[] = array("parameter_id" => $parameter_id, "value" => $v);
				}
			}
		}
		
		$data['parent_topic_id'] = $topic->topic->parent_topic_id;

		// WORKAROUND: in CB module the contents field is populated using summary parameter
		$data['summary'] = $data['contents'] ?? " ";

		$topic_new = CB::updateTopicWithParameters($topic_key, $data, $parameters);

		self::clearGetTopics($this->cb_id_to_cb_key[$topic->topic->cb_id]);
		$this->space->setAttributes($topic_key, $this->getParametersFromTopic(CB::getTopicDataWithChilds($topic_key)));
		$post_key = $topic->posts[0]->post_key;
		if (!empty($files)) {
			self::clearGetFiles($this->cb_id_to_cb_key[$topic->topic->cb_id]);
			foreach ($files as $file) {
				CB::setFilesForTopic($post_key, $file);
			}

		}
		
		return true;
	}

	public function getParameters($level){
		$cbkey = $this->getCbKeyByLevel($level);

		if (!$cbkey || !isset($this->parameters[$cbkey]))
			return array();
		
		$parameters = $this->parameters[$cbkey];

		uasort($parameters,function($a,$b) use($level){
			$p1 =  $this->getPositionByCode($level, $a['code']);	
			$p2 =  $this->getPositionByCode($level, $b['code']);
			if ($p1 == $p2) {
				return 0;
			}
			return ($p1 < $p2) ? -1 : 1;	
		});

		return $parameters;
	}

	public function getParentsOfTopic($topicKey,$max_parents = 99){
		$visited = array();
		if (!isset($this->parent_topics[$topicKey]))
			return array();
		$queue = $this->parent_topics[$topicKey];
		$num_parents = 1;
		while(!empty($queue)){
			$node = array_pop($queue);
			$visited[] = $node;
			if($num_parents > $max_parents){
				break;
			}
			$num_parents = $num_parents + 1;
			if (isset($this->parent_topics[$node])){
				$queue = array_merge($queue,array_diff($this->parent_topics[$node],$visited));
			}

		}

		return $visited;
	}

	public function getSpace(){
		return $this->space;
	}

    public function clearRedisCache($cbKey=null) {
	$cbs = (is_null($cbKey))?$this->cb_id_to_cb_key:array($cbKey);
        foreach ($cbs as $cbKey) {
	    self::clearAllRedisCache($cbKey);
        }
    }
	public static function clearAllRedisCache($cbKey){
	   try{
            self::clearGetTopics($cbKey);
            self::clearGetFiles($cbKey);
            self::clearChildren($cbKey);
            self::clearCb($cbKey);

	    $cb = CB::getCb($cbKey);

	    if($cb->parent_cb_id != 0){
	    	$cb = CB::getCbById($cb->parent_cb_id);
		self::clearGetTopics($cb->cb_key);
		self::clearGetFiles($cb->cb_key);
		self::clearChildren($cb->cb_key);
		self::clearCb($cb->cb_key);
	    }
	    }catch(Exception $e){
		return;
	    }
	}

	public static function clearGetTopics($cbkey){
		Redis::set("nestedcbs-getTopics-$cbkey",null);
	}
	
	public static function clearGetFiles($cbkey){
		Redis::set("nestedcbs-getFiles-$cbkey",null);
	}

	public static function clearChildren($cbkey){
		Redis::set("nestedcbs-children-$cbkey",null);
	}

	public static function clearCb($cbkey){
		Redis::set("nestedcbs-cb-$cbkey",null);
	}

	public static function buildSecondCycleTree($cbKey,$data,$level){
		$html = '';
		if (!isset($data[$level]))
			return null;
		
		foreach ($data[$level] as $topic){
			$html .= "<li class='dd-item nested-list-item' data-id='{$topic['id']}'>";
			$html .= "<div class='dd-handle dd-nodrag nested-list-handle'>";
			if ($topic['type'] == "node")
				$html .= "<span class='glyphicon glyphicon-th-list'></span>";
			else
				$html .= "<span class='glyphicon glyphicon-folder-close'></span>";
			$html .= "</div>";
			$html .= "<div class='nested-list-content'>";
			if($topic['type'] == "node"){
				$html .= "<a href='".action("SecondCycleController@internalShow", ["cbKey" => $cbKey, "topicKey" => $topic['id']]) . "'>{$topic['title']}</a>";
				$html .= "<div class='pull-right' style='margin-top:-3px; color: #fffbfe;'>";
				$html .= \App\One\ONE::actionButtons(["cbKey" => $cbKey, "topicKey" => $topic['id']], ['delete' => 'SecondCycleController@delete']);
				$html .= "</div>";

				$html .= "<div class='pull-right'>";
				$html .= "<a style=\"margin-top:-6px; margin-right: 6px;color: #fffbfe;\" class=\"btn btn-flat btn-success btn-xs\" href='" . action("SecondCycleController@edit", ["cbKey" => $cbKey, "topicKey" => $topic['id']]) . "'><i class=\"fa fa-pencil\"></i></a>";
				$html .= "</div>";
				if ($topic['has_news']){
					$html .= "<div class='pull-right'>";
					$html .= "<a style=\"margin-top:-6px; margin-right: 6px;\" class=\"btn btn-flat btn-info btn-xs\" href='" . action("ContentManagerController@index", ["contentType" => "news", "topicKey" => $topic['id']]) . "'><i class=\"fa fa-newspaper-o\"></i></a>";
					$html .= "</div>";
				}
			}else{
				$html .= "<span>{$topic['title']}</span>";

				$html .= "<div class='pull-right'>";
				$html .= "<a style=\"margin-top:-6px; margin-right: 6px;color: #fffbfe;\" class=\"btn btn-flat btn-success btn-xs\" href='" . action("SecondCycleController@create", ["cbKey" => $cbKey, "level" => $topic['type'],"parentTopicKey" => $level]) . "'><i class=\"fa fa-plus\"></i></a>";
				$html .= "</div>";
			}
			$html .= "</div>";
			$html .= self::buildSecondCycleTree($cbKey,$data,$topic['id']);
			$html .= "</li>";

		}

		return $html ? "\n<ol class=\"dd-list\">\n$html</ol>\n" : null;
	}
	
	public static function buildSecondCycleTreeCb($cbKey,$data,$level){
		$html = '';
		if (!isset($data[$level]))
			return null;
		foreach ($data[$level] as $l){
			$html .= "<li class='dd-item nested-list-item' data-id='{$l['id']}'>";
			$html .= "<div class='dd-handle dd-nodrag nested-list-handle'>";
			
			$html .= "<span class='glyphicon glyphicon-th-list'></span>";

			$html .= "</div>";
			$html .= "<div class='nested-list-content'>";
			$html .= "<a href='".action("CbsController@show",["type" => "project_2c","cbKey" => $l['cb_key']])."'><span>{$l['title']}</span></a>";

			$html .= "<div class='pull-right'>";
			$html .= "<a style=\"margin-top:-6px; margin-right: 6px;color: #fffbfe;\" class=\"btn btn-flat btn-success btn-xs\" href='" . action("CbsController@create", ["type" => "project_2c","parentCbKey" => $l['cb_key'] ]) . "'><i class=\"fa fa-plus\"></i></a>";
			$html .= "</div>";
			$html .= "</div>";
			$html .= self::buildSecondCycleTreeCb($cbKey,$data,$l['id']);
			$html .= "</li>";
		}

		return $html ? "\n<ol class=\"dd-list\">\n$html</ol>\n" : null;
	}

	public static function getRootCbKey($cb_id){
		$cb = CB::getCbById($cb_id);
		while($cb->parent_cb_id != 0){
			$cb = CB::getCbById($cb->parent_cb_id);
		}

		return $cb->cb_key; 		
	}

	public static function isSubpad($cbkey){
		$result = CB::getCb($cbkey);
		return $result->parent_cb_id != 0;
	}
	
	public function getRootLevel(){
		return $this->root_level;
	}
}
