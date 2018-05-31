<!-- CB Configurations -->
@foreach($configuration->configurations as $option)
  <div style="margin-bottom:15px;">
  {!! Form::oneSwitch("configuration_".$option->id,
                      array("name" => $option->title, "description" => $option->description ),
                      in_array($option->id, (isset($cbConfigurations) ? $cbConfigurations : []) ),
                      array("groupClass"=>"row", 
                            "labelClass" => "col-sm-12 col-md-6", 
                            "switchClass" => "col-sm-12 col-md-3") ) !!}
  </div>
@endforeach    
