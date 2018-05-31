@extends('public.empatia._layouts.index')

@section('content')

    @php
        // Getting layout sections
        $layoutSections = App\Http\Controllers\PublicContentManagerController::getSections("pages-designingParticipation");
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
                Public Participation
            </div>
        </div>
        <br><br>
        <div class="container color-black">
            <h3 class="color-green sub-page-title">
                Integrated voting
            </h3>
            <p class="padding-top-35">
                In many situations a variety of stakeholders and citizens is invited to design together a future
                engagement process, such as a participatory budgeting, or an ideation platform. In most cases these
                planning moment do not have the capacity to trial and test the results of the design process. Empatia
                offers a unique combination of an advanced design tool that allows to build new participatory processes
                in an intuitive way and Empaville a unique testing platform based on a roleplaying game that simulates
                persons that would be using the newly invented participatory process.
            </p>
        </div>
    </section>--}}
@endsection