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
                <li class="menu-wrapper">
                    <div class="@if($active=='registerPersonVotes') menu-active @endif">
                        <a href="{{ action("PublicCbsController@publicUserVotingRegistration", [$type, $cbKey, $voteKey]) }}">
                            {{ trans('privateSidebar.publicUserVotingRegistration') }}
                        </a>
                    </div>
                </li>
                @if(ONE::verifyUserPermissionsCrud('orchestrator', 'presencial_vote'))
                    <li class="menu-wrapper">
                        <div class="@if($active=='registerInPersonVoting') menu-active @endif">
                            <a href="{{ action("CbsVoteController@registerInPersonVoting", [$type, $cbKey, $voteKey]) }}">
                                {{ trans('privateSidebar.registerInPersonVoting') }}
                            </a>
                        </div>
                    </li>
                @endif
                @if(ONE::verifyUserPermissionsCrud('orchestrator', 'in_person_registration'))
                    <li class="menu-wrapper">
                        <div class="@if($active=='inPersonRegistration') menu-active @endif">
                            <a href="{{ action("UsersController@inPersonRegistration", [$type, $cbKey, $voteKey]) }}">
                                {{ trans('privateSidebar.inPersonRegistration') }}
                            </a>
                        </div>
                    </li>
                @endif
            </ul>
        </li>
    </ul>
</div>