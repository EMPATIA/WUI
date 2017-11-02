<script>
    if(localStorage.getItem('sidebarPosition') == 2)
        localStorage.setItem('nextSidebar', localStorage.getItem('currentSidebar'));
    else{
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
            <div class="menu-border-bottom">
                {{ trans('privateSidebar.loginLevels') }}
            </div>

            <!-- Sub Menu -->
            <ul class="sub-menu-wrapper">
                <li class="menu-wrapper">
                    <div class="@if($active=='details') menu-active @endif">
                        <a href="{{ action('EntityLoginLevelsController@show', ['login_level_key' =>isset($loginLevel->login_level_key) ? $loginLevel->login_level_key : (isset($loginLevelKey) ? $loginLevelKey : null),'entity_key' => $entityKey ?? null]) }}">
                            {{ trans('privateSidebar.details') }}
                        </a>
                    </div>
                </li>
                <li class="menu-wrapper">
                    <div class="@if($active=='parameters') menu-active @endif">
                        <a href="{{ action('EntityLoginLevelsController@showParameters', ['login_level_key' => $loginLevel->login_level_key ?? null,'entity_key' => $entityKey ?? null]) }}">
                            {{ trans('privateSidebar.user_parameters') }}
                        </a>
                    </div>
                </li>
            </ul>
        </li>
    </ul>
</div>