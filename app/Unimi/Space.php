<?php

namespace App\Unimi;

class Space{
	
	private $levels = array();

	private $nodes = array();

	private $links = array();

	public function addLevel($name){
	
		if(!is_string($name))
			return false;

		$this->levels[$name] = array();
	}

	public function removeLevel($name){
		if(!isset($this->levels[$name]))
			return true;

		$this->links = array_diff($this->links,$this->levels[$name]);
	
		foreach($this->links as $link => $v){
			$this->links[$link] = array_diff($this->links[$link],$this->levels[$name]);
		}

		$this->nodes = array_diff($this->nodes,$this->levels[$name]);
		unset($this->levels[$name]);

		return true;
	}

	public function addNode($level,$node,$attributes = array()){
		if(!isset($this->levels[$level]))
			return false;

		if(!is_string($node))
			return false;

		if(isset($this->nodes[$node]))
			return false;

		$this->nodes[$node] = $attributes;
		$this->levels[$level][] = $node;
	}

	public function removeNode($node){
		if (!isset($this->nodes[$node]))
			return true;

		unset($this->nodes[$node]);

		foreach($this->levels as $name => $v){
			$this->levels[$name] = array_diff($this->levels[$name],array($node));
		}

		unset($this->links[$node]);
		
		foreach($this->links as $name => $v){
			$this->links[$name] = array_diff($this->links[$name],array($node));
		}

		return true;
	}

	public function addLink($node1,$node2,$simmetric=true){
		if(!isset($this->nodes[$node1]))
			return false;

		if(!isset($this->nodes[$node2]))
			return false;

		if(!isset($this->links[$node1]) || !in_array($node2,$this->links[$node1])){
			if (!isset($this->links[$node1])){
				$this->links[$node1] = array($node2);
			}else{
				$this->links[$node1][] = $node2;
			}
		}

		if($simmetric){
			return $this->addLink($node2,$node1,false);	
		}

		return true;
	}

	public function removeLinkNodes($node1,$node2,$simmetric=true){
		if(!isset($this->nodes[$node1]))
			return true;

		if(!isset($this->nodes[$node2]))
			return true;

		$this->links[$node1] = array_diff($this->links[$node1],array($node2));

		if($simmetric){
			$this->links[$node2] = array_diff($this->links[$node2],array($node1));
		}

		return true;
	}

	public function isLinked($node1,$node2){
		if(!isset($this->nodes[$node1]))
			return false;

		if(!isset($this->nodes[$node2]))
			return false;
		$nodes = $this->getIndirectLinks($node1);
		if(in_array($node2,$nodes))
			return true;

		return false;
	}

	public function existsNode($node){
		if(!isset($this->nodes[$node]))
			return false;

		return true;
	}

	public function hasLinks($node){
		if(!isset($this->nodes[$node]))
			return false;

		return !empty($this->links[$node]);
	}

	public function hasNode($level,$node){
		if(!isset($this->nodes[$node]))
			return false;

		if(!isset($this->levels[$level]))
			return false;

		if(in_array($node,$this->levels[$level]))
			return true;

		return false;
	}

	public function existsLevel($level){
		if(!isset($this->levels[$level]))
			return false;

		return true;
	}

	public function getLevel($node){
		if(!isset($this->nodes[$node]))
			return false;

		foreach($this->levels as $name => $v){
			if($this->hasNode($name,$node))
				return $name;	
		}

		return false;
	}

	public function getLevels(){
		return array_keys($this->levels);
	}

	public function getAttributes($node){
		if(!isset($this->nodes[$node]))
			return null;

		return $this->nodes[$node];
	}
	
	public function getAttribute($node,$attribute){
		if(!isset($this->nodes[$node][$attribute]))
			return null;

		return $this->nodes[$node][$attribute];
	}

	public function setAttributes($node,$attributes){
		if(!isset($this->nodes[$node]))
			return false;

		$this->nodes[$node] = $attributes;

		return true;
	}

	public function setAttribute($node,$attribute,$value){
		if(!isset($this->nodes[$node]) || !is_string($attribute))
			return false;

		$this->nodes[$node][$attribute] = $value;

		return true;
	}


	public function getLinks($node){
		if(!isset($this->links[$node]))
			return array();

		return  $this->links[$node];
	}

	public function getIndirectLinks($node){
		if(!isset($this->nodes[$node]))
			return array();

		$queue = array($node);
		$visited = array();
		while(!empty($queue)){
			$n = array_pop($queue);
			$visited[] = $n;
			$tmp = array_diff($this->getLinks($n),$visited);
			$queue = array_diff(array_merge($queue,$tmp));
		}

		return $visited;
	}

	public function getLinksByLevel($node,$level){
		if(!isset($this->nodes[$node]))
			return array();
		if(!isset($this->levels[$level]))
			return array();
		
		$nodes = $this->getLinks($node);

		$tmp = array_intersect($nodes,$this->levels[$level]);

		return $tmp;
	}

	public function getIndirectLinksByLevel($node,$level){
		if(!isset($this->nodes[$node]))
			return array();
		if(!isset($this->levels[$level]))
			return array();
		
		$nodes = $this->getIndirectLinks($node);

		$tmp = array_intersect($nodes,$this->levels[$level]);

		return $tmp;
	}

	public function removeAllChildren($node){
		if(!isset($this->nodes[$node]))
			return true;
		
		$queue = array($node);

		while(!empty($queue)){
			$n = array_pop($queue);
			if(isset($this->links[$n])){
				$queue = array_unique(array_merge($queue,$this->links[$n]));
			}
			$this->removeNode($n);
		}

		return true;	
	}

	public function exploreLevel($name,$function){
		$nodes = array();
		if(!isset($this->levels[$name]))
			return array();
	
		foreach($this->levels[$name] as $node){
			if($function($node,$this->nodes[$node]))
				$nodes[] = $node;
		}

		return $nodes;
	}

	public function getNodes($name=null){
		if(is_null($name))
			return array_keys($this->nodes);
		if(!isset($this->levels[$name]))
			return array();
	
		return $this->levels[$name];
	}

	public function removeNotLinkedNodes($level=null){	
		if(!is_null($level) && !isset($this->levels[$level]))
			return true;
		if(is_null($level)){
			$levels = array_keys($this->levels);
		}else{
			$levels = array($level);
		}
		
		foreach($levels as $l){
			foreach($this->levels[$l] as $node){
				if (!isset($this->links[$node]) || empty($this->links[$node])){
					$this->removeNode($node);
				}
			}
		}

		return true;
	}

}
