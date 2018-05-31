@extends('public.empatia._layouts.index')
@section('content')
    @include('public.empatia.home.banner')
    @include('public.empatia.home.notification')

    @php
        // Getting layout sections
        $layoutSections = App\Http\Controllers\PublicContentManagerController::getSections("empatia-homepage");
    @endphp

    <!-- MAIN -->
    <section>

        @foreach(!empty($layoutSections) ? $layoutSections : [] as $layoutSection)
            @if($layoutSection)
                @includeif("public." . ONE::getEntityLayout() . ".cms.sections." . $layoutSection->section_type->code, ['section' => $layoutSection])
            @endif
        @endforeach

{{--
            <div class="container-fluid no-padding green-blocks-container">
                <div class="row">
                    <div class="col-md-4 col-xs-12"><a href="/page/pages/publicParticipation">
                            <div class="block-border-green">
                                <div class="big-span" style="display: inline-block;">
                                    <h1 style="margin-top: 10px;">Participation</h1>
                                </div>
                            </div>
                        </a></div>
                    <div class="col-md-4 col-xs-12 no-padding-block"><a href="/page/pages/smartCity">
                            <div class="block-border-green">
                                <div class="big-span" style="display: inline-block;">
                                    <h1 style="margin-top: 10px;">Smart city</h1>
                                </div>
                            </div>
                        </a></div>
                    <div class="col-md-4 col-xs-12"><a href="/page/pages/ethics">
                            <div class="block-border-green">
                                <div class="big-span" style="display: inline-block;">
                                    <h1 style="margin-top: 10px;">Ethics</h1>
                                </div>
                            </div>
                        </a></div>
                </div>
            </div>
--}}


        <!--
        <div class="container-fluid no-padding green-blocks-container">
            {{--<div class="row aboutBanner-row">
                <div class="col-sm-8 col-sm-offset-2">
                    @if(!empty($homePageConfigurations) && property_exists($homePageConfigurations,'empatia_description'))
                        @foreach($homePageConfigurations->empatia_description as $description)
                            <div>
                                <p>{{(isset($description->empatia_description_text_area)? $description->empatia_description_text_area : null)}}</p>
                            </div>
                            <a href="{{ (isset($description->empatia_description_internal_link) ? url($description->empatia_description_internal_link) : null) }}" style="color: #8cc53f;"><span id="know-more-btn">{{trans('home.knowMore')}}</span></a>
                        @endforeach
                    @else
                        <a href="./" style="color: #8cc53f;"><span class="know-more-btn">{{trans('publicHome.why_use_empatia')}}</span></a>
                    @endif
                </div>
            </div>--}}
            <div class="container-fluid no-padding">
                <div class="row">
                    <div class="col-md-4 col-xs-12">
                        <a href="/page/pages/publicParticipation">
                            <div class="block-border-green">
                                <span class="big-span"><h1 style="margin-top: 10px;">{{trans('empatiaHome.public_participation')}}</h1></span>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-4 col-xs-12 no-padding-block">
                        <a href="/page/pages/smartCity">
                            <div class="block-border-green">
                                <span class="big-span"><h1 style="margin-top: 10px;">{{trans('empatiaHome.smart_city')}}</h1></span>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-4 col-xs-12">
                        <a href="/page/pages/ethics">
                            <div class="block-border-green">
                                <span class="big-span"><h1 style="margin-top: 10px;">{{trans('empatiaHome.ethics')}}</h1></span>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        -->
        {{--
      <div class="container-fluid no-padding-sides gradient-background try-our-demo-banner">

        </div>
        --}}

        <!--
        <section class="background-white" style="padding-bottom: 60px; margin-top: -15px">
            <div class="row menus-row margin-top-15 margin-bottom-15">
                <div class="menus-line col-sm-6 col-sm-offset-3"><span class="fa fa-tasks" style="color: #b3b3b3"></span> {{ trans("empatiaHome.pilots") }}</div>
            </div>
            <div id="my-list-pilots">
                <div id="my-list-pilots-error" class="hidden">{!! Html::oneMessageInfo(trans("empatiaHome.no_pilots_to_display") )!!}</div>
            </div>
            {{--<div class="container-fluid">
                <a href="{!! action('PublicTopicController@create', ['cbKey' => env("EMPATIA_HC_CB_KEY","nYPzSZ8YaLCe9OCdjvO37maqas941jCW"), 'type' => 'idea']) !!}" class="white-button-home special-font-size">{{trans('empatiaHome.ceome_one_entity')}}</a>
            </div>--}}
        </section>
        -->

    {{--
    @include('public.empatia.home.lastNews')
    --}}

    {{--
    @include('public.empatia.home.toolsRow')
    --}}


    </section>
    {{--@include('public.empatia.home.technology')
    @include('public.empatia.home.partners')
    @include('public.empatia.home.lastNews')
    @include('public.empatia.home.nextEvents')
    @include('public.empatia.home.goals')
    @include('public.empatia.home.testimonials')--}}
@endsection

@section('scripts')
    <script>
        /*
        $(document).ready(function () {
            $.ajax({
                method: 'POST', // Type of response and matches what we said in the route
                url: "{{action('PublicCbsController@getPilotsForHomePage')}}", // This is the url we gave in the route
                data: {
                    "_token": "{{ csrf_token() }}",
                    'cb_key': "{{ env("EMPATIA_HC_CB_KEY","nYPzSZ8YaLCe9OCdjvO37maqas941jCW") }}",
                    'type': "idea",
                    'filter': "commented",
                }, beforeSend: function () {
                    $("#my-list-pilots").append('<div class="col-md-12 col-xs-12 loader"><div class="text-center"><img src="{{ asset('images/preloader.gif') }}" alt="Loading"  style="width: 40px;"/></div></div>');
                    $(".loader").show();
                }, success: function (response) { // What to do if we succeed
                    $("#my-list-pilots").html(response);
                    $('.loader').remove();

                }, error: function () { // What to do if we succeed
                    $("#my-list-pilots").removeClass('hidden');
                    $('.loader').remove();

                }
            });
        });
        */



        $(document).ready(function () {
            // Setting equal heights for div's with jQuery
            if ($(window).width() > 767) {

                var highestBox = 0;
                $('.news-box-div').each(function () {
                    if ($(this).height() > highestBox) {
                        highestBox = $(this).height();
                    }
                });
                $('.news-box-div').height(highestBox);

                checkToolBoxes();

            }

            $.each([$(".news-title-box")], function (index, value) {
                $(document).ready(function () {
                    value.dotdotdot({
                        ellipsis: '... ',
                        wrap: 'word',
                        aft: null,
                        watch: 'window'
                    });
                });
            });


        });

        function checkToolBoxes(){
            $('.tool-border-green').removeAttr('style');
            if ($(window).width() > 767) {
                var highestBox = 0;
                $('.tool-border-green').each(function () {
                    if ($(this).height() > highestBox) {
                        highestBox = $(this).height();
                    }
                });
                $('.tool-border-green').height(highestBox);
            }
        }


        $(window).resize(function(){
            checkToolBoxes();
        });


    </script>
@endsection