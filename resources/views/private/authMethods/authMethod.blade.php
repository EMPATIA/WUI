@extends('private._private.index')

@section('content')
     
     @php
     $form = ONE::form('authMethods')
        ->settings(["model" => isset($authMethod) ? $authMethod : null])
        ->show('AuthMethodsController@edit', 'AuthMethodsController@delete', ['auth_method_key' => isset($authMethod) ? $authMethod->auth_method_key : null], 'AuthMethodsController@index')
        ->create('AuthMethodsController@store', 'AuthMethodsController@index', ['auth_method_key' => isset($authMethod) ? $authMethod->auth_method_key : null])
        ->edit('AuthMethodsController@update', 'AuthMethodsController@show', ['auth_method_key' => isset($authMethod) ? $authMethod->auth_method_key : null])
        ->open();
    @endphp
    
    {!! Form::oneText('name', trans('privateAuthMethods.name'), isset($authMethod) ? $authMethod->name : null, ['class' => 'form-control', 'id' => 'name']) !!}
    {!! Form::oneText('description', trans('privateAuthMethods.description'), isset($authMethod) ? $authMethod->description : null, ['class' => 'form-control', 'id' => 'description']) !!}
    {!! Form::oneText('code', trans('privateAuthMethods.code'), isset($authMethod) ? $authMethod->code : null, ['class' => 'form-control', 'id' => 'code']) !!}

    {!! $form->make() !!}
     
@endsection

