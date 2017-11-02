@extends('private._private.index')

@section('content')
    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title"><i class="fa"></i> {{ trans('privateSiteConfGroup.title') }}</h3>
        </div>

        <div class="box-body">
            <table id="voteMethods_list" class="table table-hover table-striped dataTable no-footer table-responsive">
                <thead>
                <tr>
                    <th>{{ trans('privateSiteConfGroup.code') }}</th>
                    <th>{{ trans('privateSiteConfGroup.name') }}</th>
                    <th>{!! ONE::actionButtons(null, ['create' => 'SiteConfGroupController@create']) !!}</th>
                </tr>
                </thead>
            </table>
        </div>
    </div>

@endsection


@section('scripts')
    <script>

        $(function () {
            $('#voteMethods_list').DataTable({
                language: {
                    url: '{!! asset('/datatableLang/'.Session::get('LANG_CODE').'.json') !!}',
                    search: '<a class="btn searchBtn" id="searchBtn"><i class="fa fa-search"></i></a>'
                },
                processing: true,
                serverSide: true,
                ajax: '{!! action('SiteConfGroupController@getIndexTable') !!}',
                columns: [
                    { data: 'code', name: 'code', width: '50px' },
                    { data: 'name', name: 'name' },
                    { data: 'action', name: 'action', searchable: false, orderable: false, width: "50px" },
                ],
                order: [['1', 'asc']]
            });

        });

    </script>
@endsection



