@extends('private._private.index')

@section('content')
    <div class="box box-primary">

        <div class="box-header">
            <h3 class="box-title"><i class="fa"></i> {{ trans('privateShortLinks.short_links') }}</h3>
            <div class="sendSMS-btn">
                @if(ONE::verifyUserPermissions('wui', 'shortLinks', 'create'))
                    <a href="{{ action("ShortLinksController@create") }}" class="btn btn-flat empatia" data-toggle="tooltip" data-delay="{'show':'1000'}" title="" data-original-title="form.send">
                        {{ trans('privateShortLinks.create') }}
                    </a>
                @endif
            </div>
        </div>

        <div class="box-body">
            <table id="shortlinks_list" class="table table-striped dataTable no-footer table-responsive">
                <thead>
                <tr>
                    <th>{{ trans('privateShortLinks.name') }}</th>
                    <th>{{ trans('privateShortLinks.code') }}</th>
                    <th>{{ trans('privateShortLinks.destiny') }}</th>
                    <th>{{ trans('privateShortLinks.hits') }}</th>
                    <th></th>
                </tr>
                </thead>
            </table>
        </div>
    </div>
@endsection


@section('scripts')

    <script>
        $(function () {

            $('#shortlinks_list').DataTable({
                language: {
                    url: '{!! asset('/datatableLang/'.Session::get('LANG_CODE').'.json') !!}'
                },
                processing: true,
                serverSide: true,
                ajax: '{!! action('ShortLinksController@getIndexTable') !!}',
                columns: [
                    { data: 'name', name: 'name', searchable: true },
                    { data: 'code', name: 'code', searchable: false },
                    { data: 'url', name: 'url', searchable: true },
                    { data: 'hits', name: 'hits', searchable: false },
                    { data: 'action', name: 'action', searchable: false, orderable: false, width: "30px" },
                ],
                order: [[ 1, 'asc' ]]
            });


        });

    </script>
@endsection
