@extends('private._private.index')

@section('content')
    @php
    $form = ONE::form('entitySites', trans('privateEntitySites.details'), 'orchestrator', 'entity_site')
            ->settings(["model" => isset($site) ? $site : null, 'id' => isset($site) ? $site->key : null])
            ->show('EntitiesSitesController@edit', 'EntitiesSitesController@deleteConfirm', ['id' => isset($site) ? $site->key : null],
                    'EntitiesSitesController@index', ['id' => isset($site) ? $site->key : null])
            ->create('EntitiesSitesController@store', 'EntitiesSitesController@index', ['entityKey' => isset($entityKey) ? $entityKey : null])
            ->edit('EntitiesSitesController@update', 'EntitiesSitesController@show', ['siteKey' => isset($site) ? $site->key : null])
            ->open();

    @endphp

    {!! Form::hidden('entity_key',isset($entityKey)? $entityKey:'') !!}
    {!! Form::hidden('site_key',isset($site) ? $site->key : '') !!}

    {!! Form::oneText('name', trans('privateEntitiesDivided.siteName'), isset($site) ? $site->name  : null, ['class' => 'form-control', 'id' => 'name','required']) !!}
    {!! Form::oneTextArea('description', trans('privateEntitiesDivided.description'), isset($site) ? $site->description : null, ['class' => 'form-control', 'id' => 'contents', 'size' => '30x2', 'style' => 'resize: vertical']) !!}
    {!! Form::oneSelect('layout_key', trans('privateEntitiesDivided.template'), isset($layouts) ? $layouts : null, isset($site->layout->layout_key) ? $site->layout->layout_key : null, isset($site->layout->name) ? $site->layout->name : null, ['class' => 'form-control', 'id' => 'layout_key', '']) !!}
    {!! Form::oneText('link', trans('privateEntitiesDivided.siteLink'), isset($site) ? $site->link  : null, ['class' => 'form-control', 'id' => 'link','required']) !!}
    {!! Form::oneText('no_reply_email', trans('privateEntitiesDivided.no_reply_email'), isset($site) ? $site->no_reply_email  : null, ['class' => 'form-control', 'id' => 'no_reply_email','required']) !!}
    {!! Form::oneCheckbox('active', trans('privateEntitiesDivided.siteActive'), 1, isset($site) ? $site->active : 1, ['id' => 'active']) !!}
    {!! Form::oneDate('start_date', trans('privateEntitiesDivided.start_date'), isset($site) ? $site->start_date : null, ['class' => 'form-control oneDatePicker', 'id' => 'start_date']) !!}
    {!! Form::oneDate('end_date', trans('privateEntitiesDivided.end_date'), isset($site) ? (!empty($site->end_date)? $site->end_date: '') : '', ['class' => 'form-control oneDatePicker', 'id' => 'end_date']) !!}


    @if(ONE::actionType('entitySites') == 'show')


        <!-- Select Home page type -->
        <div class="modal fade" tabindex="-1" role="dialog" id="homePageTypeModal" >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="card-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">{{trans("privateEntitiesSite.selectHomePageType")}}</h4>
                    </div>
                    <div class="modal-body">
                        <div class="card flat">
                            <div class="card-header">{{trans('privateEntitiesSite.selectHomePageType')}}</div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="homePageTypeSelected">{{trans('privateEntitiesSite.homePageType')}}</label>
                                    <select id="homePageTypeSelected" class="form-control" name="homePageTypeSelected">
                                        <option selected="selected" value="">{{trans('privateQuestionOption.selectValue')}}</option>
                                        @foreach($homePageTypes as $key => $type)
                                            <option value="{{$key}}">{{$type}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{trans("privateEntitiesSite.close")}}</button>
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <button type="button" class="btn btn-primary" id="updateStatus" onclick="verifyHomePageType()">{{trans("privateEntitiesSite.create")}}</button>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->
    @endif

    @if(ONE::actionType('entitySites') == "show")
        <table id="site_additional_urls" class="table table-striped dataTable no-footer table-responsive">
            <thead>
            <tr>
                <th width="90%">{{ trans('privateEntities.siteAdditionalLinks') }}</th>
                <th width="10%">
                    @if(Session::get('user_role') == 'admin')
                        {!! ONE::actionButtons(['site_key' => $site->key], ['create' => 'SiteAdditionalUrlsController@create']) !!}
                    @endif
                </th>
            </tr>
            </thead>
        </table>
    @endif
    {!! $form->make() !!}


@endsection

@section('scripts')

    <script>
        @if(ONE::actionType('entitySites') == "show")
            $(function () {
            $('#site_additional_urls').DataTable({
                language: {
                    url: '{!! asset('/datatableLang/'.Session::get('LANG_CODE').'.json') !!}',
                    search: '<a class="btn searchBtn" id="searchBtn"><i class="fa fa-search"></i></a>'
                },
                processing: true,
                serverSide: true,
                ajax: '{!! action("EntitiesSitesController@getSiteAdditionalUrlsTable",$site->key) !!}',
                columns: [
                    { data: 'link', name: 'link'},
                    { data: 'action', name: 'action', searchable: false, orderable: false },
                ],
                order: [['0', 'asc']]
            });
        });
        @endif
    </script>


    <script>

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
