<script>

    $(".pager").css('display', 'block');

    if(localStorage.getItem('sidebarPosition') == 2)
        localStorage.setItem('nextSidebar', localStorage.getItem('currentSidebar'));
    localStorage.setItem('currentSidebar', 'manager');
    localStorage.setItem('previousSidebar', 'private');
    localStorage.setItem('sidebarPosition', 1)
</script>

<div class="side-menu-wrapper">
    <ul class="sidebar-menu sidebar-menu-css">
        <!-- Menu Title -->
        <li class="main-menu-title">
            <div class="menu-border-bottom">
                {{ trans('privateSidebar.user_detail') }}
            </div>

            <!-- Sub Menu -->
            <ul class="sub-menu-wrapper">
                <li class="treeview">
                    <div class="menu-wrapper"><a @if($active=='details') class="menu-active"  @endif href="{{ action("UsersController@show", ["userKey" => $userKey]) }}">{{ trans('privateSidebar.user_details') }}</a></div>
                </li>

                <li class="treeview">
                    <div class="menu-wrapper"><a @if($active=='menuPermissions') class="menu-active"  @endif href="{{ action("PermissionsController@indexUsers", ["userKey" => $userKey]) }}">{{ trans('privateSidebar.permissions') }}</a></div>
                </li>
            </ul>
        </li>
    </ul>
</div>
