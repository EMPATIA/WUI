@forelse($sectionType->section_type_parameters as $parameter)
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
    {{-- Text Related Parameters --}}
    @if($parameter->type_code == 'text')
        {!! Form::oneText($name, $label, $elementValue,['id'=> $id,'class' => $baseClasses, "data-parameter"=> $dataParameter]) !!}
    @elseif($parameter->type_code == 'textarea')
        {!! Form::oneTextArea($name, $label, $elementValue,['id'=> $id,'class' => $baseClasses, "data-parameter"=> $dataParameter]) !!}
    @elseif($parameter->type_code == 'html')
        {!! Form::oneTextArea($name, $label, $elementValue,['id'=> $id,'class' => $baseClasses . ' mceEdit', "data-parameter"=> $dataParameter]) !!}
    {{-- Images Related Parameters --}}
    @elseif($parameter->type_code == 'images_single')
        {!! Form::oneFileUpload($name, $label, (!empty($elementValue) ? json_decode($elementValue) : []), $uploadKey ?? null,["filesCountLimit"=>1,"acceptedtypes"=>"images"]) !!}
    @elseif($parameter->type_code == 'images_multiple')
        {!! Form::oneFileUpload($name, $label, (!empty($elementValue) ? json_decode($elementValue) : []), $uploadKey ?? null, ["acceptedtypes"=>"images"]) !!}
    {{-- Video Parameters --}}
    @elseif($parameter->type_code == 'video')
        {!! Form::oneFileUpload($name, $label, (!empty($elementValue) ? json_decode($elementValue) : []), $uploadKey ?? null,["filesCountLimit"=>1,"acceptedtypes"=>"videos"]) !!}
    {{-- Files Related Parameters --}}
    @elseif($parameter->type_code == 'files_single')
        {!! Form::oneFileUpload($name, $label, (!empty($elementValue) ? json_decode($elementValue) : []), $uploadKey ?? null,["filesCountLimit"=>1]) !!}
    @elseif($parameter->type_code == 'files_multiple')
        {!! Form::oneFileUpload($name, $label, (!empty($elementValue) ? json_decode($elementValue) : []), $uploadKey ?? null) !!}
    {{-- Other Generic arameters --}}
    @elseif ($parameter->type_code == "checkbox")
        {!! Form::oneSwitch($name, $label, $elementValue, ['id'=> $id,'class' => $baseClasses, "data-parameter"=> $dataParameter]) !!}
    @elseif($parameter->type_code == 'number')
        {!! Form::oneNumber($name, $label, $elementValue,['id'=> $id,'class' => $baseClasses, "data-parameter"=> $dataParameter]) !!}
    @elseif($parameter->type_code == 'color')
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
    @elseif($parameter->type_code == 'date')
        {!! Form::oneDate($name, $label, $elementValue,['id'=> $id,'class' => $baseClasses . " oneDatePicker", "data-parameter"=> $dataParameter]) !!}
    @elseif($parameter->type_code == 'time')
        {!! Form::oneTime($name, $label, $elementValue,['id'=> $id,'class' => $baseClasses, "data-parameter"=> $dataParameter]) !!}
    @elseif($parameter->type_code == 'url')
        {!! Form::oneText($name, $label, $elementValue,['id'=> $id,'class' => $baseClasses, "data-parameter"=> $dataParameter,'type'=>"url"]) !!}
    @elseif($parameter->type_code == 'alignment')
        {!! Form::oneSelect($name, $label, ["left" => trans("privateContentManager.left"),"center" => trans("privateContentManager.center"),"right" => trans("privateContentManager.right"),], $elementValue, null,["id"=>$id,'class'=>$baseClasses,"data-parameter"=> $dataParameter]) !!}
    {{-- Heading Parameter --}}
    @elseif($parameter->type_code == 'heading_number')
        {!! Form::oneNumber($name, $label, $elementValue,['id'=> $id,'class' => $baseClasses, "data-parameter"=> $dataParameter, "min"=>1,"max"=>6]) !!}
    @elseif($parameter->type_code == 'heading_1' || $parameter->type_code == 'heading_2' || $parameter->type_code == 'heading_3' || $parameter->type_code == 'heading_4' || $parameter->type_code == 'heading_5' || $parameter->type_code == 'heading_6')
        {!! Form::oneText($name, $label, $elementValue,['id'=> $id,'class' => $baseClasses, "data-parameter"=> $dataParameter]) !!}
    @elseif($parameter->type_code == 'cbType' || $parameter->type_code == 'select_cb_key')
        {{-- This parameters are defined within custom section builders. They are known but shouldn't show anything if they come here --}}
    @else
        {{-- Fallback for unknown parameter --}}
        <div>
            {{ trans("privateContentManager.unrecognized_parameter") }}: {{ $parameter->type_code }}
        </div>
    @endif
@empty
    {{ trans("privateContentManager.no_parameters_for_section") }}
@endforelse