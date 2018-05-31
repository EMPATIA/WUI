@extends('private._private.index')

@section('content')

    @php $form = ONE::form('files',trans('files.details'))
        ->settings(["model" => isset($file) ? $file : null])
        ->show('ContentsController@editFileDetails', 'ContentsController@deleteFile', ['contentKey' => isset($contentKey) ? $contentKey : null, 'id' => isset($file) ? $file->file_id : null], 'ContentsController@show', ['id' => $contentKey ? $contentKey : null])
        ->create('ContentsController@store', 'ContentsController@index', ['contentId' => isset($contentKey) ? $contentKey : null, 'id' => isset($file) ? $file->file_id : null])
        ->edit('ContentsController@updateFileDetails', 'ContentsController@getFileDetails', ['contentId' => isset($contentKey) ? $contentKey : null, 'id' => isset($file) ? $file->file_id : null])
        ->open();
    @endphp

    {!! Form::oneText('name', trans('form.name'), isset($file) ? $file->name : null, ['class' => 'form-control', 'id' => 'name']) !!}
    {!! Form::oneTextArea('description', trans('form.description'),isset($file) ? $file->description : null, ['class' => 'form-control', 'id' => 'description']) !!}

    {!! Form::hidden('file_id', isset($file) ? $file->file_id : null) !!}
    {!! Form::hidden('type_id', isset($file) ? $file->type_id : null) !!}

    {!! $form->make() !!}

@endsection
