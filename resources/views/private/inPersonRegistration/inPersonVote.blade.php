@extends('private.inPersonRegistration.indexInPersonVote')

@section('content')
    <div class="container">
        @if(isset($notSubmitted) && isset($existVotesForSubmit) && $notSubmitted && $existVotesForSubmit)
            <div class="vottingBox">
                <div class="row">
                    @foreach ($cbAndTopics as $cb)
                        @if(!empty($votesByCb[$cb->cb->cb_key]))
                            @foreach($votesByCb[$cb->cb->cb_key] as $vt)
                                @if( $vt["method"] == "VOTE_METHOD_MULTI" )
                                    {!! Html::oneVoteInfo(!empty($vt["remainingVotes"]) ? $vt["remainingVotes"] : null,$cb->cb)!!}
                                @endif
                            @endforeach
                        @endif
                    @endforeach
                </div>
                <div class="row">
                    <div class="pull-right">
                        <button type="button" class="vottingSubmitBtn" data-toggle="modal" data-target="#submitVotesModal"><i class="fa fa-check" aria-hidden="true"></i> {{ trans('vote.submitVoting')}}</button>
                    </div>
                    <!-- status history modal -->
                    <div class="modal fade" tabindex="-1" role="dialog" id="submitVotesModal" >
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="card-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                    <h4 class="modal-title" style="color: black;">{{trans("PublicCbs.submitVotes")}}</h4>
                                </div>
                                <div class="modal-body" style="color: black;">
                                    Tem a certeza que pretende submeter os votos?
                                    @foreach ($cbAndTopics as $cb)
                                        @if(!empty($votesByCb[$cb->cb->cb_key]))
                                            @foreach($votesByCb[$cb->cb->cb_key] as $vt)
                                                @if( $vt["method"] == "VOTE_METHOD_MULTI" )
                                                    {!! Html::oneVoteModalInfo(!empty($vt["remainingVotes"]) ? $vt["remainingVotes"] : null,$cb->cb)!!}
                                                @endif
                                            @endforeach
                                        @endif
                                    @endforeach
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary submitVotes" data='{{json_encode($votesByCb)}}'><i class="fa fa-check" aria-hidden="true"></i> {{ trans('vote.submitVoting')}}</button>
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{trans("privateCbs.close")}}</button>
                                </div>
                            </div><!-- /.modal-content -->
                        </div><!-- /.modal-dialog -->
                    </div><!-- /.modal -->
                </div>
            </div>
        @elseif(isset($notSubmitted) && !$notSubmitted)
            <div class="information-wrapper" style="margin-top: 10px">
                <div class="text-center" style="margin-top: 10px; border: solid 2px #ffffff; padding: 5px 15px; border-radius: 10px; background: transparent; color: #ffffff;">
                    <h3>
                        {{trans('vote.votesAlreadySubmitted')}}
                    </h3>
                </div>
            </div>
        @endif

    <!-- Proposals List -->
        @foreach ($cbAndTopics as $cb)
        <!-- Header -->
            <div class="contentPage-heading-wrapper">
                <div class="row">
                    <div class="col-12">
                        <h2 class="underline">
                            {{$cb->cb->title}}
                        </h2>
                    </div>
                </div>
            </div>
            @foreach($cb->topics as $topic)
                @if(($topic->active_status->status_type->code??"")!="excluded")
                    <div class="row defaultPadding">
                        <div class="col-12 ideaBox">
                            <div class="row">
                                <div class="col-md-8 col-sm-8 col-12">
                                    <h2>
                                        {{ $topic->title }}
                                    </h2>
                                </div>
                                <div class="col-md-4 col-sm-4 col-12 defaultPadding" id="idea_{{$topic->topic_key}}">
                                    @if($notSubmitted)
                                        @if(!empty($votesByCb[$cb->cb->cb_key]))
                                            @foreach($votesByCb[$cb->cb->cb_key] as $vt)
                                            @if( isset($vt["genericConfigurations"]) && $vt['existVotes'])
                                                    <div class="row">
                                                        <div class="col-12">
                                                            @if( $vt["method"] == "VOTE_METHOD_MULTI" )
                                                                {!! Html::oneMultiVoting($userKey,
                                                                                        $topic->topic_key,
                                                                                        $cb->cb->cb_key,
                                                                                        $vt["key"],
                                                                                        (isset($vt["genericConfigurations"]) && array_key_exists("show_total_votes",$vt["genericConfigurations"]) && $vt["genericConfigurations"]["show_total_votes"] == 1) ? (isset($vt["totalVotes"][$topic->topic_key])? $vt["totalVotes"][$topic->topic_key]["positive"] : '0' ): "",
                                                                                        !empty($vt["allReadyVoted"][$topic->topic_key]) ? $vt["allReadyVoted"][$topic->topic_key] : null ,
                                                                                        !empty($vt["remainingVotes"]->total) ? $vt["remainingVotes"]->total : null ,
                                                                                        $vt["configurations"]) !!}
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endif
                                            @endforeach
                                        @endif
                                    @else
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="vottingBox">
                                                    <h3>
                                                        {{trans('vote.votesAlreadySubmitted')}}
                                                    </h3>
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
    @endforeach
    <!-- End Proposals -->
    </div>

@endsection

@section('scripts')

    <!-- Dot Dot Dot -->
    <script>
        $( ".submitVotes" ).click(function() {
            $(".submitVotes").css('opacity','0.5');
            $(".submitVotes").css('pointer-events','none');
            //block buttons
            $(".votting-buttons").css('opacity','0.5');
            $(".votting-buttons").css('pointer-events','none');
            $(".vottingSubmitBtn").css('opacity','0.5');
            $(".vottingSubmitBtn").css('pointer-events','none');
            var obj = jQuery.parseJSON( $(this).attr("data") );
            // Vote
            $.ajax({
                method: 'POST', // Type of response and matches what we said in the route
                url: '{{action("InPersonRegistrationController@voteSubmit", $userKey)}}', // This is the url we gave in the route
                data: {
                    voteCbs:obj ,
                    _token:  '{{csrf_token()}}'
                }, // a JSON object to send back
                success: function (responseVote) { // What to do if we succeed

                    toastr.success("{{trans('vote.votesSubmitted')}}");
                    location.reload();
                },
                error: function (jqXHR, textStatus, errorThrown) { // What to do if we fail
                    $(".submitVotes").css('opacity','1');
                    $(".submitVotes").css("pointer-events","auto");
                    //enable buttons
                    $(".votting-buttons").css('opacity','1');
                    $(".votting-buttons").css("pointer-events","auto");
                    $(".vottingSubmitBtn").css('opacity','1');
                    $(".vottingSubmitBtn").css('pointer-events','auto');
                    toastr.error("{{trans('vote.errorTryingToSubmitVotes')}}");
                }
            });
        });


        $.each([$(".ideaContent"), $(".ideaTitle")], function (index, value) {
            $(document).ready(function () {
                value.dotdotdot({
                    ellipsis: '... ',
                    wrap: 'word',
                    aft: null,
                });
            });
        });
    </script>


@endsection