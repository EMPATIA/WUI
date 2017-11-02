@extends('public.empavilleSchools._layouts.index')

@section('content')
<br/>
<div class="container">

    <!-- Title -->
    <div class="row">
        <div class="col-xs-12 contents-header-title">
            <h2 class="contents-list-header-title">{!! trans('defaultPages.news')  !!}</h2>
        </div>
    </div>
    <!-- News List -->
    <div id="infinite-scroll" class="row">
        @include('public.default.pages.newsListContent')
    </div>

</div>
    <div class="bottom-buffer"></div>

@endsection

@section('scripts')
    <script>
        /* Dot Dot Dot */
        $.each([$(".ideaTitle")], function (index, value) {
            $(document).ready(function () {
                value.dotdotdot({
                    watch: "window",
                    ellipsis: '... ',
                    wrap: 'word',
                    aft: null
                });
            });
        });

        $('#infinite-scroll').jscroll({
            loadingHtml: '<div class="text-center"><img src="{{ asset('images/preloader.gif') }}" alt="Loading" /></div>',
            nextSelector: 'a.jscroll-next:last',
            callback: checkBoxes
        });

        function checkBoxes(){

            $.each([$(".ideaTitle")], function (index, value) {
                $(document).ready(function () {
                    value.dotdotdot({
                        watch: "window",
                        ellipsis: '... ',
                        wrap: 'word',
                        aft: null
                    });
                });
            });

            //corrects parallax behaviour
            $(window).trigger('resize.px.parallax');

        };
    </script>
@endsection