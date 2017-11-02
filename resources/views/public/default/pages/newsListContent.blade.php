@if(isset($contentsPagination))
    @foreach ($contentsPagination as $news)
        <div class="col-md-4 col-xs-12 news-box">
            <a href="{{ action('PublicContentsController@show', $news->content_key) }}">
            <div class="row news-inner-div">
                <div class="col-xs-12 news-inner-img-div"
                     style="background-image:url('{{ isset($contentsImage[$news->content_key]) ? action('FilesController@download', ['id'=>$contentsImage[$news->content_key]['id'],'code'=>$contentsImage[$news->content_key]['code'],1] ) : url('/images/empatia/default_img_contents.jpg')}}')">
                </div>
                <div class="col-xs-12 news-content-box text-muted">
                    @if(isset($news->publish_date))
                        <span class="contents-list-date"> {{\Carbon\Carbon::parse($news->publish_date)->format('d-m-Y')}}</span>
                    @endif
                    <h3 class="lastNewsHome-title">{{$news->title}}
                    </h3>
                </div>
                <div class="col-xs-12 view-more-btn news-list-box text-right">
                    {{trans("defaultPages.view_more")}}
                        <span class="fa fa-arrow-right"></span>
                </div>
            </div>
            </a>
        </div>
    @endforeach
    @if(count($contentsPagination)== 0)
        <div class="col-xs-12">
            <div class="alertBoxGreen">
                <div class="col-xs-12 text-center">
                    <h2><i class="fa fa-exclamation-triangle" aria-hidden="true"></i>  {{ trans("defaultPages.there_are_no_news") }}</h2>
                </div>
            </div>
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


