@extends('private._private.index')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header">
                    <h3 class="box-title"><i class="fa fa-users"></i> {{ trans('privateEntities.managers') }}</h3>
                </div>
                <div class="box-body">
                    <table id="managers_list" class="table table-striped dataTable no-footer table-responsive">
                        <thead>
                        <tr>
                            <th width="90%">{{ trans('privateEntities.managers') }}</th>
                            <th>
                                {!! ONE::actionButtons(null, ['create' => 'EntitiesDividedController@createManager']) !!}
                                {!! ONE::actionButtons(null, ['add' => 'EntitiesDividedController@addManager']) !!}
                            </th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(function () {
            $('#managers_list').DataTable({
                language: {
                    url: '{!! asset('/datatableLang/'.Session::get('LANG_CODE').'.json') !!}',
                    search: '<a class="btn searchBtn" id="searchBtn"><i class="fa fa-search"></i></a>'
                },
                processing: true,
                serverSide: true,
                ajax: '{!! action("EntitiesDividedController@tableUsersEntity") !!}',
                columns: [
                    {data: 'name', name: 'name'},
                    {data: 'action', name: 'action', searchable: false, orderable: false}
                ],
                order: [['0', 'asc']]
            });
        });
    </script>
@endsection