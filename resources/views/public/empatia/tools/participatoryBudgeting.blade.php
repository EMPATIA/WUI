@extends('public.empatia._layouts.index')

@section('content')

    @php
        // Getting layout sections
        $layoutSections = App\Http\Controllers\PublicContentManagerController::getSections("tools-participatoryBudgeting");
    @endphp

    @foreach(!empty($layoutSections) ? $layoutSections : [] as $layoutSection)
        @if($layoutSection)
            @includeif("public." . ONE::getEntityLayout() . ".cms.sections." . $layoutSection->section_type->code, ['section' => $layoutSection])
        @endif
    @endforeach
    
    <div id="my-list-pilots" class="padding-top-35">
        <div id="my-list-pilots-error" class="hidden">no pilots to display</div>
    </div>

{{--
    <section class="background-white padding-top-bottom-35 min-height-800">
        <div class="row menus-row margin-top-15 margin-bottom-35 ">
            <div class="menus-line col-sm-6 col-sm-offset-3 text-uppercase">
                <span class="fa fa-comment" style="color: #b3b3b3"></span>
                Public Participation
            </div>
        </div>
        <br><br>
        <div class="container color-black margin-bottom-35">
            <h3 class="color-green sub-page-title">
                Participatory Budgeting
            </h3>


            <p class="padding-top-35">
                Participatory budgeting (PB) represents one of the most successful civic innovations of the last
                quarter-century. PB is a family of participatory processes with many variations. In the most extensive
                format PB includes:
            </p>
            <h3 class="padding-top-35">
                <img src="{{asset('images/empatia/layout_v3_esquema.png')}}" class="img-responsive">
            </h3>
            <p class="padding-top-35">
                EMPATIA offers a family of pre-made integrated solutions for the most common participatory budgeting
                processes adopted in Europe, but also includes the possibility of designing completely new solutions
                to better respond to local specificities. All our pilots include a participatory budgeting process so
                stay tuned to see what participedia has achieved in each pilot.
            </p>
            <div id="my-list-pilots" class="padding-top-35">
                <div id="my-list-pilots-error" class="hidden">{!! Html::oneMessageInfo(trans("empatiaHome.no_pilots_to_display") )!!}</div>
            </div>
        </div>
    </section>--}}

@endsection
