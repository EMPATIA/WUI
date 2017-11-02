@extends('private._private.index')

@section('content')
    <div class="box box-primary">

        <div class="box-header">
            <h3 class="box-title"><i class="fa"></i> {{ trans('privateEntities.entities') }}</h3>
        </div>

        <div class="box-body">
            <table id="entities_list" class="table table-striped dataTable no-footer table-responsive">
                <thead>
                <tr>
                    <th>{{ trans('privateEntities.id') }}</th>
                    <th>{{ trans('privateEntities.name') }}</th>
                    <th>{{ trans('privateEntities.url') }}</th>
                    <th>{{ trans('privateEntities.created_by') }}</th>
                    <th>{!! ONE::actionButtons(null, ['create' => 'EntitiesController@create']) !!}</th>
                </tr>
                </thead>
            </table>
        </div>
    </div>

@endsection


@section('scripts')

    <script>

        $(function () {
            $('#entities_list').DataTable({
                language: {
                    url: '{!! asset('/datatableLang/'.Session::get('LANG_CODE').'.json') !!}',
                    search: '<a class="btn searchBtn" id="searchBtn"><i class="fa fa-search"></i></a>'
                },
                processing: true,
                serverSide: true,
                ajax: '{!! action('EntitiesController@tableEntities') !!}',
                columns: [
                    { data: 'id', name: 'id', width: "20px" },
                    { data: 'name', name: 'name' },
                    { data: 'url', name: 'url' },
                    { data: 'created_by', name: 'created_by' },
                    { data: 'action', name: 'action', searchable: false, orderable: false, width: "50px" },
                ],
                order: [['1', 'asc']]
            });

        });

    </script>
@endsection

