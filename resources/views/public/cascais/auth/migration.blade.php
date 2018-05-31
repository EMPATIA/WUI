@php
    $pageSubTitle = ONE::transSite('migration_subtitle');
@endphp
@extends('public.default._layouts.index')

@section('header_styles')
    <style>
        .submit_register{
            text-align: center;
            padding: 5px 15px;
            line-height: 20px;
            display: block;
            width: 100%;
            cursor: pointer;
            background-color: #1d5ba6;
            border-radius: 0;
            color: #fff;
            font-size: 0.9rem;
            width: 100%;
        }
        .pointer-events-none{
            pointer-events: none;
            opacity: 0.4;
        }


        .submit-btn{
            width: 100%;
            background-color: {{ ONE::getSiteConfiguration("color_primary") }};
            text-decoration: none;
            color:white;
            border: 1px solid {{ ONE::getSiteConfiguration("color_primary") }};
            text-decoration: none;
        }

        .cancel-btn{
            width: 100%;
            background-color: #4c4c4c;
            text-decoration: none;
            color:white;
            border: 1px solid #4c4c4c;
            text-decoration: none;
        }

        .submit-btn:hover, .submit-btn:active, .submit-btn:focus{
            cursor: pointer;
            background-color: white;
            color: {{ ONE::getSiteConfiguration("color_primary") }};
        }

        .cancel-btn:hover, .cancel-btn:active, .cancel-btn:focus{
            cursor: pointer;
            background-color: white;
            color: #4c4c4c;
        }

        @media (max-width: 700px) {
            .submit-btn{
                margin-top: 7px;
            }
        }

    </style>
@endsection

@section('content')
    <div class="migrate-account-background">
        <div class="container">
            <br><br><br>
            <div class="row">
                <div class="col-12">
                    <!-- Title -->
                    <div class="row">
                        <div class="col-12 contents-header-title">
                            <h2>{{ ONE::transSite('migration_title') }}</h2>
                        </div>
                    </div>
                    <br><br>
                    <div class="row">
                        <div class="col-12 col-md-12 page-content-summary">
                            <p>{{ ONE::transSite('migration_content') }}</p>
                        </div>
                    </div>
                    <form action="{{ URL::action('AuthController@migrateUserToEntity') }}" method="POST">
                        <div class="row">
                            <div class="col-12" style="padding-top:30px;padding-bottom:30px;">
                                <div class="row">
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">

                                    <div class="col-12 col-sm-4">
                                        <button type="submit" name="response" value="0" class="cancel-btn">
                                            <span>{{ ONE::transSite('migration_decline') }}</span>
                                        </button>
                                    </div>
                                    <div class="col-12 col-sm-4">
                                        <button type="submit" name="response" value="1" class="submit-btn">
                                            <span>{{ ONE::transSite('migration_accept') }}</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection