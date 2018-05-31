
@extends('public.cascais._layouts.index')

@section('header_styles')
    <style>
        .primary-background-color{
            background-color: {{ ONE::getSiteConfiguration("color_primary") }}!important;
            color: white;
        }

        .your-votes{
            color:white;
            margin: 10px 0 20px 0;
        }

        .a-wrapper{
            background-color: white!important;
            color:{{ ONE::getSiteConfiguration("color_primary") }}!important;
        }

        .votes-confirm-title{
            box-sizing: border-box;
            color: rgb(56, 56, 56);
            font-size: 20px;
            font-weight: 600;
            line-height: 31px;
            text-transform: uppercase;
            margin: 30px 0 15px 0;
        }

        .title-description{
            color: rgb(56, 56, 56)!important;
            margin-bottom: 60px;
        }

        .link-back{
            font-size:12px;
            margin-left: 20px;
            text-transform:none;
            color: {{ ONE::getSiteConfiguration("color_primary") }}!important;
        }

        .voted-confirm-topic-wrapper{
            padding-top:40px;
            padding-bottom:40px;
        }

        .idea-details hr {
            background-color: {{ ONE::getSiteConfiguration("color_primary") }}!important;
            color: white!important;
        }

        .ideas-grid .idea-card {
            padding-right: 0px!important;
            padding-left: 0px!important;
            display: flex;
        }

        .ideas-grid .idea-card {
            display: block;
            flex-direction: column;
        }

        .ideas-grid .idea-card .idea-details {
            padding: 5px 15px;
            font-size: 0.8rem;
            margin-top: auto;
        }

        .ideas-grid .idea-card a.a-wrapper {
            display: block;
            flex: 1;
            flex-direction: column;
            height: 100%;
        }


        .button-back{
            background-color: #4c4c4c;
            color:white;
            border:1px solid #383838;
            padding:10px 30px;
            display: block;
            text-align: center;
            margin-left: 15px;
            text-decoration: none;
        }

        .button-back:hover, .button-back:active, .button-back:focus{
            color: {{ ONE::getSiteConfiguration("color_secondary") }};
            border:1px solid #383838;
            padding:10px 30px;
            display: block;
            text-decoration: none;
            box-shadow: inset 0px 0px 0px 2px {{ ONE::getSiteConfiguration("color_secondary") }}!important;

        }

        .default-btn{
            color: white;
            background-color: {{ ONE::getSiteConfiguration("color_primary") }};
            border:1px solid {{ ONE::getSiteConfiguration("color_primary") }};
            padding:10px 30px;
            display: block;
            text-align: center;
            margin-left: 15px;
            text-decoration: none;
            box-shadow: inset 0px 0px 0px 2px {{ ONE::getSiteConfiguration("color_primary") }}!important;
        }

        .default-btn:hover, .default-btn:active, .default-btn:focus{
            color: {{ ONE::getSiteConfiguration("color_primary") }};
            background-color:white;
            border:1px solid {{ ONE::getSiteConfiguration("color_primary") }};
            text-decoration: none;
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid white-ideas">
        <div class="row margin-top-50">
            <div class="col-12">
                <div class="container">
                    <div class="row">
                        <div class="col-12 no-padding-lg">
                            <p class="votes-confirm-title">
                                {{ ONE::transCb('vote_confirm_title', !empty($cb) ? $cb->cb_key : $cbKey)}}
                                <a href="{!! action('PublicCbsController@show', [$cb->cb_key, 'type' => $type]) !!}" class="link-back">
                                    {{ ONE::transCb('vote_confirm_back', !empty($cb) ? $cb->cb_key : $cbKey)}}
                                </a>
                            </p>
                        </div>
                        <div class="col-12 no-padding-lg">
                            <p class="title-description"> {{ ONE::transCb('vote_confirm_message', !empty($cb) ? $cb->cb_key : $cbKey)}}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid ideas-grid primary-background-color voted-confirm-topic-wrapper">
        <div class="primary-background-color">
            <div class="row margin-top-50">
                <div class="col-12">
                    <div class="container voting-endpage">
                        <div class="row" style="display: flex; flex-direction: row; /*height: 100%*/">
                            <div class="col-12 col-sm-12 col-md-10 col-lg-8 idea-title-box primary-color color-text-primary equalHW">
                                @foreach($topics as $topic)
                                    <a href="#{{--{!! action('PublicTopicController@show', [$cb->cb_key , $topic->topic_key, 'type' => $type] ) !!}--}}" style="pointer-events: none" class="a-wrapper">
                                        <div>
                                            <span class="idea-number">{{$topic->topic_number}}</span>
                                            <span class="idea-name">{{$topic->title}}</span>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="container no-padding">
        <div class="row mb-5 mt-5">
            <div class="col-12 col-sm-4 no-padding">
                <div class="container">
                    <div class="row">
                        <div class="col-12 no-padding">
                            <a href="{!! action('PublicCbsController@show', [$cb->cb_key, 'type' => $type]) !!}" class="button-back">
                                {{ ONE::transCb('vote_confirm_change_vote', !empty($cb) ? $cb->cb_key : $cbKey)}}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-4 no-padding">
                <div class="container">
                    <div class="row">
                        <div class="col-12 no-padding">
                            <a href="#" id="submitVotes" class="default-btn">
                                {{ ONE::transCb('vote_confirm_ending_vote', !empty($cb) ? $cb->cb_key : $cbKey)}}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            $("#spinner").hide();
        });
        $("#submitVotes").click(function(event) {
            event.preventDefault();
            $("#submitVotes").addClass("disabled");
            $.ajax({
                method: 'POST', // Type of response and matches what we said in the route
                url: '{{action("PublicCbsController@genericSubmitVotes")}}', // This is the url we gave in the route
                data: {
                    eventKey: "{{ $voteKey }}",
                    _token:  '{{csrf_token()}}',
                }, // a JSON object to send back
                beforeSend:function(){
                    $(this).addClass('vote-not-login');
                },
                success: function (response) { // What to do if we succeed
                    $("#spinner").hide();
                    $("#check").show();
                    $(this).removeClass('vote-not-login');
                    if (response.hasOwnProperty("success")){
                        console.log("sucesso");
                        {{--@if (isset($voteType) && collect($voteType)->where("genericConfigurations.boolean_show_confirmation_view","=",1)->count()>0)--}}
                            window.location.href = "{{ action("PublicCbsController@votesSubmittedSuccessfuly",["cbKey"=>$cbKey,"type"=>$type]) }}";
                        {{--@else--}}

                        {{--@endif--}}
                    }else {
                        $("#submitVotes").removeClass("disabled");

                    }
                },
                error: function (jqXHR, textStatus, errorThrown) { // What to do if we fail
                }
            });
        });
    </script>
@endsection