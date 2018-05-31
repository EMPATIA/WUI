@extends('private._private.index')

@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title"><i class="fa"></i> {{ trans('titles.Abuses') }}</h3>
        </div>

        <div class="box-body">
            <table id="abuses_list" class="table table-hover table-striped dataTable no-footer table-responsive">
                <thead>
                <tr>
                    <th>{{ trans('privateAbuse.id') }}</th>
                    <th>{{ trans('privateAbuse.type') }}</th>
                    <th>{{ trans('privateAbuse.processed') }}</th>
                    <th>{{ trans('privateAbuse.created_at') }}</th>
                    <th>{!! ONE::actionButtons(null, ['create' => 'AbuseController@create']) !!}</th>
                </tr>
                </thead>
            </table>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(function () {
            $('#abuses_list').DataTable({
                language: {
                    url: '{!! asset('/datatableLang/'.Session::get('LANG_CODE').'.json') !!}'
                },
                processing: true,
                serverSide: true,
                ajax: '{!! action('AbuseController@getIndexTable') !!}',
                columns: [
                    { data: 'id', name: 'id', width: "20px" },
                    { data: 'type', name: 'type' },
                    { data: 'processed', name: 'processed' },
                    { data: 'created_at', name: 'created_at' },
                    { data: 'action', name: 'action', searchable: false, orderable: false, width: "30px" },
                ],
                order: [['1', 'asc']]
            });
        });
    </script>
@endsection



