@extends('private._private.index')

@section('content')
    <div class="box box-primary">

        <div class="box-header">
            <h3 class="box-title"><i class="fa"></i> {{ trans('flags.list') }}</h3>
        </div>

        <div class="box-body">
            <table id="flags_list" class="table table-striped dataTable no-footer table-responsive">
                <thead>
                <tr>
                    <th>{{ trans('flags.title') }}</th>
                    <th>{{ trans('flags.description') }}</th>
                    <th>{{ trans('flags.private_flag') }}</th>
                    <th>{{ trans('flags.flag_visible') }}</th>
                    <th>{{ trans('flags.public_visible') }}</th>
                    <th> {!! ONE::actionButtons(['type' => $type, 'cbKey' => $cbKey], ['create' => 'FlagsController@create']) !!}</th>
                </tr>
                </thead>
            </table>
        </div>
    </div>

@endsection


@section('scripts')

    <script>
        $(function () {

            $('#flags_list').DataTable({
                language: {
                    url: '{!! asset('/datatableLang/'.Session::get('LANG_CODE').'.json') !!}',
                    search: '<a class="btn searchBtn" id="searchBtn"><i class="fa fa-search"></i></a>'
                },
                processing: true,
                serverSide: true,
                ajax: '{!! action('FlagsController@getIndexTable',[$type,$cbKey]) !!}',
                columns: [
                    { data: 'title', name: 'title', searchable: true },
                    { data: 'description', name: 'description', searchable: true },
                    { data: 'private_flag', name: 'private_flag', searchable: true },
                    { data: 'flag_visible', name: 'flag_visible', searchable: true },
                    { data: 'public_visible', name: 'public_visible', searchable: true },
                    { data: 'action', name: 'action', searchable: false, orderable: false, width: "60px" },
                ],
                order: [[ 1, 'asc' ]]
            });


        });

    </script>
@endsection
