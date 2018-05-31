<aside class="main-sidebar">
    <section class="sidebar">

        <!-- Home -->
        <ul class="sidebar-menu">
            <li @if (Route::getCurrentRoute()->getName() == 'private') class="active" @endif><a href="{{ route("private") }}">
                    <i class="fa fa-home"></i><span> {{ trans('privateSidebar.home') }}</span></a>
            </li>
        </ul>

        <!-- Back and Forward -->
        <ul class="pager">
            <li class="previous" onclick="goBack()">
                <a><span aria-hidden="true">&larr;</span> {{ trans('privateSidebar.back') }}</a>
            </li>
            <li class="next" onclick="goForward()">
                <a>{{ trans('privateSidebar.forward') }} <span aria-hidden="true">&rarr;</span></a>
            </li>
        </ul>

        <div class="sidebar-content">
            @if(Session::get('user_role') == 'admin' && ONE::isEntity())
                <ul class="sidebar-menu back2AdminMenuSidebar">
                    <li class="treeview">
                        <a onclick="reloadAdminMenu()" class="back2AdminMenuSidebar"><span>{{ trans('privateSidebar.back_to_admin_menu') }}</span></a>
                    </li>
                </ul>
            @endif

            @if(Session::get('user_role') == 'admin')
                <div class="form-group entitySelectMenuLabelSidebar">
                    <div>
                        <label for="sel1">{{ trans('privateSidebar.entity') }}</label>
                    </div>
                    <select id="select-option-entity" class="form-control select2-searchable" onchange="updateEntity(this)" style="width: 100%;">
                        <option value="">{{ trans('privateSidebar.select_entity') }}</option>
                        @foreach(ONE::getEntities() as $entityDropdown)
                            <option value="{{$entityDropdown->entity_key}}" {{(ONE::getEntityKey() == $entityDropdown->entity_key)? 'selected':''}}>{{$entityDropdown->name}}</option>
                        @endforeach
                    </select>
                </div>
            @endif

            {{-- ADMIN MENU --}}
            @if(Session::get('user_role') == 'admin' && ONE::getEntityKey() == null)
                @if(isset($sidebar))
                    @includeif('private.sidebar_admin.' . $sidebar)
                @else
                    <div class="side-menu-wrapper">
                        <ul class="sidebar-menu-css">
                            <!-- Menu Title -->
                            <li class="main-menu-title">
                                <a class="menu-border-bottom" data-toggle="collapse" href="#collapseAdmin" aria-expanded="true" aria-controls="collapseAdmin">
                                    {{ trans('privateSidebar.administration') }}
                                    <i id="collapseAdmin_up" class="fa fa fa-chevron-up" style="display:none;"></i>
                                    <i id="collapseAdmin_down" class="fa fa fa-chevron-down"></i>
                                </a>

                                {{--Script para mudar a seta para cima e para baixo--}}
                                <script>
                                    $( document ).ready(function() {
                                        $('#collapseAdmin').on('shown.bs.collapse', function () {
                                            $("#collapseAdmin_up").hide();
                                            $("#collapseAdmin_down").show();
                                        });
                                        $('#collapseAdmin').on('hidden.bs.collapse', function () {
                                            $("#collapseAdmin_up").show();
                                            $("#collapseAdmin_down").hide();
                                        });
                                    });
                                </script>

                                <!-- Sub Menu -->
                                <ul  id="collapseAdmin" class="sub-menu-wrapper {{Session::get('user_role') == 'admin'? 'menu-open': ''}} show" aria-expanded="true" style="margin-bottom:30px;">

                                    <li><div class="menu-wrapper"><a id="entities" @if (strpos(Route::getCurrentRoute()->getName(), 'entities') !== false) class="menu-active" @endif href="{{ action("EntitiesController@index") }}"> {{ trans('privateSidebar.entities') }}</a></div></li>
                                    <li><div class="menu-wrapper"><a @if (strpos(Route::getCurrentRoute()->getName(), 'users') !== false)  class="menu-active" @endif href="{{ action("UsersController@index",['role' => 'admin']) }}"> {{ trans('privateSidebar.users') }}</a></div></li>
                                    <li><div class="menu-wrapper"><a @if (strpos(Route::getCurrentRoute()->getName(), 'languages') !== false) class="menu-active" @endif href="{{ action("LanguagesController@index") }}">{{ trans('privateSidebar.languages') }}</a></div></li>
                                    <li><div class="menu-wrapper"><a @if (strpos(Route::getCurrentRoute()->getName(), 'layout') !== false) class="menu-active" @endif href="{{ action("LayoutsController@index") }}"> {{ trans('privateSidebar.templates') }}</a></div></li>
                                    <li><div class="menu-wrapper"><a @if (strpos(Route::getCurrentRoute()->getName(), 'countries') !== false) class="menu-active" @endif href="{{ action("CountriesController@index") }}"> {{ trans('privateSidebar.countries') }}</a></div></li>
                                    <li><div class="menu-wrapper"><a @if (strpos(Route::getCurrentRoute()->getName(), 'timezones') !== false) class="menu-active" @endif href="{{ action("TimezonesController@index") }}"> {{ trans('privateSidebar.timezones') }}</a></div></li>
                                    <li><div class="menu-wrapper"><a @if (strpos(Route::getCurrentRoute()->getName(), 'currencies') !== false) class="menu-active" @endif href="{{ action("CurrenciesController@index") }}">{{ trans('privateSidebar.curencies') }}</a></div></li>
                                    <li>
                                        <div class="menu-wrapper"><a @if (Route::getCurrentRoute()->uri() == 'translations') class="menu-active" @endif href="{{ url("/translations") }}">{{ trans('privateSidebar.translations') }}</a></div>
                                    </li>
                                    {{--                                    <li><div class="menu-wrapper"><a @if (strpos(Route::getCurrentRoute()->getName(), 'questIcon') !== false) class="menu-active" @endif href="{{ action("QuestIconsController@index") }}">{{ trans('privateSidebar.questionnaires') }}</a></div></li>--}}
                                </ul>

                                <a class="menu-border-bottom" data-toggle="collapse" href="#collapseSystemConfig" aria-expanded="true" aria-controls="collapseSystemConfig">
                                    {{ trans('privateSidebar.systemConfig') }}
                                    <i id="collapseSystemConfig_up" class="fa fa fa-chevron-up" style="display:none;"></i>
                                    <i id="collapseSystemConfig_down" class="fa fa fa-chevron-down"></i>
                                </a>
                                {{--Script para mudar a seta para cima e para baixo--}}
                                <script>
                                    $( document ).ready(function() {
                                        $('#collapseSystemConfig').on('shown.bs.collapse', function () {
                                            $("#collapseSystemConfig_up").hide();
                                            $("#collapseSystemConfig_down").show();
                                        });
                                        $('#collapseSystemConfig').on('hidden.bs.collapse', function () {
                                            $("#collapseSystemConfig_up").show();
                                            $("#collapseSystemConfig_down").hide();
                                        });
                                    });
                                </script>

                                <ul id="collapseSystemConfig" class="sub-menu-wrapper {{Session::get('user_role') == 'admin'? 'menu-open': ''}}" style="margin-bottom:30px;">

                                    <li><div class="menu-wrapper"><a @if (strpos(Route::getCurrentRoute()->getName(), 'parameterUserTypes') !== false) class="menu-active" @endif href="{{ action("ParameterUserTypesController@index") }}"> {{ trans('privateSidebar.user_parameter_types') }}</a></div></li>
                                    <li><div class="menu-wrapper"><a @if (strpos(Route::getCurrentRoute()->getName(), 'parameterType') !== false) class="menu-active" @endif href="{{ action("ParameterTypesController@index") }}"> {{ trans('privateSidebar.process_parameters_types') }}</a></div></li>
                                    <li><div class="menu-wrapper"><a id="cbs_configs" @if (strpos(Route::getCurrentRoute()->getName(), 'cbConfigType') !== false) class="menu-active" @endif href="{{ action("CbsConfigTypesController@index") }}">{{ trans('privateSidebar.participatory_configuration_sections') }}</a></div></li>
                                    <li><div class="menu-wrapper"><a id="votes" href="#" onclick="goSidebar('votes')" id="votes">{{ trans('privateSidebar.votes') }}</a></div></li>
                                    <li><div class="menu-wrapper"><a id="modules" @if (strpos(Route::getCurrentRoute()->getName(), 'module') !== false) class="menu-active" @endif href="{{ action("ModulesController@index") }}"> {{ trans('privateSidebar.modules') }}</a></div></li>
                                    <li><div class="menu-wrapper"><a id="siteConfs" @if (strpos(Route::getCurrentRoute()->getName(), 'siteConfGroup') !== false) class="menu-active" @endif href="{{ action("SiteConfGroupController@index") }}"> {{ trans('privateSidebar.title') }}</a></div></li>
                                    <li><div class="menu-wrapper"><a @if (strpos(Route::getCurrentRoute()->getName(), 'authMethods') !== false) class="menu-active" @endif href="{{ action("AuthMethodsController@index") }}"> {{ trans('privateSidebar.auth_methods') }}</a></div></li>
                                    <li><div class="menu-wrapper"><a @if (strpos(Route::getCurrentRoute()->getName(), 'flagTypes') !== false) class="menu-active" @endif href="{{ action("FlagTypesController@index") }}">{{ trans('privateSidebar.flag_types') }}</a></div></li>
                                    {{--<li><div class="menu-wrapper"><a @if (strpos($_SERVER['REQUEST_URI'],'TrackingController')) class="menu-active" @endif href="{{ action("TrackingController@showTracking") }}"> Logs </a></div></li>--}}
                                    <li><div class="menu-wrapper"><a @if (strpos(Route::getCurrentRoute()->getName(), 'CMSectionTypes') !== false) class="menu-active" @endif href="{{ action("CMSectionTypesController@index") }}">{{ trans('privateSidebar.cms_section_types') }}</a></div></li>
                                    <li><div class="menu-wrapper"><a @if (strpos(Route::getCurrentRoute()->getName(), 'CMSectionTypeParameters') !== false) class="menu-active" @endif href="{{ action("CMSectionTypeParametersController@index") }}">{{ trans('privateSidebar.cms_section_type_parameters') }}</a></div></li>
                                    <li><div class="menu-wrapper"><a @if (strpos(Route::getCurrentRoute()->getName(), 'dashBoardElements') !== false) class='menu-active' @endif href="{{ action("DashBoardElementsController@index") }}">{{ trans('privateSidebar.dashboard_elements') }}</a></div></li>
                                    <li><div class="menu-wrapper"><a @if (strpos(Route::getCurrentRoute()->getName(), 'dashBoardElementConfigurations') !== false) class='menu-active' @endif href="{{ action("DashBoardElementConfigurationsController@index") }}">{{ trans('privateSidebar.dashboard_element_configurations') }}</a></div></li>
                                    {{--<li><div class="menu-wrapper"><a href="{{ action("AccessesController@index") }}">{{ trans('privateSidebar.access') }}</a></div></li>--}}
                                    {{--<li><div class="menu-wrapper"><a href="{{ action("AccessesController@analyticEntityKey") }}">{{ trans('privateSidebar.analytics') }}</a></div></li>--}}

                                    {{--<li><div class="menu-wrapper"><a @if (strpos(Route::getCurrentRoute()->getName(), 'groupTypes') !== false) class="menu-active" @endif href="{{ action("GroupTypesController@index") }}"> {{ trans('privateSidebar.group_types') }}</a></div></li>--}}
                                    {{--<li><div class="menu-wrapper"><a @if (strpos(Route::getCurrentRoute()->getName(), 'BEMenuElementConfigurations') !== false) class="menu-active" @endif href="{{ action("BEMenuElementParametersController@index") }}">{{ trans('privateSidebar.BEMenuElementConfigurations') }}</a></div></li>--}}
                                    {{--<li><div class="menu-wrapper"><a @if (strpos(Route::getCurrentRoute()->getName(), 'BEMenuElements') !== false) class="menu-active" @endif href="{{ action("BEMenuElementsController@index") }}">{{ trans('privateSidebar.BEMenuElements') }}</a></div></li>--}}

                                </ul>

                                <a class="menu-border-bottom" data-toggle="collapse" href="#collapseLogs" aria-expanded="true" aria-controls="collapseLogs">
                                    {{ trans('privateSidebar.logs') }}
                                    <i id="collapseLogs_up" class="fa fa fa-chevron-up" style="display:none;"></i>
                                    <i id="collapseLogs_down" class="fa fa fa-chevron-down"></i>
                                </a>
                                {{--Script para mudar a seta para cima e para baixo--}}
                                <script>
                                    $( document ).ready(function() {
                                        $('#collapseLogs').on('shown.bs.collapse', function () {
                                            $("#collapseLogs_up").hide();
                                            $("#collapseLogs_down").show();
                                        });
                                        $('#collapseLogs').on('hidden.bs.collapse', function () {
                                            $("#collapseLogs_up").show();
                                            $("#collapseLogs_down").hide();
                                        });
                                    });
                                </script>

                                <ul id="collapseLogs" class="sub-menu-wrapper {{Session::get('user_role') == 'admin'? 'menu-open': ''}}">
                                    <li><div class="menu-wrapper"><a @if (strpos(Route::getCurrentRoute()->getName(),'showTracking')  !== false) class="menu-active" @endif href="{{ action("TrackingController@showTracking") }}"> {{ trans('privateSidebar.auditing') }} </a></div></li>
                                    <li><div class="menu-wrapper"><a @if (strpos(Route::getCurrentRoute()->getName(),'access.index')  !== false) class="menu-active" @endif href="{{ action("AccessesController@index") }}">{{ trans('privateSidebar.access') }}</a></div></li>
                                    <li><div class="menu-wrapper"><a @if (strpos(Route::getCurrentRoute()->getName(),'analytic.analytic') !== false) class="menu-active" @endif href="{{ action("AccessesController@analyticEntityKey") }}">{{ trans('privateSidebar.analytics') }}</a></div></li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                @endif
            @endif

            {{-- MANAGER MENU --}}
            @if(ONE::getEntityKey() != null)
                @if(isset($sidebar))
                    {{-- Display sub-menu --}}
                    @include('private.sidebar.' . $sidebar)
                @else
                    {{--Display main menu --}}
                    <ul class="sidebar-menu" style="height: auto; width: auto; padding: 5px 10px 10px; color: rgb(60, 141, 188);">
                        {{-- Participation menu --}}
                        <li class="main-menu-title">
                            <div data-toggle="collapse" href="#collapse-participation" class="title-menu"  toggle=false>
                                <div class="row">
                                    <div class="col-9">
                                        <div class="menu-border-bottom">{{trans('privateSidebar.participation')}}</div>
                                    </div>
                                    <div class="col-3">
                                        <i class="fa fa-chevron-down pull-right"></i>
                                    </div>
                                </div>
                            </div>

                            <ul id="collapse-participation" class="collapse sub-menu-wrapper show">
                                <?php $sidebarActiveCbs = \App\ComModules\CB::getActivePads(); ?>

                                @foreach($sidebarActiveCbs as $sidebarActiveCb)
                                    @if(ONE::checkPermissions("participation_admin") || ONE::checkCBPermissions($sidebarActiveCb->cbKey, null))
                                        <li class="treeview">
                                            <div class="menu-wrapper">
                                                <a id="padsType_phase1" @if(strpos($_SERVER['REQUEST_URI'],'$sidebarActiveCb->cbType')) class='menu-active' @endif href="{{ action("CbsController@show", [$sidebarActiveCb->cbType,$sidebarActiveCb->cbKey]) }}"> {{ $sidebarActiveCb->cbTitle }}</a>
                                            </div>
                                        </li>
                                    @endif
                                @endforeach

                                @if(ONE::checkPermissions("participation_create"))
                                    <li class="treeview">
                                        <div class="menu-wrapper">
                                            <i><a id="addprocess" @if(strpos(Route::getCurrentRoute()->getName(), 'private.cbs.stepType') !== false) class='menu-active' @endif href="{{ action("CbsController@stepType") }}"> {{ trans('privateSidebar.add_process')}} </a></i>
                                        </div>
                                    </li>
                                @endif
                                @if(ONE::checkPermissions("participation_show"))
                                    <li class="treeview">
                                        <div class="menu-wrapper">
                                            <i><a id="viewallcbs"  @if(strpos(Route::getCurrentRoute()->getName(), 'private.cbs.index_manager') !== false) class='menu-active' @endif href="{{ action("CbsController@indexManager") }}"> {{trans('privateSidebar.view_all_cbs')}} </a></i>
                                        </div>
                                    </li>
                                @endif
                            </ul>
                        </li>

                        {{-- CMS menu --}}
                        <li class="main-menu-title">
                            <div data-toggle="collapse" href="#collapse-contents" class="title-menu">
                                <div class="row">
                                    <div class="col-9">
                                        <div class="menu-border-bottom menu-border-bottom">{{ trans('privateSidebar.contents') }}</div>
                                    </div>
                                    <div class="col-3">
                                        <i class="fa fa fa-chevron-down pull-right"></i>
                                    </div>
                                </div>
                            </div>

                            <ul id="collapse-contents" class="collapse sub-menu-wrapper show">
                                @if(ONE::checkPermissions("cms_sites"))
                                    <li class="treeview">
                                        <div class="menu-wrapper"><a id="site" @if(strpos(Route::getCurrentRoute()->getName(), 'entitySites') !== false) class="menu-active" @endif href="{{ action("EntitiesSitesController@index") }}">{{ trans('privateSidebar.sites') }}</a></div>
                                    </li>
                                @endif

                                @if(ONE::checkPermissions("cms_menus"))
                                    <li class="treeview">
                                        <div class="menu-wrapper"><a id="menu" @if(strpos(Route::getCurrentRoute()->getName(), 'accessMenus') !== false) class="menu-active" @endif href="{{ action("AccessMenusController@index") }}">{{ trans('privateSidebar.menus') }}</a></div>
                                    </li>
                                @endif

                                @if(ONE::checkPermissions("cms_pages"))
                                    <li class="treeview">
                                        <div class="menu-wrapper">
                                            <a @if($_SERVER['REQUEST_URI'] == "/private/newContent/pages") class="menu-active" @endif href="{{ action('ContentManagerController@index', ['contentType'=>'pages']) }}">{{ trans('privateSidebar.pages') }}</a>
                                        </div>
                                    </li>

                                    <li class="treeview">
                                        <div class="menu-wrapper">
                                            <a @if($_SERVER['REQUEST_URI'] == "/private/newContent/news") class="menu-active" @endif href="{{ action('ContentManagerController@index', ['contentType'=>'news']) }}">{{ trans('privateSidebar.news') }}</a>
                                        </div>
                                    </li>
                                @endif
                            </ul>
                        </li>

                        {{-- Users --}}
                        <li class="main-menu-title">
                            <div data-toggle="collapse" href="#collapse-users" class="title-menu">
                                <div class="row">
                                    <div class="col-9">
                                        <div class="menu-border-bottom">{{ trans('privateSidebar.users') }}</div>
                                    </div>
                                    <div class="col-3">
                                        <i class="fa fa-chevron-down pull-right"></i>
                                    </div>
                                </div>
                            </div>

                            <ul id="collapse-users" class="collapse sub-menu-wrapper show">
                                @if(ONE::checkPermissions("users_list"))
                                    <li class="treeview">
                                        <div class="menu-wrapper"><a id="manager" @if( (ONE::checkActiveMenu('user') && $_SERVER['REQUEST_URI'] == "/private/users") || ONE::checkActiveMenu('user') && (strpos(Route::getCurrentRoute()->getName(), 'users.show') !== false ))  class='menu-active' @endif href="{{ action("UsersController@index") }}">{{ trans('privateSidebar.users_list') }}</a></div>
                                    </li>
                                @endif

                                @if(ONE::checkPermissions("users_groups"))
                                    @if($groupTypes = ONE::getGroupTypes())
                                        @foreach($groupTypes as $item)
                                            <li class="treeview">
                                                <div class="menu-wrapper"><a id="entityGroupDetails" @if(strpos(Route::getCurrentRoute()->getName(), 'entityGroups') !== false) class='menu-active' @endif href="{{ action("EntityGroupsController@showGroups", ["groupTypeKey" => $item->group_type_key] )}}" id="{!! strtolower($item->code) !!}  departments">{{ trans('privateSidebar.groups') }}</a></div>
                                            </li>
                                        @endforeach
                                    @endif
                                @endif

                                @if(ONE::checkPermissions("users_analytics"))
                                    <li class="treeview">
                                        <div class="menu-wrapper"><a id="manager" @if(strpos(Route::getCurrentRoute()->getName(), 'analytic') !== false) class='menu-active' @endif href="{{ action("AccessesController@analytic") }}">{{ trans('privateSidebar.analytics') }}</a></div>
                                    </li>
                                @endif
                            </ul>
                        </li>

                        {{-- Moderation --}}
                        <li class="main-menu-title">
                            <div data-toggle="collapse" href="#collapse-moderation" class="title-menu">
                                <div class="row">
                                    <div class="col-9">
                                        <div class="menu-border-bottom">{{ trans('privateSidebar.moderation') }}</div>
                                    </div>
                                    <div class="col-3">
                                        <i class="fa fa-chevron-down pull-right"></i>
                                    </div>
                                </div>
                            </div>

                            <ul id="collapse-moderation" class="collapse sub-menu-wrapper show">
                                @if(ONE::checkPermissions("moderation_users"))
                                    <li class="treeview">
                                        <div class="menu-wrapper">
                                            <a  @if(strpos(Route::getCurrentRoute()->getName(), 'users.indexCompleted') !== false) class="menu-active" @endif href="{{ action("UsersController@indexCompleted") }}">{{ trans('privateSidebar.registration_users_completed') }}</a>
                                        </div>
                                    </li>
                                @endif

                                @if(ONE::checkPermissions("moderation_participation"))
                                    <li class="treeview">
                                        <div class="menu-wrapper"><a @if(strpos($_SERVER['REQUEST_URI'],'topics')) class='menu-active'@endif href="{{ action("ModerationController@topicsToModerate") }}" id="moderation_processes">{{ trans('privateSidebar.moderation_items') }}</a></div>
                                    </li>
                                @endif

                                @if(ONE::checkPermissions("moderation_comments"))
                                    <li class="treeview">
                                        <div class="menu-wrapper"><a @if(strpos($_SERVER['REQUEST_URI'],'posts')) class='menu-active'@endif href="{{ action("ModerationController@postsToModerate") }}" id="moderation_comments">{{ trans('privateSidebar.moderation_comments') }}</a></div>
                                    </li>
                                @endif
                            </ul>
                        </li>

                        {{-- Communiation --}}
                        <li class="main-menu-title">
                            <div data-toggle="collapse" href="#collapse-communication" class="title-menu">
                                <div class="row">
                                    <div class="col-9">
                                        <div class="menu-border-bottom">{{ trans('privateSidebar.communication') }}</div>
                                    </div>
                                    <div class="col-3">
                                        <i class="fa fa-chevron-down pull-right"></i>
                                    </div>
                                </div>
                            </div>

                            <ul id="collapse-communication" class="collapse sub-menu-wrapper show">
                                @if(ONE::checkPermissions("communication_email"))
                                    <li class="treeview">
                                        <div class="menu-wrapper"><a href="{{ action("EmailsController@showSummary") }}" id="emails">{{ trans('privateSidebar.email') }}</a></div>
                                    </li>
                                @endif

                                @if(ONE::checkPermissions("communication_sms"))
                                    <li class="treeview">
                                        <div class="menu-wrapper"><a href="{{ action("SmsController@index") }}" id="question">{{ trans('privateSidebar.sms') }}</a></div>
                                    </li>
                                @endif

                                @if(ONE::checkPermissions("communication_intMessages"))
                                    <li class="treeview">
                                        <div class="menu-wrapper"><a @if(strpos(Route::getCurrentRoute()->getName(), 'entityMessages') !== false) class="menu-active" @endif  href="{{action("EntityMessagesController@index")}}" id="all_messages">
                                                {{ trans('privateSidebar.all_messages') }}</a>
                                        </div>
                                    </li>
                                @endif
                            </ul>
                        </li>

                        {{-- Other tools --}}
                        <li class="main-menu-title">
                            <div data-toggle="collapse" href="#collapse-research" class="title-menu">
                                <div class="row">
                                    <div class="col-9">
                                        <div class="menu-border-bottom">{{ trans('privateSidebar.other_tools') }}</div>
                                    </div>
                                    <div class="col-3">
                                        <i class="fa fa-chevron-down pull-right"></i>
                                    </div>
                                </div>
                            </div>

                            <ul id="collapse-research" class="collapse sub-menu-wrapper show">
                                @if(ONE::checkPermissions("other_questionnaire"))
                                    <li class="treeview">
                                        <div class="menu-wrapper"><a id="q" @if(ONE::checkActiveMenu('questionnaire')) class='menu-active'@endif href="{{ action("QuestionnairesController@index") }}" id="question">{{ trans('privateSidebar.questionnaire') }}</a></div>
                                    </li>
                                @endif

                                @if(ONE::checkPermissions("other_polls"))
                                    <li class="treeview">
                                        <div class="menu-wrapper"><a @if(strpos($_SERVER['REQUEST_URI'],'eventSchedule')) class='menu-active'@endif href="{{ action("EventSchedulesController@index") }}" id="poll">{{ trans('privateSidebar.polls') }}</a></div>
                                    </li>
                                @endif

                                @if(ONE::checkPermissions("other_short_links"))
                                    <li class="treeview">
                                        <div class="menu-wrapper"><a @if(strpos(Route::getCurrentRoute()->getName(), 'shortLinks') !== false) class='menu-active' @endif href="{{ action('ShortLinksController@index') }}">{{ trans('privateSidebar.short_links') }}</a></div>
                                    </li>
                                @endif
                            </ul>
                        </li>

                        {{-- Configurations --}}
                        <li class="main-menu-title">
                            <div data-toggle="collapse" href="#collapse-configurations" class="title-menu">
                                <div class="row">
                                    <div class="col-9">
                                        <div class="menu-border-bottom">{{ trans('privateSidebar.configurations') }}</div>
                                    </div>
                                    <div class="col-3">
                                        <i class="fa fa-chevron-down pull-right"></i>
                                    </div>
                                </div>
                            </div>

                            <ul id="collapse-configurations" class="collapse sub-menu-wrapper show">
                                @if(ONE::checkPermissions("conf_entity"))
                                    <li class="treeview">
                                        <div class="menu-wrapper"><a id="entity" @if(strpos(Route::getCurrentRoute()->getName(), 'showEntity') !== false) class='menu-active'@endif href="{{ action("EntitiesDividedController@showEntity") }}" id="entity">{{ trans('privateSidebar.entity') }}</a></div>
                                    </li>
                                @endif
                                @if(ONE::checkPermissions("conf_gamification"))
                                    <li class="treeview">
                                        <div class="menu-wrapper"><a @if(strpos($_SERVER['REQUEST_URI'],'gamification')) class='menu-active'@endif href="{{ action("GamificationsController@index") }}" id="gamification">{{ trans('privateSidebar.gamification') }}</a></div>
                                    </li>
                                @endif
                                @if(ONE::checkPermissions("conf_kiosk"))
                                    <li class="treeview">
                                        <div class="menu-wrapper"><a @if(strpos($_SERVER['REQUEST_URI'],'kiosk')) class='menu-active'@endif href="{{ action("KiosksController@index") }}" id="kiosk">{{ trans('privateSidebar.kiosks') }}</a></div>
                                    </li>
                                @endif

                                @if(ONE::checkPermissions("conf_open_data"))
                                    <li class="treeview">
                                        <div class="menu-wrapper"><a @if(strpos(Route::getCurrentRoute()->getName(), 'openData.list') !== false) class='menu-active'@endif href="{{ action("OpenDataController@index") }}" id="question">{{ trans('privateSidebar.open_data') }}</a></div>
                                    </li>
                                @endif
                            </ul>
                        </li>
                    </ul>
                @endif
            @endif
        </div>
    </section>
</aside>

<script>
    $( document ).ready(function() {
        if(localStorage.getItem('sidebarPosition') == 0) {
            var nextSidebar = localStorage.getItem('currentSidebar');
            if ("nextSidebar" in localStorage  || localStorage.getItem('currentSidebar') != 'private') {
                if("previousSidebar" in localStorage){
                    $(".pager").css('display', 'block');
                    $(".previous").addClass('button-disabled');

                }else if("nextSidebar" in localStorage){
                    $(".pager").css('display', 'block');
                    $(".next").removeClass('button-disabled');
                }
            } else {
                $(".pager").css('display', 'none');
            }
        }else{
            $(".pager").css('display', 'block');
            if ("nextSidebar" in localStorage) {
                $(".pager").css('display', 'block');
                $(".next").removeClass('button-disabled');
            }else{
                $(".next").css('pointer-events', 'none');
                $(".next").addClass('button-disabled');
            }

            if("previousSidebar" in localStorage){
                $(".pager").css('display', 'block');
                $(".previous").removeClass('button-disabled');
            }else{
                $(".previous").addClass('button-disabled');
            }
        }

        var indexSidebar = 0;
        $(".main-sidebar1").css('z-index', '-1')
        $(".main-sidebar1").css('opacity', '0')

        var value = $("#select-option-entity").find("option:selected").val();
        var name = $("#select-option-entity").find("option:selected").text()

        if (value == 0) {
            name = 'MANAGER';
            $('#sidebar-manager-menu').hide();
        } else {
            $('#sidebar-manager-menu').show();
        }

        if (value == undefined) {
            name = 'MANAGER';
        }
        $('#menu_entity_name').html(name);

    });

    function go(element){
        $.ajax({
            url: '{{ action("OneController@getContent") }}',
            data: {name: element},
            type: 'post',

            success: function(response){

                if(element == 'private'){
                    {{--$.ajax({--}}
                    {{--url: "{{ asset(ltrim(elixir("js/private.js", "/"), "/"))}}",--}}
                    {{--dataType: "script",--}}
                    {{--success: function(response){--}}

                    {{--},--}}
                    {{--error: function(){--}}
                    {{--console.log("error");--}}
                    {{--}--}}
                    {{--});--}}
                }
                $(".sidebar-content").html(response);



            },
            complete: function(){

            },

            error: function(){console.log("erro")},
        })
    }

    function goBack(){
        var storedSidebars = localStorage.getItem('previousSidebar');
        var highlightLink = localStorage.getItem('currentSidebar');



        $.ajax({
            url: '{{ action("OneController@getContent") }}',
            data: {name: storedSidebars},
            type: 'post',

            success: function(response){

                if(storedSidebars == 'private'){
                    localStorage.setItem('sidebarPosition', 0)
                    /*
                     $.ajax({
                     url: "{{ asset(ltrim(elixir("js/private.js", "/"), "/"))}}",
                     dataType: "script",
                     method: "get",
                     async: 'true',
                     success: function(response){
                     },
                     error: function(){
                     console.log("error");
                     }
                     });
                     */
                }


//                $(".next").css('pointer-events', 'visible');
//
//                if(localStorage.getItem('sidebarPosition') == 0) {
//                    if ("nextSidebar" in localStorage) {
//                        $(".pager").css('display', 'block');
//                        $(".previous").css('pointer-events', 'none');
//                    }
//                }

                $(".sidebar-content").html(response);

                if("nextSidebar" in localStorage){
                    $(".next").css('pointer-events', 'visible');
                }

//
//                if(localStorage.getItem('positionSidebar') == 0)
//                    $(".previous").css('pointer-events', 'none');

                        @if(isset(Session::get('sidebarArguments')['type']))
                var type = '{{ Session::get('sidebarArguments')['type'] }}';
                $("#"+highlightLink+"_"+type).addClass('menu-active');
                @else
                $("#"+highlightLink).addClass('menu-active');
                @endif


            },
            complete: function(){

            },

            error: function(){console.log("erro")},
        })
    }

    function goForward() {

        if (localStorage.getItem('sidebarPosition') == 0) {
            var nextSidebar = localStorage.getItem('currentSidebar');
            localStorage.setItem('sidebarPosition', 1)
        }else{
            var nextSidebar = localStorage.getItem('nextSidebar');
        }

        $.ajax({
            url: '{{ action("OneController@getContent") }}',
            data: {name: nextSidebar},
            type: 'post',
            success: function(response){
//                $(".pager").css('display', 'block');
//                $(".back").css('pointer-events', 'visible');
//
//                if("nextSidebar" in localStorage){
//                    $(".next").css('pointer-events', 'auto');
//                }else{
//                    $(".next").css('pointer-events', 'none');
//                }


                $(".sidebar-content").html(response);
                $(".previous").removeClass('button-disabled');
                if(localStorage.getItem("nextSidebar") == null) {
                    $(".next").addClass('button-disabled');
                }else{
                    $(".next").removeClass('button-disabled');
                }
            },
            complete: function(){

            },

            error: function(){console.log("erro")},
        })
    }

    function goSidebar(elem){

        //Fetch the content to sub-menu
        $.ajax({
            url: '{{ action("OneController@getContent") }}',
            data: {name: elem},
            type: 'post',
            success: function (response) {
                if (response !== null) {
                    $(".sidebar-content").html(response);

                    //            $(".main-sidebar1").show();


                }
            },
            error: function () {
            },
            complete: function () {
            }
        })
    }


</script>