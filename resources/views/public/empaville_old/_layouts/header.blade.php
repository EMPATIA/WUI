<header class="main-header">
    <nav class="navbar navbar-static-top" style=" background: linear-gradient(to right, #9cc34e , #d8dd41); box-shadow: rgba(0,0,0,.5) 0 1px 5px;">
        <div class="container">
            <div class="navbar-header">
                <a class="navbar-brand" href="/"><img src="{{ asset('images/logo_white.png') }}" style="margin-top: -5px"/></a>
                <button data-target="#navbar-collapse" data-toggle="collapse" class="navbar-toggle collapsed" type="button">
                    <i class="fa fa-bars"></i>
                </button>
            </div>

            <!-- Collect the nav links, forms, and other content for toggling -->
            <div id="navbar-collapse" class="collapse navbar-collapse pull-left">
                <ul class="nav navbar-nav">
                    {!! ONE::getAccessMenu() !!}
                    <li class="dropdown visible-sm visible-xs">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">&nbsp;{{ONE::getAppLanguageName()}}<b class="caret"></b></a>
                        <ul class="dropdown-menu">
                            @foreach(ONE::getAllLanguages() as $language)
                                <li>
                                    <a href="#" class="lang" onclick="updateLanguage('{{$language->code}}')"  {{ONE::getAppLanguageCode() == $language->code? 'selected' : ''}}>{{$language->name}}</a>
                                </li>
                            @endforeach
                        </ul>
                    </li>
                </ul>
            </div>

            <div class="navbar-custom-menu">
                <ul class="nav navbar-nav">
                    <li class="dropdown hidden-sm hidden-xs">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">&nbsp;{{ONE::getAppLanguageName()}}<b class="caret"></b></a>
                        <ul class="dropdown-menu">
                            @foreach(ONE::getAllLanguages() as $language)
                                <li>
                                    <a href="#" class="lang" onclick="updateLanguage('{{$language->code}}')"  {{ONE::getAppLanguageCode() == $language->code? 'selected' : ''}}>{{$language->name}}</a>
                                </li>
                            @endforeach
                        </ul>
                    </li>
                @if(Session::has('X-AUTH-TOKEN'))
                    <!-- User Account Menu -->
                        <li class="dropdown user user-menu">
                            <!-- Menu Toggle Button -->
                            <a data-toggle="dropdown" class="dropdown-toggle" href="#" >
                                @if(Session::get('user')->photo_id > 0)
                                    <img class="user-image" src="{{URL::action('FilesController@download',[Session::get('user')->photo_id, Session::get('user')->photo_code, 1])}}">
                                @else
                                    <img src="{{asset('images/icon-user-default-160x160.png')}}" class="user-image" alt="User Image">
                                @endif
                                <span class="hidden-sm hidden-xs">{{Session::get('user')->name}}</span>
                            </a>
                            <ul class="dropdown-menu">
                                <!-- User image -->
                                <li class="user-header" style="background-color: #d8dd41; border: 0px;">
                                    @if(Session::get('user')->photo_id > 0)
                                        <img class="img-circle" src="{{URL::action('FilesController@download',[Session::get('user')->photo_id, Session::get('user')->photo_code, 1])}}">
                                    @else
                                        <img src="{{asset('images/icon-user-default-160x160.png')}}" class="img-circle">
                                    @endif

                                    <div style="color: black; padding-top: 15px;">
                                        {{Session::get('user')->name}} <br>
                                        <small>Member since {{date('Y-m-d', strtotime(Session::get('user')->created_at))}}</small>
                                    </div>
                                </li>
                                <!-- Menu Footer-->
                                <li class="user-footer">
                                    {{--<div class="pull-left">--}}
                                        {{--<a href="{{ action('PublicUsersController@index') }}" class="btn btn-default btn-flat">Profile</a>--}}
                                    {{--</div>--}}
                                    <div class="pull-right">
                                        <a href="{{ action('AuthController@logout') }}" class="btn btn-default btn-flat">Sign out</a>
                                    </div>
                                </li>
                            </ul>
                        </li>
                    @else
                        <li>
                            <a href="{{ action('AuthController@login') }}" style="padding-top: 17px;">
                                {{trans('public.login')}}
                            </a>
                        </li>

                    @endif

                </ul>
            </div>

            <!-- /.navbar-custom-menu -->
        </div>
        <!-- /.container-fluid -->
    </nav>
</header>

<script>
    function updateLanguage(langCode){

        $.ajax({
            url: '{{action("OneController@setLanguage")}}',
            method: 'POST',
            data: {
                langCode: langCode,
                _token: "{{ csrf_token()}}"
            },
            success: function(action){
                window.location = '/';
            },
            error: function(msg){
                console.log(msg);
                alert('failure');
            }
        });
    }
</script>