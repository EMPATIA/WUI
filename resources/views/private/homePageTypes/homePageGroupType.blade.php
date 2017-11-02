@extends('private._private.index')
@section('content')
    @php $form = ONE::form('homePageTypes', trans('privateHomePageTypes.details') , 'cm', 'home_page_types_children')
            ->settings(["model" => isset($homePageType) ? $homePageType : null,'id'=>isset($homePageType) ? $homePageType->home_page_type_key : null])
            ->show('HomePageTypesController@edit', 'HomePageTypesController@delete', ['key' => isset($homePageType) ? $homePageType->home_page_type_key : null], 'HomePageTypesController@index')
            ->create('HomePageTypesController@store', 'HomePageTypesController@index')
            ->edit('HomePageTypesController@update', 'HomePageTypesController@show', ['key' => isset($homePageType) ? $homePageType->home_page_type_key : null])
            ->open();
     @endphp

    {!! Form::oneSelect('type_code', trans('homePageType.type'), isset($types) ? $types : null, isset($homePageType->type_code) ? $homePageType->type_code : null, isset($homePageType->type_code) ? $homePageType->type_code : null, ['class' => 'form-control', 'id' => 'type_code']) !!}

    <!--<div id="parent" hidden>
        {!! Form::oneSelect('parent_key', trans('menus.parent'), isset($parents) ? $parents : null, isset($homePageType->parent) ? $homePageType->parent->home_page_type_key : null, isset($homePageType->parent) ? $homePageType->parent->name : null, ['class' => 'form-control', 'id' => 'parent_key']) !!}
            </div> -->
    {!! Form::oneText('name', trans('privateHomePageType.name'), isset($homePageType) ? $homePageType->name : null, ['class' => 'form-control', 'id' => 'name']) !!}
    {!! Form::oneText('code', trans('privateHomePageType.code'), isset($homePageType) ? $homePageType->code : null, ['class' => 'form-control', 'id' => 'code']) !!}
    {!! Form::oneText('parent_key', trans('privateHomePageType.parent'), isset($homePageTypeKey) ? $homePageTypeKey : null, ['class' => 'form-control hidden', 'id' => 'parent_key']) !!}
    {!! $form->make() !!}
@endsection