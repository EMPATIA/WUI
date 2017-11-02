<div class="col-sm-12 lastNews-headerTitle">
    <h1 style="">{{trans('public.next_events')}}</h1>
</div>
<div class="container-fluid">
    <div class="row">
        @foreach ($lastEvents as $event)
            <div class="col-sm-6 clearfix">
                <div class="nextEvents-wrapper" style="">
                    <div class="nextEvents-title">
                        <h4 class="">
                            <a href="{{ action('PublicContentsController@show', $event->id) }}">{{ $event->title }}</a>
                        </h4>
                    </div>
                    <div class="">
                        {{--<img class="attachment-img" alt="Attachment Image" src="{{$event['img']}}">--}}
                        {{--<div class="attachment-pushed">--}}
                        <div class="nextEvents-summary" style="">
                            {{$event->summary}}
                            <a href="#" class="readmore">{{trans("public.more")}}</a>
                        </div>
                        <div class="nextEvents-time">
                            <i class="fa fa-clock-o"></i> {{$event->start_date}}
                            </br>
                            {{--<i class="fa fa-map-marker margin-r-5"></i>--}}
                            {{--<a href="{{ action('PublicContentsController@show', $event->id) }}">{{$event['location']}}</a>--}}
                        </div>
                        {{--</div>--}}
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>