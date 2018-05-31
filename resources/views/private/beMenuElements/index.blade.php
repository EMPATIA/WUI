@extends('private._private.index')

@section('content')
    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title"><i class="fa"></i> {{ trans('private.CMSectionTypes') }}</h3>
        </div>

        <div class="box-body">
            <table id="sectionTypes_list" class="table table-hover table-striped table-responsive">
                <thead>
                    <tr>
                        <th>{{ trans('privateBEMenuElements.id') }}</th>
                        <th>{{ trans('privateBEMenuElements.controller') }}</th>
                        <th>{{ trans('privateBEMenuElements.method') }}</th>
                        <th>{{ trans('privateBEMenuElements.code') }}</th>
                        <th>{{ trans('privateBEMenuElements.created_at') }}</th>
                        <th>
                            {!! ONE::actionButtons(null, ['create' => 'BEMenuElementsController@create']) !!}
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
                ajax: '{!! action('BEMenuElementsController@getIndexTable') !!}',
                columns: [
                    { data: 'id', name: 'id', width: "25px"},
                    { data: 'controller', name: 'controller' },
                    { data: 'method', name: 'method' },
                    { data: 'code', name: 'code' },
                    { data: 'created_at', name: 'created_at' },
                    { data: 'action', name: 'action', searchable: false, orderable: false, width: "50px" },
                ]
            });

        });

    </script>
@endsection
