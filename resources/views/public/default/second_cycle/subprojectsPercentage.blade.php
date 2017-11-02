<?php
$projects = array_diff($space->getNodes("projects"),array($projectTopicKey));
foreach($projects as $p){
	$space->removeAllChildren($p);
}

$space->exploreLevel("phases", function($name, $attributes) use($space){
	$subprojects = $space->getLinksByLevel($name,"subprojects");
	$sp = array_pop($subprojects);
	if(!($sp))
		return;

	if(isset($attributes['code_state']) && $attributes['code_state'] == "deleted")
		return;
		
	$start_date = (isset($attributes['real_start_date']) && $attributes['real_start_date'] != "")?$attributes['real_start_date']:$attributes['start_date'];
	$end_date = (isset($attributes['real_end_date']) && $attributes['real_end_date'] != "" )?$attributes['real_end_date']:$attributes['end_date'];

	$sp_phase_start_date = $space->getAttribute($sp,'phase_start_date');
	$sp_phase_end_date = $space->getAttribute($sp,'phase_end_date');
	if(!is_null($start_date) &&  $start_date != "" && (is_null($sp_phase_start_date) || $sp_phase_start_date > $start_date)){
		$space->setAttribute($sp,'phase_start_date',$start_date);
	}

	if(!is_null($end_date) &&  $end_date != "" && (is_null($sp_phase_end_date) || $sp_phase_end_date < $end_date)){
		$space->setAttribute($sp,'phase_end_date',$end_date);
	}
});

foreach(array("news","documents","phases","expenditures") as $l){
	$space->exploreLevel($l, function($name, $attributes) use($space){
		$subprojects = $space->getLinksByLevel($name,"subprojects");
		$sp = array_pop($subprojects);
		if(!($sp))
			return;

		/* Check last update */
		if(is_null($space->getAttribute($sp,'last_update'))){
			$space->setAttribute($sp,'last_update',$space->getAttribute($sp,'modified'));
		}

		if($space->getAttribute($sp,'last_update') < $attributes['modified']){
			$space->setAttribute($sp,'last_update',$attributes['modified']);
		}	
	});
}

$status = 0;

$space->setAttribute($projectTopicKey,'last_update',$space->getAttribute($projectTopicKey,'modified'));

foreach($space->getNodes("subprojects") as $sp){
	$attributes = $space->getAttributes($sp);
	$projects = $space->getLinksByLevel($sp,"projects");
	$p = array_pop($projects);
	$start_date = $space->getAttribute($sp,'phase_start_date');
	$end_date = $space->getAttribute($sp,'phase_end_date');

	if(is_null($space->getAttribute($sp,'last_update'))){
		$space->setAttribute($sp,'last_update',$space->getAttribute($sp,'modified'));
	}

	if(is_null($space->getAttribute($p,'last_update'))){
		$space->setAttribute($p,'last_update',$space->getAttribute($p,'modified'));
	}

	if($space->getAttribute($p,'last_update') < $space->getAttribute($sp,'last_update')){
		$space->setAttribute($p,'last_update',$space->getAttribute($sp,'last_update'));
	}

	if(!$start_date || !$end_date){
		$space->setAttribute($sp,'percentage','0');
		return;
	}

	$delta1 = strtotime("now") - strtotime($start_date); 
	$delta2 = strtotime($end_date) - strtotime($start_date); 
	
	if($delta2 <= 0){
		$space->setAttribute($sp,'percentage','0');
		return;
	}

	$k = (double)$delta1/$delta2;
	if($k > 1){
		$space->setAttribute($sp,'percentage','100');
	}elseif($k < 0){
		$space->setAttribute($sp,'percentage','0');
	}else{
		$space->setAttribute($sp,'percentage',floor($k*100));
	}

	if($status == -1 && $k >= 1 || $k >= 1 && $status == 2){
		$status = 2;
	}elseif($status == -1 && $k <= 0 || $k <= 0 && $status == 0){
		$status = 0;
	}else{
		$status = 1;
	}

};

$space->setAttribute($projectTopicKey,'status',$status);

?>
