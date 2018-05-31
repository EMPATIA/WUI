@foreach ($contentsPagination as $event)
    <div class="col-xs-12 cbs-boxes-padding event-box" id="eventHorizontal">
        @if(!empty($contentsImage) && count($contentsImage) > 0)
            <div class="col-md-4 col-xs-12 news-inner-img-div" style="background-image:url('{{ isset($contentsImage[$event->content_key]) ? action('FilesController@download', ['id'=>$contentsImage[$event->content_key]['id'],'code'=>$contentsImage[$event->content_key]['code'],1] ) : url('/images/empatia/default_img_contents.jpg')}}')">
            </div>
        @else
            <div class="col-md-4 col-xs-12 news-inner-img-div" style="background-image:url('{{ url('/images/empatia/default_img_contents.jpg')}}')">
            </div>
        @endif
        <div class="col-md-8 col-xs-12 ">
            <div class="row content-box-horizontal">
                <div class="col-xs-12">{{$event->start_date}}</div>
                <div class="col-xs-12 event-title contents-list-summary">
                    <h4>
                        <a href="{!! action('PublicContentsController@show', $event->content_key )!!}">{{$event->title}}</a>
                    </h4>
                </div>
                <div class="col-xs-12 event-content  ">
                    {{ $event->summary }}
                </div>
                <div class="col-xs-12 view-more-btn-event">
                    <a href="{{ action('PublicContentsController@show', $event->content_key) }}">{{trans("defaultPages.view_more")}} <span class="glyphicon glyphicon-chevron-right"></span></a>
                </div>

            </div>
        </div>
    </div>
@endforeach
@if(count($contentsPagination)== 0)
    <div class="col-sm-12 text-center">
        <div class="otherNE-button" style="">{{ trans("defaultPages.there_are_no_events") }}</div>
    </div>
@endif
@if(!empty($contentsPagination->nextPageUrl()))
    <div class="row">
        <div class="col-xs-12">
            <a class='jscroll-next' href='{{ URL::action('PublicContentsController@showContentsList', ['type' => 'events','page' => $contentsPagination->currentPage()+1])}}'>{{ trans("defaultPages.next") }}</a>
        </div>
    </div>
@endif
