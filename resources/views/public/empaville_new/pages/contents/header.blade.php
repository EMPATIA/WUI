<div class="row">
    <!-- Title -->
    <div class="col-xs-12 contents-header-title">
        @if(isset($news))
            <h2 class="contents-list-header-title">{!! $pageContent->title !!}</h2>
        @elseif(isset($events))
            <h2 class="contents-list-header-title">{!! $pageContent->title !!}</h2>
        @else
            <h2 class="contents-list-header-title">{!! $pageContent->title !!}</h2>
        @endif
    </div>
</div>
