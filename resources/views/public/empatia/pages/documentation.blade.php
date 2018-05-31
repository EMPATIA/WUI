@extends('public.empatia._layouts.index')

@section('content')

    @php
        // Getting layout sections
        $layoutSections = App\Http\Controllers\PublicContentManagerController::getSections("pages-documentation");
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
                    Documentation
                </div>
            </div>
            <br><br>
            <!--
            <div style="background-color: #FCFCFC;">
                <div class="container color-black">
                    <h3 class="color-green sub-page-title text-center text-uppercase padding-top-35">
                        Tutorials
                    </h3>
                    <h5 class="padding-top-bottom-35">
                        Comming soon
                    </h5>
                </div>
            </div>
            -->
            <!--
            <div>
                <div class="container color-black">
                    <h3 class="color-green sub-page-title text-center text-uppercase padding-top-35">
                        White Papers
                    </h3>
                    <h5 class="padding-top-bottom-35">
                        Comming soon
                    </h5>
                </div>
            </div>
            -->
            <!--
            <div style="background-color: #FCFCFC;">
                <div class="container color-black">
                    <h3 class="color-green sub-page-title text-center text-uppercase padding-top-35">
                        Papers
                    </h3>
                    <h5 class="padding-top-bottom-35">
                        Comming soon
                    </h5>
                </div>
            </div>
            -->
            <div class="container color-black">
                <h3 class="color-green sub-page-title text-center text-uppercase padding-top-35">
                    Deliverables
                </h3>
                <h5 class="padding-top-bottom-35">
                    <p>Public Deliverable and Reports of EMPATIA</p>

                    <p>
                        To achieve the expected results, the project activities are structured in six Work Packages (WPs),
                        each organized in several tasks. The results of EMPATIA are officially reported in a number of
                        deliverable released during the project advancement. In this page we publish in all the public
                        deliverable of EMPATIA officially accepted by the European Commission.
                        The page will be updated along the lifetime of the EMPATIA project".
                    </p>
                </h5>

                <div class="col-md-3 col-sm-6 col-xs-12">
                    <a target="_blank" href="{!! asset('/generic-files/empatia/20170104_EMPATIA_D1_2.pdf') !!}">
                        <div class="col-md-12 block-border-black">
                            <span class="header">
                                D 1.2 04-01-2017
                            </span>
                            <span class="content">
                                Models, Methodologies, Scenarios & Requirements
                            </span>
                            <span class="icon fa fa-download">&nbsp;</span>
                        </div>
                    </a>
                </div>
                <div class="col-md-3 col-sm-6 col-xs-12">
                    <a target="_blank" href="{!! asset('/generic-files/empatia/EMPATIA_D2.3_v1.0.pdf') !!}">
                        <div class="col-md-12 block-border-black">
                            <span class="header">
                                D2.3: 26-01-2017
                            </span>
                            <span class="content">
                                Platform architecture and specification - final (ONE)
                            </span>
                            <span class="icon fa fa-download">&nbsp;</span>
                        </div>
                    </a>
                </div>
                <div class="col-md-3 col-sm-6 col-xs-12">
                        <div class="col-md-12 block-border-black">
                            <span class="header">
                                D 4.1 26-01-2017
                            </span>
                            <span class="content">
                                Evaluation plans and guidelines (UBRUN)
                            </span>
                            <br>
                            <strong>UNDER REVISON</strong>
                        </div>
                </div>
                <div class="col-md-3 col-sm-6 col-xs-12">
                    <a target="_blank" href="{!! asset('/generic-files/empatia/20160630_EMPATIA_D5.1_v2.0.pdf') !!}">
                        <div class="col-md-12 block-border-black">
                            <span class="header">
                                D 5.1 30-06-2016
                            </span>
                            <span class="content">
                                Electronic media and comunication materials (ONE)
                            </span>
                            <span class="icon fa fa-download">&nbsp;</span>
                        </div>
                    </a>
                </div>
                <div class="col-md-3 col-sm-6 col-xs-12">
                    <a target="_blank" href="{!! asset('/generic-files/empatia/20160628_EMPATIA_D5.2_V2.0.pdf') !!}">
                        <div class="col-md-12 block-border-black">
                            <span class="header">
                                D 5.2 28-06-2016
                            </span>
                            <span class="content">
                                Dissemination plans (CES)
                            </span>
                            <span class="icon fa fa-download">&nbsp;</span>
                        </div>
                    </a>
                </div>
                <div class="col-md-3 col-sm-6 col-xs-12">
                    <a target="_blank" href="{!! asset('/generic-files/empatia/20161216_EMPATIA_D5.3_V0.4_FINAL.pdf') !!}">
                        <div class="col-md-12 block-border-black">
                            <span class="header">
                                D 5.3 16-12-2016
                            </span>
                            <span class="content">
                                Dissemination and exploitation report – preliminary (CES)
                            </span>
                            <span class="icon fa fa-download">&nbsp;</span>
                        </div>
                    </a>
                </div>
            </div>
        </section>--}}
@endsection