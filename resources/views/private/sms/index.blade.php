@extends('private._private.index')

@section('content')
    <div class="box box-primary">

        <div class="box-header">
            <h3 class="box-title"><i class="fa"></i> {{ trans('privateSms.sent_sms') }}</h3>
            <div class="sendSMS-btn">
                @if(ONE::verifyUserPermissions('wui', 'sms', 'create'))
                    {{-- {!! ONE::actionButtons(null, ['send' => 'SmsController@create']) !!} --}}
                    <a href="{{ action("SmsController@create") }}}" class="btn btn-flat empatia" data-toggle="tooltip" data-delay="{'show':'1000'}" title="" data-original-title="form.send">
                        <i class="fa fa-send"></i> {{ trans('privateSms.sent') }}
                    </a>
                @endif
            </div>
        </div>

        <div class="box-body">
            <table id="sent_sms_list" class="table table-striped dataTable no-footer table-responsive">
                <thead>
                <tr>
                    <th>{{ trans('privateSms.recipient') }}</th>
                    <th>{{ trans('privateSms.created_by') }}</th>
                    <th>{{ trans('privateSms.created_at') }}</th>
                    <th>{{ trans('privateSms.sent') }}</th>
                    <th></th>
                </tr>
                </thead>
            </table>
        </div>
    </div>

@endsection


@section('scripts')

    <script>
        $(function() {
            getSidebar('{{ action("OneController@getSidebar") }}', 'sms', null, 'sms' );
        });

        $(function () {

            $('#sent_sms_list').DataTable({
                language: {
                    url: '{!! asset('/datatableLang/'.Session::get('LANG_CODE').'.json') !!}',
                    search: '<a class="btn searchBtn" id="searchBtn"><i class="fa fa-search"></i></a>'
                },
                processing: true,
                serverSide: true,
                ajax: '{!! action('SmsController@tableSms') !!}',
                columns: [
                    { data: 'recipient', name: 'recipient', searchable: true },
                    { data: 'created_by', name: 'created_by', searchable: false },
                    { data: 'created_at', name: 'created_at', searchable: false },
                    { data: 'sent', name: 'sent', searchable: false },
                    { data: 'action', name: 'action', searchable: false, orderable: false, width: "30px" },
                ],
                order: [[ 1, 'asc' ]]
            });


        });

    </script>
@endsection
