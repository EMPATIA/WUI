@extends('public.empatia._layouts.index')

@section('content')

    @php
        // Getting layout sections
        $layoutSections = App\Http\Controllers\PublicContentManagerController::getSections("pages-publicParticipation");
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
                <span class="typcn typcn-group" style="color: #b3b3b3"></span>
                Public Participation
            </div>
        </div>
        <br><br>
        <div class="container color-black">
            <div class="col-xs-12 col-md-6 margin-bottom-35">
                <p>
                    Among the large variety of participatory processes the Empatia consortium has decided to focus
                    initially on four families of iconic processes that are widely implemented around the world. For
                    each we offer unique and innovative solutions that adapt to a variety of settings and models.
                </p>
            </div>
            <div class="col-xs-12 col-md-6">
                <div class="row">
                    <a href="/page/pages/participatoryBudgeting">
                        <div class="col-md-6 col-xs-12">
                            <span class="tool-border-green">
                                Participatory budgeting
                            </span>
                        </div>
                    </a>
                    <a href="/page/pages/designingParticipation">
                        <div class="col-md-6 col-xs-12">
                            <span class="tool-border-green">
                                Designing participation
                            </span>
                        </div>
                    </a>
                </div>
                <div class="row">
                    <a href="/page/pages/continuousIdeation">
                        <div class="col-md-6 col-xs-12">
                            <span class="tool-border-green">
                                Continuous ideation
                            </span>
                        </div>
                    </a>
                    <a href="/page/pages/integratedVoting">
                        <div class="col-md-6 col-xs-12">
                            <span class="tool-border-green">
                                Integrated voting
                            </span>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </section>--}}
@endsection