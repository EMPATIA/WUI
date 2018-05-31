@php
    // Timezone
    $tz = One::getCurrentTimeZone();
@endphp
<style>
    @if( empty($filesByType) && count($filesByType) == 0 )
        .idea-image{
        background-color: white!important;
        background-position: center!important;
        background-size: contain;
        background-repeat: no-repeat;
    }
    @endif
    .show-vote-results{
        padding:5px 20px;
        text-align:center;
        color:white;
        width: 100%;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }


    .red {
        background: #F7606F;
        color: white;
    }

    .ideas-grid .idea-card .status-idea.green {
        background-color: #4a7a17!important;
    }
</style>
@forelse(!empty($topics) ? $topics : [] as $i => $topic)
    @php
        $active_status = collect($topic->status)->where('active', '=', 1)->first();
    @endphp
    <div class="col-12 col-sm-6 col-md-4 idea-card primary-color color-text-primary">
        <a href="{!! action('PublicTopicController@show', [$cb->cb_key , $topic->topic_key, 'type' => $type] ) !!}" class="a-wrapper">
            @if(ONE::checkCBsOption($configurations, 'SHOW-STATUS'))
                {{--    <div class="status-idea green">
                            {{ONE::getStatusTranslation($translations, $active_status->status_type->code)}}
                        </div>
                --}}
                @if($active_status->status_type->code == "approved")
                    <div class="status-idea green">
                        {{ONE::transCb('topic_approved', !empty($cb) ? $cb->cb_key : $cbKey)}}
                        {{-- {{ONE::getStatusTranslation($translations, $active_status->status_type->code)}}--}}
                    </div>
                @else
                    <div class="status-idea red">
                        {{ONE::transCb('topic_not_accepted', !empty($cb) ? $cb->cb_key : $cbKey)}}
                        {{-- {{ONE::transCb('cb_moderated', !empty($cb) ? $cb->cb_key : $cbKey)}}--}}
                    </div>
                @endif

            @endif
            <div class="card-img" style="background-image:url('@if(!empty($filesByType) && count($filesByType) >0 && isset($filesByType[$topic->topic_key]) && !empty(reset($filesByType[$topic->topic_key])) ){{ action('FilesController@download', [$filesByType[$topic->topic_key]->file_id, $filesByType[$topic->topic_key]->file_code, 'inline' => 1, 'h' => 150, 'extension' => 'jpeg', 'quality' => 65])}} @else {{ONE::getSiteConfiguration("file_logo_first","/images/demo/LogoEmpatia-l-02.png")}}@endif');background-position:center;">
                @if(ONE::isAuth() && Session::has('user'))
                    @if(Session::get('user')->confirmed != 0)
                        @if(!empty($voteType))
                            @foreach($voteType as $vt)
                                @if(!empty($vt['allReadyVoted'][$topic->topic_key]))
                                    @if($vt['allReadyVoted'][$topic->topic_key] == 1)
                                        @if($vt['alreadySubmitted'] == true)
                                            <div class="col-12 voted-btn">
                                                <span>
                                                    {{ONE::transCb('cb_voted', !empty($cb) ? $cb->cb_key : $cbKey)}}
                                                </span>
                                            </div>
                                        @endif
                                    @endif
                                @endif
                            @endforeach
                        @endif
                    @endif
                @endif
            </div>
            <div class="title">
                {{$topic->title ?? ''}}
            </div>
            <div class="description">
                {!! strip_tags($topic->contents ?? '') ?? '' !!}
            </div>
            <div class="idea-details">
                <hr>

                {{--  <div class="detail">
                    <div class="row">
                        <div class="col-6">
                            <i class="fa fa-user" aria-hidden="true"></i>   --}}
                {{--{{ ONE::getStatusTranslation($translations, 'user') }}--}}
                {{--  {{ONE::transCb('proposal_demo.created_by')}}
            </div>
            <div class="col-6">
                {{isset($usersNames->{$topic->created_by}->name) ? $usersNames->{$topic->created_by}->name : ONE::transCb('proposal_demo.anonymous') }}
            </div>
        </div>
    </div>  --}}
                @if(ONE::checkCBsOption($configurations, 'ALLOW-COMMENTS'))
                    <div class="detail">
                        <i class="fa fa-comments" aria-hidden="true"></i> {{--{{ONE::getStatusTranslation($translations, 'comments')}}--}}{{ONE::transCb('cb_comments', !empty($cb) ? $cb->cb_key : $cbKey)}}
                        {{$topic->_count_comments ?? 0}}
                    </div>
                @endif
                @if(!empty($topic->parameters))
                    @foreach($topic->parameters as $parameter)
                        @if(!empty($parameter->visible_in_list) && $parameter->visible_in_list)
                            @if(!empty($parameter->pivot->value))
                                <div class="detail">
                                    @if ($parameter->code == 'numeric')
                                        <i class="fa fa-eur" aria-hidden="true" style="margin-right: 5px; "></i>
                                        {{ number_format($parameter->pivot->value, 0, ',', '.') }}
                                    @else
                                        <?php $options = explode(",",$parameter->pivot->value); ?>
                                        @foreach($parameter->options ?? [] as $option)
                                            @if(isset($option) and !empty($option))
                                                @if(in_array($option->id, $options))
                                                    {{$option->label ?? ''}}
                                                @endif
                                            @endif
                                        @endforeach
                                    @endif
                                </div>
                            @endif
                        @endif
                    @endforeach
                @endif
            </div>
        </a>
        @if(ONE::isAuth() && Session::has('user'))
            @if(Session::get('user')->confirmed != 0 || Session::get('SITE-CONFIGURATION.boolean_register_only_nif')==true)
                @if(!empty($voteType))
                    @foreach($voteType as $vt)
                        @if($vt['alreadySubmitted'] != true)
                            <div class="vote-container">
                                @php
                                    $vt = collect($vt)->toArray();
                                    $vt["genericConfigurations"] = collect($vt["genericConfigurations"])->toArray();
                                    $vt["configurations"] = collect($vt["configurations"])->toArray();
                                    if (!empty($vt["allReadyVoted"])) {
                                        $vt["allReadyVoted"] = collect($vt["allReadyVoted"])->toArray();
                                    } else {
                                        $vt["allReadyVoted"] = [];
                                    }
                                    $vt['voteEnded'] = (isset($vt['eventVote'])
                                                        && ((is_array($vt['eventVote'])
                                                            && isset($vt['eventVote']["end_date"])
                                                            && \Carbon\Carbon::parse($vt['eventVote']["end_date"])->isPast())
                                                        || (isset($vt['eventVote']->end_date)
                                                            && \Carbon\Carbon::parse($vt['eventVote']->end_date)->isPast())));
                                    $showVoteResults = !empty($vt["genericConfigurations"]["show_vote_results"]) ? $vt["genericConfigurations"]["show_vote_results"] : 0;


					                // Check dates for votation
                                	$first  = \Carbon\Carbon::parse($vt['eventVote']->start_date, $tz);
                                	$second = \Carbon\Carbon::parse($vt['eventVote']->end_date, $tz);
                                	$isInCurrentDayBetween = \Carbon\Carbon::now($tz)->between($first, $second);
                                @endphp
                                <div class="row">
                                    <div class="col-12">
                                        <div style="text-align: center;" class="vote-container-topics-list">
                                            @if(isset($vt["genericConfigurations"]) && $vt["genericConfigurations"]["vote_in_list"]==1 && (isset($vt['voteEnded']) && !$vt['voteEnded']))
                                                @php
                                                    /* Has Voting ended */
                                                    $vt['voteEnded'] = (isset($vt['eventVote'])
                                                                            && ((is_array($vt['eventVote'])
                                                                            && isset($vt['eventVote']["end_date"])
                                                                            && \Carbon\Carbon::parse($vt['eventVote']["end_date"])->isPast())
                                                                        || (isset($vt['eventVote']->end_date)
                                                                            && \Carbon\Carbon::parse($vt['eventVote']->end_date)->isPast())));
                                                    $showVoteResults = !empty($vt["genericConfigurations"]["show_vote_results"]) ? $vt["genericConfigurations"]["show_vote_results"] : 0;
                                                    $counterVotes = !empty($vt["eventVote"]->_count_votes) ? json_decode($vt["eventVote"]->_count_votes) : null;
                                                    $topicKey = $topic->topic_key;
                                                @endphp

					                        @if(isset($vt["genericConfigurations"])  && (isset($vt['voteEnded']) && !$vt['voteEnded']) && $isInCurrentDayBetween)
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
                                                    {{-- {!! Html::oneNegativeVotingDemo($topic->topic_key,
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
					   @endif
                                            @elseif(isset($vt) && isset($vt['voteEnded']) && $showVoteResults ==1 )
                                                <div class="votes-box">
                                                    <div class="row pad-parameters-bottom margin-top-15 text-uppercase">
                                                        <div class="col-12 col-xs-12 text-center top-1 bottom-1 bg-primary" style="color:#FFF;">
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
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                @endif
            @endif
        @endif
    </div>
@empty
    @if (is_null($originalPageToken))
        <div class="col-12" style="margin-top: 10px">
            {!!  Html::oneMessageInfo(ONE::getStatusTranslation($translations, 'no_cbs_to_display')) !!}
        </div>
    @endif
@endforelse

@if(!isset($noLoop) && !empty($pageToken))
    {{--<div class="row">--}}
    <div class="col-12">
        <a class='jscroll-next'
           href='{{ URL::action('PublicCbsController@show',collect(['cbKey' => $cbKey])->merge(($filterList ?? ['type' => $type]))->merge(['page' => $pageToken, 'layout' => 'demo','topic_status' => 'moderated'])->toArray())}}'>{{ ONE::transCb('cb_next', !empty($cb) ? $cb->cb_key : $cbKey) }}</a>
    </div>{{--
    </div>--}}
@endif
