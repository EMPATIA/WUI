<div class="container">
    <div class="row">
        <div class="col-sm-12 col-xs-12 col-md-12 comments">
            {{ trans("defaultCbs.comments") }}
            @if(ONE::checkCBsOption($configurations, 'TOPIC-COMMENTS-NORMAL'))
                <div class="count-comments">{{count($messages)}} {{ trans("defaultCbs.comments") }}</div>
            @endif
        </div>
    </div>
    <div class="row">
        @if(ONE::checkCBsOption($configurations, 'TOPIC-COMMENTS-NORMAL'))
            @if((count($messages) == 0) && (!$comments))
                <div class="col-xs-12 commentOneMessage">
                    {!! Html::oneMessageInfo(trans("defaultCbs.noCommentsToDisplay") )!!}
                </div>
            @endif
            @foreach ($messages as $message)
                <div class="col-sm-12 col-xs-12 col-md-12">
                    <div class="commentBox" id="post_div_{{$message->id}}">
                        <div class="userData col-xs-12 comment-box">
                            <div class="row">
                                <div class="col-xs-2">
                                    <div class="userImg">
                                        @if(isset($usersNames[$message->created_by]['photo_id']) && ($usersNames[$message->created_by]['photo_id'] > 0))
                                            <img class="img-sm" src="{{URL::action('FilesController@download',[$usersNames[$message->created_by]['photo_id'], $usersNames[$message->created_by]['photo_code'], 1])}}">
                                        @else
                                            <img class="img-sm" src="{{ asset('images/cml/icon-user-default-160x160.png') }}">
                                        @endif
                                    </div>
                                    <div>
                                        @if(isset($usersNames[$message->created_by]))
                                            <div class="username">{{$usersNames[$message->created_by]['name']}}</div>
                                        @endif
                                        <div class="createdAt">
                                            {{$message->created_at}}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xs-9 comment-box-border">
                                    <div class="commentActions">
                                        <div class="commentTitle"></div>
                                        {{--edit and remove--}}
                                        @if(ONE::isAuth() && isset($message->created_by) && Session::has('user') && Session::get('user')->user_key == $message->created_by)
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-box-tool dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="display:inline-flex;font-size:11px;">
                                                    <i class="fa fa-pencil" title="{!! trans('defaultCbs.editOrRemove') !!}"> </i>
                                                </button>
                                                <div class="dropdown-menu  pull-right"
                                                     style="width: 80px !important; min-width: 80px; max-width: 80px;text-align: center; text-align: center;background-color: #fafafa;font-size: 12px;">
                                                    <a class="dropdown-item" style="cursor: pointer"
                                                       onclick="editMessage('{{$cbKey}}','{{$message->post_key}}','{{$type}}')">{{ trans("defaultCbs.edit") }}</a>
                                                    <div class="divider"></div>
                                                    <a class="dropdown-item"
                                                       href="javascript:oneDelete('{{ action('PublicPostController@delete', ['cbKey' => $cbKey,'topicKey' => $topicKey, 'postKey' => $message->post_key,'type'=>$type]) }}')">Remove</a>
                                                </div>
                                            </div>
                                        @endif
                                    <!-- Report abuse -->
                                        @if(!empty(Session::get('user')->user_key) && ONE::checkCBsOption($configurations, 'ALLOW-REPORT-ABUSE') && isset($message->created_by) && $message->created_by != Session::get('user')->user_key)
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-box-tool" id="buttonAbuse_{{$message->post_key}}" onclick="reportAbuse('buttonAbuse_{{$message->post_key}}', '{{$message->post_key}}');" style="color:red;">
                                                    <i class="fa fa-warning"></i> {!! trans('defaultCbs.reportAbuse') !!}
                                                </button>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="commentText">
                                        <div id="post_contents_{{$message->post_key}}">{!! nl2br($message->contents) !!}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
            @if (ONE::checkCBsOption($configurations, 'ALLOW-COMMENTS'))
                @if((ONE::isAuth())|| ONE::checkCBsOption($configurations, 'COMMENTS-ANONYMOUS'))
                    <div class="col-xs-12" style="padding-top: 20px; padding-bottom: 20px;" id="new_post">
                        <form name="topic" accept-charset="UTF-8" method="POST" style="margin-bottom: 0em;" id="new_post_form" onsubmit="return validate('new_post_contents');" action="{{action('PublicPostController@store', ['topicId' => $topic->topic_key])}}">
                            <div class="col-sm-1 hidden-xs">
                                @if(isset(Session::get('user')->photo_id) && Session::get('user')->photo_id > 0)
                                    <img class="img-responsive img-sm"
                                         src="{{URL::action('FilesController@download',[Session::get('user')->photo_id, Session::get('user')->photo_code, 1])}}">
                                @else
                                    <div class="user-default">
                                        <span class="fa fa-user"></span>
                                    </div>
                                @endif
                            </div>
                            <div class="col-sm-11 col-xs-12">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}"/>
                                <input type="hidden" name="parent_id" value="0"/>
                                <div class="input-group commentInput">
                                    <input type="text" id="new_post_contents" name="contents" class="form-control" placeholder="{{trans("PublicCbs.writeComment")}}...">
                                    <div class="input-group-btn">
                                        <button type="submit" form="new_post_form" class="btn btn-flat btn-send">{{ trans("PublicCbs.send") }}</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                @endif
            @endif
        @elseif(ONE::checkCBsOption($configurations, 'TOPIC-COMMENTS-ALL'))
            <div class="">
                <div class="row" style="margin-top: 35px">
                    <div class="col-xs-4">
                        {{--POSITIVE COMMENTS BOX--}}
                        {!! Html::oneComments($configurations, $topic, $positiveComments, $usersNames, $cbKey, $type, $topicKey, 'positive') !!}
                    </div>
                    <div class="col-xs-4">
                        {{--NEUTRAL COMMENTS BOX--}}
                        {!! Html::oneComments($configurations, $topic, $neutralComments, $usersNames, $cbKey, $type, $topicKey, 'neutral') !!}
                    </div>
                    <div class="col-xs-4">
                        {{--NEGATIVE COMMENTS BOX--}}
                        {!! Html::oneComments($configurations, $topic, $negativeComments, $usersNames, $cbKey, $type, $topicKey, 'negative') !!}
                    </div>
                </div>
            </div>
        @elseif(ONE::checkCBsOption($configurations, 'TOPIC-COMMENTS-POSITIVE-NEGATIVE'))
            {{--only positive and negative comments--}}
            <div class="container-fluid">
                <div class="row" style="margin-top: 20px">
                    <div class="col-xs-6">
                        {{--POSITIVE COMMENTS BOX--}}
                        {!! Html::oneComments($configurations, $topic, $positiveComments, $usersNames, $cbKey, $type, $topicKey, 'positive') !!}
                    </div>
                    <div class="col-xs-6">
                        {{--NEGATIVE COMMENTS BOX--}}
                        {!! Html::oneComments($configurations, $topic, $negativeComments, $usersNames, $cbKey, $type, $topicKey, 'negative') !!}
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@if(ONE::checkCBsOption($configurations, 'ALLOW-REPORT-ABUSE'))
    <!-- Modal Report Abuse -->
    <div class="modal fade" id="modalReportAbuse" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">{!! trans('defaultCbs.reportAbuse') !!}</h4>
                </div>
                <div class="modal-body">
                    <div class="radio">
                        <label><input type="radio" class="optradio" name='typeId' checked="checked" id="typeId_1" value="1">
                            <!-- Spam -->
                            {!! trans('defaultCbs.spam') !!}
                        </label>
                    </div>
                    <div class="radio">
                        <label><input type="radio" class="optradio" name='typeId' id="typeId_2" value="2">
                            <!-- Contains hate speech or attacks and individual -->
                            {{ trans("defaultCbs.containsHateSpeechOrAttacks") }}
                        </label>
                    </div>
                    <div class="radio">
                        <label><input type="radio" class="optradio" name='typeId' id="typeId_3" value="3">
                            <!-- Content not recommended -->
                            {!! trans('defaultCbs.contentNotRecommended') !!}
                        </label>
                    </div>
                    <div class="textarea">
                        <label>
                            {!! trans('defaultCbs.comment') !!}
                        </label>
                        <textarea id="reportComment" class="form-control" style="resize: none;"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <button type="button" class="btn btn-primary" id="buttonSendAbuse"><i class="glyphicon glyphicon-bullhorn"></i> {!! trans('defaultCbs.report') !!}</button>
                </div>
            </div>
        </div>
    </div>
@endif

<!-- Modal Edit Post -->
<div class="modal fade" id="modalEditPost" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">{{ trans("defaultCbs.editPost") }}</h4>
            </div>
            <div class="modal-body">
                    <textarea type="text" name="contents" id="update_contents_area" rows="6"
                              class="form-control" style="resize: none"></textarea>
            </div>
            <div class="modal-footer">
                <input type="hidden" name="_tokenPost" value="{{ csrf_token() }}">
                <button type="button" style='margin-left:10px;' class="btn btn-primary pull-right col-sm-2 "
                        id="buttonEditPost">{{ trans("defaultCbs.update") }}
                </button>
                <button type="button" class="btn btn-default col-sm-2 pull-right" data-dismiss="modal"
                        id="frm_cancel">{{ trans("defaultCbs.close") }}
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
                <h4 class="modal-title" id="myModalLabel">{{ trans("defaultCbs.postHistory") }}</h4>
            </div>
            <div class="modal-body" id="post_history">

            </div>
            <div class="modal-footer">
                <input type="hidden" name="_tokenHistory" value="{{ csrf_token() }}">
                <button type="button" class="btn btn-default col-sm-2 pull-right" data-dismiss="modal"
                        id="frm_cancel">{{ trans("defaultCbs.close") }}
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
                            aria-hidden="true">&times;</span><span class="sr-only">{{ trans("defaultCbs.close") }}</span></button>
                <h4 class="modal-title" id="frm_title">{{ trans("defaultCbs.deletePost") }}</h4>
            </div>
            <div class="modal-body">
                {{ trans("defaultCbs.areYouSureYouWantToDeleteThisPost") }}?
            </div>
            <div class="modal-footer">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <button style='margin-left:10px;' type="button" class="btn btn-danger col-sm-2 pull-right"
                        id="buttonDeletePost">{{ trans("defaultCbs.delete") }}
                </button>
                <button type="button" class="btn btn-default col-sm-2 pull-right" data-dismiss="modal"
                        id="frm_cancel">{{ trans("defaultCbs.close") }}
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Dot Dot Dot -->
<script>
    $.each([$(".commentText")], function (index, value) {
        $(document).ready(function () {
            value.dotdotdot({
                after: "a.readmore",
                ellipsis: '',
                wrap: 'word',
                aft: null,
                watch: true,
            });
        });
    });
</script>