@extends('private._private.index')

@section('content')
    @php

    $form = ONE::form('entities', trans('privateEntities.details'))
            ->settings(["model" => isset($user) ? $user : null, 'id' => isset($user) ? $user->user_key : null])
            ->show('EntitiesController@editManager', 'EntitiesController@deleteUserConfirm', ['entityId' => isset($entityId)? $entityId : null,'id' => isset($user) ? $user->user_key : null],
                    'EntitiesController@showManagers',['entityKey' => $entityKey ,'id' => isset($user) ? $user->user_key : null])
            ->create('EntitiesController@storeManager', 'EntitiesController@showManagers', ['entityKey' => isset($entityKey)? $entityKey : null , 'id' => isset($user) ? $user->user_key : null])
            ->edit('EntitiesController@updateManager', 'EntitiesController@showManager', ['entityKey' => isset($entityKey)? $entityKey : null ,'id' => isset($user) ? $user->user_key : null])
            ->open();
    @endphp

    {!! Form::hidden('role', 'manager') !!}
    {!! Form::hidden('entityKey',isset($entityKey)? $entityKey : null) !!}

    @if(ONE::actionType('entities') != "show")
        {!! Form::oneSelect('roles[]', trans('privateEntities.user_roles'), isset($roles) ? $roles : null,  isset($userRoleKey) ? $userRoleKey : null , isset($userRoleName) ? $userRoleName : null, ['class' => 'form-control', 'id' => 'country_id']) !!}

    @endif
    {!! Form::oneText('name', trans('privateEntities.name'), isset($user) ? $user->name  : null, ['class' => 'form-control', 'id' => 'name']) !!}
    {!! Form::oneText('email', trans('privateEntities.email'), isset($user) ? $user->email  : null, ['class' => 'form-control', 'id' => 'email']) !!}

    @if(ONE::actionType('entities') == "create")
        <!-- Change password -->
        <div class="card flat">
            <div class="card-header">{{ trans('privateEntities.change_password') }}</div>
            <div class="box-body">
                {!! Form::onePassword('password', trans('privateEntities.password'), null, ['class' => 'form-control', 'id' => 'password',(ONE::actionType('entities') == "create" ? 'required' : null)]) !!}
                {!! Form::onePassword('password_confirmation', trans('privateEntities.password_confirmation'), null, ['class' => 'form-control', 'id' => 'password_confirmation',(ONE::actionType('entities') == "create" ? 'required' : null)]) !!}
            </div>
        </div>
    @endif

    {!! $form->make() !!}

@endsection