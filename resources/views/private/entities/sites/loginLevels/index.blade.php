@extends('private._private.index')

@section('content')
    <div class="box box-primary">

        <div class="box-header">
            <h3 class="box-title"><i class="fa"></i> {{ trans('privateLoginLevels.login_levels') }}</h3>
        </div>

        <div class="box-body">
            <table id="login_levels_list" class="table table-striped dataTable no-footer table-responsive">
                <thead>
                <tr>
                    <th>{{ trans('privateLoginLevels.level_level') }}</th>
                    <th>{{ trans('privateLoginLevels.level_name') }}</th>
                    <th>{{ trans('privateLoginLevels.level_mandatory') }}</th>
                    <th>{{ trans('privateLoginLevels.level_manual_verification') }}</th>
                    <th>{{ trans('privateLoginLevels.level_sms_verification') }}</th>
                    <th>{{ trans('privateLoginLevels.level_show_in_registration') }}</th>
                    <th>
                        @if(Session::get('user_role') == 'admin' || ONE::verifyUserPermissionsCreate('orchestrator', 'site_login_levels'))
                            <a href="{{ action('LoginLevelsController@create', ['siteKey' => isset($siteKey) ? $siteKey : null])}}" class="btn btn-flat btn-success btn-sm" data-toggle="tooltip" data-delay="{&quot;show&quot;:&quot;1000&quot;}" title="" data-original-title="Criar"><i class="fa fa-plus"></i></a>
                        @endif
                    </th>
                </tr>
                </thead>
            </table>
        </div>
    </div>
@endsection


@section('scripts')
    <script>
        $(function () {
            $('#login_levels_list').DataTable({
                language: {
                    url: '{!! asset('/datatableLang/'.Session::get('LANG_CODE').'.json') !!}',
                    search: '<a class="btn searchBtn" id="searchBtn"><i class="fa fa-search"></i></a>'
                },
                processing: true,
                serverSide: true,
                ajax: '{!! action('LoginLevelsController@getIndexTable', ['siteKey' => isset($siteKey) ? $siteKey : null]) !!}',
                columns: [
                    { data: 'position', name: 'position' },
                    { data: 'name', name: 'name' },
                    { data: 'mandatory', name: 'mandatory' },
                    { data: 'manual_verification', name: 'manual_verification' },
                    { data: 'sms_verification', name: 'sms_verification' },
                    { data: 'show_in_registration', name: 'show_in_registration' },
                    { data: 'action', name: 'action', searchable: false, orderable: false, width: "50px" },
                ],
                order: [[ 0, 'asc' ]]
            });
        });
    </script>
@endsection
