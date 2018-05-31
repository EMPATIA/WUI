<script>
    localStorage.setItem('previousSidebar', 'padsType');
    localStorage.setItem('currentSidebar', 'vote');
    localStorage.setItem('sidebarPosition', 2)
    localStorage.removeItem('nextSidebar')
</script>

<div class="side-menu-wrapper">
    <ul class="sidebar-menu sidebar-menu-css">
        <li class="main-menu-title">
            <!-- Menu Title -->
            <div class="menu-border-bottom">
                {{ trans('privateSidebar.vote') }}
            </div>

            <!-- Sub Menu -->
            <ul class="sub-menu-wrapper">
                @if(in_array('pad_votes', Session::get('user_permissions_sidebar')) || sizeOf(Session::get('user_permissions_sidebar')) == 1)
                    <li class="menu-wrapper">
                        <div class="@if (strpos(Route::getCurrentRoute()->getName(), 'vote.show') !== false) menu-active @endif">
                            <a href="{{ action('CbsVoteController@show',  [$type, $cbKey, $voteKey]) }}">
                                {{ trans('privateSidebar.details') }}
                            </a>
                        </div>
                    </li>
                @endif
                <li class="menu-wrapper">
                    <div class="@if (strpos(Route::getCurrentRoute()->getName(), 'public.cb.vote.publicUserVotingRegistration') !== false) menu-active @endif">
                        <a href="{{ action("PublicCbsController@publicUserVotingRegistration", [$type, $cbKey, $voteKey]) }}">
                            {{ trans('privateSidebar.inPersonVote') }}
                        </a>
                    </div>
                </li>
                <li class="menu-wrapper">
                    <div class="@if($active=='registerInPersonVoting') menu-active @endif">
                        <a href="{{ action("CbsVoteController@registerInPersonVoting", [$type, $cbKey, $voteKey]) }}">
                            {{ trans('privateSidebar.ballotVote') }}
                        </a>
                    </div>
                </li>
                <li class="menu-wrapper">
                    <div class="@if($active=='inPersonRegistration') menu-active @endif">
                        <a href="{{ action("UsersController@inPersonRegistration", [$type, $cbKey, $voteKey]) }}">
                            {{ trans('privateSidebar.ballotRegistration') }}
                        </a>
                    </div>
                </li>
                <li class="menu-wrapper">
                    <div class="@if($active=='voteList') menu-active @endif">
                        <a href="{{ action("CbsVoteController@voteList", [$type, $cbKey, $voteKey]) }}">
                            {{ trans('privateSidebar.voteList') }}
                        </a>
                    </div>
                </li>
            </ul>
        </li>
    </ul>
</div>