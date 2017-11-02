@extends('private._private.index')

@section('content')
    <div class="card flat topic-data-header">
        <p><label for="contentStatusComment" >{{trans('privateCbs.pad')}}</label>  {{$cb->title}}</p>
        <p><label for="contentStatusComment" >{{trans('privateCbs.author')}}</label>  {{$author}}</p>
        <p><label for="contentStatusComment">{{trans('privateCbs.start_date')}}</label>  {{$cb->start_date}}</p>
    </div>

    <div>
        @php
        $form = ONE::form('notificationTemplate')
            ->settings(["model" => isset($topic) ? $topic : null, 'id'=>isset($topic) ? $topic->topic_key : null])
            ->show('CbsController@editNotificationEmailTemplate', null,
                ['type' => $type,'cbKey' =>$cb->cb_key ?? null, 'configuration_code' => $config_code], 'CbsController@showNotifications', ['type' => $type,'cbKey' =>$cb->cb_key ?? null])
            ->edit('CbsController@updateNotificationEmailTemplate', 'CbsController@showNotifications',
                ['type' => $type,'cbKey' =>$cb->cb_key ?? null, 'configuration_code' => $config_code])
            ->create('CbsController@storeNotificationEmailTemplate', 'CbsController@showNotifications',
                ['type' => $type,'cbKey' =>$cb->cb_key ?? null, 'configuration_code' => $config_code])
            ->open();
        @endphp

        {!! Form::hidden('title', isset($cb) ? $cb->title : null) !!}
        {!! Form::hidden('description', isset($cb) ? $cb->contents : null) !!}
        {!! Form::hidden('start_date', isset($cb) ? $cb->start_date : date('Y-m-d')) !!}
        {!! Form::hidden('end_date', isset($cb) && $cb->end_date!=null ? $cb->end_date  : '') !!}
        {!! Form::hidden('cb_key', isset($cb) ? $cb->cb_key : 0, ['id' => 'cb_key']) !!}
        {!! Form::hidden('notification_type_code', $notificationTypeCode ?? null) !!}

        @if(ONE::actionType('notificationTemplate') == 'edit')
            {{ method_field('PUT') }}
        @endif

        @if(Session::get('user_role') == 'admin' || ONE::verifyUserPermissionsShow('cb', 'notifications'))
            @if(count($languages) > 0)
                @foreach($languages as $language)
                    @php $form->openTabs('tab-translation-'.$config_code.'-' . $language->code, $language->name); @endphp
                    <div style="padding:10px;">
                        {!! Form::oneText('subject_'.$language->code, trans('privateEmails.subject'), !empty($translations[$config_code]->{$language->code}->subject) ? $translations[$config_code]->{$language->code}->subject : null, ['class' => 'form-control', 'id' => 'subject_'.$language->code]) !!}

                        {!! Form::oneTextArea('content_'.$language->code,
                        trans('privateEmails.content'),
                        !empty($translations[$config_code]->{$language->code}->content) ? $translations[$config_code]->{$language->code}->content : null,
                            ['class' => 'form-control templates mcEdit', 'id' => 'content_'.$language->code]) !!}
                    </div>
                @endforeach
                @php $form->makeTabs(); @endphp
            @endif
    </div>
    @endif
    {!! $form->make() !!}
@endsection

@section('scripts')
    <script src="{{ asset("js/tinymce/tinymce.min.js") }}"></script>

    <script>
        @if(ONE::actionType('notificationTemplate') != "show")
        $(document).ready(function(){
            {!! ONE::addTinyMCE(".mcEdit", ['action' => action('ContentsController@getTinyMCE')]) !!}
        });
        @endif
    </script>
@endsection