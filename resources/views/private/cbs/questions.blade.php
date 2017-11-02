@extends('private._private.index')

@section('content')
    <div class="box-private">
        <div class="box-header">
            <h3 class="box-title">
                {{ trans('privateCbs.questions') }}
            </h3>
        </div>

        <div class="box-body">
            <div class="dataTables_wrapper dt-bootstrap no-footer">
                <table id="questions_list" class="table table-responsive  table-hover table-striped ">
                    <thead>
                    <tr>
                        <th>{{ trans('privateCbs.questions') }}</th>
                        <th>
                            @if(Session::get('user_role') == 'admin' || ONE::verifyUserPermissionsCreate('cb', 'questions'))
                                {!! ONE::actionButtons(['type'=>$type,'cbKey'=>$cb->cb_key], ['create' => 'TechnicalAnalysisProcessesController@create']) !!}
                            @endif
                        </th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        var table;
        $(function () {
            // Questions List
            $('#questions_list').DataTable({
                language: {
                    url: '{!! asset('/datatableLang/'.Session::get('LANG_CODE').'.json') !!}',
                    search: '<a class="btn searchBtn" id="searchBtn"><i class="fa fa-search"></i></a>'
                },
                responsive: true,
                processing: true,
                serverSide: true,
                ajax: '{!! action('TechnicalAnalysisProcessesController@getIndexTable',['type'=>$type,'cbKey'=>$cb->cb_key]) !!}',
                columns: [
                    {data: 'question', name: 'question', width: "90%"},
                    { data: 'action', name: 'action', searchable: false, orderable: false}
                ],
                order: [['0', 'asc']]
            });
        });
    </script>
@endsection