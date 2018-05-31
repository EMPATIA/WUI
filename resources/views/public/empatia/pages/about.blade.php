@extends('public.empatia._layouts.index')

@section('content')

    @php
        // Getting layout sections
        $layoutSections = App\Http\Controllers\PublicContentManagerController::getSections("pages-about");
    @endphp

    @foreach(!empty($layoutSections) ? $layoutSections : [] as $layoutSection)
        @if($layoutSection)
            @includeif("public." . ONE::getEntityLayout() . ".cms.sections." . $layoutSection->section_type->code, ['section' => $layoutSection])
        @endif
    @endforeach


    {{--
        <section class="background-white padding-top-bottom-35">
        <div class="row menus-row margin-top-15 margin-bottom-35">
            <div class="menus-line col-sm-6 col-sm-offset-3"><span class="fa fa-tasks" style="color: #b3b3b3">&nbsp;</span>
                About
            </div>
        </div>

        <div class="container color-black"><h3 class="title-about">What is EMPATIA <b>?</b></h3>
            <p>Empatia is the first hands-on digital platform for creating and managing a coherent participatory system
                that
                integrates multiple channel of engagement in one simple solution.</p>
            <br>
            <p>This integration simplifies the management of existing processes and allows innovative solutions that
                were
                not imaginable before.</p>
            <br>

            <h3 class="title-about"> How we do it <b>?</b></h3>
            <p> Empatia mission is to promote inclusion, higher quality deliberation, better voting mechanisms,
                transparency
                and accountability. A key element of our mission is to balance transparency and user data protection.
                Empatia promotes open data and advanced solutions for data consent form. Empatia is particularly
                interested
                in offering solutions for those that have limited alternatives and funds. Empatia is a free and open
                source
                software for shaping the democracy of the XXI century.
            </p>
        </div>
        <div class="container-fluid gradient-our-view margin-top-35 margin-bottom-35">
            <div class="row menus-row margin-top-15 margin-bottom-35">
                <div class="menus-line col-sm-6 col-sm-offset-3 color-white"><span class="fa fa-tasks">&nbsp;</span> Our view
                </div>
            </div>
            <div class="container color-white">
                <div class="col-md-12 margin-bottom-65">
                    <div class="col-md-6 col-xs-12">
                        <div class="col-md-3 col-xs-12">
                            <img src="{{ asset('/images/empatia/easy.png') }}">
                        </div>
                        <div class="col-md-9 col-xs-12">
                            <h3 class="about-h3-title bolder">easy</h3>
                            <p class="font-size-16">We offer a do-it-yourself platform to imagine, build, and manage multiple channels of
                                participation.
                                Empatia does not require technical skills. It comes with pre-loaded templates,
                                and the freedom to create entirely new processes.</p>
                        </div>
                    </div>
                    <div class="col-md-6 col-xs-12">
                        <div class="col-md-3 col-xs-12">
                            <img src="{{ asset('/images/empatia/efficient.png') }}">
                        </div>
                        <div class="col-md-9 col-xs-12">
                            <h3 class="about-h3-title bolder">efficient</h3>
                            <p class="font-size-16">Empatia was designed to reduce the workload of city staff managing participatory
                                processes.
                                It directly integrates a variety of city information systems and it offers a number of
                                solutions to manage participation more efficiently.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 margin-bottom-65">
                    <div class="col-md-6 col-xs-12">
                        <div class="col-md-3 col-xs-12">
                            <img src="{{ asset('/images/empatia/flexible.png') }}">
                        </div>
                        <div class="col-md-9 col-xs-12">
                            <h3 class="about-h3-title bolder">flexible</h3>
                            <p class="font-size-16">Empatia can be tailored to any situation, from small villages to big capital cities. Its
                                unique level of customization offers an agile integration mechanism.
                                Empatia adapts to local necessities and opens a new array of possibilities.</p>
                        </div>
                    </div>
                    <div class="col-md-6 col-xs-12">
                        <div class="col-md-3 col-xs-12">
                            <img src="{{ asset('/images/empatia/compatible.png') }}">
                        </div>
                        <div class="col-md-9 col-xs-12">
                            <h3 class="about-h3-title bolder">compatible</h3>
                            <p class="font-size-16">EMPATIA is designed to integrate third party software. Some of the already integrated
                                platforms are
                                CKAN, Crowdrise, Limesurvey, Drupal, Wordpress and many others.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 margin-bottom-65">
                    <div class="col-md-6 col-xs-12">
                        <div class="col-md-3 col-xs-12">
                            <img src="{{ asset('/images/empatia/open_and_safe.png') }}">
                        </div>
                        <div class="col-md-9 col-xs-12">
                            <h3 class="about-h3-title bolder">open & safe</h3>
                            <p class="font-size-16">Empatia is designed to make Open Data accessible and protect the privacy of citizens.
                                Empatia includes a triple approach to informed consent that includes:
                                explanatory videos; summarized terms of service; full licenses and right to be
                                forgotten.</p>
                        </div>
                    </div>
                    <div class="col-md-6 col-xs-12">
                        <div class="col-md-3 col-xs-12">
                            <img src="{{ asset('/images/empatia/try_our_game.png') }}">
                        </div>
                        <div class="col-md-9 col-xs-12">
                            <h3 class="about-h3-title bolder">try our game</h3>
                            <p class="font-size-16">Empatia has a unique testing platform: EMPAVILLE. EMPAVILLE is a role playing game that
                                allows to experiment,
                                plan, and adapt different participatory systems.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="container color-black margin-bottom-35">
            <h3 class="title-about"> Who we are <b>?</b></h3>
            <p> Empatia consortium leverages more than 20 years of research and practice on face-to-face and digital
                participation.
                Our core development team is supported by an international board that involves, among many others,
                representatives of Participedia,
                the largest global community of scholars of participation, and IODP, the largest network of cities
                implementing participatory processes.
                Our solutions are developed via a unique participatory process that involves administrators, academics,
                citizens of all ages and experienced practitioners.
            </p>
        </div>


    </section>--}}
    @include('public.empatia.home.partners')

@endsection
