@extends('private._private.index')
<!--suppress JSUnresolvedVariable -->
<!-- Plupload Javascript fix and bootstrap fix @ start -->
<link rel="stylesheet" href="/bootstrap/plupload-fix/bootstrap.css">
<script src="/bootstrap/plupload-fix/bootstrap.min.js"></script>
<!-- Plupload Javascript fix and bootstrap fix @ End -->
<script src="{{ asset('vendor/jildertmiedema/laravel-plupload/js/plupload.full.min.js') }}"></script>
@section('content')

    <div class="row">
        <div class="col-md-12">
            @if($type == 'vat_numbers')
                <div class="box box-primary">
                    <div class="box-header">
                        <h3 class="box-title"><i class="fa fa-unlock"></i> {{ trans('manageEntityRegistrationValues.title') }}
                        </h3>
                    </div>
                    <div class="box-body">
                        {!! ONE::fileUploadBox("importer-drop-zone", trans('manageEntityRegistrationValues.drop_zone'), trans('manageEntityRegistrationValues.import_file'), 'select-importer','importer-list', "importer-files") !!}
                        <div id="result"></div>
                    </div>
                </div>
            @endif

            <div class="box box-primary">
                <div class="box-header">
                    <h3 class="box-title"><i class="fa fa-unlock"></i> {{ trans('manageEntityRegistrationValues.manual_add') }}
                    </h3>
                </div>
                <div class="box-body">
                    @if($type == 'vat_numbers')
                        <label>{{ trans('manageEntityRegistrationValues.vat_number') }}</label>
                        <input type="text" id="vat-number" class="form-control" placeholder="{{ trans('manageEntityRegistrationValues.insert_vat_number') }}">
                    @else
                        <label>{{ trans('manageEntityRegistrationValues.domain_title') }}</label>
                        <input type="text" id="domain-title"  class="form-control" placeholder="{{ trans('manageEntityRegistrationValues.insert_domain_title') }}">
                        <label>{{ trans('manageEntityRegistrationValues.domain_name') }}</label>
                        <input type="text" id="domain-name" class="form-control" placeholder="{{ trans('manageEntityRegistrationValues.insert_domain_name') }}">
                    @endif
                    <div id="loader-div">
                        <a class="btn btn-flat btn-create btn-sm add_{{$type}}" title="Create" style="margin-top:15px">
                            <i class="fa fa-plus"></i> {{ trans('manageEntityRegistrationValues.addVatNumber') }}
                        </a>
                    </div>
                </div>
            </div>


            <div class="box box-primary">
                <div class="box-body">
                    <table id="registration_values_list" class="table table-striped dataTable no-footer table-responsive">
                        <thead>
                        @if($type == 'vat_numbers')
                            <tr>
                                <th width="90%">{{ trans('manageEntityRegistrationValues.registered_values') }}</th>
                            </tr>
                        @else
                            <tr>
                                <th width="45%">{{ trans('manageEntityRegistrationValues.domain_title') }}</th>
                                <th width="45%">{{ trans('manageEntityRegistrationValues.domain_name') }}</th>
                            </tr>
                        @endif
                        </thead>
                    </table>
                </div>
            </div>

        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(function () {

            var table = $('#registration_values_list').DataTable({
                language: {
                    url: '{!! asset('/datatableLang/'.Session::get('LANG_CODE').'.json') !!}',
                    search: '<a class="btn searchBtn" id="searchBtn"><i class="fa fa-search"></i></a>'
                },
                processing: true,
                serverSide: true,
                ajax: '{!! action("EntitiesDividedController@getEntityRegistrationValues", ['entity_key' => $entity->entity_key, 'type' => $type]) !!}',
                columns: [
                    @if($type == 'vat_numbers')
                        {data: 'vat_number', name: 'vat_number'},
                    @else
                        {data: 'domain_title', name: 'domain_title'},
                        {data: 'domain_name', name: 'domain_name'},
                    @endif
                    {data: 'action', name: 'action', searchable: false, orderable: false},
                ],
            });

            {!! ONE::fileUploader('bannerFileUploader', action('EntitiesDividedController@uploadEntityRegistrationValues',["entityKey"=>$entity->entity_key, "type"=> $type]) ,null,'select-importer',  "importer-drop-zone", 'banned-list', "banner", 1, isset($uploadKey) ? $uploadKey : "",null) !!}
            bannerFileUploader.init();
            bannerFileUploader.bind('FileUploaded', function (up, file, res) {
                var result;
                if (res.status != 200)
                    toastr.error('{{ trans('error') }}');
                else {
                    result = JSON.parse(res.response);
                    $('#result').html('<div class="alert alert-success fade in alert-dismissable">' +
                        '<a href="#" class="close" style="color:#fff!important;" ' +
                        'data-dismiss="alert" aria-label="close" title="close">' +
                        '<i class="fa fa-times" aria-hidden="true"></i></a><strong>{{ trans('privateAuthMethods.importer_success') }} </strong>' +
                        result["result"].message + '</div>')
                    table.ajax.reload();
                }
            });

            $(document).on('click','.add_vat_numbers', function(){
                $.ajax({
                    method: 'POST', // Type of response and matches what we said in the route
                    url: "{{action('EntitiesDividedController@addSingleRegistrationValue',['entity_key' => $entity->entity_key, 'type' => $type])}}", // This is the url we gave in the route
                    data: {
                        "_token": "{{ csrf_token() }}",
                        'vat_number': $("#vat-number").val(),
                    }, beforeSend: function () {
                        $("#loader-div").append('<div class="loader pull-right" style="margin-top:15px"><img src="{{ asset('images/preloader.gif') }}" alt="Loading"  style="width: 20px;"/></div>');
                        $(".loader").show();
                    }, success: function () {
                        $("#vat-number").val('');
                        table.ajax.reload();
                        $("#loader-div").find('.loader').remove();
                    }
                });
            });

            $(document).on('click','.add_domain_names', function(){
                $.ajax({
                    method: 'POST', // Type of response and matches what we said in the route
                    url: "{{action('EntitiesDividedController@addSingleRegistrationValue',['entity_key' => $entity->entity_key, 'type' => $type])}}", // This is the url we gave in the route
                    data: {
                        "_token": "{{ csrf_token() }}",
                        'domain_name': $("#domain-name").val(),
                        'domain_title': $("#domain-title").val(),
                    }, beforeSend: function () {
                        $("#loader-div").append('<div class="loader pull-right" style="margin-top:15px"><img src="{{ asset('images/preloader.gif') }}" alt="Loading"  style="width: 20px;"/></div>');
                        $(".loader").show();
                    }, success: function () {
                        $("#domain-name").val('');
                        $("#domain-title").val('');
                        table.ajax.reload();
                        $("#loader-div").find('.loader').remove();

                    }
                });
            });
        });


    </script>
@endsection