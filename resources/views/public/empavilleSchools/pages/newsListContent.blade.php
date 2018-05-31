@if(isset($contentsPagination))
    @foreach ($contentsPagination as $news)
        <div class="col-md-4 col-xs-12 news-box">
            <div class="row news-inner-div">
                <div class="col-xs-12 news-inner-img-div"
                     style="background-image:url('{{ isset($contentsImage[$news->content_key]) ? action('FilesController@download', ['id'=>$contentsImage[$news->content_key]['id'],'code'=>$contentsImage[$news->content_key]['code'],1] ) : url('/images/empatia/default_img_contents.jpg')}}')">
                </div>
                <div class="col-xs-12 news-content-box">
                    @if(isset($news->publish_date))
                        <span class="contents-list-date"> {{$news->publish_date}}</span>
                    @endif
                    <h3 class="ideaTitle news-list-content-title contents-list-summary"><a
                                href="{{ action('PublicContentsController@show', $news->content_key) }}">{{$news->title}}</a>
                    </h3>
                </div>
                <div class="col-xs-12 view-more-btn">
                    <a href="{{ action('PublicContentsController@show', $news->content_key) }}">{{trans("defaultPages.view_more")}}
                        <span class="glyphicon glyphicon-chevron-right"></span></a>
                </div>
            </div>
        </div>
    @endforeach
    @if(count($contentsPagination)== 0)
        <div class="col-sm-12 text-center">
            <div class="otherNE-button" style="">{{ trans("defaultPages.there_are_no_news") }}</div>
        </div>
    @endif
@endif

@if(!empty($contentsPagination->nextPageUrl()))

    <div class="row">
        <div class="col-xs-12">
            <a class='jscroll-next' href='{{ URL::action('PublicContentsController@showContentsList', ['type' => 'news','page' => $contentsPagination->currentPage()+1])}}'>{{ trans("defaultPages.next") }}</a>
        </div>
    </div>

@endif


