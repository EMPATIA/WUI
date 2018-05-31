@foreach($parameter->options as $option)
    <div class="form-group">
        <select class="parameter_option_select" name="option_{{$option->id}}" id="{{$option->id}}" style="width:100%;">
            <option value=""></option>
            @foreach($parameterExport->options as $parameterOption)
                <option value="{{$parameterOption->id}}">{{$parameterOption->label}}</option>
            @endforeach
        </select>
    </div>
@endforeach


<script>
    $(".parameter_option_select").select2({
        placeholder: '{{ trans("privateCbs.select_the_parameter_option") }}'
    });
</script>
