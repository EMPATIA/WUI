<div class="side-menu-wrapper">
    <ul class="sidebar-menu sidebar-menu-css">
        <!-- Menu Title -->
        <li class="main-menu-title">
            <div class="menu-border-bottom">{{ trans('privateSidebar.sites') }}</div>
            <!-- Sub Menu -->
            <ul class="sub-menu-wrapper">
                <li class="menu-wrapper">
                    <div @if($active=='details') class="menu-active" @endif >
                        <a href="{{action('EntitiesController@showEntitySite', [$entityKey, $siteKey])}}">{{ trans('privateSidebar.details') }}</a>
                    </div>
                </li>
                <li class="menu-wrapper">
                    <div @if($active=='useTerms') class="menu-active" @endif>
                        <a href="{{action('EntitiesController@showUseTerms', [$entityKey, $siteKey])}}">{{ trans('privateSidebar.site_use_terms') }}</a>
                    </div>
                </li>
                <li class="menu-wrapper">
                    <div @if($active=='homePageConfigurations') class="menu-active" @endif>
                        <a href="{{action('EntitiesController@showHomePageConfigurations', [$entityKey, $siteKey])}}">{{ trans('privateSidebar.site_home_page_configuration_groups') }}</a>
                    </div>
                </li>
                <li class="menu-wrapper">
                    <div @if($active=='configurations') class="menu-active" @endif>
                        <a href="{{action('EntitiesController@showConfigurations', [$entityKey, $siteKey])}}">{{ trans('privateSidebar.configurations') }}</a>
                    </div>
                </li>
            </ul>
        </li>
    </ul>
</div>

<script>
    localStorage.setItem('previousSidebar', 'entities');
    localStorage.setItem('currentSidebar', 'sites');
    localStorage.setItem('sidebarPosition', 2)
</script>