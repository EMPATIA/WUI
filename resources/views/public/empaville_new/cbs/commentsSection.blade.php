<div class="container">


    <div class="row">
        <div class="col-xs-12 comments">
            {{ trans("defaultPadsCommentsSection.comments") }}
            @if(ONE::checkCBsOption($configurations, 'TOPIC-COMMENTS-NORMAL'))
                <div class="count-comments">{{count($messages)}} {{ trans("defaultPadsCommentsSection.comments") }}</div>
            @endif
        </div>
    </div>
    <div class="row" style="margin-top: 15px">
        @if(ONE::checkCBsOption($configurations, 'TOPIC-COMMENTS-NORMAL'))
            @if((count($messages) == 0) && (!$comments))
                <div class="col-xs-12 noCommentsToShow-info">
                    {!! Html::oneMessageInfo(trans("defaultPadsCommentsSection.noCommentsToDisplay") )!!}
                </div>
            @endif
            {{--NORMAL COMMENTS BOX--}}
            {!! Html::oneCommentsNormal($messages, $usersNames, $cbKey, $type, $topicKey, $configurations, $isModerator) !!}

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
                    <h4 class="modal-title" id="myModalLabel">{!! trans('defaultPadsCommentsSection.reportAbuse') !!}</h4>
                </div>
                <div class="modal-body">
                    <div class="radio">
                        <label><input type="radio" class="optradio" name='typeId' checked="checked" id="typeId_1" value="1">
                            <!-- Spam -->
                            {!! trans('defaultPadsCommentsSection.spam') !!}
                        </label>
                    </div>
                    <div class="radio">
                        <label><input type="radio" class="optradio" name='typeId' id="typeId_2" value="2">
                            <!-- Contains hate speech or attacks and individual -->
                            {{ trans("defaultPadsCommentsSection.containsHateSpeechOrAttacks") }}
                        </label>
                    </div>
                    <div class="radio">
                        <label><input type="radio" class="optradio" name='typeId' id="typeId_3" value="3">
                            <!-- Content not recommended -->
                            {!! trans('defaultPadsCommentsSection.contentNotRecommended') !!}
                        </label>
                    </div>
                    <div class="textarea">
                        <label>
                            {!! trans('defaultPadsCommentsSection.comment') !!}
                        </label>
                        <textarea id="reportComment" class="form-control" style="resize: none;"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <button type="button" class="btn btn-primary" id="buttonSendAbuse"><i class="glyphicon glyphicon-bullhorn"></i> {!! trans('defaultPadsCommentsSection.report') !!}</button>
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
                <h4 class="modal-title" id="myModalLabel">{{ trans("defaultPadsCommentsSection.editPost") }}</h4>
            </div>
            <div class="modal-body">
                    <textarea type="text" name="contents" id="update_contents_area" rows="6"
                              class="form-control" style="resize: none"></textarea>
            </div>
            <div class="modal-footer">
                <input type="hidden" name="_tokenPost" value="{{ csrf_token() }}">
                <button type="button" style='margin-left:10px;' class="btn btn-primary pull-right col-sm-2 "
                        id="buttonEditPost">{{ trans("defaultPadsCommentsSection.update") }}
                </button>
                <button type="button" class="btn btn-default col-sm-2 pull-right" data-dismiss="modal"
                        id="frm_cancel">{{ trans("defaultPadsCommentsSection.close") }}
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
                <h4 class="modal-title" id="myModalLabel">{{ trans("defaultPadsCommentsSection.postHistory") }}</h4>
            </div>
            <div class="modal-body" id="post_history">

            </div>
            <div class="modal-footer">
                <input type="hidden" name="_tokenHistory" value="{{ csrf_token() }}">
                <button type="button" class="btn btn-default col-sm-2 pull-right" data-dismiss="modal"
                        id="frm_cancel">{{ trans("defaultPadsCommentsSection.close") }}
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
                            aria-hidden="true">&times;</span><span class="sr-only">{{ trans("defaultPadsCommentsSection.close") }}</span></button>
                <h4 class="modal-title" id="frm_title">{{ trans("defaultPadsCommentsSection.deletePost") }}</h4>
            </div>
            <div class="modal-body">
                {{ trans("defaultPadsCommentsSection.areYouSureYouWantToDeleteThisPost") }}?
            </div>
            <div class="modal-footer">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <button style='margin-left:10px;' type="button" class="btn btn-danger col-sm-2 pull-right"
                        id="buttonDeletePost">{{ trans("defaultPadsCommentsSection.delete") }}
                </button>
                <button type="button" class="btn btn-default col-sm-2 pull-right" data-dismiss="modal"
                        id="frm_cancel">{{ trans("defaultPadsCommentsSection.close") }}
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