@extends('public.empaville_new._layouts.index')

@if($eventSchedule->type_id == 1)
@section('content')
    <?php
    /* reorder periods */
    $periods = [];

    if(!empty($eventSchedule->periods)){
        foreach($eventSchedule->periods as $period){
            $periods[$period->start_date]["month"] = date("F", strtotime($period->start_date));
            $periods[$period->start_date]["year"] = date("Y", strtotime($period->start_date));
            $periods[$period->start_date]["day"] = date("j", strtotime($period->start_date));
            $periods[$period->start_date]["dayweek"] = $days[] = strftime('%A', strtotime($period->start_date));
            // Periods
            $periods[$period->start_date]["periods"][] = array("id" => $period->id ,"period" => $period->start_time);
        }
    }

    $alreadyVoted = false;
    if(!empty($user)){
        foreach($eventSchedule->participants as $participant) {
            if($user->user_key == $participant->user_key ) {
                $alreadyVoted = true;
            }
        }
    }

    ?>
    <div class="container">
        <div class="row menus-row">
            <div class="menus-line col-sm-6 col-sm-offset-3">{{ trans('PrivateEventSchedule.schedule') }} - {!! $eventSchedule->title !!}</div>
        </div>

        <div>

            @if(!isset($_REQUEST["action"]))

                @if($eventSchedule->closed == 0)
                    {{ Form::open(array('url' => action("EventSchedulesController@publicStoreAttendance",$eventSchedule->key), 'method' => "post" )) }}
                @endif

                <table class="tableEventSchedule poll withFooter">
                    <tbody>
                    <tr class="header date month">
                        <th class="nonHeader headerSchedule " >
                        </th>
                        @foreach($periods as $key => $period)
                            <th class="rsep text-center " colspan="{{ count($period["periods"]) }}">
                                {!! $period["month"] !!} {!! $period["year"] !!}
                            </th>
                        @endforeach
                    </tr>

                    <tr class="header date day">
                        <th class="nonHeader">
                            <div></div>
                        </th>
                        @foreach($periods as $key => $period)
                            <th class="rsep text-center " colspan="{{ count($period["periods"]) }}">
                                {!! $period["dayweek"] !!} {!! $period["day"] !!}
                            </th>
                        @endforeach
                    </tr>
                    <tr class="time">
                        <th class="nonHeader partCount boldText ">
                            {{--
                              <div class='counterDiv'>{!! count($eventSchedule->participants) !!} {{ trans('PrivateEventSchedule.participants') }}</div>
                              --}}
                        </th>
                        @foreach($periods as $key => $period)
                            @foreach($period["periods"] as $time)
                                <th class="hours text-center " colspan="1">
                                    {!! $time["period"] !!}
                                </th>
                            @endforeach
                        @endforeach
                    </tr>
                    @foreach($eventSchedule->participants as $participant)
                        <tr class="participant">
                            <td class="sUserName ">

                                <div class="row">
                                    <div class="col-md-8 text-left">
                                        <div class="avatarSmall">
                                            <i class="glyphicon glyphicon-user participantIcon"></i>
                                        </div>
                                        <div class='participantName'  title="{!! $participant->name !!}" style="width:155px;">
                                            {!! $participant->name !!}
                                        </div>
                                    </div>
                                    <div class="col-md-4 text-right">
                                        <div>
                                            @if(  ( !empty($user) && $user->user_key == $participant->user_key && $eventSchedule->closed == 0) )
                                                <a href="?action=edit&participant_id={{$participant->id}}" title="{{ trans('PrivateEventSchedule.edit') }}" class="btn btn-flat btn-info btn-xs" style="margin-top:6px;font-size: 2rem;" ><i class="fa fa-pencil"></i></a>&nbsp;
                                                <a onclick="javascript:deleteAttendance('{{$participant->key}}')" title="{{ trans('PrivateEventSchedule.delete') }}" class="btn btn-flat btn-danger btn-xs" style="margin-top:6px;"><i class="fa fa-remove"></i></a>
                                            @elseif(empty($participant->user_key))
                                                <a href="?action=edit&participant_id={{$participant->id}}" title="{{ trans('PrivateEventSchedule.edit') }}" class="btn btn-flat btn-info btn-xs" style="margin-top:6px;font-size: 2rem;" ><i class="fa fa-pencil"></i></a>&nbsp;
                                                <a onclick="javascript:deleteAttendance('{{$participant->key}}')" title="{{ trans('PrivateEventSchedule.delete') }}" class="btn btn-flat btn-danger btn-xs" style="margin-top:6px;font-size: 2rem;"><i class="fa fa-remove"></i></a>
                                            @endif
                                        </div>
                                    </div>
                                </div>



                            </td>
                            @foreach($periods as $key => $period)
                                @foreach($period["periods"] as $time)
                                    <?php
                                    $bvar = false;
                                    foreach( $participant->periods as $period2 ){
                                        if($time["id"] == $period2->pivot->es_period_id && $period2->pivot->es_participant_id == $participant->id ){
                                            $bvar = true;
                                        }
                                    }
                                    ?>
                                    @if($bvar)
                                        <td class="partTableCell y sep pok ">
                                            @if(!empty($user) && $user->user_key == $participant->user_key )
                                                <div class='viewPeriod'>
                                                    <i class="glyphicon glyphicon-ok  btn-lg selectedOk"></i>
                                                </div>
                                                <div class='editPeriod' style='display:none;' >
                                                    <input id="period{!! $time['id'] !!}" name="periods[]" type="checkbox" value="{!! $time['id'] !!}" class="periods" checked="yes" />
                                                </div>
                                            @else
                                                <div>
                                                    <i class="glyphicon glyphicon-ok  btn-lg selectedOk"></i>
                                                </div>
                                            @endif
                                        </td>
                                    @else
                                        <td title="{!! $participant->name !!}, period['month'].' '.$period['day'] : {{ trans('PrivateEventSchedule.no') }}" class=" partTableCell n sep pn">
                                            @if(!empty($user) && $user->user_key == $participant->user_key )
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
                    @if( ($alreadyVoted==false || empty($user)) && $eventSchedule->closed == 0)
                        <?php
                        if( !empty($user) ){
                            $userName = $user->name;
                        }else{
                            $userName = "";
                        }
                        ?>
                        <tr class="participation yesNo partMyself">
                            <td class='' style="width:249px;">
                                <div style="width:100%">
                                    <div class="avatarSmall">
                                        <div class="gravatar">
                                            <i class="glyphicon glyphicon-user participantIcon" style='margin-left:0px'></i>
                                        </div>
                                    </div>
                                    <input id="pname" name="name" placeholder="{{ trans('PrivateEventSchedule.yourName') }}" class="form-schedule name-input" type="text" value="{!! $userName !!}" {{ !empty($user) ? 'readonly="read"' : '' }} style="width:218px;position:relative;float:left; background-color: white; border: none"  />
                                </div>
                            </td>
                            @foreach($periods as $key => $period)
                                @foreach($period["periods"] as $time)
                                    <td data="0" id="box0" title="{!!  $period['month'].' '.$period['day'] !!}" class='background'>
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
                <div class="box-footer" style="{{ ($alreadyVoted == true) ? "display:none;": "" }}">
                    <button type="submit" value="form.save" class="btnSchedule"> {{ trans('PrivateEventSchedule.save') }}</button>
                </div>
                <input name="eventName" value="{!! $eventSchedule->title !!}" type="hidden" />
                {{ Form::close() }}
                @endif
            @endif

            @if(isset($_REQUEST["action"]) && $_REQUEST["action"] == "edit")

                @if($eventSchedule->closed == 0)
                    {{ Form::open(array('url' => action("EventSchedulesController@publicUpdateAttendance",$eventSchedule->key), 'method' => "put" )) }}
                @endif
                <table cellpadding="10" cellspacing="10" class="tableEventSchedule poll withFooter" style="border-collapse:collapse;">
                    <tbody>
                    <tr class="header date month">
                        <th class="nonHeader headerSchedule " >
                        </th>
                        @foreach($periods as $key => $period)
                            <th class="rsep text-center" colspan="{{ count($period["periods"]) }}">
                                {!! $period["month"] !!} {!! $period["year"] !!}
                            </th>
                        @endforeach
                    </tr>

                    <tr class="header date day">
                        <th class="nonHeader">
                            <div></div>
                        </th>
                        @foreach($periods as $key => $period)
                            <th class="rsep text-center" colspan="{{ count($period["periods"]) }}" >
                                {!! $period["dayweek"] !!} {!! $period["day"] !!}
                            </th>
                        @endforeach
                    </tr>
                    <tr class="time">
                        <th class="nonHeader partCount boldText">
                            {{--
                           <div class='counterDiv'>{!! count($eventSchedule->participants) !!} {{ trans('PrivateEventSchedule.participants') }}</div>
                           --}}
                        </th>
                        @foreach($periods as $key => $period)
                            @foreach($period["periods"] as $time)
                                <th class="hours text-center" colspan="1">
                                    {!! $time["period"] !!}
                                </th>
                            @endforeach
                        @endforeach
                    </tr>
                    @foreach($eventSchedule->participants as $participant)
                        <tr class="participant">
                            <td class=" sUserName">
                                <div class="row">
                                    @if($participant->id == $_REQUEST["participant_id"]  && $eventSchedule->closed == 0 )
                                        <div class="col-md-10 text-left">
                                            <div class="avatarSmall">
                                                <i class="glyphicon glyphicon-user participantIcon"></i>
                                            </div>
                                            <div class='participantName'  title="{!! $participant->name !!}">
                                                @if(!empty($participant->user_key))
                                                    {!! $participant->name !!}
                                                    <input id="pname" name="name" placeholder="{{ trans('PrivateEventSchedule.yourName') }}" class="name-input" type="hidden" value="{!! $participant->name !!}"/>
                                                @else
                                                    <input id="pname" name="name" placeholder="{{ trans('PrivateEventSchedule.yourName') }}" class="name-input" type="text" value="{!! $participant->name !!}" style='width:100%;'/>
                                                @endif
                                            </div>
                                        </div>

                                        @if ($eventSchedule->closed == 0)
                                            <div clas="col-md-2">
                                                <a href="./{{ $eventSchedule->key }}" title="{{ trans('PrivateEventSchedule.edit') }}" class="btn btn-flat btn-info btn-xs" style="margin-top:6px;margin-right:2px;font-size: 2rem;" ><i class="fa fa-undo"></i></a>&nbsp;
                                                <a onclick="javascript:deleteAttendance('{{$participant->key}}')" title="{{ trans('PrivateEventSchedule.delete') }}" class="btn btn-flat btn-danger btn-xs" style="display: none;font-size: 2rem;"><i class="fa fa-remove"></i></a>
                                                <input id="participant_id" name="participant_id" type="hidden" value="{!! $participant->id !!}" />
                                                <input id="participant_key" name="participant_key" type="hidden" value="{!! $participant->key !!}" />
                                            </div>
                                        @endif
                                    @else
                                        <div class="col-md-12 text-left">
                                            <div class="avatarSmall">
                                                <i class="glyphicon glyphicon-user participantIcon"></i>
                                            </div>
                                            <div class='participantName'  title="{!! $participant->name !!}">
                                                {!! $participant->name !!}
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </td>
                            @foreach($periods as $key => $period)
                                @foreach($period["periods"] as $time)
                                    <?php
                                    $bvar = false;
                                    foreach( $participant->periods as $period2 ){
                                        if($time["id"] == $period2->pivot->es_period_id && $period2->pivot->es_participant_id == $participant->id ){
                                            $bvar = true;
                                        }
                                    }
                                    ?>
                                    @if($bvar)
                                        <td class="partTableCell y sep pok ">
                                            @if($participant->id  == $_REQUEST["participant_id"]  )
                                                <input id="period{!! $time['id'] !!}" name="periods[]" type="checkbox" value="{!! $time['id'] !!}" class="periods" checked="yes" />
                                            @else
                                                <div>
                                                    <i class="glyphicon glyphicon-ok btn-lg selectedOk"></i>
                                                </div>
                                            @endif
                                        </td>
                                    @else
                                        <td class=" partTableCell n sep pn">
                                            @if($participant->id  == $_REQUEST["participant_id"]  )
                                                <input id="period{!! $time['id'] !!}" name="periods[]" type="checkbox" value="{!! $time['id'] !!}" class="periods" />
                                            @endif
                                        </td>
                                    @endif
                                @endforeach
                            @endforeach
                        </tr>
                    @endforeach
                    </tbody>
                </table>

                <input name="eventName" value="{!! $eventSchedule->title !!}" type="hidden" />
                @if($eventSchedule->closed == 0)
                        <!-- Submit -->
                <div class="box-footer">
                    <button type="submit" value="form.save" class="btnSchedule"> {{ trans('PrivateEventSchedule.save') }}</button>
                </div>
                {{ Form::close() }}
            @endif
            @endif

            @if($eventSchedule->closed == 1)
                <div class="row">
                    <div class="col-md-12">
                        <i>{{ trans('PrivateEventSchedule.thisIsClosedMessage') }}</i>
                    </div>
                </div>
            @endif

        </div>
    </div>
@endsection

@section('scripts')
    <script>
        window.editSchedule = false;
        function deleteAttendance(participantKey){
            oneDelete('{{action("EventSchedulesController@publicDeleteAttendance",[$eventSchedule->key])}}'+'/'+participantKey);
        }
        function showEdit(){
            if( window.editSchedule == false ){
                $(".editPeriod").show();
                $(".viewPeriod").hide();
            }else{
                $(".editPeriod").hide();
                $(".viewPeriod").show();
            }
            window.editSchedule = !window.editSchedule;
        }
    </script>
@endsection

@elseif($eventSchedule->type_id == 2)

@section('content')
    <?php

    $alreadyVoted = false;
    if(!empty($user)){
        foreach($eventSchedule->participants as $participant) {
            if($user->user_key == $participant->user_key ) {
                $alreadyVoted = true;
            }
        }
    }
    ?>
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title"><i class="fa"></i> {{ trans('PrivateEventSchedule.schedule') }} - {!! $eventSchedule->title !!}</h3>
        </div>
        <div class="box-body">

            @if(!isset($_REQUEST["action"]))

                @if($eventSchedule->closed == 0)
                    {{ Form::open(array('url' => action("EventSchedulesController@publicStoreAttendance",$eventSchedule->key), 'method' => "post" )) }}
                @endif
                <table cellpadding="10" cellspacing="10" class="tableEventSchedule poll withFooter" style="border-collapse:collapse;">
                    <tbody>
                    <tr class="header date month">
                        <th class="nonHeader headerSchedule " >
                        </th>
                        @foreach($eventSchedule->questions as $question)
                            <th class="rsep text-center " colspan="1">
                                {!! $question->question !!}
                            </th>
                        @endforeach
                    </tr>

                    @foreach($eventSchedule->participants as $participant)
                        <tr class="participant">
                            <td class="sUserName">
                                <div class="row">
                                    <div class="col-md-8 text-left">
                                        <div class="avatarSmall">
                                            <i class="glyphicon glyphicon-user participantIcon"></i>
                                        </div>
                                        <div class='participantName'  title="{!! $participant->name !!}"  style='width: 155px;'>
                                            {!! $participant->name !!}
                                        </div>
                                    </div>
                                    <div class="col-md-4 text-right">
                                        @if( (!empty($user) && $user->user_key == $participant->user_key && $eventSchedule->closed == 0) )
                                            <span style="position:relative;top:6px;right:5px;">
                                    <a href="?action=edit&participant_id={{$participant->id}}" title="{{ trans('eventSchedule.edit') }}" class="btn btn-flat btn-info btn-xs"><i class="fa fa-pencil"></i></a>&nbsp;
                                    <a onclick="javascript:deleteAttendance('{{$participant->key}}')" title="{{ trans('eventSchedule.delete') }}" class="btn btn-flat btn-danger btn-xs"><i class="fa fa-remove"></i></a>
                                </span>
                                        @elseif(empty($participant->user_key))
                                            <span style="position:relative;top:6px;right:5px;">
                                    <a href="?action=edit&participant_id={{$participant->id}}" title="{{ trans('eventSchedule.edit') }}" class="btn btn-flat btn-info btn-xs"><i class="fa fa-pencil"></i></a>&nbsp;
                                    <a onclick="javascript:deleteAttendance('{{$participant->key}}')" title="{{ trans('eventSchedule.delete') }}" class="btn btn-flat btn-danger btn-xs"><i class="fa fa-remove"></i></a>
                                </span>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            @foreach($eventSchedule->questions as $question)
                                <?php
                                $bvar = false;
                                foreach( $participant->questions as $question2 ){
                                    if($question->id == $question2->pivot->es_question_id && $question2->pivot->es_participant_id == $participant->id ){
                                        $bvar = true;
                                    }
                                }
                                ?>
                                @if($bvar)
                                    <td class="partTableCell y sep pok " title="{{ trans('PrivateEventSchedule.yes') }}">
                                        @if(!empty($user) && $user->user_key == $participant->user_key )
                                            <div class='viewPeriod'>
                                                <i class="glyphicon glyphicon-ok  btn-lg selectedOk" alt="{{ trans('PrivateEventSchedule.yes') }}"></i>
                                            </div>
                                            <div class='editPeriod' style='display:none;' >
                                                <input id="questions" name="questions[]" type="checkbox" value="{!! $question->id !!}" checked="yes" />
                                            </div>
                                        @else
                                            <div>
                                                <i class="glyphicon glyphicon-ok  btn-lg selectedOk" alt="{{ trans('PrivateEventSchedule.yes') }}"></i>
                                            </div>
                                        @endif
                                    </td>
                                @else
                                    <td title="{{ trans('PrivateEventSchedule.no') }}" class=" partTableCell n sep pn">
                                        @if(!empty($user) && $user->user_key == $participant->user_key )
                                            <div class='editPeriod' style='display:none;'>
                                                <input id="question{!! $question->id !!}" name="questions[]" type="checkbox" value="{!! $question->id !!}" />
                                            </div>
                                        @endif
                                    </td>
                                @endif
                            @endforeach
                        </tr>
                    @endforeach

                    @if( ($alreadyVoted==false || empty($user)) && $eventSchedule->closed == 0)
                        <?php
                        if( !empty($user) ){
                            $userName = $user->name;
                        }else{
                            $userName = "";
                        }
                        ?>
                        <tr class="participation yesNo partMyself">
                            <td class='' style="width:249px;">
                                <div style="width:100%">
                                    <div class="avatarSmall">
                                        <div class="gravatar">
                                            <i class="glyphicon glyphicon-user participantIcon" style='margin-left:0px'></i>
                                        </div>
                                    </div>
                                    <input id="pname" name="name" placeholder="{{ trans('PrivateEventSchedule.yourName') }}" class="form-control name-input" type="text" value="{!! $userName !!}" {{ !empty($user) ? 'readonly="read"' : '' }} style="width:258px;position:relative;float:left;"  />
                                </div>
                            </td>
                            @foreach($eventSchedule->questions as $question)
                                <td data="0" id="box0" class='background'>
                                    <input id="question{!! $question->id !!}" name="questions[]" type="checkbox" value="{!! $question->id !!}" />
                                </td>
                            @endforeach
                        </tr>
                    @endif
                    </tbody>
                </table>

                <input name="eventName" value="{!! $eventSchedule->title !!}" type="hidden" />

                @if($eventSchedule->closed == 0)
                        <!-- Submit -->
                <div class="box-footer" style="{{ ($alreadyVoted == true) ? "display:none;": "" }}">
                    <button type="submit" value="form.save" class="btnSchedule"> {{ trans('PrivateEventSchedule.save') }}</button>
                </div>
                {{ Form::close() }}
            @endif

            @endif

            @if(isset($_REQUEST["action"]) && $_REQUEST["action"] == "edit")

                @if($eventSchedule->closed == 0)
                    {{ Form::open(array('url' => action("EventSchedulesController@publicUpdateAttendance",$eventSchedule->key), 'method' => "put" )) }}
                @endif
                <table cellpadding="0" cellspacing="0" class="tableEventSchedule poll withFooter" style="border-collapse:collapse;">
                    <tbody>
                    <tr class="header date month">
                        <th class="nonHeader headerSchedule " >
                        </th>
                        @foreach($eventSchedule->questions as $question)
                            <th class="rsep text-center " colspan="1">
                                {!! $question->question !!}
                            </th>
                        @endforeach
                    </tr>

                    @foreach($eventSchedule->participants as $participant)
                        <tr class="participant">
                            <td class="sUserName">
                                <div class="row">
                                    <div class="col-md-10 text-left">
                                        <div class="avatarSmall">
                                            <i class="glyphicon glyphicon-user participantIcon"></i>
                                        </div>
                                        <div class='participantName'  title="{!! $participant->name !!}">
                                            @if($participant->id == $_REQUEST["participant_id"]  && $eventSchedule->closed == 0 )
                                                @if(!empty($participant->user_key))
                                                    {!! $participant->name !!}
                                                    <input id="pname" name="name" placeholder="{{ trans('PrivateEventSchedule.yourName') }}" class="name-input" type="hidden" value="{!! $participant->name !!}"/>
                                                @else
                                                    <input id="pname" name="name" placeholder="{{ trans('PrivateEventSchedule.yourName') }}" class="name-input" type="text" value="{!! $participant->name !!}" style='width:100%;'/>
                                                @endif
                                            @else
                                                {!! $participant->name !!}
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-2 text-right">
                                        @if($participant->id == $_REQUEST["participant_id"]  && $eventSchedule->closed == 0 )
                                            <span style="top:6px;position:relative;right:5px;">
                                    <a href="./{{ $eventSchedule->key }}" title="{{ trans('PrivateEventSchedule.edit') }}" class="btn btn-flat btn-info btn-xs" style="" ><i class="fa fa-undo"></i></a>&nbsp;
                                    <a onclick="javascript:deleteAttendance('{{$participant->key}}')" title="{{ trans('PrivateEventSchedule.delete') }}" class="btn btn-flat btn-danger btn-xs" style="display: none;"><i class="fa fa-remove"></i></a>
                                    <input id="participant_id" name="participant_id" type="hidden" value="{!! $participant->id !!}" />
                                    <input id="participant_key" name="participant_key" type="hidden" value="{!! $participant->key !!}" />
                                </span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            @foreach($eventSchedule->questions as $question)
                                <?php
                                $bvar = false;
                                foreach( $participant->questions as $question2 ){
                                    if($question->id == $question2->pivot->es_question_id && $question2->pivot->es_participant_id == $participant->id ){
                                        $bvar = true;
                                    }
                                }
                                ?>
                                @if($bvar)
                                    <td class="partTableCell y sep pok ">
                                        @if($participant->id  == $_REQUEST["participant_id"]  )
                                            <input id="question{!! $question->id !!}" name="questions[]" type="checkbox" value="{!! $question->id !!}" class="periods"  checked="yes" />
                                        @else
                                            <div>
                                                <i class="glyphicon glyphicon-ok btn-lg selectedOk"></i>
                                            </div>
                                        @endif
                                    </td>
                                @else
                                    <td class=" partTableCell n sep pn">
                                        @if($participant->id  == $_REQUEST["participant_id"]  )
                                            <input id="question{!! $question->id !!}" name="questions[]" type="checkbox" value="{!! $question->id !!}" class="periods" />
                                        @endif
                                    </td>
                                @endif
                            @endforeach
                        </tr>
                    @endforeach
                    </tbody>
                </table>

                <input name="eventName" value="{!! $eventSchedule->title !!}" type="hidden" />

                @if($eventSchedule->closed == 0)
                        <!-- Submit -->



                <div class="box-footer">
                    <button type="submit" value="form.save" class="btnSchedule"> {{ trans('PrivateEventSchedule.save') }}</button>
                </div>
                {{ Form::close() }}
            @endif

            @endif

            @if($eventSchedule->closed == 1)
                <div class="row">
                    <div class="col-md-12">
                        <i>{{ trans('PrivateEventSchedule.thisIsClosedMessage') }}</i>
                    </div>
                </div>
            @endif

        </div>
    </div>
@endsection

@section('scripts')
    <script>
        window.editSchedule = false;
        function deleteAttendance(participantKey){
            oneDelete('{{action("EventSchedulesController@publicDeleteAttendance",[$eventSchedule->key])}}'+'/'+participantKey);
        }
        function showEdit(){
            if( window.editSchedule == false ){
                $(".editPeriod").show();
                $(".viewPeriod").hide();
            }else{
                $(".editPeriod").hide();
                $(".viewPeriod").show();
            }
            window.editSchedule = !window.editSchedule;
        }
    </script>
@endsection
@endif