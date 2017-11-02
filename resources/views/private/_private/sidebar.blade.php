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


            @if(Session::get('user_role') == 'admin' && ONE::getEntityKey() == null)
                @if(isset($sidebar))
                    @includeif('private.sidebar_admin.' . $sidebar)
                @else
                <div class="side-menu-wrapper">
                    <ul class="sidebar-menu-css">
                        <!-- Menu Title -->
                        <li class="main-menu-title">
                            <div class="menu-border-bottom">{{ trans('privateSidebar.admin') }}</div>

                            <!-- Sub Menu -->
                            <ul class="sub-menu-wrapper {{Session::get('user_role') == 'admin'? 'menu-open': ''}}">

                                <li><div class="menu-wrapper"><a id="entities" @if (strpos(Route::getCurrentRoute()->getName(), 'entities') !== false) class="menu-active" @endif href="{{ action("EntitiesController@index") }}"> {{ trans('privateSidebar.entities') }}</a></div></li>
                                <li><div class="menu-wrapper"><a @if (strpos(Route::getCurrentRoute()->getName(), 'languages') !== false) class="menu-active" @endif href="{{ action("LanguagesController@index") }}">{{ trans('privateSidebar.languages') }}</a></div></li>
                                <li><div class="menu-wrapper"><a @if (strpos(Route::getCurrentRoute()->getName(), 'countries') !== false) class="menu-active" @endif href="{{ action("CountriesController@index") }}"> {{ trans('privateSidebar.countries') }}</a></div></li>
                                <li><div class="menu-wrapper"><a @if (strpos(Route::getCurrentRoute()->getName(), 'timezones') !== false) class="menu-active" @endif href="{{ action("TimezonesController@index") }}"> {{ trans('privateSidebar.timezones') }}</a></div></li>
                                <li><div class="menu-wrapper"><a @if (strpos(Route::getCurrentRoute()->getName(), 'currencies') !== false) class="menu-active" @endif href="{{ action("CurrenciesController@index") }}">{{ trans('privateSidebar.curencies') }}</a></div></li>

                                <li><div class="menu-wrapper"><a id="votes" href="#" onclick="goSidebar('votes')" id="votes">{{ trans('privateSidebar.votes') }}</a></div></li>

                                <li><div class="menu-wrapper"><a id="cbs_configs" @if (strpos(Route::getCurrentRoute()->getName(), 'cbConfigType') !== false) class="menu-active" @endif href="{{ action("CbsConfigTypesController@index") }}">{{ trans('privateSidebar.cbsConfigs') }}</a></div></li>
                                <li><div class="menu-wrapper"><a @if (strpos(Route::getCurrentRoute()->getName(), 'authMethods') !== false) class="menu-active" @endif href="{{ action("AuthMethodsController@index") }}"> {{ trans('privateSidebar.auth_methods') }}</a></div></li>
                                <li><div class="menu-wrapper"><a id="modules" @if (strpos(Route::getCurrentRoute()->getName(), 'module') !== false) class="menu-active" @endif href="{{ action("ModulesController@index") }}"> {{ trans('privateSidebar.modules') }}</a></div></li>
                                <li><div class="menu-wrapper"><a @if (strpos(Route::getCurrentRoute()->getName(), 'users') !== false)  class="menu-active" @endif href="{{ action("UsersController@index",['role' => 'admin']) }}"> {{ trans('privateSidebar.users') }}</a></div></li>
                                <li><div class="menu-wrapper"><a @if (strpos(Route::getCurrentRoute()->getName(), 'layout') !== false) class="menu-active" @endif href="{{ action("LayoutsController@index") }}"> {{ trans('privateSidebar.layouts') }}</a></div></li>
                                <li><div class="menu-wrapper"><a @if (strpos(Route::getCurrentRoute()->getName(), 'parameterUserTypes') !== false) class="menu-active" @endif href="{{ action("ParameterUserTypesController@index") }}"> {{ trans('privateSidebar.parameter_types') }}</a></div></li>
                                <li><div class="menu-wrapper"><a id="siteConfs" @if (strpos(Route::getCurrentRoute()->getName(), 'siteConfGroup') !== false) class="menu-active" @endif href="{{ action("SiteConfGroupController@index") }}"> {{ trans('privateSidebar.title') }}</a></div></li>

                                <li><div class="menu-wrapper"><a @if (strpos(Route::getCurrentRoute()->getName(), 'questIcon') !== false) class="menu-active" @endif href="{{ action("QuestIconsController@index") }}">{{ trans('privateSidebar.questionnaires') }}</a></div></li>

                                <li><div class="menu-wrapper"><a @if (strpos(Route::getCurrentRoute()->getName(), 'groupTypes') !== false) class="menu-active" @endif href="{{ action("GroupTypesController@index") }}"> {{ trans('privateSidebar.group_types') }}</a></div></li>
                                <li><div class="menu-wrapper"><a @if (strpos(Route::getCurrentRoute()->getName(), 'parameterType') !== false) class="menu-active" @endif href="{{ action("ParameterTypesController@index") }}"> {{ trans('privateSidebar.parameter_pads_types') }}</a></div></li>
                                <li><div class="menu-wrapper"><a @if (strpos($_SERVER['REQUEST_URI'],'TrackingController')) class="menu-active" @endif href="{{ action("TrackingController@showTracking") }}"> Logs </a></div></li>


                                <li><div class="menu-wrapper"><a @if (strpos(Route::getCurrentRoute()->getName(), 'CMSectionTypes') !== false) class="menu-active" @endif href="{{ action("CMSectionTypesController@index") }}">{{ trans('privateSidebar.cms_section_types') }}</a></div></li>
                                <li><div class="menu-wrapper"><a @if (strpos(Route::getCurrentRoute()->getName(), 'CMSectionTypeParameters') !== false) class="menu-active" @endif href="{{ action("CMSectionTypeParametersController@index") }}">{{ trans('privateSidebar.cms_section_type_parameters') }}</a></div></li>
                                <li><div class="menu-wrapper"><a @if (strpos(Route::getCurrentRoute()->getName(), 'flagTypes') !== false) class="menu-active" @endif href="{{ action("FlagTypesController@index") }}">{{ trans('privateSidebar.flag_types') }}</a></div></li>

                                <li><div class="menu-wrapper"><a @if (strpos(Route::getCurrentRoute()->getName(), 'dashBoardElements') !== false) class='menu-active' @endif href="{{ action("DashBoardElementsController@index") }}">{{ trans('privateSidebar.dashboard_elements') }}</a></div></li>
                                <li><div class="menu-wrapper"><a @if (strpos(Route::getCurrentRoute()->getName(), 'dashBoardElementConfigurations') !== false) sclass='menu-active' @endif href="{{ action("DashBoardElementConfigurationsController@index") }}">{{ trans('privateSidebar.dashboard_element_configurations') }}</a></div></li>

                                <li><div class="menu-wrapper"><a @if (strpos(Route::getCurrentRoute()->getName(), 'BEMenuElementConfigurations') !== false) class="menu-active" @endif href="{{ action("BEMenuElementParametersController@index") }}">{{ trans('privateSidebar.BEMenuElementConfigurations') }}</a></div></li>
                                <li><div class="menu-wrapper"><a @if (strpos(Route::getCurrentRoute()->getName(), 'BEMenuElements') !== false) class="menu-active" @endif href="{{ action("BEMenuElementsController@index") }}">{{ trans('privateSidebar.BEMenuElements') }}</a></div></li>
                            </ul>
                        </li>
                    </ul>
                </div>
                @endif
                {{--@elseif(ONE::asPermission('admin') && ONE::isEntity())--}}
                {{--<ul class="sidebar-menu">--}}
                {{--<li class="treeview">--}}
                {{--<a onclick="reloadAdminMeu()" class="back2AdminMenu"><span>{{ trans('privateSidebar.back_to_admin_menu') }}</span></a>--}}
                {{--</li>--}}
                {{--</ul>--}}
            @endif

            @if(ONE::getEntityKey() != null)
                @if(isset($sidebar))
                    @include('private.sidebar.' . $sidebar)
                @else
                    <ul class="sidebar-menu" style="height: auto; width: auto; padding: 5px 10px 10px; color: rgb(60, 141, 188);">
                        @if (One::entityHasBEMenu() && ONE::getUserKey()!="OKtxhee8gnkTyPVlWLVMfZkiHfApnS4G" && ONE::getUserKey()!="HGqbDfHnfDxMFstQcpKZSl0XaG5XaNZ0" && ONE::getUserKey()!="ReRUSLZs9RvZ1CBinzLPOF9xgeyWKnxS")
                            {!! ONE::buildNestedBEMenu(One::getEntityBEMenu()->ordered_elements) !!}
                        @elseif(ONE::getUserKey()=="OKtxhee8gnkTyPVlWLVMfZkiHfApnS4G" || ONE::getUserKey()=="HGqbDfHnfDxMFstQcpKZSl0XaG5XaNZ0" || ONE::getUserKey()=="ReRUSLZs9RvZ1CBinzLPOF9xgeyWKnxS")
                            <ul id="collapse-configurations" class="collapse sub-menu-wrapper show">
                                <li class="treeview">
                                    <div class="menu-wrapper">
                                        <a href="{{ action("CbsController@showTopics",["type"=>"proposal","cbKey"=>"dxzexzdalt7CB8JZxaJYORS3NTIjZugw"]) }}">
                                            Propostas
                                        </a>
                                    </div>
                                </li>
                            </ul>
                        @else
                            @if(ONE::verifyModuleAccess('cb'))
                                {{--<li class="treeview">--}}
                                {{--<div style="padding-top: 5px"><a href="{{ action("CbsController@newCbCreate",['type' => 'idea']) }}">{{ trans('privateSidebar.newCb') }}</a></div>--}}
                                {{--</li>--}}

                                @if(!is_null(Session::get('user_permissions_sidebar_groups')) && in_array('participation', Session::get('user_permissions_sidebar_groups')) || sizeOf(Session::get('user_permissions_sidebar_groups')) == 1)
                                    {{--
                                                                    <div style="border-top: solid #CCCCCC 1px; padding-top: 3px; font-style: italic; font-weight: bold; color: #999999">{{trans('privateSidebar.participation')}}</div>
                                    --}}
                                    <li class="main-menu-title">
                                        <div data-toggle="collapse" href="#collapse-participation" class="title-menu"  toggle=false>
                                            <div class="row">
                                                <div class="col-9">
                                                    <div class="
                                                    @if(strpos($_SERVER['REQUEST_URI'],'phase1') || strpos($_SERVER['REQUEST_URI'],'phase2') || strpos($_SERVER['REQUEST_URI'],'phase3') || strpos($_SERVER['REQUEST_URI'],'idea')
                                                        || strpos($_SERVER['REQUEST_URI'],'forum') || strpos($_SERVER['REQUEST_URI'],'discussion') || strpos($_SERVER['REQUEST_URI'],'proposal') || strpos($_SERVER['REQUEST_URI'],'publicConsultation')
                                                        || strpos($_SERVER['REQUEST_URI'],'tematicConsultation') || strpos($_SERVER['REQUEST_URI'],'survey') || strpos($_SERVER['REQUEST_URI'],'project') || strpos($_SERVER['REQUEST_URI'],'project')
                                                        || strpos($_SERVER['REQUEST_URI'],'project_2c') || (isset($name_view) && str_is("mp", $name_view)) || (strpos(Route::getCurrentRoute()->getName(), 'eventSchedule') !== false)
                                                        || strpos($_SERVER['REQUEST_URI'],'kiosk') || strpos($_SERVER['REQUEST_URI'],'moderation_items') )
                                                            menu-border-bottom-active
                                                    @else
                                                            menu-border-bottom
                                                    @endif
                                                            ">
                                                        {{trans('privateSidebar.participation')}}
                                                    </div>
                                                </div>
                                                <div class="col-3">
                                                    <i class="fa
                                                    @if(strpos($_SERVER['REQUEST_URI'],'phase1') || strpos($_SERVER['REQUEST_URI'],'phase2') || strpos($_SERVER['REQUEST_URI'],'phase3') || strpos($_SERVER['REQUEST_URI'],'idea')
                                                        || strpos($_SERVER['REQUEST_URI'],'forum') || strpos($_SERVER['REQUEST_URI'],'discussion') || strpos($_SERVER['REQUEST_URI'],'proposal') || strpos($_SERVER['REQUEST_URI'],'publicConsultation')
                                                        || strpos($_SERVER['REQUEST_URI'],'tematicConsultation') || strpos($_SERVER['REQUEST_URI'],'survey') || strpos($_SERVER['REQUEST_URI'],'project') || strpos($_SERVER['REQUEST_URI'],'project')
                                                        || strpos($_SERVER['REQUEST_URI'],'project_2c') || (isset($name_view) && str_is("mp", $name_view)) || (strpos(Route::getCurrentRoute()->getName(), 'eventSchedule') !== false)
                                                        || strpos($_SERVER['REQUEST_URI'],'kiosk') || strpos($_SERVER['REQUEST_URI'],'moderation_items') )
                                                            fa-chevron-up
                                                    @else
                                                            fa-chevron-down
                                                    @endif
                                                            pull-right"></i>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Sub menu collapsed - start #collapse-participation -->
                                        <ul id="collapse-participation" class="collapse sub-menu-wrapper show">
                                            @endif

                                            @if(ONE::verifyModuleAccess('cb','phase1'))
                                                @if(!is_null(Session::get('user_permissions_sidebar')) && in_array('phase1', Session::get('user_permissions_sidebar')) || sizeOf(Session::get('user_permissions_sidebar')) == 1)
                                                    <li class="treeview">
                                                        <div class="menu-wrapper">
                                                            <a id="padsType_phase1" @if(strpos($_SERVER['REQUEST_URI'],'phase1')) class='menu-active'@endif href="{{ action("CbsController@indexManager",['typeFilter' => 'phase1']) }}">  {{ trans('privateSidebar.phase1') }}</a>
                                                        </div>
                                                    </li>
                                                @endif
                                            @endif
                                            @if(ONE::verifyModuleAccess('cb','phase2'))
                                                @if(!is_null(Session::get('user_permissions_sidebar')) && in_array('phase2', Session::get('user_permissions_sidebar')) || sizeOf(Session::get('user_permissions_sidebar')) == 1)
                                                    <li class="treeview">
                                                        <div class="menu-wrapper"><a id="padsType_phase2" @if(strpos($_SERVER['REQUEST_URI'],'phase2')) class='menu-active'@endif href="{{ action("CbsController@indexManager",['typeFilter' => 'phase2']) }}">  {{ trans('privateSidebar.phase2') }}</a></div>
                                                    </li>
                                                @endif
                                            @endif
                                            @if(ONE::verifyModuleAccess('cb','phase3'))
                                                @if(!is_null(Session::get('user_permissions_sidebar')) && in_array('phase3', Session::get('user_permissions_sidebar')) || sizeOf(Session::get('user_permissions_sidebar')) == 1)
                                                    <li class="treeview">
                                                        <div class="menu-wrapper"><a id="padsType_phase3" @if(strpos($_SERVER['REQUEST_URI'],'phase3')) class='menu-active'@endif href="{{ action("CbsController@indexManager",['typeFilter' => 'phase3']) }}">  {{ trans('privateSidebar.phase3') }}</a></div>
                                                    </li>
                                                @endif
                                            @endif
                                            @if(ONE::verifyModuleAccess('cb','qa'))
                                                @if(!is_null(Session::get('user_permissions_sidebar')) && in_array('qa', Session::get('user_permissions_sidebar')) || sizeOf(Session::get('user_permissions_sidebar')) == 1)
                                                    <li class="treeview">
                                                        <div class="menu-wrapper"><a id="padsType_qa" @if(strpos($_SERVER['REQUEST_URI'],'qa')) class='menu-active'@endif href="{{ action("CbsController@indexManager",['typeFilter' => 'qa']) }}">  {{ trans('privateSidebar.q_a') }}</a></div>
                                                    </li>
                                                @endif
                                            @endif
                                            @if(ONE::verifyModuleAccess('cb','idea'))
                                                @if(!is_null(Session::get('user_permissions_sidebar')) && in_array('idea', Session::get('user_permissions_sidebar')) || sizeOf(Session::get('user_permissions_sidebar')) == 1)
                                                    <li class="treeview">
                                                        <div class="menu-wrapper"><a id="padsType_idea" @if(strpos($_SERVER['REQUEST_URI'],'idea')) class='menu-active'@endif href="{{ action("CbsController@indexManager",['typeFilter' => 'idea']) }}">  {{ trans('privateSidebar.idea') }}</a></div>
                                                    </li>
                                                @endif
                                            @endif
                                            @if(ONE::verifyModuleAccess('cb','event'))
                                                @if(!is_null(Session::get('user_permissions_sidebar')) && in_array('events', Session::get('user_permissions_sidebar')) || sizeOf(Session::get('user_permissions_sidebar')) == 1)
                                                    <li class="treeview">
                                                        <div class="menu-wrapper"><a id="padsType_idea" @if(strpos($_SERVER['REQUEST_URI'],'event')) class='menu-active'@endif href="{{ action("CbsController@indexManager",['typeFilter' => 'event']) }}">  {{ trans('privateSidebar.event') }}</a></div>
                                                    </li>
                                                @endif
                                            @endif
                                            @if(ONE::verifyModuleAccess('cb','forum'))
                                                @if(!is_null(Session::get('user_permissions_sidebar')) && in_array('forum', Session::get('user_permissions_sidebar'))  || sizeOf(Session::get('user_permissions_sidebar')) == 1)
                                                    <li class="treeview">
                                                        <div class="menu-wrapper"><a id="padsType_forum" @if(strpos($_SERVER['REQUEST_URI'],'forum')) class='menu-active'@endif @if(strpos($_SERVER['REQUEST_URI'],'forum')) class='menu-active'@endif href="{{ action("CbsController@indexManager",['typeFilter' => 'forum']) }}"> {{ trans('privateSidebar.forum') }}</a></div>
                                                    </li>
                                                @endif
                                            @endif
                                            @if(ONE::verifyModuleAccess('cb','discussion'))
                                                @if(!is_null(Session::get('user_permissions_sidebar')) && in_array('discussion', Session::get('user_permissions_sidebar')) || sizeOf(Session::get('user_permissions_sidebar')) == 1)
                                                    <li class="treeview">
                                                        <div class="menu-wrapper"><a id="padsType_discussion" @if(strpos($_SERVER['REQUEST_URI'],'discussion')) class='menu-active'@endif href="{{ action("CbsController@indexManager",['typeFilter' => 'discussion']) }}">  {{ trans('privateSidebar.discussion') }}</a></div>
                                                    </li>
                                                @endif
                                            @endif
                                            @if(ONE::verifyModuleAccess('cb','proposal'))
                                                @if(!is_null(Session::get('user_permissions_sidebar')) && in_array('proposal', Session::get('user_permissions_sidebar'))  || sizeOf(Session::get('user_permissions_sidebar')) == 1)
                                                    <li class="treeview">
                                                        <div class="menu-wrapper"><a id="padsType_proposal" @if(strpos($_SERVER['REQUEST_URI'],'proposal')) class='menu-active'@endif href="{{ action("CbsController@indexManager",['typeFilter' => 'proposal']) }}">  {{ trans('privateSidebar.proposal') }}</a></div>
                                                    </li>
                                                @endif
                                            @endif
                                            @if(ONE::verifyModuleAccess('cb','publicConsultation'))
                                                @if(!is_null(Session::get('user_permissions_sidebar')) && in_array('publicConsultation', Session::get('user_permissions_sidebar'))  || sizeOf(Session::get('user_permissions_sidebar')) == 1)
                                                    <li class="treeview">
                                                        <div class="menu-wrapper"><a id="padsType_publicConsultation" @if(strpos($_SERVER['REQUEST_URI'],'publicConsultation')) class='menu-active'@endif href="{{ action("CbsController@indexManager",['typeFilter' => 'publicConsultation']) }}"> {{ trans('privateSidebar.publicConsultation') }}</a></div>
                                                    </li>
                                                @endif
                                            @endif
                                            @if(ONE::verifyModuleAccess('cb','tematicConsultation'))
                                                @if(!is_null(Session::get('user_permissions_sidebar')) && in_array('tematicConsultation', Session::get('user_permissions_sidebar')) || sizeOf(Session::get('user_permissions_sidebar')) == 1)
                                                    <li class="treeview">
                                                        <div class="menu-wrapper"><a id="padsType_tematicConsultation" @if(strpos($_SERVER['REQUEST_URI'],'tematicConsultation')) class='menu-active'@endif href="{{ action("CbsController@indexManager",['typeFilter' => 'tematicConsultation']) }}"> {{ trans('privateSidebar.tematicConsultation') }}</a></div>
                                                    </li>
                                                @endif
                                            @endif
                                            @if(ONE::verifyModuleAccess('cb','survey'))
                                                @if(!is_null(Session::get('user_permissions_sidebar')) && in_array('survey', Session::get('user_permissions_sidebar')) || sizeOf(Session::get('user_permissions_sidebar')) == 1)
                                                    <li class="treeview">
                                                        <div class="menu-wrapper"><a id="padsType_survey" @if(strpos($_SERVER['REQUEST_URI'],'survey')) class='menu-active'@endif href="{{ action("CbsController@indexManager",['typeFilter' => 'survey']) }}"> {{ trans('privateSidebar.survey') }}</a></div>
                                                    </li>
                                                @endif
                                            @endif
                                            @if(ONE::verifyModuleAccess('cb','project'))
                                                @if(!is_null(Session::get('user_permissions_sidebar')) && in_array('project', Session::get('user_permissions_sidebar')) || sizeOf(Session::get('user_permissions_sidebar')) == 1)
                                                    <li class="treeview">
                                                        @php
                                                            $test = explode("=",basename($_SERVER['REQUEST_URI']));
                                                        @endphp
                                                        <div class="menu-wrapper"><a id="padsType_project" @if(strpos($_SERVER['REQUEST_URI'],'project') && strpos(basename($_SERVER['REQUEST_URI']),"=") && explode("=",basename($_SERVER['REQUEST_URI']))[0]=="project") class='menu-active'@endif href="{{ action("CbsController@indexManager",['typeFilter' => 'project']) }}"> {{ trans('privateSidebar.project') }}</a></div>
                                                    </li>
                                                @endif
                                            @endif
                                            @if(ONE::verifyModuleAccess('cb','project_2c'))
                                                <li class="treeview">
                                                    <div class="menu-wrapper"><a @if(strpos($_SERVER['REQUEST_URI'],'project_2c') && strpos(basename($_SERVER['REQUEST_URI']),"=") && explode("=",basename($_SERVER['REQUEST_URI']))[0]=="project_2c" ) class='menu-active'@endif href="{{ action("CbsController@indexManager",['typeFilter' => 'project_2c']) }}"> {{ trans('privateSidebar.project_2c') }}</a></div>
                                                </li>
                                            @endif
                                            @if(ONE::verifyModuleAccess('mp'))
                                                @if(!is_null(Session::get('user_permissions_sidebar')) && in_array('mp',  Session::get('user_permissions_sidebar')) || sizeOf(Session::get('user_permissions_sidebar')) == 1)
                                                    <li class="treeview">
                                                        <div class="menu-wrapper"><a @if(ONE::checkActiveMenu('mps')) class='menu-active'@endif href="{{ action("MPsController@index") }}" id="draw">{{ trans('privateSidebar.draw') }}</a></div>
                                                    </li>
                                                @endif
                                            @endif
                                            @if(ONE::verifyModuleAccess('q','poll'))
                                                @if(!is_null(Session::get('user_permissions_sidebar')) && in_array('poll', Session::get('user_permissions_sidebar')) || sizeOf(Session::get('user_permissions_sidebar')) == 1)
                                                    <li class="treeview">
                                                        <div class="menu-wrapper"><a @if(strpos($_SERVER['REQUEST_URI'],'eventSchedule')) class='menu-active'@endif href="{{ action("EventSchedulesController@index") }}" id="poll">{{ trans('privateSidebar.polls') }}</a></div>
                                                    </li>
                                                @endif
                                            @endif
                                            @if(ONE::verifyModuleAccess('kiosk'))
                                                @if(!is_null(Session::get('user_permissions_sidebar')) && in_array('kiosk', Session::get('user_permissions_sidebar')) || sizeOf(Session::get('user_permissions_sidebar')) == 1)
                                                    <li class="treeview">
                                                        <div class="menu-wrapper"><a @if(strpos($_SERVER['REQUEST_URI'],'kiosk')) class='menu-active'@endif href="{{ action("KiosksController@index") }}" id="kiosk">{{ trans('privateSidebar.kiosks') }}</a></div>
                                                    </li>
                                                @endif
                                            @endif
                                            @if(ONE::verifyModuleAccess('cb','moderation'))
                                                @if(!is_null(Session::get('user_permissions_sidebar')) && in_array('moderation', Session::get('user_permissions_sidebar')) || sizeOf(Session::get('user_permissions_sidebar')) == 1)
                                                    <li class="treeview">
                                                        <div class="menu-wrapper"><a @if(strpos($_SERVER['REQUEST_URI'],'moderation_items')) class='menu-active'@endif href="{{ action("ModerationController@topicsToModerate") }}" id="moderation">{{ trans('privateSidebar.moderation_items') }}</a></div>
                                                    </li>
                                            @endif
                                        @endif

                                        @if(!is_null(Session::get('user_permissions_sidebar_groups')) && in_array('participation', Session::get('user_permissions_sidebar_groups')) || sizeOf(Session::get('user_permissions_sidebar_groups')) == 1)
                                            <!-- End #collapse-participation -->
                                        </ul>
                                    </li>

                                @endif
                            @endif


                            {{--hides most of the sidebar to non-admins--}}
                            @if(ONE::verifyModuleAccess('cm'))
                            <!-- -*-*-*-*-*-*-*-*-*- Contents -*-*-*-*-*-*-*-*-*- -->
                                @if(!is_null(Session::get('user_permissions_sidebar_groups')) && in_array('contents', Session::get('user_permissions_sidebar_groups')) || sizeOf(Session::get('user_permissions_sidebar_groups')) == 1)
                                    @php $url = "http" . (($_SERVER['SERVER_PORT'] == 443) ? "s://" : "://") . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; @endphp
                                    <li class="main-menu-title">
                                        <div data-toggle="collapse" href="#collapse-contents" class="title-menu">
                                            <div class="row">
                                                <div class="col-9">
                                                    <div class="menu-border-bottom
                                        @if(strpos(Route::getCurrentRoute()->getName(), 'entitySites') !== false || strpos(Route::getCurrentRoute()->getName(), 'accessMenus') !== false || (isset($type) && $type == "pages")
                                            || (isset($type) && $type == "articles") || (isset($type) && $type == "news") || (isset($type) && $type == "events") || strpos(Route::getCurrentRoute()->getName(), 'contentTypeTypes') !== false || (Route::getCurrentRoute()->getUri() == 'translations')
                                            || (strpos(Route::getCurrentRoute()->getName(), 'faqs') !== false ) || (strpos(Route::getCurrentRoute()->getName(), 'municipal_faqs') !== false ))
                                                            menu-border-bottom-active
@else
                                                            menu-border-bottom
@endif
                                                            ">
                                                        {{ trans('privateSidebar.contents') }}
                                                    </div>
                                                </div>
                                                <div class="col-3">
                                                    <i class="fa
                                        @if(strpos(Route::getCurrentRoute()->getName(), 'entitySites') !== false || strpos(Route::getCurrentRoute()->getName(), 'accessMenus') !== false || (isset($type) && $type == "pages")
                                            || (isset($type) && $type == "articles") || (isset($type) && $type == "news") || (isset($type) && $type == "events") || strpos(Route::getCurrentRoute()->getName(), 'contentTypeTypes') !== false || (Route::getCurrentRoute()->getUri() == 'translations')
                                            || (strpos(Route::getCurrentRoute()->getName(), 'faqs') !== false ) || (strpos(Route::getCurrentRoute()->getName(), 'municipal_faqs') !== false ))
                                                            fa fa-chevron-up
                                            @else
                                                            fa fa-chevron-down
                                            @endif
                                                            pull-right"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Sub menu collapsed - start #collapse-contents -->
                                        <ul id="collapse-contents" class="collapse sub-menu-wrapper show">
                                            @endif


                                            {{--hides most of the sidebar to non-admins--}}
                                            @if(ONE::verifyModuleAccess('cm'))

                                                @if(!is_null(Session::get('user_permissions_sidebar')) && in_array('entity_site', Session::get('user_permissions_sidebar')) || sizeOf(Session::get('user_permissions_sidebar')) == 1)
                                                    <li class="treeview">
                                                        <div class="menu-wrapper"><a id="site" @if(strpos(Route::getCurrentRoute()->getName(), 'entitySites') !== false) class="menu-active" @endif href="{{ action("EntitiesSitesController@index") }}">{{ trans('privateSidebar.sites') }}</a></div>
                                                    </li>
                                                @endif

                                                @if(ONE::verifyModuleAccess('cm','menu'))
                                                    @if(!is_null(Session::get('user_permissions_sidebar')) && in_array('menu', Session::get('user_permissions_sidebar')) || sizeOf(Session::get('user_permissions_sidebar')) == 1)
                                                        <li class="treeview">
                                                            <div class="menu-wrapper"><a id="menu" @if(strpos(Route::getCurrentRoute()->getName(), 'accessMenus') !== false) class="menu-active" @endif href="{{ action("AccessMenusController@index") }}">{{ trans('privateSidebar.menus') }}</a></div>
                                                        </li>
                                                    @endif
                                                @endif

                                                @if(ONE::verifyModuleAccess('cm','pages'))
                                                    @if(!is_null(Session::get('user_permissions_sidebar')) && in_array('pages', Session::get('user_permissions_sidebar')) || sizeOf(Session::get('user_permissions_sidebar')) == 1)
                                                        <li class="treeview">
                                                            <div class="menu-wrapper">
                                                                <a @if($_SERVER['REQUEST_URI'] == "/private/content/type/pages")
                                                                   class="menu-active"
                                                                   @endif href="{{ action("ContentsController@index", "pages") }}">
                                                                    {{ trans('privateSidebar.pages') }}
                                                                </a>
                                                            </div>
                                                        </li>
                                                        <li class="treeview">
                                                            {{--@if (One::isAdmin())--}}
                                                            <div class="menu-wrapper">
                                                                <a @if($_SERVER['REQUEST_URI'] == "/private/newContent/pages") class="menu-active" @endif href="{{ action('ContentManagerController@index', ['contentType'=>'pages']) }}">
                                                                    {{ trans('privateSidebar.pages') }}
                                                                    <span class="new-menu">
                                                                    <i class="fa fa-asterisk" aria-hidden="true"></i> NEW
                                                                </span>
                                                                </a>
                                                            </div>
                                                            {{--@endif--}}
                                                        </li>
                                                    @endif
                                                @endif

                                                @if(ONE::verifyModuleAccess('cm','articles'))
                                                    @if(!is_null(Session::get('user_permissions_sidebar')) && in_array('news', Session::get('user_permissions_sidebar')) || sizeOf(Session::get('user_permissions_sidebar')) == 1)
                                                        <li class="treeview">
                                                            <div class="menu-wrapper"><a @if($_SERVER['REQUEST_URI'] == "/private/newContent/articles") class="menu-active" @endif href="{{ action('ContentManagerController@index', ['contentType'=>'articles']) }}">{{ trans('privateSidebar.articles') }}</a></div>
                                                        </li>
                                                    @endif
                                                @endif

                                                @if(ONE::verifyModuleAccess('cm','gatherings'))
                                                    @if(!is_null(Session::get('user_permissions_sidebar')) && in_array('gatherings', Session::get('user_permissions_sidebar')) || sizeOf(Session::get('user_permissions_sidebar')) == 1)
                                                        <li class="treeview">
                                                            <div class="menu-wrapper"><a @if($_SERVER['REQUEST_URI'] == "/private/newContent/gatherings") class="menu-active" @endif href="{{ action('ContentManagerController@index', ['contentType'=>'gatherings']) }}">{{ trans('privateSidebar.gatherings') }}</a></div>
                                                        </li>
                                                    @endif
                                                @endif

                                                @if(ONE::verifyModuleAccess('cm','faqs'))
                                                    @if(!is_null(Session::get('user_permissions_sidebar')) && in_array('news', Session::get('user_permissions_sidebar')) || sizeOf(Session::get('user_permissions_sidebar')) == 1)
                                                        <li class="treeview">
                                                            <div class="menu-wrapper"><a @if($_SERVER['REQUEST_URI'] == "/private/newContent/faqs") class="menu-active" @endif href="{{ action('ContentManagerController@index', ['contentType'=>'faqs']) }}">{{ trans('privateSidebar.faqs') }}</a></div>
                                                        </li>
                                                    @endif
                                                @endif

                                                @if(ONE::verifyModuleAccess('cm','municipal_faqs'))
                                                    @if(!is_null(Session::get('user_permissions_sidebar')) && in_array('news', Session::get('user_permissions_sidebar')) || sizeOf(Session::get('user_permissions_sidebar')) == 1)
                                                        <li class="treeview">
                                                            <div class="menu-wrapper"><a @if($_SERVER['REQUEST_URI'] == "/private/newContent/municipal_faqs") class="menu-active" @endif href="{{ action('ContentManagerController@index', ['contentType'=>'municipal_faqs']) }}">{{ trans('privateSidebar.municipal_faqs') }}</a></div>
                                                        </li>
                                                    @endif
                                                @endif
                                                @if(ONE::verifyModuleAccess('cm','news'))
                                                    @if(!is_null(Session::get('user_permissions_sidebar')) && in_array('news', Session::get('user_permissions_sidebar')) || sizeOf(Session::get('user_permissions_sidebar')) == 1)
                                                        <li class="treeview">
                                                            <div class="menu-wrapper"><a @if(isset($type) && $type == "news")  class="menu-active" @endif href="{{ action("ContentsController@index", "news") }}">{{ trans('privateSidebar.news') }}</a></div>
                                                        </li>
                                                        <li class="treeview">
                                                            <div class="menu-wrapper">
                                                                <a @if($_SERVER['REQUEST_URI'] == "/private/newContent/news") class="menu-active" @endif href="{{ action('ContentManagerController@index', ['contentType'=>'news']) }}">{{ trans('privateSidebar.news') }}
                                                                    <span class="new-menu">
                                                                        <i class="fa fa-asterisk" aria-hidden="true"></i> NEW
                                                                    </span>
                                                                </a>
                                                            </div>
                                                        </li>
                                                    @endif
                                                @endif

                                                @if(ONE::verifyModuleAccess('cm', 'events'))
                                                    @if(!is_null(Session::get('user_permissions_sidebar')) && in_array('events', Session::get('user_permissions_sidebar')) || sizeOf(Session::get('user_permissions_sidebar')) == 1)
                                                        <li class="treeview">
                                                            <div class="menu-wrapper"><a @if(isset($type) && $type == "events") class="menu-active" @endif href="{{ action("ContentsController@index", "events") }}">{{ trans('privateSidebar.events') }}</a></div>
                                                        </li>
                                                    @endif
                                                @endif

                                                @if(ONE::verifyModuleAccess('cm','pages') || ONE::verifyModuleAccess('cm','news') || ONE::verifyModuleAccess('cm','events'))
                                                    @if(!is_null(Session::get('user_permissions_sidebar')) && in_array('content_subtypes', Session::get('user_permissions_sidebar')) || sizeOf(Session::get('user_permissions_sidebar')) == 1)
                                                        <li class="treeview">
                                                            <div class="menu-wrapper"><a @if(strpos(Route::getCurrentRoute()->getName(), 'contentTypeTypes') !== false) class="menu-active" @endif href="{{ action("ContentTypeTypesController@index") }}">{{ trans('privateSidebar.content_type_types') }}</a></div>
                                                        </li>
                                                    @endif
                                                @endif

                                                @if(ONE::verifyModuleAccess('wui', 'translations'))
                                                    @if(!is_null(Session::get('user_permissions_sidebar')) && in_array('translations', Session::get('user_permissions_sidebar')) || sizeOf(Session::get('user_permissions_sidebar')) == 1)
                                                        <li class="treeview">
                                                            <div class="menu-wrapper"><a @if (Route::getCurrentRoute()->getUri() == 'translations') class="active" @endif href="{{ url("/translations") }}">{{ trans('privateSidebar.translations') }}</a></div>
                                                        </li>
                                                @endif
                                            @endif
                                        @endif
                                        @if(!is_null(Session::get('user_permissions_sidebar_groups')) && in_array('contents', Session::get('user_permissions_sidebar_groups')) || sizeOf(Session::get('user_permissions_sidebar_groups')) == 1)
                                            <!-- End #collapse-content -->
                                        </ul>
                                    </li>
                                @endif
                            @endif




                            @if(ONE::verifyModuleAccess('auth'))

                            <!-- -*-*-*-*-*-*-*-*-*- Users -*-*-*-*-*-*-*-*-*- -->
                                @if(!is_null(Session::get('user_permissions_sidebar_groups')) && in_array('users', Session::get('user_permissions_sidebar_groups')) || sizeOf(Session::get('user_permissions_sidebar_groups')) == 1)
                                    <li class="main-menu-title">
                                        <div data-toggle="collapse" href="#collapse-users" class="title-menu">
                                            <div class="row">
                                                <div class="col-9">
                                                    <div class="@if(strpos(Route::getCurrentRoute()->getName(), 'users') !== false || (strpos(Route::getCurrentRoute()->getName(), 'parameterUserTypes') !== false))
                                                            menu-border-bottom-active
@else
                                                            menu-border-bottom
@endif
                                                            ">
                                                        {{ trans('privateSidebar.users') }}
                                                    </div>
                                                </div>
                                                <div class="col-3">
                                                    <i class="fa
                                                            @if(strpos(Route::getCurrentRoute()->getName(), 'users') !== false || (strpos(Route::getCurrentRoute()->getName(), 'parameterUserTypes') !== false))
                                                            fa-chevron-up
@else
                                                            fa-chevron-down
@endif
                                                            pull-right"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Sub menu collapsed - start #collapse-users -->
                                        <ul id="collapse-users" class="collapse sub-menu-wrapper show">
                                            @endif

                                            @if(ONE::verifyModuleAccess('auth','user'))
                                                @if(!is_null(Session::get('user_permissions_sidebar')) && (in_array('manager', Session::get('user_permissions_sidebar')) || in_array('user', Session::get('user_permissions_sidebar')) || sizeOf(Session::get('user_permissions_sidebar')) == 1))
                                                    <li class="treeview">
                                                        <div class="menu-wrapper"><a id="manager" @if(ONE::checkActiveMenu('user') && $_SERVER['REQUEST_URI'] == "/private/users") class='menu-active' @endif href="{{ action("UsersController@index") }}">{{ trans('privateSidebar.users') }}</a></div>
                                                    </li>
                                                @endif
                                            @endif

                                            @if(!is_null(Session::get('user_permissions_sidebar')) && (in_array('in_person_registration', Session::get('user_permissions_sidebar')) || sizeOf(Session::get('user_permissions_sidebar')) == 1 or in_array('confirm_user', Session::get('user_permissions_sidebar'))))
                                                <li class="treeview">
                                                    <div class="menu-wrapper"><a id="registration" href="#" onclick="goSidebar('registration')" id="registration">{{ trans('privateSidebar.moderation') }}</a></div>
                                                </li>
                                            @endif

                                            @if(!is_null(Session::get('user_permissions_sidebar')) && in_array('user_parameters', Session::get('user_permissions_sidebar')) || sizeOf(Session::get('user_permissions_sidebar')) == 1)
                                                <li class="treeview">
                                                    <div class="menu-wrapper"><a @if(ONE::checkActiveMenu('parameterUserTypes')) class='menu-active'@endif href="{{ action("ParameterUserTypesController@index") }}" id="userTypeParams">{{ trans('privateSidebar.user_type_params') }}</a></div>
                                                </li>
                                            @endif

                                            @if(ONE::verifyModuleAccess('auth','account_recovery'))
                                                @if(!is_null(Session::get('user_permissions_sidebar')) && (in_array('manager', Session::get('user_permissions_sidebar')) || in_array('user', Session::get('user_permissions_sidebar')) || sizeOf(Session::get('user_permissions_sidebar')) == 1))
                                                    <li class="treeview">
                                                        <div class="menu-wrapper"><a id="manager" @if(strpos(Route::getCurrentRoute()->getName(), 'accountRecovery') !== false) class="menu-active" @endif href="{{ action("AccountRecoveryController@index") }}">{{ trans('privateSidebar.account_recovery') }}</a></div>
                                                    </li>
                                            @endif
                                        @endif

                                        @if(!is_null(Session::get('user_permissions_sidebar_groups')) && in_array('users', Session::get('user_permissions_sidebar_groups')) || sizeOf(Session::get('user_permissions_sidebar_groups')) == 1)
                                            <!-- End #collapse-users -->
                                        </ul>
                                    </li>
                                @endif
                            @endif


                            @if(ONE::getEntityKey() != null)
                            <!-- -*-*-*-*-*-*-*-*-*- Research -*-*-*-*-*-*-*-*-*- -->
                                @if(!is_null(Session::get('user_permissions_sidebar_groups')) && in_array('research', Session::get('user_permissions_sidebar_groups')) || sizeOf(Session::get('user_permissions_sidebar_groups')) == 1)
                                    <li class="main-menu-title">
                                        <div data-toggle="collapse" href="#collapse-research" class="title-menu">
                                            <div class="row">
                                                <div class="col-9">
                                                    <div class="
                                                        @if(strpos(Route::getCurrentRoute()->getName(), 'questionnaire') !== false )
                                                            menu-border-bottom-active
@else
                                                            menu-border-bottom
@endif
                                                            ">
                                                        {{ trans('privateSidebar.research') }}
                                                    </div>
                                                </div>
                                                <div class="col-3">
                                                    <i class="fa
                                                        @if(strpos(Route::getCurrentRoute()->getName(), 'questionnaire') !== false )
                                                            fa-chevron-up
@else
                                                            fa-chevron-down
@endif
                                                            pull-right"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Sub menu collapsed - start #collapse-research -->
                                        <ul id="collapse-research" class="collapse sub-menu-wrapper show">
                                            @endif
                                            @if(ONE::verifyModuleAccess('q','q'))
                                                @if(!is_null(Session::get('user_permissions_sidebar')) && in_array('q', Session::get('user_permissions_sidebar')) || sizeOf(Session::get('user_permissions_sidebar')) == 1)
                                                    <li class="treeview">
                                                        <div class="menu-wrapper"><a id="q" @if(ONE::checkActiveMenu('questionnaire')) class='menu-active'@endif href="{{ action("QuestionnairesController@index") }}" id="question">{{ trans('privateSidebar.questionnaire') }}</a></div>
                                                    </li>
                                                @endif
                                            @endif

                                            @if(ONE::verifyModuleAccess('analytics','test_name'))
                                                @if(!is_null(Session::get('user_permissions_sidebar')) && in_array('test_code', Session::get('user_permissions_sidebar')) || sizeOf(Session::get('user_permissions_sidebar')) == 1)
                                                    <li class="treeview">
                                                        <div class="menu-wrapper"><a href="#" id="question">{{ trans('privateSidebar.analytics') }}</a></div>
                                                    </li>
                                                @endif
                                            @endif
                                            @if(ONE::verifyModuleAccess('wui','open_data'))
                                                @if(!is_null(Session::get('user_permissions_sidebar')) && in_array('open_data', Session::get('user_permissions_sidebar')) || sizeOf(Session::get('user_permissions_sidebar')) == 1)
                                                    <li class="treeview">
                                                        <div class="menu-wrapper"><a href="#" id="question">{{ trans('privateSidebar.open_data') }}</a></div>
                                                    </li>
                                                @endif
                                            @endif

                                        @if(!is_null(Session::get('user_permissions_sidebar_groups')) && in_array('research', Session::get('user_permissions_sidebar_groups')) || sizeOf(Session::get('user_permissions_sidebar_groups')) == 1)
                                            <!-- End #collapse-research -->
                                        </ul>
                                    </li>
                                @endif


                                @if(ONE::verifyModuleAccess('wui'))

                                <!-- -*-*-*-*-*-*-*-*-*- Communication -*-*-*-*-*-*-*-*-*- -->
                                    @if(!is_null(Session::get('user_permissions_sidebar_groups')) && in_array('research', Session::get('user_permissions_sidebar_groups')) || sizeOf(Session::get('user_permissions_sidebar_groups')) == 1)
                                        <li class="main-menu-title">
                                            <div data-toggle="collapse" href="#collapse-communication" class="title-menu">
                                                <div class="row">
                                                    <div class="col-9">
                                                        <div class="
                                                                    @if(strpos($_SERVER['REQUEST_URI'],'emails') || strpos(Route::getCurrentRoute()->getName(), 'messageToAll') !== false )
                                                                menu-border-bottom-active
@else
                                                                menu-border-bottom
@endif
                                                                ">
                                                            {{ trans('privateSidebar.communication') }}
                                                        </div>
                                                    </div>
                                                    <div class="col-3">
                                                        <i class="fa
                                                                    @if(strpos($_SERVER['REQUEST_URI'],'emails') || strpos(Route::getCurrentRoute()->getName(), 'messageToAll') !== false )
                                                                fa-chevron-up
@else
                                                                fa-chevron-down
@endif
                                                                pull-right"></i>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Sub menu collapsed - start #collapse-communication -->
                                            <ul id="collapse-communication" class="collapse sub-menu-wrapper show">
                                                @endif

                                                @if(ONE::verifyModuleAccess('wui','email'))
                                                    @if(!is_null(Session::get('user_permissions_sidebar')) && in_array('email', Session::get('user_permissions_sidebar')) || sizeOf(Session::get('user_permissions_sidebar')) == 1)
                                                        <li class="treeview">
                                                            <div class="menu-wrapper"><a @if(ONE::checkActiveMenu('emails')) class='menu-active'@endif href="{{ action("EmailsController@index") }}" id="emails">{{ trans('privateSidebar.email') }}</a></div>
                                                        </li>
                                                    @endif
                                                @endif

                                                @if(ONE::verifyModuleAccess('wui','sms'))
                                                    @if(!is_null(Session::get('user_permissions_sidebar')) && in_array('sms', Session::get('user_permissions_sidebar')) || sizeOf(Session::get('user_permissions_sidebar')) == 1)
                                                        <li class="treeview">
                                                            <div class="menu-wrapper"><a href="{{ action("SmsController@index") }}" id="question">{{ trans('privateSidebar.sms') }}</a></div>
                                                        </li>
                                                    @endif
                                                @endif

                                                @if(ONE::verifyModuleAccess('wui','history'))
                                                    @if(!is_null(Session::get('user_permissions_sidebar')) && in_array('history', Session::get('user_permissions_sidebar')) || sizeOf(Session::get('user_permissions_sidebar')) == 1)
                                                        <li class="treeview">
                                                            <div class="menu-wrapper"><a href="#" id="question">{{ trans('privateSidebar.history') }}</a></div>
                                                        </li>
                                                    @endif
                                                @endif

                                                @if(ONE::verifyModuleAccess('wui','all_messages'))
                                                    @if(!is_null(Session::get('user_permissions_sidebar')) && in_array('all_messages', Session::get('user_permissions_sidebar')) || sizeOf(Session::get('user_permissions_sidebar')) == 1)
                                                        <li class="treeview">
                                                            <div class="menu-wrapper"><a @if(strpos(Route::getCurrentRoute()->getName(), 'entityMessages') !== false) class="menu-active" @endif  href="{{action("EntityMessagesController@index")}}" id="all_messages">
                                                                    {{ trans('privateSidebar.all_messages') }}</a>
                                                            </div>
                                                        </li>
                                                    @endif
                                                @endif

                                                @if(ONE::verifyModuleAccess('notify','message_all_users'))
                                                    @if(!is_null(Session::get('user_permissions_sidebar')) && in_array('message_all_users', Session::get('user_permissions_sidebar')) || sizeOf(Session::get('user_permissions_sidebar')) == 1)
                                                        <li class="treeview">
                                                            <div class="menu-wrapper"><a id="message-all" @if(strpos(Route::getCurrentRoute()->getName(), 'newsletters') !== false) class="menu-active" @endif href="{{ action("PrivateNewslettersController@index") }}">
                                                                    {{ trans('privateSidebar.newsletters') }}</a></div>
                                                        </li>
                                                    @endif
                                                @endif

                                                @if(ONE::verifyModuleAccess('orchestrator','newsletter_subscriptions'))
                                                    @if(!is_null(Session::get('user_permissions_sidebar')) && in_array('newsletter_subscriptions', Session::get('user_permissions_sidebar')) || sizeOf(Session::get('user_permissions_sidebar')) == 1)
                                                        <li class="treeview">
                                                            <div class="menu-wrapper"><a id="newsletter-subscriptions" @if(strpos(Route::getCurrentRoute()->getName(), 'newsletterSubscriptions') !== false) class="menu-active" @endif href="{{ action("NewsletterSubscriptionsController@index") }}">
                                                                    {{ trans('privateSidebar.newsletter_subscriptions') }}</a></div>
                                                        </li>
                                                @endif
                                            @endif

                                            @if(!is_null(Session::get('user_permissions_sidebar_groups')) && in_array('research', Session::get('user_permissions_sidebar_groups')) || sizeOf(Session::get('user_permissions_sidebar_groups')) == 1)
                                                <!-- End #collapse-communication -->
                                            </ul>
                                        </li>
                                    @endif

                                <!-- -*-*-*-*-*-*-*-*-*- Configurations -*-*-*-*-*-*-*-*-*- -->
                                    @if(!is_null(Session::get('user_permissions_sidebar_groups')) && in_array('configurations', Session::get('user_permissions_sidebar_groups')) || sizeOf(Session::get('user_permissions_sidebar_groups')) == 1)
                                        <li class="main-menu-title">
                                            <div data-toggle="collapse" href="#collapse-configurations" class="title-menu">
                                                <div class="row">
                                                    <div class="col-9">
                                                        <div class="menu-border-bottom
                                                @if(strpos(Route::getCurrentRoute()->getName(), 'showEntity') !== false || strpos(Route::getCurrentRoute()->getName(), 'entityGroups') !== false
                                                   || strpos(Route::getCurrentRoute()->getName(), 'roles') !== false || strpos(Route::getCurrentRoute()->getName(), 'homePageTypes') !== false
                                                   || isset($name_view) && str_is("parameters_template", $name_view) )
                                                                menu-border-bottom-active
@else
                                                                menu-border-bottom
@endif
                                                                ">
                                                            {{ trans('privateSidebar.configurations') }}
                                                        </div>
                                                    </div>
                                                    <div class="col-3">
                                                        <i class="fa
                                                @if(strpos(Route::getCurrentRoute()->getName(), 'showEntity') !== false || strpos(Route::getCurrentRoute()->getName(), 'entityGroups') !== false
                                                   || strpos(Route::getCurrentRoute()->getName(), 'roles') !== false || strpos(Route::getCurrentRoute()->getName(), 'homePageTypes') !== false
                                                   || isset($name_view) && str_is("parameters_template", $name_view) )
                                                                fa-chevron-up
@else
                                                                fa-chevron-down
@endif
                                                                pull-right"></i>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Sub menu collapsed - start #collapse-configurations -->
                                            <ul id="collapse-configurations" class="collapse sub-menu-wrapper show">
                                                @endif

                                                @if(!is_null(Session::get('user_permissions_sidebar')) && in_array('entity', Session::get('user_permissions_sidebar')) || sizeOf(Session::get('user_permissions_sidebar')) == 1)
                                                    <li class="treeview">
                                                        <div class="menu-wrapper"><a id="entity" @if(strpos(Route::getCurrentRoute()->getName(), 'showEntity') !== false) class='menu-active'@endif href="{{ action("EntitiesDividedController@showEntity") }}" id="entity">{{ trans('privateSidebar.entity') }}</a></div>
                                                    </li>
                                                @endif

                                                @if($groupTypes = ONE::getGroupTypes())
                                                    @foreach($groupTypes as $item)
                                                        @if(!is_null(Session::get('user_permissions_sidebar')) && in_array('entity_groups', Session::get('user_permissions_sidebar')) || sizeOf(Session::get('user_permissions_sidebar')) == 1)
                                                            <li class="treeview">
                                                                <div class="menu-wrapper"><a id="entityGroupDetails" @if(strpos(Route::getCurrentRoute()->getName(), 'entityGroups') !== false) class='menu-active' @endif href="{{ action("EntityGroupsController@showGroups", ["groupTypeKey" => $item->group_type_key] )}}" id="{!! strtolower($item->code) !!}  departments">{{ trans('privateSidebar.'.$item->code.'') }}</a></div>
                                                            </li>
                                                        @endif
                                                    @endforeach
                                                @endif

                                                @if(ONE::verifyModuleAccess('orchestrator','role'))
                                                    @if(!is_null(Session::get('user_permissions_sidebar')) && in_array('role', Session::get('user_permissions_sidebar')) || sizeOf(Session::get('user_permissions_sidebar')) == 1)
                                                        <li class="treeview">
                                                            <div class="menu-wrapper"><a id="functions" @if(strpos(Route::getCurrentRoute()->getName(), 'roles') !== false) class='menu-active' @endif href="{{ action("RolesController@index") }}">{{trans('privateSidebar.functions')}}</a></div>
                                                        </li>
                                                    @endif
                                                @endif

                                                @if(ONE::verifyModuleAccess('cm','home_page_type'))
                                                    @if(!is_null(Session::get('user_permissions_sidebar')) && in_array('home_page_type', Session::get('user_permissions_sidebar')) || sizeOf(Session::get('user_permissions_sidebar')) == 1)
                                                        <li class="treeview">
                                                            <div class="menu-wrapper"><a @if(strpos(Route::getCurrentRoute()->getName(), 'homePageTypes') !== false) class='menu-active' @endif href="{{ action('HomePageTypesController@index') }}">{{ trans('privateSidebar.home_page_type') }}</a></div>
                                                        </li>
                                                    @endif
                                                @endif

                                                @if(ONE::verifyModuleAccess('wui','short_links'))
                                                    @if(!is_null(Session::get('user_permissions_sidebar')) && in_array('short_links', Session::get('user_permissions_sidebar')) || sizeOf(Session::get('user_permissions_sidebar')) == 1)
                                                        <li class="treeview">
                                                            <div class="menu-wrapper"><a @if(strpos(Route::getCurrentRoute()->getName(), 'shortLinks') !== false) class='menu-active' @endif href="{{ action('ShortLinksController@index') }}">{{ trans('privateSidebar.short_links') }}</a></div>
                                                        </li>
                                                    @endif
                                                @endif

                                                @if(ONE::verifyModuleAccess('cb','parameter_template'))
                                                    @if(!is_null(Session::get('user_permissions_sidebar')) && in_array('parameter_template', Session::get('user_permissions_sidebar')) || sizeOf(Session::get('user_permissions_sidebar')) == 1)
                                                        <li class="treeview">
                                                            <div class="menu-wrapper"><a @if(isset($name_view) && str_is("parameters_template", $name_view)) class='menu-active' @endif href="{{ action("ParametersTemplateController@index") }}">{{ trans('privateSidebar.parameters_template') }}</a></div>
                                                        </li>
                                                @endif
                                            @endif

                                            @if(!is_null(Session::get('user_permissions_sidebar_groups')) && in_array('configurations', Session::get('user_permissions_sidebar_groups')) || sizeOf(Session::get('user_permissions_sidebar_groups')) == 1)
                                                <!-- End #collapse-configurations -->
                                            </ul>
                                        </li>
                                    @endif
                                @endif
                            @endif
                        @endif


                        @if((
                                (ONE::verifyModuleAccess('cm','dynamic_be_menu') && !is_null(Session::get('user_permissions_sidebar')) && in_array('dynamic_be_menu', Session::get('user_permissions_sidebar')) || sizeOf(Session::get('user_permissions_sidebar')) == 1) ||
                                (ONE::verifyModuleAccess('cm','personal_dynamic_be_menu') && !is_null(Session::get('user_permissions_sidebar')) && in_array('personal_dynamic_be_menu', Session::get('user_permissions_sidebar')) || sizeOf(Session::get('user_permissions_sidebar')) == 1)
                            ) && ONE::getUserKey()!="OKtxhee8gnkTyPVlWLVMfZkiHfApnS4G" && ONE::getUserKey()!="HGqbDfHnfDxMFstQcpKZSl0XaG5XaNZ0" && ONE::getUserKey()!="ReRUSLZs9RvZ1CBinzLPOF9xgeyWKnxS"
                        )
                            <li class="main-menu-title margin-top-20">
                                <div data-toggle="collapse" href="#collapse-be_entity_menu_header" class="title-menu collapsed" toggle="false" aria-expanded="false">
                                    <div class="row">
                                        <div class="col-9">
                                            <div class="menu-border-bottom">{{ trans('privateSidebar.be_entity_menu_header') }}</div>
                                        </div>
                                        <div class="col-3">
                                            <i class="fa fa-chevron-down pull-right"></i>
                                        </div>
                                    </div>
                                </div>
                                <ul id="collapse-be_entity_menu_header" class="sub-menu-wrapper show">
                                    @if(ONE::verifyModuleAccess('cm','dynamic_be_menu'))
                                        @if(!is_null(Session::get('user_permissions_sidebar')) && in_array('dynamic_be_menu', Session::get('user_permissions_sidebar')) || sizeOf(Session::get('user_permissions_sidebar')) == 1)
                                            <li class="treeview">
                                                <div class="menu-wrapper">
                                                    <a id="BEMenu" href="{{ action("BEMenuController@index") }}" @if(strpos(Route::getCurrentRoute()->getName(), 'BEMenu') !== false) class="menu-active" @endif>
                                                        {{ trans('privateSidebar.be_entity_menu') }}
                                                    </a>
                                                </div>
                                            </li>
                                        @endif
                                    @endif
                                    @if(ONE::verifyModuleAccess('cm','personal_dynamic_be_menu'))
                                        @if(!is_null(Session::get('user_permissions_sidebar')) && in_array('personal_dynamic_be_menu', Session::get('user_permissions_sidebar')) || sizeOf(Session::get('user_permissions_sidebar')) == 1)
                                            <li class="treeview">
                                                <div class="menu-wrapper">
                                                    <a id="BEUserMenu" href="{{ action("UserBEMenuController@index") }}" @if(strpos(Route::getCurrentRoute()->getName(), 'BEUserMenu') !== false) class="menu-active" @endif>
                                                        {{ trans('privateSidebar.be_personal_menu') }}
                                                    </a>
                                                </div>
                                            </li>
                                        @endif
                                    @endif
                                </ul>
                            </li>
                        @endif
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
