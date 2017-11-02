@extends('private._private.index')

@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title"><i class="fa"></i> {{ trans('title.mails') }}</h3>
        </div>

        <div class="box-body">
            <table id="mails_list" class="table table-hover table-striped dataTable no-footer table-responsive">
                <thead>
                <tr>
                    <th>{{ trans('form.subject') }}</th>
                    <th>{{ trans('form.tag') }}</th>
                    <th>{!! ONE::actionButtons(null, ['create' => 'MailsController@create']) !!}</th>
                </tr>
                </thead>
            </table>
        </div>
    </div>

@endsection


@section('scripts')
    <script>

        $(function () {
            $('#mails_list').DataTable({
                language: {
                    url: '{!! asset('/datatableLang/'.Session::get('LANG_CODE').'.json') !!}',
                    search: '<a class="btn searchBtn" id="searchBtn"><i class="fa fa-search"></i></a>'
                },
                processing: true,
                serverSide: true,
                ajax: '{!! action('MailsController@tableMails') !!}',
                columns: [
                    { data: 'subject', name: 'subject' },
                    { data: 'tag', name: 'tag' },
                    { data: 'action', name: 'action', searchable: false, orderable: false, width: "30px" },
                ],
                order: [['1', 'asc']]
            });

        });
        
    </script>
@endsection



