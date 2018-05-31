@extends('private._private.index')

@section('content')

    @php
    $form = ONE::form('speaker')
            ->settings(["model" => isset($speaker) ? $speaker->speaker_key : null,'id'=>isset($sessionKey) ? $sessionKey : null])
            ->show('ConferenceEventSpeakerController@edit', 'ConferenceEventSpeakerController@delete', ['eventKey' =>isset($eventKey) ? $eventKey : null, 'sessionKey'=> isset($sessionKey) ? $sessionKey : null,'speakerKey'=> isset($speaker) ? $speaker->speaker_key : null], 'ConferenceEventSessionController@show', ['eventKey' => isset($eventKey) ? $eventKey : null,'sessionKey'=> isset($sessionKey) ? $sessionKey : null])
            ->create('ConferenceEventSpeakerController@store', 'ConferenceEventSessionController@show', ['eventKey' =>isset($eventKey) ? $eventKey : null,'sessionKey'=> isset($sessionKey) ? $sessionKey : null])
            ->edit('ConferenceEventSpeakerController@update', 'ConferenceEventSpeakerController@show', ['eventKey' =>isset($eventKey) ? $eventKey : null, 'sessionKey'=> isset($sessionKey) ? $sessionKey : null,'speakerKey'=> isset($speaker) ? $speaker->speaker_key : null])
            ->open();
    @endphp

    {!! Form::hidden('event_key', isset($eventKey) ? $eventKey : null) !!}
    {!! Form::hidden('session_key', isset($sessionKey) ? $sessionKey : null) !!}
    {!! Form::hidden('speaker_key', isset($speaker) ? $speaker->speaker_key : null) !!}
    {!! Form::oneText('name', trans('conferenceEvents.name'), isset($speaker) ? $speaker->name : null, ['class' => 'form-control', 'id' => 'name' ,  'required']) !!}
    {!! Form::oneText('company', trans('conferenceEvents.company'), isset($speaker) ? $speaker->company : null, ['class' => 'form-control', 'id' => 'company' ,  'required']) !!}
    {!! Form::oneText('nationality', trans('conferenceEvents.nationality'), isset($speaker) ? $speaker->nationality : null, ['class' => 'form-control', 'id' => 'nationality' ,  'required']) !!}
    {!! Form::oneText('profession', trans('conferenceEvents.profession'), isset($speaker) ? $speaker->profession : null, ['class' => 'form-control', 'id' => 'profession' ,  'required']) !!}
    {!! Form::oneText('age', trans('conferenceEvents.age'), isset($speaker) ? $speaker->age : null, ['class' => 'form-control', 'id' => 'age' ,'type' => 'number', 'required']) !!}

    {!! $form->make() !!}

@endsection



