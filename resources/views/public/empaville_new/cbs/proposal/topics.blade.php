@extends('public.empaville_new._layouts.index')
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
                        <h3>{!! $cb->title ?? trans('defaultCbsProposal.proposal') !!}</h3>
                    </div>
                </div>
                <div class="col-xs-12">
                    <div class="pads-description">
                        {!! $cb->contents ?? trans('defaultCbsProposal.proposal_description') !!}
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
                                        {!! trans('defaultCbsProposal.filter_by') !!}
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
                                    <div class="pull-left">
                                        <div class="dropdown">
                                            <button class="btn-filter dropdown-toggle" type="button" data-toggle="dropdown">{{$parameter['name']}}
                                                <i class="fa fa-angle-down angle-down-filter" aria-hidden="true"></i>
                                            </button>
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
                                        {!! trans('defaultCbsProposal.order_by') !!}
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-12">
                                    <div class="pull-left">
                                        <div class="dropdown">
                                            <button class="btn-filter dropdown-toggle" type="button" data-toggle="dropdown">{{trans('defaultCbsProposal.order_by')}}
                                                <i class="fa fa-angle-down angle-down-filter" aria-hidden="true"></i></button>
                                            <ul class="dropdown-menu">
                                                <li><a href={{action('PublicCbsController@show',['cbKey' => $cbKey, 'type' => $type, 'orderBy' => 'desc'])}}>{{trans('defaultCbsProposal.date')}} <i class="fa fa-arrow-up" aria-hidden="true"></i></a></li>
                                                <li><a href={{action('PublicCbsController@show',['cbKey' => $cbKey, 'type' => $type, 'orderBy' => 'asc'])}}>{{trans('defaultCbsProposal.date')}} <i class="fa fa-arrow-down" aria-hidden="true"></i></a></li>
                                                <li><a href={{action('PublicCbsController@show',['cbKey' => $cbKey, 'type' => $type, 'popularity' => 'desc'])}}>{{trans('defaultCbsProposal.popularity')}} <i class="fa fa-arrow-up" aria-hidden="true"></i></a></li>
                                                <li><a href={{action('PublicCbsController@show',['cbKey' => $cbKey, 'type' => $type, 'popularity' => 'asc'])}}>{{trans('defaultCbsProposal.popularity')}} <i class="fa fa-arrow-down" aria-hidden="true"></i></a></li>
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
                                    <a href="{!! action('PublicCbsController@show', ['cbKey' => $cbKey, 'type' => $type, 'listType' => 'listProposals'] ) !!}" >
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
                                        <a href="{!! action('PublicTopicController@create', ['cbKey' => $cbKey, 'type' => $type]) !!}">{{ trans("defaultCbsProposal.create_proposal_topic") }}</a>
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
                        @if($listType == 'listProposals')
                            @include('public.empaville_new.cbs.proposal.topicsPadsInList')
                        @else
                            @include('public.empaville_new.cbs.proposal.topicsPads')
                        @endif
                    @endif

                    @if(empty($topics) || count($topics) == 0)
                        <div class="col-xs-12">
                            <div class="alertBoxGreen">
                                <div class="col-xs-12 text-center">
                                    <h2>{{ trans('defaultCbsProposal.no_proposal_topics_to_display') }}</h2>
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
                $('.cbs-box').each(function () {
                    if ($(this).height() > highestBox) {
                        highestBox = $(this).height();
                    }
                });

                $('.cbs-box').height(highestBox);

                <!-- Dot Dot Dot -->
                $.each([$(".topic-content"), $(".topic-title")], function (index, value) {
                    $(document).ready(function () {
                        value.dotdotdot({
                            ellipsis: '... ',
                            wrap: 'word',
                            aft: null,
                            watch:true
                        });
                    });
                });

                $(window).trigger('resize.px.parallax');
            });
        }
    </script>
@endsection
