@extends('private._private.index')

@section('content')

    @php
    $listType = array(1 => "Event with dates", 2 => "Question type");
    @endphp

    @if(ONE::actionType('eventSchedule') == 'create' )
        @php
        $form = ONE::form('eventSchedule', trans('privateEventSchedules.details'), 'q', 'poll')
                ->settings(["model" => isset($eventSchedule) ? $eventSchedule : null])
                ->show('EventSchedulesController@edit', 'EventSchedulesController@delete', ['id' => isset($eventSchedule) ? $eventSchedule->key : null], 'EventSchedulesController@index')
                ->create('EventSchedulesController@store', 'EventSchedulesController@show', ['id' => isset($eventSchedule) ? $eventSchedule->key : null])
                ->edit('EventSchedulesController@update', 'EventSchedulesController@show', ['id' => isset($eventSchedule) ? $eventSchedule->key : null])
                ->open()
        @endphp
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-body">
                        {!! Form::hidden('entity_id', 1) !!}
                        {!! Form::hidden('closed', 0) !!}
                        {!! Form::oneText('title', trans('eventSchedule.title'), isset($eventSchedule) ? $eventSchedule->title : null, ['class' => 'form-control', 'id' => 'title','required' => 'required']) !!}
                        {!! Form::oneTextArea('description', trans('eventSchedule.description'), isset($eventSchedule) ? $eventSchedule->description : null, ['class' => 'form-control', 'id' => 'description', 'rows' =>3]) !!}
                        {!! Form::oneText('local', trans('eventSchedule.local'), isset($eventSchedule) ? $eventSchedule->local : null, ['class' => 'form-control', 'id' => 'local']) !!}
                        {!! Form::oneSelect('type_id', trans('eventSchedule.type'), isset($listType) ? $listType : null,null, null, ['id' => 'type_id','required' => 'required']) !!}
                        {!! Form::oneCheckbox('public', trans('eventSchedule.public'), 1, isset($eventSchedule->public) ? $eventSchedule->public : null, [ 'id' => 'public']) !!}
                        @section('scripts')
                            <script>
                                $( "#type_id" ).change(function() {
                                    if($( "#type_id" ).val() == 1) {
                                        $("#type1").show();
                                        $("#type2").hide();
                                    } else if($( "#type_id" ).val() == 2) {
                                        $("#type1").hide();
                                        $("#type2").show();
                                    } else {
                                        $("#type1").hide();
                                        $("#type2").hide();
                                    }
                                });
                            </script>
                        @endsection
                    </div>
                </div>
            </div>
        </div>

        <div id="type1" style="display:none;">
            <div class="row">
                <div class="col-md-12">
                    <!-- Periods LIST -->
                    <div class="box box-primary">
                        <div class="box-header">
                            <h3 class="box-title"><i class='glyphicon glyphicon-calendar'></i> {!! trans('eventSchedule.periods') !!}</h3>
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body no-padding">
                            <div id='periods' class="user-panel">
                                <div class='row'>
                                    <div class='col-md-6'><b>{!!trans('eventSchedule.date')!!}</b></div>
                                    <div class='col-md-5'><b>{!!trans('eventSchedule.time')!!}</b></div>
                                    <div class='col-md-1 text-right'>
                                        @if(ONE::actionType('eventSchedule') == 'edit' || ONE::actionType('eventSchedule') == 'create' )
                                            <a onclick="javascript:addPeriod();" title="{!! trans('eventSchedule.addPeriod') !!}" class="btn btn-flat btn-success btn-sm" href=#" data-original-title="form.create"><i class="fa fa-plus"></i></a>
                                        @endif
                                    </div>
                                </div>
                                @foreach((isset($eventSchedule->periods)?$eventSchedule->periods:[]) as $period)
                                    <div id='eventSchedule{!! $period->id !!}' class='row'>
                                        <div class='col-md-6'>
                                            <i id='eventScheduleTrashedIcon{!! $period->id !!}' style="color:red;cursor:pointer;margin-top:10px;display:none;float:left;margin-right:5px;" class="fa fa-trash"></i>
                                            {!! Form::oneDate('start_date[]', trans('eventSchedule.start_date'), isset($period) ? substr($period->start_date, 0, 10) : "", ['id' => 'start_date'.$period->id, 'data-date-format' => 'mm/dd/yyyy','startDate' => '-3d' ]) !!}
                                        </div>
                                        <div class='col-md-5'>
                                            {!! Form::oneTime('start_time[]', null, isset($eventSchedule) ? $period->start_time : "", ['id' => 'start_time']) !!}

                                        </div>
                                        <div class='col-md-1  text-right'>
                                            @if(ONE::actionType('eventSchedule') == 'edit')
                                                <i id='eventScheduleTrashed{!! $period->id !!}' style="color:red;cursor:pointer;margin-top:10px;" class="fa fa-remove" onclick='removePeriod({!! $period->id !!})'></i>
                                                <i id='eventScheduleRestore{!! $period->id !!}' style="color:green;cursor:pointer;margin-top:10px;display:none" class="fa fa-repeat" onclick='restorePeriod({!! $period->id !!})'></i>
                                                <input name='period_id[]' value='{!! $period->id !!}' type='hidden' />
                                                <input id='remove_id{!! $period->id !!}' name='remove[]' value='0' type='hidden'/>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach

                                @if(ONE::actionType('eventSchedule') == 'edit')
                                    <div class='row'>
                                        <div class='col-md-6'>
                                            {!! Form::oneDate('start_date[]', trans('eventSchedule.start_date'), null, ['id' => 'start_date']) !!}
                                        </div>
                                        <div class='col-md-5'>
                                            {!! Form::oneTime('start_time[]', null, null, ['id' => 'start_time']) !!}
                                        </div>
                                        <div class='col-md-1 text-right'>
                                            <i style="color:red;cursor:pointer;margin-top:10px;" class="fa fa-remove" onclick='removeNewPeriod(this)'></i>
                                            <input name='period_id[]'  value='' type='hidden'/>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <!--/.box -->
                </div>
            </div>
        </div>

        <div id="type2" style="display:none;">
            <div class="row">
                <div class="col-md-12">
                    <!-- Periods LIST -->
                    <div class="box box-primary">
                        <div class="box-header">
                            <h3 class="box-title"><i class='glyphicon glyphicon glyphicon-question-sign'></i> {!! trans('eventSchedule.questions') !!}</h3>
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body no-padding">
                            <div id='questions' class="user-panel">
                                <div class='row'>
                                    <div class='col-md-11'><b>{!!trans('eventSchedule.question')!!}</b></div>
                                    <div class='col-md-1 text-right'>
                                        @if(ONE::actionType('eventSchedule') == 'edit' || ONE::actionType('eventSchedule') == 'create' )
                                            <a onclick="javascript:addQuestion();" title="{!! trans('eventSchedule.addQuickQuestion') !!}" class="btn btn-flat btn-success btn-sm" href=#" data-original-title="eventSchedule.addQuickQuestion"><i class="fa fa-plus"></i></a>
                                        @endif
                                    </div>
                                </div>

                                @if(ONE::actionType('eventSchedule') == 'edit')
                                    <div class='row'>
                                        <div class='col-md-11'>
                                            {!! Form::oneText('question[]', trans('eventSchedule.question'), null, ['id' => 'question']) !!}
                                        </div>
                                        <div class='col-md-1 float-right'>
                                            <i style="color:red;cursor:pointer;margin-top:10px;" class="fa fa-remove" onclick='removeNewQuestion(this)'></i>
                                            <input name='question[]'  value='' type='hidden'/>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <!--/.box -->
                </div>
            </div>
        </div>

        {!! $form->make() !!}

    @else

        @php

        $form = ONE::form('eventSchedule', trans('privateEventSchedules.details'), 'q', 'poll')
                ->settings(["model" => isset($eventSchedule) ? $eventSchedule : null])
                ->show('EventSchedulesController@edit', 'EventSchedulesController@delete', ['id' => isset($eventSchedule) ? $eventSchedule->key : null], 'EventSchedulesController@index')
                ->create('EventSchedulesController@store', 'EventSchedulesController@show', ['id' => isset($eventSchedule) ? $eventSchedule->key : null])
                ->edit('EventSchedulesController@update', 'EventSchedulesController@show', ['id' => isset($eventSchedule) ? $eventSchedule->key : null])
                ->open();
        @endphp
        <div class="row">
            <div class="col-md-12">
                {!! Form::hidden('entity_id', 1) !!}
                {!! Form::oneText('title', trans('eventSchedule.title'), isset($eventSchedule) ? $eventSchedule->title : null, ['class' => 'form-control', 'id' => 'title']) !!}
                {!! Form::oneTextArea('description', trans('eventSchedule.description'), isset($eventSchedule) ? $eventSchedule->description : null, ['class' => 'form-control', 'id' => 'description', 'rows' =>3]) !!}
                {!! Form::oneText('local', trans('eventSchedule.local'), isset($eventSchedule) ? $eventSchedule->local : null, ['class' => 'form-control', 'id' => 'local']) !!}
                <dt>{!! trans('eventSchedule.type') !!}</dt><dd> {!! $listType[$eventSchedule->type_id] !!}  </dd>
                <hr style="margin: 10px 0 10px 0">

                <dt>{!! trans('eventSchedule.url') !!}</dt><dd>  <a href="{{ action("EventSchedulesController@publicAttendance",$eventSchedule->key) }}" target="_blank">{{ action("EventSchedulesController@publicAttendance",$eventSchedule->key) }}</a> </dd>
                <hr style="margin: 10px 0 10px 0">
                {!! Form::oneCheckbox('closed', trans('eventSchedule.closed'), 1, isset($eventSchedule->closed) ? $eventSchedule->closed : null, ['id' => 'closed']) !!}
                {!! Form::oneCheckbox('public', trans('eventSchedule.public'), 1, isset($eventSchedule->public) ? $eventSchedule->public : null, ['id' => 'public']) !!}
            </div>
        </div>
        {!! $form->make() !!}

        @if(ONE::verifyUserPermissions('q', 'poll', 'update'))
            <!-- Periods -->
            <div class="box box-primary">
                <div class="box-header">
                    <h3 class="box-title">{!! trans('privateEventSchedules.period_details') !!}</h3>
                    @if(ONE::verifyUserPermissions('q', 'poll_periods', 'create'))
                        <div class="box-tools pull-right">
                            <a class="btn btn-flat btn-success btn-sm" href="{!! action('EventSchedulesController@editPeriods', $eventSchedule->key) !!}?f=eventSchedule"><i class="fa fa-pencil"></i></a>
                        </div>
                    @endif
                </div>
                <div class="box-body">
                    <dl>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="box-body no-padding">
                                    @if($eventSchedule->type_id == 1)
                                        <div class="user-panel" id="periods">
                                            <div class="row">
                                                <div class="col-md-6"><b>{!! trans('eventSchedule.date') !!}</b></div>
                                                <div class="col-md-6"><b>{!! trans('eventSchedule.time') !!}</b></div>
                                            </div>
                                            @foreach((isset($eventSchedule->periods)?$eventSchedule->periods:[]) as $period)
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <dd> {!! $period->start_date !!} </dd>
                                                    </div>
                                                    <div class="col-md-5">
                                                        <dd> {!! $period->start_time !!} </dd>
                                                    </div>
                                                    <div class='col-md-1 text-center'>
                                                        @if($eventSchedule->closed == 1)
                                                            {{ ($eventSchedule->es_period_id==$period->id) ? "<i class='glyphicon glyphicon-ok'></i>" : "" }}
                                                        @endif
                                                    </div>
                                                </div>
                                                <hr style="margin: 10px 0 10px 0" />
                                            @endforeach
                                        </div>
                                    @elseif($eventSchedule->type_id == 2)
                                        <div class="user-panel" id="questions">
                                            <div class="row">
                                                <div class="col-md-12"><b>{!! trans('eventSchedule.question') !!}</b></div>
                                            </div>
                                            @foreach((isset($eventSchedule->questions)?$eventSchedule->questions:[]) as $question)
                                                <div class="row">
                                                    <div class="col-md-11">
                                                        <dd> {!! $question->question !!} </dd><hr style="margin: 10px 0 10px 0">
                                                    </div>
                                                    <div class='col-md-1 text-center'>
                                                        @if($eventSchedule->closed == 1)
                                                            {{ ($eventSchedule->es_question_id==$question->id) ? "<i class='glyphicon glyphicon-ok'></i>" : "" }}
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                            <hr style="margin: 10px 0 10px 0" />
                                        </div>
                                    @endif

                                </div>
                            </div>
                        </div>
                    </dl>
                </div>
            </div>
        @endif
    @endif

@endsection

<script>
    function addPeriod(){
        var html = "<div class='row'>";
        html += '<div class="col-md-6">';
        html += '<div class="form-group ">';
        html += '<div>';
        html += '{!! Form::oneDate('start_date[]', null,null, ['id' => 'start_date'],array(),true) !!}';
        html += '</div>';
        html += '</div></div>';
        html += '<div class="col-md-5">';
        html += '<div class="form-group ">';
        html += '{!! Form::oneTime('start_time[]', null, null, ['id' => 'start_time'],array(),true) !!}';
        html += '</div>';
        html += '</div>';
        html += '<div class="col-md-1 text-right"> <i style="color:red;cursor:pointer;margin-top:10px;" class="fa fa-remove" onclick="removeNewPeriod(this)"></i> <input  name="period_id[]" value=""  type="hidden"/>  <input name="remove[]" value="0" type="hidden"/>   </div>';
        html += '</div>';
        $("#periods").append(html);
        // Render DatePicker and Time Pickers
        loadDatePickers();
        loadTimePickers();
    }
    function addQuestion(){
        var html = "<div class='row'>";
        html += '<div class="col-md-11">';
        html += '<div class="form-group">';
        html += '<input id="question" name="question[]" type="text" value="" class="form-control" />';
        html += '</div></div>';
        html += '<div class="col-md-1 text-right"> <i style="color:red;cursor:pointer;margin-top:10px;" class="fa fa-remove" onclick="removeNewQuestion(this)"></i></div>';
        html += '</div>';
        $("#questions").append(html);
    }
    function removeNewPeriod(object){
        $(object).parent().parent().remove();
    }
    function removeNewQuestion(object){
        $(object).parent().parent().remove();
    }
</script>