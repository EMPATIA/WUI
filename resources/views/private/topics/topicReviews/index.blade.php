@extends('private._private.index')

@section('content')
    <div class="row" style="margin-left: 1px">
        <a href="{{ action('TopicReviewsController@create', ['type'=>$type,'cbKey'=>$cbKey, 'topicKey' => $topicKey]) }}" class="btn btn-flat btn-success" data-toggle="tooltip" data-delay="{&quot;show&quot;:&quot;1000&quot;}" title="" data-original-title="{{trans("privateTopicReviews.create")}}">{{trans("privateTopicReviews.create")}}</a>
    </div>
    <br>

    <div id="infinite-scroll">
        @foreach($topicReviews as $review)
            <div class="box box-primary">
                <div class="box-header">
                    <div class="col-sm-2">
                        <span>{{trans('privateTopicReviews.creator')}}</span>
                        <span><h5 style="font-weight: bold">{{$review->creator_name}}</h5></span>
                    </div>

                    <div class="col-sm-2" style="margin-top: 1%"><h5 style="margin-right:20px">{{trans('privateTopicReviews.reviewers')}}</h5></div>
                    @foreach($review->topic_review_reviewers as $reviewers)
                        <div class="col-sm-2" style="margin-top: 1%">

                            <h6>{{isset($reviewers->reviewer_name) ? $reviewers->reviewer_name : ""}}</h6>
                        </div>
                    @endforeach
                </div>

                <div class="box-body">

                    <div class="row" style="margin-left: 20px">

                        {!! Form::oneText('subject', trans('privateTopicReviews.subject'), isset($review) ? $review->subject : null, ['class' => 'form-control', 'id' => 'description', 'required']) !!}
                        {!! Form::oneTextArea('description', trans('privateTopicReviews.description'), isset($review) ? $review->description : null, ['class' => 'form-control', 'id' => 'description', 'required']) !!}
                        @if(!empty($review->topic_review_replies))
                            <button class="pull-right btn btn-success" type="button" data-toggle="collapse" data-target="#{{$review->topic_review_key}}" aria-expanded="false" aria-controls="{{$review->topic_review_key}}">{{trans('privateTopicReviews.replies')}}</button>
                        @else
                            <button class="pull-right btn btn-success" type="button" data-toggle="collapse" data-target="#{{$review->topic_review_key}}" disabled aria-expanded="false" aria-controls="{{$review->topic_review_key}}">{{trans('privateTopicReviews.replies')}}</button>
                        @endif
                    </div>

                </div>
            </div>
            @if(!empty($review->topic_review_replies))
                <div class="row collapse" id="{{$review->topic_review_key}}">
                    @foreach($review->topic_review_replies as $replies)

                        <div class="card card-body">
                            <div class="col-sm-4"></div>
                            <div class="box box-primary col-sm-12 pull-right"  style="width: 90%">
                                <div class="box-header">
                                    <div class="col-sm-2">
                                        {{--<span>Creator:</span>--}}
{{--                                        <span><h5 style="font-weight: bold">{{$repliers[$replies->created_by]->name}}</h5></span>--}}
                                    </div>
                                </div>

                                <div class="box-body">

                                    <div class="row" style="margin-left: 20px">

                                        {!! Form::oneTextArea('description', trans('privateTopicReviews.description'), isset($replies) ? $replies->content : null, ['class' => 'form-control', 'id' => 'description', 'required']) !!}

                                    </div>

                                </div>
                            </div>
                        </div>

                    @endforeach
                </div>
            @endif
        @endforeach
    </div>
@endsection

{{--@section('content')--}}
{{--<div class="box box-primary">--}}


{{--<div class="box-header">--}}
{{--<h3 class="box-title"><i class="fa"></i> {{ trans('privateTopicReviews.topic_review_users') }}</h3>--}}
{{--</div>--}}

{{--<div class="box-body">--}}
{{--<table id="topic_review_users_list" class="table table-striped dataTable no-footer table-responsive">--}}
{{--<thead>--}}
{{--<tr>--}}
{{--<th>{{ trans('privateTopicReviews.subject') }}</th>--}}
{{--<th>{{ trans('privateTopicReviews.created_by') }}</th>--}}
{{--<th>{{ trans('privateTopicReviews.created_at') }}</th>--}}
{{--<th>--}}
{{--<a href="{{ action('TopicReviewsController@create', ['type'=>$type,'cbKey'=>$cbKey, 'topicKey' => $topicKey]) }}" class="btn btn-flat btn-success btn-sm" data-toggle="tooltip" data-delay="{&quot;show&quot;:&quot;1000&quot;}" title="" data-original-title="Criar"><i class="fa fa-plus"></i></a>--}}
{{--</th>--}}
{{--</tr>--}}
{{--</thead>--}}
{{--</table>--}}
{{--</div>--}}
{{--</div>--}}
{{--<div class="box box-primary">--}}

{{--<div class="box-header">--}}
{{--<h3 class="box-title"><i class="fa"></i> {{ trans('privateTopicReviews.topic_review_groups') }}</h3>--}}
{{--</div>--}}

{{--<div class="box-body">--}}
{{--<table id="topic_review_groups_list" class="table table-striped dataTable no-footer table-responsive">--}}
{{--<thead>--}}
{{--<tr>--}}
{{--<th>{{ trans('privateTopicReviews.subject') }}</th>--}}
{{--<th>{{ trans('privateTopicReviews.created_by') }}</th>--}}
{{--<th>{{ trans('privateTopicReviews.created_at') }}</th>--}}
{{--</tr>--}}
{{--</thead>--}}
{{--</table>--}}
{{--</div>--}}
{{--</div>--}}
{{--@endsection--}}


@section('scripts')

    <script>

        $(function () {

            var filterTypes = $("#advancedFilter").val();

            $('#topic_review_users_list').DataTable({
                language: {
                    url: '{!! asset('/datatableLang/'.Session::get('LANG_CODE').'.json') !!}',
                    search: '<a class="btn searchBtn" id="searchBtn"><i class="fa fa-search"></i></a>'
                },
                processing: true,
                serverSide: true,
                ajax: '{!! action('TopicReviewsController@topicReviewUsersTable', ['type'=>$type,'cbKey'=>$cbKey, 'topicKey' => $topic->topic_key]) !!}',
                columns: [
                    { data: 'subject', name: 'subject' },
                    { data: 'created_by', name: 'created_by' },
                    { data: 'created_at', name: 'Created At' },
                    { data: 'action', name: 'action', searchable: false, orderable: false, width: "30px" },
                ],
                order: [[ 2, 'desc' ]]
            });


            $('#topic_review_groups_list').DataTable({
                language: {
                    url: '{!! asset('/datatableLang/'.Session::get('LANG_CODE').'.json') !!}',
                    search: '<a class="btn searchBtn" id="searchBtn"><i class="fa fa-search"></i></a>'
                },
                processing: true,
                serverSide: true,
                ajax: '{!! action('TopicReviewsController@topicReviewGroupsTable', ['type'=>$type,'cbKey'=>$cbKey, 'topicKey' => $topic->topic_key]) !!}',
                columns: [
                    { data: 'subject', name: 'subject' },
                    { data: 'created_by', name: 'created_by' },
                    { data: 'created_at', name: 'Created At' },
                    { data: 'action', name: 'action', searchable: false, orderable: false, width: "30px" },
                ],
                order: [[ 2, 'asc' ]]
            });

        });


        //        }

    </script>
@endsection

@section('header_styles')
    <style>
        .adv-search{
            margin-top: 20px;
            margin-bottom: 30px;
        }

        .select2-container--default .select2-search--inline .select2-search__field {
            border: 0;
        }
        .select2-container--default .select2-results__option[aria-selected="true"] {
            background-color: #f4f4f5;
        }
        .select2privatePosts{
            width: 80%;
        }
    </style>
@endsection
