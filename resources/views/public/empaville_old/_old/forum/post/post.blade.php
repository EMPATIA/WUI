@section('header_styles')
    <link href="{{ asset("css/bootstrap-wysihtml5.css") }}" rel='stylesheet' type='text/css'>
@endsection

@extends('public._layouts.index')

@section('content')

    <div class="row">
        <div class="col-md-12">
            {!! ONE::messages() !!}

            <div class="box box-solid box-deflaut">

                <div class="box-header " style="color: #ffffff; background-color: #333333;">
                    <h3 style="padding-top: 5px;display: inline-block;margin: 0;line-height: 1;"><b>{{$topic->title}}</b></h3>

                    @if($isModerator)

                        <div style="float:right">
                            <a  href="{!! action('PublicTopicController@edit', ['id' => isset($topic) ? $topic->id : null, 'cbId' => $topic->cb_id] ) !!}" > <i class="fa fa-pencil" title="Edit or Remove" style="color:white; padding: 8px 8px;"> </i></a>
                        </div>
                    @endif

                </div>


                <div class="box-body">
                    <div class="col-md-2 col-sm-4" style="border-right: 1px solid #62a351;">
                        <div style="text-align: center; height: 230px;padding-top: 15px;">
                            <div style="height: 100px; width: 90px; text-align: center; margin: auto;">
                                @if($usersNames[$topic->created_by]['photo_id'] > 0)
                                    <img style="width: 100%" class="img-circle"
                                         src="https://empatia-test.onesource.pt:5005/file/download/{{$usersNames[$topic->created_by]['photo_id']}}/{{$usersNames[$topic->created_by]['photo_code']}}/1">
                                @else
                                    <img style="width: 100%" class="img-circle" src="/images/icon-user-default-160x160.png">
                                @endif
                            </div>
                            <p>
                                <b style="color: #62a351">{{$usersNames[$topic->created_by]['name']}} </b><br>
                                <small>Member since 2016-03-04</small>
                                <small>Location: Portugal</small>
                            </p>
                        </div>
                    </div>
                    <div class="col-md-10 col-sm-8">
                        <div style="border-bottom: 1px solid #f4f4f4;height: 30px; padding-right: 0px; padding-left: 0px;">
                            <div class="text-muted">
                                @if($topicMessage->created_at != $topicMessage->updated_at)
                                        <b><small><i class="fa fa-info" style="color:red"></i>  Edited at {{$topicMessage->updated_at}}</small></b>
                                @else
                                    <small>Created at {{$topic->created_at}}</small>
                                @endif
                                <div class="text-muted pull-right">
                                    @if(in_array('allow_report_abuse', $configurations))
                                        <button type="button" class="btn btn-box-tool" id="buttonAbuse_{{$topic->id}}" onclick="reportAbuse('buttonAbuse_{{$topic->id}}', '{{$topic->id}}');"  style="color:red;">
                                            <i class="fa fa-warning"></i> Report Abuse
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>


                        <!-- post text -->
                        <div style="padding: 10px;height: 160px;">
                            <p>{!! nl2br($topicMessage->contents) !!}</p>
                        </div>

                        <div class="box-footer clearfix">
                            @if(in_array('allow_likes', $configurations))
                                <div class="pull-left">
                                    <ul class="list-inline" style="margin-bottom:0px;">
                                        <li>
                                            <a href="{!! action('PublicPostController@likePost', [$cbId,  $topicId ,$topicMessage->id]) !!}"
                                               class="link-black text-sm" style="color: #666;">
                                                <i class="fa fa-thumbs-o-up margin-r-5" style="color: #666;"></i>Like
                                            </a>

                                            <a href="{!! action('PublicPostController@dislikePost', [$cbId,  $topicId ,$topicMessage->id]) !!}"
                                               class="link-black text-sm" style="color: #666; padding-left: 10px"><i class="fa fa-thumbs-o-down margin-r-5" style="color: #666;">
                                                </i>Dislike</a>
                                        </li>
                                    </ul>
                                </div>
                            @endif

                            <div class="pull-right">
                            <small class="text-muted">0 likes - 0
                                dislikes</small>
                            </div>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>





            @foreach ($messages as $message)
                <div class="box-footer box-comments" style="border-bottom: 1px solid #f4f4f4;background:white;"  id="post_div_{{$message->id}}">
                    <div class="col-md-2 col-sm-4" >
                        <div style="text-align: center; height: 230px;padding-top: 15px;">
                            <div style="height: 100px; width: 90px; text-align: center; margin: auto;">

                                @if($usersNames[$message->created_by]['photo_id'] > 0)
                                    <img style="width: 100%" class="img-circle"
                                         src="https://empatia-test.onesource.pt:5005/file/download/{{$usersNames[$message->created_by]['photo_id']}}/{{$usersNames[$message->created_by]['photo_code']}}/1">
                                @else
                                    <img style="width: 100%" class="img-circle" src="/images/icon-user-default-160x160.png">
                                @endif


                            </div>
                            <p>
                                <b style="color: #62a351">{{$usersNames[$message->created_by]['name']}} </b><br>
                                <small>Member since 2016-03-04</small>
                                <small>Location: Portugal</small>
                            </p>
                        </div>
                    </div>
                    <div class="col-md-10 col-sm-8" style="border-left: 1px solid #62a351;">
                        <div style="border-bottom: 1px solid #f4f4f4;height: 30px; padding-right: 0px; padding-left: 0px;">
                            <div class="text-muted">

                                @if($message->version != 1)
                                    <b><small style="text-decoration: underline; cursor: pointer" onclick="showHistory({{$message->id}})"><i class="fa fa-info" style="color:red"></i> Edited at {{$message->updated_at}}</small></b>
                                @else
                                    <small>Created at {{$message->created_at}}</small>
                                @endif
                                <div class="text-muted pull-right">

                                    @if(in_array('allow_report_abuse', $configurations))
                                        @if($message->reported_abuse == 0 && $message->created_by != $userKey)
                                            <button type="button" class="btn btn-box-tool" id="buttonAbuse_{{$message->id}}" onclick="reportAbuse('buttonAbuse_{{$message->id}}', '{{$message->id}}');" style="color:red;">
                                                <i class="fa fa-warning"></i> Report Abuse
                                            </button>

                                        @endif
                                    @endif

                                    <button type="button" class="btn btn-box-tool" id='reply_{{$message->id}}' onclick="replyPost('{{$message->id}}',this.id)">
                                        <i class="fa fa-quote-left" title="Reply"></i>
                                    </button>
                                    @if(Session::get('user')->user_key == $message->created_by || $isModerator == 1)
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-box-tool dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fa fa-pencil" title="Edit or Remove"> </i>
                                        </button>

                                        <div class="dropdown-menu  pull-right" style="width: 80px !important; min-width: 80px; max-width: 80px;text-align: center; text-align: center;background-color: #fafafa;font-size: 12px;">
                                            <a class="dropdown-item" style="cursor: pointer" onclick="editPost({{$message->id}})">Edit</a>
                                            <div class="divider"></div>
                                            <a class="dropdown-item" href="javascript:oneDelete('{{ action('PublicPostController@delete', $message->id) }}')">Remove</a>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- post text -->
                        <div style="padding: 10px;min-height: 160px;">

                            @foreach(array_reverse($message->replies) as $reply)
                                <small><b><i class="fa fa-quote-left" title="Reply"></i> {{$usersNames[$reply->created_by]['name']}}</b> in {{$reply->created_at}}</small>
                                <div style="border: 1px solid #dedede; padding: 15px;margin-bottom: 10px;">
                            @endforeach

                            @foreach($message->replies as $reply)
                                    {!! nl2br($reply->contents) !!}
                                </div>
                            @endforeach


                            <div id="post_contents_{{$message->id}}">{!! nl2br($message->contents) !!}</div>
                        </div>

                        <div class="box-footer clearfix">
                            @if(in_array('allow_likes', $configurations))
                                <div class="pull-left">
                                    <ul class="list-inline" style="margin-bottom:0px;">
                                        <li>
                                            @if ($message->liked == 1)
                                                <a id='like_{{$message->id}}'
                                                   onclick="removelikePost('{{$message->id}}', '{{$message->postlike_id}}', 'like_')"
                                                   class="link-black text-sm" style="color:green"><i
                                                            class="fa fa-thumbs-up margin-r-5"></i>Like</a>
                                                <a id='dislike_{{$message->id}}' onclick="dislikePost('{{$message->id}}')"
                                                   class="link-black text-sm"><i class="fa fa-thumbs-o-down margin-r-5"></i>Dislike</a>
                                            @elseif ($message->liked == 0)
                                                <a id='like_{{$message->id}}' onclick="likePost('{{$message->id}}')"
                                                   class="link-black text-sm"><i class="fa fa-thumbs-o-up margin-r-5"></i>Like</a>
                                                <a id='dislike_{{$message->id}}'
                                                   onclick="removelikePost('{{$message->id}}', '{{$message->postlike_id}}', 'dislike_')"
                                                   class="link-black text-sm" style="color:red"><i
                                                            class="fa fa-thumbs-down margin-r-5"></i>Dislike</a>
                                            @else
                                                <a id='like_{{$message->id}}' onclick="likePost('{{$message->id}}')"
                                                   class="link-black text-sm"><i class="fa fa-thumbs-o-up margin-r-5"></i>Like</a>
                                                <a id='dislike_{{$message->id}}' onclick="dislikePost('{{$message->id}}')"
                                                   class="link-black text-sm"><i class="fa fa-thumbs-o-down margin-r-5"></i>Dislike</a>
                                            @endif
                                        </li>
                                    </ul>
                                </div>
                            @endif

                            <div class="pull-right">
                                <small class="text-muted">0 likes - 0
                                    dislikes</small>
                            </div>
                            </ul>
                        </div>
                    </div>
                </div>







            @endforeach

            @if(in_array('allow_comments', $configurations))
                <div class="box-footer" id="new_post" style="margin-top: 20px">
                    <form name="topic" accept-charset="UTF-8" action="" method="POST">
                        <div class="col-md-10 col-sm-8">
                            <!--
                            <div id="wysihtml5-toolbar" style="display: none;">
                                <a data-wysihtml5-command="bold">bold</a>
                                <a data-wysihtml5-command="italic">italic</a>

                                <a data-wysihtml5-command="foreColor" data-wysihtml5-command-value="red">red</a>
                                <a data-wysihtml5-command="foreColor" data-wysihtml5-command-value="green">green</a>
                                <a data-wysihtml5-command="foreColor" data-wysihtml5-command-value="blue">blue</a>


                                <a data-wysihtml5-command="createLink">insert link</a>
                                <div data-wysihtml5-dialog="createLink" style="display: none;">
                                    <label>
                                        Link:
                                        <input data-wysihtml5-dialog-field="href" value="http://" class="text">
                                    </label>
                                    <a data-wysihtml5-dialog-action="save">OK</a> <a data-wysihtml5-dialog-action="cancel">Cancel</a>
                                </div>
                            </div>

                            -->

                            <div style="margin-top: 15px;">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}"/>
                                <input type="hidden" name="parent_id" value="0"/>

                                <textarea type="text" name="contents" id="contents_area" rows="6" class="form-control" placeholder="Enter new post" style="resize: none"></textarea>
                            </div>
                            <div class="box-footer clearfix" style="padding-left: 0px;">
                                <button class="btn btn-sm empatia pull-left btn-flat" type="submit">Submit Post</button>
                            </div>
                        </div>

                        <div class="col-md-2 col-sm-4" style="border-left: 1px solid #62a351;">
                            <div style="text-align: center; height: 200px;padding-top: 35px;">
                                <div style="height: 100px; width: 90px; text-align: center; margin: auto;">
                                    @if(Session::get('user')->photo_id > 0)
                                        <img style="width: 100%" class="img-circle"
                                             src="https://empatia-test.onesource.pt:5005/file/download/{{Session::get('user')->photo_id}}/{{Session::get('user')->photo_code}}/1">
                                    @else
                                        <img style="width: 100%" class="img-circle" src="/images/icon-user-default-160x160.png">
                                    @endif
                                </div>
                                <p>
                                    <b style="color: #62a351">{{Session::get('user')->name}} </b><br>
                                </p>
                            </div>
                        </div>

                    </form>
                </div>
            @endif
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="modalReportAbuse" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Report Abuse</h4>
                </div>
                <div class="modal-body">
                    <div class="radio">
                        <label><input type="radio" class="optradio" name='optradio' checked="checked" id="1">Spam</label>
                    </div>
                    <div class="radio">
                        <label><input type="radio" class="optradio" name='optradio' id="2">Contains hate speech or attacks and
                            individual</label>
                    </div>
                    <div class="radio">
                        <label><input type="radio" class="optradio" name='optradio' id="3">Content not recommended</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <button type="button" class="btn btn-primary" id="buttonSendAbuse">Report</button>
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
                    <h4 class="modal-title" id="myModalLabel">Edit Post</h4>
                </div>
                <div class="modal-body">
                    <textarea type="text" name="contents" id="update_contents_area" rows="6" class="form-control"  style="resize: none"></textarea>


                </div>
                <div class="modal-footer">
                    <input type="hidden" name="_tokenPost" value="{{ csrf_token() }}">
                    <button type="button" style='margin-left:10px;' class="btn btn-primary pull-right col-sm-2 " id="buttonEditPost">Update</button>
                    <button type="button" class="btn btn-default col-sm-2 pull-right" data-dismiss="modal"
                            id="frm_cancel">Close
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
                    <h4 class="modal-title" id="myModalLabel">Post History</h4>
                </div>
                <div class="modal-body" id="post_history">

                </div>
                <div class="modal-footer">
                    <input type="hidden" name="_tokenHistory" value="{{ csrf_token() }}">
                    <button type="button" class="btn btn-default col-sm-2 pull-right" data-dismiss="modal" id="frm_cancel">Close
                    </button>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script src="{{ asset("js/bootstrap-wysihtml5.js") }}"></script>

    <script>
        var postIdReply;

        $( document ).ready(function() {
            postIdReply = 0;


             $(document).keyup(function(e) {
                 if(postIdReply > 0)
                     if (e.keyCode == 27) { // escape key
                         removeReplyPost();
                     }
             });

/*
         var editor = new wysihtml5.Editor("contents_area", { // id of textarea element
                toolbar:      "wysihtml5-toolbar", // id of toolbar element
                parserRules:  wysihtml5ParserRules // defined in parser rules set
            });
            */
        });



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



        function editPost(postId) {
            var post = $("#post_contents_"+postId).html();
            $("#update_contents_area").val(post);

            $('#modalEditPost').modal('show')

            $('#buttonEditPost').on('click', function (evt) {

                var newPost = $("#update_contents_area").val();
                newPost = newPost.replace(/^[ ]+|[ ]+$/g,'');

                if(newPost != ""){
                    $('#buttonEditPost').off();
                    $('#modalEditPost').modal('hide');

                    $.ajax({
                        method: 'POST', // Type of response and matches what we said in the route
                        url: '/public/topic/{{$topic->id}}/post', // This is the url we gave in the route
                        data: {_method:'PUT', postId: postId, contents: newPost, _token: $('input[name=_tokenPost]').val()}, // a JSON object to send back
                        success: function (response) { // What to do if we succeed
                            if(response != 'false'){
                                window.location.href = response;
                            }
                        },
                        error: function (jqXHR, textStatus, errorThrown) { // What to do if we fail
                            console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                        }
                    });
                }else{
                    alert("Enter a valid Post! ");
                }
            });
        }


        function likePost(postId) {
            $.ajax({
                method: 'POST', // Type of response and matches what we said in the route
                url: '/public/forum/post/likePost', // This is the url we gave in the route
                data: {idPost: postId, _token: $('input[name=post_token]').val()}, // a JSON object to send back
                success: function (postLikeId) { // What to do if we succeed


                    $("#like_" + postId).css('color', 'green');
                    $("#like_" + postId).children().removeClass('fa-thumbs-o-up').addClass('fa-thumbs-up');
                    $("#dislike_" + postId).children().removeClass('fa-thumbs-down').addClass('fa-thumbs-o-down');

                    $("#like_" + postId).attr('onclick', 'removelikePost(' + postId + ',' + postLikeId + ', "like_")');
                },
                error: function (jqXHR, textStatus, errorThrown) { // What to do if we fail
                    console.log(JSON.stringify(jqXHR));
                    console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                }
            });

        }


        function dislikePost(postId) {

            $.ajax({
                method: 'POST', // Type of response and matches what we said in the route
                url: '/public/forum/post/dislikePost', // This is the url we gave in the route
                data: {idPost: postId, _token: $('input[name=post_token]').val()}, // a JSON object to send back
                success: function (postLikeId) { // What to do if we succeed
                    $("#dislike_" + postId).css('color', 'red');
                    $("#dislike_" + postId).children().removeClass('fa-thumbs-o-down').addClass('fa-thumbs-down');
                    $("#like_" + postId).children().removeClass('fa-thumbs-up').addClass('fa-thumbs-o-up');
                    $("#dislike_" + postId).attr('onclick', 'removelikePost(' + postId + ',' + postLikeId + ',"dislike_")');

                },
                error: function (jqXHR, textStatus, errorThrown) { // What to do if we fail
                    console.log(JSON.stringify(jqXHR));
                    console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                }
            });

        }

        function removelikePost(postId, postLikeId, button) {

            $.ajax({
                method: 'POST', // Type of response and matches what we said in the route
                url: '/public/forum/post/deleteLike', // This is the url we gave in the route
                data: {idPost: postLikeId, _token: $('input[name=post_token]').val()}, // a JSON object to send back
                success: function (response) { // What to do if we succeed


                    if (button == 'dislike_') {
                        $("#dislike_" + postId).css('color', '#666');
                        $("#dislike_" + postId).children().removeClass('fa-thumbs-down').addClass('fa-thumbs-o-down');
                        $("#dislike_" + postId).attr('onclick', 'dislikePost(' + postId + ')');
                    } else {
                        $("#like_" + postId).css('color', '#666');
                        $("#like_" + postId).children().removeClass('fa-thumbs-up').addClass('fa-thumbs-o-up');
                        $("#like_" + postId).attr('onclick', 'likePost(' + postId + ')');

                    }
                },
                error: function (jqXHR, textStatus, errorThrown) { // What to do if we fail
                    console.log(JSON.stringify(jqXHR));
                    console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
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

        function replyPost(postId, buttonId) {
            postIdReply = postId;
            $('#' + buttonId).hide();
            var htmlReponse = $("#new_post").html();

            //TODO: fix
            htmlReponse = htmlReponse.replace('value="0"', 'value="' + postId + '"');

            $("#post_div_" + postId).append("<div id='new_post_space' style='clear: both;padding-top: 10px;'></div><div id='new_post_replay' style='margin-bottom: 10px; padding-left: 0px; padding-top: 10px;border-top: 1px solid #d2d6de;'>" + htmlReponse + "</div>");
            $("#post_div_" + postId).css('margin', '10px');

            $("#new_post").hide();
        }

        function removeReplyPost(){
            $("#post_div_" + postIdReply).css('margin', '0px');

            $("#new_post_space").remove();
            $("#new_post_replay").remove();

            $("#new_post").show();
            postIdReply = 0;
        }

    </script>
@endsection




