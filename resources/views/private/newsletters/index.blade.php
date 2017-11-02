@extends('private._private.index')

@section('header_styles')
    <style>
        .btn-sent{
            pointer-events: none;
            cursor: default;
        }
    </style>
@endsection

@section('content')
    <div class="box box-primary">

        <div class="box-header">
            <h3 class="box-title"><i class="fa"></i> {{ trans('privateNewsletters.newsletters') }}</h3>
        </div>

        <div class="box-body">
            <table id="newsletters_table" class="table table-striped dataTable no-footer table-responsive">
                <thead>
                <tr>
                    <th>{{ trans('privateNewsletters.subject') }}</th>
                    <th>{{ trans('privateNewsletters.created_by') }}</th>
                    <th>{{ trans('privateNewsletters.created_at') }}</th>
                    <th>{{ trans('privateNewsletters.tested') }}</th>
                    <th>
                        @if(ONE::verifyUserPermissions('notify', 'message_all_users', 'create'))
                            {!! ONE::actionButtons(['f'=>'newsletters'], ['create' => 'PrivateNewslettersController@create']) !!}
                        @endif
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

            $('#newsletters_table').DataTable({
                language: {
                    url: '{!! asset('/datatableLang/'.Session::get('LANG_CODE').'.json') !!}',
                    search: '<a class="btn searchBtn" id="searchBtn"><i class="fa fa-search"></i></a>'
                },
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: '{!! action('PrivateNewslettersController@getIndexTable') !!}',
                columns: [
                    { data: 'subject', name: 'subject', searchable: true },
                    { data: 'created_by', name: 'created_by', searchable: true },
                    { data: 'created_at', name: 'created_at', searchable: true },
                    { data: 'tested', name: 'tested', searchable: false },
                    { data: 'action', name: 'action', searchable: false, orderable: false, width: "30px" },
                ],
                order: [[ 2, 'asc' ]]
            });
        });
    </script>
@endsection
