<div class="side-menu-wrapper">
    <ul class="sidebar-menu sidebar-menu-css">
        <!-- Menu Title -->
        <li class="main-menu-title">
            <div class="menu-border-bottom">
            {{ trans('privateSidebar.site') }}
            </div>
            <!-- Sub Menu -->
            <ul class="sub-menu-wrapper">
                <li class="menu-wrapper">
                    <div class="@if($active=='details') menu-active @endif">
                        <a href="{{action('EntitiesSitesController@show', $site->key ?? $siteKey)}}">
                            {{ trans('privateSidebar.details') }}
                        </a>
                    </div>
                </li>
                <li class="menu-wrapper">
                    <div class="@if($active=='use_terms') menu-active @endif">
                        <a href="{{action('EntitiesSitesController@showUseTerms', $site->key ?? $siteKey)}}">
                            {{ trans('privateSidebar.site_use_terms') }}
                        </a>
                    </div>
                </li>
                <li class="menu-wrapper">
                    <div class="@if($active=='privacy_policy') menu-active @endif">
                        <a href="{{action('EntitiesSitesController@showPrivacyPolicy', $site->key ?? $siteKey)}}">
                            {{ trans('privateSidebar.site_privacy_policy') }}
                        </a>
                    </div>
                </li>
                <li class="menu-wrapper">
                    <div class="@if($active=='emailTemplates') menu-active @endif">
                        <a href="{{action('EntitiesSitesController@showEmailTemplates', $site->key ?? $siteKey)}}">
                            {{ trans('privateSidebar.site_email_templates') }}
                        </a>
                    </div>
                </li>
                <li class="menu-wrapper">
                    <div class="@if($active=='configurations') menu-active @endif">
                        <a href="{{action('SiteConfValuesController@index', ['siteKey' => $site->key ?? $siteKey])}}">
                            {{ trans('privateSidebar.configurations') }}
                        </a>
                    </div>
                </li>
                <li class="menu-wrapper">
                    <div class="@if($active=='siteLevels') menu-active @endif">
                        <a href="{{action('EntitiesSitesController@showSiteLevels', $site->key ?? $siteKey)}}">
                            {{ trans('privateSidebar.site_login_levels') }}
                        </a>
                    </div>
                </li>
                <li class="menu-wrapper">
                    <div class="@if($active=='reorder') menu-active @endif">
                        <a href="{{ action('LoginLevelsController@showLevelReorder', ['siteKey' => $site->key ?? $siteKey]) }}">
                            {{ trans('privateSidebar.reorder_login_levels') }}
                        </a>
                    </div>
                </li>
                @if(in_array('pages', Session::get('user_permissions_sidebar')) || sizeOf(Session::get('user_permissions_sidebar')) == 1)
                    <li class="menu-wrapper">
                        <div class="@if($active=='cm.pages') menu-active @endif">
                            <a href="{{ action('ContentManagerController@index', ['contentType'=>'pages','siteKey' => $site->key ?? $siteKey]) }}">
                                {{ trans('privateSidebar.pages') }}
                            </a>
                        </div>
                    </li>
                @endif
                <li class="menu-wrapper">
                    <div class="@if($active=='cm.news') menu-active @endif">
                        <a href="{{ action('ContentManagerController@index', ['contentType'=>'news','siteKey' => $site->key ?? $siteKey]) }}">
                            {{ trans('privateSidebar.news') }}
                        </a>
                    </div>
                </li>
                <li class="menu-wrapper">
                    <div class="@if($active=='cm.events') menu-active @endif">
                        <a href="{{ action('ContentManagerController@index', ['contentType'=>'events','siteKey' => $site->key ?? $siteKey]) }}">
                            {{ trans('privateSidebar.events') }}
                        </a>
                    </div>
                </li>

                <!-- --//-- -->
                <li class="menu-wrapper">
                    <div class="@if($active=='stepperLogin') menu-active @endif">
                        <a href="{{action('EntitiesSitesController@showStepperLoginList', $site->key ?? $siteKey)}}">
                            {{ trans('privateSidebar.stepper_login_levels') }}
                        </a>
                    </div>
                </li>
                <li class="menu-wrapper">
                    <div class="@if($active=='stepperLoginReorder') menu-active @endif">
                        <a href="{{ action('StepperLoginController@showLevelReorder', ['siteKey' => $site->key ?? $siteKey]) }}">
                            {{ trans('privateSidebar.reorder_stepper_login_levels') }}
                        </a>
                    </div>
                </li>
            </ul>
        </li>

    </ul>
</div>

<script>
    if(localStorage.getItem('sidebarPosition') == 2)
        localStorage.setItem('nextSidebar', localStorage.getItem('currentSidebar'));
    localStorage.setItem('currentSidebar', 'site');
    localStorage.setItem('previousSidebar', 'private');
    localStorage.setItem('sidebarPosition', 1)
</script>
