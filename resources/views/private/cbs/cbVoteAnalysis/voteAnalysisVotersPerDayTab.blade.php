    <!-- Download -->
    <div id="statistics_voters_per_day_chart-download-wrapper" class="chart-download-wrapper">
        <a id="voters_per_day_DownloadCSV" class="btn btn-flat btn-blue pull-right">
            <i class="fa fa-file-excel-o" aria-hidden="true"></i> {{ trans('privateCbsVoteAnalysis.download_csv') }}
        </a>
    </div>
    <!-- Chart d3plus -->
    <div id="statistics_voters_per_day" style="height: 300px">
    </div>

    @if(!empty($votersPerDate))
        <script>
            $( document ).ready(function() {
                // Data
                var statistics_voters_per_day_data = [
                    @foreach(!empty($votersPerDate->total->all_votes) ? $votersPerDate->total->all_votes : [] as $date => $voteValue)
                    {
                        '{!! trans('privateCbsVoteAnalysis.date') !!}': "{{ $date }}",
                        "name": '{!! trans('privateCbsVoteAnalysis.voters_per_day') !!}',
                        '{!! trans('privateCbsVoteAnalysis.votes') !!}': {{ $voteValue }} },
                    @endforeach

                ];

                // Setting Y range - bug fix when values are 0's
                var chartRange = [0, 10];
                for(var i = 0 ; i< statistics_voters_per_day_data.length; i++ ){
                    if(statistics_voters_per_day_data[i].{!! trim(preg_replace('/\s\s+/', ' ',trans('privateCbsVoteAnalysis.votes'))) !!} != 0){
                        chartRange = false;
                    }
                }

                // Chart d3plus
                var visualization = d3plus.viz()
                    .container("#statistics_voters_per_day")
                    .data(statistics_voters_per_day_data)
                    .type("line")
                    .id("name")
                    .y({
                        "value": "{!! trans('privateCbsVoteAnalysis.votes') !!}",
                        "range": chartRange
                    })
                    .x('{!! trans('privateCbsVoteAnalysis.date') !!}')
                    .font({"family": "Helvetica, Arial, sans-serif", "color": "#000"})
                    .resize(true)
                    .draw();

                // Error message if there are no results in Vote Analysis  :'(
                if(statistics_voters_per_day_data.length == 0){
                    var warningMessage = "{!! trim(preg_replace('/\s\s+/', ' ', trans('privateCbsVoteAnalysis.no_data_Available'))) !!}";
                    $('#statistics_voters_per_day').html('<div class="chartMessage"><div><i class="fa fa-eye-slash" aria-hidden="true"></i> '+warningMessage+'</div></div>');
                    toastr.warning(warningMessage);
                    $("#statistics_voters_per_day_chart-download-wrapper").remove();
                } else {
                    // Export data for CSV (javascript)
                    $( "#voters_per_day_DownloadCSV" ).click(function() {
                        var d = new Date();
                        var suffix_name = d.getFullYear()+"_"+(1+d.getMonth())+"_"+d.getDate()+"_"+d.getHours()+"_"+d.getMinutes()+"_"+d.getSeconds();
                        var filename = "statistics_voters_per_day_data"+suffix_name+".csv";
                        downloadCSV(statistics_voters_per_day_data, filename);
                    });
                }

            });
        </script>
    @endif


    @include('private.cbs.cbVoteAnalysis.cbDetailsScript')