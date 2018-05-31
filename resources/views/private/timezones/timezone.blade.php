@extends('private._private.index')

@section('content')

    @php $form = ONE::form('timezones')
        ->settings(["model" => isset($timezone) ? $timezone : null])
        ->show('TimezonesController@edit', 'TimezonesController@delete', ['id' => isset($timezone) ? $timezone->id : null], 'TimezonesController@index', ['id' => isset($timezone) ? $timezone->id : null])
        ->create('TimezonesController@store', 'TimezonesController@index', ['id' => isset($timezone) ? $timezone->id : null])
        ->edit('TimezonesController@update', 'TimezonesController@show', ['id' => isset($timezone) ? $timezone->id : null])
        ->open();
    @endphp

    {!! Form::oneText('country_code', trans('privateTimezones.country_code'), isset($timezone) ? $timezone->country_code : null, ['class' => 'form-control', 'id' => 'country_code']) !!}
    {!! Form::oneText('name', trans('privateTimezones.name'), isset($timezone) ? $timezone->name : null, ['class' => 'form-control', 'id' => 'name']) !!}

    {!! $form->make() !!}
     
@endsection

