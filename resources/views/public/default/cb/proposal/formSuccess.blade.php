@php
        $demoPageTitle = ONE::transCb("cb_submitted", !empty($cb) ? $cb->cb_key : $cbKey);
@endphp

@extends('public.default._layouts.index')

@section('header_styles')
    <style>
        .no-padding{
            padding: 0;
        }
        .default-title,
        .idea-content .idea-title{
            color: {{ ONE::getSiteConfiguration("color_primary") }}!important;
            font-size: 1.3rem;
            line-height: normal;
            font-weight: 600;
        }
        .default-text,
        .news-content .news-description,
        .idea-content .idea-description{
            margin-top: 30px;
            font-size: 0.9rem;
            line-height: normal;
            margin-bottom: 40px;
        }
        .default-btn{
            background-color: {{ ONE::getSiteConfiguration("color_primary") }}!important;
            padding:5px 30px;
            color:#fff;
        }

        .default-btn:hover{
            cursor: pointer;
            text-decoration: none;
            background-color: {{ ONE::getSiteConfiguration("color_secondary") }}!important;
            color:#fff;
        }

        .background-image {
            min-height: 40vh;
            height: auto;
        }
        .user-profile{
            padding: 20px 0;
        }

        .cancel-btn , .submit-btn {
            text-align: center;
            padding: 5px 15px;
            line-height: 20px;
            display: block;
            width: 100%;
        }

        .submit-btn {
            background-color: {{ ONE::getSiteConfiguration("color_primary") }};
            color: #fff;
        }

        .cancel-btn {
            background-color: #4c4c4c;
            color: #fff;
        }
        .submit-btn:hover {
            background-color: #fff;
            color: {{ ONE::getSiteConfiguration("color_primary") }};
            text-decoration: none;
            box-shadow: inset 0px 0px 0px 2px {{ ONE::getSiteConfiguration("color_primary") }};
        }

        .cancel-btn:hover {
            background-color: #383838;
            color: {{ ONE::getSiteConfiguration("color_secondary") }};
            text-decoration: none;
        }


    </style>
@endsection
@section('content')
    <div class="container-fluid user-profile">
        <div class="row">
            <div class="col-12">
                <div class="container">
                    <div class="row align-items-end idea-topic-title">
                        <div class="col title">
                            <span>{!! ONE::transCb('proposal_success_subtitle', !empty($cb) ? $cb->cb_key : $cbKey) !!}</span>
                        </div>
                    </div>
                </div>
                <div class="container">
                    <div class="row">
                        <div class="col-8 no-padding">
                            {{-- <h3 class="default-title">{!! trans("demoProposal.proposal_submitted") !!}</h3> --}}
                            @if(Session::has('user'))
                                <p>{!! ONE::transCb("cb_dear", !empty($cb) ? $cb->cb_key : $cbKey) !!} {{Session::get('user')->name}}
                                    ,</p>
                            @endif
                            <p class="default-text">{!! ONE::transCb("cb_submitted_message", !empty($cb) ? $cb->cb_key : $cbKey) !!}</p>
                        </div>
                        <div class="col-12 col-md-6 no-padding">
                            <div class="row">
                                <div class="col text-md-right">
                                    <a href="{!! action('PublicCbsController@show',['cbKey' => $cbKey, 'type' => $type])  !!}" class="default-btn">{!! ONE::transCb("cb_back", !empty($cb) ? $cb->cb_key : $cbKey) !!}</a>
                                </div>
                                <div class="col">
                                    <a href="{!! action('PublicTopicController@show',['cbKey' => $cbKey, 'topicKey' => $topicKey, 'type' => $type])  !!}" class="default-btn">{!! ONE::transCb("cb_go_to_my_idea", !empty($cb) ? $cb->cb_key : $cbKey) !!}</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
