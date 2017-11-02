@extends('public.default._layouts.homeIndex')
@section('content')
    <!-- MAIN -->
    {{--<section>
        <div class="know-more-container">
            <div class="container">
                <div class="row">
                    <div class="col-md-9 col-xs-12">
                        <h4>{{trans('defaultHome.empatia_description')}}</h4>
                    </div>
                    <div class="col-md-3 col-xs-12 know-more-button-div">
                            <a href="https://demo.empatia-project.eu/content/fUMJSCC2s5keySE8j1jNXOk0Ww3P493u">{{trans('defaultHome.about')}}</a>
                    </div>
                </div>
            </div>
        </div>
    </section>--}}
    @include('public.default._layouts.registrationSection')


    {{--<div class="container">--}}
        {{--<div class="row">--}}
            {{--<div class="home-middle text-center">--}}
                {{--<h2 class="photoBannerTitle">{{trans("defaultHome.middle_page_title")}}</h2>--}}
                {{--<h4 class="photoBannerDescription">{{trans("defaultHome.middle_page_description")}}</h4>--}}
            {{--</div>--}}
        {{--</div>--}}
    {{--</div>--}}
    @include('public.default.home.lastNews')
{{--    @include('public.default.home.questionnaire')--}}
@endsection

@section('scripts')
    <script>
        $.each([$(".news-content-box")], function (index, value) {
            $(document).ready(function () {
                value.dotdotdot({
                    ellipsis: '... ',
                    wrap: 'word',
                    aft: null,
                    watch: 'window'
                });
            });
        });
    </script>
@endsection
