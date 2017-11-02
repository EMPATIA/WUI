@extends('private._private.index')

@section('content')


@if(count($topicReviewReplies)>0)
    @foreach($topicReviewReplies as $i=>$reply)
        <div class="box-private">
            <div class="box-header">
                <h3 class="box-title">
                    @if (One::isAdmin() || isset($hasPermission) ? $hasPermission:0)
                        <a href="{{action('TopicReviewRepliesController@show', ['type'=>$type,'cbKey'=>$cbKey, 'topicKey' => $topicKey, 'topicReviewKey' => $topicReviewKey, 'topicReviewReplyKey' =>  $reply->topic_review_reply_key])}}">
                        #{!! ++$i !!} - {!! trans('privateTopicReviewReplies.reply') !!}
                    </a>
                    @else
                        <a href="#"> #{!! ++$i !!} - {!! trans('privateTopicReviewReplies.reply') !!}</a>
                    @endif
                </h3>
            </div>
            <div class="box-body">
                <dl>
                    <dt>{!! trans('privateTopicReviewReplies.created_at') !!}</dt>
                    <dd>{{ (isset($reply) ? Carbon\Carbon::parse($reply->created_at)->toDateString() : null) }}  </dd>
                    <hr style="margin: 10px 0 10px 0">

                    <dt>{!! trans('privateTopicReviewReplies.content') !!}</dt>
                    <dd> {!! isset($reply) ? $reply->content : null !!} </dd>
                    <hr style="margin: 10px 0 10px 0">

                    <dt>{!! trans('privateTopicReviewReplies.created_by') !!}</dt>
                    <dd> {!! isset($reply) ? $reply->creator_details->name : null !!}  </dd>


                </dl>
            </div>
        </div>
    @endforeach
@else
    <div class="box-private">
        <div class="box-header">
            <h3 class="box-title">
                    {!! trans('privateTopicReviewReplies.no_replies') !!}
            </h3>
        </div>
    </div>

@endif

@endsection

@section('scripts')

    <script>

        $(function() {
            var array = ["{{ isset($type) ? $type : null }}", "{{isset($cbKey) ? $cbKey : null}}", "{{isset($topicKey) ? $topicKey : null}}","{{isset($topicReviewKey) ? $topicReviewKey : null}}"];
            getSidebar('{{ action("OneController@getSidebar") }}', 'replies', array, 'topicReviews' );
        });

    </script>
@endsection

