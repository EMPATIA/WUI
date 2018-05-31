<ul class="sidebar-menu side-menu-main">
    <ul class="sidebar-menu">
        <li @if (Route::getCurrentRoute()->getName() == 'private') class="active" @endif><a
                    href="{{ route("private") }}"><i
                        class="fa fa-home"></i><span> {{ trans('privateSidebar.home') }}</span></a></li>
    </ul>
    <li><a href="#" id="back" onclick="getSidebar('{{ action("OneController@getSidebar") }}', 'siteLevels', '{{(isset($variableToView[1]) ? $variableToView[1] : null)}}', 'site')"><i
                    class="fa fa-arrow-left"></i><span> {{ trans('privateSidebar.back') }}</span></a></li>
    <div class="main-menu-title">{{ trans('privateSidebar.loginLevels') }}</div>

</ul>
