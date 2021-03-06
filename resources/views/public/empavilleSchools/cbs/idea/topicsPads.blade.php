@foreach ($topicsPagination as $topic)
    <div class="col-lg-4 col-md-6 col-xs-12 cbs-boxes-padding">
        <div class="cbs-box-border">
            <a href="{!! action('PublicTopicController@show', [$cb->cb_key , $topic->topic_key, 'type' => $type] ) !!}">
                {{--Files--}}
                @if(!empty($filesByType) && count($filesByType) >0 && isset($filesByType[$topic->topic_key]->images) && !empty(reset($filesByType[$topic->topic_key]->images)) )
                    <div class="col-xs-12 news-inner-img-div hidden-xs"
                         style="background-image:url('{{ action('FilesController@download', [reset($filesByType[$topic->topic_key]->images)->file_id, reset($filesByType[$topic->topic_key]->images)->file_code, 1])}}')">
                    </div>
                @else
                    <div class="col-xs-12 news-inner-img-div hidden-xs"
                         style="background-image:url('{{ url('/images/empatia/default_img_contents.jpg')}}')">
                    </div>
                @endif
            </a>
            <div class="col-xs-12 cbs-box">
                <a href="{!! action('PublicTopicController@show', [$cb->cb_key , $topic->topic_key, 'type' => $type] ) !!}">
                    <div class="row topic-contents">
                        <div class="col-xs-12 topic-title idea-topic-title">
                            <h4>
                                {{$topic->title}}
                            </h4>
                        </div>
                        <div class="col-xs-12 topic-content">
                            <p>
                                @if($topic->contents == '')
                                    {{$topic->first_post->contents}}
                                @else
                                    {{  $topic->contents }}
                                @endif
                            </p>
                        </div>
                        <div class="col-xs-12 idea-topic-parameters">
                            <div class="row">
                                @foreach($topic->parameters as $parameter)
                                    @if($parameter->visible_in_list)
                                        @if($parameter->type->code == 'image_map')
                                            <div class="col-xs-12 idea-topic-parameters-local">
                                                <i class="fa fa-map-marker" aria-hidden="true"></i>
                                                {{$parameter->pivot->value}}
                                            </div>
                                        @elseif($parameter->type->code == 'google_maps')
                                            <div class="col-xs-12 idea-topic-parameters-local">
                                                <i class="fa fa-map-marker" aria-hidden="true"></i>
                                                {!! Form::oneReverseGeocoding("streetReverseGeocoding".$topic->id, "", $parameter->pivot->value )  !!}
                                            </div>
                                        @endif
                                    @endif
                                @endforeach
                            </div>
                            <div class="row">
                                @foreach($topic->parameters as $parameter)
                                    @if($parameter->visible_in_list)
                                        @if(($parameter->type->code != "google_maps") && ($parameter->type->code != "image_map"))
                                            <div class="col-sm-4">
                                                <i class="fa fa-folder-open-o" aria-hidden="true"></i>
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
                                                @else
                                                    {{$parameter->pivot->value}}
                                                @endif
                                            </div>
                                        @endif
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                </a>
                <div class="row">
                    <div class="col-xs-12 topics-ideaDetailsVoting">
                        <div class="row">
                            <a href="{!! action('PublicTopicController@show', [$cb->cb_key , $topic->topic_key, 'type' => $type] ) !!}">
                                <div class="col-xs-6 topic-list-bottom">
                                    <div class="topics-ideaDetail">
                                        <i class="fa fa-user" aria-hidden="true"></i> {{ $usersNames->{$topic->created_by}->name ?? trans('empavilleSchoolsPadsIdea.anonymous')}}
                                    </div>
                                    @if(ONE::checkCBsOption($configurations, 'ALLOW-COMMENTS') && $topic->statistics->posts_counter > 0)
                                        <div >
                                            <i class="fa fa-comment-o" aria-hidden="true"></i> {{$topic->statistics->posts_counter ?? 0 }} {{trans('empavilleSchoolsPadsIdea.comments')}}
                                        </div>
                                    @endif
                                </div>
                            </a>
                            @if(!empty($voteType))
                                @foreach($voteType as $vt)
                                    @if( isset($vt["genericConfigurations"]) && array_key_exists("vote_in_list", $vt["genericConfigurations"]) && $vt["genericConfigurations"]["vote_in_list"] == 1 && $vt['existVotes'])
                                        <div class="col-xs-6 col-sm-6 columnXS12" style="margin-bottom: 10px">
                                            @if( $vt["method"] == "VOTE_METHOD_NEGATIVE" )
                                                {!! Html::oneNegativeVoting($topic->topic_key,
                                                                            $cbKey,$vt["key"],
                                                                            ($vt["genericConfigurations"]["show_total_votes"] ?? 0),
                                                                            (isset($vt["genericConfigurations"]) && array_key_exists("show_total_votes",$vt["genericConfigurations"]) && $vt["genericConfigurations"]["show_total_votes"] == 1) ? (isset($vt["totalVotes"][$topic->topic_key])? $vt["totalVotes"][$topic->topic_key]["positive"] : '0' ): "",
                                                                            (isset($vt["genericConfigurations"]) && array_key_exists("show_total_votes", $vt["genericConfigurations"]) && $vt["genericConfigurations"]["show_total_votes"] == 1) ? (isset($vt["totalVotes"][$topic->topic_key])? $vt["totalVotes"][$topic->topic_key]["negative"] : '0' ): "",
                                                                            !empty($vt["allReadyVoted"][$topic->topic_key]) ? $vt["allReadyVoted"][$topic->topic_key] : null ,
                                                                            $vt["configurations"],[],
                                                           (isset($status) || !ONE::isAuth()) ? true : ((isset($vt["disabled"]) && $vt["disabled"])? true : false)) !!}
                                            @elseif( $vt["method"] == "VOTE_METHOD_MULTI" )
                                                {!! Html::oneMultiVoting($topic->topic_key,
                                                                         $cbKey,
                                                                         $vt["key"],
                                                                         (isset($vt["genericConfigurations"]) && array_key_exists("show_total_votes",$vt["genericConfigurations"]) && $vt["genericConfigurations"]["show_total_votes"] == 1) ? (isset($vt["totalVotes"][$topic->topic_key])? $vt["totalVotes"][$topic->topic_key]["positive"] : '0' ): "",
                                                                         !empty($vt["allReadyVoted"][$topic->topic_key]) ? $vt["allReadyVoted"][$topic->topic_key] : null ,
                                                                         $vt["configurations"],[],
                                                           (isset($status) || !ONE::isAuth()) ? true : ((isset($vt["disabled"]) && $vt["disabled"])? true : false)) !!}
                                            @elseif( $vt["method"] == "VOTE_METHOD_LIKE" )
                                                {!! Html::oneLikes($topic->topic_key,
                                                                   $cbKey,
                                                                   $vt["key"],
                                                                   (isset($vt["genericConfigurations"]) && array_key_exists("show_total_votes",$vt["genericConfigurations"]) && $vt["genericConfigurations"]["show_total_votes"] == 1) ? (isset($vt["totalVotes"][$topic->topic_key])? $vt["totalVotes"][$topic->topic_key]["positive"] : '0' ): "",
                                                                   (isset($vt["genericConfigurations"]) && array_key_exists("show_total_votes", $vt["genericConfigurations"]) && $vt["genericConfigurations"]["show_total_votes"] == 1) ? (isset($vt["totalVotes"][$topic->topic_key])? $vt["totalVotes"][$topic->topic_key]["negative"] : '0' ): "",
                                                                   !empty($vt["allReadyVoted"][$topic->topic_key]) ? $vt["allReadyVoted"][$topic->topic_key] : null,
                                                                   $vt["configurations"],[],
                                                           (isset($status) || !ONE::isAuth()) ? true : ((isset($vt["disabled"]) && $vt["disabled"])? true : false)) !!}
                                            @endif
                                        </div>
                                    @endif
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
                @if(!ONE::isAuth() && !empty($voteType))
                    <div class="row">
                        <div class="col-xs-12">
                            <a href="{{ action('AuthController@login') }}">
                                <div class="cb-topics-login-box text-center">
                                    <i class="fa fa-exclamation-circle " aria-hidden="true"></i>
                                    {{ trans("defaultCbsProposal.login_to_vote") }}
                                </div>
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endforeach
@if(!empty($topicsPagination->nextPageUrl()))
    <div class="row">
        <div class="col-xs-12">
            <a class='jscroll-next'
               href='{{ URL::action('PublicCbsController@show', ["cbKey"=> $cb->cb_key, "type"=> $type, 'page' => $topicsPagination->currentPage()+1])}}'>{{ trans("defaultCbsIdea.next") }}</a>
        </div>
    </div>
@endif
