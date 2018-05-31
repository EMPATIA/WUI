@if(isset($registerParameters))
    @if(!empty($registerParameters))
        <div class="row form-row">
            <div style="padding: 0px" class="col-12 col-sm-8 col-md-8 col-lg-8">
                <h5 class="form-title">{{ ONE::transSite("parameter_personal_data") }}</h5>
            </div>
        </div>
        @foreach($registerParameters as $parameter)
            <div class="row white-bg form-row">
                @if($parameter['parameter_type_code'] == 'text')
                    <div clasS="col-lg-4 form-label">
                        {!! Form::label($parameter['parameter_user_type_key'], $parameter['name'] . ($parameter['mandatory'] == true ? ' *':null), array('class' => ($parameter['mandatory'] == true ? 'required label-required':null) )) !!}
                    </div>
                    <div clasS="col-lg-8">
                        <div class="form-group has-warning">
                            {!! Form::text($parameter['parameter_user_type_key'], $parameter['value'],  array($parameter['mandatory'] == true ? 'required' : null , 'class'=>'form-control'.($errors->has( $parameter['parameter_user_type_key'] ) ?  " input-error" : "" ), 'title' => $parameter['name'])) !!}
                            {{--<small id="titleHelp" class="form-text text-muted"> Sed volutpat erat tellus, id porttitor velit vehicula ac. Donec vestibulum tortor at varius tempor.</small>--}}
                        </div>
                    </div>
                @elseif($parameter['parameter_type_code'] == 'text_area')
                    <div clasS="col-lg-4 form-label">
                        {!! Form::label($parameter['parameter_user_type_key'], $parameter['name'] . ($parameter['mandatory'] == true ? ' *':null), array('class' => ($parameter['mandatory'] == true ? 'required label-required':null) )) !!}{!! Form::label($parameter['parameter_user_type_key'], $parameter['name'] . ($parameter['mandatory'] == true ? ' *':null), array('class' => ($parameter['mandatory'] == true ? 'required label-required':null))) !!}
                    </div>
                    <div clasS="col-lg-8">
                        <div class="form-group has-warning">
                            {!! Form::textarea($parameter['parameter_user_type_key'], $parameter['value'],  array('class'=>'form-control'.($errors->has($parameter['parameter_user_type_key']) ? " input-error": ""), 'required' => ($parameter['mandatory'] == true ? 'required' : null), 'title' => $parameter['name'])) !!}
                            {{--<small id="titleHelp" class="form-text text-muted"> Sed volutpat erat tellus, id porttitor velit vehicula ac. Donec vestibulum tortor at varius tempor.</small>--}}
                        </div>
                    </div>
                @elseif($parameter['parameter_type_code'] == 'numeric' || $parameter['parameter_type_code'] == 'mobile_phone')
                    @php
                        $parameterInputOptions = array(
                            'class'=>'form-control',
                            'required' => ($parameter['mandatory'] == true ? 'required' : null),
                            'title' => $parameter['name']
                        );

                        if($parameter["code"]=="postal") {
                            $parameterInputOptions["min"] = "1000";
                            $parameterInputOptions["max"] = "9999";
                        }
                    @endphp
                    <div clasS="col-lg-4 form-label">
                        {!! Form::label($parameter['parameter_user_type_key'], $parameter['name'] . ($parameter['mandatory'] == true ? ' *':null), array('class' => ($parameter['mandatory'] == true ? 'required label-required':null))) !!}
                    </div>
                    <div clasS="col-lg-8">
                        <div class="form-group has-warning">
                            {!! Form::number($parameter['parameter_user_type_key'], $parameter['value'],  $parameterInputOptions) !!}
                            {{--<small id="titleHelp" class="form-text text-muted"> Sed volutpat erat tellus, id porttitor velit vehicula ac. Donec vestibulum tortor at varius tempor.</small>--}}
                        </div>
                    </div>
                @elseif($parameter['parameter_type_code'] == 'radio_buttons')
                    @if(count($parameter['parameter_user_options'])> 0)
                        <div clasS="col-lg-4 form-label">
                            {!! Form::label($parameter['parameter_user_type_key'], $parameter['name'] . ($parameter['mandatory'] == true ? ' *':null), array('class' => ($parameter['mandatory'] == true ? 'required label-required':null))) !!}
                        </div>
                        <div clasS="col-lg-8">
                            <div class="form-group has-warning">
                                @foreach($parameter['parameter_user_options'] as $option)
                                    <div class="radio">
                                        <label>
                                            <input role="radiogroup"
                                                   aria-label="{!! $parameter['name'] !!}" type="radio"
                                                   name="{{$parameter['parameter_user_type_key']}}"
                                                   title="{!! $parameter['name'] !!}"
                                                   value="{{$option['parameter_user_option_key']}}"
                                                   @if($parameter['mandatory']) required
                                                   @endif @if($option['selected']) checked @endif>{{$option['name']}}
                                        </label>
                                    </div>
                                @endforeach
                                {{--<small id="titleHelp" class="form-text text-muted"> Sed volutpat erat tellus, id porttitor velit vehicula ac. Donec vestibulum tortor at varius tempor.</small>--}}
                            </div>
                        </div>
                    @endif
                @elseif($parameter['parameter_type_code'] == 'check_box')
                    @if(count($parameter['parameter_user_options'])> 0)
                        <div clasS="col-lg-4 form-label">
                            {!! Form::label($parameter['name'], $parameter['name'] . ($parameter['mandatory'] == true ? ' *':null), array('class' => ($parameter['mandatory'] == true ? 'required label-required':null))) !!}
                        </div>
                        <div clasS="col-lg-8">
                            <div class="form-group has-warning">
                                @foreach($parameter['parameter_user_options'] as $option)
                                    <div class="checkbox">
                                        <label><input type="checkbox"
                                                      value="{{$option['parameter_user_option_key']}}"
                                                      title="{!! $parameter['name'] !!}"
                                                      name="{{$parameter['parameter_user_type_key']}}[]"
                                                      @if($parameter['mandatory']) required
                                                      @endif @if($option['selected'] || old($parameter['parameter_user_type_key'])==$option['parameter_user_option_key']) checked @endif>{{$option['name']}}
                                        </label>
                                    </div>
                                @endforeach
                                {{--<small id="titleHelp" class="form-text text-muted"> Sed volutpat erat tellus, id porttitor velit vehicula ac. Donec vestibulum tortor at varius tempor.</small>--}}
                            </div>
                        </div>
                    @endif


                @elseif($parameter['parameter_type_code'] == 'dropdown')
                    @if(count($parameter['parameter_user_options'])> 0)
                        <div clasS="col-lg-4 form-label">
                            {!! Form::label($parameter['parameter_user_type_key'], $parameter['name'] . ($parameter['mandatory'] == true ? ' *':null), array('class' => ($parameter['mandatory'] == true ? 'required label-required':null) )) !!}
                        </div>
                        <div clasS="col-lg-8">
                            <select class="form-control @if($errors->has( $parameter['parameter_user_type_key'] )) input-error @endif" id="{{$parameter['parameter_user_type_key']}}"
                                    name="{{$parameter['parameter_user_type_key']}}"
                                    @if($parameter['mandatory']) required @endif>
                                <option value="" selected>{{ONE::transSite("parameter_select_option")}}</option>
                                @foreach(!empty($parameter['parameter_user_options']) ? $parameter['parameter_user_options'] : [] as $option)
                                    <option value="{{$option['parameter_user_option_key']}}"
                                            @if($option['selected'] || old($parameter['parameter_user_type_key'])==$option['parameter_user_option_key'] ) selected @endif>{{$option['name']}}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                @elseif($parameter['parameter_type_code'] == 'mobile')
                    <div clasS="col-lg-4 form-label">
                        {!! Form::label($parameter['name'], $parameter['name'] . (ONE::isEdit() && $parameter['mandatory']  ? "*" : ""), array('class' => 'label-required color-secundary ' .($parameter['mandatory'] == 1 ? 'required' :null ) )) !!}
                    </div>
                    <div clasS="col-lg-8">
                        <div class="form-group has-warning">
                            {!! Form::text($parameter['parameter_user_type_key'], $parameter['value'],  array($parameter['mandatory'] == true ? 'required' : null ,'class'=>'form-control oneFormed'.($errors->has( $parameter['parameter_user_type_key'] ) ?  " input-error" : "" ), 'title' => $parameter['name'])) !!}
                            {{--<small id="titleHelp" class="form-text text-muted"> Sed volutpat erat tellus, id porttitor velit vehicula ac. Donec vestibulum tortor at varius tempor.</small>--}}
                        </div>
                    </div>
                @elseif($parameter['code'] == 'birthday')
                    <div clasS="col-lg-4 form-label">
                        {!! Form::label($parameter['name'], $parameter['name'] . (ONE::isEdit() && $parameter['mandatory']  ? "*" : ""), array('class' => 'label-required color-secundary ' .($parameter['mandatory'] == 1 ? 'required' :null)  , 'format' => 'dd/mm/yyyy')) !!}
                    </div>
                    <div class="col-8">
                        <div class="form-group">
                            <input type="date" class="form-control" name="{{$parameter['parameter_user_type_key']}}" value="{{$parameter['value']}}" >
                        </div>
                    </div>
                @endif

            </div>
        @endforeach
    @endif
@endif