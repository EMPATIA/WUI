@extends('public.empatia._layouts.index')

@section('header_styles')
    <link rel="stylesheet" href="{{ asset('css/empatia/cbs.css')}}">
@endsection

@section('header_scripts')
    <!-- Maps -->
    <script  src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBJtyhsJJX_5DCp59m8sNsPlhHp8aQZHIE" type="text/javascript"></script>
@endsection

@section('content')

    <div class="container">

        <div class="row menus-row">
            <div class="menus-line col-sm-6 col-sm-offset-3">{{$cb->title}}</div>
        </div>


        <div style="background-color: white;padding:10px 20px 10px 20px; margin-top: 40px; margin-bottom: 50px">
            <div class="row">
                <div class="col-xs-12 topicProposalSubtitle">{{$topic->title}}</div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="row">
                        <div class="co-lg-8 col-md-8 col-sm-12 col-xs-12">
                            <div class="row">
                                <div class="col-sm-12 col-xs-12 col-md-12 map">
                                    @if((count($parameters) > 0))
                                        @foreach($parameters as $parameter)
                                            @if($parameter->code == 'google_maps')
                                                {!! Form::oneMaps('mapView',"Maps",isset($parameter->pivot->value)?$parameter->pivot->value : null,["readOnly" => true]) !!}
                                            @endif
                                        @endforeach
                                    @endif
                                </div>

                                <div class="col-sm-12 ideaTopicSummary">
                                    <p>{!! nl2br($topic->contents) !!}</p>
                                </div>

                                <div class="col-sm-12 col-xs-12 col-md-12 forumDescription">
                                    @if($topicMessage != null)
                                        {!! nl2br($topicMessage->contents) !!}
                                    @endif
                                </div>

                                <div class="col-sm-12 col-xs-12 col-md-12 ideaTopicDetails">
                                    <div class="row ideaTopicDetailsRow">
                                        <div class="col-md-6 col-sm-6 col-xs-12 forumTopicDetail">
                                            <p><i class="fa fa-calendar" ></i> {{$topic->created_at}}</p>
                                        </div>
                                        <div class="col-md-6 col-sm-6 col-xs-12 forumTopicDetail">
                                            @if(isset($usersNames[$topic->created_by]))
                                                <p><i class="fa fa-user" ></i> <span class="detailsBold">{{ trans('PublicCbs.guest') }}:</span> {{$usersNames[$topic->created_by]['name']}}</p>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="row ideaTopicDetailsRow">
                                        <div class="col-md-6 col-sm-6 col-xs-12 forumTopicDetail">
                                            @if(isset($usersNames[$topic->created_by]))
                                                <p><i class="fa fa-user" ></i><span class="detailsBold"> {{ trans('PublicCbs.author') }}:</span >{{$usersNames[$topic->created_by]['name']}}</p>
                                            @endif
                                        </div>
                                        <div class="col-md-6 col-sm-6 col-xs-12 forumTopicDetail">
                                            @if(isset($usersNames[$topic->created_by]))
                                                <p><i class="fa fa-user"></i> <span class="detailsBold">{{ trans('PublicCbs.moderator') }}:</span> {{$usersNames[$topic->created_by]['name']}}</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- Files -->
                                @if( isset($filesByType->images) )
                                    <div class="col-sm-12 col-xs-12 col-md-12 cbImages-div">
                                        @foreach($filesByType->images as $fileTmp)
                                            <img class="cbImages" src="{{action('FilesController@download', [$fileTmp->file_id, $fileTmp->file_code] )}}" />&nbsp;&nbsp;
                                        @endforeach
                                    </div>
                                @endif

                                @if( isset($filesByType->videos) )
                                    <div class="col-sm-12 col-xs-12 col-md-12 cbVideos">
                                        @foreach($filesByType->videos as $fileTmp)
                                            <a href="{{action('FilesController@download', [$fileTmp->file_id, $fileTmp->file_code] )}}" > {{ $fileTmp->file_name }} </a>,&nbsp;
                                        @endforeach
                                    </div>
                                @endif

                                @if( isset($filesByType->docs) )
                                    <div class="col-sm-12 col-xs-12 col-md-12 cbFiles">
                                        @foreach($filesByType->docs as $fileTmp)
                                            <a href="{{action('FilesController@download', [$fileTmp->file_id, $fileTmp->file_code] )}}" > {{ $fileTmp->file_name }} </a>&nbsp;
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                            <div class="row">
                                <div class="col-md-12 col-sm-6 col-xs-12 ideaTopicParameter">
                                    @if($isModerator)
                                        <a class="btn btn-default btn-xs"
                                           href="{!! action('PublicTopicController@edit', ['cbKey' => $cbKey,'topicKey' => $topicKey, 'type' => $type, 'f'=> 'topic']) !!}">
                                            <i class="fa fa-pencil"></i>
                                            <span class="hidden-xs">{!! trans('PublicCbs.change') !!}</span>
                                        </a>
                                        <a class="btn btn-default btn-xs" href="javascript:oneDelete('{!! action('PublicTopicController@delete', ['cbKey' => $cbKey,'topicKey' => $topicKey, 'type' => $type]) !!}')" data-toggle="tooltip" data-delay='{"show":"1000"}' data-original-title="Delete">
                                            <i class="fa fa-remove"></i>
                                            {!! trans('PublicCbs.delete') !!}
                                        </a>
                                    @endif
                                </div>
                                @if($parameters>0)
                                    <div class="col-md-12 col-sm-6 col-xs-12 ideaTopicParameter">
                                        <div class="row">
                                            @foreach($parameters as $parameter)
                                                <div class="col-xs-12 col-sm-12 col-md-12 topicsItemParameters">
                                                    @if($parameter->type->code == 'image_map')
                                                        <div class="parameterLabel">{{ trans("PublicCbs.location") }}</div>
                                                    @else
                                                        <div class="parameterTxt">{{ $parameter->parameter }}</div>
                                                    @endif

                                                    @if($parameter->type->code == "dropdown" || $parameter->type->code == "category" || $parameter->type->code == "budget")
                                                        <b>{{$dropDownOptions[$parameter->pivot->value]}}</b>
                                                    @elseif(isset($permissions) &&  array_key_exists("ALLOW-VIDEO-LINK",$permissions ) && $permissions["ALLOW-VIDEO-LINK"] && preg_match('/http:\/\/(www\.)*youtube\.com\/.*/', $parameter->pivot->value ) )
                                                        <iframe title="{{ trans("PublicCbs.youTubeVideoPlayer ") }}" class="youtube-player" type="text/html" width="640" height="390" src="{{$parameter->pivot->value}}" frameborder="0" allowFullScreen></iframe>
                                                    @elseif(isset($permissions) &&  array_key_exists("ALLOW-VIDEO-LINK",$permissions ) && $permissions["ALLOW-VIDEO-LINK"] && preg_match('/http:\/\/(www\.)*vimeo\.com\/.*/',$parameter->pivot->value) )
                                                        <iframe src="{{$parameter->pivot->value}}" width="640" height="390" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
                                                    @else
                                                        <b class="parameterTxt">{{$parameter->pivot->value}}</b>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                                <div class="clearfix visible-sm-block"></div>
                                @if(isset($permissions) && array_key_exists("ALLOW-SHARE", $permissions) && $permissions["ALLOW-SHARE"])
                                    <div class="col-md-12 col-sm-6 col-xs-12 ideaTopicParameter">
                                        <a href="{{ $shareLinks["facebook"]["link"] }}" target="_blank" class="btn-facebook"><i class="fa fa-facebook-square" style="font-size: 1.5em"></i> {{ trans("PublicCbs.share") }}</a>
                                    </div>
                                @endif
                                @if(!empty($voteType))
                                    <div class="col-md-12 col-sm-6 col-xs-12 ideaTopicParameter">
                                        <div class="row">
                                            @foreach($voteType as $vt)
                                                @if( isset($vt["genericConfigurations"]) && $vt['existVotes'] )
                                                    <div class="col-xs-12 discussionTopicVoting">
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
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="backBtn">
                        <a href="{!! action('PublicCbsController@show', [$cbKey, 'type'=> $type] ) !!}" >
                            <span>{{trans('PublicCbs.backButton')}}</span>
                        </a>
                    </div>
                </div>
            </div>


            <div class="row comments-container">
                <div class="col-sm-12 col-xs-12 col-md-12">
                    <div>
                        <div style="float:left"><strong class='commentsTitle'>{{ trans("PublicCbs.comments") }}</strong></div>
                        <div class="totalCommentsNum" style="float:right">{{count($messages)}} {{ trans("PublicCbs.comments") }}</div>
                    </div>
                </div>
                <div class="col-sm-12 col-xs-12 col-md-12" style="margin-top:20px">
                    @foreach ($messages as $message)
                        <div class="box box-widget" id="post_div_{{$message->id}}">
                            <div class="box-header">
                                <div class="user-block">
                                    @if($usersNames[$message->created_by]['photo_id'] > 0)
                                        <img class="img-sm" src="{{URL::action('FilesController@download',[$usersNames[$message->created_by]['photo_id'], $usersNames[$message->created_by]['photo_code'], 1])}}">
                                    @else
                                        <img class="img-sm" src="{{ asset('images/empatia/icon-user-default-160x160.png') }}">
                                    @endif
                                    <span class="username">{{$usersNames[$message->created_by]['name']}}</span>
                                    <span class="createdAt">
                                    {{$message->created_at}}
                                        @if(ONE::isAuth() && Session::has('user') && Session::get('user')->user_key == $message->created_by)
                                            <div class="btn-group">
                                            <button type="button" class="btn btn-box-tool dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="display:inline-flex;font-size:11px;">
                                                <i class="fa fa-pencil" title="Edit or Remove"> </i>
                                            </button>

                                            <div class="dropdown-menu  pull-right"
                                                 style="width: 80px !important; min-width: 80px; max-width: 80px;text-align: center; text-align: center;background-color: #fafafa;font-size: 12px;">
                                                <a class="dropdown-item" style="cursor: pointer"
                                                   onclick="editMessage('{{$cbKey}}','{{$message->post_key}}','{{$type}}')">{{ trans("PublicCbs.edit") }}</a>

                                                <div class="divider"></div>
                                                <a class="dropdown-item"
                                                   href="javascript:oneDelete('{{ action('PublicPostController@delete', ['cbKey' => $cbKey,'topicKey' => $topicKey, 'postKey' => $message->post_key,'type'=>$type]) }}')">Remove</a>
                                            </div>
                                        </div>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <hr style="margin-bottom:10px;margin-top:5px;"/>
                            <div id="post_contents_{{$message->post_key}}" class='post_contents'>{!! nl2br($message->contents) !!}</div>
                        </div>
                    @endforeach

                    @if(ONE::isAuth())
                        @if(ONE::checkCBsOption($configurations, 'ALLOW-COMMENTS'))
                            <div id="new_post">
                                <form name="topic" accept-charset="UTF-8" method="POST" id="new_post_form" onsubmit="return validate('new_post_contents');" action="{{action('PublicPostController@store', ['topicId' => $topic->topic_key])}}">
                                    @if(Session::has('user') && Session::get('user')->photo_id > 0)
                                        <img class="img-responsive img-sm"
                                             src="{{URL::action('FilesController@download',[Session::get('user')->photo_id, Session::get('user')->photo_code, 1])}}">
                                    @else
                                        <img class="img-responsive img-sm" src="{{ asset('images/empatia/icon-user-default-160x160.png') }}">
                                    @endif
                                    <div class="img-push">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}"/>
                                        <input type="hidden" name="parent_id" value="0"/>
                                        <div class="input-group">
                                            <input type="text" id="new_post_contents" name="contents" class="form-control" placeholder="{{ trans("PublicCbs.writeAComment") }}...">
                                            <div class="input-group-btn">
                                                <button type="submit" form="new_post_form" class="btn commentSubmitBtn">{{ trans("PublicCbs.send") }}</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        @endif
                    @endif
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

@section('scripts')
    <script>
        // Validate
        function validate(id) {
            var value = $("#"+id).val();
            if(value.length < 1) {
                return false; // keep form from submitting
            }
            return true;
        }

        // Dot Dot Dot
        $.each([$(".contentPage-heading")], function (index, value) {
            $(document).ready(function () {
                value.dotdotdot({
                    ellipsis: '... ',
                    wrap: 'word',
                    aft: null,
                });
            });
        });
    </script>
    <script>
        var postIdReply;
        $(document).ready(function () {
            $(document).keyup(function (e) {
                if (postIdReply > 0)
                    if (e.keyCode == 27) { // escape key
                        removeReplyPost();
                    }
            });
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
                url: '/public/forum/post/showHistory', // This is the url we gave in the route
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


        function editMessage(cbKey,postKey,type) {
            var post = $("#post_contents_" + postKey).html();
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
                            cbKey: cbKey,
                            postKey: postKey,
                            type: type,
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


        function reportAbuse(id, postId) {
            $('#modalReportAbuse').modal('show')


            $('#buttonSendAbuse').on('click', function (evt) {
                $('#buttonSendAbuse').off();
                // var text = $("input[type='radio']:checked").parent().text();

                $.ajax({
                    method: 'POST', // Type of response and matches what we said in the route
                    url: '/public/forum/post/reportAbuse', // This is the url we gave in the route
                    data: {idPost: postId, type_id: 1, _token: $('input[name=_token]').val()}, // a JSON object to send back
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

        // Form Actions
        function oneDelete(action) {
            var ele = document.getElementById("delete-modal");
            if (ele != null)
                ele.outerHTML="";

            $.get(action, function (data) {
                $('<div id="delete-modal" class="modal fade">' + data + '</div>').modal();
            });
        }
    </script>
@endsection