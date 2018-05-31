<div class="col-sm-12 lastNews-headerTitle">
    <h1>{{trans('public.last_news')}}</h1>
</div>

<div class="container-fluid">
    <div class="row">
        <?php $countNews = 1; ?>
        @foreach ($lastNews as $news)
            @if($countNews < 5)
                <div class="col-sm-6 clearfix">
                    <div class="lastNews-wrapper" style="">
                        <div class="lastNews-title">
                            <h4 class="">
                                <a href="{{ action('PublicContentsController@show', $news->id) }}">{{$news->title}}</a>
                            </h4>
                        </div>
                        <div class="">
                            {{--<img class="attachment-img" alt="Attachment Image" src="{{$news['img']}}">--}}
                            {{--<div class="attachment-pushed">--}}
                            <div class="lastNews-summary" style="">
                                {{$news->summary}}
                                <a href="{{ action('PublicContentsController@show', $news->id) }}" class="readmore">{{trans("public.more")}}</a>
                            </div>
                            <div class="lastNews-time">
                                <i class="fa fa-clock-o"></i>
                                {{$news->start_date}}
                            </div>
                            {{--</div>--}}
                        </div>
                    </div>
                </div>
            @endif
            <?php $countNews++; ?>
        @endforeach
    </div>
</div>