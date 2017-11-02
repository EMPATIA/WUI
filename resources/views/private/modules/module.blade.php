@extends('private._private.index')

@section('content')

    @php $form = ONE::form('module')
        ->settings(["model" => isset($module) ? $module : null,'id'=>isset($module) ? $module->module_key : null])
        ->show('ModulesController@edit', 'ModulesController@delete', ['key' => isset($module) ? $module->module_key : null], 'ModulesController@index')
        ->create('ModulesController@store', 'ModulesController@index', ['key' => isset($module) ? $module->module_key : null])
        ->edit('ModulesController@update', 'ModulesController@show', ['key' => isset($module) ? $module->module_key : null])
        ->open();
    @endphp

    {!! Form::oneText('name', trans('privateModules.name'), isset($module) ? $module->name : null, ['class' => 'form-control', 'id' => 'name']) !!}
    {!! Form::oneText('code', trans('privateModules.code'), isset($module) ? $module->code : null, ['class' => 'form-control', 'id' => 'code']) !!}

    {!! $form->make() !!}

@endsection