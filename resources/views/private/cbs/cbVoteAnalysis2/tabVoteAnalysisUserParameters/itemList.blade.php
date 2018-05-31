{{-- ****************** Checkbox, radio button, list  ****************** --}}

<!--  Gráfico de barras horizontal com número de votos por opção do parâmetro -->
@if(!empty($parameterCode) && !empty($parameterKey) && !empty($votesByParameter))
    <div class="padding-top-20 padding-bottom-20 margin-bottom-30">
        <div class="row">
            <div class="col-12">
                <div class="box-info">
                    <div class="box-header">
                        <h3 class="box-title"><i class="fa"></i> {{ trans('privateCbsVoteAnalysis.statistics_total_by') }} {{$parameterName ?? ''}}</h3>
                    </div>
                    <div class="box-body">
                        <!-- Download -->
                        <div class="chart-download-wrapper">
                            <a id="downloadStatsByParamCSV_{{$parameterKey}}" class="btn btn-flat btn-blue pull-right">
                                <i class="fa fa-file-excel-o" aria-hidden="true"></i> {{ trans('privateCbsVoteAnalysis.download_csv') }}
                            </a>
                            <a id="downloadStatsByParamImage_{{$parameterKey}}"  class="btn btn-flat btn-blue pull-right" style="margin-right:5px;">
                                <i class="fa fa-file-image-o" aria-hidden="true"></i> {{ trans('privateCbsVoteAnalysis.download_image') }}
                            </a>
                        </div>
                        <div id="statistics_by_{{$parameterKey}}" style="height: 300px"></div>
                        <!-- Canvas for downloading chart -->
                        <canvas id="canvas_statistics_by_{{$parameterKey}}" height="300" style="display: none;"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        @php $k = 0; @endphp
        var statistics_by_parameter_{{$parameterKey}} = [
            @foreach($votesByParameter->total as $option => $vote)
                { "{!! trans('privateCbsVoteAnalysis.option_name') !!}":"{{ $option }}","{!! trans('privateCbsVoteAnalysis.total_votes') !!}": {{ $vote }}},
                    @php $k++; @endphp
            @endforeach
        ];
        $("#statistics_by_{{$parameterKey}}").css("height", "{{ ($k <= 15) ? "400" : $k*20 }}px");
        var visualization = d3plus.viz()
            .container("#statistics_by_{{$parameterKey}}")  // container DIV to hold the visualization
            .data(statistics_by_parameter_{{$parameterKey}})  // data to use with the visualization
            .type("bar")// visualization type
            .id("{!! trans('privateCbsVoteAnalysis.option_name') !!}")
            .y("{!! trans('privateCbsVoteAnalysis.option_name') !!}")         // key to use for y-axis
            .y({"scale": "discrete"}) // Manually set Y-axis to be discrete
            .x( "{!! trans('privateCbsVoteAnalysis.total_votes') !!}")// key to use for x-axis
            .legend({
                "value": true,
                "size": 35
            })
            .font({"family": "Helvetica, Arial, sans-serif", "color": "#000"}).resize(true)
            .draw();

        // Export data for CSV (javascript)
        $("#downloadStatsByParamCSV_{{$parameterKey}}").click(function () {
            var d = new Date();
            var suffix_name = d.getFullYear() + "_" + (1 + d.getMonth()) + "_" + d.getDate() + "_" + d.getHours() + "_" + d.getMinutes() + "_" + d.getSeconds();
            var filename = "statistics_by_parameter_" + suffix_name + ".csv";
            downloadCSV(statistics_by_parameter_{{$parameterKey}}, filename);
        });
        // Download chart to PNG (javascript)
        $("#downloadStatsByParamImage_{{$parameterKey}}").click(function () {
            var d = new Date();
            var suffix_name = d.getFullYear() + "_" + (1 + d.getMonth()) + "_" + d.getDate() + "_" + d.getHours() + "_" + d.getMinutes() + "_" + d.getSeconds();
            var filename = "statistics_by_parameter_" + suffix_name + ".png";
            $("#canvas_statistics_by_{{$parameterKey}}").attr("width", $("#statistics_by_{{$parameterKey}} #d3plus").width());
            $("#canvas_statistics_by_{{$parameterKey}}").attr("height", $("#statistics_by_{{$parameterKey}} #d3plus").height());
            var svg = document.querySelector('#statistics_by_{{$parameterKey}} svg');
            var canvas = document.getElementById('canvas_statistics_by_{{$parameterKey}}');
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
    </script>
@endif


<!-- 7) Gráfico de barras horizontal com número de votos por opção do parâmetro por canal --> {{-- ✔ - Download image   --}} {{-- ✔ - Download CSV     --}} {{-- ✔ - Incluir dados que não preencheram o pârametro --}}
@if(!empty($parameterCode) && !empty($parameterKey) && !empty($statisticsByParameterChannel))
<div class="padding-top-20 padding-bottom-20 margin-bottom-30">
    <div class="row">
        <div class="col-12">
            <div class="box-info">
                <div class="box-header">
                    <h3 class="box-title">
                        <i class="fa"></i>
                        {{ trans('privateCbsVoteAnalysis.statistics_total_by') }}
                        {{ trans('privateCbsVoteAnalysis.channel') }}
                        {{ trans('privateCbsVoteAnalysis.and') }} {{$parameterName ?? ''}}
                    </h3>
                </div>
                <div class="box-body">
                    <!-- Download -->
                    <div class="chart-download-wrapper">
                        <a id="downloadStatsByParamChannelCSV_{{$parameterKey}}" class="btn btn-flat btn-blue pull-right">
                            <i class="fa fa-file-excel-o" aria-hidden="true"></i> {{ trans('privateCbsVoteAnalysis.download_csv') }}
                        </a>
                        <a id="downloadStatsByParamChannelImage_{{$parameterKey}}"  class="btn btn-flat btn-blue pull-right" style="margin-right:5px;">
                            <i class="fa fa-file-image-o" aria-hidden="true"></i> {{ trans('privateCbsVoteAnalysis.download_image') }}
                        </a>
                    </div>
                    <div id="statistics_by_channel_{{$parameterKey}}" style="height: 300px"></div>
                    <!-- Canvas for downloading chart -->
                    <canvas id="canvas_statistics_by_channel_{{$parameterKey}}" height="300" style="display: none;"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    @php $k = 0; @endphp
    var statistics_by_parameter_channel_{{$parameterKey}} = [
        @foreach($statisticsByParameterChannel as $channel => $itemChannel)
            @foreach($itemChannel->statistics_by_parameter->total as $option => $vote)
            { "{!! trans('privateCbsVoteAnalysis.option_name') !!}":"{{ $channel }} - {{ $option }}","{!! trans('privateCbsVoteAnalysis.total_votes') !!}": {{ $vote }}},
            @php $k++; @endphp
        @endforeach
    @endforeach
    ];

    $("#statistics_by_channel_{{$parameterKey}}").css("height", "{{ ($k <= 15) ? "400" : $k*20 }}px");
    var visualization = d3plus.viz()
        .container("#statistics_by_channel_{{$parameterKey}}")  // container DIV to hold the visualization
        .data(statistics_by_parameter_channel_{{$parameterKey}})  // data to use with the visualization
        .type("bar")// visualization type
        .id("{!! trans('privateCbsVoteAnalysis.option_name') !!}")
        .y("{!! trans('privateCbsVoteAnalysis.option_name') !!}")         // key to use for y-axis
        .y({"scale": "discrete"}) // Manually set Y-axis to be discrete
        .x( "{!! trans('privateCbsVoteAnalysis.total_votes') !!}")// key to use for x-axis
        .legend({
            "value": true,
            "size": 35
        })
        .font({"family": "Helvetica, Arial, sans-serif", "color": "#000"}).resize(true)
        .draw();

    // Export data for CSV (javascript)
    $("#downloadStatsByParamChannelCSV_{{$parameterKey}}").click(function () {
        var d = new Date();
        var suffix_name = d.getFullYear() + "_" + (1 + d.getMonth()) + "_" + d.getDate() + "_" + d.getHours() + "_" + d.getMinutes() + "_" + d.getSeconds();
        var filename = "statistics_by_parameter_channel_" + suffix_name + ".csv";
        downloadCSV(statistics_by_parameter_channel_{{$parameterKey}}, filename);
    });
    // Download chart to PNG (javascript)
    $("#downloadStatsByParamChannelImage_{{$parameterKey}}").click(function () {
        var d = new Date();
        var suffix_name = d.getFullYear() + "_" + (1 + d.getMonth()) + "_" + d.getDate() + "_" + d.getHours() + "_" + d.getMinutes() + "_" + d.getSeconds();
        var filename = "statistics_by_parameter_channel_" + suffix_name + ".png";
        $("#canvas_statistics_by_channel_{{$parameterKey}}").attr("width", $("#statistics_by_channel_{{$parameterKey}} #d3plus").width());
        $("#canvas_statistics_by_channel_{{$parameterKey}}").attr("height", $("#statistics_by_channel_{{$parameterKey}} #d3plus").height());
        var svg = document.querySelector('#statistics_by_channel_{{$parameterKey}} svg');
        var canvas = document.getElementById('canvas_statistics_by_channel_{{$parameterKey}}');
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

   </script>
@endif


<!-- 8) Gráfico de linhas com número de votos e votantes por dia, por parâmetro e por canal (total votos apenas) -->
    {{-- ‎✔ - Deve ter o dateRangePicker - o dateRangePicker não afecta os outros graficos  --}}
    {{-- ✘ - Evento <= 2 dia - o dateRangePicker deve ter horas tb, c.c. só dias    --}}
    {{-- ✔ - Incluir dados que não preencheram o pârametro --}}
    {{-- ✔ - Download image   --}}
    {{-- ✔ - Download CSV     --}}
<!-- Date range -->
@if(!empty($parameterCode) && !empty($parameterKey) && !empty($votesByDate) && !empty($voteEventObj))
    <script>
        function joinObjects() {
            var idMap = {};
            // Iterate over arguments
            for(var i = 0; i < arguments.length; i++) {
                // Iterate over individual argument arrays (aka json1, json2)
                for(var j = 0; j < arguments[i].length; j++) {
                    var currentID = arguments[i][j]['{!! trans("privateCbsVoteAnalysis.date") !!}'];
                    if(!idMap[currentID]) {
                        idMap[currentID] = {};
                    }
                    // Iterate over properties of objects in arrays (aka id, name, etc.)
                    for(key in arguments[i][j]) {
                        idMap[currentID][key] = arguments[i][j][key];
                    }
                }
            }

            // push properties of idMap into an array
            var newArray = [];
            for(property in idMap) {
                newArray.push(idMap[property]);
            }
            return newArray;
        }

        function addZero(i) {
            i = parseInt(i);
            if (i < 10) {
                i = "0" + i;
            }
            return i;
        }

        function getCurrentDateRangeForHoursCharts(){
            var d = new Date();
            var h = addZero(d.getHours());
            var m = addZero(d.getMinutes());
            var endDate = d.getFullYear() + "-" + addZero(d.getMonth()+1) + "-" + addZero(d.getDate())+" "+h + ":" + m;
            d.setDate(d.getDate() - 1);
            var startDate = d.getFullYear() + "-" + addZero(d.getMonth()+1) + "-" + addZero(d.getDate())+" "+h + ":" + m;
            return [startDate, endDate];
        }
    </script>
    <!-- Filter -->
    <div class="margin-top-30 margin-bottom-30" style="margin-right:30px;margin-left:30px;">
        <div class="row">
            <div class="col-12 col-sm-8 col-md-6 col-lg-4">
                <label>{{ trans('privateUserAnalysis.date_range') }}</label>
                <div class="form-group">
                    <div class='input-group date' id=''>
                        <input id="daterangepicker{{ $parameterKey }}" name="dr_vote_analysis_date2{{ $parameterKey }}" type='text' class="form-control"
                               value="@if(!empty($voteEventObj) && $voteEventObj->start_date != "0000-00-00 00:00" && $voteEventObj->end_date != "0000-00-00 00:00")
                               {{ substr($voteEventObj->start_date,0,10) }} - {{substr($voteEventObj->end_date,0,10)}}
                               @else
                               {{ Carbon\Carbon::now()->subDays(30)->toDateString() }} - {{ Carbon\Carbon::now()->toDateString() }}
                               @endif" />
                        <span class="input-group-addon"><i class="fa fa-calendar" aria-hidden="true"></i></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="margin-top-3">
            <div class="row">
                <div class="col-12 col-sm-12 col-md-8 col-lg-6">

                </div>
            </div>
        </div>

        <div style="display:none;" class="margin-top-30">
            <div class="row">
                <div class="col-12">
                    <span class="text-bold">{{ trans('privateUserAnalysis.votes') }}</span>
                </div>
            </div>
            <div class="box-filter-no-border">
                <div class="row">
                    <div class="col-12 col-md-6 col-lg-3 col-xl-2">
                        {!! Form::oneSwitch2('count_voters'.$parameterKey ,trans('privateUserAnalysis.voters'), 1, array("value"=> 1, "readonly"=>false))  !!}
                    </div>
                    <div class="col-12 col-md-6 col-lg-3 col-xl-2">
                        {!! Form::oneSwitch2('total'.$parameterKey ,trans('privateUserAnalysis.total'), 1, array("value"=> 1, "readonly"=>false))  !!}
                    </div>
                    <div class="col-12 col-md-6 col-lg-3 col-xl-2">
                        {!! Form::oneSwitch2('positive'.$parameterKey ,trans('privateUserAnalysis.positives'), 0, array("value"=> 1, "readonly"=>false))  !!}
                    </div>
                    <div class="col-12 col-md-6 col-lg-3 col-xl-2">
                        {!! Form::oneSwitch2('negative'.$parameterKey ,trans('privateUserAnalysis.negative'), 0, array("value"=> 1, "readonly"=>false))  !!}
                    </div>
                    <div class="col-12 col-md-6 col-lg-3 col-xl-2">
                        {!! Form::oneSwitch2('balance'.$parameterKey ,trans('privateUserAnalysis.balance'), 0, array("value"=> 1, "readonly"=>false))  !!}
                    </div>
                </div>
            </div>
        </div>
        <div style="display:none;" class="margin-top-30">
            <div class="row">
                <div class="col-12">
                    <span class="text-bold">{{ trans('privateUserAnalysis.channels') }}</span>
                </div>
            </div>

            <div class="margin-bottom-20">
                <div class="row">
                    <div class="col-12 col-md-6 col-lg-3 col-xl-3">
                        {!! Form::oneSwitch2('web'.$parameterKey ,trans('privateUserAnalysis.web'), 0, array("value"=> 1, "readonly"=>false))  !!}
                    </div>
                    <div class="col-12 col-md-6 col-lg-3 col-xl-3">
                        {!! Form::oneSwitch2('pc'.$parameterKey ,trans('privateUserAnalysis.pc'), 0, array("value"=> 1, "readonly"=>false))  !!}
                    </div>
                    <div class="col-12 col-md-6 col-lg-3 col-xl-3">
                        {!! Form::oneSwitch2('mobile'.$parameterKey ,trans('privateUserAnalysis.mobile'), 0, array("value"=> 1, "readonly"=>false))  !!}
                    </div>
                    <div class="col-12 col-md-6 col-lg-3 col-xl-3">
                        {!! Form::oneSwitch2('tablet'.$parameterKey ,trans('privateUserAnalysis.tablet'), 0, array("value"=> 1, "readonly"=>false))  !!}
                    </div>
                </div>
            </div>

            <div class="margin-top-20">
                <div class="row">
                    <div class="col-12 col-md-6 col-lg-3 col-xl-3">
                        {!! Form::oneSwitch2('sms'.$parameterKey ,trans('privateUserAnalysis.sms'), 0, array("value"=> 1, "readonly"=>false))  !!}
                    </div>
                    <div class="col-12 col-md-6 col-lg-3 col-xl-3">
                        {!! Form::oneSwitch2('kiosk'.$parameterKey ,trans('privateUserAnalysis.kiosk'), 0, array("value"=> 1, "readonly"=>false))  !!}
                    </div>
                    <div class="col-12 col-md-6 col-lg-3 col-xl-3">
                        {!! Form::oneSwitch2('in_person'.$parameterKey ,trans('privateUserAnalysis.in_person'), 0, array("value"=> 1, "readonly"=>false))  !!}
                    </div>
                    <div class="col-12 col-md-6 col-lg-3 col-xl-3">
                        {!! Form::oneSwitch2('other'.$parameterKey ,trans('privateUserAnalysis.other'), 0, array("value"=> 1, "readonly"=>false))  !!}
                    </div>
                </div>
            </div>
        </div>

        <div style="display:none;" class="margin-top-30">
            <div class="row">
                <div class="col-12">
                    <button id="applyFilters{{ $parameterKey  }}" class="btn btn-flat empatia margin-top-20" type="button">{{ trans('privateUserAnalysis.apply') }}</button>
                </div>
            </div>
        </div>
    </div>

    <div style="margin:10px 30px;">
        <div class="row">
            <div class="col-12 col-lg-6">
                @php
                    $arrayChartLinesKey = [];
                    foreach(!empty($votesByDate) ? $votesByDate :[] as $channel => $datesWithData){
                        foreach(!empty($datesWithData->statistics_by_parameter) ? $datesWithData->statistics_by_parameter : [] as $keyDate => $dataItem){
                            foreach(!empty($dataItem) ? $dataItem : [] as $parameterItem => $value){
                                if(!empty($value)){
                                    $arrayChartLinesKey[trans("privateCbsVoteAnalysis.total_votes")." ".$channel." - ".$parameterItem] = 1;
                                    $arrayChartLinesKey[trans("privateCbsVoteAnalysis.total_voters")." ".$channel." - ".$parameterItem] = 1;
                                }
                            }
                        }
                    }
                @endphp
                <label>{{ trans('privateUserAnalysis.view')  }}</label>
                <select id="selectLine{{ $parameterKey }}" class="form-control">
                    <option value="">{{ trans('privateUserAnalysis.all')  }} </option>
                    @foreach($arrayChartLinesKey as $key => $value)
                        <option value="{{ $key  }}"    >{{ $key  }}</option>
                    @endforeach
                </select>
                <button id="applyFiltersButton{{ $parameterKey  }}" class="btn btn-flat empatia margin-top-20" type="button">{{ trans('privateUserAnalysis.apply') }}</button>
            </div>
        </div>
    </div>

    <div id="chartViewWrapper{{ $parameterKey }}">
        @include('private.cbs.cbVoteAnalysis2.tabVoteAnalysisUserParameters._requestDateChart')
    </div>

    <script>
        $( "#applyFiltersButton{{ $parameterKey  }}" ).click(function() {
            var startDate = $("#daterangepicker{{ $parameterKey  }}").val().split(" - ")[0];
            var endDate   = $("#daterangepicker{{ $parameterKey  }}").val().split(" - ")[1];
            requestAndRenderChart{{ $parameterKey  }}(startDate, endDate, $("#selectLine{{ $parameterKey }}").val()  );
        });

        function requestAndRenderChart{{ $parameterKey  }}(startDate, endDate, selectLineToView){
            selectLineToView = typeof(selectLineToView) != 'undefined' ? selectLineToView : '';
            $.ajax({
                method: 'POST', // Type of response and matches what we said in the route
                url: "{{action('CbsController@getVoteAnalysis')}}", // This is the url we gave in the route
                data: {
                    statistics_type: "votes_by_user_parameters2_chartdates_only",
                    @if(!empty($voteEventKey))
                    vote_event_key: "{{ $voteEventKey }}",
                    @endif
                    @if(!empty($parameterKey))
                    parameter_key: '{{ $parameterKey }}',
                    @endif
                    start_date: startDate,
                    end_date: endDate,
                    selectLineToView: selectLineToView
                },beforeSend: function () {
                    var ajaxLoader = '<div class="chartLoader{{ $parameterKey }}" style="min-height:330px;padding-top:100px;"><center><i class="fa fa-circle-o-notch fa-spin fa-3x fa-fw default-color"></i><span class="sr-only">Loading...</span></center></div>';
                    $("#chartViewWrapper{{ $parameterKey }}").html(ajaxLoader);
                },
                success: function (response) { // What to do if we succeed
                    if(response == "false") {
                        var errorMessage = "{!! trim(preg_replace('/\s\s+/', ' ', trans('privateCbsVoteAnalysis.something_went_wrong'))) !!}";
                        $("#chartViewWrapper{{ $parameterKey }}").html('<div class="chartMessage"><div><i class="fa fa-eye-slash" aria-hidden="true"></i> ' + errorMessage + '</div></div>');
                        toastr.error(errorMessage);
                    } else {
                        $("#chartViewWrapper{{ $parameterKey }}").html(response);
                    }
                    // Remove loader
                    $(".chartLoader{{ $parameterKey }}").remove();
                },
                error: function () { // What to do if we fail
                    var errorMessage = "{!! trim(preg_replace('/\s\s+/', ' ', trans('privateCbsVoteAnalysis.something_went_wrong'))) !!}";
                    $('#tab_'+id+'_content').html('<div class="chartMessage"><div><i class="fa fa-eye-slash" aria-hidden="true"></i> '+errorMessage+'</div></div>');
                    toastr.error(errorMessage);
                    $(".chartLoader").remove();
                }
            });
        }

    </script>
@endif


<!-- 9) Gráfico de cluster de bolas com propostas mais votadas (balanço) por parâmetro (semelhante ao existente) --> {{-- ✔ - Incluir dados que não preencheram o pârametro --}}
@if(!empty($parameterCode) && !empty($votesByTopicParameter) && !empty($parameterKey))
    <div class="box-info text-center margin-bottom-30">
        <div class="box-header">
            <h3 class="box-title"><i class="fa"></i> {{trans('privateCbsVoteAnalysis.statistics_topics_by') }} {{trans('privateCbsVoteAnalysis.and') }} {{$parameterName ?? ''}}</h3>
        </div>
        <div class="box-body">
            <div class="col-md-12" style="height: 600px">
                <!-- Download -->
                <div class="chart-download-wrapper">
                    <a id="downloadProposalsVotesCSV_{{$parameterKey}}" class="btn btn-flat btn-blue pull-right">
                        <i class="fa fa-file-excel-o" aria-hidden="true"></i> {{ trans('privateCbsVoteAnalysis.download_csv') }}
                    </a>
                    <a id="downloadProposalsVotesImage_{{$parameterKey}}"  class="btn btn-flat btn-blue pull-right" style="margin-right:5px;">
                        <i class="fa fa-file-image-o" aria-hidden="true"></i> {{ trans('privateCbsVoteAnalysis.download_image') }}
                    </a>
                </div>
                <div id="proposals_votes_{{$parameterKey}}"></div>
                <!-- Canvas for downloading chart -->
                <canvas id="canvas_proposals_votes_{{$parameterKey}}" style="display: none;"></canvas>
            </div>
        </div>
    </div>
    <script>
        // sample data array
        var data_Proposals_Uptown_{{$parameterKey}} = [
            @foreach($votesByTopicParameter as $topic)
                @foreach($topic->parameter_options as $key => $option)
                    {"value": {{ $option->balance }}, "name": "{{ $topic->title }}", "group": "{{$key}}"},
                @endforeach
            @endforeach
        ];
        var visualization = d3plus.viz()
            .container("#proposals_votes_{{$parameterKey}}")     // container DIV to hold the visualization
            .data(data_Proposals_Uptown_{{$parameterKey}})     // data to use with the visualization
            .type("bubbles")       // visualization type
            .id(["group", "name"]) // nesting keys
            .depth(1)              // 0-based depth
            .size("value")         // key name to size bubbles
            .color("group")        // color by each group
            .font({"family": "Helvetica, Arial, sans-serif", "color": "#000"}).resize(true)
            .draw();

        // Export data for CSV (javascript)
        $("#downloadProposalsVotesCSV_{{$parameterKey}}").click(function () {
            var d = new Date();
            var suffix_name = d.getFullYear() + "_" + (1 + d.getMonth()) + "_" + d.getDate() + "_" + d.getHours() + "_" + d.getMinutes() + "_" + d.getSeconds();
            var filename = "proposals_votes_" + suffix_name + ".csv";
            downloadCSV(data_Proposals_Uptown_{{$parameterKey}}, filename);
        });
        // Download chart to PNG (javascript)
        $("#downloadProposalsVotesImage_{{$parameterKey}}").click(function () {
            var d = new Date();
            var suffix_name = d.getFullYear() + "_" + (1 + d.getMonth()) + "_" + d.getDate() + "_" + d.getHours() + "_" + d.getMinutes() + "_" + d.getSeconds();
            var filename = "proposals_votes_" + suffix_name + ".png";
            $("#canvas_proposals_votes_{{$parameterKey}}").attr("width", $("#proposals_votes_{{$parameterKey}} #d3plus").width());
            $("#canvas_proposals_votes_{{$parameterKey}}").attr("height", $("#proposals_votes_{{$parameterKey}} #d3plus").height());
            var svg = document.querySelector('#proposals_votes_{{$parameterKey}} svg');
            var canvas = document.getElementById('canvas_proposals_votes_{{$parameterKey}}');
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
    </script>
@endif


<!-- 10) Gráfico de propostas com distribuição de votos por parâmetro (igual ao que está feito) --> {{-- ✔ - Incluir dados que não preencheram o pârametro   --}}
@if(!empty($parameterCode) && !empty($votesByTopicParameter))
    <div class="margin-bottom-30">
        <div class="row">
            <div class="col-md-12">
                <div class="box-info">
                    <div class="box-header">
                        <h3 class="box-title">{{ trans('privateCbsVoteAnalysis.statistics_topics_by') }} {{$parameterName ?? ''}}</h3>
                    </div>
                    <div class="box-body">
                        <!-- Download -->
                        <div class="chart-download-wrapper">
                            <a id="downloadStatsTpCSV_{{$parameterKey}}" class="btn btn-flat btn-blue pull-right">
                                <i class="fa fa-file-excel-o" aria-hidden="true"></i> {{ trans('privateCbsVoteAnalysis.download_csv') }}
                            </a>
                            <a id="downloadStatsTpImage_{{$parameterKey}}"  class="btn btn-flat btn-blue pull-right" style="margin-right:5px;">
                                <i class="fa fa-file-image-o" aria-hidden="true"></i> {{ trans('privateCbsVoteAnalysis.download_image') }}
                            </a>
                        </div>
                        <div id="statistics_by_topic_parameter_{{$parameterKey}}" style="min-height: 600px;width: 100%;"></div>
                        <!-- Canvas for downloading chart -->
                        <canvas id="canvas_statistics_by_topic_parameter_{{$parameterKey}}" height="600" style="display: none;"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        @php $j = 0; @endphp
        var statistics_by_topic_data_{{$parameterKey}} = [
            @foreach($votesByTopicParameter as $voteTopic)
                @foreach(!empty($parametersOptions) ? $parametersOptions : [] as $option)
                    {"position": {{ $loop->parent->iteration }}, "{!! trans('privateCbsVoteAnalysis.topic_name') !!}":"{{ $voteTopic->title }}", "type":"{!! $option !!} - {!! trans('privateCbsVoteAnalysis.positive_votes') !!}","{!! trans('privateCbsVoteAnalysis.total_votes') !!}": {{ $voteTopic->parameter_options->{$option}->positive ?? 0 }}},
                    {"position": {{ $loop->parent->iteration }}, "{!! trans('privateCbsVoteAnalysis.topic_name') !!}":"{{ $voteTopic->title }}", "type":"{!! $option !!} - {!! trans('privateCbsVoteAnalysis.negative_votes') !!}","{!! trans('privateCbsVoteAnalysis.total_votes') !!}": {{ (isset($voteTopic->parameter_options->{$option}->negative) ? $voteTopic->parameter_options->{$option}->negative * -1  : 0) }}},
                    @php $j++; @endphp
                @endforeach
                {"position": {{ $loop->iteration }}, "{!! trans('privateCbsVoteAnalysis.topic_name') !!}":"{{ $voteTopic->title }}", "type": "{!! trans('privateCbsVoteAnalysis.no_value') !!} - {!! trans('privateCbsVoteAnalysis.positive_votes') !!}"       ,"{!! trans('privateCbsVoteAnalysis.total_votes') !!}": {{ $voteTopic->parameter_options->no_value->positive ?? 0 }}},
                {"position": {{ $loop->iteration }}, "{!! trans('privateCbsVoteAnalysis.topic_name') !!}":"{{ $voteTopic->title }}", "type": "{!! trans('privateCbsVoteAnalysis.no_value') !!} - {!! trans('privateCbsVoteAnalysis.negative_votes') !!}","{!! trans('privateCbsVoteAnalysis.total_votes') !!}": {{ (isset($voteTopic->parameter_options->no_value->negative) ? $voteTopic->parameter_options->no_value->negative * -1  : 0) }}},
            @endforeach
        ];

        // Update chart height
        $("#statistics_by_topic_parameter_{{$parameterKey}}").css("height","{{ ($j == 0) ? 100 : $j*8 }}px");

        var visualization = d3plus.viz()
            .container("#statistics_by_topic_parameter_{{$parameterKey}}")  // container DIV to hold the visualization
            .data(statistics_by_topic_data_{{$parameterKey}})  // data to use with the visualization
            .type("bar")// visualization type
            .id("type")
            .y("{!! trans('privateCbsVoteAnalysis.topic_name') !!}")         // key to use for y-axis
            .y({"scale": "discrete"}) // Manually set Y-axis to be discrete
            .x({"stacked": true}) // Manually set Y-axis to be discrete
            .x( "{!! trans('privateCbsVoteAnalysis.total_votes') !!}")// key to use for x-axis
            .order("position")
            .font({"family": "Helvetica, Arial, sans-serif", "color": "#000"})
            .draw();

        // Export data for CSV (javascript)
        $("#downloadStatsTpCSV_{{$parameterKey}}").click(function () {
            var d = new Date();
            var suffix_name = d.getFullYear() + "_" + (1 + d.getMonth()) + "_" + d.getDate() + "_" + d.getHours() + "_" + d.getMinutes() + "_" + d.getSeconds();
            var filename = "statistics_by_topic_parameter_" + suffix_name + ".csv";
            downloadCSV(statistics_by_topic_data_{{$parameterKey}}, filename);
        });
        // Download chart to PNG (javascript)
        $("#downloadStatsTpImage_{{$parameterKey}}").click(function () {
            var d = new Date();
            var suffix_name = d.getFullYear() + "_" + (1 + d.getMonth()) + "_" + d.getDate() + "_" + d.getHours() + "_" + d.getMinutes() + "_" + d.getSeconds();
            var filename = "statistics_by_topic_parameter_" + suffix_name + ".png";
            $("#canvas_statistics_by_topic_parameter_{{$parameterKey}}").attr("width", $("#statistics_by_topic_parameter_{{$parameterKey}} #d3plus").width());
            $("#canvas_statistics_by_topic_parameter_{{$parameterKey}}").attr("height", $("#statistics_by_topic_parameter_{{$parameterKey}} #d3plus").height());
            var svg = document.querySelector('#statistics_by_topic_parameter_{{$parameterKey}} svg');
            var canvas = document.getElementById('canvas_statistics_by_topic_parameter_{{$parameterKey}}');
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
    </script>
@endif


<!--
    11) Tabela de propostas com:
            1) Total de votos ‎✔
            2) Total de votantes
            3) Por parâmetro:
                   1) Total de votantes ‎✔
                   2) Balanço ‎✔
                   3) Positivos ‎✔
                   4) Negativos ‎✔
                   5) Total de abstenções (=== no_value) ‎✔
-->
    {{-- ✔ - Download CSV   --}}
    {{-- ✔ - Incluir pesquisa  --}}
    {{-- ✔ - Incluir dados que não preencheram o pârametro --}}
@if(!empty($parameterKey) && !empty($votesByTopicParameter))
<div class="row">
    <div class="col-12">
        <div class="box-info">
            <div class="box-header">
                <h3 class="box-title"><i class="fa"></i> {{ trans('privateCbsVoteAnalysis.statistics_topics_by') }} {{trans('privateCbsVoteAnalysis.and') }} {{$parameterName ?? ''}}</h3>
            </div>
            <div class="box-body">
                <div id="table-statistics_by_topic_downloads_wrapper" class="table-download-wrapper">
                    <a id="tableVotesByTopicParemeter_Download_CSV{{ $parameterKey }}"  class="btn btn-flat btn-blue pull-left margin-bottom-10">
                        <i class="fa fa-file-excel-o" aria-hidden="true"></i> {{ trans('privateCbsVoteAnalysis.download_csv') }}
                    </a>
                </div>
                <div class="clearfix"></div>
                <div class="table-responsive">
                    <table id="proposals_list_{{$parameterKey}}" class="table table-responsive  table-striped">
                    <thead>
                        <tr>
                            <th rowspan="2">{{ trans('privateCbsVoteAnalysis.title') }}</th>
                            <th rowspan="2" style="width: 20px;">{{ trans('privateCbsVoteAnalysis.totals') }}</th>
                            <th rowspan="2" style="width: 20px;"><i class="fa fa-users" aria-hidden="true" title="{{ trans('privateCbsVoteAnalysis.voters') }}"></i></th>
                            @foreach(!empty($parametersOptions) ? $parametersOptions : [] as $option)
                                <th colspan="3">{{$option}}</th>
                            @endforeach
                            <th colspan="3">{{ trans('privateCbsVoteAnalysis.no_value') }}</th>
                        </tr>
                        <tr>
                            @foreach(!empty($parametersOptions) ? $parametersOptions : [] as $option)
                                <th style="width: 20px;">B</th>
                                <th style="width: 20px;">+</th>
                                <th style="width: 20px;">-</th>
                            @endforeach
                            <th style="width: 20px;">B</th>
                            <th style="width: 20px;">+</th>
                            <th style="width: 20px;">-</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
                </div>
                <script>
                    var votesByTopicParameter_{{$parameterKey}} = [
                    @if(!empty($votesByTopicParameter))
                        @foreach($votesByTopicParameter as $vote)
                        {
                            "vote_title": "{{$vote->title}}",
                            "total": "{{ $vote->total }}",
                            "votersCounter": "{{ $vote->votersCounter }}",
                            @foreach(!empty($parametersOptions) ? $parametersOptions : [] as $option)
                                "balance_{{ $option }}": "{{ $vote->parameter_options->{$option}->balance ?? 0 }}",
                                "positive_{{ $option }}": "{{ $vote->parameter_options->{$option}->positive ?? 0 }}",
                                "negative_{{ $option }}": "{{ $vote->parameter_options->{$option}->negative ?? 0 }}",
                            @endforeach
                            "balance_no_value": "{{ $vote->parameter_options->no_value->balance ?? 0 }}",
                            "positive_no_value": "{{ $vote->parameter_options->no_value->positive ?? 0 }}",
                            "negative_no_value": "{{ $vote->parameter_options->no_value->negative ?? 0 }}",
                        },
                        @endforeach
                    @endif
                    ];
                    //Load  datatable
                    var oTblReport = $("#proposals_list_{{$parameterKey}}")
                    oTblReport.DataTable ({
                        data : votesByTopicParameter_{{$parameterKey}},
                        columns : [
                            { "data" : "vote_title" },
                            { "data" : "total" },
                            { "data" : "votersCounter" },
                            @foreach(!empty($parametersOptions) ? $parametersOptions : [] as $option)
                                { "data" : "balance_{{ $option }}" } ,
                                { "data" : "positive_{{ $option }}" } ,
                                { "data" : "negative_{{ $option }}" } ,
                            @endforeach
                            { "data" : "balance_no_value" } ,
                            { "data" : "positive_no_value" } ,
                            { "data" : "negative_no_value" } ,
                        ],
                        paging: false,
                        language: {
                            url: '{!! asset('/datatableLang/'.Session::get('LANG_CODE').'.json') !!}',
                            search: '<a class="btn searchBtn" id="searchBtn"><i class="fa fa-search"></i></a>'
                        },
                        stateSave: false,
                        order: [['1', 'desc']]
                    });

                    // Export data for CSV (javascript)
                    $( "#tableVotesByTopicParemeter_Download_CSV{{ $parameterKey }}" ).click(function() {
                        var d = new Date();
                        var suffix_name = d.getFullYear()+"_"+(1+d.getMonth())+"_"+d.getDate()+"_"+d.getHours()+"_"+d.getMinutes()+"_"+d.getSeconds();
                        var filename = "votes_by_topic_parameter_"+suffix_name+"_table.csv";
                        downloadCSV( votesByTopicParameter_{{$parameterKey}}, filename);
                    });
                </script>
            </div>
        </div>
    </div>
</div>
@endif

@include('private.cbs.cbVoteAnalysis2.details.cbDetailsScript')
