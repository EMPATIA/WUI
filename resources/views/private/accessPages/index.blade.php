@extends('private._private.index')

@section('content')
    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title"><i class="fa"></i> {{ trans('privateAccessPages.accessPages') }}</h3>
        </div>

        <div class="box-body">
            <table id="accessPages_list" class="table table-hover table-striped dataTable no-footer table-responsive">
                <thead>
                <tr>
                     <th>{{ trans('privateAccessPages.id') }}</th>
                    <th>{{ trans('privateAccessPages.name') }}</th>
                    <th>{{ trans('privateAccessPages.accessTypeId') }}</th>
                    <th>{{ trans('privateAccessPages.active') }}</th>
                    <th>{!! ONE::actionButtons(null, ['create' => 'AccessPagesController@create']) !!}</th>
                </tr>
                </thead>
            </table>
        </div>
    </div>

@endsection


@section('scripts')
    <script>
        
        $(function () {
            $('#accessPages_list').DataTable({
                language: {
                    url: '{!! asset('/datatableLang/'.Session::get('LANG_CODE').'.json') !!}',
                    search: '<a class="btn searchBtn" id="searchBtn"><i class="fa fa-search"></i></a>'
                },
                processing: true,
                serverSide: true,
                ajax: '{!! action('AccessPagesController@tableAccessPages') !!}',
                columns: [
                    { data: 'id', name: 'id', width: "20px" },
                    { data: 'name', name: 'name' },
                    { data: 'access_type_id', name: 'access_type_id' },
                    { data: 'active', name: 'active' },
                    { data: 'action', name: 'action', searchable: false, orderable: false, width: "50px" },
                ],
                order: [['1', 'asc']]
            });

        });

    </script>
@endsection



