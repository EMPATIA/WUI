@extends('empaville.presentation.index')

@section('content')
    <div class="content-header" >
        <div class="container" style="padding-top: 0px;text-align: center;border-bottom-color: gray;border-bottom-style: solid;">
            <h2>Questionnaire</h2>
        </div>
    </div>
    <div class="content">
        <div class="container" style="height: 100%;padding-top: 15%;">
            <div class="col-lg-12 col-md-12 col-sm-12">
                <h2>
                    <ul style="color: #66A2D8">
                        <li style='margin-top: 20px'><span style="color: black">Your feedback will help us to make adjustments to the design of
                                participatory budgeting in order to improve and expand its accessibility to more citizens!</span></li>
                    </ul>
                </h2>
            </div>
        </div>
        @include('empaville.presentation.carouselRight')
        @include('empaville.presentation.carouselLeft')
    </div>

@endsection
@section('scripts')

@endsection