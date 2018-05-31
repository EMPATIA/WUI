@if(empty($statisticsTotalData) && empty($statisticsTotalSummary))
    <div class="chartMessage">
        <div><i class="fa fa-eye-slash" aria-hidden="true"></i> {{trans('privateCbsVoteAnalysis.no_data_available')}}</div>
    </div>
@else
    {{--CHART--}}
    <div class="row">
        <div class="col-md-12">
            <div class="margin-bottom-20">
                <div class="box-header">
                    <h3 class="box-title">{{trans('privateCbsVoteAnalysis.balance_votes_by_topic')}}
                    @if(!empty($top))
                        - {{ trans('privateCbsVoteAnalysis.top') }} {{ $top }}
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
                        <a id="statisticsByTopicDownloadCSV"  class="btn btn-flat btn-blue pull-right">
                            <i class="fa fa-file-excel-o" aria-hidden="true"></i> {{ trans('privateCbsVoteAnalysis.download_csv') }}
                        </a>
                        <a id="downloadImage"  class="btn btn-flat btn-blue pull-right" style="margin-right:5px;">
                            <i class="fa fa-file-image-o" aria-hidden="true"></i> {{ trans('privateCbsVoteAnalysis.download_image') }}
                        </a>
                    </div>
                    <div id="statistics_by_topic" style="min-height: 300px;width:100%;"></div>
                    <!-- Canvas for downloading chart -->
                    <canvas id="canvas_statistics_by_topic" height="300" style="display: none;"></canvas>
                </div>
            </div>
        </div>
    </div>
    {{--CHART END--}}

    {{--TOP 10--}}

    <!-- Download -->
    <div class="row">
        <div class="col-md-12">
            <div class="box-header">
                <h3 class="box-title">
                    @if(!empty($top))
                        {{ trans('privateCbsVoteAnalysis.top_topics') }} - {{ trans('privateCbsVoteAnalysis.top') }} {{ $top }}
                    @else
                        {{trans('privateCbsVoteAnalysis.topics')}}
                    @endif
                </h3>
            </div>
            <div class="box-body">

                <div id="table-statistics_by_topic_downloads_wrapper" class="table-download-wrapper">
                    <a id="tableStatisticsByTopicDownloadCSV"  class="btn btn-flat btn-blue pull-left margin-bottom-10">
                        <i class="fa fa-file-excel-o" aria-hidden="true"></i> {{ trans('privateCbsVoteAnalysis.download_csv') }}
                    </a>
                </div>
                <table id="top10Table" class="table table-responsive  table-striped">
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
                        @if(!empty($statisticsTotalData))
                            @foreach($statisticsTotalData as $topTopic)
                                {
                                    "topic_number": {{$topTopic->topic_number}},
                                    "topic_title": "{{ $topTopic->title }}",
                                    "balance": {{$topTopic->balance}},
                                    "positives": {{$topTopic->positives}},
                                    "negatives": {{$topTopic->negatives}},
                                },
                            @endforeach
                        @endif
                    ];
                    //Load  datatable
                    var oTblReport = $("#top10Table")
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
                        order: [['2', 'desc']],
                        stateSave: false
                    });

                    var statistics_by_topic_table = [
                        @php $k = 0; @endphp
                        @foreach($statisticsTotalData as $topTopic)
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
                $( "#tableStatisticsByTopicDownloadCSV" ).click(function() {
                    var d = new Date();
                    var suffix_name = d.getFullYear()+"_"+(1+d.getMonth())+"_"+d.getDate()+"_"+d.getHours()+"_"+d.getMinutes()+"_"+d.getSeconds();
                    var filename = "table_statistics_by_topic_"+suffix_name+".csv";
                    downloadCSV(statistics_by_topic_table, filename);
                });


                // Export data for CSV (javascript)
                $( "#statisticsByTopicDownloadCSV" ).click(function() {
                    var d = new Date();
                    var suffix_name = d.getFullYear()+"_"+(1+d.getMonth())+"_"+d.getDate()+"_"+d.getHours()+"_"+d.getMinutes()+"_"+d.getSeconds();
                    var filename = "statistics_by_topic_"+suffix_name+".csv";
                    downloadCSV(statistics_by_topic_table,filename);
                });
               </script>
            </div>
        </div>
    </div>
    {{--TOP 10 END--}}
@endif

@if(!empty($statisticsTotalData))
    <script>
        var statistics_by_topic_data = [
            @php $k = 0; @endphp
            @foreach($statisticsTotalData as $topTopic)
                {"position": {{ $loop->iteration }}, "{!! trans('privateCbsVoteAnalysis.topic_name') !!}":"{{ $topTopic->topic_number}} - {{ $topTopic->title }}", "type":"{!! trans('privateCbsVoteAnalysis.positives_votes') !!}","{!! trans('privateCbsVoteAnalysis.total_votes') !!}": {{ $topTopic->positives ?? 0 }},"total_votes": {{ $topTopic->positives ?? 0 }}},
                {"position": {{ $loop->iteration }}, "{!! trans('privateCbsVoteAnalysis.topic_name') !!}":"{{ $topTopic->topic_number}} - {{ $topTopic->title }}", "type":"{!! trans('privateCbsVoteAnalysis.negatives_votes') !!}","{!! trans('privateCbsVoteAnalysis.total_votes') !!}": {{ $topTopic->negatives * -1 ?? 0 }},"total_votes": {{ $topTopic->negatives * -1 ?? 0 }}},
                @php $k++; @endphp
            @endforeach
        ];

        $("#statistics_by_topic").css("height", "{{ ($k <= 15) ? "400" : $k*20 }}px");

        setTimeout(function(){
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


            // Download chart to PNG (javascript)
            $("#downloadImage").click(function() {
                var d = new Date();
                var suffix_name = d.getFullYear()+"_"+(1+d.getMonth())+"_"+d.getDate()+"_"+d.getHours()+"_"+d.getMinutes()+"_"+d.getSeconds();
                var filename = "statistics_by_topic_"+suffix_name+".png";
                $("#canvas_statistics_by_topic").attr("width",  $("#statistics_by_topic #d3plus").width() );
                $("#canvas_statistics_by_topic").attr("height", $("#statistics_by_topic #d3plus").height() );
                var svg = document.querySelector('#statistics_by_topic svg');
                var canvas = document.getElementById('canvas_statistics_by_topic');
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

        }, 200);

            $("#total_voters").html("{{ $statisticsTotalSummary->total_users_voted }}");
            $("#moreDetails").show();
            //$("#total_votes").html("{{ $statisticsTotalSummary->total }}");
            $("#total_positives_votes").html("{{ $statisticsTotalSummary->total_positives }}");
            $("#total_negative_votes").html("{{ $statisticsTotalSummary->total_negatives }}");
    </script>
@else
    <script>
        var warningMessage = "{!! trim(preg_replace('/\s\s+/', ' ', trans('privateCbsVoteAnalysis.no_data_Available'))) !!}";
        $('#statistics_by_topic').html('<div class="chartMessage"><div><i class="fa fa-eye-slash" aria-hidden="true"></i> '+warningMessage+'</div></div>');
        // toastr.warning(warningMessage);
        $("#statistics_by_topic_downloads_wrapper").remove();
        $("#statistics_by_topic").css("min-height","100px");
        $("#statistics_by_topic").css("width","100%");
        // chartMessage
        $(".chartMessage").css("min-height","100px");
        $(".chartMessage").css("width","100%");
    </script>
@endif

@include('private.cbs.cbVoteAnalysis2.details.cbDetailsScript')