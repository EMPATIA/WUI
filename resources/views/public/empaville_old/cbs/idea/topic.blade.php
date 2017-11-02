@extends('public.empaville._layouts.index')

@section('content')

    <div class="row">
        <div class="col-md-12">
            <div class="box box-widget">

                <div class="row proposal">
                    <div class="col-sm-12 col-xs-12 col-md-{{(($posX != "" && $posY != "") || count($files) > 0) ? '8': '12'}}">
                        <div class="proposal information" style="padding: 15px">
                            <div class="title" style="color:#62a351; font-weight: 600; font-size: 24px;">
                                {{$topic->title}}
                            </div>
                            <div style="font-size: 11px"><i class="fa fa-user"
                                                            style="color: #8cc542"></i> {{$usersNames[$topic->created_by]['name']}}
                                &nbsp;&nbsp;<i class="fa fa-calendar" style="color: #8cc542"></i> {{$topic->created_at}}
                                &nbsp;&nbsp;<i class="fa fa-comments-o" style="color: #8cc542"></i> {{count($messages)}}
                                {{ trans('PublicCbs.comments') }}
                            </div>
                            <div class="summary" style=" padding-top: 10px; font-size: 12px">

                                {!! nl2br($topic->contents) !!}
                            </div>

                            <div class="description" style=" padding-top: 20px">

                                @if($topicMessage != null)
                                    {!! nl2br($topicMessage->contents) !!}
                                @endif
                            </div>
                        </div>
                    </div>

                    @if(($posX != "" && $posY != "") || count($files) > 0)
                        <div class="col-sm-12 col-xs-12 col-md-4">

                            @if($posX != "" && $posY != "")
                                <div class="map" style="margin-top: 15px;  font-size: 11px; margin-right: 20px; margin-left: 20px;">
                                    <b>{{trans('empatia.map')}}</b>

                                    <div style="border-top: 1px solid #62a351; padding-top: 5px; text-align: center">
                                        <div id="wrapper"
                                             style="border: 1px solid #d2d6de;  position: relative; width: 100%; height: 200px; overflow: hidden;">
                                            <img id="empaville_map"
                                                 src="{{URL::action('FilesController@download',[$fileId, $fileCode, 1])}}" class="pin" style="max-width:none;">
                                        </div>

                                    </div>
                                </div>
                            @endif
                            @if(count($files) > 0)
                                <div class="gallery" style="margin-top: 15px; font-size: 11px;  margin-right: 20px; margin-left: 20px;">
                                    <b>gallery</b>

                                    <div style="border-top: 1px solid #62a351; padding-top: 5px">
                                        @foreach($files as $file)
                                            <div style="float: left; padding: 5px; padding-left: 0px;">
                                                <a href="{{URL::action('FilesController@download',[$file->file_id, $file->file_code, 1])}}"
                                                   data-lightbox="roadtrip">
                                                    <img class="attachment-img"
                                                         src="{{URL::action('FilesController@download',[$file->file_id, $file->file_code, 1])}}"
                                                         style="height: 80px">
                                                </a>
                                            </div>
                                        @endforeach
                                    </div>
                                    <div style="clear:both;"></div>
                                </div>
                            @endif
                        </div>
                    @endif

                    <div class="col-sm-12 col-xs-12 col-md-12" style="margin-top: 25px;padding-left: 20px; padding-right: 20px">

                        <div class="share col-sm-8 col-xs-8 col-md-8" style="font-size: 11px;display: none;  position: absolute;">
                            <b>{{ trans('PublicCbs.share') }}</b>

                            <div style="border-top: 1px solid #62a351; padding-top: 5px; ">
                                <a class="btn btn-social-icon btn-flat btn-sm btn-facebook disabled"><i
                                            class="fa fa-facebook"></i></a>
                                <a class="btn btn-social-icon btn-flat btn-sm btn-twitter disabled"><i
                                            class="fa fa-flickr"></i></a>
                                <a class="btn btn-social-icon btn-flat btn-sm btn-google disabled"><i
                                            class="fa fa-google-plus"></i></a>
                                <a class="btn btn-social-icon btn-flat btn-sm btn-linkedin disabled"><i
                                            class="fa fa-linkedin"></i></a>
                            </div>
                        </div>
                        @if(!empty($voteType))
                            <div class="vote col-xs-12 col-xs-offset-0  col-sm-6 col-sm-offset-6 col-md-6 col-md-offset-6" style="font-size: 11px; padding-left: 20px;">
                                <b> {{ trans('PublicCbs.votes') }}</b>
                                <div class="row" style="border-top: 1px solid #62a351; padding-top: 5px; margin-bottom: 5px">
                                    @foreach($voteType as $vt)
                                        @if( isset($vt["genericConfigurations"]) && array_key_exists("vote_in_list", $vt["genericConfigurations"]) && $vt["genericConfigurations"]["vote_in_list"] == 1 && $vt['existVotes'] )
                                            <div class="col-xs-12">
                                                @if( $vt["method"] == "VOTE_METHOD_NEGATIVE" )
                                                    {!! Html::oneNegativeVoting($topic->topic_key,
                                                                                $cbKey,$vt["key"],
                                                                                (isset($vt["genericConfigurations"]) && array_key_exists("show_total_votes",$vt["genericConfigurations"]) && $vt["genericConfigurations"]["show_total_votes"] == 1) ? (isset($vt["totalVotes"][$topic->topic_key])? $vt["totalVotes"][$topic->topic_key]["positive"] : '0' ): "",
                                                                                (isset($vt["genericConfigurations"]) && array_key_exists("show_total_votes", $vt["genericConfigurations"]) && $vt["genericConfigurations"]["show_total_votes"] == 1) ? (isset($vt["totalVotes"][$topic->topic_key])? $vt["totalVotes"][$topic->topic_key]["negative"] : '0' ): "",
                                                                                !empty($vt["allReadyVoted"][$topic->topic_key]) ? $vt["allReadyVoted"][$topic->topic_key] : null ,
                                                                                $vt["configurations"]) !!}
                                                @elseif( $vt["method"] == "VOTE_METHOD_MULTI" )
                                                    {!! Html::oneMultiVoting($topic->topic_key,
                                                                             $cbKey,
                                                                             $vt["key"],
                                                                             (isset($vt["genericConfigurations"]) && array_key_exists("show_total_votes",$vt["genericConfigurations"]) && $vt["genericConfigurations"]["show_total_votes"] == 1) ? (isset($vt["totalVotes"][$topic->topic_key])? $vt["totalVotes"][$topic->topic_key]["positive"] : '0' ): "",
                                                                             !empty($vt["allReadyVoted"][$topic->topic_key]) ? $vt["allReadyVoted"][$topic->topic_key] : null ,
                                                                             $vt["configurations"]) !!}
                                                @elseif( $vt["method"] == "VOTE_METHOD_LIKE" )
                                                    {!! Html::oneLikes($topic->topic_key,
                                                                       $cbKey,
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
                                <div class="row">
                                    @if(!empty($voteType))
                                        @foreach($voteType as $vt)
                                            @if($vt['remainingVotes'])
                                                {!! Html::oneVoteInfo($vt['remainingVotes'])!!}
                                            @endif
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        @endif

                        <div class="col-xs-12 col-sm-6 col-md-6" style="margin-top:10px">

                            <div class="buttons">
                                <a href="{{action('PublicCbsController@show', ['cbKey' => $cbKey, 'type' => $type])}}" class="btn btn-flat empatia" style="padding: 3px 12px;">{{trans('empatia.back')}}</a>
                                @if($prevIdea != null)
                                    <a href="{!! action('PublicTopicController@show', ['cbKey' => $cbKey,'topicKey' => $prevIdea, 'type' => $type] ) !!}"  class="btn btn-flat" style="border: 1px solid #62a351"><i class="fa fa-arrow-left"></i> </a>
                                @endif
                                @if($nextIdea != null)
                                    <a href="{!! action('PublicTopicController@show', ['cbKey' => $cbKey,'topicKey' => $nextIdea, 'type' => $type] )  !!}" class="btn btn-flat" style="border: 1px solid #62a351"><i class="fa fa-arrow-right"></i> </a>
                                @endif
                            </div>
                        </div>
                    </div>

                    @if(count($parameters) > 0)
                        <div class="col-xs-12 col-sm-12 col-md-12">
                            <div style="border-top: 1px solid #f4f4f4;padding: 10px; font-size: 11px; margin-top:20px; margin-bottom: 20px">
                                @foreach($parameters as $parameter)
                                    <div class="col-xs-6 col-sm-4 col-md-4">
                                        @if($parameter->type->code == 'image_map')
                                            <div style="text-transform: uppercase; color:#62a351; font-weight:bold; font-size: 12px">{{ trans("PublicCbs.location") }}</div>
                                        @else
                                            <div style="text-transform: uppercase; color:#62a351; font-weight:bold; font-size: 12px">{{$parameter->parameter}}</div>
                                        @endif
                                        <span style="color: #62a351;font-weight:bold;">&#62;</span>

                                        @if($parameter->type->code == "dropdown" || $parameter->type->code == "category" || $parameter->type->code == "budget")
                                            {{$dropDownOptions[$parameter->pivot->value]}}
                                        @elseif($parameter->type->code == 'image_map')
                                            {{ONE::verifyEmpavilleGeoArea($parameter->pivot->value)}}
                                        @else
                                            {{$parameter->pivot->value}}
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                    <div class="col-xs-12 col-sm-12 col-md-12">
                        <div class="pull-right" style="padding: 0px 10px 10px 0px;">
                            @if(ONE::isAuth() && Session::has('user') && Session::get('user')->user_key == $topic->created_by || $isModerator)
                                <a href="{!!  action('PublicTopicController@edit', ['cbKey' => $cbKey, 'topicKey' => $topic->topic_key, 'type' => $type]) !!}" style="cursor: pointer; font-weight: bold; color:#62a351; font-size: 10px;">{{ trans("PublicCbs.editProposal") }}</a>
                            @endif
                        </div>
                    </div>
                </div>

                <div style="display: none">
                    <input id="marker_pos_x" value="{{$posX}}">
                    <input id="marker_pos_y" value="{{$posY}}">
                </div>
            </div>



            @foreach ($messages as $message)
                <div class="box box-widget" style="margin-bottom: 0px;border-bottom: 2px solid #f4f4f4;"
                     id="post_div_{{$message->id}}">
                    <div class="box-header" style="background: #ffffff;">

                        <div class="user-block">
                            @if($usersNames[$message->created_by]['photo_id'] > 0)
                                <img class="img-circle img-sm"
                                     src="{{URL::action('FilesController@download',[$usersNames[$message->created_by]['photo_id'], $usersNames[$message->created_by]['photo_code'], 1])}}">
                            @else
                                <img class="img-circle img-sm" src="{{ asset('images/cml/icon-user-default-160x160.png') }}">
                            @endif
                            <span class="username"
                                  style="margin-left: 40px;margin-top: 10px;"><a>{{$usersNames[$message->created_by]['name']}}</a></span>
                        </div>
                        <div class="box-tools">
                            <div class="text-muted pull-right"><i
                                        class="fa fa-clock-o"></i>
                                <small>{{$message->created_at}}</small>

                                @if(ONE::isAuth() && Session::has('user') && Session::get('user')->user_key == $message->created_by)
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-box-tool dropdown-toggle"
                                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fa fa-pencil" title="Edit or Remove"> </i>
                                        </button>

                                        <div class="dropdown-menu  pull-right"
                                             style="width: 80px !important; min-width: 80px; max-width: 80px;text-align: center; text-align: center;background-color: #fafafa;font-size: 12px;">
                                            <a class="dropdown-item" style="cursor: pointer"
                                               onclick="editMessage({{$message->id}})">{{ trans("PublicCbs.edit") }}</a>

                                            <div class="divider"></div>
                                            <a class="dropdown-item"
                                               href="javascript:oneDelete('{{ action('PublicPostController@delete', ['cbKey' => $cbKey,'topicKey' => $topicKey, 'postKey' => $message->post_key,'type'=>$type]) }}')">Remove</a>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="box-body">
                        <div id="post_contents_{{$message->id}}">{!! nl2br($message->contents) !!}</div>

                        @if(count($message->replies) > 0)
                            <button type="button" class="btn btn-box-tool pull-right text-muted"
                                    style="cursor: pointer; padding-bottom: 0px; font-size: 13px;"
                                    onclick="displayAnswers({{$message->id}})"><i
                                        class="fa fa-comments-o margin-r-5" style="color: #999;"></i><span
                                        id='button_show_{{$message->id}}'>{{ trans("PublicCbs.show") }}</span> {{ trans("PublicCbs.replies") }}<
                                ({{count($message->replies)}})
                            </button>
                        @else
                            <button type="button" class="btn btn-box-tool pull-right text-muted"
                                    style="cursor: pointer; padding-bottom: 0px; font-size: 13px;"
                                    onclick="displayAnswers('{{$message->id}}',1)"><i
                                        class="fa fa-comments-o margin-r-5" style="color: #999;"></i><span
                                        id='button_show_{{$message->id}}'>{{ trans("PublicCbs.reply") }}</span>
                            </button>
                        @endif
                    </div>


                    <div class="box-footer box-comments" id="replies_div_{{$message->id}}"
                         style="padding-left: 40px; display: none;background: #ffffff;">


                        @foreach($message->replies as $reply)

                            <div class="box-comment">
                                <!-- User image -->
                                @if($usersNames[$reply->created_by]['photo_id'] > 0)
                                    <img class="img-circle img-sm"
                                         src="{{URL::action('FilesController@download',[$usersNames[$message->created_by]['photo_id'], $usersNames[$message->created_by]['photo_code'], 1])}}">
                                @else
                                    <img class="img-circle img-sm" src="{{ asset('images/cml/icon-user-default-160x160.png') }}">
                                @endif


                                <div class="comment-text">
                                <span class="username">{{$usersNames[$reply->created_by]['name']}}
                                    <span class="text-muted pull-right">
                                        <small>{{$reply->created_at}}</small>
                                        @if(ONE::isAuth() && Session::has('user') && Session::get('user')->user_key == $message->created_by)
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-box-tool dropdown-toggle"
                                                        data-toggle="dropdown" aria-haspopup="true"
                                                        aria-expanded="false">
                                                    <i class="fa fa-pencil" title="Edit or Remove"> </i>
                                                </button>

                                                <div class="dropdown-menu  pull-right"
                                                     style="width: 80px !important; min-width: 80px; max-width: 80px;text-align: center; text-align: center;background-color: #fafafa;font-size: 12px;">
                                                    <a class="dropdown-item" style="cursor: pointer"
                                                       onclick="editMessage({{$reply->id}})">{{ trans("PublicCbs.edit") }}</a>

                                                    <div class="divider"></div>
                                                    <a class="dropdown-item"
                                                       href="javascript:oneDelete('{{ action('PublicIdeasMessageController@delete', $reply->id) }}')">{{ trans("PublicCbs.remove") }}<</a>
                                                </div>
                                            </div>
                                        @endif
                                    </span>
                                </span><!-- /.username -->
                                    <span id="post_contents_{{$reply->id}}">{!! nl2br($reply->contents) !!}</span>
                                </div>
                                <!-- /.comment-text -->
                            </div>
                        @endforeach
                        @if(count($message->replies) > 0)

                            <div class="box-footer"
                                 style=" padding-left: 0px;padding-top: 20px;padding-right: 0px;padding-bottom: 0px;background: #ffffff; border: none">
                                @else
                                    <div class="box-footer"
                                         style=" padding-left: 0px; padding-right: 0px;padding-bottom: 0px;background: #ffffff; border: none">
                                        @endif

                                        <form name="topic" accept-charset="UTF-8" action="" method="POST">
                                            @if(ONE::isAuth() && Session::has('user') && Session::get('user')->photo_id > 0)
                                                <img class="img-responsive img-circle img-sm"
                                                     src="{{URL::action('FilesController@download',[Session::get('user')->photo_id, Session::get('user')->photo_code, 1])}}">
                                            @else
                                                <img class="img-responsive img-circle img-sm"
                                                     src="{{ asset('images/cml/icon-user-default-160x160.png') }}">
                                            @endif
                                            <div class="img-push">
                                                <input type="hidden" name="_token" value="{{ csrf_token() }}"/>
                                                <input type="hidden" name="parent_id" value="{{$message->id}}"/>
                                                <input type="text" id="contents" name="contents"
                                                       class="form-control input-sm"
                                                       placeholder="Press enter to post a reply...">
                                            </div>
                                        </form>
                                    </div>
                            </div>
                    </div>
                    @endforeach


                    @if(ONE::isAuth() && ONE::checkCBsOption($configurations, 'ALLOW-COMMENTS'))
                        <div class="box-footer" style="margin-top: 20px" id="new_post">
                            <form id="new_post_form" accept-charset="UTF-8" method="POST"
                                  onsubmit="return validate('new_post_contents');" action="{{action('PublicPostController@store', ['topicKey' => $topicKey])}}">
                                @if(Session::get('user')->photo_id > 0)
                                    <img style="width: 100%" class="img-circle"
                                         src="{{URL::action('FilesController@download',[Session::get('user')->photo_id, Session::get('user')->photo_code, 1])}}">
                                @else
                                    <img class="img-responsive img-circle img-sm"
                                         src="{{ asset('images/cml/icon-user-default-160x160.png') }}">
                                @endif
                                <div class="img-push">
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}"/>
                                    <input type="hidden" name="parent_id" value="0"/>
                                    <div class="input-group">
                                        <input type="text" id="new_post_contents" name="contents" class="form-control" placeholder="Write a comment...">
                                        <div class="input-group-btn">
                                            <button type="submit" form="new_post_form" value="Submit" class="btn empatia btn-flat">{{ trans("PublicCbs.send") }}</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    @endif
                </div>
        </div>
    </div>
    </div>


    <!-- Modal Report Abuse -->
    <div class="modal fade" id="modalReportAbuse" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">{!! trans('PublicCbs.reportAbuse') !!}</h4>
                </div>
                <div class="modal-body">
                    <div class="radio">
                        <label><input type="radio" class="optradio" name='typeId' checked="checked" id="typeId_1" value="1"> 
                             <!-- Spam -->
                            {!! trans('PublicCbs.spam') !!}
                        </label>
                    </div>
                    <div class="radio">
                        <label><input type="radio" class="optradio" name='typeId' id="typeId_2" value="2"> 
                            <!-- Contains hate speech or attacks and individual -->
                            {{ trans("PublicCbs.containsHateSpeechOrAttacks") }}
                        </label>
                    </div>
                    <div class="radio">
                        <label><input type="radio" class="optradio" name='typeId' id="typeId_3" value="3"> 
                            <!-- Content not recommended -->
                            {!! trans('PublicCbs.contentNotRecommended') !!}
                        </label>
                    </div>
                    <div class="textarea">
                        <label>
                            {!! trans('PublicCbs.comment') !!}
                        </label>
                        <textarea id="reportComment" class="form-control" style="resize: none;"></textarea>
                    </div>                    
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <button type="button" class="btn btn-primary" id="buttonSendAbuse"><i class="glyphicon glyphicon-bullhorn"></i> {!! trans('PublicCbs.report') !!}</button>
                </div>
            </div>
        </div>
    </div>   


    <!-- Modal Edit Post -->
    <div class="modal fade" id="modalEditPost" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">{{ trans("PublicCbs.editPost") }}</h4>
                </div>
                <div class="modal-body">
                        <textarea type="text" name="contents" id="update_contents_area" rows="6"
                                  class="form-control" style="resize: none"></textarea>


                </div>
                <div class="modal-footer">
                    <input type="hidden" name="_tokenPost" value="{{ csrf_token() }}">
                    <button type="button" style='margin-left:10px;' class="btn btn-primary pull-right col-sm-2 "
                            id="buttonEditPost">{{ trans("PublicCbs.update") }}
                    </button>
                    <button type="button" class="btn btn-default col-sm-2 pull-right" data-dismiss="modal"
                            id="frm_cancel">{{ trans("PublicCbs.close") }}
                    </button>
                </div>
            </div>
        </div>
    </div>


    <!-- Modal Show history Post -->
    <div class="modal fade" id="modalHistoryPost" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">{{ trans("PublicCbs.postHistory") }}</h4>
                </div>
                <div class="modal-body" id="post_history">

                </div>
                <div class="modal-footer">
                    <input type="hidden" name="_tokenHistory" value="{{ csrf_token() }}">
                    <button type="button" class="btn btn-default col-sm-2 pull-right" data-dismiss="modal"
                            id="frm_cancel">{{ trans("PublicCbs.close") }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Confirm Remove -->

    <div id="confirm" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span
                                aria-hidden="true">&times;</span><span class="sr-only">{{ trans("PublicCbs.close") }}</span></button>
                    <h4 class="modal-title" id="frm_title">{{ trans("PublicCbs.deletePost") }}</h4>
                </div>
                <div class="modal-body">
                    {{ trans("PublicCbs.areYouSureYouWantToDeleteThisPost") }}?
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <button style='margin-left:10px;' type="button" class="btn btn-danger col-sm-2 pull-right"
                            id="buttonDeletePost">{{ trans("PublicCbs.delete") }}
                    </button>
                    <button type="button" class="btn btn-default col-sm-2 pull-right" data-dismiss="modal"
                            id="frm_cancel">{{ trans("PublicCbs.close") }}
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('header_styles')
<style>
    #wrapper img, #wrapper .marker {
        position: absolute;
    }

    #wrapper .marker {
        z-index: 100;
        top: 65px;
    }

    #wrapper img {
        top: 0px;
        left: 0;
    }
</style>
@endsection

@section('scripts')
    <link rel="stylesheet" href="/css/lightbox.css">
    <script src="/js/lightbox.js"></script>

    <script>
        var postIdReply;
        $(document).ready(function () {


            $(document).keyup(function (e) {
                if (postIdReply > 0)
                    if (e.keyCode == 27) { // escape key
                        removeReplyPost();
                    }
            });


            var pos_x = $("#marker_pos_x").val();
            var pos_y = $("#marker_pos_y").val();
            var $wrapper = $('#wrapper');

            if (pos_x.length > 0 && pos_y.length > 0) {

                $('#empaville_map').css({
                    left: (-1 * (pos_x * 2.95) + 100),
                    top: (-1 * (pos_y * 2.95) - 100)
                });

                var boxHeight = $wrapper.height();
                var boxWidth = $wrapper.width();

                $('<img id="map_pin" src="/images/map_pin.png">').addClass('marker').css({
                    left: (boxWidth / 2) - 20,
                    top: (boxHeight / 2) - 65
                }).appendTo($wrapper);
            }

        });


        function validate(id) {
            var value = $("#"+id).val();

            if(value.length < 1) {
                return false; // keep form from submitting
            }
            return true;
        }

        function displayAnswers(postId, hide) {
            if ($("#replies_div_" + postId).css('display') == 'none') {
                $("#replies_div_" + postId).show();
                $("#post_div_" + postId).css('margin-bottom', '10px');
                $('#button_show_' + postId).html('Hide');

            } else {
                $("#replies_div_" + postId).hide();
                $("#post_div_" + postId).css('margin-bottom', '0px');
                if (hide != undefined)
                    $('#button_show_' + postId).html('Replay');
                else
                    $('#button_show_' + postId).html('Show');


            }
        }


        function showHistory(postId) {
            $('#modalHistoryPost').modal('show')

            $.ajax({
                method: 'POST', // Type of response and matches what we said in the route
                url: '{{action("PublicPostController@showHistory")}}', // This is the url we gave in the route
                data: {postId: postId, _token: $('input[name=_tokenHistory]').val()}, // a JSON object to send back
                success: function (response) { // What to do if we succeed
                    console.log(response);
                    $("#post_history").html(response);
                },
                error: function (jqXHR, textStatus, errorThrown) { // What to do if we fail
                    console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                }
            });
        }


        function editMessage(postId) {
            var post = $("#post_contents_" + postId).html();
            //remove spaces at end
            post = post.replace(/^\s\s*/, '').replace(/\s\s*$/, '');

            $("#update_contents_area").val(post);

            $('#modalEditPost').modal('show')

            $('#buttonEditPost').on('click', function (evt) {

                var newPost = $("#update_contents_area").val();
                newPost = newPost.replace(/^[ ]+|[ ]+$/g, '');

                if (newPost != "") {
                    $('#buttonEditPost').off();
                    $('#modalEditPost').modal('hide');

                    $.ajax({
                        method: 'POST', // Type of response and matches what we said in the route
                        url: '{!!action('PublicPostController@update', ['topicKey' => $topicKey])!!}', // This is the url we gave in the route
                        data: {
                            _method: 'PUT',
                            postId: postId,
                            contents: newPost,
                            _token: $('input[name=_tokenPost]').val()
                        }, // a JSON object to send back
                        success: function (response) { // What to do if we succeed
                            if (response != 'false') {
                                window.location.href = response;
                            }
                            console.log(response);
                        },
                        error: function (jqXHR, textStatus, errorThrown) { // What to do if we fail
                            console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                        }
                    });
                } else {
                    alert("Enter a valid Post! ");
                }
            });
        }


        function reportAbuse(id, postKey) {
            $('#modalReportAbuse').modal('show')
            $('#buttonSendAbuse').on('click', function (evt) {
                $('#buttonSendAbuse').off();
                // var text = $("input[type='radio']:checked").parent().text();

                $.ajax({
                    method: 'POST', // Type of response and matches what we said in the route
                    url: '{{action('PublicPostController@reportAbuse')}}', // This is the url we gave in the route
                    data: {post_key: postKey, 
                           type_id: $('input[name=typeId]:checked').val(), 
                           comment:  $("#reportComment").val(),
                           _token: $('input[name=_token]').val()}, // a JSON object to send back
                    success: function (response) { // What to do if we succeed
                        console.log(response);
                        $('#modalReportAbuse').modal('hide');
                        $('#' + id).hide();
                    },
                    error: function (jqXHR, textStatus, errorThrown) { // What to do if we fail
                        console.log(JSON.stringify(jqXHR));
                        console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                    }
                });
            });
        }

        function removeReplyPost() {
            $('#reply_' + postIdReply).show();

            $("#post_div_" + postIdReply).css('margin', '0px');
            $("#post_div_" + postIdReply).css('padding', '0px');

            $("#new_post_space").remove();
            $("#new_post_replay").remove();

            $("#new_post").show();
            postIdReply = 0;
        }


        function vote(topicId, value) {

            $.ajax({
                method: 'POST', // Type of response and matches what we said in the route
                url: '{{action('PublicTopicController@vote', $cbKey)}}', // This is the url we gave in the route
                data: {
                    id: topicId,
                    value: value,
                    voteKey: '{{$voteKey}}',
                    _token: $('input[name=_token]').val()
                }, // a JSON object to send back
                success: function (responseVOte) { // What to do if we succeed


                    toastr.options = {
                        "closeButton": true,
                        "debug": false,
                        "newestOnTop": false,
                        "progressBar": false,
                        "positionClass": "toast-bottom-right",
                        "preventDuplicates": true,
                        "onclick": null,
                        "showDuration": "300",
                        "hideDuration": "1000",
                        "timeOut": "4000",
                        "extendedTimeOut": "1000",
                        "showEasing": "swing",
                        "hideEasing": "linear",
                        "showMethod": "fadeIn",
                        "hideMethod": "fadeOut"
                    };


                    if (responseVOte.indexOf("error") == -1) {
                        var response = JSON.parse(responseVOte);
                        var vote = response['vote'];

                        if (vote == '-1') {
                            $("#plus_button").css('border', '1px solid #8cc542');
                            $("#plus_button").css('background-color', '');
                            $("#vote_plus").css("color", "#8cc542");


                            $("#minus_button").css('background-color', '#f74553');
                            $("#vote_minus").css("color", "white");
                        } else if (vote == 1) {
                            $("#minus_button").css('border', '1px solid #f74553');
                            $("#minus_button").css('background-color', '');
                            $("#vote_minus").css("color", "#f74553");

                            $("#plus_button").css('background-color', '#8cc542');
                            $("#vote_plus").css("color", "white");
                        } else if (vote == '0') {
                            $("#plus_button").css('border', '1px solid #8cc542');
                            $("#plus_button").css('background-color', '');
                            $("#vote_plus").css("color", "#8cc542");

                            $("#minus_button").css('border', '1px solid #f74553');
                            $("#minus_button").css('background-color', '');

                            $("#vote_minus").css("color", "#f74553");
                        }

                        $("#negative-votes-info").html(response['negative']);
                        $("#total-votes-info").html(response['total']);


                        toastr.info("{{trans('empaville.remainingVotes')}}: " + response['total'] + ". {{trans('empaville.youCanUse')}}" + response['negative'] + " {{trans('empaville.negativeVotes')}}.");
                    }else{
                        var response = JSON.parse(responseVOte);
                        var message = response['error'];

                        toastr.error(message);
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) { // What to do if we fail

                    console.log(JSON.stringify(jqXHR));
                    console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                }
            });

        }


    </script>
@endsection




