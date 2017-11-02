<header class="main-header">

    <!-- Logo -->
    <a href="{{ route('public.index') }}" class="logo">
        <img src="{{ asset('images/logo_white.png') }}" alt="Logo">
        <span class="user-profile">
            {!! $value = Session::get('user_role', 'User') !!}
        </span>
    </a>

    <nav role="navigation">
        <!-- Sidebar toggle button-->
        <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
            <span class="sr-only">Toggle navigation</span>
        </a>

        @if(ONE::asPermission('admin') && ONE::isEntity())
            <ul class="back2AdminMenu">
                <li class="back2admin-menu-li color-white">
                    <a onclick="reloadAdminMenu()"><span>{{ trans('privateSidebar.back_to_admin_menu') }}</span></a>
                </li>
            </ul>
        @endif

        @if(ONE::asPermission('admin'))
            <div class="form-group entitySelectMenuLabel float-left">
                <div class="float-left">
                    <label for="sel1" class="select-entity-label">{{ trans('privateSidebar.entity') }}</label>
                </div>
                <div class="menu-wrapper-select-entity">
                    <select id="select-option-entity" class="form-control select-entity select2-searchable" onchange="updateEntity(this)">
                        <option value="">{{ trans('privateSidebar.select_entity') }}</option>
                        @foreach(ONE::getEntities() as $entity)
                            <option value="{{$entity->entity_key}}" {{(ONE::getEntityKey() == $entity->entity_key)? 'selected':''}}>{{$entity->name}}</option>
                        @endforeach
                    </select>
                    {{-- <i class="fa fa-chevron-down"></i>--}}
                </div>
            </div>
    @endif
    <!-- Navbar Right Menu -->
        <div class="navbar-custom-menu">
            <ul class="nav navbar-nav nav-empatia">
                @if(ONE::asPermission('admin') && env("EMPAVILLE_MODE",false))
                    <li class="back2AdminMenu">
                        <li class="back2admin-menu-li">
                            <a href="{{action('CbsController@createWizard',['type' => 'empaville'])}}" class="color-white" >
                                <span class="d-none d-sm-none d-md-none d-lg-inline d-xl-inline">{{ trans('privateHeader.create_empaville') }}</span>
                            </a>
                        </li>
                    </li>
                @endif
                @if(ONE::asPermission('admin'))
                    <li class="back2AdminMenu">
                        <li class="back2admin-menu-li">
                            <a href="{{action("UserAnalysisController@index")}}" class="color-white" >
                                <i class="fa fa-bar-chart color-white" data-toggle="tooltip" title="{{ trans('privateHeader.user_analyses_charts') }}" data-original-title="{{ trans('privateHeader.user_analyses_charts') }}"></i>
                                <span class="d-none d-sm-none d-md-none d-lg-inline d-xl-inline">{{ trans('privateHeader.user_analyses_charts') }}</span>
                            </a>
                        </li>
                    </li>
                @endif

            <!-- Languages Menu -->
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <span class="d-none d-sm-none d-md-inline d-lg-inline d-xl-inline">
                           {{ONE::getAppLanguageName()}}
                        </span>
                        <span class="d-inline d-sm-inline d-md-none d-lg-none d-xl-none text-uppercase">
                            {{ ONE::getAppLanguageCode() }}
                        </span>
                        <i class="fa fa-chevron-down"></i>
                    </a>
                    <ul class="dropdown-menu">
                        <li>
                            <a href="#" class="lang" onclick="updateLanguage('en')" {{ONE::getAppLanguageCode() == 'en' ? 'selected' : ''}}>English</a>
                        </li>
                        @foreach(ONE::getAllLanguages() as $language)
                            @if($language->code != 'en')
                                <li>
                                    <a href="#" class="lang" title=" {{$language->name}}" onclick="updateLanguage('{{$language->code}}')" {{ONE::getAppLanguageCode() == $language->code? 'selected' : ''}}>
                                        {{$language->name}}
                                    </a>
                                </li>
                            @endif
                        @endforeach
                    </ul>
                </li>
                <!-- User Account Menu -->
                <li class="dropdown">
                    <!-- Menu Toggle Button -->
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-user" aria-hidden="true" data-toggle="tooltip" title="{{ isset(Session::get('user')->name) ? Session::get('user')->name : '' }}" data-original-title="{{ isset(Session::get('user')->name) ? Session::get('user')->name : '' }}"></i>
                        <span class="d-none d-sm-none d-md-none d-lg-inline d-xl-inline">
                            {{ isset(Session::get('user')->name) ? Session::get('user')->name : '' }}
                        </span>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a href="{{action("UsersController@showProfile", isset(Session::get('user')->user_key) ? Session::get('user')->user_key : '' )}}"><i class="empatia-icon empatia-user-icon"></i> Profile</a></li>
                        {{--<li><a href="{{action('UsersController@showProfile', Session::get('user')->user_key)}}"><i class="empatia-icon empatia-user-icon"></i> Profile</a></li>--}}
                        <li role="separator" class="dropdown-divider"></li>
                        <li><a href="{{ action('AuthController@logout') }}"><i class="fa fa-sign-out"></i> Sign out</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>
</header>

<script>
    function reloadAdminMenu(){
        updateEntity('');
    }

    function updateEntity(select){
        $.ajax({
            "url": '{{ action("EntitiesController@setEntityKey") }}',
            type: 'post',
            data: {
                entityKey: select.value,
                _token: "{{ csrf_token() }}",
            },
            success: function () {
                window.location = '/private';
            },
        });
    }

    function updateLanguage(langCode){
        $.ajax({
            url: '{{action("OneController@setPrivateLanguage")}}',
            method: 'POST',
            data: {
                langCode: langCode,
                _token: "{{ csrf_token()}}"
            },
            success: function(){
                location.reload();
            },
            error: function(msg){
                console.log(msg);
            }
        });
    }

    $( document ).ready(function() {
        // Bootstrap4 beta - dropdown javascript fix
        $('.dropdown').on('hide.bs.dropdown', function () {
            $('.dropdown-menu').attr("style", "");
        });
        $( window ).resize(function() {
            $('.dropdown-menu').attr("style", "");
        });
    });
</script>
