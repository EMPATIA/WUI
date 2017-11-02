@foreach ($topicsPagination as $topic)
    <div class="col-xs-12">
        <a href="{!! action('PublicTopicController@show', [$cb->cb_key , $topic->topic_key, 'type' => $type] ) !!}">
            <div class="row row-eq-height ideas-list-box">
                <!-- Files -->
                @if(!empty($filesByType) && count($filesByType) >0 && isset($filesByType[$topic->topic_key]->images) && !empty(reset($filesByType[$topic->topic_key]->images)) )
                    <div class="col-md-2 col-sm-4 hidden-xs ideas-cover-img"
                         style="background-image:url('{{ action('FilesController@download', [reset($filesByType[$topic->topic_key]->images)->file_id, reset($filesByType[$topic->topic_key]->images)->file_code, 1])}}')">
                    </div>
                @else
                    <div class="col-md-2 col-sm-4 hidden-xs ideas-cover-img"
                         style="background-image:url('{{ url('/images/empatia/default_img_contents.jpg')}}')">
                    </div>
                @endif
                <div class="col-xs-10">
                    <div class="row">
                        <div class="col-xs-12 topic-title-list">
                            <h4>
                                {{$topic->title}}
                            </h4>
                        </div>
                        <div class="col-xs-12 text-right">
                            <i class="glyphicon glyphicon-time" aria-hidden="true"></i>
                            {{\Carbon\Carbon::now()->diffInDays(\Carbon\Carbon::parse($topic->created_at)).' '.trans('defaultCbsProposal.days')}}
                        </div>
                        @foreach($topic->parameters as $parameter)
                            @if($parameter->visible_in_list)
                                <div class="parameter-object-list">
                                    @if($parameter->type->code == 'image_map')
                                        <i class="fa fa-map-marker" aria-hidden="true"></i>
                                        {{$parameter->pivot->value}}
                                    @elseif($parameter->type->code == 'google_maps')
                                        <i class="fa fa-map-marker" aria-hidden="true"></i>
                                        {!! Form::oneReverseGeocoding("streetReverseGeocoding".$topic->id, "", $parameter->pivot->value )  !!}
                                    @else
                                        <span class="glyphicon glyphicon-folder-open"></span>
                                        @if($parameter->type->code == "dropdown" || $parameter->type->code == "category" || $parameter->type->code == "budget")
                                            @foreach($parameter->options as $option)
                                                @if($parameter->pivot->value == $option->id)
                                                    {{$option->label}}
                                                @endif
                                            @endforeach
                                        @elseif(isset($permissions) &&  array_key_exists("ALLOW-VIDEO-LINK",$permissions ) && $permissions["ALLOW-VIDEO-LINK"] && preg_match('/http:\/\/(www\.)*youtube\.com\/.*/', $parameter->pivot->value ) )
                                            <iframe title="YouTube video player" class="youtube-player" type="text/html"
                                                    width="640" height="390" src="{{$parameter->pivot->value}}" frameborder="0"
                                                    allowFullScreen></iframe>
                                        @elseif(isset($permissions) &&  array_key_exists("ALLOW-VIDEO-LINK",$permissions ) && $permissions["ALLOW-VIDEO-LINK"] && preg_match('/http:\/\/(www\.)*vimeo\.com\/.*/',$parameter->pivot->value) )
                                            <iframe src="{{$parameter->pivot->value}}" width="640" height="390" frameborder="0"
                                                    webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
                                        @else
                                            {{$parameter->pivot->value}}
                                        @endif
                                    @endif
                                </div>
                            @endif
                        @endforeach
                        <div class="col-xs-12 topic-content-list" style="color: #6D6D6D;">
                            <p>{{ $topic->contents }}</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 col-xs-12">
                            <div class="parameter-user-comments">
                                <span class="glyphicon glyphicon-user"></span> {{ $usersNames->{$topic->created_by}->name ?? trans('defaultCbsProposal.anonymous')}}
                            </div>
                            @if(ONE::checkCBsOption($configurations, 'ALLOW-COMMENTS'))
                                <div class="parameter-user-comments">
                                    <span class="glyphicon glyphicon-comment"></span> {{$topic->statistics->posts_counter ?? 0}} {{ trans('defaultCbsProposal.comments')}}
                                </div>
                            @endif
                        </div>
                        <div class="col-md-4 col-xs-12">
                            @if(!ONE::isAuth() && !empty($voteType))
                                <div class="alertBoxGreenNews ideas-box-text">
                                    <a href="{{ action('AuthController@login') }}" class="color-white">
                                        <i class="fa fa-exclamation-circle " aria-hidden="true"
                                           style="line-height: 50px;"></i>
                                        {{ trans("defaultCbsProposal.login_to_vote") }}
                                    </a>
                                </div>
                            @endif
                        </div>
                        <div class="col-md-4 col-xs-12">
                            @if(!empty($voteType))
                                @foreach($voteType as $vt)
                                    @if( isset($vt["genericConfigurations"]) && array_key_exists("vote_in_list", $vt["genericConfigurations"]) && $vt["genericConfigurations"]["vote_in_list"] == 1 && $vt['existVotes'])
                                        <div class="col-xs-12">
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
                                    @endif
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>
@endforeach
@if(!isset($noLoop))
    @if(!empty($pageToken))
        <div class="col-xs-12">
            @if(isset($filters))
                <a class='jscroll-next'
                   href='{{ URL::action('PublicCbsController@show', ["cbKey"=> $cb->cb_key, "type"=> $type, 'page' => $pageToken, 'filters' => $filters,'listType' => 'listProposals'])}}'>{{ trans("defaultCbsProposal.next") }}</a>
            @else
                <a class='jscroll-next'
                   href='{{ URL::action('PublicCbsController@show', ["cbKey"=> $cb->cb_key, "type"=> $type, 'page' => $pageToken,'listType' => 'listProposals'])}}'>{{ trans("cbsIdea.next") }}</a>
            @endif
        </div>

    @endif
@endif