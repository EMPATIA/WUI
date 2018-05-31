@extends('public.empaville._layouts.index')

@section('content')

    <div class="box box-solid box-deflaut">
        <div class="box-header " style="color: white; background-color: #333333;">
            <h3 style="padding-top: 5px;display: inline-block;margin: 0;line-height: 1;">
                <i class="glyphicon glyphicon-calendar"></i>
                {!! $eventTranslation->name !!}
            </h3>
            <div style="float:right">
                <a class="btn btn-flat" style="color: white;background-color: #39B54A" href="#">{!! trans('PublicConference.registration') !!}</a>
            </div>
        </div>
        <div class="box-body" style="margin-top: 10px;background-color: white">

            @foreach($sessions as $session)
                <div class="box box-solid box-deflaut">
                    <div class="box-header" style="color: white; background: linear-gradient(to right, rgb(57,181,74),      rgb(140,198,63));">
                        <h3 style="padding-top: 5px;display: inline-block;margin: 0;line-height: 1;">
                            {!! $session->session_translations[0]->name !!}
                        </h3>
                    </div>
                    <div class="box-body">
                        <div class="col-lg-3">
                            <div class="row">
                                <div class="box box-solid text-center" style="color: white; background-color: #333333;">
                                    <h5 style="padding-top: 5px;display: inline-block;margin: 0;line-height: 2;">
                                        {!! date('M-d',time($event->start_date))!!}
                                    </h5>
                                </div>
                            </div>
                            <div class="row">
                                <div class="box box-solid text-center" style="color: white; background-color: #333333;">
                                    <h5 style="padding-top: 5px;display: inline-block;margin: 0;line-height: 2;">
                                        {!! date('H:d',time($session->created_at)) !!}
                                    </h5>
                                </div>
                            </div>


                        </div>
                        <div class="col-lg-3">
                                <div class="box box-solid">
                                    <div class="box-header with-border">
                                        <h3 class="box-title" style="color: #39B54A;">Speakers</h3>
                                    </div>
                                    <div class="box-body text-center" >
                                        sadasfasfasf
                                    </div>
                                </div>

                        </div>
                        <div class="col-lg-6">
                            <div class="box box-solid">
                                <div class="box-header with-border">
                                    <h3 class="box-title" style="color: #39B54A;">Description</h3>
                                </div>
                                <div class="box-body text-center" >
                                    sadasfasfasf
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            @endforeach

        </div>
    </div>
@endsection