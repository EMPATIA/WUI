<header id="headerPage">
    <nav class="navbar top-mian-navbar noBottomMargin xs-hidden hidden-xs hidden-sm">
        <div class="container-fluid topBar-header">
            <div style="max-width: 95%; margin:auto;">
                <div class="userLogin-inline">
                    <div class="nav navbar-nav">
                    @if(Session::has('X-AUTH-TOKEN'))
                        <!-- User Account Menu -->
                            <ul class="nav navbar-nav userLogin">
                                <li class="dropdown user user-menu">
                                    <!-- Menu Toggle Button -->
                                    <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                                        @if(Session::get('user')->photo_id > 0)
                                            <img class="user-image" src="{{URL::action('FilesController@download',[Session::get('user')->photo_id, Session::get('user')->photo_code, 1])}}">
                                        @else
                                            <img src="{{asset('images/icon-user-default-160x160.png')}}" class="user-image" alt="User Image">
                                        @endif
                                        <span id="login" class="hidden-xs hidden-sm">{{Session::get('user')->name}}</span>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <!-- User image -->
                                        <li class="user-header" >
                                            @if(Session::get('user')->photo_id > 0)
                                                <img class="user-image" src="{{URL::action('FilesController@download',[Session::get('user')->photo_id, Session::get('user')->photo_code, 1])}}">
                                            @else
                                                <img src="{{asset('images/icon-user-default-160x160.png')}}" class="user-image">
                                            @endif
                                            <div style="color: black; padding-top: 15px;">
                                                {{Session::get('user')->name}} <br>
                                                <small>Member since: {{date('Y-m-d', strtotime(Session::get('user')->created_at))}}</small>
                                            </div>
                                        </li>
                                        <!-- Menu Footer-->
                                        <li class="user-footer">
                                            {{--<div class="pull-left">--}}
                                            {{--<a href="{{ action('PublicUsersController@index') }}" class="btn btn-default btn-flat">Profile</a>--}}
                                            {{--</div>--}}
                                            <div class="pull-right">
                                                <a href="{{ action('PublicUsersController@edit',['userKey' => Session::get('user')->user_key,'f' => 'user']) }}" class="btn btn-default btn-flat">{{trans('empatia.user_profile')}}</a>
                                            </div>
                                            @if (One::asPermission('manager'))
                                                <div class="pull-right">
                                                    <a href="{{ url('/private') }}" class="btn btn-default btn-flat">{{trans('home.backOffice')}}</a>
                                                </div>
                                            @endif
                                            <div class="pull-right">
                                                <a href="{{ action('AuthController@logout') }}" class="btn btn-default btn-flat">Sign out</a>
                                            </div>
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                        @else
                            <div class="login-btn-div">
                                <a href="{{ action('AuthController@register') }}">{{trans('empatiaLayout.registration')}}</a>
                                <span>|</span>
                                <a href="{{ action('AuthController@login') }}">{{trans('empatiaLayout.login')}}</a>
                            </div>
                            {{--<div id="registration-btn" style=""><a href="./">{{trans('home.registration')}}</a></div>--}}
                            {{--<div id="login-btn" style=""><a href="{{ action('AuthController@login') }}">{{trans('home.login')}}</a></div>--}}

                        @endif
                    </div>
                </div>
                <div id="lang" class="nav navbar-nav">
                    @include('public.empatia._layouts.languages')
                </div>
            </div>
        </div>
    </nav>

    <nav class="navbar main-nav-bar" id="mainNavBar">

        <div class="container-fluid topBarNavigation">
            <div style="max-width: 95%; margin:auto;">

                <div class="navbar-header">
                    <div id="home-logo" class="col-sm-3 ">
                        <a href='{{url('/')}}'><img class="logo" id='logo' alt="Logo" src="{{asset('images/empatia/empatia-desc.png')}}" /></a>
                    </div>
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
                        <span class="icon-bar" style="background-color: gray"></span>
                        <span class="icon-bar" style="background-color: gray"></span>
                        <span class="icon-bar"style="background-color: gray"></span>
                    </button>
                </div>
                <div class="collapse navbar-collapse" id="myNavbar">
                    {{--Medium and large screen--}}
                    <div id="navbar-collapse" class="hidden-xs hidden-sm">
                        <ul id='menu' class="nav navbar-nav navbar-right">
                            <div class="nav navbar-nav">
                                {!! ONE::getAccessMenu() !!}
                            </div>
                        </ul>
                    </div>

                    {{--Extra-small and small screens--}}
                    <div id="navbar-collapse" class="visible-xs visible-sm">
                        <ul id='menu' class="nav navbar-nav navbar-right">
                            @include('public.empatia._layouts.languages')
                            <div class="topBarCollapsed">
                            @if(Session::has('X-AUTH-TOKEN'))
                                <!-- User Account Menu -->
                                    <div class="user-menu">
                                        <li class="dropdown user ">
                                            <!-- Menu Toggle Button -->
                                            <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                                                @if(Session::get('user')->photo_id > 0)
                                                    <img class="user-image" src="{{URL::action('FilesController@download',[Session::get('user')->photo_id, Session::get('user')->photo_code, 1])}}">
                                                @else
                                                    <img src="{{asset('images/icon-user-default-160x160.png')}}" class="user-image" alt="User Image">
                                                @endif
                                                <span id="login" class="">{{Session::get('user')->name}}</span>
                                            </a>
                                            <ul class="dropdown-menu">
                                                <!-- User image -->
                                            {{--<li class="user-header" >--}}
                                            {{--@if(Session::get('user')->photo_id > 0)--}}
                                            {{--<img class="user-image" src="{{URL::action('FilesController@download',[Session::get('user')->photo_id, Session::get('user')->photo_code, 1])}}">--}}
                                            {{--@else--}}
                                            {{--<img src="{{asset('images/icon-user-default-160x160.png')}}" class="user-image">--}}
                                            {{--@endif--}}
                                            {{--<div style="color: black; padding-top: 15px;">--}}
                                            {{--{{Session::get('user')->name}} <br>--}}
                                            {{--<small>Member since {{Session::get('user')->created_at}}</small>--}}
                                            {{--</div>--}}
                                            {{--</li>--}}
                                            <!-- Menu Footer-->
                                                <li>
                                                    {{--<div class="pull-left">--}}
                                                    {{--<a href="{{ action('PublicUsersController@index') }}" class="btn btn-default btn-flat">Profile</a>--}}
                                                    {{--</div>--}}
                                                    <a href="{{ action('PublicUsersController@edit',['userKey' => Session::get('user')->user_key,'f' => 'user']) }}" class="text-left">{{trans('empatia.user_profile')}}</a>
                                                    @if (One::asPermission('manager'))
                                                        <a href="{{ url('/private') }}" class="text-left">{{trans('home.backOffice')}}</a>
                                                    @endif
                                                    <a href="{{ action('AuthController@logout') }}" class="text-left">Sign out</a>
                                                </li>
                                            </ul>
                                        </li>
                                    </div>
                                @else
                                    {{--<div id="registration-btn" style=""><a href="./">{{trans('home.registration')}}</a></div>--}}
                                    {{--<div id="login-btn" style=""><a href="{{ action('AuthController@login') }}">{{trans('home.login')}}</a></div>--}}
                                    <div class="login-btn-div">
                                        <a href="{{ action('AuthController@register') }}">{{trans('empatiaLayout.registration')}}</a>
                                        <a href="{{ action('AuthController@login') }}">{{trans('empatiaLayout.login')}}</a>
                                    </div>
                                @endif
                            </div>
                            {!! ONE::getAccessMenu() !!}

                        </ul>
                    </div>


                </div>
            </div>
        </div>
    </nav>
</header>

<script>
    $(document).ready(function() {
        //change the integers below to match the height of your upper dive, which I called
        //banner.  Just add a 1 to the last number.  console.log($(window).scrollTop())
        //to figure out what the scroll position is when exactly you want to fix the nav
        //bar or div or whatever.  I stuck in the console.log for you.  Just remove when
        //you know the position.
        $(window).scroll(function () {
            navBarFixed();
        });
        navBarFixed();
    });

    $(window).resize(function(){
        navBarFixed();
    });

    function navBarFixed(){
        if ( $(window).width() > 768) {
            if ($(window).scrollTop() > 35) {
                var headerFooterHeight = $('#headerPage').height();
                $('body > div.wrapper').css('padding-top', headerFooterHeight);
                $('#mainNavBar').addClass('navbar-fixed-top');
            }
            if ($(window).scrollTop() < 36) {
                $('body > div.wrapper').removeAttr('style');
                $('#mainNavBar').removeClass('navbar-fixed-top');
            }
        }else{
            $('body > div.wrapper').removeAttr('style');
            $('#mainNavBar').addClass('navbar-fixed-top');
        }
    }

</script>

