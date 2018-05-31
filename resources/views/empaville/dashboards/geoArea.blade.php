@extends('empaville._layout.index')

@section('content')
    <div class="box box-success">
        <div class="box-header with-border">
            <h3 class="box-title"><i class="fa"></i> {{ trans('empaville.geoArea') }}</h3>
        </div>
        <div class="box-body">
            <div class="table-responsive">
                <table id="proposals_list" class="table table-bordered table-hover table-striped">
                    <thead>
                    <tr>
                        <th rowspan="2">{{ trans('empaville.title') }}</th>
                        <th rowspan="2">{{ trans('empaville.geoArea') }}</th>
                        <th rowspan="2" style="width: 20px;">{{ trans('empaville.totals') }}</th>
                        <th colspan="3">{{trans('empaville.Uptown')}}</th>
                        <th colspan="3">{{trans('empaville.Middletown')}}</th>
                        <th colspan="3">{{trans('empaville.Downtown')}}</th>
                    </tr>
                    <tr>
                        <th style="width: 20px;">B</th>
                        <th style="width: 20px;">+</th>
                        <th style="width: 20px;">-</th>
                        <th style="width: 20px;">B</th>
                        <th style="width: 20px;">+</th>
                        <th style="width: 20px;">-</th>
                        <th style="width: 20px;">B</th>
                        <th style="width: 20px;">+</th>
                        <th style="width: 20px;">-</th>
                    </tr>

                    </thead>
                    <tbody>
                    @foreach( $votesByNb as $vote)
                        <tr>
                            <th>{{$vote->title}}</th>
                            <th>{{trans('empaville.'.$vote->geo_area)}}</th>
                            <th>{{isset($vote->total)?$vote->total:0}}</th>
                            <th>{{$vote->Uptown->balance}}</th>
                            <th>{{$vote->Uptown->positives}}</th>
                            <th>{{$vote->Uptown->negatives}}</th>
                            <th>{{$vote->Middletown->balance}}</th>
                            <th>{{$vote->Middletown->positives}}</th>
                            <th>{{$vote->Middletown->negatives}}</th>
                            <th>{{$vote->Downtown->balance}}</th>
                            <th>{{$vote->Downtown->positives}}</th>
                            <th>{{$vote->Downtown->negatives}}</th>
                        </tr>
                    @endforeach
                    </tbody>

                </table>
            </div>
        </div>
    </div>

    <div class="row">
        {{--CountByNb--}}
        <div class="col-md-4">
            <div class="box box-success">
                <div class="box-header">
                    <h3 class="box-title"><i class="fa"></i> {{trans('empaville.count_total_votes_by_nb')}}</h3>
                </div>
                <div class="box-body">
                    <div class="col-md-11">
                        <canvas id="countByNb" style="height:230px"></canvas>
                    </div>
                </div>
            </div>
        </div>
        {{--FirstByNb--}}
        <div class="col-md-4">
            <div class="box box-success">
                <div class="box-header">
                    <h3 class="box-title"><i class="fa"></i> {{trans('empaville.count_first_by_nb')}}</h3>
                </div>
                <div class="box-body">
                    <div class="col-md-11">
                        <canvas id="firstByNb" style="height:230px"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{--SecondByNb--}}
        <div class="col-md-4">
            <div class="box box-success">
                <div class="box-header">
                    <h3 class="box-title"><i class="fa"></i> {{trans('empaville.count_second_by_nb')}}</h3>
                </div>
                <div class="box-body">
                    <div class="col-md-11">
                        <canvas id="secondByNb" style="height:230px"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="box box-success text-center">
        <div class="box-header with-border">
            <h3 class="box-title"><i class="fa"></i> {{trans('empaville.proposalByNb')}}</h3>
        </div>
        <div class="box-body">
            <div class="col-md-12" style="height: 600px">
                <div id="proposals_votes"></div>
            </div>

        </div>
    </div>

    <div class="box box-success text-center">
        <div class="box-header with-border">
            <h3 class="box-title"><i class="fa"></i> {{trans('empaville.chart_by_middletown')}}</h3>
        </div>
        <div class="box-body">
            <div class="col-md-12" style="height: 600px">
                <div id="proposal_Middletown"></div>

            </div>

        </div>
    </div>





@endsection

@section('scripts')

    {{--Data for Middletown Votes--}}
    <script>
        var dataMidTownVotes = [
            {"Vote": "Positives", "geoArea": "{{trans('empaville.Uptown')}}", "votes": {{$data["Middletown"]["Uptown"]["positives"]}} },
            {"Vote": "Positives", "geoArea": "{{trans('empaville.Middletown')}}", "votes": {{$data["Middletown"]["Middletown"]["positives"]}} },
            {"Vote": "Positives", "geoArea": "{{trans('empaville.Downtown')}}", "votes": {{$data["Middletown"]["Downtown"]["positives"]}} },
            {"Vote": "Negatives", "geoArea": "{{trans('empaville.Uptown')}}", "votes": {{$data["Middletown"]["Uptown"]["negatives"]}} },
            {"Vote": "Negatives", "geoArea": "{{trans('empaville.Middletown')}}", "votes":  {{$data["Middletown"]["Middletown"]["negatives"]}} },
            {"Vote": "Negatives", "geoArea": "{{trans('empaville.Downtown')}}", "votes": {{$data["Middletown"]["Downtown"]["negatives"]}} }
        ];
        var attributes = [
            {"Vote":"Positives", "hex": "#006600"},
            {"Vote":"Negatives" , "hex": "#ff0000"}
        ];
        var visualization = d3plus.viz()
                .container("#proposal_Middletown")
                .data(dataMidTownVotes)
                .id(["Vote", "geoArea"])
                .size("votes")
                .type("radar")
                .attrs(attributes)
                .color("hex")
                .resize(true)
                .draw();


        // sample data array
        var data_Proposals_Uptown = [
                @foreach( $votesByNb as $vote)
            {"value": {{$vote->Uptown->balance}}, "name": "{{$vote->title}}", "group": "{{trans('empaville.Uptown')}}"},
            {"value": {{$vote->Middletown->balance}}, "name": "{{$vote->title}}", "group": "{{trans('empaville.Middletown')}}"},
            {"value": {{$vote->Downtown->balance}}, "name": "{{$vote->title}}", "group": "{{trans('empaville.Downtown')}}"},
            @endforeach
        ];
        var visualization = d3plus.viz()
                .container("#proposals_votes")     // container DIV to hold the visualization
                .data(data_Proposals_Uptown)     // data to use with the visualization
                .type("bubbles")       // visualization type
                .id(["group", "name"]) // nesting keys
                .depth(1)              // 0-based depth
                .size("value")         // key name to size bubbles
                .color("group")        // color by each group
                .resize(true)
                .draw();

    </script>





    <script>

        $(document).ready(function() {
            $('#proposals_list').DataTable({
                "paging":   false,
                "info":     false,
                "bFilter" :  false
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
            //- CountByNb -
            //--------------
            // Get context with jQuery - using jQuery's .get() method.
            var countByNb = $("#countByNb").get(0).getContext("2d");
            // This will get the first returned node in the jQuery collection.
            var countByNbBarChart = new Chart(countByNb);

            var countByNb = {
                labels: ["Uptown", "{{trans('empaville.Middletown')}}", "{{trans('empaville.Downtown')}}"],
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
                            {!! empty($countByNb->Uptown->Positives)? 0 : $countByNb->Uptown->Positives!!},
                            {!! empty($countByNb->Middletown->Positives)? 0 : $countByNb->Middletown->Positives!!},
                            {!! empty($countByNb->Downtown->Positives)? 0 : $countByNb->Downtown->Positives !!}
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
                            {!! empty($countByNb->Uptown->Negatives)? 0 : $countByNb->Uptown->Negatives!!},
                            {!! empty($countByNb->Middletown->Negatives)? 0 : $countByNb->Middletown->Negatives!!},
                            {!! empty($countByNb->Downtown->Negatives)? 0 : $countByNb->Downtown->Negatives !!}
                        ]
                    }
                ]
            };

            countByNbBarChart.Bar(countByNb, barChartOptions);


            //--------------
            //- FirstByGender -
            //--------------
            // Get context with jQuery - using jQuery's .get() method.
            var firstByNb = $("#firstByNb").get(0).getContext("2d");
            // This will get the first returned node in the jQuery collection.
            var firstByNbBarChart = new Chart(firstByNb);

            var firstByNb = {
                labels: ["{{trans('empaville.Uptown')}}", "{{trans('empaville.Middletown')}}", "{{trans('empaville.Downtown')}}"],
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
                            {!! empty($firstByNb->Uptown->Positives)? 0 : $firstByNb->Uptown->Positives!!},
                            {!! empty($firstByNb->Middletown->Positives)? 0 : $firstByNb->Middletown->Positives!!},
                            {!! empty($firstByNb->Downtown->Positives)? 0 : $firstByNb->Downtown->Positives !!}
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
                            {!! empty($firstByNb->Uptown->Negatives)? 0 : $firstByNb->Uptown->Negatives!!},
                            {!! empty($firstByNb->Middletown->Negatives)? 0 : $firstByNb->Middletown->Negatives!!},
                            {!! empty($firstByNb->Downtown->Negatives)? 0 : $firstByNb->Downtown->Negatives !!}
                        ]
                    }
                ]
            };
            firstByNbBarChart.Bar(firstByNb, barChartOptions);

            //--------------
            //- SecondByGNb -
            //--------------
            // Get context with jQuery - using jQuery's .get() method.
            var secondByNb = $("#secondByNb").get(0).getContext("2d");
            // This will get the first returned node in the jQuery collection.
            var secondByNbBarChart = new Chart(secondByNb);

            var secondByNb = {
                labels: ["{{trans('empaville.Uptown')}}", "{{trans('empaville.Middletown')}}", "{{trans('empaville.Downtown')}}"],
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
                            {!! empty($secondByNb->Uptown->Positives)? 0 : $secondByNb->Uptown->Positives!!},
                            {!! empty($secondByNb->Middletown->Positives)? 0 : $secondByNb->Middletown->Positives!!},
                            {!! empty($secondByNb->Downtown->Positives)? 0 : $secondByNb->Downtown->Positives !!}
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
                            {!! empty($secondByNb->Uptown->Negatives)? 0 : $secondByNb->Uptown->Negatives!!},
                            {!! empty($secondByNb->Middletown->Negatives)? 0 : $secondByNb->Middletown->Negatives!!},
                            {!! empty($secondByNb->Downtown->Negatives)? 0 : $secondByNb->Downtown->Negatives !!}
                        ]
                    }
                ]
            };
            secondByNbBarChart.Bar(secondByNb, barChartOptions);


        });

    </script>
@endsection