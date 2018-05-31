@extends('public.empatia._layouts.index')
@section('header_styles')
    <link rel="stylesheet" href="{{ asset('css/empatia/cbs.css')}}">
@endsection
@section('content')

<!-- Header -->
<div class="container" style="padding-bottom: 50px">
    <div class="row menus-row">
        <div class="menus-line col-sm-6 col-sm-offset-3 mainTitleCb"><i class="fa fa-commenting"></i> {{$cb->title}}</div>
        <div style="clear:both;height:10px;"></div>
    </div>
    <div class="row">
        <div class="col-xs-12">
            <div class='cbContents'>{{$cb->contents}}</div>
        </div>
    </div>

    <div style="background-color: white;padding:10px 20px 10px 20px">

        @if($isModerator == 1 || (ONE::checkCBsOption($configurations, 'CREATE-TOPIC') && ONE::isAuth()) || ONE::checkCBsOption($configurations, 'CREATE-TOPICS-ANONYMOUS'))
            <div class="row">
                <div class="col-xs-12">
                    <div style="margin:40px 0 0px 0;font-family: Open Sans">
                        <a href="{!! action('PublicTopicController@create', ['cbKey'=> $cb->cb_key, 'type' => $type])  !!}"
                       class="createCBBtn">{{ trans("PublicCbs.create") }}</a>
                    </div>
                </div>
            </div>
        @endif



        <!-- forums List -->
        <div class="container-fluid">
            @foreach ($topics as $topic)
                <div class="row">
                    <div class="col-xs-12 discussionTopicsItem-container">
                        <div class="row">
                            <div class="col-xs-12 " id="forum_{{$topic->topic_key}}">
                                <!-- title -->
                                <div class="row">
                                    <div class="col-xs-12">
                                        <h3 class="forumTopicTitle textEllipsis" title='{{$topic->title}}'>
                                            <a href="{!! action('PublicTopicController@show', [$cb->cb_key , $topic->topic_key, 'type' => $type] ) !!}">{{$topic->title}}</a>
                                        </h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-9 col-md-8 col-sm-12 col-xs-12">
                                <!-- content -->
                                <div class="row">
                                    <div class="col-xs-12">
                                        <article class="forumTopicSummary">
                                            {{ $topic->contents }}
                                        </article>
                                    </div>
                                </div>

                                <!-- Details -->
                                <div class="container-fluid forumTopicDetails">
                                    <div class="row">
                                        <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12 forumTopicDetail">
                                            <p><i class="demo-icon icon-clock-icon"></i>
                                                {{ substr($topic->created_at,0,10) }}</p>
                                        </div>
                                        @if(!empty($usersNames->{$topic->created_by}))

                                            <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12 forumTopicDetail">
                                                <p><i class="demo-icon  icon-author-icon"></i>
                                                    {{ $usersNames->{$topic->created_by}->name }}</p>
                                            </div>
                                        @endif
                                        <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12 forumTopicDetail">
                                            @if($topic->statistics->posts_counter > 1)
                                                <p><i class="fa fa-comments-o" aria-hidden="true"></i> {{ $topic->statistics->posts_counter }} {{ trans("PublicCbs.comments") }}</p>
                                            @else
                                                <p><i class="fa fa-comments-o" aria-hidden="true"></i>  {{ trans("PublicCbs.without_comments") }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-4 col-sm-12 col-xs-12">
                                <!-- Parameters -->
                                <div class="row">
                                    <div class="col-xs-12 topicsItemParameters">
                                        @if(count($topic->parameters) > 1)
                                            @foreach($topic->parameters as $parameter)
                                                @if($parameter->code == "image_map")
                                                    <div class="">
                                                        <div>{{ trans("PublicCbs.location") }}</div>
                                                        <span>&#62;</span> {{ONE::verifyEmpavilleGeoArea($parameter->pivot->value)}}
                                                    </div>
                                                @elseif($parameter->code == "google_maps")
                                                <!-- Nothing to do yet! -->
                                                @else
                                                    <div class="">
                                                        <div class="parameterLabel">{{$parameter->parameter}}</div>
                                                        <div class="parameterTxt"> {{str_replace(array('<br/>', '&', '"'), ' ', isset($categoriesNameById[$parameter->pivot->value]) ? $categoriesNameById[$parameter->pivot->value] : ''.$parameter->pivot->value)}}</div>
                                                    </div>
                                                @endif
                                            @endforeach
                                        @else
                                            @for($count = 0; $count < $parametersMaxCount; $count++)
                                                <div class="">
                                                    <div>&nbsp;</div>
                                                    <span></span>&nbsp;
                                                </div>
                                            @endfor

                                        @endif
                                    </div>
                                </div>

                                @if(!empty($voteType))
                                    @foreach($voteType as $vt)
                                        @if( isset($vt["genericConfigurations"]) && array_key_exists("vote_in_list", $vt["genericConfigurations"]) && $vt["genericConfigurations"]["vote_in_list"] == 1 && $vt['existVotes'])
                                            <div class="row">
                                                <div class="col-xs-12 forumTopicsItemVoting" style="">
                                                    @if( $vt["method"] == "VOTE_METHOD_NEGATIVE" )
                                                        {!! Html::oneNegativeVoting($topic->topic_key,
                                                                                    $cb->cb_key,$vt["key"],
                                                                                    (isset($vt["genericConfigurations"]) && array_key_exists("show_total_votes",$vt["genericConfigurations"]) && $vt["genericConfigurations"]["show_total_votes"] == 1) ? (isset($vt["totalVotes"][$topic->topic_key])? $vt["totalVotes"][$topic->topic_key]["positive"] : '0' ): "",
                                                                                    (isset($vt["genericConfigurations"]) && array_key_exists("show_total_votes", $vt["genericConfigurations"]) && $vt["genericConfigurations"]["show_total_votes"] == 1) ? (isset($vt["totalVotes"][$topic->topic_key])? $vt["totalVotes"][$topic->topic_key]["negative"] : '0' ): "",
                                                                                    !empty($vt["allReadyVoted"][$topic->topic_key]) ? $vt["allReadyVoted"][$topic->topic_key] : null ,
                                                                                    $vt["configurations"]) !!}
                                                    @elseif( $vt["method"] == "VOTE_METHOD_MULTI" )
                                                        {!! Html::oneMultiVoting($topic->topic_key,
                                                                                 $cb->cb_key,
                                                                                 $vt["key"],
                                                                                 (isset($vt["genericConfigurations"]) && array_key_exists("show_total_votes",$vt["genericConfigurations"]) && $vt["genericConfigurations"]["show_total_votes"] == 1) ? (isset($vt["totalVotes"][$topic->topic_key])? $vt["totalVotes"][$topic->topic_key]["positive"] : '0' ): "",
                                                                                 !empty($vt["allReadyVoted"][$topic->topic_key]) ? $vt["allReadyVoted"][$topic->topic_key] : null ,
                                                                                 $vt["configurations"]) !!}
                                                    @elseif( $vt["method"] == "VOTE_METHOD_LIKE" )
                                                        {!! Html::oneLikes($topic->topic_key,
                                                                           $cb->cb_key,
                                                                           $vt["key"],
                                                                           (isset($vt["genericConfigurations"]) && array_key_exists("show_total_votes",$vt["genericConfigurations"]) && $vt["genericConfigurations"]["show_total_votes"] == 1) ? (isset($vt["totalVotes"][$topic->topic_key])? $vt["totalVotes"][$topic->topic_key]["positive"] : '0' ): "",
                                                                           (isset($vt["genericConfigurations"]) && array_key_exists("show_total_votes", $vt["genericConfigurations"]) && $vt["genericConfigurations"]["show_total_votes"] == 1) ? (isset($vt["totalVotes"][$topic->topic_key])? $vt["totalVotes"][$topic->topic_key]["negative"] : '0' ): "",
                                                                           !empty($vt["allReadyVoted"][$topic->topic_key]) ? $vt["allReadyVoted"][$topic->topic_key] : null,
                                                                           $vt["configurations"]) !!}
                                                    @endif
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
            <!-- End forums -->
        </div>
    </div>
</div>
@endsection