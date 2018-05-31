@extends('public.empaville_new._layouts.index')

@section('content')

    {{--Topics list section--}}
    <section>
        <div class="container pads-container">
            <div class="row">
                <div class="col-xs-12">
                    <div class="pads-title my-pads-title">
                        <h2>{!! $cb->title ??  trans('defaultCbsDiscussion.discussion') !!}</h2>
                    </div>
                </div>
                <div class="col-xs-12">
                    <div class="pads-description">
                        <h4>{!! $cb->contents ?? trans('defaultCbsDiscussion.discussion_description') !!}</h4>
                    </div>
                </div>
            </div>
            <!-- If login is complete -->
            @if(isset($isModerator) && $isModerator == 1 || isset($configurations) && (ONE::checkCBsOption($configurations, 'CREATE-TOPIC') && ONE::isAuth()) || isset($configurations) &&  ONE::checkCBsOption($configurations, 'CREATE-TOPICS-ANONYMOUS'))
                <div class="row padding-box-default">
                    <div class="col-xs-12 text-right create-button-div my-create-button-div">
                        <a href="{!! action('PublicTopicController@create', ['cbKey' => $cbKey, 'type' => $type]) !!}">{{ trans("defaultCbsDiscussion.create_discussion_topic") }}</a>
                    </div>
                </div>
            @endif

            <div class="row cbs-boxes" id="infinite-scroll">
                @include('public.empaville_new.cbs.discussion.topicsPads')
                @if(empty($topics) || count($topics) == 0)
                    <div class="col-xs-12">
                        <div class="alertBoxGreen">
                            <div class="col-xs-12 text-center">
                                <h2>{{ trans('defaultCbsDiscussion.no_discussion_topics_to_display') }}</h2>
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
