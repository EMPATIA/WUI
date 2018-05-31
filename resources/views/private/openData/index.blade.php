@extends('private._private.index')


@section('content')
    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title"><i class="fa"></i> {{ trans('privateOpenData.list') }}</h3>
        </div>
        <div class="box-body">
            <br><br>
            <table id="openDatas-list" class="table table-hover table-striped dataTable no-footer table-responsive">
                <thead>
                    <tr>
                        <th>{{ trans('privateOpenData.entity') }}</th>
                        <th>{{ trans('privateOpenData.created_at') }}</th>
                        <th>{{ trans('privateOpenData.created_by') }}</th>
                        <th></th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(function () {
            $('#openDatas-list').DataTable({
                processing: true,
                serverSide: true,
                  ajax: '{!! action('OpenDataController@getIndexTable') !!}',
                columns: [
                    { data: 'entity_name', name: 'entity_name' },
                    { data: 'created_by', name: 'created_by' },
                    { data: 'created_at', name: 'created_at' },
                    { data: 'action', name: 'action', searchable: false, orderable: false, width: "50px" },
                ]
            });
        });
    </script>
@endsection
