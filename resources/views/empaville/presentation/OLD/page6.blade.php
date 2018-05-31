@extends('empaville.presentation.index')

@section('content')
    <div class="content-header" >
        <div class="container" style="padding-top: 0px;text-align: center;border-bottom-color: gray;border-bottom-style: solid;">
            <h2>Map</h2>
        </div>
    </div>
    <div class="content">
    <div class="container" style="width: 100%;padding-top: 3%;">
        <div class="col-lg-7 col-md-7 col-sm-7">
            <div style="text-align: center;">
                <span class="empaville-lg"><img src="{{ asset('images/EmpatyMap.jpg') }}"/></span>
            </div>
        </div>
        <div class="col-lg-5 col-md-5 col-sm-5">
            <div class="col-lg-12 col-md-12 col-sm-12">
                <h3>You can make proposals in the following areas:</h3>
                <h4>
                    <ul style="color: #66A2D8">
                        <li style='padding-top: 20px'><span style="color: black">Downtown</span></li>
                        <li style='padding-top: 20px'><span style="color: black">Middletown</span></li>
                        <li style='padding-top: 20px'><span style="color: black">Uptown</span></li>
                    </ul>
                </h4>
            </div>
            <div class="col-lg-12 col-md-12 col-sm-12">
                <h3>You can make proposals in the following categories:</h3>
                <h4>
                    <ul style="color: #66A2D8">
                        <li style='padding-top: 20px'><span style="color: black">Security</span></li>
                        <li style='padding-top: 20px'><span style="color: black">Parks</span></li>
                        <li style='padding-top: 20px'><span style="color: black">Urban works</span></li>
                        <li style='padding-top: 20px'><span style="color: black">Cultural/social services</span></li>
                        <li style='padding-top: 20px'><span style="color: black">Transports</span></li>
                    </ul>
                </h4>
            </div>
        </div>
    </div>
    </div>
    @include('empaville.presentation.carouselRight')
    @include('empaville.presentation.carouselLeft')
@endsection
