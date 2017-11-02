@extends('public.empaville._layouts.index')

@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title"><i class="fa"></i> {{$title}}</h3>
        </div>

        <div class="box-body">
                <table id="event_registrations" class="table table-bordered table-hover responsive display" >
                    <thead>
                    <tr>
                        <th>{{ trans('PublicConference.listRegistrationUserName') }}</th>
                        <th>{{ trans('PublicConference.listRegistrationUserEmail') }}</th>
                    </tr>
                    </thead>
                </table>

        </div>
    </div>
@endsection
@section('scripts')



    <script>
        $(document).ready(function() {
            var table = $('#event_registrations').DataTable({
                language: {
                    url: '{!! asset('/datatableLang/'.Session::get('LANG_CODE').'.json') !!}'
                },
                processing: true,
                serverSide: true,
                ajax: '{!! action('PublicConfEventsController@getRegistrationTable',$eventKey) !!}',
                columns: [
                    { data: 'name', name: 'name', responsivePriority: 1},
                    { data: 'email', name: 'email', responsivePriority: 0}
                ],
                responsive: true,
                scrollX : false
            });
        });

    </script>
@endsection