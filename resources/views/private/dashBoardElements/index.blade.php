@extends('private._private.index')

@section('content')
    <div class="box box-primary">
        <!-- Header -->
        <div class="box-header">
            <h3 class="box-title"><i class="fa"></i> {{ trans('dashBoardElements.list') }}</h3>
        </div>
        <!-- Body -->
        <div class="box-body">
            <table id="dashboard_elements_list" class="table table-striped dataTable no-footer table-responsive">
                <thead>
                <tr>
                    <th>{{ trans('dashBoardElements.title') }}</th>
                    <th> {!! ONE::actionButtons(null, ['create' => 'DashBoardElementsController@create']) !!}</th>
                </tr>
                </thead>
            </table>
        </div>
    </div>
@endsection


@section('scripts')

    <script>
        $(function () {

            $('#dashboard_elements_list').DataTable({
                language: {
                    url: '{!! asset('/datatableLang/'.Session::get('LANG_CODE').'.json') !!}',
                    search: '<a class="btn searchBtn" id="searchBtn"><i class="fa fa-search"></i></a>'
                },
                processing: true,
                serverSide: true,
                ajax: '{!! action('DashBoardElementsController@getIndexTable') !!}',
                columns: [
                    { data: 'title', name: 'title', searchable: true },
                    { data: 'action', name: 'action', searchable: false, orderable: false, width: "50px" },
                ],
                order: [[ 0, 'asc' ]]
            });


        });

    </script>
@endsection
