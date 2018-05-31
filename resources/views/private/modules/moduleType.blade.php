@extends('private._private.index')

@section('content')

    @php $form = ONE::form('moduleType')
            ->settings(["model" => isset($moduleType) ? $moduleType : null,'id'=>isset($moduleType) ? $moduleType->module_type_key : null])
            ->show('ModuleTypesController@edit', 'ModuleTypesController@delete', ['key' => isset($moduleType) ? $moduleType->module_type_key : null], 'ModuleTypesController@index', ['moduleKey' => isset($moduleType) ? $moduleType->module->module_key : null])
            ->create('ModuleTypesController@store', 'ModuleTypesController@index', ['key' => isset($moduleType) ? $moduleType->module_type_key : null, 'moduleKey' => isset($moduleKey) ? $moduleKey : null])
            ->edit('ModuleTypesController@update', 'ModuleTypesController@show', ['key' => isset($moduleType) ? $moduleType->module_type_key : null, 'moduleKey' => isset($moduleKey) ? $moduleKey : null])
            ->open();
    @endphp
    {!! Form::hidden('moduleKey', isset($moduleKey) ? $moduleKey : 0, ['id' => 'moduleKey']) !!}
    {!! Form::oneText('name', trans('privateModules.name'), isset($moduleType) ? $moduleType->name : null, ['class' => 'form-control', 'id' => 'name']) !!}
    {!! Form::oneText('code', trans('privateModules.code'), isset($moduleType) ? $moduleType->code : null, ['class' => 'form-control', 'id' => 'code']) !!}

    {!! $form->make() !!}


@endsection

@section('scripts')
    <script>


    </script>
@endsection