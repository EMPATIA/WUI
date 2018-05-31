@extends('private._private.index')
<?php
use Carbon\Carbon;
?>
@section('content')

    <div class="row">
        <div class="col-md-12">
            <!-- Tab panes -->

            <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="tab_vote_analysis_total">
                    @if(empty($totalSendedSms))
                        <div class="row">
                            <div class="col-12 text-center">
                                <h4>{{trans('privateCbsVoteAnalysis.no_data_available')}}</h4>
                            </div>
                        </div>
                    @else
                        <div class="row">
                            {{--TOTAL VOTES INFORMATION--}}
                            <div class="col-md-12">
                                <div class="box-info">
                                    <div class="box-header voteAnalysis-total">
                                        <h3 class="box-title"><i class="fa"></i> {{trans('privateCbsVoteAnalysis.count_total_sms')}}</h3>
                                    </div>
                                    <div class="box-body">
                                        <div class="row">
                                            <div class="col-sm-3 text-center">
                                                <div>
                                                    <img src="{{asset('/images/total_voters.png')}}" style="width: 5em">
                                                </div>
                                                <div>
                                                    <strong>{{trans('privateCbsVoteAnalysis.total_sms_sent')}}</strong>
                                                </div>
                                                <div>
                                                    {{ $totalSendedSms ?? null }}
                                                </div>
                                            </div>
                                            <div class="col-sm-3 text-center">
                                                <div>
                                                    <img src="{{asset('/images/total_votes.png')}}" style="width: 5em">
                                                </div>
                                                <div>
                                                    <strong>{{trans('privateCbsVoteAnalysis.total_sms_received')}}</strong>
                                                </div>
                                                <div>
                                                    {{ $totalReceivedSms ?? null}}
                                                </div>
                                            </div>
                                            <div class="col-sm-3 text-center">
                                                <div>
                                                    <img src="{{asset('/images/positive_votes.png')}}" style="width: 5em">
                                                </div>
                                                <div>
                                                    <strong>{{trans('privateCbsVoteAnalysis.total_sms_votes')}}</strong>
                                                </div>
                                                <div>
                                                    {{$totalSmsVotes ?? null}}
                                                </div>
                                            </div>
                                            <div class="col-sm-3 text-center">
                                                <div>
                                                    <img src="{{asset('/images/negative_votes.png')}}" style="width: 5em">
                                                </div>
                                                <div>
                                                    <strong>{{trans('privateCbsVoteAnalysis.total_sms_votes_errors')}}</strong>
                                                </div>
                                                <div>
                                                    {{$totalReceivedSmsErrors ?? null}}
                                                </div>
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
                                <div class="box-info">
                                    <div class="box-header">
                                        <h3 class="box-title">{{trans('privateCbsVoteAnalysis.last24h')}}</h3>
                                    </div>
                                    <div class="col-12 col-sm-6 col-md-3">
                                        <label for="date">{!! trans("privateCbs.filterDate") !!}</label>
                                        <div class="input-group date">
                        <span class="input-group-addon">
                        <i class="glyphicon glyphicon-th"></i>
                        </span>
                                            <input class="form-control oneDatePicker" style="width:40%" type="text" id="date24h" name="dateRange24h" value="" onchange="selectDateFilter24h()" required/>
                                        </div>
                                    </div>
                                    <div class="box-body">
                                        <tbody>
                                        <div id="statistics_last24h" style="height: 300px" class="default-padding">
                                        </div>
                                        </tbody>
                                    </div>
                                    <div class="box-header">
                                        <h3 class="box-title">{{trans('privateCbsVoteAnalysis.last30d')}}</h3>
                                    </div>
                                    <div class="col-12 col-sm-6 col-md-3">
                                        <label for="date">{!! trans("privateCbs.filterDate") !!}</label>
                                        <div class="input-group date">
                        <span class="input-group-addon">
                        <i class="glyphicon glyphicon-th"></i>
                        </span>
                                            <input class="form-control oneDatePicker" style="width:40%" type="text" id="date30d" name="dateRange30d" value="" onchange="selectDateFilter30D()" required/>
                                        </div>
                                    </div>
                                    <div class="box-body">
                                        <tbody>
                                        <div id="statistics_last30d" style="height: 300px" class="default-padding">
                                        </div>
                                        </tbody>
                                    </div>
                                </div>
                            </div>
                            {{--TOP 10 END--}}
                        </div>
                    @endif

                    {{--@endif--}}
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')

    {{--GRAFICO DAS 24H--}}
    <script>
        function selectDateFilter24h() {
            $("#statistics_last24h").html("");
//            $("#statistics_last30d").html("");

            var url = "{{action('SmsController@showAnalyticsSmsFiltered24H')}}";

            var start_date = '';
            var end_date = '';

            if ($('#start_date').val() != '' && $('#end_date').val() != ''){

                start_date = $("#date24h").val().split(" - ")[0];
                end_date   =  $("#date24h").val().split(" - ")[1];

                if (start_date < end_date ) {

                    url = url + '?start_date=' + start_date + '?end_date=' + end_date;

                    $.ajax({
                        url: url,
                        type: 'GET',
                        success: function(response){

                            chart = [];


                            // Chart for «24h» --------------------------------------------------------------------------

                            // Chart for «Total Sended Sms» --------------------------------------------------------------------------
                            var totalSendedSmsLast24H = response["TotalSendedSms24H"];
                            var chartTotalSendedSms24H = [];

                            // Setting Y range - bug fix when values are 0's
                            var chartRange = [0, 10];
                            for(var i = 0 ; i< totalSendedSmsLast24H.length; i++ ){
                                chartTotalSendedSms24H[i] = JSON.parse(totalSendedSmsLast24H[i]);
                                if(chartTotalSendedSms24H[i].value != 0){
                                    chartRange = false;
                                }
                            }

                            chart = chart.concat(chartTotalSendedSms24H);

                            // Chart for «Total Received Sms» --------------------------------------------------------------------------
                            var totalReceivedSmsLast24H = response["TotalReceivedSms24H"];
                            var chartTotalReceivedSms24H = [];

                            // Setting Y range - bug fix when values are 0's
                            var chartRange = [0, 10];
                            for(var i = 0 ; i< totalReceivedSmsLast24H.length; i++ ){
                                chartTotalReceivedSms24H[i] = JSON.parse(totalReceivedSmsLast24H[i]);
                                if(chartTotalReceivedSms24H[i].value != 0){
                                    chartRange = false;
                                }
                            }

                            chart = chart.concat(chartTotalReceivedSms24H);

                            // Chart for «Total Sms Votes» --------------------------------------------------------------------------
                            var totalSmsVotes24H = response["TotalSmsVotes24H"];
                            var chartTotalSmsVotes24H = [];

                            // Setting Y range - bug fix when values are 0's
                            var chartRange = [0, 10];
                            for(var i = 0 ; i< totalSmsVotes24H.length; i++ ){
                                chartTotalSmsVotes24H[i] = JSON.parse(totalSmsVotes24H[i]);
                                if(chartTotalSmsVotes24H[i].value != 0){
                                    chartRange = false;
                                }
                            }

                            chart = chart.concat(chartTotalSmsVotes24H);

                            // Chart for «Total Sms Votes Errors» --------------------------------------------------------------------------
                            var totalSmsVotesErrors24H = response["TotalSmsVotesErrors24H"];
                            var chartTotalSmsVotesErrors24H = [];

                            // Setting Y range - bug fix when values are 0's
                            var chartRange = [0, 10];
                            for(var i = 0 ; i< totalSmsVotesErrors24H.length; i++ ){
                                chartTotalSmsVotesErrors24H[i] = JSON.parse(totalSmsVotesErrors24H[i]);
                                if(chartTotalSmsVotesErrors24H[i].value != 0){
                                    chartRange = false;
                                }
                            }

                            chart = chart.concat(chartTotalSmsVotesErrors24H);

//                            console.log(chart);

                            var visualization = d3plus.viz()
                                .container("#statistics_last24h")
                                .data(chart)
                                .type("line")
                                .id("name")
                                .y({
                                    "value": "Votos",
//                                    "range": chartRange
                                })
                                .x('Data')
                                .color({
                                    "value": "name"
                                })
                                .legend({
                                    "value": true,
                                    "size": 50
                                })
                                .font({"family": "Helvetica, Arial, sans-serif", "color": "#000"})
                                .resize(true)
                                .time("Data")
                                .draw();
                        }
                    })
                }
            }
        }
    </script>

    {{--GRAFICO DOS 30 DIAS--}}
    <script>
        function selectDateFilter30D() {
            $("#statistics_last30d").html("");

            var url = "{{action('SmsController@showAnalyticsSmsFiltered30D')}}";

            var start_date = '';
            var end_date = '';

            if ($('#start_date').val() != '' && $('#end_date').val() != ''){

                start_date = $("#date30d").val().split(" - ")[0];
                end_date   =  $("#date30d").val().split(" - ")[1];

                if (start_date < end_date ) {

                    url = url + '?start_date=' + start_date + '?end_date=' + end_date;

                    $.ajax({
                        url: url,
                        type: 'GET',
                        success: function(response){

                            chart30D = [];


                            // Chart for «30D» --------------------------------------------------------------------------

                            // Chart for «Total Sended Sms» --------------------------------------------------------------------------
                            var totalSendedSmsLast30D = response["TotalSendedSms30D"];
                            var chartTotalSendedSms30D = [];

                            // Setting Y range - bug fix when values are 0's
                            var chartRange = [0, 10];
                            for(var i = 0 ; i< totalSendedSmsLast30D.length; i++ ){
                                chartTotalSendedSms30D[i] = JSON.parse(totalSendedSmsLast30D[i]);
                                if(chartTotalSendedSms30D[i].value != 0){
                                    chartRange = false;
                                }
                            }

                            chart30D = chart30D.concat(chartTotalSendedSms30D);

                            // Chart for «Total Received Sms» --------------------------------------------------------------------------
                            var totalReceivedSmsLast30D = response["TotalReceivedSms30D"];
                            var chartTotalReceivedSms30D = [];

                            // Setting Y range - bug fix when values are 0's
                            var chartRange = [0, 10];
                            for(var i = 0 ; i< totalReceivedSmsLast30D.length; i++ ){
                                chartTotalReceivedSms30D[i] = JSON.parse(totalReceivedSmsLast30D[i]);
                                if(chartTotalReceivedSms30D[i].value != 0){
                                    chartRange = false;
                                }
                            }

                            chart30D = chart30D.concat(chartTotalReceivedSms30D);

                            // Chart for «Total Sms Votes» --------------------------------------------------------------------------
                            var totalSmsVotes30D = response["TotalSmsVotes30D"];
                            var chartTotalSmsVotes30D = [];

                            // Setting Y range - bug fix when values are 0's
                            var chartRange = [0, 10];
                            for(var i = 0 ; i< totalSmsVotes30D.length; i++ ){
                                chartTotalSmsVotes30D[i] = JSON.parse(totalSmsVotes30D[i]);
                                if(chartTotalSmsVotes30D[i].value != 0){
                                    chartRange = false;
                                }
                            }

                            chart30D = chart30D.concat(chartTotalSmsVotes30D);

                            // Chart for «Total Sms Votes Errors» --------------------------------------------------------------------------
                            var totalSmsVotesErrors30D = response["TotalSmsVotesErrors30D"];
                            var chartTotalSmsVotesErrors30D = [];

                            // Setting Y range - bug fix when values are 0's
                            var chartRange = [0, 10];
                            for(var i = 0 ; i< totalSmsVotesErrors30D.length; i++ ){
                                chartTotalSmsVotesErrors30D[i] = JSON.parse(totalSmsVotesErrors30D[i]);
                                if(chartTotalSmsVotesErrors30D[i].value != 0){
                                    chartRange = false;
                                }
                            }

                            chart30D = chart30D.concat(chartTotalSmsVotesErrors30D);

//                            console.log(chart30D);

                            var visualization = d3plus.viz()
                                .container("#statistics_last30d")
                                .data(chart30D)
                                .type("line")
                                .id("name")
                                .y({
                                    "value": "Votos",
//                                    "range": chartRange
                                })
                                .x('Data')
                                .color({
                                    "value": "name"
                                })
                                .legend({
                                    "value": true,
                                    "size": 50
                                })
                                .font({"family": "Helvetica, Arial, sans-serif", "color": "#000"})
                                .resize(true)
                                .time("Data")
                                .draw();
                        }
                    })
                }
            }
        }
    </script>

    <!-- Date Range Picker - JavaScript -->

    <script type="text/javascript">
        $(function() {
            $('input[name="dateRange24h"]').daterangepicker({
                singleDatePicker:false,
                timePicker: true,
                timePicker24Hour: true,
                timePickerIncrement: 5,
                dateLimit: {"days" : 1},
                startDate: "{!! Carbon::now()->subDay()->format('Y-m-d H:i:s') !!}",
                endDate: "{!! Carbon::now()->format('Y-m-d H:i:s') !!}",
                locale: {
                    format: 'YYYY-MM-DD HH:mm:SS'
                }
            });
        });
    </script>

    <script type="text/javascript">
        $(function() {
            $('input[name="dateRange30d"]').daterangepicker({
                singleDatePicker:false,
                startDate: "{!! Carbon::now()->subMonth()->format('Y-m-d') !!}",
                endDate: "{!! Carbon::now()->format('Y-m-d') !!}",
                locale: {
                    format: 'YYYY-MM-DD'
                }
            });
        });
    </script>
@endsection
