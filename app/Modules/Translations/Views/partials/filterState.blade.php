<div class="col-12">
    <div class="form-group">
        <label class="filterBy-title">{!! trans('Translations::translation.filter_state') !!}</label>
    </div>
    <div class="col-xs-6 filter-margins">
        {!! Form::oneSwitch("states[]",trans("Translations::translation.empty"), true, ['class' => 'form-control','id'=> "statusEmpty",'value'=>"1", "groupClass"=>"row",
                                  "labelClass" => "col-sm-12 col-md-3 custom-line-height no-padding-left",
                                  "switchClass" => "col-sm-12 col-md-3"]) !!}
        {!! Form::oneSwitch("states[]",trans("Translations::translation.saved"), true,['class' => 'form-control','id'=> "statusSaved",'value'=>"0" ,"groupClass"=>"row",
                                  "labelClass" => "col-sm-12 col-md-3 custom-line-height no-padding-left",
                                  "switchClass" => "col-sm-12 col-md-3"]) !!}
    </div>
</div>