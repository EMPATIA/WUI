@extends('private._private.index')

@section('content')
    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title"><i class="fa"></i> {{ trans('privateNews.title') }}</h3>
        </div>

        <div class="box-body">
            <table id="news-list" class="table table-hover table-striped dataTable no-footer table-responsive">
                <thead>
                <tr>
                    <th>{{ trans('privateNews.id') }}</th>
                    <th>{{ trans('privateNews.title') }}</th>
                    <th>{{ trans('form.start_date') }}</th>
                    <th>{{ trans('form.publish_date') }}</th>
                    <th>@if(Session::get('user_role') == 'admin'){!! ONE::actionButtons($type, ['create' => 'ContentsController@create']) !!}@endif</th>
                </tr>
                </thead>
            </table>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(function () {
            $('#news-list').DataTable({
                language: {
                    url: '{!! asset('/datatableLang/'.Session::get('LANG_CODE').'.json') !!}',
                    search: '<a class="btn searchBtn" id="searchBtn"><i class="fa fa-search"></i></a>'
                },
                processing: true,
                serverSide: true,
                ajax: '{!! action('ContentsController@contentsDataTable', $type) !!}',
                columns: [
                    { data: 'id', name: 'id', width: "20px" },
                    { data: 'title', name: 'title' },
                    { data: 'start_date', name: 'start_date' },
                    { data: 'publish_date', name: 'publish_date' },
                    { data: 'action', name: 'action', searchable: false, orderable: false, width: "50px" },
                ],
                order: [['3', 'desc']]
            });

        });
    </script>
@endsection



