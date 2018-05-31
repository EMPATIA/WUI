<div class="side-menu-wrapper">
    <!-- Home & Back -->
    <ul class="sidebar-menu">
        <li @if (Route::getCurrentRoute()->getName() == 'private') class="active" @endif>
            <a href="{{ route("private") }}"><i class="fa fa-home"></i><span> {{ trans('privateSidebar.home') }}</span></a></li>
        <li><a href="#" id="back" onclick="go(this)"><i class="fa fa-arrow-left"></i><span> {{ trans('privateSidebar.back') }}</span></a></li>
    </ul>
    <!-- Menu -->
    <ul class="sidebar-menu sidebar-menu-css">
        <!-- Menu Title -->
        <li class="main-menu-title">
            <div class="menu-border-bottom">
                {{ trans('privateSidebar.section_type') }}
            </div>
            <!-- Sub Menu -->
            <ul class="sub-menu-wrapper">
                <li class="menu-wrapper">
                    <div @if($active=='details') class="menu-active" @endif>
                        <a href="{{ action("CMSectionTypesController@show",["section_type_key" => $variableToView] ) }}">
                            {{ trans('privateSidebar.details') }}
                        </a>
                    </div>
                </li>
            </ul>
        </li>
    </ul>
</div>