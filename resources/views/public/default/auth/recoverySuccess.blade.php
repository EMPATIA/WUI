@php
    $demoPageTitle = ONE::transSite("recovery_email_confirmed") ;
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
    </style>
@endsection

@section('content')
    <div class="container-fluid user-profile">
        <div class="row">
            <div class="col-12">
                <div class="container">
                    <div class="row align-items-end idea-topic-title">
                        <div class="col title">
                            <span>{{ ONE::transSite('recovery_email_subtitle') }}</span>
                        </div>
                    </div>
                </div>
                <div class="container">
                    <div class="row">
                        <div class="col-8 no-padding">
                            {{--  <h3 class="default-title">{!! ONE::transSite("recovery_email_confirmed") !!}</h3>--}}
                            @if(Session::has('user'))
                                <p>{!! ONE::transSite("recovery_dear") !!} {{Session::get('user')->name}}
                                    ,</p>
                            @endif
                            <p class="default-text">{!! ONE::transSite("recovery_email_confirmed_message") !!}</p>

                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection