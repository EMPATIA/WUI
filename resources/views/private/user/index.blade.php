@extends('private._private.index')

@section('content')
    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title"><i class="fa"></i> {{ trans('privateUsers.list') }}</h3>
        </div>
        <div class="box-body">
            <div class="row">
                <div class="col-lg-3 col-md-12 col-sm-12 col-12">
                    <label class="filterBy-title"> {{ trans('privateUsers.filter_by_type') }} </label>
                    <div class="text-left">
                        <select id="userTypeFilter" name="userTypeFilter" class="userTypeFilter pull-right" onchange="selectTypeFilter()" required style="width:70%">
                            <option value="">{{ trans('privateUsers.all_users') }}</option>
                            @foreach($roles as $key => $role_name)
                                @if(ONE::verifyUserPermissionsCrud('auth', $key))
                                    <option value="{!! $key !!}">{!! $role_name !!}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-lg-4 col-md-12 col-sm-12 col-12">
                    <label class="filterBy-title"> {{ trans('privateUsers.create_user') }} </label>
                    <div class="input-group">
                        <select id="userTypeSelect" name="userTypeSelect" class="userTypeSelect pull-right" onchange="selectUserType()" required style="width:50%" >
                            <option value="">{{ trans('privateUsers.select_user_type') }}</option>
                            @foreach($roles as $key => $role_name)
                                @if(Session::get('user_role') == 'admin' || ONE::verifyUserPermissionsCreate('auth', $key))
                                    <option value="{!! $key !!}">{!! $role_name !!}</option>
                                @endif
                            @endforeach
                        </select>
                        <div class="input-group-btn" style="height: 34px">
                            <a id="user_type_btn" type="" class="btn btn-flat btn-submit" style="height:100%;line-height: 23px;">{!! trans("privateUsers.create_user") !!}</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5 col-md-12 col-sm-12 col-12">

                    <div style="height: 100%;display: grid;padding-top: 20px">
                        <div style="display:flex;justify-content:flex-end;align-items:flex-end;">
                            <div>
                                <a href="{{ action("UsersController@excel") }}" class="btn btn-flat btn-submit" style="margin-top: 7px;">
                                    <i class="fa fa-file-excel-o" aria-hidden="true"></i>
                                    {{ trans('privateUsers.download') }}
                                </a>
                                <a href="{{ action("UsersController@pdfList") }}" class="btn btn-flat btn-submit" style="margin-top: 7px;">
                                    <i class="fa fa-file-pdf-o" aria-hidden="true"></i>
                                    {{ trans('privateUsers.download') }}
                                </a>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="box-body">
            <table id="users_list" class="table table-hover table-striped dataTable no-footer table-responsive">
                <thead>
                <tr>
                    <th>{{ trans('privateUsers.name') }}</th>
                    <th>{{ trans('privateUsers.email') }}</th>
                    <th>{{ trans('privateUsers.role') }}</th>
                    <th>{{ trans('privateUsers.created_at') }}</th>
                    <th style=""></th>
                    <th style=""></th>
                </tr>
                </thead>
            </table>

        </div>
    </div>
@endsection

@section('scripts')
    <script>

        $("#userTypeSelect").select2();
        $("#userTypeFilter").select2();

        function selectUserType() {
            if ($('#userTypeSelect').val() != ''){
                $('#user_type_btn').removeClass('disabled');
            } else {
                $('#user_type_btn').addClass('disabled');
            }
        }

        $('#user_type_btn').click(function () {
            var url = "{{action('UsersController@create')}}";
            var userType = $('#userTypeSelect').val();

            if ($('#userTypeSelect').val() == ''){
                return false;
            }

            url = url+'?role='+userType;
            window.location.href = url;
        });


        var tableUsers = $('#users_list').DataTable({
            language: {
                url: '{!! asset('/datatableLang/'.Session::get('LANG_CODE').'.json') !!}',
                search: '<a class="btn searchBtn" id="searchBtn"><i class="fa fa-search"></i></a>'
            },
            processing: true,
            serverSide: true,
//            bDestroy: true,
            ajax: '{!! action('UsersController@tableUsers', ['role' => strtolower($role) ?? null]) !!}',
            columns: [
                { data: 'name', name: 'name' },
                { data: 'email', name: 'email' },
                { data: 'role', name: 'role', searchable: false, orderable: false},
                { data: 'created_at', name: 'created_at' },
                { data: 'authorize', name: 'authorize', searchable: false, orderable: false, width: "30px" },
                { data: 'action', name: 'action', searchable: false, orderable: false, width: "50px" }
            ],
            order: [['0', 'asc']],
            "columnDefs": [
                { className: "text-center", "targets": [ 2 ] }
            ],
            "displayStart":0
        });

        function selectTypeFilter() {

            var url = "{{action('UsersController@tableUsers')}}";

            var userType = '';

            if ($('#userTypeFilter').val() != ''){
                userType = $('#userTypeFilter').val();
            }
            url = url+'?role='+userType;

            var tableUsers = $('#users_list').DataTable({
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
                    { data: 'email', name: 'email' },
                    { data: 'role', name: 'role', searchable: false, orderable: false},
                    { data: 'created_at', name: 'created_at' },
                    { data: 'authorize', name: 'authorize', searchable: false, orderable: false, width: "30px" },
                    { data: 'action', name: 'action', searchable: false, orderable: false, width: "30px" },
                ],
                order: [['0', 'asc']],
                "columnDefs": [
                    { className: "text-center", "targets": [ 2 ] }
                ]
            });
        }

    </script>
@endsection
