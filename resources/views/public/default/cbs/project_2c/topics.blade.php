@extends('public.default._layouts.index')
@section('header_scripts')
    <!-- Maps -->
    <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCjgiI5l8FanufeE3GRchTZSVOaAyzVIE8" type="text/javascript"></script>
@endsection
@section('content')
    {{--Topics list section--}}
    <section>
        <div class="container pads-container">
            <div class="row">
                <div class="col-xs-12">
                    <div class="pads-title">
                        <h3>{!! $cb->title ?? trans('defaultCbsProject2C.project_2c') !!}</h3>
                    </div>
                </div>
                <div class="col-xs-12">
                    <div class="pads-description">
                        {!! $cb->contents ?? trans('defaultCbsProject2C.project_2c_description') !!}
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-9 col-xs-12">
                    @foreach($parameters as $parameter)
                        @if($parameter['filter'])
                            <div class="row">
                                <div class="col-xs-12">
                                    <div class="filter-label">
                                        {!! trans('defaultCbsProject2C.filter_by') !!}
                                    </div>
                                </div>
                            </div>
                        @endif
                        @break
                    @endforeach
                    <div class="row">
                        <div class="col-xs-12">
                            @foreach($parameters as $parameter)
                                @if($parameter['filter'])
                                    <div class="pull-left" style="margin-top: 10px">
                                        <div class="dropdown">
                                            <button class="btn-filter dropdown-toggle" type="button" data-toggle="dropdown">
                                                @if(isset($filterOptionSelected) and $filterOptionSelected['parameter_id'] == $parameter["id"])
                                                    {{ $filterOptionSelected['label'] }}
                                                @else
                                                    {{ $parameter['name'] }}
                                                @endif
                                                <i class="fa fa-angle-down angle-down-filter" aria-hidden="true"></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                @foreach($parameter['options'] as $option)
                                                    @if($option['id'] == $filterOptionSelected['option_id'])
                                                        <li class="disabled" style="float:none"><a href="#">{{$option['name']}}</a></li>
                                                    @else
                                                        <li style="float:none"><a href={{action('PublicCbsController@show',['cbKey' => $cbKey, 'type' => $type, 'filter_'.$parameter["id"] => $option["id"], 'parameter_id' => $parameter["id"], 'option_id' => $option["id"]]) }}>{{$option['name']}}</a></li>
                                                    @endif
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                            <div class="pull-left" style="margin-top: 10px; margin-top: 10px; padding: 5px 10px 5px 10px; color: #333333">
                                <span><a href="{{action('PublicCbsController@show',['cbKey' => $cbKey, 'type' => $type]) }}" style="color: #333333"><i class="fa fa-times" aria-hidden="true" style="font-size: 18px"></i></a></span>
                                <span>{{ trans('defaultCbsProject2C.reset_filters') }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="row">
                                <div class="col-xs-12">
                                    <div class="order-label">
                                        {!! trans('defaultCbsProject2C.order_by') !!}
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-12">
                                    <div class="pull-left">
                                        <div class="dropdown">
                                            <button class="btn-filter dropdown-toggle" type="button" data-toggle="dropdown">{{trans('defaultCbsProject2C.order_by')}}
                                                <i class="fa fa-angle-down angle-down-filter" aria-hidden="true"></i></button>
                                            <ul class="dropdown-menu" style="float: none">
                                                <li style="float: none"><a href={{action('PublicCbsController@show',['cbKey' => $cbKey, 'type' => $type, 'orderBy' => 'desc'])}}>{{trans('defaultCbsProject2C.date')}} <i class="fa fa-arrow-up" aria-hidden="true"></i></a></li>
                                                <li style="float: none"><a href={{action('PublicCbsController@show',['cbKey' => $cbKey, 'type' => $type, 'orderBy' => 'asc'])}}>{{trans('defaultCbsProject2C.date')}} <i class="fa fa-arrow-down" aria-hidden="true"></i></a></li>
                                                <li style="float: none"><a href={{action('PublicCbsController@show',['cbKey' => $cbKey, 'type' => $type, 'popularity' => 'desc'])}}>{{trans('defaultCbsProject2C.popularity')}} <i class="fa fa-arrow-up" aria-hidden="true"></i></a></li>
                                                <li style="float: none"><a href={{action('PublicCbsController@show',['cbKey' => $cbKey, 'type' => $type, 'popularity' => 'asc'])}}>{{trans('defaultCbsProject2C.popularity')}} <i class="fa fa-arrow-down" aria-hidden="true"></i></a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-xs-12">
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="layout-icons">
                                <div class="hidden-xs">
                                    <a href="{!! action('PublicCbsController@show', ['cbKey' => $cbKey, 'type' => $type] ) !!}" >
                                        <i class="fa fa-th fa-2x" aria-hidden="true"></i>
                                    </a>
                                    <a href="{!! action('PublicCbsController@show', ['cbKey' => $cbKey, 'type' => $type, 'listType' => 'listProject2Cs'] ) !!}" >
                                        <i class="fa fa-list fa-2x" aria-hidden="true"></i>
                                    </a>
                                </div>
                                @if(isset($parameters['google_maps']))
                                    <a href="{!! action('PublicCbsController@generalMap', ['cbKey' => $cbKey, 'type' => $type] ) !!}" class="@if(isset($listType) && $listType == 'map') visualizationButtonActive @endif">
                                        <i class="fa fa-map-marker fa-2x location" aria-hidden="true"></i>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        {!! Form::open(['method'=>'GET', 'url'=>action('PublicCbsController@show',['cbKey' => $cbKey, 'type' => $type]),'role'=>'search'])  !!}
                        <div class="col-xs-12">
                            <div class="input-group search-group">
                                <input type="text" name="type" value="{{$type}}" hidden>
                                <input type="text" class="form-control search-box" name="search" placeholder="{{$searchTerm ?? null}}">
                                <span class="input-group-btn">
                                <button class="search-button" type="submit"><i class="fa fa-search" aria-hidden="true"></i>    </button>
                            </span>
                            </div><!-- /input-group -->
                        </div><!-- /.col-lg-6 -->
                        {!! Form::close() !!}
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <!-- If login is complete -->
                            @if(isset($isModerator) && $isModerator == 1 || isset($configurations) && (ONE::checkCBsOption($configurations, 'CREATE-TOPIC') && ONE::isAuth()) || isset($configurations) &&  ONE::checkCBsOption($configurations, 'CREATE-TOPICS-ANONYMOUS'))
                                <div class="row padding-box-default">
                                    <div class="col-xs-12 text-right create-button-div btn-create-group">
                                        <a href="{!! action('PublicTopicController@create', ['cbKey' => $cbKey, 'type' => $type]) !!}">{{ trans("defaultCbsProject2C.create_project_2c_topic") }}</a>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="margin-top">
                <div class="row cbs-boxes topics-grid" id="infinite-scroll">
                    @if(isset($listType))
                        @if($listType == 'listProject2Cs')
                            @include('public.default.cbs.project_2c.topicsPadsInList')
                        @else
                            @include('public.default.cbs.project_2c.topicsPads')
                        @endif
                    @endif

                    @if(empty($topics) || count($topics) == 0)
                        <div class="col-xs-12">
                            <div class="alertBoxGreen">
                                <div class="col-xs-12 text-center">
                                    <h2>{{ trans('defaultCbsProject2C.no_project_2c_topics_to_display') }}</h2>
                                </div>
                                <div class="col-xs-12 text-center">
                                    <i class="fa fa-exclamation-triangle" aria-hidden="true"></i>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

@endsection
@section('scripts')
    <script>

        $('#infinite-scroll').jscroll({
            loadingHtml: '<div class="text-center"><img src="{{ asset('images/preloader.gif') }}" alt="Loading" /></div>',
            nextSelector: 'a.jscroll-next:last',
            callback: checkBoxes
        });

        $(document).ready(function () {
            checkBoxes();
        });

        function checkBoxes(){
            $(document).ready(function () {
                // Setting equal heights for div's with jQuery
                var highestBox = 0;
                $('.topic-contents').each(function () {
                    if ($(this).height() > highestBox) {
                        highestBox = $(this).height();
                    }
                });

                $('.topic-contents').height(highestBox);
                <!-- Dot Dot Dot -->
                $.each([$(".topic-content"), $(".topic-title")], function (index, value) {
                    $(document).ready(function () {
                        value.dotdotdot({
                            ellipsis: '... ',
                            wrap: 'word',
                            aft: null,
                        });
                    });
                });

                $(window).trigger('resize.px.parallax');
            });
        }
    </script>
@endsection
