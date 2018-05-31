<script>
    localStorage.setItem('previousSidebar', 'questionGroup');
    localStorage.setItem('currentSidebar', 'question');
    localStorage.setItem('sidebarPosition', 3)
    localStorage.removeItem('nextSidebar')
</script>

<div class="side-menu-wrapper">
    <ul class="sidebar-menu sidebar-menu-css">
        <!-- Menu Title -->
        <li class="main-menu-title">
            <div class="menu-border-bottom">
            {{ trans('privateSidebar.question') }}
            </div>
            <!-- Sub Menu -->
            <ul class="sub-menu-wrapper">
                <li class="menu-wrapper">
                    <div class="@if($active=='details') menu-active @endif">
                        <a href="{{ action('QuestionnairesController@show', $questionKey) }}">
                            {{ trans('privateSidebar.details') }}
                        </a>
                    </div>
                </li>
            </ul>
        </li>
    </ul>
</div>