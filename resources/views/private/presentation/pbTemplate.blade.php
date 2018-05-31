
@extends('private.presentation.index')
@section('header_styles')
    <link rel="stylesheet" href="{{ asset('css/flowchart/flowchart.css')}}" />
    <link rel="stylesheet" href="{{ asset('css/flowchart/flowchartMain.css')}}" />
@endsection
@section('content')
    <div class="welcome-container">
        <div class="row box-buffer">
            <div class="col-12 text-center">
                <div class="welcome-title text-uppercase">{{trans("privatePresentation.pbTemplateTitle")}}</div>
            </div>
            <div class="col-12 graph-container">
                <div id="chart_container">
                    <div class="" id="flowchart_mp"></div>
                </div>
            </div>
			<div class="col-12 bottom-actions text-right" style="position:absolute;right:10px; bottom: 20px;">
                <a class="btn btn-presentation text-uppercase" href="{{action('PresentationController@show',['page' => 'ideaConfig'])}}">
                    {{trans("privatePresentation.build")}}
                </a>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset("js/flowchart/flowchart.js") }}"></script>
    <script src="{{ asset("js/flowchart/panzoom.js") }}"></script>
    <script src="{{ asset("js/flowchart/jquery-ui.js") }}"></script>
    <script>

        $(document).ready(function() {
            var $flowchart = $('#flowchart_mp');

            var $container = $flowchart.parent();

            var cx = $flowchart.width() / 2;
            var cy = $flowchart.height() / 2;


            // Panzoom initialization...
            $flowchart.panzoom();

            // Centering panzoom
            $flowchart.panzoom('pan', -cx + $container.width() / 2, -cy + $container.height() / 2);

            // Panzoom zoom handling...
            var possibleZooms = [0.5, 0.75, 1, 2];
            var currentZoom = 1;
            $container.on('mousewheel.focal', function( e ) {
                e.preventDefault();
                var delta = (e.delta || e.originalEvent.wheelDelta) || e.originalEvent.detail;
                var zoomOut = delta ? delta < 0 : e.originalEvent.deltaY > 0;
                currentZoom = Math.max(0, Math.min(possibleZooms.length - 1, (currentZoom + (zoomOut * 2 - 1))));
                $flowchart.flowchart('setPositionRatio', possibleZooms[currentZoom]);
                $flowchart.panzoom('zoom', possibleZooms[currentZoom], {
                    animate: false,
                    focal: e
                });
            });


            // Apply the plugin on a standard, empty div...
            $flowchart.flowchart({
                canUserEditLinks: false,
//                canUserMoveOperators: false,
                multipleLinksOnInput: true,
                multipleLinksOnOutput: true,
                defaultLinkColor: '#0e2d8c'

            });


            //Get data from Flowchart
            $('.get_data').click(function() {
                var data = $flowchart.flowchart('getData');
                $('#flowchart_data').val(JSON.stringify(data));
            });

            //Reset Flowchart
            $('.set_data').click(function() {
                var data = JSON.parse($('#flowchart_data').val());
                $flowchart.flowchart('setData', data);
            });

            //Flowchart - print diagram form database
            $flowchart.flowchart('setData', JSON.parse('{!! (isset($mp->diagram_code) ?  $mp->diagram_code : '{}') !!}'));

        });

    </script>

@endsection