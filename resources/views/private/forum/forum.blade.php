@extends('private._private.index')


@section('content')

    <div class="row">
        <div class="col-md-12">
            @if(ONE::actionType('forum') == "show")
                @if($forum->status_id != 1)
                    <div class="alert alert-warning" role="alert">
                        <span class="glyphicon glyphicon-eye-close" aria-hidden="true"></span>
                        <b>Forum not visible</b>
                    </div>
                @else
                    <div class="alert alert-success" role="alert">
                        <span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span>
                        <b>Forum published</b>
                    </div>
                @endif
            @endif

            @php $form = ONE::form('forum')
                    ->settings(["model" => isset($forum) ? $forum : null])
                    ->show('ForumController@edit', 'ForumController@destroy', ['id' => isset($forum) ? $forum->id : null], 'ForumController@index')
                    ->create('ForumController@store', 'ForumController@index', ['id' => isset($forum) ? $forum->id : null])
                    ->edit('ForumController@update', 'ForumController@show', ['id' => isset($forum) ? $forum->id : null])
                    ->open();
            @endphp

            @if(ONE::actionType('forum') == "show")
                {!! Form::button(trans(' form.Close'), ['class' => 'btn btn-flat btn btn-warning btn-sm fa fa-eye-slash pull-right', 'onclick' => "location.href='".action('ForumController@updateStatus', ['cbId' => isset($forum) ? $forum->id : null, 'id' => 2])]) !!}
                {!! Form::button(trans(' form.Publish'), ['class' => 'btn btn-flat btn btn-success btn-sm fa fa-eye pull-right', 'onclick' => "location.href='".action('ForumController@updateStatus', ['cbId' => isset($forum) ? $forum->id : null, 'id' => 1])]) !!}
            @endif
            {!! Form::oneText('title', trans('forum.title'), isset($forum) ? $forum->title : null, ['class' => 'form-control', 'id' => 'title']) !!}
            {!! Form::oneText('contents', trans('forum.contents'), isset($forum) ? $forum->contents : null, ['class' => 'form-control', 'id' => 'contents']) !!}
            {{--->addSelectEditCreate('status_id', trans('form.status'), Form::select('status_id', $options, isset($forum) ? $forum->status_id : 0, ['class' => 'form-control', 'id' => 'status_id']), $options[isset($forum->status_id) ? $forum->status_id : 0])--}}

            {!! $form->make() !!}

        </div>
        @if(ONE::actionType('forum') == "show")
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header">
                        <h3 class="box-title"><i class="fa"></i> {{ trans('forum.topics') }}</h3>
                    </div>

                    <div class="box-body">
                        <table id="forum_topics_list" class="table table-hover table-striped dataTable no-footer table-responsive">
                            <thead>
                            <tr>
                                <th>{{ trans('topic.id') }}</th>
                                <th>{{ trans('topic.title') }}</th>
                                <th>{{ trans('topic.abuses') }}</th>
                                <th>{{ trans('topic.status') }}</th>
                                <th>{!! ONE::actionButtons(isset($forum) ? $forum->id : null, ['create' => 'TopicController@create']) !!}</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
            <!--div class="col-md-6">
                <div class="box box-primary">
                    <div class="box-header">
                        <h3 class="box-title"><i class="fa"></i> {{ trans('forum.abuses') }}</h3>
                    </div>

                    <div class="box-body">
                        <table id="forum_abuses_list" class="table table-hover table-striped dataTable no-footer table-responsive">
                            <thead>
                            <tr>
                                <th>{{ trans('abuses.comment') }}</th>
                                <th>{{ trans('abuses.processed') }}</th>
                                <th>{{ trans('abuses.created_at') }}</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                    <div class="box-footer">
                        <a href="{{action('ForumController@index')}}" class="btn btn-flat btn-primary"><i class="fa fa-arrow-left"></i>{{trans('form.back')}}</a>
                    </div>
                </div>
            </div-->
        @endif
    </div>
@endsection

@if(ONE::actionType('forum') == "show")
    @section('scripts')
        <script>

            $(function () {
                $('#forum_topics_list').DataTable({
                    language: {
                        url: '{!! asset('/datatableLang/'.Session::get('LANG_CODE').'.json') !!}',
                        search: '<a class="btn searchBtn" id="searchBtn"><i class="fa fa-search"></i></a>'
                    },
                    processing: true,
                    serverSide: true,
                    ajax: '{!! action('ForumController@getTopicsTable', $forum->id) !!}',
                    columns: [
                        {data: 'id', name: 'id', width: "20px"},
                        {data: 'title', name: 'title'},
                        {data: 'abuses', name: 'abuses', width: "130px", 'className': 'text-center'},
                        {data: 'status', name: 'status', width: "80px", 'className': 'text-center'},
                        {data: 'action', name: 'action', searchable: false, orderable: false, width: "30px"},
                    ],
                    order: [['1', 'asc']]
                });
            });

        </script>
    @endsection
@endif

