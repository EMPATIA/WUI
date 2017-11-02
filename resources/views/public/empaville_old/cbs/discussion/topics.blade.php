@extends('public.empaville._layouts.index')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-header " style="color: #ffffff; background-color: #333333;">
                    <h3 style="padding-top: 5px;display: inline-block;margin: 0;line-height: 1;"><i class="fa fa-comments"></i>{!! trans('PublicCbs.discussionTopics') !!}</h3>

                    @if($isModerator)
                        <div class="box-tools pull-right" style="top: 2px;">
                            <a href="{!! action('PublicTopicController@create', $cbId) !!}"
                               class="btn btn-success btn">{{ trans('topic.create') }}</a>
                        </div>
                    @endif
                </div>
                <div class="box-body" style="margin-top: 10px;padding-bottom: 20px;">
                    @if(count($ideas) > 0)
                        <div class="col-sm-8 col-md-12">
                            <div class="box" style="min-height: 90px;margin-bottom: 0px; border-top-color: #737373;">
                                <table  class="table table-bordered">
                                    <tbody>
                                    @foreach ($ideas as $topic)
                                        <tr style="">
                                            <td style="text-align: center;width: 6%;padding: 10px;vertical-align: middle;">

                                                <a>
                                                    <i style="color:#3c8dbc;" class="fa fa-comment-o fa-2x"></i>
                                                </a>

                                            </td>
                                            <td style="padding: 10px;">
                                                <a class="subject" href="{!! action('PublicTopicController@show', ['cbId'=> $cbId, 'topicId' => $topic->id, 'type' => $type] ) !!}" style="font-size: 16px;font-weight: bold;">{{ $topic->title }}</a>

                                                <p style="padding-top: 5px;word-wrap: break-word; text-overflow: ellipsis; overflow: hidden;height: 40px;">{!! trans('PublicCbs.createdBy') !!} <b><a  href="{!! action('PublicUsersController@show', $topic->created_by ) !!}" >{{ $usersNames[$topic->created_by]['name']}} </a></b></p>
                                            </td>
                                            <td style="width: 15%;text-align: right;padding: 10px;vertical-align: middle;">
                                                <p>
                                                    {{$topic->statistics->posts_counter}} {!! trans('PublicCbs.messages') !!}
                                                    <br>{{$topic->statistics->like_counter}} {!! trans('PublicCbs.likes') !!}
                                                    <br>{{$topic->statistics->dislike_counter}} {!! trans('PublicCbs.dislikes') !!}
                                                </p>
                                            </td>
                                            <td style="width: 20%;padding: 10px;vertical-align: middle; text-align: center; position: relative">
                                                @if($isModerator)
                                                    <div style="position: absolute; right: 10px; top: 5px">
                                                        <a  href="javascript:oneDelete('{!! action('PublicTopicController@delete',  ['cbId'=> $cbId, 'topicId' => $topic->id] ) !!}')">
                                                            <i style="color:red;" class="fa fa-remove"></i>
                                                        </a>
                                                    </div>
                                                @endif
                                                <p><strong><a href="{{ action('PublicUsersController@show', $topic->last_post->created_by) }}">{{$usersNames[$topic->last_post->created_by]['name']}} </a></strong><br>
                                                    {{ date('M d, Y H:i:s', strtotime($topic->last_post->updated_at))}}
                                                </p>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                                @if(!empty($voteType))
                                    <div class="col-xs-12" style="border-bottom: 1px solid lightgrey; padding-top: 5px; margin-bottom: 5px">
                                        @foreach($voteType as $vt)
                                            @if( isset($vt["genericConfigurations"]) && array_key_exists("vote_in_list", $vt["genericConfigurations"]) && $vt["genericConfigurations"]["vote_in_list"] == 1 && $vt['existVotes'])
                                                <div class="col-lg-4 col-md-6 col-xs-12">
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
                                    </div>
                                @endif
                            </div>
                        </div>
                    @else
                        <div class="col-sm-8 col-md-12">
                            <div class="alert alert-warning">
                                <h4><i class="icon fa fa-warning"></i> {!! trans('PublicCbs.alert') !!}</h4>
                                <p>{!! trans('PublicCbs.noTopics') !!}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection