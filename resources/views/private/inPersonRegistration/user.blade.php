@extends('private._private.index')

@section('content')
    @php
    $form = ONE::form('inPersonRegistration', 'auth', 'in_person_registration')
            ->settings(["model" => isset($user) ? $user : null, 'id' => isset($user) ? $user->user_key : null])
            ->show('InPersonRegistrationController@edit', 'InPersonRegistrationController@delete', ['userKey' => isset($user) ? $user->user_key : null], 'InPersonRegistrationController@index')
            ->create('InPersonRegistrationController@store', 'InPersonRegistrationController@index', ['userKey' => isset($user) ? $user->user_key : null])
            ->edit('InPersonRegistrationController@update', 'InPersonRegistrationController@show', ['userKey' => isset($user) ? $user->user_key : null])
            ->open();
    @endphp
    {!! Form::oneText('identity_card', trans('inPersonRegistration.identityCard'), isset($user) ? $user->identity_card : null, ['class' => 'form-control', 'id' => 'identity_card','required']) !!}
    {!! Form::oneText('name', trans('inPersonRegistration.name'), isset($user) ? $user->name : null, ['class' => 'form-control', 'id' => 'name','required']) !!}
    @if(isset($registerParameters))
        @foreach($registerParameters as $parameter)
            @if($parameter['parameter_type_code'] == 'birthday')
                {!! Form::oneDate($parameter['parameter_user_type_key'], $parameter['name'], ($parameter['value'] != '' ? $parameter['value'] : null), ['class' => 'form-control oneDatePicker', 'id' => $parameter['parameter_user_type_key'], 'required']) !!}
            @endif
        @endforeach
    @endif
    @if(ONE::actionType('inPersonRegistration') == "show")
        <a href="{{ action("InPersonRegistrationController@voteInPerson",['userKey' => $user->user_key,'type' => 'proposal']) }}" class="btn btn-flat empatia" target="_blank">{{trans('inPersonRegistration.voteInPerson')}}</a>
    @endif

    {!! $form->make() !!}

@endsection
