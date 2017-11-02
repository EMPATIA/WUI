@extends('public.default._layouts.index')
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
                    <h2 >{{ strtoupper($cb->title) }}</h2>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12 col-md-7">
                <div class="title">
                    <h3 style="">{{$topic->title}}</h3>
                </div>
            </div>
            <div class="col-xs-12 col-md-5">
                @if($isModerator)
                    <a class="btn btn-default btn-lg pull-right moderator-buttons"
                       href="javascript:oneDelete('{!! action('PublicTopicController@delete', ['cbKey' => $cbKey,'topicKey' => $topicKey, 'type' => $type]) !!}')" data-toggle="tooltip" data-delay='{"show":"1000"}' data-original-title="Delete">
                        <i class="fa fa-remove"></i>
                        <span class="hidden-xs">{!! trans('defaultPads.delete') !!}</span>
                    </a>
                    <a class="btn btn-default btn-lg pull-right moderator-buttons"
                       href="{!! action('PublicTopicController@edit', ['cbKey' => $cbKey,'topicKey' => $topicKey, 'type' => $type, 'f'=> 'topic']) !!}">
                        <i class="fa fa-pencil"></i>
                        <span class="hidden-xs">{!! trans('defaultPads.change') !!}</span>
                    </a>
                @endif
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12 col-md-7">
                <div class="row">
                    <div class="col-xs-12">
                        <!-- Files -->
                        @if( isset($filesByType->images) )
                            <div class="cbImages">
                                <img class="cbImages" src="{{action('FilesController@download', [$filesByType->images[0]->file_id, $filesByType->images[0]->file_code] )}}"/>&nbsp;&nbsp;
                            </div>
                        @endif
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
                    </div>
                    <div class="col-xs-12">
                        <!-- Files -->
                    @foreach($topicData->configurations as $item)

                        <!-- Show only if docs are allowed -->
                            @if( $item == 'topic_options_allow_files' )
                                @if( isset($filesByType->docs) )
                                    <div class="col-sm-12 col-xs-12 col-md-12">
                                        <div class="summary row">{{trans('defaultPads.files')}}</div>
                                        @foreach($filesByType->docs as $fileTmp)
                                            {!! ONE::fileIconByFilename( $fileTmp->name  ) !!}
                                            <a href="{{action('FilesController@download', [$fileTmp->file_id, $fileTmp->file_code] )}}" style="height:250px;" > {{ $fileTmp->name }} </a>&nbsp;
                                        @endforeach
                                    </div>
                                @endif
                            @endif


                            @if( isset($filesByType->videos) )
                                <div class="col-sm-12 col-xs-12 col-md-12">
                                    <div class="summary row">{{trans('defaultPads.videos')}}</div>
                                    @foreach($filesByType->videos as $fileTmp)
                                        <a href="{{action('FilesController@download', [$fileTmp->file_id, $fileTmp->file_code] )}}" style="height:250px;" > {{ $fileTmp->name }} </a>,&nbsp;
                                    @endforeach
                                </div>
                            @endif


                            @if( $item == 'topic_options_allow_pictures' )
                                @if( isset($filesByType->images) && sizeof($filesByType->images) > 1 )
                                    <div class="col-sm-12 col-xs-12 col-md-12">
                                        <div class="summary row">{{trans('defaultPads.images')}}</div>
                                        @foreach($filesByType->images as $i=>$fileTmp)
                                            @if( $i != 0 )
                                                <div class="col-lg-3 col-md-4 col-xs-6 thumb">
                                                    <a class="fancybox thumbnail" rel="group" href="{{action('FilesController@download', [$fileTmp->file_id, $fileTmp->file_code, 1] )}}"><img class="" src="{{action('FilesController@download', [$fileTmp->file_id, $fileTmp->file_code] )}}" style="height:150px;" /></a>&nbsp;&nbsp;
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>

                                @endif
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="col-xs-12 col-md-5">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="map">
                            @if((count($parameters) > 0))
                                @foreach($parameters as $parameter)
                                    @if($parameter->code == 'google_maps')
                                        {!! Form::oneMaps('mapView',$parameter->parameter,isset($parameter->pivot->value)?$parameter->pivot->value : null,["readOnly" => true]) !!}
                                    @endif
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12">
                        @foreach($parameters as $parameter)
                            @if($parameter->type->code == 'google_maps')
                                <div class="col-xs-12 parameter-location">
                                    {!! Form::oneReverseGeocoding("streetReverseGeocoding", "", $parameter->pivot->value, true ) !!}
                                </div>
                            @endif
                        @endforeach
                        <div class="col-xs-12 parameter">
                            <i class="fa fa-calendar"></i>
                            {{$topic->created_at}}
                        </div>
                        <div class="col-xs-12 parameter">
                            <i class="fa fa-user"></i>
                            {{isset($usersNames[$topic->created_by]['name']) ? $usersNames[$topic->created_by]['name'] : $topic->created_by}}
                        </div>
                        @if(!is_null($totalComments))
                            <div class="col-xs-12 parameter">
                                <i class="fa fa-comment-o"></i>{{$totalComments}}
                                {{ trans("defaultPads.comments") }}
                            </div>
                        @endif
                        @foreach($parameters as $parameter)
                            @if($parameter->type->code != 'google_maps')
                                <div class="col-xs-12 parameter">
                                    @if($parameter->type->code == 'image_map')
                                        <div>{{ trans("defaultPads.location") }}</div>
                                    @else
                                        <div>{{ $parameter->parameter }}</div>
                                    @endif
                                    @if($parameter->type->code == "dropdown" || $parameter->type->code == "category" || $parameter->type->code == "budget" || $parameter->type->code == "radio_buttons")
                                        <b>{{isset($dropDownOptions[$parameter->pivot->value])? $dropDownOptions[$parameter->pivot->value] : null}}</b>
                                    @elseif(isset($permissions) &&  array_key_exists ("ALLOW-VIDEO-LINK",$permissions ) && $permissions["ALLOW-VIDEO-LINK"] && preg_match('/http:\/\/(www\.)*youtube\.com\/.*/', $parameter->pivot->value ) )
                                        <iframe title="YouTube video player" class="youtube-player" type="text/html" width="640" height="390" src="{{$parameter->pivot->value}}" frameborder="0" allowFullScreen></iframe>
                                    @elseif(isset($permissions) &&  array_key_exists ("ALLOW-VIDEO-LINK",$permissions ) && $permissions["ALLOW-VIDEO-LINK"] && preg_match('/http:\/\/(www\.)*vimeo\.com\/.*/',$parameter->pivot->value) )
                                        <iframe src="{{$parameter->pivot->value}}" width="640" height="390" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
                                    @elseif($parameter->type->code == "questionnaire")
                                        <a href="{!! action('PublicQController@showQ',$parameter->pivot->value)!!}" target="_blank">
                                            {{ trans("defaultPads.form") }}
                                        </a>
                                    @else
                                        <b>{{$parameter->pivot->value}}</b>
                                    @endif
                                </div>
                            @endif
                        @endforeach
                        @if(isset($permissions) && array_key_exists("ALLOW-SHARE", $permissions) && $permissions["ALLOW-SHARE"])
                            <div class="col-xs-12 parameter">
                                <a href="{{ $shareLinks["facebook"]["link"] }}" target="_blank" class="btn-facebook"><i class="fa fa-facebook"></i> {{ trans("defaultPads.share") }}</a>
                            </div>
                        @endif
                        <div class="col-xs-12 parameter">
                            @if(!empty($voteType))
                                @foreach($voteType as $vt)
                                    @if( isset($vt["genericConfigurations"]) && $vt['existVotes'])
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
                                    @endif
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <a class="btn btn-default" href="{!! action('PublicCbsController@show', [$cbKey, 'type'=> $type] ) !!}" style="margin-top: 20px" >
            <span>{{trans('defaultPads.backButton')}}</span>
        </a>
    </div>
    @if(ONE::checkCBsOption($configurations, 'ALLOW-COMMENTS'))
        @include('public.default.cbs.commentsSection')
    @endif
@endsection

@section('scripts')
    <script>
        // -- Fancybox plugin start --
        $(document).ready(function () {
            $(".fancybox").fancybox({
                "type": "image",
            });
        });
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

        function editMessage(cbKey,postKey,type) {
            var post = $("#post_contents_" + postKey).html();
            //remove spaces at end
            post = post.replace(/^\s\s*/, '').replace(/\s\s*$/, '');

            $("#update_contents_area").val(post);

            $('#modalEditPost').modal('show');

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

        function validate(id) {
            $("#sendComment").css('opacity','0.5');
            $("#sendComment").css('pointer-events','none');
            var value = $("#"+id).val();

            if(value.length < 1) {
                $("#sendComment").css('opacity','1');
                $("#sendComment").css("pointer-events","auto");
                return false; // keep form from submitting
            }
            return true;
        }
    </script>
@endsection
