@extends('private._private.index')

@section('content')
    <form name="entityModule" accept-charset="UTF-8" method="POST" id="entityModule" action="{{action('EntitiesDividedController@updateEntityModules')}}">

        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title"><i class="fa"></i> {{ trans('privateModules.addEntityModule') }}</h3>
            </div>
            <div class="box-body">
                {!! ONE::messages() !!}
                <input type="hidden" name="_token" value="{{ csrf_token() }}"/>
                @foreach($modules as $module)
                    <div class="" role="tablist" style="margin-bottom: 5px">
                        <div class="card">
                            <div class="card-header card-header-gray" role="tab" id="headingOne">
                                <div class="row">
                                    <div class="col-10" style="padding-top: 10px;color: #66a7dd;font-weight: bold">
                                        {{$module->name}}
                                    </div>
                                    <div class="col-2">
                                        <div class="pull-right">
                                            {!! Form::oneSwitch("modules[]",null, array_key_exists($module->module_key, (isset($entityModules) ? $entityModules : [])) ,
                                            array("readonly"=>false,'id' => $module->module_key,'data-toggle' => 'collapse','data-target' => '#'.$module->name, 'aria-expanded' => 'true','aria-controls' => $module->name, 'value' => $module->module_key) ) !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div id="{{$module->name}}" class="panel-collapse collapse {{array_key_exists($module->module_key, (isset($entityModules) ? $entityModules : [])) ?  'in': ''}}" role="tabpanel">
                                <div class="card-body">
                                    @foreach($module->module_types as $module_type)
                                        <div class="row">
                                            <div class="col-10">
                                                <label for="description">{{$module_type->name}}</label>
                                            </div>
                                            <div class="col-2">
                                                <div class="pull-right">
                                                    {!! Form::oneSwitch("modules_types[$module->module_key][]",null, (array_key_exists($module->module_key, (isset($entityModules) ? $entityModules : [])) && array_key_exists($module_type->module_type_key, (isset($entityModules[$module->module_key]['types']) ? $entityModules[$module->module_key]['types'] : []))),
                                               array("readonly"=>false,'id' => $module_type->module_type_key, 'value' => $module_type->module_type_key) ) !!}

                                                </div>
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
                <a class="btn btn-flat btn-primary" href=" {!!  action('EntitiesDividedController@showModules') !!}"><i class="fa fa-arrow-left"></i> {!! trans('privateModules.back') !!}</a>
                <button type="submit" form="entityModule" class="btn btn-group empatia">{{ trans('privateModules.save') }}</button>
            </div>
        </div>

    </form>
@endsection


@section('scripts')
    <script>

    </script>

@endsection