<!-- Download -->
<div class="row">
    <div class="col-12">
        <div class="chart-download-wrapper">
            <a id="downloadCSV"  class="btn btn-flat btn-blue pull-right">
                <i class="fa fa-file-excel-o" aria-hidden="true"></i> {{ trans('privateCbsVoteAnalysis.download_csv') }}
            </a>
            <a id="downloadImage"  class="btn btn-flat btn-blue pull-right" style="margin-right:5px;">
                <i class="fa fa-file-image-o" aria-hidden="true"></i> {{ trans('privateCbsVoteAnalysis.download_image') }}
            </a>
        </div>
    </div>
</div>

<!-- Chart -->
<div id="statistics_by_date" style="height: 600px;"></div>

<!-- Canvas for downloading chart -->
<canvas id="canvas_statistics_by_date" height="600" style="display: none;"></canvas>

<!-- JavaScript for Chart -->
<script>
    // Update date range picker
    // Date Range pickers
    if( window.voteEventKey != "{{ $voteEventKey }}" ){

        $('input[name="dr_vote_analysis_date2"]').datepicker('destroy');
        $('input[name="dr_vote_analysis_hour"]').datepicker('destroy');

        $('input[name="dr_vote_analysis_date2"]').daterangepicker({
            timePicker: false,
            timePickerIncrement: 1,
            locale: {
                format: 'YYYY-MM-DD'
            },
            @if(!empty($voteEventObj->start_date))
            startDate: "{{$voteEventObj->start_date}}",
            @endif
            @if(!empty($voteEventObj->end_date))
            endDate: "{{$voteEventObj->end_date}}",
            @endif
            @if(!empty($voteEventObj->start_date))
            minDate: "{{$voteEventObj->start_date}}",
            @endif
            @if(!empty($voteEventObj->end_date))
            maxDate: "{{$voteEventObj->end_date}}",
            @endif
        }, myCallback);

        function myCallback(start, end) {
            var startDate = start.format('YYYY-MM-DD');
            var endDate = end.format('YYYY-MM-DD');
            requestChart(startDate, endDate);
            $('#oneWeek').prop('checked', false);
            $('#oneMonth').prop('checked', false);
            $('#twoMonths').prop('checked', false);
        }
        var dateArrayRange = getCurrentDateRangeForHoursCharts();
        startDate = dateArrayRange[0];
        endDate = dateArrayRange[1];
        // Date Range pickers
        $('input[name="dr_vote_analysis_hour"]').daterangepicker({
            timePicker: true,
            timePicker24Hour: true,
            timePickerIncrement: 1,
            @if(!empty($voteEventObj->start_date)  )
            startDate: "{{$voteEventObj->start_date}}",
            endDate: "{{  Carbon\Carbon::parse($voteEventObj->start_date)->addDays(1) }}",
            @elseif(!empty($voteEventObj->end_date))
            startDate: "{{ Carbon\Carbon::parse($voteEventObj->end_date)->subDays(1) }}",
            endDate: "{{  $voteEventObj->end_date }}",
            @else
            startDate: startDate,
            endDate: endDate,
            @endif
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

        window.voteEventKey = "{{ $voteEventKey }}";
        requestChart2("{{$voteEventObj->start_date}}", "{{$voteEventObj->end_date}}");

    } else {
        window.voteEventKey = "{{ $voteEventKey }}";

        // Charts - Initialization to Zero's
        var start = new Date($("#daterangepicker1").val().split(" - ")[0]);
        var end = new Date($("#daterangepicker1").val().split(" - ")[1]);
        var statistics_by_date_web_zeros = [];
        var statistics_total_zeros = [];
        var statistics_positive_zeros = [];
        var statistics_negative_zeros = [];
        var statistics_balance_zeros = [];
        var statistics_by_date_pc_zeros = [];
        var statistics_by_date_sms_zeros = [];
        var statistics_by_date_kiosk_zeros = [];
        var statistics_by_date_mobile_zeros = [];
        var statistics_by_date_tablet_zeros = [];
        var statistics_by_date_other_zeros = [];
        var statistics_by_date_in_person_zeros = [];
        var statistics_by_date_count_voters_zeros = [];
        while (start <= end) {
            // current date
            var currentDate = new Date(start);
            var datestring = currentDate.getFullYear() + "-" + addZero((currentDate.getMonth() + 1)) + "-" + addZero(currentDate.getDate()); // + " " + d.getHours() + ":" + d.getMinutes();
            // web votes ------------------
            var item_statistics_by_date_web = {
                '{!! trans('privateCbsVoteAnalysis.date') !!}': datestring,
                "name": '{!! trans('privateCbsVoteAnalysis.total_votes_web') !!}',
                '{!! trans('privateCbsVoteAnalysis.votes') !!}': 0
            };
            statistics_by_date_web_zeros.push(item_statistics_by_date_web);
            // Total ------------------
            var item_statistics_total = {
                '{!! trans('privateCbsVoteAnalysis.date') !!}': datestring,
                "name": '{!! trans('privateCbsVoteAnalysis.total') !!}',
                '{!! trans('privateCbsVoteAnalysis.votes') !!}': 0
            };
            statistics_total_zeros.push(item_statistics_total);
            // Positive ------------------
            var item_statistics_positive = {
                '{!! trans('privateCbsVoteAnalysis.date') !!}': datestring,
                "name": '{!! trans('privateCbsVoteAnalysis.positives') !!}',
                '{!! trans('privateCbsVoteAnalysis.votes') !!}': 0
            };
            statistics_positive_zeros.push(item_statistics_positive);
            // Negative ------------------
            var item_statistics_negative = {
                '{!! trans('privateCbsVoteAnalysis.date') !!}': datestring,
                "name": '{!! trans('privateCbsVoteAnalysis.negatives') !!}',
                '{!! trans('privateCbsVoteAnalysis.votes') !!}': 0
            };
            statistics_negative_zeros.push(item_statistics_negative);
            // Balance ------------------
            var item_statistics_balance = {
                '{!! trans('privateCbsVoteAnalysis.date') !!}': datestring,
                "name": '{!! trans('privateCbsVoteAnalysis.balance') !!}',
                '{!! trans('privateCbsVoteAnalysis.votes') !!}': 0
            };
            statistics_balance_zeros.push(item_statistics_balance);
            // PC ------------------
            var item_statistics_by_date_pc_zeros = {
                '{!! trans('privateCbsVoteAnalysis.date') !!}': datestring,
                "name": '{!! trans('privateCbsVoteAnalysis.channel_pc') !!}',
                '{!! trans('privateCbsVoteAnalysis.votes') !!}': 0
            };
            statistics_by_date_pc_zeros.push(item_statistics_by_date_pc_zeros);
            // SMS ------------------
            var item_statistics_by_date_sms_zeros = {
                '{!! trans('privateCbsVoteAnalysis.date') !!}': datestring,
                "name": '{!! trans('privateCbsVoteAnalysis.channel_sms') !!}',
                '{!! trans('privateCbsVoteAnalysis.votes') !!}': 0
            };
            statistics_by_date_sms_zeros.push(item_statistics_by_date_sms_zeros);
            // Kiosk ------------------
            var item_statistics_by_date_kiosk_zeros = {
                '{!! trans('privateCbsVoteAnalysis.date') !!}': datestring,
                "name": '{!! trans('privateCbsVoteAnalysis.channel_kiosk') !!}',
                '{!! trans('privateCbsVoteAnalysis.votes') !!}': 0
            };
            statistics_by_date_kiosk_zeros.push(item_statistics_by_date_kiosk_zeros);
            // Mobile ------------------
            var item_statistics_by_date_mobile_zeros = {
                '{!! trans('privateCbsVoteAnalysis.date') !!}': datestring,
                "name": '{!! trans('privateCbsVoteAnalysis.channel_mobile') !!}',
                '{!! trans('privateCbsVoteAnalysis.votes') !!}': 0
            };
            statistics_by_date_mobile_zeros.push(item_statistics_by_date_mobile_zeros);
            // Tablet ------------------
            var item_statistics_by_date_tablet_zeros = {
                '{!! trans('privateCbsVoteAnalysis.date') !!}': datestring,
                "name": '{!! trans('privateCbsVoteAnalysis.channel_tablet') !!}',
                '{!! trans('privateCbsVoteAnalysis.votes') !!}': 0
            };
            statistics_by_date_tablet_zeros.push(item_statistics_by_date_tablet_zeros);
            // Other ------------------
            var item_statistics_by_date_other_zeros = {
                '{!! trans('privateCbsVoteAnalysis.date') !!}': datestring,
                "name": '{!! trans('privateCbsVoteAnalysis.channel_other') !!}',
                '{!! trans('privateCbsVoteAnalysis.votes') !!}': 0
            };
            statistics_by_date_other_zeros.push(item_statistics_by_date_other_zeros);
            // In person ------------------
            var item_statistics_by_date_in_person_zeros = {
                '{!! trans('privateCbsVoteAnalysis.date') !!}': datestring,
                "name": '{!! trans('privateCbsVoteAnalysis.channel_in_person') !!}',
                '{!! trans('privateCbsVoteAnalysis.votes') !!}': 0
            };
            statistics_by_date_in_person_zeros.push(item_statistics_by_date_in_person_zeros);
            // Count voters ------------------
            var item_statistics_by_date_count_voters_zeros = {
                '{!! trans('privateCbsVoteAnalysis.date') !!}': datestring,
                "name": '{!! trans('privateCbsVoteAnalysis.count_voters') !!}',
                '{!! trans('privateCbsVoteAnalysis.votes') !!}': 0
            };
            statistics_by_date_count_voters_zeros.push(item_statistics_by_date_count_voters_zeros);
            // Increment one day
            var newDate = start.setDate(start.getDate() + 1);
            start = new Date(newDate);
        }

        // Data         // Channels = ['kiosk','pc','mobile','tablet','other','in_person','sms'];
        var statistics_by_date_data = [];

        // Total Web (PC+Mobile+Tablet)
        if ($('#web').is(":checked")) {
                    @php
                        $arrayWeb = [];
                        foreach(!empty($votesByDate->total->pc) ? $votesByDate->total->pc : [] as $date => $voteValue){
                            $arrayWeb[$date] = $voteValue;
                        }
                        foreach(!empty($votesByDate->total->mobile) ? $votesByDate->total->mobile : [] as $date => $voteValue){
                            $arrayWeb[$date] = empty($arrayWeb[$date]) ? $voteValue : $voteValue + $arrayWeb[$date];
                        }
                        foreach(!empty($votesByDate->total->tablet) ? $votesByDate->total->tablet : [] as $date => $voteValue){
                            $arrayWeb[$date] = empty($arrayWeb[$date]) ? $voteValue : $voteValue + $arrayWeb[$date];
                        }
                    @endphp
            var statistics_by_date_web = [
                            @foreach(!empty($arrayWeb) ?$arrayWeb : [] as $date => $voteValue)
                    {
                        '{!! trans('privateCbsVoteAnalysis.date') !!}': "{{ $date }}",
                        "name": '{!! trans('privateCbsVoteAnalysis.total_votes_web') !!}',
                        '{!! trans('privateCbsVoteAnalysis.votes') !!}': {{ $voteValue }}
                    },
                        @endforeach
                ];
            var statistics_by_date_web_result = joinObjects(statistics_by_date_web_zeros, statistics_by_date_web);
            statistics_by_date_data = statistics_by_date_data.concat(statistics_by_date_web_result);
        }

        // Total
        if ($('#total').is(":checked")) {
            var statistics_total = [
                    @foreach($votesByDate->total->all_votes as $date => $voteValue)
                {
                    '{!! trans('privateCbsVoteAnalysis.date') !!}': "{{ $date }}",
                    "name": '{!! trans('privateCbsVoteAnalysis.all_votes') !!}',
                    '{!! trans('privateCbsVoteAnalysis.votes') !!}': {{ $voteValue }}
                },
                @endforeach
            ];
            var statistics_total_result = joinObjects(statistics_total_zeros, statistics_total);
            statistics_by_date_data = statistics_by_date_data.concat(statistics_total_result);
        }

        // Positive
        if ($('#positive').is(":checked")) {
            var statistics_positive = [
                    @foreach($votesByDate->positive->all_votes as $date => $voteValue)
                {
                    '{!! trans('privateCbsVoteAnalysis.date') !!}': "{{ $date }}",
                    "name": '{!! trans('privateCbsVoteAnalysis.positives') !!}',
                    '{!! trans('privateCbsVoteAnalysis.votes') !!}': {{ $voteValue }}
                },
                @endforeach
            ];
            var statistics_positive_result = joinObjects(statistics_positive_zeros, statistics_positive);
            statistics_by_date_data = statistics_by_date_data.concat(statistics_positive_result);
        }

        // Negative
        if ($('#negative').is(":checked")) {
            var statistics_negative = [
                    @foreach($votesByDate->negative->all_votes as $date => $voteValue)
                {
                    '{!! trans('privateCbsVoteAnalysis.date') !!}': "{{ $date }}",
                    "name": '{!! trans('privateCbsVoteAnalysis.negatives') !!}',
                    '{!! trans('privateCbsVoteAnalysis.votes') !!}': {{ $voteValue }}
                },
                @endforeach
            ];
            var statistics_negative_result = joinObjects(statistics_negative_zeros, statistics_negative);
            statistics_by_date_data = statistics_by_date_data.concat(statistics_negative_result);
        }

        // Balance
        if ($('#balance').is(":checked")) {
            var statistics_balance = [
                    @foreach($votesByDate->balance->all_votes as $date => $voteValue)
                {
                    '{!! trans('privateCbsVoteAnalysis.date') !!}': "{{ $date }}",
                    "name": '{!! trans('privateCbsVoteAnalysis.balance') !!}',
                    '{!! trans('privateCbsVoteAnalysis.votes') !!}': {{ $voteValue }}
                },
                @endforeach
            ];
            var statistics_balance_result = joinObjects(statistics_balance_zeros, statistics_balance);
            statistics_by_date_data = statistics_by_date_data.concat(statistics_balance_result);
        }

        // PC
        if ($('#pc').is(":checked")) {
            var statistics_by_date_pc = [
                    @foreach(!empty($votesByDate->total->pc) ? $votesByDate->total->pc : [] as $date => $voteValue)
                {
                    '{!! trans('privateCbsVoteAnalysis.date') !!}': "{{ $date }}",
                    "name": '{!! trans('privateCbsVoteAnalysis.channel_pc') !!}',
                    '{!! trans('privateCbsVoteAnalysis.votes') !!}': {{ $voteValue }}
                },
                @endforeach
            ];
            var statistics_by_date_pc_result = joinObjects(statistics_by_date_pc_zeros, statistics_by_date_pc);
            statistics_by_date_data = statistics_by_date_data.concat(statistics_by_date_pc_result);
        }

        // Sms
        if ($('#sms').is(":checked")) {
            var statistics_by_date_sms = [
                    @foreach(!empty($votesByDate->total->sms) ? $votesByDate->total->sms : [] as $date => $voteValue)
                {
                    '{!! trans('privateCbsVoteAnalysis.date') !!}': "{{ $date }}",
                    "name": '{!! trans('privateCbsVoteAnalysis.channel_sms') !!}',
                    '{!! trans('privateCbsVoteAnalysis.votes') !!}': {{ $voteValue }}
                },
                @endforeach
            ];
            var statistics_by_date_sms_result = joinObjects(statistics_by_date_sms_zeros, statistics_by_date_sms);
            statistics_by_date_data = statistics_by_date_data.concat(statistics_by_date_sms_result);
        }

        // Kiosk
        if ($('#kiosk').is(":checked")) {
            var statistics_by_date_kiosk = [
                    @foreach(!empty($votesByDate->total->kiosk) ? $votesByDate->total->kiosk : [] as $date => $voteValue)
                {
                    '{!! trans('privateCbsVoteAnalysis.date') !!}': "{{ $date }}",
                    "name": '{!! trans('privateCbsVoteAnalysis.channel_kiosk') !!}',
                    '{!! trans('privateCbsVoteAnalysis.votes') !!}': {{ $voteValue }}
                },
                @endforeach
            ];
            var statistics_by_date_kiosk_result = joinObjects(statistics_by_date_kiosk_zeros, statistics_by_date_kiosk);
            statistics_by_date_data = statistics_by_date_data.concat(statistics_by_date_kiosk_result);
        }

        // Mobile
        if ($('#mobile').is(":checked")) {
            var statistics_by_date_mobile = [
                    @foreach(!empty($votesByDate->total->mobile) ? $votesByDate->total->mobile : [] as $date => $voteValue)
                {
                    '{!! trans('privateCbsVoteAnalysis.date') !!}': "{{ $date }}",
                    "name": '{!! trans('privateCbsVoteAnalysis.channel_mobile') !!}',
                    '{!! trans('privateCbsVoteAnalysis.votes') !!}': {{ $voteValue }}
                },
                @endforeach
            ];
            var statistics_by_date_mobile_result = joinObjects(statistics_by_date_mobile_zeros, statistics_by_date_mobile);
            statistics_by_date_data = statistics_by_date_data.concat(statistics_by_date_mobile_result);
        }

        // Tablet
        if ($('#tablet').is(":checked")) {
            var statistics_by_date_tablet = [
                    @foreach(!empty($votesByDate->total->tablet) ? $votesByDate->total->tablet : [] as $date => $voteValue)
                {
                    '{!! trans('privateCbsVoteAnalysis.date') !!}': "{{ $date }}",
                    "name": '{!! trans('privateCbsVoteAnalysis.channel_tablet') !!}',
                    '{!! trans('privateCbsVoteAnalysis.votes') !!}': {{ $voteValue }}
                },
                @endforeach
            ];
            var statistics_by_date_tablet_result = joinObjects(statistics_by_date_tablet_zeros, statistics_by_date_tablet);
            statistics_by_date_data = statistics_by_date_data.concat(statistics_by_date_tablet_result);
        }

        // Other
        if ($('#other').is(":checked")) {
            var statistics_by_date_other = [
                    @foreach(!empty($votesByDate->total->other) ? $votesByDate->total->other : [] as $date => $voteValue)
                {
                    '{!! trans('privateCbsVoteAnalysis.date') !!}': "{{ $date }}",
                    "name": '{!! trans('privateCbsVoteAnalysis.channel_other') !!}',
                    '{!! trans('privateCbsVoteAnalysis.votes') !!}': {{ $voteValue }}
                },
                @endforeach
            ];
            var statistics_by_date_other_result = joinObjects(statistics_by_date_other_zeros, statistics_by_date_other);
            statistics_by_date_data = statistics_by_date_data.concat(statistics_by_date_other_result);
        }

        // In person
        if ($('#in_person').is(":checked")) {
            var statistics_by_date_in_person = [
                    @foreach(!empty($votesByDate->total->in_person) ? $votesByDate->total->in_person : [] as $date => $voteValue)
                {
                    '{!! trans('privateCbsVoteAnalysis.date') !!}': "{{ $date }}",
                    "name": '{!! trans('privateCbsVoteAnalysis.channel_in_person') !!}',
                    '{!! trans('privateCbsVoteAnalysis.votes') !!}': {{ $voteValue }}
                },
                @endforeach
            ];
            var statistics_by_date_in_person_result = joinObjects(statistics_by_date_in_person_zeros, statistics_by_date_in_person);
            statistics_by_date_data = statistics_by_date_data.concat(statistics_by_date_in_person_result);
        }

        // Count voters
        if ($('#count_voters').is(":checked")) {
            var statistics_by_date_count_voters = [
                    @foreach(!empty($votesByDate->total->voters_counter) ? $votesByDate->total->voters_counter : [] as $date => $countValue)
                {
                    '{!! trans('privateCbsVoteAnalysis.date') !!}': "{{ $date }}",
                    "name": '{!! trans('privateCbsVoteAnalysis.count_voters') !!}',
                    '{!! trans('privateCbsVoteAnalysis.votes') !!}': {{ $countValue }}
                },
                @endforeach
            ];
            var statistics_by_date_count_voters_result = joinObjects(statistics_by_date_count_voters_zeros, statistics_by_date_count_voters);
            statistics_by_date_data = statistics_by_date_data.concat(statistics_by_date_count_voters_result);
        }

        // Setting Y range - bug fix when values are 0's
        var chartRange = [0, 10];
        for (var i = 0; i < statistics_by_date_data.length; i++) {
            if (statistics_by_date_data[i].{!! trim(preg_replace('/\s\s+/', ' ',trans('privateCbsVoteAnalysis.votes'))) !!} != 0) {
                chartRange = false;
            }
        }

        // Chart D3plus
        d3plus.viz()
            .container("#statistics_by_date")
            .data(statistics_by_date_data)
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
        if (statistics_by_date_data.length == 0) {
            var warningMessage = "{!! trim(preg_replace('/\s\s+/', ' ', trans('privateCbsVoteAnalysis.no_data_Available'))) !!}";
            $('#statistics_by_date').html('<div class="chartMessage"><div><i class="fa fa-eye-slash" aria-hidden="true"></i> ' + warningMessage + '</div></div>');
            toastr.warning(warningMessage);
            $(".chart-download-wrapper").remove();
        } else {
            // Export data for CSV (javascript)
            $("#downloadCSV").click(function () {
                var d = new Date();
                var suffix_name = d.getFullYear() + "_" + (1 + d.getMonth()) + "_" + d.getDate() + "_" + d.getHours() + "_" + d.getMinutes() + "_" + d.getSeconds();
                var filename = "statistics_by_date_" + suffix_name + ".csv";
                downloadCSV(statistics_by_date_data, filename);
            });
            // Download chart to PNG (javascript)
            $("#downloadImage").click(function () {
                var d = new Date();
                var suffix_name = d.getFullYear() + "_" + (1 + d.getMonth()) + "_" + d.getDate() + "_" + d.getHours() + "_" + d.getMinutes() + "_" + d.getSeconds();
                var filename = "statistics_by_date_" + suffix_name + ".png";
                $("#canvas_statistics_by_date").attr("width", $("#statistics_by_date #d3plus").width());
                $("#canvas_statistics_by_date").attr("height", $("#statistics_by_date #d3plus").height());
                var svg = document.querySelector('#statistics_by_date svg');
                var canvas = document.getElementById('canvas_statistics_by_date');
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

    }

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

    @if( !empty($statisticsTotalSummary->total_users_voted) )
        $("#total_voters").html("{{ $statisticsTotalSummary->total_users_voted }}");
        $("#moreDetails").show();
    @endif
    @if( !empty($statisticsTotalSummary->total) )
        $("#total_votes").html("{{ $statisticsTotalSummary->total }}");
    @endif
    @if( !empty($statisticsTotalSummary->total_positives) )
        $("#total_positives_votes").html("{{ $statisticsTotalSummary->total_positives }}");
    @endif
    @if( !empty($statisticsTotalSummary->total_positives) )
        $("#total_negative_votes").html("{{ $statisticsTotalSummary->total_negatives }}");
    @endif
</script>

{{--
@include('private.cbs.cbVoteAnalysis2.details.cbDetailsScript')
--}}
