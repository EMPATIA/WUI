<script>
    if (localStorage.getItem('sidebarPosition') === '2')
        localStorage.setItem('nextSidebar', localStorage.getItem('currentSidebar'));
    else {
        localStorage.removeItem('nextSidebar');
    }
    localStorage.setItem('currentSidebar', 'entity');
    localStorage.setItem('previousSidebar', 'private');
    localStorage.setItem('sidebarPosition', 1)
</script>

<div class="side-menu-wrapper">
    <ul class="sidebar-menu sidebar-menu-css">
        <!-- Menu Title -->
        <li class="main-menu-title">
            <div class="menu-border-bottom">{{ trans('privateSidebar.entity') }}</div>

            <!-- Sub Menu -->
            <ul class="sub-menu-wrapper">
                <li class="menu-wrapper">
                    <div class="@if($active=='details') menu-active @endif">
                        <a href="{{ action("EntitiesDividedController@showEntity") }}">{{ trans('privateSidebar.details') }}</a>
                    </div>
                </li>
                <li class="menu-wrapper">
                    <div class="@if($active=='template') menu-active @endif">
                        <a href="{{ action("EntitiesDividedController@showLayouts") }}">{{ trans('privateSidebar.entity_template') }}</a>
                    </div>
                </li>
                <li class="menu-wrapper">
                    <div class="@if($active=='languages') menu-active @endif">
                        <a href="{{ action("EntitiesDividedController@showLanguages") }}">{{ trans('privateSidebar.entity_languages') }}</a>
                    </div>
                </li>
                <li class="menu-wrapper">
                    <div class="@if($active=='authMethods') menu-active @endif">
                        <a href="{{ action("EntitiesDividedController@showAuthMethods") }}">{{ trans('privateSidebar.entity_authentication_methods') }}</a>
                    </div>
                </li>
                <li class="menu-wrapper">
                    <div class="@if($active=='dashboard') menu-active @endif">
                        <a href="{{action('EntitiesDividedController@manageDashBoardElements')}}">{{ trans('privateSidebar.dashboard') }}</a>
                    </div>
                </li>
                @if(!empty($entityKey))
                    <li class="menu-wrapper">
                        <div class="@if($active=='notifications') menu-active @endif">
                            <a href="{{action('EntitiesController@showNotifications', $entityKey)}}">{{ trans('privateSidebar.notifications') }}</a>
                        </div>
                    </li>
                    <li class="menu-wrapper">
                        <div class="@if($active=='entityLevels') menu-active @endif">
                            <a href="{{action('EntityLoginLevelsController@index', $entityKey)}}">{{ trans('privateSidebar.entity_login_levels') }}</a>
                        </div>
                    </li>
                @endif
                <li class="menu-wrapper">
                    <div class="@if($active=='parameterUserTypes') menu-active @endif">
                        <a href="{{ action("ParameterUserTypesController@index") }}" id="userTypeParams">{{ trans('privateSidebar.user_type_params') }}</a>
                    </div>
                </li>
            </ul>
        </li>
    </ul>
</div>
