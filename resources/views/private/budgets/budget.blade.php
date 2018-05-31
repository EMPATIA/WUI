@extends('private._private.index')

@section('content')

    @php
    $form = ONE::form('budgets')
        ->settings(["model" => isset($budget) ? $budget : null])
        ->show('BudgetsController@edit', 'BudgetsController@delete', ['id' => isset($budget) ? $budget->id : null], 'BudgetsController@index', ['id' => isset($budget) ? $budget->id : null])
        ->create('BudgetsController@store', 'BudgetsController@index', ['id' => isset($budget) ? $budget->id : null])
        ->edit('BudgetsController@update', 'BudgetsController@show', ['id' => isset($budget) ? $budget->id : null])
        ->open();
    @endphp
    
    {!! Form::oneSelect('category_id', trans('privateBudget.category'), isset($category) ? $category : null, isset($budget) ? $budget->category_id : null, null, ['class' => 'form-control', 'id' => 'category_id']) !!}
    {!! Form::oneText('value', trans('privateBudget.value'), isset($budget) ? $budget->value : null, ['class' => 'form-control', 'id' => 'value']) !!}
    {!! Form::hidden('mp_id', isset($mp_id) ? $mp_id : null) !!}
    
    {!! $form->make() !!}
     
@endsection