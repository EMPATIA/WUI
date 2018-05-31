@extends('public.empatia._layouts.index')

@section('content')

    @php
        // Getting layout sections
        $layoutSections = App\Http\Controllers\PublicContentManagerController::getSections("tools-kiosks");
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
                Kiosks
            </h3>
            <div class="col-md-6 col-xs-12  padding-top-bottom-35">
                <img src="{{ asset('/images/empatia/kiosk.png') }}" style="max-width:100%;">
            </div>
            <div class="col-md-6 col-xs-12">
                <p class="padding-top-35">
                    Empatia has developed a number of solutions to support and integrate face to face participation.
                    The first kiosks we developed are aimed to support voting processes. We are also integrating
                    solution to fast count face to face votes.

                </p>
                <p class="padding-top-35">
                    Empatia is also exploring frugal technologies that can work in situations in which there is limited
                    internet connection and capacity to program.
                    For example one our voting kiosks can be easily “programmed” by simply printing a piece of paper
                    that can be integrated in the voting machine.
                </p>
            </div>
        </div>
    </section>--}}
@endsection
