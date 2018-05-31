@extends('private._private.index')

@section('content')

    @php $form = ONE::form('config')
            ->settings(["model" => isset($config) ? $config : null,'id'=>isset($config) ? $config->id : null ])
            ->show(null, null, ['configTypeId' => isset($configType) ? $configType->id : null,'id' => isset($config) ? $config->id : null], null)
            ->create('CbsConfigsController@store', 'configType@show', ['configTypeId' => isset($configType) ? $configType->id : null,'id' => isset($config) ? $config->id : null])
            ->edit('CbsConfigsController@update', 'CbsConfigsController@show', ['configTypeId' => isset($configType) ? $configType->id : null,'id' => isset($config) ? $config->id : null])
            ->open();
    @endphp

    @if(ONE::actionType('cbConfigType') == 'show')
        <div class="card flat">
            <div class="card-header">{{ trans('privateCbsConfigs.configurations') }}</div>
            <div class="box-body">
                <table id="config_list" class="table table-hover table-striped dataTable no-footer table-responsive">
                    <thead>
                    <tr>
                        <th>{{ trans('privateCbsConfigs.id') }}</th>
                        <th>{{ trans('privateCbsConfigs.title') }}</th>
                        <th>{!! ONE::actionButtons(['configTypeId'=>$configType->id], ['create' => 'CbsConfigsController@create']) !!}</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    @endif

    {!! $form->make() !!}

@endsection

@section('scripts')
    <script>
    $(function(){
        getSidebar('{{ action("OneController@getSidebar") }}', 'config', "{{isset($configType) ? $configType->id : null}}", 'sidebar_admin.cbs_configs');

        $('#config_list').DataTable({
            language: {
                url: '{!! asset('/datatableLang/'.Session::get('LANG_CODE').'.json') !!}'
            },
            processing: true,
            serverSide: true,
            ajax: '{!! action('CbsConfigsController@getIndexTable',['configTypeId'=>isset($configType->id)? $configType->id : null]) !!}',
            columns: [
                { data: 'id', name: 'id', width: "20px" },
                { data: 'title', name: 'title' },
                { data: 'action', name: 'action', searchable: false, orderable: false, width: "30px" },
            ],
            order: [['1', 'asc']]
        });
    })
    </script>
@endsection