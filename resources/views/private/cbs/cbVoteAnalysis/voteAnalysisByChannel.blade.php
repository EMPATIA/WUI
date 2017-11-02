@if(empty($votesByChannel) && empty($countByChannel) && empty($firstByChannel) && empty($secondByChannel))
    <div class="chartMessage">
        <div><i class="fa fa-eye-slash" aria-hidden="true"></i> {{trans('privateCbsVoteAnalysis.no_data_available')}}</div>
    </div>
@else
    <div class="box-info">
        <div class="box-header">
            <h3 class="box-title"><i class="fa"></i> {{ trans('privateCbsVoteAnalysis.channels') }}</h3>
        </div>
        <div class="box-body">
            <table id="proposals_list" class="table table-responsive table-hover order-column hover">
                <thead>
                <tr>
                    <th rowspan="2">{{ trans('privateCbsVoteAnalysis.title') }}</th>
                    <th rowspan="2" style="width: 20px;">{{ trans('privateCbsVoteAnalysis.totals') }}</th>
                    @foreach($channels as $channel)
                        <th colspan="3">{{ trans('privateCbsVoteAnalysis.'.$channel) }}</th>
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
                        <td>{{$vote->title}}</td>
                        <td>{{ $vote->total ?? 0 }}</td>
                        @foreach( $channels as $channel)
                            <td>{{ $vote->channels->{$channel}->balance ?? 0 }}</td>
                            <td>{{ $vote->channels->{$channel}->positives ?? 0 }}</td>
                            <td>{{ $vote->channels->{$channel}->negatives ?? 0 }}</td>
                        @endforeach
                    </tr>
                @endforeach
                </tbody>

            </table>
        </div>
    </div>

    <div class="default-padding">
        <div class="row">
        {{--CountByGender--}}
        <div class="col-12 col-sm-12 col-md-4">
            <div class="card">
                <div class="box-header">
                    <span class="chart-box-title default-color">{{trans('privateCbsVoteAnalysis.count_total_votes_by_channel')}}</span>
                    <!-- Download -->
                    <span id="countByChannel_downloads_wrapper" class="chart-download-wrapper">
                        <a id="countByChannelDownloadCSV"  class="btn btn-flat btn-blue pull-right">
                            <i class="fa fa-file-excel-o" aria-hidden="true"></i> {{ trans('privateCbsVoteAnalysis.download_csv') }}
                        </a>
                    </span>
                </div>
                <div class="box-body">
                    <div class="col-md-11">
                        <canvas id="countByChannel" style="height:230px"></canvas>
                    </div>
                </div>
            </div>
        </div>
        {{--FirstByGender--}}
        <div class="col-12 col-sm-12 col-md-4">
            <div class="card">
                <div class="box-header">
                    <span class="chart-box-title default-color">{{trans('privateCbsVoteAnalysis.count_first_by_channel')}}</span>
                    <!-- Download -->
                    <span id="firstByChannel_downloads_wrapper" class="chart-download-wrapper">
                        <a id="firstByChannelDownloadCSV"  class="btn btn-flat btn-blue pull-right">
                            <i class="fa fa-file-excel-o" aria-hidden="true"></i> {{ trans('privateCbsVoteAnalysis.download_csv') }}
                        </a>
                    </span>
                </div>
                <div class="box-body">
                    <div class="col-md-11">
                        <canvas id="firstByChannel" style="height:230px"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{--SecondByGender--}}
        <div class="col-12 col-sm-12 col-md-4">
            <div class="card">
                <div class="box-header">
                    <span class="chart-box-title default-color">{{trans('privateCbsVoteAnalysis.count_second_by_channel')}}</span>
                    <!-- Download -->
                    <span id="secondByChannel_downloads_wrapper">
                        <a id="secondByChannelDownloadCSV" class="btn btn-flat btn-blue pull-right">
                            <i class="fa fa-file-excel-o" aria-hidden="true"></i> {{ trans('privateCbsVoteAnalysis.download_csv') }}
                        </a>
                    </span>
                </div>
                <div class="box-body">
                    <div class="col-md-11">
                        <canvas id="secondByChannel" style="height:230px"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>

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
                        "{{ trans('privateCbsVoteAnalysis.'.$channel) }}",
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
                            {{  $countByChannel->{$channel}->positives ?? 0 }},
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
                            {{ $countByChannel->{$channel}->negatives ?? 0 }},
                            @endforeach
                        ]
                    }
                ]
            };

            countByChannelBarChart.Bar(countByChannel, barChartOptions);

            // Export data for CSV (javascript)
            $( "#countByChannelDownloadCSV" ).click(function() {
                // countByChannel_data
                var countByChannel_data = [
                    @foreach($channels as $channel)
                        {
                            "{!! trans('privateCbsVoteAnalysis.channel') !!}":"{{ trans('privateCbsVoteAnalysis.'.$channel) }}",
                            "positives": {{ $countByChannel->{$channel}->positives ?? 0 }},
                            "negatives": {{ $countByChannel->{$channel}->negatives ?? 0 }}
                        },
                    @endforeach
                ];
                var d = new Date();
                var suffix_name = d.getFullYear()+"_"+(1+d.getMonth())+"_"+d.getDate()+"_"+d.getHours()+"_"+d.getMinutes()+"_"+d.getSeconds();
                var filename = "total_count_by_channel_"+suffix_name+".csv";
                downloadCSV(countByChannel_data,filename);
            });


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
                        "{{ trans('privateCbsVoteAnalysis.'.$channel) }}",
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
                            {{ $firstByChannel->{$channel}->positives ?? 0 }},
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
                            {{ $firstByChannel->{$channel}->negatives ?? 0 }},
                            @endforeach
                        ]
                    }
                ]
            };
            firstByChannelBarChart.Bar(firstByChannel, barChartOptions);

            // Export data for CSV (javascript)
            $( "#firstByChannelDownloadCSV" ).click(function() {
                // countByChannel_data
                var firstByChannel_data = [
                    @foreach($channels as $channel)
                    {
                        "{!! trans('privateCbsVoteAnalysis.channel') !!}": "{{ trans('privateCbsVoteAnalysis.'.$channel) }}",
                        "positives": {{ $firstByChannel->{$channel}->positives ?? 0 }},
                        "negatives": {{ $firstByChannel->{$channel}->negatives ?? 0 }}
                    },
                    @endforeach
                ];
                var d = new Date();
                var suffix_name = d.getFullYear()+"_"+(1+d.getMonth())+"_"+d.getDate()+"_"+d.getHours()+"_"+d.getMinutes()+"_"+d.getSeconds();
                var filename = "first_vote_by_channel_"+suffix_name+".csv";
                downloadCSV(firstByChannel_data,filename);
            });

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
                        "{{ trans('privateCbsVoteAnalysis.'.$channel) }}",
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
                            {{ $secondByChannel->{$channel}->positives ?? 0 }},
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
                            {{ $secondByChannel->{$channel}->negatives ?? 0 }},
                            @endforeach
                        ]
                    }
                ]
            };
            secondByChannelBarChart.Bar(secondByChannel, barChartOptions);

            // Export data for CSV (javascript)
            $( "#secondByChannelDownloadCSV" ).click(function() {
                // countByChannel_data
                var secondByChannel_data = [
                    @foreach($channels as $channel)
                    {
                        "{!! trans('privateCbsVoteAnalysis.channel') !!}": "{{ trans('privateCbsVoteAnalysis.'.$channel) }}",
                        "positives": {{ $firstByChannel->{$channel}->positives ?? 0 }},
                        "negatives": {{ $firstByChannel->{$channel}->negatives ?? 0 }}
                    },
                    @endforeach
                ];
                var d = new Date();
                var suffix_name = d.getFullYear()+"_"+(1+d.getMonth())+"_"+d.getDate()+"_"+d.getHours()+"_"+d.getMinutes()+"_"+d.getSeconds();
                var filename = "second_vote_by_channel_"+suffix_name+".csv";
                downloadCSV(secondByChannel_data,filename);
            });
        });

    </script>
@endif
