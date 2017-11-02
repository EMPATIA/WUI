@extends('empaville.presentation.index')

@section('content')
    <div class="content" id="divCont">
        <div class="container" style="height: 100%;padding-top: 5%;padding-bottom: 5%">
            <div class="col-lg-6 col-md-6 col-sm-6">
                <div style="text-align: center;">
                    <span class="empaville-lg"><img src="{{ asset('images/empaville_map.png') }}" style="max-width: 80%"/></span>
                </div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6" style="padding-top: 10%;">
                <h1 style="color: #66A2D8;font-size: 4em"><b>"Mine the data"</b></h1>

                <a href="{{action('CbsController@voteAnalysis', ['type' => 'idea', 'cbKey'=> $cbKey])}}" target="_blank"><span class="fa fa-hand-o-right" style="font-size: 2em">Winners and data analysis</span> </a>
            </div>
        </div>
        @include('empaville.presentation.carouselLeft')
    </div>
@endsection