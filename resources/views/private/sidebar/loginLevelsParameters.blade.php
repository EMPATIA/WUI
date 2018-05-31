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
                        <a href="{{ action('LoginLevelsController@show', ['levelParameterKey' => $levelParameterKey ?? null]) }}">
                            {{ trans('privateSidebar.details') }}
                        </a>
                    </div>
                </li>
                <li class="menu-wrapper">
                    <div class="@if($active=='parameters') menu-active @endif">
                        <a href="{{ action('LoginLevelsController@showConfigurations', ['siteKey' => $siteKey, 'levelParameterKey' => $levelParameterKey]) }}">
                            {{ trans('privateSidebar.configurations') }}
                        </a>
                    </div>
                </li>
            </ul>
        </li>
    </ul>
</div>
<script>
    localStorage.setItem('previousSidebar', 'site');
    localStorage.setItem('currentSidebar', 'loginLevelsParameters');
    localStorage.setItem('sidebarPosition', 2)
</script>