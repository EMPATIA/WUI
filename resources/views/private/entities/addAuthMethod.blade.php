@extends('private._private.index')

@section('content')
    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title"><i class="fa"></i> {{ trans('privateEntities.add_auth_method') }}</h3>
        </div>

        <div class="box-body">
            {!! ONE::messages() !!}
            <table id="addAuthMethod_list" class="table table-striped dataTable no-footer table-responsive">
                <thead>
                <tr>
                    <th width="50%">{{ trans('privateEntities.auth_method_name') }}</th>
                    <th width="40%">{{ trans('privateEntities.auth_method_description') }}</th>
                    <th>{{ trans('privateEntities.add') }}</th>
                </tr>
                </thead>
            </table>
        </div>

        <div class="box-footer">
            <a class="btn btn-flat btn-primary" href=" {!!  action('EntitiesController@showAuthMethods',$entityKey) !!}"><i class="fa fa-arrow-left"></i> {!! trans('privateEntities.back') !!}</a>
        </div>        
    </div>
@endsection


@section('scripts')
<script>
    $(function () {
        $('#addAuthMethod_list').DataTable({
            language: {
                url: '{!! asset('/datatableLang/'.Session::get('LANG_CODE').'.json') !!}'
            },
            processing: true,
            serverSide: true,
            ajax: '{!! action("EntitiesController@tableAddAuthMethod", $entityKey) !!}',

            columns: [
                { data: 'name', name: 'name' },
                { data: 'description', name: 'description' },
                { data: 'action', name: 'action', searchable: false, orderable: false, width: "30px" },
            ],
            order: [['1', 'asc']]
        });
    });
</script>
@endsection