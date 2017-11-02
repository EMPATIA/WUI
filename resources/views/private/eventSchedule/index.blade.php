@extends('private._private.index')

@section('content')
    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title"><i class="fa"></i> {{ trans('title.eventSchedules') }}</h3>
        </div>

        <div class="box-body">
            {!! ONE::messages() !!}
            <table id="event_schedule_list" class="table table-hover table-striped dataTable no-footer table-responsive">
                <thead>
                <tr>
                    <th>{{ trans('eventSchedule.title') }}</th>
                    <th>@if(ONE::verifyUserPermissions('q', 'poll', 'create')){!! ONE::actionButtons(null, ['create' => 'EventSchedulesController@create']) !!}@endif</th>
                </tr>
                </thead>
            </table>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(function () {
            $('#event_schedule_list').DataTable({
                language: {
                    url: '{!! asset('/datatableLang/'.Session::get('LANG_CODE').'.json') !!}',
                    search: '<a class="btn searchBtn" id="searchBtn"><i class="fa fa-search"></i></a>'
                },
                processing: true,
                serverSide: true,
                ajax: '{!! action('EventSchedulesController@getIndexTable') !!}',
                columns: [
                    { data: 'title', name: 'title' },
                    { data: 'action', name: 'action', searchable: false, orderable: false, width: "50px" },
                ],
                order: [['0', 'asc']]
            });
        });
    </script>
@endsection