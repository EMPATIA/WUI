@extends('private._private.index')

@section('content')
    @php
    $form = ONE::form('entities', trans('privateSites.details'))
            ->settings(["model" => isset($site) ? $site : null, 'id' => isset($site) ? $site->key : null])
            ->show('EntitiesController@editEntitySite', 'EntitiesController@deleteSiteConfirm', ['entityKey' => isset($entityKey) ? $entityKey : null, 'id' => isset($site) ? $site->key : null],
                    'EntitiesController@showSites', ['entityKey' => isset($entityKey) ? $entityKey : null])
            ->create('EntitiesController@storeEntitySite', 'EntitiesController@showSites', ['entityKey' => isset($entityKey) ? $entityKey : null])
            ->edit('EntitiesController@updateEntitySite', 'EntitiesController@showEntitySite', ['entityKey' => isset($entityKey) ? $entityKey : null, 'siteKey' => isset($site) ? $site->key : null])
            ->open();
    @endphp

    {!! Form::hidden('entity_key',isset($entityKey)? $entityKey:'') !!}
    {!! Form::hidden('site_key',isset($site) ? $site->key : '') !!}

    {!! Form::oneText('name', trans('privateSites.site_name'), isset($site) ? $site->name  : null, ['class' => 'form-control', 'id' => 'name','required']) !!}
    {!! Form::oneTextArea('description', trans('privateSites.description'), isset($site) ? $site->description : null, ['class' => 'form-control', 'id' => 'contents', 'size' => '30x2', 'style' => 'resize: vertical']) !!}
    {!! Form::oneSelect('layout_key', trans('privateSites.layout'), isset($layouts) ? $layouts : null, isset($site->layout->layout_key) ? $site->layout->layout_key : null, isset($site->layout->name) ? $site->layout->name : null, ['class' => 'form-control', 'id' => 'layout_key', '']) !!}
    {!! Form::oneText('link', trans('privateSites.site_link'), isset($site) ? $site->link  : null, ['class' => 'form-control', 'id' => 'link','required']) !!}
    {!! Form::oneText('no_reply_email', trans('privateSites.no_reply_email'), isset($site) ? $site->no_reply_email  : null, ['class' => 'form-control', 'id' => 'no_reply_email','required']) !!}
    {!! Form::oneCheckbox('partial_link', trans('privateSites.partial_link'), 1, isset($site) ? $site->partial_link : 0, ['id' => 'partial_link']) !!}
    {!! Form::oneCheckbox('active', trans('privateSites.site_active'), 1, isset($site) ? $site->active : 1, ['id' => 'active']) !!}
    {!! Form::oneDate('start_date', trans('privateSites.start_date'), isset($site) ? $site->start_date : null, ['class' => 'form-control oneDatePicker', 'id' => 'start_date']) !!}
    {!! Form::oneDate('end_date', trans('privateSites.end_date'), isset($site) ? (!empty($site->end_date)? $site->end_date: '') : '', ['class' => 'form-control oneDatePicker', 'id' => 'end_date']) !!}

    {!! $form->make() !!}

@endsection

@section('scripts')
    <script>
        function verifyHomePageType(){
            if($('#homePageTypeSelected').val() == ''){
                $('#homePageTypeSelected').closest('.form-group').addClass('has-error');
            }
            else{
                var key = $('#homePageTypeSelected').val();
                $.ajax({
                    method: 'POST', // Type of response and matches what we said in the route
                    url: '{{action("HomePageConfigurationsController@getUrlWithHomePageTypeKey")}}', // This is the url we gave in the route
                    data: {siteKey: '{{isset($site) ? $site->key : null}}',homePageTypeKey: key, _token: "{{ csrf_token() }}"}, // a JSON object to send back
                    success: function (response) { // What to do if we succeed
                        if(response != 'false'){
                            window.location.href = response;
                        }
                        else{
                            $('#homePageTypeModal').modal().hide();
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown) { // What to do if we fail
                        console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                    }
                });
            }
        }
        $('#homePageTypeModal').on('hidden.bs.modal', function () {
            $('#homePageTypeSelected').closest('.form-group').removeClass('has-error');
        });
    </script>

    <script src="{{ asset("js/tinymce/tinymce.min.js") }}"></script>
    <script>
        $( document ).ready(function() {
            {!! ONE::addTinyMCE(".use_term", ['action' => action('ContentsController@getTinyMCE')]) !!}
        });
    </script>
    <!-- Plupload Javascript fix and bootstrap fix @ start -->
    <link rel="stylesheet" href="/bootstrap/plupload-fix/bootstrap.css">
    <script src="/bootstrap/plupload-fix/bootstrap.min.js"></script>
    <!-- Plupload Javascript fix and bootstrap fix @ End -->
    <script src="{{ asset('vendor/jildertmiedema/laravel-plupload/js/plupload.full.min.js') }}"></script>
    <script src="{{ asset("js/cropper.min.js") }}"></script>
    <script src="{{ asset("js/canvas-to-blob.js") }}"></script>

    @include('private._private.functions') {{-- Helper Functions --}}
    <script>

        {!! ONE::fileUploader('fileUploader',  action('FilesController@upload') , 'contentFileUploaded', 'select-files', 'drop-zone', 'files-list', 'files', 1, isset($uploadKey) ? $uploadKey : "") !!}
        fileUploader.init();

        {!! ONE::imageUploader('bannerUploader',  action('FilesController@upload') , 'contentFileUploaded', 'select-banner', 'banner-drop-zone', 'banner-list', 'files_banner', 'getCroppedCanvasModal', -1, 2, isset($uploadKey) ? $uploadKey : "") !!}
        bannerUploader.init();

        {!! ONE::imageUploader('slideUploader',  action('FilesController@upload') , 'contentFileUploaded', 'select-slide', 'slide-drop-zone', 'slide-list', 'files_slide', 'getCroppedCanvasModal', -1, 3, isset($uploadKey) ? $uploadKey : "") !!}
        slideUploader.init();

        {!! ONE::imageUploader('imageUploader', action('FilesController@upload'), 'contentFileUploaded', 'select-image', 'image-drop-zone', 'image-list', 'files_image', 'getCroppedCanvasModal', 0, 4, isset($uploadKey) ? $uploadKey : "") !!}
        imageUploader.init();

        updateClickListener();

        updateContentList('#files', 1);
        updateContentList('#files_banner', 2);
        updateContentList('#files_slide', 3);
        updateContentList('#files_image', 4);
    </script>
@endsection
