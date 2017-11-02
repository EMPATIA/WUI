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

    {{--    @if(ONE::actionType('entities') == 'edit')--}}
    {!! Form::oneText('name', trans('privateEntities.name'), isset($entity) ? $entity->name : null, ['class' => 'form-control', 'id' => 'name']) !!}
    {{--@endif--}}

    {!! Form::oneText('designation', trans('privateEntities.designation'), isset($entity) ? $entity->designation : null, ['class' => 'form-control', 'id' => 'designation']) !!}
    {!! Form::oneText('description', trans('privateEntities.description'), isset($entity) ? $entity->description : null, ['class' => 'form-control', 'id' => 'description']) !!}
    {!! Form::oneText('url', trans('privateEntities.url'), isset($entity) ? $entity->url : null, ['class' => 'form-control', 'id' => 'url']) !!}
    {!! Form::oneSelect('country_id', trans('privateEntities.country'), isset($country) ? $country : null, isset($entity) ? $entity->country_id : null, isset($entity->country->name) ? $entity->country->name: null, ['class' => 'form-control', 'id' => 'country_id']) !!}
    {!! Form::oneSelect('timezone_id', trans('privateEntities.timezone'), isset($timezone) ? $timezone : null, isset($entity) ? $entity->timezone_id : null, isset($entity->timezone->name) ? $entity->timezone->name : null, ['class' => 'form-control', 'id' => 'timezone_id']) !!}
    {!! Form::oneSelect('currency_id', trans('privateEntities.currency'), isset($currency) ? $currency : null, isset($entity) ? $entity->currency_id : null, isset($entity->currency->currency) ? $entity->currency->currency : null, ['class' => 'form-control', 'id' => 'currency_id']) !!}

    {!! $form->make() !!}

    @if(!ONE::isEntity() && ONE::actionType('entities') == "show")

        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header">
                        <h3 class="box-title"><i class="fa fa-file-text-o"></i> {{ trans('privateEntities.layouts') }}</h3>
                    </div>
                    <div class="box-body">
                        <table id="layouts_list" class="table table-striped dataTable no-footer table-responsive">
                            <thead>
                            <tr>
                                <th width="90%">{{ trans('privateEntities.layoutName') }}</th>
                                <th width="10%">
                                    {!! ONE::actionButtons($entity->entity_key, ['add' => 'EntitiesController@addLayout']) !!}
                                </th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    @endif

    @if(ONE::actionType('entities') == "show")
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header">
                        <h3 class="box-title"><i class="fa fa-language"></i> {{ trans('privateEntities.siteTitle') }}</h3>
                    </div>
                    <div class="box-body">
                        <table id="sites_list" class="table table-striped dataTable no-footer table-responsive">
                            <thead>
                            <tr>
                                <th width="50%">{{ trans('privateEntities.siteName') }}</th>
                                <th width="40%">{{ trans('privateEntities.siteUrl') }}</th>
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
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header">
                        <h3 class="box-title"><i class="fa fa-language"></i> {{ trans('privateEntities.languageTitle') }}</h3>
                    </div>
                    <div class="box-body">
                        <table id="languages_list" class="table table-striped dataTable no-footer table-responsive">
                            <thead>
                            <tr>
                                <th width="50%">{{ trans('privateEntities.languages') }}</th>
                                <th width="40%">{{ trans('privateEntities.makeDefault') }}</th>
                                <th width="10%">
                                    {!! ONE::actionButtons($entity->entity_key, ['add' => 'EntitiesController@addLanguage']) !!}
                                </th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header">
                        <h3 class="box-title"><i class="fa fa-users"></i> {{ trans('privateEntities.managers') }}</h3>
                    </div>
                    <div class="box-body">
                        <table id="managers_list" class="table table-striped dataTable no-footer table-responsive">
                            <thead>
                            <tr>
                                <th width="90%">{{ trans('privateEntities.managers') }}</th>
                                <th>
                                    {!! ONE::actionButtons($entity->entity_key, ['create' => 'EntitiesController@createManager']) !!}
                                    {!! ONE::actionButtons($entity->entity_key, ['add' => 'EntitiesController@addManager']) !!}
                                </th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header">
                        <h3 class="box-title"><i class="fa fa-unlock"></i> {{ trans('privateEntities.authMethodTitle') }}</h3>
                    </div>
                    <div class="box-body">
                        <table id="authMethods_list" class="table table-striped dataTable no-footer table-responsive">
                            <thead>
                            <tr>
                                <th width="50%">{{ trans('privateEntities.authMethodName') }}</th>
                                <th width="40%">{{ trans('privateEntities.authMethodDescription') }}</th>
                                <th>
                                    {!! ONE::actionButtons($entity->entity_key, ['add' => 'EntitiesController@addAuthMethod']) !!}
                                </th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        {{--
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header">
                        <h3 class="box-title"><i class="fa fa-cube"></i> {{ trans('privateEntities.entityModuleTitle') }}</h3>
                    </div>
                    <div class="box-body">
                        <table id="entityModules_list" class="table table-striped dataTable no-footer table-responsive">
                            <thead>
                            <tr>
                                <th>{{ trans('privateEntities.ModuleName') }}</th>
                                <th width="10%">
                                    {!! ONE::actionButtons($entity->entity_key, ['add' => 'EntitiesController@addEntityModule']) !!}
                                </th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        --}}
    @endif

@endsection

@if(ONE::actionType('entities') == "show")
@section('scripts')
    <script>
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
        $(function () {
            $('#languages_list').DataTable({
                language: {
                    url: '{!! asset('/datatableLang/'.Session::get('LANG_CODE').'.json') !!}',
                    search: '<a class="btn searchBtn" id="searchBtn"><i class="fa fa-search"></i></a>'
                },
                processing: true,
                serverSide: true,
                ajax: '{!! action("EntitiesController@tableLanguagesEntity", $entity->entity_key) !!}',
                columns: [
                    { data: 'name', name: 'name' },
                    { data: 'activateAction', name: 'action_activate', searchable: false, orderable: false, width: "5px" },
                    { data: 'action', name: 'action', searchable: false, orderable: false },
                ],
                order: [['1', 'asc']]
            });
        });
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

        $(function () {
            $('#layouts_list').DataTable({
                language: {
                    url: '{!! asset('/datatableLang/'.Session::get('LANG_CODE').'.json') !!}',
                    search: '<a class="btn searchBtn" id="searchBtn"><i class="fa fa-search"></i></a>'
                },
                processing: true,
                serverSide: true,
                ajax: '{!! action("EntitiesController@tableLayoutsEntity", $entity->entity_key) !!}',
                columns: [
                    { data: 'name', name: 'name' },
                    { data: 'action', name: 'action', searchable: false, orderable: false },
                ],
                order: [['1', 'asc']]
            });
        });

        $(function () {
            $('#authMethods_list').DataTable({
                language: {
                    url: '{!! asset('/datatableLang/'.Session::get('LANG_CODE').'.json') !!}',
                    search: '<a class="btn searchBtn" id="searchBtn"><i class="fa fa-search"></i></a>'
                },
                processing: true,
                serverSide: true,
                ajax: '{!! action("EntitiesController@tableAuthMethod", $entity->entity_key) !!}',
                columns: [
                    { data: 'name', name: 'name' },
                    { data: 'description', name: 'description' },
                    { data: 'action', name: 'action', searchable: false, orderable: false },
                ],
                order: [['1', 'asc']]
            });
        });
        /*
         $(function () {
         $('#entityModules_list').DataTable({
         processing: true,
         serverSide: true,
         ajax: '{!! action("EntitiesController@tableEntityModule", $entity->entity_key) !!}',
         columns: [
         { data: 'name', name: 'name' }
         ]
         });
         });*/
    </script>
@endsection
@endif

