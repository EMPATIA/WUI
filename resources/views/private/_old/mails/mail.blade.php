@extends('private._private.index')

@section('content')
 
     <?php $form = ONE::form('mails')
        ->settings(["model" => isset($mail) ? $mail : null])
        ->show('MailsController@edit', 'MailsController@delete', ['id' => isset($mail) ? $mail->id : null], 'MailsController@index', ['id' => isset($mail) ? $mail->id : null])
        ->create('MailsController@store', 'MailsController@index', ['id' => isset($mail) ? $mail->id : null])
        ->edit('MailsController@update', 'MailsController@show', ['id' => isset($mail) ? $mail->id : null])
        ->open();
    ?>
    
    {!! Form::oneText('subject', trans('form.subject'), isset($mail) ? $mail->subject : null, ['class' => 'form-control', 'id' => 'subject']) !!}
    {!! Form::oneTextArea('body', trans('form.body'), isset($mail) ? $mail->body : null, ['class' => 'form-control', 'id' => 'body']) !!}
    {!! Form::oneText('tag', trans('form.tag'), isset($mail) ? $mail->tag : null, ['class' => 'form-control', 'id' => 'tag']) !!}
    
    {!! $form->make() !!}
     
@endsection

