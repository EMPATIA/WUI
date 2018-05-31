@extends('public.default._layouts.index')
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


            <table class="tablePoll">
                <tbody>
                <tr class="header date_month">
                    <th class="nonHeaderPoll">
                    </th>
                    @foreach($eventSchedule->questions as $question)
                        <th class="headerPollDate" colspan="1">
                            {!! $question->question !!}
                        </th>
                    @endforeach
                </tr>

                @foreach($eventSchedule->participants as $participant)
                    <tr class="pollParticipant">
                        <td class="pollParticipantName">
                            <div class="row">
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
                                @if((isset($_REQUEST["action"]) && $_REQUEST["action"] == "edit" ) &&  $participant->id == $_REQUEST["participant_id"]  && $eventSchedule->closed == 0 )
                                    <td data="0" id="box0"  class='pollEditReply'>
                                        <div class="checkboxPoll">
                                            <input id="question{{ $question->id }}" type="checkbox" value="{{ $question->id }}"  name="questions[]" class="questions" checked="yes"/>
                                            <label for="question{{ $question->id }}"></label>
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
                                    <td data="0" id="box0"  class='pollEditReply'>
                                        <div class="checkboxPoll">
                                            <input id="question{{ $question->id }}" type="checkbox" value="{{ $question->id }}"  name="questions[]" class="periods" />
                                            <label for="question{{ $question->id }}"></label>
                                        </div>
                                    </td>
                                @else
                                    <td class="participationOptionNo">
                                    </td>
                                @endif

                            @endif
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
                        @foreach($eventSchedule->questions as $question)
                            <td data="0" id="box0"  class='pollEditReply'>
                                <div class="checkboxPoll">
                                    <input id="question{{$question->id }}" type="checkbox" value="{{ $question->id }}"  name="questions[]" class="questions"/>
                                    <label for="question{{ $question->id }}"></label>
                                </div>
                            </td>

                        @endforeach
                    </tr>
                @endif
                </tbody>
            </table>

            @if($eventSchedule->closed == 0)
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
    </script>
@endsection