<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title> {{ $page_title or null }}</title>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <link href="{{ asset(ltrim(elixir("css/empatia.css"), "/"))}}" rel="stylesheet" type="text/css"/>
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <link rel="mask-icon" href="{{ asset('maskicon.svg') }}" color="#6FB353">
    <link rel="icon" sizes="any" mask href="{{ asset('favicon.svg') }}">
    @yield('header_styles')
</head>
<body class="fixed skin-blue-light layout-top-nav">
<div class="wrapper">
    {{--@include('public._layouts.header')--}}

    <div class="content-wrapper">
        <div class="container">
            <section class="content-header">
                <h1>
                    {{ $title or "" }}
                    <small>{{ trans(array_get(Route::getCurrentRoute()->getAction(), 'as', '')) }}</small>
                </h1>
                <!-- You can dynamically generate breadcrumbs here  -->
                {!! Breadcrumbs::renderIfExists() !!}
                {{--<ol class="breadcrumb">--}}
                {{--<li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>--}}
                {{--<li class="active">Here</li>--}}
                {{--</ol>--}}
            </section>

            <section class="content">
                @yield('content')
            </section>
        </div>
    </div>

    @include('public._layouts.footer')
</div>
<script src="{{ url('/')."/".elixir("js/empatia.js") }}"></script>
{!! ONE::messages() !!}

<script>
    // DataTable defaults
    $.extend( true, $.fn.dataTable.defaults, {
        language: {
            search: "<span class='pull-right'><button class='btn btn-default btn-sm btn-flat' type='button'><i class='fa fa-search'></i></button></span>",
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
@yield('scripts')
</body>
</html>