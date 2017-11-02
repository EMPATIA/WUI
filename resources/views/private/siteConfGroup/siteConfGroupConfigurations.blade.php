@extends('private._private.index')

@section('content')

    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header">
                    <h3 class="box-title"><i class="fa fa-language"></i> {{ trans('privateSiteConf.title') }}</h3>
                </div>
                <div class="box-body">
                    <table id="siteConfsList" class="table table-striped dataTable no-footer table-responsive">
                        <thead>
                        <tr>
                            <th width="50%">{{ trans('privateSiteConf.code') }}</th>
                            <th width="40%">{{ trans('privateSiteConf.name') }}</th>
                            <th width="10%">
                                {!! ONE::actionButtons($siteConfGroup->site_conf_group_key, ['create' => 'SiteConfigurationsController@create']) !!}
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
        @if(ONE::actionType('siteConfGroup') == "show")
            $(function () {

            getSidebar('{{ action("OneController@getSidebar") }}', 'confs', "{{isset($siteConfGroup) ? $siteConfGroup->site_conf_group_key : null}}", 'sidebar_admin.siteConfs')
            $('#siteConfsList').DataTable({
                language: {
                    url: '{!! asset('/datatableLang/'.Session::get('LANG_CODE').'.json') !!}',
                    search: '<a class="btn searchBtn" id="searchBtn"><i class="fa fa-search"></i></a>'
                },
                processing: true,
                serverSide: true,
                ajax: '{!! action('SiteConfGroupController@getConfsOfGroup',$siteConfGroup->site_conf_group_key) !!}',
                columns: [
                    { data: 'code', name: 'code', width: "50px"  },
                    { data: 'name', name: 'name' },
                    { data: 'action', name: 'action', searchable: false, orderable: false, width: "30px" },
                ],
                order: [['1', 'asc']]
            });
        });
        @endif
    </script>
@endsection