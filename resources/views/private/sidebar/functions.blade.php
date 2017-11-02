<div class="side-menu-wrapper">
    <ul class="sidebar-menu sidebar-menu-css">
        <!-- Menu Title -->
        <li class="main-menu-title">
            <div class="menu-border-bottom">
                {{ trans('privateSidebar.functions') }}
            </div>
            <!-- Sub Menu -->
            <ul class="sub-menu-wrapper">
                <li class="menu-wrapper">
                    <div class="@if($active=='role') menu-active @endif">
                        <a href="{{ action("RolesController@show", $roleKey) }}">
                            {{ trans('privateSidebar.details') }}
                        </a>
                    </div>
                </li>
                @if(ONE::verifyUserPermissionsCrud('orchestrator', 'role_permissions'))
                    <li class="menu-wrapper">
                        <div class="@if($active=='permissions') menu-active @endif">
                            <a href="{{ action("RolesController@showPermissions", $roleKey) }}">
                                {{ trans('privateSidebar.permissions') }}
                            </a>
                        </div>
                    </li>
                @endif
            </ul>
        </li>
    </ul>
</div>
<script>
    if(localStorage.getItem('sidebarPosition') == 2)
        localStorage.setItem('nextSidebar', localStorage.getItem('currentSidebar'));
    localStorage.setItem('currentSidebar', 'functions');
    localStorage.setItem('previousSidebar', 'private');
    localStorage.setItem('sidebarPosition', 1)
</script>