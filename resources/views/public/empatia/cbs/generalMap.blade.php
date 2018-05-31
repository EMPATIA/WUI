@extends('public.default._layouts.index')


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
        {!! Form::oneMapsLocations("mapsLocations", "Maps", $locations,array("zoom" => 11, "defaultLocation" => "40.197854, -8.414533", "style" => "height:500px;width:100%;")) !!}
    </div>
@endsection

@section('header_scripts')
    <!-- Maps -->
    <script   src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBJtyhsJJX_5DCp59m8sNsPlhHp8aQZHIE" type="text/javascript"></script>
    <script type="text/javascript" src="{{ asset('js/js-marker-clusterer-gh-pages/src/markerclusterer.js') }}"></script>
@endsection