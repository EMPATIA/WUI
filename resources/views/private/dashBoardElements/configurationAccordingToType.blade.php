@php
    // Get element value
    $elementValue = "";
    foreach (!empty($userConfigurations) ? $userConfigurations : [] as $userConfiguration){
        if( $configuration->id == $userConfiguration->id){
            $elementValue = !empty($userConfiguration->pivot->value) ? $userConfiguration->pivot->value : "";
        }
    }
@endphp

<div class="form-control-wrapper">
    <label for="configuration_{{$configuration->id}}">{!! $configuration->title !!}</label>
    <div for="configuration_{{$configuration->id}}" style="font-size:x-small;">
        {!! $configuration->description !!}
    </div>
    @if($configuration->code == 'pad_type')
        <select name="{{ $configuration->id }}" id="configuration_{{ $configuration->id }}" class="cbTypes" style="width:100%;">
            <option value=""></option>
            <option value="all" {{ (isset($elementValue) ? $elementValue == 'all' ? 'selected' : '' : '') }}>
                {{ trans("privateDashBoardElements.all") }}
            </option>
            @if(ONE::verifyModuleAccess('cb','phase1'))
                <option value="phase1" {{ (isset($elementValue) ? $elementValue == 'phase1' ? 'selected' : '' : '') }}>
                    {{ trans("privateDashBoardElements.phase1") }}
                </option>
            @endif
            @if(ONE::verifyModuleAccess('cb','phase2'))
                <option value="phase2" {{ (isset($elementValue) ? $elementValue == 'phase2' ? 'selected' : '' : '') }}>
                    {{ trans("privateDashBoardElements.phase2") }}
                </option>
            @endif
            @if(ONE::verifyModuleAccess('cb','phase3'))
                <option value="phase3" {{ (isset($elementValue) ? $elementValue == 'phase3' ? 'selected' : '' : '') }}>
                    {{ trans("privateDashBoardElements.phase3") }}
                </option>
            @endif
            @if(ONE::verifyModuleAccess('cb','qa'))
                <option value="qa" {{ (isset($elementValue) ? $elementValue == 'qa' ? 'selected' : '' : '') }}>
                    {{ trans("privateDashBoardElements.q_a") }}
                </option>
            @endif
            @if(ONE::verifyModuleAccess('cb','forum'))
                <option value="forum" {{ (isset($elementValue) ? $elementValue == 'forum' ? 'selected' : '' : '') }}>
                    {{ trans("privateDashBoardElements.forum") }}
                </option>
            @endif
            @if(ONE::verifyModuleAccess('cb','discussion'))
                <option value="discussion" {{ (isset($elementValue) ? $elementValue == 'discussion' ? 'selected' : '' : '') }}>
                    {{ trans("privateDashBoardElements.discussion") }}
                </option>
            @endif
            @if(ONE::verifyModuleAccess('cb','proposal'))
                <option value="proposal" {{ (isset($elementValue) ? $elementValue == 'proposal' ? 'selected' : '' : '') }}>
                    {{ trans("privateDashBoardElements.proposal") }}
                </option>
            @endif
            @if(ONE::verifyModuleAccess('cb','idea'))
                <option value="idea" {{ (isset($elementValue) ? $elementValue == 'idea' ? 'selected' : '' : '') }}>
                    {{ trans("privateDashBoardElements.idea") }}
                </option>
            @endif
            @if(ONE::verifyModuleAccess('cb','proposal'))
                <option value="proposal" {{ (isset($elementValue) ? $elementValue == 'proposal' ? 'selected' : '' : '') }}>
                    {{ trans("privateDashBoardElements.proposal") }}
                </option>
            @endif
            @if(ONE::verifyModuleAccess('cb','publicConsultation'))
                <option value="publicConsultation" {{ (isset($elementValue) ? $elementValue == 'publicConsultation' ? 'selected' : '' : '') }}>
                    {{ trans("privateDashBoardElements.publicConsultation") }}
                </option>
            @endif
            @if(ONE::verifyModuleAccess('cb','tematicConsultation'))
                <option value="tematicConsultation" {{ (isset($elementValue) ? $elementValue == 'tematicConsultation' ? 'selected' : '' : '') }}>
                    {{ trans("privateDashBoardElements.tematicConsultation") }}
                </option>
            @endif
            @if(ONE::verifyModuleAccess('cb','survey'))
                <option value="survey" {{ (isset($elementValue) ? $elementValue == 'survey' ? 'selected' : '' : '') }}>
                    {{ trans("privateDashBoardElements.survey") }}
                </option>
            @endif
            @if(ONE::verifyModuleAccess('cb','project'))
                <option value="project" {{ (isset($elementValue) ? $elementValue == 'project' ? 'selected' : '' : '') }}>
                    {{ trans("privateDashBoardElements.project") }}
                </option>
            @endif
            @if(ONE::verifyModuleAccess('cb','project_2c'))
                <option value="project_2c" {{ (isset($elementValue) ? $elementValue == 'project_2c' ? 'selected' : '' : '') }}>
                    {{ trans("privateDashBoardElements.project_2c") }}
                </option>
            @endif
        </select>
        <script>
            $(".cbTypes").select2({
                placeholder: '{{ trans("privateContentManager.select_the_pad_type") }}',
            });

            @if(!empty($elementValue))
                setTimeout(function(){
                    $(".cbs").select2({
                        placeholder: '{{ trans("privateContentManager.select_the_pad") }}',
                        ajax: {
                            "url" : '{!! action('CbsController@getListOfCbsByType') !!}',
                            "type": "POST",
                            "data": function () {
                                return {
                                    "_token": "{{ csrf_token() }}",
                                    "type":  '{{ $elementValue }}', // search term
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
                }, 1000);
            @endif
        </script>
    @elseif($configuration->code == 'pad_key')
        <select name="{{ $configuration->id }}" id="configuration_{{ $configuration->id }}" class="cbs" style="width:100%;" >
            <option value=""></option>
        </select>
        <script>
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

            @if(!empty($elementValue))
                $('.cbs')
                    .empty() //empty select
                    .append($("<option/>") //add option tag in select
                        .val("{{ $elementValue }}") //set value for option to post it
                        .text("{{ \App\ComModules\CB::getCb($elementValue)->title }}")) //set a text for show in select
                    .val("{{ $elementValue }}") //select option of select2
                    .trigger("change"); //apply to select2
            @endif

        </script>
    @elseif($configuration->code == 'status_code')
        <select name="{{ $configuration->id }}" id="configuration_{{ $configuration->id }}" class="cbStatus" style="width:100%;" >
            <option value=""></option>
        </select>
        <script>
            $(".cbStatus").select2({
                placeholder: '{{ trans("privateContentManager.select_the_status") }}',
                ajax: {
                    "url" : '{!! action('CbsController@availableStatuses') !!}',
                    "type": "POST",
                    "data": function () {
                        return {
                            "_token": "{{ csrf_token() }}",
                        };
                    },
                    processResults: function (data) {
                        return {
                            results: $.map(data, function(item) {
                                return {
                                    text: item.name,
                                    id: item.status_type_key
                                }
                            })
                        };
                    }
                }
            });

            @if(!empty($elementValue))
                @php
                    try {
                        $statuses = App\ComModules\CB::getStatusTypes();
                        if (!empty($statuses))
                            $statusText = collect($statuses)->where("status_type_key",$elementValue)->first()->name ?? "";
                    } catch (Exception $e) {}

                    if (empty($statusText))
                        $statusText = trans("privateContentManager.selected_status");
                @endphp
                $('.cbStatus')
                    .empty() //empty select
                    .append($("<option/>") //add option tag in select
                        .val("{{ $elementValue }}") //set value for option to post it
                        .text("{{ $statusText }}")) //set a text for show in select
                    .val("{{ $elementValue }}") //select option of select2
                    .trigger("change"); //apply to select2
            @endif
        </script>
    @elseif($configuration->code == 'sort_order')
        <select name="{{ $configuration->id }}" id="configuration_{{ $configuration->id }}" class="sortOrder" style="width:100%;">
            <option value=""></option>
            <option value="rand" {{ (isset($elementValue) ? $elementValue == 'rand' ? 'selected' : '' : '') }}>{{ trans("privateDashBoardElements.rand") }}</option>
            <option value="asc" {{ (isset($elementValue) ? $elementValue == 'asc' ? 'selected' : '' : '') }}>{{ trans("privateDashBoardElements.asc") }}</option>
            <option value="desc" {{ (isset($elementValue) ? $elementValue == 'desc' ? 'selected' : '' : '') }}>{{ trans("privateDashBoardElements.desc") }}</option>
        </select>
        <script>
            $(".sortOrder").select2({
                placeholder: '{{ trans("privateContentManager.select_the_order") }}',
            });
        </script>
    @elseif($configuration->code == 'records_to_show')
        <input type="number" name="{{ $configuration->id }}" id="configuration_{{ $configuration->id }}" class="form-control" value="{{ $elementValue }}">
    @elseif($configuration->code == 'title' || $configuration->code == 'description')
        <input type="text" name="{{ $configuration->id }}" id="configuration_{{ $configuration->id }}" class="form-control" value="{{ $elementValue }}">
    @else
        {{ dd($configuration) }}
        <h1>text</h1>
    @endif
</div>