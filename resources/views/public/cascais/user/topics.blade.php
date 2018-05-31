@extends('public.default._layouts.index')

@section('content')
    <div class="container-fluid personal-area-buttons">
        <div class="row">
            <div class="col-12">
                <div class="container">
                    <div class="row no-margin">
                        <div class="offset-6 col-6 no-padding">
                            <div class="row buttons-margin">
                                <div class="col button">
                                    <a href="{{ action("PublicUsersController@edit",["userKey"=>$user->user_key]) }}" @if(!isset($profileSection) || (isset($profileSection) && $profileSection=="about")) class="active" @endif>
                                        {{ ONE::transSite("user_topic_profile") }}
                                    </a>
                                </div>
                                <div class="col button">
                                    <a href="{{ action("PublicUsersController@showMessages") }}" @if(!isset($profileSection) || (isset($profileSection) && $profileSection=="messages")) class="active" @endif>
                                        {{ ONE::transSite("user_topic_messages") }}
                                    </a>
                                </div>
                                <div class="col button">
                                    <a href="{{ action("PublicUsersController@userTopics") }}" @if(!isset($profileSection) || (isset($profileSection) && $profileSection=="topics")) class="active" @endif>
                                        {{ ONE::transSite("user_topic_participation") }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid user-activity-padding">
        <div class="row">
            <div class="col-12">
                <div class="container">
                    <div class="row align-items-end idea-topic-title">
                        <div clasS="col title">
                            <span>{{ONE::transSite("user_participation_title")}}</span>
                        </div>
                    </div>
                </div>
            </div>
            {{--<div class="topics-background">--}}
            {{--<div class="row margin-0" style="width:100%">--}}
            {{--<div class="col-4"></div>--}}
            {{--<div class="col-4 min-height-100">--}}
            {{--<h1 class="header-messages">{{ ONE::transSite("my_proposals") }}</h1>--}}
            {{--</div>--}}
            {{--<div class="col-4"></div>--}}
            {{--</div>--}}

            <div class="col-12">
                <div class="container no-padding user-activity-tabs" style="margin-top: 20px;">
                    <div class="activity-table" style="padding: 0">
                        <table class="table table-sm table-hover"  id="infinite-scroll">
                            <thead>
                            <tr data-href='url://link-for-first-row/'>
                                <th class="title">{{ONE::transSite("user_topic_table_title")}}</th>
                                <th>{{ONE::transSite("user_topic_table_category")}}</th>
                                <th>{{ONE::transSite("user_topic_table_creation")}}</th>
                                {{--<th>Thematic area</th>--}}
                                {{--<th>Date</th>--}}
                                {{--<th>Votes</th>--}}
                            </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                        {{--<div class="row news-container" id="infinite-scroll" style="margin-top: 40px">--}}
                        {{--<div class="col-12">--}}
                        {{--<div class="text-center" style="margin-top:25px;text-align: center!important" id="first-loader"><em class="fa fa-circle-o-notch fa-spin" style="font-size:24px"></em></div>--}}
                        {{--</div>--}}
                        {{--</div>--}}
                    </div>
                </div>
            </div>

        </div>
    </div>
    {{--</div>--}}
@endsection

@section('scripts')
    <script>
        $(function(){
            $('td').click(function(){
                alert(1);
//                window.location = $(this).parent().data('href');
            });
        });

        $('table').delegate('tr','click',function() {
            window.location = $(this).data('href');
            return false;

        });
        $(document).ready(function () {
            dataToSend = {
                "ajax_call": true,
                "topics_to_show": 100,
                "sort_order": "order_by_random"
            };
            $.ajax({
                url: "{{ action('PublicUsersController@userTopics') }}",
                type: "get", //send it through get method
                data: dataToSend,
                success: function (response) {


                    $("#first-loader").remove();

                    $("#infinite-scroll").append(response);


                    $('#infinite-scroll').jscroll({
                        loadingHtml: '<tr><td><div class="text-center"><i class="fa fa-circle-o-notch fa-spin" style="font-size:24px"></i></div></td></tr>',
                        nextSelector: 'a.jscroll-next',
                        debug: true,
                        autoTrigger: true,
                        callback: function() {


                            checkBoxes(response);

                        }
                    });

                    checkBoxes();

                },
                error: function (xhr) {

                }
            });
        });

        function checkBoxes(response) {

            $(document).ready(function () {
                setTimeout(function () {
                    $('.jscroll-added').children().unwrap();
//                    $('.jscroll-inner').children().unwrap();
                }, 10);

                $(".jscroll-loading").hide();
                // Setting equal heights for div's with jQuery
                <!-- Dot Dot Dot -->
                $.each([$(".topic-content"), $(".topic-title"), $(".topic-map-parameter-name"), $(".topic-category"), $(".parameters-topic")], function (index, value) {
                    $(document).ready(function () {
                        value.dotdotdot({
                            ellipsis: '... ',
                            wrap: 'word',
                            aft: null,
                            watch: true
                        });
                    });
                });

                $(window).trigger('resize.px.parallax');

            });
        }
    </script>
@endsection