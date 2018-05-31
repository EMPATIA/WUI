@foreach($menus as $menu)
    @if(empty($menu['id']) && !empty($menu[0]['title']))
        <li class="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">{{$menu[0]['title']}}<span class="caret"></span></a>
            <ul class="dropdown-menu topBarDropdown">
                @foreach ($menu as $subMenu)
                    @if (empty($subMenu['id']))
                        <li class="dropdown"><a href="#" data-toggle="dropdown" class="dropdown-toggle">{{$subMenu[0]['title']}}</a>
                            <ul class="dropdown-menu">
                                @foreach ($subMenu as $subSubMenu)
                                    @if($subSubMenu['id'] != $subMenu[0]['id'])
                                        <li class="dropdown-submenu"><a href="{{ONE::getActionMenu($subSubMenu)}}">{{$subSubMenu['title']}}</a></li>
                                    @endif
                                @endforeach
                            </ul>
                        </li>
                    @elseif($subMenu['id'] != $menu[0]['id'])
                        <li><a href="{{ONE::getActionMenu($subMenu)}}">{{$subMenu['title']}} </a></li>
                    @endif
                @endforeach
            </ul>
        </li>
    @elseif(!empty($menu['id']) && !empty($menu['title']))
        <li class=""><a href="{{ONE::getActionMenu($menu)}}" >{{$menu['title']}}</a></li>
    @endif
@endforeach
{{--<li id="searchBtn" class=""><a href="./"><i class="fa fa-search"></i></a></li>--}}