@php
//$headerImages = ONE::getSiteConfiguration("file_homepage_image","/images/demo/workplace-1245776_1920_grey_blured.jpg");
$contentBannerImagesSections = App\Http\Controllers\PublicContentManagerController::getSections("banner_images");

$headerImages = collect($contentBannerImagesSections)->where('code', '=', 'banner_slideshow')->first();
$headerImages = json_decode(collect($headerImages->section_parameters ?? [])->first()->value ?? "{}");

$firstLogo = ONE::getSiteConfiguration("file_logo_first","/images/demo/LogoEmpatia-l-02.png");

$headerImage = ONE::getSiteConfiguration("file_homepage_image","/images/demo/workplace-1245776_1920_grey_blured.jpg");
@endphp

<style>
    .page-title-user {
        margin-top: auto;
        text-align: right;
        text-transform: uppercase;
        font-size: 1.2rem;
        font-weight: 600;
        color: {{ Session::get("SITE-CONFIGURATION.color_primary") }};
    }
    .page-title-user > .col-6 {
        padding: 5px 15px;
    }
    .top-bar .navbar-nav .nav-item.user-btn.show, .top-bar .navbar-nav .nav-item.user-btn.show .dropdown-menu {
        background-color: #fff!important;
        border-color:#fff!important;
    }
    .nav-item.dropdown.user-btn.show .nav-link {
        color: {{ Session::get("SITE-CONFIGURATION.color_primary") }};
    }

    .sticky-wrapper{
        height: initial!important;
    }

</style>

@if(isset($isHome))
    <style>
        .background-image.homepage-banner{
            height: auto!important;
        }

        .top-bar .navbar-nav .nav-item.login-btn{
            margin:0;
            margin-top:5px;
        }

        @media (min-width: 992px) {
            .top-bar .navbar-nav .nav-item.login-btn{
                margin-left:auto;
                margin-top:0px;
            }
        }

        .background-image {
            min-height: 40vh;
            height: auto;
        }

        .top-bar .navbar-nav .nav-item{
            padding: 0 15px;
            margin: 0 5px;
            display: inline-block;
        }

        .top-bar .navbar-nav .nav-item.user-btn.show, .top-bar .navbar-nav .nav-item.user-btn.show .dropdown-menu {
            background-color: #fff!important;
            border-color:#fff!important;
        }
        .nav-item.dropdown.user-btn.show .nav-link {
            color: {{ Session::get("SITE-CONFIGURATION.color_primary") }};
        }

        .small-top-bar .main-logo img{
            max-height: 80px;
        }

    </style>
@endif
<div class="container-fluid background-image @if(isset($isHome)) homepage-banner @endif" @if(!isset($isHome)) style="background-image:url('@if(isset($headerImages) && !collect($headerImages)->isEmpty()) {!! action('FilesController@download', [$headerImages[0]->id, $headerImages[0]->code, 1, "w" => 1280]) !!} @else {{ $headerImage }} @endif ');" @endif>

<div class="small-top-bar">
    <div class="container mx-auto">
        <nav class="navbar">
            <ul class="navbar-nav navbar-main-menu">
                <li>
                    <a class="main-logo" href="/">
                        <img src="{{ $firstLogo."?h=80" }}">
                    </a>
                </li>
                <li class="ml-auto" style="display: flex;">
                    <ul class="navbar-nav" style="display: flex; justify-content: center; align-items: center;">
                            @if(ONE::siteConfigurationExists("url_social_facebook"))
                            <li class="nav-item">
                                <a href="{{ ONE::getSiteConfiguration("url_social_facebook") }}" class="nav-link" style="padding:0px !important">
                                    <i class="fa fa-facebook-official" aria-hidden="true"></i>
                                </a>
                            </li>
                            @endif
                            @if(ONE::siteConfigurationExists("url_social_instagram"))
                            <li class="nav-item">
                                <a href="{{ ONE::getSiteConfiguration("url_social_instagram") }}" class="nav-link" style="padding:0px !important">
                                    <i class="fa fa-instagram-square" aria-hidden="true"></i>
                                </a>
                            </li>
                            @endif
                            @if(ONE::siteConfigurationExists("url_social_twitter"))
                            <li class="nav-item">
                                <a href="{{ ONE::getSiteConfiguration("url_social_twitter") }}" class="nav-link" style="padding:0px !important">
                                    <i class="fa fa-twitter-square" aria-hidden="true"></i>
                                </a>
                            </li>
                            @endif
                            @if(ONE::siteConfigurationExists("url_social_youtube"))
                            <li class="nav-item">
                                <a href="{{ ONE::getSiteConfiguration("url_social_youtube") }}" class="nav-link" style="padding:0px !important">
                                    <i class="fa fa-youtube" aria-hidden="true"></i>
                                </a>
                            </li>
                            @endif
                        @if (count(ONE::getAllLanguages())>1)
                            <li class="nav-item dropdown lang-dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="langdropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="padding:0px !important">
                                    <span>{{ ONE::getAppLanguageCode() }}</span>
                                </a>
                                <div class="dropdown-menu" aria-labelledby="langdropdown">
                                    @foreach(ONE::getAllLanguages() as $language)
                                        @if($language->code!=ONE::getAppLanguageCode())
                                            <a class="dropdown-item" href="#" onclick="updateLanguage('{{ $language->code }}')" {{ ONE::getAppLanguageCode() == $language->code? 'selected' : ''}} style="padding:0px !important">
                                                {{$language->code}}
                                            </a>
                                        @endif
                                    @endforeach
                                </div>
                            </li>
                        @endif
                    </ul>
                </li>
            </ul>
        </nav>
    </div>
</div>
<div class="top-bar primary-color sticky-menu-wrapper" style="z-index:100;">
    <div class="container" style="padding:0">

        <nav class="navbar navbar-expand-md">
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <i class="fa fa-bars" aria-hidden="true"></i>
            </button>

            <div class="collapse navbar-collapse ml-auto" id="navbarSupportedContent">
                <ul class="navbar-nav">
                    {!! ONE::getAccessMenuDemo() !!}

                    @if(Session::has('X-AUTH-TOKEN'))
                        <!-- If it is logged-->
                        <li class="nav-item dropdown user-btn">
                            <div class="px-3">
                                <a class="nav-link" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    @if(Session::get('user')->photo_id > 0)
                                        <div class="user-img" style="background-image: url({{ action('FilesController@download',[Session::get('user')->photo_id, Session::get('user')->photo_code, 1, "h" => 50])  }})"></div>
                                    @else
                                        <div class="fa fa-user no-pic"></div>
                                    @endif
                                    <span>
                                            {{ strlen (Session::get('user')->name) < 15 ? Session::get('user')->name : strstr(Session::get('user')->name, ' ', true).strrchr(Session::get('user')->name,' ') }}
                                        </span>
                                </a>
                            
                                <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                                    <a href="{{ action('PublicUsersController@show',['userKey' => Session::get('user')->user_key]) }}" class="dropdown-item">
                                        {{ ONE::transSite('header_menu_user_profile') }}
                                    </a>
                                    @if (ONE::asPermission('manager'))
                                        <a href="{{ url('/private') }}" class="dropdown-item">
                                            {{ ONE::transSite('header_menu_back_office') }}
                                        </a>
                                    @endif
                                    <a href="{{ action('AuthController@logout') }}" class="dropdown-item">
                                        {{ONE::transSite('header_menu_sign_out')}}
                                    </a>
                                </div>
                            </div>
                        </li>
                    @else
                        <!-- If not logged-->
                        <li class="nav-item user-btn">
                            <div class="px-3">
                                <a class="nav-link" href="{{ action('AuthController@login') }}">
                                    {{ ONE::transSite("header_login") }}
                                </a>
                            </div>
                        </li>
                    @endif
                </ul>
            </div>
        </nav>
    </div>
</div>

@if(!empty($demoPageTitle))
    <div class="row page-title-user">
        <div class="col-6 light-grey-bg">
            <div class="container">
                <div class="row">
                    <div class="col-12 no-padding">
                        {!! $demoPageTitle !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
@if(isset($isHome))
    <?php
    $contentSection = collect($homeContentSections)->where('code', '=', 'header_content')->first();
    $buttonSection = collect($homeContentSections)->where('code', '=', 'header_button')->first();
    ?>
    <div class="row carousel-row">
        <div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel" style="flex:1; display: flex">
            @if(count($headerImages)>1)
                <ol class="carousel-indicators">
                    @foreach ($headerImages as $headerImage)
                        <li data-target="#carouselExampleIndicators" data-slide-to="{{ $loop->index }}" @if($loop->first) class="active" @endif></li>
                    @endforeach
                </ol>
            @endif

            <div class="carousel-inner" role="listbox" style="display: flex; flex: 1">
                @foreach ($headerImages as $headerImage)
                    <div class="carousel-item @if($loop->first) active @endif" style="height:40vh; background-image: url('{!!  action('FilesController@download', [$headerImage->id, $headerImage->code, 1, "w" => "1280"])  !!}'); background-size: cover; background-position: center;">
                    <!--<img class="" src="{!!  action('FilesController@download', [$headerImage->id, $headerImage->code])  !!}">-->
                    </div>
                @endforeach

            </div>
        </div>
    </div>
    <div class="row banner-bottom">
        <div class="col-12">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        @if(!is_null($contentSection))
                            {!! collect($contentSection->section_parameters)->first()->value  !!}
                        @endif
                    </div>
                </div>
                {{--<div class="banner-normal-text">--}}

                {{--</div>--}}
                @if(!is_null($buttonSection))
                    <?php
                    $buttonText = collect($buttonSection->section_parameters)->where("section_type_parameter.code","=","buttonText")->first()->value ?? "";
                    $url = collect($buttonSection->section_parameters)->where("section_type_parameter.code","=","url")->first()->value ?? "";
                    ?>

                    @if(!is_null($url && $buttonText))
                        <div class="row">
                            <div class="col-12 text-center">
                                <a class="banner-button" href="{{$url}}">{{$buttonText}}<span style="margin-right: 5px"><i class="fa fa-arrow-right" aria-hidden="true"></i></span></a>
                            </div>
                        </div>
                    @endif
                    {{--</div>--}}

                    {{--{!!  collect($buttonSection->section_parameters)->first()->value !!}--}}
                @endif
            </div>

        </div>
    </div>
    @endif
    </div>

    <script>
        $(document).ready(function(){
            $('[data-toggle="tooltip"]').tooltip()
            $(".sticky-menu-wrapper").sticky({topSpacing:0});
            $('.sticky-menu-wrapper').on('sticky-start', function() {
                $(".top-bar").css("width", "100%");
/*              $('#sticky-menu').addClass("menu-default-sticky");
                $("#main-menu > #navbarNavDropdown > ul").addClass("navbar-nav-colored");
                $("#main-menu").addClass("menu-colored");
                //  Logo
                $("#logo-colored").show();
                $("#logo-white").hide();
                //  Logo XS
                $("#logo-colored-xs").show();
                $("#logo-white-xs").hide();*/
            });
            $('.sticky-menu-wrapper').on('sticky-end', function() {
                $(".top-bar").css("width", "initial");
/*              $('#sticky-menu').removeClass("menu-default-sticky");
                $("#main-menu > #navbarNavDropdown > ul").removeClass("navbar-nav-colored");
                $("#main-menu").removeClass("menu-colored");
                //  Logo
                $("#logo-white").show();
                $("#logo-colored").hide();
                //  Logo XS
                $("#logo-white-xs").show();
                $("#logo-colored-xs").hide();*/
            });
        });

        function messageLoginLevels(){
            $('#modalMessageLoginLevels').modal('show');
        }




        $(window).on('scroll', function() {
            var scrollTop = $(this).scrollTop();
            if (scrollTop + $(this).innerHeight() >= this.scrollHeight) {
                // $('#message').text('end reached');
            } else if (scrollTop <= 0) {
                // $("#sticky-wrapper").css("height","130px");
                //$('#message').text('Top reached');
            } else {
                // $('#message').text('');
            }
        });

    </script>