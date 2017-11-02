
{{--DISPLAY THE PAD'S TYPES--}}
@if($parameter = collect($parameters)->where('type_code','=','cbType')->first())
    @php
    $dataParameter = $parameter->section_type_parameter_key;
    $elementValue = ((!isset($language) || empty($language)) ? $parameter->section_param->value ?? "" : $parameter->section_param->translations->{$language}->value ?? "");
    $name = ($dataParameter ?? "") . "_" . $sectionNumber;
    $id = $name;
    $baseClasses = "form-control";
    $label = $parameter->value ?? trans("privateContentManager.unnamed_section_parameters");
    $cbTypeId = $name;
    @endphp
    @if(ONE::isEdit())
        <div class="form-group">
            <label for="{{ $id }}">{{ trans("privateContentManager.select_the_pad_type") }}</label>
            <select name="{{ $name }}" id="{{ $id }}" class="{{$cbTypeId}}" style="width:100%;" data-parameter="{{ $dataParameter }}"  >
                <option value=""></option>
                <option value="idea" {{ (isset($elementValue) ? $elementValue == 'idea' ? 'selected' : '' : '') }}>{{ trans("privateContentManager.idea") }}</option>
                <option value="proposal" {{ (isset($elementValue) ? $elementValue == 'proposal' ? 'selected' : '' : '') }}>{{ trans("privateContentManager.proposal") }}</option>
                <option value="project" {{ (isset($elementValue) ? $elementValue == 'project' ? 'selected' : '' : '') }}>{{ trans("privateContentManager.project") }}</option>
                <option value="qa" {{ (isset($elementValue) ? $elementValue == 'qa' ? 'selected' : '' : '') }}>{{ trans("privateContentManager.qa") }}</option>

            </select>
            <script>
                $(".{{$cbTypeId}}").select2({
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
    $name = ($dataParameter ?? "") . "_" . $sectionNumber;
    $id = $name;
    $baseClasses = "form-control";
    $label = $parameter->value ?? trans("privateContentManager.unnamed_section_parameters");
    @endphp
    @if(ONE::isEdit())
        <div class="form-group cbs-div-{{ $name }}" style="display: {{(isset($elementValue) ? 'block' : 'none') }};">
            <label for="{{ $id }}">{{ trans("privateContentManager.select_the_pad") }}</label>
            <select name="{{ $name }}" id="{{ $id }}" class="{{$name}}" style="width:100%;" data-parameter="{{ $dataParameter }}"  >
                @if(!empty($elementValue))
                    <option value="{{ $elementValue }}">{{ \App\ComModules\CB::getCb($elementValue)->title }}</option>
                @endif
                <option value=""></option>
            </select>
            <script>
                @if(isset($elementValue))
                    $(".{{$name}}").select2({
                    placeholder: '{{ trans("privateContentManager.select_the_pad") }}',
                    ajax: {
                        "url" : '{!! action('CbsController@getListOfCbsByType') !!}',
                        "type": "POST",
                        "data": function () {
                            return {
                                "_token": "{{ csrf_token() }}",
                                "type":  $(".{{ $cbTypeId }}").val(), // search term
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
                    $(".{{$name}}").select2({
                        placeholder: '{{ trans("privateContentManager.select_the_pad") }}',
                        ajax: {
                            "url" : '{!! action('CbsController@getListOfCbsByType') !!}',
                            "type": "POST",
                            "data": function () {
                                return {
                                    "_token": "{{ csrf_token() }}",
                                    "type":  $(".{{ $cbTypeId }}").val(), // search term
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
                    $(".cbs-div-{{ $name }}").show();
                });
                @endif


            </script>
        </div>
    @elseif (!empty($elelmentValue))
        {!! Form::oneText($name, $label, \App\ComModules\CB::getCb($elementValue)->title,['id'=> $id,'class' => $baseClasses, "data-parameter"=> $dataParameter]) !!}
    @endif
@endif

@include("private.contentManager.sectionTemplateParameter",["parameters" => $parameters])