@extends('private._private.index')

@section('content')
    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title"><i class="fa"></i> {{ trans('privateLoginLevels.login_level_parameters') }}</h3>
        </div>

        <div class="box-body">
            <table id="login_parameters_list" class="table table-striped dataTable no-footer table-responsive">
                <thead>
                <tr>
                    <th>{{ trans('privateEntityLoginLevels.parameter_name') }}</th>
                    <th>{{ trans('privateEntityLoginLevels.parameter_mandatory') }}</th>
                    <th></th>

                </tr>
                </thead>
            </table>
        </div>
    </div>
@endsection

@section('scripts')
    <script>

        {{--$(function() {--}}
            {{--var array = ["{{isset($loginLevelKey) ? $loginLevelKey : null}}","{{isset($entityKey) ? $entityKey : null }}"];--}}
            {{--getSidebar('{{ action("OneController@getSidebar") }}', 'parameters', array, 'entityLoginLevels' );--}}
        {{--});--}}

        $(document).ready(function(){

            var table = $('#login_parameters_list').DataTable({
                language: {
                    url: '{!! asset('/datatableLang/'.Session::get('LANG_CODE').'.json') !!}',
                    search: '<a class="btn searchBtn" id="searchBtn"><i class="fa fa-search"></i></a>'
                },
                processing: true,
                serverSide: true,
                ajax: '{!! action('EntityLoginLevelsController@getIndexParametersTable', ['loginLevelKey' =>  $loginLevelKey ?? null]) !!}',
                columns: [
                    { data: 'name', name: 'code',width:"45%"},
                    { data: 'mandatory', name: 'mandatory',width:"45%"},
                    { data: 'selected', name: 'selected', searchable: false, orderable: true, width: "10%"},
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
                        toastr.success('{{trans('privateEntityLoginLevels.update_ok') }}', '', {timeOut: 2000,positionClass: "toast-bottom-right"});
                    },
                    error: function () {
                    }
                });
                return false;
            });
        });


        function updateLoginLevelParameter(url){
            var table = $('#login_parameters_list').DataTable();
            $.ajax({
                method: 'POST',
                url: url,
                data: {},
                success: function (response) {
                    table.ajax.reload();
                    toastr.success('{{trans('privateEntityLoginLevels.update_ok') }}', '', {timeOut: 2000,positionClass: "toast-bottom-right"});
                },
                error: function () {
                }
            });
            return false;
        }
    </script>
@endsection
