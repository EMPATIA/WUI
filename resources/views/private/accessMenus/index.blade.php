@extends('private._private.index')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header">
                    <h3 class="box-title"><i class="fa"></i> {{ trans('titles.accessMenus') }}</h3>

                </div>
                <div class="box-body">
                    <label class="filterBy-title" style="padding-left: 10px"> {{ trans('privateAccessMenu.filter_by') }} </label>


                    <div class="text-left" style="padding: 10px;">
                        <label style="padding-right 10px; float:left; line-height:38px">  {{trans('privateAccessMenu.active')}}</label>
                        <select id="activeFilter" name="activeFilter" class="userTypeFilter pull-left"
                                onchange="selectTypeFilter()" required>
                            <option value="0">{{ trans('privateAccessMenu.select') }}</option>
                            <option value="1">{{ trans("privateAccessMenu.Yes")}}</option>
                            <option value="2">{{ trans("privateAccessMenu.No")}}</option>

                        </select>
                    </div>
                    <br>
                    <table id="accessMenus_list" class="table table-hover table-striped dataTable no-footer">
                        <thead>
                        <tr>

                            <th>{{ trans('privateAccessMenu.name') }}</th>
                            <th>{{ trans('privateAccessMenu.link') }}</th>
                            <th>{{ trans('privateAccessMenu.active') }}</th>
                            <th>{{ trans('privateAccessMenu.code') }}</th>
                            <th>
                                {!! ONE::actionButtons(null, ['create' => 'AccessMenusController@create']) !!}
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
            $('#accessMenus_list').DataTable({
                language: {
                    url: '{!! asset('/datatableLang/'.Session::get('LANG_CODE').'.json') !!}',
                    search: '<a class="btn searchBtn" id="searchBtn"><i class="fa fa-search"></i></a>'
                },
                processing: true,
                serverSide: true,
                ajax: '{!! action('AccessMenusController@tableAccessMenus', $active ?? null) !!}',
                'beforeSend': function (request) {
                    request.setRequestHeader("X-ENTITY-KEY", "{{ Cache::get('X-ENTITY-KEY') }}" );
                },
                columns: [

                    { data: 'name', name: 'name' },
                    { data: 'siteLink', name: 'siteLink' },
                    { data: 'active', name: 'active' , searchable: false},
                    { data: 'code', name: 'code' , searchable: false},
                    { data: 'action', name: 'action', searchable: false, orderable: false, width: "55px" },
                ],
                order: [['1', 'asc']]
            });
        });


        $("#activeFilter").select2();

  function selectTypeFilter() {

      var url = "{{action('AccessMenusController@tableAccessMenus')}}";

      var userType = '';

      if ($('#activeFilter').val() != ''){
          active = $('#activeFilter').val();
      }
      url = url+'?active='+active;

      var tableUsers = $('#accessMenus_list').DataTable({
          language: {
              url: '{!! asset('/datatableLang/'.Session::get('LANG_CODE').'.json') !!}',
              search: '<a class="btn searchBtn" id="searchBtn"><i class="fa fa-search"></i></a>'
          },
          processing: true,
          serverSide: true,
          bDestroy: true,
          ajax: url,
          columns: [
            { data: 'name', name: 'name' },
            { data: 'siteLink', name: 'site.link' },
            { data: 'activeAction', name: 'action_activate', searchable: false, orderable: false, width: "5px" },
            { data: 'active', name: 'active' , searchable: false},
            { data: 'action', name: 'action', searchable: false, orderable: false, width: "55px" },
          ],
          order: [['1', 'asc']],

      });
  }

    </script>

@endsection
