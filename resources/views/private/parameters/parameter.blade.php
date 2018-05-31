@extends('private._private.index')

@section('content')

    @php $form = ONE::form('parameters', trans('privateParameter.details'))
            ->settings(["model" => isset($parameter) ? $parameter : null])
            ->show('ParametersController@edit', 'ParametersController@delete', ['id' => isset($parameter) ? $parameter->id : null], 'ParametersController@index', ['id' => isset($parameter) ? $parameter->id : null])
            ->create('ParametersController@store', 'ParametersController@index', ['id' => isset($parameter) ? $parameter->id : null])
            ->edit('ParametersController@update', 'ParametersController@show', ['id' => isset($parameter) ? $parameter->id : null])
            ->open();
    @endphp

    {!! Form::oneSelect('parameter_type_id', trans('privateParameters.type'), $types, isset($parameter->parameter_type_id) ? $parameter->parameter_type_id : 0 , isset($typeName) ? $typeName : null, ['class' => 'form-control', 'id' => 'parameter_type_id'] ) !!}

    {!! Form::oneText('parameter', trans('privateParameters.name'), isset($parameter) ? $parameter->parameter : null, ['class' => 'form-control', 'id' => 'parameter']) !!}
    {!! Form::oneTextArea('description', trans('privateParameters.description'), isset($parameter) ? $parameter->description : null, ['class' => 'form-control', 'id' => 'description']) !!}

    {!! $form->make() !!}

@endsection

