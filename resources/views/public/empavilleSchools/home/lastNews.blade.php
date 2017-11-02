<!-- News section -->
<section>
    <div class="container news-container">
        <div class="row">
            <div class="col-md-9 col-xs-12">
                <h1 class="homeSectionTitle">{{trans('empavilleSchoolsHome.last_news')}}</h1>
            </div>
            <div class="col-md-3 col-xs-12 news-button-div">
                <a href="{{ action('PublicContentsController@showContentsList', ['type' => 'news']) }}">{{trans("empavilleSchoolsHome.view_all")}}</a>
            </div>
        </div>
        <div class="row">
            @foreach ($lastNews as $news)
                @if($loop->iteration < 4)
                    <div class="col-md-4 col-xs-12 news-box">
                        <div class="row news-inner-div">
                            <div class="col-xs-12 news-inner-img-div" style="background-image:url('{{ isset($newsImage[$news->content_key]) ? action('FilesController@download', ['id'=>$newsImage[$news->content_key]['id'],'code'=>$newsImage[$news->content_key]['code'],1] ) : url('/images/empatia/default_img_contents.jpg')}}')">
                            </div>
                            <div class="col-xs-12 news-content-box">
                                @if(isset($news->start_date))
                                {{$news->start_date}}
                                @endif
                                <h3 class="lastNewsHome-title"><a href="{{ action('PublicContentsController@show', $news->content_key) }}">{{$news->title}}</a></h3>
                            </div>
                            <div class="col-xs-12 view-more-btn">
                                <a href="{{ action('PublicContentsController@show', $news->content_key) }}">{{trans("empavilleSchoolsHome.view_more")}} <span class="glyphicon glyphicon-chevron-right"></span></a>
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach
            @if(count($lastNews)== 0)
                <div class="row">
                    <div class="col-sm-12 text-center">
                        <div class="otherNE-button" style="">{{ trans("empavilleSchoolsHome.there_are_no_news") }}</div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</section>