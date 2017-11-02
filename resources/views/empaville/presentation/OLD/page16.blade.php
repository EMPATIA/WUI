@extends('empaville.presentation.index')

@section('content')
    <div class="content">
        <div class="container" style="padding-top: 5%">
            <div class="col-lg-12 col-md-12 col-sm-12">
                <h1 style="color: #66A2D8;font-size: 9em;"><b>Thank You</b></h1>
            </div>
            <div class="col-lg-12 col-md-12 col-sm-12" style="padding-top: 10%">
                <div class="col-lg-6 col-md-6 col-sm-6">
                    <h3>You can find us at:</h3>
                    <h3>
                        <ul style="color: #66A2D8">
                            <li style='padding-top: 20px'><span style="color: black">empatia@empatia-project.eu</span></li>
                            <li style='padding-top: 20px'><span style="color: black">http://empatia-project.eu</span></li>
                        </ul>
                    </h3>

                </div>
                <div class="col-lg-6 col-md-6 col-sm-6" style="text-align: center;">
                    <span class="empaville-lg"><img src="{{ asset('images/empatia.jpg') }}" style="max-width: 80%"/></span>
                </div>
            </div>
            @include('empaville.presentation.carouselLeft')
        </div>
    </div>
@endsection