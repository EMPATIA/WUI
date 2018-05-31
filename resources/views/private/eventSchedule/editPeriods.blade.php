@extends('private._private.index')

@section('content')

@php
$listType = array(1 => "Event with dates", 2 => "Question type");
@endphp

<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title">{!!trans('privateEventSchedules.details')!!}</h3>
        <div class="box-tools pull-right">
        </div>
    </div>
    <div class="box-body">
        <dl>
        <div class="row">
            <div class="col-md-12">
                <input type="hidden" value="1" name="entity_id">
                <dt>{!! trans('eventSchedule.title') !!}</dt><dd> {!!$eventSchedule->title!!} </dd><hr style="margin: 10px 0 10px 0">
                <dt>{!! trans('eventSchedule.description') !!}</dt><dd> {!!$eventSchedule->description!!}  </dd><hr style="margin: 10px 0 10px 0">            
                <dt>{!! trans('eventSchedule.local') !!}</dt><dd> {!!$eventSchedule->local!!} </dd><hr style="margin: 10px 0 10px 0">
                <dt>{!! trans('eventSchedule.type') !!}</dt><dd> {!! $listType[$eventSchedule->type_id] !!}  </dd>
                <hr style="margin: 10px 0 10px 0">                
                <dt>{!! trans('eventSchedule.url') !!}</dt><dd>  <a href="{{ action("EventSchedulesController@attendance",$eventSchedule->key) }}" target="_blank">{{ action("EventSchedulesController@attendance",$eventSchedule->key) }}</a> </dd>
                <hr style="margin: 10px 0 10px 0">
                <dt>{!! trans('eventSchedule.closed') !!}</dt><dd> {!! ($eventSchedule->closed)? trans('eventSchedule.yes') : trans('eventSchedule.no'); !!}  </dd>
                <hr style="margin: 10px 0 10px 0">
                <dt>{!! trans('eventSchedule.public') !!}</dt><dd> {!! ($eventSchedule->public)? trans('eventSchedule.yes') : trans('eventSchedule.no'); !!} </dd>                  
                <hr style="margin: 10px 0 10px 0">
            </div>
        </div>   
        </dl>
    </div>
    <div class="box-footer">
        <a class="btn btn-flat btn-primary" href=" {!!  action('EventSchedulesController@index') !!}"><i class="fa fa-arrow-left"></i> {!! trans('eventSchedule.back') !!}</a>
    </div>
</div>

@php
$form = ONE::form('eventSchedule', trans('privateEventSchedules.period_details'), 'q', 'poll')
    ->settings(["model" => isset($eventSchedule) ? $eventSchedule : null])
    ->show('EventSchedulesController@edit', 'EventSchedulesController@delete', ['id' => isset($eventSchedule) ? $eventSchedule->key : null], 'EventSchedulesController@index')
    ->create('EventSchedulesController@store', 'EventSchedulesController@show', ['id' => isset($eventSchedule) ? $eventSchedule->key : null])
    ->edit('EventSchedulesController@update', 'EventSchedulesController@show', ['id' => isset($eventSchedule) ? $eventSchedule->key : null])
    ->open()
@endphp
<div class="row">
    <div class="col-md-12">
        <div style="display:none;">
            {!! Form::oneText('title', trans('eventSchedule.title'), isset($eventSchedule) ? $eventSchedule->title : null, ['class' => 'form-control', 'id' => 'title']) !!}
            {!! Form::oneTextArea('description', trans('eventSchedule.description'), isset($eventSchedule) ? $eventSchedule->description : null, ['class' => 'form-control', 'id' => 'description', 'rows' =>3]) !!}            
            {!! Form::oneText('local', trans('eventSchedule.local'), isset($eventSchedule) ? $eventSchedule->local : null, ['class' => 'form-control', 'id' => 'local']) !!}        
            {!! Form::hidden('public',  $eventSchedule->public) !!}
            {!! Form::hidden('closed',  $eventSchedule->closed) !!}            
            {!! Form::hidden('entity_id', 1) !!}            
        </div>    
        <!-- Periods LIST -->
         @if($eventSchedule->type_id == 1)
            <div class="box-body no-padding">
                <div id='periods' class="user-panel">
                    <div class='row'>
                        <div class='col-md-5'><b>{!! trans('eventSchedule.date') !!}</b></div>
                        <div class='col-md-5'><b>{!! trans('eventSchedule.time') !!}</b></div>
                        @if($eventSchedule->closed == 1)
                            <div class='col-md-1'><b>{!! trans('eventSchedule.optionChoosed') !!}</b></div>    
                        @endif
                        
                        @if($eventSchedule->closed == 1)
                            <div class='col-md-1 text-right'>
                        @else    
                            <div class='col-md-2 text-right'>
                        @endif    
                            @if($eventSchedule->closed == 0 && ONE::actionType('eventSchedule') == 'edit' )
                                <a onclick="javascript:addPeriod();" title="{!! trans('eventSchedule.addPeriod') !!}" class="btn btn-flat btn-success btn-sm" href=#" data-original-title="{!! trans('formCreate') !!}">
                                    <i class="fa fa-plus"></i>
                                </a>                
                            @endif                                    
                        </div>
                    </div>          
                    @foreach((isset($eventSchedule->periods)?$eventSchedule->periods:[]) as $period)
                        <div id='eventSchedule{!! $period->id !!}' class='row'>
                            <div class='col-md-5'>
                                <i id='eventScheduleTrashedIcon{!! $period->id !!}' style="color:red;cursor:pointer;margin-top:10px;display:none;float:left;margin-right:5px;" class="fa fa-trash"></i> 
                                <div class="form-group ">
                                    <div>
                                        {!! Form::oneDate('start_date[]', null, isset($period) ? substr($period->start_date, 0, 10) : "",  ['id' => 'start_date']) !!}
                                    </div>
                                </div>                            
                            </div>
                            <div class='col-md-5'>
                                <div class="form-group ">
                                    {!! Form::oneTime('start_time[]', null, isset($period) ? $period->start_time : null, ['id' => 'start_time']) !!}
                                </div>                                
                            </div>
                            
                            @if($eventSchedule->closed == 1)
                            <div class='col-md-1 text-center'> 
                               <input type="radio" value="@php echo $period->id;@endphp" name="es_period_id" @php echo ($eventSchedule->es_period_id==$period->id) ? "checked='checked'" : ""; @endphp  />
                            </div>   
                            @endif

                            @if($eventSchedule->closed == 1)
                                <div class='col-md-1 text-right'>
                            @else    
                                <div class='col-md-2 text-right'>
                            @endif                             
                                @if($eventSchedule->closed == 0 && ONE::actionType('eventSchedule') == 'edit')
                                    <i id='eventScheduleTrashed{!! $period->id !!}' style="color:red;cursor:pointer;margin-top:10px;" class="fa fa-remove" onclick='removePeriod({!! $period->id !!})'></i> 
                                    <i id='eventScheduleRestore{!! $period->id !!}' style="color:green;cursor:pointer;margin-top:10px;display:none" class="fa fa-repeat" onclick='restorePeriod({!! $period->id !!})'></i> 
                                    <input name='period_id[]' value='{!! $period->id !!}' type='hidden' />  
                                    <input id='remove_id{!! $period->id !!}' name='remove[]' value='0' type='hidden'/>  
                                @endif
                            </div>
                        </div>    
                    @endforeach
                </div>
            </div>
        @elseif($eventSchedule->type_id == 2)
            <div class="box-body no-padding">
                <div id='questions' class="user-panel">
                    <div class='row'>
                        <div class='col-md-10'><b>{!! trans('eventSchedule.question') !!}</b></div>
                        @if($eventSchedule->closed == 1)
                            <div class='col-md-1'><b>{!! trans('eventSchedule.optionChoosed') !!}</b></div>    
                        @endif                        
                        
                        @if($eventSchedule->closed == 1)
                            <div class='col-md-1 text-right'>
                        @else    
                            <div class='col-md-2 text-right'>
                        @endif 
                            @if($eventSchedule->closed == 0 && ONE::actionType('eventSchedule') == 'edit' )
                                <a onclick="javascript:addQuestion();" title="{!! trans('eventSchedule.addQuestion') !!}" class="btn btn-flat btn-success btn-sm" href=#" data-original-title="form.create"><i class="fa fa-plus"></i></a>
                            @endif                            
                        </div>
                    </div>          
                    @foreach((isset($eventSchedule->questions)?$eventSchedule->questions:[]) as $question)
                        <div id='question{!! $question->id !!}' class='row'>
                            <div class='col-md-10'>
                                <div class="form-group ">
                                    <input type="text" value="@php echo isset($eventSchedule) ? $question->question : "" @endphp" name="question[]" class="form-control">
                                </div>                                
                            </div>
                            @if($eventSchedule->closed == 1)
                            <div class='col-md-1 text-center'> 
                               <input type="radio" value="@php echo $question->id;@endphp" name="es_question_id" @php echo ($eventSchedule->es_question_id==$question->id) ? "checked='checked'" : ""; @endphp />
                            </div>   
                            @endif  
                            
                            @if($eventSchedule->closed == 1)
                                <div class='col-md-1 text-right'>
                            @else    
                                <div class='col-md-2 text-right'>
                            @endif 
                                @if($eventSchedule->closed == 0 && ONE::actionType('eventSchedule') == 'edit')
                                    <i id='questionTrashed{!! $question->id !!}' style="color:red;cursor:pointer;margin-top:10px;" class="fa fa-remove" onclick='removeQuestion({!! $question->id !!})'></i> 
                                    <i id='questionRestore{!! $question->id !!}' style="color:green;cursor:pointer;margin-top:10px;display:none" class="fa fa-repeat" onclick='restoreQuestion({!! $question->id !!})'></i> 
                                    <input name='question_id[]' value='{!! $question->id !!}' type='hidden' />  
                                    <input id='remove_id{!! $question->id !!}' name='remove[]' value='0' type='hidden'/>  
                                @endif
                            </div>
                        </div>    
                    @endforeach                   
                </div>
            </div>
        @endif
    </div>    
</div>
{!! $form->make() !!}

@endsection

@section('scripts')
    <script>      
    function addPeriod(){
        var html = "<div class='row'>";
        html += '<div class="col-md-5">';
        html += '<div class="form-group ">';
        html += '<div>';
        html += '{!! Form::oneDate('start_date[]', null, null, ['id' => 'start_date'],array(),true) !!}';
        html += '</div>';
        html += '</div></div>';
        html += '<div class="col-md-5">';
        html += '<div>';
        html += '{!! Form::oneTime('start_time[]', null,null, ['id' => 'start_time'],array(),true) !!}';
        html += '</div>';  
        html += '</div>';
        html += '<div class="col-md-2 text-right"> <i style="color:red;cursor:pointer;margin-top:10px;" class="fa fa-remove" onclick="removeNewPeriod(this)"></i> <input  name="period_id[]" value=""  type="hidden"/>  <input name="remove[]" value="0" type="hidden"/>   </div>';
        html += '</div>';        
        $("#periods").append(html);
        // Render DatePicker and Time Pickers
        loadDatePickers();
        loadTimePickers();
    }
    function addQuestion(){
        var html = "<div class='row'>";
        html += '<div class="col-md-10">';
        html += '<div class="form-group">';
        html += '<input id="question" name="question[]" type="text" value="" class="form-control" />';
        html += '</div></div>';
        html += '<div class="col-md-2 text-right"> <input  name="question_id[]" value=""  type="hidden"/> <input name="remove[]" value="0" type="hidden"/>  <i style="color:red;cursor:pointer;margin-top:10px;" class="fa fa-remove" onclick="removeNewQuestion(this)"></i></div>';
        html += '</div>';        
        $("#questions").append(html);
    }    
    function removePeriod(id){
        $("#remove_id"+id).val(1);
        $("#eventScheduleTrashedIcon"+id).show();
        $("#eventScheduleTrashed"+id).hide();        
        $("#eventScheduleRestore"+id).show();
    }
    function restorePeriod(id){
        $("#remove_id"+id).val(0);
        $("#eventScheduleTrashedIcon"+id).hide();
        $("#eventScheduleTrashed"+id).show();        
        $("#eventScheduleRestore"+id).hide();
    }
    function removeQuestion(id){
        $("#remove_id"+id).val(1);
        $("#questionTrashedIcon"+id).show();
        $("#questionTrashed"+id).hide();        
        $("#questionRestore"+id).show();
    }
    function restoreQuestion(id){
        $("#remove_id"+id).val(0);
        $("#questionTrashedIcon"+id).hide();
        $("#questionTrashed"+id).show();        
        $("#questionRestore"+id).hide();
    }    
    function removeNewPeriod(object){
        $(object).parent().parent().remove();
    }    
    function removeNewQuestion(object){
        $(object).parent().parent().remove();
    }
    </script>
@endsection