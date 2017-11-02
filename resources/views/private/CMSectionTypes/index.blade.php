@extends('private._private.index')

@section('content')
    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title"><i class="fa"></i> {{ trans('private.CMSectionTypes') }}</h3>
        </div>

        <div class="box-body">
            <table id="sectionTypes_list" class="table table-hover table-striped dataTable no-footer table-responsive">
                <thead>
                <tr>
                    <th>{{ trans('privateCMSectionTypes.code') }}</th>
                    <th>{{ trans('privateCMSectionTypes.title') }}</th>
                    <th>
                        {!! ONE::actionButtons(null, ['create' => 'CMSectionTypesController@create']) !!}
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
            $('#sectionTypes_list').DataTable({
                language: {
                    url: '{!! asset('/datatableLang/'.Session::get('LANG_CODE').'.json') !!}',
                    search: '<a class="btn searchBtn" id="searchBtn"><i class="fa fa-search"></i></a>'
                },
                processing: true,
                serverSide: true,
                ajax: '{!! action('CMSectionTypesController@getIndexTable') !!}',
                columns: [
                    { data: 'code', name: 'code' },
                    { data: 'value', name: 'title' },
                    { data: 'action', name: 'action', searchable: false, orderable: false, width: "50px" },
                ],
                order: [['1', 'asc']]
            });

        });

    </script>
@endsection
