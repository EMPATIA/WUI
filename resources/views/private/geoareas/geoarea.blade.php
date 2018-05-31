@extends('private._private.index')

@section('content')
 
    @php $form = ONE::form('geoareas', trans('privateGeoareas.details'))
        ->settings(["model" => isset($geoarea) ? $geoarea : null])
        ->show('GeoAreasController@edit', 'GeoAreasController@delete', ['key' => isset($geoarea) ? $geoarea->geo_key : null], 'GeoAreasController@index')
        ->create('GeoAreasController@store', 'GeoAreasController@index', ['key' => isset($geoarea) ? $geoarea->geo_key : null])
        ->edit('GeoAreasController@update', 'GeoAreasController@show', ['key' => isset($geoarea) ? $geoarea->geo_key : null])
        ->open();
    @endphp
    
    {!! Form::oneText('name', trans('form.name'), isset($geoarea) ? $geoarea->name : null, ['class' => 'form-control', 'id' => 'name']) !!}
    
    {!! $form->make() !!}
     
@endsection

