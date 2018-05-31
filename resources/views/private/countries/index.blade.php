@extends('private._private.index')

@section('content')
    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title"><i class="fa"></i> {{ trans('privateCountries.list') }}</h3>
        </div>

        <div class="box-body">
            <table id="countries_list" class="table table-hover table-striped dataTable no-footer table-responsive">
                <thead>
                <tr>
                    <th>{{ trans('privateCountries.id') }}</th>
                    <th>{{ trans('privateCountries.name') }}</th>
                    <th>{{ trans('privateCountry.code') }}</th>
                    <th>{!! ONE::actionButtons(null, ['create' => 'CountriesController@create']) !!}</th>
                </tr>
                </thead>
            </table>
        </div>
    </div>

@endsection


@section('scripts')
    <script>

        $(function () {
            $('#countries_list').DataTable({
                language: {
                    url: '{!! asset('/datatableLang/'.Session::get('LANG_CODE').'.json') !!}'
                },
                processing: true,
                serverSide: true,
                ajax: '{!! action('CountriesController@tableCountries') !!}',
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



