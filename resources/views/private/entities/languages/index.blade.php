@extends('private._private.index')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header">
                    <h3 class="box-title"><i class="fa fa-language"></i> {{ trans('privateEntities.languageTitle') }}</h3>
                </div>
                <div class="box-body">
                    <table id="languages_list" class="table table-striped dataTable no-footer table-responsive">
                        <thead>
                        <tr>
                            <th width="50%">{{ trans('privateEntities.languages') }}</th>
                            <th width="40%">{{ trans('privateEntities.makeDefault') }}</th>
                            <th width="10%">
                                @if(Session::get('user_role') == 'admin' || ONE::verifyUserPermissionsCreate('orchestrator', 'entity_language'))
                                    {!! ONE::actionButtons(null, ['add' => 'EntitiesDividedController@addLanguage']) !!}
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
@if(ONE::actionType('entities') == "show")
@section('scripts')
    <script>

        $(function () {
            $('#languages_list').DataTable({
                language: {
                    url: '{!! asset('/datatableLang/'.Session::get('LANG_CODE').'.json') !!}',
                    search: '<a class="btn searchBtn" id="searchBtn"><i class="fa fa-search"></i></a>'
                },
                processing: true,
                serverSide: true,
                ajax: '{!! action("EntitiesDividedController@tableLanguagesEntity") !!}',
                columns: [
                    { data: 'name', name: 'name' },
                    { data: 'activateAction', name: 'action_activate', searchable: false, orderable: false, width: "5px" },
                    { data: 'action', name: 'action', searchable: false, orderable: false },
                ],
                order: [['1', 'asc']]
            });
        });
    </script>
@endsection
@endif
