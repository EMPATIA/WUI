@extends('public.default._layouts.index')
@section('header_scripts')
    <!-- Maps -->
    <script   src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCjgiI5l8FanufeE3GRchTZSVOaAyzVIE8" type="text/javascript"></script>
@endsection
@section('content')
    {{--Topics list section--}}
    <section>
        <div class="container pads-container">
            <div class="row">
                <div class="col-xs-12">
                    <div class="pads-title">
                        <h3>{!! $cb->title ?? trans('defaultCbsIdea.idea') !!}</h3>
                    </div>
                </div>
                <div class="col-xs-12">
                    <div class="pads-description">
                        {!! $cb->contents ?? trans('defaultCbsIdea.idea_description') !!}
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-9">
                    {{--<div class="row">
                        <div class="col-xs-12">
                            <div class="filter-label">
                                {!! trans('defaultPadsIdea.filter_by') !!}
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            @foreach($parameters as $parameter)
                                @if($parameter['filter'])
                                    <div class="pull-left">
                                        <div class="dropdown">
                                            <button class="btn-filter dropdown-toggle" type="button" data-toggle="dropdown">{{$parameter['name']}}
                                                <i class="fa fa-angle-down angle-down-filter" aria-hidden="true"></i></button>
                                            <ul class="dropdown-menu">
                                                @foreach($parameter['options'] as $option)
                                                    <li><a href={{action('PublicCbsController@show',['cbKey' => $cbKey, 'type' => $type, 'filter_'.$parameter["id"] => $option["id"] ]) }}>{{$option['name']}}</a></li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="row">
                                <div class="col-xs-12">
                                    <div class="order-label">
                                        {!! trans('defaultPadsIdea.order_by') !!}
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-12">
                                    <div class="pull-left">
                                        <div class="dropdown">
                                            <button class="btn-filter dropdown-toggle" type="button" data-toggle="dropdown">{{trans('defaultPadsIdea.order_by')}}
                                                <i class="fa fa-angle-down angle-down-filter" aria-hidden="true"></i></button>
                                            <ul class="dropdown-menu">
                                                <li><a href={{action('PublicCbsController@show',['cbKey' => $cbKey, 'type' => $type, 'orderBy' => 'desc'])}}>{{trans('defaultPadsIdea.date')}} <i class="fa fa-arrow-up" aria-hidden="true"></i></a></li>
                                                <li><a href={{action('PublicCbsController@show',['cbKey' => $cbKey, 'type' => $type, 'orderBy' => 'asc'])}}>{{trans('defaultPadsIdea.date')}} <i class="fa fa-arrow-down" aria-hidden="true"></i></a></li>
                                                <li><a href={{action('PublicCbsController@show',['cbKey' => $cbKey, 'type' => $type, 'popularity' => 'desc'])}}>{{trans('defaultPadsIdea.popularity')}} <i class="fa fa-arrow-up" aria-hidden="true"></i></a></li>
                                                <li><a href={{action('PublicCbsController@show',['cbKey' => $cbKey, 'type' => $type, 'popularity' => 'asc'])}}>{{trans('defaultPadsIdea.popularity')}} <i class="fa fa-arrow-down" aria-hidden="true"></i></a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>--}}
                </div>
                <div class="col-md-3">
                    {{--<div class="row">
                        <div class="col-xs-12">
                            <div class="layout-icons">
                                <i class="fa fa-th fa-2x" aria-hidden="true"></i>
                                <i class="fa fa-list fa-2x" aria-hidden="true"></i>
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
                                <button class="btn search-button" type="submit"><i class="fa fa-search" aria-hidden="true"></i>    </button>
                            </span>
                            </div><!-- /input-group -->
                        </div><!-- /.col-lg-6 -->
                        {!! Form::close() !!}
                    </div>--}}
                    <div class="row">
                        <div class="col-xs-12">
                            <!-- If login is complete -->
                            @if(isset($isModerator) && $isModerator == 1 || isset($configurations) && (ONE::checkCBsOption($configurations, 'CREATE-TOPIC') && ONE::isAuth()) || isset($configurations) &&  ONE::checkCBsOption($configurations, 'CREATE-TOPICS-ANONYMOUS'))
                                <div class="row padding-box-default">
                                    <div class="col-xs-12 text-right create-button-div btn-create-group">
                                        <a href="{!! action('PublicTopicController@create', ['cbKey' => $cbKey, 'type' => $type]) !!}">{{ trans("defaultCbsIdea.create_idea_topic") }}</a>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="margin-top">
                <div class="row cbs-boxes topics-grid" id="infinite-scroll">
                    <div class="text-center" id="first-loader"><img src="{{ asset('images/bluePreLoader.gif') }}" alt="Loading" style="width: 40px; margin-bottom:40px;"/></div>
                </div>
            </div>
        </div>
    </section>

@endsection
@section('scripts')
    <script>

        $(document).ready(function () {
            dataToSend = {
                "ajax_call": true,
                "topics_to_show": 6
            };
            $.ajax({
                url: "{{ action('PublicCbsController@show',["cbKey"=> $cb->cb_key, "type"=> $type]) }}",
                type: "get", //send it through get method
                data: dataToSend,
                success: function (response) {
                    $("#first-loader").remove();
                    $("#infinite-scroll").html(response);
                    $('#infinite-scroll').jscroll({
                        loadingHtml: '<div class="text-center"><img src="{{ asset('images/bluePreLoader.gif') }}" alt="Loading" style="width: 40px;"/></div>',
                        nextSelector: 'a.jscroll-next:last',
                        callback: checkBoxes
                    });
                    checkBoxes();

                },
                error: function (xhr) {

                }
            });
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
                            watch:true,
                        });
                    });
                });

                $(window).trigger('resize.px.parallax');
            });
        }
    </script>
@endsection
