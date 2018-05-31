@extends('private._private.index')

@section('content')

    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title"><i class="fa"></i> {{ trans('privateEntities.add_lang') }}</h3>
        </div>

        <div class="box-body">
            {!! ONE::messages() !!}
            <table id="lang_list" class="table table-striped dataTable no-footer table-responsive">
                <thead>
                <tr>
                    <th>{{ trans('privateEntities.name') }}</th>
                    <th>{{ trans('privateEntities.email') }}</th>
                    <th>{{ trans('privateEntities.add') }}</th>
                </tr>
                </thead>
            </table>
        </div>

        <div class="box-footer">
            <a class="btn btn-flat btn-primary" href=" {!!  action('EntitiesController@showLanguages',$entityKey) !!}"><i class="fa fa-arrow-left"></i> {!! trans('privateEntities.back') !!}</a>
        </div>        
    </div>


@endsection


@section('scripts')
    <script>
        $(function() {
            getSidebar('{{ action("OneController@getSidebar") }}', 'languages', "{{isset($entity) ? $entity->entity_key : null}}", 'sidebar_admin.entities' )
        })
        
        $(function () {
            $('#lang_list').DataTable({
                language: {
                    url: '{!! asset('/datatableLang/'.Session::get('LANG_CODE').'.json') !!}',
                    search: '<a class="btn searchBtn" id="searchBtn"><i class="fa fa-search"></i></a>'
                },
                processing: true,
                serverSide: true,
                bDestroy: true,
                ajax: {
                    "url": '{!! action("EntitiesController@tableAddLanguageEntity", $entityKey) !!}',
                    "data": function (d) {
                        d.entityKey = '{{ $entityKey }}';
                    }
                },
                columns: [
                    { data: 'name', name: 'name' },
                    { data: 'code', name: 'code' },               
                    { data: 'action', name: 'action', searchable: false, orderable: false, width: "30px" },
                ],
                order: [['1', 'asc']]
            });
        });

    </script>
@endsection