@extends('public.default._layouts.loginLayout')
@include('public.default._layouts.cssOverrides')
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
    }

    .login-bg .login-row .login-box .footer-buttons .signIn-btn a:hover{
        background: {{ONE::getSiteConfiguration("color_secondary") }}!important;
    }

    .login-bg .login-row .login-box .row .mx-auto .alert{
        margin-top: 15px;
    }

    .login-bg {
        background-image: url({{ ONE::getSiteConfiguration("file_background_login") ."?w=1250" }})!important ;
    }

</style>
@endsection

@section("content")
    <div class="container-fluid login-bg">
        <div class="row login-row">
            <div class="col-12 col-sm-11 col-md-10 col-lg-5 login-box">
                <div class="login-title">
                    {{ ONE::transSite("recovery_title") }}
                </div>
                <div class="login-description-text">
                    {{ ONE::transSite("recovery_title_description") }}
                </div>
                <form action="{{ action('AuthController@passwordRecovery') }}" method="POST">
                    <div class="login-form">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <div class="form-group">
                            <label>{{ ONE::transSite("recovery_email_address") }}</label>
                            <input type="email" class="form-control" id="email" name="email" placeholder="{{ ONE::transSite("recovery_email_address_placeholder") }}">
                            @if($errors->count()>0)
                                <div class="input-warning">
                                    <i class="fa fa-exclamation-triangle" aria-hidden="true"></i> {{ ONE::transSite("recovery_email_address_error") }}
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="row footer-buttons">
                        <div class="col-sm-6 col-12 signIn-btn">
                            <a href="{{ action('AuthController@login') }}">
                                {{ ONE::transSite("recovery_cancel") }}
                            </a>
                        </div>
                        <div class="col-sm-6 col-12 login-btn">
                            <button type="submit" style="cursor: pointer">
                                {{ ONE::transSite("recovery_password") }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection