@extends('empaville.presentation.index')

@section('content')
    <div class="content-header" >
        <div class="container" style="padding-top: 0px;text-align: center;border-bottom-color: gray;border-bottom-style: solid;">
            <h2>Who are you?</h2>
        </div>
    </div>
    <div class="content">
        <div class="container" style="padding-top: 5%;">
            <div class="col-lg-4 col-md-4 col-sm-4">
                <div class="col-lg-12 col-md-12 col-sm-12">
                    <h2>Role:</h2>
                    <h3>
                        <ul style="color: #66A2D8">
                            <li style='margin-top: 1em'><span style="color: black">Name</span></li>
                            <li style='margin-top: 1em'><span style="color: black">Age</span></li>
                            <li style='margin-top: 1em'><span style="color: black">Gender</span></li>
                            <li style='margin-top: 1em'><span style="color: black">Neighbourhood</span></li>
                            <li style='margin-top: 1em'><span style="color: black">Profession</span></li>
                            <li style='margin-top: 1em'><span style="color: black">Workplace neighbourhood</span></li>
                            <li style='margin-top: 1em'><span style="color: black">E-mail</span></li>
                        </ul>
                    </h3>
                </div>
            </div>

            <div class="col-lg-8 col-md-8 col-sm-8">
                <div style="text-align: center;">
                    <img src="{{ asset('images/VisitCard.jpg') }}" style="max-height: 400px" />
                </div>
            </div>
        </div>
        @include('empaville.presentation.carouselRight')
        @include('empaville.presentation.carouselLeft')
    </div>

@endsection
