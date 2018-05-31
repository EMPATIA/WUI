@extends('private._private.index')

@section('header_styles')
    <style>
        .disabled {
            background-color: lightgrey;
        }
    </style>
@endsection

@section('content')
    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title"><i class="fa"></i> {{ trans('operationSchedules.list') }}</h3>
        </div>
        <div class="box-body">
            <div class="table-responsive">
                <table id="operation_schedules_list" class="table dataTable no-footer table-responsive">
                    <thead>
                    <tr>
                        <th>{{ trans('privateOperationSchedules.action_name') }}</th>
                        <th>{{ trans('privateOperationSchedules.type_name') }}</th>
                        <th>{{ trans('privateOperationSchedules.start_date') }}</th>
                        <th>{{ trans('privateOperationSchedules.end_date') }}</th>
                        <th>{{ trans('privateOperationSchedules.active') }}</th>
                        <th></th>
                        <th> {!! ONE::actionButtons(['type' => $type, 'cbKey' => $cbKey], ['create' => 'OperationSchedulesController@create']) !!}</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
@endsection


@section('scripts')
    <script>
        var table = '';

        $(function () {
            table = $('#operation_schedules_list').DataTable({
                language: {
                    url: '{!! asset('/datatableLang/'.Session::get('LANG_CODE').'.json') !!}',
                    search: '<a class="btn searchBtn" id="searchBtn"><i class="fa fa-search"></i></a>'
                },
                responsive: true,
                processing: true,
                serverSide: true,
                ajax: '{!! action('OperationSchedulesController@getIndexTable',[$type,$cbKey]) !!}',
                columns: [
                    { data: 'action_name', name: 'action_name', searchable: true },
                    { data: 'type_name', name: 'type_name', searchable: true },
                    { data: 'start_date', name: 'start_date', searchable: true },
                    { data: 'end_date', name: 'end_date', searchable: true },
                    { data: 'active', name: 'active', searchable: true },
                    { data: 'update_status', name: 'update_status', searchable: false, orderable: false },
                    { data: 'action', name: 'action', searchable: false, orderable: false, width: "60px" },
                ],
                order: [[ 2, 'asc' ]]
            });
        });

        function deactivate(cbOperationScheduleKey){
            $.ajax({
                method: 'post',
                url: "{{action('OperationSchedulesController@changeStatus')}}",
                data: {
                    cbOperationScheduleKey: cbOperationScheduleKey,
                    status: 0
                },
                success: function (response) {
                    if (response === 'OK') {
                        table.ajax.reload();
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                }
            });
        }

        function activate(cbOperationScheduleKey) {
            $.ajax({
                method: 'post',
                url: "{{action('OperationSchedulesController@changeStatus')}}",
                data: {
                    cbOperationScheduleKey: cbOperationScheduleKey,
                    status: 1
                },
                success: function (response) {
                    if (response === 'OK') {
                        table.ajax.reload();
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                }
            });
        }

    </script>
@endsection
