@extends('private._private.index')

@section('content')

    <div class="row">
        <div class="col-md-12">
            @php $form = ONE::form('topicReview', trans('privateTopicReviews.details'))
                ->settings(["model" => isset($topicReview) ? $topicReview->topic_review_key : null, 'id' => isset($topicReview) ? $topicReview->topic_review_key : null])
                ->show('TopicReviewsController@edit', 'TopicReviewsController@delete', ['type'=>$type,'cbKey'=>$cbKey, 'topicKey' => $topicKey, 'topicReviewKey' => isset($topicReview) ? $topicReview->topic_review_key : null] , 'TopicReviewsController@index', ['type'=>$type,'cbKey'=>$cbKey, 'topicKey' => $topicKey, 'topicReviewKey' => isset($topicReview) ? $topicReview->topic_review_key : null])
                ->create('TopicReviewsController@store', 'TopicReviewsController@index', ['type'=>$type,'cbKey'=>$cbKey, 'topicKey' => $topicKey, 'topicReviewKey' => isset($topicReview) ? $topicReview->topic_review_key : null])
                ->edit('TopicReviewsController@update', 'TopicReviewsController@show', ['type'=>$type,'cbKey'=>$cbKey, 'topicKey' => $topicKey, 'topicReviewKey' => isset($topicReview) ? $topicReview->topic_review_key : null])
                ->open();
            @endphp

            @if(ONE::actionType('topicReview') == 'show')
                <div class="form-group">

                    @if (One::isAdmin() || isset($hasPermission) ? $hasPermission:false)

                        <a href="{{ action('TopicReviewRepliesController@create', ['type'=>$type,'cbKey'=>$cbKey, 'topicKey' => $topicKey, 'topicReviewKey' => $topicReview->topic_review_key]) }}" class="btn btn-flat empatia  pull-right">{!! trans('privateTopicReviewReplies.reply') !!}  <i class="fa fa-arrow-right"></i></a>
                    @endif
                    @if($topicReview->status == 'open')
                        <span class="badge badge-primary">{{trans('privateTopicReviews.open')}}</span>
                    @elseif($topicReview->status == 'approved')
                        <span class="badge badge-success">{{trans('privateTopicReviews.approved')}}</span>
                    @elseif($topicReview->status == 'rejected')
                        <span class="badge badge-danger">{{trans('privateTopicReviews.rejected')}}</span>
                    @endif
                </div>

                {!! Form::oneText('subject', array("name"=>trans('privateTopicReviews.subject'),"description"=>trans('privateTopicReviews.subjectDescription')), isset($topicReview) ? $topicReview->subject : null, ['class' => 'form-control', 'id' => 'description', 'required']) !!}
                {!! Form::oneTextArea('description', array("name"=>trans('privateTopicReviews.description'),"description"=>trans('privateTopicReviews.descriptionDescription')), isset($topicReview) ? $topicReview->description : null, ['class' => 'form-control', 'id' => 'description', 'required']) !!}
                {!! Form::oneText('createdBy', array("name"=>trans('privateTopicReviews.created_by'),"description"=>trans('privateTopicReviews.created_byDescription')), isset($topicReview) ? $topicReview->creator_name : null, ['class' => 'form-control', 'id' => 'createdBy', 'required']) !!}
                {!! Form::oneText('status', array("name"=>trans('privateTopicReviews.status'),"description"=>trans('privateTopicReviews.statusDescription')), isset($topicReview) ? trans('privateTopicReviews.'.$topicReview->status.'') : null, ['class' => 'form-control', 'id' => 'status', 'required']) !!}

                <div class="form-group">
                    <label for="users">{!! trans('privateTopicReviews.assigned_to') !!}</label>
                    <ul>
                        @foreach($topicReview->topic_review_reviewers as $item)
                            <li>{!! isset($item->reviewer_name) ? $item->reviewer_name : null  !!}</li>
                        @endforeach
                    </ul>
                </div>

            @endif
            @if(ONE::actionType('topicReview') == 'create' || ONE::actionType('topicReview') == 'edit')

                <div class="form-group">
                    <label for="users">{!! trans('privateTopicReviews.send_to') !!}</label>
                    <div for="status_type_code"  style="font-size:x-small">{{trans('privateTopicReviews.send_toDescription')}}</div>
                    <select id="users" name="users[]" class="users-data-array form-control" multiple="multiple"></select>
                </div>
                    {!! Form::oneText('subject', array("name"=>trans('privateTopicReviews.subject'),"description"=>trans('privateTopicReviews.subjectDescription')), isset($topicReview) ? $topicReview->subject : null, ['class' => 'form-control', 'id' => 'description', 'required']) !!}
                    {!! Form::oneTextArea('description', array("name"=>trans('privateTopicReviews.description'),"description"=>trans('privateTopicReviews.descriptionDescription')), isset($topicReview) ? $topicReview->description : null, ['class' => 'form-control', 'id' => 'description', 'required']) !!}

                    {!! Form::hidden('code', 'open', ['id' => 'code']) !!}
            @endif
            @if(ONE::actionType('topicReview') == 'edit')
                    <div class="form-group">
                        <label for="users">{!! trans('privateTopicReviews.already_assigned_to') !!}</label>
                        <ul>
                            @foreach($topicReview->topic_review_reviewers as $item)
                                <li>{!! isset($item->reviewer_name) ? $item->reviewer_name : null !!}</li>
                            @endforeach
                        </ul>
                    </div>
            @endif

            {!! $form->make() !!}
        </div>
    </div>

@endsection

@section('scripts')

    <script type="text/javascript">
                //change submit button text/value
        var hasPermission = '@php echo json_encode(isset($hasPermission) ? $hasPermission: false); @endphp';
        if(hasPermission == 'false')
            $(".box-tools").hide();


        //prepare/load reviewers
        var array = '@php echo json_encode(isset($reviewers) ? $reviewers: null); @endphp';

        if (array != 'null') {
            array = JSON.parse(array);

            $("#users").select2({
                data: array,
                tags: true,
                placeholder: '@php echo trans('privateEntityGroups.select_value'); @endphp'
            })
        }



    </script>
@endsection
