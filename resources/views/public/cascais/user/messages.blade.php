{{--@extends('public.demo._layouts.index')--}}

<style>
    .new-message::placeholder {
        color:#c4c4c4;
    }

    .input-group-addon {
        padding: .5rem .75rem;
        margin-bottom: 0;
        font-size: 1rem;
        font-weight: 400;
        line-height: 1.25;
        color: #464a4c;
        text-align: center;
        background-color: #eceeef;
        border: 1px solid rgba(0,0,0,.15);
        border-radius: 0;
        padding: .5rem .75rem;
        display: flex;
        justify-content: center;
        flex-direction: column;
    }
</style>

<div class="container">
    <div class="row align-items-end idea-topic-title">
        <div clasS="col title">
            <span>{{ ONE::transSite("user_messages_subtitle") }}</span>
            {{--  <a href="#">{{ ONE::transSite("back') }}</a>  --}}
        </div>
    </div>
    {{--<div class="row">--}}
    {{--<div clasS="col no-padding idea-of-comments">--}}
    {{--<span>Idea: </span>--}}
    {{--<a href="#">Cras elit ipsum, lacinia nec tortor eu, faucibus malesuada lorem</a>--}}
    {{--</div>--}}
    {{--</div>--}}
</div>
@if(isset($messages) && !empty($messages))
    <div class="container light-grey-bg idea-comments">
        @foreach($messages as $message)
            <div class="row">
                <div class="col-lg-9 col-11 @if($message->from != $user->user_key) margin-left-auto @endif">
                    <div class="row comment-row">
                        @if($message->from == $user->user_key)

                            <div class="col-lg-3 col-md-4 col-sm-12 col-12 primary-color user-info">
                                <div class="row">
                                    <div class="col-lg-12 col-md-5 col-sm-3 col-4">
                                        @if (!empty(Session::get('user')->photo_id) && !empty(Session::get('user')->photo_code))
                                            <?php $userImgUrl = URL::action('FilesController@download',[Session::get('user')->photo_id, Session::get('user')->photo_code, 1]); ?>
                                        @else
                                            <?php $userImgUrl = "/images/cml/icon-user-default-160x160.png"; ?>
                                        @endif
                                        <div class="user-img" style="background-image: url('{!! $userImgUrl !!}')"></div>
                                    </div>
                                    <div class="col-lg-12 col-md-7 col-sm-9 col-8">
                                        <div class="user-name">
                                            @if(isset($message->from_username))
                                                {{$message->from_username}}
                                            @elseif(isset($user->name))
                                                {{$user->name}}
                                            @else
                                                {{ONE::transSite("user_messages_anonymous")}}
                                            @endif
                                        </div>
                                        <div class="time">
                                            {{$message->created_at}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-9 col-md-8 col-sm-12 col-12 med-grey-bg message">
                                {!! nl2br($message->value) !!}
                            </div>
                        @else
                            <div class="col-lg-9 col-md-8 col-sm-12 col-12 med-grey-bg message order-sm-2">
                                {!! nl2br($message->value) !!}
                            </div>
                            <div class="col-lg-3 col-md-4 col-sm-12 col-12 primary-color order-sm-1 user-info sent">
                                <div class="row">
                                    <div class="col-lg-12 col-md-5 col-sm-3 col-4">
                                        <div class="user-img" style="background-image: url('images/brunette-15963_640.jpg')"></div>
                                    </div>
                                    <div class="col-lg-12 col-md-7 col-sm-9 col-8">
                                        <div class="user-name">
                                            @if(isset($message->from_username))
                                                {{$message->from_username}}
                                            @else
                                                {{ONE::transSite("user_messages_anonymous")}}
                                            @endif
                                        </div>
                                        <div class="time">
                                            {{$message->created_at}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                </div>
            </div>
        @endforeach
    </div>
@else
    <div class="container no-padding">
        <div class="alert alert-info">
            <strong>{{ ONE::transSite('user_messages_info') }}</strong> {{ ONE::transSite("user_messages_there_are_no_messages") }}
        </div>
    </div>
@endif

<div class="container comments-input">
    <div class="row comment-row">
        <div class="col-12 no-padding">
            <div class="input-group">
                <textarea class="form-control new-message" id="exampleTextarea" rows="3" placeholder="{{ONE::transSite("user_messages_placeholder")}}"></textarea>
                <span href="#" style="padding: .5rem .75rem; color: #f0f0f0 !important;" class="input-group-addon send-message" id="basic-addon2"><i class="fa fa-paper-plane" aria-hidden="true"></i></span>
            </div>
        </div>
    </div>
</div>

@section('scripts')
    <script>
        $(document).on('click', '.send-message', function () {

            var value = $(".new-message").val();
            if (value.length < 1 || $.trim(value) === '') {
                return false; // keep form from submitting
            }

            $.ajax({
                method: 'POST', // Type of response and matches what we said in the route
                url: "{{action('PublicUsersController@sendMessage')}}", // This is the url we gave in the route
                data: {
                    "_token": "{{ csrf_token() }}",
                    'message': $('.new-message').val(),
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
    </script>
@endsection