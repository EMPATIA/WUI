@extends('private._private.index')

@section('content')
    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title"><i class="fa"></i> {{ trans('title.menus') }}</h3>
        </div>

        <div class="box-body">
            <table id="menus_list" class="table table-hover table-striped dataTable no-footer table-responsive">
                <thead>
                <tr>
                    <th>{{ trans('form.id') }}</th>
                    <th>{{ trans('form.title') }}</th>
                    <th>{{ trans('menus.access_type_id') }}</th>
                    <th>{{ trans('menus.parent_id') }}</th>
                    <th>{!! ONE::actionButtons(null, ['create' => 'MenusController@create']) !!}</th>
                </tr>
                </thead>
            </table>
        </div>
    </div>

@endsection


@section('scripts')
    <script>
        
        $(function () {
            $('#menus_list').DataTable({
                language: {
                    url: '{!! asset('/datatableLang/'.Session::get('LANG_CODE').'.json') !!}',
                    search: '<a class="btn searchBtn" id="searchBtn"><i class="fa fa-search"></i></a>'
                },
                processing: true,
                serverSide: true,
                ajax: '{!! action('MenusController@tableMenus') !!}',
                columns: [
                    { data: 'id', name: 'id', width: "20px" },
                    { data: 'title', name: 'title' },
                    { data: 'access_type_id', name: 'access_type_id' },
                    { data: 'parent_id', name: 'parent_id' },
                    { data: 'action', name: 'action', searchable: false, orderable: false, width: "30px" },
                ],
                order: [['1', 'asc']]
            });

        });
        
    </script>
@endsection