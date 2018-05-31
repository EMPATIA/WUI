@php
    // $voteEventObj = empty($voteEventObj) ? Session::get("voteEvent") : $voteEventObj;
    // var_dump($voteEventObj);

/*if(!empty($voteEventObj))
  var_dump("$voteEventObj->start_date >=  ".Carbon\Carbon::now()->toDateTimeString()."  &&  ".Carbon\Carbon::now()->toDateTimeString()." <= $voteEventObj->end_date " );*/
@endphp
<script>
var strMoreDetails = "";
var voteStatus = "";
@if(!empty($voteEventObj))
    /* Vote details */
    @if(!empty($voteEventObj->method->name))
        strMoreDetails = '<p class="vote-event-details"><label for="contentStatusComment">{{trans('privateCbs.vote_event')}}</label> {!! $voteEventObj->method->name !!}</p>';
    @endif

    @if(!empty($voteEventObj->start_date))
        strMoreDetails += '<p class="vote-event-details"><label for="contentStatusComment">{{trans('privateCbs.start_date')}}</label> {!! substr($voteEventObj->start_date,0,10) !!} {!! $voteEventObj->start_time !!} </p>';
    @endif

    @if(!empty($voteEventObj->end_date))
        strMoreDetails += '<p class="vote-event-details"><label for="contentStatusComment">{{trans('privateCbs.end_date')}}</label> {!! substr($voteEventObj->end_date,0,10) !!} {!! $voteEventObj->end_time !!}</p>';
    @endif

    @if(Carbon\Carbon::now()->toDateTimeString() >= $voteEventObj->start_date && Carbon\Carbon::now()->toDateTimeString() <= $voteEventObj->end_date  )
        strMoreDetails += '<p class="vote-event-details"><label for="contentStatusComment">{{trans('privateCbs.votation_status')}}</label> {{trans('privateCbs.votation_is_going_on')}} </p>';
    @elseif(Carbon\Carbon::now()->toDateTimeString() < $voteEventObj->start_date )
        strMoreDetails += '<p class="vote-event-details"><label for="contentStatusComment">{{trans('privateCbs.votation_status')}}</label> {{trans('privateCbs.votation_didnt_started')}} </p>';
    @elseif(Carbon\Carbon::now()->toDateTimeString() > $voteEventObj->end_date )
        strMoreDetails += '<p class="vote-event-details"><label for="contentStatusComment">{{trans('privateCbs.votation_status')}}</label> {{trans('privateCbs.votation_day_just_passed_away')}} </p>';
    @endif

    if(strMoreDetails!="")
        $("#vote-more-details").html(strMoreDetails);
@endif
</script>