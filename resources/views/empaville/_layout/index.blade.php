<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Empatia {{ $page_title or null }}</title>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <link href="{{ asset(ltrim(elixir("css/empatia.css"), "/"))}}" rel="stylesheet" type="text/css"/>
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <link rel="mask-icon" href="{{ asset('maskicon.svg') }}" color="#6FB353">
    <link rel="icon" sizes="any" mask href="{{ asset('favicon.svg') }}">
    <link rel="stylesheet" href="{{ asset('css/sweetalert.css')}}">

    <style type="text/css">
        @font-face {
            font-family: kelson;
            src: url("{{ asset("fonts/kelson-sans-regular.otf")}}") format("opentype");
        }
    </style>
    @yield('header_styles')

</head>
<body class="fixed skin-blue-light layout-top-nav">
<script src="{{ asset(ltrim(elixir("js/empatia.js"), "/"))}}"></script>

    <div class="wrapper">
        @include('empaville._layout.header')

        <div class="content-wrapper">
            <section class="content">

                    @yield('content')

            </section>
        </div>
    </div>

@include('empaville._layout.footer')

</body>
</html>

@yield('scripts')