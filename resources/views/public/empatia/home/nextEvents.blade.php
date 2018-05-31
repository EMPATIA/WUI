<!-- OTHER NEWS -->
<section>
    <div class="container-fluid otherNE-container">
        <div class="row otherNE" style="">
            <div class="paddingBlock-otherN otherN">
                <div class="equalHMWrap eqWrap">

                    <div class="equalHM-otherN eq-otherN eq-otherN-bgnd">
                        <div class="otherN-left otherN-inner">
                            <div class="row sub-menus-row">
                                <div class="col-sm-12 sub-menus-line"><span class="typcn typcn-rss" style="font-size: 4rem; color: #999999"></span> {{trans('home.otherNews')}}</div>
                            </div>
                            @if(isset($lastNews))
                                <div class="otherN-inner-div">

                                    @if(count($lastNews) > 2)
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <div class="row">
                                                    <div class="col-sm-12"><h2>{{$lastNews[2]->start_date}}</h2></div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-sm-12"><h1><a href="{{ action('PublicContentsController@show', $lastNews[2]->content_key) }}">{{$lastNews[2]->title}}</a></h1></div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    @if(count($lastNews) > 3)
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <div class="row">
                                                    <div class="col-sm-12"><h2>{{$lastNews[3]->start_date}}</h2></div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-sm-12"><h1><a href="{{ action('PublicContentsController@show', $lastNews[3]->content_key) }}">{{$lastNews[3]->title}}</a></h1></div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    @if(count($lastNews) > 4)
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <div class="row">
                                                    <div class="col-sm-12"><h2>{{$lastNews[4]->start_date}}</h2></div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-sm-12"><h1><a href="{{ action('PublicContentsController@show', $lastNews[4]->content_key) }}">{{$lastNews[4]->title}}</a></h1></div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                </div>
                                @if(count($lastNews) > 0)
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="otherNE-button" style=""><a href="{{action('PublicContentsController@showNewsList')}}">{{trans('home.viewAll')}}</a></div>
                                    </div>
                                </div>
                                @endif
                            @endif
                        </div>
                    </div>


                    <!-- events -->

                    <div class="equalHM-otherN eq-otherN">
                        <div class="row sub-menus-row">
                            <div class="col-sm-12 sub-menus-line sub-menus-line-events" style=""><span class="typcn typcn-calendar-outline" style="font-size: 4rem; color: #999999"></span>{{ trans("home.events") }}</div>
                        </div>
                        @if(isset($lastEvents))
                            <div class="otherN-inner-div events">
                                @foreach ($lastEvents as $event)
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="row">
                                                <div class="col-sm-12"><h2>{{$event->start_date}}</h2></div>
                                            </div>
                                            <div class="row">
                                                <div class="col-sm-12 otherN-title"><h1><a href="{{ action('PublicContentsController@show', $event->content_key) }}">{{ $event->title }}</a></h1></div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            @if(count($lastEvents)==0)
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="otherNE-button events" style="">{{ trans("home.noEventsAvailable") }}</div>
                                    </div>
                                </div>
                            @else
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="otherNE-button events" style=""><a href="{{action('PublicContentsController@showEventsList')}}">{{ trans("home.viewFullCallendar") }}</a></div>
                                    </div>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>



