@extends('private._private.index')

@section('content')

    @php
        $form = ONE::form('sms', trans('privateSms.details'), 'q', 'poll')
                ->settings(["model" => isset($sendSms) ? $sendSms : null])
                ->show(null, null, ['id' => isset($sendSms) ? $sendSms->key : null], 'SmsController@index')
                ->create('SmsController@store', 'SmsController@showSendedSms', ['id' => isset($sendSms) ? $sendSms->key : null])
                ->edit(null, 'SmsController@show', ['id' => isset($sendSms) ? $sendSms->key : null])
                ->open()
    @endphp

    <div class="box box-primary">
        <div class="row">
            <div class="col-md-12">
                <div class="box-body">
                    {!! Form::oneText('to', trans('privateSms.to'), isset($sendSms) ? $sendSms->to : null, ['class' => 'form-control bfh-phone', 'id' => 'to','required' => 'required']) !!}
                    {!! Form::oneTextArea('message', trans('privateSms.content'), isset($sendSms) ? $sendSms->content : null, ['class' => 'form-control', 'id' => 'message', 'rows' =>4]) !!}
                    <b id="smsCount"></b> {{ trans('privateSms.SMS') }} (<b id="smsLength"></b>) {{ trans('privateSms.to') }}
                    <h6><b> {{ trans('privateSms.note') }} </b> {{ trans('privateSms.maximum_of_160_characters_and_no_accent') }} </h6>
                </div>

            </div>
        </div>
    </div>
    {!! $form->make() !!}

@endsection


@section('scripts')

    {{--SCRIPT COUNT SMS--}}
    <script>
        //Plugin
        (function($){
            $.fn.smsArea = function(options){

                var
                    e = this,
                    cutStrLength = 0,

                    s = $.extend({

                        cut: true,
                        maxSmsNum: 3,
                        interval: 400,

                        counters: {
                            message: $('#smsCount'),
                            character: $('#smsLength')
                        },

                        lengths: {
                            ascii: [160, 306, 459],
                            unicode: [70, 134, 201]
                        }
                    }, options);


                e.keyup(function(){

                    clearTimeout(this.timeout);
                    this.timeout = setTimeout(function(){

                        var
                            smsType,
                            smsLength = 0,
                            smsCount = -1,
                            charsLeft = 0,
                            text = e.val(),
                            isUnicode = false;

                        for(var charPos = 0; charPos < text.length; charPos++){
                            switch(text[charPos]){
                                case "\n":
                                case "[":
                                case "]":
                                case "\\":
                                case "^":
                                case "{":
                                case "}":
                                case "|":
                                case "€":
                                    smsLength += 2;
                                    break;

                                default:
                                    smsLength += 1;
                            }

                            //!isUnicode && text.charCodeAt(charPos) > 127 && text[charPos] != "€" && (isUnicode = true)
                            if(text.charCodeAt(charPos) > 127 && text[charPos] != "€")
                                isUnicode = true;
                        }

                        if(isUnicode)   smsType = s.lengths.unicode;
                        else                smsType = s.lengths.ascii;

                        for(var sCount = 0; sCount < s.maxSmsNum; sCount++){

                            cutStrLength = smsType[sCount];
                            if(smsLength <= smsType[sCount]){

                                smsCount = sCount + 1;
                                charsLeft = smsType[sCount] - smsLength;
                                break
                            }
                        }

                        if(s.cut) e.val(text.substring(0, cutStrLength));
                        smsCount == -1 && (smsCount = s.maxSmsNum, charsLeft = 0);

                        s.counters.message.html(smsCount);
                        s.counters.character.html(charsLeft);

                    }, s.interval)
                }).keyup()
            }}(jQuery));


        //Start
        $(function(){
            $('#message').smsArea({maxSmsNum:10});
        })
    </script>
@endsection
