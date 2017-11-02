<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>EMPATIA {{ $page_title or null }}</title>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    {{--
        <link href="{{ asset(ltrim(elixir("css/general.css", "/"), "/"))}}" rel="stylesheet" type="text/css"/>
    --}}
    <link href="{{ asset(ltrim(elixir("css/private.css", "/"), "/"))}}" rel="stylesheet" type="text/css"/>
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <link rel="mask-icon" href="{{ asset('maskicon.svg') }}" color="#6FB353">
    <link rel="icon" sizes="any" mask href="{{ asset('favicon.svg') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    @yield('header_styles')
    {{--
        <script src="{{ asset(ltrim(elixir("js/general.js", "/"), "/"))}}"></script>
    --}}
    <script src="{{ asset(ltrim(elixir("js/private.js", "/"), "/"))}}"></script>
    {{--
    <!-- Bootstrap 4.0.0.1 Beta -->
    <link rel="stylesheet" href="/bootstrap/4.0.0-beta/css/bootstrap.min.css">
    <script src="/bootstrap/4.0.0-beta/js/popper.min.js"></script>
    <script src="/bootstrap/4.0.0-beta/js/bootstrap.min.js"></script>--}}
    @yield('header_scripts')
{{--
    <link href="{{ asset("css/private-new.css")}}" rel="stylesheet" type="text/css"/>
--}}
    {{--
    <script src="{{ asset("js/sidebar.js")}}"></script>
    --}}
</head>
<body class="fixed skin-blue-light sidebar-mini">
<div class="wrapper">
    @include('private._private.header')
    @include('private._private.sidebar')
    @include('private._private.sidebar1')


    <div class="content-wrapper">
        <section class="content-header">
            <h1>
                {{ $title or "" }}
            </h1>
        </section>

        <section class="content">
            @yield('content')
        </section>
    </div>

    @include('private._private.footer')
    @include('private._private.aside')
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
        stateSave: true
    } );

    @if(env('SOCKET_IO', false))
    // Socket.IO
    var socket = io('http://{{ env('SOCKET_IO_HOST', 'localhost') }}:{{ env('SOCKET_IO_PORT', 3000) }}');
    socket.on("all-channel:App\\Events\\EventNotify", function(message){
        console.log("EVENT MSG: "+message.msg);
    });

    socket.on("connect", function() {
        console.log("socket.io connected!");
    })

    socket.on("disconnect", function() {
        console.log("socket.io disconnect!");
    })

    socket.on("connect_failed", function() {
        console.log("socket.io connection failed!");
        alert('socket.io connection failed!');
    })
    @endif
</script>
<script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
</script>

@yield('scripts')
@yield('scripts_form_delete')
{!! ONE::messages() !!}

</body>
</html>