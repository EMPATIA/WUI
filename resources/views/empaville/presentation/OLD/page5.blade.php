@extends('empaville.presentation.index')

@section('content')
    <div class="content-header" >
        <div class="container" style="padding-top: 0px;text-align: center;border-bottom-color: gray;border-bottom-style: solid;">
            <h2>Setting</h2>
        </div>
    </div>
    <div class="content">
        <div class="container" style="height: 100%;padding-top: 5%;">
            <div class="col-lg-12 col-md-12 col-sm-12">
                <h2>
				<ul style="color: #66A2D8">
				<li><span style="color: #000000">We have 1 assembly in Uptown, 2 assemblies in Middletown and 1 Downtown, each one with one facilitator</span></li>
                <li style="margin-top: 45px"><span style="color: #000000">We are in a neighbourhood assembly and our first objective is to collect project proposals</span></li>
				<li style="margin-top: 45px"><span style="color: #000000">From now on you are in character, follow the instructions of your facilitator!</span></li>
				</ul></h2>
            </div>
        </div>
        @include('empaville.presentation.carouselRight')
        @include('empaville.presentation.carouselLeft')
    </div>

@endsection