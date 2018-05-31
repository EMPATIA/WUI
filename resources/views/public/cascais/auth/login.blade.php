@extends('public.default._layouts.loginLayout')
@section('header_styles')
    <style>
        html{
            height: 100%;
        }

        .login-bg .login-row .login-box .footer-buttons .login-btn button{
            cursor: pointer;
        }
        .login-bg .login-row .login-box .footer-buttons .login-btn button{
            cursor: pointer;

        }

        .login-bg .login-row .login-box .footer-buttons .login-btn button:hover{
            background: {{ONE::getSiteConfiguration("color_secondary") }}!important;
            color: white!important;
        }

        .footer-buttons .login_line_between{
            background-color: white;
            height: 2px;
            margin-top: 25px;
            margin-bottom: 20px;
        }

        .login-bg .login-row .login-box .footer-buttons .signIn-btn a, .login-bg .login-row .login-box .footer-buttons .signIn-btn button{
            border: none;
            padding: 0;
            width: auto;
        }

        .login-bg .login-row .login-box .footer-buttons .signIn-btn a:hover, .login-bg .login-row .login-box .footer-buttons .signIn-btn button:hover{
            background-color: {{ ONE::getSiteConfiguration("color_primary") }}!important;
            color: {{ ONE::getSiteConfiguration("color_secondary") }};
        }

        .login-bg .login-row .login-box .social-login-btns a{
            color: {{ ONE::getSiteConfiguration("color_primary") }}!important;
            border: none;
        }

        .login-bg .login-row .login-box .social-login-btns a:hover{
            background-color: {{ ONE::getSiteConfiguration("color_secondary") }};
            color: white !important;
        }

        .login-bg .login-row .login-box .login-form .forgot-password a:hover{
            color: {{ ONE::getSiteConfiguration("color_secondary") }};
        }

        .login-bg{
            background-image: url({{ ONE::getSiteConfiguration("file_background_login") ."?w=1250" }})!important ;
        }

        .login-bg .login-row .login-box .login-form .input-warning{
            color: white;
        }

        .login-bg .login-row .login-box .login-form .input-warning a{
            color: white;
        }

        .modal-footer .cancel-btn:hover{
            background-color: #c4c4c4 !important;
            max-height: 800px;
        }

        .modal-body{
            padding: 2rem 30px 0rem 30px;
            max-height: 600px;
            overflow-y: auto;
        }

        .modal{
            overflow-y: auto;
            overflow-x: hidden;
        }

        .modal-dialog{
            width: 80%;
            max-width: 900px;
        }

        .modal-footer{
            justify-content: flex-end;
        }

        .accept-terms{
            padding: 10px;
            background: {{ ONE::getSiteConfiguration("color_primary") }}!important;
            color: white;
        }

        .accept-terms:hover{
            text-decoration: none;
            background: {{ ONE::getSiteConfiguration("color_secondary") }}!important;
        }
    </style>
@endsection

@section("content")
    {{--<!-- Messages -->--}}
    {{--@if(old('auth.wait.registration') == '1')--}}
        {{--<div class="auth-my-message">--}}
            {{--<div class="row">--}}
                {{--<div class="col-12 col-sm-12 col-md-4 col-lg-4 color-green login-label">--}}

                {{--</div>--}}
                {{--<div class="col-12 col-sm-12 col-md-8 col-lg-8">--}}
                    {{--<div class="form-group has-feedback">--}}
                        {{--{{trans('opjpAuth.confirmation_email_check')}}--}}
                    {{--</div>--}}
                {{--</div>--}}

            {{--</div>--}}
        {{--</div>--}}
    {{--@endif--}}

    {{--@if($errors->count()>0)--}}
        {{--<div class="auth-my-message">--}}
            {{--<div class="row">--}}
                {{--<div class="col-12 col-sm-12 col-md-4 col-lg-4 color-green login-label">--}}

                {{--</div>--}}
                {{--<div class="col-12 col-sm-12 col-md-8 col-lg-8">--}}
                    {{--<div class="form-group has-feedback">--}}
                        {{--<br>--}}
                        {{--{{ ONE::transSite("login_authentication_error") }} <a href="{{ action('AuthController@recovery') }}" class="color-green click_here">--}}
                            {{--{{ ONE::transSite("login_click_here") }}--}}
                        {{--</a>--}}
                    {{--</div>--}}
                {{--</div>--}}

            {{--</div>--}}
        {{--</div>--}}

    {{--@else--}}
        {{--<br><br>--}}
    {{--@endif--}}

    <!-- Login -->
    <div class="container-fluid login-bg">
        <div class="row login-row">
            <div class="col-12 col-sm-11 col-md-10 col-lg-5 login-box">
                <div class="login-close">

                </div>
                <div class="login-title">
                    {{ ONE::transSite("login_title") }}
                </div>
                <div class="login-description-text">
                    {{ ONE::transSite("login_title_description") }}
                </div>
                
                @if(!empty($errors->all()))
                <div class="login-form">
                    <div class="input-warning">
                        @foreach ($errors->all() as $error)
                            <div style="padding: 3px 0;"> <i class="fa fa-exclamation-triangle" aria-hidden="true"></i> {{ ONE::transSite($error) }}</div>
                        @endforeach
                    </div>
                </div>
                @endif

                @if($facebook_login || $googleAuthMethod)
                    <div class="row social-login-btns">
                        @if($facebook_login)
                            <div class="@if($googleAuthMethod) col-sm-6 @else col-12 @endif">
                                <a data-toggle="modal" data-target="#modal_terms"><i class="fa fa-facebook" aria-hidden="true"></i> {{ ONE::transSite("login_facebook") }}</a>
                                {{--                                <a href="{{ URL::action('AuthSocialNetworkController@redirectToFacebook') }}"><i class="fa fa-facebook" aria-hidden="true"></i> {{ ONE::transSite("login_facebook") }}</a>--}}
                            </div>
                        @endif
                        @if($googleAuthMethod)
                            <div class="@if($facebook_login) col-sm-6 @else col-12 @endif">
                                <a href="#"><i class="fa fa-google-plus" aria-hidden="true"></i> {{ ONE::transSite("login_google") }}</a>
                            </div>
                        @endif

                        <p>{{ ONE::transSite("login_or") }}</p>
                    </div>
                @endif
                <form action="{{ action('AuthController@verifyLogin') }}" method="POST">
                    <div class="login-form">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <div class="form-group">
                            <label>{{ ONE::transSite("login_email_address") }}</label>
                            <input type="email" class="form-control" id="email" name="email" placeholder="{{ ONE::transSite("login_email_address_placeholder") }}">
                        </div>
                        <div class="form-group">
                            <label>{{ ONE::transSite("login_password") }}</label>
                            <input type="password" class="form-control" id="password" name="password" placeholder="{{ ONE::transSite("login_password_placeholder") }}">
                            {{--
                            @if($errors->count()>0)
                              <div class="input-warning">
                                  <i class="fa fa-exclamation-triangle" aria-hidden="true"></i> {{ ONE::transSite("login_authentication_error") }}
                              </div>
                            @endif
                            --}}
                        </div>
                        <div class="forgot-password">
                            <a href="{{ action('AuthController@recovery') }}">
                                {{ ONE::transSite("login_forgot_password") }}
                            </a>
                        </div>
                    </div>
                    <div class="row footer-buttons">
                        <div class="col-12 login-btn">
                            <button type="submit" style="cursor: pointer">
                                {{ ONE::transSite("login_submit") }}
                            </button>
                        </div>
                        <div class="col-12">
                            <div class="login_line_between"></div>
                        </div>
                        <div class="col-12 signIn-btn">
                            <a href="{{ action('AuthController@register') }}">{{ ONE::transSite("login_sign_up") }}</a>
                        </div>
                    </div>
                    {{--  <div class="row footer-buttons">
                        <div class="offset-sm-6 col-sm-6 col-12 login-btn">
                            <a href="#">Resend</a>
                        </div>
                    </div>  --}}
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal_terms" role="dialog">
        <div class="modal-dialog modal-terms modal-50">
            <div class="modal-content">
                <div class="modal-header no-border">
                    <h3 class="modal-title terms-conditions-modal-title">{{ ONE::transSite("register_terms_and_conditions") }}</h3>
                    <div class="float-right"><a data-dismiss="modal" style="cursor:pointer"><em class="fa fa-times my-custom-fa-close"></em></a></div>
                </div>
                <div class="modal-body terms-conditions-wrapper">
                    {!! html_entity_decode(ONE::getSiteEthic('use_terms')) !!}
                    {!! html_entity_decode(ONE::getSiteEthic('privacy_policy')) !!}
                </div>
                <div class="modal-footer">
                                <a href="{{ URL::action('AuthSocialNetworkController@redirectToFacebook') }}" class="accept-terms" style="">{{ ONE::transSite("accept_terms") }}</a>
                                {{--                                <a href="{{ URL::action('AuthSocialNetworkController@redirectToFacebook') }}"><i class="fa fa-facebook" aria-hidden="true"></i> {{ ONE::transSite("login_facebook") }}</a>--}}
                </div>
            </div>
        </div>

        @endsection
        @section('scripts')
            <script>
                $(document).ready(function() {
                    var timeout = 0;

                    $('.modal').on('show.bs.modal', function () {
                        $(this).find('.modal-body').css({
                            width:'auto', //probably not needed
                            height:'auto', //probably not needed
                        });
                    });

                    $( window ).resize(function() {
                        $('.modal-body').css({
                            width:'auto', //probably not needed
                            height:'auto', //probably not needed
                        });
                    });


                });
            </script>

            <style>
                .terms-conditions-wrapper {
                    font-size: 14px;
                }

                .form-row{
                    margin-left: -15px;
                    margin-right: -15px;
                }


            </style>
@endsection