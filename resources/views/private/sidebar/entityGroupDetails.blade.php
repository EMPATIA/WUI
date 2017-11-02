<div class="side-menu-wrapper">
    <ul class="sidebar-menu sidebar-menu-css">
        <!-- Menu Title -->
        <li class="main-menu-title">
            <div class="menu-border-bottom">
                {{ trans('privateSidebar.entity_groups') }}
            </div>

            <!-- Sub Menu -->
            <ul class="sub-menu-wrapper">
                <li class="treeview">
                    <div class="menu-wrapper"><a @if($active=='entity_group_details') class="menu-active"  @endif href="{{ action("EntityGroupsController@show", ["entityGroupKey" => $entityGroupKey] ) }}">{{ trans('privateSidebar.entity_groups_details') }}</a></div>
                </li>
                @if(ONE::verifyUserPermissionsCrud('wui', 'entity_groups_users'))
                    <li class="treeview">
                        <div class="menu-wrapper"><a @if($active=='entity_group_users') class="menu-active"  @endif href="{{ action("EntityGroupsController@showUsers", ["entityGroupKey" => $entityGroupKey]) }}">{{ trans('privateSidebar.entity_group_users') }}</a></div>
                    </li>
                @endif
                @if(ONE::verifyUserPermissionsCrud('wui', 'entity_groups_permissions'))
                    <li class="treeview">
                        <div class="menu-wrapper"><a @if($active=='entity_group_permissions') class="menu-active"  @endif href="{{ action("EntityGroupsController@showPermissions", ["entityGroupKey" => $entityGroupKey, "groupTypeKey" => $groupTypeKey]) }}">{{ trans('privateSidebar.entity_group_permissions') }}</a></div>
                    </li>
                @endif
            </ul>
        </li>
    </ul>
</div>

<script>
    if(localStorage.getItem('sidebarPosition') == 2)
        localStorage.setItem('nextSidebar', localStorage.getItem('currentSidebar'));
    localStorage.setItem('currentSidebar', 'entityGroupDetails');
    localStorage.setItem('previousSidebar', 'private');
    localStorage.setItem('sidebarPosition', 1)
</script>
