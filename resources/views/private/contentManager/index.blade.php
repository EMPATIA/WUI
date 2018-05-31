@extends('private._private.index')

@section('content')
    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title"><i class="fa"></i> {{ trans('private.CMSectionTypes') }}</h3>
        </div>

        <div class="box-body">
            <table id="sectionTypes_list" class="table table-hover table-striped dataTable no-footer table-responsive">
                <thead>
                <tr>
                    <th>{{ trans('privateContentManager.code') }}</th>
                    <th>{{ trans('privateContentManager.name') }}</th>
                    <th>
                        @if(Session::get('user_role') == 'admin')
                            {!! ONE::actionButtons(["contentType"=>$contentType,"siteKey"=>$siteKey, "topicKey" => $topicKey], ['create' => 'ContentManagerController@create']) !!}
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
            @if (!empty($siteKey))
                getSidebar('{{ action("OneController@getSidebar") }}', 'cm.{{ $contentType }}', "{{($siteKey)}}", 'site' )
            @endif
            $('#sectionTypes_list').DataTable({
                language: {
                    url: '{!! asset('/datatableLang/'.Session::get('LANG_CODE').'.json') !!}',
                    search: '<a class="btn searchBtn" id="searchBtn"><i class="fa fa-search"></i></a>'
                },
                processing: true,
                serverSide: true,
                ajax: '{!! action('ContentManagerController@getIndexTable',["contentType"=>$contentType,"siteKey"=>$siteKey, "topicKey" => $topicKey]) !!}',
                columns: [
                    { data: 'code', name: 'code' },
                    { data: 'name', name: 'name' },
                    { data: 'action', name: 'action', searchable: false, orderable: false, width: "50px" },
                ],
                order: [['1', 'asc']]
            });

        });

    </script>
@endsection

