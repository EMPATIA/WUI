@extends('private._private.index')

@section('content')

    @php $form = ONE::form('module')
            ->settings(["model" => isset($module) ? $module : null,'id'=>isset($module) ? $module->module_key : null])
            ->show('ModulesController@edit', 'ModulesController@delete', ['key' => isset($module) ? $module->module_key : null], 'ModulesController@index')
            ->create('ModulesController@store', 'ModulesController@index', ['key' => isset($module) ? $module->module_key : null])
            ->edit('ModulesController@update', 'ModulesController@show', ['key' => isset($module) ? $module->module_key : null])
            ->open();
    @endphp

    {!! $form->make() !!}

    @if(ONE::actionType('module') != 'create')
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
                        <th>{!! ONE::actionButtons(isset($module) ? $module->module_key: null, ['create' => 'ModuleTypesController@create']) !!}</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    @endif

@endsection

@section('scripts')
    <script>
        $(function () {
            $('#module_types_list').DataTable({
                language: {
                    url: '{!! asset('/datatableLang/'.Session::get('LANG_CODE').'.json') !!}',
                    search: '<a class="btn searchBtn" id="searchBtn"><i class="fa fa-search"></i></a>'
                },
                processing: true,
                serverSide: true,
                ajax: '{!! action('ModuleTypesController@getIndexTable',isset($module) ? $module->module_key: null)!!}',
                columns: [
                    { data: 'module_type_key', name: 'module_type_key', width: "20px" },
                    { data: 'name', name: 'name' },
                    { data: 'action', name: 'action', searchable: false, orderable: false, width: "30px" },
                ],
                order: [['1', 'asc']]
            });

        });
    </script>
@endsection