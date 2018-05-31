@if (ONE::siteConfigurationExists("html_splash_screen_content") && !Session::has("splashScreen"))
{{--  @if(false)  --}}
    {{--When need to remove splash, just uncoment the last line and comment the real condition.--}}
    @include("public.default.home.splash")
@else
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="{{ Session::get('LANG_CODE') ?? Session::get('LANG_CODE_DEFAULT') }}" lang="{{ Session::get('LANG_CODE') ?? Session::get('LANG_CODE_DEFAULT') }}">
    <head>
        <meta charset="UTF-8">
        <title>{{ ONE::getSiteConfiguration("site_title","Demo") }} @if(!empty($pageSubTitle)) - {{ $pageSubTitle }}@endif</title>
        <meta content='width=device-width' name='viewport'>

        <script type="text/javascript">window.cookieconsent_options = {"message": "{{ONE::transSite('cookie_message')}}", "dismiss": "{{ONE::transSite('cookie_accept')}}",};</script>

        @includeif("public." . ONE::getEntityLayout() . "._layouts.metaTags")

        <!-- Icons -->
        <link rel="shortcut icon" href="{{ ONE::getSiteConfiguration("file_favicon","/favicon.ico") }}" type="image/x-icon" />

        <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
        <!-- Open Sans font -->
        <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i,800,800i" rel="stylesheet">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

        <script src="{{ asset(ltrim(mix("js/demo/demo.js"), "/"))}}"></script>
        <link href="{{ asset(ltrim(mix("css/demo/demo.css"), "/"))}}" rel="stylesheet" type="text/css" />

         <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css" integrity="sha384-rwoIResjU2yc3z8GV/NPeZWAv56rSmLldC3R/AZzGRnGxQQKnKkoFVhFQhNUwEyJ" crossorigin="anonymous">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js" integrity="sha384-DztdAPBWPRXSA/3eYEEUWrWCy7G5KFbe8fFjk5JAIxUYHKkDx6Qin1DkWx51bBrb" crossorigin="anonymous"></script>
         {{-- <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/js/bootstrap.min.js" integrity="sha384-vBWWzlZJ8ea9aCX4pEW3rVHjgjt7zpkNpZk+02D9phzyeVkE+jo0ieGizqPLForn" crossorigin="anonymous"></script> --}}
         <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

        {{-- <script src="{{ asset(ltrim(elixir("js/demo/jscroll.min.js"), "/"))}}"></script>--}}

        <!-- Parallax -->
        <script type="text/javascript" src="{{ asset('js/empatia/parallax.js')}}"></script>

        @if(ONE::getGoogleAnalytics())
        <!-- Google Analytics -->
        <script>(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o), m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)})(window,document,'script','https://www.google-analytics.com/analytics.js','ga');ga('create','{{ONE::getGoogleAnalytics()}}' , 'auto');ga('send', 'pageview');</script>
        @endif
        
        @if(ONE::getPiwikAnalytics())
        <!-- Piwik -->
        <script type="text/javascript">var _paq = _paq || [];_paq.push(['trackPageView']);_paq.push(['enableLinkTracking']);(function () {var u = "//piwik.onesource.pt/";_paq.push(['setTrackerUrl', u + 'piwik.php']);_paq.push(['setSiteId', {{ONE::getPiwikAnalytics()}}]);var d = document, g = d.createElement('script'), s = d.getElementsByTagName('script')[0];g.type = 'text/javascript';g.async = true;g.defer = true;g.src = u + 'piwik.js';s.parentNode.insertBefore(g, s);})();</script>
        @endif
        
        @yield('header_styles')
        @yield('header_scripts')

        @include("public.default._layouts.cssOverrides")


        <style>
            .navbar-main-menu{
                width:100%;
            }

            .lang-dropdown{
                width:56px;
            }

            @media (min-width: 768px){
                .navbar-expand-md .navbar-collapse {
                    display: -webkit-box!important;
                    display: -ms-flexbox!important;
                    display: flex!important;
                    -ms-flex-preferred-size: auto;
                    flex-basis: auto;
                }
            }

            .top-bar .navbar-nav .nav-item{
                padding-right: 0!important;
                padding-left: 0!important;
            }

            .nav-link{
                padding-right: 15px!important;
                padding-left: 15px!important;
                display:block
            }
        </style>


        <!-- Fancybox.js -->

        <!-- Mousewheel plugin (optional) -->
        <script type="text/javascript" src="{{ asset('js/fancybox/lib/jquery.mousewheel-3.0.6.pack.js')}}"></script>




        <!-- Fancybox.js -->
        {{--
        <script type="text/javascript" src="{{ asset('js/fancybox3/dist/jquery.fancybox.pack.js')}}"></script>
        <link rel="stylesheet" href="{{ asset('js/fancybox3/src/css/core.css')}}" type="text/css" media="screen" />
        <link rel="stylesheet" href="{{ asset('js/fancybox3/src/css/fullscreen.css')}}" type="text/css" media="screen" />
        <link rel="stylesheet" href="{{ asset('js/fancybox3/src/css/share.css')}}" type="text/css" media="screen" />
        <link rel="stylesheet" href="{{ asset('js/fancybox3/src/css/slideshow.css')}}" type="text/css" media="screen" />
        <link rel="stylesheet" href="{{ asset('js/fancybox3/src/css/thumbs.css')}}" type="text/css" media="screen" />
        --}}

        <script type="text/javascript" src="{{ asset('js/fancybox/source/jquery.fancybox.pack.js')}}"></script>
        <!-- helpers - button, thumbnail and/or media (optional) -->
        <script type="text/javascript" src="{{ asset('js/fancybox/source/helpers/jquery.fancybox-buttons.js')}}"></script>
        <script type="text/javascript" src="{{ asset('js/fancybox/source/helpers/jquery.fancybox-media.js')}}"></script>
        <script type="text/javascript" src="{{ asset('js/fancybox/source/helpers/jquery.fancybox-thumbs.js')}}"></script>
        <!-- // Fancybox.js -->
        <!-- Fancybox.js -->
        <link rel="stylesheet" href="{{ asset('css/fancybox/source/jquery.fancybox.css')}}" type="text/css" media="screen" />
        <!-- helpers - button, thumbnail and/or media (optional) -->
        <link rel="stylesheet" href="{{ asset('css/fancybox/source/helpers/jquery.fancybox-buttons.css')}}" type="text/css" media="screen" />

        <link rel="stylesheet" href="{{ asset('css/fancybox/source/helpers/jquery.fancybox-thumbs.css')}}" type="text/css" media="screen" />
        <!-- // Fancybox.js -->
    </head>
    <body>

        <!-- Header -->
        @include('public.cascais._layouts.header')
        <!-- Content -->
        @yield("content")

        <!-- Footer -->
        @include('public.cascais._layouts.footer')

        <!-- Messages -->
        @include('sweet::alert')

        @yield('scripts')

        @if(env('APP_DEBUG'))
            {!! ONE::messages() !!}
        @endif

        @if (count(ONE::getAllLanguages())>1)
            <script>
                function updateLanguage(langCode) {
                    $.ajax({
                        url: '{{action("OneController@setLanguage")}}',
                        method: 'POST',
                        data: {
                            langCode: langCode,
                            _token: "{{ csrf_token()}}"
                        },
                        success: function (action) {
                            location.reload();
                        },
                        error: function (msg) {
                            location.reload();
                        }
                    });
                }
            </script>
        @endif
    </body>
</html>
@endif