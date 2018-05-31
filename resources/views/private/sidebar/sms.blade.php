<div class="side-menu-wrapper">
    <ul class="sidebar-menu sidebar-menu-css">
        <div class="main-menu-title">{{ trans('privateSidebar.sms') }}</div>

        <li class="menu-wrapper">
            <div class="menu-wrapper">
                <a @if($active=='resume') class="menu-active"  @endif href="{{action('SmsController@index')}}">
                    {{ trans('privateSidebar.summary') }}
                </a>
            </div>
        </li>
        <li class="menu-wrapper">
            <div class="menu-wrapper">
                <a @if($active=='sended') class="menu-active"  @endif href="{{action('SmsController@showSendedSms')}}">
                    {{ trans('privateSidebar.sent') }}
                </a>
            </div>
        </li>
        <li class="menu-wrapper">
            <div class="menu-wrapper">
                <a @if($active=='received') class="menu-active"  @endif href="{{action('SmsController@showReceivedSms')}}">
                    {{ trans('privateSidebar.received') }}
                </a>
            </div>
        </li>
        <li class="menu-wrapper">
            <div class="menu-wrapper">
                <a @if($active=='analytics') class="menu-active"  @endif href="{{action('SmsController@showAnalyticsSms')}}">
                    {{ trans('privateSidebar.analytics') }}
                </a>
            </div>
        </li>
        <li class="menu-wrapper">
            <div class="menu-wrapper">
                <a @if($active=='sms') class="menu-active"  @endif href="{{action('SmsController@create')}}">
                    {{ trans('privateSidebar.send') }}
                </a>
            </div>
        </li>
    </ul>
</div>

<script>
    if (localStorage.getItem('sidebarPosition') === '2')
        localStorage.setItem('nextSidebar', localStorage.getItem('currentSidebar'));
    else {
        localStorage.removeItem('nextSidebar');
    }
    localStorage.setItem('currentSidebar', 'sms');
    localStorage.setItem('previousSidebar', 'private');
    localStorage.setItem('sidebarPosition', 1);
</script>