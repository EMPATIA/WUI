@extends('private._private.index')

@section('content')
    <div class="box box-primary">

        <div class="box-header">
            <h3 class="box-title"><i class="fa"></i> {{ trans('title.forum') }}</h3>
        </div>

        <div class="box-body">
            <table id="forum_list" class="table table-hover table-striped dataTable no-footer table-responsive">
                <thead>
                <tr>
                    <th>{{ trans('form.id') }}</th>
                    <th>{{ trans('forum.title') }}</th>
                    <th>{{ trans('forum.abuses') }}</th>
                    <th>{{ trans('forum.status') }}</th>
                    <th>{!! ONE::actionButtons(null, ['create' => 'ForumController@create']) !!}</th>
                </tr>
                </thead>
            </table>
        </div>
    </div>

@endsection


@section('scripts')
    <script>
        $(function () {
            $('#forum_list').DataTable({
                language: {
                    url: '{!! asset('/datatableLang/'.Session::get('LANG_CODE').'.json') !!}',
                    search: '<a class="btn searchBtn" id="searchBtn"><i class="fa fa-search"></i></a>'
                },
                processing: true,
                serverSide: true,
                ajax: '{!! action('ForumController@getIndexTable') !!}',
                columns: [
                    { data: 'id', name: 'id', width: "20px" },
                    { data: 'title', name: 'title' },
                    { data: 'abuses', name: 'abuses', width: "130px", 'className': 'text-center'},
                    { data: 'status', name: 'status', width: "80px", 'className': 'text-center' },
                    { data: 'action', name: 'action', searchable: false, orderable: false, width: "30px" },
                ],
                order: [['1', 'asc']]
            });

        });

    </script>
@endsection
