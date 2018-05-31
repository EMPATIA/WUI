@extends('public.empaville._layouts.index')

@section('content')

    <div class="row">
        <div class="col-md-3">

            <!-- Profile Image -->
            <div class="box" style="border-top-color: #737373;">
                <div class="box-body box-profile">
                    @if($user->photo_id > 0)
                        <img class="profile-user-img img-responsive img-circle" src="{{URL::action('FilesController@download', ['id' => $user->photo_id, 'code' => $user->photo_code, 1] )}}" alt="User profile picture">
                    @else
                        <img class="profile-user-img img-responsive img-circle" src="{{ asset('images/icon-user-default-160x160.png') }}" alt="User profile picture">
                    @endif
                    <h3 class="profile-username text-center">{{$user->name}}</h3>
                    <p class="text-muted text-center">{{$user->job}}</p>
                    {{--<ul class="list-group list-group-unbordered">--}}
                        {{--<li class="list-group-item">--}}
                            {{--<b>Followers</b> <a class="pull-right">1,322</a>--}}
                        {{--</li>--}}
                        {{--<li class="list-group-item">--}}
                            {{--<b>Following</b> <a class="pull-right">543</a>--}}
                        {{--</li>--}}
                        {{--<li class="list-group-item">--}}
                            {{--<b>Friends</b> <a class="pull-right">13,287</a>--}}
                        {{--</li>--}}
                    {{--</ul>--}}
                    <!--a href="#" class="btn btn-primary btn-block"><b>Follow</b></a-->
                    <a href="{{ action('PublicUsersController@edit', ['userKey' => $user->user_key,'f' => 'user'])}}" class="btn empatia btn-block"><b>{{trans('PublicUser.editMyProfile')}}</b></a>
                </div>
                <!-- /.box-body -->
            </div>
            <!-- /.box -->

            {{--<div class="box" style="border-top-color: #737373;">--}}
                {{--<div class="box-header with-border">--}}
                    {{--<h3 class="box-title">Friends</h3>--}}

                    {{--<div class="box-tools pull-right">--}}
                        {{--<span class="label label-danger">8 New Friends</span>--}}
                    {{--</div>--}}
                {{--</div>--}}
                {{--<!-- /.box-header -->--}}
                {{--<div class="box-body no-padding">--}}
                    {{--<ul class="users-list clearfix">--}}
                        {{--<li style="width:33%;padding: 20px;">--}}
                            {{--<img src="/images/icon-user-default-160x160.png" alt="User Image">--}}
                            {{--<a class="users-list-name" href="#">Alex</a>--}}
                        {{--</li>--}}
                        {{--<li style="width:33%;padding: 20px;">--}}
                            {{--<img src="/images/icon-user-default-160x160.png" alt="User Image">--}}
                            {{--<a class="users-list-name" href="#">Sarah</a>--}}
                        {{--</li>--}}
                        {{--<li style="width:33%;padding: 20px;">--}}
                            {{--<img src="/images/icon-user-default-160x160.png" alt="User Image">--}}
                            {{--<a class="users-list-name" href="#">Jane</a>--}}
                        {{--</li>--}}
                        {{--<li style="width:33%;padding: 20px;">--}}
                            {{--<img src="/images/icon-user-default-160x160.png" alt="User Image">--}}
                            {{--<a class="users-list-name" href="#">John</a>--}}
                        {{--</li>--}}
                        {{--<li style="width:33%;padding: 20px;">--}}
                            {{--<img src="/images/icon-user-default-160x160.png" alt="User Image">--}}
                            {{--<a class="users-list-name" href="#">Alex</a>--}}
                        {{--</li>--}}
                        {{--<li style="width:33%;padding: 20px;">--}}
                            {{--<img src="/images/icon-user-default-160x160.png" alt="User Image">--}}
                            {{--<a class="users-list-name" href="#">Sarah</a>--}}
                        {{--</li>--}}
                    {{--</ul>--}}
                    {{--<!-- /.users-list -->--}}
                {{--</div>--}}
                {{--<!-- /.box-body -->--}}
                {{--<div class="box-footer text-center">--}}
                    {{--<a href="" class="uppercase">View All Users</a>--}}
                {{--</div>--}}
                {{--<!-- /.box-footer -->--}}
            {{--</div>--}}

        </div>

        <!-- /.col -->
        <div class="col-md-9">
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#settings" data-toggle="tab">{{trans('PublicUser.aboutMe')}}</a></li>
                    <li><a href="#activity" data-toggle="tab">{{trans('PublicUser.activity')}}</a></li>
                    <li><a href="#timeline" data-toggle="tab">{{trans('PublicUser.timeline')}}</a></li>
                </ul>

                <div class="tab-content">
                    <div class="active tab-pane" id="settings">
                        <strong><i class="fa fa-inbox margin-r-5"></i> {{trans('PublicUser.email')}}</strong>
                        <p class="text-muted" style="padding-left: 10px;">
                            {{$user->email}}
                        </p><hr>
                        <strong><i class="fa fa-home margin-r-5"></i> {{trans('PublicUser.street')}}</strong>
                        <p class="text-muted" style="padding-left: 10px;">
                            {{$user->street}}
                        </p><hr>
                        <strong><i class="fa fa-map-marker margin-r-5"></i> {{trans('PublicUser.city')}}</strong>
                        <p class="text-muted" style="padding-left: 10px;">
                            @if(!empty($user->city))
                                {{$user->city}} , {{$user->country}}
                            @endif
                        </p><hr>
                        <strong><i class="fa fa-globe margin-r-5"></i> {{trans('PublicUser.nationality')}}</strong>
                        <p class="text-muted" style="padding-left: 10px;">
                            {{$user->nationality}}
                        </p><hr>
                        <strong><i class="fa fa-phone margin-r-5"></i> {{trans('PublicUser.mobileNumber')}}</strong>
                        <p class="text-muted" style="padding-left: 10px;">
                            {{$user->mobile_number}}
                        </p><hr>

                        <strong><i class="fa fa-bookmark margin-r-5"></i> {{trans('PublicUser.gender')}}</strong>
                        <p class="text-muted" style="padding-left: 10px;">
                            {{$user->gender}}
                        </p><hr>

                        <strong><i class="fa fa-bookmark margin-r-5"></i> {{trans('PublicUser.homePage')}}</strong>
                        <p class="text-muted" style="padding-left: 10px;">
                            {{$user->homepage}}
                        </p><hr>


                    </div>
                    <!-- /.tab-pane -->

                    <div class="tab-pane" id="activity">
                        <!-- Post -->
                        <div class="post">
                            <div class="user-block">
                                <img class="img-circle img-bordered-sm" src="{{ asset('images/icon-user-default-160x160.png') }}" alt="user image">
                        <span class="username">
                          <a href="#">Jonathan Burke Jr.</a>
                          <a href="#" class="pull-right btn-box-tool"><i class="fa fa-times"></i></a>
                        </span>
                                <span class="description">Shared publicly - 7:30 PM today</span>
                            </div>
                            <!-- /.user-block -->
                            <p>
                                Lorem ipsum represents a long-held tradition for designers,
                                typographers and the like. Some people hate it and argue for
                                its demise, but others ignore the hate as they create awesome
                                tools to help create filler text for everyone from bacon lovers
                                to Charlie Sheen fans.
                            </p>
                            <ul class="list-inline">
                                <li><a href="#" class="link-black text-sm"><i class="fa fa-share margin-r-5"></i> Share</a></li>
                                <li><a href="#" class="link-black text-sm"><i class="fa fa-thumbs-o-up margin-r-5"></i> Like</a>
                                </li>
                                <li class="pull-right">
                                    <a href="#" class="link-black text-sm"><i class="fa fa-comments-o margin-r-5"></i> Comments
                                        (5)</a></li>
                            </ul>

                            <input class="form-control input-sm" type="text" placeholder="Type a comment">
                        </div>
                        <!-- /.post -->

                        <!-- Post -->
                        <div class="post clearfix">
                            <div class="user-block">
                                <img class="img-circle img-bordered-sm" src="{{ asset('images/icon-user-default-160x160.png') }}" alt="User Image">
                        <span class="username">
                          <a href="#">Sarah Ross</a>
                          <a href="#" class="pull-right btn-box-tool"><i class="fa fa-times"></i></a>
                        </span>
                                <span class="description">Sent you a message - 3 days ago</span>
                            </div>
                            <!-- /.user-block -->
                            <p>
                                Lorem ipsum represents a long-held tradition for designers,
                                typographers and the like. Some people hate it and argue for
                                its demise, but others ignore the hate as they create awesome
                                tools to help create filler text for everyone from bacon lovers
                                to Charlie Sheen fans.
                            </p>

                            <form class="form-horizontal">
                                <div class="form-group margin-bottom-none">
                                    <div class="col-sm-9">
                                        <input class="form-control input-sm" placeholder="Response">
                                    </div>
                                    <div class="col-sm-3">
                                        <button type="submit" class="btn btn-danger pull-right btn-block btn-sm">Send</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <!-- /.post -->


                    </div>
                    <!-- /.tab-pane -->
                    <div class="tab-pane" id="timeline">
                        <!-- The timeline -->
                    </div>
                    <!-- /.tab-pane -->
                </div>
                <!-- /.tab-content -->
            </div>
            <!-- /.nav-tabs-custom -->
        </div>

        <!-- /.col -->
    </div>
@endsection