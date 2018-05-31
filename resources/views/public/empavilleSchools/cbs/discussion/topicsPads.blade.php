
@foreach ($topicsPagination as $topic)
    <div class="col-xs-12 cbs-boxes-padding row-eq-height" id="topicHorizontal">
        <!-- Files -->
        @if(!empty($filesByType) && count($filesByType) >0 && isset($filesByType->images) && !empty(reset($filesByType->images)) )
            <div class="col-md-3 col-xs-12 news-inner-img-div" style="background-image:url('{{ action('FilesController@download', [reset($filesByType[$topic->topic_key]->images)->file_id, reset($filesByType[$topic->topic_key]->images)->file_code, 1])}}')">
            </div>
        @else
            <div class="col-md-3 col-xs-12 news-inner-img-div" style="background-image:url('{{ url('/images/empatia/default_img_contents.jpg')}}')">
            </div>
        @endif
        <div class="col-md-9 col-xs-12" id="box-topic-contents">
            <div class="row cbs-box-horizontal">
                <div class="col-md-10 col-xs-12">
                    <div class="row">
                        <div class="col-xs-12 topic-title">
                            <h4>
                                <a href="{!! action('PublicTopicController@show', [$cb->cb_key , $topic->topic_key, 'type' => $type] ) !!}">{{$topic->title}}</a>
                            </h4>
                        </div>
                        <div class="col-xs-12 topic-content">
                            <p>{{ $topic->contents }}</p>
                        </div>
                        <div class="col-xs-12">
                            <span class="glyphicon glyphicon-user"></span> {{ $usersNames->{$topic->created_by}->name ?? trans('defaultCbsDiscussion.anonymous')}}
                            @if(ONE::checkCBsOption($configurations, 'ALLOW-COMMENTS'))
                                <span class="glyphicon glyphicon-comment"></span> {{$topic->statistics->posts_counter ?? 0}}
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-md-2 col-xs-12">
                    @if(!empty($voteType))
                        @foreach($voteType as $vt)
                            @if( isset($vt["genericConfigurations"]) && array_key_exists("vote_in_list", $vt["genericConfigurations"]) && $vt["genericConfigurations"]["vote_in_list"] == 1 && $vt['existVotes'])
                                <div class="row">
                                    <div class="col-xs-12">
                                        @if( $vt["method"] == "VOTE_METHOD_NEGATIVE")
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
                <a href="{!! action('PublicTopicController@show', [$cb->cb_key , $topic->topic_key, 'type' => $type] ) !!}">
                    <div class="col-xs-12 view-more-btn text-center">
                        {{trans("defaultCbsDiscussion.view_more")}} <span class="glyphicon glyphicon-chevron-right"></span>
                    </div>
                </a>
            </div>
        </div>
    </div>
@endforeach
@if(!empty($topicsPagination->nextPageUrl()))
    <div class="row">
        <div class="col-xs-12">
            <a class='jscroll-next' href='{{ URL::action('PublicCbsController@show', ["cbKey"=> $cb->cb_key, "type"=> $type, 'page' => $topicsPagination->currentPage()+1])}}'>{{ trans("defaultCbsDiscussion.next") }}</a>
        </div>
    </div>
@endif