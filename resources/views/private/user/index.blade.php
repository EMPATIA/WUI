@extends('private._private.index')

@section('content')
    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title"><i class="fa"></i> {{ trans('privateUsers.list') }}</h3>
        </div>
        <div class="box-body">
            <div class="row">
                @if(!empty(One::getEntityKey()))
                    <div class="col-lg-4 col-md-12 col-sm-12 col-12">
                        <label for="anonymized_users_checkbox">
                            {{ trans("privateUsers.anonymized_users") }}
                        </label>
                        <div class="onoffswitch">
                            <input type="checkbox" name="anonymized_users_checkbox" class="onoffswitch-checkbox" id="anonymized_users_checkbox" value="1" onchange="selectTypeFilter()">
                            <label class="onoffswitch-label" for="anonymized_users_checkbox">
                                <span class="onoffswitch-inner"></span>
                                <span class="onoffswitch-switch"></span>
                            </label>
                        </div>
                    </div>
                @endif
                <div class="col-lg-3 col-md-12 col-sm-12 col-12">
                    <label class="filterBy-title"> {{ trans('privateUsers.filter_by_type') }} </label>
                    <div class="text-left">
                        <select id="userTypeFilter" name="userTypeFilter" class="userTypeFilter pull-right" onchange="selectTypeFilter()" required style="width:70%">
                            <option value="">{{ trans('privateUsers.all_users') }}</option>
                            @foreach($roles as $key => $role_name)
                                <option value="{!! $key !!}">{!! $role_name !!}</option>
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
                                @if(Session::get('user_role') == 'admin')
                                    <option value="{!! $key !!}">{!! $role_name !!}</option>
                                @endif
                            @endforeach
                        </select>
                        <div class="input-group-btn" style="height: 34px">
                            <a id="user_type_btn" type="" class="btn btn-flat btn-submit" style="height:100%;line-height: 23px;">{!! trans("privateUsers.create_user") !!}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="box-body">
            <table id="users_list" class="table table-hover table-striped dataTable no-footer table-responsive">
                <thead>
                <tr>
                    <th><input type="checkbox" id="checkAll"/></th>
                    <th>{{ trans('privateUsers.name') }}</th>
                    <th>{{ trans('privateUsers.email') }}</th>
                    <th>{{ trans('privateUsers.role') }}</th>
                    <th>{{ trans('privateUsers.created_at') }}</th>
                    <th style=""></th>
                    <th style=""></th>
                </tr>
                </thead>
            </table>
            @if(!empty(One::getEntityKey()))
                <div>
                    <a href="#exportModal" data-toggle="modal" class="btn btn-flat btn-submit" style="margin-top: 7px;">
                        <i class="fa fa-download" aria-hidden="true"></i>
                        {{ trans('privateUsers.download') }}
                    </a>
                    @if(Session::get('user_role') == 'admin')
                        <a href="#anonymizeModal" data-toggle="modal" class="btn btn-flat btn-danger" style="margin-top: 7px;">
                            <i class="fa fa-user-secret" aria-hidden="true"></i>
                            {{ trans('privateUsers.anonymize_users') }}
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>

    @if(!empty(One::getEntityKey()))
        <div class="modal fade" id="exportModal" role="dialog" aria-labelledby="exportModal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="card-header">
                        <h3 class="modal-title">{{trans('privateUsers.export_user_data')}}</h3>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12 text-center">
                                <form id="excelExport" class="export-data" action="{{ action("UsersController@excel") }}" method="POST" style="display:inline-block;">
                                    {{ csrf_field() }}
                                    <input type="hidden" id="userKeys" name="userKeys"/>

                                    <button class="btn btn-flat btn-submit" type="submit">
                                        <i class="fa fa-file-excel-o" aria-hidden="true"></i>
                                        {{ trans('privateUsers.download') }}
                                    </button>
                                </form>
                                <form id="pdfExport" class="export-data" action="{{ action("UsersController@pdfList") }}" method="POST" style="display:inline-block;">
                                    {{ csrf_field() }}
                                    <input type="hidden" id="userKeys" name="userKeys"/>

                                    <button class="btn btn-flat btn-submit" type="submit">
                                        <i class="fa fa-file-pdf-o" aria-hidden="true"></i>
                                        {{ trans('privateUsers.download') }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" data-dismiss="modal">{{ trans('privateUsers.cancel') }}</button>
                    </div>
                </div>
            </div>
        </div>
        @if(Session::get('user_role') == 'admin')
            <div class="modal fade" id="anonymizeModal" role="dialog" aria-labelledby="anonymizeModal">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="card-header bg-danger">
                            <h3 class="modal-title">{{trans('privateUsers.anonymize_users_data')}}</h3>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-12">
                                    {{ trans("privateUsers.anonymize_users_data_content") }}
                                </div>
                                <div class="col-12 text-center">
                                    <form id="anonymizeUsers" action="{{ action("UsersController@anonymizeUsers") }}" method="POST" style="display:inline-block;">
                                        {{ csrf_field() }}
                                        <input name="_method" type="hidden" value="DELETE"/>
                                        <input type="hidden" id="userKeys" name="userKeys"/>

                                        <button class="btn btn-submit btn-danger bg-red" type="submit">
                                            <i class="fa fa-user-secret" aria-hidden="true"></i>
                                            {{ trans('privateUsers.anonymize_users') }}
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" data-dismiss="modal">{{ trans('privateUsers.cancel') }}</button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endif
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

        $('#checkAll').click(function () {
            $('table#users_list input:checkbox:enabled').prop('checked', this.checked);
        });

        function selectTypeFilter() {

            var url = "{{action('UsersController@tableUsers')}}";

            var userType = '';

            if ($('#userTypeFilter').val() != ''){
                userType = $('#userTypeFilter').val();
            }
            url = url+'?role='+userType;
            if($("#anonymized_users_checkbox").is(":checked"))
                url += "&anonymized=true";

            var tableUsers = $('#users_list').DataTable({
                lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
                language: {
                    url: '{!! asset('/datatableLang/'.Session::get('LANG_CODE').'.json') !!}',
                    search: '<a class="btn searchBtn" id="searchBtn"><i class="fa fa-search"></i></a>'
                },
                processing: true,
                serverSide: true,
                bDestroy: true,
                ajax: url,
                columns: [
                    { data: 'select_users', name: 'select_users', searchable: false, orderable: false, width: "5px" },
                    { data: 'name', name: 'name' },
                    { data: 'email', name: 'email' },
                    { data: 'role', name: 'role', searchable: false, orderable: false},
                    { data: 'created_at', name: 'created_at' },
                    { data: 'authorize', name: 'authorize', searchable: false, orderable: false, width: "30px" },
                    { data: 'action', name: 'action', searchable: false, orderable: false, width: "30px" },
                ],
                order: [['1', 'asc']],
                "columnDefs": [
                    { className: "text-center", "targets": [ 2 ] }
                ]
            });
        }

        $("form.export-data").on("submit",function(event) {
            var usersKeys = [];
            $('.user_key:checked').each( function(i, obj){
                usersKeys.push($(obj).val());
            });

            $('form.export-data input[type="hidden"][name="userKeys"]').val(JSON.stringify(usersKeys));
        });
        $("form#anonymizeUsers").on("submit",function(event) {
            var usersKeys = [];
            $('.user_key:checked').each( function(i, obj){
                usersKeys.push($(obj).val());
            });

            $('form#anonymizeUsers input[type="hidden"][name="userKeys"]').val(JSON.stringify(usersKeys));
        });

        $(document).ready(function(){
            selectTypeFilter();
        });
    </script>
@endsection
