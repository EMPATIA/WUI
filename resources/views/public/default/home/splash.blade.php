<!DOCTYPE html>
<html>
<head>
    <title>{!! ONE::getSiteConfiguration('site_title') ?? 'Demo' !!}</title>

</head>

<body>
        @if(ONE::siteConfigurationExists("html_splash_screen_content"))
            {!! ONE::getSiteConfiguration('html_splash_screen_content') !!}
        @endif
</body>
</html>