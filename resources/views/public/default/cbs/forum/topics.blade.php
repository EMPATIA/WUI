@extends('public.default._layouts.index')

@section('content')

    {{--Topics list section--}}
    <section>
        <div class="container pads-container">
            <div class="row">
                <div class="col-xs-12">
                    <div class="pads-title my-pads-title">
                        <h2 class="bolder">{!! $cb->title ??  trans('defaultCbsForum.forum') !!}</h2>
                    </div>
                </div>
                <div class="col-xs-12">
                    <div class="pads-description">
                        <h4>{!! $cb->contents ?? trans('defaultCbsForum.forum_description') !!}</h4>
                    </div>
                </div>
            </div>
            <!-- If login is complete -->
            @if(isset($isModerator) && $isModerator == 1 || isset($configurations) && (ONE::checkCBsOption($configurations, 'CREATE-TOPIC') && ONE::isAuth()) || isset($configurations) &&  ONE::checkCBsOption($configurations, 'CREATE-TOPICS-ANONYMOUS'))
                <div class="row padding-box-default">
                    <div class="col-xs-12 text-right create-button-div my-create-button-div">
                        <a href="{!! action('PublicTopicController@create', ['cbKey' => $cbKey, 'type' => $type]) !!}">{{ trans("defaultCbsForum.create_forum_topic") }}</a>
                    </div>
                </div>
            @endif

            <div class="row cbs-boxes" id="infinite-scroll">
                <div class="text-center" id="first-loader"><img src="{{ asset('images/bluePreLoader.gif') }}" alt="Loading" style="width: 40px; margin-bottom:40px;"/></div>
            </div>
        </div>
    </section>

@endsection
@section('scripts')
    <script>
        $(document).ready(function () {
            dataToSend = {
                "ajax_call": true,
                "topics_to_show": 6
            };
            $.ajax({
                url: "{{ action('PublicCbsController@show',["cbKey"=> $cb->cb_key, "type"=> $type]) }}",
                type: "get", //send it through get method
                data: dataToSend,
                success: function (response) {
                    $("#first-loader").remove();
                    $("#infinite-scroll").html(response);
                    $('#infinite-scroll').jscroll({
                        loadingHtml: '<div class="text-center"><img src="{{ asset('images/bluePreLoader.gif') }}" alt="Loading" style="width: 40px;"/></div>',
                        loadingHtml: '<div class="text-center"><img src="{{ asset('images/bluePreLoader.gif') }}" alt="Loading" style="width: 40px;"/></div>',
                        nextSelector: 'a.jscroll-next:last',
                        callback: checkBoxes
                    });
                    checkBoxes();

                },
                error: function (xhr) {

                }
            });
        });
        <!-- Dot Dot Dot -->
        function checkBoxes(){
            $(document).ready(function () {
                // Setting equal heights for div's with jQuery
                $('.row-eq-height').each(function () {
                    var height = $('.row-eq-height #box-topic-contents').height();
                    $('.row-eq-height .news-inner-img-div').height(height);
                });

                <!-- Dot Dot Dot -->
                $.each([$(".topic-content"), $(".topic-title")], function (index, value) {
                    $(document).ready(function () {
                        value.dotdotdot({
                            ellipsis: '... ',
                            wrap: 'word',
                            aft: null,
                        });
                    });
                });
                $(window).trigger('resize.px.parallax');
            });

            $.each([$(".topic-content"),$(".topic-title")], function( index, value ) {
                $(document).ready(function () {
                    value.dotdotdot({
                        ellipsis: '... ',
                        wrap: 'word',
                        aft: null,
                    });
                });
            });
        }
    </script>
@endsection
