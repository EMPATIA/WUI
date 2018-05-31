<script>
    if(localStorage.getItem('sidebarPosition') == 2)
        localStorage.setItem('nextSidebar', localStorage.getItem('currentSidebar'));
    else{
        localStorage.removeItem('nextSidebar');
    }
    localStorage.setItem('currentSidebar', 'email');
    localStorage.setItem('previousSidebar', 'private');
    localStorage.setItem('sidebarPosition', 1)

    console.log("email:"+localStorage);
    $(".pager").css("display", "block")
    $(".back").css('pointer-events', 'visible');
</script>

<div class="side-menu-wrapper">
    <ul class="sidebar-menu sidebar-menu-css">
        <!-- Menu Title -->
        <li class="main-menu-title">
            <div class="menu-border-bottom">
                {{ trans('privateSidebar.email') }}
            </div>
            <!-- Sub Menu -->
            <ul class="sub-menu-wrapper">
                <li class="menu-wrapper">
                    <div class="@if($active=='summary') menu-active @endif">
                        <a href="{{ action("EmailsController@showSummary") }}">
                            {{ trans('privateSidebar.emails_summary') }}
                        </a>
                    </div>
                </li>

                <li class="menu-wrapper">
                    <div class="@if($active=='sent') menu-active @endif">
                        <a href="{{ action("EmailsController@index") }}">
                            {{ trans('privateSidebar.emails_sent') }}
                        </a>
                    </div>
                </li>

                <li class="menu-wrapper">
                    <div class="@if($active=='newsletters') menu-active @endif">
                        <a href="{{ action("PrivateNewslettersController@index") }}">
                            {{ trans('privateSidebar.newsletters') }}
                        </a>
                    </div>
                </li>

                <li class="menu-wrapper">
                    <div class="@if($active=='newsletters_subcriptions') menu-active @endif">
                        <a href="{{ action("NewsletterSubscriptionsController@index") }}">
                            {{ trans('privateSidebar.subscriptions') }}
                        </a>
                    </div>
                </li>

                <li class="menu-wrapper">
                    <div class="@if($active=='send') menu-active @endif">
                        <a href="{{ action("EmailsController@create") }}">
                            {{ trans('privateSidebar.send_emails') }}
                        </a>
                    </div>
                </li>
            </ul>
        </li>
    </ul>
</div>
