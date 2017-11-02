<script>
    if(localStorage.getItem('sidebarPosition') == 2)
        localStorage.setItem('nextSidebar', localStorage.getItem('currentSidebar'));
    else{
        localStorage.removeItem('nextSidebar');
    }
    localStorage.setItem('currentSidebar', 'registration');
    localStorage.setItem('previousSidebar', 'private');
    localStorage.setItem('sidebarPosition', 1)

    console.log("registration:"+localStorage);
    $(".pager").css("display", "block")
    $(".back").css('pointer-events', 'visible');
</script>

<div class="side-menu-wrapper">
    <ul class="sidebar-menu sidebar-menu-css">
        <!-- Menu Title -->
        <li class="main-menu-title">
            <div class="menu-border-bottom">
            {{ trans('privateSidebar.registration') }}
            </div>
            <!-- Sub Menu -->
            <ul class="sub-menu-wrapper">
                <li class="menu-wrapper">
                    <div class="@if($active=='confirm') menu-active @endif">
                        <a href="{{ action("UsersController@indexCompleted") }}">
                            {{ trans('privateSidebar.registration_users_completed') }}
                        </a>
                    </div>
                </li>
                <li class="menu-wrapper">
                    <div class="@if($active=='personRegistration') menu-active @endif">
                        <a href="{{ action("InPersonRegistrationController@index") }}">
                            {{ trans('privateSidebar.registration_in_person_registration') }}
                        </a>
                    </div>
                </li>
            </ul>
        </li>
    </ul>
</div>
