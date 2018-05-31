@extends('_private.index')

@section('content')
    {!!
        ONE::form('users')
            ->show('UsersController@edit', 'UsersController@destroy', ['id' => isset($user) ? $user->user_key : null])
            ->create('UsersController@store', 'UsersController@index', ['id' => isset($user) ? $user->user_key : null])
            ->edit(isset($user) ? $user : null, 'UsersController@update', 'UsersController@show', ['id' => isset($user) ? $user->user_key : null])
            ->addField('name', trans('user.name'), Form::text('name', isset($user) ? $user->name : null, ['class' => 'form-control', 'id' => 'site']), isset($user) ? $user->name : null)
            ->addField('login', trans('user.login'), Form::text('login', isset($user) ? $user->login : null, ['class' => 'form-control', 'id' => 'site']), isset($user) ? $user->login : null)
            ->addField('email', trans('user.email'), Form::text('email', isset($user) ? $user->email : null, ['class' => 'form-control', 'id' => 'site']), isset($user) ? $user->email : null)
            ->addField('phone_number', trans('user.phone_number'), Form::text('phone_number', isset($user) ? $user->phone_number : null, ['class' => 'form-control', 'id' => 'site']), isset($user) ? $user->phone_number : null)
            ->addField('mobile_number', trans('user.mobile_number'), Form::text('mobile_number', isset($user) ? $user->mobile_number : null, ['class' => 'form-control', 'id' => 'site']), isset($user) ? $user->mobile_number : null)
            ->addField('gender', trans('user.gender'), Form::text('gender', isset($user) ? $user->gender : null, ['class' => 'form-control', 'id' => 'site']), isset($user) ? $user->gender : null)
            ->addField('birthday', trans('user.birthday'), Form::text('birthday', isset($user) ? $user->birthday : null, ['class' => 'form-control', 'id' => 'site']), isset($user) ? $user->birthday : null)
            ->addField('vat_number', trans('user.vat_number'), Form::text('vat_number', isset($user) ? $user->vat_number : null, ['class' => 'form-control', 'id' => 'site']), isset($user) ? $user->vat_number : null)
            ->addField('identity_card', trans('user.identity_card'), Form::text('identity_card', isset($user) ? $user->identity_card : null, ['class' => 'form-control', 'id' => 'site']), isset($user) ? $user->identity_card : null)
            ->addField('identity_type', trans('user.identity_type'), Form::text('identity_type', isset($user) ? $user->identity_type : null, ['class' => 'form-control', 'id' => 'site']), isset($user) ? $user->identity_type : null)
            ->addField('city', trans('user.city'), Form::text('city', isset($user) ? $user->city : null, ['class' => 'form-control', 'id' => 'site']), isset($user) ? $user->city : null)
            ->addField('country', trans('user.country'), Form::text('country', isset($user) ? $user->country : null, ['class' => 'form-control', 'id' => 'site']), isset($user) ? $user->country : null)
            ->addField('nationality', trans('user.nationality'), Form::text('nationality', isset($user) ? $user->nationality : null, ['class' => 'form-control', 'id' => 'site']), isset($user) ? $user->nationality : null)
            ->addField('homepage', trans('user.homepage'), Form::text('homepage', isset($user) ? $user->homepage : null, ['class' => 'form-control', 'id' => 'site']), isset($user) ? $user->homepage : null)
            ->addField('created_at', trans('user.created_at'), Form::text('created_at', isset($user) ? $user->created_at : null, ['class' => 'form-control', 'id' => 'site']), isset($user) ? $user->created_at : null)
            ->addField('updated_at', trans('user.updated_at'), Form::text('updated_at', isset($user) ? $user->updated_at : null, ['class' => 'form-control', 'id' => 'site']), isset($user) ? $user->updated_at : null)
            ->make()
    !!}

@endsection
