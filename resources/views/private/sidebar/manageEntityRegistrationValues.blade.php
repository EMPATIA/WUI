<div class="side-menu-wrapper">
    <ul class="sidebar-menu sidebar-menu-css">
        <!-- Menu Title -->
        <li class="main-menu-title">
            <div class="menu-border-bottom">
                {{ trans('manageEntityRegistrationValues.loginLevels') }}
            </div>

            <!-- Sub Menu -->
            <ul class="sub-menu-wrapper">
                <li class="menu-wrapper">
                    <div class="@if($active=='auth_methods') menu-active @endif">
                        <a href="{{ action("EntitiesDividedController@showAuthMethods") }}">
                            {{ trans('privateSidebar.auth_methods') }}
                        </a>
                    </div>
                </li>
                <li class="menu-wrapper">
                    <div class="@if($active=='domain_names') menu-active @endif">
                        <a href="{{ action("EntitiesDividedController@showEntityRegistrationValues",'domain_names') }}">
                            {{ trans('privateSidebar.manage_domain_names') }}
                        </a>
                    </div>
                </li>
                <li class="menu-wrapper">
                    <div class="@if($active=='vat_numbers') menu-active @endif">
                        <a href="{{ action("EntitiesDividedController@showEntityRegistrationValues",'vat_numbers') }}">
                            {{ trans('privateSidebar.manage_vat_numbers') }}
                        </a>
                    </div>
                </li>
            </ul>
        </li>
    </ul>
</div>

{{--<script>--}}
    {{--localStorage.setItem('previousSidebar', 'entity');--}}
    {{--localStorage.setItem('currentSidebar', 'manageEntityRegistrationValues');--}}
    {{--localStorage.setItem('sidebarPosition', 2)--}}
{{--</script>--}}

<script>
    if(localStorage.getItem('sidebarPosition') == 2)
        localStorage.setItem('nextSidebar', localStorage.getItem('currentSidebar'));
    localStorage.setItem('currentSidebar', 'manageEntityRegistrationValues');
    localStorage.setItem('previousSidebar', 'entity');
    localStorage.setItem('sidebarPosition', 1)
</script>