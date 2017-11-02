@foreach ($contentsPagination as $event)
    <div class="col-xs-12 cbs-boxes-padding event-box event-box-list">
        <div class="row">
            <div class="col-md-2 col-xs-12 content-date">
                <a href="{!! action('PublicContentsController@show', $event->content_key )!!}">
                <span class="day-month">{{\Carbon\Carbon::parse($event->start_date)->format('d M')}}</span>
                <span class="year">{{\Carbon\Carbon::parse($event->start_date)->format('Y')}}</span>
                </a>
            </div>
            <div class="col-md-10 col-xs-12 ">
                <div class="row content-box-horizontal">
                    <div class="col-xs-12 content-title">
                        <a href="{!! action('PublicContentsController@show', $event->content_key )!!}">
                            <h4>{{$event->title}}</h4>
                        </a>
                    </div>
                    <div class="col-xs-12 event-content">
                        {{ $event->summary }}
                    </div>
                </div>
            </div>
        </div>
        <div class="event-box-line">
        </div>
    </div>
@endforeach
@if(count($contentsPagination)== 0)
    <div class="col-xs-12">
        <div class="alertBoxGreen">
            <div class="col-xs-12 text-center">
                <h2>{{ trans("defaultPages.there_are_no_events") }}</h2>
            </div>
            <div class="col-xs-12 text-center">
                <i class="fa fa-exclamation-triangle" aria-hidden="true"></i>
            </div>
        </div>
    </div>
@endif
@if(!empty($contentsPagination->nextPageUrl()))
    <div class="row">
        <div class="col-xs-12">
            <a class='jscroll-next' href='{{ URL::action('PublicContentsController@showContentsList', ['type' => 'events','page' => $contentsPagination->currentPage()+1])}}'>{{ trans("pages.next") }}</a>
        </div>
    </div>
@endif
