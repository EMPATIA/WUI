@extends('public.empaville_new._layouts.index')
@section('header_scripts')
    <!-- Maps -->
    <script   src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCjgiI5l8FanufeE3GRchTZSVOaAyzVIE8" type="text/javascript"></script>

    <!-- Fancybox -->

    <!-- Mousewheel plugin (optional) -->
    <script type="text/javascript" src="{{ asset('js/fancybox/lib/jquery.mousewheel-3.0.6.pack.js')}}"></script>
    <script type="text/javascript" src="{{ asset('js/fancybox/source/jquery.fancybox.pack.js')}}"></script>

    <!-- helpers - button, thumbnail and/or media (optional) -->
    <script type="text/javascript" src="{{ asset('js/fancybox/source/helpers/jquery.fancybox-buttons.js')}}"></script>
    <script type="text/javascript" src="{{ asset('js/fancybox/source/helpers/jquery.fancybox-media.js')}}"></script>
    <script type="text/javascript" src="{{ asset('js/fancybox/source/helpers/jquery.fancybox-thumbs.js')}}"></script>
    <link rel="stylesheet" href="{{ asset('css/fancybox/source/jquery.fancybox.css')}}" type="text/css" media="screen" />
    <!-- helpers - button, thumbnail and/or media (optional) -->
    <link rel="stylesheet" href="{{ asset('css/fancybox/source/helpers/jquery.fancybox-buttons.css')}}" type="text/css" media="screen" />

    <link rel="stylesheet" href="{{ asset('css/fancybox/source/helpers/jquery.fancybox-thumbs.css')}}" type="text/css" media="screen" />
    <!-- // Fancybox -->
@endsection

@section('content')
    <div class="container container-topic">
        <div class="row">
            <div class="col-xs-12">
                <div class="pads-title">
                    <h3>{{ strtoupper($cb->title) }}</h3>
                </div>
            </div>
        </div>
        <div class="row" style="text-align: center; color: #aaaaaa; font-size: 12px">
            {{ trans("empavillePadsIdea.idea_number") }}
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
                {{--<div class="pull-right">--}}
                {{--@if(!empty($topic->active_status))--}}
                {{--{{ $topic->active_status->status_type->name }}--}}
                {{--@endif--}}
                {{--</div>--}}
            </div>

        </div>
        <div class="row margin-top">
            <div class="col-xs-12 col-md-7 title-margin">
                <div class="ideaTitle">
                    {{$topic->title}}
                </div>
            </div>
            <div class="col-xs-12 col-md-5">
                @if($isModerator)
                    <a class="btn btn-default btn-md pull-right moderator-buttons darkBtn"
                       href="javascript:oneDelete('{!! action('PublicTopicController@delete', ['cbKey' => $cbKey,'topicKey' => $topicKey, 'type' => $type]) !!}')" data-toggle="tooltip" data-delay='{"show":"1000"}' data-original-title="Delete">
                        <i class="fa fa-remove"></i>
                        <span class="hidden-xs">{!! trans('empavillePadsIdea.delete') !!}</span>
                    </a>

                    <a class="btn btn-default btn-md pull-right moderator-buttons"
                       href="{!! action('PublicTopicController@edit', ['cbKey' => $cbKey,'topicKey' => $topicKey, 'type' => $type, 'f'=> 'topic']) !!}">
                        <i class="fa fa-pencil"></i>
                        <span class="hidden-xs">{!! trans('empavillePadsIdea.change') !!}</span>
                    </a>
                @endif
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12 col-md-7 contents-margin">
                <div class="row">
                    <div class="col-xs-12">
                        <!-- Files -->
                        @if( isset($filesByType->images) )
                            <div class="cbImages">
                                <img src="{{action('FilesController@download', [$filesByType->images[0]->file_id, $filesByType->images[0]->file_code] )}}"/>&nbsp;&nbsp;
                            </div>
                        @endif
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12 visible-xs visible-sm">


                        <div class="map">
                            @if((count($parameters) > 0))
                                @foreach($parameters as $parameter)
                                    @if($parameter->code == 'google_maps')
                                        {!! Form::oneMaps('mapViewN1',null,isset($parameter->pivot->value)?$parameter->pivot->value : null,["readOnly" => true]) !!}
                                    @endif
                                    @if($parameter->code == 'image_map')
                                        {!! Form::oneEmpavilleMap( $parameter->id, $parameter->parameter, asset('images/empaville_map.jpg'),false,$parameter->mandatory ?? 0, isset($parameter->pivot->value)? $parameter->pivot->value : null) !!}
                                    @endif
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12">
                        <div class="summary">

                            {!! nl2br($topic->contents) !!}
                        </div>
                        <div class="description">
                            @if($topicMessage != null)
                                {!! nl2br($topicMessage->contents) !!}
                            @endif
                        </div>
                        <div class="row">
                            <div style="margin-top: 20px">
                                <div class="col-xs-12 col-sm-4 idea-topic-top-parameter">
                                    <i class="fa fa-user"></i>
                                    {{isset($usersNames[$topic->created_by]['name']) ? $usersNames[$topic->created_by]['name'] : $topic->created_by}}
                                </div>
                                <div class="col-xs-12 col-sm-4 idea-topic-top-parameter">
                                    @if($statistics['posts_counter'] > 1)
                                        <i class="fa fa-comment-o"></i>{{($statistics['posts_counter'] - 1)}} {{ trans("empavillePadsIdea.comments") }}
                                    @endif
                                </div>
                                <div class="col-xs-12 col-sm-4 idea-topic-top-parameter">
                                    <i class="fa fa-clock-o"></i>

                                    {{$topic->created_at}}
                                </div>
                            </div>
                        </div>
                        <div class="topic-voting idea-topic-voting">
                            @if(!ONE::isAuth())
                                <div class="alertBoxGreenNews">
                                    <div class="col-xs-12 text-center">
                                        <h2><i class="fa fa-exclamation-circle " aria-hidden="true"></i>  {{ trans("empavillePadsIdea.login_to_Vote") }}</h2>
                                    </div>
                                </div>
                            @elseif(!empty($voteType))
                                <div class="row">
                                    @foreach($voteType as $vt)
                                        @if( isset($vt["genericConfigurations"])  && $vt['existVotes'])
                                            <div class="col-xs-12 col-sm-6 idea-vote-box">
                                                <div class="idea-vote-name">
                                                    {{$vt['name']}}
                                                </div>
                                                <div class="col-xs-12 col-sm-6">
                                                    <div class="idea-vote-type">
                                                        @if( $vt["method"] == "VOTE_METHOD_NEGATIVE" )
                                                            {!! Html::oneNegativeVoting($topic->topic_key,
                                                                                        $cbKey,$vt["key"],
                                                                                        ($vt["genericConfigurations"]["show_total_votes"] ?? 0),
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
                                                </div>
                                                @if($vt['method'] == 'VOTE_METHOD_MULTI' || $vt['method'] == 'VOTE_METHOD_NEGATIVE')
                                                    <div class="col-xs-12 col-sm-6">
                                                        <div class="idea-remaining-votes">
                                                            {!! trans('empavillePadsIdea.remaining_votes') !!}
                                                        </div>
                                                        <div class="idea-vote-counter">
                                                            <div>
                                                                <span class="idea-vote-name-title">{{trans('empavillePadsIdea.total')}}</span>
                                                                <span id="remainingCounter_{{$vt['key']}}" class="idea-vote-total">{{$vt['remainingVotes']->total}}</span>
                                                            </div>
                                                            <hr class="idea-vote-hr">
                                                            @if($vt['method'] == 'VOTE_METHOD_NEGATIVE')
                                                                <div>
                                                                    <i class="fa fa-check" aria-hidden="true" style="width: 14px"> </i> {{ trans('empavillePadsIdea.positive')}}
                                                                    <span id="remainingPositiveCounter_{{$vt['key']}}" class="idea-vote-positive" >{{$vt['remainingVotes']->positive}}</span>
                                                                </div>
                                                                <div>
                                                                    <i class="fa fa-times" aria-hidden="true" style="width: 14px"> </i> {{ trans('empavillePadsIdea.negative')}}
                                                                    <span id="remainingNegativeCounter_{{$vt['key']}}" class="idea-vote-negative" >{{$vt['remainingVotes']->negative}}</span>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
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
            <div class="col-xs-12 col-md-5 contents-margin">
                <div class="row">
                    <div class="col-xs-12 hidden-sm hidden-xs">
                        <div class="map">
                            @if((count($parameters) > 0))
                                @foreach($parameters as $parameter)
                                    @if($parameter->code == 'google_maps')
                                        {!! Form::oneMaps('mapView',null,isset($parameter->pivot->value)?$parameter->pivot->value : null,["readOnly" => true]) !!}
                                    @endif
                                    @if($parameter->code == 'image_map')
                                            {!! Form::oneEmpavilleMap( $parameter->id, $parameter->parameter, asset('images/empaville_map.jpg'),false,$parameter->mandatory ?? 0, isset($parameter->pivot->value)? $parameter->pivot->value : null) !!}
                                    @endif
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12">
                        <div class="row">
                            @foreach($parameters as $parameter)
                                @if($parameter->type->code == 'google_maps')
                                    <div class="col-xs-12 parameter-location">
                                        {!! Form::oneReverseGeocoding("streetReverseGeocoding", "", $parameter->pivot->value, true ) !!}
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
                @foreach($parameters as $parameter)
                    @if($parameter->type->code != 'google_maps' && $parameter->type->code != 'image_map')
                        <div class="col-xs-12 idea-topic-parameter">
                            @if($parameter->type->code == "category")
                                <div class="idea-parameter-label">
                                    {{$parameter->parameter ?? null}}
                                </div>
                                <div class="idea-parameter-text">
                                    {{isset($dropDownOptions[$parameter->pivot->value])? $dropDownOptions[$parameter->pivot->value] : null}}
                                </div>
                            @elseif($parameter->type->code == "budget")
                                <div class="idea-parameter-label">
                                    {{$parameter->parameter ?? null}}
                                </div>
                                <div class="idea-parameter-text">
                                    {{isset($dropDownOptions[$parameter->pivot->value])? $dropDownOptions[$parameter->pivot->value] : null}}
                                </div>
                            @elseif($parameter->type->code == "dropdown" || $parameter->type->code == "radio_buttons")
                                <div class="idea-parameter-label">
                                    {{$parameter->parameter ?? null}}
                                </div>
                                <div class="idea-parameter-text">
                                    {{isset($dropDownOptions[$parameter->pivot->value])? $dropDownOptions[$parameter->pivot->value] : null}}
                                </div>
                            @elseif($parameter->type->code == "check_box")
                                @if(!empty($parameter->options))
                                    <div class="idea-parameter-label">
                                        {{$parameter->parameter ?? null}}
                                    </div>
                                    @foreach($parameter->options as $option)
                                        <div class="idea-parameter-text">
                                            {{$option->label ?? null}}
                                        </div>
                                    @endforeach
                                @endif
                            @elseif(isset($permissions) &&  array_key_exists ("ALLOW-VIDEO-LINK",$permissions ) && $permissions["ALLOW-VIDEO-LINK"] && preg_match('/http:\/\/(www\.)*youtube\.com\/.*/', $parameter->pivot->value ) )
                                <iframe title="YouTube video player" class="youtube-player" type="text/html" width="640" height="390" src="{{$parameter->pivot->value}}" frameborder="0" allowFullScreen></iframe>
                            @elseif(isset($permissions) &&  array_key_exists ("ALLOW-VIDEO-LINK",$permissions ) && $permissions["ALLOW-VIDEO-LINK"] && preg_match('/http:\/\/(www\.)*vimeo\.com\/.*/',$parameter->pivot->value) )
                                <iframe src="{{$parameter->pivot->value}}" width="640" height="390" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
                            @elseif($parameter->type->code == "questionnaire")
                                <a href="{!! action('PublicQController@showQ',$parameter->pivot->value)!!}" target="_blank">
                                    {{ trans("empavillePadsIdea.form") }}
                                </a>
                            @else
                                <b>{{$parameter->pivot->value}}</b>
                            @endif
                        </div>
                    @endif
                @endforeach
                @foreach($topicData->configurations as $item)
                    @if( $item == 'topic_options_allow_pictures' )
                        @if( isset($filesByType->images) && sizeof($filesByType->images) > 1 )
                            <div class="col-sm-12 col-xs-12 col-md-12">
                                <div class="summary row">
                                    {{trans('empavillePadsIdea.images')}}
                                </div>
                                <div class="idea-images-container">
                                    @foreach($filesByType->images as $i=>$fileTmp)
                                        @if($i != 0 && $loop->index < 4)
                                            <div class="col-sm-4 col-xs-12 thumb idea-thumb">
                                                <a class="fancybox thumbnail" rel="group" href="{{action('FilesController@download', [$fileTmp->file_id, $fileTmp->file_code, 1] )}}">
                                                    <img src="{{action('FilesController@download', [$fileTmp->file_id, $fileTmp->file_code] )}} " style="height:150px; object-fit: cover"/>
                                                </a>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @endif
                @endforeach
                <div class="col-xs-12">
                    {{--Files--}}
                    @foreach($topicData->configurations as $item)
                        {{--Show only if docs are allowed--}}
                        @if( $item == 'topic_options_allow_files' )
                            @if( isset($filesByType->docs) )
                                <div class="summary row">
                                    {{trans('empavillePadsIdea.files')}}
                                </div>
                                <div class="idea-files-container">
                                    <div class="well">
                                        @foreach($filesByType->docs as $fileTmp)
                                            <div class="idea-files">
                                                {!! ONE::fileIconByFilename( $fileTmp->name  ) !!}
                                                <a href="{{action('FilesController@download', [$fileTmp->file_id, $fileTmp->file_code] )}}" style="height:250px;" >
                                                    {{ $fileTmp->name }}
                                                </a>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        @endif
                        @if( isset($filesByType->videos) )
                            <div class="col-sm-12 col-xs-12 col-md-12">
                                <div class="summary row">
                                    {{trans('empavillePadsIdea.videos')}}
                                </div>
                                @foreach($filesByType->videos as $fileTmp)
                                    <a href="{{action('FilesController@download', [$fileTmp->file_id, $fileTmp->file_code] )}}" style="height:250px;" >
                                        {{ $fileTmp->name }}
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    @endforeach
                </div>
                @if(isset($permissions) && array_key_exists("ALLOW-SHARE", $permissions) && $permissions["ALLOW-SHARE"])
                    <div class="col-xs-12 parameter idea-share-btn">
                        <a href="{{ $shareLinks["facebook"]["link"] }}" target="_blank" class="btn-facebook">
                            <i class="fa fa-facebook"></i>
                            {{ trans("empavillePadsIdea.share") }}
                        </a>
                    </div>
                @endif
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <a class="btn btn-default backButton" href="{!! action('PublicCbsController@show', [$cbKey, 'type'=> $type] ) !!}" >
                    <span>{{trans('empavillePadsIdea.back_button')}}</span>
                </a>
            </div>
        </div>
        <div class="row">
            @if(ONE::checkCBsOption($configurations, 'ALLOW-COMMENTS'))
                @include('public.empaville_new.cbs.commentsSection')
            @endif
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="modalReportAbuse" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">{!! trans('empavillePadsIdea.reportAbuse') !!}</h4>
                </div>
                <div class="modal-body">
                    <div class="radio">
                        <label><input type="radio" class="optradio" name='optradio' checked="checked" id="1">{!! trans('empavillePadsIdea.spam') !!}</label>
                    </div>
                    <div class="radio">
                        <label><input type="radio" class="optradio" name='optradio' id="2">{!! trans('empavillePadsIdea.contains_hate_speech_or_attacks_and_individual') !!}</label>
                    </div>
                    <div class="radio">
                        <label><input type="radio" class="optradio" name='optradio' id="3">{!! trans('empavillePadsIdea.content_not_recommended') !!}</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <button type="button" class="btn btn-primary" id="buttonSendAbuse">{!! trans('empavillePadsIdea.report') !!}</button>
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
                    <h4 class="modal-title" id="myModalLabel">{!! trans('empavillePadsIdea.editPost') !!}</h4>
                </div>
                <div class="modal-body">
                    <textarea type="text" name="contents" id="update_contents_area" rows="6" class="form-control"  style="resize: none"></textarea>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="_tokenPost" value="{{ csrf_token() }}">
                    <button type="button" style='margin-left:10px;' class="btn btn-primary pull-right col-sm-2 " id="buttonEditPost">{!! trans('empavillePadsIdea.updated') !!}</button>
                    <button type="button" class="btn btn-default col-sm-2 pull-right" data-dismiss="modal"
                            id="frm_cancel">{!! trans('empavillePadsIdea.close') !!}
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
                    <h4 class="modal-title" id="myModalLabel">{!! trans('empavillePadsIdea.postHistory') !!}</h4>
                </div>
                <div class="modal-body" id="post_history">

                </div>
                <div class="modal-footer">
                    <input type="hidden" name="_tokenHistory" value="{{ csrf_token() }}">
                    <button type="button" class="btn btn-default col-sm-2 pull-right" data-dismiss="modal" id="frm_cancel">{!! trans('empavillePadsIdea.close') !!}
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
            $("#button-append").append("<button class='btn btn-sm btn-primary pull-left btn-flat btn-cancel' id='btn-cancel' type='submit'><?php  echo trans('empavillePadsIdea.cancel') ?></button>");
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




