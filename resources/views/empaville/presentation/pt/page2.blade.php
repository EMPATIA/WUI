@extends('empaville.presentation.index')

@section('content')
    <div class="content-header">
        <div class="container" style="padding-top: 0px;text-align: center;border-bottom-color: gray;border-bottom-style: solid;">
            <h2>Regras de Votação</h2>
        </div>
    </div>
    <div class="content" style="padding-top: 0%;padding-bottom: 5%">
        <div class="container">
            <div class="col-lg-12 col-md-12 col-sm-12">
                <h2>
                    <ul style="color: #66A2D8">
                        <li style='padding-top: 1em'><span style="color: black">Cada pessoa pode votar até 3 votos</span></li>
                        <li style='padding-top: 1em'><span style="color: black">Máximo de 1 voto por proposta</span></li>
                        <li style='padding-top: 1em'><span style="color: black">Máximo de 1 voto negativo</span></li>
                    </ul>
                </h2>
            </div>
            <div class="col-lg-12 col-md-12 col-sm-12" id="box2">
                <img src="{{ asset('images/EmpavilleGame_id_card_table.jpg') }}"  />
            </div>
        </div>
        <div style="float: right">
            <a class="right carousel-control" style="width: 5%" id="rightBtn" onclick="location.href='{{ action('EmpavillePresentationController@showProposal',['cbKey'=> $cbKey, 'id'=>($id+1),'count' => '1','lang' => $lang]) }}'" role="button">

                <span class="glyphicon glyphicon-chevron-right" aria-hidden="true" style="color: black"></span>

                <span class="sr-only">Próximo</span>
            </a>
        </div>
        @include('empaville.presentation.carouselLeft')
    </div>

@endsection
@section('scripts')

@endsection