@extends('public.empavilleSchools._layouts.index')
@section('header_scripts')
    <!-- Maps -->
    <script   src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCjgiI5l8FanufeE3GRchTZSVOaAyzVIE8" type="text/javascript"></script>
@endsection
@section('content')

    {{--Topics list section--}}
    <section>
        <div class="container pads-container">
            <div class="row">
                <div class="col-xs-12">
                    <div class="pads-title">
                        <h3>{!! $cb->title ?? trans('empavilleSchoolsPadsIdea.idea') !!}</h3>
                    </div>
                </div>
                <div class="col-xs-12">
                    <div class="pads-description">
                        <h4>{!! $cb->contents ?? trans('empavilleSchoolsPadsIdea.idea_description') !!}</h4>
                    </div>
                </div>
            </div>
            <!-- If login is complete -->
            @if(isset($isModerator) && $isModerator == 1 || isset($configurations) && (ONE::checkCBsOption($configurations, 'CREATE-TOPIC') && ONE::isAuth()) || isset($configurations) &&  ONE::checkCBsOption($configurations, 'CREATE-TOPICS-ANONYMOUS'))
                <div class="row padding-box-default">
                    <div class="col-xs-12 text-right create-button-div">
                        <a href="{!! action('PublicTopicController@create', ['cbKey' => $cbKey, 'type' => $type]) !!}">{{ trans("empavilleSchoolsPadsIdea.create_idea_topic") }}</a>
                    </div>
                </div>
            @endif

            <div class="row cbs-boxes topics-grid" id="infinite-scroll">
                @include('public.empavilleSchools.cbs.idea.topicsPads')
                @if(empty($topics) || count($topics) == 0)
                    <div class="col-xs-12">
                        <div class="alertBoxGreen">
                            <div class="col-xs-12 text-center">
                                <h2>{{ trans('empavilleSchoolsPadsIdea.no_idea_topics_to_display') }}</h2>
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

        $(document).ready(function () {
            checkBoxes();
        });

        function checkBoxes(){
            $(document).ready(function () {
                // Setting equal heights for div's with jQuery
                var highestBox = 0;
                $('.topic-contents').each(function () {
                    if ($(this).height() > highestBox) {
                        highestBox = $(this).height();
                    }
                });

                $('.topic-contents').height(highestBox);
                <!-- Dot Dot Dot -->
                $.each([$(".topic-content"), $(".topic-title")], function (index, value) {
                    $(document).ready(function () {
                        value.dotdotdot({
                            ellipsis: '... ',
                            wrap: 'word',
                            aft: null,
                            watch: true,
                        });
                    });
                });

                $(window).trigger('resize.px.parallax');
            });
        }
    </script>
@endsection
