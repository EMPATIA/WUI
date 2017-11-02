<script>
    if(localStorage.getItem('sidebarPosition') == 2)
        localStorage.setItem('nextSidebar', localStorage.getItem('currentSidebar'));
    localStorage.setItem('currentSidebar', 'q');
    localStorage.setItem('previousSidebar', 'private');
    localStorage.setItem('sidebarPosition', 1)
</script>


<div class="side-menu-wrapper">
    <ul class="sidebar-menu sidebar-menu-css">
            <!-- Menu Title -->
            <li class="main-menu-title">
                <div class="menu-border-bottom">
                {{ trans('privateSidebar.questionnaire') }}
                </div>
            <ul class="sub-menu-wrapper">
                <li class="menu-wrapper">
                    <div class="@if($active=='details') menu-active @endif">
                        <a href="{{ action('QuestionnairesController@show', $questionnaireKey) }}">
                            {{ trans('privateSidebar.details') }}
                        </a>
                    </div>
                </li>
                <li class="menu-wrapper">
                    <div class="@if($active=='statistics') menu-active @endif">
                        <a href="{{ action('QuestionnairesController@showStatistics', $questionnaireKey) }}">
                            {{ trans('privateSidebar.questionnaire_statistics') }}
                        </a>
                    </div>
                </li>
                
                {{--<li class="treeview">--}}
                    {{--<div class="menu-wrapper"><a @if($active=='questionnaire_event') class="menu-active"  @endif href="#">{{ trans('privateSidebar.questionnaire_event_schedules') }}</a></div>--}}
                {{--</li>--}}
            </ul>
        </li>
    </ul>
</div>