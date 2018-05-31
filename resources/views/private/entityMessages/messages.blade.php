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
            <h3 class="box-title"><i class="fa"></i> {{ trans('privateEntityMessages.messages') }}</h3>
        </div>

        <div class="box-body">
            <table id="messages_table" class="table table-hover table-striped dataTable no-footer table-responsive">
                <thead>
                <tr>
                    <th>
                        @if($active=="sent_messages")
                            {{ trans('privateEntityMessages.to') }}
                        @else
                            {{ trans('privateEntityMessages.from') }}
                        @endif
                    </th>
                    <th>{{ trans('privateEntityMessages.value') }}</th>
                    <th>{{ trans('privateEntityMessages.created_at') }}</th>
                </tr>
                </thead>
            </table>
        </div>
    </div>

@endsection


@section('scripts')

    <script>
        $(function () {

            $('#messages_table').DataTable({
                language: {
                    url: '{!! asset('/datatableLang/'.Session::get('LANG_CODE').'.json') !!}',
                    search: '<a class="btn searchBtn" id="searchBtn"><i class="fa fa-search"></i></a>'
                },
                processing: true,
                serverSide: true,
                ajax: '{!! action('EntityMessagesController@getMessagesTable', ['flag' => $flag]) !!}',
                columns: [
                    { data: 'to', name: 'to', searchable: false },
                    { data: 'value', name: 'value', searchable: true },
                    { data: 'created_at', name: 'created_at', searchable: false},
                    { data: 'action', name: 'action', searchable: false, orderable: false, width: "30px" },
                ],
                order: [[ 2, 'desc' ]]
            });
        });

    </script>
@endsection
