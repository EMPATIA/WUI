<!-- Download -->
<div id="statistics_by_top_chart-download-wrapper" class="chart-download-wrapper">
    <a id="statistics_by_top_DownloadCSV" class="btn btn-flat btn-blue pull-right">
        <i class="fa fa-file-excel-o" aria-hidden="true"></i> {{ trans('privateCbsVoteAnalysis.download_csv') }}
    </a>
</div>

<div id="statistics_by_top" style="height:300px;width: 100%;" class="default-padding">
</div>

<script>
    var statistics_by_top_data = [
        @foreach($votesByTop->balance as $vote)
            {"position": "{{ $vote->position }}", "{!! trans('privateCbsVoteAnalysis.topic_name') !!}":"{{ $vote->topic_name }}","{!! trans('privateCbsVoteAnalysis.total_votes') !!}": {{ $vote->total_votes }}},
        @endforeach
    ];

    var visualization = d3plus.viz()
        .container("#statistics_by_top")  // container DIV to hold the visualization
        .data(statistics_by_top_data)  // data to use with the visualization
        .type("bar")// visualization type
        .id("{!! trans('privateCbsVoteAnalysis.topic_name') !!}")
        .y("{!! trans('privateCbsVoteAnalysis.topic_name') !!}")         // key to use for y-axis
        .y({"scale": "discrete"}) // Manually set Y-axis to be discrete
        .x( "{!! trans('privateCbsVoteAnalysis.total_votes') !!}")// key to use for x-axis
        .order("position")
        .legend({
            "value": true,
            "size": 35
        })
        .font({"family": "Helvetica, Arial, sans-serif", "color": "#000"}).resize(true)
        .draw();


    // Error message if there are no results in Vote Analysis  :'(
    if(statistics_by_top_data.length == 0){
        var warningMessage = "{!! trim(preg_replace('/\s\s+/', ' ', trans('privateCbsVoteAnalysis.no_data_Available'))) !!}";
        $('#statistics_by_top').html('<div class="chartMessage"><div><i class="fa fa-eye-slash" aria-hidden="true"></i> '+warningMessage+'</div></div>');
        toastr.warning(warningMessage);
        $("#statistics_by_top_chart-download-wrapper").remove();
    } else {
        // Export data for CSV (javascript)
        $( "#statistics_by_top_DownloadCSV" ).click(function() {
            var d = new Date();
            var suffix_name = d.getFullYear()+"_"+(1+d.getMonth())+"_"+d.getDate()+"_"+d.getHours()+"_"+d.getMinutes()+"_"+d.getSeconds();
            var filename = "statistics_by_top_"+suffix_name+".csv";
            downloadCSV(statistics_by_top_data, filename);
        });
    }

</script>