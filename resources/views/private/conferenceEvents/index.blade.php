@extends('private._private.index')

@section('content')
    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title"><i class="fa"></i> {{ trans('conferenceEvents.event') }}</h3>
        </div>

        <div class="box-body">
            <table id="event_list" class="table table-hover table-striped dataTable no-footer table-responsive">
                <thead>
                <tr>
                    <th>{{ trans('conferenceEvents.key') }}</th>
                    <th>{{ trans('conferenceEvents.name') }}</th>
                    <th>{!! ONE::actionButtons(null, ['create' => 'ConferenceEventsController@create']) !!}</th>
                </tr>
                </thead>
            </table>
        </div>
    </div>

@endsection


@section('scripts')
    <script>

        $(function () {
            $('#event_list').DataTable({
                language: {
                    url: '{!! asset('/datatableLang/'.Session::get('LANG_CODE').'.json') !!}',
                    search: '<a class="btn searchBtn" id="searchBtn"><i class="fa fa-search"></i></a>'
                },
                processing: true,
                serverSide: true,
                ajax: '{!! action('ConferenceEventsController@getIndexTable') !!}',
                columns: [
                    { data: 'event_key', name: 'event_key', width: "20px" },
                    { data: 'title', name: 'title' },
                    { data: 'action', name: 'action', searchable: false, orderable: false, width: "30px" },
                ],
                order: [['1', 'asc']]
            });

        });

    </script>
@endsection
