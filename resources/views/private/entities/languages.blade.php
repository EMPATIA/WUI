@extends('private._private.index')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa fa-language"></i> {{ trans('privateEntities.language_title') }}</h3>
                </div>
                <div class="box-body">
                    <table id="languages_list" class="table table-striped dataTable no-footer">
                        <thead>
                        <tr>
                            <th width="50%">{{ trans('privateEntities.languages') }}</th>
                            <th width="40%">{{ trans('privateEntities.make_default') }}</th>
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
@endsection

@section('scripts')
    <script>
        $(function () {
            $('#languages_list').DataTable({
                language: {
                    url: '{!! asset('/datatableLang/'.Session::get('LANG_CODE').'.json') !!}'
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
        

        function modal(id, languageId){
            console.log(id, languageId);
            var url = '{{action('EntitiesController@makeLangDefault',[":id",":languageId"])}}';
            url = url.replace(':id', id);
            url = url.replace(':languageId', languageId);
            //TODO: Check if sucess or error!!!
            $.ajax({
                url: url,
                type: 'POST',
                data: {_method: 'get', _token: '{{csrf_token()}}'},
                success: function (action) {
                    window.location = action;
                },
                error: function (data) {
                    //TODO Deal with the error!
                }
            });
            $(document.getElementById('activate-modal')).modal("hide");
        }
        
    </script>
@endsection