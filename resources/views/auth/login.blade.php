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
</head>
<body class="hold-transition login-page">
<div id="bck_image" style="position: fixed; top: 0; bottom: 0; left: 0; right: 0; background-image: url('{{ asset('/images/background.jpg') }}'); background-position: top center; background-size: cover; background-repeat: no-repeat;  z-index: -100;"></div>
<div class="login-box" style="margin: 5% auto">
    <div class="login-box-body">
        <div style='max-width: 320px; margin: auto; text-align: center'>
            <a href="{{ action('PublicController@index') }}"><img src="{{ asset('images/orig_logo.png') }}" style='width: 80%' alt="Logo" /></a>
        </div>
        <p class="login-box-msg"></p>
        @if(old('auth.wait.registration') == '1')
            <div class="alert alert-warning fade in">
                <!--strong>Warning!</strong><br-->{{trans('register.confirmation.email.check')}}
            </div>
        @endif
        <form action="{{ URL::action('AuthController@verifyLogin') }}" method="POST">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            @if (isset($adminLogin))
                <input type="hidden" name="adminLogin" value="true">
            @endif
            <div class="form-group has-feedback">
                <input type="text" name="email" class="form-control" placeholder="Email" value="{{ old('email') }}" autofocus>
                <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
            </div>
            <div class="form-group has-feedback">
                <input type="password" name="password" class="form-control" placeholder="Password">
                <span class="glyphicon glyphicon-lock form-control-feedback"></span>
            </div>
            <div class="row">
                <div class="col-xs-8"></div>
                <div class="col-xs-4">
                    <button type="submit" class="btn btn-block btn-flat" style="background-color: #62a351; color:white">{{ trans('auth.enter') }}</button>
                </div>
            </div>
        </form>
        
        <br>
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