@extends('empaville.presentation.index')

@section('content')
    <div class="content-header" >
        <div class="container" style="padding-top: 0px;text-align: center;border-bottom-color: gray;border-bottom-style: solid;">
            <h2>Data</h2>
        </div>
    </div>

    <div class="content">
        <div class="container" style="height: 100%;margin-top: 15%;text-align: center">
            <a href="{{action('EmpavilleDashboardController@totals', ['cbId'=> $cbId])}}" target="_blank"><span style="font-size: 2em">Analytics</span> </a>
        </div>
        @include('empaville.presentation.carouselRight')
        @include('empaville.presentation.carouselLeft')
    </div>

@endsection