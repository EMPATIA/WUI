@extends('private._private.index')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header">
                    <h3 class="box-title"><i class="fa fa-language"></i> {{ trans('privateEntities.siteTitle') }}</h3>
                </div>
                <div class="box-body">
                    <table id="sites_list" class="table table-striped dataTable no-footer table-responsive">
                        <thead>
                        <tr>
                            <th width="50%">{{ trans('privateEntities.siteName') }}</th>
                            <th width="40%">{{ trans('privateEntities.siteUrl') }}</th>
                            <th width="10%">
                                @if(Session::get('user_role') == 'admin')
                                    {!! ONE::actionButtons(null, ['create' => 'EntitiesSitesController@create']) !!}
                                @endif
                            </th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>

        $(function () {
            $('#sites_list').DataTable({
                language: {
                    url: '{!! asset('/datatableLang/'.Session::get('LANG_CODE').'.json') !!}',
                    search: '<a class="btn searchBtn" id="searchBtn"><i class="fa fa-search"></i></a>'
                },
                processing: true,
                serverSide: true,
                ajax: '{!! action("EntitiesSitesController@tableSitesEntity") !!}',
                columns: [
                    { data: 'name', name: 'name' },
                    { data: 'link', name: 'link'},
                    { data: 'action', name: 'action', searchable: false, orderable: false },
                ],
                order: [['1', 'asc']]
            });
        });
    </script>
@endsection