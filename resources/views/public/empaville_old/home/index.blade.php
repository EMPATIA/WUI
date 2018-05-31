@extends('public.empaville._layouts.index')

@section('content')
    <div class="container-fluid carouselContainer hidden-xs">
        <div id="top-carousel" class="carousel slide" data-ride="carousel" style="">
            <ol id="indicators-top-carousel" class="carousel-indicators"
                style="">
                <li data-target="#top-carousel" data-slide-to="0" class="active"></li>
                <li data-target="#top-carousel" data-slide-to="1" class=""></li>
            </ol>
            <div class="carousel-inner" role="listbox" style="">
                <div class="item active" style="">
                    <div class="carouselItem">
                        <img src="{{ asset('images/home/empaville-banner-1.png') }}">  </img>
                    </div>
                </div>
                <div class="item " style="">
                    <div class="carouselItem">
                        <img src="{{ asset('images/home/empaville-banner-2_ok.jpg') }}">  </img>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid content-container">
        <div class="row">
            <div class="col-md-12">
                <div style="">
                    <div class="empavilleDescription-container" style="">
                        <h1 style="color: #8dc73d;  font-size: 28px; font-size: 1.75em; font-weight: 700; margin-bottom: 15px; margin-top: 10px;  text-transform: uppercase;">EMPAVILLE</h1>

                        <hr style="margin: 10px 0px; color: #cccccc">
                        <div class="row">
                            <div class="col-md-12 empavilleDescription">
                                {{trans("empavilleHome.firstParagraph")}}
                                <br>
                                <br>
                                {{trans("empavilleHome.secondParagraph")}}
                                <ul>
                                    <li>
                                        {{trans("empavilleHome.firstTextInList")}}
                                    </li>
                                    <li>
                                        {{trans("empavilleHome.secondTextInList")}}
                                    </li>
                                    <li>
                                        {{trans("empavilleHome.thirdTextInList")}}
                                    </li>
                                </ul>

                                {{trans("empavilleHome.thirdParagraph")}}
                                <br>

                            </div>
                            <div class="col-md-12 empavilleDescription">
                                <b>{{trans("empavilleHome.tools")}}:</b>
                                <br>
                                {{trans("empavilleHome.qrCodeScanner")}}:
                                [ <a href="https://play.google.com/store/apps/details?id=com.google.zxing.client.android" target="_blank">Android</a> ]
                                [ <a href="https://itunes.apple.com/us/app/qr-code-reader-and-scanner/id388175979?mt=8" target="_blank">Apple</a> ]
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row newsEvents-container">
            <div class="col-md-6">
                <div class="lastNews-container">
                    @include('public.empaville.home.lastNews')
                </div>
            </div>
            <div class="col-md-6" >
                <div class="nextEvents-container">
                    @include('public.empaville.home.nextEvents')
                </div>
            </div>
        </div>
    </div>
    {{--<div class="row">--}}
    {{--<div class="col-md-12">--}}
    {{--<div style="background-color: white; padding: 10px;">--}}


    {{--<div style="background-color: white; padding: 10px;">--}}
    {{--<h1 style="color: #8dc73d;  font-size: 28px; font-size: 1.75em; font-weight: 700; margin-bottom: 15px; margin-top: 10px;  text-transform: uppercase;">empatia</h1>--}}

    {{--<hr style="margin: 10px 0px; color: #cccccc">--}}
    {{--<div class="row">--}}
    {{--<div class="col-md-12">--}}
    {{--<h3>What</h3>--}}
    {{--<span>--}}
    {{--<strong>EMPATIA</strong> is a 24-month project that seeks to design, evaluate, refine and widely disseminate an ICT platform for--}}
    {{--participatory budgeting as an open source commons.--}}
    {{--</span>--}}
    {{--<h43>Vision</h43>--}}
    {{--<span>--}}
    {{--Radically enhance the inclusiveness and impact of PB--}}
    {{--processes, increasing the participation of citizens.--}}
    {{--</span>--}}
    {{--<h43>Mission</h43>--}}
    {{--<span>--}}
    {{--By designing, evaluating and making publically available an--}}
    {{--advanced ICT platform for participatory budgeting!--}}
    {{--</span>--}}
    {{--<h3>Innovations</h3>--}}
    {{--<span>--}}
    {{--The platform aims to be flexible and adaptable enough to--}}
    {{--answer to diverse demands. It combines the development--}}
    {{--of technical and conceptual solutions for the different--}}
    {{--phases of PB process, including proposal development,--}}
    {{--citizen’s interaction, voting, implementation, monitoring--}}
    {{--and advanced data analysis.--}}
    {{--</span>--}}
    {{--<h3>Goals</h3>--}}
    {{--<ul>--}}
    {{--<li>--}}
    {{--<strong>Inclusion</strong> - Reduction of any type barriers to--}}
    {{--citizen participation (digital skills,--}}
    {{--language, education level, etc).--}}
    {{--</li>--}}
    {{--<li>--}}
    {{--<strong>Deliberative quality</strong> ---}}
    {{--Enhance the quality of deliberation--}}
    {{--within a better management of the--}}
    {{--information flow, enabling the--}}
    {{--exchange of alternative proposals--}}
    {{--and using advanced algorithms--}}
    {{--system.--}}
    {{--</li>--}}
    {{--<li>--}}
    {{--<strong>Efficiency</strong> ---}}
    {{--Optimizing the investment of time--}}
    {{--and resources by facilitators and--}}
    {{--technical staff.--}}
    {{--</li>--}}
    {{--<li>--}}
    {{--<strong>Accountability</strong> ---}}
    {{--Improving a better coordination--}}
    {{--between government and citizens at--}}
    {{--all stages of the PB process,--}}
    {{--especially during the implementation--}}
    {{--cycle, an utmost of PB practices.--}}
    {{--</li>--}}
    {{--<li>--}}
    {{--<strong>Integration</strong> ---}}
    {{--Combine multichannel forms--}}
    {{--online and in–person, including--}}
    {{--open data and existing--}}
    {{--e-government tools.--}}
    {{--</li>--}}
    {{--<li>--}}
    {{--<strong>Replication & adaptation</strong> ---}}
    {{--Making all the components of the--}}
    {{--platform possible to be used and--}}
    {{--adapt in other social and--}}
    {{--institutional contexts for the--}}
    {{--improving of a better deliberation,--}}
    {{--implementation and selection--}}
    {{--mechanism.--}}
    {{--</li>--}}
    {{--<li>--}}
    {{--<strong>Enhanced evaluation</strong> ---}}
    {{--Allow involved communities to--}}
    {{--self-acess the impact of own--}}
    {{--previous deliberations and to learn--}}
    {{--from the experience of other--}}
    {{--communities.--}}
    {{--</li>--}}
    {{--<li>--}}
    {{--<strong>Marketability</strong> ---}}
    {{--Exploration of consistent business--}}
    {{--models to accelerate and amplify--}}
    {{--these innovations.--}}
    {{--</li>--}}
    {{--</ul>--}}
    {{--</div>--}}
    {{--</div>--}}
    {{--</div>--}}
    {{--</div>--}}
    {{--</div>--}}
    {{--</div>--}}



    {{--<div id="partners" class="partners"--}}
    {{--style="background-color: #8cc63e; background-image: linear-gradient(to right, #8cc63e, #3ab54a);">--}}
    {{--<div class="container-fluid partners_container"--}}
    {{--style="max-width: 1280px;margin: auto;padding-top: 3%;padding-bottom: 3%;height: 10%;">--}}
    {{--<div class="row">--}}
    {{--<div class="col-xs-12 partnersTitle"--}}
    {{--style="color: #FFFFFF;font-size: 28px; font-size: 1.75em; font-weight: 700;text-transform: uppercase;padding-bottom: 2%;padding-left: 20px;">PARTNERS--}}
    {{--</div>--}}

    {{--</div>--}}
    {{--<div class="row">--}}
    {{--<div style="width: 14.2%;" class="col-xs-1">--}}
    {{--<div class="partner_container" onclick="window.open('http://www.ces.uc.pt', '_blank');"--}}
    {{--style="position: relative; width: 100%; border-radius: 50%; margin-bottom: 10px; cursor: pointer; z-index: 10;">--}}
    {{--<img title="CES" alt="CES logo" src="{{ asset('images/home/partners/logo_CES.jpg')}}"--}}
    {{--class="partner_img" style="opacity: 1; top: 0px; border-radius: 50%;">--}}
    {{--</div>--}}
    {{--</div>--}}
    {{--<div style="width: 14.2%;" class="col-xs-1">--}}
    {{--<div class="partner_container" onclick="window.open('https://www.onesource.pt/', '_blank');"--}}
    {{--style="position: relative; width: 100%; border-radius: 50%; margin-bottom: 10px; cursor: pointer; z-index: 10;">--}}
    {{--<img title="OneSource" alt="OneSource logo"--}}
    {{--src="{{ asset('images/home/partners/onesource.png')}}" class="partner_img"--}}
    {{--style="opacity: 1; top: 0px; border-radius: 50%;">--}}
    {{--</div>--}}
    {{--</div>--}}
    {{--<div style="width: 14.2%;" class="col-xs-1">--}}
    {{--<div class="partner_container" onclick="window.open('https://www.d21.me/', '_blank');"--}}
    {{--style="position: relative; width: 100%; border-radius: 50%; margin-bottom: 10px; cursor: pointer; z-index: 10;">--}}
    {{--<img title="D21" alt="D21 logo" src="{{ asset('images/home/partners/d21.png')}}"--}}
    {{--class="partner_img" style="opacity: 1; top: 0px; border-radius: 50%;">--}}
    {{--</div>--}}
    {{--</div>--}}
    {{--<div style="width: 14.2%;" class="col-xs-1">--}}
    {{--<div class="partner_container" onclick="window.open('http://www.brunel.ac.uk/', '_blank');"--}}
    {{--style="position: relative; width: 100%; border-radius: 50%; margin-bottom: 10px; cursor: pointer; z-index: 10;">--}}
    {{--<img title="UBRUN" alt="UBRUN logo" src="{{ asset('images/home/partners//brunel.jpg')}}"--}}
    {{--class="partner_img" style="opacity: 1; top: 0px; border-radius: 50%;">--}}
    {{--</div>--}}
    {{--</div>--}}
    {{--<div style="width: 14.2%;" class="col-xs-1">--}}
    {{--<div class="partner_container" onclick="window.open('http://www.unimi.it/', '_blank');"--}}
    {{--style="position: relative; width: 100%; border-radius: 50%; margin-bottom: 10px; cursor: pointer; z-index: 10;">--}}
    {{--<img title="UNIMI" alt="UNIMI logo"--}}
    {{--src="{{ asset('images/home/partners/universitaInterno.png')}}" class="partner_img"--}}
    {{--style="opacity: 1; top: 0px; border-radius: 50%;">--}}
    {{--</div>--}}
    {{--</div>--}}
    {{--<div style="width: 14.2%;" class="col-xs-1">--}}
    {{--<div class="partner_container" onclick="window.open('http://www.zebralog.de/', '_blank');"--}}
    {{--style="position: relative; width: 100%; border-radius: 50%; margin-bottom: 10px; cursor: pointer; z-index: 10;">--}}
    {{--<img title="Zebralog" alt="Zebralog logo" src="{{ asset('images/home/partners/zebralog.png')}}"--}}
    {{--class="partner_img" style="opacity: 1; top: 0px; border-radius: 50%;">--}}
    {{--</div>--}}
    {{--</div>--}}
    {{--<div style="width: 14.2%;" class="col-xs-1">--}}
    {{--<div class="partner_container" onclick="window.open('http://www.in-loco.pt/', '_blank');"--}}
    {{--style="position: relative; width: 100%; border-radius: 50%; margin-bottom: 10px; cursor: pointer; z-index: 10;">--}}
    {{--<img title="In Loco" alt="In Loco logo" src="{{ asset('images/home/partners/loco.png')}}"--}}
    {{--class="partner_img" style="opacity: 1; top: 0px; border-radius: 50%;">--}}
    {{--</div>--}}
    {{--</div>--}}
    {{--</div>--}}
    {{--</div>--}}
    {{--</div>--}}
    {{--<div class="row">--}}

    {{--<div class="col-md-12">--}}

    {{--<div style="background-color: #f4f4f4; padding: 10px;">--}}


    {{--<div style="background-color: #f4f4f4; padding: 10px;">--}}
    {{--<h1 style="color: #8dc73d;  font-size: 28px; font-size: 1.75em; font-weight: 700; margin-bottom: 15px; margin-top: 10px; text-transform: uppercase;">Contacts</h1>--}}

    {{--<hr style="margin: 10px 0px; color: #cccccc">--}}
    {{--<div class="row">--}}
    {{--<div class="col-sm-6 col-md-3">--}}

    {{--<span style="font-weight: bold;text-transform: uppercase;">Main project contact</span><br><br>--}}

    {{--empatia@empatia-project.eu<br><br>--}}

    {{--Centro de Estudos Sociais<br>--}}
    {{--Colégio de S. Jerónimo<br>--}}
    {{--Largo D. Dinis<br>--}}
    {{--Apartado 3087<br>--}}
    {{--3000-995 Coimbra, Portugal<br>--}}

    {{--Phone: +351 239 855 570<br>--}}
    {{--Fax: +351 239 855 589<br><br><br>--}}
    {{--</div>--}}

    {{--<div class="col-sm-6 col-md-3">--}}
    {{--<span style="font-weight: bold;text-transform: uppercase;">Project Coordinator</span><br><br>--}}

    {{--Giovanni Allegretti<br>--}}
    {{--Center for Social Studies (CES)<br>--}}
    {{--giovanni.allegretti@ces.uc.pt<br><br><br>--}}


    {{--<span style="font-weight: bold;text-transform: uppercase;">Scientific Coordinator</span><br><br>--}}

    {{--Michelangelo Secchi<br>--}}
    {{--Center for Social Studies (CES)<br>--}}
    {{--michelangelo.secchi@ces.uc.pt<br><br><br>--}}
    {{--</div>--}}

    {{--<div class="col-sm-6 col-md-3">--}}
    {{--<span style="font-weight: bold;text-transform: uppercase;">Tecnical Coordinator</span><br><br>--}}

    {{--Luís Cordeiro<br>--}}
    {{--OneSource (ONE)<br>--}}
    {{--cordeiro@onesource.pt<br><br><br>--}}

    {{--<span style="font-weight: bold;text-transform: uppercase;">Ethical Coordinator</span><br><br>--}}

    {{--Giovanni Allegretti<br>--}}
    {{--Center for Social Studies (CES)<br>--}}
    {{--giovanni.allegretti@ces.uc.pt<br><br><br>--}}
    {{--</div>--}}

    {{--<div class="col-sm-6 col-md-3">--}}

    {{--<span style="font-weight: bold;text-transform: uppercase;">Administrative Coordinator</span><br><br>--}}

    {{--André Caiado<br>--}}
    {{--Center for Social Studies (CES)<br>--}}
    {{--andrecaiado@ces.uc.pt<br>--}}

    {{--</div>--}}


    {{--</div>--}}
    {{--</div>--}}


    {{--</div>--}}
    {{--</div>--}}
    {{--</div>--}}




@endsection

@section('scripts')
    <script>


    </script>
@endsection
