<div class="row">
    <div class="col-12">
        <div class="text-right margin-bottom-10">
            <div class="colors btn-group" data-toggle="buttons" style="pointer-events: none;cursor: default;opacity:0.8;">
                <label class="btn btn-primary">
                    <input type="radio" name="view_submitted" value="1" autocomplete="off" disabled > {{ trans('privateUserAnalysis.view_submitted') }}
                </label>
                <label id="default-view-all" class="btn btn-primary btn-selected">
                    <input type="radio" name="view_submitted" value="0" autocomplete="off" checked> {{ trans('privateUserAnalysis.all') }}
                </label>
            </div>
        </div>
    </div>
</div>

<div class="card flat topic-data-header margin-bottom-20">
    <div class="row">
        <div class="col-lg-3 col-md-6 col-sm-6 col-12 text-center">
            <div class="">
                <img src="{{asset('/images/total_voters.png')}}" style="width: 5em">
            </div>
            <div class="">
                <strong>{{trans('privateCbsVoteAnalysis.total_voters')}}</strong>
            </div>
            <div id="total_voters">&nbsp;</div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 col-12 text-center">
            <div class="">
                <img src="{{asset('/images/total_votes.png')}}" style="width: 5em">
            </div>
            <div class="">
                <strong>{{trans('privateCbsVoteAnalysis.total_votes')}}</strong>
            </div>
            <div id="total_votes">&nbsp;</div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 col-12 text-center">
            <div class="">
                <img src="{{asset('/images/positive_votes.png')}}" style="width: 5em">
            </div>
            <div class="">
                <strong>{{trans('privateCbsVoteAnalysis.total_positive_votes')}}</strong>
            </div>
            <div id="total_positives"></div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 col-12 text-center">
            <div class="">
                <img src="{{asset('/images/negative_votes.png')}}" style="width: 5em">
            </div>
            <div>
                <strong>{{trans('privateCbsVoteAnalysis.total_negative_votes')}}</strong>
            </div>
            <div id="total_negative_votes">&nbsp;</div>
        </div>
    </div>
</div>