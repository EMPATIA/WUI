<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Empatia</title>
    <meta content="EMPATIA"/>
    <meta name="Description" content="EMPATIA"/>
    <meta name="Keywords"
          content="EMPATIA,Radically enhance the inclusiveness and impact of participatory processes, increasing the participation by designing, evaluating and making publicly available an advanced ICT platform"/>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <meta name="Distribution" content="Global"/>
    <meta name="Author" content="OneSource, Consultoria Informatica, Lda.: http://onesource.pt"/>
    <meta name="Robots" content="INDEX,FOLLOW"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Open Graph protocol -->
    <meta property="og:url" content="http://empatia-project.eu"/>
    <meta property="og:image" content="http://empatia-project.eu/images/public/logoOgImage.jpg"/>
    <meta property="og:description"
          content="EMPATIA aims at producing the first ICT platform capable of fully encompassing both the decision-making cycle and the implementation cycle of participatory processes whose integration is the main driver of the self-sustainability process."/>
    <meta property="og:type" content="website"/>

    <!-- This is where we yield styles from views -->
{{--<!-- jQuery -->--}}
{{--<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>--}}
<!-- Bootstrap -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">

    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <link rel="mask-icon" href="{{ asset('maskicon.svg') }}" color="#6FB353">
    <link rel="icon" sizes="any" mask href="{{ asset('favicon.svg') }}">
    <!-- Sweetalert -->
    <link rel="stylesheet" href="{{ asset('css/sweetalert.css')}}">
    <!-- Typicons -->
    <link rel="stylesheet" href="{{asset('fonts/empatia/typicons.css')}}"/>
    <!-- Fonts -->
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{asset('fonts/empatia/font-awesome.css')}}"/>
    <!-- Open Sans -->
    <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,300,700' rel='stylesheet' type='text/css'>
    <!-- Roboto -->
    <link href='https://fonts.googleapis.com/css?family=Roboto:400,300,500,700' rel='stylesheet' type='text/css'>
    <!-- Main Css -->
    <link rel="stylesheet" href="{{asset('css/empatia/main.css')}}">
    <!-- Questionnaire -->
    <link rel="stylesheet" href="{{asset('css/empatia/form-css.css')}}">
    <!-- Cbs -->
    <link rel="stylesheet" href="{{asset('css/empatia/cbs.css')}}">
    <link rel="stylesheet" href="{{ asset('css/empatia/default-votes.css')}}">
    <link rel="stylesheet" href="{{ asset('css/empatia/fontello.css')}}">

    <!-- Toastr -->
    <link rel="stylesheet" href="{{ asset('css/empatia/toastr.css')}}">

@yield('header_styles')


<!-- Begin Cookie Consent plugin by Silktide - http://silktide.com/cookieconsent -->
    <script type="text/javascript"> window.cookieconsent_options = {
            "message": "{{trans('public.cookieMsg')}}",
            "{{trans('empatia.cookieDismiss')}}": "{{trans('empatia.cookieAccept')}}",
            "learnMore": "{{trans('empatia.privacy_policy')}}",
            "link": '{{ action("SiteEthicsController@showPublicSiteEthic",'privacy_policy') }}',
            "theme": "dark-bottom"
        };</script>
    <script type="text/javascript" src="{{ asset('js/cookieconsent/cookieconsent.min.js')}}"></script>
    <!-- End Cookie Consent plugin -->

    <!-- This is where we yield styles from views -->

    <!-- Toastr JS -->
    <script type="text/javascript" src="{{ asset('js/empatia/toastr.js')}}"></script>

    <script src="{{ asset(ltrim(elixir("js/general.js"), "/"))}}"></script>
    <!-- Dot Dot Dot -->
    <script type="text/javascript" src="{{ asset('js/default/jquery.dotdotdot.js')}}"></script>
    <!-- Parallax -->
    <script type="text/javascript" src="{{ asset('js/empatia/parallax.js')}}"></script>

    <!-- jscroll -->
    <script type="text/javascript" src="{{ asset('js/jquery.jscroll.min.js')}}"></script>
    @if(ONE::getGoogleAnalytics())
        <script>
            (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
                    (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
                m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
            })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

            ga('create','{{ONE::getGoogleAnalytics()}}' , 'auto');
            ga('send', 'pageview');

        </script>
    @endif

    @if(ONE::getPiwikAnalytics())
        <!-- Piwik -->
        <script type="text/javascript">
            var _paq = _paq || [];
            /* tracker methods like "setCustomDimension" should be called before "trackPageView" */
            _paq.push(['trackPageView']);
            _paq.push(['enableLinkTracking']);
            (function () {
                var u = "//piwik.onesource.pt/";
                _paq.push(['setTrackerUrl', u + 'piwik.php']);
                _paq.push(['setSiteId', {{ONE::getPiwikAnalytics()}}]);
                var d = document, g = d.createElement('script'), s = d.getElementsByTagName('script')[0];
                g.type = 'text/javascript';
                g.async = true;
                g.defer = true;
                g.src = u + 'piwik.js';
                s.parentNode.insertBefore(g, s);
            })();
        </script>
        <!-- End Piwik Code -->
    @endif

    @yield('header_scripts')

</head>



<body class="background-white" style="font-size: 1.2rem">
@include('public.empatia._layouts.header')

<div class="wrapper" style="">

    <div id="contentSectionPage">
        @yield('content')
    </div>

    {{--@include('public.empatia.home.partners')--}}
    @include('public.empatia._layouts.footer')
    @include('sweet::alert')

</div>

@yield('scripts')
{!! ONE::messages() !!}

<script>
    $(window).resize(function(){
        verifyPageHeight();
    });

    $(document).ready(function () {
        verifyPageHeight();
    });
    function verifyPageHeight(){
        //fix content height to window height
        var windowHeight = $(window).height();
        var headerFooterHeight = $('#headerPage').height() + $('#pageFooter').height();
        var contentHeight = $('#contentSectionPage').height();
        if(contentHeight+headerFooterHeight < windowHeight){
            var total = windowHeight - headerFooterHeight;
            $('#contentSectionPage').height(total);
        }
    }
</script>
<!-- Dot Dot Dot -->
<script>
    $.each([$(".pilots-summary"), $(".goals"), $(".pilots-status"), $(".otherN-title"), $(".eventsList-summary"), $(".newsList-summary")], function (index, value) {
        $(document).ready(function () {
            value.dotdotdot({
                ellipsis: '... ',
                after: 'a.readmore',
                wrap: 'word',
                aft: null,
                watch: "window",
            });
        });
    });
</script>

<!-- Parallax -->
<script>
    $('#myCarousel').bind('slide.bs.carousel', function  (e) {
        window.timmer = setInterval(function(){
            $(window).resize();
        }, 5);
    });

    $('#myCarousel').bind('slid.bs.carousel', function  (e) {
        clearInterval(window.timmer);
    });

    $(function(){
        if (navigator.userAgent.match(/(iPod|iPhone|iPad|Android)/)) {
            $('#ios-notice').removeClass('hidden');
            $('.parallax-container').height( $(window).height() * 0.3 | 0 );
        } else {
            /*$(window).resize(function(){
             var parallaxHeight = Math.max($(window).height() * 0.6, 200) | 0;
             $('.parallax-container').height(parallaxHeight);
             }).trigger('resize');*/
        }
    });
</script>
</body>
</html>

