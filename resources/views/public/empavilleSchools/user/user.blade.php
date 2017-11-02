@section('header_styles')
    <link href="{{ asset("css/cropper.min.css") }}" rel='stylesheet' type='text/css'>
@endsection

@extends('public.empavilleSchools._layouts.index')
@section('content')
    <!-- Form -->
    <div class="container" style="margin-bottom: 50px">
        <div class="contentPage-heading-wrapper" style="z-index: 0">
            <div class="row">
                <div class="col-xs-12">
                    <h2 class="underline">{{ trans("defaultUser.profile") }}</h2>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3 col-sm-12 col-xs-12 text-center">
                <div class="widget-user-header user-picture-box">
                    <div class="widget-user-image">
                        @if($user->photo_id > 0)
                            <img class="img" src="{{URL::action('FilesController@download', ['id' => $user->photo_id, 'code' => $user->photo_code, 1] )}}" alt="User profile picture" id="user-image-drop-zone">
                        @else
                            <img class="img" src="{{ asset('images/icon-user-default-160x160.png') }}" alt="User profile picture" id="user-image-drop-zone">
                        @endif
                    </div>
                </div>

                <!-- /.widget-user-image -->
                <div class="top-buffer">
                    <button id="user-image" class="btn btn-flat btn-block empatia center-block"><i
                                class="fa fa-upload"></i>&nbsp;{{ trans('defaultUser.change_profile_picture') }}</button>
                    {!! ONE::imageCropModal('getCroppedCanvasModal', 'getCroppedCanvasTitle', trans('defaultUser.image_resize')) !!}
                    <button class="btn btn-flat btn-block empatia center-block top-buffer" data-toggle="modal" data-target="#changePasswordModal">
                        {{trans('defaultUser.change_password_modal_btn')}}
                    </button>
                </div>
            </div>
            <div class="col-md-9 col-sm-12 col-xs-12">

                <div class="col-md-12 col-sm-12 col-xs-12">

                    <?php
                    $form = ONE::form('user')
                        ->settings(["model" => isset($user) ? $user : null,'id' => isset($user) ? $user->user_key : null])
                        ->show(null,null)
                        ->create(null,null)
                        ->edit('PublicUsersController@update', 'PublicController@index',isset($user)? $user->user_key : null)
                        ->open();
                    ?>

                    {!! Form::oneText('name', trans('defaultUser.user_name') , isset($user->name) ? $user->name : null, ['class' => 'form-control', 'id' => 'name', 'required' => 'required']) !!}
                    {!! Form::oneText('email', trans('defaultUser.email'), isset($user->email) ? $user->email : null, ['class' => 'form-control', 'id' => 'email', 'required' => 'required', 'readonly' => 'readonly']) !!}

                    @if(isset($registerParameters))
                        @foreach($registerParameters as $parameter)
                            @if($parameter['parameter_type_code'] == 'text')
                                {!! Form::oneText($parameter['parameter_user_type_key'], $parameter['name'],
                                    $parameter['value'],
                                    ['class' => 'form-control', 'id' => $parameter['parameter_user_type_key'], 'required' => ($parameter['mandatory'] == true ? 'required' : null)]) !!}
                            @elseif($parameter['parameter_type_code'] == 'text_area')
                                {!! Form::oneTextArea($parameter['parameter_user_type_key'], $parameter['name'],
                                    $parameter['value'],
                                    ['class' => 'form-control', 'id' => $parameter['parameter_user_type_key'] , 'required' => ($parameter['mandatory'] == true ? 'required' : null) ]) !!}
                            @elseif($parameter['parameter_type_code'] == 'radio_buttons')
                                @if(count($parameter['parameter_user_options'])> 0)
                                    <div class="form-group">
                                        <label for="{{$parameter['parameter_user_type_key']}}">{{ $parameter['name'] }}:@if($parameter['mandatory']) <span class="required-symbol">*</span> @endif</label>
                                        @foreach($parameter['parameter_user_options'] as $option)
                                            <div class="radio">
                                                <label>
                                                    <input type="radio" name="{{$parameter['parameter_user_type_key']}}" id="{{$parameter['parameter_user_type_key']}}" value="{{$option['parameter_user_option_key']}}" @if($parameter['mandatory']) required @endif @if($option['selected']) checked @endif>{{$option['name']}}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            @elseif($parameter['parameter_type_code'] == 'check_box')
                                @if(count($parameter['parameter_user_options'])> 0)
                                    <div class="form-group">
                                        <label for="{{$parameter['parameter_user_type_key']}}">{{ $parameter['name'] }}:@if($parameter['mandatory']) <span class="required-symbol">*</span> @endif</label>
                                        @foreach($parameter['parameter_user_options'] as $option)
                                            <div class="checkbox">
                                                <label><input type="checkbox" value="{{$option['parameter_user_option_key']}}" name="{{$parameter['parameter_user_type_key']}}[]" id="{{$parameter['parameter_user_type_key']}}" @if($parameter['mandatory']) required @endif @if($option['selected']) checked @endif>{{$option['name']}}</label>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            @elseif($parameter['parameter_type_code'] == 'dropdown')
                                <div class="form-group">
                                    <label for="{{$parameter['parameter_user_type_key']}}">{{ $parameter['name'] }}: @if($parameter['mandatory']) <span class="required-symbol">*</span> @endif</label>
                                    <select class="form-control" id="{{$parameter['parameter_user_type_key']}}" name="{{$parameter['parameter_user_type_key']}}" @if($parameter['mandatory']) required @endif>
                                        <option value="" selected>{{trans("PublicUser.selectOption")}}</option>
                                        @foreach($parameter['parameter_user_options'] as $option)
                                            <option value="{{$option['parameter_user_option_key']}}" @if($option['selected']) selected @endif>{{$option['name']}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @elseif($parameter['parameter_type_code'] == 'birthday')
                                {!! Form::oneDate($parameter['parameter_user_type_key'], $parameter['name'], ($parameter['value'] != '' ? $parameter['value'] : null), ['class' => 'form-control oneDatePicker', 'id' => $parameter['parameter_user_type_key'],'required' => ($parameter['mandatory'] == true ? 'required' : null)]) !!}
                            @endif
                        @endforeach
                    @endif
                    {!! $form->make() !!}
                </div>
            </div>
        </div>


        <!-- Update Password Modal -->
        <div class="modal fade" tabindex="-1" role="" id="changePasswordModal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ URL::action('PublicUsersController@updatePassword') }}" method="POST" class="">
                        <div class="panel panel-default panel-default-content">
                            <div class="panel-heading panel-default-heading">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>

                                <h4 class="panel-title">
                                    {{trans('defaultUser.change_password')}}
                                </h4>
                            </div>
                            <div class="panel-body">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                {!! Form::onePassword('old_password', trans('defaultUser.old_password'), null, ['class' => 'form-control', 'id' => 'old_password']) !!}
                                {!! Form::onePassword('password', trans('defaultUser.password'), null, ['class' => 'form-control', 'id' => 'password']) !!}
                                {!! Form::onePassword('password_confirmation', trans('defaultUser.password_confirmation'), null, ['class' => 'form-control', 'id' => 'password_confirmation']) !!}
                                <input class="btn btn-flat empatia" type="submit" value="{{trans('defaultUser.change_password_action_btn')}}" id="btn_change_password">
                            </div>
                        </div>
                    </form>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->


    </div>
@endsection


@section('scripts')
    <script src="{{ asset('vendor/jildertmiedema/laravel-plupload/js/plupload.full.min.js') }}"></script>
    <script src="{{ asset("js/cropper.min.js") }}"></script>
    <script src="{{ asset("js/canvas-to-blob.js") }}"></script>

    @include('private._private.functions') {{-- Helper Functions --}}
    <script>
        {!! ONE::imageUploader('bannerUploader', action('FilesController@upload'), 'imageFileUploaded', 'user-image', 'user-image-drop-zone', 'banner-list', 'user-image-drop-zone', 'getCroppedCanvasModal', 1, 2, isset($uploadKey) ? $uploadKey : "") !!}
        bannerUploader.init();

        updateClickListener();

    </script>
    <script>
        $( "#btn_change_password" ).click(function() {
            var old_pass = $('#old_password').val();
            var new_password = $('#password').val();
            var confirm_password = $('#password').val();
            if(old_pass == ''){
            }
            else if(new_password == ''){
            }
        });
    </script>
@endsection