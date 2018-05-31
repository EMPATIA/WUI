
{{--DISPLAY THE PAD'S TYPES--}}
@if($parameter = collect($parameters)->where('type_code','=','cbType')->first())
    @php
        $dataParameter = $parameter->section_type_parameter_key;
        $elementValue = ((!isset($language) || empty($language)) ? $parameter->section_param->value ?? "" : $parameter->section_param->translations->{$language}->value ?? "");
        $name = ($dataParameter ?? "") . "_" . $sectionNumber . (!empty($language) ? ("_" . $language) : "");
        $id = $name;
        $baseClasses = "form-control";
        $label = $parameter->value ?? trans("privateContentManager.unnamed_section_parameters");
    @endphp
    @if(ONE::isEdit())
        <div class="form-group">
            <label for="{{ $id }}">{{ trans("privateContentManager.select_the_pad_type") }}</label>
            <select name="{{ $name }}" id="{{ $id }}" class="cbTypes" style="width:100%;" data-parameter="{{ $dataParameter }}"  >
                <option value=""></option>
                <option value="idea" {{ (isset($elementValue) ? $elementValue == 'idea' ? 'selected' : '' : '') }}>{{ trans("privateContentManager.idea") }}</option>
                <option value="proposal" {{ (isset($elementValue) ? $elementValue == 'proposal' ? 'selected' : '' : '') }}>{{ trans("privateContentManager.proposal") }}</option>
                <option value="project" {{ (isset($elementValue) ? $elementValue == 'project' ? 'selected' : '' : '') }}>{{ trans("privateContentManager.project") }}</option>
                <option value="qa" {{ (isset($elementValue) ? $elementValue == 'qa' ? 'selected' : '' : '') }}>{{ trans("privateContentManager.qa") }}</option>
                <option value="event" {{ (isset($elementValue) ? $elementValue == 'event' ? 'selected' : '' : '') }}>{{ trans("privateContentManager.event") }}</option>
            </select>
            <script>
                $(".cbTypes").select2({
                    placeholder: '{{ trans("privateContentManager.select_the_pad_type") }}',
                });
            </script>
        </div>
    @else
        {!! Form::oneText($name, $label, $elementValue,['id'=> $id,'class' => $baseClasses, "data-parameter"=> $dataParameter]) !!}
    @endif
@endif


{{--DISPLAY THE PAD KEY--}}
@if($parameter = collect($parameters)->where('type_code','=','select_cb_key')->first())
    @php
        $dataParameter = $parameter->section_type_parameter_key;
        $elementValue = ((!isset($language) || empty($language)) ? $parameter->section_param->value ?? "" : $parameter->section_param->translations->{$language}->value ?? "");
        $name = ($dataParameter ?? "") . "_" . $sectionNumber . (!empty($language) ? ("_" . $language) : "");
        $id = $name;
        $baseClasses = "form-control";
        $label = $parameter->value ?? trans("privateContentManager.unnamed_section_parameters");
    @endphp
    @if(ONE::isEdit())
        <div class="form-group cbs-div" style="display: {{(isset($elementValue) ? 'block' : 'none') }};">
            <label for="{{ $id }}">{{ trans("privateContentManager.select_the_pad") }}</label>
            <select name="{{ $name }}" id="{{ $id }}" class="cbs" style="width:100%;" data-parameter="{{ $dataParameter }}"  >
                @if(!empty($elementValue))

                    <option value="{{ $elementValue }}">{{ \App\ComModules\CB::getCb($elementValue)->title }}</option>
                @endif
                <option value=""></option>
            </select>
            <script>
                @if(isset($elementValue))
                    $(".cbs").select2({
                        placeholder: '{{ trans("privateContentManager.select_the_pad") }}',
                        ajax: {
                            "url" : '{!! action('CbsController@getListOfCbsByType') !!}',
                            "type": "POST",
                            "data": function () {
                                return {
                                    "_token": "{{ csrf_token() }}",
                                    "type":  $(".cbTypes").val(), // search term
                                };
                            },
                            processResults: function (data) {
                                return {
                                    results: $.map(data, function(item) {
                                        return {
                                            text: item.title,
                                            id: item.cb_key
                                        }
                                    })
                                };
                            }
                        }
                    });
                @else
                $(document).on('change','.cbTypes',function(){
                    $(".cbs").select2({
                        placeholder: '{{ trans("privateContentManager.select_the_pad") }}',
                        ajax: {
                            "url" : '{!! action('CbsController@getListOfCbsByType') !!}',
                            "type": "POST",
                            "data": function () {
                                return {
                                    "_token": "{{ csrf_token() }}",
                                    "type":  $(".cbTypes").val(), // search term
                                };
                            },
                            processResults: function (data) {
                                return {
                                    results: $.map(data, function(item) {
                                        return {
                                            text: item.title,
                                            id: item.cb_key
                                        }
                                    })
                                };
                            }
                        }
                    });
                    $(".cbs-div").show();
                });
                @endif


            </script>
        </div>
    @else
        {!! Form::oneText($name, $label, !empty($elementValue) ? \App\ComModules\CB::getCb($elementValue)->title : null,['id'=> $id,'class' => $baseClasses, "data-parameter"=> $dataParameter]) !!}
    @endif
@endif


{{--DISPLAY THE PAD NUMBER OF TOPICS TO DISPLAY--}}
@if($parameter = collect($parameters)->where('type_code','=','url')->first())


    @php
        if (!isset($language) || empty($language))
            $dataParameter = $parameter->section_type_parameter_key;
        else
            $dataParameter = $parameter->section_type_parameter_key . "_" . $language;

        $elementValue = ((!isset($language) || empty($language)) ? $parameter->section_param->value ?? "" : $parameter->section_param->translations->{$language}->value ?? "");

        $name = ($dataParameter ?? "") . "_" . $sectionNumber . (!empty($language) ? ("_" . $language) : "");
        $label = $parameter->name ?? trans("privateContentManager.unnamed_section_parameters");
        $id = $name;
        $baseClasses = "form-control";
    @endphp
    {!! Form::oneText($name, $label, $elementValue,['id'=> $id,'class' => $baseClasses, "data-parameter"=> $dataParameter,'type'=>"url"]) !!}
@endif


{{--DISPLAY THE PAD NUMBER OF TOPICS TO DISPLAY--}}
@if($parameter = collect($parameters)->where('type_code','=','date')->first())

    @php
        if (!isset($language) || empty($language))
            $dataParameter = $parameter->section_type_parameter_key;
        else
            $dataParameter = $parameter->section_type_parameter_key . "_" . $language;

        $elementValue = ((!isset($language) || empty($language)) ? $parameter->section_param->value ?? "" : $parameter->section_param->translations->{$language}->value ?? "");

        $name = ($dataParameter ?? "") . "_" . $sectionNumber . (!empty($language) ? ("_" . $language) : "");
        $label = $parameter->name ?? trans("privateContentManager.unnamed_section_parameters");
        $id = $name;
        $baseClasses = "form-control";
    @endphp
    {!! Form::oneDate($name, $label, $elementValue,['id'=> $id,'class' => $baseClasses . " oneDatePicker", "data-parameter"=> $dataParameter]) !!}
@endif


{{--DISPLAY THE PAD NUMBER OF TOPICS TO DISPLAY--}}
@if($parameter = collect($parameters)->where('type_code','=','html')->first())

    @php
        if (!isset($language) || empty($language))
            $dataParameter = $parameter->section_type_parameter_key;
        else
            $dataParameter = $parameter->section_type_parameter_key . "_" . $language;

        $elementValue = ((!isset($language) || empty($language)) ? $parameter->section_param->value ?? "" : $parameter->section_param->translations->{$language}->value ?? "");

        $name = ($dataParameter ?? "") . "_" . $sectionNumber . (!empty($language) ? ("_" . $language) : "");
        $label = $parameter->name ?? trans("privateContentManager.unnamed_section_parameters");
        $id = $name;
        $baseClasses = "form-control";
    @endphp
    {!! Form::oneTextArea($name, $label, $elementValue,['id'=> $id,'class' => $baseClasses . ' mceEdit', "data-parameter"=> $dataParameter]) !!}
@endif


@if($parameter = collect($parameters)->where('type_code','=','color')->first())
    @php
        if (!isset($language) || empty($language))
            $dataParameter = $parameter->section_type_parameter_key;
        else
            $dataParameter = $parameter->section_type_parameter_key . "_" . $language;

        $elementValue = ((!isset($language) || empty($language)) ? $parameter->section_param->value ?? "" : $parameter->section_param->translations->{$language}->value ?? "");

        $name = ($dataParameter ?? "") . "_" . $sectionNumber . (!empty($language) ? ("_" . $language) : "");
        $label = $parameter->name ?? trans("privateContentManager.unnamed_section_parameters");
        $id = $name;
        $baseClasses = "form-control";
    @endphp
    @if (!One::isEdit() || (empty(Session::get("SITE-CONFIGURATION.color_primary","")) && empty(Session::get("SITE-CONFIGURATION.color_secondary",""))))
        {!! Form::oneColor($name, $label, !empty($elementValue) ? $elementValue : "#000000",['id'=> $id,'class' => $baseClasses, "data-parameter"=> $dataParameter]) !!}
    @else
        <div class="form-group row">
            <div class="col-lg-8 col-12">
                {!! Form::oneColor($name, $label, !empty($elementValue) ? $elementValue : "#000000",['id'=> $id,'class' => $baseClasses, "data-parameter"=> $dataParameter]) !!}
            </div>
            @if (!empty(Session::get("SITE-CONFIGURATION.color_primary","")))
                <div class="col-lg-2 col-md-6 col-12">
                    <a href="#" data-control="#{{ $id }}" data-color="{{ Session::get("SITE-CONFIGURATION.color_primary") }}" class="row">
                    <div class="col-12" style="padding:0;">{{ trans("privateContentManager.use_primary_color") }}</div>
                        <div class="col-12" style="background-color:{{ Session::get("SITE-CONFIGURATION.color_primary") }}">&nbsp;</div>
                    </a>
                </div>
            @endif
            @if (!empty(Session::get("SITE-CONFIGURATION.color_secondary","")))
                <div class="col-lg-2 col-md-6 col-12">
                    <a href="#" data-control="#{{ $id }}" data-color="{{ Session::get("SITE-CONFIGURATION.color_secondary") }}" class="row">
                    <div class="col-12" style="padding:0;">{{ trans("privateContentManager.use_secondary_color") }}</div>
                        <div class="col-12" style="background-color:{{ Session::get("SITE-CONFIGURATION.color_secondary") }}">&nbsp;</div>
                    </a>
                </div>
            @endif
            <script>
                $("a[data-control='#{{ $id }}']").on("click",function(e) {
                    e.preventDefault();
                    $($(this).attr("data-control")).val($(this).attr("data-color"));
                });
            </script>
        </div>
    @endif
@endif