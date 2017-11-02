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
                        <h3 class="box-title"><i class="fa fa-users"></i> {{ trans('privateEntities.managers') }}</h3>
                        <div class="entityManagers-actionBtns">
                            <a href="{{ action('EntitiesController@createManager', $entity->entity_key)  }}" class="btn btn-flat empatia" data-toggle="tooltip" data-delay="{'show':'1000'}" title="" data-original-title="Criar">
                                <i class="fa fa-plus"></i> {{ trans('privateEntities.create') }}
                            </a>
                            <a href="{{ action('EntitiesController@addManager', $entity->entity_key)  }}" class="btn btn-flat empatia-dark" data-toggle="tooltip" data-delay="{'show':'1000'}" title="" data-original-title="Criar">
                                <i class="fa fa-plus"></i> {{ trans('privateEntities.add') }}
                            </a>
                        </div>
                    </div>
                    <div class="box-body">
                        <table id="managers_list" class="table table-striped dataTable no-footer table-responsive">
                            <thead>
                            <tr>
                                <th width="90%">{{ trans('privateEntities.managers') }}</th>
                                <th>
                                {{--
                                    {!! ONE::actionButtons($entity->entity_key, ['create' => 'EntitiesController@createManager', 'title' => '']) !!}
                                    {!! ONE::actionButtons($entity->entity_key, ['add' => 'EntitiesController@addManager', 'title' => '']) !!}
                                --}}
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
            getSidebar('{{ action("OneController@getSidebar") }}', 'managers', "{{isset($entity) ? $entity->entity_key : null}}", 'sidebar_admin.entities' )
        })

        $(function () {
            $('#managers_list').DataTable({
                language: {
                    url: '{!! asset('/datatableLang/'.Session::get('LANG_CODE').'.json') !!}',
                    search: '<a class="btn searchBtn" id="searchBtn"><i class="fa fa-search"></i></a>'
                },
                processing: true,
                serverSide: true,
                ajax: '{!! action("EntitiesController@tableUsersEntity", $entity->entity_key) !!}',
                columns: [
                    {data: 'name', name: 'name'},
                    {data: 'action', name: 'action', searchable: false, orderable: false}
                ],
                order: [['0', 'asc']]
            });
        });
    </script>
@endsection