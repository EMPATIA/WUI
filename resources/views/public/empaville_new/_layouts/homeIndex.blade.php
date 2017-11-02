<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Empaville {{ $page_title or null }}</title>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <link href="{{ asset(ltrim(elixir("css/general.css"), "/"))}}" rel="stylesheet" type="text/css"/>
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <link rel="mask-icon" href="{{ asset('maskicon.svg') }}" color="#6FB353">
    <link rel="icon" sizes="any" mask href="{{ asset('favicon.svg') }}">

    <!-- CSS -->
    <link rel="stylesheet" href="{{ asset('css/default/default-css.css')}}">
    <link rel="stylesheet" href="{{ asset('css/default/cbs.css')}}">
    <link rel="stylesheet" href="{{ asset('css/default/cbs-topic.css')}}">
    <link rel="stylesheet" href="{{ asset('css/default/default-votes.css')}}">
    <link rel="stylesheet" href="{{ asset('css/default/pages-pageContent.css')}}">
    <link rel="stylesheet" href="{{ asset('css/default/polls.css')}}">
    <link rel="stylesheet" href="{{ asset('css/default/auth-register.css')}}">
    <link rel="stylesheet" href="{{ asset('css/default/user.css')}}">

    <style type="text/css">
        @font-face {
            font-family: kelson;
            src: url("{{ asset("fonts/kelson-sans-regular.otf")}}") format("opentype");
        }
    </style>
    @yield('header_styles')


    <script src="{{ url('/')."/".elixir("js/empatia.js") }}"></script>

    <script src="{{ asset('js/sweetalert.min.js')}}"></script>


    <!-- Begin Cookie Consent plugin by Silktide - http://silktide.com/cookieconsent -->
    <script type="text/javascript">
        window.cookieconsent_options = {"message":"{{trans('public.cookieMsg')}}","{{trans('public.cookieDismiss')}}":"{{trans('public.cookieAccept')}}","learnMore":"More info","link":null,"theme":"dark-bottom"};
    </script>
    <script type="text/javascript"  src="{{ asset('js/cookieconsent/cookieconsent.min.js')}}"></script>
    <!-- End Cookie Consent plugin -->
    <!-- Dot Dot Dot -->
    <script type="text/javascript" src="{{ asset('js/cml/jquery.dotdotdot.js')}}"></script>

    <!-- Parallax -->
    <script type="text/javascript" src="{{ asset('js/empatia/parallax.js')}}"></script>

    <!-- jscroll -->
    <script type="text/javascript" src="{{ asset('js/jquery.jscroll.min.js')}}"></script>

    <!-- dropPin -->
    <script type="text/javascript" src="{{ asset('js/dropPin.js')}}"></script>

    @yield('header_scripts')

</head>
<body>

@include('public.empaville_new._layouts.header')

<div class="wrapper">
    @include('public.empaville_new.home.empavilleInfo')
    @yield('content')
    @include('public.empaville_new._layouts.footer')
    @include('sweet::alert')

</div>

@yield('scripts')
{!! ONE::messages() !!}
</body>
</html>