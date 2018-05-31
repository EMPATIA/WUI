@extends('private._private.index')

@section('content')
    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title"><i class="fa"></i> {{ trans('privateParameterTypes.list') }}</h3>
        </div>

        <div class="box-body">
            <table id="voteMethods_list" class="table table-hover table-striped dataTable no-footer table-responsive">
                <thead>
                <tr>
                    <th>{{ trans('privateParameterTypes.code') }}</th>
                    <th>{{ trans('privateParameterTypes.name') }}</th>
                    <th>{!! ONE::actionButtons(null, ['create' => 'ParameterTypesController@create']) !!}</th>
                </tr>
                </thead>
            </table>
        </div>
    </div>

@endsection


@section('scripts')
    <script>

        $(function () {
            $('#voteMethods_list').DataTable({
                language: {
                    url: '{!! asset('/datatableLang/'.Session::get('LANG_CODE').'.json') !!}',
                    search: '<a class="btn searchBtn" id="searchBtn"><i class="fa fa-search"></i></a>'
                },
                processing: true,
                serverSide: true,
                ajax: '{!! action('ParameterTypesController@getIndexTable') !!}',
                columns: [
                    { data: 'code', name: 'code', width: "20px" },
                    { data: 'name', name: 'name' },
                    { data: 'action', name: 'action', searchable: false, orderable: false, width: "30px" },
                ],
                order: [['1', 'asc']]
            });

        });

    </script>
@endsection



