<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>EMPATIA | Register</title>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <link href="{{ url('/').elixir("css/empatia.css") }}" rel="stylesheet" type="text/css"/>
    <link rel="stylesheet" href="{{ asset('css/sweetalert.css')}}">
</head>
<body class="hold-transition login-page">
<div id="bck_image" style="position: fixed; top: 0; bottom: 0; left: 0; right: 0; background-image: url('{{ asset('/images/background.jpg') }}'); background-position: top center; background-size: cover; background-repeat: no-repeat; opacity:0.6; z-index: -100;"></div>
<div class="login-box" style="margin: 5% auto;">
    <div class="login-box-body">
        <div style='max-width: 320px; margin: auto; text-align: center'>
            <a href="{{ action('PublicController@index') }}"><img src="{{ asset('images/orig_logo.png') }}" style='width: 70%' alt="Logo" /></a>
        </div>
        <p class="login-box-msg">{{ trans('register.msg') }}</p>

        <form action="{{ URL::action('AuthController@verifyRegister') }}" method="POST">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <div class="form-group has-feedback">
                <input type="text" name="name" class="form-control" placeholder="Name" value="{{ old('name') }}" autofocus>
                <span class="form-control-feedback"></span>
            </div>
            <div class="form-group has-feedback">
                <input type="text" name="email" class="form-control" placeholder="Email" value="{{ old('email') }}">
                <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
            </div>
            <div class="form-group has-feedback">
                <input type="password" name="password" class="form-control" placeholder="Password">
                <span class="glyphicon glyphicon-lock form-control-feedback"></span>
            </div>
            <div class="form-group has-feedback">
                <input type="password" name="password_confirmation" class="form-control" placeholder="Confirm Password">
                <span class="glyphicon glyphicon-lock form-control-feedback"></span>
            </div>
            <div class="row">
                <div class="col-xs-8">
                    <!--div class="checkbox">
                        <label class="hover">
                            <div class="icheckbox_square-blue hover active" aria-checked="false" aria-disabled="false"><input type="checkbox" style="top: 5px;left: -23px;display: block;width: 60px;height: 0px;margin: 0px;padding: 0px;background-color: rgb(255, 255, 255);border: 0px;background-position: initial initial;background-repeat: initial initial;"></div> I agree to the <a href="#">terms</a>
                        </label>
                    </div-->
                </div>
                <!-- /.col -->
                <div class="col-xs-4">
                    <button type="submit" class="btn btn-primary btn-block btn-flat">{{ trans('register.register') }}</button>
                </div>
                <!-- /.col -->
            </div>
            <div class="social-auth-links text-center"  style="display: none">
                <p>- OR -</p>
                <a href="#" class="btn btn-block btn-social btn-facebook btn-flat disabled"><i class="fa fa-facebook"></i> Sign up using
                    Facebook</a>
                <a href="#" class="btn btn-block btn-social btn-google btn-flat disabled"><i class="fa fa-google-plus"></i> Sign up using
                    Google+</a>
            </div>
            <br>
            <a href="{{ action('AuthController@login') }}" class="text-center">Already have an account?</a>
        </form>

    </div>
</div>
<script src="{{ url('/')."/".elixir("js/empatia.js") }}"></script>
<script src="{{ asset('js/sweetalert.min.js')}}"></script>

@include('sweet::alert')
{!! ONE::messages() !!}
</body>
</html>