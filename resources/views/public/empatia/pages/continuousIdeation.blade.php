@extends('public.empatia._layouts.index')

@section('content')

    @php
        // Getting layout sections
        $layoutSections = App\Http\Controllers\PublicContentManagerController::getSections("pages-continuousIdeation");
    @endphp

    @foreach(!empty($layoutSections) ? $layoutSections : [] as $layoutSection)
        @if($layoutSection)
            @includeif("public." . ONE::getEntityLayout() . ".cms.sections." . $layoutSection->section_type->code, ['section' => $layoutSection])
        @endif
    @endforeach

{{--    <section class="background-white padding-top-bottom-35">
        <div class="row menus-row margin-top-15 margin-bottom-35">
            <div class="menus-line col-sm-6 col-sm-offset-3 text-uppercase">
                <span class="fa fa-comment" style="color: #b3b3b3">&nbsp;</span>
                Public Participation
            </div>
        </div>
        <br><br>
        <div class="container color-black">
            <h3 class="color-green sub-page-title">
                Continuous Ideation
            </h3>
            <p class="padding-top-35">
                Continuous ideation processes allow people to submit ideas to a municipality at any time. These
                processes are different from other participatory processes that have a fixed cycle. However in the
                majority of these processes the participants are also invited to rank the top ideas to reduce the
                amount of time required to filter the ideas to be reviewed by the municipality. For example in many
                cases only the top ranked ideas are reviewed by city officials, and in case they are feasible they are
                implemented. The most common problems of these processes are:
            </p>
            <div class="padding-top-35" style="margin-top: 20px">
                <table class="table table-responsive continuousIdeation-table">
                    <tr>
                        <td style="width:12.5%">
                            <h4 class="color-green" style="text-transform: uppercase;text-align: right">
                                Problems
                            </h4>
                        </td>
                        <td>
                            <h4>Retention</h4>
                            <p>
                                If participants do not obtain a clear feedback on the fate of their idea they disengage.
                            </p>
                        </td>
                        <td>
                            <h4>Proliferation of redundant ideas</h4>
                            <p>
                                If these processes engage a large public they risk to generate a large number of ideas
                                that cannot be processed timely.
                            </p>
                        </td>
                        <td>
                            <h4>Co-creation bissues</h4>
                            <p>
                                In many cases the author of the idea can change the idea at any time. That implies that
                                users that have given support to an idea might discover later that such idea has been
                                changed.
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="width:12.5%">
                            <h4 class="color-green" style="text-transform: uppercase; text-align: right">
                                Solutions
                            </h4>
                        </td>
                        <td>
                            <h4>Retention module</h4>
                            <p>
                                An algorithm identifies and classifies users on the basis of their probability to
                                disengage,
                                then it sends automated messages or direct the municipality to directly contact them.
                            </p>
                        </td>
                        <td>
                            <h4>Alliances</h4>
                            <p>
                                The alliance mechanism is a gamified approach to reduction of redundancies in ideation
                                platforms. Users are invited to ally with similar ideas in order to some the number of
                                supports (likes) their ideas have received and have a higher chance to get their idea
                                approved.
                            </p>
                        </td>
                        <td>
                            <h4>Embargo period & advanced notification</h4>
                            <p>
                                Empatia enforces an embargo period that combined with an advanced notification period is
                                designed to reduce the co-creation issue. When a participants submit an idea such idea
                                has
                                a certain number of days to achieve the threshold of supports required for review (for
                                example 500 likes). The Empatia ideation solution allows the author or a group to change
                                the idea up to 7 days before the end of the process. At that moment if the idea was
                                changed all supporters are notified and can remove their support.
                            </p>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </section>--}}
@endsection

@section("header_styles")

    <style>
        table, table tr, table td {
            border: 0 !important;
        }

        table tr:first-child {
            border-bottom: 3px solid #8bc740 !important;
        }

        table tr td {
            border-right: 2px solid #CCCCCC !important;
        }

        table tr td:first-child {
            border-right: 3px solid #8bc740 !important;
        }

        table tr td:last-child {
            border-right: 0 !important;
        }

        table td{
            width: 28.125%;
        }
    </style>
@endsection