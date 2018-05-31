<style>
    .vote-event-details{
        margin: 0 0 8px 0!important;
    }

    .vote-event-details > label{
        margin-top: 0;
        margin-bottom: 5px;
    }
</style>
@if(!empty($cb))
    <div class="card flat topic-data-header margin-bottom-20">

        <p class="vote-event-details">
            <label for="contentStatusComment">
                {{trans('privateCbs.pad')}}</label>
            {{$cb->title}}
        </p>

        <!-- Vote Event details -->
        <div id="vote-more-details"></div>

    </div>
@endif

@include('private.cbs.cbVoteAnalysis.cbDetailsScript')