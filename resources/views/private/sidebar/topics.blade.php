<script>
    localStorage.setItem('previousSidebar', 'padsType');
    localStorage.setItem('currentSidebar', 'topics');
    localStorage.setItem('sidebarPosition', 2)
    localStorage.removeItem('nextSidebar')
</script>

<div class="side-menu-wrapper">
    <ul class="sidebar-menu sidebar-menu-css">
        <!-- Menu Title -->
        <li class="main-menu-title">
            <div class="menu-border-bottom">
                {{ ONE::getCbMenuTranslation('topic_header', $cbKey??"default", trans('privateSidebar.topic')) }}
            </div>
            <!-- Sub Menu -->
            <ul class="sub-menu-wrapper">
                @if(ONE::getUserKey()!="OKtxhee8gnkTyPVlWLVMfZkiHfApnS4G" && ONE::getUserKey()!="HGqbDfHnfDxMFstQcpKZSl0XaG5XaNZ0" && ONE::getUserKey()!="ReRUSLZs9RvZ1CBinzLPOF9xgeyWKnxS")
                    <li class="menu-wrapper">
                        <div class="@if($active=='details') menu-active @endif">
                            <a href="{{ action("TopicController@show", [$type, $cbKey, $topicKey]) }}">
                                {{ ONE::getCbMenuTranslation('topic_details', $cbKey??"default", trans('privateSidebar.details')) }}
                            </a>
                        </div>
                    </li>
                    <li class="menu-wrapper">
                        <div class="@if($active=='posts') menu-active @endif">
                            <a href="{{ action("TopicController@showPosts", [$type, $cbKey, $topicKey]) }}">
                                {{ ONE::getCbMenuTranslation('topic_posts', $cbKey??"default", trans('privateSidebar.posts')) }}
                            </a>
                        </div>
                    </li>
                    <li class="menu-wrapper">
                        <div class="@if($active=='topicReviews') menu-active @endif">
                            <a href="{{ action("TopicReviewsController@index", [$type, $cbKey, $topicKey]) }}">
                                {{ ONE::getCbMenuTranslation('topic_reviews', $cbKey??"default", trans('privateSidebar.topic_reviews')) }}
                            </a>
                        </div>
                    </li>
                @endif

                <!-- --//-- -->
                <li class="menu-wrapper">
                    <div class="@if($active=='technicalAnalysis') menu-active @endif">
                        <a href="{{ action("TechnicalAnalysisController@verifyIfExistsTechnicalAnalysis", [$type, $cbKey, $topicKey]) }}">
                            {{ ONE::getCbMenuTranslation('topic_technical_analysis', $cbKey??"default", trans('privateSidebar.technical_analysis')) }}
                        </a>
                    </div>
                </li>
                @if(in_array('cooperators', Session::get('user_permissions_sidebar')) || sizeOf(Session::get('user_permissions_sidebar')) == 1)
                    <li class="menu-wrapper">
                        <div class="@if($active=='cooperators') menu-active @endif">
                            <a href="{{action('TopicController@showCooperators', [$type, $cbKey, $topicKey]) }}">
                                {{ ONE::getCbMenuTranslation('topic_cooperators', $cbKey??"default", trans('privateSidebar.cooperators')) }}
                            </a>
                        </div>
                    </li>
                @endif
            </ul>
        </li>
    </ul>
</div>