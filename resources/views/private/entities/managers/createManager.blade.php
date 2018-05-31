@extends('private._private.index')

@section('content')
    @php

    $form = ONE::form('entitiesDivided', trans('privateManagers.details'))
            ->settings(["model" => isset($user) ? $user : null, 'id' => isset($user) ? $user->user_key : null])
            ->show('EntitiesDividedController@editManager', 'EntitiesDividedController@deleteUserConfirm', ['userKey' => isset($user) ? $user->user_key : null],
                    'EntitiesDividedController@showManagers',['userKey' => isset($user) ? $user->user_key : null])
            ->create('EntitiesDividedController@storeManager', 'EntitiesDividedController@showManagers', ['userKey' => isset($user) ? $user->user_key : null])
            ->edit('EntitiesDividedController@updateManager', 'EntitiesDividedController@showManagers', ['userKey' => isset($user) ? $user->user_key : null])
            ->open();

    @endphp

    {!! Form::hidden('role', 'manager') !!}


    <!--
    @if(ONE::actionType('entitiesDivided') != "show")
        {!! Form::oneSelect('roles[]', trans('privateEntities.userRoles'), isset($roles) ? $roles : null, null,['class' => 'form-control', 'id' => 'country_id']) !!}
    @endif
    -->

    {!! Form::oneText('name', trans('privateEntities.name'), isset($user) ? $user->name  : null, ['class' => 'form-control', 'id' => 'name']) !!}
    {!! Form::oneText('email', trans('privateEntities.email'), isset($user) ? $user->email  : null, ['class' => 'form-control', 'id' => 'email']) !!}

    @if(ONE::actionType('entitiesDivided') == "create")
        <!-- Change password -->
        <div class="card flat">
            <div class="card-header">{{ trans('user.changePassword') }}</div>
            <div class="box-body">
                {!! Form::onePassword('password', trans('user.password'), null, ['class' => 'form-control', 'id' => 'password',(ONE::actionType('entitiesDivided') == "create" ? 'required' : null)]) !!}
                {!! Form::onePassword('password_confirmation', trans('user.passwordConfirmation'), null, ['class' => 'form-control', 'id' => 'password_confirmation',(ONE::actionType('entitiesDivided') == "create" ? 'required' : null)]) !!}
            </div>
        </div>
    @endif

    {!! $form->make() !!}

@endsection
