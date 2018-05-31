@extends('public.empatia._layouts.index')

@section('content')

    @php
        // Getting layout sections
        $layoutSections = App\Http\Controllers\PublicContentManagerController::getSections("tools-index");
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
                Empatia Tools
            </div>
        </div>
        <br><br>
        <div class="container color-black">
            <div class="row margin-top-35">
                <a href="{{ action("SubPagesController@show",["tools","participatoryBudgeting"]) }}">
                    <div class="col-md-3 col-sm-6 col-xs-12">
                        <div class="tool-border-green">
                            <img class="tool-img" src="{{ asset('images/empatia/icon/empatiaTools-icons_participatoryBudgeting_2.png') }}">
                            <img class="tool-img-hover" src="{{ asset('images/empatia/icon/empatiaTools-icons_white_participatoryBudgeting_2.png') }}">
                            <p>
                                Participatory Budgeting
                            </p>
                        </div>
                    </div>
                </a>
                <a href="{{ action("SubPagesController@show",["tools","empaville"]) }}">
                    <div class="col-md-3 col-sm-6 col-xs-12">
                        <div class="tool-border-green">
                            <img class="tool-img" src="{{ asset('images/empatia/icon/empatiaTools-icons_empaville.png') }}">
                            <img class="tool-img-hover" src="{{ asset('images/empatia/icon/empatiaTools-icons_white_empaville.png') }}">
                            <p>
                                EMPAVILLE
                            </p>
                        </div>
                    </div>
                </a>
                <a href="{{ action("SubPagesController@show",["tools","designTool"]) }}">
                    <div class="col-md-3 col-sm-6 col-xs-12">
                        <div class="tool-border-green">
                            <img class="tool-img" src="{{ asset('images/empatia/icon/empatiaTools-icons_designTool.png') }}">
                            <img class="tool-img-hover" src="{{ asset('images/empatia/icon/empatiaTools-icons_white_designTool.png') }}">
                            <p>
                                Design tool
                            </p>
                        </div>
                    </div>
                </a>
                <a href="/page/pages/continuousIdeation">
                    <div class="col-md-3 col-sm-6 col-xs-12">
                        <div class="tool-border-green">
                            <img class="tool-img" src="{{ asset('images/empatia/icon/empatiaTools-icons_ideation.png') }}">
                            <img class="tool-img-hover" src="{{ asset('images/empatia/icon/empatiaTools-icons_white_ideation.png') }}">
                            <p>
                                Ideation
                            </p>
                        </div>
                    </div>
                </a>
            </div>
            <div class="row">
                <a href="/page/pages/integratedVoting">
                    <div class="col-md-3 col-sm-6 col-xs-12">
                        <div class="tool-border-green">
                            <img class="tool-img" src="{{ asset('images/empatia/icon/empatiaTools-icons_eVoting.png') }}">
                            <img class="tool-img-hover" src="{{ asset('images/empatia/icon/empatiaTools-icons_white_eVoting.png') }}">
                            <p>
                                E-voting
                            </p>
                        </div>
                    </div>
                </a>
                <a href="{{ action("SubPagesController@show",["tools","kiosks"]) }}">
                    <div class="col-md-3 col-sm-6 col-xs-12">
                        <div class="tool-border-green">
                            <img class="tool-img" src="{{ asset('images/empatia/icon/empatiaTools-icons_kiosks.png') }}">
                            <img class="tool-img-hover" src="{{ asset('images/empatia/icon/empatiaTools-icons_white_kiosks.png') }}">
                            <p>
                                Kiosks
                            </p>
                        </div>
                    </div>
                </a>
                <a href="{{ action("SubPagesController@show",["pages","research"]) }}">
                    <div class="col-md-3 col-sm-6 col-xs-12">
                        <div class="tool-border-green">
                            <img class="tool-img" src="{{ asset('images/empatia/icon/empatiaTools-icons_research_1.png') }}">
                            <img class="tool-img-hover" src="{{ asset('images/empatia/icon/empatiaTools-icons_white_research_1.png') }}">
                            <p>
                                Research
                            </p>
                        </div>
                    </div>
                </a>
                <a href="{{ action("SubPagesController@show",["tools","fix"]) }}">
                    <div class="col-md-3 col-sm-6 col-xs-12">
                        <div class="tool-border-green">
                            <img class="tool-img" src="{{ asset('images/empatia/icon/empatiaTools-icons_fixIt.png') }}">
                            <img class="tool-img-hover" src="{{ asset('images/empatia/icon/empatiaTools-icons_white_fixIt.png') }}">
                            <p>
                                Fix!
                            </p>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </section>--}}
@endsection
@section('scripts')
    <script>

        $(document).ready(function () {
            // Setting equal heights for div's with jQuery
            if ($(window).width() > 767) {
                checkToolBoxes();
            }
        });

        function checkToolBoxes(){
            $('.tool-border-green').removeAttr('style');
            if ($(window).width() > 767) {
                var highestBox = 0;
                $('.tool-border-green').each(function () {
                    if ($(this).height() > highestBox) {
                        highestBox = $(this).height();
                    }
                });
                $('.tool-border-green').height(highestBox);
            }
        }

        $(window).resize(function(){
            checkToolBoxes();
        });


    </script>
@endsection
