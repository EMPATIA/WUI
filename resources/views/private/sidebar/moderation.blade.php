<script>
    if(localStorage.getItem('sidebarPosition') == 2)
        localStorage.setItem('nextSidebar', localStorage.getItem('currentSidebar'));
    localStorage.setItem('currentSidebar', 'moderation');
    localStorage.setItem('previousSidebar', 'private');
    localStorage.setItem('sidebarPosition', 1)
</script>

<div class="side-menu-wrapper">
    <ul class="sidebar-menu sidebar-menu-css">
        <!-- Menu Title -->
        <li class="main-menu-title">
            <div class="menu-border-bottom">
            {{ trans('privateSidebar.items_to_moderate') }}
            </div>

            <!-- Sub Menu -->
            <ul class="sub-menu-wrapper">
                <li class="menu-wrapper">
                    <div class="@if($active=='topics_to_moderate') menu-active @endif">
                        <a href="{{ action('ModerationController@topicsToModerate') }}">
                            {{ trans('privateSidebar.topics_to_moderate') }}
                        </a>
                    </div>
                </li>
                <li class="menu-wrapper">
                    <div class="@if($active=='posts_to_moderate') menu-active @endif">
                        <a href="{{ action('ModerationController@postsToModerate') }}">
                            {{ trans('privateSidebar.posts_to_moderate') }}
                        </a>
                    </div>
                </li>
            </ul>
        </li>
    </ul>
</div>