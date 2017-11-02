@extends('private._private.index')

@section('content')
    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title"><i class="fa"></i> {{ trans('privateAuthMethods.auth_methods') }}</h3>
        </div>

        <div class="box-body">
            <table id="authMethods_list" class="table table-hover table-striped dataTable no-footer table-responsive">
                <thead>
                <tr>
                    <th>{{ trans('privateAuthMethods.id') }}</th>
                    <th>{{ trans('privateAuthMethods.name') }}</th>
                    <th>{{ trans('privateAuthMethods.description') }}</th>
                    <th>{{ trans('privateAuthMethods.code') }}</th>
                    <th>{!! ONE::actionButtons(null, ['create' => 'AuthMethodsController@create']) !!}</th>
                </tr>
                </thead>
            </table>
        </div>
    </div>

@endsection


@section('scripts')
    <script>

        $(function () {
            $('#authMethods_list').DataTable({
                language: {
                    url: '{!! asset('/datatableLang/'.Session::get('LANG_CODE').'.json') !!}',
                    search: '<a class="btn searchBtn" id="searchBtn"><i class="fa fa-search"></i></a>'
                },
                processing: true,
                serverSide: true,
                ajax: '{!! action('AuthMethodsController@tableAuthMethods') !!}',
                columns: [
                    { data: 'id', name: 'id', width: "20px" },
                    { data: 'name', name: 'name' },
                    { data: 'description', name: 'description' },
                    { data: 'code', name: 'code' },
                    { data: 'action', name: 'action', searchable: false, orderable: false, width: "50px" },
                ],
                order: [['1', 'asc']]
            });

        });
        
    </script>
@endsection



