@extends('private._private.index')

@section('content')
    <div class="box box-primary">

        <div class="box-header">
            <h3 class="box-title"><i class="fa"></i> {{ trans('privateGroupTypes.group_types') }}</h3>
        </div>

        <div class="box-body">
            <table id="group_types_list" class="table table-striped dataTable no-footer table-responsive">
                <thead>
                <tr>
                    <th>{{ trans('privateGroupTypes.code') }}</th>
                    <th>{!! ONE::actionButtons(null, ['create' => 'GroupTypesController@create']) !!}</th>
                </tr>
                </thead>
            </table>
        </div>
    </div>

@endsection


@section('scripts')

    <script>

        $(function () {

            $('#group_types_list').DataTable({
                language: {
                    url: '{!! asset('/datatableLang/'.Session::get('LANG_CODE').'.json') !!}',
                    search: '<a class="btn searchBtn" id="searchBtn"><i class="fa fa-search"></i></a>'
                },
                processing: true,
                serverSide: true,
                ajax: '{!! action('GroupTypesController@tableGroupTypes') !!}',
                columns: [
                    { data: 'code', name: 'code' },
                    { data: 'action', name: 'action', searchable: false, orderable: false, width: "50px" },
                ],
                order: [[ 0, 'asc' ]]
            });

        });

    </script>
@endsection

