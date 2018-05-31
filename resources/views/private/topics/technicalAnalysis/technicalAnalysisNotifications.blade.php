@extends('private._private.index')

@section('header_styles')
    <style>
    .list-wrapper-technical-analysis{
        max-height:500px;
        min-height:300px;
        width: 100%;
        overflow-y:auto;
        overflow-x: hidden;
    }
    </style>
@endsection

@section('content')

    {{--{{dd(get_defined_vars())}}--}}

    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title">{{ trans('privateCbs.technicalAnalysisNotifications') }}</h3>
        </div>
        <br>
        <div class="box-body">
            <div class="row">
                <div class="col-12 col-lg-6">
                    <div class="dataTables_wrapper dt-bootstrap no-footer list-wrapper-technical-analysis">
                        <table id="groups_list" class="table table-hover table-striped table-responsive">
                            <thead>
                            <tr>
                                <th><input type="checkbox" class="checkAllGroups" id="checkAllGroups"/></th>
                                <th>{{ trans('privateCbs.groupName') }}</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
                <div class="col-12 col-lg-6" >
                    <div class="dataTables_wrapper dt-bootstrap no-footer list-wrapper-technical-analysis">
                        <table id="managers_list" class="table table-hover table-striped table-responsive">
                            <thead>
                            <tr>
                                <th><input type="checkbox" class="checkAllManagers" id="checkAllManagers"/></th>
                                <th>{{ trans('privateCbs.managerName') }}</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12" >
            <div class="pull-right">
                <a href="#" class="btn btn-flat btn-success" style="margin-right: 5px" id="list" role="button">{{trans('privateCbs.send')}}</a>
                <a href="{{action("TechnicalAnalysisController@show", ['type' => $type, 'cbKey' => $cbKey, 'topicKey' => $topicKey])}}" class="btn btn-flat btn-secondary" role="button">{{trans('privateCbs.not_send')}}</a>
            </div>
        </div>
    </div>

    <form id="keysList" action="{{action("TechnicalAnalysisController@sendNotification", ['type' => $type, 'cbKey' => $cbKey, 'topicKey' => $topicKey, 'technicalAnalysisKey' => $technicalAnalysisKey])}}" method="POST">
        <input type="hidden" name="_token" value="@php echo  csrf_token(); @endphp"/>
        <input type="hidden" id="groupsKeys" name="groups"/>
        <input type="hidden" id="managersKeys" name="managers"/>
    </form>

@endsection

@section('scripts')
    <script>
        $('#groups_list').DataTable({
            language: {
                url: '{!! asset('/datatableLang/'.Session::get('LANG_CODE').'.json') !!}'
            },
            responsive: true,
            processing: true,
            serverSide: true,
            bPaginate: false,
            searching: false,
            ajax: '{!!  URL::action('TechnicalAnalysisController@entityGroupsTable') !!}',
            columns: [
                { data: 'select_groups', name: 'select_groups', searchable: false, orderable: false, width: "5px"},
                { data: 'name', name: 'name', orderable: true, searchable: false, width: "100%"},
            ],
            order: [['1', 'desc']]
        });

        $('#managers_list').DataTable({
            language: {
                url: '{!! asset('/datatableLang/'.Session::get('LANG_CODE').'.json') !!}'
            },
            responsive: true,
            processing: true,
            serverSide: true,
            bPaginate: false,
            searching: false,
            ajax: '{!!  URL::action('TechnicalAnalysisController@entityManagersTable') !!}',
            columns: [
                { data: 'select_managers', name: 'select_managers', searchable: false, orderable: false, width: "5px"},
                { data: 'name', name: 'name', orderable: true, searchable: false, width: "100%"},
            ],
            order: [['1', 'desc']]
        });

        $('#checkAllGroups').click(function () {
            $('.group_key:checkbox').prop('checked', this.checked);
        });

        $('#checkAllManagers').click(function () {
            $('.user_key:checkbox').prop('checked', this.checked);
        });

        $("#list").click(function() {
            var groupsKey = [];
            $('.group_key:checked').each( function(i, obj){
                groupsKey.push($(obj).val());
            });

            $('#groupsKeys').val(groupsKey);

            var managersKey = [];
            $('.user_key:checked').each( function(i, obj){
                managersKey.push($(obj).val());
            });

            $('#managersKeys').val(managersKey);
            $('#keysList').submit();
        });
    </script>
@endsection