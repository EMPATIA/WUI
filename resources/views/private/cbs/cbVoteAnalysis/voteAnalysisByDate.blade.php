<!-- Download -->
<div class="chart-download-wrapper">
    <a id="downloadCSV"  class="btn btn-flat btn-blue pull-right">
        <i class="fa fa-file-excel-o" aria-hidden="true"></i> {{ trans('privateCbsVoteAnalysis.download_csv') }}
    </a>
</div>

<!-- Chart -->
<div id="statistics_by_date" style="height: 300px" class="default-padding">
</div>

<script>

        // Data
        var statistics_by_date_data = [
            @foreach($votesByDate->total->all_votes as $date => $voteValue)
            {
                '{!! trans('privateCbsVoteAnalysis.date') !!}': "{{ $date }}",
                "name": '{!! trans('privateCbsVoteAnalysis.all_votes') !!}',
                '{!! trans('privateCbsVoteAnalysis.votes') !!}': {{ number_format($voteValue, 3, '.', ',') }}
            },
            @endforeach
        ];

        // Setting Y range - bug fix when values are 0's
        var chartRange = [0, 10];
        for(var i = 0 ; i< statistics_by_date_data.length; i++ ){
            if(statistics_by_date_data[i].{!! trim(preg_replace('/\s\s+/', ' ',trans('privateCbsVoteAnalysis.votes'))) !!} != 0){
                chartRange = false;
            }
        }

        // Chart D3plus
        d3plus.viz()
            .container("#statistics_by_date")
            .data(statistics_by_date_data)
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
        if(statistics_by_date_data.length == 0){
            var warningMessage = "{!! trim(preg_replace('/\s\s+/', ' ', trans('privateCbsVoteAnalysis.no_data_Available'))) !!}";
            $('#statistics_by_date').html('<div class="chartMessage"><div><i class="fa fa-eye-slash" aria-hidden="true"></i> '+warningMessage+'</div></div>');
            toastr.warning(warningMessage);
            $(".chart-download-wrapper").remove();
        } else {
            // Export data for CSV (javascript)
            $( "#downloadCSV" ).click(function() {
                var d = new Date();
                var suffix_name = d.getFullYear()+"_"+(1+d.getMonth())+"_"+d.getDate()+"_"+d.getHours()+"_"+d.getMinutes()+"_"+d.getSeconds();
                var filename = "statistics_by_date_"+suffix_name+".csv";
                downloadCSV(statistics_by_date_data,filename);
            });
        }


</script>