@extends('private._private.index')

@section('content')
    <div class="box box-primary">

        <div class="box-header">
            <h3 class="box-title"><i class="fa"></i> {{ trans('privateEntityGroups.entity_groups') }}</h3>
        </div>

        <div class="box-body">
            <table id="entity_groups_list" class="table table-striped dataTable no-footer table-responsive">
                <thead>
                <tr>
                    <th>{{ trans('privateEntityGroups.name') }}</th>
                    <th>{{ trans('privateEntityGroups.designation') }}</th>
                    <th>
                        <a href="{{ action('EntityGroupsController@create', ["groupTypeKey" => is_null($groupTypeKey) ? null : $groupTypeKey]) }}" class="btn btn-flat btn-success btn-sm" data-toggle="tooltip" data-delay="{&quot;show&quot;:&quot;1000&quot;}" title="" data-original-title="Criar"><i class="fa fa-plus"></i></a>
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
            var groupTypeKey = "{{ is_null($groupTypeKey) ? null : $groupTypeKey }}"

            getSidebar('{{ action("OneController@getSidebar") }}', 'entity_groups_list', groupTypeKey , 'entityGroup')

            $('#entity_groups_list').DataTable({
                language: {
                    url: '{!! asset('/datatableLang/'.Session::get('LANG_CODE').'.json') !!}',
                    search: '<a class="btn searchBtn" id="searchBtn"><i class="fa fa-search"></i></a>'
                },
                processing: true,
                serverSide: true,
                ajax: '{!! action('EntityGroupsController@tableEntityGroups', ["groupTypeKey" => is_null($groupTypeKey) ? null : $groupTypeKey]) !!}',
                columns: [
                    { data: 'name', name: 'name' },
                    { data: 'designation', name: 'designation' },
                    { data: 'action', name: 'action', searchable: false, orderable: false, width: "30px" },
                ],
                order: [[ 1, 'asc' ]]
            });

        });

    </script>
@endsection

