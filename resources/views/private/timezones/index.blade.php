@extends('private._private.index')

@section('content')
    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title"><i class="fa"></i> {{ trans('privateTimezones.timezones') }}</h3>
        </div>

        <div class="box-body">
            <table id="timezones_list" class="table table-hover table-striped dataTable no-footer table-responsive">
                <thead>
                <tr>
                    <th>{{ trans('privateTimezones.id') }}</th>
                    <th>{{ trans('privateTimezones.country_code') }}</th>
                    <th>{{ trans('privateTimezones.continent') }}</th>
                    <th>{{ trans('privateTimezones.name') }}</th>
                    <th>{!! ONE::actionButtons(null, ['create' => 'TimezonesController@create']) !!}</th>
                </tr>
                </thead>
            </table>
        </div>
    </div>

@endsection


@section('scripts')
    <script>

        $(function () {
            $('#timezones_list').DataTable({
                language: {
                    url: '{!! asset('/datatableLang/'.Session::get('LANG_CODE').'.json') !!}',
                    search: '<a class="btn searchBtn" id="searchBtn"><i class="fa fa-search"></i></a>'
                },
                processing: true,
                serverSide: true,
                ajax: '{!! action('TimezonesController@tableTimezones') !!}',
                columns: [
                    { data: 'id', name: 'id', width: "20px" },
                    { data: 'country_code', name: 'country_code' },
                    { data: 'continent', name: 'continent'},
                    { data: 'name', name: 'name' },
                    { data: 'action', name: 'action', searchable: false, orderable: false, width: "50px" },
                ],
                order: [['1', 'asc']]
            });

        });
        
    </script>
@endsection



