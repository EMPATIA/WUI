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
                    <th>{{ trans('user.createdAt') }}</th>
                    <th>{{ trans('user.identifierNumber') }}</th>
                    <th>{{ trans('user.name') }}</th>
                    <th>{!! ONE::actionButtons(null, ['create' => 'InPersonRegistrationController@create']) !!}</th>
                </tr>
                </thead>
            </table>
        </div>
    </div>

@endsection


@section('scripts')
    <script>

        $(function () {
            $('#users_list').DataTable({
                language: {
                    url: '{!! asset('/datatableLang/'.Session::get('LANG_CODE').'.json') !!}',
                    search: '<a class="btn searchBtn" id="searchBtn"><i class="fa fa-search"></i></a>',
                },
                processing: true,
                serverSide: true,
                bDestroy: true,
                ajax: '{!! action('InPersonRegistrationController@getIndexTable') !!}',
                columns: [
                    { data: 'created_at', name: 'created_at' },
                    { data: 'identity_card', name: 'identity_card' },
                    { data: 'name', name: 'name' },
                    { data: 'action', name: 'action', searchable: false, orderable: false, width: "30px" }
                ],
                order: [['0', 'desc']]
            });
        });

    </script>


@endsection
