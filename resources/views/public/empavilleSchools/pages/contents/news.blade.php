{{--NEWS--}}
@if (isset($contentType['news']) && count(count($contentType['news'])) > 0 && !empty($news))
    <div class="row">
        <div id="column-title" class="col-md-4">
            <i class="fa fa-newspaper-o"></i> {{ trans('PublicContent.news') }}
        <a id="column-more" class="col-md-2" href='{{ URL::action('ContentsController@showNewsList', $content->key) }}'>
        {{ trans('public.read_all') }}</a></div>
    </div>
    <hr style="margin: 10px 0px; color: #cccccc">
    <ul style="list-style: none; padding-left: 0px">
        @foreach($news as $item)
            @if (!empty($item->translations[0]->title))
                <li class="news-item" onclick="location.href='{{ URL::action('ContentsController@showNews', $item->id) }}'">
                <div id="news-date">{{  $item->start_date }}</div>
                <div id="news-topic"><a style="cursor: pointer">{{ $item->translations[0]->title }}</a></div>
                </li>
            @endif
        @endforeach
    </ul>
@endif