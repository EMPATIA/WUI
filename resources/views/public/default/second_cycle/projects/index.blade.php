@extends('public.default._layouts.index')
@section('header_scripts')
    <!-- Maps -->
    <script  src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCjgiI5l8FanufeE3GRchTZSVOaAyzVIE8" type="text/javascript"></script>
    <link rel="stylesheet" href="{{ asset('css/default/secondcycle.css')}}" />
@endsection
@section('content')
    {{--Topics list section--}}
    <section>
        <div class="container pads-container">
            <div class="row">
                <div class="col-xs-12">
                    <div class="pads-title">
                        <h3>{!! $title ?? trans('defaultSecondCycle.title') !!}</h3>
                    </div>
                </div>
                <div class="col-xs-12">
                    <div class="pads-description">
                        {!! $description ?? trans('defaultSecondCycle.description') !!}
                    </div>
                </div>
            </div>
	              
            <div class="margin-top">
                <div class="row cbs-boxes topics-grid" id="infinite-scroll">
                    @include('public.default.second_cycle.projects.list_ajax')
                    @if(count($space->getNodes("projects")) == 0)
                        <div class="col-xs-12">
                            <div class="alertBoxGreen">
                                <div class="col-xs-12 text-center">
                                    <h2>{{ trans('defaultSecondCycle.no_topics_to_display') }}</h2>
                                </div>
                                <div class="col-xs-12 text-center">
                                    <i class="fa fa-exclamation-triangle" aria-hidden="true"></i>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

@endsection
@section('scripts')
    <script>

    </script>
@endsection
