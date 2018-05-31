@extends('public.default._layouts.index')

@section('content')

    {{--Topics list section--}}
    <section>
        <div class="container pads-container">
            <div class="row">
                <div class="col-xs-12">
                    <div class="pads-title">
                        <h3>{!! $cb->title ??  trans('defaultCbsForum.forum') !!}</h3>
                    </div>
                </div>
                <div class="col-xs-12">
                    <div class="pads-description">
                        <h3>{!! $cb->contents ?? trans('defaultCbsForum.forum_description') !!}</h3>
                    </div>
                </div>
            </div>
            <!-- If login is complete -->
            @if(isset($isModerator) && $isModerator == 1 || isset($configurations) && (ONE::checkCBsOption($configurations, 'CREATE-TOPIC') && ONE::isAuth()) || isset($configurations) &&  ONE::checkCBsOption($configurations, 'CREATE-TOPICS-ANONYMOUS'))
                <div class="row padding-box-default">
                    <div class="col-xs-12 text-right create-button-div">
                        <a href="{!! action('PublicTopicController@create', ['cbKey' => $cbKey, 'type' => $type]) !!}">{{ trans("defaultCbsForum.create_forum_topic") }}</a>
                    </div>
                </div>
            @endif

            <div class="row cbs-boxes" id="infinite-scroll">
                @include('public.default.cbs.forum.topicsPads')
                @if(empty($topics) || count($topics) == 0)
                    <div class="col-xs-12">
                        <div class="alertBoxGreen">
                            <div class="col-xs-12 text-center">
                                <h2>{{ trans('defaultCbsForum.no_forum_topics_to_display') }}</h2>
                            </div>
                            <div class="col-xs-12 text-center">
                                <i class="fa fa-exclamation-triangle" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </section>

@endsection
@section('scripts')
    <script>

        $('#infinite-scroll').jscroll({
            loadingHtml: '<div class="text-center"><img src="{{ asset('images/preloader.gif') }}" alt="Loading" /></div>',
            nextSelector: 'a.jscroll-next:last',
            callback: checkBoxes
        });
        <!-- Dot Dot Dot -->

        $.each([$(".topic-content"),$(".topic-title")], function( index, value ) {
            $(document).ready(function () {
                value.dotdotdot({
                    ellipsis: '... ',
                    wrap: 'word',
                    aft: null,
                });
            });
        });

        $(document).ready(function () {
            checkBoxes();
        });


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

        }
    </script>
@endsection
