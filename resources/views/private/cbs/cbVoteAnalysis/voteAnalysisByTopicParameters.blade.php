<div class="default-padding">
    <div class="row">
        <div class="col-12">
            <!-- Download -->
            <div class="chart-download-wrapper_{{$parameterKey}}">
                <a id="downloadCSV_{{$parameterKey}}"  class="btn btn-flat btn-blue pull-right">
                    <i class="fa fa-file-excel-o" aria-hidden="true"></i> {{ trans('privateCbsVoteAnalysis.download_csv') }}
                </a>
            </div>

            <div id="statistics_by_{{$parameterKey}}" style="height: 400px">
            </div>
        </div>
    </div>
</div>

@if(!empty($voteEventKey))
    <script>
        $( document ).ready(function() {
            var statistics_by_topic_parameters = [
                @foreach($topicParameters->total->all_votes as $date => $voteValue)
                {   '{!! trans('privateCbsVoteAnalysis.date') !!}': "{{ $date }}",
                    "name": '{!! trans('privateCbsVoteAnalysis.votes') !!}',
                    '{!! trans('privateCbsVoteAnalysis.votes') !!}': {{ number_format($voteValue, 3, '.', ',') }} },
                @endforeach
            ];

            // Setting Y range - bug fix when values are 0's
            var chartRange = [0, 10];
            for(var i = 0 ; i< statistics_by_topic_parameters.length; i++ ){
                if(statistics_by_topic_parameters[i].{!! trim(preg_replace('/\s\s+/', ' ',trans('privateCbsVoteAnalysis.votes'))) !!} != 0){
                    chartRange = false;
                }
            }

            var visualization = d3plus.viz()
                .container("#statistics_by_{{$parameterKey}}")
                .data(statistics_by_topic_parameters)
                .type("bar")
                .id("name")
                .y({
                    "value": "{!! trim(preg_replace('/\s\s+/', ' ',trans('privateCbsVoteAnalysis.votes'))) !!}",
                    "range": chartRange
                })
                .x('{!! trans('privateCbsVoteAnalysis.date') !!}')
                .font({"family": "Helvetica, Arial, sans-serif", "color": "#000"})
                .resize(true)
                .draw();

            // Error message if there are no results in Vote Analysis  :'(
            if(statistics_by_topic_parameters.length == 0){
                var warningMessage = "{!! trim(preg_replace('/\s\s+/', ' ', trans('privateCbsVoteAnalysis.no_data_Available'))) !!}";
                $('#statistics_by_{{$parameterKey}}').html('<div class="chartMessage"><div><i class="fa fa-eye-slash" aria-hidden="true"></i> '+warningMessage+'</div></div>');
                toastr.warning(warningMessage);
                $(".chart-download-wrapper_{{$parameterKey}}").remove();
            } else {
                // Export data for CSV (javascript)
                $( "#downloadCSV_{{$parameterKey}}" ).click(function() {
                    var d = new Date();
                    var suffix_name = d.getFullYear()+"_"+(1+d.getMonth())+"_"+d.getDate()+"_"+d.getHours()+"_"+d.getMinutes()+"_"+d.getSeconds();
                    var filename = "statistics_by_topic_parameters"+suffix_name+".csv";
                    downloadCSV(statistics_by_topic_parameters,filename);
                });
            }

        });
    </script>

@endif