@extends('public.empatia._layouts.index')

@section('content')

    @php
        // Getting layout sections
        $layoutSections = App\Http\Controllers\PublicContentManagerController::getSections("pages-protectUsers");
    @endphp

    @foreach(!empty($layoutSections) ? $layoutSections : [] as $layoutSection)
        @if($layoutSection)
            @includeif("public." . ONE::getEntityLayout() . ".cms.sections." . $layoutSection->section_type->code, ['section' => $layoutSection])
        @endif
    @endforeach


    {{--<section class="background-white padding-top-bottom-35">
        <div class="row menus-row margin-top-15 margin-bottom-35">
            <div class="menus-line col-sm-6 col-sm-offset-3 text-uppercase">
                <span class="fa fa-comment" style="color: #b3b3b3">&nbsp;</span>
                Ethics
            </div>
        </div>
        <br><br>
        <div class="container color-black">
            <h3 class="color-green">
                Our strategy to protect users
            </h3>
            <h3 class="padding-top-bottom-35">
                The Ombudsperson of EMPATIA has the purpose to collect and investigate complaints related to misuse of
                the Service or infringements of the Terms of Service and/or the Privacy Policy and/or the IP policy of
                the Service: these three policies represent the main scope of the Ombudsperson remit.
            </h3>
            <h3 class="padding-top-bottom-35">
                The Ombudsperson activity is of an informative character in relation to the matters of her/his
                competence, which means it does not have an executive or managerial character. The competences of the
                Ombudsperson are:
                <ul>
                    <li>
                        To ensure the enforcement of the right to access personal data and the right to withdraw as
                        defined in the Privacy Policy of the Service
                    </li>
                    <li>
                        To monitor on the enforcement of the Terms of Service and/or the Privacy Policy and/or the
                        IP policy of the Service
                    </li>
                    <li>
                        To assist users in exercising their rights and in complying with their duties
                    </li>
                    <li>
                        To hear and receive users’ claims, complaints or suggestions, assessing them and directing the
                        recommendations or suggestions
                    </li>
                    <li>
                        To answer to the requests and complaints received by providing consultancy, evaluation
                    </li>
                    <li>
                        Mediation and conciliation
                    </li>
                    <li>
                        Investigation and determining of complaints
                    </li>
                    <li>
                        Recommendations
                    </li>
                </ul>
            </h3>
            <h3 class="padding-top-bottom-35">
                The Ombudsperson can reject a request when complaints are detrimental to the legitimate rights of
                others, when there is an ongoing judicial or administrative on the subject of the claim or when the
                facts described have occurred for over a year; are insufficiently substantiated or are clearly
                irrelevant; when the content of the complaints do not match with the scope of the Ombudsperson.
            </h3>
        </div>
    </section>--}}
@endsection