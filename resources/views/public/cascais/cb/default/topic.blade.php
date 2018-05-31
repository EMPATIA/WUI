@php
    $demoPageTitle = ONE::transCb('cb_topic_title', !empty($cb) ? $cb->cb_key : $cbKey);
    // Timezone
    $tz = One::getCurrentTimeZone();
@endphp
@extends('public.cascais.._layouts.index')

@section('header_scripts')
    <!-- Maps -->
    <script src="https://maps.googleapis.com/maps/api/js?key={{ Session::get("SITE-CONFIGURATION.maps_api_key") ?? "AIzaSyBJtyhsJJX_5DCp59m8sNsPlhHp8aQZHIE" }}"></script>
@endsection

@section('header_styles')
    <style>
        @if( empty($filesByType) && count($filesByType) == 0 )
        .idea-image{
            background-color: white!important;
            background-position: center!important;
            background-size: contain;
            background-repeat: no-repeat;
        }
        @endif


        .idea-image {
            padding: 0;
            height: auto!important;
        }
        .idea-image img{
            width: 100%;
        }

        .idea-map > .row > div{
            padding: 0!important;
        }

        .card-img-top{
            text-align: center;
            max-height: 150px;
        }

        #imgModalContent{
            width: auto!important;
            max-width: 100%;
        }

        #missing-moderation-left{
            background-color: {{Session::get("SITE-CONFIGURATION.color_primary_on_hover")}}!important;
        }

        #missing-moderation-message{
            background-color: #c4c4c4!important;
        }

        #comment-moderation{
            background-color: #4c4c4c;
            text-align: right;
            padding: 3px 5px;
            margin-bottom: 5px;
            display: block
        }

        @media (max-width: 700px) {
            #imgModalContent{
                width: 100%!important;
            }
        }

        .points {
            color: #383838;
            font-weight: 600;
            text-align: center;
            line-height: 50px;
        }

        .button-like,
        .button-dislike{
            text-align: center;
            padding: 0;
        }

        .button-like{
            background-color: {{ ONE::getSiteConfiguration("color_primary") }};
            color: #fff !important;
        }

        .button-dislike{
            background-color: {{ ONE::getSiteConfiguration("color_secondary") }};
            color: #fff !important;
        }

        .button-like a,
        .button-dislike a{
            padding: 10px 10px;
            color: #fff;
            line-height: 30px;
            vertical-align: middle;
            display: block;
            text-decoration: none;
        }

        .button-like a:hover,
        .button-dislike a:hover{
            background-color: #fff !important;
            color: {{ ONE::getSiteConfiguration("color_primary") }} !important;
            cursor: pointer;
            text-decoration: none;
        }


        .button-like i.fa,
        .button-dislike i.fa{
            font-size: 1.8rem;
            line-height: 30px;
            vertical-align: middle;
        }

        .button-like span,
        .button-dislike span{
            font-size: 1rem;
            text-transform: uppercase;
            line-height: 30px;
            vertical-align: middle;
        }

        .like-vote-button-voted{
            background-color: {{ Session::get("SITE-CONFIGURATION.color_secondary") }};
        }

        .ideas-grid .idea-card .vote-container .vote-button a:hover {
            background-color: #4c4c4c;
            text-decoration: none;
        }

        .share-link{
            display: flex;
            height: 100%;
            text-decoration: none!important;
        }

        .votesInfoBar{
            z-index:100;
        }

        .banner-voting-info {
            text-align: left!important;
        }

        .voted {
            background-color: #fec20c;
        }

        .multipleVoteBtn{
            line-height: 50px;
            cursor: pointer;
        }

        .show-vote-results{
            padding:5px 20px;
            text-align:center;
            color:white;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .red {
            background: #F7606F!important;
            color: white!important;
        }

        .green{
            background-color: #4a7a17!important;
        }
    </style>
@endsection

@section('content')
    <div class="banner-voting-info">
        @php
            if(!empty($voteType)){
                $first  = \Carbon\Carbon::parse($voteType[0]['eventVote']->start_date, $tz);
                $second = \Carbon\Carbon::parse($voteType[0]['eventVote']->end_date, $tz);
                $isInCurrentDayBetween = \Carbon\Carbon::now($tz)->between($first, $second);
            }
        @endphp
        @if(!empty($voteType) && !empty($isInCurrentDayBetween) && $isInCurrentDayBetween)
            @if(!ONE::isAuth())
                <div id="voteBanner" class="row banner-voting-info votes-forbidden">
                    <div class="col-12 banner-voting-col">
                        <a href="{{action('AuthController@login')}}">
                            {{ ONE::transCb('topic_please_login_to_participate', !empty($cb) ? $cb->cb_key : $cbKey)}}
                        </a>
                    </div>
                </div>
            @else
                @if(Session::has('user') && Session::get('user')->confirmed == 0 && Session::get('SITE-CONFIGURATION.boolean_register_only_nif')==false)
                <!-- Banner for votes allowed and enough to submit -->
                    <div id="voteBanner" class="row banner-voting-info votes-forbidden">
                        <div class="col-12 banner-voting-col">
                            <a href="{{action('AuthController@sendConfirmEmail') }}" class="submit-votes-btn">{{ ONE::transCb('resend_email', !empty($cb) ? $cb->cb_key : $cbKey)}}</a>
                        </div>
                    </div>
                @else
                <!-- Banner for votes forbidden -->
                    <div id="votesInfoBar" class="votes-info-bar votesInfoBar">
                        @foreach($voteType as $vt)
                            @php
                                /* Has Voting ended */
                                $vt['voteEnded'] = (isset($vt['eventVote'])
                                                        && ((is_array($vt['eventVote'])
                                                        && isset($vt['eventVote']["end_date"])
                                                        && \Carbon\Carbon::parse($vt['eventVote']["end_date"])->isPast())
                                                     || (isset($vt['eventVote']->end_date)
                                                        && \Carbon\Carbon::parse($vt['eventVote']->end_date)->isPast())));
                                $requiresConfirm = !empty($vt["genericConfigurations"]["boolean_requires_confirm"]) ? $vt["genericConfigurations"]["boolean_requires_confirm"] : 0;
                                $showConfirmationView = !empty($vt["genericConfigurations"]["boolean_show_confirmation_view"]) ? $vt["genericConfigurations"]["boolean_show_confirmation_view"] : 0;
                                $userCanReOpenVoting = !empty($vt["genericConfigurations"]["allow_unsubmit_votes"]) ? $vt["genericConfigurations"]["allow_unsubmit_votes"] : 0;
                                $showVoteResults = !empty($vt["genericConfigurations"]["show_vote_results"]) ? $vt["genericConfigurations"]["show_vote_results"] : 0;
                                $votesSubmited = !empty($vt['submitedDate']) ? true : false;
                            @endphp
                            @if(isset($vt['voteEnded']) && !$vt['voteEnded'] && !$votesSubmited || $showVoteResults == 1)
                                <div class="container">
                                    <div class="row no-gutters">
                                        <div class="col-md-6 col-12">
                                            @php
                                                $requiresConfirm = !empty($vt["genericConfigurations"]["boolean_requires_confirm"]) ? $vt["genericConfigurations"]["boolean_requires_confirm"] : 0;
                                                $showConfirmationView = !empty($vt["genericConfigurations"]["boolean_show_confirmation_view"]) ? $vt["genericConfigurations"]["boolean_show_confirmation_view"] : 0;
                                                $userCanReOpenVoting = !empty($vt["genericConfigurations"]["allow_unsubmit_votes"]) ? $vt["genericConfigurations"]["allow_unsubmit_votes"] : 0;
                                                $showVoteResults = !empty($vt["genericConfigurations"]["show_vote_results"]) ? $vt["genericConfigurations"]["show_vote_results"] : 0;
                                            @endphp

                                            @if(!empty($vt["remainingVotes"]))
                                                @if(isset($vt["remainingVotes"]->total))
                                                    <div class="info">
                                                        {!! ONE::transCb('topic_available_votes', !empty($cb) ? $cb->cb_key : $cbKey) !!}
                                                        <strong id="total_votes{{ $vt["key"] }}">{{ $vt["remainingVotes"]->total }}</strong>
                                                    </div>
                                                @endif
                                                @if(isset($vt["remainingVotes"]->user_votes))
                                                    <div class="between-bar"></div>
                                                    <div class="info">
                                                        {!! ONE::transCb('topic_used_votes', !empty($cb) ? $cb->cb_key : $cbKey) !!}
                                                        <strong id="user_votes{{ $vt["key"] }}">{{ $vt["remainingVotes"]->user_votes }}</strong>
                                                    </div>
                                                @endif
                                            @endif
                                        </div>

                                        @if($requiresConfirm == 1)
                                            <div class="col-md-6 col-12 text-right">
                                            @if( $vt['submitedDate'] == null )
                                                @if( $showConfirmationView == 1 )
                                                    <!-- Banner for votes allowed and enough to submit -->
                                                        <a href="{{ action("PublicCbsController@showTopicsVoted",["cbKey"=>$cbKey,"type"=>$type]) }}" class="submit-votes-btn" @if($vt['remainingVotes']->total != 0) style="pointer-events: none" @endif>
                                                            {{ ONE::transCb('submit_votes', !empty($cb) ? $cb->cb_key : $cbKey)}}
                                                        </a>
                                                    @else
                                                        <a id="submitVotes{{ $vt["key"] }}" href="#" class="submit-votes-btn">
                                                            {{ ONE::transCb('submit_votes', !empty($cb) ? $cb->cb_key : $cbKey)}}
                                                        </a>
                                                        <script>
                                                            var submitVotesArray = new Array();
                                                            $("#submitVotes{{ $vt['key'] }}").click(function(event) {
                                                                event.preventDefault();
                                                                $("#submitVotes{{ $vt["key"] }}").addClass("disabled");
                                                                @php $i = 0; @endphp
                                                                @foreach($voteType as $vt)
$.ajax({
                                                                    method: 'POST', // Type of response and matches what we said in the route
                                                                    url: '{{action("PublicCbsController@genericSubmitVotes")}}', // This is the url we gave in the route
                                                                    data: {
                                                                        eventKey: "{{ $vt["key"] }}",
                                                                        _token:  '{{csrf_token()}}',
                                                                    }, // a JSON object to send back
                                                                    beforeSend:function(){
                                                                        $(this).addClass('vote-not-login');
                                                                    },
                                                                    success: function (response) { // What to do if we succeed
                                                                        $("#spinner").hide();
                                                                        $("#check").show();
                                                                        $(this).removeClass('vote-not-login');
                                                                        if (response.hasOwnProperty("success")){
                                                                            window.location.href = "{{ action("PublicCbsController@votesSubmittedSuccessfuly",["cbKey"=>$cbKey,"type"=>$type]) }}";
                                                                        } else {
                                                                            $("#submitVotes").removeClass("disabled");
                                                                        }
                                                                    },
                                                                    error: function (jqXHR, textStatus, errorThrown) { // What to do if we fail
                                                                        // alert("{{ ONE::transCb('submit_votes_you_need_to_have_unleast_one_vote', !empty($cb) ? $cb->cb_key : $cbKey)}}");
                                                                        $("#error_message").html("{!! ONE::transCb('submit_votes_you_need_to_have_unleast_one_vote', !empty($cb) ? $cb->cb_key : $cbKey) !!}");
                                                                        $("#errorModal").modal('show');
                                                                    }
                                                                });
                                                                @php $i++; @endphp
                                                                @endforeach
                                                            });
                                                        </script>
                                                    @endif
                                                @else
                                                <!-- Banner for votes allowed and enough to submit -->
                                                    @if($userCanReOpenVoting == 1)
                                                        <a href="{{ action("PublicCbsController@unSubmitUserVotes",["cbKey"=>$cbKey,"type"=>$type,"voteKey" => $voteType[0]['key']]) }}" class="submit-votes-btn">
                                                            {{ ONE::transCb('vote_re_open_my_votes', !empty($cb) ? $cb->cb_key : $cbKey)}}
                                                        </a>
                                                    @else
                                                        <div style="display:flex;justify-content:center;align-items:center;height:100%;">
                                                            <div>{{ ONE::transCb('vote_submited', !empty($cb) ? $cb->cb_key : $cbKey)}}</div>
                                                        </div>
                                                    @endif
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @else
                                <div class="container">
                                    <div class="row no-gutters">
                                        <div class="col-12">
                                            <div style="display:flex;justify-content:center;align-items:center;height:100%;">
                                                <div style="padding: 20px 15px; font-size: 1.2rem; text-transform: uppercase">{{ ONE::transCb('vote_submited', !empty($cb) ? $cb->cb_key : $cbKey)}}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                @endif
            @endif
        @endif

    </div>

    <div class="container">
        <div class="row idea-topic-title">
            <div class="col title">
                <span>{{--{{ONE::getStatusTranslation($translations, 'topic_detail_title')}}--}}{{ONE::transCb('cb_topic_detail_title', !empty($cb) ? $cb->cb_key : $cbKey)}}</span>
                <a href="{{ action('PublicCbsController@show', [$cb->cb_key, 'type'=> $type] ) }}">{{--{{ONE::getStatusTranslation($translations, 'topic_detail_back')}}--}}{{ONE::transCb('cb_topic_detail_back', !empty($cb) ? $cb->cb_key : $cbKey)}}</a>
            </div>
            <div clasS="col">
                <div class="row no-gutters" style="justify-content: flex-end;">
                    <div class="col-lg-2 col-md-2 col-sm-4 col-6">
                        @php
                            $previousTopic = App\Http\Controllers\PublicCbsController::getTopicKeyByIndex($cb->cb_key,$topic->topic_key,"previous");
                            $nextTopic = App\Http\Controllers\PublicCbsController::getTopicKeyByIndex($cb->cb_key,$topic->topic_key,"next");
                        @endphp
                        @if(!empty($previousTopic) || !empty($nextTopic))
                            <div class="row no-gutters ideas-nav-buttons">
                                <div class="col-6 nav-left">
                                    <a href="@if(!empty($previousTopic)) {{ action('PublicTopicController@show', [$cb->cb_key , $previousTopic["topic_key"], 'type' => $type] ) }} @else # @endif"><i class="fa fa-long-arrow-left" aria-hidden="true"></i></a>
                                </div>
                                <div class="col-6 nav-right">
                                    <a href="@if(!empty($nextTopic)) {{ action('PublicTopicController@show', [$cb->cb_key , $nextTopic["topic_key"], 'type' => $type] ) }} @else # @endif"><i class="fa fa-long-arrow-right" aria-hidden="true"></i></a>
                                </div>
                            </div>
                        @endif
                        @if(One::getUserKey()==$topic->created_by && !\Carbon\Carbon::parse($cb->end_date)->isPast())
                            <div class="row no-gutters ideas-edit">
                                <div class="col">
                                    <a href="{!! action('PublicTopicController@edit', ['cbKey' => $cbKey,'topicKey' => $topicKey, 'type' => $type, 'f'=> 'topic']) !!}"><i class="fa fa-pencil" aria-hidden="true"></i></a>
                                </div>
                            </div>
                            {{--  <div class="row no-gutters ideas-delete">
                                <div class="col">
                                    <a href="javascript:oneDelete('{!! action('PublicTopicController@delete', ['cbKey' => $cbKey,'topicKey' => $topicKey, 'type' => $type]) !!}')"><i class="fa fa-times" aria-hidden="true"></i></a>
                                </div>
                            </div>  --}}
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{--    <div class="banner-voting-info">
        @if(!ONE::isAuth())
            <div id="voteBanner" class="row banner-voting-info votes-forbidden">
                <div class="col-12 banner-voting-col">
                    <a href="#" data-toggle="modal" data-target="#loginModal" onclick="login();">
                        Por favor faça Login para participar
                    </a>
                </div>
            </div>
        @else
            @if(!empty($voteType))
                @php
                    $enableButtonConfirm = false;
                @endphp
            <!-- Banner for votes forbidden -->
                <div id="votesInfoBar" class="votes-info-bar votesInfoBar">
                    <div class="container">
                        <div class="row no-gutters">
                            <div class="col-md-6 col-12">
                                @foreach($voteType as $vt)
                                    @php
                                        $requiresConfirm = !empty($vt["genericConfigurations"]["boolean_requires_confirm"]) ? $vt["genericConfigurations"]["boolean_requires_confirm"] : 0;
                                        if($requiresConfirm == 1){
                                            $enableButtonConfirm = true;
                                        }
                                    @endphp
                                    <div class="info">
                                        Available votes <strong> {{ $vt["remainingVotes"]->total }}</strong>
                                    </div>
                                    <div class="between-bar"></div>
                                    <div class="info">
                                        Votes used <strong>{{ $vt["remainingVotes"]->user_votes }}</strong>
                                    </div>
                                @endforeach
                            </div>
                            @if($enableButtonConfirm == true)
                                <div class="col-md-6 col-12 text-right">
                                @if( $vt['submitedDate'] == null )
                                    <!-- Banner for votes allowed and enough to submit -->
                                        <a href="{{ action("PublicCbsController@showTopicsVoted",["cbKey"=>$cbKey,"type"=>$type]) }}" class="submit-votes-btn">{{ ONE::transCb('submit_votes', !empty($cb) ? $cb->cb_key : $cbKey)}}</a>
                                @else
                                    <!-- Banner for votes allowed and enough to submit -->
                                        {{ ONE::transCb('vote_submited', !empty($cb) ? $cb->cb_key : $cbKey)}}
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        @endif
    </div>--}}

    <div class="container idea-content">
        <div class="row">
            <div class="col-lg-4 col-md-5 col-sm-12 col-12 col-details med-grey-bg">
                <div class="row">
                    @if(!empty($filesByType) && count($filesByType) >0 && isset($filesByType) && !empty(reset($filesByType)) )
                    <div class="col-12 idea-image">
                        <img src="{{ action('FilesController@download', [$filesByType->images[0]->file_id, $filesByType->images[0]->file_code, 'inline'=> true] ) }}"/>
                    </div>
                    @endif
                    
                    @foreach(!empty($parameters)? $parameters : [] as $parameter)
                        {{-- Do not process private parameters --}}
                        @if(!isset($parameter->private) || $parameter->private != 0)
                            @continue
                        @endif
                        
                        @if($parameter->code == 'google_maps' && isset($parameter->pivot->value) && !empty($parameter->pivot->value))
                            <div class="col-12 idea-map">
                                {!! Form::oneMaps('mapView',"",isset($parameter->pivot->value)?$parameter->pivot->value : null,["readOnly" => true , 'categoryIcon' => $categoryIcon, 'defaultLocation' => $parameter->pivot->value, "height" => "150px", 'width' => '100%']) !!}
                            </div>
                        @endif
                    @endforeach

                    @if(ONE::checkCBsOption($configurations, 'SHOW-STATUS'))
                        @php
                            $status = $topic->status[0]->status_type;
                        @endphp

                        @if(isset($status) && $status->code == 'approved')
                            <div class="col-12 idea-status green">
                                <div class="row no-margin status-row">
                                    <div class="col-6 status-label">{{--{{ONE::getStatusTranslation($translations, 'topic_detail_status')}}--}}{{ONE::transCb('cb_topic_detail_status', !empty($cb) ? $cb->cb_key : $cbKey)}}</div>
                                    <div class="col-6 status-info">{{--{{ONE::getStatusTranslation($translations, $topic->active_status->status_type->code)}}--}}
                                        {{ONE::transCb('topic_approved', !empty($cb) ? $cb->cb_key : $cbKey)}}
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="col-12 idea-status red">
                                <div class="row no-margin status-row">
                                    <div class="col-12 text-center">{{--{{ONE::getStatusTranslation($translations, 'topic_detail_status')}}--}}{{--{{ONE::transCb('cb_topic_waiting_moderation', !empty($cb) ? $cb->cb_key : $cbKey)}}--}}
                                        {{ONE::transCb('topic_not_accepted', !empty($cb) ? $cb->cb_key : $cbKey)}}
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endif
                    
                    <div class="col-12 idea-details">
                        @foreach(!empty($parameters)? $parameters : [] as $parameter)
                            {{-- Do not process private parameters --}}
                            @if(!isset($parameter->private) || $parameter->private != 0)
                                @continue
                            @endif

                            {{-- If not a side parameter continue --}}
                            @if(!isset($parameter->side) || $parameter->side != 1)
                                @continue
                            @endif

                            {{-- Do not include Google Maps, Images and Files in side --}}
                            @if($parameter->type->code == 'google_maps' || $parameter->type->code == 'images' || $parameter->type->code === 'files')
                                @continue
                            @endif
                        
                            <div class="row detail-row">
                                <div class="col-6 detail-label" style="color: white!important">{{ $parameter->parameter ?? '' }}</div>
                                <div class="col-6 detail-info">
                                    {{-- PARAMETER TYPE: checkbox --}}
                                    @if($parameter->type->code == "check_box")
                                        @php $options = explode(",",$parameter->pivot->value); @endphp
                                        @if(count($options) > 0  && !in_array(0,$options))
                                            @foreach($options as $option)
                                                <?php
                                                    $label = collect($parameter->options)->where('id','=',$option)->first();
                                                    if(isset($label->label)) {
                                                        echo "<div class='topic_param_option'>".$label->label."</div>";
                                                    }
                                                ?>
                                            @endforeach
                                        @endif
                                    {{-- PARAMETER TYPE: dropdown --}}
                                    @elseif($parameter->type->code == "dropdown"
                                            || $parameter->type->code == "category"
                                            || $parameter->type->code == "budget"
                                            || $parameter->type->code == "radio_buttons")
                                        {{isset($dropDownOptions[$parameter->pivot->value])? $dropDownOptions[$parameter->pivot->value] : null}}
                                    {{-- PARAMETER TYPE: numeric --}}
                                    @elseif ($parameter->type->code == 'numeric')
                                        <i class="fa fa-eur" aria-hidden="true"></i>
                                        {{ number_format($parameter->pivot->value, 0, ',', '.') }}
                                    {{-- PARAMETER TYPE: other --}}
                                    @else
                                        {{isset($parameter->pivot->value)? $parameter->pivot->value : ""}}
                                    @endif
                                </div>
                            </div>
                        @endforeach
                        
                        <div class="row detail-row">
                            <div class="col-6 detail-label" style="color: white!important">{{ ONE::transCb('proposal_topic_owner', !empty($cb) ? $cb->cb_key : $cbKey) }}</div>
                            <div class="col-6 detail-info">
                                @if(isset($topic->created_on_behalf) && !empty($topic->created_on_behalf))
                                    {{ $topic->created_on_behalf }}
                                @else
                                    {{isset($usersNames[$topic->created_by]['name']) ? $usersNames[$topic->created_by]['name'] : ONE::transCb("proposal_topic_anonymous", !empty($cb) ? $cb->cb_key : $cbKey) }}
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="offset-lg-1 col-lg-7 col-md-7 col-sm-12 col-12 col-text">
                <div class="row">
                    <div class="col-12 idea-title">{!! $topic->title !!}</div>
                </div>
                <div class="row">
                    <div class="col-12 idea-summary">{!! $topic->summary !!}</div>
                </div>
                <div class="row">
                    <div class="col-12 idea-description">{!! $topic->contents !!}</div>
                </div>

                {{-- Show topic parameters --}}
                @foreach(!empty($parameters)? $parameters : [] as $parameter)
                    {{-- Do not process private parameters --}}
                    @if(!isset($parameter->private) || $parameter->private != 0)
                            @continue
                    @endif

                    {{-- If side parameter continue --}}
                    @if(isset($parameter->side) && $parameter->side == 1)
                        @continue
                    @endif

                    {{-- Do not include Google Maps, Images and Files in side --}}
                    @if($parameter->type->code == 'google_maps' || $parameter->type->code == 'images' || $parameter->type->code === 'files')
                            @continue
                    @endif

                    @if(isset($parameter->highlight) && $parameter->highlight)
                        <div class="row idea-param text-highlights light-grey-bg">
                    @else
                        <div class="row idea-param">
                    @endif

                        <div class="col-12 title idea-param-title">{{ $parameter->parameter ?? '' }}</div>
                        <div class="col-12 text idea-param-body">
                            {{-- PARAMETER TYPE: checkbox --}}
                            @if($parameter->type->code == "check_box")
                                @php $options = explode(",",$parameter->pivot->value); @endphp
                                @if(count($options) > 0  && !in_array(0,$options))
                                    @foreach($options as $option)
                                        <?php
                                            $label = collect($parameter->options)->where('id','=',$option)->first();
                                            if(isset($label->label)) {
                                                echo "<div class='topic_param_option'>".$label->label."</div>";
                                            }
                                        ?>
                                    @endforeach
                                @endif
                            {{-- PARAMETER TYPE: dropdown --}}
                            @elseif($parameter->type->code == "dropdown"
                                    || $parameter->type->code == "category"
                                    || $parameter->type->code == "budget"
                                    || $parameter->type->code == "radio_buttons")
                                {{isset($dropDownOptions[$parameter->pivot->value])? $dropDownOptions[$parameter->pivot->value] : null}}
                            {{-- PARAMETER TYPE: numeric --}}
                            @elseif ($parameter->type->code == 'numeric')
                                <i class="fa fa-eur" aria-hidden="true"></i>
                                {{ number_format($parameter->pivot->value, 0, ',', '.') }}
                            {{-- PARAMETER TYPE: other --}}
                            @else
                                {{isset($parameter->pivot->value)? $parameter->pivot->value : ""}}
                            @endif
                        </div>
                    </div>
                @endforeach

                @if(isset($filesByType) && !empty($filesByType))
                    @if(isset($filesByType->{"files"}) && !empty($filesByType->{"files"}))
                    <div class="files">
                        @foreach($filesByType as $files)
                            @foreach($files as $file)
                                <div class="file-row">
                                    <i class="fa fa-file-pdf-o" aria-hidden="true"></i> {{$file->name}}<a href="{{ action('FilesController@download', ['id' => $file->file_id,'code' => $file->file_code])}}" class="file-download-btn"><i class="fa fa-download" aria-hidden="true"></i></a>
                                </div>
                                {{--<p><a class="files-link" href="{{ action('FilesController@download', ['id' => $file->file_id,'code' => $file->file_code])}}"><span class="fa fa-download" style="margin-right: 10px" aria-hidden="true"></span>{{$file->name}}</a></p>--}}
                            @endforeach
                        @endforeach
                    </div>
                    @endif
                    @if(isset($filesByType->{"images"}) && !empty($filesByType->{"images"}))
                        <div id="carouselImages" class="row carousel slide image-gallery" data-ride="carousel">
                            <div class="col-12 content-title">{{ONE::transCb('cb_topic_image_gallery', !empty($cb) ? $cb->cb_key : $cbKey)}}</div>
                            <div class="carousel-inner" role="listbox">
                                {{--<div class="col-md-4">--}}
                                {{--<div class="card" data-toggle="modal" data-target="#imgModal">--}}
                                {{--<div class="card-img-top card-img-top-250">--}}
                                {{--<img class="img-fluid" src="{{action('FilesController@download', [$filesByType->{"images"}[0]->file_id, $filesByType->{"images"}[0]->file_code])}}" alt="Carousel 1">--}}
                                {{--</div>--}}
                                {{--</div>--}}
                                {{--</div>--}}
                                {{--</div>--}}
                                @php $j = 0; @endphp
                                @for($i=0; $i<count($filesByType->{"images"}); $i++)
                                    @if($j == 0)
                                        <div class="carousel-item {{ $i == 0 ? ' active' : '' }}">
                                            @endif

                                            <div class="col-md-4">
                                                <div class="card" data-toggle="modal" data-target="#imgModal">
                                                    <div class="card-img-top card-img-top-250">
                                                        <img class="img-fluid" src="{{action('FilesController@download', [$filesByType->{"images"}[$i]->file_id, $filesByType->{"images"}[$i]->file_code])}}" alt="Carousel 1">
                                                    </div>
                                                </div>
                                            </div>
                                            @php $j++; @endphp
                                            @if($j == 3 || $i == (count($filesByType->{"images"}) - 1))
                                        </div>
                                        @php $j = 0; @endphp
                                    @endif
                                @endfor
                                {{--</div>--}}
                            </div>

                            @if(count($filesByType->{"images"})>=3)
                                <a class="carousel-control-prev" href="#carouselImages" role="button" data-slide="prev">
                                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                    <span class="sr-only">{{ONE::transCb('cb_topic_previous_image', !empty($cb) ? $cb->cb_key : $cbKey)}}</span>
                                </a>
                                <a class="carousel-control-next" href="#carouselImages" role="button" data-slide="next">
                                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                    <span class="sr-only">{{ONE::transCb('cb_topic_next_image', !empty($cb) ? $cb->cb_key : $cbKey)}}</span>
                                </a>
                            @endif
                        </div>

                        <!-- Modal to open image -->
                        <div class="modal fade" id="imgModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog image" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="Image" id="exampleModalLabel">{{ONE::transCb('cb_topic_image_modal_title', !empty($cb) ? $cb->cb_key : $cbKey)}}</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <img id="imgModalContent">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- To open the image inside modal -->
                        <script>
                            var button = document.getElementsByClassName('img-fluid');
                            var modalContent = document.getElementById('imgModalContent');
                            for (var i = 0; i < button.length; i++) {
                                (function(index) {
                                    button[index].onclick = function() {
                                        $('#imgModal').modal('show');
                                        modalContent.src = this.src;
                                    }
                                }(i));
                            }
                        </script>
                    @endif
                @endif

            </div>
        </div>
    </div>


    <div class="container-fluid med-grey-bg" style="margin-top:-30px;">
        <div class="row">
            <div class="col-12">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-4 col-md-5 col-sm-12 col-12">
                            @if(!empty($voteType) && empty($topic->closed))
                                @foreach($voteType as $vt)
                                    @php
                                        /* Has Voting ended */
                                        $vt['voteEnded'] = (isset($vt['eventVote'])
                                                                && ((is_array($vt['eventVote'])
                                                                && isset($vt['eventVote']["end_date"])
                                                                && \Carbon\Carbon::parse($vt['eventVote']["end_date"])->isPast())
                                                             || (isset($vt['eventVote']->end_date)
                                                                && \Carbon\Carbon::parse($vt['eventVote']->end_date)->isPast())));
                                        $showVoteResults = !empty($vt["genericConfigurations"]["show_vote_results"]) ? $vt["genericConfigurations"]["show_vote_results"] : 0;
                                        $first  = \Carbon\Carbon::parse($vt['eventVote']->start_date, $tz);
                                        $second = \Carbon\Carbon::parse($vt['eventVote']->end_date, $tz);
                                        $isInCurrentDayBetween = \Carbon\Carbon::now($tz)->between($first, $second);
                                    @endphp

                                    @if(isset($vt["genericConfigurations"])  && (isset($vt['voteEnded']) && !$vt['voteEnded']) && $isInCurrentDayBetween)
                                        @php
                                            $counterVotes = json_decode($vt["eventVote"]->_count_votes);
                                            $topicKey = $topic->topic_key;
                                        @endphp
                                        @if( $vt["method"] == "VOTE_METHOD_LIKE" )
                                            {!! Html::oneLikesDemo($topic->topic_key,
                                                                   $cbKey,
                                                                   $vt["key"],
                                                                   (isset($vt["genericConfigurations"])
                                                                        && array_key_exists("show_total_votes",$vt["genericConfigurations"])
                                                                        && $vt["genericConfigurations"]["show_total_votes"] == 1)
                                                                        ? (isset($counterVotes->topics->$topicKey)
                                                                        ? $counterVotes->topics->$topicKey->positive : '0' ): "",
                                                                   (isset($vt["genericConfigurations"])
                                                                        && array_key_exists("show_total_votes", $vt["genericConfigurations"])
                                                                        && $vt["genericConfigurations"]["show_total_votes"] == 1)
                                                                        ? (isset($counterVotes->topics->$topicKey)
                                                                        ? $counterVotes->topics->$topicKey->negative : '0' ): "",
                                                                   !empty($vt["allReadyVoted"][$topic->topic_key]) ? $vt["allReadyVoted"][$topic->topic_key] : null,
                                                                   $vt["configurations"],
                                                                   [],
                                                                   (!ONE::isAuth()) ? true : (isset($vt["disabled"]) ? ($vt["disabled"] ? true : false) : false),
                                                                   true,
                                                                   [],
                                                                   (!$vt['existVotes']) ? true : false) !!}
                                        @elseif( $vt["method"] == "VOTE_METHOD_MULTI" )
                                            {!! Html::oneMultiVotingDemo($topic->topic_key,
                                                                         $cbKey,
                                                                         $vt["key"],
                                                                         (isset($vt["genericConfigurations"])
                                                                                && array_key_exists("show_total_votes",$vt["genericConfigurations"])
                                                                                && $vt["genericConfigurations"]["show_total_votes"] == 1)
                                                                                ? (isset($counterVotes->topics->$topicKey)
                                                                                ? $counterVotes->topics->$topicKey->positive : '0' ): "",
                                                                         !empty($vt["allReadyVoted"][$topic->topic_key]) ? $vt["allReadyVoted"][$topic->topic_key] : false,
                                                                         $vt["configurations"],
                                                                         [],
                                                                         ONE::isAuth(),
                                                                         false,
                                                                         true,
                                                                         false,
                                                                         true,
                                                                         $loginLevels,
                                                                         isset($vt['submitedDate']),
                                                                         $type) !!}
                                        @elseif( $vt["method"] == "VOTE_METHOD_NEGATIVE" )
                                            {{--                                            {!! Html::oneNegativeVotingDemo($topic->topic_key,
                                                                                                                        $cbKey,
                                                                                                                        $vt["key"],
                                                                                                                        (isset($vt["genericConfigurations"])
                                                                                                                            && array_key_exists("show_total_votes",$vt["genericConfigurations"])
                                                                                                                            && $vt["genericConfigurations"]["show_total_votes"] == 1)
                                                                                                                            ? (isset($counterVotes->topics->$topicKey)
                                                                                                                            ? $counterVotes->topics->$topicKey->positive : '0' ): "",
                                                                                                                        (isset($vt["genericConfigurations"])
                                                                                                                            && array_key_exists("show_total_votes", $vt["genericConfigurations"])
                                                                                                                            && $vt["genericConfigurations"]["show_total_votes"] == 1)
                                                                                                                            ? (isset($counterVotes->topics->$topicKey)
                                                                                                                            ? $counterVotes->topics->$topicKey->negative : '0' ): "",
                                                                                                                        !empty($vt["allReadyVoted"][$topic->topic_key]) ? $vt["allReadyVoted"][$topic->topic_key] : null ,
                                                                                                                        $vt["configurations"],[],
                                                                                                                        (isset($status) || !ONE::isAuth()) ? true : ((isset($vt["disabled"]) && $vt["disabled"])? true : false)) !!}--}}
                                        @endif
                                    @elseif(isset($vt) && isset($vt['voteEnded']) && $showVoteResults ==1 )
                                        <div class="votes-box">
                                            <div class="row pad-parameters-bottom margin-top-15 text-uppercase">
                                                <div class="col-12 col-xs-12 text-center top-1 bottom-1 bg-primary" style="">
                                                    <div class="show-vote-results">
                                                        @php
                                                            $countResult = 0;
                                                            $counterVotes = json_decode($vt["eventVote"]->_count_votes);
                                                            $topicKey = $topic->topic_key;
                                                            if(!empty($counterVotes->topics->$topicKey)){
                                                                $countResult = ($counterVotes->topics->$topicKey->positive) - ($counterVotes->topics->$topicKey->negative) ;
                                                            }
                                                        @endphp
                                                        <strong id="total_counter_votes_{{ $vt["key"] }}">{{ $countResult }}</strong>
                                                        {!! ONE::transCb('topic_total_votes', !empty($cb) ? $cb->cb_key : $cbKey); !!}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="offset-lg-1 col-lg-7 justify-content-end social-buttons">
        {{--
        <div class="mr-auto follow-btn">
            <a href="#" class="">
                Follow
            </a>
        </div>
        --}}
        @if(ONE::checkCBsOption($configurations, 'ALLOW-SHARE') && !empty($shareLinks))
            @foreach($shareLinks as $key => $shareLink)
                <a href="{{ $shareLink["link"] }}" class="{{ $key }} share-link" target="_blank">
                    <div class="share-social-btn">
                        <div style="display:flex;justify-content:center;align-items:center;height:100%;">
                            <div>
                                <i class="fa fa-{{ $key }}" aria-hidden="true"></i> {{ONE::transCb("cb_topic_share", !empty($cb) ? $cb->cb_key : $cbKey)}}
                            </div>
                        </div>
                    </div>
                </a>
            @endforeach
        @endif
        {{--
        <div class="share-social-btn">
            <a href="#" class="">
                <i class="fa fa-twitter" aria-hidden="true"></i> Tweet
            </a>
        </div>
        --}}
    </div>

    {{--@if(!ONE::checkOperationSchedulePermission($operationSchedules,"cenas","cenas1"))--}}
    {{--<div class="row col-details light-grey-bg no-gutters">--}}
    {{--<div class="col-lg-4 col-md-5 col-sm-12 col-12 idea-details dark-grey-bg">--}}
    {{--</div>--}}
    {{--<div class="offset-lg-1 col-lg-7 col-md-7 col-sm-12 col-12 light-grey-bg col-text">--}}
    {{--@foreach(!empty($parameters)? $parameters : [] as $parameter)--}}
    {{--<div class="text-highlights">--}}
    {{--@if($parameter->parameter_code == "checkbox_test" && !empty($parameter->pivot->value))--}}
    {{--<div class="title">--}}
    {{--<i class="fa fa-flag" aria-hidden="true"></i>  {{  ONE::transCb("cb_topic_technical_analysis", !empty($cb) ? $cb->cb_key : $cbKey) }}--}}
    {{--</div>--}}
    {{--<div class="text">--}}
    {{--{{$parameter->pivot->value}}--}}
    {{--</div>--}}
    {{--@endif--}}
    {{--</div>--}}
    {{--@endforeach--}}
    {{--</div>--}}
    {{--</div>--}}
    {{--@endif--}}

    @if(ONE::checkCBsOption($configurations, 'DISABLE-COMMENTS-FUNCTIONALITY'))
        <div class="container light-grey-bg idea-comments">
            <div class="justify-content-center">
                <div class="col-lg-9 col-md-12 mx-auto">
                    <div class="row no-gutters comment-row">
                        <div class="col comments-title">
                            {{ONE::transCb("cb_topic_comments", !empty($cb) ? $cb->cb_key : $cbKey)}} <span>{{$countComments}}</span>
                        </div>
                    </div>
                    @foreach($comments as $key => $comment)
                        @if($comment['flag'] == 'moderated')
                            <div class="row comment-row">
                                <div class="col-lg-11 mx-auto">
                                    <div class="row no-gutters">
                                        <div class="col primary-color user-info">
                                            <div class="user-img" style="background-image: url('images/brunette-15963_640.jpg')"></div>
                                            <div class="user-name">
                                                @if( !empty($usersNames[$comment['details']->created_by]["name"]))
                                                    {{ $usersNames[$comment['details']->created_by]["name"] }}
                                                @else
                                                    {{ $comment['details']->created_by }}
                                                @endif
                                            </div>
                                            <div class="time">
                                                {{$comment['details']->updated_at}}
                                            </div>

                                        </div>
                                        <div class="time">
                                            {{$comment['details']->updated_at}}
                                        </div>
                                    </div>
                                    <div class="col med-grey-bg message">
                                        {!! $comment['details']->contents !!}
                                    </div>
                                </div>
                            </div>
                </div>
                @else
                    <div class="row comment-row">
                        <div class="col-lg-11 mx-auto">
                            <div class="row no-gutters">
                                <div class="col primary-color user-info missing-moderation-left" id="missing-moderation-left">
                                    <div class="user-img" style="background-image: url('images/brunette-15963_640.jpg')"></div>
                                    <div class="user-name">
                                        {{--{{dd($usersNames)}}--}}
                                        @if( !empty($usersNames[$comment['details']->created_by]["name"]))
                                            {{ $usersNames[$comment['details']->created_by]["name"] }}
                                        @else
                                            {{ $comment['details']->created_by }}
                                        @endif
                                    </div>
                                    <div class="col med-grey-bg message missing-moderation-message" id="missing-moderation-message">
                                        <p id="comment-moderation"> {{ONE::transCb("cb_topic_comment_waiting_moderation", !empty($cb) ? $cb->cb_key : $cbKey)}} <i class="fa fa-exclamation-circle"></i></p>
                                        {!! $comment['details']->contents  !!}
                                    </div>
                                </div>
                                <div class="col med-grey-bg message missing-moderation-message" id="missing-moderation-message">
                                    <p id="comment-moderation"> {{ONE::transCb("proposal_topic_comment_waiting_moderation", !empty($cb) ? $cb->cb_key : $cbKey)}} <i class="fa fa-exclamation-circle"></i></p>
                                    {!! $comment['details']->contents  !!}
                                </div>
                            </div>
                        </div>
                        @endif
                        @endforeach
                        {{--<div class="row no-gutters comment-row">--}}
                        {{--<div class="col-lg-11 mx-auto">--}}
                        {{--<div class="input-group comments-input">--}}
                        {{--<textarea class="form-control" id="exampleTextarea" rows="3"></textarea>--}}
                        {{--<span class="input-group-addon" id="basic-addon2"><i class="fa fa-paper-plane" aria-hidden="true"></i></span>--}}
                        {{--</div>--}}
                        {{--</div>--}}
                        {{--</div>--}}
                        @if(ONE::checkCBsOption($configurations, 'ALLOW-COMMENTS'))
                            @if(ONE::isAuth() || ONE::checkCBsOption($configurations, 'COMMENTS-ANONYMOUS'))
                                <form name="topic" accept-charset="UTF-8" method="POST"
                                      onsubmit="return validateTextArea('sendComment');"
                                      action="<?php echo action('PublicPostController@store', ['topicKey' => $topicKey, 'type' => $type, 'cbKey' => $cbKey]) ?>">
                                    <div class="row no-gutters comment-row">
                                        <div class="col-lg-11 mx-auto">
                                            <div class="input-group comments-input">
                                                <textarea id="sendComment" name="contents" class="form-control" rows="3" placeholder="{{ ONE::transCb("cb_topic_comment_placeholder", !empty($cb) ? $cb->cb_key : $cbKey) }}" ></textarea>
                                                <button class="light-blue-button-send-message send-message float-right" type="submit" style="padding: 0">
                                                    <span class="input-group-addon" id="basic-addon2" style="height: 89px"><i class="fa fa-paper-plane" aria-hidden="true" style="color: white"></i></span>
                                                </button>
                                                <div class="loader float-right" style="display: none;padding-top: 13px;margin-right: 10px;"><img src="{{ asset('images/opjp/bluePreLoader.gif') }}" alt="Loading" style="width: 20px;"></div>
                                            </div>
                                        </div>
                                    </div>
                                    {{--</div>--}}
                                    <input type="hidden" name="_token" value="<?php echo  csrf_token() ?>"/>
                                </form>
                            @endif
                        @endif
                    </div>
            </div>
        </div>
    @endif


    <!-- Modal errors -->
    <div class="modal fade" id="errorModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog image" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="Image" id="exampleModalLabel">{{ONE::transCb('cb_topic_warning', !empty($cb) ? $cb->cb_key : $cbKey)}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <span id="error_message"></span>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

@endsection


@section('scripts')
    <script>
        $(".votesInfoBar").sticky({topSpacing:45});
    </script>
@endsection
