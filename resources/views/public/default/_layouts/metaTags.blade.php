<!-- Metadata tags -->
<meta name="Distribution" content="Global" />
@if(!empty(Session::get('SITE-CONFIGURATION.website_author')))
    <meta name="author" content="{{ Session::get('SITE-CONFIGURATION.website_author') }}" />
@endif
@if(!empty(Session::get('SITE-CONFIGURATION.website_description')))
    <meta name="Description" content="{{ Session::get('SITE-CONFIGURATION.website_description') }}" />
@endif
@if(!empty(Session::get('SITE-CONFIGURATION.website_keywords')))
    <meta name="Keywords" content="{{ Session::get('SITE-CONFIGURATION.website_keywords') }}" />
@endif
<meta name="Robots" content="INDEX,FOLLOW" />

<!-- Open Graph Tags -->
@if(!empty(Session::get('SITE-CONFIGURATION.og_site_name')))
    <meta property="og:site_name" content="{{ Session::get('SITE-CONFIGURATION.og_site_name') }}" />
@endif
@if(!empty($openGraphTags["title"]))
    <meta property="og:title" content="{{ $openGraphTags["title"] }}" />
@elseif(!empty(Session::get('SITE-CONFIGURATION.og_title')))
    <meta property="og:title" content="{{ Session::get('SITE-CONFIGURATION.og_title') }}" />
@endif
<meta property="og:url" content="{{ (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]" }}" />
@if(!empty($openGraphTags["image"]) && !empty($openGraphTags["image"]["file_id"]) && !empty($openGraphTags["image"]["file_code"])  )
    <meta property="og:image" content="{{ action('FilesController@download', [$openGraphTags["image"]["file_id"], $openGraphTags["image"]["file_code"], 'inline' => 1, 'h' => 315, 'w' => 600] )}}" />
@elseif(!empty(Session::get('SITE-CONFIGURATION.file_og_image')))
    <meta property="og:image" content="{{ Session::get('SITE-CONFIGURATION.file_og_image') }}" />
@endif
@if(!empty($openGraphTags["description"]))
    @php
        $ogDescription = $openGraphTags["description"];
        if (preg_match('/^.{1,260}\b/s', $ogDescription, $match)){
            $ogDescription = $match[0];
        }
    @endphp
    <meta property="og:description" content="{{ strip_tags(html_entity_decode($ogDescription)) }} &hellip;" />
@elseif(!empty(Session::get('SITE-CONFIGURATION.og_description')))
    <meta property="og:description" content="{{ Session::get('SITE-CONFIGURATION.og_description') }}" />
@endif
<meta property="fb:app_id" content="1735785593392552"/>
@if(!empty($openGraphTags["audio"]))
    <meta property="og:audio" content="{{ $openGraphTags["audio"] }}" />
@elseif(!empty(Session::get('SITE-CONFIGURATION.file_og_audio')))
    <meta property="og:audio" content="{{ Session::get('SITE-CONFIGURATION.file_og_audio') }}" />
@endif
@if(isset($openGraphTags["video"]) && !empty($openGraphTags["video"] ))
    <meta property="og:video" content="{{ $openGraphTags["video"] }}" />
@elseif(!empty(Session::get('SITE-CONFIGURATION.file_og_video')))
    <meta property="og:video" content="{{ Session::get('SITE-CONFIGURATION.file_og_video')  }}" />
@endif
<meta property="og:type" content="website" />