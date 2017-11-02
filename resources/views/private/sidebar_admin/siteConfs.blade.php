<div class="side-menu-wrapper">
    <ul class="sidebar-menu sidebar-menu-css">
        <!-- Menu Title -->
        <li class="main-menu-title">
            <div class="menu-border-bottom">
                {{ trans('privateSidebar.modules') }}
            </div>
            <!-- Sub Menu -->
            <ul class="sub-menu-wrapper">
                <li class="menu-wrapper">
                    <div @if((isset($active) ? $active : " ")=='details') class="menu-active" @endif>
                        <a href="{{ action("SiteConfGroupController@show", $siteConfGroupKey) }}">{{ trans('privateSidebar.details') }}</a>
                    </div>
                </li>
                <li class="menu-wrapper">
                    <div @if((isset($active) ? $active : "") =='confs') class="menu-active" @endif>
                        <a href="{{ action("SiteConfGroupController@showSiteConfGroupConfigurations", $siteConfGroupKey) }}">{{ trans('privateSidebar.site_confs_group_configurations') }}</a>
                    </div>
                </li>
            </ul>
        </li>
    </ul>
</div>



<script>
    if(localStorage.getItem('sidebarPosition') == 2)
        localStorage.setItem('nextSidebar', localStorage.getItem('currentSidebar'));
    localStorage.setItem('currentSidebar', 'siteConfs');
    localStorage.setItem('previousSidebar', 'private');
    localStorage.setItem('sidebarPosition', 1)
</script>