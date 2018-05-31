@extends('private._private.index')

@section('content')

    <div class="row">
        <div class="col-md-12">
            @php $form = ONE::form('sms', trans('privateSms.details'), 'wui', 'sms')
                ->settings(["model" => isset($sms) ? $sms->sms_key : null, 'id' => isset($sms) ? $sms->sms_key : null])
                ->show(null, null, null, 'SmsController@showSendedSms')
                ->create('SmsController@store','SmsController@index')
                ->edit(null, null)
                ->open();
            @endphp

            @php
                if ($sms->sent == 0){
                $answer = trans('privateSms.not_sent');
                }else{
                $answer = trans('privateSms.sent');
                }
            @endphp

            @if(ONE::actionType('sms') == 'show')
                {!! Form::oneText('recipient', trans('privateSms.recipient'), isset($sms) ? $sms->recipient : null, ['class' => 'form-control', 'id' => 'recipient', 'required']) !!}
                {!! Form::oneText('created_by', trans('privateSms.created_by'), isset($sms) ? $sms->created_by : null, ['class' => 'form-control', 'id' => 'created_by', 'required']) !!}
                {!! Form::oneText('created_at', trans('privateSms.created_at'), isset($sms) ? $sms->created_at : null, ['class' => 'form-control', 'id' => 'created_at']) !!}
                {!! Form::oneText('content', trans('privateSms.content'), isset($sms) ? $sms->content : null, ['class' => 'form-control', 'id' => 'content']) !!}
                {!! Form::oneTextArea('sent', trans('privateSms.sent'), isset($sms) ? $answer : null, ['class' => 'form-control', 'id' => 'sent']) !!}
            @endif

            {!! $form->make() !!}
        </div>
    </div>

@endsection
