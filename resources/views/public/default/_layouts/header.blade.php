<header>
    <nav class="navbar navbar-top-login">
        <div class="container">
            @include('public.default._layouts.languages')
            <div class="pull-right">
                <ul class="nav navbar-nav navbar-login">
                @if(Session::has('X-AUTH-TOKEN'))
                    <!-- User Account Menu -->
                        <li class="dropdown">
                            <!-- Menu Toggle Button -->
                            @if(Session::get('user')->photo_id > 0)
                                <a data-toggle="dropdown" class="dropdown-toggle loggedUserNavBar" href="#">
                                    <img src="{{ URL::action('FilesController@download',[Session::get('user')->photo_id, Session::get('user')->photo_code, 1]) }}">
                                    <span class="hidden-xs">{{ Session::get('user')->name }}</span>

                                </a>
                            @else
                                <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                                    <span class="fa fa-user"></span>
                                    <span class="hidden-xs">{{ Session::get('user')->name }}</span>
                                </a>
                            @endif
                            <ul id="dropdownLoginID" class="dropdown-menu dropdownLogin">
                                <li class="dropdownLoginUserInfo  hidden-xs">
                                    <ul class="dropdownLoginUsername">
                                        <!-- User image -->
                                        <li class="">
                                            <div class="media">
                                                <div class="profile-pic-container">
                                                    @if(Session::get('user')->photo_id > 0)
                                                        <img src="{{ URL::action('FilesController@download',[Session::get('user')->photo_id, Session::get('user')->photo_code, 1]) }}">
                                                    @else
                                                        <div>
                                                            <span class="fa fa-user"></span>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="profile-username-container">
                                                    <h3 class="profile-username-container">{{ Session::get('user')->name }}</h3>
                                                </div>
                                            </div>
                                        </li>
                                    </ul>
                                </li>
                                <!-- Menu Footer-->
                                <li>
                                    <ul class="dropDownLoginActionsList">
                                        <li class="dropDownLoginAction">
                                            <a href="{{ action('PublicUsersController@edit',['userKey' => Session::get('user')->user_key,'f' => 'user']) }}" class="">{{trans('defaultLayout.user_profile')}}</a>
                                        </li>
                                        @if (One::asPermission('manager'))
                                            <li class="dropDownLoginAction">
                                                <a href="{{ url('/private') }}" class="">{{trans('defaultLayout.back_office')}}</a>
                                            </li>
                                        @endif
                                        <li class="dropDownLoginAction">
                                            <a href="{{ action('AuthController@logout') }}" class="">{{trans('defaultLayout.sign_out')}}</a>
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                        </li>
                    @else
                        <div class="login-btn" style="">
                            <a href="{{ action('AuthController@register') }}">{{trans('defaultLayout.registration')}}</a>
                            <span>|</span>
                            <a href="{{ action('AuthController@login') }}">{{trans('defaultLayout.login')}}</a>
                        </div>
                    @endif
                </ul>
            </div>
        </div>
    </nav>
    <nav class="navbar navbar-top-menus">

        <div class="container">
            <div class="col-xs-3 home-logo">
                <a href='{{url('/')}}'><img class="logo" id='logo' alt="Logo" src="{{asset('images/empatia/empatia_logo.svg')}}" height="45px" /></a>
            </div>
            <div class="navbar-header">
                <button type="button" class="navbar-toggle navbar-collapse-button" data-toggle="collapse" data-target="#navBarMenu">
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                </button>
            </div>
            <div class="collapse navbar-collapse" id="navBarMenu">
                <ul class="nav navbar-nav navbar-right">
                    {!! ONE::getAccessMenu() !!}
                </ul>
            </div>
        </div>
    </nav>
</header>
