@extends('private._private.index')

@section('content')

    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title"><i class="fa"></i> {{ trans('privateModules.module_types') }}</h3>
        </div>

        <div class="box-body">
            <table id="module_types_list" class="table table-hover table-striped dataTable no-footer table-responsive">
                <thead>
                <tr>
                    <th>{{ trans('privateModules.key') }}</th>
                    <th>{{ trans('privateModules.name') }}</th>
                    <th>{!! ONE::actionButtons(isset($moduleKey) ? $moduleKey: null, ['create' => 'ModuleTypesController@create']) !!}</th>
                </tr>
                </thead>
            </table>
        </div>
    </div>

@endsection

@section('scripts')
    <script>
        $(function () {
            $('#module_types_list').DataTable({
                language: {
                    url: '{!! asset('/datatableLang/'.Session::get('LANG_CODE').'.json') !!}'
                },
                processing: true,
                serverSide: true,
                ajax: '{!! action('ModuleTypesController@getIndexTable',isset($moduleKey) ? $moduleKey: null)!!}',
                columns: [
                    { data: 'module_type_key', name: 'module_type_key', width: "20px" },
                    { data: 'name', name: 'name' },
                    { data: 'action', name: 'action', searchable: false, orderable: false, width: "50px" },
                ],
                order: [['1', 'asc']]
            });

        });
    </script>
@endsection