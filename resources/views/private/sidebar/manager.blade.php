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
                    <div class="menu-wrapper"><a @if($active=='details') class="menu-active"  @endif href="{{ action("UsersController@show", ["userKey" => $userKey, 'role' => $role]) }}">{{ trans('privateSidebar.user_details') }}</a></div>
                </li>

                <li class="treeview">
                    <div class="menu-wrapper"><a @if($active=='permissions') class="menu-active"  @endif href="{{ action("UsersController@showPermissions", ["userKey" => $userKey, 'role' => $role]) }}">{{ trans('privateSidebar.user_permissions') }}</a></div>
                </li>
            
                @if(ONE::verifyUserPermissionsCrud('cm', 'personal_dynamic_be_menu'))
                    <li class="treeview">
                        <div class="menu-wrapper">
                            <a @if($active=='personal_dynamic_be_menu') class="menu-active" @endif href="{{ action("UserBEMenuController@userIndex",["userKey" => $userKey]) }}">
                                {{ trans('privateSidebar.user_dynamic_be_menu') }}
                            </a>
                        </div>
                    </li>
                @endif
            </ul>
        </li>
    </ul>
</div>
