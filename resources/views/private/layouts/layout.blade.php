@extends('private._private.index')

@section('content')

    @if(ONE::asPermission('admin') && ONE::getEntityKey() == null)
        @php $form = ONE::form('layout')
                ->settings(["model" => isset($layout) ? $layout : null,'id'=>isset($layout) ? $layout->layout_key : null])
                ->show('LayoutsController@edit', 'LayoutsController@delete', ['roleKey' => isset($layout) ? $layout->layout_key : null], null)
                ->create('LayoutsController@store', 'LayoutsController@index', ['roleKey' => isset($layout) ? $layout->layout_key : null])
                ->edit('LayoutsController@update', 'LayoutsController@show', ['roleKey' => isset($layout) ? $layout->layout_key : null])
        @endphp
    @else
        @php  $form = ONE::form('layout', 'orchestrator', 'entity_layout')
                ->settings(["model" => isset($layout) ? $layout : null,'id'=>isset($layout) ? $layout->layout_key : null])
                ->show('LayoutsController@edit', 'LayoutsController@delete', ['roleKey' => isset($layout) ? $layout->layout_key : null], 'EntitiesDividedController@showLayouts', ['roleKey' => isset($layout) ? $layout->layout_key : null])
                ->create('LayoutsController@store', 'LayoutsController@index', ['roleKey' => isset($layout) ? $layout->layout_key : null])
                ->edit('LayoutsController@update', 'LayoutsController@show', ['roleKey' => isset($layout) ? $layout->layout_key : null])
                ->open();
        @endphp
    @endif

    {!! Form::oneText('name', trans('privateLayouts.name'), isset($layout) ? $layout->name : null, ['class' => 'form-control', 'id' => 'name']) !!}
    {!! Form::oneText('reference', trans('privateLayouts.reference'), isset($layout) ? $layout->reference : null, ['class' => 'form-control', 'id' => 'reference']) !!}

    {!! $form->make() !!}

@endsection
