<div class=" top-2"  id="accordion" role="tablist" aria-multiselectable="true" id="example">
        <div class="card">
            <div class="card-header card-header-gray" role="tab" id="collapse-summary-title">
                <div class="group_{!! $optionField !!}">
                    <a role="button" class="title_{!! $optionField !!}" data-toggle="collapse" data-parent="#collapse-{!! $optionField !!}" href="#collapse-{!! $optionField !!}" aria-expanded="true" aria-controls="collapse-{!! $optionField !!}">
                </a>
            </div>
            <div id="collapse-{!! $optionField !!}" class="panel-collapse collapse" role="tabpanel" aria-labelledby="collapse-summary-title">
                <div class="card-body">
                    <div class="form-group">
                        <label for="option_code_{!! $optionField !!}">{{ trans('privateCbsParameters.code') }}</label>
                        <input type="text" name = "option_code_{!! $optionField !!}" id="option_code_{!! $optionField !!}" class="form-control">
                    </div>
                    @if(in_array('color', $fieldTypes))
                        <div class="form-group">
                            <label for="option_color_{!! $optionField !!}">{{ trans('privateCbsParameters.color') }}</label>
                            <input type="color" name="option_color_{!! $optionField !!}" id="option_color_{!! $optionField !!}" class="form-control">
                        </div>
                    @endif
                    @if(in_array('min_value', $fieldTypes))
                        <div class="form-group">
                            <label for="option_min_value_{!! $optionField !!}">{{ trans('privateCbsParameters.min_value') }}</label>
                            <input type="text" name = "option_min_value_{!! $optionField !!}" id="option_min_value_{!! $optionField !!}" class="form-control">
                        </div>
                    @endif
                    @if(in_array('max_value', $fieldTypes))
                        <div class="form-group">
                            <label for="option_max_value_{!! $optionField !!}">{{ trans('privateCbsParameters.max_value') }}</label>
                            <input type="text" name = "option_max_value_{!! $optionField !!}" id="option_max_value_{!! $optionField !!}" class="form-control">
                        </div>
                    @endif
                    @if(in_array('icon', $fieldTypes))
                        <div class="form-group">
                            {!! Form::oneFileUpload("option_icon_".$optionField, trans('privateCbsParameters.icon'), [], $uploadKey, array("readonly"=> false)) !!}
                        </div>
                    @endif
                    @if(in_array('pin', $fieldTypes))
                        <div class="form-group">
                            {!! Form::oneFileUpload("option_pin_".$optionField, trans('privateCbsParameters.pin'), [], $uploadKey, array("readonly"=> false)) !!}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>