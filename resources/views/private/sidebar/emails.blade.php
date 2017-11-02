<div class="side-menu-wrapper">
    <ul class="sidebar-menu sidebar-menu-css">
        <!-- Menu Title -->
        <li class="main-menu-title">
            <div class="menu-border-bottom">
            {{ trans('privateSidebar.emails') }}
            </div>
            <!-- Sub Menu -->
            <ul class="sub-menu-wrapper">
                <li class="menu-wrapper">
                    <div class="@if($active=='emails') menu-active @endif">
                        <a href="{{ action("EmailsController@index") }}">{{ trans('privateSidebar.details') }}</a>
                    </div>
                </li>
            </ul>
        </li>
    </ul>
</div>