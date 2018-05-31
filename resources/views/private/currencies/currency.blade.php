@extends('private._private.index')

@section('content')

     @php
         $form = ONE::form('currencies')
        ->settings(["model" => isset($currency) ? $currency : null])
        ->show('CurrenciesController@edit', 'CurrenciesController@delete', ['id' => isset($currency) ? $currency->id : null], 'CurrenciesController@index', ['id' => isset($currency) ? $currency->id : null])
        ->create('CurrenciesController@store', 'CurrenciesController@index', ['id' => isset($currency) ? $currency->id : null])
        ->edit('CurrenciesController@update', 'CurrenciesController@show', ['id' => isset($currency) ? $currency->id : null])
        ->open();
    @endphp
    
    {!! Form::oneText('currency', trans('privateCurrencies.currency'), isset($currency) ? $currency->currency : null, ['class' => 'form-control', 'id' => 'currency']) !!}
    {!! Form::oneText('symbol_left', trans('privateCurrencies.symbol_left'), isset($currency) ? $currency->symbol_left : null, ['class' => 'form-control', 'id' => 'symbol_left']) !!}
    {!! Form::oneText('symbol_right', trans('privateCurrencies.symbol_right'), isset($currency) ? $currency->symbol_right : null, ['class' => 'form-control', 'id' => 'symbol_right']) !!}
    {!! Form::oneText('code', trans('privateCurrencies.code'), isset($currency) ? $currency->code : null, ['class' => 'form-control', 'id' => 'code']) !!}
    {!! Form::oneText('decimal_place', trans('privateCurrencies.decimal_place'), isset($currency) ? $currency->decimal_place : null, ['class' => 'form-control', 'id' => 'decimal_place']) !!}
    {!! Form::oneText('decimal_point', trans('privateCurrencies.decimal_point'), isset($currency) ? $currency->decimal_point : null, ['class' => 'form-control', 'id' => 'decimal_point']) !!}
    {!! Form::oneText('thousand_point', trans('privateCurrencies.thousand_point'), isset($currency) ? $currency->thousand_point : null, ['class' => 'form-control', 'id' => 'thousand_point']) !!}
    
    {!! $form->make() !!}
     
@endsection
