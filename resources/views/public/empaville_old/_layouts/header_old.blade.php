<header class="main-header">

    <nav class="navbar navbar-static-top" role="navigation" style=" background: linear-gradient(to right, #9cc34e , #d8dd41); box-shadow: rgba(0,0,0,.5) 0 1px 5px;">
        <div class="container">
            <div class="navbar-header">
                <a href="/">
                    <span class="logo-lg"><img src="{{ asset('images/logo_white.png') }}" style="width: 120px"/></span>
                </a>
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse"
                        data-target="#navbar-collapse">
                    <i class="fa fa-bars"></i>
                </button>
            </div>


            @if(Session::has('X-AUTH-TOKEN'))
                <div class="navbar-custom-menu">
                    <ul class="nav navbar-nav">
                        <!-- User Account: style can be found in dropdown.less -->
                        <li class="dropdown user user-menu">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true" style="padding-top: 17px; height: 50px;">
                                @if(Session::get('user')->photo_id > 0)
                                    <img class="user-image" src="{{URL::action('FilesController@download',[Session::get('user')->photo_id, Session::get('user')->photo_code, 1])}}">
                                @else
                                    <img src="/images/icon-user-default-160x160.png" class="user-image" alt="User Image">
                                @endif
                                <div style="color: black!important; float: right; padding-top: 2px;">{{Session::get('user')->name}}</div>
                            </a>

                            <ul class="dropdown-menu">
                                <!-- User image -->
                                <li class="user-header" style="background-color: #d8dd41;">
                                    @if(Session::get('user')->photo_id > 0)
                                        <img class="img-circle" src="{{URL::action('FilesController@download',[Session::get('user')->photo_id, Session::get('user')->photo_code, 1])}}">
                                    @else
                                        <img src="/images/icon-user-default-160x160.png" class="img-circle">
                                    @endif

                                    <div style="color: black; padding-top: 15px;">
                                        {{Session::get('user')->name}} <br>
                                        <small>Member since {{Session::get('user')->created_at}}</small>
                                    </div>
                                </li>
                                <!-- Menu Footer-->
                                <li class="user-footer">
                                    <div class="pull-left">
                                        <a href="{{ action('PublicUsersController@index') }}" class="btn btn-default btn-flat">Profile</a>
                                    </div>
                                    <div class="pull-right">
                                        <a href="{{ action('AuthController@logout') }}" class="btn btn-default btn-flat">Sign out</a>
                                    </div>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
                <div id="navbar-collapse" class="navbar-collapse pull-right collapse" aria-expanded="false" >
                    <ul class="nav navbar-nav" >
                        {!! ONE::getAccessMenu() !!}

                    </ul>
                </div>
            @else
            <div id="navbar-collapse" class="navbar-collapse pull-right collapse" aria-expanded="false" >
                <ul class="nav navbar-nav">

                    {!! ONE::getAccessMenu() !!}
                    <!-- Options Register/Login -->
                    {{--<li>--}}
                        {{--<a href="{{ action('AuthController@register') }}" style="color: black!important;padding-top: 17px;">--}}
                            {{--{{trans('public.register')}}--}}
                        {{--</a>--}}
                    {{--</li>--}}
                    <li>
                        <a href="{{ action('AuthController@login') }}" style="padding-top: 17px;">
                            {{trans('public.login')}}
                        </a>
                    </li>
                </ul>
            </div>
            @endif
        </div>
    </nav>
</header>