
{{--DISPLAY THE PAD'S TYPES--}}
@if($parameter = collect($parameters)->where('type_code','=','contentType')->first())
    @php
    $dataParameter = $parameter->section_type_parameter_key;
    $elementValue = ((!isset($language) || empty($language)) ? $parameter->section_param->value ?? "" : $parameter->section_param->translations->{$language}->value ?? "");
    $name = ($dataParameter ?? "") . "_" . $sectionNumber;
    $id = $name;
    $baseClasses = "form-control";
    $label = $parameter->value ?? trans("privateContentManager.unnamed_section_parameters");
    @endphp
    @if(ONE::isEdit())
        <div class="form-group">
            <label for="{{ $id }}">{{ trans("privateContentManager.select_the_content_type") }}</label>
            <select name="{{ $name }}" id="{{ $id }}" class="contentTypes" style="width:100%;" data-parameter="{{ $dataParameter }}"  >
                <option value=""></option>
                <option value="articles" {{ (isset($elementValue) ? $elementValue == 'articles' ? 'selected' : '' : '') }}>{{ trans("privateContentManager.articles") }}</option>
                <option value="events" {{ (isset($elementValue) ? $elementValue == 'events' ? 'selected' : '' : '') }}>{{ trans("privateContentManager.events") }}</option>
                <option value="pages" {{ (isset($elementValue) ? $elementValue == 'pages' ? 'selected' : '' : '') }}>{{ trans("privateContentManager.pages") }}</option>
                <option value="news" {{ (isset($elementValue) ? $elementValue == 'news' ? 'selected' : '' : '') }}>{{ trans("privateContentManager.news") }}</option>
                <option value="faqs" {{ (isset($elementValue) ? $elementValue == 'faqs' ? 'selected' : '' : '') }}>{{ trans("privateContentManager.faqs") }}</option>
                <option value="municipal_faqs" {{ (isset($elementValue) ? $elementValue == 'municipal_faqs' ? 'selected' : '' : '') }}>{{ trans("privateContentManager.municipal_faqs") }}</option>
                <option value="gatherings" {{ (isset($elementValue) ? $elementValue == 'gatherings' ? 'selected' : '' : '') }}>{{ trans("privateContentManager.gatherings") }}</option>

            </select>
            <script>
                $(".contentTypes").select2({
                    placeholder: '{{ trans("privateContentManager.select_the_content_type") }}',
                });
            </script>
        </div>
    @else
        {!! Form::oneText($name, $label, $elementValue,['id'=> $id,'class' => $baseClasses, "data-parameter"=> $dataParameter]) !!}
    @endif
@endif


{{--DISPLAY THE PAD NUMBER OF TOPICS TO DISPLAY--}}
@if($parameter = collect($parameters)->where('type_code','=','numberOfTopics')->first())
    @php
    if (!isset($language) || empty($language))
        $dataParameter = $parameter->section_type_parameter_key;
    else
        $dataParameter = $parameter->section_type_parameter_key . "_" . $language;

    $elementValue = ((!isset($language) || empty($language)) ? $parameter->section_param->value ?? "" : $parameter->section_param->translations->{$language}->value ?? "");

    $name = ($dataParameter ?? "") . "_" . $sectionNumber;
    $label = $parameter->name ?? trans("privateContentManager.unnamed_section_parameters");
    $id = $name;
    $baseClasses = "form-control";
    @endphp
    {!! Form::oneNumber($name, $label, $elementValue,['id'=> $id,'class' => $baseClasses, "data-parameter"=> $dataParameter]) !!}
@endif
