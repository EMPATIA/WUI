<!-- Download -->
<div class="chart-download-wrapper">
    <a id="downloadCSV"  class="btn btn-flat btn-blue pull-right">
        <i class="fa fa-file-excel-o" aria-hidden="true"></i> {{ trans('privateCbsVoteAnalysis.download_csv') }}
    </a>
</div>

<!-- Chart -->
<div id="statistics_by_last_day" style="height: 300px;margin-bottom:30px;" class="default-padding">
</div>

<script>
    // Data
    var statistics_by_last_day = [
        @foreach($votesLastDay->total as $hour => $voteValue)
            {'{!! trim(preg_replace('/\s\s+/', ' ',trans('privateCbsVoteAnalysis.hour'))) !!}': '{!! $hour !!}',
             "name":'{!! trans('privateCbsVoteAnalysis.all_votes') !!}',
             '{!! trim(preg_replace('/\s\s+/', ' ',trans('privateCbsVoteAnalysis.votes'))) !!}': {{ $voteValue }} },
        @endforeach
    ];

    // Setting Y range - bug fix when values are 0's
    var chartRange = [0, 10];
    for(var i = 0 ; i< statistics_by_last_day.length; i++ ){
        if(statistics_by_last_day[i].{!! trim(preg_replace('/\s\s+/', ' ',trans('privateCbsVoteAnalysis.votes'))) !!} != 0){
            chartRange = false;
        }
    }

    // D3 plus chart rendering
    d3plus.viz()
        .container("#statistics_by_last_day")
        .data(statistics_by_last_day)
        .type("line")
        .id("name")
        .order({"sort": "asc"})
        .y({
            "value": "{!! trim(preg_replace('/\s\s+/', ' ',trans('privateCbsVoteAnalysis.votes'))) !!}",
            "range": chartRange
        })
        .x('{!! trim(preg_replace('/\s\s+/', ' ',trans('privateCbsVoteAnalysis.hour'))) !!}')
        .font({"family": "Helvetica, Arial, sans-serif", "color": "#000"})
        .resize(true)
        .draw();

    // Error message if there are no results in Vote Analysis  :'(
    if(statistics_by_last_day.length == 0){
        var warningMessage = "{!! trim(preg_replace('/\s\s+/', ' ', trans('privateCbsVoteAnalysis.no_data_Available'))) !!}";
        $('#statistics_by_last_day').html('<div class="chartMessage"><div><i class="fa fa-eye-slash" aria-hidden="true"></i> '+warningMessage+'</div></div>');
        toastr.warning(warningMessage);
        $(".chart-download-wrapper").remove();
    } else {
        // Export data for CSV (javascript)
        $( "#downloadCSV" ).click(function() {
            var d = new Date();
            var suffix_name = d.getFullYear()+"_"+(1+d.getMonth())+"_"+d.getDate()+"_"+d.getHours()+"_"+d.getMinutes()+"_"+d.getSeconds();
            var filename = "statistics_by_last_day_"+suffix_name+".csv";
            downloadCSV(statistics_by_last_day,filename);
        });
    }

</script>

@include('private.cbs.cbVoteAnalysis.cbDetailsScript')