@extends('private._private.index')

@section('content')

    <div class="row">
        <div class="col-md-12">
            @php $form = ONE::form('topicReviewReply', trans('privateTopicReviewReplies.details'))
                ->settings(["model" => isset($topicReviewReply) ? $topicReviewReply->topic_review_reply_key : null, 'id' => isset($topicReviewReply) ? $topicReviewReply->topic_review_reply_key : null])
                ->show('TopicReviewRepliesController@edit', 'TopicReviewRepliesController@delete', ['type'=>$type,'cbKey'=>$cbKey, 'topicKey' => $topicKey, 'topicReviewKey' => $topicReviewKey, 'topicReviewReplyKey' => isset($topicReviewReply) ? $topicReviewReply->topic_review_reply_key : null] , 'TopicReviewRepliesController@index', ['type'=>$type,'cbKey'=>$cbKey, 'topicKey' => $topicKey, 'topicReviewKey' => $topicReviewKey, 'topicReviewReplyKey' => isset($topicReviewReply) ? $topicReviewReply->topic_review_reply_key: null])
                ->create('TopicReviewRepliesController@store', 'TopicReviewRepliesController@index', ['type'=>$type,'cbKey'=>$cbKey, 'topicKey' => $topicKey, 'topicReviewKey' => $topicReviewKey, 'topicReviewReplyKey' => isset($topicReviewReply) ? $topicReviewReply->topic_review_reply_key : null])
                ->edit('TopicReviewRepliesController@update', 'TopicReviewRepliesController@show', ['type'=>$type,'cbKey'=>$cbKey, 'topicKey' => $topicKey, 'topicReviewKey' => $topicReviewKey, 'topicReviewReplyKey' => isset($topicReviewReply) ? $topicReviewReply->topic_review_reply_key : null])
                ->open();
            @endphp

            @if(ONE::actionType('topicReviewReply') == 'show')
                {!! Form::oneText('createdAt', trans('privateTopicReviewReplies.content'), isset($topicReviewReply) ? $topicReviewReply->created_at : null, ['class' => 'form-control', 'id' => 'createdAt', 'required']) !!}
                {!! Form::oneText('content', trans('privateTopicReviewReplies.content'), isset($topicReviewReply) ? $topicReviewReply->content : null, ['class' => 'form-control', 'id' => 'content', 'required']) !!}
                {!! Form::oneText('createdBy', trans('privateTopicReviewReplies.created_by'), isset($topicReviewReply) ? $topicReviewReply->creator_name : null, ['class' => 'form-control', 'id' => 'createdBy', 'required']) !!}

            @endif

            @if(ONE::actionType('topicReviewReply') == 'create')

                    {!! Form::oneTextArea('content', trans('privateTopicReviewReplies.content'), isset($topicReviewReply) ? $topicReviewReply->content : null, ['class' => 'form-control', 'id' => 'content', 'required']) !!}
                    <div class="form-group">
                        <label for="status">{!! trans('privateTopicReviewReplies.status') !!}</label>
                        <select class="form-control" id="status" name="status" required>
                            @foreach ($status as $option)
                                <option  value="{!! $option->code !!}">   {!! $option->name !!}  </option>
                            @endforeach
                        </select>
                    </div>

                @endif
            @if(ONE::actionType('topicReviewReply') == 'edit')
                {!! Form::oneTextArea('content', trans('privateTopicReviewReplies.content'), isset($topicReviewReply) ? $topicReviewReply->content : null, ['class' => 'form-control', 'id' => 'content', 'required']) !!}

                <div class="form-group">
                    <label for="status">{!! trans('privateTopicReviewReplies.status') !!}</label>
                    <select class="form-control" id="status" name="status" required>
                        @foreach ($status as $option)
                            <option  value="{!! $option->code !!}">   {!! $option->name !!}  </option>
                        @endforeach
                    </select>
                </div>
            @endif
            {!! $form->make() !!}
        </div>
    </div>

@endsection

@section('scripts')

    <script type="text/javascript">

        //change submit button text/value
        $('input.empatia').val('{{trans('privateTopicReviewReplies.reply')}}');

        //prepare/load reviewers
        var array = '@php echo json_encode(isset($reviewers) ? $reviewers: null); @endphp';

        if (array != 'null') {
            array = JSON.parse(array);

            $("#users").select2({
                data: array,
                tags: true,
                placeholder: '@php echo trans('privateTopicReviewReplies.select_value'); @endphp'
            })
        }

        $(function() {
            var array = ["{{ isset($type) ? $type : null }}", "{{isset($cbKey) ? $cbKey : null}}", "{{isset($topicKey) ? $topicKey : null}}", "{{isset($topicReviewKey) ? $topicReviewKey: null}}"]
            getSidebar('{{ action("OneController@getSidebar") }}', 'details', array, 'topicReviews' );
        });
    </script>
@endsection

