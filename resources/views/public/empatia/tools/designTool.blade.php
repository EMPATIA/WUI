@extends('public.empatia._layouts.index')

@section('content')

    @php
        // Getting layout sections
        $layoutSections = App\Http\Controllers\PublicContentManagerController::getSections("tools-designTool");
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
                Design tool
            </h3>


            <div class="padding-top-35 ">
            <p>
                The Empatia platform contains an easy design tool that allows practitioners to construct the public participation model they desire simply dragging and dropping tools and connecting them.

                This tool complements <a href="https://empaville.org/" class="color-green">Empaville</a> our gamified user testing platform, to create a unique participatory design model.


            </p>

            </div>
            <div class="padding-top-35 ">
                <img src="{{ asset('/images/empatia/flow.png') }}" style="max-width:100%;">
            </div>
        </div>
    </section>
    --}}
@endsection
