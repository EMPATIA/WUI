@extends('private._private.index')

@section('content')

    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title"><i class="fa"></i> {{ trans('user.list') }}</h3>
        </div>

        <div class="box-body">
            <table id="users_list" class="table table-hover table-striped dataTable no-footer table-responsive">
                <thead>
                <tr>
                    <th>{{ trans('user.name') }}</th>
                    <th>{{ trans('user.email') }}</th>
                    <th style="width:10%"></th>
                    <th></th>
                </tr>
                </thead>
            </table>
        </div>
    </div>

@endsection


@section('scripts')
    <script>

        $( document ).ready(function() {
            databableUser();
        });

        function databableUser(){
            $('#users_list').DataTable({
                language: {
                    url: '{!! asset('/datatableLang/'.Session::get('LANG_CODE').'.json') !!}',
                    search: '<a class="btn searchBtn" id="searchBtn"><i class="fa fa-search"></i></a>'
                },
                processing: true,
                serverSide: true,
                bDestroy: true,
                ajax: '{!! action('UsersController@tableUsersWithStatus', $status) !!}',
                columns: [
                    { data: 'name', name: 'name' },
                    { data: 'email', name: 'email' },
                    { data: 'authorize', name: 'authorize', searchable: false, orderable: false, width: "30px" },
                    { data: 'action', name: 'action', searchable: false, orderable: false, width: "50px" },
                ],
                order: [['1', 'asc']]
            });
        }

    </script>


@endsection
