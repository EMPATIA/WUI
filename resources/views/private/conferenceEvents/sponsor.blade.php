@extends('private._private.index')
@section('header_styles')
    <link href="{{ asset("css/cropper.min.css") }}" rel='stylesheet' type='text/css'>
@endsection
@section('content')
    @php

    $form = ONE::form('sponsor')
            ->settings(["model" => isset($sponsor) ? $sponsor->sponsor_key : null,'id'=>isset($sponsor) ? $sponsor->sponsor_key : null])
            ->show('ConferenceEventSponsorsController@edit', 'ConferenceEventSponsorsController@delete', ['eventKey' =>isset($eventKey) ? $eventKey : null,'sponsorKey'=> isset($sponsor) ? $sponsor->sponsor_key : null], 'ConferenceEventsController@show', ['eventKey' => isset($eventKey) ? $eventKey : null])
            ->create('ConferenceEventSponsorsController@store', 'ConferenceEventsController@show', ['eventKey' =>isset($eventKey) ? $eventKey : null])
            ->edit('ConferenceEventSponsorsController@update', 'ConferenceEventSponsorsController@show', ['eventKey' =>isset($eventKey) ? $eventKey : null,'sponsorKey'=> isset($sponsor) ? $sponsor->sponsor_key : null])
            ->open();
    @endphp
    {!! Form::hidden('event_key', isset($eventKey) ? $eventKey : null) !!}
    {!! Form::hidden('sponsor_key', isset($sponsor) ? $sponsor->sponsor_key : null) !!}
    {!! Form::hidden('file_id', isset($sponsor) ? $sponsor->file_id : null, ['id' => 'file_id']) !!}
    {!! Form::oneText('name', trans('conferenceEvents.name'), isset($sponsor) ? $sponsor->name : null, ['class' => 'form-control', 'id' => 'name' ,  'required']) !!}
    {!! Form::oneText('description', trans('conferenceEvents.description'), isset($sponsor) ? $sponsor->description : null, ['class' => 'form-control', 'id' => 'description' ,  'required']) !!}
    {!! Form::oneText('email', trans('conferenceEvents.email'), isset($sponsor) ? $sponsor->email : null, ['class' => 'form-control', 'id' => 'email' ,  'required']) !!}
    {!! Form::oneText('web_page', trans('conferenceEvents.webPage'), isset($sponsor) ? $sponsor->web_page : null, ['class' => 'form-control', 'id' => 'web_page' ,  'required']) !!}
    @if(ONE::actionType('sponsor') == 'show')
        <div class="box-body">
            <div class="col-lg-3">
                <img class="img" src="{{action('FilesController@download', ['id'=>$file['id'],'code'=>$file['code']] )}}"  id="image_sponsor">
            </div>
        </div>
    @endif
    @if(ONE::actionType('sponsor') != 'show')

        <div id="editImage">
            <p>{!! ONE::fileUploadBox("banner-drop-zone", trans('conferenceEvents.drop-zone'), trans('conferenceEvents.banners'), 'select-banner', 'banner-list', 'files_banner') !!}</p>
        </div>
        {!! ONE::imageCropModal('getCroppedCanvasModal', 'getCroppedCanvasTitle', trans('conferenceEvents.resize')) !!}
    @endif



    {!! $form->make() !!}

@endsection
@section('scripts')
    <!-- Plupload Javascript fix and bootstrap fix @ start -->
    <link rel="stylesheet" href="/bootstrap/plupload-fix/bootstrap.css">
    <script src="/bootstrap/plupload-fix/bootstrap.min.js"></script>
    <!-- Plupload Javascript fix and bootstrap fix @ End -->
    <script src="{{ asset('vendor/jildertmiedema/laravel-plupload/js/plupload.full.min.js') }}"></script>
    <script src="{{ asset("js/cropper.min.js") }}"></script>
    @include('private._private.functions') {{-- Helper Functions --}}
    <script>

        {!! ONE::imageUploader('bannerUploader', action('FilesController@upload'), 'imageSponsorUploaded', 'select-banner', 'banner-drop-zone', 'banner-list', 'files_banner', 'getCroppedCanvasModal', 0, 0, isset($uploadKey) ? $uploadKey : "") !!}
        bannerUploader.init();

        updateClickListener();
        updateFileSponsorList('#files_banner');


    </script>
@endsection



