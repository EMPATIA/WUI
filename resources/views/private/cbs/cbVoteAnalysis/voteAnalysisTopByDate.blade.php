<!-- Download -->
<div id="statisticsTopByDateDownloadCSV-chart-download-wrapper" class="chart-download-wrapper">
    <a id="statisticsTopByDateDownloadCSV" class="btn btn-flat btn-blue pull-right">
        <i class="fa fa-file-excel-o" aria-hidden="true"></i> {{ trans('privateCbsVoteAnalysis.download_csv') }}
    </a>
</div>

<!-- Chart -->
<div id="statistics_top_by_date" style="height: 300px" class="default-padding">
</div>


<script>
    var statistics_top_by_date_data = [
        @foreach($votesTopByDate->balance as $voteTop)
            @foreach($voteTop->votes as $date => $voteValue)
                {'{!! trans('privateCbsVoteAnalysis.date') !!}': "{{ $date }}", "name":'{!! $voteTop->topic_name !!}', '{!! trans('privateCbsVoteAnalysis.votes') !!}': {{ $voteValue }} },
            @endforeach
        @endforeach
    ];

    // Setting Y range - bug fix when values are 0's
    var chartRange = [0, 10];
    for(var i = 0 ; i< statistics_top_by_date_data.length; i++ ){
        if(statistics_top_by_date_data[i].{!! trim(preg_replace('/\s\s+/', ' ',trans('privateCbsVoteAnalysis.votes'))) !!} != 0){
            chartRange = false;
        }
    }

    var visualization = d3plus.viz()
        .container("#statistics_top_by_date")
        .data(statistics_top_by_date_data)
        .type("line")
        .id("name")
        .y({
            "value": "{!! trans('privateCbsVoteAnalysis.votes') !!}",
            "range": chartRange
        })
        .x('{!! trans('privateCbsVoteAnalysis.date') !!}')
        .color({
            "value": "name"
        })
        .legend({
            "value": true,
            "size": 50
        })
        .font({"family": "Helvetica, Arial, sans-serif", "color": "#000"}).resize(true)
        .draw();

    // Error message if there are no results in Vote Analysis  :'(
    if(statistics_top_by_date_data.length == 0){
        var warningMessage = "{!! trim(preg_replace('/\s\s+/', ' ', trans('privateCbsVoteAnalysis.no_data_Available'))) !!}";
        $('#statistics_top_by_date').html('<div class="chartMessage"><div><i class="fa fa-eye-slash" aria-hidden="true"></i> '+warningMessage+'</div></div>');
        toastr.warning(warningMessage);
        $("#statisticsTopByDateDownloadCSV-chart-download-wrapper").remove();
    } else {
        // Export data for CSV (javascript)
        $( "#statisticsTopByDateDownloadCSV" ).click(function() {
            var d = new Date();
            var suffix_name = d.getFullYear()+"_"+(1+d.getMonth())+"_"+d.getDate()+"_"+d.getHours()+"_"+d.getMinutes()+"_"+d.getSeconds();
            var filename = "statistics_top_by_date_"+suffix_name+".csv";
            downloadCSV(statistics_top_by_date_data, filename);
        });
    }

</script>

@include('private.cbs.cbVoteAnalysis.cbDetailsScript')