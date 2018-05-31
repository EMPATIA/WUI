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
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-4 col-md-12 col-sm-12 col-12">
                        <label for="sent_messages">
                            {{ trans("privateEntityMessages.sent_messages") }}
                        </label>
                        <div class="onoffswitch">
                            <input type="checkbox" name="sent_messages" class="onoffswitch-checkbox" id="sent_messages" value="1" checked onchange="reloadDataTable()">
                            <label class="onoffswitch-label" for="sent_messages">
                                <span class="onoffswitch-inner"></span>
                                <span class="onoffswitch-switch"></span>
                            </label>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-12 col-sm-12 col-12">
                        <label for="received_messages">
                            {{ trans("privateEntityMessages.received_messages") }}
                        </label>
                        <div class="onoffswitch">
                            <input type="checkbox" name="received_messages" class="onoffswitch-checkbox" id="received_messages" value="1" checked onchange="reloadDataTable()">
                            <label class="onoffswitch-label" for="received_messages">
                                <span class="onoffswitch-inner"></span>
                                <span class="onoffswitch-switch"></span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="box-body">
            <table id="messages_table" class="table table-hover table-striped dataTable no-footer table-responsive">
                <thead>
                <tr>
                    <th>{{ trans('privateEntityMessages.from') }}</th>
                    <th>{{ trans('privateEntityMessages.to') }}</th>
                    <th>{{ trans('privateEntityMessages.content') }}</th>
                    <th>{{ trans('privateEntityMessages.created_at') }}</th>
                    <th></th>
                </tr>
                </thead>
            </table>
        </div>
    </div>

@endsection


@section('scripts')

    <script>
        function reloadDataTable() {
            $('#messages_table').DataTable({
                language: {
                    url: '{!! asset('/datatableLang/'.Session::get('LANG_CODE').'.json') !!}',
                    search: '<a class="btn searchBtn" id="searchBtn"><i class="fa fa-search"></i></a>'
                },
                processing: true,
                serverSide: true,
                bDestroy: true,
                ajax: {
                    url: '{!! action('EntityMessagesController@getIndexTable') !!}',
                    type:"get",
                    data:function(d) {
                        d.sent_messages = $("#sent_messages").is(":checked") ? 1 : 0,
                        d.received_messages = $("#received_messages").is(":checked") ? 1 : 0
                    }
                },
                columns: [
                    { data: 'from', name: 'from', searchable: false},
                    { data: 'to', name: 'to', searchable: false},
                    { data: 'value', name: 'value'},
                    { data: 'created_at', name: 'created_at', searchable: false},
                    { data: 'action', name: 'action', searchable: false, orderable: false, width: "30px" },
                ],
                order: [[ 2, 'asc' ]]
            });
        }

        $(document).ready(function() {
            reloadDataTable();
        });

    </script>
@endsection
