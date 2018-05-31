@extends('private._private.index')

@section('content')
    <form name="entityModule" accept-charset="UTF-8" method="POST" id="entityModule" action="{{action('EntitiesController@updateEntityModules', ['entityKey' =>$entityKey ])}}">

        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title"><i class="fa"></i> {{ trans('privateEntities.add_entity_module') }}</h3>
            </div>
            <div class="box-body">
                {!! ONE::messages() !!}
                <input type="hidden" name="_token" value="{{ csrf_token() }}"/>
                @foreach($modules as $module)
                    <div class="panel-group" role="tablist" style="margin-bottom: 5px">
                        <div class="card panel-default">
                            <div class="card-header card-header-gray" role="tab" id="headingOne">
                                <h5 class="panel-title onoffswitch-labelTxt">
                                    <div class="row">
                                        <div class="col-10 col-lg-6 col-md-8" style="padding-top: 10px;">
                                            <div class="pull-left">
                                                {!! Form::oneSwitch("modules[]",null, (array_key_exists($module->module_key, (isset($entityModules) ? $entityModules : []))),
                                                    array("readonly"=>false,'id' => $module->module_key, 'value' => $module->module_key,'data-target' => '#'.$module->name) ) !!}
                                            </div>
                                            &nbsp;
                                            <label class="label-module-title"> {{$module->name}}</label>
                                        </div>
                                    </div>
                                </h5>
                            </div>
                            <div id="{{$module->name}}" class="panel-collapse collapse {{array_key_exists($module->module_key, (isset($entityModules) ? $entityModules : [])) ?  'show': ''}}" role="tabpanel">
                                <div class="card-body">
                                    @foreach($module->module_types as $module_type)
                                        <div class="row background-hover">
                                            <div class="col-12">
                                                <div class="pull-left">
                                                    {!! Form::oneSwitch("modules_types[$module->module_key][]",null, (array_key_exists($module->module_key, (isset($entityModules) ? $entityModules : [])) && array_key_exists($module_type->module_type_key, (isset($entityModules[$module->module_key]['types']) ? $entityModules[$module->module_key]['types'] : []))),
                                               array("readonly"=>false,'id' => $module_type->module_type_key, 'value' => $module_type->module_type_key) ) !!}

                                                </div>
                                                &nbsp;<label for="description" class="label-module">{{$module_type->name}}</label>
                                            </div>
                                            {{--
                                               <div class="col-9 col-sm-9 col-md-11 col-lg-11">
                                                <label for="description">{{$module_type->name}}</label>
                                            </div>
                                            --}}
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="box-footer">
{{--                <a class="btn btn-flat btn-preview" href=" {!!  action('EntitiesController@showModules',$entityKey) !!}"><i class="fa fa-arrow-left"></i> {!! trans('privateEntities.back') !!}</a>--}}
                <button type="submit" form="entityModule" class="btn btn-flat empatia">{{ trans('privateEntities.save') }}</button>
            </div>
        </div>

    </form>
    <script>
        function collapseCard(element) {
            card = element.parent().attr("data-target");
            if (card !== undefined) {
                if (element.is(":checked"))
                    $(card).collapse("show");
                else
                    $(card).collapse("hide");
            }
        }
        @foreach($modules as $module)
            $("#{{ $module->module_key }}").on("click",function() {
                collapseCard($(this));
            });
        @endforeach

    </script>
@endsection