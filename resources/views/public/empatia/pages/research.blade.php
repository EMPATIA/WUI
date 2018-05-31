@extends('public.empatia._layouts.index')

@section('content')

    @php
        // Getting layout sections
        $layoutSections = App\Http\Controllers\PublicContentManagerController::getSections("pages-research");
    @endphp

    @foreach(!empty($layoutSections) ? $layoutSections : [] as $layoutSection)
        @if($layoutSection)
            @includeif("public." . ONE::getEntityLayout() . ".cms.sections." . $layoutSection->section_type->code, ['section' => $layoutSection])
        @endif
    @endforeach

    {{--
    <section class="background-white padding-top-bottom-35">
        <div class="row menus-row margin-top-15 margin-bottom-35">
            <div class="menus-line col-sm-6 col-sm-offset-3 text-uppercase">
                <span class="fa fa-comment" style="color: #b3b3b3">&nbsp;</span>
                Smart City
            </div>
        </div>
        <br><br>
        <div class="container color-black">
            <h3 class="color-green sub-page-title">
                Research
            </h3>
            <p class="padding-top-35">
                Research is at the core of Empatia.
            </p>

            <p class="padding-top-35">
                Empatia consortium leverages more than 20 years of research and practice on face-to-face and digital
                participation.
                Our core development team is supported by an international board that involves, among many others,
                representatives of <a href="https://www.participedia.net/" class="color-green">Participedia</a>,
                the largest global community of scholars of participation, and IODP, the largest network of cities
                implementing participatory processes.

            </p>

            <div class="row padding-top-35">
                <div class="col-md-12 col-xs-12">
                    <div class="research-block">
                        <h3>trigger survey</h3>
                        <p>Most of our research over the years has shown that short recurrent surveys are superiors data
                            gathering
                            mechanism with respect long questionnaires. For such reason we have developed trigger
                            survey,
                            a tool that allows to embed a mini-survey to any action performed by a user inside the
                            Empatia platform.
                        </p>
                    </div>
                </div>
                <div class="col-md-12 col-xs-12">
                    <div class="research-block">
                        <h3>questionnaires</h3>
                        <p>At times researchers or cities need a quick and easy to deploy feedback questionnaire this
                            tool allows to build simple questionnaires, such as feedback survey after a vote.</p>
                    </div>
                </div>
                <div class="col-md-12 col-xs-12">
                    <div class="research-block">
                        <h3>calendar</h3>
                        <p>The calendar tool is a simple tool that allows to schedule meetings</p>
                    </div>
                </div>
                <div class="col-md-12 col-xs-12">
                    <div class="research-block">
                        <h3>Lime-survey</h3>
                        <p><a href="https://www.limesurvey.org/" class="color-green">Lime survey</a> is one of the most comprehensive open source survey platform for academic
                            research</p>
                    </div>
                </div>
            </div>
        </div>
    </section>--}}
@endsection
