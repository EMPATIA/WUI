<div id="moreDetails" class="card flat topic-data-header margin-bottom-30" style="display:none;">
    <div class="margin-bottom-20" style="padding-top:20px">
        <div class="row">
            <div class="col-md-12 voteAnalysis-total">
                <div class="row box-body">
                    <div class="col text-center">
                        <div>
                            <img src="{{asset('/images/total_voters.png')}}" style="width: 5em">
                        </div>
                        <div>
                            <strong>{{trans('privateCbsVoteAnalysis.total_voters')}}</strong>
                        </div>
                        <div id="total_voters">
                            {{ $statisticsTotalSummary->total_users_voted ?? null }}
                        </div>
                    </div>
                    <div class="col text-center">
                        <div>
                            <img src="{{asset('/images/total_votes.png')}}" style="width: 5em">
                        </div>
                        <div>
                            <strong>{{trans('privateCbsVoteAnalysis.total_votes')}}</strong>
                        </div>
                        <div id="total_votes">
                            {{$statisticsTotalSummary->total ?? null}}
                        </div>
                    </div>
                    <div class="col text-center">
                        <div>
                            <img src="{{asset('/images/total_votes_submitted.png')}}" style="width: 5em">
                        </div>
                        <div>
                            <strong>{{trans('privateCbsVoteAnalysis.total_votes_submitted')}}</strong>
                        </div>
                        <div id="total_votes">
                            {{$statisticsTotalSummary->total_submitted ?? null}}
                        </div>
                    </div>
                    <div class="col text-center">
                        <div>
                            <img src="{{asset('/images/positive_votes.png')}}" style="width: 5em">
                        </div>
                        <div>
                            <strong>{{trans('privateCbsVoteAnalysis.total_positive_votes')}}</strong>
                        </div>
                        <div id="total_positives_votes">
                            {{$statisticsTotalSummary->total_positives ?? null}}
                        </div>
                    </div>
                    <div class="col text-center">
                        <div>
                            <img src="{{asset('/images/negative_votes.png')}}" style="width: 5em">
                        </div>
                        <div>
                            <strong>{{trans('privateCbsVoteAnalysis.total_negative_votes')}}</strong>
                        </div>
                        <div id="total_negative_votes">
                            {{$statisticsTotalSummary->total_negatives ?? null}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="row">
    <div class="col-12">
        <div class="text-right margin-bottom-10">
            <div class="colors btn-group" data-toggle="buttons">
                <label class="btn btn-primary">
                    <input type="radio" name="view_submitted" value="1" onchange="javascript:renderSelectedOptions()"> {{ trans('privateUserAnalysis.submitted') }}
                </label>
                <label id="default-view-all" class="btn btn-primary active">
                    <input type="radio" name="view_submitted" value="0" checked onchange="javascript:renderSelectedOptions()"> {{ trans('privateUserAnalysis.all') }}
                </label>
            </div>
        </div>
    </div>
</div>
