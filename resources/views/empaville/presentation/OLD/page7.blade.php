@extends('empaville.presentation.index')

@section('content')
    <div class="content-header" >
        <div class="container" style="padding-top: 0px;text-align: center;border-bottom-color: gray;border-bottom-style: solid;">
            <h2>Pot of money</h2>
        </div>
    </div>
    <div class="content">
        <div class="container" style="height: 100%;padding-top: 5%;">
            <div class="col-lg-12 col-md-12 col-sm-12" id="box1" style="box-shadow: 0px 0px 23px -3px rgba(0,0,0,0.75);">
                <h2>You will have to indicate the amount of money for your proposal, according to the following budget classes:</h2>
                <h2> Pot of money: 150 000 Empacoins</h2>
                <h2>
                    <ul style="color: #66A2D8">
                        <li style='padding-top: 20px'><span style="color: black">100 000 EmpaCoins</span></li>
                        <li style='padding-top: 20px'><span style="color: black">50 000 EmpaCoins</span></li>
                        <li style='padding-top: 20px'><span style="color: black">25 000EmpaCoins</span></li>
                    </ul>
                </h2>

            </div>
        </div>
        @include('empaville.presentation.carouselRight')
        @include('empaville.presentation.carouselLeft')
    </div>

@endsection