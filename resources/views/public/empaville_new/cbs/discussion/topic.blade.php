@extends('public.empaville_new._layouts.index')
@section('header_scripts')
    <!-- Maps -->
    <script   src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCjgiI5l8FanufeE3GRchTZSVOaAyzVIE8" type="text/javascript"></script>

    <!-- Fancybox.js -->

    <!-- Mousewheel plugin (optional) -->
    <script type="text/javascript" src="{{ asset('js/fancybox/lib/jquery.mousewheel-3.0.6.pack.js')}}"></script>
    <!-- Fancybox.js -->
    <script type="text/javascript" src="{{ asset('js/fancybox/source/jquery.fancybox.pack.js')}}"></script>

    <!-- helpers - button, thumbnail and/or media (optional) -->
    <script type="text/javascript" src="{{ asset('js/fancybox/source/helpers/jquery.fancybox-buttons.js')}}"></script>
    <script type="text/javascript" src="{{ asset('js/fancybox/source/helpers/jquery.fancybox-media.js')}}"></script>

    <script type="text/javascript" src="{{ asset('js/fancybox/source/helpers/jquery.fancybox-thumbs.js')}}"></script>
    <!-- // Fancybox.js -->
@endsection

@section('header_styles')
    <!-- Fancybox.js -->
    <link rel="stylesheet" href="{{ asset('css/fancybox/source/jquery.fancybox.css')}}" type="text/css" media="screen" />
    <!-- helpers - button, thumbnail and/or media (optional) -->
    <link rel="stylesheet" href="{{ asset('css/fancybox/source/helpers/jquery.fancybox-buttons.css')}}" type="text/css" media="screen" />

    <link rel="stylesheet" href="{{ asset('css/fancybox/source/helpers/jquery.fancybox-thumbs.css')}}" type="text/css" media="screen" />
    <!-- // Fancybox.js -->
@endsection

@section('content')
    <div class="container container-topic">
        <div class="row">
            <div class="col-xs-12">
                <div class="title">
                    <h2 class="bolder">{{(trans("defaultPadsDiscussion.discussion")) }}</h2>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <a class="btn btn-default backButton" href="{!! action('PublicCbsController@show', [$cbKey, 'type'=> $type] ) !!}" >
                    <span>{{trans('defaultPadsDiscussion.back_button')}}</span>
                </a>
            </div>
        </div>
        <div class="row" style="text-align: center; color: #aaaaaa; font-size: 12px">
            {{ trans("defaultPadsDiscussion.discussion_number") }}
        </div>
        <div class="row" style="background: #f9f9f9; padding: 10px">
            <div class="col-xs-4 pull-left">
                <div class="pull-left">
                    @if(!empty($parameters))
                        @foreach($parameters as $parameter)
                            @if($parameter->type->code == "category")
                                {{ isset($dropDownOptions[$parameter->pivot->value]) ? $dropDownOptions[$parameter->pivot->value] : null }}
                            @endif
                        @endforeach
                    @endif
                </div>
            </div>

            <div class="col-xs-4">
                <div style="text-align: center">
                    @if(isset($prevIdea))
                        <a href="{{ action("PublicTopicController@show", ['cbKey' => $cbKey, 'topicKey' => $prevIdea, 'type' => $type]) }}"><i class="fa fa-chevron-left" style="color: #8EC939" aria-hidden="true"></i></a>
                    @endif
                    <input type="text" style="text-align: center; width: 20%; color: #8EC939" placeholder="{{ $topic->topic_number }}" readonly>
                    @if(isset($nextIdea))
                        <a href="{{ action("PublicTopicController@show", ['cbKey' => $cbKey, 'topicKey' => $nextIdea, 'type' => $type]) }}"><i class="fa fa-chevron-right" style="color: #8EC939" aria-hidden="true"></i></a>
                    @endif
                </div>
            </div>
            <div class="col-xs-4 pull-right">
                <div class="pull-right">
                    @if(!empty($topic->active_status))
                        {{ $topic->active_status->status_type->name }}
                    @endif
                </div>
            </div>

        </div>
        <div class="row margin-top">
            <div class="col-xs-12 col-md-7">
                <div class="title">
                    <h3 style="color: #333;">{{$topic->title}}</h3>
                </div>
            </div>
            <div class="col-xs-12 col-md-5">
                @if($isModerator)
                    <a class="btn btn-default btn-lg pull-right moderator-buttons"
                       href="javascript:oneDelete('{!! action('PublicTopicController@delete', ['cbKey' => $cbKey,'topicKey' => $topicKey, 'type' => $type]) !!}')" data-toggle="tooltip" data-delay='{"show":"1000"}' data-original-title="Delete">
                        <i class="fa fa-remove"></i>
                        <span class="hidden-xs">{!! trans('defaultPadsDiscussion.delete') !!}</span>
                    </a>
                    <a class="btn btn-default btn-lg pull-right moderator-buttons"
                       href="{!! action('PublicTopicController@edit', ['cbKey' => $cbKey,'topicKey' => $topicKey, 'type' => $type, 'f'=> 'topic']) !!}">
                        <i class="fa fa-pencil"></i>
                        <span class="hidden-xs">{!! trans('defaultPadsDiscussion.change') !!}</span>
                    </a>
                @endif
            </div>
        </div>
        <div class="box-body" style="margin-top: 20px">
            <div class="row">
                <div class="col-md-2 col-sm-2">
                    <div style="text-align: center; height: 210px;padding-top: 15px;">
                        <div style="height: 100px; width: 90px; text-align: center; margin: auto;">
                            @if(isset($usersNames[$topic->created_by]['photo_id'])&& $usersNames[$topic->created_by]['photo_id'] > 0)
                                <img style="width: 100%" class="img-circle"  src="{{URL::action('FilesController@download',[$usersNames[$topic->created_by]['photo_id'], $usersNames[$topic->created_by]['photo_code'], 1])}}">

                            @else
                                <img style="width: 100%" class="img-circle" src="{{ asset('images/default/icon-user-default-160x160.png') }}">
                            @endif
                        </div>
                        <div class="user">
                            {{isset($usersNames[$topic->created_by]['name']) ? $usersNames[$topic->created_by]['name'] : $topic->created_by}}
                        </div>
                    </div>
                </div>
                <div class="col-md-10 col-sm-10 forum-topic-border">
                    <div class="row">
                        <div class="col-xs-12 text-muted padding-bottom-15">
                            @if($topicMessage->version != 1)
                                <b><small style="cursor: pointer;" onclick="showTopicHistory('{{$topicMessage->post_key}}')"><i class="fa fa-info" ></i> {!! trans('defaultPadsDiscussion.editedAt') !!} {{$topicMessage->updated_at}}</small></b>
                            @else
                                <small>{!! trans('defaultPadsDiscussion.createdBy') !!} {{$topic->created_at}}</small>
                            @endif
                            <div class="text-muted pull-right">
                                @if(ONE::checkCBsOption($configurations, 'ALLOW-REPORT-ABUSE'))
                                    <button type="button" class="btn btn-box-tool" id="buttonAbuse_{{$topic->id}}" onclick="reportAbuse('buttonAbuse_{{$topic->first_post->post_key}}', '{{$topic->first_post->post_key}}');"  style="color:red;">
                                        <i class="fa fa-warning"></i> {!! trans('defaultPadsDiscussion.reportAbuse') !!}
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                    <!-- post text -->
                    <div class="row">
                        <div class="col-xs-12">
                            <p>{!! nl2br($topicMessage->contents) !!}</p>
                        </div>
                    </div>
                    <div class="row">
                        @if((count($parameters) > 0))
                            <div class="col-xs-12 parameters-box">
                                <div class="col-xs-12 col-md-8">
                                    <div class="map">
                                        @foreach($parameters as $parameter)
                                            @if($parameter->code == 'google_maps')
                                                {!! Form::oneMaps('mapView',$parameter->parameter,isset($parameter->pivot->value)?$parameter->pivot->value : null,["readOnly" => true]) !!}
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                                <div class="col-xs-12 col-md-4">
                                    @foreach($parameters as $parameter)
                                        @if($parameter->type->code == 'google_maps')
                                            <div class="parameter-location">
                                                {!! Form::oneReverseGeocoding("streetReverseGeocoding", "", $parameter->pivot->value, true ) !!}
                                            </div>
                                        @endif
                                    @endforeach
                                    <div class="parameter">
                                        <i class="fa fa-user"></i>
                                        {{isset($usersNames[$topic->created_by]['name']) ? $usersNames[$topic->created_by]['name'] : $topic->created_by}}
                                    </div>
                                    <div class="parameter">
                                        <i class="fa fa-calendar"></i>
                                        {{$topic->created_at}}
                                    </div>
                                    @foreach($parameters as $parameter)
                                        @if($parameter->type->code != 'google_maps')
                                            <div class="parameter">
                                                @if($parameter->type->code == 'image_map')
                                                    <div>{{ trans("defaultPadsDiscussion.location") }}</div>
                                                @else
                                                    <b><div>{{ $parameter->parameter }}</div></b>
                                                @endif
                                                @if($parameter->type->code == "dropdown" || $parameter->type->code == "category" || $parameter->type->code == "budget" || $parameter->type->code == "radio_buttons")
                                                    {{isset($dropDownOptions[$parameter->pivot->value])? $dropDownOptions[$parameter->pivot->value] : null}}
                                                @elseif(isset($permissions) &&  array_key_exists ("ALLOW-VIDEO-LINK",$permissions ) && $permissions["ALLOW-VIDEO-LINK"] && preg_match('/http:\/\/(www\.)*youtube\.com\/.*/', $parameter->pivot->value ) )
                                                    <iframe title="YouTube video player" class="youtube-player" type="text/html" width="640" height="390" src="{{$parameter->pivot->value}}" frameborder="0" allowFullScreen></iframe>
                                                @elseif(isset($permissions) &&  array_key_exists ("ALLOW-VIDEO-LINK",$permissions ) && $permissions["ALLOW-VIDEO-LINK"] && preg_match('/http:\/\/(www\.)*vimeo\.com\/.*/',$parameter->pivot->value) )
                                                    <iframe src="{{$parameter->pivot->value}}" width="640" height="390" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
                                                @elseif($parameter->type->code == "questionnaire")
                                                    <a href="{!! action('PublicQController@showQ',$parameter->pivot->value)!!}" target="_blank">
                                                        {{ trans("defaultPadsDiscussion.form") }}
                                                    </a>
                                                @else
                                                    {{$parameter->pivot->value}}
                                                @endif
                                            </div>
                                        @endif
                                    @endforeach


                                </div>
                            </div>
                        @endif
                        @if(isset($topicData->configurations) && sizeof($topicData->configurations) > 0)
                            <div class="col-xs-12 configurations-box">
                                @foreach($topicData->configurations as $item)
                                    @if( $item == 'topic_options_allow_pictures' )
                                        @if( isset($filesByType->images) && sizeof($filesByType->images) > 1 )
                                            <div class="summary">
                                                {{trans('defaultPadsDiscussion.images')}}
                                            </div>
                                            @foreach($filesByType->images as $i=>$fileTmp)
                                                @if( $i != 0 )
                                                    <div class="col-lg-2 col-md-2 col-xs-3 thumb">
                                                        <a class="fancybox thumbnail" rel="group" href="{{action('FilesController@download', [$fileTmp->file_id, $fileTmp->file_code, 1] )}}"><img class="" src="{{action('FilesController@download', [$fileTmp->file_id, $fileTmp->file_code] )}}"/></a>&nbsp;&nbsp;
                                                    </div>
                                                @endif
                                            @endforeach
                                        @endif
                                    @endif
                                    @if( $item == 'topic_options_allow_files' )
                                        @if( isset($filesByType->docs) )
                                            <div class="summary">
                                                {{trans('defaultPadsDiscussion.files')}}
                                            </div>
                                            <div class="well">
                                                @foreach($filesByType->docs as $fileTmp)
                                                    <div class="row files-well">
                                                        {!! ONE::fileIconByFilename( $fileTmp->name  ) !!}
                                                        <a href="{{action('FilesController@download', [$fileTmp->file_id, $fileTmp->file_code] )}}" style="height:250px;" > {{ $fileTmp->name }} </a>&nbsp;
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    @endif
                                    @if( isset($filesByType->videos) )
                                        <div class="summary">
                                            {{trans('defaultPadsDiscussion.videos')}}
                                        </div>
                                        @foreach($filesByType->videos as $fileTmp)
                                            <a href="{{action('FilesController@download', [$fileTmp->file_id, $fileTmp->file_code] )}}" style="height:250px;" > {{ $fileTmp->name }} </a>,&nbsp;
                                        @endforeach
                                    @endif
                                @endforeach
                            </div>
                        @endif
                    </div>
                    <div class="row last-row">
                        <div class="col-md-12 col-xs-12">
                            @if(isset($permissions) && array_key_exists("ALLOW-SHARE", $permissions) && $permissions["ALLOW-SHARE"])
                                <div class="parameter pull-left facebook-left">
                                    <a href="{{ $shareLinks["facebook"]["link"] }}" target="_blank" class="btn-facebook">
                                        <i class="fa fa-facebook parameter-facebook"></i>
                                        {{ trans("defaultPadsDiscussion.share") }}
                                    </a>
                                </div>
                            @endif
                            <div class="pull-right voting-topic-div my-vote my-vote-2">
                                @if(!empty($voteType))
                                    @foreach($voteType as $vt)
                                        @if( isset($vt["genericConfigurations"]) && $vt['existVotes'])
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
                                        @endif
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if(ONE::checkCBsOption($configurations, 'ALLOW-COMMENTS'))
            @include('public.empaville_new.cbs.commentsSection')
        @endif
    </div>

    <!-- Modal -->
    <div class="modal fade" id="modalReportAbuse" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">{!! trans('defaultPadsDiscussion.reportAbuse') !!}</h4>
                </div>
                <div class="modal-body">
                    <div class="radio">
                        <label><input type="radio" class="optradio" name='optradio' checked="checked" id="1">{!! trans('defaultPadsDiscussion.spam') !!}</label>
                    </div>
                    <div class="radio">
                        <label><input type="radio" class="optradio" name='optradio' id="2">{!! trans('defaultPadsDiscussion.contains_hate_speech_or_attacks_and_individual') !!}</label>
                    </div>
                    <div class="radio">
                        <label><input type="radio" class="optradio" name='optradio' id="3">{!! trans('defaultPadsDiscussion.content_not_recommended') !!}</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <button type="button" class="btn btn-primary" id="buttonSendAbuse">{!! trans('defaultPadsDiscussion.report') !!}</button>
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
                    <h4 class="modal-title" id="myModalLabel">{!! trans('defaultPadsDiscussion.editPost') !!}</h4>
                </div>
                <div class="modal-body">
                    <textarea type="text" name="contents" id="update_contents_area" rows="6" class="form-control"  style="resize: none"></textarea>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="_tokenPost" value="{{ csrf_token() }}">
                    <button type="button" style='margin-left:10px;' class="btn btn-primary pull-right col-sm-2 " id="buttonEditPost">{!! trans('defaultPadsDiscussion.updated') !!}</button>
                    <button type="button" class="btn btn-default col-sm-2 pull-right" data-dismiss="modal"
                            id="frm_cancel">{!! trans('defaultPadsDiscussion.close') !!}
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
                    <h4 class="modal-title" id="myModalLabel">{!! trans('defaultPadsDiscussion.postHistory') !!}</h4>
                </div>
                <div class="modal-body" id="post_history">

                </div>
                <div class="modal-footer">
                    <input type="hidden" name="_tokenHistory" value="{{ csrf_token() }}">
                    <button type="button" class="btn btn-default col-sm-2 pull-right" data-dismiss="modal" id="frm_cancel">{!! trans('defaultPadsDiscussion.close') !!}
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

        // -- Fancybox plugin start --
        $(document).ready(function () {
            $(".fancybox").fancybox({
                "type": "image",
            });
        });

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

        function reportAbuse(id, postKey) {
            $('#modalReportAbuse').modal('show')
            $('#buttonSendAbuse').on('click', function (evt) {
                $('#buttonSendAbuse').off();

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

        function replyPost(postId, buttonId) {
            postIdReply = postId;
            $('#' + buttonId).hide();
            var htmlReponse = $("#new_post").html();

            //TODO: fix
            htmlReponse = htmlReponse.replace('value="0"', 'value="' + postId + '"');

            $("#post_div_" + postId).append("<div id='new_post_space' style='clear: both;padding-top: 10px;'></div><div id='new_post_replay' style='margin-bottom: 10px; padding-left: 0px; padding-top: 10px;border-top: 1px solid #d2d6de;'>" + htmlReponse + "</div>");
            $("#button-append").append("<button class='btn btn-sm btn-primary pull-left btn-flat btn-cancel' id='btn-cancel' type='submit'><?php  echo trans('defaultPadsDiscussion.cancel') ?></button>");
            $("#post_div_" + postId).css('margin', '10px');

            $("#new_post").hide();

            $("#btn-cancel").click(function(){
                console.log('teste');
                removeReplyPost();
                $("#new_post").show();
                $('#' + buttonId).show();
            });
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




