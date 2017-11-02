<?php
	$expenditures = 0;
	foreach($space->getNodes("expenditures") as $e){
		$tmp = $space->getAttribute($e,'budget');
		if(is_numeric($tmp))
			$expenditures = $expenditures + $tmp;	
	}
	$cost = $space->getAttribute($topicKey,"budget");
	if(!is_numeric($cost) || $cost <= 0){
		$percentage_expenditures = 0;
	}else{
		$percentage_expenditures = (double)$expenditures/$cost;
		if($percentage_expenditures > 1)
			$percentage_expenditures = 1;
		$percentage_expenditures = floor($percentage_expenditures * 100);
	}	
?>
<div class="margin-top">
<div class="table-responsive">
<table class="table table-phases">
<thead>
<tr>
<th>{{trans("defaultSecondCycle.phase")}}</th>
<th>{{trans("defaultSecondCycle.state")}}</th>
<th>{{trans("defaultSecondCycle.dates_expected")}}: {{trans("defaultSecondCycle.start_end")}}</th>
<th>{{trans("defaultSecondCycle.dates_real")}}: {{trans("defaultSecondCycle.start_end")}}</th>
<th>{{trans("defaultSecondCycle.output")}}</th>
</tr>
</thead>
<tbody>
@foreach($space->getNodes("phases") as $p)
<tr>
<td>{{$space->getAttribute($p,"title")}}</td>
<td>{{$space->getAttribute($p,"state")}}</td>
<td>@if (!is_null($space->getAttribute($p,"start_date"))) {{$space->getAttribute($p,"start_date")}} - {{$space->getAttribute($p,"end_date")}} @endif </td>
<td>@if (!is_null($space->getAttribute($p,"real_start_date"))) {{$space->getAttribute($p,"real_start_date")}} - {{$space->getAttribute($p,"real_end_date")}} @endif</td>
<td>
<?php $tmp = $space->getAttribute($p,"files"); ?>
@if (isset($tmp->docs))
<a href="{!! action('FilesController@download', [$tmp->docs[0]->file_id, $tmp->docs[0]->file_code])!!}"> [{{$space->getAttribute($p,"output")}}] <span class="fa fa-download"></span></a>	
@endif
</td>

</tr>
@endforeach
</tbody>
</table>
</div>
</div>

<div class="margin-top">
@include('public.default.second_cycle.phases.gantt')
</div>

@if (!empty($space->getNodes('expenditures')))
<div class="margin-top">
<div class="expenditure-text">{{trans("defaultSecondCycle.how_the_expenditures_proceed")}}</div>
<div class="expenditure-progress">{{trans("defaultSecondCycle.expenditures_progress")}}</div>
   <div class="progress">
  <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: {{$percentage_expenditures}}%">
    <span>{{$percentage_expenditures}}%</span>
  </div>
</div>
<div class="table-responsive">
<table class="table table-expenditures">
<thead>
<tr>
<th>{{trans("defaultSecondCycle.date")}}</th>
<th>{{trans("defaultSecondCycle.title_expenditure")}}</th>
<th>{{trans("defaultSecondCycle.download_expenditure")}}</th>
<th>{{trans("defaultSecondCycle.cost")}}</th>
</tr>
</thead>
<tbody>
@foreach($space->getNodes("expenditures") as $p)
<tr>
<td>{{$space->getAttribute($p,"start_date")}}</td>
<td>{{$space->getAttribute($p,"title")}}</td>
<td>
<?php $tmp = $space->getAttribute($p,"files"); ?>
@if (isset($tmp->docs))
<a href="{!! action('FilesController@download', [$tmp->docs[0]->file_id, $tmp->docs[0]->file_code])!!}" title="{{trans("defaultSecondCycle.download_expenditure")}}"> <span class="fa fa-download"> </span></a>	
@endif
</td>
<td>€ {{$space->getAttribute($p,"budget")}}</td>
</tr>
@endforeach
</tbody>
</table>
</div>

</div>
@endif
