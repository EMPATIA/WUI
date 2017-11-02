
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
            <div class="menu-border-bottom-active">
                {{ ONE::getCbMenuTranslation('header', $cb->cb_key ?? $cbKey, trans('privateSidebar.pads_'.$type)) }}
            </div>

            <!-- Sub Menu -->
            <ul class="sub-menu-wrapper">
                @if (ONE::getUserKey()!="OKtxhee8gnkTyPVlWLVMfZkiHfApnS4G" && ONE::getUserKey()!="HGqbDfHnfDxMFstQcpKZSl0XaG5XaNZ0" && ONE::getUserKey()!="ReRUSLZs9RvZ1CBinzLPOF9xgeyWKnxS")
                    <li class="menu-wrapper">
                        <div class="@if($active=='details') menu-active @endif">
                            <a href="{{ action('CbsController@show', [$type, $cb->cb_key ?? $cbKey]) }}">
                                {{ ONE::getCbMenuTranslation('details', $cb->cb_key ?? $cbKey, trans('privateSidebar.details')) }}
                            </a>
                        </div>
                    </li>
                @endif
                @if(in_array('topics', Session::get('user_permissions_sidebar')) || sizeOf(Session::get('user_permissions_sidebar')) == 1)
                    <li class="menu-wrapper">
                        <div class="@if($active=='topics') menu-active @endif">
                            <a href="{{ action('CbsController@showTopics', [$type, $cb->cb_key ?? $cbKey]) }}">
                                {{ ONE::getCbMenuTranslation('pads_topic', $cb->cb_key ?? $cbKey, trans('privateSidebar.pads_topic')) }}
                            </a>
                        </div>
                    </li>
                @endif
                @if(in_array('pad_parameters', Session::get('user_permissions_sidebar')) || sizeOf(Session::get('user_permissions_sidebar')) == 1)
                    <li class="menu-wrapper">
                        <div class="@if($active=='parameters') menu-active @endif">
                            <a href="{{ action('CbsController@showParameters', [$type, $cb->cb_key ?? $cbKey]) }}">
                                {{ ONE::getCbMenuTranslation('pads_parameter', $cb->cb_key ?? $cbKey, trans('privateSidebar.pads_parameter')) }}
                            </a>
                        </div>
                    </li>
                @endif
                @if(in_array('pad_votes', Session::get('user_permissions_sidebar')) || sizeOf(Session::get('user_permissions_sidebar')) == 1)
                    <li class="menu-wrapper">
                        <div class="@if($active=='votes') menu-active @endif">
                            <a href="{{ action('CbsController@showVotes', [$type, $cb->cb_key ?? $cbKey]) }}">
                                {{ ONE::getCbMenuTranslation('pads_vote', $cb->cb_key ?? $cbKey, trans('privateSidebar.pads_vote')) }}
                            </a>
                        </div>
                    </li>
                @endif
                @if(in_array('moderators', Session::get('user_permissions_sidebar')) || sizeOf(Session::get('user_permissions_sidebar')) == 1)
                    <li class="menu-wrapper">
                        <div class="@if($active=='moderators') menu-active @endif">
                            <a href="{{ action('CbsController@showModerators', [$type, $cb->cb_key ?? $cbKey]) }}">
                                {{ ONE::getCbMenuTranslation('pads_moderators', $cb->cb_key ?? $cbKey, trans('privateSidebar.pads_moderators')) }}
                            </a>
                        </div>
                    </li>
                @endif
                @if(in_array('configurations', Session::get('user_permissions_sidebar')) || sizeOf(Session::get('user_permissions_sidebar')) == 1)
                    <li class="menu-wrapper">
                        <div class="@if($active=='configurations') menu-active @endif">
                            <a href="{{ action('CbsController@showConfigurations', [$type, $cb->cb_key ?? $cbKey]) }}">
                                {{ ONE::getCbMenuTranslation('pads_configurations', $cb->cb_key ?? $cbKey, trans('privateSidebar.pads_configurations')) }}
                            </a>
                        </div>
                    </li>
                @endif
                @if(in_array('vote_analysis', Session::get('user_permissions_sidebar')) || sizeOf(Session::get('user_permissions_sidebar')) == 1)
                    <li class="menu-wrapper">
                        <div class="@if($active=='voteAnalysis') menu-active @endif">
                            <a href="{{ action('CbsController@voteAnalysis', [$type, $cb->cb_key ?? $cbKey]) }}">
                                {{ ONE::getCbMenuTranslation('vote_analysis', $cb->cb_key ?? $cbKey, trans('privateSidebar.vote_analysis')) }}
                            </a>
                        </div>
                    </li>
                @endif
                @if(in_array('pad_notifications', Session::get('user_permissions_sidebar')) || sizeOf(Session::get('user_permissions_sidebar')) == 1)
                    <li class="menu-wrapper">
                        <div class="@if($active=='notifications') menu-active @endif">
                            <a href="{{action('CbsController@showNotifications', [$type, $cb->cb_key ?? $cbKey]) }}">
                                {{ ONE::getCbMenuTranslation('notifications', $cb->cb_key ?? $cbKey, trans('privateSidebar.notifications')) }}
                            </a>
                        </div>
                    </li>
                @endif
                @if(in_array('empaville_analysis', Session::get('user_permissions_sidebar')) || sizeOf(Session::get('user_permissions_sidebar')) == 1)
                    <li class="menu-wrapper">
                        <div class="@if($active=='empavilleAnalysis') menu-active @endif">
                            <a href="{{ action('CbsController@voteAnalysisEmpaville', [$type, $cb->cb_key ?? $cbKey]) }}">
                                {{ ONE::getCbMenuTranslation('empaville_analytics', $cb->cb_key ?? $cbKey, trans('privateSidebar.empaville_analytics')) }}
                            </a>
                        </div>
                    </li>
                @endif
                @if(in_array('export_topics', Session::get('user_permissions_sidebar')) || sizeOf(Session::get('user_permissions_sidebar')) == 1)
                    <li class="menu-wrapper">
                        <div class="@if($active=='exportTopics') menu-active @endif">
                            <a href="{{ action('CbsController@showExportTopics', [$type, $cb->cb_key ?? $cbKey]) }}">
                                {{ ONE::getCbMenuTranslation('export_topics', $cb->cb_key ?? $cbKey, trans('privateSidebar.export_topics')) }}
                            </a>
                        </div>
                    </li>
                @endif
                @if(in_array('security_configurations', Session::get('user_permissions_sidebar')) || sizeOf(Session::get('user_permissions_sidebar')) == 1)
                    <li class="menu-wrapper">
                        <div class="@if($active=='security_configurations') menu-active @endif">
                            <a href="{{ action('CbsController@showSecurityConfigurations', [$type, $cb->cb_key ?? $cbKey]) }}">
                                {{ ONE::getCbMenuTranslation('pads_security_configurations', $cb->cb_key ?? $cbKey, trans('privateSidebar.pads_security_configurations')) }}
                            </a>
                        </div>
                    </li>
                @endif

                @if(in_array('topic_permissions', Session::get('user_permissions_sidebar')) || sizeOf(Session::get('user_permissions_sidebar')) == 1)
                    <li class="menu-wrapper">
                        <div class="@if($active=='permissions') menu-active @endif">
                            <a href="{{ action('CbsController@showGroupPermissions', [$type, $cb->cb_key ?? $cbKey]) }}">
                                {{ ONE::getCbMenuTranslation('cb_group_permissions', $cb->cb_key ?? $cbKey, trans('privateSidebar.cb_group_permissions')) }}
                            </a>
                        </div>
                    </li>
                @endif
                @if(in_array('comments', Session::get('user_permissions_sidebar')) || sizeOf(Session::get('user_permissions_sidebar')) == 1)
                    <li class="menu-wrapper">
                        <div class="@if($active=='comments') menu-active @endif">
                            <a href="{{ action('CbsController@showCbComments', [$type, $cb->cb_key ?? $cbKey]) }}">
                                {{ ONE::getCbMenuTranslation('comments', $cb->cb_key ?? $cbKey, trans('privateSidebar.comments')) }}
                            </a>
                        </div>
                    </li>
                @endif
                @if(in_array('flags', Session::get('user_permissions_sidebar')) || sizeOf(Session::get('user_permissions_sidebar')) == 1)
                    <li class="menu-wrapper">
                        <div class="@if($active=='flags') menu-active @endif">
                            <a href="{{ action('FlagsController@index', [$type, $cb->cb_key ?? $cbKey]) }}">
                                {{ ONE::getCbMenuTranslation('flags', $cb->cb_key ?? $cbKey, trans('privateSidebar.flags')) }}
                            </a>
                        </div>
                    </li>
                @endif
                @if(in_array('configurationQuestionnaires', Session::get('user_permissions_sidebar')) || sizeOf(Session::get('user_permissions_sidebar')) == 1)
                    <li class="menu-wrapper">
                        <div class="@if($active=='questionnaires') menu-active @endif">
                            <a href="{{ action('CbsController@showQuestionnaires', [$type, $cb->cb_key ?? $cbKey]) }}">
                                {{ ONE::getCbMenuTranslation('cbsQuestionnaires', $cb->cb_key ?? $cbKey, trans('privateSidebar.cbsQuestionnaires')) }}
                            </a>
                        </div>
                    </li>
                @endif
                @if(in_array('technical_analysis_process', Session::get('user_permissions_sidebar')) || sizeOf(Session::get('user_permissions_sidebar')) == 1)
                    <li class="menu-wrapper">
                        <div class="@if($active=='technicalAnalysisProcess') menu-active @endif">
                            <a href="{{ action('TechnicalAnalysisProcessesController@showQuestions', [$type, $cb->cb_key ?? $cbKey]) }}">
                                {{ ONE::getCbMenuTranslation('technical_analysis_process', $cb->cb_key ?? $cbKey, trans('privateSidebar.technical_analysis_process')) }}
                            </a>
                        </div>
                    </li>
                @endif
                @if(in_array('cb_translations', Session::get('user_permissions_sidebar')) || sizeOf(Session::get('user_permissions_sidebar')) == 1)
                    <li class="menu-wrapper">
                        <div class="@if($active=='cbtranslation') menu-active @endif">
                            <a href="{{ action('CbTranslationController@showCbTranslation', [$type, $cb->cb_key ?? $cbKey]) }}">
                                {{ ONE::getCbMenuTranslation('CbTranslation', $cb->cb_key ?? $cbKey, trans('privateSidebar.CbTranslation')) }}
                            </a>
                        </div>
                    </li>
                @endif
                @if(in_array('cb_menu_translations', Session::get('user_permissions_sidebar')) || sizeOf(Session::get('user_permissions_sidebar')) == 1)
                    <li class="menu-wrapper">
                        <div class="@if($active=='getCbMenuTranslation') menu-active @endif">
                            <a href="{{ action('CbMenuTranslationController@index', [$type, $cb->cb_key ?? $cbKey]) }}">
                                {{ ONE::getCbMenuTranslation('cbMenuTranslation', $cb->cb_key ?? $cbKey, trans('privateSidebar.cbMenuTranslation')) }}
                            </a>
                        </div>
                    </li>
                @endif
                @if(in_array('operation_schedules', Session::get('user_permissions_sidebar')) || sizeOf(Session::get('user_permissions_sidebar')) == 1)
                    <li class="menu-wrapper">
                        <div class="@if($active=='operation_schedules') menu-active @endif">
                            <a href="{{ action('OperationSchedulesController@index', [$type, $cb->cb_key ?? $cbKey]) }}">
                                {{ ONE::getCbMenuTranslation('operation_schedules', $cb->cb_key ?? $cbKey, trans('privateSidebar.operation_schedules')) }}
                            </a>
                        </div>
                    </li>
                @endif
            </ul>
        </li>
    </ul>
</div>