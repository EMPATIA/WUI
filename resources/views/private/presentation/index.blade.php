<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Empatia {{ $page_title or null }}</title>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <link href="{{ asset(ltrim(elixir("css/general.css"), "/"))}}" rel="stylesheet" type="text/css"/>
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <link rel="mask-icon" href="{{ asset('maskicon.svg') }}" color="#6FB353">
    <link rel="icon" sizes="any" mask href="{{ asset('favicon.svg') }}">

        <!-- CSS -->
    <link rel="stylesheet" href="{{ asset('css/switch-button.css')}}">
    <link rel="stylesheet" href="{{ asset('css/default/presentation.css')}}">

    @yield('header_styles')

    <script src="{{ url('/')."/".elixir("js/empatia.js") }}"></script>

    <!-- Dot Dot Dot -->
    <script type="text/javascript" src="{{ asset('js\default\jquery.dotdotdot.js')}}"></script>

    @yield('header_scripts')

</head>
<body>
<a href="/private" class="close-button fa fa-times"></a>
<div class="wrapper">
    @yield('content')
    <footer>
        <div class="logo-right">
            <img src="{{asset('images/default/presentationBackOffice_logo.jpg')}}"/>
        </div>
    </footer>
</div>

@yield('scripts')
</body>
</html>