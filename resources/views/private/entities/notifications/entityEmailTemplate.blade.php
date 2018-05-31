@extends('private._private.index')

@section('content')
    <div class="row">
        @php
        $form = ONE::form('entityNotificationTemplate')
            ->show('EntitiesController@editEntityNotificationTemplate', null, [$entityKey, $notificationCode, 'template_key' => $templateKey ?? null], 'EntitiesController@showNotifications', [$entityKey])
            ->edit('EntitiesController@updateEntityNotificationTemplate', 'EntitiesController@showEntityNotificationTemplate', [$entityKey, $notificationCode, 'template_key' => $templateKey ?? null])
            ->create('EntitiesController@storeEntityNotificationTemplate', 'EntitiesController@showNotifications', [$entityKey, $notificationCode])
            ->open();
        @endphp

        @if(ONE::actionType('entityNotificationTemplate') == 'edit')
            {{ method_field('PUT') }}
        @endif

        @if(Session::get('user_role') == 'admin')
            @if(count($languages) > 0)
                @foreach($languages as $language)
                    @php $form->openTabs('tab-translation-'.$notificationCode.'-' . $language->code, $language->name); @endphp
                    <div style="padding:10px;">
                        {!! Form::oneText('subject_'.$language->code, trans('privateEmails.subject'), !empty($translations[$notificationCode]->{$language->code}->subject) ? $translations[$notificationCode]->{$language->code}->subject : null, ['class' => 'form-control', 'id' => 'subject_'.$language->code]) !!}

                        {!! Form::oneTextArea('content_'.$language->code,
                        trans('privateEmails.content'),
                        !empty($translations[$notificationCode]->{$language->code}->content) ? $translations[$notificationCode]->{$language->code}->content : null,
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
        @if(ONE::actionType('entityNotificationTemplate') != "show")
        $(document).ready(function(){
            {!! ONE::addTinyMCE(".mcEdit", ['action' => action('ContentManagerController@getTinyMCE')]) !!}
        });
        @endif
    </script>
@endsection