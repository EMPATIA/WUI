@extends('private._private.index')

@section('content')

    @php $form = ONE::form('tags')
        ->settings(["model" => isset($tag) ? $tag : null])
        ->show('TagsController@edit', 'TagsController@delete', ['id' => isset($tag) ? $tag->id : null], 'TagsController@index', ['id' => isset($tag) ? $tag->id : null])
        ->create('TagsController@store', 'TagsController@index', ['id' => isset($tag) ? $tag->id : null])
        ->edit('TagsController@update', 'TagsController@show', ['id' => isset($tag) ? $tag->id : null])
        ->open();
    @endphp
    
    {!! Form::oneText('name', trans('privateTags.name'), isset($tag) ? $tag->name : null, ['class' => 'form-control', 'id' => 'name']) !!}
    {!! Form::hidden('entity_id', isset($entity_id) ? $entity_id : null) !!}
    
    {!! $form->make() !!}
     
@endsection

