@extends('public._layouts.index')

@section('content')

    <?php
    $form = ONE::form('registration')
            ->create('RegistrationsController@store', 'PublicConfEventsController@show', ['eventKey' => isset($eventKey) ? $eventKey : null])
            ->open()
    ?>

    {!! Form::hidden('event_key', $eventKey) !!}
    {!! Form::oneText('name', trans('publicRegistration.name'), isset($name) ? $registration->name : null, ['class' => 'form-control', 'id' => 'name']) !!}
    {!! Form::oneText('email', trans('publicRegistration.email'), isset($email) ? $registration->email : null, ['class' => 'form-control', 'id' => 'email']) !!}
    {!! $form->make() !!}
@endsection