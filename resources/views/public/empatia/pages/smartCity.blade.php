@extends('public.empatia._layouts.index')

@section('content')

    @php
        // Getting layout sections
        $layoutSections = App\Http\Controllers\PublicContentManagerController::getSections("pages-smartCity");
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
            <div class="row">
                <div class="col-xs-12 col-md-6 margin-bottom-35">
                    <p>
                        Empatia offers a preliminary array of solution to interact directly with the community
                        and the city infrastructure and to monitor what is happening in the city
                    </p>
                </div>
                <div class="col-xs-12 col-md-6">
                    <div class="row">
                        <!-- <a href="#"> -->
                            <div class="col-md-6 col-xs-12">
                                <span class="tool-border-green"  style="display:table;">
                                    Issue reporting software
                                </span>
                            </div>
                        <!-- </a> -->
                        <a href="{{ action("SubPagesController@show",["pages","openData"]) }}">
                            <div class="col-md-6 col-xs-12">
                                <span class="tool-border-green" style="display:table;">
                                    Open data
                                </span>
                            </div>
                        </a>
                    </div>
                    <div class="row">
                        <a href="{{ action("SubPagesController@show",["pages","research"]) }}">
                            <div class="col-md-6 col-xs-12">
                                <span class="tool-border-green"  style="display:table;">
                                    Research
                                </span>
                            </div>
                        </a>
                        <!-- <a href="#"> -->
                            <div class="col-md-6 col-xs-12">
                                <span class="tool-border-green"  style="display:table;">
                                    Sensors
                                </span>
                            </div>
                        <!-- </a> -->
                    </div>
                </div>
            </div>
        </div>
    </section>--}}
@endsection