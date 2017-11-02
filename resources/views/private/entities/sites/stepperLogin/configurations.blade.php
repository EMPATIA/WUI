@extends('private._private.index')

@section('content')
    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title"><i class="fa"></i> {{ trans('privateLoginLevels.login_level_parameters') }}</h3>
        </div>

        <div class="box-body">
            <table id="login_configurations_list" class="table table-striped dataTable no-footer table-responsive">
                <thead>
                <tr>
                    <th>{{ trans('privateLoginLevels.parameter_name') }}</th>
                    <th>{{ trans('privateLoginLevels.parameter_mandatory') }}</th>
                    <th>{{ trans('privateLoginLevels.parameter_selected') }}</th>
                </tr>
                </thead>
            </table>
        </div>
    </div>
@endsection

@section('scripts')
    <script>

        $(function() {
            var array = ["{{isset($siteKey) ? $siteKey : null }}", "{{isset($levelParameterKey) ? $levelParameterKey : null}}"];
            getSidebar('{{ action("OneController@getSidebar") }}', 'parameters', array, 'loginLevelsParameters' );
        });

        $(document).ready(function(){
            var table = $('#login_configurations_list').DataTable({
                language: {
                    url: '{!! asset('/datatableLang/'.Session::get('LANG_CODE').'.json') !!}',
                    search: '<a class="btn searchBtn" id="searchBtn"><i class="fa fa-search"></i></a>'
                },
                processing: true,
                serverSide: true,
                ajax: '{!! action('LoginLevelsController@getIndexConfigurationsTable', ['levelParameterKey' => isset($levelParameterKey) ? $levelParameterKey : null]) !!}',
                columns: [
                    { data: 'name', name: 'code' },
                    { data: 'mandatory', name: 'mandatory' },
                    { data: 'selected', name: 'selected' }
                ],
                order: [[ 2, 'desc' ]]
            });

            $(document).on("click", ".update-btn", function(event) {
                var updateParameter = $(this).attr('href');
                $.ajax({
                    method: 'POST',
                    url: updateParameter,
                    data: {},
                    success: function (response) {
                        table.ajax.reload();
                        toastr.success('{{trans('privateLoginLevels.update_ok') }}', '', {timeOut: 2000,positionClass: "toast-bottom-right"});
                    },
                    error: function () {
                    }
                });
                return false;
            });
        });
    </script>
@endsection
