<ul class="sidebar-menu side-menu-main">

        <li @if (Route::getCurrentRoute()->getName() == 'private') class="active" @endif><a href="{{ route("private") }}"><i class="fa fa-home"></i><span> {{ trans('privateSidebar.home') }}</span></a></li>

    <ul class="pager">
        <li>
            <a href="#" id="back" onclick="go('topics')"><i class="fa fa-arrow-left"></i><span> {{ trans('privateSidebar.back') }} </span></a>
        </li>
        <li>
            <a href="#" id="back" onclick="go('topicReviews')"><span> {{ trans('privateSidebar.forward') }} </span><i class="fa fa-arrow-right"></i></a>
        </li>

    </ul>
    <div class="main-menu-title">{{ trans('privateSidebar.topic_review') }}</div>

    <li class="treeview">
        <div class="menu-wrapper"><a @if($active=='details') class="menu-active"  @endif href="{{ action("TopicReviewsController@index", [$type, $cbKey, $topicKey]) }}">{{ trans('privateSidebar.topic_review') }}</a></div>
    </li>

    @if (One::asPermission('manager'))
        <li class="treeview">
            <div class="menu-wrapper"><a @if($active=='replies') class="menu-active"  @endif href="{{ action("TopicReviewRepliesController@index", [$type, $cbKey, $topicKey, $topicReviewKey]) }}">{{ trans('privateSidebar.topic_review_replies') }}</a></div>
        </li>
    @endif
</ul>
