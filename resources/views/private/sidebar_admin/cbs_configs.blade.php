<script>
    if(localStorage.getItem('sidebarPosition') == 2)
        localStorage.setItem('nextSidebar', localStorage.getItem('currentSidebar'));
    localStorage.setItem('currentSidebar', 'cbs_configs');
    localStorage.setItem('previousSidebar', 'private');
    localStorage.setItem('sidebarPosition', 1)
</script>

<div class="side-menu-wrapper">
    <ul class="sidebar-menu sidebar-menu-css">
        <!-- Menu Title -->
        <li class="main-menu-title">
            <div class="menu-border-bottom">
                {{ trans('privateSidebar.cbs_configs') }}
            </div>
            <!-- Sub Menu -->
            <ul class="sub-menu-wrapper">
                <li class="menu-wrapper">
                    <div @if((isset($active) ? $active : " ")=='details') class="menu-active" @endif>
                        <a href="{{ action("CbsConfigTypesController@show", $configTypeId) }}">
                            {{ trans('privateSidebar.details') }}
                        </a>
                    </div>
                </li>
                <li class="menu-wrapper">
                    <div class="@if($active=='config') menu-active @endif">
                        <a href="{{ action("CbsConfigTypesController@showConfigurations", $configTypeId) }}">
                            {{ trans('privateSidebar.cbs_configs') }}
                        </a>
                    </div>
                </li>
            </ul>
        </li>
    </ul>
</div>