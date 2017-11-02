@extends('public.empaville._layouts.index')

@section('content')

    <div class="box box-solid box-deflaut">
        <div class="box-header " style="color: white; background-color: #333333;">
            <div class="row">
                <div class="col-md-12">
                    <div>
                        <h3 style="padding-top: 5px;display: inline-block;margin: 0;line-height: 1;">
                            <i class="glyphicon glyphicon-calendar"></i>
                            {!! $event->event_translations[0]->name !!}
                        </h3>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 col-sm-8">
                    <div class="">
                        <span >
                            {!! date_format(new DateTime($event->start_date),'Y,M d')!!}
                        </span>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-4" >
                    <div class="pull-right">
                        @if($registered == false)
                            <a class="btn btn-flat" style="color: white;background-color: #39B54A" href="{{URL::action("PublicConfEventsController@setRegistration", $event->event_key)}}">Registration</a>
                        @else
                            <span  style="color: white;background-color: #39B54A" >{!! trans('PublicConference.youAlreadyRegistered') !!}</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="box-body" style="margin-top: 10px;background-color: white">
            <div class="box box-default box-body">
                <span class="description">
                    {!! html_entity_decode($event->event_translations[0]->description) !!}
                </span>
            </div>
            <div>
                <ul class="timeline">
                    @foreach($event->sessions as $session)
                        <li class="time-label">
                            <span class="bg-green">
                                {{($session->schedules[0]->start_time)}}
                                @if (!empty($session->schedules[0]->end_time))
                                    - {{$session->schedules[0]->end_time}}
                                @endif
                            </span>
                            <div class="timeline-item">
                                <h3 class="timeline-header ">
                                    <strong>
                                        {{$session->session_translations[0]->name}}
                                    </strong>
                                </h3>
                                @if($session->session_translations[0]->description != "")
                                    <div class="timeline-body">
                                        {!!html_entity_decode($session->session_translations[0]->description) !!}
                                    </div>
                                @endif
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    <div class="box box-solid box-deflaut">
        <div class="box-body" style="margin-top: 10px;background-color: white">
            <div style="margin-bottom: 10px;">
                <span><strong>For more informations, please contact:</strong></span>
            </div>

            <div>
                <span>Prof Vishanth Weerakkody -  <a href="mailto:vishanth.weerakkody@brunel.ac.uk">vishanth.weerakkody@brunel.ac.uk</a></span>
            </div>
            <div>
                <span>Dr. Sankar Sivarajah - <a href="mailto:sankar.sivarajah@brunel.ac.uk">sankar.sivarajah@brunel.ac.uk</a></span>
            </div>
            <div style="margin-top: 10px;">
                <span><strong>Brunel University</strong></span><br>
                <span>Eastern Gateway Building, Brunel University London, Uxbridge, UB8 3PH, United Kingdom</span><br>
                <span> T +44 (0) 1895 274000 | F +44 (0) 1895 232806</span>
            </div>
        </div>
    </div>
    <div class="box box-solid box-deflaut">
        <div class="box-header ">
            <span><strong>{!! trans('PublicConference.location') !!}</strong></span>
        </div>
        <div class="box-body" style="background-color: white">
            <div class="col-md-12">
                <iframe style="width: 100%;height: 500px" src="//www.google.com/maps/embed/v1/place?q=Brunel+University+London&zoom=15&key=AIzaSyCarOYi2GTb1Uj99WqqnTIosY-ZAy92uqs">
                </iframe>
            </div>
        </div>
    </div>
@endsection