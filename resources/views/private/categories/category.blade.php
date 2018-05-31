@extends('private._private.index')

@section('content')
 
    @php
     $form = ONE::form('categories', trans('privateCategories.details'))
        ->settings(["model" => isset($category) ? $category : null])
        ->show('CategoriesController@edit', 'CategoriesController@delete', ['key' => isset($category) ? $category->category_key : null], 'CategoriesController@index')
        ->create('CategoriesController@store', 'CategoriesController@index', ['key' => isset($category) ? $category->category_key : null])
        ->edit('CategoriesController@update', 'CategoriesController@show', ['key' => isset($category) ? $category->category_key : null])
        ->open();
    @endphp
    
    {!! Form::oneText('name', trans('privateCategories.name'), isset($category) ? $category->name : null, ['class' => 'form-control', 'id' => 'name']) !!}
    {!! Form::oneText('description', trans('privateCategories.description'), isset($category) ? $category->description : null, ['class' => 'form-control', 'id' => 'description']) !!}
    
    {!! $form->make() !!}
     
@endsection