<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>EMPATIA | Log in</title>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <link href="{{ url('/').elixir("css/empatia.css") }}" rel="stylesheet" type="text/css"/>
    <link rel="stylesheet" href="{{ asset('css/sweetalert.css')}}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        .loginBtn {
            box-sizing: border-box;
            position: relative;
            /* width: 13em;  - apply for fixed size */
            margin: 0.2em;
            padding: 0 15px 0 46px;
            border: none;
            text-align: left;
            line-height: 34px;
            white-space: nowrap;
            border-radius: 0.2em;
            font-size: 16px;
            color: #FFF;
        }
        .loginBtn:before {
            content: "";
            box-sizing: border-box;
            position: absolute;
            top: 0;
            left: 0;
            width: 34px;
            height: 100%;
        }
        .loginBtn:focus {
            outline: none;
        }
        .loginBtn:active {
            box-shadow: inset 0 0 0 32px rgba(0,0,0,0.1);
        }


        /* Facebook */
        .loginBtn--facebook {
            background-color: #4C69BA;
            background-image: linear-gradient(#4C69BA, #3B55A0);
            /*font-family: "Helvetica neue", Helvetica Neue, Helvetica, Arial, sans-serif;*/
            text-shadow: 0 -1px 0 #354C8C;
        }
        .loginBtn--facebook:before {
            border-right: #364e92 1px solid;
            background: url('https://s3-us-west-2.amazonaws.com/s.cdpn.io/14082/icon_facebook.png') 6px 6px no-repeat;
        }
        .loginBtn--facebook:hover,
        .loginBtn--facebook:focus {
            background-color: #5B7BD5;
            background-image: linear-gradient(#5B7BD5, #4864B1);
        }


        /* Google */
        .loginBtn--google {
            /*font-family: "Roboto", Roboto, arial, sans-serif;*/
            background: #DD4B39;
        }
        .loginBtn--google:before {
            border-right: #BB3F30 1px solid;
            background: url('https://s3-us-west-2.amazonaws.com/s.cdpn.io/14082/icon_google.png') 6px 6px no-repeat;
        }
        .loginBtn--google:hover,
        .loginBtn--google:focus {
            background: #E74B37;
        }

        /*  password recovery anchor */

        .password-recovery {
            text-align: right;
            padding-bottom: 10px;
        }
        .password-recovery a{
            color: #000;
            text-decoration: underline;
        }
        .password-recovery a:hover{
            font-weight: bold;
        }
        .password-recovery a:active{
            font-weight: bold;
            color: #588837;
        }
        /*  //  password recovery anchor*/

    </style>
</head>
<body class="hold-transition login-page">
<div id="bck_image" style="position: fixed; top: 0; bottom: 0; left: 0; right: 0; background-image: url('{{ asset('/images/background.jpg') }}'); background-position: top center; background-size: cover; background-repeat: no-repeat;  z-index: -100;"></div>
<div class="login-box" style="margin: 5% auto">
    <div class="login-box-body">
        <div style='max-width: 320px; margin: auto; text-align: center'>
            <a href="{{ action('PublicController@index') }}"><img src="{{ asset('images/orig_logo.png') }}" style='width: 80%' /></a>
        </div>
        <p class="login-box-msg"></p>
        @if(old('auth.wait.registration') == '1')
            <div class="alert alert-warning fade in">
                <!--strong>Warning!</strong><br-->{{trans('register.confirmation.email.check')}}
            </div>
        @endif
        <form action="{{ URL::action('AuthController@verifyLogin') }}" method="POST">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <div class="form-group has-feedback">
                <input type="text" name="email" class="form-control" placeholder="Email" value="{{ old('email') }}" autofocus>
                <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
            </div>
            <div class="form-group has-feedback">
                <input type="password" name="password" class="form-control" placeholder="{{ trans('authLogin.password') }}">
                <span class="glyphicon glyphicon-lock form-control-feedback"></span>
            </div>
            <div class="row">
                <div class="col-xs-12 password-recovery">
                    <div class="padding-login-button">
                        <a href="{{ action('AuthController@recovery') }}" style="text-decoration: underline" >{{ trans('authLogin.password_recovery') }}</a>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-8"></div>
                <div class="col-xs-4">
                    <button type="submit" class="btn btn-block btn-flat" style="background-color: #62a351; color:white">{{ trans('auth.enter') }}</button>
                </div>
            </div>
        </form>


        {{--Login by code--}}
        {{--<div class="row">--}}
        {{--<div class="col-md-12" style="padding-right: 42.5px; ">--}}
        {{--<h4>-- {{trans('login.or')}} --</h4>--}}
        {{--</div>--}}
        {{--<div class="col-md-12">--}}
        {{--{!! Html::oneLoginCode()!!}--}}
        {{--</div>--}}

        {{--</div>--}}


        <br>

        @if($facebook_login==true)
            <form action="{{ URL::action('AuthSocialNetworkController@redirectToFacebook') }}" method="get">
                <button class="loginBtn loginBtn--facebook">
                    {{ trans('defaultAuthLogin.facebook_login') }}
                </button>
            </form>
        @endif

        {{--<a href="{{ action('AuthController@recovery') }}" style="color: #62a351">I forgot my password</a><br>--}}
        {{--<br>--}}
        {{--<a href="{{ action('AuthController@register') }}" class="text-center" style="color: #62a351">{{trans('PublicAuth.register')}}</a>--}}
    </div>
</div>

<script src="{{ url('/')."/".elixir("js/empatia.js") }}"></script>
<script src="{{ asset('js/sweetalert.min.js')}}"></script>

@include('sweet::alert')

{!! ONE::messages() !!}
</body>
</html>