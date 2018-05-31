<div class="side-menu-wrapper">
    <ul class="sidebar-menu sidebar-menu-css">
        <!-- Menu Title -->
        <li class="main-menu-title">
            <div class="menu-border-bottom">
            {{ trans('privateSidebar.mp') }}
            </div>

            <!-- Sub Menu -->
            <ul class="sub-menu-wrapper">
                <li class="menu-wrapper">
                    <div class="@if($active=='details') menu-active @endif">
                        <a href="{{ action('MPsController@show', $mp->mp_key ?? $mpKey) }}">
                            {{ trans('privateSidebar.details') }}
                        </a>
                    </div>
                </li>
                <li class="menu-wrapper">
                    <div class="@if($active=='configurations') menu-active @endif">
                        <a href="{{ action('MPsController@showConfigurations', $mp->mp_key ?? $mpKey) }}">
                            {{ trans('privateSidebar.configurations') }}
                        </a>
                    </div>
                </li>
            </ul>
        </li>
    </ul>
</div>

<script>
    if(localStorage.getItem('sidebarPosition') == 2)
        localStorage.setItem('nextSidebar', localStorage.getItem('currentSidebar'));
    localStorage.setItem('currentSidebar', 'mp_configurations');
    localStorage.setItem('previousSidebar', 'private');
    localStorage.setItem('sidebarPosition', 1)
</script>