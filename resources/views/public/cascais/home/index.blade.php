<?php
$isHome=true;
$homeContentSections = \App\Http\Controllers\PublicContentManagerController::getSections("homepage");

$bannerLeft = collect($homeContentSections)->where('code', '=', 'html_banner_left_column')->first();
$bannerMid = collect($homeContentSections)->where('code', '=', 'html_banner_mid_column')->first();
$bannerRight = collect($homeContentSections)->where('code', '=', 'html_banner_right_column')->first();

$homePageItemSections = collect($homeContentSections)->where("section_type.code","=","homepageItemSection")->values();
?>
@extends('public.default._layouts.index')

@section('content')
    <div class="container-fluid">
        <div class="row primary-color">
            <div class="col-12 no-padding">
                <div class="container page-items-container">
                    <div class="row no-gutters">
                        @if(!empty($homePageItemSections[0]))
                            @include("public.default.sections.homePageItemSection",["section" => $homePageItemSections[0]])
                        @endif
                        @if(!empty($homePageItemSections[1]))
                            @include("public.default.sections.homePageItemSection",["section" => $homePageItemSections[1]])
                        @endif
                        @if(!empty($homePageItemSections[2]))
                            @include("public.default.sections.homePageItemSection",["section" => $homePageItemSections[2]])
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('public.default.home.lastNews')
    {{--  @include('public.demo.home.lastEvents')  --}}
    @if(Session::get("can-show-start-modal",true))
        @include("public.default.home.popup")
    @endif
@endsection

@section("scripts")
    <script>
        $(document).ready(function() {
            $.each([$(".news-group-wrapper .news-title-link")], function (index, value) {
                value.dotdotdot({
                    ellipsis: '... ',
                    wrap: 'word',
                    aft: null,
                    height: 70,
                    watch: "window"
                });
            });
        });
    </script>
@endsection