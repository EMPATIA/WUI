@extends('private._private.index')

@section('content')
    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title"><i class="fa"></i> {{ trans('privateAuthMethods.addAuthMethod') }}</h3>
        </div>

        <div class="box-body">
            {!! ONE::messages() !!}
            <table id="addAuthMethod_list" class="table table-striped dataTable no-footer table-responsive">
                <thead>
                <tr>
                    <th width="50%">{{ trans('privateEntities.authMethodName') }}</th>
                    <th width="40%">{{ trans('privateEntities.authMethodDescription') }}</th>
                    <th>{{ trans('privateEntities.add') }}</th>
                </tr>
                </thead>
            </table>
        </div>

        <div class="box-footer">
            <a class="btn btn-flat btn-primary" href=" {!!  action('EntitiesDividedController@showAuthMethods') !!}"><i class="fa fa-arrow-left"></i> {!! trans('privateEntities.back') !!}</a>
        </div>
    </div>
@endsection


@section('scripts')
    <script>

        $(function () {
            $('#addAuthMethod_list').DataTable({
                language: {
                    url: '{!! asset('/datatableLang/'.Session::get('LANG_CODE').'.json') !!}',
                    search: '<a class="btn searchBtn" id="searchBtn"><i class="fa fa-search"></i></a>'
                },
                processing: true,
                serverSide: true,
                ajax: '{!! action("EntitiesDividedController@tableAddAuthMethod") !!}',

                columns: [
                    { data: 'name', name: 'name' },
                    { data: 'description', name: 'description' },
                    { data: 'action', name: 'action', searchable: false, orderable: false, width: "30px" },
                ],
                order: [['1', 'asc']]
            });
        });
    </script>
@endsection