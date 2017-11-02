@extends('private._private.index')

@section('content')
    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title"><i class="fa"></i> {{ trans('privateEntityLoginLevels.login_levels') }}</h3>
        </div>

        <div class="box-body">
            @if(Session::has('user_role') == 'admin')
            <div class="box-footer clearfix">
                <a type="button" class="btn btn-flat btn-success pull-right" href="{{action('EntityLoginLevelsController@updateAllUserLevels')}}">{!! trans("privateEntityLoginLevels.update_all_user_levels") !!}</a>
            </div>
            @endif
            <table id="login_levels_list" class="table table-striped dataTable no-footer table-responsive">
                <thead>
                <tr>
                    <th>{{ trans('privateEntityLoginLevels.level_name') }}</th>
                    <th>
                        {!! ONE::actionButtons(null, ['create' => 'EntityLoginLevelsController@create']) !!}
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
            $('#login_levels_list').DataTable({
                language: {
                    url: '{!! asset('/datatableLang/'.Session::get('LANG_CODE').'.json') !!}',
                    search: '<a class="btn searchBtn" id="searchBtn"><i class="fa fa-search"></i></a>'
                },
                processing: true,
                serverSide: true,
                ajax: '{!! action('EntityLoginLevelsController@getIndexTable', ['entityKey' => $entityKey ?? null]) !!}',
                columns: [
                    { data: 'name', name: 'name' },
                    { data: 'action', name: 'action', searchable: false, orderable: false, width: "30px" },
                ],
                order: [[ 0, 'asc' ]]
            });
        });
    </script>
@endsection
