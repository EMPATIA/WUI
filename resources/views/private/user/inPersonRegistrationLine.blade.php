<tr class="fields">
    <td><input type="text" name="code" class="form-control" required></td>
    <td><input type="text" name="name" class="form-control" required></td>
    <td><input type="text" name="surname" class="form-control"></td>
    <td><input type="text" name="email" class="form-control"></td>
    @foreach($parameters as $parameter)
        <td>
            @if($parameter->parameter_type->code == 'dropdown')
                <select class="form-control" id="{{$parameter->parameter_user_type_key}}"
                        name="parameter_{{$parameter->parameter_user_type_key}}"
                        @if($parameter->mandatory) required @endif>
                    <option value="" selected>{{trans("user.select_option")}}</option>
                    @foreach($parameter->parameter_user_options as $option)
                        <option value="{{$option->parameter_user_option_key}}">{{$option->name}}</option>
                    @endforeach
                </select>
            @else
                <input type="text" name="parameter_{{$parameter->parameter_user_type_key}}" class="form-control" @if (isset($parameter->mandatory) && $parameter->mandatory) required @endif>
            @endif
        </td>
    @endforeach
</tr>