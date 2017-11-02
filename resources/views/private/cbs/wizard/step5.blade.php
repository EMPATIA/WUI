@if(!empty($genericConfigs))
    <div class="row">
        @foreach($genericConfigs as $config)
        <div class="col-md-4">
        {!! Form::oneSwitch("genericConfig_".$config->vote_configuration_key,
                            array("name" => $config->name, "description" => $config->description ),
                            isset($voteConfigs[$config->vote_configuration_key])) !!}                
        </div>
        @endforeach
    </div>        
@else
    <br/><br/><br/>
    <center><i><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> {{ trans("privateCbs.genericConfigurationsEmpty") }}</i><center>
    <br/><br/><br/>
@endif