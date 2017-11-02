
@foreach ($topicsPagination as $topic)
    <div class="col-xs-12 cbs-boxes-padding row-eq-height" id="topicHorizontal">
        <!-- Files -->

        {{--@if(!empty($filesByType) && count($filesByType) >0 && isset($filesByType[$topic->topic_key]->images) && !empty(reset($filesByType[$topic->topic_key]->images)) )
            <div class="col-md-3 col-xs-12 news-inner-img-div" style="background-image:url('{{ action('FilesController@download', [reset($filesByType[$topic->topic_key]->images)->file_id, reset($filesByType[$topic->topic_key]->images)->file_code, 1])}}')">
            </div>
        @else
            <div class="col-md-3 col-xs-12 news-inner-img-div" style="background-image:url('{{ url('/images/empatia/default_img_contents.jpg')}}')">
            </div>
        @endif--}}
        <div class="col-md-12 col-xs-12" id="box-topic-contents">
            <div class="row cbs-box-horizontal">
                <a href="{!! action('PublicTopicController@show', [$cb->cb_key , $topic->topic_key, 'type' => $type] ) !!}">
                    <div class="col-md-12 col-xs-12">
                        <div class="row forum-state">
                            {!!  (!isset($topic->end_date)) ?
                                    '<i class="fa fa-inbox" aria-hidden="true"></i> '.trans("defaultCbsForum.forum_open") :
                                    ((\Carbon\Carbon::now() > $topic->end_date) ?
                                    '<span class="topic-closed"><i class="fa fa-inbox" aria-hidden="true"></i> '.trans("defaultCbsForum.forum_closed").'</span>' :
                                    '<i class="fa fa-inbox" aria-hidden="true"></i> '.trans("defaultCbsForum.forum_open"))!!}

                            <div class="pull-right"> {!! isset($topic->end_date) ? '<i class="fa fa-clock-o" aria-hidden="true"></i> '.trans("defaultCbsForum.closes_in").': '.\Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $topic->end_date)->format('Y-m-d') : '' !!}</div>
                        </div>
                    </div>

                    <div class="col-md-12 col-xs-12">
                        <div class="row">
                            <div class="my-topic-title col-md-12 col-xs-12 no-padding">
                                <h4>
                                    {{$topic->title}}
                                </h4>
                            </div>
                            <div class="my-topic-content col-md-12 col-xs-12 no-padding">
                                <p>{{ $topic->contents }}</p>
                            </div>
                        </div>
                    </div>
                </a>
                <div class="col-md-12 col-xs-12">

                </div>

                {{--<a href="{!! action('PublicTopicController@show', [$cb->cb_key , $topic->topic_key, 'type' => $type] ) !!}">
                    <div class="col-xs-12 view-more-btn text-center">
                        {{trans("defaultCbsForum.view_more")}} <span class="glyphicon glyphicon-chevron-right"></span>
                    </div>
                </a>--}}
                <div class="col-md-12 col-xs-12 bottom-topic">
                    <div class="row">
                        @if(!empty($voteType))
                            <div class="col-md-12 col-xs-12">
                            @foreach($voteType as $vt)
                                @if( isset($vt["genericConfigurations"]) && array_key_exists("vote_in_list", $vt["genericConfigurations"]) && $vt["genericConfigurations"]["vote_in_list"] == 1 && $vt['existVotes'])
                                    <div class="row my-vote">
                                        <div class="col-xs-12 no-padding">
                                            @if( $vt["method"] == "VOTE_METHOD_NEGATIVE")
                                                {!! Html::oneNegativeVoting($topic->topic_key,
                                                                            $cb->cb_key,$vt["key"],
                                                                            (isset($vt["genericConfigurations"]) && array_key_exists("show_total_votes",$vt["genericConfigurations"]) && $vt["genericConfigurations"]["show_total_votes"] == 1) ? (isset($vt["totalVotes"][$topic->topic_key])? $vt["totalVotes"][$topic->topic_key]["positive"] : '0' ): "",
                                                                            (isset($vt["genericConfigurations"]) && array_key_exists("show_total_votes", $vt["genericConfigurations"]) && $vt["genericConfigurations"]["show_total_votes"] == 1) ? (isset($vt["totalVotes"][$topic->topic_key])? $vt["totalVotes"][$topic->topic_key]["negative"] : '0' ): "",
                                                                            !empty($vt["allReadyVoted"][$topic->topic_key]) ? $vt["allReadyVoted"][$topic->topic_key] : null ,
                                                                            $vt["configurations"],[],
                                                                            (isset($status) || !ONE::isAuth()) ? true : (isset($vt["disabled"]) ? ($vt["disabled"] ? true : false) : false) ) !!}
                                            @elseif( $vt["method"] == "VOTE_METHOD_MULTI" )
                                                {!! Html::oneMultiVoting($topic->topic_key,
                                                                         $cb->cb_key,
                                                                         $vt["key"],
                                                                         (isset($vt["genericConfigurations"]) && array_key_exists("show_total_votes",$vt["genericConfigurations"]) && $vt["genericConfigurations"]["show_total_votes"] == 1) ? (isset($vt["totalVotes"][$topic->topic_key])? $vt["totalVotes"][$topic->topic_key]["positive"] : '0' ): "",
                                                                         !empty($vt["allReadyVoted"][$topic->topic_key]) ? $vt["allReadyVoted"][$topic->topic_key] : null ,
                                                                         $vt["configurations"],[],
                                                                         (isset($status) || !ONE::isAuth()) ? true : (isset($vt["disabled"]) ? ($vt["disabled"] ? true : false) : false) ) !!}
                                            @elseif( $vt["method"] == "VOTE_METHOD_LIKE" )
                                                {!! Html::oneLikes($topic->topic_key,
                                                                   $cb->cb_key,
                                                                   $vt["key"],
                                                                   (isset($vt["genericConfigurations"]) && array_key_exists("show_total_votes",$vt["genericConfigurations"]) && $vt["genericConfigurations"]["show_total_votes"] == 1) ? (isset($vt["totalVotes"][$topic->topic_key])? $vt["totalVotes"][$topic->topic_key]["positive"] : '0' ): "",
                                                                   (isset($vt["genericConfigurations"]) && array_key_exists("show_total_votes", $vt["genericConfigurations"]) && $vt["genericConfigurations"]["show_total_votes"] == 1) ? (isset($vt["totalVotes"][$topic->topic_key])? $vt["totalVotes"][$topic->topic_key]["negative"] : '0' ): "",
                                                                   !empty($vt["allReadyVoted"][$topic->topic_key]) ? $vt["allReadyVoted"][$topic->topic_key] : null,
                                                                   $vt["configurations"],[],
                                                                   (isset($status) || !ONE::isAuth()) ? true : (isset($vt["disabled"]) ? ($vt["disabled"] ? true : false) : false) ) !!}
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                            </div>
                        @endif

                        @if(!ONE::isAuth())
                            <div class="col-md-3 col-xs-12 text-center padding-top-20 pull-right">
                                <div class="row ">
                                    <div class="alertBoxGreenNews">
                                        <a href="{{ action('AuthController@login') }}" class="color-white"><i
                                                    class="fa fa-exclamation-circle " aria-hidden="true"
                                                    style=""></i> {{ trans("defaultCbsForum.login_to_vote") }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endif
                        <div class="col-md-10 col-xs-12 margin-top-minus-20">
                            <div class="row">
                                <i class="fa fa-clock-o"
                                   aria-hidden="true"></i> {{ trans("defaultCbsForum.created_at") }}
                                : {!! \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $topic->created_at)->format('Y-m-d') !!}
                                @if(ONE::checkCBsOption($configurations, 'ALLOW-COMMENTS'))
                                    <span class="glyphicon glyphicon-option-vertical"></span>
                                    <i class="fa fa-comments-o"
                                       aria-hidden="true"></i> {{$statistics->posts ?? 0}}
                                @endif
                                <span class="glyphicon glyphicon-option-vertical"></span>
                                <i class="fa fa-user"
                                   aria-hidden="true"></i> {{ $usersNames->{$topic->created_by}->name ?? trans('defaultCbsDiscussion.anonymous')}}
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>

    </div>

@endforeach
@if(!isset($noLoop) && !empty($pageToken))
    {{--<div class="row">--}}
    <div class="col-xs-12">
        <a class='jscroll-next'
           href='{{ URL::action('PublicCbsController@show',collect(['cbKey' => $cbKey])->merge(($filterList ?? ['type' => $type]))->merge(['page' => $pageToken, 'layout' => 'default','topic_status' => 'opened'])->toArray())}}'>{{ trans("cbs.next") }}</a>
    </div>{{--
            </div>--}}
@endif