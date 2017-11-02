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
        <link rel="stylesheet" href="{{ asset('css/private/wizard.css')}}">

        @yield('header_styles')

        <script src="{{ url('/')."/".elixir("js/empatia.js") }}"></script>

        <!-- Dot Dot Dot -->
        <script type="text/javascript" src="{{ asset('js\cml\jquery.dotdotdot.js')}}"></script>

        @yield('header_scripts')

    </head>
    <body>
        <div class="wrapper">
            <div class="welcome-container">
                @yield('content')
            </div>
            <footer>
                <div class="logo-right">
                    <img src="{{asset('images/presentationBackOffice_logo.jpg')}}"/>
                </div>
            </footer>
        </div>
        @yield('scripts')
        {!! ONE::messages() !!}
    </body>
</html>