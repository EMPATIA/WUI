@extends('public.empaville_new._layouts.index')
@section('content')
    <!-- Pads list section -->
    <section>
        <div class="container pads-container">
            <div class="row">
                <div class="col-xs-12">
                    <div class="pads-title">
                        <h3>{!! trans('defaultCbsProposal.proposals') !!}</h3>
                    </div>
                </div>
            </div>

            <div class="row cbs-boxes" id="infinite-scroll">
                @include('public.empaville_new.cbs.proposal.padsList')
                @if(empty($cbsData) || count($cbsData) == 0)
                    <div class="col-xs-12">
                        <div class="alertBoxGreen">
                            <div class="col-xs-12 text-center">
                                <h2>{{ trans('defaultCbsProposal.no_proposals_to_display') }}</h2>
                            </div>
                            <div class="col-xs-12 text-center">
                                <i class="fa fa-exclamation-triangle" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </section>


@endsection

@section('scripts')
    <script>
        $('#infinite-scroll').jscroll({
            loadingHtml: '<div class="text-center"><img src="{{ asset('images/preloader.gif') }}" alt="Loading" /></div>',
            nextSelector: 'a.jscroll-next:last',
            callback: checkBoxes
        });

        $(document).ready(function () {
            checkBoxes();
        });

        function checkBoxes(){
            $(document).ready(function () {
                // Setting equal heights for div's with jQuery
                var highestBox = 0;
                $('.cbs-box').each(function () {
                    if ($(this).height() > highestBox) {
                        highestBox = $(this).height();
                    }
                });
                $('.cbs-box').height(highestBox);

                <!-- Dot Dot Dot -->
                $.each([$(".cb-title"), $(".cb-content")], function (index, value) {
                    $(document).ready(function () {
                        value.dotdotdot({
                            ellipsis: '... ',
                            wrap: 'word',
                            aft: null,
                        });
                    });
                });

                $(window).trigger('resize.px.parallax');
            });
        }
    </script>
@endsection
