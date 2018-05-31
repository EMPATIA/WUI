@extends('private._private.index')

@section('content')




    @if(ONE::actionType('roles') == "show")
        <div class="card card-success">
            <div class="card-header">
                <h3 class="card-header">Permissions</h3>
            </div>
            <div class="card-body">

                @foreach(\App\ComModules\Orchestrator::getRolesModuleAPI() as $key => $values)
                    <div class="card card-info">
                        <div class="card-header">
                            <h3 class="card-header">{{$key}}</h3>
                        </div>
                        <div class="card-body">
                            <ul class="list-group">
                                @foreach($values as $value)
                                    <li class="list-group-item list-group-item-action list-group-item-success" style="height: 55px;">
                                        <div style="width: 40%; float:left; line-height: 35px">
                                            {{$value}}
                                        </div>
                                        <div style="width: 60%; float:right; text-align: right">
                                            <div class="btn-group" data-toggle="buttons">
                                                <label class="btn btn-secondary {{isset($permissions[$key."_".$value."-create"])?  $permissions[$key."_".$value."-create"] == 1 ? 'active': '': ''}}" onclick="savePermission(this, '{{$key}}_{{$value}}', '{{$key}}', '{{$value}}', 'create')">
                                                    <input type="checkbox" > Create
                                                </label>
                                                <label class="btn btn-secondary {{isset($permissions[$key."_".$value."-view"])? $permissions[$key."_".$value."-view"] == 1 ? 'active': '': ''}}" onclick="savePermission(this,'{{$key}}_{{$value}}', '{{$key}}', '{{$value}}', 'view')">
                                                    <input type="checkbox"> View
                                                </label>
                                                <label class="btn btn-secondary {{isset($permissions[$key."_".$value."-update"])? $permissions[$key."_".$value."-update"] == 1 ? 'active': '': ''}}" onclick="savePermission(this,'{{$key}}_{{$value}}', '{{$key}}', '{{$value}}', 'update')">
                                                    <input type="checkbox"> Update
                                                </label>
                                                <label class="btn btn-secondary {{isset($permissions[$key."_".$value."-delete"])? $permissions[$key."_".$value."-delete"] == 1 ? 'active': '': ''}}" onclick="savePermission(this,'{{$key}}_{{$value}}', '{{$key}}', '{{$value}}', 'delete')">
                                                    <input type="checkbox"> Delete
                                                </label>
                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif


@endsection

@section('scripts')
    <script>
        $(function() {
            getSidebar('{{ action("OneController@getSidebar") }}', 'permissions', "{{(isset($role) ? $role->role_key : "")}}", 'functions' )


        })
    </script>
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