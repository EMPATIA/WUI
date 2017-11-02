@extends('private._private.index')
@section('header_styles')
    <link href="{{ asset("css/cropper.min.css") }}" rel='stylesheet' type='text/css'>
@endsection
@section('content')

    @php $form = ONE::form('questIcon')
            ->settings(["model" => isset($icon) ? $icon : null,'id'=>isset($icon) ? $icon->icon_key : null])
            ->show('QuestIconsController@edit', 'QuestIconsController@delete', ['key' => isset($icon) ? $icon->icon_key : null], 'QuestIconsController@index')
            ->create('QuestIconsController@store', 'QuestIconsController@index')
            ->edit('QuestIconsController@update', 'QuestIconsController@show', ['key' => isset($icon) ? $icon->icon_key : null])
            ->open();
    @endphp

    {!! Form::oneText('name', trans('privateQuestIcons.name'), isset($icon) ? $icon->name : null, ['class' => 'form-control', 'id' => 'name']) !!}
    {!! Form::hidden('file_id', isset($icon) ? $icon->file_id : "", ['id' => 'file_id']) !!}
    {!! Form::hidden('file_code', isset($icon) ? $icon->file_code : "", ['id' => 'file_code']) !!}

    @if(ONE::actionType('questIcon') == 'show')
        <div class="box-body">
            <div class="col-lg-3">
                <img class="img" src="{{action('FilesController@download', ['id'=>$icon->file_id,'code'=>$icon->file_code,1] )}}" >
            </div>
        </div>

    @endif
    @if(ONE::actionType('questIcon') != 'show')
        <div id="editImage">
            <p>{!! ONE::fileUploadBox("banner-drop-zone", trans('privateQuestIcons.drop_zone'), trans('privateQuestIcons.banners'), 'select-banner', 'banner-list', 'files_banner') !!}</p>
        </div>
        {!! ONE::imageCropModal('getCroppedCanvasModal', 'getCroppedCanvasTitle', trans('privateQuestIcons.resize')) !!}
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

        {!! ONE::imageUploader('bannerUploader', action('FilesController@upload'), 'questIconUploaded', 'select-banner', 'banner-drop-zone', 'banner-list', 'files_banner', 'getCroppedCanvasModal', 0, 0, isset($uploadKey) ? $uploadKey : "") !!}
        bannerUploader.init();

        updateClickListener();


    </script>
@endsection