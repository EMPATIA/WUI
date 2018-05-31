@extends('private._private.index')

@section('header_styles')
    <style>
        .group-email{
            margin-top: 25px;
        }
        .group-email-title{
            margin-bottom: 5px;
        }
    </style>
@endsection

@section('content')
    <div class="row">
    @php
    $form = ONE::form('entityNotifications', trans('privateTopic.notifications'), 'cb', 'notifications')
        ->settings(["model" => isset($topic) ? $topic : null, 'id'=>isset($topic) ? $topic->topic_key : null])
        ->show('EntitiesController@editNotifications', null,
            ['entityKey' => $entityKey ?? null],
            null)
        ->edit('EntitiesController@updateNotifications', 'EntitiesController@showNotifications',
            ['entityKey' => $entityKey ?? null])
        ->open();
    @endphp

    @if(ONE::actionType('entityNotifications') == 'edit')
        {{ method_field('PUT') }}
    @endif

    @if(Session::get('user_role') == 'admin')
        <!-- CB Configurations -->
            <div class="card flat">
                <div class="box-header">
                    <h3 class="box-title">{{trans('privateEntity.notifications')}}</h3>
                </div>
                <div class="card-body">
                    @foreach($notificationTypes as $notificationCode => $notificationType)
                        <div class="row">
                            <div class="col-6 col-sm-6 col-md-3 margin-bottom-20">
                                {!! Form::oneSwitch('notifications[]',$notificationType->value, (array_key_exists($notificationCode, (array) $entityNotifications) && $entityNotifications->{$notificationCode}->active) , ["readonly" => ONE::actionType('entityNotifications') == 'show' ? true : false, "value" => $notificationCode, "id" => "notification_".$notificationCode] ) !!}
                            </div>
                            <div class="col-6 col-sm-6 col-md-3 margin-bottom-20">
                                <label class="form-control-label">{{trans('privateEntity.notification_email_template')}}</label>
                                @if(isset($entityNotifications->{$notificationCode}->template_key))
                                    <a type="button" style="width: 100%" class="btn btn-flat btn-info" href="{{action('EntitiesController@showEntityNotificationTemplate',[$entityKey ?? null, $notificationCode, 'template_key' => $entityNotifications->{$notificationCode}->template_key])}}">{{trans('privateEntity.editEntityNotificationTemplate')}}</a>
                                @else
                                    <a type="button" style="width: 100%" class="btn btn-flat btn-success" href="{{action('EntitiesController@createEntityNotificationTemplate',[$entityKey ?? null, $notificationCode])}}">{{trans('privateEntity.createEntityNotificationTemplate')}}</a>
                                @endif
                            </div>
                            <div class="col-12 col-sm-12 col-md-6 margin-bottom-20">
                                <label class="form-control-label">{{trans('privateEntity.groups')}}</label>
                                <select id="{{$notificationCode}}" class="form-control groups" name="groups[{{$notificationCode}}][]" multiple="multiple" @if(ONE::actionType('entityNotifications') != "edit") disabled @endif>
                                    @foreach($groups as $group)
                                        <option value="{{$group->id}}" @if(in_array($group->id, !empty($entityNotifications->{$notificationCode}->groups) ? json_decode($entityNotifications->{$notificationCode}->groups) : [])) selected @endif>{{$group->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
    {!! $form->make() !!}
@endsection

@section('scripts')
    <script>
        $(".groups").select2();
    </script>
@endsection