@extends('public.default._layouts.loginLayout')
@section('header_styles')
<style>
    .login-bg .login-row .login-box .footer-buttons .login-btn button{
        cursor: pointer;
    }
    .login-bg .login-row .login-box .footer-buttons .login-btn button{
        cursor: pointer;

    }

    .login-bg .login-row .login-box .footer-buttons .login-btn button:hover{
        background: {{ONE::getSiteConfiguration("color_secondary") }}!important;
    }

</style>
@endsection

@section("content")
    <div class="container-fluid login-bg" style="background-image: url('images/demo/workplace-1245776_1920_grey_blured.jpg')">
        <div class="row login-row">
            <div class="col-12 col-sm-11 col-md-10 col-lg-7 login-box">
                <div class="login-close">

                </div>
                <div class="login-title">
                    {{ ONE::transSite("update_password_success_title") }}
                </div>
                <form action="{{ action('AuthController@login') }}" method="GET">
                    <div class="login-form" style="margin-top: 40px">
                        {{ ONE::transSite("update_password_success_messsage") }}
                    </div>
                    <div class="row footer-buttons">
                        <div class="col-sm-6 col-12 login-btn">
                            <button type="submit" style="cursor: pointer">
                                {{ ONE::transSite("go_to_login") }}
                            </button>
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
@endsection