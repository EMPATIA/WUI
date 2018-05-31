
@foreach ($topicsPagination as $topic)
    <div class="col-xs-12 forumTopic-container cbs-forum-box" id="forum_{{$topic->topic_key}}" style="background-color: white;" data-href="{!! action('PublicTopicController@show', [$cb->cb_key , $topic->topic_key, 'type' => $type] ) !!}">
        <!-- title -->
        <div class="row">
            <div class="col-xs-12">
                <h3 class="forumTopicTitle textEllipsis" title='{{$topic->title}}'>
                    <a href="{!! action('PublicTopicController@show', [$cb->cb_key , $topic->topic_key, 'type' => $type] ) !!}">{{$topic->title}}</a>
                </h3>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <!-- content -->
                <div class="row">
                    <div class="col-xs-12">
                        <article class="forumTopicSummary">
                            {{ $topic->contents }}
                        </article>
                    </div>
                </div>

                <!-- Details -->
                <div class="forumTopicDetails">
                    <!-- Parameters -->
                    <div class="row">
                        @foreach($topic->parameters as $parameter)
                            @if($parameter->visible_in_list)
                                <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12 parameters-topic">
                                    @if($parameter->type->code == 'image_map')
                                        <i class="fa fa-map-marker" aria-hidden="true"></i><b>{{ trans("pads.location") }}: </b>
                                        {{$parameter->pivot->value}}
                                    @elseif($parameter->type->code == 'google_maps')
                                        <i class="fa fa-map-marker" aria-hidden="true"></i><b>{{ $parameter->parameter }}: </b>
                                        {!! Form::oneReverseGeocoding("streetReverseGeocoding".$topic->id, "", $parameter->pivot->value )  !!}
                                    @else
                                        <i class="fa fa-folder-open" aria-hidden="true"></i> <b>{{ $parameter->parameter }}: </b>
                                        @if($parameter->type->code == "dropdown" || $parameter->type->code == "category" || $parameter->type->code == "budget")
                                            @foreach($parameter->options as $option)
                                                @if($parameter->pivot->value == $option->id)
                                                    {{$option->label}}
                                                @endif
                                            @endforeach
                                        @elseif(isset($permissions) &&  array_key_exists("ALLOW-VIDEO-LINK",$permissions ) && $permissions["ALLOW-VIDEO-LINK"] && preg_match('/http:\/\/(www\.)*youtube\.com\/.*/', $parameter->pivot->value ) )
                                            <iframe title="YouTube video player" class="youtube-player" type="text/html" width="640" height="390" src="{{$parameter->pivot->value}}" frameborder="0" allowFullScreen></iframe>
                                        @elseif(isset($permissions) &&  array_key_exists("ALLOW-VIDEO-LINK",$permissions ) && $permissions["ALLOW-VIDEO-LINK"] && preg_match('/http:\/\/(www\.)*vimeo\.com\/.*/',$parameter->pivot->value) )
                                            <iframe src="{{$parameter->pivot->value}}" width="640" height="390" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
                                        @elseif($parameter->type->code == "questionnaire")
                                            <a href="{!! action('PublicQController@showQ',$parameter->pivot->value)!!}" target="_blank">
                                                {{ trans("padsPublicConsultation.form") }}
                                            </a>
                                        @else
                                            {{$parameter->pivot->value}}
                                        @endif
                                    @endif
                                </div>
                            @endif
                        @endforeach
                    </div>
                    <div class="row">
                        <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12 forumTopicDetail">
                            <i class="fa fa-clock-o" aria-hidden="true"></i>
                                {{ substr($topic->created_at,0,10) }}
                        </div>
                        @if(!empty($usersNames->{$topic->created_by}))
                            <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12 forumTopicDetail">
                                <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
                                {{ $usersNames->{$topic->created_by}->name }}
                            </div>
                        @endif
                        @if(ONE::checkCBsOption($configurations, 'ALLOW-COMMENTS'))
                            <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12 forumTopicDetail">
                                @if($topic->statistics->posts_counter > 1)
                                    <i class="fa fa-comments-o" aria-hidden="true"></i> {{ $topic->statistics->posts_counter }} {{ trans("PublicCbs.comments") }}
                                @else
                                    <i class="fa fa-comments-o" aria-hidden="true"></i>  {{ trans("PublicCbs.without_comments") }}
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>

        </div>
        <!-- Votes -->
        @if(!empty($voteType) && !empty($topic->closed))
            <div class="row">
                @foreach($voteType as $vt)
                    @if( isset($vt["genericConfigurations"]) && array_key_exists("vote_in_list", $vt["genericConfigurations"]) && $vt["genericConfigurations"]["vote_in_list"] == 1 && $vt['existVotes'])
                        <div class="col-md-3 col-sm-6 col-xs-12 forumTopicsItemVoting">
                            @if( $vt["method"] == "VOTE_METHOD_NEGATIVE" )
                                {!! Html::oneNegativeVoting($topic->topic_key,
                                                            $cb->cb_key,$vt["key"],
                                                            ($vt["genericConfigurations"]["show_total_votes"] ?? 0),
                                                            (isset($vt["genericConfigurations"]) && array_key_exists("show_total_votes",$vt["genericConfigurations"]) && $vt["genericConfigurations"]["show_total_votes"] == 1) ? (isset($vt["totalVotes"][$topic->topic_key])? $vt["totalVotes"][$topic->topic_key]["positive"] : '0' ): "",
                                                            (isset($vt["genericConfigurations"]) && array_key_exists("show_total_votes", $vt["genericConfigurations"]) && $vt["genericConfigurations"]["show_total_votes"] == 1) ? (isset($vt["totalVotes"][$topic->topic_key])? $vt["totalVotes"][$topic->topic_key]["negative"] : '0' ): "",
                                                            !empty($vt["allReadyVoted"][$topic->topic_key]) ? $vt["allReadyVoted"][$topic->topic_key] : null ,
                                                            $vt["configurations"],[],
                                                            (!ONE::isAuth()) ? true : (isset($vt["disabled"]) ? ($vt["disabled"] ? true : false) : false)) !!}
                            @elseif( $vt["method"] == "VOTE_METHOD_MULTI" )
                                {!! Html::oneMultiVoting($topic->topic_key,
                                                         $cb->cb_key,
                                                         $vt["key"],
                                                         (isset($vt["genericConfigurations"]) && array_key_exists("show_total_votes",$vt["genericConfigurations"]) && $vt["genericConfigurations"]["show_total_votes"] == 1) ? (isset($vt["totalVotes"][$topic->topic_key])? $vt["totalVotes"][$topic->topic_key]["positive"] : '0' ): "",
                                                         !empty($vt["allReadyVoted"][$topic->topic_key]) ? $vt["allReadyVoted"][$topic->topic_key] : null ,
                                                         $vt["configurations"],[],
                                                         (!ONE::isAuth()) ? true : (isset($vt["disabled"]) ? ($vt["disabled"] ? true : false) : false)) !!}
                            @elseif( $vt["method"] == "VOTE_METHOD_LIKE" )
                                {!! Html::oneLikes($topic->topic_key,
                                                   $cb->cb_key,
                                                   $vt["key"],
                                                   (isset($vt["genericConfigurations"]) && array_key_exists("show_total_votes",$vt["genericConfigurations"]) && $vt["genericConfigurations"]["show_total_votes"] == 1) ? (isset($vt["totalVotes"][$topic->topic_key])? $vt["totalVotes"][$topic->topic_key]["positive"] : '0' ): "",
                                                   (isset($vt["genericConfigurations"]) && array_key_exists("show_total_votes", $vt["genericConfigurations"]) && $vt["genericConfigurations"]["show_total_votes"] == 1) ? (isset($vt["totalVotes"][$topic->topic_key])? $vt["totalVotes"][$topic->topic_key]["negative"] : '0' ): "",
                                                   !empty($vt["allReadyVoted"][$topic->topic_key]) ? $vt["allReadyVoted"][$topic->topic_key] : null,
                                                   $vt["configurations"],[],
                                                   (!ONE::isAuth()) ? true : (isset($vt["disabled"]) ? ($vt["disabled"] ? true : false) : false)) !!}
                            @endif
                        </div>
                    @endif
                @endforeach
            </div>
        @endif
    </div>
@endforeach
@if(!empty($topicsPagination->nextPageUrl()))
    <div class="row">
        <div class="col-xs-12">
            <a class='jscroll-next' href='{{ URL::action('PublicCbsController@show', ["cbKey"=> $cb->cb_key, "type"=> $type, 'page' => $topicsPagination->currentPage()+1])}}'>{{ trans("defaultCbsForum.next") }}</a>
        </div>
    </div>
@endif