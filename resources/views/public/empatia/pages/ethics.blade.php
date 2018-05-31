@extends('public.empatia._layouts.index')

@section('content')

    @php
        // Getting layout sections
        $layoutSections = App\Http\Controllers\PublicContentManagerController::getSections("pages-ethics");
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
                <span class="fa fa-compass" style="color: #b3b3b3">&nbsp;</span>
                Ethics
            </div>
        </div>
        <br><br>
        <div class="container color-black">
            <div class="col-xs-12">
                <p class="padding-bottom-35">
                    EMPATIA is committed to enforce the following ethical principles:
                </p>
            </div>
            <br>
            <div class="col-xs-12">
                <div class="row">
                    <div class="col-md-4 col-sm-6 col-xs-12 ">
                        --}}{{--<a href="#">--}}{{--
                        --}}{{--<a href="/page/pages/socialInclusion">--}}{{--
                            <span class="tool-border-green noHover new-padding">
                                <h4 class="text-uppercase">Social Inclusion</h4>
                                <p class="ethicsBox-description">
                                    We aim to give voice to those that have no voice. All our technology is designed
                                    with such principle in mind.
                                </p>
                            </span>
                        --}}{{--</a>--}}{{--
                    </div>
                    <div class="col-md-4 col-sm-6 col-xs-12">
                        --}}{{--<a href="#">--}}{{--
                        --}}{{--<a href="/page/pages/personalDataProtection">--}}{{--
                            <span class="tool-border-green noHover new-padding">
                                <h4 class="text-uppercase">Personal Data Protection</h4>
                                <p class="ethicsBox-description">
                                    We aim to increase users’ awareness on the value of their personal data and the
                                    dangers and opportunities of sharing them in different ways.
                                </p>
                            </span>--}}{{--
                        </a>--}}{{--
                    </div>
                    <div class="col-md-4 col-sm-6 col-xs-12">
                        --}}{{--<a href="#">--}}{{--
                        --}}{{--<a href="/page/pages/transparencyForAccountability">--}}{{--
                            <span class="tool-border-green noHover new-padding">
                                <h4 class="text-uppercase">Transparency for accountability</h4>
                                <p class="ethicsBox-description">
                                    We strive to promote transparency that is designed to support accountability
                                    and not release mountains of useless data.
                                </p>
                            </span>
                        --}}{{--</a>--}}{{--
                    </div>

                    <div class="col-md-4 col-sm-6 col-xs-12">
                        --}}{{--<a href="#">--}}{{--
                        --}}{{--<a href="/page/pages/commons">--}}{{--
                            <span class="tool-border-green noHover new-padding">
                                <h4 class="text-uppercase">Commons</h4>
                                <p class="ethicsBox-description">
                                    The Site and the Service are conceived in the first instance as a “non-exclusive”
                                    and “non-appropriable” tool.
                                </p>
                            </span>
                        --}}{{--</a>--}}{{--
                    </div>
                    <div class="col-md-4 col-sm-6 col-xs-12">
                        --}}{{--<a href="#">--}}{{--
                        --}}{{--<a href="/page/pages/ourTransparency">--}}{{--
                            <span class="tool-border-green noHover new-padding">
                                <h4 class="text-uppercase">Our transparency</h4>
                                <p class="ethicsBox-description">
                                    We received funds from the EU, all our pilots are free.
                                </p>
                            </span>
                        --}}{{--</a>--}}{{--
                    </div>
                    <div class="col-md-4 col-sm-6 col-xs-12">
                        --}}{{--<a href="#">--}}{{--
                        --}}{{--<a href="/page/pages/protectUsers">--}}{{--
                            <span class="tool-border-green noHover new-padding">
                                <h4 class="text-uppercase">Protect Users</h4>
                                <p class="ethicsBox-description">
                                    EMPATIA has introduced the role of Ombudsperson, an external and neutral person
                                    who collects and investigates user complaints.
                                </p>
                            </span>
                        --}}{{--</a>--}}{{--
                    </div>
                </div>
            </div>
        </div>
    </section>--}}
@endsection