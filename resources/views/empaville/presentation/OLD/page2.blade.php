@extends('empaville.presentation.index')

@section('content')
    <div class="content" id="divCont">
        <div class="container" style="height: 100%;padding-top: 5%;">
            <div class="col-lg-6 col-md-6 col-sm-6">
                <div style="text-align: center;">
                    <span class="empaville-lg"><img src="{{ asset('images/EmpatyMap1.png') }}" style="max-height: 400px"/></span>
                </div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6" style="padding-top: 10%">
                <h1 style="color: #66A2D8;font-size: 5em"><b>Lets play!</b></h1>
                <h2>Roles and rules...</h2>
            </div>
        </div>
        @include('empaville.presentation.carouselRight')
        @include('empaville.presentation.carouselLeft')
    </div>
@endsection
