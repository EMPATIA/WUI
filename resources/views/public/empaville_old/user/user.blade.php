@section('header_styles')
    <link href="{{ asset("css/cropper.min.css") }}" rel='stylesheet' type='text/css'>
@endsection

@extends('public.empaville._layouts.index')
@section('content')
    <div class='row'>
        <div class="col-xs-offset-2 col-md-8">
            <!-- Form -->
            <?php
            $form = ONE::form('user')
                    ->settings(["model" => isset($user) ? $user : null,'id' => isset($user) ? $user->user_key : null])
                    ->show(null,null)
                    ->create(null,null)
                    ->edit('PublicUsersController@update', 'PublicUsersController@index',isset($user)? $user->user_key : null)
                    ->open();
            ?>
            <div class="box box-widget widget-user-2">
                <div class="widget-user-header" style="background-color: #62a351;">
                    <div class="box-tools pull-right">
                        {!! ONE::imageButtonUpload("user-image") !!}
                        {!! ONE::imageCropModal('getCroppedCanvasModal', 'getCroppedCanvasTitle', trans('PublicUser.imageResize')) !!}
                    </div>
                    <div class="widget-user-image">
                        @if($user->photo_id > 0)
                            <img class="img-circle" src="{{URL::action('FilesController@download', ['id' => $user->photo_id, 'code' => $user->photo_code, 1] )}}" alt="User profile picture" id="user-image-drop-zone">
                        @else
                            <img class="img-circle" src="{{ asset('images/icon-user-default-160x160.png') }}" alt="User profile picture" id="user-image-drop-zone">
                        @endif
                    </div>
                    <!-- /.widget-user-image -->
                    <h3 class="widget-user-username" style="color:white">{{$user->name}}</h3>
                    <h5 class="widget-user-desc" style="color:white">{{trans('PublicUser.profile')}}</h5>
                </div>
                <div class="box-body">
                    {!! Form::oneText('name', trans('PublicUser.name'), isset($user->name) ? $user->name : null, ['class' => 'form-control', 'id' => 'name', 'required' => 'required']) !!}
                    {!! Form::oneText('email', trans('PublicUser.email'), isset($user->email) ? $user->email : null, ['class' => 'form-control', 'id' => 'email', 'required' => 'required']) !!}
                    {!! Form::oneSelect('gender', trans('PublicUser.gender'), ['' => 'Select Gender', 'Male' => 'Male', 'Female'=> 'Female'], isset($user->gender) ? $user->gender : '', null, ['class' => 'form-control', 'id' => 'gender']) !!}
                    {!! Form::oneText('street', trans('PublicUser.street'), isset($user->street) ? $user->street : null, ['class' => 'form-control', 'id' => 'street']) !!}
                    {!! Form::oneText('city', trans('PublicUser.city'), isset($user->city) ? $user->city : null, ['class' => 'form-control', 'id' => 'city']) !!}
                    {!! Form::oneText('country', trans('PublicUser.country'), isset($user->country) ? $user->country : null, ['class' => 'form-control', 'id' => 'country']) !!}
                    {!! Form::oneText('nationality', trans('PublicUser.nationality'), isset($user->nationality) ? $user->nationality : null, ['class' => 'form-control', 'id' => 'nationality']) !!}
                    {!! Form::oneText('job', trans('PublicUser.job'), isset($user->job) ? $user->job : null, ['class' => 'form-control', 'id' => 'job']) !!}
                    {!! Form::oneText('mobile_number', trans('PublicUser.mobile_number'), isset($user->mobile_number) ? $user->mobile_number : null, ['class' => 'form-control', 'id' => 'mobile_number']) !!}
                    {!! Form::oneText('homepage', trans('PublicUser.homepage'), isset($user->homepage) ? $user->homepage : null, ['class' => 'form-control', 'id' => 'homepage']) !!}
                    {!! Form::onePassword('password', trans('user.password'), null, ['class' => 'form-control', 'id' => 'password']) !!}
                    {!! Form::onePassword('password_confirmation', trans('user.passwordConfirmation'), null, ['class' => 'form-control', 'id' => 'password_confirmation']) !!}
                </div>
                <!-- /.box-body -->
            </div>
            {!! $form->make() !!}
        </div>
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
@endsection