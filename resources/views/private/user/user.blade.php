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

        .btn-user-profile {
            margin: 5px;
            border-radius: 0;
        {{ ONE::actionType('users') == "edit" ? '' : 'width: 90%;' }}
        }

        .login-levels{
            font-weight: bold;
            margin-bottom: 15px;
        }

        dl{
            margin-bottom: 0 !important;
        }
    </style>
    <div class="row">
        @if(ONE::actionType('users') == "edit")
            @if(isset($user->moderated) && ($user->moderated = false))
                <div class="col-12 text-right" style="padding-bottom: 20px">
                    @foreach ($data->{$user->user_key}->login_levels as $login_level)
                        <span href='{{action('UsersController@manualCheckLoginLevel',['userKey' => $user->user_key, 'login_level_key' => $login_level->key])}}' class='manual-login-level btn btn-success btn-sm right'><i class='glyphicon glyphicon-thumbs-up'></i>{{$login_level->name}}</span>
                    @endforeach
                </div>
            @endif
            <div class="col-12 d-block d-sm-block d-md-none">
                <form action="{{ URL::action('UsersController@updatePassword', isset($user) ? $user->user_key : null) }}" method="POST">
                    <div class="" id="accordion" role="tablist" aria-multiselectable="true" style="margin-bottom: 0">
                        <div class="card">
                            <div class="card-header" role="tab" id="headingOne">
                                <h4>
                                    <a role="button" data-toggle="collapse" data-parent="#accordion" href="#changePassword" aria-expanded="true" aria-controls="changePassword" style="text-decoration: underline">
                                        {{trans('user.changePassword')}}
                                    </a>
                                </h4>
                            </div>
                            <div id="changePassword" class="panel-collapse collapse out" role="tabpanel" aria-labelledby="headingOne">
                                <div class="card-body">
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                    {!! Form::onePassword('old_password', trans('user.oldPassword'), null, ['class' => 'form-control', 'id' => 'old_password']) !!}
                                    {!! Form::onePassword('password', trans('user.password'), null, ['class' => 'form-control', 'id' => 'password']) !!}
                                    {!! Form::onePassword('password_confirmation', trans('user.passwordConfirmation'), null, ['class' => 'form-control', 'id' => 'password_confirmation']) !!}
                                    <input class="btn btn-flat empatia" type="submit" value="{{trans('privateUsers.changePassword')}}" id="btn_change_password">
                                    <input class="btn btn-info" type="button" value="{{trans('privateUsers.generate_random_password')}}" id="btn_generate_random_password">
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="col-12 d-block d-sm-block d-md-none">
                <div class="card d-none d-sm-block">
                    <div class="card-header">
                        <h4>{{trans('privateUsers.add_user_to_entity')}}</h4>
                    </div>
                    <div class="card-body">
                        {!! Form::oneText('search_email', trans('privateUsers.search_user_email'), null, ['class' => 'form-control', 'id' => 'search_email']) !!}
                        {!! Form::button('Search', ['class' => 'btn btn-success', 'onclick' => 'getUser()']) !!}
                        <table id="users_list" class="table table-striped dataTable no-footer margin-top-20 table-responsive">
                            <thead>
                            <tr>
                                <th>{{ trans('privateUsers.email') }}</th>

                            </tr>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        @endif
        <div class="{{((ONE::actionType('users') == "edit") ? 'col-12 col-md-6' : 'col-12 col-sm-9')}}">
            @php
                $form = ONE::form('users', trans('privateUser.details'))
                    ->settings(["model" => isset($user) ? $user : null, 'id' => isset($user) ? $user->user_key : null])
                    ->show('UsersController@edit', 'UsersController@delete', ['userKer' => isset($user) ? $user->user_key : null,'role' => isset($inputRole) ? $inputRole : null], (empty($moderation)? 'UsersController@index' : 'UsersController@indexCompleted'), ['role' => isset($inputRole) ? $inputRole : null])
                    ->create('UsersController@store', 'UsersController@index', ['userKer' => isset($user) ? $user->user_key : null,'role' => isset($inputRole) ? $inputRole : null])
                    ->edit('UsersController@update', 'UsersController@show', ['userKer' => isset($user) ? $user->user_key : null,'inputRole' => isset($inputRole) ? $inputRole : null])
                    ->open();
            @endphp

            @if(ONE::actionType('users') == "show" && isset($usersToModerate) && array_key_exists($user->user_key,$usersToModerate))
                @if(isset($usersToModerate->{$user->user_key}))
                    <div class="text-right">
                        @foreach ($usersToModerate->{$user->user_key}->login_levels as $login_level)
                            <span href='{{action('UsersController@manualCheckLoginLevel',['userKey' => $user->user_key, 'login_level_key' => $login_level->key])}}' class='manual-login-level btn btn-success btn-sm right'><i class='glyphicon glyphicon-thumbs-up'></i>{{$login_level->name}}</span>
                        @endforeach
                    </div>
                @endif


            @endif
        <!-- Role and Entity Selection -->
            {!! Form::hidden('role', isset($role) ? $role : "", ['id' => 'role']) !!}

            @if(ONE::actionType('users') == "edit" && ONE::isAdmin())
                {!! Form::oneSelect('role', trans('user.user_roles'), isset($roles) ? $roles : null, $role ?? null, isset($roles) ? $roles : null, ['class' => 'form-control', 'id' => 'role']) !!}
            @endif
        <!-- User details -->
            {!! Form::oneText('name', trans('user.name'), isset($user) ? $user->name  : null, ['class' => 'form-control', 'id' => 'name']) !!}
            {!! Form::oneText('email', trans('user.email'), isset($user) ? $user->email  : null, ['class' => 'form-control', 'id' => 'email']) !!}

            @if(ONE::actionType('users') == "show" && isset($hasLoginLevels) && !empty($hasLoginLevels))
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

            @if(!empty($levels))
                {!! Form::oneSelect('user_level', trans('user.user_level'), isset($levels) ? $levels : null, $user->user_level->position ?? null, isset($user->user_level->name) ? $user->user_level->name : null, ['class' => 'form-control', 'id' => 'status']) !!}
            @endif

            @if(ONE::actionType('users') == "create")
            <!-- Change password -->
                <div class="card flat">
                    <div class="card-header">{{ trans('user.password') }}</div>
                    <div class="box-body">
                        {!! Form::onePassword('password', trans('privateUser.password'), null, ['class' => 'form-control', 'id' => 'password',(ONE::actionType('users') == "create" ? 'required' : null)]) !!}
                        {!! Form::onePassword('password_confirmation', trans('privateUser.password_confirmation'), null, ['class' => 'form-control', 'id' => 'password_confirmation',(ONE::actionType('users') == "create" ? 'required' : null)]) !!}
                        <input class="btn btn-secondary" onclick="generatePassword()" type="button" value="{{trans('privateUser.generate_random_password')}}" id="btn_generate_random_password">
                    </div>
                </div>
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
                    @elseif($parameter['parameter_type_code'] == 'numeric')
                        <div class="form-group">
                            {!! Form::oneNumber($parameter['parameter_user_type_key'], $parameter['name'],
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

        @if(ONE::actionType('users') == "edit")
            <div class="col-md-6 d-none d-sm-none d-md-block">
                <form action="{{ URL::action('UsersController@updatePassword', isset($user) ? $user->user_key : null) }}" method="POST" class="d-none d-sm-block">
                    <div class="card d-none d-sm-block">
                        <div class="card-header">
                            <h4>{{trans('user.changePassword')}}</h4>
                        </div>
                        <div class="card-body">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            {{--{!! Form::onePassword('old_password', trans('user.oldPassword'), null, ['class' => 'form-control', 'id' => 'old_password']) !!}--}}
                            {!! Form::onePassword('password', trans('user.password'), null, ['class' => 'form-control', 'id' => 'password']) !!}
                            {!! Form::onePassword('password_confirmation', trans('user.passwordConfirmation'), null, ['class' => 'form-control', 'id' => 'password_confirmation']) !!}
                            <input class="btn btn-flat empatia" type="submit" value="{{trans('user.changePassword')}}" id="btn_change_password">
                            <input class="btn btn-info" onclick="generatePassword()" type="button" value="{{trans('privateUsers.generate_random_password')}}" id="btn_generate_random_password">
                            <input type="text" id="generated_password" style="display: none">
                        </div>
                    </div>
                </form>
            </div>
        @endif

        <div class="col-12 {{(ONE::actionType('users') == "edit" ? 'col-sm-6' : 'col-sm-3') }} ">
            <div class="card">
                <div class="card-header">
                    <h4>
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
                        <div class="reconfirm-email-buttons">
                            @if($user->confirmed == '0')
                                <a id='resend-email-confirmation' class="btn btn-sm btn-flat btn-success btn-user-profile">{{trans("privateUsers.resend_email_confirmation")}}</a>
                                <a id='manual-email-confirmation' class="btn btn-sm btn-flat btn-success btn-user-profile">{{trans("privateUsers.manual_email_confirmation")}}</a>
                            @endif
                        </div>
                        <div class="reconfirm-sms-buttons">
                            @if(!is_null($user->sms_token))
                                <a id='manual-sms-confirmation' class="btn btn-sm btn-flat btn-success btn-user-profile">{{trans("privateUsers.manual_sms_confirmation")}}</a>
                            @endif
                        </div>
                        @if(ONE::actionType('users') == "show" && isset($hasLoginLevels) && !empty($hasLoginLevels))
                            <button type="button" class="btn btn-sm btn-flat btn-warning btn-user-profile" data-toggle="modal" data-target="#login-levels-modal">
                                <i class="fa fa-unlock-alt" aria-hidden="true"></i>
                                {{trans('privateUsers.manage_login_levels')}}
                            </button>
                            <div class="automatic-update-login-levels">
                                <a id='automatic-update-login-levels' class="btn btn-sm btn-flat btn-warning btn-user-profile" onclick="updateLoginLevels()"><i class="fa fa-refresh" aria-hidden="true"></i> {{trans("privateUsers.automatic_update_login_levels")}}</a>
                            </div>
                        @endif
                        @if(ONE::actionType('users') == 'show' && Session::has('SITE-CONFIGURATION.sms_max_send') && isset($user->sms_sent) && $user->sms_sent >= Session::get('SITE-CONFIGURATION.sms_max_send'))
                            <a id="reset_sms_sent" class="btn btn-xs btn-flat btn-warning btn-user-profile">{{trans("privateUsers.reset_sms_sent")}}</a>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
    </div>
    @if(ONE::actionType('users') == "show" && isset($hasLoginLevels) && !empty($hasLoginLevels))
        <!-- Modal -->
        <div id="login-levels-modal" class="modal fade" role="dialog">
            <div class="modal-dialog">

                <!-- Modal content-->
                <div class="modal-content">
                    <div class="card-header">
                        <h4 class="modal-title">{{trans('privateUsers.manage_login_level')}}</h4>
                    </div>
                    <div class="modal-body">
                        <table id="manage_user_login_levels" class="table table-hover table-condensed">
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
        $( document ).ready(function() {
            @if(ONE::actionType('users') == "create" || ONE::actionType('users') == "edit")
            roleChanged();
            @endif
        });

        var userLoginLevels = $('#user_login_levels').DataTable({
            language: {
                url: '{!! asset('/datatableLang/'.Session::get('LANG_CODE').'.json') !!}',
                search: '<a class="btn searchBtn" id="searchBtn"><i class="fa fa-search"></i></a>'
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

        $("#reset_sms_sent").on('click', function() {
            $.ajax({
                method: 'POST',
                url: "{{ action('AuthController@resetSentSms') }}",
                data: {
                    user_key:"{{$user->user_key ?? null}}"
                },
                success: function (response ) {
                    toastr.success("{{trans('privateUsers.sms_reseted')}}");
                    location.reload();
                },
                error: function () {
                    toastr.success("{{trans('privateUsers.error_reseting_sms')}}");
                }
            });
        });

        function updateLoginLevels() {
            $.ajax({
                method: 'GET',
                url: "{{ action('UsersController@checkAndUpdateUserLoginLevel', ['userKey' => isset($user) ? $user->user_key : null]) }}",
                success: function (response) {
                    if (response !== 'ERROR') {
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
        }

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
                        updateLoginLevels();
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

        var manageUserLoginLevels = $('#manage_user_login_levels').DataTable({
            language: {
                url: '{!! asset('/datatableLang/'.Session::get('LANG_CODE').'.json') !!}',
                search: '<a class="btn searchBtn" id="searchBtn"><i class="fa fa-search"></i></a>'
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