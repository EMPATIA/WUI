@extends('private._private.index')

@section('content')
    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title"><i class="fa"></i> {{ trans('privateSiteSiteConfig.title') }}</h3>
        </div>

        <div class="box-body">
            <table id="voteMethods_list" class="table table-hover table-striped dataTable no-footer table-responsive">
                <thead>
                <tr>
                    <th>{{ trans('privateSiteSiteConfig.value') }}</th>
                    <th>{{ trans('privateSiteSiteConfig.siteConf') }}</th>
                    <th>{{ trans('privateSiteSiteConfig.siteConfGroup') }}</th>
                    <th>{!! ONE::actionButtons(null, ['create' => 'SiteSiteConfigController@create']) !!}</th>
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
                ajax: '{!! action('SiteSiteConfigController@getIndexTable') !!}',
                columns: [
                    { data: 'value', name: 'value', width: "20px" },
                    { data: 'name', name: 'name' },
                    { data: 'group', name: 'group' },
                    { data: 'action', name: 'action', searchable: false, orderable: false, width: "30px" },
                ],
                order: [['1', 'asc']]
            });

        });

    </script>
@endsection



