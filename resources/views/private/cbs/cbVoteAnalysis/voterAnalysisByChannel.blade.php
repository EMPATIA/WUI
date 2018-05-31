@if(empty($votesByChannel) && empty($countByChannel))
    <div class="chartMessage">
        <div><i class="fa fa-eye-slash" aria-hidden="true"></i> {{trans('privateCbsVoteAnalysis.no_data_available')}}</div>
    </div>
@else
    <div class="box-info">
        <div class="box-header">
            <div class="row">
                <div class="col-12 col-lg-12">
                    <h3 class="box-title"><i class="fa"></i> {{ trans('privateCbsVoteAnalysis.channels') }}</h3>
                </div>
            </div>
        </div>
        <div class="box-body">
            <table id="voters_list" class="table table-responsive table-hover order-column hover">
                <thead>
                <tr>
                    <th>{{ trans('privateCbsVoteAnalysis.title') }}</th>
                    <th style="width: 20px;">{{ trans('privateCbsVoteAnalysis.totals') }}</th>
                    @foreach($channels as $channel)
                        <th>{{ trans('privateCbsVoteAnalysis.'.$channel) }}</th>
                    @endforeach
                </tr>
                </thead>
                <tbody>
                @foreach( $votesByChannel as $vote)
                    <tr>
                        <td>{{$vote->title}}</td>
                        <td>{{ $vote->total ?? 0 }}</td>
                        @foreach( $channels as $channel)
                            <td>{{ $vote->channels->{$channel} ?? 0 }}</td>
                        @endforeach
                    </tr>
                @endforeach
                </tbody>

            </table>
        </div>
    </div>

    <div class="default-padding">
        <div class="row">
            {{--countVotersByChannel--}}
            <div class="col-12 col-sm-12">
                <div class="card">
                    <div class="box-header">
                        <span class="chart-box-title default-color">{{trans('privateCbsVoteAnalysis.count_total_voters_by_channel')}}</span>
                        <!-- Download -->
                        <span id="countVotersByChannel_downloads_wrapper" class="chart-download-wrapper">
                        <a id="countVotersByChannelDownloadCSV"  class="btn btn-flat btn-blue pull-right">
                            <i class="fa fa-file-excel-o" aria-hidden="true"></i> {{ trans('privateCbsVoteAnalysis.download_csv') }}
                        </a>
                    </span>
                    </div>
                    <div class="box-body">
                        <div class="col-md-11">
                            <canvas id="countVotersByChannel" style="height:230px"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            var table = $('#voters_list').DataTable({
                "paging":   false,
                "info":     false,
                "bFilter" :  false
            } );

            $('#voters_list tbody')
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
            //- countVotersByChannel -
            //--------------
            // Get context with jQuery - using jQuery's .get() method.
            var countVotersByChannel = $("#countVotersByChannel").get(0).getContext("2d");
            // This will get the first returned node in the jQuery collection.
            var countVotersByChannelBarChart = new Chart(countVotersByChannel);

            var countVotersByChannel = {

                labels: [
                    @foreach($channels as $channel)
                        "{{ trans('privateCbsVoteAnalysis.'.$channel) }}",
                    @endforeach
                ],
                datasets: [
                    {
                        label: "Voters",
                        fillColor: "rgba(0, 255, 0, 1)",
                        strokeColor: "rgba(0, 255, 0, 1)",
                        pointColor: "rgba(0, 255, 0, 1)",
                        pointStrokeColor: "#c1c7d1",
                        pointHighlightFill: "#fff",
                        pointHighlightStroke: "rgba(220,220,220,1)",
                        data:[
                            @foreach($channels as $channel)
                            {{  $countByChannel->{$channel} ?? 0 }},
                            @endforeach
                        ]
                    }
                ]
            };

            countVotersByChannelBarChart.Bar(countVotersByChannel, barChartOptions);

            // Export data for CSV (javascript)
            $( "#countVotersByChannelDownloadCSV" ).click(function() {
                // countVotersByChannel_data
                var countVotersByChannel_data = [
                        @foreach($channels as $channel)
                    {
                        "{!! trans('privateCbsVoteAnalysis.channel') !!}":"{{ trans('privateCbsVoteAnalysis.'.$channel) }}",
                        "voters": {{ $countByChannel->{$channel} ?? 0 }}
                    },
                    @endforeach
                ];
                var d = new Date();
                var suffix_name = d.getFullYear()+"_"+(1+d.getMonth())+"_"+d.getDate()+"_"+d.getHours()+"_"+d.getMinutes()+"_"+d.getSeconds();
                var filename = "total_count_by_channel_"+suffix_name+".csv";
                downloadCSV(countVotersByChannel_data,filename);
            });
        });

    </script>
@endif

@include('private.cbs.cbVoteAnalysis.cbDetailsScript')