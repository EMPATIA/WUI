@section('header_styles')
    <link href="{{ asset("css/cropper.min.css") }}" rel='stylesheet' type='text/css'>
@endsection

@extends('private._private.index')

@section('content')
    <!-- Form -->
    {!! Form::open(['action' => ['PublicUsersController@update', $user->user_key], 'method'  => 'put', 'update' => 'form','class'=>'form-horizontal']) !!}
    <div class="box box-primary">
        <div class="box-body">
            <div class="box-wrapper userProfile-box">
                <div class="row">
                    <div class="col-md-4 col-sm-12 col-12">
                        <div class="widget-user-header user-picture-box text-center" >
                            <div class="widget-user-image" >
                                @if($user->photo_id > 0)
                                    <img class="profile-img" src="{{ URL::action('FilesController@download', ['id' => $user->photo_id, 'code' => $user->photo_code, 1] )}}" alt="User profile picture" id="user-image-drop-zone" style="max-height: 200px">
                                @else
                                    <img class="profile-img" src="{{ asset('images/icon-user-default-160x160.png') }}" alt="User profile picture" id="user-image-drop-zone" style="max-height: 200px">
                                @endif
                            </div>
                        </div>
                        <!-- /.widget-user-image -->
                        <div class="top-buffer action-buttons d-flex justify-content-center">
                            <button id="user-image" class="btn btn-block center-block image-upload-profile"><i class="fa fa-upload"></i>&nbsp;{{ trans('user.change_profile_picture') }}</button>
                            {!! ONE::imageCropModal('getCroppedCanvasModal', 'getCroppedCanvasTitle', trans('user.image_resize')) !!}
                        </div>
                    </div>
                        <div class="col-md-8 col-sm-12 col-12">
                            <div class="form-group">
                                <div class="row">
                                    {!! Form::label('name', trans('user.name'), array('class' => 'col-md-2 col-12 label-required')) !!}
                                    <div class="col-md-10 col-12">{!! Form::text('name', $user->name,  array('required','class'=>'form-control')) !!}</div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    {!! Form::label('email', trans('user.email'), array('class' => 'col-md-2 col-12 label-required')) !!}
                                    <div class="col-md-10 col-12">{!! Form::text('email', $user->email,  array('required','class'=>'form-control', 'readonly' => 'readonly')) !!}</div>
                                </div>
                            </div>

                            @if(isset($registerParameters))
                                @foreach($registerParameters as $parameter)
                                    @if($parameter['parameter_type_code'] == 'text')
                                        <div class="form-group">
                                            <div class="row">
                                                {!! Form::label($parameter['name'], $parameter['name'], array('class' => 'col-md-2 col-12 ' .($parameter['mandatory'] == true ? 'required':null))) !!}
                                                <div class="col-md-10 col-12">{!! Form::text($parameter['parameter_user_type_key'], $parameter['value'],  array($parameter['mandatory'] == true ? 'required' : '' ,'class'=>'form-control')) !!}</div>
                                            </div>
                                        </div>
                                    @elseif($parameter['parameter_type_code'] == 'text_area')
                                        <div class="form-group">
                                            <div class="row">
                                                {!! Form::label($parameter['name'], $parameter['name'], array('class' => 'col-md-2 col-12 ' .($parameter['mandatory'] == true ? 'required':null))) !!}
                                                <div class="col-md-10 col-12">{!! Form::textarea($parameter['parameter_user_type_key'], $parameter['value'],  array('class'=>'form-control', 'required' => ($parameter['mandatory'] == true ? 'required' : null))) !!}</div>
                                            </div>
                                        </div>
                                    @elseif($parameter['parameter_type_code'] == 'numeric')
                                        <div class="form-group">
                                            <div class="row">
                                                {!! Form::label($parameter['name'], $parameter['name'], array('class' => 'col-md-2 col-12 ' .($parameter['mandatory'] == true ? 'required':null))) !!}
                                                <div class="col-md-10 col-12">{!! Form::number($parameter['parameter_user_type_key'], $parameter['value'],  array('class'=>'form-control', 'required' => ($parameter['mandatory'] == true ? 'required' : null))) !!}</div>
                                            </div>
                                        </div>
                                    @elseif($parameter['parameter_type_code'] == 'radio_buttons')
                                        @if(count($parameter['parameter_user_options'])> 0)
                                            <div class="form-group">
                                                <div class="row">
                                                    {!! Form::label($parameter['name'], $parameter['name'], array('class' => 'col-md-2 col-12 ' .($parameter['mandatory'] == true ? 'required':null))) !!}
                                                    <div class="col-md-10 col-12">
                                                        @foreach($parameter['parameter_user_options'] as $option)
                                                            <div class="radio">
                                                                <label>
                                                                    <input type="radio" name="{{$parameter['parameter_user_type_key']}}" id="{{$parameter['parameter_user_type_key']}}" value="{{$option['parameter_user_option_key']}}" @if($parameter['mandatory']) required @endif @if($option['selected']) checked @endif>{{$option['name']}}
                                                                </label>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    @elseif($parameter['parameter_type_code'] == 'check_box')
                                        @if(count($parameter['parameter_user_options'])> 0)
                                            <div class="form-group">
                                                <div class="row">
                                                    {!! Form::label($parameter['name'], $parameter['name'], array('class' => 'col-md-2 col-12 ' .($parameter['mandatory'] == true ? 'required':null))) !!}
                                                    <div class="col-md-10 col-12">
                                                        @foreach($parameter['parameter_user_options'] as $option)
                                                            <div class="checkbox">
                                                                <label><input type="checkbox" value="{{$option['parameter_user_option_key']}}" name="{{$parameter['parameter_user_type_key']}}[]" id="{{$parameter['parameter_user_type_key']}}" @if($parameter['mandatory']) required @endif @if($option['selected']) checked @endif>{{$option['name']}}</label>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    @elseif($parameter['parameter_type_code'] == 'dropdown')
                                        <div class="form-group">
                                            <div class="row">
                                                {!! Form::label($parameter['name'], $parameter['name'], array('class' => 'col-md-2 col-12 ' .($parameter['mandatory'] == true ? 'required':null))) !!}
                                                <div class="col-md-10 col-12">
                                                    <select class="form-control" id="{{$parameter['parameter_user_type_key']}}" name="{{$parameter['parameter_user_type_key']}}" @if($parameter['mandatory']) required @endif>
                                                        <option value="" selected>{{trans("user.select_option")}}</option>
                                                        @foreach($parameter['parameter_user_options'] as $option)
                                                            <option value="{{$option['parameter_user_option_key']}}" @if($option['selected']) selected @endif>{{$option['name']}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    @elseif($parameter['parameter_type_code'] == 'birthday')
                                        <div class="form-group">
                                            <div class="row">
                                                {!! Form::label($parameter['name'], $parameter['name'], array('class' => 'col-md-2 col-12 ' .($parameter['mandatory'] == true ? 'required':null))) !!}
                                                <div class="col-md-10 col-12">
                                                    <div class="input-group date">
                                                        <span class="input-group-addon"><i class="glyphicon glyphicon-th"></i></span>
                                                        <input class="form-control oneDatePicker" id="{!! $parameter['parameter_user_type_key'] !!}" {!! $parameter['mandatory'] == true ? 'required' : null !!} placeholder="{!! \Carbon\Carbon::now()->format('Y-m-d') !!}" data-date-format="yyyy-mm-dd" name="{!! $parameter['parameter_user_type_key'] !!}" value="{!! $parameter['value'] !!}" type="text">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            @endif

                        <!-- reset password -->
                            <div class="form-group  top-buffer">
                                <div class="row">
                                    {!! Form::label('password', trans('user.password') , array('class' => 'col-md-2 col-12')) !!}
                                    <div class="col-md-10 col-12">
                                        <a data-toggle="modal" data-target="#changePasswordModal" class="usr-change-pass">{{trans('user.reset_password')}}</a>
                                    </div>
                                </div>
                            </div>
                            <!-- //reset password -->



                            <!-- // submit and return buttons -->


                        </div>
                </div>


            </div>
        </div>
    </div>
    <!-- submit and return buttons -->

    <div class="action-btn-container ">
        <a href="{!! url('/')!!}"
           class="btn btn-secondary btn-flat back-btn"><i class="fa fa-chevron-left" aria-hidden="true"></i>{{trans('user.back')}}</a>

        {!! Form::submit(trans('user.save'),
          array('class'=>'btn btn-saveAll')) !!}
    </div>

    {!! Form::close() !!}

    <!-- Update Password Modal -->
    <div class="modal fade" tabindex="-1" role="" id="changePasswordModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="card">
                    <div class="card-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4>{{trans('user.change_password')}}</h4>
                    </div>
                    <div class="card-body">
                        <div class="default-padding">
                            {!! Form::open(['action' => ['PublicUsersController@updatePassword', $user->user_key], 'method'  => 'POST']) !!}
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <div class="form-group has-feedback">
                                <label for="old_password">{{ trans('user.old_password') }}</label>
                                <input type="password" name="old_password" class="form-control" autofocus>
                            </div>
                            <div class="form-group has-feedback">
                                <label for="old_password">{{ trans('user.password') }}</label>
                                <input type="password" name="password" class="form-control">
                            </div>
                            <div class="form-group has-feedback">
                                <label for="old_password">{{ trans('user.password_confirmation') }}</label>
                                <input type="password" name="password_confirmation" class="form-control">
                            </div>
                            <br>
                            <div class="btn-change-password-wrapper">
                                <input class="btn btn-submit pull-right" type="submit" value="{{trans('user.change_password_action_btn')}}" id="btn_change_password">
                            </div>
                            {!! Form::close() !!}
                        </div>
                    </div>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

@endsection


@section('scripts')
    <!-- Plupload Javascript fix and bootstrap fix @ start -->
    <link rel="stylesheet" href="/bootstrap/plupload-fix/bootstrap.css">
    <script src="/bootstrap/plupload-fix/bootstrap.min.js"></script>
    <!-- Plupload Javascript fix and bootstrap fix @ End -->
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

        $("form :input").change(function() {
            console.log($(this).closest('form').serialize());
        });
    </script>
@endsection
