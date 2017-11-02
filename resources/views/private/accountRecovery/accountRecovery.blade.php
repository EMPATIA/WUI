@extends('private._private.index')

@section('content')
     
    @php
    $form = ONE::form('accountRecovery',trans('privateAccountRecovery.account_recovery'))
        ->settings(["model" => isset($authMethod) ? $authMethod : null])
        ->show('AccountRecoveryController@edit', 'AccountRecoveryController@delete', ['key' => isset($accountRecoveryParameter) ? $accountRecoveryParameter->table_key: null], 'AccountRecoveryController@index')
        ->create('AccountRecoveryController@store', 'AccountRecoveryController@index', ['key' => isset($accountRecoveryParameter) ? $accountRecoveryParameter->table_key : null])
        ->edit('AccountRecoveryController@update', 'AccountRecoveryController@show', ['key' => isset($accountRecoveryParameter) ? $accountRecoveryParameter->table_key : null])
        ->open();
    @endphp

    {!! Form::oneSelect('parameter_user_type', trans('privateAccountRecovery.parameter_user_type'), $registerParameters ?? [], $accountRecoveryParameter->parameter_user_type_key ?? null, $accountRecoveryParameter->name ?? null, ['class' => 'form-control','required', (ONE::actionType("accountRecovery")=="edit" ? "disabled" : "")] ) !!}

    {!! Form::oneSwitch('send_token',trans('privateAccountRecovery.send_token'), $accountRecoveryParameter->send_token ?? null, ['id' => 'send_token'] ) !!}

    {!! $form->make() !!}
@endsection

