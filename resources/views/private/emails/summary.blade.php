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
                    {{--@if(empty($totalSentEmails))--}}
                        {{--<div class="row">--}}
                            {{--<div class="col-12 text-center">--}}
                                {{--<h4>{{trans('privateEmails.no_data_available')}}</h4>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                    {{--@else--}}
                        <div class="row">
                            {{--TOTAL VOTES INFORMATION--}}
                            <div class="col-md-12">
                                <div class="box-info">
                                    <div class="box-header voteAnalysis-total">
                                        <h3 class="box-title"><i class="fa"></i> {{trans('privateEmails.count_total_Emails')}}</h3>
                                    </div>
                                    <div class="box-body">
                                        <div class="row">
                                            <div class="col-sm-4 text-center">
                                                <div>
                                                    <img src="{{asset('/images/total_voters.png')}}" style="width: 5em">
                                                </div>
                                                <div>
                                                    <strong>{{trans('privateEmails.total_emails_sent')}}</strong>
                                                </div>
                                                <div>
                                                    {{ $totalSentEmails ?? null }}
                                                </div>
                                            </div>
                                            <div class="col-sm-4 text-center">
                                                <div>
                                                    <img src="{{asset('/images/total_votes.png')}}" style="width: 5em">
                                                </div>
                                                <div>
                                                    <strong>{{trans('privateEmails.total_not_sent_emails')}}</strong>
                                                </div>
                                                <div>
                                                    {{ $totalNotSentEmails ?? null}}
                                                </div>
                                            </div>
                                            <div class="col-sm-4 text-center">
                                                <div>
                                                    <img src="{{asset('/images/positive_votes.png')}}" style="width: 5em">
                                                </div>
                                                <div>
                                                    <strong>{{trans('privateEmails.total_errors_emails')}}</strong>
                                                </div>
                                                <div>
                                                    {{$totalEmailsErrors ?? null}}
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
                                    <h3 class="box-title">{{trans('privateEmails.last30d')}}</h3>
                                </div>
                                <div class="col-12 col-sm-6 col-md-3">
                                    <label for="date">{!! trans("privateEmails.filterDate") !!}</label>
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
                    {{--@endif--}}

                    {{--@endif--}}
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')

    {{--GRAFICO DOS 30 DIAS--}}
    <script>
        function selectDateFilter30D() {
            $("#statistics_last30d").html("");

            var url = "{{action('EmailsController@showStats')}}";

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

                            // Chart for «Total Sent Emails» --------------------------------------------------------------------------
                            var totalSendedSmsLast30D = response["TotalSentEmails30D"];
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
                            var totalReceivedSmsLast30D = response["TotalNotSentEmails30D"];
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
                            var totalSmsVotes30D = response["TotalEmailsErrors30D"];
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

//                            console.log(chart30D);

                            var visualization = d3plus.viz()
                                .container("#statistics_last30d")
                                .data(chart30D)
                                .type("line")
                                .id("name")
                                .y({
                                    "value": "Emails",
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
