@extends('private._private.index')

@section('content')
    <div class="box box-primary">

        <div class="box-header">
            <h3 class="box-title"><i class="fa"></i> {{ trans('flagTypes.list') }}</h3>
        </div>

        <div class="box-body">
            <table id="flag_types_list" class="table table-striped dataTable no-footer table-responsive">
                <thead>
                <tr>
                    <th>{{ trans('flagTypes.title') }}</th>
                    <th> {!! ONE::actionButtons(null, ['create' => 'FlagTypesController@create']) !!}</th>
                </tr>
                </thead>
            </table>
        </div>
    </div>

@endsection


@section('scripts')

    <script>
        $(function () {

            $('#flag_types_list').DataTable({
                language: {
                    url: '{!! asset('/datatableLang/'.Session::get('LANG_CODE').'.json') !!}',
                    search: '<a class="btn searchBtn" id="searchBtn"><i class="fa fa-search"></i></a>'
                },
                processing: true,
                serverSide: true,
                ajax: '{!! action('FlagTypesController@getIndexTable') !!}',
                columns: [
                    { data: 'title', name: 'title', searchable: true },
                    { data: 'action', name: 'action', searchable: false, orderable: false, width: "30px" },
                ],
                order: [[ 0, 'asc' ]]
            });


        });

    </script>
@endsection
