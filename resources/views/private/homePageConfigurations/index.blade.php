@extends('private._private.index')

@section('content')
    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title"><i class="fa"></i> {{ trans('homePageConfiguration.title') }}</h3>
        </div>
        <div class="box-body">
            <table id="homePageConfigurations_list" class="table table-hover table-striped dataTable no-footer table-responsive">
                <thead>
                <tr>
                    <th>{{ trans('homePageConfiguration.home_page_configuration_key') }}</th>
                    <th>{{ trans('homePageConfiguration.value') }}</th>
                    <th>{!! ONE::actionButtons(null, ['create' => 'HomePageConfigurationsController@create']) !!}</th>
                </tr>
                </thead>
            </table>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(function () {
            $('#homePageConfigurations_list').DataTable({
                language: {
                    url: '{!! asset('/datatableLang/'.Session::get('LANG_CODE').'.json') !!}',
                    search: '<a class="btn searchBtn" id="searchBtn"><i class="fa fa-search"></i></a>'
                },
                processing: true,
                serverSide: true,
                ajax: '{!! action('HomePageConfigurationsController@getIndexTable') !!}',
                columns: [
                    { data: 'home_page_configuration_key', name: 'home_page_configuration_key', width: "20px" },
                    { data: 'value', name: 'value' },
                    { data: 'action', name: 'action', searchable: false, orderable: false, width: "0px" },
                ],
                order: [['1', 'asc']]
            });
        });
    </script>
@endsection