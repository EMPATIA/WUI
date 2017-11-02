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



    <!-- Bootstrap -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">

    <!-- CSS -->
    <link rel="stylesheet" href="{{ asset('css/empaville/default-css.css')}}">

    <link rel="stylesheet" href="{{ asset('css/empaville/cbs.css')}}">

    <style type="text/css">
        @font-face {
            font-family: kelson;
            src: url("{{ asset("fonts/kelson-sans-regular.otf")}}") format("opentype");
        }
    </style>
    @yield('header_styles')



    {{--O Dot dot dot não funciona se isto estiver activo--}}
      <script src="{{ url('/')."/".elixir("js/empatia.js") }}"></script>


    <!-- Begin Cookie Consent plugin by Silktide - http://silktide.com/cookieconsent -->
    <script type="text/javascript">
        window.cookieconsent_options = {"message":"{{trans('public.cookieMsg')}}","{{trans('public.cookieDismiss')}}":"{{trans('public.cookieAccept')}}","learnMore":"More info","link":null,"theme":"dark-bottom"};
    </script>
    <script type="text/javascript"  src="{{ asset('js/cookieconsent/cookieconsent.min.js')}}"></script>
    <!-- End Cookie Consent plugin -->
    <!-- Dot Dot Dot -->
    <script type="text/javascript" src="{{ asset('js\cml\jquery.dotdotdot.js')}}"></script>

</head>
<body class="fixed skin-blue-light layout-top-nav">

  <script src="{{ asset('js/sweetalert.min.js')}}"></script>

    <div class="wrapper">
        @include('public.empaville._layouts.header')

        <div class="content-wrapper">
            <div class="container">
                {{--<section class="content-header" style="height: 40px">--}}
                    {{--<h1 >--}}
                        {{--<small>{{array_get(Route::getCurrentRoute()->getAction(), 'as', '')}}</small>--}}
                        {{----}}
 {{--{{ $title or "" }}--}}
                       {{--<small>{{ trans(array_get(Route::getCurrentRoute()->getAction(), 'as', '')) }}</small>--}}
                       {{----}}
                    {{--</h1>--}}
               {{--<!-- You can dynamically generate breadcrumbs here  -->--}}
               {{--{!! Breadcrumbs::renderIfExists() !!}--}}
               {{--<ol class="breadcrumb">--}}
                    {{--<li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>--}}
                    {{--<li class="active">Here</li>--}}
                    {{--</ol>--}}
                {{--</section>--}}

                <section class="content">
                    @yield('content')
                </section>
            </div>
        </div>

        @include('public.empaville._layouts.footer')
        @include('sweet::alert')

    </div>

    <!--  TESTE <script>
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

        {{-- @if(env('SOCKET_IO', false))
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
        @endif--}}
    </script>-->


    <!-- Dot Dot Dot -->
    <script>
        $.each([$(".lastNews-summary"), $(".nextEvents-summary"), $(".nextEvents-title")], function( index, value ) {
            $(document).ready(function () {
                value.dotdotdot({
                    after: 'a.readmore',
                    watch: "window",
                });
            });
        });
    </script>
    @yield('scripts')
    {!! ONE::messages() !!}
</body>
</html>