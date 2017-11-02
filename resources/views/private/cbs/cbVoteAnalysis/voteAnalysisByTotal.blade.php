@if(empty($statisticsTotalData) && empty($statisticsTotalSummary))
    <div class="row">
        <div class="col-12 text-center">
            <h4>{{trans('privateCbsVoteAnalysis.no_data_available')}}</h4>
        </div>
    </div>
@else
    <div class="row">
        {{--TOTAL VOTES INFORMATION--}}
        <div class="col-md-12 voteAnalysis-total">
            <div class="">
                <div class="box-header">
                    <h3 class="box-title"><i class="fa"></i> {{trans('privateCbsVoteAnalysis.count_total_votes')}}</h3>
                </div>
                <div class="row box-body">
                    <div class="col-lg-3 col-md-6 col-sm-6 col-12 text-center">
                        <div class="">
                            <img src="{{asset('/images/total_voters.png')}}" style="width: 5em">
                        </div>
                        <div class="">
                            <strong>{{trans('privateCbsVoteAnalysis.total_voters')}}</strong>
                        </div>
                        <div class="">
                            {{ $statisticsTotalSummary->total_users_voted ?? null }}
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 col-sm-6 col-12 text-center">
                        <div class="">
                            <img src="{{asset('/images/total_votes.png')}}" style="width: 5em">
                        </div>
                        <div class="">
                            <strong>{{trans('privateCbsVoteAnalysis.total_votes')}}</strong>
                        </div>
                        <div class="">
                            {{$statisticsTotalSummary->total ?? null}}
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 col-sm-6 col-12 text-center">
                        <div class="">
                            <img src="{{asset('/images/positive_votes.png')}}" style="width: 5em">
                        </div>
                        <div class="">
                            <strong>{{trans('privateCbsVoteAnalysis.total_positive_votes')}}</strong>
                        </div>
                        <div class="">
                            {{$statisticsTotalSummary->total_positives ?? null}}
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 col-sm-6 col-12 text-center">
                        <div class="">
                            <img src="{{asset('/images/negative_votes.png')}}" style="width: 5em">
                        </div>
                        <div class="">
                            <strong>{{trans('privateCbsVoteAnalysis.total_negative_votes')}}</strong>
                        </div>
                        <div class="">
                            {{$statisticsTotalSummary->total_negatives ?? null}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{--END TOTAL VOTES INFORMATION--}}
    </div>
    <div class="row">
        {{--TOP 10--}}
        <div class="col-md-12">
            <div class="">
                <div class="box-header">
                    <h3 class="box-title">{{trans('privateCbsVoteAnalysis.top_topics')}}</h3>
                </div>
                <div class="box-body">
                    <table class="table table-responsive  table-striped">
                        <tbody>
                        <tr>
                            <th style="width: 10px">#</th>
                            <th>{{trans('privateCbsVoteAnalysis.topic_name')}}</th>
                            <th class="text-center" style="width: 50px;">{{trans('privateCbsVoteAnalysis.total_budget')}}</th>
                            <th class="text-center" style="width: 50px;">{{trans('privateCbsVoteAnalysis.total_balance')}}</th>
                            <th class="text-center" style="width: 50px;">{{trans('privateCbsVoteAnalysis.total_positives')}}</th>
                            <th class="text-center" style="width: 50px;">{{trans('privateCbsVoteAnalysis.total_negatives')}}</th>
                        </tr>
                        @if(!empty($statisticsTotalData))
                            @foreach($statisticsTotalData as $topTopic)
                                <tr style="{{$topTopic->winner ? "background-color: #c2dcf1;": ""}}">
                                    <td>{{$loop->iteration }} </td>
                                    <td>{{$topTopic->title}}</td>
                                    <td>{{$topTopic->budget}}</td>
                                    <td class=" text-center">
                                        @if($topTopic->balance >= 0 )
                                            <span class="label bg-green"> {{$topTopic->balance}}</span>
                                        @else
                                            <span class="label bg-red"> {{$topTopic->balance}}</span>
                                        @endif
                                    </td>
                                    <td class="text-center">{{$topTopic->positives}}</td>
                                    <td class="text-center">{{$topTopic->negatives}}</td>

                                </tr>
                            @endforeach
                        @else
                            <tr style="background-color:#fbfbfb;"><td colspan="6" class="text-center">{{ trans('privateCbsVoteAnalysis.no_data_Available') }}</td></tr>
                        @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        {{--TOP 10 END--}}
    </div>

    <br>

    <div class="row">
        <div class="col-md-12">
            <div class="">
                <div class="box-header">
                    <h3 class="box-title">{{trans('privateCbsVoteAnalysis.balance_votes_by_topic')}}</h3>
                </div>
                <div class="box-body">
                    <!-- Download -->
                    <div id="statistics_by_topic_downloads_wrapper" class="chart-download-wrapper">
                        <a id="statisticsByTopicDownloadCSV"  class="btn btn-flat btn-blue pull-right">
                            <i class="fa fa-file-excel-o" aria-hidden="true"></i> {{ trans('privateCbsVoteAnalysis.download_csv') }}
                        </a>
                    </div>
                    <div id="statistics_by_topic" style="min-height: 300px;width: 100%;"></div>
                </div>
            </div>
        </div>
    </div>
@endif

@if(!empty($statisticsTotalData))
    <script>
        var statistics_by_topic_data = [
            @php $k = 0; @endphp
            @foreach($statisticsTotalData as $topTopic)
                {"position": "{{ $loop->iteration }}", "{!! trans('privateCbsVoteAnalysis.topic_name') !!}":"{{ $topTopic->title }}", "type":"{!! trans('privateCbsVoteAnalysis.positives_votes') !!}","{!! trans('privateCbsVoteAnalysis.total_votes') !!}": {{ $topTopic->positives ?? 0 }},"total_votes": {{ $topTopic->positives ?? 0 }}},
                {"position": "{{ $loop->iteration }}", "{!! trans('privateCbsVoteAnalysis.topic_name') !!}":"{{ $topTopic->title }}", "type":"{!! trans('privateCbsVoteAnalysis.negatives_votes') !!}","{!! trans('privateCbsVoteAnalysis.total_votes') !!}": {{ $topTopic->negatives * -1 ?? 0 }},"total_votes": {{ $topTopic->negatives * -1 ?? 0 }}},
                @php $k++; @endphp
            @endforeach
        ];

        $("#statistics_by_topic").css("height", "{{ ($k <= 15) ? "400" : $k*20 }}px");

        var visualization = d3plus.viz()
            .container("#statistics_by_topic")  // container DIV to hold the visualization
            .data(statistics_by_topic_data)  // data to use with the visualization
            .type("bar")// visualization type
            .id("type")
            .y("{!! trans('privateCbsVoteAnalysis.topic_name') !!}")         // key to use for y-axis
            .y({"scale": "discrete"}) // Manually set Y-axis to be discrete
            .x({"stacked": true}) // Manually set Y-axis to be discrete
            .x( "{!! trans('privateCbsVoteAnalysis.total_votes') !!}")// key to use for x-axis
            .order("position")
            .color(function(d){
                return d.total_votes > 0 ? "#07A614" : "#A61106";
            })
            .font({"family": "Helvetica, Arial, sans-serif", "color": "#000"})
            .resize(true)
            .draw();

            // Export data for CSV (javascript)
            $( "#statisticsByTopicDownloadCSV" ).click(function() {
                var d = new Date();
                var suffix_name = d.getFullYear()+"_"+(1+d.getMonth())+"_"+d.getDate()+"_"+d.getHours()+"_"+d.getMinutes()+"_"+d.getSeconds();
                var filename = "statistics_by_topic_"+suffix_name+".csv";
                downloadCSV(statistics_by_topic_data,filename);
            });
    </script>
@else
    <script>
        var warningMessage = "{!! trim(preg_replace('/\s\s+/', ' ', trans('privateCbsVoteAnalysis.no_data_Available'))) !!}";
        $('#statistics_by_topic').html('<div class="chartMessage"><div><i class="fa fa-eye-slash" aria-hidden="true"></i> '+warningMessage+'</div></div>');
        // toastr.warning(warningMessage);
        $("#statistics_by_topic_downloads_wrapper").remove();
        $("#statistics_by_topic").css("min-height","100px");
        $(".chartMessage").css("min-height","100px");
    </script>
@endif