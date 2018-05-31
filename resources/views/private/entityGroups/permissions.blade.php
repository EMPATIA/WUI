@extends('private._private.index')

@section('content')

    <form name="entityGroupPermissions" accept-charset="UTF-8" method="POST" id="entityGroupPermissions" action="{{action('EntityGroupsController@storePermissions', ['entityGroupKey' =>$entityGroupKey ])}}">

        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title"><i class="fa"></i> {{ trans('privateEntityGroup.add_entity_group_permission') }}</h3>
            </div>

            <div class="box-body">
                {!! ONE::messages() !!}
                <input type="hidden" name="_token" value="{{ csrf_token() }}"/>
                @foreach($modules as $module)
                    <div class="" role="tablist" style="margin-bottom: 5px">
                        <div class="card">
                            <div class="card-header card-header-gray" role="tab" id="headingOne">

                                    <div class="row">
                                        <div class="col-4" style="padding-top: 10px;color: #66a7dd;font-weight: bold">
                                            {{$module->name}}
                                        </div>
                                        <div class="col-2">
                                            <div class="row center-block">
                                                <div class="col-12 ">
                                                    {{trans('privateEntityGroups.permissions_show')}}
                                                </div>
                                                <div class="col-12 switch-module-div" id="{{$module->module_key}}_type_show">
                                                    {!! Form::oneSwitch(
                                                        "modules[show]",null, array_key_exists($module->module_key, []) ,
                                                        array(
                                                            "readonly"=>false,
                                                            'id' => $module->module_key."_show",
                                                            'data-target' => '#'.$module->name,
                                                            'aria-expanded' => 'true',
                                                            'aria-controls' => $module->name,
                                                            'value' => 1
                                                        )
                                                    ) !!}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-2">
                                            <div class="row">
                                                <div class="col-12">
                                                    {{trans('privateEntityGroups.permissions_create')}}
                                                </div>
                                                <div class="col-12 switch-module-div" id="{{$module->module_key}}_type_create">
                                                    {!! Form::oneSwitch(
                                                        "modules[create]",null, array_key_exists($module->module_key, []) ,
                                                        array(
                                                            "readonly"=>false,
                                                            'id' => $module->module_key."_create",
                                                            'data-target' => '#'.$module->name,
                                                            'aria-expanded' => 'true',
                                                            'aria-controls' => $module->name,
                                                            'value' => 1
                                                        )
                                                    ) !!}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-2">
                                            <div class="row">
                                                <div class="col-12">
                                                    {{trans('privateEntityGroups.permissions_update')}}
                                                </div>
                                                <div class="col-12 switch-module-div" id="{{$module->module_key}}_type_update">
                                                    {!! Form::oneSwitch(
                                                        "modules[update]",null, array_key_exists($module->module_key, []) ,
                                                        array(
                                                            "readonly"=>false,
                                                            'id' => $module->module_key."_update",
                                                            'data-target' => '#'.$module->name,
                                                            'aria-expanded' => 'true',
                                                            'aria-controls' => $module->name,
                                                            'value' => 1
                                                        )
                                                    ) !!}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-2">
                                            <div class="row">
                                                <div class="col-12" id="{{$module->module_key}}_type_delete">
                                                    {{trans('privateEntityGroup.permissions_delete')}}
                                                </div>
                                                <div class="col-12">
                                                    {!! Form::oneSwitch(
                                                        "modules[delete]",
                                                        null,
                                                        array_key_exists($module->module_key, []) ,
                                                        array(
                                                            "readonly"=>false,
                                                            'id' => $module->module_key."_delete",
                                                            'data-target' => '#'.$module->name,
                                                            'aria-expanded' => 'true',
                                                            'aria-controls' => $module->name,
                                                            'value' => 1
                                                        )
                                                    ) !!}
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                            </div>
                            <div id="{{$module->name}}" class="panel-collapse collapse show" role="tabpanel">
                                <div class="card-body">
                                    @foreach($module->types as $module_type)
                                        <div class="row">
                                            <div class="col-4">
                                                <label for="description">{{$module_type->name}}</label>
                                            </div>
                                            <div class="col-2 {{$module->module_key}}_type_show">
                                                {!! Form::oneSwitch(
                                                    "modules_types[$module->module_key][$module_type->module_type_key][show]",
                                                    null,
                                                    (isset($permissions[$module->module_key][$module_type->module_type_key]) &&$permissions[$module->module_key][$module_type->module_type_key]->permission_show == 1 ) ? true: false  ,
                                                    array(
                                                        "readonly"=>false,
                                                        'id' => $module_type->module_type_key."_show",
                                                        'value' => 1
                                                    )
                                                ) !!}
                                            </div>
                                            <div class="col-2 {{$module->module_key}}_type_create">
                                                {!! Form::oneSwitch(
                                                    "modules_types[$module->module_key][$module_type->module_type_key][create]",
                                                    null,
                                                    (isset($permissions[$module->module_key][$module_type->module_type_key]) &&$permissions[$module->module_key][$module_type->module_type_key]->permission_create == 1 ) ? true: false,
                                                    array(
                                                        "readonly"=>false,
                                                        'id' => $module_type->module_type_key."_create",
                                                        'value' => 1
                                                    )
                                                ) !!}
                                            </div>
                                            <div class="col-2 {{$module->module_key}}_type_update">
                                                {!! Form::oneSwitch(
                                                    "modules_types[$module->module_key][$module_type->module_type_key][update]",
                                                    null,
                                                    (isset($permissions[$module->module_key][$module_type->module_type_key]) &&$permissions[$module->module_key][$module_type->module_type_key]->permission_update == 1 ) ? true: false,
                                                    array(
                                                        "readonly"=>false,
                                                        'id' => $module_type->module_type_key."_update",
                                                        'value' => 1
                                                    )
                                                ) !!}
                                            </div>
                                            <div class="col-2 {{$module->module_key}}_type_delete">
                                                {!! Form::oneSwitch(
                                                    "modules_types[$module->module_key][$module_type->module_type_key][delete]",
                                                    null,
                                                    (isset($permissions[$module->module_key][$module_type->module_type_key]) &&$permissions[$module->module_key][$module_type->module_type_key]->permission_delete == 1 ) ? true: false,
                                                    array(
                                                        "readonly"=>false,
                                                        'id' => $module_type->module_type_key."_delete",
                                                        'value' => 1
                                                    )
                                                ) !!}
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="box-footer">
                <button type="submit" form="entityGroupPermissions" class="btn btn-group empatia">{{ trans('privateEntities.save') }}</button>
            </div>
        </div>
    </form>
@endsection
@section('scripts')
    <script>

        $('.switch-module-div').click(function(evt){
            evt.stopPropagation();
            evt.preventDefault();
            var id = $(this).attr('id');
            var value = $(this).find('input[type=checkbox]:checkbox:checked').length;
            if(value == 0){
                $(this).find('input[type=checkbox]').prop('checked', true);
            }
            else{
                $(this).find('input[type=checkbox]').prop('checked', false);
            }


            $('.'+id).each(function () {
                if(value == 0){
                    $(this).find('input[type=checkbox]').prop('checked', true);
                }
                else{
                    $(this).find('input[type=checkbox]').prop('checked', false);
                }
            });

        });
    </script>
@endsection