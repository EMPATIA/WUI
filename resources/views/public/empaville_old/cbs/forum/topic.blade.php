@section('header_styles')
    <link href="{{ asset("css/bootstrap-wysihtml5.css") }}" rel='stylesheet' type='text/css'>
@endsection

@extends('public.empaville._layouts.index')

@section('content')

    <div class="row">
        <div class="col-md-12">
            <div class="box box-widget">
                <div class="box-header with-border" style="background-color: #3c8dbc; color: #ffffff;">
                    <div class="box-title">
                        <b>{{$topic->title}}</b>
                    </div>

                    @if($isModerator)
                        <div class="text-muted pull-right" style="color: #ffffff;">
                            <a  href="{!! action('PublicTopicController@edit', ['cbKey' => $cbKey, 'topicKey' => $topic->topic_key, 'type' => $type]) !!}">
                                <i class="fa fa-pencil" title="Edit or Remove" style="color:white"> </i>
                            </a>
                        </div>
                    @endif
                </div>

                <div class="box-body">
                    <div class="col-md-2 col-sm-4">
                        <div style="text-align: center; height: 210px;padding-top: 15px;">
                            <div style="height: 100px; width: 90px; text-align: center; margin: auto;">
                                @if($usersNames[$topic->created_by]['photo_id'] > 0)
                                    <img style="width: 100%" class="img-circle"  src="{{URL::action('FilesController@download',[$usersNames[$topic->created_by]['photo_id'], $usersNames[$topic->created_by]['photo_code'], 1])}}">

                                @else
                                    <img style="width: 100%" class="img-circle" src="{{ asset('images/default/icon-user-default-160x160.png') }}">
                                @endif
                            </div>
                            <p>
                                <b style="color: #3c8dbc">{{$usersNames[$topic->created_by]['name']}} </b><br>
                                <small>{!! trans('PublicCbs.memberSince') !!}</small>
                                <small>{!! trans('PublicCbs.location') !!}</small>
                            </p>
                        </div>
                    </div>
                    <div class="col-md-10 col-sm-8" style="border-left: 1px solid #3c8dbc;">
                        <div style="border-bottom: 1px solid #f4f4f4;height: 30px; padding-right: 0px; padding-left: 0px;">
                            <div class="text-muted">
                                @if($topicMessage->version != 1)
                                    <b><small style="text-decoration: underline; cursor: pointer" onclick="showTopicHistory('{{$topicMessage->post_key}}')"><i class="fa fa-info" style="color:red"></i> {!! trans('PublicCbs.editedAt') !!} {{$topicMessage->updated_at}}</small></b>

                                @else
                                    <small>{!! trans('PublicCbs.createdBy') !!} {{$topic->created_at}}</small>
                                @endif
                                <div class="text-muted pull-right">
                                    @if(ONE::checkCBsOption($configurations, 'ALLOW-REPORT-ABUSE'))
                                        <button type="button" class="btn btn-box-tool" id="buttonAbuse_{{$topic->id}}" onclick="reportAbuse('buttonAbuse_{{$topic->id}}', '{{$topic->id}}');"  style="color:red;">
                                            <i class="fa fa-warning"></i> {!! trans('PublicCbs.reportAbuse') !!}
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>


                        <!-- post text -->
                        <div style="padding: 10px;min-height: 210px;">
                            <p>{!! nl2br($topicMessage->contents) !!}</p>
                        </div>
                        <div id="topicHistory" style="display:none; padding-top:20px;">
                            <div class="box-header with-border" style=" color: #ffffff;">
                                <div class="box-title">
                                    <b>Topic Historiy</b>
                                </div>
                                <div class="text-muted pull-right">
                                    <a  href="javascript:hideTopicHistory()"><i class="fa fa-close" title="Close" style="color:black"> </i>
                                    </a>
                                </div>


                            </div>
                            <div id="topicHistoryContents">

                            </div>
                        <div>

                        <div class="box-footer">
                            @if(!empty($voteType))
                                <div class="row">
                                    @foreach($voteType as $vt)
                                        @if( isset($vt["genericConfigurations"]) && array_key_exists("vote_in_list", $vt["genericConfigurations"]) && $vt["genericConfigurations"]["vote_in_list"] == 1 && $vt['existVotes'])
                                            <div class="col-lg-4 col-md-6 col-xs-12">
                                                @if( $vt["method"] == "VOTE_METHOD_NEGATIVE")
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
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            @foreach ($messages as $message)
                <div class="box-footer box-comments" style="border-bottom: 1px solid #f4f4f4;background:white;"  id="post_div_{{$message->id}}">
                    <div class="col-md-2 col-sm-4" style="border-right: 1px solid #3c8dbc;">
                        <div style="text-align: center; height: 230px;padding-top: 15px;">
                            <div style="height: 100px; width: 90px; text-align: center; margin: auto;">

                                @if($usersNames[$message->created_by]['photo_id'] > 0)
                                    <img style="width: 100%" class="img-circle"  src="{{URL::action('FilesController@download',[$usersNames[$topic->created_by]['photo_id'], $usersNames[$topic->created_by]['photo_code'], 1])}}">

                                @else
                                    <img style="width: 100%" class="img-circle" src="{{ asset('images/default/icon-user-default-160x160.png') }}">
                                @endif


                            </div>
                            <p>
                                <b style="color: #3c8dbc">{{$usersNames[$message->created_by]['name']}} </b><br>
                                <small>{!! trans('PublicCbs.memberSince') !!}</small>
                                <small>{!! trans('PublicCbs.location') !!}</small>
                            </p>
                        </div>
                    </div>
                    <div class="col-md-10 col-sm-8">
                        <div style="border-bottom: 1px solid #f4f4f4;height: 30px; padding-right: 0px; padding-left: 0px;">
                            <div class="text-muted">

                                @if($message->version != 1)
                                    <b><small style="text-decoration: underline; cursor: pointer" onclick="showHistory('{{$message->post_key}}')"><i class="fa fa-info" style="color:red"></i> {!! trans('PublicCbs.editedAt') !!} {{$message->updated_at}}</small></b>
                                @else
                                    <small>{!! trans('PublicCbs.createdAt') !!} {{$message->created_at}}</small>
                                @endif
                                <div class="text-muted pull-right">

                                    @if(ONE::isAuth())
                                        @if(ONE::checkCBsOption($configurations, 'ALLOW-REPORT-ABUSE'))
                                            @if($message->created_by != ONE::getUserKey())
                                                <button type="button" class="btn btn-box-tool" id="buttonAbuse_{{$message->id}}" onclick="reportAbuse('buttonAbuse_{{$message->id}}', '{{$message->id}}');" style="color:red;">
                                                    <i class="fa fa-warning"></i> {!! trans('PublicCbs.reportAbuse') !!}
                                                </button>

                                            @endif
                                        @endif
                                    @endif

                                    <button type="button" class="btn btn-box-tool" id='reply_{{$message->id}}' onclick="replyPost('{{$message->id}}',this.id)">
                                        <i class="fa fa-quote-left" title="Reply"></i>
                                    </button>
                                    @if(ONE::getUserKey() == $message->created_by || $isModerator == 1)
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-box-tool dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="fa fa-pencil" title="Edit or Remove"> </i>
                                            </button>

                                            <div class="dropdown-menu  pull-right" style="width: 80px !important; min-width: 80px; max-width: 80px;text-align: center; text-align: center;background-color: #fafafa;font-size: 12px;">
                                                <a class="dropdown-item" style="cursor: pointer" onclick="editPost('{{$message->post_key}}')">Edit</a>
                                                <div class="divider"></div>
                                                <a class="dropdown-item" href="javascript:oneDelete('{{ action('PublicPostController@delete', ['cbKey' => $cbKey,'topicKey' => $topicKey, 'postKey' => $message->post_key,'type'=>$type]) }}')">{!! trans('PublicCbs.remove') !!}</a>
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
                                        {{$reply->contents}}
                                </div>
                            @endforeach


                            <div id="post_contents_{{$message->post_key}}">  {{$message->contents}}</div>
                        </div>

                        <div class="box-footer clearfix">
                            @if(ONE::checkCBsOption($configurations, 'ALLOW-LIKES'))
                                <div class="pull-left">
                                    <ul class="list-inline" style="margin-bottom:0px;">
                                        <li>
                                            {{--
                                            @if ($message->liked == 1)
                                                <a id='like_{{$message->id}}'
                                                   onclick="removelikePost('{{$message->id}}', '{{$message->postlike_id}}', 'like_')"
                                                   class="link-black text-sm" style="color:green"><i
                                                            class="fa fa-thumbs-up margin-r-5"></i>{!! trans('PublicCbs.like') !!}</a>
                                                <a id='dislike_{{$message->id}}' onclick="dislikePost('{{$message->id}}')"
                                                   class="link-black text-sm"><i class="fa fa-thumbs-o-down margin-r-5"></i>{!! trans('PublicCbs.dislike') !!}</a>
                                            @elseif ($message->liked == 0)
                                                <a id='like_{{$message->id}}' onclick="likePost('{{$message->id}}')"
                                                   class="link-black text-sm"><i class="fa fa-thumbs-o-up margin-r-5"></i>{!! trans('PublicCbs.like') !!}</a>
                                                <a id='dislike_{{$message->id}}'
                                                   onclick="removelikePost('{{$message->id}}', '{{$message->postlike_id}}', 'dislike_')"
                                                   class="link-black text-sm" style="color:red"><i
                                                            class="fa fa-thumbs-down margin-r-5"></i>{!! trans('PublicCbs.dislike') !!}</a>
                                            @else
                                                <a id='like_{{$message->id}}' onclick="likePost('{{$message->id}}')"
                                                   class="link-black text-sm"><i class="fa fa-thumbs-o-up margin-r-5"></i>{!! trans('PublicCbs.like') !!}</a>
                                                <a id='dislike_{{$message->id}}' onclick="dislikePost('{{$message->id}}')"
                                                   class="link-black text-sm"><i class="fa fa-thumbs-o-down margin-r-5"></i>{!! trans('PublicCbs.dislike') !!}</a>
                                            @endif
                                            --}}
                                        </li>
                                    </ul>
                                </div>
                            @endif

                            <div class="pull-right">
                                <small class="text-muted">0 {!! trans('PublicCbs.likes') !!} - 0
                                    {!! trans('PublicCbs.dislikes') !!}</small>
                            </div>
                            </ul>
                        </div>
                    </div>
                </div>
            @endforeach

            @if(ONE::isAuth() && ONE::checkCBsOption($configurations, 'ALLOW-COMMENTS'))
                <div id="new_post" style="margin-top: 20px">
                    <form name="topic" accept-charset="UTF-8" method="POST"
                          onsubmit="return validate('contents_area');"
                          action="{{action('PublicPostController@store', ['topicKey' => $topicKey])}}">
                        <div class="col-md-10 col-sm-8">


                            <div style="margin-top: 15px;">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}"/>
                                <input type="hidden" name="parent_id" value="0"/>

                                <textarea type="text" name="contents" id="contents_area" rows="6" class="form-control" placeholder="Enter new post" style="resize: none"></textarea>
                            </div>
                            <div class="box-footer clearfix" style="padding-left: 0px;">
                                <button class="btn btn-sm btn-primary pull-left  btn-flat" type="submit">{!! trans('PublicCbs.submit') !!}</button>
                            </div>
                        </div>

                        <div class="col-md-2 col-sm-4" style="border-left: 1px solid #3c8dbc;">
                            <div style="text-align: center; height: 200px;padding-top: 35px;">
                                <div style="height: 100px; width: 90px; text-align: center; margin: auto;">
                                    @if(Session::get('user')->photo_id > 0)
                                        <img style="width: 100%" class="img-circle"
                                             src="{{URL::action('FilesController@download',[Session::get('user')->photo_id, Session::get('user')->photo_code, 1])}}">
                                    @else
                                        <img style="width: 100%" class="img-circle" src="{{ asset('images/default/icon-user-default-160x160.png') }}">
                                    @endif
                                </div>
                                <p>
                                    <b style="color: #3c8dbc">{{Session::get('user')->name}} </b><br>
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
                    <h4 class="modal-title" id="myModalLabel">{!! trans('PublicCbs.reportAbuse') !!}</h4>
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
                    <button type="button" class="btn btn-primary" id="buttonSendAbuse">{!! trans('PublicCbs.report') !!}</button>
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
                    <h4 class="modal-title" id="myModalLabel">{!! trans('PublicCbs.editPost') !!}</h4>
                </div>
                <div class="modal-body">
                    <textarea type="text" name="contents" id="update_contents_area" rows="6" class="form-control"  style="resize: none"></textarea>


                </div>
                <div class="modal-footer">
                    <input type="hidden" name="_tokenPost" value="{{ csrf_token() }}">
                    <button type="button" style='margin-left:10px;' class="btn btn-primary pull-right col-sm-2 " id="buttonEditPost">{!! trans('PublicCbs.updated') !!}</button>
                    <button type="button" class="btn btn-default col-sm-2 pull-right" data-dismiss="modal"
                            id="frm_cancel">{!! trans('PublicCbs.close') !!}
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
                    <h4 class="modal-title" id="myModalLabel">{!! trans('PublicCbs.postHistory') !!}</h4>
                </div>
                <div class="modal-body" id="post_history">

                </div>
                <div class="modal-footer">
                    <input type="hidden" name="_tokenHistory" value="{{ csrf_token() }}">
                    <button type="button" class="btn btn-default col-sm-2 pull-right" data-dismiss="modal" id="frm_cancel">{!! trans('PublicCbs.close') !!}
                    </button>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script src="{{ asset("js/bootstrap-wysihtml5.js") }}"></script>
    <script src="{{ asset("js/diff/diff.js") }}"></script>

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
        });

        function nl2br (str, is_xhtml) {
            // http://kevin.vanzonneveld.net
            // +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
            // +   improved by: Philip Peterson
            // +   improved by: Onno Marsman
            // +   improved by: Atli Þór
            // +   bugfixed by: Onno Marsman
            // +      input by: Brett Zamir (http://brett-zamir.me)
            // +   bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
            // +   improved by: Brett Zamir (http://brett-zamir.me)
            // +   improved by: Maximusya
            // *     example 1: nl2br('Kevin\nvan\nZonneveld');
            // *     returns 1: 'Kevin<br />\nvan<br />\nZonneveld'
            // *     example 2: nl2br("\nOne\nTwo\n\nThree\n", false);
            // *     returns 2: '<br>\nOne<br>\nTwo<br>\n<br>\nThree<br>\n'
            // *     example 3: nl2br("\nOne\nTwo\n\nThree\n", true);
            // *     returns 3: '<br />\nOne<br />\nTwo<br />\n<br />\nThree<br />\n'
            var breakTag = (is_xhtml || typeof is_xhtml === 'undefined') ? '<br ' + '/>' : '<br>'; // Adjust comment to avoid issue on phpjs.org display

            return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1' + breakTag + '$2');
        }


        function hideTopicHistory(){
            $("#topicHistory").hide();


        }

        function revertTopic(postKey, version){

            $.ajax({
                method: 'POST', // Type of response and matches what we said in the route
                url: '{{action('PublicTopicController@revertVersionTopic', ['cbKey' => $cbKey, 'topicKey' => $topicKey])}}', // This is the url we gave in the route
                data: {postKey: postKey, postVersion: version,  _token: $('input[name=_tokenHistory]').val()}, // a JSON object to send back
                success: function (response) { // What to do if we succeed

                    window.location.href = "{!! action('PublicTopicController@show',  ['cbKey' => $cbKey,'topicKey' => $topicKey, 'type' => $type] ) !!}"
                },
                error: function (jqXHR, textStatus, errorThrown) { // What to do if we fail
                    console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                }
            });
        }

        function showTopicHistory(postKey){

            $.ajax({
                method: 'POST', // Type of response and matches what we said in the route
                url: '{{action('PublicPostController@showHistory')}}', // This is the url we gave in the route
                data: {postKey: postKey, _token: $('input[name=_tokenHistory]').val()}, // a JSON object to send back
                success: function (response) { // What to do if we succeed

                    var array = JSON.parse(response);
                    var html = '';
                    var  color = '', span = null


                    var temp_contents = '';
                    var temp_created_at = '';

                    html += '<div class="row" style="min-height: 200px;overflow-y: auto;">';
                    html += '<div class="col-md-12">';

                    for(var i = 0; i < array.length; i++){

                        var post = array[i];
                        var contents = post['contents'];
                        var updated_at = post['updated_at'];
                        var version = post['version'];

                        html += '<div style="padding:10px">';
                        html += '<b><i class="fa fa-commenting" title="Reply"></i> Created</b> in '+updated_at;
                        html += '<div style="float:right; text-decoration: underline; cursor: pointer" onclick="revertTopic(\''+postKey+'\', \''+version+'\');">Revert</div>';
                        html += '<div style="border: 1px solid #dedede;margin: 10px; padding: 10px;min-height: 40px;">';
                        html += nl2br(contents);
                        html += '</div>';
                        html += '</div>';


                    }

                    html += '</div>';
                    html += '</div>';


                    $("#topicHistoryContents").html(html);
                    $("#topicHistory").show();

                },
                error: function (jqXHR, textStatus, errorThrown) { // What to do if we fail
                    alert('fail');
                    console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                }
            });
        }


        function showHistory(postKey) {

            $('#modalHistoryPost').modal('show')

            $.ajax({
                method: 'POST', // Type of response and matches what we said in the route
                url: '{{action('PublicPostController@showHistory')}}', // This is the url we gave in the route
                data: {postKey: postKey, _token: $('input[name=_tokenHistory]').val()}, // a JSON object to send back
                success: function (response) { // What to do if we succeed

                    var array = JSON.parse(response);
                    var html = '';
                    var  color = '', span = null


                    var temp_contents = '';
                    var temp_created_at = '';


                    html += '<div class="row" style="padding-top: 10px; max-height: 300px;overflow-y: auto;">';
                    html += '<div class="col-md-1"></div><div class="col-md-10">';

                    for(var i = 0; i < array.length; i++){

                        var post = array[i];
                        var contents = post['contents'];
                        var created_at = post['created_at'];

                        if(temp_contents != '' && temp_created_at != ''){
                            var  diff = JsDiff.diffChars(temp_contents, contents);
                            var fragment = document.createDocumentFragment();
                            var div = document.createElement('div');

                            diff.forEach(function(part){
                                // green for additions, red for deletions
                                // grey for common parts

                                color = part.added ? 'green' :
                                        part.removed ? 'red' : 'grey';
                                span = document.createElement('span');
                                span.style.color = color;
                                if(part.added){
                                    span.style.fontWeight= "bold";
                                }
                                span.appendChild(document.createTextNode(part.value));

                                fragment.appendChild(span);
                            });
                            div.appendChild(fragment);

                            html += '<small><b><i class="fa fa-commenting" title="Reply"></i> Created</b> in '+created_at+'</small>';
                            html += '<div style="border: 1px solid #dedede;margin-bottom: 10px; padding: 10px;min-height: 40px;">';
                            html += div.innerHTML;
                            html += '</div>';
                        }else{
                            html += '<small><b><i class="fa fa-commenting" title="Reply"></i> Created</b> in '+created_at+'</small>';
                            html += '<div style="border: 1px solid #dedede;margin-bottom: 10px; padding: 10px;min-height: 40px;">';
                            html += contents;
                            html += '</div>';

                        }
                        temp_contents = contents;
                        temp_created_at = created_at;
                    }

                    html += '</div>';
                    html += '</div>';


                    $("#post_history").html(html);
                },
                error: function (jqXHR, textStatus, errorThrown) { // What to do if we fail
                    alert('fail');
                    console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                }
            });
        }



        function editPost(postKey) {

            var post = $("#post_contents_"+postKey).html();
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
                        url: '{!!action('PublicPostController@update', ['topicKey' => $topicKey])!!}', // This is the url we gave in the route
                        data: {_method:'PUT', cbKey: '{{$cbKey}}', type: '{{$type}}' ,postKey: postKey, contents: newPost, _token: $('input[name=_tokenPost]').val()}, // a JSON object to send back
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
                url: '/public/discussion/post/likePost', // This is the url we gave in the route
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
                url: '/public/discussion/post/dislikePost', // This is the url we gave in the route
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
                url: '/public/discussion/post/deleteLike', // This is the url we gave in the route
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

        function validate(id) {
            var value = $("#" + id).val();

            if (value.length < 1) {
                return false; // keep form from submitting
            }
            return true;
        }

        function reportAbuse(id, postId) {
            $('#modalReportAbuse').modal('show')


            $('#buttonSendAbuse').on('click', function (evt) {
                $('#buttonSendAbuse').off();
                // var text = $("input[type='radio']:checked").parent().text();

                $.ajax({
                    method: 'POST', // Type of response and matches what we said in the route
                    url: '/public/discussion/post/reportAbuse', // This is the url we gave in the route
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




