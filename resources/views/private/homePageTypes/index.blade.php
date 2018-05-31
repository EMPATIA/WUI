@extends('private._private.index')

@section('content')
    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title"><i class="fa"></i> {{ trans('homePageType.title') }}</h3>
        </div>
        <div class="box-body">
            <table id="homePageTypes_list" class="table table-hover table-striped dataTable no-footer table-responsive">
                <thead>
                <tr>
                    <th>{{ trans('homePageType.name') }}</th>
                    <th>@if(Session::get('user_role') == 'admin'){!! ONE::actionButtons(null, ['create' => 'HomePageTypesController@create']) !!}@endif</th>
                </tr>
                </thead>
            </table>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(function () {
            $('#homePageTypes_list').DataTable({
                language: {
                    url: '{!! asset('/datatableLang/'.Session::get('LANG_CODE').'.json') !!}',
                    search: '<a class="btn searchBtn" id="searchBtn"><i class="fa fa-search"></i></a>'
                },
                processing: true,
                serverSide: true,
                ajax: '{!! action('HomePageTypesController@getIndexTable') !!}',
                columns: [
                    { data: 'name', name: 'name' },
                    { data: 'action', name: 'action', searchable: false, orderable: false, width: "50px" },
                ],
                order: [['0', 'asc']]
            });
        });
    </script>
@endsection



