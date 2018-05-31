@if(isset($registerParameters))
    @if(!empty($registerParameters))
        <div class="row">
            <div class="col-12 col-sm-8 col-md-8 col-lg-8 no-padding">
                <br>
                <h5 class="form-title">{{ ONE::transSite("user_parameter_personal_data") }}</h5>
            </div>
        </div>
        @foreach($registerParameters as $parameter)
            <div class="row white-bg form-row">
                @if($parameter['parameter_type_code'] == 'text')
                    <div clasS="col-lg-4 form-label">
                        {!! Form::label($parameter['name'], $parameter['name'] . (ONE::isEdit() && $parameter['mandatory']  ? "*" : ""), array('class' => 'label-required color-secundary ' .($parameter['mandatory'] == 1 ? 'required' :null ) )) !!}
                    </div>
                    <div clasS="col-lg-8">
                        <div class="form-group">
                            {{--{!! Form::text($parameter['parameter_user_type_key'], $parameter['value'],  array($parameter['mandatory'] == true ? 'required' : null ,'class'=>'form-control oneFormed'.($errors->has( $parameter['parameter_user_type_key'] ) ?  " input-error" : "" ), 'title' => $parameter['name'], 'disabled' => @if(ONE::isEdit()) @endif)) !!}--}}
                            <input name="{{$parameter['parameter_user_type_key']}}" id="{{$parameter['parameter_user_type_key']}}" class="form-control oneFormed {{($errors->has( $parameter['parameter_user_type_key'] ) ?  " input-error" : "" )}}" value="{{(!empty($parameter['value']) ? $parameter['value'] : (!One::isEdit() ? ONE::transSite("user_parameter_non_defined") : null ))}}" @if(!ONE::isEdit()) disabled @endif/>
                            {{--<input type="text" class='form-control {{(($errors->has("name")) ? " input-error" : "")}}' @if(!ONE::isEdit()) disabled @endif value="{{(!empty($user->name) ? $user->name : (!One::isEdit() ? trans("parameter_non_defined") : null ))}}" name="name" id="name">--}}
                        </div>
                    </div>
                @elseif($parameter['parameter_type_code'] == 'text_area')
                    <div clasS="col-lg-4 form-label">
                        {!! Form::label($parameter['parameter_user_type_key'], $parameter['name'] . (ONE::isEdit() && $parameter['mandatory']  ? "*" : ""), array('class' => 'label-required color-secundary ' .($parameter['mandatory'] == "1" ? 'required':null))) !!}
                    </div>
                    <div clasS="col-lg-8">
                        <div class="form-group">
                            {!! Form::textarea($parameter['parameter_user_type_key'], $parameter['value'],  array('class'=>'form-control oneFormed'.($errors->has( $parameter['parameter_user_type_key'] ) ?  " input-error" : "" ), 'required' => ($parameter['mandatory'] == true ? 'required' : null), 'title' => $parameter['name'])) !!}
                        </div>
                    </div>
                @elseif($parameter['parameter_type_code'] == 'numeric')
                    <div clasS="col-lg-4 form-label">
                        {!! Form::label($parameter['parameter_user_type_key'], $parameter['name'] . (ONE::isEdit() && $parameter['mandatory']  ? "*" : ""), array('class' => 'label-required color-secundary ' .($parameter['mandatory'] == "1" ? 'required':null))) !!}
                    </div>
                    <div clasS="col-lg-8">
                        <div class="form-group">
                            @if (ONE::isEdit())
                                {!! Form::number($parameter['parameter_user_type_key'], $parameter['value'],  array('class'=>'form-control oneFormed', 'required' => ($parameter['mandatory'] == "1" ? 'required' : null), 'title' => $parameter['name'])) !!}
                            @else
                                @if (!empty($parameter['value']))
                                    {{ $parameter['value'] }}
                                @else
                                    {{ ONE::transSite("user_parameter_non_defined") }}
                                @endif
                            @endif
                        </div>
                    </div>
                @elseif($parameter['parameter_type_code'] == 'radio_buttons')
                    @if(count($parameter['parameter_user_options'])> 0)
                        <div clasS="col-lg-4 form-label">
                            {!! Form::label($parameter['parameter_user_type_key'], $parameter['name'] . (ONE::isEdit() && $parameter['mandatory']  ? "*" : ""), array('class' => 'col-md-2 col-xs-12 ' .($parameter['mandatory'] == "1" ? 'required':null))) !!}
                        </div>
                        <div clasS="col-lg-8">
                            <div class="form-group">
                                @if (ONE::isEdit())
                                    @foreach($parameter['parameter_user_options'] as $option)
                                        <div class="radio">
                                            <label>
                                                <input role="radiogroup" aria-label="{!! $parameter['name'] !!}" type="radio" name="{{$parameter['parameter_user_type_key']}}" title="{!! $parameter['name'] !!}" value="{{$option['parameter_user_option_key']}}" @if($parameter['mandatory']) required @endif @if($option['selected'] || old($parameter['parameter_user_type_key'])==$option['parameter_user_option_key']) checked @endif>{{$option['name']}}
                                            </label>
                                        </div>
                                    @endforeach
                                @else
                                    <?php $hasValue = false;?>
                                    @foreach($parameter['parameter_user_options'] as $option)
                                        @if($option['selected'])
                                            <?php $hasValue = true;?>
                                            {{$option['name']}}
                                        @endif
                                    @endforeach
                                    @if (!$hasValue)
                                        {{ ONE::transSite("user_parameter_non_defined") }}
                                    @endif
                                @endif
                            </div>
                        </div>
                    @endif
                @elseif($parameter['parameter_type_code'] == 'gender')
                    @if(count($parameter['parameter_user_options'])> 0)
                        <div clasS="col-lg-4 form-label">
                            {!! Form::label($parameter['name'], $parameter['name'] . (ONE::isEdit() && $parameter['mandatory']  ? "*" : ""), array('class' => 'col-md-2 col-xs-12 ' .($parameter['mandatory'] == "1" ? 'required':null))) !!}
                        </div>
                        <div clasS="col-lg-8">
                            <div class="form-group">
                                @if (ONE::isEdit())
                                    @foreach($parameter['parameter_user_options'] as $option)
                                        <div class="radio">
                                            <label>
                                                <input role="radiogroup" aria-label="{!! $parameter['name'] !!}" type="radio" name="{{$parameter['parameter_user_type_key']}}" title="{!! $parameter['name'] !!}" value="{{$option['parameter_user_option_key']}}" @if($parameter['mandatory']) required @endif @if($option['selected']) checked @endif>{{$option['name']}}
                                            </label>
                                        </div>
                                    @endforeach
                                @else
                                    <?php $hasValue = false;?>
                                    @foreach($parameter['parameter_user_options'] as $option)
                                        @if($option['selected'])
                                            <?php $hasValue = true;?>
                                            {{$option['name']}}
                                        @endif
                                    @endforeach
                                    @if (!$hasValue)
                                        {{ ONE::transSite("user_parameter_non_defined") }}
                                    @endif
                                @endif
                            </div>
                        </div>
                    @endif
                @elseif($parameter['parameter_type_code'] == 'check_box')
                    @if(count($parameter['parameter_user_options'])> 0)
                        <div clasS="col-lg-4 form-label">
                            {!! Form::label($parameter['name'], $parameter['name'] . (ONE::isEdit() && $parameter['mandatory']  ? "*" : ""), array('class' => ($parameter['mandatory'] == "1" ? 'required':null))) !!}
                        </div>
                        <div clasS="col-lg-8">
                            <div class="form-group">
                                @if (ONE::isEdit())
                                    @foreach($parameter['parameter_user_options'] as $option)
                                        <div class="checkbox">
                                            <label><input type="checkbox" value="{{$option['parameter_user_option_key']}}" title="{!! $parameter['name'] !!}" name="{{$parameter['parameter_user_type_key']}}[]" @if($parameter['mandatory']) required @endif @if($option['selected'] || old($parameter['parameter_user_type_key'])==$option['parameter_user_option_key']) checked @endif>{{$option['name']}}</label>
                                        </div>
                                    @endforeach
                                @else
                                    <?php $hasValue = false;?>
                                    @foreach($parameter['parameter_user_options'] as $option)
                                        @if($option['selected'])
                                            <?php $hasValue = true;?>
                                            {{$option['name']}}
                                        @endif
                                    @endforeach
                                    @if (!$hasValue)
                                        {{ ONE::transSite("user_parameter_non_defined") }}
                                    @endif
                                @endif
                            </div>
                        </div>
                    @endif
                @elseif($parameter['parameter_type_code'] == 'dropdown')
                    <div clasS="col-lg-4 form-label">
                        {!! Form::label($parameter['parameter_user_type_key'], $parameter['name'] . (ONE::isEdit() && $parameter['mandatory']  ? "*" : ""), array('class' => 'label-required color-secundary ' .($parameter['mandatory'] == "1" ? 'required':null))) !!}
                    </div>
                    <div clasS="col-lg-8">
                        <div class="form-group">
                            @if (ONE::isEdit())
                                <div class="field-wrapper">
                                    <select class="form-control @if($errors->has( $parameter['parameter_user_type_key'] )) input-error @endif" id="{{$parameter['parameter_user_type_key']}}" name="{{$parameter['parameter_user_type_key']}}" @if($parameter['mandatory']) required @endif>
                                        <option value="" selected>{{ ONE::transSite("user_parameter_select_option") }}</option>
                                        @foreach($parameter['parameter_user_options'] as $option)
                                            <option value="{{$option['parameter_user_option_key']}}" @if($option['selected'] || old($parameter['parameter_user_type_key'])==$option['parameter_user_option_key']) selected @endif>{{$option['name']}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @else
                                <?php $hasValue = false;?>
                                @foreach($parameter['parameter_user_options'] as $option)
                                    @if($option['selected'])
                                        <?php $hasValue = true;?>
                                        {{$option['name']}}
                                    @endif
                                @endforeach
                                @if (!$hasValue)
                                    {{ ONE::transSite("user_parameter_non_defined") }}
                                @endif
                            @endif
                        </div>
                    </div>
                @elseif($parameter['parameter_type_code'] == 'mobile')
                    <div clasS="col-lg-4 form-label">
                        {!! Form::label($parameter['name'], $parameter['name'] . (ONE::isEdit() && $parameter['mandatory']  ? "*" : ""), array('class' => 'label-required color-secundary ' .($parameter['mandatory'] == 1 ? 'required' :null ) )) !!}
                    </div>
                    <div clasS="col-lg-8">
                        <div class="form-group">
                            @if (ONE::isEdit())
                                {!! Form::text($parameter['parameter_user_type_key'], $parameter['value'],  array($parameter['mandatory'] == true ? 'required' : null ,'class'=>'form-control oneFormed'.($errors->has( $parameter['parameter_user_type_key'] ) ?  " input-error" : "" ), 'title' => $parameter['name'])) !!}
                            @else
                                @if (!empty($parameter['value']))

                                    {{ $parameter['value'] }}
                                @else
                                    {{ ONE::transSite("user_parameter_non_defined") }}
                                @endif
                            @endif
                        </div>
                    </div>
                @elseif($parameter['parameter_code'] == 'birthday')
                    <div clasS="col-lg-4 form-label">
                        {!! Form::label($parameter['name'], $parameter['name'] . (ONE::isEdit() && $parameter['mandatory']  ? "*" : ""), array('class' => 'label-required color-secundary ' .($parameter['mandatory'] == 1 ? 'required' :null ) )) !!}
                    </div>
                    <div class="col-8">
                        <div class="form-group">
                            <input type="date" class="form-control" name="{{$parameter['parameter_user_type_key']}}" value="{{(!empty($parameter['value']) ? $parameter['value'] : (!One::isEdit() ? ONE::transSite("user_parameter_non_defined") : null ))}}" @if(!ONE::isEdit()) disabled @endif >
                        </div>
                    </div>
                @endif
            </div>
        @endforeach
    @endif
@endif
