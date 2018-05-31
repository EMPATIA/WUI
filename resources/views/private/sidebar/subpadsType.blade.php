
<script>
    if(localStorage.getItem('sidebarPosition') == 2)
        localStorage.setItem('nextSidebar', localStorage.getItem('currentSidebar'));
    else{
        localStorage.removeItem('nextSidebar');
    }
    localStorage.setItem('currentSidebar', 'padsType');
    localStorage.setItem('previousSidebar', 'private');
    localStorage.setItem('sidebarPosition', 1)

</script>

<div class="side-menu-wrapper">
    <ul class="sidebar-menu sidebar-menu-css">
    {{--<li>--}}
    {{--<span style="width: 50%"><a href="#" id="back" onclick="go('private')"><i class="fa fa-arrow-left"></i>{{trans('privateSidevar.back')}}</a></span>--}}
    {{--<span><a href="#" id="back" onclick="go('topics')"><i class="fa fa-arrow-right"></i></a></span>--}}
    {{--</li>--}}

        <!-- Menu Title -->
        <li class="main-menu-title">
            <div class="menu-border-bottom">
                <span class="main-menu-title">{{ trans('privateSidebar.pads_'.$type) }}</span>
            </div>

            <!-- Sub Menu -->
            <ul class="sub-menu-wrapper">
                <li class="menu-wrapper">
                    <div class="@if($active=='details') menu-active @endif">
                        <a href="{{ action('CbsController@show', [$type, $cb->cb_key ?? $cbKey]) }}">
                            {{ trans('privateSidebar.details') }}
                        </a>
                    </div>
                </li>

                @if(in_array('pad_parameters', Session::get('user_permissions_sidebar')) || sizeOf(Session::get('user_permissions_sidebar')) == 1)
                    <li class="menu-wrapper">
                        <div class="@if($active=='parameters') menu-active @endif">
                            <a href="{{ action('CbsController@showParameters', [$type, $cb->cb_key ?? $cbKey]) }}">
                                {{ trans('privateSidebar.pads_parameter') }}
                            </a>
                        </div>
                    </li>
                @endif
                @if(in_array('configurations', Session::get('user_permissions_sidebar')) || sizeOf(Session::get('user_permissions_sidebar')) == 1)
                    <li class="menu-wrapper">
                        <div class="@if($active=='configurations') menu-active @endif">
                            <a href="{{ action('CbsController@showConfigurations', [$type, $cb->cb_key ?? $cbKey]) }}">
                                {{ trans('privateSidebar.pads_configurations') }}
                            </a>
                        </div>
                    </li>
                @endif

            </ul>
        </li>
    </ul>
</div>