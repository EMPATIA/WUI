@extends('private._private.index')

@section('content')

    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title"><i class="fa"></i> {{ trans('homePageConfiguration.homePageConfigurationGroups') }}</h3>
        </div>
        <div class="box-body">
            <table id="homePageConfigurations_list" class="table table-hover table-striped dataTable no-footer table-responsive">
                <thead>
                <tr>
                    <th>{{ trans('privateEntitiesSite.homePageConfigurationGroupKey') }}</th>
                    <th>{{ trans('privateEntitiesSite.homePageConfigurationGroupName') }}</th>
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
@endsection
@section('scripts')
    <script>
        $(function () {
            $('#homePageConfigurations_list').DataTable({
                language: {
                    url: '{!! asset('/datatableLang/'.Session::get('LANG_CODE').'.json') !!}',
                    search: '<a class="btn searchBtn" id="searchBtn"><i class="fa fa-search"></i></a>'
                },
                processing: true,
                serverSide: true,
                ajax: '{!! action('HomePageConfigurationsController@getIndexTable', isset($siteKey) ? $siteKey : null) !!}',
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
@endsection