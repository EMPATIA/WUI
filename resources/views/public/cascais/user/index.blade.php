{{-- Title --}}
@if($profileSection == 'messages')
    @php
        $demoPageTitle = ONE::transSite("user_index_messages_title");
    @endphp
@elseif($profileSection =='topics' )
    @php
        $demoPageTitle = ONE::transSite("user_index_topics_title");
    @endphp
@endif


@extends('public.default._layouts.index')
{{--@section('header_styles')--}}
{{--<link rel="stylesheet" href="{{ asset('css/switch-button.css')}}">--}}
{{--@endsection--}}
@section('header_styles')
@include('public.default.user.cssOverrides')
<style>
table{
    color: black;
}
td{
    width: 100%;
}

.title{
    width: 60%!important;
}
.user-activity-padding{
    padding-bottom: 50px;
}

.user-activity{
    padding-bottom: 70px;
    min-height: 400px;
}

.user-activity-tabs{
}

.user-activity-tabs ul{
    height: 30px;
}

.user-activity-tabs .nav-tabs{
    border:none;
}

.user-activity-tabs .nav-item .nav-link{
    height: 30px;
    text-align: center;
    padding: 0 15px;
    line-height: 30px;
    border:none;
    border-radius: 0;
    color:#fff;
}

.user-activity-tabs .nav-item .nav-link:hover{
    background-color: #7fb940;
    color: #fff;
}


.user-activity-tabs .nav-item .nav-link.active,
.user-activity-tabs .nav-item .nav-link.active:hover{
    color: #7fb940;
    background-color: #fff;
    font-weight: 600;
}

.activity-table{
    padding: 0 15px 20px 15px;
    margin-top: 0px!important;
}

.activity-table table{
    font-size: 0.9rem;
}

.table-hover tbody tr:hover{
    cursor: pointer;
    background-color: #7fb940;
    color:#fff;
}

.activity-table table thead tr > th{
    border:0;
}

.activity-table table tbody tr:first-child > td{
    /*border: none;*/
    border-top: solid 2px #7fb940;
}
</style>
@endsection

@section('content')

<div class="container-fluid personal-area-buttons">
<div class="row">
    <div class="col-12">
        <div class="container">
            <div class="row no-margin">
                <div class="offset-6 col-6 no-padding">
                    <div class="row buttons-margin">
                        <div class="col button">
                            <a href="{{ action("PublicUsersController@edit",["userKey"=>$user->user_key]) }}" @if(!isset($profileSection) || (isset($profileSection) && $profileSection=="about")) class="active" @endif>
                                {{ ONE::transSite("user_profile") }}
                            </a>
                        </div>
                        <div class="col button">
                            <a href="{{ action("PublicUsersController@showMessages") }}" @if(!isset($profileSection) || (isset($profileSection) && $profileSection=="messages")) class="active" @endif>
                                {{ ONE::transSite("user_messages") }}
                            </a>
                        </div>
                        <div class="col button">
                            <a href="{{ action("PublicUsersController@userTopics") }}" @if(!isset($profileSection) || (isset($profileSection) && $profileSection=="topics")) class="active" @endif>
                                {{ ONE::transSite("user_participation") }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

@if($profileSection == 'about')
<div class="container">
    <div class="row align-items-end idea-topic-title">
        <div clasS="col title">
            <span>{{ ONE::transSite("user_profile") }}</span>
            {{--  <a href="#">{{ ONE::transSite("back') }}</a>  --}}
        </div>
    </div>
</div>
<div class="container-fluid @if(ONE::isEdit()) light-grey-bg user-profile @else user-profile @endif">
    <div class="row">
        <div class="col-12">
            <div class="container">
                <div class="row">

                    @include('public.default.user.user')
                    @if(!ONE::isEdit())<div class="col-lg-3 offset-lg-1 col-md-4 files-col">
                        <div class="button-container secondary-color smaller">
                            <a data-toggle="modal" data-target="#changePasswordModal" href="#" id="change-password">
                                {{ ONE::transSite("user_change_password") }}
                            </a>
                        </div>
                        <div class="photo-box">
                            <div class="box image-div" style="background-image: url('{{URL::action('FilesController@download', ['id' => $user->photo_id, 'code' => $user->photo_code, 1] )}}')">
                            </div>

                            <!-- ########## If user doesn't have photo ########### -->

                        </div>
                        <div class="button-container secondary-color smaller">
                            @if($user->photo_id > 0)
                                {!! ONE::imageButtonUpload("user-image","", "fa fa-upload") !!}
                            @else
                                <div class="upload-profile-picture">
                                    {!! ONE::imageButtonUpload("user-image","", "fa fa-upload") !!}
                                </div>
                            @endif
                            {!! ONE::imageCropModal('getCroppedCanvasModal', 'getCroppedCanvasTitle', ONE::transSite("user_imageResize")) !!}
                            {{--<a href="#">--}}
                            {{--<i class="fa fa-upload" aria-hidden="true"></i> Change photo--}}
                            {{--</a>--}}
                        </div>

                        <div class="button-container primary-color bigger">
                            <a href="#">
                                <i class="fa fa-facebook" aria-hidden="true"></i>
                                <p>{{ ONE::transSite("user_link_facebook_account") }}</p>
                            </a>
                        </div>
                    </div>

                    @endif

                </div>
            </div>
        </div>
    </div>
</div>
@elseif($profileSection == 'messages')
@include('public.default.user.messages')
@else
@include('public.default.user.topics')
@endif

<div class="modal fade" id="changePasswordModal">
<div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">{{ ONE::transSite("user_change_password") }}</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            {!! Form::open(['action' => ['PublicUsersController@updatePassword', $user->user_key], 'method'  => 'POST']) !!}
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <div class="form-group has-feedback">
                <label for="old_password">{{ ONE::transSite("user_old_password") }}</label>
                <input type="password" name="old_password" class="form-control" title="old_password" autofocus required>
            </div>
            <div class="form-group has-feedback">
                <label for="password">{{ ONE::transSite("user_password") }}</label>
                <input type="password" name="password" class="form-control" title="password" required>
            </div>
            <div class="form-group has-feedback">
                <label for="password_confirmation">{{ ONE::transSite("user_password_confirmation") }}</label>
                <input type="password" name="password_confirmation" class="form-control"  title="password_confirmation" required>
            </div>
            <input class="btn btn-success pull-right change-password" type="submit" value="{{ONE::transSite("user_change_password_action_btn")}}" id="btn_change_password">
            {!! Form::close() !!}
        </div>
    </div>
</div>
</div>

<script src="{{ asset('vendor/jildertmiedema/laravel-plupload/js/plupload.full.min.js') }}"></script>
<script src="{{ asset("js/cropper.min.js") }}"></script>
<script src="{{ asset("js/canvas-to-blob.js") }}"></script>
@include('private._private.functions') {{-- Helper Functions --}}

<script>
{!! ONE::fileUploader('bannerUploader', action('FilesController@upload'), 'imageFileUploaded', 'user-image', 'user-image-drop-zone', 'banner-list', 'user-image-drop-zone', 2,isset($uploadKey) ? $uploadKey : "", ["images"], true) !!}
bannerUploader.init();

updateClickListener();
</script>
@endsection