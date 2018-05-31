@extends('private._private.index')

@section('content')

    @php $form = ONE::form('parameterType')
            ->settings(["model" => isset($parameterType) ? $parameterType : null,'id'=>isset($parameterType) ? $parameterType->id : null])
            ->show('ParameterTypesController@edit', 'ParameterTypesController@delete', ['parameterType' => isset($parameterType) ? $parameterType->id : null], 'ParameterTypesController@index', ['parameterType' => isset($parameterType) ? $parameterType->id : null])
            ->create('ParameterTypesController@store', 'ParameterTypesController@index', ['parameterType' => isset($parameterType) ? $parameterType->id : null])
            ->edit('ParameterTypesController@update', 'ParameterTypesController@show', ['parameterType' => isset($parameterType) ? $parameterType->id : null])
            ->open();
    @endphp

    {!! Form::oneText('code', trans('privateParameterTypes.code'), isset($parameterType) ? $parameterType->code : null, ['class' => 'form-control', 'id' => 'code']) !!}
    {!! Form::oneText('name', trans('privateParameterTypes.name'), isset($parameterType) ? $parameterType->name : null, ['class' => 'form-control', 'id' => 'name']) !!}

    {!! Form::oneSwitch("options", trans('privateParameterTypes.options'), isset($parameterType) ? $parameterType->options : 0, ['id' => 'options'])!!}

    @if(!empty($parameterType->param_add_fields))

        @endif
    {{--@if(ONE::actionType('parameterType') == 'show')--}}
    {{--{!! Form::oneSelect('fieldType', trans('privateParameterTypes.fieldTypes'), $fieldTypeSelect, null, null, ['class' => 'form-control', 'id' => 'fieldType']) !!}--}}
    {{--@endif--}}

    @if(ONE::actionType('parameterType') == 'edit')
        {!! Form::hidden('typesSelected', $selectedTypes ?? null, ['id' => 'typesSelected']) !!}

        <div style="margin-bottom: 100px">
            <div style="width: 25%; float: left">
                {!! Form::oneSelect('fieldType', trans('privateParameterTypes.fieldTypes'), $fieldTypeSelect, null, null, ['class' => 'form-control', 'id' => 'fieldType']) !!}
            </div>
            <div style="padding-top: 25px; margin-left: 15px; float:left">
                <button type="button" class="btn btn-flat btn-success btn-sm margin-top" onclick="getFields()">{{ trans('privateParameterTypes.create_new_field') }}</button>
            </div>
        </div>

        @foreach($parameterType->param_add_fields as $fields)
            @php $usedBoxes[] = $fields->field_type_id; @endphp
            <div class = "box" id="type_{{ $fields->field_type_id }}">
                <div class="box-header">

                    <h3 class="box-title">{{ $fields->code }}</h3>

                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                        </button>
                        <button type="button" class="btn btn-box-tool" onclick="removeInputs({{ $fields->field_type_id }})" data-widget="remove"><i class="fa fa-times"></i></button>
                    </div>
                </div>

                <div class = "box-body">
                    @if($fields->code == 'max_value')
                        {!! Form::oneText('max_value', trans('privateParameterTypes.max_value'), $fields->value, ['class' => 'form-control', 'id' => 'max_value']) !!}
                    @elseif($fields->code == 'min_value')
                        {!! Form::oneText('min_value', trans('privateParameterTypes.min_value'), $fields->value, ['class' => 'form-control', 'id' => 'min_value']) !!}
                    @elseif($fields->code == 'icon' || $fields->code == 'pin')
                        @php
                        $values = explode(",", $fields->value);
                        @endphp
                        <div style="margin-top: 10px; margin-bottom: 10px">
                            {!! Form::oneText('width'.$fields->code, trans('privateParameterTypes.width'), isset($values) ? $values[0] : null, ['class' => 'form-control', 'id' => 'width'.$fields->code]) !!}
                            {!! Form::oneText('height'.$fields->code, trans('privateParameterTypes.height'), isset($values) ? $values[1] : null, ['class' => 'form-control', 'id' => 'height'.$fields->code]) !!}
                        </div>
                    @else
                    @endif
                    @if(count($languages) > 0)
                        @foreach($languages as $language)

                            @php $form->openTabs($fields->code . 'tab-translation-' . $language->code, $language->name); @endphp
                            <div style="padding: 10px;">
                                {!! Form::oneText($fields->code.'_content_name_'.$language->code, trans('privateParameterTypes.name'), isset($fields->translations->{$language->code}) ? $fields->translations->{$language->code}->name : null,
                                ['class' => 'form-control', 'id' => $fields->code.'_content_name_'.$language->code]) !!}
                                {!! Form::oneTextArea($fields->code.'_content_description_'.$language->code, trans('privateParameterTypes.description'), isset($fields->translations->{$language->code}) ? $fields->translations->{$language->code}->description : null,
                                ['class' => 'form-control', 'id' => $fields->code.'_content_description_'.$language->code, 'size' => '30x7',]) !!}


                            </div>
                        @endforeach
                        @php $form->makeTabs(); @endphp
                    @endif
                </div>
            </div>


        @endforeach

        @foreach($fieldTypes as $type)
            @if(!in_array($type->id, $selectedTypesArray))
                <div class = "box" id="type_{{ $type->id }}" style="display: none;">
                    <div class="box-header">

                        <h3 class="box-title">{{ $type->name }}</h3>

                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                            </button>
                            <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                        </div>
                    </div>

                    <div class = "box-body">
                        @if($type->code == 'max_value')
                            {!! Form::oneText('max_value', trans('privateParameterTypes.max_value'), null, ['class' => 'form-control', 'id' => 'max_value']) !!}
                        @elseif($type->code == 'min_value')
                            {!! Form::oneText('min_value', trans('privateParameterTypes.min_value'), null, ['class' => 'form-control', 'id' => 'min_value']) !!}
                        @elseif($type->code == 'icon' || $type->code == 'pin')
                            <div style="margin-top: 10px; margin-bottom: 10px">
                                {!! Form::oneText('width'.$type->code, trans('privateParameterTypes.width'), null, ['class' => 'form-control', 'id' => 'width'.$type->code]) !!}
                                {!! Form::oneText('height'.$type->code, trans('privateParameterTypes.height'), null, ['class' => 'form-control', 'id' => 'height'.$type->code]) !!}
                            </div>
                        @else
                        @endif
                        @if(count($languages) > 0)
                            @foreach($languages as $language)
                                @php $form->openTabs($type->code . 'tab-translation-' . $language->code, $language->name); @endphp
                                <div style="padding: 10px;">
                                    {!! Form::oneText($type->code.'_content_name_'.$language->code, trans('privateParameterTypes.name'), null,
                                    ['class' => 'form-control', 'id' => $type->code.'_content_name_'.$language->code]) !!}
                                    {!! Form::oneTextArea($type->code.'_content_description_'.$language->code, trans('privateParameterTypes.description'), null,
                                    ['class' => 'form-control', 'id' => $type->code.'_content_description_'.$language->code, 'size' => '30x7',]) !!}


                                </div>
                            @endforeach
                            @php $form->makeTabs(); @endphp
                        @endif
                    </div>
                </div>
            @endif
        @endforeach
    @endif

    @if(ONE::actionType('parameterType') == 'create')

        {!! Form::hidden('typesSelected', '', ['id' => 'typesSelected']) !!}

        <div style="margin-bottom: 100px">
            <div style="width: 25%; float: left">
                {!! Form::oneSelect('fieldType', trans('privateParameterTypes.fieldTypes'), $fieldTypeSelect, null, null, ['class' => 'form-control', 'id' => 'fieldType']) !!}
            </div>
            <div style="padding-top: 23px; margin-left: 15px; float:left">
                <button type="button" class="btn btn-flat btn-submit btn-sm margin-top" onclick="getFields()">{{ trans('privateParameterTypes.create_new_field') }}</button>
            </div>
        </div>

        @foreach($fieldTypes as $type)
            <div class = "box" id="type_{{ $type->id }}" style="display: none;">
                <div class="box-header">

                    <h3 class="box-title">{{ $type->name }}</h3>

                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                        </button>
                        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                    </div>
                </div>

                <div class = "box-body">
                    @if($type->code == 'max_value')
                        {!! Form::oneText('max_value', trans('privateParameterTypes.max_value'), null, ['class' => 'form-control', 'id' => 'max_value']) !!}
                    @elseif($type->code == 'min_value')
                        {!! Form::oneText('min_value', trans('privateParameterTypes.min_value'), null, ['class' => 'form-control', 'id' => 'min_value']) !!}
                    @elseif($type->code == 'icon' || $type->code == 'pin')
                        <div style="margin-top: 10px; margin-bottom: 10px">
                            {!! Form::oneText('width'.$type->code, trans('privateParameterTypes.width'), null, ['class' => 'form-control', 'id' => 'width'.$type->code]) !!}
                            {!! Form::oneText('height'.$type->code, trans('privateParameterTypes.height'), null, ['class' => 'form-control', 'id' => 'height'.$type->code]) !!}
                        </div>
                    @else
                    @endif
                    @if(count($languages) > 0)
                        @foreach($languages as $language)
                            @php $form->openTabs($type->code . 'tab-translation-' . $language->code, $language->name); @endphp
                            <div style="padding: 10px;">
                                {!! Form::oneText($type->code.'_content_name_'.$language->code, trans('privateParameterTypes.name'), null,
                                ['class' => 'form-control', 'id' => $type->code.'_content_name_'.$language->code]) !!}
                                {!! Form::oneTextArea($type->code.'_content_description_'.$language->code, trans('privateParameterTypes.description'), null,
                                ['class' => 'form-control', 'id' => $type->code.'_content_description_'.$language->code, 'size' => '30x7',]) !!}


                            </div>
                        @endforeach
                        @php $form->makeTabs(); @endphp
                    @endif
                </div>
            </div>
        @endforeach
    @endif

    {!! $form->make() !!}

@endsection

@section('scripts')
    <script>
        function getFields(){

            var div = $("#fieldType").val();
            console.log(div)

            var selected_to_append = $("#typesSelected").val();
            $("#typesSelected").val(div + ',' + selected_to_append)

            $("#type_"+div).css('display', 'block');
        }

        function removeInputs(id){
            $.each($("#type"+id).next('input'), function(index, value){
                console.log(index)
            })
        }
    </script>
@endsection