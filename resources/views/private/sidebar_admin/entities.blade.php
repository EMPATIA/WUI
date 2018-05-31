<div class="side-menu-wrapper">
    <ul class="sidebar-menu sidebar-menu-css">
        <!-- Menu Title -->
        <li class="main-menu-title">
            <div class="menu-border-bottom">{{ trans('privateSidebar.entity') }}</div>
            <!-- Sub Menu -->
            <ul class="sub-menu-wrapper">
                <li class="menu-wrapper">
                    <div @if($active=='details') class="menu-active" @endif>
                        <a href="{{ action("EntitiesController@show", $entityKey) }}">{{ trans('privateSidebar.details') }}</a>
                    </div>
                </li>
                <li class="menu-wrapper">
                    <div @if($active=='layouts') class="menu-active" @endif>
                        <a href="{{ action("EntitiesController@showLayouts", $entityKey) }}">{{ trans('privateSidebar.templates') }}</a>
                    </div>
                </li>
                <li class="menu-wrapper">
                    <div @if($active=='sites') class="menu-active" @endif>
                        <a  href="{{ action("EntitiesController@showSites", $entityKey) }}">{{ trans('privateSidebar.sites') }}</a>
                    </div>
                </li>
                <li class="menu-wrapper">
                    <div @if($active=='languages') class="menu-active" @endif>
                        <a href="{{ action("EntitiesController@showLanguages", $entityKey) }}">{{ trans('privateSidebar.entity_languages') }}</a>
                    </div>
                </li>
                <li class="menu-wrapper">
                    <div @if($active=='managers') class="menu-active" @endif>
                        <a href="{{ action("EntitiesController@showManagers", $entityKey) }}">{{ trans('privateSidebar.entity_managers') }}</a>
                    </div>
                </li>
                <li class="menu-wrapper">
                    <div @if($active=='auth') class="menu-active" @endif>
                        <a href="{{ action("EntitiesController@showAuthMethods", $entityKey) }}">{{ trans('privateSidebar.entity_authentication_methods') }}</a>
                    </div>
                </li>
                <li class="menu-wrapper">
                    <div @if($active=='modules') class="menu-active" @endif>
                        <a href="{{ action("EntitiesController@addEntityModule", $entityKey) }}">{{ trans('privateSidebar.entity_modules') }}</a>
                    </div>
                </li>
            </ul>
        </li>
    </ul>
</ul>



<script>
    if(localStorage.getItem('sidebarPosition') == 2)
        localStorage.setItem('nextSidebar', localStorage.getItem('currentSidebar'));
    localStorage.setItem('currentSidebar', 'entities');
    localStorage.setItem('previousSidebar', 'private');
    localStorage.setItem('sidebarPosition', 1)
</script>