@extends('public.default._layouts.index')
@section('header_scripts')
    <!-- Maps -->
    <script   src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCjgiI5l8FanufeE3GRchTZSVOaAyzVIE8" type="text/javascript"></script>

    <!-- Accordeon -->
    <script type="text/javascript" src="{{ asset('js/jquery.accordion.min.js')}}"></script>
@endsection

@section('header_styles')

    <link rel="stylesheet" href="{{ asset('css/default/secondcycle.css')}}" type="text/css" media="screen" />
@endsection

@section('content')
<?php
	$created = $space->getAttribute($topicKey,'created');
	$previousProject = null;
	$nextProject = null;
	foreach($space->getNodes("projects") as $key){
		$created_current = $space->getAttribute($key,"created");	
		if($topicKey == $key) continue;
		if($created_current > $created && (is_null($nextProject) || $created_current < $space->getAttribute($nextProject,'created'))){
			$nextProject = $key;
		}elseif($created_current < $created && (is_null($previousProject) || $created_current > $space->getAttribute($previousProject,'created'))){
			$previousProject = $key;
		}
	};
?>
<?php $files = $space->getAttribute($topicKey,'files');?>
    <div class="container container-topic container-project">
        <div class="row">
            <div class="col-xs-12">
                <div class="pads-title">
                    <h3>{{ strtoupper($space->getAttribute($topicKey,'title')) }}</h3>
                </div>
            </div>
        </div>
	    
	<div class="description-section row">
	@if (isset($files->images))
	<div class="col-xs-12 col-sm-6">
	<img class="img-project" src="{{action('FilesController@download', [$files->images[0]->file_id, $files->images[0]->file_code])}}" alt="Project image" />
	</div>
	@endif
	<div class="project-description col-xs-12 @if (isset($files->images)) col-sm-6 @endif">
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
	@if (isset($previousProject))
	<div class="previous-project"><a href="{{action("SecondCycleController@show",["level" => "projects","topicKey" => $previousProject,"cbKey" => $cbKey]) }}"><span class="fa fa-arrow-left"> </span> {{trans("defaultSecondCycle.previousProject")}}</a></div>
	@endif
	@if (isset($nextProject))
	<div class="next-project"><a href="{{action("SecondCycleController@show",["level" => "projects","topicKey" => $nextProject,"cbKey" => $cbKey]) }}">{{trans("defaultSecondCycle.nextProject")}} <span class="fa fa-arrow-right"></span> </a></div>
	@endif
	</div>
	</div>

	@include('public.default.second_cycle.subprojectsPercentage',['projectTopicKey' => $topicKey])
	<div class="details-section row">
	<div class="col-xs-12 col-sm-3 category-box">
	@if (!is_null($space->getAttribute($topicKey,'category')))
	<span class="detail-name-small">{{trans("defaultSecondCycle.category")}}</span>
	<span class="detail-value-big">{{$space->getAttribute($topicKey,'category')}}</span>
	@endif
	</div>
	<div class="col-xs-12 col-sm-3 num-votes-box">
	@if (!is_null($space->getAttribute($topicKey,'num_votes')))
	<span class="detail-name-big">{{trans("defaultSecondCycle.num_votes")}}</span>
	<span class="detail-value-big">{{$space->getAttribute($topicKey,'num_votes')}}</span>
	@endif
	</div>

	<div class="col-xs-12 col-sm-3 cost-box">
	@if (!is_null($space->getAttribute($topicKey,'budget')))
	<span class="detail-name-big">{{trans("defaultSecondCycle.budget")}}</span>
	<span class="detail-value-big">{{$space->getAttribute($topicKey,'budget')}}€</span>
	@endif
	</div>
	<div class="col-xs-12 col-sm-3 details-box">
	@if (!is_null($space->getAttribute($topicKey,'details')) && $space->getAttribute($topicKey,'details') != "")
	<a role="button" data-toggle="collapse" href="#collapseDetails" aria-expanded="false" aria-controls="collapseDetails">{{trans("defaultSecondCycle.read_more_details")}}</a>
	@endif
	@if (isset($files->docs))
	  <a class="btn btn-default backButton" href="{!! action('FilesController@download', [$files->docs[0]->file_id, $files->docs[0]->file_code])!!}" >
                    <span>{{trans('defaultSecondCycle.download_project')}}</span>
                </a>

	@endif
	</div>
	</div>
   
	<div class="status-section row">
	<div class="col-xs-12 col-sm-3">
	<div class="status-box">
	@if (!is_null($space->getAttribute($topicKey,'status')))
	<div class="status">
	<span class="detail-name-small">{{trans("defaultSecondCycle.status")}}</span>
	<span class="detail-value-big">
		@if ($space->getAttribute($topicKey,'status') <= 0)
		{{trans('defaultSecondCycle.projectNotStarted')}}
		@elseif($space->getAttribute($topicKey,'status') == 1)
		{{trans('defaultSecondCycle.projectInCourse')}}
		@else
		{{trans('defaultSecondCycle.projectCompleted')}}
		@endif

		</span>
	</div>
	@endif
	@if (!is_null($space->getAttribute($topicKey,'start_date')))
	<div>
	<span class="detail-date-name">{{trans("defaultSecondCycle.start_date")}}</span>
	<span class="detail-date-value">{{$space->getAttribute($topicKey,'start_date')}}</span>
	</div>
	@endif
	@if (!is_null($space->getAttribute($topicKey,'end_date')))
	<div>
	<span class="detail-date-name">{{trans("defaultSecondCycle.end_date")}}</span>
	<span class="detail-date-value">{{$space->getAttribute($topicKey,'end_date')}}</span>
	</div>
	@endif
	</div>

	<div class="modified-box">
	<span class="detail-name-small">{{trans("defaultSecondCycle.last_update")}}</span>
	<span class="detail-value-big">{{date('Y-m-d',strtotime($space->getAttribute($topicKey,'last_update')))}}</span>
	</div>

	@if (!is_null($space->getAttribute($topicKey,'contacts')) && $space->getAttribute($topicKey,'contacts') != "")
	<div class="contacts-box">
	<span class="detail-name-small">{{trans("defaultSecondCycle.contacts")}}</span>
	<span class="detail-value-small">{{$space->getAttribute($topicKey,'contacts')}}</span>
	</div>
	@endif
	</div>
	<div class="col-xs-12 col-sm-9">
	<div id="collapseDetails" class="collapse">
	{{$space->getAttribute($topicKey,'details')}}
	</div>
	</div>
	</div>

	<div class="margin-top">
	<div class="row">
	<div class="col-xs-12">

	<div class="text-list-subprojects">{{trans('defaultSecondCycle.list_subprojects')}}</div>
	<div class="table-responsive">
	<table class="table table-subprojects">
	<thead>
	<tr>
		<th>{{trans('defaultSecondCycle.subproject')}}</th>
		<th>{{trans('defaultSecondCycle.expected_start_date')}}</th>
		<th>{{trans('defaultSecondCycle.expected_end_date')}}</th>
		<th>{{trans('defaultSecondCycle.last_update')}}</th>
		<th>{{trans('defaultSecondCycle.process')}}</th>
		<th>{{trans('defaultSecondCycle.view_more')}}</th>
	</tr>
	</thead>
	<tbody>
	@foreach ($space->getNodes("subprojects") as $sp)
	<tr>
	<td>{{$space->getAttribute($sp,"title")}}</td>
	<td>{{$space->getAttribute($sp,"start_date")}}</td>
	<td>{{$space->getAttribute($sp,"end_date")}}</td>
	<td>{{date('Y-m-d',strtotime($space->getAttribute($sp,"last_update")))}}</td>
	<td><div class="progress"><div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="{{$space->getAttribute($sp,"percentage")}}" aria-valuemin="0" aria-valuemax="100" style=" width:{{$space->getAttribute($sp,"percentage")}}%">{{$space->getAttribute($sp,"percentage")}}% </div></div>
	</td>
	<td><a href="{{action("SecondCycleController@show",["topicKey" => $sp,"level" => "subprojects", "cbKey" => $cbKey])}}" title="{{trans("defaultSecondCycle.view_more")}}"><span class="fa fa-eye"></span></a></td>

	</tr>
	@endforeach
	</tbody>
	</table>
	</div>

	</div>
	</div>
	</div>

	  <a class="btn btn-default backButton" href="{!! action('SecondCycleController@index', ["cbKey" => $cbKey])!!}" >
                    <span>{{trans('defaultSecondCycle.back')}}</span>
                </a>

    </div>

@endsection

@section('scripts')
    <script>

    </script>
@endsection
