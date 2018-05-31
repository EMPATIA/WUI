@extends('private._private.index')

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
                                        <h3 class="box-title">{{trans('privateCbsVoteAnalysis.last48h')}}</h3>
                                    </div>
                                    <div class="box-body">
                                        <tbody>
                                        <div id="statistics_last48h" style="height: 300px" class="default-padding">
                                        </div>
                                        </tbody>
                                    </div>
                                    <div class="box-header">
                                        <h3 class="box-title">{{trans('privateCbsVoteAnalysis.last30d')}}</h3>
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

    {{--GRAFICO DAS 48H--}}

    <script>
        $("#statistics_last48h").html("");

        var url = "{{action('SmsController@showResume48H')}}";

        $.ajax({
            url: url,
            type: 'GET',
            success: function(response){

                chart48H = [];


                // Chart for «48H» --------------------------------------------------------------------------

                // Chart for «Total Sended Sms» --------------------------------------------------------------------------
                var totalSendedSmsLast48H = response["TotalSendedSms48H"];
                var chartTotalSendedSms48H = [];

                // Setting Y range - bug fix when values are 0's
                var chartRange = [0, 10];
                for(var i = 0 ; i< totalSendedSmsLast48H.length; i++ ){
                    chartTotalSendedSms48H[i] = JSON.parse(totalSendedSmsLast48H[i]);
                    if(chartTotalSendedSms48H[i].value != 0){
                        chartRange = false;
                    }
                }

//                console.log(chartTotalSendedSms48H);

                chart48H = chart48H.concat(chartTotalSendedSms48H);

                // Chart for «Total Received Sms» --------------------------------------------------------------------------
                var totalReceivedSmsLast48H = response["TotalReceivedSms48H"];
                var chartTotalReceivedSms48H = [];

                // Setting Y range - bug fix when values are 0's
                var chartRange = [0, 10];
                for(var i = 0 ; i< totalReceivedSmsLast48H.length; i++ ){
                    chartTotalReceivedSms48H[i] = JSON.parse(totalReceivedSmsLast48H[i]);
                    if(chartTotalReceivedSms48H[i].value != 0){
                        chartRange = false;
                    }
                }

                chart48H = chart48H.concat(chartTotalReceivedSms48H);

                // Chart for «Total Sms Votes» --------------------------------------------------------------------------
                var totalSmsVotes48H = response["TotalSmsVotes48H"];
                var chartTotalSmsVotes48H = [];

                // Setting Y range - bug fix when values are 0's
                var chartRange = [0, 10];
                for(var i = 0 ; i< totalSmsVotes48H.length; i++ ){
                    chartTotalSmsVotes48H[i] = JSON.parse(totalSmsVotes48H[i]);
                    if(chartTotalSmsVotes48H[i].value != 0){
                        chartRange = false;
                    }
                }

                chart48H = chart48H.concat(chartTotalSmsVotes48H);

                // Chart for «Total Sms Votes Errors» --------------------------------------------------------------------------
                var totalSmsVotesErrors48H = response["TotalSmsVotesErrors48H"];
                var chartTotalSmsVotesErrors48H = [];

                // Setting Y range - bug fix when values are 0's
                var chartRange = [0, 10];
                for(var i = 0 ; i< totalSmsVotesErrors48H.length; i++ ){
                    chartTotalSmsVotesErrors48H[i] = JSON.parse(totalSmsVotesErrors48H[i]);
                    if(chartTotalSmsVotesErrors48H[i].value != 0){
                        chartRange = false;
                    }
                }

                chart48H = chart48H.concat(chartTotalSmsVotesErrors48H);

//                console.log(chart48H);

                var visualization = d3plus.viz()
                    .container("#statistics_last48h")
                    .data(chart48H)
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
    </script>

    {{--GRAFICO DOS 30 DIAS--}}
    <script>
        $("#statistics_last30d").html("");

        var url = "{{action('SmsController@showResume30D')}}";

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

//                console.log(chart30D);

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
                    .font({"family": "Helvetica, Arial, sans-serif", "color": "#000"}).resize(true)
                    .resize(true)
                    .time("Data")
                    .draw();
            }
        })
    </script>
@endsection
