@extends('private._private.index')

@section('content')
    @php
    $form = ONE::form('entityLoginLevels', trans('privateEntityLoginLevels.login_level_details'))
        ->settings(["model" => $loginLevel ?? null, 'id' => $loginLevel->login_level_key ?? null])
        ->show('EntityLoginLevelsController@edit', 'EntityLoginLevelsController@delete', ['levelKey' => $loginLevel->login_level_key ?? null, 'entityKey' => $entityKey ?? null], null)
        ->create('EntityLoginLevelsController@store', null)
        ->edit('EntityLoginLevelsController@update', null, ['levelKey' =>  $loginLevel->login_level_key ?? null, 'entityKey' => $entityKey ?? null])
        ->open();
    @endphp

    {!! Form::hidden('entity_key', $entityKey ?? null) !!}
    {!! Form::oneText('name', trans('privateEntityLoginLevels.login_level_name'), $loginLevel->name ?? null, ['class' => 'form-control', 'id' => 'name']) !!}
    {!! Form::oneSwitch("manual_verification", trans('privateEntityLoginLevels.level_manual_verification'), $loginLevel->manual_verification ?? null) !!}
    {!! Form::oneSwitch("sms_verification", trans('privateEntityLoginLevels.level_sms_verification'), $loginLevel->sms_verification ?? null) !!}

    @if((!empty($loginLevelDependencies) && ONE::actionType('entityLoginLevels') == 'show' && !empty($loginLevel->login_level_dependencies)) ||
            (!empty($loginLevelDependencies) && ONE::actionType('entityLoginLevels') != 'show' ))
        <div class="row" style="padding-top: 20px">
            <div class="col-12">
                <label for="manual_verification">{{trans('privateEntityLoginLevels.dependencies')}}</label>
                <select id="dependencies" name="dependencies[]" class="form-control" multiple="true" @if(ONE::actionType('entityLoginLevels') == 'show') disabled @endif>
                    @foreach( $loginLevelDependencies as $loginLevelDependency)
                        @if((ONE::actionType('entityLoginLevels') == 'create') || (isset($loginLevel) && $loginLevelDependency->login_level_key != $loginLevel->login_level_key))
                            <option value="{{ $loginLevelDependency->login_level_key }}" {{ (isset($loginLevel) && array_key_exists($loginLevelDependency->login_level_key,$loginLevel->login_level_dependencies)? 'selected':'') }}  >{!! $loginLevelDependency->name !!}</option>
                        @endif
                    @endforeach
                </select>
            </div>
        </div>
    @endif
    {!! $form->make() !!}
@endsection

@section('scripts')
    {{--@if(ONE::actionType('entityLoginLevels') != 'create')--}}
    {{--<script>--}}
    {{--$(function() {--}}
    {{--var array = ["{{ $loginLevel->login_level_key ?? null}}","{{ $entityKey ?? null }}"];--}}
    {{--getSidebar('{{ action("OneController@getSidebar") }}', 'details', array, 'entityLoginLevels');--}}
    {{--});--}}
    {{--</script>--}}
    {{--@else--}}
    {{--<script>--}}
    {{--$(function() {--}}
    {{--getSidebar('{{ action("OneController@getSidebar") }}', 'entityLevels', '{{  $entityKey ?? null }}', 'entity' );--}}
    {{--})--}}
    {{--</script>--}}
    {{--@endif--}}

    <script>

        $(document).ready(function(){
            $("#dependencies").select2();
        });
    </script>
@endsection