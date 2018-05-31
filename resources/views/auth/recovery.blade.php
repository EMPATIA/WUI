<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>EMPATIA | Recovery</title>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <link href="{{ url('/').elixir("css/empatia.css") }}" rel="stylesheet" type="text/css"/>
</head>
<body class="hold-transition login-page">
<div id="bck_image" style="position: fixed; top: 0; bottom: 0; left: 0; right: 0; background-image: url('{{ asset('/images/background.jpg') }}'); background-position: top center; background-size: cover; background-repeat: no-repeat; opacity:0.6; z-index: -100;"></div>
<div class="login-box" style="margin: 5% auto">
    <div class="login-box-body">
        <div style='max-width: 320px; margin: auto; text-align: center'>
            <a href="{{ action('PublicController@index') }}"><img src="{{ asset('images/orig_logo.png') }}" style='width: 80%' alt="Logo" /></a>
        </div>
        <p class="login-box-msg">{{ trans('auth.recovery.msg') }}</p>

        <form action="" method="POST">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <div class="form-group has-feedback">
                <input type="text" name="email" class="form-control" placeholder="Email" autofocus>
                <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
            </div>
            <br>
            <br>
            <div class="row">
                <div class="col-xs-7">

                </div>
                <div class="col-xs-5">
                    <button type="submit" class="btn btn-primary btn-block btn-flat">{{ trans('recovery.enter') }}</button>
                </div>
            </div>
            <br>
        </form>
    </div>
</div>

<script src="{{ url('/')."/".elixir("js/empatia.js") }}"></script>
{!! ONE::messages() !!}
</body>
</html>