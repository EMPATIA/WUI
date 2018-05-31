@extends('private._private.index')

@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title"><i class="fa"></i> {{ trans('privateDiscussion.discussion') }}</h3>
        </div>

        <div class="box-body">
            <table id="discussion_list" class="table table-hover table-striped dataTable no-footer table-responsive">
                <thead>
                <tr>
                    <th>{{ trans('privateDiscussion.id') }}</th>
                    <th>{{ trans('privateDiscussion.title') }}</th>
                    <th>{!! ONE::actionButtons(null, ['create' => 'DiscussionController@create']) !!}</th>
                </tr>
                </thead>
            </table>
        </div>
    </div>

@endsection


@section('scripts')
    <script>

        $(function () {
            $('#discussion_list').DataTable({
                language: {
                    url: '{!! asset('/datatableLang/'.Session::get('LANG_CODE').'.json') !!}',
                    search: '<a class="btn searchBtn" id="searchBtn"><i class="fa fa-search"></i></a>'
                },
                processing: true,
                serverSide: true,
                ajax: '{!! action('DiscussionController@getIndexTable') !!}',
                columns: [
                    { data: 'id', name: 'id', width: "20px" },
                    { data: 'title', name: 'title'},
                    { data: 'action', name: 'action', searchable: false, orderable: false, width: "30px" },
                ],
                order: [['1', 'asc']]
            });

        });

    </script>
@endsection
