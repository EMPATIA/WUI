{{--{{ dd($collection) }}--}}
<div class="dashboard-scrollable">
    <div class='dashboard-item-wrapper'>
        <div class='row'>
            <div class='col-12 col-md-4 col-lg-5 ellipis'><b>{{ trans('private.name') }}</b></div>
            <div class='col-12 col-md-4 col-lg-4 ellipis'><b>{{ trans('private.email') }}</b></div>
            <div class='col-12 col-md-4 col-lg-3'><b>{{ trans('private.created_at') }}</b></div>
        </div>
    </div>

    @foreach(!empty($users) ? $users :[] as $user)
    <div class='dashboard-item-wrapper'>
                <div class='row'>
            <div class='col-12 col-md-4 col-lg-5 ellipis'>
                <div class='dashboard-text ellipis'>
                    <a href="{{ action("UsersController@showUserMessages",$user->user_key) }}">
                        {{ $user->name }}
                    </a>
                </div>
            </div>
            <div class='col-12 col-md-4 col-lg-4 ellipis'>
                <div class='dashboard-text ellipis'>
                    <a href="{{ action("UsersController@showUserMessages",$user->user_key) }}">
                        {{ $user->email }}
                    </a>
                </div>
            </div>
            <div class='col-12 col-md-4 col-lg-3 ellipis'>
                <div class='dashboard-text ellipis'>
                    <a href="{{ action("UsersController@showUserMessages",$user->user_key) }}">
                        {{ $user->created_at }}
                    </a>
                </div>
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
            <a href="{{ action("EntityMessagesController@showMessagesTable","receivedMessages") }}" class="btn-seemore pull-right">{{ trans('private.view_full_list') }}</a>
        </div>
    </div>
</div>

