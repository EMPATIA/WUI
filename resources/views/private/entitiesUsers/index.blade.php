@extends('_private.index')

@section('content')

    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title"><i class="fa"></i> {{ trans('user.list') }}</h3>
        </div>

        <div class="box-body">
            {!! ONE::messages() !!}
            <table id="user_list" class="table table-hover table-striped dataTable no-footer table-responsive">
                <thead>
                <tr>
                    <th>{{ trans('form.name') }}</th>
                    <th>{{ trans('form.login') }}</th>
                    <th>{{ trans('form.add') }}</th>
                </tr>
                </thead>
            </table>
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
                ajax: '{!! action("EntitiesUsersController@tableUsers") !!}',
                columns: [
                    { data: 'name', name: 'name' },
                    { data: 'login', name: 'login' },
                    { data: 'action', name: 'action', searchable: false, orderable: false, width: "30px" },
                ],
                order: [['1', 'asc']]
            });
        }

    </script>


@endsection