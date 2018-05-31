@extends('private._private.index')

@section('content')


    <form name="cbPermissions" accept-charset="UTF-8" method="POST" id="cbPermissions" action="{{action('CbsController@storePermissions', ['type' => $type, 'cbKey' =>$cbKey, 'groupKey' => $groupKey, 'userKey' => $userKey ])}}">
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title"><i class="fa"></i> {{ trans('privateCbsPermissions.add_cb_permissions') }}</h3>
            </div>
            <div class="box-body">

                {!! ONE::messages() !!}
                <input type="hidden" name="_token" value="{{ csrf_token() }}"/>

                @foreach($parameters as $parameter)
                    @if(!empty($parameter->options))
                        <div class="" role="tablist" style="margin-bottom: 5px">
                            <div class="card">
                                <div class="card-header module-div card-header-gray" role="tab" id="headingOne">

                                        <div class="row">
                                            <div class="col-4" style="padding-top: 10px;color: #66a7dd;font-weight: bold">
                                                {{$parameter->parameter}}
                                            </div>
                                            <div class="col-2">
                                                <div class="row center-block">
                                                    <div class="col-12">
                                                        {{trans('privateCbsPermissions.permissions_show')}}
                                                    </div>
                                                    <div class="col-12 switch-module-div" id="{{$parameter->id}}_type_show">
                                                        {!! Form::oneSwitch(
                                                            "modules[show]",null, in_array($parameter->id.'_show', $parameterPermissions) ,
                                                            array(
                                                                "readonly"=>false,
                                                                'id' => $parameter->id."_show",
                                                                'data-target' => '#'.$parameter->parameter,
                                                                'aria-expanded' => 'true',
                                                                'aria-controls' => $parameter->parameter,
                                                                'value' => 1
                                                            )
                                                        ) !!}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-2">
                                                <div class="row">
                                                    <div class="col-12">
                                                        {{trans('privateCbsPermissions.permissions_create')}}
                                                    </div>
                                                    <div class="col-12 switch-module-div" id="{{$parameter->id}}_type_create">
                                                        {!! Form::oneSwitch(
                                                            "modules[create]",null, in_array($parameter->id.'_create', $parameterPermissions) ,
                                                            array(
                                                                "readonly"=>false,
                                                                'id' => $parameter->id."_create",
                                                                'data-target' => '#'.$parameter->parameter,
                                                                'aria-expanded' => 'true',
                                                                'aria-controls' => $parameter->parameter,
                                                                'value' => 1
                                                            )
                                                        ) !!}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-2">
                                                <div class="row">
                                                    <div class="col-12">
                                                        {{trans('privateCbsPermissions.permissions_update')}}
                                                    </div>
                                                    <div class="col-12 switch-module-div" id="{{$parameter->id}}_type_update">
                                                        {!! Form::oneSwitch(
                                                            "modules[update]",null, in_array($parameter->id.'_update', $parameterPermissions) ,
                                                            array(
                                                                "readonly"=>false,
                                                                'id' => $parameter->id."_show",
                                                                'data-target' => '#'.$parameter->parameter,
                                                                'aria-expanded' => 'true',
                                                                'aria-controls' => $parameter->parameter,
                                                                'value' => 1
                                                            )
                                                        ) !!}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-2">
                                                <div class="row">
                                                    <div class="col-12 switch-module-div">
                                                        {{trans('privateCbsPermissions.permissions_delete')}}
                                                    </div>
                                                    <div class="col-12 switch-module-div" id="{{$parameter->id}}_type_delete">
                                                        {!! Form::oneSwitch(
                                                            "modules[delete]",null, in_array($parameter->id.'_delete', $parameterPermissions) ,
                                                            array(
                                                                "readonly"=>false,
                                                                'id' => $parameter->id."_delete",
                                                                'data-target' => '#'.$parameter->parameter,
                                                                'aria-expanded' => 'true',
                                                                'aria-controls' => $parameter->parameter,
                                                                'value' => 1
                                                            )
                                                        ) !!}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                </div>
                                <div id="{{$parameter->parameter}}" class="panel-collapse collapse show" role="tabpanel">
                                    <div class="card-body">
                                            @foreach($parameter->options as $option)
                                                <div class="row">
                                                    <div class="col-4">
                                                        <label for="description">{{$option->label}}</label>
                                                    </div>
                                                    <div class="col-2 {{$parameter->id}}_type_show">
                                                        {!! Form::oneSwitch(
                                                            "modules_types[$option->parameter_id][$option->id][show]",
                                                            null,
                                                            (isset($permissions[$option->id]) && !empty($permissions)) ? $permissions[$option->id]['show'] : 0  ,
                                                            array(
                                                                "readonly"=>false,
                                                                'id' => $option->id."_show",
                                                                'value' => 1
                                                            )
                                                        ) !!}
                                                    </div>
                                                    <div class="col-2 {{$parameter->id}}_type_create">
                                                        {!! Form::oneSwitch(
                                                            "modules_types[$option->parameter_id][$option->id][create]",
                                                            null,
                                                            (isset($permissions[$option->id]) && !empty($permissions)) ? $permissions[$option->id]['create'] : 0,
                                                            array(
                                                                "readonly"=>false,
                                                                'id' => $option->id."_create",
                                                                'value' => 1
                                                            )
                                                        ) !!}
                                                    </div>
                                                    <div class="col-2 {{$parameter->id}}_type_update">
                                                        {!! Form::oneSwitch(
                                                            "modules_types[$option->parameter_id][$option->id][update]",
                                                            null,
                                                            (isset($permissions[$option->id]) && !empty($permissions)) ? $permissions[$option->id]['update'] : 0,
                                                            array(
                                                                "readonly"=>false,
                                                                'id' => $option->id."_update",
                                                                'value' => 1
                                                            )
                                                        ) !!}
                                                    </div>
                                                    <div class="col-2 {{$parameter->id}}_type_delete">
                                                        {!! Form::oneSwitch(
                                                            "modules_types[$option->parameter_id][$option->id][delete]",
                                                            null,
                                                            (isset($permissions[$option->id]) && !empty($permissions)) ? $permissions[$option->id]['delete'] : 0,
                                                            array(
                                                                "readonly"=>false,
                                                                'id' => $option->id."_delete",
                                                                'value' => 1
                                                            )
                                                        ) !!}
                                                    </div>
                                                </div>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>

                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
            <div class="box-footer">
                <button type="submit" form="cbPermissions" class="btn empatia">{{ trans('privateCbsPermissions.save') }}</button>
                <a href="{{ action('CbsController@showGroupPermissions', ["type" => $type,"cbKey" => $cbKey]) }}" class="btn btn-flat btn-default">
                    {{ trans('privateCbsPermissions.cancel') }}
                </a>
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
            console.log(id);

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
                    console.log(value);

                    $(this).find('input[type=checkbox]').prop('checked', false);
                }
            });

        });
    </script>
@endsection