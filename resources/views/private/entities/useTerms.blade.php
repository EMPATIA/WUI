@extends('private._private.index')

@section('content')
    @php
    $form = ONE::form('entities', trans('privateSites.details'))
            ->settings(["model" => isset($site) ? $site : null, 'id' => isset($site) ? $site->key : null])
            ->show( null, null, ['entityKey' => isset($entityKey) ? $entityKey : null, 'id' => isset($site) ? $site->key : null],
                    null, ['entityKey' => isset($entityKey) ? $entityKey : null, 'id' => isset($site) ? $site->key : null])
            ->create('EntitiesController@storeEntitySite', 'EntitiesController@show', ['entityKey' => isset($entityKey) ? $entityKey : null])
            ->edit('EntitiesController@updateEntitySite', 'EntitiesController@showUseTerms', ['entityKey' => isset($entityKey) ? $entityKey : null, 'siteKey' => isset($site) ? $site->key : null])
            ->open();
    @endphp

    {!! Form::hidden('entity_key',isset($entityKey)? $entityKey:'') !!}
    {!! Form::hidden('site_key',isset($site) ? $site->key : '') !!}


    <div class="row">
        <div class="col-12">
            @if(count($languages) > 0)
                @foreach($languages as $language)
                    @php $form->openTabs('tab-translation-' . $language->code, $language->name); @endphp
                    <div style="padding:10px;">
                        @if(ONE::actionType('entities') == 'show')
                            <dt><i class="fa fa-eye"></i> {{ trans('privateSites.preview') }}</dt>
                            <div style="border:1px solid #999999;width:100%;height:350px;overflow-y: scroll">
                                {{html_entity_decode(!empty($site->use_terms->{$language->code}->content) ? $site->use_terms->{$language->code}->content : null)}}
                            </div>
                            <hr style="margin: 10px 0 10px 0">
                        @else
                            {!! Form::oneTextArea($language->default == true ? 'required_content_'.$language->code : 'content_'.$language->code,
                                                  trans('privateSites.use_terms'),
                                                  !empty($site->use_terms->{$language->code}->content) ? $site->use_terms->{$language->code}->content : null,
                                                  ['class' => 'form-control use_term', 'id' => 'content_'.$language->code]) !!}
                        @endif
                    </div>
                @endforeach
                @php $form->makeTabs(); @endphp
            @endif
        </div>
    </div>



    {!! $form->make() !!}

@endsection

@section('scripts')
    <script>
        $(function() {
            var array = ["{{ $entityKey }}", "{{$site->key}}"]
            getSidebar('{{ action("OneController@getSidebar") }}', 'useTerms', array, 'sidebar_admin.sites' )
        })

        $(function () {
            $('#homePageConfigurations_list').DataTable({
                language: {
                    url: '{!! asset('/datatableLang/'.Session::get('LANG_CODE').'.json') !!}',
                    search: '<a class="btn searchBtn" id="searchBtn"><i class="fa fa-search"></i></a>'
                },
                processing: true,
                serverSide: true,
                ajax: '{!! action('HomePageConfigurationsController@getIndexTable', isset($site) ? $site->key : null) !!}',
                columns: [
                    {data: 'group_key', name: 'group_key', width: "20px"},
                    {data: 'group_name', name: 'group_name'},
                    {data: 'action', name: 'action', searchable: false, orderable: false, width: "30px"}
                ],
                order: [['1', 'asc']]
            });
        });
    </script>
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
            {!! ONE::addTinyMCE(".use_term", ['action' => action('ContentManagerController@getTinyMCE')]) !!}
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
