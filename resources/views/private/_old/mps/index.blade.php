@extends('private._private.index')

@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title"><i class="fa"></i> {{ trans('title.mps') }}</h3>
        </div>

        <div class="box-body">
            <table id="mps_list" class="table table-hover table-striped dataTable no-footer table-responsive">
                <thead>
                <tr>
                    <th>{{ trans('form.id') }}</th>
                    <th>{{ trans('form.name') }}</th>
                    <th>{!! ONE::actionButtons(null, ['create' => 'MPsController@create']) !!}</th>
                </tr>
                </thead>
            </table>
        </div>
    </div>

@endsection


@section('scripts')
    <script>

        $(function () {
            $('#mps_list').DataTable({
                language: {
                    url: '{!! asset('/datatableLang/'.Session::get('LANG_CODE').'.json') !!}',
                    search: '<a class="btn searchBtn" id="searchBtn"><i class="fa fa-search"></i></a>'
                },
                processing: true,
                serverSide: true,
                ajax: '{!! action('MPsController@tableMPs') !!}',
                columns: [
                    { data: 'id', name: 'id', width: "20px" },
                    { data: 'name', name: 'name' },
                    { data: 'action', name: 'action', searchable: false, orderable: false, width: "30px" },
                ],
                order: [['1', 'asc']]
            });

        });
        
    </script>
@endsection

