<div class="card-body">
    @if(in_array('color', $fieldTypes))
        <div class="form-group">
            <label for="color">{{ trans('privateCbsParameters.color') }}</label>
            <input type="color" name="color" id="color" class="form-control">
        </div>
    @endif
    @if(in_array('min_value', $fieldTypes))
        <div class="form-group">
            <label for="min_value">{{ trans('privateCbsParameters.min_value') }}</label>
            <input type="text" name = "min_value" id="option_min_value" class="form-control">
        </div>
    @endif
    @if(in_array('max_value', $fieldTypes))
        <div class="form-group">
            <label for="max_value">{{ trans('privateCbsParameters.max_value') }}</label>
            <input type="text" name = "max_value" id="option_max_value" class="form-control">
        </div>
    @endif
    @if(in_array('icon', $fieldTypes))
        <div class="form-group">
            {!! Form::oneFileUpload("icon", trans('privateCbsParameters.icon'), [], $uploadKey, array("readonly"=> false)) !!}
        </div>
    @endif
    @if(in_array('pin', $fieldTypes))
        <div class="form-group">
            {!! Form::oneFileUpload("pin", trans('privateCbsParameters.pin'), [], $uploadKey, array("readonly"=> false)) !!}
        </div>
    @endif
</div>