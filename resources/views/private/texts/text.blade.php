@extends('private._private.index')

@section('content')

    @php $form = ONE::form('texts')
        ->settings(["model" => isset($text) ? $text : null])
        ->show('TextsController@edit', 'TextsController@delete', ['id' => isset($text) ? $text->id : null], 'TextsController@index', ['id' => isset($text) ? $text->id : null])
        ->create('TextsController@store', 'TextsController@index', ['id' => isset($text) ? $text->id : null])
        ->edit('TextsController@update', 'TextsController@show', ['id' => isset($text) ? $text->id : null])
        ->open();
    @endphp
    
    {!! Form::oneText('title', trans('privateTexts.title'), isset($text) ? $text->title : null, ['class' => 'form-control', 'id' => 'title']) !!}
    {!! Form::oneTextArea('content', trans('privateTexts.content'), isset($text) ? $text->content : null, ['class' => 'form-control', 'id' => 'content']) !!}
    {!! Form::oneText('tag', trans('privateTexts.tag'), isset($text) ? $text->tag : null, ['class' => 'form-control', 'id' => 'tag']) !!}
    
    {!! $form->make() !!}
     
@endsection

