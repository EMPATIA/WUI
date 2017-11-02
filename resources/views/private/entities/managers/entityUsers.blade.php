@extends('private._private.index')

@section('content')

    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title"><i class="fa"></i> {{ trans('privateEntityUser.list') }}</h3>
        </div>

        <div class="box-body">
            {!! ONE::messages() !!}
            <table id="user_list" class="table table-striped dataTable no-footer table-responsive">
                <thead>
                <tr>
                    <th>{{ trans('privateEntityUser.name') }}</th>
                    <th>{{ trans('privateEntityUser.email') }}</th>
                    <th>{{ trans('privateEntityUser.add') }}</th>
                </tr>
                </thead>
            </table>
        </div>

        <div class="box-footer">
            <a class="btn btn-flat btn-primary" href=" {!!  action('EntitiesDividedController@showManagers') !!}"><i class="fa fa-arrow-left"></i> {!! trans('privateEntityUser.back') !!}</a>
        </div>
    </div>

@endsection


@section('scripts')
    <script>

        $(function () {
            $('#user_list').DataTable({
                language: {
                    url: '{!! asset('/datatableLang/'.Session::get('LANG_CODE').'.json') !!}',
                    search: '<a class="btn searchBtn" id="searchBtn"><i class="fa fa-search"></i></a>'
                },
                processing: true,
                serverSide: true,
                bDestroy: true,
                ajax: {
                    "url": '{{ action("UsersController@tableUsersManagerMan") }}',
                    "data": function (d) {
                        d.entityKey = '{{ $entityKey }}';
                    }
                },
                columns: [
                    { data: 'name', name: 'name' },
                    { data: 'email', name: 'email' },
                    { data: 'action', name: 'action', searchable: false, orderable: false, width: "30px" },
                ],
                order: [['1', 'asc']]
            });
        });

    </script>
@endsection