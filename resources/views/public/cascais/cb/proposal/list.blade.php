<?php
$demoPageTitle = ONE::transCb('cb_list_title', !empty($cb) ? $cb->cb_key : $cbKey);

$content = App\Http\Controllers\PublicContentManagerController::getSections($cb->cb_key);
if(!empty($content)){
    $titleSection = collect($content)->where('section_type.code', '=', 'headingSection')->first();
    if(!empty($titleSection))
        $title = collect($titleSection->section_parameters)->where('section_type_parameter.code', '=', 'textParameter')->first()->value;

    $descriptionSection = collect($content)->where('section_type.code', '=', 'contentSection')->first();
    if(!empty($descriptionSection))
        $description = collect($descriptionSection->section_parameters)->first()->value;
}
?>
@extends('public.default._layouts.index')

@section('header_scripts')
    <script src="https://maps.googleapis.com/maps/api/js?key={{ Session::get("SITE-CONFIGURATION.maps_api_key") ?? "AIzaSyBJtyhsJJX_5DCp59m8sNsPlhHp8aQZHIE" }}"></script>
@endsection

@section('header_styles')
    <style>
        .filter:hover:not(.active), .filter:active:not(.active){
            text-decoration: none;
            background-color: #313131 !important;
        }

        .filter:focus{
            text-decoration: none;
        }

        .jscroll-inner, .jscroll-added  {
            margin-right: -15px;
            margin-left: -15px;
        }
        .jscroll-loading{
            width:100%;
        }

        .ideas-grid .idea-card{
            padding-right: 0px!important;
            padding-left: 0px!important;
            display:flex;
        }

        .jscroll-inner, .jscroll-added {
            display: -webkit-box;
            display: -webkit-flex;
            display: -ms-flexbox;
            display: flex;
            -webkit-flex-wrap: wrap;
            -ms-flex-wrap: wrap;
            flex-wrap: wrap;
            margin-right: 0px;
            margin-left: 0px;
            width:100%;
        }

        .jscroll-inner col{
            padding-right: 0!important;
            padding-left: 0!important;
        }

        #my-map{
            height: auto!important;
        }
        .active, .active:hover, .active:active, .active:focus{
            background-color: {{ ONE::getSiteConfiguration("color_primary") }}!important;
            text-decoration: none;
        }

        .not-active{
            background: blue!important;
            color: white!important;
        }
        .ideas-grid.white-ideas .idea-card a.a-wrapper:hover .title{color:#fff!important}
        .ideas-grid.white-ideas .idea-card a.a-wrapper:hover .idea-details hr{background-color:#fff!important}
        .news-title,
        .idea-topic-title{
            align-items: flex-end;
        }


        .ideas-grid.white-ideas .idea-card a.a-wrapper:hover .description{color:#fff!important}

        .ideas-grid.white-ideas .idea-card a.a-wrapper:hover .ideas-grid.white-ideas .idea-card{
            opacity: 0.1;
        }

        .ideas-grid.white-ideas .idea-card a.a-wrapper{
            flex:1;
        }

        .news-title .title,
        .idea-topic-title .title{
            min-width: 50%;
        }

        .idea-topic-title .title {
            padding: 30px 0px 15px 0px;
        }

        .news-title .title span,
        .idea-topic-title .title span{
            text-transform: uppercase;
            font-size: 1.3rem;
            font-weight: 600;
        }

        .news-title .title a,
        .idea-topic-title .title a{
            color: {{ Session::get("SITE-CONFIGURATION.color_primary") }};
            font-size: 1.1rem;
            margin-left: 3px;
            font-weight: 600;
        }

        .news-title .title a:hover,
        .idea-topic-title .title a:hover {
            text-decoration: none;
            color: #383838;
        }

        .idea-topic-title .ideas-nav-buttons{
            background-color: #f4f4f4;
        }

        .idea-topic-title .ideas-nav-buttons>.nav-left,
        .idea-topic-title .ideas-nav-buttons>.nav-right{
            text-align: center;
            flex-basis: 70px;
            padding: 30px 0px 7px 0px;
            background-color: #f4f4f4;
            font-size: 1.3rem;
        }

        .idea-topic-title .ideas-nav-buttons>.nav-left a{
            color: #383838;
        }

        .idea-topic-title .ideas-nav-buttons>.nav-right a{
            color: {{ Session::get("SITE-CONFIGURATION.color_primary") }};
        }

        .idea-topic-title .ideas-nav-buttons>.nav-left:hover,
        .idea-topic-title .ideas-nav-buttons>.nav-right:hover{
            cursor: pointer;
        }

        .idea-topic-title .ideas-nav-buttons>.nav-left:hover{
            background-color: #383838;
        }

        .idea-topic-title .ideas-nav-buttons>.nav-right:hover{
            background-color: {{ Session::get("SITE-CONFIGURATION.color_primary") }};
        }

        .idea-topic-title .ideas-nav-buttons>.nav-left:hover a,
        .idea-topic-title .ideas-nav-buttons>.nav-right:hover a{
            color:#fff;
        }

        .idea-topic-title .ideas-nav-buttons>.col:hover a>i.fa{
            color: #fff;
        }

        .idea-topic-title .ideas-edit>.col,
        .idea-topic-title .ideas-delete>.col{
            padding:3px 7px;
            text-align: center;
        }

        .idea-topic-title .ideas-edit>.col:hover,
        .idea-topic-title .ideas-delete>.col:hover{
            cursor: pointer;
        }

        .idea-topic-title .ideas-edit>.col a,
        .idea-topic-title .ideas-delete>.col a{
            color: #fff;
        }

        .idea-topic-title .ideas-edit>.col{
            background-color: {{ Session::get("SITE-CONFIGURATION.color_primary") }};
        }

        .idea-topic-title .ideas-edit>.col:hover{
            background-color: #fff;
            color: {{ Session::get("SITE-CONFIGURATION.color_primary") }};
        }

        .idea-topic-title .ideas-edit>.col:hover a{
            color: {{ Session::get("SITE-CONFIGURATION.color_primary") }};
        }

        .idea-topic-title .ideas-delete>.col{
            background-color: #4c4c4c;
        }

        .idea-topic-title .ideas-delete>.col:hover{
            color:#fff;
            background-color: {{ Session::get("SITE-CONFIGURATION.color_secondary") }};
        }


        .idea-topic-title .ideas-delete,
        .idea-topic-title .ideas-edit {
            color: #fff;
            font-size: 0.9rem;
        }

        .idea-content{
            margin-top:60px;
            padding-bottom: 30px;
        }

        .idea-content .col-details {
            order: 2;
        }

        .idea-content .col-text{
            order:1;
        }

        @media (min-width: 768px) {
            .idea-content .col-details {
                order: 1;
                margin-top:0px;
            }

            .idea-content .col-text {
                order: 2;
            }
        }

        .idea-content .idea-title{
            color: {{ Session::get("SITE-CONFIGURATION.color_primary") }};
            font-size: 1.3rem;
            line-height: normal;
            font-weight: 600;
        }

        .news-content .news-summary,
        .idea-content .idea-summary{
            font-size: 0.9rem;
            font-weight: 600;
            line-height: normal;
        }

        .idea-content .idea-summary {
            margin-top: 30px;
        }


        .news-content .news-description,
        .idea-content .idea-description{
            margin-top: 30px;
            font-size: 0.9rem;
            line-height: normal;
            margin-bottom: 40px;
        }

        .news-content .news-description .see-more-btn,
        .idea-content .idea-description .see-more-btn {
            color: {{ Session::get("SITE-CONFIGURATION.color_primary") }};
        }

        .news-content .news-description .see-more-btn:hover,
        .idea-content .idea-description .see-more-btn:hover {
            color: #383838;
            text-decoration: none;
        }

        .idea-content .files{
            margin-bottom: 30px;
        }

        .idea-content .files .file-row{
            margin-bottom: 5px;
        }

        .idea-content .files .file-row .file-download-btn{
            color: {{ Session::get("SITE-CONFIGURATION.color_primary") }};
            margin-left: 10px;
        }

        .idea-content .files .file-row .file-download-btn:hover{
            color: #383838;
            text-decoration: none;
        }

        .idea-content .text-highlights{
            padding-bottom:15px;
        }

        .idea-content .text-highlights:first-child{
            padding:30px 0;
        }

        .idea-content .text-highlights .title{
            color: #383838;
            font-size: 1.1rem;
            font-weight: 600;
        }

        .idea-content .idea-image{
            height: 150px;
            background-size: cover;
        }

        .idea-content .idea-status{
            background-color: #d6c12c;
            padding: 5px 0;
        }

        .idea-content .idea-status .status-label{
            color: #383838;
        }

        .idea-content .detail-row{
            margin-bottom: 10px;
        }

        .idea-content .details-title{
            color: #fff;
            font-size: 1rem;
            font-weight: 600;
            margin: 0 0 15px 0;
            text-align: right;
        }

        .idea-content .idea-details{
            padding: 30px 15px;
        }

        .idea-content .idea-status .status-label,
        .idea-content .idea-details .detail-label{
            text-transform: uppercase;
            font-size: 0.75rem;
            font-weight: 600;
            text-align: right;
            line-height: 20px;
        }

        .idea-content .idea-details .detail-label{
            color: {{ Session::get("SITE-CONFIGURATION.color_primary") }};
        }

        .idea-content .idea-status .status-info,
        .idea-content .idea-details .detail-info{
            color: #fff;
            font-size: 0.85rem;
            line-height: 20px;
            font-weight: 300;
        }

        .idea-content .idea-details .detail-info.detail-big{
            font-size: 1.1rem;
            font-weight: 600;
        }

        .idea-details-buttons .buttons-row .button-dislike,
        .idea-details-buttons .buttons-row .button-like{
            color: #fff;
            padding: 7px 15px;
            text-align: center;
        }


        .idea-details-buttons .buttons-row .button-dislike{
            background-color: {{ Session::get("SITE-CONFIGURATION.color_secondary") }};

        }

        .idea-details-buttons .buttons-row .button-like{
            background-color: {{ Session::get("SITE-CONFIGURATION.color_primary") }};
        }

        .idea-details-buttons .buttons-row .button-dislike a,
        .idea-details-buttons .buttons-row .button-like a{
            color: #fff;
        }

        .idea-details-buttons .buttons-row .button-dislike a:hover,
        .idea-details-buttons .buttons-row .button-like a:hover{
            color: #fff;
            text-decoration: none;
        }

        .idea-details-buttons .buttons-row .button-dislike:hover,
        .idea-details-buttons .buttons-row .button-like:hover{
            background-color: #fff;
            cursor: pointer;
        }

        .idea-details-buttons .buttons-row .button-dislike:hover a,
        .idea-details-buttons .buttons-row .button-like:hover a{
            color: {{ Session::get("SITE-CONFIGURATION.color_primary") }};
            text-decoration: none;
        }

        .idea-details-buttons .buttons-row .button-dislike .fa,
        .idea-details-buttons .buttons-row .button-like .fa{
            font-size: 30px;
        }

        .idea-details-buttons .buttons-row .button-dislike span,
        .idea-details-buttons .buttons-row .button-like span{
            font-size: 0.8rem;
            text-transform: uppercase;
            font-weight: 300;
            margin-left: 7px;
            line-height: 30px;
        }

        .social-buttons{
            display: flex;
            padding:0;
        }

        @media (min-width: 992px) {
            .social-buttons{
                padding: 0 15px;
            }
        }

        .social-buttons .follow-btn,
        .social-buttons .share-social-btn{
            background-color: #fff;
            color: {{ Session::get("SITE-CONFIGURATION.color_primary") }};
            margin: 5px 0;
            padding: 0 25px;
        }

        .social-buttons .share-social-btn {
            align-items: flex-end;
            margin-left: 10px;
        }

        .social-buttons .follow-btn a,
        .social-buttons .share-social-btn a{
            line-height: 40px;
            vertical-align: middle;
            display: block;
            color: {{ Session::get("SITE-CONFIGURATION.color_primary") }};
        }

        .social-buttons .follow-btn:hover,
        .social-buttons .share-social-btn:hover{
            background-color: {{ Session::get("SITE-CONFIGURATION.color_primary") }};
        }

        .social-buttons .follow-btn:hover a,
        .social-buttons .share-social-btn:hover a{
            color: #fff;
        }

        .social-buttons .follow-btn a:hover,
        .social-buttons .share-social-btn a:hover{
            text-decoration: none;
            color:#fff;
        }

        .idea-comments{
            background-color: #f4f4f4;
            padding:40px 0 80px 0;
        }

        .idea-content>.row{
            flex-wrap: wrap;
        }

        .idea-comments .comments-title{
            text-transform: uppercase;
            color: {{ Session::get("SITE-CONFIGURATION.color_primary") }};
            text-align: center;
            font-size: 1.3rem;
            margin: 30px auto;
        }

        .idea-comments .comments-title span{
            color: #383838;
            font-size: 1.1rem;
        }

        .idea-comments .comment-row{
            padding: 15px 0;
        }

        .idea-comments .user-info{
            text-align: center;
            color:#fff;
            padding:15px;
            flex:1;
            flex-basis: 200px;
        }

        .idea-comments .user-info .user-name{
            font-size: 1.2rem;
            font-weight: 500;
            line-height: normal;
            margin-top: 10px;
        }

        .idea-comments .user-info .time{
            font-weight: 300;
        }


        .idea-comments .user-info .user-img{
            min-height: 80px;
            max-height: 100px;
            max-width: 80px;
            background-size: cover;
            margin:auto;
        }

        .idea-comments .message{
            flex:2;
            padding: 10px 15px;
            color: #fff;
            font-weight: 300;
            flex-basis: 300px;
        }

        .idea-comments .input-group.comments-input textarea{
            background-color: #4c4c4c;
            color: #fff;
            border:none;
            border-radius: 0;
            padding: 1rem 0.75rem;
        }

        .idea-comments .input-group.comments-input span.input-group-addon{
            background-color: {{ Session::get("SITE-CONFIGURATION.color_primary") }};
            color: #fff;
            border-radius: 0;
            border:none;
        }

        .idea-comments .input-group.comments-input span.input-group-addon:hover{
            background-color: #fff;
            color: {{ Session::get("SITE-CONFIGURATION.color_primary") }};
            cursor: pointer;
        }

        .map-container .map-btn{
            border-color: transparent transparent {{ ONE::getSiteConfiguration("color_primary") }}!important;
        }

        .grid-view{
            margin-top: 55px;
        }


        .like-vote-button-voted{
            background-color: {{ Session::get("SITE-CONFIGURATION.color_secondary") }};
        }

        .ideas-grid .idea-card .vote-container .vote-button a:hover {
            background-color: #4c4c4c;
            text-decoration: none;
        }

        .ideas-grid {
            padding-top:0px;
        }

        .ideas-grid>.row>.col-12 {
            margin-top: 0px;
        }


        .topicFilter:not(:first-child){
            margin-top: 25px;
        }

        .search-box>.input-group{
            margin-top:15px;
            padding:0 5px;
        }

        @media (min-width: 768px){
            .search-box>.input-group{
                margin-top:5px;
            }
        }
    </style>

    <style>
        .points {
            color: #383838;
            font-weight: 600;
            text-align: center;
            line-height: 50px;
        }

        .button-like,
        .button-dislike{
            text-align: center;
            padding: 0;
        }

        .button-like{
            background-color: {{ ONE::getSiteConfiguration("color_primary") }};
            color: #fff !important;
        }

        .button-dislike{
            background-color: {{ ONE::getSiteConfiguration("color_secondary") }};
            color: #fff !important;
        }

        .button-like a,
        .button-dislike a{
            padding: 10px 10px;
            color: #fff;
            line-height: 30px;
            vertical-align: middle;
            display: block;
            text-decoration: none;
        }

        .button-like a:hover,
        .button-dislike a:hover{
            background-color: #fff;
            color: {{ ONE::getSiteConfiguration("color_secondary") }} !important;
            cursor: pointer;
            text-decoration: none;
        }

        .ideas-grid .idea-card .vote-container .button-like i.fa, .ideas-grid .idea-card .vote-container .button-dislike i.fa {
            font-size: 0.8rem;
        }

        .button-like:hover{
            color: {{ ONE::getSiteConfiguration("color_secondary") }}!important;
            background-color: #c4c4c4!important;
        }
        .create-proposal-banner{
            background-color: {{ ONE::getSiteConfiguration("color_primary") }};
        }

        .create-proposal-banner .submit-idea-btn{
            background-color: #fff !important;
            color: {{ ONE::getSiteConfiguration("color_primary") }}!important;
            text-transform: uppercase;
            font-size: 1.2rem;
            padding:10px 30px;
        }

        .create-proposal-banner .submit-idea-btn:hover{
            color: #fff!important;
        }
    </style>
@endsection

<style>
    .red{
        background: #F7606F;
    }
</style>

@section('content')
    <div class="container">
        <div class="row ideas-list-tile">
            <div clasS="col title">
                <span>
                    @if(!empty($title)) {!! $title !!} @else {!! $cb->title !!} @endif
                </span>
            </div>
            <?php $possibilities = \App\Http\Controllers\PublicTopicController::userCanCreateTopic($configurations,$moderators,$cb,$type);?>


        </div>
        @if(!empty($description))
            <div class="row">
                <div class="col-12" style="margin-bottom: 40px">
                    {!! $description !!}
                </div>
            </div>
        @endif
    </div>
    <div class="">
        <div class="row med-grey-bg no-gutters">
            <div clasS="col-12">
                @if(!empty($parameters))
                    @foreach($parameters as $parameter)
                        @if($parameter['code'] == 'google_maps')
                            <div class="map-container">
                                <div class="no-padding" style="padding: 0">
                                    <div class="collapse" id="collapseExample">
                                        <div class="card card-block map" id="map" >

                                        </div>
                                    </div>
                                    <button class="map-btn"  type="button" data-toggle="collapse" data-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
                                        <a data-toggle="collapse" href="#collapseExample" aria-expanded="false" aria-controls="collapseExample" style="font-size:14px;">
                                            <i class="fa fa-map-marker" aria-hidden="true"></i>
                                            <p>{{ONE::transCb('cb_list_map', !empty($cb) ? $cb->cb_key : $cbKey)}}</p>
                                        </a>
                                    </button>
                                </div>
                            </div>
                        @endif
                    @endforeach
                @endif
                <div class="container">
                    <div class="row">
                        <div class="col-12 ideas-filters">
                            <div class="row filters-row">
                                <div class="col-lg-8 col-md-7 col-sm-12 col-12">
                                    @foreach($parameters as $parameter)
                                        @if($parameter['filter'])
                                            <div class="topicFilter">

                                                <div class="">
                                                    <div class="filters-labels">
                                                        <label>
                                                            <span href="#" class="default-button-topics-label">
                                                                <span class="light-blue">{{$parameter['name']}}</span>
                                                            </span>
                                                        </label>
                                                    </div>

                                                    <div class="filters">
                                                        @foreach($parameter['options'] as $option)
                                                            <a href="#" class="filter my-filter-selector" data-parameter-id="{{$parameter["id"]}}"
                                                               data-option-id="{{$option["id"]}}"><span class="dark-blue" style="margin-top:5px;">{{$option['name']}}</span></a>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>

                                        @endif
                                    @endforeach
                                </div>

                                <div class="col-12 order-1 col-sm-12 col-md-5 col-lg-3 search-box">
                                    <div class="input-group">
                                        <input type="text" class="form-control searchTopics" placeholder="{{--{{ONE::getStatusTranslation($translations, 'search')}}--}}{{ONE::transCb('cb_list_search', !empty($cb) ? $cb->cb_key : $cbKey)}}">
                                        <span class="input-group-btn">
                                            <button class="search-tbn" type="button">
                                                <i class="fa fa-search" aria-hidden="true"></i>
                                            </button>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="row filters-row topicStatus">
                                @php
                                    $showStatusTypesFilters = false;
                                    foreach($statusTypes as $statusCode => $statusName){
                                        if ($statusCode=="approved" || $statusCode=="not_accepted"){
                                            $showStatusTypesFilters = true;
                                        }
                                    }
                                @endphp
                                @if(!empty($statusTypes) && $showStatusTypesFilters == true )
                                    <div class="col-10">
                                        <div class="row">
                                            <div class="filters-labels">
                                                <label>
                                                    <span href="#" class="default-button-topics-label">
                                                        <span class="light-blue">{{ONE::transCb('cb_status', !empty($cb) ? $cb->cb_key : $cbKey)}}</span>
                                                    </span>
                                                </label>
                                            </div>
                                            <div class="filters">
                                                @foreach($statusTypes as $statusCode => $statusName)
                                                    @if ($statusCode=="approved" || $statusCode=="not_accepted")
                                                        <a href="#" class="filter my-filter-selector @if($statusCode=="approved") active @endif" data-option-id="{{ $statusCode }}">
                                                            <span class="dark-blue" style="margin-top:5px;">{{ $statusName }}</span>
                                                        </a>
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            {{--<div class="row filters-row">--}}
                            {{--<div class="col-12 order-by">--}}
                            {{--<div class="row">--}}
                            {{--<div class="filters-labels">--}}
                            {{--<span href="#" class="default-button-topics-label">--}}
                            {{--<span class="light-blue">{{ ONE::transCb('proposal_opjpPadsProposal.order_by') }}</span>--}}
                            {{--</span>--}}
                            {{--</div>--}}

                            {{--<div class="col-sm-7 order-3 col-md-2 filters">--}}
                            {{--<a href="#rand" class="order-filter filter  my-filter-selector" data-filter="order_by_random">--}}
                            {{--<span class="dark-blue">{{ ONE::transCb('proposal_opjpPadsProposal.order_by_random') }}</span>--}}
                            {{--</a>--}}
                            {{--</div>--}}
                            {{--<div class="col-sm-7 order-3 col-md-2 filters">--}}
                            {{--<a href="#rand" class="order-filter filter order-filter filters active my-filter-selector" data-filter="order_by_topic_number">--}}
                            {{--<span class="dark-blue">{{ ONE::transCb('proposal_opjpPadsProposal.number_of_topic') }}</span>--}}
                            {{--</a>--}}
                            {{--</div>--}}
                            {{--</div>--}}

                            {{--</div>--}}
                            {{--</div>--}}

                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div class="container-fluid ideas-grid white-ideas">
        <div class="row" style="/*height: 100%*/">
            <div class="col-12" style="/*height: 100%*/">
                <div class="grid-view">
                    <div class="container" style="/*height: 100%*/">
                        <div class="row no-gutters" id="infinite-scroll" style="display: flex; flex-direction: row; /*height: 100%*/">

                            @section('topics')

                                <div class="text-center" style="margin-top:25px;text-align: center!important" id="first-loader"><em class="fa fa-circle-o-notch fa-spin" style="font-size:24px"></em></div>





                            @endsection
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{--<div class="grid-view">--}}
        @yield('topics')
        {{--</div>--}}
    </div>
@endsection

@section('scripts')
    <script>

        $(document).ready(function () {
            loadMap();
            $("#map").hide();

            dataToSend = {
                "ajax_call": true,
                "topics_to_show": 12,
                "sort_order": "order_by_random"
            };

            dataToSend = jQuery.extend(dataToSend, getSelectedFilters());

            $.ajax({
                url: "{{ action('PublicCbsController@show',["cbKey"=> $cb->cb_key]) }}",
                type: "get", //send it through get method
                data: dataToSend,
                success: function (response) {
//                    $("#infinite-scroll").remove();
//                    $(".grid-view").append('<div class="container"><div id="infinite-scroll" class="row no-gutters"></div></div>');
                    $("#first-loader").remove();
                    $("#infinite-scroll").html(response);
                    $('.jscroll-inner').children().unwrap();

                    $('#infinite-scroll').jscroll({
                        loadingHtml: '<div class="col-12"><div class="text-center" style="margin-top:25px;text-align: center!important" id="first-loader"><em class="fa fa-circle-o-notch fa-spin" style="font-size:24px"></em></div> </div>',
                        nextSelector: 'a.jscroll-next:last',
                        callback: function() {
                            $(".ideas-grid .idea-card").css('padding-right', '0');
                            $(".ideas-grid .idea-card").css('padding-left', '0');
                            checkBoxes();

                        }
                    });
                    checkBoxes();

                    $('.jscroll-inner,.jscroll-added').children().unwrap();

                },
                error: function (xhr) {

                }
            });
        });

        function checkBoxes() {

            $(document).ready(function () {
                setTimeout(function(){ $('.jscroll-added').children().unwrap();  }, 1);


                // Setting equal heights for div's with jQuery

                <!-- Dot Dot Dot -->
                $.each([$(".description"), $(".title"), $(".topic-map-parameter-name"), $(".detail")], function (index, value) {
                    $(document).ready(function () {
                        value.dotdotdot({
                            ellipsis: '... ',
                            wrap: 'word',
                            aft: null,
                            watch: true
                        });
                    });
                });


                $(window).trigger('resize.px.parallax');
            });
        }

        var delay = (function(){
            var timer = 0;
            return function(callback, ms){
                clearTimeout (timer);
                timer = setTimeout(callback, ms);
            };
        })();

        $(document).on('mouseover', "a.a-wrapper", function(){
            $(this).find(".idea-image").css('opacity', '1');
        })

        $(document).on('mouseout', "a.a-wrapper", function(){
            $(this).css('opacity', '1');
        })

        $(document).on('click', '.my-filter-selector', function () {
            if($(this).hasClass('active')){
                $(this).removeClass('active')
            }else{
                $(this).parent().parent().parent().find("a.active").removeClass('active');
                $(this).addClass('active');
            }

            reloadTopics();

        });

        $(document).on('keyup', '.searchTopics', function () {
            delay(function(){
                reloadTopics();
            }, 2000 );

        })

        function getSelectedFilters() {
            var filters = [];
            // topicFilter parameters
            var divWithParameters = $('.topicFilter');
            var activeFilters = divWithParameters.find('a.active');
            for (var i = 0; i < activeFilters.length; i++) {
                if($(activeFilters[i]).hasClass('active')){
                    var myObject = new Object();
                    myObject.parameter_id = $(activeFilters[i]).attr('data-parameter-id');
                    myObject.option_id = $(activeFilters[i]).attr('data-option-id');
                    filters["filter_" + myObject.parameter_id] = myObject.option_id;
                }
            }
            // topicFilter status
            var divWithParameters = $('.topicStatus');
            var activeFilters = divWithParameters.find('a.active');
            for (var i = 0; i < activeFilters.length; i++) {
                if($(activeFilters[i]).hasClass('active')){
                    var myObject = new Object();
                    myObject.parameter_id = $(activeFilters[i]).attr('data-parameter-id');
                    myObject.option_id = $(activeFilters[i]).attr('data-option-id');
                    filters["status"] = myObject.option_id;
                }
            }
            return filters;
        }

        var currentRequest = null;

        function reloadTopics() {
            $(".grid-view").html("");
            $("#infinite-scroll").remove();
            $(".grid-view").append('<div class="container"><div id="infinite-scroll" class="row"></div></div>');

            dataToSend = {
                "ajax_call": true,
                "sort_order": "order_by_random",
                "search": $('.searchTopics').val(),
                "topics_to_show": 12,
            };

            dataToSend = jQuery.extend(dataToSend, getSelectedFilters());

            $("#infinite-scroll").html('<div class="col-12"><div class="text-center" style="margin-top:25px;text-align: center!important" id="first-loader"><em class="fa fa-circle-o-notch fa-spin" style="font-size:24px"></em></div> </div>');



            currentRequest =
                $.ajax({
                    url: "{{ action('PublicCbsController@show',["cbKey"=> $cb->cb_key, "type"=> $type]) }}",
                    type: "get", //send it through get method
                    data: dataToSend,
                    success: function (response) {
                        $("#infinite-scroll").remove();
                        $(".grid-view").append('<div class="container"><div id="infinite-scroll" class="row no-gutters"></div></div>');
                        $("#infinite-scroll").html(response);
                        $('#infinite-scroll').jscroll({
                            loadingHtml: '<div class="col-12"><div class="text-center" style="margin-top:25px;text-align: center!important" id="first-loader"><em class="fa fa-circle-o-notch fa-spin" style="font-size:24px"></em></div> </div>',
                            nextSelector: 'a.jscroll-next:last',
                            callback: function() {
//                                $('.jscroll-inner,.jscroll-added').children().unwrap();
                                checkBoxes();
                            }
                        });
                        checkBoxes();
                    },
                    error: function (xhr) {
                        console.log(xhr);
                    }
                });

        }

        $(".map-btn").on('click', function(){
            $("#map").show();
        })

        function loadMap() {
            $.ajax({
                method: 'POST', // Type of response and matches what we said in the route
                url: "{{action('PublicCbsController@getCbTopicsListMap')}}", // This is the url we gave in the route
                data: {
                    "_token": "{{ csrf_token() }}",
                    'cb_key': "{{ $cbKey }}",
                    'type': "{{$type}}",
                }, beforeSend: function () {
                    // $("#map").append('<div class="col-12"><div class="center-loader"><i class="fa fa-circle-o-notch fa-spin fa-2x fa-fw default-color"></i><span class="sr-only">Loading...</span></div>');
                    $(".loader").show();
                }, success: function (response) {
                    $(".loader").hide();
                    $("#map")
                        .css("min-height","300px")
                        .html(response);
                }
            });
        }


    </script>
@endsection