s<ul class="sidebar-menu side-menu-main">
    <ul class="sidebar-menu">
        <li @if (Route::getCurrentRoute()->getName() == 'private') class="active" @endif><a href="{{ route("private") }}"><i class="fa fa-home"></i><span> {{ trans('privateSidebar.home') }}</span></a></li>
    </ul>
    <li><a href="#" id="back" onclick="go(this)"><i class="fa fa-arrow-left"></i><span> {{ trans('privateSidebar.back') }}</span></a></li>


    <li>
        <div class="menu-border-bottom">
            {{ trans('privateSidebar.entity_groups') }}
        </div>
        <ul  class="sub-menu-wrapper">
            @if(verifyUserPermissionsCrud('wui', 'entity_groups'))
                <li class="treeview">
                    <div class="menu-wrapper"><a @if($active=='entity_groups_list') class="menu-active"  @endif href="{{ action("EntityGroupsController@index",["groupTypeKey" => $variableToView] ) }}">{{ trans('privateSidebar.entity_groups_list') }}</a></div>
                </li>
            @endif
            <li class="treeview">
                <div class="menu-wrapper"><a @if($active=='entity_groups_tree') class="menu-active"  @endif href="{{ action("EntityGroupsController@showGroups", ["groupTypeKey" => $variableToView]) }}">{{ trans('privateSidebar.entity_groups_tree') }}</a></div>
            </li>
        </ul>
    </li>

</ul>