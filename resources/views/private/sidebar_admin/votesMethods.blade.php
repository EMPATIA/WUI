<div class="side-menu-wrapper">
    <ul class="sidebar-menu sidebar-menu-css">
        <!-- Menu Title -->
        <li class="main-menu-title">
            <div class="menu-border-bottom">
                {{ trans('privateSidebar.votes_methods') }}
            </div>
            <!-- Sub Menu -->
            <ul class="sub-menu-wrapper">
                <li class="menu-wrapper">
                    <div @if((isset($active) ? $active : " ")=='details') class="menu-active" @endif >
                        <a href="{{ action("VoteMethodsController@show", $voteMethodId) }}">{{ trans('privateSidebar.details') }}</a>
                    </div>
                </li>
                <li class="menu-wrapper">
                    <div @if((isset($active) ? $active : "") =='config') class="menu-active" @endif>
                        <a href="{{ action("VoteMethodsController@showConfigurations", $voteMethodId) }}">{{ trans('privateSidebar.vote_methods_configs') }}</a>
                    </div>
                </li>
            </ul>
        </li>
    </ul>
</div>

<script>
    localStorage.setItem('previousSidebar', 'votes');
    localStorage.setItem('currentSidebar', 'votesMethods');
    localStorage.setItem('sidebarPosition', 2)
</script>