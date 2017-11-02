
@foreach ($topicsPagination as $topic)
    <div class="col-lg-4 col-md-6 col-xs-12 cbs-boxes-padding">
        <!-- Files -->
        @if(!empty($filesByType) && count($filesByType) >0 && isset($filesByType[$topic->topic_key]->images) && !empty(reset($filesByType[$topic->topic_key]->images)) )
            <div class="col-xs-12 news-inner-img-div" style="background-image:url('{{ action('FilesController@download', [reset($filesByType[$topic->topic_key]->images)->file_id, reset($filesByType[$topic->topic_key]->images)->file_code, 1])}}')">
            </div>
        @else
            <div class="col-xs-12 news-inner-img-div" style="background-image:url('{{ url('/images/empatia/default_img_contents.jpg')}}')">
            </div>
        @endif
        <div class="col-xs-12 cbs-box">
            <div class="row">
                <div class="col-xs-12 text-right">
                    <i class="glyphicon glyphicon-time" aria-hidden="true"></i>
                    {{\Carbon\Carbon::now()->diffInDays(\Carbon\Carbon::parse($topic->created_at)).' '.trans('defaultCbsProposal.days')}}

                </div>
            </div>

            <div class="row topic-contents">
                <div class="col-xs-12 topic-title">
                    <h4>
                        <a href="{!! action('PublicTopicController@show', [$cb->cb_key , $topic->topic_key, 'type' => $type] ) !!}">{{$topic->title}}</a>
                    </h4>
                </div>

                <div class="col-xs-12 topic-content">
                    <p>{{ $topic->contents }}</p>
                </div>
                <div class="col-xs-12 padding-box-default">
                    <span class="glyphicon glyphicon-user"></span> {{ $usersNames->{$topic->created_by}->name ?? trans('defaultCbsProposal.anonymous')}}
                    @if(ONE::checkCBsOption($configurations, 'ALLOW-COMMENTS'))
                        <span class="glyphicon glyphicon-comment"></span> {{$topic->statistics->posts_counter ?? 0}}
                    @endif
                </div>

                @foreach($topic->parameters as $parameter)
                    @if($parameter->visible_in_list)
                        <div class="col-xs-12">
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
                                    <iframe title="YouTube video player" class="youtube-player" type="text/html" width="640" height="390" src="{{$parameter->pivot->value}}" frameborder="0" allowFullScreen></iframe>
                                @elseif(isset($permissions) &&  array_key_exists("ALLOW-VIDEO-LINK",$permissions ) && $permissions["ALLOW-VIDEO-LINK"] && preg_match('/http:\/\/(www\.)*vimeo\.com\/.*/',$parameter->pivot->value) )
                                    <iframe src="{{$parameter->pivot->value}}" width="640" height="390" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
                                @else
                                    {{$parameter->pivot->value}}
                                @endif
                            @endif
                        </div>
                    @endif
                @endforeach

                @if(!empty($voteType))
                    @foreach($voteType as $vt)
                        @if( isset($vt["genericConfigurations"]) && array_key_exists("vote_in_list", $vt["genericConfigurations"]) && $vt["genericConfigurations"]["vote_in_list"] == 1 && $vt['existVotes'])
                            <div class="col-xs-12 padding-box-default">
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
                        @endif
                    @endforeach
                @endif
            </div>
            <div class="row">

                <a href="{!! action('PublicTopicController@show', [$cb->cb_key , $topic->topic_key, 'type' => $type] ) !!}">
                    <div class="col-xs-12 view-more-btn text-center">
                        {{trans("defaultCbsProposal.view_more")}} <span class="glyphicon glyphicon-chevron-right"></span>
                    </div>
                </a>
            </div>
        </div>
    </div>
@endforeach
@if(!empty($topicsPagination->nextPageUrl()))
    <div class="row">
        <div class="col-xs-12">
            <a class='jscroll-next' href='{{ URL::action('PublicCbsController@show', ["cbKey"=> $cb->cb_key, "type"=> $type, 'page' => $topicsPagination->currentPage()+1])}}'>{{ trans("defaultCbsProposal.next") }}</a>
        </div>
    </div>
@endif