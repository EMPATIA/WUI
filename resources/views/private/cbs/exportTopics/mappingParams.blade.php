<table class="table table-bordered table-hover table-striped ">
    <thead>
    <tr>
        <th width="70%">{{ trans('privateCbs.parameter') }}</th>
        <th>{{ trans('privateCbs.parameter_export') }}</th>
    </tr>
    </thead>
    <tbody>
    @foreach($parameters as $parameter)
        <tr role="row" class="{{($loop->iteration%2 == 0 ? 'even' : 'odd')}}">
            <td>
                <span style="display: block; min-height: 34px;">{{$parameter->parameter}}</span>
                @if(count($parameter->options) > 0 && $parametersExport->has($parameter->type->code))
                    <table class="table table-bordered">
                        <tbody>
                        @foreach($parameter->options as $option)
                            <tr role="row">
                                <td width="50%" style="text-align: center">
                                    <span class="text-center">{{$option->label}}</span>
                                </td>
                                @if($loop->first)
                                    <td rowspan="{{count($parameter->options)}}">
                                        <div id="options_export_{{$parameter->id}}">

                                        </div>
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                @endif
            </td>
            @if($parametersExport->has($parameter->type->code))
                <td>
                    <div class="form-group">
                        <select class="parameter_select" name="parameter_{{$parameter->id}}" id="{{$parameter->id}}" style="width:100%;">
                            <option value=""></option>
                            @foreach($parametersExport->get($parameter->type->code) as $parameterExport)
                                <option value="{{$parameterExport->id}}">{{$parameterExport->parameter}}</option>
                            @endforeach
                        </select>
                    </div>
                </td>
            @else
                <td>
                    {{ trans("privateCbs.no_parameter_of_same_type") }}
                </td>
            @endif
        </tr>
    @endforeach
    </tbody>
</table>

<script>
    $(".parameter_select").select2({
        placeholder: '{{ trans("privateCbs.select_the_parameter") }}'
    });

    $(".parameter_select").change(function() {
        var param_id = $(this).attr('id');
        var param_export_id = $(this).val();
        $.ajax({
            url: '{{action("CbsController@mappingParamOptions")}}',
            method: 'post',
            data: {
                cb_key: '{{$cbKey}}',
                cb_key_export: $("#pad_selected").val(),
                param_id: param_id,
                param_export_id: param_export_id,
                _token: "{{ csrf_token()}}"
            },
            success: function(response){
                $('#options_export_'+param_id).html(response);
            },
            error: function(msg){
            }
        });
    });
</script>
