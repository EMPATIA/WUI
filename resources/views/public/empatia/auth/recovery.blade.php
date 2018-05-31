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

        .btn.btn-flat{
            border-width: 2px;
        }
        .btn-recovery-default {
            background-color: #62a351;
            color: white;
            border: 2px solid #62a351;
        }
        .btn-recovery-default:hover{
            background-color: white;
            color: #8bc740;
            border: 2px solid #8bc740;
        }
        .return-button{
            margin-top: 10px;
        }

    </style>

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
        @if(isset($errors))
            @if(count($errors->all())>0)
            <div class="alert alert-danger fade in text-center">
                {{trans('empatiaAuth.please_check_your_email')}}
            </div>
            @endif
        @endif
        <form action="{{ URL::action('AuthController@passwordRecovery') }}" method="POST" id="recovery_form">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <div class="row">
                <div class="col-xs-12">
                    <div class="form-group has-feedback">
                        <input type="email" name="email" class="form-control" placeholder="{{ trans('empatiaAuth.email') }}" required autofocus>
                        <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12">
                    <div class="padding-login-button">
                        <button type="submit" class="btn btn-block btn-flat btn-recovery-default">{{ trans('empatiaAuth.password_recovery') }}</button>
                    </div>
                </div>
            </div>
        </form>
        <div class="row">
            <div class="col-xs-6 pull-right return-button">
                <div class="padding-login-button">
                    <a href="{{ action('PublicController@index') }}">
                        <button class="btn btn-block btn-flat btn-recovery-default">{{ trans('empatiaAuth.back') }}</button>
                    </a>
                </div>
            </div>
        </div>
        <br>
    </div>
</div>
@include('sweet::alert')
<script>
    $( "#recovery_form" ).submit(function( event ) {
        $(".login-button").css('opacity', '0.5');
        $(".login-button").css('pointer-events', 'none');
    });
</script>
</body>
</html>