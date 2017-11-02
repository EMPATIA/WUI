<?php
	$phases_gantt = array();
	$num = 1;
	$phases = $space->getNodes("phases");
	usort($phases,function($a,$b) use($space){
		$dA = $space->getAttribute($a,"start_date");
		$dB = $space->getAttribute($b,"start_date");
		if($dA == $dB)
			return 0;
		
		if($dA > $dB)
			return 1;

		return -1;

	});	
	foreach($phases as $p){
		$start_date = $space->getAttribute($p,'start_date');
		$end_date = $space->getAttribute($p,'end_date');
		$real_start_date = $space->getAttribute($p,'real_start_date');
		$real_end_date = $space->getAttribute($p,'real_end_date');
		if(isset($start_date) && isset($end_date)){
			$phases_gantt[] = array("start" => $start_date,"end" => $end_date,"activity" => trans("defaultSecondCycle.expected"),"text" => $space->getAttribute($p,'title'),'alias' => "empatia-gantt-$num");
		}
		if(isset($real_start_date) && isset($real_end_date)){
			$phases_gantt[] = array("start" => $real_start_date,"end" => $real_end_date,"activity" => trans("defaultSecondCycle.real"),"text" => $space->getAttribute($p,'title'),'alias' => "empatia-gantt-$num");
		}
		$num = $num + 1;
	}

?>

<div id="gantt-phases-container">
<div id="gantt-phases">
<?php $num = 1; ?>
@foreach($phases as $p)
<div class="empatia-gantt-{{$num}}" data-alias="empatia-gantt-{{$num}}"></div>

<?php $num = $num + 1; ?>
@endforeach
</div>
</div>

<script>
$(document).ready(function(){
	var selector = "#gantt-phases";

	var tasks = {!! json_encode($phases_gantt) !!};
	var days = 0;
	$.each(tasks,function(i,v){
		tasks[i].start = new Date(tasks[i].start);
		tasks[i].end = new Date(tasks[i].end);
		days = days + Math.floor((tasks[i].end - tasks[i].start) / (1000*60*60*24));
		var fill = $(selector+' div[data-alias="'+v.alias+'"]').css('fill');
		if (fill)
			tasks[i].fillColor = fill;
		tasks[i].strokeColor = "#000";
	});
	var interval = null;
	if(tasks.length > 0)
		days = days/(tasks.length);
	if(days/365 > 1){
		interval = d3.timeYear.every(1);
	}else if(days/30 > 1){
		interval = d3.timeMonth.every(1);
	}else if(days/15 > 1){
		interval = d3.timeWeek.every(1);
	}else{
		interval = d3.timeDay.every(1);
	}

	var activities = [ { name: "{{trans("defaultSecondCycle.real")}}"} , { name: "{{trans("defaultSecondCycle.expected")}}" }];

	var gantt = Object.create(d3.ganttChart);

	gantt.init({
	  // ID of the HTML element that will contain the Gantt chart
	  node: selector,

	  // Y-axis values; description contains the text shown as tooltip when hovering over the Y-axis labels
	  activities: activities,

	  // data to describe the elements             
	  data: tasks,
	  
	xAxis: {
	    interval: interval,
                height: 80,
                label: {
                  format: '%Y-%m-%d',
                }

	  },

	  yAxis: {
	    width: 50
	  }
	});

	// Show chart
	gantt.draw();
});
</script>
