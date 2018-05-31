@extends('private._private.index')

@section('content')
    @if(ONE::isEntity())
        @php $form = ONE::form('entities', trans('privateEntities.details'))
                ->settings(["model" => isset($entity) ? $entity : null])
                ->show('EntitiesController@edit', null, ['id' => isset($entity) ? $entity->entity_key : null])
                ->create('EntitiesController@store', 'EntitiesController@index', ['id' => isset($entity) ? $entity->entity_key : null])
                ->edit('EntitiesController@update', 'EntitiesController@show', ['id' => isset($entity) ? $entity->entity_key : null])
                ->open();
        @endphp
    @else
        @php $form = ONE::form('entities')
                ->settings(["model" => isset($entity) ? $entity : null])
                ->show('EntitiesController@edit', 'EntitiesController@delete', ['id' => isset($entity) ? $entity->entity_key : null], 'EntitiesController@index')
                ->create('EntitiesController@store', 'EntitiesController@index', ['id' => isset($entity) ? $entity->entity_key : null])
                ->edit('EntitiesController@update', 'EntitiesController@show', ['id' => isset($entity) ? $entity->entity_key : null])
                ->open();
        @endphp
    @endif

    @if(ONE::actionType('entities') == "show")
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header">
                        <h3 class="box-title"><i class="fa fa-language"></i> {{ trans('privateEntities.site_title') }}</h3>
                    </div>
                    <div class="box-body">
                        <table id="sites_list" class="table table-striped dataTable no-footer table-responsive">
                            <thead>
                            <tr>
                                <th width="50%">{{ trans('privateEntities.site_name') }}</th>
                                <th width="40%">{{ trans('privateEntities.site_url') }}</th>
                                <th width="10%">
                                    {!! ONE::actionButtons($entity->entity_key, ['create' => 'EntitiesController@createEntitySite']) !!}
                                </th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @endif

@endsection

@section('scripts')
    <script>
        $(function() {
            getSidebar('{{ action("OneController@getSidebar") }}', 'sites', "{{isset($entity) ? $entity->entity_key : null}}", 'sidebar_admin.entities' )
        })

        $(function () {
            $('#sites_list').DataTable({
                language: {
                    url: '{!! asset('/datatableLang/'.Session::get('LANG_CODE').'.json') !!}',
                    search: '<a class="btn searchBtn" id="searchBtn"><i class="fa fa-search"></i></a>'
                },
                processing: true,
                serverSide: true,
                ajax: '{!! action("EntitiesController@tableSitesEntity", $entity->entity_key) !!}',
                columns: [
                    { data: 'name', name: 'name' },
                    { data: 'link', name: 'link'},
                    { data: 'action', name: 'action', searchable: false, orderable: false },
                ],
                order: [['1', 'asc']]
            });
        });
    </script>
@endsection