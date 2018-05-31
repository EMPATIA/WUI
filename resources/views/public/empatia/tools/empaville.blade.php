@extends('public.empatia._layouts.index')

@section('content')

    @php
        $sectionContent = App\Http\Controllers\PublicContentManagerController::getSection("tools-empaville", "tools-empaville-content");
    @endphp

    @if($sectionContent)
        @includeif("public." . ONE::getEntityLayout() . ".cms.sections." . $sectionContent->section_type->code, ['section' => $sectionContent])
    @endif

    @php
        // Getting layout sections
        $layoutSections = App\Http\Controllers\PublicContentManagerController::getSections("tools-empaville");
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
                <span class="fa fa-comment" style="color: #b3b3b3"></span>
                Empatia Tools
            </div>
        </div>
        <br><br>
        <div class="container color-black">
            <h3 class="color-green sub-page-title">
                EMPAVILLE
            </h3>


            <p class="padding-top-35">
                One of the main problem of designing public participation is the risk of implementing untested solutions in the wild.

                EMPAVILLE is a unique do it yourself technology that simulates different use case scenarios of Empatia. It can be used in a variety of ways:
            </p>
            <p class="padding-top-35">
                <span class="color-green">a)</span> teach different models of participation to the public and in schools<br>
                <span class="color-green">b)</span> design new features of an existing model of public participation and test them in a sandbox<br>
                <span class="color-green">c)</span> user test new technologies<br>
            </p>
            <p class="padding-top-35">
                We already implemented EMPAVILLE multiple times in academic conferences, practitioners conferences, planning meetings with city officials and in schools.

            </p>


        </div>
    </section>--}}
@endsection
