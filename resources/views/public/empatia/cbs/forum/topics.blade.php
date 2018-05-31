@extends('public.empatia._layouts.index')
@section('header_scripts')
    <!-- Maps -->
    <script  src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBJtyhsJJX_5DCp59m8sNsPlhHp8aQZHIE" type="text/javascript"></script>
@endsection
@section('content')

    <!-- Header -->
    <div class="container" style="padding-bottom: 50px">

        <div class="row menus-row">
            <div class="menus-line col-sm-6 col-sm-offset-3 mainTitleCb"><i class="fa fa-commenting"></i> {{$cb->title}}</div>
            <div style="clear:both;height:10px;"></div>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <div class='cbContents'>{{$cb->contents}}</div>
            </div>
        </div>
        @if(isset($isModerator) && $isModerator == 1 || isset($configurations) && (ONE::checkCBsOption($configurations, 'CREATE-TOPIC') && ONE::isAuth()) || isset($configurations) &&  ONE::checkCBsOption($configurations, 'CREATE-TOPICS-ANONYMOUS'))
            <div class="row">
                <div class="col-xs-12 text-right create-button-div my-create-button-div">
                    <a href="{!! action('PublicTopicController@create', ['cbKey'=> $cb->cb_key, 'type' => $type])  !!}">{{ trans("PublicCbs.create") }}</a>
                </div>
            </div>
        @endif
        <div class="row" id="infinite-scroll">
            @include('public.empatia.cbs.forum.topicsPads')
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
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            $("body").tooltip({ selector: '[data-toggle=tooltip]' });
        });
    </script>
    <script>

        $('.cbs-forum-box').click(function(){
            window.location = $(this).attr('data-href');
        });

        $('.cbs-forum-box a').click(function(){
            event.cancelBubble = true;
            if(event.stopPropagation) {
                event.stopPropagation();
            }
        });
    </script>
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