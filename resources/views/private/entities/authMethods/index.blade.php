@extends('private._private.index')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header">
                    <h3 class="box-title"><i class="fa fa-unlock"></i> {{ trans('privateEntities.authMethodTitle') }}</h3>
                </div>
                <div class="box-body">
                    <table id="authMethods_list" class="table table-striped dataTable no-footer table-responsive">
                        <thead>
                        <tr>
                            <th width="50%">{{ trans('privateEntities.authMethodName') }}</th>
                            <th width="40%">{{ trans('privateEntities.authMethodDescription') }}</th>
                            <th>
                                @if(Session::get('user_role') == 'admin' || ONE::verifyUserPermissionsCreate('orchestrator', 'entity_auth_method'))
                                    {!! ONE::actionButtons(null, ['add' => 'EntitiesDividedController@addAuthMethod']) !!}
                                @endif
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
            $('#authMethods_list').DataTable({
                language: {
                    url: '{!! asset('/datatableLang/'.Session::get('LANG_CODE').'.json') !!}',
                    search: '<a class="btn searchBtn" id="searchBtn"><i class="fa fa-search"></i></a>'
                },
                processing: true,
                serverSide: true,
                ajax: '{!! action("EntitiesDividedController@tableAuthMethod") !!}',
                columns: [
                    { data: 'name', name: 'name' },
                    { data: 'description', name: 'description' },
                    { data: 'action', name: 'action', searchable: false, orderable: false },
                ],
                order: [['1', 'asc']]
            });
        });
    </script>
@endsection