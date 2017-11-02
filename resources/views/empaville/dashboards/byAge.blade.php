@extends('empaville._layout.index')

@section('content')
    <div class="row">
        <div class="col-md-12">
            {{--CountByAge--}}
            <div class="col-md-4">
                <div class="box box-success">
                    <div class="box-header">
                        <h3 class="box-title"><i class="fa"></i> {{trans('empaville.count_total_votes_by_age')}}</h3>
                    </div>
                    <div class="box-body">
                        <div class="col-md-11">
                            <canvas id="countByAge" style="height:230px"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            {{--FirstByAge--}}
            <div class="col-md-4">
                <div class="box box-success">
                    <div class="box-header">
                        <h3 class="box-title"><i class="fa"></i> {{trans('empaville.count_first_by_age')}}</h3>
                    </div>
                    <div class="box-body">
                        <div class="col-md-11">
                            <canvas id="firstByAge" style="height:230px"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            {{--First/second Votes by Age --}}

            {{--SecondByGender--}}
            <div class="col-md-4">
                <div class="box box-success">
                    <div class="box-header">
                        <h3 class="box-title"><i class="fa"></i> {{trans('empaville.count_second_by_age')}}</h3>
                    </div>
                    <div class="box-body">
                        <div class="col-md-11">
                            <canvas id="secondByAge" style="height:230px"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
@section('scripts')
    <script>

        $(function () {



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
            //- CountByAge -
            //--------------
            // Get context with jQuery - using jQuery's .get() method.
            var countByAge = $("#countByAge").get(0).getContext("2d");
            // This will get the first returned node in the jQuery collection.
            var countByAgeBarChart = new Chart(countByAge);

            var countByAge = {
                labels: ["< 19", "20 - 29", "30 - 39", "40 - 49", "50 - 59", "60 - 69","70 - 79"],
                datasets: [
                    {
                        label: "Positives",
                        fillColor: "rgba(0, 255, 0, 1)",
                        strokeColor: "rgba(0, 255, 0, 1)",
                        pointColor: "rgba(0, 255, 0, 1)",
                        pointStrokeColor: "#c1c7d1",
                        pointHighlightFill: "#fff",
                        pointHighlightStroke: "rgba(220,220,220,1)",
                        data:[
                            {!! empty($votesByAge[20]['Positives'])? 0 : $votesByAge[20]['Positives']!!},
                            {!! empty($votesByAge[30]['Positives'])? 0 : $votesByAge[30]['Positives']!!},
                            {!! empty($votesByAge[40]['Positives'])? 0 : $votesByAge[40]['Positives'] !!},
                            {!! empty($votesByAge[50]['Positives'])? 0 : $votesByAge[50]['Positives']!!},
                            {!! empty($votesByAge[60]['Positives'])? 0 : $votesByAge[60]['Positives']!!},
                            {!! empty($votesByAge[70]['Positives'])? 0 : $votesByAge[70]['Positives'] !!},
                            {!! empty($votesByAge[80]['Positives'])? 0 : $votesByAge[80]['Positives']!!}
                        ]
                    },
                    {
                        label: "Negatives",
                        fillColor: "rgba(255,0,0,0.9)",
                        strokeColor: "rgba(255,0,0,0.8)",
                        pointColor: "#3b8bba",
                        pointStrokeColor: "rgba(255,0,0,1)",
                        pointHighlightFill: "#fff",
                        pointHighlightStroke: "rgba(60,141,188,1)",
                        data: [
                            {!! empty($votesByAge[20]['Negatives'])? 0 : $votesByAge[20]['Negatives']!!},
                            {!! empty($votesByAge[30]['Negatives'])? 0 : $votesByAge[30]['Negatives']!!},
                            {!! empty($votesByAge[40]['Negatives'])? 0 : $votesByAge[40]['Negatives'] !!},
                            {!! empty($votesByAge[50]['Negatives'])? 0 : $votesByAge[50]['Negatives']!!},
                            {!! empty($votesByAge[60]['Negatives'])? 0 : $votesByAge[60]['Negatives']!!},
                            {!! empty($votesByAge[70]['Negatives'])? 0 : $votesByAge[70]['Negatives'] !!},
                            {!! empty($votesByAge[80]['Negatives'])? 0 : $votesByAge[80]['Negatives']!!}
                        ]
                    }
                ]
            };

            countByAgeBarChart.Bar(countByAge, barChartOptions);

            //--------------
            //- FirstByAge -
            //--------------
            // Get context with jQuery - using jQuery's .get() method.
            var firstByAge = $("#firstByAge").get(0).getContext("2d");
            // This will get the first returned node in the jQuery collection.
            var firstByAgeBarChart = new Chart(firstByAge);

            var firstByAge = {
                labels: ["< 19", "20 - 29", "30 - 39", "40 - 49", "50 - 59", "60 - 69","70 - 79"],
                datasets: [
                    {
                        label: "Positives",
                        fillColor: "rgba(0, 255, 0, 1)",
                        strokeColor: "rgba(0, 255, 0, 1)",
                        pointColor: "rgba(0, 255, 0, 1)",
                        pointStrokeColor: "#c1c7d1",
                        pointHighlightFill: "#fff",
                        pointHighlightStroke: "rgba(220,220,220,1)",
                        data:[
                            {!! empty($firstByAge[20]['Positives'])? 0 : $firstByAge[20]['Positives']!!},
                            {!! empty($firstByAge[30]['Positives'])? 0 : $firstByAge[30]['Positives']!!},
                            {!! empty($firstByAge[40]['Positives'])? 0 : $firstByAge[40]['Positives'] !!},
                            {!! empty($firstByAge[50]['Positives'])? 0 : $firstByAge[50]['Positives']!!},
                            {!! empty($firstByAge[60]['Positives'])? 0 : $firstByAge[60]['Positives']!!},
                            {!! empty($firstByAge[70]['Positives'])? 0 : $firstByAge[70]['Positives'] !!},
                            {!! empty($firstByAge[80]['Positives'])? 0 : $firstByAge[80]['Positives']!!}
                        ]
                    },
                    {
                        label: "Negatives",
                        fillColor: "rgba(255,0,0,0.9)",
                        strokeColor: "rgba(255,0,0,0.8)",
                        pointColor: "#3b8bba",
                        pointStrokeColor: "rgba(255,0,0,1)",
                        pointHighlightFill: "#fff",
                        pointHighlightStroke: "rgba(60,141,188,1)",
                        data: [
                            {!! empty($firstByAge[20]['Negatives'])? 0 : $firstByAge[20]['Negatives']!!},
                            {!! empty($firstByAge[30]['Negatives'])? 0 : $firstByAge[30]['Negatives']!!},
                            {!! empty($firstByAge[40]['Negatives'])? 0 : $firstByAge[40]['Negatives'] !!},
                            {!! empty($firstByAge[50]['Negatives'])? 0 : $firstByAge[50]['Negatives']!!},
                            {!! empty($firstByAge[60]['Negatives'])? 0 : $firstByAge[60]['Negatives']!!},
                            {!! empty($firstByAge[70]['Negatives'])? 0 : $firstByAge[70]['Negatives'] !!},
                            {!! empty($firstByAge[80]['Negatives'])? 0 : $firstByAge[80]['Negatives']!!}
                        ]
                    }
                ]
            };
            firstByAgeBarChart.Bar(firstByAge, barChartOptions);

            //--------------
            //- SecondByGNb -
            //--------------
            // Get context with jQuery - using jQuery's .get() method.
            var secondByAge = $("#secondByAge").get(0).getContext("2d");
            // This will get the first returned node in the jQuery collection.
            var secondByAgeBarChart = new Chart(secondByAge);

            var secondByAge = {
                labels: ["< 19", "20 - 29", "30 - 39", "40 - 49", "50 - 59", "60 - 69","70 - 79"],
                datasets: [
                    {
                        label: "Positives",
                        fillColor: "rgba(0, 255, 0, 1)",
                        strokeColor: "rgba(0, 255, 0, 1)",
                        pointColor: "rgba(0, 255, 0, 1)",
                        pointStrokeColor: "#c1c7d1",
                        pointHighlightFill: "#fff",
                        pointHighlightStroke: "rgba(220,220,220,1)",
                        data:[
                            {!! empty($secondByAge[20]['Positives'])? 0 : $secondByAge[20]['Positives']!!},
                            {!! empty($secondByAge[30]['Positives'])? 0 : $secondByAge[30]['Positives']!!},
                            {!! empty($secondByAge[40]['Positives'])? 0 : $secondByAge[40]['Positives']!!},
                            {!! empty($secondByAge[50]['Positives'])? 0 : $secondByAge[50]['Positives']!!},
                            {!! empty($secondByAge[60]['Positives'])? 0 : $secondByAge[60]['Positives']!!},
                            {!! empty($secondByAge[70]['Positives'])? 0 : $secondByAge[70]['Positives']!!},
                            {!! empty($secondByAge[80]['Positives'])? 0 : $secondByAge[80]['Positives']!!}
                        ]
                    },
                    {
                        label: "Negatives",
                        fillColor: "rgba(255,0,0,0.9)",
                        strokeColor: "rgba(255,0,0,0.8)",
                        pointColor: "#3b8bba",
                        pointStrokeColor: "rgba(255,0,0,1)",
                        pointHighlightFill: "#fff",
                        pointHighlightStroke: "rgba(60,141,188,1)",
                        data: [
                            {!! empty($secondByAge[20]['Negatives'])? 0 : $secondByAge[20]['Negatives']!!},
                            {!! empty($secondByAge[30]['Negatives'])? 0 : $secondByAge[30]['Negatives']!!},
                            {!! empty($secondByAge[40]['Negatives'])? 0 : $secondByAge[40]['Negatives']!!},
                            {!! empty($secondByAge[50]['Negatives'])? 0 : $secondByAge[50]['Negatives']!!},
                            {!! empty($secondByAge[60]['Negatives'])? 0 : $secondByAge[60]['Negatives']!!},
                            {!! empty($secondByAge[70]['Negatives'])? 0 : $secondByAge[70]['Negatives']!!},
                            {!! empty($secondByAge[80]['Negatives'])? 0 : $secondByAge[80]['Negatives']!!}
                        ]
                    }
                ]
            };
            secondByAgeBarChart.Bar(secondByAge, barChartOptions);

        });


    </script>
@endsection
