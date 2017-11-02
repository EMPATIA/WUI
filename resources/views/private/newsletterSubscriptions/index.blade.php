@extends('private._private.index')

@section('content')
    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title"><i class="fa"></i> {{ trans('privateNewsletterSubscriptions.sent_emails') }}</h3>
        </div>
        <div class="box-body">
            <table id="subscriptions_list" class="table table-striped dataTable no-footer table-responsive">
                <thead>
                <tr>
                    <th>{{ trans('privateNewsletterSubscriptions.email') }}</th>
                    <th>{{ trans('privateNewsletterSubscriptions.created_at') }}</th>
                    <th>{{ trans('privateNewsletterSubscriptions.is_active') }}</th>
                </tr>
                </thead>
            </table>
            <a href="{{ action("NewsletterSubscriptionsController@exportAsCsv") }}" class="btn btn-flat btn-success btn-sm">
                <i class="fa fa-file-excel-o" aria-hidden="true"></i>
                {{ trans('privateUsers.download') }}
            </a>
        </div>
    </div>

@endsection


@section('scripts')

    <script>
        $(function () {
            $('#subscriptions_list').DataTable({
                language: {
                    url: '{!! asset('/datatableLang/'.Session::get('LANG_CODE').'.json') !!}',
                    search: '<a class="btn searchBtn" id="searchBtn"><i class="fa fa-search"></i></a>'
                },
                processing: true,
                serverSide: true,
                ajax: '{!! action('NewsletterSubscriptionsController@getIndexTable') !!}',
                columns: [
                    { data: 'email', name: 'email', searchable: true },
                    { data: 'created_at', name: 'created_at', searchable: false },
                    { data: 'active', name: 'active', searchable: true, width: "30px" },
                    { data: 'action', name: 'action', searchable: false, orderable: false, width: "30px" },
                ],
                order: [[ 1, 'asc' ]]
            });
        });

    </script>
@endsection
