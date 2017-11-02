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
            <h3 class="box-title"><i class="fa"></i> {{ trans('privateEmails.sent_emails') }}</h3>
        </div>
        <div class="box-body">
            <table id="sent_emails_list" class="table table-striped dataTable no-footer table-responsive">
                <thead>
                <tr>
                    <th>{{ trans('privateEmails.recipient') }}</th>
                    <th>{{ trans('privateEmails.subject') }}</th>
                    <th>{{ trans('privateEmails.sender_email') }}</th>
                    <th>{{ trans('privateEmails.created_at') }}</th>
                    <th>{{ trans('privateEmails.sent_at') }}</th>
                    <th>@if(ONE::verifyUserPermissions('wui', 'email', 'create')) {!! ONE::actionButtons(null, ['send' => 'EmailsController@create']) !!} @endif</th>
                </tr>
                </thead>
            </table>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(function () {
            $('#sent_emails_list').DataTable({
                language: {
                    url: '{!! asset('/datatableLang/'.Session::get('LANG_CODE').'.json') !!}',
                    search: '<a class="btn searchBtn" id="searchBtn"><i class="fa fa-search"></i></a>'
                },
                processing: true,
                serverSide: true,
                ajax: '{!! action('EmailsController@tableEmails') !!}',
                columns: [
                    { data: 'recipient', name: 'recipient', searchable: true },
                    { data: 'subject', name: 'subject', searchable: false, orderable: false },
                    { data: 'sender_email', name: 'sender_email', searchable: true },
                    { data: 'created_at', name: 'created_at', searchable: false, orderable: false, },
                    { data: 'sent', name: 'sent', searchable: false },
                    { data: 'action', name: 'action', searchable: false, orderable: false, width: "30px" },
                ],
                order: [[ 3, 'asc' ]]
            });
        });
    </script>
@endsection
