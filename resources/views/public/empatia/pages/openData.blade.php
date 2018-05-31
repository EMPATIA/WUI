@extends('public.empatia._layouts.index')

@section('content')

    @php
        // Getting layout sections
        $layoutSections = App\Http\Controllers\PublicContentManagerController::getSections("pages-openData");
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
                Open data
            </h3>
            <p class="padding-top-35">
                Empatia employs Ckan and is partnering with Partecipedia to create a joint repository of data on participatory processes that share the same tagging mechanism.
             </p>
        </div>
    </section>
    --}}
@endsection