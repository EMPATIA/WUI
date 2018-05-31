@extends('public.empaville._layouts.index')


@section('content')
    <!-- Header -->
    <div class="container">
        <div class="contentPage-heading-wrapper">
            <div class="row">
                <div class="col-xs-10">
                    <h2>{{ trans("PublicCbs.generalMap") }}</h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Maps -->
    <div class="container">
        {!! Form::oneMapsLocations("mapsLocations", "Maps", $locations)) !!}
    </div>
@endsection

@section('scripts')
    <!-- Maps -->
    <script   src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC7NLFqdo5V4czUHhEWMm4bfRo_Zag0AKQ" type="text/javascript"></script>
    <script type="text/javascript" src="{{ asset('js/js-marker-clusterer-gh-pages/src/markerclusterer.js') }}"></script>
@endsection