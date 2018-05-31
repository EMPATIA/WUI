@extends('public.empaville_new._layouts.index')
@section('content')

    <!-- Topics list section -->
    <section>
        <div class="container">

            <div class="row padding-box-default">
                <div class="col-xs-12 text-right create-button-div map-back-btn-div">
                    <a href="{!!action('PublicCbsController@show', ['cbKey' => $cbKey, 'type' => $type] ) !!}">{{ trans("defaultCbs.backButton") }}</a>
                </div>
            </div>

            <div class="mapContainer">
                {!! Form::oneMapsLocations("mapsLocations", "", $locations, array("markerIcon" => asset('images/default/pins/construction.png'), "zoom" => 13, "folderIcons" => "/images/default/pins/", "defaultLocation" => "38.7436214, -9.1952231", "style" => "height:550px;width:100%;"), array("select" => "geometry", "from" => "1N2LBk4JHwWpOY4d9fobIn27lfnZ5MDy-NoqqRpk", "where" => "ISO_2DIGIT IN ('PT')")) !!}
            </div>

        </div>
    </section>
@endsection


@section('scripts')
    <!-- Map -->
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBJtyhsJJX_5DCp59m8sNsPlhHp8aQZHIE"></script>
    <script type="text/javascript" src="{{ asset('js/js-marker-clusterer-gh-pages/src/markerclusterer.js') }}"></script>
    <script>

        $('.mapContainer').on('click', function () {
            dotDot();
        });
        var dotDot = function () {
            $.each([$(".map-dialog-title")], function (index, value) {
                $(document).ready(function () {
                    value.dotdotdot({
                        watch: "window",
                        ellipsis: '... ',
                        wrap: 'word',
                        aft: null
                    });
                });
            });
        }
    </script>
@endsection

