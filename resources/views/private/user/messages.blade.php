@extends('private._private.index')
@section('header_styles')
    <link href="{{ asset("css/jquery.scrollbar.css") }}" rel='stylesheet' type='text/css'>
@endsection
@section('content')
    <style>
        .bg-secundary{
            background: #ccc!important;
        }
        .message-author{
            font-size: 15px;
            text-transform: uppercase;
            font-weight: 700;
        }
        .color-secundary-message{
            color: #337ab7;
        }
        .color-primary{
            color: #b5b4b4;
        }
        .explanation-input{
            padding-left: 15px;
            font-weight: 600;
            margin-top: 20px;
        }
        .explanation-message{
            font-size: 17px;
            font-weight: 600;
            margin-top: 20px;
        }
        .height-400{
            height: 400px;
        }
        .topic-link{
            margin-top: 10px;
            font-weight: bold;
            text-align: right;
        }
        .userTopicSelect{
            margin-right: 15px;
        }
        .helper-text {
            font-size: 100%;
            font-weight: bold;
        }

    </style>
    <div class="row">
        <div class="col-md-12 col-12">
            @if(isset($messages) and count($messages) > 0)
                <p class="explanation-message">{{ trans("privateUsers.messages_explanation") }}</p>
                <div class="scrollbar-macosx height-400">
                    <div class="messages-content scrollbar-inner">
                        @foreach($messages as $message)
                            <div class="row margin-0" style="float:left; width:100%;">
                                <div class="col-md-9 col-9 pad-0 margin-top-bottom-10 {{ $message->from == $user_key ? 'pull-right' : 'pull-left' }}">
                                    @if($message->from == $user_key)
                                        <span class="message-author color-primary">{{ $message->from_username ?? trans("privateUsers.unknown_admin") }}</span>
                                    @else
                                        <span class="message-author color-secundary-message">{{ $message->from_username }}</span>
                                    @endif
                                    <div class="message {{ $message->from == $user_key ? 'my-message border-secundary' : 'other-message border-primary' }} "><span class="message-content">{!! $message->value !!}</span>
                                        {{--<span class="delete-message" data-message-key="{{ $message->message_key }}"><small><i class="fa fa-times"></i></small></span>--}}
                                        @if(isset($message->link))
                                            <div  class="topic-link">
                                                <a href="{{ action('TopicController@show',[$message->link->type, $message->link->cb_key, $message->link->topic_key]) }}" class="btn btn-sm btn-secondary btn-related-topic"><i class="fa fa-info-circle"></i> {{ trans('privateUsers.related_topic') }}</a>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="col-12 text-left message-date pad-0 {{ $message->from == $user_key ? 'bg-secundary' : 'bg-primary' }}">
                                        <small class="pull-left"><i class="fa fa-clock-o" aria-hidden="true"></i> {{ $message->created_at }}</small>
                                        @if($message->from != $user_key and $message->viewed)
                                            <small class="pull-right"><i class="fa fa-check" aria-hidden="true"></i></i> {{ trans("privateUsers.message_seen") }}</small>
                                        @endif
                                    </div>
                                </div>

                            </div>
                            @if($message->viewed == 1)
                                <div class="row" style="width:100%;">
                                    <div class="col-12">
                                        <button id="unseen" onclick="unseen('{{$message->message_key}}')" class="btn btn-secondary @if($message->from == $user_key) pull-right @endif">{{ trans('privateUsers.unseen_message') }}</button>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            @else
                <div class="alert alert-info alert-dismissable">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    {{ trans("privateUsers.there_are_no_messages") }}
                </div>
            @endif
            <div class="container-fluid">
                <div class="row margin-0" style="margin-bottom:35px; margin-top: 35px; ">
                    <div class="col-md-12 col-12" style="padding: 0;">
                        <span class="explanation-input">{{ trans("privateUsers.explanation_new_message") }}</span>
                        <textarea name="message" class="form-control new-message" placeholder="{{ trans("privateUsers.write_message_placeholder") }}" style="height: 150px"></textarea>
                    </div>
                    <div class="col-md-4 col-12">
                        <div class="helper-text">{{trans('privateUsers.notify_user_by_email')}}</div>
                        <div class="checkbox pull-left">
                            <label>
                                <input id="send-email" type="checkbox" value="send-email">
                                {{trans('privateUsers.send_email')}}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4 col-12 pad-0 text-center">
                        <div class="helper-text">{{trans('privateUsers.associate_topic_to_message')}}</div>
                        <select id="userTopicSelect" name="userTopicSelect" class="userTopicSelect pull-right">
                            <option value="">{{ trans('privateUsers.select_topic') }}</option>
                            @foreach($topics as $topic)
                                <option value="{!! $topic->topic_key !!}">{!! $topic->title !!}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 col-12 pad-0 text-right">
                        <a class="btn btn-flat empatia send-message pull-right">{{ trans("privateUsers.send_message") }}</a>
                        <div class="loader pull-right" style="display: none;padding-top: 13px;margin-right: 10px;"><img src="{{ asset('images/bluePreLoader.gif') }}" alt="Loading"  style="width: 20px;"/></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if((isset($cbKey) && !empty($cbKey)) && (isset($topicKey) && !empty($topicKey)) && (isset($type) && !empty($type)))
        <a href="{{ action('TopicController@show',['type' => $type,'cbKey' => $cbKey,'topicKey' => isset($topicKey) ? $topicKey : null]) }}" class="btn btn-flat empatia"><i class="fa fa-arrow-left"></i> {{ trans('form.back') }}</a>
    @else
        <a href="{{ action('UsersController@show',['userKey' => $user_key]) }}" class="btn btn-flat empatia"><i class="fa fa-arrow-left"></i> {{ trans('form.back') }}</a>
    @endif
@endsection


@section('scripts')
    <script type="text/javascript" src="{{ asset('js/jquery.scrollbar.js')}}"></script>
    <script>
        toastr.options = {
            "closeButton": true,
            "debug": false,
            "newestOnTop": false,
            "progressBar": false,
            "positionClass": "toast-bottom-right",
            "preventDuplicates": false,
            "onclick": null,
            "showDuration": "0",
            "hideDuration": "0",
            "timeOut": "0",
            "extendedTimeOut": "0",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        };

        $("#userTopicSelect").select2();

        $(document).on('click', '.send-message', function () {
            $.ajax({
                method: 'POST', // Type of response and matches what we said in the route
                url: "{{action('PublicUsersController@sendMessage')}}", // This is the url we gave in the route
                data: {
                    "_token": "{{ csrf_token() }}",
                    'message': $('.new-message').val(),
                    'to': "{{$user_key}}",
                    'send_email': $('#send-email').is(":checked"),
                    @if(isset($topicKey))
                    'topic_key': "{{$topicKey}}",
                    @else
                    'topic_key': $('#userTopicSelect').val(),
                    @endif
                }, beforeSend: function () {
                    $(".loader").show();

                }, success: function (response) {
                    $(".loader").hide();
                    if(response != 'error'){
                        location.reload();
                    }else{
                    }
                }
            });
        });

        $(document).ready(function () {
            $('.scrollbar-macosx').scrollbar();
            $(".scrollbar-macosx").animate({ scrollTop: $('.messages-content')[0].scrollHeight}, 300);

            $.ajax({
                url: '{{ URL::action('PublicUsersController@markMessagesAsSeen') }}',
                method: 'post',
                data: {
                    _token: "{{ csrf_token()}}",
                    from: "{{$user_key}}",
                }, success: function (response) {
                    console.log(response)
                }
            });
        });

        function unseen(key){
            $.ajax({
                url: '{{ URL::action('UsersController@markMessagesAsUnseen') }}',
                method: 'get',
                data: {
                    _token: "{{ csrf_token()}}",
                    from: "{{$user_key}}",
                    messageKey: key
                }, success: function (response) {
                    if(response=='success'){
                        toastr.success('{{ trans('privateUsers.marked_as_unseen') }}', '', {timeOut: 3000,positionClass: "toast-bottom-right"});
                        location.reload();
                    }
                    else
                        toastr.error('{{ trans('privateCbs.error_marking_as_unseen') }}', '', {timeOut: 3000,positionClass: "toast-bottom-right"});
                    console.log(response)
                }, error: function(jqXHR, textStatus, errorThrown){
                    toastr.error('{{ trans('privateCbs.error_marking_as_unseen') }}', '', {timeOut: 3000,positionClass: "toast-bottom-right"});
                }
            });
        }


    </script>
@endsection