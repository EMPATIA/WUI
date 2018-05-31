@extends('empaville.presentation.index')

@section('content')
    <div class="content-header">
        <div class="container" style="padding-top: 0px;text-align: center;border-bottom-color: gray;border-bottom-style: solid;">
            <h2>winners</h2>
        </div>
    </div>

    <div class="content">
        <div class="container">
            @include('empaville.presentation.OLD.totals')
        </div>
        @include('empaville.presentation.carouselRight')
        @include('empaville.presentation.carouselLeft')
    </div>

@endsection