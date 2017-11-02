@extends('private._private.index')


@section('content')
        <!-- Charts Filter -->
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header">
                    <h3 class="box-title">{!! trans("privateDashboardVotes.Filters")!!}</h3>
                </div>
                <div class="box-body">
                    <select id="filterProposal" name="filterProposal" class="form-control">
                        <option value=""> -- {!! trans("privateDashboardVotes.selectProposal")!!} -- </option>
                        @foreach( $ideas as $idea)
                            <option value="{{ $idea->id }}" @if(!empty($cbId) && $idea->id == $cbId) selected @endif  >{{ $idea->title }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts -->
    @if(!empty($voteSessions))
        @foreach($voteSessions as $key => $data)
            <div class="row">
                <section class="col-lg-6 connectedSortable ui-sortable">
                    {{--TOTAL VOTES INFORMATION--}}
                    <div class="col-md-12">
                        <div class="box box-primary">
                            <div class="box-header">
                                {{trans('privateDashboardVotes.countTotalVotes')}}
                            </div>
                            <div class="box-body">
                                <div class="col-sm-3 text-center">
                                    <div class="row">
                                        {{trans('privateDashboardVotes.totalVotes')}}
                                    </div>
                                    <div class="row">
                                        <img src="/images/total_votes.png"  >
                                    </div>
                                    <div class="row">
                                        {{$data['summary']->total}}
                                    </div>
                                </div>
                                <div class="col-sm-3 text-center">
                                    <div class="row">
                                        {{trans('privateDashboardVotes.totalPositiveVotes')}}
                                    </div>
                                    <div class="row">
                                        <img src="/images/positive_votes.png"  >
                                    </div>
                                    <div class="row">
                                        {{$data['summary']->total_positives}}
                                    </div>
                                </div>
                                <div class="col-sm-3 text-center">
                                    <div class="row">
                                        {{trans('privateDashboardVotes.totalNegativeVotes')}}
                                    </div>
                                    <div class="row">
                                        <img src="/images/negative_votes.png" >
                                    </div>
                                    <div class="row">
                                        {{$data['summary']->total_negatives}}
                                    </div>
                                </div>
                                <div class="col-sm-3 text-center">
                                    <div class="row">
                                        {{trans('privateDashboardVotes.totalVoters')}}
                                    </div>
                                    <div class="row">
                                        <img src="/images/total_voters.png" >
                                    </div>
                                    <div class="row">
                                        {{$data['summary']->total_users_voted}}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{--END TOTAL VOTES INFORMATION--}}

                    {{--TOP 10--}}
                    <div class="col-md-12">
                        <div class="box box-primary">
                            <div class="box-header">
                                <h3 class="box-title">{{trans('privateDashboardVotes.topTen')}}</h3>
                            </div>
                            <div class="box-body no-padding">
                                <table class="table table-condensed table-responsive">
                                    <tbody>
                                    <tr>
                                        <th style="width: 10px">#</th>
                                        <th>{{trans('privateDashboardVotes.proposal')}}</th>
                                        <th>{{trans('privateDashboardVotes.balance')}}</th>
                                        <th style="width: 40px"></th>
                                    </tr>
                                    @foreach($data["top"] as $key => $topProposal)
                                        <tr>
                                            <td>{{$key + 1 }} </td>
                                            <td>{{$topProposal->title}}</td>
                                            <td>
                                                <div class="progress progress-xs">
                                                    {{--CHANGE PERCENTAGE COUNTS TO CONTROLLER--}}
                                                    <div class="progress-bar progress-bar-success" style="width:{{( ($topProposal->balance * 100 )/$data['summary']->total_balance )}}%"></div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="label bg-green">{{( ($topProposal->balance * 100 )/$data['summary']->total_balance )}}%</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    {{--TOP 10 END--}}

                </section>

                <section class="col-lg-6 connectedSortable ui-sortable">
                    {{--CHART DAILY INFORMATION--}}
                    <div class="col-md-12">
                        <div class="box box-primary">
                            <div class="chart">
                                <canvas id="lineChart" style="height: 250px; width: 787px;" width="787" height="250"></canvas>
                            </div>
                        </div>
                    </div>
                </section>



            </div>


            <script>
                $( function () {
                    var areaChartData = {
                        labels:  [
                            @foreach( $data["daily"]["labels"] as $value)
                                    "{!! $value !!}",
                            @endforeach
                        ]
                        ,
                        datasets: [
                            {
                                label: "{{trans('dashboard.votes_positives')}}",
                                fillColor: "rgba(210, 214, 222, 1)",
                                strokeColor: "rgba(210, 214, 222, 1)",
                                pointColor: "rgba(210, 214, 222, 1)",
                                pointStrokeColor: "#c1c7d1",
                                pointHighlightFill: "#fff",
                                pointHighlightStroke: "rgba(220,220,220,1)",
                                data: [
                                    @foreach( $data["daily"]['positives'] as $value)
                                        {!! $value !!},
                                    @endforeach
                                ]

                            },
                            {
                                label: "{{trans('dashboard.votes_negatives')}}",
                                fillColor: "rgba(60,141,188,0.9)",
                                strokeColor: "rgba(60,141,188,0.8)",
                                pointColor: "#3b8bba",
                                pointStrokeColor: "rgba(60,141,188,1)",
                                pointHighlightFill: "#fff",
                                pointHighlightStroke: "rgba(60,141,188,1)",
                                data: [
                                    @foreach( $data["daily"]['negatives'] as $value)
                                        {!! $value !!},
                                    @endforeach
                                ]
                            }
                        ]
                    };

                    var areaChartOptions = {
                        //Boolean - If we should show the scale at all
                        showScale: true,
                        //Boolean - Whether grid lines are shown across the chart
                        scaleShowGridLines: false,
                        //String - Colour of the grid lines
                        scaleGridLineColor: "rgba(0,0,0,.05)",
                        //Number - Width of the grid lines
                        scaleGridLineWidth: 1,
                        //Boolean - Whether to show horizontal lines (except X axis)
                        scaleShowHorizontalLines: true,
                        //Boolean - Whether to show vertical lines (except Y axis)
                        scaleShowVerticalLines: true,
                        //Boolean - Whether the line is curved between points
                        bezierCurve: true,
                        //Number - Tension of the bezier curve between points
                        bezierCurveTension: 0.3,
                        //Boolean - Whether to show a dot for each point
                        pointDot: false,
                        //Number - Radius of each point dot in pixels
                        pointDotRadius: 4,
                        //Number - Pixel width of point dot stroke
                        pointDotStrokeWidth: 1,
                        //Number - amount extra to add to the radius to cater for hit detection outside the drawn point
                        pointHitDetectionRadius: 20,
                        //Boolean - Whether to show a stroke for datasets
                        datasetStroke: true,
                        //Number - Pixel width of dataset stroke
                        datasetStrokeWidth: 2,
                        //Boolean - Whether to fill the dataset with a color
                        datasetFill: true,
                        //String - A legend template
                          maintainAspectRatio: true,
                          //Boolean - whether to make the chart responsive to window resizing
                          responsive: true
                        };

                    //-------------
                    //- LINE CHART -
                    //--------------
                    var lineChartCanvas = $("#lineChart").get(0).getContext("2d");
                    var lineChart = new Chart(lineChartCanvas);
                    var lineChartOptions = areaChartOptions;
                    lineChartOptions.datasetFill = false;
                    lineChart.Line(areaChartData, lineChartOptions);

                });

            </script>
        @endforeach
    @endif
    <script>
        $( "#filterProposal" ).change(function() {
            location.href = "/private/dashboardVotes/proposals/"+$("#filterProposal").val();
        });
    </script>

@endsection