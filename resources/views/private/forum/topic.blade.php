@extends('private._private.index')


@section('content')
<div class="row">
    <div class="col-md-12">

        @php $form = ONE::form('topic')
                ->settings(["model" => isset($topic) ? $topic : null])
                ->show('TopicController@edit', 'TopicController@destroy', ['id' => isset($topic) ? $topic->id : null, 'cbId' => $cbId], 'ForumController@show',['id' => isset($cbId) ? $cbId : null])
                ->create('TopicController@store', 'TopicController@show', ['id' => isset($topic) ? $topic->id : null])
                ->edit('TopicController@update', 'TopicController@show', ['id' => isset($topic) ? $topic->id : null])
                ->open();
        @endphp

        {!! Form::oneText('title', trans('forum.title'), isset($topic) ? $topic->title : null, ['class' => 'form-control', 'id' => 'title']) !!}
        {!! Form::oneText('contents', trans('forum.contents'), isset($topic) ? $topic->contents : null, ['class' => 'form-control', 'id' => 'contents']) !!}
        {!! Form::oneSelect('blocked', trans('form.blocked'), $options, isset($topic) ? $topic->blocked : 0, $options[isset($topic) ? $topic->blocked : 0], ['class' => 'form-control', 'id' => 'access_type_id'] ) !!}

        {!! $form->make() !!}

    </div>

    @if(ONE::actionType('topic') == "show")

        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header">
                    <h3 class="box-title"><i class="fa"></i> {{ trans('topic.abuses') }}</h3>
                </div>

                <div class="box-body">
                    <table id="topic_abuses_list" class="table table-hover table-striped dataTable no-footer table-responsive">
                        <thead>
                        <tr>
                            <th>{{ trans('abuses.postId') }}</th>
                            <th>{{ trans('abuses.comment') }}</th>
                            <th>{{ trans('abuses.abuses') }}</th>
                            <th>{{ trans('abuses.processed') }}</th>
                            <th>{{ trans('abuses.created_at') }}</th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    @endif
</div>

@endsection

@if(ONE::actionType('topic') == "show")
    @section('scripts')
        <script>

            $(function () {
                $('#topic_abuses_list').DataTable({
                    language: {
                        url: '{!! asset('/datatableLang/'.Session::get('LANG_CODE').'.json') !!}',
                        search: '<a class="btn searchBtn" id="searchBtn"><i class="fa fa-search"></i></a>'
                    },
                    processing: true,
                    serverSide: true,
                    ajax: '{!! action('AbuseController@getAbusesByTopicTable', $topic->id) !!}',
                    columns: [
                        {data: 'postId', name: 'postId'},
                        {data: 'comment', name: 'comment'},
                        {data: 'abuses', name: 'abuses', searchable: false, orderable: false, width: "30px" },
                        {data: 'processed', name: 'processed', width: "20px", 'className': 'text-center'},
                        {data: 'created_at', name: 'created_at'}
                    ],
                    order: [['1', 'asc']]
                });

            });

        </script>
    @endsection
@endif

