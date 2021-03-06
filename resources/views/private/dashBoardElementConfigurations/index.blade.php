@extends('private._private.index')

@section('content')
    <div class="box box-primary">

        <div class="box-header">
            <h3 class="box-title"><i class="fa"></i> {{ trans('dashBoardElements.list') }}</h3>
        </div>

        <div class="box-body">
            <table id="dashboard_element_configurations_list" class="table table-striped dataTable no-footer table-responsive">
                <thead>
                <tr>
                    <th>{{ trans('privateDashBoardElementConfigurations.title') }}</th>
                    <th> {!! ONE::actionButtons(null, ['create' => 'DashBoardElementConfigurationsController@create']) !!}</th>
                </tr>
                </thead>
            </table>
        </div>
    </div>

@endsection


@section('scripts')

    <script>
        $(function () {

            $('#dashboard_element_configurations_list').DataTable({
                language: {
                    url: '{!! asset('/datatableLang/'.Session::get('LANG_CODE').'.json') !!}',
                    search: '<a class="btn searchBtn" id="searchBtn"><i class="fa fa-search"></i></a>'
                },
                processing: true,
                serverSide: true,
                ajax: '{!! action('DashBoardElementConfigurationsController@getIndexTable') !!}',
                columns: [
                    { data: 'title', name: 'title', searchable: true },
                    { data: 'action', name: 'action', searchable: false, orderable: false, width: "50px" },
                ],
                order: [[ 1, 'asc' ]]
            });


        });

    </script>
@endsection
