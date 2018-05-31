@extends('private._private.index')


@section('content')
    @php

    $form = ONE::form('session')
            ->settings(["model" => isset($session) ? $session->session_key : null,'id'=>isset($session) ? $session->session_key : null])
            ->show('ConferenceEventSessionController@edit', 'ConferenceEventSessionController@delete', ['eventKey' =>isset($eventKey) ? $eventKey : null, 'sessionKey'=> isset($session) ? $session->session_key : null], 'ConferenceEventsController@show', ['eventKey' => isset($eventKey) ? $eventKey : null])
            ->create('ConferenceEventSessionController@store', 'ConferenceEventsController@show', ['eventKey' =>isset($eventKey) ? $eventKey : null])
            ->edit('ConferenceEventSessionController@update', 'ConferenceEventSessionController@show', ['eventKey' =>isset($eventKey) ? $eventKey : null, 'sessionKey'=> isset($session) ? $session->session_key : null])
            ->open();
    @endphp

    {!! Form::hidden('event_key', isset($eventKey) ? $eventKey : null) !!}
    {!! Form::hidden('session_key', isset($session) ? $session->session_key : null) !!}
    @if(count($languages) > 0)
        @foreach($languages as $language)
            @php $form->openTabs('tab-translation-' . $language->code, $language->name); @endphp
            <div style="padding: 10px;">

                {!! Form::oneText('title_'.$language->code .'', trans('conferenceEvents.title_'.$language->code .''), isset($translations[$language->code]) ? $translations[$language->code]->name : null, ['class' => 'form-control', 'id' => 'title_'.$language->code .'' , $language->default == 1 ? 'required' : '']) !!}
                {!! Form::oneTextArea('description_'.$language->code .'', trans('conferenceEvents.description_'.$language->code .''), isset($translations[$language->code]) ? $translations[$language->code]->description : null, ['class' => 'form-control webContent', 'id' => 'summary_'.$language->code]) !!}

            </div>
        @endforeach
        @php $form->makeTabs(); @endphp
    @endif
    <div class="card flat">
        <div class="card-header">Schedules
            @if(ONE::actionType('session') != 'show')
                <a class="btn btn-flat btn-success btn-sm pull-right" title="" data-delay="{show:1000}" data-toggle="tooltip" onclick="addSchedule()" data-original-title="form.create">
                    <i class="fa fa-plus"></i>
                </a>
            @endif
        </div>

        <div class="box-body btn-group" id="schedules_div">
            @if(ONE::actionType('session') == 'create')
                <div class="btn-group" id="div_default">
                    <div class="card" >
                        <div class="card-header">Schedule
                            <a class="btn btn-flat btn-danger btn-sm pull-right" id="default" title="" data-delay="{show:1000}" data-toggle="tooltip"  data-original-title="form.delete" onclick="deleteSchedule(this)">
                                <i class="fa fa-remove"></i>
                            </a>
                        </div>

                        <div class="box-body">
                            {!! Form::oneDate('newStartDate[]', trans('conferenceEvents.startDate'), $eventStartDate, ['id' => 'startDate']) !!}
                            {!! Form::oneDate('newEndDate[]', trans('conferenceEvents.endDate'), $eventStartDate, ['id' => 'endDate']) !!}
                            {!! Form::oneTime('newStartTime[]',  trans('conferenceEvents.startTime'),date('H:i') ,['id' => 'startTime']) !!}
                            {!! Form::oneTime('newEndTime[]',  trans('conferenceEvents.endTime'),date('H:i') ,['id' => 'endTime']) !!}
                        </div>
                    </div>
                </div>

            @endif
            @if(isset($schedules))
                @foreach($schedules as $schedule)
                    <div class="btn-group" id="div_{{$schedule->schedule_key}}">
                        <div class="card" >
                            <div class="card-header">Schedule
                                @if(ONE::actionType('session') != 'show')
                                    <a class="btn btn-flat btn-danger btn-sm pull-right" id="{{$schedule->schedule_key}}" title="" data-delay="{show:1000}" data-toggle="tooltip"  data-original-title="form.delete" onclick="deleteSchedule(this)">
                                        <i class="fa fa-remove"></i>
                                    </a>
                                @endif
                            </div>

                            <div class="box-body">
                                {!! Form::hidden('scheduleKey[]',$schedule->schedule_key) !!}
                                {!! Form::oneDate('startDate_'.$schedule->schedule_key, trans('conferenceEvents.startDate'), isset($schedule) ? substr($schedule->start_date, 0, 10) : null, ['id' => 'startDate', 'readonly' => isset($schedule)?($schedule->start_date < date('Y-m-d') ? 'readonly' : null):null]) !!}
                                {!! Form::oneDate('endDate_'.$schedule->schedule_key, trans('conferenceEvents.endDate'), isset($schedule) ? substr($schedule->end_date, 0, 10) : null, ['id' => 'endDate']) !!}
                                {!! Form::oneTime('startTime_'.$schedule->schedule_key,  trans('conferenceEvents.startTime'), isset($schedule) ? substr($schedule->start_time, 0, 10) : null, ['id' => 'startTime']) !!}
                                {!! Form::oneTime('endTime_'.$schedule->schedule_key,  trans('conferenceEvents.endTime'), isset($schedule) ? substr($schedule->end_time, 0, 10) : null, ['id' => 'endTime']) !!}

                            </div>
                        </div>
                    </div>

                @endforeach
            @endif
        </div>
    </div>
    @if(ONE::actionType('session') == 'show')
        <div class="card flat">
            <div class="card-header">Speakers</div>
            <div class="box-body">
                <table id="speaker_list" class="table table-hover table-striped dataTable no-footer table-responsive">
                    <thead>
                    <tr>
                        <th>{{ trans('conferenceEvents.key') }}</th>
                        <th>{{ trans('conferenceEvents.name') }}</th>
                        <th>{!! ONE::actionButtons(['eventKey' =>$eventKey,'sessionKey' =>isset($session) ? $session->session_key : null], ['create' => 'ConferenceEventSpeakerController@create']) !!}</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    @endif

    {!! $form->make() !!}

@endsection
@section('scripts')
    <script src="{{ asset("js/tinymce/tinymce.min.js") }}"></script>
    <script>
        $( document ).ready(function() {
            {!! ONE::addTinyMCE(".webContent",array("plugins" => [])) !!}
         });
    </script>
    
    @if(ONE::actionType('session') != 'show')
        <script>

            function deleteSchedule(elem){
                var id = $(elem).attr("id");
                var id_2 = 'div_'+id;
                $('#'+id_2).html("");

            }
            function addSchedule(){
                var schedulesDiv = $('#schedules_div');
                var i = $('#schedules_div').children().length;

                var html = '<div class="btn-group" id="div_'+i+'"><div class="card"><div class="card-header">Schedule<a class="btn btn-flat btn-danger btn-sm pull-right" id="'+i+'" title="" data-delay="{show:1000}" data-toggle="tooltip"  data-original-title="form.delete" onclick="deleteSchedule(this)">';
                html += '<i class="fa fa-remove"></i></a></div>';
                html += '<div class="box-body">';
                html += '{!! Form::oneDate('newStartDate[]', trans('conferenceEvents.startDate'), $eventStartDate, ['id' => 'startDate']) !!}';
                html += '{!! Form::oneDate('newEndDate[]', trans('conferenceEvents.endDate'), $eventEndDate, ['id' => 'endDate']) !!}';
                html += '</div></div></div>';
                $(html).appendTo(schedulesDiv);
                // Render DatePicker

                $('.oneDatePicker').datepicker({
                    startDate: '{!! $eventStartDate !!}',
                    endDate : '{!! $eventEndDate !!}'
                });
//            reloadDatePickers();
            }

            $('.oneDatePicker').datepicker({
                startDate: '{!! $eventStartDate !!}',
                endDate : '{!! $eventEndDate !!}'
            });

        </script>

    @endif
    @if(ONE::actionType('speaker') == 'show')
        <script>


            $(function () {
                $('#speaker_list').DataTable({
                    language: {
                        url: '{!! asset('/datatableLang/'.Session::get('LANG_CODE').'.json') !!}',
                        search: '<a class="btn searchBtn" id="searchBtn"><i class="fa fa-search"></i></a>'

                    },
                    processing: true,
                    serverSide: true,
                    ajax: '{!! action('ConferenceEventSpeakerController@getIndexTable',['eventKey'=>$eventKey,'sessionKey'=>isset($session) ? $session->session_key : null]) !!}',
                    columns: [
                        { data: 'speaker_key', name: 'speaker_key', width: "20px" },
                        { data: 'title', name: 'title' },
                        { data: 'action', name: 'action', searchable: false, orderable: false, width: "30px" },
                    ],
                    order: [['1', 'asc']],
                    responsive: true,
                    columnDefs: [
                        { responsivePriority: 1, targets: 1 },
                        { responsivePriority: 2, targets: -1 }
                    ]                    
                });

            });


        </script>
    @endif


@endsection


