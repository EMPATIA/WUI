@extends('empaville.presentation.index')

@section('content')
    <div class="content-header">
        <div style="padding-top: 0px;text-align: center;border-bottom-color: gray;border-bottom-style: solid;" class="container">
            <h2>Roles</h2>
        </div>
    </div>
    <div class="content" id="divCont">
        <div class="container" style="height: 100%;padding-top: 3%;">
            <div class="col-lg-12 col-md-12 col-sm-12">
                <h2>
				One player is the Mayor<br/><br/><br/>
				The other players are citizens
                </h2>
            </div>
        </div>
        @include('empaville.presentation.carouselRight')
        @include('empaville.presentation.carouselLeft')
    </div>
@endsection
