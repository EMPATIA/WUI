<!-- News section -->
<section>
    <div class="container news-container">
        <div class="row">
            <div class="col-md-9 col-xs-12">
                <h1 class="homeSectionTitle">{{trans('defaultHome.last_news')}}</h1>
            </div>
            <div class="col-md-3 col-xs-12 news-button-div">
                <a href="{{ action('PublicContentsController@showContentsList', ['type' => 'news']) }}">{{trans("defaultHome.view_all")}}</a>
            </div>
        </div>
        <div class="row">
            @if(isset($lastNews))
                @foreach ($lastNews as $news)
                    @if($loop->iteration < 4)
                        <div class="col-md-4 col-xs-12 news-box">
                            <a href="{{ action('PublicContentsController@show', $news->content_key) }}">

                                <div class="row news-inner-div">
                                    <div class="col-xs-12 news-inner-img-div"
                                         style="background-image:url('{{ isset($newsImage[$news->content_key]) ? action('FilesController@download', ['id'=>$newsImage[$news->content_key]['id'],'code'=>$newsImage[$news->content_key]['code'],1] ) : url('/images/empatia/default_img_contents.jpg')}}')">
                                    </div>
                                    <div class="col-xs-12 news-content-box text-muted">
                                        @if(isset($news->start_date))
                                            {{$news->start_date}}
                                        @endif
                                        <h3 class="lastNewsHome-title">{{$news->title}}
                                        </h3>
                                    </div>
                                    <div class="col-xs-12 view-more-btn text-right">
                                        {{trans("defaultHome.view_more")}}
                                        <span class="fa fa-arrow-right"></span>
                                    </div>
                                </div>
                            </a>

                        </div>
                    @endif
                @endforeach
            @endif
            @if(empty($lastNews) || count($lastNews) == 0)
                <div class="row">
                    <div class="col-xs-12">
                        <div class="alertBoxGreenNews">
                            <div class="col-xs-12 text-center">
                                <h2><i class="fa fa-exclamation-circle "
                                       aria-hidden="true"></i> {{ trans("defaultHome.there_are_no_news") }}</h2>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</section>

