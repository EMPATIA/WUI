@extends('private._private.index')

@section('content')
    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title"><i class="fa"></i> {{ trans('privateEntitiesDivided.emailsTemplate') }}</h3>
        </div>
        <div class="box-body">

            <table id="siteEmails_list" class="table table-hover table-striped dataTable no-footer table-responsive">
                <thead>
                <tr>
                    <th width="90%">{{ trans('privateEmailTemplates.templateName') }}</th>
                    <th width="10%">
                        @if(ONE::verifyUserPermissions('orchestrator', 'site_email_template', 'create'))
                            {!! ONE::actionButtons(isset($siteKey) ? $siteKey  : null, ['create' => 'EmailTemplatesController@createEmailsFromTemplates']) !!}
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

            $('#siteEmails_list').DataTable({
                language: {
                    url: '{!! asset('/datatableLang/'.Session::get('LANG_CODE').'.json') !!}',
                    search: '<a class="btn searchBtn" id="searchBtn"><i class="fa fa-search"></i></a>'
                },
                processing: true,
                serverSide: true,
                ajax: '{!! action("EntitiesSitesController@tableSiteEmailsManagers", ['siteKey' => (isset($siteKey) ? $siteKey : null)]) !!}',
                columns: [
                    { data: 'templateSubject', name: 'templateSubject' },
                    { data: 'action', name: 'action', searchable: false, orderable: false },
                ],
                order: [['0', 'asc']]
            });
        });
    </script>
@endsection