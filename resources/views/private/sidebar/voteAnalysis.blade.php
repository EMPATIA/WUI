<script>
    localStorage.setItem('previousSidebar', 'padsType');
    localStorage.setItem('currentSidebar', 'voteAnalysis');
    localStorage.setItem('sidebarPosition', 2)
    localStorage.removeItem('nextSidebar')
</script>


<div class="side-menu-wrapper">
    <ul class="sidebar-menu sidebar-menu-css">
        <!-- Menu Title -->
        <li class="main-menu-title">
            <div class="menu-border-bottom">
                {{ trans('privateSidebar.vote_analysis') }}
            </div>
            <!-- Sub Menu -->
            <ul class="sub-menu-wrapper">
                <li class="menu-wrapper">
                    <div class="@if($active=='total_votes2') menu-active @endif">
                        <a href="{{ action('CbsController@voteAnalysis', ['type' => $type,'cbKey' => $cbKey,'statistics_type' => 'total_votes2']) }}">
                            {{ trans('privateSidebar.summary') }}
                        </a>
                    </div>
                </li>
                <li class="menu-wrapper">
                    <div class="@if($active=='total_votes_detail2') menu-active @endif">
                        <a href="{{ action('CbsController@voteAnalysis', ['type' => $type,'cbKey' => $cbKey,'statistics_type' => 'total_votes_detail2']) }}">
                            {{ trans('privateSidebar.detail') }}
                        </a>
                    </div>
                </li>
                <li class="menu-wrapper">
                    <div class="@if($active=='votes_summary') menu-active @endif">
                        <a href="{{ action('CbsController@voteAnalysis', ['type' => $type,'cbKey' => $cbKey,'statistics_type' => 'votes_summary']) }}">
                            {{ trans('privateSidebar.vote_analysis_table') }}
                        </a>
                    </div>
                </li>
                <li class="menu-wrapper">
                    <div class="@if($active=='votes_by_date2') menu-active @endif">
                        <a href="{{ action('CbsController@voteAnalysis', ['type' => $type,'cbKey' => $cbKey,'statistics_type' => 'votes_by_date2']) }}">
                            {{ trans('privateSidebar.by_date') }}
                        </a>
                    </div>
                </li>
                <li class="menu-wrapper">
                    <div class="@if($active=='votes_by_user_parameters2') menu-active @endif">
                        <a href="{{ action('CbsController@voteAnalysis', ['type' => $type,'cbKey' => $cbKey,'statistics_type' => 'votes_by_user_parameters2']) }}">
                            {{ trans('privateSidebar.by_user_parameter') }}
                        </a>
                    </div>
                </li>
                <li class="menu-wrapper">
                    <div class="@if($active=='votes_by_topic_parameters2') menu-active @endif">
                        <a href="{{ action('CbsController@voteAnalysis', ['type' => $type,'cbKey' => $cbKey,'statistics_type' => 'votes_by_topic_parameters2']) }}">
                            {{ trans('privateSidebar.by_topic_parameter') }}
                        </a>
                    </div>
                </li>
                <li class="menu-wrapper">
                    <div class="@if($active=='data_vote_analysis_by_channel') menu-active @endif">
                        <a href="{{ action('CbsController@voteAnalysis', ['type' => $type,'cbKey' => $cbKey,'statistics_type' => 'data_vote_analysis_by_channel']) }}">
                            {{ trans('privateSidebar.vote_analysis_advanced') }}
                        </a>
                    </div>
                </li>
            </ul>
        </li>
    </ul>
    {{--
    <ul class="sidebar-menu sidebar-menu-css" style="display:none;">
        <!-- Menu Title -->
        <li class="main-menu-title">
            <div class="menu-border-bottom">
                {{ trans('privateSidebar.vote_analysis') }}
            </div>

            <!-- Sub Menu -->
            <ul class="sub-menu-wrapper">
                <li class="menu-wrapper">
                    <div class="@if($active=='total_votes') menu-active @endif">
                        <a href="{{ action('CbsController@voteAnalysis', ['type' => $type,'cbKey' => $cbKey,'statistics_type' => 'total_votes']) }}">
                            {{ trans('privateSidebar.total_votes') }}
                        </a>
                    </div>
                </li>
                <li class="menu-wrapper">
                    <div class="@if($active=='votes_by_date') menu-active @endif">
                        <a href="{{ action('CbsController@voteAnalysis', ['type' => $type,'cbKey' => $cbKey,'statistics_type' => 'votes_by_date']) }}">
                            {{ trans('privateSidebar.votes_by_date') }}
                        </a>
                    </div>
                </li>
                <li class="menu-wrapper">
                    <div class="@if($active=='top_votes') menu-active @endif">
                        <a href="{{ action('CbsController@voteAnalysis', ['type' => $type,'cbKey' => $cbKey,'statistics_type' => 'top_votes']) }}">
                            {{ trans('privateSidebar.top_votes') }}
                        </a>
                    </div>
                </li>
                <li class="menu-wrapper">
                    <div class="@if($active=='votes_by_parameter') menu-active @endif">
                        <a href="{{ action('CbsController@voteAnalysis', ['type' => $type,'cbKey' => $cbKey,'statistics_type' => 'votes_by_parameter']) }}">
                            {{ trans('privateSidebar.votes_by_parameter') }}
                        </a>
                    </div>
                </li>
                <li class="menu-wrapper">
                    <div class="@if($active=='votes_summary') menu-active @endif">
                        <a href="{{ action('CbsController@voteAnalysis', ['type' => $type,'cbKey' => $cbKey,'statistics_type' => 'votes_summary']) }}">
                            {{ trans('privateSidebar.votes_summary') }}
                        </a>
                    </div>
                </li>
                <li class="menu-wrapper">
                    <div class="@if($active=='voters_per_day') menu-active @endif">
                        <a href="{{ action('CbsController@voteAnalysis', ['type' => $type,'cbKey' => $cbKey,'statistics_type' => 'voters_per_day']) }}">
                            {{ trans('privateSidebar.voters_per_day') }}
                        </a>
                    </div>
                </li>
                <li class="menu-wrapper">
                    <div class="@if($active=='votes_topic_parameters') menu-active @endif">
                        <a href="{{ action('CbsController@voteAnalysis', ['type' => $type,'cbKey' => $cbKey,'statistics_type' => 'votes_topic_parameters']) }}">
                            {{ trans('privateSidebar.votes_topic_parameters') }}
                        </a>
                    </div>
                </li>


                <li class="menu-wrapper">
                    <div class="@if($active=='data_vote_analysis_by_channel') menu-active @endif">
                        <a href="{{ action('CbsController@voteAnalysis', ['type' => $type,'cbKey' => $cbKey,'statistics_type' => 'data_vote_analysis_by_channel']) }}">
                            {{ trans('privateSidebar.data_vote_analysis_by_channel') }}
                        </a>
                    </div>
                </li>

            </ul>
        </li>
    </ul>
--}}
</div>