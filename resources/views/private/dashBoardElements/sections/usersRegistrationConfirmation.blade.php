<div class="dashboard-scrollable">
    <div class='dashboard-item-wrapper'>
        {{--{{ dd($collection) }}--}}
        <div class='row'>
            <div class='col-12 col-md-4 col-lg-5 ellipis'><b>{{ trans('private.name') }}</b></div>
            <div class='col-12 col-md-4 col-lg-4 ellipis'><b>{{ trans('private.email') }}</b></div>
            <div class='col-12 col-md-4 col-lg-3'></div>
        </div>
    </div>

    @foreach(!empty($users) ? $users :[] as $user)
    <div class='dashboard-item-wrapper'>
        <div class='row'>
            <div class='col-12 col-md-4 col-lg-5 ellipis'>
                <div class='dashboard-text ellipis'>
                    <a href="{{action('UsersController@show', ['user_key' => $user->user_key, 'role' => 'user','moderation' => true])}}">{{ $user->name }}</a>
                </div>
            </div>
            <div class='col-12 col-md-4 col-lg-4 ellipis'>
                <div class='dashboard-text ellipis'>
                    {{ $user->email }}
                </div>
            </div>
            <div class='col-12 col-md-4 col-lg-3 ellipis'>
                <a class='btn-green pull-right' onclick="alert('Not implemented yet!')">
                    <i class='fa fa-check' aria-hidden=true'></i> Confirm <!-- {{  trans('privateDashboard.confirm') }} -->
                </a>
                <!-- ?!?!?!?!?!?! check UsersController@tableUsersCompleted -->
                @if(!empty($user->login_levels)) {
                    @foreach ($user->login_levels as $login_level) {
                        <a href='{{ action('UsersController@manualCheckLoginLevel', ['userKey' => $user->user_key, 'login_level_key' => $login_level->key]) }}'
                          class='manual-login-level btn btn-success btn-sm btn-block text-left'>
                            <i class='glyphicon glyphicon-thumbs-up'></i> {!! $login_level->name !!}
                        </a>;
                    @endforeach
                @endif
            </div>
        </div>
    </div>
    @endforeach

    @if(empty($users))
        @include("private.dashBoardElements.sections._emptyListMessage")
    @endif
</div>

<div class="view_full_list">
    <div class="row">
        <div class='col-12'>
            <a href="" class="btn-seemore pull-right">{{ trans('private.view_full_list') }}</a>
        </div>
    </div>
</div>