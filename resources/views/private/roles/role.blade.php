@extends('private._private.index')

@section('content')

    @php $form = ONE::form('roles', trans('privateRoles.details'), 'orchestrator', 'role')
            ->settings(["model" => isset($role) ? $role : null,'id'=>isset($role) ? $role->role_key : null])
            ->show('RolesController@edit', 'RolesController@delete', ['roleKey' => isset($role) ? $role->role_key : null], 'RolesController@index', ['roleKey' => isset($role) ? $role->role_key : null])
            ->create('RolesController@store', 'RolesController@index', ['roleKey' => isset($role) ? $role->role_key : null])
            ->edit('RolesController@update', 'RolesController@show', ['roleKey' => isset($role) ? $role->role_key : null])
            ->open();
    @endphp

    {!! Form::oneText('name', trans('privateRoles.name'), isset($role) ? $role->name : null, ['class' => 'form-control', 'id' => 'name']) !!}
    {!! Form::oneText('code', trans('privateRoles.code'), isset($role) ? $role->code : null, ['class' => 'form-control', 'id' => 'code']) !!}
    {!! Form::oneTextArea('description', trans('privateRoles.description'), isset($role) ? $role->description : null, ['class' => 'form-control', 'id' => 'description', 'size' => '30x2', 'style' => 'resize: vertical']) !!}



    {!! $form->make() !!}

@endsection

@section('scripts')
    <script>
        @if(ONE::actionType('roles') == "show")


        function savePermission(label, code, module, api, optionName){

            var valueOption = 0;

            if(!$(label).hasClass('active') ){
                valueOption = 1;
            }

            $.ajax({
                url: "{{action('RolesController@setPermissionRole')}}",
                type: 'post',
                data: {
                    role_key: '{{$role->role_key}}',
                    code: code,
                    module: module,
                    api: api,
                    option: optionName,
                    value: valueOption,
                    _token: "{{ csrf_token() }}"
                }
            });
        }


        @endif

    </script>
@endsection