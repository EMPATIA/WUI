@extends('private._private.index')

@section('content')
    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title"><i class="fa"></i> {{ trans('privatePhases.title') }}</h3>
        </div>

        <div class="box-body">
            <table id="phases_list" class="table table-hover table-striped dataTable no-footer table-responsive">
                <thead>
                <tr>
                    <th>{{ trans('privatePhases.id') }}</th>
                    <th>{{ trans('privatePhases.name') }}</th>
                    <th>{{ trans('privatePhases.startDate') }}</th>
                    <th>{{ trans('privatePhases.endDate') }}</th>
                    <th>{{ trans('privatePhases.entityId') }}</th>
                    <th>{!! ONE::actionButtons(null, ['create' => 'PhasesController@create']) !!}</th>
                </tr>
                </thead>
            </table>
        </div>
    </div>

@endsection


@section('scripts')
    <script>

        $(function () {
            $('#phases_list').DataTable({
                language: {
                    url: '{!! asset('/datatableLang/'.Session::get('LANG_CODE').'.json') !!}',
                    search: '<a class="btn searchBtn" id="searchBtn"><i class="fa fa-search"></i></a>'
                },
                processing: true,
                serverSide: true,
                ajax: '{!! action('PhasesController@tablePhases') !!}',
                columns: [
                    { data: 'id', name: 'id', width: "20px" },
                    { data: 'start_date', name: 'start_date' },
                    { data: 'end_date', name: 'end_date' },
                    { data: 'entity_id', name: 'entity_id' },
                    { data: 'action', name: 'action', searchable: false, orderable: false, width: "30px" },
                ],
                order: [['1', 'asc']]
            });

        });
        
    </script>
@endsection



