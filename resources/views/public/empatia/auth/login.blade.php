<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>EMPATIA | Log in</title>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <link href="{{ url('/').elixir("css/empatia.css") }}" rel="stylesheet" type="text/css"/>
    <link rel="stylesheet" href="{{ asset('css/empatia/auth-login.css')}}">
    <link rel="stylesheet" href="{{ asset('css/sweetalert.css')}}">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @if(ONE::getPiwikAnalytics())
        <!-- Piwik -->
        <script type="text/javascript">
            var _paq = _paq || [];
            /* tracker methods like "setCustomDimension" should be called before "trackPageView" */
            _paq.push(['trackPageView']);
            _paq.push(['enableLinkTracking']);
            (function () {
                var u = "//piwik.onesource.pt/";
                _paq.push(['setTrackerUrl', u + 'piwik.php']);
                _paq.push(['setSiteId', {{ONE::getPiwikAnalytics()}}]);
                var d = document, g = d.createElement('script'), s = d.getElementsByTagName('script')[0];
                g.type = 'text/javascript';
                g.async = true;
                g.defer = true;
                g.src = u + 'piwik.js';
                s.parentNode.insertBefore(g, s);
            })();
        </script>
        <!-- End Piwik Code -->
    @endif
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
                <!--strong>Warning!</strong><br-->{{trans('empatiaAuth.confirmation_email_check')}}
            </div>
        @endif
        <form action="{{ URL::action('AuthController@verifyLogin') }}" method="POST">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <div class="form-group has-feedback">
                <input type="text" name="email" class="form-control" placeholder="{{trans('empatiaAuth.email')}}" value="{{ old('email') }}" autofocus>
                <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
            </div>
            <div class="form-group has-feedback">
                <input type="password" name="password" class="form-control" placeholder="{{trans('empatiaAuth.password')}}">
                <span class="glyphicon glyphicon-lock form-control-feedback"></span>
            </div>
            <div class="row">
                <div class="col-xs-8">
                    <a class="link-default-login" href="{{ action('AuthController@recovery') }}">{{trans('empatiaAuth.password_recovery')}}</a><br>
                    <a class="link-default-login" href="{{ action('AuthController@register') }}">{{trans('empatiaAuth.register')}}</a>
                </div>
                <div class="col-xs-4">
                    <button type="submit" class="btn btn-block btn-flat btn-login">{{ trans('empatiaAuth.enter') }}</button>
                </div>
            </div>
        </form>
        <br>
        @if($facebook_login)
            <div class="row">
                <div class="col-xs-12">
                    <form action="{{ URL::action('AuthSocialNetworkController@redirectToFacebook') }}" method="get">
                        <button class="loginBtn loginBtn--facebook padding-login-button btn-block">
                            {{ trans('empatiaAuth.facebook_login') }}
                        </button>
                    </form>
                </div>
            </div>
        @endif

    </div>
</div>

<script src="{{ url('/')."/".elixir("js/empatia.js") }}"></script>
<script src="{{ asset('js/sweetalert.min.js')}}"></script>

@include('sweet::alert')

{!! ONE::messages() !!}
</body>
</html>