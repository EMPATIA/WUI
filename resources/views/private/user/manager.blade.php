@extends('private._private.index')

@section('content')
    <style>
        .user-messages-notification{
            position: absolute;
            left: -12px;
            top: -12px;
            background: #fff;
            color: #62a351;
            padding: 0px;
            border: 2px solid;
            border-radius: 50%;
            font-size: 16px;
            height: 27px;
            width: 27px;
            font-weight: 900;
        }

        .btn-user-profile{
            margin: 5px;
            border-radius: 0;
        {{ ONE::actionType('users') == "edit" ? '' : 'width: 90%;' }}
}
    </style>
    <div class="row">
        <div class="{{((ONE::actionType('users') == "edit") ? 'col-12 col-md-6' : 'col-12 col-sm-9')}}">
            @php

            $form = ONE::form('users', trans('privateUser.details'), 'auth', $role)
                    ->settings(["model" => isset($user) ? $user : null, 'id' => isset($user) ? $user->user_key : null])
                    ->show('UsersController@edit', empty($user->anonymization) ? 'UsersController@delete' : null, ['id' => isset($user) ? $user->user_key : null,'role' => isset($inputRole) ? $inputRole : null], 'UsersController@index', ['id' => isset($user) ? $user->user_key : null,'role' => isset($inputRole) ? $inputRole : null])
                    ->create('UsersController@store', 'UsersController@index', ['id' => isset($user) ? $user->user_key : null,'role' => isset($inputRole) ? $inputRole : null])
                    ->edit('UsersController@update', 'UsersController@show', ['id' => isset($user) ? $user->user_key : null,'role' => isset($inputRole) ? $inputRole : null])
                    ->open();
            @endphp

            {!! Form::hidden('role', isset($role) ? $role : null) !!}
            {!! Form::oneText('name', array("name"=>trans('privateEntities.name'),"description"=>trans('privateEntities.nameDescription')), isset($user) ? $user->name  : null, ['class' => 'form-control', 'id' => 'name']) !!}
            {!! Form::oneText('email', array("name"=>trans('privateEntities.email'),"description"=>trans('privateEntities.emailDescription')), isset($user) ? $user->email  : null, ['class' => 'form-control', 'id' => 'email']) !!}

            @if($inputRole == 1 || $inputRole == 'manager' || $inputRole = "admin")
                @php
                    $hasLoginLevels = false;
                @endphp
            @endif

            @if(ONE::actionType('users') == "show" && $hasLoginLevels && $role != "admin")
                <div class="login-levels">{{trans('user.loginLevels')}}</div>
                <table id="user_login_levels" class="table table-striped dataTable no-footer table-responsive">
                    <thead>
                    <tr>
                        <th>{{ trans('privateUsers.login_level_name') }}</th>
                        <th>{{ trans('privateUsers.login_level_created_at') }}</th>
                    </tr>
                    </thead>
                </table>
            @endif

        <!-- Parameters -->

            @if(isset($registerParameters))
                @foreach($registerParameters as $parameter)
                    @if($parameter['parameter_type_code'] == 'text' || $parameter['parameter_type_code'] == 'vat_number')
                        {!! Form::oneText($parameter['parameter_user_type_key'], $parameter['name'],
                            !empty($parameter['value']) ? $parameter['value'] : null,
                            ['class' => 'form-control', 'id' => $parameter['parameter_user_type_key'], ($parameter['mandatory'] == true ? 'required' : null)]) !!}
                    @elseif($parameter['parameter_type_code'] == 'text_area')
                        {!! Form::oneTextArea($parameter['parameter_user_type_key'], $parameter['name'],
                            !empty($parameter['value']) ? $parameter['value']:null,
                            ['class' => 'form-control', 'id' => $parameter['parameter_user_type_key'] , ($parameter['mandatory'] == true ? 'required' : null) ]) !!}
                    @elseif($parameter['parameter_type_code'] == 'radio_buttons')
                        @if(count($parameter['parameter_user_options'])> 0)

                            @if(ONE::actionType('users') == "show")

                                @php
                                //$key = array_search(true, array_column($parameter['parameter_user_options'], 'selected'));
                                @endphp

                                @if(array_search(true, array_column($parameter['parameter_user_options'], 'selected')))
                                    <div class="form-group">
                                        <label for="{{$parameter['parameter_user_type_key']}}">{{ $parameter['name'] }}:@if($parameter['mandatory']) <span class="required-symbol">*</span> @endif</label>
                                        @foreach($parameter['parameter_user_options'] as $option)
                                            @if(!empty($option['selected']) && $option['selected'])
                                                <dd>{{$option['name']}}</dd>
                                            @endif
                                        @endforeach
                                    </div>
                                    <hr style="margin: 10px 0 10px 0">
                                @endif

                            @else
                                <div class="form-group">
                                    <label for="{{$parameter['parameter_user_type_key']}}">{{ $parameter['name'] }}:@if($parameter['mandatory']) <span class="required-symbol">*</span> @endif</label>
                                    @foreach($parameter['parameter_user_options'] as $option)
                                        <div class="radio">
                                            <label>
                                                <input @if(ONE::actionType('users') == "show") disabled @endif type="radio" name="{{$parameter['parameter_user_type_key']}}" id="{{$parameter['parameter_user_type_key']}}" value="{{$option['parameter_user_option_key']}}" @if($parameter['mandatory']) required @endif @if(!empty($option['selected']) && $option['selected']) checked @endif>{{$option['name']}}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                                <hr style="margin: 10px 0 10px 0">
                            @endif
                        @endif
                    @elseif($parameter['parameter_type_code'] == 'check_box')
                        @if(count($parameter['parameter_user_options'])> 0)
                            @if(ONE::actionType('users') == "show")
                                @if(array_search(true, array_column($parameter['parameter_user_options'], 'selected')))
                                    <div class="form-group">
                                        <label for="{{$parameter['parameter_user_type_key']}}">{{ $parameter['name'] }}:@if($parameter['mandatory']) <span class="required-symbol">*</span> @endif</label>
                                        @foreach($parameter['parameter_user_options'] as $option)
                                            @if(!empty($option['selected']) && $option['selected'])
                                                <dd>&#9745;&nbsp;{{$option['name']}}</dd>
                                            @endif
                                        @endforeach
                                    </div>
                                    <hr style="margin: 10px 0 10px 0">
                                @endif
                            @else
                                <div class="form-group">
                                    <label for="{{$parameter['parameter_user_type_key']}}">{{ $parameter['name'] }}:@if($parameter['mandatory']) <span class="required-symbol">*</span> @endif</label>
                                    @foreach($parameter['parameter_user_options'] as $option)
                                        <div class="checkbox">
                                            <label>
                                                <input type="checkbox" value="{{$option['parameter_user_option_key']}}" name="{{$parameter['parameter_user_type_key']}}[]" id="{{$parameter['parameter_user_type_key']}}" @if($parameter['mandatory']) required @endif @if(!empty($option['selected']) && $option['selected']) checked @endif>{{$option['name']}}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                                <hr style="margin: 10px 0 10px 0">
                            @endif
                        @endif
                    @elseif($parameter['parameter_type_code'] == 'dropdown')
                        @if(ONE::actionType('users') == "show")
                            @if(array_search(true, array_column($parameter['parameter_user_options'], 'selected')))
                                <div class="form-group">
                                    <label for="{{$parameter['parameter_user_type_key']}}">{{ $parameter['name'] }}: @if($parameter['mandatory']) <span class="required-symbol">*</span> @endif</label>
                                    @foreach($parameter['parameter_user_options'] as $option)
                                        @if(!empty($option['selected']) && $option['selected'])
                                            <dd>{{$option['name']}}</dd>
                                        @endif
                                    @endforeach
                                </div>
                                <hr style="margin: 10px 0 10px 0">
                            @endif
                        @else
                            <div class="form-group">
                                <label for="{{$parameter['parameter_user_type_key']}}">{{ $parameter['name'] }}: @if($parameter['mandatory']) <span class="required-symbol">*</span> @endif</label>
                                <select class="form-control" id="{{$parameter['parameter_user_type_key']}}" name="{{$parameter['parameter_user_type_key']}}" @if($parameter['mandatory']) required @endif>
                                    <option value="" selected>{{trans("publicUser.selectOption")}}</option>
                                    @foreach($parameter['parameter_user_options'] as $option)
                                        <option value="{{$option['parameter_user_option_key']}}" @if(!empty($option['selected']) && $option['selected']) selected @endif>{{$option['name']}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <hr style="margin: 10px 0 10px 0">
                        @endif
                    @elseif($parameter['parameter_type_code'] == 'birthday')
                        {!! Form::oneDate($parameter['parameter_user_type_key'], $parameter['name'], ( (!empty($parameter['value']) && $parameter['value'] != '') ? $parameter['value'] : date('Y-m-d')), ['class' => 'form-control oneDatePicker', 'id' => $parameter['parameter_user_type_key'], ($parameter['mandatory'] == true ? 'required' : null)]) !!}
                    @elseif($parameter['parameter_type_code'] == 'file' && !empty($parameter['value']))
                    <!-- This is not editable here we only see the photo or file to download -->
                        <div class="form-group" style="display:none">
                            <label for="{{$parameter['parameter_user_type_key']}}">{{ $parameter['name'] }}: @if($parameter['mandatory'])
                                    <span class="required-symbol">*</span> @endif</label>
                            <div class="box-tools dropFilesArea" id="{{$parameter['parameter_user_type_key']}}">
                                {!! ONE::fileSingleUploadBox("drop-zone", trans("cb.drag_and_drop_files_to_here") , 'user-file', 'files-list', (isset($parameter['value']['name']) ? $parameter['value']['name'] : null)) !!}
                            </div>
                            {!! Form::hidden($parameter['parameter_user_type_key'], (isset($parameter['value']['id']) ? $parameter['value']['id'] : null), ['id' => 'file_id']) !!}
                        </div>
                        <div class="form-group">
                            <label for="{{$parameter['parameter_user_type_key']}}">
                                {{ $parameter['name'] }}: @if($parameter['mandatory']) <span class="required-symbol">*</span> @endif
                            </label>
                            <div>
                                <a href="{{ action('FilesController@download',["id"=>$parameter['value']['id'], "code" => $parameter['value']['code'], 1, "inline" => 1])}}" target="_blank">
                                    {{ isset($parameter['value']['name']) ? $parameter['value']['name'] : null }}
                                </a>
                            </div>
                        </div>
                    @elseif($parameter['parameter_type_code'] == 'mobile')
                        <div class="form-group">
                            {!! Form::oneText($parameter['parameter_user_type_key'], $parameter['name'],
                                                        !empty($parameter['value']) ? $parameter['value'] : null,
                                                        ['class' => 'form-control', 'id' => $parameter['parameter_user_type_key'], ($parameter['mandatory'] == true ? 'required' : null), 'pattern'=> '\+?\s?[0-9\s]+' ]) !!}
                        </div>
                    @endif
                @endforeach
            @endif

            @if(ONE::actionType('users') == "create")
                {!! Form::hidden('confirmed', 1, ['id' => 'confirmed']) !!}
            @endif

            {!! $form->make() !!}

        </div>
        <div class="col-12 {{(ONE::actionType('users') == "edit" ? 'col-sm-6' : 'col-sm-3') }} ">
            @if(ONE::actionType("users")=="show" && !empty($user->anonymization))
                <div class="card">
                    <div class="card-header">
                        <h4>
                            {{ trans('privateUser.anonymization') }}
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="author">{{ trans("privateUsers.anonymizer") }}</label>
                            <br>
                            <a href="{{ action("UsersController@show",["userKey"=> $user->anonymization->anonymization_request->anonymizer->user_key,"role"=> "manager"]) }}">
                                {{ $user->anonymization->anonymization_request->anonymizer->name }}
                            </a>
                            <hr style="margin: 10px 0 10px 0">
                            <label for="author">{{ trans("privateUsers.anonymization_date") }}</label>
                            <br>
                            {{ $user->anonymization->created_at }}
                            <hr style="margin: 10px 0 10px 0">
                        </div>
                    </div>
                </div>
                <br>
            @endif
            <div class="card">
                <div class="card-header card-header-blue">
                    <h4 class="no-margin">
                        {{trans('user.options')}}
                    </h4>
                </div>
                <div class="card-body text-center">
                    @if(isset($user))
                        <a href="{{ action('UsersController@showUserMessages',['userKey' => $user->user_key]) }}" class="btn btn-sm btn-flat btn-info btn-user-profile" style="position: relative;">
                            <i class="fa fa-envelope-o" aria-hidden="true"></i>
                            {{trans('privateUsers.show_user_messages')}}
                            @if(isset($user_messages) and $user_messages > 0)
                                <span class="user-messages-notification">{{ $user_messages }}</span>
                            @endif
                        </a>
                    @endif

                    <div class="reconfirm-email-buttons">
                        @if(isset($user) && $user->confirmed == '0')
                            <a id='resend-email-confirmation'  class="btn btn-sm btn-flat btn-success btn-user-profile">{{trans("privateUsers.resend_email_confirmation")}}</a>
                            <a id='manual-email-confirmation'  class="btn btn-sm btn-flat btn-success btn-user-profile">{{trans("privateUsers.manual_email_confirmation")}}</a>
                        @endif
                    </div>
                    <div class="reconfirm-sms-buttons">
                        @if(isset($user) && !is_null($user->sms_token))
                            <a id='manual-sms-confirmation'  class="btn btn-sm btn-flat btn-success btn-user-profile">{{trans("privateUsers.manual_sms_confirmation")}}</a>
                        @endif
                    </div>
                </div>
            </div>
            
    </div>

    @if(ONE::actionType('users') == "show" && $hasLoginLevels)
        <!-- Modal -->
        <div id="login-levels-modal" class="modal fade" role="dialog">
            <div class="modal-dialog">

                <!-- Modal content-->
                <div class="modal-content">
                    <div class="card-header">
                        <h4 class="modal-title">{{trans('privateUsers.manage_login_level')}}</h4>
                    </div>
                    <div class="modal-body">
                        <table id="manage_user_login_levels" class="table table-bordered table-hover table-condensed">
                            <thead>
                            <tr>
                                <th>{{ trans('privateUsers.login_level_name') }}</th>
                                <th>{{ trans('privateUsers.login_level_manage') }}</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                    <div class="modal-footer">
                        {{--Empty--}}
                    </div>
                </div>

            </div>
        </div>
    @endif
@endsection

@section('scripts')
    <script>
        $("#user_type").select2();

        function generatePassword(){
            $("#generated_password").css('display', 'inline');
            var password = Math.random().toString(36).slice(-8);
            $("#generated_password").val(password)
            $("#password").val(password);
            $("#password_confirmation").val(password);
        }

        function roleChanged(){
            var role = $( "#role option:selected" ).val();
            if(role == "admin" || role == ""){
                $("#entity_id").parent().hide();
            }else{
                $("#entity_id").parent().show();
            }
        }

        {{--$("#reset_sms_sent").on('click', function() {--}}
            {{--$.ajax({--}}
                {{--method: 'POST',--}}
                {{--url: "{{ action('AuthController@resetSentSms') }}",--}}
                {{--data: {--}}
                    {{--user_key:"{{$user->user_key ?? null}}"--}}
                {{--},--}}
                {{--success: function (response ) {--}}
                    {{--toastr.success("{{trans('privateUsers.sms_reseted')}}");--}}
                    {{--location.reload();--}}
                {{--},--}}
                {{--error: function () {--}}
                    {{--toastr.success("{{trans('privateUsers.error_reseting_sms')}}");--}}
                {{--}--}}
            {{--});--}}
        {{--});--}}

        $('#resend-email-confirmation').on("click", function () {
            $.ajax({
                method: 'POST',
                url: "{{ action('AuthController@resendConfirmEmail') }}",
                data: {
                    user_key:"{{$user->user_key ?? null}}"
                },
                success: function () {
                    toastr.success("{{trans('privateUsers.emailSent')}}");
                },
                error: function () {
                    location.reload();
                }
            });
            return false;
        });

        $('#manual-email-confirmation').on("click", function () {
            $.ajax({
                method: 'GET',
                url: "{{ action('AuthController@manuallyConfirmUserEmail', ['userKey' => isset($user) ? $user->user_key : null]) }}",
                success: function (response) {
                    if (response !== 'ERROR'){
                        toastr.success("{{trans('privateUsers.emailConfirmed')}}");
                        $(".reconfirm-email-buttons").remove();
                    } else {
                        toastr.error("{{trans('privateUsers.errorConfirmingEmail')}}");
                    }
                },
                error: function () {
                    location.reload();
                }
            });
            return false;
        });

        $('#manual-sms-confirmation').on("click", function () {
            $.ajax({
                method: 'GET',
                url: "{{ action('AuthController@manuallyConfirmUserSms', ['userKey' => isset($user) ? $user->user_key : null]) }}",
                success: function (response) {
                    if (response !== 'ERROR'){
                        toastr.success("{{trans('privateUsers.smsConfirmed')}}");
                        $(".reconfirm-sms-buttons").remove();
                    } else {
                        toastr.error("{{trans('privateUsers.errorConfirmingSms')}}");
                    }
                },
                error: function () {
                    location.reload();
                }
            });
            return false;
        });

        $('#automatic-update-login-levels').on("click", function () {
            $.ajax({
                method: 'GET',
                url: "{{ action('UsersController@checkAndUpdateUserLoginLevel', ['userKey' => isset($user) ? $user->user_key : null]) }}",
                success: function (response) {
                    if (response !== 'ERROR'){
                        toastr.success("{{trans('privateUsers.loginLevelsUpdated')}}");
                        userLoginLevels.ajax.reload()
                        manageUserLoginLevels.ajax.reload()
                    } else {
                        toastr.error("{{trans('privateUsers.errorUpdatingLoginLevels')}}");
                    }
                },
                error: function () {
                    location.reload();
                }
            });
            return false;
        });

        var userLoginLevels = $('#user_login_levels').DataTable({
            language: {
                url: '{!! asset('/datatableLang/'.Session::get('LANG_CODE').'.json') !!}'
            },
            processing: true,
            serverSide: true,
            bDestroy: true,
            ajax: '{!! action('UsersController@tableUserLoginLevels', ['userKey' => $user->user_key ?? null]) !!}',
            columns: [
                { data: 'name', name: 'name' },
                { data: 'created_at', name: 'created_at' }
            ]
        });

        var manageUserLoginLevels = $('#manage_user_login_levels').DataTable({
            language: {
                url: '{!! asset('/datatableLang/'.Session::get('LANG_CODE').'.json') !!}'
            },
            processing: true,
            serverSide: true,
            bDestroy: true,
            ajax: '{!! action('UsersController@tableManageUserLoginLevels', ['userKey' => $user->user_key ?? null]) !!}',
            columns: [
                { data: 'name', name: 'name' },
                { data: 'manage', name: 'manage' },
            ],
            drawCallback: function( settings ) {
                $('#manage_user_login_levels a.login-level-operation').on("click",function(e){
                    clickedElement = $(this);
                    if (clickedElement.attr("href") !== undefined) {
                        $.ajax({
                            method: 'GET',
                            url: clickedElement.attr("href"),
                            beforeSend: function() {
                                clickedElement
                                        .css("pointer-events","none")
                                        .removeClass("btn-warning btn-danger")
                                        .addClass("btn-info")
                                        .find("i")
                                        .removeClass()
                                        .addClass("fa fa-spinner fa-spin");
                            },
                            success: function () {
                                $('#user_login_levels').DataTable().ajax.reload();
                                $('#manage_user_login_levels').DataTable().ajax.reload();
                            },
                            error: function () {
                                $('#user_login_levels').DataTable().ajax.reload();
                                $('#manage_user_login_levels').DataTable().ajax.reload();
                            }
                        });
                    }

                    e.preventDefault();
                    return false;
                });
            }
        });
    </script>
@endsection
