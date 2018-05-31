@php
    $demoPageTitle = ONE::transSite("account_created");
@endphp

@extends('public.default._layouts.index')

@section('header_styles')
    <style>
        .no-padding{
            padding: 0;
        }
        .default-title,
        .idea-content .idea-title{
            color:  {{ Session::get("SITE-CONFIGURATION.color_primary") }};
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
            background-color:  {{ Session::get("SITE-CONFIGURATION.color_primary") }};
            padding:5px 30px;
            color:#fff;
        }

        .default-btn:hover{
            cursor: pointer;
            text-decoration: none;
            background-color: {{ Session::get("SITE-CONFIGURATION.color_secondary") }};
            color:#fff;
        }

        .background-image {
            min-height: 40vh;
            height: auto;
        }
        .user-profile{
            padding: 20px 0;
        }

        .default-btn {
            display: block;
            text-align: center;
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
                            <span>{{ ONE::transSite('account_created_subtitle') }}</span>
                        </div>
                    </div>
                </div>
                <div class="container">
                    <div class="row">
                        <div class="col-8 no-padding">
                            {{--  <h3 class="default-title">{!! ONE::transSite("account_created") !!}</h3>--}}
                            @if(Session::has('user'))
                                <p>{!! ONE::transSite("account_dear") !!} {{Session::get('user')->name}}
                                    ,</p>
                            @endif
                            <p class="default-text">{!! ONE::transSite("account_created_message") !!}</p>
                        </div>
                        <div class="col-12 col-md-6 no-padding">
                            <div class="row">
                                <div class="col-6">
                                    <a href="/" class="default-btn"><i class="fa fa-home" aria-hidden="true"></i> {!! ONE::transSite("account_go_home") !!}</a>
                                </div>
                                @if(ONE::siteConfigurationExists("active_cb"))
                                    <div class="col-6">
                                        <a href="{!! action('PublicTopicController@create', ['cbKey' => Session::get('SITE-CONFIGURATION.active_cb')]) !!}" class="default-btn"> {!! ONE::transSite("account_go_create_proposal") !!}</a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
@endsection