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

    @if(!ONE::isEntity() && ONE::actionType('entities') == "show")

        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header">
                        <h3 class="box-title"><i class="fa fa-file-text-o"></i> {{ trans('privateEntities.templates') }}</h3>
                    </div>
                    <div class="box-body">
                        <table id="layouts_list" class="table table-striped dataTable no-footer table-responsive">
                            <thead>
                            <tr>
                                <th width="90%">{{ trans('privateEntities.templateName') }}</th>
                                <th width="10%">
                                    {!! ONE::actionButtons(isset($entity) ? $entity->entity_key : null, ['add' => 'EntitiesController@addLayout']) !!}
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
            getSidebar('{{ action("OneController@getSidebar") }}', 'layouts', "{{isset($entity) ? $entity->entity_key : null}}", 'sidebar_admin.entities' )
        });

        $(function () {
            $('#layouts_list').DataTable({
                language: {
                    url: '{!! asset('/datatableLang/'.Session::get('LANG_CODE').'.json') !!}',
                    search: '<a class="btn searchBtn" id="searchBtn"><i class="fa fa-search"></i></a>'
                },
                processing: true,
                serverSide: true,
                ajax: '{!! action("EntitiesController@tableLayoutsEntity", isset($entity) ? $entity->entity_key : null) !!}',
                columns: [
                    { data: 'name', name: 'name' },
                    { data: 'action', name: 'action', searchable: false, orderable: false },
                ],
                order: [['1', 'asc']]
            });
        });
    </script>
@endsection