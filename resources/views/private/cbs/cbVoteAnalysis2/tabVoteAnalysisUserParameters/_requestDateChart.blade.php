<!-- Download -->
<div style="margin:10px 30px;">
    <div class="row">
        <div class="col-12">
            <div class="chart-download-wrapper{{ $parameterKey }}">
                <a id="downloadCSV{{ $parameterKey }}"  class="btn btn-flat btn-blue pull-right">
                    <i class="fa fa-file-excel-o" aria-hidden="true"></i> {{ trans('privateCbsVoteAnalysis.download_csv') }}
                </a>
                <a id="downloadImage{{ $parameterKey }}"  class="btn btn-flat btn-blue pull-right" style="margin-right:5px;">
                    <i class="fa fa-file-image-o" aria-hidden="true"></i> {{ trans('privateCbsVoteAnalysis.download_image') }}
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Chart -->
<div id="statistics_by_date{{ $parameterKey }}" style="height:600px;" class="margin-bottom-30"></div>

<!-- Canvas for downloading chart -->
<canvas id="canvas_statistics_by_date{{ $parameterKey }}" height="600" style="display: none;"></canvas>

<script>
    $( document ).ready(function() {
        // Date Range pickers
        $('input[name="dr_vote_analysis_date2{{ $parameterKey }}"]').daterangepicker({
            timePicker: false,
            timePickerIncrement: 5,
            locale: {
                format: 'YYYY-MM-DD'
            },
            @if(!empty($voteEventObj->start_date))
            minDate: "{{$voteEventObj->start_date}}", /*  minDate: new Date(2017,11,1),*/
            @endif
                    @if(!empty($voteEventObj->end_date))
            maxDate: "{{$voteEventObj->end_date}}",
            @endif
        }, myCallback);

        function myCallback(start, end) {
            var startDate = start.format('YYYY-MM-DD');
            var endDate = end.format('YYYY-MM-DD');
            requestAndRenderChart{{ $parameterKey  }}(startDate, endDate, $("#selectLine{{ $parameterKey }}").val() );
            $('#oneWeek').prop('checked', false);
            $('#oneMonth').prop('checked', false);
            $('#twoMonths').prop('checked', false);
        }

        //var dateArrayRange = getCurrentDateRangeForHoursCharts();
        // startDate = dateArrayRange[0];
        // endDate = dateArrayRange[1];

        // Date Range pickers
        /*
        $('input[name="dr_vote_analysis_date2"]').daterangepicker({
            timePicker: true,
            timePicker24Hour: true,
            timePickerIncrement: 5,
            startDate: startDate,
            endDate: endDate,
            dateLimit: {
                days: 1
            },
            locale: {
                format: 'YYYY-MM-DD HH:mm'
            },
        @if(!empty($voteEventObj->start_date))
        minDate: "{{$voteEventObj->start_date}}",
        @endif
        @if(!empty($voteEventObj->end_date))
        maxDate: "{{$voteEventObj->end_date}}",
                @endif
        }, myCallback2);

        function myCallback2(start, end) {
            var startDate = start.format('YYYY-MM-DD');
            var endDate = end.format('YYYY-MM-DD');
            requestChart2(startDate, endDate);
        }
        */
    });



    // Data         // Channels = ['kiosk','pc','mobile','tablet','other','in_person','sms'];
    var statistics_by_date_data{{ $parameterKey }} = [];

    // Total
    // if ($('#total{{ $parameterKey }}').is(":checked")) {
    @php
        $arrayChartLinesKey = [];
    @endphp
    var statistics_total = [
        @foreach(!empty($votesByDate) ? $votesByDate :[] as $channel => $datesWithData)
            @foreach(!empty($datesWithData->statistics_by_parameter) ? $datesWithData->statistics_by_parameter : [] as $keyDate => $dataItem)
                @foreach(!empty($dataItem) ? $dataItem : [] as $parameterItem => $value)

                    @if( (!empty($value) &&  empty($selectLineToView))
                    ||   (!empty($value) && !empty($selectLineToView) && $selectLineToView == trans("privateCbsVoteAnalysis.total_votes")." ".$channel." - ".$parameterItem))
                    {
                        '{!! trans('privateCbsVoteAnalysis.date') !!}': "{{ $keyDate }}",
                        "name": '{!! trans("privateCbsVoteAnalysis.total_votes") !!} {!! $channel !!} - {!! $parameterItem !!}',
                        '{!! trans('privateCbsVoteAnalysis.votes') !!}': {{ $value->total }}
                    },
                    @php
                        $arrayChartLinesKey[trans("privateCbsVoteAnalysis.total_votes")." ".$channel." - ".$parameterItem] = 1;
                    @endphp
                    @endif

                    @if( (!empty($value) &&  empty($selectLineToView))
                    ||   (!empty($value) && !empty($selectLineToView) && $selectLineToView == trans("privateCbsVoteAnalysis.total_voters")." ".$channel." - ".$parameterItem))
                        {
                            '{!! trans('privateCbsVoteAnalysis.date') !!}': "{{ $keyDate }}",
                            "name": '{!! trans("privateCbsVoteAnalysis.total_voters") !!} {!! $channel !!} - {!! $parameterItem !!}',
                            '{!! trans('privateCbsVoteAnalysis.votes') !!}': {{ count(json_decode( json_encode($value->voters), true)) }}
                        },
                            @php
                                $arrayChartLinesKey[trans("privateCbsVoteAnalysis.total_voters")." ".$channel." - ".$parameterItem] = 1;
                            @endphp
                    @endif

                @endforeach
            @endforeach
        @endforeach
    ];

    var start = new Date($("#daterangepicker{{ $parameterKey }}").val().split(" - ")[0]);
    var end = new Date($("#daterangepicker{{ $parameterKey }}").val().split(" - ")[1]);
    var statistics_total_zeros = [];
    while (start <= end) {
        var currentDate = new Date(start);
        var datestring = currentDate.getFullYear() + "-" + addZero((currentDate.getMonth() + 1)) + "-" + addZero(currentDate.getDate()); // + " " + d.getHours() + ":" + d.getMinutes();
        @foreach($arrayChartLinesKey as $key => $value)
            var founded = false;
            for(i =0 ; i< statistics_total.lenght; i++ ){
                if(  (statistics_total[i].Data == start  && statistics_total[i].Name == "{!! $key !!}")
                ){
                    founded = true;
                }
            }
            if(founded == false){
                var item_statistics_total_zeros = {
                    '{!! trans('privateCbsVoteAnalysis.date') !!}': datestring,
                    "name": '{!! $key !!}',
                    '{!! trans('privateCbsVoteAnalysis.votes') !!}': 0
                };
                statistics_total.push(item_statistics_total_zeros);
            }
        @endforeach

        // Increment one day
        var newDate = start.setDate(start.getDate() + 1);
        start = new Date(newDate);
    }

    // var statistics_total_result = joinObjects(statistics_total_zeros, statistics_total);
    // statistics_by_date_data{{ $parameterKey }} = statistics_by_date_data{{ $parameterKey }}.concat(statistics_total_result);
    statistics_by_date_data{{ $parameterKey }} =  statistics_total;
    // }


    // Setting Y range - bug fix when values are 0's
    var chartRange = [0, 10];
    for (var i = 0; i < statistics_by_date_data{{ $parameterKey }}.length; i++) {
        if (statistics_by_date_data{{ $parameterKey }}[i].{!! trim(preg_replace('/\s\s+/', ' ',trans('privateCbsVoteAnalysis.votes'))) !!} != 0) {
            chartRange = false;
        }
    }

    // Chart D3plus
    d3plus.viz()
        .container("#statistics_by_date{{ $parameterKey }}")
        .data(statistics_by_date_data{{ $parameterKey }})
        .type("line")
        .id("name")
        .color({
            "value": "name"
        })
        .y({
            "value": "{!! trans('privateCbsVoteAnalysis.votes') !!}",
            "range": chartRange
        })
        .x('{!! trans('privateCbsVoteAnalysis.date') !!}')
        .legend({
            "value": true,
            "size": 50
        })
        .font({"family": "Helvetica, Arial, sans-serif", "color": "#000"})
        .resize(true)
        .time("{!! trans('privateCbsVoteAnalysis.date') !!}")
        .timeline({"handles": false})
        .draw();

    // Error message if there are no results in Vote Analysis  :'(
    if (statistics_by_date_data{{ $parameterKey }}.length == 0) {
        var warningMessage = "{!! trim(preg_replace('/\s\s+/', ' ', trans('privateCbsVoteAnalysis.no_data_Available'))) !!}";
        $('#statistics_by_date{{ $parameterKey }}').html('<div class="chartMessage"><div><i class="fa fa-eye-slash" aria-hidden="true"></i> ' + warningMessage + '</div></div>');
        toastr.warning(warningMessage);
        $(".chart-download-wrapper{{ $parameterKey }}").remove();
    } else {
        // Export data for CSV (javascript)
        $("#downloadCSV{{ $parameterKey }}").click(function () {
            var d = new Date();
            var suffix_name = d.getFullYear() + "_" + (1 + d.getMonth()) + "_" + d.getDate() + "_" + d.getHours() + "_" + d.getMinutes() + "_" + d.getSeconds();
            var filename = "statistics_by_date_" + suffix_name + ".csv";
            downloadCSV(statistics_by_date_data{{ $parameterKey }}, filename);
        });
        // Download chart to PNG (javascript)
        $("#downloadImage{{ $parameterKey }}").click(function () {
            var d = new Date();
            var suffix_name = d.getFullYear() + "_" + (1 + d.getMonth()) + "_" + d.getDate() + "_" + d.getHours() + "_" + d.getMinutes() + "_" + d.getSeconds();
            var filename = "statistics_by_date_" + suffix_name + ".png";
            $("#canvas_statistics_by_date{{ $parameterKey }}").attr("width", $("#statistics_by_date{{ $parameterKey }} #d3plus").width());
            $("#canvas_statistics_by_date{{ $parameterKey }}").attr("height", $("#statistics_by_date{{ $parameterKey }} #d3plus").height());
            var svg = document.querySelector('#statistics_by_date{{ $parameterKey }} svg');
            var canvas = document.getElementById('canvas_statistics_by_date{{ $parameterKey }}');
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
    }


</script>