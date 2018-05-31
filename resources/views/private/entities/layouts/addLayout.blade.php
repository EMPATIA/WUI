@extends('private._private.index')

@section('content')

    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title"><i class="fa"></i> {{ trans('privateEntities.addTemplate') }}</h3>
        </div>

        <div class="box-body">
            <table id="layout_list" class="table table-striped dataTable no-footer table-responsive">
                <thead>
                <tr>
                    <th>{{ trans('privateEntities.name') }}</th>
                    <th>{{ trans('privateEntities.reference') }}</th>
                    <th>{{ trans('privateEntities.add') }}</th>
                </tr>
                </thead>
            </table>
        </div>

        <div class="box-footer">
            <a class="btn btn-flat btn-primary" href=" {!!  action('EntitiesDividedController@showLayouts') !!}"><i class="fa fa-arrow-left"></i> {!! trans('privateEntities.back') !!}</a>
        </div>
    </div>


@endsection


@section('scripts')
    <script>

        $(function () {
            $('#layout_list').DataTable({
                language: {
                    url: '{!! asset('/datatableLang/'.Session::get('LANG_CODE').'.json') !!}',
                    search: '<a class="btn searchBtn" id="searchBtn"><i class="fa fa-search"></i></a>'
                },
                processing: true,
                serverSide: true,
                ajax: '{!! action("EntitiesDividedController@tableAddLayout", $entityKey) !!}',
                columns: [
                    {data: 'name', name: 'name'},
                    {data: 'reference', name: 'reference'},
                    {data: 'action', name: 'action', searchable: false, orderable: false}
                ],
                order: [['0', 'asc']]
            });
        });


    </script>
@endsection