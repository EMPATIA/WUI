@extends('private._private.index')

@section('content')
    <div class="box-private">
        <div class="box-header">
            <h3 class="box-title"><i class="fa"></i> {{ trans('title.geoareas') }}</h3>
        </div>

        <div class="box-body">
            <table id="geoareas_list" class="table table-striped dataTable no-footer table-responsive" style="border-collapse:separate;border-collapse:separate; border-spacing:4px 0px;" >
                <thead>
                <tr>
                    <th>{{ trans('form.id') }}</th>
                    <th>{{ trans('form.name') }}</th>
                    <th>{{ trans('form.entity_id') }}</th>
                    <th>{!! ONE::actionButtons(null, ['create' => 'GeoAreasController@create']) !!}</th>
                </tr>
                </thead>
            </table>
        </div>
    </div>

@endsection


@section('scripts')
    <script>

        $(function () {
            $('#geoareas_list').DataTable({
                language: {
                    url: '{!! asset('/datatableLang/'.Session::get('LANG_CODE').'.json') !!}',
                    search: '<a class="btn searchBtn" id="searchBtn"><i class="fa fa-search"></i></a>'
                },
                processing: true,
                serverSide: true,
                ajax: '{!! action('GeoAreasController@tableGeoAreas') !!}',
                columns: [
                    { data: 'id', name: 'id', width: "20px" },
                    { data: 'name', name: 'name' },
                    { data: 'entity_id', name: 'entity_id' },
                    { data: 'action', name: 'action', searchable: false, orderable: false, width: "30px" },
                ],
                order: [['1', 'asc']]
            });

        });
        
    </script>
@endsection



