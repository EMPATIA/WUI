@extends('private._private.index')

@section('content')
    @php
    $form = ONE::form('entities', trans('privateSites.details'))
            ->settings(["model" => isset($site) ? $site : null, 'id' => isset($site) ? $site->key : null])
            ->show('EntitiesController@editEntitySite', 'EntitiesController@deleteSiteConfirm', ['entityKey' => isset($entityKey) ? $entityKey : null, 'id' => isset($site) ? $site->key : null],
                    null, ['entityKey' => isset($entityKey) ? $entityKey : null, 'id' => isset($site) ? $site->key : null])
            ->create('EntitiesController@storeEntitySite', 'EntitiesController@show', ['entityKey' => isset($entityKey) ? $entityKey : null])
            ->edit('EntitiesController@updateEntitySite', 'EntitiesController@showHomePageConfigurations', ['entityKey' => isset($entityKey) ? $entityKey : null, 'siteKey' => isset($site) ? $site->key : null])
            ->open();
    @endphp

    {!! Form::hidden('entity_key',isset($entityKey)? $entityKey:'') !!}
    {!! Form::hidden('site_key',isset($site) ? $site->key : '') !!}

    @if(ONE::actionType('entities') == 'show')
        <div class="card">
            <div class="box-header">
                <h3 class="box-title"><i class="fa"></i> {{ trans('privateSites.home_page_configuration_groups') }}</h3>
            </div>
            <div class="box-body">
                <table id="homePageConfigurations_list" class="table table-hover table-striped dataTable no-footer table-responsive">
                    <thead>
                    <tr>
                        <th>{{ trans('privateSites.home_page_configuration_group_key') }}</th>
                        <th>{{ trans('privateSites.home_page_configuration_group_name') }}</th>
                        <th>
                            <a href="" class="btn btn-flat btn-success btn-sm" title="Create" data-toggle="modal" data-target="#homePageTypeModal">
                                <i class="fa fa-plus"></i>
                            </a>
                        </th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
        <!-- Select Home page type -->
        <div class="modal fade" tabindex="-1" role="dialog" id="homePageTypeModal" >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="card-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">{{trans("privateEntities.select_home_page_type")}}</h4>
                    </div>
                    <div class="modal-body">
                        <div class="card flat">
                            <div class="card-header">{{trans('privateSites.select_home_page_type')}}</div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="homePageTypeSelected">{{trans('privateSites.home_page_type')}}</label>
                                    <select id="homePageTypeSelected" class="form-control" name="homePageTypeSelected">
                                        <option selected="selected" value="">{{trans('privateSites.home_page_type_select')}}</option>
                                        @foreach($homePageTypes as $key => $type)
                                            <option value="{{$key}}">{{$type}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{trans("privateEntities.close_modal")}}</button>
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <button type="button" class="btn btn-primary" id="updateStatus" onclick="verifyHomePageType()">{{trans("privateEntities.create_modal")}}</button>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->
    @endif

    {!! $form->make() !!}

@endsection

@section('scripts')
    <script>
        $(function() {
            var array = ["{{ $entityKey }}", "{{$site->key}}"]
            getSidebar('{{ action("OneController@getSidebar") }}', 'homePageConfigurations', array, 'sidebar_admin.sites' )
        })

        $(function () {
            $('#homePageConfigurations_list').DataTable({
                language: {
                    url: '{!! asset('/datatableLang/'.Session::get('LANG_CODE').'.json') !!}',
                    search: '<a class="btn searchBtn" id="searchBtn"><i class="fa fa-search"></i></a>'
                },
                processing: true,
                serverSide: true,
                ajax: '{!! action('HomePageConfigurationsController@getIndexTable', ['siteKey' => isset($site) ? $site->key : null, "entityKey" => isset($entityKey) ? $entityKey : null]) !!}',
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
