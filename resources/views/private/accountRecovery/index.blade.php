@extends('private._private.index')

@section('content')
    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title"><i class="fa"></i> {{ trans('privateAccountRecovery.list_title') }}</h3>
        </div>

        <div class="box-body">
            <table id="accountRecovery_list" class="table table-hover table-striped table-responsive">
                <thead>
                <tr>
                     <th>{{ trans('privateAccountRecovery.key') }}</th>
                    <th>{{ trans('privateAccountRecovery.parameter_user_type') }}</th>
                    <th>{{ trans('privateAccountRecovery.created_at') }}</th>
                    <th>{{ trans('privateAccountRecovery.send_token') }}</th>
                    <th>{!! ONE::actionButtons(null, ['create' => 'AccountRecoveryController@create']) !!}</th>
                </tr>
                </thead>
            </table>
        </div>
    </div>

@endsection


@section('scripts')
    <script>
        
        $(function () {
            $('#accountRecovery_list').DataTable({
                language: {
                    url: '{!! asset('/datatableLang/'.Session::get('LANG_CODE').'.json') !!}'
                },
                processing: true,
                serverSide: true,
                ajax: '{!! action('AccountRecoveryController@getIndexTable') !!}',
                columns: [
                    { data: 'key', name: 'key', width: "50px" },
                    { data: 'parameter_user_type', name: 'parameter_user_type' },
                    { data: 'created_at', name: 'created_at' },
                    { data: 'send_token', name: 'send_token', width: "20px"},
                    { data: 'action', name: 'action', searchable: false, orderable: false, width: "30px" }
                ],
                order: [['1', 'asc']]
            });

        });

    </script>
@endsection



