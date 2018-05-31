@extends('public.empatia._layouts.index')
@section('content')

    @php
        // Getting layout sections
        $layoutSections = App\Http\Controllers\PublicContentManagerController::getSections("pages-contacts");
    @endphp

    @foreach(!empty($layoutSections) ? $layoutSections : [] as $layoutSection)
        @if($layoutSection)
            @includeif("public." . ONE::getEntityLayout() . ".cms.sections." . $layoutSection->section_type->code, ['section' => $layoutSection])
        @endif
    @endforeach

   {{--
    <section class="background-white padding-top-bottom-35">
        <div class="row menus-row margin-top-15 margin-bottom-35">
            <div class="menus-line col-sm-6 col-sm-offset-3"><span class="fa fa-comment" style="color: #b3b3b3">&nbsp;</span>
                Contacts
            </div>
        </div>

        <div class="container font-size-23">
            <div class="col-md-4 col-xs-12">
                <h3 class="bolder">{{trans('empatiaContacts.mainProjectContact')}}</h3>
                <p>empatia@empatia-project.eu</p>
                <br>
                <h3 class="bolder">Centro de Estudos Sociais</h3>
                <p>Colégio de S.Jerónimo</p>
                <p>Largo D. Dinis</p>
                <p>Apartado 3087</p>
                <p>3000-995 Coimbra, Portugal</p>
                <p>{{trans('home.phone')}}: +351 239 855 570</p>
                <p>Fax: +351 239 855 589</p>
                <br>
                <h3 class="bolder">{{trans('empatiaContacts.project_coordinator')}}</h3>
                <p>Giovanni Allegretti</p>
                <p>Centro de Estudos Sociais (CES)</p>
                <p>giovanni.allegretti@ces.uc.pt</p>
            </div>
            <div class="col-md-4 col-xs-12">
                <h3 class="bolder">{{trans('empatiaContacts.cientific_coordinator')}}</h3>
                <p>Michelangelo Secchi</p>
                <p>Coordenador Científico(CES)</p>
                <p>michelangelo.secchi@ces.uc.pt</p>
                <br>
                <h3 class="bolder">{{trans('empatiaContacts.technical_coordinator')}}</h3>
                <p>Luís Cordeiro</p>
                <p>OneSource (ONE)</p>
                <p>cordeiro@onesource.pt</p>
                <br>
                <h3 class="bolder">{{trans('empatiaContacts.administrative_coordinator')}}</h3>
                <p>André Caiado</p>
                <p>Centro de Estudos Sociais (CES)</p>
                <p>andrecaiado@ces.uc.pt</p>
            </div>
        </div>
    </section>--}}
@endsection
