@extends('private._private.index')

@section('content')

    <div class="row">
        <div class="col-md-12">
            @php $form = ONE::form('sms', trans('privateSms.details'), 'wui', 'sms')
                ->settings(["model" => isset($sms) ? $sms->sms_key : null, 'id' => isset($sms) ? $sms->sms_key : null])
                ->show(null, null, null, 'SmsController@index')
                ->create('SmsController@store','SmsController@index')
                ->edit(null, null)
                ->open();
            @endphp

            @if(ONE::actionType('sms') == 'show')
                {!! Form::oneText('recipient', trans('privateSms.recipient'), isset($sms) ? $sms->recipient : null, ['class' => 'form-control', 'id' => 'recipient', 'required']) !!}
                {!! Form::oneText('created_by', trans('privateSms.created_by'), isset($sms) ? $sms->created_by : null, ['class' => 'form-control', 'id' => 'created_by', 'required']) !!}
                {!! Form::oneText('created_at', trans('privateSms.created_at'), isset($sms) ? $sms->created_at : null, ['class' => 'form-control', 'id' => 'created_at']) !!}
                {!! Form::oneTextArea('content', trans('privateSms.content'), isset($sms) ? $sms->content : null, ['class' => 'form-control', 'id' => 'content']) !!}
            @endif

            {!! Form::oneTextArea('to', trans('form.to'), isset($sms) ? $sms->recipient : null, ['class' => 'form-control', 'id' => 'to', 'required']) !!}

            <!-- {!! Form::oneText('subject', trans('form.subject'), isset($sms) ? $sms->recipient : null, ['class' => 'form-control', 'id' => 'subject']) !!} -->

            {!! Form::oneTextArea('message', trans('form.message'), isset($sms) ? $sms->content : null, ['class' => 'form-control', 'id' => 'message', 'required']) !!}


            {!! $form->make() !!}
        </div>
    </div>

@endsection
