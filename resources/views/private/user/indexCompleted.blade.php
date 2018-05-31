@extends('private._private.index')

@section('content')
    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title"><i class="fa"></i> {{ trans('user.list') }}</h3>
        </div>

        <div class="box-body">

            <div class="row" style="margin:0 -20px">
                <div class="col-sm-6 col-12 margin-bottom-20">
                    <div style="padding-left: 10px"> {{ trans('user.site') }} </div>
                    <div class="text-left" style="padding: 10px">
                        <select id="userSiteFilter" name="userSiteFilter" class="userSiteFilter pull-left col-6" onchange="selectSiteFilter()" required>
                            @foreach($sites as $site)
                                <option value="{!! $site->key !!}">{!! $site->name !!}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <table id="users_list" class="table table-hover table-striped dataTable no-footer table-responsive">
                <thead>
                <tr>
                    <th>{{ trans('user.name') }}</th>
                    <th>{{ trans('user.email') }}</th>
                    <th>{{ trans('user.created_at') }}</th>
                    <th>{{ trans('user.updated_at') }}</th>
                    <th style="width:10%"></th>
                    <th></th>
                </tr>
                </thead>
            </table>
        </div>
    </div>

@endsection


@section('scripts')
    <script>
        $("#userSiteFilter").select2();

        $( document ).ready(function() {
            selectSiteFilter();
        });

        function selectSiteFilter()
        {
            var userSite = '';
            var url = "{{action('UsersController@tableUsersCompleted')}}";

            if ($('#userSiteFilter').val() != ''){
                userSite = $('#userSiteFilter').val();
            }
            url = url+'?site_key='+userSite;

            var tableUsers = $('#users_list').DataTable({
                language: {
                    url: '{!! asset('/datatableLang/'.Session::get('LANG_CODE').'.json') !!}',
                    search: '<a class="btn searchBtn" id="searchBtn"><i class="fa fa-search"></i></a>'
                },
                processing: true,
                serverSide: true,
                bDestroy: true,
                ajax: {
                    url: url,
                    dataFilter: function(data){
                        var json = jQuery.parseJSON(data);
                        json.recordsFiltered = json.filtered;
                        json.recordsTotal = json.total;
                        return JSON.stringify( json ); // return JSON string
                    }
                },
                columns: [
                    { data: 'name', name: 'name' },
                    { data: 'email', name: 'email' },
                    { data: 'created_at', name: 'created_at' },
                    { data: 'updated_at', name: 'updated_at', searchable: false, orderable: false },
                    { data: 'authorize', name: 'authorize', searchable: false, orderable: false, width: "30px" },
                    { data: 'action', name: 'action', searchable: false, orderable: false, width: "30px" },
                ],
                order: [['2', 'desc']]
            });
        }


        $(document).on("click", ".manual-login-level", function(event) {
            var manualLoginLevel = $(this).attr('href');
            var table = $('#users_list').DataTable();
            $.ajax({
                method: 'POST',
                url: manualLoginLevel,
                data: {
                    page: 'moderate_users',
                    moderation: false
                },
                success: function (response) {
                    table.ajax.reload();
                    toastr.success('{{trans('privateEntityLoginLevels.manual_login_level_ok') }}', '', {positionClass: "toast-bottom-full-width"});
                },
                error: function () {
                    table.ajax.reload();
                    toastr.error('{{trans('privateEntityLoginLevels.manual_login_level_failed') }}', '', {positionClass: "toast-bottom-full-width"});
                }
            });
            return false;
        });


    </script>
@endsection
