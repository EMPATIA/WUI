<script>
    if(localStorage.getItem('sidebarPosition') == 2)
        localStorage.setItem('nextSidebar', localStorage.getItem('currentSidebar'));
    else{
        localStorage.removeItem('nextSidebar');
    }
    localStorage.setItem('currentSidebar', 'entityMessages');
    localStorage.setItem('previousSidebar', 'private');
    localStorage.setItem('sidebarPosition', 1)
</script>
<div class="side-menu-wrapper">
    <ul class="sidebar-menu sidebar-menu-css">
        <!-- Menu Title -->
        <li class="main-menu-title">
            <div class="menu-border-bottom">
                {{ trans('privateSidebar.entityMessages') }}
            </div>

            <!-- Sub Menu -->
            <ul class="sub-menu-wrapper">
                <li class="menu-wrapper">
                    <div class="@if($active=='messages') menu-active @endif">
                        <a href="{{action("EntityMessagesController@index")}}">
                            {{ trans('privateSidebar.details') }}
                        </a>
                    </div>
                </li>
                <li class="menu-wrapper">
                    <div class="@if($active=='sent_messages') menu-active @endif">
                        <a href="{{ action("EntityMessagesController@showMessagesTable", ['flag' => 'sentMessages']) }}">
                            {{ trans('privateSidebar.sent_messages') }}
                        </a>
                    </div>
                </li>
                <li class="menu-wrapper">
                    <div class="@if($active=='received_messages') menu-active @endif">
                        <a href="{{ action("EntityMessagesController@showMessagesTable", ['flag' => 'receivedMessages']) }}">
                            {{ trans('privateSidebar.received_messages') }}
                        </a>
                    </div>
                </li>
            </ul>
        </li>
    </ul>
</div>
