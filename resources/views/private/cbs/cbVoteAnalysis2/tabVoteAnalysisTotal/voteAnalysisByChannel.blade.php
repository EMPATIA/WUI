@if(empty($statisticsTotalByChannel))
    <div class="chartMessage">
        <div><i class="fa fa-eye-slash" aria-hidden="true"></i> {{trans('privateCbsVoteAnalysis.no_data_available')}}</div>
    </div>
@else
    <div class="box-info">
        <div class="box-body">
            @php $chartId = 0; @endphp
            {{--{{dd($statisticsTotalByChannel)}}--}}
            @foreach(!empty($statisticsTotalByChannel) ? $statisticsTotalByChannel : [] as $channel => $statChannelInfo)
                @if(!empty($statChannelInfo->data))
                    @php
                    $statisticsTotalSummary = $statChannelInfo->summary;
                    @endphp
                    <div class="margin-bottom-10 card-header margin-top-20" style="padding-top:11px;padding-bottom: 3px;;" data-toggle="collapse" data-target=".multi-collapse{{$channel}}" aria-expanded="false" aria-controls="collapse{{$channel}}1 collapse{{$channel}}2 collapse{{$channel}}3">
                        <h5 class="box-title color-white">{{ trans('privateCbsVoteAnalysis.'.$channel) }}</h5>
                    </div>
                    <div class="collapse multi-collapse{{$channel}} margin-bottom-20" style="padding-top:20px" id="collapse{{$channel}}1">
                        <div class="row">
                            <div class="col-md-12 voteAnalysis-total">
                                <div class="row box-body">
                                    <div class="col text-center">
                                        <div>
                                            <img src="{{asset('/images/total_voters.png')}}" style="width: 5em">
                                        </div>
                                        <div>
                                            <strong>{{trans('privateCbsVoteAnalysis.total_voters')}}</strong>
                                        </div>
                                        <div>
                                            {{ $statisticsTotalSummary->total_users_voted ?? null }}
                                        </div>
                                    </div>
                                    <div class="col text-center">
                                        <div>
                                            <img src="{{asset('/images/total_votes.png')}}" style="width: 5em">
                                        </div>
                                        <div>
                                            <strong>{{trans('privateCbsVoteAnalysis.total_votes')}}</strong>
                                        </div>
                                        <div>
                                            {{$statisticsTotalSummary->total_votes ?? null}}
                                        </div>
                                    </div>
                                    <div class="col text-center">
                                        <div>
                                            <img src="{{asset('/images/total_votes_submitted.png')}}" style="width: 5em">
                                        </div>
                                        <div>
                                            <strong>{{trans('privateCbsVoteAnalysis.total_votes_submitted')}}</strong>
                                        </div>
                                        <div>
                                            {{$statisticsTotalSummary->total_submitted ?? null}}
                                        </div>
                                    </div>
                                    <div class="col text-center">
                                        <div>
                                            <img src="{{asset('/images/positive_votes.png')}}" style="width: 5em">
                                        </div>
                                        <div>
                                            <strong>{{trans('privateCbsVoteAnalysis.total_positive_votes')}}</strong>
                                        </div>
                                        <div>
                                            {{$statisticsTotalSummary->total_positives ?? null}}
                                        </div>
                                    </div>
                                    <div class="col text-center">
                                        <div>
                                            <img src="{{asset('/images/negative_votes.png')}}" style="width: 5em">
                                        </div>
                                        <div>
                                            <strong>{{trans('privateCbsVoteAnalysis.total_negative_votes')}}</strong>
                                        </div>
                                        <div>
                                            {{$statisticsTotalSummary->total_negatives ?? null}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @php
                        $statisticsTotalData = $statChannelInfo->data;
                    @endphp

                    {{--CHART--}}
                    <div class="collapse multi-collapse{{$channel}} row" id="collapse{{$channel}}2">
                        <div class="col-md-12">
                            <div class="margin-bottom-20">
                                <div class="box-header">
                                    <h3 class="box-title">{{trans('privateCbsVoteAnalysis.balance_votes_by_topic')}}
                                        @if(!empty($top))
                                        - {{ trans('privateCbsVoteAnalysis.top') }}  {{ $top }}
                                        @else

                                        @endif
                                        @if($viewSubmitted == 0)
                                            [ {{ trans('privateUserAnalysis.votes_all') }} ] 
                                        @else
                                            [ {{ trans('privateUserAnalysis.votes_submitted') }} ] 
                                        @endif
                                    </h3>
                                </div>
                                <div class="box-body">
                                    <!-- Download -->
                                    <div id="statistics_by_topic_downloads_wrapper" class="chart-download-wrapper">
                                        <a id="statisticsByTopicDownloadCSV{{ $chartId }}"  class="btn btn-flat btn-blue pull-right">
                                            <i class="fa fa-file-excel-o" aria-hidden="true"></i> {{ trans('privateCbsVoteAnalysis.download_csv') }}
                                        </a>
                                        <a id="downloadImage{{ $chartId }}"  class="btn btn-flat btn-blue pull-right" style="margin-right:5px;">
                                            <i class="fa fa-file-image-o" aria-hidden="true"></i> {{ trans('privateCbsVoteAnalysis.download_image') }}
                                        </a>
                                    </div>
                                    <div id="statistics_by_topic{{ $chartId }}" style="min-height: 300px;width:100%;"></div>
                                    <!-- Canvas for downloading chart -->
                                    <canvas id="canvas_statistics_by_topic{{ $chartId }}" height="300" style="display: none;"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{--CHART END--}}
                    <script>
                        var statistics_by_topic_data{{ $chartId }} = [
                        @php $k = 0; @endphp
                        @foreach(!empty($statisticsTotalData) ? $statisticsTotalData : [] as $topTopic)
                            {"position": {{ $loop->iteration }}, "{!! trans('privateCbsVoteAnalysis.topic_name') !!}":"{{ $topTopic->topic_number}} - {{ $topTopic->title }}", "type":"{!! trans('privateCbsVoteAnalysis.positives_votes') !!}","{!! trans('privateCbsVoteAnalysis.total_votes') !!}": {{ $topTopic->positives ?? 0 }},"total_votes": {{ $topTopic->positives ?? 0 }}},
                            {"position": {{ $loop->iteration }}, "{!! trans('privateCbsVoteAnalysis.topic_name') !!}":"{{ $topTopic->topic_number}} - {{ $topTopic->title }}", "type":"{!! trans('privateCbsVoteAnalysis.negatives_votes') !!}","{!! trans('privateCbsVoteAnalysis.total_votes') !!}": {{ $topTopic->negatives * -1 ?? 0 }},"total_votes": {{ $topTopic->negatives * -1 ?? 0 }}},
                            @php $k++; @endphp
                        @endforeach
                        ];

                        $("#statistics_by_topic{{ $chartId }}").css("height", "{{ ($k <= 15) ? "400" : $k*20 }}px");

                        setTimeout(function(){
                            var visualization = d3plus.viz()
                                .container("#statistics_by_topic{{ $chartId }}")  // container DIV to hold the visualization
                                .data(statistics_by_topic_data{{ $chartId }})  // data to use with the visualization
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

                            // Download chart to PNG (javascript)
                            $("#downloadImage{{ $chartId }}").click(function() {
                                var d = new Date();
                                var suffix_name = d.getFullYear()+"_"+(1+d.getMonth())+"_"+d.getDate()+"_"+d.getHours()+"_"+d.getMinutes()+"_"+d.getSeconds();
                                var filename = "statistics_by_topic_"+suffix_name+"_channel{{ $chartId }}.png";
                                $("#canvas_statistics_by_topic{{ $chartId }}").attr("width",  $("#statistics_by_topic{{ $chartId }} #d3plus").width() );
                                $("#canvas_statistics_by_topic{{ $chartId }}").attr("height", $("#statistics_by_topic{{ $chartId }} #d3plus").height() );
                                var svg = document.querySelector('#statistics_by_topic{{ $chartId }} svg');
                                var canvas = document.getElementById('canvas_statistics_by_topic{{ $chartId }}');
                                var ctx = canvas.getContext('2d');
                                var data = (new XMLSerializer()).serializeToString(svg);
                                var DOMURL = window.URL || window.webkitURL || window;
                                var img = new Image();
                                var svgBlob = new Blob([data], {type: 'image/svg+xml;charset=utf-8'});
                                var url = DOMURL.createObjectURL(svgBlob);
                                img.onload = function () {
                                    ctx.drawImage(img, 0, 0);
                                    DOMURL.revokeObjectURL(url);
                                    var imgURI = canvas
                                        .toDataURL('image/png')
                                        .replace('image/png', 'image/octet-stream');

                                    triggerChartDownload(filename, imgURI);
                                };
                                img.src = url;
                            });

                        }, 500);
                    </script>

                    <div class="collapse multi-collapse{{$channel}} margin-bottom-20" id="collapse{{$channel}}3">
                        <div class="row">
                        <div class="col-md-12">
                            <div class="">
                                <div class="box-header">
                                    <h3 class="box-title">
                                        @if(!empty($top))
                                            {{ trans('privateCbsVoteAnalysis.top_topics')}} - {{ trans('privateCbsVoteAnalysis.top') }} {{ $top }}
                                        @else
                                            {{ trans('privateCbsVoteAnalysis.topics') }}
                                        @endif</h3>
                                </div>
                                <div class="box-body">
                                    <div id="table-statistics_by_topic_downloads_wrapper" class="table-download-wrapper">
                                        <a id="tableStatisticsByTopicDownloadCSV{{ $chartId }}"  class="btn btn-flat btn-blue pull-left margin-bottom-10">
                                            <i class="fa fa-file-excel-o" aria-hidden="true"></i> {{ trans('privateCbsVoteAnalysis.download_csv') }}
                                        </a>
                                    </div>
                                    <table id="top10Table{{ $chartId }}" class="table table-responsive  table-striped">
                                        <thead>
                                        <tr>
                                            <th style="width: 10px">#</th>
                                            <th>{{trans('privateCbsVoteAnalysis.topic_name')}}</th>
                                            <th class="text-center" style="width: 50px;">{{trans('privateCbsVoteAnalysis.total_balance')}}</th>
                                            <th class="text-center" style="width: 50px;">{{trans('privateCbsVoteAnalysis.total_positives')}}</th>
                                            <th class="text-center" style="width: 50px;">{{trans('privateCbsVoteAnalysis.total_negatives')}}</th>
                                        </tr>
                                        </thead>
                                    </table>
                                    <script>

                                        var top10TableItems = [
                                            @foreach(!empty($statisticsTotalData) ? $statisticsTotalData : [] as $topTopic)
                                            {
                                                "topic_number": {{$topTopic->topic_number}},
                                                "topic_title": "{{ $topTopic->title }}",
                                                "balance": {{$topTopic->balance}},
                                                "positives": {{$topTopic->positives}},
                                                "negatives": {{$topTopic->negatives}},
                                            },
                                            @endforeach
                                        ];
                                        //Load  datatable
                                        var oTblReport = $("#top10Table{{ $chartId }}")
                                        oTblReport.DataTable ({
                                            data : top10TableItems,
                                            columns : [
                                                { "data" : "topic_number" },
                                                { "data" : "topic_title" },
                                                { "data" : "balance" },
                                                { "data" : "positives" },
                                                { "data" : "negatives" }
                                            ],
                                            paging: false,
                                            language: {
                                                url: '{!! asset('/datatableLang/'.Session::get('LANG_CODE').'.json') !!}',
                                                search: '<a class="btn searchBtn" id="searchBtn"><i class="fa fa-search"></i></a>'
                                            },
                                            stateSave: false,
                                            order: [['2', 'desc']]
                                        });

                                        var statistics_by_topic_table{{ $chartId }} = [
                                            @php $k = 0; @endphp
                                            @foreach(!empty($statisticsTotalData) ? $statisticsTotalData : [] as $topTopic)
                                                {
                                                    "{{ trans('privateCbsVoteAnalysis.topic_number') }}":  {{$topTopic->topic_number}},
                                                    "{{ trans('privateCbsVoteAnalysis.topic_name') }}": "{{ $topTopic->title }}",
                                                    "{{ trans('privateCbsVoteAnalysis.positives') }}": {{ $topTopic->positives ?? 0 }},
                                                    "{{ trans('privateCbsVoteAnalysis.negatives') }}": {{ $topTopic->negatives * -1 ?? 0 }},
                                                    "{{ trans('privateCbsVoteAnalysis.balance') }}": {{ $topTopic->balance ?? 0 }}
                                                },
                                                @php $k++; @endphp
                                            @endforeach
                                        ];

                                        // Export data for CSV (javascript)
                                        $( "#tableStatisticsByTopicDownloadCSV{{ $chartId }}" ).click(function() {
                                            var d = new Date();
                                            var suffix_name = d.getFullYear()+"_"+(1+d.getMonth())+"_"+d.getDate()+"_"+d.getHours()+"_"+d.getMinutes()+"_"+d.getSeconds();
                                            var filename = "statistics_by_topic_"+suffix_name+"_table{{ $chartId }}.csv";
                                            downloadCSV(statistics_by_topic_table{{ $chartId }}, filename);
                                        });

                                        // Export data for CSV (javascript)
                                        $( "#statisticsByTopicDownloadCSV{{ $chartId }}" ).click(function() {
                                            var d = new Date();
                                            var suffix_name = d.getFullYear()+"_"+(1+d.getMonth())+"_"+d.getDate()+"_"+d.getHours()+"_"+d.getMinutes()+"_"+d.getSeconds();
                                            var filename = "statistics_by_topic_"+suffix_name+"_channel{{ $chartId }}.csv";
                                            downloadCSV(statistics_by_topic_table{{ $chartId }},filename);
                                        });
                                    </script>
                                </div>
                            </div>
                        </div>
                    </div>
                    </div>
                    <br><br>
                    <hr>
                    @php $chartId++; @endphp
                @endif
            @endforeach
        </div>
    </div>
@endif

@include('private.cbs.cbVoteAnalysis2.details.cbDetailsScript')