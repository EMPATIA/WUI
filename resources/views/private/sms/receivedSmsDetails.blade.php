@extends('private._private.index')

@section('content')

    <div class="row">
        <div class="col-md-12">
            @php $form = ONE::form('sms', trans('privateSms.details'), 'wui', 'sms')
                ->settings(["model" => isset($sms) ? $sms->received_sms_key : null, 'id' => isset($sms) ? $sms->received_sms_key : null])
                ->show(null, null, null, 'SmsController@showReceivedSms')
                ->create('SmsController@store','SmsController@index')
                ->edit(null, null)
                ->open();
            @endphp

            @php
            if (strpos($sms->answer, 'O teu voto foi recebido')){
            $answer = trans('privateSms.received_vote');
            }elseif (strpos($sms->answer, 'Houve um erro com o teu voto.')){
            $answer = trans('privateSms.error_vote');
            }
            @endphp

            @if(ONE::actionType('sms') == 'show')
                {!! Form::oneText('date_hour', trans('privateSms.date_hour'), isset($sms) ? $sms->created_at : null, ['class' => 'form-control', 'id' => 'date_hour', 'required']) !!}
                {!! Form::oneText('mobile_number', trans('privateSms.mobile_number'), isset($sms) ? $sms->sender : null, ['class' => 'form-control', 'id' => 'mobile_number', 'required']) !!}
                {!! Form::oneText('text', trans('privateSms.text'), isset($sms) ? $sms->content : null, ['class' => 'form-control', 'id' => 'text']) !!}
                {!! Form::oneTextArea('status', trans('privateSms.status'), isset($sms) ? $answer : null, ['class' => 'form-control', 'id' => 'status']) !!}
            @endif

            {!! $form->make() !!}
        </div>
    </div>

@endsection
