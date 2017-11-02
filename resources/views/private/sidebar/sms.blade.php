<ul class="sidebar-menu side-menu-main">
    <ul class="sidebar-menu">
        <li @if (Route::getCurrentRoute()->getName() == 'private') class="active" @endif><a href="{{ route("private") }}"><i class="fa fa-home"></i><span> {{ trans('privateSidebar.home') }}</span></a></li>
    </ul>
    <li><a href="#" id="back" onclick="go(this)"><i class="fa fa-arrow-left"></i><span> {{ trans('privateSidebar.back') }}</span></a></li>
    <div class="main-menu-title">{{ trans('privateSidebar.sms') }}</div>

    <li class="treeview">
        <div class="menu-wrapper"><a @if($active=='sms') class="menu-active"  @endif href="{{ action("SmsController@index") }}">{{ trans('privateSidebar.sent_sms') }}</a></div>
    </li>
</ul>
