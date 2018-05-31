<script>
    if(localStorage.getItem('sidebarPosition') == 2)
        localStorage.setItem('nextSidebar', localStorage.getItem('currentSidebar'));
    else
        localStorage.removeItem('nextSidebar');

    localStorage.setItem('currentSidebar', 'cmHomePagesType');
    localStorage.setItem('previousSidebar', 'private');
    localStorage.setItem('sidebarPosition', 1)
</script>

<div class="side-menu-wrapper">
    <ul class="sidebar-menu sidebar-menu-css">
        <!-- Menu Title -->
        <li class="main-menu-title">
            <div class="menu-border-bottom">
                {{ trans('privateSidebar.content_home_pages_type') }}
            </div>

            <!-- Sub Menu -->
            <ul class="sub-menu-wrapper">
                <li class="menu-wrapper">
                    <div class="@if($active=='details') menu-active @endif">
                        <a href="{{ action("HomePageTypesController@show", $homePageTypeKey) }}">{{ trans('privateSidebar.details') }}</a>
                    </div>
                </li>
                <li class="menu-wrapper">
                    <div class="@if($active=='children') menu-active @endif">
                        <a href="{{ action("HomePageTypesController@showHomePageTypesChildren", $homePageTypeKey) }}">{{ trans('privateSidebar.content_home_pages_type_children') }}</a>
                    </div>
                </li>
            </ul>
        </li>
    </ul>
</div>