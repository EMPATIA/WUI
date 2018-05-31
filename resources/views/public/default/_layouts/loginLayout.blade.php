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
        <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" />

        <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>

        <!-- Bootstrap -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css" integrity="sha384-rwoIResjU2yc3z8GV/NPeZWAv56rSmLldC3R/AZzGRnGxQQKnKkoFVhFQhNUwEyJ" crossorigin="anonymous">
        <script src="https://code.jquery.com/jquery-3.1.1.slim.min.js" integrity="sha384-A7FZj7v+d/sdmMqp/nOQwliLvUsJfDHW+k9Omg/a/EheAdgtzNs3hpfag6Ed950n" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js" integrity="sha384-DztdAPBWPRXSA/3eYEEUWrWCy7G5KFbe8fFjk5JAIxUYHKkDx6Qin1DkWx51bBrb" crossorigin="anonymous"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/js/bootstrap.min.js" integrity="sha384-vBWWzlZJ8ea9aCX4pEW3rVHjgjt7zpkNpZk+02D9phzyeVkE+jo0ieGizqPLForn" crossorigin="anonymous"></script>

        <!-- Open Sans font -->
        <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i,800,800i" rel="stylesheet">

        <link href="{{ asset(ltrim(elixir("css/demo.css"), "/"))}}" rel="stylesheet" type="text/css" />

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
    </head>
    <body>
        @yield("content")
        @include("public.default._layouts.cssOverrides")
    </body>
</html>
@endif