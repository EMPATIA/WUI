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
                    <th>
                        <a href="{{ action('LoginLevelsController@create', ['siteKey' => isset($siteKey) ? $siteKey : null])}}" class="btn btn-flat btn-success btn-sm" data-toggle="tooltip" data-delay="{&quot;show&quot;:&quot;1000&quot;}" title="" data-original-title="Criar"><i class="fa fa-plus"></i></a>
                    </th>
                </tr>
                </thead>
            </table>
        </div>
    </div>
@endsection


@section('scripts')

    <script>
        $(function() {
            getSidebar('{{ action("OneController@getSidebar") }}', 'stepperLogin', '{{ isset($siteKey) ? $siteKey : null }}', 'site' );
        });
    </script>

    <script>
        $(function () {
            $('#login_levels_list').DataTable({
                language: {
                    url: '{!! asset('/datatableLang/'.Session::get('LANG_CODE').'.json') !!}',
                    search: '<a class="btn searchBtn" id="searchBtn"><i class="fa fa-search"></i></a>'
                },
                processing: true,
                serverSide: true,
                ajax: '{!! action('StepperLoginController@getIndexTable', ['siteKey' => isset($siteKey) ? $siteKey : null]) !!}',
                columns: [
                    { data: 'position', name: 'position' },
                    { data: 'name', name: 'name' },
                    { data: 'action', name: 'action', searchable: false, orderable: false, width: "30px" },
                ],
                order: [[ 0, 'asc' ]]
            });
        });
    </script>
@endsection
