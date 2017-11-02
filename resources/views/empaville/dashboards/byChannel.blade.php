@extends('empaville._layout.index')

@section('content')
    <div class="box box-success">
        <div class="box-header with-border">
            <h3 class="box-title"><i class="fa"></i> {{ trans('empaville.channels') }}</h3>
        </div>
        <div class="box-body">
            <div class="table-responsive">
                <table id="proposals_list" class="table table-bordered table-hover table-striped order-column hover">
                    <thead>
                    <tr>
                        <th rowspan="2">{{ trans('empaville.title') }}</th>
                        <th rowspan="2" style="width: 20px;">{{ trans('empaville.totals') }}</th>
                        @foreach($channels as $channel)
                            <th colspan="3">{{$channel}}</th>
                        @endforeach
                    </tr>
                    <tr>
                        @foreach($channels as $channel)
                            <th style="width: 20px;">B</th>
                            <th style="width: 20px;">+</th>
                            <th style="width: 20px;">-</th>
                        @endforeach

                    </tr>
                    </thead>
                    <tbody>
                    @foreach( $votesByChannel as $vote)
                        <tr>
                            <td>{{$vote['title']}}</td>
                            <td>{{ !empty($vote['total'])? $vote['total'] : 0 }}</td>
                            @foreach( $channels as $channel)
                                <td>{{ !empty($vote['channels'][$channel]['balance'])? $vote['channels'][$channel]['balance'] : 0 }}</td>
                                <td>{{ !empty($vote['channels'][$channel]['positives'])? $vote['channels'][$channel]['positives'] : 0 }}</td>
                                <td>{{ !empty($vote['channels'][$channel]['negatives'])? $vote['channels'][$channel]['negatives'] : 0 }}</td>
                            @endforeach
                        </tr>
                    @endforeach
                    </tbody>

                </table>
            </div>
        </div>
    </div>

    <div class="row">
        {{--CountByGender--}}
        <div class="col-md-4">
            <div class="box box-success">
                <div class="box-header">
                    {{trans('empaville.count_total_votes_by_channel')}}
                </div>
                <div class="box-body">
                    <div class="col-md-11">
                        <canvas id="countByChannel" style="height:230px"></canvas>
                    </div>
                </div>
            </div>
        </div>
        {{--FirstByGender--}}
        <div class="col-md-4">
            <div class="box box-success">
                <div class="box-header">
                    {{trans('empaville.count_first_by_channel')}}
                </div>
                <div class="box-body">
                    <div class="col-md-11">
                        <canvas id="firstByChannel" style="height:230px"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{--SecondByGender--}}
        <div class="col-md-4">
            <div class="box box-success">
                <div class="box-header">
                    {{trans('empaville.count_second_by_channel')}}
                </div>
                <div class="box-body">
                    <div class="col-md-11">
                        <canvas id="secondByChannel" style="height:230px"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>


@endsection

@section('scripts')

    <script>

        $(document).ready(function() {
            var table = $('#proposals_list').DataTable({
                "paging":   false,
                "info":     false,
                "bFilter" :  false
            } );


            $('#proposals_list tbody')
                    .on( 'mouseenter', 'td', function () {
                        var colIdx = table.cell(this).index().column;
                        $( table.cells().nodes() ).removeClass( 'highlight');
                        $( table.column( colIdx ).nodes() ).addClass( 'highlight' );
                    } );
        } );

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
            //- CountByChannel -
            //--------------
            // Get context with jQuery - using jQuery's .get() method.
            var countByChannel = $("#countByChannel").get(0).getContext("2d");
            // This will get the first returned node in the jQuery collection.
            var countByChannelBarChart = new Chart(countByChannel);

            var countByChannel = {
                labels: [
                    @foreach($channels as $channel)
                            "{{$channel}}",
                    @endforeach
                ],
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
                            @foreach($channels as $channel)
                            {{ !empty($byChannel[$channel]['positives'])? $byChannel[$channel]['positives'] : 0 }},
                            @endforeach
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
                            @foreach($channels as $channel)
                            {{ !empty($byChannel[$channel]['negatives'])? $byChannel[$channel]['negatives'] : 0 }},
                            @endforeach
                        ]
                    }
                ]
            };

            countByChannelBarChart.Bar(countByChannel, barChartOptions);


            //--------------
            //- FirstByGender -
            //--------------
            // Get context with jQuery - using jQuery's .get() method.
            var firstByChannel = $("#firstByChannel").get(0).getContext("2d");
            // This will get the first returned node in the jQuery collection.
            var firstByChannelBarChart = new Chart(firstByChannel);

            var firstByChannel = {
                labels: [
                    @foreach($channels as $channel)
                            "{{$channel}}",
                    @endforeach
                ],
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
                            @foreach($channels as $channel)
                            {{ !empty($firstByChannel[$channel]['positives'])? $firstByChannel[$channel]['positives'] : 0 }},
                            @endforeach
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
                            @foreach($channels as $channel)
                            {{ !empty($firstByChannel[$channel]['negatives'])? $firstByChannel[$channel]['negatives'] : 0 }},
                            @endforeach
                        ]
                    }
                ]
            };
            firstByChannelBarChart.Bar(firstByChannel, barChartOptions);

            //--------------
            //- SecondByGNb -
            //--------------
            // Get context with jQuery - using jQuery's .get() method.
            var secondByChannel = $("#secondByChannel").get(0).getContext("2d");
            // This will get the first returned node in the jQuery collection.
            var secondByChannelBarChart = new Chart(secondByChannel);

            var secondByChannel = {
                labels: [
                    @foreach($channels as $channel)
                            "{{$channel}}",
                    @endforeach
                ],
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
                            @foreach($channels as $channel)
                            {{ !empty($secondByChannel[$channel]['positives'])? $secondByChannel[$channel]['positives'] : 0 }},
                            @endforeach
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
                            @foreach($channels as $channel)
                            {{ !empty($secondByChannel[$channel]['negatives'])? $secondByChannel[$channel]['negatives'] : 0 }},
                            @endforeach
                        ]
                    }
                ]
            };
            secondByChannelBarChart.Bar(secondByChannel, barChartOptions);
        });

    </script>
@endsection