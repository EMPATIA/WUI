@extends('public.empatia._layouts.index')

@section('content')
        <div class="container-fluid">
            <div class="row" style='max-height: 300px; position: relative;'>
                <div class="jumboHeader " style="background-image:url({{ asset('/images/empatia/default.jpg') }})">
                    <img src='{{ asset('/images/empatia/default.jpg') }}' style='opacity: 0; margin: auto; width: 100%; max-width: 1280px; max-height: 300px' />
                </div>
            </div>
        </div>
    <section>
        <div class="container-fluid whiteBgnd" style="padding:50px;">
            <div class="row">
                <div class="col-xs-12 col-md-7 text-center">
                    <h4>ASDGFGASDG ASDG ASDG ASDG ASDGASDGDSFHSDBF GDFSG SAAGSDGDSA HASF ASDG SDGASDGSD</h4>
                    <br>
                    <p>
                        agsagdsadgsfiohsdj iopdfs gjioasfjp gjasdiopjaspdogaj sopdg jnopasdjg nmopasdgj
                        asdgiasjdiopsdgjsiopadjg jopsdag jopasdgpj asdojg opasdjgpo asjgosdj da
                        asdgpjioasdjgposd odgsja opasdgjkop asdkgop sdkop asdg
                    </p>
                </div>
                <div class="col-xs-12 col-md-5 text-center row">
                    <div class="col-xs-5 col-xs-offset-1 block-green-small">
                        <h4>Título 1</h4>
                        Conteúdo 1 Conteúdo 1
                    </div>
                    <div class="col-xs-5 col-xs-offset-1 block-green-small">
                        <h4>Título 2</h4>
                        Conteúdo 2 Conteúdo 2
                    </div>
                    <div class="col-xs-5 col-xs-offset-1 block-green-small">
                        <h4>Título 3</h4>
                        Conteúdo 3 Conteúdo 3
                    </div>
                    <div class="col-xs-5 col-xs-offset-1 block-green-small">
                        <h4>Título 4</h4>
                        Conteúdo 4 Conteúdo 4
                    </div>
                </div>
            </div>
        </div>
        <div >
            <img src='{{ (isset($homePageConfig['pilot_banner']) ? url($homePageConfig['pilot_banner']) : null) }}' />
        </div>
        <div class="container-fluid" id="content-container">
            <div class="row menus-row pilotPage-menu">
                <div class="col-sm-6 col-sm-offset-3 menus-line">
                    <i class="fa fa-wrench menusIcon" aria-hidden="true" style="font-size: 3rem; color: #999999"></i>
                    EMPATIA TOOLS
                </div>
            </div>
        </div>
    </section>
    <section>
        <div class="container main-section-container">
            <div class="row row-eq-lg-height box-container-buffer">
                <div class="col-lg-3 col-md-12 col-sm-12 col-xs-12">
                    <i class="fa fa-times fa-5x"></i>
                </div>
                <div class="col-lg-2 col-md-6 col-sm-6 col-xs-12">
                    <i class="fa fa-times fa-5x"></i>
                </div>
                <div class="col-lg-2 col-md-6 col-sm-6 col-xs-12">
                    <i class="fa fa-times fa-5x"></i>
                </div>
                <div class="col-lg-2 col-md-6 col-sm-6 col-xs-12">
                    <i class="fa fa-times fa-5x"></i>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
                    <i class="fa fa-times fa-5x"></i>
                </div>
            </div>
        </div>
    </section>
    <br>
    <section>
        <div class="partnersHidden"></div>
        <div class="partners text-center" style="margin: auto;">
            <a href="#" class="btn btn-goto">
                GO TO
            </a>
        </div>
    </section>
    <section class="whiteBgnd">
        <div class="container-fluid" id="content-container">
            <div class="row menus-row pilotPage-menu">
                <div class="col-sm-6 col-sm-offset-3 menus-line">
                    <i class="fa fa-fa-play-circle-o menusIcon" aria-hidden="true" style="font-size: 3rem; color: #999999"></i>
                    Videos
                </div>
            </div>
        </div>
        <div class="container-fluid" style="padding:50px;">
            <div class="row">
                <div class="col-xs-12 col-md-4 text-center">
                    Vídeo 1
                </div>
                <div class="col-xs-12 col-md-4 text-center">
                    Vídeo 2
                </div>
                <div class="col-xs-12 col-md-4 text-center">
                    Vídeo 3
                </div>
            </div>
        </div>
    </section>
    <section>
        <div class="container-fluid pilotPage-topics" id="content-container">
            <div class="row">
                @if (isset($homePageConfig['pilot_topic_1']) && isset($homePageConfig['pilot_topic_description_1']))
                    <div class="col-md-4 pilotPageTopic">
                        <h2 class="pilotPageTopic-title">{!! $homePageConfig['pilot_topic_1']!!}</h2>
                        <p>{!! $homePageConfig['pilot_topic_description_1'] !!}</p>
                    </div>
                @endif
                @if (isset($homePageConfig['pilot_topic_2']) && isset($homePageConfig['pilot_topic_description_2']))
                    <div class="col-md-4 pilotPageTopic">
                        <h2 class="pilotPageTopic-title">{!! $homePageConfig['pilot_topic_2']!!}</h2>
                        <p>{!! $homePageConfig['pilot_topic_description_2'] !!}</p>
                    </div>
                @endif
                @if (isset($homePageConfig['pilot_topic_3']) && isset($homePageConfig['pilot_topic_description_3']))
                    <div class="col-md-4 pilotPageTopic">
                        <h2 class="pilotPageTopic-title">{!! $homePageConfig['pilot_topic_3']!!}</h2>
                        <p>{!! $homePageConfig['pilot_topic_description_3'] !!}</p>
                    </div>
                @endif
            </div>
        </div>
    </section>
    @include('public.empatia.pages.pilots.pilotContent')
    @include('public.empatia.home.pilots')
@endsection

