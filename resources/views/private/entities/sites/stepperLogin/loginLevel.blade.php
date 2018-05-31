@extends('private._private.index')

@section('content')
    @php
    $form = ONE::form('siteLoginLevels', trans('privateLoginLevels.level_details'))
        ->settings(["model" => isset($loginLevel) ? $loginLevel : null, 'id' => isset($loginLevel) ? $loginLevel->level_parameter_key : null])
        ->show('LoginLevelsController@edit', 'LoginLevelsController@delete', ['levelKey' => isset($loginLevel) ? $loginLevel->level_parameter_key : null, 'siteKey' => isset($siteKey) ? $siteKey : null], 'LoginLevelsController@index', ['siteKey' => isset($siteKey) ? $siteKey : null])
        ->create('LoginLevelsController@store', 'LoginLevelsController@index')
        ->edit('LoginLevelsController@update', 'LoginLevelsController@show', ['levelKey' => isset($loginLevel) ? $loginLevel->level_parameter_key : null])
        ->open();
    @endphp

    <input type="hidden" name="siteKey" value="{{ isset($siteKey) ? $siteKey : null }}">
    {!! Form::oneText('name', trans('privateLoginLevels.level_name'), isset($loginLevel->name) ? $loginLevel->name : null, ['class' => 'form-control', 'id' => 'name']) !!}

    @if (ONE::actionType('siteLoginLevels') == 'show')
        {!! Form::oneText('position', trans('privateLoginLevels.level_position'), isset($loginLevel->position) ? $loginLevel->position : null, ['class' => 'form-control', 'id' => 'position']) !!}
    @endif
    {!! Form::oneSwitch("mandatory", trans('privateLoginLevels.level_mandatory'), isset($loginLevel->mandatory) ? $loginLevel->mandatory : null)!!}
    {!! Form::oneSwitch("manual_verification", trans('privateLoginLevels.level_manual_verification'), isset($loginLevel->manual_verification) ? $loginLevel->manual_verification : null) !!}
    {!! Form::oneSwitch("show_in_registration", trans('privateLoginLevels.level_show_in_registration'), isset($loginLevel->show_in_registration) ? $loginLevel->show_in_registration : null) !!}
    {!! Form::oneSwitch("sms_verification", trans('privateLoginLevels.level_sms_verification'), isset($loginLevel->sms_verification) ? $loginLevel->sms_verification : null) !!}

    {!! $form->make() !!}
@endsection

@section('scripts')
    <script>
        $(function() {
            var array = ["{{ $siteKey ?? null }}", "{{isset($loginLevel) ? $loginLevel->level_parameter_key : null}}"]
            getSidebar('{{ action("OneController@getSidebar") }}', 'details', array, 'loginLevelsParameters');
        });
    </script>
@endsection