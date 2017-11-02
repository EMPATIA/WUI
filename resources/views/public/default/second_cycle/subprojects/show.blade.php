@extends('public.default._layouts.index')
@section('header_scripts')
    <!-- Maps -->
    <script   src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCjgiI5l8FanufeE3GRchTZSVOaAyzVIE8" type="text/javascript"></script>

    <!-- Accordeon -->
    <script type="text/javascript" src="{{ asset('js/jquery.accordion.min.js')}}"></script>
    <script type="text/javascript" src="{{ asset('js/default/gantt/d3.min.js')}}"></script>
    <script type="text/javascript" src="{{ asset('js/default/gantt/d3-gantt.js')}}"></script>
@endsection

@section('header_styles')

    <link rel="stylesheet" href="{{ asset('css/default/secondcycle.css')}}" type="text/css" media="screen" />
    <link rel="stylesheet" href="{{ asset('js/default/gantt/d3-gantt.css')}}" type="text/css" media="screen" />
@endsection

@section('content')
<?php
	$created = $space->getAttribute($topicKey,'created');
	$previousSubproject = null;
	$nextSubproject = null;
	foreach($space->getNodes("subprojects") as $key){
		$created_current = $space->getAttribute($key,"created");	
		if($topicKey == $key) continue;
		if($created_current > $created && (is_null($nextSubproject) || $created_current < $space->getAttribute($nextSubproject,'created'))){
			$nextSubproject = $key;
		}elseif($created_current < $created && (is_null($previousSubproject) || $created_current > $space->getAttribute($previousSubproject,'created'))){
			$previousSubproject = $key;
		}
	};
?>
<?php $files = $space->getAttribute($topicKey,'files');?>
    <div class="container container-topic container-subproject">
        <div class="row">
            <div class="col-xs-12">
                <div class="pads-title">
                    <h3>{{ strtoupper($space->getAttribute($topicKey,'title')) }}</h3>
                </div>
            </div>
        </div>
	    
	<div class="description-section row">
	@if (!is_null($space->getAttribute($topicKey,'location')))
	<div class="col-xs-12 col-sm-6"> 
         {!! Form::oneMaps('mapViewN1',null,$space->getAttribute($topicKey,'location'),["readOnly" => true]) !!}
	</div>
	@endif
	<div class="project-description col-xs-12 @if (!is_null($space->getAttribute($topicKey,'location'))) col-sm-6 @endif"> 
	{{$space->getAttribute($topicKey,'description')}}
	</div>
	</div>
	
	<div class="navigation-bar row">
	<div class="col-xs-8">
	@if (!is_null($space->getAttribute($topicKey,'location')))
	<div class="project-location">
	{!! Form::oneReverseGeocoding("streetReverseGeocoding", "", $space->getAttribute($topicKey,'location'), true ) !!}
	</div>
	@endif
	@if (!is_null($space->getAttribute($topicKey,'created_on_behalf')))
	<div class="project-behalf">
	<span class="fa fa-user fa-2x"></span> {{$space->getAttribute($topicKey,'created_on_behalf')}}	
	</div>
	@endif
	</div>

	<div class="col-xs-4">
	@if (isset($previousSubproject))
	<div class="previous-project"><a href="{{action("SecondCycleController@show",["level" => "subprojects","topicKey" => $previousSubproject,"cbKey" => $cbKey]) }}"><span class="fa fa-arrow-left"> </span> {{trans("defaultSecondCycle.previousSubproject")}}</a></div>
	@endif
	@if (isset($nextSubproject))
	<div class="next-project"><a href="{{action("SecondCycleController@show",["level" => "subprojects","topicKey" => $nextSubproject,"cbKey" => $cbKey]) }}">{{trans("defaultSecondCycle.nextSubproject")}} <span class="fa fa-arrow-right"></span> </a></div>
	@endif
	</div>
	</div>

	<?php
		foreach(array("expenditures","news","documents","phases") as $l){
			$nodes = $space->getLinksByLevel($topicKey,$l);
			foreach(array_diff($space->getNodes($l),$nodes) as $n){
				$space->removeNode($n);
			}
		}
		foreach(array_diff($space->getNodes("subprojects"),array($topicKey)) as $n){
			$space->removeNode($n);
		}
	?>

	@include('public.default.second_cycle.subprojectsPercentage',['projectTopicKey' => $parents[0]])
	<?php $has_news = (count($space->getNodes("news")) == 0)?false:true; ?>
	<div class="details-section row">

	<div class="col-xs-12 @if ($has_news) col-sm-4 @else col-sm-6 @endif">

	<div class="subproject-cost-box">
	@if (!is_null($space->getAttribute($topicKey,'budget')))
	<span class="detail-name-big">{{trans("defaultSecondCycle.budget")}}</span>
	<span class="detail-value-big">{{$space->getAttribute($topicKey,'budget')}}€</span>
	@endif
	</div>

	<div class="subproject-progress">
	<?php $percentage = $space->getAttribute($topicKey,'percentage'); ?>
	<div class="status">
		@if ($percentage == 100)
			{{trans("defaultSecondCycle.subprojectCompleted")}}
		@elseif ($percentage == 0)
			{{trans("defaultSecondCycle.subprojectNotStarted")}}
		@else
			{{trans("defaultSecondCycle.subprojectInCourse")}}
		@endif
	</div>
	<div class="progress">
  <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: {{$percentage}}%">
    <span>{{$percentage}}%</span>
  </div>
	</div>


	@if (!is_null($space->getAttribute($topicKey,'start_date')))
	<div>
	<span class="detail-date-name">{{trans("defaultSecondCycle.start_date")}}:</span>
	<span class="detail-date-value">{{$space->getAttribute($topicKey,'start_date')}}</span>
	</div>
	@endif
	@if (!is_null($space->getAttribute($topicKey,'end_date')))
	<div>
	<span class="detail-date-name">{{trans("defaultSecondCycle.end_date")}}:</span>
	<span class="detail-date-value">{{$space->getAttribute($topicKey,'end_date')}}</span>
	</div>
	@endif

	</div>

	@if (!is_null($space->getAttribute($topicKey,'contacts')) && $space->getAttribute($topicKey,'contacts') != "")
	<div class="subproject-contacts-box">
	<span class="detail-name-small">{{trans("defaultSecondCycle.contacts")}}</span>
	<span class="detail-value-small">{{$space->getAttribute($topicKey,'contacts')}}</span>
	</div>
	@endif

	</div>
	
	@if ($has_news)
	<div class="col-sm-4 col-xs-12">
	<?php 
	$last_news = null;
	foreach($space->getNodes("news") as $n){
		if(is_null($last_news) || $space->getAttribute($n,'start_date') > $space->getAttribute($last_news,'start_date'))
			$last_news = $n; 
	}
	?>
	<div class="last_news">
		<div class="last_news_title">{{trans('defaultSecondCycle.last_news')}}</div>	
		<div class="last_news_date">
		@if ($space->getAttribute($last_news,'code_type') == "type1")
		<span class="fa fa-flag"></span>
		@elseif ($space->getAttribute($last_news,'code_type') == "type2")
			<span class="fa fa-check"></span> 
		@else
			<span class="fa fa-remove"></span>
		@endif
		<span class="sr-only">{{$space->getAttribute($last_news,'type')}}</span>
				{{$space->getAttribute($last_news,'start_date')}}
		</div>
		<div class="news_description">{{$space->getAttribute($last_news,'description')}}</div>
	</div>

	</div>
	@endif
	<div class="col-xs-12 @if ($has_news) col-sm-4 @else col-sm-6 @endif">
	<div class="subprojects-details-box">
	@if (!is_null($space->getAttribute($topicKey,'details')) && $space->getAttribute($topicKey,'details') != "")
	<div class="details-expand-box">
	<a role="button" data-toggle="collapse" href="#collapseDetails" aria-expanded="false" aria-controls="collapseDetails">{{trans("defaultSecondCycle.read_more_details")}}</a>
	</div>
	@endif
	@if (isset($files->docs))
	  <a class="btn btn-default backButton" href="{!! action('FilesController@download', [$files->docs[0]->file_id, $files->docs[0]->file_code])!!}" >
                    <span>{{trans('defaultSecondCycle.download_subproject')}}</span>
                </a>

	@endif
	</div>
	<div id="collapseDetails" class="collapse">{{$space->getAttribute($topicKey,'details')}}</div>

	</div>
</div>
<div class="margin-top detail-subprojects">

  <!-- Nav tabs -->
  <ul class="nav nav-pills nav-justified" role="tablist">
    <li role="presentation" class="active"><a href="#subproject" aria-controls="subproject" role="tab" data-toggle="tab">{{trans("defaultSecondCycle.subproject")}}</a></li>
    <li role="presentation"><a href="#news" aria-controls="news" role="tab" data-toggle="tab">{{trans("defaultSecondCycle.news")}}</a></li>
    <li role="presentation"><a href="#documents" aria-controls="documents" role="tab" data-toggle="tab">{{trans("defaultSecondCycle.documents")}}</a></li>
  </ul>

  <!-- Tab panes -->
  <div class="tab-content">
    <div role="tabpanel" class="tab-pane active" id="subproject">
	@include('public.default.second_cycle.subprojects.subproject_tab')
    </div>
    <div role="tabpanel" class="tab-pane" id="news">
	@include('public.default.second_cycle.news.news_tab')
   </div>
    <div role="tabpanel" class="tab-pane" id="documents">
	@include('public.default.second_cycle.documents.documents_tab')
    </div>
  </div>

</div>



	  <a class="btn btn-default backButton" href="{!! action('SecondCycleController@show', ["cbKey" => $cbKey,'level' => 'projects', 'topicKey' => $parents[0]])!!}" >
                    <span>{{trans('defaultSecondCycle.back')}}</span>
                </a>

    </div>

@endsection
