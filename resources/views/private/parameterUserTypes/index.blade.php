@extends('private._private.index')

@section('content')
    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title"><i class="fa"></i> {{ trans('privateParameterUserTypes.list') }}</h3>
        </div>

        <div class="box-body">
            <table id="parameter_user_types_list" class="table table-hover table-striped dataTable no-footer table-responsive">
                <thead>
                <tr>
                    <th>{{ trans('privateParameterUserTypes.id') }}</th>
                    <th>{{ trans('privateParameterUserTypes.code') }}</th>
                    <th>{{ trans('privateParameterUserTypes.name') }}</th>
                    @if(!empty($entityKey))
                        <th>
                            {!! ONE::actionButtons(null, ['create' => 'ParameterUserTypesController@create']) !!}
                        </th>
                    @endif
                </tr>
                </thead>
            </table>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(function () {
            $('#parameter_user_types_list').DataTable({
                language: {
                    url: '{!! asset('/datatableLang/'.Session::get('LANG_CODE').'.json') !!}',
                    search: '<a class="btn searchBtn" id="searchBtn"><i class="fa fa-search"></i></a>'
                },
                processing: true,
                serverSide: true,
                ajax: '{!! action('ParameterUserTypesController@getIndexTable') !!}',
                columns: [
                    { data: 'id', name: 'id', width: "20px" },
                    { data: 'code', name: 'code', width: "50px" },
                    { data: 'name', name: 'name'},
                        @if(!empty($entityKey))
                    { data: 'action', name: 'action', searchable: false, orderable: false, width: "50px" },
                    @endif
                ],
                order: [['0', 'desc']]
            });
        });
    </script>
@endsection