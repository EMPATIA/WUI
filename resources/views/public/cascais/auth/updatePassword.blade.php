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
                    {{ ONE::transSite("update_password_title") }}
                </div>
                <form action="{{ action('AuthController@updatePassword') }}" method="POST">
                    <div class="login-form">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" name="recoverToken" value="{{ isset($recoverToken) ? $recoverToken : null }}">
                        <input type="hidden" name="userKey" value="{{ isset($userKey) ? $userKey : null }}">
                        
                        <div class="form-group">
                            <label>{{ ONE::transSite("update_password") }}</label>
                            <input type="password" class="form-control" id="password" name="password" placeholder="{{ ONE::transSite("update_password_placeholder") }}">
                            {{--  <div class="input-warning"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> Wrong password</div>  --}}
                        </div>
                        <div class="form-group">
                            <label>{{ ONE::transSite("update_password_confirmation") }}</label>
                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="{{ ONE::transSite("update_password_confirmation_placeholder") }}">
                            {{--  <div class="input-warning"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> Wrong password</div>  --}}
                        </div>
                    </div>
                    <div class="row footer-buttons">
                        <div class="col-sm-6 col-12 login-btn">
                            <button type="submit" style="cursor: pointer">
                                {{ ONE::transSite("update_recover_password") }}
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