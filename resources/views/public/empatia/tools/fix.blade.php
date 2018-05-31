@extends('public.empatia._layouts.index')

@section('content')

    @php
        // Getting layout sections
        $layoutSections = App\Http\Controllers\PublicContentManagerController::getSections("tools-fix");
    @endphp

    @foreach(!empty($layoutSections) ? $layoutSections : [] as $layoutSection)
        @if($layoutSection)
            @includeif("public." . ONE::getEntityLayout() . ".cms.sections." . $layoutSection->section_type->code, ['section' => $layoutSection])
        @endif
    @endforeach

    {{--
    <section class="background-white padding-top-bottom-35 min-height-800">
        <div class="row menus-row margin-top-15 margin-bottom-35">
            <div class="menus-line col-sm-6 col-sm-offset-3 text-uppercase">
                <span class="fa fa-comment" style="color: #b3b3b3"></span>
                Empatia Tools
            </div>
        </div>
        <br><br>
        <div class="container color-black">
            <h3 class="color-green sub-page-title">
                Fix!
            </h3>
            <div class="margin-bottom-35">
                <p class="padding-top-35">
                    Fix is the issue reporting tool supported by the Empatia platform. It is a <a href="/page/pages/continuousIdeation" class="color-green">modified pad</a> that includes an advanced upload suite

                </p>

            </div>

        </div>
    </section>--}}
@endsection
