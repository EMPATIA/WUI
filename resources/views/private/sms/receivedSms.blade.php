@extends('private._private.index')

<?php
use Carbon\Carbon;
?>

@section('content')
    <div class="box box-primary">

        <div class="row">
            {{--TOTAL VOTES INFORMATION--}}
            <div class="col-md-12">
                <div class="box-info">
                    <div class="box-header voteAnalysis-total">
                        <h3 class="box-title"><i class="fa"></i> {{trans('privateCbsVoteAnalysis.received_sms')}}</h3>
                    </div>
                    <div class="box-body">
                        <div class="row">
                            <div class="col-sm-3 text-center">
                                <div>
                                    <img src="{{asset('/images/total_voters.png')}}" style="width: 5em">
                                </div>
                                <div>
                                    <strong>{{trans('privateCbsVoteAnalysis.total_sms')}}</strong>
                                </div>
                                <div>
                                    {{ $totalReceivedSms ?? null }}
                                </div>
                            </div>
                            <div class="col-sm-3 text-center">
                                <div>
                                    <img src="{{asset('/images/total_votes.png')}}" style="width: 5em">
                                </div>
                                <div>
                                    <strong>{{trans('privateCbsVoteAnalysis.total_sms_errors')}}</strong>
                                </div>
                                <div>
                                    {{ $totalReceivedSmsErrors ?? null}}
                                </div>
                            </div>
                            <div class="col-sm-3 text-center">
                                <div>
                                    <img src="{{asset('/images/positive_votes.png')}}" style="width: 5em">
                                </div>
                                <div>
                                    <strong>{{trans('privateCbsVoteAnalysis.total_last_24h')}}</strong>
                                </div>
                                <div>
                                    {{$totalReceivedSmsLast24H ?? null}}
                                </div>
                            </div>
                            <div class="col-sm-3 text-center">
                                <div>
                                    <img src="{{asset('/images/negative_votes.png')}}" style="width: 5em">
                                </div>
                                <div>
                                    <strong>{{trans('privateCbsVoteAnalysis.total_last_24h_errors')}}</strong>
                                </div>
                                <div>
                                    {{$totalReceivedSmsLast24hErrors ?? null}}
                                </div>
                            </div>
                        </div>

                        {{--FILTERS --}}

                        <div class="row">
                            <div class="col-12">
                                <br>
                                <h5 class="filterBy-title">{!! trans("privateCbs.filter_by") !!}</h5>
                            </div>
                            <div class="col-12 col-sm-6 col-md-3">
                                <label for="end_date">{!! trans("privateCbs.filterDate") !!}</label>
                                <div class="input-group date">
                        <span class="input-group-addon">
                        <i class="glyphicon glyphicon-th"></i>
                        </span>
                                    <input class="form-control oneDatePicker" style="width:40%" type="text" id="date" name="dateRange" value="" onchange="selectDateFilter()" required/>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6 col-md-3">
                                <div class="form-group">
                                    <label for="sel1">{!! trans("privateSms.status") !!}</label>
                                    <select class="form-control" id="selectValue" onchange="selectDateFilter()">
                                        <option></option>
                                        <option>{!! trans("privateSms.received_vote") !!}</option>
                                        <option>{!! trans("privateSms.error_vote") !!}</option>
                                    </select>
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
                        <h3 class="box-title"><i class="fa"></i> {{ trans('privateSms.list') }}</h3>
                    </div>

                    <div class="box-body">
                        <table id="sent_sms_list" class="table table-striped dataTable no-footer table-responsive">
                            <thead>
                            <tr>
                                <th>{{ trans('privateSms.date_hour') }}</th>
                                <th>{{ trans('privateSms.mobile_number') }}</th>
                                <th>{{ trans('privateSms.text') }}</th>
                                <th>{{ trans('privateSms.status') }}</th>
                                <th></th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>

                @endsection


                @section('scripts')

                    <script>
                        function selectDateFilter() {

                            var url = "{{action('SmsController@getReceivedDatatableFilter')}}";

                            var start_date = '';
                            var end_date = '';
                            var status = '';

                            if ($('#start_date').val() != '' && $('#end_date').val() != '' ){


                                start_date = $("#date").val().split(" - ")[0];
                                end_date   =  $("#date").val().split(" - ")[1];
                                status = $("#selectValue").val();

                                if (start_date < end_date ) {
                                    if (status == '{!! trans("privateSms.received_vote") !!}'){
                                        status = 1;
                                    }
                                    else if(status == '{!! trans("privateSms.error_vote") !!}'){
                                        status = 0;
                                    }else{
                                        status = 2;
                                    }

                                    url = url + '?start_date=' + start_date + '?end_date=' + end_date + '?status=' + status;

                                    var tableUsers = $('#sent_sms_list').DataTable({
                                        language: {
                                            url: '{!! asset('/datatableLang/'.Session::get('LANG_CODE').'.json') !!}'
                                        },
                                        processing: true,
                                        serverSide: true,
                                        bDestroy: true,
                                        ajax:
                                            {
                                                'url': url,
                                                "type": "GET"
                                            },
                                        columns: [
                                            { data: 'created_at', name: 'created_at', searchable: false },
                                            { data: 'sender', name: 'sender', searchable: true },
                                            { data: 'content', name: 'content', searchable: true, orderable: false},
                                            { data: 'processed', name: 'processed', searchable: false },
                                            { data: 'action', name: 'action', searchable: false, orderable: false, width: "30px" },
                                        ],
                                        order: [['1', 'asc']],
                                    });
                                }
                            }
                        }

                    </script>

                    <!-- Date Range Picker - JavaScript -->

                    <script type="text/javascript">
                        $(function() {
                            $('input[name="dateRange"]').daterangepicker({
                                singleDatePicker:false,
                                timePicker: true,
                                timePicker24Hour: true,
                                timePickerIncrement: 5,
                                startDate: "{!! Carbon::now()->subDay()->format('Y-m-d H:i:s') !!}",
                                endDate: "{!! Carbon::now()->format('Y-m-d H:i:s') !!}",
                                locale: {
                                    format: 'YYYY-MM-DD HH:mm:SS'
                                }
                            });
                        });
                    </script>
@endsection
