
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

    <!-- Menu Title Proposal-->
        <li class="main-menu-title">
            <div class="menu-border-bottom-active">

                <?php
                if(empty($cb->title) && !empty($cbKey)) {
                    try {
                        $cb = \App\ComModules\CB::getCb($cbKey);
                    } catch(\Exception $e) {}
                }

                if(!empty($cb->title))
                    $cbTitle = $cb->title;
                elseif(!empty($type))
                    $cbTitle = trans("privateCbs." . $type);
                else
                    $cbTitle = trans("privateCbs.pad");
                ?>
                {{ $cbTitle }}
            </div>

            <!-- Sub Menu Proposal-->
            <ul class="sub-menu-wrapper">
                @if(ONE::checkCBPermissions($cb->cb_key ?? $cbKey, "participation_details"))
                    <li class="menu-wrapper">
                        <div class="@if($active=='details') menu-active @endif">
                            <a href="{{ action('CbsController@show', [$type, $cb->cb_key ?? $cbKey]) }}">
                                {{ ONE::getCbMenuTranslation('details', $cb->cb_key ?? $cbKey, trans('privateSidebar.details')) }}
                            </a>
                        </div>
                    </li>
                @endif
                @if(ONE::checkCBPermissions($cb->cb_key ?? $cbKey, "participation_list"))
                <li class="menu-wrapper">
                    <div class="@if($active=='topics') menu-active @endif">
                        <a href="{{ action('CbsController@showTopics', [$type, $cb->cb_key ?? $cbKey]) }}">
                            {{ ONE::getCbMenuTranslation('pads_topic', $cb->cb_key ?? $cbKey, trans('privateSidebar.list')) }}
                        </a>
                    </div>
                </li>
                @endif
                @if(ONE::checkCBPermissions($cb->cb_key ?? $cbKey, "participation_comments"))
                <li class="menu-wrapper">
                    <div class="@if($active=='comments') menu-active @endif">
                        <a href="{{ action('CbsController@showCbComments', [$type, $cb->cb_key ?? $cbKey]) }}">
                            {{ ONE::getCbMenuTranslation('comments', $cb->cb_key ?? $cbKey, trans('privateSidebar.comments')) }}
                        </a>
                    </div>
                </li>
                @endif
                @if(ONE::checkCBPermissions($cb->cb_key ?? $cbKey, "participation_analytics"))
                <li class="menu-wrapper">
                    <div class="@if($active=='voteAnalysis') menu-active @endif">
                        <a href="{{ action('CbsController@voteAnalysis', [$type, $cb->cb_key ?? $cbKey, 'statistics_type' => 'total_votes2']) }}">
                            {{ ONE::getCbMenuTranslation('vote_analysis', $cb->cb_key ?? $cbKey, trans('privateSidebar.analytics')) }}
                        </a>
                    </div>
                </li>
                @endif
            </ul>
        </li>

        <!-- Menu Title Config-->
        <li class="main-menu-title">
            <div class="menu-border-bottom-active">
                {{ ONE::getCbMenuTranslation('header_config', $cb->cb_key ?? $cbKey, trans('privateSidebar.configurations')) }}
            </div>

            <!-- Sub Menu Config-->
            <ul class="sub-menu-wrapper">
                @if(ONE::checkCBPermissions($cb->cb_key ?? $cbKey, "conf_process"))
                    <li class="menu-wrapper">
                        <div class="@if($active=='configurations') menu-active @endif">
                            <a href="{{ action('CbsController@showConfigurations', [$type, $cb->cb_key ?? $cbKey]) }}">
                                {{ ONE::getCbMenuTranslation('pads_configurations', $cb->cb_key ?? $cbKey, trans('privateSidebar.process')) }}
                            </a>
                        </div>
                    </li>
                @endif
                @if(ONE::checkCBPermissions($cb->cb_key ?? $cbKey, "conf_parameters"))
                    <li class="menu-wrapper">
                        <div class="@if($active=='parameters') menu-active @endif">
                            <a href="{{ action('CbsController@showParameters', [$type, $cb->cb_key ?? $cbKey]) }}">
                                {{ ONE::getCbMenuTranslation('pads_parameter', $cb->cb_key ?? $cbKey, trans('privateSidebar.parameters')) }}
                            </a>
                        </div>
                    </li>
                @endif
                @if(ONE::checkCBPermissions($cb->cb_key ?? $cbKey, "conf_events"))
                    <li class="menu-wrapper">
                        <div class="@if($active=='votes') menu-active @endif">
                            <a href="{{ action('CbsController@showVotes', [$type, $cb->cb_key ?? $cbKey]) }}">
                                {{ ONE::getCbMenuTranslation('pads_vote', $cb->cb_key ?? $cbKey, trans('privateSidebar.vote_events')) }}
                            </a>
                        </div>
                    </li>
                @endif
                @if(ONE::checkCBPermissions($cb->cb_key ?? $cbKey, "conf_notifications"))
                    <li class="menu-wrapper">
                        <div class="@if($active=='notifications') menu-active @endif">
                            <a href="{{action('CbsController@showNotifications', [$type, $cb->cb_key ?? $cbKey]) }}">
                                {{ ONE::getCbMenuTranslation('notifications', $cb->cb_key ?? $cbKey, trans('privateSidebar.notifications')) }}
                            </a>
                        </div>
                    </li>
                @endif
                @if(ONE::checkCBPermissions($cb->cb_key ?? $cbKey, "conf_flags"))
                    <li class="menu-wrapper">
                        <div class="@if($active=='flags') menu-active @endif">
                            <a href="{{ action('FlagsController@index', [$type, $cb->cb_key ?? $cbKey]) }}">
                                {{ ONE::getCbMenuTranslation('flags', $cb->cb_key ?? $cbKey, trans('privateSidebar.flags')) }}
                            </a>
                        </div>
                    </li>
                @endif
            </ul>
        </li>

        <!-- Menu Title Security-->
        <li class="main-menu-title">
            <div class="menu-border-bottom-active">
                {{ ONE::getCbMenuTranslation('header_security', $cb->cb_key ?? $cbKey, trans('privateSidebar.security')) }}
            </div>

            <!-- Sub Menu Security-->
            <ul class="sub-menu-wrapper">
                @if(ONE::checkCBPermissions($cb->cb_key ?? $cbKey, "security_login_levels"))
                    <li class="menu-wrapper">
                        <div class="@if($active=='security_configurations') menu-active @endif">
                            <a href="{{ action('CbsController@showSecurityConfigurations', [$type, $cb->cb_key ?? $cbKey]) }}">
                                {{ ONE::getCbMenuTranslation('pads_security_configurations', $cb->cb_key ?? $cbKey, trans('privateSidebar.login_levels')) }}
                            </a>
                        </div>
                    </li>
                @endif
                @if(ONE::checkCBPermissions($cb->cb_key ?? $cbKey, "security_permissions"))
                    <li class="menu-wrapper">
                        <div class="@if($active=='permissions') menu-active @endif">
                            <a href="{{ action('CbsController@showGroupPermissions', [$type, $cb->cb_key ?? $cbKey]) }}">
                                {{ ONE::getCbMenuTranslation('cb_group_permissions', $cb->cb_key ?? $cbKey, trans('privateSidebar.permissions')) }}
                            </a>
                        </div>
                    </li>
                @endif
            </ul>
        </li>

        <!-- Menu Title Advanced Options-->
        <li class="main-menu-title">
            <div class="menu-border-bottom-active">
                {{ ONE::getCbMenuTranslation('header_advanced_options', $cb->cb_key ?? $cbKey, trans('privateSidebar.advanced_options')) }}
            </div>

            <!-- Sub Menu Advanced Options-->
            <ul class="sub-menu-wrapper">
                @if(ONE::checkCBPermissions($cb->cb_key ?? $cbKey, "advanced_moderators"))
                    <li class="menu-wrapper">
                        <div class="@if($active=='moderators') menu-active @endif">
                            <a href="{{ action('CbsController@showModerators', [$type, $cb->cb_key ?? $cbKey]) }}">
                                {{ ONE::getCbMenuTranslation('pads_moderators', $cb->cb_key ?? $cbKey, trans('privateSidebar.pads_moderators')) }}
                            </a>
                        </div>
                    </li>
                @endif
                @if(ONE::checkCBPermissions($cb->cb_key ?? $cbKey, "advanced_empaville"))
                    <li class="menu-wrapper">
                        <div class="@if($active=='empavilleAnalysis') menu-active @endif">
                            <a href="{{ action('CbsController@voteAnalysisEmpaville', [$type, $cb->cb_key ?? $cbKey]) }}">
                                {{ ONE::getCbMenuTranslation('empaville_analytics', $cb->cb_key ?? $cbKey, trans('privateSidebar.empaville')) }}
                            </a>
                        </div>
                    </li>
                @endif
                @if(ONE::checkCBPermissions($cb->cb_key ?? $cbKey, "advanced_dataMigration"))
                    <li class="menu-wrapper">
                        <div class="@if($active=='exportTopics') menu-active @endif">
                            <a href="{{ action('CbsController@showExportTopics', [$type, $cb->cb_key ?? $cbKey]) }}">
                                {{ ONE::getCbMenuTranslation('export_topics', $cb->cb_key ?? $cbKey, trans('privateSidebar.dataMigration')) }}
                            </a>
                        </div>
                    </li>
                @endif
                @if(ONE::checkCBPermissions($cb->cb_key ?? $cbKey, "advanced_quest"))
                    <li class="menu-wrapper">
                        <div class="@if($active=='questionnaires') menu-active @endif">
                            <a href="{{ action('CbsController@showQuestionnaires', [$type, $cb->cb_key ?? $cbKey]) }}">
                                {{ ONE::getCbMenuTranslation('cbsQuestionnaires', $cb->cb_key ?? $cbKey, trans('privateSidebar.cbsQuestionnaires')) }}
                            </a>
                        </div>
                    </li>
                @endif
                @if(ONE::checkCBPermissions($cb->cb_key ?? $cbKey, "advanced_TA"))
                    <li class="menu-wrapper">
                        <div class="@if($active=='technicalAnalysisProcess') menu-active @endif">
                            <a href="{{ action('TechnicalAnalysisProcessesController@showQuestions', [$type, $cb->cb_key ?? $cbKey]) }}">
                                {{ ONE::getCbMenuTranslation('technical_analysis_process', $cb->cb_key ?? $cbKey, trans('privateSidebar.technical_analysis_process')) }}
                            </a>
                        </div>
                    </li>
                @endif
                @if(ONE::checkCBPermissions($cb->cb_key ?? $cbKey, "advanced_translations"))
                    <li class="menu-wrapper">
                        <div class="@if($active=='translations') menu-active @endif">
                            <a href="{{ action('TranslationsController@index', ['type' => 'proposal', 'cbKey' => $cb->cb_key ?? $cbKey]) }}">
                                {{ ONE::getCbMenuTranslation('translations', $cb->cb_key ?? $cbKey, trans('privateSidebar.translations')) }}
                            </a>
                        </div>
                    </li>
                @endif
                @if(ONE::checkCBPermissions($cb->cb_key ?? $cbKey, "advanced_schedules"))
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