<script>
    if(localStorage.getItem('sidebarPosition') == 3)
        localStorage.setItem('nextSidebar', localStorage.getItem('currentSidebar'));
    else{
        localStorage.removeItem('nextSidebar');
    }
    localStorage.setItem('previousSidebar', 'q');
    localStorage.setItem('currentSidebar', 'questionGroup');
    localStorage.setItem('sidebarPosition', 2)
</script>


<div class="side-menu-wrapper">
    <ul class="sidebar-menu sidebar-menu-css">
        <!-- Menu Title -->
        <li class="main-menu-title">
            <div class="menu-border-bottom">
            {{ trans('privateSidebar.questionsGroup') }}
            </div>
            <ul class="sub-menu-wrapper">
                <li class="menu-wrapper">
                    <div class="@if($active=='details') menu-active @endif">
                        <a href="{{ action('QuestionGroupsController@show', $questiongroupKey) }}">
                            {{ trans('privateSidebar.details') }}
                        </a>
                    </div>
                </li>
            </ul>
        </li>
    </ul>
</div>