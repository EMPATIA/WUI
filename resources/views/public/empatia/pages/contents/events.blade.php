{{--EVENTS--}}
@if (isset($contentType['events']) && count(count($contentType['events'])) > 0 && !empty($events))
    <div class="row">
        <div id="column-title" class="col-md-4">
            <i class="fa fa-calendar"></i> {{ trans('PublicContent.events') }}
        <a id="column-more" class="col-md-2" href="{{ URL::action('ContentsController@showEventsList', $content->key) }}">
            {{ trans('public.see_all') }}</a></div>
    </div>
    <hr style="margin: 10px 0px; color: #cccccc">
    <ul style="list-style: none; padding-left: 0px">
        @foreach($events as $item)
            @if (!empty($item->translations[0]->title))
                <li class="event-item" onclick="location.href='{{ URL::action('ContentsController@showEvent', $item->id) }}'">
                    {{--<div id="event-square">--}}
                        {{--<div id="event-month">{{ strtoupper(substr($item->start_date->formatLocalized('%B'), 0, 3)) }}</div>--}}
                        {{--<div id="event-day">{{ $item->start_date->day }}</div>--}}
                    {{--</div>--}}
                    <div id="event-date">{{  $item->start_date }}</div>
                    <div style="margin-bottom: 20px;">
                        {{--<div id="event-location">{{ $item->translations[0]->location }}</div>--}}
                        <div id="event-title"><a style="cursor: pointer">{{ $item->translations[0]->title }}</a></div>
                    </div>
                    <div style="clear: both"></div>
                </li>
            @endif
        @endforeach
    </ul>
@endif