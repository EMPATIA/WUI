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
                @if(!in_array(ONE::getUserKey(),["OKtxhee8gnkTyPVlWLVMfZkiHfApnS4G","HGqbDfHnfDxMFstQcpKZSl0XaG5XaNZ0","ReRUSLZs9RvZ1CBinzLPOF9xgeyWKnxS","welfX1NcdZyaAaOpkjziQQTKzPLKnzSv","KnDGHYbWwkmm40a6ki9CkpruWvDSxffP","AmawFZ1jiR92iBpoq7ycVy9eRzXIgERG","cvwZ6RbE8mIYHTEznMsdvIE1oVJydyTC","M3Z4doELUqgN0O2miDt8AkI19F7ULMKS","57UbDJ7lgzdRc0vnbqHqXOmRdLqq9Oll","xE2InyIXxgRvYZlOFYFqnWhcM49L56R0","Gv4OrMROG1MPLVuyuZM64StzzWosLEV7"]))
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
                    @if(ONE::checkCBPermissions($cb->cb_key ?? $cbKey, "topic_review"))
                        <li class="menu-wrapper">
                            <div class="@if($active=='topicReviews') menu-active @endif">
                                <a href="{{ action("TopicReviewsController@index", [$type, $cbKey, $topicKey]) }}">
                                    {{ ONE::getCbMenuTranslation('topic_reviews', $cbKey??"default", trans('privateSidebar.topic_reviews')) }}
                                </a>
                            </div>
                        </li>
                    @endif
                @endif

                <!-- --//-- -->
                @if(ONE::checkCBPermissions($cb->cb_key ?? $cbKey, "topic_ta"))
                    <li class="menu-wrapper">
                        <div class="@if($active=='technicalAnalysis') menu-active @endif">
                            <a href="{{ action("TechnicalAnalysisController@verifyIfExistsTechnicalAnalysis", [$type, $cbKey, $topicKey]) }}">
                                {{ ONE::getCbMenuTranslation('topic_technical_analysis', $cbKey??"default", trans('privateSidebar.technical_analysis')) }}
                            </a>
                        </div>
                    </li>
                @endif
                {{--@if(in_array('cooperators', Session::get('user_permissions_sidebar')) || sizeOf(Session::get('user_permissions_sidebar')) == 1)--}}
                    {{--<li class="menu-wrapper">--}}
                        {{--<div class="@if($active=='cooperators') menu-active @endif">--}}
                            {{--<a href="{{action('TopicController@showCooperators', [$type, $cbKey, $topicKey]) }}">--}}
                                {{--{{ ONE::getCbMenuTranslation('topic_cooperators', $cbKey??"default", trans('privateSidebar.cooperators')) }}--}}
                            {{--</a>--}}
                        {{--</div>--}}
                    {{--</li>--}}
                {{--@endif--}}
            </ul>
        </li>
    </ul>
</div>