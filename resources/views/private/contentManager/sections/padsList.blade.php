
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
        {!! Form::oneText($name, $label, \App\ComModules\CB::getCb($elementValue)->title,['id'=> $id,'class' => $baseClasses, "data-parameter"=> $dataParameter]) !!}
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

        $name = ($dataParameter ?? "") . "_" . $sectionNumber . (!empty($language) ? ("_" . $language) : "");
        $label = $parameter->name ?? trans("privateContentManager.unnamed_section_parameters");
        $id = $name;
        $baseClasses = "form-control";
    @endphp
    {!! Form::oneNumber($name, $label, $elementValue,['id'=> $id,'class' => $baseClasses, "data-parameter"=> $dataParameter]) !!}
@endif


{{--DISPLAY THE PAD TOPICS DISPLAY ORDER--}}
@if($parameter = collect($parameters)->where('type_code','=','topicsSortOrder')->first())
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
            <label for="{{ $id }}">{{ trans("privateContentManager.select_the_order") }}</label>
            <select name="{{ $name }}" id="{{ $id }}" class="cbSortOrder" style="width:100%;" data-parameter="{{ $dataParameter }}"  >
                <option value=""></option>
                <option value="order_by_recent" {{ (isset($elementValue) ? $elementValue == 'order_by_recent' ? 'selected' : '' : '') }}>{{ trans("privateContentManager.order_by_recent") }}</option>
                <option value="order_by_popular" {{ (isset($elementValue) ? $elementValue == 'order_by_popular' ? 'selected' : '' : '') }}>{{ trans("privateContentManager.order_by_popular") }}</option>
                <option value="order_by_post_count" {{ (isset($elementValue) ? $elementValue == 'order_by_post_count' ? 'selected' : '' : '') }}>{{ trans("privateContentManager.order_by_post_count") }}</option>
                <option value="order_random" {{ (isset($elementValue) ? $elementValue == 'order_random' ? 'selected' : '' : '') }}>{{ trans("privateContentManager.order_random") }}</option>
            </select>
            <script>
                $(".cbSortOrder").select2({
                    placeholder: '{{ trans("privateContentManager.select_the_order") }}',
                });
            </script>
        </div>
    @else
        {!! Form::oneText($name, $label, $elementValue,['id'=> $id,'class' => $baseClasses, "data-parameter"=> $dataParameter]) !!}
    @endif

@endif

