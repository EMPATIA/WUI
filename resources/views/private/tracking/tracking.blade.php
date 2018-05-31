@extends('private._private.index')

@section('content')
    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title"><i class="fa"></i> {{ trans('auditing.list') }}</h3>
        </div>

        <div class="box-body">
            <table class="table table-hover table-striped dataTable no-footer table-responsive" id="tracking-table">
                <thead>
                <tr>
                    <th> {{ trans('auditing.id') }} </th>
                    <th>{{ trans('auditing.user') }} </th>
                    <th>{{ trans('auditing.ip') }} </th>
                    <th>{{ trans('auditing.url') }} </th>
                    <th>{{ trans('auditing.action') }} </th>
                    <th>{{ trans('auditing.time') }} </th>
                </tr>
                </thead>

            </table>
        </div>
    </div>
@endsection

@section('scripts')

    <script>
        $(function() {
            $('#tracking-table').DataTable({
                language: {
                    url: '{!! asset('/datatableLang/'.Session::get('LANG_CODE').'.json') !!}',
                    search: '<a class="btn searchBtn" id="searchBtn"><i class="fa fa-search"></i></a>'
                },
                processing: true,
                scrollX:    true,
                serverSide: true,
                ajax: '{!! action('TrackingController@getTrackingTable') !!}',
                columns: [
                    { data: 'id', name: 'id'},
                    { data: 'user', name: 'user'},
                    { data: 'ip', name: 'ip' },
                    { data: 'url', name: 'url'},
                    { data: 'message', name: 'message' },
                    { data: 'created_at', name: 'created_at' }
                ],
                order: [['0', 'desc']]
            });
        });
    </script>

@endsection