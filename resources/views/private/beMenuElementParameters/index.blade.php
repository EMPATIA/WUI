@extends('private._private.index')

@section('content')
    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title"><i class="fa"></i> {{ trans('private.beMenuElementParameters') }}</h3>
        </div>

        <div class="box-body">
            <table id="parameters_list" class="table table-hover table-striped table-responsive">
                <thead>
                    <tr>
                        <th>{{ trans('privateBEMenuElementParameters.id') }}</th>
                        <th>{{ trans('privateBEMenuElementParameters.key') }}</th>
                        <th>{{ trans('privateBEMenuElementParameters.code') }}</th>
                        <th>{{ trans('privateBEMenuElementParameters.name') }}</th>
                        <th>{{ trans('privateBEMenuElementParameters.created_at') }}</th>
                        <th>
                            {!! ONE::actionButtons(null, ['create' => 'BEMenuElementParametersController@create']) !!}
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
            $('#parameters_list').DataTable({
                language: {
                    url: '{!! asset('/datatableLang/'.Session::get('LANG_CODE').'.json') !!}',
                    search: '<a class="btn searchBtn" id="searchBtn"><i class="fa fa-search"></i></a>'
                },
                processing: true,
                serverSide: true,
                ajax: '{!! action('BEMenuElementParametersController@getIndexTable') !!}',
                columns: [
                    { data: 'id', name: 'id', width: "25px" },
                    { data: 'key', name: 'key' },
                    { data: 'code', name: 'code' },
                    { data: 'name', name: 'name' },
                    { data: 'created_at', name: 'created_at' },
                    { data: 'action', name: 'action', searchable: false, orderable: false, width: "60px" }
                ],
                order: [['1', 'asc']]
            });

        });

    </script>
@endsection
