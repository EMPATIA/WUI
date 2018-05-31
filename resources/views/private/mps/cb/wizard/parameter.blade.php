<!-- Parameter -->
<div id="parameters_add_{{ $parameterCounter }}" class="parameterItem parametersGroupDiv" style="display: none;">

    <input type="hidden" name="parameter_id" value="{{ $parameter['id'] ?? null }}">
    <input type="hidden" name="operator_key" value="{{ $operatorKey ?? null }}">
    <div id="parameterWizard_{{ $parameterCounter }}">

        <div class="navbar ">
            <div class="navbar-inner">
                <ul class="nav nav-pills navParameterWizard">
                    <li class="active disabledTab"><a href="#stepParameter{{ $parameterCounter }}_1" data-toggle="tab" data-step="1">1</a></li>
                    <li class="disabledTab"><a href="#stepParameter{{ $parameterCounter }}_2" data-toggle="tab" data-step="2">2</a></li>
                    <li class="disabledTab"><a href="#stepParameter{{ $parameterCounter }}_3" data-toggle="tab" data-step="3">3</a></li>
                    @if(empty($parameter))
                        <li id='parameterStep4_{{ $parameterCounter }}' class='disabledTab @if(ONE::actionType('node') == 'create') disabled @endif'><a href="#stepParameter{{ $parameterCounter }}_4" data-toggle="tab" data-step="4">4</a></li>
                    @elseif(count($parameter['options']) > 0)
                        <li class='disabledTab'><a href="#stepParameter{{ $parameterCounter }}_4" data-toggle="tab" data-step="4">4</a></li>
                    @endif
                </ul>
            </div>
        </div>
        <div class="tab-content">
            <div class="tab-pane fade in active" id="stepParameter{{ $parameterCounter }}_1">
                <div class="well">
                    @if(empty($show))
                        <div class="form-group">
                            <label for="paramTypeSelect_{{ $parameterCounter }}">{!! trans("privateCbs.parameter_type") !!}  {{ $parameterCounter }}</label>
                            <select class="form-control" id="paramTypeSelect_{{ $parameterCounter }}" {{!empty($parameter) ? 'disabled' : null}} name="paramTypeSelect_{{ $parameterCounter }}" onchange="selectNewParameterType('{{ $parameterCounter }}',this.value)" required>
                                <option value=""> -- {!! trans("privateCbs.select_one_parameter_type") !!} -- </option>
                                @if(!empty($parameterType))
                                    @foreach($parameterType as $type)
                                        <option value="{{$type->code}}"
                                                {{ (!empty($parameterTemplateChoosed) && $parameterTemplateChoosed->code == $type->code) || (!empty($parameter) && $parameter['code'] == $type->code) ? "selected" : null }}>{{$type->name}}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    @else
                        {!! Form::oneSelect('parameter_type',['name' => trans('privateCbs.parameter_type'),'description' => trans("privateCbs.parameter_type_help")], $parameterTypes, $parameter['code'], $parameterTypes[$parameter['code']]->name ?? null, ['class' => 'form-control','required' => 'required'] ) !!}
                    @endif
                </div>
                <!-- Buttons: Next -->
                <div class="row">
                    <div class="col-md-12">
                        <a class="btn btn-flat empatia next pull-right" href="#">{!! trans("privateCbs.next") !!} <i class="fa fa-step-forward" aria-hidden="true"></i></a>
                        <br/><br/>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade" id="stepParameter{{ $parameterCounter }}_2">
                <div class="well">
                    <div class="row">
                        <div class="col-12">
                            @if(count($languages) > 0)
                                <div class="card" style="border-radius: 0">
                                    <div class="card-body">
                                        <ul class="nav nav-tabs" role="tablist">
                                            @foreach($languages as $language)
                                                <li class="nav-item" role="presentation">
                                                    <a class="nav-link {{(isset($language->default) && $language->default == true ? 'active' : null)}}" href="#tab-translation{{$language->code}}_{{$parameterCounter}}" aria-controls="affa" role="tab" data-toggle="tab">{{$language->name}}</a>
                                                </li>
                                            @endforeach
                                        </ul>
                                        <!-- Tab panes -->
                                        <div class="tab-content">
                                            @foreach($languages as $language)
                                                <div role="tabpanel" class="tab-pane {{(isset($language->default) && $language->default == true ? 'active' : null)}} " id="tab-translation{{$language->code}}_{{$parameterCounter}}">
                                                    <div style="">
                                                    @if(empty($show))
                                                        <!-- Name -->
                                                            <div class="form-group">
                                                                <label for="parameterName_{{ $parameterCounter }}">{{ trans('privateCbs.parameter_name') }} </label>
                                                                <input class="form-control" id="parameterName_{{ $parameterCounter }}" name="parameterName_{{ $parameterCounter }}[{{$language->code}}]" type="text" value="{{($translations[$language->code]['parameter'] ?? null)}}" onchange="changeParameterTitle({{ $parameterCounter }})" onfocusout="changeParameterTitle({{ $parameterCounter }})" {{(isset($language->default) && $language->default == true ? 'required' : null)}} />
                                                            </div>
                                                            <!-- Description -->
                                                            <div class="form-group">
                                                                <label for="parameterDescription_{{ $parameterCounter }}">{{ trans('privateCbs.parameter_description') }}</label>
                                                                <textarea class="form-control" id="parameterDescription_{{ $parameterCounter }}" name="parameterDescription_{{ $parameterCounter }}[{{$language->code}}]" cols="30" rows="10">{{($translations[$language->code]['description'] ?? null)}}</textarea>
                                                            </div>
                                                        @else
                                                            {!! Form::oneText('parameterName_'.$language->code, trans('privateCbs.parameterName'),
                                                            ($translations[$language->code]['parameter'] ?? null),
                                          ['class' => 'form-control', 'id' => 'parameterName_'.$language->code, (isset($language->default) && $language->default == true ? 'required' : null)]) !!}
                                                            {!! Form::oneTextArea('parameterDescription_'.$language->code, trans('privateCbs.parameterDescription'),
                                                             ($translations[$language->code]['description'] ?? null),
                                                             ['class' => 'form-control', 'id' => 'parameterDescription_'.$language->code ,(isset($language->default) && $language->default == true ? 'required' : null)]) !!}

                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                <!-- Buttons: Previous && Next -->
                <a class="btn btn-flat empatia prev " href="#"><i class="fa fa-step-backward" aria-hidden="true"></i> {!! trans("privateCbs.previous") !!}</a>
                <a class="btn btn-flat empatia next pull-right" href="#">{!! trans("privateCbs.next") !!} <i class="fa fa-step-forward" aria-hidden="true"></i></a>

            </div>
            <div class="tab-pane fade" id="stepParameter{{ $parameterCounter }}_3">
                <div class="well">
                    <!-- Mandatory -->
                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-12 col-md-6">
                                <label for="mandatory_{{ $parameterCounter }}">{{ trans('privateCbs.parameter_mandatory') }}</label>
                                <span class="form-text oneform-help-block" style="margin:-4px 0px 5px;font-size:10px;">{{ trans('privateCbs.help_parameter_mandatory') }}</span>
                            </div>
                            <div class="col-sm-12 col-md-3">
                                @if(empty($show))
                                    <div class="onoffswitch">
                                        <input type="checkbox" name="mandatory_{{ $parameterCounter }}" class="onoffswitch-checkbox" id="mandatory_{{ $parameterCounter }}" value="1"
                                                {{ (!empty($parameterTemplateChoosed) && $parameterTemplateChoosed->mandatory ==1) || (!empty($parameter) && $parameter['mandatory'] == 1) ?  'checked': '' }} >
                                        <label class="onoffswitch-label" for="mandatory_{{ $parameterCounter }}" @if(ONE::actionType('node') == 'show') disabled @endif>
                                            <span class="onoffswitch-inner"></span>
                                            <span class="onoffswitch-switch"></span>
                                        </label>
                                    </div>
                                @else
                                    {!! Form::oneSwitch("mandatory",null, $parameter['mandatory'],
                                                array("readonly"=>false, 'value' => $parameter['mandatory']) ) !!}
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Use filter -->
                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-12 col-md-6">
                                <label for="use_filter_{{ $parameterCounter }}">{{ trans('privateCbs.parameter_use_filter') }}</label>
                                <span class="form-text oneform-help-block" style="margin:-4px 0px 5px;font-size:10px;">{{ trans('privateCbs.help_parameter_use_filter') }}</span>
                            </div>
                            <div class="col-sm-12 col-md-3">
                                @if(empty($show))
                                    <div class="onoffswitch">
                                        <input type="checkbox" name="use_filter_{{ $parameterCounter }}" class="onoffswitch-checkbox" id="use_filter_{{ $parameterCounter }}" value="1"
                                                {{ (!empty($parameterTemplateChoosed) && $parameterTemplateChoosed->use_filter ==1) || (!empty($parameter) && $parameter['use_filter'] ==1) ?  'checked': '' }} >
                                        <label class="onoffswitch-label" for="use_filter_{{ $parameterCounter }}">
                                            <span class="onoffswitch-inner"></span>
                                            <span class="onoffswitch-switch"></span>
                                        </label>
                                    </div>
                                @else
                                    {!! Form::oneSwitch("use_filter",null, $parameter['use_filter'],
                                                array("readonly"=>false, 'value' => $parameter['use_filter']) ) !!}
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- visible -->
                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-12 col-md-6">
                                <label for="visible_{{ $parameterCounter }}">{{ trans('privateCbs.visible') }}</label>
                                <span class="form-text oneform-help-block" style="margin:-4px 0px 5px;font-size:10px;">{{ trans('privateCbs.help_visible') }}</span>
                            </div>
                            <div class="col-sm-12 col-md-3">
                                @if(empty($show))
                                    <div class="onoffswitch">
                                        <input type="checkbox" name="visible_{{ $parameterCounter }}" class="onoffswitch-checkbox" id="visible_{{ $parameterCounter }}" value="1"
                                                {{ (!empty($parameterTemplateChoosed) && $parameterTemplateChoosed->visible ==1) || (!empty($parameter) && $parameter['visible'] ==1) ?  'checked': '' }} >
                                        <label class="onoffswitch-label" for="visible_{{ $parameterCounter }}">
                                            <span class="onoffswitch-inner"></span>
                                            <span class="onoffswitch-switch"></span>
                                        </label>
                                    </div>
                                @else
                                    {!! Form::oneSwitch("visible",null, $parameter['visible'],
                                                array("readonly"=>false, 'value' => $parameter['visible']) ) !!}
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- visible in list -->
                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-12 col-md-6">
                                <label for="visibleInList_{{ $parameterCounter }}">{{ trans('privateCbs.visible_in_list') }}</label>
                                <span class="form-text oneform-help-block" style="margin:-4px 0px 5px;font-size:10px;">{{ trans('privateCbs.help_visible_in_list') }}</span>
                            </div>
                            <div class="col-sm-12 col-md-3">
                                @if(empty($show))
                                    <div class="onoffswitch">
                                        <input type="checkbox" name="visibleInList_{{ $parameterCounter }}" class="onoffswitch-checkbox" id="visibleInList_{{ $parameterCounter }}" value="1"
                                                {{ (!empty($parameterTemplateChoosed) && $parameterTemplateChoosed->visible_in_list ==1) || (!empty($parameter) && $parameter['visible_in_list'] ==1) ?  'checked': '' }} >
                                        <label class="onoffswitch-label" for="visibleInList_{{ $parameterCounter }}">
                                            <span class="onoffswitch-inner"></span>
                                            <span class="onoffswitch-switch"></span>
                                        </label>
                                    </div>
                                @else
                                    {!! Form::oneSwitch("visible_in_list",null, $parameter['visible_in_list'],
                                                array("readonly"=>false, 'value' => $parameter['visible_in_list']) ) !!}
                                @endif
                            </div>
                        </div>
                    </div>

                </div>
                <!-- Buttons: Previous && Next -->
                <a class="btn btn-flat empatia prev " href="#"><i class="fa fa-step-backward" aria-hidden="true"></i> {!! trans("privateCbs.previous") !!}</a>
                @if(empty($parameter))
                    <a id="parameterNextStep_{{ $parameterCounter }}_3"  class="btn btn-flat empatia next pull-right" href="#">{!! trans("privateCbs.next") !!} <i class="fa fa-step-forward" aria-hidden="true"></i></a>
                @elseif(count($parameter['options']) > 0)
                    <a id="parameterNextStep_{{ $parameterCounter }}_3"  class="btn btn-flat empatia next pull-right" href="#">{!! trans("privateCbs.next") !!} <i class="fa fa-step-forward" aria-hidden="true"></i></a>
                @endif
            </div>
            <div class="tab-pane fade" id="stepParameter{{ $parameterCounter }}_4">
                <div class="well">
                    @if(count($languages) > 0)
                        <div class="card" style="border-radius: 0">
                            <div class="card-body">
                                <ul class="nav nav-tabs" role="tablist">
                                    @foreach($languages as $language)
                                        <li role="presentation" class="nav-item">
                                            <a class="nav-link {{(isset($language->default) && $language->default == true ? 'active' : null)}}" href="#tab-translationOption{{$language->code}}_{{$parameterCounter}}" aria-controls="affa" role="tab" data-toggle="tab">{{$language->name}}</a>
                                        </li>
                                    @endforeach
                                </ul>
                                <!-- Tab panes -->
                                <div class="tab-content">
                                    @foreach($languages as $language)
                                        <div role="tabpanel" class="tab-pane {{(isset($language->default) && $language->default == true ? 'active' : null)}} " id="tab-translationOption{{$language->code}}_{{$parameterCounter}}">
                                            <!-- Options -->
                                            @if(empty($show))
                                                <div id="parameterOptionsDiv_{{$parameterCounter}}" class="card flat parameterOptionsDiv_{{$parameterCounter}}">
                                                    <div class="card-header">
                                                        {{ trans('privateCbs.options') }}
                                                        <div class="pull-right">
                                                            <a class="btn btn-flat btn-success btn-sm" title=""
                                                               data-original-title="Create" id="buttonAddOption" onclick="addNewOption('{{ $parameterCounter }}')">
                                                                <i class="fa fa-plus"></i>
                                                            </a>
                                                        </div>
                                                    </div>
                                                    <div class="box-body">
                                                        <div id="newOptionsDiv_{{$parameterCounter}}_{{$language->code}}" class="row newOptionsDiv_{{$parameterCounter}} {{(isset($language->default) && $language->default == true ? 'required' : null)}}">
                                                            @if(isset($parameter))
                                                                @foreach($parameter['options'] as $option)
                                                                    <div class="col-md-3 opt_{{ $parameterCounter }}_{{$loop->iteration}}" id="">
                                                                        <div class="btn-group" style="margin-top:5px;margin-bottom:10px;">
                                                                            <input class="form-control" id="optionsNew_{{ $parameterCounter }}" placeholder="{!! trans("privateCbs.option_value") !!}" value="{{$option['translations'][$language->code]['label'] ?? null}}" {{(isset($language->default) && $language->default == true ? 'required' : null)}} name="optionsOld_{{ $parameterCounter }}[{{$option['id']}}][{{$language->code}}]" type="text">
                                                                        </div>
                                                                        <div class="btn-group" style="margin-top:5px;margin-bottom:10px;">
                                                                            <a style="margin-left:6px;" class="btn btn-flat btn-danger btn-sm" onclick="removeOption('{{ $parameterCounter }}','{{$loop->iteration}}')" data-original-title="Delete"><i class="fa fa-remove"></i></a>
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            @else
                                                @foreach($parameter['options'] as $option)
                                                    <div class="col-md-3 optSelect_{!! $option['id'] !!}" id="optSelect_{!! $option['id'] !!}">
                                                        {{--                                                            {!! Form::hidden('optionsSelectIds[]', isset($option) ? $option->id : null, ['id' => 'optionsSelectIds']) !!}--}}
                                                        <div class="btn-group">
                                                            {!! Form::oneText('optionsSelect','',
                                                             $option['translations'][$language->code]['label'] ?? null,
                                                              ['class' => 'form-control', 'id' => 'optionsSelect' , (isset($language->default) && $language->default == true ? 'required' : null)]) !!}
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
                <!-- Buttons: Previous && Next -->
                <a class="btn btn-flat empatia prev " href="#"><i class="fa fa-step-backward" aria-hidden="true"></i> {!! trans("privateCbs.previous") !!}</a>
            </div>

        </div>



    </div>

<!-- Not implemented
    <div id="imageMapGroup_{{$parameterCounter}}" class="form-group" style="display:none;">
        <label for="parameterDescription">{{ trans('privateCbs.imageMap') }}</label>
        <br/>
        <div style="width:100%;border:1px solid #d2d6de;padding:10px;text-align:center;">
            <img id="imageMapFile" class="img" src="{{ asset(ONE::getEmpavilleImageMap()) }}" id="imageMapFile" style="width:200px;max-height:400px;"/>
        </div>    
    </div>
    -->

<!--
    <div class="uploadImage" id="uploadImageCb_{{$parameterCounter}}">
        <p>{!! ONE::fileUploadBox("banner-drop-zone", trans('files.drop-zone'), trans('files.banners'), 'select-banner', 'banner-list', 'files_banner') !!}</p>
    </div>
    -->


</div>

@if(!empty($parameterTemplateChoosed))
    <script>
        setTimeout(
            function(){
                @if( !empty($parameterTemplateChoosed->type->code) )
                    $('#paramTypeSelect_{{ $parameterCounter }} option[value={{ $parameterTemplateChoosed->type->code }}]').prop('selected', true);
                selectNewParameterType('{{ $parameterCounter }}',"{{ $parameterTemplateChoosed->type->code }}",true);
                @foreach($parameterTemplateChoosed->template_options as $opt)
                    addNewOption('{{ $parameterCounter }}',"{{ $opt->label }}");
                @endforeach
                @endif

                $("#paramTemplateSelectDiv{{ $parameterCounter }}").hide();
            }
            , 1);

        setTimeout(
            function(){
                var booleanVar = isParameterWithOptions({{ $parameterCounter }});
                if(booleanVar) {
                    $("#parameterNextStep_{{ $parameterCounter }}_3").show();
                    $("#parameterStep4_{{ $parameterCounter }}").removeClass("disabled");
                } else {
                    $("#parameterNextStep_{{ $parameterCounter }}_3").hide();
                    $("#parameterStep4_{{ $parameterCounter }}").addClass("disabled");
                }
            }
            , 500);
    </script>
@endif

<script>
    toastr.options = {
        "closeButton": true,
        "debug": false,
        "newestOnTop": false,
        "progressBar": false,
        "positionClass": "toast-bottom-right",
        "preventDuplicates": false,
        "onclick": null,
        "showDuration": "0",
        "hideDuration": "0",
        "timeOut": "0",
        "extendedTimeOut": "0",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    };
    setTimeout(
        function(){
            $("#parameterStep4_{{ $parameterCounter }}").addClass( "disabled" );
            $("#parameterStep4_{{ $parameterCounter }}").addClass( "disabledTab" );


            /* Stepper Engine [create.blade.php] --------------------------- START */
            $('#parameterWizard_{{ $parameterCounter }} .next').click(function(){

                var stepDiv = $(this).parents('.tab-pane').next().attr("id");

                // Show submit button at ...
                var booleanVar = isParameterWithOptions({{ $parameterCounter }});

                if( stepDiv == "stepParameter{{ $parameterCounter }}_2" && $("#paramTypeSelect_{{ $parameterCounter }}").val() =="" ){
                    $("#paramTypeSelect_{{ $parameterCounter }}").focus();
                    toastr.error("{!! preg_replace( "/\r|\n/", "", htmlentities(trans("privateCbs.please_select_type_on_tab"),ENT_QUOTES)) !!} #1!", '', {timeOut: 0,positionClass: "toast-bottom-right"});
                    return false;
                } else if( stepDiv == "stepParameter{{ $parameterCounter }}_3" && $("#parameterName_{{ $parameterCounter }}").val() =="" ){
                    $("#parameterName_{{ $parameterCounter }}").focus();
                    toastr.error("{!! preg_replace( "/\r|\n/", "", htmlentities(trans("privateCbs.name_is_required_on_tab"),ENT_QUOTES)) !!} #2!", '', {timeOut: 0,positionClass: "toast-bottom-right"});
                    return false;
                } else if( stepDiv == "stepParameter{{ $parameterCounter }}_3" && $("#parameterDescription_{{ $parameterCounter }}").val() =="" ){
                    $("#parameterDescription_{{ $parameterCounter }}").focus();
                    toastr.error("{!! preg_replace( "/\r|\n/", "", htmlentities(trans("privateCbs.description_is_required_on_tab"),ENT_QUOTES)) !!} #2!", '', {timeOut: 0,positionClass: "toast-bottom-right"});
                    return false;
                } else {
                    if( stepDiv == "stepParameter{{ $parameterCounter }}_4"
                        && booleanVar){
                        $('#modalAddParameterButton{{ $parameterCounter }}').show();
                    } else if( stepDiv == "stepParameter{{ $parameterCounter }}_3"
                        && !booleanVar  ){
                        $('#modalAddParameterButton{{ $parameterCounter }}').show();
                    }
                    var nextId = stepDiv;
                    $('[href=#'+nextId+']').tab('show');
                    return false;
                }

            });

            $('#parameterWizard_{{ $parameterCounter }} .prev').click(function(){
                var nextId = $(this).parents('.tab-pane').prev().attr("id");
                $('[href=#'+nextId+']').tab('show');
                return false;
            });

            $('#parameterWizard_{{ $parameterCounter }} a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
                //update progress
                var step = $(e.target).data('step');
            });


        }
        , 50);
</script>

