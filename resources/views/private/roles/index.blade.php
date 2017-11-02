@extends('private._private.index')

@section('content')
    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title"><i class="fa"></i> {{ trans('privateRoles.roles') }}</h3>
        </div>

        <div class="box-body">
            <table id="roles_list" class="table table-striped dataTable no-footer table-responsive">
                <thead>
                <tr>
                    <th>{{ trans('privateRoles.name') }}</th>
                    <th>{{ trans('privateRoles.code') }}</th>
                    <th>@if(ONE::verifyUserPermissions('orchestrator', 'role', 'create')){!! ONE::actionButtons(null, ['create' => 'RolesController@create']) !!}@endif</th>
                </tr>
                </thead>
            </table>
        </div>
    </div>
@endsection


@section('scripts')
    <script>

        $(function () {
            $('#roles_list').DataTable({
                language: {
                    url: '{!! asset('/datatableLang/'.Session::get('LANG_CODE').'.json') !!}',
                    search: '<a class="btn searchBtn" id="searchBtn"><i class="fa fa-search"></i></a>'
                },
                processing: true,
                serverSide: true,
                ajax: '{!! action('RolesController@getIndexTable') !!}',
                columns: [
                    { data: 'name', name: 'name' },
                    { data: 'code', name: 'code' },
                    { data: 'action', name: 'action', searchable: false, orderable: false, width: "30px" },
                ],
                order: [['1', 'asc']]
            });

        });

    </script>
@endsection



