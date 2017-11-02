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
    <div class="card flat topic-data-header">
        <p><label for="contentStatusComment" >{{trans('privateCbs.pad')}}</label>  {{$cb->title}}</p>
        @if(!empty($cbAuthor))
        <p><label for="contentStatusComment" >{{trans('privateCbs.author')}}</label>
            <a href="{{action('UsersController@show', ['userKey' => $cbAuthor->user_key, 'role' => $cbAuthor->role ?? null])}}">{{$cbAuthor->name}}</a>
        </p>
        @endif
        <p><label for="contentStatusComment" >{{trans('privateCbs.start_date')}}</label>  {{$cb->start_date}}</p>
    </div>

    <div class="" style="margin-top: 25px">
        @php
        $form = ONE::form('cbsNotifications', trans('privateTopic.notifications'), 'cb', 'notifications')
                ->settings(["model" => isset($topic) ? $topic : null, 'id'=>isset($topic) ? $topic->topic_key : null])
                ->show('CbsController@editNotifications', null,
                        ['type' => isset($type) ? $type : null, 'cbKey' =>isset($cb) ? $cb->cb_key : null],
                        null)
                ->edit('CbsController@update', 'CbsController@showNotifications',
                        ['type' => $type,'cbKey' =>isset($cb) ? $cb->cb_key : null, 'notifications_flag' => 1])
                ->open();
        @endphp

        {!! Form::hidden('title', isset($cb) ? $cb->title : null) !!}
        {!! Form::hidden('description', isset($cb) ? $cb->contents : null) !!}
        {!! Form::hidden('start_date', isset($cb) ? $cb->start_date : date('Y-m-d')) !!}
        {!! Form::hidden('end_date', isset($cb) && $cb->end_date!=null ? $cb->end_date  : '') !!}
        {!! Form::hidden('cb_key', isset($cb) ? $cb->cb_key : 0, ['id' => 'cb_key']) !!}

        @if(Session::get('user_role') == 'admin' || ONE::verifyUserPermissionsShow('cb', 'notifications'))
                <!-- CB Configurations -->
        <div class="card flat">
            <div class="card-title" style="padding:10px">
                {{trans('privateCbs.notifications')}}
            </div>
            <div class="card-body">
                {{-- followers notifications--}}
                @foreach($configurations as $configuration)
                    {{-- WAITING FOR SCRIPT DONT DELETE THIS --}}
                    @if($configuration->code == 'notification_deadline' || $configuration->code == 'notifications' || $configuration->code == 'notifications_owners' || $configuration->code=='notifications_topic')
                        <div class="card flat">
                            <div class="card-header">
                                <div class="col-12">
                                    <a class="collapsed" role="button" data-toggle="collapse"
                                       href="#collapse_{{$configuration->id}}" aria-expanded="false"
                                       aria-controls="collapse_{{$configuration->id}}">
                                        @if ($configuration->code == 'notification_deadline')
                                            {{trans('privateCbs.deadlineNotifications')}}
                                        @elseif($configuration->code == 'notifications')
                                            {{trans('privateCbs.followersNotifications')}}
                                        @elseif($configuration->code == 'notifications_owners')
                                            {{trans('privateCbs.ownersNotifications')}}
                                        @elseif($configuration->code == 'notifications_topic')
                                            {{trans('privateCbs.topicNotifications')}}
                                        @elseif($configuration->code == 'notifications_owners')
                                            {{trans('privateCbs.ownersNotifications')}}
                                        @else
                                            {{  $configuration->title }}
                                        @endif
                                    </a>
                                </div>
                            </div>
                            <div id="collapse_{{$configuration->id}}" class="panel-collapse collapse show"
                                 role="tabpanel">
                                <div class="card-body">
                                    @foreach($configuration->configurations as $option)
                                        <div class="row">
                                            <div class="cols-6 col-sm-6 col-md-2 col-lg-2">
                                                {!! Form::oneSwitch('notif['.$configuration->code.'][]',$option->title, in_array($option->id, (isset($cbConfigurations[$option->code]) ? array_keys($cbConfigurations[$option->code]) : []) ) , ["readonly" => ONE::actionType('cbsNotifications') == 'show' ? true : false, "groupClass"=>"row", "labelClass" => "col-12", "switchClass" => "col-12", "value" => $option->id, "id" => "configuration_".$option->id] ) !!}
                                            </div>
                                            <div class="col-6 col-sm-6 col-md-3 col-lg-4">
                                                @if($cbTemplates->has($option->code))
                                                    <a type="" style="margin-top:25px"
                                                       class="btn btn-flat btn-submit"
                                                       href="{{action('CbsController@showNotificationEmailTemplate',['type' =>$type,'cbKey'=>$cb->cb_key, 'configuration_code'=>$option->code])}}">{{trans('privateCbs.updateEmailTemplate')}}</a>
                                                @else
                                                    <a type="button" style="margin-top:25px"
                                                       class="btn btn-flat btn-success"
                                                       href="{{action('CbsController@createNotificationEmailTemplate',['type' =>$type,'cbKey'=>$cb->cb_key, 'configuration_code'=>$option->code, 'notification_type_code' => 'generic_cb_notifications'])}}">{{trans('privateCbs.createEmailTemplate')}}</a>
                                                @endif
                                            </div>
                                            @if($configuration->code=='notifications_topic')
                                                {{-- groups --}}
                                                @if($loop->iteration == 1)
                                                    <div class="col-12 col-sm-12 col-md-7 col-lg-6">
                                                        <div class="group-email-title">
                                                            {{trans('privateCbs.groups')}}
                                                        </div>
                                                        <select id="{{$option->id}}" class="form-control groups"
                                                                name="groups[]" multiple="multiple"
                                                                @if (ONE::actionType('cbsNotifications') == "show") disabled @endif>
                                                            @foreach($groups as $group)
                                                                <option value="{{$option->id.'_'.$group->entity_group_key}}"
                                                                        @if(in_array($group->entity_group_key, isset($cbConfigurations[$option->code][$option->id]) ? $cbConfigurations[$option->code][$option->id] : [])) selected @endif >{{$group->name}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                @else
                                                    <div class="col-12 col-sm-12 col-md-7 col-lg-6 group-email">
                                                        <select id="{{$option->id}}" class="form-control groups"
                                                                name="groups[]" multiple="multiple"
                                                                @if(ONE::actionType('cbsNotifications') == "show") disabled @endif>
                                                            @foreach($groups as $group)
                                                                <option value="{{$option->id.'_'.$group->entity_group_key}}"
                                                                        @if(in_array($group->entity_group_key, isset($cbConfigurations[$option->code][$option->id]) ? $cbConfigurations[$option->code][$option->id] : [])) selected @endif>{{$group->name}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                @endif
                                            @endif
                                            @if($configuration->code == 'notification_deadline')
                                                <div class="col-12 col-sm-12 col-md-2">
                                                    {!! Form::oneText('deadline', trans('privateCbs.deadline'),in_array($option->id, (isset($cbConfigurations[$option->code][$option->id]) ? array_keys($cbConfigurations[$option->code]) : []) ) ? $cbConfigurations[$option->code][$option->id] : null, ['class' => 'form-control', 'id' => 'deadline']) !!}
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif
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