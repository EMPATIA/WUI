@extends('private._private.index')

@section('content')
    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title"><i class="fa"></i> </h3>
        </div>

        <div class="box-body">
            <table class="table table-hover table-striped dataTable no-footer table-responsive" id="tracking-table">
                <thead>
                <tr>
                    <th>Id</th>
                    <th>User Key</th>
                    <th>Ip</th>
                    <th>Url</th>
                    <th>Method</th>
                    <th>Time</th>
                    <th>Action</th>
                </tr>
                </thead>

            </table>
        </div>
    </div>
@endsection

@section('scripts')

    <script>
        $(function() {
            $('#tracking-table').DataTable({
                language: {
                    url: '{!! asset('/datatableLang/'.Session::get('LANG_CODE').'.json') !!}',
                    search: '<a class="btn searchBtn" id="searchBtn"><i class="fa fa-search"></i></a>'
                },
                processing: true,
                scrollX:    true,
                serverSide: true,
                ajax: '{!! action('TrackingController@getTrackingTable') !!}',
                columns: [
                    { data: 'id', name: 'id'},
                    { data: 'user_key', name: 'user_key'},
                    { data: 'ip', name: 'ip' },
                    { data: 'url', name: 'url'},
                    { data: 'method', name: 'method'},
                    { data: 'time', name: 'time' },
                    { data: 'action', name: 'action' }
                ],
                order: [['0', 'desc']]
            });
        });
    </script>

@endsection