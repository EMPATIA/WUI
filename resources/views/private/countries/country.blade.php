@extends('private._private.index')

@section('content')

     @php
         $form = ONE::form('countries')
        ->settings(["model" => isset($country) ? $country : null])
        ->show('CountriesController@edit', 'CountriesController@delete', ['id' => isset($country) ? $country->id : null], 'CountriesController@index', ['id' => isset($country) ? $country->id : null])
        ->create('CountriesController@store', 'CountriesController@index', ['id' => isset($country) ? $country->id : null])
        ->edit('CountriesController@update', 'CountriesController@show', ['id' => isset($country) ? $country->id : null])
        ->open();
    @endphp
    
    {!! Form::oneText('name', trans('privateCountries.name'), isset($country) ? $country->name : null, ['class' => 'form-control', 'id' => 'name']) !!}
    {!! Form::oneText('code', trans('privateCountries.code'), isset($country) ? $country->code : null, ['class' => 'form-control', 'id' => 'code']) !!}
    
    {!! $form->make() !!}
     
     
     
     
@endsection

