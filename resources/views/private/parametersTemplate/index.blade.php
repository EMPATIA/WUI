@extends('private._private.index')

@section('content')
    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title"><i class="fa"></i> {{ trans('parametersTemplate.title') }}</h3>
        </div>

        <div class="box-body">
            <table id="news-list" class="table table-hover table-striped dataTable no-footer table-responsive">
                <thead>
                <tr>
                    <th>{{ trans('parametersTemplate.parameter') }}</th>
                    <th>@if(ONE::verifyUserPermissions('cb', 'parameter_template', 'create')){!! ONE::actionButtons(null, ['create' => 'ParametersTemplateController@create']) !!}@endif</th>
                </tr>
                </thead>
            </table>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(function () {
            $('#news-list').DataTable({
                language: {
                    url: '{!! asset('/datatableLang/'.Session::get('LANG_CODE').'.json') !!}',
                    search: '<a class="btn searchBtn" id="searchBtn"><i class="fa fa-search"></i></a>'
                },
                processing: true,
                serverSide: true,
                ajax: '{!! action('ParametersTemplateController@getIndexTable') !!}',
                columns: [
                    { data: 'parameter', name: 'parameter' },
                    { data: 'action', name: 'action', searchable: false, orderable: false, width: "30px" },
                ],
                order: [['0', 'asc']]
            });

        });
    </script>
@endsection



