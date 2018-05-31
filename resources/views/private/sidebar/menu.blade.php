<div class="side-menu-wrapper">
    <ul class="sidebar-menu sidebar-menu-css">
        <!-- Menu Title -->
        <li class="main-menu-title">
            <div class="menu-border-bottom">
                {{ trans('privateSidebar.menu') }}
            </div>

            <!-- Sub Menu -->
            <ul class="sub-menu-wrapper">
                <li class="menu-wrapper">
                    <div class="@if($active=='accessMenu') menu-active @endif">
                        <a href="{{ action('AccessMenusController@show', $accessM) }}">
                            {{ trans('privateSidebar.details') }}
                        </a>
                    </div>
                </li>
                <li class="menu-wrapper">
                    <div class="@if($active=='indexTree') menu-active @endif">
                        <a href="{{ action('AccessMenusController@showMenus', $accessM) }}">
                            {{ trans('privateSidebar.menu_menus') }}
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
    localStorage.setItem('currentSidebar', 'menu');
    localStorage.setItem('previousSidebar', 'private');
    localStorage.setItem('sidebarPosition', 1)
</script>