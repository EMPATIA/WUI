@extends('private._private.index')
    
@if($eventSchedule->type_id == 1)

    @section('content')
    {{ Html::style('/css/scheduleAttendance.css') }}
    @php
    /* reorder periods */
    $periods = [];

    if(!empty($eventSchedule->periods)){
        foreach($eventSchedule->periods as $period){
            $periods[$period->start_date]["month"] = date("F", strtotime($period->start_date));
            $periods[$period->start_date]["year"] = date("Y", strtotime($period->start_date));
            $periods[$period->start_date]["day"] = date("j", strtotime($period->start_date));
            $periods[$period->start_date]["dayweek"] =  strftime('%A', strtotime($period->start_date)); 
            // Periods
            $periods[$period->start_date]["periods"][] = array("id" => $period->id ,"period" => $period->start_time);    
        }
    }

    $alreadyVoted = false;
    $method = "post";
    foreach($eventSchedule->participants as $participant) {               
        if( $user->user_key == $participant->user_key ) {
            $alreadyVoted = true;
            $method = "put";
        } 
    }

    $participantKey = "";
    @endphp
    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title"><i class="fa"></i> {{ trans('eventSchedule.schedule') }} - {!! $eventSchedule->title !!}</h3>
        </div>
        <div class="box-body">  

       @if($eventSchedule->closed == 0)
        {{ Form::open(array('url' => action("EventSchedulesController@storeAttendance",$eventSchedule->key), 'method' => $method )) }}         
       @endif
       
            <table cellpadding="0" cellspacing="0" class="tableEventSchedule poll withFooter" style="border-collapse:collapse;">
               <tbody>
                <tr class="header date month">
                   <th class="nonHeader headerSchedule " >
                   </th> 
                   @foreach($periods as $key => $period)
                   <th class="rsep text-center borderSchedule" colspan="{{ count($period["periods"]) }}">
                         {!! $period["month"] !!} {!! $period["year"] !!}
                   </th>  
                   @endforeach
                </tr>

                <tr class="header date day">
                   <th class="nonHeader">
                      <div></div>
                   </th>              
                   @foreach($periods as $key => $period)
                   <th class="rsep text-center borderSchedule" colspan="{{ count($period["periods"]) }}" >
                         {!! $period["dayweek"] !!} {!! $period["day"] !!}
                   </th>  
                   @endforeach            
                </tr>
                <tr class="time">                 
                   <th class="nonHeader partCount boldText borderSchedule">
                      <div class='counterDiv'>{!! count($eventSchedule->participants) !!} {{ trans('eventSchedule.participants') }}</div>
                   </th>            
                   @foreach($periods as $key => $period)
                        @foreach($period["periods"] as $time)
                        <th class="text-center borderSchedule" colspan="1">
                             {!! $time["period"] !!}
                        </th>
                        @endforeach 
                   @endforeach                     
                </tr>

                @foreach($eventSchedule->participants as $participant)                  
                  <tr class="participant">  
                      <td class="sUserName borderSchedule">
                        <div class="row"> 
                            <div class="col-md-9 text-left">                           
                                <div class="avatarSmall">
                                   <i class="glyphicon glyphicon-user participantIcon"></i>
                                </div>
                                <div class='participantName'  title="{!! $participant->name !!}" style='width:185px;'>
                                    {!! $participant->name !!}
                                </div>
                            </div>        
                            <div class="col-md-3 text-right">
                                @if( $user->user_key == $participant->user_key && $eventSchedule->closed == 0)
                                    <div id="editControls" style="position:relative;top:6px;right:5px;">
                                        <a onclick="javascript:showRestoreControls()" title="{{ trans('eventSchedule.edit') }}" class="btn btn-flat btn-info btn-sm"><i class="fa fa-pencil"></i></a>&nbsp;
                                        <a onclick="javascript:deleteAttendance()" title="{{ trans('eventSchedule.delete') }}" class="btn btn-flat btn-danger btn-sm"><i class="fa fa-remove"></i></a>
                                    </div>
                                    <div id="restoreControls" style="display:none;position:relative;top:6px;right:5px;">
                                        <a onclick="javascript:showEditControls()" title="{{ trans('eventSchedule.edit') }}" class="btn btn-flat btn-info btn-sm" style="display: inline;" ><i class="fa fa-undo"></i></a>&nbsp;
                                    </div>                                   
                                    <input id="pname" name="name" type="hidden" value="{!! $user->name !!}" />        
                                    <input id="participant_id" name="participant_id" type="hidden" value="{!! $participant->id !!}" />     
                                    <input id="participant_key" name="participant_key" type="hidden" value="{!! $participant->key !!}" />     
                                    @php
                                    $participantKey = $participant->key;
                                    @endphp
                                @endif 
                            </div>
                         </div>       
                    </td>
                    @foreach($periods as $key => $period)
                         @foreach($period["periods"] as $time)
                            @php
                            $bvar = false;
                            foreach( $participant->periods as $period2 ){
                                if($time["id"] == $period2->pivot->es_period_id && $period2->pivot->es_participant_id == $participant->id ){
                                    $bvar = true;
                                }
                            }
                            @endphp
                            @if($bvar)
                            <td class="partTableCell y sep pok borderSchedule" title="{!! $participant->name !!},{!! $period['day'].' '.$period['month'].' '.$period['year'].' @ '.$time['period'] !!}: {{ trans('eventSchedule.yes') }}">                
                                @if( $user->user_key == $participant->user_key )
                                <div class='viewPeriod'>
                                    <i class="glyphicon glyphicon-ok" alt="{!! $participant->name !!},{!! $period['day'].' '.$period['month'].' '.$period['year'].' @ '.$time['period'] !!}: {{ trans('eventSchedule.yes') }}"></i>
                                </div>                            
                                <div class='editPeriod' style='display:none;' >
                                    <input id="period{!! $time['id'] !!}" name="periods[]" type="checkbox" value="{!! $time['id'] !!}" class="periods" checked="yes" />       
                                </div>
                                @else
                                <div>
                                    <i class="glyphicon glyphicon-ok" alt="{!! $participant->name !!},{!! $period['day'].' '.$period['month'].' '.$period['year'].' @ '.$time['period'] !!}: {{ trans('eventSchedule.yes') }}"></i>
                                </div>                            
                                @endif
                            </td>
                            @else 
                            <td title="{!! $participant->name !!}, period['month'].' '.$period['day'] : {{ trans('eventSchedule.no') }}" class="borderSchedule partTableCell n sep pn">
                                @if( $user->user_key == $participant->user_key )
                                <div class='editPeriod' style='display:none;'>
                                    <input id="period{!! $time['id'] !!}" name="periods[]" type="checkbox" value="{!! $time['id'] !!}" class="periods" />       
                                </div>                                
                                @endif
                            </td>                    
                            @endif  
                         @endforeach 
                    @endforeach                  
                  </tr>
                @endforeach

                @if( $alreadyVoted==false && $eventSchedule->closed == 0)
                    <tr class="participation yesNo partMyself">  
                       <td class='borderSchedule' style="width:251px;">
                          <div style="width:100%">
                              <div class="avatarSmall">
                                 <div class="gravatar">
                                      <i class="glyphicon glyphicon-user participantIcon"></i>
                                 </div>
                             </div>
                              <input id="pname" name="name" placeholder="{{ trans('eventSchedule.yourName') }}" class="form-control" type="text" value="{!! $user->name !!}" readonly="read" style="width:219px;position:relative;float:left;">                           
                          </div>
                       </td>
                        @foreach($periods as $key => $period)
                             @foreach($period["periods"] as $time)
                                <td data="0" id="box0" title="{!!  $period['month'].' '.$period['day'] !!}" class='borderSchedule'>                                
                                    <input id="period{!! $time['id'] !!}" name="periods[]" type="checkbox" value="{!! $time['id'] !!}" class="periods" />                            
                                </td>
                             @endforeach 
                        @endforeach             
                    </tr>
                @endif
                </tbody>
            </table>   

            @if($eventSchedule->closed == 0)
                <!-- Submit -->
                <div id="storeButton" class="box-footer" style="{{ ($alreadyVoted == true) ? "display:none;": "" }}">
                    <button type="submit" value="form.save" class="btn btn-flat btn-primary btnSchedule"> {{ trans('attendance.save') }}</button> 
                </div>   
                <input name="eventName" value="{!! $eventSchedule->title !!}" type="hidden" />
                {{ Form::close() }}        
            @else
                <div class="row">
                    <div class="col-md-12">
                        <i>{{ trans('eventSchedule.thisIsClosedMessage') }}</i>
                    </div>
                </div>    
            @endif
        </div>
    </div>
    @endsection

    @section('scripts')
        <script>
            function deleteAttendance(){
                oneDelete('{{action("EventSchedulesController@deleteAttendance",[$eventSchedule->key,$participantKey])}}');            
            }
            function showEditControls(){
                $("#editControls").show();
                $("#restoreControls").hide();
                $(".editPeriod").hide();
                $(".viewPeriod").show();  
                $("#storeButton").hide();
            }
            function showRestoreControls(){
                $("#editControls").hide();
                $("#restoreControls").show();
                $(".editPeriod").show();
                $(".viewPeriod").hide();
                $("#storeButton").show();
            }     
        </script>
    @endsection

@elseif($eventSchedule->type_id == 2)

    @section('content')
    {{ Html::style('/css/scheduleAttendance.css') }}    
    @php
    $alreadyVoted = false;
    $method = "post";
    foreach($eventSchedule->participants as $participant) {               
        if( $user->user_key == $participant->user_key ) {
            $alreadyVoted = true;
            $method = "put";
        } 
    }
    $participantKey = "";
    @endphp
    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title"><i class="fa"></i> {{ trans('eventSchedule.schedule') }} - {!! $eventSchedule->title !!}</h3>
        </div>
        <div class="box-body">  
            
       @if($eventSchedule->closed == 0)
        {{ Form::open(array('url' => action("EventSchedulesController@storeAttendance",$eventSchedule->key), 'method' => $method )) }}         
       @endif
       
            <table cellpadding="0" cellspacing="0" class="tableEventSchedule poll withFooter" style="border-collapse:collapse;">
               <tbody>
                <tr class="header date month">
                   <th class="nonHeader headerSchedule " >
                   </th> 
                   @foreach($eventSchedule->questions as $question)
                   <th class="rsep text-center borderSchedule" colspan="1">
                        {!! $question->question !!}
                   </th>  
                   @endforeach
                </tr>
                @foreach($eventSchedule->participants as $participant)   
                  <tr class="participant">  
                      <td class="sUserName borderSchedule">   
                        <div class="row"> 
                            <div class="col-md-9 text-left">                           
                                <div class="avatarSmall">
                                     <i class="glyphicon glyphicon-user participantIcon"></i>
                                </div>
                                <div class='participantName' title="{!! $participant->name !!}" style='width:158px;'>
                                 {!! $participant->name !!}
                                </div> 
                            </div>
                            <div class="col-md-3 text-right">                                 
                                @if( $user->user_key == $participant->user_key && $eventSchedule->closed == 0)
                                    <span>
                                        <div id="editControls" style="position:relative;top:6px;right:5px;">
                                            <a onclick="javascript:showRestoreControls()" title="{{ trans('eventSchedule.edit') }}" class="btn btn-flat btn-info btn-sm" style="display: inline;" ><i class="fa fa-pencil"></i></a>&nbsp;
                                            <a onclick="javascript:deleteAttendance()" title="{{ trans('eventSchedule.delete') }}" class="btn btn-flat btn-danger btn-sm" style="display: inline;"><i class="fa fa-remove"></i></a>
                                        </div>
                                        <div id="restoreControls" style="display:none;position:relative;top:6px;right:5px;">
                                            <a onclick="javascript:showEditControls()" title="{{ trans('eventSchedule.edit') }}" class="btn btn-flat btn-info btn-sm" style="display: inline;" ><i class="fa fa-undo"></i></a>&nbsp;
                                        </div>                                
                                        <input id="pname" name="name" type="hidden" value="{!! $user->name !!}" />        
                                        <input id="participant_id" name="participant_id" type="hidden" value="{!! $participant->id !!}" />     
                                        <input id="participant_key" name="participant_key" type="hidden" value="{!! $participant->key !!}" />     
                                        @php
                                        $participantKey = $participant->key;
                                        @endphp
                                    </span>
                                @endif 
                            </div>        
                        </div>    
                    </td>
                    
                    @foreach($eventSchedule->questions as $question)
                        @php
                        $bvar = false;
                        foreach( $participant->questions as $question2 ){
                            if($question->id == $question2->pivot->es_question_id && $question2->pivot->es_participant_id == $participant->id ){
                                $bvar = true;
                            }
                        }
                        @endphp
                        @if($bvar)
                        <td class="partTableCell y sep pok borderSchedule" title="{{ trans('eventSchedule.yes') }}">                
                            @if( $user->user_key == $participant->user_key )
                            <div class='viewPeriod'>
                                <i class="glyphicon glyphicon-ok" alt="{{ trans('eventSchedule.yes') }}"></i>
                            </div>                            
                            <div class='editPeriod' style='display:none;' >
                                <input id="questions" name="questions[]" type="checkbox" value="{!! $question->id !!}" checked="yes" />       
                            </div>
                            @else
                            <div>
                                <i class="glyphicon glyphicon-ok" alt="{{ trans('eventSchedule.yes') }}"></i>
                            </div>                            
                            @endif
                        </td>
                        @else 
                        <td title="{{ trans('eventSchedule.no') }}" class="borderSchedule partTableCell n sep pn">
                            @if( $user->user_key == $participant->user_key )
                            <div class='editPeriod' style='display:none;'>
                                <input id="question{!! $question->id !!}" name="questions[]" type="checkbox" value="{!! $question->id !!}" />       
                            </div>                                
                            @endif
                        </td>                    
                        @endif  
                    @endforeach                  
                  </tr>
                @endforeach
                
               @if(count($eventSchedule->participants) == 0 && $eventSchedule->closed == 1)
                <tr><td colspan="{{ count($eventSchedule->questions)+1 }}" class="borderSchedule text-center"><i>{{ trans('eventSchedule.empty') }}</i> </td></tr>
               @endif

               @if( $alreadyVoted==false && $eventSchedule->closed == 0 )
                    <tr class="participation yesNo partMyself">  
                       <td class='borderSchedule' style="width:251px;">
                          <div style="width:100%">
                              <div class="avatarSmall">
                                 <div class="gravatar">
                                      <i class="glyphicon glyphicon-user participantIcon"></i>
                                 </div>
                             </div>
                              <input id="pname" name="name" placeholder="{{ trans('eventSchedule.yourName') }}" class="form-control" type="text" value="{!! $user->name !!}" readonly="read" style="width:220px;position:relative;float:left;">                           
                          </div>
                       </td>
                       @foreach($eventSchedule->questions as $question)
                            <td data="0" id="box0" title="" class='borderSchedule'>                                
                                <input id="question{!! $question->id !!}" name="questions[]" type="checkbox" value="{!! $question->id !!}" class="periods" />                            
                            </td>
                        @endforeach             
                    </tr>
                @endif
                </tbody>
            </table>   

            @if($eventSchedule->closed == 0)    
                <!-- Submit -->
                <div id="storeButton" class="box-footer" style="{{ ($alreadyVoted == true) ? "display:none;": "" }}">
                    <button type="submit" value="form.save" class="btn btn-flat btn-primary btnSchedule"> {{ trans('attendance.save') }}</button> 
                </div>   

                <input name="eventName" value="{!! $eventSchedule->title !!}" type="hidden" />
                {{ Form::close() }}        
            @else
                <div class="row">
                    <div class="col-md-12">
                        <i>{{ trans('eventSchedule.thisIsClosedMessage') }}</i>
                    </div>
                </div>    
            @endif
            
        </div>
    </div>
    @endsection

    @section('scripts')
        <script>
            function deleteAttendance(){
                oneDelete('{{action("EventSchedulesController@deleteAttendance",[$eventSchedule->key,$participantKey])}}');            
            }
            function showEditControls(){
                $("#editControls").show();
                $("#restoreControls").hide();
                $(".editPeriod").hide();
                $(".viewPeriod").show();    
                $("#storeButton").hide();
            }
            function showRestoreControls(){
                $("#editControls").hide();
                $("#restoreControls").show();
                $(".editPeriod").show();
                $(".viewPeriod").hide();
                $("#storeButton").show();
            }            
        </script>
    @endsection

@endif