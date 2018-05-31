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
    
    @if(env("DEMO_MODE",false)==true)
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal">
            {{trans("wizard.create")}}
        </button> 
    @endif

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

    
    <!-- Modal -->
    <div class="modal fade bd-example-modal-xl" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="exampleModalLabel">{{trans("wizard.title")}}</h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                @include('public.default.wizard.createEntity')
            </div>
            </div>
        </div>
    </div>

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