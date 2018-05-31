<script>
    if(localStorage.getItem('sidebarPosition') == 2)
        localStorage.setItem('nextSidebar', localStorage.getItem('currentSidebar'));
    else{
        localStorage.removeItem('nextSidebar');
    }
    localStorage.setItem('currentSidebar', 'votes');
    localStorage.setItem('previousSidebar', 'private');
    localStorage.setItem('sidebarPosition', 1)
</script>

<div class="side-menu-wrapper">
    <ul class="sidebar-menu sidebar-menu-css">
        <!-- Menu Title -->
        <li class="main-menu-title">
            <div class="menu-border-bottom">{{ trans('privateSidebar.votes') }}</div>
            <!-- Sub Menu -->
            <ul class="sub-menu-wrapper">
                <li class="menu-wrapper">
                    <div @if((isset($active) ? $active : " ")=='config') class="menu-active" @endif >
                        <a href="{{ action("VotesConfigsController@index") }}">{{ trans('privateSidebar.vote_configs') }}</a>
                    </div>
                </li>
                <li class="menu-wrapper">
                    <div @if((isset($active) ? $active : "") =='methods') class="menu-active" @endif >
                        <a href="{{ action("VoteMethodsController@index") }}">{{ trans('privateSidebar.vote_methods') }}</a>
                    </div>
                </li>
            </ul>
        </li>
    </ul>
</div>


