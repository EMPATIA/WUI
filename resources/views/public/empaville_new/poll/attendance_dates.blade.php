@extends('public.empaville_new._layouts.index')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <div class="polls-title">
                    <h3>{{$eventSchedule->title}}</h3>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <div class="polls-description">
                    <h3>{{$eventSchedule->description}}</h3>
                </div>
            </div>
        </div>

        <div>
                @if($eventSchedule->closed == 0)
                    @if(isset($_REQUEST["action"]) && $_REQUEST["action"] == "edit")
                        {{ Form::open(array('url' => action("EventSchedulesController@publicUpdateAttendance",["event_key" => $eventSchedule->key,"participant_id"=>$_REQUEST["participant_id"] ]), 'method' => "put" )) }}
                    @else
                        {{ Form::open(array('url' => action("EventSchedulesController@publicStoreAttendance",$eventSchedule->key), 'method' => "post" )) }}
                    @endif
                @endif

                <div class="pollTableDiv">
                <table class="tablePoll">
                    <tbody>
                        <tr class="header date_month">
                            <th class="nonHeaderPoll">
                            </th>
                            @foreach($periods as $key => $period)
                                <th class="headerPollDate" colspan="{{ count($period["periods"]) }}">
                                    <div class="headerPollDateText col-sx-12">{{ $period["month"] }} {{ $period["year"] }}</div>
                                    <div class="headerPollDateText col-sx-12">{{ $period["dayweek"] }} {{ $period["day"] }}</div>
                                </th>
                            @endforeach
                        </tr>
                        <tr class="time">
                            <th class="nonHeader partCount boldText ">
                            </th>
                            @foreach($periods as $key => $period)
                                @foreach($period["periods"] as $time)
                                    <th class="headerPollhours text-center" colspan="1">
                                        {{$time["period"]}}
                                    </th>
                                @endforeach
                            @endforeach
                        </tr>

                        @foreach($eventSchedule->participants as $participant)
                            <tr class="pollParticipant">
                                <td class="pollParticipantName">
                                    <div class="row">
                                        <div class="col-xs-12">
                                        @if( (isset($_REQUEST["action"]) && $_REQUEST["action"] == "edit" ) &&  $participant->id == $_REQUEST["participant_id"]  && $eventSchedule->closed == 0 )
                                            <span>
                                                <i class="glyphicon glyphicon-user participantIcon"></i>
                                                <input id="pname" name="name" placeholder="{{ trans('defaultPoll.yourName') }}" class="form-schedule name-input" type="text" value="{{$participant->name }}"  />
                                            </span>
                                        @else
                                            <div class="col-md-8 text-left"  title="{{ $participant->name }}">
                                                <span>
                                                    <i class="glyphicon glyphicon-user participantIcon"></i> {{$participant->name}}
                                                </span>
                                            </div>
                                            <div class="col-md-4 text-right">
                                                <div>
                                                    @if(  ( !empty($user) && $user->user_key == $participant->user_key && $eventSchedule->closed == 0) )
                                                        <a href="?action=edit&participant_id={{$participant->id}}" title="{{ trans('defaultPoll.edit') }}" class="btn btn-flat btn-info btn-xs"  ><i class="fa fa-pencil"></i></a>
                                                        <a onclick="deleteAttendance('{{$participant->key}}')" title="{{ trans('defaultPoll.delete') }}" class="btn btn-flat btn-danger btn-xs" ><i class="fa fa-remove"></i></a>
                                                    @elseif(empty($participant->user_key))
                                                        <a href="?action=edit&participant_id={{$participant->id}}" title="{{ trans('defaultPoll.edit') }}" class="btn btn-flat btn-info btn-xs"><i class="fa fa-pencil"></i></a>
                                                        <a onclick="deleteAttendance('{{$participant->key}}')" title="{{ trans('defaultPoll.delete') }}" class="btn btn-flat btn-danger btn-xs"><i class="fa fa-remove"></i></a>
                                                    @endif
                                                </div>
                                            </div>
                                        @endif
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
                                                @if((isset($_REQUEST["action"]) && $_REQUEST["action"] == "edit" ) &&  $participant->id == $_REQUEST["participant_id"]  && $eventSchedule->closed == 0 )
                                                    <td data="0" id="box0" title="{{  $period['month'].' '.$period['day'] }}" class='pollEditReply'>
                                                        <div class="checkboxPoll">
                                                            <input id="period{{ $time['id'] }}" type="checkbox" value="{{ $time['id'] }}"  name="periods[]" class="periods" checked="yes"/>
                                                            <label for="period{{ $time['id'] }}"></label>
                                                        </div>
                                                    </td>
                                                @else
                                                    <td class="participationOptionYes">
                                                        <div class='viewPeriod'>
                                                            <i class="glyphicon glyphicon-ok  btn-lg selectedOk"></i>
                                                        </div>
                                                    </td>
                                                @endif

                                        @else

                                                @if((isset($_REQUEST["action"]) && $_REQUEST["action"] == "edit" ) &&  $participant->id == $_REQUEST["participant_id"]  && $eventSchedule->closed == 0  )
                                                    <td data="0" id="box0" title="{{  $period['month'].' '.$period['day'] }}" class='pollEditReply'>
                                                        <div class="checkboxPoll">
                                                            <input id="period{{ $time['id'] }}" type="checkbox" value="{{ $time['id'] }}"  name="periods[]" class="periods" />
                                                            <label for="period{{ $time['id'] }}"></label>
                                                        </div>
                                                    </td>
                                                @else
                                                    <td class="participationOptionNo">
                                                    </td>
                                                @endif

                                        @endif
                                    @endforeach
                                @endforeach
                            </tr>
                        @endforeach
                        @if( (($alreadyVoted==false || empty($user)) && $eventSchedule->closed == 0 ) && (!isset($_REQUEST["action"])) )
                            <?php
                            if( !empty($user) ){
                                $userName = $user->name;
                            }else{
                                $userName = "";
                            }
                            ?>
                            <tr class="pollParticipant">
                                <td class='pollParticipantNameNew'>
                                    <span>
                                        <i class="glyphicon glyphicon-user participantIcon"></i>
                                        <input id="pname" name="name" placeholder="{{ trans('defaultPoll.yourName') }}" class="form-schedule name-input" type="text" value="{{ $userName }}" {{ !empty($user) ? 'readonly="read"' : '' }}  />
                                    </span>
                                </td>
                                @foreach($periods as $key => $period)
                                    @foreach($period["periods"] as $time)
                                        <td data="0" id="box0" title="{{  $period['month'].' '.$period['day'] }}" class='pollEditReply'>
                                            <div class="checkboxPoll">
                                                <input id="period{{ $time['id'] }}" type="checkbox" value="{{ $time['id'] }}"  name="periods[]" class="periods" />
                                                <label for="period{{ $time['id'] }}"></label>
                                            </div>
                                        </td>
                                    @endforeach
                                @endforeach
                            </tr>
                        @endif
                    </tbody>
                </table>
                </div>
                @if( ($eventSchedule->closed == 0 && ($alreadyVoted==false || empty($user)) )  || ($eventSchedule->closed == 0 && (isset($_REQUEST["action"]) && $_REQUEST["action"] =='edit') && ($alreadyVoted==false || empty($user)) ))
                <!-- Submit -->
                    <div class="pollFormActions">
                        <button type="submit" value="form.save" class="btnPollSubmit"> {{ trans('defaultPolls.save') }}</button>
                    </div>
                    {{ Form::close() }}
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

        $( document ).ready(function() {
            console.log( $('.pollTableDiv')._hasScroll );
        });

    </script>
@endsection