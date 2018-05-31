@extends('public.empatia._layouts.index')

@section('content')

    @php
        // Getting layout sections
        $layoutSections = App\Http\Controllers\PublicContentManagerController::getSections("pages-integratedVoting");
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
                Designing participation
            </h3>
            <p class="padding-top-35">
                The Empatia consortium has developed an integrated voting modules that combines tools for remote voting
                and tools to improve in person voting. Our integrated system supports a variety of voting mechanisms
                that include ranking, multi-voting, negative voting, and also a gamified voting process in which
                participants are called to allocate a fix amount of virtual coins.
            </p>
        </div>
    </section> --}}
@endsection