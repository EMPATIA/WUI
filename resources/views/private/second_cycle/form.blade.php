@extends('private._private.index')

@section('header_scripts')
    <!-- Maps -->
    <script  src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCn-K_QLK1mNPM6SjCjnUl2e3neuQ9FX6Q&libraries=places" type="text/javascript"></script>
	<!-- Plupload Javascript fix and bootstrap fix @ start -->
	<link rel="stylesheet" href="/bootstrap/plupload-fix/bootstrap.css">
	<script src="/bootstrap/plupload-fix/bootstrap.min.js"></script>
	<!-- Plupload Javascript fix and bootstrap fix @ End -->
	<script src="{{ asset('vendor/jildertmiedema/laravel-plupload/js/plupload.full.min.js') }}"></script>
	<script src="{{ asset("js/cropper.min.js") }}"></script>
@endsection
@section('content')
<div id="form-2c-container">
@yield('form_init')
<div class="box-header">
<h3 class="box-title">{{trans('secondCycle.details')}}</h3>
</div>
<div class="box-body">
<div class="form-group">
   {!! Form::label('title', trans('secondCycle.titleRequired'), ['class' => 'form-control-label']) !!}
    <div>
	{!! Form::text('title',$space->getAttribute($topicKey,'title'), ['class' => 'form-control', 'required' => 'required']) !!}
    </div>
</div>

<div class="form-group">
	@if ($level=="expenditures" || $level=="phases")
		{!! Form::label('description', trans('secondCycle.description'), ['class' => 'form-control-label']) !!}
		<div>
			{!! Form::textArea('description', $space->getAttribute($topicKey,'description'), ['class' => 'form-control', 'rows' => 2]) !!}
		</div>
	@else
		{!! Form::label('description', trans('secondCycle.descriptionRequired'), ['class' => 'form-control-label']) !!}
		<div>
			{!! Form::textArea('description', $space->getAttribute($topicKey,'description'), ['class' => 'form-control', 'rows' => 2, 'required' => 'required']) !!}
		</div>
	@endif
</div>

<div class="form-group">
   {!! Form::label('created_on_behalf', trans('secondCycle.created_on_behalf'), ['class' => 'form-control-label']) !!}
    <div>
	{!! Form::text('created_on_behalf', $space->getAttribute($topicKey,'created_on_behalf'), ['class' => 'form-control']) !!}
    </div>
</div>

<div class="form-group">
   {!! Form::label('start_date', trans('secondCycle.startDate'), ['class' => 'form-control-label']) !!}
    <div>

    {!! Form::text('start_date', $space->getAttribute($topicKey,'start_date'), [ 'class' => 'oneDatePicker form-control', "date-format" => "yyyy-mm-dd"]) !!}
    </div>
</div>

<div class="form-group">
   {!! Form::label('end_date',  trans('secondCycle.endDate'), ['class' => 'form-control-label']) !!}
    <div>

    {!! Form::text('end_date', $space->getAttribute($topicKey,'end_date'), [ 'class' => 'oneDatePicker form-control', "date-format" => "yyyy-mm-dd"]) !!}
    </div>
</div>
@php foreach($parameters as $p): @endphp
<div class="form-group">
{!! Form::label($p['code'], (($p['mandatory'] == 1)?"* ".$p['name']:$p['name']), ['class' => 'form-control-label']) !!}
	@if (in_array($p['type'],array('coin','numeric')))
	<div>
		@if ($p['mandatory'] == 1)
			{!! Form::input('number',$p['code'], $space->getAttribute($topicKey,$p['code']), ['class' => 'form-control', 'required' => 'required']) !!}
		@else
			{!! Form::input('number', $p['code'],$space->getAttribute($topicKey,$p['code']), ['class' => 'form-control']) !!}
		@endif
	</div>
	@elseif (in_array($p['type'],array('text')))
	<div>
		@if ($p["code"]=="real_start_date" || $p["code"]=="real_end_date")
			@if ($p['mandatory'] == 1)
				{!! Form::text($p['code'], $space->getAttribute($topicKey,$p['code']), ['class' => 'oneDatePicker form-control', "date-format" => "yyyy-mm-dd", 'required' => 'required']) !!}
			@else
				{!! Form::text($p['code'], $space->getAttribute($topicKey,$p['code']), ['class' => 'oneDatePicker form-control', "date-format" => "yyyy-mm-dd"]) !!}
			@endif
		@else
			@if ($p['mandatory'] == 1)
				{!! Form::text($p['code'], $space->getAttribute($topicKey,$p['code']), ['class' => 'form-control', 'required' => 'required']) !!}
			@else
				{!! Form::text($p['code'], $space->getAttribute($topicKey,$p['code']), ['class' => 'form-control']) !!}
			@endif
		@endif
	</div>
	@elseif (in_array($p['type'],array('date')))
	<div>
		@if ($p['mandatory'] == 1)
			{!! Form::text($p['code'], $space->getAttribute($topicKey,$p['code']), [ 'class' => 'oneDatePicker form-control', "date-format" => "yyyy-mm-dd", 'required' => 'required']) !!}
		@else
			{!! Form::text($p['code'], $space->getAttribute($topicKey,$p['code']), [ 'class' => 'oneDatePicker form-control', "date-format" => "yyyy-mm-dd"]) !!}
		@endif
	</div>
	@elseif (in_array($p['type'],array('text_area')))
	<div>
		@if ($p['mandatory'] == 1)
			{!! Form::textArea($p['code'], $space->getAttribute($topicKey,$p['code']), ['class' => 'form-control', 'required' => 'required']) !!}
		@else
			{!! Form::textArea($p['code'], $space->getAttribute($topicKey,$p['code']), ['class' => 'form-control']) !!}
		@endif
	</div>
	@elseif(in_array($p['type'],array('radio_buttons')))
		@foreach ($p['options'] as $k => $v)
		<div class="radio">
			<label for="radio_{{$k}}">
			@if ($p['mandatory'] == 1)
			    {!! Form::radio($p['code'], $k, ($space->getAttribute($topicKey,$p['code']) == $v)?true:false, ['id'=>'radio_'.$k, 'required' => 'required']) !!}
			@else
			    {!! Form::radio($p['code'], $k, ($space->getAttribute($topicKey,$p['code']) == $v)?true:false, ['id'=>'radio_'.$k]) !!}
			@endif
			{{$v}}
			</label>
		</div>
		@endforeach
	@elseif (in_array($p['type'],array('dropdown')) || in_array($p['type'],array('category')))
		<div>
			@php $opt = array_flip($p['options']); @endphp
			@if ($p['mandatory'] == 1)
				{!! Form::select($p['code'], $p['options'], ((!$space->getAttribute($topicKey,$p['code']))?null:$opt[$space->getAttribute($topicKey,$p['code'])]), ['class' => 'form-control', 'required' => 'required']) !!}
			@else
				{!! Form::select($p['code'], $p['options'], ((!$space->getAttribute($topicKey,$p['code']))?null:$opt[$space->getAttribute($topicKey,$p['code'])]),['class' => 'form-control']) !!}
			@endif
		</div>
	@elseif (in_array($p['type'],array('google_maps')))
	<div>

	    {!! Form::oneMaps($p['code'],"", ((!$space->getAttribute($topicKey,$p['code']))?null:$space->getAttribute($topicKey,$p['code'])),["required" => $p["mandatory"], "defaultLocation" => "38.7436213,-9.1952232", "enableSearch" => true, "readOnly" => false]) !!}
	</div>
	@endif 

</div>
@php endforeach @endphp

		<!-- Files -->

                    @if(isset($configurations) && ((ONE::checkCBsOption($configurations, 'ALLOW-FILES')) || (ONE::checkCBsOption($configurations, 'ALLOW-PICTURES'))))
<div class="form-group">
                        <div class="parameter">
			    <div class="form-group" id="attachments-container">

                        <label for="title">{{ trans("cb.add_files") }}</label>
                        {!! ONE::fileSimpleUploadBox("drop-zone", trans("cb.drag_and_drop_files_to_here") , trans('PublicCbs.files'), 'select-files', 'files-list', 'files') !!}
                            </div>
			</div>

</div>
@endif
</div>
<div class="box-footer">
	{!! Form::submit(trans('secondCycle.save'), ['id' => 'submit-2c-form', 'class' => 'btn btn-flat empatia'] ) !!}
<a class="btn btn-flat btn-secondary" href="{{action('SecondCycleController@manage',['cbKey' => $cbKey,"level" => "project_2c"])}}"> {{trans('secondCycle.cancel')}}</a>
</div>

{!! Form::close()  !!}
</div>

@endsection
@section('scripts')

@include('private._private.functions') {{-- Helper Functions --}}

    <script>
        $(function() {
            var array = ["project_2c", "{{$cbKey}}"];
            getSidebar('{{ action("OneController@getSidebar") }}', 'second_cycle', array, 'padsType' );
        });
    </script>


<script>
var url_updateFiles = '{{action('SecondCycleController@update_files',['cbKey' => $cbKey, 'cbKeyChild' => $cbKeyChild])}}';

$('#form-2c-container form').on('submit',function(ev){
	$('#submit-2c-form').attr('disabled','disabled');
});

$(document).on('files-updated','#files',function(){
	$.get(url_updateFiles);
});
</script>
		<script>
			    {!! ONE::fileUploader('fileUploader', action('FilesController@upload'), 'ideaFileUploaded', 'select-files', 'drop-zone', 'files-list', 'files', 1, isset($uploadKey) ? $uploadKey : "", $allowFiles) !!}
			    fileUploader.init();
			    updateClickListener();
			    updateFilesPostList('#files',1);
		</script>
@endsection
