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
    <link rel="stylesheet" href="{{ asset('css/sweetalert.css')}}">

    <style type="text/css">
        @font-face {
            font-family: kelson;
            src: url("{{ asset("fonts/kelson-sans-regular.otf")}}") format("opentype");
        }
    </style>

    <!-- fonts -->
    <link href='https://fonts.googleapis.com/css?family=Source+Sans+Pro:400,900' rel='stylesheet' type='text/css'>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href='https://fonts.googleapis.com/css?family=Maven+Pro' rel='stylesheet' type='text/css'>
    <script src="https://use.fontawesome.com/76a29a2c01.js"></script>

    <link rel="stylesheet" href="{{ asset('css/cbs.css')}}">



    <!-- Begin Cookie Consent plugin by Silktide - http://silktide.com/cookieconsent -->
    <script type="text/javascript">
        window.cookieconsent_options = {"message":"{{trans('public.cookieMsg')}}","{{trans('public.cookieDismiss')}}":"{{trans('public.cookieAccept')}}","learnMore":"More info","link":null,"theme":"dark-bottom"};
    </script>
    <script type="text/javascript"  src="{{ asset('js/cookieconsent/cookieconsent.min.js')}}"></script>
    <!-- End Cookie Consent plugin -->

    <!-- Não comentar!!!!! -->
    <script src="{{ asset(ltrim(elixir("js/general.js"), "/"))}}"></script>
    <script src="{{ asset('js/sweetalert.min.js')}}"></script>

    <!-- dotdotdot  -->
    <script type="text/javascript" src="{{ asset('js/jquery.dotdotdot.min.js')}}"></script>
    <script type="text/javascript" src="{{ asset('js/jquery.dotdotdot.js')}}"></script>
</head>



<body class="fixed skin-blue-light layout-top-nav">
<div class="wrapper">
    @include('private.inPersonRegistration.headerInPersonVote')

    <section class="content">
        @yield('content')
    </section>
    @include('sweet::alert')

</div>

<script>
    // DataTable defaults
    $.extend( true, $.fn.dataTable.defaults, {
        language: {
            search: "<span class='pull-right'><button class='btn btn-secondary btn-sm btn-flat' type='button'><i class='fa fa-search'></i></button></span>",
            searchPlaceholder: "{{ trans('table.search') }}",
        },
        paging: true,
        lengthChange: true,
        searching: true,
        ordering: true,
        info: true,
        autoWidth: false,
        responsive: true,
    } );

</script>

@yield('scripts')
{!! ONE::messages() !!}

</body>
</html>