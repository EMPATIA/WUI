@extends('empaville._layout.index')

@section('content')
        <div class="box box-success">
            <div class="box-header text-center">
                <h3 class="box-title"><i class="fa"></i> {{trans('empaville.chart_by_gender')}}</h3>
            </div>
            <div class="box-body">
                <div class="col-md-3 chart-legend" id="js-legend"></div>
                <div class="col-md-6" style="">
                    <canvas id="pieChart" style=""></canvas>
                </div>
            </div>
        </div>

    <div class="row">
        {{--CountByGender--}}
        <div class="col-md-4">
            <div class="box box-success">
                <div class="box-header">
                    {{trans('empaville.count_total_votes_by_gender')}}
                </div>
                <div class="box-body">
                    <div class="col-md-11">
                        <canvas id="countByGender" style="height:230px"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{--First/second Votes by Gender --}}

        {{--FirstByGender--}}
        <div class="col-md-4">
            <div class="box box-success">
                <div class="box-header">
                    {{trans('empaville.count_first_by_gender')}}
                </div>
                <div class="box-body">
                    <div class="col-md-11">
                        <canvas id="firstByGender" style="height:230px"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{--First/second Votes by Gender --}}

        {{--SecondByGender--}}
        <div class="col-md-4">
            <div class="box box-success">
                <div class="box-header">
                    {{trans('empaville.count_second_by_gender')}}
                </div>
                <div class="box-body">
                    <div class="col-md-12">
                        <canvas id="secondByGender" style="height:230px"></canvas>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <style>
        .chart-legend li{
            list-style-type: none;
        }
        .chart-legend li span{
            display: inline-block;
            width: 12px;
            height: 12px;
            margin-right: 5px;
        }
    </style>
    @endsection

@section('scripts')

    <script>


        $(function () {
            //-------------
            //- PIE CHART -
            //-------------
            // Get context with jQuery - using jQuery's .get() method.
            var pieChartCanvas = $("#pieChart").get(0).getContext("2d");
            var pieChart = new Chart(pieChartCanvas);
            var PieData = [
                {
                    value: [{!! isset($votesByGender->Male->Total)? $votesByGender->Male->Total : null !!}],
                    color: "#f56954",
                    highlight: "#f56954",
                    label: "{{trans('empaville.male')}}"
                },
                {
                    value: [{!! isset($votesByGender->Female->Total)? $votesByGender->Female->Total : null !!}],
                    color: "#00a65a",
                    highlight: "#00a65a",
                    label: "{{trans('empaville.female')}}"
                }
            ];
            var pieOptions = {
                //Boolean - Whether we should show a stroke on each segment
                segmentShowStroke: true,
                //String - The colour of each segment stroke
                segmentStrokeColor: "#fff",
                //Number - The width of each segment stroke
                segmentStrokeWidth: 2,
                //Number - The percentage of the chart that we cut out of the middle
                percentageInnerCutout: 0, // This is 0 for Pie charts
                //Number - Amount of animation steps
                animationSteps: 100,
                //String - Animation easing effect
                animationEasing: "easeOutBounce",
                //Boolean - Whether we animate the rotation of the Doughnut
                animateRotate: true,
                //Boolean - Whether we animate scaling the Doughnut from the centre
                animateScale: false,
                //Boolean - whether to make the chart responsive to window resizing
                responsive: true,
                // Boolean - whether to maintain the starting aspect ratio or not when responsive, if set to false, will take up entire container
                maintainAspectRatio: true,

            };
                        //Create pie or douhnut chart
            // You can switch between pie and douhnut using the method below.
            var chart = pieChart.Pie(PieData, pieOptions);
            $('#js-legend').append(chart.generateLegend());

            var barChartOptions = {
                //Boolean - Whether the scale should start at zero, or an order of magnitude down from the lowest value
                scaleBeginAtZero: true,
                //Boolean - Whether grid lines are shown across the chart
                scaleShowGridLines: true,
                //String - Colour of the grid lines
                scaleGridLineColor: "rgba(0,0,0,.2)",
                //Number - Width of the grid lines
                scaleGridLineWidth: 1,
                //Boolean - Whether to show horizontal lines (except X axis)
                scaleShowHorizontalLines: true,
                //Boolean - Whether to show vertical lines (except Y axis)
                scaleShowVerticalLines: true,
                //Boolean - If there is a stroke on each bar
                barShowStroke: true,
                //Number - Pixel width of the bar stroke
                barStrokeWidth: 2,
                //Number - Spacing between each of the X value sets
                barValueSpacing: 5,
                //Number - Spacing between data sets within X values
                barDatasetSpacing: 1,
                //Boolean - whether to make the chart responsive
                responsive: true,
                maintainAspectRatio: false
            };
            barChartOptions.datasetFill = false;
            //--------------
            //- CountByGender -
            //--------------
            // Get context with jQuery - using jQuery's .get() method.
            var countByGender = $("#countByGender").get(0).getContext("2d");
            // This will get the first returned node in the jQuery collection.
            var countByGenderBarChart = new Chart(countByGender);

            var countByGender = {
                labels: ["{{trans('empaville.male')}}", "{{trans('empaville.female')}}"],
                datasets: [
                    {
                        label: "{{trans('empaville.positives')}}",
                        fillColor: "rgba(0, 255, 0, 1)",
                        strokeColor: "rgba(0, 255, 0, 1)",
                        pointColor: "rgba(0, 255, 0, 1)",
                        pointStrokeColor: "#c1c7d1",
                        pointHighlightFill: "#fff",
                        pointHighlightStroke: "rgba(220,220,220,1)",
                        data:[
                            {!! isset($votesByGender->Male->Positives) ? $votesByGender->Male->Positives : null !!},
                            {!! isset($votesByGender->Female->Positives) ? $votesByGender->Female->Positives : null !!},

                        ]
                    },
                    {
                        label: "{{trans('empaville.negatives')}}",
                        fillColor: "rgba(255,0,0,0.9)",
                        strokeColor: "rgba(255,0,0,0.8)",
                        pointColor: "#3b8bba",
                        pointStrokeColor: "rgba(255,0,0,1)",
                        pointHighlightFill: "#fff",
                        pointHighlightStroke: "rgba(60,141,188,1)",
                        data: [
                            {!! isset($votesByGender->Male->Negatives) ? $votesByGender->Male->Negatives : null !!},
                            {!! isset($votesByGender->Female->Negatives) ? $votesByGender->Female->Negatives : null !!},

                        ]
                    }
                ]
            };

            countByGenderBarChart.Bar(countByGender, barChartOptions);


            //--------------
            //- FirstByGender -
            //--------------
            // Get context with jQuery - using jQuery's .get() method.
            var firstByGender = $("#firstByGender").get(0).getContext("2d");
            // This will get the first returned node in the jQuery collection.
            var firstByGenderBarChart = new Chart(firstByGender);

            var firstByGender = {
                labels: ["{{trans('empaville.male')}}", "{{trans('empaville.female')}}",],
                datasets: [
                    {
                        label: "{{trans('empaville.positives')}}",
                        fillColor: "rgba(0, 255, 0, 1)",
                        strokeColor: "rgba(0, 255, 0, 1)",
                        pointColor: "rgba(0, 255, 0, 1)",
                        pointStrokeColor: "#c1c7d1",
                        pointHighlightFill: "#fff",
                        pointHighlightStroke: "rgba(220,220,220,1)",
                        data:[
                            {!! isset($firstByGender->Male->Positives) ? $firstByGender->Male->Positives : null !!},
                            {!! isset($firstByGender->Female->Positives) ? $firstByGender->Female->Positives : null !!},

                        ]
                    },
                    {
                        label: "{{trans('empaville.negatives')}}",
                        fillColor: "rgba(255,0,0,0.9)",
                        strokeColor: "rgba(255,0,0,0.8)",
                        pointColor: "#3b8bba",
                        pointStrokeColor: "rgba(255,0,0,1)",
                        pointHighlightFill: "#fff",
                        pointHighlightStroke: "rgba(60,141,188,1)",
                        data: [
                            {!! isset($firstByGender->Male->Negatives) ? $firstByGender->Male->Negatives : null !!},
                            {!! isset($firstByGender->Female->Negatives) ? $firstByGender->Female->Negatives : null !!},


                        ]
                    }
                ]
            };
            firstByGenderBarChart.Bar(firstByGender, barChartOptions);

            //--------------
            //- SecondByGender -
            //--------------
            // Get context with jQuery - using jQuery's .get() method.
            var secondByGender = $("#secondByGender").get(0).getContext("2d");
            // This will get the first returned node in the jQuery collection.
            var secondByGenderBarChart = new Chart(secondByGender);

            var secondByGender = {
                labels: ["{{trans('empaville.male')}}", "{{trans('empaville.female')}}"],
                datasets: [
                    {
                        label: "{{trans('empaville.positives')}}",
                        fillColor: "rgba(0, 255, 0, 1)",
                        strokeColor: "rgba(0, 255, 0, 1)",
                        pointColor: "rgba(0, 255, 0, 1)",
                        pointStrokeColor: "#c1c7d1",
                        pointHighlightFill: "#fff",
                        pointHighlightStroke: "rgba(220,220,220,1)",
                        data:[
                            {!! isset($secondByGender->Male->Positives) ? $secondByGender->Male->Positives : null !!},
                            {!! isset($secondByGender->Female->Positives) ? $secondByGender->Female->Positives : null !!},

                        ]
                    },
                    {
                        label: "{{trans('empaville.negatives')}}",
                        fillColor: "rgba(255,0,0,0.9)",
                        strokeColor: "rgba(255,0,0,0.8)",
                        pointColor: "#3b8bba",
                        pointStrokeColor: "rgba(255,0,0,1)",
                        pointHighlightFill: "#fff",
                        pointHighlightStroke: "rgba(60,141,188,1)",
                        data: [
                            {!! isset($secondByGender->Male->Negatives) ? $secondByGender->Male->Negatives : null !!},
                            {!! isset($secondByGender->Female->Negatives) ? $secondByGender->Female->Negatives : null !!},

                        ]
                    }
                ]
            };
            secondByGenderBarChart.Bar(secondByGender, barChartOptions);



        });


    </script>

@endsection