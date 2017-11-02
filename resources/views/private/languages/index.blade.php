@extends('private._private.index')
@section('content')
    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title"><i class="fa"></i> {{ trans('privateLanguages.languages') }}</h3>
        </div>

        <div class="box-body">
            <table id="languages_list" class="table table-hover table-striped dataTable no-footer table-responsive">
                <thead>
                <tr>
                    <th>{{ trans('privateLanguages.id') }}</th>
                    <th>{{ trans('privateLanguages.name') }}</th>
                    <th>{{ trans('privateLanguages.code') }}</th>
                    <th>{!! ONE::actionButtons(null, ['create' => 'LanguagesController@create']) !!}</th>
                </tr>
                </thead>
            </table>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(function () {
            $('#languages_list').DataTable({
                language: {
                    url: '{!! asset('/datatableLang/'.Session::get('LANG_CODE').'.json') !!}',
                    search: '<a class="btn searchBtn" id="searchBtn"><i class="fa fa-search"></i></a>'
                },
                processing: true,
                serverSide: true,
                ajax: '{!! action('LanguagesController@tableLanguages') !!}',
                columns: [
                    { data: 'id', name: 'id', width: "20px" },
                    { data: 'name', name: 'name' },
                    { data: 'code', name: 'code' },
                    { data: 'action', name: 'action', searchable: false, orderable: false, width: "50px" },
                ],
                order: [['1', 'asc']]
            });
        });
    </script>
@endsection



