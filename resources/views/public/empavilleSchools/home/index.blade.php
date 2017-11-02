@extends('public.empavilleSchools._layouts.index')
@section('content')
    <!-- MAIN -->
    <section>
        <div class="know-more-container">
            <div class="container">
                <div class="row">
                    <div class="col-md-9 col-xs-12">
                        <h4>{{trans('empavilleSchoolsHome.empatia_description')}}</h4>
                    </div>
                    <div class="col-md-3 col-xs-12 know-more-button-div">
                            <a href="https://empaville.org/content/IqEgtaq5N4FOWsNSaboAco1nbvqfQvFa">{{trans('empavilleSchoolsHome.about')}}</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
{{--    @include('public.default.home.questionnaire')--}}
    @include('public.empavilleSchools.home.lastNews')

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
